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

/**
 * shortcut for handling helper correlating classes, also needed for secure refactoring
 */
class MLHelper {
    
    /**
     * Returns the instance of a helper class based on its name.
     * @param string $sHelper
     * @return Object
     */
    public static function gi($sHelper) {
        return ML::gi()->instance('helper_'.$sHelper);
    }
    
    /**
     * Returns the instance of the encoder helper class.
     * @return ML_Core_Helper_Encoder
     */
    public static function getEncoderInstance() {
        return ML::gi()->instance('helper_encoder');
    }
    
    /**
     * Returns the instance of the array helper class.
     * @return ML_Core_Helper_Array
     */
    public static function getArrayInstance() {
        return ML::gi()->instance('helper_array');
    }
    
    
    
    /**
     * Returns the instance of the array helper class.
     * @return ML_Core_Helper_Filesystem
     */
    public static function getFilesystemInstance() {
        return ML::gi()->instance('helper_filesystem');
    }
}