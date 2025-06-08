<?php
/**
 * File cron.php
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
$domain = Configuration::get("INVEBAY_SHOP_DOMAIN");
if (!$domain) {
    die("Don't have correct domain name. Can't call cron job");
}
$_SERVER['SERVER_NAME'] = $_SERVER['HTTP_HOST'] = $_SERVER['HTTP_X_FORWARDED_HOST'] = $domain;
include(dirname(__FILE__).'/../../init.php');

/** Check Last Cron Execution Time */
$cronTime = Configuration::get("INVEBAY_SYNC_CRON_TIME");
$nowTime = time();

if ($cronTime && ceil(abs($nowTime - strtotime($cronTime))/60) < 5) {
    // Have last cron time, and last execution time less that 5 minues
    die("Cron run less that 5 minues");
}
// @todo uncomment this line if some problem with saving value into DB
Db::getInstance()->execute("SET sql_mode = ''", false);


// Load PrestaBay Loader
if (!defined('_PRESTABAY_AUTOLOADER_LOADED_') || !_PRESTABAY_AUTOLOADER_LOADED_) {
    $path = _PS_MODULE_DIR_ . 'prestabay/';
    include($path . 'library/Autoloader.php');
    Autoloader::init($path);
}

if (CoreHelper::isPS15()) {
    $sqlToGetE = "SELECT id_employee  FROM " . _DB_PREFIX_ . "employee WHERE active = 1";
    $valueId = Db::getInstance()->getValue($sqlToGetE, false);
    if ($valueId > 0) {
        $employee = new Employee($valueId);
        Context::getContext()->employee = $employee;
    }
}

if (CoreHelper::isPS15()) {
    // Generate correct URL for console application
    Context::getContext()->shop->setUrl();
}

// Execute All Synch Task
$syncModel = new Synchronization_Run();
$syncModel->execute();

Configuration::updateValue("INVEBAY_SYNC_CRON_TIME", date("Y-m-d H:i:s", $nowTime));

// Check and run if required auto synch log cleaner
if ((int)Configuration::get("INVEBAY_AUTOCLEAR_VALUE") <= 0) {
    // auto clean not enabled
    die("");
}

/** Check Last Clear Execution Time */
$clearTime = Configuration::get("INVEBAY_AUTOCLEAR_TIME");
$nowClearTime = time();

if ($nowClearTime && ceil(abs($nowClearTime - strtotime($clearTime))/3600) < 12) {
    // Cleaner run less that 12 hour ago
    die("");
}

// Run auto clear log
Log_SyncModel::clearLog((int)Configuration::get("INVEBAY_AUTOCLEAR_VALUE"));
Configuration::updateValue("INVEBAY_AUTOCLEAR_TIME", date("Y-m-d H:i:s", $nowClearTime));
