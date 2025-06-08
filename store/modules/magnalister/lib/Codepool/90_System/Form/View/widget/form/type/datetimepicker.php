<?php 
    class_exists('ML', false) or die();
    MLSetting::gi()->set('blFormDatepickerLoaded',true);
    MLSettingRegistry::gi()->addJs(array(
        'jquery-ui-timepicker-addon.js',
        'jquery.magnalister.form.datepicker.js'
    ));
?>
<div class="datetimepicker">
    <input type="text" id="<?php echo $aField['id']; ?>" name="<?php echo MLHttp::gi()->parseFormFieldName($aField['name']) ?>" value="<?php echo $aField['value'] ?>" />
    <span class="gfxbutton small delete "></span>
</div>