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
class ML_Ricardo_Helper_Model_Service_Product {

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Ricardo_Model_Table_Ricardo_Prepare $oPrepare  */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct  */
    protected $oProduct = null;
    protected $aData = null;
    protected $aListingLangs = null;
    protected $aLangs = null;

    public function __call($sName, $mValue) {
        return $sName . '()';
    }

    public function __construct() {
        $this->oPrepare = MLDatabase::factory('ricardo_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }

    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct) {
        $this->oVariant = $oProduct;
        return $this;
    }

    public function resetData() {
        $this->aData = null;
        $this->aLangs = MLModul::gi()->getConfig('langs');
        return $this;
    }

    public function getData() {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id', $this->oVariant->get('id'));
            $this->aListingLangs = $this->oPrepare->get('ListingLangs');
            $aData = array();
            $aFields = array(
                'SKU',
                'Descriptions',
                'Images',
                'Quantity',
                'WarrantyDescription',
                'Price',
                'MarketplaceCategories',
                'ListingType',
                'Auction',
                'ConditionType',
                'DescriptionTemplate',
                'DeliveryDescription',
                'MaxRelistCount',
                'StartTime',
                'EndTime',
                'ListingDuration',
                'PaymentMethods',
                'PaymentDescription',
                'Promotions',
                'ShippingTime',
                'ShippingServices'
            );

            foreach ($aFields as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
//                        foreach ($mValue as $sKey => $mCurrentValue) {
//                            if (empty($mCurrentValue)) {
//                                unset ($mValue[$sKey]);
//                            }
//                        }
                        $mValue = empty($mValue) ? null : $mValue;
                    }
                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function  ML_Ricardo_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
                }
            }
            if (empty($aData['BasePrice'])) {
                unset($aData['BasePrice']);
            }
            $this->aData = $aData;
        }
        return $this->aData;
    }

    protected function getSKU() {
        return $this->oVariant->getMarketPlaceSku();
    }

    protected function getDescriptions() {
        $aDescriptions = array();
        foreach ($this->aListingLangs as $sLang => $sIsEnabled) {
            if ($sIsEnabled === 'true') {
                $sTitle = $this->oPrepare->get(ucfirst($sLang) . 'Title');
                if (empty($sTitle) === false && $sTitle !== '') {
                    $aDescriptions[strtoupper($sLang)]['Title'] = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
                } else {
                    $sValue = MLModul::gi()->getConfig('template.name');
                    if (!isset($sValue) || $sValue === '') {
                        $sValue = '#TITLE#';
                    }

                    $aLangs = MLModul::gi()->getConfig('langs');
                    $this->oVariant->setLang($aLangs[$sLang]);
                    $sBasePrice = $this->getPrice();
                    $aReplace = array(
                        '#TITLE#' => $this->oVariant->getName(),
                        '#ARTNR#' => $this->oVariant->getMarketPlaceSku(),
                        '#PID#' => $this->oVariant->get('marketplaceidentid'),
                        '#BASEPRICE#' => $sBasePrice,
                    );

                    $sTitle = str_replace(array_keys($aReplace), array_values($aReplace), $sValue);
                    $aDescriptions[strtoupper($sLang)]['Title'] = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
                }

                $sDescription = $this->oPrepare->get(ucfirst($sLang) . 'Description');
                if (empty($sDescription) === false && $sDescription !== '') {
                    $aDescriptions[strtoupper($sLang)]['Description'] = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
                } else {
                    $sValue = MLModul::gi()->getConfig('template.content');
                    if (!isset($sValue) || $sValue === '') {
                        $sValue = '<p>#TITLE#</p>
                            <p>#ARTNR#</p>
                            <p>#SHORTDESCRIPTION#</p>
                            <p>#PICTURE1#</p>
                            <p>#PICTURE2#</p>
                            <p>#PICTURE3#</p>
                            <p>#DESCRIPTION#</p>';
                    }

                    $aLangs = MLModul::gi()->getConfig('langs');
                    $this->oVariant->setLang($aLangs[$sLang]);
                    $oProduct = $this->oVariant;
                    $aReplace = $oProduct->getReplaceProperty();
                    $sValue = str_replace(array_keys($aReplace), array_values($aReplace), $sValue);
                    $iSize = $this->getImageSize();
                    //images
                    $iImageIndex = 1;
                    foreach ($oProduct->getImages() as $sPath) {
                        try {
                            $aImage = MLImage::gi()->resizeImage($sPath, 'products', $iSize, $iSize);
                            $sValue = str_replace(
                                    '#PICTURE' . (string) ($iImageIndex) . '#', "<img src=\"" . $aImage['url'] . "\" style=\"border:0;\" alt=\"\" title=\"\" />", preg_replace('/(src|SRC|href|HREF|rev|REV)(\s*=\s*)(\'|")(#PICTURE' . (string) ($iImageIndex) . '#)/', '\1\2\3' . $aImage['url'], $sValue)
                            );
                            $iImageIndex ++;
                        } catch (Exception $oEx) {
                            //no image in fs
                        }
                    }
                    // delete not replaced #PICTUREx#  
                    $sValue = preg_replace(
                            '/#PICTURE\d+#/', '', preg_replace('/<[^<]*(src|SRC|href|HREF|rev|REV)\s*=\s*(\'|")#PICTURE\d+#(\'|")[^>]*\/*>/', '', $sValue)
                    );
                    // delete empty images
                    $sValue = preg_replace('/<img[^>]*src=(""|\'\')[^>]*>/i', '', $sValue);
                    $aDescriptions[strtoupper($sLang)]['Description'] = html_entity_decode(fixHTMLUTF8Entities($sValue), ENT_COMPAT, 'UTF-8');
                }

                $aDescriptions[strtoupper($sLang)]['Subtitle'] = $this->oPrepare->get(ucfirst($sLang) . 'Subtitle');
            }
        }

        return $aDescriptions;
    }

    protected function getImages() {
        $aImagesPrepare = $this->oPrepare->get('Images');
        $aOut = array();
        if (empty($aImagesPrepare) === false) {
            $aImages = $this->oVariant->getImages();
            $aImages = empty($aImages) && $this->oVariant->get('parentid') != 0 && $this->oVariant->getParent()->getVariantCount() == 1 // if we have just one variant and if variant doesn't have any picture 
                    ? $this->oVariant->getParent()->getImages() : $aImages;
            foreach ($aImages as $sImage) {
                $sImageName = basename(str_replace('\\', '/', $sImage));
                if (in_array($sImageName, $aImagesPrepare) === false) {
                    continue;
                }

                try {
                    $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                    $aOut[] = array('URL' => $aImage['url']);
                } catch (Exception $ex) {
                    // Happens if image doesn't exist.
                }
            }
        }

        return $aOut;
    }

    protected function getQuantity() {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
                MLModul::gi()->getConfig('quantity.type'), MLModul::gi()->getConfig('quantity.value')
        );
        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getWarrantyDescription() {
        $aWarrantyDescriptions = $this->oPrepare->get('WarrantyDescription');
        $aWarrantyCondition = $this->oPrepare->get('WarrantyCondition');

        if (empty($aWarrantyDescriptions) === true || $aWarrantyCondition !== '0') {
            return null;
        }

        $aWarrantyDescriptionsToReturn = array();

        foreach ($aWarrantyDescriptions as $sLang => $sWarrantyDescription) {
            if ($this->aListingLangs[$sLang] === 'true' && empty($sWarrantyDescription) === false) {
                $aWarrantyDescriptionsToReturn[strtoupper($sLang)] = $sWarrantyDescription;
            }
        }

        return $aWarrantyDescriptionsToReturn;
    }

    protected function getPrice() {
        $sRicardoPrice = $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        $sPrice = $this->oPrepare->get('FixPrice');
        $bEnable = $this->oPrepare->get('EnableBuyNowPrice');
        if ($this->oPrepare->get('BuyingMode') === 'buy_it_now') {
            if (empty($sPrice) === false && (float) $sPrice > 0) {
                return $sPrice;
            } else {
                return $sRicardoPrice;
            }
        } else {
            if ($bEnable === '0') {
                return null;
            }

            if (empty($sPrice) === false && (float) $sPrice > 0) {
                return $sPrice;
            } else {
                return $sRicardoPrice;
            }
        }
    }

    protected function getMarketplaceCategories() {
        return array($this->oPrepare->get('PrimaryCategory'));
    }

    protected function getListingType() {
        return $this->oPrepare->get('BuyingMode');
    }

    protected function getAuction() {
        $sBuyingMode = $this->oPrepare->get('BuyingMode');

        $aAuction = array();

        if ($sBuyingMode === 'auction') {
            $aAuction['StartPrice'] = $this->oPrepare->get('PriceForAuction');
            $aAuction['Increment'] = $this->oPrepare->get('PriceIncrement');
        }

        return $aAuction;
    }

    protected function getConditionType() {
        return $this->oPrepare->get('ArticleCondition');
    }

    protected function getDescriptionTemplate() {
        $sTemplate = $this->oPrepare->get('DescriptionTemplate');
        if ($sTemplate === '-1') {
            return null;
        }

        return $sTemplate;
    }

    protected function getDeliveryDescription() {
        $aDeliveryDescriptions = $this->oPrepare->get('DeliveryDescription');
        $aDeliveryCondition = $this->oPrepare->get('DeliveryCondition');

        if (empty($aDeliveryDescriptions) === true || $aDeliveryCondition !== '0') {
            return null;
        }

        $aDeliveryDescriptionsToReturn = array();

        foreach ($aDeliveryDescriptions as $sLang => $sDeliveryDescription) {
            if ($this->aListingLangs[$sLang] === 'true' && empty($sDeliveryDescription) === false) {
                $aDeliveryDescriptionsToReturn[strtoupper($sLang)] = $sDeliveryDescription;
            }
        }

        return $aDeliveryDescriptionsToReturn;
    }

    protected function getMaxRelistCount() {
        return $this->oPrepare->get('MaxRelistCount');
    }

    protected function getStartTime() {
        return $this->oPrepare->get('StartDate');
    }

    protected function getEndTime() {
        return $this->oPrepare->get('EndDate');
    }

    protected function getListingDuration() {
        return $this->oPrepare->get('Duration');
    }

    protected function getPaymentMethods() {
        return $this->oPrepare->get('PaymentMethods');
    }

    protected function getPaymentDescription() {
        $aPaymentDescriptions = $this->oPrepare->get('PaymentDescription');
        $aPaymentMethods = $this->oPrepare->get('PaymentMethods');

        if (empty($aPaymentDescriptions) === true || in_array('0', $aPaymentMethods) === false) {
            return null;
        }

        $aPaymentDescriptionsToReturn = array();

        foreach ($aPaymentDescriptions as $sLang => $sPaymentDescription) {
            if ($this->aListingLangs[$sLang] === 'true' && empty($sPaymentDescription) === false) {
                $aPaymentDescriptionsToReturn[strtoupper($sLang)] = $sPaymentDescription;
            }
        }

        return $aPaymentDescriptionsToReturn;
    }

    protected function getPromotions() {
        $sFirstPromotion = $this->oPrepare->get('FirstPromotion');
        $sSecondPromotion = $this->oPrepare->get('SecondPromotion');

        $aPromotions = array();

        if ($sFirstPromotion !== '-1') {
            $aPromotions[] = $sFirstPromotion;
        }

        if ($sSecondPromotion !== '-1') {
            $aPromotions[] = $sSecondPromotion;
        }

        return $aPromotions;
    }

    protected function getShippingTime() {
        return $this->oPrepare->get('Availability');
    }

    protected function getShippingServices() {
        $shippingService = array(
            'Service' => $this->oPrepare->get('DeliveryCondition'),
            'Cost' => $this->oPrepare->get('DeliveryCost'),
            'Cumulative' => (int) $this->oPrepare->get('Cumulative')
        );

        $packageSize = $this->oPrepare->get('PackageSize');
        if (empty($packageSize) === false) {
            $shippingService['PackageSize'] = $packageSize;
        }

        return array($shippingService);
    }

    private function getImageSize() {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int) $sSize;
        return $iSize;
    }

}
