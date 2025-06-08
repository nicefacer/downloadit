<?php
/**
 * File upgrade-2.4.3-2.5.0.php
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
$installer->executeSql(
    "
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

ALTER TABLE `PREFIX_prestabay_selling_list`
    ADD COLUMN `duplicate_protect_mode` TINYINT(1) NULL DEFAULT 0;

ALTER TABLE `PREFIX_prestabay_profiles`
    ADD COLUMN `sku_variation` TINYINT(1) NULL DEFAULT 0;

ALTER TABLE `PREFIX_prestabay_profiles`
  ADD COLUMN `price_discount` DOUBLE NOT NULL DEFAULT 0;

ALTER TABLE `PREFIX_prestabay_selling_products`
    ADD COLUMN `full_revise` TINYINT(1) NULL DEFAULT 0;


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
"
);