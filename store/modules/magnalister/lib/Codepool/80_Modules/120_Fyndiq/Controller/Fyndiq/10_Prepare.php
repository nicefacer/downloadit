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
MLFilesystem::gi()->loadClass('Productlist_Controller_Widget_ProductList_Selection');

class ML_Fyndiq_Controller_Fyndiq_Prepare extends ML_Productlist_Controller_Widget_ProductList_Selection
{
    protected $aParameters = array('controller');

    public function __construct()
    {
        parent::__construct();
        try {
            $sExecute = $this->oRequest->get('view');
            if (in_array($sExecute, array('unprepare', 'reset'))) {
                $oModel = MLDatabase::factory('fyndiq_prepare');
                $oList = MLDatabase::factory('selection')->set('selectionname', 'apply')->getList();
                $iLang = MLModul::gi()->getConfig('lang');
                foreach ($oList->get('pid') as $iPid) {
                    $oModel->init()->set('products_id', $iPid);
                    switch ($sExecute) {
                        case 'unprepare': {//delete from fyndiq_prepare
                            $oModel->delete();
                            break;
                        }

                        case 'reset': {//set products title, description and images of fyndiq_prepare to actual product values
                            if ($oModel->exists()) {
                                MLProduct::factory()->set('id', $iPid)->setLang($iLang);
                                $this->resetData($oModel);
                            }

                            break;
                        }
                    }
                }
            }
        } catch (Exception $oEx) {
//            echo $oEx->getMessage();
        }
    }

    public static function getTabTitle()
    {
        return MLI18n::gi()->get('ML_GENERIC_PREPARE');
    }

    public static function getTabActive()
    {
        return MLModul::gi()->isConfigured();
    }

    public static function getTabDefault()
    {
        return true;
    }

    public function getPriceObject(ML_Shop_Model_Product_Abstract $oProduct)
    {
        return MLModul::gi()->getPriceObject();
    }

    public function getProductListWidget()
    {
        try {
            if ($this->isCurrentController()) {
                return parent::getProductListWidget();
            }

            return $this->getChildController('form')->render();
        } catch (Exception $oExc) {
            MLHttp::gi()->redirect($this->getParentUrl());
        }
    }

    private function resetData($oModel)
    {
        $oModel->set('ItemTitle', '')
            ->set('Description', '')
            ->set('Images', '')
            ->save();
    }

    public static function substringAferLast($sNeedle, $sString)
    {
        if (!is_bool(self::strrevpos($sString, $sNeedle))) {
            return substr($sString, self::strrevpos($sString, $sNeedle) + strlen($sNeedle));
        }
    }

    private static function strrevpos($instr, $needle)
    {
        $rev_pos = strpos(strrev($instr), strrev($needle));
        if ($rev_pos === false) {
            return false;
        } else {
            return strlen($instr) - $rev_pos - strlen($needle);
        }
    }


}
