<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */

namespace PrestaShop\Module\AutoUpgrade\UpgradeTools\CoreUpgrader;

use Cache;
use PrestaShop\Module\AutoUpgrade\UpgradeContainer;
use PrestaShop\Module\AutoUpgrade\UpgradeException;
use PrestaShop\Module\AutoUpgrade\UpgradeTools\ThemeAdapter;
use PrestaShop\Module\AutoUpgrade\Log\LoggerInterface;

/**
 * Class used to modify the core of PrestaShop, on the files are copied on the filesystem.
 * It will run subtasks such as database upgrade, language upgrade etc.
 */
abstract class CoreUpgrader
{
    /**
     * @var UpgradeContainer
     */
    protected $container;

    /**
     * @var \Db
     */
    protected $db;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Version PrestaShop is upgraded to.
     *
     * @var string
     */
    protected $destinationUpgradeVersion;

    /**
     * Path to the temporary install folder, where upgrade files can be found
     *
     * @var string
     */
    protected $pathToInstallFolder;

    /**
     * Path to the folder containing PHP upgrade files
     *
     * @var string
     */
    protected $pathToUpgradeScripts;

    public function __construct(UpgradeContainer $container, LoggerInterface $logger)
    {
        $this->container = $container;
        $this->logger = $logger;
    }

    public function doUpgrade()
    {
        $this->initConstants();

        $oldversion = $this->getPreUpgradeVersion();
        $this->checkVersionIsNewer($oldversion);

        //check DB access
        error_reporting(E_ALL);
        $resultDB = \Db::checkConnection(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_);
        if ($resultDB !== 0) {
            throw new UpgradeException($this->container->getTranslator()->trans('Invalid database configuration', array(), 'Modules.Autoupgrade.Admin'));
        }

        if ($this->container->getUpgradeConfiguration()->shouldDeactivateCustomModules()) {
            $this->disableCustomModules();
        }

        $this->upgradeDb($oldversion);

        // At this point, database upgrade is over.
        // Now we need to add all previous missing settings items, and reset cache and compile directories
        $this->writeNewSettings();
        $this->runRecurrentQueries();
        $this->logger->debug($this->container->getTranslator()->trans('Database upgrade OK', array(), 'Modules.Autoupgrade.Admin')); // no error!

        $this->upgradeLanguages();
        $this->generateHtaccess();
        $this->cleanXmlFiles();

        if ($this->container->getUpgradeConfiguration()->shouldDeactivateCustomModules()) {
            $this->disableOverrides();
        }

        $this->updateTheme();

        $this->runCoreCacheClean();

        if ($this->container->getState()->getWarningExists()) {
            $this->logger->warning($this->container->getTranslator()->trans('Warning detected during upgrade.', array(), 'Modules.Autoupgrade.Admin'));
        } else {
            $this->logger->info($this->container->getTranslator()->trans('Database upgrade completed', array(), 'Modules.Autoupgrade.Admin'));
        }
    }

    protected function initConstants()
    {
        // Initialize
        // setting the memory limit to 128M only if current is lower
        $memory_limit = ini_get('memory_limit');
        if ((substr($memory_limit, -1) != 'G')
            && ((substr($memory_limit, -1) == 'M' && substr($memory_limit, 0, -1) < 512)
                || is_numeric($memory_limit) && ((int) $memory_limit < 131072))
        ) {
            @ini_set('memory_limit', '512M');
        }

        // Redefine REQUEST_URI if empty (on some webservers...)
        if (!isset($_SERVER['REQUEST_URI']) || empty($_SERVER['REQUEST_URI'])) {
            if (!isset($_SERVER['SCRIPT_NAME']) && isset($_SERVER['SCRIPT_FILENAME'])) {
                $_SERVER['SCRIPT_NAME'] = $_SERVER['SCRIPT_FILENAME'];
            }
            if (isset($_SERVER['SCRIPT_NAME'])) {
                if (basename($_SERVER['SCRIPT_NAME']) == 'index.php' && empty($_SERVER['QUERY_STRING'])) {
                    $_SERVER['REQUEST_URI'] = dirname($_SERVER['SCRIPT_NAME']) . '/';
                } else {
                    $_SERVER['REQUEST_URI'] = $_SERVER['SCRIPT_NAME'];
                    if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING'])) {
                        $_SERVER['REQUEST_URI'] .= '?' . $_SERVER['QUERY_STRING'];
                    }
                }
            }
        }
        $_SERVER['REQUEST_URI'] = str_replace('//', '/', $_SERVER['REQUEST_URI']);

        $this->destinationUpgradeVersion = $this->container->getState()->getInstallVersion();
        $this->pathToInstallFolder = realpath($this->container->getProperty(UpgradeContainer::LATEST_PATH) . DIRECTORY_SEPARATOR . 'install');
        // Kept for backward compatbility (unknown consequences on old versions of PrestaShop)
        define('INSTALL_VERSION', $this->destinationUpgradeVersion);
        // 1.4
        define('INSTALL_PATH', $this->pathToInstallFolder);
        // 1.5 ...
        if (!defined('_PS_CORE_DIR_')) {
            define('_PS_CORE_DIR_', _PS_ROOT_DIR_);
        }

        define('PS_INSTALLATION_IN_PROGRESS', true);
        define('SETTINGS_FILE_PHP', $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/app/config/parameters.php');
        define('SETTINGS_FILE_YML', $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/app/config/parameters.yml');
        define('DEFINES_FILE', $this->container->getProperty(UpgradeContainer::PS_ROOT_PATH) . '/config/defines.inc.php');
        define('INSTALLER__PS_BASE_URI', substr($_SERVER['REQUEST_URI'], 0, -1 * (strlen($_SERVER['REQUEST_URI']) - strrpos($_SERVER['REQUEST_URI'], '/')) - strlen(substr(dirname($_SERVER['REQUEST_URI']), strrpos(dirname($_SERVER['REQUEST_URI']), '/') + 1))));
        //	define('INSTALLER__PS_BASE_URI_ABSOLUTE', 'http://'.ToolsInstall::getHttpHost(false, true).INSTALLER__PS_BASE_URI);

        define('_PS_INSTALL_PATH_', $this->pathToInstallFolder . '/');
        define('_PS_INSTALL_DATA_PATH_', _PS_INSTALL_PATH_ . 'data/');
        define('_PS_INSTALL_CONTROLLERS_PATH_', _PS_INSTALL_PATH_ . 'controllers/');
        define('_PS_INSTALL_MODELS_PATH_', _PS_INSTALL_PATH_ . 'models/');
        define('_PS_INSTALL_LANGS_PATH_', _PS_INSTALL_PATH_ . 'langs/');
        define('_PS_INSTALL_FIXTURES_PATH_', _PS_INSTALL_PATH_ . 'fixtures/');

        if (function_exists('date_default_timezone_set')) {
            date_default_timezone_set('Europe/Paris');
        }

        // if _PS_ROOT_DIR_ is defined, use it instead of "guessing" the module dir.
        if (defined('_PS_ROOT_DIR_') && !defined('_PS_MODULE_DIR_')) {
            define('_PS_MODULE_DIR_', _PS_ROOT_DIR_ . '/modules/');
        } elseif (!defined('_PS_MODULE_DIR_')) {
            define('_PS_MODULE_DIR_', $this->pathToInstallFolder . '/../modules/');
        }

        $this->pathToUpgradeScripts = _PS_MODULE_DIR_ . 'autoupgrade/upgrade/';
        define('_PS_INSTALLER_PHP_UPGRADE_DIR_', $this->pathToUpgradeScripts . 'php/');

        if (!defined('__PS_BASE_URI__')) {
            define('__PS_BASE_URI__', realpath(dirname($_SERVER['SCRIPT_NAME'])) . '/../../');
        }

        if (!defined('_THEMES_DIR_')) {
            define('_THEMES_DIR_', __PS_BASE_URI__ . 'themes/');
        }

        if (file_exists($this->pathToInstallFolder . DIRECTORY_SEPARATOR . 'autoload.php')) {
            require_once $this->pathToInstallFolder . DIRECTORY_SEPARATOR . 'autoload.php';
        }
        $this->db = \Db::getInstance();
    }

    protected function getPreUpgradeVersion()
    {
        return $this->normalizeVersion(\Configuration::get('PS_VERSION_DB'));
    }

    /**
     * Add missing levels in version.
     * Example: 1.7 will become 1.7.0.0.
     *
     * @param string $version
     *
     * @return string
     *
     * @internal public for tests
     */
    public function normalizeVersion($version)
    {
        $arrayVersion = explode('.', $version);
        if (count($arrayVersion) < 4) {
            $arrayVersion = array_pad($arrayVersion, 4, '0');
        }

        return implode('.', $arrayVersion);
    }

    protected function checkVersionIsNewer($oldVersion)
    {
        if (strpos($this->destinationUpgradeVersion, '.') === false) {
            throw new UpgradeException($this->container->getTranslator()->trans('%s is not a valid version number.', array($this->destinationUpgradeVersion), 'Modules.Autoupgrade.Admin'));
        }

        $versionCompare = version_compare($this->destinationUpgradeVersion, $oldVersion);

        if ($versionCompare === -1) {
            throw new UpgradeException(
                $this->container->getTranslator()->trans('[ERROR] Version to install is too old.', array(), 'Modules.Autoupgrade.Admin')
                . ' ' .
                $this->container->getTranslator()->trans(
                'Current version: %oldversion%. Version to install: %newversion%.',
                array(
                    '%oldversion%' => $oldVersion,
                    '%newversion%' => $this->destinationUpgradeVersion,
                ),
                'Modules.Autoupgrade.Admin'
            ));
        } elseif ($versionCompare === 0) {
            throw new UpgradeException($this->container->getTranslator()->trans('You already have the %s version.', array($this->destinationUpgradeVersion), 'Modules.Autoupgrade.Admin'));
        }
    }

    /**
     * Ask the core to disable the modules not coming from PrestaShop.
     */
    protected function disableCustomModules()
    {
        $this->container->getModuleAdapter()->disableNonNativeModules($this->pathToUpgradeScripts);
    }

    protected function upgradeDb($oldversion)
    {
        $upgrade_dir_sql = $this->pathToUpgradeScripts . '/sql/';
        $sqlContentVersion = $this->applySqlParams(
            $this->getUpgradeSqlFilesListToApply($upgrade_dir_sql, $oldversion)
        );

        foreach ($sqlContentVersion as $upgrade_file => $sqlContent) {
            foreach ($sqlContent as $query) {
                $this->runQuery($upgrade_file, $query);
            }
        }
    }

    protected function getUpgradeSqlFilesListToApply($upgrade_dir_sql, $oldversion)
    {
        if (!file_exists($upgrade_dir_sql)) {
            throw new UpgradeException($this->container->getTranslator()->trans('Unable to find upgrade directory in the installation path.', array(), 'Modules.Autoupgrade.Admin'));
        }

        $upgradeFiles = $neededUpgradeFiles = array();
        if ($handle = opendir($upgrade_dir_sql)) {
            while (false !== ($file = readdir($handle))) {
                if ($file[0] === '.') {
                    continue;
                }
                if (!is_readable($upgrade_dir_sql . $file)) {
                    throw new UpgradeException($this->container->getTranslator()->trans('Error while loading SQL upgrade file "%s".', array($file), 'Modules.Autoupgrade.Admin'));
                }
                $upgradeFiles[] = str_replace('.sql', '', $file);
            }
            closedir($handle);
        }
        if (empty($upgradeFiles)) {
            throw new UpgradeException($this->container->getTranslator()->trans('Cannot find the SQL upgrade files. Please check that the %s folder is not empty.', array($upgrade_dir_sql), 'Modules.Autoupgrade.Admin'));
        }
        natcasesort($upgradeFiles);

        foreach ($upgradeFiles as $version) {
            if (version_compare($version, $oldversion) == 1 && version_compare($this->destinationUpgradeVersion, $version) != -1) {
                $neededUpgradeFiles[$version] = $upgrade_dir_sql . $version . '.sql';
            }
        }

        return $neededUpgradeFiles;
    }

    /**
     * Replace some placeholders in the SQL upgrade files (prefix, engine...).
     *
     * @param array $sqlFiles
     *
     * @return array of SQL requests per version
     */
    protected function applySqlParams(array $sqlFiles)
    {
        $search = array('PREFIX_', 'ENGINE_TYPE', 'DB_NAME');
        $replace = array(_DB_PREFIX_, (defined('_MYSQL_ENGINE_') ? _MYSQL_ENGINE_ : 'MyISAM'), _DB_NAME_);

        $sqlRequests = array();

        foreach ($sqlFiles as $version => $file) {
            $sqlContent = file_get_contents($file) . "\n";
            $sqlContent = str_replace($search, $replace, $sqlContent);
            $sqlContent = preg_split("/;\s*[\r\n]+/", $sqlContent);
            $sqlRequests[$version] = $sqlContent;
        }

        return $sqlRequests;
    }

    /**
     * ToDo, check to move this in a database class.
     *
     * @param string $upgrade_file File in which the request is stored (for logs)
     * @param string $query
     */
    protected function runQuery($upgrade_file, $query)
    {
        $query = trim($query);
        if (empty($query)) {
            return;
        }
        // If php code have to be executed
        if (strpos($query, '/* PHP:') !== false) {
            return $this->runPhpQuery($upgrade_file, $query);
        }
        $this->runSqlQuery($upgrade_file, $query);
    }

    protected function runPhpQuery($upgrade_file, $query)
    {
        // Parsing php code
        $pos = strpos($query, '/* PHP:') + strlen('/* PHP:');
        $phpString = substr($query, $pos, strlen($query) - $pos - strlen(' */;'));
        $php = explode('::', $phpString);
        preg_match('/\((.*)\)/', $phpString, $pattern);
        $paramsString = trim($pattern[0], '()');
        preg_match_all('/([^,]+),? ?/', $paramsString, $parameters);
        $parameters = (isset($parameters[1]) && is_array($parameters[1])) ?
            $parameters[1] :
            array();
        foreach ($parameters as &$parameter) {
            $parameter = str_replace('\'', '', $parameter);
        }

        // reset phpRes to a null value
        $phpRes = null;
        // Call a simple function
        if (strpos($phpString, '::') === false) {
            $func_name = str_replace($pattern[0], '', $php[0]);
            $pathToPhpDirectory = $this->pathToUpgradeScripts . 'php/';

            if (!file_exists($pathToPhpDirectory . strtolower($func_name) . '.php')) {
                $this->logger->error('[ERROR] ' . $pathToPhpDirectory . strtolower($func_name) . ' PHP - missing file ' . $query);
                $this->container->getState()->setWarningExists(true);

                return;
            }

            require_once $pathToPhpDirectory . strtolower($func_name) . '.php';
            $phpRes = call_user_func_array($func_name, $parameters);
        }
        // Or an object method
        else {
            $func_name = array($php[0], str_replace($pattern[0], '', $php[1]));
            $this->logger->error('[ERROR] ' . $upgrade_file . ' PHP - Object Method call is forbidden (' . $php[0] . '::' . str_replace($pattern[0], '', $php[1]) . ')');
            $this->container->getState()->setWarningExists(true);

            return;
        }

        if (isset($phpRes) && (is_array($phpRes) && !empty($phpRes['error'])) || $phpRes === false) {
            $this->logger->error('
                [ERROR] PHP ' . $upgrade_file . ' ' . $query . "\n" . '
                ' . (empty($phpRes['error']) ? '' : $phpRes['error'] . "\n") . '
                ' . (empty($phpRes['msg']) ? '' : ' - ' . $phpRes['msg'] . "\n"));
            $this->container->getState()->setWarningExists(true);
        } else {
            $this->logger->debug('<div class="upgradeDbOk">[OK] PHP ' . $upgrade_file . ' : ' . $query . '</div>');
        }
    }

    protected function runSqlQuery($upgrade_file, $query)
    {
        if (strstr($query, 'CREATE TABLE') !== false) {
            $pattern = '/CREATE TABLE.*[`]*' . _DB_PREFIX_ . '([^`]*)[`]*\s\(/';
            preg_match($pattern, $query, $matches);
            if (!empty($matches[1])) {
                $drop = 'DROP TABLE IF EXISTS `' . _DB_PREFIX_ . $matches[1] . '`;';
                if ($this->db->execute($drop, false)) {
                    $this->logger->debug('<div class="upgradeDbOk">' . $this->container->getTranslator()->trans('[DROP] SQL %s table has been dropped.', array('`' . _DB_PREFIX_ . $matches[1] . '`'), 'Modules.Autoupgrade.Admin') . '</div>');
                }
            }
        }

        if ($this->db->execute($query, false)) {
            $this->logger->debug('<div class="upgradeDbOk">[OK] SQL ' . $upgrade_file . ' ' . $query . '</div>');

            return;
        }

        $error = $this->db->getMsgError();
        $error_number = $this->db->getNumberError();
        $this->logger->warning('
            <div class="upgradeDbError">
            [WARNING] SQL ' . $upgrade_file . '
            ' . $error_number . ' in ' . $query . ': ' . $error . '</div>');

        $duplicates = array('1050', '1054', '1060', '1061', '1062', '1091');
        if (!in_array($error_number, $duplicates)) {
            $this->logger->error('SQL ' . $upgrade_file . ' ' . $error_number . ' in ' . $query . ': ' . $error);
            $this->container->getState()->setWarningExists(true);
        }
    }

    public function writeNewSettings()
    {
        // Do nothing
    }

    protected function runRecurrentQueries()
    {
        $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `name` = \'PS_LEGACY_IMAGES\' WHERE name LIKE \'0\' AND `value` = 1');
        $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = 0 WHERE `name` LIKE \'PS_LEGACY_IMAGES\'');
        if ($this->db->getValue('SELECT COUNT(id_product_download) FROM `' . _DB_PREFIX_ . 'product_download` WHERE `active` = 1') > 0) {
            $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET `value` = 1 WHERE `name` LIKE \'PS_VIRTUAL_PROD_FEATURE_ACTIVE\'');
        }

        // Exported from the end of doUpgrade()
        $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET value="0" WHERE name = "PS_HIDE_OPTIMIZATION_TIS"', false);
        $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET value="1" WHERE name = "PS_NEED_REBUILD_INDEX"', false);
        $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET value="' . $this->destinationUpgradeVersion . '" WHERE name = "PS_VERSION_DB"', false);
    }

    protected function upgradeLanguages()
    {
        if (!defined('_PS_TOOL_DIR_')) {
            define('_PS_TOOL_DIR_', _PS_ROOT_DIR_ . '/tools/');
        }
        if (!defined('_PS_TRANSLATIONS_DIR_')) {
            define('_PS_TRANSLATIONS_DIR_', _PS_ROOT_DIR_ . '/translations/');
        }
        if (!defined('_PS_MODULES_DIR_')) {
            define('_PS_MODULES_DIR_', _PS_ROOT_DIR_ . '/modules/');
        }
        if (!defined('_PS_MAILS_DIR_')) {
            define('_PS_MAILS_DIR_', _PS_ROOT_DIR_ . '/mails/');
        }

        $langs = $this->db->executeS('SELECT * FROM `' . _DB_PREFIX_ . 'lang` WHERE `active` = 1');

        if (!is_array($langs)) {
            return;
        }
        foreach ($langs as $lang) {
            $this->upgradeLanguage($lang);
        }
    }

    abstract protected function upgradeLanguage($lang);

    protected function generateHtaccess()
    {
        $this->loadEntityInterface();

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Tools.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Tools.php';
        }

        if (!class_exists('ToolsCore') || !method_exists('ToolsCore', 'generateHtaccess')) {
            return;
        }
        $url_rewrite = (bool) $this->db->getvalue('SELECT `value` FROM `' . _DB_PREFIX_ . 'configuration` WHERE name=\'PS_REWRITING_SETTINGS\'');

        if (!defined('_MEDIA_SERVER_1_')) {
            define('_MEDIA_SERVER_1_', '');
        }

        if (!defined('_PS_USE_SQL_SLAVE_')) {
            define('_PS_USE_SQL_SLAVE_', false);
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/ObjectModel.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/ObjectModel.php';
        }
        if (!class_exists('ObjectModel', false) && class_exists('ObjectModelCore')) {
            eval('abstract class ObjectModel extends ObjectModelCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Configuration.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Configuration.php';
        }
        if (!class_exists('Configuration', false) && class_exists('ConfigurationCore')) {
            eval('class Configuration extends ConfigurationCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/cache/Cache.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/cache/Cache.php';
        }
        if (!class_exists('Cache', false) && class_exists('CacheCore')) {
            eval('abstract class Cache extends CacheCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/PrestaShopCollection.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/PrestaShopCollection.php';
        }
        if (!class_exists('PrestaShopCollection', false) && class_exists('PrestaShopCollectionCore')) {
            eval('class PrestaShopCollection extends PrestaShopCollectionCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/shop/ShopUrl.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/shop/ShopUrl.php';
        }
        if (!class_exists('ShopUrl', false) && class_exists('ShopUrlCore')) {
            eval('class ShopUrl extends ShopUrlCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/shop/Shop.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/shop/Shop.php';
        }
        if (!class_exists('Shop', false) && class_exists('ShopCore')) {
            eval('class Shop extends ShopCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Translate.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Translate.php';
        }
        if (!class_exists('Translate', false) && class_exists('TranslateCore')) {
            eval('class Translate extends TranslateCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/module/Module.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/module/Module.php';
        }
        if (!class_exists('Module', false) && class_exists('ModuleCore')) {
            eval('class Module extends ModuleCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Validate.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Validate.php';
        }
        if (!class_exists('Validate', false) && class_exists('ValidateCore')) {
            eval('class Validate extends ValidateCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Language.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Language.php';
        }
        if (!class_exists('Language', false) && class_exists('LanguageCore')) {
            eval('class Language extends LanguageCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Tab.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Tab.php';
        }
        if (!class_exists('Tab', false) && class_exists('TabCore')) {
            eval('class Tab extends TabCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Dispatcher.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Dispatcher.php';
        }
        if (!class_exists('Dispatcher', false) && class_exists('DispatcherCore')) {
            eval('class Dispatcher extends DispatcherCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Hook.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Hook.php';
        }
        if (!class_exists('Hook', false) && class_exists('HookCore')) {
            eval('class Hook extends HookCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Context.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Context.php';
        }
        if (!class_exists('Context', false) && class_exists('ContextCore')) {
            eval('class Context extends ContextCore{}');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/Group.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/Group.php';
        }
        if (!class_exists('Group', false) && class_exists('GroupCore')) {
            eval('class Group extends GroupCore{}');
        }

        \ToolsCore::generateHtaccess(null, $url_rewrite);
    }

    protected function loadEntityInterface()
    {
        require_once _PS_ROOT_DIR_ . '/src/Core/Foundation/Database/EntityInterface.php';
    }

    protected function cleanXmlFiles()
    {
        $files = array(
            $this->container->getProperty(UpgradeContainer::PS_ADMIN_PATH) . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR . 'default' . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . 'header.tpl',
            _PS_ROOT_DIR_ . '/app/cache/dev/class_index.php',
            _PS_ROOT_DIR_ . '/app/cache/prod/class_index.php',
            _PS_ROOT_DIR_ . '/cache/class_index.php',
            _PS_ROOT_DIR_ . '/config/xml/blog-fr.xml',
            _PS_ROOT_DIR_ . '/config/xml/default_country_modules_list.xml',
            _PS_ROOT_DIR_ . '/config/xml/modules_list.xml',
            _PS_ROOT_DIR_ . '/config/xml/modules_native_addons.xml',
            _PS_ROOT_DIR_ . '/config/xml/must_have_modules_list.xml',
            _PS_ROOT_DIR_ . '/config/xml/tab_modules_list.xml',
            _PS_ROOT_DIR_ . '/config/xml/trusted_modules_list.xml',
            _PS_ROOT_DIR_ . '/config/xml/untrusted_modules_list.xml',
            _PS_ROOT_DIR_ . '/var/cache/dev/class_index.php',
            _PS_ROOT_DIR_ . '/var/cache/prod/class_index.php',
        );
        foreach ($files as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    protected function disableOverrides()
    {
        $exist = $this->db->getValue('SELECT `id_configuration` FROM `' . _DB_PREFIX_ . 'configuration` WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
        if ($exist) {
            $this->db->execute('UPDATE `' . _DB_PREFIX_ . 'configuration` SET value = 1 WHERE `name` LIKE \'PS_DISABLE_OVERRIDES\'');
        } else {
            $this->db->execute('INSERT INTO `' . _DB_PREFIX_ . 'configuration` (name, value, date_add, date_upd) VALUES ("PS_DISABLE_OVERRIDES", 1, NOW(), NOW())');
        }

        if (file_exists(_PS_ROOT_DIR_ . '/classes/PrestaShopAutoload.php')) {
            require_once _PS_ROOT_DIR_ . '/classes/PrestaShopAutoload.php';
        }

        if (class_exists('PrestaShopAutoload') && method_exists('PrestaShopAutoload', 'generateIndex')) {
            \PrestaShopAutoload::getInstance()->_include_override_path = false;
            \PrestaShopAutoload::getInstance()->generateIndex();
        }
    }

    protected function updateTheme()
    {
        // The merchant can ask for keeping its current theme.
        if (!$this->container->getUpgradeConfiguration()->shouldSwitchToDefaultTheme()) {
            return;
        }
        $this->logger->info($this->container->getTranslator()->trans('Switching to default theme.', array(), 'Modules.Autoupgrade.Admin'));
        $themeAdapter = new ThemeAdapter($this->db, $this->destinationUpgradeVersion);

        Cache::clean('*');

        $themeErrors = $themeAdapter->enableTheme(
            $themeAdapter->getDefaultTheme()
        );

        if ($themeErrors !== true) {
            throw new UpgradeException($themeErrors);
        }
    }

    protected function runCoreCacheClean()
    {
        \Tools::clearCache();
    }
}
