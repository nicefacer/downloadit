<?php
/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 *  It is available through the world-wide-web at this URL:
 *  http://involic.com/license.txt
 *  If you are unable to obtain it through the world-wide-web,
 *  please send an email to license@involic.com so
 *  we can send you a copy immediately.
 *
 *  PrestaBay - eBay Integration with PrestaShop e-commerce platform.
 *  Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 *  @author      Involic <contacts@involic.com>
 *  @copyright   Copyright (c) 2011- 2016 by Involic (http://www.involic.com)
 *  @license     http://involic.com/license.txt
 */

class Grids_FeeTotalListings extends Grid
{
    protected $accountId = false;
    protected $range = false;

    public function __construct()
    {
        $this->_gridId = "fee_total";
        $this->_multiSelect = false;
        $this->_selectModel = new Selling_FeeModel();
        $this->_primaryKeyName = "id";
        $this->_defaultSort = 'sum_fee';
        $this->_defaultDir = 'desc';

        $this->setHeader(L::t("Selling Listings Total Fee"));


        $this->addButton("feeTotalListingsMonth", array(
            'value' => L::t("30 Days Fee Total"),
            'name' => 'totalFeeMonth',
            'class' => 'button btn btn-success btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('fee/total', array('range' => '30')) . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));

        $this->addButton("feeTotalListingsWeek", array(
            'value' => L::t("7 Days Fee Total"),
            'name' => 'totalFeeWeek',
            'class' => 'button btn btn-success btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('fee/total', array('range' => '7')) . '"',
            'target' => '_blank',
            'inlineButton' => true,
        ));

        $this->addButton("feeTotalListingsDay", array(
            'value' => L::t("24 Hours Fee Total"),
            'name' => 'totalFeeDay',
            'class' => 'button btn btn-success btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('fee/total', array('range' => '1')) . '"',
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

        if ($this->range > 0) {
            $this->getSelect()->setExtraWhere('(mt.date_add >= CURRENT_DATE() - INTERVAL ' . (int)$this->range . ' DAY)');
        }

        $this->getSelect()->addFields('sum(mt.fee_total) as sum_fee, count(mt.id) as total_actions');
        $this->getSelect()->groupBy('mt.product_id, mt.fee_currency');

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {

        $this->addColumn('product_id', array(
            'header' => L::t('Product ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'product_id',
            'filterKey' => 'product_id',
        ));

        $this->addColumn('product_name', array(
            'header' => L::t('PS Product'),
            'align' => 'right',
            'width' => '*',
            'index' => 'name',
            'filterKey' => 'pl.name',
            'type' => 'productname'
        ));

        $this->addColumn('sum_fee', array(
            'header' => L::t('Fee'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'sum_fee',
            'type' => 'currency',
            'currency_column' => 'fee_currency'
        ));

        $this->addColumn('total_actions', array(
            'header' => L::t('Total Actions'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'total_actions'
        ));

        parent::_prepareColumns();
    }

    public function setAccountFilter($accountId)
    {
        $this->accountId = $accountId > 0 ? $accountId : false;
    }

    public function setRangeFilter($range)
    {
        $this->range = $range > 0 ? $range : false;
        $this->setHeader(L::t(sprintf("Selling Listings Fee for %s Days", $this->range)));
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("fee/total", array('account_select' => $this->accountId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

}