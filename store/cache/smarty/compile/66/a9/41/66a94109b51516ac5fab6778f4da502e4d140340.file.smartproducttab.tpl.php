<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 14:24:43
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartproducttab.tpl" */ ?>
<?php /*%%SmartyHeaderCode:93208733968442f8befd392-76976142%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '66a94109b51516ac5fab6778f4da502e4d140340' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartproducttab.tpl',
      1 => 1738189810,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '93208733968442f8befd392-76976142',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'sds_results' => 0,
    'sds_result' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_68442f8bf2f296_00406312',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68442f8bf2f296_00406312')) {function content_68442f8bf2f296_00406312($_smarty_tpl) {?><?php if (configuration::get('smart_shortcode_tab_style')=='tab') {?>
	<?php  $_smarty_tpl->tpl_vars['sds_result'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sds_result']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sds_results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sds_result']->key => $_smarty_tpl->tpl_vars['sds_result']->value) {
$_smarty_tpl->tpl_vars['sds_result']->_loop = true;
?>
	   <div id="idsmartproducttab-<?php echo $_smarty_tpl->tpl_vars['sds_result']->value['id_smart_product_tab'];?>
">
	        <?php echo $_smarty_tpl->tpl_vars['sds_result']->value['content'];?>

	   </div>
	<?php } ?>
<?php }?><?php }} ?>
