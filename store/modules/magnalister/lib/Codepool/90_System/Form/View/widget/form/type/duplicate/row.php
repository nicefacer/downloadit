<?php class_exists('ML', false) or die(); ?>
<div>
    <div>
        <?php
            $aMyField=array_merge($aField, $aField['duplicate']['field']);
            unset($aMyField['duplicate']);
            $aMyField['name'].='['.$iValue.']';
            if (isset($aMyField['subfields'])) {
                foreach ($aMyField['subfields'] as &$aSubField) {
                    $aSubField['name'] .= '['.$iValue.']';
                }
            }
            $this->includeType($aMyField);
        ?>
    </div>
    <span>
        <?php 
            if (array_key_exists('radiogroup', $aField['duplicate']) && $aField['duplicate']['radiogroup']) { 
                echo MLI18n::gi()->get('form_type_duplicate_radiogroup'); ?><input class="ml-js-form-duplicate-radiogroup" type="radio" name="<?php echo md5($aField['name'].'['.$aField['duplicate']['radiogroup'].']'); ?>" value="1"<?php echo array_key_exists($aField['duplicate']['radiogroup'], $aMyField['value']) && !empty($aMyField['value'][$aField['duplicate']['radiogroup']]) ? ' checked="checked"' : '' ; ?> /><?php
                ?><input class="ml-js-form-duplicate-radiogroup" type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName($aMyField['name'].'['.$aField['duplicate']['radiogroup'].']'); ?>" value="<?php echo array_key_exists($aField['duplicate']['radiogroup'], $aMyField['value']) ? $aMyField['value'][$aField['duplicate']['radiogroup']] : '' ; ?>" /><?php
            }
        ?>
        <button <?php echo $blAdd? '' : ' disabled="disabled"' ?>class="mlbtn fullfont mlbtnPlus" type="button" data-ajax-additional="<?php echo htmlentities(json_encode(array('type' => 'add', 'ident' => $iValue))); ?>">&#043;</button>
        <button <?php echo $blSub ? '' : ' disabled="disabled"' ?>class="mlbtn fullfont mlbtnMinus" type="button" data-ajax-additional="<?php echo htmlentities(json_encode(array('type' => 'sub', 'ident' => $iValue))); ?>">&#8211;</button>
    </span>
</div>