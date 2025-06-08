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
MLFilesystem::gi()->loadClass('Base_Model_Image');
class ML_Prestashop_Model_Image extends ML_Base_Model_Image {
    
    public function getFallbackUrl($sSrc , $sDst , $iX , $iY){
        return MLHttp::gi()->getUrl().'&action=resizeImage&'.
			MLHttp::gi()->parseFormFieldName('x').'='.$iX.
            '&'.MLHttp::gi()->parseFormFieldName('y').'='.$iY.
            '&'.MLHttp::gi()->parseFormFieldName('src').'='.$sSrc.
            '&'.MLHttp::gi()->parseFormFieldName('dst').'='.$sDst
        ;
    }
    
}
