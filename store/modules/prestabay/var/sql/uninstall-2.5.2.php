<?php
/**
 * File uninstall-2.4.3.php
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

$installer->executeSql("
DROP TABLE `PREFIX_prestabay_accounts`,
           `PREFIX_prestabay_import_categories`,
           `PREFIX_prestabay_import_shipping`,
           `PREFIX_prestabay_marketplaces`,
           `PREFIX_prestabay_profiles`,
           `PREFIX_prestabay_selling_list`,
           `PREFIX_prestabay_selling_products`,
           `PREFIX_prestabay_log_selling`,
           `PREFIX_prestabay_log_sync`,
           `PREFIX_prestabay_order`,
           `PREFIX_prestabay_order_log`,
           `PREFIX_prestabay_order_items`,
           `PREFIX_prestabay_order_external_transactions`,
           `PREFIX_prestabay_product_connection`,
           `PREFIX_prestabay_template_price`,
           `PREFIX_prestabay_template_price_conditions`,
           `PREFIX_prestabay_template_shipping`,
           `PREFIX_prestabay_template_shipping_conditions`,
           `PREFIX_prestabay_template_description`,
           `PREFIX_prestabay_selling_variations`,
           `PREFIX_prestabay_selling_variations_options`,
           `PREFIX_prestabay_selling_categories`,
           `PREFIX_prestabay_ebay_listings`,
           `PREFIX_prestabay_product_ebay_data`,
           `PREFIX_prestabay_ebay_store_mapping`,
           `PREFIX_prestabay_ebay_store_mapping_categories`,
           `PREFIX_prestabay_feedbacks`,
           `PREFIX_prestabay_feedbacks_templates`,
           `PREFIX_prestabay_messages`,
           `PREFIX_prestabay_category_mapping`,
           `PREFIX_prestabay_category_mapping_line`,
           `PREFIX_prestabay_category_mapping_categories`,
           `PREFIX_prestabay_notifications`
");