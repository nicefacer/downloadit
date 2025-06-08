<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class AdminShortCodeSettingController extends AdminController
{
  
  public function __construct()
  {
    $this->bootstrap = true;
    $this->className = 'Configuration';
    $this->table = 'configuration';

    parent::__construct();
        
      $arr = array();
      $tabarr = array();
      $arr[] = array('id' => 'theme', 'name' => 'Theme');
      $arr[] = array('id' => 'module', 'name' => 'Module');
      $tabarr[] = array('id' => 'tab', 'name' => 'Tab View');
      $tabarr[] = array('id' => 'general', 'name' => 'General View');

    $this->fields_options = array(
      'email' => array(
        'title' => $this->l('General Setting for Shortcode'),
        'icon' => 'icon-cogs',
        'fields' => array(
          'smart_load_font' => array(
            'title' => $this->l('Load Font Awesome From:'), 
            'desc' => $this->l('if you want to load font Awesome from your theme or module.'), 
            'validation' => 'isGenericName', 
            'type' => 'select',  
            'identifier' => 'id', 
            'list' => $arr
          ),
          'smart_load_bootstrapcss' => array(
            'title' => $this->l(' Load Bootstrap CSS From:'), 
            'desc' => $this->l('if you want to load Bootstrap CSS from your theme or module.'), 
            'validation' => 'isGenericName', 
            'type' => 'select',  
            'identifier' => 'id', 
            'list' => $arr
          ),
          'smart_load_bootstrapjs' => array(
            'title' => $this->l(' Load Bootstrap JS From:'), 
            'desc' => $this->l('if you want to load Bootstrap JS from your theme or module.'), 
            'validation' => 'isGenericName', 
            'type' => 'select',  
            'identifier' => 'id', 
            'list' => $arr
          ),
          'smart_shortcode_tab_style' => array(
            'title' => $this->l('Product Tab Style'), 
            'desc' => $this->l('You can change your product tab style'), 
            'validation' => 'isGenericName', 
            'type' => 'select',  
            'identifier' => 'id', 
            'list' => $tabarr
          ),
        ),
        'submit' => array('title' => $this->l('Save'))
      ),
    );
    ksort($this->fields_options['email']['fields']);
  }
  public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
            $this->page_header_toolbar_btn['tinymce_shortcodesetting'] = array(
                'href' => self::$currentIndex.'&tinymce'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Normal Tinymce', null, null, false),
                'icon' => 'process-icon-export'
            );
            $this->page_header_toolbar_btn['tinymceex_shortcodesetting'] = array(
                'href' => self::$currentIndex.'&tinymceex'.$this->table.'&token='.$this->token,
                'desc' => $this->l('Extended Tinymce', null, null, false),
                'icon' => 'process-icon-export'
            );
  }
  public function init()
  {
      parent::init();
      if(Tools::isSubmit('tinymceconfiguration')){
          $this->storfile(false);            
      }
      if(Tools::isSubmit('tinymceexconfiguration')){
          $this->storfile(true);            
      }
  }
  public function storfile($ex)
  {
    if($ex == true){
      if(file_exists(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js")){
          @copy(dirname(__FILE__) ."/../../js/lib/ex/tinymce.inc.js",_PS_ROOT_DIR_."/js/admin/tinymce.inc.js");
      }else{
          @copy(dirname(__FILE__) . "/../../js/lib/ex/tinymce.inc.js",_PS_ROOT_DIR_."/js/tinymce.inc.js");
      }
    }else{
      if(file_exists(_PS_ROOT_DIR_."/js/admin/tinymce.inc.js")){
           @copy(dirname(__FILE__) . "/../../js/lib/tinymce.inc.js",_PS_ROOT_DIR_."/js/admin/tinymce.inc.js");
      }else{
           @copy(dirname(__FILE__) . "/../../js/lib/tinymce.inc.js",_PS_ROOT_DIR_."/js/tinymce.inc.js");
      }
    }
  }
}
