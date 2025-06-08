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

MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Tools_Controller_Main_Tools_Products_Search extends ML_Core_Controller_Abstract {
    
    protected $aParameters = array('controller');
    public function __construct() {
        parent::__construct();
        $iRequestMp = $this->getRequestedMpid();
        if($iRequestMp != null) {
            ML::gi()->init(array('mp' => $iRequestMp));
            if (!MLModul::gi()->isConfigured()) {
                throw new Exception('module is not configured');
            }
        }
    }

    protected function getRequestedSku() {
        return $this->getRequest('sku');
    }
    
    protected function getRequestedMpid() {
        return $this->getRequest('mpid');
    }
    
    protected function getProduct($blMaster) {
        $sSku = $this->getRequestedSku();
        if (!empty($sSku)) {
            return MLProduct::factory()->getByMarketplaceSKU($sSku, $blMaster);
        }
    }
}
