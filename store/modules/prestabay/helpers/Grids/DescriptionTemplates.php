<?php
/**
 * File DescriptionTemplates.php
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


class Grids_DescriptionTemplates extends Grid
{

    public function __construct()
    {
        $this->_gridId = "descriptionTemplates";
        $this->_multiSelect = true;
        $this->_selectModel = new Description_TemplateModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Description Templates"));
        $this->addButton("addNewDescriptionTemplate", array(
            'type' => 'button',
            'class' => 'button btn btn-success btn-small',
            'name' => 'addNewDescriptionTemplate',
            'value' => '<i class="icon-plus icon-white"></i> '.L::t('New Description Template'),
            'onclick' => 'document.location="' . UrlHelper::getUrl("descriptionTemplates/new") . '"'
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
                    'width' => '75px',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => L::t('Edit'),
                            'url' => 'descriptionTemplates/edit',
                            'field' => 'id',
                            'icon' => 'edit.gif'
                        ),
                        array(
                            'caption' => L::t('Delete'),
                            'confirm' => L::t('Delete Description Template?'),
                            'url' => 'descriptionTemplates/delete',
                            'field' => 'id',
                            'icon' => 'delete.gif'
                        ),
                        array(
                            'caption' => L::t('Preview'),
                            'url' => 'descriptionTemplates/preview',
                            'field' => 'id',
                            'icon' => 'details.gif',
                            'newWindow' => true
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
        return UrlHelper::getUrl("descriptionTemplates/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}