{*
* 2007-2024 PayPal
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author 2007-2024 PayPal
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

<style>

  [installment-container] {
    padding: 10px;
  }

</style>

{include file='./banner.tpl'}

<script>
    Banner.prototype.updateAmount = function() {
        var quantity = parseFloat(document.querySelector('input[name="qty"]').value);
        var price = 0;

        if (typeof priceWithDiscountsDisplay !== 'undefined') {
            price = priceWithDiscountsDisplay;
        } else {
            price = parseFloat(document.querySelector('[itemprop="price"]').getAttribute('content'));
        }

        this.amount = quantity * price;
    };

    Banner.prototype.refresh = function() {
        var amount = paypalBanner.amount;
        this.updateAmount();

        if (amount != this.amount) {
            this.initBanner();
        }
    };

    window.addEventListener('load', function() {
        window.paypalBanner = new Banner({
            layout: layout,
            placement: placement,
            container: '[paypal-banner-message]',
            textAlign: 'center'
        });
        paypalBanner.updateAmount();
        paypalBanner.initBanner();

        document.querySelector('.box-info-product').addEventListener('click', function() {
            setTimeout(paypalBanner.refresh.bind(paypalBanner), 1000);
        });

        document.querySelector('.box-info-product').addEventListener('change', paypalBanner.refresh.bind(paypalBanner));
    });
</script>
