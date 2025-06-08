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
    class_exists('ML',false) or die();
    foreach (array('SyncOrderStatus', 'SyncInventory', 'ImportOrders') as $sSync) {
        MLSetting::gi()->{'s'.$sSync.'Url'} = MLHttp::gi()->getFrontendDoUrl(array('do' => $sSync, 'auth' => md5(MLShop::gi()->getShopId().trim(MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.passphrase')->get('value')))));
    }
?>
<?php ob_start();?>
<div class="magna">
    <?php
        if (MLCache::gi()->exists('Model_Image__BrokenImageResize')) {
            $aImage = MLCache::gi()->get('Model_Image__BrokenImageResize');
            try {
                $sUrl = MLImage::gi()->getFallBackUrl($aImage['sSrc'], $aImage['sDst'], $aImage['iMaxWidth'], $aImage['iMaxHeight']);
                ?><iframe style="display:none" src="<?php echo $sUrl; ?>"></iframe><?php
                $oTable = MLDatabase::getTableInstance('image')
                    ->set('sourcePath', $aImage['sSrc'])
                    ->set('destinationPath', $aImage['sDst'])
                    ->set('skipCheck', true)
                    ->save()
                ;
            } catch (Exception $oEx) {
                // not implemented MLImage::getFallBackUrl() is shopspecific
            }
            MLCache::gi()->delete('Model_Image__BrokenImageResize');
        }
    ?>
    <?php
        $this->includeView('widget_js_i18n');
    ?>
    <!--<messages />-->
    <table id="tableWrap" border="0" width="100%" cellspacing="0" cellpadding="2" style="padding: 0 10px;">
        <tbody>
            <tr>
                <td width="100%">
                    <?php $this->headController()->render(); ?>
                    <?php $this->getTabsWidget(); ?>
                    <?php $this->footController()->render(); ?>
                </td>
            </tr>
        </tbody>
    </table>
    <?php 
        try {
            foreach (MLSetting::gi()->get('aModals') as $sModal) {
                echo $sModal;
            }
        } catch (MLSetting_Exception $oEx) {
            //no modals setted
        }
        try {
            foreach (MLSetting::gi()->get('aScripts') as $sScript) {
                echo $sScript;
            }
        } catch (MLSetting_Exception $oEx) {
            //no modals setted
        }
    ?>
</div>
<?php
MLSettingRegistry::gi()->addJs('magnalister.global.ajax.js');
$sMain = ob_get_clean();
$sClientVersion = MLSetting::gi()->get('sClientBuild');
$aResourcesFound = array();
foreach (array('css', 'js') as $sResourceType) {
    $aResourcesFound[$sResourceType] = true; 
    foreach(MLSetting::gi()->get('a'.  ucfirst($sResourceType)) as $sFile){
        $aFile = MLFilesystem::gi()->findResource('resource_'.$sResourceType.'_'.$sFile);
        $sResourceUrl = MLHttp::gi()->getResourceUrl($sResourceType.'/'.sprintf($sFile, $sClientVersion));
        if (MLSetting::gi()->get('blInlineResource')) {
            $aResourcesFound[$sResourceType] = false;
            MLMessage::gi()->addError(MLI18n::gi()->get('sMessageCannotLoadResource'));
            MLMessage::gi()->addDebug('Can not load Resource', array(
                '$sResourceUrl' => $sResourceUrl
            ));
        }
        break; // only check one resource
    }
}
ob_start();
?>
    <div class="ml-js-mlMessages" id="ml-js-pushMessages"><?php
        $this->includeView('main_messages');
    ?></div>
    <?php MLLog::gi()->render();?>
<?php
$sMessage = ob_get_clean();
$sMain = str_replace('<!--<devBar />-->', $this->includeViewBuffered('main_debug_bar'), $sMain); //replace placeholder with debug-bar
$sMain = str_replace ('<!--<messages />-->', $sMessage, $sMain); //replace placeholder with messages.
$sIdent = '<div class="magnamain">';
$iLastMain = strrpos($sMain, $sIdent);
$sMain = substr($sMain,0,$iLastMain).'<div class="magnamain" id="content">'.substr($sMain,$iLastMain,strlen($sMain));
ob_start();
$sWarnings = '';
foreach ($aResourcesFound as $sResourceType => $blFound) {
    if (!$blFound) {
        foreach(array_unique(MLSetting::gi()->get('a'.  ucfirst($sResourceType))) as $sFile){
            $aFile = MLFilesystem::gi()->findResource('resource_'.$sResourceType.'_'.$sFile);
            $sResource = MLHelper::gi('Remote')->fileGetContents($aFile['path'], $sWarnings, 1);
            if ($sResourceType == 'css') {
                ?><style type="text/css"><?php echo $sResource; ?></style><?php
            } else {
                ?><script type="text/javascript">/*<![CDATA[*/ <?php echo $sResource; ?>/*]]>*/</script><?php
            }
        }
        MLSetting::gi()->set('a'.ucfirst($sResourceType), array(), true);
    }
}
echo ob_get_clean().$sMain;

 

?>