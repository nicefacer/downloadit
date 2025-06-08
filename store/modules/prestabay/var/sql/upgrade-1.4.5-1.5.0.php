<?php
/**
 * File upgrade-1.4.5-1.5.0.php
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

$installer->registerHook('updateQuantity');

$installer->executeSql("
    CREATE  TABLE `PREFIX_prestabay_selling_categories` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `selling_id` INT NULL ,
      `category_id` INT NULL ,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
");

categoryModeMigrate();

$defaultImageSize = "thickbox";
if (CoreHelper::isPS15()) {
    $defaultImageSize = "thickbox_default";
}

$installer->executeSql("
    ALTER TABLE `PREFIX_prestabay_selling_list`
        ADD COLUMN `attribute_mode` TINYINT(1)  NOT NULL DEFAULT 0,
        ADD COLUMN `category_send_product` TINYINT(1) NULL DEFAULT NULL;

    ALTER TABLE `PREFIX_prestabay_selling_products` ADD COLUMN `product_id_attribute` INTEGER DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_product_connection` ADD COLUMN `presta_attribute_id` INT NULL DEFAULT 0;


    ALTER TABLE `PREFIX_prestabay_profiles`
        ADD COLUMN `best_offer_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN `best_offer_minimum_price` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN `best_offer_auto_accept_price` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
        ADD COLUMN `enhancement` TEXT NULL DEFAULT NULL,
        ADD COLUMN `ps_image_type` VARCHAR(45) NULL DEFAULT '$defaultImageSize',
        ADD COLUMN `gallery_type` VARCHAR(45) NULL DEFAULT 'None',
        ADD COLUMN `photo_display` VARCHAR(45) NULL DEFAULT 'None',
        ADD COLUMN `ean` TINYINT NULL DEFAULT NULL,
        ADD COLUMN `upc` TINYINT NULL DEFAULT NULL,
        ADD COLUMN `payment_instruction` TEXT NULL DEFAULT NULL,
        ADD COLUMN `gift_icon` TINYINT NULL DEFAULT 0,
        ADD COLUMN `gift_services` TEXT NULL DEFAULT NULL,
        ADD COLUMN `insurance_fee` DOUBLE NULL DEFAULT 0,
        ADD COLUMN `insurance_option` VARCHAR(45) NULL DEFAULT NULL,
        ADD COLUMN `insurance_international_fee` DOUBLE NULL DEFAULT 0,
        ADD COLUMN `insurance_international_option` VARCHAR(45) NULL DEFAULT NULL,
        ADD COLUMN `unit_include` TINYINT NULL DEFAULT 0,
        ADD COLUMN `unit_type` VARCHAR(45) NULL DEFAULT NULL,
        ADD COLUMN `promotional_shipping_discount` TINYINT NULL DEFAULT 0,
        ADD COLUMN `promotional_int_shipping_discount` TINYINT NULL DEFAULT 0,
        ADD COLUMN `shipping_discount_profile_id` VARCHAR(45)  NULL DEFAULT '',
        ADD COLUMN `int_shipping_discount_profile_id` VARCHAR(45) NULL DEFAULT '';


    ALTER TABLE `PREFIX_prestabay_log_sync`
        ADD COLUMN `selling_product_id` INT NULL DEFAULT NULL,
        ADD COLUMN `ps_product_id` INT NULL DEFAULT NULL,
        ADD COLUMN `pb_order_id` INT NULL DEFAULT NULL,
        ADD COLUMN `ebay_item_id` BIGINT(20) NULL DEFAULT NULL,
        ADD COLUMN `ebay_account_id` INT NULL DEFAULT NULL;

    CREATE  TABLE `PREFIX_prestabay_ebay_listings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account_id` INT NULL ,
      `item_id` VARCHAR(45) NULL ,
      `title` VARCHAR(85) NULL ,
      `start_time` DATETIME NULL ,
      `buy_price` FLOAT NULL ,
      `currency` VARCHAR(45) NULL ,
      `qty` INT NULL ,
      `qty_available` INT NULL ,
      `url` VARCHAR(255) NULL ,
      `picture_url` VARCHAR(255) NULL ,
      `sku` VARCHAR(100) NULL ,
      `listing_type` INT NULL ,
      `listing_duration` VARCHAR(45) NULL ,
      `status` TINYINT NULL ,
      PRIMARY KEY (`id`) );

    INSERT INTO `PREFIX_prestabay_marketplaces` (`id`, `code`, `label`, `url`)
        VALUES ('100', 'eBayMotors', 'eBay Motors', 'motors.ebay.com');


");

function categoryModeMigrate() {

    $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_list WHERE mode = 1 AND category_id > 0";
    $sellingList = Db::getInstance()->ExecuteS($sql);
    foreach ($sellingList as $singleSelling) {
            Db::getInstance()->autoExecute(_DB_PREFIX_ . 'prestabay_selling_categories', array(
                'selling_id' => $singleSelling['id'],
                'category_id' => $singleSelling['category_id']), 'INSERT');

    }

}