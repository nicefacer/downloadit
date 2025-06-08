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

MLFilesystem::gi()->loadClass('Shop_Model_ConfigForm_Shop_Abstract');

class ML_Shopware_Model_ConfigForm_Shop extends ML_Shop_Model_ConfigForm_Shop_Abstract {

    protected $sPlatformName = '';

    public function getDescriptionValues() {
        $aLangs = array();
        $shop = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->getBaseListQuery()->getArrayResult();
        #echo print_m($shop, '$shop');
        foreach ($shop as $aRow) {
            /*
             * Load language of locale
             */
            $builder = Shopware()->Models()->getRepository('Shopware\Models\Shop\Locale')->createQueryBuilder('Locale')->where('Locale.id = :localeId');
            $builder->setParameters(array(
                'localeId' => $aRow['localeId'],
            ));
            $locale = $builder->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);

            /*
             * Load main category of shop
             */
            $builder = Shopware()->Models()->getRepository('Shopware\Models\Category\Category')->createQueryBuilder('Category')->where('Category.id = :categoryId');
            $builder->setParameters(array(
                'categoryId' => $aRow['categoryId'],
            ));
            $mainCategory = $builder->getQuery()->getOneOrNullResult(\Doctrine\ORM\AbstractQuery::HYDRATE_ARRAY);
            #echo print_m($mainCategory, '$mainCategory');
            /*
             * Set languages for configuration
             */
            $aLangs[$aRow['id']] = $aRow['name'] . ' - ' . $locale['language'] . ' - ' . $mainCategory['name'];
        }
        return $aLangs;
    }
    
    public function getShopValues() {
        $aShops = array();
        $aShopData = Shopware()->Models()->getRepository('Shopware\Models\Shop\Shop')->getBaseListQuery()->getArrayResult();
        #echo print_m($shop, '$shop');
        foreach ($aShopData as $aRow) {
            $aShops[$aRow['id']] = $aRow['name'] ;
        }
        return $aShops;
    }


    public function getCustomerGroupValues($blNotLoggedIn = false) {
        $aGroupsName = array();
        $customerGroups = Shopware()->Models()->getRepository('Shopware\Models\Customer\Customer')->getCustomerGroupsQuery()->getArrayResult();
        foreach ($customerGroups as $aRow) {
            $aGroupsName[$aRow['id']] = $aRow['name'];
        }
        $oQueryBuilder = Shopware()->Models()->createQueryBuilder();
        if ($blNotLoggedIn) {
            $aRes = $oQueryBuilder
                        ->select('snippet.value')
                        ->from('Shopware\Models\Snippet\Snippet', 'snippet')
                        ->where("snippet.name = 'RegisterLabelNoAccount' AND snippet.namespace = 'frontend/register/personal_fieldset' And snippet.localeId = " . Shopware()->Shop()->getLocale()->getId())->getQuery()->getArrayResult();
            if (!empty($aRes)) {
                $aGroupsName['-'] = $aRes[0]['value'];
            } else {
                $aGroupsName['-'] = MLI18n::gi()->Shopware_Orderimport_CustomerGroup_Notloggedin;
            }
        }
        return $aGroupsName;
    }

    public function getOrderStatusValues() {
        $oQueryBuilder = Shopware()->Models()->createQueryBuilder();
        $aRes = $oQueryBuilder
                        ->select('snippet.name,snippet.value')
                        ->from('Shopware\Models\Snippet\Snippet', 'snippet')
                        ->where("snippet.namespace = 'backend/static/order_status' And snippet.localeId = " . Shopware()->Shop()->getLocale()->getId())->getQuery()->getArrayResult();
        $aStatusI18N = array();
        foreach ($aRes as $aRow) {
            $aStatusI18N[$aRow['name']] = $aRow['value'];
        }
        $orderStates = Shopware()->Models()->getRepository('Shopware\Models\Order\Order')->getOrderStatusQuery()->getArrayResult();

        $aOrderStatesName = array();
        foreach ($orderStates as $aRow) {
            $sI18NIndex = strtolower(str_replace(array(' / ', ' '), '_', $aRow['description']));
            $sI18NIndex = strtolower(str_replace(
                            array(
                'in_work',
                'canceled',
                'clarification_needed',
                'partial_delivered',
                'fully_completed',
                'delivered_completely'
                            ), array(
                'in_process',
                'cancelled',
                'clarification_required',
                'partially_delivered',
                'completed',
                'completely_delivered'
                            ), $sI18NIndex));

            $aOrderStatesName[$aRow['id']] = isset($aStatusI18N[$sI18NIndex]) ? $aStatusI18N[$sI18NIndex] : $aRow['description'];
        }
        $aCanceledStatus = $aOrderStatesName[-1];
        unset($aOrderStatesName[-1]);
        $aOrderStatesName[-1] = $aCanceledStatus;
        return $aOrderStatesName;
    }

    public function getPaymentStatusValues() {
        $oQueryBuilder = Shopware()->Models()->createQueryBuilder();
        $aRes = $oQueryBuilder
                        ->select('snippet.name,snippet.value')
                        ->from('Shopware\Models\Snippet\Snippet', 'snippet')
                        ->where("snippet.namespace = 'backend/static/payment_status' And snippet.localeId = " . Shopware()->Shop()->getLocale()->getId())->getQuery()->getArrayResult();
        $aStatusI18N = array();
        foreach ($aRes as $aRow) {
            $aStatusI18N[$aRow['name']] = $aRow['value'];
        }
        MLDatabase::getDbInstance()->setCharset(MLDatabase::getDbInstance()->tableEncoding('s_core_states'));
        $paymentStates = Shopware()->Db()->fetchAll("select id, description from `s_core_states` where `group` = 'payment' order By `position` ");
        $aPaymentStatesName = array();
        foreach ($paymentStates as $aRow) {
            $aPaymentStatesName[$aRow['id']] = $aRow['description'];
        }
        return $aPaymentStatesName;
    }

    public function getEan($pID) {
        return array(
            'ean' => 'EAN');
    }

    protected function getListOfArticleFields() {
        $aFields = array_merge(
            array('' => MLI18n::gi()->get('ConfigFormEmptySelect')),
            Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Article')->columnNames, 
            Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Detail')->columnNames
        );
        //remove some field that could not be used 
        unset($aFields['filterGroupId']);
        foreach ($aFields as $sKye => &$sFields) {
            if ($sKye !== 'articleId' && $sKye != 'Id' && substr($sKye, -2) == 'Id') {
                $sFields = str_replace('_',' ',substr($sFields, 0, -2).' Name');
            }
        }
        asort($aFields);
        return $aFields;
    }
    
    public function getManufacturerPartNumber() {
        return $this->getListOfArticleFields();
    }

    public function getManufacturer() {
        return $this->getListOfArticleFields();
    }

    public function getBrand() {
        return $this->getListOfArticleFields();
    }

    public function getCurrency() {
        $aCurrencyModel = Shopware()->Models()->getRepository('Shopware\Models\Shop\Currency')->createQueryBuilder('Currency')->getQuery()->getArrayResult();
        $aCurrency = array();
        foreach ($aCurrencyModel as $aCur) {
            $aCurrency[$aCur['id']] = $aCur['currency'];
        }
        return $aCurrency;
    }

    /**
     * Gets the list of product attributes prefixed with attribute type.
     *
     * @return array Collection of prefixed attributes
     */
    public function getPrefixedAttributeList() {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));

        $aConfiguratorGroups = Shopware()->Db()->fetchAll('select id, name from '.Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Configurator\Group')->getTableName());
        foreach ($aConfiguratorGroups as &$aConfiguratorGroup) {
                $aAttributes['c_' . $aConfiguratorGroup['id']] = $aConfiguratorGroup['name'];
        }

        $aOpenTextFields = MLDatabase::factorySelectClass()->select('name, label')->from(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Element')->getTableName())->getResult();
        foreach ($aOpenTextFields as $aOpenTextField) {
                $aAttributes['a_' . $aOpenTextField['name']] = $aOpenTextField['label'];
        }

        $aAttributes['p_articleName'] = 'Title';
        $aAttributes['pd_Number'] = 'Item number';
        $aAttributes['p_description'] = 'Short description';
        $aAttributes['p_description_long'] = 'Description';
        $aAttributes['pd_Ean'] = 'EAN';
        $aAttributes['pd_Weight'] = 'Weight';
        $aAttributes['pd_Width'] = 'Width';
        $aAttributes['pd_Height'] = 'Height';
        $aAttributes['pd_Len'] = 'Length';

        // NOTE: Properties are multivalue field and therefore are not added

        return $aAttributes;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     *
     * @return array Collection of attributes with options
     */
    public function getAttributeListWithOptions() {
        $aAttributes = array('' => MLI18n::gi()->get('ConfigFormEmptySelect'));
        $aConfiguratorGroups = Shopware()->Db()->fetchAll('select id, name from '.Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Configurator\Group')->getTableName());;
        foreach ($aConfiguratorGroups as $aConfiguratorGroup) {
                $aAttributes['c_' . $aConfiguratorGroup['id']] = mb_convert_encoding($aConfiguratorGroup['name'], 'HTML-ENTITIES');
        }

        // NOTE: Properties are multivalue field and therefore are not added

        return $aAttributes;
    }

    /**
     * Gets the list of product attributes that have options (displayed as dropdown or multiselect fields).
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attributes with options
     */
    public function getAttributeOptions($sAttributeCode, $iLangId = null) {
		$aAttributeCode = explode('_', $sAttributeCode, 2);
		$attributes = array();

		if ($aAttributeCode[0] === 'c') {
			$configuratorOptions = MLDatabase::factorySelectClass()->select('id, name')->from(Shopware()->Models()->getClassMetadata('Shopware\Models\Article\Configurator\Option')->getTableName())->where("group_id = $aAttributeCode[1]")->getResult();
			foreach ($configuratorOptions as &$configuratorOption) {
				$attributes[$configuratorOption['id']] = $configuratorOption['name'];
			}
		}

        return $attributes;
    }

	/**
     * Gets the list of product attribute values.
     * If $iLangId is set, use translation for attribute options' labels.
     *
     * @return array Collection of attribute values
     */
    public function getPrefixedAttributeOptions($sAttributeCode, $iLangId = null) {
		return $this->getAttributeOptions($sAttributeCode, $iLangId);
	}

    public function getTaxClasses() {
        $oQueryBuilder = Shopware()->Models()->createQueryBuilder();
        $aTaxes = $oQueryBuilder
                        ->select('tax.id as value , tax.name as label')
                        ->from('Shopware\Models\Tax\Tax', 'tax')->getQuery()->getArrayResult();
        return $aTaxes;
    }

    public function getPaymentMethodValues(){
        $oBuilder = Shopware()->Models()->createQueryBuilder()
        ->from('Shopware\Models\Payment\Payment', 'p');
        $oBuilder->select(
                array(
                    'p.id as id',
                    'p.description as description',
                )
        );
//        $oBuilder->where('p.active = 1');
        $aPayments = $oBuilder->getQuery()->getArrayResult();
        $aResult = array();
        foreach ($aPayments as $aPayment) {
            $aResult[$aPayment['id']] = $aPayment['description'];
        }
        return $aResult;
    }
    
    public function getShippingMethodValues(){
        $oBuilder = Shopware()->Models()->createQueryBuilder()
                ->from('Shopware\Models\Dispatch\Dispatch', 'dispatches');
        $oBuilder->select(array(
            'id' => 'dispatches.id',
            'name' => 'dispatches.name',
        ));
//        $oBuilder->where('dispatches.active = 1');
        $aDispatchs = $oBuilder->getQuery()->getArrayResult();
        $aResult = array();
        foreach ($aDispatchs as $aDispatch) {
            $aResult[$aDispatch['id']] = $aDispatch['name'];
        }
        return $aResult;
    }
    
    public function getPossibleVariationGroupNames () {
        return $this->getAttributeListWithOptions();
    }
    
}