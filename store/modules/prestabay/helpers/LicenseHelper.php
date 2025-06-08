<?php
/**
 * File LicenseHelper.php
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
class LicenseHelper
{

    public static function verifyLicenseKey()
    {
        $response = ApiModel::getInstance()->api->keys->check()->post();

        if (!isset($response['success'])  || !$response['success']) {
            RenderHelper::addError(ApiModel::getInstance()->getErrorsAsHtml());
            return false;
        }
        $keyInfo = $response;
        unset($keyInfo['success']);

        Configuration::updateValue("INVEBAY_LICENSE_INFO", json_encode($keyInfo));

        Configuration::updateValue("INVEBAY_LICENSE_EXPIRED", $keyInfo['is_expired']);

        return true;
    }
}