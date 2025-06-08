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
abstract class ML_Form_Helper_Model_Table_PrepareData_Abstract{
    
    /**
     * @var ML_Shop_Model_Product_Abstract $oProduct
     */
    protected $oProduct = null;
    
    /**
     * compares all entrees - if all the same, use entry, if not use default or specific
     * @var ML_Database_Model_List $oPrepareList 
     */
    protected $oPrepareList = null;
    
    /**
     * comes from request or use as primary default
     * @var array array('name'=>mValue)
     */
    protected $aRequestFields = array();
    
    /**
     * makes active or not
     * @var array array('name'=>blValue)
     */
    protected $aRequestOptional = array();
    
    /**
     * @var array calculated fields
     */
    protected $aFields = array();
    
    /**
     * field names, for save in config which are optional
     * @return array
     */
    protected function getOptionalPrepareDefaultsFields() {
        $aConfigData = MLSetting::gi()->get(strtolower(MLModul::gi()->getMarketPlaceName()).'_prepareDefaultsOptionalFields');
        return is_array($aConfigData) ? $aConfigData : array();
    }
    
    /**
     * field names, for save in config
     * @return array
     */
    protected function getPrepareDefaultsFields() {
        $aConfigData = MLSetting::gi()->get(strtolower(MLModul::gi()->getMarketPlaceName()).'_prepareDefaultsFields');
        return is_array($aConfigData) ? $aConfigData : array();
    }
    
    /**
     * field name of preparetable for products_id
     * @return string
     */
    abstract public function getPrepareTableProductsIdField();
    
    public function saveToConfig() {
        $aFieldsToOptional = $this->getOptionalPrepareDefaultsFields();
        $aFieldsToConfig = $this->getPrepareDefaultsFields();
        $aConfig = array();
        $aConfigOptional = array();
        foreach ($aFieldsToConfig as $sName) {
            $aConfig[$sName] = $this->getField($sName,'value');
        }
        foreach ($aFieldsToOptional as $sName) {
            $aConfigOptional[$sName] = $this->optionalIsActive($sName);
        }
        MLDatabase::factory('preparedefaults')
            ->set('values', $aConfig)
            ->set('active', $aConfigOptional)
            ->save()
        ;
        return $this;
    }
    
    /**
     * setting values with high priority eg. request
     * @param array $aRequestFields
     * @return \ML_Ebay_Helper_Ebay_Prepare
     */
    public function setRequestFields($aRequestFields = array()) {
        $this->aRequestFields = $aRequestFields;
        return $this;
    }
    
    public function setRequestOptional($aRequestOptional = array()){
        $this->aRequestOptional = $aRequestOptional;
        return $this;
    }
    
    /**
     * @param ML_Ebay_Model_Table_Ebay_Prepare $oPrepareList
     */
    public function setPrepareList($oPrepareList) {
        $this->oPrepareList = $oPrepareList;
        return $this;
    }
    
    protected function getPrepareList() {
        if ($this->oPrepareList === null) {
            $oPrepareList = MLDatabase::factory(MLModul::gi()->getMarketPlaceName().'_prepare')->getList();
            if ($this->oProduct === null) {
                $oPrepareList->getQueryObject()->where("false");
            }else{
                $oPrepareList->getQueryObject()->where($this->getPrepareTableProductsIdField()." = '".$this->oProduct->get('id')."'");
            }
            $this->oPrepareList = $oPrepareList;
        }
        return $this->oPrepareList;
    }
    
    /**
     * @param ML_Shop_Model_Product_Abstract  $oProduct
     * @param null $oProduct
     */
    public function setProduct($oProduct) {
        $this->aFields = array();//init new product= new fields
        $this->oProduct = $oProduct;
        return $this;
    }
    
    protected function getRequestField($sName = null, $blOptional = false){
        $sName = strtolower($sName);
        if ($blOptional) {
            $aFields = $this->aRequestOptional;
        }else{
            $aFields = $this->aRequestFields;
        }
        $aFields = array_change_key_case($aFields, CASE_LOWER);
        if ($sName == null) {
            return $aFields;
        } else {
            return isset($aFields[$sName]) ? $aFields[$sName] : null;
        }
    } 
    
    /**
     * @param array $aFields eg. array(justTheDoMethod, doMethodWithPreconfiguredValues=>array('optional'=>array('active'=>true))
     * @return array
     */
    public function getPrepareData($aFields, $sIndex = null) {
        $aRow = array();
        foreach ($aFields as $sKey => $sField) {
            if (is_array($sField)) {
                $aField = $sField;
                $aField['name'] = $sKey;
            } else {
                $aField = array('name' => $sField);
            }
            $aField = array_merge($aField, $this->getField($aField));
            if (array_key_exists('value', $aField)) {
                $aField['value'] = $this->optionalIsActive($aField) ? $aField['value'] : null;
                $aRow[$aField['name']] = $aField;
            }
        }
        if ($sIndex === null) {
            return $aRow;
        } else {
            $aOut = array();
            $sKey = strtolower($sKey);
            foreach ($aRow as $sKey => $aValue) {
                $aOut[$sKey] = $aValue[$sIndex];
            }
            return $aOut;
        }
    }  
    
    public function getFromConfig($sField, $blOptional = false) {
        $oModel = MLDatabase::factory('preparedefaults');
        return $blOptional ? $oModel->getActive($sField) : $oModel->getValue($sField);
    }
    
    public function getField ($aField, $sVector = null){
        $aField = is_array($aField) ? $aField : array('name' => $aField);
        $aField = array_change_key_case($aField, CASE_LOWER);
        $sName = strtolower(isset($aField['realname']) ? $aField['realname'] : $aField['name']);
        $aField['realname'] = $sName;
        if (!isset($this->aFields[$sName])) {
            $sMethod = str_replace('.', '_', $sName.'Field');// no points
             if (method_exists($this, $sMethod)) {
                $aResult = $this->{$sMethod}($aField);
                if (is_array($aResult)) {
                    $aResult = array_change_key_case($aResult, CASE_LOWER);
                    $aField = array_merge($aField, $aResult);
                }
             }
            $this->aFields[$sName] = $aField;
        }
        if ($sVector === null) {
            return $this->aFields[$sName];
        } else {
            $sVector = strtolower($sVector);
            return isset($this->aFields[$sName][$sVector]) ? $this->aFields[$sName][$sVector] : null;
        }
    }
    
    /**
     * checks if a field is active, or not
     *
     * @param type $aField
     * @param bool $blDefault defaultvalue, if  no request or dont find in prepared
     * @return bool
     */
    public function optionalIsActive($aField) {
        if (isset($aField['optional']['active'])) {
            // 1. already setted
            $blActive = $aField['optional']['active'];
        } else {
            if (is_string($aField)) {
                $sField = $aField;
            } else {
                if (isset($aField['optional']['name'])) {
                    $sField = $aField['optional']['name'];
                } else {
                    $sField = isset($aField['realname']) ? $aField['realname'] : $aField['name'];
                }
            }
            $sField = strtolower($sField);
            // 2. get from request
            $sActive = $this->getRequestField($sField,true);
            if ($sActive == 'true' || $sActive === true) {
                $blActive = true;
            } elseif ($sActive == 'false' || $sActive === false) {
                $blActive = false;
            } else {
                $blActive = null;
            }
            if ($blActive === null) {//not in request - look in model, if null is possible
                $aFieldInfo = $this->getPrepareList()->getModel()->getTableInfo($sField);
                if (isset($aFieldInfo['Null']) && $aFieldInfo['Null'] == 'NO') {
                    $blActive = true;
                }
            }
            if ($blActive === null) {
                // 3. check if prepared
                $aPrepared = $this->getPrepareList()->get($sField, true);
                if (count($aPrepared) == 0) {//not prepared
                    // 4. is in config
                    $blActive = $this->getFromConfig($sField, true);
                    // 5. optional-field have default-value
                    $blActive = ($blActive === null && isset($aField['optional']['defaultvalue'])) ? $aField['optional']['defaultvalue'] : $blActive;
                    // 6. not prepared, not in config, no default value
                    $blActive = $blActive===null ? false : $blActive;
                } else {
                    // 7. if any null value in prepared => false
                    $blActive = in_array(null, $aPrepared, true) ? false : true;
                }
            }
        }
        return $blActive;
    }
    
    /**
     * @return mixed first not null value of func_get_args
     * @return null no value != null
     */
    protected function getFirstValue($aField) {
        $sField = strtolower(isset($aField['realname']) ? $aField['realname'] : $aField['name']);
        $mRequestValue = $this->getRequestField($sField);
        if (isset($aField['dependonfield'])) {//if depend on other field, array-key should string value of depended field
            $sDependValue = $this->getField(array('name'=>$aField['dependonfield']['depend']), 'value');
        }
        $aArray=array();
        // 1. already setted value
        $aArray[__line__] = isset($aField['value'])?$aField['value']:null;
        // 2. request-value
        $aArray[__line__] = $mRequestValue;
        if (isset($sDependValue) && !isset($aArray[2][$sDependValue])) {
            $aArray[__line__] = null;
        }
        // 3. is in preparetable and all values are the same
        $aPrepared = $this->getPrepareList()->get(str_replace('.', '_', $sField), true);
        $aArray[__line__] = count($aPrepared) == 1 ? current($aPrepared) : null;
        if (isset($sDependValue) && !isset($aArray[3][$sDependValue])) {
            $aArray[__line__] = null;
        }
        // 4. get from config
        $aArray[__line__] = $this->getFromConfig($sField, false);
        foreach (func_get_args() as $iValue => $sValue) {
            //5. manual added values eg. from product
            if ($iValue > 0) {
                if (isset($sDependValue)) {
                    $aArray[__line__.'('.$iValue.')'] = array($sDependValue => $sValue);
                } else {
                    $aArray[__line__.'('.$iValue.')'] = $sValue;
                }
            }
        }
        $sReturn = null;
        foreach ($aArray as $iValue => $sValue) {
            if ($sValue !== null) {
//                MLMessage::gi()->addDebug(__METHOD__."('".$sField."')".' Line '.$iValue);
                $sReturn = $sValue;
                break;
            }
        }
        if ($sReturn === null) {
            if (isset($aField['dependonfield'])) {
                $sReturn = array($this->getField($aMyField, 'value') => null);
            }
        }
        $this->hookPrepareField($sField, $sReturn);
        return $sReturn;
    }
    
    protected function hookPrepareField($sKey, &$mValue) {
        /* {Hook} "preparefield": Enables you to extend or modify the data for the prepare forms for all marketplaces.<br><br>
           The hook will be executed before any templates will be processed (this means eventual placeholders are still intact).
           This hook will be executed in 2 places. One is during the preparation process (the form) and the other during uploading the items.
           If multiple items are prepared in one go, some variables will be null and certain fields will not be prepared (eg. title and product description).
           They will be completed during the uploading process. During the upload process these values will never be null.<br>
           <b>So if you want to append certain fields you have pay attention that your values aren't already appended! Otherwise they will be appended
           multiple times!</b><br><br>
           Variables that can be used: 
           <ul>
               <li>$iMagnalisterProductsId (?int): Id of the product in the database table `magnalister_product`. If null multiple items will be prepared.</li>
               <li>$aProductData (?array): Data row of `magnalister_product` for the corresponding $iMagnalisterProductsId. The field "productsid" is the product id from the shop. If null multiple items will be prepared.</li>
               <li>$iMarketplaceId (int): Id of marketplace</li>
               <li>$sMarketplaceName (string): Name of marketplace</li>
               <li>$sKey (string): name of form field. Use this to make sure you only manipulate the values you intend to.</li>
               <li>&$mValue (?mixed): current value for field. Overwrite the content of this variable for your additions.</li>
           </ul><br>
           Make sure to safeguard your hook for the marketplace and field you want to manipulate using $iMarketplaceId or $sMarketplaceName and $sKey.<br>
           To get a basic idea what values are available in which situation you can use the following code in your contrib file:
           <pre>&lt;?php
MLMessage::gi()->addInfo(basename(__FILE__), get_defined_vars());</pre>
           This adds an info message which can be expanded in order to see all available variables and their content.
           
        */
        if (($sHook = MLFilesystem::gi()->findhook('preparefield', 1)) !== false) {
            $iMagnalisterProductsId = $this->oProduct === null ? null : $this->oProduct->get('id');
            $aProductData = $this->oProduct === null ? null : $this->oProduct->data();
            $iMarketplaceId = MLModul::gi()->getMarketPlaceId();
            $sMarketplaceName = MLModul::gi()->getMarketPlaceName();
            require $sHook;
        }
    }
}