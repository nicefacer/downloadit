<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Core_Update_Abstract');

class ML_Magento_Update_ResourcesToShopFolder extends ML_Core_Update_Abstract {
    
    public function execute() {
        if (MLRequest::gi()->data(__CLASS__) === null) {
            $this->cleanFolders();
        } else {
            $this->copy();
        }
        return $this;
    }
    
    protected function cleanFolders () {
        foreach (array(Mage::getBaseDir().'/js/', Mage::getDesign()->getSkinBaseDir(array('_area' => 'adminhtml', '_package' => 'default' ,'_theme' => 'default'))) as $sFolder) { // dont delete magnalister folder if parent folder is write-protected
            foreach (MLFilesystem::glob($sFolder.'/magnalister/*') as $sDeleteFolder) {
                MLHelper::getFilesystemInstance()->rm($sDeleteFolder);
            }
        }
    }
    
    protected $blFinalize = false;
    protected function copy () {
        $iTime = microtime(true);
        foreach (MLFilesystem::glob(MLFilesystem::getLibPath('Codepool'). '/*/*/[rR]esource' . DIRECTORY_SEPARATOR . '*') as $sSrcPath) {
            $sBasePath = substr($sSrcPath, strlen(MLFilesystem::gi()->getLibPath('codepool'))+1);
            $sDstPath = (
                basename($sBasePath) == 'js'
                ? Mage::getBaseDir().'/js/'
                : Mage::getDesign()->getSkinBaseDir(array('_area' => 'adminhtml', '_package' => 'default' ,'_theme' => 'default'))
            ).'/magnalister'.$sBasePath;
            if (!file_exists($sDstPath)) {
                MLHelper::getFilesystemInstance()->cp($sSrcPath, $sDstPath);
                if (microtime(true) > $iTime + 5) {
                    return;
                }
            }
        }
        $this->blFinalize = true;
    }
    
    public function getParameters() {
        if (!$this->blFinalize) {
            return array(__CLASS__ => 'copy');
        }
    }
}
