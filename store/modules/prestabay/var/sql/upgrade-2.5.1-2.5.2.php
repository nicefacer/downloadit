<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 *  It is available through the world-wide-web at this URL:
 *  http://involic.com/license.txt
 *  If you are unable to obtain it through the world-wide-web,
 *  please send an email to license@involic.com so
 *  we can send you a copy immediately.
 *
 *  PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 *  Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 *  @author      Involic <contacts@involic.com>
 *  @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 *  @license     http://involic.com/license.txt
 */

$installer = $this;
$installer->executeSql(
    "
ALTER TABLE `PREFIX_prestabay_profiles`
    ADD COLUMN `item_condition_description` VARCHAR(45) default NULL;

ALTER TABLE `PREFIX_prestabay_category_mapping_line`
    ADD COLUMN `item_condition_description` VARCHAR(100) default NULL;

ALTER TABLE `PREFIX_prestabay_order`
    ADD COLUMN `order_to_process` TINYINT(1) NULL DEFAULT 0;

"
);