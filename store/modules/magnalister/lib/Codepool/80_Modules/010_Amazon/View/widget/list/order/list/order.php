<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Upload_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
class_exists('ML', false) or die();
?>
<tr class="main" data-actionTopForm="<?php echo $this->getRowAction($aOrder); ?>">
    <?php if ($this->isSelectable()) { ?>
    <th class="cell-magnalisterSelectionRow">
        <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
            <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
        <?php } ?>
        <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('method') ?>" value="deleteFromSelection" />
        <input <?php echo $aOrder['isselected'] ? 'checked="checked"' : '' ?> title="<?php echo $aOrder['AmazonOrderID'] ?>" class="js-mlFilter-activeRowCheckBox" name="<?php echo MLHttp::gi()->parseFormFieldName('method') ?>" value="addToSelection" type="checkbox" />
    </th>
     <?php } ?>
    <?php foreach ($oList->getHead() as $sHead => $aHead) { ?>
        <th class="cell<?php echo isset($aHead['type'])? '-'.$aHead['type']:'' ?>">
            <?php
            if (isset($aHead['type'])) {
                $this->includeView('widget_list_order_cell_amazon_orderlist_' . $aHead['type'], array('aOrder' => $aOrder, 'sHead' => $sHead));
            } else {
                echo $aOrder[$sHead];
            }
            ?>
        </th>
    <?php } ?>
</tr>