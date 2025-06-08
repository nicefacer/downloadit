<?php
/**
 * File SimulateCron.php
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

class Hooks_SimulateCron
{

    public function execute()
    {
        return false; // Depricated
        //
        // !!! Important. Cron configurated run every 5 minutes. Simulate run
        // every 15 minues to disable run it when have configurated cron job
        
        /** Check Last Cron Execution Time */

        $cronTime = Configuration::get("INVEBAY_SYNC_CRON_TIME");
        $nowTime = time();

        if ($cronTime && ceil(abs($nowTime - strtotime($cronTime))/60) < 15) {
            // Have last cron time, and last execution time less that 15 minues
            return false; //Cron run less that 15 minues
        }
        
        // Execute All Synch Task
        $syncModel = new Synchronization_Run();
        $syncModel->execute();

        Configuration::updateValue("INVEBAY_SYNC_CRON_TIME", date("Y-m-d H:i:s", $nowTime));
    }

}