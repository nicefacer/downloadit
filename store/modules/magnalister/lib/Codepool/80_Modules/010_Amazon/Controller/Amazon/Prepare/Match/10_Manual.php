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
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Abstract');

class ML_Amazon_Controller_Amazon_Prepare_Match_Manual extends ML_Productlist_Controller_Widget_ProductList_Abstract {
    protected $blRenderVariants = true;
    protected $aParameters = array('controller');
    //protected $aParameters = array('mp', 'mode', 'view', 'execute');

    public function __construct() {
        parent::__construct();
        $aStatistic = $this->getProductList()->getStatistic();
        if ($aStatistic['iCountTotal'] == 0) {
            MLHttp::gi()->redirect($this->getParentUrl());
        }
    }

    public function renderAjax() {
        $sMethod = MLRequest::gi()->data('method');
        if ($sMethod !== null && method_exists($this, $sMethod.'_ajax')) {
            $this->{$sMethod.'_ajax'}();
        } else {
            echo json_encode(array('success' => false, 'error' => MLI18n::gi()->get('Productlist_Message_sErrorGeneral')));
        }
        exit();
    }

    protected function amazonItemsearch_ajax() {
        $oRequest = MLRequest::gi();
        $oProduct = MLProduct::factory()->set('id', $oRequest->data('id'));
        //print_r($oProduct->data());
        if (!in_array($oRequest->data('search'), array(null, ''))) {
            $sName = $oRequest->data('search');
            $sEan = null;
        } else {
            $sName = $oProduct->getName();
            $sEan = $oProduct->getModulField('general.ean', true);
        }
        $sContent = $this->includeViewBuffered(
            'widget_productlist_list_variantarticleadditional_amazon_itemsearch',
            array(
                'oProduct' => $oProduct,
                'aAdditional' => array('aAmazonResult' => MLModul::gi()->performItemSearch(null, $sEan, $sName))
            )
        );
        echo json_encode(array('success' => true, 'content' => $sContent));
    }

    public function update_ajax() {
        $aRequest = $this->getRequest();
        //        new dBug($aRequest);
        $aData = json_decode(str_replace("\\\"", "'", str_replace("'", '"', $aRequest['data'])), true);
        $oProduct = MLProduct::factory()->set('id', $aRequest['id'])->load();
        if (is_array($aData)) {//have amazon data
            MLHelper::gi('Model_Table_Amazon_Prepare_Product')->manual($oProduct, array_merge($aRequest['amazonProperties'], array('aidentid' => $aData['ASIN'], 'lowestprice' => $aData['LowestPrice'])))->getTableModel()->save();
        }
        echo json_encode(array('success' => true));
        MLDatabase::factory('selection')->set('selectionname', 'match')->set('pid', $oProduct->get('id'))->delete();
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }

    public function isSingleMatching() {
        return count($this->getProductList()->getMasterIds()) == 1;
    }
    
    public function getCurrentProduct(){
        $oTable = MLDatabase::factory('selection')->set('selectionname', 'match');
        $oSelectList = $oTable->getList();
        /* @var $oSelectList ML_Database_Model_List */
        $aResult = $oSelectList->getQueryObject()->init('aSelect')->select('parentid')->join(array('magnalister_products','p','pid = p.id'))->getResult();
        $sParent = null;
        foreach ($aResult as $aRow){//show shippingtime , condition and ... if it is single preparation
            if($sParent !== null && $sParent != $aRow['parentid']){
                return null;
            }
            $sParent = $aRow['parentid'];
        }
        $aList = $oTable->getList()->getList();
        $oProduct = MLProduct::factory()->set('id', current($aList)->get('pid'));
        $aPreparedData = MLHelper::gi('Model_Service_Product')->addVariant($oProduct)->getData();
        return current($aPreparedData);
    }
    
}