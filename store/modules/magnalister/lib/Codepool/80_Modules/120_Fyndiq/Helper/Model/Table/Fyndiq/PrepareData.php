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

class ML_Fyndiq_Helper_Model_Table_Fyndiq_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract
{
    public $aErrorFields = array();
    public $bIsSinglePrepare;
    const TITLE_MAX_LENGTH = 64;
    const TITLE_MIN_LENGTH = 5;
    const DESC_MAX_LENGTH = 4096;
    const DESC_MIN_LENGTH = 10;


    public function getPrepareTableProductsIdField()
    {
        return 'products_id';
    }

    protected function listingTypeField(&$aField)
    {
        $aField['value'] = $this->getFirstValue($aField);
    }

    protected function itemTitleField(&$aField)
    {
        $aField['value'] = $this->getFirstValue($aField);
        if (isset($this->oProduct)) {
            if (isset($aField['value']) === false || empty($aField['value'])) {
                $aField['value'] = $this->oProduct->getName();
            }
        }

        if (isset($aField['value']) && mb_strlen($aField['value'], 'UTF-8') > self::TITLE_MAX_LENGTH) {
            $aField['value'] = mb_substr($aField['value'], 0, self::TITLE_MAX_LENGTH, 'UTF-8');
        }

        $aField['maxlength'] = self::TITLE_MAX_LENGTH;
        $aField['minlength'] = self::TITLE_MIN_LENGTH;

        if($this->bIsSinglePrepare && (!isset($aField['value']) || $aField['value'] === '' || mb_strlen($aField['value'], 'UTF-8') < self::TITLE_MIN_LENGTH)) {
            $this->aErrors[] = 'fyndiq_prepare_form_itemtitle';
        }
    }

    protected function priceField(&$aField)
    {
        if ($this->bIsSinglePrepare) {
            $price = $aField['value'] = $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject());
            $aField['value'] = round($price, 2);
        } else {
            $aField['issingleview'] = false;
        }
    }

    protected function shippingCostField(&$aField)
    {
        $aField['value'] = $this->toFloat($this->getFirstValue($aField));
    }

    protected function imagesField(&$aField)
    {
        $aField['value'] = $this->getFirstValue($aField);
        $aField['values'] = array();
        $aIds = array();
        if (isset($this->oProduct)) {
            $aImages = $this->oProduct->getImages();

            foreach ($aImages as $sImagePath) {
                $sId = self::substringAferLast('\\', $sImagePath);
                if (isset($sId) === false || strpos($sId, '/') !== false) {
                    $sId = self::substringAferLast('/', $sImagePath);
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

        if (isset($aField['value']) && $aField['value'] && $this->bIsSinglePrepare === true) {
            if (in_array('false', $aField['value']) === true) {
                array_shift($aField['value']);
            }
        } else {
            $aField['value'] = $aIds;
        }
    }

    protected function descriptionField(&$aField)
    {
        $aField['value'] = $this->getFirstValue($aField);
        if ($this->oProduct) {
            $aField['optional']['field']['type'] = 'text';
            if (isset($aField['value']) === false || empty($aField['value'])) {
                $aField['value'] = $this->oProduct->getDescription();
            }
        }

        $aField['value'] = $this->fyndiqSanitizeDescription($aField['value']);

        $aField['maxlength'] = self::DESC_MAX_LENGTH;
        $aField['minlength'] = self::DESC_MIN_LENGTH;

        if ($this->bIsSinglePrepare && (!isset($aField['value']) || $aField['value'] === '' || mb_strlen($aField['value'], 'UTF-8') < self::TITLE_MIN_LENGTH)) {
            $this->aErrors[] = 'fyndiq_prepare_form_description';
        }
    }

    protected function primaryCategoryField(&$aField)
    {
        $aField['value'] = $this->getFirstValue($aField);
        if (!isset($aField['value'])) {
            $this->aErrors[] = 'fyndiq_prepare_form_category';
        } else if (isset($aField['value'])) {
            $sValue = trim($aField['value']);
            if (empty($sValue)) {
                $this->aErrors[] = 'fyndiq_prepare_form_category';
            }
        }
    }

    protected function products_idField(&$aField)
    {
        $aField['value'] = $this->oProduct->get('id');
    }

    protected function attributesField(&$aField)
    {
        $aAttributes = $this->getFirstValue($aField, array());
        $aField['value'] = json_encode($aAttributes);
        $aCat = reset($aAttributes);
        $aCat = is_array($aCat) ? $aCat : array();
        $sCategoryId = key($aAttributes);

        foreach ($aCat as $sAttributeId => $sAttributeValue) {
            $blRequired = (int)$sAttributeValue['Required'] === 1;
            $iMaxLength = (int)$sAttributeValue['MaxLength'];
            $sCode = $sAttributeValue['Code'];
            $sMatchAttribute = isset($sAttributeValue['MatchAttribute']) ? $sAttributeValue['MatchAttribute'] : null;

            if ($blRequired === true) {
                if ($sCode === '__none__' || ($sCode === '__freevalue__' && empty($sMatchAttribute) === true)) {
                    $this->aErrorFields["attributes.$sCategoryId.$sAttributeId.code"] = true;
                } else if ($blRequired === true && $sCode === 'aamatchaa') {
                    if (empty($sMatchAttribute) === true) {
                        $this->aErrorFields["attributes.$sCategoryId.$sAttributeId.code"] = true;//
                    }
                }
            }

            if (count($this->aErrorFields) > 0) {
                MLMessage::gi()->addError(MLI18n::gi()->get('configform_check_entries_error'));
            }

            if ($sCode === '__freevalue__' && strlen($sMatchAttribute) > $iMaxLength) {
                MLMessage::gi()->addError(MLI18n::gi()->get('fyndiq_prepareform_max_length_part1')
                    . ' ' . $sAttributeValue['AttrName'] . ' ' . MLI18n::gi()->get('fyndiq_prepareform_max_length_part2')
                    . ' ' . $sAttributeValue['MaxLength'] . '.');
                $this->aErrorFields["attributes.$sCategoryId.$sAttributeId.code"] = true;
            }
        }
    }

    /**
     * Sanitazes description and preparing it for Fyndiq because Fyndiq doesn't allow html tags.
     *
     * @param string $sDescription
     * @return string $sDescription
     *
     */
    protected function fyndiqSanitizeDescription($sDescription)
    {
        $sDescription = preg_replace("#(<\\?div>|<\\?li>|<\\?p>|<\\?h1>|<\\?h2>|<\\?h3>|<\\?h4>|<\\?h5>|<\\?blockquote>)([^\n])#i", "$1\n$2", $sDescription);
        // Replace <br> tags with new lines
        $sDescription = preg_replace('/<[h|b]r[^>]*>/i', "\n", $sDescription);
        $sDescription = trim(strip_tags($sDescription));
        // Normalize space
        $sDescription = str_replace("\r", "\n", $sDescription);
        $sDescription = preg_replace("/\n{3,}/", "\n\n", $sDescription);
        $sDescription = mb_substr($sDescription,0,4096, 'UTF-8');

        return $sDescription;
    }

    protected function callApi($aRequest, $iLifeTime)
    {
        try {
            $aResponse = MagnaConnector::gi()->submitRequestCached($aRequest, $iLifeTime);
            if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                return $aResponse['DATA'];
            } else {
                return array();
            }
        } catch (MagnaException $e) {
            return array();
        }
    }

    public static function substringAferLast($sNeedle, $sString)
    {
        if (!is_bool(self::strrevpos($sString, $sNeedle))) {
            return substr($sString, self::strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }

    protected function getImageSize()
    {
        $sSize = MLModul::gi()->getConfig('imagesize');
        $iSize = $sSize == null ? 500 : (int)$sSize;
        return $iSize;
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

}
