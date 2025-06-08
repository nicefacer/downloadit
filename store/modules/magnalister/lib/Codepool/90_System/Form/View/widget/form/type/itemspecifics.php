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
if (!empty($aField['specifics'])) {
    require_once(MLFilesystem::getOldLibPath('php/lib/classes/GenerateProductsDetailInput.php'));
    $sCategory = $this->getField($aField['parent'], 'value');
    $gPDI = new GenerateProductsDetailInput(
        array($sCategory => $aField['specifics']), 
        isset($aField['value'][$sCategory][$aField['specifics']['key']]) ? $aField['value'][$sCategory][$aField['specifics']['key']] : array(), 
        $sCategory, $aField['realname']
    );
    $sAttributesHtml = $gPDI->render();
} else {
    $sAttributesHtml = '';
}
if (!empty($sAttributesHtml)) {
    $aFieldset = isset($aField['fieldset']) ? $aField['fieldset'] : array() ;
    $aFieldset['legend']['i18n'] = $aField['specifics']['head'];
    $aFieldset['legend']['template'] = 'h4';
    ob_start();
    ?>
        <tr class="headline<?php echo isset($aFieldset['legend']['classes']) ? ' '.implode(' ', $aFieldset['legend']['classes']) : '' ?>">
            <?php $this->includeView('widget_form_legend_'.$aFieldset['legend']['template'],array('aFieldset'=>$aFieldset));?>
        </tr>
    <?php
    $sAttributesHtml = ob_get_contents().$sAttributesHtml;
    ob_end_clean();
} else {
    $sAttributesHtml = '';
}
if (MLHttp::gi()->isAjax()) {
    MLSetting::gi()->add('aAjaxPlugin', array('dom' => array('#'.$aField['id'] => $sAttributesHtml)));
} elseif (isset($aField['show']) && $aField['show'] = true) {
    echo $sAttributesHtml;
}
