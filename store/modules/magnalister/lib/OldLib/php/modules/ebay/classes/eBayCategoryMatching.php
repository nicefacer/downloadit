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
 * $Id: categorymatching.php 674 2011-01-08 03:21:50Z derpapst $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class eBayCategoryMatching {
	const EBAY_CAT_VALIDITY_PERIOD = 86400; # Nach welcher Zeit werden Kategorien ungueltig (Sekunden)
	const EBAY_STORE_CAT_VALIDITY_PERIOD = 600; # Nach welcher Zeit werden Store-Kategorien ungueltig (Sekunden)
	
	private $request = 'view';
	private $isStoreCategory = false;
        private $SiteID;


	public function __construct($request = 'view') {
		$this->request = $request;
                $this->SiteID = MLModul::gi()->getEbaySiteID();
	}

	
	
	private function geteBayCategories($ParentID = 0, $purge = false) {
		if ($purge) {
                    MLDatabase::factory('ebay_categories')->set('storecategory',0)->getList()->delete();
		}
                
                return MLDatabase::factory('ebay_categories')
                    ->set('storecategory',0)
                    ->set('parentid', $ParentID)
                    ->getList()
                    ->data()
                ;
	}

	private function geteBayStoreCategories($ParentID = 0, $purge = false) {
        if ($purge) {
            MLDatabase::factory('ebay_categories')->set('storecategory',1)->getList()->delete();
		}
        return MLDatabase::factory('ebay_categories')
                    ->set('storecategory',1)
                    ->set('parentid', $ParentID)
                    ->getList()
                    ->data()
                ;
	}

	private function rendereBayCategories($ParentID = 0, $purge = false) {
		#echo print_m(func_get_args(), __METHOD__);
		#echo var_dump_pre($this->isStoreCategory, '$this->isStoreCategory');
		if ($this->isStoreCategory) {
			$ebaySubCats = $this->geteBayStoreCategories($ParentID, $purge);
		} else {
			$ebaySubCats = $this->geteBayCategories($ParentID, $purge);
		}
		if ($ebaySubCats === false) {
			return '';
		}
		$ebayTopLevelList = '';
		foreach ($ebaySubCats as $item) {
			if (1 == $item['leafcategory']) {
				$class = 'leaf';
			} else {
				$class = 'plus';
			}
			$ebayTopLevelList .= '
				<div class="catelem" id="y_'.$item['categoryid'].'">
					<span class="toggle '.$class.'" id="y_toggle_'.$item['categoryid'].'">&nbsp;</span>
					<div class="catname" id="y_select_'.$item['categoryid'].'">
						<span class="catname">'.fixHTMLUTF8Entities($item['categoryname']).'</span>
					</div>
				</div>';
		}
		return $ebayTopLevelList;
	}
	
	# dummy
	private function renderShopCategories() {
		return '';
	}
	# Artikel-Auswahl anzeigen. Spaeter schauen ob wir das auch strukturiert mit Kategorien machen,
	# aber erschtmal flat.
	private function renderSelection() {
		$selection = $this->getSelection();
		if ($selection === false) {
			return '';
		}
		$itemList = '';
		foreach ($selection as $item) {
			$itemList .= '
				<div class="catelem" id="y_'.$item['SKU'].'">
					<span class="toggle leaf" id="y_toggle_'.$item['SKU'].'">&nbsp;</span>
					<div class="catname" id="y_select_'.$item['SKU'].'">
						<span class="catname">'.fixHTMLUTF8Entities($item['products_name'].' ('.$item['SKU'].')').'</span>
					</div>
				</div>';
		}
		return $itemList;
	
	}

	private function rendereBayCategoryItem($id) {
		return '
			<div id="yc_'.$id.'" class="ebayCategory">
				<div id="y_remove_'.$id.'" class="y_rm_handle">&nbsp;</div><div class="ycpath">'.geteBayCategoryPath($id, $this->isStoreCategory).'</div>
			</div>';
	}

        protected function getTryAgainBlock(){
            return json_encode('<div class="category_tryagain">'.MLI18n::gi()->get('ML_ERROR_LABEL_API_CONNECTION_PROBLEM').'</div>');
        }
        
	public function renderView() {
		$html = '
			<div id="ebayCategorySelector" class="dialog2" title="'.ML_EBAY_LABEL_SELECT_CATEGORY.'">
				<table id="catMatch"><tbody>
					<tr>
						<td id="ebayCats" class="catView"><div class="catView">'.$this->rendereBayCategories('').'</div></td>
					</tr>
					<!--<tr>
						<td id="selectedeBayCategory" class="catView"><div class="catView"></div></td>
					</tr>-->
				</tbody></table>
				<div id="messageDialog" class="dialog2"></div>
			</div>
		';
		ob_start();
                $sPostNeeded = '';
                foreach (MLHttp::gi()->getNeededFormFields() as $sKey => $sValue) {
                    $sPostNeeded .= "'$sKey' : '$sValue' ,";
                }
                $aMlHttp = MLHttp::gi();
?>
<script type="text/javascript">/*<![CDATA[*/
(function($){
    var selectedEBayCategory = '';
    var madeChanges = false;
    var isStoreCategory = false;

    function collapseAllNodes(elem) {
        $('div.catelem span.toggle:not(.leaf)', $(elem)).each(function() {
            $(this).removeClass('minus').addClass('plus');
            $(this).parent().children('div.catname').children('div.catelem').css({display: 'none'});
        });
        $('div.catname span.catname.selected', $(elem)).removeClass('selected').css({'font-weight':'normal'});
    }

    function resetEverything() {
        madeChanges = false;
        collapseAllNodes($('#ebayCats'));
        /* Expand Top-Node */
        $('#s_toggle_0').removeClass('plus').addClass('minus').parent().children('div.catname').children('div.catelem').css({display: 'block'});
        $('#selectedeBayCategory div.catView').empty();
        selectedEBayCategory = '';
    }

    function selectEBayCategory(yID, html) {
        madeChanges = true;
    	$('#selectedeBayCategory div.catView').html(html);

        selectedEBayCategory = yID;
        myConsole.log('selectedeBayCategory', selectedEBayCategory);

        //$('#ebayCats div.catname span.catname.selected').removeClass('selected').css({'font-weight':'normal'});
        $('#ebayCats div.catView').find('span.catname.selected').removeClass('selected').css({'font-weight':'normal'});
        $('#ebayCats div.catView').find('span.toggle.tick').removeClass('tick');
	
        $('#'+yID+' span.catname').addClass('selected').css({'font-weight':'bold'});
        $('#'+yID+' span.catname').parents().prevAll('span.catname').addClass('selected').css({'font-weight':'bold'});
        $('#'+yID+' span.catname').parents().prev('span.toggle').addClass('tick');
	
    }

    function clickEBayCategory(elem) {
        // hier Kategorien zuordnen, zu allen ausgewaehlten Items
        tmpNewID = $(elem).parent().attr('id');
        jqml.blockUI(blockUILoading);
        jqml.ajax({
            type: 'POST',
            url: '<?php echo $aMlHttp->getCurrentUrl( array('where' => 'prepareView', 'kind' => 'ajax'));?>',
            data: { 
                <?php echo $sPostNeeded ?>
                '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': 'primaryCategory',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'rendereBayCategoryItem',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[id]': tmpNewID,
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[isStoreCategory]': isStoreCategory
            },
            success: function(data) {
                try {
                    var oJson=$.parseJSON(data);
                    var data=oJson.plugin.content;
                    selectEBayCategory(tmpNewID, data);
                } catch(oExeception) {
                }
                jqml.unblockUI();
            },
            error: function() {
                jqml.unblockUI();
            },
            dataType: 'html'
        });
    }

    function addeBayCategoriesEventListener(elem) {
        $('div.catelem span.toggle:not(.leaf)', $(elem)).each(function() {
            $(this).click(function () {
                myConsole.log($(this).attr('id'));
                if ($(this).hasClass('plus')) {
                    tmpElem = $(this);				
                    if (tmpElem.parent().children('div.catname').children('div.catelem').length == 0) {
                        jqml.blockUI(blockUILoading);
                        jqml.ajax({
                            type: 'POST',
                            url: '<?php echo $aMlHttp->getCurrentUrl(array('where' => 'prepareView', 'kind' => 'ajax'));?>',
                            data: {
                                <?php echo $sPostNeeded ?>
                                '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': 'primaryCategory',
                                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'geteBayCategories',
                                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[objID]': tmpElem.attr('id'),
                                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[isStoreCategory]': isStoreCategory
                            },
                            success: function(data) {
                                try {
                                    var oJson = $.parseJSON(data);
                                    var data = oJson.plugin.content;
                                    if(data != '' && data != null && data != 'undefined') {
                                        tmpElem.removeClass('plus').addClass('minus');
                                    } else {
                                        data = <?php echo $this->getTryAgainBlock()?>;
                                    }
                                } catch(oExeception) {
                                    data = <?php echo $this->getTryAgainBlock()?>;
                                }
                                appendTo = tmpElem.parent().children('div.catname');
                                appendTo.find("div.category_tryagain").remove();
                                appendTo.append(data);
                                addeBayCategoriesEventListener(appendTo);
                                appendTo.children('div.catelem').css({display: 'block'});
                                jqml.unblockUI();
                            },
                            error: function() {
                                jqml.unblockUI();
                            },
                            dataType: 'html'
                        });
                    } else {
                        tmpElem.parent().children('div.catname').children('div.catelem').css({display: 'block'});
                    }
                } else {
                    $(this).removeClass('minus').addClass('plus');
                    $(this).parent().children('div.catname').children('div.catelem').css({display: 'none'});
                }
            });
        });	
        $('div.catelem span.toggle.leaf', $(elem)).each(function() {
            $(this).click(function () {
                clickEBayCategory($(this).parent().children('div.catname').children('span.catname'));
            });
            $(this).parent().children('div.catname').children('span.catname').each(function() {
                $(this).click(function () {
                    clickEBayCategory($(this));
                });
                if ($(this).parent().attr('id') == selectedEBayCategory) {
                    //$(this).addClass('selected').css({'font-weight':'bold'});	
                }
            });
        });
    }

    function returnCategoryID() {
        if (selectedEBayCategory == '') {
            $('#messageDialog').html(
                'Bitte w&auml;hlen Sie eine eBay-Kategorie aus.'
            ).jDialog({
                title: <?php echo json_encode(MLI18n::gi()->ML_LABEL_NOTE); ?>
            });
            return false;
        }
        cID = selectedEBayCategory;
        cID = str_replace('y_select_', '', cID);
        resetEverything();
        return cID;
    }

    function generateEbayCategoryPath(cID, viewElem) {
        cID = typeof cID === 'undefined' ? 0 : cID;
        viewElem.find('option').removeAttr('selected');
        if (viewElem.find('option[value="'+cID+'"]').length > 0) {
            viewElem.find('option[value="'+cID+'"]').attr('selected','selected');
            viewElem.find('select').blur();
            viewElem.find('select').trigger('change');
        } else {
            jqml.blockUI(blockUILoading);
            jqml.ajax({
                type: 'POST',
                url: '<?php echo $aMlHttp->getCurrentUrl( array('where' => 'prepareView', 'kind' => 'ajax'));?>',
                data: {
                    <?php echo $sPostNeeded ?>
                    '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': 'primaryCategory',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'geteBayCategoryPath',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[id]': cID,
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[isStoreCategory]': isStoreCategory
                },
                success: function(data) {
                    try {
                        var oJson = $.parseJSON(data);
                        var data = oJson.plugin.content;
                        viewElem.find('select').append('<option selected="selected" value="'+cID+'">'+data+'</option>');
                        viewElem.find('select').trigger('change');
                    } catch(oExeception) {
                    }
                jqml.unblockUI();
                },
                error: function() {
                    jqml.unblockUI();
                },
                dataType: 'html'
            });
        }
    }

    function VariationsEnabled(cID, viewElem) {
        viewElem.html('');
        if (cID != 0) {
            jqml.blockUI(blockUILoading);
            jqml.ajax({
                type: 'POST',
                url: '<?php echo $aMlHttp->getCurrentUrl( array('where' => 'prepareView', 'kind' => 'ajax'));?>',
                data: {
                    <?php echo $sPostNeeded ?>
                    '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': 'primaryCategory',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'VariationsEnabled',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[id]': cID
                },
                success: function(data) {
                    try{
                        var oJson = $.parseJSON(data);
                        var data = oJson.plugin.content;
                    } catch(oExeception) {
                    }
                    jqml.unblockUI();
                    var msg;
                    if(data == 'true') msg = <?php echo json_encode(MLI18n::gi()->ML_EBAY_NOTE_VARIATIONS_ENABLED) ?>;
                    else msg = <?php echo json_encode(MLI18n::gi()->ML_EBAY_NOTE_VARIATIONS_DISABLED) ?>;
                    viewElem.html(msg);
                },
                error: function() {
                    jqml.unblockUI();
                },
                dataType: 'html'
            });
        }
    }

    function initEBayCategories(purge) {
        purge = purge || false;
        myConsole.log('isStoreCategory', isStoreCategory);
        jqml.blockUI(blockUILoading);
        jqml.ajax({
            type: 'POST',
            url: '<?php echo $aMlHttp->getCurrentUrl( array('where' => 'prepareView', 'kind' => 'ajax'));?>',
            data: {
                <?php echo $sPostNeeded ?>
                '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': 'primaryCategory',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'geteBayCategories',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[objID]': '',
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[isStoreCategory]': isStoreCategory,
                '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[purge]': purge ? 'true' : 'false'
            },
            success: function(data) {
                try {
                    var oJson = $.parseJSON(data);
                    var data = oJson.plugin.content;
                    $('#ebayCats > div.catView').html(data);
                    addeBayCategoriesEventListener($('#ebayCats'));
                } catch(oExeception) {
                }
                jqml.unblockUI();
            },
            error: function() {
                jqml.unblockUI();
            },
            dataType: 'html'
        });
    }

    function startCategorySelector(callback, kind) {
        newStoreState = (kind == 'store');
        if (newStoreState != isStoreCategory) {
            isStoreCategory = newStoreState;
            $('#ebayCats > div.catView').html('');
            initEBayCategories();
        }

        $('#ebayCategorySelector').jDialog({
            width: '75%',
            minWidth: '300px',
            buttons: {
                <?php echo json_encode(MLI18n::gi()->ML_BUTTON_LABEL_ABORT); ?>: function() {
                    $(this).dialog('close');
                },
                <?php echo json_encode(MLI18n::gi()->ML_BUTTON_LABEL_OK); ?>: function() {
                    cID = returnCategoryID();
                    if (cID != false) {
                        callback(cID);
                        $(this).dialog('close');
                    }
                }
            },
            open: function(event, ui) {
                var tbar = $('#ebayCategorySelector').parent().find('.ui-dialog-titlebar');
                if (tbar.find('.ui-icon-arrowrefresh-1-n').length == 0) {
                    var rlBtn = $(
                        '<a class="ui-dialog-titlebar-close ui-corner-all ui-state-focus ml-js-noBlockUi" '+
                        'role="button" href="#" style="right: 2em; padding: 0px;">'+
			    		'<span class="ui-icon ui-icon-arrowrefresh-1-n">reload</span>'+
                        '</a>'
                    );
                    tbar.append(rlBtn);
                    rlBtn.click(function (event) {
                        event.preventDefault();
                        initEBayCategories(true);
    			    });
        		}
            }
        });
    }

    function getEBayCategoryAttributes(cID,  sSelector, sMethod) {
        $('#'+sSelector).find('tr').not('.headline,.spacer').remove();
        $('#'+sSelector+' .headline').after('');
        $('#'+sSelector).css({'display':'none'});
        if (cID != 0) {
            jqml.blockUI(blockUILoading);
            jqml.ajax({
                type: 'POST',
                url: '<?php echo $aMlHttp->getCurrentUrl( array('where' => 'prepareView', 'kind' => 'ajax'));?>',
                data: {
                    <?php echo $sPostNeeded ?>
                    '<?php echo MLHttp::gi()->parseFormFieldName('method') ?>': 'getField',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[method]': sMethod,
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[action]': 'getEBayAttributes',
                    '<?php echo MLHttp::gi()->parseFormFieldName('ajaxData') ?>[CategoryID]': cID,
                },
                success: function(data) {
                    try {
                        var oJson = $.parseJSON(data);
                        var data = oJson.plugin.content;
                    } catch(oExeception) {
                        data = <?php echo $this->getTryAgainBlock()?>;
                    }
                    $('#'+sSelector).find('tr').not('.headline,.spacer').remove();
                    $('#'+sSelector+' .headline').after(data+'');
                    if (data == '') {
                        $('#'+sSelector).css({'display':'none'});
                    } else {
                        $('#'+sSelector).css({'display':'table-row-group'});
                    }
                    jqml.unblockUI();
                },
                error: function() {
                    jqml.unblockUI();
                },
                dataType: 'html'
            });
        }
    }
    $(document).ready(function() {
        addeBayCategoriesEventListener($('#ebayCats'));
        $('.js-category-dialog').click(function(){
            var sField=$(this).attr('data-field');
            var blStore=$(this).attr('data-store');
            startCategorySelector(
                function(cID) {
                    $('#'+sField+'_visual').val(cID);
                    generateEbayCategoryPath(cID, $('#'+sField+'_visual'));
                }, 
                blStore ? 'store' : 'eBay'
            );
        });
        $('[data-field]').each(function(){
            var sField = $(this).attr('data-field');
            var cId = $(this).closest('tr').find('select').val();
            generateEbayCategoryPath(cId, $('#'+sField+'_visual'));
        });
        $('.js-category-clean').click(function(){
            generateEbayCategoryPath(0, $(this).closest('tr').find('.ebayCatVisual'));
        });

        $('.magna .magnalisterForm button.js-category-dialog').closest('tr').find('select').change(function () {
            var self = $(this);// select-element
            var button = self.closest('tr').find('button.js-category-dialog');// cat-popup-button
            if (button.data('variationsenabled')) {
                VariationsEnabled(self.val(), $('#noteVariationsEnabled'));
            }
            if (!button.data('store')) {
                getEBayCategoryAttributes(self.val(), button.data('field').replace('_field_','_fieldset_')+'_attributes', button.data('method'));
            }
        }).trigger('change');
            
    });
})(jqml);
/*]]>*/</script>
<?php
		$html .= ob_get_contents();	
		ob_end_clean();

		return $html;
	}
	
	public function renderAjax() {
            $aData= MLRequest::gi()->data('ajaxData');
		$id = '';
		if (isset($aData['id'])) {
			if (($pos = strrpos($aData['id'], '_')) !== false) {
				$id = substr($aData['id'], $pos+1);
			} else {
				$id = $aData['id'];
			}
		}
		$this->isStoreCategory = (array_key_exists('isStoreCategory', $aData))
			? (($aData['isStoreCategory'] == 'false')
				? false
				: true
			) : false;

		switch($aData['action']) {
			/*case 'getCategoryPath': {
				$_timer = microtime(true);
				$cID = (int)$id;
				$yIDs = MLDatabase::getDbInstance()->fetchArray('
					SELECT ebay_category_id 
					  FROM '.TABLE_MAGNA_EBAY_CATEGORYMATCHING.'
					 WHERE category_id=\''.$cID.'\'', true
				);
				$ebayCategories = array();
				if (!empty($yIDs)) {
					foreach ($yIDs as $yID) {
						$ebayCategories[] = array(
							'origID' => 'y_select_'.$yID,
							'html' => $this->rendereBayCategoryItem($yID)
						);
					}
				}
				$shopCatHtml = renderCategoryPath($cID);
				return json_encode(array(
					'shopCatHtml' => $shopCatHtml,
					'yCategories' => $ebayCategories,
					'timer' => microtime2human(microtime(true) -  $_timer)
				));
				break;
			}*/
			case 'geteBayCategories': {
				return $this->rendereBayCategories(
					empty($aData['objID'])
						? 0
						: str_replace('y_toggle_', '', $aData['objID']),
					isset($aData['purge']) ? $aData['purge'] : false
				);
				break;
			}
			#case 'getShopCategories': {
			#	return $this->renderShopCategories(str_replace('s_toggle_', '', $aData['cID']));
			#	break;
			#}
			# dummy
			case 'rendereBayCategoryItem': {
				return $this->rendereBayCategoryItem($id);
			}
			case 'geteBayCategoryPath': {
				return geteBayCategoryPath($id, $this->isStoreCategory);
			}
			case 'VariationsEnabled': {
				return VariationsEnabled($id)?'true':'false';
			}
			case 'saveCategoryMatching': {
				if (!isset($aData['selectedShopCategory']) || empty($aData['selectedShopCategory']) || 
					(isset($aData['selectedeBayCategories']) && !is_array($aData['selectedeBayCategories']))
				) {
					return json_encode(array(
						'debug' => var_dump_pre($aData['selectedeBayCategories'], true),
						'error' => preg_replace('/\s\s+/', ' ', ML_EBAY_ERROR_SAVING_INVALID_EBAY_CATS)
					));
				}
 
				$cID = str_replace('s_select_', '', $aData['selectedShopCategory']);
				if (!ctype_digit($cID)) {
					return json_encode(array(
						'debug' => var_dump_pre($cID, true),
						'error' => preg_replace('/\s\s+/', ' ', ML_EBAY_ERROR_SAVING_INVALID_SHOP_CAT)
					));
				}
				$cID = (int)$cID;
				
				if (isset($aData['selectedeBayCategories']) && !empty($aData['selectedeBayCategories'])) {
					$ebayIDs = array();
					foreach ($aData['selectedeBayCategories'] as $tmpYID) {
						$tmpYID = str_replace('y_select_', '', $tmpYID);
						if (preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{2}$/', $tmpYID)) {
							$ebayIDs[] = $tmpYID;
						}
					}
					if (empty($ebayIDs)) {
						return json_encode(array(
							'error' => preg_replace('/\s\s+/', ' ', ML_EBAY_ERROR_SAVING_INVALID_EBAY_CATS_ALL)
						));
					}
					#MLDatabase::getDbInstance()->delete(TABLE_MAGNA_EBAY_CATEGORYMATCHING, array (
					#	'category_id' => $cID
					#));
					#foreach ($ebayIDs as $yID) {
/*
	Hier muss stehen:
	fuer alle ausgewaehlten produkte:
	insert(TABLE_MAGNA_EBAY_PROPERTIES, ...)
	Wobei: Kategorie-Auswahl haben wir, ABER wir brauchen noch die Auswahl von dem ganzen anderen Zeug.
*/
					#	MLDatabase::getDbInstance()->insert(TABLE_MAGNA_EBAY_CATEGORYMATCHING, array (
					#		'category_id' => $cID,
					#		'ebay_category_id' => $yID
					#	));
					#}
				} else {
					#MLDatabase::getDbInstance()->delete(TABLE_MAGNA_EBAY_CATEGORYMATCHING, array (
					#	'category_id' => $cID
					#));
				}

				return json_encode(array(
					'error' => ''
				));

				break;
			}
			default: {
				return json_encode(array(
					'error' => ML_EBAY_ERROR_REQUEST_INVALID
				));
			}
		}
	}
	
	public function render() {
		if ($this->request == 'ajax') {
			return $this->renderAjax();
		} else {
			return $this->renderView();
		}
		
	}
}
