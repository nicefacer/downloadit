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
 * (c) 2010 - 2015 RedGecko GmbH -- http://www.redgecko.de
 *     Released under the MIT License (Expat)
 * -----------------------------------------------------------------------------
 */
MLSetting::gi()->ebay_prepare_form=array(
    'details' => array(
        'fields' => array(
            array(
                'name' => 'title',
                'singleproduct' => true,
            ),
            array(
                'name' => 'subtitle',
                'type' => 'optional',
                'singleproduct' => true,
            ),
            array(
                'name' => 'description',
                'singleproduct' => true,
            ),
            array(
                'name' => 'pricecontainer',
                'singleproduct' => true,
            )
        )
    ),
    'pictures' => array(
        'fields' => array(
                        array(
                'name' => 'pictureUrl',
                'singleproduct' => true,
            ),
            array(
                'name' => 'galleryType',  
                'type' => 'select',
            ),
            array(
                'name' => 'variationDimensionForPictures',
            ),
            array(
                'name' => 'variationPictures',
            ),
        ),
    ),
    'auction' => array(
        'fields' => array(
            array(
                'name' => 'site',
            ),
            array(
                'name' => 'listingType',
                
            ),
            array(
                'name' => 'ListingDuration',
            ),
            array(
                'name' => 'PaymentMethods',
            ),
            array(
                'name' => 'ConditionID',
            ),
            array(
                'name' => 'privateListing',
            ),
            array(
                'name' => 'bestOfferEnabled',
            ),
            array(
                'name' => 'eBayPlus',
            ),
            array(
                'name' => 'hitcounter',
            ),
            array(
                'name' => 'startTime',
            ),
        )
    ),
    'category' => array(
        'legend' => array('template' => 'ebay_categories'),
        'fields' => array(
            array(
                'name' => 'PrimaryCategory',
                'hint' => array(
                    'template' => 'ebay_categories'
                )
            ),
            array(
                'name' => 'SecondaryCategory',
                'hint' => array(
                    'template' => 'ebay_categories'
                )
            ),
            array(
                'name' => 'StoreCategory',
                'hint' => array(
                    'template' => 'ebay_categories'
                )
            ),
            array(
                'name' => 'StoreCategory2',
                'hint' => array(
                    'template' => 'ebay_categories'
                )
            )
        )
    ),
    'PrimaryCategory_attributes' => array(),
    'SecondaryCategory_attributes' => array(),
    'Shipping' => array(
        'fields' => array(
            array(
                'name' => 'shippinglocalcontainer',
                'type' => 'ebay_shippingcontainer'
            ),
            array(
                'name' => 'dispatchtimemax',
                'type' => 'select',
            ),
            array(
                'name' => 'shippinginternationalcontainer',
                'type' => 'optional',
                'optional' => array(
                    'name' => 'shippinginternational',
                    'field' => array(
                        'type' => 'ebay_shippingcontainer'
                    )
                )
            ),
        )
    ),
);
