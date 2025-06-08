<?php
/**
 * File Products.php
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

class Grids_ProductsAppend extends Grids_Products
{
    protected $_sellingId = null;
    
    public function __construct($sellingId, $productLanguage = 3)
    {
        $this->_sellingId = $sellingId;

        $this->addButton("backToSelling", array(
            'value' => '<i class="icon-arrow-left"></i> '.L::t("Back"),
            'name' => 'appendProducts',
            'class' => 'button btn btn-small float-left',
            'onclick' => 'document.location.href="' . UrlHelper::getUrl('selling/edit', array('id' => $this->_sellingId)) . '"',
        ));

        $model = new Selling_ListModel($this->_sellingId);
        

        parent::__construct($productLanguage);

        $this->setHeader(L::t("Addend Product To Selling List")." — '".$model->name."'");
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("selling/append", array('id' => $this->_sellingId));
    }

}
