<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
    <?php
        $oModel = MLDatabase::factory('bepado_prepare')->set('products_id', $oProduct->get('id'));
        if($oModel->exists()){
            echo $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject('b2c'), true, true);
            if ($oModel->get('isb2b')) {
                ?><br /><span style="color:gray"><?php
                    echo $oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject('b2b'), false, true); 
                ?></span><?php
            }
        }else{
            ?>&mdash;<?php 
        }
    ?>
<?php } ?>