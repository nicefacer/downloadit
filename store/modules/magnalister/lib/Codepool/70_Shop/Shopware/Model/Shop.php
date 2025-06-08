<?php

class ML_Shopware_Model_Shop extends ML_Shop_Model_Shop_Abstract {
    protected $sSessionID = null;
    /** @var Shopware\Models\Shop\Shop   */
    protected $oDefaultShop = null;
    
    public function __construct() {
        $oShop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->find(1);
        Shopware()->Bootstrap()->registerResource('Shop', $oShop);
    }
    
    public function getShopSystemName() {
        return 'shopware';
    }
    
    public function getDbConnection() {
        $dbConnection = Shopware()->getOption('db');
        $sMlConnection = array(
            'host' => $dbConnection['host'] . ((isset($dbConnection['unix_socket']) && !empty($dbConnection['unix_socket']) )
                ? ':' . $dbConnection['unix_socket'] 
                : (isset($dbConnection['port'])  &&  $dbConnection['port'] !== ''
                    ? ':' . $dbConnection['port'] 
                    : ''
                )
            ), //(string) $dbConnection['host'],
            'user' => (string)$dbConnection['username'],
            'password' => (string)$dbConnection['password'],
            'database' => (string)$dbConnection['dbname'],
            'port' => $dbConnection['port'] //for some server that use port and socket
        );
        return $sMlConnection;
    }
    
    public function initializeDatabase () {
        $aDbConfig = Shopware()->getOption('db');
        if (array_key_exists('charset', $aDbConfig)) {
            MLDatabase::getDbInstance()->setCharset($aDbConfig['charset']);
        }
        return $this;
    }
    
    public function getOrderSatatistic($sDateBack) {
        $oMLQB = MLDatabase::factorySelectClass();
        $sTableName = Shopware()->Models()->getClassMetadata('Shopware\Models\Order\Order')->getTableName();
        $aOut = MLDatabase::getDbInstance()->fetchArray("
           SELECT * FROM (
                SELECT so.`ordertime`, mo.`platform` as `platform`, so.id
                  FROM `s_order` so
            INNER JOIN `magnalister_orders` mo ON so.`id` = mo.`current_orders_id`
                 WHERE (so.`ordertime` BETWEEN '$sDateBack' AND NOW())
                 
                 UNION all
                 
                SELECT so.`ordertime`, null as`platform`, so.id
                  FROM `s_order` so
                 WHERE (so.`ordertime` BETWEEN '$sDateBack' AND NOW())
            ) AS T
            Group by T.id
        ");
        return $aOut;
    }
    
    public function getSessionId() {
        if ($this->sSessionID == null) {
            if (Shopware()->Front()->Request()->{'module'} == 'backend') {
                Shopware()->Session();
                $sId = Enlight_Components_Session::getId();
            } else {
                $sId = md5('frontsession');
            }
            $this->sSessionID = $sId;
        }
        return $this->sSessionID;
    }
    
    /**
     * return default shop in shopware
     * @return Shopware\Models\Shop\Shop
     */
    public function getDefaultShop() {
        
        
        if ($this->oDefaultShop === null) {
            try {$oBbuilder = Shopware()->Models()->createQueryBuilder();
                $this->oDefaultShop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->getActiveDefault();  
            } catch (Exception $exc) {
                try {
                    $oBbuilder = Shopware()->Models()->createQueryBuilder();
                    $oQuery = $oBbuilder->select(array('shop'))
                ->from('Shopware\Models\Shop\Shop', 'shop');
                    $aShops = $oQuery
                                    ->getQuery()->getArrayResult();
                    foreach ($aShops as $aShop) {
                        if($aShop['host'] != null){
                            $this->oDefaultShop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->find($aShop['id']);
                        }
                    }
                } catch (Exception $exc) {
                    
                }
            }
        }
        return $this->oDefaultShop;
    }

    public function getProductsWithWrongSku() {
        return array();
    }
    
    /**
     * will be triggered after plugin update for shop-spec. stuff
     * eg. clean shop-cache
     * @param bool $blExternal if true external files (outside of plugin-folder) was updated
     * @return $this
     */
    public function triggerAfterUpdate($blExternal) {
        try {
            MLDatabase::getDbInstance()->query("
                UPDATE s_order_shippingaddress sa
                INNER JOIN s_order o on sa.orderid = o.id
                SET sa.userid = o.userid
                WHERE ( 
                    o.userid <> sa.userid
                    OR sa.userid IS NULL
                )
                AND o.id IS NOT null
            ");
        } catch (Exception $oEx) {
            //Model_Db is not part of installer
        }
        return $this;
    }
}
