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

class AdminKbCronRequestHandlerController extends ModuleAdminController
{

    public function postProcess()
    {
        if (!Tools::isEmpty(trim(Tools::getValue('action')))) {
            $action = Tools::getValue('action');
            switch ($action) {
                case 'optimizeproducts':
                    $this->optimizeproducts();
                    break;
                case 'productmeta':
                    $this->productmeta();
                    break;
            }
        }
        parent::init();
    }

    public function optimizeproducts()
    {
        $type = 'products';
        $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_interlinking WHERE knit_selector = 'products' ORDER BY knit_date_added limit 5";
        $interlinkingdetails = DB::getInstance()->executeS($selectSQL, true, false);
        $selectprodSQL = "SELECT id_product FROM " . _DB_PREFIX_ . "product WHERE active = '1'";
        $prodids = DB::getInstance()->executeS($selectprodSQL, true, false);
        foreach ($prodids as $prod) {
            foreach ($interlinkingdetails as $interlinkingdetail) {
                $selectdata = "SELECT description, description_short, meta_description ,meta_keywords ,meta_title FROM " . _DB_PREFIX_ . "product_lang WHERE id_product = " . (int) $prod['id_product'] . " AND id_shop = " . (int) Context::getContext()->shop->id . " AND id_lang = " . (int) $this->context->language->id . "";
                $data = DB::getInstance()->executeS($selectdata, true, false);
                $this->checkInitalContentExist($type, $prod['id_product'], $interlinkingdetail['knit_lang_id']);
                $rel = '';
                $target = '';
                $anchor = '<a ' . $rel . ' ' . $target . ' href="' . $interlinkingdetail['knit_keyword_url'] . '" title="' . $interlinkingdetail['knit_keyword_url_title'] . '" class="knowband_seo_anchor-' . $interlinkingdetail['knit_id'] . '">' . $interlinkingdetail["knit_keyword"] . '</a>';
                if ($interlinkingdetail['knit_description'] == 'long') {
                    $long_description = $data[0]['description'];
                    if (preg_match('%<a[^>]+class="knowband_seo_anchor-' . $interlinkingdetail['knit_id'] . '"[^>]*>(.*?)</a>%', $long_description, $regs)) {
                        $result = $regs[1];
                        $long_description = str_replace($regs[0], $regs[1], $long_description);
                    }
                    $short_description = $data[0]['description_short'];
                    if (preg_match('%<a[^>]+class="knowband_seo_anchor-' . $interlinkingdetail['knit_id'] . '"[^>]*>(.*?)</a>%', $short_description, $regs)) {
                        $result = $regs[1];
                        $short_description = str_replace($regs[0], $regs[1], $short_description);
                    }
                    $updateSQL = "UPDATE " . _DB_PREFIX_ . "product_lang SET description_short = '" . pSQL($short_description, true) . "' WHERE id_product = " . (int) $prod['id_product'] . " AND id_shop = " . (int) Context::getContext()->shop->id . " AND id_lang = " . (int) $interlinkingdetail['knit_lang_id'] . "";
                    DB::getInstance()->execute($updateSQL);
                    if ($interlinkingdetail['knit_enable'] == 1) {
                        if ($interlinkingdetail['knit_link_position'] == 'top') {
                            $data = $long_description;
                            $from = '/' . preg_quote($interlinkingdetail['knit_keyword'], '/') . '/';
                            $long = preg_replace($from, $anchor, $data, 1);
                        } else if ($interlinkingdetail['knit_link_position'] == 'bottom') {
                            $string = $long_description;
                            $find = $interlinkingdetail['knit_keyword'];
                            $replace = $anchor;
                            $result = preg_replace(strrev("/$find/"), strrev($replace), strrev($string), 1);
                            $long = strrev($result);
                        } else {
                            // middle
                        }
                    } else {
                        $long = $long_description;
                    }

                    $updateSQL = "UPDATE " . _DB_PREFIX_ . "product_lang SET description = '" . pSQL($long, true) . "' WHERE id_product = " . (int) $prod['id_product'] . " AND id_shop = " . (int) Context::getContext()->shop->id . " AND id_lang = " . (int) $interlinkingdetail['knit_lang_id'] . "";
                    DB::getInstance()->execute($updateSQL);
                }
            }
        }
        echo 'Success';
        die;
    }

    protected function productmeta()
    {
        $type = 'products';
        $selectSQL = "SELECT * FROM " . _DB_PREFIX_ . "knowband_meta WHERE knme_selector = 'products' AND knme_meta_type = 'Normal' AND knme_enable = '1' ORDER BY knme_date_added LIMIT 1";
        $metadetails = DB::getInstance()->executeS($selectSQL, true, false);
        if (!empty($metadetails)) {
            $selectprodSQL = "SELECT id_product FROM " . _DB_PREFIX_ . "product WHERE active = '1'";
            $prodids = DB::getInstance()->executeS($selectprodSQL, true, false);
            $lang['id_lang'] = $this->context->language->id;
            foreach ($prodids as $prod) {
                $detail = array(
                    'id_product' => $prod['id_product'],
                    'id_lang' => $lang['id_lang']
                );
                $meta_tag = $this->placeholderToData($metadetails[0]['knme_meta_tag'], 'products', $detail);
                $meta_tag = $this->myTruncate($meta_tag, 70, '');
                $meta_description = $this->placeholderToData($metadetails[0]['knme_meta_description'], 'products', $detail);
                $meta_description = $this->myTruncate($meta_description, 160, '');
                $meta_keyword = $this->placeholderToData($metadetails[0]['knme_meta_keyword'], 'products', $detail);
                $meta_keyword = $this->myTruncate($meta_keyword, 255, '');
                if (trim($meta_tag) != '' || trim($meta_description) != '' || trim($meta_keyword) != '') {
                    $this->checkInitalContentExist($type, $prod['id_product'], $lang['id_lang']);
                    $updateSQL = "UPDATE " . _DB_PREFIX_ . "product_lang SET meta_description = '" . pSQL($meta_description) . "', meta_keywords = '" . pSQL($meta_keyword) . "', meta_title = '" . pSQL($meta_tag) . "' WHERE id_product = " . (int) $prod['id_product'] . " AND id_shop = " . (int) Context::getContext()->shop->id . " AND id_lang = " . (int) $lang['id_lang'] . "";
                    DB::getInstance()->execute($updateSQL);
                }
            }
        }
        echo 'Success';
        die;
    }

    protected function myTruncate($string, $length, $dots = "...")
    {
        return (Tools::strlen($string) > $length) ? Tools::substr($string, 0, $length - Tools::strlen($dots)) . $dots : $string;
    }

    protected function placeholderToData($string, $group, $detail = array())
    {
        switch ($group) {
            case 'products':
                $product = new Product($detail['id_product'], false, $detail['id_lang'], Context::getContext()->shop->id);
                $string = str_replace('[product_name]', $product->name, $string);
                $string = str_replace('[product_description]', $product->description, $string);
                $string = str_replace('[product_reference]', $product->reference, $string);
                $string = str_replace('[product_short_description]', $product->description_short, $string);
                if (strpos($string, '[product_manufacturer]') !== false) {
                    $manufacturers = Manufacturer::getNameById($product->id_manufacturer);
                    $string = str_replace('[product_manufacturer]', $manufacturers, $string);
                }

                if (strpos($string, '[product_category_name]') !== false) {
                    $cat = Category::getCategoryInformations(array(
                            $product->id_category_default), $detail['id_lang']);
                    $string = str_replace('[product_category_name]', $cat[$product->id_category_default]['name'], $string);
                }
                return $string;
                break;
            case 'categories':
                $category = new Category($detail['id_cat'], $detail['id_lang'], Context::getContext()->shop->id);
                $string = str_replace('[category_name]', $category->name, $string);
                $string = str_replace('[category_description]', $category->description, $string);
                return $string;
                break;
            case 'manufacturers':
                $manufacturers = new Manufacturer($detail['id_man'], $detail['id_lang']);
                $string = str_replace('[manufacturer_name]', $manufacturers->name, $string);
                $string = str_replace('[manufacturer_description]', $manufacturers->description, $string);
                $string = str_replace('[manufacturer_short_description]', $manufacturers->short_description, $string);
                return $string;
            default:
                return $string;
                break;
        }
        return $string;
    }

    private function checkInitalContentExist($type, $content_id, $lang_id)
    {
        $selectContent = "SELECT count(*) as exist FROM " . _DB_PREFIX_ . "knowband_initial_content WHERE id_lang =" . (int) $lang_id . " and id_content = " . (int) $content_id . " and id_shop = " . (int) Context::getContext()->shop->id . " and type = '" . pSQL($type) . "'";
        $contentExist = DB::getInstance()->getValue($selectContent);
        $initial_exist = ($contentExist == 0) ? true : false;
        if (($initial_exist) && ($type == 'products')) {
            $selectdata = "SELECT description, description_short, meta_description ,meta_keywords ,meta_title FROM " . _DB_PREFIX_ . "product_lang WHERE id_product = " . (int) $content_id . " AND id_shop = " . (int) Context::getContext()->shop->id . " AND id_lang = " . (int) $lang_id . "";
            $content_details = DB::getInstance()->executeS($selectdata, true, false);
            $insert = array(
                'id_content' => (int) $content_id,
                'id_lang' => (int) $lang_id,
                'id_shop' => (int) Context::getContext()->shop->id,
                'type' => pSQL($type),
                'description' => pSQL($content_details[0]['description'], true),
                'short_description' => pSQL($content_details[0]['description_short'], true),
                'meta_title' => pSQL($content_details[0]['meta_title'], true),
                'meta_description' => pSQL($content_details[0]['meta_description'], true),
                'meta_keyword' => pSQL($content_details[0]['meta_keywords'], true),
                'date_add' => pSQL(date('Y-m-d H:i:s')),
                'date_upd' => pSQL(date('Y-m-d H:i:s')),
            );
            Db::getInstance()->insert('knowband_initial_content', $insert);
        }
    }
}
