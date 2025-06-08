<?php

/**
 * File upgrade-1.3.2-1.4.0.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
$installer = $this;

$installer->registerHook('postUpdateOrderStatus');


$installer->executeSql("
    ALTER TABLE `PREFIX_prestabay_marketplaces` ADD COLUMN `shipping_exclude_location` TEXT  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_marketplaces` ADD COLUMN `shipping_packages` TEXT  DEFAULT NULL;

    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_exclude_location` TEXT  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `product_specifics_custom` TEXT  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `subtitle` VARCHAR(255) DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `autopay` TINYINT(1) DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `cross_border_trade` TINYINT(1) DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `description_template_id` INTEGER DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_measurement` VARCHAR(75)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_package` VARCHAR(75)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_depth` TINYINT(1)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_depth_custom` INTEGER  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_length` TINYINT(1)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_length_custom` INTEGER  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_width` TINYINT(1)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_width_custom` INTEGER  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_weight` TINYINT(1)  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_weight_custom` FLOAT  DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_calculated_postal` VARCHAR(10)  DEFAULT NULL;
    
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_local_type` TINYINT(1)  NOT NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `shipping_int_type` TINYINT(1)  NOT NULL DEFAULT 0;


    ALTER TABLE `PREFIX_prestabay_order` ADD COLUMN `account_id` INTEGER  DEFAULT NULL;

    CREATE TABLE `PREFIX_prestabay_order_log` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `prestabay_order_id` INTEGER  NOT NULL,
      `message` TEXT  NOT NULL,
      `date_add` DATETIME NOT NULL,
       PRIMARY KEY  (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_order_external_transactions` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `transaction_id` VARCHAR(20)  NOT NULL,
      `prestabay_order_id` INTEGER  NOT NULL,
      `time` DATETIME  NOT NULL,
      `fee` float  NOT NULL DEFAULT 0,
      `total` float  NOT NULL DEFAULT 0,
      `refund` tinyint(1)  NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;


    ALTER TABLE `PREFIX_prestabay_selling_products` ADD COLUMN `product_price_change` FLOAT NOT NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_selling_variations` ADD COLUMN `price_change` FLOAT NOT NULL DEFAULT 0;

    CREATE TABLE `PREFIX_prestabay_template_shipping` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255)  NOT NULL,
      `mode` TINYINT(1)  NOT NULL DEFAULT 0,
      `remove_not_in_range` TINYINT(1) NOT NULL DEFAULT 0,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_template_shipping_conditions` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `shipping_id` INTEGER  NOT NULL,
      `value_from` FLOAT  NOT NULL,
      `value_to` FLOAT  NOT NULL,
      `plain` FLOAT  NOT NULL,
      `additional` FLOAT  NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_template_description` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `name` VARCHAR(255)  NOT NULL,
      `template` TEXT  NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

 ");

reset_marketplace_category();
migrateShippingInformation();

function reset_marketplace_category()
{
       $sqlToUpdate = "UPDATE " . _DB_PREFIX_ . "prestabay_marketplaces SET
                            `version` = 0,
                            `status` = " . MarketplacesModel::STATUS_PENDING . ",
                            `date_upd` = null,
                            `dispatch` = null,
                            `policy` = null,
                            `payment_methods` = null,
                            `shipping_location` = null";

        Db::getInstance()->Execute($sqlToUpdate);
}

function migrateShippingInformation()
{

    $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_profiles";
    $profilesList = Db::getInstance()->ExecuteS($sql);
    foreach ($profilesList as $singleProfile) {
        // Convert local shipping to new format
        $newShippingFormat = _convertLocalShipping($singleProfile['shipping_local']);
        if ($newShippingFormat != false) {
            // This row not converted
            // Execute DB update to this row
            Db::getInstance()->autoExecute(_DB_PREFIX_ . 'prestabay_profiles', array('shipping_local' => $newShippingFormat), 'UPDATE', 'id = ' . $singleProfile['id']);
        }

        $newIntShippingFormat = _convertIntShipping($singleProfile['shipping_int'], $singleProfile['shipping_to_location']);
        if ($newIntShippingFormat != false) {
            // This row not converted
            // Execute DB update to this row
            Db::getInstance()->autoExecute(_DB_PREFIX_ . 'prestabay_profiles', array('shipping_int' => $newIntShippingFormat), 'UPDATE', 'id = ' . $singleProfile['id']);
        }
    }
}

function _convertLocalShipping($oldShippingFormat)
{
    $newShippingFormat = array();

    $shippingList = unserialize($oldShippingFormat);

    if (!isset($shippingList['plain']) || !isset($shippingList['additional']) ||
            !is_array($shippingList['plain']) || !is_array($shippingList['additional'])) {
        // Not possible to convert possible already converted
        return false;
    }

    foreach ($shippingList['plain'] as $shippingKey => $additionalValue) {
        if (!is_string($shippingKey)) {
            continue;
        }
        if (!isset($newShippingFormat[$shippingKey])) {
            $newShippingFormat[$shippingKey] = array(
                'name' => $shippingKey,
                'priority' => 0
            );
        }
        $newShippingFormat[$shippingKey]['plain'] = $additionalValue;
    }

    foreach ($shippingList['additional'] as $shippingKey => $additionalValue) {
        if (!is_string($shippingKey)) {
            continue;
        }
        if (!isset($newShippingFormat[$shippingKey])) {
            $newShippingFormat[$shippingKey] = array(
                'name' => $shippingKey
            );
        }
        $newShippingFormat[$shippingKey]['additional'] = $additionalValue;
    }

    $newShippingFormat = array_values($newShippingFormat);

    return serialize($newShippingFormat);
}

function _convertIntShipping($oldIntShippingFormat, $shippingToLocation)
{
    $newIntShippingFormat = _convertLocalShipping($oldIntShippingFormat);
    if ($newIntShippingFormat == false) {
        return false;
    }
    $newIntShippingFormat = unserialize($newIntShippingFormat);
    $shippingToLocation = unserialize($shippingToLocation);
    foreach ($newIntShippingFormat as &$singleIntShipping) {
        $singleIntShipping['locations'] = $shippingToLocation;
    }

    return serialize($newIntShippingFormat);
}
