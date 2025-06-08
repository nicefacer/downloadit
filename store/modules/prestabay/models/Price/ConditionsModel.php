<?php
/**
 * File ConditionsModel.php
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

class Price_ConditionsModel extends AbstractModel
{
    const CONDITION_TYPE_ANY = 0;
    const CONDITION_TYPE_RANGE = 1;
    const CONDITION_SOURCE_PRODUCT = 1;
    const CONDITION_SOURCE_CUSTOM = 2;

    const TYPE_SOURCE_PRICE = 0;
    const TYPE_SOURCE_WEIGHT = 1;

    public $price_id;
    public $type;
    public $price_from;
    public $price_to;
    public $price_source;
    public $price_custom_value;
    public $price_ratio;
    public $source;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_template_price_conditions";
        $this->identifier = "id";

        $this->fieldsRequired = array('price_id');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'price_id' => (int) ($this->price_id),
            'type' => (int) ($this->type),
            'price_from' => (float) ($this->price_from),
            'price_to' => (float) ($this->price_to),
            'price_source' => (float) ($this->price_source),
            'price_custom_value' => (float) ($this->price_custom_value),
            'price_ratio' => pSQL($this->price_ratio),
            'source' => (int)$this->source,
        );
    }

    public function addCondition($priceId, $condition)
    {
        $this->id = null;
        $this->price_id = $priceId;
        $this->setData($condition)->save();
    }

    public function getList($priceId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE price_id = " . $priceId;
        return Db::getInstance()->ExecuteS($sql);
    }

    public function getFirstConditionByTemplatePriceWeight($templateId, $price, $weight)
    {
        // First check for condition that apply for all price type
        $sql = 'SELECT * FROM '. _DB_PREFIX_ . $this->table . ' WHERE price_id = '.$templateId.' AND `type` = '.self::CONDITION_TYPE_ANY.'  ORDER BY id ASC';
        $result = Db::getInstance()->getRow($sql, false);
        if (isset($result['id']) && $result['id'] > 0) {
            return $result;
        }

        // Second check price in range [from, to). "to" not included
        $sql = 'SELECT * FROM '. _DB_PREFIX_ . $this->table . ' WHERE price_id = '.$templateId.
                        ' AND `type` = '.self::CONDITION_TYPE_RANGE.
                        ' AND `source` = '.self::TYPE_SOURCE_PRICE.
                        ' AND price_from<='.$price.
                        ' AND '.$price.' < price_to ORDER BY id ASC';

        $result = Db::getInstance()->getRow($sql, false);
        if (isset($result['id']) && $result['id'] > 0) {
            return $result;
        }

        // Third check weight in range [from, to). "to" not included
        $sql = 'SELECT * FROM '. _DB_PREFIX_ . $this->table . ' WHERE price_id = '.$templateId.
            ' AND `type` = '.self::CONDITION_TYPE_RANGE.
            ' AND `source` = '.self::TYPE_SOURCE_WEIGHT.
            ' AND price_from<='.$weight.
            ' AND '.$weight.' < price_to ORDER BY id ASC';

        $result = Db::getInstance()->getRow($sql, false);
        if (isset($result['id']) && $result['id'] > 0) {
            return $result;
        }

        // Nothing found
        return false;
    }

}