<?php
/**
 * File Marketplaces.php
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

class Grids_Marketplaces extends Grid
{

    public function __construct()
    {
        $this->_gridId = "marketplaces";
        $this->_multiSelect = true;
        $this->_selectModel = new MarketplacesModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Marketplaces List"));



        $this->addButton("submitClearMarketplaces", array(
            'value' => L::t("Clear Selected"),
            'name' => 'submitClearMarketplaces',
            'class' => 'button btn btn-danger',
            'type' => 'button'
        ));

        $this->addButton("submitUpdateMarketplaces", array(
            'value' => '<i class="icon-download icon-white"></i> '.L::t("Download Selected"),
            'name' => 'submitUpdateMarketplaces',
            'class' => 'button btn btn-success',
            'type' => 'button'
        ));

        parent::__construct();
    }

    protected function _prepareColumns()
    {
//        $this->addColumn('id', array(
//            'header' => 'ID',
//            'align' => 'right',
//            'width' => '50px',
//            'index' => 'id',
//        ));

        $this->addColumn('label', array(
            'header' => L::t('Name'),
            'align' => 'left',
            'width' => '*',
            'index' => 'label',
        ));

        $this->addColumn('version', array(
            'header' => L::t('Version'),
            'align' => 'center',
            'width' => '50px',
            'index' => 'version',
        ));

        $this->addColumn('date_upd', array(
            'header' => L::t('Update Date'),
            'align' => 'center',
            'width' => '120px',
            'type' => 'datetime',
            'index' => 'date_upd',
        ));

        $this->addColumn('status', array(
            'header' => L::t('Status'),
            'align' => 'left',
            'width' => '120px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                0 => L::t('Not Downloaded'),
                1 => L::t('OK')
            ),
        ));

        parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("marketplaces/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}