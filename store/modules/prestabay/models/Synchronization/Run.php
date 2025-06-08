<?php
/**
 * File Run.php
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

class Synchronization_Run
{

    /**
     * Run sync Process
     */
    public function execute()
    {
        $lockFilePath = _PS_MODULE_DIR_ . "prestabay/var/tmp/sync.run";
        $lock = fopen($lockFilePath, 'w');
        if (!flock($lock, LOCK_EX | LOCK_NB)) {
            // Can't obtain file lock. Someone is lock this file
            // echo "Can't Obtain Synchronization Lock";
            return;
        }

        // Check for correct license
        LicenseHelper::verifyLicenseKey();

        try {
            NotificationsHelper::receiveLatestApiNotifications();
        } catch (Exception $ex) {

        }

        // This will update status for eBay Items
        Synchronization_Factory::getTask("default")->run();

        if (Configuration::get('INVEBAY_SYNC_RESYN_QTY') == 1) {
            Synchronization_Factory::getTask("resynchronize")->run();
        }
        
//        if (Configuration::get('INVEBAY_SYNC_RESYN_PRICE') == 1) {
//            Synchronization_Factory::getTask("resynchronizePrice")->run();
//        }
        
        if (Configuration::get('INVEBAY_SYNC_RESYN_CATEGORY') == 1) {
            Synchronization_Factory::getTask("resynchronizeCatalog")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_ORDER') == 1) {
            Synchronization_Factory::getTask("order")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_QTY') == 1) {
            Synchronization_Factory::getTask("stockLevel")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_PRICE') == 1) {
            Synchronization_Factory::getTask("price")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_RELIST') == 1) {
            Synchronization_Factory::getTask("relist")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_TASK_STOP') == 1) {
            Synchronization_Factory::getTask("stop")->run();
        }

        if (Configuration::get('INVEBAY_LIST_NOT_ACTIVE') == 1) {
            Synchronization_Factory::getTask("list")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_FEEDBACK') == 1) {
            Synchronization_Factory::getTask("feedbacks")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_FEEDBACK_AUTO') == 1) {
            Synchronization_Factory::getTask("feedbacksAuto")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_MESSAGES') == 1) {
            Synchronization_Factory::getTask("messages")->run();
        }

        if (Configuration::get('INVEBAY_SYNC_FULL_REVISE') == 1) {
            Synchronization_Factory::getTask("fullRevise")->run();
        }
    }
}