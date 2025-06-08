<?php
/**
 * File MarketplacesController.php
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

class MarketplacesController extends BaseAdminController
{

    /**
     * Show Grid with marketplaces list
     */
    public function indexAction()
    {
        if (CoreHelper::isPS16()) {
            return $this->ajaxGrid();
        }

        if (UrlHelper::getGet("submitUpdateMarketplaces", false) !== false) {
            $marketplacesIdList = UrlHelper::getGet("marketplacesBox", array());

            if ($marketplacesIdList != array()) {
                // Some marketplaces specify
                $idList = base64_encode(serialize($marketplacesIdList));
                UrlHelper::redirect("marketplaces/synchronize", array('ids' => $idList));
                return;
            }
            RenderHelper::addError(L::t("You need select marketplace to synchronize"));
        }


        if (UrlHelper::getGet("submitClearMarketplaces", false) !== false) {
            $marketplacesIdList = UrlHelper::getGet("marketplacesBox", array());

            if ($marketplacesIdList == array()) {
                RenderHelper::addError(L::t("You need select marketplace to clear"));
            } else {
                $marketplacesCategories = new ImportCategoriesModel();
                $marketplacesList = new MarketplacesModel();
                $importShipping = new ImportShippingModel();

                $marketplacesCategories->removeCategoryData($marketplacesIdList);
                $importShipping->removeShippingsData($marketplacesIdList);

                $marketplacesList->clearCategoryVersion($marketplacesIdList);

                RenderHelper::addSuccess(L::t("Marketplaces categories data was deleted"));
            }
        }
        $myGrid = new Grids_Marketplaces();
        $myGrid->getHtml();
    }

    /**
     * Run Marketplace Synchronization Process.
     *
     * Display loader page that make ajax request to synchronize actions. 
     */
    public function synchronizeAction()
    {
        $ids = UrlHelper::getGet("ids", "");
        if ($ids == "") {
            RenderHelper::addError(L::t("You need select marketplace to synchronize"));
            UrlHelper::redirect("marketplaces/index");
            return;
        }
        $idsList = array();
        try {
            $idsList = unserialize(base64_decode($ids));
        } catch (Exception $ex) {
            RenderHelper::addError($ex->getMessage());
        }

        if ($idsList == array()) {
            RenderHelper::addError(L::t("You need select marketplace to synchronize"));
            UrlHelper::redirect("marketplaces/index");
            return;
        }

        $this->view("marketplaces/synch", array('idsList' => $idsList));
    }

    // ################################################################
    // Ajax synch task for synchronize marketplace categories

    /**
     * Base function that call in each ajax request
     */
    protected function _prepareMarketplaceAjax()
    {
        RenderHelper::cleanOutput();

        if (($id = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id"), 'next' => false));
            return false;
        }
        return $id;
    }

    /**
     * Marketplace synchronization - Ajax Call. Step1.
     *
     * Retrive marketplace information: category version, name and other.
     *
     * @return json resonse
     */
    public function getVersionAction()
    {
        if (($id = $this->_prepareMarketplaceAjax()) === false) {
            return;
        }

        // Get information about existing marketplace
        $marketplaceInfo = new MarketplacesModel($id);

        if ($marketplaceInfo->id == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Unknown Marketplace"), 'next' => false));
            return;
        }

        $site_id = $id;

        DebugHelper::addProfilerTimeSpot("Marketplace Get Version - " . $id);

        ApiModel::getInstance()->reset();
        $eBayMarketplaceVersion = ApiModel::getInstance()->ebay->marketplaces->getVersion(array('marketplaceId' => $site_id))->post();

        DebugHelper::endProfilerTimeSpot("Marketplace Get Version - " . $id);

        if ($eBayMarketplaceVersion == false || !isset($eBayMarketplaceVersion['result'])) {
            echo json_encode(array('success' => false, 'message' => ApiModel::getInstance()->getErrorsAsHtml(), 'next' => false));
            return;
        }

        $eBayMarketplaceVersion = $eBayMarketplaceVersion['result'];

        if ($marketplaceInfo->version == 0 || $marketplaceInfo->version != $eBayMarketplaceVersion) {
            // Need to synchronize marketplaces
            echo json_encode(array(
                'success' => true,
                'message' => L::t("Start marketplace synchronization").": <b>" . $marketplaceInfo->label . "</b>",
                'next' => true,
                'session' => uniqid(),
                'version' => $eBayMarketplaceVersion,
                'label' => $marketplaceInfo->label,
            ));
            return;
        } else {
            // No need synchronize categories
            echo json_encode(array('success' => true, 'message' => L::t("You have latest category version for marketplace").": <b>" . $marketplaceInfo->label . "</b>", 'next' => false));
            return;
        }
    }

    /**
     * Marketplace synchronization - Ajax Call. Step2.
     *
     * Retrvive selected marketplace categories, payment, shipping information from eBay.
     *
     * @return json response with result of call
     */
    public function getCategoriesAction()
    {
        if (($id = $this->_prepareMarketplaceAjax()) === false) {
            return;
        }
        DebugHelper::addProfilerMessage("=============================\n\nGet Categories Call For Marketplace - " . $id);

        $site_id = $id;
        $session = UrlHelper::getPost("session", uniqid());

        DebugHelper::addProfilerTimeSpot("Get Categories Api Call #" . $id);

        ApiModel::getInstance()->reset();
        $eBayMarketplaceCategories = ApiModel::getInstance()->ebay
                        ->marketplaces->getCategories(array('marketplaceId' => $site_id))
                        ->post();
        DebugHelper::endProfilerTimeSpot("Get Categories Api Call #" . $id);

        if ($eBayMarketplaceCategories == false) {
            echo json_encode(array('success' => false, 'message' => ApiModel::getInstance()->getErrorsAsHtml(), 'next' => false));
            return;
        }

        DebugHelper::addProfilerTimeSpot("Serialize Category Data to Temp File #" . $id);
        $tempDir = _PS_MODULE_DIR_ . "prestabay/var/tmp";
        $tempFile = $tempDir . "/" . $session;
        if (!file_put_contents($tempFile, serialize($eBayMarketplaceCategories))) {
            echo json_encode(array('success' => false, 'message' => L::t("Can't save information to file. Please check write permission for dir")." " . $tempDir, 'next' => false));
            return;
        }
        DebugHelper::endProfilerTimeSpot("Serialize Category Data to Temp File #" . $id);

        echo json_encode(array('success' => true, 'message' => L::t("Categories has been imported from eBay"), 'next' => true));
        return;
    }

    /**
     * Marketplace synchronization - Ajax Call. Step2.
     *
     * Import marketplace relation information to DB.
     *
     * @return json result of call
     */
    public function importCategoriesAction()
    {

        if (($id = $this->_prepareMarketplaceAjax()) === false) {
            return;
        }
        DebugHelper::addProfilerMessage("Inport Categories to Db. Marketplace - " . $id);

        $session = UrlHelper::getPost("session");
        $version = UrlHelper::getPost("version", 0);
        $label = UrlHelper::getPost("label", "");

        DebugHelper::addProfilerTimeSpot("Import Information to DB #" . $id);

        $tempFile = _PS_MODULE_DIR_ . "prestabay/var/tmp/" . $session;
        $_d = file_get_contents($tempFile);
        if (!$_d) {
            echo json_encode(array('success' => false, 'message' => L::t("Can't get saved information. Please check read permission for file")." " . $tempFile, 'next' => false));
            return;
        }

        try {
            $marketplaceInfo = unserialize($_d);
        } catch (Exception $ex) {
            echo json_encode(array('success' => false, 'message' => $ex->getMessage(), 'next' => false));
            return;
        }
        @unlink($tempFile);

        try {
            // Import categories
            $marketplacesCategoryModel = new ImportCategoriesModel();
            $marketplacesCategoryModel->updateCategory($id, $marketplaceInfo['categories']);

            // Shipping Info
            $importShippingModel = new ImportShippingModel();
            $importShippingModel->importShippings($id, $marketplaceInfo['shipping_services']);

            // General Marketplace Info
            $marketplaces = new MarketplacesModel($id);
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
        } catch (Exception $ex) {
            echo json_encode(array('success' => false, 'message' => $ex->getMessage(), 'next' => false));
            return;
        }

        DebugHelper::endProfilerTimeSpot("Import Information to DB #" . $id);
        DebugHelper::addProfilerMessage("\n\nFinish Marketplace {$id} Synchronization\n\n##################################\n\n");

        echo json_encode(array('success' => true, 'message' => sprintf(L::t("Finish %s marketplace synchronization"), "<b>" . $label . "</b>" )));
        return;
    }

    /**
     * Handle Ajax grid
     * Only for PS16
     */
    protected function ajaxGrid()
    {
        $marketplacesGrid = new AjaxGrids_Marketplaces();

        $processingResult = $marketplacesGrid->handleRequest(
            UrlHelper::getGet(),
            UrlHelper::getPost()
        );

        if ($processingResult == false) {
            $defaultGridHtml = $this->view("widget/ajaxgrid.phtml", $marketplacesGrid->getConfig(), false);

            $this->view("marketplaces/index.phtml", array('grid' => $defaultGridHtml));
            return;
        }

        RenderHelper::cleanOutput();
        RenderHelper::setJSONHeader();

        echo json_encode($processingResult);
    }

}