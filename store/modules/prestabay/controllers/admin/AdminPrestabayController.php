<?php

class AdminPrestabayController extends ModuleAdminController
{
    protected $_module = 'prestabay';
    protected $_defaultController = "selling";
    protected $_defaultAction = "index";

    protected $myCone = "";

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';
        $this->meta_title = $this->l('PrestaBay eBay Integration');
        parent::__construct();
        if (!$this->module->active) {
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        }
    }

    public function renderView()
    {
        $this->myCone = $this->prestabayProccessRequest();
        return $this->myCone;
    }

    public function initContent()
    {
        session_start();
        return parent::initContent();
    }

    protected function prestabayProccessRequest()
    {
        if (defined('_PRESTABAY_AUTOLOADER_LOADED_') && _PRESTABAY_AUTOLOADER_LOADED_) {
        } else {
            $path = _PS_MODULE_DIR_ . 'prestabay/';
            include($path . 'library/Autoloader.php');
            Autoloader::init($path);
        }

        // $debugHelper = new DebugHelper();
        // $debugHelper->initErrorHandler();

        return $this->displayContent();
    }

     protected function displayContent()
    {
        // @todo uncomment this line if some problem with saving value into DB
         Db::getInstance()->execute("SET sql_mode = ''", false);
        // session_save_path(_PS_MODULE_DIR_ . $this->_module . '/var/tmp');
        // example listing_templates/view = /controller/Listings/TemplatesController.php/view
        $dispatchPath = Tools::getValue('request');

        $arguments = explode("/", $dispatchPath);

        $controllerFileName = isset($arguments[0]) ? $arguments[0] : "";
        if ($controllerFileName == "") {
            // When no controller. Do license key check
            LicenseHelper::verifyLicenseKey();

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

        $controllerOutput = "";
        if (RenderHelper::isAjax() || RenderHelper::isSetClean()) {
            $this->ajax = true;
            @ob_end_clean();
        }
        ob_start();

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

        if (RenderHelper::isSetClean() || RenderHelper::isAjax()) {
            // Remove top level cache and display output from controller
            $this->ajax = true;
            $controllerOutput = ob_get_contents();
            @ob_end_clean();
            @ob_end_clean();
            header('Content-Type: text/html; charset=utf-8');
            return $controllerOutput;
        }

        $licenseKey = Configuration::get("INVEBAY_LICENSE_KEY");
        if ($licenseKey == false || $licenseKey == null || $licenseKey == "") {
            RenderHelper::addError(L::t("License key not specified. Please setup key on 'Configuration Information' section."));
        } else {
            $isExpired = Configuration::get("INVEBAY_LICENSE_EXPIRED");
            if ($isExpired) {
                RenderHelper::addError(L::t("License key is EXPIRED. Please renew it on 'Configuration Information' section."));
            }
        }


        $controllerOutput = ob_get_contents();
        @ob_end_clean();

        ob_start();
        RenderHelper::addCss("style16.css");
        RenderHelper::addScript("main.js");

        RenderHelper::generateHeader();
        RenderHelper::view("main/menu.phtml");

        $headOutput = ob_get_contents();
        @ob_end_clean();

        if ($hasError) {
            $controllerOutput =  "<h2>Error</h2>"."<p>{$errorMessage}</p>".$controllerOutput;
        }
        return $headOutput.$controllerOutput;
    }

}
