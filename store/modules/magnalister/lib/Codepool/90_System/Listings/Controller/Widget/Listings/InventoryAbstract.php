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
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_ListingAbstract');

class ML_Listings_Controller_Widget_Listings_InventoryAbstract extends ML_Listings_Controller_Widget_Listings_ListingAbstract {

    protected $marketplace = '';
    protected $aPostGet = array();
    protected $aSetting = array();
    protected $iMpId = 0;
    protected $aSort = array(
        'type' => null,
        'order' => null
    );
    protected $iOffset = 0;
    protected $aData = array();
    protected $iNumberofitems = 0;
    protected $search = '';
    protected $sCurrency = '';

    public function __construct() {
        parent::__construct();
        $this->setCurrentState();
        $this->aPostGet = MLRequest::gi()->data();
        $this->marketplace = MLModul::gi()->getMarketPlaceName();
        $this->iMpId = MLModul::gi()->getMarketPlaceId();
        $aConfig = MLModul::gi()->getConfig();
        $this->aSetting['maxTitleChars'] = 40;
        $this->aSetting['itemLimit'] = 50;
        $this->aSetting['language'] = $aConfig['lang'];
        $this->sCurrency = $aConfig['currency'];


        if (array_key_exists('tfSearch', $this->aPostGet) && !empty($this->aPostGet['tfSearch'])) {
            $this->search = $this->aPostGet['tfSearch'];
        }
        /** @todo        if (isset($this->aPostGet['refreshStock'])) {
         * $classFile = DIR_MAGNALISTER_MODULES . strtolower($this->marketplace) . '/crons/' . ucfirst($this->marketplace) . 'SyncInventory.php';
         * if (file_exists($classFile)) {
         * require_once($classFile);
         * $className = ucfirst($this->marketplace) . 'SyncInventory';
         * if (class_exists($className)) {
         * @set_time_limit(60 * 10);
         * $ic = new $className($this->iMpId, $this->marketplace);
         * $ic->process();
         * }
         * }
         * } */
    }

    public function getShopTitle() {
        try {
            $aModules = MLSetting::gi()->get('aModules');
            if (!isset($aModules[$this->marketplace]['settings']['subsystem'])) {
                throw new Exception;
            }
            return $aModules[$this->marketplace]['settings']['subsystem'];
        } catch (Exception $exc) {
            return $this->marketplace;
        }
    }

    public function getUrlParams() {
        return $this->aPostGet;
    }

    public function prepareData() {
        $result = $this->getInventory();
        if (($result !== false) && !empty($result['DATA'])) {
            $this->aData = $result['DATA'];
            foreach ($this->aData as &$item) {
                $sTitle = isset($item['Title']) ? $item['Title'] : (isset($item['ItemTitle']) ? $item['ItemTitle'] : '');
                $item['TitleShort'] = (strlen($sTitle) > $this->aSetting['maxTitleChars'] + 2) ?
                    (fixHTMLUTF8Entities(substr($sTitle, 0, $this->aSetting['maxTitleChars'])).'&hellip;') :
                    fixHTMLUTF8Entities($sTitle);
                $item['DateAdded'] = ((isset($item['DateAdded'])) ? strtotime($item['DateAdded']) : '');
            }
            unset($result);
        }
    }

    protected function getInventory() {
        try {
            $request = array(
                'ACTION' => 'GetInventory',
                'LIMIT' => $this->aSetting['itemLimit'],
                'OFFSET' => $this->iOffset,
                'ORDERBY' => $this->aSort['order'],
                'SORTORDER' => $this->aSort['type']
            );
            if (!empty($this->search)) {
                $request['SEARCH'] = $this->search;
            }
            $result = MagnaConnector::gi()->submitRequest($request);
            $this->iNumberofitems = (int)$result['NUMBEROFLISTINGS'];
            return $result;
        } catch (MagnaException $e) {
            return false;
        }
    }

    public function initAction() {
        if (   isset($this->aPostGet['SKUs']) && is_array($this->aPostGet['SKUs'])
            && isset($this->aPostGet['action']) && $this->aPostGet['action'] == 'delete'
        ) {
            $_SESSION['POST_TS'] = $this->aPostGet['timestamp'];

            $aInsertData = array();
            $aDeleteItemsData = array();
            foreach ($this->aPostGet['SKUs'] as $sSku) {
                $oProduct = MLProduct::factory()->getByMarketplaceSKU($sSku);

                $aDetails = unserialize(str_replace('\\"', '"', $this->aPostGet['details'][$sSku]));
                $iProductId = $sProductSku = '';
                if ($oProduct->exists()) {
                    $iProductId = $oProduct->get('MarketplaceIdentId');
                    $sProductSku = $oProduct->get('MarketplaceIdentSku');
                } else {
                    $sProductSku = $sSku;
                }

                $aInsertData[] = array(
                    'mpID' => $this->iMpId,
                    'productsId' => $iProductId,
                    'productsSku' => $sProductSku,
                    'price' => $aDetails['Price'],
                    'timestamp' => date('Y-m-d H:i:s')
                );

                $aDeleteItemsData[] = array(
                    'SKU' => $sSku,
                );
            }

            try {
                $result = MagnaConnector::gi()->submitRequest(array(
                    'ACTION' => 'DeleteItems',
                    'DATA' => $aDeleteItemsData
                ));
                /** @todo create helper if need  call_user_func(ucfirst($this->marketplace) . 'Helper::processCheckinErrors', $result, $this->iMpId); */
            } catch (MagnaException $e) {
                $result = array(
                    'STATUS' => 'ERROR'
                );
            }

            if ($result['STATUS'] == 'SUCCESS') {
                $oDb = MLDatabase::getDbInstance();
                if ($oDb->batchinsert(
                        'magnalister_listings_deleted', $aInsertData
                    ) != true
                ) {
                    MLMessage::gi()->addWarn($oDb->getLastError());
                }
                $this->postDelete();
            }
        }

        $this->getSortOpt();

        if (isset($this->aPostGet['page']) && ctype_digit($this->aPostGet['page'])) {
            $this->iOffset = ($this->aPostGet['page'] - 1) * $this->aSetting['itemLimit'];
        } else {
            $this->iOffset = 0;
        }
    }

    protected function postDelete() { /* Nix :-) */
    }

    protected function isSearchable() {
        return true;
    }

    protected function getFields() {
        return array(
            'SKU' => array(
                'Label' => ML_LABEL_SKU,
                'Sorter' => 'sku',
                'Getter' => null,
                'Field' => 'SKU'
            ),
            'ItemID' => array(
                'Label' => ML_MAGNACOMPAT_LABEL_MP_ITEMID,
                'Sorter' => 'itemid',
                'Getter' => null,
                'Field' => 'ItemID',
            ),
            'Title' => array(
                'Label' => ML_LABEL_SHOP_TITLE,
                'Sorter' => null,
                'Getter' => 'getTitle',
                'Field' => null,
            ),
            'Price' => array(
                'Label' => ML_GENERIC_PRICE,
                'Sorter' => 'price',
                'Getter' => 'getItemPrice',
                'Field' => null
            ),
            'Quantity' => array(
                'Label' => ML_LABEL_QUANTITY,
                'Sorter' => 'quantity',
                'Getter' => null,
                'Field' => 'Quantity',
            ),
            'DateAdded' => array(
                'Label' => ML_GENERIC_CHECKINDATE,
                'Sorter' => 'dateadded',
                'Getter' => 'getItemDateAdded',
                'Field' => null
            ),
        );
    }

    protected function getSortOpt() {
        if (isset($this->aPostGet['sorting'])) {
            $sorting = $this->aPostGet['sorting'];
        } else {
            $sorting = 'blabla'; // fallback for default
        }
        //ToDo
        $sortFlags = array(
            'sku' => 'SKU',
            'meinpaketid' => 'MeinpaketID',
            'price' => 'Price',
            'quantity' => 'Quantity',
            'dateadded' => 'DateAdded',
            'starttime' => 'StartTime'
        );
        $order = 'ASC';
        if (strpos($sorting, '-asc') !== false) {
            $sorting = str_replace('-asc', '', $sorting);
        } else if (strpos($sorting, '-desc') !== false) {
            $order = 'DESC';
            $sorting = str_replace('-desc', '', $sorting);
        }

        if (array_key_exists($sorting, $sortFlags)) {
            $this->aSort['order'] = $sortFlags[$sorting];
            $this->aSort['type'] = $order;
        } else {
            $this->aSort['order'] = 'DateAdded';
            $this->aSort['type'] = 'DESC';
        }
    }

    public function getEmptyDataLabel() {
        return (empty($this->search) ? ML_GENERIC_NO_INVENTORY : ML_LABEL_NO_SEARCH_RESULTS);
    }

    protected function getCurrentPage() {
        if (isset($this->aPostGet['page']) && (1 <= (int)$this->aPostGet['page']) && ((int)$this->aPostGet['page'] <= $this->getTotalPage())) {
            return (int)$this->aPostGet['page'];
        }

        return 1;
    }

    protected function getTotalPage() {
        return ceil($this->iNumberofitems / $this->aSetting['itemLimit']);
    }

    public function getData() {
        return $this->aData;
    }


    public function getNumberOfItems() {
        return $this->iNumberofitems;
    }

    public function getOffset() {
        return $this->iOffset;
    }

    protected function getTitle($item) {
        return '<td title="'.fixHTMLUTF8Entities($item['Title'], ENT_COMPAT).'">'.$item['TitleShort'].'</td>';
    }

    protected function getItemPrice($item) {
        $item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->sCurrency;
        return '<td>'.MLPrice::factory()->format($item['Price'], $item['Currency']).'</td>';
    }

    protected function getItemDateAdded($item) {
        return '<td>'.date("d.m.Y", $item['DateAdded']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['DateAdded']).'</span>'.'</td>';
    }
}
