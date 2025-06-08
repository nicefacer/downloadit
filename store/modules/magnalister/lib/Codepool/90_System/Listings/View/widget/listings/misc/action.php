<?php class_exists('ML', false) or die() ?>
<input type="hidden" id="action" name="<?php echo MLHttp::gi()->parseFormFieldName('action') ?>" value="">
<input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('timestamp') ?>" value="<?php echo time() ?>">
<table class="actions">
    <thead><tr><th><?php echo $this->__('ML_LABEL_ACTIONS') ?></th></tr></thead>
    <tbody>
        <tr>
            <td>
                <div class="actionBottom">
                    <table>
                        <tbody>
                            <tr>
                                <td class="firstChild">
                                    <input type="button" class="mlbtn" value="<?php echo $this->__('ML_BUTTON_LABEL_DELETE') ?>" id="listingDelete" name="<?php echo MLHttp::gi()->parseFormFieldName('listing[delete]'); ?>"/>
                                </td>
                                <td>
                                    <?php if($this->isSearchable()){?>
                                    <div class="newSearch">
                                        <input id="tfSearch" placeholder="<?php $this->__('Productlist_Filter_sSearch') ?>"  name="<?php echo MLHttp::gi()->parseFormFieldName('tfSearch') ?>" type="text" value="<?php echo fixHTMLUTF8Entities($this->search, ENT_COMPAT) ?>"/>
                                        <button type="submit" class="mlbtn action">
                                            <span></span>
                                        </button>
                                    </div>
                                    <?php }?>
                                </td>
                                <td class="lastChild">
                                    <table class="right">
                                        <tbody>
                                            <tr>
                                                <td class="firstChild">
                                                    <!--   @todo hook rightaction -->
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">/*<![CDATA[*/
    jqml(document).ready(function() {
        jqml('#listingDelete').click(function() {
            if ((jqml('.ml-js-plist input[type="checkbox"]:checked').length > 0) &&
                    confirm(unescape(<?php
echo "'" . html_entity_decode(sprintf($this->__('ML_GENERIC_DELETE_LISTINGS'), $this->getShopTitle())) . "'";
?>))
                    ) {
                jqml('#action').val('delete');
                jqml(this).parents('form').submit();
            }
        });
    });
    /*]]>*/</script>