<?php
class_exists('ML', false) or die();
$aValues = isset($aValues) ? $aValues : (isset($aField['values']) ? $aField['values'] : array());
$sValue = isset($sValue) ? $sValue : (isset($aField['value']) ? $aField['value'] : '');

foreach ($aValues as $sOptionKey => $sOptionValue) {
    if (is_array($sOptionValue)) { ?>
        <optgroup label="<?= $sOptionKey ?>">
            <?php $this->includeType($aField, array('aValues' => $sOptionValue, 'sValue' => $sValue)); ?>
        </optgroup>
    <?php } else {
        $this->includeType(array_merge($aField, array('type' => 'select_option')), array('aOption' => array(
                'selected' => is_array($sValue) === false && (string) $sValue === (string) $sOptionKey,
                'key' => $sOptionKey,
                'value' => $sOptionValue
        )));
    }
}