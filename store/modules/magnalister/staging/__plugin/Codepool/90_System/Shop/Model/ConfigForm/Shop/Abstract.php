<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author mba
 */
abstract class ML_Shop_Model_ConfigForm_Shop_Abstract {
    
    /**
     * you can override this function and edit form mask in each shop specific class
     * @param type $aForm
     */
    public function manipulateForm(&$aForm) {
    }
}