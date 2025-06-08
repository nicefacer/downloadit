<?php /* Smarty version Smarty-3.1.19, created on 2025-06-03 18:25:54
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/addjs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1292226161683f2212df9186-44285610%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8180801accf50d62b4b99deb12722180fc640db' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/addjs.tpl',
      1 => 1738189811,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1292226161683f2212df9186-44285610',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ajax_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_683f22131e40b6_99838300',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_683f22131e40b6_99838300')) {function content_683f22131e40b6_99838300($_smarty_tpl) {?><script type="text/javascript">
    
if( typeof tinymce != 'undefined')
tinymce.PluginManager.load('shortcode', '<?php echo $_smarty_tpl->tpl_vars['ajax_url']->value;?>
&tinymceAction=tinymcejs');


</script>
<style>
	.icon-Adminsmartshortcode{
		font-family: FontAwesome;
		font-weight: normal;
		font-style: normal;
		text-decoration: inherit;
		-webkit-font-smoothing: antialiased;
		display: inline;
		width: auto;
		height: auto;
		line-height: normal;
		vertical-align: baseline;
		background-image: none;
		background-position: 0% 0%;
		background-repeat: repeat;
		margin-top: 0;
	}
	.icon-Adminsmartshortcode:before{
		content: "\f160";
		text-decoration: inherit;
		display: inline-block;
		speak: none;
	}
</style><?php }} ?>
