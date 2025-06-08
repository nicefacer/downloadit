<?php class_exists('ML', false) or die() ?>
<?php
    $sListingType=$this->getField('listingType', 'value');
?>
<table class="ebayPrice ">
    <tbody>
        <tr>
            <th><?php echo $this->__('ML_EBAY_PRICE_CALCULATED') ?>:</th>
            <td colspan="2">
                <input type="hidden" name="<?php echo  MLHTTP::gi()->parseFormFieldName($this->sOptionalIsActivePrefix.'[startprice]') ?>" value="<?php echo($sListingType=='Chinese')?'true':'false';?>" />
                <?php echo $this->oProduct->getSuggestedMarketplacePrice(MLModul::gi()->getPriceObject($sListingType), true,true) ?>
            </td>
        </tr>
        <?php
            if ($sListingType !== null) {
                if (in_array($sListingType, array('StoresFixedPrice', 'FixedPriceItem'))) {
                    ?>
                    <?php
                } else {//chinese
                    ?>
                        <tr>
                            <?php 
                                $aPrice=$this->getField('startprice');
                                $aPrice['type']=isset($aPrice['optional']['field']['type'])?$aPrice['optional']['field']['type']:$aPrice['type'];
                                $aPrice['value']=number_format((float)$aPrice['value'], 2, '.','');
                                $this->includeType($aPrice); 
                            ?>
                        </tr>
                        <?php 
                            $aBuyItNow=$this->getField('buyitnowprice');
                            $aBuyItNow['value']=number_format((float)$aBuyItNow['value'], 2, '.','');
                        ?>
                        <tr class="buynow"><?php $this->includeType($aBuyItNow); ?></tr>
                    <?php
                }
            }
        ?>
    </tbody>
</table>
