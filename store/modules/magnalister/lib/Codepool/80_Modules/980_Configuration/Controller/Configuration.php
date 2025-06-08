<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Configuration_Controller_Configuration extends ML_Core_Controller_Abstract {
    public function execute() {
        $_lang = 'german';
        $requiredConfigKeys = array('general.passphrase', 'general.keytype', 'general.stats.backwards', 'general.callback.importorders');
        include MLFilesystem::getOldLibPath('php/modules/configuration.php');
    }
}
