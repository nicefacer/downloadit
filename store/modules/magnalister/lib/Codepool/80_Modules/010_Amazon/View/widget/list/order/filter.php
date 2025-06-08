<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aStatistic array */
class_exists('ML', false) or die();
?>
<form action="<?php echo $this->getCurrentUrl() ?>" method="post" class="js-mlFilter">
    <div>
        <?php
        foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) {
            ?><input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" /><?php
        }
        ?><input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('filter[current]') ?>" value="<?php echo $aStatistic['iCurrentPage'] ?>" /><?php
       
        foreach ($oList->getFilters() as $sFilterName => $mFilter) {
            if (is_object($mFilter)) {
                echo $mFilter->renderFilter($this, $sFilterName);
            } else { /** @deprecated productlist-depenendcies */
                try {
                    $this->includeView('widget_list_order_filter_' . $mFilter['type'] . '_snippet', array('aFilter' => $mFilter));
                } catch (ML_Filesystem_Exception $oEx) {
                    print_r($mFilter);
                }
            }
        }
        ?>
    </div>
</form>