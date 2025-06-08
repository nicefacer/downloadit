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
MLFilesystem::gi()->loadClass('Shop_Model_ProductListDependency_CategoryFilter_Abstract');
class ML_Prestashop_Model_ProductListDependency_CategoryFilter extends ML_Shop_Model_ProductListDependency_CategoryFilter_Abstract {
    
    /**
     * key=>value for filtering (eg. validation and form-select)
     * @var array|null
     */
    protected $aFilterValues = null;
    
    /**
     * all categories
     * @var array|null
     */
    protected $aCategories = null;


    /**
     * @param ML_Database_Model_Query_Select $mQuery
     * @return void
     */
    public function manipulateQuery($mQuery) {
        $sFilterValue = (int)$this->getFilterValue();
        if (
            !empty($sFilterValue) 
            && $sFilterValue !== 1 //root-category
            && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $aCats = $this->getPrestaCategories();
            $mQuery
                ->where('
                    (
                        c.nleft >= '.$aCats[$sFilterValue]['nleft'].'
                        AND c.nright <= '.$aCats[$sFilterValue]['nright']. '
                    )
                    || cp.id_category='.$sFilterValue// filter exact match for corrupted nested sets, to get min. selected category
                )
                ->join(array(
                    _DB_PREFIX_ . 'category_product' , 'cp' , 'p.`id_product` = cp.`id_product`'
                ), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                ->join(array(//nested set
                    _DB_PREFIX_ . 'category' , 'c' , 'cp.`id_category` = c.`id_category`'
                ), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
            ;
        }
    }
    
    protected function getPrestaCategories ($iCategoryId = 1, $iLanguageId = false, $iShopId = false) {
        if ($this->aCategories === null || $iCategoryId != 1) {
            $iLanguageId = $iLanguageId ? (int) $iLanguageId : (int) Context::getContext()->language->id;
            try{
                $iShopId = MLModul::gi()->getConfig('orderimport.shop');
                Shop::setContext(Shop::CONTEXT_SHOP, $iShopId);
            } catch (Exception $ex) {

            }
            $oCategory = new Category((int) $iCategoryId, (int) $iLanguageId, (int) $iShopId);
            if (is_null($oCategory->id)) {
                return;
            }
            $aChildrens = Category::getChildren((int) $iCategoryId, (int) $iLanguageId, false, (int) $iShopId);
            $oShop = (object) Shop::getShop((int) $oCategory->getShopID());
            $this->aCategories[(string)$oCategory->id] = array(
                'value' => $oCategory->id,
                'label' =>  str_repeat('&nbsp;', $oCategory->level_depth * 5).$oCategory->name. ' (' . $oShop->name . ')',
                'nleft' => $oCategory->nleft,
                'nright' => $oCategory->nright,
            );
            if (isset($aChildrens) && count($aChildrens)){
                foreach ($aChildrens as $aChildren) {
                    $this->getPrestaCategories((int) $aChildren['id_category'], (int) $iLanguageId, (int) $aChildren['id_shop']);
                }
            }
        }
        return $this->aCategories;
    }

    /**
     * key=>value for categories
     * @return array
     */
    protected function getFilterValues() {
        if ($this->aFilterValues === null) {
            $aCats = array();
            foreach ($this->getPrestaCategories() as $aCat) {
                $aCats[$aCat['value']] = array(
                    'value' => $aCat['value'],
                    'label' => $aCat['label'],
                );
            }
            $this->aFilterValues = $aCats;
        }
        return $this->aFilterValues;
    }

}
