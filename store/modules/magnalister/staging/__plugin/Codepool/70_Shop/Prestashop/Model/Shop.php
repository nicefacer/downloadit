<?php

class ML_Prestashop_Model_Shop extends ML_Shop_Model_Shop_Abstract {

    public function getShopSystemName() {
        return 'prestashop';
    }
    
    public function getShopVersion() {
         $aVersion = explode('.', _PS_VERSION_);
         return "{$aVersion[1]}" ;
    }

    public function getDbConnection() {
        return array(
            'host' => _DB_SERVER_,
            'user' => _DB_USER_,
            'password' => _DB_PASSWD_,
            'database' => _DB_NAME_,
            'persistent' => false
                );
    }
    
    /**
     * hardcoded utf8
     * @see MySQLCore::connect || DbMySQLiCore::connect
     * @return \ML_Prestashop_Model_Shop
     */
    public function initializeDatabase () {
        MLDatabase::getDbInstance()->setCharset('utf8');
        return $this;
    }

    public function getProductsWithWrongSku() {
        return array();
    }

    public function getOrderSatatistic($sDateBack) {
        $oMLQB = MLDatabase::factorySelectClass();
        $result= $oMLQB->select(array('date_add', 'mo.platform'))
                                ->from(_DB_PREFIX_ . 'orders')
                                ->join(array('magnalister_orders', 'mo', 'id_order = mo.current_orders_id'), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                                ->where("date_add BETWEEN '$sDateBack' AND NOW()")->getResult();
                        ;
                        
                        return $result;
    }

    public function getSessionId() {
        // Context::getContext()->cookie->checksum could be a better alternative to simulate session id,
        // testing this stuff is a little tricky and it should be tested in prestashop 1.5.X and 1.6,X
        if (isset(Context::getContext()->cookie->checksum) && Context::getContext()->cookie->checksum != '') {
            return md5(Context::getContext()->cookie->checksum);
        } elseif (isset(Context::getContext()->employee->id)) {
            return md5(Context::getContext()->employee->id);
        } elseif (isset(Context::getContext()->cookie->id_employee)) {
            return md5(Context::getContext()->cookie->id_employee);
        } elseif(isset(Context::getContext()->customer) && isset (Context::getContext()->customer->id)) {
            return md5(Context::getContext()->customer->id);
        }else{
            return 'tempsessionid____123';
        }
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
    
}