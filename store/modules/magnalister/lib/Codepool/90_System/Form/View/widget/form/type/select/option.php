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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
class_exists('ML', false) or die();
$sDataMlAlert = 
    (
        array_key_exists('alert', $aField['i18n']) 
        && is_array($aField['i18n']['alert']) 
        && array_key_exists($aOption['key'], $aField['i18n']['alert'])
        && is_array($aField['i18n']['alert'][$aOption['key']])
        && array_key_exists('title', $aField['i18n']['alert'][$aOption['key']])
        && array_key_exists('content', $aField['i18n']['alert'][$aOption['key']])
    ) 
    ? json_encode($aField['i18n']['alert'][$aOption['key']])
    : ''
;
?>
<option <?php echo empty($sDataMlAlert) ? '' : "data-ml-alert='".$sDataMlAlert."' "; ?>value="<?php echo $aOption['key']?>"<?php echo $aOption['selected']?' selected="selected"':'';?>><?php echo $aOption['value'] ?></option>