<?php
/**
 * File upgrade-2.1.2-2.2.0.php
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
    ALTER TABLE `PREFIX_prestabay_template_price_conditions` ADD COLUMN `source` TINYINT(1) NULL DEFAULT 0;

    ALTER TABLE `PREFIX_prestabay_profiles`
            ADD COLUMN `mpn` TINYINT NULL DEFAULT NULL,
            ADD COLUMN `isbn` TINYINT NULL DEFAULT NULL;
"
);