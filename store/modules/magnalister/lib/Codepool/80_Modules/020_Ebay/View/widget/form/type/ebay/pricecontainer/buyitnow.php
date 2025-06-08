<?php 
class_exists('ML', false) or die();
?>
<th><?php echo $this->__('ML_EBAY_BUYITNOW_PRICE'); ?> :</th>
<td class="input"><?php $this->includeType($this->getSubField($aField))?></td>