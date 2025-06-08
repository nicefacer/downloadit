<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $aStatistic array */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
        <table class="actions">
            <tbody class="firstChild">
                <tr><td>
                    <div class="actionBottom">
                        <table class="nostyle nospacing nopadding"><tbody>
                            <tr><td>
                                <table class="nostyle nospacing nopadding"><tbody>
                                    <tr><td>
                                        <form action="<?php echo $this->getCurrentUrl() ?>" method="post">
                                            <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
                                                <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
                                            <?php } ?>
                                            <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('execute') ?>" value="unprepare" />
                                            <input class="mlbtn fullWidth" type="submit" value="<?php echo $this->__('ML_EBAY_BUTTON_UNPREPARE'); ?>">
                                        </form>
                                    </td></tr>
                                    <tr><td>
                                        <form action="<?php echo $this->getCurrentUrl() ?>" method="post">
                                            <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
                                                <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
                                            <?php } ?>
                                            <input type="hidden" name="<?php echo MLHttp::gi()->parseFormFieldName('execute') ?>" value="resetdescription" />
                                            <input class="mlbtn fullWidth" type="submit" value="<?php echo $this->__('ML_EBAY_BUTTON_RESET_DESCRIPTION')?>">
                                        </form>
                                    </td></tr>
                                </tbody></table>
                            </td><td>
                                <div class="right">
                                    <a class="mlbtn action" href="<?php echo $this->getUrl(array('controller' => $this->getRequest('controller') . '_form')); ?>">
                                        <?php echo $this->__('ML_AMAZON_BUTTON_PREPARE') ?>
                                    </a>
                                </div>
                                <div class="clear"></div>
                            </td></tr>
                        </tbody></table>
                    </div>
                </td></tr>
            </tbody>
        </table>
<?php } ?>
<?php
