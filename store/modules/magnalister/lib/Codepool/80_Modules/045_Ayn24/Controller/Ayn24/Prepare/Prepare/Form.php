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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_PrepareAbstract');

class ML_Ayn24_Controller_Ayn24_Prepare_Prepare_Form extends ML_Form_Controller_Widget_Form_PrepareAbstract {

    protected $aParameters = array('controller');

    protected function getSelectionNameValue() {
        return 'match';
    }

    protected function triggerBeforeFinalizePrepareAction() {
        if (!empty($this->oPrepareHelper->aErrors)) {
            foreach ($this->oPrepareHelper->aErrors as $error) {
                MLMessage::gi()->addError(MLI18n::gi()->get($error));
            }

            $this->oPrepareList->set('verified', 'ERROR');
            return false;
        }

        $this->oPrepareList->set('verified', 'OK');
        return true;
    }

    public function render() {
        $this->getFormWidget();
        return $this;
    }

    protected function shippingtypeField(&$aField) {
        $aField['values'] = ML::gi()->instance('controller_ayn24_config_prepare')->getField('shippingtype', 'values');
    }

    protected function categoriesField(&$aField) {
        $aField['subfields']['primary']['values'] = array('' => '..') 
            + ML::gi()->instance('controller_ayn24_config_prepare')->getField('primarycategory', 'values');
        
        foreach ($aField['subfields'] as &$aSubField) { //adding current cat, if not in top cat
            if (!array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('ayn24_categories' . $aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value']);
                $sCat = '';
                foreach ($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname') . ' &gt; ' . $sCat;
                }
                $aSubField['values'][$aSubField['value']] = substr($sCat, 0, -6);
            }
        }
    }
    
    protected function variationConfigurationField(&$aField) {
        $aList = MLDatabase::factory('ayn24_variantmatching')->getAllItems();
        $aField['values'][] = $aField['i18n']['withoutvariations'];
        foreach ($aList as $sKey => $sValue) {
            $aField['values'][$sKey] = MLHelper::gi('text')->decodeText($sValue);
        }
    }
}
