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
 * $Id: Configurator.php 5269 2015-02-25 14:06:42Z markus.bauer $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */
defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');

class Configurator {

    private $form = array();
    private $id = '';
    private $magnaQuery = array();
    private $config = array();
    private $mpID = 0;
    private $requiredConfigKeys = array();
    private $missingConfigKeys = array();
    private $testingMethods = array();
    private $notCorrect = array();
    private $renderResetJS = false;
    private $renderTabIdent = false;
    private $topHTML = '';
    private $keyOrder = array();
    private $ajaxUpdateFuncs = array();
    private $_GET = array();
    private $_POST = array();

    public function __construct(&$form, $mpID, $id = '', $requiredConfigKeys = array()) {
        MLFormHelper::getShopInstance()->manipulateForm($form);
        global $magnaConfig, $_magnaQuery, $_MagnaSession, $_modules;
        $this->_GET = MLRequest::gi()->data();
        $this->_POST = MLRequest::gi()->data();
        $this->form = &$form;
        $this->id = $id;
        $this->mpID = $mpID;

        $this->form['invisible']['fields'][] = array(
            'key' => (empty($_MagnaSession['currentPlatform']) ? 'general' : $_MagnaSession['currentPlatform']) . '.firstactivation',
            'type' => 'hidden',
            'default' => date('Y-m-d H:i:s')
        );

        $this->magnaQuery = $_magnaQuery;
        if (!array_key_exists((string) $this->mpID, $magnaConfig['db'])) {
            loadDBConfig((string) $this->mpID);
        }
        $this->config = &$magnaConfig['db'][$this->mpID];
        try {
            if (MLModul::gi()->getMarketPlaceName() == 'ebay') {
                $oModelPrepareDefaults = MLDatabase::factory('preparedefaults');
                if (is_array($oModelPrepareDefaults->get('values'))) {
                    foreach ($oModelPrepareDefaults->get('values') as $sValue => $mValue) {
                        if (is_bool($mValue)) {
                            $sCurrent = array('val' => (bool) $oModelPrepareDefaults->getValue($sValue));
                        } else {
                            $sCurrent = $oModelPrepareDefaults->getValue($sValue);
                        }
                        $this->config['prepareDefaults.value.' . $sValue] = $sCurrent;
                    }
                }
                if (is_array($oModelPrepareDefaults->get('active'))) {
                    foreach ($oModelPrepareDefaults->get('active') as $sValue => $mValue) {
                        $sCurrent = array('val' => (bool) $oModelPrepareDefaults->getActive($sValue));
                        $this->config['prepareDefaults.active.' . $sValue] = $sCurrent;
                    }
                }
            }
        } catch (Exception $oEx) {//globla config
        }
        if (!empty($_MagnaSession['currentPlatform']) &&
                array_key_exists($_MagnaSession['currentPlatform'], $_modules) &&
                array_key_exists('requiredConfigKeys', $_modules[$_MagnaSession['currentPlatform']])
        ) {
            $this->requiredConfigKeys = $_modules[$_MagnaSession['currentPlatform']]['requiredConfigKeys'];
        }

        if (!empty($requiredConfigKeys)) {
            $this->requiredConfigKeys = array_merge(
                    $this->requiredConfigKeys, $requiredConfigKeys
            );
        }

        $this->testingMethods = array(
            'int' => MLI18n::gi()->ML_CONFIG_NOT_INT,
            'float' => MLI18n::gi()->ML_CONFIG_NOT_FLOAT,
            'notempty' => MLI18n::gi()->ML_CONFIG_NOT_EMPTY,
            'contains' => MLI18n::gi()->ML_CONFIG_MUST_CONTAIN,
            'regex' => MLI18n::gi()->ML_CONFIG_INVALID_CHARS,
        );

        $this->itemSettings = array(
            'trim' => true,
            'save' => true,
        );

        
        if (!array_key_exists('expert', $this->_GET) || ($this->_GET['expert'] != 'true')) {
            foreach ($form as $sKey => $aValue) {
                $iDelete = 0;
                foreach ($aValue['fields'] as $aField) {
                    if (isset($aField['expertsetting']) && $aField['expertsetting'] == true) {
                        ++$iDelete;
                    }
                }
                if ($iDelete == count($aValue['fields'])) {
                    unset($form[$sKey]);
                }
            }
        }
    }

    public function setRenderTabIdent($b) {
        $this->renderTabIdent = $b;
    }

    public function setTopHTML($html) {
        $this->topHTML = $html;
    }

    public function setRequiredConfigKeys($requiredConfigKeys) {
        $this->requiredConfigKeys = array_merge(
                $this->requiredConfigKeys, $requiredConfigKeys
        );
    }

    private function keySort($lkey, $rkey) {
        //echo $lkey.' '.$rkey.'<br>';
        if (!array_key_exists($lkey, $this->keyOrder) || !array_key_exists($rkey, $this->keyOrder))
            return 0;

        if ($this->keyOrder[$lkey] == $this->keyOrder[$rkey])
            return 0;

        return $this->keyOrder[$lkey] > $this->keyOrder[$rkey] ? 1 : -1;
    }

    public function sortKeys($order) {
        if (empty($order))
            return;
        $this->keyOrder = array_flip($order);

        $invisible = $this->form['invisible'];
        unset($this->form['invisible']);

        $tmpForm = array();
        foreach ($this->form as $key => $item) {
            if (!array_key_exists($key, $this->keyOrder)) {
                $tmpForm[$key] = $item;
                unset($this->form[$key]);
            }
        }

        uksort($this->form, array($this, 'keySort'));

        if (!empty($tmpForm)) {
            foreach ($tmpForm as $key => $item) {
                $this->form[$key] = $item;
            }
            unset($tmpForm);
        }

        $this->form['invisible'] = $invisible;
    }

    private function verify($verify, $key, $value) {
        $correct = false;

        if ($verify === false) {
            return true;
        }
        if (array_key_exists($verify, $this->testingMethods)) {
            switch ($verify) {
                case 'int': {
                        $correct = preg_match('/^-?[0-9]*$/', $value);
                        break;
                    }
                case 'float': {
                        $value = str_replace(',', '.', $value);
                        $correct = is_numeric($value);
                        $value = (float) $value;
                        break;
                    }
                case 'notempty': {
                        $correct = !empty($value);
                        break;
                    }
            }
            if (!$correct) {
                $this->notCorrect[$key] = $this->testingMethods[$verify];
            }
        }

        if (preg_match('/^contains\(\"(.*)\"\)$/', $verify, $match)) {
            if (!($correct = (strpos($value, $match[1]) === false) ? false : true)) {
                $this->notCorrect[$key] = sprintf($this->testingMethods['contains'], $match[1]);
            }
        }
        if (strpos($verify, 'regex') === 0) {
            $regex = '/^' . substr($verify, strlen('regex("'), -strlen('")')) . '$/';
            if (empty($value) || preg_match($regex, $value)) {
                $correct = true;
            } else {
                $this->notCorrect[$key] = $this->testingMethods['regex'];
            }
        }
        return $correct;
    }

    public function processPOST() {
        $keysToSubmit = array();
        $tmpKeysToSubmit = array();
        try {
            if (MLModul::gi()->getMarketPlaceName() == 'ebay') {
                $oModelPrepareDefaults = MLDatabase::factory('preparedefaults')->set('active', array())->set('values', array());
            }
        } catch (Exception $oEx) {//global config
        }

        if (!(array_key_exists('conf', $this->_POST) && is_array($this->_POST['conf']) &&
                array_key_exists('configtool', $this->_POST) && ($this->_POST['configtool'] == 'MagnaConfigurator') /* Nur um gaaaanz sicher zu gehen :D */
                )) {
            return true;
        }

        $noError = true;

        /* Save TabIdent */
        if ($this->renderTabIdent && array_key_exists('tabident', $this->_POST)) {
            setDBConfigValue(
                    array('general.tabident', $this->mpID), '0', stringToUTF8(trim($this->_POST['tabident'])), true
            );
            $keysToSubmit['PlugIn.Label'] = trim($this->_POST['tabident']);
        }
        #echo print_m(getDBConfigValue('general.tabident', '0'));
        #echo print_m($_POST);
        #echo print_m($this->config);

        /* Save config */
        $postData = $this->_POST['conf'];
        foreach ($postData as $key => &$value) {
            $key = trim($key);

            $verify = false;
            $correct = false;

            $settings = $this->itemSettings;

            $foundItem = array();

            foreach ($this->form as $fi) {
                foreach ($fi['fields'] as $configItem) {
                    if ($key == $configItem['key']) {
                        $foundItem = $configItem;
                        break;
                    }
                    if (isset($configItem['morefields'])) {
                        foreach ($configItem['morefields'] as $moreFields) {
                            if ($key == $moreFields['key']) {
                                $foundItem = $moreFields;
                                break;
                            }
                        }
                    }
                }
            }

            if (!empty($foundItem)) {
                if (array_key_exists('settings', $foundItem)) {
                    $settings = array_merge(
                            $this->itemSettings, $foundItem['settings']
                    );
                }
                if (array_key_exists('verify', $foundItem)) {
                    $verify = $foundItem['verify'];
                }
                if (array_key_exists('submit', $foundItem) && !empty($foundItem['submit'])) {
                    $tmpKeysToSubmit[] = array(
                        'confKey' => $foundItem['key'],
                        'apiKey' => $foundItem['submit'],
                    );
                }
            }

            if (is_array($value)) {
                foreach ($value as $k => &$v) {
                    if (($v == 'true') || ($v == 'false')) {
                        $value[$k] = ($v == 'true') ? true : false;
                    }
                }
                arrayEntitiesToUTF8($value);
                $value = json_encode($value);
            }
            if ($settings['trim']) {
                $value = trim($value);
            }
            if (!$settings['save'] && (!empty($value) || (isset($this->config[$key]) && $this->config[$key] == '__saved__'))) {
                $value = '__saved__';
            }

            $correct = $this->verify($verify, $key, $value);
            if (!empty($foundItem) && ($foundItem['type'] == 'extern') && is_callable($foundItem['procFunc'])) {
                $correct = call_user_func($foundItem['procFunc'], array_merge(
                                $foundItem['params'], array(
                    'key' => $foundItem['key'],
                    'kind' => 'save',
                                )
                        ), $value);
            }
            if (!$correct)
                $noError = false;
            if (!empty($key) && $correct) {
                if (empty($value) && ($value !== '0'))
                    $value = '';
                if (($value === 'null') || ($value === null))
                    $value = '';
                try {
                    $blEbay = MLModul::gi()->getMarketPlaceName() == 'ebay';
                } catch (Exception $oEx) {//global config
                    $blEbay = false;
                }
                if (strpos($key, 'prepareDefaults.') === 0 && $blEbay) {
                    $sTemp = substr($key, strpos($key, '.') + 1);
                    if (preg_match('/\{"val":(true|false)\}/', $value)) {
                        $value = json_decode($value)->val;
                    }
                    if ($key == 'prepareDefaults.value.shippinginternational') {//ebay
                        $oModelPrepareDefaults->setActive('shippinginternational', false);
                        $aIntShip = MLHelper::getEncoderInstance()->decode($value);
                        foreach ($aIntShip as $sKey => $aValue) {
                            if ($aValue['ShipToLocation'] !== 'None' && $aValue['ShippingService'] !== '') {
                                $oModelPrepareDefaults->setActive('shippinginternational', true);
                            } else {
                                unset($aIntShip[$sKey]);
                            }
                        }
                        $value = MLHelper::getEncoderInstance()->encode($aIntShip);
                    } elseif (
                            $key == 'prepareDefaults.value.shippinglocalprofile' ||
                            $key == 'prepareDefaults.value.shippinginternationalprofile'
                    ) {
                        if ($value == 0) {
                            $oModelPrepareDefaults->setActive(substr($sTemp, strpos($sTemp, '.') + 1), false);
                        } else {
                            $oModelPrepareDefaults->setActive(substr($sTemp, strpos($sTemp, '.') + 1), true);
                        }
                    }
                    $oModelPrepareDefaults->{'set' . substr($sTemp, 0, strpos($sTemp, '.'))}(substr($sTemp, strpos($sTemp, '.') + 1), $value);
                } else {
                    if (preg_match('/^.*preimport\.start/', $key)) {
                        $iValue = strtotime($value);
                        if ($iValue > 0) {
                            $value = date('Y-m-d', $iValue);
                        }
                    }
                    $data = array('mpID' => $this->mpID, 'mkey' => $key, 'value' => $value);
                    if (MLDatabase::getDbInstance()->recordExists(TABLE_MAGNA_CONFIG, array(
                                'mpID' => $this->mpID,
                                'mkey' => $key,
                            ))) {
                        MLDatabase::getDbInstance()->update(TABLE_MAGNA_CONFIG, $data, array(
                            'mpID' => $this->mpID,
                            'mkey' => $key
                        ));
                    } else {
                        MLDatabase::getDbInstance()->insert(TABLE_MAGNA_CONFIG, $data);
                    }
                }
            }
        }
        try {
            if (MLModul::gi()->getMarketPlaceName() == 'ebay') {

                $oModelPrepareDefaults->save()->init(true);
                if (is_array($oModelPrepareDefaults->get('values'))) {
                    foreach ($oModelPrepareDefaults->get('values') as $sValue => $mValue) {
                        if (is_bool($mValue)) {
                            $sCurrent = array('val' => (bool) $oModelPrepareDefaults->getValue($sValue));
                        } else {
                            $sCurrent = $oModelPrepareDefaults->getValue($sValue);
                        }
                        $this->config['prepareDefaults.value.' . $sValue] = $sCurrent;
                    }
                }
                if (is_array($oModelPrepareDefaults->get('active'))) {
                    foreach ($oModelPrepareDefaults->get('active') as $sValue => $mValue) {
                        $sCurrent = array('val' => (bool) $oModelPrepareDefaults->getActive($sValue));
                        $this->config['prepareDefaults.active.' . $sValue] = $sCurrent;
                    }
                }
            }
        } catch (Exception $oEx) {//global config
        }
        // reload DB Config
        $cfgData = loadDBConfig($this->mpID);
        if (!empty($tmpKeysToSubmit)) {
            foreach ($tmpKeysToSubmit as $key) {
                $keysToSubmit[$key['apiKey']] = getDBConfigValue($key['confKey'], $this->mpID);
            }
        }
        if (!empty($keysToSubmit)) {
            $request = array(
                'ACTION' => 'SetConfigValues',
                'DATA' => $keysToSubmit,
            );
            try {
                MagnaConnector::gi()->submitRequest($request);
            } catch (MagnaException $me) {
                
            }
        }

        if (is_array($cfgData)) {
            $request = array(
                'ACTION' => 'SavePluginConfig',
                'DATA' => $cfgData,
            );
            try {
                MagnaConnector::gi()->setTimeOutInSeconds(1);
                MagnaConnector::gi()->submitRequest($request);
                MagnaConnector::gi()->resetTimeOut();
            } catch (MagnaException $me) {
                $me->setCriticalStatus(false);
            }
        }

        return $noError;
    }

    private function getDefault($key) {
        foreach ($this->form as $item) {
            if (!isset($item['fields']) || empty($item['fields'])) {
                continue;
            }
            foreach ($item['fields'] as $field) {
                if ($field['key'] == $key) {
                    return (isset($field['default']) && !empty($field['default'])) ? $field['default'] : '';
                }
                if (isset($field['morefields']) && !empty($field['morefields'])) {
                    foreach ($field['morefields'] as $mfield) {
                        if ($mfield['key'] == $key) {
                            return (isset($mfield['default']) && !empty($mfield['default'])) ? $mfield['default'] : '';
                        }
                    }
                }
            }
        }
    }

    private function renderDBCols($args) {
        $values = array();
        if (!empty($args['value']) && MLDatabase::getDbInstance()->tableExists($args['value'])) {
            $values = MLDatabase::getDbInstance()->getTableCols($args['value']);
            if (!empty($values)) {
                $values = array_flip($values);
                foreach ($values as $col => &$colname) {
                    $colname = $col;
                }
            }
        }
        if (empty($values)) {
            $values = array('' => MLI18n::gi()->ML_LABEL_SELECT_TABLE_FIRST);
        }
        $default = (array_key_exists($args['key'], $this->config) && array_key_exists('column', (array) $this->config[$args['key']])) ? $this->config[$args['key']]['column'] : false;
        $html = '';
        foreach ($values as $k => $v) {
            $html .= '<option value="' . $k . '"' . (($default == $k) ? ' selected="selected"' : '') . '>' . $v . '</option>' . "\n";
        }
        return $html;
    }

    public function processAjaxRequest() {
        if (!isset($this->_POST['action']) || empty($this->_POST['action'])) {
            return '';
        }
        switch ($this->_POST['action']) {
            case 'getDefault': {
                    if (!isset($this->_POST['key']) || empty($this->_POST['key'])) {
                        return '';
                    }
                    return $this->getDefault(str_replace('_', '.', preg_replace('/^config_/', '', $this->_POST['key'])));
                    break;
                }
            case 'update': {
                    $args = array(
                        'key' => $this->_POST['key'],
                        'value' => $this->_POST['value'],
                    );
                    if (strpos($this->_POST['function'], 'UpdateColumns') === 0) {
                        return $this->renderDBCols($args);
                    }
                    if (strtolower(substr($this->_POST['function'], 0, 6)) == 'shop::') {
                        $sMethod = substr($this->_POST['function'], 6);
                        $oShopHelper = MLFormHelper::getShopInstance();
                        if (method_exists($oShopHelper, $sMethod)) {
                            return $oShopHelper->{$sMethod}($args);
                        } else {
                            return 'FALIURE';
                        }
                    } elseif (strtolower(substr($this->_POST['function'], 0, 7)) == 'modul::') {
                        $sMethod = substr($this->_POST['function'], 7);
                        $oModulHelper = MLFormHelper::getModulInstance();
                        if (method_exists($oModulHelper, $sMethod)) {
                            return $oModulHelper->{$sMethod}($args);
                        } else {
                            return 'FALIURE';
                        }
                    } else {
                        return 'FALIURE';
                    }
                }
            case 'extern': {
                    $args = $this->_POST;
                    unset($args['function']);
                    unset($args['action']);
                    if (strtolower(substr($this->_POST['function'], 0, 6)) == 'shop::') {
                        $sMethod = substr($this->_POST['function'], 6);
                        $oShopHelper = MLFormHelper::getShopInstance();
                        if (method_exists($oShopHelper, $sMethod)) {
                            return $oShopHelper->{$sMethod}($args);
                        } else {
                            return 'FALIURE';
                        }
                    } elseif (strtolower(substr($this->_POST['function'], 0, 7)) == 'modul::') {
                        $sMethod = substr($this->_POST['function'], 7);
                        $oModulHelper = MLFormHelper::getModulInstance();
                        if (method_exists($oModulHelper, $sMethod)) {
                            return $oModulHelper->{$sMethod}($args);
                        } else {
                            return 'FALIURE';
                        }
                    } else {
                        return 'FALIURE';
                    }
                }
        }
        return '';
    }

    private function renderSubInput($item, $subKey, $pItem) {
        $idkey = 'config_' . str_replace('.', '_', $pItem['key']) . '_' . $subKey . '_' . $item['type'];

        if (array_key_exists($pItem['key'], $this->config) && array_key_exists($subKey . '.value', $this->config[$pItem['key']])) {
            $value = $this->config[$pItem['key']][$subKey . '.value'];
            if (is_array($value) && is_array($item['default'])) {
                $value = array_merge($item['default'], $value);
            }
        } else if (isset($item['default'])) {
            $value = $item['default'];
        }

        if (array_key_exists('ajaxlinkto', $item)) {
            $item['ajaxlinkto']['from'] = $pItem['key'];
            $this->ajaxUpdateFuncs[] = $item['ajaxlinkto'];
        }

        $html = '';
        $parameters = '';
        if (isset($item['parameters'])) {
            foreach ($item['parameters'] as $key => $val) {
                $parameters .= ' ' . $key . '="' . $val . '"';
            }
        }

        if (!in_array($item['type'], array('checkbox', 'radio')) && array_key_exists('label', $item)) {
            $html .= '<label for="' . $idkey . '">' . $item['label'] . '</label>' . "\n";
        }

        switch ($item['type']) {
            case 'selection': {
                    $html .= '<select id="config_' . $idkey . '" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $pItem['key'] . '][' . $subKey . '.value]')
                            . '"' . $parameters . '>' . "\n";
                    foreach ($item['values'] as $k => $v) {
                        $html .= '	<option value="' . $k . '"' . (($value == $k) ? ' selected="selected"' : '') . '>' . $v . '</option>' . "\n";
                    }
                    $html .= '</select>' . "\n";
                    break;
                }
        }

        //echo print_m($html);
        //echo print_m($item);
        //echo print_m($pItem);
        return $html;
    }

    private function renderInput($item) {
        $value = '';
        if (array_key_exists($item['key'], $this->config)) {
            $value = $this->config[$item['key']];
            if (is_array($value) && isset($item['default']) && is_array($item['default'])) {
                //echo print_m($item['default'], 'default'); echo print_m($value, 'config');
                //var_dump(isNumericArray($item['default']), isNumericArray($value));
                if (isNumericArray($item['default']) && isNumericArray($value)) {
                    foreach ($item['default'] as $k => $v) {
                        if (array_key_exists($k, $value))
                            continue;
                        $value[$k] = $item['default'][$k];
                    }
                } else {
                    $value = array_merge($item['default'], $value);
                }
            }
        } else if (isset($item['default'])) {
            $value = $item['default'];
        }
        $item['__value'] = $value;

        $idkey = str_replace('.', '_', $item['key']);

        $parameters = '';
        if (isset($item['parameters'])) {
            foreach ($item['parameters'] as $key => $val) {
                $parameters .= ' ' . $key . '="' . $val . '"';
            }
        }
        if (array_key_exists('ajaxlinkto', $item)) {
            $item['ajaxlinkto']['from'] = $item['key'];
            $item['ajaxlinkto']['fromid'] = 'config_' . $idkey;
            if (array_key_exists('key', $item['ajaxlinkto'])) {
                $item['ajaxlinkto']['toid'] = 'config_' . str_replace('.', '_', $item['ajaxlinkto']['key']);
                $this->ajaxUpdateFuncs[] = $item['ajaxlinkto'];
            } else { # mehrere ajaxlinkto eintraege
                foreach ($item['ajaxlinkto'] as $aLiTo) {
                    if (!is_array($aLiTo) || !array_key_exists('key', $aLiTo))
                        continue;
                    $aLiTo['toid'] = 'config_' . str_replace('.', '_', $aLiTo['key']);
                    $this->ajaxUpdateFuncs[] = $aLiTo;
                }
            }
        }

        $html = '';
        switch ($item['type']) {
            case 'text':
            case 'password':
            case 'hidden': {
                    if (($item['type'] == 'password') && (!empty($value))) {
//					$html .= '<label for="config_'.$idkey.'" class="savedPassword"><span>'.MLI18n::gi()->ML_LABEL_SAVED.'</span></label>';
                    }
                    if ($value == '__saved__') {
                        $__saved__ = true;
                        $value = '';
                    } else {
                        $__saved__ = false;
                    }
                    if (isset($item['formatstr']) && !empty($item['formatstr'])) {
                        $value = sprintf($item['formatstr'], $value);
                    }
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                        $item['cssClasses'][] = 'fullwidth';
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                    $html .= '<input' . ($item['type'] == 'password' && $value == '' && $__saved__ == true ? ' placeholder="' . MLI18n::gi()->ML_LABEL_SAVED . '"' : '') . ' type="' . $item['type'] . '"' . $class . ' id="config_' . $idkey . '" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . ']')
                            . '" value="' . (string) $value . '"' . $parameters . '/>';
                    break;
                }
            case 'selection': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    if (is_string($item['values'])) {
                        if (strtolower(substr($item['values'], 0, 6)) == 'shop::') {
                            $sMethod = substr($item['values'], 6);
                            $oShopHelper = MLFormHelper::getShopInstance();
                            if (method_exists($oShopHelper, $sMethod)) {
                                $item['values'] = $oShopHelper->{$sMethod}();
                            } else {
                                $html.='<div class="noticeBox">' . $item['values'] . '</div>';
                            }
                        } elseif (strtolower(substr($item['values'], 0, 7)) == 'modul::') {
                            $sMethod = substr($item['values'], 7);
                            $oModulHelper = MLFormHelper::getModulInstance();
                            if (method_exists($oModulHelper, $sMethod)) {
                                $item['values'] = $oModulHelper->{$sMethod}();
                            } else {
                                $html.='<div class="noticeBox">' . $item['values'] . '</div>';
                            }
                        }
                    }
                    if (isset($item['values']) && is_array($item['values'])) {
                        $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                        $html .= '<select id="config_' . $idkey . '" name="' .
                                MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . ']')
                                . '"' . $parameters . '' . $class . '>' . "\n";
                        foreach ($item['values'] as $k => $v) {
                            if ($k === '__calc__') {
                                if (preg_match('/^range\(([0-9]*),([0-9]*)\)$/', $v, $matches)) {
                                    $a = range($matches[1], $matches[2]);
                                    foreach ($a as $nV) {
                                        $html .= '<option value="' . $nV . '"' . (($value == $nV) ? ' selected="selected"' : '') . '>' . $nV . '</option>' . "\n";
                                    }
                                }
                            } else {
                                $html .= '<option value="' . $k . '"' . (($value == $k) ? ' selected="selected"' : '') . '>' . (!preg_match('/&[^\s;]*;/', $v) ? fixHTMLUTF8Entities($v) : $v) . '</option>' . "\n";
                            }
                        }
                        $html .= '</select>' . "\n";
                    } elseif (MLSetting::gi()->get('blDebug')) {
                        $html.='<div class="noticeBox">' . $item['key'] . '</div>';
                    }
                    break;
                }
            case 'multiselection': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    if (is_string($item['values'])) {
                        if (strtolower(substr($item['values'], 0, 6)) == 'shop::') {
                            $sMethod = substr($item['values'], 6);
                            $oShopHelper = MLFormHelper::getShopInstance();
                            if (method_exists($oShopHelper, $sMethod)) {
                                $item['values'] = $oShopHelper->{$sMethod}();
                            } else {
                                $html.='<div class="noticeBox">' . $item['values'] . '</div>';
                            }
                        } elseif (strtolower(substr($item['values'], 0, 7)) == 'modul::') {
                            $sMethod = substr($item['values'], 7);
                            $oModulHelper = MLFormHelper::getModulInstance();
                            if (method_exists($oModulHelper, $sMethod)) {
                                $item['values'] = $oModulHelper->{$sMethod}();
                            } else {
                                $html.='<div class="noticeBox">' . $item['values'] . '</div>';
                            }
                        }
                    }
                    if (is_array($item['values'])) {
                        $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                        $html .= '<select id="config_' . $idkey . '" name="' .
                                MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . '][]')
                                . '" multiple="multiple" ' . $parameters . '' . $class . '>' . "\n";
                        foreach ($item['values'] as $k => $v) {
                            if ($k === '__calc__') {
                                if (preg_match('/^range\(([0-9]*),([0-9]*)\)$/', $v, $matches)) {
                                    $a = range($matches[1], $matches[2]);
                                    foreach ($a as $nV) {
                                        $html .= '<option value="' . $nV . '"' . (in_array($nV, $value) ? ' selected="selected"' : '') . '>' . $nV . '</option>' . "\n";
                                    }
                                }
                            } else {
                                $html .= '<option value="' . $k . '"' . (in_array($k, $value) ? ' selected="selected"' : '') . '>' . (!preg_match('/&[^\s;]*;/', $v) ? fixHTMLUTF8Entities($v) : $v) . '</option>' . "\n";
                            }
                        }
                        $html .= '</select>' . "\n";
                    } elseif (MLSetting::gi()->get('blDebug')) {
                        $html.='<div class="noticeBox">' . $item['key'] . '</div>';
                    }

                    break;
                }
            case 'radio': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                    $c = 0;
                    $modSep = isset($item['separatormodulo']) && is_int($item['separatormodulo']) ? $item['separatormodulo'] : 1;
                    foreach ($item['values'] as $k => $v) {
                        $sep = '';
                        if (($c % $modSep) == ($modSep - 1)) {
                            $sep = isset($item['separator']) ? $item['separator'] : '';
                        }
                        $html .= '<span><input type="radio" value="' . $k . '" name="' .
                                MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . ']')
                                . '" id="conf_' . $item['key'] . '_' . $k . '"' .
                                (($value == $k) ? ' checked="checked"' : '') . $parameters .
                                ' /> <label for="conf_' . $item['key'] . '_' . $k . '"' . $class . '>' . $v . '</label></span>' . $sep . "\n";
                    }
                    break;
                }
            case 'checkbox': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                    $c = 0;
                    $modSep = isset($item['separatormodulo']) && is_int($item['separatormodulo']) ? $item['separatormodulo'] : 1;
                    //echo print_m($value);
                    foreach ($item['values'] as $k => $v) {
                        if (is_array($v)) {
                            $v = $this->renderSubInput($v, $k, $item);
                        } else {
                            $v = '<label for="conf_' . $item['key'] . '_' . $k . '"' . $class . '>' . $v . '</label>';
                        }
                        $sep = '';
                        if (($c % $modSep) == ($modSep - 1)) {
                            $sep = isset($item['separator']) ? $item['separator'] : '';
                        }
                        $html .= '<span>
						<input type="hidden" value="false" name="' .
                                MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . '][' . $k . ']')
                                . '" />
						<input type="checkbox" value="true" name="' .
                                MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . '][' . $k . ']')
                                . '" id="conf_' . $item['key'] . '_' . $k . '"' .
                                (is_array($value) && (array_key_exists($k, $value) && $value[$k]) ? ' checked="checked"' : '') . $parameters .
                                ' /> ' . $v . '</span>' . $sep . "\n";
                        ++$c;
                    }
                    break;
                }
            case 'textarea': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                        $item['cssClasses'][] = 'fullwidth';
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                    $html .= '<textarea' . $class . ' id="config_' . $idkey . '" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . ']')
                            . '"' . $parameters . '>' . str_replace('<', '&lt;', (string) $value) . '</textarea>';
                    break;
                }
            case 'dbfieldselector': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';
                    $html .= '<select id="config_' . $idkey . '_table" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . '][table]')
                            . '"' . $parameters . '' . $class . '>' . "\n";
                    $tables = MLDatabase::getDbInstance()->getAvailableTables();
                    if (!empty($tables)) {
                        $tables = array_flip($tables);
                        foreach ($tables as $tbl => &$tblname) {
                            $tblname = $tbl;
                        }
                    }
                    $item['values'] = array_merge(
                            array(
                        '' => MLI18n::gi()->ML_LABEL_DONT_USE,
                            ), $tables
                    );
                    $tblVal = (array_key_exists('table', (array) $value)) ? $value['table'] : false;
                    foreach ($item['values'] as $k => $v) {
                        $html .= '<option value="' . $k . '"' . (($tblVal == $k) ? ' selected="selected"' : '') . '>' . $v . '</option>' . "\n";
                    }
                    $html .= '</select>' . "\n";

                    $item['ajaxlinkto'] = array(
                        'fromid' => 'config_' . $idkey . '_table',
                        'toid' => 'config_' . $idkey . '_column',
                        'key' => $item['key'],
                        'func' => 'UpdateColumns_' . $idkey,
                        'initload' => ($tblVal === false),
                    );
                    $this->ajaxUpdateFuncs[] = $item['ajaxlinkto'];

                    $html .= '<select id="config_' . $idkey . '_column" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . '][column]')
                            . '"' . $parameters . '' . $class . '>' . "\n";
                    $html .= $this->renderDBCols(array(
                        'value' => $tblVal,
                        'key' => $item['key']
                    ));
                    $html .= '</select>' . "\n";
                    break;
                }
            case 'date': {
                    if (!isset($item['cssClasses'])) {
                        $item['cssClasses'] = array();
                    }
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $item['cssClasses'][] = 'missing';
                    }
                    $class = ' class="' . implode(' ', $item['cssClasses']) . '"';

                    $default = $value;
                    if (!empty($default)) {
                        $default = strtotime($default);
                        if ($default > 0) {
                            $default = date('Y/m/d', $default);
                        } else {
                            $default = '';
                        }
                    }
                    if (empty($default)) {
                        $default = date('Y/m/d');
                    } else {
                        $default = date('Y/m/d', strtotime($default));
                    }

                    $langCode = MLI18n::gi()->getLang();
                    if (empty($langCode)) {
                        $langCode = $_SESSION['language_code'] = MLDatabase::getDbInstance()->fetchOne('
						SELECT code FROM ' . TABLE_LANGUAGES . '
						 WHERE languages_id=\'' . $_SESSION['languages_id'] . '\'
					');
                    }
                    $deleteButton = '';

                    $html .= '
					<input type="text" id="config_' . $idkey . '_visual" value="" readonly="readonly" ' . $class . '/>
					<input type="hidden" id="config_' . $idkey . '" name="' .
                            MLHttp::gi()->parseFormFieldName('conf[' . $item['key'] . ']')
                            . '" value="' . $default . '"/>
					' . $deleteButton . '
					<script type="text/javascript">/*<![CDATA[*/
						(function($) {$(document).ready(function() {
							jqml.datepicker.setDefaults(jqml.datepicker.regional[\'\']);
							$("#config_' . $idkey . '_visual").datepicker(
								jqml.datepicker.regional[\'' . $langCode . '\']
							).datepicker(
								"option", "altField", "#config_' . $idkey . '"
							).datepicker(
								"option", "altFormat", "yy-mm-dd"
							)' . (!empty($default) ? '.datepicker(
								"option", "defaultDate", new Date(\'' . $default . '\')
							)' : '') . ';
							var dateFormat' . $idkey . ' = $("#config_' . $idkey . '_visual").datepicker("option", "dateFormat");
							' . (!empty($default) ? '
							$("#config_' . $idkey . '_visual").val(
								jqml.datepicker.formatDate(dateFormat' . $idkey . ', new Date(\'' . $default . '\'))
							);
							$("#config_' . $idkey . '").val(
								jqml.datepicker.formatDate("yy-mm-dd", new Date(\'' . $default . '\'))
							);' : '') . '
						})})(jqml);
					/*]]>*/</script>' . "\n";
                    break;
                }
            case 'extern': {
                    if (!is_callable($item['procFunc'])) {
                        if (is_array($item['procFunc'])) {
                            $item['procFunc'] = get_class($item['procFunc'][0]) . '->' . $item['procFunc'][1];
                        }
                        $html .= 'Function <span class="tt">\'' . $item['procFunc'] . '\'</span> does not exists.';
                        break;
                    }
                    $html .= call_user_func($item['procFunc'], array_merge($item['params'], array('key' => $item['key'])));
                    break;
                }
            case 'html': {
                    $html .= $item['value'];
                    break;
                }
        }
        return $html;
    }

    private function renderLabel($label, $idkey) {
        if ((strpos($label, 'const') !== false) && preg_match('/^const\((.*)\)$/', $label, $match)) {
            $label = constant($match[1]);
        }
        return '<label for="config_' . $idkey . '">' . $label . '</label>';
    }

    private function renderButton($button, $idkey) {
        switch ($button) {
            case '#restoreDefault#': {
                    $this->renderResetJS = true;
                    return '<input class="mlbtn" type="button" onclick="resetDefaults(\'config_' . $idkey . '\')" value="' . MLI18n::gi()->ML_BUTTON_RESTORE_DEFAULTS . '" />';
                    break;
                }
            default: {
                    if (is_array($button)) {
                        return sprintf($button['html'], MLHttp::gi()->parseFormFieldName($button['name']));
                    } else {
                        return $button;
                    }
                }
        }
    }

    public function renderConfigForm() {
        $html = '';

        if (array_key_exists('conf', $this->_POST) && is_array($this->_POST['conf']) &&
                array_key_exists('configtool', $this->_POST) && ($this->_POST['configtool'] == 'MagnaConfigurator') /* Nur um gaaaanz sicher zu gehen :D */
        ) {
            if (empty($this->notCorrect)) {
                $html .= '<p class="successBox">' . MLI18n::gi()->ML_TEXT_CONFIG_SAVED_SUCCESSFULLY . '</p>';
            } else {
                $html .= '<p class="noticeBox">' . MLI18n::gi()->ML_TEXT_CONFIG_SAVED_SEMI_SUCCESSFULLY . '</p>';
            }
        }
        $which = array();
        if (!empty($this->requiredConfigKeys) && !allRequiredConfigKeysAvailable($this->requiredConfigKeys, $this->mpID, false, $this->missingConfigKeys)
        ) {
            $html .= '<div class="successBoxBlue">' . MLI18n::gi()->ML_TEXT_FILLOUT_CONFIG_FORM . (
                    isset($this->_GET['showMissingKeys']) ? ('<ul><li>' . implode('</li><li>', $this->missingConfigKeys) . '</li></ul>') : '') .
                    '</div>';
        }
        $html .= '<form id="' . ($this->id ? $this->id : 'config') . '" class="config" method="POST" action="' . MLHttp::gi()->getCurrentUrl() . '">' . "\n";
        $descCount = 0;
        $html .= '
			<table class="conftbl' . ($this->renderTabIdent ? ' tabident' : '') . '">
				<tbody>';
        if ($this->renderTabIdent) {
            $tabLabel = getDBConfigValue(array('general.tabident', $this->mpID), '0', '');
            $tabLabel = fixHTMLUTF8Entities($tabLabel);
            $html .= '
				<tr class="conf"><th class="label">
					<label for="config_tabident">' . MLI18n::gi()->ML_LABEL_TAB_IDENT . '</label></th>
					<th class="desc">
						<div class="desc" id="desc_' . ($descCount++) . '" title="' . MLI18n::gi()->ML_LABEL_INFOS . '"><span>' . MLI18n::gi()->ML_TEXT_TAB_IDENT . '</span></div>
					</th>
					<td class="input" colspan="3">
						<input type="text" id="config_tabident" name="' . MLHttp::gi()->parseFormFieldName('tabident') . '" value="' .
                    str_replace(
                            array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $tabLabel
                    )
                    . '"/>
					</td>
				</tr>
			';
        }
        if (!empty($this->topHTML)) {
            $html .= '
				<tr class="text"><td colspan="5">
					' . $this->topHTML . '
				</td></tr>';
        }

        $hiddenFields = '';
        foreach ($this->form as $section) {
            if (isset($section['headline']) && !empty($section['headline'])) {
                $html .= '
					<tr class="text"><td colspan="5">
						<h3>' . $section['headline'] . '</h3>
					</td></tr>';
            }
            if (isset($section['desc']) && !empty($section['desc'])) {
                $class = 'text' . ((isset($section['headline']) && !empty($section['headline'])) ? '' : ' noheadline');
                $html .= '
					<tr class="' . $class . '"><td colspan="5">
						<p>' . $section['desc'] . '</p>
					</td></tr>';
            }

            foreach ($section['fields'] as $item) {
                if (empty($item) || !is_array($item))
                    continue;

                $isExpert = array_key_exists('expertsetting', $item) && $item['expertsetting'];

                if ($isExpert && (!array_key_exists('expert', $this->_GET) || ($this->_GET['expert'] != 'true'))
                ) {
                    continue;
                }
                if (!isset($item['key'])) {
                    echo'key is not set :D<pre>' . print_r($section, true) . '</pre>';
                    die;
                }
                $idkey = str_replace('.', '_', $item['key']);
                $input = $this->renderInput($item);
                if ($item['type'] != 'hidden') {
                    if (isset($item['rightlabel'])) {
                        $input .= $this->renderLabel($item['rightlabel'], $idkey);
                    }
                    if (isset($item['morefields'])) {
                        $sTemplate = (isset($item['morefields']['template']))?$item['morefields']['template']:'' ;
                        foreach ($item['morefields'] as $sKey => $mfItem) {
                            $mfidkey = str_replace('.', '_', $mfItem['key']);
                            if (isset($mfItem['label'])) {
                                $sTemplate = str_replace(":label$sKey", $this->renderLabel($mfItem['label'], $mfidkey), $sTemplate);
                                $input .= '&nbsp;' . $this->renderLabel($mfItem['label'], $mfidkey);
                            }
                            if (isset($mfItem['desc'])) {
                                $input .= '&nbsp;<div class="desc" id="desc_' . ($descCount++) . '" title="' . MLI18n::gi()->ML_LABEL_INFOS . '"><span>' . $mfItem['desc'] . '</span></div>';
                            }
                            if (isset($mfItem['label'])) {
                                $input .= ':&nbsp;';
                            }
                            $sTemplate = str_replace(":input$sKey", $this->renderInput($mfItem), $sTemplate);
                            $input .= $this->renderInput($mfItem);

                            if (isset($mfItem['rightlabel'])) {
                                $input .= $this->renderLabel($mfItem['rightlabel'], $mfidkey);
                            }
                            if ($mfItem['type'] != 'hidden') {
                                if (array_key_exists($mfItem['key'], $this->notCorrect)) {
                                    $this->notCorrect[$item['key']] .= ' ' . $this->notCorrect[$mfItem['key']];
                                }
                            }
                            if($sTemplate){
                                $input = $sTemplate;
                            }
                        }
                    }

                    $labelClasses = $isExpert ? 'expert ' : '';
                    if (in_array($item['key'], $this->missingConfigKeys)) {
                        $labelClasses .= 'missing ';
                    }
                    $html .= '
						<tr class="conf">
							' . (!empty($item['label']) ? (
                                    '<th class="label">' .
                                    '<label for="config_' . $idkey . '" class="' . $labelClasses . '">' . $item['label'] . '</label>' .
                                    '</th>'
                                    ) : ''
                            );
                    $cfgRow = '
							<th class="desc">';
                    if (isset($item['desc'])) {
                        $cfgRow .= '<div class="desc" id="desc_' . ($descCount++) . '" title="' . MLI18n::gi()->ML_LABEL_INFOS . '"><span>' . $item['desc'] . '</span></div>';
                    } else {
                        $cfgRow .= '&nbsp;';
                    }
                    $colspan = 3;
                    if (isset($item['hint'])) {
                        $colspan = 2;
                    }
                    if (array_key_exists($item['key'], $this->notCorrect)) {
                        $colspan = 1;
                    }
                    if ($item['type'] == 'textarea' && (!isset($item['cssClasses']) || !in_array('inline', $item['cssClasses']))) {
                        if (isset($item['buttons']) && !empty($item['buttons']) && is_array($item['buttons'])) {
                            $content = '';
                            foreach ($item['buttons'] as $button) {
                                $content .= $this->renderButton($button, $idkey);
                            }
                        } else {
                            $content = '&nbsp;';
                        }
                        $cfgRow .= '</th>
								<th class="space textright" colspan="' . $colspan . '">' . $content . '</th>';
                    } else {
                        $cfgRow .= '</th>
								<td class="input" ' . (isset($item['inputCellStyle']) ? 'style="' . $item['inputCellStyle'] . '" ' : '') . 'colspan="' . $colspan . '">' .
                                $input .
                                '</td>';
                    }
                    $html .=!empty($item['label']) ?
                            $cfgRow :
                            '<td class="subtable" colspan="' . (2 + $colspan) . '"><table><tbody><tr>' . $cfgRow . '</tr></tbody></table></td>';
                    if (array_key_exists($item['key'], $this->notCorrect)) {
                        $html .= '
							<td class="error"' . (!isset($item['hint']) ? ' colspan="2"' : '') . '>' . $this->notCorrect[$item['key']] . '</td>';
                    }
                    if (isset($item['hint'])) {
                        $html .= '
							<td class="hint">' . $item['hint'] . '</td>';
                    }
                    $html .= '
						</tr>';
                    if ($item['type'] == 'textarea' && (!isset($item['cssClasses']) || !in_array('inline', $item['cssClasses']))) {
                         $html.= '<tr'.(isset($item['cssClasses']) && in_array('tinymce', $item['cssClasses']) ? ' class="ml-js-noBlockUi"' : '').'>' ;
                        if (isset($item['externalDesc']) && !empty($item['externalDesc'])) {
                            $html .= '
								<td colspan="5" class="subconf"><table class="subtable"><tbody><tr>
									<td class="noborder editor">' . $input .'</td>
									<td class="noborder externalDesc">' . $item['externalDesc'] . '</td>
								</tr></tbody></table></td>
                            ';
                        } else {
                            $html .= '
								<td colspan="5" class="editor">' .$item. '</td>
							';
                        }
                        $html .= '</tr>';
                    }
                } else {
                    $hiddenFields .= $input . "\n";
                }
            }
        }
        /* Eine Leere Zeile mit allen Spalten um mein Freund den IE gluecklich zu machen -.-' */
        $html .= '
        </tbody>
				</table>
				' . $hiddenFields . '
				<table class="actions">
					<tbody>
						<tr><td>
							<table><tbody><tr>
								<td class="firstChild">
									<a href="' . MLHttp::gi()->getCurrentUrl(array('expert' => 'true') ) . '" title="' . MLI18n::gi()->ML_BUTTON_LABEL_EXPERTVIEW . '" class="mlbtn">' . MLI18n::gi()->ML_BUTTON_LABEL_EXPERTVIEW .'</a>
								</td>
								<td class="lastChild">';
        foreach (MLHttp::gi()->getNeededFormFields() as $sKey => $sValue) {
            $html.='<input type="hidden" name="' . $sKey . '" value="' . $sValue . '" />';
        }
        $html.='<input type="hidden" value="MagnaConfigurator" name="' . MLHttp::gi()->parseFormFieldName('configtool') . '"/>
									<input class="mlbtn" type="reset" value="' . MLI18n::gi()->ML_BUTTON_LABEL_RESET . '"/>
									<input class="mlbtn action" type="submit" value="' . MLI18n::gi()->ML_BUTTON_LABEL_SAVE_DATA . '"/>
								</td>
							</tr></tbody></table>
						</td></tr>
					</tbody>
				</table>
			</form>
			<div id="infodiag" class="dialog2" title="' . MLI18n::gi()->ML_LABEL_INFORMATION . '"></div>';
        if (('tinyMCE' == getDBConfigValue('general.editor', 0, 'tinyMCE')) && (strpos($html, 'tinymce') !== false)) {
            $langCode = MLI18n::gi()->getLang(); //MLDatabase::getDbInstance()->fetchOne('
            //SELECT code FROM '.TABLE_LANGUAGES.' WHERE languages_id=\''.$_SESSION['languages_id'].'\' LIMIT 1
            //');
            if (!empty($langCode) && file_exists(MLFilesystem::getLibPath() . 'Resources/Js/tiny_mce/langs/' . $langCode . '.js')) {
                $langCode = 'language: "' . $langCode . '",';
            } else {
                $langCode = '';
            }
            echo '
			<script type="text/javascript" src="' . MLHttp::gi()->getResourceUrl('js/tiny_mce/tiny_mce.js') . '"></script>';
            ob_start();
            ?>
            <script type="text/javascript">/*<![CDATA[*/
            <?php echo getTinyMCEDefaultConfigObject(); ?>
                (function($) {
                    $(document).ready(function() {
                        tinyMCE.init(tinyMCEMagnaDefaultConfig);
                    })
                })(jqml);
                /*]]>*/</script><?php
            $html .= ob_get_contents();
            ob_end_clean();
        }
        $html .= '
	        <script type="text/javascript">/*<![CDATA[*/';
        $html.='(function($) {';
        if ($descCount > 0) {
            $html .= '
				$(document).ready(function() {';
            for (; $descCount > 0; --$descCount) {
                $html .= '
					$(\'#desc_' . ($descCount - 1) . '\').click(function () {
						var d = $(\'#desc_' . ($descCount - 1) . ' span\').html();
						$(\'#infodiag\').html(d).jDialog({\'width\': (d.length > 1000) ? \'700px\' : \'500px\'});
					});';
            }
            $html .= '
				});';
        }

        /* $html .= '
          $(document).ready(function() {
          $(\'form#'.($this->id ? $this->id : 'config').' input[type="password"]\').focus(function() {
          $(\'label.savedPassword\', $(this).parent()).addClass(\'partial\');
          }).blur(function() {
          $(\'label.savedPassword\', $(this).parent()).removeClass(\'partial\');
          }).keyup(function() {
          if ($(this).val() != \'\') {
          $(\'label.savedPassword\', $(this).parent()).addClass(\'hidden\');
          } else {
          $(\'label.savedPassword\', $(this).parent()).removeClass(\'hidden\');
          }
          });
          });
         */
        /* Disable autocompleted passwords though browser. Can\'t use autocomplete="off" for the entire form. */
        $html.='$(window).load(function() {
					$(\'form#' . ($this->id ? $this->id : 'config') . ' input[type="password"]\').each(function() {
						if (jqml.trim($(this).val()) != \'\') {
							// remove from browser autocompleted field
						 	$(this).val(\'\');
						}
					});
				});';
        if (!empty($this->ajaxUpdateFuncs)) {
            $funcCall = '';
            foreach ($this->ajaxUpdateFuncs as $ajx) {
                $sFnc = str_replace(':', '_', $ajx['func']);
                $funcCall .= '
					$(\'#' . $ajx['fromid'] . '\').change(function() {
						' . $sFnc . '();
					});
					' . ((!array_key_exists('initload', $ajx) || $ajx['initload']) ? $sFnc . '();' : ''
                        );
                $sNeeded = '';
                foreach (MLHttp::gi()->getNeededFormFields(true) as $sKey => $sValue) {
                    $sNeeded.="'" . $sKey . "':'" . $sValue . "',";
                }
                $oShop = MLShop::gi();
                $html .= '
				function ' . $sFnc . '() {
					jqml.blockUI(blockUILoading); 
					jqml.ajax({
						type: \'POST\',
						url: \'' . MLHttp::gi()->getCurrentUrl(array('kind' => 'ajax', 'ajax' => 'true')) . '\',
						data: {
							\'' . MLHttp::gi()->parseFormFieldName('action') . '\': \'update\',
							\'' . MLHttp::gi()->parseFormFieldName('function') . '\': \'' . $ajx['func'] . '\',
                                                        ' . $sNeeded . '
							\'' . MLHttp::gi()->parseFormFieldName('key') . '\': \'' . $ajx['key'] . '\',
							\'' . MLHttp::gi()->parseFormFieldName('value') . '\': $(\'#' . $ajx['fromid'] . '\').val()
						},
						success: function(data) {
							jqml.unblockUI();
							el = $(\'#' . $ajx['toid'] . '\');
							if (el.is(\'select\')) {
								el.html(data);
							} else {
								el.val(data);
							}
						},
						error: function (xhr, status, error) {
							jqml.unblockUI();
						},
						dataType: \'html\'
					});
				}';
            }
            $html .= '
				$(document).ready(function() {
					' . $funcCall . '
				});';
        }
        $html.='})(jqml);';
        if ($this->renderResetJS) {
            $sMlPrefix = MLSetting::gi()->get('sRequestPrefix');
            $aRequest = array_merge(MLHttp::gi()->getNeededFormFields(), array(
                $sMlPrefix => array('action' => 'getDefault'),
                MLHttp::gi()->parseFormFieldName('ajax')=>'true'
            ));
            $html .= "
				function resetDefaults(configKey) {
                                        var aRequest=" . json_encode($aRequest) . ";
                                        aRequest['$sMlPrefix']['key'] = configKey;
					confField = jqml('#'+configKey);
					myConsole.log(confField);
					if (confField.length > 0) {
						jqml.ajax({
							type: 'POST',
							url: '" . MLHttp::gi()->getCurrentUrl( array('kind' => 'ajax')) . "',
							data: aRequest,
							success: function(data) {
								confField.val(data);
								if (confField.hasClass('tinymce')) {
									tinyMCE.get(configKey).setContent(confField.val());
								}
							},
							dataType: 'html'
						});
					}
				}";
        }
        $html .= '				
			/*]]>*/</script>';
        return $html;
    }

}
