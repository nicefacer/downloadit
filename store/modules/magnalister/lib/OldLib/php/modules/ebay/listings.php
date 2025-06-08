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
 * $Id: listings.php 319 2010-08-11 00:25:01Z derpapst $
 *
 * (c) 2010 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the GNU General Public License v2 or later
 * -----------------------------------------------------------------------------
 */

# vgl auch: lib/classes/ComparisonShopping/ComparisonShoppingListings.php

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');
require_once(DIR_MAGNALISTER_MODULES.'ebay/classes/InventoryView.php');
require_once(DIR_MAGNALISTER_MODULES.'ebay/classes/DeletedView.php');

$sController = MLRequest::gi()->get('controller');
if ( strpos($sController, '_deleted') ) {
	$dV = new DeletedView(MLModul::gi()->getMarketPlaceId());
        $dV->includeView('widget_listings_misc_listingbox');
        echo $dV->renderView();
	
} else {
	$iV = new InventoryView(MLModul::gi()->getMarketPlaceId());
        $iV->includeView('widget_listings_misc_listingbox');
	echo $iV->renderView();
}
