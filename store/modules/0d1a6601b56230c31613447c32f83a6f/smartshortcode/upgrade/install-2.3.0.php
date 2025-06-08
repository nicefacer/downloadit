<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_3_0($object)
{
	$sql = 'ALTER TABLE `'._DB_PREFIX_.'smart_contentanywhere` ';
	$sql .= ' ADD display_type varchar(150) DEFAULT NULL';
 	$sql .= ', ADD prd_page varchar(150) DEFAULT NULL';
 	$sql .= ', ADD prd_specify varchar(150) DEFAULT NULL';
 	$sql .= ', ADD cat_page varchar(150) DEFAULT NULL';
 	$sql .= ', ADD cat_specify varchar(150) DEFAULT NULL';
 	$sql .= ', ADD cms_page varchar(150) DEFAULT NULL';
 	$sql .= ', ADD cms_specify varchar(150) DEFAULT NULL';
 	$sql .= ', ADD blg_page varchar(150) DEFAULT NULL';
 	$sql .= ', ADD blg_specify varchar(150) DEFAULT NULL';
 	$sql .= ', ADD position varchar(150) DEFAULT NULL';
	Db::getInstance()->execute($sql);
	return true;
}







	




