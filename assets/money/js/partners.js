var enableQuickSearch = false;
function initPartnersGrid() {
    var grid = jQuery('#partnerGrid');
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
            url: getBaseURL('money') + "partners/index",
            dataType: "JSON",
            type: "POST",
            complete: function () {
                if (jQuery('#filtersFormWrapper').is(':visible'))
                    jQuery('#filtersFormWrapper').slideUp();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
                animateDropdownMenuInGrids('partnerGrid');
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
                type: {type: "string"}
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
            {
                field: 'id',
                title: ' ',
                filterable: false,
                sortable: false,
                template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="quickAddAccount(\'#= id #\',\'partner\',\'#= addslashes(name) #\');">' + _lang.newAccount + '</a>' +
                        '</div></div>',
                width: '17px'
            },
            {field: "name", template: '<a href="' + getBaseURL('money') + 'partners/partner_details/#= id #">#= name ? name : "" #</a>', title: _lang.partnerName, width: '100px'},
            {field: "type", template: "#= (type == 'Person') ? getTranslation('Contact') : getTranslation(type)#", title: _lang.partnerType, width: '70px'},
            {field: "isThirdParty", template: "#= getTranslation(isThirdParty)#", title: _lang.isThirdParty, width: '140px'}
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
                name: "partner-grid-toolbar",
                template: '<div class="row col-md-12 no-margin no-padding d-flex">'
                        + '<div class="form-inline no-margin col-md-4 no-padding">'
                        + '<h4 class="form-group col-md-5">' + _lang.partners + '</h4>'
                        + '<div class="form-group col-md-5">'
                        + '<input type="text" class="form-control search w-100" placeholder=" '
                        + _lang.search + '" name="partner-search-lookup" id="partner-search-lookup" onkeyup="partnerQuickSearch(event.keyCode, this.value);" title="'
                        + _lang.searchPartner + '" />'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-7 no-padding">'
                        + '<div class="col-md-2 no-padding advanced-search">'
                        + '<a href="javascript:;" class="" onclick="partnersAdvancedSearchFilters()">' + _lang.advancedSearch + '</a>'
                        + '</div>'
                        + '</div>'
                        + '<div class="col-md-1 align-self-end no-padding">'
                        + '<div class="btn-group">'
                        + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                        + _lang.actions + ' <span class="caret"></span>'
                        + '<span class="sr-only">Toggle Dropdown</span>'
                        + '</button>'
                        + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                        + '<a class="dropdown-item" href="' + getBaseURL('money') + 'partners/add" >' + _lang.addNewPartner + ' </a>'
                        + '<a class="dropdown-item" href="javascript:;" class="" onclick="exportClientsToExcel();">' + _lang.exportToExcel + '</a>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
                        + '</div>'
            }]
    };
    initPartnersGrid();
    jQuery('#partnerSearchFilters').bind('submit', function (e) {
        jQuery("form#partnerSearchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#partnerSearchFilters').validationEngine("validate")) {
            return false;
        }

        enableQuickSearch = false;
        e.preventDefault();
        initPartnersGrid();
    });
    jQuery('#partner-search-lookup').val('');
    enableQuickSearch = false;
});
function partnerQuickSearch(keyCode, term) {
    if (keyCode === 13) {
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quick-search-filter-partner-value', '#filtersFormWrapper').val(term);
        jQuery('#partnerGrid').data("kendoGrid").dataSource.page(1);
    }
}
function partnersAdvancedSearchFilters() {
    if (!jQuery('#filtersFormWrapper').is(':visible')) {
        partnerLookup();
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
    newFormFilter.attr('action', getBaseURL('money') + 'partners/export_to_excel').submit();
}
function getFormFilters() {
    var filtersForm = jQuery('#partnerSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('partnerSearchFilters', '.', true);
    if (!enableQuickSearch) {
        var filters = searchFilters.filter;
    } else if (jQuery('#quick-search-filter-partner-value', filtersForm).val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function partnerLookup() {
    jQuery("#partnerNameValue", '#partnerSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'partners/autocomplete',
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
                                label: item.name,
                                value: item.name,
                                record: item
                            };
                        }));
                    }
                }, error: defaultAjaxJSONErrorsHandler
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
}
function quickAddAccount(id, type, partnerName) {
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
                                            pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.partnerAccount])});
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
                    jQuery('#name', '#accountsForm').val(partnerName);
                    jQuery('#currency_id', '#accountsForm').removeAttr('disabled');
                    var that = jQuery(this);
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '60%', '500');
                    }));
                    resizeNewDialogWindow(that, '60%', '500');
                },
                resizable: true,
                responsive: true,
                title: _lang.newAccountFor.sprintf([partnerName])
            });
        }, error: defaultAjaxJSONErrorsHandler,
    });
}
;
function getTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function validateIntegers(field, rules, i, options) {
    var val = field.val();
    var integerPattern = /^(?:[1-9]\d*|0)$/;
    if (!integerPattern.test(val)) {
        return _lang.integerAllowed;
    }
}
