<?php 
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ML', false) or die();
$sDataMlAlert = 
    (
        array_key_exists('alert', $aField['i18n']) 
        && is_array($aField['i18n']['alert']) 
        && !empty($aField['i18n']['alert'])
    ) 
    ? json_encode(array(
        'abort' => MLI18n::gi()->get('ML_BUTTON_LABEL_ABORT'), 
        'ok' => MLI18n::gi()->get('ML_BUTTON_LABEL_OK')
    ))
    : ''
;
?>
<select <?php echo empty($sDataMlAlert) ? '' : "data-ml-alert='".$sDataMlAlert."' " ?> name="<?php echo MLHTTP::gi()->parseFormFieldName($aField['name']);?>" <?php echo isset($aField['id'])?  'id="'.$aField['id'].'"':'' ?> 
                <?php 
                    if (!array_key_exists('value', $aField) || empty($aField['value'])) {
                        $blValueExists = true;
                    } else {
                        $blValueExists = false;
                        foreach (array_key_exists('values', $aField) ? $aField['values'] : array() as $sOptionValue) {
                            if (is_array($sOptionValue)) { 
                                $blValueExists = $blValueExists || array_key_exists($aField['value'], $sOptionValue);
                            } else {
                                $blValueExists = array_key_exists($aField['value'], $aField['values']);
                                break;
                            }
                        }
                    }
                    if (
                        isset($aField['cssclass']) 
                        || (isset($aField['required']) && empty($aField['value']))
                        || !$blValueExists
                    ) {
                        echo 'class="';
                        echo isset($aField['cssclass']) ? $aField['cssclass'] : '';
                        if (
                            (isset($aField['required']) && empty($aField['value'])) 
                            || !$blValueExists
                        ) {
                            echo isset($aField['cssclass']) ? ' ' : '';
                            echo 'ml-error';
                        }
                        echo '"';
                    }
                ?>
        style="width:100%;">
    <?php
        if (!$blValueExists) {
            ?><option selected="selected" disabled="disabled" value="<?php echo $aField['value']; ?>"><?php echo MLI18n::gi()->get('ML_LABEL_INVALID'); ?></option><?php
        }
        $aField['type']='select_optgroup';
        $this->includeType($aField);
    ?>
</select>
<?php
    if (!empty($sDataMlAlert)) {
        MLSettingRegistry::gi()->addJs('jquery.magnalister.form.select.js');
    }
?>