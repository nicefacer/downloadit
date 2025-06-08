<?php

/**
 * File FeedbacktransactionRenderer.php
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
class Grid_FeedbacktransactionRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $transaction = isset($row[$fieldKey]) && $row[$fieldKey] != "0" ? $row[$fieldKey] : "";

        $return = $transaction;
        if ($row['order_id'] > 0) {
            $return = "<a href='".UrlHelper::getUrl("ebayOrder/view", array('id'=> $row['order_id']))."' target=_blank>".$return."</a>";
        }

        return $return;
    }

}