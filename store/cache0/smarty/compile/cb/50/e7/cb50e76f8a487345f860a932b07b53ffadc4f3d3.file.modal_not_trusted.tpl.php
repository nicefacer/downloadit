<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:07:31
         compiled from "/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/controllers/modules/modal_not_trusted.tpl" */ ?>
<?php /*%%SmartyHeaderCode:973887938667d80338842f4-32904597%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'cb50e76f8a487345f860a932b07b53ffadc4f3d3' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/admindlkhe5d4/themes/default/template/controllers/modules/modal_not_trusted.tpl',
      1 => 1603055817,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '973887938667d80338842f4-32904597',
  'function' => 
  array (
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d80338dba06_78674842',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d80338dba06_78674842')) {function content_667d80338dba06_78674842($_smarty_tpl) {?>

<div class="untrusted-content-action">

	<div class="modal-body">
		<div class="alert alert-warning">
			<h3><?php echo smartyTranslate(array('s'=>'Do you want to install this module that could not be verified by PrestaShop?'),$_smarty_tpl);?>
</h3>

			<p><?php echo smartyTranslate(array('s'=>"This generally happens when the module isn't distributed through our official marketplace, PrestaShop Addons - or when your server failed to communicate with PrestaShop Addons."),$_smarty_tpl);?>
</p>
		</div>

		<div class="row">
			<div class="col-sm-2" style="text-align: center;">
				<img id="untrusted-module-logo" class="" src="" alt="" style="max-width:96px;">
			</div>
			<div class="col-sm-10">
				<table class="table">
					<tr>
						<td><?php echo smartyTranslate(array('s'=>'Module'),$_smarty_tpl);?>
</td>
						<td><strong><span class="module-display-name-placeholder"></span></strong></td>
					</tr>
					<tr>
						<td><?php echo smartyTranslate(array('s'=>'Author'),$_smarty_tpl);?>
</td>
						<td><strong><span class="author-name-placeholder"></span></strong></td>
					</tr>
				</table>
			</div>

			<div class="col-sm-12" style="text-align: center; padding-top: 12px;">
				<a id="proceed-install-anyway" href="#" class="btn btn-warning"><?php echo smartyTranslate(array('s'=>'Proceed with the installation'),$_smarty_tpl);?>
</a>
				<button type="button" class="btn btn-default" data-dismiss="modal"><?php echo smartyTranslate(array('s'=>'Back to modules list'),$_smarty_tpl);?>
</button>
			</div>
		</div>
	</div>

	<div class="modal-footer">
		<div class="alert alert-info">
			<p>
				<?php echo smartyTranslate(array('s'=>'Since you may not have downloaded this module from PrestaShop Addons, we cannot assert that the module is not adding some undisclosed functionalities. We advise you to install it only if you trust the source of the content.'),$_smarty_tpl);?>

				<a id="untrusted-show-risk" href="#"><strong><?php echo smartyTranslate(array('s'=>"What's the risk?"),$_smarty_tpl);?>
</strong></a>
			</p>
		</div>
	</div>

</div>

<div class="untrusted-content-more-info" style="display:none;">

	<div class="modal-body">
		<h4><?php echo smartyTranslate(array('s'=>'Am I at Risk?'),$_smarty_tpl);?>
</h4>

		<p><?php echo smartyTranslate(array('s'=>"A module that hasn't been verified may be dangerous and could add hidden functionalities like backdoors, ads, hidden links, spam, etc. Don’t worry, this alert is simply a warning."),$_smarty_tpl);?>
</p>

		<p><?php echo smartyTranslate(array('s'=>"PrestaShop, being an open-source software, has an awesome community with a long history of developing and sharing high quality modules. Before installing this module, making sure its author is a known community member is always a good idea (by checking [1]our forum[/1] for instance).",'tags'=>array('<a href="https://www.prestashop.com/forums/">')),$_smarty_tpl);?>
</p>

		<h4><?php echo smartyTranslate(array('s'=>'What Should I Do?'),$_smarty_tpl);?>
</h4>
		
		<p><?php echo smartyTranslate(array('s'=>"If you trust or find the author of this module to be an active community member, you can proceed with the installation."),$_smarty_tpl);?>


		<p><?php echo smartyTranslate(array('s'=>"Otherwise you can look for similar modules on the official marketplace. [1]Click here to browse PrestaShop Addons[/1].",'tags'=>array('<a class="catalog-link" href="#">')),$_smarty_tpl);?>


	</div>

	<div class="modal-footer">
		<a id="untrusted-show-action" class="btn btn-default" href="#"><?php echo smartyTranslate(array('s'=>'Back'),$_smarty_tpl);?>
</a>
	</div>

</div>
<?php }} ?>
