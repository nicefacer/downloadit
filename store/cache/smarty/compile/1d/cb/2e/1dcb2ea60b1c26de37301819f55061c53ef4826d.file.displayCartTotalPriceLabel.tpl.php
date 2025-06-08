<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 12:35:32
         compiled from "/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/displayCartTotalPriceLabel.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2145239979684415f49e27d5-08863935%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '1dcb2ea60b1c26de37301819f55061c53ef4826d' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/displayCartTotalPriceLabel.tpl',
      1 => 1738189759,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2145239979684415f49e27d5-08863935',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'smartyVars' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_684415f49fb249_96200849',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_684415f49fb249_96200849')) {function content_684415f49fb249_96200849($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value)) {?>
    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['price'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['price']['tax_str_i18n'])) {?>
        <span class="aeuc_tax_label_shopping_cart">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['price']['tax_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </span>
    <?php }?>
<?php }?><?php }} ?>
