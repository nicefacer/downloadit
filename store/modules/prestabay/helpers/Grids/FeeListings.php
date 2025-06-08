<?php
/**
 * File FeeListings.php
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

class Grids_FeeListings extends Grid
{
    protected $accountId = false;

    public function __construct()
    {
        $this->_gridId = "fee_listings";
        $this->_multiSelect = false;
        $this->_selectModel = new Selling_FeeModel();
        $this->_primaryKeyName = "id";
        $this->_defaultSort = 'date_add';
        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("Selling Listings Fee"));

        $this->addButton("feeTotalListings", array(
            'value' => L::t("Product Total Fee"),
            'name' => 'totalFee',
            'class' => 'button btn btn-success btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('fee/total') . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));

        parent::__construct();
    }

    protected function  _prepareCollection()
    {
        if ($this->accountId > 0) {
            $this->getSelect()->addFilter('mt.`account_id`', $this->accountId);
        }
        $languageId = (int) (Configuration::get('PS_LANG_DEFAULT'));

        $shopSQLCondition = "";
        if (CoreHelper::isPS15()) {
            if (Shop::isFeatureActive() && Shop::getTotalShops(false, null) > 1) {
                $shopId = (int) Shop::getContextShopID();
                if ($shopId == 0) {
                    $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
                }
                $shopSQLCondition = " AND pl.id_shop = $shopId";
            } else {
                $shopId = (int) Configuration::get('PS_SHOP_DEFAULT');
                $shopSQLCondition = " AND pl.id_shop = $shopId";
            }
        }

        $this->getSelect()->addJoin(
            'left',
            array('pl' => _DB_PREFIX_.'product_lang'),
            array('name'),
            "mt.`product_id` = pl.`id_product` AND pl.id_lang = " . $languageId . $shopSQLCondition
        );

        $this->getSelect()->addJoin(
            'left',
            array('sp' => _DB_PREFIX_.'prestabay_selling_products'),
            array('selling_id'),
            "mt.`selling_product_id` = sp.`id`"
        );


        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('ID', array(
            'header' => L::t('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id'
        ));

        $this->addColumn('ebay_id', array(
            'header' => L::t('Ebay ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'ebay_id'
        ));

        $this->addColumn('product_name', array(
            'header' => L::t('PS Product'),
            'align' => 'right',
            'width' => '*',
            'index' => 'name',
            'type' => 'feeproduct',
            'filterKey' => 'pl.name',
        ));

        $this->addColumn('action', array(
            'header' => L::t('Action'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'action',
            'type' => 'options',
            'options' => array(
                Selling_FeeModel::ACTION_LIST => L::t('List'),
                Selling_FeeModel::ACTION_RELIST => L::t('Relist'),
                Selling_FeeModel::ACTION_REVISE => L::t('Revise'),
            ),
        ));

        $this->addColumn('fee_total', array(
            'header' => L::t('Fee'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'fee_total',
            'type' => 'currency',
            'currency_column' => 'fee_currency'
        ));


        $this->addColumn('date_add', array(
            'header' => L::t('Date'),
            'align' => 'center',
            'width' => '150px',
            'type' => 'datetime',
            'index' => 'date_add',
        ));

        parent::_prepareColumns();
    }

    public function setAccountFilter($accountId)
    {
        $this->accountId = $accountId > 0 ? $accountId : false;
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("fee/index", array('account_select' => $this->accountId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

}