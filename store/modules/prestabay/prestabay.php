<?php

/**
 * File prestabay.php
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

class Prestabay extends Module
{

    protected $tabName = "AdminBay";

    public function __construct()
    {
        $this->name = 'prestabay';
        $this->tab = 'Integration';
        $this->version = '2.5.2';
        $this->author = 'Involic';

        if (substr(_PS_VERSION_, 0, 3) == '1.6') {
            $this->tabName = "AdminPrestabay";
            $this->bootstrap = true;
        }

        parent::__construct();

        $this->displayName = $this->l('PrestaBay - PrestaShop eBay Integration');
        $this->description = $this->l('Integrate your PrestaShop store with eBay. List store product directly to eBay.');
        $this->confirmUninstall = $this->l('Are you sure you want to delete your details ?');
        // @todo hook out of stock productOutOfStock($product)

    }

    public function hookNewOrder($params)
    {
        if (!isset($params['cart']->id) || $params['cart']->id < 1) {
            return false;
        }

        $productInCustomersCart = Db::getInstance()->ExecuteS("SELECT id_product FROM "._DB_PREFIX_."cart_product WHERE id_cart = ".(int)$params['cart']->id);
        if (!$productInCustomersCart || $productInCustomersCart == array()) {
            return false;
        }
        $this->_initPrestabayAutoloader();

        if (CoreHelper::isPS15()) {
            // For 1.5 & 1.6 update qty handled by hookupdateQuantity
            return false;
        }
        foreach ($productInCustomersCart as $cartProduct) {
            $modelProduct = new Product($cartProduct['id_product']);
            if (!$modelProduct->id) {
                continue;
            }
            $updateQty = new Hooks_UpdateQty();
            $updateQty->execute($modelProduct->id, Product::getQuantity($modelProduct->id));
        }
    }

    public function hookpostUpdateOrderStatus($params)
    {
        $this->_initPrestabayAutoloader();
        $updateOrderStatus = new Hooks_UpdateOrder();
        $updateOrderStatus->execute($params['id_order'], $params['newOrderStatus']->id);
    }

    public function hookactionAdminOrdersTrackingNumberUpdate($params)
    {
        if (!isset($params['order'])) {
            return;
        }
        $this->_initPrestabayAutoloader();
        $shippingTracking = new Hooks_ShippingTracking();
        $shippingTracking->execute($params['order']);
    }

    public function hookupdateproduct($params)
    {
        $idProduct = null;

        if (isset($params['id_product']) && $params['id_product'] > 0) {
            $idProduct = $params['id_product'];
        } else if (isset($params['product']->id) && $params['product']->id > 0) {
            $idProduct = $params['product']->id;
        } else {
            return false;
        }

        // Load Product Model
        $modelProduct = new Product($idProduct);
        if (!$modelProduct->id) {
            return false;
        }

        $this->_initPrestabayAutoloader();

        // Save custom product values
        $productPrestaBayInfo = Tools::getValue('prestabay');
        if ($productPrestaBayInfo) {
            $shopId = 0;
            if (CoreHelper::isPS15()) {
                $shopId = (int)Context::getContext()->shop->id;
            }
            $productEbayData = ProductEbayDataModel::loadByProductStoreId($idProduct, $shopId);
            $productEbayData->setData($productPrestaBayInfo);
            $productEbayData->product_id = $idProduct;
            $productEbayData->store_id = $shopId;
            $productEbayData->save();
        }

        if (!CoreHelper::isPS15()) {
            $updateQty = new Hooks_UpdateQty();
            $updateQty->execute($modelProduct->id, Product::getQuantity($modelProduct->id));
        }

        $updatePrice = new Hooks_UpdatePrice();
        $updatePrice->execute($modelProduct->id, $modelProduct->getPrice());

        $categoriesList = $this->_getProductCategory($modelProduct->id);

        $updateCategory = new Hooks_UpdateCategory();
        $updateCategory->execute($modelProduct->id, $modelProduct->id_category_default, $categoriesList);

    }

    public function hookupdateProductAttribute($params)
    {

        if (!isset($params['id_product_attribute'])) {
            return false;
        }

        $result = Db::getInstance()->getRow('SELECT `id_product`, `quantity`, `price`
                                                FROM `'._DB_PREFIX_.'product_attribute`
                                                WHERE `id_product_attribute` = '.(int)$params['id_product_attribute']);
        $this->_initPrestabayAutoloader();

        if (!CoreHelper::isPS15()) {
            // This does not make sense for PS15

            // Get Base QTY for product
            $baseQty = Product::getQuantity($result['id_product']);
            $attributeQty = Product::getQuantity($result['id_product'], (int)$params['id_product_attribute']);
            $updateQty = new Hooks_UpdateQty();
            $updateQty->execute($result['id_product'], $baseQty, $params['id_product_attribute'], $attributeQty);
        }

        $basePrice = 0; // Not used on price update when change attribute

        $variationPrice = Product::getPriceStatic((int) $result['id_product'], true, (int) $params['id_product_attribute']);

        $updatePrice = new Hooks_UpdatePrice();
        $updatePrice->execute($result['id_product'], $basePrice, $params['id_product_attribute'], $variationPrice);
    }

    /**
     * compatibility for PS 1.5
     * @param <type> $params
     */
    public function hookupdateQuantity($params)
    {
         $this->_initPrestabayAutoloader();
        if (!CoreHelper::isPS15()) {
            return;
        }

        if ($params['id_product_attribute'] == 0)  {
            // Product without attribute update qty
            $updateQty = new Hooks_UpdateQty();
            $updateQty->execute($params['id_product'], $params['quantity']);
        } else {
            $baseQty = Product::getQuantity($params['id_product']);
            $updateQty = new Hooks_UpdateQty();
            $updateQty->execute($params['id_product'], $baseQty, $params['id_product_attribute'], $params['quantity']);
        }
    }

    public function hookaddproduct($params)
    {
        if (!isset($params['product']->id) || $params['product']->id <= 0) {
            return false;
        }

        // Load Product Model
        $modelProduct = new Product($params['product']->id);
        if (!$modelProduct->id) {
            return false;
        }

        $this->_initPrestabayAutoloader();

        $categoriesList = $this->_getProductCategory($modelProduct->id);

        $updateCategory = new Hooks_UpdateCategory();
        $updateCategory->execute($modelProduct->id, $modelProduct->id_category_default, $categoriesList);
    }

    public function hookbackOfficeTop($params)
    {
        // depricated from version @since 1.4.0
    }

    public function hookbackOfficeFooter($params)
    {
        // depricated from version @since 1.4.0
    }

    public function hookheader($params)
    {
        // depricated from version @since 1.4.0
    }

    /**
     * This is specific hook related to PS 1.5 and PS 1.6
     *
     * @param $params
     * @return mixed
     */
    public function hookDisplayAdminProductsExtra($params)
    {
        $this->_initPrestabayAutoloader();

        $productId = Tools::getValue('id_product', 0);
        $shopId = (int)Context::getContext()->shop->id;
        if (!$productId) {
            return;
        }

        return RenderHelper::view('psproduct/ebay-tab.phtml', array(
                'productEbayDataModel' => ProductEbayDataModel::loadByProductStoreId($productId, $shopId)
            ), false);
    }

    public function install()
    {
	    $this->_initPrestabayAutoloader();

        if (CoreHelper::isPS16()) {
            $this->tabName = "AdminPrestabay";
        }

        if (!parent::install()) {
            return false;
        }


        if (Configuration::get('INVEBAY_VERSION_DATA')) {
            echo "Module already installed";
            // Module already installed don't allow install one more time
            return false;
        }

        // Registering menu
        // old value 1,
        $resultOfMainTabId = $this->_installModuleTab($this->tabName, 'eBay', CoreHelper::isPS15()?9:1);
        if ($resultOfMainTabId === false) {
            return false;
        }

        // Perform sql updates
        // install or upgrade?
        $action = "install";
        $fromVersion = null;
        if (Configuration::get('INVEBAY_VERSION_DATA')) {
            $fromVersion = Configuration::get('INVEBAY_VERSION_DATA');
            $action = "upgrade";
        }

        $installer = new Installer();
        try {
            $applyDataVersion = $installer->applyAction($action, $fromVersion, $this->version);
        } catch (Exception $ex) {
            return false;
        }

        if (!$applyDataVersion) {
            return false;
        }

        if (!Configuration::updateValue('INVEBAY_VERSION_DATA', $applyDataVersion)) {
            return false;
        }

	if (substr(_PS_VERSION_, 0, 3) == '1.3') {
	    $this->registerHook('newOrder');
	    $this->registerHook('updateproduct');
	    $this->registerHook('addproduct');
	    $this->registerHook('updateProductAttribute');
	    $this->registerHook('postUpdateOrderStatus');
	}
        return true;
      }

    protected function _installModuleTab($tabClass, $tabName, $idTabParent)
    {
        $tab = new Tab();
        foreach (Language::getLanguages() as $language) {
            $tab->name[$language['id_lang']] = $tabName;
        }

        $tab->class_name = $tabClass;
        $tab->module = $this->name;
        $tab->id_parent = $idTabParent;
        if (!$tab->save())
            return false;
        return $tab->id;
    }

    public function uninstall()
    {
        // Remove menu
        if (!parent::uninstall()) {
            return false;
        }
        $this->_initPrestabayAutoloader();

        if (CoreHelper::isPS16()) {
            $this->tabName = "AdminPrestabay";
        }

        if (!$this->_uninstallModuleTab($this->tabName)) {
            return false;
        }

        // Run uninstall sql
        if (!($fromVersion = Configuration::get('INVEBAY_VERSION_DATA'))) {
            return false;
        }

        include _PS_MODULE_DIR_ . $this->name . '/library/Installer.php';

        $installer = new Installer();
        try {
            $applyUninstallDataVersion = $installer->applyAction("uninstall", $fromVersion, null);
        } catch (Exception $ex) {
            return false;
        }

        if (!$applyUninstallDataVersion) {
            return false;
        }

        // Remove configuration data
        if (!Configuration::deleteByName('INVEBAY_VERSION_DATA') || !Configuration::deleteByName("INVEBAY_LICENSE_KEY")) {
            return false;
        }

        return true;
    }

    protected function _uninstallModuleTab($tabClass)
    {
        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            $tab->delete();
            return true;
        }
        return false;
    }

    protected function _initPrestabayAutoloader()
    {
        if (defined('_PRESTABAY_AUTOLOADER_LOADED_') && _PRESTABAY_AUTOLOADER_LOADED_) {
            return;
        }
        $path = _PS_MODULE_DIR_ . $this->name . '/';
        include($path . 'library/Autoloader.php');
        Autoloader::init($path);
    }

    protected function _getProductCategory($idProduct)
    {
        $sql = 'SELECT id_category FROM ' . _DB_PREFIX_ . 'category_product p WHERE id_product = ' . $idProduct;
        $listOfCategories = Db::getInstance()->ExecuteS($sql, true, false);
        $parsedCategoryIds = array();
        foreach ($listOfCategories as $singleCategory) {
            $parsedCategoryIds[] = $singleCategory['id_category'];
        }
        return $parsedCategoryIds;
    }

}
