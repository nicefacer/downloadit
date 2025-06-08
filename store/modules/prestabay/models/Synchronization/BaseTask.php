<?php

/**
 * File BaseTask.php
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
abstract class Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_UNKNOWN;
    protected $_taskName = "unknown";
    protected $_hasErrors = false;

    final public function run()
    {
        $this->_execute();
        if (!$this->_hasErrors) {
            $this->_appendSucces(sprintf(L::t("Finish '%s' synchronization"), ucfirst($this->_taskName)));
        } else {
            $this->_appendSucces(sprintf(L::t("'%s' synchronization finished with errors"), ucfirst($this->_taskName)));
        }
    }

    abstract protected function _execute();

    public function setTaskName($taskName)
    {
        $this->_taskName = $taskName;
    }


    protected function _appendSucces($message, $addParams = array())
    {
        Log_SyncModel::appendSuccess($message, $this->_syncType, $addParams);
    }

    protected function _appendWarning($message, $addParams = array())
    {
        Log_SyncModel::appendWarning($message, $this->_syncType, $addParams);
    }

    protected function _appendError($message, $addParams = array())
    {
        Log_SyncModel::appendError($message, $this->_syncType, $addParams);
    }

}