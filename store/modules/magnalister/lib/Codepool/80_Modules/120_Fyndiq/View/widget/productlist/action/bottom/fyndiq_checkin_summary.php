<?php
/* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
/* @var $oList ML_Productlist_Model_ProductList_Abstract */
/* @var $aStatistic array */
class_exists('ML', false) or die();

  MLSettingRegistry::gi()->addJs('jquery.preUploadPopup.js');
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
    <table class="actions">
        <tbody class="firstChild">
        <tr>
            <td>
                <div class="actionBottom right">
                    <table>
                        <tr>
                            <td class="textleft">
                                <form class="right" action="<?php echo $this->getCurrentUrl() ?>" method="post"
                                      title="<?php echo ML_STATUS_FILTER_SYNC_ITEM ?>">
                                    <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
                                        <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>"/>
                                    <?php } ?>
                                    <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('method') ?>"
                                           value="checkinAdd"/>
                                    <input type="submit" value="<?php echo $this->__('ML_BUTTON_LABEL_CHECKIN_ADD') ?>"
                                           class="ml-js-noBlockUi js-marketplace-upload mlbtn action"/>
                                </form>
                            </td>
                        </tr>
                        <tr>
                            <td class="textleft">
                                <form class="right" action="<?php echo $this->getCurrentUrl() ?>" method="post"
                                      title="<?php echo ML_STATUS_FILTER_SYNC_ITEM ?>">
                                    <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
                                        <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>"/>
                                    <?php } ?>
                                    <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('method') ?>"
                                           value="checkinPurge"/>
                                    <input type="submit"
                                           value="<?php echo $this->__('ML_BUTTON_LABEL_CHECKIN_PURGE') ?>"
                                           class="ml-js-noBlockUi js-marketplace-upload mlbtn"/>
                                </form>
                            </td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <script type="text/javascript">/*<![CDATA[*/
        (function($) {
            function addItems(form) {
                $(form).magnalisterRecursiveAjax({
                    sOffset: '<?php echo MLHttp::gi()->parseFormFieldName('offset') ?>',
                    sAddParam: '<?php echo MLHttp::gi()->parseFormFieldName('ajax') ?>=true',
                    oFinalButtons       : {
                        oError      : [
                            {text:'Ok',click:function(){
                                window.location.href = '<?php $sMpId =MLModul::gi()->getMarketPlaceId();$sMpName = MLModul::gi()->getMarketPlaceName();echo $this->getUrl(array('controller' => "{$sMpName}:{$sMpId}_errorlog")); ?>';
                            }}
                        ],
                        oSuccess    : [
                            {text:'Ok',click:function(){
                                window.location.href = '<?php echo $this->getUrl(array('controller'=>   "{$sMpName}:{$sMpId}_listings")); ?>';
                            }}
                        ]
                    },
                    oI18n: {
                        sProcess: <?php echo json_encode($this->__('ML_STATUS_FILTER_SYNC_CONTENT')) ?>,
                        sError: <?php echo json_encode($this->__('ML_ERROR_SUBMIT_PRODUCTS')) ?>,
                        sErrorLabel: <?php echo json_encode($this->__('ML_ERROR_LABEL'))?>,
                        sSuccess: <?php echo json_encode($this->__('ML_STATUS_SUBMIT_PRODUCTS_SUMMARY'))?>,
                        sSuccessLabel: <?php echo json_encode($this->__('ML_STATUS_FILTER_SYNC_SUCCESS')) ?>,
                        sInfo: <?php echo json_encode($this->__('fyndiq_upload_explanation')) ?>
                    },
                    onProgessBarClick: function(data) {
                        console.dir({data: data});

                    },
                    onFinalize:function(blError){

                    },
                    blDebug: <?php echo MLSetting::gi()->get('blDebug') ? 'true' : 'false' ?>,
                    sDebugLoopParam : "<?php echo MLHttp::gi()->parseFormFieldName('saveSelection') ?>=true"
                });
            }

            $(document).ready(function() {
                $('.js-marketplace-upload').click(function() {
                    $(this).configureUploadPopup({
                        addItems: addItems,
                        message: '<?= MLI18n::gi()->get('fyndiq_pre_upload_popup') ?>',
                        i18n: {
                            ok: '<?= $this->__('ML_BUTTON_LABEL_OK') ?>',
                            abort: '<?= $this->__('ML_BUTTON_LABEL_ABORT') ?>',
                        }
                    });


                    return false;
                });
            });
        })(jqml);
        /*]]>*/</script>

   <?php
 }