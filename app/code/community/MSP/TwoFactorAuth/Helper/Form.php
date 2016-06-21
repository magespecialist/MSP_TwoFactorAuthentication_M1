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

class MSP_TwoFactorAuth_Helper_Form extends Mage_Core_Helper_Abstract
{
    public function handleAccountForm($block, $user)
    {
        $form = $block->getForm();

        $tfaFieldset = $form->addFieldset(
            'msp_twofactorauthentication',
            array(
                'legend' => 'Two Factor Authentication',
                'class' => 'fieldset-wide'
            )
        );

        $tfaFieldset->addField(
            'msp_tfa_enabled',
            'select',
            array(
                'value' => $user->getMspTfaEnabled(),
                'name'  => 'msp_tfa_enabled',
                'label' => Mage::helper('adminhtml')->__('Enable Two Factor Authentication'),
                'title' => Mage::helper('adminhtml')->__('Enable Two Factor Authentication'),
                'options' => array(
                    0 => Mage::helper('adminhtml')->__('No'),
                    1 => Mage::helper('adminhtml')->__('Yes'),
                ),
            )
        );

        if ($user->getMspTfaEnabled()) {
            $regenerateUrl = Mage::helper('adminhtml')->getUrl('adminhtml/tfa/regenerate', array(
                'user_id' => $user->getId(),
            ));

            $tfaFieldset->addField(
                'msp_tfa_regenerate',
                'label',
                array(
                    'label' => Mage::helper('adminhtml')->__('Regenerate Auth'),
                    'name' => 'msp_tfa_regenerate',
                    'after_element_html' =>
                        '<button'
                        .' type="button" '
                        .' onclick="self.location.href=\''.$regenerateUrl.'\'">'
                        .Mage::helper('adminhtml')->__('Regenerate')
                        .'</button>',
                )
            );
        }
    }
}
