<?php

/**
 * File OptionsModel.php
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

/**
 * Information related to variation data for Selling Product.
 * Variation option data.
 */
class Selling_Variations_OptionsModel extends AbstractModel
{

    public $variation_id;
    public $key;
    public $value;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_selling_variations_options";
        $this->identifier = "id";

        $this->fieldsRequired = array('variation_id', 'key', 'value');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'variation_id' => (int) $this->variation_id,
            'key' => pSQL($this->key),
            'value' => pSQL($this->value),
        );
    }

    public function insertOptions($variationId, $optionList)
    {
        $db = Db::getInstance();
        foreach ($optionList as $optionKey => $optionValue) {
            $result = $db->autoExecute(_DB_PREFIX_ . $this->table, array(
                'variation_id' => (int)$variationId,
                'key' => pSQL($optionKey),
                'value' => pSQL($optionValue)), 'INSERT');
            if (!$result) {
                return false;
            }
        }
        return true;
    }

    /**
     * Return all options connected to single variation
     * @param int $sellingVariationId
     */
    public function deleteOptions($sellingVariationId)
    {
        $sqlToDelete = "DELETE FROM " . _DB_PREFIX_ . $this->table . "
                        WHERE variation_id = " . $sellingVariationId;

        return Db::getInstance()->Execute($sqlToDelete);
    }

}