<?php
MLFilesystem::gi()->loadClass('Modul_Helper_Model_Service_OrderData_Normalize');

class ML_Dawanda_Helper_Model_Service_OrderData_Normalize extends ML_Modul_Helper_Model_Service_OrderData_Normalize {
    
    protected function normalizeAddressSets () {
        parent::normalizeAddressSets();
        $this->aOrder['AddressSets']['Main']['EMailIdent'] = $this->dawandaFindCustomerIdent(
            $this->aOrder['MPSpecific']['BuyerUsername'],
            $this->aOrder['AddressSets']['Main']['EMail']
        );
        return $this;
    }
    
    /**
     * add payment to totals
     */
    protected function normalizeTotals () {
        $this->aOrder['Totals'] = array_key_exists('Totals', $this->aOrder) ? $this->aOrder['Totals'] : array();
        $blFound = false;
        foreach ($this->aOrder['Totals'] as $aTotal) {
            if ($aTotal['Type'] == 'Payment') {
                $blFound = true;
            }
        }
        if (!$blFound && isset($this->aOrder['MPSpecific']['Payment']) && isset($this->aOrder['MPSpecific']['Payment']['Code'])) {
            $this->aOrder['Totals'][] = array(
                'Type' => 'Payment',
                'Code' => $this->aOrder['MPSpecific']['Payment']['Code'],
                'Value' => 0
            );
        }
        return parent::normalizeTotals();
    }
    
    protected function dawandaFindCustomerIdent ($sBuyer, $sDefault) {
        if (MLModul::gi()->getConfig('customersync')) {
            $sResult = MLDatabase::getDbInstance()->fetchOne("
                SELECT orderdata 
                FROM magnalister_orders 
                WHERE orderdata like  '%\"BuyerUsername\":\"".$sBuyer."\"%' 
                AND platform = '".  MLModul::gi()->getMarketPlaceName()."'
                ORDER BY inserttime desc
                LIMIT 1
            ");
            $aResult = json_decode($sResult, true);
            if (
                !empty($aResult)
                && isset($aResult['AddressSets']['Main']['EMail'])
            ) {
                return $aResult['AddressSets']['Main']['EMail'];
            }
        }
        return $sDefault;
    }
    
}
