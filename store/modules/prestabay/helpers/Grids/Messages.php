<?php
/**
 * File Messages.php
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

class Grids_Messages extends Grid
{
    protected $_accountId = false;

    public function __construct()
    {
        $this->_gridId = "messages";
        $this->_multiSelect = false;
        $this->_defaultSort = 'date_upd';
        $this->_defaultDir = 'desc';

        $this->_selectModel = new Messages_MessagesModel();

        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Messages"));

        parent::__construct();
    }

    protected function  _prepareCollection()
    {
        if ($this->_accountId > 0) {
            $this->getSelect()->addFilter('mt.`account_id`', $this->_accountId);
        }
        $this->getSelect()->addJoin("left", array("pa" => _DB_PREFIX_ . 'prestabay_accounts'), array('mode'), '`mt`.account_id = `pa`.id');

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('title', array(
                'header' => L::t('Item'),
                'align' => 'left',
                'width' => '150px',
                'index' => 'title',
                'type' => 'feedbackitem'
            ));

        $this->addColumn('subject', array(
                'header' => L::t('Subject'),
                'align' => 'left',
                'width' => '350px',
                'index' => 'subject',
            ));

        $this->addColumn('text', array(
                'header' => L::t('Text'),
                'align' => 'left',
                'index' => 'text',
                'type' => 'shorttext',
                'length' => '60'

            ));


        $this->addColumn('status', array(
                'header' => L::t('Status'),
                'align' => 'status',
                'width' => '60px',
                'index' => 'status',
                'type' => 'options',
                'options' => array(
                    Messages_MessagesModel::MESSAGE_STATUS_ANSWERED => "<span class='message_status' style='color: #00aa00;'>".L::t('Answered')."</span>",
                    Messages_MessagesModel::MESSAGE_STATUS_UNANSWERED => "<span class='message_status' style='color: red;'>".L::t('Unanswered')."</span>",
                ),
            ));

        $this->addColumn('date', array(
                'header' => L::t('Date'),
                'align' => 'left',
                'width' => '120px',
                'type' => 'datetime',
                'index' => 'date',
            ));

        $this->addColumn('action', array(
                'header' => L::t('Response'),
                'align' => 'left',
                'width' => '100px',
                'type' => 'messagesresponse',
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
        ));

        parent::_prepareColumns();
    }

    public function setAccountFilter($accountId)
    {
        $this->_accountId = $accountId > 0 ? $accountId : false;
    }


    public function getGridUrl()
    {
        return UrlHelper::getUrl("messages/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}