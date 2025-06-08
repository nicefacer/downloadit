<?php
/**
 * File upgrade-1.0.4-1.1.0.php
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

$this->registerHook('newOrder');
$this->registerHook('updateproduct');

$installer->executeSql("

ALTER TABLE `PREFIX_prestabay_profiles` 
    ADD COLUMN `item_vat` FLOAT  NOT NULL DEFAULT 0,
    ADD COLUMN `product_specifics` TEXT DEFAULT NULL;

ALTER TABLE `PREFIX_prestabay_selling_products`
 ADD COLUMN `product_qty_change` INTEGER  NOT NULL DEFAULT 0,
 ADD COLUMN `ebay_sold_qty` INTEGER  NOT NULL DEFAULT 0,
 ADD COLUMN `ebay_sold_qty_sync` INTEGER  NOT NULL DEFAULT 0;

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
  PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET=utf8;

");
