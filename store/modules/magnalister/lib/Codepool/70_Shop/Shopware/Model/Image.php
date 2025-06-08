<?php
MLFilesystem::gi()->loadClass('Base_Model_Image');
class ML_Shopware_Model_Image extends ML_Base_Model_Image {
    
    public function getFallbackUrl($sSrc , $sDst , $iX , $iY){        
        return MLHttp::gi()->getBackendBaseUrl() . '/backend/Magnalister/imageResize'.
            '?'.MLHttp::gi()->parseFormFieldName('x').'='.$iX.
            '&'.MLHttp::gi()->parseFormFieldName('y').'='.$iY.
            '&'.MLHttp::gi()->parseFormFieldName('src').'='.$sSrc.
            '&'.MLHttp::gi()->parseFormFieldName('dst').'='.$sDst
        ;
    }
    
}
