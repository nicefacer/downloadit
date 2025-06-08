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

MLFilesystem::gi()->loadClass('Form_Helper_Model_Table_PrepareData_Abstract');
class ML_Dawanda_Helper_Model_Table_Dawanda_PrepareData extends ML_Form_Helper_Model_Table_PrepareData_Abstract{
    
    public function getPrepareTableProductsIdField() {
        return 'products_id';    
    }
    
    protected function productTypeField (&$aField){
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function listingDurationField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function returnPolicyField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function products_idField (&$aField) {
        $aField['value'] = $this->oProduct->get('id');
    }
    
    protected function shippingServiceField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function mpColorsField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
        $aField['value'] = is_array($aField['value']) ? $aField['value'] : array();
        if (count($aField['value']) < 2) {
            $aField['value'][] = '';
        }
        if (count($aField['value']) < 2) {
            $aField['value'][] = '';
        }
    }
    
    protected function primaryCategoryField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function secondaryCategoryField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function storeCategoryField (&$aField) {
        $aField['value'] = $this->getFirstValue($aField);
    }
    
    protected function attributesField (&$aField) {
        $aField['dependonfield']['depend'] = 'primarycategory';
        $aField['value'] = $this->getFirstValue($aField, array());
    }
    
}