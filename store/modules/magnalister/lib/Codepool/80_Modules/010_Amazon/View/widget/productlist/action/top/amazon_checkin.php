<?php class_exists('ML',false) or die()?>
<?php if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) { ?>
    <?php try{ ?>
        <div class="inlineblock">
            <a class="mlbtn ml-js-noBlockUi" target="_blank" style="margin: 0" href="<?php echo MLModul::gi()->getPublicDirLink() ?>"><?php echo MLI18n::gi()->get('Amazon_Productlist_History'); ?></a>
        </div>
    <?php }catch(Exception $oEx){} ?>
<?php } ?>
