<?php class_exists('ML', false) or die();?>
<input type="hidden" id="<?php echo $aField['id']?>_hidden" value="false" name="<?php echo MLHTTP::gi()->parseFormFieldName($aField['name']); ?>[]">
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
                    <input type="checkbox" id="<?php echo $aField['id']?>_<?php echo $sOptionKey ?>" value="<?php echo $sOptionKey?>"<?php echo(in_array($sOptionKey, $aField['value'])?' checked="checked"':'');?> name="<?php echo MLHTTP::gi()->parseFormFieldName($aField['name']); ?>[]">
                </td>
            <?php } ?>
        </tr>
    </tbody>
</table>