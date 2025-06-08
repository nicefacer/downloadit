<?php
/**
 * File Feedback.php
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

class Grids_Feedbacks extends Grid
{
    protected $_accountId = false;

    public function __construct()
    {
        $this->_gridId = "feedback";
        $this->_multiSelect = false;
        $this->_defaultSort = 'date_upd';
        $this->_defaultDir = 'desc';

        $this->_selectModel = new Feedbacks_FeedbacksModel();

        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Feedback"));

        parent::__construct();
    }

    protected function  _prepareCollection()
    {
        if ($this->_accountId > 0) {
            $this->getSelect()->addFilter('mt.`account_id`', $this->_accountId);
        }
        $this->getSelect()->addJoin("left", array("pa" => _DB_PREFIX_ . 'prestabay_accounts'), array('mode'), '`mt`.account_id = `pa`.id');
        $this->getSelect()->addJoin("left", array("pt" => _DB_PREFIX_ . 'prestabay_order_items'), array('order_id'), '`mt`.transaction_id = `pt`.transaction_id AND `mt`.transaction_id > 0');

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('title', array(
                'header' => L::t('Item'),
                'align' => 'left',
                'width' => '250px',
                'index' => 'title',
                'type' => 'feedbackitem'
            ));

        $this->addColumn('transaction_id', array(
                'header' => L::t('Transaction'),
                'align' => 'left',
                'width' => '120px',
                'index' => 'transaction_id',
                'type' => 'feedbacktransaction'
            ));

        $this->addColumn('feedback', array(
                'header' => L::t('Feedback'),
                'align' => 'left',
                'width' => '*',
                'sortable' => false,
                'filtrable' => false,
                'type' => 'feedback',
            ));

        $this->addColumn('buyer_time', array(
                'header' => L::t('Buyer  Date'),
                'align' => 'left',
                'width' => '120px',
                'type' => 'datetime',
                'index' => 'buyer_time',
            ));

        $this->addColumn('seller_time', array(
                'header' => L::t('Seller Date'),
                'align' => 'left',
                'width' => '120px',
                'type' => 'datetime',
                'index' => 'seller_time',
            ));

        parent::_prepareColumns();
    }

    public function setAccountFilter($accountId)
    {
        $this->_accountId = $accountId > 0 ? $accountId : false;
    }


    public function getGridUrl()
    {
        return UrlHelper::getUrl("feedback/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}