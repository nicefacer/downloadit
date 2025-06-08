<?php
/**
 * File OrdersLog.php
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

class Grids_OrdersLog extends Grid
{

    protected $_orderId = null;

    public function __construct($orderId)
    {
        $this->_orderId = $orderId;
        $this->_gridId = "ordersLog";
        $this->_multiSelect = false;
        $this->_selectModel = new Order_LogModel();
        $this->_primaryKeyName = "id";
        $this->_shortView = true;
        $this->_defaultSort = 'id';
        $this->_defaultDir = 'asc';

        $this->setHeader(L::t("Log"));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => L::t('#'),
            'align' => 'left',
            'width' => '10px',
            'index' => 'id',
        ));

        $this->addColumn('title', array(
            'header' => L::t('Date'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'date_add',
            'type' => 'datetime'
        ));

        $this->addColumn('message', array(
            'header' => L::t('Message'),
            'align' => 'left',
            'width' => '*',
            'type' => 'text',
            'index' => 'message',

        ));

        parent::_prepareColumns();
    }

    protected function  _prepareCollection()
    {
        $this->getSelect()->addFilter('mt.`prestabay_order_id`', $this->_orderId);
        parent::_prepareCollection();
    }

}