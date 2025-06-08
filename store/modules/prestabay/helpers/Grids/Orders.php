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

class Grids_Orders extends Grid
{

    public function __construct()
    {
        $this->_gridId = "orders";
        $this->_multiSelect = false;
        $this->_selectModel = new Order_OrderModel();
        $this->_primaryKeyName = "id";
        $this->_defaultSort = 'create_date';
        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("eBay Order List"));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header' => L::t('Order #'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'order_id',
        ));
        $this->addColumn('presta_order_id', array(
            'header' => L::t('Presta'),
            'align' => 'left',
            'width' => '40px',
            'filter' => false,
            'index' => 'presta_order_id',
            'type' => 'prestaOrder',
        ));

        $this->addColumn('items', array(
            'header' => L::t('Items'),
            'align' => 'left',
            'width' => '*',
            'filter' => false,
            'type' => 'ebayOrderItems',
            'sortable' => false,
        ));

        $this->addColumn('paid', array(
            'header' => L::t('Total'),
            'align' => 'left',
            'width' => '60px',
            'index' => 'paid',
            'type' => 'currency',
            'currency_column' => 'currency'
        ));

        $this->addColumn('buyer_name', array(
            'header' => L::t('Buyer Name'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'buyer_name',
        ));

        $this->addColumn('status', array(
            'header' => L::t('Status'),
            'align' => 'left',
            'width' => '100px',
            'filter' => false,
            'sortable' => false,
            'type' => 'ebayOrderStatus',
        ));

        $this->addColumn('create_date', array(
            'header' => L::t('Date'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'create_date',
            'type' => 'datetime',
        ));

        $this->addColumn('action',
                array(
                    'header' => L::t('Action'),
                    'width' => '50px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => L::t('View'),
                            'url' => 'order/view',
                            'field' => 'id',
                            'icon' => 'details.gif'
                        )
                    ),
                    'filter' => false,
                    'sortable' => false,
                    'is_system' => true,
        ));

        parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("order/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}