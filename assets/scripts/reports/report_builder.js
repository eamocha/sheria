var dataSearch = '';

jQuery(document).ready(function () {
    jQuery("#columns").multiselect({keepOrder: true});
    makeFieldsDatePicker({fields: ['arrivalDateValue', 'caseArrivalDateValue', 'dueDateValue', 'arrivalDateEndValue', 'caseArrivalDateEndValue', 'dueDateEndValue', 'closedOnValue', 'closedOnEndValue', 'constitutionDateEndValue', 'sentenceDateEndValue', 'due_date', 'hearingDateEndValue','last_hearingValue']});

    jQuery('#opponentNationalitiesValue').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({url: getBaseURL() + 'home/load_country_list', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.countryName, value: item.countryName, record: item}
                        }));
                    }
                }});
        }, minLength: 2, select: function (event, ui) {
        }}
    );
    jQuery('div.ui-multiselect.ui-helper-clearfix.ui-widget').css("width", "100%");
    jQuery('div.ui-multiselect.ui-helper-clearfix.ui-widget div.selected').css("width", "50%");
    jQuery('div.ui-multiselect.ui-helper-clearfix.ui-widget div.available').css("width", "50%");

    jQuery('#outsourceTypeOperator', '#caseSearchFilters').change(function () {
        jQuery('#outsourceToValue', '#caseSearchFilters').val('');

        if (jQuery(this).val() == 'Company') {
            jQuery('#outsourceToField', '#caseSearchFilters').val('companyOutsourceTo');
            jQuery('#outsourceToFunction', '#caseSearchFilters').val('companyoutsourceto_field_value');
        } else {
            jQuery('#outsourceToField', '#caseSearchFilters').val('contactOutsourceTo');
            jQuery('#outsourceToFunction', '#caseSearchFilters').val('contactoutsourceto_field_value');
        }
    });

    jQuery('#outsourceToValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#outsourceTypeOperator', '#caseSearchFilters').val();
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
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
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
});
function exportCasesTo(type) {
    var filter = checkWhichTypeOfFilterIUseAndReturnFilters('filters');
    var reportTitle = jQuery("#reportTitle").val();
    var SelectedReportId = jQuery("#SelectedReportId").val();
    var sort = jQuery("#sortData").val();
    var columns = [];
    jQuery('div.selected ul.selected li.ui-element').each(function (a, value) {
        var title = jQuery(value).attr('title');
        jQuery('select#columns option').each(function (b, option) {
            if (jQuery(option).text() == title) {
                columns.push(jQuery(option).val());
            }
        });
    });
    var cases_category = jQuery("#cases_category").val();
    var limits = jQuery("#limits").val();
    var report_type = jQuery("#report_type").val();
    var Client_Position = jQuery("#Client_Position").val();
    var take = jQuery("#take").val();
    var skip = jQuery("#skip").val();
    var newFormFilter = jQuery('#exportResultsForm');
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filter));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('caseSearchFilters')));
    jQuery('#reportTitle', newFormFilter).val(reportTitle);
    jQuery('#SelectedReportId', newFormFilter).val(SelectedReportId);
    jQuery('#sortData', newFormFilter).val(sort);
    jQuery('#columns', newFormFilter).val(JSON.stringify(columns));
    jQuery('#cases_category', newFormFilter).val(cases_category);
    jQuery('#limits', newFormFilter).val(limits);
    jQuery('#report_type', newFormFilter).val(report_type);
    jQuery('#Client_Position', newFormFilter).val(Client_Position);
    jQuery('#report_type', newFormFilter).val(type);
    jQuery('#take', newFormFilter).val(take);
    jQuery('#skip', newFormFilter).val(skip);
    newFormFilter.attr('action', getBaseURL() + 'reports/report_builder_' + type.toLowerCase()).submit();
}


function getTranslation(fieldValue) {
    return _lang.case_columns[fieldValue];
}

function rename(dataSerach, sharedWithUsers) {
    name = prompt(_lang.enter_name_report);
    if ('null' != name && "" != name) {
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'reports/report_builder',
            data: {routine: 'save_report', rename: 'true', dataSerach: dataSerach, reportName: name, Watchers_Users: sharedWithUsers},
            success: function (response) {
                if (response.result) {
                    pinesMessage({ty: 'information', m: _lang.reportAdded});
                    var filters = document.getElementById('filters');
                    if (filters == null) {
                        jQuery('#filtersList', '#caseSearchFilters').append("<div class=' col-md-12 no-padding'><label class='control-label col-md-5' ></label> <div class='col-md-6 no-padding'><h5>" + _lang.chooseSavedReports + "</h5></div></div> <div class='col-md-12 no-padding'><label class='control-label col-md-5' ></label><div class='col-md-6 no-padding'><select id='filters'  class='form-control' name='filters' onchange='FiltersAction(value)'><option >  </option></select></div></div>")
                    }
                    jQuery('#filters', '#caseSearchFilters').append("<option value=" + response.id + ">" + response.keyName + "</option>");


                } else {
                    if (response.error === '300') {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.filtererror});
                    } else if (response.error === '400') {
                        pinesMessage({ty: 'error', m: response.UserName + _lang.alreadyShared + response.reportName});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

}
function delete_report() {
    var report_id = jQuery("#filters").val();
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'reports/report_builder',
        data: {routine: 'delete_report', report_id: report_id},
        success: function (response) {
            if (response == true) {
                pinesMessage({ty: 'success', m: _lang.deleteReportSuccessfull});
                window.location = getBaseURL() + 'reports/report_builder/';
            } else {
                pinesMessage({ty: 'error', m: _lang.deleteReportFailed});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function FiltersAction(filters) {
    if (undefined !== filters)
        window.location = getBaseURL() + 'reports/report_builder_view/' + filters;
}
function save_action(action) {
    var reportTitle = jQuery("#reportTitle").val();
    var report_id = jQuery("#filters").val();
    var sort = jQuery("#sortData").val();
    var title = '';
    var routine = '';
    if (action === 'rename') {
        title = _lang.save_as;
        routine = 'save_report';
    } else if (action === 'save') {
        title = _lang.saveReport;
        routine = 'save_report';
    } else if (action === 'edit') {
        title = _lang.edit;
        routine = 'edit_report';
    }
    if (undefined != dataSearch && dataSearch != '') {
        $dialogId = jQuery("#sharedWithDialog");
        $dialogId.html('<div align="center"><img height="18" src="assets/images/icons/16/loader-submit.gif" /></div>');
        jQuery.ajax({
            url: getBaseURL() + 'reports/report_builder/',
            type: 'POST',
            dataType: 'JSON',
            data: {routine: 'report_shared_with', SelectedReportId: jQuery('#SelectedReportId').val()},
            success: function (response) {
                if (response) {
                    $dialogId.dialog({
                        autoOpen: true,
                        buttons: [{
                                text: _lang.save,
                                'class': 'btn btn-info',
                                click: function () {
                                    var dataIsValid = jQuery("form#sharedWithForm").validationEngine('validate');
                                    if (dataIsValid) {
                                        var that = this;
                                        jQuery("#routine", '#sharedWithForm').val(routine);
                                        jQuery("#report_id", '#sharedWithForm').val(report_id);
                                        jQuery('#sortData', '#sharedWithForm').val(sort);
                                        var formData = jQuery("#sharedWithForm", $dialogId).serialize();
                                        jQuery.ajax({
                                            dataType: 'JSON',
                                            type: 'POST',
                                            url: getBaseURL() + 'reports/report_builder_save',
                                            data: formData,
                                            success: function (response) {
                                                if (response.result) {
                                                    jQuery(that).dialog("close");
                                                    pinesMessage({ty: 'information', m: _lang.reportAdded});
                                                    var filters = document.getElementById('filters');
                                                    if (action === 'edit') {
                                                        window.location = getBaseURL() + 'reports/report_builder_view/' + report_id;
                                                    } else {
                                                        if (filters == null) {
                                                            jQuery('#filtersList', '#caseSearchFilters').append("<div class='col-md-12'><label class='control-label col-md-5' ></label> <div class='col-md-6 no-padding'><h5>" + _lang.chooseSavedReports + "</h5></div></div> <div class='col-md-12'><label class='control-label col-md-5' ></label><div class='col-md-6 no-padding'><select id='filters'  class='form-control' name='filters' onchange='FiltersAction(value)'><option ></option></select></div></div>")
                                                        }
                                                        jQuery('#filters', '#caseSearchFilters').append("<option  value=" + response.id + ">" + response.keyName + "</option>");
                                                        window.location = getBaseURL() + 'reports/report_builder_view/' + response.id;
                                                    }
                                                } else {
                                                    if (response.error === '400') {
                                                        pinesMessage({ty: 'error', m: response.UserName + _lang.alreadyShared + response.reportName});
                                                    } else {
                                                        var errorMsg = '';
                                                        for (i in response.validationErrors) {
                                                            errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                        }
                                                        if (errorMsg != '') {
                                                            pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                        }
                                                    }

                                                }
                                            },
                                            error: defaultAjaxJSONErrorsHandler
                                        });
                                    }

                                }
                            },
                            {
                                text: _lang.cancel,
                                'class': 'btn btn-link',
                                click: function () {
                                    if (jQuery("#sharedWithForm", $dialogId).length)
                                        jQuery("#sharedWithForm", $dialogId)[0].reset();
                                    jQuery(this).dialog("close");
                                }
                            }],
                        close: function () {
                            if (jQuery("#sharedWithForm", $dialogId).length)
                                jQuery("#sharedWithForm", $dialogId)[0].reset();
                        },
                        open: function () {
                            jQuery(this).removeClass('d-none');
                            jQuery(window).bind('resize', (function () {
                                resizeNewDialogWindow(jQuery(this), '50%', '320');
                            }));
                            resizeNewDialogWindow(jQuery(this), '50%', '320');
                        },
                        draggable: true,
                        modal: false,
                        title: title,
                        resizable: true,
                        responsive: true
                    });
                    $dialogId.html(response.html);
                    jQuery('#dataSerach', $dialogId).val(JSON.stringify(dataSearch));
                    if (action === 'edit') {
                        jQuery('#reportName', $dialogId).val(reportTitle);
                    }
                    jQuery('#lookupUsers', '#sharedWithDialog').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
                            request.term = request.term.trim();
                            jQuery.ajax({url: getBaseURL() + 'users/autocomplete/active', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                                    if (data.length < 1) {
                                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {user_id: -1, term: request.term}}]);
                                    } else {
                                        response(jQuery.map(data, function (item) {
                                            return {label: item.firstName + ' ' + item.lastName, value: '', record: item}
                                        }));
                                    }
                                }});
                        }, minLength: 2, select: function (event, ui) {
                            if (ui.item.record.id > 0) {
                                setNewCaseMultiOption(jQuery('#selected_users', '#sharedWithDialog'), {id: ui.item.record.id, value: ui.item.record.firstName + ' ' + ui.item.record.lastName, name: 'Watchers_Users'});
                            }
                        }});

                }
            },
            complete: function () {

            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
var EXCEL = 'EXCEL';
var PDF = 'PDF';
var RTF = 'RTF';
var gridDataSource = {};

function checkWhichTypeOfFilterIUseAndReturnFilters(type) {
    var filtersForm = jQuery('#caseSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('caseSearchFilters', '.', true);
    var filters = '';
    if (type == 'filters') {
        filters = searchFilters.filter;
        filters.customFields = searchFilters.customFields;
        enableAll(filtersForm);
    }
    return filters;

}
function submitForm(submit) {
    var selectColumns = [];
    jQuery('div.selected ul.selected li.ui-element').each(function (a, value) {
        var title = jQuery(value).attr('title');
        jQuery('select#columns option').each(function (b, option) {
            if (jQuery(option).text() == title) {
                selectColumns.push(jQuery(option).val());
            }
        });
    });
    if (validateInteger() == true) {
        jQuery("#report_type").val('HTML');
        var filters_versions = '';
        if (jQuery("#filters").length > 0)
            filters_versions = jQuery("#filters").val();
        var edit_save = '';
        var tableColumns = [];
        if (jQuery("#columns").val()) {
            jQuery("#searchResults").html('');
            jQuery.each(selectColumns, function (i, item) {
                if (item === 'case_id') {
                    array_push = {field: item, template: "<a href=" + getBaseURL() + "#= category == 'IP' ? 'intellectual_properties/' : 'cases/' #"  + "edit/#= case_id.substring(1,case_id.length) #>#= case_id #</a>", title: getTranslation('case_id'), width: '140px', sortable: true};
                } else if (item === 'opponentNationalities') {
                    array_push = {field: item, title: getTranslation(item), width: '140px', sortable: false};
                } else if (item === 'client_foreign_name') {
                    array_push = {field: item, title: _lang.clientForeignName, width: '140px', sortable: false};
                } else if (item === 'opponent_foreign_name') {
                    array_push = {field: item, title: _lang.opponentForeignName, width: '140px', sortable: false};
                } else if (item === 'caseValue') {
                    array_push = {field: item, title: getTranslation(item), template: "#= (caseValue==null)?'':((caseValue==0)?'0.00':number_format(caseValue,2, '.', ',') )#", width: '140px', sortable: true};
                } else if (item === 'judgmentValue') {
                    array_push = {field: item, title: getTranslation(item), template: "#= (judgmentValue==null)?'':((judgmentValue==0)?'0.00':number_format(judgmentValue,2, '.', ',')) #", width: '140px', sortable: true};
                } else if (item === 'recoveredValue') {
                    array_push = {field: item, title: getTranslation(item), template: "#= (recoveredValue==null)?'':((recoveredValue==0)?'0.00':number_format(recoveredValue,2, '.', ','))  #", width: '140px', sortable: true};
                } else if (item === 'latest_development') {
                    array_push = {field: item, title: getTranslation(item), template: '#= (latest_development!=null&&latest_development!="") ? ((latest_development.length>40) ? ((_lang.languageSettings[\'langDirection\'] === \'rtl\') ? "..." + latest_development.substring(0,40) : latest_development.substring(0,40)+ "...") : latest_development) : ""#', width: '320px'};
                } else if (item === 'description') {
                    array_push = {field: item, title: getTranslation(item), template: '#= (description!=null&&description!="") ? ((description.length>40)? description.substring(0,40)+"..." : description) : ""#', width: '320px'};
                } else if (item === 'judgment') {
                    array_push = {field: item, title: getTranslation(item), template: '#= (judgment!=null&&judgment!="") ? ((judgment.length>40)? judgment.substring(0,40)+"..." : judgment) : ""#', width: '320px'};
                } else if (item === 'reasons_of_postponement_of_last_hearing') {
                    array_push = {field: item, title: getTranslation(item), template: '#= (reasons_of_postponement_of_last_hearing!=null&&reasons_of_postponement_of_last_hearing!="") ? ((reasons_of_postponement_of_last_hearing.length>40)? reasons_of_postponement_of_last_hearing.substring(0,40)+"..." : reasons_of_postponement_of_last_hearing) : ""#', width: '320px'};
                }
                 else if (item === 'statusComments') {
                    array_push = {field: item, title: getTranslation(item), template: '#= (statusComments!=null&&statusComments!="") ? ((statusComments.length>40)? statusComments.substring(0,40)+"..." : statusComments) : ""#', width: '320px'};
                } else if (item === 'litigationExternalRef') {
                    array_push = {field: item, title: getTranslation(item), template: '#= litigationExternalRef==null||litigationExternalRef==""  ? "" : (litigationExternalRef.trim().substr(litigationExternalRef.trim().length - 1)=="," ? litigationExternalRef.trim().slice(0,-1) : litigationExternalRef) #', width: '320px'};
                } else if (!isNaN(parseInt(item * 1))) {
                    array_push = {field: 'custom_' + item, title: customData[item], template: '#= custom_' + item + '!==null ? (custom_' + item + '.length>255 ? custom_' + item + '.substring(0, 255) + "..." :custom_' + item + ' ):"" #', width: '140px', sortable: false};
                } else {
                    array_push = {field: item, title: getTranslation(item), width: '140px', sortable: true};
                }
                tableColumns.push(array_push);
            });
        } else {
            array_push = {field: 'case_id', template: "<a href=" + getBaseURL() + "#= category == 'IP' ? 'intellectual_properties/' : 'cases/' #"  + "edit/#= case_id.substring(1,case_id.length) #>#= case_id #</a>", title: getTranslation('case_id'), width: '140px', sortable: true};
            tableColumns.push(array_push);
            array_push = {field: 'subject', title: getTranslation('subject'), width: '140px', sortable: true};
            tableColumns.push(array_push);
        }
        var gridDataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + 'reports/report_builder_list',
                    dataType: "JSON",
                    type: "POST",
                    complete: function (XHRObj) {
                        var response = jQuery.parseJSON(XHRObj.responseText || "null");
                        dataSearch = response.dataSerach;
                        jQuery('#searchResults').removeClass('disabled-kendoGrid');
                        jQuery('#submitBtn2').removeAttr('disabled');
                        jQuery('#take', '#caseSearchFilters').val(response.dataSerach.take);
                        jQuery('#skip', '#caseSearchFilters').val(response.dataSerach.skip);
                    }
                },
                parameterMap: function (options, operation) {
                    jQuery('#submitBtn2').attr('disabled', 'disabled');
                    jQuery('#searchResults').addClass('disabled-kendoGrid');
                    var currentSorting = JSON.stringify(gridDataSource.sort());
                    if (undefined != currentSorting) {
                        jQuery("#sortData").val(currentSorting);
                    }
                    options.filter = checkWhichTypeOfFilterIUseAndReturnFilters('filters');
                    options.reportTitle = jQuery("#reportTitle", '#caseSearchFilters').val();
                    options.SelectedReportId = jQuery("#SelectedReportId", '#caseSearchFilters').val();
                    options.columns = [];
                    jQuery('div.selected ul.selected li.ui-element').each(function (a, value) {
                        var title = jQuery(value).attr('title');
                        jQuery('select#columns option').each(function (b, option) {
                            if (jQuery(option).text() == title) {
                                options.columns.push(jQuery(option).val());
                            }
                        });
                    });
                    options.cases_category = jQuery("#cases_category", '#caseSearchFilters').val();
                    options.limits = jQuery("#limits", '#caseSearchFilters').val();
                    options.report_type = jQuery("#report_type", '#caseSearchFilters').val();
                    options.Client_Position = jQuery("#Client_Position", '#caseSearchFilters').val();
                    options.submit = submit;
                    options.sortData = jQuery("#sortData", '#caseSearchFilters').val();
                    return options;
                }
            },
            schema: {
                type: "json",
                data: "data",
                total: "totalRows",
                model: {
                    id: "case_id"
                },
                parse: function(response) {
                    var rows = [];
                    if(response.data){
                        var data = response.data;
                        rows = response;
                        rows.data = [];
                        for (var i = 0; i < data.length; i++) {
                            var row = data[i];
                            row['latest_development'] = escapeHtml(row['latest_development']);
                            row['description'] = escapeHtml(row['description']);
                            row['statusComments'] = escapeHtml(row['statusComments']);
                            row['litigationExternalRef'] = escapeHtml(row['litigationExternalRef']);
                            for(var key in row){
                                if(key.includes('custom_')){
                                    row[key] = escapeHtml(row[key]);
                                }
                            }
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr);
            },
            sort: jQuery.parseJSON(jQuery("#sortData").val()),
            pageSize: jQuery("#take", '#caseSearchFilters').val() ? jQuery("#take", '#caseSearchFilters').val() : 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });

        var contractSearchGridOptions = {
            autobind: true,
            dataSource: gridDataSource,
            scrollable: true,
            editable: false,
            filterable: false,
            height: 400,
            columns: tableColumns,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            sortable: {mode: "multiple"},
            toolbar: [{
                    name: "toolbar-menu",
                    template:
                            '<div class="col-md-1 pull-right">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                            + _lang.actions + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">'
                            + edit_save
                            + '<a class="dropdown-item" onclick="exportCasesTo(' + EXCEL + ')" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                            + '<div class="dropdown-divider" style="margin:4px !important"></div>'
                            + '<a class="dropdown-item" onclick="exportCasesTo(' + PDF + ')" title="' + _lang.exportToPDF + '" class="" href="javascript:;" >' + _lang.exportToPDF + '</a>'
                            + '</div>'
                            + '</div>'
                            + '</div>'
                }]
        };
        if (filters_versions === "") {
            jQuery('#saveEditDiv').html('');
        }
        jQuery('#searchResults').empty();
        jQuery('#searchResults').kendoGrid(contractSearchGridOptions);
        jQuery('#save-btn').removeClass('d-none');
        jQuery('#filtersFormWrapper').hide();
    }
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function advancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        loadEventsForFilters();
        jQuery('#filtersFormWrapper').slideDown();
    } else {
        scrollToId('#filtersFormWrapper');
    }
}
function fill_between_dates(DateOpertator) {
    var DateOpertator = jQuery('#' + DateOpertator);
    DateOpertator.val('cast_between');
    var container = DateOpertator.parent().parent();
    var endDate = jQuery('.end-date-filter', container);
    jQuery('.start-date-operator', container).val('cast_gte');
    endDate.removeClass('d-none');
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['arrivalDateValue', 'caseArrivalDateValue', 'dueDateValue', 'arrivalDateEndValue', 'caseArrivalDateEndValue', 'dueDateEndValue', 'closedOnValue', 'closedOnEndValue', 'constitutionDateEndValue', 'sentenceDateEndValue', 'due_date']});
    userLookup('assigneeValue');
    contactAutocompleteMultiOption('contactOutsourceToValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('contributorsHelpersValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('contactContributorValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    companyAutocompleteMultiOption(jQuery('#companyValue', '#caseSearchFilters'), resultHandlerAfterCompaniesLegalCasesAutocomplete);
    contactAutocompleteMultiOption('contactValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('referredByNameValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('requestedByNameValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    jQuery('#clientNameOperator', '#caseSearchFilters').change(function () {
        jQuery('#clientNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#clientForeignNameOpertator', '#caseSearchFilters').change(function () {
        jQuery('#clientForeignNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#clientNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientNameOperator', '#caseSearchFilters').val();
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
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
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
        minLength: 0,
        select: function (event, ui) {
        }
    });
    jQuery('#clientForeignNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#clientForeignNameOpertator', '#caseSearchFilters').val();
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
    jQuery('#opponentNamesOpertator', '#caseSearchFilters').change(function () {
        jQuery('#opponentNamesValue', '#caseSearchFilters').val('');
    });
    jQuery('#opponentForeignNameOpertator', '#caseSearchFilters').change(function () {
        jQuery('#opponentForeignNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#opponentNamesValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#opponentNamesOpertator', '#caseSearchFilters').val();
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
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
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
        minLength: 0,
        select: function (event, ui) {
        }
    });
    jQuery('#opponentForeignNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#opponentForeignNameOpertator', '#caseSearchFilters').val();
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
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
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
}
function changePerPage(selectedNum) {
    var containerForm = jQuery('#caseSearchFilters');
    jQuery('#take', containerForm).val(selectedNum.options[selectedNum.selectedIndex].value);
    jQuery('#skip', containerForm).val('0');
    containerForm.attr('action', getBaseURL() + 'reports/report_builder/').submit();
}
function ResetPage() {
    window.location = getBaseURL() + 'reports/report_builder/';
}
