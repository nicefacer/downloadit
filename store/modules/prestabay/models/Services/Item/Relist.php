<?php
/**
 * File Relist.php
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

class Services_Item_Relist extends Services_Item_Abstract
{

    /**
     * Perform internal request validation.
     * To reduce server load
     */
    public function validate()
    {
        $errors = array();
        // 1) check for not empty title
        $this->_isTitleEmpty() && $errors[] = L::t('Please provide item "Title"');
        // 2) check for not empty description
        $this->_isDescriptionEmpty() && $errors[] = L::t('Item "Description" can\'t be empty');
        // 3) check for all price more that 0
        $this->_isPriceEmpty() && $errors[] = L::t('"Price" for item need to be more that 0');

        return $errors;
    }

    public function getData()
    {
        return array(
            'title' => $this->getProfileProduct()->getTitle(),
            'qty' => $this->getProfileProduct()->getQty(),
            'description' =>  $this->getProfileProduct()->getDescription(),
            'price' => $this->_getRelistPrice(),
            'site' => $this->getProfileProduct()->getProfile()->getSiteKey()
        );
    }

    public function _getRelistPrice()
    {
        $auctionType = $this->getProfileProduct()->getProfile()->auction_type;
        switch ($auctionType) {
            case ProfilesModel::AUCTION_TYPE_FIXEDPRICE:
                return array(
                    'start' => $this->getProfileProduct()->getStartPrice()
                );
            default:
            case ProfilesModel::AUCTION_TYPE_CHINESE:
                return array(
                    'start' => $this->getProfileProduct()->getStartPrice(),
                    'reserve' => $this->getProfileProduct()->getReservePrice(),
                    'buynow' =>  $this->getProfileProduct()->getBuynowPrice(),
                );
        }
    }

}
