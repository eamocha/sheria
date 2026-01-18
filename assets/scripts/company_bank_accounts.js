function getFormFilters() {
    var filtersForm = jQuery('#companyBankAccountsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companyBankAccountsSearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
jQuery(document).ready(function () {
    bankAccountDialogForm();
    var companyBankAccountsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/bank_accounts/" + jQuery('#companyIdFilter').val(),
                dataType: "JSON",
                type: "POST",
                complete: function(){
                     if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                     animateDropdownMenuInGrids('bankAccountsGrid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" === operation && options.models) {
                    options.filter = getFormFilters();
                    options.returnData = 1;
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
                    company_id: {type: "string"},
                    bankName: {type: "string"},
                    bankFullAddress: {type: "string"},
                    bankPhone: {type: "string"},
                    bankFax: {type: "string"},
                    accountName: {type: "string"},
                    accountCurrency: {type: "string"},
                    accountNb: {type: "string"},
                    swiftCode: {type: "string"},
                    iban: {type: "string"},
                    comments: {type: "string"}
                }
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
    var companyBankAccountsGridOptions = {
        autobind: true,
        dataSource: companyBankAccountsDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: 'actions', title: ' ', filterable: false, sortable: false, template:
                        '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="editBankAccount(\'#= id #\');">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteBankAccount(\'#= id #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>', title: ' ', width: '50px'
            },
            {field: "bankName", title: _lang.bankName, width: '120px'},
            {field: "accountCurrency", title: _lang.accountCurrency, width: '90px'},
            {field: "swiftCode", title: _lang.swiftCode, width: '80px'},
            {field: "iban", title: _lang.iban, width: '160px'},
            {field: "comments", title: _lang.comments, width: '160px'}
        ],
        toolbar: [
            {
                name: "bank-account-toolbar",
                template: '<div class="col-md-4 no-padding">'
                    + '<h4>' + _lang.bankAccounts + '</h4>'
                    + '</div>'
            }
        ],
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
    var bankAccountsGrid = jQuery('#bankAccountsGrid');
    if (undefined == bankAccountsGrid.data('kendoGrid')) {
        bankAccountsGrid.kendoGrid(companyBankAccountsGridOptions);
        return false;
    }
    bankAccountsGrid.data('kendoGrid').dataSource.read();
    return false;
});
function openBankAccountForm() {
    var bankAccountDialog = jQuery("#bankAccountDialog");
    jQuery('#id', '#bankAccountForm').val('');
    bankAccountDialog.dialog("open");
}
function bankAccountDialogForm() {
    //UI Dialog
    jQuery('#id', '#bankAccountForm').val('');
    var bankDialog = jQuery("#bankAccountDialog");
    bankDialog.dialog({
        autoOpen: false,
        buttons: [{
                text: _lang.save,
                'class': 'btn btn-info',
                click: function () {
                    var dataIsValid = jQuery("form#bankAccountForm", this).validationEngine('validate');
                    var formData = jQuery("form#bankAccountForm", this).serialize();
                    var urlRequest = jQuery('#id', '#bankAccountForm').val() ?
                            'companies/edit_bank_account/' + jQuery('#id', '#bankAccountForm').val() :
                            'companies/add_bank_account';
                    if (dataIsValid) {
                        var that = this;
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery("#output", that).html('<img height="18" src="assets/images/icons/16/loader-submit.gif" />');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + urlRequest,
                            success: function (response) {
                                jQuery("#output", that).html('&nbsp;');
                                if (response.result) {
                                    jQuery(that).dialog("close");
                                    jQuery("form#bankAccountForm", that)[0].reset();
                                    jQuery('#bankAccountsGrid').data("kendoGrid").dataSource.read();
                                } else {
                                    for (i in response.errors) {
                                        jQuery('#' + i, that).addClass("invalid");
                                        pinesMessageV2({ty: 'error', m: response.errors[i]});
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
                    jQuery(this).dialog("close");
                    jQuery("form#bankAccountForm", this)[0].reset();
                    jQuery('#comments', '#bankAccountForm').text('');
                }
            }],
        close: function () {
            jQuery("form#bankAccountForm", this).validationEngine('hide');
            jQuery("form#bankAccountForm", this)[0].reset();
            jQuery('#comments', '#bankAccountForm').text('');
            jQuery(window).unbind('resize');
        },
        open: function () {
            setTimeout(function(){jQuery("#bankName").focus()},200);
            var that = jQuery(this);
            that.removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '65%', '533');
            }));
            resizeNewDialogWindow(that, '65%', '533');
        },
        draggable: true,
        modal: false,
        title: _lang.bankAccountForm,
        resizable: true,
        responsive: true
    });
    jQuery("#bankAccountForm").validationEngine({
		validationEventTrigger :"submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
}
function editBankAccount(Id) {
    var bankAccountDialog = jQuery("#bankAccountDialog");
    jQuery.ajax({
        url: getBaseURL() + 'companies/edit_bank_account/' + Id,
        dataType: "json",
        beforeSend: function () {
            jQuery("#output", '#bankAccountForm').html('<img height="18" src="assets/images/icons/16/loader-submit.gif" />');
            jQuery('#bankAccountFormFieldSet').hide();
        },
        success: function (response) {
            bankAccountDialog.dialog("open");
            if (response.id) {
                var memberType = (response.memberType == 'Company') ? 'companies' : 'contacts';
                jQuery('#id', '#bankAccountForm').val(response.id);
                jQuery('#bankName', '#bankAccountForm').val(response.bankName);
                jQuery('#bankFullAddress', '#bankAccountForm').val(response.bankFullAddress);
                jQuery('#bankPhone', '#bankAccountForm').val(response.bankPhone);
                jQuery('#bankFax', '#bankAccountForm').val(response.bankFax);
                jQuery('#accountName', '#bankAccountForm').val(response.accountName);
                jQuery('#accountCurrency', '#bankAccountForm').val(response.accountCurrency);
                jQuery('#accountNb', '#bankAccountForm').val(response.accountNb);
                jQuery('#swiftCode', '#bankAccountForm').val(response.swiftCode);
                jQuery('#iban', '#bankAccountForm').val(response.iban);
                if (response.comments)
                    jQuery('#comments', '#bankAccountForm').text(response.comments);
                else
                    jQuery('#comments', '#bankAccountForm').text('');
                jQuery('#bankAccountFormFieldSet').show();
            }
        },error: defaultAjaxJSONErrorsHandler
    });
}
function deleteBankAccount(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_bank_account/' + id,
            type: 'POST',
            dataType: 'JSON',
            data: {
                archivedHardCopyId: id
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// removed successfuly
                        ty = 'information';
                        m = _lang.bankAccountDeletedSuccessfully;
                        jQuery('#bankAccountsGrid').data("kendoGrid").dataSource.read();
                        break;
                    case 101:	// could not remove record
                        m = _lang.recordNotDeleted;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: ty, m: m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
