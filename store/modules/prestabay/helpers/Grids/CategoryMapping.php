
<?php
/**
 * File StoreMapping.php
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


class Grids_CategoryMapping extends Grid
{

    public function __construct()
    {
        $this->_gridId = "categoryMapping";
        $this->_multiSelect = true;
        $this->_selectModel = new Mapping_CategoryModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Category Mapping"));
        $this->addButton("addNewCategoryMapping", array(
                'type' => 'button',
                'class' => 'button btn btn-success btn-small',
                'name' => 'addNewCategoryMapping',
                'value' => '<i class="icon-plus icon-white"></i> '.L::t('New Mapping'),
                'onclick' => 'document.location="' . UrlHelper::getUrl("categoryMapping/new") . '"'
            ));

        parent::__construct();
    }

    protected function _prepareCollection()
    {
        if (is_null($this->getSelect())) {
            throw new Exception(L::t("Please load select model"));
        }
        $this->getSelect()->addJoin("left", array("pm" => _DB_PREFIX_ . 'prestabay_marketplaces'), array('label' => 'label'), '`mt`.marketplace_id = `pm`.id');


        parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
                'header' => L::t('id'),
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


        $this->addColumn('marketplace_id', array(
                'header' => L::t('Marketplace'),
                'align' => 'left',
                'width' => '200px',
                'index' => 'label',
                'filtrable' => false
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
                        'url' => 'categoryMapping/edit',
                        'field' => 'id',
                        'icon' => 'edit.gif'
                    ),
                    array(
                        'caption' => L::t('Delete'),
                        'confirm' => L::t('Delete Category Mapping?'),
                        'url' => 'categoryMapping/delete',
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
        return UrlHelper::getUrl("categoryMapping/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}