<?php
$aRequest = MLRequest::gi()->data();
// xtc simulation
$_SESSION['language'] = MLLanguage::gi()->getCurrentIsoCode();
$_SESSION['language_charset'] = MLLanguage::gi()->getCurrentCharset();
$aDb = MLShop::gi()->getDbConnection();
if (file_exists($aDb['host'])) {//socket connection
    $aDb['host'] = ':' . $aDb['host'];
}

// php5 behavior
defined('E_RECOVERABLE_ERROR') OR define('E_RECOVERABLE_ERROR', 0x1000);
defined('E_DEPRECATED') OR define('E_DEPRECATED', 0x2000);
defined('E_USER_DEPRECATED') OR define('E_USER_DEPRECATED', 0x4000);

//magnalister.php
global $_js, $_url, $_MagnaSession, $_MagnaShopSession, $_magnaQuery, $_modules, $magnaConfig;
$_modules = MLSetting::gi()->get('aModules');
$magnaConfig = array();

define('MAGNALISTER_PLUGIN', true);

define('MAGNA_CALLBACK_MODE', 'UTILITY');

define('MAGNA_PUBLIC_SERVER', MLSetting::gi()->get('sPublicUrl'));
define('MAGNA_PLUGIN_DIR', MLFilesystem::getLibPath());
define('DIR_MAGNALISTER_ABSOLUTE', dirname(__FILE__) . '/');
define('DIR_MAGNALISTER', 'includes/' . MAGNA_PLUGIN_DIR);

if (MLSetting::gi()->get('blDebug') && (MLSetting::gi()->get('blShowWarnings') || MLSetting::gi()->get('blShowFatal'))) {
    ini_set("display_errors", 1);
    register_shutdown_function('magnaHandleFatalError');
    if (version_compare(PHP_VERSION, '5.2.0', '<')) {
        ini_set('track_errors', 1);
    }
}
if (MLSetting::gi()->get('blShowWarnings')) {
    error_reporting(E_ALL | E_STRICT);
}

//init.php
define('MAGNA_WITHOUT_AUTH', 0x00000004);
//The timestamp of the start of the request. Available since PHP 5.1.0. 
$_SERVER['REQUEST_TIME'] = isset($_SERVER['REQUEST_TIME']) ? $_SERVER['REQUEST_TIME'] : time();
date_default_timezone_set(@date_default_timezone_get());
global $_executionTime;
$_executionTime = microtime(true);
/**
 * Defines
 */
define('DIR_MAGNALISTER_INCLUDES', MLFilesystem::getOldLibPath('php/'));
define('DIR_MAGNALISTER_MODULES', MLFilesystem::getOldLibPath('php/modules/'));
define('DIR_MAGNALISTER_CALLBACK', MLFilesystem::getOldLibPath('php/callback/'));
define('DIR_MAGNALISTER_RESOURCE', MLFilesystem::getOldLibPath('resource/'));
define('DIR_MAGNALISTER_IMAGES', MLFilesystem::getOldLibPath('images/'));
define('DIR_MAGNALISTER_CONTRIBS', MLFilesystem::getOldLibPath('contribs/'));
define('DIR_MAGNALISTER_LOGS', MLFilesystem::getOldLibPath('logs/'));

if (MLSetting::gi()->get('blDebug')) {
    define('MAGNA_DEBUG_TF', false);
}

//define('ML_DEFAULT_RAM', '256M');

if (MLSetting::gi()->get('blDebug') && isset($aRequest['MagnaRAW'])) {
    $_SESSION['MagnaRAW'] = $aRequest['MagnaRAW'];
}

define('LOCAL_CLIENT_VERSION', MLSetting::gi()->get('sClientVersion'));
define('CLIENT_BUILD_VERSION', MLSetting::gi()->get('sClientBuild'));

define('CURRENT_CLIENT_VERSION', MLSetting::gi()->get('sCurrentVersion'));
define('MINIMUM_CLIENT_VERSION', MLSetting::gi()->get('sMinClientVersion'));
define('CURRENT_BUILD_VERSION', MLSetting::gi()->get('sCurrentVersion'));
/**
 * Global includes and initialisation
 */
define('SHOPSYSTEM', MLShop::gi()->getShopSystemName());
require_once(DIR_MAGNALISTER_INCLUDES . 'lib/json_wrapper.php');
//loading models
MLDatabase::factory('config');

/* Title of page */
$_mainTitle = '';

$_url = array();

/* RAM Check. Wenn RAM Begrenzung zu klein ist, wird diese erhoeht. 
 * Idr wird nur bei ImageResize Operationen mehr RAM benoetigt, falls 
 * die Produktbider zu gross sind. */
magnaFixRamSize();

/* Kein Error-Handling da DB Fehler immer Fatal */
//echo print_m($_dbUpdateErrors, 'updateDatabase');

require_once(DIR_MAGNALISTER_INCLUDES . 'config.php');
loadDBConfig();    /* Load configuration from database */

require_once(DIR_MAGNALISTER_INCLUDES . 'lib/MagnaException.php');
require_once(DIR_MAGNALISTER_INCLUDES . 'lib/MagnaError.php');
require_once(DIR_MAGNALISTER_INCLUDES . 'lib/MagnaConnector.php');
$_mConnect = MagnaConnector::gi();

$requiredConfigKeys = array(
    'general.passphrase',
    'general.keytype',
    'general.stats.backwards',
    'general.callback.importorders',
);

/* Is magic_quotes on? */
if (get_magic_quotes_gpc()) {
    /* Strip the added slashes */
    $aRequest = arrayMap('stripslashes', $aRequest);
    $_COOKIE = arrayMap('stripslashes', $_COOKIE);
}
/**
 * Gobal verfuegbare Variablen:
 */
$_js = array();
$_magnaQuery = array();

$forceConfigView = false;
# SKU == products_model: Fehlermeldung wenn nicht ueberall gefuellt und unique
if (getDBConfigValue('general.keytype', '0', 'pID') == 'artNr') {
    if (count(MLShop::gi()->getProductsWithWrongSku()) > 0) {
        $forceConfigView = '<p class="errorBox">' . str_replace(
                        '#LINK#', toURL(array('module' => 'configuration', 'fixProductsModel' => 'true')), MLI18n::gi()->get('ML_GENERIC_ERROR_PRODUCTS_WITHOUT_MODEL_EXIST')
                ) . '</p>';
    }
}
/* If the PassPhrase is not set in the database show the global config */
if (!allRequiredConfigKeysAvailable($requiredConfigKeys, '0') || ($forceConfigView !== false)) {
    /* Send the user to the configuration panel */
    $_url['module'] = $aRequest['mp'] = $_magnaQuery['module'] = 'configuration';
    MLRequest::gi()->set('mp', 'configuration', true);
    $_MagnaSession['currentPlatform'] = '';
} else {

    /* Don't try to authenticate if the PassPhrase is going to be set */
    if (!isset($aRequest['conf']['general.passphrase']) && !loadMaranonCacheConfig() && (!isset($aRequest['mp']) || ($aRequest['mp'] != 'configuration'))
    ) {
        return;
    }

    /* No modules are available (usually the case when the PassPhrase is wrong) or global config is requested.
      Let's go to the global config page */
    if (!isset($magnaConfig['maranon']['Marketplaces']) || empty($magnaConfig['maranon']['Marketplaces'])) {
        $aRequest['mp'] = 'configuration';
    }
    if (isset($aRequest['mp']) && array_key_exists($aRequest['mp'], $_modules) && ($_modules[$aRequest['mp']]['type'] == 'system') && file_exists(DIR_MAGNALISTER_MODULES . $aRequest['mp'] . '.php')
    ) {
        /* Send the user to the configuration panel */
        $_url['module'] = $_magnaQuery['module'] = $aRequest['mp'];
        $_MagnaSession['currentPlatform'] = '';
        if (!in_array($aRequest['mp'], array(/* modules that works in mvc */'configuration', 'guide'))) {
            include_once(DIR_MAGNALISTER_MODULES . $aRequest['mp'] . '.php');
        }
    } else {  
        // apirequest automaticly try old requests with errors
        MLDatabase::factory('apirequest');

//        magnaFixOrders();
        
        if (array_key_exists('mp', $aRequest) && array_key_exists($aRequest['mp'], $magnaConfig['maranon']['Marketplaces']) && ($mp = $magnaConfig['maranon']['Marketplaces'][$aRequest['mp']]) && array_key_exists($mp, $_modules)
        ) {
            $_MagnaSession['mpID'] = $aRequest['mp'];
            $_MagnaSession['currentPlatform'] = $mp;

            $_magnaQuery['module'] = $_MagnaSession['currentPlatform'];
            $_url = array('mp' => $_MagnaSession['mpID']);
        } else {
            if (isset($aRequest['mp']) && array_key_exists($aRequest['mp'], $_modules)) {
                $_url['mp'] = $aRequest['mp'];
                if (!in_array($aRequest['mp'], array('more', 'statistics'))) {
                    $_mainTitle = ' - ' . MLI18n::gi()->get('ML_HEADLINE_NOT_YET_BOOKED');
                    shopAdminDiePage('
                        <h2>' . MLI18n::gi()->get('ML_HEADLINE_NOT_YET_BOOKED') . '</h2>
                        <p>' . sprintf(MLI18n::gi()->get('ML_TEXT_CURRENT_MODULE_NOT_BOOKED'), $_modules[$aRequest['mp']]['title']) . '</p>
                    ');
                }
            }
        } 
    }
}