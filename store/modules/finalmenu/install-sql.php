<?php

if (!defined('_MYSQL_ENGINE_')) {
    define(_MYSQL_ENGINE_,'MyISAM');
}

Db::getInstance()->Execute("CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."desktop_menu_tabs` (
                            `id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `active` tinyint(1) unsigned DEFAULT '1',
                            `type` tinyint(1) unsigned DEFAULT '1',
                            `tab_position` varchar(50),
                            `link_window` tinyint(1) unsigned DEFAULT '0',
                            `position` int(10) unsigned,
                            `tab_icon` varchar(50),
                            `tab_image` varchar(250),
                            `tab_note_bg_color` varchar(50) DEFAULT '#777777',
                            `links_color` varchar(50) DEFAULT '#777777',
                            `other_text_color` varchar(50) DEFAULT '#777777',
                            `links_hover_color` varchar(50) DEFAULT '#7caa3d',
                            `settings` varchar(20000) NOT NULL,
                            PRIMARY KEY (`id_tab`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."desktop_menu_tabs_lang` (
                            `id_tab` int(10) unsigned NOT NULL,
                            `id_lang` int(10) unsigned NOT NULL,
                            `name` varchar(255) NOT NULL,
                            `tab_note` varchar(50),
                            `tab_link` varchar(255),
                            PRIMARY KEY (`id_tab`,`id_lang`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."desktop_menu_tabs_shop` (
                            `id_tab` int(11) unsigned NOT NULL,
                            `id_shop` int(11) unsigned NOT NULL,
                            PRIMARY KEY (`id_tab`,`id_shop`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."vertical_menu_tabs` (
                            `id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `active` tinyint(1) unsigned DEFAULT '1',
                            `type` tinyint(1) unsigned DEFAULT '1',
                            `tab_position` varchar(50),
                            `link_window` tinyint(1) unsigned DEFAULT '0',
                            `position` int(10) unsigned,
                            `tab_icon` varchar(50),
                            `tab_image` varchar(250),
                            `tab_note_bg_color` varchar(50) DEFAULT '#777777',
                            `links_color` varchar(50) DEFAULT '#777777',
                            `other_text_color` varchar(50) DEFAULT '#777777',
                            `links_hover_color` varchar(50) DEFAULT '#7caa3d',
                            `settings` varchar(20000) NOT NULL,
                            PRIMARY KEY (`id_tab`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."vertical_menu_tabs_lang` (
                            `id_tab` int(10) unsigned NOT NULL,
                            `id_lang` int(10) unsigned NOT NULL,
                            `name` varchar(255) NOT NULL,
                            `tab_note` varchar(50),
                            `tab_link` varchar(255),
                            PRIMARY KEY (`id_tab`,`id_lang`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."vertical_menu_tabs_shop` (
                            `id_tab` int(11) unsigned NOT NULL,
                            `id_shop` int(11) unsigned NOT NULL,
                            PRIMARY KEY (`id_tab`,`id_shop`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mobile_menu_tabs` (
                            `id_tab` int(10) unsigned NOT NULL AUTO_INCREMENT,
                            `name` varchar(255) NOT NULL,
                            `product_ID` int(10) unsigned,
                            `position` int(10) unsigned,
                            `link_new_window` tinyint(1) unsigned DEFAULT '0',
                            `active` tinyint(1) unsigned DEFAULT '1',
                            PRIMARY KEY (`id_tab`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mobile_menu_tabs_shop` (
                            `id_tab` int(10) unsigned NOT NULL,
                            `id_shop` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id_tab`,`id_shop`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;
                        CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."mobile_menu_tabs_lang` (
                            `id_tab` int(10) unsigned NOT NULL,
                            `link_title` varchar(255) DEFAULT '',
                            `link_url` varchar(255) DEFAULT '',
                            `id_lang` int(10) unsigned NOT NULL,
                            PRIMARY KEY (`id_tab`,`id_lang`)
                          ) ENGINE="._MYSQL_ENGINE_." DEFAULT CHARSET=utf8;");
