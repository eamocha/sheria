function requestLegalOpinion(module) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL(module) + 'legal_opinions/add',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#contract-generate-container').length <= 0) {
                        jQuery('<div id="contract-generate-container" class="primary-style"></div>').appendTo("body");
                        var contractGenerateContainer = jQuery('#contract-generate-container');
                        contractGenerateContainer.html(response.html);
                        initializeModalSize(contractGenerateContainer);
                       commonModalDialogEvents(contractGenerateContainer);
                       contractGenerateEvents(module, contractGenerateContainer);
                    }
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}