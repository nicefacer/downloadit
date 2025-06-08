<?php
/**
 * File ConfigController.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

class ConfigController extends BaseAdminController
{

    public function indexAction()
    {
        $hasLicense = false;
        $licenseValue = Configuration::get("INVEBAY_LICENSE_KEY");

        // Update domain information - required for cron job
        Configuration::updateValue("INVEBAY_SHOP_DOMAIN", (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']));

        if (strlen($licenseValue) > 0) {
            $hasLicense = true;
        }
        if ($hasLicense) {
            $hasLicense = LicenseHelper::verifyLicenseKey();
            if (!$hasLicense) {
                Configuration::updateValue("INVEBAY_LICENSE_KEY", false);
                RenderHelper::addError("There was error happens during try to check your license. Please try again latter.");
            }
        }

        $memLimit = ini_get('memory_limit');
        $maxExecution = (int) ini_get('max_execution_time');

        $versionFile = $this->_getModuleVersion();
        $versionDb = Configuration::get('INVEBAY_VERSION_DATA');
        $needUpgrade = version_compare($versionFile, $versionDb) > 0;


        $phpVersion = phpversion();
        $isSupportedPhpVersion = version_compare($phpVersion, '5.4.0', '>=');

        $moduleConfig = array(
            'Installed Version:' => $versionFile,
            'DB Version:' => $versionDb ? $versionDb : "N/A",
            'Upgrade Required:' => ($needUpgrade) ? '<font color="red">Yes</font>' : 'No',
//            'Latest Stable Version:' => '<font color="red">1.0.2</font>',
            '&nbsp;' => '',
            'PHP Version:' => $isSupportedPhpVersion ? $phpVersion : '<font color="red">'.$phpVersion.'  !!! should be >= 5.4</font>',
            'Memory Limit:' => $memLimit,
            'Max Execution Time:' => ($maxExecution > 0 && $maxExecution < 300)?'<font color="red">'.$maxExecution.'  !!!</font>':$maxExecution,
            'Write Permission:' => $this->_checkWritePermission(),
            'Magic Quites GPC:' => get_magic_quotes_gpc() ? '<font color="red">Yes !!!</font>': 'No'
        );

        $this->view("config/main.phtml", array(
            'hasLicense' => $hasLicense,
            'licenseKey' => $licenseValue,
            'licenseInfo' => json_decode(Configuration::get('INVEBAY_LICENSE_INFO'), true),
            'moduleConfig' => $moduleConfig,
            'showUpgradeButton' => $needUpgrade,
        ));
    }

    public function variablesAction()
    {
        $variablesList = array(
            'INVEBAY_VERSION_DATA',
            'INVEBAY_SYNC_SUCCESS_TIME',
            'INVEBAY_SYNC_ORDER_SUCCESS_TIME',
            'INVEBAY_ORDER_TAX',
            'INVEBAY_ORDER_SHIPPING_TAX',
            'INVEBAY_SYNC_FEEDBACK_AUTO_ID',
            'INVEBAY_ORDER_NEW_IMPORT',
            'INVEBAY_NEW_SELLING_DEACTIVATE',
            'INVEBAY_SYNC_FULL_REVISE',
            'INVEBAY_EXTENDED_ORDER_LOG',
            'INVEBAY_ORDER_FAKE_EMAIL',

        );

        $updateValues = UrlHelper::getPost("config", null);
        if ($updateValues) {
            foreach ($updateValues as $key => $value) {
                if (in_array($key, $variablesList)) {
                    Configuration::updateValue($key, trim($value));
                }
            }
            RenderHelper::addSuccess(L::t("Configuration variables saved"));
            UrlHelper::redirect("config/index");
            return;
        }
        $this->view("config/variables.phtml", array(
            'variablesList' => $variablesList
        ));
    }


    public function upgradeAction()
    {
        $action = "upgrade";
        $fromVersion = Configuration::get("INVEBAY_VERSION_DATA");
        $toVersion = $this->_getModuleVersion();

        if ($fromVersion == "" || $fromVersion == null || $fromVersion == false) {
            $action = "install";
            $fromVersion = null;
        }

        $installer = new Installer();
        $applyDataVersion = false;
        try {
            $applyDataVersion = $installer->applyAction($action, $fromVersion, $toVersion);
        } catch (Exception $ex) {
            RenderHelper::addError($ex->getMessage());
            $applyDataVersion = false;
        }

        if (!$applyDataVersion || !Configuration::updateValue('INVEBAY_VERSION_DATA', $applyDataVersion)) {
            RenderHelper::addError(L::t("Can't upgrade DB Version"));
        } else {
            RenderHelper::addSuccess(L::t("DB Successfull upgraded to version").": " . $applyDataVersion);
        }

        UrlHelper::redirect("config/index");
        return;
    }

    public function clearCacheAction()
    {
        $files = glob(_PS_MODULE_DIR_ . 'prestabay/var/cache/*');
        $files = array_filter($files, 'is_file');
        array_map('unlink', $files);
        RenderHelper::addSuccess(L::t("Success Cleared"));
        UrlHelper::redirect("config/index");
    }

    public function recalculateTaxAction()
    {
        $orderMode = new Order_OrderModel();
        $orderMode->recalculateTaxForAllImportedOrders();
    }

    public function updateProductMappingAction()
    {
        $sql    = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_selling_products WHERE ebay_id > 0";
        $listings = Db::getInstance()->ExecuteS($sql, true, false);
        $totalListings = count($listings);
        echo "Found {$totalListings} listings prepared for re-mapping<br/>";
        foreach ($listings as $listingRow) {
            $sqlUpdate = "UPDATE " . _DB_PREFIX_ . "prestabay_product_connection SET
                     `presta_id` = ".$listingRow['product_id'].",
                     `presta_attribute_id` = ".$listingRow['product_id_attribute']."
                     WHERE `ebay_id` = ".$listingRow['ebay_id'];

            try {
                Db::getInstance()->execute($sqlUpdate, false);
                echo " - Remapping for Product  #{$listingRow['product_id']}<br/>";
            } catch (Exception $ex) {
                echo " - FAILED! Product #{$listingRow['product_id']}. Reason {$ex->getMessage()}<br/>";
            }

        }
        echo "Remapping finished<br/>";
    }

    protected function _getModuleVersion()
    {
        $xml = simplexml_load_file(_PS_MODULE_DIR_ . "/prestabay/config.xml");
        return (string) $xml->version;
    }

    protected function _checkWritePermission()
    {
        $result = "";
        $folders = array('var/cache', 'var/tmp');
        foreach ($folders as $folder) {
            $fullFolderPath = _PS_MODULE_DIR_ . "prestabay/" . $folder . "/";
            if (!is_dir($fullFolderPath) || !is_writable($fullFolderPath)) {
                $result.="<font color='red'>".sprintf(L::t("Path '%s' - Not Writable"),$fullFolderPath)."</font><br/>";
            }
        }
        return $result == "" ? L::t("OK") : $result;
    }

}