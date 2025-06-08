<?php
/**
 * File install-2.5.0.php
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

$installer->registerHook('newOrder');
$installer->registerHook('updateproduct');
$installer->registerHook("addproduct");
$installer->registerHook('updateProductAttribute');
$installer->registerHook('updateQuantity');
$installer->registerHook('postUpdateOrderStatus');
$installer->registerHook('actionAdminOrdersTrackingNumberUpdate');
$installer->registerHook('displayAdminProductsExtra');

$installer->executeSql("
    CREATE TABLE  `PREFIX_prestabay_accounts` (
      `id` int(10) unsigned NOT NULL auto_increment,
      `name` varchar(255) NOT NULL,
      `token` text NOT NULL,
      `exp_date` datetime default NULL,
      `mode` tinyint(1) NOT NULL default '0',
      `date_add` datetime default NULL,
      `date_upd` datetime default NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_import_categories` (
      `id` bigint(20) unsigned NOT NULL auto_increment,
      `category_id` bigint(20) unsigned NOT NULL,
      `parent_category_id` bigint(20) unsigned NOT NULL default '0',
      `marketplace_id` int(11) unsigned NOT NULL,
      `name` varchar(100) NOT NULL,
      PRIMARY KEY  (`id`),
      KEY `marketplace_id` (`marketplace_id`),
      KEY `parent_category_id` (`parent_category_id`),
      KEY `name` (`name`),
      KEY `category_id` (`category_id`)
    ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE  `PREFIX_prestabay_import_shipping` (
      `id` int(11) NOT NULL auto_increment,
      `site_id` int(11) NOT NULL,
      `shipping_ebay_name` varchar(255) NOT NULL,
      `title` varchar(255) NOT NULL,
      `flat` tinyint(1) NOT NULL default '0',
      `calculated` tinyint(1) NOT NULL default '0',
      `international` tinyint(1) NOT NULL default '0',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE  `PREFIX_prestabay_marketplaces` (
      `id` int(10) unsigned NOT NULL auto_increment,
      `code` varchar(40) NOT NULL,
      `label` varchar(50) NOT NULL,
      `url` varchar(50) NOT NULL,
      `date_upd` datetime default NULL,
      `status` tinyint(1) NOT NULL,
      `version` int(10) unsigned NOT NULL default '0',
      `dispatch` text,
      `policy` text,
      `payment_methods` text,
      `shipping_location` text,
      `shipping_exclude_location` TEXT  DEFAULT NULL,
      `shipping_packages` TEXT  DEFAULT NULL,
      `identify_unavailable_text` VARCHAR(255) NULL DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE  `PREFIX_prestabay_profiles` (
      `id` int(11) NOT NULL auto_increment,
      `ebay_account` int(11) NOT NULL,
      `ebay_site` int(11) NOT NULL,
      `profile_name` varchar(100) NOT NULL,
      `ebay_primary_category_name` varchar(100) default NULL,
      `ebay_primary_category_value` bigint(20) UNSIGNED NOT NULL,
      `ebay_secondary_category_name` VARCHAR(100) DEFAULT NULL,
      `ebay_secondary_category_value` BIGINT(20) UNSIGNED DEFAULT NULL,
      `ebay_store_category_main` BIGINT(20) UNSIGNED DEFAULT NULL,
      `ebay_store_category_secondary` BIGINT(20) UNSIGNED DEFAULT NULL,
      `auction_type` tinyint(1) NOT NULL default '2',
      `auction_duration` varchar(20) NOT NULL,
      `item_title` varchar(255) NOT NULL,
      `subtitle` VARCHAR(255) DEFAULT NULL,
      `remove_more_80` TINYINT(1) NOT NULL DEFAULT 0,
      `item_condition` int(11) default NULL,
      `item_condition_description` VARCHAR(45) default NULL,
      `item_qty_mode` tinyint(1) NOT NULL default '2',
      `item_qty_value` int(11) default NULL,
      `item_image` tinyint(1) NOT NULL default '1',
      `item_image_count` tinyint(2) default NULL,
      `item_description_mode` tinyint(1) NOT NULL,
      `item_description_custom` text NOT NULL,
      `description_template_id` INTEGER DEFAULT 0,
      `item_currency` varchar(3) NOT NULL,
      `item_vat` FLOAT NOT NULL DEFAULT 0,
      `hit_counter` VARCHAR(50) NOT NULL DEFAULT 'NoHitCounter',
      `item_sku` TINYINT(1)  NOT NULL DEFAULT 0,
      `price_start` tinyint(1) NOT NULL default '1',
      `price_start_multiply` double NOT NULL default '1',
      `price_start_custom` double NOT NULL default '0',
      `price_start_template` INTEGER DEFAULT NULL,
      `price_reserve` tinyint(1) NOT NULL default '1',
      `price_reserve_multiply` double NOT NULL default '1',
      `price_reserve_custom` double NOT NULL default '0',
      `price_reserve_template` INTEGER DEFAULT NULL,
      `price_buynow` tinyint(1) NOT NULL default '1',
      `price_buynow_multiply` double NOT NULL default '1',
      `price_buynow_custom` double NOT NULL default '0',
      `price_buynow_template` INTEGER DEFAULT NULL,
      `price_discount` DOUBLE NOT NULL DEFAULT 0,
      `payment_methods` text,
      `payment_paypal_email` varchar(100) default NULL,
      `autopay` TINYINT(1) DEFAULT 0,
      `shipping_country` varchar(200) NOT NULL,
      `shipping_location` varchar(200) NOT NULL,
      `shipping_dispatch` varchar(50) NOT NULL,
      `shipping_local` text,
      `shipping_int` text,
      `shipping_exclude_location` TEXT  DEFAULT NULL,
      `shipping_allowed_location` TEXT DEFAULT NULL,
      `cross_border_trade` TINYINT(1) DEFAULT 0,
      `shipping_local_type` TINYINT(1)  NOT NULL DEFAULT 0,
      `shipping_int_type` TINYINT(1)  NOT NULL DEFAULT 0,
      `shipping_calculated_measurement` VARCHAR(75)  DEFAULT NULL,
      `shipping_calculated_package` VARCHAR(75)  DEFAULT NULL,
      `shipping_calculated_depth` TINYINT(1)  DEFAULT NULL,
      `shipping_calculated_depth_custom` INTEGER  DEFAULT NULL,
      `shipping_calculated_length` TINYINT(1)  DEFAULT NULL,
      `shipping_calculated_length_custom` INTEGER  DEFAULT NULL,
      `shipping_calculated_width` TINYINT(1)  DEFAULT NULL,
      `shipping_calculated_width_custom` INTEGER  DEFAULT NULL,
      `shipping_calculated_weight` TINYINT(1)  DEFAULT NULL,
      `shipping_calculated_weight_custom` FLOAT  DEFAULT NULL,
      `shipping_calculated_postal` VARCHAR(10)  DEFAULT NULL,
      `returns_accepted` varchar(50) default NULL,
      `refund` varchar(50) default NULL,
      `returns_within` varchar(50) default NULL,
      `shipping_cost_paid_by` varchar(50) default NULL,
      `refund_description` text,
      `shipping_to_location` text,
      `date_add` datetime default NULL,
      `date_upd` datetime default NULL,
      `product_specifics` TEXT DEFAULT NULL,
      `product_specifics_attribute` TEXT  DEFAULT NULL,
      `product_specifics_custom` TEXT  DEFAULT NULL,
      `attribute_set_id` INTEGER  DEFAULT 0,
      `use_multivariation` TINYINT(1) DEFAULT 0,
      `private_listing` TINYINT(1) DEFAULT 0,
      `get_it_fast` TINYINT(1) DEFAULT 0,
      `cod_cost_italy` DOUBLE  DEFAULT NULL,
      `best_offer_enabled` TINYINT(1) UNSIGNED NOT NULL DEFAULT 0,
      `best_offer_minimum_price` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
      `best_offer_auto_accept_price` TINYINT(2) UNSIGNED NOT NULL DEFAULT 0,
      `enhancement` TEXT NULL DEFAULT NULL,
      `ps_image_type` VARCHAR(45) NULL DEFAULT 'thickbox',
      `gallery_type` VARCHAR(45) NULL DEFAULT 'None',
      `photo_display` VARCHAR(45) NULL DEFAULT 'None',
      `ean` TINYINT NULL DEFAULT NULL,
      `upc` TINYINT NULL DEFAULT NULL,
      `mpn` TINYINT NULL DEFAULT NULL,
      `isbn` TINYINT NULL DEFAULT NULL,
      `payment_instruction` TEXT NULL DEFAULT NULL,
      `gift_icon` TINYINT NULL DEFAULT 0,
      `gift_services` TEXT NULL DEFAULT NULL,
      `insurance_fee` DOUBLE NULL DEFAULT 0,
      `insurance_option` VARCHAR(45) NULL DEFAULT NULL,
      `insurance_international_fee` DOUBLE NULL DEFAULT 0,
      `insurance_international_option` VARCHAR(45) NULL DEFAULT NULL,
      `unit_include` TINYINT NULL DEFAULT 0,
      `unit_type` VARCHAR(45) NULL DEFAULT NULL,
      `promotional_shipping_discount` TINYINT NULL DEFAULT 0,
      `promotional_int_shipping_discount` TINYINT NULL DEFAULT 0,
      `shipping_discount_profile_id` VARCHAR(45)  NULL DEFAULT '',
      `int_shipping_discount_profile_id` VARCHAR(45) NULL DEFAULT '',
      `restock_fee` VARCHAR(45) NULL,
      `ebay_store_mode` TINYINT(4) NULL DEFAULT 0,
      `ebay_store_mapping_id` INT NULL DEFAULT 0,
      `multivariation_images` TINYINT(1) NULL DEFAULT 0,
      `ebay_category_mode` TINYINT NULL DEFAULT 0,
      `ebay_category_mapping_id` INT NULL DEFAULT 0,
      `identify_not_available` TINYINT NULL DEFAULT 0,
      `global_shipping` TINYINT(1) NULL DEFAULT 0,
      `shipping_calculated_local_handling_cost` DOUBLE  DEFAULT NULL,
      `shipping_calculated_int_handling_cost` DOUBLE  DEFAULT NULL,
      `identify_variation` TINYINT(1) NULL DEFAULT 0,
      `sku_variation` TINYINT(1) NULL DEFAULT 0,
       PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE  `PREFIX_prestabay_selling_list` (
      `id` int(11) NOT NULL auto_increment,
      `name` varchar(255) NOT NULL,
      `language` int(11) NOT NULL,
      `profile` int(11) NOT NULL,
      `mode` TINYINT(1) NOT NULL DEFAULT 0,
      `category_id` INTEGER DEFAULT NULL,
      `attribute_mode` TINYINT(1)  NOT NULL DEFAULT 0,
      `category_send_product` TINYINT(1) NULL DEFAULT NULL,
      `duplicate_protect_mode` TINYINT(1) NULL DEFAULT 0,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE  `PREFIX_prestabay_selling_products` (
      `id` int(11) NOT NULL auto_increment,
      `selling_id` int(11) NOT NULL,
      `product_id` int(11) NOT NULL,
      `product_id_attribute` INTEGER DEFAULT 0,
      `product_name` varchar(255) NOT NULL,
      `product_price` float NOT NULL,
      `product_qty` int(11) NOT NULL,
      `product_qty_change` INTEGER  NOT NULL DEFAULT 0,
      `product_price_change` FLOAT NOT NULL DEFAULT 0,
      `ebay_id` bigint(20) default NULL,
      `ebay_name` varchar(80) default NULL,
      `ebay_price` float default NULL,
      `ebay_qty` int(11) default NULL,
      `ebay_sold_qty` INTEGER  NOT NULL DEFAULT 0,
      `ebay_sold_qty_sync` INTEGER  NOT NULL DEFAULT 0,
      `ebay_start_time` datetime default NULL,
      `ebay_end_time` datetime default NULL,
      `full_revise` TINYINT(1) NULL DEFAULT 0,
      `status` tinyint(1) NOT NULL default '0',
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    
    CREATE  TABLE `PREFIX_prestabay_selling_categories` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `selling_id` INT NULL ,
      `category_id` INT NULL ,
      PRIMARY KEY (`id`) 
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
    
    INSERT INTO `PREFIX_prestabay_marketplaces` (`id`, `code`, `label`, `url`, `date_upd`, `status`, `version`, `dispatch`, `policy`, `payment_methods`, `shipping_location`) VALUES
    (1, 'US', 'United States', 'ebay.com', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (2, 'Canada', 'Canada', 'ebay.ca', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (3, 'UK', 'United Kingdom', 'ebay.co.uk', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (77, 'Germany', 'Germany', 'ebay.de', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (15, 'Australia', 'Australia', 'ebay.com.au', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (71, 'France', 'France', 'ebay.fr', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (101, 'Italy', 'Italy', 'ebay.it', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (146, 'Netherlands', 'Netherlands', 'ebay.nl', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (186, 'Spain', 'Spain', 'ebay.es', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (203, 'India', 'India', 'ebay.in', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (201, 'HongKong', 'Hong Kong', 'ebay.com.hk', NULL, 0, 0, NULL, NULL, NULL, NULL);

    INSERT INTO `PREFIX_prestabay_marketplaces` (`id`, `code`, `label`, `url`, `date_upd`, `status`, `version`, `dispatch`, `policy`, `payment_methods`, `shipping_location`) VALUES
    (216, 'Singapore', 'Singapore', 'ebay.com.sg', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (207, 'Malaysia', 'Malaysia', 'ebay.com.my', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (211, 'Philippines', 'Philippines', 'ebay.ph', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (210, 'CanadaFrench', 'Canada (French)', 'cafr.ebay.ca', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (212, 'Poland', 'Poland', 'ebay.pl', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (123, 'Belgium_Dutch', 'Belgium (Dutch)', 'benl.ebay.be', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (23, 'Belgium_French', 'Belgium (French)', 'befr.ebay.be', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (16, 'Austria', 'Austria', 'ebay.at', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (193, 'Switzerland', 'Switzerland', 'ebay.ch', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (205, 'Ireland', 'Ireland', 'ebay.ie', NULL, 0, 0, NULL, NULL, NULL, NULL),
    (100, 'eBayMotors', 'eBay Motors', 'ebay.com', NULL, 0, 0, NULL, NULL, NULL, NULL);

    CREATE TABLE `PREFIX_prestabay_log_selling` (
      `id` int(11) NOT NULL auto_increment,
      `selling_id` int(11) NOT NULL,
      `selling_product_id` int(11) NOT NULL,
      `action` enum('send','relist','revise','stop') NOT NULL,
      `message` text NOT NULL,
      `level` enum('error','warning','notice') NOT NULL default 'notice',
      `date_add` datetime NOT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_log_sync` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `task` TINYINT(1)  NOT NULL DEFAULT 0,
      `message` TEXT NOT NULL,
      `level` enum('error','warning','notice')  NOT NULL DEFAULT 'notice',
      `date_add` DATETIME  NOT NULL,
      `selling_product_id` INT NULL DEFAULT NULL,
      `ps_product_id` INT NULL DEFAULT NULL,
      `pb_order_id` INT NULL DEFAULT NULL,
      `ebay_item_id` BIGINT(20) NULL DEFAULT NULL,
      `ebay_account_id` INT NULL DEFAULT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_order` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `order_id` VARCHAR(35)  NOT NULL,
      `account_id` INTEGER  DEFAULT NULL,
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
      `tax` TEXT DEFAULT NULL,
      `create_date` DATETIME  NOT NULL,
      `update_date` DATETIME  NOT NULL,
      `sales_record_number` INT UNSIGNED NULL DEFAULT NULL,
      `order_to_process` TINYINT(1) NULL DEFAULT 0,
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
      `presta_attr_id` INTEGER DEFAULT NULL,
      `variation_info` TEXT DEFAULT NULL,
      PRIMARY KEY  (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
    
    CREATE TABLE  `PREFIX_prestabay_product_connection` (
      `id` int(11) NOT NULL auto_increment,
      `presta_id` int(11) NOT NULL,
      `presta_attribute_id` INT NULL DEFAULT 0,
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
      `source` TINYINT(1) NULL DEFAULT 0  COMMENT '0 - product price, 1 - weight',
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

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

    CREATE TABLE `PREFIX_prestabay_selling_variations` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `selling_product_id` INTEGER  NOT NULL,
      `product_id_attribute` INTEGER  NOT NULL,
      `qty` INTEGER  NOT NULL,
      `qty_change` INTEGER  NOT NULL DEFAULT 0,
      `price` FLOAT  NOT NULL,
      `price_change` FLOAT NOT NULL DEFAULT 0,
      `sku` VARCHAR(50)  DEFAULT NULL,
      `ebay_qty` int(11) NOT NULL,
      `ebay_sold_qty` int(11) NOT NULL default '0',
      `ebay_sold_qty_sync` int(11) NOT NULL default '0',
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_selling_variations_options` (
      `id` int(11) NOT NULL auto_increment,
      `variation_id` INTEGER  NOT NULL,
      `key` VARCHAR(80)  NOT NULL,
      `value` VARCHAR(80)  NOT NULL,
       PRIMARY KEY  (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE  TABLE `PREFIX_prestabay_ebay_listings` (
      `id` INT(11) NOT NULL AUTO_INCREMENT,
      `account_id` INT NULL ,
      `item_id` VARCHAR(45) NULL ,
      `product_id` INT(11) UNSIGNED NULL DEFAULT NULL,
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
      PRIMARY KEY (`id`) 
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_product_ebay_data` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `product_id` INT UNSIGNED NULL ,
          `store_id` INT UNSIGNED NULL ,
          `item_title` VARCHAR(120) NULL ,
          `subtitle` VARCHAR(120) NULL ,
          `description` TEXT NULL ,
          `item_qty_value` INT NULL ,
          `price_value` FLOAT NULL ,
          `ebay_store_category_main_id` BIGINT UNSIGNED NULL ,
          `ebay_store_category_secondary_id` BIGINT UNSIGNED NULL ,
          PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE  TABLE `PREFIX_prestabay_ebay_store_mapping` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `name` VARCHAR(200) NULL ,
          `account_id` INT UNSIGNED NOT NULL ,
          PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE  TABLE `PREFIX_prestabay_ebay_store_mapping_categories` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `mapping_id` INT  NOT NULL ,
      `ebay_store_category_id` BIGINT NOT NULL ,
      `ebay_secondary_category_id` BIGINT NOT NULL ,
      `ps_category_id` INT NOT NULL ,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;;

    CREATE TABLE `PREFIX_prestabay_feedbacks` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `account_id` int(11) NOT NULL,
        `item_id` bigint(20) NOT NULL,
        `transaction_id` bigint(20) NOT NULL,
        `title` varchar(100) DEFAULT NULL,
        `buyer_feedback_id` bigint(20) DEFAULT NULL,
        `buyer_name` varchar(100) DEFAULT NULL,
        `buyer_time` datetime DEFAULT NULL,
        `buyer_comment` text,
        `buyer_type` enum('Neutral','Negative','Positive') DEFAULT NULL,
        `seller_feedback_id` bigint(20) DEFAULT NULL,
        `seller_time` datetime DEFAULT NULL,
        `seller_comment` text,
        `seller_type` enum('Neutral','Negative','Positive') DEFAULT NULL,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_feedbacks_templates` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `message` VARCHAR(80) NULL,
        `feedback_type` ENUM('Negative','Neutral', 'Positive') NULL,
        `date_upd` DATETIME NULL,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

    CREATE TABLE `PREFIX_prestabay_messages` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `account_id` INT NOT NULL,
        `message_id` varchar(20) DEFAULT NULL,
        `item_id` varchar(20) DEFAULT NULL,
        `title` varchar(100) DEFAULT NULL,
        `message_type` enum('AskSellerQuestion','ContactTransactionPartner','ContactMyBidder','ContactEbayMember','ResponseToASQQuestion','ResponseToContacteBayMember') DEFAULT NULL,
        `question_type` enum('None','MultipleItemShipping','General','Payment','Shipping','CustomizedSubject') DEFAULT NULL,
        `sender` varchar(50) DEFAULT NULL,
        `subject` text,
        `text` text,
        `status` enum('Answered','Unanswered') DEFAULT NULL,
        `date` datetime DEFAULT NULL,
        `replay` text,
        `date_upd` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

  CREATE  TABLE `PREFIX_prestabay_category_mapping` (
    `id` INT NOT NULL AUTO_INCREMENT ,
    `name` VARCHAR(100) NOT NULL,
    `marketplace_id` INT NOT NULL ,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

  CREATE  TABLE `PREFIX_prestabay_category_mapping_line` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `mapping_id` INT NULL ,
        `ebay_primary_category_name` VARCHAR(300) NOT NULL ,
        `ebay_primary_category_value` BIGINT(20) NOT NULL ,
        `ebay_secondary_category_name` VARCHAR(300) NULL ,
        `ebay_secondary_category_value` BIGINT(20) NULL ,
        `item_condition` VARCHAR(100) NULL,
        `item_condition_description` VARCHAR(100) NULL,
        `product_specifics` TEXT NULL,
        `product_specifics_custom` TEXT NULL ,
        PRIMARY KEY (`id`)
  ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

  CREATE  TABLE `PREFIX_prestabay_category_mapping_categories` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `mapping_id` INT NOT NULL ,
        `mapping_line_id` INT NOT NULL ,
        `category_id` INT NOT NULL ,
        `category_name` VARCHAR(45) NOT NULL ,
        `category_path` TEXT NOT NULL ,
    PRIMARY KEY (`id`)
  ) ENGINE = InnoDB CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE `PREFIX_prestabay_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message_id` int(11) NOT NULL,
  `title` varchar(150) NOT NULL,
  `message` text NOT NULL,
  `level` enum('notice','warning','error') DEFAULT 'warning',
  `type` enum('system', 'api') DEFAULT 'api',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `PREFIX_prestabay_selling_fee` (
  `id` INT NOT NULL  AUTO_INCREMENT,
  `ebay_id` BIGINT(20) NULL,
  `account_id` INT NULL,
  `selling_product_id` INT NULL,
  `product_id` INT NULL,
  `product_id_attribute` INT NULL,
  `action` ENUM('list', 'revise', 'relist') NULL,
  `fee_total` FLOAT NULL,
  `fee_currency` VARCHAR(15) NULL,
  `fee_list` TEXT NULL,
  `date_add` DATETIME NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

");
