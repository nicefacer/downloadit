<?php

/**
 * File DebugHelper.php
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
class EbayHelper
{

    public static function convertDateTimeToEbayTime(DateTime $time)
    {
        $t = clone $time;
        $t->setTimezone(new DateTimeZone('UTC'));

        $tstamp = $t->format('U'); // to timestamp
        setlocale(LC_TIME, 'en_US');

        return strftime("%Y-%m-%dT%H:%M:%S", $tstamp);
    }

    public static function convertEbayTimeToMysqlDateTime($ebayTime)
    {
        $ebayTime = (string) $ebayTime;
        return date('Y-m-d H:i:s', strtotime($ebayTime));
    }

    public static function getItemPath($itemId, $mode, $siteId = 1)
    {
        $url = '';

        switch ($mode) {
            case AccountsModel::ACCOUNT_MODE_LIVE:
                $marketplace = new MarketplacesModel($siteId);
                $url = $marketplace->url;
                break;
            case AccountsModel::ACCOUNT_MODE_SANDBOX:
                $url = 'sandbox.ebay.com';
                break;
        }

        return 'http://cgi.' . $url . '/ws/eBayISAPI.dll?ViewItem&item=' . $itemId;
    }

    public static function getListingTypeCodeByName($listingType)
    {
        switch ($listingType) {
            case "FixedPriceItem":
                return ProfilesModel::AUCTION_TYPE_FIXEDPRICE;
            case "Chinese":
                return ProfilesModel::AUCTION_TYPE_CHINESE;
        }
        
        return ProfilesModel::AUCTION_TYPE_FIXEDPRICE;
    }

}
