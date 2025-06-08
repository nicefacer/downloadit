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
MLFilesystem::gi()->loadClass('Form_Controller_Widget_Form_Abstract');

abstract class ML_Form_Controller_Widget_Form_ConfigAbstract extends ML_Form_Controller_Widget_Form_Abstract {

    protected $aParameters = array('controller');

    protected $blExpert = false;
    protected $sActionTemplate = 'action-row-row-row';
//    protected $blValidateAuthKeys = true;


    protected $oConfigHelper = null;


    /**
     * default-data for prepare mask
     * @var array
     */
    protected $aPrepareDefaults = array();
    
    protected $aPrepareDefaultsActive = array();

    protected static function addMissingConfKeyError($sClass, $aErrorField) {

        $aFormArray = MLController::gi($sClass)->getNormalizedFormArray();
        $blFound = false;
        foreach ($aFormArray as $aFieldSet) {
            foreach ($aFieldSet['fields'] as $aField) {
                if ($aField['realname'] == $aErrorField['name']) {
                    $blFound = true;
                } elseif (array_key_exists('subfields', $aField)) {
                    foreach ($aField['subfields'] as $aSubfield) {
                        if ($aSubfield['realname'] == $aErrorField['name']) {
                            $blFound = true;
                            break;
                        }
                    }
                }
                if ($blFound) {
                    $sLegend = '';
                    if (
                        isset($aFieldSet['legend']) && 
                        isset($aFieldSet['legend']['i18n']) && 
                        !empty($aFieldSet['legend']['i18n'])
                    ) {
                        $sLegend = 
                            (
                                is_array($aFieldSet['legend']['i18n']) 
                                ? $aFieldSet['legend']['i18n']['title'] 
                                : $aFieldSet['legend']['i18n']
                            ).' > '
                        ;
                    }
                    MLMessage::gi()->addError(
                        MLModul::gi()->getMarketPlaceName(false).'('.MLModul::gi()->getMarketPlaceId().') '.sprintf(
                            ML_CONFIG_FIELD_EMPTY_OR_MISSING, 
                            $sLegend.$aField['i18n']['label']
                        )
                    );
                    break;
                }
                
            }
            if ($blFound) {
                break;
            }
        }
        
    }

    /**
     * calcs if * config tab is active tab depending on wizard
     * @param string $sClassName
     * @param bool $blDefault
     */
    protected static function calcConfigTabActive($sClassName, $blDefault) {
        $sController = MLRequest::gi()->cleanMarketplaceId('controller');
        $sClass = strtolower(preg_replace('/^(.*_.*_.*_).*/U', '', $sClassName));
        if (!MLRequest::gi()->data('wizard')) {
            if (!MLModul::gi()->isConfigured()) {
                $aSettings = MLSetting::gi()->get('aModules');
                foreach (
                    array_unique(
                        array_merge(
                            array_keys($aSettings[MLModul::gi()->getMarketPlaceName()]['authKeys']),
                            (MLModul::gi()->isAuthed() ? $aSettings[MLModul::gi()->getMarketPlaceName()]['requiredConfigKeys'] : array())
                        )
                    ) as $sMissingConfKey
                ) {
                    if (
                        (MLModul::gi()->getConfig($sMissingConfKey) === null)
                        || (MLModul::gi()->getConfig($sMissingConfKey) === '')
                    ) {
                        break;
                    }
                    $sMissingConfKey = '';
                }
                if (isset($sMissingConfKey) && !empty($sMissingConfKey)) {
                    foreach (ML::gi()->getChildClassesNames('controller_'.substr($sClass, 0, strrpos($sClass, '_')), false) as $sConfTab) {
                        try {
                            try{
                                $aConfForm = MLSetting::gi()->get(MLModul::gi()->getMarketPlaceName().'_config_'.$sConfTab);
                            } catch (Exception $ex) {
                                $aConfForm = MLSetting::gi()->get('generic_config_'.$sConfTab);
                            }
                            foreach ($aConfForm as $aFormPart) {
                                $aFormPart['fields'] = array_key_exists('fields', $aFormPart) ? $aFormPart['fields'] : array();
                                // go through the tabs until the missing key is found
                                foreach ($aFormPart['fields'] as $aField) {
                                    if (
                                        $aField['name'] == $sMissingConfKey
                                        && $sClass == substr($sClass, 0, strrpos($sClass, '_')).'_'.$sConfTab
                                    ) {
                                        self::addMissingConfKeyError($sClass, $aField);
                                        return true;
                                    } elseif (
                                        isset($aField['subfields'])
                                        && is_array($aField['subfields'])
                                    ) {
                                        // we can also have required subfields
                                        foreach ($aField['subfields'] as $sSubfield) {
                                            if (
                                                $sSubfield['name'] == $sMissingConfKey
                                                && $sClass == substr($sClass, 0, strrpos($sClass, '_')).'_'.$sConfTab
                                            ) {    
                                                self::addMissingConfKeyError($sClass, $aField);
                                                return true;
                                            } else if ($sSubfield['name'] == $sMissingConfKey) {
                                                return false;
                                            }
                                        }
                                    } elseif ($aField['name'] == $sMissingConfKey) {
                                        return false;
                                    }
                                }
                            }
                        } catch (Exception $oEx) {
                        }
                    }
                }
            }
        }
        if ($blDefault) {
            return
                (
                    MLRequest::gi()->data('wizard')
                    && $sClass != $sController
                    && substr_count($sController, '_') >= substr_count($sClass, '_')
                )
                    ? false
                    : true;
        } else {
            return
                (
                    MLRequest::gi()->data('wizard')
                    && $sClass !== $sController
                )
                    ? false
                    : MLModul::gi()->isAuthed();
        }
    }

    /**
     * check if expert-setting ar active
     * @return bool
     */
    public function isExpert() {
        return $this->blExpert;
    }

    /**
     * adding tab-ident automaticly on first tab
     * @param string $sType can be aI18n or aForm
     * @param null $sType returns both arrays
     * @return array
     */
//    protected function getFormArray($sType = null) {
//        $aParent = parent::getFormArray($sType);
//        if (!$this->isAuthed() && $this->blValidateAuthKeys) { // drop fields, which are not in auth-form
//            if ($sType === null) {
//                $aForms = $aParent['aForm'];
//            } elseif ($sType == 'aForm') {
//                $aForms = $aParent;
//            } else {
//                $aForms = parent::getFormArray('aForm');
//            }
//            $aModules = MLSetting::gi()->get('aModules');
//            $aAuthKeyDefinition = $aModules[MLModul::gi()->getMarketPlaceName()]['authKeys'];
//            foreach ($aForms as $sLegend => $aLegends) {
//                if ($sLegend == 'action') {
//                    continue;
//                }
//                foreach ($aLegends['fields'] as $sField => $aField) {
//                    if (
//                        !array_key_exists($aField['name'], $aAuthKeyDefinition)
//                        && $aField['name'] != 'tabident' && (!isset($aField['icludeauth']) || !$aField['icludeauth'])
//                    ) {
//                        if ($sType === null) {
//                            unset($aParent['aForm'][$sLegend]['fields'][$sField]);
//                            unset($aParent['aI18n']['field'][$sField]);
//                        } elseif ($sType == 'aForm') {
//                            unset($aParent[$sLegend]['fields'][$sField]);
//                        } else {
//                            unset($aParent['field'][$sField]);
//                        }
//                    }
//                }
//            }
//        }
//        return $aParent;
//    }

    protected function getField($aField, $sVector = null) {
        $aField = is_array($aField) ? $aField : array('name' => $aField);
        if (isset($aField['expert']) && $aField['expert']) {
            if ($this->isExpert()) {
                $aField['classes'][] = 'mlexpert';
            } else {
                unset($aField['type']);
            }
        }
        try {
            $sName = array_key_exists('realname', $aField) ? $aField['realname'] : $aField['name'];
            $aModules = MLSetting::gi()->get('aModules');
            $aRequiredKeys = $aModules[MLModul::gi()->getMarketPlaceName()]['requiredConfigKeys'];
            if (
                    in_array($sName, $aRequiredKeys)
                    && !MLModul::gi()->isConfigured()
                    && !MLRequest::gi()->data('wizard')
            ) {
                $aField['required'] = 'true';
            }
        } catch (Exception $oEx) {
        }
        return parent::getField($aField, $sVector);
    }

    public function render() {
        $this->getFormWidget();
    }

    protected function construct() {
        $this->isAuthed();
        $oPrepareDefaults = MLDatabase::factory('preparedefaults')->set('mpId', MLModul::gi()->getMarketPlaceId())->set('name', 'defaultconfig');
        $aPrepareDefaults = $oPrepareDefaults->get('values');
        $aPrepareDefaults = is_array($aPrepareDefaults) ? $aPrepareDefaults : array();
        $aPrepareDefaultsConfig = MLSetting::gi()->get(strtolower(MLModul::gi()->getMarketPlaceName()).'_prepareDefaultsFields');
        $aPrepareDefaultsConfig = isset($aPrepareDefaultsConfig) ? $aPrepareDefaultsConfig : array();
        foreach ($aPrepareDefaultsConfig as $sDefaultKey) {
            $this->aPrepareDefaults[$sDefaultKey] = isset($aPrepareDefaults[$sDefaultKey]) ? $aPrepareDefaults[$sDefaultKey] : null;
        }
        
        $aPrepareDefaultsActive = $oPrepareDefaults->get('active');
        $aPrepareDefaultsActiveConfig = MLSetting::gi()->get(strtolower(MLModul::gi()->getMarketPlaceName()).'_prepareDefaultsOptionalFields');
        $aPrepareDefaultsActiveConfig= isset($aPrepareDefaultsActiveConfig) ? $aPrepareDefaultsActiveConfig : array();
        foreach ($aPrepareDefaultsActiveConfig as $sDefaultKey) {
            $this->aPrepareDefaultsActive[$sDefaultKey] = isset($aPrepareDefaultsActive[$sDefaultKey]) ? $aPrepareDefaultsActive[$sDefaultKey] : null;
            $this->aRequestOptional[$sDefaultKey] = array_key_exists($sDefaultKey, $this->aRequestOptional) ? $this->aRequestOptional[$sDefaultKey] : $this->aPrepareDefaultsActive[$sDefaultKey];
        }
        $this->oConfigHelper = MLHelper::gi('model_table_'.MLModul::gi()->getMarketPlaceName().'_configdata');
        $this->oConfigHelper
            ->setIdent($this->getIdent())
            ->setRequestFields($this->aRequestFields)
            ->setRequestOptional($this->aRequestOptional)
        ;
    }

    protected function optionalIsActive($aField) {
        return $this->oConfigHelper->optionalIsActive($aField);
    }

    protected function getFieldMethods($aField) {
        $aMethods = array();
        $aMethods[] = 'getRequestValue'; //request
        $aMethods[] = 'getValue'; //  database
        $aMethods[] = 'getDefaultValue'; //  config
        $aMethods[] = 'prepareAddonField'; // addon field 
        foreach (parent::getFieldMethods($aField) as $sMethod) {
            $aMethods[] = $sMethod;
        }

        $aMethods[] = 'prepareFieldByFormHelper';
        return $aMethods;
    }

    protected function prepareFieldByFormHelper(&$aField) {
        $sMethod = str_replace('.', '_', $aField['realname'].'field');
        if (method_exists($this->oConfigHelper, $sMethod)) {
            $this->oConfigHelper->{$sMethod}($aField);
        }
    }

    public function getRequestValue(&$aField) {
        if (!isset($aField['value'])) {
            if (($mValue = $this->getRequestField($aField['realname'])) !== null) {
                $aField['value'] = $mValue;
            }
        }
    }

    public function getValue(&$aField) {
        if (!isset($aField['value'])) {
            if ($aField['realname'] == 'tabident') {
                $aIdents = MLDatabase::factory('config')->set('mpId', 0)->set('mkey', 'general.tabident')->get('value');
                $aIdents = is_array($aIdents) ? $aIdents : array();
                $aField['value'] = isset($aIdents[MLModul::gi()->getMarketPlaceId()]) ? $aIdents[MLModul::gi()->getMarketPlaceId()] : '';
            } else {
                if (array_key_exists($aField['realname'], $this->aPrepareDefaults)) {
                    $aField['value'] = $this->aPrepareDefaults[$aField['realname']];
                } else {
                    $aField['value'] = MLModul::gi()->getConfig($aField['realname']);//MLDatabase::factory('config')->set('mpId', MLModul::gi()->getMarketPlaceId())->set('mkey', $aField['realname'])->get('value');
                }
            }
        }
    }

    public function getDefaultValue(&$aField) {
        if (!isset($aField['value']) && isset($aField['default'])) {
            $aField['value'] = $aField['default'];
        }
        if (isset($aField['i18n']['values'])) {
            $aField['values'] = $aField['i18n']['values'];
        }
    }

    protected function prepareAddonField(&$aField) {
        if (array_key_exists('type', $aField) && ($aField['type'] == 'addon_bool' || $aField['type'] == 'addon_select')) {
            if (
                array_key_exists('addonsku', $aField)
                && !MLShop::gi()->addonBooked($aField['addonsku'])
            ) {
                try {
                    $aResponse = MagnaConnector::gi()->submitRequest(array(
                        'SKU' => $aField['addonsku'],
                        'SUBSYSTEM' => 'Core',
                        'ACTION' => 'GetAddonInfo',
                    ), null, true);
                    if (
                        array_key_exists('DATA', $aResponse)
                        && array_key_exists('PluginText', $aResponse['DATA'])
                    ) {
                        $aField['i18n']['alert'] = $aResponse['DATA']['PluginText'];
                        $aField['value'] = false;
                    }
                } catch (Exception $oEx) {// addon cant be booked.
                    MLMessage::gi()->addDebug($oEx);
                    $aField = array();
                }
            } elseif (!array_key_exists('addonsku', $aField)) {
                MLMessage::gi()->addDebug('Field addon have no SKU.');
                $aField = array();
            }
        }
    }

    public function callAjaxAddAddon() {
        $sSku = '';
        try {
            $aAjaxData = $this->getAjaxData();
            if (!array_key_exists('addonsku', $aAjaxData)) {
                throw new Exception('No Addon-Sku setted.');
            }
            $sSku = $aAjaxData['addonsku'];
            MagnaConnector::gi()->submitRequest(array(
                'SUBSYSTEM' => 'Core',
                'ACTION' => 'AddAddon',
                'SKU' => $sSku,
                'CHANGE_TARIFF' => true,
            ));
            MLShop::gi()->getShopInfo(true);// reload addons
            MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('.addon_'.$sSku.'>.ml-addAddonError' => '<div class="successBox">'.MLI18n::gi()->get('form_text_addon_success', array('Sku' => $sSku)).'</div>')));
            MLSetting::gi()->add('aAjax', array('success' => true));
        } catch (Exception $oEx) {
            MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('.addon_'.$sSku.'>.ml-addAddonError' => '<div class="errorBox">'.MLI18n::gi()->get('form_text_addon_error', array('Sku' => $sSku, 'Error' => $oEx->getMessage())).'</div>')));
            throw $oEx;
        }
        return $this;
    }

    protected function isAuthed($aAuthKeys = array()) {
        $aModules = MLSetting::gi()->get('aModules');
        $blForce = false;
//        foreach ($aAuthKeys as $iAuthKey => $sAuthKey) {
//            if ($sAuthKey == '__saved__') {
//                unset($aAuthKeys[$iAuthKey]);
//            }
//        }
        if (count($aAuthKeys)) {
            MLMessage::gi()->addDebug($aAuthKeys);
            $blForce = true;
            try {
                MagnaConnector::gi()->submitRequest(array('ACTION' => 'SetCredentials') + $aAuthKeys);
                if (MLModul::gi()->isAuthed($blForce)) {
                    $blForce = false;
                    MLMessage::gi()->addSuccess(MLI18n::gi()->get('ML_GENERIC_STATUS_LOGIN_SAVED'));
                }
            } catch (MagnaException $oEx) {
                MLMessage::gi()->addDebug($oEx);
                MLMessage::gi()->addError(MLI18n::gi()->get('ML_GENERIC_STATUS_LOGIN_SAVEERROR'));
            }
        }
        return MLModul::gi()->isAuthed($blForce);
    }

    public function expertAction($blExecute = true) {
        if ($blExecute) {
            $this->blExpert = true;
            return $this;
        } else {
            return array(
                'aI18n' => array('label' => MLI18n::gi()->get('form_action_expert')),
                'aForm' => array(
                    'type' => 'submit',
                    'position' => 'left',
                    'disabled' => $this->isExpert(),
                    'hiddenifdisabled' => true,
                )
            );
        }
    }

    public function resetAction($blExecute = true) {
        if ($blExecute) {
            return $this;
        } else {
            return array(
                'aI18n' => array('label' => MLI18n::gi()->get('form_action_reset')),
                'aForm' => array('type' => 'reset', 'position' => 'right')
            );
        }
    }

    protected function testMailAction($blExecute = true) {
        if ($blExecute) {
            $this->saveAction();
            ML::gi()->init(array('do' => 'importorders'));//activate sync-modul
            $blTestMail = MLService::getImportOrdersInstance()->sendPromotionMailTest();
            ML::gi()->init();
            if ($blTestMail) {
                MLMessage::gi()->addSuccess(MLI18n::gi()->ML_GENERIC_TESTMAIL_SENT);
            } else {
                MLMessage::gi()->addNotice(MLI18n::gi()->ML_GENERIC_TESTMAIL_SENT_FAIL);
            }
            return $this;
        } else {
            return array();
        }
    }

    /**
     * returns true if modul is not completly configured
     * @param array $aParams
     */
    protected function isWizard() {
        return ($this->getRequest('wizard') || !MLModul::gi()->isConfigured());
    }

    /**
     * adds wizard to url, if config startet first time
     * @param array $aParams
     */
    public function getCurrentUrl($aParams = array()) {
        $sNextController = $this->getNextController();
        if ($this->isWizard()) {
            $aParams['wizard'] = true;
        }
        return parent::getCurrentUrl($aParams);
    }

    /**
     * calculates next controller of current controller, only for wizard
     * @param bool $blLong true for complete controllername, false, only for name of child-part
     * @return string
     */
    protected function getNextController($blLong = false) {
        if (!$this->isWizard()) {
            return '';
        } else {
            $sParentController = substr($this->getIdent(), 0, strrpos($this->getIdent(), '_'));
            $sCurrentController = substr($this->getIdent(), strrpos($this->getIdent(), '_') + 1);
            $sNextController = '';
            $aSiblingControllers = ML::gi()->getChildClassesNames('controller_'.$sParentController, false);
            $blNext = false;
            foreach ($aSiblingControllers as $sSiblingController) {
                if ($blNext) {
                    $sNextController = $sSiblingController;
                    break;
                }
                if ($sCurrentController == $sSiblingController) {
                    $blNext = true;
                }
            }
            if ($blLong && $sNextController != '') {
                $sNextController = $sParentController.'_'.$sNextController;
            }
            return $sNextController;
        }
    }

    public function saveAction($blExecute = true) {
        if ($blExecute) {
            $aModules = MLSetting::gi()->get('aModules');
            $aAuthKeyDefinition = $aModules[MLModul::gi()->getMarketPlaceName()]['authKeys'];
            foreach ($this->getFormArray('aForm') as $aLegend) {
                foreach ($aLegend['fields'] as $aField) {
                    $aFields[$aField['name']] = $aField;
                }
            }
            $aAuthKeys = array();
            $blAuthKeysChanged = false;
            $blAuthed = MLModul::gi()->isAuthed();
            foreach ($this->aRequestFields as $sName => $mValue) {
                if ($sName == 'tabident') {
                    $aIdents = MLDatabase::factory('config')->set('mpId', 0)->set('mkey', 'general.tabident')->get('value');
                    $aIdents = is_array($aIdents) ? $aIdents : array();
                    $aIdents[MLModul::gi()->getMarketPlaceId()] = $mValue;
                    $aIdents = MLDatabase::factory('config')->set('mpId', 0)->set('mkey', 'general.tabident')->set('value', $aIdents)->save();
                } else {
                    if (array_key_exists($sName, $aAuthKeyDefinition)) {
                        $mValue = trim($mValue);
                        $sSavedAuthValue = MLModul::gi()->getConfig($sName); //MLDatabase::factory('config')->set('mpId', MLModul::gi()->getMarketPlaceId())->set('mkey', $sName)->get('value');
                        if (
                               !empty($mValue)
                            && ($sSavedAuthValue != $mValue || !$blAuthed)
                        ) {
                            $blAuthKeysChanged = true;
                            $aAuthKeys[$aAuthKeyDefinition[$sName]] = $mValue;
                        } elseif (isset($aFields[$sName]['savevalue'])) {
                            $aAuthKeys[$aAuthKeyDefinition[$sName]] = $aFields[$sName]['savevalue'];
                        } else {
                            $aAuthKeys[$aAuthKeyDefinition[$sName]] = $sSavedAuthValue;
                        }
                    }
                    if (isset($aFields[$sName]['savevalue'])) {
                        $mValue = $aFields[$sName]['savevalue'];
                    }
                    if (array_key_exists($sName, $this->aPrepareDefaultsActive)) {
                        $this->aPrepareDefaultsActive[$sName] = $this->optionalIsActive(array('realname' => $sName));
                    }
                    if (array_key_exists($sName, $this->aPrepareDefaults)) {
                        $this->aPrepareDefaults[$sName] = $this->getField($sName, 'value');
                    } else {
                        MLModul::gi()->setConfig($sName, $this->getField($sName, 'value'));
                        //MLDatabase::factory('config')->set('mpId', MLModul::gi()->getMarketPlaceId())->set('mkey', $sName)->set('value', $this->getField($sName, 'value'))->save();
                    }
                }
            }
            MLModul::gi()->sendConfigToApi();
            MLDatabase::factory('preparedefaults')
                ->set('mpId', MLModul::gi()->getMarketPlaceId())
                ->set('name', 'defaultconfig')
                ->set('values', $this->aPrepareDefaults)
                ->set('active', $this->aPrepareDefaultsActive)
                ->save()
            ;
            if ($blAuthKeysChanged) {
                $this->isAuthed($aAuthKeys);
            }
            if (MLModul::gi()->isAuthed() && $this->isWizard()) {// redirect to next form
                $sNextController = $this->getNextController();
                if (!empty($sNextController)) {
                    MLHttp::gi()->redirect($this->getUrl(array(
                        'controller' => substr($this->getRequest('controller'), 0, strrpos($this->getRequest('controller'), '_')).'_'.$sNextController,
                        'wizard' => 'true'
                    )));
                } else {
                    $aController = explode('_', MLRequest::gi()->get('controller'));
                    MLHttp::gi()->redirect($this->getUrl(array(
                        'controller' => current($aController)
                    )));
                }
            }
            $this->aFields = array();
            return $this;
        } else {
            if ($this->isWizard()) {
                $sNextController = $this->getNextController(true);
                if ($sNextController == '') {
                    $sForward = MLI18n::gi()->get('form_action_finish_wizard_save');
                } else {
                    $sForwardI18n = MLFilesystem::gi()->callStatic('controller_'.$sNextController, 'getTabTitle');
                    $sForward = sprintf(MLI18n::gi()->get('form_action_wizard_save'), $sForwardI18n);
                }
            } else {
                $sForward = MLI18n::gi()->get('form_action_save');
            }
            return array(
                'aI18n' => array('label' => $sForward),
                'aForm' => array('type' => 'submit', 'position' => 'right')
            );
        }
    }

}
