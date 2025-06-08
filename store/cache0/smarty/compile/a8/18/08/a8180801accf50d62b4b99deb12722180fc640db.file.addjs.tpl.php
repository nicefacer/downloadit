<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:07:31
         compiled from "/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/addjs.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1918133402667d80336cc650-50137784%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8180801accf50d62b4b99deb12722180fc640db' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/smartshortcode/views/templates/front/addjs.tpl',
      1 => 1482273769,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1918133402667d80336cc650-50137784',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'ajax_url' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d80336e5258_86490768',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d80336e5258_86490768')) {function content_667d80336e5258_86490768($_smarty_tpl) {?><script type="text/javascript">
    
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
