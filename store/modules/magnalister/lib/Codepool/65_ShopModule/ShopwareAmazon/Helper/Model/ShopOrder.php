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


MLFilesystem::gi()->loadClass('Shopware_Helper_Model_ShopOrder');
class ML_ShopwareAmazon_Helper_Model_ShopOrder extends ML_Shopware_Helper_Model_ShopOrder {
    
    protected function getDispatch() {
        if (MLModul::gi()->getConfig('orderimport.shippingmethod') == '__automatic__'){
            $aData = $this->aNewData;
            if (
                isset($aData['AddressSets']['Shipping'])
                & isset($aData['AddressSets']['Shipping']['CountryCode'])
            ) {
                $sIso = MLDatabase::getDbInstance()->escape(strtoupper($aData['AddressSets']['Shipping']['CountryCode']));
                $iDispatch = MLDatabase::getDbInstance()->fetchOne("
                    SELECT d.id
                    FROM ".Shopware()->Models()->getClassMetadata('\Shopware\Models\Dispatch\Dispatch')->getTableName()." d
                    INNER JOIN ".Shopware()->Models()->getClassMetadata('\Shopware\Models\Dispatch\Dispatch')->getTableName()."_countries dc on d.id=dc.dispatchid
                    INNER JOIN ".Shopware()->Models()->getClassMetadata('\Shopware\Models\Country\Country')->getTableName()." c on dc.countryid=c.id 
                    WHERE c.countryiso='".$sIso."'
                        and d.type in(0, 1)
                    ORDER BY d.active DESC, "./*active first*/
                    "   d.type ASC, "./*standard first*/
                    "   d.position ASC
                    LIMIT 1
                ");
                if (!empty($iDispatch)) {
                    return $iDispatch;
                } else {
                    foreach ($this->aNewData['Totals'] as $iTotal => $aTotal ) {//create shipping for missed country
                        if ($aTotal['Type'] == 'Shipping') {
                            $this->aNewData['Totals'][$iTotal]['Code'] = $sIso;
                        }
                    }
                }
            }
        }
        return parent::getDispatch();

    }
    
}