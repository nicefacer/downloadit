{*
* 2007-2024 PrestaShop
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
 *  @author 2007-2024 PrestaShop SA <contact@prestashop.com>
 *  @author 2014-2022 202 ecommerce <tech@202-ecommerce.com>
*  @copyright PayPal
*  @license http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
*
*}

        <p>
            <img src="/modules/paypal/views/img/logos/FR_pp_cc_mark_74x46.jpg" alt="{l s='PayPal' mod='paypal'}" class="paypal_logo" />
            <br />{l s='You have chosen to pay with PayPal.' mod='paypal'}
            <br/><br />
        {l s='Here is a short summary of your order:' mod='paypal'}
        </p>

        <p class="shipping_address col-sm-3 column grid_2">
            <strong>{l s='Shipping address' mod='paypal'}</strong><br/>
            {AddressFormat::generateAddress($address_shipping, $patternRules, '<br/>')}
            
        </p>
        <p class="billing_address col-sm-3">
            <strong>{l s='Billing address' mod='paypal'}</strong><br/>
            {AddressFormat::generateAddress($address_billing, $patternRules, '<br/>')}
            
        </p>

        <div class="clearfix"></div>
        
        <div class="col-sm-12 cart_container">
            <strong class="title">{l s='Your cart' mod='paypal'}</strong>
            <table id="cart_summary" class="table table-bordered stock-management-on">
            <thead>
                <tr>
                    <th>{l s='Image' mod='paypal'}</th>
                    <th>{l s='Name' mod='paypal'}</th>
                    <th>{l s='Quantity' mod='paypal'}</th>
                </tr>
            </thead>
            {foreach from=$cart->getProducts() item=product}
                <tr>
                    <td>
                        <img src="{$link->getImageLink('small', $product.id_image, $cart_image_size)|escape:'htmlall':'UTF-8'}" alt="">
                    </td>
                    <td>
                        {$product.name|escape:'htmlall':'UTF-8'}<br/>
                        {if isset($product.attributes) && $product.attributes}<small>{$product.attributes|escape:'html':'UTF-8'}</small>{/if}
                    </td>
                    <td>
                        {$product.quantity|escape:'htmlall':'UTF-8'}
                    </td>
                </tr>
            {/foreach}
            </table>
        </div>

        <p class="paypal_total_amount">
            - {l s='The total amount of your order is' mod='paypal'}
            <span id="amount" class="price"><strong>{$total|escape:'htmlall':'UTF-8'}</strong></span> {if $use_taxes == 1}{l s='(tax incl.)' mod='paypal'}{/if}
        </p>
        <p>
            - {l s='We accept the following currency to be sent by PayPal:' mod='paypal'}&nbsp;<b>{$currency->name|escape:'htmlall':'UTF-8'}</b>
        </p>

        
<link rel="stylesheet" href="{$base_dir|escape:'htmlall':'UTF-8'}/modules/paypal/views/css/paypal-cart_summary.css">
{if $useStyle14}
    <link rel="stylesheet" href="{$base_dir|escape:'htmlall':'UTF-8'}/modules/paypal/views/css/paypal_1_4_paypal-cart_summary.css">
{/if}

{if $useStyle15}
    <link rel="stylesheet" href="{$base_dir|escape:'htmlall':'UTF-8'}/modules/paypal/views/css/paypal_1_5_paypal-cart_summary.css">
{/if}