<?php
/**
 * File ProductimageRenderer.php
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

class Grid_ProductimageRenderer extends Grid_TextRenderer
{
    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $value = isset($row[$fieldKey]) ? $row[$fieldKey] : "";
        if (isset($row['product_id_attribute']) && $row['product_id_attribute'] > 0 && isset($row['attr_img']) && $row['attr_img'] > 0) {
            $value = $row['attr_img'];
        }
        $imgId = (int)($row['id_product']) . '-' . $value;

        return "<img src='" . $this->_getImageLink(
            $row['link_rewrite'],
            $imgId,
            CoreHelper::isPS15() ? 'medium_default' : 'medium'
        ) . "'/>";
    }

    protected function _getImageLink($name, $ids, $type = null)
    {
        if (CoreHelper::isPS15()) {
            $linkInstance = new Link(null, Tools::getProtocol());
        } else {
            $linkInstance = new Link();
        }

        $imgPathValue = $linkInstance->getImageLink($name, $ids, $type);
        if (CoreHelper::isPS15()) {
//            if (strpos($imgPathValue, "www.") === false) {
//                $imgPathValue = str_replace("http://", "http://www.", $imgPathValue);
//            }
        }

        if (strpos($imgPathValue, 'http://') === false && strpos($imgPathValue, 'https://') == false) {
            // Not full path to image. Possible it's PrestaShop 1.3?
            $imgPathValue = _PS_BASE_URL_ . $imgPathValue;
        }

        return $imgPathValue;
    }
}


