<?php
/**
 * File TextRenderer.php
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

class Grid_OrderitemtotalRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $columnTotal = $row['qty']*$row['price'];

        if (isset($config['currency_column']) && isset($row[$config['currency_column']])) {
            return Tools::displayPrice($columnTotal, (int) Currency::getIdByIsoCode($row[$config['currency_column']]), false);
        }
        return $columnTotal;
    }


    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null) {
        return '-';
    }

}