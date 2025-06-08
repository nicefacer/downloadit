<?php
/**
 * File MarketplacesModel.php
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

class MarketplacesModel extends AbstractModel
{
    const STATUS_PENDING = 0;
    const STATUS_ACTIVE = 1;

    public $code;
    public $label;
    public $url;
    public $status;
    public $date_upd;
    public $version;
    public $dispatch;
    public $policy;
    public $payment_methods;
    public $shipping_location;
    public $shipping_exclude_location;
    public $shipping_packages;
    public $identify_unavailable_text;

    public function __construct($id = NULL, $id_lang = NULL)
    {
        $this->table = "prestabay_marketplaces";
        $this->identifier = "id";

        $this->fieldsRequired = array('code', 'label', 'url');

        $this->fieldsSize = array('code' => 40, 'label' => 50, 'url' => 50, 'dispatch' => 9999, 'policy' => 9999, 'payment_methods' => 9999);

        $this->fieldsValidate = array(
            'code' => 'isGenericName',
            'label' => 'isGenericName',
            'url' => 'isString',
            'status' => 'isInt',
            'version' => 'isInt',
            'dispatch' => 'isString',
            'policy' => 'isString',
            'payment_methods' => 'isString',
            'shipping_location' => 'isString',
            'shipping_exclude_location' => 'isString',
            'shipping_packages' => 'isString',
            'identify_unavailable_text' => 'isString'
        );
        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();

        return array(
            'code' => pSQL($this->code),
            'label' => pSQL($this->label),
            'url' => pSQL($this->url),
            'status' => $this->status,
            'version' => (int) $this->version,
            'date_upd' => $this->date_upd,
            'dispatch' => pSQL($this->dispatch),
            'policy' => pSQL($this->policy),
            'payment_methods' => pSQL($this->payment_methods),
            'shipping_location' => pSQL($this->shipping_location),
            'shipping_exclude_location' => pSQL($this->shipping_exclude_location),
            'shipping_packages' => pSQL($this->shipping_packages),
            'identify_unavailable_text' => pSQL($this->identify_unavailable_text),
        );
    }

    public function clearCategoryVersion($idsList)
    {

        $sqlToUpdate = "UPDATE " . _DB_PREFIX_ . $this->table . " SET
                            `version` = 0,
                            `status` = " . self::STATUS_PENDING . ",
                            `date_upd` = null,
                            `dispatch` = null,
                            `policy` = null,
                            `payment_methods` = null,
                            `shipping_location` = null,
                            `shipping_exclude_location` = null,
                            `shipping_packages` = null,
                            `identify_unavailable_text` = null
                        WHERE id in (" . implode(",", $idsList) . ")";

        Db::getInstance()->Execute($sqlToUpdate);
    }

    public function filterOnlyActive()
    {
        $select = $this->getSelect();
        $select->addFilter('`mt`.status', self::STATUS_ACTIVE);
        return $select;
    }

    public static function getMarketplaceList()
    {
        $instance = new MarketplacesModel();
        $select = $instance->getSelect()->addFilter("status", self::STATUS_ACTIVE);
        $availableMarketplaces = $instance->getSelect()->getItems();
        $marketplaces = array();
        foreach ($availableMarketplaces as $marketplace) {
                        $marketplaces[] = array(
                            'id'   => $marketplace['id'],
                            'name' => $marketplace['label'],
                        );
        }

        return $marketplaces;
    }

    /**
     * Get Marketplace ID by code
     *
     * @param string $code marketplace code
     *
     * @return bool|int
     *
     */
    public static function getMarketplaceIdByCode($code)
    {
        $sql = "SELECT id FROM " . _DB_PREFIX_ . "prestabay_marketplaces
            WHERE code = '".pSQL($code)."'";
        $row = Db::getInstance()->getRow($sql, false);

        if (!isset($row['id'])) {
            return false;
        }


        return $row['id'];
    }
}