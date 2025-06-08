<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 14:24:44
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartshortcontent.tpl" */ ?>
<?php /*%%SmartyHeaderCode:161096685068442f8c6691b3-07578322%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1e4830e6aeef8c172947de8d3fbf22866b15eea3' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/smartshortcontent.tpl',
      1 => 1738189811,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '161096685068442f8c6691b3-07578322',
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
  'unifunc' => 'content_68442f8c69ebd9_09874929',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68442f8c69ebd9_09874929')) {function content_68442f8c69ebd9_09874929($_smarty_tpl) {?><?php  $_smarty_tpl->tpl_vars["res"] = new Smarty_Variable; $_smarty_tpl->tpl_vars["res"]->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['results']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars["res"]->key => $_smarty_tpl->tpl_vars["res"]->value) {
$_smarty_tpl->tpl_vars["res"]->_loop = true;
?>
<?php if ($_smarty_tpl->tpl_vars['res']->value['content']!='') {?><?php echo $_smarty_tpl->tpl_vars['res']->value['content'];?>
<?php }?>
<?php } ?><?php }} ?>
