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
MLFilesystem::gi()->loadClass('ProductList_Model_ProductListDependency_SelectFilter_Abstract');
class ML_ProductList_Model_ProductListDependency_MarketplaceSyncFilter extends ML_ProductList_Model_ProductListDependency_SelectFilter_Abstract {
    
    protected $aFilterValues = null;
    
    protected $aConfig = array(
        'blPrepareMode' => true,
    );
    
    /**
     * max count for chunks per request
     * @var int $iLimit
     */
    protected $iLimit = 100;
    
    /**
     * life-time for cache
     * @var int $iCacheLifeTime
     */
    protected $iCacheLifeTime = 1800;
    
    /**
     * delete cache after success
     * @var bool $blDeleteCache  default = false
     */
    protected $blDeleteCache = false;
    
    /**
     * constructor, deletes cache on none-ajax request
     */
    public function __construct() {
        if ($this->blDeleteCache && !MLHttp::gi()->isAjax()) {
            foreach (array('optimize', 'deleted', 'default') as $sStep) {
                MLCache::gi()->delete(strtoupper(get_class($this)).'__'.$sStep.'_'.MLModul::gi()->getMarketPlaceId().'.json');
            }
        }
    }

    /**
     * render current filter form-field
     * @param ML_Core_Controller_Abstract $oController
     * @param string $sFilterName
     * @return string rendered HTML
     */
    public function renderFilter(ML_Core_Controller_Abstract $oController, $sFilterName) {
        return $oController->includeViewBuffered('widget_productlist_filter_select_marketplacesync_snippet', array(
            'aFilter' => array(
                'name' => $sFilterName,
                'value' => $this->getFilterValue(),
                'values' => $this->getFilterValues()
            )
        ));
    }

    /**
     * calcs filter-sql depended by request
     * @return string
     */
    protected function getFilterSql () {
        $sValue = strtolower($this->getFilterValue());
        switch ($sValue) {
            case 'notactive' : {
                $sSql = "(prepare.transferred='0' OR prepare.deletedBy!='')";
                break;
            }
            case 'nottransferred' : {
                $sSql = "(prepare.transferred='0')";
                break;
            }
            case 'active': {
                $sSql = "(prepare.transferred='1' AND prepare.deletedBy='')";
                break;
            }
            case 'sync':
            case 'button':
            case 'expired': {
                $sSql = "prepare.deletedBy='".$sValue."'";
                break;
            }
        }
        return $sSql;
    }
    
    /**
     * returns array with in or not in ident-type query-values
     * @return array array('in' => (array||null), 'notIn' => (array||null)) if null, filter-part is not active
     */
    public function getMasterIdents() {
        $sValue = $this->getFilterValue();
        if ($sValue !== 'all' && in_array($sValue, array_keys($this->getFilterValues()))) {
            $sProductTable = MLDatabase::getTableInstance('product')->getTableName();
            // get masterarticles which have prepared variants
            $sSql = "
                SELECT master.id
                FROM " . $sProductTable . " master 
                INNER JOIN " . $sProductTable . " variant ON master.id = variant.parentid
                INNER JOIN " . MLDatabase::getPrepareTableInstance()->getTableName() . " prepare ON variant.id = prepare." . MLDatabase::getPrepareTableInstance()->getProductIdFieldName() . "
                WHERE " . $this->getFilterSql() . " AND prepare." . MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName() . "='" . MLModul::gi()->getMarketPlaceId() . "'" . "
                GROUP BY master.id
            ";
            $aIds = MLDatabase::getDbInstance()->fetchArray($sSql, true);
            if ($this->getConfig('blPrepareMode') && in_array(strtolower($sValue), array('notactive', 'nottransferred'))) {
                // add all products which are not in preparetable
                $aIds = array_merge($aIds, MlDatabase::getDbInstance()->fetchArray("
                    SELECT p.id 
                        FROM magnalister_products p 
                        INNER JOIN magnalister_products v ON p.id=v.parentid
                        WHERE 
                            p.parentid = 0
                            AND v.id NOT IN (
                                SELECT products_id 
                                FROM magnalister_ebay_prepare 
                                WHERE " . MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName() . "='" . MLModul::gi()->getMarketPlaceId() . "'
                            )
                ", true));
            }
            return array(
                'in' => MLDatabase::getDbInstance()->fetchArray("
                    SELECT " . (MLDatabase::factory('config')->set('mpid', 0)->set('mkey', 'general.keytype')->get('value') == 'pID' ? 'productsid' : 'productssku') . " 
                    FROM " . $sProductTable . " 
                    WHERE id IN ('" . implode("', '", array_unique($aIds)) . "')
                ", true),
                'notIn' => null,
            );
        } else {
            return array(
                'in' => null,
                'notIn' => null
            );
        }
    }

    /**
     * check if variant is in filter or not
     * @param ML_Shop_Model_Product_Abstract $oProduct
     * @return boolean
     */
    public function variantIsActive(ML_Shop_Model_Product_Abstract $oProduct) {
        $sValue = $this->getFilterValue();
        if ($sValue !== 'all' && in_array($sValue, array_keys($this->getFilterValues()))) {
            $sSql = "
                SELECT count(*)
                FROM ".MLDatabase::getPrepareTableInstance()->getTableName()." prepare
                WHERE ".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                AND ".$this->getFilterSql()."
                AND prepare.".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."='".$oProduct->get('id')."'
            ";
            $iCount = MLDatabase::getDbInstance()->fetchOne($sSql);
            if ($iCount == 0 && substr($sValue, 0, 3) == 'not' && $this->getConfig('blPrepareMode')) {
                $sSql = "
                    SELECT count(*) 
                    FROM ".MLDatabase::getPrepareTableInstance()->getTableName()." prepare
                    WHERE prepare.".MLDatabase::getPrepareTableInstance()->getMarketplaceIdFieldName()."='".MLModul::gi()->getMarketPlaceId()."'
                    AND prepare.".MLDatabase::getPrepareTableInstance()->getProductIdFieldName()."='".$oProduct->get('id')."'
                ";
                $iCount = MLDatabase::getDbInstance()->fetchOne($sSql);
                $iCount = $iCount > 0 ? 0 : 1;
            }
            return $iCount > 0 ? true : false;
        }
        return true;
    }
    
    /**
     * optimize table
     * @param ML_Core_Controller_Widget_ProgressBar $oProgress
     * @param string $sCacheName
     * @return boolean success
     */
    protected function stepOptimize (ML_Core_Controller_Widget_ProgressBar $oProgress, $sCacheName) {
        $sSql = "OPTIMIZE TABLE ".MLDatabase::getPrepareTableInstance()->getTableName().';';
        MLDatabase::getDbInstance()->query($sSql);
        $oProgress->addLog('sql: '.$sSql)->setTotal(100)->setDone(100);
        MLCache::gi()->set($sCacheName, '', $this->iCacheLifeTime);
        return true;
    }    
    
    
    protected function stepDefault(ML_Core_Controller_Widget_ProgressBar $oProgress, $sCacheName) {
        if (!MLCache::gi()->exists($sCacheName)) {
            return $this->stepDefaultNoCache($oProgress, $sCacheName);
        } else {
            return $this->stepDefaultCache($oProgress, $sCacheName);
        }
    }
    
    protected function getInventoryOnlySkusRequest () {
        return array(
            'ACTION' => 'GetInventoryOnlySKUs',
            'SUBSYSTEM' => MLModul::gi()->getMarketPlaceName(),
            'MARKETPLACEID' => MLModul::gi()->getMarketPlaceId(),
        );
    }
    
    /**
     * intialize table, get api items and save to cache
     * @param ML_Core_Controller_Widget_ProgressBar $oProgress
     * @param string $sCacheName
     * @return boolean success
     */
    protected function stepDefaultNoCache(ML_Core_Controller_Widget_ProgressBar $oProgress, $sCacheName) {
        //set all articles as deleted, after api-request they should be correct not-deleted-value<br />
        $sSql = "
            UPDATE " . MLDatabase::getPrepareTableInstance()->getTableName() . "
            SET deletedBy = 'notML' 
            WHERE deletedBy = '' 
            AND mpID = '" . MLModul::gi()->getMarketPlaceId() . "';
        ";
        $oProgress->addLog('sql: ' . $sSql);
        MLDatabase::getDbInstance()->query($sSql);
        $aRequest = $this->getInventoryOnlySkusRequest();
        $oProgress->addLog('api: ' . $aRequest['ACTION'] . '(' . $aRequest['SUBSYSTEM'] . ': ' . $aRequest['MARKETPLACEID'] . ')');
        $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
        $aItems = empty($aResponse['DATA']) ? array() : $aResponse['DATA'];
        MLCache::gi()->set($sCacheName, array('done' => array(), 'items' => $aItems), $this->iCacheLifeTime);
        $oProgress->setTotal(count($aItems))->setDone(0);
        return false;// ever false
    }
    
    /**
     * walk api-items which are in cache
     * @param ML_Core_Controller_Widget_ProgressBar $oProgress
     * @param string $sCacheName
     * @return boolean success
     */
    protected function stepDefaultCache (ML_Core_Controller_Widget_ProgressBar $oProgress, $sCacheName) {
        $aItems = MLCache::gi()->get($sCacheName);
        $oProgress->setTotal(count($aItems['items']) + count($aItems['done']));
        $aUpdateLog = array();
        $aNotFoundLog = array();
        $iCount = 0;
        while (!empty($aItems['items']) && $iCount+1 <= $this->iLimit) {
            $iCount++;
            $sSku = array_shift($aItems['items']);
            $oProduct = MLProduct::factory()->getByMarketplaceSKU($sSku);
            if ($oProduct->exists()) {
                $aUpdateLog[] = $sSku;
                MLDatabase::getPrepareTableInstance()->init(true)->set('products_id', $oProduct->get('id'))->set('transferred', 1)->set('deletedBy', '')->save();
            } else {
                $aNotFoundLog[] = $sSku;
            }
            $aItems['done'][] = $sSku;
        }
        if (!empty($aUpdateLog)) {
            $oProgress->addLog('update as available: '.implode(', ',$aUpdateLog));
        }
        if (!empty($aNotFoundLog)) {
            $oProgress->addLog('products not found: '.implode(', ',$aNotFoundLog));
        }
        $oProgress->setDone(count($aItems['done']));
        MLCache::gi()->set($sCacheName, $aItems, $this->iCacheLifeTime);
        return empty($aItems['items']);
    }
    
    /**
     * sync. deleted items
     * @param ML_Core_Controller_Widget_ProgressBar $oProgress
     * @param string $sCacheName
     * @return boolean success
     */
    protected function stepDeleted (ML_Core_Controller_Widget_ProgressBar $oProgress, $sCacheName) {
        if (!MLCache::gi()->exists($sCacheName)) {
            $iOffset = 0;
        } else {
            $iOffset = MLCache::gi()->get($sCacheName);
        }
        $aRequest = array(
            'ACTION' => 'GetInventory',
			'SUBSYSTEM' => MLModul::gi()->getMarketPlaceName(),
			'MARKETPLACEID' => MLModul::gi()->getMarketPlaceId(),
			'LIMIT' => $this->iLimit,
			'OFFSET' => $iOffset,
			'ORDERBY' => 'DateAdded',
			'SORTORDER' => 'DESC',
			'FILTER' => 'DELETED',
        );
        $oProgress->addLog('api: ' . $aRequest['ACTION'] . '(' . $aRequest['SUBSYSTEM'].'('.$aRequest['FILTER'].'): ' . $aRequest['MARKETPLACEID'] . ')');
        $aResponse = MagnaConnector::gi()->submitRequest($aRequest);
        $oProgress->setTotal($aResponse['NUMBEROFLISTINGS']);
        $aItems = isset($aResponse['DATA']) && is_array($aResponse['DATA']) ? $aResponse['DATA'] : array();
        $aUpdateLog = array();
        $aNotFoundLog = array();
        foreach ($aItems as $aItem) {
            $oProduct = MLProduct::factory()->getByMarketplaceSKU($aItem['SKU']);
            if ($oProduct->exists()) {
                $aUpdateLog[] = $aItem['SKU'];
                MLDatabase::getPrepareTableInstance()->init(true)->set('products_id', $oProduct->get('id'))->set('transferred', 1)->set('deletedBy', $aItem['deletedBy'])->save();
            } else {
                $aNotFoundLog[] = $aItem['SKU'];
            }
        }
        if (!empty($aUpdateLog)) {
            $oProgress->addLog('update as deleted: '.implode(', ',$aUpdateLog));
        }
        if (!empty($aNotFoundLog)) {
            $oProgress->addLog('products not found: '.implode(', ',$aNotFoundLog));
        }
        MLCache::gi()->set($sCacheName, $iOffset + count($aItems), $this->iCacheLifeTime);
        return $aResponse['NUMBEROFLISTINGS'] <= $iOffset + count($aItems);
    }
    
    /**
     * Return possible values for filtering
     * @return array array('filter-value' => 'translated-filter-value')
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $aFilter = array();
            foreach (MLI18n::gi()->get('Productlist_Filter_aMarketplaceSync', array('marketplace' => MLModul::gi()->getMarketPlaceName(false))) as $sFilter => $sLabel) {
                $aSteps = array();
                if (in_array($sFilter, array('sync', 'expired'))) {
                    $aSteps = array('optimize', 'deleted', 'default');
                } elseif (in_array($sFilter, array('notTransferred', 'active', 'notActive'))) {
                    $aSteps = array('optimize', 'default');
                }
                foreach ($aSteps as $iStep => $sStep) {
                    if (MLCache::gi()->exists( strtoupper(get_class($this)).'__'.$sStep.'_'.MLModul::gi()->getMarketPlaceId().'.json')) {
                        unset($aSteps[$iStep]);
                    }
                }
                $aFilter[$sFilter] = array('value' => $sFilter, 'label'=> $sLabel, 'steps' => implode(',', $aSteps));
            }
            $this->aFilterValues = $aFilter;
        }
        return $this->aFilterValues;
    }
    
    /**
     * executes sync for prepare-table
     * @return $this
     */
    public function callAjax() {
        $aSteps = explode(',', MLRequest::gi()->get('marketplacesyncfilter'));
        $aSteps = is_array($aSteps) ? $aSteps : array();
        /* @var $oProgress ML_Core_Controller_Widget_ProgressBar */
        $oProgress = MLController::gi('widget_progressbar')->setId('marketplacesyncfilter');
        if (count($aSteps) > 0) {
            $sStep = current($aSteps);
            $sCacheName = strtoupper(get_class($this)).'__'.$sStep.'_'.MLModul::gi()->getMarketPlaceId().'.json';
            if (method_exists($this, 'step'.$sStep)) {
                if ($this->{'step'.$sStep}($oProgress, $sCacheName)) {
                    array_shift($aSteps);
                }
            } else {
                array_shift($aSteps);
            }
        }
        if (count($aSteps) == 0) {
            MLSetting::gi()->add('aAjax', array('success'=> true ));
            $oProgress->addLog('ready.');
        } else {
            $aParams = MLRequest::gi()->data();
            $aParams['marketplacesyncfilter'] = implode(',', $aSteps);
            MLSetting::gi()->add('aAjax', array('Next' => MLHttp::gi()->getUrl($aParams)));
        }
        $oProgress->render();
        return parent::callAjax();
    }
    
}