<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
class_exists('ML', false) or die();
?>
<div class="ml-hidden-detail">
<span style="text-decoration: underline"> <?php echo $aOrder['Product'] ?></span>
<div class="tooltip">
    <?php
    foreach ($aOrder['ItemList'] as $aItem) {
        echo $aItem['Quantity'] . ' x ' . $aItem['ProductName'] . '<br>';
    }
    ?>
</div>
</div>
