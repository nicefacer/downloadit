function HtmlHelper() {
    return {
        valuesToSelect: function(selectElement, valueList, isAddEmpty) {
            // Hide notice grid
            jQuery(selectElement).prev("div.hide-notice").hide();

            jQuery(selectElement).empty();
            jQuery(selectElement).show();

            if(typeof(valueList) !== 'undefined' && valueList != null) {
              var optionsList =  this.generateOptionList(valueList, isAddEmpty);
              jQuery(selectElement).append(optionsList);
            }

            if (typeof(valueList) === 'undefined' || valueList == null || valueList.length == 0) {
                // Hide rows with options that don't have values
                jQuery(selectElement).empty().hide().parent().parent().hide();
            } else {
                jQuery(selectElement).parent().parent().show();
            }
        },

        generateOptionList: function(valueList, isAddEmpty) {
            var totalOptions = "";
            if (isAddEmpty != null) {
                totalOptions = totalOptions + '<option value=""> -- Please Select --</option>';
            }
            for (var key in valueList) {
                var val = valueList[key];
                totalOptions = totalOptions + '<option value="'+val.id+'">'+ val.label + '</option>';
            }
            
            return totalOptions;
        },
        
        generateCheckboxList: function(checkBoxesName, checkBoxesIdPrefix, valueList) {
            var totalList = "";
            for (var key in valueList) {
                var val = valueList[key];
                var checkBoxId = checkBoxesIdPrefix+'_'+val.id;

                totalList += '<li>' +
                                     '<input type="checkbox" \
                                                name="'+checkBoxesName+'"\
                                                id="'+checkBoxId+'"\
                                                value="'+val.id+'" />\n' +
                                     '<label for="'+checkBoxId+'" >'+ val.label + '</label>' +
                              '</li>';
            }
            return totalList;
        },

        shippingToGrid: function (gridElement, valueList, shippingType) {
            jQuery(gridElement).parent().show().prev().hide();

            jQuery(gridElement).empty();
            for (var key in valueList) {
                var val = valueList[key];
                jQuery(gridElement).append(jQuery('<tr><td><input type="checkbox" class="shipping_checkbox" id="shipping'+shippingType+'Box_'+val.id+'"name="shipping'+shippingType+'Box['+val.id+']" value="'+val.id+'"/></td><td><label for="shipping'+shippingType+'Box_'+val.id+'">'+val.label+'</label></td><td><input type="text" name="shipping'+shippingType+'Cost['+val.id+']" disabled="disabled"/></td><td><input type="text" name="shipping'+shippingType+'CostAdditional['+val.id+']" disabled="disabled"/></td></tr>'));
            }

        },

        checkboxesToContainer: function (container, valueList, groupName) {
            // Hide notice and show main element
            jQuery(container).parent().show().prev().hide();

            jQuery(container).empty();
            for (var key in valueList) {
                var val = valueList[key];
                jQuery(container).append(jQuery('<tr><td><input type="checkbox" name="'+groupName+'[]" id="'+groupName+'_'+val.id+'" value="'+val.id+'" /></td><td><label for="'+groupName+'_'+val.id+'">'+val.label+'</label></td></tr>'));
            }
        },

        setCookie: function (name, value, expires, path, domain, secure) {
              document.cookie = name + "=" + escape(value) +
                ((expires) ? "; expires=" + expires : "") +
                ((path) ? "; path=" + path : "") +
                ((domain) ? "; domain=" + domain : "") +
                ((secure) ? "; secure" : "");
        },
        getCookie: function (name) {
                var cookie = " " + document.cookie;
                var search = " " + name + "=";
                var setStr = null;
                var offset = 0;
                var end = 0;
                if (cookie.length > 0) {
                        offset = cookie.indexOf(search);
                        if (offset != -1) {
                                offset += search.length;
                                end = cookie.indexOf(";", offset)
                                if (end == -1) {
                                        end = cookie.length;
                                }
                                setStr = unescape(cookie.substring(offset, end));
                        }
                }
                return(setStr);
        },


        specificsToContainer: function (containerId, htmlList) {
            var $container = jQuery("#"+containerId);
            jQuery('#' + containerId + '_not_select').hide(); // Hide notice message
            jQuery('#' + containerId + '_empty').hide();
            $container.empty();
            $container.html(htmlList);
            $container.show();

            if (htmlList.length == 0) {
                return true; // empty
            }
            return false;
        }

    }

}