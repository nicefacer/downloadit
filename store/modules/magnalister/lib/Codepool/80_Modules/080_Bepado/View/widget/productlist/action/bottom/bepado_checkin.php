<?php 
    /* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
    /* @var $oList ML_Productlist_Model_ProductList_Abstract */
    /* @var $aStatistic array */
    class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
        <table class="actions">
            <tbody class="firstChild">
                <tr>
                    <td>
                        <div class="actionBottom">
                            <div style="float:right">
                                 <form action="<?php echo $this->getUrl(array('controller' => $this->getRequest('controller').'_summary')) ?>" method="post">
                                    <?php foreach (MLHttp::gi()->getNeededFormFields() as $sName => $sValue) { ?>
                                        <input type="hidden" name="<?php echo $sName ?>" value="<?php echo $sValue ?>" />
                                    <?php } ?>
                                    <input type="submit" value="<?php echo $this->__('ML_BUTTON_LABEL_SUMMARY') ?>" class="mlbtn action" />
                                </form>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
<?php } ?>
