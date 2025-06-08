<?php
/**
 * File PriceTemplates.php
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


class Grids_PriceTemplates extends Grid
{

    public function __construct()
    {
        $this->_gridId = "priceTemplates";
        $this->_multiSelect = true;
        $this->_selectModel = new Price_TemplateModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Price Templates"));
        $this->addButton("addNewPriceTemplate", array(
            'type' => 'button',
            'class' => 'button btn btn-success btn-small',
            'name' => 'addNewPriceTemplate',
            'value' => '<i class="icon-plus icon-white"></i> '.L::t('New Price Template'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("priceTemplates/new") . '"'
        ));

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

        $this->addColumn('name', array(
            'header' => L::t('Name'),
            'align' => 'left',
            'width' => '*',
            'index' => 'name',
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
                            'url' => 'priceTemplates/edit',
                            'field' => 'id',
                            'icon' => 'edit.gif'
                        ),
                        array(
                            'caption' => L::t('Delete'),
                            'confirm' => L::t('Delete Price Template?'),
                            'url' => 'priceTemplates/delete',
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
        return UrlHelper::getUrl("priceTemplates/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}