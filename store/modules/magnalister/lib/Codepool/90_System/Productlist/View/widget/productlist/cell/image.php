<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {?>
    <?php if($sUrl=$oProduct->getImageUrl()){?>
        <img src="<?php echo $sUrl ?>" title="<?php echo $oProduct->getName()?>" />
    <?php }else{ ?>
        <img width="40" src="<?php echo MLHttp::gi()->getResourceUrl('images/noimage.png')?>" title="<?php echo $this->__('Productlist_Cell_sNoImage')?>" />
    <?php } ?>
<?php } 