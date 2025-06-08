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

MLI18n::gi()->ayn24_config_account_title = 'Zugangsdaten';
MLI18n::gi()->ayn24_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->ayn24_config_account_price = 'Preisberechnung';
MLI18n::gi()->ayn24_config_account_sync = 'Synchronisation';
MLI18n::gi()->ayn24_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->ayn24_config_account_emailtemplate = 'Promotion-E-Mail Template';
MLI18n::gi()->ayn24_config_account_emailtemplate_sender = 'Beispiel-Shop';
MLI18n::gi()->ayn24_config_account_emailtemplate_sender_email = 'beispiel@onlineshop.de';
MLI18n::gi()->ayn24_config_account_emailtemplate_subject = 'Ihre Bestellung bei #SHOPURL#';
MLI18n::gi()->ayn24_config_account_emailtemplate_content = '
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

MLI18n::gi()->meinpaket_preimport_start_help = 'Startzeitpunkt, ab dem die Bestellungen erstmalig '
        . 'importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit möglich ist, '
        . 'da die Daten bei Ayn24 höchstens einige Wochen lang vorliegen.';

MLI18n::gi()->add('ayn24_config_account', array(
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
            'label' => 'Benutzername E-Mail-Adresse',
        ),
        'password' => array(
            'label' => 'Passwort',
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'shipping' => 'Versandoptionen',
        'checkin' => 'Artikel hochladen: Voreinstellungen'
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
            'valuehint' => 'Kategorien dieses Shops als eigene Ayn24-Kategorien verwenden'
        ),
        'shippingcost' => array (
            'label' => 'Versandkosten',
            'help' => 'Spezifische Versandkosten f&uuml;r Produkt.',
        ),
		'shippingcostfixed' => array (
            'label' => 'Fixe Versandkosten',
            'valuehint' => 'Versandkosten fixiert',
            'help' => 'Angabe, ob die Versandkosten f&uuml;r Produkt immer voll berechnet werden sollen.<br><br>'
                . 'Ben&ouml;tigt eine der folgenden Lieferarten:<ul><li>Sperrgut</li><li>Speditionsware</li></ul>',
        ),
		'shippingtype' => array(
            'label' => 'Lieferart',
        ),
        'checkin.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
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
                <strong>Beispiel:</strong> Wert auf "<i>2</i>" setzen. Ergibt &#8594; Shoplager: 10 &#8594; Ayn24-Lager: 8<br>
                <br>
                <strong>Hinweis:</strong>Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabh&auml;ngig der verwendeten Lagermengen<br>
                auch auf dem Marktplatz als Lager "<i>0</i>" behandeln wollen, gehen Sie bitte wie folgt vor:<br>
                <ul>
                <li>"<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" auf "<i>automatische Synchronisation per CronJob" einstellen</i></li>
                <li>"<i>Globale Konfiguration" > "<i>Produktstatus</i>" > "<i>Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</i></li>
                </ul>',
        ),
        'checkin.skipean' => array(
            'label' => 'EAN &uuml;bermitteln',
            'valuehint' => '&uuml;bermitteln',
            'help' => 'Wenn die Checkbox aktiviert ist, wird eine am Artikel hinterlegte EAN zu Ayn24 '
                . '&uuml;bertragen.<br><br> Bitte beachten Sie, dass Ayn24 dann versucht die EAN an '
                . 'bestehende Artikel zu matchen. Dies kann dazu f&uuml;hren, dass bei abweichenden Informationen der '
                . 'Artikel von Ayn24 zur&uuml;ckgewiesen wird. Die EAN ist KEIN Pflichtfeld bei Ayn24.',
        ),
        'checkin.leadtimetoship' => array(
            'label' => 'Bearbeitungszeit in Tagen',
            'help' => 'Gibt den Zeitraum (in Tagen) zwischen dem Auftragseingang f&uuml;r einen Artikel und dem Versand '
                . 'des Artikels an. Sofern Sie hier keinen Wert angeben, bel&auml;uft sich die Lieferzeit '
                . 'standardm&auml;&szlig;ig auf 1-2 Werktage. Verwenden Sie dieses Feld, wenn die Lieferzeit '
                . 'f&uuml;r einen Artikel mehr als zwei Werktage betr&auml;gt.',
        ),
        'checkin.manufacturerfallback' => array(
            'label' => 'Alternativ-Hersteller',
            'help' => 'Falls ein Produkt keinen Hersteller hinterlegt hat, wird der hier angegebene Hersteller verwendet.'
        ),
        'checkin.shortdesc' => array(
            'label' => 'Kurzbeschreibung',
            'help' => 'Die Kurzbeschreibung ist ein Pflichfeld bei Ayn24. Standardm&auml;&szlig;ig wird hier '
                . "die Langbeschreibung Ihres Shops verwendet.Sie k&ouml;nnen jedoch auch die "
                . 'Kurzbeschreibung aus einem anderen Datenfeld verwenden, wenn Sie w&uuml;nschen.',
        ),
        'checkin.longdesc' => array(
            'label' => 'Langbeschreibung',
            'help' => 'Die Langbeschreibung ist kein Pflichfeld bei Ayn24. Standardm&auml;&szlig;ig wird diese '
                . "nicht &uuml;bertragen.Sie k&ouml;nnen jedoch auch die Langbeschreibung aus einem anderen "
                . 'Datenfeld verwenden, und diese an Ayn24 &uuml;bertragen.<br>',
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_config_price', array(
    'legend' => array(
        'price' => 'Preisberechnung',
    ),
    'field' => array(
        'price' => array(
            'label' => 'Preis',
            'help' => 'Geben Sie einen prozentualen oder fest definierten Preis Auf- oder Abschlag an. '
                . 'Abschlag mit vorgesetztem Minus-Zeichen.'
        ),
        'price.addkind' => array(
            'label' => '',
        ),
        'price.factor' => array(
            'label' => '',
        ),
        'price.signal' => array(
            'label' => 'Nachkommastelle',
            'help' => '
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu Ayn24 als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br><br>
                <strong>Beispiel:</strong> <br>
                Wert im Textfeld: 99 <br>
                Preis-Ursprung: 5.58 <br>
                Finales Ergebnis: 5.99 <br><br>
                Die Funktion hilft insbesondere bei prozentualen Preis-Auf-/Abschl&auml;gen.<br>
                Lassen Sie das Feld leer, wenn Sie keine Nachkommastelle &uuml;bermitteln wollen.<br>
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
            'label' => '',
            'valuehint' => 'auch Sonderpreise verwenden',
        ),
        'exchangerate_update' => array(
            'label' => 'Wechselkurs',
            'valuehint' => 'Wechselkurs automatisch aktualisieren',
            'help' => '{#i18n:form_config_orderimport_exchangerate_update_help#}',
            'alert' => '{#i18n:form_config_orderimport_exchangerate_update_alert#}',
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu Ayn24',
    ),
    'field' => array(
        'importactive' => array(
            'label' => 'Import aktivieren',
            'help' => '
                Sollen Bestellungen aus den Marktplatz importiert werden? <br><br>
                Wenn die Funktion aktiviert ist, werden Bestellungen voreingestellt st&uuml;ndlich importiert.<br><br>
                Sie k&ouml;nnen die Zeiten der automatischen Bestellimporte selbst unter<br> 
				"magnalister Admin" &rarr; "Globale Konfiguration" &rarr; "Bestellabrufe" bestimmen.<br><br>
				Einen manuellen Import k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton 
                in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
				Zus&auml;tzlich k&ouml;nnen Sie den Bestellimport (ab Tarif Flat - maximal viertelst&uuml;ndlich) 
                auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
    			<i>{#setting:sImportOrdersUrl#}</i><br><br>
    			Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als 
                viertelst&uuml;ndlich laufen, werden geblockt.'
        ),
        'import' => array(
            'label' => '',
        ),
        'preimport.start' => array(
            'label' => 'erstmalig ab Zeitpunkt',
            'hint' => 'Startzeitpunkt',
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, 
                dass dies nicht beliebig weit in die Vergangenheit möglich ist, da die Daten bei Ayn24 höchstens
                einige Wochen lang vorliegen.',
        ),
        'customergroup' => array(
            'label' => 'Kundengruppe',
            'help' => 'Kundengruppe, zu der Kunden bei neuen Bestellungen zugeordnet werden sollen.',
        ),
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'hint' => '',
            'help' => '{#i18n:form_config_orderimport_shop_help#}',
        ),
        'orderstatus.open' => array(
            'label' => 'Bestellstatus im Shop',
            'help' => '
                Der Status, den eine von Ayn24 neu eingegangene Bestellung im Shop automatisch bekommen soll.<br>
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf 
                "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'orderimport.shippingmethod' => array(
            'label' => 'Versandart der Bestellungen',
            'help' => 'Versandart, die allen Ayn24-Bestellungen zugeordnet wird. Standard: "marketplace".<br><br>'
                . 'Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r '
                . 'die nachtr&amul;gliche Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
        ),
        'orderimport.paymentmethod' => array(
            'label' => 'Zahlart der Bestellungen',
            'help' => 'Zahlart, die allen Ayn24-Bestellungen zugeordnet wird. Standard: "marketplace".<br><br>'
                . 'Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r '
                . 'die nachtr&amul;gliche Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
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
            'help' => 'Ayn24 &uuml;bermittelt nicht den Steuersatz der Versandkosten, sondern nur die
                Brutto-Preise. Daher muss der Steuersatz zur korrekten Berechnung der Mehrwertsteuer f&uuml;r die 
                Versandkosten hier angegeben werden. Falls Sie mehrwertsteuerbefreit sind, tragen Sie in das Feld 0 ein.',
        ),
        //*/
        'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>
                        Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden 
                        den aktuellen Versendet-Status zu Ayn24.<br>
                        Dabei werden die Status-Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn 
                        die &Auml;nderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
                        Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie die Bestellung direkt im 
                        Web-Shop bearbeiten, dort  den gew&uuml;nschten Status setzen, und dann auf "Aktualisieren" klicken.<br>
                        Sie k&ouml;nnen auch den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister 
                        anklicken (links von der Ameise), um den Status sofort zu &uuml;bergeben.<br><br>
                        Zus&auml;tzlich k&ouml;nnen Sie den Bestellstatus-Abgleich (ab Tarif Flat - maximal 
                        viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden 
                        Link zu Ihrem Shop aufrufen: <br><br>
                        <i>{#setting:sSyncOrderStatusUrl#}</i><br><br>
                        Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger 
                        als viertelst&uuml;ndlich laufen, werden geblockt.
                    </dd>
                </dl>
            ',
        ),
        'orderstatus.shipped' => array(
            'label' => 'Versand best&auml;tigen mit',
            'help' => 'Setzen Sie hier den Shop-Status, der auf Ayn24 automatisch den Status "Versand best&auml;tigen" setzen soll.',
        ),
        'orderstatus.canceled.customerrequest' => array(
            'label' => 'Stornieren (auf Kundenwunsch) mit',
            'help' => 'Ayn24 verlangt die &Uuml;bermittlung eines Grundes bei einer Stornierung.<br><br>
                Setzen Sie hier den Shop-Status, der auf Ayn24.pl dem Status "Stornieren auf Kundenwunsch (Customer Request)"
                setzen soll.<br><br>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese 
                Funktion storniert und dem K&auml;ufer gutgeschrieben.',
        ),
        'orderstatus.canceled.outofstock' => array(
            'label' => 'Stornieren (Nicht auf Lager) mit',
            'help' => 'Meinpaket verlangt die &Uuml;bermittlung eines Grundes bei einer Stornierung.<br><br>
                Setzen Sie hier den Shop-Status, der auf Ayn24.pl automatisch den Status "Stornieren, da Ware
                nicht auf Lager (Out Of Stock)" setzen soll. <br><br>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese 
                Funktion storniert und dem K&auml;ufer gutgeschrieben.',
        ),
        'orderstatus.canceled.damagedgoods' => array(
            'label' => 'Stornieren (Besch&auml;digte Ware) mit',
            'help' => 'Ayn24 verlangt die &Uuml;bermittlung eines Grundes bei einer Stornierung.<br><br>
                Setzen Sie hier den Shop-Status, der auf Ayn24.pl automatisch den Status "Stornieren, da Ware
                besch&auml;igt (Damaged Goods)" setzen soll. <br><br>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese 
                Funktion storniert und dem K&auml;ufer gutgeschrieben.',
        ),
        'orderstatus.canceled.dealerrequest' => array(
            'label' => 'Stornieren (durch H&auml;ndler) mit',
            'help' => 'Ayn24 verlangt die &Uuml;bermittlung eines Grundes bei einer Stornierung.<br><br>
                Setzen Sie hier den Shop-Status, der auf Ayn24.pl automatisch den Status "Storniert durch
                H&auml;ndler (Dealer Request)" setzen soll.<br><br>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese 
                Funktion storniert und dem K&auml;ufer gutgeschrieben.',
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_config_sync', array(
    'legend' => array(
        'sync' => 'Synchronisation des Inventars',
    ),
    'field' => array(
        'stocksync.tomarketplace' => array(
            'label' => 'Lagerver&auml;nderung Shop',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                        den aktuellen Ayn24-Lagerbestand an der Shop-Lagerbestand an
                        (je nach Konfiguration ggf. mit Abzug).<br>
                        Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die 
                        &Auml;nderungen durch z.B. eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
                        Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden 
                        Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
                        Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) 
                        auch durch einen eigenen CronJob ansto&szlig;en, indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
                        <i>{#setting:sSyncInventoryUrl#}</i><br>
                        Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als 
                        viertelst&uuml;ndlich laufen, werden geblockt.
                    </dd>
                    <dt>Bestellung / Artikel bearbeiten setzt Ayn24-Lagerbestand gleich Shop-Lagerbestand</dt>
                    <dd>Wenn der Lagerbestand im Shop durch eine Bestellung oder durch das Bearbeiten des Artikels 
                        ge&auml;ndert wird, wird der dann g&uuml;ltige aktuelle Lagerbestand vom Shop zu Ayn24
                        &uuml;bertragen.<br>
                        &Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b>
                        erfasst und &uuml;bermittelt!
                    </dd>
                    <dt>Bestellung / Artikel bearbeiten &auml;ndert Ayn24-Lagerbestand (Differenz)</dt>
                    <dd>Wenn z. B. im Shop ein Artikel 2 mal gekauft wurde, wird der Lagerbestand bei 
                        Ayn24 um 2 reduziert.<br>
                        Wenn die Artikelanzahl unter "Artikel bearbeiten" im Shop ge&auml;ndert wird, wird die Differenz 
                        zum vorigen Stand aufaddiert bzw. abgezogen.<br>
                        &Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b>
                        erfasst und &uuml;bermittelt!
                    </dd>
                </dl>
                <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Artikel hochladen: Voreinstellungen" 
                    &rarr; "St&uuml;ckzahl Lagerbestand" werden f&uuml;r die ersten beiden Optionen ber&uuml;cksichtigt.
            ',
            'values' => array(
                'auto' => '{#i18n:configform_sync_value_auto#}',
                /*
                'auto_fast' => 'Schnellere automatische Synchronisation cronjob (auf 15 Minuten)',
                */
                'abs' => 'Bestellung / Artikel bearbeiten setzt Ayn24-Lagerbestand gleich Shop-Lagerbestand',
                'rel' => 'Bestellung / Artikel bearbeiten &auml;ndert Ayn24-Lagerbestand (Differenz)',
                'no' => '{#i18n:configform_sync_value_no#}',
            ),
        ),
        'stocksync.frommarketplace' => array(
            'label' => 'Lagerver&auml;nderung Ayn24',
            'help' => '
                Wenn z. B. bei Ayn24 ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br><br>
                <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!
            ',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'help' => '
                <p>
                    Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                    den aktuellen Shop-Preis auf Ihren Ayn24-Preis an.<br>
                    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
                    eine Warenwirtschaft nur in der Datenbank erfolgten.
                    <br><br>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" werden ber&uuml;cksichtigt.
                </p>
            ',
            'values' => array(
                'auto' => '{#i18n:configform_sync_value_auto#}',
                'edit' => 'Artikel bearbeiten im Shop &auml;ndert Preis auf Ayn24',
                'no' => '{#i18n:configform_sync_value_no#}',
            ),
        ),
    ),
), false);

MLI18n::gi()->add('ayn24_config_emailtemplate', array(
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
