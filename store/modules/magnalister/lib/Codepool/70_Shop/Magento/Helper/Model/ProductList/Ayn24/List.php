<?php

/**
 * 888888ba                 dP  .88888.                    dP                
 * 88    `8b                88 d8'   `88                   88                
 * 88aaaa8P' .d8888b. .d888b88 88        .d8888b. .d8888b. 88  .dP  .d8888b. 
 * 88   `8b. 88ooood8 88'  `88 88   YP88 88ooood8 88'  `"" 88888"   88'  `88 
 * 88     88 88.  ... 88.  .88 Y8.   .88 88.  ... 88.  ... 88  `8b. 88.  .88 
 * dP     dP `88888P' `88888P8  `88888'  `88888P' `88888P' dP   `YP `88888P' 
 *
 *                          m a g n a l i s t e r
 *                                      boost your Online-Shop
 *
 * -----------------------------------------------------------------------------
 * $Id$
 *
 * (c) 2010 - 2014 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLFilesystem::gi()->loadClass('Magento_Helper_Model_ProductList_List');

class ML_Magento_Helper_Model_ProductList_Ayn24_List extends ML_Magento_Helper_Model_ProductList_List {

    public function categoryMarketplace() {
        if ($this->oLoadedProduct === null) {
            if (!in_array(__FUNCTION__, $this->aFields)) {
                $this->aFields[] = __FUNCTION__;
                $this->aHeader[__FUNCTION__] = array(
                    'title' => $this->oI18n->get('Productlist_Header_sMarketplaceCategory'),
                    'type' => 'categoryMarketplace',
                );
            }

            return $this;
        }
    }
}
