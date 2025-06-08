jQuery.fn.extend({
    insertAtCaret: function(myValue){
        return this.each(function(i) {
            if (document.selection) {
                this.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
                this.focus();
            }
            else if (this.selectionStart || this.selectionStart == '0') {
                var startPos = this.selectionStart;
                var endPos = this.selectionEnd;
                var scrollTop = this.scrollTop;
                this.value = this.value.substring(0, startPos)+myValue+this.value.substring(endPos,this.value.length);
                this.focus();
                this.selectionStart = startPos + myValue.length;
                this.selectionEnd = startPos + myValue.length;
                this.scrollTop = scrollTop;
            } else {
                this.value += myValue;
                this.focus();
            }
        })
    }
});


function ProfileForm(settings) {

    var htmlHelper = HtmlHelper();

    {
        jQuery("#ebay_site").change(marketplaceOnChange);
        jQuery("#auction_type").change(auctionTypeOnChange);
        jQuery("#ebay_account").change(accountOnChange);
        jQuery("#item_qty_mode").change(itemQtyModeOnChange);

        jQuery("#item_description_mode").change(itemDescriptionModeChange);
        jQuery("#item_description_custom_variable_insert").click(itemDescriptionVariableInsertClick);

        if (htmlHelper.getCookie("prestabay-tiny-mce-enable") == 0) {
            jQuery("#tinymce-custom-description-toggle").toggle(function () {
                tinymce_add("item_description_custom");
                htmlHelper.setCookie("prestabay-tiny-mce-enable", 1);
            }, function(){
                tinymce_remove("item_description_custom");
                htmlHelper.setCookie("prestabay-tiny-mce-enable", 0);
            });
        } else {
            if (typeof tinyMCE !== 'undefined') {
                tinymce_add("item_description_custom");
            }
            jQuery("#tinymce-custom-description-toggle").toggle(function () {
                tinymce_remove("item_description_custom");
                htmlHelper.setCookie("prestabay-tiny-mce-enable", 0);
            }, function(){
                tinymce_add("item_description_custom");
                htmlHelper.setCookie("prestabay-tiny-mce-enable", 1);
            });
        }


        // -- This options only for edit already created template
        jQuery("#returns_accepted").change(returnsAcceptedChange);
        jQuery("#paymentBox_PayPal").change(paymentPaypalOnChange);
        jQuery("#payment_list input").change(paymentListOnChange);
        jQuery("#paymentBox_COD").change(paymentCODOnChange);
        jQuery(".shipping_checkbox").click(shippingCheckboxOnClick);

        jQuery("#new-local-shipping").click(shippingNewDomesticOnClick);
        jQuery(".remove-local-shipping-row").click(shippingRemoveDomesticRowOnClick);
        jQuery("#new-int-shipping").click(shippingNewIntOnClick);
        jQuery(".remove-int-shipping-row").click(shippingRemoveIntRowOnClick);

        jQuery("#new-exclude-location").click(shippingNewExcludeOnClick);
        jQuery(".remove-exclude-location").click(removeExcludeLocationOnClick);

        jQuery('.shipping-mode-local').change(shippingModeOnChange);
        jQuery('.shipping-mode-int').change(shippingModeOnChange);


        jQuery("#change_primary_category").click(categoryPrimaryChangeOnClick);
        jQuery("#change_secondary_category").click(categorySecondaryChangeOnClick);
        jQuery("#reset_primary_category").click(categoryPrimaryResetOnClick);
        jQuery("#reset_secondary_category").click(categorySecondaryResetOnClick);
        jQuery('#ebay_secondary_category_0').change(secondaryCategoryOnChange);

        jQuery('#ebay_category_mode').change(changeCategoryMode);
//        jQuery('#ebay_category_mode').change();


        jQuery(".ebay_store_category").change(changeEbayStoreCategory);
        jQuery(".ebay_store_category").change();

        jQuery("#ebay_store_mode").change(changeEbayStoreMappingMode);
        jQuery("#ebay_store_mode").change();

        jQuery(".price-option-select-box").change(priceOptionSelectOnChange);

        jQuery(".product_specific_select").change(productSpecificSelectOnChange);

        jQuery("#new-local-shipping").click();
        jQuery("#new-int-shipping").click();

        jQuery("#shipping_local_type").change(shippingTypeModeOnChange);
        jQuery("#shipping_int_type").change(shippingTypeModeOnChange);

        jQuery("#shipping_calculated_depth").change(calculatedShippingParamsOnChange);
        jQuery("#shipping_calculated_length").change(calculatedShippingParamsOnChange);
        jQuery("#shipping_calculated_width").change(calculatedShippingParamsOnChange);
        jQuery("#shipping_calculated_weight").change(calculatedShippingParamsOnChange);

        jQuery("#best_offer_enabled").change(bestOfferParamOnChange);
        jQuery("#unit_include").change(unitIncludeOnChange);

        jQuery("#gift_icon").change(giftIconOnChange);
        jQuery("#use_multivariation").change(variationModeOnChange)

        // Form validator
        var validator = jQuery("#profileForm").submit(function() {
            // update underlying textarea before submit validation
            if (tinyMCE) {
                tinyMCE.triggerSave();
            }
        }).validate({
            submitHandler: function(form) {
                form.submit();
            },

            showErrors: function(errorMap, errorList) {
                this.defaultShowErrors();


            },
            errorPlacement: function(error, element) {
                var $errorContainer =  jQuery(".error-container[for='"+element.attr("name")+"']");
                if  ($errorContainer.size() > 0) {
                    error.appendTo($errorContainer);
                } else {
                    error.insertAfter(element);
                }
            },

            onfocusout: false,
            onkeyup: false,
            onclick: false,
            rules: {
                profile_name: "required",
                ebay_account: "required",
                ebay_site: "required",
                ebay_category_mode: "required",
                ebay_primary_category_value: {
                    required: function (element) {
                        var isRequired = jQuery("#ebay_site option:selected").val() != "" && jQuery("#ebay_category_mode option:selected").val() == settings.EBAY_CATEGORY_MODE_PROFILE;
                        return isRequired;
                    }
                },
                auction_type: "required",
                auction_duration: "required",

                item_title: "required",
                item_condition: {
                    required: function (element) {
                        if (jQuery("#ebay_primary_category_value").val() != "" && jQuery("#item_condition option").size() > 0 && jQuery("#ebay_category_mode option:selected").val() == settings.EBAY_CATEGORY_MODE_PROFILE) {
                            return true;
                        }
                        return false;
                    }
                },
                item_qty_value: {
                    required: function (element) {
                        return parseInt(jQuery("#item_qty_mode option:selected").val()) == settings.ITEM_QTY_MODE_CUSTOM
                            || parseInt(jQuery("#item_qty_mode option:selected").val()) == settings.ITEM_QTY_MODE_NOT_MORE_THAT
                            || parseInt(jQuery("#item_qty_mode option:selected").val()) == settings.ITEM_QTY_MODE_RESERVED_VALUE;
                    },
                    number: true,
                    min: 1
                },
                item_description_mode: "required",
                item_description_custom: {
                    required: function (element) {
                        return parseInt(jQuery("#item_description_mode").val()) == settings.ITEM_DESCRIPTION_MODE_CUSTOM;
                    }
                },

                item_currency: "required",

                price_start: {
                    required: function (element) {
                        return jQuery("#auction_type option:selected").val() != "";
                    }
                },
                price_start_multiply: {
                    required: true,
                    number: true
                },
                price_start_custom: {
                    required: function (element) {
                        return parseInt(jQuery("#price_start option:selected").val()) == settings.PRICE_MODE_CUSTOM;
                    },
                    number: true
                },

                price_reserve: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE;
                    }
                },
                price_reserve_multiply: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE;
                    },
                    number: true
                },
                price_reserve_custom: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE && parseInt(jQuery("#price_reserve option:selected").val()) == settings.PRICE_MODE_CUSTOM;
                    },
                    number: true
                },

                price_buynow: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE;
                    }
                },
                price_buynow_multiply: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE;
                    },
                    number: true
                },
                price_buynow_custom: {
                    required: function (element) {
                        return parseInt(jQuery("#auction_type option:selected").val()) == settings.AUCTION_TYPE_CHINESE && parseInt(jQuery("#price_buynow option:selected").val()) == settings.PRICE_MODE_CUSTOM;
                    },
                    number: true
                },

                'paymentBox[]': {
                    required: true,
                    minlength: 1
                },
                payment_paypal_email: {
                    required: function (element) {
                        return jQuery("#paymentBox_PayPal:checked").size() > 0;
                    },
                    email: true
                },

                shipping_country: 'required',
                shipping_location: 'required',

                shipping_dispatch: {
                    required: function (element) {
                        return jQuery("#ebay_site option:selected").val() != "" && jQuery("#shipping_dispatch option").size() > 0;
                    }
                },

                'internation_ship_to_location[]': {
                    required: function (element) {
                        return jQuery("#shipping_international_list input[type=checkbox]:checked").size() > 0
                    }
                },

                'returns_accepted': {
                    required: function (element) {
                        return jQuery("#ebay_site option:selected").val() != "" && jQuery("#returns_accepted option").size() > 0;
                    }
                }

            },
            errorElement: 'div',
            errorClass: 'invalid',

            messages: {
                'paymentBox[]': {
                    required: "Please select at list one Payment method"
                },
                'internation_ship_to_location[]': {
                    required: "Please select at list one Location"
                }
            }
        });
        validator.focusInvalid = function() {
            // put focus on tinymce on submit validation
            if( this.settings.focusInvalid ) {
                try {
                    var className = ".tab-page";
                    if (isPS16) {
                        className = ".tab-panel";
                    }
                    var firstSelectedErrorTabId = jQuery(".invalid:not(div):eq(0)").parents(className).attr("id");
                    if (firstSelectedErrorTabId != null) {
                        jQuery("a[href='#"+firstSelectedErrorTabId+"']").click();
                    }


                    var toFocus = jQuery(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
                    if (toFocus.is("textarea") && tinyMCE) {
                        tinyMCE.get(toFocus.attr("id")).focus();
                    } else {
                        toFocus.filter(":visible").focus();
                    }
                } catch(e) {
                // ignore IE throwing errors when focusing hidden elements
                }
            }
        }

    }

    //####################################################################
    // Marketplace part

    function marketplaceOnChange() {
        if (jQuery(this).val() == "") {
            marketplaceHideDependingNodes();
        } else {
            marketplaceLoadDependingNodes(jQuery(this).val());
        }
    }

    function marketplaceHideDependingNodes() {

        // Primary category
        jQuery("#ebay_primary_category_name").attr("value", "");
        jQuery("#ebay_primary_category_value").attr("value", "");
        jQuery("#primary_category_label").html("");
        var primaryContainer = jQuery("#ebay_primary_category_value").parent();
        primaryContainer.find(".hide-notice").show();
        primaryContainer.find(".ebay_primary_category").remove();
        primaryContainer.find(".hide-notice:eq(0)").after('<select class="ebay_primary_category  col-lg-5" level="0" id="ebay_primary_category_0"></select>');
        jQuery("#ebay_primary_category_0").hide();
        primaryContainer.find(".button, br").remove();

        // Secondary category
        jQuery("#ebay_secondary_category_name").attr("value", "");
        jQuery("#ebay_secondary_category_value").attr("value", "");
        jQuery("#secondary_category_label").html("");
        var secondaryContainer = jQuery("#ebay_secondary_category_value").parent();
        secondaryContainer.find(".hide-notice").show();
        secondaryContainer.find(".ebay_secondary_category").remove();
        secondaryContainer.find(".hide-notice:eq(0)").after('<select class="ebay_secondary_category  col-lg-5" level="0" id="ebay_secondary_category_0"></select>');
        jQuery("#ebay_secondary_category_0").hide();
        secondaryContainer.find(".button, br").remove();
        jQuery("#cross_border_trade_row").hide();
        jQuery("#cross_border_trade").val(0);

        // Category mapping options
        jQuery("#ebay_category_mode").empty().hide().prev().show();

        // Item Condition reset
        jQuery("#item_condition").empty().hide().prev().show();
        jQuery("#item_condition_description").hide().prev().show();

        // Product Specifics reset
        jQuery("#product_specifics").empty().hide();
        jQuery("#product_specifics_empty").hide();
        jQuery("#product_specifics_not_select").show();
        jQuery("#attribute_set_id").val(0);
        jQuery("#product_specifics_attribute").empty().hide();

        // Payment Methods Reset
        jQuery("#payment_list tbody").empty();
        jQuery("#payment_list").hide().prev().show();
        jQuery("#payment_paypal_email").parent().parent().hide();
        
        // Hide Required imediate payment
        jQuery("#autopay_row").hide();
        jQuery("#autopay").val(0);

        jQuery("#payment_instruction_row").hide();

        // Shipping Reset
        jQuery("#shipping_dispatch").empty().hide().prev().show();

        jQuery("#shipping_local_list tbody").empty();
        jQuery("#shipping_local_list").hide().prev().show();
        
        jQuery("#shipping_international_list tbody").empty();
        jQuery("#shipping_international_list").hide().prev().show();
        
        jQuery(".new-shipping-container").hide();

        jQuery(".insurance_international_row").hide();
        jQuery(".insurance_row").hide();
        jQuery("#insurance_option").val("");
        jQuery("#insurance_international_option").val("");

        jQuery("#global-shipping-row").hide();
        jQuery("#global_shipping").val(0);

        // hide Int location list
        jQuery("#shipping-allowed-location-row").hide();
        jQuery("#shipping-allowed-location-box").empty();
        // Hide Int Shipping Exclude location
        jQuery("#exclude-location-row").hide();
        jQuery("#exclude-location-selected-list").empty();
        jQuery("#exclude-location-select-container").empty();

        // Remove all connected to Calculated Shipping
        jQuery("#local_shipping_mode_row").hide();
        jQuery("#int_shipping_mode_row").hide();
        jQuery("#shipping_local_type").val(settings.SHIPPING_TYPE_FLAT);
        jQuery("#shipping_int_type").val(settings.SHIPPING_TYPE_FLAT);
        jQuery("#calculated_shipping_row").hide();

        // Policy Reset
        jQuery("#returns_accepted").empty().hide().prev().show();
        jQuery(".return-policy-accepted").hide();
        jQuery("#refund").empty();
        jQuery("#returns_within").empty();
        jQuery("#shipping_cost_paid_by").empty();
        jQuery("#restock_fee").empty();


    }

    function marketplaceLoadDependingNodes(marketplaceId) {
        showLoader();
        marketplaceHideDependingNodes();
        jQuery.ajax({
            url: settings.marketplaceAjaxInfoUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: marketplaceId
            },
            success: function(result) {
                if (result.success == true) {

                    categoryInitFirstChange(result.data.parent_categories, 'all');
                    jQuery("#ebay_category_mode").html(result.data.categoryMappingHtml);
                    jQuery("#ebay_category_mode").show().prev().hide();
                    jQuery("#ebay_category_mode").val(settings.EBAY_CATEGORY_MODE_PROFILE);


                    htmlHelper.valuesToSelect(jQuery("#shipping_dispatch"), result.data.dispatch, true);

                    // Policy
                    jQuery(".return-policy-accepted").show();

                    htmlHelper.valuesToSelect(jQuery("#returns_accepted"), result.data.policy.returns_accepted, true );

                    htmlHelper.valuesToSelect(jQuery("#refund"), result.data.policy.refund, true);
                    htmlHelper.valuesToSelect(jQuery("#returns_within"), result.data.policy.returns_within, true);
                    htmlHelper.valuesToSelect(jQuery("#shipping_cost_paid_by"), result.data.policy.shipping_cost_paid_by, true);
                    htmlHelper.valuesToSelect(jQuery("#restock_fee"), result.data.policy.restocking_fee, true);

                    jQuery("#returns_accepted").unbind("change", returnsAcceptedChange);
                    jQuery("#returns_accepted").change(returnsAcceptedChange);
                    jQuery("#returns_accepted").change();

                    // Payment
                    htmlHelper.checkboxesToContainer(jQuery("#payment_list tbody"), result.data.payment_methods, "paymentBox");
                    jQuery("#paymentBox_PayPal").unbind("change", paymentPaypalOnChange);
                    jQuery("#paymentBox_PayPal").change(paymentPaypalOnChange);

                    jQuery("#payment_list input").unbind("change", paymentListOnChange);
                    jQuery("#payment_list input").change(paymentListOnChange);

                    jQuery("#payment_paypal_email").parent().parent().hide();

                    jQuery("#paymentBox_COD").unbind('change', paymentCODOnChange);
                    jQuery("#cod_cost_italy").parent().parent().hide();
                    jQuery("#payment_instruction_row").show();
                    if (marketplaceId == 101) {
                        // For Italy show cod cost for specific payment method
                        jQuery("#paymentBox_COD").change(paymentCODOnChange);
                    }

                    if (marketplaceId == 1 || marketplaceId == 2 || marketplaceId == 3 || marketplaceId == 205) {
                        // For US, UK, CA and Ireland show International Site Visibility
                        jQuery("#cross_border_trade_row").show();
                    }

                    // Reset data for domestic shipping
                    localShippingIndex = 0;
                    localShippingList = result.data.local_shippings;

                    jQuery("#shipping_local_list").show().prev().hide();
                    jQuery("#shipping_local_list tbody").empty();

                    jQuery("#new-local-shipping").unbind("click", shippingNewDomesticOnClick);
                    jQuery("#new-local-shipping").click(shippingNewDomesticOnClick);

                    // Reset data for international shipping
                    intShippingIndex = 0;
                    intShippingList = result.data.international_shippings;

                    // Load ship to location list
                    intLocationList = result.data.location_shipping;

                    jQuery("#shipping_international_list tbody").parent().show().prev().hide();
                    jQuery("#shipping_international_list tbody").empty();

                    jQuery("#new-int-shipping").unbind("click", shippingNewIntOnClick);
                    jQuery("#new-int-shipping").click(shippingNewIntOnClick);

                    jQuery(".new-shipping-container").show();
                    jQuery("#new-local-shipping").click(); // Adding one empty shipping
                    jQuery("#new-int-shipping").click(); // Adding one empty shipping

                    if (marketplaceId == 1 || marketplaceId == 100 || marketplaceId == 2 || marketplaceId == 210 || marketplaceId == 15) {
                        // For US, CA, CAFR, AU show Calculated Shipping
                        jQuery("#int_shipping_mode_row").show();
                        jQuery("#local_shipping_mode_row").show();
                        jQuery("#shipping_calculated_package").empty();
                        htmlHelper.valuesToSelect(jQuery("#shipping_calculated_package"), result.data.shipping_packages, true);
                        jQuery("#shipping_calculated_measurement option").show();
                        if (marketplaceId == 1) {
                            jQuery("#shipping_calculated_measurement option[value="+settings.SHIPPING_CALCULATED_MEASUREMENT_METRIC+"]").hide();
                            jQuery("#shipping_calculated_measurement").val(settings.SHIPPING_CALCULATED_MEASUREMENT_ENGLISH);
                        } else {
                            jQuery("#shipping_calculated_measurement option[value="+settings.SHIPPING_CALCULATED_MEASUREMENT_ENGLISH+"]").hide()
                            jQuery("#shipping_calculated_measurement").val(settings.SHIPPING_CALCULATED_MEASUREMENT_METRIC);
                        }
                    }

                    if (marketplaceId == 15 || marketplaceId == 71 || marketplaceId == 101) {
                        // For FR, IT, AU show Insurance option
                        jQuery(".insurance_international_row").show();
                        jQuery(".insurance_row").show();
                    }

                    if (marketplaceId == 1 || marketplaceId == 3) {
                        // For US, UK - show global shipping
                        jQuery("#global-shipping-row").show();
                    }

                    jQuery("#shipping-allowed-location-row").show();
                    htmlHelper.checkboxesToContainer(jQuery("#shipping-allowed-location-box"), intLocationList, "shipping_allowed_location");

                    // Load Int Exclude location
                    jQuery("#exclude-location-selected-list").empty();
                    jQuery("#exclude-location-select-container").empty();
                    jQuery("#exclude-location-select-container").html(result.data.exclude_location);

                    jQuery("#new-exclude-location").unbind("click", shippingNewExcludeOnClick);
                    jQuery("#new-exclude-location").click(shippingNewExcludeOnClick);

                    jQuery("#exclude-location-row").show();

                    hideLoader();
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                // log the error to the console
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);

            }
        });
    }

    //####################################################################
    // Account part

    function accountOnChange() {
        if (jQuery(this).val() == "") {
            accountHideDependingNodes();
        } else {
            accountLoadDependingNodes(jQuery(this).val());
        }
    }

    function accountHideDependingNodes() {
        // Hide content on Store Tab
        jQuery("#ebay_account_store_not_select").show();
        jQuery("#ebay_account_store_empty").hide();
        jQuery("#ebay_account_store_information").hide();
        
        jQuery("#shipping_discount_profile_id").html();
        jQuery("#int_shipping_discount_profile_id").html()
        jQuery(".shipping_discount_message").show();
        jQuery(".shipping_discount_container").hide();
    }

    function accountShowNoStoreSubscription() {
        jQuery("#ebay_account_store_empty").show();
        jQuery("#storeName").html('');
        jQuery("#storeName").attr("href", '');
        jQuery("#storeSubscription").html('');
        jQuery(".ebay_store_category").html('');
    }

    function accountLoadDependingNodes(accountId) {
        showLoader();
        accountHideDependingNodes();
        jQuery.ajax({
            url: settings.accountStoreInfoUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: accountId
            },
            success: function(result) {
                jQuery("#ebay_account_store_not_select").hide();
                if (result.success == true) {
                    if (result.no_store == true) {
                        accountShowNoStoreSubscription();
                    } else {
                        jQuery("#storeName").html(result.name);
                        jQuery("#storeName").attr("href", result.url);
                        jQuery("#storeSubscription").html(result.subscription);
                        jQuery(".ebay_store_category").html(result.categoryOptionsHtml);
                        jQuery("#ebay_account_store_information").show();

                        htmlHelper.valuesToSelect(jQuery("#shipping_discount_profile_id"), result.discountProfiles, true);
                        htmlHelper.valuesToSelect(jQuery("#int_shipping_discount_profile_id"), result.discountProfiles, true);
                        jQuery(".shipping_discount_message").hide();
                        jQuery(".shipping_discount_container").show();

                    }
                } else {
                    alert('Problem with retrive account information');
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                // log the error to the console
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);
            },
            complete: function() {
                hideLoader();
            }
        });
    }


    //####################################################################
    // Category part

    function categoryInitFirstChange(category_data, type) {
        if (type == 'primary' || type == 'all') {
            htmlHelper.valuesToSelect(jQuery("#ebay_primary_category_0"), category_data, true);
            jQuery('#ebay_primary_category_0').unbind("change", primaryCategoryOnChange);
            jQuery('#ebay_primary_category_0').change(primaryCategoryOnChange);
        }
        if (type == 'secondary' || type == 'all') {
            htmlHelper.valuesToSelect(jQuery("#ebay_secondary_category_0"), category_data, true);
            jQuery('#ebay_secondary_category_0').unbind("change", secondaryCategoryOnChange);
            jQuery('#ebay_secondary_category_0').change(secondaryCategoryOnChange);
        }


    }

    function primaryCategoryOnChange() {
        categoryOnChange('primary', this);
    }

    function secondaryCategoryOnChange() {
        categoryOnChange('secondary', this);
    }

    function changeCategoryMode() {
        var categoryMode = jQuery(this).val();
        if (categoryMode === "") {
            jQuery('.category-mode-profile-blocks').hide();
            jQuery('.category-mode-mapping-blocks').hide();
        } else if (categoryMode == settings.EBAY_CATEGORY_MODE_PROFILE) {
            jQuery('.category-mode-profile-blocks').show();
            jQuery('.category-mode-mapping-blocks').hide();
            jQuery("#ebay_site").change();
        } else {
            jQuery('.category-mode-profile-blocks').hide();
            jQuery('.category-mode-mapping-blocks').show();
        }

    }

    function changeEbayStoreCategory() {
        jQuery(this).next().html("ID: " + jQuery(this).val());
    }

    function changeEbayStoreMappingMode() {
        if (jQuery(this).val() == settings.EBAY_STORE_MODE_PROFILE) {
            jQuery('.mapping-categories-row').show();
        } else {
            jQuery('.mapping-categories-row').hide();
        }
    }

    function categoryOnChange(type, element) {
        var level = jQuery(element).attr("level");
        // Remove all categories element that go after current
        var nextLevels = parseInt(level) + 1;

        while (jQuery('#ebay_' + type + '_category_'+nextLevels).length > 0) {

            jQuery('#ebay_' + type + '_category_'+nextLevels).remove();
            nextLevels++;
            if (nextLevels > 10) {
                break;
            }
        }
        // Remove button
        jQuery('#confirm_' + type + '_category').remove().next("br").remove().next("br").remove();

        level++;

        var currentElement = jQuery(element);

        if (currentElement.val()  == "") {
            return;
        }

        showLoader();

        var marketplaceId = parseInt(jQuery("#ebay_site option:selected").val());

        jQuery.ajax({
            url: settings.childCategoryAjaxInfoUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: currentElement.val(),
                marketplaceId: marketplaceId
            },
            success: function(result) {
                if (result.data.is_latest == true) {
                    // Create new button
                    var newElement = '<br clear="both"/><input type="button" id="confirm_' + type + '_category" class="button" value="Confirm"/>';
                    currentElement.after(newElement);
                    if (type == 'primary') {
                        jQuery('#confirm_' + type + '_category').click(confirmPrimaryCategoryOnClick);
                    } else {
                        jQuery('#confirm_' + type + '_category').click(confirmSecondaryCategoryOnClick);
                    }
                } else {
                    var newElement = '<select id="ebay_' + type + '_category_'+level+'" level="'+level+'" class="col-lg-5 ebay_' + type + '_category"></select>'
                    currentElement.after(newElement);

                    htmlHelper.valuesToSelect(jQuery('#ebay_' + type + '_category_'+level), result.data.categories, true);
                    if (type == 'primary') {
                        jQuery('#ebay_primary_category_'+level).change(primaryCategoryOnChange);
                    } else {
                        jQuery('#ebay_secondary_category_'+level).change(secondaryCategoryOnChange);
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);
            },
            complete: function() {
                 hideLoader();
            }
        });


    }

    function confirmPrimaryCategoryOnClick() {
        confirmCategoryOnClick('primary');
    }

    function confirmSecondaryCategoryOnClick() {
        confirmCategoryOnClick('secondary');
    }

    function confirmCategoryOnClick(type) {
        showLoader();
        var totalCategoryText = "";
        var lastId = 0;
        jQuery(".ebay_"+ type + "_category").each(function(index, element) {
            jQuery(element).find(":selected").text()
            if (totalCategoryText != "") {
                totalCategoryText+=" > ";
            }
            totalCategoryText = totalCategoryText + jQuery(element).find(":selected").text();
            lastId = jQuery(element).val();
            jQuery(element).remove();
        });
        lastId = parseInt(lastId);

        jQuery("#ebay_"+ type + "_category_value").attr("value", lastId);
        //
        jQuery("#confirm_"+ type + "_category").remove();

        jQuery("#ebay_"+ type + "_category_name").attr("value", totalCategoryText);
        jQuery("#"+ type + "_category_label").html(totalCategoryText);

        // Remove all leaved brs
        jQuery("#"+ type + "_category_label").parent().find("br").remove();

        // Insert special button that allow change existing category
        jQuery("#"+ type + "_category_label").after("<br/><input type='button' id='change_"+ type + "_category' class='button' value='Change'/> <input type='button' id='reset_"+ type + "_category' class='button' value='Reset'/>");
        if (type == 'primary') {
            jQuery("#change_"+ type + "_category").unbind("click", categoryPrimaryChangeOnClick);
            jQuery("#change_"+ type + "_category").click(categoryPrimaryChangeOnClick);
            jQuery("#reset_"+ type + "_category").unbind("click", categoryPrimaryResetOnClick);
            jQuery("#reset_"+ type + "_category").click(categoryPrimaryResetOnClick);
        } else {
            jQuery("#change_"+ type + "_category").unbind("click", categorySecondaryChangeOnClick);
            jQuery("#change_"+ type + "_category").click(categorySecondaryChangeOnClick);
            jQuery("#reset_"+ type + "_category").unbind("click", categorySecondaryResetOnClick);
            jQuery("#reset_"+ type + "_category").click(categorySecondaryResetOnClick);
        }

        marketplaceId = parseInt(jQuery("#ebay_site").val());

        // Send ajax request to get category additional data information
        jQuery.ajax({
            url: settings.categoryOptionsAjaxInfoUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: lastId,
                marketplaceId: marketplaceId
            },
            success: function (result) {
                if (result.success == true && result.data != false) {
                    if (type == 'primary') {
                        htmlHelper.valuesToSelect(jQuery("#item_condition"), result.data.conditions, true);

                        jQuery("#item_condition_description").prev("div.hide-notice").hide();
                        jQuery("#item_condition_description").show();

                        // insert item specifics
                        var specificEmpty = htmlHelper.specificsToContainer("product_specifics", result.data.specifics_html);

                        var attributeEmpty = true;
                        jQuery("#attribute_set_id").val(0);
                        jQuery("#product_specifics_attribute").html(" ").hide();
                        if (specificEmpty && attributeEmpty) {
                            jQuery('#product_specifics_empty').show();
                        }

                        // Rebind custom values select possiblity
                        jQuery(".product_specific_select").unbind('change', productSpecificSelectOnChange);
                        jQuery(".product_specific_select").bind('change', productSpecificSelectOnChange);
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);
            },
            complete: function() {
                 hideLoader();
            }
        });
    }

    function categoryPrimaryChangeOnClick() {
        categoryChangeOnClick("primary");
    }

    function categorySecondaryChangeOnClick() {
        categoryChangeOnClick("secondary");
    }

    /**
     * Show new select box with all possible main categories
     */
    function categoryChangeOnClick(type) {
        showLoader();
        var element = jQuery("#change_"+ type + "_category");
        jQuery("#ebay_"+type+"_category_0").remove();
        element.next().remove();
        element.after('<select class="col-lg-5 ebay_'+type+'_category" level="0" id="ebay_'+type+'_category_0"></select>');

        element.remove();

        jQuery.ajax({
            url: settings.categoryMarketplaceMainAjaxInfoUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: jQuery("#ebay_site").val()
            },
            success: function(result) {
                if (result.success == true) {
                    categoryInitFirstChange(result.data.parent_categories, type);
                }
            },
            error: function(jqXHR, textStatus, errorThrown){
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);
            },
            complete: function() {
                 hideLoader();
            }
        });
    }

    function categorySecondaryResetOnClick() {
        categoryResetOnClick('secondary');
    }
    function categoryPrimaryResetOnClick() {
        // Product Specifics reset
        jQuery("#product_specifics").empty().hide();
        jQuery("#product_specifics_empty").hide();
        jQuery("#product_specifics_not_select").show();
        jQuery("#attribute_set_id").val(0);
        jQuery("#product_specifics_attribute").empty().hide();

        categoryResetOnClick('primary');
    }

    function categoryResetOnClick(type) {
        // First active select box

        categoryChangeOnClick(type);
        jQuery("#ebay_"+type+"_category_name").attr("value", "");
        jQuery("#ebay_"+type+"_category_value").attr("value", "");
        jQuery("#"+type+"_category_label").html("");
        jQuery("#ebay_"+type+"_category_value").parent().find(".button, br").remove();
    }


    //#######################################################################
    // Auction Type (FixedPrice, Chinese)

    function auctionTypeOnChange() {
        jQuery(jQuery("#auction_duration option").get(0)).attr("selected", "selected");
        jQuery("#price_start_hide_notice").hide();
        jQuery("#price_start_row").show();

        jQuery(".price-option-select-box").unbind("change", priceOptionSelectOnChange);

        jQuery(".price-option-select-box").change(priceOptionSelectOnChange);
        jQuery(".price-option-select-box").change();

        jQuery("#item_qty_mode option").show();
        jQuery('#price_discount_row').hide();

        switch (parseInt(jQuery(this).val())) {
            case settings.AUCTION_TYPE_CHINESE:
                // Auction
                jQuery("#auction_duration option").each(function(index, element) {
                    switch (jQuery(element).val()) {
                        case "Days_30":
                        case "GTC":
                            jQuery(element).hide();
                            break;
                        default:
                            jQuery(element).show();
                            break;
                    }
                });
                jQuery("#start_price_label").html("Start Price <em>*</em>");

                jQuery("#price_reserve_row").show();
                jQuery("#price_buynow_row").show();

                // qty mode only single item
                jQuery("#item_qty_mode option").hide();
                jQuery("#item_qty_mode option:eq(0)").show().attr("selected", "selected");

                // Disable multivariation
                jQuery("#use_multivariation option:eq(0)").attr("selected", "selected");
                jQuery("#use_multivariation").parent().parent().hide();
                jQuery("#variation-images-row").hide();

                break;
            case settings.AUCTION_TYPE_FIXEDPRICE:
                // Fixed Price
                jQuery("#auction_duration option").each(function(index, element) {
                    switch (jQuery(element).val()) {
                        case "Days_1":
                            jQuery(element).hide();
                            break;
                        default:
                            jQuery(element).show();
                            break;
                    }
                });

                jQuery("#start_price_label").html("Product Price <em>*</em>");
                jQuery("#price_reserve_row").hide();
                jQuery("#price_buynow_row").hide();

                // enable multivariation
                jQuery("#use_multivariation").parent().parent().show();

                jQuery('#price_discount_row').show();
                break;

            default:
                // No selection
                jQuery("#price_start_row_hide_notice").show();

                jQuery("#price_start_row").hide();
                jQuery("#price_reserve_row").hide();
                jQuery("#price_buynow_row").hide();
                break;
        }

        jQuery("#item_qty_mode").change();
    }

    //#######################################################################
    // Price Part

    function priceOptionSelectOnChange() {
        switch (parseInt(jQuery(this).val())) {
            default:
            case settings.PRICE_MODE_PRODUCT:
                jQuery(this).nextAll("span:eq(1)").hide().next().hide();
                break;
            case settings.PRICE_MODE_CUSTOM:
                jQuery(this).nextAll("span:eq(1)").show().next().show();
                break;
        }
    }
    
    // #######################################################################
    // Best Offer
    //
    function bestOfferParamOnChange() {
        switch (parseInt(jQuery(this).val())) {
            default:
            case settings.BEST_OFFER_NO:
                jQuery('.best-offer-related-row').hide();
                break;
            case settings.BEST_OFFER_YES:
                jQuery('.best-offer-related-row').show();
                break;
        }
    }

    // #######################################################################
    // Gifr Service
    //
    function giftIconOnChange() {
        switch (parseInt(jQuery(this).val())) {
            default:
            case settings.GIFT_ICON_NO:
                jQuery('#gift_services_row').hide();
                break;
            case settings.GIFT_ICON_YES:
                jQuery('#gift_services_row').show();
                break;
        }
    }

    function variationModeOnChange()
    {
        switch (parseInt(jQuery(this).val())) {
            default:
            case settings.USE_MULTI_VARIATION_NO:
                jQuery('#variation-images-row').hide();
                break;
            case settings.USE_MULTI_VARIATION_YES:
                jQuery('#variation-images-row').show();
                break;
        }
    }

    function unitIncludeOnChange() {
        switch (parseInt(jQuery(this).val())) {
            default:
            case settings.GIFT_ICON_NO:
                jQuery('#unit_include_row').hide();
                break;
            case settings.GIFT_ICON_YES:
                jQuery('#unit_include_row').show();
                break;
        }
    }


    //#######################################################################
    // QTY Mode

    function itemQtyModeOnChange() {
        switch (parseInt(jQuery(this).val())) {
            case settings.ITEM_QTY_MODE_CUSTOM:
            case settings.ITEM_QTY_MODE_NOT_MORE_THAT:
            case settings.ITEM_QTY_MODE_RESERVED_VALUE:
                jQuery("#item_qty_value").show();
                break;
            default:
                jQuery("#item_qty_value").hide();
        }
    }

    //#######################################################################
    // Description

    function itemDescriptionModeChange() {
        switch (parseInt(jQuery(this).val())) {
            case settings.ITEM_DESCRIPTION_MODE_CUSTOM:
                jQuery("#item_description_mode_custom_row").show();
                break;
            default:
                jQuery("#item_description_mode_custom_row").hide();
        }
    }


    function itemDescriptionVariableInsertClick() {
        var selectedTemplateValue = jQuery(this).prev().find(":selected").val();
        if (jQuery("#item_description_custom").next("span.mceEditor").length > 0 || jQuery(".mce-tinymce").length > 0) {
            if (!isPS16) {
                var mceElement =  tinyMCE.get('item_description_custom')
                mceElement.setContent(mceElement.getContent() + selectedTemplateValue);
            } else {
                tinyMCE.activeEditor.insertContent(selectedTemplateValue);
            }
        } else {
            jQuery("#item_description_custom").insertAtCaret(selectedTemplateValue)
        }

    }
    //#######################################################################
    // Specific part
    function productSpecificSelectOnChange() {
       element = this;
       var selectedValue = jQuery(element).val();
       if (selectedValue instanceof Array) {
           return;
       }
       var elementToShow = jQuery(element).next().next();
       if (selectedValue == settings.SPECIFIC_CUSTOM_VALUE_KEY) {
           elementToShow.show();
       } else {
           elementToShow.hide();
       }

    }

    //#######################################################################
    // Shipping part

    function shippingCheckboxOnClick() {
        element = this;
        var containerQuery = jQuery(element).parent().next().next();
        var textFieldElementFirst = containerQuery.children(":first")
        var textFieldElementSecond = containerQuery.next().children(":first")

        if (!jQuery(element).attr("checked")) {
            textFieldElementFirst.attr("disabled", "disabled");
            textFieldElementSecond.attr("disabled", "disabled");
        } else {
            textFieldElementFirst.removeAttr("disabled");
            textFieldElementSecond.removeAttr("disabled");
        }
    }

    function shippingNewDomesticOnClick() {
        var tbodyContainer = jQuery("#shipping_local_list").children("tbody");

        // Get top priority from list
        var priorityListValues = jQuery("input.local-priority").map(function(i,e) {
            var value = parseInt(jQuery(e).val());
            return isNaN(value)?0:value;
        }).get();
        // lowest element is always 0
        priorityListValues.push(0);
        var newPriorityValue = Math.max.apply(null, priorityListValues) + 1;

        tbodyContainer.append("<tr>\
                <td>\
                    <select name='shippingList["+localShippingIndex+"][name]'>" + htmlHelper.generateOptionList(localShippingList, true) + "</select>\
                </td>\
                <td>\
                   <select name='shippingList["+localShippingIndex+"][mode]' class='shipping-mode-local small-width'>" + shippingModeElementHtml + "</select>\
                </td>\
                <td>\
                    <input type='text' name='shippingList["+localShippingIndex+"][plain]' value='' class='small-width'/>\
                </td>\
                <td>\
                    <input type='text' name='shippingList["+localShippingIndex+"][additional]' value='' class='small-width'/>\
                </td>\
                <td>\
                    <input type='text' name='shippingList["+localShippingIndex+"][priority]' value='"+newPriorityValue+"' class='small-width local-priority'/>\
                </td>\
                <td>\
                    <img src='../img/admin/disabled.gif' class='remove-local-shipping-row' />\
                </td>\
             </tr>");

        localShippingIndex++;

        jQuery(".remove-local-shipping-row").unbind('click', shippingRemoveDomesticRowOnClick);
        jQuery(".remove-local-shipping-row").click(shippingRemoveDomesticRowOnClick);
        
        jQuery('.shipping-mode-local').unbind('change', shippingModeOnChange);
        jQuery('.shipping-mode-local').change(shippingModeOnChange);
        return false;
    }

    function shippingRemoveDomesticRowOnClick() {
        element = this;
        jQuery(element).parent().parent().remove();
    }

    function shippingNewIntOnClick() {
        var tbodyContainer = jQuery("#shipping_international_list").children("tbody");

        // Get top priority from list
        var priorityListValues = jQuery("input.international-priority").map(function(i,e) {
            var value = parseInt(jQuery(e).val());
            return isNaN(value)?0:value;
        }).get();
        // lowest element is always 0
        priorityListValues.push(0);
        var newPriorityValue = Math.max.apply(null, priorityListValues) + 1;

        // Conainer with general shipping information
        tbodyContainer.append("<tr class='int-shipping-row'>\
                <td>\
                    <select name='shippingIntList["+intShippingIndex+"][name]'>" + htmlHelper.generateOptionList(intShippingList, true) + "</select>\
                </td>\
                <td>\
                   <select name='shippingIntList["+intShippingIndex+"][mode]' class='shipping-mode-int small-width'>" + shippingModeElementHtml + "</select>\
                </td>\
                <td>\
                    <input type='text' name='shippingIntList["+intShippingIndex+"][plain]' value='' class='small-width'/>\
                </td>\
                <td>\
                    <input type='text' name='shippingIntList["+intShippingIndex+"][additional]' value='' class='small-width'/>\
                </td>\
                <td>\
                    <input type='text' name='shippingIntList["+intShippingIndex+"][priority]' value='"+newPriorityValue+"' class='small-width international-priority'/>\
                </td>\
                <td>\
                    <img src='../img/admin/disabled.gif' class='remove-int-shipping-row' />\
                </td>\
             </tr>");
        
        // Container with shipping to location list
        tbodyContainer.append("<tr>\
                <td class='location-row' colspan='6'>\
                    <strong>Ship to Location:</strong><br/>\
                    <ul class='ship-to-location-list'>" + 
            htmlHelper.generateCheckboxList('shippingIntList['+intShippingIndex+'][locations][]',
                'internation_ship_to_location_'+intShippingIndex,
                intLocationList) +
            "</ul>\
                </td>\
             </tr>");

        intShippingIndex++;

        jQuery(".remove-int-shipping-row").unbind('click', shippingRemoveIntRowOnClick);
        jQuery(".remove-int-shipping-row").click(shippingRemoveIntRowOnClick);

        jQuery('.shipping-mode-int').unbind('change', shippingModeOnChange);
        jQuery('.shipping-mode-int').change(shippingModeOnChange);

        return false;
    }

    function shippingRemoveIntRowOnClick() {
        element = this;
        var trRow = jQuery(element).parent().parent();
        trRow.next().remove();
        trRow.remove();
    }

    // Create new Exclude location 
    function shippingNewExcludeOnClick() {
        var excludeValue = jQuery("#exclude-location-select-container select option:selected").val();
        if (excludeValue != "") {
            var excludeText = jQuery("#exclude-location-select-container select option:selected").text();
            jQuery("#exclude-location-selected-list").append(
                    "<li><input type='hidden' name='shippingExcludeLocations[]' value='"+excludeValue+"'/>"+
                            excludeText +
                            "&nbsp;<img class='remove-exclude-location' src='../img/admin/disabled.gif'/>"+
                     "</li>")
            jQuery(".remove-exclude-location").unbind("click", removeExcludeLocationOnClick);
            jQuery(".remove-exclude-location").click(removeExcludeLocationOnClick);
        }

        return false;
    }

    function removeExcludeLocationOnClick() {
        element = this;
        jQuery(element).parent().remove();
    }
    
    function shippingModeOnChange() {
        var parentTd = jQuery(this).parent();
        if (jQuery(this).val() == settings.SHIPPING_MODE_CUSTOM_PRICE) {
            parentTd.next().find('input').removeAttr('readonly');
            parentTd.next().next().find('input').removeAttr('readonly');
        } else {
            parentTd.next().find('input').attr('readonly', 'readonly');
            parentTd.next().next().find('input').attr('readonly', 'readonly');
        }
    }

    function calculatedShippingParamsOnChange() {
        var isProductValue = (jQuery(this).val() == 0);
        var relatedField = jQuery(this).next();
        if (isProductValue) {
            relatedField.hide();
        } else {
            relatedField.show();
        }
    }

    function shippingTypeModeOnChange() {
        // Change shipping mode
        var elementName = jQuery(this).attr('name');

        var intValue = jQuery("#shipping_int_type").val();
        var localValue = jQuery("#shipping_local_type").val();
        if (intValue == settings.SHIPPING_TYPE_CALCULATED || localValue == settings.SHIPPING_TYPE_CALCULATED) {
            jQuery("#calculated_shipping_row").show();
        } else {
            jQuery("#calculated_shipping_row").hide();
        }
        
        jQuery.ajax({
            url: settings.shippingListByModeUrl,
            type: "post",
            dataType: 'json',
            data: {
                id: jQuery("#ebay_site").val(),
                value: jQuery(this).val()
            },
            success: function(result, textStatus, jqXHR) {
                if (result.success == true) {
                    if (elementName == 'shipping_local_type') {
                        jQuery("#shipping_local_list tbody").empty();
                        localShippingIndex = 0;
                        localShippingList = result.local_shippings;
                        jQuery("#new-local-shipping").click();
                    } else if (elementName == 'shipping_int_type') {
                        jQuery("#shipping_international_list tbody").empty();
                        intShippingIndex = 0;
                        intShippingList = result.international_shippings;
                        jQuery("#new-int-shipping").click();
                    }
                }
            },
            // callback handler that will be called on error
            error: function(jqXHR, textStatus, errorThrown){
                // log the error to the console
                alert('Problem with get JSON data.\n' + textStatus + "\n" + errorThrown);

            },
            complete: function() {

            }
        });
    }

    //#######################################################################
    // Payment part

    function paymentPaypalOnChange() {
        if (jQuery(this).is(':checked')) {
            jQuery("#payment_paypal_email").show().parent().parent().show();
        } else {
            jQuery("#payment_paypal_email").parent().parent().hide();
        }
    }

    function paymentListOnChange() {
        if (jQuery("#paymentBox_PayPal").is(':checked') && jQuery("#payment_list input:checked").size() == 1) {
            jQuery("#autopay_row").show();
        } else {
            jQuery("#autopay_row").hide();
            jQuery("#autopay").val(0)
        }
    }
    
    // For Italy marketplace when select CoD payment show cost field
    function paymentCODOnChange() {
        if (jQuery(this).is(':checked')) {
            jQuery("#cod_cost_italy").show().parent().parent().show();
        } else {
            jQuery("#cod_cost_italy").parent().parent().hide();
        }
    }

    //#######################################################################
    // Return policy
    function returnsAcceptedChange() {
        switch (jQuery(this).val()) {
            case settings.RETURN_ACCEPTED_EBAY_CONST:
                jQuery(".return-policy-accepted").show();
                jQuery(".return-policy-accepted select").each(function(index, value)  {
                    if (jQuery(this).find("option").size() <= 1) {
                        jQuery(this).parents(".return-policy-accepted").hide();
                    }
                });
                break;
            default:
                jQuery(".return-policy-accepted").hide();
        }
    }

    //#######################################################################
    // Export any functions we may need outside the closure
    return {

    };

}

