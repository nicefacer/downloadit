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
class ML_Ebay_Helper_Model_Form_Type_Attribute {

    public function fillValue($oProduct, $sLable) {
        $isMultiProduct = !($oProduct instanceof ML_Shop_Model_Product_Abstract);
        $mValue = '';
        if (MLShop::gi()->addonBooked('EbayProductIdentifierSync') && MLModul::gi()->getConfig('syncproperties')) {
            if (in_array($sLable, array('Marke', 'Hersteller', 'Brand')) && MLModul::gi()->getConfig('productfield.brand') !== null) {
                $mValue = $isMultiProduct ? '(matching)' : $oProduct->getModulField('productfield.brand');
            } elseif (in_array($sLable, array('Herstellernummer', 'MPN')) && MLModul::gi()->getConfig('manufacturerpartnumber') !== null) {
                $mValue = $isMultiProduct ? '(matching)' : $oProduct->getModulField('general.manufacturerpartnumber', true);
            } elseif (in_array($sLable, array('EAN', 'ISBN', 'UPC')) && MLModul::gi()->getConfig('ean') !== null) {
                $mValue = $isMultiProduct ? '(matching)' : $oProduct->getModulField('general.ean', true);
            }
        }
        return $mValue;
    }

}
