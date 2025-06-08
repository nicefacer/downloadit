<?php
/**
 * File Orders.php
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

class Grids_OrdersItems extends Grid
{

    protected $_orderId = null;

    public function __construct($orderId)
    {
        $this->_orderId = $orderId;
        $this->_gridId = "ordersItems";
        $this->_multiSelect = false;
        $this->_selectModel = new Order_OrderItemsModel();
        $this->_primaryKeyName = "id";
        $this->_shortView = true;
//        $this->_defaultSort = 'id';
//        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("Order Items"));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('title', array(
            'header' => L::t('Product'),
            'align' => 'left',
            'width' => '*',
            'index' => 'title',
            'type' => 'orderProductTitle'
        ));

        $this->addColumn('price', array(
            'header' => L::t('Up'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'currency',
            'index' => 'price',
            'currency_column' => 'currency'
        ));

        $this->addColumn('qty', array(
            'header' => L::t('Qty'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'qty',
        ));

        $this->addColumn('total', array(
            'header' => L::t('Total'),
            'align' => 'left',
            'width' => '200px',
            'type' => 'orderItemTotal',
            'currency_column' => 'currency',
            'filter' => false,
            'sortable' => false,
        ));
//
//        $this->addColumn('paid', array(
//            'header' => 'Total',
//            'align' => 'left',
//            'width' => '60px',
//            'index' => 'paid',
//            'type' => 'currency',
//            'currency_column' => 'currency'
//        ));

        parent::_prepareColumns();
    }

    protected function  _prepareCollection()
    {
        $this->getSelect()->addFilter('mt.`order_id`', (int)$this->_orderId);
        parent::_prepareCollection();
    }

}