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

class MSP_TwoFactorAuth_Helper_Auth extends Mage_Core_Helper_Abstract
{

    /**
     * Get admin session
     * @return Mage_Admin_Model_Session
     */
    protected function _getAdminSession()
    {
        return Mage::getSingleton('admin/session');
    }

    /**
     * Return true if two auth factor auth is passed
     * @return bool
     */
    public function getTwoAuthFactorPassed()
    {
        return (bool) $this->_getAdminSession()->getMspTfaPassed();
    }

    /**
     * Authorize tfa
     * @return $this
     */
    public function setTwoAuthFactorPassed()
    {
        $this->_getAdminSession()->setMspTfaPassed(true);
        return $this;
    }

    /**
     * Disable TFA auth
     */
    public function unsetTwoAuthFactorPassed()
    {
        $this->_getAdminSession()->setMspTfaPassed(false);
        return $this;
    }

    /**
     * Return true if user must use two factor authentication
     * @return bool
     */
    public function getUserMustAuth()
    {
        if ($this->getTwoAuthFactorPassed()) {
            return false;
        }

        if (!Mage::helper('msp_twofactorauth')->getEnabled()) {
            return false;
        }

        if (Mage::helper('msp_twofactorauth')->getForce()) {
            return true;
        }

        return (bool) $this->_getAdminSession()->getUser()->getMspTfaEnabled();
    }

    /**
     * Return true if user setup is complete
     * @return bool
     */
    public function getUserSetupComplete()
    {
        return
            (bool) ($this->_getAdminSession()->getUser()->getMspTfaSecret()) &&
            (bool) ($this->_getAdminSession()->getUser()->getMspTfaActivated());
    }

    /**
     * Set TFA activated flag
     * @return $this
     */
    public function activateUserTfa()
    {
        $user = $this->_getAdminSession()->getUser();
        $user
            ->setMspTfaActivated(true)
            ->setMspTfaEnabled(true)
            ->save();

        return $this;
    }

    public function disableTwoDactorAuthentication()
    {
        $user = $this->_getAdminSession()->getUser();
        $user
            ->setMspTfaActivated(false)
            ->setMspTfaEnabled(false)
            ->save();

        return $this;
    }
}
