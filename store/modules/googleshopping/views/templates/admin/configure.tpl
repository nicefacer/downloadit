{*
* Copyright (c) 2016, Google Inc. All rights reserved.
*
* NOTICE OF LICENSE
*
* Redistribution and use in source and binary forms, with or without
* modification, are permitted provided that the following conditions are met:
*
* 1. Redistributions of source code must retain the above copyright notice,
* this list of conditions and the following disclaimer.
*
* 2. Redistributions in binary form must reproduce the above copyright notice,
* this list of conditions and the following disclaimer in the documentation
* and/or other materials provided with the distribution.
*
* 3. Neither the name of the copyright holder nor the names of its
* contributors may be used to endorse or promote products derived from this
* software without specific prior written permission.
*
* THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
* AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
* IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
* ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
* LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
* CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
* SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
* INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
* CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
* ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
* POSSIBILITY OF SUCH DAMAGE.
*}

<div class="panel">
  <div>
    <img src="https://www.gstatic.com/shopping-platforms/google-shopping-logo.svg" alt="Google Shopping" id="shopping-logo" />
  </div>

  <div id="left-column">
      {capture "string_one"}{l s='{link_start}Google Shopping{link_end} helps businesses tap into the power of customer intent to reach the right people with relevant products ads when it matters the most. Use this module to upload your store and product data to Google Merchant Center and make it available to Google Shopping and other Google services, allowing you to reach millions of new customers searching for what you sell.' mod='googleshopping'}{/capture}
      <p>{$smarty.capture.string_one|replace:'{link_start}':'<a href="https://www.youtube.com/watch?v=xIil1YlBMOw" target="_blank">'|replace:'{link_end}':'</a>'}</p>

      <p>{l s='Connect your PrestaShop account to Merchant Center to begin importing your product data through the store API. After you’ve linked your PrestaShop and Merchant Center accounts, any store information that’s updated in your PrestaShop store will also be updated in Merchant Center.' mod='googleshopping'}</p>

      {capture "string_two"}{l s='Get started by clicking "Sign Up" below. A new PrestaShop API account will be automatically generated and your store information and credentials will be submitted to Merchant Center through a secure connection. Follow the sign up prompts to create a Merchant Center account -- if you don’t create a new account, your API information will be deleted after 60 days. {link_start}Learn more{link_end}' mod='googleshopping'}{/capture}
      <p>{$smarty.capture.string_two|replace:'{link_start}':'<a href="https://support.google.com/merchants/answer/6351218" target="_blank">'|replace:'{link_end}':'</a>'}</p>

      <p>
        <a class="signup-button" href="launch-button-signup-link" target="_blank">{l s='Launch' mod='googleshopping'}</a>
      </p>
  </div>

  <div id="right-column">
    <img src="https://www.gstatic.com/shopping-platforms/shopping-ads.png" alt="Google Shopping Ads" id="shopping-ads" /> 
  </div>
  <br/>
