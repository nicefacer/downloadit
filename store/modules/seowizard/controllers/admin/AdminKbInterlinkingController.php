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

include_once(_PS_MODULE_DIR_ . 'seowizard/libraries/simplehtmldom/simple_html_dom.php');

class AdminKbInterlinkingController extends ModuleAdminController
{
    /*
     * Default function used here to define columns in the helper list of Audit Log helper list
     */

    public $kb_smarty;
    protected $kb_module_name = 'seowizard';

    public function __construct()
    {
        $this->bootstrap = true;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->list_no_link = true;
        $this->kb_smarty = new Smarty();
        $this->kb_smarty->registerPlugin('function', 'l', 'smartyTranslate');
        $this->kb_smarty->setTemplateDir(_PS_MODULE_DIR_ . $this->kb_module_name . '/views/templates/admin/');
        $this->table = 'knowband_interlinking';
        $this->identifier = 'knit_id';
        $this->lang = false;
        $this->display = 'list';

        parent::__construct();

        if (Tools::isSubmit('updateknowband_interlinking')) {
            $this->toolbar_title = $this->l('Edit Keyword', 'AdminKbInterlinkingController');
        } elseif (Tools::isSubmit('addknowband_interlinking')) {
            $this->toolbar_title = $this->l('Add New Keyword', 'AdminKbInterlinkingController');
        } else {
            $this->toolbar_title = $this->l('Interlinking Settings', 'AdminKbInterlinkingController');
        }

        $this->fields_list = array(
            'knit_id' => array(
                'title' => $this->module->l('Keyword ID', 'AdminKbInterlinkingController'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
                'search' => false
            ),
            'knit_keyword' => array(
                'title' => $this->module->l('Keyword', 'AdminKbInterlinkingController')
            ),
            'knit_selector' => array(
                'title' => $this->module->l('Selector', 'AdminKbInterlinkingController')
            ),
            'knit_enable' => array(
                'title' => $this->module->l('Enable', 'AdminKbInterlinkingController'),
                'active' => 'knit_enable',
                'type' => 'bool',
                'search' => true
            ),
            'knit_new_tab' => array(
                'title' => $this->module->l('New tab', 'AdminKbInterlinkingController'),
                'active' => 'knit_new_tab',
                'type' => 'bool',
                'search' => true
            ),
            'knit_follow' => array(
                'title' => $this->module->l('No Follow', 'AdminKbInterlinkingController'),
                'active' => 'knit_follow',
                'type' => 'bool',
                'search' => true
            ),
            'knit_date_added' => array(
                'title' => $this->module->l('Added On', 'AdminKbInterlinkingController'),
                'type' => 'datetime'
            )
        );
        $this->_use_found_rows = false;
        //Line added to remove link from list row
        //$this->module->list_no_link = true;
    }

    public function init()
    {
        parent::init();
    }

    public function initProcess()
    {
        parent::initProcess();
        if (Tools::isSubmit('action') && Tools::getValue('action') == 'duplicate') {
            if(Db::getInstance()->getValue("SELECT count(*) as result from " . _DB_PREFIX_ . "knowband_interlinking") < 5 ){
                Tools::redirectAdmin($this->context->link->getAdminlink('AdminKbInterlinking') . '&add' . $this->table . '&duplicate_id=' . (int) Tools::getValue('knit_id'));
            } else {
                $this->context->cookie->__set('kb_redirect_error', $this->module->l('Only five(5) interlinking keywords are allowed in free version module.', 'AdminKbInterlinkingController'));
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
            }
        }

        if (Tools::isSubmit('knit_enable' . $this->table)) {
            if (Tools::isSubmit('knit_id')) {
                $enable = 1;
                $updateenable = "UPDATE " . _DB_PREFIX_ . "knowband_interlinking SET knit_enable = '" . pSQL($enable) . "' WHERE knit_id = " . (int) Tools::getValue('knit_id');
                Db::getInstance()->execute($updateenable);
            }
        }

        if (Tools::isSubmit('knit_new_tab' . $this->table)) {
            if (Tools::isSubmit('knit_id')) {
                $enable = 0;
                $updateenable = "UPDATE " . _DB_PREFIX_ . "knowband_interlinking SET knit_new_tab = '" . pSQL($enable) . "' WHERE knit_id = " . (int) Tools::getValue('knit_id');
                Db::getInstance()->execute($updateenable);
            }
        }

        if (Tools::isSubmit('knit_follow' . $this->table)) {
            if (Tools::isSubmit('knit_id')) {
                $enable = 0;
                $updateenable = "UPDATE " . _DB_PREFIX_ . "knowband_interlinking SET knit_follow = '" . pSQL($enable) . "' WHERE knit_id = " . (int) Tools::getValue('knit_id');
                Db::getInstance()->execute($updateenable);
            }
        }
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
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $tpl = '';
        if ((Tools::isSubmit('deleteknowband_interlinking')) && (!Tools::isSubmit('deleteLinking'))) {
            $knit_id = Tools::getValue('knit_id');
            $this->context->smarty->assign(array(
                'REQUEST_URI' => $this->context->link->getAdminLink('AdminKbInterlinking', true) . '&deleteknowband_interlinking&knit_id=' . $knit_id,
            ));
            $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/_configure/helpers/list/list_header_interlinking.tpl');
            
        }
        $free = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/free.tpl');
        return $tpl . parent::renderList().$free;
    }

    public function postProcess()
    {
        parent::postProcess();
    }

    /**
     * Render a form
     */
    public function renderForm()
    {
        $languages = array();
        $currentLanguage = Language::getLanguage($this->context->language->id);
        $languages[] = $currentLanguage;
        $group = array(
            0 => array(
                'id_group' => 'products',
                'name' => $this->module->l('Products', 'AdminKbInterlinkingController'),
            ),
            1 => array(
                'id_group' => 'categories',
                'name' => $this->module->l('Categories', 'AdminKbInterlinkingController'),
            ),
            2 => array(
                'id_group' => 'manufacturers',
                'name' => $this->module->l('Manufacturers', 'AdminKbInterlinkingController'),
            ),
            3 => array(
                'id_group' => 'cms',
                'name' => $this->module->l('CMS Pages', 'AdminKbInterlinkingController'),
            )
        );
        $position = array(
            0 => array(
                'id_position' => 'top',
                'name' => $this->module->l('Top', 'AdminKbInterlinkingController'),
            ),
            2 => array(
                'id_position' => 'bottom',
                'name' => $this->module->l('Bottom', 'AdminKbInterlinkingController'),
            )
        );
        $description_arr = array(
            1 => array(
                'id_desc' => 'long',
                'name' => $this->module->l('Long Description', 'AdminKbInterlinkingController'),
            ),
            0 => array(
                'id_desc' => 'short',
                'name' => $this->module->l('Short Description', 'AdminKbInterlinkingController'),
            ),
            2 => array(
                'id_desc' => 'shortlong',
                'name' => $this->module->l('Short and Long Description', 'AdminKbInterlinkingController'),
            )
        );

        $knit_id = Tools::getValue('knit_id');
        $this->fields_form = array(
            'legend' => array(
                'title' => $knit_id ? $this->module->l('Edit Keyword', 'AdminKbInterlinkingController') : $this->module->l('Add New Keyword', 'AdminKbInterlinkingController'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Language', 'AdminKbInterlinkingController'),
                    'name' => 'language_id',
                    'required' => true,
                    'options' => array(
                        'query' => $languages,
                        'id' => 'id_lang',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Group', 'AdminKbInterlinkingController'),
                    'name' => 'group_id',
                    'required' => true,
                    'options' => array(
                        'query' => $group,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Description', 'AdminKbInterlinkingController'),
                    'name' => 'desc_id',
                    'hint' => $this->module->l('This is valid only for the products group.', 'AdminKbInterlinkingController'),
                    'required' => true,
                    'options' => array(
                        'query' => $description_arr,
                        'id' => 'id_desc',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'seo_knit_id'
                ),
                array(
                    'type' => 'hidden',
                    'name' => 'duplicate'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Keyword', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('URL associated with the keyword', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_url',
                    'required' => true,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('URL Title', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_title',
                    'required' => true,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Position of the Link', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_link_position',
                    'required' => true,
                    'options' => array(
                        'query' => $position,
                        'id' => 'id_position',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable nofollow', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_follow',
                    'disabled' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'switch_value_follow_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes', 'AdminKbInterlinkingController')),
                        array(
                            'id' => 'switch_value_follow_off',
                            'value' => 0,
                            'label' => $this->module->l('No', 'AdminKbInterlinkingController')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Open link in new tab', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_newtab',
                    'disabled' => true,
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'switch_value_newtab_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes', 'AdminKbInterlinkingController')),
                        array(
                            'id' => 'switch_value_newtab_off',
                            'value' => 0,
                            'label' => $this->module->l('No', 'AdminKbInterlinkingController')),
                    ),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Activate Keyword', 'AdminKbInterlinkingController'),
                    'name' => 'seo_keyword_enable',
                    'is_bool' => true,
                    'disabled' => true,
                    'values' => array(
                        array(
                            'id' => 'switch_value_enable_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes', 'AdminKbInterlinkingController')),
                        array(
                            'id' => 'switch_value_enable_off',
                            'value' => 0,
                            'label' => $this->module->l('No', 'AdminKbInterlinkingController')),
                    ),
                ),
            ),
            'buttons' => array(
                array(
                    'class' => 'btn btn-default pull-right',
                    'name' => 'submit_' . $this->table,
                    'js' => "interlinkingValidation(this)",
                    'title' => $knit_id ? $this->module->l('Update', 'AdminKbInterlinkingController') : $this->module->l('Add', 'AdminKbInterlinkingController'),
                    'icon' => 'process-icon-save'
                )
            )
        );

        //Code for Form Editing
        if ($knit_id) {
            $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_interlinking WHERE knit_id = " . (int) $knit_id;
            $selectordetails = DB::getInstance()->executeS($selectSQL, true, false);
            if (!empty($selectordetails)) {
                $this->fields_value = array(
                    'language_id' => $this->context->language->id,
                    'seo_knit_id' => $selectordetails[0]['knit_id'],
                    'group_id' => 'products',
                    'desc_id' => $selectordetails[0]['knit_description'],
                    'seo_keyword' => $selectordetails[0]['knit_keyword'],
                    'seo_keyword_url' => $selectordetails[0]['knit_keyword_url'],
                    'seo_keyword_title' => $selectordetails[0]['knit_keyword_url_title'],
                    'seo_keyword_link_position' => $selectordetails[0]['knit_link_position'],
                    'seo_keyword_follow' => 0,
                    'seo_keyword_newtab' => 0,
                    'seo_keyword_enable' => 1,
                    'duplicate' => 0,
                );
            }
        }

        if (Tools::getValue('duplicate_id')) {
            $this->warnings[] = $this->module->l('Please click on Add button to save the duplicate keyword configurations.', 'AdminKbInterlinkingController');
            $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_interlinking WHERE knit_id = " . (int) Tools::getValue('duplicate_id');
            $selectordetails = DB::getInstance()->executeS($selectSQL, true, false);
            if (!empty($selectordetails)) {
                $this->fields_value = array(
                    'language_id' => $this->context->language->id,
                    'seo_knit_id' => $selectordetails[0]['knit_id'],
                    'group_id' => 'products',
                    'desc_id' => $selectordetails[0]['knit_description'],
                    'seo_keyword' => $selectordetails[0]['knit_keyword'],
                    'seo_keyword_url' => $selectordetails[0]['knit_keyword_url'],
                    'seo_keyword_title' => $selectordetails[0]['knit_keyword_url_title'],
                    'seo_keyword_link_position' => $selectordetails[0]['knit_link_position'],
                    'seo_keyword_newtab' => 0,
                    'seo_keyword_follow' => 0,
                    'seo_keyword_enable' => 1,
                    'duplicate' => 1
                );
            }
        }
        $this->fields_value['language_id'] = $this->context->language->id;
        $this->fields_value['group_id'] = 'products';
        $this->fields_value['seo_keyword_newtab'] = 0;
        $this->fields_value['seo_keyword_follow'] = 0;
        $this->fields_value['seo_keyword_enable'] = 1;
        $tpl = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/velovalidation.tpl');
        $free = $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/free.tpl');
        return $tpl . parent::renderForm().$free;
    }

    public function processAdd()
    {
        if (Tools::isSubmit('submitAddknowband_interlinking')) {
            //Check setting already exist for module
            if (trim(Tools::getValue('seo_knit_id')) == '' || Tools::getValue('duplicate') == 1) {
                $checkKeywordExistQry = "Select * from " . _DB_PREFIX_ . "knowband_interlinking where knit_keyword = '" . pSQL(trim(Tools::getValue('seo_keyword'))) . "' and knit_selector = 'products' and knit_lang_id = '".$this->context->language->id."'";
                $keywordExist = Db::getInstance()->executeS($checkKeywordExistQry, true);
                if (count($keywordExist) > 0) {
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('An interlinking setting already exist for provided keyword in selected group.', 'AdminKbInterlinkingController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                }
                if(Db::getInstance()->getValue("SELECT count(*) as result from " . _DB_PREFIX_ . "knowband_interlinking") < 5 ){
                    $addkeyword = "INSERT INTO " . _DB_PREFIX_ . "knowband_interlinking VALUES ('', '" . pSQL(trim(Tools::getValue('seo_keyword'))) . "', 'products', '0', '" .(int)$this->context->language->id. "', '" . pSQL(Tools::getValue('seo_keyword_url')) . "', '" . pSQL(Tools::getValue('seo_keyword_title')) . "', '" . pSQL(Tools::getValue('seo_keyword_link_position')) . "','" . pSQL(Tools::getValue('desc_id')) . "', '1','0', '0', NOW(), NOW())";
                    if (Db::getInstance()->execute($addkeyword)) {
                        $this->context->cookie->__set('kb_redirect_success', $this->module->l('Keyword added successfully.', 'AdminKbInterlinkingController'));
                        Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                    }
                } else {
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('Only five(5) interlinking keywords are allowed in free version module.', 'AdminKbInterlinkingController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                }
            } else {
                $checkKeywordExistQry = "Select * from " . _DB_PREFIX_ . "knowband_interlinking where knit_keyword = '" . pSQL(trim(Tools::getValue('seo_keyword'))) . "' and knit_selector = 'products' and knit_lang_id = " . (int) $this->context->language->id . " and knit_id != " . (int) Tools::getValue('seo_knit_id');
                $keywordExist = Db::getInstance()->executeS($checkKeywordExistQry, true);
                if (count($keywordExist) > 0) {
                    $this->context->cookie->__set('kb_redirect_error', $this->module->l('An interlinking setting already exist for provided keyword in selected group.', 'AdminKbInterlinkingController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                }

                $updatekeyword = "UPDATE " . _DB_PREFIX_ . "knowband_interlinking SET knit_keyword = '" . pSQL(trim(Tools::getValue('seo_keyword'))) . "', knit_selector = 'products', knit_group = '0', knit_lang_id = '" . $this->context->language->id . "', knit_keyword_url = '" . pSQL(Tools::getValue('seo_keyword_url')) . "', knit_keyword_url_title = '" . pSQL(Tools::getValue('seo_keyword_title')) . "', knit_link_position = '" . pSQL(Tools::getValue('seo_keyword_link_position')) . "',knit_description = '" . pSQL(Tools::getValue('desc_id')) . "', knit_enable = '1', knit_new_tab = '0', knit_follow = '0' WHERE knit_id = " . (int) Tools::getValue('seo_knit_id');
                if (Db::getInstance()->execute($updatekeyword)) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('Keyword updated successfully.', 'AdminKbInterlinkingController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                }
            }
        }
    }

    //Function definition to delete Shipping Template
    public function processDelete()
    {
        if (Tools::isSubmit('deleteknowband_interlinking')) {
            if (Tools::isSubmit('deleteLinking')) {
                if (Tools::getValue('deleteLinking') == '1') {
                    $this->deleteKeywordLinking(Tools::getValue('knit_id'));
                }
                $deleteShippingTemplateSQL = "DELETE FROM " . _DB_PREFIX_ . "knowband_interlinking WHERE knit_id = " . (int) Tools::getValue('knit_id');
                if (Db::getInstance()->execute($deleteShippingTemplateSQL)) {
                    $this->context->cookie->__set('kb_redirect_success', $this->module->l('The selected keyword has been successfully deleted.', 'AdminKbInterlinkingController'));
                    Tools::redirectAdmin($this->context->link->getAdminLink('AdminKbInterlinking', true));
                }
            }
        }
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

    /**
     * Display view action link
     */
    public function displayViewLink($token = null, $id = null, $name = null)
    {
        if (!array_key_exists('View', self::$cache_lang)) {
            self::$cache_lang['View'] = $this->module->l('Duplicate', 'AdminKbInterlinkingController');
        }

        $this->context->smarty->assign(array(
            'href' => $this->context->link->getAdminlink('AdminKbInterlinking') . '&action=duplicate&knit_id=' . $id,
            'action' => self::$cache_lang['View'],
            'icon' => 'icon-copy'
        ));

        return $this->context->smarty->fetch(_PS_MODULE_DIR_ . 'seowizard/views/templates/admin/list/list_action.tpl');
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
        $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_interlinking where knit_id =" . (int) $linkingId;
        $interlinkingdetails = DB::getInstance()->executeS($selectSQL, true, false);
        if (count($interlinkingdetails)) {
            $knit_keyword = $interlinkingdetails[0]['knit_keyword'];
            $knit_selector = $interlinkingdetails[0]['knit_selector'];
            $knit_lang_id = $interlinkingdetails[0]['knit_lang_id'];
            $knit_description = $interlinkingdetails[0]['knit_description'];

            if ($knit_selector == 'products') {
                $allProducts = Product::getSimpleProducts($knit_lang_id);
                foreach ($allProducts as $product) {
                    $productObj = new Product($product['id_product'], false, $knit_lang_id);
                    if ($knit_description == 'short') {
                        $productObj->description_short = $this->removeKeywordAnchorTag($productObj->description_short, $knit_keyword);
                    } elseif ($knit_description == 'long') {
                        $productObj->description = $this->removeKeywordAnchorTag($productObj->description, $knit_keyword);
                    } elseif ($knit_description == 'shortlong') {
                        $productObj->description_short = $this->removeKeywordAnchorTag($productObj->description_short, $knit_keyword);
                        $productObj->description = $this->removeKeywordAnchorTag($productObj->description, $knit_keyword);
                    }
                    $productObj->save();
                }
            } elseif ($knit_selector == 'categories') {
                $categoryList = CategoryCore::getSimpleCategories($knit_lang_id);
                foreach ($categoryList as $category) {
                    $categoryObj = new Category($category['id_category'], $knit_lang_id);
                    $categoryObj->description = $this->removeKeywordAnchorTag($categoryObj->description, $knit_keyword);
                    $categoryObj->save();
                }
            } elseif ($knit_selector == 'manufacturers') {
                $manufacturerList = Manufacturer::getManufacturers(false, $knit_lang_id);
                foreach ($manufacturerList as $manufacturer) {
                    $manufacturerObj = new Manufacturer($manufacturer['id_manufacturer'], $knit_lang_id);
                    $manufacturerObj->description = $this->removeKeywordAnchorTag($manufacturerObj->description, $knit_keyword);
                    $manufacturerObj->save();
                }
            } elseif ($knit_selector == 'cms') {
                $cmsList = CMS::getCMSPages($knit_lang_id);
                foreach ($cmsList as $cms) {
                    $cmsObj = new CMS($cms['id_cms'], $knit_lang_id);
                    $cmsObj->content = $this->removeKeywordAnchorTag($cmsObj->content, $knit_keyword);
                    $cmsObj->save();
                }
            }
        }
    }

    private function removeKeywordAnchorTag($text, $keyword)
    {
        if ($text != '') {
            $domLong = str_get_html($text);
            foreach ($domLong->find('a') as $item) {
                if (isset($item->attr['class'])) {
                    if (strpos($item->attr['class'], 'knowband_seo_anchor') !== false) {
                        $innerText = $item->innertext;
                        if (trim($keyword) == trim($innerText)) {
                            $item->outertext = $innerText;
                        }
                    }
                }
            }
            $domLong->save();
            return $domLong;
        } else {
            return '';
        }
    }

    public function initPageHeaderToolbar()
    {
        if (!Tools::getValue('knit_id') && !Tools::isSubmit('addknowband_interlinking')) {
            if(Db::getInstance()->getValue("SELECT count(*) as result from " . _DB_PREFIX_ . "knowband_interlinking") < 5 ){
                $this->page_header_toolbar_btn['new_template'] = array(
                    'href' => self::$currentIndex . '&add' . $this->table . '&token=' . $this->token,
                    'desc' => $this->module->l('Add new', 'AdminKbInterlinkingController'),
                    'icon' => 'process-icon-new'
                );
            }
        }

        if (Tools::getValue('knit_id') || Tools::isSubmit('knit_id')) {
            $this->page_header_toolbar_btn['kb_cancel_action'] = array(
                'href' => self::$currentIndex . '&token=' . $this->token,
                'desc' => $this->module->l('Cancel', 'AdminKbInterlinkingController'),
                'icon' => 'process-icon-cancel'
            );
        }
        parent::initPageHeaderToolbar();
    }
    
    public function initToolbar()
    {
        parent::initToolbar();
        if(Db::getInstance()->getValue("SELECT count(*) as result from " . _DB_PREFIX_ . "knowband_interlinking") >= 5 ){
            unset($this->toolbar_btn['new']);
        }
    }
}
