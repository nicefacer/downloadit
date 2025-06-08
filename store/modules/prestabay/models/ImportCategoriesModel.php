<?php
/**
 * File ImportCategoriesModel.php
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

class ImportCategoriesModel extends AbstractModel
{

    public $parent_category_id;
    public $marketplace_id;
    public $name;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_import_categories";
        $this->identifier = "id";

        $this->fieldsRequired = array('parent_category_id', 'marketplace_id', 'name');

        $this->fieldsSize = array();

        $this->fieldsValidate = array(
            'parent_category_id' => 'isInt',
            'marketplace_id' => 'isInt',
            'name' => 'isString',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'parent_category_id' => (int) $this->code,
            'marketplace_id' => (int) $this->label,
            'name' => pSQL($this->url)
        );
    }

    public function updateCategory($marketplaceId, $categoriesList)
    {
        // First remove old categories
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE marketplace_id = " . $marketplaceId;
        Db::getInstance()->Execute($removeSql);

        // Start update process
        // Insert by 5 pair
        $count = count($categoriesList);
        $plainInserSql = "INSERT INTO `" . _DB_PREFIX_ . $this->table . "` (`category_id`, `parent_category_id`, `marketplace_id`, `name`) VALUES ";

        DebugHelper::addProfilerMessage("Before start import");

        DebugHelper::addProfilerTimeSpot("Full Insert");
        for ($i = 0; $i < $count; $i+=5) {

            $insertCommand = $plainInserSql;
            $hasValues = false;

            for ($j = 0; $j < 5; $j++) {
                if (!isset($categoriesList[$i + $j])) {
                    break;
                }
                if ($hasValues) {
                    $insertCommand.=",";
                }
                $currentRow = $categoriesList[$i + $j];

                $insertCommand.="(";
                $insertCommand.=$currentRow['id'] . "," . $currentRow['parent_category_id'] . "," . $marketplaceId . ",'" . pSQL($currentRow['name']) . "'";
                $insertCommand.=")";

                $hasValues = true;
            }
            if ($hasValues) {
                $insertCommand.=";";
                Db::getInstance()->Execute($insertCommand);
            }
        }

        DebugHelper::endProfilerTimeSpot("Full Insert");
    }

    public function removeCategoryData($idsList)
    {
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE marketplace_id in (" . implode(",", $idsList) . ")";
        Db::getInstance()->Execute($removeSql);
    }

    public function getMarketplaceMainCategories($marketplaceId)
    {
        return $this->getChildCategories($marketplaceId, 0);
    }

    public function getChildCategories($marketplaceId, $parentCategoryId)
    {
        $marketplaceId = (((int) $marketplaceId) == 0) ? 1 : ((int) $marketplaceId);
        $sql = "SELECT category_id as id, name as label FROM " . _DB_PREFIX_ . $this->table . " WHERE marketplace_id = " . ((int) $marketplaceId) . " AND parent_category_id = " . ((int) $parentCategoryId) . " ORDER BY name ASC";
        return Db::getInstance()->ExecuteS($sql);
    }

}