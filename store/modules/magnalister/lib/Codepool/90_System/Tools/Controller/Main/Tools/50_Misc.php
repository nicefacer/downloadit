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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Tools_Controller_Main_Tools_Misc extends ML_Core_Controller_Abstract {
    
    protected $aParameters = array('controller');
    
    protected function getAjaxMethods () {
        $aMethods = array();
        $oReflection = new ReflectionClass($this);
        foreach ($oReflection->getMethods(ReflectionMethod::IS_PROTECTED) as $oReflectionMethod) {
            if (strpos($oReflectionMethod->name, 'callAjax_') === 0) {
                $aMethods[] = substr($oReflectionMethod->name, 9);
            }
        }
        return $aMethods;
    }
    
    public function callAjaxExecuteMethod () {
        MLSetting::gi()->add('aAjaxPlugin', array(
            'dom' => array(
                '#ml-'.$this->getIdent().' .ml-result-head' => MLRequest::gi()->data('callAjaxMethod').'():',
                '#ml-'.$this->getIdent().' .ml-result-content' => $this->{'callAjax_'.MLRequest::gi()->data('callAjaxMethod')}(),
            ),
        ));
        
    }
    
    protected function callAjax_phpinfo() {
        ob_start();
        phpinfo();
        return preg_replace('/.*(<div\sclass\=\"center\">.*<\/div>).*/Uis', '$1', ob_get_clean());
    }
    
    protected function callAjax_customerodule() {
        $sCustomerFolder = MLFilesystem::getLibPath('Codepool/10_Customer');
        $aCustomerFolder = $this->getDir($sCustomerFolder);
        ob_start();
        echo $sCustomerFolder.'<br />';
        new dBug($aCustomerFolder, '', true);
        return ob_get_clean();
    }
    
    
    protected function getDir ($sFolder) {
        $aOut = array();
        if (is_readable($sFolder)) {
            foreach (MLFilesystem::glob($sFolder.'/*') as $sPath) {
                if (is_readable($sPath)) {
                    $sPath = realpath($sPath);
                    $sRealPath = basename($sPath);
                    if (is_dir($sPath)) {
                        $aOut[$sRealPath] = $this->getDir($sPath);
                    } else {
                        $aOut[$sRealPath] = '<pre>'.htmlentities(file_get_contents($sPath), ENT_IGNORE).'</pre>';
                    }
                }
            }
        }
        return $aOut;
    }
    
}