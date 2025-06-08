<?php
/**
 * Helper class for generic serializazion and deserialization operations.
 */
class ML_Core_Helper_Encoder {
    
    /**
     * Encodes a data element
     * @param mixed $mValue
     *    The element that has to be encoded.
     *    Arrays will be json encoded
     *    Objects will be serialized
     *    Other values will be converted to string unless they are null.
     * @return ?string
     */
    public function encode($mValue) {
        if (is_array($mValue)) {
            $sValue = json_encode($mValue);
        } elseif (is_object($mValue)) {
            $sValue = serialize($mValue);
        } elseif ($mValue !== null) {
            $sValue = (string) $mValue;
        } else {
             $sValue = null;
        }
        return $sValue;
    }

    /**
     * Decodes a string to an Array or Object.
     * @param ?string $mData
     * @return mixed
     *    The decoded array or object.
     */
    public function decode($mData) {
        if ($mData !== null){
            $aJson = json_decode($mData, true);
            if (is_array($aJson)) {
                $mData = $aJson;
            } else {
                $oSerialized = @unserialize($mData);
                if (is_object($oSerialized)) {
                    $mData = $oSerialized;
                } else {
                    $mData = (string) $mData;
                }
            }
        }
        return $mData;
    }

}
