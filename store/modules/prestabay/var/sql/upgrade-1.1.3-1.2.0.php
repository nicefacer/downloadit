<?php
/**
 * File upgrade-1.1.3-1.2.0.php
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

$installer->registerHook("backOfficeTop");
$installer->registerHook("backOfficeFooter");
$installer->registerHook("header");
$installer->registerHook("addproduct");

$installer->executeSql("

ALTER TABLE `PREFIX_prestabay_selling_list`
      ADD COLUMN `mode` TINYINT(1) NOT NULL DEFAULT 0,
      ADD COLUMN `category_id` INTEGER DEFAULT NULL;

ALTER TABLE `PREFIX_prestabay_profiles`
      ADD COLUMN `price_start_template` INTEGER DEFAULT NULL,
      ADD COLUMN `price_reserve_template` INTEGER DEFAULT NULL,
      ADD COLUMN `price_buynow_template` INTEGER DEFAULT NULL,
      ADD COLUMN `ebay_secondary_category_name` VARCHAR(100) DEFAULT NULL,
      ADD COLUMN `ebay_secondary_category_value` BIGINT(20) DEFAULT NULL,
      ADD COLUMN `ebay_store_category_main` BIGINT(20) DEFAULT NULL,
      ADD COLUMN `ebay_store_category_secondary` BIGINT(20) DEFAULT NULL,
      ADD COLUMN `remove_more_55` TINYINT(1) NOT NULL DEFAULT 0,
      ADD COLUMN `hit_counter` VARCHAR(50) NOT NULL DEFAULT 'NoHitCounter',
      ADD COLUMN `item_sku` TINYINT(1)  NOT NULL DEFAULT 0,
      ADD COLUMN `product_specifics_attribute` TEXT  DEFAULT NULL,
      ADD COLUMN `attribute_set_id` INTEGER  DEFAULT 0;

CREATE TABLE `PREFIX_prestabay_order` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `order_id` VARCHAR(35)  NOT NULL,
  `presta_order_id` INTEGER  DEFAULT NULL,
  `containing_order` TINYINT(1)  NOT NULL DEFAULT 0 COMMENT '0 - false, 1 - true',
  `buyer_id` VARCHAR(75)  NOT NULL,
  `buyer_email` VARCHAR(100)  NOT NULL,
  `buyer_name` VARCHAR(150)  NOT NULL,
  `buyer_address` TEXT  NOT NULL,
  `status_checkout` TINYINT(1)  NOT NULL DEFAULT 0,
  `status_payment` TINYINT(1)  NOT NULL DEFAULT 0,
  `status_shipping` TINYINT(1)  NOT NULL DEFAULT 0,
  `paid` FLOAT  NOT NULL,
  `currency` VARCHAR(15)  NOT NULL,
  `message` TEXT  NOT NULL,
  `payment_method` VARCHAR(50)  DEFAULT NULL,
  `payment_paypal_email` VARCHAR(100)  DEFAULT NULL,
  `payment_date` DATETIME  DEFAULT NULL,
  `shipping_method` VARCHAR(50)  DEFAULT NULL,
  `shipping_cost` FLOAT  DEFAULT NULL,
  `shipping_date` DATETIME  DEFAULT NULL,
  `create_date` DATETIME  NOT NULL,
  `update_date` DATETIME  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE  `PREFIX_prestabay_order_items` (
  `id` int(11) NOT NULL auto_increment,
  `order_id` int(11) NOT NULL,
  `item_id` bigint(20) NOT NULL,
  `presta_id` int(11) default NULL,
  `presta_lang_id` int(11)  DEFAULT NULL,
  `transaction_id` bigint(20) NOT NULL,
  `title` varchar(100) NOT NULL,
  `sku` varchar(100) default NULL,
  `qty` int(11) NOT NULL,
  `price` float NOT NULL,
  `currency` varchar(15) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE  `PREFIX_prestabay_product_connection` (
  `id` int(11) NOT NULL auto_increment,
  `presta_id` int(11) NOT NULL,
  `language_id` int(11) NOT NULL,
  `ebay_id` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `PREFIX_prestabay_template_price` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(75)  NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `PREFIX_prestabay_template_price_conditions` (
  `id` INTEGER  NOT NULL AUTO_INCREMENT,
  `price_id` INTEGER  NOT NULL,
  `type` TINYINT(1)  NOT NULL DEFAULT 0 COMMENT '0 - any, 1 - range',
  `price_from` FLOAT  DEFAULT NULL,
  `price_to` FLOAT  DEFAULT NULL,
  `price_source` TINYINT(1)  NOT NULL DEFAULT 1 COMMENT '1 - product price, 2 - custom value',
  `price_custom_value` FLOAT  DEFAULT NULL,
  `price_ratio` VARCHAR(15)  NOT NULL DEFAULT 'x1',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

");
