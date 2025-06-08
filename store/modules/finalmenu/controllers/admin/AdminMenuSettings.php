<?php
/**
 * Finalmenu
 *
 * @author     Matej Berka
 * @copyright  2014 Matej
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.0
 */
require_once(_PS_MODULE_DIR_.'finalmenu/classes/DesktopMenuObject.php');
require_once(_PS_MODULE_DIR_.'finalmenu/classes/VerticalMenuObject.php');
require_once(_PS_MODULE_DIR_.'finalmenu/classes/MobileMenuObject.php');
require_once(_PS_MODULE_DIR_.'finalmenu/finalmenu.php');

class AdminMenuSettingsController extends ModuleAdminController
{
    /*
     * Spaces per depth in BO
     */
    private $spacer_size = '5';
    private $shop_id;
    private $lang_id;
    private $animations = array("bounce","flash","pulse","rubberBand","shake","swing","tada","wobble","bounceIn","bounceInDown","bounceInLeft","bounceInRight","bounceInUp","bounceOut","bounceOutDown","bounceOutLeft","bounceOutRight","bounceOutUp","fadeIn","fadeInDown","fadeInDownBig","fadeInLeft","fadeInLeftBig","fadeInRight","fadeInRightBig","fadeInUp","fadeInUpBig","fadeOut","fadeOutDown","fadeOutDownBig","fadeOutLeft","fadeOutLeftBig","fadeOutRight","fadeOutRightBig","fadeOutUp","fadeOutUpBig","flip","flipInX","flipInY","flipOutX","flipOutY","lightspeedIn","lightspeedOut","rotateIn","rotateInDownLeft","rotateInDownRight","rotateInUpLeft","rotateInUpRight","rotateOut","rotateOutDownLeft","rotateOutDownRight","rotateOutUpLeft","rotateOutUpRight","hinge","rollIn","rollOut","zoomIn","zoomInDown","zoomInLeft","zoomInRight","zoomInUp","zoomOut","zoomOutDown","zoomOutLeft","zoomOutRight","zoomOutUp");

    public function __construct()
    {
        $this->lang = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->bootstrap = true;
        $this->className = 'DesktopMenuObject';
        $this->identifier = 'id_tab';
        $this->_orderBy = 'position';
        $this->display_footer = false;

        parent::__construct();
        $this->bulk_actions = array('delete' => array('text' => $this->module->l('Delete selected', 'AdminMenuSettings'), 'confirm' => $this->module->l('Delete selected items?', 'AdminMenuSettings')));
        // just to make things easier
        $this->shop_id = $this->context->shop->id;
        $this->lang_id = $this->context->language->id;
        $this->shop_group_id = $this->context->shop->id_shop_group;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // BASIC SETUP
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Overrides default init method
     */
    public function init()
    {
        // init
        if (isset($_GET['desktop_menu_tabs'])) {
            $this->initMenuSpecific("desktop");
        } elseif (isset($_GET['mobile_menu_tabs'])) {
            $this->initMenuSpecific("mobile");
        } elseif (isset($_GET['vertical_menu_tabs'])) {
            $this->initMenuSpecific("vertical");
        } else {
            parent::init();
        }
    }

    /**
     * Adds specific settings for specific tab
     * @param String $table_name table name
     */
    private function initMenuSpecific($table_name)
    {
        $this->table =  $table_name.'_menu_tabs';
        parent::init();
        Shop::addTableAssociation($this->table, array('type' => 'shop'));
        if (Shop::getContext() == Shop::CONTEXT_SHOP)
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_. $table_name.'_menu_tabs_shop` sa ON (a.`id_tab` = sa.`id_tab` AND sa.id_shop = '.$this->shop_id.') ';

        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive())
            $this->_where = ' AND sa.`id_shop` = '.$this->shop_id;

        AdminControllerCore::$currentIndex = 'index.php?controller=AdminMenuSettings&'.$table_name.'_menu_tabs';
        $this->className = ucfirst($table_name).'MenuObject';
    }

    /**
     * Overrides default beforeAdd method
     */
    protected function beforeAdd($obj)
    {
        if (isset($_GET['desktop_menu_tabs']) || isset($_GET['vertical_menu_tabs'])) {
            $obj->settings = $_POST['advance_tab_object'];
        }
    }

    /**
     * Overrides default afterAdd method
     */
    protected function afterAdd($object)
    {
        $this->menuUpdate();

        return true;
    }

    /**
     * Overrides default afterUpdate method
     */
    protected function afterUpdate($object)
    {
        if (isset($_GET['desktop_menu_tabs']) || isset($_GET['vertical_menu_tabs'])) {
            $object->settings = $_POST['advance_tab_object'];
            $object->update();
        }

        $this->menuUpdate();

        return true;
    }

    /**
     * Overrides default initContent method. Initializes and displays menu settings.
     */
    public function initContent()
    {
        if (!$this->viewAccess()) {
            $this->errors[] = Tools::displayError('You do not have permission to view this.');

            return;
        }

        $update = false;
        $this->getLanguages();
        $this->initToolbar();
        $this->initTabModuleList();
        $this->initPageHeaderToolbar();

        if (Tools::isSubmit('desktop_menu_settings_submit')) {
            $update = true;
            $this->updateHorizontalMenuSettings();
        } elseif (Tools::isSubmit('mobile_menu_settings_submit')) {
            $update = true;
            $this->updateMobileMenuSettings();
        } elseif (Tools::isSubmit('vertical_menu_settings_submit')) {
            $update = true;
            $this->updateVerticalMenuSettings();
        }

        if ($update) {
            $this->menuUpdate();
            $this->confirmations[] = $this->module->l('Menu has been updated!', 'AdminMenuSettings');
        }

        if ($this->display == 'edit' || $this->display == 'add') {
            if (!$this->loadObject(true)) {
                return;
            }

            if($this->table == 'desktop_menu_tabs')
                $this->content .= $this->renderDesktopTabSettingsPage("horizontal");
            else if($this->table == 'mobile_menu_tabs')
                $this->content .= $this->renderMobileTabSettingsPage();
            else if($this->table == 'vertical_menu_tabs')
                $this->content .= $this->renderDesktopTabSettingsPage("vertical");

        } elseif (!$this->ajax) {
            $this->content .= $this->renderGlobalSettingsPage();

            // if we have to display the required fields form
            if($this->required_database)
                $this->content .= $this->displayRequiredFields();
        }

        $this->context->smarty->assign(array(
            'content' => $this->content,
            'lite_display' => $this->lite_display,
            'url_post' => self::$currentIndex.'&token='.$this->token,
            'show_page_header_toolbar' => $this->show_page_header_toolbar,
            'page_header_toolbar_title' => $this->page_header_toolbar_title,
            'title' => $this->page_header_toolbar_title,
            'toolbar_btn' => $this->page_header_toolbar_btn,
            'page_header_toolbar_btn' => $this->page_header_toolbar_btn
        ));
    }

    /**
     * Overrides default setMedia method.
     */
    public function setMedia()
    {
        parent::setMedia();
        $this->addJqueryUI('ui.draggable');
        $this->addJqueryUI('ui.sortable');
        $this->context->controller->addjQueryPlugin(array('autocomplete', 'colorpicker'));
        $this->addJS(array(_MODULE_DIR_ . $this->module->name . '/js/admin/finalmenu_main_settings.js'));
        $this->addCSS(array( _MODULE_DIR_ . $this->module->name . '/css/admin/FINALmenu_admin.css'));
    }

    /**
     * Overrides default ajaxProcessUpdatePositions method. Handles ajax request.
     */
    public function ajaxProcessUpdatePositions()
    {
        $tabs = Tools::getValue('tab');
        if (isset($_GET['desktop_menu_tabs'])) {
            foreach ($tabs as $position => $value) {
                $id_tab = explode('_', $value);
                $id_tab = (int) $id_tab[2];
                if ($id_tab > 0)
                    if ($tabObject = new DesktopMenuObject($id_tab)) {
                        $tabObject->position = $position+1;
                        $tabObject->update();
                    }
            }
        } elseif (isset($_GET['vertical_menu_tabs'])) {
            foreach ($tabs as $position => $value) {
                $id_tab = explode('_', $value);
                $id_tab = (int) $id_tab[2];
                if ($id_tab > 0)
                    if ($tabObject = new VerticalMenuObject($id_tab)) {
                        $tabObject->position = $position+1;
                        $tabObject->update();
                    }
            }
        } elseif (isset($_GET['mobile_menu_tabs'])) {
            foreach ($tabs as $position => $value) {
                $id_tab = explode('_', $value);
                $id_tab = (int) $id_tab[2];
                if ($id_tab > 0)
                    if ($tabObject = new MobileMenuObject($id_tab)) {
                        $tabObject->position = $position+1;
                        $tabObject->update();
                    }
            }
        }
        $this->menuUpdate();
    }

    /**
     * Method triggers cache clear
     */
    protected function menuUpdate()
    {
        Hook::exec('actionUpdateMenu');
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // SETTINGS UPDATE METHODS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Updates horizontal menu settings
     */
    protected function updateHorizontalMenuSettings()
    {
        $settings = unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $this->shop_group_id, $this->shop_id));
        $settings = ((empty($settings)) ? finalmenu::$config_desktop_menu : $settings);

        foreach($settings as $key => $value)
            if($key == 'custom_css' || $key == 'custom_js')
                $settings[$key] = htmlentities($_POST[$key], ENT_QUOTES);
            else if($key == 'menu_top_links_font_url')
                $settings[$key] = ((isset($_POST[$key])) ? $_POST[$key] : '' );
            else
                $settings[$key] = $_POST[$key];

        Configuration::updateValue('FINALm_DESKTOP_CONFIG', serialize($settings), NULL, $this->shop_group_id, $this->shop_id);
    }

    /**
     * Updates vertical menu settings
     */
    protected function updateVerticalMenuSettings()
    {
        $settings = unserialize(Configuration::get('FINALm_VERTICAL_CONFIG', null, $this->shop_group_id, $this->shop_id));
        $settings = ((empty($settings) || isset($settings)) ? finalmenu::$config_vertical_menu : $settings);

        foreach($settings as $key => $value)
            if($key == 'menu_top_links_font_url')
                $settings[$key] = ((isset($_POST[$key])) ? $_POST[$key] : '' );
            else
                $settings[$key] = $_POST[$key];

        Configuration::updateValue('FINALm_VERTICAL_CONFIG', serialize($settings), NULL, $this->shop_group_id, $this->shop_id);
    }

    /**
     * Updates mobile menu settings
     */
    protected function updateMobileMenuSettings()
    {
        $settings = unserialize(Configuration::get('FINALm_MOBILE_CONFIG', null, $this->shop_group_id, $this->shop_id));
        $settings = ((empty($settings) || isset($settings)) ? finalmenu::$config_mobile_menu : $settings);

        foreach($settings as $key => $value)
            if($key != 'FINALm_links_font_url')
                $settings[$key] = $_POST[$key];

        $settings['FINALm_links_font_url'] = ((isset($_POST['FINALm_links_font_url'])) ? $_POST['FINALm_links_font_url'] : '' );
        Configuration::updateValue('FINALm_MOBILE_CONFIG', serialize($settings), NULL, $this->shop_group_id, $this->shop_id);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // MAIN SETTINGS PAGE GENERATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * First method which triggers settings page generation
     * @return html settings page
     */
    public function renderGlobalSettingsPage()
    {
        $html = $this->renderTabs(array('desktop_menu_wrapper','vertical_menu_wrapper', 'mobile_menu_wrapper'),
            array('icon-laptop', 'icon-list-ul', 'icon-mobile'), array($this->module->l('Horizontal menu', 'AdminMenuSettings'), $this->module->l('Vertical menu', 'AdminMenuSettings'), $this->module->l('Mobile menu', 'AdminMenuSettings')));

        $html .= '<div class="settings_wrapper col-lg-10 tab-content"><div id="desktop_menu_wrapper" class="tab-panel">';
        $html .= $this->renderDesktopMenu();
        $html .= '</div><div id="vertical_menu_wrapper" class="tab-panel" style="display: none">';
        $html .= $this->renderVerticalMenu();
        $html .= '</div><div id="mobile_menu_wrapper" class="tab-panel" style="display: none">';
        $html .= $this->renderMobileMenu();
        $html .= '</div></div>';

        return $html;
    }

    /**
     * Methods creates tab switcher
     * @param String $IDs    tab  ids
     * @param String $icons  tab icons
     * @param String $titles names
     */
    private function renderTabs($IDs, $icons, $titles)
    {
        $html = "<div id=\"tabs-menu\" class=\"list-group col-lg-2\"><a class=\"list-group-item selected\" href=\"#{$IDs[0]}\"><i class=\"{$icons[0]}\"></i>&nbsp;&nbsp;&nbsp;{$titles[0]}</a>";
        for ($i = 1; $i < count($IDs); $i++) {
            $html .= "<a class=\"list-group-item\" href=\"#{$IDs[$i]}\"><i class=\"{$icons[$i]}\"></i>&nbsp;&nbsp;&nbsp;{$titles[$i]}</a>";
        }
        $html .= '</div>';

        return $html;
    }

    /**
     * Method starts generating settings for horizontal menu
     */
    private function renderDesktopMenu()
    {
        $this->table = 'desktop_menu_tabs';
        AdminControllerCore::$currentIndex = 'index.php?controller=AdminMenuSettings&desktop_menu_tabs';

        return $this->renderListDesktopMenu().$this->renderConfigDesktopMenu();
    }

    /**
     * Method starts generating list of tabs for horizontal menu
     */
    private function renderListDesktopMenu()
    {
        $this->_orderBy = 'position';
        $this->position_identifier = 'position';
        $this->identifier = 'id_tab';
        $this->initToolbar();

        $this->fields_list = array(
            'name' => array(
                'title' => $this->module->l('Name', 'AdminMenuSettings'),
                'orderby' => false,
            ),
            'active' => array(
                'title' => $this->module->l('Displayed', 'AdminMenuSettings'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->module->l('Tab position', 'AdminMenuSettings'),
                'position' => 'position'
            ),
            'tab_position' => array(
                'title' => $this->module->l('Tab position', 'AdminMenuSettings'),
                'orderby' => false,
            ),
        );

        return parent::renderList();
    }

    /**
     * Method starts generating main confing page for horizontal menu
     */
    private function renderConfigDesktopMenu()
    {
        $this->submit_action = 'desktop_menu_settings_submit';
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Settings', 'AdminMenuSettings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'layout_picker',
                    'name' => 'menu_layout_holder',
                    'label' => $this->module->l('Tabs layout ', 'AdminMenuSettings'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable sticky menu', 'AdminMenuSettings'),
                    'name' => 'sticky_menu',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Sticky menu transparency', 'AdminMenuSettings'),
                    'desc' => $this->module->l('The value must be in range from 0 to 1. And decimal numbers must be with DOT not with dash.', 'AdminMenuSettings'),
                    'name' => 'sticky_menu_transparency',
                    'size' => 30,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable google fonts', 'AdminMenuSettings'),
                    'name' => 'google_fonts',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font family', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: \"Pathway Gothic One\", sans-serij", 'AdminMenuSettings'),
                    'name' => 'menu_top_links_font',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font url', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: http://fonts.googleapis.com/css?family=Pathway+Gothic+One", 'AdminMenuSettings'),
                    'name' => 'menu_top_links_font_url',
                    'disabled' => true,
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs font size:', 'AdminMenuSettings'),
                    'hint' => $this->module->l('This size will be set to the most top links in menu "tabs links"', 'AdminMenuSettings'),
                    'name' => 'menu_top_links_font_size',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Menu fullwidth', 'AdminMenuSettings'),
                    'hint' => $this->module->l('Sticky menu will have fullwidth', 'AdminMenuSettings'),
                    'name' => 'menu_top_full_width',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                $this->addEffects('menuAnimateIn', $this->module->l('Select menu entrance animation', 'AdminMenuSettings')),
                $this->addEffects('menuAnimateOut', $this->module->l('Select menu exit animation', 'AdminMenuSettings')),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs line height:', 'AdminMenuSettings'),
                    'name' => 'menu_top_links_line_height',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs background color', 'AdminMenuSettings'),
                    'name' => 'menu_background_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs text color', 'AdminMenuSettings'),
                    'name' => 'text_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs icon color', 'AdminMenuSettings'),
                    'name' => 'icons_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs hover background color', 'AdminMenuSettings'),
                    'name' => 'tab_hover_background_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs separator color', 'AdminMenuSettings'),
                    'name' => 'links_separator_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs text and icon hover color', 'AdminMenuSettings'),
                    'name' => 'foreground_hover_color',
                    'desc' => $this->module->l('Note that tabs are called the most top links in the menu.', 'AdminMenuSettings'),
                    'size' => 30,
                ),
                array(
                    'type' => 'html',
                    'html_content' => '<h3>'.$this->module->l('Only for advance users', 'AdminMenuSettings').'</h3>',
                    'label' => $this->l(''),
                    'form_group_class' => 'html-part',
                    'col' => '12',
                    'name' => '',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Insert custom CSS', 'AdminMenuSettings'),
                    'hint' => $this->module->l('You can override the menu look by inserting custom CSS.', 'AdminMenuSettings'),
                    'name' => 'custom_css',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('Insert custom JS', 'AdminMenuSettings'),
                    'hint' => $this->module->l('You can override the menu behaviour by inserting custom JS.', 'AdminMenuSettings'),
                    'desc' => $this->module->l('Is recommended to surround your JS by jQuery by document ready function or by similar method.', 'AdminMenuSettings'),
                    'name' => 'custom_js',
                ),
            ),
            'buttons' => array(
                'save' => array(
                    'title' => $this->module->l('Save', 'AdminMenuSettings'),
                    'name' => 'desktop_menu_settings_submit',
                    'type' => 'button',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        $this->tpl_form_vars['fields_value'] = unserialize(Configuration::get('FINALm_DESKTOP_CONFIG', null, $this->shop_group_id, $this->shop_id));
        $this->tpl_form_vars['fields_value']['custom_css'] = html_entity_decode($this->tpl_form_vars['fields_value']['custom_css'], ENT_QUOTES);
        $this->tpl_form_vars['fields_value']['custom_js'] = html_entity_decode($this->tpl_form_vars['fields_value']['custom_js'], ENT_QUOTES);

        return parent::renderForm();
    }

    /**
     * Method starts generating list of tabs for horizontal menu
     */
    private function addEffects($name, $title)
    {

      $desc = '<a href="http://daneden.github.io/animate.css" target="_blank">'.$this->module->l('Learn more', 'AdminMenuSettings').'</a>';
      $select = array(
            'type' => 'select',
            'label' => $this->module->l($title, 'AdminMenuSettings'),
            'desc' => $desc,
            'name' => $name,
            'options' => array(
                'query' => array(),
                'id' => 'id_option',
                'name' => 'name'
            )
        );

        foreach ($this->animations as $index => $animation)
            $select['options']['query'][] = array('id_option' => $animation, 'name' => $animation);

        return $select;
    }

    /**
     * First method which triggers settings page generation
     * @return html settings page
     */
    private function renderVerticalMenu()
    {
        $this->table = 'vertical_menu_tabs';
        AdminControllerCore::$currentIndex = 'index.php?controller=AdminMenuSettings&vertical_menu_tabs';

        return $this->renderListVerticalMenu().$this->renderConfigVerticalMenu();
    }

    /**
     * Method starts generating list of tabs for vertical menu
     */
    private function renderListVerticalMenu()
    {
        $this->_orderBy = 'position';
        $this->position_identifier = 'position';
        $this->identifier = 'id_tab';
        $this->initToolbar();

        $this->fields_list = array(
            'name' => array(
                'title' => $this->module->l('Name', 'AdminMenuSettings'),
                'orderby' => false,
            ),
            'active' => array(
                'title' => $this->module->l('Displayed', 'AdminMenuSettings'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->module->l('Tab position', 'AdminMenuSettings'),
                'position' => 'position'
            ),
            'tab_position' => array(
                'title' => $this->module->l('Tab position', 'AdminMenuSettings'),
                'orderby' => false,
            ),
        );

        return parent::renderList();
    }

    /**
     * Method starts generating main confing page for vertical menu
     */
    private function renderConfigVerticalMenu()
    {
        $this->submit_action = 'vertical_menu_settings_submit';
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Settings', 'AdminMenuSettings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'layout_picker',
                    'name' => 'menu_layout_holder',
                    'label' => $this->module->l('Tabs layout ', 'AdminMenuSettings'),
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable google fonts', 'AdminMenuSettings'),
                    'name' => 'google_fonts_vertical',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font family', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: \"Pathway Gothic One\", sans-serij", "AdminMenuSettings"),
                    'name' => 'menu_top_links_font',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font url', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: http://fonts.googleapis.com/css?family=Pathway+Gothic+One", "AdminMenuSettings"),
                    'name' => 'menu_top_links_font_url',
                    'disabled' => true,
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs font size:', 'AdminMenuSettings'),
                    'hint' => $this->module->l('This size will be set to the most top links in menu "tabs links"', 'AdminMenuSettings'),
                    'name' => 'menu_top_links_font_size',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                $this->addEffects('menuAnimateIn', $this->module->l('Select menu entrance animation', 'AdminMenuSettings')),
                $this->addEffects('menuAnimateOut', $this->module->l('Select menu exit animation', 'AdminMenuSettings')),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs line height:', 'AdminMenuSettings'),
                    'name' => 'menu_top_links_line_height',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs background color', 'AdminMenuSettings'),
                    'name' => 'menu_background_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs text color', 'AdminMenuSettings'),
                    'name' => 'text_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs icon color', 'AdminMenuSettings'),
                    'name' => 'icons_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs hover background color', 'AdminMenuSettings'),
                    'name' => 'tab_hover_background_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs separator color', 'AdminMenuSettings'),
                    'name' => 'links_separator_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tabs text and icon hover color', 'AdminMenuSettings'),
                    'name' => 'foreground_hover_color',
                    'desc' => $this->module->l('Note that tabs are called the most top links in the menu.', 'AdminMenuSettings'),
                    'size' => 30,
                ),
                array(
                    'type' => 'html',
                    'html_content' => '<h3>'.$this->module->l('Only for advance users', 'AdminMenuSettings').'</h3>',
                    'label' => $this->l(' '),
                    'form_group_class' => 'html-part',
                    'col' => '12',
                    'name' => '',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab wrapper 100% width on screens >= 1200px', 'AdminMenuSettings'),
                    'name' => 'tab_wrapper_w_1200',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab wrapper 100% width on screens >= 992px', 'AdminMenuSettings'),
                    'name' => 'tab_wrapper_w_992',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab wrapper 100% width on screens >= 768px', 'AdminMenuSettings'),
                    'name' => 'tab_wrapper_w_768',
                    'suffix' => 'px',
                    'size' => 30,
                ),
            ),
            'buttons' => array(
                'save' => array(
                    'title' => $this->module->l('Save', 'AdminMenuSettings'),
                    'name' => 'vertical_menu_settings_submit',
                    'type' => 'button',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        $this->tpl_form_vars['fields_value'] = unserialize(Configuration::get('FINALm_VERTICAL_CONFIG', null, $this->shop_group_id, $this->shop_id));

        return parent::renderForm();
    }

    /**
     * First method which triggers settings page generation
     * @return html settings page
     */
    private function renderMobileMenu()
    {
        $this->table = 'mobile_menu_tabs';
        AdminControllerCore::$currentIndex = 'index.php?controller=AdminMenuSettings&mobile_menu_tabs';

        return $this->renderListMobileMenu().$this->renderConfigMobileMenu();
    }

    /**
     * Method starts generating main list of tabs for horizontal menu
     */
    private function renderListMobileMenu()
    {
        $this->initToolbar();

        $this->fields_list = array(
            'name' => array(
                'title' => $this->module->l('Name', 'AdminMenuSettings'),
                'orderby' => false,
            ),
            'active' => array(
                'title' => $this->module->l('Displayed', 'AdminMenuSettings'),
                'width' => 25,
                'active' => 'status',
                'align' => 'center',
                'type' => 'bool',
                'orderby' => false
            ),
            'position' => array(
                'title' => $this->module->l('Tab position', 'AdminMenuSettings'),
                'position' => 'position'
            ),
        );

        return parent::renderList();
    }

    /**
     * Method starts generating main confing page for horizontal menu
     */
    private function renderConfigMobileMenu()
    {
        $this->submit_action = 'mobile_menu_settings_submit';

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Settings', 'AdminMenuSettings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Menu background color', 'AdminMenuSettings'),
                    'name' => 'FINALm_bg_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Submenu background color', 'AdminMenuSettings'),
                    'name' => 'FINALm_submenu_bg_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Icon color', 'AdminMenuSettings'),
                    'name' => 'FINALm_icon_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Text color', 'AdminMenuSettings'),
                    'name' => 'FINALm_text_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Text and icons hover color', 'AdminMenuSettings'),
                    'name' => 'FINALm_text_hover_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Links background hover color', 'AdminMenuSettings'),
                    'name' => 'FINALm_background_hover_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enable google fonts', 'AdminMenuSettings'),
                    'name' => 'FINALm_google_fonts',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font family', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: \"Pathway Gothic One\", sans-serij", "AdminMenuSettings"),
                    'name' => 'FINALm_links_font',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Font url', 'AdminMenuSettings'),
                    'desc' => $this->module->l("Example: http://fonts.googleapis.com/css?family=Pathway+Gothic+One", "AdminMenuSettings"),
                    'name' => 'FINALm_links_font_url',
                    'disabled' => true,
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs font size:', 'AdminMenuSettings'),
                    'hint' => $this->module->l('This size will be set to the most top links in menu "tabs links"', 'AdminMenuSettings'),
                    'name' => 'FINALm_links_font_size',
                    'suffix' => 'px',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tabs line height:', 'AdminMenuSettings'),
                    'name' => 'FINALm_links_line_height',
                    'suffix' => 'px',
                    'size' => 30,
                ),
            ),
            'buttons' => array(
                'save' => array(
                    'title' => $this->module->l('Save', 'AdminMenuSettings'),
                    'name' => 'mobile_menu_settings_submit',
                    'type' => 'submit',
                    'class' => 'btn btn-default pull-right',
                    'icon' => 'process-icon-save'
                )
            )
        );

        $this->tpl_form_vars['fields_value'] = unserialize(Configuration::get('FINALm_MOBILE_CONFIG',null, $this->shop_group_id, $this->shop_id));

        return parent::renderForm();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // TAB SETTINGS PAGE GENERATION
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    // TAB FOR HORIZONTAL, VERTICAL MENU
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generates horizontal, vertical tab settings page
     */
    private function renderDesktopTabSettingsPage($tab_type)
    {
        $hide = "";
        if ($tab_type == "vertical") {
            $hide = 'hide-for-vertical';
        }

        $this->addJS(array(
            _MODULE_DIR_ . $this->module->name . '/js/admin/finalmenu_new_functions.js',
            _MODULE_DIR_ . $this->module->name . '/js/admin/finalmenu_horVer_tab_settings.js'
            ));
        if ($object = $this->loadObject(true)) {
            $settings_json_object = $object->getSettings();
            $tab_object = json_decode($settings_json_object, true);

            // check for backup compatibility
            if ($tab_object['blocks'] !== null && !$this->isAssoc($tab_object['blocks'])) {
               $tab_object = $this->updateArray($tab_object);
            }

            // sorting block by order index
            if (!empty($tab_object['blocks']) && array_key_exists("order_index", current($tab_object['blocks']))) {
                 uasort($tab_object['blocks'], 'finalmenu::comparator');
            }
            $this->tpl_form_vars = $tab_object;

            // this is object version used by JS
            $this->tpl_form_vars['tab_object'] = htmlentities($settings_json_object);
        }

        $this->tpl_form_vars['adv_view_cms_options'] = $this->getCMSOptions();
        $this->tpl_form_vars['adv_view_cat_options'] = $this->generateCategoriesOption(Category::getNestedCategories(null, $this->lang_id, true));
        $this->tpl_form_vars['adv_view_man_options'] = $this->generateManOptions(Manufacturer::getManufacturers(false, $this->lang_id));
        $this->tpl_form_vars['adv_view_sup_options'] = $this->generateSupOptions(Supplier::getSuppliers(false, $this->lang_id));
        $this->tpl_form_vars['adv_view_available_cms_pages'] = $this->getCMSOptions(0, 1, 'null', true);
        $this->tpl_form_vars['smp_view_available_options'] = $this->generateAvailableOptions('null');

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Settings', 'AdminMenuSettings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab name', 'AdminMenuSettings'),
                    'required' => true,
                    'lang' => true,
                    'class' => 'col-lg-5',
                    'name' => 'name',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab link', 'AdminMenuSettings'),
                    'desc' => $this->module->l('Optional tab link. Can be omitted.', 'AdminMenuSettings'),
                    'lang' => true,
                    'class' => 'col-lg-5',
                    'name' => 'tab_link',
                    'size' => 30,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('New window for link', 'AdminMenuSettings'),
                    'name' => 'link_window',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type'      => 'radio',
                    'label'     => $this->module->l('Tab position', 'AdminMenuSettings'),
                    'name'      => 'tab_position',
                    'form_group_class' => $hide,
                    'values'    => array(
                        array(
                            'id'    => 'active_on',
                            'value' => 'right',
                            'label' => $this->module->l('Right', 'AdminMenuSettings')
                        ),
                        array(
                            'id'    => 'active_off',
                            'value' => 'left',
                            'label' => $this->module->l('Left', 'AdminMenuSettings')
                        )
                    ),
                ),
                array(
                    'type' => 'icon_picker',
                    'name' => 'tab_icon',
                    'label' => $this->module->l('Pick custom icon', 'AdminMenuSettings'),
                ),
                array(
                    'type' => 'image_upload',
                    'label' => $this->module->l('Or upload own image', 'AdminMenuSettings'),
                    'desc' => $this->module->l('Recommended size is 24x24px', 'AdminMenuSettings'),
                    'name' => 'tab_image',
                    'buttonLabel' => $this->module->l('Upload image', 'AdminMenuSettings'),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Tab note', 'AdminMenuSettings'),
                    'desc' => $this->module->l('Optional, can be omitted.', 'AdminMenuSettings'),
                    'hint' => $this->module->l('Small text above tab name.', 'AdminMenuSettings'),
                    'lang' => true,
                    'class' => 'col-lg-5',
                    'name' => 'tab_note',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Tab note background color', 'AdminMenuSettings'),
                    'name' => 'tab_note_bg_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Links color', 'AdminMenuSettings'),
                    'name' => 'links_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Text color', 'AdminMenuSettings'),
                    'name' => 'other_text_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'color',
                    'label' => $this->module->l('Links hover color', 'AdminMenuSettings'),
                    'name' => 'links_hover_color',
                    'size' => 30,
                ),
                array(
                    'type' => 'select',
                    'label' => $this->module->l('Select tab view', 'AdminMenuSettings'),
                    'name' =>  'type',
                    'class' => 'tab-view-select',
                    'options' => array(
                        'query' => array(
                            array(
                                'id_option' => 0,
                                'name' => $this->module->l('Advanced view', 'AdminMenuSettings')
                            ),
                            array(
                                'id_option' => 1,
                                'name' => $this->module->l('Simple view', 'AdminMenuSettings')
                            )
                        ),
                        'id' => 'id_option',
                        'name' => 'name'
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save', 'AdminMenuSettings')
            )
        );

        if (Shop::isFeatureActive())
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->module->l('Shop association:', 'AdminMenuSettings'),
                'name' => 'checkBoxShopAsso',
            );

        return parent::renderForm();
    }

    // HELPER METHODS
    //////////////////////////////////////////////

    /**
     * Updates array depending on previous check
     */
    private function updateArray($tab_object)
    {
        $keys = array();
        $arrayLength = sizeOf($tab_object['blocks']);

        for($i = 0; $i < $arrayLength; $i++)
            $keys[] = $tab_object['blocks'][$i]['name'];

        $tab_object['blocks'] = array_combine($keys, array_values($tab_object['blocks']));

        return $tab_object;
    }

    /**
     * Check whether array is associative
     */
    private function isAssoc(array $array)
    {
        return array_keys(array_merge($array)) !== range(0, count($array) - 1);
    }

    // TAB FOR MOBILE MENU
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generates mobile tab settings page
     */
    private function renderMobileTabSettingsPage()
    {
        $selected = false;
        if($object = $this->loadObject(true))
            $selected = $object->name;

        $this->tpl_form_vars['mobile_menu_available_options'] = $this->generateAvailableOptions($selected);

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->module->l('Settings', 'AdminMenuSettings'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'mobile_select_box',
                    'label' => $this->module->l('Select tab view ', 'AdminMenuSettings'),
                    'name' => 'name',
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Show tab', 'AdminMenuSettings'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Insert product ID', 'AdminMenuSettings'),
                    'class' => 'col-lg-5',
                    'form_group_class' => 'product-select-show-box',
                    'name' => 'product_ID',
                    'size' => 30,
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Title', 'AdminMenuSettings'),
                    'lang' => true,
                    'class' => 'col-lg-5',
                    'form_group_class' => 'link-select-show-box',
                    'name' => 'link_title',
                ),
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Insert link', 'AdminMenuSettings'),
                    'lang' => true,
                    'class' => 'col-lg-5',
                    'form_group_class' => 'link-select-show-box',
                    'name' => 'link_url',
                    'size' => 30,
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('New window', 'AdminMenuSettings'),
                    'name' => 'link_new_window',
                    'form_group_class' => 'link-select-show-box',
                    'is_bool' => false,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Enabled', 'AdminMenuSettings')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('Disabled', 'AdminMenuSettings')
                        )
                    )
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save', 'AdminMenuSettings')
            )
        );

        if (Shop::isFeatureActive())
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->module->l('Shop association', 'AdminMenuSettings'),
                'name' => 'checkBoxShopAsso',
            );

        return parent::renderForm();
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // HELPER METHODS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * HORIZONTAL, VERTICAL MENU - ADVANCE VIEW - CMS LIST
     * Generates avaible CMS categories and pages
     * @param integer $parent         parent ID
     * @param integer $depth          CMS level
     * @param string  $selected       selected CMS item
     * @param boolean $only_cms_pages
     */
    private function getCMSOptions($parent = 0, $depth = 1, $selected = 'null', $only_cms_pages = false)
    {
        $html = '';
        if(!$only_cms_pages)
            $categories = $this->getCMSCategories(false, $parent);

        $pages = $this->getCMSPages($parent, $only_cms_pages);

        $spacer = str_repeat('&nbsp;', $this->spacer_size * $depth);

        if (!$only_cms_pages) {
            foreach ($categories as $category) {
                $name = 'CMS_CAT'.$category['id_cms_category'];
                $selected = (($selected == $name) ? 'selected' : '');
                $html .= "<option {$selected} value='{$name}' style='font-weight: bold;'>{$spacer}{$category['name']}</option>";
                $html .= $this->getCMSOptions($category['id_cms_category'], (int) $depth + 1, $selected);
            }
        }

        foreach ($pages as $page) {
            $name = 'CMS'.$page['id_cms'];
            $selected = (($selected == $name) ? 'selected' : '');
            $html .= "<option {$selected} value='{$name}'>{$spacer}{$page['meta_title']}</option>";
        }

        return $html;
    }

    /**
     * HORIZONTAL, VERTICAL MENU - ADVANCE VIEW - AVAILABLE CATEGORIES
     * Depending on input generates available options
     * @param array  $categories array of generated categories
     * @param string $selected   selected category
     */
    private function generateCategoriesOption($categories, $selected = 'null')
    {
        $html = '';

        foreach ($categories as $key => $category) {
            $shop = (object) Shop::getShop((int) $category['id_shop']);
            $name = 'CAT'.(int) $category['id_category'];
            $selected = (($selected == $name) ? 'selected' : '');
            $preffix = str_repeat('&nbsp;', $this->spacer_size * (int) $category['level_depth']);

            $html .= "<option {$selected} value='{$name}' id='{$name}'>{$preffix}{$category['name']} ({$shop->name})</option>";
            if (isset($category['children']) && !empty($category['children']))
                $html .= $this->generateCategoriesOption($category['children'], $selected);
        }

        return $html;
    }

    /**
     * HORIZONTAL, VERTICAL MENU - ADVANCE VIEW -  MANUFACTURES
     * Depending on input generates available manufacturers options
     * @param array $Mans array of manufacturers
     */
    private function generateManOptions($Mans, $selected = 'null')
    {
        $html = '';
        $spacer = str_repeat('&nbsp;', $this->spacer_size);
        foreach ($Mans as $man) {
            $manID = "MAN{$man['id_manufacturer']}";
            $slc = (($selected == $manID) ? 'selected' : '');
            $html .= "<option {$slc} value='{$manID}' id='{$manID}'>{$spacer}{$man['name']}</option>";
        }

        return $html;
    }
    /**
     * HORIZONTAL, VERTICAL MENU - ADVANCE VIEW -  SUPPLIERS
     * Depending on input generates available supliers options
     * @param array $Sups array of suppliers
     */
    private function generateSupOptions($Sups, $selected = 'null')
    {
        $html = '';
        $spacer = str_repeat('&nbsp;', $this->spacer_size);
        foreach ($Sups as $sup) {
            $supID = "SUP{$sup['id_supplier']}";
            $slc = (($selected == $supID) ? 'selected' : '');
            $html .= "<option {$slc} value='{$supID}' id='{$supID}'>{$spacer}{$sup['name']}</option>";
        }

        return $html;
    }

    /**
     * MOBILE MENU - SELECT BOX
     * Generates available options for mobile menu select box
     * @param string $selected selected item
     */
    private function generateAvailableOptions($selected = 'null')
    {
        $spacer = str_repeat('&nbsp;', $this->spacer_size);

        $html = "<optgroup label='{$this->module->l('CMS', 'AdminMenuSettings')}'>";
        $html .= $this->getCMSOptions(0, 1, $selected);
        $html .= '</optgroup>';

        // BEGIN SUPPLIER
        $html .= "<optgroup label='{$this->module->l('Supplier', 'AdminMenuSettings')}'>";
        // Option to show all Suppliers
        $selected_sup = (($selected == 'ALLSUP0') ? 'selected' : '');
        $html .= "<option {$selected_sup} id='ALLSUP0' value='ALLSUP0'>{$this->module->l('All suppliers', 'AdminMenuSettings')}</option>";
        $html .= $this->generateSupOptions(Supplier::getSuppliers(false, $this->lang_id), $selected);
        $html .= '</optgroup>';

        // BEGIN Manufacturer
        $html .= "<optgroup label='{$this->module->l('Manufacturer', 'AdminMenuSettings')}'>";
        // Option to show all Manufacturers
        $selected_cat = (($selected == 'ALLMAN0') ? 'selected' : '');
        $html .= "<option {$selected_cat} value='ALLMAN0' id='ALLMAN0'>{$this->module->l('All manufacturers', 'AdminMenuSettings')}</option>";
        $html .= $this->generateManOptions(Manufacturer::getManufacturers(false, $this->lang_id), $selected);
        $html .= '</optgroup>';

        // BEGIN Categories
        $shop = new Shop((int) Shop::getContextShopID());
        $html .= "<optgroup label='{$this->module->l('Categories', 'AdminMenuSettings')}'>";
        $html .= $this->generateCategoriesOption(Category::getNestedCategories(null, (int) $this->lang_id, true), $selected);
        $html .= '</optgroup>';

        // BEGIN Shops
        if (Shop::isFeatureActive()) {
            $html .= "<optgroup label='{$this->module->l('Shops', 'AdminMenuSettings')}'>";
            $shops = Shop::getShopsCollection();
            foreach ($shops as $shop) {
                if (!$shop->setUrl() && !$shop->getBaseURL())
                    continue;
                    $name = 'SHOP'.(int) $shop->id;
                    $selected = (($selected == $name) ? 'selected' : '');
                    $html .= "<option {$selected} value='{$name}' id='{$name}'>{$spacer}{$shop->name}</option>";
            }
            $html .= '</optgroup>';
        }

        // BEGIN Product
        $selected = (($selected == 'PRODUCT') ? 'selected' : '');
        $html .= "<optgroup label='{$this->module->l('Product', 'AdminMenuSettings')}'>";
        $html .= "<option id='product-select' {$selected} value='PRODUCT' id='PRODUCT' style='font-style:italic'>{$spacer}{$this->module->l('Choose product ID', 'AdminMenuSettings')}</option>";
        $html .= '</optgroup>';

        // BEGIN Link
        $selected = (($selected == 'LNK') ? 'selected' : '');
        $html .= "<optgroup label='{$this->module->l('Link', 'AdminMenuSettings')}'>";
        $html .= "<option id='link-select' {$selected} value='LNK' id='LNK' style='font-style:italic'>{$spacer}{$this->module->l('Choose custom link', 'AdminMenuSettings')}</option>";
        $html .= "</optgroup>";

        return $html;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // DATABASE QUERIES
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Selects CMS categories from DB depending their parent and language
     * @param  int   $parent parent ID
     * @return array selected CMS categories
     */
    private function getCMSCategories($recursive = false, $parent = 1)
    {
        if ($recursive === false) {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
                FROM `' . _DB_PREFIX_ . 'cms_category` bcp
                INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
                ON (bcp.`id_cms_category` = cl.`id_cms_category`)
                WHERE cl.`id_lang` = ' . $this->lang_id . '
                AND bcp.`id_parent` = ' . (int) $parent;

            return Db::getInstance()->executeS($sql);
        } else {
            $sql = 'SELECT bcp.`id_cms_category`, bcp.`id_parent`, bcp.`level_depth`, bcp.`active`, bcp.`position`, cl.`name`, cl.`link_rewrite`
                FROM `' . _DB_PREFIX_ . 'cms_category` bcp
                INNER JOIN `' . _DB_PREFIX_ . 'cms_category_lang` cl
                ON (bcp.`id_cms_category` = cl.`id_cms_category`)
                WHERE cl.`id_lang` = ' . $this->lang_id . '
                AND bcp.`id_parent` = ' . (int) $parent;
            $results = Db::getInstance()->executeS($sql);

            foreach ($results as $result) {
                $sub_categories = $this->getCMSCategories(true, $result['id_cms_category']);
                if ($sub_categories && count($sub_categories) > 0)
                    $result['sub_categories'] = $sub_categories;
                $categories[] = $result;
            }

            return isset($categories) ? $categories : false;
        }
    }

    /**
     * Selects CMS pages from DB depending on CMS category ID, shop ID and current language
     * @param  int   $id_cms_category CMS category ID
     * @return array selected CMS pages
     */
    private function getCMSPages($id_cms_category = 0, $all_categories = false)
    {
        $sql = 'SELECT c.`id_cms`, cl.`meta_title`, cl.`link_rewrite`
                FROM `' . _DB_PREFIX_ . 'cms` c
                INNER JOIN `' . _DB_PREFIX_ . 'cms_shop` cs
                ON (c.`id_cms` = cs.`id_cms`)
                INNER JOIN `' . _DB_PREFIX_ . 'cms_lang` cl
                ON (c.`id_cms` = cl.`id_cms`)
                WHERE cs.`id_shop` = '. $this->shop_id .'
                AND cl.`id_lang` = '. $this->lang_id .'
                AND c.`active` = 1';

        if(!$all_categories)
            $sql .= ' AND c.`id_cms_category` = ' . (int) $id_cms_category . '';

            $sql .= ' ORDER BY `position`';

        return Db::getInstance()->executeS($sql);
    }
}
