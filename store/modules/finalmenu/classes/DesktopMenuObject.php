<?php
/**
* Finalmenu
*
* @author     Matej Berka
* @copyright  2014 Matej
* @license    http://www.php.net/license/3_01.txt  PHP License 3.0
*/

class DesktopMenuObject extends ObjectModel
{
    public $name;
    public $tab_link;
    public $tab_position;
    public $link_window;
    public $active;
    public $type;
    public $position;
    public $tab_icon;
    public $tab_image;
    public $tab_note;
    public $tab_note_bg_color;
    public $links_color;
    public $other_text_color;
    public $links_hover_color;
    public $settings;

    public static $definition = array(
        'table' => 'desktop_menu_tabs',
        'primary' => 'id_tab',
        'multilang' => true,
        'fields' => array(
            'name'     => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isGenericName', 'required' => true),
            'tab_link' => array('type' => self::TYPE_STRING,'lang' => true, 'validate' => 'isUrl'),
            'link_window' => array('type' => self::TYPE_BOOL),
            'active'   => array('type' => self::TYPE_BOOL),
            'type'     => array('type' => self::TYPE_INT),
            'position' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt'),
            'tab_icon' => array('type' => self::TYPE_STRING),
            'tab_image' => array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'tab_note' => array('type' => self::TYPE_STRING, 'lang' => true),
            'tab_note_bg_color' => array('type' => self::TYPE_STRING),
            'tab_position'   => array('type' => self::TYPE_STRING),
            'links_color' => array('type' => self::TYPE_STRING),
            'other_text_color' => array('type' => self::TYPE_STRING),
            'links_hover_color' => array('type' => self::TYPE_STRING),
            'settings' => array('type' => self::TYPE_STRING),
        ),
    );

    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        $this->links_color = '#777777';

        $this->other_text_color = '#777777';
        $this->links_hover_color = '#7caa3d';
        $this->tab_note_bg_color = '#686868';
        $this->tab_position = 'left';
        $this->active = 1;

        parent::__construct($id, $id_lang, $id_shop);
    }

    public function add($autodate = true, $nullValues = false)
    {
        $this->position = DesktopMenuObject::getPosition();

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

    public function getSettings()
    {
         return ($this->settings);
    }

    private static function getPosition()
    {
        return (Db::getInstance()->getValue('
            SELECT IFNULL(MAX(position),0)+1
            FROM `'._DB_PREFIX_.'desktop_menu_tabs`'
        ));
    }

    public function reorder()
    {
        $result = Db::getInstance()->executeS('
            SELECT `id_tab`
            FROM `'._DB_PREFIX_.'desktop_menu_tabs`
            ORDER BY `position`
        ');

        $sizeof = count($result);
        for ($i = 0; $i < $sizeof; ++$i)
            Db::getInstance()->execute('
                UPDATE `'._DB_PREFIX_.'desktop_menu_tabs`
                SET `position` = '.($i + 1).'
                WHERE `id_tab` = '.(int) $result[$i]['id_tab']
            );

        return true;
    }

}
