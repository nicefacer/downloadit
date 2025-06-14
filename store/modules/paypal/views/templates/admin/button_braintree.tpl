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
*  @license	http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*
*}

<p>{l s='Activate 3D Secure?' mod='paypal'}</p>
<input type="radio" id="threedsecure_on" name="use_threedsecure" value="1"{if $PayPal_check3Dsecure == 1} checked="checked"{/if}/> <label for="threedsecure_on">{l s='Yes' mod='paypal'}</label><br />
<input type="radio" id="threedsecure_off" name="use_threedsecure" value="0"{if $PayPal_check3Dsecure == 0} checked="checked"{/if}/> <label for="threedsecure_off">{l s='No' mod='paypal'}</label>

{if $Braintree_Configured}
<p style="color:#008000;">
{if $PayPal_sandbox_mode}
	{l s='Your Braintree account is configured in sandbox mode. You can join the Braintree support on 08 05 54 27 14' mod='paypal'}
{else}
	{l s='Your Braintree account is configured in live mode. You can sell on Euro only. You can join the Braintree support on 08 05 54 27 14' mod='paypal'}
{/if}
</p>
{/if}
