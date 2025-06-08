<?php /* Smarty version Smarty-3.1.19, created on 2025-06-07 13:44:23
         compiled from "/homepages/40/d657041287/htdocs/modules/lgcookieslaw//views/templates/hook/cookieslaw.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1162568340684426177ced35-21816314%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'dd5996171f09f4dee849e1ae1a7b6469e9c7dcd0' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/lgcookieslaw//views/templates/hook/cookieslaw.tpl',
      1 => 1738189782,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1162568340684426177ced35-21816314',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'lgcookieslaw_position' => 0,
    'cookie_message' => 0,
    'cms_target' => 0,
    'cms_link' => 0,
    'button2' => 0,
    'lgcookieslaw_setting_button' => 0,
    'button1' => 0,
    'third_paries' => 0,
    'cookie_additional' => 0,
    'cookie_required' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_684426177ee151_91108061',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_684426177ee151_91108061')) {function content_684426177ee151_91108061($_smarty_tpl) {?>
<?php if ($_smarty_tpl->tpl_vars['lgcookieslaw_position']->value==3) {?>
<div id="lgcookieslaw_banner" class="lgcookieslaw_banner  lgcookieslaw_message_floating">
    <div class="container">
        <div class="lgcookieslaw_message"><?php if (version_compare(@constant('_PS_VERSION_'),'1.7.0','>=')) {?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['cookie_message']->value;?>
<?php $_tmp7=ob_get_clean();?><?php echo strip_tags($_tmp7);?>
<?php } else { ?><?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['cookie_message']->value));?>
<?php }?>
            <a id="lgcookieslaw_info" <?php if (isset($_smarty_tpl->tpl_vars['cms_target']->value)&&$_smarty_tpl->tpl_vars['cms_target']->value) {?> target="_blank" <?php }?> href="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['cms_link']->value);?>
" >
                <?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['button2']->value));?>

            </a>            
            <?php if ($_smarty_tpl->tpl_vars['lgcookieslaw_setting_button']->value!=1) {?>
            <a onclick="customizeCookies()">
                <?php echo smartyTranslate(array('s'=>'customize cookies','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

            </a>
            <?php }?>
        </div>
        <div class="lgcookieslaw_button_container">
            <?php if ($_smarty_tpl->tpl_vars['lgcookieslaw_setting_button']->value==1) {?>
            <a class="lgcookieslaw_customize_cookies" onclick="customizeCookies()">
                <?php echo smartyTranslate(array('s'=>'customize cookies','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

            </a>
            <?php }?>
            <button id="lgcookieslaw_accept" class="lgcookieslaw_btn<?php if ($_smarty_tpl->tpl_vars['lgcookieslaw_setting_button']->value!=1) {?> lgcookieslaw_btn_accept_big<?php }?>" onclick="closeinfo(true, true)"><?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['button1']->value));?>
</button>
        </div>
    </div>
</div>
<?php } else { ?>
<div id="lgcookieslaw_banner" class="lgcookieslaw_banner">
    <div class="container">
        <div class="lgcookieslaw_message"><?php if (version_compare(@constant('_PS_VERSION_'),'1.7.0','>=')) {?><?php ob_start();?><?php echo $_smarty_tpl->tpl_vars['cookie_message']->value;?>
<?php $_tmp8=ob_get_clean();?><?php echo strip_tags($_tmp8);?>
<?php } else { ?><?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['cookie_message']->value));?>
<?php }?>
            <a id="lgcookieslaw_info" <?php if (isset($_smarty_tpl->tpl_vars['cms_target']->value)&&$_smarty_tpl->tpl_vars['cms_target']->value) {?> target="_blank" <?php }?> href="<?php echo preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['cms_link']->value);?>
" >
                <?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['button2']->value));?>

            </a>            
            <a class="lgcookieslaw_customize_cookies" onclick="customizeCookies()">
                <?php echo smartyTranslate(array('s'=>'customize cookies','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

            </a>
        </div>
        <div class="lgcookieslaw_button_container">
            <button id="lgcookieslaw_accept" class="lgcookieslaw_btn lgcookieslaw_btn_accept" onclick="closeinfo(true, true)"><?php echo stripslashes(preg_replace("%(?<!\\\\)'%", "\'",$_smarty_tpl->tpl_vars['button1']->value));?>
</button>
        </div>
    </div>
</div>
<?php }?>
<div style="display: none;" id="lgcookieslaw-modal">
    <div class="lgcookieslaw-modal-body">
        <h2><?php echo smartyTranslate(array('s'=>'Cookies configuration','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</h2>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                <?php echo smartyTranslate(array('s'=>'Customization','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw_switch">
                    <div class="lgcookieslaw_slider_option_left"><?php echo smartyTranslate(array('s'=>'No','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</div>
                    <input type="checkbox" id="lgcookieslaw-cutomization-enabled" <?php if ($_smarty_tpl->tpl_vars['third_paries']->value) {?>checked="checked"<?php }?>>
                    <span class="lgcookieslaw_slider<?php if ($_smarty_tpl->tpl_vars['third_paries']->value) {?> lgcookieslaw_slider_checked<?php }?>"></span>
                    <div class="lgcookieslaw_slider_option_right"><?php echo smartyTranslate(array('s'=>'Yes','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                <?php echo $_smarty_tpl->tpl_vars['cookie_additional']->value;?>

            </div>
        </div>
        <div class="lgcookieslaw-section">
            <div class="lgcookieslaw-section-name">
                <?php echo smartyTranslate(array('s'=>'Functional (required)','mod'=>'lgcookieslaw'),$_smarty_tpl);?>

            </div>
            <div class="lgcookieslaw-section-checkbox">
                <label class="lgcookieslaw_switch">
                    <div class="lgcookieslaw_slider_option_left"><?php echo smartyTranslate(array('s'=>'No','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</div>
                    <input type="checkbox" checked="checked" disabled="disabled">
                    <span class="lgcookieslaw_slider lgcookieslaw_slider_checked"></span>
                    <div class="lgcookieslaw_slider_option_right"><?php echo smartyTranslate(array('s'=>'Yes','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</div>
                </label>
            </div>
            <div class="lgcookieslaw-section-description">
                <?php echo $_smarty_tpl->tpl_vars['cookie_required']->value;?>

            </div>
        </div>
    </div>
    <div class="lgcookieslaw-modal-footer">
        <div class="lgcookieslaw-modal-footer-left">
            <button class="btn" id="lgcookieslaw-close"> > <?php echo smartyTranslate(array('s'=>'Cancel','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</button>
        </div>
        <div class="lgcookieslaw-modal-footer-right">
            <button class="btn" id="lgcookieslaw-save" onclick="closeinfo(true)"><?php echo smartyTranslate(array('s'=>'Accept and continue','mod'=>'lgcookieslaw'),$_smarty_tpl);?>
</button>
        </div>
    </div>
</div>
<div class="lgcookieslaw_overlay"></div>
<?php }} ?>
