<?php
/**
 * File FullOrdersLog.php
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

class Grids_FullOrdersLog extends Grid
{
    public function __construct()
    {
        $this->_gridId = "fullOrdersLog";
        $this->_multiSelect = false;
        $this->_selectModel = new Order_LogModel();
        $this->_primaryKeyName = "id";
        $this->_defaultSort = 'date_add';
        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("Order Log"));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('order_id', array(
            'header' => L::t('Order ID'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'order_id',
            'filterKey' => 'po.order_id',
        ));

        $this->addColumn('message', array(
            'header' => L::t('Message'),
            'align' => 'left',
            'width' => '*',
            'type' => 'text',
            'index' => 'message',
        ));

        $this->addColumn('date', array(
            'header' => L::t('Date'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'date_add',
            'type' => 'datetime'
        ));


        $this->addColumn('action',
            array(
                'header' => L::t('Action'),
                'width' => '50px',
                'type' => 'action',
                'getter' => 'getOrderId',
                'actions' => array(
                    array(
                        'caption' => L::t('View'),
                        'url' => 'order/view',
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            ));

        parent::_prepareColumns();
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        $this->getSelect()->addJoin("left", array("po" => _DB_PREFIX_ . 'prestabay_order'), array('order_id'), '`mt`.prestabay_order_id = `po`.id');

        parent::_prepareCollection();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("order/log");
    }

    public function getOrderId($row)
    {
        return $row['prestabay_order_id'];
    }
}
