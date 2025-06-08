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
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');

class ML_Fyndiq_Controller_Fyndiq_Listings_Rejected extends ML_Listings_Controller_Widget_Listings_InventoryAbstract
{

    protected $aParameters = array('controller');

    public static function getTabTitle()
    {
        return MLI18n::gi()->get('ML_GENERIC_REJECTED');
    }

    public static function getTabActive()
    {
        return MLModul::gi()->isConfigured();
    }

    public static function getTabDefault()
    {
        return true;
    }

    public function prepareData() {
        $result = $this->getInventory();
        if (($result !== false) && !empty($result['DATA'])) {
            $this->aData = $result['DATA'];
            foreach ($this->aData as &$item) {
                $item['Category'] = '';
                $oProduct = MLProduct::factory()->getByMarketplaceSKU($item['ArticleSKU']);
                if ($oProduct->exists()) {
                    $item['Category'] = $oProduct->getCategoryPath();
                    $item['Title'] = $oProduct->getName();
                }
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
                'SORTORDER' => $this->aSort['type'],
                'MODE' => 'Rejected'
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

    protected function getSortOpt() {
        if (isset($this->aPostGet['sorting'])) {
            $sorting = $this->aPostGet['sorting'];
        } else {
            $sorting = 'blabla'; // fallback for default
        }
        //ToDo
        $sortFlags = array(
            'sku' => 'SKU',
            'title' => 'Title',
            'timestamp' => 'timestamp',
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

    public function getFields() {
        return array(
            'ArticleSKU' => array(
                'Label' => ML_LABEL_SKU,
                'Sorter' => null,
                'Getter' => null,
                'Field' => 'ArticleSKU'
            ),
            'Title' => array(
                'Label' => ML_LABEL_SHOP_TITLE,
                'Sorter' => 'title',
                'Field' => 'Title',
            ),
            'Category' => array(
                'Label' => ML_LABEL_CATEGORY_PATH,
                'Sorter' => null,
                'Field' => 'Category'
            ),
            'Price' => array(
                'Label' => ML_GENERIC_OLD_PRICE,
                'Sorter' => null,
                'Getter' => 'getItemMarketplacePrice',
                'Field' => null
            ),
            'Reason' => array(
                'Label' => ML_GENERIC_REASON,
                'Sorter' => null,
                'Getter' => 'getRejectReason',
                'Field' => null
            ),
            'timestamp' => array(
                'Label' => ML_GENERIC_DELETEDDATE,
                'Sorter' => 'timestamp',
                'Getter' => 'getItemLastSyncTime',
                'Field' => null
            ),
        );
    }

    protected function getItemMarketplacePrice($item) {
        $item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->sCurrency;
        $price = $item['Price'] + $item['ShippingCost'];
        return '<td>'.MLPrice::factory()->format($price, $item['Currency']).'</td>';
    }

    protected function getRejectReason($item)
    {
        if (!isset($item['RejectReason']) || empty($item['RejectReason'])) {
            $item['RejectReason'] = MLI18n::gi()->fyndiq_inventory_reject_message;
        }

        return '<td>'.fixHTMLUTF8Entities($item['RejectReason'], ENT_COMPAT).'</td>';
    }

    protected function getItemLastSyncTime($item)
    {
        if ($item['LastSync'] == null) {
            return '<td>-</td>';
        }

        $item['LastSync'] = strtotime($item['LastSync']);
        return '<td>' . date("d.m.Y", $item['LastSync']) . ' &nbsp;&nbsp;<span class="small">' . date("H:i", $item['LastSync']) . '</span>' . '</td>';
    }
}
