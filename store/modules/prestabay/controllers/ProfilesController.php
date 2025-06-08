<?php

/**
 * File ProfilesController.php
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * It is available through the world-wide-web at this URL:
 * http://involic.com/license.txt
 * If you are unable to obtain it through the world-wide-web,
 * please send an email to license@involic.com so
 * we can send you a copy immediately.
 *
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class ProfilesController extends BaseAdminController
{

    public function indexAction()
    {
        $myGrid = new Grids_Profiles();
        $myGrid->getHtml();
    }

    public function newAction()
    {
        // Forward to edit action
        $this->editAction();
    }

    public function deleteAction()
    {
        $id = UrlHelper::getGet("id", null);
        if (is_null($id)) {
            RenderHelper::addError(L::t("Please Specify 'Selling Profile' Id"));
            UrlHelper::redirect("profiles/index");
            return;
        }
        $model = new ProfilesModel();
        if (!$model->deleteProfileWithCheck($id)) {
            RenderHelper::addError(L::t("Can't delete 'Selling Profile'. Please delete connected 'Selling List' first."));
        } else {
            RenderHelper::addSuccess(L::t("'Selling Profile' successfuly deleted"));
        }

        UrlHelper::redirect("profiles/index");
    }

    public function editAction()
    {
        // Get profile ID from request
        // load profile information
        // load connected marketplace, category information
        // assign loaded information to template

        $accountsModel = new AccountsModel();
        $marketplacesModel = new MarketplacesModel();

        $isEdit = false;

        $id = UrlHelper::getGet("id", 0);
        if ($id > 0) {
            $model = new ProfilesModel($id);
        } else {
            $model = new ProfilesModel();
        }

        $loadedResults = array();
        if ($model->id) {
            $isEdit = true;

            $siteId = $model->ebay_site;
            $primaryCategoryId = $model->ebay_primary_category_value;

            $marketplaces = new MarketplacesModel($siteId);
            $categoryList = new ImportCategoriesModel();
            $importShipping = new ImportShippingModel();

            $excludeListValues = json_decode($this->_decodeJsonUtf8($marketplaces->shipping_exclude_location), true);

            $isAllowedCalculated = in_array($siteId, array(1,2,100,210,15));
            $isLocalCalculated = $isAllowedCalculated && $model->shipping_local_type == 1;
            $isIntCalculated = $isAllowedCalculated && $model->shipping_int_type == 1;
            $loadedResults = array(
                'dispatch' => json_decode($this->_decodeJsonUtf8($marketplaces->dispatch), true),
                'policy' => json_decode($this->_decodeJsonUtf8($marketplaces->policy), true),
                'payment_methods' => json_decode($this->_decodeJsonUtf8($marketplaces->payment_methods), true),
                'local_shippings' => $importShipping->getLocalShippingMethods($siteId, $isLocalCalculated),
                'international_shippings' => $importShipping->getInternationalShippingMethods($siteId, $isIntCalculated),
                'location_shipping' => json_decode($this->_decodeJsonUtf8($marketplaces->shipping_location), true),
                'account_store_info' => AccountStoreHelper::getAccountStoreInformation($model->ebay_account),
                'shipping_discount_profiles' => AccountStoreHelper::getAccountShippingDiscountProfiles($model->ebay_account),
                'marketplace_main_category' => $categoryList->getMarketplaceMainCategories($siteId),
                'exclude_location' => HtmlHelper::dropDownListWithGroup('shippingIntExclude',
                        '',
                        $excludeListValues,
                        array('groupKey' => 'region', 'addSelect' => true)),
                'exclude_location_values' => ProfilesHelper::reformatKeyLabel($excludeListValues),
                'shipping_packages' => json_decode($this->_decodeJsonUtf8($marketplaces->shipping_packages), true),
                'ebay_category_mapping_options' => Mapping_CategoryModel::getMarketplaceMappingList($siteId),
            );

            ApiModel::getInstance()->reset();
            $response = ApiModel::getInstance()->ebay->category->getDetails(array('categoryId' => $primaryCategoryId, 'marketplaceId' => $siteId))->post();
            if (!isset($response['conditions'])) {
                $response['conditions'] = array();
            }
            if (!isset($response['specifics'])) {
                $response['specifics'] = array();
            }
            if (!isset($response['attribute_specifics']['list'])) {
                $response['attribute_specifics'] = array();
                $response['attribute_specifics_id'] = 0;
            } else {
                $response['attribute_set_id'] = $response['attribute_specifics']['attribute_set_id'];
                $response['attribute_specifics'] = $response['attribute_specifics']['list'];
            }

            $loadedResults = $loadedResults + $response;
        }



        $params = array(
                'accounts' => $accountsModel->getSelect()->getItems(),
                'marketplaces' => $marketplacesModel->filterOnlyActive()->getItems(), // ->filterOnlyActive()->getItems(),
            ) + $loadedResults;



        $profilesRefl = new ReflectionClass('ProfilesModel');
        $settingsList = $profilesRefl->getConstants();
        $settingsList += array(
            'marketplaceAjaxInfoUrl' => UrlHelper::getUrl('profiles/getMarketplaceInfo'),
            'childCategoryAjaxInfoUrl' => UrlHelper::getUrl('profiles/getChildCategory'),
            'categoryOptionsAjaxInfoUrl' => UrlHelper::getUrl('profiles/getCategoryOptions'),
            'categoryMarketplaceMainAjaxInfoUrl' => UrlHelper::getUrl('profiles/getMarketplaceMainCategories'),
            'accountStoreInfoUrl' => UrlHelper::getUrl('profiles/getAccountInformation'),
            'shippingListByModeUrl' => UrlHelper::getUrl('profiles/getShippingByMode')
        );

        $this->view("profiles/edit.phtml", array('hdbk' => $params, 'isEdit' => $isEdit, 'model' => $model, 'jsSettingsList' => json_encode($settingsList)));
    }

    public function saveAction()
    {
        // @todo validation and additional checking
        $postValues = $_POST;

        $profileId = null;
        if (isset($postValues['profileId']) && $postValues['profileId'] > 0) {
            $profileId = $postValues['profileId'];
        }

        $profilesModel = new ProfilesModel($profileId);

        $keysForDb = array_keys($profilesModel->getFieldsWihoutValidation());
        $filteredValues = array();

        foreach ($keysForDb as $dbKey) {
            if (isset($postValues[$dbKey])) {
                $filteredValues[$dbKey] = $postValues[$dbKey];
            } else {
                $filteredValues[$dbKey] = null;
            }
        }

        // Listing Enh setters
        !isset($postValues['enhancement']) && $postValues['enhancement'] = array();
        $filteredValues['enhancement'] = json_encode($postValues['enhancement']);

        // Gift services
        !isset($postValues['gift_services']) && $postValues['gift_services'] = array();
        if (!isset($postValues['gift_icon']) || $postValues['gift_icon'] == ProfilesModel::GIFT_ICON_NO) {
            $postValues['gift_services'] = array();
            $postValues['gift_icon'] = ProfilesModel::GIFT_ICON_NO;
        }

        $filteredValues['gift_services'] = json_encode($postValues['gift_services']);

        // Get Price Values
        $priceAttributesList = array('start', 'reserve', 'buynow');
        foreach ($priceAttributesList as $priceAttributeName) {
            $priceValue = explode("-", $postValues['price_' . $priceAttributeName]);
            unset($postValues['price_' . $priceAttributeName]);
            $filteredValues['price_' . $priceAttributeName] = $priceValue[0];
            if (count($priceValue) > 1) {
                $filteredValues['price_' . $priceAttributeName] = ProfilesModel::PRICE_MODE_TEMPLATE;
                $filteredValues['price_' . $priceAttributeName . '_template'] = $priceValue[1];
            }
        }
        // Get Description Values
        if (strpos($filteredValues['item_description_mode'], 'd-') === 0) {
            // This is custom Description Template
            $filteredValues['description_template_id'] = substr($filteredValues['item_description_mode'], 2);
            $filteredValues['item_description_mode'] = ProfilesModel::ITEM_DESCRIPTION_MODE_TEMPLATE;
        }

        // Get Description Values
        if (strpos($filteredValues['ebay_category_mode'], 'd-') === 0) {
            // This is custom Description Template
            $filteredValues['ebay_category_mapping_id'] = substr($filteredValues['ebay_category_mode'], 2);
            $filteredValues['ebay_category_mode'] = ProfilesModel::EBAY_CATEGORY_MODE_MAPPING;
        }

        // Get Store Mapping Values
        if (strpos($filteredValues['ebay_store_mode'], 'd-') === 0) {
            // This is custom Description Template
            $filteredValues['ebay_store_mapping_id'] = substr($filteredValues['ebay_store_mode'], 2);
            $filteredValues['ebay_store_mode'] = ProfilesModel::EBAY_STORE_MODE_MAPPING;
        }

        $filteredValues['payment_methods'] = serialize($postValues['paymentBox']);

        // Process shipping list
        // Get local shipping from post and reset keys
        $localShipping = array_values(isset($postValues['shippingList']) ? $postValues['shippingList'] : array());
        foreach ($localShipping as $shippingKey => $shippingValue) {
            if ($shippingValue['name'] == '') {
                unset($localShipping[$shippingKey]);
                continue;
            }
        }
        $filteredValues['shipping_local'] = serialize($localShipping);

        $intShipping = array_values(isset($postValues['shippingIntList']) ? $postValues['shippingIntList'] : array());
        foreach ($intShipping as $intShippingKey => $intShippingValue) {
            if ($intShippingValue['name'] == '') {
                unset($intShipping[$intShippingKey]);
                continue;
            }
        }
        $filteredValues['shipping_int'] = serialize($intShipping);

        $filteredValues['shipping_exclude_location'] = serialize(array_unique(isset($postValues['shippingExcludeLocations']) ? $postValues['shippingExcludeLocations'] : array()));
        $filteredValues['shipping_allowed_location'] = serialize(array_unique(isset($postValues['shipping_allowed_location']) ? $postValues['shipping_allowed_location'] : array()));

        $filteredValues['product_specifics'] = serialize(isset($postValues['product_specifics']) ? $postValues['product_specifics'] : array());
        $filteredValues['product_specifics_attribute'] = serialize(array());
        $filteredValues['product_specifics_custom'] = serialize(isset($postValues['product_specifics_custom']) ? $postValues['product_specifics_custom'] : array());
        $profilesModel->setData($filteredValues);
        try {
            $result = $profilesModel->save();
        } catch (Exception $ex) {
            $result = false;
        }
        if ($result !== false) {
            RenderHelper::addSuccess(L::t("Profile information Saved"));
        } else {
            RenderHelper::addError(L::t("Can't save Profile"));
            RenderHelper::addError(Db::getInstance()->getMsgError());
            UrlHelper::redirect("profiles/index");
            return;
        }
        if (isset($postValues['save-and-continue'])) {
            UrlHelper::redirect("profiles/edit", array('id' => $profilesModel->id));
        } else {
            UrlHelper::redirect("profiles/index");
        }
    }

    // ###############################################################################3
    // Ajax actions

    /**
     * Return general information about marketplace
     * Such as: dispatch times, return policy, payments methods, payments categories,
     * shippings list
     *
     * @return <type>
     */
    public function getMarketplaceInfoAction()
    {
        RenderHelper::cleanOutput();

        if (($id = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }

        $marketplaces = new MarketplacesModel($id);
        $categoryList = new ImportCategoriesModel();

        $importShipping = new ImportShippingModel();


        $groupKeyCategoryMapping = L::t("Ebay Category Mappings");

        $mappingCategoriesProfiles = Mapping_CategoryModel::getMarketplaceMappingList($id);
        foreach ($mappingCategoriesProfiles as &$singleMapping) {
            $singleMapping['id'] = 'd-'.$singleMapping['id'];
            $singleMapping['group'] = $groupKeyCategoryMapping;
        }
        $categoryMappingSelectOptions[] =  array('id' => ProfilesModel::EBAY_CATEGORY_MODE_PROFILE, 'label' => L::t("Selling Profile"));
        foreach ($mappingCategoriesProfiles as $singleProfile) {
            array_push($categoryMappingSelectOptions, $singleProfile);
        }
//        array_push($categoryMappingSelectOptions, $mappingCategoriesProfiles);
//        + $mappingCategoriesProfiles;

        $result = array(
            'success' => true,
            'data' => array(
                'dispatch' => json_decode($this->_decodeJsonUtf8($marketplaces->dispatch)),
                'policy' => json_decode($this->_decodeJsonUtf8($marketplaces->policy)),
                'payment_methods' => json_decode($this->_decodeJsonUtf8($marketplaces->payment_methods)),
                'parent_categories' => $categoryList->getMarketplaceMainCategories($id),
                'local_shippings' => $importShipping->getLocalShippingMethods($id),
                'international_shippings' => $importShipping->getInternationalShippingMethods($id),
                'location_shipping' => json_decode($this->_decodeJsonUtf8($marketplaces->shipping_location)),
                'shipping_packages' => json_decode($this->_decodeJsonUtf8($marketplaces->shipping_packages), true),
                'exclude_location' => HtmlHelper::dropDownListWithGroup('shippingIntExclude',
                        '',
                        json_decode($this->_decodeJsonUtf8($marketplaces->shipping_exclude_location), true),
                        array('groupKey' => 'region', 'addSelect' => true)),
                'categoryMappingHtml' => HtmlHelper::dropDownListWithGroup('ebay_category_mode', '',
                            $categoryMappingSelectOptions
                        , array('addSelect' => true, 'onlyElements' => true ))
            )
        );
        echo $this->_decodeJsonUtf8(json_encode($result));
        return;
    }

    /**
     * Get list of categories connected to specific marketplace
     * @return string json encoded array with list of categories
     */
    public function categoryListAction()
    {
        RenderHelper::cleanOutput();
        if (($marketplaceId = UrlHelper::getGet("marketplaceId", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }

        $parentCategoryId = UrlHelper::getGet("parentCategoryId", 0);

        $categoryList = new ImportCategoriesModel();

        $childCategoryList = $categoryList->getChildCategories($marketplaceId, (int)$parentCategoryId);
        $data = array(
            'categories' => $childCategoryList,
            'is_latest' => false
        );

        if ($data['categories'] == array()) {
            $data['is_latest'] = true;
        }

        echo json_encode(array(
                'success' => true,
                'data' => $data
        ));

        return;
    }


    public function getMarketplaceMainCategoriesAction()
    {
        RenderHelper::cleanOutput();
        if (($id = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }
        $categoryList = new ImportCategoriesModel();
        $result = array(
            'success' => true,
            'data' => array(
                'parent_categories' => $categoryList->getMarketplaceMainCategories($id)
            )
        );

        echo json_encode($result);
        return;
    }

    public function getChildCategoryAction()
    {
        RenderHelper::cleanOutput();

        if (($id = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Parent Category Id")));
            return;
        } else if (($marketplaceId = UrlHelper::getPost("marketplaceId", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }

        $categoryList = new ImportCategoriesModel();

        $childCategoryList = $categoryList->getChildCategories($marketplaceId, $id);
        $data = array(
            'categories' => $childCategoryList,
            'is_latest' => false
        );

        if ($data['categories'] == array()) {
            $data['is_latest'] = true;
        }

        echo json_encode(array(
                'success' => true,
                'data' => $data
            ));
    }

    public function getCategoryOptionsAction()
    {
        // @todo Add cache of retrive category information!
        RenderHelper::cleanOutput();
        if (($categoryId = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Category Id")));
            return;
        }

        if (($marketplaceId = UrlHelper::getPost("marketplaceId", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }

        $isAngular = UrlHelper::getPost("angular", false);


        ApiModel::getInstance()->reset();
        $response = ApiModel::getInstance()->ebay->category->getDetails(array('categoryId' => $categoryId, 'marketplaceId' => $marketplaceId))->post();
        if (!isset($response['specifics'])) {
            $response['specifics'] = array();
        }

        $model = new ProfilesModel();
        $response['specifics_html'] = $this->view("profiles/edit/specifics/specifics-list.phtml", array(
                'specificsList' => $response['specifics'],
                'model' => $model,
                'isAngular' => $isAngular
            ), false);
        unset($response['specifics']);

         $response['conditions'][] = array('id' => ProfilesModel::ITEM_CONDITION_PRODUCT_DATA, 'label' => L::t('PrestaShop product value'));

        // Correctly put all specific html into UTF-8 format
        $specificHtml = $response['specifics_html'];
        $specificHtml =	iconv(mb_detect_encoding($specificHtml, mb_detect_order(), true), "UTF-8//IGNORE", $specificHtml);
        $response['specifics_html'] = $specificHtml;

        echo $this->_decodeJsonUtf8(json_encode(array(
                'success' => true,
                'data' => $response
            )));
        return;
    }

    public function getAccountInformationAction()
    {
        RenderHelper::cleanOutput();
        if (($accountId = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please select eBay Account")));
            return;
        }
        $storeInformation = AccountStoreHelper::getAccountStoreInformation($accountId);
        if ($storeInformation == AccountStoreHelper::STORE_NOT_AVAILABLE) {
            echo json_encode(array('success' => true, 'no_store' => true));
            return;
        }

        $discountProfiles = AccountStoreHelper::getAccountShippingDiscountProfiles($accountId);

        $returnInformation = array(
            'success' => true,
            'no_store' => false,
            'name' => $storeInformation['name'],
            'url' => $storeInformation['url'],
            'subscription' => $storeInformation['subscription'],
            'categoryOptionsHtml' => AccountStoreHelper::getCategoryAsOptions($accountId, true),
            'discountProfiles' => $discountProfiles
        );
        echo json_encode($returnInformation);
        return;
    }

    public function getShippingByModeAction()
    {
        RenderHelper::cleanOutput();
        if (($marketplaceId = UrlHelper::getPost("id", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Marketplace Id")));
            return;
        }
        if (($value = UrlHelper::getPost("value", null)) == null) {
            echo json_encode(array('success' => false, 'message' => L::t("Please specify Value")));
            return;
        }

        $importShipping = new ImportShippingModel();

        $returnInformation = array(
            'success' => true,
            'local_shippings' => $importShipping->getLocalShippingMethods($marketplaceId, $value),
            'international_shippings' => $importShipping->getInternationalShippingMethods($marketplaceId, $value),
        );
        echo json_encode($returnInformation);
        return;
    }
//
//    protected function _decodeJsonUtf8($string)
//    {
//        $return = preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
//            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
//        }, $string);
//        return $return; //preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V',hexdec('U$1')))", $string);
//    }

    protected function _decodeJsonUtf8($string)
    {
        if (version_compare(phpversion(), '5.5.0', '>=')) {
            return preg_replace_callback("/\\\\u([a-f0-9]{4})/", function ($m) {
                return iconv('UCS-4LE', 'UTF-8', pack('V', hexdec('U' . $m[1])));
            }, $string);
        } else {
            return preg_replace("/\\\\u([a-f0-9]{4})/e", "iconv('UCS-4LE','UTF-8',pack('V',hexdec('U$1')))", $string);
        }

    }
}
