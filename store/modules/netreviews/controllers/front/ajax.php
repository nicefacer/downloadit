<?php

require_once _PS_MODULE_DIR_ . 'netreviews/netreviews.php';

class NetreviewsAjaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if (Tools::getValue('ajax')) {
            $consent = Tools::getValue('collect_consent');
            $idShop = (int)Tools::getValue('idShop');
            $groupName = Tools::getValue('groupName');
            $idCustomer = (int)Tools::getValue('idCustomer');
            echo $consent;

            $netreviews = new Netreviews();
            $order = $netreviews->getLastIdOrder($idShop, $idCustomer);
            $idOrder = $order['id_order'];

            if($consent == 'no') {
                if(Configuration::get('AV_MULTILINGUE', null, null, $idShop) == 'checked') {
                    $key = 'AV_CONSENT_ANSWER_NO'.$groupName;
                } else {
                    $key = 'AV_CONSENT_ANSWER_NO';
                }
                echo $key;

                if(Configuration::hasKey($key, null, null, $idShop)){
                    $value = json_decode(Configuration::get($key, null, null, $idShop, false), true);
                    $values = array_values($value);
                    $values[] = (int)$idOrder;
                } else {
                    $values = [];
                    $values[] = (int)$idOrder;
                }
                Configuration::updateValue($key, json_encode($values), false, null, $idShop);
            }
            
            die(json_encode($consent));
        }
    }
}