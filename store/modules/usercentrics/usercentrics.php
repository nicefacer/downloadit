<?php
/**
 * usercentrics
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2021 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.3
 * @link      http://www.silbersaiten.de
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(dirname(__FILE__) . '/classes/UsercentricsApi.php');

class Usercentrics extends Module
{
    private $module_html = '';
    private $module_post_errors = array();
    public $register_link = array(
        'de' => 'https://usercentrics.com/de/preise/prestashop-spezial?partnerid=18930',
        'en' => 'https://usercentrics.com/pricing/prestashop-special?partnerid=18930'
    );
    public $password_link = 'https://usercentrics.silbersaiten.de/ucpassword';
    public $partner_endpoint = 'https://usercentrics.silbersaiten.de/ucapi';

    private $api;

    public function __construct()
    {
        $this->name = 'usercentrics';
        $this->tab = 'front_office_features';
        $this->version = '1.0.3';
        $this->author = 'Silbersaiten';
        $this->need_instance = 0;
        $this->module_key = 'f9f66e678fc17f6261eb912f3f0f2b94';
        $this->ps_versions_compliancy = array(
            'min' => '1.6.0.0',
            'max' => _PS_VERSION_
        );
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Usercentrics');
        $this->description = $this->l('Usercentrics');
        $this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
        $this->api = new UsercentricsApi($this);
    }

    public function install()
    {
        $installed = parent::install()
            && $this->registerHook('displayHeader') && $this->registerHook('displayBackofficeHeader');
        return $installed;
    }

    public function getContent()
    {
        $definition_pages = $this->getDefinitionConfigurePages();
        $this->module_html .= $this->displayMenu($definition_pages);
        if (Shop::isFeatureActive() && Context::getContext()->shop->getContext() != Shop::CONTEXT_SHOP) {
            $this->module_html .= parent::displayError($this->l('You have to select a shop.'));
        } else {
            $this->module_html .= $this->postProcess();

            $this->module_html .= $this->displayPage($definition_pages);
        }
        return $this->module_html;
    }

    public static function logToFile($msg, $key = '')
    {
        if (Configuration::get('USERCENTRICS_LOG')) {
            $filename = dirname(__FILE__) . '/logs/log_' . $key . '.txt';
            $fd = fopen($filename, 'a');
            fwrite($fd, "\n" . date('Y-m-d H:i:s') . ' ' . $msg);
            fclose($fd);
        }
    }

    /*public function hookActionFrontControllerSetMedia()
    {
        $this->context->controller->registerJavascript('modules-usercentrics',
            '//app.usercentrics.eu/browser-ui/latest/bundle.js',
            array(
                'position' => 'head',
                'priority' => 1000,
                'inline' => false,
                'attributes' => 'defer',
                'server' => 'remote'));
    }*/

    public function hookDisplayHeader()
    {
        $setting_id = Configuration::get('USERCENTRICS_SETTING_ID');
        if ($setting_id != '') {
            $this->smarty->assign(array('setting_id' => $setting_id));
            return $this->display(__FILE__, 'views/templates/hook/header.tpl');
        }
    }

    public function hookDisplayBackofficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryPlugin(array('colorpicker', 'select2'));
            $this->context->controller->addJS($this->_path . 'views/js/admin.js');
            $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
            $this->context->controller->addJS(array(
                _PS_JS_DIR_ . 'jquery/plugins/select2/select2_locale_' . $this->context->language->iso_code . '.js',
            ));
        }
    }

    public function displayMenu($def_pages)
    {
        $menu_items = array();
        foreach ($def_pages['pages'] as $page_key => $page_item) {
            $menu_items[$page_key] = array(
                'name' => $page_item['name'],
                'icon' => isset($page_item['icon']) ? $page_item['icon'] : '',
                'url' => (isset($page_item['link']) && $page_item['link'] != '') ? $page_item['link'] : $this->getModuleUrl() . '&' . $def_pages['cparam'] . '=' . $page_key,
                'active' => ((!in_array(Tools::getValue($def_pages['cparam']), array_keys($def_pages['pages'])) && isset($page_item['default']) && $page_item['default'] == true) || Tools::getValue($def_pages['cparam']) == $page_key) ? true : false
            );
        }

        $this->smarty->assign(array(
            'menu_items' => $menu_items,
            'module_version' => $this->version,
            'module_name' => $this->displayName,
            'changelog' => file_exists(dirname(__FILE__) . '/Readme.md'),
            'changelog_path' => $this->getModuleUrl() . '&' . $def_pages['cparam'] . '=changelog',
            '_path' => $this->_path
        ));

        return $this->display(__FILE__, 'views/templates/admin/menu.tpl');
    }

    public function getDefinitionConfigurePages()
    {
        return array(
            'cparam' => 'view',
            'pages' => array(
                'settings' => array('name' => $this->l('Settings'), 'icon' => '', 'default' => true),
                'information' => array('name' => $this->l('Information'), 'icon' => ''),
            )
        );
    }

    public function displayPage($def_pages)
    {
        $page = Tools::getValue($def_pages['cparam']);

        $changelog_file = dirname(__FILE__) . '/Readme.md';
        if ($page == 'changelog' && method_exists($this, 'displayPage' . $page) && file_exists($changelog_file)) {
            die($this->{'displayPage' . $page}($changelog_file));
        } else {
            if (!in_array($page, array_keys($def_pages['pages']))) {
                foreach ($def_pages['pages'] as $page_key => $page_item) {
                    if (isset($page_item['default']) && $page_item['default'] == true) {
                        $page = $page_key;
                        break;
                    }
                }
            }
            if (method_exists($this, 'displayPage' . $page)) {
                return $this->{'displayPage' . $page}($def_pages['pages'][$page]['name'], array($def_pages['cparam'] => $page));
            }
        }
        return '';
    }

    public function displayPageChangelog($file)
    {
        $this->smarty->assign(
            array(
                'changelog_content' => Tools::file_get_contents($file),
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/changelog.tpl');
    }

    public function displayGeneralSettings()
    {
        $helper = new HelperForm();
        $this->context->controller->multiple_fieldsets = true;
        $helper->submit_action = 'updateSettings';
        $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
        $helper->table = '';
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->languages = $this->context->controller->_languages;
        $helper->default_form_language = $this->context->controller->default_form_language;
        $helper->module = $this;

        $fields_form = array();
        $fields_form['settings'] = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-circle'
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('Username'),
                        'name' => 'USERCENTRICS_USERNAME',
                    ),
                    array(
                        'type' => 'password',
                        'label' => $this->l('Password'),
                        'name' => 'USERCENTRICS_PASSWORD',
                        'desc' => $this->l('Leave this field blank if there\'s no change.')
                    ),
                    array(
                        'type' => 'free',
                        'name' => 'password_change',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('Domain'),
                        'name' => 'USERCENTRICS_DOMAIN',
                        'disabled' => true
                    ),
                    /*
                    array(
                        'type' => 'text',
                        'label' => $this->l('URL of shop'),
                        'name' => 'USERCENTRICS_SHOP_URL',
                        'disabled' => true
                    ),
                    */
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                    'name' => 'saveSettings',
                )
            )
        );

        $values = array();
        foreach ($fields_form['settings']['form']['input'] as $field) {
            $values[$field['name']] = Tools::getValue($field['name'], Configuration::get($field['name']));
        }

        $helper->fields_value = $values;
        $helper->fields_value['USERCENTRICS_SHOP_URL'] = $this->getShopUrl();
        $helper->fields_value['USERCENTRICS_DOMAIN'] = $this->getShopDomain();
        // $helper->fields_value['log_information'] = $this->displayLogInformation();
        $helper->fields_value['password_change'] = $this->displayPasswordChange();

        return $helper->generateForm($fields_form);
    }

    public function displayUCSettingSettings($setting_id)
    {
        $html = '';

        // get 'en' setting
        $setting = array();
        $setting['en'] = $this->api->getSetting($setting_id, 'en');
        $package_data = $this->api->getPackageData();

        foreach (array_keys($this->getUCLanguages($setting['en']['languagesAvailable'])) as $lang) {
            if ($lang != 'en') {
                // set available languages
                $language_setting = $this->api->getLanguageSetting($setting_id, $lang);
                $setting[$lang] = $language_setting;
            }
        }

        // language form
        if ($setting['en'] != false && isset($setting['en'])) {
            $helper_lang = new HelperForm();
            $this->context->controller->multiple_fieldsets = true;
            $helper_lang->submit_action = 'updateUCSetting';
            $helper_lang->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
            $helper_lang->table = '';
            $helper_lang->token = Tools::getAdminTokenLite('AdminModules');
            $helper_lang->languages = $this->context->controller->_languages;
            $helper_lang->default_form_language = $this->context->controller->default_form_language;
            $helper_lang->module = $this;

            $uc_languages = array();
            foreach ($this->getUCLanguages() as $lang => $lang_name) {
                $uc_languages[] = array('id_lang' => $lang, 'name' => $lang_name);
            }

            $fields_form = array();
            $fields_form['language'] = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Language Setting'),
                        'icon' => 'icon-circle'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Setting ID'),
                            'name' => 'setting_id',
                        ),
                        array(
                            'type' => 'uc_languagesAvailable',
                            'label' => $this->l('Languages'),
                            'name' => 'languagesAvailable[]',
                            'id' => 'languagesAvailable',
                            'size' => 50,
                            'multiple' => true,
                            'options' => array(
                                'query' => $uc_languages,
                                'id' => 'id_lang',
                                'name' => 'name'
                            ),
                            'data' => array(
                                array('name' => 'maximum-selection', 'value' => ($package_data['limit_languages'] == -1) ? 0 : ($package_data['limit_languages'] + 1))
                            )
                        ),
                    ),
                    'submit' => array(
                        'title' => $this->l('Update'),
                        'name' => 'saveUCLanguageSetting',
                    )
                )
            );
            $helper_lang->fields_value['setting_id'] = $setting_id;
            $helper_lang->fields_value['languagesAvailable[]'] = $setting['en']['languagesAvailable'];
            $html .= $helper_lang->generateForm($fields_form);
        }

        // data form
        if ($setting['en'] != false && isset($setting['en'])) {
            // form description
            $helper = new HelperForm();
            $this->context->controller->multiple_fieldsets = true;
            $helper->submit_action = 'updateUCSetting';
            $helper->currentIndex = AdminController::$currentIndex . '&configure=' . $this->name;
            $helper->table = '';
            $helper->token = Tools::getAdminTokenLite('AdminModules');
            $helper->languages = $this->context->controller->_languages;
            $helper->default_form_language = $this->context->controller->default_form_language;
            $helper->module = $this;

            // form uc languages
            $helper->tpl_vars['uc_languages'] = array();
            foreach ($this->getUCLanguages($setting['en']['languagesAvailable']) as $lang => $lang_name) {
                $helper->tpl_vars['uc_languages'][] = array('id_lang' => $lang, 'name' => $lang_name);
            }

            $helper->tpl_vars['default_uc_language'] = 'en';

            $cms_options = array();
            foreach ($helper->tpl_vars['uc_languages'] as $language) {
                $cms_options[$language['id_lang']] = array(
                    'query' => $this->getCMSPages($language['id_lang']),
                    'id' => 'value',
                    'name' => 'name'
                );
            }

            // font
            $fontfamily_options = array();
            foreach ($this->getFontFamilies() as $code => $name) {
                $fontfamily_options[] = array('code' => $code, 'name' => $name);
            }
            $fontsize_options = array();
            foreach ($this->getFontSizes() as $code => $name) {
                $fontsize_options[] = array('code' => $code, 'name' => $name);
            }

            // uc templates
            foreach ($setting['en']['categories'] as $id_cat => &$category) {
                $labels = array();
                $descriptions = array();
                $labels['en'] = $category['label'];
                $descriptions['en'] = $category['description'];
                foreach (array_keys($this->getUCLanguages($setting['en']['languagesAvailable'])) as $lang) {
                    if ($lang != 'en') {
                        $labels[$lang] = $setting[$lang]['categories'][$id_cat]['label'];
                        $descriptions[$lang] = $setting[$lang]['categories'][$id_cat]['description'];
                    }
                }
                $category['label'] = $labels;
                $category['description'] = $descriptions;
                $category['consentTemplates'] = array();
                foreach ($setting['en']['consentTemplates'] as $uc_template) {
                    if ($uc_template['categorySlug'] == $category['categorySlug']) {
                        $category['consentTemplates'][] = $uc_template;
                    }
                }
            }

            $fields_form = array();
            $fields_form['settings'] = array(
                'form' => array(
                    'legend' => array(
                        'title' => $this->l('Setting-ID') . ' : ' . $setting_id,
                        'icon' => 'icon-circle'
                    ),
                    'input' => array(
                        array(
                            'type' => 'hidden',
                            'label' => $this->l('Setting ID'),
                            'name' => 'setting_id',
                        ),
                        array(
                            'type' => 'text',
                            'label' => $this->l('Data Controller'),
                            'name' => 'dataController',
                            'desc' => $this->l('The data controller is the person or legal entity (company or organization) that is responsible for this domain.')
                        ),
                        array(
                            'type' => 'uc_textarea',
                            'label' => $this->l('Banner Message'),
                            'lang' => true,
                            'name' => 'bannerMessage',
                            'desc' => $this->l('The banner message is displayed within the Usercentrics Privacy Banner to inform your users about the usage of third-party technologies and how they can manage their privacy settings on your website.')
                        ),
                        array(
                            'type' => 'uc_select',
                            'label' => $this->l('Imprint URL'),
                            'name' => 'imprintUrl',
                            //'desc' => $this->l('Please provide a relative URL. For example "/imprint" instead of "https://example.com/imprint"'),
                            'options' => $cms_options,
                            'empty_message' => $this->l('Enable the additional Language in PrestaShop')
                        ),
                        array(
                            'type' => 'uc_select',
                            'label' => $this->l('Privacy Policy URL'),
                            'name' => 'privacyPolicyUrl',
                            //'desc' => $this->l('Please provide a relative URL. For example "/privacy-policy" instead of "https://example.com/privacy-policy"')
                            'options' => $cms_options,
                            'empty_message' => $this->l('Enable the additional Language in PrestaShop')
                        ),
                        array(
                            'type' => 'color',
                            'label' => $this->l('Primary Color'),
                            'name' => 'color',
                            'desc' => $this->l('You can enter your brand color which is used to create a custom color palette for your Consent Management Platform.')
                        ),
                        array(
                            'type' => 'uc_logo',
                            'label' => $this->l('Logo URL'),
                            'name' => 'logoUrl',
                            'disabled' => 'disabled', // For disabling
                            'class' => 'business_coming', // For disabling
                            'button' => array(
                                'label' => $this->l('Set Shop Logo URL'),
                                'url' => '' // For disabling
//                                'url' => Tools::getShopProtocol() . Tools::getMediaServer(_PS_IMG_) . _PS_IMG_.Configuration::get('PS_LOGO')
                            ),
                            'desc' => $this->l('You can set the URL to your logo. It will appear in the top left of your CMP.')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Family'),
                            'name' => 'fontfamily',
                            'disabled' => 'disabled', // For disabling
                            'class' => 'business_coming', // For disabling
                            'options' => array(
                                'query' => $fontfamily_options,
                                'id' => 'code',
                                'name' => 'name'
                            )
                            //'desc' => $this->l('The data controller is the person or legal entity (company or organization) that is responsible for this domain.')
                        ),
                        array(
                            'type' => 'select',
                            'label' => $this->l('Font Size'),
                            'name' => 'fontsize',
                            'disabled' => 'disabled', // For disabling
                            'class' => 'business_coming', // For disabling
                            'options' => array(
                                'query' => $fontsize_options,
                                'id' => 'code',
                                'name' => 'name'
                            )
                            //'desc' => $this->l('The data controller is the person or legal entity (company or organization) that is responsible for this domain.')
                        ),
                        array(
                            'type' => 'uc_lv',
                            'label' => $this->l('First Layer'),
//                            'class' => 'uc_radio',
                            'name' => 'firstlayervariant',
                            //'disabled' => 'disabled', // For disabling
                            'class' => 'uc_radio', // For disabling
                            'values' => array(
                                array('id' => 'firstlayervariant_banner', 'label' => $this->l('Privacy banner'), 'value' => 'BANNER', 'image_url' => $this->_path . 'views/img/firstlayer_banner.svg'),
                                array('id' => 'firstlayervariant_wall', 'label' => $this->l('Privacy wall'), 'value' => 'WALL', 'image_url' => $this->_path . 'views/img/firstlayer_wall.svg'),
                            ),
                            'desc' => $this->l('The First Layer is displayed to the user when the website / app is accessed for the very first time (without any preexisting consent information) and contains all information that must be disclosed to the user in order to obtain a valid consent.')
                        ),
                        array(
                            'type' => 'uc_lv',
                            'label' => $this->l('Second Layer'),
                            'name' => 'secondlayervariant',
//                            'class' => 'uc_radio',
                            //'disabled' => 'disabled', // For disabling
                            'class' => 'uc_radio', // For disabling
                            'values' => array(
                                array('id' => 'secondlayervariant_center', 'label' => $this->l('Privacy settings center'), 'value' => 'CENTER', 'image_url' => $this->_path . 'views/img/secondlayer_center.svg'),
                                array('id' => 'secondlayervariant_side', 'label' => $this->l('Privacy settings side'), 'value' => 'SIDE', 'image_url' => $this->_path . 'views/img/secondlayer_side.svg'),
                            ),
                            'desc' => $this->l('The Second Layer contains detailed information about the integrated Data Processing Services and Categories. It enables the user to view current privacy settings and to adapt them on a granular level at any time')
                        ),
                        array(
                            'type' => 'uc_switch',
                            'label' => $this->l('Overlay'),
                            'name' => 'layeroverlay',
//                            'class' => 't',
                            'is_bool' => true,
//                            'disabled' => false,
                            'disabled' => 'disabled', // For disabling
                            'class' => 'business_coming', // For disabling
                            'values' => array(
                                array(
                                    'id' => 'overlay_yes',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'overlay_no',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                ),
                            ),
                            //'desc' => $this->l('The data controller is the person or legal entity (company or organization) that is responsible for this domain.')
                        ),
                        array(
                            'type' => 'uc_privacy_button_visible',
                            'label' => $this->l('Privacy Trigger'),
                            'name' => 'privacyButtonIsVisible',
                            'desc' => $this->l('The Privacy Button is a floating Icon on your website. The Privacy Link is a static Link. Both allow your Customers to customize their Privacy Settings.'),
                            'class' => 'uc_radio',
                            'values' => array(
                                array('id' => 'privacybuttonvisible_1', 'label' => $this->l('Privacy button'), 'value' => '1',
                                    'image_url' => $this->_path . 'views/img/privacytrigger_privacybutton.svg', 'p' => ''),
                                array('id' => 'privacybuttonvisible_0', 'label' => $this->l('Privacy link'), 'value' => '0',
                                    'image_url' => $this->_path . 'views/img/privacytrigger_privacylink.svg',
                                    'p' => $this->l('Include the following link into the website in order for your users to be able to change their Privacy Settings: '),
                                    'p2' => '<a href="#" onClick="UC_UI.showSecondLayer();">' . $this->l('Privacy Settings') . '</a>')
                            ),
                        ),
                        array(
                            'type' => 'uc_privacy_button',
                            'label' => $this->l('Privacy Button Icon'),
                            'name' => 'buttonPrivacyOpenIconUrl',
                            'class' => 'uc_radio uc_privacy_button show_for_privacybuttonvisible_1',
                            'values' => array(
                                array('id' => 'privacybutton_fingerprint', 'label' => $this->l('Fingerprint'), 'value' => 'https://img.usercentrics.eu/misc/icon-fingerprint@2X.png', 'image_url' => $this->_path . 'views/img/privacybutton_fingerprint.png'),
                                array('id' => 'privacybutton_settings', 'label' => $this->l('Settings'), 'value' => 'https://img.usercentrics.eu/misc/icon-settings-2X.png', 'image_url' => $this->_path . 'views/img/privacybutton_settings.png'),
                                array('id' => 'privacybutton_security', 'label' => $this->l('Security'), 'value' => 'https://img.usercentrics.eu/misc/icon-shield-2X.png', 'image_url' => $this->_path . 'views/img/privacybutton_security.png'),
                            ),
                            'desc' => $this->l('The Privacy Button gives your users the opportunity to manage their opt-in preferences, view their opt-in history and get detailed information about specific Data Processing Services.')
                        ),
                        array(
                            'type' => 'radio',
                            'label' => $this->l('Privacy Button Position'),
                            'name' => 'buttonDisplayLocation',
                            'class' => 'show_for_privacybuttonvisible_1',
                            'values' => array(
                                array('id' => 'buttonDisplayLocation_left', 'label' => $this->l('Left'), 'value' => 'bl'),
                                array('id' => 'buttonDisplayLocation_right', 'label' => $this->l('Right'), 'value' => 'br'),
                            ),
                            'desc' => $this->l('You can choose in which position the Privacy Button will appear on your website.')
                        ),
                        array(
                            'type' => 'uc_technologies',
                            'label' => $this->l('Third-party Technologies'),
                            'name' => 'uc_technologies',
                            'desc_before' => $this->l('Below you can see default categories and the option to create new categories. Click on the relevant category to configure third-party technologies.'),
                            'categories' => $setting['en']['categories'],
                            'desc_after_templates_category' => $this->l('Usercentrics automatically blocks all major third-party technologies. You can see the full list of supported technologies here: ') . '<a href="https://docs.usercentrics.com/#/smart-data-protector?id=currently-supported-technologies">https://docs.usercentrics.com/#/smart-data-protector?id=currently-supported-technologies</a><br>' .
                                $this->l('If you have configured technologies not in the list, please follow the documentation.: ') .
                                '<a href="https://docs.usercentrics.com/#/direct-implementation-guide">https://docs.usercentrics.com/#/direct-implementation-guide</a><br>' .
                                $this->l(' For more details, contact: ') .
                                '<a href="mailt:usercentrics@silbersaiten.de">usercentrics@silbersaiten.de</a><br>',
                            'crawler_button_label' => $this->l('Rerun crawler'),
                            'addcategory_button_label' => $this->l('Add category'),
                            'addtechnology_button_label' => $this->l('Add technology'),
                            'show_crawler_button' => (isset($package_data['limit_crawler']) && ($package_data['limit_crawler'] == -1)) ? true : false,
                            'category_name_label' => $this->l('Name'),
                            'category_description_label' => $this->l('Description'),
                            'open_uc_newcategory_form_button_label' => $this->l('New category'),
                            'close_uc_newcategory_form_button_label' => $this->l('Close'),
                            'category_isessential_label' => $this->l('Is essential?'),
                            'newcategory_form_label' => $this->l('Add new category'),
                            'category_disable_autotrans_label' => $this->l('Disable auto translation?'),
                            'category_disable_autotrans_help' => $this->l('If it\'s disabled then Name and Description will be auto-translated and set for every language'),
                            'category_is_essential_label' => $this->l('Essential - vendors inside the essential category are automatically activated'),
                            'material_design_icons' => version_compare(_PS_VERSION_, '1.7.0.0') >= 0 ? 1 : 0
                        ),
                        array(
                            'name' => 'USERCENTRICS_LOG',
                            'type' => 'switch',
                            'label' => $this->l('Enable Log'),
                            'desc' => $this->l('Logs of actions in') . ' ' . DIRECTORY_SEPARATOR . 'logs ' .
                                $this->l('directory. Please notice: logs information can take a lot of disk space after a time.'),
                            'class' => 't',
                            'is_bool' => true,
                            'disabled' => false,
                            'values' => array(
                                array(
                                    'id' => 'log_yes',
                                    'value' => 1,
                                    'label' => $this->l('Yes')
                                ),
                                array(
                                    'id' => 'log_no',
                                    'value' => 0,
                                    'label' => $this->l('No')
                                ),
                            ),
                        ),
                        array(
                            'type' => 'free',
                            'name' => 'log_information',
                        )
                    ),
                    'submit' => array(
                        'title' => $this->l('Update'),
                        'name' => 'saveUCSetting',
                    )
                )
            );

            $values = array();
            $values['setting_id'] = $setting_id;

            foreach (array_keys($this->getUCLanguages()) as $lang) {
                if (!isset($setting[$lang])) {
                    continue;
                }
                $language_setting = $setting[$lang];
                $values['imprintUrl'][$lang] = Tools::getValue('imprintUrl', $language_setting['imprintUrl']);
                $values['privacyPolicyUrl'][$lang] = Tools::getValue('privacyPolicyUrl', $language_setting['privacyPolicyUrl']);
                $values['bannerMessage'][$lang] = Tools::getValue('bannerMessage', $language_setting['bannerMessage']);
            }
            $language_setting = $setting['en'];

            //
            $values['dataController'] = Tools::getValue('dataController', $language_setting['dataController']);
            $values['color'] = '#' . str_replace('#', '', Tools::getValue('color', $language_setting['customization']['color']['primary']));
            $values['logoUrl'] = Tools::getValue('logoUrl', $language_setting['customization']['logoUrl']);
            $values['fontfamily'] = Tools::getValue('fontfamily', $language_setting['customization']['font']['family']);
            $values['fontsize'] = Tools::getValue('fontsize', $language_setting['customization']['font']['size']);
            $values['firstlayervariant'] = Tools::getValue('firstlayervariant', $language_setting['firstLayer']['variant']);
            $values['secondlayervariant'] = Tools::getValue('secondlayervariant', $language_setting['secondLayer']['variant']);
            $values['layeroverlay'] = Tools::getValue('layeroverlay', $language_setting['secondLayer']['isOverlayEnabled']);
            $values['privacyButtonIsVisible'] = Tools::getValue('privacyButtonIsVisible', $language_setting['privacyButtonIsVisible']);
            $values['buttonPrivacyOpenIconUrl'] = Tools::getValue('buttonPrivacyOpenIconUrl', $language_setting['buttonPrivacyOpenIconUrl']);
            $values['buttonDisplayLocation'] = Tools::getValue('buttonDisplayLocation', $language_setting['buttonDisplayLocation']);
            $values['USERCENTRICS_LOG'] = Tools::getValue('USERCENTRICS_LOG', Configuration::get('USERCENTRICS_LOG'));
            $values['log_information'] = $this->displayLogInformation();

            $helper->fields_value = $values;
            //$html .= '<pre>'.print_r($setting['en']['categories'], true).'</pre>';
            //$html .= '<pre>'.print_r($setting['en']['consentTemplates'], true).'</pre>';

            $html .= $helper->generateForm($fields_form);
        } else {
            $html_errors = '';
            foreach ($this->api->errors as $error) {
                $html_errors .= parent::displayError($error);
            }
            $this->smarty->assign(array(
                'errors' => $html_errors
            ));
        }
        return $html;
    }

    private function getFontFamilies()
    {
        return array(
            'helvetica' => 'helvetica',
            'verdana' => 'verdana',
            'georgia' => 'georgia',
            'arial' => 'arial',
            'BlinkMacSystemFont,-apple-system,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,Helvetica,Arial,sans-serif' => 'BlinkMacSystemFont,-apple-system,Segoe UI,Roboto,Oxygen-Sans,Ubuntu,Cantarell,Fira Sans,Droid Sans,Helvetica Neue,Helvetica,Arial,sans-serif'
        );
    }

    private function getFontSizes()
    {
        return array(
            '12' => '12',
            '14' => '14',
            '16' => '16',
            '18' => '18'
        );
    }

    public function displayPageSettings()
    {
        $html = $this->displayGeneralSettings();

        if (Configuration::get('USERCENTRICS_SETTING_ID') != '') {
            $html .= $this->displayUCSettingSettings(Configuration::get('USERCENTRICS_SETTING_ID'));
        }

        return $html;
    }

    private function displayLogInformation()
    {
        $this->smarty->assign(array(
            'general_log_file_path' => AdminController::$currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&log_file=general',
        ));

        return $this->display(__FILE__, 'views/templates/admin/log_information.tpl');
    }

    private function displayPasswordChange()
    {
        $reg_link = isset($this->register_link[$this->context->language->iso_code]) ? $this->register_link[$this->context->language->iso_code] : $this->register_link['en'];
        $this->smarty->assign(array(
            'password_change_link' => $this->password_link,
            'register_link' => $reg_link
        ));

        return $this->display(__FILE__, 'views/templates/admin/password_change.tpl');
    }

    public function displayPageInformation()
    {
        $iso_code = $this->context->language->iso_code == 'de' ? 'de' : 'en';
        $this->smarty->assign(array(
            'external_link' => 'https://addons.prestashop.com/' . $iso_code . '/contact-us?id_product=50763',
            '_path' => $this->_path,
            'displayName' => $this->displayName,
            'author' => $this->author,
            'description' => $this->description,
            'register_link' => (isset($this->register_link[$this->context->language->iso_code])) ? $this->register_link[$this->context->language->iso_code] : $this->register_link['en']
        ));

        return $this->display(__FILE__, 'views/templates/admin/information.tpl');
    }

    private function postProcess()
    {
        switch (Tools::getValue('m')) {
            case 1:
                $this->_confirmations[] = $this->l('Settings has been updated successfully');
                break;
            case 2:
                $this->module_post_errors[] = $this->l('No log data');
                break;
            case 3:
                $this->_confirmations[] = $this->l('Setting has been deleted');
                break;
            case 4:
                $this->_confirmations[] = $this->l('Language has been added');
                break;
        }

        if (Tools::getIsset('ajax')) {
            if (Tools::getValue('action') == 'getConsentTemplates') {
                if (($res = $this->api->getConsentTemplates(Tools::getValue('q'))) !== false) {
                    die(Tools::jsonEncode(array_values($res['consentTemplates'])));
                }
            } elseif (Tools::getValue('action') == 'addVendor') {
                if (($res = $this->api->addVendor(Configuration::get('USERCENTRICS_SETTING_ID'), Tools::getValue('template_id'), Tools::getValue('category_slug'))) !== false) {
                    die(Tools::jsonEncode(array('success' => true, 'message' => $this->l('Vendor has been added'))));
                } else {
                    die(Tools::jsonEncode(array('success' => false, 'message' => $this->l('Vendor adding failed'))));
                }
            } elseif (Tools::getValue('action') == 'deleteVendor') {
                if (($res = $this->api->deleteVendor(Configuration::get('USERCENTRICS_SETTING_ID'), Tools::getValue('template_id'))) !== false) {
                    die(Tools::jsonEncode(array('success' => true, 'message' => $this->l('Vendor has been removed'))));
                } else {
                    die(Tools::jsonEncode(array('success' => false, 'message' => $this->l('Vendor removing failed'))));
                }
            } elseif (Tools::getValue('action') == 'addCategory') {
                if (($la = $this->api->getSettingLanguagesAvailable(Configuration::get('USERCENTRICS_SETTING_ID'))) !== false) {
                    $cat_name = array();
                    $cat_description = array();
                    $cat_disable_autotrans = array();

                    foreach ($la['languagesAvailable'] as $uc_lang) {
                        if (Tools::getValue('catdisable_autotrans_' . $uc_lang) != 'true') {
                            if (trim(Tools::getValue('catname_' . $uc_lang)) == '') {
                                die(Tools::jsonEncode(array('success' => false, 'message' => str_replace('[lang]', $uc_lang, $this->l('Category name is empty for "[lang]" language')))));
                            }
                            if (trim(Tools::getValue('catdescription_' . $uc_lang)) == '') {
                                die(Tools::jsonEncode(array('success' => false, 'message' => str_replace('[lang]', $uc_lang, $this->l('Category description is empty for "[lang]" language')))));
                            }
                        }
                        $cat_name[$uc_lang] = trim(Tools::getValue('catname_' . $uc_lang));
                        $cat_description[$uc_lang] = trim(Tools::getValue('catdescription_' . $uc_lang));
                        $cat_disable_autotrans[$uc_lang] = trim(Tools::getValue('catdisable_autotrans_' . $uc_lang));
                    }
                    $category_slug = Tools::passwdGen(10);
                    if (($res = $this->api->addCategory(Configuration::get('USERCENTRICS_SETTING_ID'), $category_slug, Tools::getValue('catisessential'), $cat_name, $cat_description, $cat_disable_autotrans)) !== false) {
                        die(Tools::jsonEncode(array('success' => true, 'slug' => $category_slug, 'message' => $this->l('Category has been added'))));
                    } else {
                        die(Tools::jsonEncode(array('success' => false, 'message' => $this->l('Category adding failed'))));
                    }
                } else {
                    die(Tools::jsonEncode(array('success' => false, 'message' => $this->l('List of avaialable languages is invalid'))));
                }
            } elseif (Tools::getValue('action') == 'deleteCategory') {
                if (($res = $this->api->deletecategory(Configuration::get('USERCENTRICS_SETTING_ID'), Tools::getValue('category_slug'))) !== false) {
                    die(Tools::jsonEncode(array('success' => true, 'message' => $this->l('Category has been removed'))));
                } else {
                    die(Tools::jsonEncode(array('success' => false, 'message' => $this->l('Category removing failed'))));
                }
            }
        }

        if (Tools::getIsset('log_file')) {
            if (in_array(Tools::getValue('log_file'), array('general', 'api'))) {
                $key = Tools::getValue('log_file');
                $file_path = dirname(__FILE__) . '/logs/log_' . $key . '.txt';
                if (file_exists($file_path)) {
                    header('Content-type: text/plain');
                    header('Content-Disposition: attachment; filename=' . $key . '.txt');
                    echo Tools::file_get_contents($file_path);
                    exit;
                }
            }
            Tools::redirectAdmin($this->getModuleUrl() . '&m=2');
        }

        if (Tools::getIsset('saveUCSetting')) {
            if (Tools::getValue('setting_id') == '') {
                $this->module_post_errors[] = $this->l('Setting id is empty');
            }
            if (($la = $this->api->getSettingLanguagesAvailable(Tools::getValue('setting_id'))) === false) {
                $this->module_post_errors[] = $this->l('Available languages is invalid');
            }
            if (!count($this->module_post_errors)) {
                $data_lang = array();
                foreach (array_keys($this->getUCLanguages($la['languagesAvailable'])) as $lang) {
                    $data_lang[$lang]['imprintUrl'] = Tools::getValue('imprintUrl_' . $lang);
                    $data_lang[$lang]['privacyPolicyUrl'] = Tools::getValue('privacyPolicyUrl_' . $lang);
                    $data_lang[$lang]['bannerMessage'] = Tools::getValue('bannerMessage_' . $lang);
                }
                $data = array(
                    'imprintUrl' => $data_lang['en']['imprintUrl'],
                    'privacyPolicyUrl' => $data_lang['en']['privacyPolicyUrl'],
                    'bannerMessage' => $data_lang['en']['bannerMessage'],
                    'color' => str_replace('#', '', Tools::getValue('color')),
                    'logoUrl' => Tools::getIsset('logoUrl') ? Tools::getValue('logoUrl') : '',
                    'fontfamily' => Tools::getIsset('fontfamily') ? Tools::getValue('fontfamily') : '',
                    'fontsize' => Tools::getIsset('fontsize') ? Tools::getValue('fontsize') : '',
                    'firstlayervariant' => Tools::getIsset('firstlayervariant') ? Tools::getValue('firstlayervariant') : '',
                    'firstlayeroverlay' => Tools::getIsset('layeroverlay') ? Tools::getValue('layeroverlay') : '',
                    'secondlayervariant' => Tools::getIsset('secondlayervariant') ? Tools::getValue('secondlayervariant') : '',
                    'secondlayeroverlay' => Tools::getIsset('layeroverlay') ? Tools::getValue('layeroverlay') : '',
                    'privacyButtonIsVisible' => Tools::getValue('privacyButtonIsVisible'),
                    'buttonPrivacyOpenIconUrl' => Tools::getValue('buttonPrivacyOpenIconUrl'),
                    'buttonDisplayLocation' => Tools::getValue('buttonDisplayLocation'),
                );

                if ($this->api->saveSetting(Tools::getValue('setting_id'), $data) !== false) {
                    foreach ($data_lang as $lang => $data) {
                        if ($lang != 'en') {
                            $this->api->saveLanguageSetting(Tools::getValue('setting_id'), $lang, $data_lang[$lang]);
                        }
                    }
                    $this->_confirmations[] = $this->l('Setting has been saved');
                } else {
                    if ($this->api->errors) {
                        foreach ($this->api->errors as $error) {
                            $this->module_post_errors[] = $error;
                        }
                    }
                }
            }
        }

        if (Tools::getIsset('saveSettings')) {
            $username = trim(Tools::getValue('USERCENTRICS_USERNAME'));
            $password = trim(Tools::getValue('USERCENTRICS_PASSWORD'));
            if ($username == '') {
                $this->module_post_errors[] = $this->l('Username is empty');
            }

            if (!count($this->module_post_errors)) {
                if ($password == '') {
                    $password = Configuration::get('USERCENTRICS_PASSWORD');
                }
                $res = $this->api->authenticate($username, $password, $this->getShopDomain(), $this->getShopUrl());
                if ($res === false) {
                    foreach ($this->api->errors as $error) {
                        $this->module_post_errors[] = $error;
                    }
                } else {
                    Configuration::updateValue('USERCENTRICS_USERNAME', Tools::getValue('USERCENTRICS_USERNAME'));
                    Configuration::updateValue('USERCENTRICS_PASSWORD', $password);
                    Configuration::updateValue('USERCENTRICS_SETTING_ID', $res);
                }
                if (!count($this->module_post_errors)) {
                    Configuration::updateValue('USERCENTRICS_LOG', (int)Tools::getValue('USERCENTRICS_LOG', 0));
                    Tools::redirectAdmin($this->getModuleUrl() . '&m=1');
                }
            }
        }

        if (Tools::getIsset('saveUCLanguageSetting')) {
            $languages_available = Tools::getValue('languagesAvailable');

            if (Tools::getValue('setting_id') == '') {
                $this->module_post_errors[] = $this->l('Setting id is empty');
            }
            if (!is_array($languages_available) || !count($languages_available)) {
                $this->module_post_errors[] = $this->l('Please select languages');
            }
            if (count($languages_available) > 0 && !in_array('en', $languages_available)) {
                $this->module_post_errors[] = $this->l('English language does not exist in list');
            }
            if (!count($this->module_post_errors)) {
                if ($this->api->saveAvailableLanguages(Tools::getValue('setting_id'), $languages_available) !== false) {
                    $this->_confirmations[] = $this->l('Languages have been saved');
                } else {
                    if ($this->api->errors) {
                        foreach ($this->api->errors as $error) {
                            $this->module_post_errors[] = $error;
                        }
                    }
                }
            }
        }

        $this->displayErrors();
        $this->displayConfirmations();
    }

    public function getShopDomain()
    {
        $shop_url = $this->getShopUrl();
        $parsed_url = parse_url($shop_url);
        return isset($parsed_url['host']) ? $parsed_url['host'] : '';
    }

    public function getShopUrl()
    {
        return $this->context->shop->getBaseURL();
    }

    private function displayErrors()
    {
        if (count($this->module_post_errors)) {
            foreach ($this->module_post_errors as $error) {
                $this->module_html .= parent::displayError($error);
            }
        }
    }

    private function displayConfirmations()
    {
        if (count($this->_confirmations)) {
            foreach ($this->_confirmations as $confirmation) {
                $this->module_html .= parent::displayConfirmation($confirmation);
            }
        }
    }

    public function getModuleUrl($params = false)
    {
        $url = 'index.php?controller=AdminModules&token=' .
            Tools::getAdminTokenLite('AdminModules', $this->context) . '&configure=' . $this->name .
            '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        if (is_array($params) && count($params)) {
            foreach ($params as $k => $v) {
                $url .= '&' . $k . '=' . $v;
            }
        }
        return $url;
    }

    public function getUCLanguages($available_languages = null)
    {
        $langs = array(
            'en' => $this->l('English'),
            'de' => $this->l('German'),
            'fr' => $this->l('French'),
            'sq' => $this->l('Albanian'),
            'ar' => $this->l('Arabic'),
            'bs' => $this->l('Bosnian'),
            'bg' => $this->l('Bulgarian'),
            'zh' => $this->l('Chinese simple'),
            'zh_tw' => $this->l('Chinese traditional'),
            'hr' => $this->l('Croatian'),
            'cs' => $this->l('Czech'),
            'da' => $this->l('Danish'),
            'nl' => $this->l('Dutch'),
            'et' => $this->l('Estonian'),
            'fi' => $this->l('Finnish'),
            'el' => $this->l('Greek'),
            'hu' => $this->l('Hungarian'),
            'is' => $this->l('Icelandic'),
            'it' => $this->l('Italian'),
            'ja' => $this->l('Japanese'),
            'ko' => $this->l('Korean'),
            'lv' => $this->l('Latvian'),
            'lt' => $this->l('Lithuanian'),
            'no' => $this->l('Norwegian'),
            'po' => $this->l('Polish'),
            'pt' => $this->l('Portuguese'),
            'ro' => $this->l('Romanian'),
            'ru' => $this->l('Russian'),
            'sr' => $this->l('Serbian'),
            'sk' => $this->l('Slovak'),
            'sl' => $this->l('Slovenian'),
            'es' => $this->l('Spanish'),
            'sv' => $this->l('Swedish'),
            'th' => $this->l('Thai'),
            'tr' => $this->l('Turkish'),
            'uk' => $this->l('Ukrainian'),
        );
        if ($available_languages !== null and is_array($available_languages)) {
            $avail = array();
            foreach ($available_languages as $lang_code) {
                $avail[$lang_code] = isset($langs[$lang_code]) ? $langs[$lang_code] : $lang_code;
            }
            return $avail;
        }
        return $langs;
    }


    private function getCMSPages($lang = 'en')
    {
        if ($lang == 'zh_tw') {
            $lang = 'tw';
        }

        $cms_pages = array();
        $id_lang = Language::getIdByIso($lang);
        if ($id_lang > 0) {
            $result = CMS::getCMSPages($id_lang, null, false, $this->context->shop->id);
            $cms_pages[] = array('value' => '', 'name' => $this->l('-- Please select a CMS page --'));

            foreach ($result as $row) {
                $cms_pages[] = array('value' => $this->getRelativeCMSLink($row['id_cms'], $id_lang, $this->context->shop->id), 'name' => $row['id_cms'] . ' ' . $row['meta_title']);
            }

            usort($cms_pages, function ($a, $b) {
                return $a['value'] > $b['value'];
            });
        }

        return $cms_pages;
    }

    private function getRelativeCMSLink($cms, $idLang, $idShop)
    {
        $link = $this->context->link->getCMSLink($cms, null, null, $idLang, $idShop);

        $ssl = (Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE'));
        $base = (($ssl) ? 'https://' . $this->context->shop->domain_ssl : 'http://' . $this->context->shop->domain);

        return str_replace($base, '', $link);
    }
}
