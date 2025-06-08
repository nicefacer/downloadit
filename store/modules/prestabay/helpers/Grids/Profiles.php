<?php
/**
 * File Profiles.php
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

class Grids_Profiles extends Grid
{

    public function __construct()
    {
        $this->_gridId = "profiles";
        $this->_multiSelect = true;
        $this->_selectModel = new ProfilesModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Selling Profiles"));
        $this->addButton("viewPriceTemplates", array(
            'type' => 'button',
            'class' => 'button btn btn-primary float-left btn-small',
            'name' => 'viewPriceTemplates',
            'value' => L::t('View Price Templates'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("priceTemplates/index") . '"'
        ));

        $this->addButton("viewShippingTemplates", array(
            'type' => 'button',
            'class' => 'button btn btn-primary float-left btn-small',
            'name' => 'viewShippingTemplates',
            'value' => L::t('View Shipping Templates'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("shippingTemplates/index") . '"'
        ));

        $this->addButton("viewDescriptionTemplates", array(
            'type' => 'button',
            'class' => 'button btn btn-primary float-left btn-small',
            'name' => 'viewDescriptionTemplates',
            'value' => L::t('View Description Templates'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("descriptionTemplates/index") . '"'
        ));

        $this->addButton("addNewProfile", array(
            'type' => 'button',
            'class' => 'button btn btn-success btn-small',
            'name' => 'addNewProfile',
            'value' => '<i class="icon-plus icon-white"></i> '.L::t('New Selling Profile'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("profiles/new") . '"'
        ));

        parent::__construct();
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        $this->getSelect()->addJoin("left", array("pa" => _DB_PREFIX_ . 'prestabay_accounts'), array('name'), '`mt`.ebay_account = `pa`.id');
        $this->getSelect()->addJoin("left", array("pm" => _DB_PREFIX_ . 'prestabay_marketplaces'), array('label'), '`mt`.ebay_site = `pm`.id');

        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header' => L::t('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'id',
        ));

        $this->addColumn('profile_name', array(
            'header' => L::t('Profile Name'),
            'align' => 'left',
            'width' => '*',
            'index' => 'profile_name',
        ));

        $this->addColumn('marketplace', array(
            'header' => L::t('Marketplace'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'label',
            'filtrable' => false
        ));

        $this->addColumn('ebay_primary_category_name', array(
            'type' => 'profileebaycategory',
            'header' => L::t('Category'),
            'align' => 'left',
            'width' => '300px',
            'index' => 'ebay_primary_category_name',
        ));

        $this->addColumn('ebay_account', array(
            'header' => L::t('eBay Account'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'name',
            'filtrable' => false
        ));

        $this->addColumn('auction_type', array(
            'header' => L::t('Auction Type'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'auction_type',
            'type' => 'options',
            'options' => array(
                1 => L::t('Auction'),
                2 => L::t('Fixed Price')
            ),
        ));

        $this->addColumn('action',
                array(
                    'header' => L::t('Action'),
                    'width' => '50px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => L::t('Edit'),
                            'url' => 'profiles/edit',
                            'field' => 'id',
                            'icon' => 'edit.gif'
                        ),
                        array(
                            'caption' => L::t('Delete'),
                            'confirm' => L::t('Delete Profile?'),
                            'url' => 'profiles/delete',
                            'field' => 'id',
                            'icon' => 'delete.gif'
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
        return UrlHelper::getUrl("profiles/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}