<?php class_exists('ML', false) or die(); ?>
<div class="duplicate" id="<?php echo $aField['id'] . '_duplicate' ?>"><?php
    $aAjaxData = $this->getAjaxData();
    $sAddType = isset($aAjaxData['additional']['type']) ? $aAjaxData['additional']['type'] : '';
    $iAddIdent = isset($aAjaxData['additional']['ident']) ? $aAjaxData['additional']['ident'] : -1;
    $iAddValue = 0;
//    new dBug($aField, '', true);
    $aField['type']='duplicate_row';
    $iFieldCount = max(
        (
            isset($aField['value']) && is_array($aField['value']) 
            ? count($aField['value']) 
            : 0
        )
        , (
            isset($aField['subfields']) && is_array($this->getField(current($aField['subfields']), 'value'))
            ? count($this->getField(current($aField['subfields']), 'value'))
            : 0
        )
    );
    if ($iFieldCount == 0) {
        $aMyField = $aField;
        $aMyField['value'] = '';
        $this->includeType($aMyField, array('iValue' => $iAddValue, 'blSub' => false,'blAdd'=>true));
    } else {
        if ($sAddType == 'add') {
            $blSub = true;
            $blAdd = !isset($aField['duplicate']['max'])||($iFieldCount + 1<$aField['duplicate']['max']);
        } elseif ($sAddType == 'sub') {
            $blSub = ($iFieldCount - 1) > 1;
            $blAdd = true;
        } else {
            $blSub = $iFieldCount > 1;
            $blAdd = !isset($aField['duplicate']['max'])||($iFieldCount<$aField['duplicate']['max']);
        }
        /**
         * @var string $sFieldJson 
         * workaround php uses without any reason last element of $aField['subfields'] in template as a reference
         * so remember original here as JSON
         * anyway $this->includeType() dont have references in function header
         */
        $sFieldJson = json_encode($aField);
        for ($iValue = 0; $iValue < $iFieldCount; $iValue++) {
            $aMyField = json_decode($sFieldJson, true);
            if(isset($aField['fieldinfo'][$iValue]) && is_array($aField['fieldinfo'][$iValue])){//additional info to current field eg. style...
                foreach($aField['fieldinfo'][$iValue] as $sKey=>$mValue){
                    $aMyField[$sKey]=$mValue;
                }
            }
            $aMyField['value'] = isset($aField['value'][$iValue]) ? $aField['value'][$iValue] : '';
            if (isset($aField['subfields'])) {
                foreach ($aField['subfields'] as $sSubField => $aSubField) {
                    $aMyField['subfields'][$sSubField]['value'] = isset($aSubField['value'][$iValue]) ? $aSubField['value'][$iValue] : '';
                }
            }
            

            if ($sAddType == 'sub' && $iAddIdent == $iValue) {
                --$iAddValue;
            } else {
                $this->includeType($aMyField, array('aField' => $aMyField, 'iValue' => $iValue + $iAddValue, 'blSub' => $blSub, 'blAdd'=>$blAdd));
            }
            if ($sAddType == 'add' && $iAddIdent == $iValue) {
                if (array_key_exists('radiogroup', $aField['duplicate']) && $aField['duplicate']['radiogroup']) { 
                    $aMyField['value'][$aField['duplicate']['radiogroup']] = 0;
                }
                ++$iAddValue;
                $this->includeType($aMyField, array('aField' => $aMyField, 'iValue' => $iValue + $iAddValue, 'blSub' => $blSub,'blAdd'=>$blAdd ));
            }
        }
        $aField = json_decode($sFieldJson, true);
    }
?></div>