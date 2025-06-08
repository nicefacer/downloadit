<?php 
    class_exists('ML', false) or die(); 
    /*
     * example 1: $aField[optional][field] isset => load other template
     *  $aField['type']='optional';
     *  $aField['optional']['field']['type']='string';//type for for include
     *  $aField['optional']['name']='othername';// optional, if not set, we use current name of field
     *  $aField['value']=$this->getFirstValue($aField, $mRequestValue, '');
     * 
     * example 2: $aField[optional][field] is not set => no other template
     *  $aField['type']='optional';
     * 
     * use checkbox, if $aField['i18n']['optional']['checkbox'] isset 
     *  if $aField['i18n']['optional']['checkbox']['labelNegativ'] isset negative logic, else positive logic
     */
    $aCheckbox=isset($aField['i18n']['optional']['checkbox'])?$aField['i18n']['optional']['checkbox']:false;
    $aField['i18n']['optional']['select']=
        isset($aField['i18n']['optional']['select'])
        ?$aField['i18n']['optional']['select']
        :MLI18n::gi()->get('form_type_optional_select')
    ;
    $blActive=$this->optionalIsActive($aField);
?>
<?php if(MLHttp::gi()->isAjax()&&(!isset($aField['checkajax'])||(isset($aField['checkajax'])&&$aField['checkajax']===true))){
    try{
        $this->includeType($this->getSubField($aField));
    }catch(Exception $oEx){//no subtype

    }
} else {
    ?>
    <div class="optional">
        <div>
            <select class="optional<?php echo array_key_exists('optional', $aField) && array_key_exists('editable', $aField['optional']) && $aField['optional']['editable'] == true ? ' editable' : ''; ?>"<?php echo is_array($aCheckbox)?' style="display:none"':'' ?> id="<?php echo $aField['id'].'_'.$this->sOptionalIsActivePrefix?>" name="<?php echo  MLHTTP::gi()->parseFormFieldName($this->sOptionalIsActivePrefix.'['. (isset($aField['optional']['name'])?$aField['optional']['name']:$aField['realname']) . ']') ?>">
                <option value="false"<?php echo !$blActive?' selected="selected"':''?>><?php echo $aField['i18n']['optional']['select']['false'] ?></option>
                <option value="true"<?php echo $blActive?' selected="selected"':''?>><?php echo $aField['i18n']['optional']['select']['true'] ?></option>
            </select>
            <?php if(is_array($aCheckbox)){?>
                <?php $blCheckboxPositiv=!isset($aCheckbox['labelNegativ'])?>
                <input id="<?php echo $aField['id'].'_'.$this->sOptionalIsActivePrefix ?>_checkbox" type="checkbox" value="<?php echo $blCheckboxPositiv?'true':'false'?>" class="optional"<?php echo (($blActive&&$blCheckboxPositiv)||(!$blActive&&!$blCheckboxPositiv))?' checked="checked"':'' ?> />
                <label for="<?php echo $aField['id'].'_'.$this->sOptionalIsActivePrefix ?>_checkbox">
                    <?php echo $blCheckboxPositiv?$aCheckbox['labelPositiv']:$aCheckbox['labelNegativ'] ?>
                </label>
            <?php }?>
        </div>
        <?php 
            try {
                $aSubField = $this->getSubField($aField);
                $sSubfield = $this->includeTypeBuffered($aSubField);
                if (array_key_exists('default', $aSubField)) {
                    $aDefaultField = $aSubField;                    
                    $aDefaultField['id'] .= '_default';
                    $aDefaultField['value'] = $aDefaultField['default'];
                    $sDefaultField = $this->includeTypeBuffered($aDefaultField);
                } else {
                    $sDefaultField = '';
                }
                ?>
                    <span class="optional <?php echo $blActive ? 'visible' : 'hidden'; ?>"<?php echo (empty($sDefaultField)||true ? '' : ' data-mloptionaldefault="'.htmlentities($sDefaultField).'" data-mloptionalcurrent="'.htmlentities($sSubfield).'"'); ?>>
                        <div class="optional-field"><?php echo $sSubfield; ?></div>
                        <?php 
                            if (!empty($sDefaultField)) {
                                // default element need to shown completely if tinmce should work, so cant use data-attribute or put content as htmlentities
                                ?><div class="optional-default"><?php echo ($sDefaultField) ?></div><?php
                            }
                        ?>
                        <div class="clear"></div>
                    </span>
                <?php
            } catch (Exception $oEx) {
                //no subtype
            }
        ?>
    </div>
    <?php
        MLSettingRegistry::gi()->addJs('jquery.magnalister.form.optional.js');
        MLSetting::gi()->add('aCss','magnalister.form.optional.css', true)
    ?>
<?php }?>