<?php
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');
class ML_Check24_Controller_Check24_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract {
    protected $aParameters=array('controller');
    
    public static function getTabTitle () {
        return MLI18n::gi()->get('ML_GENERIC_INVENTORY');
    }
    public static function getTabActive() {
        return MLModul::gi()->isConfigured();
    }
    
    protected function getFields() {
        $oI18n = MLI18n::gi();
		return array(
			'SKU' => array (
				'Label' => $oI18n->ML_LABEL_SKU,
				'Sorter' => 'sku',
				'Getter' => null,
				'Field' => 'SKU'
			),
			'Title' => array (
				'Label' => $oI18n->ML_LABEL_SHOP_TITLE,
				'Sorter' => null,
				'Getter' => 'getTitle',
				'Field' => null,
			),
			'Price' => array (
				'Label' => $oI18n->ML_GENERIC_PRICE,
				'Sorter' => 'price',
				'Getter' => 'getItemPrice',
				'Field' => null
			),
			'Quantity' => array (
				'Label' => $oI18n->ML_LABEL_QUANTITY,
				'Sorter' => 'quantity',
				'Getter' => null,
				'Field' => 'Quantity',
			),
			'LastSync' => array (
				'Label' => $oI18n->ML_GENERIC_LASTSYNC,
				'Sorter' => null,
				'Getter' => 'getItemLastSyncTime',
				'Field' => null
			),/*
			'Status' => array (
				'Label' => $oI18n->check24_inventory_listing_status,
				'Getter' => 'getStatus',
				'Field' => null
			),*/
		);
	}

	protected function getItemLastSyncTime($item) {
		$item['LastSync'] = strtotime($item['LastSync']);
		if ($item['LastSync'] < 0) {
			return '<td>-</td>';
		}
		return '<td>'.date("d.m.Y", $item['LastSync']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['LastSync']).'</span>'.'</td>';
	}
	
	protected function postDelete() {
		MagnaConnector::gi()->submitRequest(array(
			'ACTION' => 'UploadItems'
		));
    }

	/**
	 * Overridden method, because of asynchronous upload concept, here parameter EXTRA is added
	 *
	 * @return bool
	 */
	/*protected function getInventory() {
		try {
			$request = array(
				'ACTION' => 'GetInventory',
				'LIMIT' => $this->aSetting['itemLimit'],
				'OFFSET' => $this->iOffset,
				'ORDERBY' => $this->aSort['order'],
				'SORTORDER' => $this->aSort['type'],
				'EXTRA' => 'ShowPending'
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
	}*/

	/**
	 * Prints indicators in inventory table
	 * @param $item
	 * @return string
	 */
	protected function getStatus($item) {
		$html = '<td>';
		$status = $item['Status'];
		$updated = $item['Updated'];
		if ($status == 'active') {
			$html .= '<div class="semaphore-base semaphoreGreen"></div>';
		} elseif ($status == 'pending' && $updated == 'false') {
			$html .= '<div class="semaphore-base semaphoreGray"></div>';
		} elseif ($status == 'pending' && $updated == 'true') {
			$html .= '<div class="semaphore-base semaphoreBlue"></div>';
		}

		return $html . '</td>';
	}
        
}