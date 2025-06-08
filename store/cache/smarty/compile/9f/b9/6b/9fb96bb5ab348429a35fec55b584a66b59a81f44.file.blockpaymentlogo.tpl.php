<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 11:26:25
         compiled from "/homepages/40/d657041287/htdocs/modules/blockpaymentlogo/blockpaymentlogo.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1559525585684405c16e1636-08493096%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9fb96bb5ab348429a35fec55b584a66b59a81f44' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/blockpaymentlogo/blockpaymentlogo.tpl',
      1 => 1738189334,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1559525585684405c16e1636-08493096',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'cms_payement_logo' => 0,
    'link' => 0,
    'img_dir' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_684405c16e4602_28723036',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_684405c16e4602_28723036')) {function content_684405c16e4602_28723036($_smarty_tpl) {?>

<!-- Block payment logo module -->
<div id="paiement_logo_block_left" class="paiement_logo_block">
	<a href="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link']->value->getCMSLink($_smarty_tpl->tpl_vars['cms_payement_logo']->value), ENT_QUOTES, 'UTF-8', true);?>
">
		<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
logo_paiement_visa.jpg" alt="visa" width="33" height="21" />
		<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
logo_paiement_mastercard.jpg" alt="mastercard" width="32" height="21" />
		<img src="<?php echo $_smarty_tpl->tpl_vars['img_dir']->value;?>
logo_paiement_paypal.jpg" alt="paypal" width="61" height="21" />
	</a>
</div>
<!-- /Block payment logo module --><?php }} ?>
