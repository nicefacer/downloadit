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
 * $Id: amazonFunctions.php 5299 2015-03-03 18:03:48Z masoud.khodaparast $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

require_once(DIR_MAGNALISTER_MODULES.'generic/genericFunctions.php');

function magnaAmazonSKU2pID($sku, $asin = '') {
	$iPID = magnaSKU2pID($sku);
	if (($iPID <= 0) && !empty($asin)) {
        /* @var $oProperty ML_Database_Model_Table_Abstract*/
        $iPID = MLDatabase::factory('amazon_prepare')->getByIdentifier($asin,"asin");
//		global $_MagnaSession;
//		if (getDBConfigValue('general.keytype', '0') == 'artNr') {
//			$query = '
//				SELECT p.products_id 
//				  FROM '.TABLE_PRODUCTS.' p, '.TABLE_MAGNA_AMAZON_PROPERTIES.' pa
//				 WHERE p.products_model = pa.products_model
//				       AND pa.asin=\''.$asin.'\' AND mpID=\''.$_MagnaSession['mpID'].'\'
//				 LIMIT 1
//			';
//		} else {
//			$query = '
//				SELECT products_id 
//				  FROM '.TABLE_MAGNA_AMAZON_PROPERTIES.' 
//				 WHERE asin=\''.$asin.'\' AND mpID=\''.$_MagnaSession['mpID'].'\'
//				 LIMIT 1';
//		}
//		$pID = (int)MLDatabase::getDbInstance()->fetchOne($query);
//		unset($query);
	}
	return $iPID;
}

//function performItemSearch($asin, $ean, $productsName) {
////	require_once (DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
//    $sCacheId= __FUNCTION__.'_'.md5(json_encode(array($asin, $ean, $productsName)));
//    try{
//        $searchResults=  MLCache::gi()->get($sCacheId);
//    }catch(ML_Filesystem_Exception $oEx){
//        $searchResults = array();
//        if (!empty($asin)) {
//            try {
//                $result = MagnaConnector::gi()->submitRequest(array(
//                    'ACTION' => 'ItemLookup',
//                    'ASIN' => $asin
//                ));
//                if (!empty($result['DATA'])) {
//                    $searchResults = array_merge($searchResults, $result['DATA']);
//                }
//            } catch (MagnaException $e) {
//                $e->setCriticalStatus(false);
//            }
//        }
//        $ean = str_replace(array(' ', '-'), '', $ean);
//        if (!empty($ean)) {
//            try {
//                $result = MagnaConnector::gi()->submitRequest(array(
//                    'ACTION' => 'ItemLookup',
//                    'ASIN' => $ean
//                ));
//                if (!empty($result['DATA'])) {
//                    $searchResults = array_merge($searchResults, $result['DATA']);
//                }
//            } catch (MagnaException $e) {
//                $e->setCriticalStatus(false);
//            }
//            try {
//                $result = MagnaConnector::gi()->submitRequest(array(
//                    'ACTION' => 'ItemSearch',
//                    'NAME' => $ean
//                ));
//                if (!empty($result['DATA'])) {
//                    $searchResults = array_merge($searchResults, $result['DATA']);
//                }
//            } catch (MagnaException $e) {
//                $e->setCriticalStatus(false);
//            }
//        }
//
//        if (!empty($productsName)) {
//            try {	
//                $result = MagnaConnector::gi()->submitRequest(array(
//                    'ACTION' => 'ItemSearch',
//                    'NAME' => $productsName
//                ));
//                if (!empty($result['DATA'])) {
//                    $searchResults = array_merge($searchResults, $result['DATA']);
//                }
//            } catch (MagnaException $e) {
//                $e->setCriticalStatus(false);
//            }
//        }
//        if (!empty($searchResults)) {
//            $searchResults = array_map('unserialize', array_unique(array_map('serialize', $searchResults)));
//            foreach ($searchResults as &$data) {
//                if (!empty($data['Author'])) {
//                    $data['Title'] .= ' ('.$data['Author'].')';
//                }
//    //	    	$price = new SimplePrice($data['LowestPrice']['Price'], $data['LowestPrice']['CurrencyCode']);
//                $data['LowestPriceFormated'] = MLPrice::factory()->format($data['LowestPrice']['Price'],$data['LowestPrice']['CurrencyCode']);//$price->format();
//                $data['LowestPrice'] = $data['LowestPrice']['Price'];
//            }
//        }
//        MLCache::gi()->set($sCacheId,$searchResults,60*60*2);
//    }
//	return $searchResults;
//}

function amazonGetPossibleOptions($kind, $mpID = false) {
	if ($mpID === false) {
		global $_MagnaSession;
		$mpID = $_MagnaSession['mpID'];
	}
	
	initArrayIfNecessary($_MagnaSession, array($mpID, $kind));
	
	if (empty($_MagnaSession[$mpID][$kind])) {
		try {
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'Get'.$kind,
				'SUBSYSTEM' => 'Amazon',
				'MARKETPLACEID' => $mpID,
			));
			$_MagnaSession[$mpID][$kind] = $result['DATA'];
		} catch (MagnaException $e) { }
	}
	return $_MagnaSession[$mpID][$kind];
}

function amazonGetMarketplaces() {
	global $_MagnaSession;
	
	initArrayIfNecessary($_MagnaSession, array($_MagnaSession['mpID'], 'Marketplaces', 'Sites'));
	initArrayIfNecessary($_MagnaSession, array($_MagnaSession['mpID'], 'Marketplaces', 'Currencies'));

	if (empty($_MagnaSession[$_MagnaSession['mpID']]['Marketplaces']['Sites']) || 
		empty($_MagnaSession[$_MagnaSession['mpID']]['Marketplaces']['Currencies'])
	) {
		try {
			$_MagnaSession[$_MagnaSession['mpID']]['Marketplaces'] = array();
			
			$result = MagnaConnector::gi()->submitRequest(array(
				'ACTION' => 'GetMarketplaces',
				'SUBSYSTEM' => 'Amazon',
			));
			foreach ($result['DATA'] as $item) {
				$_MagnaSession[$_MagnaSession['mpID']]['Marketplaces']['Sites'][$item['Key']] = fixHTMLUTF8Entities($item['Label']);
				$_MagnaSession[$_MagnaSession['mpID']]['Marketplaces']['Currencies'][$item['Key']] = $item['Currency'];
			}
		} catch (MagnaException $e) { }
	}

	return $_MagnaSession[$_MagnaSession['mpID']]['Marketplaces'];
}

function amazonGetLeadtimeToShip($mpID, $pID) {
    $iTime=MLDatabase::factory('amazon_prepare')->set('mpid', $mpID)->set('productsid', $pID)->get('leadtimetoship');
    if(empty($iTime)){
        $iTime = MLModul::gi()->getConfig('leadtimetoship');
    }
    return $iTime;
}

function updateAmazonInventoryByEdit($mpID, $updateData) {
	$updateItem = genericInventoryUpdateByEdit($mpID, $updateData);
	if (!is_array($updateItem)) {
		return;
	}
	$timeToShip = getDBConfigValue('amazon.leadtimetoship', $mpID, '');
	if (!empty($timeToShip)) {
		$updateItem['LeadtimeToShip'] = (int)$timeToShip;
	}
	#echo print_m($updateItem, '$updateItem');
	magnaUpdateItems($mpID, array($updateItem), true);
}

function updateAmazonInventoryByOrder($mpID, $boughtItems, $subRelQuant = true) {
	if (getDBConfigValue('amazon.stocksync.tomarketplace', $mpID, 'no') == 'no') {
		return;
	}
	$data = genericInventoryUpdateByOrder($mpID, $boughtItems, $subRelQuant);
	$timeToShip = getDBConfigValue('amazon.leadtimetoship', $mpID, '');
	if (!empty($timeToShip)) {
		foreach ($data as &$item) {
			$item['LeadtimeToShip'] = (int)$timeToShip;
		}
	}
	#echo print_m($data, '$data');
	magnaUpdateItems($mpID, $data, true);
}

function loadCarrierCodes($mpID = false) {
    $aPost= MLRequest::gi()->data();
	if ($mpID === false) {
		global $_MagnaSession;
		$mpID = $_MagnaSession['mpID'];
	}
	$carrier = amazonGetPossibleOptions('CarrierCodes', $mpID);

	# Amazon Config Form
	if (array_key_exists('conf', $aPost) && array_key_exists('amazon.orderstatus.carrier.additional', $aPost['conf'])) {
		setDBConfigValue(
			'amazon.orderstatus.carrier.additional',
			$mpID,
			$aPost['conf']['amazon.orderstatus.carrier.additional']
		);
	}

	$addCarrier = explode(',', getDBConfigValue('amazon.orderstatus.carrier.additional', $mpID, ''));
	if (!empty($addCarrier)) {
		foreach ($addCarrier as $val) {
			$val = trim($val);
			if (empty($val)) continue;
			$carrier[$val] = $val;
		}
	}
	$carrierValues = array('null' => MLI18n::gi()->ML_LABEL_CARRIER_NONE);
	if (!empty($carrier)) {
		foreach ($carrier as $val) {
			if ($val == 'Other') continue;
			$carrierValues[$val] = $val;
		}
	}
	return $carrierValues;
}

//function amazonDoOrderStatusSyncByTigger($mpID) {
//	$mp = 'amazon';
//	return getDBConfigValue($mp.'.orderstatus.sync', $mpID, 'no') != 'no';
//}

function amazonRenderOrderStatusSync($args) {
	$html = '';
	$order = $args['order'];
	if (array_key_exists('ML_LABEL_SHIPPING_DATE', $order['data'])) {
		return '';
	}
	if (isset($order['internaldata']['Request'])) {
		return '';
	}
	$carrierCodes = loadCarrierCodes($order['mpID']);
	$defaultcarrier = getDBConfigValue(
		'amazon.orderstatus.carrier.default',
		$order['mpID']
	);

	$replace = array (
		'{#TRACKING_CODE#}' => magnaAmazonFetchTrackingCode3rdParty($order['orders_id'], $order['mpID']),
		'{#CARRIERS_OPTIONS#}' => '',
	);
	foreach ($carrierCodes as $key => $val) {
		$replace['{#CARRIERS_OPTIONS#}'] .= '
			<option '.(($key == $defaultcarrier) ? 'selected="selected"' : '').' value="'.$key.'">'.$val.'</option>';
	}
	
	$htmlTmpl = '
		<tr id="amazonSending"><td class="main" colspan="2" style="padding-left: 0;">
			<table><tbody>
				<tr><td class="main"><b>'.MLI18n::gi()->ML_LABEL_TRACKINGCODE.':</b></td>
					<td><input type="text" name="magna[trackingcode]" value="{#TRACKING_CODE#}"/></td></tr>
				<tr><td class="main"><b>'.MLI18n::gi()->ML_LABEL_CARRIER.':</b></td>
					<td><select name="magna[carriercode]">{#CARRIERS_OPTIONS#}</select></td></tr>
			</tbody></table>
		</td></tr>';

	if (SHOPSYSTEM == 'gambio') {
		if ($args['view'] == 'orderDetailMulti') {
			global $content_multi_order_status;
			$content_multi_order_status[] = array (
				'text' => MLI18n::gi()->ML_LABEL_TRACKINGCODE.': <input type="text" name="magna[trackingcode]" value="'.$replace['{#TRACKING_CODE#}'].'"/>',
			);
			$content_multi_order_status[] = array (
				'text' => MLI18n::gi()->ML_LABEL_CARRIER.': <select name="magna[carriercode]">'.$replace['{#CARRIERS_OPTIONS#}'].'</select>',
			);
			return;
		} else {
			$htmlTmpl = '
				<tr><td class="main">'.MLI18n::gi()->ML_LABEL_TRACKINGCODE.':</td>
					<td class="main"><input type="text" name="magna[trackingcode]" value="{#TRACKING_CODE#}"/></td></tr>
				<tr><td class="main">'.MLI18n::gi()->ML_LABEL_CARRIER.':</td>
					<td><select name="magna[carriercode]">{#CARRIERS_OPTIONS#}</select></td></tr>';
		}
	}

	$html = str_replace(array_keys($replace), array_values($replace), $htmlTmpl);
	return $html;
}
//
//function amazonProcessSingleOrderStatus($args) {
//	$order = $args['order'];
//	$mp = $order['platform'];
//	$mpID = $order['mpID'];
//	$aPost=  MLRequest::gi()->data();
//	$cancelledState = getDBConfigValue($mp.'.orderstatus.cancelled', $mpID, false);
//	$shippedState = getDBConfigValue($mp.'.orderstatus.shipped', $mpID, false);
//
//	$newState = $args['status'];
//	if ($newState == 'cancel') {
//		$newState = $cancelledState;
//	} else if ($newState == 'order') {
//		$newState = $shippedState;
//		$aPost['magna']['trackingcode'] = magnaAmazonFetchTrackingCode3rdParty($order['orders_id'], $order['mpID']);
//		$aPost['magna']['carriercode']  = magnaAmazonFetchCarrier3rdParty($order['orders_id'], $order['mpID']);
//		if (empty($aPost['magna']['carriercode'])) {
//			$aPost['magna']['carriercode']  = getDBConfigValue('amazon.orderstatus.carrier.default', $order['mpID']);
//		}
//	}
//	
//	$oStatAutoSync = getDBConfigValue($mp.'.orderstatus.sync', $mpID, 'auto') == 'auto';
//	
//	$request = false;
//	if ($newState == $shippedState) {
//		$trackercode = (
//			array_key_exists('magna', $aPost) 
//			&& array_key_exists('trackingcode', $aPost['magna']) 
//			&& !eempty(trim($aPost['magna']['trackingcode']))
//				? trim($aPost['magna']['trackingcode'])
//				: ''
//		);
//		$carrier = (
//			array_key_exists('magna', $aPost) 
//			&& array_key_exists('carriercode', $aPost['magna']) 
//			&& !eempty(trim($aPost['magna']['carriercode']))
//			&& !($aPost['magna']['carriercode'] == 'null')
//				? trim($aPost['magna']['carriercode'])
//				: ''
//		);
//		$request = array (
//			'ACTION' => 'ConfirmShipment',
//			'SUBSYSTEM' => 'Amazon',
//			'MARKETPLACEID' => $order['mpID'],
//			'DATA' => array (
//				array (
//					'AmazonOrderID' => $order['data']['AmazonOrderID'],
//					'ShippingDate' => gmdate('Y-m-d'),
//					'Carrier' => $carrier,
//					'TrackingCode' => $trackercode
//				)
//			)
//		);
//	} else if ($newState == $cancelledState) {
//		$request = array (
//			'ACTION' => 'CancelShipment',
//			'SUBSYSTEM' => 'Amazon',
//			'MARKETPLACEID' => $order['mpID'],
//			'DATA' => array (
//				array (
//					'AmazonOrderID' => $order['data']['AmazonOrderID'],
//				)
//			)
//		);
//	}
//	/*/
//	else {
//		echo 'Do nothing';
//		echo var_dump_pre($cancelledState, '$cancelledState');
//		echo var_dump_pre($shippedState, 'shippedState');
//	}
//	//*/
//
//	if ($request === false) return '';
//	#echo print_m($request);
//
//	if ($oStatAutoSync) {
//		if ($newState == $shippedState) {
//			unset($request['DATA'][0]['AmazonOrderID']);
//			$order['internaldata']['Request'] = array (
//				'Action' => $request['ACTION'],
//				'Data' => $request['DATA'][0],
//			);
//		} else {
//			$order['internaldata']['Request'] = array (
//				'Action' => $request['ACTION']
//			);
//		}
//		if (isset($trackercode)) {
//			magnaAmazonSaveTrackingCode3rdParty($order['orders_id'], $trackercode, $mpID);
//		}
//		if (isset($carrier)) {
//			magnaAmazonSaveCarrier3rdParty($order['orders_id'], $carrier, $mpID);
//		}
//		magnaSaveOrder($order);
//		return '';
//	}
//
//	try {
//		$result = MagnaConnector::gi()->submitRequest($request);
//		# $result['BatchIDs'] = array('1234768');
//	} catch (MagnaException $e) {
//		$result = array();
//	}
//
//	if (!isset($result['DATA'][0])) {
//		return '';
//	}
//
//	foreach ($result['DATA'] as $cData) {
//		if ($order['data']['AmazonOrderID'] != $cData['AmazonOrderID']) continue;
//		if ($newState == $shippedState) {
//			$order['data']['ML_LABEL_SHIPPING_DATE'] = date('Y-m-d H:i:s');
//			$order['data']['ML_LABEL_TRACKINGCODE'] = $trackercode;
//			$order['data']['ML_LABEL_CARRIER'] = $carrier;
//			magnaAmazonSaveTrackingCode3rdParty($order['orders_id'], $trackercode, $order['mpID']);
//			magnaAmazonSaveCarrier3rdParty($order['orders_id'], $carrier, $order['mpID']);
//		} else if ($newState == $cancelledState) {
//			$order['data']['ML_LABEL_ORDER_CANCELLED'] = date('Y-m-d H:i:s');
//		}
//		$order['data']['ML_AMAZON_LABEL_BATCHID'] = $cData['BatchID'];
//		$order['orders_status'] = $newState;
//		magnaSaveOrder($order);
//	}
//}

function amazonProcessMultiOrderStatus($args) {
/*
	echo var_export_pre($args, '$args');
	echo var_export_pre($_GET, '$_GET');
	echo var_export_pre($_POST, '$_POST');
*/
        $aPost=  MLRequest::gi()->data();
	$args['orders'] = array_values($args['orders']);
	$mpID = $args['orders'][0]['mpID'];
	$mp = $args['orders'][0]['platform'];

	$cancelledState = getDBConfigValue($mp.'.orderstatus.cancelled', $mpID, false);
	$shippedState = getDBConfigValue($mp.'.orderstatus.shipped', $mpID, false);
/*
	echo var_dump_pre($cancelledState, '$cancelledState');
	echo var_dump_pre($shippedState, '$shippedState');
	echo var_dump_pre($args['status'], '$args[status]');
*/
#	$args['status'] = '99';

	$oStatAutoSync = getDBConfigValue($mp.'.orderstatus.sync', $mpID, 'auto') == 'auto';

	$request = false;
	$preparedOrders = array();
	if ($args['status'] == $shippedState) {
		$trackercode = (
			array_key_exists('magna', $aPost) 
			&& array_key_exists('trackingcode', $aPost['magna']) 
			&& !eempty(trim($aPost['magna']['trackingcode']))
				? trim($aPost['magna']['trackingcode'])
				: ''
		);
		$carrier = (
			array_key_exists('magna', $aPost) 
			&& array_key_exists('carriercode', $aPost['magna']) 
			&& !eempty(trim($aPost['magna']['carriercode']))
			&& !($aPost['magna']['carriercode'] == 'null')
				? trim($aPost['magna']['carriercode'])
				: ''
		);
		$request = array (
			'ACTION' => 'ConfirmShipment',
			'SUBSYSTEM' => 'Amazon',
			'MARKETPLACEID' => $mpID,
			'DATA' => array (),
		);
		foreach ($args['orders'] as $o) {
			$r = array (
				'ShippingDate' => gmdate('Y-m-d'),
				'Carrier' => $carrier,
				'TrackingCode' => $trackercode
			);
			if ($oStatAutoSync) {
				$o['internaldata']['Request'] = array (
					'Action' => $request['ACTION'],
					'Data' => $r
				);
				magnaSaveOrder($o);
				magnaAmazonSaveTrackingCode3rdParty($o['orders_id'], $trackercode, $mpID);
				magnaAmazonSaveCarrier3rdParty($o['orders_id'], $carrier, $mpID);
			} else {
				$r['AmazonOrderID'] = $o['data']['AmazonOrderID'];
				$request['DATA'][] = $r;
				$preparedOrders[$o['data']['AmazonOrderID']] = &$o;
			}
		}

	} else if ($args['status'] == $cancelledState) {
		$request = array (
			'ACTION' => 'CancelShipment',
			'SUBSYSTEM' => 'Amazon',
			'MARKETPLACEID' => $mpID,
			'DATA' => array (),
		);
		foreach ($args['orders'] as $o) {
			if ($oStatAutoSync) {
				$o['internaldata']['Request'] = array (
					'Action' => $request['ACTION']
				);
				magnaSaveOrder($o);
			} else {
				$request['DATA'][] = array (
					'AmazonOrderID' => $o['data']['AmazonOrderID'],
				);
				$preparedOrders[$o['data']['AmazonOrderID']] = &$o;	
			}
		}
	}

	if ($request === false) return '';
	#echo print_m($request);

	if ($oStatAutoSync) {
		return '';
	}

	/*
	$result = array('BatchIDs' => '');
	/*/
	try {
		$result = MagnaConnector::gi()->submitRequest($request);
		# $result['BatchIDs'] = array('1234768');
	} catch (MagnaException $e) {
		$result = array();
	}
	//*/

	if (!isset($result['DATA'][0])) {
		return '';
	}

	foreach ($result['DATA'] as $cData) {
		if (!isset($preparedOrders[$cData['AmazonOrderID']])) continue;
		$o = &$preparedOrders[$cData['AmazonOrderID']];
		if ($args['status'] == $shippedState) {
			$o['data']['ML_LABEL_SHIPPING_DATE'] = date('Y-m-d H:i:s');
			$o['data']['ML_LABEL_TRACKINGCODE'] = $trackercode;
			$o['data']['ML_LABEL_CARRIER'] = $carrier;
			magnaAmazonSaveTrackingCode3rdParty($o['orders_id'], $trackercode, $o['mpID']);
			magnaAmazonSaveCarrier3rdParty($o['orders_id'], $carrier, $o['mpID']);
		} else if ($args['status'] == $cancelledState) {
			$o['data']['ML_LABEL_ORDER_CANCELLED'] = date('Y-m-d H:i:s');
		}
		$o['data']['ML_AMAZON_LABEL_BATCHID'] = $cData['BatchID'];
		$o['orders_status'] = $args['status'];
		#echo print_m($o, '$o');
		magnaSaveOrder($o);
	}
	#die();
}

function magnaAmazonFetchTrackingCode3rdParty($oID, $mpID) {
	$table = getDBConfigValue('amazon.orderstatus.carrier.trackingcode.table', $mpID, false);
	if (($table === false) || empty($table['column']) || empty($table['table'])) return '';
	$cIDAlias = getDBConfigValue('amazon.orderstatus.carrier.trackingcode.alias', $mpID);
	if (empty($cIDAlias)) {
		$cIDAlias = 'orders_id';
	}
	return (string)MLDatabase::getDbInstance()->fetchOne('
		SELECT `'.$table['column'].'` 
		  FROM `'.$table['table'].'` 
		 WHERE `'.$cIDAlias.'`=\''.MLDatabase::getDbInstance()->escape($oID).'\'
	');
}

function magnaAmazonSaveTrackingCode3rdParty($oID, $tc, $mpID) {
	$table = getDBConfigValue('amazon.orderstatus.carrier.trackingcode.table', $mpID, false);
	if ($table === false || empty($table['column']) || empty($table['table'])) return;
	$cIDAlias = getDBConfigValue('amazon.orderstatus.carrier.trackingcode.alias', $mpID);
	if (empty($cIDAlias)) {
		$cIDAlias = 'orders_id';
	}
	MLDatabase::getDbInstance()->update($table['table'], array (
		$table['column'] => trim($tc),
	), array (
		$cIDAlias => $oID,
	));
}

function magnaAmazonFetchCarrier3rdParty($oID, $mpID) {
	$table = getDBConfigValue('amazon.orderstatus.carrier.carrier.table', $mpID, false);
	if (($table === false) || empty($table['column']) || empty($table['table'])) return '';
	$cIDAlias = getDBConfigValue('amazon.orderstatus.carrier.carrierDBMatching.alias', $mpID);
	if (empty($cIDAlias)) {
		$cIDAlias = 'orders_id';
	}
	return (string)MLDatabase::getDbInstance()->fetchOne('
		SELECT `'.$table['column'].'` 
		  FROM `'.$table['table'].'` 
		 WHERE `'.$cIDAlias.'`=\''.MLDatabase::getDbInstance()->escape($oID).'\'
	');
}

function magnaAmazonSaveCarrier3rdParty($oID, $carrier, $mpID) {
	$table = getDBConfigValue('amazon.orderstatus.carrier.carrier.table', $mpID, false);
	if ($table === false || empty($table['column']) || empty($table['table'])) return;
	$cIDAlias = getDBConfigValue('amazon.orderstatus.carrier.carrierDBMatching.alias', $mpID);
	if (empty($cIDAlias)) {
		$cIDAlias = 'orders_id';
	}
	MLDatabase::getDbInstance()->update($table['table'], array (
		$table['column'] => trim($carrier),
	), array (
		$cIDAlias => $oID,
	));
}
//
//function autoupdateAmazonOrdersStatus($mpID) {
//	$mp = 'amazon';
//	if (getDBConfigValue($mp.'.orderstatus.sync', $mpID, 'no') != 'auto') {
//		return false;
//	}
//	$orders = MLDatabase::getDbInstance()->fetchArray(eecho('
//	    SELECT mo.orders_id, mo.orders_status, mo.data, mo.internaldata, 
//	           o.orders_status AS orders_status_shop
//	      FROM `'.TABLE_MAGNA_ORDERS.'` mo, `'.TABLE_ORDERS.'` o
//	     WHERE mo.orders_id=o.orders_id
//	           AND mo.mpID=\''.$mpID.'\'
//	           AND mo.orders_status<>o.orders_status
//	', function_exists('ml_debug_out')));
//	if (function_exists('ml_debug_out')) ml_debug_out(print_m($orders, '$orders'));
//	if (empty($orders)) return true;
//
//	$mp = 'amazon';
//	$cancelledState = getDBConfigValue($mp.'.orderstatus.cancelled', $mpID, false);
//	$shippedState = getDBConfigValue($mp.'.orderstatus.shipped', $mpID, false);
//	
//	$carrierDefault = getDBConfigValue($mp.'.orderstatus.carrier.default', $mpID, '');
//
//	$confirmations = array();
//	$cancellations = array();
//	$unprocessed = array();
//
//	$preparedOrders = array();
//
//	foreach ($orders as $key => &$order) {
//		$order['data'] = @unserialize($order['data']);
//		if (!is_array($order['data'])) {
//			$order['data'] = array();
//		}
//		$order['internaldata'] = @unserialize($order['internaldata']);
//		if (!is_array($order['internaldata'])) {
//			$order['internaldata'] = array();
//		}
//		
//		$status = $order['orders_status_shop'];
//		unset($order['orders_status_shop']);
//
//		if (($status != $shippedState) && ($status != $cancelledState)) {
//			$unprocessed[] = $order['orders_id'];
//			unset($orders[$key]);
//			$order['orders_status'] = $status;
//			continue;
//		}
//
//		$date = MLDatabase::getDbInstance()->fetchOne('
//		    SELECT date_added FROM `'.TABLE_ORDERS_STATUS_HISTORY.'`
//		     WHERE orders_id='.$order['orders_id'].'
//		           AND orders_status_id='.$status.'
//		  ORDER BY date_added DESC
//		     LIMIT 1
//		');
//
//		if ($date === false) {
//			$date = date('Y-m-d');
//		} else {
//			$date = date('Y-m-d', strtotime($date));
//		}
//
//		if ($status == $shippedState) {
//			$trackercode = magnaAmazonFetchTrackingCode3rdParty($order['orders_id'], $mpID);
//			$carrier = magnaAmazonFetchCarrier3rdParty($order['orders_id'], $mpID);
//			if (empty($carrier)) {
//				$carrier = $carrierDefault;
//			}
//			if (isset($order['internaldata']['Request']['Data'])) {
//				$cfirm = $order['internaldata']['Request']['Data'];
//				$cfirm['AmazonOrderID'] = $order['data']['AmazonOrderID'];
//				unset($order['internaldata']['Request']);
//			} else {
//				$cfirm = array (
//					'AmazonOrderID' => $order['data']['AmazonOrderID'],
//					'ShippingDate' => $date,
//					'Carrier' => $carrier,
//					'TrackingCode' => $trackercode
//				);
//			}
//			$confirmations[] = $cfirm;
//			$order['data']['ML_LABEL_SHIPPING_DATE'] = $cfirm['ShippingDate'];
//			if (!empty($cfirm['TrackingCode'])) {
//				$order['data']['ML_LABEL_TRACKINGCODE'] = $cfirm['TrackingCode'];
//			}
//			$order['data']['ML_LABEL_CARRIER'] = $carrier;
//		} else if ($status == $cancelledState) {
//			if (isset($order['internaldata']['Request'])) {
//				unset($order['internaldata']['Request']);
//			}
//			$cancellations[] = array (
//				'AmazonOrderID' => $order['data']['AmazonOrderID'],
//			);
//			$order['data']['ML_LABEL_ORDER_CANCELLED'] = $date;
//		}
//		$order['orders_tmp_status'] = $status;
//		//$order['internaldata'] = serialize($order['internaldata']);
//		if (isset($preparedOrders[$order['data']['AmazonOrderID']])) {
//			/* This is a lie, but meh... the result will be correct. */
//			$unprocessed[] = $preparedOrders[$order['data']['AmazonOrderID']]['orders_id'];
//		}
//		$preparedOrders[$order['data']['AmazonOrderID']] = &$order;
//	}
//	$confirmedOrders = array();
//	$cancelledOrders = array();
//
//	$successfullySubmittedOrders = array();
//	if (!empty($confirmations)) {
//		$request = array (
//			'ACTION' => 'ConfirmShipment',
//			'SUBSYSTEM' => 'Amazon',
//			'MARKETPLACEID' => $mpID,
//			'DATA' => $confirmations,
//		);
//		if (defined('MAGNA_ECHO_UPDATE') && MAGNA_ECHO_UPDATE) {
//			ml_debug_out(print_m($request, 'confirmations'));
//		} else {
//			//echo var_export_pre($request, '$requestConfirm');
//			try {
//				$result = MagnaConnector::gi()->submitRequest($request);
//			} catch (MagnaException $e) {
//				$result = array();
//			}
//			//echo var_export_pre($result, '$resultConfirm');
//			if (isset($result['DATA'][0])) {
//				foreach ($result['DATA'] as $cData) {
//					if (!isset($preparedOrders[$cData['AmazonOrderID']])) continue;
//					$tO = &$preparedOrders[$cData['AmazonOrderID']];
//					if (!isset($tO['orders_tmp_status'])) {
//						$unprocessed[] = $tO['orders_id'];
//						continue;
//					}
//					$tO['orders_status'] = $tO['orders_tmp_status'];
//					unset($tO['orders_tmp_status']);
//					$tO['data']['ML_AMAZON_LABEL_BATCHID'] = $cData['BatchID'];
//					$successfullySubmittedOrders[$cData['AmazonOrderID']] = &$tO;
//				}
//			}
//		}
//	}
//
//	if (!empty($cancellations)) {
//		$request = array (
//			'ACTION' => 'CancelShipment',
//			'SUBSYSTEM' => 'Amazon',
//			'MARKETPLACEID' => $mpID,
//			'DATA' => $cancellations,
//		);
//		if (defined('MAGNA_ECHO_UPDATE') && MAGNA_ECHO_UPDATE) {
//			ml_debug_out(print_m($request, 'cancellations'));
//		} else {
//			//echo var_export_pre($request, '$requestCancel');
//			try {
//				$result = MagnaConnector::gi()->submitRequest($request);
//			} catch (MagnaException $e) {
//				$result = array();
//			}
//			//echo var_export_pre($result, '$resultCancel');
//			if (isset($result['DATA'][0])) {
//				foreach ($result['DATA'] as $cData) {
//					if (!isset($preparedOrders[$cData['AmazonOrderID']])) continue;
//					$tO = &$preparedOrders[$cData['AmazonOrderID']];
//					if (!isset($tO['orders_tmp_status'])) {
//						$unprocessed[] = $tO['orders_id'];
//						continue;
//					}
//					$tO['orders_status'] = $tO['orders_tmp_status'];
//					unset($tO['orders_tmp_status']);
//					$tO['data']['ML_AMAZON_LABEL_BATCHID'] = $cData['BatchID'];
//					$successfullySubmittedOrders[$cData['AmazonOrderID']] = &$tO;
//				}
//			}
//		}
//	}
//	
//	if (!empty($unprocessed)) {
//		MLDatabase::getDbInstance()->query("
//			UPDATE `".TABLE_MAGNA_ORDERS."` mo, `".TABLE_ORDERS."` o 
//			   SET mo.orders_status=o.orders_status
//		     WHERE mo.orders_id=o.orders_id
//		           AND mo.orders_id IN ('".implode("', '", $unprocessed)."')
//		");
//		if (function_exists('ml_debug_out')) ml_debug_out(print_m($unprocessed, '$unprocessed'));
//	}
//	if (empty($successfullySubmittedOrders)) return true;
//	foreach ($successfullySubmittedOrders as $o) {
//		#echo print_m($o);
//		$o['data'] = serialize($o['data']);
//		$o['internaldata'] = serialize($o['internaldata']);
//		//*
//		MLDatabase::getDbInstance()->update(TABLE_MAGNA_ORDERS, $o, array(
//			'orders_id' => $o['orders_id']
//		));
//		//*/
//	}
//	return true;
//}
