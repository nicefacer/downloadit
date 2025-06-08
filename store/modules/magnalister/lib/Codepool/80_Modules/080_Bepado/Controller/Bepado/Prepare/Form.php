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
class ML_Bepado_Controller_Bepado_Prepare_Form extends ML_Form_Controller_Widget_Form_PrepareAbstract {
    
    protected $aParameters = array('controller');
    
    protected function getSelectionNameValue() {
        return 'match';
    }
    
    /**
     * @todo verify addItems
     * @return boolean
     */
    protected function triggerBeforeFinalizePrepareAction() {
        $this->oPrepareList->set('verified', 'OK');
        return true;
    }
    
    public function render() {
        $this->getFormWidget();
        return $this;
    }
    
    protected function categoriesField (&$aField) {
        $aField['subfields']['category']['values'] = 
            array('' => '..') 
            + MLDatabase::factory('bepado_prepare')->getTopCategories()
        ;
        foreach ($aField['subfields'] as &$aSubField) { //adding current cat, if not in top cat
            if (isset($aSubField['values']) && !array_key_exists($aSubField['value'], $aSubField['values'])) {
                $oCat = MLDatabase::factory('bepado_categories'.$aSubField['cattype']);
                $oCat->init(true)->set('categoryid', $aSubField['value']);
                $sCat = '';
                foreach($oCat->getCategoryPath() as $oParentCat) {
                    $sCat = $oParentCat->get('categoryname').' &gt; '.$sCat;
                }
                $aSubField['values'][$aSubField['value']] = substr($sCat,0 ,-6);
            }
        }
    }
    
    protected function shippingTimeField (&$aField) {
        $aField['values'] = ML::gi()->instance('controller_bepado_config_prepare')->getField('shippingtime', 'values');
    }
    
    protected function shippingCountryField (&$aField) {
        $aField['values'] = ML::gi()->instance('controller_bepado_config_prepare')->getField('shippingcountry', 'values');
    }
    
}