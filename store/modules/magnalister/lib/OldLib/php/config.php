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
 * $Id: config.php 4654 2014-09-29 11:12:15Z markus.bauer $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

/* Dynamisches Konfigurationsarray */
$magnaConfig = array();

/* DB Config */
function loadDBConfig($mpID = '0') {
	global $magnaConfig;
	if (!array_key_exists('db', $magnaConfig)) {
		$magnaConfig['db'] = array();
	}
	if (!array_key_exists($mpID, $magnaConfig['db'])) {
		$magnaConfig['db'][$mpID] = array();
	}
	if ( !defined('TABLE_MAGNA_CONFIG') || !MLDatabase::getDbInstance()->tableExists(TABLE_MAGNA_CONFIG)){
                return false;
            }

	$tmpConf = MLDatabase::getDbInstance()->fetchArray('
		SELECT * FROM '.TABLE_MAGNA_CONFIG.' 
		 WHERE `mpID`=\''.$mpID.'\'ORDER BY `mkey` ASC
	');
	if (empty($tmpConf)) return false;

	foreach ($tmpConf as $row) {
		$a = json_decode($row['value'], true);
		if (is_array($a)) {
			$magnaConfig['db'][$row['mpID']][$row['mkey']] = $a;
		} else {
			$magnaConfig['db'][$row['mpID']][$row['mkey']] = $row['value'];
		}
	}
	return $magnaConfig['db'][$row['mpID']];
}

function loadJSONConfig($lang = '') {
	global $magnaConfig;
	$path = DIR_MAGNALISTER.'config/'.$lang;

	if ($dirhandle = @opendir($path)) {
	    while (false !== ($filename = readdir($dirhandle))) {
	    	if (($filename == '.') || ($filename == '..')) continue;

	    	if (!preg_match('/^.*\.config\.json$/', $filename)) continue;

	    	$a = json_decode(file_get_contents($path.'/'.$filename), true);
	    	if (!is_array($a) || empty($a)) continue;

	    	$magnaConfig = array_merge_recursive($magnaConfig, $a);
	    }
	}
}

function loadMaranonCacheConfig($purge = false) {
	global $magnaConfig;        
	/* In case this is loaded from the magnaCallback and the magnalister server 
	   is in the coffee shop for a small break, RUN AWAY */
	if (defined('MAGNA_CALLBACK_MODE') && (CURRENT_CLIENT_VERSION == 0)) {
		return false;
	}

	$magnaConfig['maranon'] = array();
	if (!allRequiredConfigKeysAvailable(array('general.passphrase'), '0')) {
		$magnaConfig['maranon']['Marketplaces'] = array();
		return false;
	}

	$exceptionOccured = false;
	try {
//		$result = MagnaConnector::gi()->submitRequestCached(array(
//			'SUBSYSTEM' => 'Core',
//			'ACTION' => 'GetShopInfo',
//            'CALLBACKURL' => MLHttp::gi()->getFrontendDoUrl(array())
//		),30 * 60,$purge
//        );
        $result = MLShop::gi()->getShopInfo();
		#echo print_m($result, 'GetShopInfo');
		$magnaConfig['maranon'] = isset($result['DATA'])?$result['DATA']:null;
	} catch (MagnaException $e) {            
                            MLMessage::gi()->addError($e);
		$exceptionOccured = true;
		$magnaConfig['maranon']['Modules'] = array();
		$magnaConfig['maranon']['Marketplaces'] = array();
		$magnaConfig['maranon']['IsAccessAllowed'] = 'unsure';
		if ($e->getCode() == MagnaException::TIMEOUT) {
			MLDatabase::getDbInstance()->delete(TABLE_MAGNA_CONFIG, array (
				'mpID' => 0,
				'mkey' => 'CurrentClientVersion'
			));
		}
	}

	/* Part of soft deinstallation */
	if ($magnaConfig['maranon']['IsAccessAllowed'] != 'yes') {
		$interruptionCounter = (int)getDBConfigValue('InterruptionCounter', 0, 0) + 1;
		setDBConfigValue('InterruptionCounter', 0, $interruptionCounter, true);
		if ($interruptionCounter > 10) {
			setDBConfigValue('CallbackAccessInterrupted', 0, true, true);
		}
		#echo var_dump_pre((bool)getDBConfigValue('CallbackAccessInterrupted', 0));
	} else {
		setDBConfigValue('InterruptionCounter', 0, 0, true);
		setDBConfigValue('CallbackAccessInterrupted', 0, false, true);
	}
	
	if ($exceptionOccured) {
		return false; /* non-recoverable failure */
	}
	
	$mps = array();
	if (array_key_exists('Marketplaces', $magnaConfig['maranon']) && 
	    !empty($magnaConfig['maranon']['Marketplaces'])
	) {
		foreach ($magnaConfig['maranon']['Marketplaces'] as $mp) {
			$mps[$mp['ID']] = $mp['Marketplace'];
		}
		$magnaConfig['maranon']['Marketplaces'] = $mps;
	}

	/* Store Config */
	return (isset($magnaConfig['maranon']['IsAccessAllowed']) && $magnaConfig['maranon']['IsAccessAllowed'] == 'yes');
}

function allRequiredConfigKeysAvailable($keys, $mpID, $where = false, &$which = array()) {
	global $magnaConfig;
	if (!$where) {
		if (!array_key_exists($mpID, $magnaConfig['db'])) {
			return false;
		}
		$where = $magnaConfig['db'][$mpID];
	}
	if (empty($keys)) return true;
	foreach ($keys as $key) {
		if (!array_key_exists($key, $where)) {
			$which[] = $key;
		}
	}
	return empty($which);
}

function getDBConfigValue($key, $mpID, $default = null) {
	global $magnaConfig;
	if (is_array($key)) {
		$subItem = $key[1];
		$key = $key[0];
	}
	if (!(is_string($mpID) ^ is_int($mpID))) {
		$dbt = prepareErrorBacktrace(2);
		if (!empty($dbt)) {
			$who = array();
			$who['file'] = str_replace(DIR_FS_CATALOG, '', $dbt[0]['file']);
			$who['line'] = $dbt[0]['line'];
			if (array_key_exists(1, $dbt) && array_key_exists('class', $dbt[1])
				&& array_key_exists('function', $dbt[1]) && array_key_exists('type', $dbt[1])
			) {
				$who['call'] = $dbt[1]['class'].$dbt[1]['type'].$dbt[1]['function'];
			} else {
				$who['call'] = '--';
			}
			echo print_m($who);
		}
		unset($dbt);
		trigger_error("getDBConfigValue($key, $mpID, $default) :: \$mpID should be a numerical string", E_USER_ERROR);
	}
	if (!array_key_exists('db',  $magnaConfig)) {
		loadDBConfig();
	}
	if (!array_key_exists($mpID, $magnaConfig['db'])) {
		loadDBConfig($mpID);
	}
	if (   !array_key_exists($mpID, $magnaConfig['db'])
		|| !array_key_exists($key,  $magnaConfig['db'][$mpID])
	) {
		return $default;
	}
	if (isset($subItem)) {
		if (!is_array($magnaConfig['db'][$mpID][$key])
		    || !array_key_exists($subItem, $magnaConfig['db'][$mpID][$key])
		) {
			return $default;
		}
		return $magnaConfig['db'][$mpID][$key][$subItem];
	}
	
	return $magnaConfig['db'][$mpID][$key];
}

function removeDBConfigValue($key, $mpID) {
	global $magnaConfig;
	if (is_array($key)) {
		$subItem = $key[1];
		$key = $key[0];
	}
	if (!array_key_exists($mpID, $magnaConfig['db'])) {
		return true;
	}	
	if (!array_key_exists($key, $magnaConfig['db'][$mpID])) {
		return true;
	}
	if (isset($subItem)) {
		if (!is_array($magnaConfig['db'][$mpID][$key]) || !array_key_exists($subItem, $magnaConfig['db'][$mpID][$key])) {
			return true;
		}
		unset($magnaConfig['db'][$mpID][$key][$subItem]);
		return true;
	}
	unset($magnaConfig['db'][$mpID][$key]);
	return true;
}

function setDBConfigValue($key, $mpID, $value, $persistent = false) {
	global $magnaConfig;
	if (is_array($key)) {
		$subItem = $key[1];
		$key = $key[0];
	}
	if (!array_key_exists($mpID, $magnaConfig['db'])) {
		$magnaConfig['db'][$mpID] = array();
	}	
	if (!array_key_exists($key, $magnaConfig['db'][$mpID])) {
		$magnaConfig['db'][$mpID][$key] = null;
	}
	if (isset($subItem)) {
		if (!is_array($magnaConfig['db'][$mpID][$key])) {
			$magnaConfig['db'][$mpID][$key] = array(
				$subItem => $value
			);
		} else {
			$magnaConfig['db'][$mpID][$key][$subItem] = $value;
		}
	} else {
		$magnaConfig['db'][$mpID][$key] = $value;
	}
	if ($persistent) {
		$value = $magnaConfig['db'][$mpID][$key];
		if (is_array($value)) {
			foreach ($value as $k => &$v) {
				if (($v == 'true') || ($v == 'false')) {
					$value[$k] = ($v == 'true') ? true : false;
				}
			}
			$value = json_encode($value);
		}
		$data = array('mpID' => $mpID, 'mkey' => $key, 'value' => $value);
		if (MLDatabase::getDbInstance()->recordExists(TABLE_MAGNA_CONFIG, array(
				'mpID' => $mpID,
				'mkey' => $key,
		))) {
			MLDatabase::getDbInstance()->update(TABLE_MAGNA_CONFIG, $data, array(
				'mpID' => $mpID,
				'mkey' => $key
			));
		} else {
			MLDatabase::getDbInstance()->insert(TABLE_MAGNA_CONFIG, $data);
		}
	}
	return true;
}
