/**
 * Append new message to container or change existing
 */
function appendMessage(elementId, message, messageType) {
    if (messageType == undefined || messageType == null) {
        messageType = "processing";
    }
    if (message == undefined || message == null) {
        message = false;
    }

    if (jQuery("#"+elementId).size() > 0) {
        // Change
        jQuery("#"+elementId).removeClass("notify-processing notify-success notify-warning notify-error").addClass("notify-"+messageType)
        if (message!=false) {
            jQuery("#"+elementId).find("ul:eq(0) li:eq(0)").html(message);
        }
    } else {
        // Create
        var messageContainer = jQuery('<div id="'+ elementId + '" class="notify-'+ messageType + ' notify-block"><ul></ul></div>');
        var ulContainer = messageContainer.find("ul:eq(0)");

        jQuery("#synchronize-process").append(messageContainer);
        ulContainer.append("<li>" + message + "</li>");
    }
}

/**
 * Adding return button
 */
function appendReturnButton(message, backUrl) {
    var buttonHtml = "<input type='button' class='button' onclick='document.location.href=\""+backUrl+"\"' value='"+message+"'/>";
    jQuery('#synchronize-process').append(buttonHtml);
}
