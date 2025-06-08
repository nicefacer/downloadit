<?php
MLI18n::gi()->amazon_config_general_mwstoken_help = '
Amazon ben&ouml;tigt eine Authentifizierung zum &uuml;bermitteln von Daten &uuml;ber die Schnittstelle. Bitte tragen Sie unter "H&auml;ndler-ID", "Marktplatz-ID" und "MWS Token“ die jeweiligen Schl&uuml;ssel ein. Sie k&ouml;nnen diese Schlüssel auf dem jeweiligen Amazon Marketplace beantragen, auf dem Sie einstellen wollen.
<ul><li><a href="https://developer.amazonservices.de/index.html" title="Amazon MWS" target="_blank">Amazon Deutschland</a></li>
        <li><a href="https://developer.amazonservices.co.uk/index.html" title="Amazon MWS" target="_blank">Amazon UK</a></li>
        <li><a href="https://developer.amazonservices.fr/index.html" title="Amazon MWS" target="_blank">Amazon Frankreich</a></li>
        <li><a href="https://developer.amazonservices.it/index.html" title="Amazon MWS" target="_blank">Amazon Italien</a></li>
        <li><a href="https://developer.amazonservices.es/index.html" title="Amazon MWS" target="_blank">Amazon Spanien</a></li>
        <li><a href="https://developer.amazonservices.com/index.html" title="Amazon MWS" target="_blank">Amazon USA</a></li>
    </ul>
<strong>Schritt 1:</strong>
<br /> Bitte klicke Sie oben auf die gew&uuml;nschte Amazon Anbindung und folgen dem Amazon Wizard. 
Tragen Sie dabei folgende Daten ein, sobald sie von Amazon erfragt werden:<br />
<br />
<strong>F&uuml;r europ&auml;ische Amazon Marktpl&auml;tze:</strong><br />
Name of Application: <strong>magnalister</strong> <br />
Kontonummer des Anwendungsentwicklers: <strong>4141-0616-7444</strong><br /><br />

<strong>F&uuml;r Amazon USA:</strong><br />
Name der Anwendung: <strong>magnalister-us </strong><br />
Kontonummer des Anwendungsentwicklers: <strong>8260-4311-6738</strong> <br /><br />

<strong>Schritt 2:</strong><br />
Kopieren Sie, „Merchant ID“, „Marketplace ID“ and MWS Token von Amazon und kopieren sie in die hier vorgesehenen Felder.<br /><br />

F&uuml;r eine genaue Anweisung folgen Sie diesem Video-Tutorial:<br /><br />
  <iframe width="472" height="289" src="https://www.youtube-nocookie.com/embed/qjpRWre-Umo?rel=0&vq=hd720" frameborder="0" allowfullscreen></iframe>';
MLI18n::gi()->amazon_config_general_autosync = 'Automatische Synchronisierung per CronJob (empfohlen)';
MLI18n::gi()->amazon_config_general_nosync = 'keine Synchronisierung';
MLI18n::gi()->amazon_config_account_title = 'Zugangsdaten';
MLI18n::gi()->amazon_config_account_prepare = 'Artikelvorbereitung';
MLI18n::gi()->amazon_config_account_price = 'Preisberechnung';
MLI18n::gi()->amazon_configform_orderstatus_sync_values = array(
                        'auto' => '{#i18n:amazon_config_general_autosync#}',
                        'trigger' => 'Synchronisierung &uuml;ber Web-Shop',
                        'no' => '{#i18n:amazon_config_general_nosync#}',
                    );
MLI18n::gi()->amazon_configform_sync_values = array(
                        'auto' => '{#i18n:amazon_config_general_autosync#}',
    /*
                        'auto_fast' => 'Schnellere automatische Synchronisation cronjob (auf 15 Minuten)',
    */
                        'no' => '{#i18n:amazon_config_general_nosync#}',
                    );
MLI18n::gi()->amazon_configform_stocksync_values = array(
                        'rel' => 'Bestellung (keine FBA-Bestellung) reduziert Shop-Lagerbestand (empfohlen)',
                        'fba' => 'Bestellung (auch FBA-Bestellung) reduziert Shop-Lagerbestand',
                        'no' => '{#i18n:amazon_config_general_nosync#}',
                    );
MLI18n::gi()->amazon_configform_pricesync_values = array(
                        'auto' => '{#i18n:amazon_config_general_autosync#}',
                        'edit' => 'Artikel bearbeiten setzt Amazon-Preis gleich den Shop-Preis',
                        'no' => '{#i18n:amazon_config_general_nosync#}',
                    );
MLI18n::gi()->amazon_configform_orderimport_payment_values = array(    
    'textfield' => array(
        'title' => 'Aus Textfeld',
        'textoption' => true
    ),
    'Amazon' => array(
        'title' => 'Amazon',
    ),
);

MLI18n::gi()->amazon_configform_orderimport_shipping_values = array(
    'textfield' => array(
        'title' => 'Aus Textfeld',
        'textoption' => true
    ),
);
MLI18n::gi()->amazon_config_account_sync = 'Synchronisation';
MLI18n::gi()->amazon_config_account_orderimport = 'Bestellimport';
MLI18n::gi()->amazon_config_account_emailtemplate = 'Promotion-E-Mail Template';
MLI18n::gi()->amazon_config_account_shippinglabel = 'Versandentgelt';
MLI18n::gi()->amazon_config_account_emailtemplate_sender = 'Beispiel-Shop';
MLI18n::gi()->amazon_config_account_emailtemplate_sender_email = 'beispiel@onlineshop.de';
MLI18n::gi()->amazon_config_account_emailtemplate_subject = 'Ihre Bestellung bei #SHOPURL#';
MLI18n::gi()->amazon_config_account_emailtemplate_content = '
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

MLI18n::gi()->add('amazon_config_account', array(
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
            'label' => 'Seller Central E-Mail-Adresse',
            'hint' => '',
        ),
        'password' => array(
            'label' => 'Seller Central Kennwort',
            'help' => 'Tragen Sie hier Ihr aktuelles Amazon-Passwort ein, mit dem Sie sich auch auf Ihrem Seller-Central-Account einloggen.',
        ),
        'mwstoken' => array(
            'label' => 'MWS Token',
            'help' => '{#i18n:amazon_config_general_mwstoken_help#}',
				          /* Das Youtube Video muss mit folgenden Dimensionen eingefuegt werden: width="472" height="289" */
        ),
        'merchantid' => array(
            'label' => 'H&auml;ndler-ID',
            'help' => '{#i18n:amazon_config_general_mwstoken_help#}',
				          /* Das Youtube Video muss mit folgenden Dimensionen eingefuegt werden: width="472" height="289" */
        ),
        'marketplaceid' => array(
            'label' => 'Marktplatz-ID',
            'help' => '{#i18n:amazon_config_general_mwstoken_help#}',
				          /* Das Youtube Video muss mit folgenden Dimensionen eingefuegt werden: width="472" height="289" */
        ),
        'site' => array(
            'label' => 'Amazon Site',
        ),
    ),
), false);


MLI18n::gi()->add('amazon_config_prepare', array(
    'legend' => array(
        'prepare' => 'Artikelvorbereitung',
        'matchingvalues' => '<b>Standardwerte:</b> Hier k&ouml;nnen Sie einstellen, welche Werte 
		               standardm&auml;&szlig;ig bei der Vorbereitung mehrerer Artikel verwendet werden sollen.',
        'machingbehavior' => 'Matchingverhalten',
        'apply' => 'Neue Produkte erstellen',
        'shipping' => 'Versand',
        'upload' => 'Artikel hochladen: Voreinstellungen',
    ),
    'field' => array(
        'prepare.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
        'topten' => array(
            'label' => 'Kategorie-Schnellauswahl',
            'help' => 'Anzeigen der Kategorie-Schnellauswahl unter Produkte vorbereiten',
        ),
        'checkin.status' => array(
            'label' => 'Statusfilter',
            'valuehint' => 'nur aktive Artikel &uuml;bernehmen',
        ),
        'lang' => array(
            'label' => 'Artikelbeschreibung',
        ),
        'itemcondition' => array(
            'label' => 'Artikelzustand',
        ),
        'internationalshipping' => array(
            'label' => 'Versand',
        ),
        'multimatching' => array(
            'label' => 'Neu matchen',
            'valuehint' => 'Bereits gematchte Produkte beim Multi- und Automatching &uuml;berschreiben.',
            'help' => 'Sollten Sie diese Einstellung aktivieren, werden die bereits gematcheten Produkte durch das neue Matching &uuml;berschrieben.'
        ),
        'multimatching.itemsperpage' => array(
            'label' => 'Ergebnisse',
            'help' => 'Hier k&ouml;nnen Sie festlegen, wie viele Produkte pro Seite beim Multimatching angezeigt werden sollen. <br/>
					Je h&ouml;her die Anzahl, desto h&ouml;her auch die Ladezeit (bei 50 Ergebnissen ca. 30 Sekunden).',
            'hint' => 'pro Seite beim Multimatching',
        ),
        'prepare.manufacturerfallback' => array(
            'label' => 'Alternativ-Hersteller',
            'help' => 'Falls ein Produkt keinen Hersteller hinterlegt hat, wird der hier angegebene Hersteller verwendet.<br />
                        Unter „Globale Konfiguration“ > „Produkteigenschaften“ können Sie auch generell „Hersteller“ auf Ihre Attribute matchen.
                    ',
        ),
        'quantity' => array(
            'label' => 'St&uuml;ckzahl Lagerbestand',
            'help' => 'Geben Sie hier an, wie viel Lagermenge eines Artikels auf dem Marktplatz verf&uuml;gbar sein soll.<br/>
                        <br/>
                        Sie k&ouml;nnen die St&uuml;ckzahl direkt unter "Hochladen" einzeln ab&auml;ndern - in dem Fall ist es empfehlenswert,<br/>
                        die automatische Synchronisation unter "Synchronisation des Inventars" > "Lagerver&auml;nderung Shop" auszuschalten.<br/>
                        <br/>
                        Um &Uuml;berverk&auml;ufe zu vermeiden, k&ouml;nnen Sie den Wert<br/>
                        "Shop-Lagerbestand &uuml;bernehmen abzgl. Wert aus rechtem Feld" aktivieren.<br/>
                        <br/>
                        <strong>Beispiel:</strong> Wert auf "2" setzen. Ergibt &#8594; Shoplager: 10 &#8594; Amazon-Lager: 8<br/>
                        <br/>
                        <strong>Hinweis:</strong>Wenn Sie Artikel, die im Shop inaktiv gesetzt werden, unabh&auml;ngig der verwendeten Lagermengen<br/>
                        auch auf dem Marktplatz als Lager "0" behandeln wollen, gehen Sie bitte wie folgt vor:<br/>
                        <ul>
                        <li>Synchronisation des Inventars" > "Lagerver&auml;nderung Shop" auf "automatische Synchronisation per CronJob" einstellen</li>
                        <li>"Globale Konfiguration" > "Produktstatus" > "Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt" aktivieren</li>
                        </ul>',
        ),
        'leadtimetoship' => array(
            'label' => 'Lieferzeit in Tagen',
            'help' => 'Gibt den Zeitraum (in Tagen) zwischen dem Auftragseingang f&uuml;r einen Artikel und dem Versand des 
					Artikels an. Sofern Sie hier keinen Wert angeben, bel&auml;uft sich die Lieferzeit standardm&auml;&szlig;ig auf 1-2 Werktage.
					Verwenden Sie dieses Feld, wenn die Lieferzeit f&uuml;r einen Artikel mehr als zwei Werktage betr&auml;gt.',
        ),
        'checkin.skuasmfrpartno' => array(
            'label' => 'Herstellerartikelnummer',
            'help' => 'SKU wird als Herstellerartikelnummer &uuml;bertragen.',
            'valuehint' => 'SKU wird als Herstellerartikelnummer verwenden',
        ),
        'imagesize' => array(
            'label' => 'Bildgr&ouml;&szlig;e',
            'help' => '<p>Geben Sie hier die Pixel-Breite an, die Ihr Bild auf dem Marktplatz haben soll.
Die H&ouml;he wird automatisch dem urspr&uuml;nglichen Seitenverh&auml;ltnis nach angepasst.</p>
<p>
Die Quelldateien werden aus dem Bildordner {#setting:sSourceImagePath#} verarbeitet und mit der hier gew&auml;hlten Pixelbreite im Ordner {#setting:sImagePath#}  f&uuml;r die &Uuml;bermittlung zum Marktplatz abgelegt.</p>',
            'hint' => 'Gespeichert unter: {#setting:sImagePath#}'
        ),
    )
), false);

MLI18n::gi()->add('amazon_config_price', array(
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
                Dieses Textfeld wird beim &Uuml;bermitteln der Daten zu Amazon als Nachkommastelle an Ihrem Preis &uuml;bernommen.<br/><br/>
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


MLI18n::gi()->add('amazon_config_sync',  array(
    'legend' => array(
        'sync' => 'Synchronisation des Inventars',
    ),
    'field' => array(
        'stocksync.tomarketplace' => array(
            'label' => 'Lagerver&auml;nderung Shop',
            'hint' => '',
            'help' => '<dl>
            <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
            den aktuellen Amazon-Lagerbestand an der Shop-Lagerbestand an (je nach Konfiguration ggf. mit Abzug).<br>
            Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
            eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
            Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
            Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, 
            indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
            <i>{#setting:sSyncInventoryUrl#}</i><br>
            Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.
            </dd>
                            <dt>Bestellung / Artikel bearbeiten setzt Amazon-Lagerbestand gleich Shop-Lagerbestand</dt>
                                    <dd>Wenn der Lagerbestand im Shop durch eine Bestellung oder durch das Bearbeiten des Artikels ge&auml;ndert wird,
                                        wird der dann g&uuml;ltige aktuelle Lagerbestand vom Shop zu Amazon &uuml;bertragen.<br>
                                        &Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>
                            <dt>Bestellung / Artikel bearbeiten &auml;ndert Amazon-Lagerbestand (Differenz)</dt>
                                    <dd>Wenn z. B. im Shop ein Artikel 2 mal gekauft wurde, wird der Lagerbestand bei Amazon um 2 reduziert.<br />
                                            Wenn die Artikelanzahl unter "Artikel bearbeiten" im Shop ge&auml;ndert wird, wird die Differenz zum vorigen Stand aufaddiert bzw. abgezogen.<br>
                                        &Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>
                    </dl>
                    <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Einstellvorgang" &rarr; "St&uuml;ckzahl Lagerbestand" werden f&uuml;r die 
                            ersten beiden Optionen ber&uuml;cksichtigt.
            ',
        ),
        'stocksync.frommarketplace' => array(
            'label' => 'Lagerver&auml;nderung Amazon',
            'hint' => '',
            'help' => 'Wenn z. B. bei Amazon ein Artikel 3 mal gekauft wurde, wird der Lagerbestand im Shop um 3 reduziert.<br /><br />
				           <strong>Wichtig:</strong> Diese Funktion l&auml;uft nur, wenn Sie den Bestellimport aktiviert haben!',
        ),
        'inventorysync.price' => array(
            'label' => 'Artikelpreis',
            'hint' => '',
            'help' => '<dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                        <dd>Die Funktion "Automatische Synchronisierung" gleicht alle 4 Stunden (beginnt um 0:00 Uhr nachts)
                            den Amazon-Preis an den Shop-Preis an (mit ggf. Auf- oder Absch&auml;gen, je nach Konfiguration).<br>
    Dabei werden die Werte aus der Datenbank gepr&uuml;ft und &uuml;bernommen, auch wenn die &Auml;nderungen durch z.B. 
    eine Warenwirtschaft nur in der Datenbank erfolgten.<br><br>
    Einen manuellen Abgleich k&ouml;nnen Sie ansto&szlig;en, indem Sie den entsprechenden Funktionsbutton in der Kopfzeile vom magnalister anklicken (links von der Ameise).<br><br>
    Zus&auml;tzlich k&ouml;nnen Sie den Lagerabgleich (ab Tarif Flat - maximal viertelst&uuml;ndlich) auch durch einen eigenen CronJob ansto&szlig;en, 
    indem Sie folgenden Link zu Ihrem Shop aufrufen: <br>
    <i>{#setting:sSyncInventoryUrl#}</i><br>
    Eigene CronJob-Aufrufe durch Kunden, die nicht im Tarif Flat sind, oder die h&auml;ufiger als viertelst&uuml;ndlich laufen, werden geblockt.			                        
    </dd>
                    <dt>Artikel bearbeiten setzt Amazon-Preis gleich den Shop-Preis</dt>
                            <dd>Wenn der Artikelpreis im Shop durch das Bearbeiten des Artikels ge&auml;ndert wird,
                                wird der dann g&uuml;ltige aktuelle Artikelpreis vom Shop zu Amazon &uuml;bertragen.<br>
                                &Auml;nderungen nur in der Datenbank, z.B. durch eine Warenwirtschaft, werden hier <b>nicht</b> erfasst und &uuml;bermittelt!</dd>
            </dl><br>
            <b>Hinweis:</b> Die Einstellungen unter "Konfiguration" &rarr; "Preisberechnung" werden ber&uuml;cksichtigt.
',
        ),
    ),
), false);

MLI18n::gi()->add('amazon_config_orderimport', array(
    'legend' => array(
        'importactive' => 'Bestellimport',
        'mwst' => 'Mehrwertsteuer',
        'orderstatus' => 'Synchronisation des Bestell-Status vom Shop zu Amazon',
    ),
    'field' => array(
        'orderstatus.sync' => array(
            'label' => 'Status Synchronisierung',
            'hint' => '',
            'help' => '
                <dl>
                    <dt>Automatische Synchronisierung per CronJob (empfohlen)</dt>
                    <dd>
                        Die Funktion "Automatische Synchronisierung per CronJob" &uuml;bermittelt alle 2 Stunden den aktuellen Versendet-Status zu Amazon.<br/>
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
            'help' => 'Setzen Sie hier den Shop-Status, der auf Amazon automatisch den Status "Versand bestätigen" setzen soll.',
        ),
        'orderstatus.canceled' => array(
            'label' => 'Bestellung stornieren mit',
            'hint' => '',
            'help' => '
                Setzen Sie hier den Shop-Status, der auf  Amazon automatisch den Status "Bestellung stornieren" setzen soll. <br/><br/>
                Hinweis: Teilstorno ist hier&uuml;ber nicht m&ouml;glich. Die gesamte Bestellung wird &uuml;ber diese Funktion storniert
                und dem K&auml;ufer gutgeschrieben.
            ',
        ),
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'hint' => '',
            'help' => '{#i18n:form_config_orderimport_shop_help#}',
        ),
        'orderimport.paymentmethod' => array(
            'label' => 'Zahlart der Bestellungen',
            'help' => 'Zahlart, die allen Amazon-Bestellungen zugeordnet wird. Standard: "Amazon".<br><br>
				           Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&amul;gliche
				           Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
            'hint' => '',
        ),
        'orderimport.shippingmethod' => array(
            'label' => 'Versandart der Bestellungen',
            'help' => 'Versandart, die allen Amazon-Bestellungen zugeordnet wird. Standard: "Amazon".<br><br>
				           Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&amul;gliche
				           Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
           'hint' => '',
        ),
        'mwstfallback' => array(
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
                Amazon &uuml;bermittelt nicht den Steuersatz der Versandkosten, sondern nur die Brutto-Preise. Daher muss der Steuersatz zur korrekten Berechnung der Mehrwertsteuer f&uuml;r die Versandkosten hier angegeben werden. Falls Sie mehrwertsteuerbefreit sind, tragen Sie in das Feld 0 ein.
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
            'help' => 'Startzeitpunkt, ab dem die Bestellungen erstmalig importiert werden sollen. Bitte beachten Sie, dass dies nicht beliebig weit in die Vergangenheit möglich ist, da die Daten bei Amazon höchstens einige Wochen lang vorliegen.',
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
                Der Status, den eine von Amazon neu eingegangene Bestellung im Shop automatisch bekommen soll.<br />
                Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration → Bestellstatus).
            ',
        ),
        'orderstatus.fba' => array(
            'label' => 'Status f&uuml;r FBA-Bestellungen',
            'hint' => '',
            'help' => 'Funktion nur f&uuml;r H&auml;ndler, die am Programm "Versand durch Amazon (FBA)" teilnehmen: <br/>Definiert wird der Bestellstatus, 
				           den eine von Amazon importierte FBA-Bestellung im Shop automatisch bekommen soll. <br/><br/>
				           Sollten Sie ein angeschlossenes Mahnwesen verwenden, ist es empfehlenswert, den Bestellstatus auf "Bezahlt" zu setzen (Konfiguration &rarr; 
						   Bestellstatus).',
        ),
        'orderimport.fbapaymentmethod' => array(
            'label' => 'Zahlart der Bestellungen (FBA)',
            'help' => 'Zahlart, die allen Amazon-Bestellungen zugeordnet wird, die durch Amazon versendet werden. Standard: "Amazon".<br><br>
                        Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&auml;gliche
                        Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
            'hint' => '',
        ),
        'orderimport.fbashippingmethod' => array(
            'label' => 'Versandart der Bestellungen (FBA)',
            'help' => 'Versandart, die allen Amazon-Bestellungen zugeordnet wird, die durch Amazon versendet werden. Standard: "amazon".<br><br>
				           Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck und f&uuml;r die nachtr&auml;gliche
				           Bearbeitung der Bestellung im Shop sowie einige Warenwirtschaften.',
            'hint' => '',
        ),
        'orderstatus.carrier.default'=>array(
            'label' => '&nbsp;&nbsp;&nbsp;&nbsp;Spediteur',
            'help' => 'Vorausgew&auml;hlter Spediteur beim Best&auml;tigen des Versandes nach Amazon',
        ),
        'orderstatus.carrier.additional'=>array(
            'label' => '&nbsp;&nbsp;&nbsp;&nbsp;Zus&auml;tzliche Spediteure',
            'help' => 'Amazon bietet standardm&auml;&szlig;ig einige Spediteure zur Vorauswahl an. Sie k&ouml;nnen diese Liste erweitern.
				     Tragen Sie dazu weitere Spediteure kommagetrennt in das Textfeld ein.'
        ),
        'orderstatus.cancelled' => array(
            'label' => 'Bestellung stornieren mit',
            'hint' => '',
            'help' => 'Setzen Sie hier den Shop-Status, der auf Amazon automatisch den Status "Bestellung stornieren" setzen soll. <br/><br/>
                Hinweis: Teilstorno wird &uuml;ber die API von Amazon nicht angeboten. Die gesamte Bestellung wird &uuml;ber diese Funktion storniert
                und dem K&auml;ufer gutgeschrieben.',
        ),
    ),
), false);

MLI18n::gi()->add('amazon_config_emailtemplate', array(
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

MLI18n::gi()->add('amazon_config_shippinglabel', array(
    'legend' => array(
        'shippingaddresses' => 'Versandadressen',
        'shippingservice' => 'Versandeinstellungen',
        'shippinglabel' => 'Versandoptionen',
    ),
    'field' => array(
        'shippinglabel.address' => array(
            'label' => 'Versanadresse'
        ),
        'shippinglabel.address.name' => array(
            'label' => 'Name'
        ),
        'shippinglabel.address.company' => array(
            'label' => 'Firmenname'
        ),
        'shippinglabel.address.streetandnr' => array(
            'label' => 'Straße und Hausnummer'
        ),
        'shippinglabel.address.city' => array(
            'label' => 'Stadt'
        ),
        'shippinglabel.address.state' => array(
            'label' => 'Bundesland / Kanton'
        ),
        'shippinglabel.address.zip' => array(
            'label' => 'Postleitzahl'
        ),
        'shippinglabel.address.country' => array(
            'label' => 'Land'
        ),
        'shippinglabel.address.phone' => array(
            'label' => 'Telefonnummer'
        ),
        'shippinglabel.address.email' => array(
            'label' => 'E-Mail-Adresse'
        ),
        'shippingservice.carrierwillpickup' => array(
            'label' => 'Paket Abholung',
            'default' => 'false',
        ),
        'shippingservice.deliveryexpirience' => array(
            'label' => 'Versandbedingung',
        ),
        'shippinglabel.fallback.weight' => array(
            'label' => 'Alternativ Gewicht',
            'help' => ' Falls ein Produkt kein Gewicht hinterlegt hat, wird der hier angegebene Wert verwendet.',
        ),
        'shippinglabel.weight.unit' => array(
            'label' => 'Maßeinheit Gewicht',
        ),
        'shippinglabel.size.unit' => array(
            'label' => 'Maßeinheit Größe',
        ),
        'shippinglabel.default.dimension' => array(
            'label' => 'Benutzerdefinierte Paketgrößen',
        ),
        'shippinglabel.default.dimension.text' => array(
            'label' => 'Bezeichnung',
        ),
        'shippinglabel.default.dimension.length' => array(
            'label' => 'L&auml;nge',
        ),
        'shippinglabel.default.dimension.width' => array(
            'label' => 'Breite',
        ),
        'shippinglabel.default.dimension.height' => array(
            'label' => 'H&ouml;he',
        ),
    ),
), false);