<?php
/**
 * File upgrade-1.3.2-1.3.3.php
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
 * @copyright   Copyright (c) 2011 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

$installer = $this;
$installer->executeSql("
    ALTER TABLE `PREFIX_prestabay_selling_list` MODIFY COLUMN `name` VARCHAR(255);
 ");
