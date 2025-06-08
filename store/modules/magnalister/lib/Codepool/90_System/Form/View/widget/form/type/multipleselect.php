<?php 
    class_exists('ML', false) or die();
    $aField['value'] = (isset($aField['value']) && is_array($aField['value'])) ? $aField['value'] : array();
?>
<select multiple="multiple" name="<?php echo MLHTTP::gi()->parseFormFieldName($aField['name']);?>[]" id="<?php echo $aField['id'] ?>" <?php echo ((isset($aField['required']) && empty($aField['value']))? ' class="ml-error"' : '')?>>
    <?php foreach($aField['values'] as $sOptionKey=>$sOptionValue){?>
        <option value="<?php echo $sOptionKey?>"<?php echo(in_array($sOptionKey,$aField['value']))?' selected="selected"':'';?>><?php echo $sOptionValue ?></option>
    <?php } ?>
</select>

