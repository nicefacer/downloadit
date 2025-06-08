<?php
/**
 * File shipping_callback.php
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

/** PrestaShop Initilization */
error_reporting(E_ERROR | E_WARNING | E_PARSE);
include(dirname(__FILE__) . '/../../config/config.inc.php');
if (!Configuration::get("INVEBAY_LICENSE_KEY")) {
    die(""); // PrestaBay not configurated
}
$domain = Configuration::get('PS_SHOP_DOMAIN') ? Configuration::get('PS_SHOP_DOMAIN') : Configuration::get(
    "INVEBAY_SHOP_DOMAIN"
);
if (!$domain) {
    die("Don't have correct domain name. Can't call cron job");
}
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'] = $domain;
include(dirname(__FILE__) . '/../../init.php');

// Load PrestaBay Loader
if (!defined('_PRESTABAY_AUTOLOADER_LOADED_') || !_PRESTABAY_AUTOLOADER_LOADED_) {
    $path = _PS_MODULE_DIR_ . 'prestabay/';
    include($path . 'library/Autoloader.php');
    Autoloader::init($path);
}

if (!isset($_GET['id_order'])) {
    echo json_encode(array('success' => false));
    return;
}

$idOrder    = (int)$_GET['id_order'];
$orderModel = new Order_OrderModel($idOrder);
if (!$orderModel->id) {
    echo json_encode(array('success' => false));

    return;
}

$result = $orderModel->updateEbayOrderStatusByPrestaBayId(_PS_OS_SHIPPING_);

echo json_encode(array('success' => (bool)$result));
return;


