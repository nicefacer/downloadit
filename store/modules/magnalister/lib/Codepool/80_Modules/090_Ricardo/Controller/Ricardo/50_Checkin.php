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

class ML_Ricardo_Controller_Ricardo_Checkin extends ML_Productlist_Controller_Widget_ProductList_Selection {

    protected $aParameters = array('controller');

    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_CHECKIN');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }

    public static function getTabDefault() {
        return true;
    }

    public function getProductListWidget() {
        if ($this->isCurrentController()) {
            if (count($this->getProductList()->getMasterIds(true))==0) {//only check current page
                MLMessage::gi()->addInfo($this->__('ML_RICARDO_TEXT_NO_MATCHED_PRODUCTS'));
            }
            return parent::getProductListWidget();
        } else {
            return $this->getChildController('summary')->render();
        }
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct) {
        return MLModul::gi()->getPriceObject();
    }
}
