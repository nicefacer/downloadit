<?php
/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */

/**
 * Class for handling translations.
 */
class MLI18n extends MLRegistry_Abstract {
    
    protected $blDefaultValue=true;
    
    protected $aIncludedFiles = array();
    
    /**
     * Singleton. Returns the created instance.
     * @return MLI18n
     */
    public static function gi($sInstance = null) {
        return parent::getInstance('MLI18n', $sInstance);
    }
    
    /**
     * Boots the class, gets the language and loads all language files.
     * @return void
     */
    protected function bootstrap() {
        $this->getLang();
        $this->includeFiles();
    }
    
    /**
     * Returns all languages which have translations.
     * @return array
     */
    public static function getPossibleLanguages() {
        $aAllLanguages = array();
        foreach (MLFilesystem::gi()->getBasePaths('i18n') as $aModul){//getting all availible languages
            foreach ($aModul as $aModulInfo) {
                $sPathLang = strtolower(basename(dirname($aModulInfo['path'])));
                if (!in_array($sPathLang, $aAllLanguages)) {
                    $aAllLanguages[] = $sPathLang;
                }
            }
        }
        return $aAllLanguages;
    }
    
    /**
     * Alias getter for shoptype.
     * Find out what shop type and set $sShopType eg. magento, oscommerce.
     * Sets config value sLibPath (path/to/ML/Lib).
     * @return string
     * @todo Exeption unknown shoptype
     */
    public function getLang() {
        try {
            $sLang = MLSetting::gi()->get('sLang');
        } catch(Exception $oEx) {
            $sLang = MLLanguage::gi()->getCurrentIsoCode();
            $aAllLanguages = self::getPossibleLanguages();
            if (!in_array($sLang, $aAllLanguages)) {
                if (in_array('en', $aAllLanguages)) {//default
                    $sLang='en';
                }else{
                    $sLang='de';
                }
            }
            MLSetting::gi()->set('sLang', $sLang);
        }
        return $sLang;
    }
    
    /**
     * Part of bootstrap
     * Reads all files in config folders, customerspecific first.
     * @return void
     */
    public function includeFiles() {
        foreach (array_diff(
            MLFilesystem::gi()->getLangFiles($this->getLang()), 
            $this->aIncludedFiles
        ) as $sPath) {
            $this->aIncludedFiles[]=$sPath;
            if (pathinfo($sPath, PATHINFO_EXTENSION) == 'php') {
                include($sPath);
            } else {
                $rPath = fopen($sPath,'r');
                while ($aI18n = fgetcsv($rPath)) {
                    if (substr($aI18n[0], 0, 1) != '[') {
                        $this->$aI18n[0] = $aI18n[1];
                    }
                }
                fclose($rPath);
            }
        }
    }
    
    /**
     * Finds a language file based of its name, and the specified prefixes.
     *
     * return string
     */
    public function find($sName, $aPrefixes = array('')) {
        foreach ($aPrefixes as $sPrefix) {
            if ($this->__get($sPrefix.$sName) != $sPrefix.$sName) {
                return $this->__get($sPrefix.$sName);
            }
        }
        return $sName;
    }
    
    /**
     * override set to use array_replace_recursive instead of array_merge_recursive
     * to prevent converting existed non array value to array
     * array_merge_recursive(
     *      array('Title' => 'Produktname' ),
     *      array('Title' => 'Product name' )
     * )
     * result : array(
     *     'Title' => array( 
     *         0=>'Produktname',
     *         1=>'Product name' 
     *     ) 
     * )
     *       ****
     * array_replace_recursive(
     *      array('Title' => 'Produktname' ),
     *      array('Title' => 'Product name' )
     * )
     * result : array(
     *    'Title' => 'Product name'
     * )
     * @param string $sName
     * @param mixed $mValue
     * @param bool $blForce
     * @return MLRegistry
     * @throws MLAbstract_Exception
     */
    public function set($sName, $mValue, $blForce = false) {
        if (strpos($sName,'__')!==false ) {
            $aData = MLHelper::getArrayInstance()->flat2Nested(array($sName => $mValue));
            if (!function_exists('array_replace_recursive')){
                $this->aData = $this->array_replace_recursive($this->aData, $aData);
            }else{
                $this->aData = array_replace_recursive($this->aData, $aData);
            }
        } else {
            if (!isset($this->aData[$sName]) || $blForce) {
                $this->aData[$sName] = $mValue;
            } else {
                throw new $this->sExceptionClass('Value `'.$sName.'` alerady exists.', 1356259108);
            }
        }
        return $this;
    }
    
    /**
     * alternative for array_replace_recursive in php < 5.3
     * http://stackoverflow.com/questions/2874035/php-array-replace-recursive-alternative
     * @param type $array
     * @param type $array1
     * @return type
     */
    public function array_replace_recursive($array, $array1) {
        // handle the arguments, merge one by one
        $args = func_get_args();
        $array = $args[0];
        if (!is_array($array)) {
            return $array;
        }
        for ($i = 1; $i < count($args); $i++) {
            if (is_array($args[$i])) {
                $array = $this->recurse($array, $args[$i]);
            }
        }
        return $array;
    }

    /**
     * walk array recursive
     * @param type $array
     * @param type $array1
     * @return array
     */
    protected function recurse($array, $array1) {
        foreach ($array1 as $key => $value) {
            // create new key in $array, if it is empty or not an array
            if (!isset($array[$key]) || (isset($array[$key]) && !is_array($array[$key]))) {
                $array[$key] = array();
            }

            // overwrite the value in the base array
            if (is_array($value)) {
                $value = $this->recurse($array[$key], $value);
            }
            $array[$key] = $value;
        }
        return $array;
    }

}
