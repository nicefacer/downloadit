<?php class_exists('ML', false) or die();?>
<table class="imageBox">
    <tbody>
        <tr>
            <?php
                $aField['type']='image_list';
                $this->includeType($aField);
            ?>
        </tr>
        <tr>
            <?php foreach($aField['values'] as $sOptionKey=>$aImage){?>
                <td class="cb">
                    <input type="radio" id="<?php echo $aField['id']?>_<?php echo $sOptionKey ?>" value="<?php echo $sOptionKey?>"<?php echo($aField['value']==$sOptionKey || (is_array($aField['value']) && current($aField['value']) == $sOptionKey) ?' checked="checked"':'');?> name="<?php echo MLHTTP::gi()->parseFormFieldName($aField['name']).(array_key_exists('asarray', $aField) && $aField['asarray'] ? '[]' : ''); ?>">
                </td>
            <?php } ?>
        </tr>
    </tbody>
</table>