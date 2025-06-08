<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
class_exists('ML', false) or die();
?>
    <?php
    $sMpId = MLModul::gi()->getMarketPlaceId();
    $sMpName = MLModul::gi()->getMarketPlaceName();
    ?>
<form action="<?php echo $this->getUrl(array('controller' => "{$sMpName}:{$sMpId}_shippinglabel_upload_summary")); ?>" method="post">

<div class="ml-plist <?php echo MLModul::gi()->getMarketPlaceName(); ?>">
    <?php
    $this->includeView('widget_list_order_list', get_defined_vars());
    $this
            ->includeView('widget_list_order_action_eachRow', array('oList' => $oList, 'aStatistic' => $aStatistic))
            ->includeView('widget_list_order_action_bottom', array('oList' => $oList, 'aStatistic' => $aStatistic))
    ;
    MLSettingRegistry::gi()->addJs('magnalister.productlist.js');
    MLSetting::gi()->add('aCss', array('magnalister.productlist.css?%s'), true);
    ?>
</div>
</form>