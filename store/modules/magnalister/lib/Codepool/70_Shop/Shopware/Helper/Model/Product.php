<?php

class ML_Shopware_Helper_Model_Product {

    public function getProductSelectQuery() {
        return MLDatabase::factorySelectClass() 
                ->select('DISTINCT p.id')
                ->from(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Article')->getTableName(),'p')
                ->join(array(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Detail')->getTableName(), 'details','p.id = details.articleid AND p.main_detail_id = details.id '), ML_Database_Model_Query_Select::JOIN_TYPE_INNER)
                ->join(array(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Price')->getTableName(), 'pp','pp.articledetailsID = details.id'),ML_Database_Model_Query_Select::JOIN_TYPE_INNER);
               
    }
    
    /**
     * Get name from a certain article by ordernumber
     * @param string $ordernumber
     * @param bool $returnAll return only name or additional data, too
     * @access public
     * @return string or array
     */
    public function getTranslatedInfo($ordernumber)
    {
        $checkForArticle = Shopware()->Db()->fetchRow("
            SELECT s_articles.id, s_articles.name AS articleName, description ,description_long,keywords  FROM s_articles WHERE
            id=?
		", array($ordernumber));
        if (!empty($checkForArticle)) {
            return Shopware()->Modules()->Articles()->sGetTranslation($checkForArticle, $checkForArticle["id"], "article");
        } else {
            return false;
        }
    }
    
    /**
     * use sConfigurator::getDefaultPrices
     * @param type $detailId
     * @return type
     */
    public function getDefaultPrices($detailId, $sUserGroup = null) {
        if ($sUserGroup === null) {
            $sUserGroup = Shopware()->System()->sUSERGROUPDATA['key'];
        }
        $oBbuilder = Shopware()->Models()->createQueryBuilder();
        $aPriceRows = $oBbuilder->select(array('prices'))
                ->from('Shopware\Models\Article\Price', 'prices')
                ->where('prices.articleDetailsId = :detailId')
                ->andWhere('prices.customerGroupKey = :key')
                ->setParameter('detailId', $detailId)
                ->setParameter('key', $sUserGroup)
                ->orderBy('prices.from', 'ASC')
                ->getQuery()
                ->getArrayResult();
        
        if(count($aPriceRows) <= 0 && $sUserGroup == 'EK'){
            throw new Exception('Error to get Price : there is no Price for this product. Detail id = '.$detailId);
        }
        $aPrice = count($aPriceRows) > 0 ? array_shift($aPriceRows) : array('price' => $this->getDefaultPrices($detailId, 'EK'));
        return $aPrice['price'];
    }
    
    /**
     * return all variants related to this product id
     * @param int $iProductId
     * @return array
     */
    public function getProductDetails($iProductId){
        $oShopwareProduct = Shopware()->Models()->getRepository('Shopware\Models\Article\Article')->find($iProductId);
        /* @var $oShopwareProduct Shopware\Models\Article\Article */
        $oQueryBuilder = Shopware()->Models()->createQueryBuilder();
        $oQueryBuilder->select(array('details', 'attribute', 'prices', 'configuratorOptions', 'configuratorGroup'))->distinct('details.id')
                ->from('Shopware\Models\Article\Detail', 'details')
                ->leftJoin('details.configuratorOptions', 'configuratorOptions')
                ->leftJoin('configuratorOptions.group', 'configuratorGroup')
                ->leftJoin('details.prices', 'prices')
                ->leftJoin('details.attribute', 'attribute')
                ;
        
        $mConfiguratorSet = $oShopwareProduct->getConfiguratorSet();
        if(empty($mConfiguratorSet)){
            $oQueryBuilder->where('details.articleId = ?1 AND details.id = ?2 ')
                    ->setParameter(2, $oShopwareProduct->getMainDetail()->getId());
        } else {
            $oQueryBuilder->where('details.articleId = ?1 ');
        }
        $oQueryBuilder->setParameter(1, $iProductId);
        return $oQueryBuilder->getQuery()->getArrayResult();
    }
    
    public function getProperties($iArticleId, $iPropertyGroupId) {
        $aProperties = array();
        $oBuilder = Shopware()->Models()->createQueryBuilder()
                ->from('Shopware\Models\Property\Option', 'po')
                ->join('po.groups', 'pg', 'with', 'pg.id = :propertyGroupId')
                ->setParameter('propertyGroupId', $iPropertyGroupId)
                ->select(array('PARTIAL po.{id,name}'));
        $aOptions = array();

        foreach ($oBuilder->getQuery()->getArrayResult() as $option) {
            $option['name'] = $this->translate($option['id'], 'propertyoption', 'optionName', $option['name']);
            $aOptions[$option['id']] = $option;
        }

        $oBuilder = Shopware()->Models()->createQueryBuilder()
                ->from('Shopware\Models\Property\Value', 'pv')
                ->join('pv.articles', 'pa', 'with', 'pa.id = :articleId')
                ->setParameter('articleId', $iArticleId)
                ->join('pv.option', 'po')
                ->select(array('po.id as optionId', 'pv.id', 'pv.value'));

        $aValues = $oBuilder->getQuery()->getArrayResult();

        foreach ($aValues as $value) {
            $optionId = $value['optionId'];
            if (isset($aOptions[$optionId])) {
                $sName = $aOptions[$optionId]['name'];
                $value['value'] = $this->translate($value['id'], 'propertyvalue', 'optionValue', $value['value']);
                $aProperties[$sName][] = $value['value'];
            }
        }
        return $aProperties;
    }

    public function translate($iId, $sType, $sTranlationIndex, $sFallback){
        $translationWriter = new \Shopware_Components_Translation();
        $iLanguage = Shopware()->Shop()->getId();
        $aTranslate = $translationWriter->read($iLanguage, $sType, $iId);
        $sTranslate = '';
        if (empty($aTranslate) || !isset($aTranslate[$sTranlationIndex])) {
            $sTranslate = $sFallback;
        } else {
            $sTranslate = $aTranslate[$sTranlationIndex];
        }
        return $sTranslate;
    }

}