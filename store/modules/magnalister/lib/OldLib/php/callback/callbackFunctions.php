<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id: callbackFunctions.php 5122 2015-02-04 15:15:53Z markus.bauer $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

//function magnaGetClientVersion($args) {
//	return array(
//		'ClientVersion' => LOCAL_CLIENT_VERSION,
//		'BuildVersion'  => CLIENT_BUILD_VERSION,
//	);
//}

function magnaGetInvolvedMarketplaces() {
	$_modules = MLSetting::gi()->get('aModules');        
	$fm = array();
	
	// backwards compat for the js thingy.
//	if (isset($_GET['mps']) && !isset($_GET['ml']['mps'])) {
//		$_GET['ml']['mps'] = $_GET['mps'];
//	}
	if (isset($_GET['ml']['mps']) && !empty($_GET['ml']['mps'])) {
		$mps = explode(',', $_GET['ml']['mps']);
		foreach ($mps as $m) {
			if (array_key_exists($m, $_modules) && ($_modules[$m]['type'] == 'marketplace')) {
				$fm[] = $m;
			}
		}
	}
	if (!empty($fm)) {
		return $fm;
	}
	foreach ($_modules as $m => $mp) {
		if ($mp['type'] == 'marketplace') {
			$fm[] = $m;
		}
	}
	return $fm;
}

function magnaGetInvolvedMPIDs($marketplace) {
	$mpIDs = magnaGetIDsByMarketplace($marketplace);
	if (empty($mpIDs)) {
		return array();
	}
	// backwards compat for the js thingy.
//	if (isset($_GET['mpid']) && !isset($_GET['ml']['mpid'])) {
//		$_GET['ml']['mpid'] = $_GET['mpid'];
//	}
	if (isset($_GET['ml']['mpid'])) {
		if (in_array($_GET['ml']['mpid'], $mpIDs)) {
			return array($_GET['ml']['mpid']);
		} else {
			return array();
		}
	}
	return $mpIDs;
}
