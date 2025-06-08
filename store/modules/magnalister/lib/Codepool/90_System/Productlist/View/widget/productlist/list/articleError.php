<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
    if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {
        if (count(MLMessage::gi()->getObjectMessages($oProduct)) > 0) { ?>
            <div class="errorBox" style="text-align: left">
                <?php if (count(MLMessage::gi()->getObjectMessages($oProduct)) > 1) { ?>
                    <ul>
                        <?php foreach (MLMessage::gi()->getObjectMessages($oProduct) as $sMessage) { ?>
                            <li><?php echo $sMessage ?></li>
                        <?php } ?>
                    </ul>
                <?php }else{?>
                        <?php echo current(MLMessage::gi()->getObjectMessages($oProduct)); ?>
                <?php } ?>
            </div>
        <?php }
    }
?>