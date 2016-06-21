<?php
/**
 * IDEALIAGroup srl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@idealiagroup.com so we can send you a copy immediately.
 *
 * @category   MSP
 * @package    MSP_TwoFactorAuth
 * @copyright  Copyright (c) 2016 IDEALIAGroup srl (http://www.idealiagroup.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class MSP_TwoFactorAuth_Adminhtml_TfaController extends Mage_Adminhtml_Controller_Action
{
    public function _isAllowed()
    {
        // Must be visible for all users
        return true;
    }

    public function authAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function authpostAction()
    {
        $token = $this->getRequest()->getParam('token');
        if (Mage::helper('msp_twofactorauth/totp')->verify($token)) {
            Mage::helper('msp_twofactorauth/auth')->setTwoAuthFactorPassed();
            $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Invalid token');
            $this->_redirectReferer();
        }
    }

    public function setupAction()
    {
        if (Mage::helper('msp_twofactorauth/auth')->getUserSetupComplete()) {
            $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
            return;
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function setuppostAction()
    {
        $token = $this->getRequest()->getParam('token');
        if (Mage::helper('msp_twofactorauth/totp')->verify($token)) {
            Mage::helper('msp_twofactorauth/auth')->activateUserTfa();
            Mage::helper('msp_twofactorauth/auth')->setTwoAuthFactorPassed();
            Mage::getSingleton('adminhtml/session')->addSuccess('Two Factor Authentication activated');

            $this->_redirect('adminhtml/system_account/index');
        } else {
            Mage::getSingleton('adminhtml/session')->addError('Invalid token');
            $this->_redirectReferer();
        }
    }

    public function qrcodeAction()
    {
        if (Mage::helper('msp_twofactorauth/auth')->getUserSetupComplete()) {
            exit;
        }

        $this->getResponse()->setHeader('Content-Type', 'image/png');
        Mage::helper('msp_twofactorauth/totp')->renderQrCode();
    }

    public function disableAction()
    {
        if (Mage::helper('msp_twofactorauth/auth')->getUserSetupComplete()) {
            $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
            return;
        }

        Mage::helper('msp_twofactorauth/auth')->disableTwoDactorAuthentication();
        Mage::getSingleton('adminhtml/session')->addSuccess('Two Factor Authentication disabled');
        $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
    }

    public function regenerateAction()
    {
        $userId = $this->getRequest()->getParam('user_id');
        if (!$userId) {
            $user = Mage::getSingleton('admin/session')->getUser();
        } else {
            $user = Mage::getModel('admin/user')->load($userId);
        }

        if (!$user->getId()) {
            Mage::getSingleton('adminhtml/session')->addError('Invalid user');
            $this->_redirectReferer();
        }

        // Check authorization to handle this user
        if (($user->getId() != Mage::getSingleton('admin/session')->getUser()->getId()) &&
            !Mage::getSingleton('admin/session')->isAllowed('system/acl/users')
        ) {
            $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
            return;
        }

        if (!Mage::getSingleton('admin/session')->isAllowed('system/myaccount')) {
            $this->_redirect(Mage::getSingleton('admin/session')->getUser()->getStartupPageUrl());
            return;
        }

        $user
            ->setMspTfaSecret('')
            ->setMspTfaActivated(false)
            ->save();

        // If it is the same user we must reset authorization
        if ($user->getId() == Mage::getSingleton('admin/session')->getUser()->getId()) {
            Mage::helper('msp_twofactorauth/auth')->unsetTwoAuthFactorPassed();
        }

        Mage::getSingleton('adminhtml/session')->addSuccess('Two factor authentication has been rebuilt');
        $this->_redirectReferer();
    }
}
