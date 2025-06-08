<?php

/**
 * File CategorynameRenderer.php
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
class Grid_CategorynameRenderer extends Grid_AbstractRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        if ($row['mode'] == Selling_ListModel::MODE_PRODUCT) {
            return '';
        }

        $categoriesIds = Selling_CategoriesModel::getCategoriesMapped($row['id']);

        $totalFullOut = "";
        foreach ($categoriesIds as $singleCategoryId) {
            $totalFullOut != "" && $totalFullOut .= "<br/>";

            $categoryModel = new Category($singleCategoryId);
            $totalOut = "";
            if (!$categoryModel->id) {
                $totalOut = 'N/A';
            } else if ($categoryModel->id == 1) {
                $totalOut = 'Home';
            } else {
                $list = $categoryModel->getParentsCategories();
                foreach ($list as $single) {
                    $totalOut != "" && $totalOut = "->" . $totalOut;
                    $totalOut = $single['name'] . $totalOut;
                }
            }
            $totalFullOut .= $totalOut;
        }

        return $totalFullOut;
    }

    public function filter($fieldKey = null, $value = null, $config = array(), $grid = null)
    {
        return '';
    }

}