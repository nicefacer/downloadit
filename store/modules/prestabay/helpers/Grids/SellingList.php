<?php
/**
 * File SellingList.php
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

class Grids_SellingList extends Grid
{

    const MASSACTION_FULL_REVISE = "fullRevise";
    const MASSACTION_QTY_REVISE = "qtyRevise";
    const MASSACTION_PRICE_REVISE = "priceRevise";
    const MASSACTION_QTY_PRICE_REVISE = "qtyPriceRevise";
    const MASSACTION_STOP_ALL_ACTIVE = "stopAllRevise";
    const MASSACTION_STOP_ZERO_QTY = "stopZeroRevise";
    const MASSACTION_RELIST_AVAILABLE = "relistAvailableRevise";

    public function __construct()
    {
        $this->_gridId = "sellings_list";
        $this->_multiSelect = true;
        $this->_selectModel = new Selling_ListModel();
        $this->_primaryKeyName = "id";
        $this->setHeader(L::t("Selling List"));

        $this->addButton("viewItemsLogButton", array(
            'value' => '<i class="icon-list-alt icon-white"></i> '.L::t("View Items Log"),
            'name' => 'viewItemsLog',
            'class' => 'button btn btn-primary btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/itemsLog') . '"',
            'target' => '_blank'
        ));

        $this->addButton("viewListedProductsButton", array(
            'value' => '<i class="icon-magnet icon-white"></i> '.L::t("Products In Selling Lists"),
            'name' => 'viewProductsInSellingLists',
            'class' => 'button btn btn-primary btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/listedProducts') . '"',
            'target' => '_blank'
        ));


        $this->addButton("newSellingButton", array(
            'type' => 'button',
            'class' => 'button btn btn-success btn-small',
            'value' => '<i class="icon-plus icon-white"></i> '.L::t('New List'),
            'name' => 'newSellingButton',
            'onclick' => 'document.location="' . UrlHelper::getUrl("selling/new") . '"'
        ));

        $this->_massactionType = Grid::MASSACTION_TYPE_SUBMIT;

        $this->addMassaction(L::t("Full Revise active eBay Listing"), self::MASSACTION_FULL_REVISE);
        $this->addMassaction(L::t("QTY Revise active eBay Listing"), self::MASSACTION_QTY_REVISE);
        $this->addMassaction(L::t("Price Revise active eBay Listing"), self::MASSACTION_PRICE_REVISE);
        $this->addMassaction(L::t("QTY & Price Revise active eBay Listing"), self::MASSACTION_QTY_PRICE_REVISE);
        $this->addMassaction(L::t("Stop active eBay Listing"), self::MASSACTION_STOP_ALL_ACTIVE);
        $this->addMassaction(L::t("Stop with QTY 0"), self::MASSACTION_STOP_ZERO_QTY);
        $this->addMassaction(L::t("Relist with positive QTY"), self::MASSACTION_RELIST_AVAILABLE);


        parent::__construct();
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        $this->getSelect()->addJoin("left", array("pp" => _DB_PREFIX_ . 'prestabay_profiles'), array('profile_name'), '`mt`.profile = `pp`.id');
        $this->getSelect()->addJoin("left", array("pl" => _DB_PREFIX_ . 'lang'), array('lang_name' => 'name'), '`mt`.language = `pl`.id_lang');

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => L::t('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        $this->addColumn('name', array(
            'header' => L::t('Name'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'name',
        ));

        $this->addColumn('mode', array(
            'header' => L::t('Mode'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'mode',
            'type' => 'options',
            'options' => array(
                Selling_ListModel::MODE_PRODUCT => L::t('Products'),
                Selling_ListModel::MODE_CATEGORY => L::t('Category'),
            ),
        ));

        $this->addColumn('category_id', array(
            'header' => L::t('Category'),
            'align' => 'right',
            'width' => '*',
            'index' => 'category_id',
            'type' => 'categoryname',
            'filtrable' => false,
        ));

        $this->addColumn('language', array(
            'header' => L::t('Language'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'lang_name',
            'filtrable' => false,
        ));

        $this->addColumn('profile', array(
            'header' => L::t('Profile'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'profile_name',
            'filter' => 'profile',
            'filtrable' => false,
        ));

        $this->addColumn('action',
                array(
                    'header' => L::t('Action'),
                    'width' => '120px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => '<i class="icon-pencil"></i>',
                            'url' => 'selling/edit',
                            'field' => 'id',
                            'title' => L::t('Edit'),
                            'bootstrap_icon' => true,
                        ),
                        array(
                            'caption' => '<i class="icon-list-alt"></i>',
                            'url' => 'selling/log',
                            'field' => 'id',
                            'title' => L::t('Log'),
                            'bootstrap_icon' => true,
                        ),
                        array(
                            'caption' => '<i class="icon-trash"></i>',
                            'confirm' => L::t('Delete Selling?'),
                            'url' => 'selling/delete',
                            'field' => 'id',
                            'title' =>  L::t('Delete'),
                            'bootstrap_icon' => true,
                        ),
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'is_system' => true,
        ));

        parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("selling/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}