<?php
/**
 * File EbaylistingdurationRenderer.php
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

class Grid_EbaylistingdurationRenderer extends Grid_TextRenderer
{

    public function render($fieldKey = null, $row = null, $config = array(), $grid = null)
    {
        $return = $row['start_time'].'<br/>'.  str_replace("_", " ", $row['listing_duration']);
        $row['listing_type'] == EbayListingsModel::LISTING_TYPE_CHINESE && $return .= "<br/>".L::t("Auction");
        $row['listing_type'] == EbayListingsModel::LISTING_TYPE_FIXEDPRICE && $return .= "<br/>".L::t("Fixed Price");
        
        return $return;
    }



}