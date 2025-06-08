<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2015 knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class SeoWizard extends Module
{

    public function __construct()
    {
        $this->name = 'seowizard';
        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'Knowband';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array(
            'min' => '1.6',
            'max' => _PS_VERSION_);
        $this->bootstrap = true;
        parent::__construct(); /* Calling the parent constuctor method */
        $this->displayName = $this->l('Seo Wizard Free Version');
        $this->description = $this->l('This module will help you in generating sitemap for your site. It will also help you in interlinking of your site.');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        if (!Configuration::get('knowband_seo_wizard')) {
            $this->warning = $this->l('No name provided');
        }
    }

//Installation
    public function install()
    {
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!Configuration::get('KBSEO_SECURE_KEY')) {
            Configuration::updateValue('KBSEO_SECURE_KEY', $this->kbmaSecureKeyGenerator());
        }

        if ($this->installModuleTabs('AdminKbSeoWizardMenu', $this->l('Seo Wizard'), 0)) {
//Code to add submenus
            $subMenuList = $this->adminSubMenus();
            if (isset($subMenuList)) {
                foreach ($subMenuList as $subMenuList) {
                    $this->installModuleTabs($subMenuList['class'], $subMenuList['name'], $subMenuList['parent_id'], $subMenuList['active']);
                }
            }
        } else {
            $this->custom_errors[] = $this->l('Error occurred while adding module tabs.');
            return false;
        }

        if (!parent::install() || !$this->registerHook('displayBackOfficeHeader') || !$this->registerHook('displayHeader')) {
            return false;
        }
        $create_table = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "knowband_sitemap` (
            `ks_id` int(11) NOT NULL AUTO_INCREMENT,
            `ks_type` enum('products','categories','cms','manufacturers') DEFAULT NULL,
            `ks_shop_id` int(11) DEFAULT NULL,
            `ks_language_id` int(11) DEFAULT NULL,
            `ks_priority` varchar(3) NOT NULL,
            `ks_frequency` enum('daily', 'weekly', 'monthly', 'yearly') DEFAULT 'daily',
            `ks_images` tinyint(1) DEFAULT '0',
            `ks_enable` tinyint(1) DEFAULT '0',
            `ks_date_added` datetime NOT NULL,
            `ks_date_modified` datetime NOT NULL,
            PRIMARY KEY (`ks_id`),
            KEY `ks_shop_id` (`ks_shop_id`),
            KEY `ks_language_id` (`ks_language_id`),
            KEY `ks_type` (`ks_type`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        Db::getInstance()->execute($create_table);

        $create_table_interlinking = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "knowband_interlinking` (
            `knit_id` int(11) NOT NULL AUTO_INCREMENT,
            `knit_keyword` varchar(100) NOT NULL,
            `knit_selector` varchar(20) DEFAULT NULL,
            `knit_group` int(11) DEFAULT NULL,
            `knit_lang_id` int(11) NOT NULL,
            `knit_keyword_url` text NOT NULL,
            `knit_keyword_url_title` varchar(255) NOT NULL,
            `knit_link_position` enum('top','bottom','middle') DEFAULT 'top',
            `knit_description` varchar(20) NOT NULL DEFAULT 'short',
            `knit_enable` int(1) DEFAULT '0',
            `knit_new_tab` int(1) DEFAULT '0',
            `knit_follow` int(1) DEFAULT '0',
            `knit_date_added` datetime NOT NULL,
            `knit_date_modified` datetime NOT NULL,
            PRIMARY KEY (`knit_id`),
            KEY `knit_lang_id` (`knit_lang_id`),
            KEY `knit_selector` (`knit_selector`),
            KEY `knit_enable` (`knit_enable`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        Db::getInstance()->execute($create_table_interlinking);

        $create_table_meta = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "knowband_meta` (
            `knme_id` int(11) NOT NULL AUTO_INCREMENT,
            `knme_enable` tinyint(1) NOT NULL,
            `knme_selector` varchar(20) NOT NULL,
            `knme_lang_id` int(11) NOT NULL,
            `knme_meta_tag` varchar(255) NOT NULL,
            `knme_meta_description` text NOT NULL,
            `knme_meta_keyword` varchar(255) NOT NULL,
            `knme_meta_type` varchar(20) NOT NULL,
            `knme_date_added` datetime NOT NULL,
            `knme_date_modified` datetime NOT NULL,
            PRIMARY KEY (`knme_id`),
            KEY `knme_lang_id` (`knme_lang_id`),
            KEY `knme_meta_type` (`knme_meta_type`),
            KEY `knme_enable` (`knme_enable`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        Db::getInstance()->execute($create_table_meta);

        $create_table_initial_data = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "knowband_initial_content` (
            `id_content` int(11) NOT NULL,
            `id_lang` int(11) NOT NULL,
            `id_shop` int(11) NOT NULL,
            `type` varchar(20) NOT NULL,
            `description` text,
            `short_description` text,
            `meta_tags` text,
            `meta_title` text,
            `meta_description` text,
            `meta_keyword` text,
            `date_add` date NOT NULL,
            `date_upd` date DEFAULT NULL
           ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        Db::getInstance()->execute($create_table_initial_data);
        Configuration::updateValue('knowband_seo_wizard', 0);
        return true;
    }

    public function uninstall()
    {
        if (!parent::uninstall() || !Configuration::deleteByName('KBSEO_PRODUCT_UPDATE_SECURE_KEY') || !Configuration::deleteByName('knowband_seo_wizard') || !$this->unregisterHook('displayBackOfficeHeader') || !$this->unregisterHook('displayHeader')
        ) {
            return false;
        }

        $tabClass = 'AdminKbSeoWizardMenu';

        $idTab = Tab::getIdFromClassName($tabClass);
        if ($idTab != 0) {
            $tab = new Tab($idTab);
            if ($tab->delete()) {
//Code to add submenus
                $subMenuList = $this->adminSubMenus();

                if (isset($subMenuList)) {
                    foreach ($subMenuList as $subMenuList) {
                        $idTab = Tab::getIdFromClassName($subMenuList['class']);
                        if ($idTab != 0) {
                            $tab = new Tab($idTab);
                            $tab->delete();
                        }
                    }
                }
            }
        }

        return true;
    }

    public function hookDisplayHeader($params)
    {
        $this->context = Context::getContext();
        $controllerName = Context::getContext()->controller->php_self;
        $return_facebook = '';
        $return_twitter = '';
        return $return_facebook . $return_twitter;
    }

    private function ajaxProcess($method)
    {
        $this->json = array();
        switch ($method) {
            case 'generateproductsitemap':
                $form_arr = array();
                $form_arr = Tools::getValue('seowizard');
                $sql = 'SELECT COUNT(*) as TOTAL FROM ' . _DB_PREFIX_ . 'knowband_sitemap WHERE ks_shop_id = ' . (int) $form_arr['sitemap_prod_shop'] . ' AND ks_type = "products" AND ks_language_id = ' . (int) $this->context->language->id;
                $count = Db::getInstance()->getRow($sql);
                $priority = $form_arr['sitemap_prod_priority'] / 10;
                if ($count['TOTAL'] > 0) {
                    $query = "UPDATE " . _DB_PREFIX_ . "knowband_sitemap SET ks_priority = '" . pSQL($priority) . "',"
                        . " ks_frequency = '" . pSQL($form_arr['sitemap_prod_frequency']) . "',"
                        . " ks_images = '0',"
                        . " ks_enable = '" . (int) $form_arr['enable_prod'] . "',"
                        . " ks_date_modified = NOW()"
                        . " WHERE ks_shop_id = '" . (int) $form_arr['sitemap_prod_shop'] . "' AND ks_type = 'products' AND ks_language_id = '" . (int) $this->context->language->id . "'";
                } else {
                    $query = "INSERT INTO " . _DB_PREFIX_ . "knowband_sitemap
                        (ks_type, ks_shop_id, ks_priority, ks_frequency, ks_language_id, ks_images, ks_enable, ks_date_added, ks_date_modified)
                        VALUES('products'," . (int) $form_arr['sitemap_prod_shop'] . ", '" . pSQL($priority) . "', '" . pSQL($form_arr['sitemap_prod_frequency']) . "'," . (int) $this->context->language->id . ", '0', '" . (int) $form_arr['enable_prod'] . "', NOW(), NOW())";
                }
                Db::getInstance()->execute($query);
                $xml = $this->generateSitemap($this->context->language->id, 'products');
                $this->json['success'] = $this->getSitemapDirUrl() . $xml;
                break;

            default:
                break;
        }


        header('Content-Type: application/json', true);
        echo Tools::jsonEncode($this->json);
        die;
    }

//Hook to add content on Back Office Header
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS($this->getModuleDirUrl() . 'seowizard/views/css/admin/tab.css');
    }

    public function getContent()
    {

        $output = null;
        $this->setKbMedia();  //Add Media
        if (Tools::isSubmit('ajax')) {
            $this->ajaxProcess(Tools::getValue('method'));
        }
        $shops = Shop::getShops();
        $sitemap = array();
        if (!is_writable(_PS_ROOT_DIR_)) {
            $output .= $this->displayError(
                $this->l('An error occured while trying to check your file permissions. Please adjust your permissions to allow PrestaShop to create a writable folder in your root directory.')
            );
        } else {
            if (!file_exists(_PS_ROOT_DIR_ . '/kbseowizard/')) {
                if (!mkdir(_PS_ROOT_DIR_ . '/kbseowizard/', 0777, true)) {
                    $output .= $this->displayError(
                        $this->l('Unable to create writable `kbseowizard` folder inside ') . _PS_ROOT_DIR_ . $this->l(' directory.')
                    );
                }
            }
        }

        foreach ($shops as $shop) {
            $languages = Language::getLanguages(true, $shop['id_shop'], true);
            foreach ($languages as $language) {
                $lang_code = Language::getIsoById($language);
                $path = _PS_ROOT_DIR_ . '/kbseowizard/sitemap_' . $shop['id_shop'] . '_' . $lang_code . '.xml';
                $url = $this->getSitemapDirUrl() . 'sitemap_' . $shop['id_shop'] . '_' . $lang_code . '.xml';
                if (file_exists($path)) {
                    $sitemap[$shop['id_shop']][$language] = $url;
                }
            }
        }
        $this->context->smarty->assign('sitemaps', $sitemap);
        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->default_form_language = $this->context->language->id;
        $action = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules');
        $helper->submit_action = $action;
        $module_path = AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules') .
            '&configure=' . $this->name;
        $this->context->smarty->assign('module_path', $module_path); //module path

        $formvalue = array();
        $selecSiteMapSQL = "SELECT ks_shop_id ,ks_language_id,ks_priority,ks_frequency,ks_images,ks_enable FROM " . _DB_PREFIX_ . "knowband_sitemap WHERE ks_type = 'products' and ks_language_id =" . (int) $this->context->language->id;
        $selecSiteMapSQL = DB::getInstance()->getRow($selecSiteMapSQL, false);
        if (!Tools::isEmpty($selecSiteMapSQL)) {
            $formvalue['sitemap_prod_shop'] = $selecSiteMapSQL['ks_shop_id'];
            $formvalue['sitemap_prod_priority'] = $selecSiteMapSQL['ks_priority'] * 10;
            $formvalue['sitemap_prod_frequency'] = $selecSiteMapSQL['ks_frequency'];
            $formvalue['img_prod_enable'] = $selecSiteMapSQL['ks_images'];
            $formvalue['enable_prod'] = $selecSiteMapSQL['ks_enable'];
            $formvalue['sitemap_prod_lang'] = $selecSiteMapSQL['ks_language_id'];
        }
        
        //d($formvalue['sitemap_cms_frequency']);
        $helper->fields_value['seowizard[sitemap_prod_shop]'] = isset($formvalue['sitemap_prod_shop']) ? $formvalue['sitemap_prod_shop'] : '';
        $helper->fields_value['seowizard[sitemap_prod_priority]'] = isset($formvalue['sitemap_prod_priority']) ? $formvalue['sitemap_prod_priority'] : '';
        $helper->fields_value['seowizard[sitemap_prod_frequency]'] = isset($formvalue['sitemap_prod_frequency']) ? $formvalue['sitemap_prod_frequency'] : '';
        $helper->fields_value['seowizard[img_prod_enable]'] = isset($formvalue['img_prod_enable']) ? $formvalue['img_prod_enable'] : '';
        $helper->fields_value['seowizard[enable_prod]'] = isset($formvalue['enable_prod']) ? $formvalue['enable_prod'] : '';
        $helper->fields_value['seowizard[submit_seo_prod_wizard]'] = isset($formvalue['submit_seo_prod_wizard']) ? $formvalue['submit_seo_prod_wizard'] : '';
        $helper->fields_value['seowizard[sitemap_prod_lang]'] = isset($formvalue['sitemap_prod_lang']) ? $formvalue['sitemap_prod_lang'] : '';

        $helper->fields_value['seowizard[sitemap_cat_shop]'] = isset($formvalue['sitemap_cat_shop']) ? $formvalue['sitemap_cat_shop'] : '';
        $helper->fields_value['seowizard[sitemap_cat_priority]'] = isset($formvalue['sitemap_cat_priority']) ? $formvalue['sitemap_cat_priority'] : '';
        $helper->fields_value['seowizard[sitemap_cat_frequency]'] = isset($formvalue['sitemap_cat_frequency']) ? $formvalue['sitemap_cat_frequency'] : '';
        $helper->fields_value['seowizard[sitemap_cat_lang]'] = isset($formvalue['sitemap_cat_lang']) ? $formvalue['sitemap_cat_lang'] : '';
        $helper->fields_value['seowizard[enable_cat]'] = isset($formvalue['enable_cat']) ? $formvalue['enable_cat'] : '';

        $helper->fields_value['seowizard[sitemap_cms_shop]'] = isset($formvalue['sitemap_cms_shop']) ? $formvalue['sitemap_cms_shop'] : '';
        $helper->fields_value['seowizard[sitemap_cms_priority]'] = isset($formvalue['sitemap_cms_priority']) ? $formvalue['sitemap_cms_priority'] : '';
        $helper->fields_value['seowizard[sitemap_cms_frequency]'] = isset($formvalue['sitemap_cms_frequency']) ? $formvalue['sitemap_cms_frequency'] : '';
        $helper->fields_value['seowizard[sitemap_cms_lang]'] = isset($formvalue['sitemap_cms_lang']) ? $formvalue['sitemap_cms_lang'] : '';
        $helper->fields_value['seowizard[enable_cms]'] = isset($formvalue['enable_cms']) ? $formvalue['enable_cms'] : '';

        $helper->fields_value['seowizard[sitemap_man_shop]'] = isset($formvalue['sitemap_man_shop']) ? $formvalue['sitemap_man_shop'] : '';
        $helper->fields_value['seowizard[sitemap_man_priority]'] = isset($formvalue['sitemap_man_priority']) ? $formvalue['sitemap_man_priority'] : '';
        $helper->fields_value['seowizard[sitemap_frequency]'] = isset($formvalue['sitemap_man_frequency']) ? $formvalue['sitemap_man_frequency'] : '';
        $helper->fields_value['seowizard[sitemap_man_lang]'] = isset($formvalue['sitemap_man_lang']) ? $formvalue['sitemap_man_lang'] : '';
        $helper->fields_value['seowizard[enable_man]'] = isset($formvalue['enable_man']) ? $formvalue['enable_man'] : '';

        $this->context->smarty->assign('form_product', $helper->generateForm(array(
                self::sitemapProductForm())));
        $this->context->smarty->assign('form_category', $helper->generateForm(array(
                self::sitemapCategoryForm())));
        $this->context->smarty->assign('form_cms', $helper->generateForm(array(
                self::sitemapCmsForm())));
        $this->context->smarty->assign('form_manufacturer', $helper->generateForm(array(
                self::sitemapManufacturersForm())));
        $html = $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name . '/views/templates/admin/admin.tpl'
        );

        $output .= $html;
//        $output .= $sitemap_generation_table;
        return $output;
    }

    public function setKbMedia()
    {
        $this->context->controller->addJs($this->getModuleDirUrl() . 'seowizard/views/js/velovalidation.js');
        $this->context->controller->addJs($this->getModuleDirUrl() . 'seowizard/views/js/admin/seowizard.js');
        $this->context->controller->addCSS($this->getModuleDirUrl() . 'seowizard/views/css/admin/seo_wizard_admin.css');
    }

    private function getModuleDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . str_replace(_PS_ROOT_DIR_ . '/', '', _PS_MODULE_DIR_);
        }
        return $module_dir;
    }

// <editor-fold defaultstate="collapsed" desc="TO get root directory sitemap url">
//**********************************************************Author: Harish Singh****************************************************//
//**************************************************************START HERE**********************************************************//
    private function getSitemapDirUrl()
    {
        $module_dir = '';
        if ($this->checkSecureUrl()) {
            $module_dir = _PS_BASE_URL_SSL_ . __PS_BASE_URI__ . 'kbseowizard/';
        } else {
            $module_dir = _PS_BASE_URL_ . __PS_BASE_URI__ . 'kbseowizard/';
        }
        return $module_dir;
    }

//***************************************************************END HERE***********************************************************//
// </editor-fold>

    private function checkSecureUrl()
    {
        $custom_ssl_var = 0;

        if (isset($_SERVER['HTTPS'])) {
            if ($_SERVER['HTTPS'] == 'on') {
                $custom_ssl_var = 1;
            }
        } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $custom_ssl_var = 1;
        }
        if ((bool) Configuration::get('PS_SSL_ENABLED') && $custom_ssl_var == 1) {
            return true;
        } else {
            return false;
        }
    }

    public function generateSitemap($language, $sitemap_type = null)
    {
        $json = array();
        $fetch_sitemap_data = "select * from " . _DB_PREFIX_ . "knowband_sitemap where ks_language_id = '" . (int) $language . " ' AND ks_type='" . pSQL($sitemap_type) . "'";
        $sitemap_data = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($fetch_sitemap_data);
        $language_id = $this->context->language->id;
        
        $lang_code = Language::getIsoById($language_id);
        if (!empty($sitemap_data) > 0) {
            switch ($sitemap_data['ks_type']) {
                case 'products':
                    $html = '';
                    if ($sitemap_data['ks_enable'] == 1) {
                        $fetchproduct_data = 'select id_product from ' . _DB_PREFIX_ . 'product where active = "1" AND id_shop_default = ' . (int) $sitemap_data['ks_shop_id'];
                        $product_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($fetchproduct_data, true, false);
                        $prod_url = array();
                        $i = 0;
                        foreach ($product_id as $prod_id) {
                            $link_rewrite = array();
                            $prod_url[$i]['prod'] = $this->getProductLinkKb($prod_id['id_product'], $language_id);
                            $i++;
                        }
                        $this->context->smarty->assign('prod_url_data', $prod_url);
                        $this->context->smarty->assign('sitemap_data', $sitemap_data);

                        $html = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/product_template.tpl');
                    }
                    $fp = fopen(_PS_ROOT_DIR_ . '/kbseowizard/product_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml', 'w');
                    fwrite($fp, $html);
                    fclose($fp);
                    break;

                default:
                    break;
            }
            $html_sitemap = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/sitemap_template.tpl');

            $product = _PS_ROOT_DIR_ . '/kbseowizard/product_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml';
            if (file_exists($product)) {
            } else {
                $fp = fopen(_PS_ROOT_DIR_ . '/kbseowizard/product_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml', 'w');
                fwrite($fp, '');
                fclose($fp);
            }

            $html_sitemap_prod = $this->context->smarty->fetch(_PS_ROOT_DIR_ . '/kbseowizard/product_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml');
            $html_sitemap_data = str_replace('[html_data]',  $html_sitemap_prod , $html_sitemap);
            $fp = fopen(_PS_ROOT_DIR_ . '/kbseowizard/sitemap_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml', 'w');
            fwrite($fp, $html_sitemap_data);
            fclose($fp);
            return 'sitemap_' . $sitemap_data['ks_shop_id'] . '_' . $lang_code . '.xml';
        }
        return $json;
    }


    public function getProductLinkKb($pid, $id_lang = 0, $paid = 0)
    {
        $pro_obj = new Product((int) $pid);
        $def_attr = 0;
        if ($paid == 0) {
            if (isset($pro_obj->cache_default_attribute)) {
                $def_attr = $pro_obj->cache_default_attribute;
            } else {
                $def_attr = Product::getDefaultAttribute($pid);
            }
        } else {
            $def_attr = $paid;
        }
        $link_rewrite = null;
        if (isset($pro_obj->link_rewrite[$id_lang])) {
            $link_rewrite = $pro_obj->link_rewrite[$id_lang];
        }
        $product_url = Context::getContext()->link->getProductLink(
            $pro_obj->id,
            $link_rewrite,
            $pro_obj->category,
            null,
            $id_lang,
            $pro_obj->id_shop_default,
            $def_attr,
            false,
            false,
            true
        );
        unset($pro_obj);
        unset($link_rewrite);
        unset($def_attr);
        return $product_url;
    }

    public function getUrlRewriteInformations($id_product, $lang_id)
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                        SELECT pl.`id_lang`, pl.`link_rewrite`, p.`ean13`, cl.`link_rewrite` AS category_rewrite
                        FROM `' . _DB_PREFIX_ . 'product` p
                        LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product`' . Shop::addSqlRestrictionOnLang('pl') . ')
                        ' . Shop::addSqlAssociation('product', 'p') . '
                        LEFT JOIN `' . _DB_PREFIX_ . 'lang` l ON (pl.`id_lang` = l.`id_lang`)
                        LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON (cl.`id_category` = product_shop.`id_category_default`  AND cl.`id_lang` = pl.`id_lang`' . Shop::addSqlRestrictionOnLang('cl') . ')
                        WHERE p.`id_product` = ' . (int) $id_product . '
                        AND l.`active` = 1 AND pl.`id_lang` = ' . (int) $lang_id . '
                ');
    }

    public function getCategories($shop, $language_id)
    {
        $id_lang = $language_id;
        $active = true;
        if (!Validate::isBool($active)) {
            die(Tools::displayError());
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
                    SELECT c.id_category
                    FROM `' . _DB_PREFIX_ . 'category` c
                    ' . Shop::addSqlAssociation('category', 'c') . '
                    LEFT JOIN `' . _DB_PREFIX_ . 'category_lang` cl ON c.`id_category` = cl.`id_category`' . Shop::addSqlRestrictionOnLang('cl') . '
                    WHERE 1 ' . ($id_lang ? 'AND id_shop_default = ' . (int) $shop . ' AND `id_lang` = ' . (int) $id_lang : '') . '
                    ' . ($active ? 'AND `active` = 1' : '') . '
                    ' . (!$id_lang ? 'GROUP BY c.id_category' : '') . '
                    ORDER BY c.`level_depth` ASC, category_shop.`position` ASC');
        return $result;
    }

    protected function sitemapProductForm()
    {
        $shops = Shop::getShops();
        $priority = $this->getSitemapPriority();
        $frequency = $this->getFrequency();
        $languages = array();
        $currentLanguage = Language::getLanguage($this->context->language->id);
        $languages[] = $currentLanguage;
        $fields_form = array(
            'form' => array(
                'id_form' => 'kbsx_sitemap_prod_form',
                'legend' => array(
                    'title' => $this->l('Sitemap settings for Products'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Select Shop'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_prod_shop]',
                        'hint' => $this->l('Select shops from here'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $shops,
                            'id' => 'id_shop',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Sitemap Language'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_prod_lang]',
                        'hint' => $this->l('Select language from here'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $languages,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Priority'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_prod_priority]',
                        'hint' => $this->l('Select priority from here'),
                        'is_bool' => true,
                        
                        'options' => array(
                            'query' => $priority,
                            'id' => 'id_priority',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_prod_frequency]',
                        'hint' => $this->l('Select frequency from here'),
                        'is_bool' => true,
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'class' => 'free-disabled',
                        'label' => $this->l('Include Images'),
                        'name' => 'seowizard[img_prod_enable]',
                        'desc' => $this->l('Toggle to enable or disable images'),
                        'hint' => $this->l('To enable or disable the images'),
                        'is_bool' => true,
                        'disabled' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_img_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_img_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable/Disable'),
                        'name' => 'seowizard[enable_prod]',
                        'desc' => $this->l('Toggle to enable or disable product sitemap'),
                        'hint' => $this->l('To enable or disable the product sitemap'),
                        'is_bool' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_prod_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_prod_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                ),
                'buttons' => array(
                    array(
                        'class' => 'btn btn-default pull-right',
                        'name' => 'seowizard[submit_seo_prod_wizard]',
                        'id' => 'submit_seo_prod_wizard',
                        'js' => "seositemapprodvalidation()",
                        'title' => $this->l('Save'),
                        'icon' => 'process-icon-save'
                    )
                )
            )
        );
        return $fields_form;
    }

    protected function sitemapCategoryForm()
    {
        $shops = Shop::getShops();
        $priority = $this->getSitemapPriority();
        $frequency = $this->getFrequency();
        $languages = array();
        $currentLanguage = Language::getLanguage($this->context->language->id);
        $languages[] = $currentLanguage;
//        d($languages);
        $fields_form = array(
            'form' => array(
                'id_form' => 'kbsx_sitemap_cat_form',
                'legend' => array(
                    'title' => $this->l('Sitemap settings for Categories'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Select Shop'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cat_shop]',
                        'hint' => $this->l('Select shops from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $shops,
                            'id' => 'id_shop',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Sitemap Language'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cat_lang]',
                        'hint' => $this->l('Select language from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $languages,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Priority'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cat_priority]',
                        'hint' => $this->l('Select priority from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $priority,
                            'id' => 'id_priority',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cat_frequency]',
                        'hint' => $this->l('Select frequency from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable/Disable'),
                        'name' => 'seowizard[enable_cat]',
                        'desc' => $this->l('Toggle to enable or disable category sitemap'),
                        'hint' => $this->l('To enable or disable the category sitemap'),
                        'is_bool' => true,
                        'disabled' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_cat_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_cat_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                ),
                'buttons' => array(
                    array(
                        'class' => 'btn btn-default pull-right',
                        'name' => 'seowizard[submit_seo_cat_wizard]',
                        'id' => 'submit_seo_wizard',
                        'disabled' => true,
                        'title' => $this->l('Save'),
                        'icon' => 'process-icon-save'
                    )
                )
            )
        );
        return $fields_form;
    }

    protected function sitemapCmsForm()
    {
        $shops = Shop::getShops();
        $priority = $this->getSitemapPriority();
        $frequency = $this->getFrequency();
        $languages = array();
        $currentLanguage = Language::getLanguage($this->context->language->id);
        $languages[] = $currentLanguage;
        $fields_form = array(
            'form' => array(
                'id_form' => 'kbsx_sitemap_cms_form',
                'legend' => array(
                    'title' => $this->l('Sitemap settings for CMS pages'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Select Shop'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cms_shop]',
                        'hint' => $this->l('Select shops from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $shops,
                            'id' => 'id_shop',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Sitemap Language'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cms_lang]',
                        'hint' => $this->l('Select language from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $languages,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Priority'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cms_priority]',
                        'hint' => $this->l('Select priority from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $priority,
                            'id' => 'id_priority',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_cms_frequency]',
                        'hint' => $this->l('Select frequency from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable/Disable'),
                        'name' => 'seowizard[enable_cms]',
                        'desc' => $this->l('Toggle to enable or disable CMS sitemap'),
                        'hint' => $this->l('To enable or disable the CMS sitemap'),
                        'is_bool' => true,
                        'disabled' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_cms_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_cms_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                ),
                'buttons' => array(
                    array(
                        'class' => 'btn btn-default pull-right',
                        'name' => 'seowizard[submit_seo_cms_wizard]',
                        'id' => 'submit_seo_cms_wizard',
                        'disabled' => true,
                        'title' => $this->l('Save'),
                        'icon' => 'process-icon-save'
                    )
                )
            )
        );
        return $fields_form;
    }

    protected function sitemapManufacturersForm()
    {
        $shops = Shop::getShops();
        $priority = $this->getSitemapPriority();
        $frequency = $this->getFrequency();
        $languages = array();
        $currentLanguage = Language::getLanguage($this->context->language->id);
        $languages[] = $currentLanguage;
        $fields_form = array(
            'form' => array(
                'id_form' => 'kbsx_sitemap_man_form',
                'legend' => array(
                    'title' => $this->l('Sitemap setting for Manufacturers'),
                    'icon' => 'icon-cogs'
                ),
                'input' => array(
                    array(
                        'label' => $this->l('Select Shop'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_man_shop]',
                        'hint' => $this->l('Select shops from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $shops,
                            'id' => 'id_shop',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Sitemap Language'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_man_lang]',
                        'hint' => $this->l('Select language from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $languages,
                            'id' => 'id_lang',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Priority'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_man_priority]',
                        'hint' => $this->l('Select priority from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $priority,
                            'id' => 'id_priority',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'label' => $this->l('Frequency'),
                        'type' => 'select',
                        'name' => 'seowizard[sitemap_frequency]',
                        'hint' => $this->l('Select frequency from here'),
                        'is_bool' => true,
                        'disabled' => true,
                        'options' => array(
                            'query' => $frequency,
                            'id' => 'id_frequency',
                            'name' => 'name',
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Enable/Disable'),
                        'name' => 'seowizard[enable_man]',
                        'desc' => $this->l('Toggle to enable or disable manufacturers sitemap'),
                        'hint' => $this->l('To enable or disable the manufacturers sitemap'),
                        'is_bool' => true,
                        'disabled' => true,
                        'values' => array(
                            array(
                                'id' => 'switch_value_man_on',
                                'value' => 1,
                                'label' => $this->l('Yes')),
                            array(
                                'id' => 'switch_value_man_off',
                                'value' => 0,
                                'label' => $this->l('No')),
                        ),
                    ),
                ),
                'buttons' => array(
                    array(
                        'class' => 'btn btn-default pull-right',
                        'name' => 'seowizard[submit_seo_cms_wizard]',
                        'id' => 'submit_seo_manu_wizard',
                        'disabled' => true,
                        'title' => $this->l('Save'),
                        'icon' => 'process-icon-save'
                    )
                )
            )
        );
        return $fields_form;
    }

    protected function getSitemapPriority()
    {
        return array(
            0 => array(
                'id_priority' => '1',
                'name' => 0.1
            ),
            1 => array(
                'id_priority' => '2',
                'name' => 0.2
            ),
            2 => array(
                'id_priority' => '3',
                'name' => 0.3
            ),
            3 => array(
                'id_priority' => '4',
                'name' => 0.4
            ),
            4 => array(
                'id_priority' => '5',
                'name' => 0.5
            ),
            5 => array(
                'id_priority' => '6',
                'name' => 0.6
            ),
            6 => array(
                'id_priority' => '7',
                'name' => 0.7
            ),
            7 => array(
                'id_priority' => '8',
                'name' => 0.8
            ),
            8 => array(
                'id_priority' => '9',
                'name' => 0.9
            ),
            9 => array(
                'id_priority' => '10',
                'name' => 1
            )
        );
    }

    protected function getFrequency()
    {
        return array(
            0 => array(
                'id_frequency' => 'daily',
                'name' => $this->l('Daily')
            ),
            1 => array(
                'id_frequency' => 'weekly',
                'name' => $this->l('Weekly')
            ),
            2 => array(
                'id_frequency' => 'monthly',
                'name' => $this->l('Monthly')
            ),
            3 => array(
                'id_frequency' => 'yearly',
                'name' => $this->l('Yearly')
            ),
        );
    }

//Function definition to install module tabs
    public function installModuleTabs($tabClass = '', $tabName = '', $idTabParent = 0, $status = true)
    {
        if (!empty($tabClass) && !empty($tabName)) {
            if (Tab::getIdFromClassName($tabClass)) {
                return (true);
            }

            $tabNameLang = array();

            foreach (Language::getLanguages() as $language) {
                $tabNameLang[$language['id_lang']] = $tabName;
            }

            $tab = new Tab();
            $tab->name = $tabNameLang;
            $tab->class_name = $tabClass;
            $tab->module = $this->name;
            $tab->active = $status;
            $tab->id_parent = (int) $idTabParent;

            if ($tab->save()) {
                return true;
            }
        }
    }

//Function definition to get submenus list
    public function adminSubMenus()
    {
        $subMenu = array(
            array(
                'class' => 'AdminKbSitemap',
                'name' => $this->l('Sitemap Settings'),
                'parent_id' => Tab::getIdFromClassName('AdminKbSeoWizardMenu'),
                'active' => true
            ),
            array(
                'class' => 'AdminKbInterlinking',
                'name' => $this->l('Interlinking Settings'),
                'parent_id' => Tab::getIdFromClassName('AdminKbSeoWizardMenu'),
                'active' => true
            ),
            array(
                'class' => 'AdminKbCron',
                'name' => $this->l('Optimization Settings'),
                'parent_id' => Tab::getIdFromClassName('AdminKbSeoWizardMenu'),
                'active' => true
            ),
            array(
                'class' => 'AdminKbMetaTag',
                'name' => $this->l('Meta Tag Settings'),
                'parent_id' => Tab::getIdFromClassName('AdminKbSeoWizardMenu'),
                'active' => true
            ),
            array(
                'class' => 'AdminKbCronRequestHandler',
                'name' => $this->l('Ajax Request Handler'),
                'parent_id' => Tab::getIdFromClassName('AdminKbSeoWizardMenu'),
                'active' => false
            )
        );

        return $subMenu;
    }

    private function kbmaSecureKeyGenerator($length = 32)
    {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= chr(mt_rand(33, 126));
        }
        return md5($random);
    }
}
