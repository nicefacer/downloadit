<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 *  It is available through the world-wide-web at this URL:
 *  http://involic.com/license.txt
 *  If you are unable to obtain it through the world-wide-web,
 *  please send an email to license@involic.com so
 *  we can send you a copy immediately.
 *
 *  PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 *  Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 *  @author      Involic <contacts@involic.com>
 *  @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 *  @license     http://involic.com/license.txt
 */

class FeeSaveHelper
{
    /**
     * Save information about list item Fee for future calculation of cost
     *
     * @param $ebayId
     * @param $accountId
     * @param $action
     * @param $sellingProductId
     * @param $feeInfo
     *
     * @return bool
     */
    public static function saveFee($ebayId, $accountId, $action, $sellingProductId, $feeInfo)
    {
        $sellingProductModel = new Selling_ProductsModel($sellingProductId);
        if (!$sellingProductModel->id) {
            return false;
        }
        $feeTotal = isset($feeInfo['total']) ? $feeInfo['total'] : 0;
        $feeCurrency = isset($feeInfo['currency']) ? $feeInfo['currency']: 'USD';
        $feeList = isset($feeInfo['list']) ? $feeInfo['list']: array();

        $sellingFeeModel = new Selling_FeeModel();
        $sellingFeeModel->setData(array(
            'ebay_id' => $ebayId,
            'account_id' => $accountId,
            'selling_product_id' => $sellingProductId,
            'product_id' => $sellingProductModel->product_id,
            'product_id_attribute' => $sellingProductModel->product_id_attribute,
            'action' => $action,
            'fee_total' => (float)$feeTotal,
            'fee_currency' => $feeCurrency,
            'fee_list' => json_encode($feeList),
        ));

        return $sellingFeeModel->save();
    }
}