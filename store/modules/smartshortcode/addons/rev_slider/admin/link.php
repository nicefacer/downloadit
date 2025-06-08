<?php
if(Module::isEnabled('revsliderprestashop') == 1 &&
    Module::isInstalled('revsliderprestashop') == 1 &&
    file_exists(_PS_MODULE_DIR_.'revsliderprestashop/revprestashoploader.php')){    
?>
<a href="rev_slider"><i class="icon-tasks"></i> Revolution Slider</a>
<?php
}  
?>