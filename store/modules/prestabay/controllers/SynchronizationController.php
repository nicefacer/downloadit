<?php
/**
 * File SynchronizationController.php
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

class SynchronizationController extends BaseAdminController
{

    /**
     * Show settings list
     */
    public function indexAction()
    {
        if (!is_null(UrlHelper::getPost("saveSettings", null))) {
            $this->_saveSyncSettings();
        }
        if (!is_null(UrlHelper::getPost("runTask", null))) {
            $this->_saveSyncSettings();
            $this->_runSync();
        }

        $this->view("sync/settings.phtml");
    }

    /**
     * See log of synchronization
     */
    public function logAction()
    {
        if (!is_null(UrlHelper::getPost("saveClearSetting", null))) {
            Configuration::updateValue('INVEBAY_AUTOCLEAR_VALUE', (int) UrlHelper::getPost('autoclearlog-setting', 0));
        }

        if (!is_null(UrlHelper::getPost("clearAllLog", null))) {
            Log_SyncModel::clearLog(0); // Clear all logs
        }

        $grid = new Grids_LogSync();
        $gridOutput = $grid->getHtml(false);
        $this->view("sync/logGrid.phtml", array(
            'grid' => $gridOutput,
            'autoclearlogValue' => Configuration::get('INVEBAY_AUTOCLEAR_VALUE')));
    }

    protected function _saveSyncSettings()
    {
        $syncTaskQty = UrlHelper::getPost("sync_qty", 0);
        Configuration::updateValue('INVEBAY_SYNC_TASK_QTY', $syncTaskQty);

        $syncTaskRelist = UrlHelper::getPost("sync_relist", 0);
        Configuration::updateValue('INVEBAY_SYNC_TASK_RELIST', $syncTaskRelist);

        $syncTaskStop = UrlHelper::getPost("sync_stop", 0);
        Configuration::updateValue('INVEBAY_SYNC_TASK_STOP', $syncTaskStop);

        Configuration::updateValue('INVEBAY_SYNC_TASK_OOSC', UrlHelper::getPost("sync_oosc", 0));

        $syncTaskOrder = UrlHelper::getPost("sync_order", 0);
        Configuration::updateValue('INVEBAY_SYNC_TASK_ORDER', $syncTaskOrder);

        $syncTaskPrice = UrlHelper::getPost("sync_price", 0);
        Configuration::updateValue('INVEBAY_SYNC_TASK_PRICE', $syncTaskPrice);

        Configuration::updateValue("INVEBAY_AUTO_CATEGORY_ADD", UrlHelper::getPost("auto_category", 0));

        Configuration::updateValue("INVEBAY_LIST_NOT_ACTIVE", UrlHelper::getPost("auto_list", 0));

        Configuration::updateValue("INVEBAY_SYNC_RESYN_QTY", UrlHelper::getPost("resynchronize_qty", 0));

        Configuration::updateValue("INVEBAY_SYNC_RESYN_PRICE", UrlHelper::getPost("resynchronize_price", 0));

        Configuration::updateValue("INVEBAY_SYNC_RESYN_CATEGORY", UrlHelper::getPost("resynchronize_category", 0));

        Configuration::updateValue("INVEBAY_SYNCH_ORDER_IMPORT", UrlHelper::getPost("create_prestaorder", 0));

        Configuration::updateValue("INVEBAY_SYNCH_ORDER_SKU", UrlHelper::getPost("create_prestaorder_by_sku", 0));

        Configuration::updateValue("INVEBAY_SYNCH_ORDER_OK_PAYMENT", UrlHelper::getPost("create_prestaorder_after_payment", 0));

        Configuration::updateValue("INVEBAY_ORDER_QTY_SIMULATION", UrlHelper::getPost("order_qty_simulation", 0));

        $importFeedbackValue = UrlHelper::getPost("import_feedback", 0);
        Configuration::updateValue("INVEBAY_SYNC_FEEDBACK", $importFeedbackValue);

        $newAutoFeedbackValue = UrlHelper::getPost("auto_feedback", 0);
        if ($importFeedbackValue == 0) {
            $newAutoFeedbackValue = 0;
        }

        if (Configuration::get('INVEBAY_SYNC_FEEDBACK_AUTO') == 0 && $newAutoFeedbackValue == 1) {
            Configuration::updateValue('INVEBAY_SYNC_FEEDBACK_AUTO_ID', Feedbacks_FeedbacksModel::getMaxId());
        }

        Configuration::updateValue("INVEBAY_SYNC_MESSAGES", UrlHelper::getPost("download_messages", 0));


        Configuration::updateValue("INVEBAY_SYNC_FEEDBACK_AUTO", $newAutoFeedbackValue);

        RenderHelper::addSuccess(L::t("Synchronization Settings was Saved."));
    }

    protected function _runSync()
    {
        $syncModel = new Synchronization_Run();
        $syncModel->execute();
        RenderHelper::addSuccess(L::t("Synchronization Tasks was Executed. Result of Run Available in Log."));
    }

}