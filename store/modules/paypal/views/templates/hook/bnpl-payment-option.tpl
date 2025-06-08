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

<style>
    .paypal-button-container
    {ldelim}
        border: 1px solid #d6d4d4;
        -moz-border-radius: 4px;
        -webkit-border-radius: 4px;
        border-radius: 4px;
        font-size: 17px;
        line-height: 23px;
        color: #333;
        font-weight: bold;
        padding: 15px 90px 15px 15px;
        letter-spacing: -1px;
        position: relative;
        margin-bottom: 10px;
    {rdelim}

    .paypal-button-container .wrapper
    {ldelim}
        max-width: 400px;
    {rdelim}
</style>

{if $smarty.const._PS_VERSION_ >= 1.6}

    <div class="row">
        <div class="col-xs-12">
            <div class="paypal-button-container">
                <div class="wrapper">
                    {$bnpl nofilter}
                </div>
            </div>
        </div>
    </div>


{else}
    <p class="payment_module paypal-bnpl">
        <a href="javascript:void(0)">
            {l s='Pay Later' mod='paypal'}
        </a>
    </p>

{/if}



