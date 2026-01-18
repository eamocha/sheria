function initializePetitionsOppositionsGrid() {
    var petitionsOppositionsGridDataSource = {
        transport: {
            read: {
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('petitionsOppositionsGrid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.legal_case_id = jQuery('#legalCaseId').val();
                    // options.returnData = 1;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {type: "integer"},
                    type: {type: "string"},
                    description: {type: "string"},
                    arrivalDate: {type: "date"},
                    dueDate: {type: "date"},
                    agent: {type: "string"},
                    assignee: {type: "string"},
                    result: {type: "string"}
                }
            },
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['description'] = escapeHtml(row['description']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: 20,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    };
    var petitionsOppositionsGridOptions = {
        autobind: true,
        dataSource: petitionsOppositionsGridDataSource,
        columns: [
            {field: 'id', title: ' ', filterable: false, sortable: false, template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="petitionOppositionEditForm(\'#= id #\')">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')">' + _lang.delete + '</a>' +
                        '</div></div>', width: '70px'
            },
            {field: "type", title: _lang.type, width: '220px'},
            {field: "description", title: _lang.description, template: '<a href="javascript:;" onclick="petitionOppositionEditForm(\'#= id #\')">#=  description#</a>', width: '220px'},
            {field: "arrivalDate", title: _lang.arrival_date, format: "{0:yyyy-MM-dd}", width: '220px'},
            {field: "dueDate", title: _lang.dueDate, format: "{0:yyyy-MM-dd}", width: '220px'},
            {field: "agent", title: _lang.agent, width: '220px'},
            {field: "assignee", title: _lang.assignee, width: '220px'},
            {field: "result", title: _lang.result, width: '220px'}
        ],
        editable: false,
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {
            mode: "multiple"
        },
        selectable: "single",
        toolbar: [{
                name: "quick-add-edit",
                template: '<div class="col-md-1 pull-right no-padding margin-bottom-10">'
                        + '<div class="btn-group pull-right">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu dropdown-menu-right" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" id="IPBtn" class="" href="javascript:;" onclick="petitionOppositionAddForm()">' + _lang.addPetitionOpposition + '</a>'
                        + '<a class="dropdown-item" onclick="exportToExcel()" title="' + _lang.exportToExcel + '" class="" href="javascript:;" >' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'

            }],
        columnMenu: {messages: _lang.kendo_grid_sortable_messages}
    };
    var petitionsOppositionsGrid = jQuery('#petitionsOppositionsGrid');
    petitionsOppositionsGrid.kendoGrid(petitionsOppositionsGridOptions);
}

jQuery(document).ready(function () {
    initializePetitionsOppositionsGrid();
});

// function advancedSearchFilters() {
//     if (!jQuery('#filtersFormWrapper').is(':visible')) {
//         loadEventsForFilters();
//         jQuery('#filtersFormWrapper').slideDown();
//         if (jQuery('#submitAndSaveFilter').is(':visible')) {
//             jQuery("#advancedSearchFields").removeClass('d-none');
//         }
//     }
//     jQuery('html, body').animate({scrollTop: 0}, 0);
// }

// function loadEventsForFilters() {
//     makeFieldsDatePicker({fields: ['arrivalDateValue', 'dueDateValue']});
//     userLookup('assigneeValue');
//     jQuery('#typeValue').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"});
//     jQuery('#agentTypeOpertator', '#petitionOppositionSearchFilters').change(function () {
//         jQuery('#agentValue', '#petitionOppositionSearchFilters').val('');
//     });
//     jQuery('#agentValue', '#petitionOppositionSearchFilters').autocomplete({
//         autoFocus: true,
//         delay: 600,
//         source: function (request, response) {
//             request.term = request.term.trim();
//             var lookupType = jQuery('select#agentTypeOpertator', '#petitionOppositionSearchFilters').val();
//             if (lookupType !== '') {
//                 lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
//                 jQuery.ajax({
//                     url: getBaseURL() + lookupType + '/autocomplete',
//                     dataType: "json",
//                     data: request,
//                     error: defaultAjaxJSONErrorsHandler,
//                     success: function (data) {
//                         if (data.length < 1) {
//                             response([{
//                                     label: _lang.no_results_matched_for.sprintf([request.term]),
//                                     value: '',
//                                     record: {
//                                         id: -1,
//                                         term: request.term
//                                     }
//                                 }]);
//                         } else {
//                             response(jQuery.map(data, function (item) {
//                                 if (lookupType == 'contacts') {
//                                     return {
//                                         label: item.firstName + ' ' + item.lastName,
//                                         value: item.firstName + ' ' + item.lastName,
//                                         record: item
//                                     }
//                                 } else if (lookupType == 'companies') {
//                                     return {
//                                         label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
//                                         value: item.name,
//                                         record: item
//                                     }
//                                 }
//                             }));
//                         }
//                     }
//                 });
//             }
//         },
//         response: function (event, ui) {
//         },
//         minLength: 1,
//         select: function (event, ui) {
//         }
//     });
// }

// function hideAdvancedSearch() {
//     jQuery('#filtersFormWrapper').slideUp();
// }

function exportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    // var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    // jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));

    newFormFilter.attr('action', getBaseURL() + 'export/ip_petitions_oppositions/' + intellecutalPropertyId).submit();
}

// function checkWhichTypeOfFilterIUseAndReturnFilters() {
//     var filtersForm = jQuery('#petitionOppositionSearchFilters');
//     disableEmpty(filtersForm);
//     var searchFilters = form2js('petitionOppositionSearchFilters', '.', true);
//     var filters = '';
//     filters = searchFilters.filter;
//     filters.customFields = searchFilters.customFields;
//     enableAll(filtersForm);
//     return filters;
// }

function petitionOppositionAddForm() {
    petitionOppositionForm();
}

function petitionOppositionEditForm(id) {
    petitionOppositionForm(id);
}

function petitionOppositionForm(id) {
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    id = id || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'intellectual_properties/' + (id ? 'petition_opposition_edit/' + id : 'petition_opposition_add'),
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#petition-opposition-dialog').length <= 0) {
                    jQuery('<div id="petition-opposition-dialog"></div>').appendTo("body");
                    var petitionOppositionDialog = jQuery('#petition-opposition-dialog');
                    petitionOppositionDialog.html(response.html);
                    jQuery('.modal', petitionOppositionDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                    var petitionOppositionId = jQuery("#id", petitionOppositionDialog).val();
                    jQuery("#save-petition-opposition-btn", petitionOppositionDialog).click(function () {
                        petitionOppositionFormSubmit(petitionOppositionDialog, petitionOppositionId);
                    });
                    jQuery(petitionOppositionDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            petitionOppositionFormSubmit(petitionOppositionDialog, petitionOppositionId);
                        }
                    });
                    petitionOppositionFormEvents(petitionOppositionDialog);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('#legalCaseId', '#petition-opposition-form').val(intellecutalPropertyId);
        },
        error: defaultAjaxJSONErrorsHandler
    });

}

function petitionOppositionFormEvents(container) {
    jQuery('.select-picker', '#petition-opposition-dialog').selectpicker();
    setDatePicker('#arrival-date', container);
    setDatePicker('#due-date', container);
    notifyMeBeforeEvent({'input': 'due-date-input', 'inputContainer': 'due-date'}, container, true);
    agentInitialization(container);
    initializeModalSize(container);
    jQuery('.modal-body',container).on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(container);
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#description', container).focus();
        jQuery('#pendingReminders').parent().popover('hide');
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.date-picker', container).bootstrapDP("remove");
            jQuery('.modal', container).modal('hide');
        }
    });
}

function petitionOppositionFormSubmit(container, id) {
    id = id || false;
    var formData = jQuery("form#petition-opposition-form", container).serializeArray();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'intellectual_properties/' + (id ? 'petition_opposition_edit/' + id : 'petition_opposition_add'),
        success: function (response) {
            if (response.totalNotifications >= 1) {
                jQuery('#pendingNotifications').css('display', 'inline-block').text(response.totalNotifications);
            } else {
                jQuery('#pendingNotifications').html('');
            }
            jQuery('.inline-error', '#petition-opposition-dialog').addClass('d-none');
            if (response.result) {
                if (jQuery('#petitionsOppositionsGrid').length) {
                    jQuery('#petitionsOppositionsGrid').data("kendoGrid").dataSource.read();
                }
                if (jQuery('#notify-me-before-container', container).is(':visible')) {
                    loadUserLatestReminders('refresh');
                }
                pinesMessage({ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : _lang.recordAddedSuccessfully});
                jQuery('.modal', '#petition-opposition-dialog').modal('hide');
            } else {
                for (i in response.validationErrors) {
                    jQuery("div", '#petition-opposition-dialog').find("[data-field=" + i + "]").removeClass('d-none').html(response.validationErrors[i]);
                }
            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteSelectedRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'intellectual_properties/petition_opposition_delete',
            type: 'POST',
            dataType: 'JSON',
            data: {ipPetitionOppositionId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// removed successfuly
                        ty = 'information';
                        m = _lang.deleteRecordSuccessfull;
                        break;
                    case 101:	// could not remove record
                        m = _lang.recordNotDeleted;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#petitionsOppositionsGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
