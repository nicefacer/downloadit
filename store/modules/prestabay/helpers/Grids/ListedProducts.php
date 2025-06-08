<?php

/**
 * File ListedProducts.php
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
 * @copyright   Copyright (c) 2011-2016 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Grids_ListedProducts extends Grids_Products
{
    protected $_productLanguage = null;

    public function __construct($productLanguage = 3)
    {
        $this->_gridId = "listedProducts";
        $this->_multiSelect = false;
        $this->_selectModel = new Product();
        $this->_primaryKeyName = "id_product";
        $this->_productLanguage = $productLanguage;

        $this->setHeader(L::t("Product Listed to eBay"));

        $this->setDefaultDir("ASC");
        $this->setDefaultSort("id_product");

        $this->addButton("backToSelling", array(
            'value' => L::t("Back"),
            'name' => 'backToSelling',
            'class' => 'button button-back float-left',
            'style' => 'width: 75px;',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/index') . '"'
        ));

        $this->init();
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        $this->addColumn('ebay_count', array(
            'header' => L::t('In Selling Lists'),
            'align' => 'left',
            'width' => '30px',
            'index' => 'ebay_count',
            'filtrable' => false,
        ));
    }

    protected function generateSql()
    {
        parent::generateSql();

        $this->getSelect()->addJoin('left', array('sp' => _DB_PREFIX_ . 'prestabay_selling_products'), array('ebay_count' => 'count(sp.id)'), "mt.`id_product` = sp.`product_id`", true);
        $this->getSelect()->groupBy('`mt`.id_product');
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("selling/listedProducts");
    }

}
