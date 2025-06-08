<?php
/**
 * Finalmenu
 *
 * @author     Matej Berka
 * @copyright  2014 Matej
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.0
*/

if (!defined('_PS_VERSION_'))
    exit;

require_once(_PS_MODULE_DIR_ . 'finalmenu/classes/filesHandler.php');

class finalmenu extends Module
{
    /* variable holding default horizontal menu settings */
    public static $config_desktop_menu = array(
        'menu_layout_holder' => 'layout-1',
        'menu_background_color' => '#333333',
        'text_color' => '#ffffff',
        'icons_color' => '#ffffff',
        'sticky_menu' => '1',
        'menuAnimateIn' => 'zoomInUp',
        'menuAnimateOut' => 'zoomOutUp',
        'sticky_menu_transparency' => '1',
        'google_fonts' => '0',
        'menu_top_links_font' => '"Open Sans", sans-serif',
        'menu_top_links_font_url' => '',
        'menu_top_links_font_size' => '18',
        'menu_top_full_width' => 0,
        'menu_top_links_line_height' => '24',
        'tab_hover_background_color' => '#7caa3d',
        'links_separator_color' => '#4E4C4C',
        'foreground_hover_color' => '#ffffff',
        'custom_css' => '',
        'custom_js' => '',
    );

    /* variable holding default vertical menu settings */
    public static $config_vertical_menu = array(
        'menu_layout_holder' => 'layout-1',
        'menu_background_color' => '#333333',
        'text_color' => '#ffffff',
        'icons_color' => '#ffffff',
        'menuAnimateIn' => 'zoomInUp',
        'menuAnimateOut' => 'zoomOutUp',
        'google_fonts_vertical' => '0',
        'menu_top_links_font' => '"Open Sans", sans-serif',
        'menu_top_links_font_url' => '',
        'menu_top_links_font_size' => '18',
        'menu_top_links_line_height' => '24',
        'tab_hover_background_color' => '#7caa3d',
        'links_separator_color' => '#4E4C4C',
        'foreground_hover_color' => '#ffffff',
        'tab_wrapper_w_1200' => '900',
        'tab_wrapper_w_992' => '727',
        'tab_wrapper_w_768' => '562',
    );

    /* variable holding default mobile menu settings */
    public static $config_mobile_menu = array(
        'FINALm_bg_color' => '#333333',
        'FINALm_submenu_bg_color' => '#686868',
        'FINALm_icon_color' => '#ffffff',
        'FINALm_text_color' => '#ffffff',
        'FINALm_text_hover_color' => '#7caa3d',
        'FINALm_background_hover_color' => '#8A8A8A',
        'FINALm_google_fonts' => '0',
        'FINALm_links_font' => '"Open Sans", sans-serif',
        'FINALm_links_font_url' => '',
        'FINALm_links_font_size' => '18',
        'FINALm_links_line_height' => '50',
    );

    /* variable holding horizontal menu html */
    private $desktop_menu;
    /* variable holding mobile menu html */
    private $mobile_menu;
    /* variable holding vertical menu html */
    private $vertical_menu;
    /* shop id holder */
    private $shop_id;
    /* shop group id holder */
    private $shop_group_id;
    /* lang id holder */
    private $lang_id;

     /*
     * Name of the controller
     * Used to set item selected or not in top menu
     */
    private $page_name = '';

    /* Pattern for matching config values */
    private $pattern = '/^([A-Z_]*)[0-9]+/';

    public function __construct()
    {
        $this->name = 'finalmenu';
        $this->tab = 'front_office_features';
        $this->version = '1.2.4';
        $this->author = 'marpaweb.eu';
        $this->need_instance = 1;

        $this->bootstrap = false;

        if (Shop::isFeatureActive()) {
            Shop::addTableAssociation('desktop_menu_tabs', array('type' => 'shop'));
            Shop::addTableAssociation('vertical_menu_tabs', array('type' => 'shop'));
        }

        parent::__construct();

        $this->displayName = $this->l('Finalmenu');
        $this->description = $this->l('Finalmenu is powerful and easy to use menu for prestashop.');
        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);

        $this->shop_id = $this->context->shop->id;
        $this->shop_group_id = $this->context->shop->id_shop_group;
        $this->lang_id = $this->context->language->id;
    }

    /**
     * Menu installation method
     * @return boolean identifies whether installations ended correctly
     */
    public function install()
    {
        if (!parent::install() ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayTop') ||
            !$this->registerHook('displayLeftColumn') ||
            !$this->registerHook('displayRightColumn') ||
            !$this->registerHook('displayBackOfficeHeader') ||

            !$this->registerHook('actionObjectCategoryUpdateAfter') ||
            !$this->registerHook('actionObjectCategoryDeleteAfter') ||
            !$this->registerHook('actionObjectCategoryAddAfter') ||

            !$this->registerHook('actionObjectCmsUpdateAfter') ||
            !$this->registerHook('actionObjectCmsDeleteAfter') ||
            !$this->registerHook('actionObjectCmsAddAfter') ||

            !$this->registerHook('actionObjectSupplierUpdateAfter') ||
            !$this->registerHook('actionObjectSupplierDeleteAfter') ||
            !$this->registerHook('actionObjectSupplierAddAfter') ||

            !$this->registerHook('actionObjectManufacturerUpdateAfter') ||
            !$this->registerHook('actionObjectManufacturerDeleteAfter') ||
            !$this->registerHook('actionObjectManufacturerAddAfter') ||

            !$this->registerHook('actionObjectProductUpdateAfter') ||
            !$this->registerHook('actionObjectProductDeleteAfter') ||
            !$this->registerHook('actionObjectProductAddAfter') ||

            !$this->registerHook('categoryUpdate') ||
            !$this->registerHook('actionUpdateMenu') ||
            !$this->registerHook('actionShopDataDuplication') ||

            !$this->createAdminTab() ||
            !$this->installDB()
        )

            return false;

        require_once(dirname(__FILE__) . '/install-sql.php');

        return true;
    }

    /**
     * Uploads defualt values into DB
     * @return boolean confirms upload
     */
    private function installDB()
    {
        $shops = Shop::getShops();
        foreach ($shops as $key => $id_shop) {
            $id_shop = $id_shop['id_shop'];
            $id_shop_group = Shop::getGroupFromShop($id_shop);

            // horizontal menu
            if (!unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $id_shop_group, $id_shop))) {
                Configuration::updateValue('FINALm_DESKTOP_CONFIG', serialize(finalmenu::$config_desktop_menu), false, $id_shop_group, $id_shop);
            } else {
                $old_settings = unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $id_shop_group, $id_shop));
                $new_settings = finalmenu::$config_desktop_menu;
                foreach ($new_settings as $key => $value) {
                    if(array_key_exists($key, $old_settings))
                        $new_settings[$key] = $old_settings[$key];
                }
                Configuration::updateValue('FINALm_DESKTOP_CONFIG', serialize($new_settings), false, $id_shop_group, $id_shop);
            }

            // vertical menu
            if (!unserialize(Configuration::get('FINALm_VERTICAL_CONFIG', null, $id_shop_group, $id_shop))) {
                Configuration::updateValue('FINALm_VERTICAL_CONFIG', serialize(finalmenu::$config_vertical_menu), false, $id_shop_group, $id_shop);
            } else {
                $old_settings = unserialize(Configuration::get('FINALm_VERTICAL_CONFIG', null, $id_shop_group, $id_shop));
                $new_settings = finalmenu::$config_vertical_menu;
                foreach ($new_settings as $key => $value) {
                    if(array_key_exists($key, $old_settings))
                        $new_settings[$key] = $old_settings[$key];
                }
                Configuration::updateValue('FINALm_VERTICAL_CONFIG', serialize($new_settings), false, $id_shop_group, $id_shop);
            }

            // mobile menu
            if (!unserialize(Configuration::get('FINALm_MOBILE_CONFIG', null, $id_shop_group, $id_shop))) {
                Configuration::updateValue('FINALm_MOBILE_CONFIG', serialize(finalmenu::$config_mobile_menu), false, $id_shop_group, $id_shop);
            } else {
                $old_settings = unserialize(Configuration::get('FINALm_MOBILE_CONFIG', null, $id_shop_group, $id_shop));
                $new_settings = finalmenu::$config_mobile_menu;
                foreach ($new_settings as $key => $value) {
                    if(array_key_exists($key, $old_settings))
                        $new_settings[$key] = $old_settings[$key];
                }
                Configuration::updateValue('FINALm_MOBILE_CONFIG', serialize($new_settings), false, $id_shop_group, $id_shop);
            }

        }

        return true;
    }

    /**
     * Creates menu tab in backoffice dashboard
     * @return boolean confirms the creation
     */
    private function createAdminTab()
    {
        $langs = Language::getLanguages();
        $admin_tab = new Tab();
        $admin_tab->class_name = "AdminMenuSettings";
        $admin_tab->module = "finalmenu";
        $admin_tab->id_parent = 0;
        foreach ($langs as $l) {
            $admin_tab->name[$l['id_lang']] = $this->l('Finalmenu');
        }
        $admin_tab->save();

        return true;
    }

    /**
     * Uninstalls the module from prestashop. Leaves Data in Database
     * @return boolean confirms the uninstallation
     */
    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->uninstallTab()
        )

            return false;
        return true;
    }

    /**
     * Removes module tab from user backoffice dashboard
     * @return boolean confirms the uninstallation
     */
    private function uninstallTab()
    {
        $tab_id = Tab::getIdFromClassName("AdminMenuSettings");
        if ($tab_id) {
            $tab = new Tab($tab_id);
            $tab->delete();
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // HOOKS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Hookes custom css to edit menu tab look in back office
     */
    public function hookDisplayBackOfficeHeader()
    {
        $this->context->controller->addCSS(($this->_path) . 'css/admin/FINALmenu_tab_admin.css', 'all');
        $tab = Tools::getValue('tab', 0);
        $controller = Tools::getValue('controller', 0);

        if ($controller)
            if ($controller == 'AdminImportFast')
                return;

        if ($tab)
            if ($tab == 'AdminSelfUpgrade')
                return;
    }

    public function hookActionObjectCategoryAddAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectCategoryUpdateAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectCategoryDeleteAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectCmsUpdateAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectCmsDeleteAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectCmsAddAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectSupplierUpdateAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectSupplierDeleteAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectSupplierAddAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectManufacturerUpdateAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectManufacturerDeleteAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectManufacturerAddAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectProductUpdateAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectProductDeleteAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionObjectProductAddAfter($params)
    {
        $this->clearMenuCache();
    }

    public function hookCategoryUpdate($params)
    {
        $this->clearMenuCache();
    }

    public function hookActionUpdateMenu()
    {
        $this->clearMenuCache();
    }

    /**
     * Clears menu files, menu will be regenerated
     */
    private function clearMenuCache()
    {
        $this->_clearCache('top_menu.tpl', $this->getCacheId('top_menu'));
        $this->_clearCache('side_menu.tpl', $this->getCacheId('side_menu'));
    }

    /**
     * Adds necessary files in front office
     */
    public function hookHeader($params)
    {
        $this->context->controller->addJS(($this->_path).'js/front/FINALmenu_blocks_specific_'.$this->shop_id.'.js');
        $this->context->controller->addCSS(($this->_path) . 'css/front/FINALmenu_'.$this->shop_id.'.css');
        $this->context->controller->addCSS(($this->_path) . 'css/front/animate.css');
        $this->context->controller->addJS(($this->_path) . 'js/front/FINALmenu.js');
        $this->context->controller->addJqueryPlugin(array('bxslider'));
    }

    // Menu position hooks
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Displays menu in site top position
     * @return html menu code
     */
    public function hookDisplayTop($param)
    {
        if (!$this->isCached('top_menu.tpl', $this->getCacheId('top_menu'))) {

            $mob_menu_sttg = unserialize(Configuration::get('FINALm_MOBILE_CONFIG', null, $this->shop_group_id, $this->shop_id));
            $hrz_menu_sttg = unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $this->shop_group_id, $this->shop_id));

            $this->makeHorizontalMenu($hrz_menu_sttg);
            $this->makeMobileMenu();

            $menu = $this->desktop_menu;
            $menu .= '<div class="mobile_menu_wrapper"><div class="menu-place-holder"><i class="icon-reorder"></i></div>';
            $menu .= $this->mobile_menu;
            $menu .= '</div>';
            $this->smarty->assign('FINALmenu', $menu);
            $this->smarty->assign('wide', $hrz_menu_sttg['menu_top_full_width']);

            filesHandler::generateCSS($hrz_menu_sttg, $mob_menu_sttg, dirname(__FILE__).'/css/front/FINALmenu_'.$this->shop_id.'.css');
            filesHandler::generateCustomJS($hrz_menu_sttg, $this->context->getMobileDevice(), dirname(__FILE__).'/js/front/FINALmenu_blocks_specific_'.$this->shop_id.'.js');
        }

        return $this->display(__FILE__,'top_menu.tpl', $this->getCacheId('top_menu'));
    }

    /**
     * Displays menu in site nav position
     * @return html menu code
     */
    public function hookDisplayNav($params)
    {

        if (!$this->isCached('top_menu.tpl', $this->getCacheId('top_menu'))) {
            $mob_menu_sttg = unserialize(Configuration::get('FINALm_MOBILE_CONFIG', null, $this->shop_group_id, $this->shop_id));
            $hrz_menu_sttg = unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $this->shop_group_id, $this->shop_id));

            $this->makeHorizontalMenu($hrz_menu_sttg);
            $this->makeMobileMenu();

            $menu = $this->desktop_menu;
            $menu .= '<div class="mobile_menu_wrapper"><div class="menu-place-holder"><i class="icon-reorder"></i></div>';
            $menu .= $this->mobile_menu;
            $menu .= '</div>';
            $this->smarty->assign('FINALmenu', $menu);
            $this->smarty->assign('wide', $hrz_menu_sttg['menu_top_full_width']);

            filesHandler::generateCSS($hrz_menu_sttg, $mob_menu_sttg, dirname(__FILE__).'/css/front/FINALmenu_'.$this->shop_id.'.css');
            filesHandler::generateCustomJS($hrz_menu_sttg, $this->context->getMobileDevice(), dirname(__FILE__).'/js/front/FINALmenu_blocks_specific_'.$this->shop_id.'.js');
        }

        return $this->display(__FILE__,'top_menu.tpl', $this->getCacheId('top_menu'));
    }

    /**
     * Displays vertical menu in left position
     * @return html menu code
     */
    public function hookLeftColumn($params)
    {
        if (!$this->isCached('side_menu.tpl', $this->getCacheId('side_menu'))) {

            $vrt_menu_sttg = unserialize(Configuration::get('FINALm_VERTICAL_CONFIG', null, $this->shop_group_id, $this->shop_id));

            $this->makeVerticalMenu($vrt_menu_sttg);
            $this->smarty->assign('FINALmenu_vertical', $this->vertical_menu);

            filesHandler::appendCSS($vrt_menu_sttg, dirname(__FILE__).'/css/front/FINALmenu_'.$this->shop_id.'.css');
            filesHandler::appendJS($vrt_menu_sttg, dirname(__FILE__).'/js/front/FINALmenu_blocks_specific_'.$this->shop_id.'.js');
        }

        return $this->display(__FILE__, 'side_menu.tpl', $this->getCacheId('side_menu'));
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // VERTICAL MENU
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method triggers vertical menu creation
     * @param  array $vrt_menu_sttg contains menu settings
     * @return html  vertical menu
     */
    private function makeVerticalMenu($vrt_menu_sttg)
    {
        $menu_items = $this->getDesktopMenuItems("vertical");
        $tab_index = 1;
        $this->vertical_menu .= '<ul id="FINALmenu-vertical-nav">';

        foreach ($menu_items as $tab) {
            $tab_specific_settings = json_decode($tab['settings'], true);

            if ($tab['type'] == 0) {
                $this->vertical_menu .= $this->generateDesktopAdvanceViewTab($tab_specific_settings, $tab, $vrt_menu_sttg, $tab_index, "vertical");
            } elseif ($tab['type'] == 1) {
                $this->vertical_menu .= $this->generateDesktopSimpleViewTab($tab_specific_settings, $tab, $vrt_menu_sttg, $tab_index, "vertical");
            }

            filesHandler::addTabCSS($tab_index, "float: left", $tab, $tab_specific_settings, "#FINALmenu-vertical", "vertical");
            $tab_index++;
        }
        $this->vertical_menu .= '</ul>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // HORIZONTAL MENU
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method triggers horizontal menu creation
     * @param  array $hrz_menu_sttg contains menu settings
     * @return html  horizontal menu
     */
    private function makeHorizontalMenu($hrz_menu_sttg)
    {
        $menu_items = $this->getDesktopMenuItems("desktop");
        $tab_index = 1;

        $this->desktop_menu .= '<ul id="FINALmenu-desktop-nav">';
        foreach ($menu_items as $tab) {
            $tab_specific_settings = json_decode($tab['settings'], true);

            if ($tab['type'] == 0)
                $this->desktop_menu .= $this->generateDesktopAdvanceViewTab($tab_specific_settings, $tab, $hrz_menu_sttg, $tab_index, "desktop");
            else if ($tab['type'] == 1)
                $this->desktop_menu .= $this->generateDesktopSimpleViewTab($tab_specific_settings, $tab, $hrz_menu_sttg, $tab_index, "desktop");

            $tab_float = ($tab['tab_position'] === 'left') ? 'float: left;' : 'float: right';
            filesHandler::addTabCSS($tab_index, $tab_float, $tab, $tab_specific_settings, "#FINALmenu", "desktop");
            $tab_index++;
        }
        $this->desktop_menu .= '</ul>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MOBILE MENU
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method triggers mobile menu creation
     */
    private function makeMobileMenu()
    {
        $menu_items = $this->getMobileMenuItems();
        $this->mobile_menu .= '<ul id="FINALmenu-mobile-nav">';
        $this->mobile_menu .= $this->generateMobileMenu($menu_items);
        $this->mobile_menu .= '</ul>';
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ADVANCE TAB CREATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method starts creation of desktop menu tab (advance menu tab)
     * @param  array  $tab_specific_settings tab specific settings
     * @param  array  $tab                   tab settings, includes tab specific settings
     * @param  array  $dsk_menu_sttg         desktop menu settings
     * @param  int    $tab_index             tab identifier
     * @param  String $menu_type             [vertical|horizontal]
     * @return html   generated code
     */
    private function generateDesktopAdvanceViewTab($tab_specific_settings, $tab, $dsk_menu_sttg, $tab_index, $menu_type)
    {
        $blocks = $tab_specific_settings['blocks'];
        $block_index = 1;
        $tab_image = trim($tab['tab_image']);
        $tab_icon = trim($tab['tab_icon']);
        $isDesktop = !strcmp($menu_type, "desktop");
        $posTabs = ($tab['tab_position'] === 'left') ? 'left-tabs' : 'right-tabs';

        if($dsk_menu_sttg['menu_layout_holder'] == 'layout-4')
            $icon = '';
        else
            $icon = ((!empty($tab_image)) ? "<img alt=\"{$tab['name']}\" title=\"{$tab['name']}\" src=\"{$tab['tab_image']}\">" : ((!empty($tab_icon)) ? "<i class=\"{$tab['tab_icon']}\"></i>" : '' ));

        $html = "<li class=\"FINALmenu-advance-tab {$posTabs}\" id=\"FINALmenu-tab-{$menu_type}-{$tab_index}\"><div class=\"top-link-wrapper clearfix\">";

        if(!empty($tab['tab_note']))
            $html .= "<span class=\"tab-note\" style=\"background-color: {$tab['tab_note_bg_color']};\" >{$tab['tab_note']}</span>";

        // TAB TOP LINK
        if (!empty($tab['tab_link'])) {
            $target = (($tab['link_window'] == 1) ? '_blank' : '_self' );
            $layout = (($dsk_menu_sttg['menu_layout_holder'] != 'layout-3') ? $tab['name'] : '' );
            $html .= "<a target=\"{$target}\" href=\"{$tab['tab_link']}\"  class=\"top-level-link {$dsk_menu_sttg['menu_layout_holder']}\">{$icon}{$layout}</a>";
        } else {
            $layout = (($dsk_menu_sttg['menu_layout_holder'] != 'layout-3') ? $tab['name'] : '' );
            $html .= "<span class=\"top-level-link {$dsk_menu_sttg['menu_layout_holder']}\">{$icon}{$layout}</span>";
        }

        if (!empty($blocks)) {
            if($isDesktop)
                $html .= '<i class="icon-chevron-down show-items-icon"></i>';
            else
                $html .= '<i class="icon-chevron-right show-items-icon"></i>';
        }
        $html .= '</div>';

        // TAB SPECIFIC CSS
        $tab_position = ($tab['tab_position'] === 'left') ? 'left: 0px;' : 'right: 0px;';

        if($isDesktop)
            filesHandler::addAdvanceMenuTabCSS($tab_index, $tab_specific_settings, $tab_position, $this->lang_id, "#FINALmenu", $menu_type);
        else
            filesHandler::addAdvanceMenuTabCSS($tab_index, $tab_specific_settings, $tab_position, $this->lang_id, "#FINALmenu-vertical", $menu_type);

        // TAB CONTENT
        if (!empty($blocks)) {
            if (array_key_exists("order_index", current($blocks))) {
                 uasort($blocks, 'finalmenu::comparator');
            }
            if (!strcmp($menu_type, "vertical")) {
                $html .= "<div class='tw-w-1200 tw-w-992 tw-w-768 FINALmenu-tab-content animated'><div id='FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper' class='col-xs-{$tab_specific_settings['tab_wrapper_width']} clearfix'>";
                foreach ($blocks as $block) {
                    $html .= $this->generateBlock($block['type'], $block, $tab_index, $block_index, "#FINALmenu-vertical", $menu_type);

                    // generating blocks specific css
                    filesHandler::addAdvanceMenuBlockCSS($block, $tab_index, $block_index, "#FINALmenu-vertical", $menu_type);
                    $block_index++;
                }
                $html .= "</div></div>";
            } elseif (!strcmp($menu_type, "desktop")) {
                $html .= "<div id='FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper' class='FINALmenu-tab-content animated col-xs-{$tab_specific_settings['tab_wrapper_width']}'>";
                foreach ($blocks as $block) {
                    $html .= $this->generateBlock($block['type'], $block, $tab_index, $block_index, "#FINALmenu", $menu_type);

                    // generating blocks specific css
                    filesHandler::addAdvanceMenuBlockCSS($block, $tab_index, $block_index, "#FINALmenu", $menu_type);
                    $block_index++;
                }
                $html .= '</div>';
            }
        }
        $html .= '</li>';

        return $html;
    }

    /**
     * Blocks switcher, depending on type will select corect block generator and calls it's generate method
     * @param  String $type        Block type
     * @param  array  $block       Block values
     * @param  int    $tab_index   tab identifier
     * @param  int    $block_index block identifier
     * @param  String $menu_id     [#FINALmenu-vertical|#FINALmenu]
     * @param  String $menu_type   [horizontal|vertical]
     * @return html   generated html
     */
    private function generateBlock($type, $block, $tab_index, $block_index, $menu_id, $menu_type)
    {
        switch ($type) {
            case 'cms-pages':
                return $this->generateCMSCategoryBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'categories':
                return $this->generateCategoryBlock($type, $block, $tab_index, $block_index, $menu_id, $menu_type);
            case 'suppliers':
            case 'manufacturers':
                return $this->generateCarriageBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'products':
                return $this->generateProductBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'search-field':
                return $this->generateSearchBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'custom-image':
                return $this->generateCustomImageBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'custom-html':
                return $this->generateCustomHTMLBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'cms-page':
                return $this->generateCMSpageBlock($type, $block, $tab_index, $block_index, $menu_type);
            case 'custom-link':
                return $this->generateCustomLinkBlock($type, $block, $tab_index, $block_index, $menu_type);
            default:
                return '';
        }
    }

    private function generateCategoryBlock($type, $block, $tab_index, $block_index, $menu_id, $menu_type)
    {
        $html = "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'><ul>";

        if ($block['selected_view'] == 'list') {
            $html .= $this->generateListItems(explode(',', $block['selected']), $block['nmb_of_columns']);
        } else {
            $c_per_row = floor($block['nmb_of_columns']/$block['item_number_of_columns']);
            $c_per_row = ($c_per_row == 0) ? 1 : $c_per_row;
            $c_per_row_small = floor($block['nmb_of_columns']/($block['item_number_of_columns'] +  1));
            $c_per_row_small = ($c_per_row_small == 0) ? 1 : $c_per_row_small;

            filesHandler::addGripItemsCSS($block, dirname(__FILE__).'/css/front/FINALmenu_'.$this->shop_id.'.css', $tab_index, $block_index, $menu_id, $menu_type);

            if(!empty($block['grip_view']))
                $html .= $this->generateGripItems($block['grip_view'], $c_per_row, $c_per_row_small);
        }
        $html .= '</ul></div>';

        return $html;
    }

    private function generateCMSCategoryBlock($type, $block, $tab_index, $block_index, $menu_type)
    {

        $html = "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'><ul>";
        $html .= $this->generateListItems(explode(',', $block['selected']), $block['nmb_of_columns']);
        $html .= '</ul></div>';

        return $html;
    }

    private function generateCarriageBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        $block_specific_name = "{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}";
        if (strlen($block['selected'])) {

            $html .= "<div id='FINALmenu-{$block_specific_name}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";

            if ($block['block_view'] == 'multi') {
                $name = $this->l(ucfirst($type));
                $html .= "<ul><li class='first-level-item'><span>{$name}</span><ul>";
                $html .= $this->generateListItems(explode(',', $block['selected']), $block['nmb_of_columns']);
                $html .= '</ul></li></ul>';
            } elseif ($block['block_view'] == 'carousel') {
                $selected_carriages = $block['selected'];
                $selected_carriages = explode(',', $selected_carriages);
                $nmb_of_carriages_per_slide = $block['car_per_row'] * $block['nmb_of_rows'];
                $carriages_count = sizeof($selected_carriages);
                $nmb_of_slides = ceil($carriages_count / $nmb_of_carriages_per_slide);
                $index = 0;

                $html .= "<div id='FINALmenu-{$block_specific_name}-slider'>";
                for ($i = 0; $i < $nmb_of_slides; $i++) {
                    $html .= '<div class="slide">';
                    for ($j = 0; $j < $nmb_of_carriages_per_slide; $j++) {
                        if (($index) >= $carriages_count)
                            break;

                        if ($index % $block['car_per_row'] == 0)
                            $html .= '<span class="clearfix"></span>';

                        $html .= '<div class="image-wrapper" style="width: ' . (100 / $block['car_per_row']) . '%">';
                        $html .= $this->generateBoxedItem($selected_carriages[$index]);
                        $html .= '</div>';

                        $index++;
                    }
                    $html .= '</div>';
                }
                $html .= '</div>';
                $html .= "<script type='text/javascript'>
                         $(document).ready(function () {
                                var {$block_specific_name} = $('#FINALmenu-{$block_specific_name}-slider').bxSlider({
                                    useCSS: false,
                                    pager: false,
                                    responsive: true,
                                    nextText: '',
                                    prevText: ''
                                });
                                // this call method is necessary, without it you will get 0px slider width during the slider resize.
                                $('#FINALmenu-tab-{$menu_type}-{$tab_index}-{$block_index} .show-items-icon').click(function (e) { {$block_specific_name}.reloadSlider();
                                });
                                menuSliders.push({$block_specific_name});
                          });
                          </script>";
            }
            $html .= '</div>'; // end of block
        }

        return $html;
    }

    private function generateProductBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        $block_specific_name = "{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}";
        if (strlen($block['selected_products_IDs'])) {
            $selected_products_IDs = rtrim($block['selected_products_IDs'], '-');
            $selected_products_IDs = explode('-', $selected_products_IDs);
            $nmb_of_products_per_slide = $block['pro_per_row'] * $block['nmb_of_rows'];
            $products_count = sizeof($selected_products_IDs);

            $nmb_of_slides = ceil($products_count / $nmb_of_products_per_slide);
            $index = 0;
            $html .= "<div id='FINALmenu-{$block_specific_name}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'><div id='FINALmenu-{$block_specific_name}-slider'>";
            for ($i = 0; $i < $nmb_of_slides; $i++) {
                $html .= '<div class="slide">';
                for ($j = 0; $j < $nmb_of_products_per_slide; $j++) {
                    if (($index) >= $products_count)
                        break;

                    if ($index % $block['pro_per_row'] == 0)
                        $html .= '<span class="clearfix"></span>';

                    $product = new Product($selected_products_IDs[$index], false, $this->lang_id);
                    $id_product_cover = $product->getCover($selected_products_IDs[$index]);
                    $all_images = $product->getImages($selected_products_IDs[$index]);

                    $imageURL = Context::getContext()->link->getImageLink($product->link_rewrite, $id_product_cover['id_image'], 'home_default', $selected_products_IDs[$index]);
                    $productURL = Context::getContext()->link->getProductLink($product);

                    $width = (100 / $block['pro_per_row']);
                    $html .= "<div class='image-wrapper' style='width: {$width}%'><div class='image-view'><a href='{$productURL}' >";
                    $html .= '<img src="' . $imageURL . '" alt="' . $product->name . '" title="' . $product->name . '">';

                    if (array_key_exists(1, $all_images)) {
                        $sec_img_src = Context::getContext()->link->getImageLink($product->link_rewrite, $all_images[1]['id_image'], 'home_default', $selected_products_IDs[$index]);
                        $html .= "<img class='second-image' src='{$sec_img_src}' alt='{$product->name}' title='{$product->name}'>";
                    }
                    $html .= "</a></div><div class='clearfix'></div><p>{$product->name}</p></div>";
                    $index++;
                }
                $html .= '</div>';
            }
            $html .= '</div></div>';
            $html .= "<script type='text/javascript'>
                        $(document).ready(function () {
                        var {$block_specific_name} = $('#FINALmenu-{$block_specific_name}-slider').bxSlider({
                                useCSS: false,
                                pager: false,
                                responsive: true,
                                nextText: '',
                                prevText: ''
                            });
                            // this call method is necessary, without it you will get 0px slider width during the slider resize.
                            $('#FINALmenu-tab-{$menu_type}-{$tab_index}-{$block_index} .show-items-icon').click(function (e) { {$block_specific_name}.reloadSlider();
                            });
                            menuSliders.push({$block_specific_name});
                        });
                    </script>";
        }

        return $html;
    }

    private function generateSearchBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";
        $html .= ' <form id="search_query_menu" action="' . Context::getContext()->link->getPageLink('search') . '" method="get" style="text-align: '.$block['position'].';">
                        <p>
                            <span class="search-wrapper">
                                <button type="submit" name="submit_search" class="button-search">
                                    <span>search</span>
                                </button>
                                <input type="hidden" value="position" name="orderby"/>
                                <input type="hidden" value="desc" name="orderway"/>
                                <input type="text" class="search_query_menu" name="search_query" placeholder="' . $this->l('Search') . '"/>
                                <input type="hidden" name="controller" value="search" />
                            </span>
                        </p>
                    </form>
                </div>';

        return $html;
    }

    private function generateCustomImageBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        $id_lang = $this->lang_id;
        if (!empty($block['image_url']) && !empty($block['image_url'][$id_lang])) {
            $html .= "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";

            if(!empty($block['image_link'][$id_lang]))
                $html .= "<a href='{$block['image_link'][$id_lang]}'><img src='{$block['image_url'][$id_lang]}' title='{$block['image_desc'][$id_lang]}' alt='{$block['image_desc'][$id_lang]}'></a>";
            else
                $html .= "<img src='{$block['image_url'][$id_lang]}' title='{$block['image_desc'][$id_lang]}' alt='{$block['image_desc'][$id_lang]}'>";
            $html .= '</div>';
        }

        return $html;
    }

    private function generateCustomLinkBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        if (!empty($block['custom_link_name'][$this->lang_id])) {
            $html .= "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";
            $target = (($block['custom_new_window'] == 1) ? '_blank' : '_self');
            $val = $block['custom_link_name'][$this->lang_id];
            $link_url = $block['custom_link_url'][$this->lang_id];
            $html .= "<a href='{$link_url}' target='{$target}'>{$val}</a></div>";
        }

        return $html;
    }

    private function generateCustomHTMLBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        if (!empty($block['code'])) {
            $html .= "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";
            $html .= '<div class="custom-html">' . html_entity_decode($block['code'][$this->lang_id], ENT_QUOTES) . '</div></div>';
        }

        return $html;
    }

    private function generateCMSpageBlock($type, $block, $tab_index, $block_index, $menu_type)
    {
        $html = '';
        if (!empty($block['selected'])) {
            $cms = new CMS(preg_replace("/[^0-9]/","",$block['selected']), $this->lang_id);
            $html .= "<div id='FINALmenu-{$menu_type}_{$block['name']}_{$tab_index}_{$block_index}' class='{$block['separator']} {$type} tab-block col-xs-{$block['nmb_of_columns']}'>";
            $html .= "<div class='cms-page'>{$cms->content}</div></div>";
        }

        return $html;
    }

    // ADVANCE TAB VIEW HELPER METHODS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function generateGripItems($grid_view, $c_per_row, $c_per_row_small)
    {
        $html = '';
        $count = 0;
        foreach ($grid_view as $item) {
            $count++;
            $cat = new Category(preg_replace("/[^0-9]/","",$item['name']),$this->lang_id);
            $link = Tools::HtmlEntitiesUTF8($cat->getLink());
            $html .= "<li class='category-grid-view'><a href='{$link}'>";
            if (!empty($item['image_link'][$this->lang_id])) {
                $img_url = $item['image_link'][$this->lang_id];
                $html .= "<img src='{$img_url}' alt='{$cat->name}' title='{$cat->name}' width='64' height='64'>";
            }
           $desc = @$item['category_desc'][$this->lang_id];
           $html .= "<div class='product-category-name grid-category-name'>{$cat->name}</div>{$desc}</a></li>";
           if (($count % $c_per_row) == 0) {
                $html .= "<li class='separator separator-bg-screens'></li>";
           } elseif (($count % $c_per_row_small) == 0) {
                $html .= "<li class='separator separator-sm-screens'></li>";
           }
        }

        return $html;
    }

    private function generateBoxedItem($item)
    {
        $html = '';
        preg_match($this->pattern, $item, $value);
        $id = (int) substr($item, strlen($value[1]), strlen($item));
        switch (substr($item, 0, strlen($value[1]))) {
            case 'MAN':
                $manufacturer = new Manufacturer((int) $id, $this->lang_id);
                if (!is_null($manufacturer->id)) {
                    if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
                        $manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
                    else
                        $manufacturer->link_rewrite = 0;
                    $link = new Link;
                    $link_rewrite = Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int) $id, $manufacturer->link_rewrite));
                    $src =  __PS_BASE_URI__ . 'img/m/' . (int) $manufacturer->id . '.jpg';
                    $html .= "<a href='{$link_rewrite}' title='{$manufacturer->name}'><img src='{$src}' title='{$manufacturer->name}' alt='{$manufacturer->name}'></a><p>{$manufacturer->name}</p>";
                }
                break;

            case 'SUP':
                $supplier = new Supplier((int) $id, $this->lang_id);
                if (!is_null($supplier->id)) {
                    $link = new Link;
                    $link_rewrite = Tools::HtmlEntitiesUTF8($link->getSupplierLink((int) $id, $supplier->link_rewrite));
                    $src =  __PS_BASE_URI__ . 'img/su/' . (int) $supplier->id . '.jpg';
                    $html .= "<a href='{$link_rewrite}' title='{$supplier->name}'><img src='{$src}' title='{$supplier->name}' alt='{$supplier->name}'></a><p>{$supplier->name}</p>";
                }
                break;
        }

        return $html;
    }

    private function generateListItems($menu_items, $nmb_of_columns)
    {
        $html = '';
        $width = ($nmb_of_columns == 1) ? 100 : (100 / ($nmb_of_columns/2));
        foreach ($menu_items as $item) {
            if (!$item)
                continue;

            preg_match($this->pattern, $item, $value);
            $id = (int) substr($item, strlen($value[1]), strlen($item));

            switch (substr($item, 0, strlen($value[1]))) {
                case 'CAT':
                    $html .= $this->generateCategoriesItems(Category::getNestedCategories($id, $this->lang_id, true, null), 0, $nmb_of_columns);
                    break;
                case 'CMS':
                    $cms = CMS::getLinks((int) $this->lang_id, array($id));
                    if (count($cms)) {
                        $link = Tools::HtmlEntitiesUTF8($cms[0]['link']);
                        $html .= "<li><a href='{$link}' title='{$cms[0]['meta_title']}'>{$cms[0]['meta_title']}</a></li>";
                    }
                    break;
                case 'CMS_CAT':
                    $category = new CMSCategory((int) $id, (int) $this->lang_id);
                    if (count($category)) {
                        $link = Tools::HtmlEntitiesUTF8($category->getLink());
                        $html .= "<li class='first-level-item'><a href='{$link}'>{$category->name}</a>";
                        $html .= $this->generateCMSItems($category->id, 1, $nmb_of_columns, false);
                        $html .= '</li>';
                    }
                    break;
                case 'MAN':
                    $manufacturer = new Manufacturer((int) $id, (int) $this->lang_id);
                    if (!is_null($manufacturer->id)) {
                        if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
                            $manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
                        else
                            $manufacturer->link_rewrite = 0;
                        $link = new Link;
                        $link_rewrite = Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int) $id, $manufacturer->link_rewrite));
                        $html .= "<li class='second-level-item' style='width: {$width}%'><a href='{$link_rewrite}' title='{$manufacturer->name}'>{$manufacturer->name}</a></li>";
                    }
                    break;
                case 'SUP':
                    $supplier = new Supplier((int) $id, (int) $this->lang_id);
                    if (!is_null($supplier->id)) {
                        $link = new Link;
                        $link_rewrite = Tools::HtmlEntitiesUTF8($link->getSupplierLink((int) $id, $supplier->link_rewrite));
                        $html .= "<li class='second-level-item' style='width: {$width}%'><a href='{$link_rewrite}' title='{$supplier->name}'>{$supplier->name}</a></li>";
                    }
                    break;
            }
        }

        return $html;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SIMPLE TAB CREATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method starts creation of desktop menu tab (simple view tab)
     * @param  array  $tab_specific_settings tab specific settings
     * @param  array  $tab                   tab settings, includes tab specific settings
     * @param  array  $dsk_menu_sttg         desktop menu settings
     * @param  int    $tab_index             tab identifier
     * @param  String $menu_type             [vertical|horizontal]
     * @return html   generated code
     */
    private function generateDesktopSimpleViewTab($tab_specific_settings, $tab, $dsk_menu_sttg, $tab_index, $menu_type)
    {
        $tab_image = trim($tab['tab_image']);
        $tab_icon = trim($tab['tab_icon']);
        $posTabs = ($tab['tab_position'] === 'left') ? 'left-tabs' : 'right-tabs';

        if($dsk_menu_sttg['menu_layout_holder'] == 'layout-4')
            $icon = '';
        else
            $icon = ((!empty($tab_image)) ? "<img src='{$tab['tab_image']}'>" : ((!empty($tab_icon)) ? "<i class={$tab['tab_icon']}></i>" : "" ));

        $html = "<li class='FINALmenu-simple-tab {$posTabs}' id='FINALmenu-tab-{$menu_type}-{$tab_index}'><div class='top-link-wrapper clearfix'>";
        if(!empty($tab['tab_note']))
            $html .= "<span class='tab-note' style='background-color: {$tab['tab_note_bg_color']};' >{$tab['tab_note']}</span>";

        // TAB TOP LINK
        $tab_name = (($dsk_menu_sttg['menu_layout_holder'] != 'layout-3') ? $tab['name'] : '' );
        if (!empty($tab['tab_link'])) {
            $target = (($tab['link_window'] == 1) ? '_blank' : '_self' );
            $html .= "<a target='{$target}' href='{$tab['tab_link']}' class='top-level-link {$dsk_menu_sttg['menu_layout_holder']}'>{$icon}{$tab_name}</a>";
        } else {
            $html .= "<span class='top-level-link {$dsk_menu_sttg['menu_layout_holder']}'>{$icon}{$tab_name}</span>";
        }

        $isEmpty = empty($tab_specific_settings['simple_menu_select']);
        if (!$isEmpty) {
          if(!strcmp($menu_type, "desktop"))
              $html .= '<i class="icon-chevron-down show-items-icon"></i>';
          else if(!strcmp($menu_type, "vertical"))
              $html .= '<i class="icon-chevron-right show-items-icon"></i>';
        }

        if (!$isEmpty) {
          $html .= "</div><div id='FINALmenu-{$menu_type}-{$tab_index}-tab-wrapper' class='FINALmenu-tab-content animated'><ul>";
          $html .= $this->generateSimpleMenuItems($tab_specific_settings, $tab_index);
          $html .= '</ul></div>';
        }
        $html .= '</li>';

        return $html;
    }

    private function generateSimpleMenuItems($tab_specific_settings, $tab_index)
    {
        $html = '';
        if ($tab_specific_settings['simple_menu_select'] != 'PRODUCT' && $tab_specific_settings['simple_menu_select'] != 'LNK') {
            preg_match($this->pattern, $tab_specific_settings['simple_menu_select'], $value);

            $id = (int) substr($tab_specific_settings['simple_menu_select'], strlen($value[1]), strlen($tab_specific_settings['simple_menu_select']));
            $switch = substr($tab_specific_settings['simple_menu_select'], 0, strlen($value[1]));
        } else {
            $switch = $tab_specific_settings['simple_menu_select'];
        }

        $limit = (int) (empty($tab_specific_settings['category_limit']) ? 0 : $tab_specific_settings['category_limit']);
        switch ($switch) {
            case 'CAT':
                $html .= $this->generateCategoriesItems(Category::getNestedCategories($id, $this->lang_id, true, null), 0, 1, false, $limit);
                break;
            // simple menu specific
            case 'PRODUCT':
                $product = new Product((int) $tab_specific_settings['product_ID'], true, (int) $this->lang_id);
                if (!is_null($product->id)) {
                    $link = Tools::HtmlEntitiesUTF8($product->getLink());
                    $html .= "<li><a href='{$link}' title='{$product->name}'>{$product->name}</a></li>";
                }
                break;
            case 'CMS':
                $cms = CMS::getLinks((int) $this->lang_id, array($id));
                if (count($cms)) {
                    $link = Tools::HtmlEntitiesUTF8($cms[0]['link']);
                    $html .= "<li><a href='{$link}' title='{$cms[0]['meta_title']}'>{$cms[0]['meta_title']}</a></li>";
                }
                break;
            case 'CMS_CAT':
                $category = new CMSCategory((int) $id, (int) $this->lang_id);
                if (count($category)) {
                    $link = Tools::HtmlEntitiesUTF8($category->getLink());
                    $html .= "<li class='first-level-item'><a href='{$link}'>{$category->name}</a>";
                    $html .= $this->generateCMSItems($category->id, 1, 1, false, $limit);
                    $html .= '</li>';
                }
                break;
            // simple menu specific
            case 'ALLMAN':
                $link = new Link;
                $html .= "<li><a href='{$link->getPageLink('manufacturer')}' title='{$this->l('All manufacturers')}'>{$this->l('All manufacturers')}</a><ul>";
                $manufacturers = Manufacturer::getManufacturers();
                foreach ($manufacturers as $key => $manufacturer) {
                    $link_rewrite = $link->getManufacturerLink((int) $manufacturer['id_manufacturer'], $manufacturer['link_rewrite']);
                    $html .= "<li><a href='{$link_rewrite}' title='{$manufacturer['name']}'>{$manufacturer['name']}</a></li>";
                }
                $html .= '</ul>';
                break;
            case 'MAN':
                $manufacturer = new Manufacturer((int) $id, (int) $this->lang_id);
                if (!is_null($manufacturer->id)) {
                    if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
                        $manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
                    else
                        $manufacturer->link_rewrite = 0;
                    $link = new Link;
                    $link_rewrite = Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int) $id, $manufacturer->link_rewrite));
                    $html .= "<li class='second-level-item' style='width: 100%'><a href='{$link_rewrite}' title='{$manufacturer->name}'>{$manufacturer->name}</a></li>";
                }
                break;
            // simple menu specific
            case 'ALLSUP':
                $link = new Link;
                $html .= "<li><a href='{$link->getPageLink('supplier')}' title='{$this->l('All suppliers')}'>{$this->l('All suppliers')}</a><ul>";
                $suppliers = Supplier::getSuppliers();
                foreach ($suppliers as $key => $supplier) {
                    $link_rewrite = $link->getSupplierLink((int) $supplier['id_supplier'], $supplier['link_rewrite']);
                    $html .= "<li><a href='{$link_rewrite}' title='{$supplier['name']}'>{$supplier['name']}</a></li>";
                }
                $html .= '</ul>';
                break;
            case 'SUP':
                $supplier = new Supplier((int) $id, (int) $this->lang_id);
                if (!is_null($supplier->id)) {
                    $link = new Link;
                    $link_rewrite = Tools::HtmlEntitiesUTF8($link->getSupplierLink((int) $id, $supplier->link_rewrite));
                    $html .= "<li class='second-level-item' style='width: 100%'><a href='{$link_rewrite}' title='{$supplier->name}'>{$supplier->name}</a></li>";
                }
                break;
            // simple menu specific
            case 'SHOP':
                $shop = new Shop((int) $id);
                if (Validate::isLoadedObject($shop)) {
                    $link = new Link;
                    $url = Tools::HtmlEntitiesUTF8($shop->getBaseURL());
                    $html .= "<li><a href='{$url}' title='{$shop->name}'>{$shop->name}</a></li>";
                }
                break;
            case 'LNK':
                $url = Tools::HtmlEntitiesUTF8($tab_specific_settings['link_url'][$this->lang_id]);
                $action = (($tab_specific_settings['tab_link_new_window']) ? ' onclick="return !window.open(this.href);"' : '');
                $link_title = $tab_specific_settings['link_title'][$this->lang_id];
                $html .= "<li><a href='{$url}' {action} title='{$link_title}'>{$link_title}</a></li>";
                break;
        }

        return $html;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MOBILE MENU ITEMS CREATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Method starts creation of mmobile menu tab
     * @param  array $menu_items mobile menu items
     * @return html  generated code
     */
    private function generateMobileMenu($menu_items)
    {
        $html = '';
        foreach ($menu_items as $item) {
            if (!$item)
                continue;

            if ($item['name'] != 'PRODUCT' && $item['name'] != 'LNK') {
                preg_match($this->pattern, $item['name'], $value);
                $id = (int) substr($item['name'], strlen($value[1]), strlen($item['name']));
                $switch = substr($item['name'], 0, strlen($value[1]));
            } else {
                $switch = $item['name'];
            }
            switch ($switch) {
                case 'CAT':
                    $html .= $this->generateCategoriesItems(Category::getNestedCategories($id, $this->lang_id, true, null), 0, 1, true);
                    break;
                case 'PRODUCT':
                    $product = new Product((int) $item['product_ID'], true, (int) $this->lang_id);
                    if (!is_null($product->id)) {
                        $link = Tools::HtmlEntitiesUTF8($product->getLink());
                        $html .= "<li><a href='{$link}' title='{$product->name}'>{$product->name}</a></li>";
                    }
                    break;
                case 'CMS':
                    $cms = CMS::getLinks((int) $this->lang_id, array($id));
                    if (count($cms)) {
                        $link = Tools::HtmlEntitiesUTF8($cms[0]['link']);
                        $html .= "<li><a href='{$link}' title='{$cms[0]['meta_title']}' >{$cms[0]['meta_title']}</a></li>";
                    }
                    break;
                case 'CMS_CAT':
                    $category = new CMSCategory((int) $id, (int) $this->lang_id);
                    if (count($category)) {
                        $link = Tools::HtmlEntitiesUTF8($category->getLink());
                        $html .= "<li><a href='{$link}' title='{$category->name}'>{$category->name}</a>";
                        $html .= $this->generateCMSItems($category->id, 1, 1, true);
                        $html .= '</li>';
                    }
                    break;
                // Case to handle the option to show all Manufacturers
                case 'ALLMAN':
                    $link = new Link;
                    $html .= "<li><a href=\"{$link->getPageLink('manufacturer')}\" title=\"{$this->l('All manufacturers')}\">{$this->l('All manufacturers')}</a><i class=\"icon-plus\"></i><ul>";
                    $manufacturers = Manufacturer::getManufacturers();
                    foreach ($manufacturers as $key => $manufacturer) {
                        $link = $link->getManufacturerLink((int) $manufacturer['id_manufacturer'], $manufacturer['link_rewrite']);
                        $html .= "<li><a href='{$link}' title='{$manufacturer['name']}'>{$manufacturer['name']}</a></li>";
                    }
                    $html .= '</ul>';
                    break;
                case 'MAN':
                    $manufacturer = new Manufacturer((int) $id, (int) $this->lang_id);
                    if (!is_null($manufacturer->id)) {
                        if (intval(Configuration::get('PS_REWRITING_SETTINGS')))
                            $manufacturer->link_rewrite = Tools::link_rewrite($manufacturer->name);
                        else
                            $manufacturer->link_rewrite = 0;
                        $link = new Link;
                        $link_rewrite = Tools::HtmlEntitiesUTF8($link->getManufacturerLink((int) $id, $manufacturer->link_rewrite));
                        $html .= "<li><a href='{$link_rewrite}' title='{$manufacturer->name}'>{$manufacturer->name}</a></li>";
                    }
                    break;
                // Case to handle the option to show all Suppliers
                case 'ALLSUP':
                    $link = new Link;
                    $html .= "<li><a href=\"{$link->getPageLink('supplier')}\" title=\"{$this->l('All suppliers')}\">{$this->l('All suppliers')}</a><i class=\"icon-plus\"></i><ul>";
                    $suppliers = Supplier::getSuppliers();
                    foreach ($suppliers as $key => $supplier) {
                        $link_rewrite = $link->getSupplierLink((int) $supplier['id_supplier'], $supplier['link_rewrite']);
                        $html .= "<li><a href='{$link_rewrite}' title='{$supplier['name']}'>{$supplier['name']}</a></li>";
                    }
                    $html .= '</ul>';
                    break;
                case 'SUP':
                    $supplier = new Supplier((int) $id, (int) $this->lang_id);
                    if (!is_null($supplier->id)) {
                        $link = new Link;
                        $link_rewrite = Tools::HtmlEntitiesUTF8($link->getSupplierLink((int) $id, $supplier->link_rewrite));
                        $html .= "<li><a href='{$link_rewrite}' title='{$supplier->name}'>{$supplier->name}</a></li>";
                    }
                    break;
                case 'SHOP':
                    $shop = new Shop((int) $id);
                    if (Validate::isLoadedObject($shop)) {
                        $link = new Link;
                        $link_url = Tools::HtmlEntitiesUTF8($shop->getBaseURL());
                        $html .= "<li><a href='{$link_url}' title='{$shop->name}'>{$shop->name}</a></li>";
                    }
                    break;
                case 'LNK':
                    if ($item['link_url'][$this->lang_id] || $item['link_title'][$this->lang_id]) {
                        $action = (($item['link_new_window']) ? ' onclick="return !window.open(this.href);"' : '');
                        $html .= "<li><a href='{$item['link_url']}' {$action} title='{$item['link_title']}'>{$item['link_title']}</a></li>";
                    }
                    break;
            }
        }

        return $html;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SHARED METHODS FOR ALL THREE MENU TYPES
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private function generateCategoriesItems($categories, $menu_level_depth, $nmb_of_columns, $mobile = false, $limit = 0)
    {
        $html = '';
        $menu_level_depth++;
        foreach ($categories as $key => $category) {
            if ($category['level_depth'] > 1) {
                $cat = new Category($category['id_category']);
                $link = Tools::HtmlEntitiesUTF8($cat->getLink());
            } else
                $link = $this->context->link->getPageLink('index');

            $width = ($nmb_of_columns == 1) ? 100 : (100 / ($nmb_of_columns/2));
            $li_class = (($menu_level_depth == 1) ? 'class="first-level-item"' : (($menu_level_depth == 2) ? "class='second-level-item' style='width: {$width}%'" : 'class="sub-items"'));
            $padding = (($mobile === true) ? 'style="padding-left: ' . ($menu_level_depth * 10) . 'px"' : '');

            $html .= "<li {$li_class}><a href='{$link}' title='{$category['name']}' {$padding}>{$category['name']}</a>";
            if (isset($category['children']) && !empty($category['children']) && ($limit == 0 || $limit >= $menu_level_depth)) {

                if ($mobile == true)
                    $html .= '<i class="icon-plus"></i>';
                else if ($menu_level_depth > 1)
                    $html .= '<i class="icon-chevron-right show-items-icon"></i>';

                $html .= '<ul ' . (($menu_level_depth >= 2) ? 'class="hidden-categories" ' : 'class="final_no_padding"') . '>';
                $html .= $this->generateCategoriesItems($category['children'], $menu_level_depth, $nmb_of_columns, $mobile, $limit);
                $html .= '</ul>';
            }
            $html .= '</li>';
        }

        return $html;
    }

    private function generateCMSItems($parent, $menu_level_depth, $nmb_of_columns, $mobile = false, $limit = 0)
    {
        $html = '';
        if ($limit == 0 || $limit >= $menu_level_depth) {
            $menu_level_depth++;
            $categories = $this->getCMSCategories((int) $parent, $this->lang_id);
            $pages = $this->getCMSPages((int) $parent, false, $this->lang_id);

            if (count($categories) || count($pages)) {
                $width = ($nmb_of_columns == 1) ? 100 : (100 / ($nmb_of_columns/2));
                if ($mobile == true)
                    $html .= '<i class="icon-plus"></i>';
                else if ($menu_level_depth > 2)
                    $html .= '<i class="icon-chevron-right show-items-icon"></i>';

                $html .= '<ul ' . (($menu_level_depth > 2) ? 'class="hidden-categories" ' : 'class="final_no_padding"') . '>';
                foreach ($categories as $category) {
                    $cat = new CMSCategory((int) $category['id_cms_category'], $this->lang_id);
                    $link = Tools::HtmlEntitiesUTF8($cat->getLink());
                    $li_class =  (($menu_level_depth == 2) ? "class='second-level-item' style='width: {$width}%'" : 'class="sub-items"');
                    $padding = (($mobile === true) ? 'style="padding-left: ' . ($menu_level_depth * 10) . 'px"' : '');

                    $html .= "<li {$li_class}><a href='{$link}' {$padding}>{$category['name']}</a>";
                    $html .= $this->generateCMSItems($category['id_cms_category'], ((int) $menu_level_depth), $nmb_of_columns, $mobile, $limit);
                    $html .= '</li>';
                }
                if (count($pages)) {
                    if ($menu_level_depth == 2) {
                        $html .= "<div class=\"related-posts-title\" style=\"width: {$width} %\">{$this->l('Category related posts')}</div><div class=\"related-posts\"><div class=\"related-posts\">";
                        foreach ($pages as $page) {
                            $cms = new CMS($page['id_cms'], $this->lang_id);
                            $links = $cms->getLinks($this->lang_id, array((int) $cms->id));
                            $html .= "<li class='second-level-item' style='width: {$width}%'><a href='{$links[0]['link']}'>{$cms->meta_title}</a></li>";
                        }
                        $html .= '</div>';
                    } else {
                        $html .= "<div class=\"related-posts-title\" style=\"width: 100%\">{$this->l('Category related posts')}</div><div class=\"related-posts\">";
                        foreach ($pages as $page) {
                            $cms = new CMS($page['id_cms'], $this->lang_id);
                            $links = $cms->getLinks($this->lang_id, array((int) $cms->id));
                            $html .= "<li class='sub-items'><a href='{$links[0]['link']}'>{$cms->meta_title}</a></li>";
                        }
                        $html .= '</div>';
                    }
                }
                $html .= '</ul>';
            }
        }

        return $html;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DATABASE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Selects mobile menu items from DB depending on current language and shop ID
     * @return array selected items
     */
    private function getMobileMenuItems()
    {
        $sql = 'SELECT mobm.`name`, mobm.`product_ID`, mobm.`position` ,mobm.`link_new_window`, mobm.`position`,  mobml.`link_title`,  mobml.`link_url`
                FROM `' . _DB_PREFIX_ . 'mobile_menu_tabs` mobm
                INNER JOIN `' . _DB_PREFIX_ . 'mobile_menu_tabs_lang` mobml
                    ON mobml.`id_tab` = mobm.`id_tab`
                INNER JOIN `' . _DB_PREFIX_ . 'mobile_menu_tabs_shop` mobms
                    ON mobms.`id_tab` = mobm.`id_tab`
                WHERE mobml.`id_lang` = ' . (int) $this->lang_id . '
                AND mobms.`id_shop` = ' . (int) $this->shop_id . '
                AND mobm.`active` = 1
                ORDER BY mobm.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Selects vertical or horizontal menu items from DB depending on current language and shop ID
     * @param  String $table_prefix [vertical|horizontal]
     * @return array  selected items
     */
    private function getDesktopMenuItems($table_prefix)
    {
        $sql = 'SELECT desm.`type`, desm.`tab_icon`, desm.`tab_image`, desm.`tab_note_bg_color`, desm.`tab_position`, desml.`tab_link`, desml.`tab_note`, desm.`other_text_color`, desm.`link_window`, desm.`links_color` ,desm.`links_hover_color`, desm.`position`, desm.`settings`, desml.`name`
                FROM `' . _DB_PREFIX_ . $table_prefix . '_menu_tabs` desm
                INNER JOIN `' . _DB_PREFIX_ . $table_prefix . '_menu_tabs_lang` desml
                    ON desml.`id_tab` = desm.`id_tab`
                INNER JOIN `' . _DB_PREFIX_ . $table_prefix .'_menu_tabs_shop` desms
                    ON desms.`id_tab` = desm.`id_tab`
                WHERE desml.`id_lang` = '.$this->lang_id.'
                AND desms.`id_shop` = '.$this->shop_id.'
                AND desm.`active` = 1
                ORDER BY desm.`position` ASC';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Selects CMS categories from DB depending their parent and language
     * @return array selected CMS categories
     */
    private function getCMSCategories($parent = 1)
    {
        $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
                FROM `' . _DB_PREFIX_ . 'cms_category` bcp
                INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
                ON (bcp.`id_cms_category` = cl.`id_cms_category`)
                WHERE cl.`id_lang` = ' . $this->lang_id . '
                AND bcp.`id_parent` = ' . (int) $parent;

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Selects CMS pages from DB depending on CMS category ID, shop ID and current language
     * @param  int   $id_cms_category CMS category ID
     * @return array selected CMS pages
     */
    private function getCMSPages($id_cms_category)
    {
        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
                FROM `' . _DB_PREFIX_ . 'cms` c
                INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
                ON (c.`id_cms` = cs.`id_cms`)
                INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
                ON (c.`id_cms` = cl.`id_cms`)
                WHERE c.`id_cms_category` = ' . (int) $id_cms_category . '
                AND cs.`id_shop` = ' . (int) $this->shop_id . '
                AND cl.`id_lang` = ' . (int) $this->lang_id . '
                AND c.`active` = 1
                ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * Custom comparator
     * @param  array $a first compared tab
     * @param  array $b second compared tab
     * @return int   [<0|0|>0]
     */
    public static function comparator($a, $b)
    {
    return ($a['order_index'] - $b['order_index']);
    }
}
