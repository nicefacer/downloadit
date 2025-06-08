<?php

/**
 * File ImportModel.php
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
 * eBay Listener Itegration with PrestaShop e-commerce platform.
 * Adding possibilty list PrestaShop Product dirrectly to eBay.
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2015 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Order_ImportModel
{

    const HOOK_UPDATE_ORDER_BLOCK_KEY = 'PRESTABAY_UPDATE_ORDER';
    private $_languageId;

    public function importNewOrder($orderInformation, $itemsInformation)
    {
        $this->_languageId = $languageId = $this->_prepareProductToCart($itemsInformation);

        if (Configuration::get('INVEBAY_ORDER_FAKE_EMAIL') == '1') {
            $orderInformation['buyer_email'] = 'fake_' . $orderInformation['buyer_email'];
        }

        $customerId = $this->_getCustomerId($orderInformation['buyer_email'], $orderInformation['buyer_address']);

        $addressId = $this->_createOrderAddress($customerId, $orderInformation['buyer_address']);


        $orderId = $this->_createOrder($customerId, $addressId, $languageId, $orderInformation, $itemsInformation);
        return $orderId;
    }

    public function changeStatusToPaid($orderId)
    {
        $orderStatus = _PS_OS_PAYMENT_;
        Registry::set(Order_ImportModel::HOOK_UPDATE_ORDER_BLOCK_KEY, true);
        $orderHistory = new OrderHistory();
        $orderHistory->id_order = $orderId;
        $orderHistory->changeIdOrderState((int) $orderStatus, (int) $orderId);
        $orderHistory->add(true);
    }

    public function changeStatusToShipped($orderId)
    {
        $orderStatus = _PS_OS_SHIPPING_;
        Registry::set(Order_ImportModel::HOOK_UPDATE_ORDER_BLOCK_KEY, true);
        $orderHistory = new OrderHistory();
        $orderHistory->id_order = $orderId;
        $orderHistory->changeIdOrderState((int) $orderStatus, (int) $orderId);
        $orderHistory->add(true);
    }

    public function changeOrderStatusRelatedToEbayOrder($orderId, $orderInformation)
    {
        $orderStatus = $this->_getOrderStatusBasedOnEbayStatus($orderInformation);
        // Block execute Hook on update order status by get update from ebay
        Registry::set(Order_ImportModel::HOOK_UPDATE_ORDER_BLOCK_KEY, true);
        // Check that order that we want to change already don't have such order

        $orderStatusesToCheck = array($orderStatus);
        if ($orderStatus == _PS_OS_SHIPPING_) {
            // When order status is shipping, we also should check for delivery
            $orderStatusesToCheck[] = _PS_OS_DELIVERED_;
        }

        $isStatusInHistory = false;
        foreach ($orderStatusesToCheck as $statusToCheck) {
            // Check for all status of order that can be present
            if ($this->_isStatusPresentInOrderHistory($orderId, $statusToCheck)) {
                $isStatusInHistory = true;
            }
        }
        if (!$isStatusInHistory) {
            // when status not present, add it to order history
            $orderHistory = new OrderHistory();
            $orderHistory->id_order = $orderId;
            $orderHistory->changeIdOrderState((int) $orderStatus, $orderId);
            $orderHistory->add(true);
        }
    }

    /**
     * Return true when such status present on order history
     *
     * @param int $orderId
     * @param int $newStatus
     * @return boolean true when status having in order history, false otherwise
     */
    protected function _isStatusPresentInOrderHistory($orderId, $newStatus)
    {
        $idOrderHistory = Db::getInstance()->getValue('
                SELECT id_order_history FROM ' . _DB_PREFIX_ . 'order_history as h
                WHERE id_order = ' . (int) $orderId . ' AND id_order_state = ' . (int) $newStatus);
        return $idOrderHistory !== false;
    }

    /**
     * Change connected PrestaShop order status releated to connected eBay Order.
     * Change order status and price
     * @param int $orderId
     * @param array $orderInformation
     * @param array $productList
     */
    public function updatePrestaOrderRelatedToEbayOrderChange($orderId, $orderInformation, $productList)
    {
        // Remove not linked product
        $this->_prepareProductToCart($productList);

        if (CoreHelper::isPS15()) {
            // 3) Recalculate all price regarding eBay price
            $this->_updateOrderPricePS15($orderId, $orderInformation, $productList);
        } else {
            // 3) Recalculate all price regarding eBay price
            $this->_updateOrderPrice($orderId, $orderInformation, $productList);
        }

        $this->changeOrderStatusRelatedToEbayOrder($orderId, $orderInformation);
    }

    /** ============= Customer Block ==================== */
    protected function _getCustomerId($customerEmail, $customerAddress)
    {
        $customerId = Customer::customerExists($customerEmail, true);
        if ($customerId > 0) {
            $customer = new Customer($customerId);
        } else {
            $customer = $this->_createCustomer($customerEmail, $customerAddress);
        }
        if (CoreHelper::isPS15()) {
            Context::getContext()->customer = $customer;
        }

        return $customer->id;
    }

    protected function _createCustomer($customerEmail, $customerAddress)
    {
        $lastName = trim($customerAddress['lastname']) . " eB";
        if (strlen($lastName) > 32) {
            $lastName = substr($lastName, 0, 31);
        }
        $listOfLastNames = explode(" ", $lastName);
        if (isset($listOfLastNames[0])) {
            $lastName = $listOfLastNames[0];
        }

        $firstName = trim($customerAddress['firstname']);
        if (strlen($firstName) > 32) {
            $firstName = substr($firstName, 0, 31);
        }

        $listOfFirstNames = explode(" ", $firstName);
        if (isset($listOfFirstNames[0])) {
            $firstName = $listOfFirstNames[0];
        }
        // Remove not allowed chars from FirstName and LastName
        $firstName = preg_replace('/[0-9!<>,;?=+()@#"{}_$%:]+/', ' ', $firstName);
        $lastName = preg_replace('/[0-9!<>,;?=+()@#"{}_$%:]+/', ' ', $lastName);

        if (strlen($firstName) == 0) {
            $firstName = "Noname";
        }

        if (strlen($lastName) == 0) {
            $lastName = "Noname";
        }

        $password = $this->_getPassword();

        $customer = new Customer();
        $customer->email = $customerEmail;
        $customer->passwd = $password;
        $customer->newsletter = 0;
        $customer->lastname = $lastName;
        $customer->firstname = $firstName;
        $customer->active = 1;
        $customer->id_default_group = 3;
        $customer->optin = 0;

        $customer->add();

        // $this->_sendConfirmationMail($customer, $password);

        return $customer;
    }

    protected function _sendConfirmationMail(Customer $customer, $password)
    {
        return Mail::Send(
            $this->_languageId,
            'account',
            Mail::l('Welcome!'),
            array(
                '{firstname}' => $customer->firstname,
                '{lastname}' => $customer->lastname,
                '{email}' => $customer->email,
                '{passwd}' => $password,
            ),
            $customer->email,
            $customer->firstname . ' ' . $customer->lastname
        );
    }

    protected function _getPassword()
    {
        $pwd = str_shuffle('abcefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890@#%$*');
        return substr($pwd, 0, 8);
    }

    /** ============= Address Block ==================== */
    protected function _createOrderAddress($customerId, $customerAddress)
    {
        $address = new Address();
        $address->id_customer = $customerId;
        $address->id_country = 0;
        if ($customerAddress['country'] != "") {
            if (CoreHelper::isPS15()) {
                $address->id_country = (int)Country::getByIso($customerAddress['country']);
            } else {
                $address->id_country = (int)$this->_getByIso($customerAddress['country']);
            }
        }
        if ($address->id_country == 0) {
            $address->id_country = 1;
        }


        $firstName = trim($customerAddress['firstname']);
        if (strlen($firstName) > 32) {
            $firstName = substr($firstName, 0, 31);
        }
        $listOfFirstNames = explode(" ", $firstName);
        if (isset($listOfFirstNames[0])) {
            $firstName = $listOfFirstNames[0];
        }

        $lastName = trim($customerAddress['lastname']);
        if (strlen($lastName) > 32) {
            $lastName = substr($lastName, 0, 28) . "...";
        }


        // Remove not allowed chars from FirstName and LastName
        $firstName = preg_replace('/[0-9!<>,;?=+()@#"{}_$%:]+/', ' ', $firstName);
        $lastName = preg_replace('/[0-9!<>,;?=+()@#"{}_$%:]+/', ' ', $lastName);

        if (strlen($firstName) == 0) {
            $firstName = "Noname";
        }

        if (strlen($lastName) == 0) {
            $lastName = "Noname";
        }


        $address->firstname = $firstName;
        $address->lastname = $lastName;
        $address->alias = 'eBay Imported';


        $address->address1 = isset($customerAddress['street'][0]) ? $customerAddress['street'][0] : 'N/A';
        $address->address2 = isset($customerAddress['street'][1]) ? $customerAddress['street'][1] : '';
        if (isset($customerAddress['state']) && $customerAddress['state'] != "") {
            // When have information about state import it to customer address
            $stateResult = method_exists('State', 'getIdByIso') ? State::getIdByIso($customerAddress['state'], $address->id_country) : 0;
            if ((int) $stateResult > 0) {
                $address->id_state = (int) $stateResult;
            } else {
                $stateResult = State::getIdByName($customerAddress['state']);
                if ((int) $stateResult > 0) {
                    $address->id_state = (int) $stateResult;
                } else {
                    $address->address2 .= " ".$customerAddress['state'];
                }
            }
        }

        // Prepare post code for match PrestaShop order field
        $postCode = preg_replace('/[^a-zA-Z 0-9-]+/', '', $customerAddress['postal_code']);
        if (strlen($postCode) > 12) {
            $postCode = substr($postCode, 0, 12);
        }

        $address->postcode = $postCode;
        $address->city = (isset($customerAddress['city']) && $customerAddress['city'] != "" )?$customerAddress['city']:"N/A";
        $phone = (isset($customerAddress['phone']) && $customerAddress['phone'] != "")?$customerAddress['phone']: "000000";
        // Remove all not validated pass chars
        $phone = preg_replace("/[^0-9+. ()-]/", "", $phone);
        if (strlen($phone) > 16) {
            $phone = substr($phone, 0, 16);
        }
        $address->phone = $phone;
        if (property_exists($address, 'phone_mobile')) {
            $address->phone_mobile = $phone;
        }

        $address->active = 1;
        $address->other = 'Ebay';
        $address->add();

        return $address->id;
    }

    /** ============= Cart Block ==================== */

    /**
     * Remove not connected product and return language used for cart
     *
     * @param array $productList
     * @return int language used for cart
     */
    protected function _prepareProductToCart(&$productList)
    {
        $cartLanguage = false;
        foreach ($productList as $key => $product) {
            if (is_null($product['presta_id']) || is_null($product['presta_lang_id'])) {
                unset($productList[$key]);
            } else if (!$cartLanguage) {
                $cartLanguage = $product['presta_lang_id'];
            }
        }
        return $cartLanguage;
    }

    protected function _createOrder($customerId, $addressId, $languageId, $orderInformation, $productList)
    {
        Order_LogModel::addLogMessage($orderInformation['id'], L::t('Start Import PrestaShop order'));
        if (CoreHelper::isPS15()) {
            // Clear cart and customer from context
            Context::getContext()->cart = null;
            Context::getContext()->customer = null;
        }

        $this->cleanCustomerCarts($customerId);

        $customer = new Customer($customerId);

        $skey = md5(time());
        $cart = new Cart();
        $cart->id_customer = $customerId;
        $cart->id_address_invoice = $addressId;
        $cart->id_address_delivery = $addressId;
        $cart->secure_key = $skey;
        $shippingId = $this->_getShippingCarrierId($orderInformation['shipping_method']);
        $cart->id_carrier = $shippingId;
        $cart->id_lang = $languageId;
        $cart->id_currency = Currency::getIdByIsoCode($orderInformation['currency']);

        if (CoreHelper::isPS15()) {
            $cart->recyclable = 0;
            $cart->gift = 0;
        }
        $cart->add();
        if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
            Order_LogModel::addLogMessage($orderInformation['id'], L::t('– Create new Cart'));
        }

        if (CoreHelper::isPS15()) {
            Context::getContext()->cart = $cart;
            Context::getContext()->customer = $customer;
        }

        foreach ($productList as $key => $eBayProduct) {
            $productInstance = new Product((int) $eBayProduct['presta_id']);
            $orderQty = $eBayProduct['qty'];
            if ($productInstance->minimal_quantity > 1) {
                // When we have minimum qty we sell pack of product
                // So Total qty will be more, but price for one will be less
                $orderQty = $orderQty * $productInstance->minimal_quantity;
                $productList[$key]['qty'] = $orderQty;
                $productList[$key]['price'] = $productList[$key]['price'] / $productInstance->minimal_quantity;
                $eBayProduct = $productList[$key];
            }
            if (!$productInstance->id) {
                throw new Exception("Order product not found in PrestaShop");
            }

            if (!$productInstance->checkQty($eBayProduct['qty'])) {
                throw new Exception("Product #".$eBayProduct['presta_id']." QTY not enough for create PrestaShop order");
            }

            if (!$productInstance->available_for_order) {
                throw new Exception("Product #". $eBayProduct['presta_id'].' not available for order');
            }

            $z = $cart->updateQty(
                $eBayProduct['qty'], $eBayProduct['presta_id'], isset($eBayProduct['presta_attr_id']) ? $eBayProduct['presta_attr_id'] : null);
            if ($z === -1) {
                throw new Exception(L::t("Product not match PrestaShop minimum QTY condition"));
            }
        }
        if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
            Order_LogModel::addLogMessage(
                $orderInformation['id'],
                L::t('– Add all products to the Cart.') .
                'Cart: '. count($cart->getProducts()). ', Product:' . count($productList)
            );
        }
        $cart->update();

        $this->_disableCustomerEmail($customerId);
        if (count($cart->getProducts()) == 0) {
            // No products in cart, problem create order
            throw new Exception("Products not added to cart");
        }

        $this->_disableCustomerEmail($customerId);
        $reportingSave = error_reporting(E_ERROR || E_WARNING);
        // Convert Cart to Order and validate it
        $carTotal = (float) $cart->getOrderTotal(true, 3);
        $total = $carTotal;
        $payment = new PrestaBayPayment();
        // There some hack to correct calculate price for order
        // First we create order without payment and finally change order status to correct one

        $oldPaymentStatus = $orderInformation['status_payment'];
        $oldShippingStatus = $orderInformation['status_shipping'];
        $orderInformation['status_payment'] = Order_OrderModel::STATUS_PAYMENT_NONE;
        $orderInformation['status_shipping'] = Order_OrderModel::STATUS_SHIPPING_NONE;

        if (CoreHelper::isPS15()) {
            // We need to clear cache!
            $carTmp = new Cart((int)$cart->id);
            $carTmp->getDeliveryOption(null, false, false);
            $carTmp->getDeliveryOptionList(null, true);
            $carTmp->getPackageList(true);
        }

//        if (CoreHelper::isPS15()) {
//            $this->_createPS15Order();
//        } else {
        try {
            $orderPrefix = '[ebay ';
            if (!empty($orderInformation['sales_record_number'])) {
                $orderPrefix .= 'SR:' . $orderInformation['sales_record_number'];
            } else {
                $orderPrefix .= '#' . $orderInformation['id'];
            }
            $orderPrefix .= ']';

            $isExperementalImport = trim(Configuration::get('INVEBAY_ORDER_NEW_IMPORT')) === '1';
            if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
                Order_LogModel::addLogMessage($orderInformation['id'], L::t('- Before validate order'));
            }
            if (CoreHelper::isPS15() && $isExperementalImport) {
                // custom code for create order in PS15 and PS16
                $payment->validateEbayOrder($cart, $this->_getOrderStatusBasedOnEbayStatus($orderInformation), $total, $orderPrefix . $orderInformation['payment_method'], null, array(), (int)$cart->id_currency, false, $skey);
            } else {
                $payment->validateOrder((int)$cart->id, $this->_getOrderStatusBasedOnEbayStatus($orderInformation), $total, $orderPrefix . $orderInformation['payment_method'], null, array(), (int)$cart->id_currency, false, $skey);
            }
            if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
                Order_LogModel::addLogMessage($orderInformation['id'], L::t('- After validated order'));
            }
        } catch (Exception $ex) {
            error_reporting($reportingSave);
            $this->_enableCustomerEmail($customerId, $orderInformation['buyer_email']);
            throw new Exception($ex->getMessage());
        }
        $orderInformation['status_payment'] = $oldPaymentStatus;
        $orderInformation['status_shipping'] = $oldShippingStatus;
        error_reporting($reportingSave);
        $this->_enableCustomerEmail($customerId, $orderInformation['buyer_email']);

        $orderId = $payment->currentOrder;

        // Code to correctly set order payent method name
        if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
            Order_LogModel::addLogMessage($orderInformation['id'], L::t('- Update payment method name'));
        }
        $idOrderPayment = Db::getInstance()->getValue("
              SELECT p.id_order_payment FROM " . _DB_PREFIX_ . "orders o
              INNER JOIN " . _DB_PREFIX_ . "order_payment p ON o.reference = p.order_reference
              WHERE o.id_order =".(int)$orderId);

        if ($idOrderPayment > 0) {
            Db::getInstance()->query(
                "UPDATE " . _DB_PREFIX_ . "order_payment
                SET payment_method='".pSQL($orderPrefix . $orderInformation['payment_method'])."'
                WHERE id_order_payment=".(int)$idOrderPayment);

        }

        if (CoreHelper::isPS15()) {
            if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
                Order_LogModel::addLogMessage($orderInformation['id'], L::t('- Update shipping method name'));
            }
            // PS15 HACK for set correct shipping
            $order = new Order($orderId);
            // We need remove all orde carrier for current order
            $removeSql = "DELETE FROM " . _DB_PREFIX_ . 'order_carrier' . " WHERE id_order = " . (int) $orderId;
            Db::getInstance()->Execute($removeSql);

            // Create own carrier that use our shipping method
            $order_carrier = new OrderCarrier();
            $order_carrier->id_order = $orderId;
            $order_carrier->id_carrier = (int) $shippingId;
            $order_carrier->weight = (float) $cart->getTotalWeight();
            $order_carrier->shipping_cost_tax_incl = $order_carrier->shipping_cost_tax_excl = $orderInformation['shipping_cost'];

            $order_carrier->save();
        }

        if (isset($orderInformation['message']) && $orderInformation['message'] != "") {
            $message = new Message();
            $message->id_employee = (int) ($customerId);
            $message->message = htmlentities($orderInformation['message'], ENT_COMPAT, 'UTF-8');
            $message->id_order = $orderId;
            $message->private = 1;
            $message->add();
        }

        $this->changeOrderStatusRelatedToEbayOrder($orderId, $orderInformation);

        if (CoreHelper::isPS15()) {
            // Clear cart and customer from context
            Context::getContext()->cart = null;
            Context::getContext()->customer = null;
        }

        if (Configuration::get('INVEBAY_EXTENDED_ORDER_LOG') == '1') {
            Order_LogModel::addLogMessage($orderInformation['id'], L::t('- Recalculate price & tax'));
        }
        if (CoreHelper::isPS15()) {
            // 3) Recalculate all price regarding eBay price
            $this->_updateOrderPricePS15($orderId, $orderInformation, $productList);
        } else {
            // 3) Recalculate all price regarding eBay price
            $this->_updateOrderPrice($orderId, $orderInformation, $productList);
        }

        $updateOrderCarrierId = "UPDATE " . _DB_PREFIX_ . 'orders' . " SET id_carrier = ".((int) $shippingId)." WHERE id_order = " . (int) $orderId;
        Db::getInstance()->Execute($updateOrderCarrierId);

        return $orderId;
    }

    /**
     * Try to remove all customer carts that not finished (don't link to order)
     *
     * @param $customerId
     */
    protected function cleanCustomerCarts($customerId)
    {
        $customerCarts = Cart::getCustomerCarts($customerId, false);
        foreach ($customerCarts as $singleCart) {
            $tmpCart = new Cart($singleCart['id_cart']);
            $tmpCart->delete();
        }
    }

    /**
     * Return status for order.
     * This status depends from have or not eBay Payment, shipping information
     *
     * @param array $orderInformation
     */
    protected function _getOrderStatusBasedOnEbayStatus($orderInformation)
    {
        $logMessage = L::t('eBay Order status has') . " ";
        $defaultStatus = _PS_OS_CHEQUE_; // Waiting for payment (order not complete)
        if ($orderInformation['status_checkout'] == Order_OrderModel::STATUS_CHECKOUT_INCOMPLETE) {
            Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('Checkout incomplete'));
            return _PS_OS_CHEQUE_;
        }

        if ($orderInformation['status_shipping'] == Order_OrderModel::STATUS_SHIPPING_COMPLETE) {
            // Order is shipped
            Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('Shipping completed'));
            return _PS_OS_SHIPPING_;
        }

        if (in_array($orderInformation['status_payment'], array(Order_OrderModel::STATUS_PAYMENT_NONE, Order_OrderModel::STATUS_PAYMENT_PENDING))) {
            if ($orderInformation['payment_method'] == 'PayPal') {
                // Awaiting PayPal Payment
                Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('PayPal payment Pending'));
                return _PS_OS_PAYPAL_;
            } else {
                Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('Payment Pending'));
                return _PS_OS_CHEQUE_;
            }
        }

        if ($orderInformation['status_payment'] == Order_OrderModel::STATUS_PAYMENT_FAIL) {
            Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('Payment Fail'));
            return _PS_OS_ERROR_;
        }

        if ($orderInformation['status_payment'] == Order_OrderModel::STATUS_PAYMENT_COMPLETE) {
            // Payment by buyer completed
            Order_LogModel::addLogMessage($orderInformation['id'], $logMessage . L::t('Payment completed'));
            return _PS_OS_PAYMENT_;
        }
    }

    protected function _updateOrderPrice($orderId, $orderInformation, $orderProducts)
    {
        foreach ($orderProducts as $eBayProduct) {
            // Get shop tax
            $taxRate = Db::getInstance()->getValue('
                SELECT `tax_rate` FROM `' . _DB_PREFIX_ . 'order_detail`
                    WHERE `id_order` = ' . (int) $orderId . '
                        AND `product_id` = ' . (int) $eBayProduct['presta_id'] . '
                        AND `product_attribute_id` = ' . (int) $eBayProduct['presta_attr_id']);
            // Decrise product price by shop tax
            Db::getInstance()->autoExecute(_DB_PREFIX_ . 'order_detail', array(
                'product_price' => floatval($eBayProduct['price'] / (1 + $taxRate / 100)),
                'reduction_percent' => 0,
                'reduction_amount' => 0
                    ), 'UPDATE', '`id_order` = ' . (int) $orderId .
                    ' AND `product_id` = ' . (int) $eBayProduct['presta_id'] .
                    ' AND `product_attribute_id` = ' . (int) $eBayProduct['presta_attr_id']);
        }

        $product_wt = (float) Db::getInstance()->getValue('SELECT SUM(`product_price`) FROM `' . _DB_PREFIX_ . 'order_detail` WHERE `id_order` = ' . (int) $orderId);

        if ($orderInformation['paid'] == 0) {
            $orderInformation['paid'] =  $product_wt + $orderInformation['shipping_cost'];
        }

        // Update order price based on eBay sold price
        $updateOrder = array();
        $updateOrder['total_paid'] = $updateOrder['total_paid_real'] = (float) $orderInformation['paid'];
        $updateOrder['total_products'] = $product_wt;
        $updateOrder['total_products_wt'] = (float) ($orderInformation['paid'] - $orderInformation['shipping_cost']);
        $updateOrder['total_shipping'] = (float) $orderInformation['shipping_cost'];

        Db::getInstance()->autoExecute(_DB_PREFIX_ . 'orders', $updateOrder, 'UPDATE', '`id_order` = ' . (int) $orderId);
    }

    protected function _isEUCountry($countryCode)
    {
        return in_array($countryCode, array("AT","BE", "BG", "CY", "CZ", "DK", "EE", "FI", "FR", "DE", "EL", "HU", "IE",
                "IT", "LV", "LT", "LU", "MT", "NL", "PL", "PT", "RO", "SK", "SI", "ES", "SE", "UK"));
    }

    public function recalculateOrderPricePS15($orderInformation, $orderProducts)
    {
        if ((int)$orderInformation['presta_order_id'] > 0) {
            $this->_updateOrderPricePS15($orderInformation['presta_order_id'], $orderInformation, $orderProducts);
        }
    }

    protected function _updateOrderPricePS15($orderId, $orderInformation, $orderProducts)
    {
//        $countryCode = $orderInformation['buyer_address']['country'];
//        if ($this->_isEUCountry($countryCode)) {
//            $productTax = 0.20;
//            $shippingTax = 0.20;
//        } else {
            $productTax = ((float)Configuration::get('INVEBAY_ORDER_TAX'))/100;
            $shippingTax = ((float)Configuration::get('INVEBAY_ORDER_SHIPPING_TAX'))/100;
//        }
        $isUSTax = false;
        if (isset($orderInformation['tax']['percent']) && $orderInformation['tax']['percent'] > 0) {
            $usTaxPercent = ((float)$orderInformation['tax']['percent'])/100;
            $productTax = $usTaxPercent;
            if (isset($orderInformation['amount']) && $orderInformation['amount'] > 0) {
                if (isset($orderInformation['tax']['shipping_included']) && $orderInformation['tax']['shipping_included'] == 1) {
                    $shippingTax = $usTaxPercent;
                    $orderInformation['shipping_cost'] = $orderInformation['shipping_cost']*(1+$shippingTax);
                }
                $isUSTax = true;
            } else {
                $shippingTax = $usTaxPercent;
            }
        }
        $productTaxPercent = 1-$productTax;
        $shippingTaxPercent = 1-$shippingTax;

        $totalProductExcl = 0;
        $totalProductIncl = 0;
        foreach ($orderProducts as $eBayProduct) {
            // Get shop tax
//            $taxRate = Db::getInstance()->getValue('
//                SELECT `tax_rate` FROM `' . _DB_PREFIX_ . 'order_detail`
//                    WHERE `id_order` = ' . (int) $orderId . '
//                        AND `product_id` = ' . (int) $eBayProduct['presta_id'] . '
//                        AND `product_attribute_id` = ' . (int) $eBayProduct['presta_attr_id']);
            if (!$isUSTax) {
                // Decrise product price by shop tax
                $productPrice = floatval($eBayProduct['price']);
                $productPriceExcl = round(floatval($eBayProduct['price'] / (1 + $productTax)), 4);
            } else {
                $productPrice = round(floatval($eBayProduct['price'] * (1 + $productTax)), 4);
                $productPriceExcl = floatval($eBayProduct['price']);
            }
            $totalProductExcl+=$productPriceExcl * $eBayProduct['qty'];
            $totalProductIncl+=$productPrice * $eBayProduct['qty'];

            Db::getInstance()->autoExecute(_DB_PREFIX_ . 'order_detail', array(
                    'product_price' => $productPriceExcl,
                    'unit_price_tax_excl' => $productPriceExcl,
                    'unit_price_tax_incl' => $productPrice,
                    'total_price_tax_excl' => $productPriceExcl * $eBayProduct['qty'],
                    'total_price_tax_incl' => $productPrice * $eBayProduct['qty'],
                    'reduction_percent' => 0,
                    'reduction_amount' => 0,
                    'tax_rate' => $productTax
                ), 'UPDATE', '`id_order` = ' . (int) $orderId .
                ' AND `product_id` = ' . (int) $eBayProduct['presta_id'] .
                ' AND `product_attribute_id` = ' . (int) $eBayProduct['presta_attr_id']);

            $id_order_detail = Db::getInstance()->getValue(          '
             SELECT `id_order_detail` FROM `' . _DB_PREFIX_ .'order_detail`
                 WHERE `id_order` = ' . (int)$orderId . '
                     AND `product_id` = ' . (int)$eBayProduct['presta_id'] . '
                     AND `product_attribute_id` = ' . (int)$eBayProduct['presta_attr_id']
            );

            Db::getInstance()->autoExecute(
                _DB_PREFIX_ . 'order_detail_tax',
                array(
                    'unit_amount'  => ($productPrice - $productPriceExcl),
                    'total_amount' => ($productPrice -  $productPriceExcl) * $eBayProduct['qty'],

                ),
                'UPDATE',
                '`id_order_detail` = ' . (int)$id_order_detail
            );
        }

        $order = new Order($orderId);
        $order->carrier_tax_rate = $shippingTax*100;

        // Get product with taxes
        $product_wt = (float) Db::getInstance()->getValue('SELECT SUM(`total_price_tax_incl`) FROM `' . _DB_PREFIX_ . 'order_detail` WHERE `id_order` = ' . (int) $orderId);

        $order->total_shipping
            = $order->total_shipping_tax_incl
            = (float) $orderInformation['shipping_cost'];

        $order->total_shipping_tax_excl = round($order->total_shipping/(1+$shippingTax), 4);

        if ($orderInformation['paid'] == 0) {
            $orderInformation['paid'] = $product_wt + $order->total_shipping;
        }

        $order->total_products = $totalProductExcl; //round($product_wt/(1 + $productTax), 4);
        $order->total_products_wt = $product_wt = $totalProductIncl; //(float) ($orderInformation['paid'] - $orderInformation['shipping_cost']);

        $order->total_paid_real
            = $order->total_paid_tax_incl
            = $order->total_paid
            = (float) $orderInformation['paid'];

        $order->total_paid_tax_excl = $order->total_shipping_tax_excl +  $order->total_products;

        // discount
        $order->total_discounts = (float) 0;

        $order->update();

        $invoicePaymentResult = Db::getInstance()->ExecuteS('SELECT `id_order_invoice`, `id_order_payment` FROM `' . _DB_PREFIX_ . 'order_invoice_payment`  WHERE `id_order` = ' . (int) $orderId);
        if (count($invoicePaymentResult) > 0) {
            $invoicePaymentResult = reset($invoicePaymentResult);
            $invoiceId = $invoicePaymentResult['id_order_invoice'];
            $paymentId = $invoicePaymentResult['id_order_payment'];
            if ($invoiceId > 0) {
                $orderInvoice = new OrderInvoice($invoiceId);
                if (!$orderInvoice->id) {
                    return;
                }
                $orderInvoice->total_paid_tax_incl = (float)$orderInformation['paid'];

                $orderInvoice->total_products = round($totalProductExcl, 4);
                $orderInvoice->total_products_wt = round($totalProductIncl, 4);

                $orderInvoice->total_shipping_tax_incl =  (float)$orderInformation['shipping_cost'];
                $orderInvoice->total_shipping_tax_excl = round($orderInvoice->total_shipping_tax_incl/(1 + $shippingTax), 4);

                $orderInvoice->total_paid_tax_excl = $orderInvoice->total_products + $orderInvoice->total_shipping_tax_excl;

                $orderInvoice->update();
            }
            if ($paymentId > 0) {
                $orderPayment = new OrderPayment($paymentId);
                if (!$orderPayment->id) {
                    return;
                }
                $orderPayment->amount = (float)$orderInformation['paid'];
                $orderPayment->update();
            }
        }
    }

    protected function _disableCustomerEmail($customerId)
    {
        Db::getInstance()->autoExecute(_DB_PREFIX_ . 'customer', array('email' => 'disable-notify'), 'UPDATE', 'id_customer = ' . $customerId);
        $customerClear = new Customer();
        if (method_exists($customerClear, 'clearCache')) {
            $customerClear->clearCache(true);
        }
    }

    protected function _enableCustomerEmail($customerId, $customerEmail)
    {
        Db::getInstance()->autoExecute(_DB_PREFIX_ . 'customer', array('email' => pSQL($customerEmail)), 'UPDATE', 'id_customer = ' . $customerId);
    }

    /** ========= Shipping Block ============= */

    /**
     * Get connected to eBay Shipping PS Carrier ID
     * @param string $carrierName
     * @return int
     */
    protected function _getShippingCarrierId($carrierName = "eBay Shipping")
    {
        // Convert shipping name (from eBay) to good read format
        $importShippingModel = new ImportShippingModel();
        $shippingTitle = $importShippingModel->getShippingNameById($carrierName);
        $carrierName = ($shippingTitle == false) ? $carrierName : $shippingTitle;

        // Append eBay to Shipping
        $originalName = $carrierName;
        $carrierName != "eBay Shipping" && $carrierName = 'eBay Shipping: ' . $carrierName;
        if (strlen($carrierName) > 64) {
            // PrestaShop has limit to length of Carier title
            if (strlen($originalName) < 61) {
                $carrierName = 'eB:' . $originalName;
            } else {
                $carrierName = substr($carrierName, 0, 61) . "...";
            }
        }

        $carrierId = $this->_getPrestaShopCarrierIdByName($carrierName);

        if (!$carrierId) {
            $carrier = new Carrier();
            $carrier->name = $carrierName;
            $carrier->active = 0;
            if (CoreHelper::isPS15()) {
                $carrier->delay = CoreHelper::createMultiLangField('eBay');
            }
            $carrier->add();

            $carrierId = $carrier->id;
        }

        return $carrierId;
    }

    protected function _getPrestaShopCarrierIdByName($carrierName)
    {
        $listOfCarriers = Db::getInstance()->ExecuteS('SELECT `id_carrier` FROM `' . _DB_PREFIX_ . 'carrier` WHERE `deleted` = 0 AND `name` = "' . pSQL($carrierName) . '"');

        if (is_array($listOfCarriers)) {
            $singleCarrierInfo = reset($listOfCarriers);
        } else {
            return false;
        }

        return isset($singleCarrierInfo['id_carrier']) ? $singleCarrierInfo['id_carrier'] : false;
    }

    protected function _getByIso($iso)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
		SELECT `id_country`
		FROM `'._DB_PREFIX_.'country`
		WHERE `iso_code` = \''.pSQL(strtoupper($iso)).'\'');

        return $result['id_country'];
    }
}
