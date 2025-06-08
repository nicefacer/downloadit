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
class ML_Shopware_Model_ProductListDependency_CategoryFilter extends ML_Shop_Model_ProductListDependency_CategoryFilter_Abstract {
    
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
    public function manipulateQuery ($mQuery) {
        $sFilterValue = (int)$this->getFilterValue();
        if (
            !empty($sFilterValue) 
            && $sFilterValue !== 1 //root-category
            && array_key_exists($sFilterValue, $this->getFilterValues())
        ) {
            $mQuery
                ->join(array('s_categories', 'c', "c.id = $sFilterValue AND c.active = 1"), ML_Database_Model_Query_Select::JOIN_TYPE_LEFT)
                ->join(array(MLDatabase::getDbInstance()->tableExists('s_articles_categories_ro') ? 's_articles_categories_ro' : 's_articles_categories', 'pc', 'pc.articleID  = p.id AND pc.categoryID = c.id'), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
            ;
        }
    }
    
    /**
     * key=>value for categories
     * @return array
     */
    protected function getFilterValues () {
        if ($this->aFilterValues === null) { 
            $aCats = array(array(
                'value' => '',
                'label' => sprintf(MLI18n::gi()->get('Productlist_Filter_sEmpty'), MLI18n::gi()->get('Shopware_Productlist_Filter_sCategory')),
            ));
            foreach($this->getShopwareCategories() as $aValue){                
                 $aCats[$aValue['value']] = $aValue;
            }
            $this->aFilterValues = $aCats;
        }
        return $this->aFilterValues;
    }
    
    /**
     * gets all categories
     * @param array|null $aCats nested cats
     * @return array
     */
    protected function getShopwareCategories ($iParentId = null) {
        $aCats = $this->getShopwareCategoryByParentId($iParentId === null ? 1 : $iParentId) ;
        foreach ($aCats as $aCat) {
            $this->aCategories[$aCat['id']] = array(
                'value' => $aCat['id'],
                'label' => str_repeat('&nbsp;', substr_count($aCat['path'], '|') * 2) . $aCat['name'],
            );
            $this->getShopwareCategories($aCat['id']);
        }
        if($iParentId === null){
            return $this->aCategories;
        } else {
            return;
        }
    }
    protected function getShopwareCategoryByParentId ($iParentId) {
        $builder = Shopware()->Models()->createQueryBuilder();
        $builder->from('Shopware\Models\Category\Category', 'category')
                ->select(array(
                    'category',
                ))
                ->andWhere('category.parent = :parent')
                ->setParameter('parent', $iParentId)
                ->addOrderBy('category.position');
        return $builder->getQuery()->getArrayresult();
    }
    
}
