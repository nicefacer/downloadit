<?php

/**
 * File RenderHelper.php
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
class RenderHelper
{

    private static $_scriptsList = array();
    private static $_scriptsGlobalList = array();
    private static $_cssList = array();
    private static $_isClean = false;
    private static $_template_dir = "prestabay/views";

    public static function addGrobalScript($scriptPath)
    {
        $pos = strrpos($scriptPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $scriptPath;
        } else {
            $key = substr($scriptPath, $pos);
        }
        self::$_scriptsGlobalList[$key] = $scriptPath;
    }

    public static function addScript($scriptPath)
    {
        $pos = strrpos($scriptPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $scriptPath;
        } else {
            $key = substr($scriptPath, $pos);
        }
        self::$_scriptsList[$key] = $scriptPath;
    }

    public static function addCss($cssPath)
    {
        $pos = strrpos($cssPath, "/");
        $key = null;
        if ($pos === false) {
            $key = $cssPath;
        } else {
            $key = substr($cssPath, $pos);
        }
        self::$_cssList[$key] = $cssPath;
    }

    public static function addSuccess($message)
    {
        $messages = isset($_SESSION['messages_succeses']) ? $_SESSION['messages_succeses'] : array();
        $messages[] = $message;
        $_SESSION['messages_succeses'] = $messages;
    }

    public static function addWarning($message)
    {
        $messages = isset($_SESSION['messages_warnings']) ? $_SESSION['messages_warnings'] : array();
        $messages[] = $message;
        $_SESSION['messages_warnings'] = $messages;
    }

    public static function addError($message)
    {
        $messages = isset($_SESSION['messages_errors']) ? $_SESSION['messages_errors'] : array();
        $messages[] = $message;
        $_SESSION['messages_errors'] = $messages;
    }

    public static function generateHeader()
    {
        foreach (self::$_cssList as $singleCss) {
            echo '<link rel="stylesheet" type="text/css" media="screen" href="../modules/prestabay/css/' . $singleCss . '" />';
        }

        foreach (self::$_scriptsGlobalList as $singleScript) {
            echo '<script type="text/javascript" src="../js/' . $singleScript . '"></script>';
        }

        foreach (self::$_scriptsList as $singleScript) {
            echo '<script type="text/javascript" src="../modules/prestabay/js/' . $singleScript . '"></script>';
        }

        // Generate success/warning/error messages list
        self::_generateMessages('messages_succeses', 'conf', 'ok2.png');
        self::_generateMessages('messages_warnings', 'warn', 'warn2.png');
        self::_generateMessages('messages_errors', 'error', 'error2.png');
    }

    protected static function _generateMessages($identify, $className, $imageName)
    {
        $isPS15 = CoreHelper::isOnlyPS15();

        $messagesList = isset($_SESSION[$identify]) ? $_SESSION[$identify] : array();
        if (CoreHelper::isPS16()) {
            if ($identify == "messages_succeses") {
                foreach ($messagesList as $message) {
                    echo self::displayBootstrapConfirmation($message);
                }
            } elseif ($identify == "messages_warnings") {
                foreach ($messagesList as $message) {
                    echo self::displayBootstrapWarning($message);
                }
            } else {
                foreach ($messagesList as $message) {
                    echo self::displayBootstrapError($message);
                }
            }

            if (count($messagesList) > 0) {
                unset($_SESSION[$identify]);
            }

            return;
        }


        if (($sc = count($messagesList)) > 0) {
            echo '<div class="' . $className . '">';
        }

        $c = 0;
        foreach ($messagesList as $message) {
            $c++;
            if (!$isPS15) {
                echo '<img src="../modules/prestabay/img/' . $imageName . '" />';
            } else {
                echo "<ul><li>";
            }
            echo $message;
            if (!$isPS15) {
                if ($c < $sc) {
                    echo "<br/>";
                }
            } else {
                echo "</li></ul>";
            }
        }

        if ($sc > 0) {
            echo '</div>';
            unset($_SESSION[$identify]);
        }
    }


    public static function displayBootstrapError($error)
    {
        $output = '
	 	<div class="bootstrap">
		<div class="module_error alert alert-danger">
			'.$error.'
		</div>
		</div>';
        return $output;
    }

    public static function displayBootstrapConfirmation($string)
    {
        $output = '
	 	<div class="bootstrap">
		<div class="module_confirmation confirm alert alert-success">
			'.$string.'
		</div>
		</div>';
        return $output;
    }

    public static function displayBootstrapWarning($string)
    {
        $output = '<div class="alert alert-warning">
                <button type="button" class="close" data-dismiss="alert">×</button>
				<ul class="list-unstyled">
				    <li>'.$string.'</li>
			    </ul>
	    </div>';

        return $output;
    }

    public static function displayModuleNotification($type = 'info', $title, $viewId = null)
    {
        $textLine = $title . ' ' .
            ($viewId ? '<a href="' . UrlHelper::getUrl('notification/view', array('id' => $viewId)) . '">' . L::t('Read more') . '</a>' : '') ;
        if (CoreHelper::isPS16()) {
            $output = '<div id="module-notification" class="alert alert-' . $type . '" data-id='.$viewId.'>
                <button type="button" class="close">×</button>
                <ul class="list-unstyled">
				    <li>' . $textLine . '</li> </ul>
	    </div>';
        } else {
            $output = '<div class="' . $type . '"><ul><li>';
            $output .= $textLine;
            $output .= '</li></ul></div>';
        }

        return $output;
    }

    public static function cleanOutput()
    {
        self::$_isClean = true;

        self::$_scriptsList = array();
        self::$_cssList = array();
    }

    public static function setJSONHeader()
    {
        header('Content-Type: application/json');
    }

    public static function isSetClean()
    {
        return self::$_isClean;
    }

    public static function isAjax()
    {
        return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
    }
    
    public static function view($view, $vars = array(), $showOutput = true)
    {
        $output = self::_loadView(array('_view' => $view, '_vars' => $vars));
        if ($showOutput) {
            echo $output;
        } else {
            return $output;
        }
    }

    protected static function _loadView($_data)
    {
        $defaultExt = ".phtml";

        $templateDir = _PS_MODULE_DIR_ . self::$_template_dir;
        // Set the default data variables
        foreach (array('_view', '_vars', '_path') as $_val) {
            $$_val = (!isset($_data[$_val])) ? false : $_data[$_val];
        }

        // Set the path to the requested file
        $_ext = pathinfo($_view, PATHINFO_EXTENSION);
        $_file = ($_ext == '') ? $_view . $defaultExt : $_view;
        $_path = $templateDir . "/" . $_file;


        if (!file_exists($_path)) {
            echo L::t('Unable to load the requested file') . ': ' . $_file . "<br>";
        }
        extract($_vars);

        // Start buffer the output 
        ob_start();

        include($_path);

        // Return the file data
        // Buffered output to variable 
        $buffer = ob_get_contents();
        @ob_end_clean();
        return $buffer;
    }

}

