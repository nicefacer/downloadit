<?php
abstract class ML_Modul_Model_Service_Abstract {
    protected $oModul=null;
    
    protected $iLogLevel = 1;//default=1
    
    const LOG_LEVEL_NONE = 0;
    const LOG_LEVEL_LOW = 1;
    const LOG_LEVEL_MEDIUM = 2;
    const LOG_LEVEL_HIGH = 3;


    /**
     * executes complete service-process
     */
    abstract public function execute();

    public function __construct(){
        require_once MLFilesystem::getOldLibPath('php/callback/callbackFunctions.php');
        $this->oModul=  MLModul::gi();
    }
    protected function getMarketplaceName(){
        return $this->oModul->getMarketplaceName();
    }
    protected function getMarketplaceId(){
        return $this->oModul->getMarketplaceId();
    }    
    /**
     * 
     * @param string $sString
     * @param array $aReplace array('search'=>'replace',...)
     * @return string Description
     */
    protected function replace($sString,$aReplace){
        foreach($aReplace as $sSearch=>$sReplace){
            $sString=str_replace($sSearch,$sReplace,$sString);
        }
        return $sString;
    } 
    
    protected function log($sString, $iLogLevel = 0, $sLimiter = '==') {
        if ($iLogLevel <= $this->iLogLevel || MLSetting::gi()->get('blDebug')) {
            $this->out($sLimiter.' '.MLModul::gi()->getMarketPlaceName(). ' ('.MLModul::gi()->getMarketPlaceId().'): '.$sString.' '.$sLimiter);
        }
        return $this;
    }
    
    protected function out($mValue) {
        echo is_array($mValue) ? "\n{#".base64_encode(json_encode(array_merge(array('Marketplace' => MLModul::gi()->getMarketPlaceName(), 'MPID' => MLModul::gi()->getMarketPlaceId(),), $mValue)))."#}\n\n": $mValue."\n";
        flush();
        return $this;
    }

}