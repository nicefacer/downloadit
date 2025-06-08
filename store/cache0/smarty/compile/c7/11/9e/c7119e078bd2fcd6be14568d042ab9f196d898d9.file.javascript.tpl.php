<?php /* Smarty version Smarty-3.1.19, created on 2024-06-27 17:07:31
         compiled from "/homepages/40/d657041287/htdocs/modules/paypal/views/templates/_partials/javascript.tpl" */ ?>
<?php /*%%SmartyHeaderCode:455722538667d8033c04920-35015751%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c7119e078bd2fcd6be14568d042ab9f196d898d9' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/paypal/views/templates/_partials/javascript.tpl',
      1 => 1668599249,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '455722538667d8033c04920-35015751',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'JSvars' => 0,
    'varName' => 0,
    'varValue' => 0,
    'JSscripts' => 0,
    'keyScript' => 0,
    'JSscriptAttributes' => 0,
    'attrName' => 0,
    'attrVal' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_667d8033c41a85_31000569',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_667d8033c41a85_31000569')) {function content_667d8033c41a85_31000569($_smarty_tpl) {?>

<script>
    <?php  $_smarty_tpl->tpl_vars['varValue'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['varValue']->_loop = false;
 $_smarty_tpl->tpl_vars['varName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['JSvars']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['varValue']->key => $_smarty_tpl->tpl_vars['varValue']->value) {
$_smarty_tpl->tpl_vars['varValue']->_loop = true;
 $_smarty_tpl->tpl_vars['varName']->value = $_smarty_tpl->tpl_vars['varValue']->key;
?>
      var <?php echo $_smarty_tpl->tpl_vars['varName']->value;?>
 = <?php echo $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_MODIFIER]['json_encode'][0][0]->jsonEncode($_smarty_tpl->tpl_vars['varValue']->value);?>
;
    <?php } ?>
</script>

<?php if (isset($_smarty_tpl->tpl_vars['JSscripts']->value)&&is_array($_smarty_tpl->tpl_vars['JSscripts']->value)&&false===empty($_smarty_tpl->tpl_vars['JSscripts']->value)) {?>
    <?php  $_smarty_tpl->tpl_vars['JSscriptAttributes'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['JSscriptAttributes']->_loop = false;
 $_smarty_tpl->tpl_vars['keyScript'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['JSscripts']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['JSscriptAttributes']->key => $_smarty_tpl->tpl_vars['JSscriptAttributes']->value) {
$_smarty_tpl->tpl_vars['JSscriptAttributes']->_loop = true;
 $_smarty_tpl->tpl_vars['keyScript']->value = $_smarty_tpl->tpl_vars['JSscriptAttributes']->key;
?>
      <script>
          var script = document.querySelector('script[data-key="<?php echo $_smarty_tpl->tpl_vars['keyScript']->value;?>
"]');

          if (null == script) {
              var newScript = document.createElement('script');
              <?php  $_smarty_tpl->tpl_vars['attrVal'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['attrVal']->_loop = false;
 $_smarty_tpl->tpl_vars['attrName'] = new Smarty_Variable;
 $_from = $_smarty_tpl->tpl_vars['JSscriptAttributes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['attrVal']->key => $_smarty_tpl->tpl_vars['attrVal']->value) {
$_smarty_tpl->tpl_vars['attrVal']->_loop = true;
 $_smarty_tpl->tpl_vars['attrName']->value = $_smarty_tpl->tpl_vars['attrVal']->key;
?>
                newScript.setAttribute('<?php echo $_smarty_tpl->tpl_vars['attrName']->value;?>
', '<?php echo $_smarty_tpl->tpl_vars['attrVal']->value;?>
');
              <?php } ?>

              newScript.setAttribute('data-key', '<?php echo $_smarty_tpl->tpl_vars['keyScript']->value;?>
');
              document.body.appendChild(newScript);
          }
      </script>
    <?php } ?>
<?php }?>

<?php }} ?>
