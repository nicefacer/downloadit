<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 * We offer the best and most useful modules PrestaShop and modifications for your online store.
 *
 * @author    knowband.com <support@knowband.com>
 * @copyright 2017 Knowband
 * @license   see file: LICENSE.txt
 * @category  PrestaShop Module
 */

class AdminKbSitemapController extends ModuleAdminController
{

    public function __construct()
    {
        parent::__construct();
    }

    public function initContent()
    {
        parent::initContent();
        Tools::redirectAdmin(
            $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . urlencode($this->module->name) . '&tab_module=' . $this->module->tab . '&module_name=' . urlencode($this->module->name)
        );
    }
}
