<?php

/**
 * File ProfileebaycategoryRenderer.php
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
class Grid_ProfileebaycategoryRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        if ($row['ebay_category_mode'] == ProfilesModel::EBAY_CATEGORY_MODE_MAPPING) {
            $mappingModel = new Mapping_CategoryModel($row['ebay_category_mapping_id']);
            return L::t('Mapping').": ".$mappingModel->name;
        }

        return $row['ebay_primary_category_name'];
    }

}