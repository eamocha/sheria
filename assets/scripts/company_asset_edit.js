var companyAssetContainer;
jQuery(document).ready(function () {
    companyAssetContainer = jQuery('#asset-edit-form-container');
    loadCustomFieldsEvents('custom-field-',companyAssetContainer);
    jQuery('.select-picker', companyAssetContainer).selectpicker({dropupAuto: false});
    jQuery('.date-picker', companyAssetContainer).css({float: 'left'});
    fixDateTimeFieldDesign(companyAssetContainer);
    jQuery('#lineage', '#documentsForm').val(jQuery('#parent-lineage', '#asset-docs-container').val());
});
function companyAssetFormSubmit(id) {
    var formData = jQuery("form#company-asset-form", companyAssetContainer).serializeArray();
    jQuery.ajax({
        url: getBaseURL() + 'companies/asset_edit/' + id,
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', companyAssetContainer).addClass('loading');
            jQuery('.modal-save-btn', companyAssetContainer).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', companyAssetContainer).addClass('d-none');
            if (response.result) {
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                $documentsGrid.data('kendoGrid').dataSource.read();
            } else {
                displayValidationErrors(response.validation_errors, companyAssetContainer);
            }
        }, complete: function () {
            jQuery('.modal-save-btn', companyAssetContainer).removeAttr('disabled');
            jQuery('.loader-submit', companyAssetContainer).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function disableInactiveBreadCrumb() {
    jQuery.each(jQuery('li', jQuery('#BreadcrumbContainer', '#documentsContainer')), function (index, val) {
        if (index <2) {
            if (jQuery('a', this).length > 0) {
                jQuery(this).html(jQuery('a', this).text());
            }
            jQuery(this).addClass('active');
        }
    });
}