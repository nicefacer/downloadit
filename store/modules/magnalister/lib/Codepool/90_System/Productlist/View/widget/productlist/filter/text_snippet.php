<?php
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $aFilter array array('name'=>'', 'value'=>'', 'values'=>array('value'=>'','label'=>'translatedText'), 'placeholder'=>'') */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {?>
    <input type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('filter['.$aFilter['name'].']')?>" value="<?php echo $aFilter['value']?>" placeholder="<?php echo $this->__($aFilter['placeholder']); ?>" />
<?php } ?>
