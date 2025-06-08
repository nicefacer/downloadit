<?php

class L
{

    protected static $_messageFile = null;
    protected static $_cachedTranslate = array();

    public static function t($textToTranslate)
    {


        $key = $textToTranslate; //strtolower($textToTranslate);
        if (isset(self::$_cachedTranslate[$key])) {
            return self::$_cachedTranslate[$key];
        }

        if (is_null(self::$_messageFile)) {
            global $cookie;
            $idLang = (!isset($cookie) OR !is_object($cookie)) ? (int) (Configuration::get('PS_LANG_DEFAULT')) : (int) ($cookie->id_lang);
            self::$_messageFile = _PS_MODULE_DIR_ . 'prestabay' . '/locale/' . Language::getIsoById($idLang) . '.php';
        }

        if (!file_exists(self::$_messageFile)) {
            return self::$_cachedTranslate[$key] = $textToTranslate;
        }

        $messages = include(self::$_messageFile);
        if (!is_array($messages)) {
            $messages = array();
        }
        return self::$_cachedTranslate[$key] = isset($messages[$key]) ? $messages[$key] : $textToTranslate;
    }

}