<?php

MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Sync_Controller_Frontend_Do_SyncOrderStatus extends ML_Core_Controller_Abstract {

    public function renderAjax() {//@todo in future renderAjax could be more clear        
        try {
            $this->execute();
            $aAjax = MLSetting::gi()->get('aAjax');
            if(empty($aAjax)){
                throw new Exception;
            }
        } catch(Exception $oEx){//if there is no data to be sync or if there is an error
            MLSetting::gi()->add('aAjax', array('success' => true));
        }        
        if(MLHttp::gi()->isAjax()){ 
            $this->finalizeAjax();
        }
    }

    public function render() {
        $this->execute();
    }
    public function execute(){
        $iStartTime = microtime(true);
        require_once MLFilesystem::getOldLibPath('php/callback/callbackFunctions.php');
        $sMessage='';
        $iRequestMp=  MLRequest::gi()->data('mpid');
        $aTabIdents=  MLDatabase::factory('config')->set('mpid',0)->set('mkey','general.tabident')->get('value');
        foreach(magnaGetInvolvedMarketplaces() as $sMarketPlace){
            foreach(magnaGetInvolvedMPIDs($sMarketPlace) as $iMarketPlace){
                if($iRequestMp===null||$iRequestMp==$iMarketPlace){
                    ML::gi()->init(array('mp'=>$iMarketPlace));
                    try{
                        $oService=  MLService::getSyncOrderStatusInstance();
                        $oService->execute();
                        $sMessage.=$sMarketPlace.' ('.(isset($aTabIdents[$iMarketPlace])&&$aTabIdents[$iMarketPlace]!=''?$aTabIdents[$iMarketPlace].' - ':'').$iMarketPlace.'), ';
                    }catch(Exception $oEx){//modul dont exists
//                        echo $oEx->getMessage();
                    }
                }
            }
        }
        if(MLHttp::gi()->isAjax()){            
            $iOffset = (int)MLModul::gi()->getConfig('orderstatussyncoffset');
            $iTotal = MLOrder::factory()->getOutOfSyncOrdersArray(0,true);
            if($iTotal <= $iOffset + 100){
                $blFinished = true;
                $iOffset = 0;
            }else{
                $blFinished = false;
                $iOffset = $iOffset + 100;
            }
            MLModul::gi()->setConfig('orderstatussyncoffset', $iOffset);
            MLSetting::gi()->add(
                    'aAjax',
                    array(
                        'success' => $blFinished ,
                        'error' => '',
                        'offset' => $iOffset ,
                        'info' => array(
                            'total' => $iTotal,
                            'current' => $iOffset ,
                            'purge' => false,
                        ),
                    )
            );
        }else{            
            echo "{#".base64_encode(json_encode(array('Complete' => 'true')))."#}\n";
    //        if( strlen($sMessage)>0){
    //            MLMessage::gi()->addInfo(
    //                '<strong>'.date(MLI18n::gi()->get('sDateTimeFormat'),time()).'</strong><br />'.
    //                MLI18n::gi()->get('sOrderStatusSyncByService').' '.
    //                substr($sMessage,0,-2).'.'
    //            );
    //        }
            echo "\n\nComplete (".microtime2human(microtime(true) - $iStartTime).").\n";
        }
    }
}