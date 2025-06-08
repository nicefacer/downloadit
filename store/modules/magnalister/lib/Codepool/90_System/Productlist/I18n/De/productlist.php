<?php
MLI18n::gi()->Productlist_Filter_sEmpty='Filter (%s)';
MLI18n::gi()->Productlist_Filter_sLimit='%s Artikel pro Seite';
MLI18n::gi()->Productlist_Filter_sSearch='suchen...';
MLI18n::gi()->Productlist_Filter_sCategory='Kategorie';
MLI18n::gi()->Productlist_Header_sPriceShop='Shop-Preis';
MLI18n::gi()->Productlist_Header_sPriceMarketplace='%s Preis';
MLI18n::gi()->Productlist_Header_sImage='Bild';
MLI18n::gi()->Productlist_Header_sProduct='Produkt';
MLI18n::gi()->Productlist_Header_sSortAsc='Sortiere aufsteigend';
MLI18n::gi()->Productlist_Header_sSortDesc='Sortiere absteigend';
MLI18n::gi()->Productlist_Header_sSku='SKU';
MLI18n::gi()->Productlist_Header_Field_sManufacturerpartnumber='MPN';
MLI18n::gi()->Productlist_Header_Field_sEan='EAN';
MLI18n::gi()->Productlist_Header_Field_sCategoryPath='Kategoriepfad';
MLI18n::gi()->Productlist_Header_sMarketplaceCategory='Marktplatz Kategorie';
MLI18n::gi()->Productlist_Pagination_sFirstPage='Erste Seite';
MLI18n::gi()->Productlist_Pagination_sLastPage='Letzte Seite';
MLI18n::gi()->Productlist_Header_sPreparedStatus='Vorbereitungsstatus';
MLI18n::gi()->Productlist_Header_sPreparedType='Vorbereitungsart';
MLI18n::gi()->Productlist_Cell_sEditProduct='Shopartikel bearbeiten';
MLI18n::gi()->Productlist_Cell_sNoImage='kein Bild';
MLI18n::gi()->Productlist_Cell_aNotPreparedStatus__title='Nicht vorbereitet';
MLI18n::gi()->Productlist_Cell_sNotPreparedYet = 'bitte erst vorbereiten';

MLI18n::gi()->Productlist_Filter_aPreparedStatus_all='Filter (Vorbereitungsstatus)';
MLI18n::gi()->Productlist_Filter_aPreparedStatus_not='Nicht vorbereitete';
MLI18n::gi()->Productlist_Cell_sNotPreparedType='Nicht vorbereitet';

MLI18n::gi()->Productlist_Filter_aLastPrepared_all = 'Filter (Vorbereitet am)';
MLI18n::gi()->Productlist_Filter_aLastPrepared_dateFormat = 'd.m.Y | H:i \U\h\r';//escape fixed text!!!

MLI18n::gi()->Productlist_Filter_aMarketplaceSync__all = 'Filter (Marktplatzstatus)';
MLI18n::gi()->Productlist_Filter_aMarketplaceSync__notActive = 'Zeige nicht auf {#marketplace#} vorhandene';
MLI18n::gi()->Productlist_Filter_aMarketplaceSync__notTransferred = 'Zeige noch nie auf {#marketplace#} eingestellte';
MLI18n::gi()->Productlist_Filter_aMarketplaceSync__active = 'Zeige auf {#marketplace#} vorhandene';
MLI18n::gi()->Productlist_Filter_aMarketplaceSync__sync = 'Zeige Beendete duch Ausverkauf';
MLI18n::gi()->Productlist_Filter_aMarketplaceSync__expired = 'Zeige Beendete durch Laufzeitende';

MLI18n::gi()->sModal_marketplacesyncfilter_title = 'Synchronisiere Daten mit magnalister-Server';
MLI18n::gi()->sModal_marketplacesyncfilter_content = 'Bitte haben Sie Geduld.';
                
MLI18n::gi()->Productlist_Filter_ARTICLES_ALL = 'Zeige alle';
MLI18n::gi()->Productlist_Filter_ARTICLES_NOTTRANSFERRED = 'Zeige noch nicht auf %s eingestellte';
MLI18n::gi()->Productlist_Filter_ARTICLES_NOTACTIVE = 'Zeige nicht auf %s vorhandene';
MLI18n::gi()->Productlist_Filter_ARTICLES_ACTIVE = 'Zeige auf %s vorhandene';
MLI18n::gi()->Productlist_Filter_ARTICLES_DELETEDBY_SYNC = 'Beendete: durch Lagersync.';
MLI18n::gi()->Productlist_Filter_ARTICLES_DELETEDBY_BUTTON = 'Beendete: durch man. L&ouml;schen';
MLI18n::gi()->Productlist_Filter_ARTICLES_DELETEDBY_EXPIRED = 'Beendete: durch Laufzeitende';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection_selectedArticlesCountInfo='Auswahl ({#count#} ausgew&auml;hlt)';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__selection__name='{#i18n:Productlist_Cell_aToMagnalisterSelection_selectedArticlesCountInfo#}';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__add__name='hinzuf&uuml;gen';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__add__values__page='wähle Produkte dieser Seite';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__add__values__filter='wähle alle Produkte (gefilterte)';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__sub__name='entfernen';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__sub__values__page='Auswahl dieser Seite aufheben';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__sub__values__filter='Auswahl aller Produkte aufheben (gefilterte)';
MLI18n::gi()->Productlist_Cell_aToMagnalisterSelection__sub__values__all='Auswahl aller Produkte aufheben';
MLI18n::gi()->Productlist_Message_sEditProducts='%s Produkte wurden zur Auswahl hinzugefügt oder geändert.';
MLI18n::gi()->Productlist_Message_sDeleteProducts='%s Produkte wurden aus der Auswahl gelöscht.';
MLI18n::gi()->Productlist_Message_sErrorGeneral='Ihre Anfrage konnte nicht erfolgreich bearbeitet werden.';

MLI18n::gi()->Productlist_ProductMessage_sErrorNoVariants='Produkt hat keine Varianten.';
MLI18n::gi()->Productlist_ProductMessage_sErrorToManyVariants='Dieses Produkt hat {#variantCount#} errechnete Varianten. Der Marktplatzanbieter erlaubt maximal {#maxVariantCount#} Varianten pro Produkt.';
MLI18n::gi()->Productlist_ProductMessage_sNoDistinctSku='{#count#} Varianten dieses Produkts haben keine eindeutige SKU.';
MLI18n::gi()->Productlist_ProductMessage_sErrorMissingField='Produkt hat kein `%s` Wert.';
MLI18n::gi()->Productlist_ProductMessage_sErrorProductTypeNotSupported='Produkttyp "{#productType#}." wird nicht unterstützt.';
MLI18n::gi()->Productlist_ProductMessage_sVariantsHaveError='Varianten diese Artikels haben Fehler.';