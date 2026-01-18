var enableQuickSearch = true;
jQuery(document).ready(function () {
    var contactRelatedRemindersDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "contacts/related_reminders/" + jQuery('#contactIdFilter').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('related-reminders-grid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" === operation) {
                    options.filter = getFormFilters();
                    options.returnData = 1;
                }
                options.contactIdFilter = jQuery('#contactIdFilter').val();

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
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['assignee'] = escapeHtml(row['assignee']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText != 'True')
                defaultAjaxJSONErrorsHandler(e.xhr)
        },
        batch: true,
        pageSize: 10,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var contactRelatedRemindersGridOptions = {
        autobind: true,
        dataSource: contactRelatedRemindersDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: "id",
                filterable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="reminderForm(\'#= id #\');">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="dismissReminder(\'#= id #\');">' + _lang.dismiss + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="reminderForm(\'#= id #\',\'postpone\');">' + _lang.postpone + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="checkReminderRecurrence(\'#= id #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>',
                title: _lang.actions,
                width: '106px'
            },
		    {field: "remindDate", format: "{0:yyyy-MM-dd}", template: "#= (remindDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(remindDate) : remindDate, 'yyyy-MM-dd'))#", title: _lang.remindOn, width: '140px'},
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
            {field: "createdOn", format: "{0:yyyy-MM-dd}", template: '#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(createdOn) : createdOn), \'yyyy-MM-dd\')) #' + '</a>', title: _lang.createdOn, width: '140px'},

        ],
        toolbar: [{name: "quick-search", template: ' <div class="col-md-4 no-padding"><div class="input-group col-md-11"><input type="text" class="form-control search margin-top quick-search-filter" placeholder=" ' + _lang.searchReminder + '" name="reminderLookUp" id="reminderLookUp" onkeyup="remindersQuickSearch(event.keyCode, this.value);" title="' + _lang.searchReminder + '" /></div></div>'
                        + '</div>'
            }],
        editable: false,
        filterable: false,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        height: '330',
        selectable: "single",
        sortable: {mode: "multiple"}
    };
    var relatedRemindersGrid = jQuery('#related-reminders-grid');
    if (undefined == relatedRemindersGrid.data('kendoGrid')) {
        relatedRemindersGrid.kendoGrid(contactRelatedRemindersGridOptions);
        return false;
    }
    relatedRemindersGrid.data('kendoGrid').dataSource.read();
    return false;
});

function remindersQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        if (term.length > 0) {
            document.getElementsByName("page").value = 1;
            document.getElementsByName("skip").value = 0;
            enableQuickSearch = true;
            jQuery('#quickSearchFilterSummaryValue', '#filtersFormWrapper').val(term);
        } else {
            enableQuickSearch = false;
        }
        jQuery('#related-reminders-grid').data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#contactRelatedRemindersSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('contactRelatedRemindersSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterSummaryValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}