<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 11:26:25
         compiled from "/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/column.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1690240054684405c173a342-76222340%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'de1af084192c8d9810def8c550777fd81de54a47' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/column.tpl',
      1 => 1740773531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1690240054684405c173a342-76222340',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'base_dir_ssl' => 0,
    'logo' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_684405c173cb97_05556812',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_684405c173cb97_05556812')) {function content_684405c173cb97_05556812($_smarty_tpl) {?>

<div id="paypal-column-block">
	<p><a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['base_dir_ssl']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
modules/paypal/about.php" rel="nofollow"><img src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['logo']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" alt="PayPal" title="<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>
" style="max-width: 100%" /></a></p>
</div>
<?php }} ?>
