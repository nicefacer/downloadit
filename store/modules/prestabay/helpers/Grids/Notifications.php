<?php
/**
 * File Notifications.php
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

class Grids_Notifications extends Grid
{

    public function __construct()
    {
        $this->_gridId = "notifications";
        $this->_multiSelect = true;
        $this->_selectModel = new NotificationsModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Notifications List"));

        $this->_defaultSort = '`mt`.`date`';
        $this->_defaultDir = "desc";

        parent::__construct();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => L::t('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        $this->addColumn('title', array(
            'header' => L::t('Title'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'title',
        ));

        $this->addColumn('message', array(
            'header' => L::t('Message'),
            'align' => 'left',
            'type' => 'text',
            'width' => '*',
            'index' => 'message',
            'length' => 40,
            'removeHtml' => true,
        ));

        $this->addColumn('date', array(
            'header' => L::t('Date'),
            'width' => '80px',
            'type' => 'date'
        ));

        $this->addColumn('level', array(
            'header' => L::t('Level'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'level',
            'type' => 'options',
            'options' => array(
                NotificationsModel::LEVEL_NOTICE => L::t('Notice'),
                NotificationsModel::LEVEL_WARNING => L::t('Warning'),
                NotificationsModel::LEVEL_ERROR => L::t('Error')
            ),
        ));

        $this->addColumn('is_read', array(
            'header' => L::t('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'is_read',
            'type' => 'options',
            'options' => array(
                NotificationsModel::READ_NO => L::t('New'),
                NotificationsModel::READ_YES => L::t('Read')
            ),
        ));


        $this->addColumn('action',
            array(
                'header' => L::t('Action'),
                'width' => '50px',
                'align' => 'center',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => L::t('View'),
                        'url' => 'notification/view',
                        'field' => 'id',
                        'icon' => 'edit.gif'
                    ),
                    array(
                        'caption' => L::t('Mark as Read'),
                        'url' => 'notification/markAsReadGrid',
                        'field' => 'id',
                        'icon' => 'ok.gif'
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
        return UrlHelper::getUrl("notification/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

    protected function  _prepareCollection()
    {
        parent::_prepareCollection();
    }
}