var enableQuickSearch = false;
function initClientsGrid() {
    var grid = jQuery('#clientGrid');
    document.getElementsByName("page").value = 1;
    document.getElementsByName("skip").value = 0;
    if (undefined === grid.data('kendoGrid')) {
        grid.kendoGrid(gridOptions);
        fixGridHeader();
        return false;
    }
    grid.data('kendoGrid').dataSource.page(1);
    return false;
}
var gridDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL('money') + "clients/index",
            dataType: "JSON",
            type: "POST",
            complete: function () {
                if (jQuery('#filtersFormWrapper').is(':visible'))
                    jQuery('#filtersFormWrapper').slideUp();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
                animateDropdownMenuInGrids('clientGrid');
            }
        },
        parameterMap: function (options, operation) {
            if ("read" !== operation && options.models) {
                return {
                    models: kendo.stringify(options.models)
                };
            } else {
                options.filter = getFormFilters();
                options.returnData = 1;
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
                id: {editable: false, type: "number"},
                name: {type: "string"},
                type: {type: "string"},
                member_name: {type: "string"}
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
                    row['name'] = escapeHtml(row['name']);
                    row['type'] = escapeHtml(row['type']);
                    row['member_name'] = escapeHtml(row['member_name']);
                    rows.data.push(row);
                }
            }
            return rows;
        }
    },
    error: function (e) {
        defaultAjaxJSONErrorsHandler(e.xhr)
    },
    pageSize: 20,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});
var gridOptions = {};
jQuery(document).ready(function () {
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: 'id', title: ' ', filterable: false, sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="quickAddAccount(\'#= id #\',\'client\',\'#= addslashes(name) #\');">' + _lang.newAccount + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="accountStatement(\'#= id #\');">' + _lang.accountStatement + '</a>' +
                        '</div></div>', width: '17px'
            },
            {field: "name", template: '<a href="' + getBaseURL('money') + 'clients/client_details/#= id #">#= name ? name : "" #</a>', title: _lang.clientName_Money, width: '100px'},
            {field: "type", template: "#= (type == 'Person') ? getTranslation('Contact') : getTranslation(type)#", title: _lang.clientType, width: '70px'},
            {field: "member_name", template: "<a href='" + getBaseURL() + "#= (type == 'Person') ?'contacts/edit/'+member_id: (company_category == 'Internal') ?'companies/tab_company/'+member_id : 'companies/containers_list' #' > #=(type == 'Person') ?'PER'+member_id+'-'+ member_name : (company_category == 'Internal') ? 'COM'+member_id+'-'+member_name  : member_id+'-'+member_name #</a>", title: _lang.relatedTo, width: '70px'},
        ],
        editable: "",
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        },
        toolbar: [{
                name: "client-grid-toolbar",
                template: '<div class="row col-md-12 no-margin no-padding d-flex">'
                        + '<div class="form-inline no-margin col-md-4 no-padding">'
                        + '<h4 class="form-group col-md-5">' + _lang.clients_Money + '</h4>'
                        + '<div class="form-group col-md-5">'
                        + '<input type="text" class="form-control w-100 search" placeholder=" '
                        + _lang.search + '" name="client-search-lookup" id="client-search-lookup" onkeyup="clientQuickSearch(event.keyCode, this.value);" title="'
                        + _lang.searchClient + '" />'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-7 no-padding">'
                        + '<div class="col-md-2 no-padding advanced-search">'
                        + '<a href="javascript:;" class="" onclick="clientsAdvancedSearchFilters()">' + _lang.advancedSearch + '</a>'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-1 align-self-end no-padding">'
                        + '<div class="btn-group">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'clients/add" >' + _lang.addNewClient + ' </a>'
                        + '<a class="dropdown-item" href="javascript:;" class="" onclick="exportClientsToExcel();">' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }]
    };
    initClientsGrid();
    jQuery('#clientSearchFilters').bind('submit', function (e) {
        jQuery("form#clientSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#clientSearchFilters').validationEngine("validate")) {
            return false;
        }

        enableQuickSearch = false;
        e.preventDefault();
        initClientsGrid();
    });
    jQuery('#client-search-lookup').val('');
    enableQuickSearch = false;
});
function clientQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quick-search-filter-client-value', '#filtersFormWrapper').val(term);
        jQuery('#clientGrid').data("kendoGrid").dataSource.page(1);
    }
}
function clientsAdvancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        clientLookup({"lookupField": jQuery("#clientNameValue", "#clientSearchFilters")});
        jQuery('#filtersFormWrapper').slideDown();
    }
    jQuery('html, body').animate({scrollTop: 0}, 0);
}
function hideAdvancedSearch() {
    jQuery('#filtersFormWrapper').slideUp();
}
function exportClientsToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    newFormFilter.attr('action', getBaseURL('money') + 'clients/export_to_excel').submit();
}
function getFormFilters() {
    var filtersForm = jQuery('#clientSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('clientSearchFilters', '.', true);
    if (!enableQuickSearch) {
        var filters = searchFilters.filter;
    } else if (jQuery('#quick-search-filter-client-value', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function quickAddAccount(id, type, clientName) {
    accountsFormDialog = jQuery('#accountsFormDialog');
    jQuery.ajax({
        data: {id: id, type: type},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('money') + 'accounts/quick_add',
        success: function (response) {
            accountsFormDialog.html(response.html);
            jQuery('form#accountsForm').submit(function (e) {
                jQuery('#btnSubmitSave').trigger('click');
                e.preventDefault();
            });
            accountsFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#accountsForm", this).validationEngine('validate');
                            var formData = jQuery("form#accountsForm", this).serializeArray();
                            formData.push({name: 'id', value: id}, {name: 'type', value: type});
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function () {
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'accounts/quick_add',
                                    success: function (response) {
                                        if (!response.status) {
                                            for (i in response.validationErrors) {
                                                pinesMessage({ty: 'error', m: response.validationErrors[i]});
                                            }
                                        } else {
                                            pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.clientAccount])});
                                            jQuery(that).dialog("close");
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
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function () {
                    jQuery(window).unbind('resize');
                },
                draggable: true,
                modal: false,
                open: function () {
                    jQuery('#addAnother', accountsFormDialog).hide();
                    jQuery('ul.breadcrumb', accountsFormDialog).remove();
                    jQuery('.form-action', accountsFormDialog).remove();
                    jQuery('#accountForm').removeClass('col-md-6').addClass('col-md-12');
                    jQuery('#account_type_id', '#accountsForm').parent().parent().remove();
                    jQuery('#account-number', '#accountsForm').removeClass('d-none');
                    jQuery('#name', '#accountsForm').val(clientName);
                    jQuery('#currency_id', '#accountsForm').removeAttr('disabled');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '60%', '500');
                    }));
                    resizeNewDialogWindow(that, '60%', '500');
                },
                resizable: true,
                title: _lang.newAccountFor.sprintf([clientName]),
                responsive: true
            });
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function getTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function accountStatement(clientId) {
    jQuery.ajax({
        url: getBaseURL('money') + 'clients/account_statement/' + clientId,
        dataType: "json",
        success: function (data) {
            if (data.nbOfAccounts == 0) {
                pinesMessage({ty: 'information', m: _lang.noDataForTheAccountStatment});
            } else if (data.nbOfAccounts == 1) {
                window.location = getBaseURL('money') + 'reports/statement/' + data.accountRecords.id;
            } else if (data.nbOfAccounts > 1) {
                if (!jQuery('#clientAccountStatementDialog').length) {
                    jQuery('<div id="clientAccountStatementDialog" class="d-none"></div>').appendTo('body');
                }
                var clientAccountStatementDialog = jQuery('#clientAccountStatementDialog');
                clientAccountStatementDialog.dialog({
                    autoOpen: true,
                    buttons: [
                        {
                            'class': 'btn btn-info btnSaveNew',
                            click: function () {
                                var accountId = jQuery('#accountSelect', this).val();
                                if (accountId) {
                                    window.location = getBaseURL('money') + 'reports/statement/' + accountId;
                                }
                            },
                            text: _lang.goTo
                        }
                    ],
                    open: function () {
                        jQuery('#clientAccountStatementDialog').removeClass('d-none').html(data.html);
//						alert(jQuery('#clientAccountStatementDialog').html());
                    },
                    draggable: false,
                    modal: false,
                    position: {my: 'center', at: 'center'},
                    title: _lang.listClientAccounts,
                    resizable: false
                });
                clientAccountStatementDialog.dialog("open");
            }
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function validateIntegers(field, rules, i, options) {
    var val = field.val();
    var integerPattern = /^(?:[1-9]\d*|0)$/;
    if (!integerPattern.test(val)) {
        return _lang.integerAllowed;
    }
}
