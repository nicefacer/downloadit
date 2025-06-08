<?php
/**
 * File SellingEdit.php
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

class Grids_SellingEdit extends Grid
{
    protected $_sellingId = null;
    protected $_productLanguage = null;

    public function __construct($sellingId = null)
    {
        $this->_gridId = "sellings_products";
        $this->_multiSelect = true;
        $this->_selectModel = new Selling_ProductsModel();
        $this->_primaryKeyName = "id";
        $this->_sellingId = $sellingId;

        $listModel = new Selling_ListModel($sellingId);
        $this->_productLanguage = $listModel->language;

        $this->setHeader(sprintf(L::t("Selling List '%s' - Products"), $listModel->name));

        $this->initButtons($this, $sellingId, $listModel);

        parent::__construct();
    }

    public function initButtons(Grid $grid, $sellingId, $listModel)
    {
        $grid->addButton("appendProducts", array(
            'value' => '<i class="icon-plus icon-white"></i> '.L::t("Add Products"),
            'name' => 'appendProducts',
            'class' => 'button btn btn-primary btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/append', array('id' => $sellingId)) . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));

        $grid->addButton("editProfile", array(
            'value' => '<i class="icon-edit icon-white"></i> '.L::t("Edit Profile"),
            'name' => 'editProfile',
            'class' => 'button btn btn-primary btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('profiles/edit', array('id' => $listModel->profile)) . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));

        $grid->addButton("viewLog", array(
            'value' => '<i class="icon-list-alt icon-white"></i> '.L::t("View Log"),
            'name' => 'viewLog',
            'class' => 'button btn btn-primary btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/log', array('id' => $sellingId)) . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));


        $grid->addButton("submitRemove", array(
            'value' => '<i class="icon-remove icon-white"></i> '.L::t("Remove"),
            'name' => 'submitRemove',
            'class' => 'button btn btn-danger btn-small',
            'type' => 'button'
        ), array($grid, 'removeItemButton'));

        $grid->addButton("submitStop", array(
            'value' => '<i class="icon-stop icon-white"></i> '.L::t("Stop"),
            'name' => 'submitStop',
            'class' => 'button btn btn-danger btn-small',
            'type' => 'button'
        ), array($grid, 'stopItemButton'));

        $grid->addButton("submitRevise", array(
            'value' => '<i class="icon-refresh icon-white"></i> '.L::t("Revise"),
            'name' => 'submitRevise',
            'class' => 'button btn btn-success btn-small',
            'type' => 'button'
        ), array($grid, 'reviseItemButton'));

        $grid->addButton("submitRelist", array(
            'value' => '<i class="icon-retweet icon-white"></i> '.L::t("Relist"),
            'name' => 'submitRelist',
            'class' => 'button btn btn-success btn-small',
            'type' => 'button'
        ), array($grid, 'relistItemButton'));

        $grid->addButton("submitSendToEbay", array(
            'value' => '<i class="icon-share-alt icon-white"></i> '.L::t("Send to eBay"),
            'name' => 'submitSendToEbay',
            'class' => 'button btn btn-success btn-small',
            'type' => 'button'
        ), array($grid, 'sendItemButton'));
        
        $grid->addMassaction(L::t("Send to eBay All 'Not Active' Items"), UrlHelper::getUrl("selling/all", array('action' => 'send', 'id' => $sellingId)));
        $grid->addMassaction(L::t("Relist All 'Finished' Items"), UrlHelper::getUrl("selling/all", array('action' => 'relist', 'id' => $sellingId)));
        $grid->addMassaction(L::t("Revise All 'Active' Items"), UrlHelper::getUrl("selling/all", array('action' => 'revise', 'id' => $sellingId)));

        $grid->addFooterButton("submitDuplicate", array(
            'value' => L::t("Duplicate"),
            'name' => 'submitDuplicate',
            'class' => 'button btn btn-success btn-small controll-button',
            'type' => 'button',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/new', array('duplicateId' => $sellingId)) . '"',
            'inlineButton' => true,
        ));

        $grid->addFooterButton("submitPriceQtyRevise", array(
            'value' => L::t("Price & QTY Revise"),
            'name' => 'submitPriceQtyRevise',
            'class' => 'button btn btn-success btn-small controll-button',
            'type' => 'button'
        ), array($grid, 'priceQtyReviseButton'));

        $sm = new Selling_ListModel();

        $grid->addFooterButton("submitMove", array(
            'value' => L::t("Move to another Selling List"),
            'name' => 'submitMove',
            'class' => 'button btn btn-success btn-small dropdown-toggle',
            'items' => $sm->getSelect()->getItems(),
            'type' => 'button',
            'onclick' => 'showSellingSelection()',
            'inlineButton' => true,
            'data-toggle'=> "dropdown",
        ), array($grid, 'moveItemsButton'));



        $grid->addFooterButton("editSellingList", array(
            'value' => L::t("Edit 'Selling List' Details"),
            'name' => 'editSellingList',
            'class' => 'button btn btn-success btn-small',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/editDetails', array('id' => $sellingId)) . '"',
            'inlineButton' => true,
        ));

        $grid->addHidden('moveTo', array(
            'name' => 'moveTo',
        ));
    }

    protected function  _prepareCollection()
    {
        $this->getSelect()->addFilter('mt.`selling_id`', (int)($this->_sellingId));

        $shopSQLCondition = "";
        if (CoreHelper::isPS15()) {
            if (Shop::isFeatureActive() && Shop::getTotalShops(false, null) > 1) {
                $shopId = (int) Shop::getContextShopID();
                if ($shopId == 0) {
                    $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
                }
                $shopSQLCondition = " AND pl.id_shop = $shopId";
            }
        }

        $this->getSelect()->addJoin(
            'left',
            array('pl' => _DB_PREFIX_.'product_lang'),
            array('name', 'link_rewrite'),
            "mt.`product_id` = pl.`id_product` AND pl.id_lang = " . $this->_productLanguage . $shopSQLCondition);

        $this->getSelect()->addJoin(
            'left',
            array('p' => _DB_PREFIX_ . 'product'),
            array('reference'),
            "mt.`product_id` = p.`id_product`"
        );
        $this->getSelect()->addJoin('left', array('pi' => _DB_PREFIX_.'image'), array('id_image', 'id_product'), "mt.`product_id` = pi.`id_product` AND pi.cover = 1");
        $this->getSelect()->addJoin('left', array('pai' => _DB_PREFIX_.'product_attribute_image'), array('attr_img' => 'id_image'), "mt.`product_id_attribute` = pai.`id_product_attribute`");
        $this->getSelect()->addJoin('left', array('pa' => _DB_PREFIX_.'product_attribute'), array('attr_reference' => 'reference'), "mt.`product_id_attribute` = pa.`id_product_attribute`");

        $this->getSelect()->groupBy('`mt`.id');

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
//
//        $this->addColumn('id_image',
//            array(
//                'header'    => L::t('Photo'),
//                'type'      => 'productImage',
//                'align'     => 'center',
//                'width'     => '70px',
//                'index'     => 'id_image',
//                'filterKey' => 'pi.id_image',
//                'filtrable' => false,
//            )
//        );

        $this->addColumn(
            'product_id',
            array(
                'header'    => L::t('Product'),
                'type'      => 'product',
                'align'     => 'left',
                'width'     => '*',
                'filterKey' => 'pl.name',
                'index'     => 'pl.name',
            )
        );

        $this->addColumn(
            'reference',
            array(
                'header'    => L::t('SKU'),
                'type'      => 'reference',
                'align'     => 'left',
                'width'     => '140px',
                'filterKey' => 'p.reference',
                'index'     => 'reference',
            )
        );

        $this->addColumn('product_qty', array(
            'header' => L::t('QTY'),
            'align' => 'left',
            'width' => '50px',
            'index' => 'product_qty',
        ));

        $this->addColumn('ebay_price', array(
            'header' => L::t('eBay Price'),
            'align' => 'left',
            'width' => '70px',
            'index' => 'ebay_price',
        ));

        $this->addColumn('ebay_qty', array(
            'header' => L::t('eBay QTY'),
            'align' => 'left',
            'width' => '70px',
            'type' => 'ebayQty',
            'index' => 'ebay_qty',
        ));

        $this->addColumn('status', array(
            'header' => L::t('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                Selling_ProductsModel::STATUS_NOT_ACTIVE => L::t('Not Active'),
                Selling_ProductsModel::STATUS_ACTIVE => L::t('Active'),
                Selling_ProductsModel::STATUS_FINISHED => L::t('Finished'),
                Selling_ProductsModel::STATUS_STOPED => L::t('Stopped'),
            ),
        ));

        $this->addColumn('action',
                array(
                    'header' => L::t('Action'),
                    'width' => '85px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => '<i class="icon-zoom-in"></i>',
                            'title' => L::t('View On eBay'),
                            'url' => 'selling/ebayView/selling/' . $this->_sellingId,
                            'field' => 'item',
                            'bootstrap_icon' => true,
                        ),
                        array(
                            'caption' => '<i class="icon-list-alt"></i>',
                            'url' => 'selling/log/id/'. $this->_sellingId,
                            'field' => 'productId',
                            'title' => L::t('Log'),
                            'bootstrap_icon' => true,
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'is_system' => true,
        ));

        parent::_prepareColumns();
    }

//    public function getSelect()
//    {
//        return $this->_selectModel->filterBySelling($this->_sellingId);
//    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("selling/edit", array('id' => $this->_sellingId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

}