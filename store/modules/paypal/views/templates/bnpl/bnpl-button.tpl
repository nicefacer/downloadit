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

{include file='../_partials/javascript.tpl'}

<div id="paypal-bnpl-container"></div>

<script>
    setTimeout(

        function() {
            var sdkNamespace = '{$sdkNamespace|escape:'htmlall':'UTF-8'}';
            var order = {$order|json_encode nofilter};

            function waitPaypalSDKIsLoaded() {
                if (window[sdkNamespace] === undefined || window['BNPL'] === undefined) {
                    setTimeout(waitPaypalSDKIsLoaded, 200);
                    return;
                }
                (new BNPL({
                    paypal: window[sdkNamespace],
                    validationController: '{$validationController}'
                })).render(
                    '#paypal-bnpl-container',
                    order,
                    function() {
                        document.querySelector('.paypal-button-container').remove();
                    }
                );
            }

            waitPaypalSDKIsLoaded();
        },
        0
    );
</script>