<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:07:17
         compiled from "/homepages/40/d657041287/htdocs/modules/finalmenu/top_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1402025463667d8025ad2586-93050006%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab123f25e0f67d7870a5e7aaefe023af0d110339' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/finalmenu/top_menu.tpl',
      1 => 1482272108,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1402025463667d8025ad2586-93050006',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'wide' => 0,
    'FINALmenu' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d8025ad4d68_48665847',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d8025ad4d68_48665847')) {function content_667d8025ad4d68_48665847($_smarty_tpl) {?><div class="clearfix"></div>
<nav id="FINALmenu">
    <div class="<?php if ($_smarty_tpl->tpl_vars['wide']->value==1) {?>container-fluid<?php } else { ?>container<?php }?>">
        <?php echo $_smarty_tpl->tpl_vars['FINALmenu']->value;?>

    </div>
</nav><?php }} ?>
