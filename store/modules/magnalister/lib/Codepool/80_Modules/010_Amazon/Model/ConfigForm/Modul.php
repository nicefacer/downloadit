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
MLFilesystem::gi()->loadClass('Modul_Model_ConfigForm_Modul_Abstract');
require_once MLFilesystem::getOldLibPath('php/modules/amazon/amazonFunctions.php');
class ML_Amazon_Model_ConfigForm_Modul extends ML_Modul_Model_ConfigForm_Modul_Abstract{
    public function getConditionValues(){
        return amazonGetPossibleOptions('ConditionTypes');
    }
    
    public function getCarrierCodeValues() {
        $aCarrierCodes = MLModul::gi()->getCarrierCodes();
        if (MLHttp::gi()->isAjax()) {
            $aFields = MLRequest::gi()->data('field');
            $sAdditional = $aFields['orderstatus.carrier.additional'];
        }else{
            $sAdditional = MLModul::gi()->getConfig('orderstatus.carrier.additional');
        }
        $aAdditional = explode(',', $sAdditional);
        if (!empty($aAdditional)) {
            foreach ($aAdditional as $sValue) {
                if(trim($sValue) != ''){
                    $aCarrierCodes[$sValue] = $sValue;
                }
            }
            if(MLHttp::gi()->isAjax()){
                MLModul::gi()->setConfig('orderstatus.carrier.additional', $sAdditional, true);
            }
        }

        return $aCarrierCodes;
    }

    public function getShippingLocationValues(){
        return amazonGetPossibleOptions('ShippingLocations');
    }
    
    /**
     * deprecated , it will be removed after amazon new configuration
     */
    function updateCarrierCodesAjax($args) {
	global $_MagnaSession;

	setDBConfigValue('amazon.orderstatus.carrier.additional', $_MagnaSession['mpID'], $args['value']);

	$carrierCodes = loadCarrierCodes();
	$setting = getDBConfigValue(
		'amazon.orderstatus.carrier.default',
		$_MagnaSession['mpID']
	);

	$ret = '';
	foreach ($carrierCodes as $val) {
		$ret .= '<option '.(($val == $setting) ? 'selected="selected"' : '').' value="'.$val.'">'.$val.'</option>'."\n";
	}
	return $ret;
    }
}

