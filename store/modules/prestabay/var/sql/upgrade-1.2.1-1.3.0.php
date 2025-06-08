<?php

/**
 * File upgrade-1.2.1-1.3.0.php
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

$installer->registerHook('updateProductAttribute');

$installer->executeSql("
    ALTER TABLE `PREFIX_prestabay_profiles` CHANGE COLUMN `remove_more_55` `remove_more_80` TINYINT(1)  NOT NULL DEFAULT 0;
    ALTER TABLE `PREFIX_prestabay_profiles` MODIFY COLUMN `item_title` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
    ALTER TABLE `PREFIX_prestabay_profiles` ADD COLUMN `use_multivariation` TINYINT(1) DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_selling_products` MODIFY COLUMN `ebay_name` VARCHAR(80) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL;

    CREATE TABLE `PREFIX_prestabay_selling_variations` (
      `id` INTEGER  NOT NULL AUTO_INCREMENT,
      `selling_product_id` INTEGER  NOT NULL,
      `product_id_attribute` INTEGER  NOT NULL,
      `qty` INTEGER  NOT NULL,
      `qty_change` INTEGER  NOT NULL DEFAULT 0,
      `price` FLOAT  NOT NULL,
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

    ALTER TABLE `PREFIX_prestabay_order_items` ADD COLUMN `presta_attr_id` INTEGER DEFAULT NULL;
    ALTER TABLE `PREFIX_prestabay_order_items` ADD COLUMN `variation_info` TEXT DEFAULT NULL;


");
