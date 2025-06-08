<?php
/**
 * File Products.php
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

class Grids_Products extends Grid
{

    protected $_productLanguage = null;

    public function __construct($productLanguage = 3)
    {
        $this->_gridId = "products";
        $this->_multiSelect = true;
        $this->_selectModel = new Product();
        $this->_primaryKeyName = "id_product";
        $this->_productLanguage = $productLanguage;

        $this->setHeader(L::t("New Selling List - Products"));

        $this->addButton("submitAddSelected", array(
            'value' => '<i class="icon-plus icon-white"></i> '.L::t("Add Selected"),
            'name' => 'submitAddSelected',
            'class' => 'button btn btn-success btn-small',
            'type' => 'button',
            'onclick' => 'jQuery("#products").append("<input type=hidden name=addProduct value=true/>");jQuery("#products").submit()'
        ));

        $this->setDefaultDir("ASC");
        $this->setDefaultSort("id_product");

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn(
            'id_product',
            array(
                'header'    => 'ID',
                'align'     => 'right',
                'width'     => '50px',
                'index'     => 'id_product',
                'filtrable' => false,
            )
        );


        $this->addColumn(
            'id_image',
            array(
                'header'    => L::t('Photo'),
                'type'      => 'productImage',
                'align'     => 'center',
                'width'     => '70px',
                'index'     => 'id_image',
                'filterKey' => 'pi.id_image',
                'filtrable' => false,
            )
        );


        $this->addColumn('name', array(
            'header' => L::t('Product Name'),
            'align' => 'left',
            'width' => '*',
            'index' => 'name',
            'filterKey' =>  'pl.name',
//            'filtrable' => true,
        ));

        $this->addColumn('reference', array(
            'header' => L::t('Reference'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'reference',
        ));

        if (CoreHelper::isPS15()) {
            $this->addColumn('stock_qty', array(
                'header' => L::t('QTY'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'stock_qty',
                'filterKey' => 'sl.quantity'
            ));
        } else {
            $this->addColumn('qty', array(
                'header' => L::t('QTY'),
                'align' => 'left',
                'width' => '80px',
                'index' => 'quantity',
                'filtrable' => false,
            ));
        }

        $this->addColumn('price', array(
            'header' => L::t('Price'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'price',
            'type' => 'productPrice',
            'product_id_column' => 'id_product',
            'filtrable' => false,
        ));
    }

    /**
     *
     * @var PrestaSelect
     */
    protected $_createadPrestaSelect = null;

    public function  getSelect()
    {
        if (is_null($this->_createadPrestaSelect)) {
            return $this->_createadPrestaSelect = new PrestaSelect('product');
        }

        return $this->_createadPrestaSelect;
    }

    /**
     * Generate SQL query for grid
     */
    protected function generateSql()
    {
        $this->getSelect()->addJoin('left', array('pl' => _DB_PREFIX_.'product_lang'), array('name', 'link_rewrite'), "mt.`id_product` = pl.`id_product`");
        $this->getSelect()->addJoin('left', array('pi' => _DB_PREFIX_.'image'), array('id_image'), "mt.`id_product` = pi.`id_product` AND pi.cover = 1");
        $this->getSelect()->addFilter('pl.`id_lang`', (int)($this->_productLanguage));
        if (CoreHelper::isPS15()) {
            $this->getSelect()->addJoin('left', array('sl' => _DB_PREFIX_.'stock_available'), array('stock_qty' => 'quantity'), "mt.`id_product` = sl.`id_product`");
            $this->getSelect()->addFilter('sl.`id_product_attribute`', 0);
            if (Shop::isFeatureActive() && Shop::getTotalShops(false, null) > 1) {
                $shopId = (int)Shop::getContextShopID();
                if ($shopId == 0) {
                    $shopId = (int)Configuration::get('PS_SHOP_DEFAULT');
                }
                $this->getSelect()->addFilter('pl.id_shop', $shopId);
            }
        }
    }

    protected function _prepareCollection()
    {
        $this->generateSql();

        return parent::_prepareCollection();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("selling/save");
    }

}
