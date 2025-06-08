<?php
/**
 * File Factory.php
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

class Synchronization_Factory
{

    protected $_hasChanges = false;
    protected $_hasWarnings = false;
    protected $_hasErrors = false;

    protected $_syncType = Log_SyncModel::LOG_TASK_UNKNOWN;
    
    /**
     *
     * @param string $taskName
     * @return Synchronization_BaseTask
     */
    public static function getTask($taskName)
    {
        $className = "Synchronization_Tasks_" . ucfirst($taskName);
        $taskClass = new $className;
        $taskClass->setTaskName($taskName);
        return $taskClass;
    }

//    public function run()
//    {
//        $this->_execute();
//
//        if (!$this->_hasChanges) {
//            Log_SyncModel::appendSuccess(L::t("No Actions for Synchronization"), $this->_syncType);
//        } else {
//            if ($this->_hasErrors) {
//                Log_SyncModel::appendError(L::t("Some Actions for Synchronization Finished with Errors"), $this->_syncType);
//            }
//
//            if ($this->_hasWarnings) {
//                Log_SyncModel::appendWarning(L::t("Some Actions for Synchronization Finished with Warnings"), $this->_syncType);
//            }
//
//            if (!$this->_hasErrors && !$this->_hasWarnings) {
//                Log_SyncModel::appendSuccess(L::t("Performed Actions for Synchronization"), $this->_syncType);
//            }
//        }
//    }

//    protected abstract function _execute();
}