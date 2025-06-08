<?php

class ML_Prestashop_Helper_Model_Product {

    public function getProductFeatureValue($id_product, $id_feature, $id_lang) {
        $value = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
				SELECT value
				FROM ' . _DB_PREFIX_ . 'feature_product pf
				LEFT JOIN ' . _DB_PREFIX_ . 'feature_lang fl ON (fl.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'feature_value_lang fvl ON (fvl.id_feature_value = pf.id_feature_value AND fvl.id_lang = ' . (int) $id_lang . ')
				LEFT JOIN ' . _DB_PREFIX_ . 'feature f ON (f.id_feature = pf.id_feature AND fl.id_lang = ' . (int) $id_lang . ')
				' . Shop::addSqlAssociation('feature', 'f') . '
				WHERE pf.id_product = ' . (int) $id_product . '
				AND pf.id_feature = ' . (int) $id_feature . '
				ORDER BY f.position ASC'
        );
        if ($value !== false) {
            return $value;
        } else {
            return '';
        }
    }

    public function getProductByReference($sReference, $blOnlyMaster = false) {
        $iPID = Db::getInstance()->getValue('
		SELECT `id_product`
		FROM `' . _DB_PREFIX_ . 'product` p
		WHERE p.reference = "' . pSQL($sReference) . '"');

        if ($iPID !== false) {
            $oProduct = new Product($iPID, true);
        } elseif ($blOnlyMaster) {
            $oProduct = new Product();
        } else {
            $oProduct = $this->getCombinationByReference($sReference);
        }
        return $oProduct;
    }

    protected function getCombinationByReference($sReference) {
        $iPID = Db::getInstance()->getValue('
             SELECT `id_product_attribute` 		
		FROM `' . _DB_PREFIX_ . 'product_attribute`
		WHERE reference = "' . pSQL($sReference) . '"');

        if ($iPID !== false) {
            $oProduct = new Combination($iPID);
            return $oProduct;
        } else {
            return null;
        }
    }

    public function getProductSelectQuery($blJustId = false) {
        $oSqlQuery = MLDatabase::factorySelectClass();
        if ($blJustId) {
            $oSqlQuery->select('DISTINCT p.id_product');
        } else {
            $oSqlQuery->select(array('p.id_product as ProductId', 'p.*', 'product_shop.*', 'pl.*'));
        }
        $oSqlQuery->from(_DB_PREFIX_ . 'product', 'p')
                ->join(array(_DB_PREFIX_ . 'product_shop', 'product_shop', '(product_shop.id_product = p.id_product AND product_shop.id_shop = ' . Context::getContext()->shop->id . ') '), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                ->join(array(_DB_PREFIX_ . 'product_lang', 'pl', '(p.`id_product` = pl.`id_product` ' . Shop::addSqlRestrictionOnLang('pl') . ') AND pl.`id_lang` = ' . (int) _LANG_ID_), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
        ;
        $sConfig = MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value');
        $this->oSelectQuery = $oSqlQuery;
        return $oSqlQuery;
    }

}
