<?php
/**
 * File LicenseController.php
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

class LicenseController extends BaseAdminController
{
    public function generateAction()
    {
        $this->_generateLicense();
    }

    protected function _generateLicense()
    {
        ApiModel::getInstance()->reset();
        $response = ApiModel::getInstance()->api->keys->generate()->post();
        if (is_array($response)) {
            $keyInfo = array(
                'domain' => $response['domain'],
                'expired_time' => $response['expired_time'],
                'trial' => $response['trial'],
            );

            Configuration::updateValue("INVEBAY_LICENSE_KEY", trim($response['key']));
            Configuration::updateValue("INVEBAY_LICENSE_INFO", json_encode($keyInfo));

            RenderHelper::addSuccess(L::t("License key successfully obtained"));
        } else {
            RenderHelper::addError(ApiModel::getInstance()->getErrorsAsHtml());
            RenderHelper::addError(L::t("Failed obtain license key. Please try again latter"));
        }
//        $r = ApiModel::getInstance()->api->keys->check()->post();
        UrlHelper::redirect("config/index");
        return;
    }


}