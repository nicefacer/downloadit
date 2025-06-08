<?php

/**
 * File PrestaBayPayment.php
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
class PrestaBayPayment extends PaymentModule
{
    public $active = true;
    public $name = "prestabay";

    /**
     * Experemental rewrite order import, removing not needed cache and hooks
     *
     * @param CartCore $cart
     * @param $id_order_state
     * @param $amount_paid
     * @param string $payment_method
     * @param null $message
     * @param array $extra_vars
     * @param null $currency_special
     * @param bool $dont_touch_amount
     * @param bool $secure_key
     * @param Shop|null $shop
     */
    public function validateEbayOrder($cart, $id_order_state, $amount_paid, $payment_method = 'Unknown',
                                      $message = null, $extra_vars = array(), $currency_special = null, $dont_touch_amount = false,
                                      $secure_key = false, Shop $shop = null)
    {
        // This functionality only used on PrestaShop 1.5 and 1.6
        $customer = new Customer($cart->id_customer);
        if (!Validate::isLoadedObject($customer)) {
            throw new \Exception('Invalid customer for ebay order');
        }
        $currency = new Currency($cart->id_currency);

        /** @var OrderCore $order */
        $order = new Order();
        $order->id_cart = (int)$cart->id;
        $order->id_customer = (int)$cart->id_customer;
        $order->id_currency = (int)$cart->id_currency;
        $order->id_lang = (int)$cart->id_lang;
        $order->id_carrier = (int)$cart->id_carrier;
        $order->id_address_invoice = (int)$cart->id_address_invoice;
        $order->id_address_delivery = (int)$cart->id_address_delivery;

        $order->secure_key = pSQL($customer->secure_key);
        if (!$order->secure_key) {
            $order->secure_key = md5(microtime(true));
        }
        $order->payment = $payment_method;
        $order->module = $this->name;
        $order->recyclable = $cart->recyclable;
        $order->conversion_rate = $currency->conversion_rate;

        $order->total_paid_real = 0;
        $order->total_products = (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt = (float)$cart->getOrderTotal(true, Cart::ONLY_PRODUCTS);
        $order->total_discounts = (float)$cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS);
        $order->total_shipping = (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING);
        $order->total_wrapping = (float)abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
        $order->total_paid = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $order->total_paid_tax_excl = (float)$cart->getOrderTotal(false, Cart::BOTH);
        $order->total_paid_tax_incl = (float)$cart->getOrderTotal(true, Cart::BOTH);
        $order->total_shipping_tax_excl = (float)$cart->getOrderTotal(false, Cart::ONLY_SHIPPING);
        $order->total_shipping_tax_incl = (float)$cart->getOrderTotal(true, Cart::ONLY_SHIPPING);

        $order->id_order_state = $id_order_state;
        $order->shipping_number = '';
        $order->delivery_number = 0;
        $order->exported = '';

        $order->carrier_tax_rate = 0; // @todo tax from order

        $id_warehouse = null; // @todo not supported

        $order->reference = Order::generateReference();
        $order->current_state = (int)$id_order_state;

        $id_shop = (int)Configuration::get('PS_SHOP_DEFAULT');
        $shop = new Shop($id_shop);
        $order->id_shop = $shop->id;
        $order->id_shop_group = $shop->id_shop_group;

        $order->invoice_date = '0000-00-00 00:00:00';
        $order->delivery_date = '0000-00-00 00:00:00';

        if (!($products = $cart->getProducts())) {
            throw new Exception('Obtain products from card failed');
        }


        $order->add(true, null);
        if (!Validate::isLoadedObject($order)) {
            throw new Exception('Order creation failed');
        }

        $total_wrapping_tax_incl = 0;
        $total_wrapping_tax_excl = 0;

        foreach ($products as $product) {

            $reference = trim((string)$product['reference']);

            $id_product = (int)$product['id_product'];
            $id_product_attribute = $product['id_product_attribute'] ? (int)$product['id_product_attribute'] : null;


            $productQuantity = Product::getRealQuantity($id_product, $id_product_attribute, $id_warehouse, $order->id_shop);
            $quantityInStock = $productQuantity - $product['cart_quantity'];

            StockAvailable::updateQuantity($id_product, $id_product_attribute, $product['cart_quantity'] * -1, $order->id_shop);

            $product['id_tax'] = 0;
            $product['tax'] = null;
            $product['rate'] = 0;

            $quantity = (int)$product['cart_quantity'];

            $unit_price_tax_incl = (float)$product['price'];
            $unit_price_tax_excl = (float)$product['price'];

            $total_price_tax_incl = (float)Tools::ps_round($unit_price_tax_incl, 2) * $quantity;
            $total_price_tax_excl = (float)Tools::ps_round($unit_price_tax_excl, 2) * $quantity;

            $unit_wrapping_tax_excl = 0;
            $unit_wrapping_tax_incl = 0;

            $total_wrapping_tax_incl += $unit_wrapping_tax_incl;
            $total_wrapping_tax_excl += $unit_wrapping_tax_excl;

            $taxes = (float)Tools::ps_round(($total_price_tax_incl - $total_price_tax_excl) * $quantity, 2);

            $product_name = $product['name'] . ((isset($product['attributes']) && $product['attributes'] != null) ? ' - ' . $product['attributes'] : '');
            $realReference = $reference;

            // Order Detail entry
            /** @var OrderDetailCore $order_detail */
            $order_detail = new OrderDetail();

            $order_detail->id_order = (int)$order->id;

            $order_detail->product_name = $product_name;
            $order_detail->product_id = $id_product;
            $order_detail->product_attribute_id = $id_product_attribute;

            $order_detail->product_quantity = (int)$product['cart_quantity'];
            $order_detail->product_quantity_in_stock = (int)$quantityInStock;

            $order_detail->product_price = (float)$unit_price_tax_excl;
            $order_detail->product_ean13 = $product['ean13'] ? $product['ean13'] : null;
            $order_detail->product_upc = $product['upc'] ? $product['upc'] : null;
            $order_detail->product_reference = $realReference;
            $order_detail->product_supplier_reference = $product['supplier_reference'] ? $product['supplier_reference'] : null;
            $order_detail->product_weight = (float)Tools::ps_round($product['id_product_attribute'] ? $product['weight_attribute'] : $product['weight'], 2);

            $order_detail->tax_name = $product['tax'];
            $order_detail->tax_rate = (float)$product['rate'];
            $order_detail->ecotax = $product['ecotax'];

            $order_detail->total_price_tax_incl = (float)$total_price_tax_incl;
            $order_detail->total_price_tax_excl = (float)$total_price_tax_excl;
            $order_detail->unit_price_tax_incl = (float)$unit_price_tax_incl;
            $order_detail->unit_price_tax_excl = (float)$unit_price_tax_excl;

            $order_detail->id_shop = (int)$order->id_shop;
            $order_detail->id_warehouse = (int)$id_warehouse;

            $order_detail->add();

            if (!Validate::isLoadedObject($order_detail)) {
                throw new Exception('Failed add order details', mysql_error());
            }
        }

        if ($order->id_carrier) {
            $order_carrier = new OrderCarrier();
            $order_carrier->id_order = (int)$order->id;
            $order_carrier->id_carrier = $order->id_carrier;
            $order_carrier->weight = (float)$order->getTotalWeight();
            $order_carrier->shipping_cost_tax_excl = $order->total_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = $order->total_shipping_tax_incl;
            $order_carrier->add();
        }

        $orderStatus = new OrderState((int)$id_order_state);

        if (Validate::isLoadedObject($orderStatus)) {

            Hook::exec('actionValidateOrder', array(
                'cart' => $cart,
                'order' => $order,
                'customer' => $customer,
                'currency' => $currency,
                'orderStatus' => $orderStatus
            ));

            foreach ($cart->getProducts() as $product) {
                if ($orderStatus->logable)
                    ProductSale::addProductSale((int)$product['id_product'], (int)$product['cart_quantity']);
            }
        }

        // Set the order status
        $new_history = new OrderHistory();
        $new_history->id_order = (int)$order->id;
        $new_history->changeIdOrderState((int)$id_order_state, $order, true);
        $new_history->addWithemail(true);

        $order = new Order($order->id);

        if (!Validate::isLoadedObject($order)) {
            throw new Exception('Failed load order after status change');
        }

        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
            if (StockAvailable::dependsOnStock($id_product)) {
                foreach ($products as $key => $product) {
                    StockAvailable::synchronize((int)$product['id_product'], $order->id_shop);
                }
            }
        }

        $this->currentOrder = (int)$order->id;

        return $this->currentOrder;
    }
}