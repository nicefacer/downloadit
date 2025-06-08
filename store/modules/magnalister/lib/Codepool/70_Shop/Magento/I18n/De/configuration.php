<?php
MLI18n::gi()->Magento_Global_Configuration_Label = 'Gewichtseinheit';
MLI18n::gi()->Magento_Global_Configuration_Description = 'Gewichtseinheit';
MLI18n::gi()->form_config_orderimport_exchangerate_update_help = '<strong>Grundsätzlich:</strong>
<p>
Wenn die Web-Shop Standard-Währung von der Marktplatz-Währung abweicht, berechnet magnalister beim Bestellimport und beim Artikelupload anhand des Währungskurses, der im Web-Shop hinterlegt ist. 
Beim Bestellimport verhält sich magnalister beim Speichern der Währungen und Beträge 1:1 so, wie der Web-Shop sie bei Bestelleingang auch anlegt.
</p>

<strong>Achtung:</strong>
<p>
Durch Aktivieren dieser Funktion hier wird der im Web-Shop hinterlegte Wechselkurs mit dem Import Dienst aktualisiert, der in Magento unter "System" > „Währungen verwalten“ aktiviert ist. 
<u>Dadurch werden auch die Preise in Ihrem Web-Shop mit dem aktualisierten Wechselkurs zum Verkauf angezeigt:</u>
</p>
<p>
Folgende Funktionen lösen die Aktualisierung aus:
<ul>
<li>Bestellimport</li>
<li>Artikel-Vorbereitung</li>
<li>Artikel-Upload</li>
<li>Lager-/Preis-Synchronisation</li>
</ul>
</p>
<p>
Sollte der Währungskurs eines Marktplatzes in der Währungskonfiguration des Web-Shops nicht angelegt sein, gibt magnalister eine Fehlermeldung aus.
</p>';
MLI18n::gi()->form_config_orderimport_exchangerate_update_alert = '<strong>Achtung:</strong>
<p>
Durch Aktivieren wird der im Web-Shop hinterlegte Wechselkurs mit dem Import Dienst aktualisiert, der in Magento unter "System" > "Währungen verwalten“ aktiviert ist. 
<u>Dadurch werden auch die Preise in Ihrem Web-Shop mit dem aktualisierten Wechselkurs zum Verkauf angezeigt:</u>
</p>
<p>
Folgende Funktionen lösen die Aktualisierung aus:
<ul>
<li>Bestellimport</li>
<li>Artikel-Vorbereitung</li>
<li>Artikel-Upload</li>
<li>Lager-/Preis-Synchronisation</li>
</ul>
</p>
';
MLI18n::gi()->magentospecific_aGeneralForm__orderimport__fields__orderinformation = array (
    'values' => array('val' => 'Bestellnummer und Marktplatzname im Rechnungsdruck anzeigen.'),
    'desc' => '
        Wenn Sie die Funktion aktivieren, wird die Marktplatz-Bestellnummer und der Marktplatzname nach dem Bestellimport in den Rechnungsdaten gespeichert.<br />
        Der Rechnungsdaten werden in der Rechnung mit ausgegeben, so dass der Endkunde somit automatisch Information erhält, woher die Bestellung urspr&uuml;nglich stammt.
    ',
);