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

MLI18n::gi()->hitmeister_config_account_title = 'Zugangsdaten';
MLI18n::gi()->hitmeister_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->hitmeister_config_account_price = 'Preisberechnung';
MLI18n::gi()->hitmeister_config_account_sync = 'Synchronisation';
MLI18n::gi()->hitmeister_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->hitmeister_config_checkin_badshippingcost = 'Die Versandkosten muss eine Zahl sein.';
MLI18n::gi()->hitmeister_config_checkin_shippingmatching = 'Das Versandzeiten Matching wird von diesem Shop-System nicht unterstützt.';
MLI18n::gi()->hitmeister_config_checkin_manufacturerfilter = 'Das Hersteller Filter wird von diesem Shop-System nicht unterstützt.';

MLI18n::gi()->add('hitmeister_config_account', array(
    'legend' => array(
        'account' => 'Zugangsdaten',
        'tabident' => ''
    ),
    'field' => array(
        'tabident' => array(
            'label' => '{#i18n:ML_LABEL_TAB_IDENT#}',
            'help' => '{#i18n:ML_TEXT_TAB_IDENT#}'
        ),
		'clientkey' => array(
            'label' => 'ClientKey',
            'help' => 'Die API-Zugangsdaten bekommen Sie in Ihrem Hitmeister Account. Dazu loggen Sie sich bitte bei Hitmeister ein und klicken auf <b>Hitmeister API<\/b>, im Men&uuml; links ganz unten, bei <b>Zusatzfunktionen<\/b>.'
        ),
        'secretkey' => array(
            'label' => 'SecretKey',
        ),
		'mpusername' => array(
            'label' => 'Mitgliedsname',
        ),
        'mppassword' => array(
            'label' => 'Passwort',
        ),
    ),
), false);

MLI18n::gi()->add('hitmeister_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'upload' => 'Artikel hochladen: Voreinstellungen'
    ),
    'field' => array(
        'prepare.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
        'checkin.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
        'lang' => array(
            'label' => 'Artikelbeschreibung',
        ),
         'imagepath' => array(
            'label' => 'Bildpfad',
        ),
        'itemcondition' => array(
            'label' => 'Zustand',
        ),
        'shippingtime' => array(
            'label' => 'Lieferzeit',
            'help' => 'Voreinstellung f&uuml;r die Lieferzeit. Diese kann bei Artikel-Vorbereitung noch angepa&szlig;t werden.<br><br> <b>Hinweis:</b><br> Wenn Sie "Experteneinstellungen" aktivieren (Button links unten), k&ouml;nnen Sie ein Lieferzeit-Matching definieren, d.h. die Lieferzeiten, die Sie im Shop verwenden, den Hitmeister-Lieferzeiten zuordnen, so dass jeder Artikel eine Lieferzeit entsprechend der Lieferzeit im Shop bekommt.'
        ),
        'shippingtimematchingsub' => array(
            'label' => 'Lieferzeit Matching',
        ),
        'shippingtimematching' => array(
            'label' => '',
            'matching' => array(
				'titlesrc' => 'Hitmeister Shipping Time',
				'titledst' => 'Shop Shipping Time',
			),
        ),
        'shippingtimematching.prefer' => array(
            'label' => '',
            'valuehint' => 'Lieferzeit Matching bevorzugen',
        ),
        'itemcountry' => array(
            'label' => 'Artikel wird versandt aus',
            'help' => 'Bitte w&auml;hlen Sie aus welchem Land Sie versenden. Im Normalfall ist es das Land in dem Ihr Shop liegt.'
        ),
        'itemsperpage' => array(
            'label' => 'Ergebnisse',
            'help' => 'Hier k&ouml;nnen Sie festlegen, wie viele Produkte pro Seite beim Multimatching angezeigt werden sollen. <br\/>Je h&ouml;her die Anzahl, desto h&ouml;her auch die Ladezeit (bei 50 Ergebnissen ca. 30 Sekunden).',
            'hint' => 'pro Seite beim Multimatching',
        ),
        'checkin.quantity' => array(
            'label' => 'St&uuml;ckzahl Lagerbestand',
            'help' => 'Geben Sie hier an, wie viel Lagermenge eines Artikels auf dem Marktplatz verf&uuml;gbar sein soll.<br>
                <br>
                Sie k&ouml;nnen die St&uuml;ckzahl direkt unter "<i>Hochladen</i>" einzeln ab&auml;ndern - in dem Fall ist es empfehlenswert,<br>
                die automatische Synchronisation unter "<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auszuschalten.<br>
                <br>
                Um &Uuml;berverk&auml;ufe zu vermeiden, k&ouml;nnen Sie den Wert<br>
                "<i>Shop-Lagerbestand &uuml;bernehmen abzgl. Wert aus rechtem Feld</i>" aktivieren.<br>
                <br>
                <strong>Beispiel:</strong> Wert auf "<i>2</i>" setzen. Ergibt &#8594; Shoplager: 10 &#8594; Hitmeister-Lager: 8<br>
                <br>
                <strong>Hinweis:</strong>Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabh&auml;ngig der verwendeten Lagermengen<br>
                auch auf dem Marktplatz als Lager "<i>0</i>" behandeln wollen, gehen Sie bitte wie folgt vor:<br>
                <ul>
                <li>"<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auf "<i>automatische Synchronisation per CronJob" einstellen</i></li>
                <li>"<i>Globale Konfiguration" > "<i>Produktstatus</i>" > "<i>Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</i></li>
                </ul>',
        ),
    ),
), false);

MLI18n::gi()->add('hitmeister_config_price', array(
    'legend' => array(
        'price' => 'Preisberechnung',
    ),
    'field' => array(
        'price' => array(
            'label' => 'Preis',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen.'
        ),
        'price.addkind' => array(
            'label' => '',
        ),
        'price.factor' => array(
            'label' => '',
        ),
        'price.signal' => array(
            'label' => 'Nachkommastelle',
            'hint' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu Hitmeister als Nachkommastelle an
                Ihrem Preis &uuml;bernommen.<br><br>
                <strong>Beispiel:</strong><br>
                Wert im Textfeld: 99<br>
                Preis-Ursprung: 5.58<br>
                Finales Ergebnis: 5.99<br><br>
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            ',
        ),
        'priceoptions' => array(
            'label' => 'Preisoptionen',
            'help' => '{#i18n:configform_price_field_priceoptions_help#}',
        ),
        'price.group' => array(
            'label' => '',
        ),
        'price.usespecialoffer' => array(
            'label' => 'auch Sonderpreise verwenden',
        ),
        'exchangerate_update' => array(
            'label' => 'Wechselkurs',
            'valuehint' => 'Wechselkurs automatisch aktualisieren',
            'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
            'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
        ),
    ),
), false);

MLI18n::gi()->add('hitmeister_config_sync', array(
    'legend' => array(
        'sync' => 'Synchronisation des Inventars',
    ),
    'field' => array(
        'stocksync.tomarketplace' => array(
            'label' => 'Lagerver&auml;nderung Shop',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen Hitmeister-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br>
                    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
                    eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
                    Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der
                    Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
                    Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch
                    einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
                    <i>{#setting:sSyncInventoryUrl#}</i><br>
                    Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen,
                    werden geblockt.<br><br>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Einstellvorgang" &rarr; "St&uuml;ckzahl Lagerbestand"
                    werden ber&uuml;cksichtigt.
				</p>
            ',
        ),
        'stocksync.frommarketplace' => array(
            'label' => 'Lagerver&auml;nderung Hitmeister',
            'help' => '
                Wenn z. B. bei Hitmeister ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br><br>
                <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!
            ',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen Shop-Preis auf Ihren Hitmeister-Preis an.<br>
                    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
                    eine Warenwirtschaft nur in der Datenbank erfolgten.
                    <br><br>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" werden ber&uuml;cksichtigt.
                </p>
            ',
        ),
    ),
), false);

MLI18n::gi()->add('hitmeister_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu Hitmeister',
    ),
    'field' => array(
		'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>
                        Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden den aktuellen Versendet-Status zu Hitmeister.<br/>
                        Dabei werden die Status-Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br/><br/>
                        Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie die Bestellung direkt im Web-Shop bearbeiten, dort  den gew&uuml;nschten Status setzen, und dann auf "Aktualisieren" klicken.<br/>
                        Sie k&ouml;nnen auch den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise), um den Status sofort zu &uuml;bergeben.<br/><br/>
                        Zus&auml;tzlich k&ouml;nnen Sie den Bestellstatus-Abgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br/><br/>
                        <i>{#setting:sSyncOrderStatusUrl#}</i><br/><br/>
                        Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.
                    </dd>
                </dl>
            ',
        ),
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'hint' => '',
            'help' => '{#i18n:form_config_orderimport_shop_help#}',
        ),
        'orderstatus.shipped' => array(
            'label' => 'Versand best&auml;tigen mit',
            'help' => 'Setzen Sie hier den Shop-Status, der auf Hitmeister automatisch den Status "Versand best&auml;tigen" setzen soll.',
        ),
        'orderstatus.cancelled' => array(
            'label' => 'Bestellung stornieren mit',
            'help' => ' Setzen Sie hier den Shop-Status, der auf  MercadoLivre automatisch den Status "Bestellung stornieren" setzen soll. <br/><br/>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese Funktion storniert
                und dem K&auml;ufer gutgeschrieben.',
        ),
        'orderstatus.carrier' => array(
            'label' => 'Spediteur',
            'help' => 'Vorausgew&auml;hlter Spediteur beim Best&auml;tigen des Versandes nach _#_platformName_#_.',
        ),
        'orderstatus.cancelreason' => array(
            'label' => 'Bestellung stornieren Grund',
            'help' => 'Der Grund warum die Bestellung storniert wird.',
        ),
        'mwst.fallback' => array(
            'label' => 'MwSt. Shop-fremder Artikel',
            'hint' => 'Steuersatz, der f&uuml;r Shop-fremde Artikel bei Bestellimport verwendet wird in %.',
            'help' => '
                Sollte der Artikel im Web-Shop nicht gefunden werden, verwendet magnalister den hier hinterlegten Steuersatz, da die Marktpl&auml;tze beim Bestellimport keine Angabe zur Mehrwertsteuer machen.<br />
                <br />
                Weitere Erl&auml;uterungen:<br />
                Grunds&auml;tzlich verh&auml;lt sich magnalister beim Bestellimport bei der Berechnung der Mehrwertsteuer so wie das Shop-System selbst.<br />
                <br />
                Damit die Mehrwertsteuer pro Land automatisch ber&uuml;cksichtigt werden kann, muss der gekaufte Artikel mit seinem des Nummernkreis (SKU) im Web-Shop gefunden werden.<br />
                magnalister verwendet dann die im Web-Shop konfigurierten Steuerklassen.
            ',
        ),
        'importactive' => array(
            'label' => 'Import aktivieren',
            'hint' => '',
            'help' => '
                Sollen Bestellungen aus den Marktplatz importiert werden? <br/><br/>Wenn die Funktion aktiviert ist, 
                werden Bestellungen voreingestellt st&uuml;ndlich importiert.<br><br>
                Sie k&ouml;nnen die Zeiten der automatischen Bestellimporte selbst unter<br> 
				"magnalister Admin" &rarr; "Globale Konfiguration" &rarr; "Bestellabrufe" bestimmen.<br><br>
				Einen manuellen Import k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in 
                der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
				Zus&auml;tzlich k&ouml;nnen Sie den Bestellimport (ab Tarif Flat - maximal viertelst&uuml;ndlich) 
                auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
    			<i>{#setting:sImportOrdersUrl#}</i><br><br>
    			Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als 
                viertelst&uuml;ndlich laufen, werden geblockt.						   
            ',
        ),
        'import' => array(
            'label' => '',
        ),
        'preimport.start' => array(
            'label' => 'erstmalig ab Zeitpunkt',
            'hint' => 'Startzeitpunkt',
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, '
                . 'dass dies nicht beliebig weit in die Vergangenheit m&ouml;glich ist, da die Daten bei Hitmeister '
                . 'h&ouml;chstens einige Wochen lang vorliegen.',
        ),
		'orderstatus.open' => array(
            'label' => 'Bestellstatus im Shop',
            'hint' => '',
            'help' => '
                Der Status, den eine von DaWanda neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'customergroup' => array(
            'label' => 'Kundengruppe',
            'help' => 'Kundengruppe, zu der Kunden bei neuen Bestellungen zugeordnet werden sollen.',
        ),
    ),
), false);
