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
class_exists('ML',false) or die();

if ($this instanceof ML_Tabs_Controller_Widget_Tabs_Abstract) {
    ob_start();
    try {
        $oTabContent = $this->getTabContentController();
        $oTabContent->render();
        $sContent = ob_get_contents();
    } catch (Exception $oEx) {
        MLMessage::gi()->addDebug($oEx);
        try {
            MLController::gi('widget_message')->renderByMd5($oEx->getCode());
        } catch (Exception $oEx) {
            
        }
        $oTabContent = MLController::gi('main_content_empty');
        $oTabContent->render();
        $sContent = ob_get_contents();
    }
    ob_end_clean();
    //manipulate request for current url
    foreach ($this->getTabUrlHierarchy() as $sParameter => $aConfig) {
        $sRequest = $this->getRequest($sParameter);
        if (!in_array($sRequest, $aConfig)) {
            $this->oRequest->set($sParameter, current($aConfig), true);
        }
    }
    //render
    ?>
    <div class="magnaTabs2">
        <ul>
            <?php  foreach ($this->getTabs() as $aItem) { ?>
                <?php 
                    if (isset($aItem['controllerClass'])) {
                        try {
                            if (!MLFilesystem::gi()->callStatic($aItem['controllerClass'], 'getTabActive')) {// here we check again for changed config
                                $aItem['class'] .= (empty($aItem['class']) ? '' : ' ').'inactive ml-js-noBlockUi';
                            }
                        } catch (ReflectionException $oEx) {
                        }
                        if('controller_'.$oTabContent->getIdent() == $aItem['controllerClass']){
                            $aItem['class'] .= (empty($aItem['class']) ? '' : ' ').'selected';
                        }
                    }
                ?>
                <li class="<?php echo $aItem['class'] ?>"> 
                    <a <?php echo isset($aItem['breadcrumb']) && $aItem['breadcrumb'] !== false ? 'style="pointer-events: none;" class="breadcrumb"':''?>href="<?php echo $aItem['url'] ?>" title="<?php echo str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $aItem['subtitle'].(empty($aItem['label']) ? '' : '&nbsp;::&nbsp;'.$aItem['label']));?>"<?php echo strpos($aItem['class'], 'inactive') !== false ? ' onclick="return false;"' : '' ?>>
                        <?php if (!empty($aItem['image'])) { ?>
                            <img src="<?php echo $aItem['image']; ?>" alt="<?php echo str_replace(array('<', '>', '"'), array('&lt;', '&gt;', '&quot;'), $aItem['subtitle'].(empty($aItem['label']) ? '' : '&nbsp;::&nbsp;'.$aItem['label'])); ?>" />
                        <?php } else { ?>
                            <?php echo str_replace(array('<', '>', '"', '&amp;hellip;'), array('&lt;', '&gt;', '&quot;', '&hellip;'), fixHTMLUTF8Entities($aItem['title'])); ?>
                        <?php } ?>
                        <?php echo str_replace(array('<', '>', '"', '&amp;hellip;'), array('&lt;', '&gt;', '&quot;', '&hellip;'), fixHTMLUTF8Entities($aItem['label'])); ?>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="magnamain"><?php echo $sContent; ?></div><?php
}