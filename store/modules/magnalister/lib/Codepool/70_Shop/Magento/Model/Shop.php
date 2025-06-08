<?php 
class ML_Magento_Model_Shop extends ML_Shop_Model_Shop_Abstract {
    
    public function getShopSystemName() {
        return 'magento';
    }
    
    public function getDbConnection() {
        $oConnection = Mage::getConfig()->getNode('global/resources/default_setup/connection');
//        new dBug($oConnection->asArray());
        return array(
            'host'      => (string)$oConnection->host,
            'user'      => (string)$oConnection->username,
            'password'  => (string)$oConnection->password,
            'database'  => (string)$oConnection->dbname,
            'persistent' => false
        );
    }
    
    /**
     * magento dont have defined charset, but initstatements with charset info
     * @return \ML_Magento_Model_Shop
     */
    public function initializeDatabase () {
        foreach (Mage::getConfig()->getNode('global/resources/default_setup/connection/initStatements') as $sQuery) {
            MLDatabase::getDbInstance()->query($sQuery);
        }
        return $this;
    }

    public function getProductsWithWrongSku() {
        return array();
    }

    public function getOrderSatatistic($sDateBack) {
        return MLDatabase::factorySelectClass()
            ->select(array('mgo.`created_at`', 'mlo.`platform`'))
            ->from(Mage::getSingleton('core/resource')->getTableName('sales_flat_order'), 'mgo')
            ->join(array('magnalister_orders', 'mlo', 'mgo.`increment_id` = mlo.`current_orders_id`'), ML_Database_Model_Query_Select::JOIN_TYPE_LEFT)
            ->where("mgo.`created_at` BETWEEN '$sDateBack' AND NOW()")
            ->getResult()
        ;
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

    public function initMagentoStore($sStoreId) {
        if (Mage::app()->getStore()->getId() != $sStoreId) {
            Mage::app()->getStore()->clearInstance()->setId($sStoreId);
            Mage::app()->getStore()->load($sStoreId);
        }
        return Mage::app()->getStore();
    }
    
    /**
     * magento can manage that by it self , so don't need it
     * @return boolean
     */
    public function needConvertToTargetCurrency(){
        return false;
    }
}
