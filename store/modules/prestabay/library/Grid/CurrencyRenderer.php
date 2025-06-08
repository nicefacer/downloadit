<?php
/**
 * File CurrencyRenderer.php
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

class Grid_CurrencyRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $value = isset($row[$fieldKey]) ? $row[$fieldKey] : "";
        
        if (isset($config['currency_column']) && isset($row[$config['currency_column']])) {
            $currency = (int) Currency::getIdByIsoCode($row[$config['currency_column']]);
            if ($currency > 0) {
                return Tools::displayPrice($value, (int) $currency);
            } else {
                return money_format('%i', $value) . ' ' . $row[$config['currency_column']];
            }
        }

        return $value;
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        return '<input type="text" value="' . $value . '" id="filter_' . $fieldKey . '" name="filter[' . $fieldKey . ']"/>';
    }

}