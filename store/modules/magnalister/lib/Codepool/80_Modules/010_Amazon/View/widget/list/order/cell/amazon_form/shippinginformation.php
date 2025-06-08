<?php
/* @var $this  ML_Amazon_Controller_Amazon_ShippingLabel_Orderlist */
/* @var $oList ML_Amazon_Model_List_Amazon_Order */
class_exists('ML', false) or die();
?>
<tbody class="even ml-shippinglabel-form ml-shippinglabel-form-upload" id="orderlist-<?php echo $aOrder['MPSpecific']['MOrderID'] ?>">
    <tr>
        <td colspan="6">
            <table class="fullWidth">
                <tr>
                    <td>
                        <table>
                            <tbody>
                                <tr>
                                    <td colspan="2"><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Shipping_Information_Label') ?>:</td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Package_Size_Label') ?>:</td>
                                    <td>
                                        <?php
                                        $sIdent = MLHttp::gi()->parseFormFieldName($aOrder['MPSpecific']['MOrderID']);
                                        $sHtmlId = str_replace(array('[', ']'), '_', $sIdent);
                                        $aDefault = MLModul::gi()->getConfig('shippinglabel.default.dimension');
                                        $aText = MLModul::gi()->getConfig('shippinglabel.default.dimension.text');
                                        $aLength = MLModul::gi()->getConfig('shippinglabel.default.dimension.length');
                                        $aWidth = MLModul::gi()->getConfig('shippinglabel.default.dimension.width');
                                        $aHeight = MLModul::gi()->getConfig('shippinglabel.default.dimension.height');
                                        $fLength = 0;
                                        $fWidth = 0;
                                        $fHeight = 0;
                                        ?>
                                        <select class="ml-shippinglabel-configshipping" id="<?php echo $sHtmlId ?>">
                                            <?php
                                            $sSizeUnit = MLModul::gi()->getConfig('shippinglabel.size.unit');
                                            $sSizeUnit = ($sSizeUnit == 'centimeters' ? 'cm' : ($sSizeUnit == 'inches' ? 'in' : ''));
                                            foreach ($aDefault as $iKey => $sValue) {
                                                if ($aDefault[$iKey]['default'] == '1' ? 'selected=selected' : '') {
                                                    $fLength = $aLength[$iKey];
                                                    $fWidth = $aWidth[$iKey];
                                                    $fHeight = $aHeight[$iKey];
                                                }
                                                ?>
                                                <option <?php echo $aDefault[$iKey]['default'] == '1' ? 'selected=selected' : '' ?> value="<?php echo $aLength[$iKey] . '-' . $aWidth[$iKey] . '-' . $aHeight[$iKey] ?>">
                                                    <?php echo $aText[$iKey] . ' (' . $aLength[$iKey] . ' ' . $sSizeUnit . ' x ' . $aWidth[$iKey] . ' ' . $sSizeUnit . ' x ' . $aHeight[$iKey] . ' ' . $sSizeUnit . ')'; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Package_Dimension_Label') ?>:</td>
                                    <td>
                                        <table>
                                            <tr>
                                                <td class="normal"><label for="<?php echo $sHtmlId . 'length' ?>"><?php echo $this->__('ML_Amazon_Shippinglabel_Package_Length') ?></label></td><td>:</td><td><input class="ml-shippinglabel-size" id="<?php echo $sHtmlId . 'length' ?>" type="text" name="<?php echo MLHttp::gi()->parseFormFieldName('length[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" value="<?php echo $fLength ?>"/></td><td><?php echo $sSizeUnit ?></td><td>&nbsp;&nbsp;</td>
                                                <td class="normal"><label for="<?php echo $sHtmlId . 'width' ?>"><?php echo $this->__('ML_Amazon_Shippinglabel_Package_Width') ?></label></td><td>:</td><td><input class="ml-shippinglabel-size" type="text" id="<?php echo $sHtmlId . 'width' ?>" name="<?php echo MLHttp::gi()->parseFormFieldName('width[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" value="<?php echo $fWidth ?>"/></td><td><?php echo $sSizeUnit ?></td><td>&nbsp;&nbsp;</td>
                                                <td class="normal"><label for="<?php echo $sHtmlId . 'height' ?>"><?php echo $this->__('ML_Amazon_Shippinglabel_Package_Height') ?></label></td><td>:</td><td><input class="ml-shippinglabel-size" type="text" id="<?php echo $sHtmlId . 'height' ?>" name="<?php echo MLHttp::gi()->parseFormFieldName('height[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" value="<?php echo $fHeight ?>"/></td><td><?php echo $sSizeUnit ?></td><td>&nbsp;&nbsp;</td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->__('ML_GENERIC_WEIGHT') ?>:</td>
                                    <td>
                                        <input type="text" class="ml-shippinglabel-size ml-shippinglabel-weight-<?php echo $aOrder['MPSpecific']['MOrderID'] ?>" name="<?php echo MLHttp::gi()->parseFormFieldName('weight[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" value="<?php echo  $aOrder['TotalWeight'] ?>"/> <span class="normal"><?php echo MLModul::gi()->getConfig('shippinglabel.weight.unit') ?></span>
                                        <span class="infoTextGray"><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Weight_Notice') ?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><?php echo $this->__('ML_LABEL_SHIPPING_DATE') ?>:</td>
                                    <td>
                                        <select name="<?php echo MLHttp::gi()->parseFormFieldName('date[' . $aOrder['MPSpecific']['MOrderID'] . ']'); ?>">
                                            <?php
                                            foreach (array(
                                                date('d.m.Y', time()),
                                                date('d.m.Y', time() + 24 * 60 * 60)
                                             ) as $sDate) {
                                                ?>
                                                <option value="<?php echo $sDate ?>"><?php echo $sDate ?></option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table>
                            <tr>                    
                                <td class="input">
                                    <label ><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Package_Carrierwillpickup_Label') ?>:</label>
                                </td>
                                <td class="normal">
                                    <?php
                                    $aService = MLModul::gi()->MfsGetConfigurationValues('ServiceOptions');
                                    $aOptions = array_key_exists('CarrierWillPickUp', $aService) ? $aService['CarrierWillPickUp'] : array();
                                    $sSelected = MLModul::gi()->getConfig('shippingservice.carrierwillpickup');
                                    foreach ($aOptions as $sKey => $sValue) {
                                        ?>
                                        <input type="radio" <?php echo $sSelected == $sKey ? 'checked=checked' : '' ?> name="<?php echo MLHttp::gi()->parseFormFieldName('carrierwillpickup[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" value="<?php echo $sKey ?>" id="amazon_config_shippinglabel_<?php echo $sKey ?>">
                                        <label for="amazon_config_shippinglabel_<?php echo $sKey ?>"><?php echo $sValue ?></label>
                                    <?php } ?>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td class="input">
                                    <label><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Package_Deliveryexpirience_Label') ?>:</label>
                                </td>
                                <td>
                                    <select name="<?php echo MLHttp::gi()->parseFormFieldName('deliveryexpirience[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" >
                                        <?php
                                        $aService = MLModul::gi()->MfsGetConfigurationValues('ServiceOptions');
                                        $aOptions = array_key_exists('DeliveryExperience', $aService) ? $aService['DeliveryExperience'] : array();
                                        $sSelected = MLModul::gi()->getConfig('shippingservice.deliveryexpirience');
                                        foreach ($aOptions as $sKey => $sValue) {
                                            ?>
                                            <option <?php echo $sSelected == $sKey ? 'selected=selected' : '' ?> value="<?php echo $sKey ?>"> <?php echo $sValue ?></option>
                                        <?php } ?>
                                    </select>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <tr>
                                <td><?php echo $this->__('ML_Amazon_Shippinglabel_Form_Package_SenderAddress_Label') ?>:</td>
                                <td>
                                    <?php
                                    $aDefaultAddress = MLModul::gi()->getConfig('shippinglabel.address');
                                    $aStreet = MLModul::gi()->getConfig('shippinglabel.address.streetandnr');
                                    $aZip = MLModul::gi()->getConfig('shippinglabel.address.zip');
                                    $aCity = MLModul::gi()->getConfig('shippinglabel.address.city');
                                    ?>
                                    <select name="<?php echo MLHttp::gi()->parseFormFieldName('addressfrom[' . $aOrder['MPSpecific']['MOrderID'] . ']') ?>" >
                                        <?php
                                        foreach ($aDefaultAddress as $iKey => $sValue) {
                                            ?>
                                            <option <?php echo $aDefaultAddress[$iKey]['default'] == '1' ? 'selected=selected' : '' ?> value="<?php echo $iKey ?>">
                                                <?php echo $aStreet[$iKey] . ' - ' . $aZip[$iKey] . ' - ' . $aCity[$iKey]; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</tbody>