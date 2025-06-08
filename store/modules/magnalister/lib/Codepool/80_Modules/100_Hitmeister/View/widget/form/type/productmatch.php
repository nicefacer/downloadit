<?php class_exists('ML', false) or die(); ?>
<input type="hidden" name="matching_nextpage" value="<?= $this->oPrepareHelper->currentPage == $this->oPrepareHelper->totalPages ? 'null' : $this->oPrepareHelper->currentPage + 1 ?>" />
<input type="hidden" name="matching_totalpages" value="<?= $this->oPrepareHelper->totalPages ?>" />
<div id="productDetailContainer" class="dialog2" title="<?= ML_LABEL_DETAILS ?>"></div>
<?php foreach ($this->oPrepareHelper->currentChunk as $aProduct) : ?>
<table class="matching">
    <tbody class="product">
        <tr>
            <th colspan="5">
                <div class="title">
                    <span class="darker"><?= ML_LABEL_SHOP_TITLE ?>:</span>
                         <?= $aProduct['Title'] ?>&nbsp;&nbsp;
                    <span>
                        [<span style="color: #ddd;"><?= ML_LABEL_ARTICLE_NUMBER ?></span>: <?= $aProduct['Model'] ?>,
                        <span style="color: #ddd;"><?= ML_LABEL_SHOP_PRICE_BRUTTO ?></span>: <?= $aProduct['Price'] ?>]
                    </span>
                </div>
                <input type="hidden" name="match[<?= $aProduct['Id'] ?>]" value="false">
                <input type="hidden" name="model[<?= $aProduct['Id'] ?>]" value="<?= $aProduct['Model'] ?>">
                <div id="productDetails_<?= $aProduct['Id'] ?>" class="productDescBtn" title="<?= ML_LABEL_DETAILS ?>"><?= ML_LABEL_DETAILS ?></div>
            </th>
        </tr>
    </tbody>
    <tbody class="headline">
        <tr>
            <th class="input"><?= ML_LABEL_CHOOSE ?></th>
            <th class="title"><?= MLI18n::gi()->hitmeister_label_title ?></th>
            <th class="productGroup"><?= MLI18n::gi()->hitmeister_category ?></th>
            <th class="asin"><?= MLI18n::gi()->hitmeister_label_item_id ?></th>
        </tr>
    </tbody>
    <tbody class="options" id="matchingResults_<?= $aProduct['Id'] ?>">
        <?= $this->getSearchResultsHtml($aProduct) ?>
    </tbody>
    <tbody class="func">
        <tr>
            <td colspan="5">
                <div><?= MLI18n::gi()->hitmeister_search_by_title ?>: <input type="text" id="newSearch_<?= $aProduct['Id'] ?>"> <input type="button" value="OK" id="newSearchGo_<?= $aProduct['Id'] ?>"></div>
                <div><?= MLI18n::gi()->hitmeister_search_by_ean ?>: <input type="text" id="newEAN_<?= $aProduct['Id'] ?>"> <input type="button" value="OK" id="newEANGo_<?= $aProduct['Id'] ?>"></div>
            </td>
        </tr>
    </tbody>
    <tr class="spacer"><td colspan="4"></td></tr>
    <script type="text/javascript">/*<![CDATA[*/
        var productDetailJson_<?= $aProduct['Id'] ?> = <?php echo $this->renderDetailView($aProduct); ?>

        jqml('#productDetails_<?= $aProduct['Id'] ?>').click(function() {
            myConsole.log(productDetailJson_<?= $aProduct['Id'] ?>);
            jqml('#productDetailContainer').html(productDetailJson_<?= $aProduct['Id'] ?>.content).jDialog({
                width: "75%",
                title: productDetailJson_<?= $aProduct['Id'] ?>.title
            });
        });
        jqml('#newSearchGo_<?= $aProduct['Id'] ?>').click(function() {
            newSearch = jqml('#newSearch_<?= $aProduct['Id'] ?>').val();
            if (jqml.trim(newSearch) != '') {
                jqml.blockUI({ message: blockUIMessage, css: blockUICSS });
                myConsole.log(newSearch);
                jqml.ajax({
                    type: 'POST',
                    url: '<?php echo $this->getCurrentUrl() ?>',

                    data: ({
                        'ml[method]': 'ItemSearchByTitle',
                        'ml[ajax]': true,
                        'productID': <?= $aProduct['Id'] ?>,
                        'search': newSearch
                    }),
                    dataType: "json",
                    success: function(data) {
                        jqml('#matchingResults_<?= $aProduct['Id'] ?>').html(data[0]);
                        if (function_exists("initRadioButtons")) {
                            initRadioButtons();
                        }
                        jqml.unblockUI();
                    },
                    error: function() {
                        jqml.unblockUI();
                    }
                });
            }
        });
        jqml('#newSearch_<?= $aProduct['Id'] ?>').keypress(function(event) {
            if (event.keyCode == '13') {
                event.preventDefault();
                jqml('#newSearchGo_<?= $aProduct['Id'] ?>').click();
            }
        });
        jqml('#newEANGo_<?= $aProduct['Id'] ?>').click(function() {
            newEAN = jqml('#newEAN_<?= $aProduct['Id'] ?>').val();
            if (jqml.trim(newEAN) != '') {
                myConsole.log(newEAN);
                jqml.blockUI({ message: blockUIMessage, css: blockUICSS });
                jqml.ajax({
                    type: 'POST',
                    url: '<?php echo $this->getCurrentUrl() ?>',
                    data: ({
                        'ml[method]': 'ItemSearchByEAN',
                        'ml[ajax]': true,
                        'productID': <?= $aProduct['Id'] ?>,
                        'ean': newEAN
                    }),
                    dataType: "json",
                    success: function(data) {
                        jqml('#matchingResults_<?= $aProduct['Id'] ?>').html(data[0]);
                        if (function_exists("initRadioButtons")) {
                            initRadioButtons();
                        }
                        jqml.unblockUI();
                    },
                    error: function() {
                        jqml.unblockUI();
                    }
                });
            }
        });
        jqml('#newEAN_<?= $aProduct['Id'] ?>').keypress(function(event) {
            if (event.keyCode == '13') {
                event.preventDefault();
                jqml('#newEANGo_<?= $aProduct['Id'] ?>').click();
            }
        });
    /*]]>*/
    </script>
</table>
<?php endforeach ?>