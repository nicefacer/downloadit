<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:06:55
         compiled from "/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/helpers/modules_list/modal.tpl" */ ?>
<?php /*%%SmartyHeaderCode:152209271667d800f19ff49-76776168%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a051e3ce8dfd300c6cf9151b9341625549d94bca' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/helpers/modules_list/modal.tpl',
      1 => 1603055818,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '152209271667d800f19ff49-76776168',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d800f1a0d43_17995310',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d800f1a0d43_17995310')) {function content_667d800f1a0d43_17995310($_smarty_tpl) {?><div class="modal fade" id="modules_list_container">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h3 class="modal-title"><?php echo smartyTranslate(array('s'=>'Recommended Modules and Services'),$_smarty_tpl);?>
</h3>
			</div>
			<div class="modal-body">
				<div id="modules_list_container_tab_modal" style="display:none;"></div>
				<div id="modules_list_loader"><i class="icon-refresh icon-spin"></i></div>
			</div>
		</div>
	</div>
</div>
<?php }} ?>
