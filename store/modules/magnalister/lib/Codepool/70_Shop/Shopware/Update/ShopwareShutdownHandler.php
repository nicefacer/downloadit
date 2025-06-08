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
MLFilesystem::gi()->loadClass('Core_Update_Abstract');
/**
 * @deprecated since version 6053
 * Registers Shopware ShutdownHandler after update
 */
class ML_Shopware_Update_ShopwareShutdownHandler extends ML_Core_Update_Abstract {
    
	protected $subscribesTable = null;
	protected $pluginId = null;
	protected $constollerName = null;
    
 
    /**
     * @deprecated since version 6053
     * check, if update is needed
     * @return boolean
     */
    public function needExecution() {
        return false;
        //disable this update , this Update register an event to skip maintanence mode
        //I have disabled it because I see some problem sometime in shopware event and it make problem in shopware order overview
        //I am not completely sure , but this is the only code that we edit shopware event
        //it seem it is not standard way to register an event in shopware
//        return 
//            ((int)$this->getPluginId() > 0)
//            && !MLDatabase::getDbInstance()->recordExists($this->getSubscribeTable(), array (
//                'subscribe' => 'Enlight_Controller_Front_RouteShutdown',
//                'type' => 0,
//                'listener' => $this->getControllerName().'::onRouteShutdown',
//                'pluginID' => $this->getPluginId(),
//            )
//        );
    }
    
    /**
     * @deprecated since version 6053
     * register shutdownhandler to shopware
     * @return $this
     * @throws Exception, ML_Core_Exception_Update
     */
    public function execute() {
//        if ($this->needExecution()) {
//			MLDatabase::getDbInstance()->insert($this->getSubscribeTable(), array (
//				'subscribe' => 'Enlight_Controller_Front_RouteShutdown',
//				'type' => 0,
//				'listener' => $this->getControllerName().'::onRouteShutdown',
//				'pluginID' => $this->getPluginId(),
//				'position' => 0,
//			));
//		}
        return $this;
    }
    
    /**
     * @deprecated since version 6053
     * sets class variables
     * @return $this
     */
    protected function setClassVars() {
//        $tables = MLDatabase::getDbInstance()->getAvailableTables('/.*core_subscribes$/i');
//		if (!empty($tables)) {
//            $this->subscribesTable = $tables[0];
//            $plugin = MLDatabase::getDbInstance()->fetchRow("
//                  SELECT `pluginID`, `listener`
//                    FROM `".$this->subscribesTable."`
//                   WHERE `listener` LIKE 'Shopware_Plugins_Backend_%Magnalister_Bootstrap%'
//                ORDER BY `pluginID` DESC
//                   LIMIT 1
//            ");
//            if (is_array($plugin)) {
//                $this->pluginId = $plugin['pluginID'];
//                $constollerName = explode('::', $plugin['listener']);
//                $this->constollerName = $constollerName[0];
//            }
//        }
        return $this;
    }
    
    /**
     * @deprecated since version 6053
     * get table name for subscribes
     * @return string
     */
    protected function getSubscribeTable() {
//        if ($this->subscribesTable === null) {
//            $this->setClassVars();
//        }
//        return $this->subscribesTable;
    }
    
    /**
     * @deprecated since version 6053
     * gets controller name for subscribe table (magnalister)
     * @return string
     */
    protected function getControllerName() {
//        if ($this->constollerName === null) {
//            $this->setClassVars();
//        }
//        return $this->constollerName;
    }
    
    /**
     * @deprecated since version 6053
     * gets plugin id for subscribe table (magnalister)
     * @return string
     */
    protected function getPluginId() {
//        if ($this->pluginId === null) {
//            $this->setClassVars();
//        }
//        return $this->pluginId;
    }
    
}
