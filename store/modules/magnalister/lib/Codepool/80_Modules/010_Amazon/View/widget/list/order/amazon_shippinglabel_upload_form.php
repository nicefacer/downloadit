<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
class_exists('ML', false) or die();
//        new dBug($aStatistic);
//        new dBug($oList->getHead());
//        new dBug(array('product'=>$oList->getList()->current(),'data'=>$oList->getList()->current()->mixedData()));
?>
<div class="ml-plist <?php echo MLModul::gi()->getMarketPlaceName(); ?>">
    <?php
    $sMpId = MLModul::gi()->getMarketPlaceId();
    $sMpName = MLModul::gi()->getMarketPlaceName();
    ?>
    <form action="<?php echo $this->getUrl(array('controller' => "{$sMpName}:{$sMpId}_shippinglabel_upload_shippingmethod")); ?>" method="post">

        <?php
        $this->includeView('widget_list_order_list', get_defined_vars());
        $this
                ->includeView('widget_list_order_action_eachRow', array('oList' => $oList, 'aStatistic' => $aStatistic))
                ->includeView('widget_list_order_action_bottom', array('oList' => $oList, 'aStatistic' => $aStatistic))
        ;
        MLSettingRegistry::gi()->addJs('magnalister.productlist.js');
        MLSetting::gi()->add('aCss', array('magnalister.productlist.css?%s'), true);
        ?>
        <script type="text/javascript">/*<![CDATA[*/
            (function ($) {
                $(document).ready(function () {
                    $('.ml-shippinglabel-configshipping').change(function (e) {
                        var element = $(this)[0];
                        var index = element.selectedIndex;
                        var selectedValue = element.options[index].value;
                        var sizes = selectedValue.split("-");
                        $('#' + $(this).attr('id') + 'length').val(sizes[0]);
                        $('#' + $(this).attr('id') + "width").val(sizes[1]);
                        $('#' + $(this).attr('id') + "height").val(sizes[2]);
                    });

                    $('.ml-shippinglabel-quantity').change(function (e) {
                        var totalweight = 0; 
                        $('.ml-shippinglabel-quantity.ml-shippinglabel-orderid-'+$(this).attr('data')).each(function(index){
                            var element = $(this)[0];
                            var index = element.selectedIndex;
                            var quantity = element.options[index].value;
                            var weight = $(this).parent().find('.ml-shippinglable-product-weight').val();
                            totalweight += quantity * weight;
                        });
                        $('.ml-shippinglabel-weight-'+$(this).attr('data')).val(totalweight);
                    });

                });
            })(jqml);
            /*]]>*/</script>

    </form>
</div>