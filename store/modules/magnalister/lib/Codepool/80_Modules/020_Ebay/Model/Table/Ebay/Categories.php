<?php
require_once MLFilesystem::getOldLibPath('php/modules/ebay/ebayFunctions.php');

class ML_Ebay_Model_Table_Ebay_Categories extends ML_Database_Model_Table_Abstract {
    const iEbayLiveTime=86400;
    const iStoreLiveTime=600;

    protected $sTableName = 'magnalister_ebay_categories';
    
    protected $aFields = array(
         'CategoryID'     =>array(
             'isKey' => true,
             'Type' => 'bigint(11)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'SiteID'         =>array(
             'isKey' => true,
             'Type' => 'int(4)',      'Null' => 'NO', 'Default' => 77,   'Extra' => '', 'Comment'=>'' ),
         'CategoryName'   =>array(
             'Type' => 'varchar(128)','Null' => 'NO', 'Default' => '',   'Extra' => '', 'Comment'=>'' ),
         'CategoryLevel'  =>array(
             'Type' => 'int(3)',      'Null' => 'NO', 'Default' => 1,    'Extra' => '', 'Comment'=>'' ),
         'ParentID'       =>array(
             'Type' => 'bigint(11)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'LeafCategory'   =>array(
             'Type' => 'tinyint(4)',  'Null' => 'NO', 'Default' => 1,    'Extra' => '', 'Comment'=>'' ),
         'StoreCategory'  =>array(
             'isKey' => true, 
             'Type' => 'tinyint(4)',  'Null' => 'NO', 'Default' => 0,    'Extra' => '', 'Comment'=>'' ),
         'Attributes'     =>array(
             'Type' => 'int(11)',     'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>'' ),
         'Expires'        =>array(
             'isExpirable' => true,
             'Type' => 'datetime',    'Null' => 'NO', 'Default' => NULL, 'Extra' => '', 'Comment'=>'' ),
     );
    
     protected $aTableKeys=array(
         'PRIMARY'    => array('Non_unique' => '0', 'Column_name' => 'CategoryID, SiteID, StoreCategory'),
     );
     
    /**
     *
     * @var bool $blEbayBatchmode if true, no check by loading
     */
    protected static $blLoadFromApi=true;
    

    public function __construct() {
        parent::__construct();
    }

    protected function setDefaultValues() {
        $this->set('storecategory',0);
        return $this;
    }
    
    public function set($sName, $mValue) {
        if(strtolower($sName)=='storecategory'){
            if($mValue==0){
                parent::set('siteid', MLModul::gi()->getEbaySiteId());
            }else{
                parent::set('siteid', MLModul::gi()->getMarketPlaceId());
            }
        }elseif(strtolower($sName)=='siteid'){
            if($mValue== MLModul::gi()->getMarketPlaceId()){
                parent::set('storecategory',1);
            }else{
                parent::set('storecategory',0);
            }
        }
        return parent::set($sName, $mValue);
    }

    public function load(){
        if(isset($this->aData['categoryid']) && $this->aData['categoryid']==0){//cannot be 0
            $this->blLoaded=true;
            return $this;
        }else{
            $oParent=parent::load();
            if(
                !$this->blLoaded
                && isset($this->aData['categoryid'])
                && isset($this->aData['siteid'])
                && isset($this->aData['storecategory'])
                && $this->aData['storecategory']==0
                && self::$blLoadFromApi
            ){
                try {
                    $aRequest = MagnaConnector::gi()->submitRequest(array(
                        'ACTION' => 'GetCategoryWithAncestors',
                        'DATA' => array (
                            'CategoryID' => $this->get('categoryid'),
                            'Site' => MLModul::gi()->getConfig('site')
                        ),
                ));
                    if($aRequest['STATUS']=='SUCCESS'){
                        if(is_array($aRequest['DATA']) && count($aRequest['DATA'])>0){
                            foreach($aRequest['DATA'] as $aRow){
                                self::$blLoadFromApi = false; // disable api-server loading (∞ recursion)
                                /*
                                 * we only fill by parent-id
                                 * because getchildcategories use api-request only if count==0.
                                 * so if we fill only one spec. category, the siblings have earlier expired date 
                                 * and will deleted earlier.
                                 * so count is not 0 but also list not complete
                                 */
                                MLDatabase::factory('ebay_categories')
                                    ->set('parentid', (int) $aRow['ParentID'])
                                    ->set('siteid', $this->get('siteid'))
                                    ->set('storecategory', $this->get('storecategory'))
                                    ->getList()
                                    ->save()
                                ;
                                self::$blLoadFromApi = true;
                            }
                            parent::load();
                            if (!$this->blLoaded) {// add only current category if not loaded yet (for category-path)
                                self::$blLoadFromApi = false;
                                $this
                                    ->set('parentid', (int) $aRequest['DATA'][0]['ParentID'])
                                    ->set('categoryname', $aRequest['DATA'][0]['CategoryName'])
                                    ->set('categorylevel', $aRequest['DATA'][0]['CategoryLevel'])
                                    ->set('leafcategory', $aRequest['DATA'][0]['LeafCategory'])
                                    ->save()
                                ;
                                self::$blLoadFromApi = true;
                            }
                        }
                    }
                    MLLog::gi()->add('ebay_category_temp', $aRequest);
                } catch (MagnaException $oEx) {
                     throw new Exception(MLI18n::gi()->ML_ERROR_LABEL_API_CONNECTION_PROBLEM);
                }
            }
            return $oParent;
        }
    }
    public function variationsEnabled(){
        if($this->get('categoryid')==0){
            $blOut=false;
        }else{            
            try{
                $aResponse= MagnaConnector::gi()->submitRequestCached(array(
                    'ACTION' => 'VariationsEnabled',
                    'DATA' => array (
                        'CategoryID' => $this->get('categoryid'),
                        'Site' => MLModul::gi()->getConfig('site')
                    ),
                ),self::iEbayLiveTime);
                if (
                    isset($aResponse['DATA']['VariationsEnabled'])
                    && ('true' == (string)$aResponse['DATA']['VariationsEnabled'])
                ) {
                    $blOut=true;
                }else{
                    $blOut=false;
                }
            } catch (MagnaException $e) {
                echo $e->getMessage();
                $blOut=false;
            }
        }
        return $blOut;
    }
    public function getConditionValues(){
        if($this->get('categoryid')==0){
            $blOut=false;
        }else{            
            try{
                $aResponse= MagnaConnector::gi()->submitRequestCached(array(
                    'ACTION' => 'GetConditionValues',
                    'DATA' => array (
                        'CategoryID' => $this->get('categoryid'),
                        'Site' => MLModul::gi()->getConfig('site')
                    ),
                ),self::iEbayLiveTime);
                if (
                    isset($aResponse['DATA']['ConditionValues'])
                    && (is_array($aResponse['DATA']['ConditionValues']))
                ) {
                    $blOut=$aResponse['DATA']['ConditionValues'];
                }else{
                    $blOut=false;
                }
            } catch (MagnaException $e) {
                echo $e->getMessage();
                $blOut=false;
            }
        }
        return $blOut;
    }
    public function getCategoryPath($blHtml=true){
        $sSeperator=$blHtml?'&nbsp;<span class="cp_next">&gt;</span>&nbsp;':' > ';
        $sClass=get_class($this);
        $sPath='';
        $sCatId=$this->get('categoryid');
        $sStoreId=$this->get('storecategory');
        $sSideId=$this->get('siteid');
        do{  
            $oModel=new $sClass;
            $oModel
                ->set('categoryid',$sCatId)
                ->set('storecategory',$sStoreId)
                ->set('siteid',$sSideId)
            ;
            $sPath=$oModel->get('categoryname').($sPath==''?'':$sSeperator.$sPath);
//            new dBug($oModel->data());
            $sCatId=(int)$oModel->get('parentid'); 
        }while($oModel->get('parentid')!=0); 
        if($sPath === ''){
            $sPath = MLI18n::gi()->ml_ebay_prepare_form_category_notvalid ;
        }        
        return $sPath;
    }
    public function save(){
        if ($this->aData['categoryid'] == $this->aData['parentid']) {
            $this->aData['parentid'] = 0;
        }
        if($this->get('storecategory')==0){
            $iExpires=self::iEbayLiveTime;
        }else{
            $iExpires=self::iStoreLiveTime;
        }
        $this->set('expires',date('Y-m-d H:i:s', time() + $iExpires));
        return parent::save();
    }
    

    public function getAttributes(){
        if ($this->get('storecategory') == 1) {//store dont have attributes
            return array();
        }elseif($this->get('categoryid')==0){
            return array();
        }else{
            $aRequest=array(
                    "SUBSYSTEM" => "eBay",
                    'MARKETPLACEID' => MLModul::gi()->getMarketPlaceId(),
                    'DATA' => array(
                        'CategoryID' => $this->get('categoryid'),
                        'FormStructure' => true,
                        'Site' => MLModul::gi()->getConfig('site')
                    )
            );
            try {
                $aRequest['ACTION']='GetItemSpecifics';
                $aSpecifics = MagnaConnector::gi()->submitRequestCached($aRequest, self::iEbayLiveTime);
            } catch (MagnaException $e) {
                $aSpecifics['STATUS'] = 'ERROR';
                $aSpecifics['DATA'] = array();
            }
            if ($aSpecifics['STATUS'] == 'SUCCESS' && count($aSpecifics['DATA'])) {
                return $aSpecifics['DATA'];
            } else {
                try {
                    $aRequest['ACTION']='GetAttributes';
                    $aAttributes = MagnaConnector::gi()->submitRequestCached($aRequest, self::iEbayLiveTime);
                    if ($aAttributes['STATUS'] == 'SUCCESS' && count($aAttributes['DATA'])) {
                        return $aAttributes['DATA'];
                    } else {
                        return array();
                    }
                } catch (MagnaException $e) {
                    return array();
                }
            }
        }
    }
}
