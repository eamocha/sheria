var timeTrackingGridOptions = null, userRatesGridOptions = null, clientName, $timeCategoriesGridOptions, $bulkTimeTrackingGrid = null;
var splittedItems = [];
function accessFolderByUrl() {
    jQuery('li#' + 'timeTrackingTab', '#timeLogsTabs').addClass('k-state-active');
    searchTimeLogs();
}

function searchTimeLogs(container) {
    container = container || "#main-container";
    $timeTrackingGrid = jQuery('#timeTrackingGrid',container);
    if (undefined === $timeTrackingGrid.data('kendoGrid')) {
        $timeTrackingGrid.kendoGrid(timeTrackingGridOptions);
        return false;
    }
    $timeTrackingGrid.data('kendoGrid').dataSource.read();
    return false;
}

function searchUserRates() {
    $usersRatesGrid = jQuery('#usersRatesGrid');
    if (undefined === $usersRatesGrid.data('kendoGrid')) {
        $usersRatesGrid.kendoGrid(userRatesGridOptions);
        customGridToolbarCSSButtons();
        return false;
    }
    $usersRatesGrid.data('kendoGrid').dataSource.read();
    return false;
}

function addUserRate(caseId) {
    $userRateDialog = jQuery("#userRateDialog");
    if ($userRateDialog.length == 0) {
        $userRateDialog = jQuery('<div id="userRateDialog"></div>').addClass("loading").appendTo("body")
    }
    jQuery.ajax({
        url: getBaseURL() + controller + '/user_rates_per_hour/',
        type: 'POST',
        dataType: 'JSON',
        data: {caseId: caseId, requestType: 'getRateForm'},
        beforeSend: function () {
        },
        success: function (response) {
            $userRateDialog.dialog({
                autoOpen: true,
                buttons: [
                    {text: _lang.save,
                        "class": "btn btn-info",
                        click: function () {
                            var dataIsValid = jQuery("#userRateForm").validationEngine('validate');
                            if (dataIsValid) {
                                var that = this;
                                var formData = jQuery("#userRateForm").serialize();
                                jQuery.ajax({
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL() + controller + '/user_rate_add/' + caseId,
                                    success: function (response) {
                                        if (response.result) {
                                            jQuery(that).dialog("close");
                                            if (jQuery('#usersRatesGrid').is(':visible')) {
                                                jQuery('#usersRatesGrid').data('kendoGrid').dataSource.read();
                                            } else if (jQuery('#timeTrackingGrid').is(':visible')) {
                                                jQuery('#timeTrackingGrid').data('kendoGrid').dataSource.read();
                                            }
                                        } else {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
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
                        "class": "btn btn-default btn-link",
                        click: function () {
                            resetUserRatesForm();
                            jQuery(this).dialog("close");
                        }
                    }
                ],
                close: resetUserRatesForm,
                open: function () {
                    jQuery(this).removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(jQuery(this), '50%', '300');
                    }));
                    resizeNewDialogWindow(jQuery(this), '50%', '300');

                },
                draggable: true, modal: false, resizable: true, responsive: true, title: _lang.addUserRate
            });

            $userRateDialog.html(response.html);
            if ($userRateDialog.hasClass('ui-dialog-content')) {
                userLookup("user_id", "userId", fetchUserRate, 'active');
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function fetchUserRate(record) {
    var organizationId = jQuery('#organizations', '#userRateForm').val();
    var userId = record.id;
    if (undefined !== userId) {
        jQuery.ajax({
            url: getBaseURL() + controller + '/user_rates_per_hour/',
            type: 'POST',
            dataType: 'JSON',
            data: {requestType: 'getRatePerCurrUser', userId: userId, caseId: caseId, organizationId: organizationId},
            success: function (response) {
                var userRate = response.userRate;
                jQuery('#ratePerHour', '#userRateForm').val(userRate);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function resetUserRatesForm() {
    $userRateForm = jQuery("form#userRateForm", jQuery("#userRateDialog"));
    $userRateForm.validationEngine("hide");
    if (!jQuery('#userRateDialog').empty()) {
        $userRateForm[0].reset();
    }
    jQuery(".invalid", $userRateForm).each(function (e, t) {
        jQuery(t).removeClass("invalid")
    });
    jQuery('#user_id', $userRateForm).val('');
}

jQuery(document).ready(function () {
    try {
        var timeTrackingGridDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + controller + ( myTimeLogs ? "/my_time_logs/" : "/time_logs/" ) + caseId,
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                        animateDropdownMenuInGridsV2('timeTrackingGrid');
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {id: "id",
                    fields: {
                        id: {editable: false, type: "number"},
                        user_id: {type: "number"},
                        billingStatus: {type: "string"},
                        task_id: {type: "number"},
                        legal_case_id: {type: "number"},
                        logDate: {type: "date"},
                        effectiveEffort: {type: "string"},
                        createdBy: {type: "number"},
                        createdOn: {type: "date"},
                        taskId: {type: "string"},
                        taskSummary: {type: "string"},
                        legalCaseId: {type: "string"},
                        legalCaseSummary: {type: "string"},
                        worker: {type: "string"},
                        inserter: {type: "string"},
                        comments: {type: "string"},
                        timeTypeName: {type: "string"},
                        timeStatus: {type: "string"},
                        allRecordsClientName: {type: "string"}
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
                            row['worker'] = escapeHtml(row['worker']);
                            row['inserter'] = escapeHtml(row['inserter']);
                            row['comments'] =  replaceHtmlCharacter(row['comments']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
        timeTrackingGridOptions = {
            autobind: true,
            dataSource: timeTrackingGridDataSrc,
            columns: [
                {title: _lang.actions, field: 'id',
                    template: '<div class="wraper-actions"><div class="list-of-actions">' +
                        '<div class="dropdown">' + gridActionIconHTML + '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<li><a href="javascript:;" onclick="logActivityDialog(\'#= id #\',' + undefined + ',\'hideCase\')">' + _lang.viewEdit + '</a></li>' +
                        '<li><a href="javascript:;" onclick="deleteActivityDialog(\'#= id #\')">' + _lang.delete_log + '</a></li>' +
                        '</ul></div>'+
                        '</div></div>',
                    sortable: false, width: '60px'},
                {field: "logDate", format: "{0:yyyy-MM-dd}", template: "#= (logDate == null) ? '' : kendo.toString(logDate, 'yyyy-MM-dd') #", title: _lang.date, width: '85px'},
                {field: "worker", title: _lang.user, width: '120px', template: '#= (worker!=null ? worker : "") #'},
                {field: "allRecordsClientName", title: _lang.client, width: '140px'},
                {field: "effectiveEffort", title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort})+" ("+ effectiveEffort + "h)"#', width: '162px'},
                {field: "ratePerHour", title: _lang.rate, sortable: false, width: "85px", template: '#= timeStatus == "billable" ? (ratePerHour == 0 ? \'\' : ratePerHour) : \'\' #'},
                {field: "comments", title: _lang.comments, template: '#= (comments!=null&&comments!="") ? comments.substring(0,40)+"..." : ""#', width: '320px'},
                {field: "billingStatus", template: '<span class="#= billingStatus == "invoiced" ? "lightGreen" : billingStatus == "to-invoice" ? "red" : billingStatus == "reimbursed" ? "darkGreen" : "" #">#= billingStatus #</span>', width: '95px', title: _lang.status},
                {field: "timeTypeName", title: _lang.timeType, width: '100px'},
                {field: "timeStatus", title: _lang.timeStatus,template: '#= timeStatus == "billable" ? helpers.capitalizeFirstLetter(getTimeLogsStatusTranslation(timeStatus)) : helpers.capitalizeFirstLetter(getExpenseStatusTranslation("non-billable")) #', width: '125px'},
                {field: "inserter", title: _lang.createdBy, width: '130px', template: '#= (inserter!=null ? inserter : "") #'},
                {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '140px'},
            ],
            editable: "", filterable: false, height: 500,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true}, reorderable: true, resizable: true, scrollable: true, sortable: {mode: "multiple"}, selectable: "single",
            toolbar: [{
                    name: "task-grid-toolbar",
                    template: '<div class="col-md-4 no-padding margin-bottom-10">'
                            + '<h4 class="col-md-5 no-padding">' + _lang.time_tracking + '</h4>'
                            + '</div>'
                            + '<div class="col-md-2 pull-right margin-bottom-10">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                            + _lang.actions + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                            + '<li><a href="javascript:;" class="log-case-time" onclick="logCaseActivity(clientName)" >' + _lang.log_time + '</a></li>'
                            + '<li><a href="javascript:;" class="" onclick="bulkEditTimeLogs()">' + _lang.bulkEditTime + '</a></li>'
                            + '<li><a href="javascript:;" class=""  onclick="exportActivityLogsToExcel();">'
                            + _lang.exportToExcel + '</a></li>'
                            + '</ul>'
                            + '</div>'
                            + '</div>'
            }]
        };

        var userRatesDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + controller + "/user_rates_per_hour",
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    }
                },
                update: {
                    url: getBaseURL() + controller + "/user_rate_edit/" + caseId,
                    dataType: "jsonp",
                    type: "POST",
                    complete: function (XHRObj) {
                        jQuery('#usersRatesGrid').data('kendoGrid').dataSource.read();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" !== operation && options.models) {
                        return {
                            models: kendo.stringify(options.models)
                        };
                    }
                    options.requestType = 'readData';
                    options.caseId = caseId;
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
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
                        id: {editable: false, type: "integer"},
                        ratePerHour: {type: "integer", validation: {required: true}},
                        entityName: {editable: false, type: "string"},
                        currencyCode: {editable: false, type: "string"},
                        seniorityLevel: {editable: false, type: "string"},
                        firstName: {editable: false, type: "string"},
                        lastName: {editable: false, type: "string"},
                        actions: {editable: false}
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
                            row['entityName'] = escapeHtml(row['entityName']);
                            row['currencyCode'] = escapeHtml(row['currencyCode']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            },
            error: function (e) {
//				if (e.xhr.responseText.validationErrors == '')
                defaultAjaxJSONErrorsHandler(e.xhr)
            },
            pageSize: 20,
            serverPaging: true,
            serverFiltering: true,
            serverSorting: true
        });
        userRatesGridOptions = {
            autobind: true,
            dataSource: userRatesDataSrc,
            columns: [
                {field: "ratePerHour", title: _lang.ratePerHour, width: '156px', template: '#= timeStatus == "billable" ? ratePerHour : \'\' #'},
                {field: "entityName", template: '#= entityName# (' + '#=currencyCode#)', title: _lang.relatedEntity, width: '156px'},
                {field: "firstName", title: _lang.firstName, width: '120px'},
                {field: "lastName", title: _lang.lastName, width: '120px'},
                {field: "seniorityLevel", title: _lang.SeniorityLevel, width: '192px'},
                {field: "actions", template: '<a href="javascript:;" onclick="deleteRatesRow(\'#= id #\')"  title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>', sortable: false, title: _lang.actions, width: '65px'},
            ],
            editable: true,
            filterable: false,
            height: 480,
            pageable: {
                input: true,
                messages: _lang.kendo_grid_pageable_messages,
                numeric: false,
                refresh: true,
                pageSizes: [10, 20, 50, 100]
            },
            reorderable: true,
            resizable: true,
            scrollable: true,
            selectable: "single",
            sortable: {
                mode: "multiple"
            },
            columnMenu: {
                messages: _lang.kendo_grid_sortable_messages
            },
            toolbar: [{
                    name: "users-grid-toolbar",
                    template: '<div class="col-md-4 no-padding">'
                            + '<h4 class="col-md-11">' + _lang.userRatePerHour + '</h4>'
                            + '</div>'
                            + '<div class="col-md-2 pull-right">'
                            + '<div class="btn-group pull-right">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                            + _lang.actions + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<ul class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                            + '<li><a class="" onclick="addUserRate(\'' + caseId + '\');" >' + _lang.addUserRate + '</a></li>'
                            + '</ul>'
                            + '</div>'
                            + '</div>'
                },
                {name: "gridToolbarOpen", template: '<div class=" pull-right">'},
                {name: "save", text: _lang.save},
                {name: "cancel", text: _lang.cancel},
                {name: "gridToolbarClose", template: '</div>'}
            ]
        };
    } catch (e) {
    }

    accessFolderByUrl();
    jQuery('#timeLogsTabs').kendoTabStrip({
        collapsible: true,
        activate: function (e) {
            var $contentTarget = jQuery(e.contentElement);
            if ($contentTarget.attr('id') == 'timeLogsTabs-1') {
                searchTimeLogs();
            } else {
                searchUserRates();
            }
        },
        select: function (e) {
            var $contentTarget = jQuery(e.contentElement);
            var $tabVisited = document.location.hash;
            if ($contentTarget.attr('id') == 'timeLogsTabs-2') {
                $tabVisited = 'user-rates';
            } else {
                $tabVisited = 'time-logs';
            }
            document.location.hash = $tabVisited;
        }
        ,
        animation: {
            close: {
                duration: 25
            }
        }
    });
});

function deleteRatesRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        $usersRatesGrid = jQuery('#usersRatesGrid');
        jQuery.ajax({
            url: getBaseURL() + controller + '/delete_user_rate',
            type: 'POST',
            dataType: 'JSON',
            data: {rateId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// removed successfuly
                        ty = 'information';
                        m = _lang.selectedRecordDeleted;
                        break;
                    case 101:	// could not remove record
                        m = _lang.recordNotDeleted;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                $usersRatesGrid.data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function exportActivityLogsToExcel() {
    jQuery('#exportResultsForm').attr('action', getBaseURL() + 'export/case_time_tracking/' + caseId + (controller == 'cases' ? ('/' + myTimeLogs) : '')).submit();
}
function deleteActivityDialog(id) {
    confirmationDialog('confirmation_delete_selected_record', {
        resultHandler: function () {
            jQuery.ajax({
                url: getBaseURL() + "time_tracking/delete/" + id,
                type: "POST",
                dataType: "JSON",
                beforeSend: function () {
                    jQuery("#loader-global").show();
                },
                success: function (e) {
                    jQuery("#loader-global").hide();
                    if (e.result) {
                        pinesMessageV2({ty: "success", m: e.msg});
                        if (null != $bulkTimeTrackingGrid){
                            $bulkTimeTrackingGrid.data("kendoGrid").dataSource.read();
                        }
                    } else
                        pinesMessageV2({ty: "warning", m: e.msg});
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    });
}
function setTimeTrackingBillableDefaultStatus(caseId) {
    timeTrackingBillableIsChecked = timeTrackingBillableContainer.is(':checked');
    if (timeTrackingBillableIsChecked) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/add_client_and_update_case',
            type: "GET",
            data: {case_id: caseId},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.result) {
                    savetimeTrackingBillable(timeTrackingBillableIsChecked, response);
                } else {
                    var caseClientFormId = "#case-client-form-container";
                    if (jQuery(caseClientFormId).length <= 0) {
                        jQuery("<div id='case-client-form-container'></div>").appendTo("body");
                        var caseClientForm = jQuery(caseClientFormId);
                        caseClientForm.html(response.html);
                        clientInitialization(caseClientForm);
                        commonModalDialogEvents(caseClientForm);
                        jQuery('#client-type', caseClientForm).selectpicker();
                        jQuery(".close-dialog", caseClientForm).click(function () {
                            timeTrackingBillableContainer.prop('checked', !timeTrackingBillableIsChecked).removeAttr('disabled');
                        });
                        jQuery(document).keyup(function (e) {
                            if (e.keyCode == 27) {
                                timeTrackingBillableContainer.prop('checked', !timeTrackingBillableIsChecked).removeAttr('disabled');
                            }
                        });
                        jQuery("#form-submit", caseClientForm).click(function () {
                            submitTimeTrackingBillableDefaultStatus(caseClientForm,response);
                        });
                        jQuery(caseClientForm).find('input').keypress(function (e) {
                            // Enter pressed?
                            if (e.which == 13) {
                                submitTimeTrackingBillableDefaultStatus(caseClientForm, response);
                            }
                        });
                    }
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        savetimeTrackingBillable(timeTrackingBillableIsChecked);
    }
}
function submitTimeTrackingBillableDefaultStatus(container) {
    var formData = jQuery('#case-client-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'cases/add_client_and_update_case',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            timeTrackingBillableContainer.attr('disabled', 'disabled');
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                clientName = response.client.clientName;
                clientId = response.client.id;
                timeTrackingBillableContainer.removeAttr('disabled');
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                //load the client widgets
                clientTransactionsBalance({case: jQuery('#case-id', '#object-header').val(), client:  jQuery('#contact-company-id', container).val()}, jQuery('#client-account-status','#object-header'), jQuery('#controller','#object-header').val());
                jQuery(".modal", container).modal("hide");
                savetimeTrackingBillable(timeTrackingBillableIsChecked, response);
            } else {
                displayValidationErrors(response.validation_errors, container);
                pinesMessageV2({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                timeTrackingBillable.prop('checked', !timeTrackingBillableIsChecked).removeAttr('disabled');
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function savetimeTrackingBillable(timeTrackingBillableIsChecked) {
    jQuery.ajax({
        data: {'case_id': caseId},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + controller + '/set_time_tracking_billable_default_status',
        beforeSend: function () {
            timeTrackingBillableContainer.attr('disabled', 'disabled');
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.result) {
                if (typeof response.billable !== 'undefined') {
                    timeTrackingBillable = response.billable;
                    jQuery('.log-case-time').attr('onclick', false);
                    jQuery('.log-case-time').off().on('click', function () {
                        logCaseActivity(clientName);
                    });
                }
                timeTrackingBillableContainer.prop('checked', timeTrackingBillableIsChecked).removeAttr('disabled');
                jQuery('.tooltip-title','#edit-bulk-time-container').tooltipster("destroy");
                jQuery('.tooltip-title','#edit-bulk-time-container').attr("title", timeTrackingBillable == 1 ? billableByDefault : noneBillableByDefault).tooltipster({
                    contentAsHTML: true,
                    timer: 22800,
                    animation: 'grow',
                    delay: 200,
                    theme: 'tooltipster-default',
                    touchDevices: false,
                    trigger: 'hover',
                    maxWidth: 350,
                    interactive: true
                });
                pinesMessageV2({ty: 'success', m: timeTrackingBillable == 1 ? billableByDefault : noneBillableByDefault});
            } else {
                timeTrackingBillableContainer.prop('checked', !timeTrackingBillableIsChecked).removeAttr('disabled');
                pinesMessageV2({ty: 'error', m: _lang.invalid_request});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function logCaseActivity(client) {
    clientName = client;
    logActivityDialog(false, {'legalCaseLookupId': caseId, 'legalCaseLookup': "M" + caseId, 'legalCaseSubject': caseSubject, 'billable': timeTrackingBillable, 'clientId': clientId, 'clientName': clientName, 'modifiedByName':modifiedByName, 'modifiedOn': modifiedOn, 'createdByName': createdByName, 'createdOn': createdOn, 'status': status}, 'hideCase');
}

function bulkEditTimeLogs(container) {
    setDatePicker('#to-date-wrapper', container);
    setDatePicker('#from-date-wrapper', container);
    jQuery('#entity-time-log', container).selectpicker();
    var bulkGrid = jQuery('#bulk-edit-time-logs');
    $bulkTimeTrackingGrid = bulkGrid;
    jQuery("#to-date-wrapper", container).bootstrapDP("setStartDate", timeActions.get_previous_month(new Date()));
    jQuery("#from-date-wrapper", container).bootstrapDP("setEndDate", timeActions.get_next_month(new Date()));
    lookUpUsers(jQuery('#user-name', container), jQuery('#user-id', container), 'user_name', jQuery('.user-name-lookup-wrapper', container), container, false, {
        'callback': onUserLookupSelect,
        'onClearLookup': function (){
            timeLogsGrid();
        }
    });
    jQuery("#from-date-wrapper", container).bootstrapDP().on('changeDate', function (e) {
        jQuery("#to-date-wrapper", container).bootstrapDP("setStartDate", timeActions.get_current_date(e.date));
        bulkGrid.data('kendoGrid').dataSource.read();
    }).change(
        function (){
            timeLogsGrid();
        }
    );
    jQuery("#to-date-wrapper", container).bootstrapDP().on('changeDate', function (e) {
        jQuery("#from-date-wrapper", container).bootstrapDP("setEndDate", timeActions.get_current_date(e.date));
        bulkGrid.data('kendoGrid').dataSource.read();
    }).change(
        function (){
            timeLogsGrid();
        }
    );
    disableAutocomplete(container);
    jQuery('#edit-bulk-time-container').on('hidden.bs.modal', function () {
        splittedItems = [];
    });
    jQuery("#bulk-time-dialog-submit",container).on('click',function () {
        bulkGridActions.submitSplittedTimeLogs();
    });
    jQuery('.tooltip-title', '#edit-bulk-time-container').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true,
        multiple: true
    });
    timeLogsGrid();
}

function timeLogsGrid() {
    var bulkEditTimeLogsGrid = jQuery('#bulk-edit-time-logs');
    var hiddenUserRate = showUserRate ? false : true;
    var BulkEditTimeGridDataSource = new kendo.data.DataSource({
        transport: {
            read: {
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (_lang.languageSettings['langDirection'] === 'rtl')  gridScrollRTL();
                    animateDropdownMenuInGridsV2('bulk-edit-time-logs');
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        gridEvents();
                    }
                }, beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['timeTypeId']) < 1) {
                            options.models[i]['timeTypeId'] = null;
                        }
                        if (parseInt(options.models[i]['timeInternalStatusId']) < 1) {
                            options.models[i]['timeInternalStatusId'] = null;
                        }
                    }
                    return { models: kendo.stringify(options.models) };
                } else {
                    var container = jQuery("#edit-bulk-time-container");
                    options.filter = getFormFilters();
                    options.returnData = 1;
                    options.organization_id = jQuery('#entity-time-log', container).val();
                    options.only_log_rate = 0;
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                        id: {editable: false, type: "number"},
                        user_id: { editable: false, type: "number"},
                        worker: { editable: false},
                        allRecordsClientName: { editable: false},
                        effectiveEffort: { editable: true, type: "string"},
                        comments: {type: "string"},
                        timeTypeId: {field: "timeTypeId"},
                        timeInternalStatusId: {field: "timeInternalStatusId", editable: false},
                        timeStatus: { editable: false, type: "string"},
                        ratePerHour: { type: "number", validation: { min: 0 }},
                        rate_system: {editable: true},
                        createdOn: {editable: false},
                        inserter: {editable: false},
                        billingStatus: {editable: false},
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
                        row['worker'] = escapeHtml(row['worker']);
                        row['comments'] = replaceHtmlCharacter(row['comments']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        change: function (e) {
            var bulkGrid = jQuery('#bulk-edit-time-logs').data("kendoGrid");
            if(!e.action){
                var items = this.data();
                if(Array.isArray(splittedItems) || !splittedItems.length){
                    jQuery.each(items, function(index, value){
                        jQuery.each(splittedItems, function(rowIndex, row){
                            if(row.itemId === value.id){
                                if(row.isNew){
                                    var newRowbulkGrid = {
                                        id: "",
                                        logDate: value.logDate,
                                        worker: value.worker,
                                        effectiveEffort: splittedItems[rowIndex].effectiveEffort,
                                        ratePerHour: "",
                                        entityRatePerHour: "",
                                        timeStatus: 'newRecord',
                                        comments: typeof value.comments === 'undefined' ? "" : value.comments,
                                        timeTypeId: value.timeTypeId && value.timeTypeId.value ? value.timeTypeId.value : value.timeTypeId,
                                        timeInternalStatusId: value.timeInternalStatusId && value.timeInternalStatusId.value ? value.timeInternalStatusId.value : value.timeInternalStatusId,
                                        rate_system: value.rate_system,
                                        createdOn: value.createdOn,
                                        inserter: value.inserter,
                                        billingStatus: value.billingStatus
                                    };
                                    var rowItem = bulkGrid.dataSource.get(row.itemId);
                                    var indexNewRow = bulkGrid.dataSource.indexOf(rowItem);
                                    var newItem = bulkGrid.dataSource.insert(indexNewRow + 1, newRowbulkGrid);
                                    splittedItems[rowIndex].id = newItem.uid;
                                } else {
                                    splittedItems[rowIndex].id = value.uid;
                                    splittedItems[rowIndex].entityRatePerHour = value.entityRatePerHour;
                                    bulkGridActions.setGridField(value.uid, "effectiveEffort", splittedItems[rowIndex].effectiveEffort, bulkGrid);
                                }
                                bulkGridActions.setFields(value.uid, splittedItems[rowIndex], bulkGrid, value.rate_system);
                            }
                        });
                    });
                }
            }
            if (e.action === "itemchange"){
                var rowItem = e.items[0];
                bulkGridActions.updateItemRow(bulkGrid, rowItem);
                bulkGridActions.getTotalsOfBulkTimeLogs();
            }
        }
    });
    $timeCategoriesGridOptions = {
        autobind: true,
        dataSource: BulkEditTimeGridDataSource,
        columns: [
            {title: ' ', field: 'id',
                template: function (dataItem) {
                    var rowNumber = ++record - 1;
                    if(dataItem.timeStatus == "billable"){
                        if(dataItem.billingStatus != "reimbursed" && dataItem.billingStatus != "invoiced"){
                            return  helpers.getSettingGridTemplate([
                                ['onclick="logActivityDialog('+ dataItem.id +',' + undefined + ',\'hideCase\')"', _lang.viewEdit],
                                ['onclick="splitTimePopup(\'' + convertDecimalToTime(  { time: dataItem.effectiveEffort })  + '\', '+ rowNumber + ',\'' + dataItem.uid +'\')"', helpers.capitalizeFirstLetter(_lang.split)],
                                ['onclick="deleteActivityDialog('+ dataItem.id +')"', _lang.delete_log],
                            ]);
                        } else {
                            return '';
                        }
                    } else if(dataItem.timeStatus == "newRecord"){
                        var marginGear = _lang.languageSettings['langDirection'] === 'rtl' ? 'margin-0-2' : 'margin-left-8';
                        return '<span class="fa-solid fa-trash-can purple_color '+ marginGear + '" onclick="bulkGridActions.deleteRowGrid(\''+ dataItem.uid + '\', \''+ dataItem.itemId + '\')" aria-hidden="true"></span>';
                    } else if(dataItem.timeStatus == "internal"){
                        return  helpers.getSettingGridTemplate([
                            ['onclick="logActivityDialog('+ dataItem.id +',' + undefined + ',\'hideCase\')"', _lang.viewEdit],
                            ['onclick="deleteActivityDialog('+ dataItem.id +')"', _lang.delete_log],
                        ]);
                    } else {
                        return '';
                    }
                },
                sortable: false, width: '30px'},
            {field: "logDate", format: "{0:yyyy-MM-dd}", template: "#= (logDate == null) ? '' : kendo.toString(logDate, 'yyyy-MM-dd') #", title: _lang.date, width: '120px',
                editor: function(container, options) {
                    var input = jQuery("<input/>");
                    input.attr("name", options.field);
                    input.appendTo(container);
                    input.kendoDatePicker({format: "yyyy-MM-dd"});
                }},
            {field: "worker", title: _lang.user, width: '160px', template: '#= (worker!=null ? worker : "") #'},
            {field: "effectiveEffort", title: _lang.effort, template: '#= jQuery.fn.timemask({time: effectiveEffort})+" ("+ effectiveEffort + "h)"#', width: '130px', editable: true},
            {field: "ratePerHour", title: _lang.rate, template: "#= bulkGridActions.getUserRatePerHourTemplate(ratePerHour, timeStatus, rate_system, entityRatePerHour) #", sortable: false, width: "105px", hidden: hiddenUserRate},
            {field: "timeTypeId", title: _lang.timeType, template: "#= (timeTypeId == null) ? ' ' : helpers.getObjectFromArr(timeTypesFromView, 'text', 'value', timeTypeId) #",  width: '130px'},
            {field: "timeInternalStatusId", title: _lang.timeInternalStatus, template: "#= (timeInternalStatusId == null) ? ' ' : helpers.getObjectFromArr(timeInternalStatusesFromView, 'text', 'value', timeInternalStatusId) #",  width: '130px'},
            {field: "timeStatus", title: helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.billable), template: '<span class="#= timeStatus == "billable" ? "darkGreen" : "light-orange" #">#= timeStatus == "billable" ? _lang.yes : _lang.no #</span>' ,width: '105px'},
            {field: "comments", title: _lang.comments, template: '#= (comments!=null&&comments!="") ? comments.substring(0,40)+"..." : ""#', width: '200px'},
            {field: "allRecordsClientName", title: _lang.client, width: '140px'},
            {field: "rate_system", hidden: true},
            {field: "entityRatePerHour", hidden: true},
            {field: "billingStatus", template: '<span class="#= billingStatus == "invoiced" ? "lightGreen" : billingStatus == "reimbursed" ? "darkGreen" : "red" #">#= timeStatus == "billable" ? (billingStatus == "invoiced" ? helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.invoiced) : billingStatus == "reimbursed" ?helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.reimbursed) : helpers.capitalizeFirstLetter(_lang.timeTrackingStatus.toInvoice)) : billingStatus #</span>', width: '95px', title: _lang.status},
            {field: "inserter", title: _lang.createdBy, width: '130px', template: '#= (inserter!=null ? inserter : "") #'},
            {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '140px'},
        ],
        editable: true,
        filterable: false, height: 500,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: true,buttonCount:5
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: false,
        dataBinding: function(e) {
            record = (this.dataSource.page() -1) * this.dataSource.pageSize();
        },
        dataBound: function (e) {
            bulkGridActions.boundDataToGridStyle();
            bulkGridActions.getTotalsOfBulkTimeLogs();
        },
        edit: function (e) {
            if(e.model.timeStatus == "internal" || e.model.timeStatus == "newRecord") {
                var ratePerHourElement = e.container.find("input[name=ratePerHour]");
                ratePerHourElement.val("");
                ratePerHourElement.attr('disabled', 'disabled');
            }
            if(e.model.billingStatus == "reimbursed" || e.model.billingStatus == "invoiced"){
                var invoicedRow = e.container.find("input");
                invoicedRow.attr('disabled', 'disabled');
            }
            if(e.container.find("input[name=ratePerHour]") && e.container.find("input[name=ratePerHour]").length > 0){
                var ratePerHourElement = e.container.find("input[name=ratePerHour]");
                ratePerHourElement.blur(function(){
                    setTimeout(function () {
                        jQuery('.default-rate-tooltip').tooltipster({
                            contentAsHTML: true,
                            timer: 22800,
                            animation: 'grow',
                            delay: 200,
                            theme: 'tooltipster-default',
                            touchDevices: false,
                            trigger: 'hover',
                            maxWidth: 350,
                            interactive: true,
                            multiple: true
                        });
                    }, 800);
                });
            }
            var effectiveEffortElement = e.container.find("input[name=effectiveEffort]");
            effectiveEffortElement.attr('disabled', 'disabled');
            var fieldName = $timeCategoriesGridOptions.columns[e.container.index()].field;
            if(fieldName == 'effectiveEffort'){
                pinesMessageV2({ty: 'information', m: _lang.fieldCanBeEdit});
            }
        }
    };
    if (undefined === bulkEditTimeLogsGrid.data('kendoGrid')) {
        if (timeTypesFromView !== ""){
            $timeCategoriesGridOptions.columns[5].values = timeTypesFromView;
        }
        bulkEditTimeLogsGrid.kendoGrid($timeCategoriesGridOptions);
        return false;
    }
    bulkEditTimeLogsGrid.data('kendoGrid').dataSource.read();
    return false;
}

function splitTimePopup(effortValue, id, uid) {
    uid = uid || false;
    var containerAddEdit = jQuery("#add-edit-time-log");
    var splitDialogUrl = getBaseURL().concat('time_tracking/split_time/').concat(effortValue);
    var data = {};
    jQuery.ajax({
        url: splitDialogUrl,
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var splitDialogId = "split-time-container";
                jQuery('<div id="split-time-container"></div>').appendTo("body");
                var container = jQuery("#"+ splitDialogId);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                var bulkGrid = jQuery('#bulk-edit-time-logs').data("kendoGrid");
                var rowItem = bulkGrid.dataSource.getByUid(uid);
                if(rowItem.logDate){
                    jQuery("#split-date-wrapper span",container).html(rowItem.logDate);
                    jQuery("#split-date-wrapper", container).removeClass("hide");
                }
                var originalTimeDecimal = parseFloat(rowItem.effectiveEffort);
                jQuery("#effective-effort-hour-billable",container).keyup(function() {
                    if(jQuery(this).val()){
                        var convertedBillableTime = convertTimeToDecimal(jQuery(this).val());
                        if(convertedBillableTime){
                            jQuery("#effective-effort-hour-non-billable",container).val(convertDecimalToTime({time: parseFloat((originalTimeDecimal - convertedBillableTime).toFixed(2))}));
                        }
                    }
                });
                jQuery("#split-time-dialog-submit",container).click(function () {
                    var convertedBillableTime = convertTimeToDecimal(jQuery("#effective-effort-hour-billable",container).val());
                    var convertedNoneBillableTime = convertTimeToDecimal(jQuery("#effective-effort-hour-non-billable",container).val());
                    if(!jQuery("#effective-effort-hour-billable",container).val()){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.validation_field_required.sprintf([_lang.timeTrackingStatus.billable]));
                    } else if(!convertedBillableTime){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.timeValidateFormat.sprintf([_lang.timeTrackingStatus.billable]));
                    } else if(originalTimeDecimal <= convertedBillableTime){
                        ajaxEvents.displayValidationError('effective-effort-hour-billable', container,_lang.splitTimeMessage);
                    } else {
                        bulkGridActions.splitRow(bulkGrid, id, uid, convertedBillableTime, convertedNoneBillableTime);
                        jQuery('.modal', "#split-time-container").modal('hide');
                    }
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function getFormFilters() {
    var filtersForm = jQuery('#edit-bulk-time-form');
    disableEmpty(filtersForm);
    var searchFilters = form2js('bulk-edit-filter-container', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}

function getExpenseStatusTranslation(val) {
    return _lang.ExpenseStatus[val];
}
function getTimeLogsStatusTranslation(val) {
    return _lang.timeTrackingStatus[val];
}
function onUserLookupSelect() {
    var bulkGrid = jQuery('#bulk-edit-time-logs');
    bulkGrid.data('kendoGrid').dataSource.read();
}

var bulkGridActions = (function() {
    'use strict';

    function splitRow(bulkGrid, id, uid, convertedBillableTime, convertedNoneBillableTime){
        var itemRow = bulkGrid.dataSource.getByUid(uid);
        var existInSplitArray = "";
        jQuery.each(splittedItems, function(index, value){
            if(value.id === uid){
                existInSplitArray = index;
            }
        });
        if(existInSplitArray !== ""){
            setGridField(uid, "effectiveEffort", convertedBillableTime, bulkGrid);
            splittedItems[existInSplitArray].effectiveEffort = convertedBillableTime;
        } else {
            setGridField(uid, "effectiveEffort", convertedBillableTime, bulkGrid);
        }
        insertRowToGrid(bulkGrid, id, convertedNoneBillableTime, itemRow.id);
    }

    function setGridField(uid, field, value, grid) {
        var rowItem = grid.dataSource.getByUid(uid);
        rowItem.set(field, value);
    }

    function insertRowToGrid(bulkGrid , id ,NoneBillableTime, itemRowId) {
        id = parseInt(id);
        var gridData = bulkGrid.dataSource.data();
        var newRowbulkGrid = {
            id: "",
            logDate: gridData[id].logDate,
            worker: gridData[id].worker,
            effectiveEffort: NoneBillableTime,
            ratePerHour: gridData[id].ratePerHour,
            entityRatePerHour: gridData[id].entityRatePerHour,
            timeStatus: 'newRecord',
            comments: typeof gridData[id].comments === 'undefined' ? "" : gridData[id].comments,
            itemId: itemRowId,
            timeTypeId: gridData[id].timeTypeId && gridData[id].timeTypeId.value ? gridData[id].timeTypeId.value : gridData[id].timeTypeId,
            timeInternalStatusId: gridData[id].timeInternalStatusId && gridData[id].timeInternalStatusId.value ? gridData[id].timeInternalStatusId.value : gridData[id].timeInternalStatusId,
            rate_system: gridData[id].rate_system,
            createdOn: gridData[id].createdOn,
            inserter: gridData[id].inserter,
            billingStatus: gridData[id].billingStatus
        };
        var newItem = bulkGrid.dataSource.insert(id + 1, newRowbulkGrid);
        splittedItems.push({
            id: newItem.uid,
            isNew: true,
            itemId: itemRowId,
            effectiveEffort: NoneBillableTime,
            rowNumber: id,
            ratePerHour: gridData[id].ratePerHour,
            entityRatePerHour: gridData[id].entityRatePerHour,
            timeTypeId: gridData[id].timeTypeId && gridData[id].timeTypeId.value ? gridData[id].timeTypeId.value : gridData[id].timeTypeId,
            timeInternalStatusId: gridData[id].timeInternalStatusId && gridData[id].timeInternalStatusId.value ? gridData[id].timeInternalStatusId.value : gridData[id].timeInternalStatusId,
            comments: typeof gridData[id].comments === 'undefined' ? "" : gridData[id].comments,
            logDate: gridData[id].logDate,
            rate_system: gridData[id].rate_system,
            createdOn: gridData[id].createdOn,
            inserter: gridData[id].inserter,
            billingStatus: gridData[id].billingStatus
        });
        boundDataToGridStyle();
    }

    function boundDataToGridStyle(){
        var bulkTable = jQuery("#bulk-edit-time-logs tbody");
        if(Array.isArray(splittedItems) && splittedItems.length){
            jQuery.each(splittedItems,function (index, value) {
                bulkTable.find("tr[data-uid=" + value.id + "]").addClass(value.isNew ? "new-kendo-row" : "edit-kendo-row");
            });
        }
    }

    function deleteRowGrid(uid){
        var bulkEditData = jQuery('#bulk-edit-time-logs').data("kendoGrid").dataSource;
        var dataRow = bulkEditData.getByUid(uid);
        var itemParentIndex = '';
        var isTheLatestChild = true;
        splittedItems = splittedItems.filter(function( item ) {
            if(item.id === uid){
                jQuery.each(splittedItems,function (parentIndex, parentItem) {
                    if(parentItem.itemId === item.itemId && !parentItem.isNew){
                        splittedItems[parentIndex].effectiveEffort = (parseFloat(parentItem.effectiveEffort) + parseFloat(dataRow.effectiveEffort)).toFixed(2);
                        itemParentIndex = parentIndex;
                        jQuery.each(splittedItems,function(childrenIndex, childrenValue){
                            if(childrenValue.isNew && parentItem.itemId === childrenValue.itemId && childrenValue.id !== uid){
                                isTheLatestChild = false;
                            }
                        });
                    }
                });
            } else {
                return true;
            }
        });
        if(isTheLatestChild) splittedItems.splice(itemParentIndex, 1);
        bulkEditData.remove(dataRow);
        bulkEditData.read();
    }

    function submitSplittedTimeLogs() {
        var container = jQuery("#edit-bulk-time-container");
        if(Array.isArray(splittedItems) && splittedItems.length){
            jQuery.ajax({
                dataType: 'JSON',
                url: getBaseURL() + 'cases/bulk_edit_time/' + caseId,
                data: {"data": JSON.stringify(splittedItems)},
                type: 'POST',
                beforeSend: function () {
                    ajaxEvents.beforeActionEvents(container);
                },
                success: function (response) {
                    if(response.status){
                        if (null != $timeTrackingGrid) {
                            try { $timeTrackingGrid.data("kendoGrid").dataSource.read(); } catch (e) {}
                        }
                        splittedItems = [];
                        if (null != $bulkTimeTrackingGrid) {
                            try { $bulkTimeTrackingGrid.data("kendoGrid").dataSource.read(); } catch (e) {}
                        }
                        pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    }
                }, complete: function () {
                    ajaxEvents.completeEventsAction(container);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        } else {
            pinesMessageV2({ty: 'information', m: _lang.noRecordsChanged});
            jQuery('.modal').modal('hide');
        }
    }

    function getTotalsOfBulkTimeLogs() {
        var container = jQuery("#edit-bulk-time-container");
        jQuery.ajax({
            url: getBaseURL() + "cases/get_total_effort_time_logs_case/" + caseId + (myTimeLogs ? ('/' + myTimeLogs) : ''),
            data: {'filter': getFormFilters(), 'entity': jQuery("#entity-time-log",container).val(),
                'legal_case_id' : jQuery('#legal-case-id', container).val(),
                'user_id': jQuery('#user-id', container).val()},
            dataType: 'JSON',
            type: 'POST',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            success: function (response) {
                if (response.result) {
                    var totalEffort = convertDecimalToTime({time: parseFloat(response.data.totalEffectiveEffort).toFixed(2)});
                    jQuery("#total-effort", container).html(totalEffort != '' ? totalEffort : '0h');
                    var totalNoneBillableEffort = parseFloat(response.data.totalNoneBillableEffort).toFixed(2);
                    var totalBillableEffort = parseFloat(response.data.totalBillableEffort).toFixed(2);
                    var totalCost = isNaN(parseFloat(response.data.totalCost).toFixed(2)) ? 0 : parseFloat(response.data.totalCost).toFixed(2);
                    if(Array.isArray(splittedItems) && splittedItems.length){
                        jQuery.each(splittedItems, function (index, value) {
                            if(response && response.items && response.items.data){
                                var ratePerHourValue = parseFloat(value.ratePerHour).toFixed(2);
                                jQuery.each(response.items.data, function(itemIndex, itemValue){
                                    if(itemValue.ratePerHour && value.effectiveEffort && !isNaN(itemValue.ratePerHour) && !isNaN(value.effectiveEffort) && value.isNew && itemValue.id == value.itemId){
                                        totalNoneBillableEffort = parseFloat(totalNoneBillableEffort) + parseFloat(value.effectiveEffort);
                                        totalBillableEffort -= value.effectiveEffort;
                                        totalCost = totalCost - (itemValue.ratePerHour * value.effectiveEffort);
                                    }
                                    if(isNaN(ratePerHourValue)){
                                        ratePerHourValue = 0;
                                        ratePerHourValue = parseFloat(ratePerHourValue).toFixed(2);
                                    }
                                    if(ratePerHourValue && itemValue.ratePerHour && !isNaN(ratePerHourValue) && !isNaN(itemValue.ratePerHour) && value.effectiveEffort && !value.isNew && itemValue.id == value.itemId && ratePerHourValue != itemValue.ratePerHour){
                                        totalCost = totalCost - (value.effectiveEffort * itemValue.ratePerHour);
                                        totalCost = totalCost + (value.effectiveEffort * ratePerHourValue);
                                    }
                                });
                            }
                        });
                    }
                    totalNoneBillableEffort = convertDecimalToTime({time: totalNoneBillableEffort});
                    totalBillableEffort = convertDecimalToTime({time: totalBillableEffort});
                    totalCost = helpers.numberWithCommas(parseFloat(totalCost).toFixed(2));
                    var totalCostWithCurrency = totalCost ? (totalCost + " " + response.currency_value) : '';
                    totalCostWithCurrency = showUserRate ? totalCostWithCurrency : "-";
                    jQuery("#total-billable", container).html(totalBillableEffort != '' ? totalBillableEffort : '0h');
                    jQuery("#total-none-billable", container).html(totalNoneBillableEffort != '' ? totalNoneBillableEffort : '0h');
                    jQuery("#total-cost", container).html(totalCostWithCurrency);
                    jQuery('.billable-check-box-message').tooltipster({
                        content: _lang.noEntityRateAvailable.sprintf([getBaseURL() + 'modules/money/money_preferences#UsersValues']),
                        contentAsHTML: true,
                        timer: 22800,
                        animation: 'grow',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        maxWidth: 350,
                        interactive: true,
                        multiple: true
                    });
                    jQuery('.default-rate-tooltip').tooltipster({
                        contentAsHTML: true,
                        timer: 22800,
                        animation: 'grow',
                        delay: 200,
                        theme: 'tooltipster-default',
                        touchDevices: false,
                        trigger: 'hover',
                        maxWidth: 350,
                        interactive: true,
                        multiple: true
                    });
                }
            }, complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function timeCategoriesMapping(bulkEditTimeLogsGrid) {
        if (undefined === bulkEditTimeLogsGrid.data('kendoGrid')) return false;
        $timeCategoriesGridOptions.columns[5].values = timeTypesFromView;
        bulkEditTimeLogsGrid.kendoGrid($timeCategoriesGridOptions);
        resetPagination(bulkEditTimeLogsGrid);
        return false;
    }

    function onChangeEntity(element) {
        var bulkEditTimeLogsGrid = jQuery('#bulk-edit-time-logs');
        bulkEditTimeLogsGrid.data('kendoGrid').dataSource.read();
    }

    function updateItemRow(bulkGrid, item){
        var itemRow = bulkGrid.dataSource.getByUid(item.uid);
        var existInSplitArray = "";
        jQuery.each(splittedItems, function(index, value){
            if(value.id === item.uid){
                existInSplitArray = index;
            }
        });
        if(existInSplitArray !== ""){
            splittedItems[existInSplitArray].effectiveEffort = itemRow.effectiveEffort;
            splittedItems[existInSplitArray].ratePerHour = itemRow.ratePerHour;
            splittedItems[existInSplitArray].entityRatePerHour = itemRow.entityRatePerHour;
            splittedItems[existInSplitArray].logDate = convert(itemRow.logDate).date;
            splittedItems[existInSplitArray].comments = itemRow.comments;
            splittedItems[existInSplitArray].timeTypeId = itemRow.timeTypeId && itemRow.timeTypeId.value ? itemRow.timeTypeId.value : itemRow.timeTypeId;
            splittedItems[existInSplitArray].timeInternalStatusId = itemRow.timeInternalStatusId && itemRow.timeInternalStatusId.value ? itemRow.timeInternalStatusId.value : itemRow.timeInternalStatusId;
            splittedItems[existInSplitArray].rate_system = (itemRow.ratePerHour) ? (itemRow.rate_system == 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null;
            splittedItems[existInSplitArray].createdOn= itemRow.createdOn;
            splittedItems[existInSplitArray].inserter = itemRow.inserter;
            splittedItems[existInSplitArray].billingStatus = itemRow.billingStatus;
        } else {
            splittedItems.push(
                {
                    id: item.uid,
                    isNew: false,
                    itemId: itemRow.id,
                    effectiveEffort: itemRow.effectiveEffort,
                    rowNumber: itemRow.id,
                    ratePerHour: itemRow.ratePerHour,
                    entityRatePerHour: itemRow.entityRatePerHour,
                    timeTypeId: itemRow.timeTypeId && itemRow.timeTypeId.value ? itemRow.timeTypeId.value : itemRow.timeTypeId,
                    timeInternalStatusId: itemRow.timeInternalStatusId && itemRow.timeInternalStatusId.value ? itemRow.timeInternalStatusId.value : itemRow.timeInternalStatusId,
                    comments: typeof itemRow.comments === 'undefined' ? "" : itemRow.comments,
                    inserter: itemRow.inserter,
                    createdOn: itemRow.createdOn,
                    billingStatus: itemRow.billingStatus,
                    logDate: convert(itemRow.logDate).date,
                    rate_system: (itemRow.ratePerHour) ? (itemRow.rate_system === 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null
                }
            );
        }
        var itemRowSystem = (itemRow.ratePerHour) ? (itemRow.rate_system === 'system_rate' ? itemRow.rate_system : 'fixed_rate')  : null;
        setFields(item.uid, itemRow, bulkGrid, itemRowSystem);
        var bulkTable = jQuery("#bulk-edit-time-logs tbody");
        bulkTable.find("tr[data-uid=" + item.uid + "]").addClass("edit-kendo-row");
    }

    function setFields(uid, itemRow, bulkGrid, rate_system) {
        var itemSchemaObject = {
                "effectiveEffort": itemRow.effectiveEffort,
                "ratePerHour": itemRow.ratePerHour,
                "logDate": itemRow.logDate,
                "comments": typeof itemRow.comments === 'undefined' ? "" : itemRow.comments,
                "timeTypeId": itemRow.timeTypeId,
                "timeInternalStatusId": itemRow.timeInternalStatusId,
                "entityRatePerHour": itemRow.entityRatePerHour,
                "rate_system": rate_system,
                "inserter": itemRow.inserter,
                "createdOn": itemRow.createdOn,
                "billingStatus": itemRow.billingStatus,
            };
        jQuery.each(itemSchemaObject,function(index, value){
            setGridField(uid, index, value, bulkGrid);
        });
    }

    function getUserRatePerHourTemplate(ratePerHour, timeStatus, rateSystem, entityRatePerHour){
        var NoRateTemplate = _lang.noRateFound + "<span class=\"margin-left-3 pull-right\" style=\"width:auto;margin-top:3px;margin-right:3px\"><span class=\"fas fa-question-circle tooltip-title billable-check-box-message\"></span></span>";
        if(timeStatus == "billable"){
            if(rateSystem != null){
                if((ratePerHour != null && ratePerHour > 0)){
                    return ratePerHour;
                } else{
                    return NoRateTemplate;
                }
            } else {
                if(entityRatePerHour != null && entityRatePerHour > 0){
                    return getDefaultRateTemplate(entityRatePerHour);
                } else{
                    return NoRateTemplate;
                }
            }
        } else{
            return "";
        }
    }

    function getDefaultRateTemplate(rate){
        return "<span title='" + rate + "' class='default-rate-tooltip'>" +_lang.defaultRate + "</span>"
    }

    return {
        splitRow: splitRow,
        setGridField: setGridField,
        insertRowToGrid: insertRowToGrid,
        boundDataToGridStyle: boundDataToGridStyle,
        deleteRowGrid: deleteRowGrid,
        submitSplittedTimeLogs: submitSplittedTimeLogs,
        getTotalsOfBulkTimeLogs: getTotalsOfBulkTimeLogs,
        timeCategoriesMapping: timeCategoriesMapping,
        onChangeEntity: onChangeEntity,
        updateItemRow: updateItemRow,
        setFields: setFields,
        getUserRatePerHourTemplate: getUserRatePerHourTemplate
    };
}());





