<?php
MLFilesystem::gi()->loadClass('Core_Controller_Abstract');

class ML_Amazon_Controller_Amazon_Prepare_Apply_Form extends ML_Core_Controller_Abstract {

    protected $aParameters = array('controller');

    public function __construct() {
        parent::__construct();
        if (MLDatabase::factory('selection')->set('selectionname', 'apply')->getList()->getCountTotal() == 0) {
            MLHttp::gi()->redirect($this->getParentUrl());
        }
    }

    public function render() {
        if ($this->getRequest('saveApplyData') == 'true') {
            $aRequest = $this->getRequest();
            $aData = array();
            foreach (array(
                         'ProductType',
                         'BrowseNodes',
                         'ItemTitle',
                         'Manufacturer',
                         'Brand',
                         'ManufacturerPartNumber',
                         'EAN',
                         'Images',
                         'BulletPoints',
                         'Description',
                         'Keywords',
                         'Attributes',
                         'ConditionType',
                         'ConditionNote',
                     ) as $sKey) {
                if (isset($aRequest[$sKey])) {
                    $aData['ApplyData'][$sKey] = $aRequest[$sKey];
                }

            }

            $aData['ShippingTime'] = isset($aRequest['ShippingTime']) ? $aRequest['ShippingTime'] : (isset($aRequest['LeadtimeToShip']) ?$aRequest['LeadtimeToShip']:null );
            $aData['LeadtimeToShip'] = $aData['ShippingTime'] ; //deprecated
            $aData['PreparedTs'] = date('Y-m-d H:i:s');

            foreach (array(
                         'MainCategory',
                         'ProductType',
                         'BrowseNodes',
                         'ConditionType',
                         'ConditionNote',
                     ) as $sKey) {
                if (isset($aRequest[$sKey])) {
                    $aData[$sKey] = $aRequest[$sKey];
                }
            }

            $sSql = "
                SELECT pID
                  FROM magnalister_selection s
                 WHERE     s.session_id = '".MLShop::gi()->getSessionId()."'
                       AND s.selectionname = 'apply'
                       AND mpid = '".MLModul::gi()->getMarketPlaceId()."'
            ";
            $oProduct = MLProduct::factory();
            //$oPrepare=MLTable::factory('amazon_prepare');
            $oSelection = MLDatabase::factory('selection');
            $aMissing = array();
            /* @var $oPrepare ML_Amazon_Model_Table_Amazon_Prepare */
            foreach (MLDatabase::getDbInstance()->fetchArray($sSql) as $aRow) {
                if ($oProduct->init(true)->set('id', $aRow['pID'])->exists()) {
                    try {
                        $oPrepareModel = MLHelper::gi('Model_Table_Amazon_Prepare_Product')->apply($oProduct->init(true)->set('id', $aRow['pID']), $aData)->getTableModel()->save();

                        if (count($oPrepareModel->getMissingFields()) > 0) {
                            $aMissing[$aRow['pID']] = $oPrepareModel->getMissingFields();
                        }
                        //prepareByProduct('apply',$oProduct->init(true)->set('id',$aRow['pID']),$aData)->save();
                        //$oPrepare->init(true)->setByProduct($oProduct->init(true)->set('id',$aRow['pID']), 'apply', $aData)->save();
                        $oSelection->init(true)->set('selectionname', 'apply')->set('pid', $aRow['pID'])->delete();
                    } catch (Exception $oExc) {
                        MLMessage::gi()->addWarn($oExc);
                    }
                }
            }
            if (count($aMissing) > 0) {
                $sMessage = '';
                $oddEven = true;
                $i = 0;
                foreach ($aMissing as $sId => $aItems) {
                    if ($i > 20) {
                        $sMessage .= '
                            <tr class="'.(($oddEven = !$oddEven) ? 'odd' : 'even').'">
                                <td colspan="5" class="textcenter bold">&hellip;</td>
                            </tr>
                        ';
                        break;
                    }
                    /* @var $oProduct ML_Shop_Model_Product_Abstract */
                    $oProduct = MLProduct::factory()->set('id', $sId);
                    $sMessage .= '
                        <tr class="'.(($oddEven = !$oddEven) ? 'odd' : 'even').'">
                            <td>'.$sId.'</td>
                            <td>'.$oProduct->getMarketPlaceSku().'</td>
                            <td>'.$oProduct->getName().'</td>
                            <td>'.implode(',', $aItems).'</td>
                            <td  class="product-link"><a class="gfxbutton edit ml-js-noBlockUi" title="bearbeiten" target="_blank" href="'.$oProduct->getEditLink().'">&nbsp;</a></td>
                        </tr>
                    ';
                    ++$i;
                }
                $sMessage =
                    '
                    <table class="datagrid">
                        <thead>
                            <tr>
                                <th>'.$this->__('ML_LABEL_PRODUCTS_ID').'</th>
                                <th>'.$this->__('ML_LABEL_ARTICLE_NUMBER').'</th>
                                <th>'.$this->__('ML_LABEL_PRODUCT_NAME').'</th>
                                <th>'.$this->__('ML_AMAZON_LABEL_MISSING_FIELDS').'</th>
                                <th>&nbsp;</th>
                            </tr>
                        </thead>
                        <tbody>'.
                            $sMessage.'
                        </tbody>
                    </table>
                ';
                MLMessage::gi()->addNotice($this->__('ML_AMAZON_TEXT_APPLY_DATA_INCOMPLETE').$sMessage, $aMissing);
            }
            throw new Exception('saved');
        }
        return parent::render();
    }

    public function execute() {
        try {
            include MLFilesystem::getOldLibPath('php/modules/amazon/apply.php');
        } catch (Exception $oEx) {
            MLMessage::gi()->addNotice($oEx);
        }
    }

    public function renderAjax() {
        $this->execute();
    }
}
