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
 * $Id: apply.php 6612 2016-04-07 11:57:11Z masoud.khodaparast $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'amazon/amazonFunctions.php');

/* see ML_Amazon_Helper_Model_Table_Amazon_Prepare_Product::amazonSanitizeDescription($sDescription) 
function amazonSanitizeDesc($str) {
	$str = str_replace(array('&nbsp;', html_entity_decode('&nbsp;')), ' ', $str);	
        $str = sanitizeProductDescription(
		$str, 
		'<ul><ol><li><u><b><i><p><big><small><h1><h2><h3><h4><h5><h6><span>'.
		'<hr><strike><s><br><strong><em><i>',
		'_keep_all_'
	);
	$str = str_replace(array('<br />', '<br/>'), '<br>', $str);
	$str = preg_replace('/(\s*<br[^>]*>\s*)*$/', '', $str);
	$str = preg_replace('/\s\s+/', ' ', $str);
	return substr($str, 0, 2000);
}
 */

function populateGenericData($pID, $edit = false) {
        /* @var $oPrepare ML_Amazon_Model_Table_Amazon_Prepare */
        if($edit){
            $oPrepare=MLDatabase::factory('amazon_prepare')->set('productsid',$pID)->load();
            if($oPrepare->get('PrepareType')!='apply'){
                $oProduct=MLProduct::factory()->set('id',$pID);
                $oPrepare=  MLHelper::gi('Model_Table_Amazon_Prepare_Product')->apply($oProduct)->getTableModel();
            }
        }else{
            $oProduct=MLProduct::factory()->set('id',$pID);
            $oPrepare=  MLHelper::gi('Model_Table_Amazon_Prepare_Product')->apply($oProduct)->getTableModel();
        }
        $aData=$oPrepare->data(false);
        $genericDataStructure=$aData['applydata'];
        $genericDataStructure['MainCategory']=$aData['maincategory'];
        $genericDataStructure['LeadtimeToShip']=$aData['leadtimetoship'];
        $genericDataStructure['ConditionNote']=$aData['conditionnote'];
        $genericDataStructure['ConditionType']=$aData['conditiontype'];
        if (isset($genericDataStructure['Images']) && !empty($pID)) {
            try { //perhaps images have changed?
                $aImages = array();
                $oProduct = MLProduct::factory();
                $oProduct->set('id', $pID);
                if($oProduct->get('parentid') != 0){
                    $oProduct = $oProduct->getParent();
                }
                foreach ($oProduct->getAllImages() as $sImagePath) {
                    if (array_key_exists($sImagePath, $genericDataStructure['Images'])) {
                        $aImages[$sImagePath] = $genericDataStructure['Images'][$sImagePath];
                    } else {
                        $aImages[$sImagePath] = false;
                    }
                }
                $genericDataStructure['Images'] = $aImages;
            } catch (Exception $oEx) {
                $oProduct = null;
            }
        }
        
        $iMagnalisterProductsId = $oProduct === null ? null : $oProduct->get('id');
        $aProductData = $oProduct === null ? null : $oProduct->data();
        foreach ($genericDataStructure as $sKey => &$mValue) {
            amazon_hookPrepareField($sKey, $mValue, $iMagnalisterProductsId, $aProductData);
        }
	/* {Hook} "AmazonApply_populateGenericData": Enables you to extend or modifiy the product data.<br>
	   Variables that can be used: 
	   <ul><li>$pID: The ID of the product (Table <code>products.products_id</code>).</li>
		   <li>$product: The data of the product (Tables <code>products</code>, <code>products_description</code>,
			           <code>products_images</code> and <code>products_vpe</code>).</li>
		   <li>$genericDataStructure: The additional recommenced data of the product for Amazon (MainCategory, ProductType, BrowseNodes, ItemTitle, Manufacture, Brand, ManufacturerPartNumber, EAN, Images, BulletPoints, Description, Keywords, Attributes, LeadtimeToShip)</li>
	   </ul>
	 */
	if (($hp = magnaContribVerify('AmazonApply_populateGenericData', 1)) !== false) {
		require($hp);
	}
	
	//echo print_m($genericDataStructure);
	return $genericDataStructure;
}


/**
 * making preparefield hook work for old amazon form
 */
function amazon_hookPrepareField($sKey, &$mValue, $iMagnalisterProductsId, $aProductData) {
    if (($sHook = MLFilesystem::gi()->findhook('preparefield', 1)) !== false) {
        $iMarketplaceId = MLModul::gi()->getMarketPlaceId();
        $sMarketplaceName = MLModul::gi()->getMarketPlaceName();
        require $sHook;
    }
}
$_url['view'] = 'apply';
$applySetting = array(
	'selectionName' => 'apply'
);

$applyAction = 'categoryview';

$aRequest=  MLRequest::gi()->data();

if (!empty($_POST) && isset($aRequest['kind']) && ($aRequest['kind'] == 'ajax')) {
	if (isset($aRequest['applyAction'])) {
		$applyAction = $aRequest['applyAction'];
	}
}

/**
 * Beantragen Vorbereitung
 */
    $itemCount = (int)  MLDatabase::getDbInstance()->fetchOne(eecho('
		SELECT count(distinct p.parentid) FROM '.TABLE_MAGNA_SELECTION.' s
        INNER JOIN magnalister_products p on s.pID = p.id
		 WHERE mpID=\''.MLModul::gi()->getMarketPlaceId().'\' AND
		       selectionname=\''.$applySetting['selectionName'].'\' AND
		       session_id=\''.MLShop::gi()->getSessionId().'\'
	  GROUP BY selectionname
	', false));
	if ($itemCount == 1) {
		$applyAction = 'singleapplication';
	} else if ($itemCount > 1) {
		$applyAction = 'multiapplication';
	}
        
if (($applyAction == 'singleapplication') || ($applyAction == 'multiapplication')) {
	include_once(DIR_MAGNALISTER_MODULES.'amazon/application/applicationviews.php');

}
