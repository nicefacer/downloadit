<?php

/**
 * File TemplateModel.php
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
class Shipping_TemplateModel extends AbstractModel
{
    const MODE_WEIGHT = 0;
    const MODE_PRICE = 1;

    const REMOVE_NOT_IN_RANGE_FALSE = 0;
    const REMOVE_NOT_IN_RANGE_TRUE = 1;

    public $name;
    public $mode;
    public $remove_not_in_range;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_template_shipping";
        $this->identifier = "id";

        $this->fieldsRequired = array('name');

        $this->fieldsSize = array('name' => 255);

        $this->fieldsValidate = array( );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name' => pSQL($this->name),
            'mode' => (int) $this->mode,
            'remove_not_in_range' => (int)$this->remove_not_in_range, 
        );
    }

    public static function getTemplatesList()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_template_shipping";
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Remove all conditions that related to current template
     */
    public function removeAllConditions()
    {
        if (is_null($this->id)) {
            throw new Exception("Please Load Shipping Template Model");
        }
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'prestabay_template_shipping_conditions WHERE shipping_id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }

    /**
     * Remove template with conditions
     */
    public function remove()
    {
        if (is_null($this->id)) {
            throw new Exception(L::t("Please Load Shipping Template Model"));
        }
        $this->removeAllConditions();
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }

    /**
     * Process template to calculate shipping cost based on product price or weight
     *
     * @param int $templateId shipping template id
     * @param float $productPrice
     * @param float $productWeight
     * @return array|bool on false shipping need to be removed, otherwise first
     * element item cost, second additional item cost
     */
    public static function calculateShippingCost($templateId, $productPrice, $productWeight)
    {
        $shippingTemplateModel = new Shipping_TemplateModel($templateId);
        if (is_null($shippingTemplateModel->id)) {
            // template not found, shipping will be free
            return array(0, 0);
        }
        $valueToCheck = false;
        if ($shippingTemplateModel->mode == self::MODE_WEIGHT) {
            $valueToCheck = $productWeight;
        } else if ($shippingTemplateModel->mode == self::MODE_PRICE) {
            $valueToCheck = $productPrice;
        }
        if (!$valueToCheck) {
            // Invalid mode or undefined price/weight
            return array(0,0);
        }

        $shippingConditionModel = new Shipping_ConditionsModel();
        $conditionRow = $shippingConditionModel->getFirstConditionByTemplateAndValue($templateId, $valueToCheck);
        if (!$conditionRow || !isset($conditionRow['plain']) || !isset($conditionRow['additional'])) {
            if ($shippingTemplateModel->remove_not_in_range == self::REMOVE_NOT_IN_RANGE_TRUE) {
                // Exclude shipping from sending to eBay
                return false;
            } else {
                // Free shipping
                return array(0,0);
            }
        }
        return array($conditionRow['plain'], $conditionRow['additional']);

    }

}