<?php
/**
 * File upgrade-2.3.0-2.4.0.php
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
    ALTER TABLE `PREFIX_prestabay_ebay_listings`
        ADD COLUMN `product_id` INT(11) UNSIGNED NULL DEFAULT NULL;

    ALTER TABLE `PREFIX_prestabay_marketplaces`
      ADD COLUMN `identify_unavailable_text` VARCHAR(255) NULL DEFAULT NULL;

    UPDATE PREFIX_prestabay_marketplaces SET
            `version` = 0,
            `status` = 0,
            `date_upd` = null,
            `dispatch` = null,
            `policy` = null,
            `payment_methods` = null,
            `shipping_location` = null,
            `shipping_exclude_location` = null,
            `shipping_packages` = null,
            `identify_unavailable_text` = null;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `identify_not_available` TINYINT NULL DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `global_shipping` TINYINT(1) NULL DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `shipping_calculated_local_handling_cost` DOUBLE DEFAULT NULL;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `shipping_calculated_int_handling_cost` DOUBLE DEFAULT NULL;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `identify_variation` TINYINT(1) NULL DEFAULT 0;


"
);