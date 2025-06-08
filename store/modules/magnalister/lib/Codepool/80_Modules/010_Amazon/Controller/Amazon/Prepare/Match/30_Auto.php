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
//MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Abstract');

class ML_Amazon_Controller_Amazon_Prepare_Match_Auto extends ML_Core_Controller_Abstract {

    protected $aParameters = array('controller');
    //protected $aParameters = array('mp', 'mode', 'view', 'execute');

    public function render() {
        if ($this->getRequest('amazonProperties') !== null) {
            MLMessage::gi()->addSuccess($this->__('Amazon_Label_sAutoMatchSuccess'));
            throw new Exception('automatched');
        }
        parent::render();
    }

    public function renderAjax() {
        $oList = MLProductList::gi('amazon_prepare_match_auto');
        $iOffset = (int) $this->getRequest('offset');
        $iCurrent = $iOffset;
        $oList->setFilters(array('iOffset' => $iOffset));
        $aData = $this->getRequest('amazonProperties');
        $aStatistic = $oList->getStatistic();
        foreach ($oList->getList() as $oProduct) {
            $aVariants = $oList->getVariants($oProduct);
            $iCurrent++;
            if (count($aVariants) == 0) {
                $iOffset++;
            } else {
                foreach ($aVariants as $oVariant) {
                    MLHelper::gi('Model_Table_Amazon_Prepare_Product')->auto($oVariant, $aData)->getTableModel()->save();
                    MLDatabase::factory('selection')->set('selectionname', 'match')->set('pid', $oVariant->get('id'))->delete();
                }
            }
        }
        if ($aStatistic['iCountTotal'] <= $iCurrent) {
            echo json_encode(
                array(
                    'success' => true,
                    'error' => false,
                    'offset' => $iOffset,
                    'info' => array(
                        'current' => $iCurrent,
                        'total' => $aStatistic['iCountTotal'],
                    )
                )
            );
        } else {
            echo json_encode(
                array(
                    'success' => false,
                    'error' => false,
                    'offset' => $iOffset,
                    'info' => array(
                        'current' => $iCurrent,
                        'total' => $aStatistic['iCountTotal'],
                    )
                )
            );
        }
    }
    
    
    public function getCurrentProduct(){
        $oTable = MLDatabase::factory('selection')->set('selectionname', 'match');
        $oSelectList = $oTable->getList();
        /* @var $oSelectList ML_Database_Model_List */
        $aResult = $oSelectList->getQueryObject()->init('aSelect')->select('parentid')->join(array('magnalister_products','p','pid = p.id'))->getResult();
        $sParent = null;
        foreach ($aResult as $aRow){//show shippingtime , condition and ... if it is single preparation
            if($sParent !== null && $sParent != $aRow['parentid']){
                return null;
            }
            $sParent = $aRow['parentid'];
        }
        $aList = $oTable->getList()->getList();
        $oProduct = MLProduct::factory()->set('id', current($aList)->get('pid'));
        $aPreparedData = MLHelper::gi('Model_Service_Product')->addVariant($oProduct)->getData();
        return current($aPreparedData);
    }

}