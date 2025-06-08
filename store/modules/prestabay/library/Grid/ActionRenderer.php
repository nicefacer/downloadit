<?php
/**
 * File ActionRenderer.php
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

class Grid_ActionRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $totalStr = "";

        $getterFunction = isset($config['getter']) ? $config['getter'] : "getId";
        foreach ($config['actions'] as $action) {
            if (isset($action['condition'])) {
                $condResult = eval($action['condition']);
                if (!$condResult) {
                    continue;
                }
            }
            $getterField = isset($action['field']) ? $action['field'] : "id";
            $actionBaseUrl = isset($action['url']) ? $action['url'] : $grid->getGridUrl();
            $newWindows = isset($action['newWindow']) ? (bool)$action['newWindow'] : false;


            $actionUrl = UrlHelper::getUrl($actionBaseUrl, array($getterField => call_user_func(array($grid, $getterFunction), $row)));

            $classA = '';
            if (isset($action['bootstrap_icon'])) {
                $classA = 'btn btn-default';
            }
            $totalStr.="<a class='".$classA."' href='" . $actionUrl . "' title='" . (isset($action['title']) ? $action['title'] : '') . "' " . (isset($action['confirm']) ? "onclick='return confirm(\"" . $action['confirm'] . "\");'" : "").' ' .
                                ($newWindows?'target="_blank"':''). ">";
            if (isset($action['icon'])) {
                $totalStr.='<img title="' . $action['caption'] . '" alt="' . $action['caption'] . '" src="../img/admin/' . $action['icon'] . '"/>';
            } else {
                $totalStr.=$action['caption'];
            }
            $totalStr.="</a>";

            if (!isset($action['icon']) && !isset($action['bootstrap_icon'])) {
                $totalStr.="<br/>";
            }
            if (isset($action['bootstrap_icon'])) {
                $totalStr.="&nbsp;";
            }
        }
        return $totalStr;
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        // Action not filtrable
        return '--';
    }

}