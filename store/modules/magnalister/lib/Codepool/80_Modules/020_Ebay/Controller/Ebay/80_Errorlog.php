<?php
MLFilesystem::gi()->loadClass('ErrorLog_Controller_Widget_ErrorLog_Abstract');
class ML_Ebay_Controller_Ebay_Errorlog extends ML_ErrorLog_Controller_Widget_ErrorLog_Abstract {
	
    public static function getTabTitle() {
        return MLI18n::gi()->get('ML_GENERIC_ERRORLOG');
    }

    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    protected function processErrorAdditonalData(&$item) {
        $item['ErrorData'] = 
            (is_array($item['ErrorData'])?$item['ErrorData']:array())
            +
            array('origin' => isset($item['Origin']) ? $item['Origin'] : '');
    }
    
    public function getFields() {
        return array(
                'SKU' => array(
                    'Label' => $this->__('ML_AMAZON_LABEL_ADDITIONAL_DATA'),
                    'Sorter' => 'products_model',
                    'Field' => 'SKU',
                ),
                'ErrorMessage' => array(
                    'Label' => $this->__('ML_GENERIC_ERROR_MESSAGES'),
                    'Sorter' => 'errormessage',
                    'Field' => 'errormessage',
                ),
                'Origin' => array(
                    'Label' => $this->__('ML_GENERIC_LABEL_ORIGIN'),
                    'Field' => 'Origin',
                ),
                'DateAdded' => array(
                    'Label' => $this->__('ML_GENERIC_CHECKINDATE'),
                    'Sorter' => 'id',
                    'Field' => 'dateadded',
                ),
        );
    }
    public function render(){
        $this->getErrorLogWidget();
        return $this;
    }
    
    public function getOrigin($oErrorlog) {
        $aData = $oErrorlog->get('data');
        echo isset($aData['origin'])?$aData['origin']:'';
    }
}