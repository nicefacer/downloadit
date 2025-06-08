<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {
    try{
        echo $oProduct->getSuggestedMarketplacePrice($this->getPriceObject($oProduct), true,true);
    }catch(Exception $oEx){//marketplace have multiple price-configs eg. eBay
    }
} ?>