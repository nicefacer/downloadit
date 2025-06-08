<?php
/**
 * File List.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

class Services_Item_List extends Services_Item_Abstract
{

    /**
     * Perform internal request validation.
     * To reduce server load
     */
    public function validate()
    {
        $errors = array();
        // 0)  check item status
        $itemCheckResult = $this->checkItemStatus();
        if ($itemCheckResult != false) {
            $errors[] = $itemCheckResult;
        }
        // 1) check for not empty title
        $this->_isTitleEmpty() && $errors[] = L::t('Please provide item "Title"');
        // 2) check for not empty description
        $this->_isDescriptionEmpty() && $errors[] = L::t('Item "Description" can\'t be empty');
        // 3) check for all price more that 0
        $this->_isPriceEmpty() && $errors[] = L::t('"Price" for item need to be more that 0');

        // 4) check for possible list variations
        $variationErrors = $this->_checkCorrectVariations();
        if ($variationErrors) {
            $errors[] = $variationErrors;
        }
        return $errors;
    }

    public function getModeRelatedData($mode)
    {
        switch ($mode)
        {
            default:
            case EbayListHelper::MODE_FULL:
                return $this->getData();

            case EbayListHelper::MODE_QTY:
            case EbayListHelper::MODE_PRICE:
            case EbayListHelper::MODE_QTY_PRICE:
                return $this->getPartData($mode);
        }
    }

    public function getData()
    {
        return array(
            'listing' =>
                $this->_getTitle() +
                $this->_getSubtitle() +
                $this->_getSku() +
                $this->_getQuantity() +
                $this->_getListingType() +
                $this->_getListingDuration() +
                $this->_getCrossBorderTrade() +
                $this->_getProductSpecific() +
                $this->_getProductSpecificAttribute() +
                $this->_getCatalogCategory() +
                $this->_getStoreCategories() +
                $this->_getItemDescription() +
                $this->_getSite() +
                $this->_getConditionID() +
                $this->_getPrivate() +
                $this->_getPictureDetails() +
                $this->_getHitCounter() +
                $this->_getVatPercent() +
                $this->_getMultiVariations() +
                $this->_getBestOffer() +
                $this->_getEnhancement() +
                $this->_getEAN() +
                $this->_getUPC() +
                $this->_getISBN() +
                $this->_getMPN() +
                $this->_getGiftService() +
                $this->_getUnitInfo(),
            'price' => $this->_getCurrency() + $this->_getPrice(),
            'shipping' => $this->_getItemFrom() + $this->_getShippingDetails(),
            'payments' => $this->_getPaymentMethods(),
            'return' => $this->_getReturnPolicy(),
        );
    }

    public function getPartData($mode)
    {
        $requestData = array(
            'listing' => $this->_getListingType() + $this->_getMultiVariations() + $this->_getSite(),
        );
        if ($mode == EbayListHelper::MODE_QTY || $mode == EbayListHelper::MODE_QTY_PRICE) {
            $requestData['listing'] += $this->_getQuantity();
        }
        if ($mode == EbayListHelper::MODE_PRICE || $mode == EbayListHelper::MODE_QTY_PRICE) {
            $requestData['price'] = $this->_getCurrency() + $this->_getPrice();
        }

        if (!isset($requestData['price'])) {
            $requestData['price'] = false;
        }

        $requestData['return'] = $this->_getReturnPolicy();

        return $requestData;
    }

    protected function checkItemStatus()
    {
        if ($this->getProfileProduct()->getSellingProduct()->status == Selling_ProductsModel::STATUS_ACTIVE) {
            return L::t("Item already active");
        }

        return false;
    }
}
