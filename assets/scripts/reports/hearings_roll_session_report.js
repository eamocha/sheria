jQuery(document).ready(function () {
    gridInitialization();
    if (_lang.languageSettings['langDirection'] === 'rtl')
        gridScrollRTL();
    jQuery('div.k-grid-content').css("height", "300px");
    jQuery('.multi-select', '#caseReportsQuickSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"});
    jQuery('.toolTipReport div').each(function (index, element) {
        jQuery(element).tooltip({
            show: {
                effect: "highlight",
                duration: 1100
            },
            track: false
        });
    });
    userLookup(jQuery('#assigneeValue', '#caseReportsQuickSearchFilters'));

});
function changePerPage(selectedNum) {
    var containerForm = jQuery('#hearingsSearchFilters');
    jQuery('#take', containerForm).val(selectedNum.options[selectedNum.selectedIndex].value);
    jQuery('#skip', containerForm).val('0');
    containerForm.attr('action', getBaseURL() + 'reports/hearings_roll_session_report').submit();
}
function hearingsFilterType($type) {
    var containerForm = jQuery('#hearingsSearchFilters');
    jQuery('#filter_type', containerForm).val($type);
    containerForm.attr('action', getBaseURL() + 'reports/hearings_roll_session_report').submit();
}
function functionReturnTRUE() {
    return true;
}
function loadEventsForFilters() {
    contactAutocompleteMultiOption('contactOutsourceToValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    jQuery('#opponentsOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#opponentsValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#opponentsValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#opponentsOpertator', '#hearingsSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'companies' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched,
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    jQuery('#opponentForeignNameValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignOpponentTypeOpertator', '#hearingsSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'companies' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched,
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                               if (lookupType == 'contacts') {
                                   return {
                                       label: item.contactForeignName,
                                       value: item.contactForeignName,
                                       record: item
                                   }
                               } else if (lookupType == 'companies') {
                                   return {
                                       label: item.foreignName,
                                       value: item.foreignName,
                                       record: item
                                   }
                               }
                           }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    jQuery('#foreignOpponentTypeOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#opponentForeignNameValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientsOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#clientsValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientsValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientsOpertator', '#hearingsSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched,
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    jQuery('#foreignClientTypeOpertator', '#hearingsSearchFilters').change(function () {
        jQuery('#clientForeignNameValue', '#hearingsSearchFilters').val('');
    });
    jQuery('#clientForeignNameValue', '#hearingsSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignClientTypeOpertator', '#hearingsSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.noMatchesFound,
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.contactForeignName,
                                        value: item.contactForeignName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: item.foreignName,
                                        value: item.foreignName,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    userLookup('lawyersValue');
}
function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function exportCaseHearingsToExcel() {
    var containerForm = jQuery('#hearingsSearchFilters');
    var filters = hearingsSearchFilters();
    jQuery('#filtersForExport', containerForm).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', containerForm).val(JSON.stringify(getExportInfoFilter('hearingsSearchFilters')));

    jQuery('#filter_type',containerForm).attr('type',"text").addClass('d-none');
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', containerForm).val(pageNumber);
    containerForm.attr('action', getBaseURL() + 'export/hearings_roll_session_report').submit();
    containerForm.attr('action', getBaseURL() + 'reports/hearings_roll_session_report');
    jQuery('#filtersForExport', containerForm).val("");
    jQuery('#filter_type',containerForm).attr('type',"hidden");
    enableAll(containerForm);
}
jQuery(function () {
    jQuery('.multi-select', '#hearingsSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    makeFieldsDatePicker({fields: ['startDateValue', 'startDateEndValue', 'postponedDateValue', 'postponedDateEndValue', 'arrivalDateValue', 'arrivalDateEndValue', 'sentenceDateValue', 'sentenceDateEndValue', 'closedOnDateValue', 'closedOnDateEndValue']});
    makeFieldsHijriDatePicker({fields: ['start-date-hijri-filter', 'start-date-end-hijri-filter', 'postponed-date-hijri-filter', 'postponed-date-end-hijri-filter']});
    jQuery('#hearingsSearchFilters').bind('submit', function (e) {
        hideAdvancedSearch();
    });
    jQuery('a', '#pagination').each(function (index, element) {
        jQuery(element).click(function (e) {
            e.preventDefault();
            var containerForm = jQuery('#hearingsSearchFilters');
            jQuery('#skip', containerForm).val((element.innerHTML - 1) * jQuery('#take', '#hearingsSearchFilters').val());
            jQuery(containerForm).attr('action', element.getAttribute('href')).submit();
        });
    });
});
/**
 * regridInitialization function
 *
 * @return void
 */
function regridInitialization(event) {
    event.preventDefault();
    jQuery('#reportTable').data('kendoGrid').dataSource.read();
    jQuery('#reportTable').data('kendoGrid').refresh();
}
/**
 * gridInitialization function
 *
 * @return void
 */
function gridInitialization() {
    var gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "reports/hearings_roll_session_report",
                dataType: "JSON",
                type: "POST",
                beforeSend: function() {
                    jQuery("#loader-global").show();
                },
                complete: function (XHRObj) {
                    jQuery(".exportCaseHearingsToExcel").removeClass('d-none');
                    jQuery("#loader-global").hide();
                    jQuery('#take', '#hearingsSearchFilters').val(jQuery('#reportTable').data('kendoGrid').dataSource._take);
                    jQuery('#skip', '#hearingsSearchFilters').val(jQuery('#reportTable').data('kendoGrid').dataSource._skip);
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    jQuery('*[data-callexport]').off("click").on('click', function () {
                        if (hasAccessToExport != 1) {
                            pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                        } else {
                            $response = jQuery.parseJSON(XHRObj.responseText || "null");
                            if ($response.totalRows <= 10000) {
                                exportCaseHearingsToExcel();
                            } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                    });
                    loadExportModalRanges($response.totalRows);
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter_type = jQuery('#filter_type').val();
                    options.filter = hearingsSearchFilters();

                }
                return options;

            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows"
        },
        pageSize: 20,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    jQuery("#reportTable").kendoGrid({
        sortable: true,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 30, 40, 50, 100], refresh: true},
        dataSource: gridDataSrc,
        dataBound: function () {
            jQuery('.row-title').each(function (index, element) {
                jQuery(element).tooltip({
                    content: function () {
                        return this.getAttribute("title");
                    },
                    delay: {"show": 100, "hide": 100},
                    template: '<div class="tooltip" role="tooltip" style="z-index:6666"><div class="tooltip-arrow"></div><div class="tooltip-inner tooltip-cust"></div></div>',
                    container: 'body'
                });
            });
            displayColHeaderPlaceholder();
        },
        columns: tableColumns
    });
}
/**
 * hearingsSearchFilters function
 *  @return void
 */
function hearingsSearchFilters() {
    var filtersForm = jQuery('#hearingsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('hearingsSearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}