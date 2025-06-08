<?php
class_exists('ML', false) or die();
?>
<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
/* @var $aStatistic array */

$sMpId = MLModul::gi()->getMarketPlaceId();
$sMpName = MLModul::gi()->getMarketPlaceName();

$sUrlPrefix = "{$sMpName}:{$sMpId}_";
$sI18nPrefix = 'ML_' . ucfirst($sMpName) . '_';
?>



<table class="actions">
    <tbody class="firstChild">
        <tr>
            <td>
                <div class="actionBottom">
                    <a class="js-marketplace-upload mlbtn action right" href="<?php echo $this->getUrl(array('controller' => "{$sUrlPrefix}ShippingLabel_Upload_Summary", 'method' => 'confirmShipping')); ?>" title="<?php echo ML_STATUS_FILTER_SYNC_ITEM ?>" >
                        <?php echo $this->__('ML_Amazon_Shippinglabel_Confirm') ?>
                    </a>
                    <a class="mlbtn backbtn right" href="<?php echo $this->getUrl(array('controller' => "{$sUrlPrefix}Shippinglabel_Upload_Shippingmethod")); ?>">
                        <?php echo $this->__('ML_BUTTON_LABEL_BACK') ?>
                    </a>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">/*<![CDATA[*/
    (function ($) {
        $(document).ready(function () {
            $('.js-marketplace-upload').click(function () {
                $(this).magnalisterRecursiveAjax({
                    sOffset: '<?php echo MLHttp::gi()->parseFormFieldName('offset') ?>',
                    sAddParam: '<?php echo MLHttp::gi()->parseFormFieldName('ajax') ?>=true',
                    oFinalButtons: {
                        oError: [
                            {text: 'Ok', click: function () {
                                    var eDialog = $('#recursiveAjaxDialog');
                                    if (eDialog.find(".requestErrorBox").is(':hidden')) {
                                        window.location.href = '<?php
                        echo $this->getUrl(array('controller' => "{$sUrlPrefix}errorlog"));
                        ?>';
                                    } else {
                                        window.location.href = '<?php echo $this->getCurrentUrl() ?>';
                                    }
                                }}
                        ],
                        oSuccess: [
                            {text: 'Download ', click: function () {
                                    if ($('a.ml-downloadshippinglabel').length > 0) {
                                        $('a.ml-downloadshippinglabel')[0].click();
                                    }
                                }
                            }

                        ]
                    },
                    oI18n: {
                        sProcess: <?php echo json_encode($this->__('ML_STATUS_FILTER_SYNC_CONTENT')) ?>,
                        sError: <?php echo json_encode($this->__('ML_ERROR_GLOBAL')) ?>,
                        sErrorLabel: <?php echo json_encode($this->__('ML_ERROR_LABEL')) ?>,
                        sSuccess: <?php echo json_encode($this->__('ML_Amazon_Shippinglabel_Summary_Statistic')) ?>,
                        sSuccessLabel: <?php echo json_encode($this->__('ML_Amazon_Shipping_Success')) ?>,
                        sInfo: <?php echo json_encode($this->__('ML_Amazon_Shippinglabel_Popup_Afterconfirm_Infocontent')) ?>
                    },
                    onProgessBarClick: function (data) {
                        console.dir({data: data});

                    },
                    onFinalize: function (blError) {

                    },
                    blDebug: <?php echo MLSetting::gi()->get('blDebug') ? 'true' : 'false' ?>,
                    sDebugLoopParam: "<?php echo MLHttp::gi()->parseFormFieldName('saveSelection') ?>=true"
                });
                return false;
            });
        });
    })(jqml);
    /*]]>*/</script>