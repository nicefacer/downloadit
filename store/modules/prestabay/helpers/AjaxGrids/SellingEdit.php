<?php
/**
 * File SellingEdit.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */

class AjaxGrids_SellingEdit extends AjaxGrid
{
    protected $_sellingId = null;
    protected $_productLanguage = null;

    protected $originalSellingGrid;

    /**
     * @param null $sellingId
     */
    public function __construct($sellingId = null)
    {
        $this->_gridId = "sellings_products";
        $this->_multiSelect = true;
        $this->_selectModel = new Selling_ProductsModel();
        $this->_primaryKeyName = "id";
        $this->_sellingId = $sellingId;

        $listModel = new Selling_ListModel($sellingId);
        $this->_productLanguage = $listModel->language;

        $this->setHeader(sprintf(L::t("Selling List '%s' - Products"), $listModel->name));

        $this->originalSellingGrid = new Grids_SellingEdit($sellingId);
        $this->originalSellingGrid->initButtons($this, $sellingId, $listModel);

        parent::__construct();
    }

    protected function  _prepareCollection()
    {
        $this->setSelect($this->originalSellingGrid->getSelectModel());

        parent::_prepareCollection();

    }

    protected function _prepareColumns()
    {
        $this->_columns = $this->originalSellingGrid->getColumns();

        parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return UrlHelper::getUrl("sellingAjaxEdit/index", array('id' => $this->_sellingId));
    }

    public function getId($row)
    {
        return $row['id'];
    }

    /** Buttons handlers */

    public function sendItemButton($params = array())
    {
        return array(
            'jshandler' => 'sendItemHandler'
        );
    }

    public function stopItemButton($params = array())
    {
        return array(
            'jshandler' => 'stopItemHandler'
        );
    }

    public function relistItemButton($params = array())
    {
        return array(
            'jshandler' => 'relistItemHandler'
        );
    }

    public function reviseItemButton($params = array())
    {
        return array(
            'jshandler' => 'reviseItemHandler'
        );
    }


    public function removeItemButton($params = array())
    {
        if (count($params) > 0) {
            foreach ($params as $id) {
                Selling_ProductsModel::deleteSellingProductById($id);
            }
        }

        return array(
            'jshandler' => 'removeItemHandler'
        );
    }


    public function priceQtyReviseButton($params = array())
    {
        return array(
            'jshandler' => 'revisePriceQtyItemHandler'
        );
    }

    public function moveItemsButton($params = array(), $extraValue = false)
    {
        $moveIntoSellingListId = $extraValue;

        if (count($params) > 0 && $moveIntoSellingListId != false) {
            Selling_ListModel::moveSellingProducts($params, $moveIntoSellingListId);
        }

        return array(
            'jshandler' => 'moveItemsHandler',
        );
    }

    /** Actions handlers  */

    public function sendItemAction($params = array())
    {
        return $this->multiEbayAction($params, EbayRequestHelper::ACTION_LIST);
    }

    public function reviseItemAction($params = array())
    {
        return $this->singleEbayAction($params, 'reviseList', EbayListHelper::MODE_FULL);
    }

    public function revisePriceQtyItemHandler($params = array())
    {
        return $this->singleEbayAction($params, 'reviseList', EbayListHelper::MODE_QTY_PRICE);
    }


    public function stopItemAction($params = array())
    {
        return $this->singleEbayAction($params, 'stopList', false);
    }

    public function relistItemAction($params = array())
    {
        return $this->singleEbayAction($params, 'relistList', false);
    }

    /**
     * Perform action with item on ebay
     *
     * @param $params
     * @param $action
     *
     * @return array
     *
     * @throws Exception
     */
    protected function multiEbayAction($params, $action)
    {
        if (empty($params['sellingItemIds'])) {
            throw new Exception(L::t('Please select products for actions'));
        }

        $ebayRequestHelper = new EbayRequestHelper();

        return array(
            'success' => true,
            'result' => $ebayRequestHelper->itemAction($params['sellingItemIds'], $action)
        );
    }

    protected function singleEbayAction($params, $action, $extraParam = null)
    {
        if (empty($params['sellingItemIds'])) {
            throw new Exception(L::t('Please select products for actions'));
        }

        if ($action == 'reviseList' && is_null($extraParam)) {
            $extraParam = EbayListHelper::MODE_FULL; // mode
        }

        $response = array();
        foreach ($params['sellingItemIds'] as $row) {
            $sellingProductId = $row['id'];
            $index = $row['index'];

            $sellingProductModel = new Selling_ProductsModel($sellingProductId);
            $sellingId = $sellingProductModel->selling_id;

            $response[$sellingProductId] = array(
                'index' => $index
            );

            if (is_null($sellingId)) {
                // no selling Id for product
                $response[$sellingProductId] += array(
                    'success' => false,
                    'warnings' => "",
                    'errors' => L::t("Selling Not Found"),
                    'item' => array()
                );
                continue;
            }

            $response[$sellingProductId] += EbayListHelper::$action($sellingId, $sellingProductId, $extraParam);
            if ($response[$sellingProductId]['success']) {
                switch ($action) {
                    case 'reviseList':
                    case 'relistList':
                        $response[$sellingProductId]['newState'] = L::t('Active');
                        break;
                    case 'stopList':
                        // On success eBay stop change status to stop manualy. To avoid relist
                        $sellingProductModelToUpdate = new Selling_ProductsModel($sellingProductId);
                        $sellingProductModelToUpdate->status = Selling_ProductsModel::STATUS_STOPED;
                        $sellingProductModelToUpdate->save();
                        $response[$sellingProductId]['newState'] = L::t('Stopped');
                        break;
                }
            }
        }

        return array(
            'success' => true,
            'result' => $response
        );
    }
}