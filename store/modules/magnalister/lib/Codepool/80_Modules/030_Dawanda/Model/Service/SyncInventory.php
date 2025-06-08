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

class ML_Dawanda_Model_Service_SyncInventory extends ML_Modul_Model_Service_SyncInventory_Abstract {
    /**
     * 
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @param array $aResponse api-response of current product
     * return array() for request eg. array('Quantity' => (int))
     */
    protected function getStock(ML_Shop_Model_Product_Abstract $oProduct, $aMpItem) {
        $iShopQuantity = $oProduct->getSuggestedMarketplaceStock(
                MLModul::gi()->getConfig('quantity.type'), 
                MLModul::gi()->getConfig('quantity.value'), 
                MLModul::gi()->getConfig('maxquantity')//only ebay (... till now)
        );
        /*
         * dawanda quantity limitation is 1000 
         */
        if ($iShopQuantity >= 1000) {
            $iShopQuantity = 1000;
        }
        return array(
            'Quantity' => $iShopQuantity
        );
    }

}
