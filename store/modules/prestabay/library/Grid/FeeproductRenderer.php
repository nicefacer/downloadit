<?php

/**
 * File FeeproductRenderer.php
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
 * eBay Listing Import.
 * Adding possibility import all eBay Listing to PrestaShop
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2012 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Grid_FeeproductRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $productName = isset($row[$fieldKey]) ? $row[$fieldKey] : "";

        $return = $productName;
        $return .= ' [';
        if ($row['selling_product_id'] > 0) {
            $sellingProductUrl = UrlHelper::getUrl("selling/edit",
                    array('id' => $row['selling_id'])) . "&filter[id]=" . $row['selling_product_id'];

            $return .= "<a href='" . $sellingProductUrl . "' target=_blank>PrestaBay</a>";
        }
        if ($row['product_id'] > 0) {
            if ($row['selling_product_id'] > 0) {
                $return .= ' | ';
            }
            $return .= " <a href='" .
                UrlHelper::getPrestaUrl("AdminProducts", array(
                    'id_product' => $row['product_id'],
                    'updateproduct' => null)) . "'>PrestaShop</a>";
        }
        $return .= ']';

        return $return;
    }

}