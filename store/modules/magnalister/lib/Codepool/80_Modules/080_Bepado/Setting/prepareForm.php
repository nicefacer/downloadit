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

MLSetting::gi()->bepado_prepare_form = array(
    'categories' => array(
        'fields' => array(
            array(
                'name' => 'categories',
                'type' => 'categoryselect',
                'subfields' => array(
                    'category' => array('name' => 'category', 'type' => 'categoryselect', 'cattype' => 'marketplace'),
                )
            ),
        ),
    ),
    'shipping' => array(
        'fields' => array(
            array(
                'name' => 'shippingTime',
                'type' => 'select',
            ),
            array(
                'name' => 'shippingcontainer',
                'type' => 'duplicate',
                'duplicate' => array(
                    'field' => array('type' => 'subFieldsContainer')
                ),
                'subfields' => array(
                    array('name' => 'shippingcountry', 'type' => 'select'),
                    array('name' => 'shippingservice', 'type' => 'string'),
                    array('name' => 'shippingcost', 'type' => 'string'),
                )
            ),
        )
    ),
    'details' => array(
        'fields' => array(
            array(
                'name' => 'b2b_price_active',
                'type' => 'bool',
            ),
        )
    ),
);
