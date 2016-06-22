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

class MSP_TwoFactorAuth_Model_Observer
{
    public function controllerActionPredispatch($event)
    {
        if (!Mage::getSingleton('admin/session')->getUser()) {
            return;
        }

        if (!Mage::helper('msp_twofactorauth/auth')->getUserMustAuth()) {
            return;
        }

        /** @var $controllerAction Mage_Adminhtml_Controller_Action */
        $controllerAction = $event->getEvent()->getControllerAction();

        if (in_array($controllerAction->getFullActionName(), array(
            'adminhtml_tfa_auth',
            'adminhtml_tfa_authpost',
            'adminhtml_tfa_setup',
            'adminhtml_tfa_setuppost',
            'adminhtml_tfa_qrcode',
            'adminhtml_tfa_disable',
        ))) {
            return;
        }

        if (!Mage::helper('msp_twofactorauth/auth')->getUserSetupComplete()) {
            Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/tfa/setup'));
            Mage::app()->getResponse()->sendResponse();
            exit;
        }

        Mage::app()->getResponse()->setRedirect(Mage::helper('adminhtml')->getUrl('adminhtml/tfa/auth'));
        Mage::app()->getResponse()->sendResponse();
        exit;
    }
    


    public function adminhtmlBlockHtmlBefore($event)
    {
        $block = $event->getEvent()->getBlock();
        if (!isset($block)) {
            return $this;
        }

        if ($block->getType() == 'adminhtml/system_account_edit_form') {
            $user = Mage::getSingleton('admin/session')->getUser();
            Mage::helper('msp_twofactorauth/form')->handleAccountForm($block, $user);
        } elseif ($block->getType() == 'adminhtml/permissions_user_edit_tab_main') {
            $userId = Mage::app()->getRequest()->getParam('user_id');
            $user = Mage::getModel('admin/user')->load($userId);
            Mage::helper('msp_twofactorauth/form')->handleAccountForm($block, $user);
        }
    }

    public function adminUserSaveBefore($event)
    {
        $request = Mage::app()->getRequest();
        $fullActionName = $request->getControllerName().'_'.$request->getActionName();

        if ($fullActionName == 'system_account_save') {
            $user = $event->getEvent()->getObject();

            $tfaEnabled = $request->getPost('msp_tfa_enabled');

            if ($tfaEnabled && !$user->getMspTfaEnabled()) {
                if ($user->getId() == Mage::getSingleton('admin/session')->getUser()->getId()) {
                    Mage::helper('msp_twofactorauth/auth')->unsetTwoAuthFactorPassed();
                }
            }

            /** @var $user Mage_Admin_Model_User */
            $user->setMspTfaEnabled($tfaEnabled);
            if (!$tfaEnabled) {
                $user->setMspTfaActivated(false);
            }
        }
    }
}
