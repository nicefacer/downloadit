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

MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');
class ML_Ebay_Controller_Ebay_Prepare extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');
//  protected $aParameters = array('mp', 'mode');
        
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_PREPARE');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    public static function getTabDefault() {
        return true;
    }
    
    public function __construct() {
        parent::__construct();
        try {
            $mExecute = $this->oRequest->get('view');
            if ($mExecute == 'unprepare') {
                $oModel = MLDatabase::factory('ebay_prepare');
                $oList = MLDatabase::factory('selection')->set('selectionname','match')->getList();
                foreach ($oList->get('pid') as $iPid) {
                    $oModel->init()->set('products_id', $iPid)->delete();
                }
            } elseif (
                is_array($mExecute) 
                && !empty($mExecute)
                && (
                    in_array('reset_title', $mExecute)
                    ||
                    in_array('reset_subtitle', $mExecute)
                    ||
                    in_array('reset_description', $mExecute)
                    ||
                    in_array('reset_pictures', $mExecute)                    
                )
            ) {
                $oModel = MLDatabase::factory('ebay_prepare');
                $oList = MLDatabase::factory('selection')->set('selectionname','match')->getList();
                foreach ($oList->get('pid') as $iPid) {
                    $oModel->init()->set('products_id', $iPid);
                    if (in_array('reset_title', $mExecute)) {
                        $oModel->set('title', null);
                    }
                    if (in_array('reset_subtitle', $mExecute)) {
                        $oModel->set('subtitle', null);
                    }
                    if (in_array('reset_description', $mExecute)) {
                        $oModel->set('description', null);
                    }
                    if (in_array('reset_pictures', $mExecute)) {
                        $oModel->set('pictureurl', null)->set('variationpictures', null);
                    }
                    $oModel->save();
                }
            }
            
            if(in_array($mExecute, array('unprepare', 'resetdescription'))){
                $oModel=  MLDatabase::factory('ebay_prepare');
                $oList=MLDatabase::factory('selection')->set('selectionname','match')->getList();
                foreach($oList->get('pid') as $iPid){
                        $oModel->init()->set('products_id',$iPid);
                        switch($mExecute){
                            case 'unprepare':{//delete from ebay_prepare
                                $oModel->delete();
                                break;
                            }
                            case 'resetvalues':{//set products description of ebay_prepare to actual product-description
                                if($oModel->exists() && $oModel->get('description')!==null){
                                    $oModel
                                        ->set('description', null)
                                        ->set('PictureURL', null)
                                        ->set('VariationPictures', null)
                                        ->save();
                                }
                                break;
                            }
                        }
                }
            }
        } catch(Exception $oEx) {
//            echo $oEx->getMessage();
        }
    }
    
 
    public function getProductListWidget() {
        try{
            if ($this->isCurrentController()) {
                throw new Exception;
            }
            return $this->getChildController('form')->render();
        }  catch (Exception $oExc){
            return parent::getProductListWidget();
        }
    }
    
    /**
     * @thows Exception dont need in this view, shows only prepared value
     */
    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        throw new Exception('price config can not loaded yet.');
    }    
    public function productSelectable(ML_Shop_Model_Product_Abstract $oProduct, $blRender) {
        return !$blRender||$oProduct->get('parentid') == 0;
    }
}
