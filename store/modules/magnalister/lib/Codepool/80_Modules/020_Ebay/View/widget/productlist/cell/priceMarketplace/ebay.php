<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
    <?php
        $oModel=  MLDatabase::factory('ebay_prepare')->set('products_id', $oProduct->get('id'));
        if($oModel->exists()){
            $oHelper=MLHelper::gi('Model_Table_Ebay_PrepareData');
            /* @var $oHelper ML_Model_Table_Ebay_PrepareData */
            $oHelper->setProduct($oProduct)->setPrepareList(null);
            $aData=$oHelper->getPrepareData(array(
                'startprice'=>array('optional'=>array('active'=>true)),
                'currencyId'
            ));
            $aAuto=$oHelper->setProduct($oProduct)->getPrepareData(array('startprice'));
            ?><span<?php echo $aAuto['startprice']['value']===null?' style="color:gray"':''?>>
                <?php echo MLPrice::factory()->format($aData['startprice']['value'], $aData['currencyId']['value']); ?>
            </span><?php
        }else{
            ?>&mdash;<?php 
        }
    ?>
<?php } ?>