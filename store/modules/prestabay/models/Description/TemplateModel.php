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

class Description_TemplateModel extends AbstractModel
{

    public $name;
    public $template;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_template_description";
        $this->identifier = "id";

        $this->fieldsRequired = array('name', 'template');

        $this->fieldsSize = array('name' => 255);

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
            'template' => pSQL($this->template, true),
        );
    }

    public static function getTemplatesList()
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_template_description";
        return Db::getInstance()->ExecuteS($sql);
    }


    /**
     * Remove template
     */
    public function remove()
    {
        if (is_null($this->id)) {
            throw new Exception(L::t("Please Load Description Template Model"));
        }
        $sql = 'DELETE FROM ' . _DB_PREFIX_ . $this->table . ' WHERE id = ' . $this->id;
        Db::getInstance()->Execute($sql);
    }

    public static function generateRandomProductId()
    {
        $sql = "SELECT id_product FROM "._DB_PREFIX_."product LIMIT 0,1000";
        $productIds =  Db::getInstance()->ExecuteS($sql);
        if (!is_array($productIds)) {
            return false;
        }
        shuffle($productIds);

        $firstOne = reset($productIds);
        return isset($firstOne['id_product'])?$firstOne['id_product']:false;
    }

}