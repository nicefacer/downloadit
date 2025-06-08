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

MLI18n::gi()->tradoria_config_account_title = 'Zugangsdaten';
MLI18n::gi()->tradoria_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->tradoria_config_account_price = 'Preisberechnung';
MLI18n::gi()->tradoria_config_account_sync = 'Synchronisation';
MLI18n::gi()->tradoria_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->tradoria_config_account_emailtemplate = 'Promotion-E-Mail Template';
MLI18n::gi()->tradoria_config_account_emailtemplate_sender = 'Beispiel-Shop';
MLI18n::gi()->tradoria_config_account_emailtemplate_sender_email = 'beispiel@onlineshop.de';
MLI18n::gi()->tradoria_config_account_emailtemplate_subject = 'Ihre Bestellung bei #SHOPURL#';
MLI18n::gi()->tradoria_config_account_emailtemplate_content = '
    <style>
        <!--body { font: 12px sans-serif; }
        table.ordersummary { width: 100%; border: 1px solid #e8e8e8; }
        table.ordersummary td { padding: 3px 5px; }
        table.ordersummary thead td { background: #cfcfcf; color: #000; font-weight: bold; text-align: center; }
        table.ordersummary thead td.name { text-align: left; }
        table.ordersummary tbody tr.even td { background: #e8e8e8; color: #000; }
        table.ordersummary tbody tr.odd td { background: #f8f8f8; color: #000; }
        table.ordersummary td.price, table.ordersummary td.fprice { text-align: right; white-space: nowrap; }
        table.ordersummary tbody td.qty { text-align: center; }-->
    </style>
    <p>Hallo #FIRSTNAME# #LASTNAME#,</p>
    <p>vielen Dank f&uuml;r Ihre Bestellung! Sie haben &uuml;ber #MARKETPLACE# in unserem Shop folgendes bestellt:</p>
    #ORDERSUMMARY#
    <p>Zuz&uuml;glich etwaiger Versandkosten.</p>
    <p>Weitere interessante Angebote finden Sie in unserem Shop unter <strong>#SHOPURL#</strong>.</p>
    <p>&nbsp;</p>
    <p>Mit freundlichen Gr&uuml;&szlig;en,</p>
    <p>Ihr Online-Shop-Team</p>
';

MLI18n::gi()->add('tradoria_config_account', array(
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
            'label' => 'Kundennummer',
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

MLI18n::gi()->add('tradoria_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'upload' => 'Artikel hochladen: Voreinstellungen'
    ),
    'field' => array(
        'prepare.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
		'lang' => array(
            'label' => 'Artikelbeschreibung',
        ),
        'catmatch.mpshopcats' => array(
            'label' => 'Eigene Kategorien',
            'valuehint' => 'Kategorien dieses Shops als eigene Rakuten-Kategorien verwenden'
        ),
        'checkin.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel übernehmen',
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
                <strong>Beispiel:</strong> Wert auf "<i>2</i>" setzen. Ergibt &#8594; Shoplager: 10 &#8594; MeinPaket-Lager: 8<br>
                <br>
                <strong>Hinweis:</strong>Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabh&auml;ngig der verwendeten Lagermengen<br>
                auch auf dem Marktplatz als Lager "<i>0</i>" behandeln wollen, gehen Sie bitte wie folgt vor:<br>
                <ul>
                <li>"<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auf "<i>automatische Synchronisation per CronJob" einstellen</i></li>
                <li>"<i>Globale Konfiguration" > "<i>Produktstatus</i>" > "<i>Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</i></li>
                </ul>',
        ),
		'checkin.leadtimetoship' => array(
            'label' => 'Bearbeitungszeit in Tagen',
            'help' => 'Gibt den Zeitraum (in Tagen) zwischen dem Auftragseingang f&uuml;r einen Artikel und dem Versand '
                . 'des Artikels an. Sofern Sie hier keinen Wert angeben, bel&auml;uft sich die Lieferzeit '
                . 'standardm&auml;&szlig;ig auf 1-2 Werktage. Verwenden Sie dieses Feld, wenn die Lieferzeit '
                . 'f&uuml;r einen Artikel mehr als zwei Werktage betr&auml;gt.',
			'values' => array(
                '0' => 'Sofort lieferbar (Lieferzeit 1-4 Werktage)',
				'3' => 'versandfertig in 3 Werktagen (Lieferzeit 4-6 Werktage)',
				'5' => 'versandfertig in 5 Werktagen (Lieferzeit 6-8 Werktage)',
				'7' => 'versandfertig in 7 Werktagen (Lieferzeit 8-10 Werktage)',
				'10' => 'versandfertig in 10 Werktagen (Lieferzeit 10-15 Werktage)',
				'15' => 'versandfertig in 15 Werktagen (Lieferzeit 15-20 Werktage)',
				'20' => 'versandfertig in 20 Werktagen (Lieferzeit 20-30 Werktage)',
				'30' => 'versandfertig in 30 Werktagen (Lieferzeit 30-40 Werktage)',
				'40' => 'versandfertig in 40 Werktagen (Lieferzeit 40-50 Werktage)',
				'50' => 'versandfertig in 50 Werktagen (Lieferzeit 50-60 Werktage)',
				'60' => 'versandfertig in 60 Werktagen (Lieferzeit länger als 3 Monate)',
            ),
        ),
        'checkin.manufacturerfallback' => array(
            'label' => 'Alternativ-Hersteller',
            'help' => 'Falls ein Produkt keinen Hersteller hinterlegt hat, wird der hier angegebene Hersteller verwendet.'
        ),
        'checkin.taxmatching' => array(
            'label' => 'Steuerklassen<br> Kategorie-Matching',
            'help' => 'Shop-eigene Steuerklassen denen von Rakuten zuordnen.',
            'matching' => array(
                'titlesrc' => '',
                'titledst' => '',
                'labelsdst' => array(
                    'Standard' => 'Standard',
                    'Reduced'  => 'Erm&auml;&szlig;igt',
                    'Free'     => 'Steuerfrei',
                ),
            )
        ),
        'checkin.shippinggroup' => array(
            'label' => 'Versandkosten-Gruppe',
            'help' => 'Die Versandkosten-Gruppen können bei Rakuten unter
			<a href="https://merchants.rakuten.de/office/shipping/index" target="_blank" class="ml-js-noBlockUi">
			Verwaltung &gt; Versand</a> eingestellt werden (Standard: 1).'
        ),
        'imagesize' => array(
            'label' => 'Bildgr&ouml;&szlig;e',
            'help' => '<p>Geben Sie hier die Pixel-Breite an, die Ihr Bild auf dem Marktplatz haben soll.
Die H&ouml;he wird automatisch dem urspr&uuml;nglichen Seitenverh&auml;ltnis nach angepasst.</p>
<p>
Die Quelldateien werden aus dem Bildordner {#setting:sSourceImagePath#} verarbeitet und mit der hier gew&auml;hlten Pixelbreite im Ordner {#setting:sImagePath#}  f&uuml;r die &Uuml;bermittlung zum Marktplatz abgelegt.</p>',
            'hint' => 'Gespeichert unter: {#setting:sImagePath#}'
        ),
    ),
), false);

MLI18n::gi()->add('tradoria_config_price', array(
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
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu Rakuten als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
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
        ),
        'price.group' => array(
            'label' => '',
        ),
        'price.usespecialoffer' => array(
            'label' => 'auch Sonderpreise verwenden',
            'hint' => '',
        ),
        'exchangerate_update' => array(
            'label' => 'Wechselkurs',
            'valuehint' => 'Wechselkurs automatisch aktualisieren',
            'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
            'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
        ),
    ),
), false);

MLI18n::gi()->add('tradoria_config_sync', array(
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
                    den aktuellen Rakuten-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br>
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
            'label' => 'Lagerveränderung Rakuten',
            'hint' => '',
            'help' => '
                Wenn z. B. bei Rakuten ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br /><br />
                <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!
            ',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'hint' => '',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen Shop-Preis auf Ihren Rakuten-Preis an.<br>
                    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
                    eine Warenwirtschaft nur in der Datenbank erfolgten.
                    <br><br>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" werden ber&uuml;cksichtigt.
                </p>
            ',
        ),
    ),
), false);

MLI18n::gi()->add('tradoria_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu Rakuten',
    ),
    'field' => array(
        'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'hint' => '',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>
                        Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden den aktuellen Versendet-Status zu Rakuten.<br/>
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
            'help' => 'Setzen Sie hier den Shop-Status, der auf Rakuten automatisch den Status "Versand bestätigen" setzen soll.',
        ),        
        'orderstatus.carrier.default'=>array(
            'label' => '&nbsp;&nbsp;&nbsp;&nbsp;Spediteur',
            'help' => 'Vorausgew&auml;hlter Spediteur beim Best&auml;tigen des Versandes nach Rakuten',
        ),
        'orderstatus.canceled' => array(
            'label' => 'Bestellung stornieren mit',
            'hint' => '',
            'help' => '
                Setzen Sie hier den Shop-Status, der auf  Rakuten automatisch den Status "Bestellung stornieren" setzen soll. <br/><br/>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese Funktion storniert
                und dem K&auml;ufer gutgeschrieben.
            ',
        ),
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
                Rakuten &uuml;bermittelt nicht den Steuersatz der Versandkosten, sondern nur die Brutto-Preise. Daher muss der Steuersatz zur korrekten Berechnung der Mehrwertsteuer f&uuml;r die Versandkosten hier angegeben werden. Falls Sie mehrwertsteuerbefreit sind, tragen Sie in das Feld 0 ein.
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
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit möglich ist, da die Daten bei Rakuten höchstens einige Wochen lang vorliegen.',
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
                Der Status, den eine von Rakuten neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'order.importonlypaid'=> array(
            'label' => 'Nur bezahlte Bestellungen importieren',
            'hint' => '',
        )
    ),
), false);

MLI18n::gi()->add('tradoria_config_emailtemplate', array(
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
                    <dd>Password des K&auml;ufers zum Einloggen in Ihren Shop. Nur bei Kunden, die dabei 
                        automatisch angelegt werden, sonst wird der Platzhalter durch \'(wie bekannt)\' ersetzt.</dd>
                    <dt>#ORDERSUMMARY#</dt>
                    <dd>Zusammenfassung der gekauften Artikel. Sollte extra in einer Zeile stehen.<br>
                        <i>Kann nicht im Betreff verwendet werden!</i>
                    </dd>
                    <dt>#MARKETPLACE#</dt>
                    <dd>Name dieses Marketplaces</dd>
                    <dt>#SHOPURL#</dt>
                    <dd>URL zu Ihrem Shop</dd>
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