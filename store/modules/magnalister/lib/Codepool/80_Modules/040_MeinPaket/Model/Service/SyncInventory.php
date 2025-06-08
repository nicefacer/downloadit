<?php
class ML_MeinPaket_Model_Service_SyncInventory extends ML_Modul_Model_Service_SyncInventory_Abstract {
    public function execute() {
        include_once MLFilesystem::getOldLibPath('php/modules/meinpaket/crons/MeinPaketSyncInventory.php');
        $oModul = new MeinPaketSyncInventory($this->oModul->getMarketplaceId(), $this->oModul->getMarketplaceName());
        $oModul->process();
        return $this;
    }
}
