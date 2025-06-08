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
MLI18n::gi()->ml_ebay_no_conditions_applicable_for_cat = 'Diese Kategorie erlaubt keine Angabe des Artikelzustands.';
MLI18n::gi()->ml_ebay_prepare_form_category_notvalid = 'Diese Kategorie ist ungültig';
MLI18n::gi()->add('ebay_prepare_form',array(
    'legend' => array(
        'details'                       => 'Artikeldetails',
        'pictures'                      => 'Einstellungen f&uuml;r Bilder',
        'auction'                       => 'Auktionseinstellungen',
        'category'                      => 'eBay-Kategorie',
        'primarycategory_attributes'    => 'Attribute für Primär-Kategorie',
        'secondarycategory_attributes'  => 'Attribute für Sekundär-Kategorie',
        'shipping'                      => 'Versand',
    ),
    'field' => array(
        'title' => array(
            'label' => 'Produktname',
            'hint'  => 'Titel max. 80 Zeichen<br />Erlaubte Platzhalter:<br />#BASEPRICE# - Grundpreis<br />Bitte dazu den <span style="color:red;">Info-Text in der Konfiguration</span> (bei Template Produktname) beachten.',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'Artikelname immer aktuell aus Web-Shop &uuml;bernehmen',
                )
            )
        ),
        'subtitle' => array(
            'label' => 'Untertitel',
            'hint'  => 'Untertitel max. 55 Zeichen <span style="color:red,">kostenpflichtig</span>',
            'optional'=>array(
                'select'=>array(
                    'false'=>'Nicht &Uuml;bertragen',
                    'true'=>'&Uuml;bertragen',
                )
            )
        ),
        'pictureurl' => array(
            'label' => 'eBay-Bild',
            'hint'  => 'Bilder',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'immer alle Bilder aus Webshop übernehmen',
                )
            )
        ),
        'variationdimensionforpictures' => array(
            'label' => 'Bilderpaket Varianten-Ebene',
			'help'  => '
                Sollten Sie Variantenbilder an Ihren Artikel gepflegt haben, werden diese mit Aktivierung von "Bilderpaket" zu eBay übermittelt.<br>
                Hierbei läßt eBay nur eine zu verwendende Varianten-Ebene zu (wählen Sie z. B. "Farbe", so zeigt eBay jeweils ein anderes Bild an, wenn der Käufer eine andere Farbe auswählt).<br>
                Sie können in der Produkt-Vorbereitung jederzeit den hier hinterlegten Standard-Wert für die getroffene Auswahl individuell abändern.<br><br>
                Nachträgliche Änderungen bedürfen einer Anpassung der Vorbereitung und eine erneute Übermittlung der betroffenen Produkte.
            ',
        ),
        'variationpictures' => array(
            'label' => 'Varianten Bilder',
            'hint' => '',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'immer alle Variantenbilder aus Webshop übernehmen',
                )
            ),
        ),
        'gallerytype' => array(
			'label' => 'Galerie-Bilder',
            'help' => '
                <b>Galerie-Bilder</b><br>
                <br>
				Mit Aktivieren dieser Funktion werden in der eBay Suchergebnis-Liste Ihre Angebote mit einem kleinen Vorschaubild platziert. Dies erhöht Ihre Verkaufschancen maßgeblich, da Käufer Angebote ohne Galeriebilder in der Regel weniger aufrufen.<br>
                <br>
				<b>Galerie Plus</b>
                <br>
                <br>
				Durch Aktivierung der Galerie Plus Bilder öffnet sich ein Fenster mit einer vergrößerten Darstellung des Galeriebildes, wenn der Käufer in den Suchergebnissen mit der Maus auf Ihr Angebot zeigt. Bitte beachten Sie, dass die Bilder <b>mindestens 800x800 px</b> groß sein müssen.<br>
                <br>
				<b>Besonderheit "Kleidung &amp; Accessoires"</b><br>
                <br>
				Wenn Sie einen Artikel in der Kategorie "Kleidung &amp; Accessoires" einstellen und Galerie oder Galerie Plus auswählen, bieten Sie den Käufern auf der Suchergebnisseite die Möglichkeit zum "Schnellen Überblick". Galerie Plus muss nicht zusätzlich in Ihrem eBay-Account aktiviert werden.<br>
                <br>
				<b>eBay-Gebühren</b><br>
                <br>
				Durch Nutzung von "Galerie Plus" können im Hintergrund <span style="color:red">zusätzliche Gebühren von eBay</span> erhoben werden! RedGecko GmbH übernimmt für die anfallenden Gebühren keine Haftung.<br>
                <br>
				<b>Weitere Infos</b><br>
                <br>
				Besuchen Sie für weitere Infos zu dem Thema die <a href="http://pages.ebay.de/help/sell/gallery-upgrade.html" target="_blank">eBay Hilfeseiten</a>.
            ',
			'hint'  => 'Galerie-Einstellung<br />("Plus" in einigen Kategorien <span style="color:red">kostenpflichtig</span>)',
            'alert' => array(
                'Plus' => array(
                    'title' => 'Galerie Plus',
                    'content' => '
                        Mit der Zusatzoption <b>Galerie Plus</b> erscheint Ihr Artikelfoto als vergrößertes Vorschaubild in den Suchergebnissen und in der Galerie.<br>
                        <br>
                        Die hochgeladenen Fotos müssen mindestens 800 x 800 Pixel groß sein.<br>
                        <br>
                        Dadurch können im Hintergrund  <span style="color:red;">zusätzliche Gebühren</span> von eBay erhoben werden!<br>
                        <br>Weitere Infos dazu finden Sie auf den <a href="http://pages.ebay.de/help/sell/gallery-upgrade.html" target="_blank">eBay Hilfeseiten</a>.<br>
                        <br>
                        RedGecko GmbH übernimmt für die anfallenden Gebühren keine Haftung.<br>
                        <br>
                        Bitte bestätigen Sie mit "Ok", die Information zur Kenntnis genommen zu haben, oder brechen ab, ohne die Funktion zu aktivieren.
                    '
                ),
            )
        ),
        'description' => array(
            'label' => 'Beschreibung',
            'hint'  => 'Liste verf&uuml;gbarer Platzhalter f&uuml;r die Produktbeschreibung:<dl><dt>#TITLE#</dt><dd>Produktname (Titel)</dd><dt>#ARTNR#</dt><dd>Artikelnummer</dd><dt>#PID#</dt><dd>Produkt-ID</dd><dt>#SHORTDESCRIPTION#</dt><dd>Kurzbeschreibung aus dem Shop</dd><dt>#DESCRIPTION#</dt><dd>Beschreibung aus dem Shop</dd><dt>#PICTURE1#</dt><dd>erstes Produktbild</dd><dt>#PICTURE2# etc.</dt><dd>zweites Produktbild, mit #PICTURE3#, #PICTURE4# usw. können weitere Bilder übermittelt werden, so viele wie im Shop vorhanden.</dd></dl>',
            'optional'=>array(
                'checkbox'=>array(
                    'labelNegativ'=>'Artikelbeschreibung immer aktuell aus Web-Shop verwenden',
                )
            )
        ),
        'pricecontainer' => array(
            'label' => 'eBay Preis',
            'hint'  => 'Preis für eBay',
        ),
        'buyitnowprice'=>array(
            'optional'=>array(
                'select'=>array(
                    'true'=>'Sofortkaufen aktivieren',
                    'false'=>'Kein Sofortkaufen',
                )
            )
        ),
        'site' => array(
            'label' => 'eBay-Site',
            'hint'  => 'eBay-Marketplace, auf dem Sie einstellen.',
        ),
        'listingtype' => array(
            'label' => 'Art der Auktion',
            'hint'  => 'Art der Auktion',
        ),
        'listingduration' => array(
            'label' => 'Laufzeit',
            'hint'  => 'Dauer der Auktion',
        ),
        'paymentmethods' => array(
            'label' => 'Zahlungsarten',
            'hint'  => 'Angebotene Zahlungsarten',
        ),
        'conditionid' => array(
            'label' => 'Artikelzustand',
            'hint'  => 'Zustand des Artikels (wird in den meisten Kategorien bei eBay angezeigt)',
        ),
        'privatelisting' => array(
            'label' => 'Privat-Listing',
            'hint'  => 'Wenn aktiv, kann die Käufer / Bieterliste nicht von Dritten eingesehen werden',
        ),
        'bestofferenabled' => array(
            'label' => 'Preisvorschlag',
            'hint'  => 'Wenn aktiv, können Käufer eigene Preise vorschlagen'
        ),
        'ebayplus' => array(
            'label' => 'eBay Plus',
            'hint'  => "'eBay Plus' aktivieren"
        ),
        'hitcounter' => array(
            'label' => 'Besucherz&auml;hler',
            'hint'  => '',
        ),
        'starttime' => array(
            'label' => 'Startzeit<br />(falls vorbelegt)',
            'hint'  => 'Im Normalfall ist ein eBay-Artikel sofort nach dem Hochladen aktiv. Aber wenn Sie dieses Feld füllen, erst ab Startzeit (kostenpflichtig).',
        ),
        'primarycategory' => array(
            'label' => 'Prim&auml;rkategorie',
            'hint'  => 'W&auml;hlen',
        ),
        'secondarycategory' => array(
            'label' => 'Sekund&aumlrkategorie',
            'hint'  => 'W&auml;hlen',
        ),
        'storecategory' => array(
            'label' => 'eBay Store Kategorie',
            'hint'  => 'W&auml;hlen',
        ),
        'storecategory2' => array(
            'label' => 'Sekundäre Store Kategorie',
            'hint'  => 'W&auml;hlen',
        ),
        'shippinglocalcontainer'=>array(
            'label' => 'Versand Inland',
            'hint'  => 'Angebotene inl&auml;ndische Versandarten<br /><br />Angabe "=GEWICHT" bei den Versandkosten setzt diese gleich dem Artikelgewicht.',
        ),
        'shippinginternationalcontainer'=>array(
            'label' => 'Versand Ausland',
            'hint'  => 'Angebotene ausländische Versandarten',
        ),
        
        'shippinglocal' => array(
            'cost'  => 'Versandkosten'
        ),
        'shippinglocalprofile' => array(
            'option'=>'{#NAME#} ({#AMOUNT#} je weiteren Artikel)',
            'optional'=>array(
                'select'=>array(
                    'false'=>'Versandprofil nicht anwenden',
                    'true'=>'Versandprofil anwenden',
                )
            )
        ),
        'shippinglocaldiscount' => array(
            'label'=>'Regeln f&uuml;r Versand zum Sonderpreis anwenden'
        ),
        'shippinginternationaldiscount' => array(
            'label'=>'Regeln f&uuml;r Versand zum Sonderpreis anwenden'
        ),
        'shippinginternational' => array(
            'cost'  => 'Versandkosten',
            'optional'=>array(
                'select'=>array(
                    'false'=>'Nicht ins Ausland versenden',
                    'true'=>'Ins Ausland Versenden',
                )
            )
        ),
	'dispatchtimemax' => array(
            'label' => 'Zeit bis Versand',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'Zeit bis Versand immer aus eBay-Konfiguration nehmen',
                )
            )
        ),
        'shippinginternationalprofile' => array(
            'option'=>'{#NAME#} ({#AMOUNT#} je weiteren Artikel)',
            'notavailible'=>'Nur wenn `<i>Versand Ausland</i>` aktiv ist.',
            'optional'=>array(
                'select'=>array(
                    'false'=>'Versandprofil nicht anwenden',
                    'true'=>'Versandprofil anwenden',
                )
            )
        ),
    )
),false);
