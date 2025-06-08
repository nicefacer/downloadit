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
MLFilesystem::gi()->loadClass('Core_Update_Abstract');
/**
 * check if need to clean cache of shopware
 */
class ML_Shopware_Update_ShopwareCheckClearCache extends ML_Core_Update_Abstract {
    
 
    /**
     * check, if update is needed
     * @return boolean
     */
    public function needExecution() {
        try{
            return (strpos(Shopware()->Template()->fetch("backend/order/model/order.js"),'magnalisterlogo')=== false) ;
        }  catch (Exception $oExc){
            MLMessage::gi()->addDebug($oExc);
            return false;
        }
    }
    
    /**
     * show a meesage to customer when cleaning shopware cache needed
     * @return \ML_Shopware_Update_ShopwareCheckClearCache
     */
    public function execute() {
        MLMessage::gi()->addInfo(MLI18n::gi()->Shopware_Update_ClearShopwareCache,'',false);
        return $this;
    }
    
}
