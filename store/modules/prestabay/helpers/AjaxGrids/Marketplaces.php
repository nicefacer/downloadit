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
class AjaxGrids_Marketplaces extends AjaxGrid
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
            'type' => 'button',
        ), array($this, 'clearMarketplacesButton'));

        $this->addButton("submitUpdateMarketplaces", array(
            'value' => '<i class="icon-download icon-white"></i> ' . L::t("Download Selected"),
            'name' => 'submitUpdateMarketplaces',
            'class' => 'button btn btn-success',
            'type' => 'button'
        ), array($this, 'downloadMarketplacesButton'));

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

    public function clearMarketplacesButton($params = array())
    {
        return array(
            'jshandler' => 'cleanMarketplaceHandler'
        );
    }

    public function clearMarketplaceAction($params = array())
    {
        if (empty($params['marketplacesIdList'])) {
            throw new Exception('You need select marketplace to clear');
        }
        $marketplacesIdList = $params['marketplacesIdList'];

        $marketplacesCategories = new ImportCategoriesModel();
        $marketplacesList = new MarketplacesModel();
        $importShipping = new ImportShippingModel();

        $marketplacesCategories->removeCategoryData($marketplacesIdList);
        $importShipping->removeShippingsData($marketplacesIdList);

        $marketplacesList->clearCategoryVersion($marketplacesIdList);

        return array('success' => true);
    }

    public function downloadMarketplacesButton($params = array())
    {
        return array(
            'jshandler' => 'downloadMarketplaceHandler'
        );
    }

    /**
     * Check for current version of marketplace
     *
     * @param array $params
     * @return array
     * @throws Exception
     */
    public function getVersionAction($params = array())
    {
        if (empty($params['marketplaceId'])) {
            throw new Exception('You need select marketplace');
        }

        $siteId = $params['marketplaceId'];

        // Get information about existing marketplace
        $marketplaceInfo = new MarketplacesModel($siteId);

        if ($marketplaceInfo->id == null) {
            throw new Exception(L::t("Unknown Marketplace"));
        }

        DebugHelper::addProfilerTimeSpot("Marketplace Get Version - " . $siteId);

        ApiModel::getInstance()->reset();
        $eBayMarketplaceVersion = ApiModel::getInstance()->ebay->marketplaces->getVersion(array('marketplaceId' => $siteId))->post();

        DebugHelper::endProfilerTimeSpot("Marketplace Get Version - " . $siteId);

        if ($eBayMarketplaceVersion == false || !isset($eBayMarketplaceVersion['result'])) {
            throw new Exception(ApiModel::getInstance()->getErrorsAsHtml());
        }

        $eBayMarketplaceVersion = $eBayMarketplaceVersion['result'];

        if ($marketplaceInfo->version == 0 || $marketplaceInfo->version != $eBayMarketplaceVersion) {
            // Need to synchronize marketplaces
            return array(
                'success' => true,
                'version' => $eBayMarketplaceVersion,
                'next' => true,
            );
        }
        // No need synchronize categories
        return array(
            'success' => true,
            'next' => false,
            'message' => L::t("You have latest category version for marketplace") . ": <b>" . $marketplaceInfo->label . "</b>"
        );
    }


    /**
     * Marketplace synchronization - Ajax Call. Step2.
     *
     * Retrvive selected marketplace categories, payment, shipping information from eBay.
     *
     * @return array response with result of call
     *
     * @throws Exception
     */
    public function getCategoriesAction($params)
    {
        if (empty($params['marketplaceId'])) {
            throw new Exception('You need select marketplace');
        }

        $siteId = $params['marketplaceId'];
        $session = 'marketplace-' . $siteId;

        ApiModel::getInstance()->reset();
        $eBayMarketplaceCategories = ApiModel::getInstance()->ebay
            ->marketplaces->getCategories(array('marketplaceId' => $siteId))
            ->post();

        if ($eBayMarketplaceCategories == false) {
            throw new Exception(ApiModel::getInstance()->getErrorsAsHtml());
        }

        $tempDir = _PS_MODULE_DIR_ . "prestabay/var/tmp";
        $tempFile = $tempDir . "/" . $session;
        if (file_exists($tempFile)) {
            @unlink($tempFile);;
        }

        if (!file_put_contents($tempFile, serialize($eBayMarketplaceCategories))) {
            throw new Exception(L::t("Can't save information to file. Please check write permission for dir") . " " . $tempDir);
        }

        return array('success' => true, 'next' => true);
    }

    /**
     * Marketplace synchronization - Ajax Call. Step2.
     *
     * Import marketplace relation information to DB.
     * @return array result of call
     * @throws Exception
     */
    public function importCategoriesAction($params)
    {
        if (empty($params['marketplaceId'])) {
            throw new Exception('You need select marketplace');
        }

        $siteId = $params['marketplaceId'];

        $session = 'marketplace-' . $siteId;
        $version = UrlHelper::getPost("version", 0);

        $tempFile = _PS_MODULE_DIR_ . "prestabay/var/tmp/" . $session;
        $_d = file_get_contents($tempFile);
        if (!$_d) {
            throw new Exception(L::t("Can't get saved information. Please check read permission for file") . " " . $tempFile);
        }

        $marketplaceInfo = unserialize($_d);
//        @unlink($tempFile);

        // Import categories
        $marketplacesCategoryModel = new ImportCategoriesModel();
        $marketplacesCategoryModel->updateCategory($siteId, $marketplaceInfo['categories']);

        // Shipping Info
        $importShippingModel = new ImportShippingModel();
        $importShippingModel->importShippings($siteId, $marketplaceInfo['shipping_services']);

        // General Marketplace Info
        $marketplaces = new MarketplacesModel($siteId);
        $marketplaces->dispatch = json_encode($marketplaceInfo['dispatch']);
        $marketplaces->policy = json_encode($marketplaceInfo['policy']);
        $marketplaces->payment_methods = json_encode($marketplaceInfo['payment_methods']);
        $marketplaces->shipping_location = json_encode($marketplaceInfo['shipping_location']);
        $marketplaces->shipping_packages = json_encode($marketplaceInfo['packages']);
        $marketplaces->shipping_exclude_location = json_encode($marketplaceInfo['shipping_exclude_location']);
        $marketplaces->identify_unavailable_text = $marketplaceInfo['identify_unavailable_text'];
        $marketplaces->version = $version;
        $marketplaces->status = MarketplacesModel::STATUS_ACTIVE;
        $marketplaces->save();

        return array(
            'success' => true,
            'version' => $version,
            'date_update' => $marketplaces->date_upd
        );
    }

}