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

defined('_VALID_XTC') or die('Direct Access to this location is not allowed.');


require_once(DIR_MAGNALISTER_MODULES.'magnacompatible/crons/MagnaCompatibleSyncOrderStatus.php');

class HitmesterSyncOrderStatus extends MagnaCompatibleSyncOrderStatus {

	/**
	 * Builds an element for the ConfirmShipment request.
	 * @return array
	 */
	protected function confirmShipment($date) {
		$cfirm = array (
			'MOrderID' => $this->oOrder['special'],
			'ShippingDate' => localTimeToMagnaTime($date),
			'CarrierCode' => getDBConfigValue($this->marketplace . '.orderstatus.carrier', $this->mpID)
		);
		$this->oOrder['data']['ML_LABEL_SHIPPING_DATE'] = $cfirm['ShippingDate'];

		$trackercode = $this->getTrackingCode($this->oOrder['orders_id']);
		if (false != $trackercode) {
			$this->oOrder['data']['ML_LABEL_TRACKINGCODE'] = $cfirm['TrackingCode'] = $trackercode;
		}

		// flag order as dirty, meaning that it has to be saved.
		$this->oOrder['__dirty'] = true;
		return $cfirm;
	}

	/**
	 * Builds an element for the CancelShipment request
	 * @return array
	 */
	protected function cancelOrder($date) {
		$cncl = array (
			'MOrderID' => $this->oOrder['special'],
			'Reason' => getDBConfigValue($this->marketplace . '.orderstatus.cancelreason', $this->mpID)
		);

		$this->oOrder['data']['ML_LABEL_ORDER_CANCELLED'] = $date;
		// flag order as dirty, meaning that it has to be saved.
		$this->oOrder['__dirty'] = true;
		return $cncl;
	}
	
}
