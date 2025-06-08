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
MLI18n::gi()->ebay_config_general_autosync = 'Automatische Synchronisierung per CronJob (empfohlen)';
MLI18n::gi()->ebay_config_general_nosync = 'keine Synchronisierung';
MLI18n::gi()->ebay_config_account_title = 'Zugangsdaten';
MLI18n::gi()->ebay_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->ebay_config_account_price = 'Preisberechnung';
MLI18n::gi()->ebay_config_account_sync = 'Synchronisation';
MLI18n::gi()->ebay_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->ebay_config_account_emailtemplate = 'Promotion-E-Mail Template';
MLI18n::gi()->ebay_config_account_producttemplate = 'Produkt Template';

MLI18n::gi()->ebay_configform_prepare_hitcounter_values = array(
    'NoHitCounter' => 'keiner',
    'BasicStyle' => 'einfach',
    'RetroStyle' => 'Retro-Style',
    'HiddenStyle' => 'versteckt',
);
MLI18n::gi()->ebay_configform_prepare_gallerytype_values = array(
    'None' => 'Kein Bild',
    'Gallery' => 'Standard',
    'Plus' => 'Plus',
);
MLI18n::gi()->ebay_configform_prepare_dispatchtimemax_values = array(
    '0' => 'am gleichen Tag',
    '1' => '1 Tag',
    '2' => '2 Tage',
    '3' => '3 Tage',
    '4' => '4 Tage',
    '5' => '5 Tage',
    '6' => '6 Tage',
    '7' => '7 Tage',
    '8' => '8 Tage',
    '9' => '9 Tage',
    '10' => '10 Tage',
    '11' => '11 Tage',
    '12' => '12 Tage',
    '13' => '13 Tage',
    '14' => '14 Tage',
    '15' => '15 Tage',
    '16' => '16 Tage',
    '17' => '17 Tage',
    '18' => '18 Tage',
    '19' => '19 Tage',
    '20' => '20 Tage',
    '21' => '21 Tage',
    '22' => '22 Tage',
    '23' => '23 Tage',
    '24' => '24 Tage',
    '25' => '25 Tage',
    '26' => '26 Tage',
    '27' => '27 Tage',
    '28' => '28 Tage',
    '29' => '29 Tage',
    '30' => '30 Tage',
);
MLI18n::gi()->ebay_configform_price_chinese_quantityinfo = 'Bei Steigerungsauktionen kann die St&uuml;ckzahl nur genau 1 betragen.';
MLI18n::gi()->ebay_configform_account_sitenotselected = 'Bitte erst eBay-Site w&auml;hlen';
MLI18n::gi()->ebay_configform_orderstatus_sync_values = array(
    'auto' => '{#i18n:ebay_config_general_autosync#}',
    'no' => '{#i18n:ebay_config_general_nosync#}',
);
MLI18n::gi()->ebay_configform_sync_values = array(
    'auto' => '{#i18n:ebay_config_general_autosync#}',
    /*
      'auto_fast' => 'Schnellere automatische Synchronisation cronjob (auf 15 Minuten)',
     */
    'no' => '{#i18n:ebay_config_general_nosync#}',
);
MLI18n::gi()->ebay_configform_stocksync_values = array(
    'rel' => 'Bestellung reduziert Shop-Lagerbestand (empfohlen)',
    'no' => '{#i18n:ebay_config_general_nosync#}',
);
MLI18n::gi()->ebay_configform_pricesync_values = array(
    'auto' => '{#i18n:ebay_config_general_autosync#}',
    'no' => '{#i18n:ebay_config_general_nosync#}',
);

MLI18n::gi()->ebay_configform_sync_chinese_values = array(
    'auto' => '{#i18n:ebay_config_general_autosync#}',
    'abs' => 'Bestellung / Artikel bearbeiten setzt eBay-Lagerbestand gleich Shop-Lagerbestand',
    'rel' => 'Bestellung / Artikel bearbeiten &auml;ndert eBay-Lagerbestand (Differenz)',
    'onlydecr' => 'Bestellung / Lageranzahl verkleinern reduziert eBay-Lagerbestand (kein Aufstocken)',
    'no' => '{#i18n:ebay_config_general_nosync#}',
);
MLI18n::gi()->ebay_configform_orderimport_payment_values = array(
    'textfield' => array(
        'title' => 'Aus Textfeld',
        'textoption' => true
    ),
    'matching' => array(
        'title' => 'Automatische Zuordnung',
    ),
);

MLI18n::gi()->ebay_configform_orderimport_shipping_values = array(
    'textfield' => array(
        'title' => 'Aus Textfeld',
        'textoption' => true
    ),
    'matching' => array(
        'title' => 'Automatische Zuordnung',
    ),
);

MLI18n::gi()->ebay_config_sync_inventory_import = array(
    'true' => 'Ja',
    'false' => 'Nein'
);

MLI18n::gi()->ebay_config_account_emailtemplate_sender = 'Beispiel-Shop';
MLI18n::gi()->ebay_config_account_emailtemplate_sender_email = 'beispiel@onlineshop.de';
MLI18n::gi()->ebay_config_account_emailtemplate_subject = 'Ihre Bestellung bei #SHOPURL#';
MLI18n::gi()->ebay_config_producttemplate_content = '<p>#TITLE#</p>' .
        '<p>#ARTNR#</p>' .
        '<p>#SHORTDESCRIPTION#</p>' .
        '<p>#PICTURE1#</p>' .
        '<p>#PICTURE2#</p>' .
        '<p>#PICTURE3#</p>' .
        '<p>#DESCRIPTION#</p>';
MLI18n::gi()->ebay_config_emailtemplate_content = '
 <style><!--
body {
    font: 12px sans-serif;
}
table.ordersummary {
	width: 100%;
	border: 1px solid #e8e8e8;
}
table.ordersummary td {
	padding: 3px 5px;
}
table.ordersummary thead td {
	background: #cfcfcf;
	color: #000;
	font-weight: bold;
	text-align: center;
}
table.ordersummary thead td.name {
	text-align: left;
}
table.ordersummary tbody tr.even td {
	background: #e8e8e8;
	color: #000;
}
table.ordersummary tbody tr.odd td {
	background: #f8f8f8;
	color: #000;
}
table.ordersummary td.price,
table.ordersummary td.fprice {
	text-align: right;
	white-space: nowrap;
}
table.ordersummary tbody td.qty {
	text-align: center;
}
--></style>
<p>Hallo #FIRSTNAME# #LASTNAME#,</p>
<p>vielen Dank f&uuml;r Ihre Bestellung! Sie haben &uuml;ber #MARKETPLACE# in unserem Shop folgendes bestellt:</p>
#ORDERSUMMARY#
<p>Zuz&uuml;glich etwaiger Versandkosten.</p>
<p>&nbsp;</p>
<p>Mit freundlichen Gr&uuml;&szlig;en,</p>
<p>Ihr Online-Shop-Team</p>';

MLI18n::gi()->add('ebay_config_account', array(
    'legend' => array(
        'account' => 'Zugangsdaten',
        'tabident' => ''
    ),
    'field' => array(
        'tabident' => array(
            'label' => '{#i18n:ML_LABEL_TAB_IDENT#}',
            'help' => '{#i18n:ML_TEXT_TAB_IDENT#}'
        ),
        'username' => array(
            'label' => 'eBay-Mitgliedsname',
            'help' => 'Bitte hier den eBay-Usernamen eintragen',
            'hint' => '',
        ),
        'password' => array(
            'label' => 'eBay-Passwort',
            'help' => 'Bitte hier das eBay-Passwort eintragen',
        ),
        'token' => array(
            'label' => 'eBay-Token',
            'help' => 'Um einen neuen ebay-Token zu beantragen, klicken Sie bitte auf den Button.<br>
                                Sollte kein Fenster zu eBay aufgehen, wenn Sie auf den Button klicken, haben Sie einen Pop-Up Blocker aktiv.<br><br>
                                    Der Token ist notwendig, um &uuml;ber elektronische	Schnittstellen wie den magnalister Artikel auf eBay einzustellen und zu verwalten.<br>
                                    Folgen Sie von da an den Anweisungen auf der eBay Seite, um den Token zu beantragen und Ihren Online-Shop &uuml;ber magnalister mit eBay zu verbinden.',
        ),
        'site' => array(
            'label' => 'eBay Site',
            'help' => 'eBay-L&auml;nderseite, auf der gelistet wird',
        ),
        'currency' => array(
            'label' => 'W&auml;hrung',
            'help' => 'Die W&auml;hrung, in der Artikel auf eBay gelistet werden. Bitte w&auml;hlen Sie eine W&auml;hrung passend zur eBay-L&auml;nderseite',
        ),
    ),
        ), false);


MLI18n::gi()->add('ebay_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'location' => array(
            'title' => 'Standort',
            'info' => 'Geben Sie bitte hier den Standort Ihres Shops ein. Dieser ist dann als Verk&auml;uferadresse auf der Artikelseite bei eBay sichtbar. '
        ),
        'pictures' => 'Einstellungen f&uuml;r Bilder',
        'payment' => '<b>Einstellungen f&uuml;r Zahlungsarten</b>',
        'returnpolicy' => '<b>R&uuml;cknahmebedingungen</b>',
        'shipping' => 'Versand',
        'misc' => '<b>Einstellungen Sonstiges</b>',
        'upload' => 'Artikel hochladen: Voreinstellungen',
    ),
    'field' => array(
        'prepare.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
        'postalcode' => array(
            'label' => 'PLZ',
            'help' => 'Geben Sie bitte hier den Standort Ihres Shops ein. Dieser ist dann als Verk&auml;uferadresse auf der Artikelseite bei eBay sichtbar. '
        ),
        'location' => array(
            'label' => 'Ort',
        ),
        'country' => array(
            'label' => 'Land',
        ),
        'mwst' => array(
            'label' => 'Mehrwertsteuer',
            'help' => 'H&ouml;he der Mehrwertsteuer, die bei eBay ausgewiesen wird. Bitte nur ausf&uuml;llen wenn Sie ein gewerbliches H&auml;ndlerkonto bei eBay haben',
            'hint' => '&nbsp;Steuersatz f&uuml;r gewerbliche H&auml;ndler in %',
        ),
        'conditionid' => array(
            'label' => 'Artikelzustand',
            'help' => 'Voreinstellung f&uuml den Artikelzustand (f&uuml;r eBay-Kategorien wo dieser angegeben werden kann oder mu&szlig;). Nicht alle Werte sind f&uuml;r jede Kategorie zul&auml;ssig, ggf. mu&szlig; nach der Wahl der Kategorie der Zustand noch mal korrigiert werden.',
        ),
        'lang' => array(
            'label' => 'Sprache',
            'help' => 'Sprache f&uuml;r Ihre Artikelnamen und Beschreibungen. Ihr Shop erm&ouml;glicht es, 
					Namen und Beschreibungen in mehreren Sprachen zu hinterlegen. F&uuml;r eBay-Artikelnamen und Beschreibungen mu&szlig; eine davon ausgew&auml;lt werden.
					In derselben Sprache kommen auch etwaige Fehlermeldungen von eBay.',
        ),
        'dispatchtimemax' => array(
            'label' => 'Zeit bis Versand',
            'help' => 'Maximale Dauer die Sie brauchen, bis Sie den Artikel versenden. Der Wert wird bei eBay angezeigt.',
        ),
        'topten' => array(
            'label' => 'Kategorie-Schnellauswahl',
            'help' => 'Anzeigen der Kategorie-Schnellauswahl unter Produkte vorbereiten',
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
            'hint' => 'Galerie-Einstellung<br />("Plus" in einigen Kategorien <span style="color:red">kostenpflichtig</span>)',
            'alert' => array(
                'Plus' => array(
                    'title' => 'Bitte beachten',
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
        'variationdimensionforpictures' => array(
            'label' => 'Bilderpaket Varianten-Ebene',
            'help' => '
                Sollten Sie Variantenbilder an Ihren Artikel gepflegt haben, werden diese mit Aktivierung von "Bilderpaket" zu eBay übermittelt.<br>
                Hierbei läßt eBay nur eine zu verwendende Varianten-Ebene zu (wählen Sie z. B. "Farbe", so zeigt eBay jeweils ein anderes Bild an, wenn der Käufer eine andere Farbe auswählt).<br>
                Sie können in der Produkt-Vorbereitung jederzeit den hier hinterlegten Standard-Wert für die getroffene Auswahl individuell abändern.<br><br>
                Nachträgliche Änderungen bedürfen einer Anpassung der Vorbereitung und eine erneute Übermittlung der betroffenen Produkte.
            ',
        ),
        'paymentmethods' => array(
            'label' => 'Zahlungsart',
            'help' => 'Voreinstellung f&uuml;r Zahlungsarten (Mehrfach-Auswahl mit Strg+Klick). Auswahl nach Vorgabe von eBay.',
        ),
        'paypal.address' => array(
            'label' => 'PayPal E-Mail-Adresse',
            'help' => 'E-Mail-Adresse, die Sie bei eBay f&uuml;r PayPal-Zahlungen angegeben haben. Pflicht, wenn Sie eBay-Store-Artikel hochladen.'
        ),
        'paymentinstructions' => array(
            'label' => 'Weitere Angaben zur Kaufabwicklung',
            'help' => 'Geben Sie hier einen Text ein, der am unteren Ende der Artikel-Ansicht unter "Zahlungshinweise des Verk&auml;ufers" erscheint. Erlaubt sind bis zu 500 Zeichen (nur Text, kein HTML).'
        ),
        'shippinglocalcontainer' => array(
            'label' => 'Versand Inland',
            'help' => 'Mindestens eine oder mehrere Versandarten ausw&auml;hlen, die standardm&auml;&szlig;ig verwendet werden soll.<br /><br />Bei den Versandkosten k&ouml;nnen Sie eine Zahl eintragen (ohne Angabe der W&auml;hrung) oder "=GEWICHT", um die Versandkosten gleich dem Artikelgewicht zu setzen.'
            . '
<div class="ui-dialog-titlebar ">
<span>Rabatte Kombizahlung und Versand</span>
</div>
Auswahl des Profils f&uuml;r Versandrabatte. Die Profile k&ouml;nnen Sie anlegen in Ihrem eBay account, unter Mein eBay -> Mitgliedskonto -> Einstellungen -> Versandeinstellungen<br /><br />
				Die Regeln f&uuml;r Versand zum Sonderpreis (z.B. maximalen Versandpreis pro Bestellung, oder einen Betrag ab dem der Versand kostenfrei ist) k&ouml;nnen Sie ebenfalls dort anlegen.<br /><br />
				<b>Hinweis:</b><br />
				Beim Bestell-Import wird die Regel verwendet, welche aktuell hier ausgew&auml;hlt ist (da wir von eBay nicht die Information bekommen, wie es beim Einstellen des Artikels ausgesehen hat).',
        ),
        'shippinginternationalcontainer' => array(
            'label' => 'Versand Ausland',
            'help' => 'Keine oder mehrere Versandarten und L&auml;nder ausw&auml;hlen, die standardm&auml;&szlig;ig verwendet werden sollen.'
            . '<div class="ui-dialog-titlebar ">
<span>Rabatte Kombizahlung und Versand</span>
</div>
Auswahl des Profils f&uuml;r Versandrabatte. Die Profile k&ouml;nnen Sie anlegen in Ihrem eBay account, unter Mein eBay -> Mitgliedskonto -> Einstellungen -> Versandeinstellungen<br /><br />
				Die Regeln f&uuml;r Versand zum Sonderpreis (z.B. maximalen Versandpreis pro Bestellung, oder einen Betrag ab dem der Versand kostenfrei ist) k&ouml;nnen Sie ebenfalls dort anlegen.<br /><br />
				<b>Hinweis:</b><br />
				Beim Bestell-Import wird die Regel verwendet, welche aktuell hier ausgew&auml;hlt ist (da wir von eBay nicht die Information bekommen, wie es beim Einstellen des Artikels ausgesehen hat).',
        ),
        'shippinglocal' => array(
            'cost' => 'Versandkosten'
        ),
        'shippinglocalprofile' => array(
            'option' => '{#NAME#} ({#AMOUNT#} je weiteren Artikel)',
            'optional' => array(
                'select' => array(
                    'false' => 'Versandprofil nicht anwenden',
                    'true' => 'Versandprofil anwenden',
                )
            )
        ),
        'shippinglocaldiscount' => array(
            'label' => 'Regeln f&uuml;r Versand zum Sonderpreis anwenden'
        ),
        'shippinginternationaldiscount' => array(
            'label' => 'Regeln f&uuml;r Versand zum Sonderpreis anwenden'
        ),
        'shippinginternational' => array(
            'cost' => 'Versandkosten',
            'optional' => array(
                'select' => array(
                    'false' => 'Nicht ins Ausland versenden',
                    'true' => 'Ins Ausland Versenden',
                )
            )
        ),
        'shippinginternationalprofile' => array(
            'option' => '{#NAME#} ({#AMOUNT#} je weiteren Artikel)',
            'notavailible' => 'Nur wenn `<i>Versand Ausland</i>` aktiv ist.',
            'optional' => array(
                'select' => array(
                    'false' => 'Versandprofil nicht anwenden',
                    'true' => 'Versandprofil anwenden',
                )
            )
        ),
        'returnpolicy.returnsaccepted' => array(
            'label' => 'R&uuml;ckgabe m&ouml;glich',
        ),
        'returnpolicy.returnswithin' => array(
            'label' => 'R&uuml;cknahmefrist',
            'help' => 'Zeitraum in dem Sie ein Artikel zur&uuml;cknehmen'
        ),
        'returnpolicy.shippingcostpaidby' => array(
            'label' => 'R&uuml;cksendekosten',
        ),
        'returnpolicy.description' => array(
            'label' => 'R&uuml;cknahmebedingungen: Weitere Angaben',
            'help' => 'Beschreiben Sie hier Ihre R&uuml;cknahmebedingungen. Erlaubt sind bis zu 5000 Zeichen (nur Text, kein HTML).',
        ),        
        'usevariations' => array(
            'label' => 'Varianten',
            'help' => 'Funktion aktiviert: Produkte, die in mehreren Varianten (wie Gr&ouml;&szlig;e oder Farbe) im Shop vorhanden sind, werden auch so an eBay &uuml;bermittelt.<br /><br /> Die Einstellung "St&uuml;ckzahl" wird dann auf jede einzelne Variante angewendet.<br /><br /><b>Beispiel:</b> Sie haben einen Artikel 8 mal in blau, 5 mal in gr&uuml;n und 2 mal in schwarz, unter St&uuml;ckzahl "Shop-Lagerbestand &uuml;bernehmen abzgl. Wert aus rechtem Feld", und den Wert 2 in dem Feld. Der Artikel wird dann 6 mal in blau und 3 mal in gr&uuml;n &uuml;bermittelt.<br /><br /><b>Hinweis:</b> Es kommt vor, da&szlig; etwas das Sie als Variante verwenden (z.B. Gr&ouml;&szlig;e oder Farbe) ebenfalls in der Attribut-Auswahl f&uuml;r die Kategorie erscheint. In dem Fall wird Ihre Variante verwendet, und nicht der Attributwert.',
            'valuehint' => 'Varianten &uuml;bermitteln'
        ),
        'useprefilledinfo' => array(
            'label' => 'Produktinfos',
            'help' => 'Funktion aktiviert: Falls es im eBay Katalog zu dem Produkt Detail-Informationen gibt, werden diese auf der Produktseite angezeigt. Dazu muß aber auch die EAN &uuml;bergeben werden.',
            'valuehint' => 'eBay Produktinfos anzeigen',
        ),
        'privatelisting' => array(
            'label' => 'Privat-Listings',
            'help' => 'Funktion aktiviert: Listings werden als \'privat\' gekennzeichnet, das hei&szlig;t, die K&auml;ufer- bzw. Bieterliste ist nicht &ouml;ffentlich einsehbar.',
            'valuehint' => 'K&auml;ufer / Bieterliste nicht &ouml;ffentlich',
        ),
        'hitcounter' => array(
            'label' => 'Besucherz&auml;hler',
            'help' => 'Voreinstellung f&uuml den Besucherz&auml;hler f&uuml;r die Listings.',
        ),
        'imagesize' => array(
            'label' => 'Bildgr&ouml;&szlig;e',
            'help' => '<p>Geben Sie hier die Pixel-Breite an, die Ihr Bild auf dem Marktplatz haben soll.
Die H&ouml;he wird automatisch dem urspr&uuml;nglichen Seitenverh&auml;ltnis nach angepasst.</p>
<p>
Die Quelldateien werden aus dem Bildordner {#setting:sSourceImagePath#} verarbeitet und mit der hier gew&auml;hlten Pixelbreite im Ordner {#setting:sImagePath#}  f&uuml;r die &Uuml;bermittlung zum Marktplatz abgelegt.</p>',
            'hint' => 'Gespeichert unter: {#setting:sImagePath#}'
        ),
        'picturepack' => array(
            'label' => 'Bilderpaket',
            'help' => '
                <b>Bilderpaket</b><br><br>
				Durch Aktivieren der Funktion "Bilderpaket" können Sie in der Artikelansicht auf eBay zusätzlich dem Hauptbild oben links bis zu 12 weitere Bilder anzeigen lassen. Der Käufer kann sich die Bilder größer anzeigen lassen ("XXL-Foto") sowie Ausschnitte zoomen ("Zoom-Funktion"). Besondere Einstellungen in Ihrem eBay-Konto sind nicht notwendig.<br><br>
				<b>Variantenbilder</b><br><br>
				Sollten Sie in Ihrem Web-Shop Variantenbilder gepflegt haben, werden diese entsprechend übermittelt (bis zu 12 Bilder pro Variante) und unter dem Hauptbild auf eBay angezeigt.<br><br>
				<b>Hinweis</b><br><br>
				magnalister verarbeitet die Basisdaten Ihres Web-Shops. Sollte Ihr Shop-System keine Variantenbilder unterstützen, ist diese Funktion somit auch über magnalister nicht verfügbar.<br><br>
				<b>XXL-Foto und "Zoom" Funktion</b><br><br>
				Um die Features "XXL-Bilder" und "Zoom" nutzen zu können, verwenden Sie bitte möglichst große Bilder. Wenn ein Bild zu klein ist (weniger als <b>1000px</b> auf der längsten Seite), wird es zwar hochgeladen, aber im Fehlerlog erscheint eine Warnung.<br><br>
				<b>Bildübertragung im https-Protokoll (sichere Bild-URLs)</b><br><br>
				Ohne das Bilderpaket gestattet eBay keine Verlinkungen Ihrer Bilder auf gesicherte URLs (https://...). Mit Aktivierung wird der eBay Picture Service verwendet, bei dem https-Adressen erlaubt sind.<br><br>
				<b>Verarbeitungsdauer</b><br><br>
				Mit Aktivierung der Funktion werden die Bilder beim Hochladen zuerst durch eBay verarbeitet und auf den eBay Servern gespeichert, bevor die restlichen Produktdaten übermittelt werden. Je nach Bildgröße werden dafür 2-5 Sekunden pro Bild benötigt.<br><br>
				Um die Verarbeitungsdauer auf Shop-Seite zu verkürzen, werden die übermittelten Daten über die magnalister-Server zwischengepuffert. Etwaiges Fehler-Feedback von eBay kann erst nach der endgültigen Übergabe an eBay angezeigt werden und ist im Fehlerlog zu finden.<br><br>
				<b>Wann werden im Web-Shop geänderte Bilder aktualisiert?</b><br><br>
				Wenn Sie das eBay Bilderpaket ausgewählt haben, werden geänderte Bilder beim Hochladen immer aktualisiert.<br>
				Ohne Bilderpaket verlangt eBay zum Aktualisieren die Änderung des Bildpfads oder Bildnamen.<br><br>
				<b>Internationale eBay-Accounts</b><br><br>
				Je nach Land können die Features seitens eBay geringfügig abweichen, ggf kann deren Nutzung auch Kosten verursachen. Informieren Sie sich bitte bei eBay direkt, sollten Sie einen eBay Account nutzen, der nicht in der Region der DACH-Länder liegt.
            ',
            'valuehint' => 'Bilderpaket aktiv',
        ),
        'productfield.brand' => array(
            'label' => 'Marke',
        )
    )
        ), false);

MLI18n::gi()->add('ebay_config_price', array(
    'legend' => array(
        'price' => 'Preisberechnung',
        'fixedprice' => '<b>Einstellungen f&uuml;r Festpreis-Listings</b>',
        'chineseprice' => '<b>Einstellungen f&uuml;r Steigerungsauktionen</b>',
    ),
    'field' => array(
        'fixed.quantity' => array(
            'label' => 'St&uuml;ckzahl',
            'help' => 'Geben Sie hier an, wie viel Lagermenge eines Artikels auf dem Marktplatz verf&uuml;gbar sein soll.<br/>' .
            '<br/>' .
            'Sie k&ouml;nnen die St&uuml;ckzahl direkt unter "Hochladen" einzeln ab&auml;ndern - in dem Fall ist es empfehlenswert,<br/>' .
            'die automatische Synchronisation unter "Synchronisation des Inventars" > "Lagerver&auml;nderung Shop" auszuschalten.<br/>' .
            '<br/>' .
            'Um &Uuml;berverk&auml;ufe zu vermeiden, k&ouml;nnen Sie den Wert<br/>' .
            '"Shop-Lagerbestand &uuml;bernehmen abzgl. Wert aus rechtem Feld" aktivieren.<br/>' .
            '<br/>' .
            '<strong>Beispiel:</strong> Wert auf "2" setzen. Ergibt &#8594; Shoplager: 10 &#8594; eBay-Lager: 8<br/>' .
            '<br/>' .
            '<strong>Hinweis:</strong> Wenn Sie Angebote zu Artikeln, die im Shop inaktiv gesetzt werden,<br/>' .
            'unabh&auml;ngig der verwendeten Lagermengen auch auf eBay beenden wollen, gehen Sie bitte wie folgt vor:<br/>' .
            '<ul>' .
            '<li>Synchronisation des Inventars" > "Lagerver&auml;nderung Shop" auf "automatische Synchronisation per CronJob" einstellen</li>' .
            '<li>"Globale Konfiguration" > "Produktstatus" > "Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</li>' .
            '</ul>',
        ),
        'maxquantity' => array(
            'label' => 'St&uuml;ckzahl-Begrenzung',
            'help' => 'Hier k&ouml;nnen Sie die St&uuml;ckzahlen der auf eBay eingestellten Artikel begrenzen.<br /><br />' .
            '<strong>Beispiel:</strong> Sie stellen bei "St&uuml;ckzahl" ein "Shop-Lagerbestand &uuml;bernehmen", und tragen hier 20 ein. Dann werden beim Hochladen so viel St&uuml;ck eingestellt wie im Shop vorhanden, aber nicht mehr als 20. Die Lagersynchronisierung (wenn aktiviert) gleicht die eBay-St&uuml;ckzahl an den Shopbestand an, solange der Shopbestand unter 20 St&uuml;ck ist. Wenn im Shop mehr als 20 St&uuml;ck auf Lager sind, wird die eBay-St&uuml;ckzahl auf 20 gesetzt.<br /><br />' .
            'Lassen Sie dieses Feld leer oder tragen Sie 0 ein, wenn Sie keine Begrenzung w&uuml;nschen.<br /><br />' .
            '<strong>Hinweis:</strong> Wenn die "St&uuml;ckzahl"-Einstellung "Pauschal (aus rechtem Feld)" ist, hat die Begrenzung keine Wirkung.',
        ),
        'fixed.price' => array(
            'label' => 'Preis',
            'hint' => '',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen.'
        ),
        'fixed.price.addkind' => array(
            'label' => '',
            'hint' => '',
        ),
        'fixed.price.factor' => array(
            'label' => '',
            'hint' => '',
        ),
        'fixed.price.signal' => array(
            'label' => 'Nachkommastelle',
            'hint' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu ebay als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
                <strong>Beispiel:</strong> <br />
                Wert im Textfeld: 99 <br />
                Preis-Ursprung: 5.58 <br />
                Finales Ergebnis: 5.99 <br /><br />
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br/>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br/>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            '
        ),
        'fixed.priceoptions' => array(
            'label' => 'Preisoptionen',
            'help' => '{#i18n:configform_price_field_priceoptions_help#}',
            'hint' => '',
        ),
        'fixed.price.group' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.quantity' => array(
            'label' => 'St&uuml;ckzahl',
        ),
        'chinese.price' => array(
            'label' => 'Startpreis',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen. \'Fester Wert\' bedeutet, der hier eingetragene Wert wird direkt &uuml;bernommen (z.B. wenn Sie immer einen Startpreis von 1 Euro verwenden wollen).',
        ),
        'chinese.price.addkind' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.price.factor' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.price.signal' => array(
            'label' => 'Nachkommastelle',
            'hint' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu ebay als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
                <strong>Beispiel:</strong> <br />
                Wert im Textfeld: 99 <br />
                Preis-Ursprung: 5.58 <br />
                Finales Ergebnis: 5.99 <br /><br />
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br/>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br/>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            '
        ),
        'chinese.priceoptions' => array(
            'label' => 'Preisoptionen',
            'help' => '{#i18n:configform_price_field_priceoptions_help#}',
            'hint' => '',
        ),
        'chinese.price.group' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.buyitnow.price' => array(
            'label' => 'Sofortkauf-Preis',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen.<br/>
						Der Sofort-Kaufen Preis muss mindestens 40&#37; h&ouml;her sein, als der Startpreis.',
        ),
        'chinese.buyitnow.price.addkind' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.buyitnow.price.factor' => array(
            'label' => '',
            'hint' => '',
        ),
        'chinese.buyitnow.price.signal' => array(
            'label' => 'Nachkommastelle',
            'hint' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu ebay als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
                <strong>Beispiel:</strong> <br />
                Wert im Textfeld: 99 <br />
                Preis-Ursprung: 5.58 <br />
                Finales Ergebnis: 5.99 <br /><br />
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br/>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br/>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            '
        ),
        'chinese.buyitnow.priceoptions' => array(
            'label' => 'Preisoptionen',
            'hint' => '',
        ),
        'buyitnowprice' => array(
            'label' => 'Sofortkauf-Preis aktiv',
            'hint' => '',
        ),
        'fixed.duration' => array(
            'label' => 'Dauer des Listings',
            'help' => 'Voreinstellung f&uuml;r die Dauer der Festpreis-Listings. Die Einstellung kann bei der Vorbereitung der Artikel ge&auml;ndert werden.',
        ),
        'ebayplus' => array(
            'label' => 'eBay Plus',
            'valuehint' => 'Artikel mit eBay Plus einstellen',
            'help' => '<a href="http://verkaeuferportal.ebay.de/ebay-plus" target="_blank">eBay Plus</a> kann &uuml;ber den eBay Account aktiviert werden, sofern Sie von eBay daf&uuml;r freigeschaltet wurden. Dieses Feature wird z.Zt. nur bei eBay Deutschland angeboten.<br /><br />
                           Die Checkbox hier ist eine Voreinstellung f&uuml;r das Hochladen &uuml;ber magnalister. Sie ist anklickbar wenn eBay Plus in Ihrem Account aktiv ist. Sie hat keinen Einflu&szlig; auf die eBay Voreinstellung f&uuml;r alle Artikel (diese kann nur &uuml;ber den eBay Account aktiviert werden).<br /><br />
                           Sollte die Checkbox nicht anklickbar sein, obwohl Sie die Funktion bei eBay aktiviert haben, speichern Sie bitte die Konfiguration einmal ab (dabei ruft magnalister die aktuellen Einstellungen von eBay ab).<br /><br />
                           <b>Hinweise:</b><ul>
                           <li>F&uuml;r eBay Plus Listings m&uuml;ssen weitere Bedingungen erf&uuml;t werden: 1 Monat R&uuml;cknahmefrist, M&ouml;glichkeit der PayPal Zahlung, eine <a href="http://verkaeuferportal.ebay.de/versand-bei-ebay-plus" target="_blank">f&uuml;r eBay Plus zugelassene Versandart</a>. Wir bekommen <b>keine R&uuml;ckmeldung</b> von eBay, ob diese Bedingungen zutreffen, Sie m&uuml;ssen selbst darauf achten.</li>
                           <li>Bitte lassen Sie Bestell-&Auml;nderungen zu (unter Bestellsynchronisation), oder verwenden Sie die Funktion &quotNur bezahlt-markierte Bestellungen importieren&quot (unter Bestellimport). Die eBay Plus Kennzeichnung f&uuml;r Bestellungen wird uns nicht mit der ersten Bestell-Nachricht von eBay mitgeteilt, sondern erst dann, wenn der K&auml;ufer Zahlungs- und Versandart gew&auml;hlt hat.</li>
                           <li>Es kommt vor, dass eBay Plus Bestellungen mit nicht zugelassenen Versandarten ankommen. In der Detailansicht der Bestellung wird in solchen F&auml;llen eine Warnung angezeigt.</li></ul>',
        ),
        'fixed.price.usespecialoffer' => array(
            'label' => 'auch Sonderpreise verwenden',
            'hint' => '',
        ),
        'chinese.price.usespecialoffer' => array(
            'label' => 'auch Sonderpreise verwenden',
            'hint' => '',
        ),
        'chinese.duration' => array(
            'label' => 'Dauer der Auktion',
            'help' => 'Voreinstellung f&uuml;r die Dauer der Auktion. Die Einstellung kann bei der Vorbereitung der Artikel ge&auml;ndert werden.',
        ),
        'bestofferenabled' => array(
            'label' => 'Preisvorschlag',
            'help' => 'Funktion aktiviert: K&auml;ufer k&ouml;nnen eigene Preise f&uuml;r die Artikel vorschlagen.<br /><br />
                           Diese Einstellung ist nur bei \'einfachen\' Artikeln (ohne Varianten) wirksam. Wenn Varianten da sind, wird sie ignoriert.',
            'valuehint' => '\'Preisvorschlag\' aktivieren (gilt nur f&uuml;r Artikel ohne Varianten)',
        ),
        'exchangerate_update' => array(
            'label' => 'Wechselkurs',
            'valuehint' => 'Wechselkurs automatisch aktualisieren',
            'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
            'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
        ),
    ),
        ), false);


MLI18n::gi()->add('ebay_config_sync', array(
    'legend' => array(
        'syncchinese' => '<b>Einstellungen f&uuml;r Steigerungsauktionen</b>',
        'sync' => array(
            'title' => 'Synchronisation des Inventars',
            'info' => 'Legt fest, welche Produkteigenschaften des Produktes in diesem Shop ebenfalls bei eBay automatisch aktualisiert werden sollen.<br /><br /><b>Einstellungen f&uuml;r Festpreis-Listings</b>',
        )
    ),
    'field' => array(
        'synczerostock' => array(
            'label' => 'Nullbest&auml;nde synchronisieren',
            'help' => 'Ausverkaufte Angebote werden bei eBay normalerweise beendet. Durch das Neueinstellen und Vergabe einer neuen eBay Angebots geht dann Ihr Produkt-Ranking verloren.
<br /><br />
Damit Ihre ausverkauften Artikel auf eBay automatisch beendet und nach Lagerauff&uuml;llung erneut angeboten werden, ohne dass Ihr Produkt-Ranking verloren geht, unterst&uuml;tzt magnalister mit diesem Feature die eBay Option „Nicht mehr vorr&auml;tig“ f&uuml;r „G&uuml;ltig bis auf Widerruf“-Angebote.
<br /><br />
Aktivieren Sie zus&auml;tzlich zu dieser Funktion bitte direkt in Ihrem eBay.de-Account die Option „Nicht mehr vorr&auml;tig“ in "Mein eBay" > "Verk&auml;ufereinstellungen".
<br /><br />
Beachten Sie, dass die Funktion nur f&uuml;r "G&uuml;ltig bis auf Widerruf“-Angebote" Auswirkungen hat.
<br /><br />
Lesen Sie weitere Hinweise zum dem Thema auf den eBay Hilfeseiten (Suchbegriff “Nicht mehr vorr&auml;tig”).
',
            'valuehint' => 'Nullbest&auml;nde synchronisieren aktiv',
        ),
        'syncrelisting' => array(
            'label' => 'Auto-Relisting',
            'help' => 'Mit Aktivierung dieser Funktion werden Ihre Artikel auf eBay vollautomatisch wieder eingestellt, wenn:
<ul>
<li>Ihr Angebot endet, ohne dass ein Gebot vorliegt</li>
<li>Sie die Transaktion abbrechen</li>
<li>Sie Ihr Angebot vorzeitig beenden</li>
<li>der Artikel nicht verkauft wurde oder</li>
<li>der K&auml;ufer den Artikel nicht bezahlt hat.</li>
</ul>

Beachten Sie, dass eBay maximal 2 Re-Listings zul&auml;sst. 
<br />
Lesen Sie weitere Hinweise zum dem Thema auf den eBay Hilfeseiten (Suchbegriff “Artikel wiedereinstellen”).
',
            'valuehint' => 'Auto-Relisting aktiv',
        ),
        'syncproperties' => array(
            'label' => 'EAN, MPN & Hersteller Synchronisation',
            'help' => '<p>eBay verlangt in vielen Kategorien f&uuml;r Ihre Artikel die Produktkennzeichnung durch EAN*, MPN (Herstellerartikelnummer) und den Hersteller (Marke). Wenn diese Attribute nicht &uuml;bermittelt werden, hat das Auswirkungen auf Ihr eBay-Produkt-Ranking. Auch werden &Auml;nderungen wie Preis- und Lagersynchronisationen f&uuml;r bestehende Angebote seitens eBay zur&uuml;ckgewiesen.  
<br /><br />Durch Aktivierung der EAN, MPN und Hersteller Synchronisation k&ouml;nnen Sie entsprechenden Werte per Knopfdruck zu eBay &uuml;bermitteln. Verwenden Sie daf&uuml;r den neuen Synchro-Button (erscheint wenn die EAN &amp; MPN Synchronisation gebucht ist) links vom Bestellimport-Button.  
<br /><br />Dabei werden auch Artikel synchronisiert, die nicht &uuml;ber magnalister gelistet wurden und deren eBay-Bestandseinheit &uumlber die Artikelnummer sowohl auf eBay als auch im Web-Shop identisch ist und damit erkannt werden (vgl. “magnalister” > “eBay” > “Inventar”). Die allererste Synchronisation kann dabei bis zu 24 Stunden dauern.
</p><p>
Bei <b>Varianten</b> wird, wenn an der Variante keine EAN hinterlegt ist, die EAN des Hauptartikels verwendet. Falls die EAN an einem Teil der Varianten hinterlegt ist, und die Hauptartikel-EAN nicht, wird eine der hinterlegten EANs genommen und f&uuml;r die restlichen Varianten des Artikels mit verwendet. Die Werte werden auch bei der &quot;normalen&quot; Preis- und Lagersynchronisation nachgetragen, sofern Sie das &quot;EAN &amp; MPN Synchronisation&quot; AddOn gebucht haben.<br />
</p><p>
*Sie k&ouml;nnen &uumlber das EAN-Feld auch ISBN oder UPC &uuml;bermitteln. Der magnalister-Server erkennt automatisch, welcher Bezeichner von eBay vorausgesetzt wird.
  </p><p>
<strong>Wichtig:  </strong><br /><br />
{#i18n:sAddAewShopAttributeInstruction#}
	<ul>
        <li>Herstellerartikelnummer (MPN) </li>
	<li>EAN</li>
    <li>Hersteller *¹</li>
        </ul><br />
*¹ <font color="green" >Der Hersteller kann zus&auml;tzlich unter “magnalister” > “eBay” > “Konfiguration” > “Arikelvorbereitung” > “Marke” abweichend konfiguriert werden.</font>        
  </p><p>
<strong>Weitere wichtige Hinweise: </strong><br /><br />

eBay erlaubt das &uuml;bermitteln von Platzhaltern f&uuml;r EAN und MPN (“Nicht zutreffend”) anstelle der echten Werte. Diese Produkte werden jedoch schlechter auf eBay gerankt und daher weniger gut gefunden!  
<br /><br />
magnalister sendet diese eBay-Platzhalter f&uuml;r Artikel, an denen keine EAN oder MPN gefunden werden, um zumindest die &Auml;nderung von bestehenden Angeboten zu erm&ouml;glichen.

</p>',
            'valuehint' => 'EAN & MPN Synchronisation aktiv',
        ),
        'stocksync.tomarketplace' => array(
            'label' => 'Lagerver&auml;nderung Shop',
            'hint' => '',
            'help' => '<dl>' .
            '<dt>Bestellung / Artikel bearbeiten &auml;ndert eBay-Lagerbestand (Differenz)</dt>' .
            '<dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)' .
            'den aktuellen eBay-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br>' .
            'Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. ' .
            'eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>' .
            'Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>' .
            'Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, ' .
            'indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>' .
            '<i>{#setting:sSyncInventoryUrl#}</i><br>' .
            'Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.' .
            '</dd>' .
            '<dt>Bestellung / Artikel bearbeiten setzt eBay-Lagerbestand gleich Shop-Lagerbestand</dt>' .
            '<dd>Wenn der Lagerbestand im Shop durch eine Bestellung oder durch das Bearbeiten des Artikels ge&auml;ndert wird,' .
            'wird der dann g&uuml;ltige aktuelle Lagerbestand vom Shop zu eBay &uuml;bertragen.<br>' .
            '&Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>' .
            '<dt>Bestellung / Artikel bearbeiten &auml;ndert eBay-Lagerbestand (Differenz)</dt>' .
            '<dd>Wenn z. B. im Shop ein Artikel 2 mal gekauft wurde, wird der Lagerbestand bei eBay um 2 reduziert.<br />' .
            'Wenn die Artikelanzahl unter "Artikel bearbeiten" im Shop ge&auml;ndert wird, wird die Differenz zum vorigen Stand aufaddiert bzw. abgezogen.<br>' .
            '&Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>' .
            '</dl>' .
            '<b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Artikel hochladen: Voreinstellungen" &rarr; "St&uuml;ckzahl Lagerbestand" werden f&uuml;r die ' .
            'ersten beiden Optionen ber&uuml;cksichtigt.</dd>' .
            '</dl>',
        ),
        'stocksync.frommarketplace' => array(
            'label' => 'Lagerver&auml;nderung ebay',
            'hint' => '',
            'help' => 'Wenn z. B. bei ebay ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br /><br />
				           <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'hint' => '',
            'help' => '<dl>' .
            '<dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>' .
            '<dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)' .
            'den eBay-Preis an den Shop-Preis an (mit ggf. Auf- oder Absch&auml;gen, je nach Konfiguration).<br>' .
            'Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. ' .
            'eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>' .
            'Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>' .
            'Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, ' .
            'indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>' .
            '<i>{#setting:sSyncInventoryUrl#}</i><br>' .
            'Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.' .
            '</dd>' .
            '</dl><br>' .
            '<b>Hinweise:</b><ul><li>Beim Abgleich werden die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" ber&uuml;cksichtigt.</li>' .
            '<li>Preise, die in der Vorbereitung eingefroren wurden, sind von der Synchronisierung ausgenommen.</li></ul>',
        ),
        'inventory.import' => array(
            'label' => 'Fremdartikel synchronisieren',
            'help' => 'Sollen Artikel, die nicht &uuml;ber magnalister eingestellt wurden, mit angezeigt und synchronisiert werden? <br/><br/>' .
            'Wenn die Funktion aktiviert ist, werden alle Artikel, die f&uuml;r diesen eBay Account bei eBay angeboten werden, jede Nacht in die magnalister Datenbank geladen und im Plugin unter \'Listings\' angezeigt.<br/><br/>' .
            'Die Preis- und Lagersynchronisierung funktioniert f&uuml;r diese Artikel auch, soweit die SKU (Bestandseinheit) auf eBay mit einer Artikelnummer im Shop &uuml;bereinstimmt.<br/><br/>' .
            'Ausserdem m&uuml;ssen Sie unter "Globaler Konfiguration" > "Synchronisation Nummernkreise" > "Artikelnummer (Shop) = SKU (Marketplace)" eingestellt haben.<br/>' .
            'Bitte achten Sie darauf, dass wenn Sie die Nummernkreise &auml;ndern, diese auf den Marktpl&auml;tzen komplett erneuert werden m&uuml;ssen, um eine korrekte Synchronisation sicher zu stellen.<br/>' .
            'Lassen Sie sich hier ggf. beraten.<br/><br/>' .
            'Diese Funktionalit&auml;t ist momentan nicht f&uuml;r Fremdartikel mit Varianten verf&uuml;gbar.<br/><br/>' .
            '<b>Achtung:</b> Artikel, die zwar &uuml;ber magnalister eingestellt, aber sp&auml;ter auf eBay ge-re-listed wurden, erkennt magnalister durch die Vergabe einer neuen eBay Angebotsnummer nur noch als Fremdartikel. Schalten Sie diese Funktion also nicht! aus, wenn Sie ge-re-listete Artikel auch automatisch synchronisieren lassen wollen!',
        ),
        'chinese.stocksync.tomarketplace' => array(
            'label' => 'Lagerver&auml;nderung Shop',
            'help' => '<dl>
				<dt>Automatische Synchronisierung per CronJob</dt>
					<dd>Die Funktion "Automatische Synchronisierung" pr&uuml;ft alle 4 Stunden (beginnt um 0:00 Uhr nachts)
					    den aktuellen Shop-Lagerbestand und l&ouml;scht eBay-Auktionen f&uuml;r Artikel, deren Shop-Bestand 0 ist.<br>
    		            Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
    		            eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
    		            Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
    		            Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, 
    		            indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
    		            <i>{#setting:sSyncInventoryUrl#}</i><br>
    		            Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.
    		        </dd>
				<dt>Bestellung / Lageranzahl verkleinern reduziert eBay-Lagerbestand</dt>
					<dd>Wenn der Lagerbestand im Shop durch eine Bestellung oder durch das Bearbeiten des Artikels auf 0 geht,
						wird die Auktion auf eBay gel&ouml;scht.<br>
						&Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>
			            </dl>
			    <b>Hinweis:</b><ul><li>Sobald auf die Auktion geboten wurde, kann sie nicht mehr gel&ouml;scht werden.</li></ul>',
        ),
        'chinese.stocksync.frommarketplace' => array(
            'label' => 'Lagerver&auml;nderung eBay',
            'help' => 'Wenn z. B. bei eBay ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.',
        ),
        'chinese.inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'help' => '<dl>
							<dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
							    <dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
							        den eBay-Preis an den Shop-Preis an (mit ggf. Auf- oder Absch&auml;gen, je nach Konfiguration).<br>
   			                        Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
   			                        eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
   			                        Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
   			                        Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, 
   			                        indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
   			                        <i>{#setting:sSyncInventoryUrl#}</i><br>
   			                        Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.			                        
   			                        </dd>
						</dl><br>
						<b>Hinweise:</b><ul><li>Beim Abgleich werden die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" ber&uuml;cksichtigt.</li>
                               <li>Sobald auf die Auktion geboten wurde, kann der Preis nicht mehr ge&auml;ndert werden.</li></ul>
			',
        ),
    ),
        ), false);

MLI18n::gi()->add('ebay_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderupdate' => array(
            'title' => 'Bestellstatus Synchronisation',
            'info' => '',
        ),
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu eBay',
    ),
    'field' => array(
        'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'hint' => '',
            'help' =>
            '<dl>' .
            '<dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>' .
            '<dd>Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden (beginnt um 0:00 Uhr nachts) den aktuellen Versendet-Status zu eBay.<br/>' .
            'Dabei werden die Status-Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br/><br/>' .
            'Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie die Bestellung direkt im Web-Shop bearbeiten, dort  den gew&uuml;nschten Status setzen, und dann auf "Aktualisieren" klicken.<br/>' .
            'Sie k&ouml;nnen auch den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise), um den Status sofort zu &uuml;bergeben.<br/><br/>' .
            'Zus&auml;tzlich k&ouml;nnen Sie den Bestellstatus-Abgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br/><br/>' .
            '<i>{#setting:sSyncOrderStatusUrl#}</i><br/><br/>' .
            'Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.' .
            '</dd>' .
            '</dl>',
        ),
        'orderstatus.shipped' => array(
            'label' => 'Versand best&auml;tigen mit',
            'hint' => '',
            'help' => 'Setzen Sie hier den Shop-Status, der auf ebay automatisch den Status "Versand best&auml;tigen" setzen soll.',
        ),
        'orderstatus.canceled' => array(
            'label' => 'Versand widerrufen mit',
            'hint' => '',
            'help' => '
                Setzen Sie hier den Shop-Status, der auf eBay den Versand stornieren soll. <br/><br/>
                Hinweis: Diese Funktion bewirkt da&szlig; die Bestellung nicht mehr als "Versendet" bei eBay steht. Es ist keine Stornierung der Bestellung.
            ',
        ),
        'orderstatus.cancelled' => array(
            'label' => 'Versand widerrufen mit',
            'hint' => '',
            'help' => '
                Setzen Sie hier den Shop-Status, der auf eBay den Versand stornieren soll. <br/><br/>
                Hinweis: Diese Funktion bewirkt da&szlig; die Bestellung nicht mehr als "Versendet" bei eBay steht. Es ist keine Stornierung der Bestellung.
            ',
        ),
        'importonlypaid' => array(
            'label' => 'Nur bezahlt-markierte Bestellungen importieren',
            'help' => '
                <p>Durch Aktivieren der Funktion werden Bestellungen erst dann importiert, wenn Sie auf eBay als „bezahlt“ markiert wurden. Im Falle von PayPal-Bestellungen erfolgt das automatisch. Bei &Uuml;berweisungen muss die Zahlung auf eBay entsprechend markiert werden.
                </p>
                <p>
                <strong>Vorteil:</strong>
                Die importierte Bestellung ist vom K&auml;ufer nicht mehr &auml;nderbar, da komplett abgeschlossen. 
                Adressdaten und Versandkosten werden von eBay 1:1 wie bestellt &uuml;bergeben, so dass eine &Uuml;berwachung auf eBay und eine Aktualisierung im Web-Shop nicht mehr notwendig ist. 
            </p>
            ',
            'alert' => '
                <p>Durch Aktivieren der Funktion werden Bestellungen erst dann importiert, wenn Sie auf eBay als „bezahlt“ markiert wurden. Im Falle von PayPal-Bestellungen erfolgt das automatisch. Bei &Uuml;berweisungen muss die Zahlung auf eBay entsprechend markiert werden.
                </p>
                <p>
                <strong>Vorteil:</strong>
                Die importierte Bestellung ist vom K&auml;ufer nicht mehr &auml;nderbar, da komplett abgeschlossen. 
                Adressdaten und Versandkosten werden von eBay 1:1 wie bestellt &uuml;bergeben, so dass eine &Uuml;berwachung auf eBay und eine Aktualisierung im Web-Shop nicht mehr notwendig ist. 
            </p>',
        ),
        'orderstatus.closed' => array(
            'label' => 'Bestellzusammenfassung beenden',
            'help' => 'Wenn Sie eine Bestellung auf einen der hier ausgew&auml;hlten Stati setzen, werden neue Bestellungen des gleichen Kunden nicht mehr zu dieser hinzugef&uuml;gt. <br />
                Falls Sie keine Bestellzusammenfassung w&uuml;nschen, markieren Sie hier alle Stati.',
        ),
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'hint' => '',
            'help' => '{#i18n:form_config_orderimport_shop_help#}',
        ),
        'orderimport.paymentmethod' => array(
            'label' => 'Zahlart der Bestellungen',
            'help' => '<p>Zahlart, die allen ebay-Bestellungen beim Bestellimport zugeordnet wird. 
Standard: "Automatische Zuordnung"</p>
<p>
Wenn Sie „Automatische Zuordnung" w&auml;hlen, &uuml;bernimmt magnalister die Zahlart, die der K&auml;ufer auf ebay gew&auml;hlt hat.
Diese wird dann zus&auml;tzlich auch unter Shopware > Einstellungen > Zahlungsarten angelegt.</p>
<p>
Alle weiteren verf&uuml;gbaren Zahlarten in der Liste k&ouml;nnen Sie ebenfalls unter Shopware > Einstellungen > Zahlungsarten definieren und hier&uuml;ber dann verwenden.</p>
<p>
Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck, und f&uuml;r die nachtr&auml;gliche Bearbeitung der Bestellung im Shop, sowie in Warenwirtschaften.</p>',
            'hint' => '',
        ),
        'orderimport.shippingmethod' => array(
            'label' => 'Versandart der Bestellungen',
            'help' => 'Versandart, die allen ebay-Bestellungen zugeordnet wird. Standard: "marketplace".<br><br>
				           Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&amul;gliche
				           Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
            'hint' => '',
        ),
        'mwstfallback' => array(
            'label' => 'MwSt. Shop-fremder Artikel',
            'hint' => 'Steuersatz, der f&uuml;r Shop-fremde Artikel bei Bestellimport verwendet wird in %.',
            'help' => '
                Wenn der Artikel nicht &uuml;ber magnalister eingestellt wurde, kann die Mehrwertsteuer nicht ermittelt werden.<br />
                Als L&ouml;sung wird der hier angegebene Wert in Prozent bei allen Produkten hinterlegt, deren Mehrwertsteuersatz beim Bestellimport aus ebay nicht bekannt ist
            ',
        ),
        /* //{search: 1427198983}
          'mwst.shipping' => array(
          'label' => 'MwSt. Versandkosten',
          'hint' => 'Steuersatz f&uuml;r Versandkosten in %.',
          'help' => '
          ebay &uuml;bermittelt nicht den Steuersatz der Versandkosten, sondern nur die Brutto-Preise. Daher muss der Steuersatz zur korrekten Berechnung der Mehrwertsteuer f&uuml;r die Versandkosten hier angegeben werden. Falls Sie mehrwertsteuerbefreit sind, tragen Sie in das Feld 0 ein.
          ',
          ),
          // */
        'importactive' => array(
            'label' => 'Import aktivieren',
            'hint' => '',
            'help' => '
                Sollte der Artikel im Web-Shop nicht gefunden werden, verwendet magnalister den hier hinterlegten Steuersatz, da die Marktpl&auml;tze beim Bestellimport keine Angabe zur Mehrwertsteuer machen.<br />
                <br />
                Weitere Erl&auml;uterungen:<br />
                Grunds&auml;tzlich verh&auml;lt sich magnalister beim Bestellimport bei der Berechnung der Mehrwertsteuer so wie das Shop-System selbst.<br />
                <br />
                Damit die Mehrwertsteuer pro Land automatisch ber&uuml;cksichtigt werden kann, muss der gekaufte Artikel mit seinem des Nummernkreis (SKU) im Web-Shop gefunden werden.<br />
                magnalister verwendet dann die im Web-Shop konfigurierten Steuerklassen.
            '
        ),
        'updateableorderstatus' => array(
            'label' => 'Bestell-&Auml;nderung zulassen wenn',
            'help' => 'Stati der Bestellungen, die bei eBay-Zahlungen ge&auml;ndert werden d&uuml;rfen.
			                Wenn die Bestellung einen anderen Status hat, wird er bei eBay-Zahlungen nicht ge&auml;ndert.<br /><br />
			                Wenn Sie gar keine &Auml;nderung des Bestellstatus bei eBay-Zahlung w&uuml;nschen, deaktivieren Sie die Checkbox.<br /><br />
			                <b>Hinweis:</b> Der Status von zusammengefa&szlig;ten Bestellungen wird nur dann ge&auml;ndert, wenn alle Teile bezahlt sind.',
        ),
        'updateable.orderstatus' => array(
            'label' => '',
            'help' => '',
        ),
        'update.orderstatus' => array(
            'label' => 'Bestell-&Auml;nderung aktiv',
        ),
        'import' => array(
            'label' => '',
            'hint' => '',
        ),
        'preimport.start' => array(
            'label' => 'erstmalig ab Zeitpunkt',
            'hint' => 'Startzeitpunkt',
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit m&ouml;glich ist, da die Daten bei ebay h&ouml;chstens einige Wochen lang vorliegen.',
        ),
        'customergroup' => array(
            'label' => 'Kundengruppe',
            'hint' => '',
            'help' => 'Kundengruppe, zu der Kunden bei neuen Bestellungen zugeordnet werden sollen.',
        ),
        'orderstatus.open' => array(
            'label' => 'Bestellstatus im Shop',
            'hint' => '',
            'help' => '
                Der Status, den eine von ebay neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'orderstatus.paid' => array(
            'label' => 'eBay Bezahlt-Status im Shop',
            'help' => 'Der Status, den Bestellung im Shop bekommt, wenn sie bei eBay bezahlt wird.',
        ),
        'orderstatus.carrier.default' => array(
            'label' => 'Spediteur',
            'help' => 'Vorausgew&auml;hlter Spediteur beim Best&auml;tigen des Versandes nach eBay',
        ),
    ),
        ), false);

MLI18n::gi()->add('ebay_config_emailtemplate', array(
    'legend' => array(
        'mail' => 'Promotion-E-Mail Template',
    ),
    'field' => array(
        'mail.send' => array(
            'label' => 'E-Mail versenden?',
            'help' => 'Soll von Ihrem Shop eine E-Mail an den K&auml;ufer gesendet werden um Ihren Shop zu promoten?',
        ),
        'mail.originator.name' => array(
            'label' => 'Absender Name',
        ),
        'mail.originator.adress' => array(
            'label' => 'Absender E-Mail Adresse',
        ),
        'mail.subject' => array(
            'label' => 'Betreff',
        ),
        'mail.content' => array(
            'label' => 'E-Mail Inhalt',
            'hint' => 'Liste verf&uuml;gbarer Platzhalter f&uuml;r Betreff und Inhalt:
        <dl>
                <dt>#FIRSTNAME#</dt>
                        <dd>Vorname des K&auml;ufers</dd>
                <dt>#LASTNAME#</dt>
                        <dd>Nachname des K&auml;ufers</dd>
                <dt>#EMAIL#</dt>
                        <dd>e-mail Adresse des K&auml;ufers</dd>
                <dt>#PASSWORD#</dt>
                        <dd>Password des K&auml;ufers zum Einloggen in Ihren Shop. Nur bei Kunden, die dabei automatisch angelegt werden, sonst wird der Platzhalter durch \'(wie bekannt)\' ersetzt.</dd>
                <dt>#ORDERSUMMARY#</dt>
                        <dd>Zusammenfassung der gekauften Artikel. Sollte extra in einer Zeile stehen.<br/><i>Kann nicht im Betreff verwendet werden!</i></dd>
                <dt>#ORIGINATOR#</dt>
                        <dd>Absender Name</dd>
        </dl>',
        ),
        'mail.copy' => array(
            'label' => 'Kopie an Absender',
            'help' => 'Die Kopie wird an die Absender E-Mail Adresse gesendet.',
        ),
    ),
        ), false);


MLI18n::gi()->add('ebay_config_producttemplate', array(
    'legend' => array(
        'product' => array(
            'title' => 'Produkt-Template',
            'info' => 'Template f&uuml;r die Produktbeschreibung auf eBay. (Sie k&ouml;nnen den Editor unter "Globale Konfiguration" > "Experteneinstellungen" umschalten.)',
        )
    ),
    'field' => array(
        'template.name' => array(
            'label' => 'Template Produktname',
            'help' => '<dl>
							<dt>Name des Produkts auf eBay</dt>
							 <dd>Einstellung, wie das Produkt auf eBay hei&szlig;en soll.
							     Der Platzhalter <b>#TITLE#</b> wird automatisch durch den Produktnamen aus dem Shop ersetzt,
						  		 <b>#BASEPRICE#</b> durch Preis pro Einheit, soweit f&uuml;r das betreffende Produkt im Shop hinterlegt.</dd>
							<dt>Bitte beachten Sie:</dt>
							 <dd>Der Platzhalter <b>#BASEPRICE#</b> ist normalerweise nicht n&ouml;tig, da magnalister die im Shop hinterlegten Grundpreise automatisch &uuml;bertr&auml;gt, soweit die eBay Kategorie das vorsieht (vgl. &quot;Produkte vorbereiten&quot; &gt; &quot;Attribute f&uuml;r Prim&auml;r-Kategorie&quot;).</dd>
							 <dd>Wenn Sie den Grundpreis nachtr&auml;glich im Web-Shop hinterlegen, laden Sie den Artikel bitte nochmals hoch, damit die &Auml;nderungen auf eBay &uuml;bernommen werden. Die so hochgeladenen Grundpreise werden &uuml;ber die Preisaktualisierung synchron gehalten.</dd>
							 <dd>Nutzen Sie den Platzhalter <b>#BASEPRICE#</b>, wenn Sie nicht-metrische Einheiten verwenden (die eBay nicht akzeptiert), oder Grundpreise auch in Kategorien anzeigen wollen, wo eBay es nicht vorsieht (und der Gesetzgeber es nicht vorschreibt).</dd>
							 <dd>Falls Sie den Platzhalter <b>#BASEPRICE#</b> verwenden, <b>schalten Sie bitte die Preissynchronisation ab</b>. Der Titel kann auf eBay nicht ge&auml;ndert werden, und bei Preis&auml;nderungen w&uuml;rde die Grundpreis-Angabe im Titel dann nicht mehr stimmen.</dd>
							 <dd><b>#BASEPRICE#</b> wird beim Hochladen zu eBay ersetzt.</dd>
							 <dd>F&uuml;r <b>Artikel-Varianten</b> unterst&uuml;tzt eBay die Grundpreise nicht. Daher h&auml;ngen wir die Grundpreise an Varianten-Titel an.</dd>
							 <dd>Beispiel: <br />&nbsp;Variantengruppe: F&uuml;llmenge<ul><li>Variante: 0,33 l (3 EUR / Liter)</li><li>Variante: 0,5 l (2,50 EUR / Liter)</li><li>usw.</li></ul></dd>
							<dd>In diesem Fall schalten Sie bitte ebenfalls <b>die Preissynchronisation ab</b>,  da Varianten-Titel bei eBay nicht ge&auml;ndert werden k&ouml;nnen.</dd>
							</dl>',
            'hint' => 'Platzhalter: #TITLE# - Produktname; #BASEPRICE# - Grundpreis',
        ),
        'template.content' => array(
            'label' => 'Template Produktbeschreibung',
            'hint' => '
                Liste verf&uuml;gbarer Platzhalter f&uuml;r die Produktbeschreibung:
                <dl>
                        <dt>#TITLE#</dt>
                                <dd>Produktname (Titel)</dd>
                        <dt>#ARTNR#</dt>
                                <dd>Artikelnummer im Shop</dd>
                        <dt>#PID#</dt>
                                <dd>Produkt ID im Shop</dd>
                        <!--<dt>#PRICE#</dt>
                                <dd>Preis</dd>
                        <dt>#VPE#</dt>
                                <dd>Preis pro Verpackungseinheit</dd>-->
                        <dt>#SHORTDESCRIPTION#</dt>
                                <dd>Kurzbeschreibung aus dem Shop</dd>
                        <dt>#DESCRIPTION#</dt>
                                <dd>Beschreibung aus dem Shop</dd>
                        <dt>#PICTURE1#</dt>
                                <dd>erstes Produktbild</dd>
                        <dt>#PICTURE2# usw.</dt>
                                <dd>zweites Produktbild; mit #PICTURE3#, #PICTURE4# usw. k&ouml;nnen weitere Bilder &uuml;bermittelt werden, so viele wie im Shop vorhanden.</dd>
                </dl>
                ',
        ),
    ),
        ), false);
