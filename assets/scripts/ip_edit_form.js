jQuery(document).ready(function () {
    jQuery('#ipEditForm').validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    bindIpFormEvents();
    jQuery('#renewals').click();
});

function bindIpFormEvents() {
    var ipFormDialog = '#ipFormDialog';
    jQuery('#provider_group_id', ipFormDialog)
            .change(function () {
                if (jQuery('#provider_group_id', ipFormDialog).val() != '') {
                    jQuery("#userId", ipFormDialog).removeAttr('disabled');
                    reloadUsersListByProviderGroupSelected(jQuery('#provider_group_id', ipFormDialog).val(), jQuery("#userId", ipFormDialog));
                } else {
                    jQuery("#userId", ipFormDialog).html('').attr('disabled', 'disabled').trigger("chosen:updated");
                    jQuery("#user_id", ipFormDialog).val('');
                }
            }).chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseProviderGroup, height: "150px", width: "100%"
    });
    jQuery('#userId', ipFormDialog).change(function () {
        if (jQuery('#userId', ipFormDialog).val() == 'quick_add') {
            jQuery('#userId', ipFormDialog).val('').trigger("chosen:updated");
            addUserToTheProviderGroup(jQuery('#provider_group_id', ipFormDialog).val(), 'userId');
        }
    }).chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.chooseUsers, height: 130, width: "100%"});
    jQuery('#country_id', ipFormDialog).chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.chooseCountry, height: 130, width: "100%"});
    caseClientFieldEvents('ipEditForm');
    agentFieldEvents('ipEditForm');
    makeFieldsDatePicker({fields: ['registrationDate'], hiddenField: 'registrationDate_Hidden', container: 'ipEditForm'});
    makeFieldsDatePicker({fields: ['arrivalDate'], hiddenField: 'arrivalDate_Hidden', container: 'ipEditForm'});
    makeFieldsDatePicker({fields: ['acceptanceRejection'], hiddenField: 'acceptanceRejection_Hidden', container: 'ipEditForm'});
    if (jQuery('.visualize-hijri-date', ipFormDialog).length > 0) {
        getHijriDate(jQuery('#registrationDate', ipFormDialog), jQuery('#registration-date-container', ipFormDialog), true);
        getHijriDate(jQuery('#arrivalDate', ipFormDialog), jQuery('#filed-on-date-container', ipFormDialog), true);
        getHijriDate(jQuery('#acceptanceRejection', ipFormDialog), jQuery('#acceptance-rejection-date-container', ipFormDialog), true);
    }
}


function agentFieldEvents(container) {
    jQuery('#agentType', '#' + container).change(function () {
        jQuery("#agentId", '#' + container).val('');
        jQuery("#agentLookup", '#' + container).val('');
        if (jQuery('#agentLinkId', '#' + container).length) {
            jQuery('#agentLinkId', '#' + container).addClass('d-none');
        }
    });
    jQuery('#agentLookup', '#' + container).change(function () {
        if (this.value == '')
            jQuery("#agentId", '#' + container).val('');
    });
    jQuery("#agentLookup", '#' + container).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#agentType", '#' + container).val();
            jQuery.ajax({
                url: getBaseURL() + lookupType + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_add.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            if (lookupType == 'contacts') {
                                return {
                                    label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                    record: item
                                }
                            } else if (lookupType == 'companies') {
                                return {
                                    label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                    value: item.name,
                                    record: item
                                }
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            var lookupType = jQuery("select#agentType", '#' + container).val();
            if (ui.item.record.id > 0) {
                if (jQuery('#agentLinkId', '#' + container).length) {
                    var clientHref = '';
                    if (lookupType == 'contacts') {
                        clientHref = 'contacts/edit/';
                    } else {
                        if (ui.item.record.category === 'Internal') {
                            clientHref = 'companies/tab_company/';
                        } else if (ui.item.record.category === 'Group') {
                            jQuery('#agentLinkId', '#' + container).addClass('d-none');
                        }
                    }
                    if (ui.item.record.category !== 'Group') {
                        jQuery('#agentLinkId', '#' + container).attr('href', getBaseURL() + clientHref + ui.item.record.id).removeClass('d-none');
                    }
                }
                jQuery('#agentId', jQuery(this).parent()).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (jQuery('#agentLinkId', '#' + container).length) {
                    jQuery('#agentLinkId', '#' + container).addClass('d-none');
                }
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": jQuery("#" + container),
                        "lookupResultHandler": setDataToCaseAgentField,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": jQuery("#" + container),
                        "lookupResultHandler": setDataToCaseAgentField,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        },
        open: function (event, ui) {
            disableBlurEventToCheckLookupValidity = true;
        },
        close: function (event, ui) {
            disableBlurEventToCheckLookupValidity = false;
        }
    });
}

function addNewCaseTask() {
    var caseId = jQuery('#id', '#ipEditForm').val();
    taskAddForm(caseId);
}

try {
    var gridSize10 = 10;
    var countriesRenewalsGridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "intellectual_properties/renewals/",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    showHideGridContent(response, 'countriesRenewalsGrid');
                    if (response.totalRows < gridSize10)
                        jQuery('.k-pager-wrap', jQuery('#countriesRenewalsGrid')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "intellectual_properties/renewals",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response) {
                        if (response.result) {
                            pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                            if (jQuery('#notify-me-before-container', '#countriesRenewalsGrid').is(':visible')) {
                                loadUserLatestReminders('refresh');
                            }
                            jQuery('#countriesRenewalsGrid').data('kendoGrid').dataSource.read();
                        } else {
                            var errorMsg = '';
                            for (i in response.validationErrors) {
                                errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                            }
                            if (errorMsg != '') {
                                pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                            }
                        }
                    }
                    else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['countryName']) < 1) {
                            options.models[i]['countryName'] = null;
                        }
                    }
                    return {
                        models: kendo.stringify(options.models),
                        action: 'updateCountriesRenewals'
                    };
                } else {
                    options.filter = getFormFilters('countriesRenewalsFilters');
                    options.returnData = 1;
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    renewalDate: {type: "date"},
                    expiryDate: {type: "date"},
                    comments: {type: "string"},
                    usersToRemind: {editable: false, type: "string"},
                    actions: {editable: false, type: "string"}
                }
            }
        }, error: function (e) {
            if (e.xhr.responseText != 'True')
                defaultAjaxJSONErrorsHandler(e.xhr)
        },
        batch: true,
        pageSize: gridSize10,
        editable: true,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });

    var countriesRenewalsGridOptions = {
        autobind: true,
        dataSource: countriesRenewalsGridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "renewalDate", title: _lang.renewalDate, format: "{0:yyyy-MM-dd}", width: '250px'},
            {field: "expiryDate", title: _lang.expiryDate, format: "{0:yyyy-MM-dd}", width: '250px'},
            {field: "comments", title: _lang.comments, width: '200px'},
            {field: "usersToRemind", title: _lang.usersToRemind, width: '150px'},
            {field: "actions", sortable: false, title: _lang.actions, width: '80px', template: '<a href="javascript:;" onclick="deleteCountryRenewal(\'#= id #\', \'deleteCountryRenewal\', \'countriesRenewalsGrid\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>'}
        ],
        editable: true,
        filterable: false,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [
            {name: "renewals-grid-toolbar",
                template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<h4 class="col-md-5 no-padding">' + _lang.renewals + '</h4>&nbsp;&nbsp;'
                        + '</div>'
                        + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left">'
                        + '<input type="button"  style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addCountriesRenewals(\'countriesRenewalsDialog\')" value="'
                        + _lang.add + '" />'
                        + '</div>'

            }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}
        ]
    };
} catch (e) {
}


function toggleCountriesRenewalsContainer(icon, div) {
    toggleFieldsetGroup(icon, div);
    var countriesRenewalsGridId = jQuery('#countriesRenewalsGrid');
    if (undefined == countriesRenewalsGridId.data('kendoGrid')) {
        countriesRenewalsGridId.kendoGrid(countriesRenewalsGridOptions);
        customGridToolbarCSSButtons();
    }
}

function addCountriesRenewals(countriesRenewalsDialogID) {
    var countriesRenewalsDialog = jQuery('#' + countriesRenewalsDialogID);
    var countriesAddForm = jQuery('form#countriesRenewalsAddForm', countriesRenewalsDialog);
    if (!countriesRenewalsDialog.is(':data(dialog)')) {
        countriesRenewalsDialog.dialog({
            autoOpen: false,
            buttons: [
                {
                    'class': 'btn btn-info',
                    click: function () {
                        var that = this;
                        if (countriesAddForm.validationEngine('validate')) {
                            jQuery.ajax({
                                data: jQuery(countriesAddForm, that).serialize(),
                                beforeSend: function () {
                                    jQuery('#countriesDialogSave', countriesRenewalsDialog.parent()).attr('disabled', 'disabled');
                                },
                                success: function (response) {
                                    jQuery('#countriesDialogSave', countriesRenewalsDialog.parent()).removeAttr('disabled');
                                    if (response.result) {
                                        if (jQuery('#notify-me-before-container', countriesRenewalsDialog).is(':visible')) {
                                            loadUserLatestReminders('refresh');
                                        }
                                        countriesRenewalsDialog.dialog("close");
                                        jQuery('#countriesRenewalsGrid').data("kendoGrid").dataSource.read();
                                        pinesMessage({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.renewal])});
                                    } else { // form has validation errors
                                        var errorMsg = '';
                                        for (i in response.validationErrors) {
                                            jQuery('#' + i, countriesRenewalsDialog).addClass('invalid').focus(function () {
                                                jQuery(this).removeClass('invalid');
                                            });
                                            errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                        }
                                        if (errorMsg != '') {
                                            pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                        }
                                    }


                                }, error: defaultAjaxJSONErrorsHandler,
                                type: 'post',
                                url: getBaseURL() + 'intellectual_properties/renewals/'
                            });
                        }
                    },
                    id: 'countriesDialogSave',
                    text: _lang.save
                },
                {
                    'class': 'btn btn-link',
                    click: function () {
                        countriesRenewalsDialog.dialog("close");
                        resetDialogForm(countriesAddForm);
                    },
                    text: _lang.cancel
                }
            ],
            close: function () {
                jQuery(window).unbind('resize');
                jQuery('#user_id1').val('');
                resetDialogForm(countriesAddForm);
            },
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '60%', '400');
                }));
                jQuery("form#countriesRenewalsAddForm").validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false, 'custom_error_messages': {}});
                resizeNewDialogWindow(that, '60%', '400');
                userLookup('renewalUserLookUp', 'renewal_user_id', '', 'active');
                jQuery('#renewal_user_id').val(jQuery('#userId').val());
                if (jQuery('#userId option:selected').val() != '') {
                    jQuery('#renewalUserLookUp').val(jQuery('#userId option:selected').text());
                }
                makeFieldsDatePicker({fields: ['renewalDate'], hiddenField: 'renewalDate_Hidden', container: 'countriesRenewalsAddForm'});
                makeFieldsDatePicker({fields: ['renewalExpiryDate'], hiddenField: 'renewalExpiryDate_Hidden', container: 'countriesRenewalsAddForm'});
                notifyMeBeforeEvent({'input': 'renewalExpiryDate'}, jQuery('#countriesRenewalsAddForm'));
                jQuery("#renewalDate").blur();
                usersLookup();
                jQuery('#notify-me-before-container').addClass('d-none');
                jQuery('#pendingReminders').parent().popover('hide');
            },
            draggable: true,
            modal: false,
            resizable: true,
            responsive: true,
            title: _lang.addRenewal
        });
    }
    jQuery('#ui-id-3').html(_lang.addRenewal);
    countriesRenewalsDialog.dialog("open");
}

function deleteCountryRenewal(id, actionType, gridId) {
    jQuery('#pendingReminders').parent().popover('hide');
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'intellectual_properties/renewals',
            dataType: "json",
            type: "POST",
            data: {
                recordId: id,
                action: actionType
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                if (response.result) {
                    ty = 'information';
                    m = _lang.deleteRecordSuccessfull;
                        loadUserLatestReminders('refresh');
                } else {
                    m = _lang.deleteRecordFailed;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#' + gridId).data('kendoGrid').dataSource.read();
            }, error: defaultAjaxJSONErrorsHandler
        });
    }
}

function getFormFilters(formId) {
    var filtersForm = jQuery('#' + formId);
    disableEmpty(filtersForm);
    var searchFilters = form2js(formId, '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}

function resetDialogForm(form) {
    jQuery(form)[0].reset();
    jQuery("#renewalDate").datepicker('destroy');
    jQuery("#selected-users").html('');
}

function deleteIpRecord(id) {
    var msg = _lang.confirmationOfCaseDelete;
    if (confirm(msg)) {
        window.location = getBaseURL() + 'intellectual_properties/delete_ip/' + id;
    }
}
//set data to agent field after adding company or contact
function setDataToCaseAgentField(record, container) {
    var agentName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#agentId', container).val(record.id);
    jQuery('#agentLookup', container).val(agentName);
}

function usersLookup() {
    jQuery('#lookup-users').autocomplete({
        autoFocus: false,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'users/autocomplete/active',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_for.sprintf([request.term]),
                                value: '',
                                record: {
                                    user_id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.firstName + ' ' + item.lastName,
                                value: '',
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        minLength: 2,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                setNewCaseMultiOption(jQuery('#selected-users'), {
                    id: ui.item.record.id,
                    value: ui.item.record.firstName + ' ' + ui.item.record.lastName,
                    name: 'users'
                });
            }
        }
    });
}
