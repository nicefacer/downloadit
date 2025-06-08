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

MLI18n::gi()->ebay_config_producttemplate_content =
'<style>
ul.magna_properties_list {
    margin: 0 0 20px 0;
    list-style: none;
    padding: 0;
    display: inline-block;
    width: 100%
}
ul.magna_properties_list li {
    border-bottom: none;
    width: 100%;
    height: 20px;
    padding: 6px 5px;
    float: left;
    list-style: none;
}
ul.magna_properties_list li.odd {
    background-color: rgba(0, 0, 0, 0.05);
}
ul.magna_properties_list li span.magna_property_name {
    display: block;
    float: left;
    margin-right: 10px;
    font-weight: bold;
    color: #000;
    line-height: 20px;
    text-align: left;
    font-size: 12px;
    width: 50%;
}
ul.magna_properties_list li span.magna_property_value {
    color: #666;
    line-height: 20px;
    text-align: left;
    font-size: 12px;

    width: 50%;
}
</style>
<p>#TITLE#</p>
<p>#ARTNR#</p>
<p>#SHORTDESCRIPTION#</p>
<p>#PICTURE1#</p>
<p>#PICTURE2#</p>
<p>#PICTURE3#</p>
<p>#DESCRIPTION#</p>
<p>#Bezeichnung1# #Freitextfeld1#</p>
<p>#Bezeichnung2# #Freitextfeld2#</p>
<div>#PROPERTIES#</div>';

MLI18n::gi()->add('ebay_config_orderimport', array(
     'field' => array(
         'updateablepaymentstatus' => array(
             'label' => 'Zahl-Status-&Auml;nderung zulassen wenn',
             'help' => 'Stati der Bestellungen, die bei eBay-Zahlungen ge&auml;ndert werden d&uuml;rfen.
			                Wenn die Bestellung einen anderen Status hat, wird er bei eBay-Zahlungen nicht ge&auml;ndert.<br /><br />
			                Wenn Sie gar keine &Auml;nderung des Zahlstatus bei eBay-Zahlung w&uuml;nschen, deaktivieren Sie die Checkbox.<br /><br />
			                <b>Hinweis:</b> Der Status von zusammengefa&szlig;ten Bestellungen wird nur dann ge&auml;ndert, wenn alle Teile bezahlt sind.',
         ),
        'paidstatus'=> array(
            'label' => 'eBay Bezahlt-Status im Shop',
            'help' => '<p>Hier setzen Sie den Zahl- und den Bestellstatus, den eine Bestellung im Shop bekommt, sobald sie bei eBay mit PayPal bezahlt wird.</p>
<p>
Wenn ein Kunde auf eBay kauft, wird die Bestellung sofort in Ihren Web-Shop &uuml;bertragen.
Dabei wird zuerst die Zahlungsweise auf „eBay“, oder den Wert, den Sie unter „Experteneinstellungen“ hinterlegt haben, gesetzt.</p>

<p>
magnalister &uuml;berwacht weiterhin 16 Tage lang st&uuml;ndlich, ob ein Käufer auf eBay nach dem ersten Bestellimport seine Zahlung später getätigt, oder seine Versandadresse geändert hat.
Dabei rufen wir Änderungen in folgenden Intervallen ab:


	<ul>
        <li>	1,5 Std. nach der Bestellung jede 15 Minuten,</li>
	<li>	bis 24 Std. nach der Bestellung jede Stunde,</li>
	<li>	bis 48 Std. - alle 2 Std.</li>
	<li>	bis 1 Woche - alle 3 Std.</li>
	<li>	bis 16 Tage nach der Bestellung alle 6 Std.</li>
        </ul>

Dabei verwendet magnalister die eBay-Information, die Sie auch in Ihrem eBay-Account unter "Aktivität" > "Zusammenfassung" > "Verkaufsmanager Pro" > "Verkauft" in der 12ten Spalte (Euro-Symbol) sehen können: Ein fett gedrucktes Symbol ist der Hinweis auf "bezahlt". 
</p>'
        ),
        'orderstatus.paid' => array(
            'label' => 'Bestellstatus',
            'help' => '',
        ),
        'paymentstatus.paid' => array(
            'label' => 'Zahlstatus',
            'help' => '',
        ),
        'updateable.paymentstatus' => array(
            'label' => '',
            'help' => '',
        ),
        'update.paymentstatus' => array(
            'label' => 'Status-&Auml;nderung aktiv',
        ),
        'orderimport.paymentmethod' => array(
            'label' => 'Zahlart der Bestellungen',
            'help' => '<p>Zahlart, die allen Tradoria-Bestellungen beim Bestellimport zugeordnet wird. 
<p>
Alle Zahlarten in der Liste k&ouml;nnen Sie ebenfalls unter Shopware > Einstellungen > Zahlungsarten definieren und hier&uuml;ber dann verwenden.
</p>
<p>
Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck, und f&uuml;r die nachtr&auml;gliche Bearbeitung der Bestellung im Shop, sowie in Warenwirtschaften.
</p>',
            'hint' => '',
        ),
        'orderimport.shippingmethod' => array(
            'label' => 'Versandart der Bestellungen',
            'help' => '<p>Zahlart, die allen {#platformName#}-Bestellungen beim Bestellimport zugeordnet wird. 
Standard: "Automatische Zuordnung"</p>
<p>
Wenn Sie „Automatische Zuordnung" wählen, &uuml;bernimmt magnalister die Zahlart, die der K&auml;ufer auf {#platformName#} gew&auml;hlt hat.
Diese wird dann zus&auml;tzlich auch unter Shopware > Einstellungen > Zahlungsarten angelegt.</p>
<p>
Alle weiteren verf&uuml;gbaren Zahlarten in der Liste k&ouml;nnen Sie ebenfalls unter Shopware > Einstellungen > Zahlungsarten definieren und hier&uuml;ber dann verwenden.</p>
<p>
Diese Einstellung ist wichtig f&uuml;r den Rechnungs- und Lieferscheindruck, und f&uuml;r die nachtr&auml;gliche Bearbeitung der Bestellung im Shop, sowie in Warenwirtschaften.</p>',
            'hint' => '',
        ),
        'orderimport.paymentstatus' => array(
            'label' => 'Zahlstatus im Shop',
            'hint' => '',
        ),
    ),
), true);

MLI18n::gi()->add('ebay_config_producttemplate', array(
    'field' => array(
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
            <dd>zweites Produktbild; mit #PICTURE3#, #PICTURE4# usw. k&ouml;nnen weitere Bilder &uuml;bermittelt werden, so viele wie im Shop vorhanden.</dd>'
        .'<br><dt>Artikel-Freitextfelder:</dt><br>'
        .'<dt>#Bezeichnung1#&nbsp;#Freitextfeld1#</dt>'
        .'<dt>#Bezeichnung2#&nbsp;#Freitextfeld2#</dt>'
        .'<dt>#Bezeichnung..#&nbsp;#Freitextfeld..#</dt><br>'
        .'<dd>&Uuml;bernahme der Artikel-Freitextfelder:&nbsp;'
        .'Die Ziffer hinter dem Platzhalter (z.B. #Freitextfeld1#) entspricht der Position des Freitextfelds.
                <br> Siehe Einstellungen > Grundeinstellungen > Artikel > Artikel-Freitextfelder</dd>'
        .'<dt>#PROPERTIES#</dt>'
        .'<dd>Eine Liste aller Produkteigenschaften des Produktes. Aussehen kann &uuml;ber CSS gesteuert werden (siehe Code vom Standard Template)</dd>'.
        '</dl>',
        ),
    ),
), false);
MLI18n::gi()->{'ebay_config_orderimport__field__customergroup__help'} = '{#i18n:global_config_orderimport_field_customergroup_help#}';
