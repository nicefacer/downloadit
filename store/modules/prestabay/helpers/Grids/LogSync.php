<?php
/**
 * File LogSync.php
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

class Grids_LogSync extends Grid
{

    public function __construct()
    {
        $this->_gridId = "logSync";
        $this->_multiSelect = false;
        $this->_defaultSort = 'date_add';
        $this->_defaultDir = 'desc';

        $this->_selectModel = new Log_SyncModel();

        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Synchronization Log"));


        $this->addButton("backToSync", array(
            'value' => L::t("Back"),
            'name' => 'backToSync',
            'class' => 'button button-back',
            'style' => 'width: 75px;',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('synchronization/index') . '"'
        ));

        parent::__construct();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('message', array(
            'header' => L::t('Message'),
            'align' => 'left',
            'width' => '*',
            'index' => 'message',
        ));

        $this->addColumn('object', array(
            'header' => L::t('Object'),
            'align' => 'left',
            'width' => '200px',
            'sortable' => false,
            'filtrable' => false,
            'type' => 'synchData',
        ));

        $this->addColumn('task', array(
            'header' => L::t('Sync Task'),
            'align' => 'left',
            'width' => '60px',
            'index' => 'task',
            'type' => 'options',
            'options' => array(
                Log_SyncModel::LOG_TASK_DEFAULT => L::t('Default'),
                Log_SyncModel::LOG_TASK_STOCK_LEVEL => L::t('Stock Level'),
                Log_SyncModel::LOG_TASK_RELIST => L::t('Relist'),
                Log_SyncModel::LOG_TASK_END => L::t('End'),
		        Log_SyncModel::LOG_TASK_ORDER => L::t('Order'),
                Log_SyncModel::LOG_TASK_PRICE => L::t('Price'),
                Log_SyncModel::LOG_TASK_UNKNOWN => L::t('Unknown'),
                Log_SyncModel::LOG_TASK_RESYNCHRONIZE_QTY =>  L::t('Resynchronize QTY'),
                Log_SyncModel::LOG_TASK_RESYNCHRONIZE_PRICE =>  L::t('Resynchronize Price'),
                Log_SyncModel::LOG_TASK_RESYNCHRONIZE_CATALOG =>  L::t('Resynchronize Catalog'),
                Log_SyncModel::LOG_TASK_LIST =>  L::t('List'),
                Log_SyncModel::LOG_TASK_FEEDBACKS =>  L::t('Import Feedbacks'),
                Log_SyncModel::LOG_TASK_FEEDBACKS_AUTO =>  L::t('Auto Feedback'),
                Log_SyncModel::LOG_TASK_MESSAGES =>  L::t('Messages'),
                Log_SyncModel::LOG_TASK_FULL_REVISE =>  L::t('Full Revise'),
            ),
        ));

        $this->addColumn('level', array(
            'header' => L::t('Level'),
            'align' => 'left',
            'width' => '60px',
            'index' => 'level',
            'type' => 'options',
            'options' => array(
                Log_SyncModel::LOG_LEVEL_ERROR => L::t('Error'),
                Log_SyncModel::LOG_LEVEL_WARNING => L::t('Warning'),
                Log_SyncModel::LOG_LEVEL_NOTICE => L::t('Notice'),
            ),
        ));

        $this->addColumn('date_add', array(
            'header' => L::t('Date'),
            'align' => 'center',
            'width' => '100px',
            'type' => 'datetime',
            'index' => 'date_add',
        ));

        parent::_prepareColumns();
    }


    public function getGridUrl()
    {
        return UrlHelper::getUrl("synchronization/log");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}