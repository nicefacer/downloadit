<?php
/**
 * File Accounts.php
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

class Grids_LogSelling extends Grid
{
    protected $_sellingId = null;
    protected $_sellingProductId = null;

    public function __construct($sellingId, $sellingProductId)
    {
        $this->_gridId = "logSelling";
        $this->_multiSelect = false;
        $this->_defaultSort = 'date_add';
        $this->_defaultDir = 'desc';

        $this->_selectModel = new Log_SellingModel();
        
        $this->_primaryKeyName = "id";
        
        $this->_sellingId = $sellingId;
        $this->_sellingProductId = $sellingProductId;

        $this->setHeader(L::t("Log Selling List"));


        if (!is_null($this->_sellingId)) {
            $this->addButton("backToSelling", array(
                'value' => L::t("Back"),
                'name' => 'backToSelling',
                'class' => 'button button-back float-left',
                'style' => 'width: 75px;',
                'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/edit', array('id' => $sellingId)) . '"'
            ));
        } else {
            $this->addButton("backToSelling", array(
                'value' => L::t("Back"),
                'name' => 'backToSelling',
                'class' => 'button button-back float-left',
                'style' => 'width: 75px;',
                'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/index') . '"'
            ));
        }

        parent::__construct();
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        $this->getSelect()->addJoin("left", array("sp" => _DB_PREFIX_ . 'prestabay_selling_products'), array('product_name'), '`mt`.selling_product_id = `sp`.id');

        if (!is_null($this->_sellingId)) {
            $this->getSelect()->addFilter('`mt`.selling_id', (int)($this->_sellingId));
        }

        if (!is_null($this->_sellingProductId)) {
            $this->getSelect()->addFilter('`mt`.selling_product_id', (int)($this->_sellingProductId));
        }

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('product_name', array(
            'header' => L::t('Product'),
            'align' => 'left',
            'width' => '150px',
            'index' => 'product_name',
            'type' => 'sellinglogitem',
            'filterKey' => 'sp.product_name',
        ));

        $this->addColumn('message', array(
            'header' => L::t('Message'),
            'align' => 'left',
            'width' => '*',
            'index' => 'message',
        ));

        $this->addColumn('action', array(
            'header' => L::t('Action'),
            'align' => 'left',
            'width' => '60px',
            'index' => 'action',
            'type' => 'options',
            'options' => array(
                Log_SellingModel::LOG_ACTION_SEND => L::t('Send'),
                Log_SellingModel::LOG_ACTION_REVISE => L::t('Revise'),
                Log_SellingModel::LOG_ACTION_RELIST => L::t('Relist'),
                Log_SellingModel::LOG_ACTION_STOP => L::t('Stop'),
            ),
        ));

        $this->addColumn('level', array(
            'header' => L::t('Level'),
            'align' => 'left',
            'width' => '60px',
            'index' => 'level',
            'type' => 'options',
            'options' => array(
                Log_SellingModel::LOG_LEVEL_ERROR => L::t('Error'),
                Log_SellingModel::LOG_LEVEL_WARNING => L::t('Warning'),
                Log_SellingModel::LOG_LEVEL_NOTICE => L::t('Notice'),
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
        if (is_null($this->_sellingId) && is_null($this->_sellingProductId)) {
            return UrlHelper::getUrl("selling/itemsLog");
        }

        return UrlHelper::getUrl("selling/log", array('id' => $this->_sellingId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

}