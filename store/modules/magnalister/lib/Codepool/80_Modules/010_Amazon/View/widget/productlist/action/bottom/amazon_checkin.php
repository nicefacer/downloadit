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
                            <a class="mlbtn action right" href="<?php echo $this->getUrl(array('controller' => $this->getRequest('controller') . '_summary')); ?>">
                               <?php echo $this->__('ML_BUTTON_LABEL_SUMMARY') ?>
                            </a>
                            <div class="clear"></div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
<?php } ?>
