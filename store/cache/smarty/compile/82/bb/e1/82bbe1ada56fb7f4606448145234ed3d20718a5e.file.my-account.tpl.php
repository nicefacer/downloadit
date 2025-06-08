<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 11:26:25
         compiled from "/homepages/40/d657041287/htdocs/themes/default-bootstrap/modules/mailalerts/views/templates/hook/my-account.tpl" */ ?>
<?php /*%%SmartyHeaderCode:609910338684405c16c1160-47401195%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '82bbe1ada56fb7f4606448145234ed3d20718a5e' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/themes/default-bootstrap/modules/mailalerts/views/templates/hook/my-account.tpl',
      1 => 1738190388,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '609910338684405c16c1160-47401195',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_684405c16c4dd3_50429767',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_684405c16c4dd3_50429767')) {function content_684405c16c4dd3_50429767($_smarty_tpl) {?>

<li class="mailalerts">
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('mailalerts','account',array(),true), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'My alerts','mod'=>'mailalerts'),$_smarty_tpl);?>
" rel="nofollow">
    	<i class="icon-envelope"></i>
		<span><?php echo smartyTranslate(array('s'=>'My alerts','mod'=>'mailalerts'),$_smarty_tpl);?>
</span>
	</a>
</li>
<?php }} ?>
