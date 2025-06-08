<?php
/**
 * File upgrade-1.5.2-1.5.3.php
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
ALTER TABLE `PREFIX_prestabay_profiles`
    CHANGE COLUMN `ebay_primary_category_value` `ebay_primary_category_value` BIGINT(20) UNSIGNED NOT NULL,
    CHANGE COLUMN `ebay_secondary_category_value` `ebay_secondary_category_value` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    CHANGE COLUMN `ebay_store_category_main` `ebay_store_category_main` BIGINT(20) UNSIGNED NULL DEFAULT NULL,
    CHANGE COLUMN `ebay_store_category_secondary` `ebay_store_category_secondary` BIGINT(20) UNSIGNED NULL DEFAULT NULL ;
"
);