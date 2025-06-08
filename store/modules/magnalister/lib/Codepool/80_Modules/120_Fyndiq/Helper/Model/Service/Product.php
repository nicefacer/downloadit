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
class ML_Fyndiq_Helper_Model_Service_Product
{

    /** @var ML_Database_Model_Table_Selection $oSelection */
    protected $oSelection = null;
    protected $aSelectionData = array();

    /** @var ML_Fyndiq_Model_Table_Fyndiq_Prepare $oPrepare */
    protected $oPrepare = null;

    /** @var ML_Shop_Model_Product_Abstract $oProduct */
    protected $oProduct = null;
    protected $oVariant = null;
    protected $aData = null;

    public function __call($sName, $mValue)
    {
        return $sName . '()';
    }

    public function __construct()
    {
        $this->oPrepare = MLDatabase::factory('fyndiq_prepare');
        $this->oSelection = MLDatabase::factory('selection');
    }

    public function setProduct(ML_Shop_Model_Product_Abstract $oProduct)
    {
        $this->oProduct = $oProduct;
        $this->sPrepareType = '';
        $this->aData = null;
        return $this;
    }

    public function setVariant(ML_Shop_Model_Product_Abstract $oProduct)
    {
        $this->oVariant = $oProduct;
        return $this;
    }

    public function resetData()
    {
        $this->aData = null;
        return $this;
    }

    public function getData()
    {
        if ($this->aData === null) {
            $this->oPrepare->init()->set('products_id', $this->oVariant->get('id'));
            $aData = array();
            foreach (
                array(
                    'SKU',
                    'ItemTitle',
                    'Description',
                    'Brand',
                    'Currency',
                    'Images',
                    'CategoryId',
                    'Price',
                    'BasePrice',
                    'ShippingCost',
                    'VatPercent',
                    'Quantity',
                    'ArticleEan',
                    'ArticleMpn',
                    'ArticleName',
                    'ArticleSKU'
                ) as $sField) {
                if (method_exists($this, 'get' . $sField)) {
                    $mValue = $this->{'get' . $sField}();
                    if (is_array($mValue)) {
                        foreach ($mValue as $sKey => $mCurrentValue) {
                            if (empty($mCurrentValue)) {
                                unset ($mValue[$sKey]);
                            }
                        }
                        $mValue = empty($mValue) ? null : $mValue;
                    }
                    if ($mValue !== null) {
                        $aData[$sField] = $mValue;
                    }
                } else {
                    MLMessage::gi()->addWarn("function  ML_Fyndiq_Helper_Model_Service_Product::get" . $sField . "() doesn't exist");
                }
            }
            if (empty($aData['BasePrice'])) {
                unset($aData['BasePrice']);
            }
            $this->aData = $aData;
        }
        return $this->aData;
    }

    protected function getSKU()
    {
        return $this->oVariant->getMarketPlaceSku();
    }

    /**
     * Checks from which shop is called this function,gets BasePrice and formats it for API.This function is implemented in this way
     * because $this->oVariant->getBasePrice() returns different structures of arrays and API needs to recieve the same BasePrice data.
     * This function is implemented in this part because there is possibility in 70_SHOP e-commerce folder for something else to not work.
     */
    protected function getBasePrice(){
        $basePrice = $this->oVariant->getBasePrice();
        $formattedBasePrice = array();
        // Shopware shop
        if(array_key_exists('ShopwareDefaults',$basePrice)){
            $formattedBasePrice['Unit'] = $basePrice['ShopwareDefaults']['$sUnit'];
            $formattedBasePrice['Value'] = number_format((float)$basePrice['Value'], 2, '.','');
            return $formattedBasePrice;
            //prestashop
        }elseif(array_key_exists('Unit',$basePrice)){
            $formattedBasePrice['Unit'] = $basePrice['Unit'];
            $formattedBasePrice['Value'] = number_format((float)$basePrice['Value'], 2, '.','');
            return $formattedBasePrice;
        }
    }

    protected function getArticleSKU()
    {
        return $this->oVariant->getMarketPlaceSku();
    }

    protected function getItemTitle()
    {
        $iLang = MLModul::gi()->getConfig('lang');

        $sTitle = $this->oPrepare->get('ItemTitle');
        if (empty($sTitle) === false && $sTitle !== '') {
            $sTitle = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
        } else {
            $this->oVariant->setLang($iLang);
            $sTitle = $this->oVariant->getName();
            $sTitle = html_entity_decode(fixHTMLUTF8Entities($sTitle), ENT_COMPAT, 'UTF-8');
        }

        return $sTitle;
    }

    protected function getDescription()
    {
        $sDescription = $this->oPrepare->get('Description');
        if (empty($sDescription) === false && $sDescription !== '') {
            $sDescription = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
        } else {
            $iLang = MLModul::gi()->getConfig('lang');
            $this->oVariant->setLang($iLang);
            $sDescription = $this->oVariant->getDescription();
            $sDescription = html_entity_decode(fixHTMLUTF8Entities($sDescription), ENT_COMPAT, 'UTF-8');
            $sDescription = $this->fyndiqSanitizeDescription($sDescription);
        }

        return $sDescription;
    }

    protected function getBrand()
    {
        $brandField = $this->oVariant->getManufacturer();
        return $brandField ? $brandField : '';
    }

    protected function getShippingCost()
    {
        $fShippingCost = $this->toFloat($this->oPrepare->get('ShippingCost'));
        if (isset($fShippingCost) && $fShippingCost > 0) {
            return $fShippingCost;
        }

        return null;
    }

    protected function getImages()
    {
        $aOut = array();

        $aPreparedImgs = $this->oPrepare->get('Images');
        $imgsMaster = $this->oProduct->getImages();
        $imgsVariant = $this->oVariant->getImages();

        $imgs = $this->fixImages($imgsVariant, $imgsMaster);

        if (!empty($aPreparedImgs) || !empty($imgs)) {

            $sImagePathFromConfig = MLModul::gi()->getConfig('imagepath');

            foreach ($imgs as $sImage) {
                $sImageName = $this->substringAferLast('\\', $sImage);

                if (isset($sImageName) === false || strpos($sImageName, '/') !== false) {
                    $sImageName = $this->substringAferLast('/', $sImage);
                }

                if (isset($sImageName) === false) {
                    $sImageName = $sImage;
                }

                if ($aPreparedImgs != null && !in_array($sImageName, $aPreparedImgs) && count($imgsMaster) == count($imgsVariant)) {
                    continue;
                }

                try {
                    if (isset($sImagePathFromConfig) && $sImagePathFromConfig != '') {
                        $sImagePath = $sImagePathFromConfig . $sImageName;
                    } else {
                        $aImage = MLImage::gi()->resizeImage($sImage, 'products', 500, 500);
                        $sImagePath = $aImage['url'];
                    }

                    $aOut[] = array('URL' => $sImagePath, 'id' => $sImageName);

                } catch (Exception $ex) {
                    // Happens if image doesn't exist.
                }
            }
        }

        return $aOut;
    }

    protected function getQuantity()
    {
        $iQty = $this->oVariant->getSuggestedMarketplaceStock(
            MLModul::gi()->getConfig('quantity.type'),
            MLModul::gi()->getConfig('quantity.value')
        );
        return $iQty < 0 ? 0 : $iQty;
    }

    protected function getPrice()
    {
        if (isset($this->aSelectionData['price'])) {
            $fPrice = $this->aSelectionData['price'];
        } else {
            $fPrice = $this->oVariant->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        }

        return $fPrice;
    }

    protected function getCategoryID()
    {
        return $this->oPrepare->get('PrimaryCategory');
    }

    protected function getCurrency()
    {
        return 'EUR';
    }

    protected function getVatPercent()
    {
        $vats = MLModul::gi()->getConfig('vat');
        return $vats[$this->oVariant->getTaxClassId()];
    }

    protected function getArticleEAN()
    {
        return $this->oVariant->getEAN();
    }

    protected function getArticleMPN()
    {
        return $this->oVariant->getManufacturerPartNumber();
    }

    protected function getVariationId()
    {
        $aResult = $this->oVariant->getVariatonData();
        if (count($aResult) != 0) {
            return $this->oVariant->get('id');
        } else {
            return null;
        }
    }

    protected function getArticleName()
    {
        $iLangId = MLModul::gi()->getConfig('lang');
        $this->oVariant->setLang($iLangId);
        return $this->oVariant->getName();
    }

    /**
     * Sanitazes description and preparing it for Fyndiq because Fyndiq doesn't allow html tags.
     *
     * @param string $sDescription
     * @return string $sDescription
     *
     */
    private function fyndiqSanitizeDescription($sDescription)
    {
        $sDescription= preg_replace("#(<\\?div>|<\\?li>|<\\?p>|<\\?h1>|<\\?h2>|<\\?h3>|<\\?h4>|<\\?h5>|<\\?blockquote>)([^\n])#i", "$1\n$2", $sDescription);
        // Replace <br> tags with new lines
        $sDescription = preg_replace('/<[h|b]r[^>]*>/i', "\n", $sDescription);
        $sDescription = trim(strip_tags($sDescription));
        // Normalize space
        $sDescription = str_replace("\r", "\n", $sDescription);
        $sDescription = preg_replace("/\n{3,}/", "\n\n", $sDescription);
        $sDescription = mb_substr($sDescription,0,4096, 'UTF-8');

        return $sDescription;
    }

    private function substringAferLast($sNeedle, $sString)
    {
        if (!is_bool($this->strrevpos($sString, $sNeedle))) {
            return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    private function toFloat($num) {
        $dotPos = strrpos($num, '.');
        $commaPos = strrpos($num, ',');
        $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos :
            ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);

        if (!$sep) {
            return floatval(preg_replace("/[^0-9]/", "", $num));
        }

        return floatval(
            preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . '.' .
            preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
        );
    }

    private function fixImages($imgsVariant, $imgsMaster) {
        foreach ($imgsMaster as $imageMaster) {
            if (!in_array($imageMaster, $imgsVariant)) {
                $imgsVariant[] = $imageMaster;
            }
        }

        return $imgsVariant;
    }
}
