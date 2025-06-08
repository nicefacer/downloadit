<?php
/**
 * usercentrics
 *
 * @author    silbersaiten <info@silbersaiten.de>
 * @copyright 2020 silbersaiten
 * @license   See joined file licence.txt
 * @category  Module
 * @support   silbersaiten <support@silbersaiten.de>
 * @version   1.0.0
 * @link      http://www.silbersaiten.de
 */

class UsercentricsApi
{
    private $module;

    public $errors = array();

    private $data;
    private $package_data;

    public function __construct($module)
    {
        $this->module = $module;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPackageData()
    {
        return $this->package_data;
    }

    public function authenticate($username, $password, $shop_domain, $shop_url)
    {
        $params = array('action' => 'auth', 'lang' => Context::getContext()->language->iso_code, 'email' => $username, 'password' => $password, 'domain' => $shop_domain, 'url' => $shop_url);
        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data']['setting_id'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    private function addMandatoryParameters()
    {
        return array(
            'lang' => Context::getContext()->language->iso_code,
            'email' => Configuration::get('USERCENTRICS_USERNAME'),
            'password' => Configuration::get('USERCENTRICS_PASSWORD'),
            'domain' => $this->module->getShopDomain()
        );
    }

    public function getSetting($setting_id, $language = 'en')
    {
        $params = array(
            'action' => 'getSetting',
            'setting_id' => $setting_id,
            'language' => $language
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            $this->data = $res['data']['setting'];
            $this->package_data = $res['package_data'];
            return $this->data;
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function getSettingLanguagesAvailable($setting_id)
    {
        $params = array(
            'action' => 'getSettingLanguagesAvailable',
            'setting_id' => $setting_id,
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            $this->data = $res['data']['setting'];
            return $this->data;
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function getLanguageSetting($setting_id, $language = 'en')
    {
        $params = array(
            'action' => 'getLanguageSetting',
            'setting_id' => $setting_id,
            'language' => $language
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data']['setting'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function saveAvailableLanguages($setting_id, $languages = array())
    {
        $params = array(
            'action' => 'saveAvailableLanguages',
            'setting_id' => $setting_id,
            'languages' => implode(',', $languages)
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data']['setting'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function addTechnology($setting_id, $url)
    {
        $params = array(
            'action' => 'addTechnology',
            'setting_id' => $setting_id,
            'url' => $url
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function getConsentTemplates($search_str = '')
    {
        $params = array(
            'action' => 'getConsentTemplates',
            'search_str' => $search_str,
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }


    public function saveLanguageSetting($setting_id, $language, $data)
    {
        $params = array(
            'action' => 'saveLanguageSetting',
            'setting_id' => $setting_id,
            'language' => $language,
            'data' => $data
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }


    public function saveSetting($setting_id, $data)
    {
        $params = array(
            'action' => 'saveSetting',
            'setting_id' => $setting_id,
            'data' => $data
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function addVendor($setting_id, $template_id, $category_slug)
    {
        $params = array(
            'action' => 'addVendor',
            'setting_id' => $setting_id,
            'template_id' => $template_id,
            'category_slug' => $category_slug
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function deleteVendor($setting_id, $template_id)
    {
        $params = array(
            'action' => 'deleteVendor',
            'setting_id' => $setting_id,
            'template_id' => $template_id,
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function addCategory($setting_id, $category_slug, $is_essential = false, $label = array(), $description = array(), $disable_autotrans = array())
    {
        $params = array(
            'action' => 'addCategory',
            'setting_id' => $setting_id,
            'category_slug' => $category_slug,
            'is_essential' => $is_essential,
        );

        foreach ($label as $lang => $value_lang) {
            $params['label[' . $lang . ']'] = $value_lang;
        }
        foreach ($description as $lang => $value_lang) {
            $params['description[' . $lang . ']'] = $value_lang;
        }
        foreach ($disable_autotrans as $lang => $label_lang) {
            $params['disable_autotrans[' . $lang . ']'] = $label_lang;
        }

        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    public function deleteCategory($setting_id, $category_slug)
    {
        $params = array(
            'action' => 'deleteCategory',
            'setting_id' => $setting_id,
            'category_slug' => $category_slug,
        );
        $params = array_merge($params, $this->addMandatoryParameters());

        $res = $this->httpRequest($this->module->partner_endpoint, $params);

        if (isset($res['success']) && $res['success'] == 'true') {
            return $res['data'];
        } else {
            $this->errors[] = isset($res['error_message']) ? $res['error_message'] : 'Error';
            return false;
        }
    }

    private function httpRequest($endpoint, $params, $json = false, $token = null)
    {
        $headers = ['User-Agent: prestashop-usercentrics'];
        if ($json == false) {
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
        } else {
            $headers[] = 'Content-Type: application/json';
        }

        if (null !== $token) {
            $headers[] = "Authorization: bearer $token";
        }

        $postdata = http_build_query(
            $params
        );

        $msg = "\n\n--------------\n" . date('Y-m-d H:i:s');
        $msg .= "\nEndpoint: " . $endpoint;
        $msg .= "\nParams: " . $postdata;

        if (false === $data = @Tools::file_get_contents($endpoint, false, stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => $headers,
                    'content' => $postdata,
                ]
            ]))) {
            $error = error_get_last();
            $msg .= "\nError: " . $error['message'] . ' ' . $error['type'];
            Usercentrics::logToFile($msg, 'general');
        }
        $msg .= "\nResponse: " . print_r($data, true);
        Usercentrics::logToFile($msg, 'general');
        return json_decode($data, true);
    }
}
