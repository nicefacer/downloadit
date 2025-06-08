<?php
/**
 * File upgrade-1.5.11-2.0.0.php
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

$installer->registerHook('displayAdminProductsExtra');

$installer->executeSql(
    "
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

    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `ebay_store_mode` TINYINT(4) NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `ebay_store_mapping_id` INT NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `multivariation_images` TINYINT(1) NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `ebay_category_mode` TINYINT NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `ebay_category_mapping_id` INT NULL DEFAULT 0;

    CREATE TABLE `PREFIX_prestabay_ebay_store_mapping` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `name` VARCHAR(200) NULL ,
          `account_id` INT UNSIGNED NOT NULL ,
          PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE  TABLE `PREFIX_prestabay_ebay_store_mapping_categories` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `mapping_id` INT  NOT NULL ,
      `ebay_store_category_id` BIGINT NOT NULL ,
      `ebay_secondary_category_id` BIGINT NOT NULL ,
      `ps_category_id` INT NOT NULL ,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_feedbacks` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `account_id` INT(11) NOT NULL,
          `item_id` BIGINT(20) NOT NULL,
          `transaction_id` BIGINT(20) NOT NULL,
          `title` VARCHAR(100) DEFAULT NULL,
          `buyer_feedback_id` BIGINT(20) DEFAULT NULL,
          `buyer_name` VARCHAR(100) DEFAULT NULL,
          `buyer_time` DATETIME DEFAULT NULL,
          `buyer_comment` TEXT,
          `buyer_type` ENUM('Neutral','Negative','Positive') DEFAULT NULL,
          `seller_feedback_id` BIGINT(20) DEFAULT NULL,
          `seller_time` DATETIME DEFAULT NULL,
          `seller_comment` TEXT,
          `seller_type` ENUM('Neutral','Negative','Positive') DEFAULT NULL,
          `date_upd` DATETIME DEFAULT NULL,
          PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_feedbacks_templates` (
        `id` INT NOT NULL AUTO_INCREMENT ,
        `message` VARCHAR(80) NULL,
        `feedback_type` ENUM('Negative','Neutral', 'Positive') NULL,
        `date_upd` DATETIME NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

    CREATE TABLE `PREFIX_prestabay_messages` (
          `id` INT(11) NOT NULL AUTO_INCREMENT,
          `account_id` INT NOT NULL,
          `message_id` VARCHAR(20) DEFAULT NULL,
          `item_id` VARCHAR(20) DEFAULT NULL,
          `title` VARCHAR(100) DEFAULT NULL,
          `message_type` ENUM('AskSellerQuestion','ContactTransactionPartner','ContactMyBidder','ContactEbayMember','ResponseToASQQuestion','ResponseToContacteBayMember') DEFAULT NULL,
          `question_type` ENUM('None','MultipleItemShipping','General','Payment','Shipping','CustomizedSubject') DEFAULT NULL,
          `sender` VARCHAR(50) DEFAULT NULL,
          `subject` TEXT,
          `text` TEXT,
          `status` ENUM('Answered','Unanswered') DEFAULT NULL,
          `date` DATETIME DEFAULT NULL,
          `replay` TEXT,
          `date_upd` DATETIME DEFAULT NULL,
          PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    CREATE  TABLE `PREFIX_prestabay_category_mapping` (
      `id` INT NOT NULL AUTO_INCREMENT ,
      `name` VARCHAR(100) NOT NULL,
      `marketplace_id` INT NOT NULL ,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB  DEFAULT CHARSET=utf8;

    CREATE  TABLE `PREFIX_prestabay_category_mapping_line` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `mapping_id` INT NULL ,
          `ebay_primary_category_name` VARCHAR(300) NOT NULL ,
          `ebay_primary_category_value` BIGINT(20) NOT NULL ,
          `ebay_secondary_category_name` VARCHAR(300) NULL ,
          `ebay_secondary_category_value` BIGINT(20) NULL ,
          `item_condition` VARCHAR(100) NULL,
          `product_specifics` TEXT NULL,
          `product_specifics_custom` TEXT NULL ,
          PRIMARY KEY (`id`)
    ) ENGINE = InnoDB  DEFAULT CHARSET=utf8;

    CREATE  TABLE `PREFIX_prestabay_category_mapping_categories` (
          `id` INT NOT NULL AUTO_INCREMENT ,
          `mapping_id` INT NOT NULL ,
          `mapping_line_id` INT NOT NULL ,
          `category_id` INT NOT NULL ,
          `category_name` VARCHAR(45) NOT NULL ,
          `category_path` TEXT NOT NULL ,
      PRIMARY KEY (`id`)
    ) ENGINE = InnoDB   DEFAULT CHARSET=utf8;

"
);