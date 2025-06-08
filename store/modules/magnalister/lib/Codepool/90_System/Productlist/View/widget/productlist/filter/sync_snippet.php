<?php
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $aFilter array array('name'=>'', 'value'=>'', 'values'=>array('value'=>'','label'=>'translatedText'), 'placeholder'=>'') */
    class_exists('ML',false) or die();
?>
<select name="<?php echo MLHttp::gi()->parseFormFieldName('filter['.$aFilter['name'].']')?>">
	<?php foreach (array() as $sKey => $sI18n) { ?>
		<option value="<?php echo $sKey; ?>"<?php echo($sFilter != null && ($sFilter == $sKey) ? ' selected="selected"' : '') ?>>
			<?php echo sprintf($sI18n, MLModul::gi()->getMarketPlaceName(false)); ?>
		</option>
	<?php } ?>
</select>