<?php

// default config
$sClientVerionFile = MLFilesystem::getLibPath('ClientVersion');
if (file_exists($sClientVerionFile)) {
	$aClientVersion = json_decode(file_get_contents(MLFilesystem::getLibPath('ClientVersion')), true);
} else {
	$aClientVersion = array();
}
MLSetting::gi()->sClientVersion = isset($aClientVersion['CLIENT_VERSION']) ? $aClientVersion['CLIENT_VERSION'] : false;
MLSetting::gi()->sClientBuild = isset($aClientVersion['CLIENT_BUILD_VERSION']) ? $aClientVersion['CLIENT_BUILD_VERSION'] : false;



MLSetting::gi()->sShowToolsMenu = '';
MLSetting::gi()->aServiceVars=array(
    'sShowToolsMenu'            => array('validation' => ('/^$|^time$|^settings$|^sql$|^api$|^config$|^request$|^messages$|^session$|^tree$/'),   'ajax' => true, ),
    'blDebug'                   => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => false /*because ajax-forms (additems) are js*/, ),
    'blShowInfos'               => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ), 
    'blTemplateDebug'           => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => false /*because template dont update completely*/, ), 
    'blShowWarnings'            => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ), 
    'blShowFatal'               => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ), 
    'blUseCache'                => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ), 
    'blCronDryRun'              => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ),
    'blDryAddItems'             => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ),
    'blCleanRunOncePerSession'  => array('validation' => FILTER_VALIDATE_BOOLEAN,   'ajax' => true, ), 
    'sUpdateUrl'                => array('validation' => FILTER_VALIDATE_URL,       'ajax' => true, ), 
    'sApiUrl'                   => array('validation' => FILTER_VALIDATE_URL,       'ajax' => true, ),
    'iOrderPastInterval'        => array('validation' => FILTER_VALIDATE_INT,       'ajax' => true, )
);
MLSetting::gi()->blCronDryRun=false;
MLSetting::gi()->blDryAddItems=false;
MLSetting::gi()->blDebug=false;
MLSetting::gi()->blShowInfos=false;
MLSetting::gi()->blShowWarnings=false;
MLSetting::gi()->blShowFatal=false;
MLSetting::gi()->blTemplateDebug=false;
MLSetting::gi()->blCleanRunOncePerSession=false;

/**
 * (de)activate cache-class. 
 * except (force cache): 
 *  ajax-requests
 *  session-vars
 * @var bool MLSetting::gi()->blUseCache
 */
MLSetting::gi()->blUseCache = true;

MLSetting::gi()->sApiUrl = 'http://api.magnalister.com/API/';
MLSetting::gi()->sDefaultApiUrl = 'http://api.magnalister.com/API/';
MLSetting::gi()->sApiRelatedUrl = 'http://api.magnalister.com/APIRelated/';
MLSetting::gi()->sUpdateUrl='http://api.magnalister.com/update/v3/';
MLSetting::gi()->sPublicUrl='http://magnalister.com/';
MLSetting::gi()->blUseCurl=function_exists('curl_init');

MLSetting::gi()->sRequestPrefix='ml';//all parameters have a prefix yet

MLSetting::gi()->iDefaultCacheLifeTime=7200;// 2 hour
MLSetting::gi()->iOrderPastInterval=60 * 60 * 24 * 7;
MLSetting::gi()->iOrderMinTime=time() - 60 * 60 * 24 * 30;
MLSetting::gi()->MAGNA_SUPPORT_URL='<a href="{#setting:sPublicUrl#}" title="{#setting:sPublicUrl#}">{#setting:sPublicUrl#}</a>';
MLSetting::gi()->sMemoryLimit='512M';
switch (ini_get('safe_mode')) {
    case 'on':
    case 'yes':
    case 'true':{
        MLSetting::gi()->blSaveMode=true;
    }
    default:{
        MLSetting::gi()->blSaveMode=(bool)(int)ini_get('safe_mode');
    }
}
MLSetting::gi()->blInlineResource = false;
