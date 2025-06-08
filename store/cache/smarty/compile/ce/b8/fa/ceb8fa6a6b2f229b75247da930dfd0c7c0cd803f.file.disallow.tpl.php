<?php /* Smarty version Smarty-3.1.19, created on 2025-05-01 11:22:01
         compiled from "/homepages/40/d657041287/htdocs/modules/lgcookieslaw/views/templates/front/disallow.tpl" */ ?>
<?php /*%%SmartyHeaderCode:189387202668133d39a4ce27-57986199%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ceb8fa6a6b2f229b75247da930dfd0c7c0cd803f' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/lgcookieslaw/views/templates/front/disallow.tpl',
      1 => 1738189781,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '189387202668133d39a4ce27-57986199',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lgcookieslaw_safe_cookies' => 0,
    'cookie' => 0,
    'lgcookieslaw_token_ok' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_68133d39a5f9d8_86214382',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_68133d39a5f9d8_86214382')) {function content_68133d39a5f9d8_86214382($_smarty_tpl) {?>
<script type="text/javascript">
    var lgcookieslaw_safe_cookies = [];
    <?php  $_smarty_tpl->tpl_vars['cookie'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['cookie']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['lgcookieslaw_safe_cookies']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['cookie']->key => $_smarty_tpl->tpl_vars['cookie']->value) {
$_smarty_tpl->tpl_vars['cookie']->_loop = true;
?>
    lgcookieslaw_safe_cookies.push('<?php echo mb_convert_encoding(htmlspecialchars($_smarty_tpl->tpl_vars['cookie']->value, ENT_QUOTES, 'UTF-8', true), "HTML-ENTITIES", 'UTF-8');?>
');
    <?php } ?>

    var getCookies = function(){
        var pairs = document.cookie.split(";");
        var cookies = {};
        for (var i=0; i<pairs.length; i++){
            var pair = pairs[i].split("=");
            cookies[(pair[0]+'').trim()] = unescape(pair[1]);
        }
        return cookies;
    }

    var myCookies = getCookies();
    Object.keys(myCookies).map(function(key, index) {
        document.cookie = key + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = key + "=; domain=." + window.location.hostname + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
        document.cookie = key + "=; domain=." + window.location.hostname.replace('www.',  '') + "; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/";
    });
</script>
<div>
    <?php if ($_smarty_tpl->tpl_vars['lgcookieslaw_token_ok']->value) {?>
        <h2><?php echo smartyTranslate(array('s'=>'All your cookies except Prestashop session ones have been deleted','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</h2>
    <?php } else { ?>
        <h2><?php echo smartyTranslate(array('s'=>'Bad request','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</h2>
    <?php }?>
</div><?php }} ?>
