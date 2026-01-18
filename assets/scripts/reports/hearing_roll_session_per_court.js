jQuery(function() {
    jQuery('.multi-select', '#hearingReportSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
	loadReportsEventsForFilters();
	jQuery('a', '#pagination').each(function(index, element) {
		jQuery(element).click(function(e) {
			e.preventDefault();
			var containerForm = jQuery('#submitBtn2', '#hearingReportQuickSearchFilters').attr('clicked') == '1' ? '#hearingReportQuickSearchFilters' : '#hearingReportSearchFilters';
			jQuery('#skip', containerForm).val((element.innerHTML - 1) * jQuery('#take', '#hearingReportSearchFilters').val());
			jQuery(containerForm).attr('action', element.getAttribute('href')).submit();
		});
	});
	collapseExpandRows();
    if ( window.history.replaceState ) {
        window.history.replaceState( null, null, window.location.href );
    }
});
function collapseExpandRows(){
	jQuery("i.first-grouping").click(function() {
		$this = jQuery(this);
		$targetParent = $this.parent().parent();
		$target = $targetParent.find("table.first-grouping");
		if ($target.is(':visible')) {
			$target.hide();
			$this.removeClass('fa fa-chevron-down');
			$this.addClass('fa fa-chevron-right');
		} else {
			$target.show();
			$this.removeClass('fa fa-chevron-right');
			$this.addClass('fa fa-chevron-down');
		}
	});
}
function collapseExpandAllRows(expand){
	expand = expand || false;
	jQuery("i.first-grouping").each(function(){
		$this = jQuery(this);
		$targetParent = $this.parent().parent();
		$target = $targetParent.find("table.first-grouping");
		if(expand){
			$target.show();
			$this.removeClass('fa fa-chevron-right');
			$this.addClass('fa fa-chevron-down');
		}else{
			$target.hide();
			$this.removeClass('fa fa-chevron-down');
			$this.addClass('fa fa-chevron-right');
		}	
	});
}
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function changePerPage(selectedNum) {
	var containerForm = jQuery('#hearingReportSearchFilters');
	jQuery('#take', containerForm).val(selectedNum.options[selectedNum.selectedIndex].value);
	jQuery('#skip', containerForm).val('0');
	containerForm.attr('action', getBaseURL() + 'reports/hearing_roll_session_per_court').submit();
}
function loadReportsEventsForFilters() {
	makeFieldsDatePicker({fields: [jQuery('#startDateValue', '#hearingReportSearchFilters'), jQuery('#startDateEndValue', '#hearingReportSearchFilters')]});
        makeFieldsHijriDatePicker({fields: ['start-date-hijri-filter', 'start-date-end-hijri-filter']});
	userLookup(jQuery('#lawyersValue', '#hearingReportSearchFilters'), 'lawyersValue');
}
function functionReturnTrue() {
	return true;
}
function exportToPDF() {
    var formId = jQuery('#hearingReportSearchFilters');
    jQuery('#filtersInfoForExport1', formId).val(JSON.stringify(getExportInfoFilter('hearingReportSearchFilters')));
    var oldFormAction = formId.attr('action');
    formId.attr('action', getBaseURL() + 'reports/hearing_roll_session_per_court_pdf').submit();
    formId.attr('action', oldFormAction);
    enableAll(formId);
}
function hearingReportSettings(){
    jQuery.ajax({
        url: getBaseURL() + 'reports/hearing_roll_session_per_court_settings',
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery(".settings-dialog-container").length <= 0) {
                    jQuery('<div class="d-none settings-dialog-container"></div>').appendTo("body");
                    var settingsDialogContainer = jQuery('.settings-dialog-container');
                    settingsDialogContainer.html(response.html).removeClass('d-none');
                    jQuery('#hearing-excluded-statuses').html(response.html_hearing_excluded_statuses);
                    jQuery(document).keyup(function (e) {
                        if (e.keyCode == 27) {
                            jQuery('.modal', settingsDialogContainer).modal('hide');
                        }
                    });
                    initializeModalSize(settingsDialogContainer);
                    jQuery('.modal', settingsDialogContainer).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'

                    });
                    
                    jQuery("#settings-dialog-submit").click(function () {
                        var formData = jQuery("form#settings-dialog-form", settingsDialogContainer).serializeArray();
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery('.loader-submit', settingsDialogContainer).addClass('loading');
                                jQuery('#settings-dialog-submit', settingsDialogContainer).attr('disabled', 'disabled');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'reports/hearing_roll_session_per_court_settings',
                            success: function (response) {
                                if (undefined !== response.validationErrors) {
                                    for (i in response.validationErrors) {
                                        jQuery('.validation-error-container', '#settings-dialog-form').removeClass('d-none');
                                        jQuery("div", settingsDialogContainer).find("[data-field=" + i + "]").html(response.validationErrors[i]);

                                    }
                                } else {
                                    jQuery('.modal', settingsDialogContainer).modal('hide');
                                    // reload report
                                    window.location = window.location.href;
                                }
                            }, complete: function () {
                                jQuery('.loader-submit', settingsDialogContainer).removeClass('loading');
                                jQuery('#settings-dialog-submit', settingsDialogContainer).removeAttr('disabled');

                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    });
                    jQuery("input", settingsDialogContainer).keypress(function (e) {
                        if (e.which == 13) {
                            e.preventDefault();
                            jQuery("#settings-dialog-submit", settingsDialogContainer).click();
                        }
                    });
                    jQuery('.modal-body',settingsDialogContainer).on("scroll", function() {
                        jQuery('.bootstrap-select.open').removeClass('open');
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(settingsDialogContainer);
                    });
                    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
                        jQuery('#field-name', settingsDialogContainer).focus();

                    });
                    ctrlS(function () {
                        jQuery("#settings-dialog-submit", settingsDialogContainer).click();
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
