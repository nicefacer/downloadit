<?php
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Abstract');
abstract class ML_Productlist_Controller_Widget_ProductList_Selection extends ML_Productlist_Controller_Widget_ProductList_Abstract {
    public function __construct() {
        parent::__construct();
        $aFilter = MLRequest::gi()->data('filter');
        if (isset($aFilter['meta']['selection'])) {
            $aSelection = explode('_', $aFilter['meta']['selection']);
            if (count($aSelection) == 2) {
                if ($aSelection[1] == 'page') {
                    $aIds = $this->getProductList()->getMasterIds(true); 
                    if (MLHttp::gi()->isAjax()) {
                        MLSetting::gi()->add('aAjax', array('Redirect' => $this->getCurrentUrl())); 
                    }
                } elseif ($aSelection[1] == 'filter') {
                    if (MLHttp::gi()->isAjax()) {
                        $aStatistic = $this->getProductList()->getStatistic();
                        $iFrom = 0;
                        $iCount = 100; // if its to high, we have fast-cgi problems, perhaps make some output (spaces) after while and flush() them directly
                         if (MLRequest::gi()->data('selectionlimit') !== null) {
                             list($iFrom,$iCount) = explode('_', MLRequest::gi()->data('selectionlimit'));
                         }
                         if( $aStatistic['iCountTotal'] > $iCount && $aStatistic['iCountTotal']  > $iFrom ){
                                MLSetting::gi()->add('aAjax', array('Next' => $this->getCurrentUrl(array(                         
                                   'filter' => $aFilter ,
                                   'selectionlimit' => ($iFrom + $iCount)."_". $iCount , 
                               ))));
                         }else{                                                                
                            MLSetting::gi()->add('aAjax', array('Redirect' => $this->getCurrentUrl())); 
                         }
                        $aIds = $this->getProductList()->setLimit($iFrom,$iCount)->getMasterIds(true);
                    }else{
                        $aIds = $this->getProductList()->getMasterIds();
                    }
                } else {
                    $aIds = null;                    
                    if (MLHttp::gi()->isAjax()) {
                        MLSetting::gi()->add('aAjax', array('Redirect' => $this->getCurrentUrl())); 
                    }
                }
                if ($aSelection[0] == 'sub') {//delete, we dont need to check article for errors   
                    $this->deleteProductsFromSelection($aIds);   
                }elseif ($aIds !== null) {// have ids but no (delete)query => add items
                    $this->addProductsToSelection($aIds);
                }
            }
        }
    }
    
    /**
     * get count of selected master-articles
     * @return int
     */
    protected function getSelectedCount () {
        return MLDatabase::getDbInstance()->fetchOne("
            SELECT count(distinct v.parentid)
            FROM magnalister_selection s
            INNER JOIN magnalister_products v on s.pid = v.id
            WHERE 
                s.mpid='" . MLRequest::gi()->get('mp') . "'
                AND s.selectionname='" . $this->getProductList()->getSelectionName() . "'
                AND s.session_id='" . MLShop::gi()->getSessionId() . "'
        ");
    }
    
    public function countSelectedVariants($mProduct) {
        $iMasterProductId = (int)(
            $mProduct instanceof ML_Database_Model_Table_Abstract
            ? $mProduct->get('id')
            : $mProduct
        );
        $sSql = "
            select count(*) 
            from magnalister_products p
            inner join magnalister_selection s on p.id=s.pid
            where p.parentid='" . $iMasterProductId . "'
                and s.mpid='" . MLRequest::gi()->get('mp') . "'
                and s.selectionname='" . $this->getProductList()->getSelectionName() . "'
                and s.session_id='" . MLShop::gi()->getSessionId() . "'
        ";
        return MLDatabase::getDbInstance()->fetchOne($sSql);
    }
    protected function callAjaxDeleteFromSelection() {
        $iProductId =  MLRequest::gi()->get('pid');
        $oProduct = MLDatabase::factory('product')->set('id',$iProductId);
        $this->deleteProductsFromSelection(array($oProduct));
        if ($oProduct->get('parentid') == 0) {
            try {
                MLRequest::gi()->get('render');
                $this->callAjaxRenderProduct(false);
            } catch (Exception $oEx) {
            }
        }
        $this->includeView('widget_productlist_action_selection_selectionoption', array(
            'sName' => MLI18n::gi()->get(
                'Productlist_Cell_aToMagnalisterSelection_selectedArticlesCountInfo', 
                 array('count' => $this->getSelectedCount())
            )
        ));
        return $this;
    }
    /**
     * @param array $aProducts (values = int?product-id:ML_Shop_Model_Product)
     * @param null $aProducts delete complete selection
     * @return \ML_Productlist_Controller_Widget_ProductList_Selection
     */
    protected function deleteProductsFromSelection($aProducts=null) {
        $oQuery =
            MLDatabase::factory('selection')->set(
                'selectionname',
                 $this->getProductList()->getSelectionName()
            )->getList()
            ->getQueryObject()
        ;
        $aIds = array();
        if ($aProducts !== null) {
            foreach ($aProducts as $oProduct) {//check parent id if model
                $aIds[] = is_object($oProduct) ? $oProduct->get('id') : $oProduct;
            }
            
        }
        if (!empty($aIds)) {// we dont care of master or variant just delete from selection
            $oQuery->where("
                (
                    pID in (select id from magnalister_products where parentid in('".implode("', '",$aIds)."'))
                    || pID in ('".implode("', '",$aIds)."')
                 )
            ");
        }
        
        MLMessage::gi()->addDebug(sprintf(MLI18n::gi()->get('Productlist_Message_sDeleteProducts'), $oQuery->doDelete()));
        return $this;
    }
    /**
     * @param array $aProducts (values = int?product-id:ML_Shop_Model_Product)
     * @param array $aData data-field of selection
     * @return \ML_Productlist_Controller_Widget_ProductList_Selection
     */
    protected function addProductsToSelection($aProducts, $aData = array()) {
        $aVariantIds = array();
        foreach ($aProducts as $oProduct) {
            $oProduct = is_object($oProduct)?$oProduct:MLProduct::factory()->set('id',$oProduct);
            if ($oProduct->get('parentid')==0) {
                if(count(MLMessage::gi()->getObjectMessages($oProduct)) == 0){//master dont have error we check variants
                    $iCountVariants=0;
                    $aMessages=array();
                    $aVariants=$this->getProductList()->getVariants($oProduct);//$oProduct->getVariants();                    
                    foreach($aVariants as $oVariant){
                        $iCountVariants+= (int)$this->productSelectable($oVariant,false);// counter and maybe adds error
                        if (count(MLMessage::gi()->getObjectMessages($oVariant))>0) {//variant have message
                            foreach(MLMessage::gi()->getObjectMessages($oVariant) as $sMessage){
                                $aMessages[$sMessage] = isset($aMessages[$sMessage])?$aMessages[$sMessage]+1:1;
                            }
                        }
                    }
                    if (!empty($aMessages)) {//variant(s) have error
                        $sAddMessage = '<ul>';
                        foreach ($aMessages as $sMessage => $iCount) {
                            $sAddMessage.= '<li>'.$iCount.'&nbsp;*&nbsp;'.$sMessage.'</li>';
                        }
                        $sAddMessage.= '</ul>';
                        MLMessage::gi()->addObjectMessage($oProduct, MLI18n::gi()->get('Productlist_ProductMessage_sVariantsHaveError').$sAddMessage);
                    }
                }
                if (count(MLMessage::gi()->getObjectMessages($oProduct)) == 0) {//any message now?
                    foreach($aVariants as $oVariant){
                        $aVariantIds[] = $oVariant->get('id');
                    }
                }
            } else {
                $blSelectable=false;
                if (count(MLMessage::gi()->getObjectMessages($oProduct)) == 0) {//variant dont have error
                    $blSelectable = $this->productSelectable($oProduct,false);// adding message
                }
                if ($blSelectable && count(MLMessage::gi()->getObjectMessages($oProduct)) == 0) {
                    $aVariantIds[] = $oProduct->get('id');
                }
            }
        }
        if (!empty($aVariantIds)) {
            $sSelectionName=$this->getProductList()->getSelectionName();
            $oModel = MLDatabase::factory('selection');
            foreach ($aVariantIds as $sId) {
                $oModel->init()->set('selectionname', $sSelectionName)->set('pid', $sId)->set('data', $aData)->save();
            }
            MLMessage::gi()->addDebug(sprintf(MLI18n::gi()->get('Productlist_Message_sEditProducts'), count($aVariantIds)));
        }
        return $this;
    }
    protected function callAjaxAddToSelection() {
        $iProductId =  MLRequest::gi()->get('pid');
        $aData=$this->getRequest('selection');
        $oProduct = MLProduct::factory()->set('id',$iProductId);
        $this->addProductsToSelection(array($oProduct), isset($aData['data']) && is_array($aData['data']) ? $aData['data'] : array());
        if ($oProduct->get('parentid') == 0) {
            try{
                MLRequest::gi()->get('render');
                $this->callAjaxRenderProduct(false);
            } catch (Exception $oEx){
            }
        }
        $this->includeView('widget_productlist_action_selection_selectionoption', array(
            'sName' => MLI18n::gi()->get(
                'Productlist_Cell_aToMagnalisterSelection_selectedArticlesCountInfo', 
                 array('count' => $this->getSelectedCount())
            )
        ));
        return $this;
    }
    /**
     * checks if product is selectable
     * @param ML_Shop_Model_Product_Abstract $oProduct can be master or variant
     * @param bool $blForRender selectable for rendering(checkbox), if false for selectlist(table)
     */
    public function productSelectable(ML_Shop_Model_Product_Abstract $oProduct, $blForRender) {
        return true;
    }
}