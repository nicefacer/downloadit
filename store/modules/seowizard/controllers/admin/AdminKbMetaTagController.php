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

class AdminKbMetaTagController extends ModuleAdminController
{
    /*
     * Default function used here to define columns in the helper list of Audit Log helper list
     */

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'knowband_meta';
        $this->display = 'list';
        $this->identifier = 'knme_id';
        $this->list_no_link = true;
        parent::__construct();

        if (Tools::isSubmit('updateknowband_meta')) {
            $this->toolbar_title = $this->l('Edit Meta Keyword', 'AdminKbMetaTagController');
        } elseif (Tools::isSubmit('addknowband_meta')) {
            $this->toolbar_title = $this->l('Add New Meta Keyword', 'AdminKbMetaTagController');
        } else {
            $this->toolbar_title = $this->l('Meta Tag Settings', 'AdminKbMetaTagController');
        }

        $this->fields_list = array(
            'knme_id' => array(
                'title' => $this->module->l('Meta ID', 'AdminKbMetaTagController'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'knme_selector' => array(
                'title' => $this->module->l('Selector', 'AdminKbMetaTagController')
            ),
            'knme_meta_type' => array(
                'title' => $this->module->l('Type', 'AdminKbMetaTagController')
            ),
            'knme_enable' => array(
                'title' => $this->module->l('Enable', 'AdminKbMetaTagController'),
                'active' => 'knme_enable',
                'type' => 'bool'
            ),
            'knme_date_added' => array(
                'title' => $this->module->l('Added On', 'AdminKbMetaTagController'),
                'type' => 'datetime'
            ),
            'knme_date_modified' => array(
                'title' => $this->module->l('Updated On', 'AdminKbMetaTagController'),
                'type' => 'datetime'
            )
        );

        //Line added to remove link from list row
        $this->module->list_no_link = true;
    }

    //Set JS and CSS
    public function setMedia()
    {
        parent::setMedia();

        $this->addJS($this->getModuleDirUrl() . 'seowizard/views/js/admin/seowizard.js');
        $this->addJS($this->getModuleDirUrl() . 'seowizard/views/js/velovalidation.js');
        $this->addCSS($this->getModuleDirUrl() . 'seowizard/views/css/admin/seo_wizard_admin.css');
    }

    public function renderList()
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $tpl = '';
        if ((Tools::isSubmit('deleteknowband_meta')) && (!Tools::isSubmit('deleteLinking'))) {
            $knme_id = Tools::getValue('knme_id');
            $this->context->smarty->assign(array(
                'REQUEST_URI' => $this->context->link->getAdminLink('AdminKbMetaTag', true) . '&deleteknowband_meta&knme_id=' . $knme_id,
            ));
            $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/_configure/helpers/list/list_header_meta.tpl');
        }
        $free = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/free.tpl');
        return $tpl . parent::renderList().$free;
    }

    /**
     * Render a form
     */
    public function renderForm()
    {
        $group = array(
            0 => array(
                'id_group' => 'products',
                'name' => $this->module->l('Products', 'AdminKbMetaTagController'),
            ),
            1 => array(
                'id_group' => 'categories',
                'name' => $this->module->l('Categories', 'AdminKbMetaTagController'),
            ),
            2 => array(
                'id_group' => 'manufacturers',
                'name' => $this->module->l('Manufacturers', 'AdminKbMetaTagController'),
            )
        );

        $type = array(
            0 => array(
                'id_type' => 'Normal',
                'name' => $this->module->l('Normal', 'AdminKbMetaTagController'),
            ),
            1 => array(
                'id_type' => 'Facebook',
                'name' => $this->module->l('Facebook', 'AdminKbMetaTagController'),
            ),
            2 => array(
                'id_type' => 'Twitter',
                'name' => $this->module->l('Twitter', 'AdminKbMetaTagController'),
            )
        );

        $knme_id = Tools::getValue('knme_id');
        $this->fields_form = array(
            'legend' => array(
                'title' => $knme_id ? $this->module->l('Edit Meta Tag', 'AdminKbMetaTagController') : $this->module->l('Add New Meta Tag', 'AdminKbMetaTagController'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Group', 'AdminKbMetaTagController'),
                    'name' => 'group_id',
                    'required' => true,
                    'options' => array(
                        'query' => $group,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'seo_knme_id'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Meta Title Tag', 'AdminKbMetaTagController'),
                    'name' => 'seo_meta_tag'
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Tag Type', 'AdminKbMetaTagController'),
                    'name' => 'seo_tag_type',
                    'options' => array(
                        'query' => $type,
                        'id' => 'id_type',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Meta Description Tag', 'AdminKbMetaTagController'),
                    'name' => 'seo_meta_tag_description'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Meta Keyword Tag', 'AdminKbMetaTagController'),
                    'name' => 'seo_meta_tag_keyword'
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Activate Meta Tag', 'AdminKbMetaTagController'),
                    'name' => 'seo_keyword_enable',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'switch_value_enable_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes', 'AdminKbMetaTagController')),
                        array(
                            'id' => 'switch_value_enable_off',
                            'value' => 0,
                            'label' => $this->module->l('No', 'AdminKbMetaTagController')),
                    ),
                ),
            ),
            'buttons' => array(
                array(
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit_' . $this->table,
                    'js' => "metaTagValidation(this)",
                    'id' => "save_meta_data",
                    'title' => $knme_id ? $this->module->l('Update', 'AdminKbMetaTagController') : $this->module->l('Add', 'AdminKbMetaTagController'),
                    'icon' => 'process-icon-save'
                )
            )
        );

        //Code for Form Editing
        if ($knme_id) {
            $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_meta WHERE knme_id = " . (int) $knme_id;
            $selectordetails = DB::getInstance()->executeS($selectSQL, true, false);
            if (!empty($selectordetails)) {
                $this->fields_value = array(
                    'seo_meta_tag' => $selectordetails[0]['knme_meta_tag'],
                    'seo_meta_tag_description' => $selectordetails[0]['knme_meta_description'],
                    'seo_tag_type' => $selectordetails[0]['knme_meta_type'],
                    'seo_meta_tag_keyword' => $selectordetails[0]['knme_meta_keyword'],
                    'group_id' => $selectordetails[0]['knme_selector'],
                    'seo_keyword_enable' => $selectordetails[0]['knme_enable'],
                    'seo_knme_id' => $selectordetails[0]['knme_id']
                );
            }
        }

//        $this->displayWarning($this->module->l('Only one combination of each group and tag type will be add else it will update matched group and tag type combination with new keyword', 'AdminKbMetaTagController'));

        $form_data = parent::renderForm();
        $this->context->smarty->assign('meta_form', $form_data);
        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/metatag.tpl');
        $free = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/free.tpl');
        return $tpl.$free;
    }

    public function postProcess()
    {
        parent::postProcess();
        //die('asdasd');
    }

    public function processAdd()
    {
        if (Tools::isSubmit('submitAddknowband_meta')) {
            if (trim(Tools::getValue('seo_knme_id')) == '') {
                $addkeyword = "INSERT INTO " . _DB_PREFIX_ . "knowband_meta VALUES ('', '" . pSQL(Tools::getValue('seo_keyword_enable')) . "', 'products', " . (int) $this->context->language->id . ", '" . pSQL(Tools::getValue('seo_meta_tag')) . "', '" . pSQL(Tools::getValue('seo_meta_tag_description')) . "', '" . pSQL(Tools::getValue('seo_meta_tag_keyword')) . "', 'Normal', NOW(), NOW())";
                if (Db::getInstance()->execute($addkeyword)) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Keyword added successfully.', 'AdminKbMetaTagController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbMetaTag', true));
                }
            } else {
                $updatekeyword = "UPDATE " . _DB_PREFIX_ . "knowband_meta SET knme_lang_id = '" . (int) $this->context->language->id . "', knme_meta_tag = '" . pSQL(Tools::getValue('seo_meta_tag')) . "', knme_meta_description = '" . pSQL(Tools::getValue('seo_meta_tag_description')) . "', knme_meta_keyword = '" . pSQL(Tools::getValue('seo_meta_tag_keyword')) . "', knme_enable = '" . pSQL(Tools::getValue('seo_keyword_enable')) . "', knme_date_modified = NOW() WHERE knme_id = " . (int) Tools::getValue('seo_knme_id');
                if (Db::getInstance()->execute($updatekeyword)) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Keyword updated successfully.', 'AdminKbMetaTagController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbMetaTag', true));
                }
            }
        }
    }

    //Function definition to delete Shipping Template
    public function processDelete()
    {
        if (Tools::isSubmit('deleteknowband_meta')) {
            if (Tools::isSubmit('deleteLinking')) {
                if (Tools::getValue('deleteLinking') == '1') {
                    $this->deleteKeywordLinking(Tools::getValue('knme_id'));
                }

                $deleteShippingTemplateSQL = "DELETE FROM " . _DB_PREFIX_ . "knowband_meta WHERE knme_id = " . (int) Tools::getValue('knme_id');
                if (Db::getInstance()->execute($deleteShippingTemplateSQL)) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Meta Tag setting deleted successfully.', 'AdminKbMetaTagController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbMetaTag', true));
                }
            }
        }
    }

    public function init()
    {
        parent::init();
    }

    public function initContent()
    {
        if (isset($this->context->cookie->kb_redirect_error)) {
            $this->errors[] = $this->context->cookie->kb_redirect_error;
            unset($this->context->cookie->kb_redirect_error);
        }

        if (isset($this->context->cookie->kb_redirect_success)) {
            $this->confirmations[] = $this->context->cookie->kb_redirect_success;
            unset($this->context->cookie->kb_redirect_success);
        }
        parent::initContent();
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'duplicate') {
            Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbInterlinking') . '&add' . $this->table . '&duplicate_id=' . (int) Tools::getValue('knme_id'));
        }

        if (Tools::isSubmit('knme_enable' . $this->table)) {
            if (Tools::isSubmit('knme_id')) {
                $selectSQL = "SELECT knme_enable FROM " . _DB_PREFIX_ . "knowband_meta WHERE knme_id = " . (int) Tools::getValue('knme_id');
                $selectenable = DB::getInstance()->executeS($selectSQL, true, false);
                if ($selectenable[0]['knme_enable'] == 1) {
                    $enable = 0;
                } else {
                    $enable = 1;
                }

                $updateenable = "UPDATE " . _DB_PREFIX_ . "knowband_meta SET knme_enable = '" . pSQL($enable) . "' WHERE knme_id = " . (int) Tools::getValue('knme_id');
                Db::getInstance()->execute($updateenable);
            }
        }
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

    private function deleteKeywordLinking($linkingId)
    {
        $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_meta where knme_id =" . (int) $linkingId;
        $meta_details = DB::getInstance()->executeS($selectSQL, true, false);
        if (count($meta_details)) {
            $knme_selector = $meta_details[0]['knme_selector'];
            $knme_lang_id = $meta_details[0]['knme_lang_id'];
            if ($knme_selector == 'products') {
                $allProducts = Product::getSimpleProducts($knme_lang_id);
                foreach ($allProducts as $product) {
                    $content = "SELECT *  FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($knme_selector) . "' and id_content = " . (int) $product['id_product'] . " and id_lang=" . (int) $knme_lang_id;
                    $contentItem = DB::getInstance()->executeS($content);
                    if (count($contentItem)) {
                        $productObj = new Product($product['id_product'], false, $knme_lang_id);
                        $meta_title = $contentItem[0]['meta_title'];
                        $meta_description = $contentItem[0]['meta_description'];
                        $meta_keyword = $contentItem[0]['meta_keyword'];
                        $productObj->meta_description = $meta_description;
                        $productObj->meta_keywords = $meta_keyword;
                        $productObj->meta_title = $meta_title;
                        $productObj->save();
                    }
                }
            } elseif ($knme_selector == 'categories') {
                $categoryList = CategoryCore::getSimpleCategories($knme_lang_id);
                foreach ($categoryList as $category) {
                    $content = "SELECT *  FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($knme_selector) . "' and id_content = " . (int) $category['id_category'] . " and id_lang=" . (int) $knme_lang_id;
                    $contentItem = DB::getInstance()->executeS($content);
                    if (count($contentItem)) {
                        $categoryObj = new Category($category['id_category'], $knme_lang_id);
                        $meta_title = $contentItem[0]['meta_title'];
                        $meta_description = $contentItem[0]['meta_description'];
                        $meta_keyword = $contentItem[0]['meta_keyword'];
                        $categoryObj->meta_description = $meta_description;
                        $categoryObj->meta_keywords = $meta_keyword;
                        $categoryObj->meta_title = $meta_title;
                        $categoryObj->save();
                    }
                }
            } elseif ($knme_selector == 'manufacturers') {
                $manufacturerList = Manufacturer::getManufacturers(false, $knme_lang_id);
                foreach ($manufacturerList as $manufacturer) {
                    $content = "SELECT *  FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($knme_selector) . "' and id_content = " . (int) $category['id_manufacturer'] . " and id_lang=" . (int) $knme_lang_id;
                    $contentItem = DB::getInstance()->executeS($content);
                    if (count($contentItem)) {
                        $manufacturerObj = new Manufacturer($category['id_manufacturer'], $knme_lang_id);
                        $meta_title = $contentItem[0]['meta_title'];
                        $meta_description = $contentItem[0]['meta_description'];
                        $meta_keyword = $contentItem[0]['meta_keyword'];
                        $manufacturerObj->meta_description = $meta_description;
                        $manufacturerObj->meta_keywords = $meta_keyword;
                        $manufacturerObj->meta_title = $meta_title;
                        $manufacturerObj->save();
                    }
                }
            }
        }
    }

    public function initToolbar()
    {
        parent::initToolbar();
        if(Db::getInstance()->getValue("SELECT count(*) as result from " . _DB_PREFIX_ . "knowband_meta") != 0 ){
            unset($this->toolbar_btn['new']);
        }
    }
}
