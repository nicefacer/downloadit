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
MLI18n::gi()->configform_quantity_values = array(
    'stock' => array(
        'title' => 'Shop Lagerbestand &uuml;bernehmen',
        'textoption' => false
    ),
    'stocksub' => array(
        'title' => 'Shop Lagerbestand &uuml;bernehmen abzgl. Wert aus rechten Feld',
        'textoption' => true
    ),
    'lump' => array(
        'title' => 'Pauschal (aus rechtem Feld)',
        'textoption' => true
    ),
);
MLI18n::gi()->configform_price_addkind_values = array(
    'percent' => 'x% Shop-Preis Auf-/Abschlag',
    'addition' => 'x  Shop-Preis Auf-/Abschlag',
);

MLI18n::gi()->configform_sync_value_auto = 'Automatische Synchronisierung per CronJob (empfohlen)';
MLI18n::gi()->configform_sync_value_fast = 'Schnellere automatische Synchronisation cronjob (auf 15 Minuten)';
MLI18n::gi()->configform_sync_value_no = 'Keine Synchronisierung';

MLI18n::gi()->configform_sync_values = array(
    'auto' => '{#i18n:configform_sync_value_auto#}',
    'no' => '{#i18n:configform_sync_value_no#}',
);

MLI18n::gi()->configform_fast_sync_values = array(
    'auto' => '{#i18n:configform_sync_value_auto#}',
    'auto_fast' => '{#i18n:configform_sync_value_fast#}',
    'no' => '{#i18n:configform_sync_value_no#}',
);

MLI18n::gi()->configform_stocksync_values = array(
    'rel' => 'Bestellung reduziert Shop-Lagerbestand (empfohlen)',
    'no' => '{#i18n:configform_sync_value_no#}',
);

MLI18n::gi()->{'configform_price_field_priceoptions_help'} = '<p>Mit dieser Funktion k&ouml;nnen Sie abweichende Preise zum Marktplatz &uuml;bergeben und automatisch synchronisieren lassen, die Sie in Ihren Web-Shop Kundengruppen hinterlegen k&ouml;nnen.
Wenn Sie keinen Preis in der neuen Kundengruppe eintragen, wird automatisch der Standard-Preis aus dem Web-Shop verwendet. Somit ist es sehr einfach, auch f&uuml;r nur wenige Artikel einen abweichenden Preis zu hinterlegen.
Die übrigen Konfigurationen zum Preis finden ebenfalls Anwendung.
</p>
<ul>
<li>Hinterlegen Sie in Ihrem Web-Shop eine Kundengruppe z.B. "eBay-Kunden"
<li>F&uuml;gen Sie in Ihrem Web-Shop an den Artikeln in der neuen Kundengruppe die gew&uuml;nschten Preise ein. 
 </ul>';