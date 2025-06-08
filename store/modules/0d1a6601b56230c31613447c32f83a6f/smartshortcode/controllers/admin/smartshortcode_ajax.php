<?php
defined('_PS_VERSION_') OR die('No Direct Script Access Allowed');
/**
 * Description of ajax
 *
 * @author Smartdatasoft 
 * @todo Description
 * @link http://smartdatasoft.net description
 * @version v2.1.1
 * @copyright (c) 2013, Smart Datasoft
 * @license http://URL name 
 */

class Smartshortcode_ajaxController extends ModuleAdminController 
{
    /**
     *
     * @var array
     */
    protected $_ajax_results;
    /**
     *
     * @var type 
     */
    protected $_ajax_stripslash;
    /**
     *
     * @var type 
     */
    protected $_filter_whitespace;
    /**
     *
     * @var Lushslider_model
     */
    protected $lushslider_model;
    /**
     * {@inheritdoc}
     */
    public function __construct() 
    {        
        $this->display_header = false;
        $this->display_footer = false;
        $this->content_only   = true;
        //$this->bindToAjaxRequest();        
        parent::__construct();
        $this->_ajax_results['error_on'] = 1; 
        // Let's include Lushslider Model
        
    }
    public function init()
    {        

        // Process POST | GET
        $this->initProcess();
    }
    /**
     * 
     * @throws Exception
     */
    public function initProcess()
    {
       
        
        if(!Tools::getValue('secure_key') || Tools::getValue('secure_key') !== Tools::encrypt('smartshortcode')) die('wrong action.');
        
        $iframedir = _PS_MODULE_DIR_.'/smartshortcode/plugins/shortcode/iframes';

        $action = Tools::getValue('smartShortcodeAction');
        $mainaction = Tools::getValue('tinymceAction');
        
        $admin_url = $_SERVER['REQUEST_URI'];
        $admin_url = substr($admin_url,0,strpos($admin_url,'index.php'));
        $admin_url = Tools::getHttpHost(true).$admin_url;
        $admin_url .= Context::getContext()->link->getAdminLink('Smartshortcode_ajax',false);
        $admin_url .= '&secure_key='.Tools::encrypt('smartshortcode');
        
        
        //work here
        switch($mainaction){
            case 'tinymcejs':
                header('Content-type: text/javascript');
                ?>
/* Adapted from http://brettterpstra.com/adding-a-tinymce-button/ */

(function() {
    tinyMCE.create('tinymce.plugins.shortcode', {
        init : function(ed, url) {
            
            ed.addButton('shortcode', {
                title : 'Short Code',
              
                image : '<?php echo _MODULE_DIR_?>smartshortcode/plugins/shortcode/dropcap.jpg',
                
                onclick : function() {

                ed.windowManager.open({
                        
                        title: 'Smart Short Code Module',

						file : '<?php echo $admin_url?>&tinymceAction=tinymcemarkup',

						width : 900,

						height : 450,

						inline : 1

					});
	}
            });
 

        },
        // ... Hidden code
    });
    // Register plugin
    tinyMCE.PluginManager.add( 'shortcode', tinyMCE.plugins.shortcode );
})();
                <?php
                break;
            case 'tinymcemarkup':                
                require_once(_PS_MODULE_DIR_.'/smartshortcode/lib/tinymce_shortcodes.php');
                break;
            
            default :
                
            switch($action){
                case 'sdsdsdsdsdsdsdsds':
                    break;
                default :
                    Hook::exec('sdsShortcodeAdminPages');
                    break;
            }
            
            break;
        }
        
        
        die();
        
    }
    
}

