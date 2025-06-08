<?php

$sql = array();

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_contentanywhere` (
    `id_smart_contentanywhere` int(11) NOT NULL auto_increment,
    `hook_name` varchar(200) DEFAULT NULL,
    `active` int(11) DEFAULT NULL,
    `id_category` varchar(50) DEFAULT NULL,
    `id_product` varchar(50) DEFAULT NULL,
    `display_type` varchar(150) DEFAULT NULL,
    `prd_page` varchar(150) DEFAULT NULL,
    `prd_specify` varchar(150) DEFAULT NULL,
    `cat_page` varchar(150) DEFAULT NULL,
    `cat_specify` varchar(150) DEFAULT NULL,
    `cms_page` varchar(150) DEFAULT NULL,
    `cms_specify` varchar(150) DEFAULT NULL,
    `blg_page` varchar(150) DEFAULT NULL,
    `blg_specify` varchar(150) DEFAULT NULL,
    `position` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`id_smart_contentanywhere`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8';

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_contentanywhere_lang` (
  `id_smart_contentanywhere` int(11) NOT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `title` varchar(500) DEFAULT NULL,
  `content` text,
  PRIMARY KEY (`id_smart_contentanywhere`,`id_lang`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_contentanywhere_shop` (
  `id_smart_contentanywhere_shop`  int(11) NOT NULL auto_increment,
  `id_smart_contentanywhere`  int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  KEY(`id_smart_contentanywhere_shop`),
  PRIMARY KEY (`id_smart_contentanywhere`,`id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_product_tab`(
  `id_smart_product_tab` int(11) NOT NULL auto_increment,
  `id_product` int(11) DEFAULT NULL,
  `active` int(11) DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_smart_product_tab`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_product_tab_lang`(
  `id_smart_product_tab` int(11) DEFAULT NULL,
  `id_lang` int(11) DEFAULT NULL,
  `title` varchar(256) DEFAULT NULL,
  `content` text
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

$sql[] = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'smart_product_tab_shop` (
  `id_smart_product_tab_shop`  int(11) NOT NULL auto_increment,
  `id_smart_product_tab`  int(11) NOT NULL,
  `id_shop` int(11) NOT NULL,
  KEY(`id_smart_product_tab_shop`),
  PRIMARY KEY (`id_smart_product_tab`,`id_shop`)
) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8' ;

?>