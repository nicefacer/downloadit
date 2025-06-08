<?php
/* @var $this  ML_Productlist_Controller_Widget_ProductList_Abstract */
/* @var $oList ML_Productlist_Model_ProductList_Abstract */
/* @var $oProduct ML_Shop_Model_Product_Abstract */
class_exists('ML', false) or die();
if ($this instanceof ML_Productlist_Controller_Widget_ProductList_Abstract) {
    $oPrepare = $this->getPrepareData($oProduct);
    $aI18n = $this->__('Ricardo_CheckinForm');
    ?>
    <table style="width:100%">
        <tr>
            <th>
                <?php echo $aI18n['Price'] ?>:
            </th>
            <td>
                <?php echo $this->getPrice($oProduct) ?>
            </td>
            <td style="color:gray;font-style: italic;float:right;">
                (
                <?php echo $aI18n['ShopPrice'] ?>: <?php echo $oProduct->getShopPrice(true, false); ?>, 
                <?php echo $aI18n['SuggestedPrice'] ?>: <?php echo $oProduct->getSuggestedMarketplacePrice($this->getPriceObject($oProduct), true, false) ?>
                )
            </td>
        </tr>
        <tr>
            <th><?php echo $aI18n['Amount'] ?>:</th>
            <td><?php echo $this->getStock($oProduct) ?></td>
            <td style="color:gray;font-style: italic;float:right;">
                (
                <?= $aI18n['AvailibleAmount'] ?>: <?= $oProduct->getStock() ?>, 
                <?= $aI18n['SuggestedAmount'] ?>: <?= $this->getStock($oProduct) ?>
                )
            </td>
        </tr>
   </table>
<?php }