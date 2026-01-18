function courtForm(courtId){
    courtId = courtId || false;
    courtRelatedForm(courtId, 'courts/', null, 'court', "#save-court-btn");
}
function courtTypeForm(courtTypeId){
    courtTypeId = courtTypeId || false;
    courtRelatedForm(courtTypeId, 'court_types/', {quick_add_form: true}, 'court-type', "#administration-dialog-submit");
}
function courtDegreeForm(courtDegreeId){
    courtDegreeId = courtDegreeId || false;
    courtRelatedForm(courtDegreeId, 'court_degrees/', {quick_add_form: true}, 'court-degree', "#administration-dialog-submit");
}
function courtRegionForm(courtRegionId){
    courtRegionId = courtRegionId || false;
    courtRelatedForm(courtRegionId, 'court_regions/', {quick_add_form: true}, 'court-region', "#administration-dialog-submit");
}
function courtRelatedForm(courtRelatedId, url, formData, moduleName, submitButtonSelector){
    courtRelatedId = courtRelatedId || false;
    url = url || false;
    formData = formData || false;
    moduleName = moduleName || false;
    submitButtonSelector = submitButtonSelector || false;
    ajaxUrl = getBaseURL() + url + (courtRelatedId ? ('edit/' + courtRelatedId) : 'add');
    jQuery.ajax({
        dataType: 'JSON',
        url: ajaxUrl,
        data: formData,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#' + moduleName + '-dialog').length <= 0) {
                    jQuery('<div id="' + moduleName + '-dialog"></div>').appendTo("body");
                    var dialog = jQuery('#' + moduleName + '-dialog');
                    dialog.html(response.html);
                    jQuery('.modal', dialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                    var courtRelatedId = jQuery('#id', '#' + moduleName + '-form').val();
                    jQuery(submitButtonSelector, dialog).click(function () {
                        courtRelatedFormSubmit(dialog, courtRelatedId, url, moduleName);
                    });
                    jQuery(dialog).find('input').keypress(function (e) {
                        if (e.which == 13) { // pressing enter
                            courtRelatedFormSubmit(dialog, courtRelatedId, url, moduleName);
                        }
                    });
                    courtRelatedFormEvents(dialog, moduleName);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function courtRelatedFormSubmit(container, id, url, moduleName) {
    id = id || false;
    url = url || false;
    moduleName = moduleName || false;
    var formData = jQuery("form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + url + (id ? 'edit/' + id : 'add'),
        success: function (response) {
            jQuery('.inline-error', '#' + moduleName + '-dialog').addClass('d-none');
            if (response.result) {
                pinesMessage({ty: 'success', m: response.msg});
                if (typeof courtCallBack === "function") {
                  courtCallBack(response.id);   
                }
                jQuery('.modal', '#' + moduleName + '-dialog').modal('hide');
                location.reload();
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function courtRelatedFormEvents(container, moduleName){
    moduleName = moduleName || false;
    jQuery('.select-picker', '#' + moduleName + '-dialog').selectpicker();
    jQuery('#court_type_id', '#litigation-stage-dialog').on('shown.bs.select', function (e) {
        jQuery('.dropdown-menu.inner').animate({
            scrollTop: jQuery(".selected").offset().top
        }, "fast");
        jQuery('.modal-body').animate({
            scrollTop: '0px'
        }, "fast");
    });
    initializeModalSize(container,0.4,0.1);
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery("div", container).find("[data-id=court_type_id]").focus();
        jQuery('#pendingReminders').parent().popover('hide');
    });
}