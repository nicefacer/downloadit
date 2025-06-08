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
 * $Id: amazonConfig.php 5103 2015-02-01 11:45:54Z derpapst $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
$aGet=  MLRequest::gi()->data();
$aPost=$aGet;
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/Configurator.php');
include_once(DIR_MAGNALISTER_INCLUDES.'lib/configFunctions.php');

function renderAuthError($authError) {
	if (!is_array($authError)) {
		return '';
	}
	$errors = array();
	if (array_key_exists('ERRORS', $authError) && !empty($authError['ERRORS'])) {
		foreach ($authError['ERRORS'] as $err) {
			$errors[] = $err['ERRORMESSAGE'];
		}
	}
    return '<p class="errorBox">
     	<span class="error bold larger">'.MLI18n::gi()->ML_ERROR_LABEL.':</span>
     	'.MLI18n::gi()->ML_ERROR_AMAZON_WRONG_SELLER_CENTRAL_LOGIN.(
     		(!empty($errors))
     			? '<br /><br />'.implode('<br />', $errors)
     			: ''
     	).'</p>';
}

function amazonTopTenConfig($aArgs = array(), &$sValue = ''){
	global $_MagnaSession;
        $aGet=  MLRequest::gi()->data();
	require_once MLFilesystem::getOldLibPath('php'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'amazon'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'amazonTopTen.php');
	$oTopTen = new amazonTopTen();
	$oTopTen->setMarketPlaceId($_MagnaSession['mpID']);
	if (isset($aGet['what'])) {
		if (!isset($aGet['tab'])) {
			echo $oTopTen->renderConfig();
		} elseif ($aGet['tab'] == 'init') {
			echo $oTopTen->renderConfigCopy( (isset($aGet['executeTT'])) && ($aGet['executeTT']=='true') );
		} elseif($aGet['tab'] == 'delete') {
			echo $oTopTen->renderConfigDelete(
				isset($aGet['delete']) 
				? $aGet['delete'] 
				: array()
			);
		}
	} else {
		return $oTopTen->renderMain(
			$aArgs['key'],
			isset($aGet['conf'][$aArgs['key']])
			? (int)$aGet['conf'][$aArgs['key']]
			: (int)getDBConfigValue($aArgs['key'], $_MagnaSession['mpID'])
		);
	}
}

//function magnaUpdateCarrierCodes($args) {
//	global $_MagnaSession;
//
//	setDBConfigValue('amazon.orderstatus.carrier.additional', $_MagnaSession['mpID'], $args['value']);
//
//	$carrierCodes = loadCarrierCodes();
//	$setting = getDBConfigValue(
//		'amazon.orderstatus.carrier.default',
//		$_MagnaSession['mpID']
//	);
//
//	$ret = '';
//	foreach ($carrierCodes as $val) {
//		$ret .= '<option '.(($val == $setting) ? 'selected="selected"' : '').' value="'.$val.'">'.$val.'</option>'."\n";
//	}
//	return $ret;
//}

$_url['mode'] = 'conf';
if (isset($aGet['what']) && ($aGet['what'] == 'topTenConfig')){
	amazonTopTenConfig();
	exit();
}

//$form = loadConfigForm($_lang,
//	array(
//		'amazon.form' => array(),
//	), array(
//		'_#_platform_#_' => $_MagnaSession['currentPlatform'],
//		'_#_platformName_#_' => $_modules[$_Marketplace]['title']
//	)
//);
//new dBug(MLI18n::gi()->getGlobal('aConfigForm'));
$form= json_decode(
        str_replace(
                array('_#_platform_#_','_#_platformName_#_'),
                array($_MagnaSession['currentPlatform'],$_modules[$_Marketplace]['title']),
                json_encode(MLI18n::gi()->getGlobal('aConfigForm'))
        ),true
 );

function amazonLeadtimeToShipMatching($args, &$value = '') {
	global $_MagnaSession;
	if (!defined('TABLE_SHIPPING_STATUS') || !MLDatabase::getDbInstance()->tableExists(TABLE_SHIPPING_STATUS)) {
		return MLI18n::gi()->ML_ERROR_NO_SHIPPINGTIME_MATCHING;
	}
	$hippingtimes = MLDatabase::getDbInstance()->fetchArray('
	    SELECT shipping_status_id as id, shipping_status_name as name
	      FROM '.TABLE_SHIPPING_STATUS.'
	     WHERE language_id = '.$_SESSION['languages_id'].' 
	  ORDER BY shipping_status_id ASC
	');
	$leadtimeMatch = getDBConfigValue($args['key'], $_MagnaSession['mpID'], array());
	$opts = array_merge(array (
		'0' => '&mdash;',
	), range(1, 30));
	$html = '<table class="nostyle" style="float: left; margin-right: 2em;">
		<thead><tr>
			<th>'.MLI18n::gi()->ML_LABEL_SHIPPING_TIME_SHOP.'</th>
			<th>'.MLI18n::gi()->ML_AMAZON_LABEL_LEADTIME_TO_SHIP.'</th>
		</tr></thead>
		<tbody>';
	foreach ($hippingtimes as $st) {
		$html .= '
			<tr>
				<td class="nowrap">'.$st['name'].'</td>
				<td><select name="conf['.$args['key'].']['.$st['id'].']">';
		foreach ($opts as $key => $val) {
			$html .= '<option value="'.$key.'" '.(
				(array_key_exists($st['id'], $leadtimeMatch) && ($leadtimeMatch[$st['id']] == $key))
					? 'selected="selected"'
					: ''
			).'>'.$val.'</option>';
		}
		$html .= '
				</select></td>
			</tr>';
	}
	$html .= '</tbody></table>';

#	$html .= print_m($taxes, '$taxes');
#	$html .= print_m(func_get_args(), 'func_get_args');
	return $html;
}


$aMarketplaces = amazonGetMarketplaces();
$form['amazonaccount']['fields']['site']['values'] = $aMarketplaces['Sites'];
	
$boxes = '';
$auth = getDBConfigValue('amazon.authed', $_MagnaSession['mpID'], false);
if ((!is_array($auth) || !$auth['state']) &&
	allRequiredConfigKeysAvailable($authConfigKeys, $_MagnaSession['mpID']) && 
	!(
		array_key_exists('conf', $aGet) && 
		allRequiredConfigKeysAvailable($authConfigKeys, $_MagnaSession['mpID'], $aGet['conf'])
	)
) {
    $boxes .= renderAuthError($authError);
}

if (array_key_exists('conf', $aGet)) {
	$nUser = trim($aGet['conf']['amazon.username']);
	$nPass = trim($aGet['conf']['amazon.password']);
	$nMerchant = trim($aGet['conf']['amazon.merchantid']);
	$nMarketplace = trim($aGet['conf']['amazon.marketplaceid']);
	$nSite = $aGet['conf']['amazon.site'];

	if (!empty($nUser) && (getDBConfigValue('amazon.password', $_MagnaSession['mpID']) == '__saved__') && empty($nPass)) {
		$nPass = '__saved__';
	}

	if (!empty($nUser) && !empty($nPass)) {
		if ((strpos($nPass, '&#9679;') === false) && (strpos($nPass, '&#8226;') === false)) {
			/*               Windows                                  Mac                */
			setDBConfigValue('amazon.authed', $_MagnaSession['mpID'], array (
				'state' => false,
				'expire' => time()
			), true);
			try {
				$result = MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'SetCredentials',
					'USERNAME' => $nUser,
					'PASSWORD' => $nPass,
					'MERCHANTID' => $nMerchant,
					'MARKETPLACE' => $nMarketplace,
					'SITE' => $nSite
				));
				$boxes .= '
					<p class="successBox">'.MLI18n::gi()->ML_GENERIC_STATUS_LOGIN_SAVED.'</p>
				';
			} catch (MagnaException $e) {
				$boxes .= '
					<p class="errorBox">'.MLI18n::gi()->ML_GENERIC_STATUS_LOGIN_SAVEERROR.'</p>
				';
			}
			
			try {
				MagnaConnector::gi()->submitRequest(array(
					'ACTION' => 'IsAuthed',
				));
				$auth = array (
					'state' => true,
				);
			} catch (MagnaException $e) {
				$e->setCriticalStatus(false);
				$boxes .= renderAuthError($e->getErrorArray());
				$auth = array (
					'state' => false
				);
			}

		} else {
	        $boxes .= '
	            <p class="errorBox">'.MLI18n::gi()->ML_ERROR_INVALID_PASSWORD.'</p>
	        ';
		}
	}
	
	if (!empty($nSite)) {
		setDBConfigValue('amazon.currency', $_MagnaSession['mpID'], $aMarketplaces['Currencies'][$nSite], true);
	}
	unset($currencyError);
	require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/SimplePrice.php');
	$sp = new SimplePrice();
	if (!$sp->currencyExists($aMarketplaces['Currencies'][$nSite])) {
		$boxes .= '<p class="errorBox">'.sprintf(
			MLI18n::gi()->ML_GENERIC_ERROR_CURRENCY_NOT_IN_SHOP,
			$aMarketplaces['Currencies'][$nSite]
		).'</p>';
	}
}
if (isset($currencyError) && (getCurrencyFromMarketplace($_MagnaSession['mpID']) !== false)) {
	$boxes .= $currencyError;
}

if (!$auth['state']) {
	$form = array (
		'amazonaccount' => $form['amazonaccount']
	);
} else {
	$auth['expire'] = time() + 60 * 15;
	setDBConfigValue('amazon.authed', $_MagnaSession['mpID'], $auth, true);
//	$form['matchingvalues']['fields']['itemcondition']['values'] = amazonGetPossibleOptions('ConditionTypes');
//	$form['matchingvalues']['fields']['shipping']['values'] = amazonGetPossibleOptions('ShippingLocations');
//	$form['orderSyncState']['fields']['carrier']['values'] = loadCarrierCodes();

//	getLanguages($form['prepare']['fields']['lang']);

//	getOrderStatus($form['import']['fields']['openstatus']);
//	getOrderStatus($form['import']['fields']['orderStatusFba']);
//	getOrderStatus($form['orderSyncState']['fields']['cancelstatus']);
//	getOrderStatus($form['orderSyncState']['fields']['shippedstatus']);
	
//	getCustomersStatus($form['import']['fields']['customersgroup']);
//	getCustomersStatus($form['price']['fields']['whichprice'], false);
//	if (!empty($form['price']['fields']['whichprice'])) {
//		$form['price']['fields']['whichprice']['values']['0'] = ML_LABEL_SHOP_PRICE;
//		ksort($form['price']['fields']['whichprice']['values']);
//        unset($form['price']['fields']['specialprices']);
//	} else {
//		unset($form['price']['fields']['whichprice']);
//	}
//	$form['apply']['fields']['imagepath']['default'] = SHOP_URL_POPUP_IMAGES;

//	getShippingModules($form['import']['fields']['defaultshipping']);
//	getPaymentModules($form['import']['fields']['defaultpayment']);
}

$cG = new Configurator($form, $_MagnaSession['mpID'], 'conf_amazon');
$cG->setRenderTabIdent(true);
$allCorrect = $cG->processPOST();

if (isset($aGet['ajax']) && ($aGet['ajax'] == true)) {
	echo $cG->processAjaxRequest();
} else {
	echo $boxes;
        try{
            MLRequest::gi()->get('sendTestMail');
		if ($allCorrect) {
                    ML::gi()->init(array('do'=>'importorders'));//activate sync-modul
                    if (sendTestMail($_MagnaSession['mpID'])) {
                        MLMessage::gi()->addSuccess(MLI18n::gi()->ML_GENERIC_TESTMAIL_SENT);
                    } else {
                        MLMessage::gi()->addNotice(MLI18n::gi()->ML_GENERIC_TESTMAIL_SENT_FAIL);
                    }
                    ML::gi()->init();
		} else {
                    MLMessage::gi()->addNotice(MLI18n::gi()->ML_GENERIC_NO_TESTMAIL_SENT);
		}
	}catch(Exception $oEx){
//            echo $oEx->getMessage();
        }

	echo $cG->renderConfigForm();
}
