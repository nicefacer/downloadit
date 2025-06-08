<?php
/**
 * FINALmenu
 *
 * @author     Matej Berka
 * @copyright  2014 Matej
 * @license    http://www.php.net/license/3_01.txt  PHP License 3.0
 */

class MobileMenuObject extends ObjectModel
{
    public $name;
    public $product_ID;
    public $link_title;
    public $active;
    public $link_url;
    public $position;
    public $link_new_window;

    public static $definition = array(
        'table' => 'mobile_menu_tabs',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'name'     => array('type' => self::TYPE_STRING),
            'product_ID'   => array('type' => self::TYPE_BOOL),
            'active'        => array('type' => self::TYPE_BOOL),
            'link_title'     => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName'),
            'link_url'      => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isUrl'),
            'position'       => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'link_new_window' => array('type' => self::TYPE_BOOL),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $nullValues = false)
    {
        $this->position = MobileMenuObject::getPosition();

        return parent::add($autodate, true);
    }

    public function delete()
    {
        if ((int) $this->id === 0)
            return false;

        return parent::delete() && $this->reorder();
    }

    // ADDITIONAL METHODS
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    private static function getPosition()
    {
        return (Db::getInstance()->getValue('
            SELECT IFNULL(MAX(position),0)+1
            FROM `'._DB_PREFIX_.'mobile_menu_tabs`'
        ));
    }

    public function reorder()
    {
        $result = Db::getInstance()->executeS('
            SELECT `id_tab`
            FROM `'._DB_PREFIX_.'mobile_menu_tabs`
            ORDER BY `position`
        ');

        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i)
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'mobile_menu_tabs`
                SET `position` = '.($i + 1).'
                WHERE `id_tab` = '.(int) $result[$i]['id_tab']
            );

        return true;
    }

}
