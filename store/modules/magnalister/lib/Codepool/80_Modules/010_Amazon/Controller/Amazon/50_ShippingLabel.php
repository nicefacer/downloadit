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

MLFilesystem::gi()->loadClass('Tabs_Controller_Widget_Tabs_Filesystem_Abstract');
class ML_Amazon_Controller_Amazon_ShippingLabel extends ML_Tabs_Controller_Widget_Tabs_Filesystem_Abstract {

    
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_Amazon_Shippinglabel');
    }
    
    public static function getTabVisibility () {
        try{
            MLSetting::gi()->get('ml_amazon_shippinglabel_testphase');
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    
    public static function getTabActive() {
        return self::getTabVisibility() && MLModul::gi()->isConfigured();
    }
}