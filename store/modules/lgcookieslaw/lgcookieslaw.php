<?php
/**
 *  Please read the terms of the CLUF license attached to this module(cf "licences" folder)
 *
 * @author    Línea Gráfica E.C.E. S.L.
 * @copyright Lineagrafica.es - Línea Gráfica E.C.E. S.L. all rights reserved.
 * @license   https://www.lineagrafica.es/licenses/license_en.pdf
 *            https://www.lineagrafica.es/licenses/license_es.pdf
 *            https://www.lineagrafica.es/licenses/license_fr.pdf
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

class LGCookiesLaw extends Module
{
    public $bootstrap;

    public function __construct()
    {
        $this->name = 'lgcookieslaw';
        $this->tab = 'front_office_features';
        $this->version = '1.4.25';
        $this->author = 'Línea Gráfica';
        $this->need_instance = 0;
        $this->module_key = '56c109696b8e3185bc40d38d855f7332';
        $this->author_address = '0x30052019eD7528f284fd035BdA14B6eC3A4a1ffB';

        if (substr_count(_PS_VERSION_, '1.6') > 0) {
            $this->bootstrap = true;
        } else {
            $this->bootstrap = false;
        }

        parent::__construct();

        $this->displayName = $this->l('EU Cookie Law (Notification Banner + Cookie Blocker)');
        $this->description = $this->l('Display a cookie banner and block cookies before getting the user consent.');

        /* Backward compatibility */
        if (_PS_VERSION_ < '1.5') {
            require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
        }
    }

    public function install()
    {
        if (!parent::install()
            || !$this->registerHook('top')
            || !$this->registerHook('displayMobileTop')
            || !$this->registerHook('header')
            || !$this->registerHook('backofficeHeader')
            || !$this->registerHook('displayCustomerAccount')
            || !$this->registerHook('footer')
        ) {
            return false;
        }

        Configuration::updateValue('PS_LGCOOKIES_TIMELIFE', '31536000');
        Configuration::updateValue('PS_LGCOOKIES_NAME', '__lglaw');
        Configuration::updateValue('PS_LGCOOKIES_DIVCOLOR', '#707070');
        Configuration::updateValue('PS_LGCOOKIES_POSITION', '2');
        Configuration::updateValue('PS_LGCOOKIES_OPACITY', '0.7');
        Configuration::updateValue('PS_LGCOOKIES_TESTMODE', '1');
        Configuration::updateValue('PS_LGCOOKIES_RELOAD', '0');
        Configuration::updateValue('PS_LGCOOKIES_BLOCK', '0');
        Configuration::updateValue('PS_LGCOOKIES_HIDDEN', '0');

        Configuration::updateValue('PS_LGCOOKIES_SHADOWCOLOR', '#707070');
        Configuration::updateValue('PS_LGCOOKIES_FONTCOLOR', '#FFFFFF');
        Configuration::updateValue('PS_LGCOOKIES_CMS', '1');
        Configuration::updateValue('PS_LGCOOKIES_NAVIGATION_BTN', '5');
        Configuration::updateValue('PS_LGCOOKIES_CMS_TARGET', '1');
        Configuration::updateValue('PS_LGCOOKIES_POSITION', '2');
        Configuration::updateValue('PS_LGCOOKIES_SETTING_BUTTON', '0');
        Configuration::updateValue('PS_LGCOOKIES_SHOW_CLOSE', '1');
        Configuration::updateValue('PS_LGCOOKIES_BTN1_FONT_COLOR', '#FFFFFF');
        Configuration::updateValue('PS_LGCOOKIES_BTN1_BG_COLOR', '#8BC954');
        Configuration::updateValue('PS_LGCOOKIES_BTN2_FONT_COLOR', '#FFFFFF');
        Configuration::updateValue('PS_LGCOOKIES_BTN2_BG_COLOR', '#5BC0DE');
        Configuration::updateValue('PS_LGCOOKIES_THIRD_PARTIES', '0');
        Configuration::updateValue('PS_LGCOOKIES_HOOK', 'footer');

        Configuration::updateValue('PS_LGCOOKIES_NAVIGATION', '0');
        Configuration::updateValue('PS_LGCOOKIES_IPTESTMODE', ''.$_SERVER['REMOTE_ADDR'].'');
        Configuration::updateValue(
            'PS_LGCOOKIES_BOTS',
            'Teoma,alexa,froogle,Gigabot,inktomi,looksmart,URL_Spider_SQL,Firefly,NationalDirectory,'.
            'AskJeeves,TECNOSEEK,InfoSeek,WebFindBot,girafabot,crawler,www.galaxy.com,Googlebot,Scooter,'.
            'TechnoratiSnoop,Rankivabot,Mediapartners-Google, Sogouwebspider,WebAltaCrawler,TweetmemeBot,'.
            'Butterfly,Twitturls,Me.dium,Twiceler'
        );
        // db tables
        include(dirname(__FILE__) . '/sql/install.php');

        $this->saveCss();

        return true;
    }

    public function uninstall()
    {
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'lgcookieslaw`');
        Db::getInstance()->Execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'lgcookieslaw_lang`');
        return parent::uninstall();
    }

    private function cleanBots($bots)
    {
        $bots = str_replace(' ', '', $bots);
        return $bots;
    }

    private function getCMSList()
    {
        $cms = Db::getInstance()->ExecuteS(
            'SELECT * '.
            'FROM '._DB_PREFIX_.'cms_lang '.
            'WHERE id_lang = '.(int)(Configuration::get('PS_LANG_DEFAULT'))
        );
        return $cms;
    }

    private function isBot($agente)
    {
        $bots = Configuration::get('PS_LGCOOKIES_BOTS');
        $botlist = explode(',', $bots);
        foreach ($botlist as $bot) {
            if (strpos($agente, $bot) !== false) {
                return true;
            }
        }

        return false;
    }

    private function getModuleList()
    {
        $modules = Db::getInstance()->ExecuteS('SELECT * FROM '._DB_PREFIX_.'module');
        foreach ($modules as &$module) {
            $module['checked'] = (int)$this->checkModule($module['id_module']);
        }

        return $modules;
    }

    private function checkModule($id_module)
    {
        $checkmodule = Db::getInstance()->getValue(
            'SELECT id_module '.
            'FROM '._DB_PREFIX_.'lgcookieslaw '.
            'WHERE id_module = '.(int)$id_module
        );
        if ($checkmodule) {
            return true;
        } else {
            return false;
        }
    }

    private function getContentLang($id_lang, $field)
    {
        $content = Db::getInstance()->getValue(
            'SELECT '.$field.' '.
            'FROM '._DB_PREFIX_.'lgcookieslaw_lang '.
            'WHERE id_lang = '.(int)$id_lang
        );
        return $content;
    }

    private function formatBootstrap($text)
    {
        $text = str_replace('<fieldset>', '<div class="panel">', $text);
        $text = str_replace(
            '<fieldset style="background:#DFF2BF;color:#4F8A10;border:1px solid #4F8A10;">',
            '<div class="panel"  style="background:#DFF2BF;color:#4F8A10;border:1px solid #4F8A10;">',
            $text
        );
        $text = str_replace('</fieldset>', '</div>', $text);
        $text = str_replace('<legend>', '<h3>', $text);
        $text = str_replace('</legend>', '</h3>', $text);
        return $text;
    }

    public function installOverrides()
    {
        $path = _PS_MODULE_DIR_.$this->name.
            DIRECTORY_SEPARATOR.'override'.
            DIRECTORY_SEPARATOR.'classes'.
            DIRECTORY_SEPARATOR;
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            copy($path.'Hook1700.php', $path.'Hook.php');
        } elseif (version_compare(_PS_VERSION_, '1.6.0.10', '>')) {
            copy($path.'Hook16011.php', $path.'Hook.php');
        } elseif (version_compare(_PS_VERSION_, '1.6.0.5', '>')) {
            copy($path.'Hook16010.php', $path.'Hook.php');
        } else {
            copy($path.'Hook15.php', $path.'Hook.php');
        }
        return parent::installOverrides();
    }

    private function getP($template)
    {
        $iso_langs = array('es', 'en', 'fr', 'it', 'de');
        $current_iso_lang = $this->context->language->iso_code;
        $iso = (in_array($current_iso_lang, $iso_langs)) ? $current_iso_lang : 'en';

        $this->context->smarty->assign(
            array(
                'lgcookieslaw_iso' => $iso,
                'base_url' => _MODULE_DIR_. $this->name . DIRECTORY_SEPARATOR,
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_ . $this->name
            . DIRECTORY_SEPARATOR . 'views'
            . DIRECTORY_SEPARATOR . 'templates'
            . DIRECTORY_SEPARATOR . 'admin'
            . DIRECTORY_SEPARATOR . '_p_' . $template . '.tpl'
        );
    }


    private function warningA()
    {
        if (!file_exists(_PS_ROOT_DIR_.'/override/classes/Hook.php')) {
            $warningA = $this->displayError(
                $this->l('The Hook.php override is missing.').
                '&nbsp;'.$this->l('Please reset the module or copy the override manually on your FTP.')
            );
            return $warningA;
        }
    }

    private function warningB()
    {
        if ((int)Configuration::get('PS_DISABLE_OVERRIDES') > 0) {
            $tokenP = Tools::getAdminTokenLite('AdminPerformance');

            $w = $this->getLinkTag(
                'index.php?tab=AdminPerformance&token='.$tokenP,
                'here',
                '_blank'
            );

            $warningB = $this->displayError(
                $this->l('The overrides are currently disabled on your store.').
                '&nbsp;'.$this->l('Please change the configuration').
                '&nbsp;'.$w
            );
            return $warningB;
        }
    }

    private function warningC()
    {
        if ((int)Configuration::get('PS_DISABLE_NON_NATIVE_MODULE') > 0) {
            $tokenP = Tools::getAdminTokenLite('AdminPerformance');

            $w = $this->getLinkTag(
                'index.php?tab=AdminPerformance&token='.$tokenP,
                'here',
                '_blank'
            );

            $warningC = $this->displayError(
                $this->l('Non PrestaShop modules are currently disabled on your store.').
                '&nbsp;'.$this->l('Please change the configuration').
                '&nbsp;'.$w
            );
            return $warningC;
        }
    }

    private function warningD()
    {
        if ((int)Configuration::get('PS_LGCOOKIES_TESTMODE') > 0) {
            $warningD = $this->displayError(
                $this->l('The preview mode of the module is enabled.').
                '&nbsp;'.$this->l('Don\'t forget to disable it once you have finished configuring the banner.')
            );
            return $warningD;
        }
    }

    private function warningE()
    {
        if ((int)Configuration::get('PS_LGCOOKIES_NAVIGATION') > 0
            and (int)Configuration::get('PS_LGCOOKIES_NAVIGATION_BTN') > 1
        ) {
            $warningE = $this->displayError(
                $this->l('The mode "Accept cookies through navigation" should be enabled').
                '&nbsp;'.$this->l('only if the option "Banner without buttons" is selected.')
            );
            return $warningE;
        }
    }

    private function warningF()
    {
        if ((int)Configuration::get('PS_LGCOOKIES_NAVIGATION') == 0
            and (int)Configuration::get('PS_LGCOOKIES_NAVIGATION_BTN') == 1
        ) {
            $warningF = $this->displayError(
                $this->l('The option "Banner without buttons" should be selected').
                '&nbsp;'.$this->l('only if the mode "Accept cookies through navigation" is enabled.')
            );
            return $warningF;
        }
    }

    private function warningGDPRKeepBrowsing()
    {
        $w  = $this->l(
            'Due to the changes on Europen Union General Data Protection Rules that aplyes on 18th, May 2018, '
        );
        $w .= $this->l('If yuo enable this option, you will breaking Europen Union laws. ');
        $w .= $this->l('Please if your shop is on European Union Territory, disable this option. ');
        $w .= $this->l('We will not assume any damage, ');
        $w .= $this->l('or conflict caused directly or indirectly as a consequence of have this option enabled. ');
        $w .= $this->l('You can get more info in next link: ');
        if ((int)Configuration::get('PS_LGCOOKIES_NAVIGATION') == 1) {
            $languages = array(
                'BG', 'ES', 'CS', 'DA', 'DE', 'ET', 'EL', 'EN', 'FR', 'GA', 'HR', 'IT', 'LV', 'LT',
                'HU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SL', 'FI', 'SV',
            );

            $lang_iso = $this->context->language->iso_code;
            $lang = 'EN';
            if (in_array($lang_iso, $languages)) {
                $lang = $lang_iso;
            }

            $url = 'http://eur-lex.europa.eu/legal-content/'.$lang.'/TXT/HTML/?uri=CELEX:32016R0679&from=EN';

            $w .= $this->getLinkTag(
                $url,
                'European Union General Data Protection Rules law',
                '_blank',
                'European Union General Data Protection Rules law'
            );

            return $this->displayError($w);
        }
    }

    public function getLinkTag($href, $message, $target = null, $title = null)
    {
        $this->context->smarty->assign(
            array(
                'href'    => $href,
                'target'  => $target,
                'title'   => $title,
                'message' => $message,
            )
        );

        return $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->name.
            DIRECTORY_SEPARATOR.'views'.
            DIRECTORY_SEPARATOR.'templates'.
            DIRECTORY_SEPARATOR.'admin'.
            DIRECTORY_SEPARATOR.'message_link.tpl'
        );
    }

    public function getContent()
    {
        $this->postProcess();
        $this->context->controller->addJqueryPlugin('ui.tooltip', null, true);
        $this->fields_form = array();
        $this->fields_form[0]['form']['tabs'] = array(
            'config' => $this->l('General settings'),
            'banner' => $this->l('Banner settings'),
            'buttons' => $this->l('Button settings'),
            'modules' => $this->l('Modules blocked'),
        );
        $field_type = 'switch';

        $urll = $this->context->link->getModuleLink(
            $this->name,
            'disallow',
            array(
                'token' => md5(_COOKIE_KEY_.$this->name)
            ),
            true
        );
        $this->context->smarty->assign(
            array(
                'href'   => $urll,
                'target' => '_blank',
                'title'  => 'European Union General Data Protection Rules law',
                'message'  => $urll,
            )
        );

        $w = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->name.
            DIRECTORY_SEPARATOR.'views'.
            DIRECTORY_SEPARATOR.'templates'.
            DIRECTORY_SEPARATOR.'admin'.
            DIRECTORY_SEPARATOR.'message_link.tpl'
        );

        $t1 = $this->l('With this option your banner styles set initially as disabled:none then show by javascript. ');

        $banner_images = array(
            1 => $this->_path . 'views/img/en_banner_top.jpg',
            2 => $this->_path . 'views/img/en_banner_bottom.jpg',
            3 => $this->_path . 'views/img/en_banner_float.jpg',
        );
        $iso_code = $this->context->language->iso_code;
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_top.jpg')) {
            $banner_images[1] = $this->_path . 'views/img/' . $iso_code . '_banner_top.jpg';
        }
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_bottom.jpg')) {
            $banner_images[2] = $this->_path . 'views/img/' . $iso_code . '_banner_bottom.jpg';
        }
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_float.jpg')) {
            $banner_images[3] = $this->_path . 'views/img/' . $iso_code . '_banner_float.jpg';
        }
        $banner_buttons = array(
            0 => $this->_path . 'views/img/en_banner_button_normal.jpg',
            1 => $this->_path . 'views/img/en_banner_button_big.jpg',
        );

        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_button_normal.jpg')) {
            $banner_buttons[0] = $this->_path . 'views/img/' . $iso_code . '_banner_button_normal.jpg';
        }
        if (file_exists(_PS_MODULE_DIR_ . $this->name . '/views/img/' . $iso_code . '_banner_button_big.jpg')) {
            $banner_buttons[1] = $this->_path . 'views/img/' . $iso_code . '_banner_button_big.jpg';
        }
        $this->fields_form[0]['form']['input'] = array(
            array(
                'label' => $this->l('IMPORTANT:'),
                'tab' => 'config',
                'desc' =>
                    $this->l('Don´t forget to disable the preview mode once you have finished configuring the banner.'),
                'type' => 'free',
                'name' => 'important',
            ),
            array(
                'label' => $this->l('Disallow url'),
                'tab' => 'config',
                'desc' => $w.'&nbsp;'.
                    $this->l('This link will grant the right of revoke their consent to your customers.').
                    $this->l('You can paste this url on your CMS.').
                    $this->l('Ypur users will be able to clean all cookies except Prestashop ones.'),
                'type' => 'free',
                'name' => 'important',
            ),
            array(
                'type' => 'switch', //$field_type,
                'label' => $this->l('Enable by default third parties cookies'),
                'name' => 'PS_LGCOOKIES_THIRD_PARTIES',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('If this option is enabled, third parties cookies checkbox will be enabled by default.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_THIRD_PARTIES_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_THIRD_PARTIES_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch', //$field_type,
                'label' => $this->l('Add revoke consent button'),
                'name' => 'PS_LGCOOKIES_DISALLOW',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Enable this option to add a button on customers acount to revoke cookie consent.').
                    '&nbsp;'.$this->l('It will lcean all cookies except Prestashop ones,'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_DISALLOW_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_DISALLOW_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch', //$field_type,
                'label' => $this->l('Reload the page after accepting cookies'),
                'name' => 'PS_LGCOOKIES_RELOAD',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Enable this option if you wish reload the page after a customer accepts cookies.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_RELOAD_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_RELOAD_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Block site navigation'),
                'name' => 'PS_LGCOOKIES_BLOCK',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Enable this option if you wish to block your site navigation until the customers push the accept button on the banner.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_BLOCK_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_BLOCK_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch', //$field_type,
                'label' => $this->l('Preview mode:'),
                'name' => 'PS_LGCOOKIES_TESTMODE',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Enable this option to preview the cookie banner in your front-office').'&nbsp;'.
                    $this->l('without bothering your customers (when the preview mode is enabled,').'&nbsp;'.
                    $this->l('the banner doesn´t disappear, the module doesn´t block cookies').'&nbsp;'.
                    $this->l('and only the person using the IP below is able to see the cookie banner).'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_TESTMODE_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_TESTMODE_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'switch', //$field_type,
                'label' => $this->l('Cache modules compatibility'),
                'name' => 'PS_LGCOOKIES_HIDDEN',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $t1.
                    $this->l('This can be usefull if your site use some cache module. ').
                    $this->l('Do not active this option if your accept button hide the banner, ').
                    $this->l('this option may not comply with the law.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_HIDDEN_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_HIDDEN_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'ip',
                'label' => $this->l('IP  for the preview mode:'),
                'name' => 'PS_LGCOOKIES_IPTESTMODE',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Click on the button "Add IP" to be the only person').'&nbsp;'.
                    $this->l('able to see the banner (if the preview mode is enabled).'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Cookie lifetime (seconds):'),
                'name' => 'PS_LGCOOKIES_TIMELIFE',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Set the duration during which the user consent will be saved (1 year = 31536000s).'),
            ),
            array(
                'type' => 'hidden',
                'label' => $this->l('Cookie lifetime (seconds):'),
                'name' => 'PS_LGCOOKIES_SEL_TAB',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Set the duration during which the user consent will be saved (1 year = 31536000s).'),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Cookie name:'),
                'name' => 'PS_LGCOOKIES_NAME',
                'tab' => 'config',
                'required' => false,
                'desc' =>
                    $this->l('Choose the name of the cookie used by our module to remember user consent').
                    '&nbsp;'.$this->l('(don´t use any space).'),
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Hook position:'),
                'name' => 'PS_LGCOOKIES_HOOK',
                'tab' => 'config',
                'required' => false,
                'desc' => $this->l('Choose a different hook if you need. Useful for some themes where hook "top" not present.'),
                'options' => array(
                    'query' => array(
                        array('id' => 'top', 'name' => 'top'),
                        array('id' => 'footer', 'name' => 'footer'),
                    ),
                    'id' => 'id',
                    'name' => 'name',

                ),
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('SEO protection:'),
                'name' => 'PS_LGCOOKIES_BOTS',
                'tab' => 'config',
                'required' => false,
                'cols' => '10',
                'rows' => '5',
                'desc' =>
                    $this->l('The module will prevent the search engine bots above').'&nbsp;'.
                    $this->l('from seeing the cookie warning banner when they crawl your website.'),
            ),
            array(
                'type' => 'free',
                'tab' => 'config',
                'label' => ' ',
                'name' => 'help1',
            ),
            array(
                'type' => 'free',
                'tab' => 'config',
                'label' => ' ',
                'name' => 'help5',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Banner position:'),
                'name' => 'PS_LGCOOKIES_POSITION',
                'tab' => 'banner',
                'required' => false,
                'desc' => $this->l('Choose the position of the warning banner (top or bottom of the page).'),
                'options' => array(
                    'query' => array(
                        array('id' => '1', 'name' => $this->l('Top')),
                        array('id' => '2', 'name' => $this->l('Bottom')),
                        array('id' => '3', 'name' => $this->l('Floating')),
                    ),
                    'id' => 'id',
                    'name' => 'name',

                ),
            ),
            array(
                'type' => 'banner_type',
                'tab' => 'banner',
                'label' => ' ',
                'name' => 'PS_LGCOOKIES_BANNER_TYPE',
                'selected' => (int) Configuration::get('PS_LGCOOKIES_POSITION', 2),
                'images' => $banner_images
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Highlight settings button'),
                'name' => 'PS_LGCOOKIES_SETTING_BUTTON',
                'tab' => 'banner',
                'required' => false,
                'desc' =>
                    $this->l('Enable this option to highlight the customize setting button.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_SETTING_BUTTON_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_SETTING_BUTTON_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            array(
                'type' => 'banner_button_type',
                'tab' => 'banner',
                'label' => ' ',
                'name' => 'PS_LGCOOKIES_SETTING_BUTTON',
                'selected' => (int) Configuration::get('PS_LGCOOKIES_SETTING_BUTTON', 0),
                'images' => $banner_buttons
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Background color:'),
                'name' => 'PS_LGCOOKIES_DIVCOLOR',
                'tab' => 'banner',
                'required' => false,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Background opacity:'),
                'name' => 'PS_LGCOOKIES_OPACITY',
                'tab' => 'banner',
                'required' => false,
                'desc' => $this->l('Choose the opacity of the background color (1 is opaque, 0 is transparent).'),
                'options' => array(
                    'query' => array(
                        array('id' => '1', 'name' => '1'),
                        array('id' => '0.9', 'name' => '0.9'),
                        array('id' => '0.8', 'name' => '0.8'),
                        array('id' => '0.7', 'name' => '0.7'),
                        array('id' => '0.6', 'name' => '0.6'),
                        array('id' => '0.5', 'name' => '0.5'),
                        array('id' => '0.4', 'name' => '0.4'),
                        array('id' => '0.3', 'name' => '0.3'),
                        array('id' => '0.2', 'name' => '0.2'),
                        array('id' => '0.1', 'name' => '0.1'),
                        array('id' => '0', 'name' => '0'),
                    ),
                    'id' => 'id',
                    'name' => 'name',

                ),
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Shadow color:'),
                'name' => 'PS_LGCOOKIES_SHADOWCOLOR',
                'tab' => 'banner',
                'required' => false,
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Font color:'),
                'name' => 'PS_LGCOOKIES_FONTCOLOR',
                'tab' => 'banner',
                'required' => false,
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Banner message:'),
                'name' => 'content',
                'autoload_rte' => 'true',
                'lang' => 'true',
                'tab' => 'banner',
                'required' => false,
                'cols' => '10',
                'rows' => '5',
                'desc' =>
                    $this->l('Example: "Our webstore uses cookies to offer a better user experience').'&nbsp;'.
                    $this->l('and we recommend you to accept their use to fully enjoy your navigation."'),
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Required cookies block:'),
                'name' => 'required',
                'autoload_rte' => 'true',
                'lang' => 'true',
                'tab' => 'banner',
                'required' => false,
                'cols' => '10',
                'rows' => '5',
            ),
            array(
                'type' => 'textarea',
                'label' => $this->l('Additional cookies block:'),
                'name' => 'additional',
                'autoload_rte' => 'true',
                'lang' => 'true',
                'tab' => 'banner',
                'required' => false,
                'cols' => '10',
                'rows' => '5',
            ),
            array(
                'type' => 'free',
                'tab' => 'banner',
                'label' => ' ',
                'name' => 'help2',
            ),
            array(
                'type' => 'free',
                'tab' => 'banner',
                'label' => ' ',
                'name' => 'help5',
            ),
            /*
            array(
                'type' => 'select',
                'label' => $this->l('Button position:'),
                'name' => 'PS_LGCOOKIES_NAVIGATION_BTN',
                'tab' => 'buttons',
                'required' => false,
                'desc' =>
                $this->l('Choose the position of the "I accept" and "More information" buttons inside the banner.'),
                'class' => 't',
                'options' => array(
                    'query' => array(
                        array('id' => '1', 'name' => $this->l('Banner without buttons')),
                        array('id' => '2', 'name' => $this->l('Buttons above the text')),
                        array('id' => '3', 'name' => $this->l('Buttons below the text')),
                        array('id' => '4', 'name' => $this->l('Buttons to the left of the text')),
                        array('id' => '5', 'name' => $this->l('Buttons to the right of the text')),

                    ),
                    'id' => 'id',
                    'name' => 'name',
                ),
            ),
            array(
                'type' => $field_type,
                'label' => $this->l('Accept cookies through navigation:'),
                'name' => 'PS_LGCOOKIES_NAVIGATION',
                'tab' => 'buttons',
                'required' => false,
                'desc' =>
                    $this->l('Disable this option is you want the banner to disappear').'&nbsp;'.
                    $this->l('only when users click on the "I accept" button (banner with buttons).').'&nbsp;'.
                    $this->l('And enable this option is you want the banner to disappear automatically').'&nbsp;'.
                    $this->l('when users keep browsing your website (banner without buttons).').'&nbsp;'.
                    $this->l('This option does not comply with European data protection law.'),
                'class' => 't',
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_NAVIGATION_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_NAVIGATION_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            */
            array(
                'type' => 'text',
                'lang' => 'true',
                'label' => $this->l('Title of the button 1 "I accept":'),
                'name' => 'button1',
                'tab' => 'buttons',
                'required' => false,
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Button 1 background color:'),
                'name' => 'PS_LGCOOKIES_BTN1_BG_COLOR',
                'tab' => 'buttons',
                'required' => false,
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Button 1 font color:'),
                'name' => 'PS_LGCOOKIES_BTN1_FONT_COLOR',
                'tab' => 'buttons',
                'required' => false,
            ),
            array(
                'type' => 'text',
                'lang' => 'true',
                'label' => $this->l('Title of the button 2 "More information":'),
                'name' => 'button2',
                'tab' => 'buttons',
                'required' => false,
            ),
            /*
            array(
                'type' => 'color',
                'label' => $this->l('Button 2 background color:'),
                'name' => 'PS_LGCOOKIES_BTN2_BG_COLOR',
                'tab' => 'buttons',
                'required' => false,
            ),
            array(
                'type' => 'color',
                'label' => $this->l('Button 2 font color:'),
                'name' => 'PS_LGCOOKIES_BTN2_FONT_COLOR',
                'tab' => 'buttons',
                'required' => false,
            ),
            */
            array(
                'type' => 'select',
                'label' => $this->l('Link of the button 2 "More information":'),
                'name' => 'PS_LGCOOKIES_CMS',
                'tab' => 'buttons',
                'required' => false,
                'desc' =>
                    $this->l('When you click on the "More information" button,').'&nbsp;'.
                    $this->l('it will take you to CMS page you have selected.'),
                'options' => array(
                    'query' => CMSCore::getCMSPages($this->context->language->id),
                    'id' => 'id_cms',
                    'name' => 'meta_title',
                ),
            ),
            array(
                'type' => $field_type,
                'label' => $this->l('Open the link in a new window:'),
                'name' => 'PS_LGCOOKIES_CMS_TARGET',
                'tab' => 'buttons',
                'required' => false,
                'desc' =>
                    $this->l('When you click on the "More information" button,').'&nbsp;'.
                    $this->l('the CMS page will be opened in a new or the same window of your browser.'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_CMS_TARGET_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_CMS_TARGET_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            /*
            array(
                'type' => $field_type,
                'label' => $this->l('Button to close the banner:'),
                'name' => 'PS_LGCOOKIES_SHOW_CLOSE',
                'tab' => 'buttons',
                'required' => false,
                'desc' => $this->l('Display a button to close the cookies banner.'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'PS_LGCOOKIES_SHOW_CLOSE_on',
                        'value' => 1,
                        'label' => $this->l('Yes')
                    ),
                    array(
                        'id' => 'PS_LGCOOKIES_SHOW_CLOSE_off',
                        'value' => 0,
                        'label' => $this->l('No')
                    )
                ),
            ),
            */
            array(
                'type' => 'free',
                'tab' => 'buttons',
                'name' => 'help3',
                'label' => ' ',
            ),
            array(
                'type' => 'free',
                'tab' => 'buttons',
                'label' => ' ',
                'name' => 'help5',
            ),
            array(
                'type' => 'free',
                'label' => $this->l('Block cookies:'),
                'name' => 'PS_BANNER_LIST',
                'tab' => 'modules',
                'desc' =>
                    $this->l('Here is the list of all the modules installed on your store.').'&nbsp;'.
                    $this->l('Tick the modules that you want to disable until users give their consent.'),
            ),
            array(
                'type' => 'free',
                'tab' => 'modules',
                'label' => ' ',
                'name' => 'help4',
            ),
            array(
                'type' => 'free',
                'tab' => 'modules',
                'label' => ' ',
                'name' => 'help5',
            ),
        );
        $this->fields_form[0]['form']['submit'] = array(
            'title' => $this->l('Save'),
            'name' => 'submitForm',

        );
        $config_params = array();
        $config_params['tabs'] = $this->fields_form[0]['form']['tabs'];
        $form = new HelperForm($this);
        if (version_compare(_PS_VERSION_, '1.6.0', '<')) {
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/bootstrap.js');
            $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/admin15.js');
            $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/admin15.css');
            $ps15 = true;
        } else {
            $ps15 = false;
        }
        $form->tpl_vars = $config_params;
        $form->show_toolbar = true;
        $form->module = $this;
        $form->fields_value = $this->getConfigFormValues();
        $form->name_controller = 'lgcookieslaw';
        $form->identifier = $this->identifier;
        $form->token = Tools::getAdminTokenLite('AdminModules');
        $form->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $form->allow_employee_form_lang = $this->context->language->id;
        $languages = Language::getLanguages();
        $language_exists = false;
        foreach ($languages as &$lang) {
            $lang['is_default'] = (int)($lang['id_lang'] == $this->context->language->id);
            if ($lang['is_default']) {
                $language_exists = true;
            }
        }
        $form->default_form_language = $language_exists
            ? $this->context->language->id
            : (int)Configuration::get('PS_LANG_DEFAULT');


        $form->languages = $languages;
        $form->toolbar_scroll = true;
        $form->title = $this->displayName;
        $form->submit_action = 'submitForm';
        $form->toolbar_btn = array(
            'back' =>
                array(
                    'href' =>
                        AdminController::$currentIndex.'&configure='.$this->name.
                        '&token='.Tools::getAdminTokenLite('AdminModules'),
                    'desc' => $this->l('Back to the list')
                )
        );
        $params = array();
        $params['link'] = $this->context->link;
        $params['current_id_lang'] = $this->context->language->id;
        $params['ps15'] = $ps15;
        $params['ssl'] = (int)Configuration::get('PS_SSL_ENABLED_EVERYWHERE');
        $this->context->smarty->assign($params);
        $content =
            $this->context->smarty->fetch(
                _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.
                'templates'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'configure.tpl'
            );

        $advise = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.
            'templates'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'advise.tpl'
        );

        return
            $this->getP('top') .
            $advise.
            $this->warningA().
            $this->warningB().
            $this->warningC().
            $this->warningD().
            $this->warningE().
            $this->warningF().
            $this->warningGDPRKeepBrowsing().
            $content.
            $form->generateForm($this->fields_form).
            $this->getP('bottom');
    }

    public function postProcess()
    {
        if (Tools::getIsset('submitForm')) {
            $fields = array(
                'PS_LGCOOKIES_TESTMODE',
                'PS_LGCOOKIES_IPTESTMODE',
                'PS_LGCOOKIES_TIMELIFE',
                'PS_LGCOOKIES_NAME',
                'PS_LGCOOKIES_NAVIGATION',
                'PS_LGCOOKIES_BOTS',
                'PS_LGCOOKIES_CMS',
                'PS_LGCOOKIES_OPACITY',
                'PS_LGCOOKIES_DIVCOLOR',
                'PS_LGCOOKIES_SHADOWCOLOR',
                'PS_LGCOOKIES_FONTCOLOR',
                'PS_LGCOOKIES_NAVIGATION_BTN',
                'PS_LGCOOKIES_CMS_TARGET',
                'PS_LGCOOKIES_POSITION',
                'PS_LGCOOKIES_SETTING_BUTTON',
                'PS_LGCOOKIES_SHOW_CLOSE',
                'PS_LGCOOKIES_BTN1_FONT_COLOR',
                'PS_LGCOOKIES_BTN1_BG_COLOR',
                'PS_LGCOOKIES_BTN2_FONT_COLOR',
                'PS_LGCOOKIES_BTN2_BG_COLOR',
                'PS_LGCOOKIES_HIDDEN',
                'PS_LGCOOKIES_THIRD_PARTIES',
                'PS_LGCOOKIES_HOOK',
            );
            $res = true;
            foreach ($fields as $field) {
                $value = Tools::getValue($field, '');
                if (strpos($field, 'COLOR') !== false) {
                    $value = substr($value, 0, 1) == '#'
                        ? $value
                        : '#' . $value;
                }
                $res &= Configuration::updateValue($field, $value);
            }
            $res &= Configuration::updateValue(
                'PS_LGCOOKIES_DISALLOW',
                (int)Tools::getValue('PS_LGCOOKIES_DISALLOW', 0)
            );
            $res &= Configuration::updateValue(
                'PS_LGCOOKIES_RELOAD',
                (int)Tools::getValue('PS_LGCOOKIES_RELOAD', 0)
            );
            $res &= Configuration::updateValue(
                'PS_LGCOOKIES_BLOCK',
                (int)Tools::getValue('PS_LGCOOKIES_BLOCK', 0)
            );
            $res &= Configuration::updateValue(
                'PS_LGCOOKIES_SEL_TAB',
                (int)Tools::getValue('PS_LGCOOKIES_SEL_TAB', 0)
            );
            foreach (Language::getLanguages() as $lang) {
                Db::getInstance()->Execute(
                    'REPLACE INTO '._DB_PREFIX_.'lgcookieslaw_lang VALUES '.
                    '('.(int)$lang['id_lang'].', \''.pSQL(Tools::getValue('button1_'.(int)$lang['id_lang'])).'\', '.
                    '"'.pSQL(Tools::getValue('button2_'.(int)$lang['id_lang'])).'", '.
                    '"'.pSQL(Tools::getValue('content_'.(int)$lang['id_lang']), 'html').'", '.
                    '"'.pSQL(Tools::getValue('required_'.(int)$lang['id_lang']), 'html').'", '.
                    '"'.pSQL(Tools::getValue('additional_'.(int)$lang['id_lang']), 'html').'")'
                );
            }

            // module list update
            Db::getInstance()->Execute('TRUNCATE TABLE '._DB_PREFIX_.'lgcookieslaw');
            foreach ($this->getModuleList() as $modulos) {
                if (Tools::getIsset('module'.$modulos['id_module'])) {
                    Db::getInstance()->Execute(
                        'INSERT INTO '._DB_PREFIX_.'lgcookieslaw '.
                        'VALUES ('.pSQL($modulos['id_module']).')'
                    );
                }
            }

            $this->saveCss();
        }
    }

    public function saveCss()
    {
        if (Configuration::get('PS_LGCOOKIES_POSITION') == 1) {
            $position = 'top:0';
        } elseif (Configuration::get('PS_LGCOOKIES_POSITION') == 2) {
            $position = 'bottom:0';
        }
        list($r, $g, $b) = sscanf(Configuration::get('PS_LGCOOKIES_DIVCOLOR'), '#%02x%02x%02x');
        $bgcolor = $r.','.$g.','.$b.','.Configuration::get('PS_LGCOOKIES_OPACITY');
        $this->context->smarty->assign(array(
            'bgcolor'             => $bgcolor,
            'fontcolor'           => Configuration::get('PS_LGCOOKIES_FONTCOLOR'),
            'btn1_bgcolor'        => Configuration::get('PS_LGCOOKIES_BTN1_BG_COLOR'),
            'btn1_fontcolor'      => Configuration::get('PS_LGCOOKIES_BTN1_FONT_COLOR'),
            'btn2_bgcolor'        => Configuration::get('PS_LGCOOKIES_BTN2_BG_COLOR'),
            'btn2_fontcolor'      => Configuration::get('PS_LGCOOKIES_BTN2_FONT_COLOR'),
            'shadowcolor'         => Configuration::get('PS_LGCOOKIES_SHADOWCOLOR'),
            'opacity'             => 'opacity:' . Configuration::get('PS_LGCOOKIES_OPACITY'),
            'buttons_position'    => Configuration::get('PS_LGCOOKIES_NAVIGATION_BTN'),
            'show_close'          => Configuration::get('PS_LGCOOKIES_SHOW_CLOSE'),
            'path_module'         => _MODULE_DIR_ . $this->name,
            'nombre_cookie'       => Configuration::get('PS_LGCOOKIES_NAME'),
            'tiempo_cookie'       => Configuration::get('PS_LGCOOKIES_TIMELIFE'),
            'lgcookieslaw_reload' => Configuration::get('PS_LGCOOKIES_RELOAD'),
            'hidden'              => Configuration::get('PS_LGCOOKIES_HIDDEN'),
            'position'            => isset($position) ? $position : null,
        ));
        /*
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            $rendered_template = $this->display(__FILE__, '/views/templates/hook/cookieslaw_header_1_7.tpl');
        } elseif (version_compare(_PS_VERSION_, '1.6', '>')) {
            $rendered_template = $this->display(__FILE__, '/views/templates/hook/cookieslaw_header_1_6.tpl');
        } elseif (version_compare(_PS_VERSION_, '1.5', '>')) {
            $rendered_template = $this->display(__FILE__, '/views/templates/hook/cookieslaw_header_1_5.tpl');
        } else {
            $rendered_template = $this->display(__FILE__, '/views/templates/hook/cookieslaw_header.tpl');
        }
        */
        $rendered_template = $this->display(__FILE__, '/views/templates/hook/style.tpl');

        $path = _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.
            'css'.DIRECTORY_SEPARATOR.'lgcookieslaw.css';
        $fp = fopen($path, 'w');
        fwrite($fp, $rendered_template);
        fclose($fp);
    }

    public function getConfigFormValues()
    {
        $fields = array(
            'PS_LGCOOKIES_TESTMODE',
            'PS_LGCOOKIES_IPTESTMODE',
            'PS_LGCOOKIES_TIMELIFE',
            'PS_LGCOOKIES_NAME',
            'PS_LGCOOKIES_NAVIGATION',
            'PS_LGCOOKIES_NAVIGATION2',
            'PS_LGCOOKIES_BOTS',
            'PS_LGCOOKIES_SHADOWCOLOR',
            'PS_LGCOOKIES_FONTCOLOR',
            'PS_LGCOOKIES_CMS',
            'PS_LGCOOKIES_OPACITY',
            'PS_LGCOOKIES_DIVCOLOR',
            'PS_LGCOOKIES_NAVIGATION_BTN',
            'PS_LGCOOKIES_CMS_TARGET',
            'PS_LGCOOKIES_POSITION',
            'PS_LGCOOKIES_SETTING_BUTTON',
            'PS_LGCOOKIES_SHOW_CLOSE',
            'PS_LGCOOKIES_BTN1_FONT_COLOR',
            'PS_LGCOOKIES_BTN1_BG_COLOR',
            'PS_LGCOOKIES_BTN2_FONT_COLOR',
            'PS_LGCOOKIES_BTN2_BG_COLOR',
            'PS_LGCOOKIES_SEL_TAB',
            'PS_LGCOOKIES_DISALLOW',
            'PS_LGCOOKIES_RELOAD',
            'PS_LGCOOKIES_BLOCK',
            'PS_LGCOOKIES_HIDDEN',
            'PS_LGCOOKIES_THIRD_PARTIES',
            'PS_LGCOOKIES_HOOK',
        );
        $out = Configuration::getMultiple($fields);
        $fields_lang = array('button1', 'button2', 'content', 'required', 'additional');
        foreach ($fields_lang as $field) {
            foreach (Language::getLanguages() as $lang) {
                $out[$field][$lang['id_lang']] = $this->getContentLang($lang['id_lang'], $field);
            }
        }
        $out['PS_LGCOOKIES_SEL_TAB'] = (int)Configuration::get('PS_LGCOOKIES_SEL_TAB');
        $out['PS_LGCOOKIES_BANNER_TYPE'] = (int)Configuration::get('PS_LGCOOKIES_POSITION');

        $this->context->smarty->assign('module_list', $this->getModuleList());
        $out['PS_BANNER_LIST'] = $this->context->smarty->fetch(
            _PS_MODULE_DIR_.$this->name.DIRECTORY_SEPARATOR.'views'.DIRECTORY_SEPARATOR.
            'templates'.DIRECTORY_SEPARATOR.'admin'.DIRECTORY_SEPARATOR.'_configure'.DIRECTORY_SEPARATOR.
            'helpers'.DIRECTORY_SEPARATOR.'form'.DIRECTORY_SEPARATOR.'check_module_list.tpl'
        );

        $out['help1'] =
        '<a href="../modules/'.$this->name.'/readme/readme_'.$this->l('en').'.pdf#page=4" target="_blank">
        <img src="../modules/'.$this->name.'/views/img/info.png"> '.$this->l('Read this page for more information').
        '</a>';
        $out['help2'] =
        '<a href="../modules/'.$this->name.'/readme/readme_'.$this->l('en').'.pdf#page=6" target="_blank">
        <img src="../modules/'.$this->name.'/views/img/info.png"> '.$this->l('Read this page for more information').
        '</a>';
        $out['help3'] =
        '<a href="../modules/'.$this->name.'/readme/readme_'.$this->l('en').'.pdf#page=9" target="_blank">
        <img src="../modules/'.$this->name.'/views/img/info.png"> '.$this->l('Read this page for more information').
        '</a>';
        $out['help4'] =
        '<a href="../modules/'.$this->name.'/readme/readme_'.$this->l('en').'.pdf#page=13" target="_blank">
        <img src="../modules/'.$this->name.'/views/img/info.png"> '.$this->l('Read this page for more information').
        '</a>';
        $out['important'] = '';
        $out['help5'] =
        '<a href="../modules/'.$this->name.'/readme/readme_'.$this->l('en').'.pdf#page=16" target="_blank">
        <img src="../modules/'.$this->name.'/views/img/info.png"> '.$this->l('FAQ: SEE THE COMMON ERRORS').
        '</a>';
        $out['important'] = '';
        return $out;
    }

    public function hookTop($params)
    {
        if (Configuration::get('PS_LGCOOKIES_HOOK') == 'top') {
            return $this->renderHook($params);
        }
    }

    public function hookdisplayMobileTop($params)
    {
        return $this->hookTop($params);
    }

    public function hookFooter($params)
    {
        if (Configuration::get('PS_LGCOOKIES_HOOK') == 'footer') {
            return $this->renderHook($params);
        }
    }

    public function hookDisplayFooterAfter($params)
    {
        return $this->hookFooter($params);
    }

    public function hookDisplayFooterBefore($params)
    {
        return $this->hookFooter($params);
    }

    public function renderHook($params)
    {
        $link = new Link();
        $this->context->smarty->assign(array(
            'cookie_message' => $this->getContentLang($this->context->cookie->id_lang, 'content'),
            'cookie_required' => $this->getContentLang($this->context->cookie->id_lang, 'required'),
            'cookie_additional' => $this->getContentLang($this->context->cookie->id_lang, 'additional'),
            'button1' => $this->getContentLang($this->context->cookie->id_lang, 'button1'),
            'button2' => $this->getContentLang($this->context->cookie->id_lang, 'button2'),
            'cms_link' => $link->getCMSLink(Configuration::get('PS_LGCOOKIES_CMS')),
            'cms_target' => Configuration::get('PS_LGCOOKIES_CMS_TARGET'),
            'target' => Configuration::get('PS_LGCOOKIES_CMS'),
            'buttons_position' => Configuration::get('PS_LGCOOKIES_NAVIGATION_BTN'),
            'show_close' => Configuration::get('PS_LGCOOKIES_SHOW_CLOSE'),
            'hidden' => Configuration::get('PS_LGCOOKIES_HIDDEN'),
            'path_module' => _MODULE_DIR_ . $this->name,
            'third_paries' => Configuration::get('PS_LGCOOKIES_THIRD_PARTIES'),
            'lgcookieslaw_position'=> Configuration::get('PS_LGCOOKIES_POSITION'),
            'lgcookieslaw_setting_button'=> Configuration::get('PS_LGCOOKIES_SETTING_BUTTON'),
        ));

        if (Configuration::get('PS_LGCOOKIES_TESTMODE') == 1) {
            if (Configuration::get('PS_LGCOOKIES_IPTESTMODE') == $_SERVER['REMOTE_ADDR']) {
                return $this->display(__FILE__, '/views/templates/hook/cookieslaw.tpl');
            }
        } else {
            if (!$this->isBot($_SERVER['HTTP_USER_AGENT'])) {
                if (Tools::isSubmit('aceptocookies')) {
                    setcookie(
                        Configuration::get('PS_LGCOOKIES_NAME'),
                        '1',
                        time() + (int)Configuration::get('PS_LGCOOKIES_TIMELIFE'),
                        '/'
                    );
                    //Tools::redirect($_SERVER['REQUEST_URI']);
                    echo '<meta http-equiv="refresh" content="0; url=' . $_SERVER['REQUEST_URI'] . '" />';
                    die();
                }
                if (Configuration::get('PS_LGCOOKIES_NAVIGATION') == 1) {
                    if (!isset($_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')])) {
                        $url_actual = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $url_actual = "$_SERVER[REQUEST_SCHEME]://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        $url_actual = parse_url($url_actual);

                        if (!isset($_SERVER['HTTP_REFERER'])) {
                            $url_referer = $url_actual;
                        } else {
                            $url_referer = parse_url($_SERVER['HTTP_REFERER']);
                        }
                    }
                    if ($url_actual['host'] == $url_referer['host']) {
                        if ($url_actual['host'] == $url_referer['host'] &&
                            $url_actual['path'] != $url_referer['path']
                        ) {
                            if (!isset($_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')])) {
                                setcookie(
                                    Configuration::get('PS_LGCOOKIES_NAME'),
                                    '1',
                                    time() + (int)Configuration::get('PS_LGCOOKIES_TIMELIFE'),
                                    '/'
                                );
                            }
                        }
                    }

                    if (!isset($_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')])) {
                        return $this->display(__FILE__, '/views/templates/hook/cookieslaw.tpl');
                    }
                } else {
                    if (!isset($_COOKIE[Configuration::get('PS_LGCOOKIES_NAME')])) {
                        return $this->display(__FILE__, '/views/templates/hook/cookieslaw.tpl');
                    }
                }
            }
        }
    }

    public function hookBackOfficeHeader()
    {
        if ($this->context->controller instanceof AdminController
            && pSQL(Tools::getValue('configure')) == $this->name
        ) {
            $this->context->controller->addCSS($this->_path . '/views/css/publi/lgpubli.css');
        }
    }

    public function hookDisplayHeader($params)
    {
        $this->context->controller->addJqueryPlugin('fancybox');
        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/front.css');
        $this->context->controller->addCSS(_MODULE_DIR_.$this->name.'/views/css/lgcookieslaw.css');
        $this->context->controller->addJS(_MODULE_DIR_.$this->name.'/views/js/front.js');

        if (version_compare(_PS_VERSION_, '1.6.1.0', '>=')) {
            Media::addJsDef(
                array(
                    'lgcookieslaw_cookie_name' => Configuration::get('PS_LGCOOKIES_NAME'),
                    'lgcookieslaw_session_time' => Configuration::get('PS_LGCOOKIES_TIMELIFE'),
                    'lgcookieslaw_reload'=> (int) Configuration::get('PS_LGCOOKIES_RELOAD') == 1 ? true : false,
                    'lgcookieslaw_block'=> (int) Configuration::get('PS_LGCOOKIES_BLOCK') == 1 ? true : false,
                    'lgcookieslaw_position'=> Configuration::get('PS_LGCOOKIES_POSITION'),
                )
            );
        } else {
            $this->context->smarty->assign(
                array(
                    'lgcookieslaw_cookie_name' => Configuration::get('PS_LGCOOKIES_NAME'),
                    'lgcookieslaw_session_time' => Configuration::get('PS_LGCOOKIES_TIMELIFE'),
                    'lgcookieslaw_reload'=> Configuration::get('PS_LGCOOKIES_RELOAD'),
                    'lgcookieslaw_block'=> Configuration::get('PS_LGCOOKIES_BLOCK'),
                    'lgcookieslaw_position'=> Configuration::get('PS_LGCOOKIES_POSITION'),
                )
            );

            return $this->context->smarty->fetch(
                _PS_MODULE_DIR_ . $this->name
                . DIRECTORY_SEPARATOR . 'views'
                . DIRECTORY_SEPARATOR . 'templates'
                . DIRECTORY_SEPARATOR . 'front'
                . DIRECTORY_SEPARATOR . 'javascript.tpl'
            );
        }
    }

    public function hookDisplayCustomerAccount($params)
    {
        if (Configuration::get('PS_LGCOOKIES_DISALLOW')) {
            $version = '15';
            if (version_compare(_PS_VERSION_, '1.6.0', '>=') && version_compare(_PS_VERSION_, '1.7.0', '<')) {
                $version = '16';
            } elseif (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
                $version = '17';
            }
            $lgcookieslaw_image_path = $this->getPathUri();
            $lgcookieslaw_disallow_url = $this->context->link->getModuleLink(
                $this->name,
                'disallow',
                array(
                    'token' => md5(_COOKIE_KEY_.$this->name)
                ),
                true
            );
            $this->context->smarty->assign(
                array(
                    'lgcookieslaw_disallow_url' => $lgcookieslaw_disallow_url,
                    'lgcookieslaw_image_path'   => $lgcookieslaw_image_path
                        .'/views/img/account_button_icon_'.$version.'.png',
                )
            );
            return $this->display(__FILE__, 'views/templates/front/account_button_'.$version.'.tpl');
        }
    }
}
