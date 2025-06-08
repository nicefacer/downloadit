<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 14:24:45
         compiled from "/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/hookDisplayProductPriceBlock.tpl" */ ?>
<?php /*%%SmartyHeaderCode:160848203068442f8dd4e9d2-16651333%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6a957e969a94c16cd1b53b81f4839ff402dad62e' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/hookDisplayProductPriceBlock.tpl',
      1 => 1738189759,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '160848203068442f8dd4e9d2-16651333',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'smartyVars' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_68442f8e06cda7_03798773',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68442f8e06cda7_03798773')) {function content_68442f8e06cda7_03798773($_smarty_tpl) {?>

<?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value)) {?>
    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['before_price'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['before_price']['from_str_i18n'])) {?>
        <span class="aeuc_from_label">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['before_price']['from_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </span>
    <?php }?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['old_price'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['old_price']['before_str_i18n'])) {?>
        <span class="aeuc_before_label">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['old_price']['before_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </span>
    <?php }?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['price'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['price']['tax_str_i18n'])) {?>
        <span class=<?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['price']['css_class'])) {?>
                        "<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['price']['css_class'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
"
                    <?php } else { ?>
                        "aeuc_tax_label"
                    <?php }?>>
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['price']['tax_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </span>
    <?php }?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['ship'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['ship']['link_ship_pay'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['ship']['ship_str_i18n'])) {?>
        <div class="aeuc_shipping_label">
            <a href="<?php echo $_smarty_tpl->tpl_vars['smartyVars']->value['ship']['link_ship_pay'];?>
" class="iframe">
                <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['ship']['ship_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

            </a>
        </div>
    <?php }?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['weight'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['weight']['rounded_weight_str_i18n'])) {?>
        <div class="aeuc_weight_label">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['weight']['rounded_weight_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </div>
    <?php }?>

    
    <?php if (isset($_smarty_tpl->tpl_vars['smartyVars']->value['after_price'])&&isset($_smarty_tpl->tpl_vars['smartyVars']->value['after_price']['delivery_str_i18n'])) {?>
        <div class="aeuc_delivery_label">
            <?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['smartyVars']->value['after_price']['delivery_str_i18n'], ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>

        </div>
    <?php }?>
<?php }?><?php }} ?>
