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

class ML_Magento_Update_SetConfigLangAsShop extends ML_Core_Update_Abstract {

    /**
     * fills config table (orderimport.shop) with same values like values from config.lang (if not setted)
     */
    public function execute() {
        if (MLDatabase::getDbInstance()->tableExists('magnalister_config')) {
            MLDatabase::getDbInstance()->query("
                INSERT INTO magnalister_config (mpid, mkey, value)
                SELECT mpid, 'orderimport.shop', value FROM magnalister_config lang 
                WHERE lang.mkey='lang'
                AND (
                    SELECT COUNT(*)=0 
                    FROM magnalister_config shop 
                    WHERE shop.mkey='orderimport.shop' 
                    AND shop.mpid=lang.mpid)
                    GROUP BY lang.mpid
            ");
        }
        return parent::execute();
    }

}
