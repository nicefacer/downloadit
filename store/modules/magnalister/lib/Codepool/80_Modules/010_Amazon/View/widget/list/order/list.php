<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aStatistic array */
class_exists('ML', false) or die();
?><?php
$iRow = 0;
$aOrders = $oList->getList();
?><table class="ml-plist-table"><?php
$this->includeView('widget_list_order_list_head', array('oList' => $oList, 'aStatistic' => $aStatistic));
foreach ($aOrders as $aOrder) {
    ?><tbody class="<?php echo $iRow % 2 == 0 ? 'even' : 'odd' ?>" id="orderlist-<?php echo $aOrder['AmazonOrderID']; ?>"><?php
            $this->includeView('widget_list_order_list_order', array('aOrder' => $aOrder, 'oList' => $oList));
            $iRow++;
            ?></tbody><?php
    }
    ?>
</table>