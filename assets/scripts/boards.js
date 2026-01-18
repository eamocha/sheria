/*
 * Save search filter
 * Open the modal and save the name of the filter
 * @param string boardType(name of the board)
 */
function saveSearchFilters(boardType) {
    var saveFilterContainer = jQuery('.save-filter-container');
    jQuery('#filter-name', saveFilterContainer).val('');
    jQuery('.validation-error-container', saveFilterContainer).addClass('d-none');
    saveFilterContainer.removeClass('d-none');
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.modal', saveFilterContainer).modal('hide');
        }
    });
    jQuery('.modal', saveFilterContainer).modal({
        keyboard: false,
        show: true,
        backdrop: 'static'
    });
    jQuery("#save-filter-submit").click(function () {
        // Submitting the form
        var formContainer = boardType == 'case_board' ? '#planningBoardFilters' : '#taskBoardFilters';
        jQuery('#savedForm', formContainer).val(jQuery('#filter-name', saveFilterContainer).val());
        var formData = jQuery(formContainer).serializeArray();
        jQuery.ajax({
            beforeSend: function () {
                jQuery('.loader-submit', saveFilterContainer).addClass('loading');
                jQuery('#save-filter-submit', saveFilterContainer).attr('disabled', 'disabled');
            },
            data: formData,
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'dashboard/' + boardType + '_save_search_filters',
            success: function (response) {
                if (response.result) {
                    jQuery('.modal', saveFilterContainer).modal('hide');
                    pinesMessage({ty: 'success', m: _lang.filterSavedSuccessfully});
                    var savedFiltersList = jQuery('#savedFilters');
                    if (jQuery('#savedFilters option').length <= 1) { // no saved reports
                        jQuery('div.showHide').removeClass('d-none');
                    }
                    savedFiltersList.append("<option value=" + response.id + ">" + response.keyName + "</option>");
                    jQuery('#deleteFilterLink').removeClass('d-none');
                    savedFiltersList.val(response.id).trigger('change');
                } else {
                    for (i in response.validationErrors) {
                        jQuery('.validation-error-container', saveFilterContainer).removeClass('d-none');
                        jQuery("div").find("[data-field=" + i + "]", saveFilterContainer).removeClass('d-none').html(response.validationErrors[i]);

                    }
                }
            }, complete: function () {
                jQuery('.loader-submit', saveFilterContainer).removeClass('loading');
                jQuery('#save-filter-submit', saveFilterContainer).removeAttr('disabled');

            },
            error: defaultAjaxJSONErrorsHandler
        });
    });
    jQuery("input", saveFilterContainer).keypress(function (e) {
        if (e.which == 13) {
            e.preventDefault();
            jQuery("#save-filter-submit").click();
        }
    });
    jQuery('.modal-body',saveFilterContainer).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#filter-name', saveFilterContainer).focus();

    });
    ctrlS(function () {
        jQuery("#save-filter-submit").click();
    });
    resizeMiniModal(saveFilterContainer)
}
// check if mouseup function is triggered in case/task board and not in the kanban container to remove the column css
function onMouseUpForBoards() {
    jQuery(document).mouseup(function (e)
    {
        var container = jQuery("#kanBan");
        if (!container.is(e.target) // if the target of the click isn't the container...
                && container.has(e.target).length === 0) // ... nor a descendant of the container
        {
            jQuery('.webix_view.webix_kanban_list.column-transition-highlight').removeClass('column-transition-highlight');
        }
    });
}
