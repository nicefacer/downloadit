<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
class_exists('ML', false) or die();
//        new dBug($aStatistic);
//        new dBug($oList->getHead());
//        new dBug(array('product'=>$oList->getList()->current(),'data'=>$oList->getList()->current()->mixedData()));
?>
<div class="ml-plist <?php echo MLModul::gi()->getMarketPlaceName(); ?>">
    <table class="fullWidth nospacing nopadding valigntop topControls"><tbody><tr>
                <td class="actionLeft">
                    <?php
                    if ($this->isSelectable()) {
                        $this->includeView('widget_list_order_action_selection', array('oList' => $oList, 'aStatistic' => $aStatistic));
                    }
                    $this->includeView('widget_list_order_action_top', array('oList' => $oList, 'aStatistic' => $aStatistic));
                    ?>
                </td>
                <td>
                    <table class="nospacing nopadding right"><tbody><tr>
                                <td class="filterRight">
                                    <div class="filterWrapper">
                                        <?php
                                        $this->includeView('widget_list_order_filter', get_defined_vars());
                                        ?>
                                    </div>
                                </td>
                            </tr></tbody></table>
                </td>
            </tr></tbody></table>
    <div class="clear"></div>
    <div class="pagination_bar">
        <?php
        if ($this->showPagination()) {
            $this->includeView('widget_list_order_pagination', get_defined_vars());
        }
        ?>
    </div>
    <?php
    $this->includeView('widget_list_order_list', get_defined_vars());
    ?>
    <div class="pagination_bar">
        <?php
        if ($this->showPagination()) {
            $this->includeView('widget_list_order_pagination', get_defined_vars());
        }
        ?>
    </div>
    <?php
    $this
            ->includeView('widget_list_order_action_eachRow', array('oList' => $oList, 'aStatistic' => $aStatistic))
            ->includeView('widget_list_order_action_bottom', array('oList' => $oList, 'aStatistic' => $aStatistic))
    ;
    MLSettingRegistry::gi()->addJs('magnalister.productlist.js');
    MLSetting::gi()->add('aCss', array('magnalister.productlist.css?%s'), true);
    MLSetting::gi()->add('aCss', array('magnalister.amazon.shippinglabel.css?%s'), true);
    ?>
</div>