<?php
class ML_Ebay_Model_Service_SyncInventory extends ML_Modul_Model_Service_SyncInventory_Abstract {
    public function execute() {
        include_once MLFilesystem::getOldLibPath('php/modules/ebay/crons/EbaySyncInventory.php');
        $oModul = new EbaySyncInventory($this->oModul->getMarketplaceId(), $this->oModul->getMarketplaceName());
        $oModul->process();
        return $this;
    }
}
