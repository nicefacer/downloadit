<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $aFilter array array('name'=>'', 'value'=>'', 'values'=>array('value'=>'','label'=>'translatedText'), 'placeholder'=>'') */
class_exists('ML', false) or die();
?>
<div class="newSearch">
    <input type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('filter[' . $aFilter['name'] . ']') ?>" value="<?php echo $aFilter['value'] ?>" placeholder="<?php echo $this->__($aFilter['placeholder']); ?>" />
    <button class="mlbtn action" type="submit">
        <span></span>
    </button>
</div>