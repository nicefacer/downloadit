<?php
/**
 * Implements some generic methods that can be shared between various shopsystems for the
 * Price Model.
 */
abstract class ML_Shop_Model_Price_Abstract implements ML_Shop_Model_Price_Interface {

    /**
     * @var array $aPriceConfig
     *    The currently used price config.
     */
    protected $aPriceConfig = array (
        'kind' => null,
        'factor' => null,
        'signal' => null,
        'group' => null,
        'special' => null
    );
    
    /**
     * Calculates missing percentages.
     * Minimum 2 parameters must be set, the third one will be returned.
     * If all 3 parameters are set, verify that all values are correct.
     *
     * @param float $fBrut
     * @param float $fNet
     * @param float $fPercent 10(%)
     * @return mixed
     *    Returns a boolean if all 3 parameters have been set.
     *        true: All parameters are correct, false otherwise.
     *    Returns a float if a missing parameter has been calculated.
     *
     * @throws Exception
     *    In case parameters are missing
     */
    public function calcPercentages($fBrut = null, $fNet = null, $fPercent = null){
        $fBrut    = is_null($fBrut)    ? null : (float)$fBrut;
        $fNet     = is_null($fNet)     ? null : (float)$fNet;
        $fPercent = is_null($fPercent) ? null : (float)$fPercent;
        
        if (($fBrut !== null) && ($fNet !== null) && ($fPercent !== null)) {
            // Check the correctness avoiding rounding errors.
            return abs($fBrut - ($fNet + ($fNet * ($fPercent / 100)))) < 0.00001;
        } else if (($fBrut !== null) && ($fNet !== null)) {
            // Calculate the value for $fPercent.
            return (($fBrut / $fNet) - 1) * 100;
        } else if (($fBrut !== null) && ($fPercent !== null)) {
            // Calculate the value for $fNet.
            return (100 * $fBrut) / ($fPercent + 100);
        } else if (($fNet !== null) && ($fPercent !== null)) {
            // Calculate the value for $fBrut.
            return $fNet + ($fNet * ($fPercent / 100));
        } else {
            //new dBug(func_get_args());
            throw new Exception('missing value(s)');
        }
    }
    
    /**
     * Sets the config values to calculate the prices based on the saved
     * settings.
     *
     * @param string $sKind
     *    Surcharge kind (e.g. fixed or percentage)
     * @param float $fFactor
     *    Surcharge factor
     * @param int $iSignal
     *    Defines the decimal places for the price, eg always x.99
     * @param string $sGroup
     *    The customer group which should be used to load the price from.
     * @param bool $blSpecial
     *    Use the special price if available
     *
     * @return self
     */
    public function setPriceConfig($sKind, $fFactor, $iSignal, $sGroup, $blSpecial, $fTax = null){
        $this->aPriceConfig = array(
            'kind' => $sKind,
            'factor' => $fFactor,
            'signal' => $iSignal,
            'group' => $sGroup,
            'special' => $blSpecial,
            'tax' => $fTax
        );
        return $this;
    }
    
    /**
     * Gets the current applied price config.
     * 
     * @return array
     *    An array containing the keys:
     *        kind, factor, signal, group and special
     *    For their meanings @see self::setPriceConfig
     */
    public function getPriceConfig(){
        return $this->aPriceConfig;
    }
    
    /**
     * Converts a numerical string with possible thousands separators into a float
     * Example:
     *    1,533.12 => 1533.12
     *    1.533,12 => 1533.12
     *
     * @param type $sValue
     * @return string
     *    The converted value in float representation.
     */
    public function unformat($sValue) {
        $sValue = (string)$sValue;
        if (empty($sValue) || ((strpos($sValue, ',') === false) && (strpos($sValue, '.') === false))) {
            return $sValue;
        }
        $sSeparator = null;
        $iSeparator = null;
        $iComma = iconv_strrpos($sValue, ',');
        $iDot = iconv_strrpos($sValue, '.');
        
        if ($iComma === false) {
            $sSeparator = '.';
            $iSeparator = $iDot;
            
        } else if ($iDot === false) {
            $sSeparator = ',';
            $iSeparator = $iComma;
            
        } else if ($iComma > $iDot) {
            $sSeparator = ',';
            $iSeparator = $iComma;
            
        } else {
            $sSeparator = '.';
            $iSeparator = $iDot;
        }
        
        $iDecimal = substr($sValue, $iSeparator + 1, strlen($sValue) - 1);
        $iDigit = str_replace(array(',', '.'), '', substr($sValue, 0, $iSeparator));
        
        return $iDigit.'.'.$iDecimal;
    }
    
}
