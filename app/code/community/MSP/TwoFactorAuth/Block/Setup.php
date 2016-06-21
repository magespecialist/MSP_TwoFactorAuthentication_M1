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

class MSP_TwoFactorAuth_Block_Setup extends Mage_Adminhtml_Block_Template
{
    public function getPostUrl()
    {
        return $this->getUrl('adminhtml/tfa/setuppost');
    }

    public function getQrCode()
    {
        return $this->getUrl('adminhtml/tfa/qrcode');
    }

    /**
     * Return true if user can disable two auth factor
     * @return bool
     */
    public function canDisable()
    {
        return !Mage::helper('msp_twofactorauth')->getForce();
    }

    /**
     * Get disable URL
     * @return string
     */
    public function getDisableUrl()
    {
        return $this->getUrl('adminhtml/tfa/disable');
    }
}