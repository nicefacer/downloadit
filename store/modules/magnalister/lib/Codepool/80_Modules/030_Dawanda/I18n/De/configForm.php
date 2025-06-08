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

MLI18n::gi()->dawanda_config_account_title = 'Zugangsdaten';
MLI18n::gi()->dawanda_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->dawanda_config_account_price = 'Preisberechnung';
MLI18n::gi()->dawanda_config_account_sync = 'Synchronisation';
MLI18n::gi()->dawanda_config_account_orderimport = 'Bestellimport';


MLI18n::gi()->add('dawanda_config_account', array(
    'legend' => array(
        'account' => 'Zugangsdaten',
        'tabident' => ''
    ),
    'field' => array(
        'tabident' => array(
            'label' => '{#i18n:ML_LABEL_TAB_IDENT#}',
            'hint' => '',
            'help' => '{#i18n:ML_TEXT_TAB_IDENT#}'
        ),
        'mpusername' => array(
            'label' => 'Mitgliedsname',
            'hint' => '',
        ),
        'mppassword' => array(
            'label' => 'Passwort',
            'hint' => '',
        ),
        'apikey' => array(
            'label' => 'Anwendungs&#8209;Schlüssel',
            'hint' => '',
        ),
    ),
), false);

MLI18n::gi()->add('dawanda_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'upload' => 'Artikel hochladen: Voreinstellungen'
    ),
    'field' => array(
        'prepare.status' => array(
            'label' => 'Statusfilter',
            'hint' => 'nur aktive Artikel übernehmen',
        ),
        'producttype' => array(
            'label' => 'Art des Produktes',
            'hint' => ''
        ),
        'returnpolicy' => array(
            'label' => 'Widerrufsbelehrung',
            'hint' => '',
        ),
        'checkin.status' => array(
            'label' => 'Statusfilter',
            'hint' => 'nur aktive Artikel übernehmen',
        ),
        'langs' => array(
            'label' => 'Artikelbeschreibung',
            'hint' => '',
            'matching' => array(
                'titlesrc' => 'DaWanda Sprache',
                'titledst' => 'Shop Sprache',
            )
        ),
        'quantity' => array(
            'label' => 'St&uuml;ckzahl Lagerbestand',
            'hint' => '',
            'help' => '
                Geben Sie hier an, wie viel Lagermenge eines Artikels auf dem Marktplatz verf&uuml;gbar sein soll.<br/>
                <br/>
				Sie k&ouml;nnen die St&uuml;ckzahl direkt unter "<i>Hochladen</i>" einzeln ab&auml;ndern - in dem Fall ist es empfehlenswert,<br/>
				die automatische Synchronisation unter "<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auszuschalten.<br/>
				<br/>
				Um &Uuml;berverk&auml;ufe zu vermeiden, k&ouml;nnen Sie den Wert<br/>
				"<i>Shop-Lagerbestand &uuml;bernehmen abzgl. Wert aus rechtem Feld</i>" aktivieren.<br/>
				<br/>
				<strong>Beispiel:</strong> Wert auf "<i>2</i>" setzen. Ergibt &#8594; Shoplager: 10 &#8594; DaWanda-Lager: 8<br/>
				<br/>
				<strong>Hinweis:</strong>Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabh&auml;ngig der verwendeten Lagermengen<br/>
				auch auf dem Marktplatz als Lager "<i>0</i>" behandeln wollen, gehen Sie bitte wie folgt vor:<br/>
				<ul>
                    <li>"<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auf "<i>automatische Synchronisation per CronJob" einstellen</i></li>
                    <li>"<i>Globale Konfiguration" > "<i>Produktstatus</i>" > "<i>Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</i></li>
				</ul>
            ',
        ),
        'checkin.leadtimetoship' => array(
            'label' => 'Versand',
            'hint' => ''
        ),
        'checkin.manufacturerfallback' => array(
            'label' => 'Alternativ-Hersteller',
            'hint' => '',
            'help' => 'Falls ein Produkt keinen Hersteller hinterlegt hat, wird der hier angegebene Hersteller verwendet.'
        ),
        'imagesize' => array(
            'label' => '{#i18n:form_config_orderimport_imagesize_lable#}',
            'help' => '{#i18n:form_config_orderimport_imagesize_help#}',
            'hint' => '{#i18n:form_config_orderimport_imagesize_hint#}'
        ),
    ),
), false);

MLI18n::gi()->add('dawanda_config_price', array(
    'legend' => array(
        'price' => 'Preisberechnung',
    ),
    'field' => array(
        'price' => array(
            'label' => 'Preis',
            'hint' => '',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. Abschlag mit vorgesetztem Minus-Zeichen.'
        ),
        'price.addkind' => array(
            'label' => '',
            'hint' => '',
        ),
        'price.factor' => array(
            'label' => '',
            'hint' => '',
        ),
        'price.signal' => array(
            'label' => 'Nachkommastelle',
            'hint' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu DaWanda als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
                <strong>Beispiel:</strong> <br />
                Wert im Textfeld: 99 <br />
                Preis-Ursprung: 5.58 <br />
                Finales Ergebnis: 5.99 <br /><br />
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br/>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br/>
                Das Eingabe-Format ist eine ganzstellige Zahl mit max. 2 Ziffern.
            '
        ),
        'priceoptions' => array(
            'label' => 'Preisoptionen',
            'help' => '{#i18n:configform_price_field_priceoptions_help#}',
            'hint' => '',
        ),
        'price.group' => array(
            'label' => '',
            'hint' => '',
        ),
        'price.usespecialoffer' => array(
            'label' => 'auch Sonderpreise verwenden',
            'hint' => '',
            
        ),
        'exchangerate_update' => array(
            'label' => 'Wechselkurs',
            'hint' => 'Wechselkurs automatisch aktualisieren',
            'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
            'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
        ),
    ),
), false);

MLI18n::gi()->add('dawanda_config_sync', array(
    'legend' => array(
        'sync' => 'Synchronisation des Inventars',
    ),
    'field' => array(
        'stocksync.tomarketplace' => array(
            'label' => 'Lagerveränderung Shop',
            'hint' => '',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen DaWanda-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br>
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
            'label' => 'Lagerveränderung DaWanda',
            'hint' => '',
            'help' => '
                Wenn z. B. bei DaWanda ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br /><br />
                <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!
            ',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'hint' => '',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen Shop-Preis auf Ihren DaWanda-Preis an.<br>
                    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
                    eine Warenwirtschaft nur in der Datenbank erfolgten.
                    <br><br>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" werden ber&uuml;cksichtigt.
                </p>
            ',
        ),
    ),
), false);

MLI18n::gi()->add('dawanda_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu DaWanda',
    ),
    'field' => array(
        'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'hint' => '',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>
                        Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden den aktuellen Versendet-Status zu DaWanda.<br/>
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
        'orderstatus.shipped' => array(
            'label' => 'Versand bestätigen mit',
            'hint' => '',
            'help' => 'Setzen Sie hier den Shop-Status, der auf DaWanda automatisch den Status "Versand bestätigen" setzen soll.',
        ),
        /*'orderstatus.canceled' => array(
            'label' => 'Bestellung stornieren mit',
            'hint' => '',
            'help' => '
                Setzen Sie hier den Shop-Status, der auf  DaWanda automatisch den Status "Bestellung stornieren" setzen soll. <br/><br/>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese Funktion storniert
                und dem K&auml;ufer gutgeschrieben.
            ',
        ),*/        
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'hint' => '',
            'help' => '{#i18n:form_config_orderimport_shop_help#}',
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
        /*//{search: 1427198983}
        'mwst.shipping' => array(
            'label' => 'MwSt. Versandkosten',
            'hint' => 'Steuersatz für Versandkosten in %.',
            'help' => '
                DaWanda &uuml;bermittelt nicht den Steuersatz der Versandkosten, sondern nur die Brutto-Preise. Daher muss der Steuersatz zur korrekten Berechnung der Mehrwertsteuer f&uuml;r die Versandkosten hier angegeben werden. Falls Sie mehrwertsteuerbefreit sind, tragen Sie in das Feld 0 ein.
            ',
        ),
        //*/
        'importactive' => array(
            'label' => 'Import aktivieren',
            'hint' => '',
            'help' => '
                Sollen Bestellungen aus den Marktplatz importiert werden? <br/><br/>Wenn die Funktion aktiviert ist, werden Bestellungen voreingestellt st&uuml;ndlich
                importiert.<br><br>
                Sie k&ouml;nnen die Zeiten der automatischen Bestellimporte selbst
				unter<br> 
				"magnalister Admin" &rarr; "Globale Konfiguration" &rarr; "Bestellabrufe" bestimmen.<br><br>
				Einen manuellen Import k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
				Zus&auml;tzlich k&ouml;nnen Sie den Bestellimport (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link
    			zu Ihrem Shop aufrufen: <br>
    			<i>{#setting:sImportOrdersUrl#}</i><br><br>
    			Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.						   
				'
        ),
        'import' => array(
            'label' => '',
            'hint' => '',
        ),
        'preimport.start' => array(
            'label' => 'erstmalig ab Zeitpunkt',
            'hint' => 'Startzeitpunkt',
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit möglich ist, da die Daten bei DaWanda höchstens einige Wochen lang vorliegen.',
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
                Der Status, den eine von DaWanda neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'order.importonlypaid'=> array(
            'label' => 'Nur bezahlte Bestellungen importieren',
            'hint' => '',
        ),
        'customersync' => array(
            'label' => 'Wiederkehrende Kunden',
            'hint' => '',
            'help' => '
                DaWanda erzeugt mit jeder Bestellung eine neue K&auml;ufer E-Mail Adresse (Weiterleitung) über die pro Bestellung kommuniziert werden kann.<br />
                <br />
                W&auml;hlen Sie im DropDown aus, ob die E-Mail Adresse und sonstige Stammdaten für wiederkehrende Kunden aktualisiert und damit &uuml;berschrieben werden sollen, oder ob ein komplett neuer Kundendatensatz angelegt werden soll.
            ',
            'values' => array(
                1 => 'Kundendaten aktualisieren',
                0 => 'Kundendaten neu anlegen',
            )
        )
    ),
), false);
