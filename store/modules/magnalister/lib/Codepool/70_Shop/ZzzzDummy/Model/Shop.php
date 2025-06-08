<?php 
class ML_ZzzzDummy_Model_Shop extends ML_Shop_Model_Shop_Abstract {
    
    public function getShopSystemName(){
        return 'zzzzdummy';
    }
    
    public function getDbConnection(){
        return json_decode(ZZZZDUMMY_DB_CONNECTION, true);
    }
    public function initializeDatabase () {
        MLDatabase::getDbInstance()->setCharset('utf8');
        return $this;
    }
    public function getProductsWithWrongSku(){
        return MLDatabase::getDbInstance()->fetchArray("
            SELECT
                `sku`
            FROM
                `".MLDatabase::factory('ZzzzDummyShopProduct')->getTableName()."`
            GROUP BY 
                `sku`
            HAVING 
                COUNT(`sku`) > 1
            ;
        ", true);
    }

    public function getSessionId() {
        return md5(session_id());
    }
    
    /**
     * will be triggered after plugin update for shop-spec. stuff
     * eg. clean shop-cache
     * @param bool $blExternal if true external files (outside of plugin-folder) was updated
     * @return $this
     */
    public function triggerAfterUpdate($blExternal) {
        return $this;
    }

    /**
     * @todo: order is not implemented
     */
    public function getOrderSatatistic($sDateBack) {
        $aOut = array();
        return $aOut; 
    }
    
}
