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
abstract class ML_Modul_Model_Modul_Abstract {

    /**
     * prepareConfig-array cache
     * @var array $aPrepareDefaultConfig
     */
    protected $aPrepareDefaultConfig = null;
    /**
     * config-array cache
     * @var array $aConfig
     */
    protected $aConfig = null;

    /**
     * backup config-array for checking changed values
     * @var array $aConfigBackup
     */
    protected $aConfigBackup = array();

    /**
     * constructor prepares MagnaConnector
     */
    public function __construct() {
        MagnaConnector::gi()->setAddRequestsProps(array(
            'SUBSYSTEM' => $this->getMarketPlaceName(),
            'MARKETPLACEID' => $this->getMarketPlaceId()
        ));
        MLShop::gi()->getShopInfo();
    }

    /**
     * returns current marketplace-id
     * @return int
     */
    public function getMarketPlaceId() {
        return (int)MLRequest::gi()->get('mp');
    }

    /**
     * returns marketplace name
     * @param $blInter bool if false return human readable name
     * @return string marketplace name for work inside plugin
     */
    abstract public function getMarketPlaceName($blIntern = true);

    /**
     * checks if configured completely
     * @return boolean
     */
    public function isConfigured() {
        if (MLRequest::gi()->data('wizard')) {
            return false;
        }
        $aSettings = MLSetting::gi()->get('aModules');
        $aRequiredConfig = array_unique(array_merge(array_keys($aSettings[$this->getMarketPlaceName()]['authKeys']), $aSettings[$this->getMarketPlaceName()]['requiredConfigKeys']));
        $aMissingConfigKeys = array();
        foreach ($aRequiredConfig as $sName) {
            if (    (
                        ($this->getConfig($sName) === null)
                     || ($this->getConfig($sName) === '')
                )
                && !in_array($sName, $aMissingConfigKeys)
            ) {
                $aMissingConfigKeys[] = $sName;
            }
        }
        if (count($aMissingConfigKeys) != 0) {
            MLMessage::gi()->addDebug(MLModul::gi()->getMarketPlaceName().'('.MLModul::gi()->getMarketPlaceId().') missing '.(count($aMissingConfigKeys)).' config-keys.', $aMissingConfigKeys);
            return false;
        } else {
            return $this->isAuthed();
        }
    }

    /**
     * check if auth-data for mp is correct
     * @todo check auth keys and no api-request
     * @return bool
     */
    public function isAuthed($blResetCache = false) {
        $aSettings = MLSetting::gi()->get('aModules');
        foreach(array_keys($aSettings[$this->getMarketPlaceName()]['authKeys']) as $sAuthkey) {
            if ($this->getConfig($sAuthkey) === null) {
                return false;
            }
        }
        if ($blResetCache) {
            MLCache::gi()->delete(strtoupper(__class__).'__'.$this->getMarketPlaceId().'_authed');
        }
        if (!MLCache::gi()->exists(strtoupper(__class__).'__'.$this->getMarketPlaceId().'_authed')) {
            try {
                MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'IsAuthed',
                ), $blResetCache);
                MLCache::gi()->set(strtoupper(__class__).'__'.$this->getMarketPlaceId().'_authed', true, 60 * 30);
                MLMessage::gi()->remove('authError_'.get_class($this));
            } catch (MagnaException $oEx) {
                $oEx->setCriticalStatus(false);
                MLMessage::gi()->addDebug($oEx);
                MLMessage::gi()->addError(
                    sprintf(MLI18n::gi()->get('ML_MAGNACOMPAT_ERROR_ACCESS_DENIED'), MLModul::gi()->getMarketPlaceName(false)),
                    array('md5' => 'authError_'.get_class($this))
                );
                return false;
            } catch (Exception $oEx) {
                MLMessage::gi()->addDebug($oEx);
            }
        }
        return true;
    }

    /**
     * return default values for config
     * @return array default key => value
     */
    protected function getDefaultConfigValues() {
        return array(
            'import' => 1,
            'preimport.start' => date('Y-m-d'),
        );
    }

    public function getConfig($sName = null) {
        if ($this->aConfig === null) {
            $aConf = $this->getDefaultConfigValues();
            $sMarketPlace = $this->getMarketPlaceName();
            foreach (array(0 => 'general', $this->getMarketPlaceId() => $this->getMarketPlaceName()) as $iId => $sMarketPlace) {
                foreach (MLDatabase::getDbInstance()->fetchArray("select mkey, value from magnalister_config where mpid='".$iId."'") as $aRow) {
                    $sKey = (substr($aRow['mkey'], 0, strlen($sMarketPlace) + 1) == $sMarketPlace.'.') ? substr($aRow['mkey'], strlen($sMarketPlace.'.')) : $aRow['mkey'];
                    $aConf[$sKey] = MLHelper::getEncoderInstance()->decode($aRow['value']);
                }
            }
            $this->aConfig = $aConf;
            $this->aConfigBackup = $aConf;
        }
        if ($sName !== null) {
            $sName = substr($sName, 0, strlen($this->getMarketPlaceName().'.')) == $this->getMarketPlaceName().'.' ? substr($sName, strlen($this->getMarketPlaceName().'.')) : $sName;
        }
        if ($sName == null) {
            return $this->aConfig;
        } elseif (array_key_exists($sName, $this->aConfig)) {
            return $this->replaceConfig($sName , $this->aConfig[$sName]);
        } else {
            return null;
        }
    }
    
    /**
     * get preparedefaultconfig array
     * @param type $sName
     * @return type
     */
    public function getPrepareDefaultConfig($sName = null) {
        if ($this->aPrepareDefaultConfig === null) {
            $aPrepareDefaults = MLDatabase::factory('preparedefaults')->set('name', 'defaultconfig')->get('values');
            $aPrepareDefaults = is_array($aPrepareDefaults) ? $aPrepareDefaults : array();
            $aPrepareDefaultsConfig = MLSetting::gi()->get(strtolower($this->getMarketPlaceName()).'_prepareDefaultsFields');
            $aPrepareDefaultsConfig = isset($aPrepareDefaultsConfig) ? $aPrepareDefaultsConfig : array();
            foreach ($aPrepareDefaultsConfig as $sDefaultKey) {
                $aPrepareDefaults[$sDefaultKey] = isset($aPrepareDefaults[$sDefaultKey]) ? $aPrepareDefaults[$sDefaultKey] : null;
            }
            $this->aPrepareDefaultConfig = $aPrepareDefaults;
        }
        if ($sName == null) {
            return $this->aPrepareDefaultConfig;
        } elseif (array_key_exists($sName, $this->aPrepareDefaultConfig)) {
            return $this->replaceConfig($sName , $this->aPrepareDefaultConfig[$sName]);
        } else {
            return null;
        }
    }
    
    protected function replaceConfig($sName , $sValue){
        if($sName == 'mwstfallback'){
            $sValue = str_replace('%', '', $sValue);
        }
        return $sValue;
    }

    public function setConfig($sName, $mValue, $blSave = true) {
        if ($this->aConfig === null) {
            $this->getConfig();//init
        }
        $this->aConfig[$sName] = $mValue;
        if ($blSave) {
            MLDatabase::factory('config')->set('mpId', MLModul::gi()->getMarketPlaceId())->set('mkey', $sName)->set('value', $mValue)->save();
        }
        return $this;
    }

    public function sendConfigToApi() {
        $aSend = array();
        foreach ($this->getConfigApiKeysTranslation() as $sKey => $aApi) {
            $aSend[$aApi['api']] = $aApi['value'];
        }
        $aSend['PlugIn.Label'] = getDBConfigValue(array('general.tabident', MLModul::gi()->getMarketPlaceId()), '0', '');
        try {
            MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'SetConfigValues',
                'DATA' => $aSend,
            ));
        } catch (MagnaException $oEx) {
        }
        try {
            MagnaConnector::gi()->setTimeOutInSeconds(1);
            MagnaConnector::gi()->submitRequest(array(
                'ACTION' => 'SavePluginConfig',
                'DATA' => $this->getConfig(),
            ));
        } catch (MagnaException $oEx) {
        }
        MagnaConnector::gi()->resetTimeOut();
        return $this;
    }

    /**
     * @return array('configKeyName'=>array('api'=>'apiKeyName', 'value'=>'currentSantizedValue'))
     */
    abstract protected function getConfigApiKeysTranslation();

    /**
     * @return int timestamp
     * @throws Exception no Import
     */
    public function getOrderImportStartTime() {
        if (!$this->getConfig('import') || $this->getConfig('import') == 'false') {
            throw new Exception('no import');
        } else {
            $iStartTime = MLSetting::gi()->get('iOrderMinTime');
            $aTimes = array($iStartTime);
            foreach (array('orderimport.lastrun', 'preimport.start') as $sConfig) {
                $iTimestamp = strtotime($this->getConfig($sConfig));
                if ($sConfig == 'orderimport.lastrun') {
                    $iTimestamp = $iTimestamp - MLSetting::gi()->get('iOrderPastInterval');
                } elseif (
                    $sConfig == 'preimport.start'
                    &&
                    $iTimestamp > time()
                ) {
                    throw new Exception('begin import time is in future');
                }
                $aTimes[] = $iTimestamp;
                $iStartTime = $iTimestamp > $iStartTime ? $iTimestamp : $iStartTime;
            }
            return $iStartTime;
        }
    }
    
    /**
     * to get specific configuration that can have several option and user can select one of them as a default 
     * @param type $sName
     * @return type
     */
    public function getOneFromMultiOptionConfig($sName, $iSelected = null) {
        $aData = array();
        $aDefault = $this->getConfig($sName);
        if($iSelected === null){
            $iDetault = 0;
            foreach($aDefault as $iKey => $sValue){
                if($sValue['default'] == '1') {
                   $iDetault = $iKey;
                   break;
                }
            }
        }else{
            $iDetault = $iSelected;
        }
        foreach($this->getConfig() as $sKey => $aConfig) {
            if(strpos($sKey, $sName.'.') !== false && isset($aConfig[$iDetault])){
                $aData[str_replace($sName.'.', '', $sKey)] = $aConfig[$iDetault];
            }
        }
        return $aData;
    }

    /**
     * @var string $sType defines price type, if marketplace supports multiple prices
     * @return ML_Shop_Model_Price_Interface
     */
    abstract public function getPriceObject($sType = null);

}
