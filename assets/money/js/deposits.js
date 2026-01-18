var organizationCurrency;
jQuery(document).ready(function () {
    depositsGrid = jQuery('#deposits-grid');
    jQuery('.multi-select', '#deposits-search-filters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        depositsGrid.data('kendoGrid').dataSource.read();
    });
    jQuery('#deposits-search-filters').bind('submit', function (e) {
        e.preventDefault();
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        depositsGrid.data('kendoGrid').dataSource.page(1);
        jQuery('#expenseSearchLookUp').val('');
    });
});

function gridInitialization() {
    depositsSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('money') + "clients/deposits/",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
                    if (jQuery('.grid-main-container').length > 0) { //applied only for the grids that have the new design
                        setTimeout(function () {
                            fixFooterPosition();
                        }, 300);
                    }
                    jQuery('.k-pager-info-top', '.k-grid-info-refresh-top').text(jQuery('.k-pager-info', '.k-grid-pager').text());
                    if (gridAdvancedSearchLinkState) {
                        gridAdvancedSearchLinkState = false;
                    } else {
                        if (jQuery('#filtersFormWrapper').is(':visible')) {
                            jQuery('#filtersFormWrapper').slideUp();
                            scrollToId('#filtersFormWrapper');
                        }
                    }
                    animateDropdownMenuInGrids('deposits-grid');
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.loadWithSavedFilters = 0;
                    if (gridSavedFiltersParams) {
                        options.filter = gridSavedFiltersParams;
                        var gridFormData = [];
                        gridFormData.formData = ["gridFilters"];
                        gridFormData.formData.gridFilters = gridSavedFiltersParams;
                        setGridFiltersData(gridFormData, 'legalCaseGrid');
                        options.loadWithSavedFilters = 1;
                        options.take = gridDefaultPageSize;
                    } else {
                        options.sortData = JSON.stringify(depositsSearchDataSrc.sort());
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
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
                    deposit_id: {type: "integer"},
                    voucher_header_id: {type: "integer"},
                    description: {type: "string"},
                    payment_method: {type: "string"},
                    client_id: {type: "integer"},
                    client_name: {type: "string"},
                    liability_acc_id: {type: "integer"},
                    liability_account: {type: "string"},
                    asset_acc_id: {type: "integer"},
                    asset_account: {type: "string"},
                    deposited_on: {type: "date"},
                    local_amount: {type: "string"},
                    trust_balance: {type: "string"}
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
                        row['client_name'] = escapeHtml(row['client_name']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
    });
    depositsSearchGridOptions = {
        autobind: true,
        dataSource: depositsSearchDataSrc,
        columns: [
            {field: 'id', title: ' ', filterable: false, sortable: false,
                template:
                        '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="depositForm(#= id #,#= voucher_header_id #)">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confirm_delete_record\',{resultHandler: deleteDeposit, parm: \'#= id #\'});">' + _lang.delete + '</a>' +
                        '</div></div>', width: '70px'
            },
            {field: "deposited_on", title: _lang.depositedOn, format: "{0:yyyy-MM-dd}", width: '82px'},
            {field: "deposit_id", template: '<a href="javascript:;" onclick="depositForm(#= id #,#= voucher_header_id #)">#= deposit_id #</a>', title: _lang.depositId, width: '82px'},
            {field: "client_name", template: '<a href="' + getBaseURL('money') + 'clients/client_details/#= client_id #">#= client_name #</a>', title: _lang.clientName_Money, width: '120px'},
            {field: "liability_account", title: _lang.trustLiabilityAccount, width: '190px'},
            {field: "asset_account", title: _lang.trustAssetAccount, width: '170px'},
            {field: "local_amount", title: _lang.depositToAmount + ' (' + organizationCurrency + ')', template: "#= money_format('%i',round(local_amount,2)) #", width: '140px'},
            {field: "trust_balance", title: _lang.trustBalance + ' (' + organizationCurrency + ')', template: "#= trust_balance #", width: '140px'},
            {field: "payment_method", title: _lang.paymentMethod, width: '120px'},
            {field: "description", title: _lang.description, template: '#= description!=null ? (description.length > 50 ? description.substring(0, 50) + "..." : description) : "" #', width: '192px'},
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
                name: "toolbar-menu",
                template: '<div></div>'

            }],
        columnResize: function (e) {
            fixFooterPosition();
            resizeHeaderAndFooter();
        }
    };
    if (depositsGrid.length > 0) {
        if (undefined == depositsGrid.data('kendoGrid')) {
            depositsGrid.kendoGrid(depositsSearchGridOptions);
            fixGridHeader(true);
        } else {
            depositsGrid.empty().kendoGrid(depositsSearchGridOptions);
            jQuery('#grid-header-fixed-navbar').trigger('detach.ScrollToFixed');
            jQuery('#fixed-navbar').trigger('detach.ScrollToFixed');
            fixGridHeader();
        }
    }
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#deposits-search-filters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('deposits-search-filters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['deposited_onValue', 'deposited_onEndValue']});
    clientLookup({"lookupField": jQuery("#client_nameValue")});
    accountLookup(jQuery('#asset_acc_id'), jQuery('#asset_acc_idValue'), 'TrustAsset');
    accountLookup(jQuery('#liability_acc_id'), jQuery('#liability_acc_idValue'), 'TrustLiability');
}
function accountLookup(accountID, accountLookup, accountType) {
    accountLookup.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.type = accountType;
            jQuery.ajax({
                url: getBaseURL('money') + 'accounts/lookup',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name + ' - ' + item.currencyCode,
                                value: item.name + ' - ' + item.currencyCode,
                                record: item
                            };
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            accountID.val(ui.item.record.id);
        }
    });
}
function deleteDeposit(id) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('money') + 'clients/delete_deposit',
        type: 'POST',
        data: {id: id},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                depositsGrid.data('kendoGrid').dataSource.page(1);
            }
            if (typeof response.feedback_message != 'undefined') {
                pinesMessage({ty: response.feedback_message['type'], m: response.feedback_message['message']});
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.deleteRecordFailed.sprintf([_lang.deposit])});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function exportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    newFormFilter.attr('action', getBaseURL('money') + 'clients/export_deposits_to_excel').submit();
}
