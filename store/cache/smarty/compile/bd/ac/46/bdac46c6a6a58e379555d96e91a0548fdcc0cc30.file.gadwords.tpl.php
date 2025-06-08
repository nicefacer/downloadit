<?php /* Smarty version Smarty-3.1.19, created on 2025-05-16 16:18:07
         compiled from "/homepages/40/d657041287/htdocs/modules/gadwordsfree/views/templates/hook/gadwords.tpl" */ ?>
<?php /*%%SmartyHeaderCode:15536038166827491f0d89a8-62970123%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'bdac46c6a6a58e379555d96e91a0548fdcc0cc30' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/gadwordsfree/views/templates/hook/gadwords.tpl',
      1 => 1738189773,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '15536038166827491f0d89a8-62970123',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'GADWORDS_CONVERSION_TRACKING_ID' => 0,
    'GADWORDS_CONVERSION_TRACKING_LABEL' => 0,
    'LANG' => 0,
    'CURRENCY' => 0,
    'TOTAL_ORDER' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_6827491f0fc891_08242084',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6827491f0fc891_08242084')) {function content_6827491f0fc891_08242084($_smarty_tpl) {?>
<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('GADWORDS_CONVERSION_TRACKING_ID'=>htmlspecialchars($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_ID']->value, ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>

<?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['addJsDef'][0][0]->addJsDef(array('GADWORDS_CONVERSION_TRACKING_LABEL'=>htmlspecialchars($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_LABEL']->value, ENT_QUOTES, 'UTF-8', true)),$_smarty_tpl);?>


<!-- Google Code for conversion  module google adwords tracking -->

<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = <?php echo intval($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_ID']->value);?>
;
    var google_conversion_language = "<?php if (!empty($_smarty_tpl->tpl_vars['LANG']->value)) {?><?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['LANG']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php } else { ?>en<?php }?>";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_LABEL']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
";
    var google_conversion_currency = "<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['CURRENCY']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
";
    var google_conversion_value = <?php if (!empty($_smarty_tpl->tpl_vars['TOTAL_ORDER']->value)) {?><?php echo floatval($_smarty_tpl->tpl_vars['TOTAL_ORDER']->value);?>
<?php } else { ?>1.000000<?php }?>;
    var google_remarketing_only = false;
    /* ]]> */
</script>



    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/<?php echo intval($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_ID']->value);?>
/?value=<?php if (!empty($_smarty_tpl->tpl_vars['TOTAL_ORDER']->value)) {?><?php echo floatval($_smarty_tpl->tpl_vars['TOTAL_ORDER']->value);?>
<?php } else { ?>1.000000<?php }?><?php if (!empty($_smarty_tpl->tpl_vars['CURRENCY']->value)) {?>&amp;currency_code=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['CURRENCY']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
<?php }?>&amp;label=<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['GADWORDS_CONVERSION_TRACKING_LABEL']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>

<?php }} ?>
