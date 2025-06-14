<?php
/**
 *  2007-2024 PayPal
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 2007-2024 PayPal
 *  @author 202 ecommerce <tech@202-ecommerce.com>
 *  @copyright PayPal
 *  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

require_once __DIR__ . '/../Services/FormatterPaypal.php';

class OrderBuilder implements BuilderInterface
{
    /** @var Context*/
    protected $context;

    protected $module;

    protected $items = [];

    protected $wrappings = [];

    protected $products = [];

    protected $useTax = null;

    /** @var FormatterPaypal */
    protected $formatter;

    public function __construct(Context $context)
    {
        $this->context = $context;
        $this->module = Module::getInstanceByName('paypal');
        $this->formatter = new FormatterPaypal();
    }

    public function build()
    {
        $currency = $this->getCurrency();
        $items = $this->getItems($currency);
        $payer = $this->getPayer();
        $shippingInfo = $this->getShippingInfo();

        $body = [
            'intent' => $this->getIntent(),
            'application_context' => $this->getApplicationContext(),
            'purchase_units' => [
                [
                    'amount' => $this->getAmount($currency),
                    'items' => $items,
                ],
            ],
        ];

        if (empty($payer) == false) {
            $body['payer'] = $payer;
        }

        if (empty($shippingInfo) == false) {
            $body['purchase_units'][0]['shipping'] = $shippingInfo;
        }

        return $body;
    }

    /**
     * @return string
     */
    protected function getCurrency()
    {
        return $this->module->getPaymentCurrencyIso();
    }

    protected function getItems($currency, $cache = false)
    {
        if ($cache && false === empty($this->items)) {
            return $this->items;
        }

        $this->items = array_merge(
            $this->getProductItems($currency, $cache),
            $this->getWrappingItems($currency, $cache)
        );

        return $this->items;
    }

    protected function isUseTax()
    {
        if (is_null($this->useTax) == false) {
            return $this->useTax;
        }

        $this->useTax = (int) Configuration::get('PS_TAX') == 1;

        return $this->useTax;
    }

    /**
     * @param $currency string Iso code
     *
     * @return array
     */
    protected function getProductItems($currency, $cache = false)
    {
        if ($cache && false === empty($this->products)) {
            return $this->products;
        }

        $items = [];
        $products = $this->context->cart->getProducts();

        foreach ($products as $product) {
            $item = [];
            $priceExcl = $this->formatter->formatPrice($product['price']);
            $priceIncl = $this->formatter->formatPrice($product['price_wt']);

            if ($this->isUseTax()) {
                $productTax = $this->formatter->formatPrice($priceIncl - $priceExcl, null, false);
            } else {
                $productTax = 0;
            }

            if (isset($product['attributes']) && (empty($product['attributes']) === false)) {
                $product['name'] .= ' - ' . $product['attributes'];
            }

            if (isset($product['reference']) && false === empty($product['reference'])) {
                $product['name'] .= ' Ref: ' . $product['reference'];
            }

            $item['name'] = $this->formatter->formatPaypalString($product['name']);
            $item['sku'] = $product['id_product'];
            $item['unit_amount'] = [
                'currency_code' => $currency,
                'value' => $priceExcl,
            ];
            $item['tax'] = [
                'currency_code' => $currency,
                'value' => $productTax,
            ];
            $item['quantity'] = $product['quantity'];

            $items[] = $item;
        }

        $this->products = $items;

        return $items;
    }

    protected function getPayer()
    {
        $payer = [];
        $customer = new Customer($this->context->cart->id_customer);

        if (Validate::isLoadedObject($customer) === false) {
            return $payer;
        }

        $payer['name'] = [
            'given_name' => $this->formatter->formatPaypalString($customer->firstname),
            'surname' => $this->formatter->formatPaypalString($customer->lastname),
        ];
        $payer['email'] = $customer->email;

        if ($this->context->cart->isVirtualCart() === false) {
            $payer['address'] = $this->getAddress();
        }

        return $payer;
    }

    protected function getAmount($currency)
    {
        $items = $this->getItems($currency, true);
        $subTotalExcl = 0;
        $shippingTotal = $this->formatter->formatPrice($this->getTotalShipping());
        $subTotalTax = 0;
        $discountTotal = $this->formatter->formatPrice(abs($this->getDiscount()));
        $handling = $this->getHandling($currency);

        foreach ($items as $item) {
            $subTotalExcl += (float) $item['unit_amount']['value'] * (float) $item['quantity'];
            $subTotalTax += (float) $item['tax']['value'] * (float) $item['quantity'];
        }

        $subTotalExcl = $this->formatter->formatPrice($subTotalExcl, null, false);
        $subTotalTax = $this->formatter->formatPrice($subTotalTax, null, false);
        $totalOrder = $this->formatter->formatPrice(
            $subTotalExcl + $subTotalTax + $shippingTotal + $handling - $discountTotal,
            null,
            false
        );

        $amount = [
            'currency_code' => $currency,
            'value' => $totalOrder,
            'breakdown' => [
                'item_total' => [
                    'currency_code' => $currency,
                    'value' => $subTotalExcl,
                ],
                'shipping' => [
                    'currency_code' => $currency,
                    'value' => $shippingTotal,
                ],
                'tax_total' => [
                    'currency_code' => $currency,
                    'value' => $subTotalTax,
                ],
                'discount' => [
                    'currency_code' => $currency,
                    'value' => $discountTotal,
                ],
                'handling' => [
                    'currency_code' => $currency,
                    'value' => $handling,
                ],
            ],
        ];

        return $amount;
    }

    protected function getWrappingItems($currency, $cache = false)
    {
        if ($cache && false === empty($this->wrappings)) {
            return $this->wrappings;
        }

        $items = [];

        if ($this->context->cart->gift && $this->context->cart->getGiftWrappingPrice()) {
            $item = [];
            $priceIncl = $this->context->cart->getGiftWrappingPrice(true);
            $priceExcl = $this->context->cart->getGiftWrappingPrice(false);

            if ($this->isUseTax()) {
                $tax = $priceIncl - $priceExcl;
            } else {
                $tax = 0;
            }

            $item['name'] = $this->module->l('Gift wrapping', get_class($this));
            $item['sku'] = $this->context->cart->id;
            $item['unit_amount'] = [
                'currency_code' => $currency,
                'value' => $this->formatter->formatPrice($priceExcl),
            ];
            $item['tax'] = [
                'currency_code' => $currency,
                'value' => $this->formatter->formatPrice($tax),
            ];
            $item['quantity'] = 1;

            $items[] = $item;
        }

        $this->wrappings = $items;

        return $items;
    }

    /**
     * @return array
     */
    protected function getApplicationContext()
    {
        $applicationContext = [
            'shipping_preference' => 'SET_PROVIDED_ADDRESS',
            'user_action' => 'PAY_NOW',
        ];

        if ($this->context->cart->isVirtualCart()) {
            $applicationContext['shipping_preference'] = 'NO_SHIPPING';
        }

        return $applicationContext;
    }

    /**
     * @return array
     */
    protected function getShippingInfo()
    {
        if ($this->context->cart->id_address_delivery == false || $this->context->cart->isVirtualCart()) {
            return [];
        }

        $shippingInfo = [
            'address' => $this->getAddress(),
        ];

        $name = $this->getShippingName();

        if (false == empty($name)) {
            $shippingInfo['name'] = $name;
        }

        return $shippingInfo;
    }

    protected function getShippingName()
    {
        if (empty($this->context->cart->id_address_delivery)) {
            return [];
        }

        $address = new Address($this->context->cart->id_address_delivery);

        if (false == Validate::isLoadedObject($address)) {
            return [];
        }

        return [
            'full_name' => implode(
                ' ',
                [
                    $address->firstname,
                    $address->lastname,
                ]
            ),
        ];
    }

    /**
     * @return array
     */
    protected function getAddress($address = null)
    {
        if (!$address) {
            $address = new Address($this->context->cart->id_address_delivery);
        }

        $country = new Country($address->id_country);

        $addressArray = [
            'address_line_1' => $this->formatter->formatPaypalString($address->address1),
            'address_line_2' => $this->formatter->formatPaypalString($address->address2),
            'postal_code' => $address->postcode,
            'country_code' => Tools::strtoupper($country->iso_code),
            'admin_area_2' => $this->formatter->formatPaypalString($address->city),
        ];

        if ($address->id_state) {
            $state = new State($address->id_state);
            $addressArray['admin_area_1'] = Tools::strtoupper($state->iso_code);
        }

        return $addressArray;
    }

    /**
     * @return string
     */
    protected function getIntent()
    {
        return 'CAPTURE';
    }

    protected function getHandling($currency)
    {
        $handling = 0;
        $discounts = $this->context->cart->getCartRules();

        if (empty($discounts)) {
            return $handling;
        }

        foreach ($discounts as $discount) {
            if ($discount['value_real'] < 0) {
                $handling += $this->method->formatPrice(abs($discount['value_real']));
            }
        }

        return $this->formatter->formatPrice($handling);
    }

    /**
     * @return float
     */
    protected function getDiscount()
    {
        $discountTotal = $this->context->cart->getOrderTotal($this->isUseTax(), Cart::ONLY_DISCOUNTS);

        return $discountTotal;
    }

    protected function getTotalShipping()
    {
        return $this->context->cart->getOrderTotal($this->isUseTax(), Cart::ONLY_SHIPPING);
    }
}