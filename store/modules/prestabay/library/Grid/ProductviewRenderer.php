<?php

/**
 * File ProductRenderer.php
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
 * Adding possibilty import all eBay Listing to PrestaShop
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2012 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Grid_ProductviewRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $returnString = "<a href='#' class='setProductId'>N/A</a>";
        if ($row['product_id'] > 0) {
            $productUrl = UrlHelper::getPrestaUrl(
                CoreHelper::isPS15() ? 'AdminProducts' : 'AdminCatalog',
                array('id_product' => $row['product_id'], 'updateproduct' => null)
            );

            $returnString = "<a href='{$productUrl}' target=_blank>" . '#' . $row['product_id'] . "</a>";
        }

        return $returnString;
    }
}

