<?php

/*
 * File Resynchronize.php
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

class Synchronization_Tasks_Resynchronize extends Synchronization_BaseTask
{

    protected $_syncType = Log_SyncModel::LOG_TASK_RESYNCHRONIZE_QTY;

    protected function _execute()
    {
        $sellingProduct = new Selling_ProductsModel();
        $numberOfResynchronizationNormal = $sellingProduct->resynchronizeQTY();
        $numberOfResynchronizationVariation = $sellingProduct->resynchronizeVariationQTY();
        if ($numberOfResynchronizationNormal + $numberOfResynchronizationVariation > 0) {
            $this->_appendSucces(sprintf(L::t('%s listings have been updated related to QTY changes in PS'),
                    $numberOfResynchronizationNormal + $numberOfResynchronizationVariation));
        }
    }

}