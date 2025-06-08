<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 11:25:43
         compiled from "/homepages/40/d657041287/htdocs/modules/lgcookieslaw/views/templates/front/account_button_16.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6888999486844059763acd8-89077523%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '000de439633f748045719cb48c2764753770034f' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/lgcookieslaw/views/templates/front/account_button_16.tpl',
      1 => 1738189781,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6888999486844059763acd8-89077523',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lgcookieslaw_disallow_url' => 0,
    'lgcookieslaw_image_path' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_68440597669005_75049824',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68440597669005_75049824')) {function content_68440597669005_75049824($_smarty_tpl) {?>
<li class="lgcookies">
    <a href="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['lgcookieslaw_disallow_url']->value);?>
" title="<?php echo smartyTranslate(array('s'=>'Revoke my consent to cookies','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
">
        <img src="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['lgcookieslaw_image_path']->value);?>
" style="padding: 10px; float: left;">
        <span>
        <?php echo smartyTranslate(array('s'=>'Revoke my consent to cookies','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

        </span>
    </a>
</li>
<?php }} ?>
