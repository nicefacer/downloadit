<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

 /**
  * @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract
  * @var $aFilter array array('name'=>'', 'value'=>'', 'values'=>array('value'=>'','label'=>'translatedText'), 'placeholder'=>'') 
  */
class_exists('ML',false) or die();
?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {?>

    <?php 
        $oProgress = 
            MLController::gi('widget_progressbar')
            ->setId('marketplacesyncfilter')
            ->setTotal(isset($aAjaxData['Total']) ? $aAjaxData['Total'] : 100)
            ->setDone(isset($aAjaxData['Done']) ? $aAjaxData['Done'] : 0)
            ->render()
        ;
    ?>
    
    <select name="<?php echo MLHttp::gi()->parseFormFieldName('filter['.$aFilter['name'].']')?>">
        <?php foreach($aFilter['values'] as $aValue){?>
            <option <?php echo ($aValue['steps'] == '' ? '' : 'data-ml-modal="#'.$oProgress->getId().'" data-marketplacefilter-steps="'.$aValue['steps'].'" '); ?>value="<?php echo $aValue['value']?>"<?php echo $aFilter['value']==$aValue['value']?' selected="selected"':'' ?>><?php echo $aValue['label']?></option>
        <?php } ?>
    </select>
    <script type="text/javascript">
        /*<![CDATA[*/
            (function($) {
                $(document).ready(function() {
                    $('form [name="<?php echo  MLHttp::gi()->parseFormFieldName('filter['.$aFilter['name'].']') ?>"]').change(function(event) {
                        var self = $(this).find('option:selected');
                        if (typeof self.data('marketplacefilter-steps') === "undefined") {
                            return true;
                        } else {
                            $.ajax({
                                url : "<?php echo $this->getCurrentURl(array('ajax' => 'true', 'method' => 'dependency' , 'dependency' => 'marketplacesyncfilter')) ?>",
                                data : {
                                    'ml[marketplacesyncfilter]' : self.data('marketplacefilter-steps')
                                },
                                ml : {
                                    triggerAfterSuccess : function() {
                                        self.parentsUntil('form').parent().trigger('submit');
                                    }
                                }
                            });
                            return false;
                        }
                    });
                });
            })(jqml);
        /*]]>*/
    </script>
<?php }?>