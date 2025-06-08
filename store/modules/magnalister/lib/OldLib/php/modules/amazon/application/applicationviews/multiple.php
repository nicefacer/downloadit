<?php 
	global $conditionHtml;

    $categories=  MLModul::gi()->getMainCategories();
	$htmlCategories = '<option value="null">'.$this->__('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT').'</option>';
	$tmpCats = array('null' => $this->__('ML_AMAZON_LABEL_APPLY_PLEASE_SELECT'));
	if (!empty($categories)) {
		$htmlCategories .= renderAmazonTopTen('topMainCategory');
		foreach ($categories as $catKey => $catName) {
		$htmlCategories .= '
						<option value="'.$catKey.'">'.fixHTMLUTF8Entities($catName).'</option>';
		}
	}
	if (($data['MainCategory'] != '') && ($data['MainCategory'] != 'null')) {
		$htmlCategories = str_replace(
			'<option value="'.$data['MainCategory'].'">',
			'<option value="'.$data['MainCategory'].'" selected="selected">',
			$htmlCategories
		);
		$cna = getProductTypesAndAttributes($data['MainCategory']);
        $conditionHtml = checkCondition($cna, $data['ConditionType']);		
		$htmlSubCategories = $cna['ProductTypes'];
		$htmlAdditionalAttributes = $cna['Attributes'];
                
	} else {
		$htmlSubCategories = '<option value="null">'.$this->__('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_CAT_FIRST').'</option>';
		$htmlAdditionalAttributes = false;
	}

	if (($data['MainCategory'] != '') && ($data['MainCategory'] != 'null')
	    && (array_key_exists('ProductType', $data) || !empty( $data['Attributes']))
	) {
		if (array_key_exists('ProductType', $data) && ($data['ProductType'] != '') 
			&& ($data['ProductType'] != 'null') && ($data['ProductType'] != false)
		) {
			$htmlSubCategories = str_replace(
				'<option value="'.$data['ProductType'].'">',
				'<option value="'.$data['ProductType'].'" selected="selected">',
				$htmlSubCategories
			);
		} else {
			$data['ProductType'] = false;
		}
		$browseNodes = getBrowseNodes($data['MainCategory'], $data['ProductType']);		
                if (isset($data['BrowseNodes'][0]) && $data['BrowseNodes'][0] != '') {
                        $browseNodes = str_replace(
                                '<option value="'.$data['BrowseNodes'][0].'">',
                                '<option value="'.$data['BrowseNodes'][0].'" selected="selected">',
                                $browseNodes
                        );
                }
	} else {
		$browseNodes = '<option value="null">'.$this->__('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST').'</option>';
	}

	$htmlAdditionalAttributes = convertAttrArrayToHTML($htmlAdditionalAttributes, $data['Attributes']);

	$html = '
		<tbody>
			<tr class="headline">
				<td colspan="3"><h4>'.ML_LABEL_CATEGORY.'</h4></td>
			</tr>
			<tr class="odd">
				<th>'.ML_LABEL_MAINCATEGORY.' <span>&bull;</span></th>
				<td class="input">
					<select name="'.MLHttp::gi()->parseFormFieldName('MainCategory').'" id="maincat" class="fullWidth">
						'.$htmlCategories.'
					</select>
				</td>
				<td class="info">&nbsp;</td>
			</tr>
			<tr id="subCategory" class="even" '.(empty($htmlSubCategories) ? 'style="display:none;"' : '').'>
				<th>'.ML_LABEL_SUBCATEGORY.' <span>&bull;</span></th>
				<td class="input">
					<select name="'.MLHttp::gi()->parseFormFieldName('ProductType').'" id="subcat"  class="fullWidth">
						'.$htmlSubCategories.'
					</select>
				</td>
				<td class="info">&nbsp;</td>
			</tr>
			<tr id="additionalAttributes" class="even" '.(empty($htmlAdditionalAttributes) ? 'style="display:none;"' : '').'>
				<th>' . $this->__('ML_AMAZON_LABEL_APPLY_ATTRIBUTES') . '</th>
				<td class="input" colspan="2">
					'.$htmlAdditionalAttributes.'
				</td>
			</tr>
			<tr class="odd">
				<th>'.$this->__('ML_AMAZON_LABEL_APPLY_BROWSENODES').' <span>&bull;</span></th>
				<td class="input" id="browsenodes">
					<select name="'.MLHttp::gi()->parseFormFieldName('BrowseNodes[]').'"  class="fullWidth" id="browsenode">
						'.$browseNodes.'
					</select>
				</td>
				<td class="info">&nbsp;</td>
			</tr>
			<tr class="spacer">
				<td colspan="3">&nbsp;';
	
	ob_start();
$sNeeded='';
foreach(MLHttp::gi()->getNeededFormFields() as $sKey=>$sValue){
    $sNeeded.=", '".$sKey."':'".$sValue."'";
}
    /* @var $this ML_Amazon_Controller_Amazon_Prepare_Apply_Form  */
?><script type="text/javascript">/*<![CDATA[*/
 (function($) {
function loadBrowseNodes(subCat) {
	$.blockUI(blockUILoading);
	$.ajax({
		type: 'POST',
		url: '<?php echo $this->getCurrentUrl();?>',
		data: {
			'<?php echo MLHttp::gi()->parseFormFieldName('type')?>': 'browsenodes',
			'<?php echo MLHttp::gi()->parseFormFieldName('category')?>': $('#maincat').val(),
			'<?php echo MLHttp::gi()->parseFormFieldName('subcategory')?>': subCat,
                        '<?php echo MLHttp::gi()->parseFormFieldName('kind')?>':'ajax'
                        <?php echo $sNeeded ?>
		},
		success: function(data) {
			$('#browsenodes select').html(data);
			$.unblockUI();
		},
		error: function(xhr, status, error) {
			$('#browsenodes select').html('<option value="null"><?php  echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST',array('\'')) ?></option>');
			$('#subcat').val('null');
			myConsole.log(arguments);
			$.unblockUI();
		}
	});
}

function loadProductTypesAndAttributes(mainCategory) {
	$.blockUI(blockUILoading);
	$.ajax({
		type: 'POST',
		url: '<?php echo $this->getCurrentUrl();?>',
		data: {
			'<?php echo MLHttp::gi()->parseFormFieldName('type')?>': 'subcategories',
			'<?php echo MLHttp::gi()->parseFormFieldName('category')?>': mainCategory,
                        '<?php echo MLHttp::gi()->parseFormFieldName('kind')?>':'ajax'
                        <?php echo $sNeeded ?>
		},
		success: function(data) {
                        try{
                            var data=$.parseJSON(data);
                        }catch(e){
                        }
			if (data.ProductTypes == false) {
				$('#subCategory').css({'display':'none'});
				$('#subcat').html('');
			} else {
				$('#subCategory').css({'display':'table-row'});
				$('#subcat').html(data.ProductTypes)
			}
			subcatVal = $('#subcat').val();
			if ((subcatVal == null) || (subcatVal == '') || (subcatVal == 'null')) {
				loadBrowseNodes(false);
			} else {
				loadBrowseNodes(subcatVal);
			}
			if (data.Attributes == false) {
				$('#additionalAttributes').css({'display':'none'});
				$('#additionalAttributes td.input').html('');
			} else {
				$('#additionalAttributes').css({'display':'table-row'});
				$('#additionalAttributes > td.input').html(data.Attributes);
			}
                        if (data.ConditionType == false) {
                            $('.ArtikelConditions').css({'display': 'none'});
                        } else {
                            $('.ArtikelConditions').css({'display': 'table-row'});
                            $('#condition_type').html(data.ConditionType);
                        }
			//jqml.unblockUI();
		},
		error: function(xhr, status, error) {
			$('#subcat').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_CAT_FIRST',array('\'')) ?></option>').css({'display':'block'});
			$('#browsenodes select').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST',array('\'')) ?></option>');
			$('#maincat').val('null');
			myConsole.log(arguments);
			$.unblockUI();
		}
	});
}

$(document).ready(function() {
	$('#maincat').change(function() {
		if ($(this).val() != 'null') {
			loadProductTypesAndAttributes($(this).val());
		} else {
			$('#subcat').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_CAT_FIRST',array('\'')) ?></option>').css({'display':'block'});
			$('#browsenodes select').html('<option value="null"><?php echo $this->__s('ML_AMAZON_LABEL_APPLY_SELECT_MAIN_SUB_CAT_FIRST',array('\'')) ?></option>');
			$('#additionalAttributes').css({'display':'none'});
			$('#additionalAttributes td.input').html('');
		}
	});
	$('#subcat').change(function() {
		if ($(this).val() != 'null') {
			loadBrowseNodes($(this).val());
		}
	});
});
})(jqml);
/*]]>*/</script><?php
	$html .= ob_get_contents();
	ob_end_clean();
	$html .= '
				</td>
			</tr>
		</tbody>';

	return $html;