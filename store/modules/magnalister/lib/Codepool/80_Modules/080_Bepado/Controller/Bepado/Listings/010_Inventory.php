<?php
MLFilesystem::gi()->loadClass('Listings_Controller_Widget_Listings_InventoryAbstract');
class ML_Bepado_Controller_Bepado_Listings_Inventory extends ML_Listings_Controller_Widget_Listings_InventoryAbstract {
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
		);
	}
    
    public function render() {
        $this->includeView('widget_listings_inventory');
        return $this;
    }
        
}