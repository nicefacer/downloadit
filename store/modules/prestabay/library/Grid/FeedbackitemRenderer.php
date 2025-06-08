<?php

/**
 * File FeedbackitemRenderer.php
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
 * eBay Listing Import.
 * Adding possibility import all eBay Listing to PrestaShop
 *
 * @author      Involic <contacts@involic.com>
 * @copyright   Copyright (c) 2011-2012 by Involic (http://www.involic.com)
 * @license     http://involic.com/license.txt
 */
class Grid_FeedbackitemRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $title = isset($row[$fieldKey]) && $row[$fieldKey] ? $row[$fieldKey] : "N/A";

        $return = "$title<br/>{$this->ebayLink($row['mode'], $row['item_id'])}";

        return $return;
    }

    protected function ebayLink($mode, $itemId)
    {
        $baseLink = "http://cgi.ebay.com/";
        if ($mode == AccountsModel::ACCOUNT_MODE_SANDBOX) {
            $baseLink = "http://cgi.sandbox.ebay.com/";
        }
        return "<a href='{$baseLink}{$itemId}' target='_blank'>{$itemId}</a>";
    }
}