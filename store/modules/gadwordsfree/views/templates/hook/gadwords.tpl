
{addJsDef GADWORDS_CONVERSION_TRACKING_ID=$GADWORDS_CONVERSION_TRACKING_ID|escape:'html':'UTF-8'}
{addJsDef GADWORDS_CONVERSION_TRACKING_LABEL=$GADWORDS_CONVERSION_TRACKING_LABEL|escape:'html':'UTF-8'}

<!-- Google Code for conversion  module google adwords tracking -->
{literal}
<script type="text/javascript">
    /* <![CDATA[ */
    var google_conversion_id = {/literal}{$GADWORDS_CONVERSION_TRACKING_ID|intval}{literal};
    var google_conversion_language = "{/literal}{if !empty($LANG)}{$LANG|escape:'htmlall':'UTF-8'}{else}en{/if}{literal}";
    var google_conversion_format = "3";
    var google_conversion_color = "ffffff";
    var google_conversion_label = "{/literal}{$GADWORDS_CONVERSION_TRACKING_LABEL|escape:'htmlall':'UTF-8'}{literal}";
    var google_conversion_currency = "{/literal}{$CURRENCY|escape:'htmlall':'UTF-8'}{literal}";
    var google_conversion_value = {/literal}{if !empty($TOTAL_ORDER)}{$TOTAL_ORDER|floatval}{else}1.000000{/if}{literal};
    var google_remarketing_only = false;
    /* ]]> */
</script>
{/literal}

{literal}
    <script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js">
    </script>
    <noscript>
        <div style="display:inline;">
            <img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/{/literal}{$GADWORDS_CONVERSION_TRACKING_ID|intval}{literal}/?value={/literal}{if !empty($TOTAL_ORDER)}{$TOTAL_ORDER|floatval}{else}1.000000{/if}{if !empty($CURRENCY)}&amp;currency_code={$CURRENCY|escape:'htmlall':'UTF-8'}{/if}{literal}&amp;label={/literal}{$GADWORDS_CONVERSION_TRACKING_LABEL|escape:'htmlall':'UTF-8'}{literal}&amp;guid=ON&amp;script=0"/>
        </div>
    </noscript>
{/literal}
