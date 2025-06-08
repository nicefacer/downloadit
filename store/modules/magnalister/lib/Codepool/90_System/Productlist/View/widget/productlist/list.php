<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $aStatistic array */
    class_exists('ML',false) or die();
?><?php 
if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { 
    $iRow = 0;
    $oIterator = $oList->getList();
    ?><table class="ml-plist-table"><?php
        $this->includeView('widget_productlist_list_head', array('oList' => $oList, 'aStatistic' => $aStatistic));
        while ($oIterator->valid()) {
            ?><tbody class="<?php echo $iRow % 2 == 0 ? 'even' : 'odd' ?>" id="productlist-master-<?php echo $oIterator->current()->get('id'); ?>"><?php 
                $this->includeView('widget_productlist_list_article',  array('oProduct'=>$oIterator->current(), 'oList'=>$oList));
                $iRow++;
                $oIterator->next();
            ?></tbody><?php
        }
    ?></table><?php
} 
?>