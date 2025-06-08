<?php
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');
class ML_Dawanda_Controller_Dawanda_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract {
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
			'StartTime' => array (
				'Label' => $oI18n->ML_GENERIC_CHECKINDATE,
				'Sorter' => 'starttime',
				'Getter' => 'getItemStartTime',
				'Field' => null
			),
			'LastSync' => array (
				'Label' => $oI18n->ML_GENERIC_LASTSYNC,
				'Sorter' => null,
				'Getter' => 'getItemLastSyncTime',
				'Field' => null
			),
		);
	}

	protected function getItemStartTime($item) {
		$item['StartTime'] = strtotime($item['StartTime']);
		return '<td>'.date("d.m.Y", $item['StartTime']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['StartTime']).'</span>'.'</td>';
	}

	protected function getItemLastSyncTime($item) {
		$item['LastSync'] = strtotime($item['LastSync']);
		if ($item['LastSync'] < 0) {
			return '<td>-</td>';
		}
		return '<td>'.date("d.m.Y", $item['LastSync']).' &nbsp;&nbsp;<span class="small">'.date("H:i", $item['LastSync']).'</span>'.'</td>';
	}
        
}