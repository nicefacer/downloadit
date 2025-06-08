<?php
/**
 * File ConnectionsModel.php
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

class Selling_ConnectionsModel extends AbstractModel
{

    public $presta_id;
    public $presta_attribute_id;
    public $language_id;
    public $ebay_id;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_product_connection";
        $this->identifier = "id";

        $this->fieldsRequired = array('presta_id', 'language_id', 'ebay_id');

        $this->fieldsSize = array();

        $this->fieldsValidate = array(
            'presta_id' => 'isInt',
            'language_id' => 'isInt',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'presta_id' => (int) ($this->presta_id),
            'presta_attribute_id' => (int) ($this->presta_attribute_id),
            'language_id' => (int) ($this->language_id),
            'ebay_id' => $this->ebay_id, // ebay id not fill to int
        );
    }

    /**
     * Save connection between PrestaShop product and eBay Item.
     * Such connection used for create Order into PrestaShop store.
     *
     * @param int $prestaId product id into PrestaShop
     * @param int $prestaAttrId PS product attribute id
     * @param int $prestaLanguageId language used for product
     * @param bigint $eBayId item number into eBay site
     */
    public static function appendNewConnection($prestaId, $prestaAttrId, $prestaLanguageId, $eBayId)
    {
        Db::getInstance()->autoExecute(_DB_PREFIX_ . 'prestabay_product_connection',
                array(
                    'presta_id' => (int) ($prestaId),
                    'presta_attribute_id' => (int) ($prestaAttrId),
                    'language_id' => (int) ($prestaLanguageId),
                    'ebay_id' => $eBayId,
                ),
                'INSERT');
    }

    /**
     * Try to find PrestaShop product assigned to eBay Product
     *
     * @param string $eBayId eBay item identificator
     * @param array $variationInfo information
     * @return array information about connected product or false when no found
     */
    public static function getPrestaConnectionByEbayId($eBayId, $variationInfo = null)
    {
        $normalConnectedResult = self::_getNormalPrestaConnectedByEbayId($eBayId);
        if (is_null($variationInfo) || !$normalConnectedResult) {
            // Not variation product or not connected to PrestaShop
//            if ($normalConnectedResult) {
//                $normalConnectedResult['attribute_id'] = null;
//            }
            return $normalConnectedResult;
        }
        $prestaShopProductId = $normalConnectedResult['presta_id'];
        $product = new Product($prestaShopProductId, $normalConnectedResult['language_id']);
        $variationList = VariationHelper::getProductCombinationList($product, $normalConnectedResult['language_id']);
        $resultOfSearch = VariationHelper::variationSearch($variationList, $variationInfo);
        if ($resultOfSearch !== false) {
            $normalConnectedResult['attribute_id'] = $variationList[$resultOfSearch]['id_product_attribute'];
        } else if ($normalConnectedResult['attribute_id'] <= 0) {
           $normalConnectedResult['attribute_id'] = null;

        }
        return $normalConnectedResult;

    }
    
    /**
     * Check that eBay product is connected to PrestaShop.
     * Check for normal product connection
     * @param string $eBayId eBay identify
     * @return array
     */
    protected static function _getNormalPrestaConnectedByEbayId($eBayId)
    {
        $result = Db::getInstance()->getRow('SELECT * FROM ' . _DB_PREFIX_ . 'prestabay_product_connection WHERE ebay_id = ' . $eBayId);
        if (isset($result['id']) && $result['id'] > 0) {
            return array('presta_id' => $result['presta_id'], 'attribute_id' => (int)$result['presta_attribute_id'], 'language_id' => $result['language_id']);
        } else {
            // Try find in table prestabay_selling_products (all active items)
            $result = DB::getInstance()->getRow('SELECT l.language as language_id, p.product_id as presta_id, p.product_id_attribute as presta_attribute_id FROM ' . _DB_PREFIX_ . 'prestabay_selling_products p
                                                        LEFT JOIN ' . _DB_PREFIX_ . 'prestabay_selling_list l on p.selling_id = l.id
                                                        WHERE p.ebay_id = '. $eBayId);
            if (isset($result['presta_id']) && $result['presta_id'] > 0) {
                return array('presta_id' => $result['presta_id'], 'attribute_id' => (int)$result['presta_attribute_id'], 'language_id' => $result['language_id']);
            }
        }
        return false;
    }

}