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
class_exists('ML', false) or die();

$sName = str_replace('field', '', $aField['name']);
$sNameWithoutValue = str_replace('[Values]', '', $sName);
$aNameWithoutValue = explode('][', $sNameWithoutValue);
$sFirst = substr($aNameWithoutValue[0], 1);
$sLast = end($aNameWithoutValue);
$sLast = substr($sLast, 0, -1);
$sSelector = $this->aFields[$sFirst . '.' . $aNameWithoutValue[1] . '.' . $sLast . '.code']['id'];

//Getting type of tab (is it variation tab or apply form)
$sChangedSelector = ' ' . $sSelector;
$ini = strpos($sChangedSelector, 'hitmeister_prepare_');
if ($ini == 0) return '';
$ini += strlen('hitmeister_prepare_');
$len = strpos($sChangedSelector, '_field', $ini) - $ini;
$tabType = substr($sChangedSelector, $ini, $len);

?>
<span>
    <table style="width:100%;">
        <?php if (!empty($aField['i18n']['matching']['titlesrc']) || !empty($aField['i18n']['matching']['titledst'])) { ?>
        <thead>
            <th style="width: 35%"><?php echo $aField['i18n']['matching']['titlesrc']; ?></th>
            <th style="width: 35%"><?php echo $aField['i18n']['matching']['titledst']; ?></th>
        </thead>
        <?php } ?>
        <tbody>
                <tr>
                    <td style="width: 35%">
                        <?php
                        $aSelect = array(
                            'name' => $aField['name'] . '[0][Shop][Key]',
                            'type' => 'select',
                            'i18n' => array(),
                            'values' => $aField['valuessrc'],
                            'value' => current($aField['valuessrc'])
                        );
                        $aHidden = array(
                            'type' => 'hidden',
                            'id' => $sSelector . '_hidden_shop_value',
                            'name' => $aField['name'] . '[0][Shop][Value]'
                        );

                        if (isset($aField['error'])) {
                            $aSelect['cssclass'] = 'error';
                        }

                        $aNewArray = array(
                            'noselection' => MLI18n::gi()->get('form_type_matching_select_optional'),
                            'all' => MLI18n::gi()->get('form_type_matching_select_all'),
                        );
                        foreach ($aSelect['values'] as $sSelectKey => $sSelectValue) {
                            $aNewArray[$sSelectKey] = $sSelectValue;
                        }

                        $aSelect['values'] = $aNewArray;
                        $this->includeType($aSelect);
                        $this->includeType($aHidden);
                        ?>
                    </td>
                    <td style="width: 35%">
                        <?php
                            $aSelect = array(
                                'name' => $aField['name'] . '[0][Marketplace][Key]',
                                'type' => 'select',
                                'i18n' => array(),
                                'values' => $aField['valuesdst']['values'],
                                'value' => current($aField['valuesdst']['values'])
                            );
                            $aHidden = array(
                                'type' => 'hidden',
                                'id' => $sSelector . '_hidden_marketplace_value',
                                'name' => $aField['name'] . '[0][Marketplace][Value]'
                            );

                            if (isset($aField['error'])) {
                                $aSelect['cssclass'] = 'error';
                            }

                            // Changed because in previous implementation array keys are recreated.
                            $aNewArray = array(
                                'noselection' => MLI18n::gi()->get('form_type_matching_select_optional'),
                                'auto' => MLI18n::gi()->get('form_type_matching_select_auto'),
                                'reset' => MLI18n::gi()->get('form_type_matching_select_reset'),
                                'manual' => MLI18n::gi()->get('form_type_matching_select_manual'),
                            );

                            if ($aField['valuesdst']['from_mp']) {
                                unset($aNewArray['manual']);
                            }

                            foreach ($aSelect['values'] as $sSelectKey => $sSelectValue) {
                                $aNewArray[$sSelectKey] = $sSelectValue;
                            }

                            $aSelect['values'] = $aNewArray;
                            $this->includeType($aSelect);
                            $this->includeType($aHidden);
                        ?>
                    </td>
                    <td id="freetext_<?php echo $sLast?>" style="border: none; display: none;">
                        <input type="text" name="ml[field]<?php echo $sName ?>[FreeText]" style="width:100%;">
                    </td>
                    <td style="border: none">
                        <?php if ($tabType === 'variations') { ?>
                            <button type="submit" value="0" id="hitmeister_prepare_variations_field_saveaction" class="mlbtn" style="font-weight: bold;" name="ml[action][saveaction]">+</button>
                        <?php } else { ?>
                            <button type="submit" value="0" id="hitmeister_prepare_apply_form_field_prepareaction" class="mlbtn" style="font-weight: bold;" name="ml[action][prepareaction]">+</button>
                        <?php } ?>
                    </td>
                </tr>
        </tbody>
    </table>
</span>
<?php
    if (!empty($aField['values']) && is_array($aField['values'])) {
?>
<span id="spanMatchingTable" style="padding-right:2em;">
    <div style="font-weight: bold;">
        <?php echo MLI18n::gi()->get('hitmeister_prepare_variations_matching_table'); ?>
    </div>
    <table id="<?php echo $sSelector ?>_button_matched_table" style="width:100%;">
        <tbody>
        <?php
        $i = 1;
        foreach ($aField['values'] as $sKey => $aValue) {
            $aNewFieldShopKey = array(
                'type' => 'hidden',
                'id' => $sSelector . '_shop_key_' . $i,
                'name' => $aField['name'] . '[' . $i . '][Shop][Key]',
                'value' => $aValue['Shop']['Key']
            );
            $aNewFieldShopValue = array(
                'type' => 'hidden',
                'id' => '$sSelector . \'_shop_value_' . $i,
                'name' => $aField['name'] . '[' . $i . '][Shop][Value]',
                'value' => $aValue['Shop']['Value']
            );
            $aNewFieldMarketplaceKey = array(
                'type' => 'hidden',
                'id' => $sSelector . '_marketplace_key_' . $i,
                'name' => $aField['name'] . '[' . $i . '][Marketplace][Key]',
                'value' => $aValue['Marketplace']['Key']
            );
            $aNewFieldMarketplaceValue = array(
                'type' => 'hidden',
                'id' => $sSelector . '_marketplace_value_' . $i,
                'name' => $aField['name'] . '[' . $i . '][Marketplace][Value]',
                'value' => $aValue['Marketplace']['Value']
            );
            $i++;
        ?>
            <tr>
                <td style="width: 35%">
                    <?php
                        $this->includeType($aNewFieldShopKey);
                        $this->includeType($aNewFieldShopValue);
                        echo $aValue['Shop']['Value'];
                    ?>
                </td>
                <td style="width: 35%">
                    <?php
                        $this->includeType($aNewFieldMarketplaceKey);
                        $this->includeType($aNewFieldMarketplaceValue);
                        echo $aValue['Marketplace']['Value'];
                    ?>
                </td>
                <td style="border: none">
                    <button class="mlbtn" id="<?php echo $sSelector ?>_button" style="font-weight: bold; float: left" onclick="deleteRow(this)">-</button>
                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</span>
<?php
}
?>
<div id="infodiag" class="ml-modal dialog2" title="Hinweis"></div>
<span id="beforereset" style="display: none"><?= MLI18n::gi()->get('hitmeister_prepare_variations_reset_info') ?></span>
<script>
    (function($) {
        $('[name="ml[field]<?php echo $sName ?>[0][Shop][Key]"]').on('change', function() {
            var val = $('[name="ml[field]<?php echo $sName ?>[0][Shop][Key]"] option:selected').html();
            $('[name="ml[field]<?php echo $sName ?>[0][Shop][Value]"]').val(val);
        });

        $('[name="ml[field]<?php echo $sName ?>[0][Marketplace][Key]"]').on('change', function() {
            var val = $('[name="ml[field]<?php echo $sName ?>[0][Marketplace][Key]"] option:selected').html();
            $('[name="ml[field]<?php echo $sName ?>[0][Marketplace][Value]"]').val(val);
            var oldValue = $('[name="ml[field]<?php echo $sName ?>[0][Marketplace][Key]"]').defaultValue;
            if ($(this).val() === 'reset') {
                var d = $('#beforereset').html();
                $('#infodiag').html(d).jDialog({
                    width: (d.length > 1000) ? '700px' : '500px',
                    buttons: {
                        '<?php echo ML_BUTTON_LABEL_ABORT; ?>': function() {
                            $(this).dialog('close');
                            $('[name="ml[field]<?php echo $sName ?>[0][Marketplace][Key]"]').val(oldValue);
                        },
                        '<?php echo ML_BUTTON_LABEL_OK; ?>': function() {
                            $(this).dialog('close');
                        }
                    }
                });
            }

            if ($(this).val() === 'manual') {
                $('td #freetext_<?php echo $sLast?>').show();
            } else {
                $('td #freetext_<?php echo $sLast?>').hide();
            }
        });
    })(jqml);
</script>

