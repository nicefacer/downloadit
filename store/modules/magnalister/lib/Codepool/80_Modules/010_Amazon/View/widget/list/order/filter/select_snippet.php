<?php
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $aFilter array array('name'=>'', 'value'=>'', 'values'=>array('value'=>'','label'=>'translatedText'), 'placeholder'=>'') */
    class_exists('ML',false) or die();
?>
<select style="margin-left: 5px" name="<?php echo MLHttp::gi()->parseFormFieldName('filter['.$aFilter['name'].']')?>">
    <?php 
    foreach($aFilter['values'] as $aValue){?>
        <option value="<?php echo $aValue['value']?>"<?php echo $aFilter['value']==$aValue['value']?' selected="selected"':'' ?>><?php echo $aValue['label']?></option>
    <?php } ?>
</select>