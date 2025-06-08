<?php 
    class_exists('ML',false) or die();
    $aData = $aField['ebay_attributes'];
    if (isset($aField['value'][$aData['categoryId']])) {
        $aValue = $aField['value'][$aData['categoryId']];
        if (isset($aValue['specifics'])) {
            $aField['value'] = $aValue['specifics'];
        }else{
            $aField['value'] = $aValue['attributes'];
        }
    }
    $aPreselect = isset($aField['value'][$aData['categoryId']]['specifics']) ? $aField['value'][$aData['categoryId']]['specifics'] : array();
    $sAttributesHtml = getEBayAttributes($aData['categoryId'], $aField['value'], $aField['realname'], $this->oProduct );
    
    if ( MLHttp::gi()->isAjax()) {
        MLSetting::gi()->add('aAjaxPlugin', array('content' => $sAttributesHtml));
    } else {
        echo $sAttributesHtml;
    }
?>