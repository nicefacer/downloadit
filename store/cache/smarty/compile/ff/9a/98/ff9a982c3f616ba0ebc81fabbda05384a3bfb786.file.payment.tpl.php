<?php /* Smarty version Smarty-3.1.19, created on 2025-06-02 10:16:26
         compiled from "/homepages/40/d657041287/htdocs/themes/default-bootstrap/modules/bankwire/views/templates/hook/payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:394134311683d5dda72cf73-73452102%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ff9a982c3f616ba0ebc81fabbda05384a3bfb786' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/themes/default-bootstrap/modules/bankwire/views/templates/hook/payment.tpl',
      1 => 1738190391,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '394134311683d5dda72cf73-73452102',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_683d5dda75b5a7_13893852',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_683d5dda75b5a7_13893852')) {function content_683d5dda75b5a7_13893852($_smarty_tpl) {?>
<div class="row">
	<div class="col-xs-12">
		<p class="payment_module">
			<a class="bankwire" href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getModuleLink('bankwire','payment'), ENT_QUOTES, 'UTF-8', true);?>
" title="<?php echo smartyTranslate(array('s'=>'Pay by bank wire','mod'=>'bankwire'),$_smarty_tpl);?>
">
				<?php echo smartyTranslate(array('s'=>'Pay by bank wire','mod'=>'bankwire'),$_smarty_tpl);?>
 <span><?php echo smartyTranslate(array('s'=>'(order processing will be longer)','mod'=>'bankwire'),$_smarty_tpl);?>
</span>
			</a>
		</p>
	</div>
</div>
<?php }} ?>
