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

MLI18n::gi()->add('ricardo_prepare_form', array(
    'field' => array(
        'dedescription' => array(
            'label' => 'Beschreibung (Deutsch)',
            'hint' =>
                'Liste verf&uuml;gbarer Platzhalter f&uuml;r die Produktbeschreibung:'.
                '<dl>' .
                '<dt>#TITLE#</dt>'.
                '<dd>Produktname (Titel)</dd>'.
                '<dt>#ARTNR#</dt>'.
                '<dd>Artikelnummer im Shop</dd>'.
                '<dt>#PID#</dt>'.
                '<dd>Produkt ID im Shop</dd>'.
                '<dt>#SHORTDESCRIPTION#</dt>'.
                '<dd>Kurzbeschreibung aus dem Shop</dd>'.
                '<dt>#DESCRIPTION#</dt>'.
                '<dd>Beschreibung aus dem Shop</dd>'.
                '<dt>#PICTURE1#</dt>'.
                '<dd>erstes Produktbild</dd>'.
                '<dt>#PICTURE2# usw.</dt>'.
                '<dd>zweites Produktbild; mit #PICTURE3#, #PICTURE4# usw. k&ouml;nnen weitere Bilder &uuml;bermittelt werden, so viele wie im Shop vorhanden.</dd>'.
                '<br><dt>Artikel-Freitextfelder:</dt><br>'.
                '<dt>#Bezeichnung1#&nbsp;#Freitextfeld1#</dt>'.
                '<dt>#Bezeichnung2#&nbsp;#Freitextfeld2#</dt>'.
                '<dt>#Bezeichnung..#&nbsp;#Freitextfeld..#</dt><br>'.
                '<dd>&Uuml;bernahme der Artikel-Freitextfelder:&nbsp;'.
                '   Die Ziffer hinter dem Platzhalter (z.B. #Freitextfeld1#) entspricht der Position des Freitextfelds.'.
                '   <br> Siehe Einstellungen > Grundeinstellungen > Artikel > Artikel-Freitextfelder</dd>'.
                '<dt>#PROPERTIES#</dt>'.
                '<dd>Eine Liste aller Produkteigenschaften des Produktes. Aussehen kann &uuml;ber CSS gesteuert werden (siehe Code vom Standard Template)</dd>'.
                '</dl>',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'Artikelbeschreibung immer aktuell aus Web-Shop verwenden',
                )
            ),
        ),
        'frdescription' => array(
            'label' => 'Beschreibung (Deutsch)',
            'hint' =>
                'Liste verf&uuml;gbarer Platzhalter f&uuml;r die Produktbeschreibung:'.
                '<dl>' .
                '<dt>#TITLE#</dt>'.
                '<dd>Produktname (Titel)</dd>'.
                '<dt>#ARTNR#</dt>'.
                '<dd>Artikelnummer im Shop</dd>'.
                '<dt>#PID#</dt>'.
                '<dd>Produkt ID im Shop</dd>'.
                '<dt>#SHORTDESCRIPTION#</dt>'.
                '<dd>Kurzbeschreibung aus dem Shop</dd>'.
                '<dt>#DESCRIPTION#</dt>'.
                '<dd>Beschreibung aus dem Shop</dd>'.
                '<dt>#PICTURE1#</dt>'.
                '<dd>erstes Produktbild</dd>'.
                '<dt>#PICTURE2# usw.</dt>'.
                '<dd>zweites Produktbild; mit #PICTURE3#, #PICTURE4# usw. k&ouml;nnen weitere Bilder &uuml;bermittelt werden, so viele wie im Shop vorhanden.</dd>'.
                '<br><dt>Artikel-Freitextfelder:</dt><br>'.
                '<dt>#Bezeichnung1#&nbsp;#Freitextfeld1#</dt>'.
                '<dt>#Bezeichnung2#&nbsp;#Freitextfeld2#</dt>'.
                '<dt>#Bezeichnung..#&nbsp;#Freitextfeld..#</dt><br>'.
                '<dd>&Uuml;bernahme der Artikel-Freitextfelder:&nbsp;'.
                '   Die Ziffer hinter dem Platzhalter (z.B. #Freitextfeld1#) entspricht der Position des Freitextfelds.'.
                '   <br> Siehe Einstellungen > Grundeinstellungen > Artikel > Artikel-Freitextfelder</dd>'.
                '<dt>#PROPERTIES#</dt>'.
                '<dd>Eine Liste aller Produkteigenschaften des Produktes. Aussehen kann &uuml;ber CSS gesteuert werden (siehe Code vom Standard Template)</dd>'.
                '</dl>',
            'optional' => array(
                'checkbox' => array(
                    'labelNegativ' => 'Artikelbeschreibung immer aktuell aus Web-Shop verwenden',
                )
            ),
        ),
    )
), false);
