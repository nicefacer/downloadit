<?php
/**
 * File DebugHelper.php
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

class DebugHelper
{

    public function initErrorHandler()
    {
        register_shutdown_function(array($this, 'shutdownHandler'));
        set_error_handler(array($this, 'errorHandler'));
    }

    public function errorHandler($errorLevel, $errorMessage, $errorFile, $errorLine, $errorContext)
    {
        $error = "lvl: " . $errorLevel . " | msg:" . $errorMessage . " | file:" . $errorFile . " | ln:" . $errorLine;
        $trace = "";
        try {
            throw new Exception();
        } catch (Exception $ex) {
            $trace = $ex->getTraceAsString();
        }
        $error .= "\nTrace:\n".$trace;

        switch ($errorLevel) {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_PARSE:
            case E_USER_ERROR:
                $this->logMe($error, "fatal");
                break;
            case E_RECOVERABLE_ERROR:
                $this->logMe($error, "error");
                break;
            case E_WARNING:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_USER_WARNING:
                $this->logMe($error, "warn");
                break;
            case E_NOTICE:
            case E_USER_NOTICE:
                $this->logMe($error, "info");
                break;
            case E_STRICT:
                $this->logMe($error, "debug");
                break;
            default:
                $this->logMe($error, "warn");
        }

        restore_error_handler();
        trigger_error($errorMessage, E_USER_WARNING);
    }

    public function shutdownHandler() //will be called when php script ends.
    {
        $lastError = error_get_last();

        if (!isset($lastError['type'])) {
            return;
        }

        switch ($lastError['type'])
        {
            case E_ERROR:
            case E_CORE_ERROR:
            case E_COMPILE_ERROR:
            case E_USER_ERROR:
            case E_RECOVERABLE_ERROR:
            case E_CORE_WARNING:
            case E_COMPILE_WARNING:
            case E_PARSE:
                $error = "[SHUTDOWN] lvl:" . $lastError['type'] . " | msg:" . $lastError['message'] . " | file:" . $lastError['file'] . " | ln:" . $lastError['line'];
                $this->logMe($error, "fatal");
        }
    }

    protected function logMe($error, $errorLevel)
    {
        $message = 'Error No: ' . $error . "\n".  'Error Level: ' .$errorLevel . "\n==========\n";
        self::addErrorLog($message);
    }
//    protected static $_timeSpots = array();

    public static function addErrorLog($message)
    {
        $profilerFile = _PS_MODULE_DIR_ . "prestabay/var/tmp/errors.txt";

        file_put_contents($profilerFile,  $message . "\n\r", FILE_APPEND);
    }

    public static function addDebug($message)
    {
        $profilerFile = _PS_MODULE_DIR_ . "prestabay/var/profiler/request-response.txt";

        file_put_contents($profilerFile, 'Message ' . $message . "\n\r", FILE_APPEND);
    }
    public static function addProfilerMessage($message)
    {
//        $profilerFile = _PS_MODULE_DIR_ . "prestabay/var/profiler/log.txt";
//
//        file_put_contents($profilerFile, 'Message ' . $message . "\n\r", FILE_APPEND);
    }

    public static function addProfilerTimeSpot($uid)
    {
//        self::$_timeSpots[$uid] = microtime();
    }

    public static function endProfilerTimeSpot($uid)
    {
//        $profilerFile = _PS_MODULE_DIR_ . "prestabay/var/profiler/log.txt";
//
//        $endTime = microtime();
//        $totalTime = 0;
//        if (isset(self::$_timeSpots[$uid])) {
//            $totalTime = $endTime - self::$_timeSpots[$uid];
//            unset(self::$_timeSpots[$uid]);
//            file_put_contents($profilerFile, 'Time ' . $uid . ": " . $totalTime . "\n\r", FILE_APPEND);
//        }

    }

}
