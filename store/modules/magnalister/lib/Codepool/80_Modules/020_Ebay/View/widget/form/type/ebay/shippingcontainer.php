<?php class_exists('ML',false) or die();?>
<div class="shipping">
<?php
$aField=array('name'=>  substr($aField['realname'], 0,-9));
$aField=$this->getField($aField);
$this->includeType($aField);
if(!MLHttp::gi()->isAjax()){
    ?>
        <div class="profile">
            <div>
                <?php
                    $aProfile=array('name'=>$aField['realname'].'profile');
                    $aProfile=$this->getField($aProfile);
                    $this->includeType($aProfile);
                    $aDiscount=array('name'=>$aField['realname'].'discount');
                    $aDiscount=$this->getField($aDiscount);
                    $this->includeType($aDiscount);
                    echo '&nbsp;'.$aDiscount['i18n']['label']
                ?>
            </div>
        </div>
    <?php
}
?>
</div>