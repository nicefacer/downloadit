<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 14:24:42
         compiled from "/homepages/40/d657041287/htdocs/modules/finalmenu/top_menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:81347422768442f8a11e474-73056113%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ab123f25e0f67d7870a5e7aaefe023af0d110339' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/finalmenu/top_menu.tpl',
      1 => 1738189376,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '81347422768442f8a11e474-73056113',
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
  'unifunc' => 'content_68442f8a14d259_93208196',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68442f8a14d259_93208196')) {function content_68442f8a14d259_93208196($_smarty_tpl) {?><div class="clearfix"></div>
<nav id="FINALmenu">
    <div class="<?php if ($_smarty_tpl->tpl_vars['wide']->value==1) {?>container-fluid<?php } else { ?>container<?php }?>">
        <?php echo $_smarty_tpl->tpl_vars['FINALmenu']->value;?>

    </div>
</nav><?php }} ?>
