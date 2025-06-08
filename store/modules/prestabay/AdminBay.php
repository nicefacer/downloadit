<?php
/**
 * File AdminBay.php
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
if (defined('PS_ADMIN_DIR')) {
    if (!class_exists('AdminTab')) {
        include_once(PS_ADMIN_DIR . '/../classes/AdminTab.php');
    }
}


class AdminBay extends AdminTab
{

    public $multishop_context_group = true;
    public $multishop_context = null;

    protected $_module = 'prestabay';
    protected $_defaultController = "selling";
    protected $_defaultAction = "index";

    public function __construct()
    {
        parent::__construct();

        session_start();

        if (defined('_PRESTABAY_AUTOLOADER_LOADED_') && _PRESTABAY_AUTOLOADER_LOADED_) {
            return;
        }

        $path = _PS_MODULE_DIR_ . $this->_module . '/';
        include($path . 'library/Autoloader.php');
        Autoloader::init($path);

        // $debugHelper = new DebugHelper();
        // $debugHelper->initErrorHandler();
    }

    /**
     * Add a new stylesheet in page header.
     *
     * @param mixed  $css_uri Path to css file, or list of css files like this : array(array(uri => media_type), ...)
     * @param string $css_media_type
     * @param null   $offset
     *
     * @return true
     */
    public function addCSS($css_uri, $css_media_type = 'all', $offset = null)
    {
        // back compatibility to PS 15 for modules that not support old mode
        return true;
    }


    /**
     * Add a new javascript file in page header.
     *
     * @param mixed $js_uri
     * @param bool $check_path
     * @return void
     */
    public function addJS($js_uri, $check_path = true)
    {
        // back compatibility to PS 15 for modules that not support old mode
    }

    public function display()
    {
        if (CoreHelper::isPS16()) {
            // Change tab reference to PS16 version
            DB::getInstance()->update("tab",array(
                    'class_name' => "AdminPrestabay"
                ), 'class_name="AdminBay"');
            @ob_end_clean();
            $url = UrlHelper::getPrestaUrl("AdminPrestabay");
            header('Location: ' . $url);
            exit;
        }

        // @todo uncomment this line if some problem with saving value into DB
        // Db::getInstance()->execute("SET sql_mode = ''", false);
        // session_save_path(_PS_MODULE_DIR_ . $this->_module . '/var/tmp');
        // example listing_templates/view = /controller/Listings/TemplatesController.php/view
        $dispatchPath = Tools::getValue('request');

        $arguments = explode("/", $dispatchPath);

        $controllerFileName = isset($arguments[0]) ? $arguments[0] : "";
        if ($controllerFileName == "") {
            $controllerFileName = $this->_defaultController;
        }

        $delim = "_";
        if (strpos($controllerFileName, $delim) !== false) {
            $controllerFileName = implode($delim, array_map('ucfirst', explode($delim, $controllerFileName)));
        } else {
            $controllerFileName = ucfirst($controllerFileName);
        }
        $controllerFileName == "Order" && $controllerFileName = "EbayOrder";
        
        $controllerFileName.="Controller";

        if (!isset($arguments[1])) {
            $arguments[1] = $this->_defaultAction;
        }

        $arguments[1] .= "Action";

        if (count($arguments) > 2) {
            for ($i = 2; $i < count($arguments); $i+=2) {
                $_GET[$arguments[$i]] = isset($arguments[$i + 1]) ? $arguments[$i + 1] : true;
            }
        }

	    ini_set('display_errors', 1);
        error_reporting(E_ERROR | E_WARNING | E_PARSE);
//        error_reporting(E_ALL);
        umask(0);

        if (CoreHelper::isOnlyPS15()) {
            RenderHelper::addCss("style15.css");
        } else {
            RenderHelper::addCss("style.css");
        }
        
        RenderHelper::addCss("bootstrap.min.css");
        RenderHelper::addScript("main.js");

        $hasError = false;
        $errorMessage = "";

        // General error
        if (!class_exists($controllerFileName)) {
            $hasError = true;
            $errorMessage = L::t("Controller not found");
        } else {
            $classInstance = new $controllerFileName();
        }

        if (!$hasError && !method_exists($classInstance, $arguments[1])) {
            $hasError = true;
            $errorMessage = L::t("Action not found");
        }

        $controllerOutput = "";
        if (RenderHelper::isAjax()) {
            @ob_end_clean();
        }
        ob_start();
        try {
            if (!$hasError) {
                $classInstance = new $controllerFileName();
                 call_user_func(array($classInstance, $arguments[1]));
            }
        } catch (Exception $ex) {
            echo "<h2>Exception</h2>";
            echo "<strong>" . $ex->getMessage() . "</strong>";
            echo "<p>Details:</p>";
            echo "<pre>";
            print_r($ex->getTraceAsString());
            echo "</pre>";
        }

        $controllerOutput = ob_get_contents();
        @ob_end_clean();

        if (RenderHelper::isSetClean() || RenderHelper::isAjax()) {
            // Remove top level cache and display output from controller
            @ob_end_clean();
            header('Content-Type: text/html; charset=utf-8');
            echo $controllerOutput;
            exit;
        }
        $licenseKey = Configuration::get("INVEBAY_LICENSE_KEY");
        if ($licenseKey == false || $licenseKey == null || $licenseKey == "") {
            RenderHelper::addError(L::t("License key not specified. Please setup key on 'Configuration Information' section."));
        }

        RenderHelper::generateHeader();
        RenderHelper::view("main/menu.phtml");

        if ($hasError) {
            echo "<h2>Error</h2>";
            echo "<p>{$errorMessage}</p>";
        }
        echo $controllerOutput;
    }

}