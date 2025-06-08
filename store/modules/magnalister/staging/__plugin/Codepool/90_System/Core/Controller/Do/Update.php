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

MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

/**
 * Updates and installs the magnalister-plugin.
 */
class ML_Core_Controller_Do_Update extends ML_Core_Controller_Abstract {
    
    protected $blDebug = false;// default = false
    
    protected $aParameters = array('do');
    
    /**
     * @var array $aIgnorePattern regex-pattern for files that not will touched
     */
    protected $aIgnorePatterns=array(
        '^writable\/.*',
        '^files.list$',
        '^specialfiles.list$',
        '^permissions.list$',
        '^external.list$',
        '^Update$',
        '\.svn',
    );
    
    protected $aCopyLocal2Staging = array(
        'Codepool/00_Dev/',
        'Codepool/10_Customer/',
    );
    
    /**
     * @var int $iItemsPerRequest max remote items per request
     */
    protected $iItemsPerRequest = 20;// default = 20;
    
    /**
     * @var int $iMinItems minimal count of items which are in plugin process
     */
    protected $iMinItems = 200;
    
    /**
     * @var string $sCacheName cach name
     */
    protected $sCacheName = '';
    
    /**
     * sequenzes with actions (staging, plugin)
     * @var array $aSequences
     */
    protected $aSequences = array();
    
    /**
     * Meta-Data from Sequenzes
     * @var array
     */
    protected $aMeta = array(
        'remoteActions' => array()
    );
    
    /**
     * costructor, sets default values, preloads classes, to be shure, that them from actual installation
     */
    public function __construct() {
        // be shure, classes are loaded before copie
        MLSession::gi();
        MLSetting::gi();
        MLMessage::gi();
        MLController::gi('widget_message');
        MLController::gi('widget_progressbar');
        MLHelper::gi('remote');
        MLCache::gi();
        MLShop::gi();
        MLException::factory('update');
        try {
            MLDatabase::factory('config');
        } catch (Exception $oEx) {
            // database is not part of installer
        }
        $this->sCacheName = __CLASS__.'__updateinfo.json';
        parent::__construct();
    }
    
    /**
     * returns misc. paths
     * @param string $sType
     * @return string path/to/folder
     */
    protected function getPath ($sType) {
        switch ($sType) {
            case 'server': {
                return MLSetting::gi()->get('sUpdateUrl') . 'magnalister/';
            }
            case 'plugin': {
                return MLFilesystem::getLibPath();
            }
            case 'staging': {
                return dirname(MLFilesystem::getLibPath()).'/staging/';//dirname be shure not relative to libfolder, it could be renamed
            }
            case 'clientversion' : {
                $sCurrentVersion = MLSetting::gi()->get('sClientVersion');
                return MLSetting::gi()->get('sUpdateUrl') . 'ClientVersion/'.(empty($sCurrentVersion) ? 'install' : $sCurrentVersion);
            }
        }
    }
    
    /**
     * counts different types for statistic
     * @param string $sType
     * @return int
     */
    protected function getCount($sType) {
        switch ($sType) {
            case 'final' : {
                $sCount = 'plugin';
                $aCount = array('', 'mkdir', 'cp');
                break;
            }
            case 'total_plugin' : {
                $sCount = 'plugin';
                $aCount = array('', 'mkdir', 'cp', 'rm', 'rmdir');
                break;
            }
            case 'action_plugin' : {
                $sCount = 'plugin';
                $aCount = array('mkdir', 'cp', 'rm', 'rmdir');
                break;
            }
            case 'total_staging' : {
                $sCount = 'staging';
                $aCount = array('', 'mkdir', 'cp', 'rm', 'rmdir');
                break;
            }
            case 'action_staging' : {
                $sCount = 'staging';
                $aCount = array('mkdir', 'cp', 'rm', 'rmdir');
                break;
            }
            case 'remote_plugin' : {
                return isset($this->aMeta['remoteActions']['plugin']) ? count($this->aMeta['remoteActions']['plugin']) : 0;
            }
            case 'remote_staging' : {
                return isset($this->aMeta['remoteActions']['staging']) ? count($this->aMeta['remoteActions']['staging']) : 0;
            }
            default : {
                return 0;
            }
        }
        $iCount = 0;
        foreach (array_keys($this->aSequences[$sCount]) as $sAction) {
            if (in_array($sAction, $aCount)) {
                $iCount += count($this->aSequences[$sCount][$sAction]);
            }
        }
        return $iCount;
    }
    
    /**
     * checks if staging folder, creates folder if not exists, checks disk space of needed base-folders
     * @return \ML_Core_Controller_Do_Update
     * @throws \ML_Core_Exception_Update
     */
    protected function checkStagingFolder () {
        // creating update-folder
        $this->mkdir(array('dst' => $this->getPath('staging')));
        $sListOfFunction = @ini_get('disable_functions');
        if (strpos($sListOfFunction, 'disk_free_space') === false) {
            foreach (array('plugin', 'staging') as $sPath) {
                $iFreeSpace = disk_free_space($this->getPath($sPath));
                if ($iFreeSpace === false) {
                    MLMessage::gi()->addDebug('disk_free_space function returns false.');
                } elseif ($iFreeSpace < 30 * 1024 * 1024) {
                    throw MLException::factory(
                        'update', 
                        'Insufficient disk space ({#currentDiskSpace#} needed {#neededDiskSpace#}).', 
                        1407751100
                    )->setData(array('currentDiskSpace' => disk_free_space($this->getPath($sPath)), 'neededDiskSpace' => 30 * 1024 * 1024));
                }
            }
        } else {
            MLMessage::gi()->addDebug('disk_free_space function cannot be used.');
        }
        return $this;
    }
    
    /**
     * add plugin-data to $aFolderData if folder-data is external
     * the fileslist dont know external data, but with info of server or update data we can fill it now
     * @param array $aFolderData
     * @return \ML_Core_Controller_Do_Update
     */
    protected function addPluginDataDynamicly (&$aFolderData, $sIdentPath) {
        if (strpos($sIdentPath, '__/') === 0) { //external
            if (isset($aFolderData['server']) || isset($aFolderData['staging'])) {
                $sFrom = isset($aFolderData['server']) ? 'server' : 'staging';
                $sPluginFile = MLFilesystem::getLibPath($aFolderData[$sFrom]['dst']);
                if (file_exists($sPluginFile)) {
                    $aFolderData['plugin'] = array(
                        'src' => $aFolderData[$sFrom]['src'],
                        'dst' => $aFolderData[$sFrom]['dst'],
                        'hash' => empty($aFolderData[$sFrom]['hash']) ? $aFolderData[$sFrom]['hash'] : md5_file($sPluginFile),
                    );
                }
            }
        }
        return $this;
    }
    
    /**
     * calculates sequenz for path to put as a staging-action
     * @param array $aFolderData
     * @param string $sIdentPath
     * @return array(action => string, data => array(for, action))
     */
    protected function calcStagingSequence ($aFolderData, $sIdentPath) {
        $sStaging = $this->getPath('staging');
        $sType = (strpos($sIdentPath, '__/') === 0 ? '__external' : '__plugin');
        if (!isset($aFolderData['server']) && isset($aFolderData['staging'])) {
            //dont exists in server => delete
            $sAction = empty($aFolderData['staging']['hash']) ? 'rmdir' : 'rm';
            $aStaging = array('dst' => $sStaging.'/'.$sType.'/'.$aFolderData['staging']['src']);
        } elseif (
            isset($aFolderData['server']) 
            && (!isset($aFolderData['staging']) || $aFolderData['server']['hash'] != $aFolderData['staging']['hash'])
        ) {//create
            if (empty($aFolderData['server']['hash'])) {// mkdir
                $sAction = 'mkdir';
                $aStaging = array('dst' => $sStaging.'/'.$sType.'/'.$sIdentPath);
            } else {// cp
                $sAction ='cp';
                if (isset($aFolderData['plugin']) && $aFolderData['plugin']['hash'] == $aFolderData['server']['hash']) {
                    $sSrc = $this->getPath('plugin').$aFolderData['plugin']['dst'];//copy from plugin
                } else {
                    //copy from server
                    $sSrc = 
                        $this->getPath('server').(
                            $sType == '__plugin' 
                            ? '' 
                            : '../shopspecific/'.MLShop::gi()->getShopSystemName().'/'
                        ).$aFolderData['server']['src']
                    ;
                }
                $aStaging = array(
                    'src' => $sSrc,
                    'dst' => $sStaging.'/'.$sType.'/'.$sIdentPath,
                );
            }
        } else {
            $sAction = '';
            $aStaging = array();
        }
        return array('data' => $aStaging, 'action' => $sAction);
    }
    
    /**
     * calculates sequenz for path to put as a plugin
     * @param array $aFolderData
     * @param string $sIdentPath
     * @return array(action => string, data => array(for, action))
     */
    protected function calcPluginSequence($aFolderData, $sIdentPath){
        $sPlugin = $this->getPath('plugin');
        if (!isset($aFolderData['server']) && isset($aFolderData['plugin'])) {//delete - can only come from staging
            $sAction = empty($aFolderData['plugin']['hash']) ? 'rmdir' : 'rm';
            $aPlugin = array('dst' => $sPlugin.str_replace('__.', '../',$aFolderData['plugin']['dst']));
        } elseif (
            isset($aFolderData['server']) 
            && (
                !isset($aFolderData['plugin']) || $aFolderData['server']['hash'] != $aFolderData['plugin']['hash']
            )
        ) {//create
            if (empty($aFolderData['server']['hash'])) {// mkdir
                $sAction = 'mkdir';
                $aPlugin = array('dst' => $sPlugin.str_replace('__.', '../',$aFolderData['server']['dst']));
            } else {// cp
                $sAction ='cp';
                $aPlugin = array(
                    'src' => $this->getPath('staging').'/'.(strpos($sIdentPath, '__/') === 0 ? '__external' : '__plugin').'/'.$sIdentPath,
                    'dst' => $sPlugin.str_replace('__.', '../',$aFolderData['server']['dst']),
                );
            }
        } else {
            $sAction ='';
            $aPlugin = array();
        }     
        return array('data' => $aPlugin, 'action' => $sAction);   
    }

    /**
     * calculates all neccessary actions and meta datafor update process
     * 
     * @param string $sServer path to server
     * @param string $sPlugin path to plugin
     * @param string $sStaging path to staging area
     * @return $this
     */
    protected function prepareSequenzes () {
        if (!MLCache::gi()->exists($this->sCacheName)) {
            $aMerged = array();
            foreach(array('server', 'plugin', 'staging') as $sFolder ) {
                foreach (MLHelper::gi('remote')->getFileList($this->getPath($sFolder)) as $aPluginOrExtenal) {
                    foreach ($aPluginOrExtenal as $sIdentPath => $aData) {
                        if ($sIdentPath == '__/') { // path dont comes from server.lst, but path is part of plugin
                            continue;
                        }
                        $aMerged[$sIdentPath][$sFolder] = $aData;
                    }
                }
            }
            foreach ($this->aCopyLocal2Staging as $sCopyLocal2Staging) {
                $aPluginOrExtenal = MLHelper::gi('remote')->getFileList(MLFilesystem::getLibPath($sCopyLocal2Staging));
                foreach ($aPluginOrExtenal['__plugin'] as $sFolderFileIdent => $aFolderFilesData) {
                    $aMerged[$sCopyLocal2Staging.$sFolderFileIdent]['server'] = array(
                        'src' => $sCopyLocal2Staging.$aFolderFilesData['src'],
                        'dst' => $sCopyLocal2Staging.$aFolderFilesData['dst'],
                        'hash' => $aFolderFilesData['hash'],
                    );
                }
                $aMerged[$sCopyLocal2Staging]['server'] = array(
                    'src' => $sCopyLocal2Staging,
                    'dst' => $sCopyLocal2Staging,
                    'hash' => 0,
                );
            }
            
//            echo json_encode($aMerged);die;
            /* @var $aSequences output array */
            $aSequences = array();
            
            //prefill actions
            foreach (array(
                'staging', // (server||plugin) => staging
                'plugin', // staging => plugin
            ) as $sDstType) {
                $aSequences[$sDstType] = array(
                    '' => array(), // do nothing just statistic
                    'mkdir' => array(), // create folders before copy
                    'cp' => array(),
                    'rm' => array(),
                    'rmdir' => array(), // delete folders afer delete files
                );
            }
            foreach ($aMerged as  $sIdentPath => &$aFolderData) {
                
                // check ignore patterns
                foreach($this->aIgnorePatterns as $sPattern){
                    if(preg_match('/'.$sPattern.'/Uis', $sIdentPath)){
                        continue 2;
                    }
                }
                
                // fill plugin data dynamicly, plugin dont know external files before
                $this->addPluginDataDynamicly($aFolderData, $sIdentPath);
                
                // (plugin || server) => staging
                $aStaging = $this->calcStagingSequence($aFolderData, $sIdentPath);
                if ($this->blDebug) {
                    $aStaging['data']['data'] = $aFolderData;
                }
                $aSequences['staging'][$aStaging['action']][$sIdentPath] = $aStaging['data'];
                
                // now staging is equal to server
                if (isset($aFolderData['server'])) {
                    $aFolderData['staging'] = $aFolderData['server'];
                }
                
                // staging => plugin
                $aPlugin = $this->calcPluginSequence($aFolderData, $sIdentPath);
                if ($this->blDebug) {
                    $aPlugin['data']['data'] = $aFolderData;
                }
                $aSequences['plugin'][$aPlugin['action']][$sIdentPath] = $aPlugin['data'];
            }
            //sorting
            foreach ($aSequences as $sSequence => $aSequence) {
                if (isset($aSequence['mkdir'])) { // create dirs ascending
                    ksort($aSequences[$sSequence]['mkdir']);
                }
                if (isset($aSequence['rmdir'])) { // delete dirs descending
                    krsort($aSequences[$sSequence]['rmdir']);
                }
            }
            foreach ($aSequences as $sSequence => $aSequence) {
                foreach ($aSequence as $sSequenceType => $aSequenceData) { // remote actions
                    if (!empty($sSequenceType)) {
                        foreach ($aSequenceData as $sActionIdent => $aActionData) {
                            if (strpos($sActionIdent, '__/') === 0) {
                                $this->aMeta['remoteActions'][$sSequence][$sActionIdent] = array($sSequenceType =>$aActionData);
                            }
                        }
                    }
                }
            }
            $this->aSequences = $aSequences;
        } elseif (empty($this->aSequences)) {// load cached
            $aCached = MLCache::gi()->get($this->sCacheName);
            $this->aSequences = $aCached['sequences'];
            $this->aMeta = $aCached['meta'];
        } 
        if (empty($this->aSequences)) {
            MLCache::gi()->delete($this->sCacheName);
        } else {
//            echo json_encode($this->aSequences);die;
            MLCache::gi()->set($this->sCacheName, array('sequences' => $this->aSequences, 'meta' => $this->aMeta), 10 * 60);
        }
        return $this;
    }    
    
    /**
     * iterates a sequence
     * @param string $sSequence
     * @return \ML_Core_Controller_Do_Update
     */
    protected function runSequence ($sSequence) {
        if ($sSequence == 'plugin') {
            $this->rmDir(array('dst' => $this->getPath('staging').'__swap'));
            $this->rename(array(
                'src' => $this->getPath('plugin'), 
                'dst' => $this->getPath('staging').'__swap'
            ));
            try {
                $this->rename(array(
                    'src' => $this->getPath('staging').'__plugin', 
                    'dst' => $this->getPath('plugin')
                ));
            } catch (Exception $oEx) {// rollback
                $this->rename(array(
                    'src' => $this->getPath('staging').'__swap',
                    'dst' => $this->getPath('plugin')
                ));
                throw $oEx;
            }
            $this->rename(array(
                'src' => $this->getPath('staging').'__swap',
                'dst' => $this->getPath('staging').'__plugin'
            ));
        }
        $aSequences = $this->aSequences[$sSequence];
        $iActionCountTotal = $this->getCount('total_'.$sSequence);
        MLController::gi('widget_progressbar')->setTotal($iActionCountTotal);
        $iRemoteActionCount = 0;
        $iActionCount = 0;
        foreach ($aSequences as $sAction => $aAction) {
            if (empty($sAction)) {
                $iActionCount += count($aAction);
            } else {
                foreach ($aAction as $sIdent => $aActionData) {
                    if ($sSequence != 'plugin' || strpos($sIdent, '__/') === 0) {//plugin files are moved before
                        if (strpos($sIdent, '__/') === 0 && $sAction == 'rmdir') {//only remove empty dir for shopspecific files
                            $this->rmDir($aActionData, true);
                        } else {
                            $this->{$sAction}($aActionData);
                        }
                    }
                    ++$iActionCount;
                    //move action to done
                    $this->aSequences[$sSequence][''][$sIdent] = array();
                    unset($this->aSequences[$sSequence][$sAction][$sIdent]);
                    MLSetting::gi()->add('aAjax', array(
                        'Next' => MLHttp::gi()->getUrl(array('do' => 'update', 'method' => 'update')),
                        'Done' => $iActionCount,
                        'Total' => $iActionCountTotal
                    ));
                    if (MLSetting::gi()->get('blDebug') && $sSequence == 'staging'
                            //&& isset($aActionData['src']) && preg_match('/http(s{0,1}):\/\//', $aActionData['src'])
                    ) {
                        MLController::gi('widget_progressbar')
                            ->addLog((isset($aActionData['src']) && preg_match('/http(s{0,1}):\/\//', $aActionData['src']) ? 'wget ' : $sAction).' ./'.$sIdent)
                            ->setBarInfo($iActionCount . ' / ' . $iActionCountTotal)
                        ;
                    }
                    MLController::gi('widget_progressbar')->setDone($iActionCount);
                    if (($sAction == 'cp') && preg_match('/http(s{0,1}):\/\//', $aActionData['src'])) {
                        ++$iRemoteActionCount;
                        if ($iRemoteActionCount > $this->iItemsPerRequest) {
                            $this->prepareSequenzes();//save
                            return $this;//next
                        }
                    }
                }
            }
        }
        MLSetting::gi()->add('aAjax', array(
            'Next' => MLHttp::gi()->getUrl(array('do' => 'update', 'method' => 'update')),
            'Done' => $iActionCount,
            'Total' => $iActionCountTotal
        ));
        $this->prepareSequenzes();//save
        return $this;
    }
    
    /**
     * check file permissions for all actions
     * 
     * @throws \ML_Core_Exception_Update
     * @return \ML_Core_Controller_Do_Update
     */
    protected function checkPermissions () {
        foreach ($this->aSequences as $sType => $aActions) {
            foreach ($aActions as $sAction => $aAction) {
                if (!empty($sAction)) {
                    foreach ($aAction as $sIdent => $aFile) {
                        if (!$this->isWriteable($aFile)) {
                            throw MLException::factory(
                                'update', 
                                'File `{#path}` is not writable.', 
                                1407759765
                            )->setData(array('path' => MLHelper::getFilesystemInstance()->getFullPath($aFile['dst'])));
                        }
                    }
                }
            }
        }
        return $this;
    }
    
    /**
     * redirecting for developers
     * @return \ML_Core_Controller_Do_Update
     */
    public function callAjaxSuccess () {
        MLSetting::gi()->add('aAjax', array('success' => true));
        return $this;
    }
    
    /**
     * method will be triggered via reload after file-update
     * here can some imigration be done (eg. db-data-changes)
     * @return \ML_Core_Controller_Do_Update
     */
    public function callAjaxAfterUpdate () {
        $iStartTime = microtime(true);
        $aUpdateClasses = MLFilesystem::gi()->getClassCollection('/^update_.*$/', false);
        $iTotal = count($aUpdateClasses);
        $aParams = MLRequest::gi()->data();
        unset($aParams['do'], $aParams['method'], $aParams['ajax'], $aParams['unique']);
        MLController::gi('widget_progressbar')
            ->setId('updatePlugin')
            ->setContent(MLI18n::gi()->get('sModal_afterUpdatePlugin_content'))
            ->setTotal($iTotal)
            ->addLog('Parameters: <span style="color:silver;">'.  json_encode($aParams).'</span>')
        ;
        $iDone = MLRequest::gi()->data('done');
        $iDone = is_numeric($iDone) ? $iDone : 0;
        for($iCount=0; $iCount < $iDone; $iCount++) {
            array_shift($aUpdateClasses);
        }
        $aUpdateClassParameters = array();
        while (count($aUpdateClasses) && $iStartTime+10 >= microtime(true)) {
            $iUpdateClassTime = microtime(true);
            $sIdent = key($aUpdateClasses);
            $aUpdateClass = current(current($aUpdateClasses));                
            $sLog = 'Ident <span style="color:#32CD32;" title="'.'./'.substr($aUpdateClass['path'], strlen(MLFilesystem::getLibPath())).'">'.$sIdent.'</span> ';
            try {
                require_once $aUpdateClass['path'];
                $oReflection = new ReflectionClass($aUpdateClass['class']);
                if (
                    !$oReflection->isSubclassOf('ML_Core_Update_Abstract')
                    || $oReflection->isAbstract() 
                    || $oReflection->isInterface()
                ) {
                    $sLog .= 'skipped';
                } else {
                    $oUpdateClass = new $aUpdateClass['class'];

                    if ($oUpdateClass->needExecution()) {
                        $oUpdateClass->execute();
                        $sLog .= 'executed in <span style="color:#6495ED;">'.  microtime2human(microtime(true)-$iUpdateClassTime).'</span>.';
                        if (is_array($oUpdateClass->getParameters())) {
                            $aUpdateClassParameters = $oUpdateClass->getParameters();
                            MLController::gi('widget_progressbar')->addLog($sLog);
                            break;
                        }
                    } else {
                        $sLog .= 'skipped.';

                    }
                }
                MLController::gi('widget_progressbar')->addLog($sLog);
                array_shift($aUpdateClasses);
                ++$iDone;
            } catch (Exception $oEx) {
                MLController::gi('widget_progressbar')
                    ->addLog($sLog.'threw Exception with the message "<span style="color: #FF0000">'.$oEx->getMessage().'</span>" after <span style="color:#6495ED;">'. microtime2human(microtime(true)-$iUpdateClassTime).'</span>.')
                    ->addLog('
                        <span>
                            <a class="global-ajax" data-ml-global-ajax=\'{"triggerAfterSuccess":"currentUrl"}\' onclick="jqml(this).siblings().remove();" style="color:gray;font-size:inherit;" href="'.$this->getCurrentUrl(array('method'=>'afterUpdate', 'done'=>$iDone)).'">Again</a>
                            <span> or </span>
                            <a class="global-ajax" data-ml-global-ajax=\'{"triggerAfterSuccess":"currentUrl"}\' onclick="jqml(this).siblings().remove();" style="color:gray;font-size:inherit;" href="'.$this->getCurrentUrl(array('method'=>'afterUpdate', 'done'=>$iDone+1)).'">Skip current class.</a>
                        </span>
                    ')
                    ->setContent(MLI18n::gi()->get('sUpdateError_doAgain', array(
                        'link' => $this->getCurrentUrl(array('method'=>'afterUpdate')
                    ))))
                ;
                if ($oEx instanceof ML_Core_Exception_Update) {
                    MLMessage::gi()->addError($oEx->getTranslation());
                } else {
                    MLMessage::gi()->addDebug($oEx);
                }
                
                break;
            }
        }
        $aAjax = array(
            'Done' => $iDone,
            'Total' => $iTotal
        );
        if ($iDone >= $iTotal) {
            MLHelper::getFilesystemInstance()->write(
                $this->getPath('plugin') . 'ClientVersion', MLHelper::gi('remote')->fileGetContents($this->getPath('clientversion'))
            );
            MLHelper::getFilesystemInstance()->write(
                MLFilesystem::getLibPath('Update'), 
                date('Y-m-d H:i:s').' '. MLHelper::gi('remote')->fileGetContents($this->getPath('plugin') . 'ClientVersion')
            );
            MLMessage::gi()->addSuccess(
                MLI18n::gi()->get('ML_TEXT_UPDATE_SUCCESS', array('url'=>  MLHttp::gi()->getUrl(array('content'=>'changelog'))))
            );
            if (MLSetting::gi()->get('blDebug')) { // adding automatic redirect
                MLController::gi('widget_progressbar')->addLog('<a class="global-ajax" data-ml-global-ajax=\'{"triggerAfterSuccess":"currentUrl"}\' style="color:gray;" href="'.$this->getUrl(array('do' => 'update', 'method' => 'success')).'">Finish.</a>');
            } else {
                MLController::gi('widget_progressbar')->addLog('Finish.');
            }
            $aAjax['success'] = true;
            MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'after-update')->set('value', true)->save();
        } elseif(!isset($oEx)) {// Exception handles with message-text
            $aAjax['Next'] = MLHttp::gi()->getUrl(array_merge(
                    array('do' => 'update', 'method' => 'afterUpdate', 'done' => $iDone),
                    $aUpdateClassParameters
            ));
        }
        MLSetting::gi()->add('aAjax', $aAjax);
        MLController::gi('widget_progressbar')
            ->setDone($iDone)
            ->render()
        ;
        return $this;
    }
    
    /**
     * starts with update, only method for extern classes
     * @return \ML_Core_Controller_Do_Update
     */
    public function callAjaxUpdate () {
        MLController::gi('widget_progressbar')->setId('updatePlugin');
        if (ML::isUpdate()) {
            MLController::gi('widget_progressbar')
                ->setContent(MLI18n::gi()->get('sModal_updatePlugin_content'))
            ;
        } else {
            MLController::gi('widget_progressbar')
                ->setContent(MLI18n::gi()->get('sModal_installPlugin_content'))
            ;
        }
        try {
            if (!MLCache::gi()->exists($this->sCacheName)) {// cache dont exists => generate and reload
                MLController::gi('widget_progressbar')
                    ->addLog('# Plugin-Path: '.$this->getPath('plugin')) 
                    ->addLog('# Staging-Path: '.$this->getPath('staging'))
                    ->addLog('# Remote-Path: '.$this->getPath('server'))
                ;
                $this->checkStagingFolder();
                $aSequences = $this->prepareSequenzes()->aSequences;
                MLSetting::gi()->add('aAjax', array(
                    'Next' => MLHttp::gi()->getUrl(array('do' => 'update', 'method' => 'update')),
                    'Done' => 0,
                    'Total' => $this->getCount('plugin')
                ));
                MLController::gi('widget_progressbar')->render();
                if (MLSetting::gi()->get('blDebug')) {// remove autom. redirect, show sequenzes
                    MLMessage::gi()->addDebug('Update-Sequences', $aSequences);
                    MLController::gi('widget_progressbar')
                        ->addLog('# Sequences are calculated, see debug-bar for more info. <a style="color:gray;" onclick="jqml(this).html(\'\');return true;" class="global-ajax" href="'.$this->getCurrentUrl(array('method'=>'update')).'"><br />Click here for next step.</a>')
                    ;
                    throw new Exception('filelist loaded');
                } else {
                    return $this;
                }
            }
            $aSequences = $this->prepareSequenzes()->aSequences;
            #new dBug($aSequences, '', true);
            
            $this
                ->checkFilesCount($aSequences)
                ->checkPermissions()
            ;
            
            if ($this->getCount('action_staging') !=0 ) {
                $this->runSequence('staging');
                MLController::gi('widget_progressbar')->render();
                return $this;
            }
            // finalize
            if ($this->getCount('action_plugin')) {
                $this->runSequence('plugin');
            }
            MLCache::gi()->flush();
            MLController::gi('widget_progressbar')->addLog('Check Updated Files.');
            foreach ($this->prepareSequenzes()->aSequences['plugin'] as $sCheckSequenceType => $aCheckSequenceType) {
                if ($sCheckSequenceType != '' && count($aCheckSequenceType) != 0) {
                    MLController::gi('widget_progressbar')->addLog('<span style="color:red;">'.$sCheckSequenceType.': '.count($aCheckSequenceType).' actions</span>');
                    MLMessage::gi()->addDebug($sCheckSequenceType.' actions', $aCheckSequenceType);
                    MLController::gi('widget_progressbar')->setContent(MLI18n::gi()->get('sUpdateError_doAgain', array(
                        'link' => $this->getCurrentUrl(array('method'=>'update')
                    ))));
                    throw new Exception('update again');
                }
            }
            MLSession::gi()->delete('runOncePerSession');
            if ($this->blDebug) {
                MLSetting::gi()->add('aAjax', array(
                    'debug-updater' => array(
                        'paths' => array(
                            'server' => $this->getPath('server'),
                            'plugin' => $this->getPath('plugin'),
                            'staging' => $this->getPath('staging'),
                        ),
                        'meta' => $this->aMeta, 
                        'remoteActions' => $this->getCount('remote_plugin')
                    )
                ));
            }
            MLShop::gi()->triggerAfterUpdate($this->getCount('remote_plugin') > 0);
            if (ML::isUpdate()) {
//                MLMessage::gi()->addSuccess(
//                    MLI18n::gi()->get('ML_TEXT_UPDATE_SUCCESS', array('url'=>  MLHttp::gi()->getUrl(array('content'=>'changelog')))),
//                    array('md5' => 'newVersion')
//                );
            } else {
                MLMessage::gi()->addSuccess(
                    MLI18n::gi()->get('ML_TEXT_INSTALL_SUCCESS'),
                    array('md5' => 'newVersion')
                );
            }
            $aAjax = MLSetting::gi()->get('aAjax');
            try {
                MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'after-update')->delete();
            } catch (Exception $oEx) {
                // database is not part of installer
            }
            $aAjax['Next'] = $this->getCurrentUrl(array('method'=>'afterUpdate'));//next-ajax is afterupdate
            MLSetting::gi()->set('aAjax', $aAjax, true);
        } catch (Exception $oEx) {
            if ($oEx instanceof ML_Core_Exception_Update) {
                MLMessage::gi()->addError($oEx->getTranslation());
            }
            MLMessage::gi()->addDebug($oEx);
            MLController::gi('widget_progressbar')->setContent(MLI18n::gi()->get('sUpdateError_doAgain', array(
                'link' => $this->getCurrentUrl(array('method'=>'update')
            ))));
            $aAjax = MLSetting::gi()->get('aAjax'); 
            unset($aAjax['Redirect']);
            unset($aAjax['Next']);
            MLSetting::gi()->set('aAjax', $aAjax, true);
        }
        MLController::gi('widget_progressbar')->render();
        return $this;
    }


    
    /**
     * Checks if the count of files incl. folders after update is over $this->iMinItems.
     * @throws \ML_Core_Exception_Update
     * @return \ML_Core_Controller_Do_Update
     */
    protected function checkFilesCount() {
        $iCount = $this->getCount('final');
        if ($iCount < $this->iMinItems) {
            if (MLSetting::gi()->get('blDebug')) {
                MLI18n::gi()->set('sModal_updatePlugin_barInfo', '0 / '. $iCount, true);
                MLController::gi('widget_progressbar')->addLog('Error. Not enough files for update.');
            }
            $iActionCountTotal = $this->getCount('action_plugin');
            MLSetting::gi()->add('aAjax', array('Done' => 0), true);
            MLSetting::gi()->add('aAjax', array('Total' => $iActionCountTotal), true);
            throw MLException::factory(
                'update', 
                'Insufficient filecount ({#currentFileCount#} needed {#minFileCount#}).', 
                1407753851
            )->setData(array('currentFileCount' => $iActionCountTotal, 'minFileCount' => $this->iMinItems));
        } else {
            return $this;
        }
    }
    

    protected function rename($aFile) {
        MLHelper::getFilesystemInstance()->mv($aFile['src'], $aFile['dst']);
        return $this;
    }


    /**
     * delete directory in filesystem
     * checks if there are files existing because with external can be
     * @param array $aFile
     * @throws \ML_Core_Exception_Update
     * @return \ML_Core_Controller_Do_Update
     */
    protected function rmDir ($aFile, $blOnlyEmptyFolders = false) {
        if (!is_array($aFile) || !isset($aFile['dst'])) {
            throw MLException::factory(
                'update',
                'Wrong Paramater for {#method#}.',
                1407833718
            )->setData(array('method' => __METHOD__));
        }
        if ($blOnlyEmptyFolders && count(MLFilesystem::glob($aFile['dst'].'/*', 0)) > 0) {
            return $this;
        }
        MLHelper::getFilesystemInstance()->rm($aFile['dst']);
        return $this;
    }

    /**
     * copy a file in filesystem
     * @throws \ML_Core_Exception_Update
     * @return \ML_Core_Controller_Do_Update
     */
    protected function cp ($aFile) {
        if (!is_array($aFile) || !isset($aFile['dst']) || !isset($aFile['src'])) {
            throw MLException::factory(
                'update',
                'Wrong Paramater for {#method#}.',
                1407833718
            )->setData(array('method' => __METHOD__));
        }
        $sSrc = $aFile['src'];
        $sDst = $aFile['dst'];
        if (!file_exists(dirname($sDst))) {
            $this->mkdir(array('dst' => dirname($sDst)));
        }
        if (strpos($sSrc, 'http') === 0) {
            MLHelper::getFilesystemInstance()->write($sDst, MLHelper::gi('remote')->fileGetContents($sSrc));
        } else {
            MLHelper::getFilesystemInstance()->cp($sSrc, $sDst);
        }
        return $this;
    }
    
    /**
     * delete a file in filesystem
     * @param string $sFile
     * @throws \ML_Core_Exception_Update
     * @return \ML_Core_Controller_Do_Update
     */
    protected function rm($aFile) {
        if (!is_array($aFile) || !isset($aFile['dst'])) {
            throw MLException::factory(
                'update',
                'Wrong Paramater for {#method#}.',
                1407833718
            )->setData(array('method' => __METHOD__));
        }
        MLHelper::getFilesystemInstance()->rm($aFile['dst']);
        return $this;
    }
    
    /**
     * creates a directory in filesystem
     * @return \ML_Core_Controller_Do_Update
     * @throws \ML_Core_Exception_Update
     */
    protected function mkdir($aFile) {
        if (!is_array($aFile) || !isset($aFile['dst'])) {
            throw MLException::factory(
                'update',
                'Wrong Paramater for {#method#}.',
                1407833718
            )->setData(array('method' => __METHOD__));
        }
        MLHelper::getFilesystemInstance()->write($aFile['dst']);
        return $this;
    }
    
    /**
     * checks if a path is writeable
     * @param string $sPath
     * @return boolean
     */
    protected function isWriteable($aFile) {
        if (!is_array($aFile) || !isset($aFile['dst'])) {
            throw MLException::factory(
                'update',
                'Wrong Paramater for {#method#}.',
                1407833718
            )->setData(array('method' => __METHOD__));
        }
        return MLHelper::getFilesystemInstance()->isWritable($aFile['dst']);
    }

}