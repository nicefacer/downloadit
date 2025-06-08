<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 14:24:43
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smart_product_tab_creator.tpl" */ ?>
<?php /*%%SmartyHeaderCode:187104084668442f8be9a3e2-21644112%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '33153c23908a41898441cf3923ca49ca9464f8d7' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smart_product_tab_creator.tpl',
      1 => 1738189810,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '187104084668442f8be9a3e2-21644112',
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
  'unifunc' => 'content_68442f8bed3546_23997761',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68442f8bed3546_23997761')) {function content_68442f8bed3546_23997761($_smarty_tpl) {?><?php if (configuration::get('smart_shortcode_tab_style')=='tab') {?>
	<?php if ($_smarty_tpl->tpl_vars['sds_results']->value!='') {?>
		<?php  $_smarty_tpl->tpl_vars['sds_result'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sds_result']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sds_results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sds_result']->key => $_smarty_tpl->tpl_vars['sds_result']->value) {
$_smarty_tpl->tpl_vars['sds_result']->_loop = true;
?>
			<li><a class="idTabHrefShort" href="#idsmartproducttab-<?php echo $_smarty_tpl->tpl_vars['sds_result']->value['id_smart_product_tab'];?>
"><?php echo $_smarty_tpl->tpl_vars['sds_result']->value['title'];?>
</a></li>
		<?php } ?>
	<?php }?>
<?php } else { ?>
	<?php if ($_smarty_tpl->tpl_vars['sds_results']->value!='') {?>
		<?php  $_smarty_tpl->tpl_vars['sds_result'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['sds_result']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['sds_results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['sds_result']->key => $_smarty_tpl->tpl_vars['sds_result']->value) {
$_smarty_tpl->tpl_vars['sds_result']->_loop = true;
?>
			<section class="page-product-box">
				<h3 class="page-product-heading"><?php echo $_smarty_tpl->tpl_vars['sds_result']->value['title'];?>
</h3>
				<div class="rte"><?php echo $_smarty_tpl->tpl_vars['sds_result']->value['content'];?>
</div>
			</section>
		<?php } ?>
	<?php }?>
<?php }?><?php }} ?>
