jQuery(document).ready(function () {
    try {
        var caseExpensesDataSrc = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + controller + (myExpenses ? "/my_expenses/" : "/expenses/") + caseId,
                    dataType: "JSON",
                    type: "POST",
                    complete: function () {
                        if (_lang.languageSettings['langDirection'] === 'rtl')
                            gridScrollRTL();
                    }
                },
                parameterMap: function (options, operation) {
                    if ("read" === operation) {
                        jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                    }
                    return options;
                }
            },
            schema: {type: "json", data: "data", total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: {editable: false, type: "integer"},
                        dated: {type: "date"},
                        case_id: {type: "number"},
                        caseID: {type: "string"},
                        task: {type: "string"},
                        hearing: {type: "string"},
                        event: {type: "string"},
                        client_id: {type: "number"},
                        clientName: {type: "string"},
                        expenseID: {type: "number"},
                        expenseCategory: {type: "string"},
                        amount: {type: "number"},
                        referenceNum: {type: "string"},
                        currency: {type: "string"},
                        paidThroughID: {type: "string"},
                        paidThroughAccount: {type: "string"},
                        billingStatus: {type: "string"},
                        status: {type: "string"},
                        organizationName: {type: "string"},
                        createdByName: {type: "string"},
                        createdOn: {type: "date"},
                        modifiedByName: {type: "string"},
                        modifiedOn: {type: "date"}
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
                            row['task'] = escapeHtml(row['task']);
                            row['paidThroughAccount'] = escapeHtml(row['paidThroughAccount']);
                            row['createdByName'] = escapeHtml(row['createdByName']);
                            row['modifiedByName'] = escapeHtml(row['modifiedByName']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            }, error: function (e) {
                defaultAjaxJSONErrorsHandler(e.xhr);
            },
            batch: true, pageSize: 20, serverPaging: true, serverFiltering: true, serverSorting: true
        });
        var caseExpensesGridOptions = {
            autobind: true,
            dataSource: caseExpensesDataSrc,
            columnMenu: {messages: _lang.kendo_grid_sortable_messages},
            columns: [
                {field: "dated", title: _lang.paidOn, format: "{0:yyyy-MM-dd}", width: '123px'},
                {field: "expenseID", template: '<a href="' + getBaseURL('money') + 'vouchers/expense_edit/#= id #">#= str_pad(expenseID, 8, "0", "STR_PAD_LEFT") #</a>', title: _lang.expenseID, width: '140px'},
                {field: "referenceNum", title: _lang.internalReferenceNb, width: '150px'},
                {field: "expenseCategory", title: _lang.expenseCategory, width: '200px'},
                {field: "amount", template: "#= money_format('%i',amount)#", title: _lang.expenseAmount, width: '180px'},
                {field: "currency", title: _lang.currency, width: '120px'},
                {field: "paidThroughID", template: '#= paidThroughAccount #', title: _lang.paidThrough, width: '160px'},
                {field: "billingStatus", title: _lang.billingStatus, template: '<span class="#= billingStatus == "invoiced" ? "lightGreen" : billingStatus == "to-invoice" ? "red" : billingStatus == "reimbursed" ? "darkGreen" : "" #">#= billingStatus #</span>', width: '160px'},
                {field: "status", title: _lang.status, template:'<span class="#= status == "approved" ? "darkGreen" : status == "needs_revision" ? "red" : status == "cancelled" ? "purple" : status=="open" ? "orange" : "" #">#= getTranslation(status) #</span>' ,width: '145px'},
                {field: "clientName", title: _lang.clientName_Case, width: '160px'},
                {field: "case_id", filterable: false, title: controller!=='cases' ? _lang.ipID :_lang.caseId, template: '<a href="' + getBaseURL() + controller + '/edit/#= case_id #">#=  controller!="cases" ? case_id :caseID #</a>', width: '140px'},
                {field: "task", filterable: false, title: _lang.relatedTo, template:'#= task ? returnRelatedTaskTemplate(case_id,caseCategory,task) : "" # #= hearing ? ( \'<a href="cases/events/\'+case_id+\'">\'+_lang.hearing +\' </a> <br> \') : ""# #= event ? (\' <a href="cases/events/\'+case_id+\'">\'+_lang.event +\' </a> \') : ""#', width: '180px'},
                {field: "organizationName", title: _lang.relatedEntity, width: '160px'},
                {field: "createdByName", title: _lang.createdBy, width: '140px', template: '#= (createdByName!=null ? createdByName : "") #'},
                {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '140px'},
                {field: "modifiedByName", title: _lang.modifiedBy, width: '140px', template: '#= (modifiedByName!=null ? modifiedByName : "") #'},
                {field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '158px'}
            ],
            editable: false,
            filterable: false,
            pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
            reorderable: true,
            resizable: true,
            scrollable: true,
            height: 330,
            sortable: {mode: "multiple"},
            selectable: "single",
            toolbar: [{
                    name: "case-expenses-grid-toolbar",
                    template: '<div class="col-md-4 no-padding margin-15-23">'
                            + '<h4 class="col-md-5 no-padding">' + _lang.relatedExpenses + '</h4>'
                            + '</div>'
                            + '<div class="col-md-2 pull-right margin-15-23 pr_30">'
                            + '<div class="btn-group pull-right">'
                            + '<div class="dropdown">'
                            + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                            + _lang.actions + ' <span class="caret"></span>'
                            + '<span class="sr-only">Toggle Dropdown</span>'
                            + '</button>'
                            + '<div class="dropdown-menu action-add-btn">'
                           // + '<button class="dropdown-item btn-action" href="javascript:;" class="" onclick="expenseQuickAdd(\''+caseId+'\');"  >' + _lang.recordExpense + '</button>'
                            + '<button class="dropdown-item btn-action" href="javascript:;" class="" onclick="recordRelatedExpense(\''+caseId+'\');"  >' + _lang.recordExpense + '</button>'
                            + '<button class="dropdown-item btn-action" href="javascript:;" class="" onclick="recordRelatedExpense(\''+caseId+'\', null, false, true);"  >' + _lang.recordBulkExpenses + '</button>'
                            + '<a class="dropdown-item" href="javascript:;" onclick="goToStatementOfExpenses()" class="">'
                            + _lang.statementOfExpenses + '</a>'
                            + '<a class="dropdown-item" href="javascript:;" class=""  onclick="exportCaseExpensesToExcel()">' + _lang.exportToExcel + '</a>'
                            + '</div>'
                            + '</div>'
                            + '</div>'
                            + '</div>'
                }]
        };
        var caseExpensesGridId = jQuery('#caseExpensesGrid');
        if (undefined == caseExpensesGridId.data('kendoGrid')) {
            caseExpensesGridId.kendoGrid(caseExpensesGridOptions);
        }
        actionAddBtn = jQuery('.action-add-btn');
        if('undefined' !== typeof(disableMatter) && disableMatter){
            disableFields(actionAddBtn);
        }
    } catch (e) {
    }
});
function exportCaseExpensesToExcel() {
    jQuery('#exportResultsForm').attr('action', getBaseURL() + 'export/' + (controller == 'cases' ? 'case_expenses' : 'intellectual_property_expenses') + '/' + caseId + (controller == 'cases' ? ('/' + myExpenses) : '')).submit();
}
function goToStatementOfExpenses() {
    window.location = getBaseURL('money') + 'reports/expenses/' + caseId;
}
function returnRelatedTaskTemplate(caseId,caseCategory,task){
    return _lang.task + ': <a href="'+ getBaseURL() + controller +(caseCategory == 'Litigation' ? '/events/': '/tasks/')+caseId+'">'+task +'</a><br>';
}

function expenseQuickAdd(caseId, counselId, func) {
    counselId = counselId || false;
    data = [{name: "case_id", value: caseId}];

    if (counselId) {
        data.push({name: "counsel_id", value: counselId});
    }

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('money') + 'vouchers/expenseQuickAdd',
        type: 'GET',
        data: data,
        beforeSend: function() {
            jQuery('#loader-global').show();
        },
        success: function(response) {
            if (response.html) {
                if (jQuery('#expense-form-container').length <= 0) {
                    jQuery('<div id="expense-form-container"></div>').appendTo("body");
                    var formContainer = jQuery('#expense-form-container');
                    formContainer.html(response.html);
                    jQuery('.modal-container', formContainer).addClass('modal');
                    // Initialize form elements
                    jQuery('.select-picker', formContainer).selectpicker();
                    setDatePicker('#expense-date', formContainer, datePickerOptions);

                    // Currency formatting if needed
                    jQuery('#amount', formContainer).each(function() {
                        var input = jQuery(this);
                        input.val(money_format(input.val()));
                    });
                    // Set up modal and form events
                    commonModalDialogEvents(formContainer);
                    initializeModalSize(formContainer, 0.5, 0.7);

                    // Form submission handling
                    jQuery("#form-submit", formContainer).click(function() {
                        expenseFormSubmit(formContainer, func);
                    });

                    // Handle Enter key press
                    jQuery(formContainer).find('input').keypress(function(e) {
                        if (e.which == 13) {
                            e.preventDefault();
                            expenseFormSubmit(form, func);
                        }
                    });
                }

                if (response.notification_available) {
                    notifyMeBefore(jQuery('#expense-form-container'));
                }
            }
        },
        complete: function() {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function expenseFormSubmit(formContainer, func) {
    var formData = new FormData(form[0]);

    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/saveExpense',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            jQuery('#loader-global').show();
        },
        success: function(response) {
            if (response.success) {
                toastr.success(response.message);
                jQuery('#expense-form-container').modal('hide');
                if (typeof func === 'function') {
                    func();
                }
            } else {
                toastr.error(response.message);
            }
        },
        complete: function() {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
