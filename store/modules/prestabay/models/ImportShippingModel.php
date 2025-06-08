<?php
/**
 * File ImportShippingModel.php
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

class ImportShippingModel extends AbstractModel
{

    public $site_id;
    public $shipping_ebay_name;
    public $title;
    public $flat;
    public $calculated;
    public $international;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_import_shipping";
        $this->identifier = "id";

        $this->fieldsRequired = array('site_id', 'shipping_ebay_name', 'title');

        $this->fieldsSize = array();

        $this->fieldsValidate = array(
            'site_id' => 'isInt',
            'shipping_ebay_name' => 'isString',
            'title' => 'isString',
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'site_id' => (int) $this->site_id,
            'shipping_ebay_name' => pSQL($this->shipping_ebay_name),
            'title' => pSQL($this->title),
            'flat' => (int) $this->flat,
            'calculated' => (int) $this->calculated,
            'international' => (int) $this->international
        );
    }

    public function importShippings($siteId, $shippingList)
    {
        // First remove old shipping available options
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE site_id = " . $siteId;
        Db::getInstance()->Execute($removeSql);

        if (!is_array($shippingList)) {
            throw new Exception('Invalid shipping data');
        }

        foreach ($shippingList as $singleShipping) {
            $inserSql = "INSERT INTO `" . _DB_PREFIX_ . $this->table . "` (`site_id`, `shipping_ebay_name`, `title`, `flat`, `calculated`, `international`) VALUES (" .
                    (int) $siteId . "," .
                    "'" . pSQL($singleShipping['shipping_ebay_name']) . "'," .
                    "'" . pSQL($singleShipping['title']) . "'," .
                    (int) $singleShipping['flat'] . "," .
                    (int) $singleShipping['calculated'] . "," .
                    (int) $singleShipping['international'] .
                    ")";
            Db::getInstance()->Execute($inserSql);
        }
    }

    public function getShippingNameById($shippingId)
    {
        $sql = "SELECT title FROM ". _DB_PREFIX_ . $this->table ." WHERE shipping_ebay_name = '".pSQL($shippingId)."'";
        return Db::getInstance()->getValue($sql, false);
    }

    public function removeShippingsData($idsList)
    {
        $removeSql = "DELETE FROM " . _DB_PREFIX_ . $this->table . " WHERE site_id in (" . implode(",", $idsList) . ")";
        Db::getInstance()->Execute($removeSql);
    }

    /**
     * Get domestic shipping methods for provided marketplace
     */
    public function getLocalShippingMethods($marketplaceId, $isCalculated = false)
    {
        $sql = "SELECT shipping_ebay_name as id, title as label FROM " . _DB_PREFIX_ . $this->table .
                " WHERE site_id = " . ((int) $marketplaceId) .
                " AND international = 0 AND calculated=".($isCalculated?'1':'0')." ORDER BY title ASC";
        return Db::getInstance()->ExecuteS($sql);
    }

    /**
     * Get international shipping methods for provided marketplace
     */
    public function getInternationalShippingMethods($marketplaceId, $isCalculated = false)
    {
        $sql = "SELECT shipping_ebay_name as id, title as label FROM " . _DB_PREFIX_ . $this->table .
                " WHERE site_id = " . ((int) $marketplaceId) .
                " AND international = 1 AND calculated=".($isCalculated?'1':'0')." ORDER BY title ASC";
        return Db::getInstance()->ExecuteS($sql);
    }

}
