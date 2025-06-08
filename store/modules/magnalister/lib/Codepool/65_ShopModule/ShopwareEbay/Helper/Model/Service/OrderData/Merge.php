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
 * $Id: EbayImportOrders.php 167 2013-02-08 12:00:00Z tim.neumann $
 *
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Ebay_Helper_Model_Service_OrderData_Merge');

class ML_ShopwareEbay_Helper_Model_Service_OrderData_Merge extends ML_Ebay_Helper_Model_Service_OrderData_Merge {
    
    /**
     * new value
     * @return array
     */
    protected function mergeOrder() {
        $aNew = parent::mergeOrder();         
        if ($aNew['Payed']) {
            $aNew['PaymentStatus'] = MLModul::gi()->getConfig('paymentstatus.paid');
        } elseif(MLModul::gi()->getConfig('orderimport.paymentstatus') !== null) {
            $aNew['PaymentStatus'] = MLModul::gi()->getConfig('orderimport.paymentstatus');
        } else {
            $aNew['PaymentStatus'] = 17;//deprecated code , just use for user who configured ebay before
        }
        return $aNew;
    }

}