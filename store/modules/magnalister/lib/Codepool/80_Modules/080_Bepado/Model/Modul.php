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
class ML_Bepado_Model_Modul extends ML_Modul_Model_Modul_Abstract {
    
    protected $aPrice = array(
        'b2b' => null,
        'b2c' => null,
    );
    
    /**
     *
     * @var ML_Shop_Model_Price_Interface $oPrice 
     */
    protected $oPrice=null;
    
    public function getMarketPlaceName ($blIntern = true) {
        return $blIntern ? 'bepado' : MLI18n::gi()->get('sModuleNameBepado');
    }    

    public function getConfig($sName = null) {
        if ($sName == 'currency') {
            $mReturn = 'EUR';
        } elseif ($sName == 'marketplace.lang') {
            $mReturn = 'DE';
        } else {
            $mReturn = parent::getConfig($sName);
        }        
                
        if ($sName === null) {// merge
            $mReturn = MLHelper::getArrayInstance()->mergeDistinct($mReturn, array('marketplace.lang' => 'DE', 'currency' => 'EUR'));
        }

        return $mReturn;
    }   
    
    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     * @todo
     */
    protected function getConfigApiKeysTranslation() {
        $sDate = $this->getConfig('preimport.start');
        //magento tip to find empty date
        $sDate = (preg_replace('#[ 0:-]#', '', $sDate) ==='') ? date('Y-m-d') : $sDate;
        $sDate = date('Y-m-d', strtotime($sDate));
        $sSync = $this->getConfig('stocksync.tomarketplace');
        return array(
            'mpusername'  => array('api' => 'MPUsername',  'value' => $this->getConfig('mpusername') ),
            'mppassword'  => array('api' => 'MPPassword',  'value' => $this->getConfig('mppassword') ),
            'shopid'      => array('api' => 'ShopId',      'value' => $this->getConfig('shopid')     ),
            'apikey'      => array('api' => 'ApiKey',      'value' => $this->getConfig('apikey')     ),
            'ftpusername' => array('api' => 'FtpUsername', 'value' => $this->getConfig('ftpusername')),
            'ftppassword' => array('api' => 'FtpPassword', 'value' => $this->getConfig('ftppassword')),
            'import' => array('api' => 'Orders.Import', 'value' => ($this->getConfig('import') ? 'true' : 'false')),
            'preimport.start' => array('api' => 'Orders.Import.Start', 'value' => $sDate),
            'stocksync.tomarketplace' => array('api' => 'Callback.SyncInventory', 'value' => isset($sSync) ? $sSync : 'no'),
        );
    }
    
    /**
     * configures price-object possible values b2b
     * 
     * @param string $sType could be keys of $this->aPrice
     * @return ML_Shop_Model_Price_Interface
     */
    public function getPriceObject($sType = null) {
        $sType = $sType === null ? 'b2b' : $sType;
        $sType = strtolower($sType);
        if ($this->aPrice[$sType] === null) {
            $sKind = $this->getConfig($sType.'.price.addkind');
            $fFactor = (float)$this->getConfig($sType.'.price.factor');
            $iSignal = $this->getConfig($sType.'.price.signal');
            $iSignal = $iSignal === '' ? null : (int)$iSignal;
            $blSpecial = $this->getConfig($sType.'.price.usespecialoffer');
            $sGroup = $this->getConfig($sType.'.price.group');
            $this->aPrice[$sType] = MLPrice::factory()->setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial);
        }
        return $this->aPrice[$sType];
    }
}