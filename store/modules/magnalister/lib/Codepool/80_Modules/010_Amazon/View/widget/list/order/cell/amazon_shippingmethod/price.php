<?php

/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
class_exists('ML', false) or die();
?>
<?php
$this->includeView('widget_list_order_cell_price' , array('fAmount' => $aShippingMethod['Rate']['Amount'],'sCurrencyCode' => $aShippingMethod['Rate']['CurrencyCode'])); ?>

