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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

MLFilesystem::gi()->loadClass('ZzzzDummy_Model_ProductList_Abstract');
class ML_ZzzzDummyDawanda_Model_ProductList_Dawanda_Prepare extends ML_ZzzzDummy_Model_ProductList_Abstract {
    
    protected function getZzzzDummyConfig($sType) {
        if ($sType == 'filter') {
            $aMyConfig = array(
                'preparedstatus' => MLProductList::dependencyInstance('preparestatusfilter')->setConfig(array()),
            );
        } else {
            $aMyConfig = array(
                'priceMarketplace' => array(
                    'title' =>  sprintf(MLI18n::gi()->get('Productlist_Header_sPriceMarketplace'), MLModul::gi()->getMarketPlaceName(false)),
                    'order' => false,
                    'type' => 'priceMarketplace'
                ),
                'preparedstatus' => array(
                    'title' => MLI18n::gi()->get('Productlist_Header_sPreparedStatus'),
                    'type' => 'preparedstatus',
                ),
            );
        }
        return array_merge(parent::getZzzzDummyConfig($sType), $aMyConfig);
    }
    
    public function getSelectionName() {
        return 'match';
    }
    
}