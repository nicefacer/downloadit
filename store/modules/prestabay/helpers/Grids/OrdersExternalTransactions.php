<?php
/**
 * File OrdersExternalTransactions.php
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

class Grids_OrdersExternalTransactions extends Grid
{

    protected $_orderId = null;

    public function __construct($orderId)
    {
        $this->_orderId = $orderId;
        $this->_gridId = "ordersExtTransactions";
        $this->_multiSelect = false;
        $this->_selectModel = new Order_ExternalTransactionsModel();
        $this->_primaryKeyName = "id";
        $this->_shortView = true;
        $this->_defaultSort = 'time';
        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("External Transactions"));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('transaction_id', array(
            'header' => L::t('Transaction'),
            'align' => 'left',
            'width' => '*',
            'index' => 'transaction_id',
        ));

        $this->addColumn('total', array(
            'header' => L::t('Total'),
            'align' => 'right',
            'width' => '70px',
            'type' => 'text',
            'index' => 'total',
        ));

        $this->addColumn('fee', array(
            'header' => L::t('Fee'),
            'align' => 'right',
            'width' => '70px',
            'type' => 'text',
            'index' => 'fee',
        ));

        $this->addColumn('time', array(
            'header' => L::t('Date'),
            'align' => 'left',
            'width' => '150px',
            'type' => 'datetime',
            'index' => 'time',
        ));
        parent::_prepareColumns();
    }

    protected function  _prepareCollection()
    {
        $this->getSelect()->addFilter('mt.`prestabay_order_id`', $this->_orderId);
        parent::_prepareCollection();
    }

}