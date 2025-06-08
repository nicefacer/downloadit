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
 * $Id: configuration.php 6101 2015-10-13 12:03:24Z markus.bauer $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
/**
 * Global Configuration
 */

$_MagnaSession['mpID'] = '0';
 
require_once(DIR_MAGNALISTER_INCLUDES.'lib/classes/Configurator.php');

/*
MagnaConnector::gi()->setTimeOutInSeconds(1);
try {
	MagnaConnector::gi()->submitRequest(array(
		'ACTION' => 'Ping',
		'SUBSYSTEM' => 'Core',
	));
} catch (MagnaException $e) {}
MagnaConnector::gi()->resetTimeOut();
*/
//debugBacktrace(4);die;
$form = MLI18n::gi()->getGlobal('aGeneralForm');
unset($form['ftp']);

$keysToSubmit = array();

/* {Hook} "GenericConfiguration": Enables you to extend the generic configuration mask<br>
   Variables that can be used: 
   <ul><li>$form: The array that is used to generate the form.</li>
   </ul>
 */
if (($hp = magnaContribVerify('GenericConfiguration', 1)) !== false) {
	require($hp);
}

$cG = new Configurator($form, $_MagnaSession['mpID'], 'conf_general');
$cG->processPOST($keysToSubmit);

/* Passphrase is in DB now. Try to authenticate us */
if (isset($_POST['conf']['general.passphrase'])) {
	MagnaConnector::gi()->updatePassPhrase();
	if (!loadMaranonCacheConfig(true)) {
		MLMessage::gi()->addError(MLI18n::gi()->ML_ERROR_UNAUTHED);
	} else {
//		if (MLDatabase::getDbInstance()->recordExists(TABLE_CONFIGURATION, array (
//			'configuration_key' => 'MAGNALISTER_PASSPHRASE'
//		))) {
//			MLDatabase::getDbInstance()->update(TABLE_CONFIGURATION, array (
//				'configuration_value' => $_POST['conf']['general.passphrase']
//			), array (
//				'configuration_key' => 'MAGNALISTER_PASSPHRASE'
//			));
//		} else {
//			MLDatabase::getDbInstance()->insert(TABLE_CONFIGURATION, array (
//				'configuration_value' => $_POST['conf']['general.passphrase'],
//				'configuration_key' => 'MAGNALISTER_PASSPHRASE'
//			));
//		}
	}
}

$passPhrase = getDBConfigValue('general.passphrase', '0');

if (empty($passPhrase) || MLRequest::gi()->data('welcome') !== null) {
	$form = array(
		'general' => $form['general']
	);
    
    try {
        $partner = 'partner='.MLSetting::gi()->get('magnaPartner');
    } catch (MLSetting_Exception $oEx) {
        $partner = '';
    }
	unset($form['general']['headline']);
	/* Hier die bunte Startseite */
    try {
        $partner = 'partner='.MLSetting::gi()->get('magnaPartner');
    } catch (MLSetting_Exception $oEx) {
        $partner = '';
    }
    MLMessage::gi()->addNotice(sprintf(MLI18n::gi()->ML_NOTICE_PLACE_PASSPHRASE, $partner));
    ob_start();
    MLController::gi('main_content_promotion')->render();
    $comercialText = ob_get_clean();
} else {
	$cG->setRequiredConfigKeys($requiredConfigKeys);
}

global $forceConfigView;
if (($forceConfigView !== false) && !isset($comercialText)) {
	$evilProducts=MLShop::gi()->getProductsWithWrongSku();
	if (!empty($evilProducts)) {
		$traitorTable = '
			<table class="datagrid">
				<thead><tr>
					<th>'.str_replace(' ', '&nbsp;', MLI18n::gi()->ML_LABEL_PRODUCT_ID).'</th>
					<th>'.MLI18n::gi()->ML_LABEL_ARTICLE_NUMBER.'</th>
					<th>'.MLI18n::gi()->ML_LABEL_PRODUCTS_WITH_INVALID_MODELNR.'</th>
					<th>'.MLI18n::gi()->ML_LABEL_EDIT.'</th>
				</tr></thead>
				<tbody>';
			$oddEven = true;
			foreach ($evilProducts as $item) {
				$traitorTable .= '
					<tr class="'.(($oddEven = !$oddEven) ? 'odd' : 'even').'">
						<td style="width: 1px;">'.$item['products_id'].'</td>
						<td style="width: 1px;">'.(empty($item['products_model']) ? '<i class="grey">'.MLI18n::gi()->ML_LABEL_NOT_SET.'</i>' : $item['products_model']).'</td>
						<td>'.(empty($item['products_name']) ? '<i class="grey">'.MLI18n::gi()->ML_LABEL_UNKNOWN.'</i>' : $item['products_name']).'</td>
						<td class="textcenter" style="width: 1px;">
							<a class="gfxbutton edit ml-js-noBlockUi" title="'.MLI18n::gi()->ML_LABEL_EDIT.'" target="_blank" href="categories.php?pID='.$item['products_id'].'&action=new_product">&nbsp;</a>
						</td>
					</tr>';
			}
		$traitorTable .= '
				</tbody>
			</table>';
		echo $traitorTable;
	}
}

echo $cG->renderConfigForm();
?>
<style>
body.magna div#content .button {
/*
	background: linear-gradient(center top, rgba(255,255,255, 0.8) 0%, rgba(255,255,255,0) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.4) 100%), linear-gradient(left, red, orange, yellow, green, blue, indigo, violet);
	background: -moz-linear-gradient(center top, rgba(255,255,255, 0.8) 0%, rgba(255,255,255,0) 50%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.4) 100%), -moz-linear-gradient(left, red, orange, yellow, green, blue, indigo, violet);
	background: 
	-webkit-gradient(linear, left top, left bottom, 
		color-stop(0.00, rgba(255,255,255, 0.8)), 
		color-stop(0.49, rgba(255,255,255, 0)), 
		color-stop(0.51, rgba(0,0,0, 0)), 
		color-stop(1.00, rgba(0,0,0,0.4))
	), -webkit-gradient(linear, left top, right top, 
		color-stop(0.00, red), 
		color-stop(16%, orange),
		color-stop(32%, yellow),
		color-stop(48%, green),
		color-stop(60%, blue),
		color-stop(76%, indigo),
		color-stop(1.00, violet)
	);
	text-shadow: 0px 0px 2px rgba(255,255,255, 1);
	background-position: 0px 0px;
*/
}
</style>
<?php
if (isset($comercialText)) echo $comercialText;

if (isset($_POST['conf']['general.callback.importorders'])) {
	$hours = array();
	foreach ($_POST['conf']['general.callback.importorders'] as $hour => $selected) {
		if (!ctype_digit($hour) && !is_int($hour)) {
			continue;
		}
		$hours[(int)$hour] = $selected == 'true';
	}
	$request = array (
		'ACTION' => 'SetCallbackTimers',
		'SUBSYSTEM' => 'Core',
		'DATA' => array (
			'Command' => 'ImportOrders',
			'Hours' => $hours
		),
	);
	try {
		MagnaConnector::gi()->submitRequest($request);
	} catch (MagnaException $e) {}
}

if (isset($_GET['SKU'])) {
	$pID = magnaSKU2pID($_GET['SKU']);
	if ($pID > 0) {
		$pIDh = '<pre>magnaSKU2pID('.$_GET['SKU'].') :: '.var_dump_pre($pID, true).'</pre>';
	} else {
		$pIDh = var_dump_pre(magnaSKU2pID($_GET['SKU']), 'magnaSKU2pID('.$_GET['SKU'].')');
	}
	$aID = magnaSKU2aID($_GET['SKU']);
	if ($aID > 0) {
		$aIDh = '<form action="new_attributes.php" method="post">
			<input type="hidden" name="action" value="edit">
			<input type="hidden" name="current_product_id" value="'.$pID.'">
			<pre>magnaSKU2aID('.$_GET['SKU'].') ::<input style="background:transparent;border:none;font:12px monospace;" type="submit" value="'.var_dump_pre($aID, true).'"></pre></form>';
	} else {
		$aIDh = var_dump_pre(magnaSKU2aID($_GET['SKU']), 'magnaSKU2aID('.$_GET['SKU'].')');
	}
	echo $pIDh;
	echo $aIDh;
}

echo '<div id="switchSKU" class="dialog2" title="'.MLI18n::gi()->ML_TEXT_CONFIRM_SKU_CHANGE_TITLE.'">'.MLI18n::gi()->ML_TEXT_CONFIRM_SKU_CHANGE_TEXT.'</div>';
?>
<script type="text/javascript">/*<![CDATA[*/
(function($) {
    $(document).ready(function() {
        var radio = $('input[name="<?php echo MLHttp::gi()->parseFormFieldName('conf[general.keytype]'); ?>"]');
        radio.change(function (e) {
            $('#switchSKU').dialog({
                modal: true,
                width: '600px',
                buttons: {
                    "<?php echo MLI18n::gi()->ML_BUTTON_LABEL_ABORT; ?>": function() {
                        var radio = $('input[name="<?php echo MLHttp::gi()->parseFormFieldName('conf[general.keytype]'); ?>"]');
                        if (radio[1].checked) {
                            radio[0].checked = true;
                        } else {
                            radio[1].checked = true;
                        }
                        $(this).dialog("close");
                    },
                    "<?php echo MLI18n::gi()->ML_BUTTON_LABEL_OK; ?>": function() { 
                        $(this).dialog("close");
                    }
                }
            });
        });
    });
})(jqml);
/*]]>*/</script>
