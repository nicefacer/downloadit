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
MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_PrepareData_Abstract');

class ML_Ricardo_Helper_Model_Table_Ricardo_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract {

    const TITLE_MAX_LENGTH = 40;
    const SUBTITLE_MAX_LENGTH = 60;

    public $aErrors = array();
    public $bIsSinglePrepare;

    public function getPrepareTableProductsIdField() {
        return 'products_id';
    }

    protected function primaryCategoryField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        if(!isset($aField['value']) || $aField['value'] === '') {
            $this->aErrors[] = 'ricardo_prepareform_category';
        }
    }

    protected function products_idField(&$aField) {
        $aField['value'] = $this->oProduct->get('id');
    }

    protected function listinglangsField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function descriptionTemplateField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function articleConditionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        unset($aField['type']);
    }

    protected function availabilityField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function deliveryConditionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function deliveryPackageField(&$aField) {
        $aField['type'] = 'select';
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function deliveryDescriptionField(&$aField) {
        $this->getOptionalDescription($aField, 'deliverycondition', 'ricardo_prepareform_delivery_description');
    }

    protected function deliveryCostField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function cumulativeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function warrantyConditionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function warrantyDescriptionField(&$aField) {
        $this->getOptionalDescription($aField, 'warrantycondition', 'ricardo_prepareform_warranty_description');
    }

    protected function maxRelistCountField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function buyingModeField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function paymentMethodsField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        $posible = array('0', '8192', '1073741824');
        $intersect = array_intersect($posible, is_array($aField['value']) ? $aField['value'] : array());
        if(count($intersect) !== 1 || (count($aField['value']) == 2 && in_array('1073741824', $aField['value']))) {
            $this->aErrors[] = 'ricardo_prepareform_paymentmethods';
        }
    }

    protected function paymentDescriptionField(&$aField) {
        $this->getOptionalDescription($aField, 'paymentmethods', 'ricardo_prepareform_payment_description');
    }

    protected function fixPriceField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        if($this->bIsSinglePrepare === true && isset($aField['value']) === false) {
            $aField['value'] = $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
        } elseif($this->bIsSinglePrepare === false) {
            $aField['value'] = 0;
        }
    }

    protected function enableBuyNowPriceField(&$aField) {
        $fFixPrice = $this->getField('fixprice', 'value');
        $aField['value'] = $this->getFirstValue($aField);
        if(isset($fFixPrice) && $fFixPrice > 0 && $this->bIsSinglePrepare === true) {
            $aField['value'] = '1';
        } elseif(isset($aField['value']) === false || $this->bIsSinglePrepare === true) {
            $aField['value'] = '0';
        }
    }

    protected function priceForAuctionField(&$aField) {
        $aField['type'] = 'price';
        $aField['currency'] = 'CHF';
        $aField['value'] = $this->getFirstValue($aField);
        if(!isset($aField['value'])) {
            $aField['value'] = 0;
        }
    }

    protected function priceIncrementField(&$aField) {
        $aField['type'] = 'price';
        $aField['currency'] = 'CHF';
        $aField['value'] = $this->getFirstValue($aField);
        if(!isset($aField['value'])) {
            $aField['value'] = 0;
        }
    }

    protected function startDateField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        if(!isset($aField['value'])) {
            $this->aErrors[] = 'ricardo_prepareform_setdate';
        }
    }

    protected function endDateField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);

        if(!isset($aField['value'])) {
            $this->aErrors[] = 'ricardo_prepareform_setdate';
        }
    }

    protected function durationField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function deTitleField(&$aField) {
        $this->getTitle($aField, 'de');
    }

    protected function frTitleField(&$aField) {
        $this->getTitle($aField, 'fr');
    }

    protected function deSubtitleField(&$aField) {
        $this->getSubtitle($aField, 'de');
    }

    protected function frSubtitleField(&$aField) {
        $this->getSubtitle($aField, 'fr');
    }

    protected function deDescriptionField(&$aField) {
        $this->getDescription($aField, 'de');
    }

    protected function frDescriptionField(&$aField) {
        $this->getDescription($aField, 'fr');
    }

    protected function imagesField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        $aField['values'] = array();
        $aIds = array();
        if(isset($this->oProduct)) {
            if($this->oProduct->get('parentid') == 0){
                $aImages = $this->oProduct->getImages();
            }else{
                $aImages = $this->oProduct->getParent()->getImages();
            }
            
            foreach ($aImages as $sImagePath) {
                $sId = $this->substringAferLast('\\', $sImagePath);
                if(isset($sId) === false || strpos($sId, '/') !== false) {
                    $sId = $this->substringAferLast('/', $sImagePath);
                }

                try {
                    $aUrl = MLImage::gi()->resizeImage($sImagePath, 'products', 60, 60);
                    $aField['values'][$sId] = array(
                        'height' => '60',
                        'width' => '60',
                        'alt' => $sId,
                        'url' => $aUrl['url'],
                    );
                    $aIds[] = $sId;
                } catch (Exception $ex) {
                    // Happens if image doesn't exist.
                }
            }
        }

        if(isset($aField['value']) && $this->bIsSinglePrepare === true) {
            if(in_array('false', $aField['value']) === true) {
                array_shift($aField['value']);
            }
        } else {
            $aField['value'] = $aIds;
        }
    }

    protected function firstPromotionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function secondPromotionField(&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }

    private function getOptionalDescription(&$aField, $sCondition, $sMessage) {
        $aField['type'] = 'table';
        $aField['value'] = $this->getFirstValue($aField);
        $aField['values'] = $this->getField('listinglangs', 'value');

        $bValueEmpty = false;
        $aLangs = $this->getField('listinglangs', 'value');
        if(($aLangs['de'] === 'true') && (isset($aField['value']['de']) === false || $aField['value']['de'] === '')) {
            $bValueEmpty = true;
        }

        if(($aLangs['fr'] === 'true') && (isset($aField['value']['fr']) === false || $aField['value']['fr'] === '')) {
            $bValueEmpty = true;
        }

        $aIds = $this->getField($sCondition, 'value');
        if(is_array($aIds) === true) {
            $condition = in_array('0', $aIds);
        } else {
            $condition = $aIds === '0';
        }

        if((isset($aField['value']) === false || $bValueEmpty) && $condition) {
            $this->aErrors[] = $sMessage;
        }
    }

    private function getTitle(&$aField, $sLang) {
        $aListinglangs = $this->getField('listinglangs', 'value');
        if($aListinglangs[$sLang] === 'true') {
            $bLangs = true;
        } else {
            $bLangs = false;
        }

        if(isset($this->oProduct)) {
            $sValue = $this->getFirstValue($aField);
            if(!isset($sValue) || $sValue === '') {
                $sValue = MLModul::gi()->getConfig('template.name');
            }

            if(!isset($sValue) || $sValue === '') {
                $sValue = '#TITLE#';
            }

            $aLangs = MLModul::gi()->getConfig('langs');
            $this->oProduct->setLang($aLangs[$sLang]);
            $sBasePrice = $this->getField('fixPrice', 'value');
            $aReplace = array(
                '#TITLE#' => $this->oProduct->getName(),
                '#ARTNR#' => $this->oProduct->getMarketPlaceSku(),
                '#PID#' => $this->oProduct->get('marketplaceidentid'),
                '#BASEPRICE#' => $sBasePrice,
            );
            $aField['value'] = str_replace(array_keys($aReplace), array_values($aReplace), $sValue);
            if(trim($aField['value']) == '') {
                $aField['value'] = $this->getFirstValue($aField, $this->oProduct->getName());
            }
        }

        if(isset($aField['value']) && mb_strlen($aField['value'], 'UTF-8') > self::TITLE_MAX_LENGTH) {
            $aField['value'] = mb_substr($aField['value'], 0, self::TITLE_MAX_LENGTH, 'UTF-8');
        }

        $aField['maxlength'] = self::TITLE_MAX_LENGTH;
        $aField['value'] = html_entity_decode(fixHTMLUTF8Entities($aField['value']), ENT_COMPAT, 'UTF-8');

        if($this->bIsSinglePrepare && (!isset($aField['value']) || $aField['value'] === '') && $bLangs) {
            $this->aErrors[] = 'ricardo_prepareform_title';
        }
    }

    protected function getSubtitle(&$aField, $sLang) {
        $aField['value'] = $this->getFirstValue($aField);
        if(isset($this->oProduct)) {
            $aLangs = MLModul::gi()->getConfig('langs');
            $this->oProduct->setLang($aLangs[$sLang]);
            if(!isset($aField['value']) || $aField['value'] === '') {
                $aField['value'] = $this->oProduct->getShortDescription();
            }
        }

        if(mb_strlen($aField['value'], 'UTF-8') > self::SUBTITLE_MAX_LENGTH) {
            $aField['value'] = mb_substr($aField['value'], 0, self::SUBTITLE_MAX_LENGTH, 'UTF-8');
        }

        $aField['value'] = isset($aField['value']) ? $aField['value'] : '';
        $aField['maxlength'] = self::SUBTITLE_MAX_LENGTH;
        $aField['value'] = html_entity_decode(fixHTMLUTF8Entities($aField['value']), ENT_COMPAT, 'UTF-8');
    }

    private function getDescription(&$aField, $sLang) {
        $aListinglangs = $this->getField('listinglangs', 'value');
        if($aListinglangs[$sLang] === 'true') {
            $bLangs = true;
        } else {
            $bLangs = false;
        }

        if(isset($this->oProduct)) {
            $sValue = $this->getFirstValue($aField);
            if(!isset($sValue) || $sValue === '') {
                $sValue = MLModul::gi()->getConfig('template.content');
            }

            if(!isset($sValue) || $sValue === '') {
                $sValue = '<p>#TITLE#</p>
                    <p>#ARTNR#</p>
                    <p>#SHORTDESCRIPTION#</p>
                    <p>#PICTURE1#</p>
                    <p>#PICTURE2#</p>
                    <p>#PICTURE3#</p>
                    <p>#DESCRIPTION#</p>';
            }

            $aLangs = MLModul::gi()->getConfig('langs');
            $this->oProduct->setLang($aLangs[$sLang]);
            $oProduct = $this->oProduct;
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
            $aField['value'] = $sValue;
        }

        $aField['value'] = html_entity_decode(fixHTMLUTF8Entities($aField['value']), ENT_COMPAT, 'UTF-8');
        if($this->bIsSinglePrepare && (!isset($aField['value']) || $aField['value'] === '') && $bLangs) {
            $this->aErrors[] = 'ricardo_prepareform_description';
        }
    }

    protected function getImageSize() {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int) $sSize;
        return $iSize;
    }

    private function substringAferLast($sNeedle, $sString) {
        if(!is_bool($this->strrevpos($sString, $sNeedle))) {
            return substr($sString, $this->strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private function strrevpos($instr, $needle) {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

}
