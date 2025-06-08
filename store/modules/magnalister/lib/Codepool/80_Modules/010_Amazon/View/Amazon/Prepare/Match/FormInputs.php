<?php class_exists('ML',false) or die()?>
<?php
    $aPreparedData = $this->getCurrentProduct();
    $oModul=  MLModul::gi();
    $oModulHelper= MLFormHelper::getModulInstance();
//    $oShop=  MLShop::gi();
    $aRequest=$this->getRequest('amazonProperties');
    $sPreparedTs = isset($aRequest['preparedts']) ? $aRequest['preparedts'] : date('Y-m-d H:i:s');
    $iShipping=isset($aRequest['shipping'])?$aRequest['shipping']:(isset($aPreparedData)?$aPreparedData['WillShipInternationally']:$oModul->getConfig('internationalshipping'));
    $sCondition=isset($aRequest['conditiontype'])?$aRequest['conditiontype']:isset($aPreparedData)?$aPreparedData['ConditionType']:$oModul->getConfig('itemcondition');
    $sNote=isset($aRequest['conditionnote'])?$aRequest['conditionnote']:isset($aPreparedData)?$aPreparedData['ConditionNote']:'';
    $iShippingTime = isset($aPreparedData)?$aPreparedData['ShippingTime']: MLModul::gi()->getConfig('leadtimetoship');
?>
<div class="clear"></div>
<input  type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('amazonProperties[preparedts]'); ?>" value="<?php echo $sPreparedTs; ?>" />
<table class="amazon_properties amazon_properties2">
    <thead><tr><th colspan="2"><?php echo $this->__('ML_GENERIC_PRODUCTDETAILS'); ?></th></tr></thead>
    <tbody>
        <tr>
            <td class="label top">
                <?php echo $this->__('ML_AMAZON_CONDITION_DESCRIPTION') ?><br>
                <span class="normal"><?php echo sprintf($this->__('ML_AMAZON_X_CHARS_LEFT'), '<span id="charsLeft">0</span>') ?></span>
            </td>
            <td class="options">
                <textarea id="item_note" name="<?php echo MLHttp::gi()->parseFormFieldName('amazonProperties[conditionnote]') ?>" wrap="soft" cols="100" rows="10" class="fullwidth"><?php echo $sNote ?></textarea>
            </td>
        </tr>
        <tr class="odd">
            <td class="label"><?php echo $this->__('ML_GENERIC_CONDITION'); ?></td>
            <td class="options">
                <select id="item_condition" name="<?php echo MLHttp::gi()->parseFormFieldName('amazonProperties[conditiontype]') ?>">
                    <?php foreach ($oModulHelper->getConditionValues() as $sKey => $sValue) { ?>
                        <option <?php echo ($sCondition == $sKey ? 'selected="selected" ' : '') ?>value="<?php echo $sKey ?>"><?php echo $sValue; ?></option>
                    <?php } ?>
                </select>
            </td>
        </tr>
        <tr class="last">
            <td class="label"><?php echo $this->__('ML_GENERIC_SHIPPING'); ?></td>
            <td class="options">
                <select id="amazon_shipping" name="<?php echo MLHttp::gi()->parseFormFieldName('amazonProperties[shipping]') ?>">
                    <?php foreach ($oModulHelper->getShippingLocationValues() as $iKey => $sValue) { ?>
                        <option <?php echo ($iShipping == $iKey ? 'selected="selected" ' : '') ?>value="<?php echo $iKey ?>"><?php echo $sValue; ?></option>
                    <?php } ?>
                </select>
                &nbsp;<?php echo $this->__('ML_LABEL_MARKETPLACE_SHIPPING_TIME')?>:
                <select name="<?php echo MLHttp::gi()->parseFormFieldName('amazonProperties[ShippingTime]') ?>">
                    <option>—</option>
                    <?php for($i=1;$i<31;$i++){?>
                        <option <?php echo($iShippingTime == $i?'selected="selected" ':'') ?>value="<?php echo $i ?>"><?php echo $i?></option>
                    <?php } ?>    
                </select>
            </td>
        </tr>
    </tbody>
</table>
<div class="clear"></div>
<?php MLSettingRegistry::gi()->addJs('magnalister.amazon.countChars.js'); ?>
