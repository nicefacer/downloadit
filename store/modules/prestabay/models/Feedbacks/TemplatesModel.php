<?php

/**
 * File FeedbacksModel.php
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
 * eBay Listener Integration with PrestaShop e-commerce platform.
 * Adding possibility list PrestaShop Product directly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Feedbacks_TemplatesModel extends AbstractModel
{
     public $message;

    public $feedback_type;

    public $date_upd;

    public function __construct($id = null, $id_lang = null)
    {
        $this->table      = "prestabay_feedbacks_templates";
        $this->identifier = "id";

        $this->fieldsRequired = array('message', 'feedback_type',);
        $this->fieldsSize     = array();
        $this->fieldsValidate = array();

        parent::__construct($id, $id_lang);
    }

    public function getFields()
    {
        parent::validateFields();
        $returnArray = array(
            'message'       => pSQL(substr($this->message, 0, 80)),
            'feedback_type' => $this->feedback_type,
            'date_upd'      => $this->date_upd,
        );

        return $returnArray;
    }

    /**
     * @param string|bool $type
     *
     * @return array
     */
    public static function getTemplatesList($type = false)
    {
        $sql = "SELECT * FROM " . _DB_PREFIX_ . "prestabay_feedbacks_templates";
        if ($type) {
            $sql.=" WHERE feedback_type='".pSQL($type)."'";
        }

        return Db::getInstance()->ExecuteS($sql);
    }

} 