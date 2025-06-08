<?php /* Smarty version Smarty-3.1.19, created on 2025-06-03 18:26:00
         compiled from "/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/content.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1017284452683f22184b5da7-06625209%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a2197870d921b3751d9ff2c896ffaa0fbc9d21b3' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/content.tpl',
      1 => 1738184536,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1017284452683f22184b5da7-06625209',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'content' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_683f221857ac72_17710221',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_683f221857ac72_17710221')) {function content_683f221857ac72_17710221($_smarty_tpl) {?>
<div id="ajax_confirmation" class="alert alert-success hide"></div>

<div id="ajaxBox" style="display:none"></div>


<div class="row">
	<div class="col-lg-12">
		<?php if (isset($_smarty_tpl->tpl_vars['content']->value)) {?>
			<?php echo $_smarty_tpl->tpl_vars['content']->value;?>

		<?php }?>
	</div>
</div>
<?php }} ?>
