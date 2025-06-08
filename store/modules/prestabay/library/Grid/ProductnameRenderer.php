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
class Grid_ProductnameRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $returnString = $row[$fieldKey];

        if ($row['product_id'] > 0) {
            $productUrl = UrlHelper::getPrestaUrl(
                CoreHelper::isPS15() ? 'AdminProducts' : 'AdminCatalog',
                array('id_product' => $row['product_id'], 'updateproduct' => null)
            );

            $returnString = "<a href='{$productUrl}' target=_blank>" .  $row[$fieldKey] . "</a>";
        }

        return $returnString;
    }
}

