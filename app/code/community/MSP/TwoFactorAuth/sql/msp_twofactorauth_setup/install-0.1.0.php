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

/* @var $this Mage_Core_Model_Resource_Setup */

$this->startSetup();

$conn = $this->getConnection();

$tableName = $this->getTable('admin/user');

$conn->addColumn(
    $tableName,
    'msp_tfa_secret',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'default' => null,
        'comment' => 'Two Factor Authentication Secret',
    )
);

$conn->addColumn(
    $tableName,
    'msp_tfa_enabled',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => true,
        'default' => null,
        'comment' => 'Two Factor Authentication Enabled Flag',
    )
);

$conn->addColumn(
    $tableName,
    'msp_tfa_activated',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'nullable' => true,
        'default' => null,
        'comment' => 'Two Factor Authentication Enabled Flag',
    )
);

$this->endSetup();
