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
 * (c) 2010 - 2016 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class Magnalister extends Module
{

    public function __construct()
    {
        $this->name = 'magnalister';
        $this->module_key = '53ef6ee1f8f22f0985ab0176fe061529';
        $this->author = 'magnalister';
        $this->tab = 'administration';
        $this->ps_versions_compliancy = array('min' => '1.5.3', 'max' => '1.7');
        $this->version = '3.0.4';
        parent::__construct();
        $this->displayName = $this->l('magnalister');
        $this->description = $this->l('Connect your Store to Big Store (Amazon , eBay and ...)');
    }

    public function install()
    {
        $admin_webpath = preg_replace('/^'.preg_quote(DS, '/').'/', '', str_ireplace(_PS_ROOT_DIR_, '', _PS_ADMIN_DIR_));
        define('_PS_ADMIN_PATH_', __PS_BASE_URI__.$admin_webpath.'/');
        if (($id_tab = Tab::getIdFromClassName('AdminMainMagnalister')) != null) {
            $tab = new Tab((int)$id_tab);
            if (!$tab->delete()) {
                $this->_errors[] = sprintf($this->l('Unable to delete AdminMainMagnalister tab'), (int)$id_tab);
            }
        }

        if (($id_tab = Tab::getIdFromClassName('AdminMagnalister')) != null) {
            $tab = new Tab((int)$id_tab);
            if (!$tab->delete()) {
                $this->_errors[] = sprintf($this->l('Unable to delete AdminMagnalister tab'), (int)$id_tab);
            }
        }
        /* If the "magnalister" tab does not exist yet, create it */
        if (!$id_tab = Tab::getIdFromClassName('Magnalister')) {
            $tab = new Tab();
            $tab->class_name = 'AdminMainMagnalister';
            $tab->module = 'magnalister';
            $tab->id_parent = 0;
            foreach (Language::getLanguages(false) as $lang) {
                $tab->name[(int)$lang['id_lang']] = 'magnalister';
            }
            $subtab = new Tab();
            $subtab->class_name = 'AdminMagnalister';
            $subtab->module = 'magnalister';
            foreach (Language::getLanguages(false) as $lang) {
                $subtab->name[(int)$lang['id_lang']] = 'magnalister Admin';
            }
            if (!$tab->add()) {
                return $this->abortMagnalisterInstall($this->l('Unable to create the "AdminMainMagnalister" tab'));
            } else {
                $subtab->id_parent = (int)Tab::getIdFromClassName('AdminMainMagnalister');
                if (!$subtab->add()) {
                    return $this->abortMagnalisterInstall($this->l('Unable to create the "AdminMagnalister" tab'));
                }
            }
        } else {
            $tab = new Tab((int)$id_tab);
        }
        /* Update the "AdminMagnalister" tab id in database or exit */
        if (Validate::isLoadedObject($tab)) {
            Configuration::updateValue('PS_MAGNALISTER_MODULE_IDTAB', (int)$tab->id);
        } else {
            return $this->abortMagnalisterInstall($this->l('Unable to load the "AdminMagnalister" tab'));
        }
        if (parent::install() && $this->registerHook('displayAdminOrder')
                && $this->registerHook('DisplayBackOfficeHeader')) {
            return true;
        }
    }

    public function uninstall()
    {
        if (($id_tab = Tab::getIdFromClassName('AdminMainMagnalister')) != null) {
            $tab = new Tab((int)$id_tab);
            $tab->delete();
        }
        if (($id_tab = Tab::getIdFromClassName('AdminMagnalister')) != null) {
            $tab = new Tab((int)$id_tab);
            $tab->delete();
        }
        if (parent::uninstall() && $this->registerHook('displayAdminOrder') &&
                $this->unregisterHook('DisplayBackOfficeHeader') && $this->unregisterHook('actionOrderStatusUpdate')) {
            return true;
        }
    }

    public function getContent()
    {
        $id_employee = (int)Context::getContext()->cookie->id_employee;
        $form_class = (int)Tab::getIdFromClassName('AdminMagnalister');
        $md5 = md5(pSQL(_COOKIE_KEY_.'AdminMagnalister'.$form_class.$id_employee));
        Tools::redirectAdmin('index.php?tab=AdminMagnalister&token='.$md5);
        exit;
    }

    /**
     * Set installation errors and return false
     *
     * @param string $s_error Installation abortion reason
     * @return boolean Always false
     */
    protected function abortMagnalisterInstall($s_error)
    {
        if (version_compare(_PS_VERSION_, '1.5.3.0 ', '>=')) {
            $this->_errors[] = $s_error;
        }
        return false;
    }

    /**
     * display magnalister logo in order detail
     *
     */
    public function hookDisplayAdminOrder($a_params)
    {
        require_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'lib'.DIRECTORY_SEPARATOR.'Core'.DIRECTORY_SEPARATOR.'ML.php');
        if (ML::isInstalled()) {
            ML::setFastLoad(true);
            $o_order = MLOrder::factory()->set('current_orders_id', $a_params['id_order']);
            $s_logo_html = $o_order->getTitle();

            if ($o_order->get('special') !== null) {
                ob_start();
                require(MLFilesystem::gi()->getViewPath('hook_orderdetails'));
                $s_info_html = ob_get_clean();
                $this->smarty->assign(array(
                    's_logo_html' => $s_logo_html,
                    's_info_html' => $s_info_html,
                ));
                return $this->display(__FILE__, 'magnalister_order_detail.tpl');
            }
        } else {
            return '';
        }
    }

    /**
     * prestashop order status change triger 
     *
     */
    public function hookActionOrderStatusUpdate($a_params)
    {
        /**
         * in this function we could change magnalister_order state depends on prestashop order status changes
         * we don't use it at moment , we keep as it is , because may be 
         * removing it  can cause problem for already installed module 
         */
        isset($a_params['newOrderStatus']);
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        //some of thirdparty module can have controller, that doesn't have this method
        if (is_callable(array($this->context->controller, 'addCSS')) && is_callable(array($this->context->controller, 'addJS'))) {
            //load css and javascript to load magnalister icon on vertical menu
            if (version_compare(_PS_VERSION_, '1.6.1.0 ', '>=')) {
                $this->context->controller->addCSS(($this->_path).'/views/css/menuTabIcon_161.css');
            } else {
                $this->context->controller->addJS(($this->_path).'/views/js/fixlogo.js');
                $this->context->controller->addCSS(($this->_path).'/views/css/menuTabIcon_160.css');
            }

            //load javascript to display boarding correctly
            if (get_class($this->context->controller) == 'AdminMagnalisterController' && version_compare(_PS_VERSION_, '1.6', '>=')) {
                $this->context->controller->addJS(($this->_path).'/views/js/fixboarding.js');
                $this->context->controller->addCSS(($this->_path).'/views/css/global_16.css');
            }
        }
    }
}
