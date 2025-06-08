<?php

$aForm = array(
    'general' => array(
        'headline' => 'Allgemeine Einstellungen',
        'fields' => array(
            'pass'=>array(
                'label' => 'PassPhrase',
                'desc' => 'Die PassPhrase erhalten Sie nach der Registrierung auf www.magnalister.com.',
            ),
        ),
    ),
    'sku' => array(
        'headline' => 'Synchronisation Nummernkreise',
        'fields' => array(
            'sku'=>array(
                'label' => 'Bitte w&auml;hlen Sie',
                'desc' => 'Je nach Auswahl wird die Artikelnummer vom Shop als SKU auf dem Marketplace verwendet, oder die Product ID
				      des Shops als Marketplace-SKU verwendet, um das Produkt bei Lagersynchronisation und Bestellimporten zuordnen zu k&ouml;nnen.<br/><br/>
				      Diese Funktion wirkt sich ma&szlig;geblich bei der Weiterverarbeitung 
				      &uuml;ber eine Warenwirtschaft, sowie bei Abgleich der Shop- und Marketplace-Inventare aus.<br /><br />
				      <strong>Vorsicht!</strong> Die Synchronisation der Lagermengen und -Preise h&auml;ngt von dieser Einstellung ab. Wenn Sie bereits Artikel hochgeladen haben, sollten Sie diese Einstellung <strong>nicht mehr &auml;ndern</strong>, sonst k&ouml;nnen die "alten" Artikel nicht mehr synchronisiert werden.',
                
                'values' => array(
                    'pID' => 'Product ID (Shop) = SKU (Marketplace)',
                    'artNr' => 'Artikelnummer (Shop) = SKU (Marketplace)'
                )
            )
        )
    ),
    'stats' => array(
        'headline' => 'Statistiken',
        'fields' => array(
            'back' => array(
                'label' => 'Monate zur&uuml;ck',
                'desc' => 'Wie viele Monate soll die Statistik zur&uuml;ck reichen?',
                'values' => array(
                    '0' => '1 Monat',
                    '1' => '2 Monate',
                    '2' => '3 Monate',
                    '3' => '4 Monate',
                    '4' => '5 Monate',
                    '5' => '6 Monate',
                    '6' => '7 Monate',
                    '7' => '8 Monate',
                    '8' => '9 Monate',
                    '9' => '10 Monate',
                    '10' => '11 Monate',
                    '11' => '12 Monate',
                ),
            ),
        ),
    ),
    'ftp' => array(
        'headline' => 'FTP',
        'fields' => array(
            'host' => array(
                'label' => 'FTP-Server',
                'morefields' => array(
                    'port'=>array(
                        'label' => 'Port',
                    )
                )
            ),
            'login'=>array(
                'label' => 'Benutzername',
                'desc' => 'Bitte geben Sie den Benutzernamen zu Ihrem FTP-Server an.',
            ),
            'pswd'=>array(
                'label' => 'Passwort',
                'desc' => 'Bitte geben Sie das Passwort zu dem Benutzernamen an.',
            ),
        ),
    ),
    'orderimport' => array(
        'headline' => 'Bestellimport',
        'fields' => array(
            'timetable' => array(
                'label' => 'Bestellabrufe',
                'desc' => 'Zeiten, zu denen Bestellungen abgerufen werden. Voreinstellung ist zu jeder vollen Stunde.<br /><br />eBay ist hiervon nicht betroffen: Beim Klick auf \'Kaufen\' auf eBay wird der Shop sofort benachrichtigt und die Bestellung wird importiert.',
                'values' => array(
                    '0' => '00:00', '1' => '01:00', '2' => '02:00', '3' => '03:00',
                    '4' => '04:00', '5' => '05:00', '6' => '06:00', '7' => '07:00',
                    '8' => '08:00', '9' => '09:00', '10' => '10:00', '11' => '11:00',
                    '12' => '12:00', '13' => '13:00', '14' => '14:00', '15' => '15:00',
                    '16' => '16:00', '17' => '17:00', '18' => '18:00', '19' => '19:00',
                    '20' => '20:00', '21' => '21:00', '22' => '22:00', '23' => '23:00',
                ),
            ),
            'orderinformation' => array(
                'label' => 'Bestellinformation',
                'values' => array(
                    'val' => 'Bestellnummer und Marktplatzname im Kundenkommentar speichern',
                ),
                'desc' => 'Wenn Sie die Funktion aktivieren, wird die Marktplatz-Bestellnummer und der Marktplatzname nach dem Bestellimport im Kundenkommentar gespeichert.<br />
Der Kundenkommentar kann in vielen Systemen auf der Rechnung &uuml;bernommen werden, so dass der Endkunde somit automatisch Information erhält, woher die Bestellung urspr&uuml;nglich stammt.<br />
Auch k&ouml;nnen Sie damit Erweiterungen f&uuml;r weitere statistische Umsatz-Auswertungen programmieren lassen.<br />
<b>Wichtig:</b> Einige Warenwirtschaften importieren keine Bestellungen, bei denen der Kundenkommentar gesetzt ist. Wenden Sie sich für weitere Fragen dazu bitte direkt an Ihren WaWi-Anbieter.',                
                
            ),
        ),
    ),
    'cronTimeTable' => array(
        'headline' => 'Sonstiges',
        'fields' => array( 
            /*
             *  this part is commented , because till now no customer in v3 need it and shopware has own configuration
                       
            'cid' => array(
                'label' => 'Kundennummern',
                'desc' => 'Vergabe von eigenen Kundennummern beim Anlegen von Neukunden, wenn Bestellungen importiert werden:<br /><br />
					<strong>Fortlaufend:</strong><br />Jede neue Kundennummer (customers_cid in der Datenbanktabelle "customers") wird aus der h&ouml;chsten bestehenden Kundennummer plus 1 berechnet.<br /><br />
					<strong>customers_id:</strong><br />Die Kundennummer wird der Datenbank-internen customers_id in der Tabelle "customers" gleichgesetzt:<br />
					customers_cid = customers_id<br /><br />
					<strong>Leer lassen:</strong><br />Die Nummer wird nicht vergeben. Sie kann manuell nachgetragen werden.<br /><br />
					Die Kundennummernvergabe hat Bedeutung f&uuml;r einige Warenwirtschaften.<br /><br />Diese Einstellung gilt nur f&uuml;r Shops die frei zu vergebende Kundennummern haben (Gambio, xt:Commerce und Weiterentwicklungen davon).',
                
                'values' => array(
                    'sequential' => 'Fortlaufend',
                    'customers_id' => '= customers_id',
                    'none' => 'Leer lassen',
                ),
            ),
             */
            'editor' => array(
                'label' => 'Editor',
                'desc' => 'Editor f&uuml;r Artikelbeschreibungen, Templates und Promotion-E-Mails.<br /><br />
	                <strong>TinyMCE Editor:</strong><br />Verwenden Sie einen komfortablen Editor, der fertig formatiertes HTML anzeigt und z.B. Bild-Pfade in der 
	                Artikelbeschreibung automatisch korrigiert.<br /><br />
	                <strong>Einfaches Textfeld, lokale Links erweitern:</strong><br />Verwenden Sie ein einfaches Textfeld. Sinnvoll in F&auml;llen wenn der TinyMCE Editor ungewollte &Auml;nderungen der eingegebenen Templates bewirkt
	                (wie z.B. in dem eBay-Produkt-Template).<br />
	                Bilder oder Links, deren Adressen nicht mit <strong>http://</strong>,
	                <strong>javascript:</strong>, <strong>mailto:</strong> oder <strong>#</strong> anfangen,
	                werden jedoch um die Shop-Adresse erweitert.<br /><br />
	                <strong>Einfaches Textfeld, Daten direkt &uuml;bernehmen:</strong><br />Es werden keine Adressen erweitert oder sonstige &Auml;nderungen am eingegebenen Text vorgenommen.',
                
                'values' => array(
                    'tinyMCE' => 'TinyMCE Editor',
                    'none' => 'Einfaches Textfeld, lokale Links erweitern',
                    'none_none' => 'Einfaches Textfeld, Daten direkt &uuml;bernehmen'
                ),
            ),
            'stocksyncbyorder' => array(
                'label' => 'Synchro-Trigger<br /> bei Bestellungen',
                'desc' => 'Soll bei Bestellungen im Shop der Lagerbestand auf den angeschlossenen Marktpl&auml;tzen direkt abgeglichen werden?<br /><br />
							<b>Vorteil</b>: So vermeiden Sie &Uuml;berverk&auml;ufe<br />
							<b>Nachteil</b>: Der Abgleich kann einige Sekunden dauern, was den Bestellvorgang verz&ouml;gert.<br />',
                
                'values' => array(
                    'val' => 'Bei Bestellungen im Shop, den Lagerbestand sofort zu den Marktpl&auml;tzen synchronisieren',
                ),
            ),
        ),
    ),
    'articleStatusInventory' => array(
        'headline' => 'Inventar',
        'fields' => array(
            'statusIsZero' => array(
                'label' => 'Produktstatus',
                'desc' => 'Sie k&ouml;nnen mit dieser Funktion bestimmen, ob Artikel, die im Web-Shop auf "<i>Inaktiv</i>" gesetzt werden, auch auf dem Marktplatz beendet (eBay),<br/>
						oder ebenfalls "inaktiv" gesetzt werden (&uuml;brige).<br/>
						<br/>
						Damit diese Funktion wirksam wird, aktivieren Sie bitte auch im jeweiligen Marktplatz Modul unter<br/>
						"<i>Synchronisation des Inventars</i>" > "<i>Lagerver&auml;nderung Shop</i>" ><br/>
						"<i>automatische Synchronisation per CronJob</i>".<br/>',
                
                'values' => array(
                    'true' => 'Wenn Produktstatus inaktiv ist, wird der Lagerbestand wie 0 behandelt',
                    'false' => 'Immer den aktuellen Lagerbestand nutzen'
                ),
            )
        )
    ),
    'productfields' => array(
            'headline' => 'Produkteigenschaften',
            'fields' => array(
                'manufacturer' => array(
                    'label' => 'Hersteller',
                    'desc' => 'W&auml;hlen Sie hier das Produkt-Attribut / Freitextfeld, in dem der Hersteller-Name des Produkts gespeichert wird.
Die Attribute / Freitextfelder definieren Sie direkt &uuml;ber Ihre Web-Shop Verwaltung.',
                   
                ),
                'mfnpartno' => array(
                    'label' => 'Hersteller-Modellnummer',
                    'desc' => 'W&auml;hlen Sie hier die Artikel-Eigenschaft / Freitextfeld, in dem die Hersteller-Modellnummer des Produkts gespeichert wird.
Die Artikel-Eigenschaften / Freitextfelder definieren Sie direkt &uuml;ber Ihre Web-Shop Verwaltung.',

                ),
                'EAN' => array(
                    'label' => 'EAN',
                    'desc' => 'European Article Number<br/><br/>
				           <b>Hinweis:</b> Diese Daten werden nicht &uuml;berpr&uuml;ft. Sollten Sie fehlerhaft sein, wird es zu Datenbankfehlern kommen!',
                    
                ),
            ),
        ),
);
MLI18n::gi()->set('aGeneralForm', $aForm);