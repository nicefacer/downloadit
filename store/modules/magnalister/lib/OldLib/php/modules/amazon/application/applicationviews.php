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
 * $Id: applicationviews.php 5966 2015-09-02 13:06:33Z masoud.khodaparast $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

function renderFlat($data, $prefix = '') {
	$finalArray = array();
	foreach ($data as $key => $value) {
		$newKey = (empty($prefix)) ? $key : $prefix.'['.$key.']';
		if (is_array($value)) {
			$finalArray = array_merge($finalArray, renderFlat($value, $newKey));
		} else {
			$finalArray[$newKey] = $value;
		}
	}
	return $finalArray;
}

function getProductTypesAndAttributes($category) {
    $result=  MLModul::gi()->getProductTypesAndAttributes($category);
	
	if ($result['ProductTypes'] !== false) {
		$html = renderAmazonTopTen('topProductType', array($category));
		foreach ($result['ProductTypes'] as $key => $value) {
			$html .= '
				<option value="'.$key.'">'.$value.'</option>';
		}
		$result['ProductTypes'] = $html;
	}
	return $result;
}

function getBrowseNodes($category, $subcategory) {
	try {
		$browseNodes = MagnaConnector::gi()->submitRequest(array(
			'ACTION' => 'GetBrowseNodes',
			'CATEGORY' => $category,
			'SUBCATEGORY' => $subcategory
		));
		$browseNodes = $browseNodes['DATA'];
	}
	catch (MagnaException $e) {	}
	if (!isset($browseNodes) || empty($browseNodes)) {
		$browseNodes = array('null' => ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST);
	}
	$html = '
			<option value="null">'.ML_AMAZON_LABEL_APPLY_BROWSENODE_NOT_SELECTED.'</option>';
	$html.= renderAmazonTopTen('topBrowseNode', array($category,$subcategory ));
	foreach ($browseNodes as $nodeID => $nodeName) {
		$html .= '
			<option value="'.$nodeID.'">'.str_replace(
				array('\\/',  '/',        '#\\#'),
				array('#\\#', ' &rarr; ', '/'   ),
				fixHTMLUTF8Entities($nodeName)
			).'</option>';
	}
	return $html;
}

function convertAttrArrayToHTML($data, $usrData = array()) {
	if (!is_array($data) || empty($data)) return '';
	$attr = array();

	foreach ($data as $key => &$def) {
		$usrValue = isset($usrData[$key]) ? $usrData[$key] : '';
		#echo var_dump_pre($usrValue, $key);
		$def['type'] = isset($def['type']) ? $def['type'] : 'text';
		$def['desc'] = isset($def['desc']) ? $def['desc'] : '';

		switch ($def['type']) {
			case 'select': {
				$html = '<select name="'.MLHttp::gi()->parseFormFieldName('Attributes['.$key.']').'" class="fullWidth">'."\n";
				foreach ($def['values'] as $vk => $vv) {
					$html .= '    <option value="'.$vk.'"'.(($vk == $usrValue) ? 'selected="selected"' : '').'>'.$vv.'</option>'."\n";
				}
				$html .= '</select><br/>'."\n";
				break;
			}
			default: {
				$html = '<input type="text" value="'.$usrValue.'" name="'.MLHttp::gi()->parseFormFieldName('Attributes['.$key.']').'">'."\n";
				break;
			}
		}
		$def['html'] = $html;
	}

	$htmlAA = '<table class="attrTable"><tbody>';
	$rowC = 0;
	$maxRowC = count($data) - 1;
	foreach ($data as $a) {
		$class = array();
		if ($rowC == 0) $class[] = 'first';
		if ($rowC == $maxRowC) $class[] = 'last';
		$htmlAA .= '<tr class="'.implode(' ', $class).'">
			<td class="key">'.fixHTMLUTF8Entities($a['title']).': </td>
			<td class="input">'.$a['html'].'</td>
			<td class="info">'.(isset($a['desc']) ? str_replace("\n", "<br>\n", fixHTMLUTF8Entities($a['desc'])) : '').'</td>
		</tr>';
		++$rowC;
	}
	$htmlAA .= '</tbody></table>';
	return $htmlAA;
}

function renderAmazonTopTen($sField, $aConfig = array()){
	global $_MagnaSession;
	require_once MLFilesystem::gi()->getOldLibPath('php'.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.'amazon'.DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'amazonTopTen.php');
	$oTopTen = new amazonTopTen();
	$oTopTen->setMarketPlaceId($_MagnaSession['mpID']);
	$aTopTen = $oTopTen->getTopTenCategories($sField, $aConfig);
	$sOut ='<optgroup label="'.ML_TOPTEN_TEXT.'">';
	foreach ($aTopTen as $sKey=>$sValue) {
		$sOut .= '<option value="'.$sKey.'">'.$sValue.'</option>';
	}
	$sOut .='</optgroup>';
	return $sOut;
}

function checkCondition(&$attributes, $selected = false) {
    global $conditionStatus;
    $html = '';
    if (!empty($attributes['Attributes']) && array_key_exists('ConditionType', $attributes['Attributes'])) {
        $selected = ($selected && !empty($selected)) ? $selected : MLModul::gi()->getConfig('itemcondition');
        $mapConditionAttributes = $attributes['Attributes']['ConditionType']['values'];
        unset($attributes['Attributes']['ConditionType']);
        $html = '';
        foreach ($mapConditionAttributes as $conditions_key => $conditions_val) {
            $html .= '<option value="' . $conditions_key . '" ' . (($selected == $conditions_key) ? 'selected' : '') . '>' . fixHTMLUTF8Entities($conditions_val) . '</option>';
        }
        $attributes['ConditionType'] = $html;
        $conditionStatus = true;
    } else {
        $attributes['ConditionType'] = false;
    }
    return $html;
}

function renderGenericApplication($data) {
        global $conditionStatus, $conditionHtml;
	$opts = array_merge(array (
		'0' => '&mdash;',
		'X' => ML_LABEL_DO_NOT_CHANGE,
	), range(1, 30));

	$html = '		
		<tbody>
			<tr class="headline">
				<td colspan="3"><h4>'.ML_LABEL_GENERIC_SETTINGS.'</h4></td>
			</tr>
			<tr class="odd">
				<th>'.ML_GENERIC_SHIPPING_TIME.'</th>
				<td class="input">
                                    <select class="fullwidth" name="'.MLHttp::gi()->parseFormFieldName('LeadtimeToShip').'">';
	$usrValue = $data['LeadtimeToShip'];
	foreach ($opts as $vk => $vv) {
		$html .= '    <option value="'.$vk.'"'.(($vk == $usrValue) ? 'selected="selected"' : '').'>'.$vv.'</option>'."\n";
	}
        $html .= '"
                            </select>
                    </td>
                    <td class="info">&nbsp;</td>
            </tr>';
        $html .= '
            <tr class="odd ArtikelConditions" style="display: ' . (($conditionStatus) ? 'table-row' : 'none') . ';">
                    <th>' . ML_GENERIC_CONDITION . '</th>
                    <td class="input"><select class="fullWidth" id="condition_type" name="'.MLHttp::gi()->parseFormFieldName('ConditionType').'">' . (($conditionStatus) ? $conditionHtml : '') . '</select></td>
                    <td class="info"></td>
            </tr>
            <tr class="odd ArtikelConditions" style="display:' . (($conditionStatus) ? 'table-row' : 'none') . ';">
                    <th>' . MLI18n::gi()->ML_GENERIC_CONDITION_NOTE . '</th>
                    <td class="input"><textarea class="fullWidth" rows="10" id="condition_note" name="'.MLHttp::gi()->parseFormFieldName('ConditionNote').'">' . (($conditionStatus) ? $data['ConditionNote'] : '') . '</textarea></td>
                    <td class="info"></td>
            </tr>';
        $html .= '
			<tr class="spacer">
				<td colspan="3">&nbsp;</td>
			</tr>
		</tbody>';//.print_m($data);
	return $html;
}
$aPost=  MLRequest::gi()->data();
$aGet=$aPost;
$conditionStatus = false;
$conditionHtml = '';
if (isset($aGet['kind']) && ($aGet['kind'] == 'ajax')) {
	if (isset($aPost['type']) && ($aPost['type'] == 'subcategories') && isset($aPost['category'])) {
		$caa = getProductTypesAndAttributes($aPost['category']);
                checkCondition($caa);
		$caa['Attributes'] = convertAttrArrayToHTML($caa['Attributes']);
		die(json_encode($caa));
	}
	if (isset($aPost['type']) && ($aPost['type'] == 'browsenodes') && isset($aPost['category']) && isset($aPost['subcategory'])) {
		die(getBrowseNodes($aPost['category'], $aPost['subcategory']));
	}
	if (isset($aPost['type']) && ($aPost['type'] == 'resetToDefaults') && isset($aPost['pID'])) {
            $aProductids = explode(',', $aPost['pID']);
            if($aProductids !== false){
                foreach ($aProductids as $sId){
                    if(ctype_digit($sId)){
                        if(!isset($pID)){
                            $pID = (int)$sId;
                        }
                        MLDatabase::factory('amazon_prepare')->set('productsid',(int)$sId)->delete();
                    }
                }
            }
            $dataReset = populateGenericData($pID);
            $dataReset = renderFlat($dataReset);
            arrayEntitiesToUTF8($dataReset);
            $dataReset['Description'] = html_entity_decode($dataReset['Description'], ENT_COMPAT, 'UTF-8');
            die(json_encode($dataReset));
	}
	die();
}

echo '<h2>'.(($applyAction == 'multiapplication') ? ML_AMAZON_LABEL_APPLY_MULTI : ML_AMAZON_LABEL_APPLY_SINGLE).'</h2>';
if ($applyAction != 'multiapplication') {
    $sSql = "
            SELECT s.pID, p.mpID IS NOT NULL as edit
            FROM magnalister_selection s 
            LEFT JOIN magnalister_amazon_prepare p on s.pID=p.ProductsID and  s.mpID=p.mpID
            WHERE
            s.mpID='".MLModul::gi()->getMarketPlaceId()."' 
            AND
            s.selectionname='apply' 
            AND
            s.session_id='".MLShop::gi()->getSessionId()."'
        ";
        $aProducts = MLDatabase::getDbInstance()->fetchArray($sSql);
        $aFirstVariant = current($aProducts);
        $pID = '';
        foreach($aProducts as $aProduct){
            $pID.= $aProduct['pID'].',';
        }
        $pID = substr($pID, 0,-1);
	$data = populateGenericData($aFirstVariant['pID'], $aFirstVariant['edit']);
        
} else {
	$multiEdit = MLDatabase::getDbInstance()->fetchOne(eecho("
            SELECT s.pID
            FROM magnalister_selection s 
            LEFT JOIN magnalister_amazon_prepare p on s.pID=p.ProductsID and  s.mpID=p.mpID
            WHERE 
            s.mpID='".MLModul::gi()->getMarketPlaceId()."' 
            AND
            s.selectionname='apply' 
            AND
            s.session_id='".MLShop::gi()->getSessionId()."'
            LIMIT 1
	", false)) === false ? false : true;
	$data = populateGenericData(0, $multiEdit);
}
echo '
<form name="apply" method="post" action="'.$this->getCurrentUrl().'">';
foreach(MLHttp::gi()->getNeededFormFields() as $sKey=>$sValue){
    echo '<input type="hidden" name="'.$sKey.'" value="'.$sValue.'" />';
}
    $sMultipleView = include (dirname(__FILE__).'/applicationviews/multiple.php');
    if($applyAction != 'multiapplication'){
        $sSingleView = include (dirname(__FILE__).'/applicationviews/single.php');
    }
    echo '<input type="hidden" name="'.MLHttp::gi()->parseFormFieldName('saveApplyData').'" value="true" />
	<p>'.ML_AMAZON_TEXT_APPLY_REQUIERD_FIELDS.'</p>
	<table class="attributesTable">
		'.$sMultipleView .'
		'.(($applyAction != 'multiapplication') ? $sSingleView: '').'
		'.renderGenericApplication($data).'
	</table>
	<table class="actions">
		<thead><tr><th>'.ML_LABEL_ACTIONS.'</th></tr></thead>
		<tbody>
			<tr class="firstChild"><td>
				<table><tbody><tr>
					<td class="firstChild">'.(($applyAction == 'singleapplication')
						? '<input id="resetToDefaults" class="mlbtn" type="button" value="'.ML_BUTTON_LABEL_REVERT.'"/>'
						: ''
					).'</td>
					<td class="lastChild">'.'<input class="mlbtn action" type="submit" value="'.ML_BUTTON_LABEL_SAVE_DATA.'"/>'.'</td>
				</tr></tbody></table>
			</td></tr>
		</tbody>
	</table>
</form>';
if ($applyAction != 'multiapplication') {
$sNeeded='';
foreach(MLHttp::gi()->getNeededFormFields() as $sKey=>$sValue){
    $sNeeded.=", '".$sKey."':'".$sValue."'";
}
?>
<script type="text/javascript">/*<![CDATA[*/
     (function($) {
    $(document).ready(function() {
    	$('#resetToDefaults').click(function() {  
    		$.blockUI(blockUILoading);
    		$.ajax({
    			type: 'POST',
    			url: '<?php echo $this->getCurrentUrl(); ?>',
    			data: {
    				'<?php echo MLHttp::gi()->parseFormFieldName('type') ?>': 'resetToDefaults',
    				'<?php echo MLHttp::gi()->parseFormFieldName('pID') ?>': '<?php echo $pID; ?>',
                                    '<?php echo MLHttp::gi()->parseFormFieldName('kind') ?>':'ajax'
        <?php echo $sNeeded ?>
    			},
    			success: function(data) {                            
                                try{
                                    var data=$.parseJSON(data);
                                }catch(e){
                                }
    				$('#maincat').val('null');
    				$('#subcat').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_CAT_FIRST',array('\'')) ?></option>').css({'display':'block'});
    				$('#browsenodes select').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST',array('\'')) ?></option>');
    				myConsole.log(data);
    				if (is_object(data)) {
    					for (var k in data) {
    						var v = data[k];
    						var e = $('[name$="\['+k+'\]"]');
    						if (e.attr('type') == 'checkbox') {
    							if (v == "false") {
    								e.removeAttr('checked');
    							} else {
    								e.attr('checked', 'checked');
    							}
    						} else {
    							e.val(v);
    						}
    					}
    				}
    				$.unblockUI();
    			},
    			error: function(xhr, status, error) {
    				myConsole.log(arguments);
    				$.unblockUI();
    			}
    		});
    	});
    });
    })(jqml);
/*]]>*/</script>
<?php
}