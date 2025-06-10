<?php
/**
 * gmerchantcenter.xml.php file execute the fly output data feed
 *
 * @author    Business Tech SARL <http://www.businesstech.fr/en/contact-us>
 * @copyright 2003-2015 Business Tech SARL
 * @license http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

require_once(dirname(__FILE__).'/config/config.inc.php');
require_once(dirname(__FILE__).'/init.php');
require_once(_PS_MODULE_DIR_.'/gmerchantcenterpro/gmerchantcenterpro.php');

// instantiate
$oMainClass = new GMerchantCenterPro();

// use case - handle to generate XML files
$_POST['sAction'] = Tools::getIsset('sAction')? Tools::getValue('sAction') : 'generate';
$_POST['sType'] = Tools::getIsset('sType')? Tools::getValue('sType') : 'flyOutput';

echo $oMainClass->getContent();