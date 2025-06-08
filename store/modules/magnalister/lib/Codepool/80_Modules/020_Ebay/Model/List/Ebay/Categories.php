<?php
require_once MLFilesystem::getOldLibPath('php/modules/ebay/ebayFunctions.php');
class ML_Ebay_Model_List_Ebay_Categories extends ML_Database_Model_List{
    protected $sOrder='leafcategory asc, categoryname';
    protected function execute(){
        parent::execute();
        if(count($this->aList)==0){
            $aData=$this->oModel->data(false);
            if(//needed values to get data from server
                isset($aData['parentid'])//needed filter for request
                &&
                isset($aData['storecategory'])//need for requesttype
                &&
                isset($aData['siteid'])//without dont work
            ){
                if ((int) $aData['storecategory'] == 0) {
                    $aRequest=array(
                        'ACTION' => 'GetChildCategories',
                        "SUBSYSTEM" => "eBay",
                        'MARKETPLACEID' => MLModul::gi()->getMarketPlaceId(),
                        'DATA' => array('ParentID' => $this->oModel->get('parentid'))
                    );
                } else {
                    // delete all data
                    $sClass = get_class($this->oModel);
                    $oClean = new $sClass;
                    $oClean
                            ->set('storecategory', '1')
                            ->set('siteid', MLModul::gi()->getMarketPlaceId())
                            ->getList()
                            ->delete()
                    ;
                    $aRequest=array(
                        'ACTION' => 'GetStoreCategories',
                    );
                }
                try {
                    $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
                    if ($aResponse['STATUS'] == 'SUCCESS' && isset($aResponse['DATA']) && is_array($aResponse['DATA'])) {
                        foreach ($aResponse['DATA'] as $aRow) {
                            if (isset($aRow['mpID'])) {
                                $aRow['SiteID'] = $aRow['mpID'];
                                unset($aRow['mpID']);
                            }
                            $aRow['ParentID'] = $aRow['ParentID'] == $aRow['CategoryID'] ? 0 : $aRow['ParentID'];
                            $this->add($aRow);
                        }
                        $this->save()->reset(); //reset= order
                        parent::execute();
                    }
                    MLLog::gi()->add('ebay_category_temp', $aResponse);
                } catch (MagnaException $e) {
                    throw new Exception(MLI18n::gi()->ML_ERROR_LABEL_API_CONNECTION_PROBLEM);
                }
            }
        }
        return $this;
    }

}