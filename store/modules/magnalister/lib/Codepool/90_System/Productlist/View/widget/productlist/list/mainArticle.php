<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $oProduct ML_Shop_Model_Product_Abstract */
    class_exists('ML',false) or die();
    if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract && $oProduct->exists()) {
        $iVariantCount = $this->getVariantCount($oProduct);
        $sError = $this->includeViewBuffered('widget_productlist_list_articleerror', array('oProduct' => $oProduct));
        $sProduct = $this->includeViewBuffered('widget_productlist_list_maincells', array('oProduct' => $oProduct, 'oList' => $oList));
        ?>
            <tr class="main" data-actionTopForm="<?php echo $this->getRowAction($oProduct); ?>">
                <th class="<?php echo $iVariantCount > 0 && $sError=='' ? 'switch' : 'no-switch' ?>">
                    <?php 
                        if ($iVariantCount > 0 && $sError=='') { 
                            if(!$this->renderVariants()){ 
                                ?>
                                    <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('render')?>" value="true" />
                                    <a class="ml-js-noBlockUi" href="<?php echo $this->getCurrentUrl(array('ajax'=>'true','method'=>'renderProduct','pid'=>$oProduct->get('id'))) ?>">
                                        &#x25bc;
                                    </a>
                                <?php
                            }else{ 
                                ?>&#x25b2;<?php
                            } 
                        } 
                    ?>
                </th>
                <?php if($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection){ ?>
                    <th class="cell-magnalisterSelectionRow">
                        <?php 
                            if(
                                $sError == '' 
                                && $this->productSelectable($oProduct, true)
                            ){
                                foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue ) { 
                                    ?><input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue?>" /><?php
                                }
                                ?><input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('method')?>" value="deleteFromSelection" /><?php
                                $blChecked = $iVariantCount <= $this->countSelectedVariants($oProduct);
                                ?><input <?php echo $blChecked?'checked="checked"':''?> title="<?php echo $oProduct->get('id')?>" class="js-mlFilter-activeRowCheckBox" name="<?php echo MLHttp::gi()->parseFormFieldName('method')?>" value="addToSelection" type="checkbox" /><?php
                            } 
                        ?>
                    </th>
                <?php } ?>
                <?php if($sError!=''){ ?>
                    <th colspan="<?php echo count($oList->getHead()); ?>">
                        <?php echo $sError; ?>
                    </th>
                <?php }else{ ?>
                    <?php echo $sProduct; ?>
                <?php } ?>
            </tr>
            <?php if ($sError!='') { ?>
                <tr class="main">
                    <th <?php echo ($this instanceof ML_Productlist_Controller_Widget_ProductList_Selection)?' colspan="2"':'';?>></th>
                    <?php echo $sProduct;?>
                </tr>
            <?php } ?>
    <?php
    } 
?>