<?php 
    class_exists('ML',false) or die();
    if($aField['realname']=='secondarycategory'){
        $iWidth=48;
    }else{
        $iWidth=100;
    }
//    new dBug($aField);
?>
<button 
    data-field="<?php echo $aField['id']?>"
    data-variationsEnabled="<?php echo $aField['realname']=='primarycategory'?'true':'false'; ?>"
    data-method="<?php echo $aField['realname'].'attributes'?>"
    data-store="<?php echo in_array($aField['realname'],array('storecategory','storecategory2'))?>"
    class="mlbtn js-category-dialog <?php echo $aField['realname']=='primarycategory'?'action':''; ?>"
    style="width:<?php echo $iWidth; ?>%;margin:0;display:inline;float:left;" type="button">
        <div style="width:100%;float:left;">
            <?php echo $aField['i18n']['hint'] ?>
        </div>
        <div class="clear"></div>
    </button>
<?php
    if($aField['realname']=='secondarycategory'){
        ?><button class="mlbtn js-category-clean" style="width:<?php echo $iWidth; ?>%;margin:0;display:inline;float:right;" type="button">
            <div style="width:100%;float:right;">
                <?php echo $this->__('ML_EBAY_DELETE')?>
                <span class="gfxbutton small delete"></span>
            </div>
            <div class="clear"></div>
        </button><?php
    }
?>