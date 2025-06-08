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

class Price_TemplateModel extends AbstractModel
{

    public $name;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_template_price";
        $this->identifier = "id";

        $this->fieldsRequired = array('name');

        $this->fieldsSize = array('name' => 75);

        $this->fieldsValidate = array(
            'name' => 'isGenericName',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'name' => pSQL($this->name),
        );
    }

    public static function getTemplatesList()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_template_price";
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Remove all conditions that related to current template
     */
    public function removeAllConditions()
    {
        if (is_null($this->id)) {
            throw new Exception("Please Load Price Template Model");
        }
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . 'prestabay_template_price_conditions WHERE price_id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }

    /**
     * Remove template with conditions
     */
    public function remove()
    {
        if (is_null($this->id)) {
            throw new Exception(L::t("Please Load Price Template Model"));
        }
        $this->removeAllConditions();
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }

    /**
     * Parse and return result value of price for provided template and target
     * product price
     * @param int $templateId price template id
     * @param float $productPrice original product price
     * @param float $productWeight original weight of product
     *
     * @return float parsed price value
     */
    public static function getParsedPrice($templateId, $productPrice, $productWeight = 0.0)
    {
        $priceTemplateModel = new Price_TemplateModel($templateId);
        if (is_null($priceTemplateModel->id)) {
            // template not found
            return 0;
        }

        $priceTemplateConditionsModel = new Price_ConditionsModel();
        $conditionRow = $priceTemplateConditionsModel->getFirstConditionByTemplatePriceWeight($templateId, $productPrice, $productWeight);
        if (!$conditionRow) {
            return 0;
        }
        $templateCalculatedValue = $productPrice;

        if ($conditionRow['price_source'] == Price_ConditionsModel::CONDITION_SOURCE_CUSTOM) {
            $templateCalculatedValue = $conditionRow['price_custom_value'];
        }

        if ($matchesCount = preg_match_all('((x|\+|-|\*)([0-9\.]+))', $conditionRow['price_ratio'], $matches)) {
            for ($i = 0; $i < $matchesCount; $i++) {
                $templateCalculatedValue = self::getPriceByOperationValue($templateCalculatedValue, $matches[1][$i], $matches[2][$i]);
            }
        }
        return $templateCalculatedValue;
    }

    /**
     * Calculate new price value used operation and coefficient
     *
     * @param float $price price value before calculated
     * @param string $operation operation, support +,-, * (x)
     * @param string $coefficient how mutch increase price
     * @return float new price value after apply operation
     */
    public static function getPriceByOperationValue($price, $operation, $coefficient)
    {
        switch ($operation) {
            case '*':
            case 'x':
                $price *= $coefficient;
                break;
            case '+':
                $price += $coefficient;
                break;
            case '-':
                $price -= $coefficient;
        }

        return $price;
    }

}