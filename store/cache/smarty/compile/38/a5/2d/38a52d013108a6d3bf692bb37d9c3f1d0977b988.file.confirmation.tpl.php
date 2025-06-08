<?php /* Smarty version Smarty-3.1.19, created on 2025-05-12 09:03:26
         compiled from "/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/confirmation.tpl" */ ?>
<?php /*%%SmartyHeaderCode:46529600168219d3ed75264-88920471%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '38a52d013108a6d3bf692bb37d9c3f1d0977b988' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/confirmation.tpl',
      1 => 1740773531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '46529600168219d3ed75264-88920471',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'shop_name' => 0,
    'PayPal_payment_mode' => 0,
    'link' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_68219d3ed89f52_00115593',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68219d3ed89f52_00115593')) {function content_68219d3ed89f52_00115593($_smarty_tpl) {?>

<p><?php echo smartyTranslate(array('s'=>'Your order on','mod'=>'paypal'),$_smarty_tpl);?>
 <span class="paypal-bold"><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['shop_name']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
</span> <?php echo smartyTranslate(array('s'=>'is complete.','mod'=>'paypal'),$_smarty_tpl);?>

	<br /><br />
	<?php echo smartyTranslate(array('s'=>'You have chosen the PayPal method.','mod'=>'paypal'),$_smarty_tpl);?>

	<br /><br /><span class="paypal-bold">
		<?php if ($_smarty_tpl->tpl_vars['PayPal_payment_mode']->value) {?>
			<?php echo smartyTranslate(array('s'=>'Your order will be sent to you as soon as the payment is captured.','mod'=>'paypal'),$_smarty_tpl);?>

		<?php } else { ?>
            <?php echo smartyTranslate(array('s'=>'Your order will be sent very soon.','mod'=>'paypal'),$_smarty_tpl);?>

        <?php }?>
	</span>
	<br /><br /><?php echo smartyTranslate(array('s'=>'For any questions or for further information, please contact our','mod'=>'paypal'),$_smarty_tpl);?>

	<a href="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getPageLink('contact',true), ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" data-ajax="false" target="_blank"><?php echo smartyTranslate(array('s'=>'customer support','mod'=>'paypal'),$_smarty_tpl);?>
</a>.
</p>
<?php }} ?>
