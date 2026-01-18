function uploadFileForm() {
       jQuery.ajax({
        dataType: 'JSON',
        data: {'id': conveyancingId, 'lineage': jQuery('#lineage', documentsForm).val()|0},//remove this line if you want to use the default value of lineage
        type: 'GET',
        url: getBaseURL(module) + 'conveyancing/upload_file',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".upload-file-container").length <= 0) {
                    jQuery('<div class="d-none upload-file-container"></div>').appendTo("body");
                    var uploadFileContainer = jQuery('.upload-file-container');
                    uploadFileContainer.html(response.html).removeClass('d-none');
                    jQuery('.select-picker', uploadFileContainer).selectpicker();
                    var lineage = jQuery('#lineage', documentsForm).val();
                    jQuery('#lineage', uploadFileContainer).val(lineage);
                    commonModalDialogEvents(uploadFileContainer);
                    jQuery("#form-submit", uploadFileContainer).click(function () {
                        uploadFileFormSubmit(uploadFileContainer);
                    });
                    jQuery(uploadFileContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            uploadFileFormSubmit(uploadFileContainer);
                        }
                    });
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}