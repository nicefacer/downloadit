<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aStatistic array */
/* @var iLinkedPage int */
/* @var sLabel string */
class_exists('ML', false) or die();
?>
<form action="<?php echo $this->getCurrentUrl() ?>" method="post">
    <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
        <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
    <?php } ?>
    <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('filter[meta][page]') ?>" value="<?php echo $iLinkedPage ?>" />
    <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('filter[meta][order]') ?>" value="<?php echo count($aStatistic['aOrder']) == 2 ? implode('_', $aStatistic['aOrder']) : '' ?>" />
    <?php
    foreach ($oList->getFilters() as $sFilterName => $mFilter) { /** @deprecated array | productlist-depenendcies */ ?>
        <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('filter[' . $sFilterName . ']') ?>" value="<?php echo is_object($mFilter) ? $mFilter->getFilterValue() : $mFilter['value'] ?>" />
    <?php } ?>
    <input class="noButton" type="submit" value="<?php echo $sLabel ?>"<?php echo $aStatistic['iCurrentPage'] == $iLinkedPage ? ' disabled="disabled"' : '' ?> />
</form>