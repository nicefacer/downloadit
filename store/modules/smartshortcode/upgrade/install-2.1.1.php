<?php

if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_2_1_1($object)
{
		$ssc = new SmartShortCode();
		$ssc->registerHook('sdsShortcodeAdminLists');
		$ssc->registerHook('sdsShortcodeAdminPages');
		$ssc->registerHook('sdsShortcodeFront');
		$ssc->moduleControllerRegistration();
		return true;
}
