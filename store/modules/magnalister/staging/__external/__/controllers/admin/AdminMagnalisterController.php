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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class AdminMagnalisterController extends AdminController
{

    private $s_rendered_html = '';

    public function checkAccess()
    {
        return true;
    }

    public function viewAccess()
    {
        return true;
    }

    public function initContent()
    {
        if (Tools::getValue('action') == 'resizeImage') {
            //just resize image and create new image in img/magnalister/product
            $this->resizeImage();
            die();
        }
        parent::initContent();
        define('_LANG_ISO_', $this->context->language->iso_code);
        define('_LANG_ID_', $this->context->language->id);
        require_once(dirname(__FILE__).'/../../lib/Core/ML.php');
        $admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_));
        define('_PS_ADMIN_PATH_', __PS_BASE_URI__.$admin_webpath.'/');
        if ($this->s_rendered_html == '') {
            $this->s_rendered_html = ML::gi()->run();
        }
        /* @var $s_client_version string will be added to url as parameter to avoid browser-cache */
        $s_client_version = MLSetting::gi()->get('sClientBuild');
        $bl_absolute = (int)MLShop::gi()->getShopVersion() >= 6;
        foreach (MLSetting::gi()->get('aCss') as $s_file) {
            $this->addCSS(sprintf(MLHttp::gi()->getResourceUrl('css_'.$s_file, $bl_absolute), $s_client_version), 'all');
        }
        foreach (MLSetting::gi()->get('aJs') as $s_file) {
            $this->addJs(sprintf(MLHttp::gi()->getResourceUrl('js_'.$s_file, $bl_absolute), $s_client_version));
        }
        if (MLRequest::gi()->data('ajax')) {
            exit();
        }
        $script_add_body_class = "<script type='text/javascript'> $(document).ready(function(){ $('body')";
        foreach (MLSetting::gi()->get('aBodyClasses') as $s_class) {
            $script_add_body_class .= ".addClass('$s_class')";
        }
        $script_add_body_class .= '});</script>';
        $this->context->smarty->assign('magnalister', $this->s_rendered_html.$script_add_body_class);
        $this->setTemplate('magnalister.tpl');
    }

    public function createTemplate($tpl_name)
    {
        $s_filename = dirname(__FILE__).DS.'..'.DS.'..'.DS.'views'.DS.'templates'.DS.'admin'.DS.$tpl_name;
        return $this->context->smarty->createTemplate($s_filename, $this->context->smarty);
    }

    protected function resizeImage()
    {
        $a_image = Tools::getValue('ml');
        ImageManager::resize($a_image['src'], $a_image['dst'], $a_image['x'], $a_image['y']);
    }
}
