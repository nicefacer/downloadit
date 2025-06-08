<?php
/**
 * File EbayListings.php
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

class Grids_EbayListings extends Grid
{
    protected $_accountId = false;

    public function __construct()
    {
        $this->_gridId = "ebay_listings";
        $this->_multiSelect = true;
        $this->_selectModel = new EbayListingsModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("eBay Listings"));

        $this->addButton("submitDownload", array(
            'value' => L::t("Download eBay Listings"),
            'name' => 'submitDownload',
            'class' => 'button btn btn-success btn-small float-left',
            'type' => 'button'
        ));

        $this->addButton("submitStop", array(
            'value' => '<i class="icon-stop icon-white"></i> '.L::t("Stop"),
            'name' => 'submitStop',
            'class' => 'button btn btn-danger btn-small',
            'type' => 'button'
        ));

        $this->addButton("submitRelist", array(
            'value' => '<i class="icon-retweet icon-white"></i> '.L::t("Relist"),
            'name' => 'submitRelist',
            'class' => 'button btn btn-success btn-small',
            'type' => 'button'
        ));

        $this->_massactionType = self::MASSACTION_TYPE_REDIRECT;

        $this->addMassaction(L::t("Move to  Selling List"), 'javascript:showSellingSelection');
        $this->addMassaction(L::t("Find Product by Title & SKU"),  'javascript:submitAutodetect');
        $this->addMassaction(L::t("Find All Unmapped"),  'javascript:submitAutodetectAll');

        $this->addHidden('moveTo', array(
            'name' => 'moveTo',
        ));

        parent::__construct();
    }


    protected function  _prepareCollection()
    {
        if ($this->_accountId > 0) {
            $this->getSelect()->addFilter('mt.`account_id`', $this->_accountId);
        }
        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('item_id', array(
            'header' => L::t('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'item_id',
            'type' => 'ebaylistingitemid'
        ));

        $this->addColumn('product_id', array(
            'header' => L::t('PS Product'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_id',
            'type' => 'productview'
        ));

        $this->addColumn('title', array(
            'header' => L::t('Title'),
            'align' => 'left',
            'width' => '*',
            'index' => 'title',
        ));

        $this->addColumn('qty_available', array(
            'header' => L::t('QTY'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'qty_available',
            'type' => 'ebaylistingqty',
            'filter' => 'qty_available',
        ));
        
        $this->addColumn('buy_price', array(
            'index' => 'buy_price',
            'header' => L::t('Price'),
            'align' => 'left',
            'width' => '80px',
            'type' => 'currency',
            'currency_column' => 'currency',
        ));

        $this->addColumn('start_time', array(
            'header' => L::t('Start time'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'start_time',
            'type' => 'ebaylistingduration',
        ));


        $this->addColumn('status', array(
            'header' => L::t('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                EbayListingsModel::STATUS_NOT_ACTIVE => L::t('Not Active'),
                EbayListingsModel::STATUS_STOPED => L::t('Stoped'),
                EbayListingsModel::STATUS_FINISHED => L::t('Finished'),
                EbayListingsModel::STATUS_ACTIVE => L::t('Active'),
            ),
        ));

        parent::_prepareColumns();
    }

    public function setAccountFilter($accountId)
    {
        $this->_accountId = $accountId > 0 ? $accountId : false;
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("ebayListings/index", array('account_select' => $this->_accountId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

}