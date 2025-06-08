<?php
/**
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
 *
 *  @author    Google
 *  @copyright Copyright 2016 Google Inc.
 *  @license   BSD
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

include('lib/phpseclib/Crypt/RSA.php');
include('lib/phpseclib/Math/BigInteger.php');

class GoogleShopping extends Module
{
    const MERCHANT_CENTER_PUBLIC_KEY = 'google_shopping.pem';
    const SIGNUP_PAGE = 'https://merchants.google.com/PlatformsSignup';

    private static $read_resources = array("addresses", "carriers", "cart_rules", "carts",
      "categories", "combinations", "configurations",
      "content_management_system", "countries", "currencies", "customizations",
      "deliveries", "groups", "guests", "image_types", "images", "languages",
      "manufacturers", "order_carriers", "order_details", "order_discounts",
      "order_histories", "order_invoices", "order_payments", "order_slip",
      "order_states", "orders", "price_ranges", "product_customization_fields",
      "product_feature_values", "product_features", "product_option_values",
      "product_options", "product_suppliers", "products", "shop_groups",
      "shop_urls", "shops", "specific_price_rules", "specific_prices",
      "states", "stock_availables", "stock_movement_reasons", "stock_movements",
      "stocks", "stores", "tags", "tax_rule_groups", "tax_rules",
      "translated_configurations", "weight_ranges", "zones");

    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'googleshopping';
        $this->tab = 'advertising_marketing';
        $this->version = '1.0.2';
        $this->author = 'Google';
        $this->need_instance = 1;
        $this->module_key = 'ed07e17ba10c9df587e557b3797a38e7';

        // This module is compliant with bootstrap (PrestaShop 1.6).
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Google Shopping');
        $this->description = $this->l('Use your Prestashop store data to promote your products on Google.');

        $this->confirmUninstall = $this->l('Uninstalling the module will cause the Google Shopping offers to expire.');
    }

    /**
     * TODO: Setup auto-update if needed.
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */

    public function install()
    {
        Configuration::updateValue('GOOGLE_SHOPPING_ACCOUNT_EMAIL', null);
        Configuration::updateValue('GOOGLE_WEBMASTER_TAG_CONTENT', null);
        Configuration::updateValue('GOOGLE_WEBSERVICE_KEY_ID', null);

        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHeader');
    }

    public function uninstall()
    {
        $this->deleteWebserviceKey();
        Configuration::deleteByName('GOOGLE_SHOPPING_ACCOUNT_EMAIL');
        Configuration::deleteByName('GOOGLE_WEBMASTER_TAG_CONTENT');
        Configuration::deleteByName('GOOGLE_WEBSERVICE_KEY_ID');

        return parent::uninstall();
    }

    /**
     * Load the configuration form.
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submit'.$this->name)) == true) {
            $this->postProcess();
        }

        // Load the backstore css and js.
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');

        $this->context->smarty->assign('module_dir', $this->_path);
        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');
        $signup_link = $this->getSignupLink();
        return str_replace('launch-button-signup-link', $signup_link, $output);
    }

    /**
     * Returns link to signup on Google Merchant Center, which contains the
     * encrypted key.
     */
    private function getSignupLink()
    {
        $encrypted_key = $this->getEncryptedWebserviceKey();
        if ($encrypted_key == '') {
            return '';
        }

        return self::SIGNUP_PAGE.
            '?platform=prestashop'.
            '&path='.$this->getAPIPath().
            '&encrypted_key='.urlencode($encrypted_key);
    }

    private function getAPIPath()
    {
        return urlencode(Tools::getHttpHost(true).__PS_BASE_URI__.'api/');
    }

    /**
     * Activates the webservice and returns an encrypted webservice key with
     * appropriate permissions. If the key doesn't exist it is created.
     *
     * The encrypted key is returned as a base64 encoded string.
     */
    private function getEncryptedWebserviceKey()
    {
        $webservice_key = $this->getWebserviceKeyCode();
        if ($webservice_key == '') {
            return '';
        }

        $public_key = Tools::file_get_contents(
            $this->local_path . self::MERCHANT_CENTER_PUBLIC_KEY
        );
        if ($public_key == '') {
            return '';
        }

        $rsa = new Crypt_RSA();
        $rsa->loadKey($public_key);

        $rsa->setEncryptionMode(CRYPT_RSA_ENCRYPTION_OAEP);
        $rsa->setHash('sha256');
        $rsa->setMGFHash('sha256');

        $encrypted_webservice_key = $rsa->encrypt($webservice_key);
        return base64_encode($encrypted_webservice_key);
    }

    /**
     * Activates the webservice and returns a webservice key with appropriate
     * permissions. If the key doesn't exist it is created.
     */
    private function getWebserviceKeyCode()
    {
        $webservice_key = $this->getWebserviceKey();
        return is_null($webservice_key) ? '' : $webservice_key->key;
    }

    /**
     * Returns the webservice key. If the key doesn't exist it is created and
     * appropriate permissions are set.
     */
    private function getWebserviceKey()
    {
        $key_id = Configuration::get('GOOGLE_WEBSERVICE_KEY_ID', null);
        if ($key_id == '') {
            $webservice_key = $this->createWebserviceKey();
        } else {
            // Load the key.
            $webservice_key = new WebserviceKey($key_id);
            if ($webservice_key->key == '') {
                return $this->createWebserviceKey();
            }
        }
        // Set appropriate permissions
        $this->setPermissions($webservice_key);
        // Make sure htaccess is properly setup.
        Tools::generateHtaccess();
        // Enable Webservice
        Configuration::updateValue('PS_WEBSERVICE', '1');

        return $webservice_key;
    }

    /**
     * Returns a new webservice key for Google Shopping.
     */
    private function createWebserviceKey()
    {
        // Create a webservice key.
        $webservice_key = new WebserviceKey();
        $webservice_key->description = 'Google Shopping API Key';
        $webservice_key->key = $this->getRandomKey();
        $webservice_key->active = true;
        $webservice_key->add();
        Configuration::updateValue(
            'GOOGLE_WEBSERVICE_KEY_ID',
            $webservice_key->id
        );
        return $webservice_key;
    }


    /**
     * Sets the permissions for the given webservice key.
     *
     * Permissions:
     * GET, HEAD: For all resources
     * ALL: for Configurations since we need to send metatag for website
     * verification.
     */
    private function setPermissions($webservice_key)
    {
        $permissions = array();
        $ressources = WebserviceRequest::getResources();
        foreach ($ressources as $resource_name => $resource) {
            if ($resource_name == 'configurations') {
                $permissions[$resource_name] = array('GET' => 1, 'PUT' => 1,
                    'POST' => 1, 'DELETE' => 1, 'HEAD' => 1);
            } else if (in_array($resource_name, self::$read_resources)) {
                $permissions[$resource_name] = array('GET' => 1, 'HEAD' => 1);
            }
        }
        WebserviceKey::setPermissionForAccount($webservice_key->id, $permissions);
    }

    /**
     * Deletes the webservice key.
     */
    private function deleteWebserviceKey()
    {
        $key_id = Configuration::get('GOOGLE_WEBSERVICE_KEY_ID', null);
        if ($key_id != '') {
            $webservice_key = new WebserviceKey($key_id);
            $webservice_key->delete();
        }
    }

    /**
     * Disable the webservice key if it exists.
     */
    private function disableWebserviceKey()
    {
        $key_id = Configuration::get('GOOGLE_WEBSERVICE_KEY_ID', null);
        if ($key_id != '') {
            $webservice_key = new WebserviceKey($key_id);
            $webservice_key->active = false;
        }
    }

    private function getRandomKey($length = 32)
    {
        $random = '';
        /* There are no O/0 in the codes in order to avoid confusion */
        $chars = "123456789ABCDEFGHIJKLMNPQRSTUVWXYZ";
        $min = new Math_BigInteger(0);
        $max = new Math_BigInteger(Tools::strlen($chars) - 1);
        for ($i = 0; $i < $length; $i++) {
            $random .= $chars[$min->random($max)->toString()];
        }
        return $random;
    }

    /**
     * Adds the metatag for verification to front-office.
     */
    public function hookHeader($params)
    {
        if (Configuration::get('GOOGLE_WEBMASTER_TAG_CONTENT')) {
            return '<meta name="google-site-verification" content="'
                .Tools::safeOutput(
                    Configuration::get('GOOGLE_WEBMASTER_TAG_CONTENT')
                ).'" />';
        }
    }

    public function hookDisplayHeader()
    {
        if (Configuration::get('GOOGLE_WEBMASTER_TAG_CONTENT')) {
            return '<meta name="google-site-verification" content="'
                .Tools::safeOutput(
                    Configuration::get('GOOGLE_WEBMASTER_TAG_CONTENT')
                ).'" />';
        }
    }
}
