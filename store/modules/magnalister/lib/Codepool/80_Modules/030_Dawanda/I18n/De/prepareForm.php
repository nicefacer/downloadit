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

MLI18n::gi()->add('dawanda_prepare_form',array(
    'legend' => array(
        'details' => 'Produktdetails',
        'categories' => 'DaWanda Kategorien',
        'attributes' => 'attributes',
    ),
    'field' => array(
        'producttype' => array(
            'label' => 'Art des Produktes',
            'hint'  => '',
        ),
        'listingduration' => array(
            'label' => 'Laufzeit',
            'hint' => '',
        ),
        'returnpolicy' => array(
            'label' => 'Widerrufsbelehrung',
            'hint' => '',
        ),
        'shippingservice' => array(
            'label' => 'Versand-Profil',
            'hint' => ''
        ),
        'mpcolors' => array(
            'label' => 'Produkt Farben',
            'hint' => '',
            'subfieldlabel' => 'Produkt Farbe',
            'valuenocolor' => 'Keine Farbe'
        ),
        'categories' => array(
            'label' => 'DaWanda Kategorien',
            'hint' => '',
        ),
        'primarycategory' => array(
            'label' => 'Marktplatz-Kategorie:',
            'hint' => '',
        ),
        'secondarycategory' => array(
            'label' => '2. Marktplatz-Kategorie:',
            'hint' => '',
        ),
        'storecategory' => array(
            'label' => 'Shop-Kategorie:',
            'hint' => '',
        ),
        'attributes' => array(
            'label' => 'attributes',
            'hint' => '',
        ),
    )
),false);

MLI18n::gi()->prepareform_listingduration_values = array(
    -1 => 'Automatisches wiedereinstellen nach 120 Tagen',
    120 => 'Kein automatisches wiedereinstellen'
);