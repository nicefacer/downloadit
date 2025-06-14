<?php
/* Autogenerated file. Do not change! */

MLI18n::gi()->{'tradoria_config_account_title'} = 'Login Details';
MLI18n::gi()->{'tradoria_config_account_prepare'} = 'Item preparation';
MLI18n::gi()->{'tradoria_config_account_price'} = 'Price Calculation';
MLI18n::gi()->{'tradoria_config_account_sync'} = 'Synchronization';
MLI18n::gi()->{'tradoria_config_account_orderimport'} = 'Order Import';
MLI18n::gi()->{'tradoria_config_account_emailtemplate'} = 'Promotion Email Template';
MLI18n::gi()->{'tradoria_config_account_emailtemplate_sender'} = 'Example Shop';
MLI18n::gi()->{'tradoria_config_account_emailtemplate_sender_email'} = 'example@onlineshop.com';
MLI18n::gi()->{'tradoria_config_account_emailtemplate_subject'} = 'Your Order from #SHOPURL#';
MLI18n::gi()->{'tradoria_config_account_emailtemplate_content'} = ' <style>
        <!—body { font: 12px sans-serif; }
        table.ordersummary { width: 100%; border: 1px solid #e8e8e8; }
        table.ordersummary td { padding: 3px 5px; }
        table.ordersummary thead td { background: #cfcfcf; color: #000; font-weight: bold; text-align: center; }
        table.ordersummary thead td.name { text-align: left; }
        table.ordersummary tbody tr.even td { background: #e8e8e8; color: #000; }
        table.ordersummary tbody tr.odd td { background: #f8f8f8; color: #000; }
        table.ordersummary td.price, table.ordersummary td.fprice { text-align: right; white-space: nowrap; }
        table.ordersummary tbody td.qty { text-align: center; }—>
    </style>
    <p>Hello #FIRSTNAME# #LASTNAME#,</p>
    <p>Thank you for your order! You have purchased the following from our shop on #MARKETPLACE#:</p>
    #ORDERSUMMARY#
    <p>Shipping costs are included.</p>
    <p>You’ll find more great offers in our shop at <strong>#SHOPURL#</strong>.</p>
    <p>&nbsp;</p>
    <p>Yours sincerely,</p>
    <p>Your online shop team</p>';
MLI18n::gi()->{'tradoria_config_account__legend__account'} = 'Login Details';
MLI18n::gi()->{'tradoria_config_account__legend__tabident'} = '';
MLI18n::gi()->{'tradoria_config_account__field__tabident__label'} = '{#i18n:ML_LABEL_TAB_IDENT#}';
MLI18n::gi()->{'tradoria_config_account__field__tabident__hint'} = '';
MLI18n::gi()->{'tradoria_config_account__field__tabident__help'} = '{#i18n:ML_TEXT_TAB_IDENT#}';
MLI18n::gi()->{'tradoria_config_account__field__mpusername__label'} = 'Customer Number';
MLI18n::gi()->{'tradoria_config_account__field__mpusername__hint'} = '';
MLI18n::gi()->{'tradoria_config_account__field__mppassword__label'} = 'Password';
MLI18n::gi()->{'tradoria_config_account__field__mppassword__hint'} = '';
MLI18n::gi()->{'tradoria_config_account__field__apikey__label'} = 'Rakuten API Key';
MLI18n::gi()->{'tradoria_config_account__field__apikey__hint'} = '';
MLI18n::gi()->{'tradoria_config_prepare__legend__prepare'} = 'Prepare Items';
MLI18n::gi()->{'tradoria_config_prepare__legend__upload'} = 'Upload items: Presets';
MLI18n::gi()->{'tradoria_config_prepare__field__prepare.status__label'} = 'Status Filter';
MLI18n::gi()->{'tradoria_config_prepare__field__prepare.status__valuehint'} = 'Only transfer active items';
MLI18n::gi()->{'tradoria_config_prepare__field__lang__label'} = 'Item Description';
MLI18n::gi()->{'tradoria_config_prepare__field__catmatch.mpshopcats__label'} = 'Own Categories';
MLI18n::gi()->{'tradoria_config_prepare__field__catmatch.mpshopcats__valuehint'} = 'Use the categories from your Shop as your Rakuten categories';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.status__label'} = 'Status Filter';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.status__valuehint'} = 'Only transfer active items';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.quantity__label'} = 'Inventory Item Count';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.quantity__help'} = 'Please enter how much of the inventory should be available on the Marketplace.<br/>
                        <br/>
You can change the individual item count directly under ‘Upload’. In this case it is recommended that you turn off automatic<br/>
synchronization under ‘Synchronization of Inventory’ > ‘Stock Sync to Marketplace’.<br/>
                        <br/>
To avoid overselling, you can activate ‘Transfer shop inventory minus value from the right field’.
                        <br/>
<strong>Example:</strong> Setting the value at 2 gives &#8594; Shop inventory: 10 &#8594; Rakuten*** inventory: 8<br/>
                        <br/>
                        <strong>Please note:</strong>If you want to set an inventory count for an item in the Marketplace to ‘0’, which is already set as Inactive in the Shop, independent of the actual inventory count, please proceed as follows:<br/>
                        <ul>
                        <li>’Synchronize Inventory”> Set “Stock Sync to Marketplace” to “Automatic Synchronization with CronJob”</li>
                        <li>”Global Configuration” > “Product Status” > Activate setting “If product status is inactive, treat inventory count as 0”</li>
                        </ul>';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__label'} = 'Lead Time in Days';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__help'} = 'Please enter the time period (in days) from the receipt of an order and the sending of the item. If no value is entered, the lead time will be set at 1-2 working days. Use this field if the lead time is more that 2 working days.';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__0'} = 'Immediate delivery (delivery time 1-4 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__3'} = 'Ready to ship in 3 work days (delivery time 4-6 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__5'} = 'Ready to ship in 5 work days (delivery time 6-8 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__7'} = 'Ready to ship in 7 work days (delivery time 8-10 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__10'} = 'Ready to ship in 10 work days (delivery time 10-15 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__15'} = 'Ready to ship in 15 work days (delivery time 15-20 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__20'} = 'Ready to ship in 20 work days (delivery time 20-30 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__30'} = 'Ready to ship in 30 work days (delivery time 30-40 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__40'} = 'Ready to ship in 40 work days (delivery time 40-50 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__50'} = 'Ready to ship in 50 work days (delivery time 50-60 work days)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.leadtimetoship__values__60'} = 'Ready to ship in 60 work days (delivery time longer than 3 months)';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.manufacturerfallback__label'} = 'Alternative Manufacturer';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.manufacturerfallback__help'} = 'If a product has no manufacturer assigned, the manufacturer entered here will be used.';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__label'} = 'Tax Class<br> Category Matching';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__help'} = 'Shop\'s tax class assigned by Rakuten***';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__matching__titlesrc'} = '';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__matching__titledst'} = '';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Standard'} = 'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Standard';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Reduced'} = 'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Reduced';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Free'} = 'tradoria_config_prepare__field__checkin.taxmatching__matching__labelsdst__Free';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.shippinggroup__label'} = 'Shipping Cost Group';
MLI18n::gi()->{'tradoria_config_prepare__field__checkin.shippinggroup__help'} = 'The shipping cost group can be set on Rakuten under <a href="https://merchants.rakuten.de/office/shipping/index" target="_blank" class="ml-js-noBlockUi">
			Administration &gt; Shipping***</a> (Standard: 1).';
MLI18n::gi()->{'tradoria_config_prepare__field__imagesize__label'} = 'Image Size';
MLI18n::gi()->{'tradoria_config_prepare__field__imagesize__help'} = '<p>Please enter the pixel width for the image as should appear on the Marketplace. The height will be automatically matched based on the original aspect ratio.</p>
<p>The source files will be processed from the image folder {#setting:sSourceImagePath#}, and will be stored in the folder {#setting:sImagePath#} with the selected pixel width for use on the Marketplace.</p>';
MLI18n::gi()->{'tradoria_config_prepare__field__imagesize__hint'} = 'Saved under: {#setting:sImagePath#}';
MLI18n::gi()->{'tradoria_config_price__legend__price'} = 'Price Calculation';
MLI18n::gi()->{'tradoria_config_price__field__price__label'} = 'Price';
MLI18n::gi()->{'tradoria_config_price__field__price__help'} = 'Please enter a price markup or markdown, either in percentage or fixed amount. Use a minus sign (-) before the amount to denote markdown.';
MLI18n::gi()->{'tradoria_config_price__field__price.addkind__label'} = '';
MLI18n::gi()->{'tradoria_config_price__field__price.factor__label'} = '';
MLI18n::gi()->{'tradoria_config_price__field__price.signal__label'} = 'Decimal Amount';
MLI18n::gi()->{'tradoria_config_price__field__price.signal__hint'} = 'Decimal Amount';
MLI18n::gi()->{'tradoria_config_price__field__price.signal__help'} = 'This textfield shows the decimal value that will appear in the item price on Rakuten.<br/><br/>
                <strong>Example:</strong> <br />
Value in textfeld: 99 <br />
                Original price: 5.58 <br />
                Final amount: 5.99 <br /><br />
This function is useful when marking the price up or down***. <br/>
Leave this field empty if you do not wish to set any decimal amount. <br/>
The format requires a maximum of 2 numbers.';
MLI18n::gi()->{'tradoria_config_price__field__priceoptions__label'} = 'Price Options';
MLI18n::gi()->{'tradoria_config_price__field__priceoptions__help'} = '{#i18n:configform_price_field_priceoptions_help#}';
MLI18n::gi()->{'tradoria_config_price__field__price.group__label'} = '';
MLI18n::gi()->{'tradoria_config_price__field__price.usespecialoffer__label'} = 'Use special offer prices';
MLI18n::gi()->{'tradoria_config_price__field__price.usespecialoffer__hint'} = '';
MLI18n::gi()->{'tradoria_config_price__field__exchangerate_update__label'} = 'Exchange Rate';
MLI18n::gi()->{'tradoria_config_price__field__exchangerate_update__valuehint'} = 'Automatically update exchange rate';
MLI18n::gi()->{'tradoria_config_price__field__exchangerate_update__help'} = 'Active: If the Shop currency differs from the Marketplace currency, the price will be automatically calculated based on the Yahoo Finance exchange rate at the time.<br /><br />
				<b>Liability Notice:</b> RedGecko GmbH takes no responsibility for the accuracy of exchange rates. Please verify this rate in your Rakuten account. ';
MLI18n::gi()->{'tradoria_config_price__field__exchangerate_update__alert'} = '{#i18n:form_config_orderimport_exchangerate_update_alert#}';
MLI18n::gi()->{'tradoria_config_sync__legend__sync'} = 'Inventory Synchronization';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.tomarketplace__label'} = 'Stock Sync to Marketplace';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.tomarketplace__hint'} = '';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.tomarketplace__help'} = '<p>
                    Current Rakuten stock will be synchronized with shop stock every 4 hours, beginning at 0.00am (with ***, depending on configuration).<br>Values will be transferred from the database, including the changes that occur through an ERP or similar.<br><br>
Manual comparison can be activated by clicking the corresponding button in the magnalister header (left of the shopping cart).<br><br>
Additionally, you can activate the stock comparison through CronJon (flat tariff*** - maximum every 4 hours) with the link:<br>
            <i>{#setting:sSyncInventoryUrl#}</i><br>
Some CronJob requests may be blocked, if they are made through customers not on the flat tariff*** or if the request is made more than once every 4 hours. 
<br><br>
                    <b>Note:</b> The settings in ‘Configuration’, ‘Adjusting Procedure’ and ‘Inventory Item Count’ will be taken into account.</p>';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.frommarketplace__label'} = 'Stock Sync from Marketplace';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.frommarketplace__hint'} = '';
MLI18n::gi()->{'tradoria_config_sync__field__stocksync.frommarketplace__help'} = 'If, for example, an item is purchased 3 times on Rakuten, the Shop inventory will be reduced by 3.<br /><br />
<strong>Important:</strong>This function will only work if you have Order Imports activated!';
MLI18n::gi()->{'tradoria_config_sync__field__inventorysync.price__label'} = 'Item Price';
MLI18n::gi()->{'tradoria_config_sync__field__inventorysync.price__hint'} = '';
MLI18n::gi()->{'tradoria_config_sync__field__inventorysync.price__help'} = '<p>
                    Current Rakuten prices will be synchronized with Shop prices every 4 hours, beginning at 0.00am (with ***, depending on configuration).<br>Values will be transferred from the database, including the changes that occur through an ERP or similar.<br><br>
                    <b>Note:</b> The settings in ‘Configuration’, ‘Adjusting Procedure’ and ‘Inventory Item Count’ will be taken into account.</p>';
MLI18n::gi()->{'tradoria_config_orderimport__legend__importactive'} = 'Order Import';
MLI18n::gi()->{'tradoria_config_orderimport__legend__mwst'} = 'VAT';
MLI18n::gi()->{'tradoria_config_orderimport__legend__orderstatus'} = 'Order Status Synchronization between Shop and Rakuten';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.sync__label'} = 'Status Synchronization';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.sync__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.sync__help'} = ' <dl>
                    <dt>Automatic Synchronization via CronJob (recommended)</dt>
                    <dd>
                        The function ‘Automatic Synchronization with CronJob’ transfers the current Sent Status to Rakuten every 2 hours.<br/>
                        The status values from the database will be checked and transferred, including when the changes are only made to the database, for example, with an ERP. <br/><br/>
To do a manual comparison, which allows you to edit the order directly in the web shop, set the desired status there and then click ‘refresh’.<br/>
                       Click the button in the magnalister header (left of the shopping cart) to transfer the status immediately.<br/><br/>
Additionally you can activate the Order Status Comparison through CronJob (flat tariff*** - maximum every 4 hours) with the link: <br/><br/>
    <i>{#setting:sSyncOrderStatusUrl#}</i><br/><br/>
Some CronJob requests may be blocked, if they are made through customers not on the flat tariff*** or if the request is made more than once every 4 hours. 
</dd>
</dl>
';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.shipped__label'} = 'Confirm Shipping With';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.shipped__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.shipped__help'} = '     Please set the Shop Status that should trigger the ‘Shipping Confirmed’ status on Rakuten.';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.carrier.default__label'} = 'Carrier';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.carrier.default__help'} = 'tradoria_config_orderimport__field__orderstatus.carrier.default__help';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.canceled__label'} = 'Cancel Order With';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.canceled__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.canceled__help'} = 'Please set the Shop Status that should trigger the ‘Order Cancelled’ status on Rakuten.<br/><br/>
Note: Part cancellations are not possible here. This function will cancel the complete order and refund the buyer.
';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderimport.shop__label'} = '{#i18n:form_config_orderimport_shop_lable#}';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderimport.shop__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderimport.shop__help'} = '{#i18n:form_config_orderimport_shop_help#}';
MLI18n::gi()->{'tradoria_config_orderimport__field__mwst.fallback__label'} = 'VAT for non-Shop items***';
MLI18n::gi()->{'tradoria_config_orderimport__field__mwst.fallback__hint'} = 'The tax rate to apply to non-Shop items on order imports, in %. ';
MLI18n::gi()->{'tradoria_config_orderimport__field__mwst.fallback__help'} = 'If an item is not entered via magnalister, the VAT cannot be calculated. <br />
The percentage value entered here will be taken as the VAT rate for all orders imported to Rakuten. ';
MLI18n::gi()->{'tradoria_config_orderimport__field__importactive__label'} = 'Activate Import';
MLI18n::gi()->{'tradoria_config_orderimport__field__importactive__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__importactive__help'} = 'Import orders from the Marketplace? <br/><br/>When activated, orders will be automatically imported every hour.<br><br>
You can adjust the automatic import times under<br> 
‘magnalister admin’ > ‘Global Configurations’ > ‘Order Calls’.<br><br>
Manual import can be activated by clicking the corresponding button in the magnalister header (left of the shopping cart).<br><br>Additionally, you can activate the stock comparison through CronJon (flat tariff*** - maximum every 4 hours) with the link:<br>
            <i>{#setting:sImportOrdersUrl#}</i><br>
Some CronJob requests may be blocked, if they are made through customers not on the flat tariff*** or if the request is made more than once every 4 hours. ';
MLI18n::gi()->{'tradoria_config_orderimport__field__import__label'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__import__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__preimport.start__label'} = 'Start import from';
MLI18n::gi()->{'tradoria_config_orderimport__field__preimport.start__hint'} = 'Start Date';
MLI18n::gi()->{'tradoria_config_orderimport__field__preimport.start__help'} = 'The date from which orders will start being imported. Please note that it is not possible to set this too far in the past, as the data only remains available on Rakuten for a few weeks.***';
MLI18n::gi()->{'tradoria_config_orderimport__field__customergroup__label'} = 'Customer Group';
MLI18n::gi()->{'tradoria_config_orderimport__field__customergroup__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__customergroup__help'} = 'The customer group that customers from new orders should be sorted into.';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.open__label'} = 'Order Status in Shop';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.open__hint'} = '';
MLI18n::gi()->{'tradoria_config_orderimport__field__orderstatus.open__help'} = 'The status that should be transferred automatically to the Shop after a new order on Rakuten. <br />
If you are using a connected dunning process***, it is recommended to set the Order Status to ‘Paid’ (‘Configuration’ > ‘Order Status’).';
MLI18n::gi()->{'tradoria_config_orderimport__field__order.importonlypaid__label'} = 'Import Only Paid Items';
MLI18n::gi()->{'tradoria_config_orderimport__field__order.importonlypaid__hint'} = '';
MLI18n::gi()->{'tradoria_config_emailtemplate__legend__mail'} = 'Promotion Email Template';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.send__label'} = 'Send Email?';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.send__help'} = 'Should an email be sent from your Shop to customers, to promote your Shop?';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.originator.name__label'} = 'Sender Name';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.originator.adress__label'} = 'Sender Email Address';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.subject__label'} = 'Subject';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.content__label'} = 'Email Content';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.content__hint'} = 'List of available placeholders for Subject and Content:
<dl>
                    <dt>#FIRSTNAME#</dt>
                    <dd>Buyer\'s first name</dd>
                    <dt>#LASTNAME#</dt>
                    <dd>Buyer\'s last name</dd>
                    <dt>#EMAIL#</dt>
                    <dd>Buyer\'s email address</dd>
                    <dt>#PASSWORD#</dt>
                    <dd>Buyer’s password for logging in to your Shop. Only for customers that are automatically assigned passwords – otherwise the placeholder will be replaced with ‘(as known)’***.</dd>
                    <dt>#ORDERSUMMARY#</dt>
                    <dd>Summary of the purchased items. Should be written on a separate line. <br/><i>Cannot be used in the Subject!</i>
                    </dd>
                    <dt>#MARKETPLACE#</dt>
                    <dd>Marketplace Name</dd>
                    <dt>#SHOPURL#</dt>
                    <dd>Your Shop URL</dd>
                    <dt>#ORIGINATOR#</dt>
                    <dd>Sender Name</dd>
                </dl>';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.copy__label'} = 'Copy to Sender';
MLI18n::gi()->{'tradoria_config_emailtemplate__field__mail.copy__help'} = 'A copy will be sent to the sender email address.';
