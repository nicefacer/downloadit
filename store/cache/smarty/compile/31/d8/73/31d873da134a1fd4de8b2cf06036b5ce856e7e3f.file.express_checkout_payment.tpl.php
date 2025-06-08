<?php /* Smarty version Smarty-3.1.19, created on 2025-06-02 10:16:26
         compiled from "/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/express_checkout_payment.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1935905566683d5ddad07865-50900041%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '31d873da134a1fd4de8b2cf06036b5ce856e7e3f' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/paypal/views/templates/hook/express_checkout_payment.tpl',
      1 => 1740773531,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1935905566683d5ddad07865-50900041',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'use_paypal_in_context' => 0,
    'baseURI' => 0,
    'braintreeToken' => 0,
    'logos' => 0,
    'base_dir_ssl' => 0,
    'PayPal_payment_type' => 0,
    'PayPal_current_page' => 0,
    'PayPal_tracking_code' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_683d5ddadb9d46_47232555',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_683d5ddadb9d46_47232555')) {function content_683d5ddadb9d46_47232555($_smarty_tpl) {?>

<?php if (@constant('_PS_VERSION_')>=1.6) {?>

<div class="row">
	<div class="col-xs-12">
        <p class="payment_module paypal">
        	<?php if ($_smarty_tpl->tpl_vars['use_paypal_in_context']->value) {?>
				<a href="javascript:void(0)" onclick="" id="paypal_process_payment" title="<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>
">
			<?php } else { ?>
				<a href="javascript:$('#paypal_payment_form_payment').submit();" title="<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>
">
			<?php }?>

					<img
							src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['baseURI']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
modules/paypal/views/img/logos/FR_pp_cc_mark_74x46.jpg"
							alt="<?php echo smartyTranslate(array('s'=>'Pay with your card or your PayPal account','mod'=>'paypal'),$_smarty_tpl);?>
" />

                    <?php if (isset($_smarty_tpl->tpl_vars['braintreeToken']->value)) {?>
                    <?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>

                    <?php } else { ?>
					<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>

                    <?php }?>

			</a>
		</p>
    </div>
</div>

<style>
	p.payment_module.paypal a
	{
		padding-left:17px;
	}
</style>
<?php } else { ?>
<p class="payment_module">
		<a href="javascript:void(0)" id="paypal_process_payment" title="<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>
">
			<img
					src="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['logos']->value['LocalPayPalLogoMedium'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"
					alt="<?php echo smartyTranslate(array('s'=>'Pay with your card or your PayPal account','mod'=>'paypal'),$_smarty_tpl);?>
" />

			<?php echo smartyTranslate(array('s'=>'Pay with your card or your PayPal account','mod'=>'paypal'),$_smarty_tpl);?>


		</a>
</p>

<?php }?>


<?php if ($_smarty_tpl->tpl_vars['use_paypal_in_context']->value) {?>
	<input type="hidden" id="in_context_checkout_enabled" value="1">
<?php } else { ?>
<script>
    $(document).ready(function(){
        $(document).on('click', '#paypal_process_payment', function(){
            $('#paypal_payment_form_payment').submit();
        });
    });
</script>
<?php }?>
<form id="paypal_payment_form_payment" class="paypal_payment_form" action="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['base_dir_ssl']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
modules/paypal/express_checkout/payment.php" data-ajax="false" title="<?php echo smartyTranslate(array('s'=>'Pay with PayPal','mod'=>'paypal'),$_smarty_tpl);?>
" method="post">
	<input type="hidden" name="express_checkout" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['PayPal_payment_type']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"/>
	<input type="hidden" name="current_shop_url" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['PayPal_current_page']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" />
	<input type="hidden" name="bn" value="<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['PayPal_tracking_code']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
" />
</form><?php }} ?>
