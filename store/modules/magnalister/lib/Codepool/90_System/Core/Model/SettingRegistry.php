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
 * alias for special-vars of mlsetting
 * 
 * @todo same like aJs for aCss, aAjax, aAjaxPluginDom
 */
class ML_Core_Model_SettingRegistry {
    
    /**
     * add a js-script to registry
     * @param string $mScript scriptname
     * @param array $mScript scriptnames
     * @return $this
     */
    public function addJs($mScript, $blOverwrite = true) {
        $mScript = is_string($mScript) ? array($mScript) : $mScript;
        foreach ($mScript as $i => $sScript) {
            $mScript[$i] = $sScript.'?%s';
        }
        MLSetting::gi()->add('aJs', $mScript, $blOverwrite);
        return $this;
    }
    
    /**
     * get registered js-scripts
     * @return array
     */
    public function getJs() {
        return array_unique(MLSetting::gi()->get('aJs'));
    }
    
}
