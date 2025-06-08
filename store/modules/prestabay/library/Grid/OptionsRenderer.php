<?php
/**
 * File OptionsRenderer.php
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

class Grid_OptionsRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $valueFromDb = $row[$fieldKey];

        return isset($config['options'][$valueFromDb]) ? $config['options'][$valueFromDb] : "";
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        $s = '<select id="filter_' . $fieldKey . '" name="filter[' . $fieldKey . ']">
                <option value=""></option>';
        foreach ($config['options'] as $oKey => $oValue) {
            $s.='<option value="' . $oKey . '"' . (($oKey == $value && !is_null($value)) ? ' selected="selected"' : '') . '>' . $oValue . '</option>';
        }

        $s.='</select>';
        return $s;
    }

}