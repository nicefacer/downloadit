<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:07:18
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartshortcontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2117967992667d80262cdfb1-50893930%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1e4830e6aeef8c172947de8d3fbf22866b15eea3' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartshortcontent.tpl',
      1 => 1482273770,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2117967992667d80262cdfb1-50893930',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'results' => 0,
    'res' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d80262d14d5_93463415',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d80262d14d5_93463415')) {function content_667d80262d14d5_93463415($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["res"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["res"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["res"]->key => $_smarty_tpl->tpl_vars["res"]->value) {
$_smarty_tpl->tpl_vars["res"]->_loop = true;
?>
<?php if ($_smarty_tpl->tpl_vars['res']->value['content']!='') {?><?php echo $_smarty_tpl->tpl_vars['res']->value['content'];?>
<?php }?>
<?php } ?><?php }} ?>
