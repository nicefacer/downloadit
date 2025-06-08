<?php /* Smarty version Smarty-3.1.19, created on 2025-05-15 09:23:49
         compiled from "/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/helpers/list/list_action_preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1048936276825968584b113-33296393%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '30dba1111ec343183bb515ec671310de5a0bf11a' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/helpers/list/list_action_preview.tpl',
      1 => 1738184982,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1048936276825968584b113-33296393',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'href' => 0,
    'action' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_6825968584d185_53770391',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6825968584d185_53770391')) {function content_6825968584d185_53770391($_smarty_tpl) {?>
<a href="<?php echo $_smarty_tpl->tpl_vars['href']->value;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['action']->value, ENT_QUOTES, 'UTF-8', true);?>
" target="_blank">
	<i class="icon-eye"></i> <?php echo $_smarty_tpl->tpl_vars['action']->value;?>

</a>
<?php }} ?>
