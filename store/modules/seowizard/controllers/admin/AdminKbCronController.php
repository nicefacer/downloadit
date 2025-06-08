<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class AdminKbCronController extends ModuleAdminController
{

//Class Constructor
    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'view';

        parent::__construct();
    }

//Set JS and CSS
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJqueryPlugin('autocomplete');
        $this->addJS($this->getModuleDirUrl() . 'seowizard/views/js/admin/seowizard.js');
        $this->addCSS($this->getModuleDirUrl() . 'seowizard/views/css/admin/seo_wizard_admin.css');
    }

    public function initContent()
    {
        parent::initContent();
        $secure_key = Configuration::get('KBSEO_SECURE_KEY');
        $this->context->smarty->assign(array(
            'sync_products_optimization_url' => $this->context->link->getModuleLink('seowizard', 'cron', array(
                'action' => 'optimizeproducts',
                'secure_key' => $secure_key)),
            'sync_product_meta_tags_url' => $this->context->link->getModuleLink('seowizard', 'cron', array(
                'action' => 'productmeta',
                'secure_key' => $secure_key)),
            'sync_generate_sitemap_url' => $this->context->link->getModuleLink('seowizard', 'cron', array(
                'action' => 'generatesitmap',
                'secure_key' => $secure_key))
        ));

        $content = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/sync.tpl');

        $this->context->smarty->assign(
            array(
                'content' => $this->content . $content,
            )
        );
    }

    public function initPageHeaderToolbar()
    {
        $this->page_header_toolbar_title = $this->module->l('Seo Expert Optimizations', 'AdminKbCronController');
        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        Configuration::updateValue('KBSEO_PRODUCT_UPDATE_SECURE_KEY', $this->productUpdateKeyGenerator());
        $this->context->smarty->assign(array(
            'prod_opt_url' => $this->context->link->getAdminLink('AdminKbCronRequestHandler', true) . '&action=optimizeproducts',
            'cat_opt_url' => $this->context->link->getAdminLink('AdminKbCronRequestHandler', true) . '&action=optimizecategories',
            'cms_opt_url' => $this->context->link->getAdminLink('AdminKbCronRequestHandler', true) . '&action=optimizecms',
            'manu_opt_url' => $this->context->link->getAdminLink('AdminKbCronRequestHandler', true) . '&action=optimizemanufacturers',
            'pro_met_url' => $this->context->link->getAdminLink('AdminKbCronRequestHandler', true) . '&action=productmeta',
            'spinner_img' => $this->getModuleDirUrl() . 'seowizard/views/img/admin/loader.gif',
        ));

        $this->context->smarty->assign(
            array(
                'kb_current_token' => Tools::getAdminTokenLite('AdminKbCron')
            )
        );
        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/cron.tpl');
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

    protected function productUpdateKeyGenerator($length = 32)
    {
        $random = '';
        for ($i = 0; $i < $length; $i++) {
            $random .= chr(mt_rand(33, 126));
        }
        return md5($random);
    }

    public function postProcess()
    {
        $json = array();
        parent::postProcess();
        if (Tools::isSubmit('ajax') && Tools::getValue('ajax')) {
            if (Tools::isSubmit('method') && Tools::getValue('method')) {
                switch (Tools::getValue('method')) {
                    case 'searchAutoCompleteKbProduct':
                        $id_lang = $this->context->language->id;
                        $json = $this->kbAjaxProductList($id_lang, 0, 0, 'price', 'desc', true);
                        break;
                }
            }
            header('Content-Type: application/json', true);
            echo Tools::jsonEncode($json);
            die;
        }
        if (Tools::isSubmit('kb_seo')) {
            $this->restoreDefault();
        }
    }

    public function kbAjaxProductList($id_lang, $start, $limit, $order_by, $order_way, $search_product = false, $id_category = false, $only_active = false, Context $context = null)
    {
        if ($search_product) {
            $prod_query = trim(Tools::getValue('q', false));
            if (!$prod_query or $prod_query == '' or Tools::strlen($prod_query) < 1) {
                die();
            }

            if ($pos = strpos($prod_query, ' (ref:')) {
                $prod_query = Tools::substr($prod_query, 0, $pos);
            }
        }

        $excludeIds = Tools::getValue('excludeIds', false);
        if ($excludeIds && $excludeIds != 'NaN') {
            $excludeIds = implode(',', array_map('intval', explode(',', $excludeIds)));
        } else {
            $excludeIds = '';
        }

        if (!$context) {
            $context = Context::getContext();
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array(
                'front',
                'modulefront'))) {
            $front = false;
        }

        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'p';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        } elseif ($order_by == 'position') {
            $order_by_prefix = 'c';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }
        $sql = 'SELECT p.*, product_shop.*, pl.*
                FROM `' . _DB_PREFIX_ . 'product` p
                ' . Shop::addSqlAssociation('product', 'p') . '
                LEFT JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ')
                LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
                LEFT JOIN `' . _DB_PREFIX_ . 'supplier` s ON (s.`id_supplier` = p.`id_supplier`)
                LEFT JOIN `' . _DB_PREFIX_ . 'image_shop` image_shop
                ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1
                AND image_shop.id_shop=' . (int) $context->shop->id . ')
		LEFT JOIN `' . _DB_PREFIX_ . 'image_lang` il '
            . 'ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = ' . (int) $id_lang . ')' .
            ($id_category ? 'LEFT JOIN `' . _DB_PREFIX_ . 'category_product` c ON (c.`id_product` = p.`id_product`)' : '') . '
                WHERE pl.`id_lang` = ' . (int) $id_lang .
            (!empty($excludeIds) ? ' AND p.id_product NOT IN (' . $excludeIds . ') ' : ' ') .
            (($search_product && $prod_query != '') ? ' AND (pl.name LIKE \'%' . pSQL($prod_query) . '%\' OR p.reference LIKE \'%' . pSQL($prod_query) . '%\')' : '') .
            ($id_category ? ' AND c.`id_category` = ' . (int) $id_category : '') .
            ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '') .
            ($only_active ? ' AND product_shop.`active` = 1' : '') . '
				ORDER BY ' . (isset($order_by_prefix) ? pSQL($order_by_prefix) . '.' : '') . '`' . pSQL($order_by) . '` ' . pSQL($order_way) .
            ($limit > 0 ? ' LIMIT ' . (int) $start . ',' . (int) $limit : '');
        $rq = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
        return $rq;
    }

    private function restoreDefault()
    {
        $form_data = Tools::getValue('kb_seo');
        $group_id = $form_data['group_id'];
        $selected = $form_data['selected'];
        $excluded_products_hidden = $form_data['excluded_products_hidden'];

        $only_selected = false;
        if ($selected == '1') {
            $only_selected = true;
            if ($group_id == 'products') {
                $this->restoreProductValue($group_id, $only_selected, $excluded_products_hidden);
            }
        } else {
            if ($group_id == 'products') {
                $this->restoreProductValue($group_id, $only_selected);
            }
        }

        $this->confirmations[] = $this->module->l('Group data restored successfully.', 'AdminKbCronController');
    }

    private function restoreProductValue($type, $selected = false, $contentIds = array())
    {
        $allContent = '';
        if ($selected) {
            $allContent = "SELECT *  FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($type) . "' and id_content in (" . $contentIds . ")";
        } else {
            $allContent = "SELECT *  FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($type) . "'";
        }
        $contentList = DB::getInstance()->executeS($allContent);
        if (count($contentList)) {
            foreach ($contentList as $key => $contentItem) {
                $id_content = $contentItem['id_content'];
                $id_lang = $contentItem['id_lang'];
                $id_shop = $contentItem['id_shop'];
                $description = $contentItem['description'];
                $short_description = $contentItem['short_description'];
                $meta_title = $contentItem['meta_title'];
                $meta_description = $contentItem['meta_description'];
                $meta_keyword = $contentItem['meta_keyword'];

                $update = array(
                    'description' => pSQL($description, true),
                    'description_short' => pSQL($short_description, true),
                    'meta_title' => pSQL($meta_title, true),
                    'meta_description' => pSQL($meta_description, true),
                    'meta_keywords' => pSQL($meta_keyword, true),
                );
                $where = 'id_shop=' . (int) $id_shop . ' and id_lang =' . (int) $id_lang . ' and id_product=' . (int) $id_content;
                Db::getInstance()->update('product_lang', $update, $where);
                $where = 'id_shop=' . (int) $id_shop . ' and id_lang =' . (int) $id_lang . ' and id_content=' . (int) $id_content;
                Db::getInstance()->delete('knowband_initial_content', $where);
            }
        }
    }

}
