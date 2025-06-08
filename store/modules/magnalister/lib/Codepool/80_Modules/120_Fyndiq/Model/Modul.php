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
class ML_Fyndiq_Model_Modul extends ML_Modul_Model_Modul_Abstract
{

    /**
     * @var ML_Shop_Model_Price_Interface $oPrice
     */
    protected $oPrice = null;

    public function getMarketPlaceName($blIntern = true)
    {
        return $blIntern ? 'fyndiq' : MLI18n::gi()->get('sModuleNameFyndiq');
    }

    public function getOrderLogo(ML_Shop_Model_Order_Abstract $oOrder)
    {
        return 'fyndiq_orderview.png';
    }

    public function getConfig($sName = null)
    {
        $mReturn = parent::getConfig($sName);

        if ($sName === null) {// merge
            $mReturn = MLHelper::getArrayInstance()->mergeDistinct($mReturn, array(
                'lang' => parent::getConfig('lang'),
                'currency' => 'EUR'
            ));
        }

        return $mReturn;
    }

    public function getPriceObject($sType = null)
    {
        if ($this->oPrice === null) {
            $sKind = $this->getConfig('price.addkind');
            $fFactor = (float)$this->getConfig('price.factor');
            $iSignal = $this->getConfig('price.signal');
            $iSignal = $iSignal === '' ? null : (int)$iSignal;
            $blSpecial = $this->getConfig('price.usespecialoffer');
            $sGroup = $this->getConfig('price.group');
            $this->oPrice = MLPrice::factory()->setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial);
        }
        return $this->oPrice;
    }

    public function getStockConfig()
    {
        return array(
            'type' => $this->getConfig('quantity.type'),
            'value' => $this->getConfig('quantity.value')
        );
    }

    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     */
    protected function getConfigApiKeysTranslation()
    {
        $aConfig = $this->getConfig();
        $sDate = $aConfig['preimport.start'];
        //magento tip to find empty date
        $sDate = (preg_replace('#[ 0:-]#', '', $sDate) === '') ? date('Y-m-d') : $sDate;
        $sDate = date('Y-m-d', strtotime($sDate));
        $sSync = $this->getConfig('stocksync.tomarketplace');
        return array(
            'import' => array('api' => 'Orders.Import', 'value' => ($this->getConfig('import') ? 'true' : 'false')),
            'preimport.start' => array('api' => 'Orders.Import.Start', 'value' => $sDate),
            'stocksync.tomarketplace' => array('api' => 'Callback.SyncInventory', 'value' => isset($sSync) ? $sSync : 'no'),
        );
    }

}
