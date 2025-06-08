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

class ML_Fyndiq_Controller_Fyndiq_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract
{
    protected $aParameters = array('controller');

    public static function getTabTitle()
    {
        return MLI18n::gi()->get('ML_GENERIC_INVENTORY');
    }

    public static function getTabActive()
    {
        return MLModul::gi()->isConfigured();
    }

    public static function getTabDefault()
    {
        return true;
    }

    protected function getFields()
    {
        $oI18n = MLI18n::gi();
        return array(
            'ArticleSKU' => array(
                'Label' => $oI18n->FYNDIQ_ML_LABEL_ARTICLE_SKU,
                'Sorter' => null,
                'Getter' => null,
                'Field' => 'ArticleSKU'
            ),
            'Title' => array(
                'Label' => $oI18n->ML_LABEL_SHOP_TITLE,
                'Sorter' => null,
                'Getter' => null,
                'Field' => 'ItemTitle',
            ),
            'ArticleName' => array(
                'Label' => $oI18n->FYNDIQ_ML_LABEL_ARTICLE_NAME,
                'Sorter' => null,
                'Getter' => null,
                'Field' => 'ArticleName'
            ),
            'ShopPrice' => array(
                'Label' => $oI18n->FYNDIQ_ML_LABEL_ARTICLE_SHOP_PREIS,
                'Sorter' => 'price',
                'Getter' => 'getItemPrice',
                'Field' => null
            ),
            'MarketplacePrice' => array(
                'Label' => $oI18n->FYNDIQ_ML_LABEL_ARTICLE_MARKETPLACE_PREIS,
                'Sorter' => 'price',
                'Getter' => 'getItemMarketplacePrice',
                'Field' => null
            ),
            'Quantity' => array(
                'Label' => $oI18n->ML_LABEL_QUANTITY,
                'Sorter' => 'quantity',
                'Getter' => 'getQuantities',
                'Field' => null,
            ),
            'LastSync' => array(
                'Label' => $oI18n->ML_GENERIC_LASTSYNC,
                'Sorter' => null,
                'Getter' => 'getItemLastSyncTime',
                'Field' => null
            ),
            'Status' => array(
                'Label' => MLI18n::gi()->fyndiq_inventory_listing_status,
                'Sorter' => 'status',
                'Getter' => 'getStatus',
                'Field' => null
            )
        );
    }

    protected function getItemStartTime($item)
    {
        $item['StartTime'] = strtotime($item['StartTime']);
        return '<td>' . date("d.m.Y", $item['StartTime']) . ' &nbsp;&nbsp;<span class="small">' . date("H:i", $item['StartTime']) . '</span>' . '</td>';
    }

    protected function getItemLastSyncTime($item)
    {
        if ($item['LastSync'] == null) {
            return '<td>-</td>';
        }

        $item['LastSync'] = strtotime($item['LastSync']);
        return '<td>' . date("d.m.Y", $item['LastSync']) . ' &nbsp;&nbsp;<span class="small">' . date("H:i", $item['LastSync']) . '</span>' . '</td>';
    }

    protected function postDelete()
    {
        MagnaConnector::gi()->submitRequest(array(
            'ACTION' => 'UploadItems'
        ));
    }

    protected function getQuantities($item)
    {
        $oProduct = MLProduct::factory()->getByMarketplaceSKU($item['ArticleSKU']);
        if ($oProduct->exists()) {
            $shopQuantity = $oProduct->getStock();
        } else {
            $shopQuantity = '&mdash;';
        }

        return '<td>' . $shopQuantity . ' / ' . $item['Quantity'] . '</td>';
    }

    protected function getStatus($item)
    {
        if (isset($item['Status']) === false) {
            $status = '<td> - </td>';
        } else {
            $status = "<td>{$item['Status']}</td>";
        }

        return $status;
    }

    protected function getItemMarketplacePrice($item) {
        $item['Currency'] = isset($item['Currency']) ? $item['Currency'] : $this->sCurrency;
        $price = $item['Price'] + $item['ShippingCost'];
        return '<td>'.MLPrice::factory()->format($price, $item['Currency']).'</td>';
    }
}
