var depositsGrid, trustAssetAccountsPerClient;

function depositForm(id, voucherHeaderId, clientData) {

    id = id || false;

    voucherHeaderId = voucherHeaderId || false;

    clientData = clientData || false;

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('money') + 'clients/' + (!id ? 'add_deposit' : 'edit_deposit/' + id + '/' + voucherHeaderId),
        type: 'GET',
        beforeSend: function () {

            jQuery('#loader-global').show();

        },
        success: function (response) {
            if (typeof response.result !== 'undefined' && !response.result) {
                if (typeof response.message !== 'undefined' && response.message) {
                    // pinesMessage({ty: 'error', m: response.message});
                    setTrustAccount(function(){
                        depositForm(id, voucherHeaderId, clientData);   //after success add a trust fund account, call same ft. to open the add modal
                    });
                } else {
                    window.location = getBaseURL('money') + 'setup/rate_between_money_currencies';
                }
            }
            else if (response.html && jQuery('#deposit-form-container').length <= 0) {
                jQuery('<div id="deposit-form-container"></div>').appendTo("body");
                var depositFormContainer = jQuery('#deposit-form-container');
                depositFormContainer.html(response.html);
                if (clientData) {
                    jQuery('#lookup-client-id', depositFormContainer).val(clientData.client_id);
                    jQuery('#lookup-client', depositFormContainer).val(clientData.client_name);
                    setTrustAssetAccountPerClient(depositFormContainer);
                }
                commonModalDialogEvents(depositFormContainer);
                jQuery("#form-submit", depositFormContainer).click(function () {

                    depositFormSubmit(id, voucherHeaderId, depositFormContainer);

                });

                jQuery(depositFormContainer).find('input').keypress(function (e) {

                    // Enter pressed?

                    if (e.which == 13) {

                        depositFormSubmit(id, voucherHeaderId, depositFormContainer);

                    }

                });

                depositFormEvents(depositFormContainer);

            }

        }, complete: function () {

            jQuery('#loader-global').hide();

        },
        error: defaultAjaxJSONErrorsHandler

    });

}

function depositFormEvents(container) {

    initializeModalSize(container);

    jQuery('.select-picker', container).selectpicker({dropupAuto: false});

    setDatePicker('.date-picker', container);

    lookUpClients({'lookupField': jQuery('#lookup-client', container), 'hiddenId': jQuery('#lookup-client-id', container), 'errorDiv': 'client', 'callBackFunction': setTrustAssetAccountPerClient}, container);

    showToolTip();

}

function depositFormSubmit(id, voucherHeaderId, container) {

    id = id || false;

    voucherHeaderId = voucherHeaderId || false;

    var formData = jQuery('#deposit-form', container).serialize();

    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('money') + 'clients/' + (!id ? 'add_deposit' : 'edit_deposit/' + id + '/' + voucherHeaderId),
        data: formData,
        type: 'POST',
        beforeSend: function () {

            jQuery('#loader-global').show();

        },
        success: function (response) {

            jQuery('.inline-error', container).addClass('d-none');

            if (response.validation_errors) {

                displayValidationErrors(response.validation_errors, container);

                return false;

            }

            if (response.result) {

                pinesMessage({ty: 'success', m: id ? _lang.feedback_messages.updatesSavedSuccessfully : _lang.recordAddedSuccessfully});

                if (jQuery('#client-details-container').length > 0) {

                    clientTrustBalance(jQuery('#lookup-client-id', container).val(), jQuery('#client-account-status'));

                }

                jQuery(".modal", container).modal("hide");

                if (typeof depositsGrid !== 'undefined') {

                    depositsGrid.data('kendoGrid').dataSource.page(1);

                }

            } else {

                pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});



            }



        }, complete: function () {

            jQuery('#loader-global').hide();

        },
        error: defaultAjaxJSONErrorsHandler

    });

}

function setTrustAssetAccountPerClient(container) {

    var clientId = jQuery('#lookup-client-id', container).val();

    if (typeof trustAssetAccountsPerClient[clientId] != 'undefined') {

        jQuery('#trust-asset-account', container).html(trustAssetAccountsPerClient[clientId]['asset_acc_name']);

        jQuery('#trust-asset-account-id', container).val(trustAssetAccountsPerClient[clientId]['asset_acc_id']);

    }

}

function changeToLocalAmount(container) {

    var foreignAmountVal = jQuery('#amount-in-foreign-currency', container).val();

    var foreignCurrency = jQuery('#foreign-currency-id', container).val();

    if (foreignAmountVal !== '' && !isNaN(foreignAmountVal)) {

        var exchangeRates = jQuery.parseJSON(jQuery('#exchange-rates').html());

        jQuery('#amount-in-local-currency-container', container).removeClass('d-none');

        var localCurrrency = jQuery('#local-currency-id', container).val();

        var localAmountVal;

        if (foreignCurrency !== localCurrrency) {

            localAmountVal = round(foreignAmountVal * exchangeRates[foreignCurrency] * 1, 2, 2);

        } else {

            localAmountVal = foreignAmountVal;

        }

        jQuery('#amount-in-local-currency', container).html(localAmountVal);

        jQuery('#amount-to-submit', container).val(localAmountVal);

    } else {

        jQuery('#amount-in-local-currency', container).html('');

        jQuery('#amount-to-submit', container).val('');

        jQuery('#amount-in-local-currency-container', container).addClass('d-none');

    }

}
function invoiceExport(id, autoExportType, type) {
    autoExportType = autoExportType || '';
    type = type || 'invoice';
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/invoice_export_options',
        type: "GET",
        data: {'return': 'html', 'type': type, 'auto_export_type': autoExportType, 'id': id},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var exportOptions = "#export-options-container";
                if (jQuery(exportOptions).length <= 0) {
                    jQuery("<div id='export-options-container'></div>").appendTo("body");
                    var exportOptionsContainer = jQuery(exportOptions);
                    exportOptionsContainer.html(response.html);
                    jQuery('.select-picker', exportOptionsContainer).selectpicker();
                    commonModalDialogEvents(exportOptionsContainer);
                    jQuery("#form-submit", exportOptionsContainer).click(function () {
                        exportSubmit(id, exportOptionsContainer);
                    });
                    jQuery(exportOptionsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            exportSubmit(id, exportOptionsContainer);
                        }
                    });
                    //if there is a default template or invoice related template then auto submit the modal
                    if(response.selected_template_id)
                        exportSubmit(id, exportOptionsContainer);
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function hide_unhide_date_item(voucherID, displayItem, formDetails, type) {
    formDetails = formDetails || false;
    voucherID = voucherID || false;
    if (voucherID) {
        jQuery.ajax({
            url: getBaseURL('money') + 'vouchers/edit_' + type + '_hide_unhide_item_date/' + voucherID,
            type: 'POST',
            dataType: 'JSON',
            data: {display: displayItem},
            success: function (response) {
                if (response.status) {
                    window.location = getBaseURL('money') + 'vouchers/' + type + '_edit/' + jQuery('#voucher_header_id').val();
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        window.location = getBaseURL('money') + 'vouchers/add_' + type + '_hide_unhide_item_date/' + formDetails['activateTax'] + '/' + formDetails['activateDiscount'] + '/' + displayItem;
    }
}

function RemoveFormatMoney(selector , allowMiuns){
    setTimeout(function (){
        var input = jQuery(selector).val();
        var input_replaced = input.replace(",","");
        var ex = (allowMiuns)?/^[0-9]+\-\.?[0-9]*$/:/^[0-9]+\.?[0-9]*$/;
        if(ex.test(input_replaced)==false){
            if(allowMiuns){
                input_replaced = input_replaced.replace(/[^\d\-.]/g, '');
                const regex = /^[+-]?[0-9]{0,12}(?:\.[0-9]{0,12})?$/gm;
                let m;
                if ((m = regex.exec(input_replaced)) !== null) {
                    input_replaced = m[0];
                }else{
                    var posn = jQuery(selector)[0].selectionStart;
                    input_replaced = input_replaced.substr(0,posn-1) + input_replaced.substr(posn,input_replaced.length-posn) 
                }
            }else{
                input_replaced = input_replaced.replace(/[^\d.]/g, '');
                input_replaced.replace( /^([^.]*\.)(.*)$/, function ( a, b, c ) { 
                    input_replaced = b + c.replace( /\./g, '' );
                });  
            }
        }
        jQuery(selector).val(input_replaced);
    }, 200);
}

/**
 * On change date operator value
 * @param element Select date operator
 * @param container #time-logs-date-filter-container
 */
function onchangeOperatorsTimeLogsFiltersDate(element,container){
    let jContainer = jQuery('#'+container);
    let hiddenInputs = jQuery("#hidden-time-logs :input",jContainer);
    onchangeOperatorsFiltersDate(element,container);
    jQuery(element).val() === "cast_between" ? hiddenInputs.attr("disabled", false): hiddenInputs.attr("disabled", true);
}


/**
 * On filter values change
 */
function onSubmitFilterValues() {
    let timeLogsFilterContainer = jQuery('#time-logs-date-filter-container');
    if (timeLogsFilterContainer.length > 0) {
        getFilteredTimeLogs(timeLogsFilterContainer);
    }
}

/**
 * @function Get filtered Data from server
 */
function getFilteredTimeLogs(container) {
    let timeLogsDateFilterForm = jQuery('form#time-logs-date-filter-form',container);
    timeLogsDateFilterForm.submit(false);
    let timeLogsDateFilterFormData = timeLogsDateFilterForm.serializeArray();
    jQuery(selectedCases).each(function (index,val) {
        timeLogsDateFilterFormData.push(val);
    });
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/get_filtered_time_logs',
        dataType: "json",
        type: 'POST',
        data: timeLogsDateFilterFormData,
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            getFilteredTimeLogsOnSuccess(response)
        },
        complete: function () {
            jQuery("#loader-global").hide();
        }, error: defaultAjaxJSONErrorsHandler
    });
}

/**
 * Get filter time logs response
 * @param response
 */
function getFilteredTimeLogsOnSuccess (response) {
    timeLogsResponse = {};
    timeLogsResponse = response.time_logs;
    let matterContainer = jQuery('#relate-matters-container');
    let timeLogsContainer = jQuery('#time-logs-table-container');
    let taxDiscountFilterContainer = jQuery('#time-logs-date-filter-tax-discount-container');
    if (!jQuery.isEmptyObject(timeLogsResponse.data)) {
        timeLogsContainer.html(timeLogsResponse.html);
        taxDiscountFilterContainer.removeClass('d-none');
        jQuery('#time-logs-date-filter-operator',timeLogsContainer).trigger("change");
        jQuery('#submit-actions', matterContainer).html(_lang.next);
        jQuery('#submit-actions', matterContainer).attr('action', 'submit-form');
        initializeSelectAllCheckbox(matterContainer);
    }else{
        timeLogsContainer.html("<p class='border-radius-5 text-center padding-15 border caseRanges'>"+_lang.noRecordFound+"</p>");
        taxDiscountFilterContainer.addClass('d-none');
        jQuery('#submit-actions', matterContainer).html(_lang.ok);
        jQuery('#submit-actions', matterContainer).attr('action', 'no-actions');
    }
}
function settlementOfAccount(accountID, balance, invoiceId) {
    balance = balance || 0;
    invoiceId = invoiceId || undefined;
    settlementOfAccountDialog = jQuery('#settlementOfAccountDialog');
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/settlement_of_partner_account',
        type: 'POST',
        dataType: 'JSON',
        data: invoiceId ? { account_id: accountID, invoice_id: invoiceId, balance: Math.abs(balance) } : { account_id: accountID, balance: Math.abs(balance) },
        success: function(response) {
            settlementOfAccountDialog.html(response.html);
            settlementOfAccountDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function() {
                            var dataIsValid = jQuery("form#settlementOfAccountForm", this).validationEngine('validate');
                            var formData = jQuery("form#settlementOfAccountForm", this).serialize();
                            if (invoiceId) {
                                formData += '&invoice_id=' + invoiceId;
                            }
                            if (dataIsValid) {
                                var that = this;
                                jQuery.ajax({
                                    beforeSend: function() {
                                        jQuery("#loader-global").show();
                                    },
                                    data: formData,
                                    dataType: 'JSON',
                                    type: 'POST',
                                    url: getBaseURL('money') + 'vouchers/settlement_of_partner_account',
                                    success: function(response) {
                                        jQuery("#loader-global").hide();
                                        switch (response.status) {
                                            case 102:
                                                pinesMessage({ ty: 'error', m: response.invalid_request });
                                                break;
                                            case 101:
                                                if (response.validationErrors) {
                                                    for (i in response.validationErrors) {
                                                        pinesMessage({ ty: 'error', m: response.validationErrors[i] });
                                                    }
                                                }
                                                break;
                                            case 500:
                                                pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                                                if (jQuery('#accountsGrid').length) {
                                                    jQuery('#accountsGrid').data("kendoGrid").dataSource.read();
                                                } else {
                                                    window.location.reload();
                                                }
                                                break;
                                        }
                                        jQuery(that).dialog("close");
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        }
                    },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function() {
                            jQuery(this).dialog("close");
                        }
                    }
                ],
                close: function() {
                    jQuery(window).unbind('resize');
                },
                draggable: true,
                modal: false,
                open: function() {
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function() {
                        resizeNewDialogWindow(that, '60%', '450');
                    }));
                    resizeNewDialogWindow(that, '60%', '450');
                },
                resizable: true,
                responsive: true,
                title: _lang.settlementOfPartnerAccount
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function partnerInvoiceExport(id) {
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/partner_invoice_export_options/'+id,
        type: "GET",
        data: {'return': 'html'},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var exportOptions = "#export-options-container";
                if (jQuery(exportOptions).length <= 0) {
                    jQuery("<div id='export-options-container'></div>").appendTo("body");
                    var exportOptionsContainer = jQuery(exportOptions);
                    exportOptionsContainer.html(response.html);
                    jQuery('.select-picker', exportOptionsContainer).selectpicker();
                    commonModalDialogEvents(exportOptionsContainer);
                    jQuery("#form-submit", exportOptionsContainer).click(function () {
                        account_id = jQuery('#partner-list option:selected',exportOptionsContainer).val();
                        (account_id) ? partnerExportSubmit(id, exportOptionsContainer, 'partner_', account_id) : pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.partner])});
                    });
                    jQuery(exportOptionsContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            account_id = jQuery('#partner-list option:selected',exportOptionsContainer).val();
                            (account_id) ? partnerExportSubmit(id, exportOptionsContainer, 'partner_', account_id) : pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.partner])});
                        }
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function configureInvoiceDiscount(redirect) {
    redirect = redirect || false
    jQuery.ajax({
        url: getBaseURL('money') + 'setup/configure_invoice_discount',
        type: "GET",
        data: {return_html: true},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var invoiceDiscountId = "#invoice-discount-container";
                if (jQuery(invoiceDiscountId).length <= 0) {
                    jQuery("<div id='invoice-discount-container'></div>").appendTo("body");
                    var invoiceDiscountContainer = jQuery(invoiceDiscountId);
                    invoiceDiscountContainer.html(response.html);
                    commonModalDialogEvents(invoiceDiscountContainer,setInvoiceDiscountConfigSubmit, redirect ? 'reload' : 'close_modal' );
                     initializeModalSize(invoiceDiscountContainer,0.4,0.3);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function setInvoiceDiscountConfigSubmit(container, redirect) {
    redirect = redirect || '';
    var formData = jQuery("form#activate-discount-form", container).serialize();
    jQuery.ajax({
        url: getBaseURL('money') + 'setup/configure_invoice_discount',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                if(redirect == 'close_modal') jQuery(".modal", container).modal("hide");
                if(redirect == 'reload') location.reload();
            }
            if (typeof response.validation_errors !== 'undefined') {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function selectPickerLoad(id, container){
    jQuery(id, container).selectpicker({dropupAuto: false});
}

function onChangeEInvoicing(event) {
    if (event.target.value === "active") {
        pinesMessageV2({ ty: 'warning', m: _lang.money.enableEInvoicingWarning, d: 5000 });
    }
}

function onChangeOrganizationCountry(countriesCodes) {
    const saudiArabia = countriesCodes['SA'];
    let countrySelect = jQuery("#country_id", "#organizationForm");
    if (countrySelect.val() === saudiArabia) {
        jQuery("#zatca-icon-helper").removeClass("hidden");
    } else {
        jQuery("#zatca-icon-helper").addClass("hidden");
    }
}

function validateEInvoicingFields(data, countriesCodes) {
    var saudiArabiaId = countriesCodes['SA'];
    var fieldsLabels = ['address', 'city', 'country', 'taxNumber', 'streetName', 'buildingNumber', 'addressAdditionalNumber', 'additionalIds'];
    var eInvoicingValue = data.find(item => item.name == "e_invoicing")?.value;
    var countryValue = data.find(item => item.name == "country_id")?.value;
    var addressValue = data.find(item => item.name == "address1")?.value;
    var cityValue = data.find(item => item.name == "city")?.value;
    var taxNumberValue = data.find(item => item.name == "tax_number")?.value;

    var streetValue = data.find(item => item.name == "street_name")?.value;
    var buildingValue = data.find(item => item.name == "building_number")?.value;
    var additionalIdType = data.find(item => item.name == "additional_id_type")?.value;
    var additionalIdValue = data.find(item => item.name == "additional_id_value")?.value;
    var addressAddNumberValue = data.find(item => item.name == "address_additional_number")?.value;
    if (eInvoicingValue !== "inactive" && (!countryValue || countryValue === saudiArabiaId) && (!addressValue || !cityValue || !taxNumberValue || !streetValue || !buildingValue || !additionalIdType || !additionalIdValue || !addressAddNumberValue)) {
        pinesMessage({ty: 'error', m: _lang.money.eInvoicingMissingFields + " " + fieldsLabels.map(item => (_lang[item])).join(", "), d: 5000});
        return false;
    }
    return true;
}

function validatePercentage(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^(\d+\.?\d{0,2}|\.\d{1,2})$/;
    if (val.length != 0 && !decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
    if (val > 100) {
        return _lang.discountmaxValue;
    }
}
function setTrustAccount(callback) {
    jQuery.ajax({
        url: getBaseURL('money') + 'setup/set_trust_account',
        type: "GET",
        data: {return_html: true},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var trustAccountsId = "#trust-accounts-container";
                if (jQuery(trustAccountsId).length <= 0) {
                    jQuery("<div id='trust-accounts-container'></div>").appendTo("body");
                    var trustAccountsContainer = jQuery(trustAccountsId);
                    trustAccountsContainer.html(response.html);
                    commonModalDialogEvents(trustAccountsContainer,setTrustAccountSubmit, callback);
                    jQuery('#accounts', trustAccountsContainer).selectpicker({
                        dropupAuto: false
                    });
                     initializeModalSize(trustAccountsContainer,0.4,0.2);
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function setTrustAccountSubmit(container, callback) {
    var formData = jQuery("form#global-account-to-entity", container).serialize();
    jQuery.ajax({
        url: getBaseURL('money') + 'setup/set_trust_account',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                jQuery(".modal", container).modal("hide");
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                if(callback)
                    callback();
            }
            if (typeof response.validation_errors !== 'undefined') {
                displayValidationErrors(response.validation_errors, container);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function openFileViewer(file){
    jQuery.ajax({
        url: getBaseURL('money') + 'vouchers/open_file_viewer',
        type: "GET",
        data: {'file': file},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var viewerContainer = "#file-viewer-container";
                if (jQuery(viewerContainer).length <= 0) {
                    jQuery("<div id='file-viewer-container'></div>").appendTo("body");
                    var viewerContainer = jQuery(viewerContainer);
                    viewerContainer.html(response.html);
                    commonModalDialogEvents(viewerContainer);
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
