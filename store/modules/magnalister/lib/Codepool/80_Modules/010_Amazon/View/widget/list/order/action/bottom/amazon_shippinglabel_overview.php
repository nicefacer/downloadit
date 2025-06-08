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
                    <form action="<?php echo $this->getUrl(array('controller' => "{$sUrlPrefix}shippinglabel_overview")); ?>" method="POST">
                        <button type="submit" name="<?php echo MLHttp::gi()->parseFormFieldName('method')?>" value="delete" class="mlbtn ml-js-config-reset left" >
                            <?php echo $this->__('ML_Amazon_Shippinglabel_Delete') ?>
                        </button>
                        <button type="submit" name="<?php echo MLHttp::gi()->parseFormFieldName('method')?>" value="cancel" class="mlbtn action right">
                            <?php echo $this->__('ML_Amazon_Shippinglabel_Cancel') ?>
                        </button>
                        <button type="submit" name="<?php echo MLHttp::gi()->parseFormFieldName('method')?>" value="download" class="mlbtn ml-js-config-reset right" style="margin-right: 1em;">
                            <?php echo $this->__('ML_Amazon_Shippinglabel_Download') ?>
                        </button>
                    </form>
                    <div class="clear"></div>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<?php if($this->getDownloadLink() !== null){ ?>
<script type="text/javascript">//<![CDATA[
    (function ($) {
        $(document).ready(function () {
            var eModal = $('<div title="<?php echo MLI18n::gi()->get('ML_Amazon_Shippinglabel_Download_Title') ?>"><?php echo MLI18n::gi()->get('ML_Amazon_Shippinglabel_Overview_Popup_Afterconfirm_Infocontent') ?><a class="ml-downloadshippinglabel" target="_blank" href="<?php echo $this->getDownloadLink() ?>"></a></div>');
            eModal.dialog({
                modal: true,
                width: '600px',
                buttons: [
                    {
                        text: "DOWNLOAD",
                        click: function () {
                                if ($('.ml-downloadshippinglabel').length > 0) {
                                    $('.ml-downloadshippinglabel')[0].click();
                                }
                                return false;
                        }
                    },
                    {
                        text: "OK",
                        click: function () {
                            $(this).dialog("close");
                            return false;
                        }
                    }
                ]
            });
        });
    })(jqml);
    //]]></script>
<?php }