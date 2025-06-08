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

class Grids_Accounts extends Grid
{

    public function __construct()
    {
        $this->_gridId = "accounts";
        $this->_multiSelect = true;
        $this->_selectModel = new AccountsModel();
        $this->_primaryKeyName = "id";

        $this->setHeader(L::t("Accounts List"));


        $this->addButton("newAccount", array(
            'value' => '<i class="icon-plus icon-white"></i> '.L::t("New Account"),
            'name' => 'newAccount',
            'class' => 'button btn btn-success btn-small',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('accounts/new') . '"'
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

        $this->addColumn('mode', array(
            'header' => L::t('Mode'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'mode',
            'type' => 'options',
            'options' => array(
                0 => L::t('Sandbox'),
                1 => L::t('Live')
            ),
        ));


        $this->addColumn('action',
                array(
                    'header' => L::t('Action'),
                    'width' => '100',
                    'type' => 'action',
                    'getter' => 'getId',
                    'actions' => array(
                        array(
                            'caption' => L::t('Edit'),
                            'url' => 'accounts/edit',
                            'field' => 'id',
                            'icon' => 'edit.gif'
                        ),
                        array(
                            'caption' => L::t('Delete'),
                            'confirm' => L::t('Delete account?'),
                            'url' => 'accounts/delete',
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
        return UrlHelper::getUrl("accounts/index");
    }

    public function getId($row)
    {
        return $row['id'];
    }

}