var enableQuickSearch, gridDataSrc, gridOptions;

function reminderGridEvents() {
    reminderGridInitialization();
    searchRelatedReminders();
    jQuery('#reminderSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#reminderLookUp').val('');
        enableQuickSearch = false;
        jQuery('#reminders-grid').data('kendoGrid').dataSource.read();
    });
}

function reminderQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        if (term.length > 0) {
            document.getElementsByName("page").value = 1;
            document.getElementsByName("skip").value = 0;
            enableQuickSearch = true;
            jQuery('#quickSearchFilterSummaryValue', '#filtersFormWrapper').val(term);
        } else {
            enableQuickSearch = false;
        }
        jQuery('#reminders-grid').data("kendoGrid").dataSource.page(1);
    }
}

function searchRelatedReminders() {
    var grid = jQuery('#reminders-grid');
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}

function getReminderFormFilters() {
    var filtersForm = jQuery('#reminderSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('reminderSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterSummaryValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);

    return filters;
}

function enableDisableUnarchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#unarchivedButtonId').removeAttr('disabled');
    } else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
    }
}

function addNewCaseReminder() {
    var contractId = jQuery('#contractIdInPage', '#gridFormContent').val();
    reminderForm(false, false, false, contractId);
}

function reminderGridInitialization() {
    gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('contract') + "contracts/related_reminders",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible')) jQuery('#filtersFormWrapper').slideUp();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('reminders-grid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter = getReminderFormFilters();
                    options.returnData = 1;
                }
                options.contractIdFilter = jQuery('#contract-id-filter', '#reminderSearchFilters').val();
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
                    reminderId: {type: "string"},
                    contractId: {type: "string"},
                    legal_case: {type: "string"},
                    legal_case_id: {type: "integer"},
                    reminderType: {type: "string"},
                    private: {type: "string"},
                    location: {type: "string"},
                    priority: {type: "string"},
                    reminderStatus: {type: "string"},
                    description: {type: "string"},
                    assigned_to: {type: "string"},
                    estimated_effort: {type: "string"},
                    due_date: {type: "date"},
                    createdBy: {type: "string"},
                    createdOn: {type: "date"},
                    archivedReminders: {type: "string"},
                    actions: {type: "string"}
                }
            },
            parse: function (response) {
                var rows = [];
                if (response.data) {
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['assigned_to'] = escapeHtml(row['assigned_to']);
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
        serverSorting: true
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
		{
			field: "id",
			filterable: false,
			template: '<div class="dropdown">' + gridActionIconHTML + '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
					'<li><a href="javascript:;" onclick="reminderForm(\'#= id #\');">' + _lang.viewEdit + '</a></li>' +
					'<li><a href="javascript:;" onclick="dismissReminder(\'#= id #\');">' + _lang.dismiss + '</a></li>' +
					'<li><a href="javascript:;" onclick="reminderForm(\'#= id #\',\'postpone\');">' + _lang.postpone + '</a></li>' +
					'<li><a href="javascript:;" onclick="checkReminderRecurrence(\'#= id #\');">' + _lang.deleteRow + '</a></li>' +
					'</ul></div>',
			title: _lang.actions,
			width: '106px'
		},
		{field: "remindDate", format: "{0:yyyy-MM-dd}", template: "#= (remindDate == null) ? '' : kendo.toString(remindDate, 'yyyy-MM-dd') #", title: _lang.remindOn, width: '120px'},
		{field: "remindTime", template: '#= remindTime.substring(0, 5) #', title: _lang.time, width: '104px'},
		{field: "summary", template: '<a href="javascript:;" onclick="reminderForm(\'#= id #\');">#= summary #</a>', title: _lang.summary, width: '160px'},
		{field: "type", title: _lang.type, width: '100px'},
		{field: "status", title: _lang.status, width: '100px'},
		{field: "remindUser", title: _lang.remindUser,template: '#= (remindUser!=null && userStatus=="Inactive")? remindUser+" ("+_lang.custom[userStatus]+")":((remindUser!=null)?remindUser:"") #', width: '184px'},
		{field: "legal_case", title: _lang.relatedCase, width: '150px'},
		{field: "company", title: _lang.relatedCompany, width: '150px'},
		{field: "contact", title: _lang.relatedContact, width: '140px'},
		{field: "task", title: _lang.relatedTask, width: '140px'},
		{field: "createdByName", title: _lang.createdBy,template: '#= (createdByName!=null && createdByStatus=="Inactive")? createdByName+" ("+_lang.custom[createdByStatus]+")":((createdByName!=null)?createdByName:"") #', width: '140px'},
		{field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '140px'}
	],
        editable: "",
        filterable: false,
        pageable: {
            input: true,
            messages: _lang.kendo_grid_pageable_messages,
            numeric: false,
            pageSizes: [5, 10, 20, 50, 100],
            refresh: true
        },
        reorderable: true,
        resizable: true,
        height: 330,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{
            name: "reminder-grid-toolbar",
            template: '<div class="col-md-3">'
                + '<div class="input-group col-md-12 margin-top">'
                + '<input type="text" class="form-control search" placeholder=" '
                + _lang.searchReminder + '" name="reminderLookUp" id="reminderLookUp" onkeyup="reminderQuickSearch(event.keyCode, this.value);" title="'
                + _lang.searchReminder + '" />'
                + '</div>'
                + '</div>'
                + '<div class="col-md-1 float-right">'
                + '<div class="btn-group float-right">'
                + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                + _lang.actions + ' <span class="caret"></span>'
                + '<span class="sr-only">Toggle Dropdown</span>'
                + '</button>'
                + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                + '<div><a class="dropdown-item" role="button" onclick="addNewCaseReminder()"  >'
                + _lang.addNewReminder + '</a></li>'
                + '</ul>'
                + '</div>'
                + '</div>'
        }]
    };
}

function reminderCallBack() {
    jQuery('#reminders-grid').data("kendoGrid").dataSource.read();
    return true;
}