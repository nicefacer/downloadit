<?php
MLI18n::gi()->form_config_orderimport_exchangerate_update_help = '<strong>Grundsätzlich:</strong>
<p>
Wenn die Web-Shop Standard-Währung von der Marktplatz-Währung abweicht, berechnet magnalister beim Bestellimport und beim Artikelupload anhand des Währungskurses, der im Web-Shop hinterlegt ist. 
Beim Bestellimport verhält sich magnalister beim Speichern der Währungen und Beträge 1:1 so, wie der Web-Shop sie bei Bestelleingang auch anlegt.
</p>

<strong>Achtung:</strong>
<p>
Durch Aktivieren dieser Funktion hier wird der im Web-Shop hinterlegte Wechselkurs mit dem Prestashop Webservice für Wechselkurse aktualisiert. 
<u>Dadurch werden auch die Preise in Ihrem Web-Shop mit dem aktualisierten Wechselkurs zum Verkauf angezeigt.</u>
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
Durch Aktivieren wird der im Web-Shop hinterlegte Wechselkurs mit dem Prestashop Webservice für Wechselkurse aktualisiert. 
<u>Dadurch werden auch die Preise in Ihrem Web-Shop mit dem aktualisierten Wechselkurs zum Verkauf angezeigt.</u>
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

MLI18n::gi()->generic_config_generic_title = 'Allgemein';
MLI18n::gi()->add('generic_config_generic', array(
    'legend' => array(
        'generic' => 'Allgemein',
        'tabident' => ''
    ),
    'field' => array(
        'orderimport.shop' => array(
            'label' => '{#i18n:form_config_orderimport_shop_lable#}',
            'help' => '',
        ),
    ),
), false);
