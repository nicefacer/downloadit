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

class Shipping_ConditionsModel extends AbstractModel
{
    const CONDITION_TYPE_ANY = 0;
    const CONDITION_TYPE_RANGE = 1;
    const CONDITION_SOURCE_PRODUCT = 1;
    const CONDITION_SOURCE_CUSTOM = 2;

    public $shipping_id;
    public $value_from;
    public $value_to;
    public $plain;
    public $additional;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_template_shipping_conditions";
        $this->identifier = "id";

        $this->fieldsRequired = array('shipping_id');

        $this->fieldsSize = array();

        $this->fieldsValidate = array();
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'shipping_id' => (int) ($this->shipping_id),
            'value_from' => (float) ($this->value_from),
            'value_to' => (float) ($this->value_to),
            'plain' => (float) ($this->plain),
            'additional' => (float) ($this->additional),
        );
    }

    public function addCondition($shippingId, $condition)
    {
        $this->id = null;
        $this->shipping_id = $shippingId;
        $this->setData($condition)->save();
    }

    public function getList($shippingId)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . $this->table . " WHERE shipping_id = " . $shippingId;
        return Db::getInstance()->ExecuteS($sql);
    }

    public function getFirstConditionByTemplateAndValue($templateId, $value)
    {
        // Check value in range [from, to). "to" not included
        $sql = 'SELECT * FROM '. _DB_PREFIX_ . $this->table . ' WHERE shipping_id = '.$templateId.'
             AND value_from<='.$value.' AND '.$value.' < value_to ORDER BY id ASC';

        $result = Db::getInstance()->getRow($sql);
        if (isset($result['id']) && $result['id'] > 0) {
            return $result;
        }
        
        // Nothing found
        return false;
    }

}