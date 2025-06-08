<?php /* Smarty version Smarty-3.1.19, created on 2025-06-06 10:36:05
         compiled from "/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/hookOverrideTOSDisplay.tpl" */ ?>
<?php /*%%SmartyHeaderCode:923064786842a875243762-71887827%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '687e09a60957b6a8bc37dacd477022feede8ae71' => 
    array (
      0 => '/homepages/40/d657041287/htdocs/modules/advancedeucompliance/views/templates/hook/hookOverrideTOSDisplay.tpl',
      1 => 1738189759,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '923064786842a875243762-71887827',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'has_tos_override_opt' => 0,
    'checkedTOS' => 0,
    'link_conditions' => 0,
    'link_revocations' => 0,
    'has_virtual_product' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.19',
  'unifunc' => 'content_6842a87528e555_89750796',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_6842a87528e555_89750796')) {function content_6842a87528e555_89750796($_smarty_tpl) {?>

<div class="row">
    <div class="col-xs-12 col-md-12">

        <?php if ($_smarty_tpl->tpl_vars['has_tos_override_opt']->value) {?>
            <h2><?php echo smartyTranslate(array('s'=>'Terms and Conditions','mod'=>'advancedeucompliance'),$_smarty_tpl);?>
</h2>
            <div class="tnc_box">
                <p class="checkbox">
                    <input type="checkbox" name="cgv" id="cgv" value="1" <?php if (isset($_smarty_tpl->tpl_vars['checkedTOS']->value)&&$_smarty_tpl->tpl_vars['checkedTOS']->value) {?>checked="checked"<?php }?>/>
                    <?php if (isset($_smarty_tpl->tpl_vars['link_conditions']->value)&&$_smarty_tpl->tpl_vars['link_conditions']->value&&isset($_smarty_tpl->tpl_vars['link_revocations']->value)&&$_smarty_tpl->tpl_vars['link_revocations']->value) {?>
                        <label for="cgv">
                            <?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link_conditions']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp7=ob_get_clean();?><?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link_revocations']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp8=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'I agree to the [1]terms of service[/1] and to the [2]terms of revocation[/2] and will adhere to them unconditionally.','tags'=>array((('<a href="').($_tmp7)).('" class="iframe" rel="nofollow">'),(('<a href="').($_tmp8)).('" class="iframe" rel="nofollow">')),'mod'=>'advancedeucompliance'),$_smarty_tpl);?>

                        </label>
                    <?php } else { ?>
                        <label for="cgv">
                            <?php echo smartyTranslate(array('s'=>'I agree to the terms of service and to the terms of revocation and will adhere to them unconditionally','mod'=>'advancedeucompliance'),$_smarty_tpl);?>

                        </label>
                    <?php }?>
                </p>
            </div>
        <?php } else { ?>
            <h2><?php echo smartyTranslate(array('s'=>'Terms and Conditions','mod'=>'advancedeucompliance'),$_smarty_tpl);?>
</h2>
            <div class="box">
                <p class="checkbox">
                    <input type="checkbox" name="cgv" id="cgv" value="1" <?php if ($_smarty_tpl->tpl_vars['checkedTOS']->value) {?>checked="checked"<?php }?> />
                    <?php if (isset($_smarty_tpl->tpl_vars['link_conditions']->value)&&$_smarty_tpl->tpl_vars['link_conditions']->value) {?>
                        <label for="cgv">
                            <?php ob_start();?><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['link_conditions']->value, ENT_QUOTES, 'UTF-8', true);?>
<?php $_tmp9=ob_get_clean();?><?php echo smartyTranslate(array('s'=>'I agree to the terms of service and will adhere to them unconditionally. [1](Read the Terms of Service)[/1].','tags'=>array((('<a href="').($_tmp9)).('" class="iframe" rel="nofollow">')),'mod'=>'advancedeucompliance'),$_smarty_tpl);?>

                        </label>
                    <?php } else { ?>
                        <label for="cgv">
                            <?php echo smartyTranslate(array('s'=>'I agree to the terms of service and to the terms of revocation and will adhere to them unconditionally','mod'=>'advancedeucompliance'),$_smarty_tpl);?>

                        </label>
                    <?php }?>
                </p>
            </div>
        <?php }?>


        <?php if ($_smarty_tpl->tpl_vars['has_virtual_product']->value) {?>
            <div class="tnc_box">
                <p class="checkbox">
                    <input type="checkbox" name="revocation_vp_terms_agreed" id="revocation_vp_terms_agreed" value="1"/>
                    <label for="revocation_vp_terms_agreed"><?php echo smartyTranslate(array('s'=>'I agree that the digital products in my cart can not be returned or refunded due to the nature of such products.','mod'=>'advancedeucompliance'),$_smarty_tpl);?>
</label>
                </p>
            </div>
        <?php }?>

    </div>
</div>
<?php }} ?>
