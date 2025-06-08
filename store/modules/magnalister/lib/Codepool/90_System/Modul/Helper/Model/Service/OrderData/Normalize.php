<?php

class ML_Modul_Helper_Model_Service_OrderData_Normalize {
    
    /**
     *
     * @var array order-info from api will be normalized
     */
    protected $aOrder = array();
    
    /**
     * set it true in update order
     * @var bool
     */
    protected $blUpdateMode = false;

    /**
     * 
     * @param bool $blUpdateMode
     * @return \ML_Modul_Helper_Model_Service_OrderData_Normalize
     */
    public function setUpdateMode($blUpdateMode){
        $this->blUpdateMode = $blUpdateMode;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function getUpdateMode(){
        return $this->blUpdateMode;
    }
    /**
     * normalize order date
     * @param array $aOrder order-info from api
     * @return array normalized order-info
     */
    public function normalizeServiceOrderData($aOrder) {
        $this->aOrder = $aOrder;
        $this
            ->normalizeAddressSets()
            ->normalizeTotals()
            ->normalizeProducts()
            ->normalizeOrder()
            ->normalizeMpSpecific()
        ;
        return $this->aOrder;
    }
    
    /**
     * $aOrder['AddressSets']['Main']['EMailIdent'] is for shoporder to find customer by email
     * after update customer with $aOrder['AddressSets']['Main']['EMail']
     */
    protected function normalizeAddressSets () {
        $this->aOrder['AddressSets']['Main']['EMailIdent'] = 
            isset($this->aOrder['AddressSets']['Main']['EMailIdent']) 
            ? $this->aOrder['AddressSets']['Main']['EMailIdent']
            : $this->aOrder['AddressSets']['Main']['EMail']
        ;
        return $this;
    }
    
    protected function normalizeProducts () {
        $fDefaultProductTax = MLModul::gi()->getConfig('mwst.fallback');
        $fDefaultProductTax = $fDefaultProductTax === null ? MLModul::gi()->getConfig('mwstfallback') : $fDefaultProductTax;// some moduls have this, other that
        $this->aOrder['Products'] = isset($this->aOrder['Products']) ? $this->aOrder['Products'] : array();
        foreach ($this->aOrder['Products'] as &$aProduct) {
            $this->normalizeProduct($aProduct, $fDefaultProductTax);
        }
        return $this;
    }
    
    protected function normalizeProduct (&$aProduct, $fDefaultProductTax) {
        $aProduct['Tax'] = !isset($aProduct['Tax']) || $aProduct['Tax'] == 0 ? $fDefaultProductTax : $aProduct['Tax'];
        $aProduct['StockSync'] = MLModul::gi()->getConfig('stocksync.frommarketplace') == 'rel';
        if (isset($aProduct['SKU']) && $aProduct['SKU'] == '') {
            unset($aProduct['SKU']);
        }
        return $this;
    }
    
    /**
     * attribute-matching is geplant, derz. nur pflichtattribute der selektierten kategorie.
     * dropdown aus config weg.
     */
    protected function normalizeTotalTypeShipping (&$aTotal) {
        $aTotal['Code'] = $this->getShippingCode($aTotal);
        return $this;
    }
    
    protected function normalizeTotalTypePayment (&$aTotal) {
        $aTotal['Code'] = $this->getPaymentCode($aTotal);
        return $this;
    }
    
    protected function getShippingCode($aTotal) {
        $sShipping = MLModul::gi()->getConfig('orderimport.shippingmethod.name');
        return $sShipping == '' ? $aTotal['Code'] : $sShipping;
    }

    protected function getPaymentCode($aTotal) {
        if ('textfield' == MLModul::gi()->getConfig('orderimport.paymentmethod')) {
            $sPayment = MLModul::gi()->getConfig('orderimport.paymentmethod.name');
            return $sPayment == '' ? $aTotal['Code'] : $sPayment;
        } else if (!isset($aTotal['Code']) || in_array($aTotal['Code'], array('', 'none', 'None'))) {
            return MLModul::gi()->getMarketPlaceName();
        }else{
            return $aTotal['Code'];
        }
    }
    
    protected function normalizeTotals () {
        $this->aOrder['Totals'] = array_key_exists('Totals', $this->aOrder) ? $this->aOrder['Totals'] : array();
        foreach ($this->aOrder['Totals'] as &$aTotal) {
            if (method_exists($this, 'normalizeTotalType'.$aTotal['Type'])) {
                $this->{'normalizeTotalType'.$aTotal['Type']}($aTotal);
            }
            $aTotal['Value'] = isset($aTotal['Value']) ? $aTotal['Value'] : 0;
        }
        return $this;
    }
    
    protected function normalizeOrder () {
        $this->aOrder['Order']['Status'] = MLModul::gi()->getConfig('orderstatus.open');
        $this->aOrder['Order']['Payed'] = false;
        $this->aOrder['Order']['Shipped'] = false;
        $this->aOrder['Order']['Comments'] = isset($this->aOrder['Order']['Comments']) ? $this->aOrder['Order']['Comments'] : '';
        try {
            foreach (array('DatePurchased', 'ImportDate') as $sDateType) {
                if (array_key_exists($sDateType, $this->aOrder['Order'])) {
                        $oDateTime = new DateTime($this->aOrder['Order'][$sDateType], new DateTimeZone('Europe/Berlin'));
                        $oDateTime->setTimeZone(new DateTimeZone(date_default_timezone_get()));
                        $this->aOrder['Order'][$sDateType] = $oDateTime->format('Y-m-d H:i:s');
                    }
                }
        }catch (Exception $oEx) {
            // timezone not found
        }
        return $this;
    }
    
    protected function normalizeMpSpecific () {
        $this->aOrder['MPSpecific']['InternalComment'] = 	
            sprintf(MLI18n::gi()->get('ML_GENERIC_AUTOMATIC_ORDER_MP_SHORT'), MLModul::gi()->getMarketPlaceName(false) )."\n".
            MLI18n::gi()->get('ML_LABEL_MARKETPLACE_ORDER_ID').': '.$this->aOrder['MPSpecific']['MOrderID']."\n\n"
            .$this->aOrder['Order']['Comments']
        ;
        if (!isset($aOrder['MPSpecific']['ML_LABEL_NOTE'])) {
            $this->aOrder['MPSpecific'] = array_merge(array('ML_LABEL_NOTE' => 'ML_GENERIC_AUTOMATIC_ORDER'), $this->aOrder['MPSpecific']);
        }
        return $this;
    }
    
    /**
     * in some api order data , there is no Payment data , that cause default payment method in configuration is not used 
     * in this function we fill these data 
     * we use it in meinpaket and rakuten
     * @return ML_Modul_Helper_Model_Service_OrderData_Normalize
     */
    protected function addMissingTotal(){        
        $this->aOrder['Totals'] = array_key_exists('Totals', $this->aOrder) ? $this->aOrder['Totals'] : array();
        $blPaymentExist = false;
        foreach ($this->aOrder['Totals'] as &$aTotal) {
            if ($aTotal['Type'] == 'Payment') {
                $blPaymentExist = true;
            }
        }
        if(!$blPaymentExist){
            $this->aOrder['Totals'][] = array(
                'Type' => 'Payment',
                'Tax' => 0
            );
        }
        return $this;
    }

}
