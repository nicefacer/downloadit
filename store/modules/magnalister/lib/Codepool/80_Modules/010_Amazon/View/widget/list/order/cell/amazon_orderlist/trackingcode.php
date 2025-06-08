<?php
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aOrder array */
class_exists('ML', false) or die();

echo $aOrder['ShippingService']['CarrierName'].'<br>'.$aOrder['TrackingId'];
