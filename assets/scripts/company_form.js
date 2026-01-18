var newAuditCompanyFormDialog = null;
var newLawyerCompanyFormDialog = null;
var newDischargeSocialSecurityFormDialog = null;
var socialSecurityDischargeFormElement = null;
var auditCompanyFormElement = null;
var lawyerCompanyFormElement = null;
var gridSize10 = 10;
auditorsDataSource = new kendo.data.DataSource({
    transport: {
        read: {
            url: function () {
                return getBaseURL() + "companies/auditors/" + jQuery('#id', '#companyDetailedForm').val();
            },
            dataType: "JSON",
            data: {},
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText || "null");
                showHideGridContent(response, 'auditors_grid');
                if (response.totalRows < gridSize10)
                    jQuery('.k-pager-wrap', jQuery('#auditors_grid')).hide();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
            }
        }
    },
    schema: {
        type: "json",
        data: "data",
        total: "totalRows",
        model: {
            id: "auditorId",
            companyAuditorsId: "companyAuditorsId",
            fields: {
                auditCompany: {type: "string"},
                designationDate: {type: "string"},
                expiryDate: {type: "string"},
                comments: {type: "string"},
                fees: {type: "string"}
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
                        row['auditCompany'] = escapeHtml(row['auditCompany']);
                        row['comments'] = escapeHtml(row['comments']);
                        row['fees'] = escapeHtml(row['fees']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
    }, error: function (e) {
        defaultAjaxJSONErrorsHandler(e.xhr)
    },
    pageSize: gridSize10,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});
lawyersDataSource = new kendo.data.DataSource({
    transport: {
        read: {
            url: function () {
                return getBaseURL() + "companies/lawyers/" + jQuery('#id', '#companyDetailedForm').val();
            },
            dataType: "JSON",
            data: {},
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText || "null");
                showHideGridContent(response, 'company_lawyer_grid');
                if (response.totalRows < gridSize10)
                    jQuery('.k-pager-wrap', jQuery('#company_lawyer_grid')).hide();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
            }
        }
    },
    schema: {
        type: "json",
        data: "data",
        total: "totalRows",
        model: {
            companyLawyersId: "companyLawyersId",
            fields: {
                lawyerId: {type: "integer"},
                lawyerName: {type: "string"},
                comments: {type: "string"}
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
                    row['lawyerName'] = escapeHtml(row['lawyerName']);
                    row['auditCompany'] = escapeHtml(row['auditCompany']);
                    rows.data.push(row);
                }
            }
            return rows;
        }
    }, error: function (e) {
        defaultAjaxJSONErrorsHandler(e.xhr)
    },
    pageSize: gridSize10,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});
function auditorsGrid() {
    var grid = jQuery("#auditors_grid").kendoGrid({
        columns: [
            {field: "auditCompany", template: '<a href="#= (auditorType == \'Company\') ? getBaseURL() + \'companies/tab_company/\' + auditorId : getBaseURL() + \'contacts/edit/\' + auditorId #">#= auditCompany #</a>', title: _lang.auditor, width: "110px"},
            {field: "auditorType", title: _lang.type, width: "80px"},
            {field: "designationDate", title: _lang.designationDate, width: "133px", format: "{0:yyyy-MM-dd}", template: "#= (designationDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(designationDate) : designationDate, 'yyyy-MM-dd'))#"},
            {field: "expiryDate", title: _lang.expiryDate, width: "125px", format: "{0:yyyy-MM-dd}", template: "#= (expiryDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(expiryDate) : expiryDate, 'yyyy-MM-dd'))#"},
            {field: "fees", title: _lang.fees, width: "150px"},
            {field: "comments", title: _lang.comments, sortable: false, width: "220px"},
            {template: '<a href="javascript:" onclick="edit_audit_company(\'#= companyAuditorsId #\');" rel="tooltip" title="' + _lang.edit_audit_company + '"><i class="icon-alignment fa fa-pencil purple_color cursor-pointer-click"></i></a> &nbsp; <a href="javascript:" onclick="delete_audit_company(\'#= companyAuditorsId #\',event);" rel="tooltip" title="' + _lang.delete_auditor + '"><i class="icon-alignment fa fa-trash light_red-color"></i></a>', title: _lang.actions, width: "79px"}
        ],
        dataBound: function () {
            jQuery('.k-focusable tr').each(function () {
                jQuery('td:last-child', this).attr('align', 'center');
            });
        },
        dataSource: auditorsDataSource,
        editable: "popup",
        // filter menu settings
        /*filterable: {name: "FilterMenu", extra: true, // turns on/off the second filter option
         messages: _lang.kendo_grid_filterable_messages,
         operators: _lang.kendo_grid_filterable_operators
         },*/
        //navigatable: true,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: false,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{name: "add_auditor", template: '<input type="button" class="btn btn-default btn-info pull-left mb-10" value="' + _lang.add_auditor + '" name="add_auditor" id="" onclick="auditCompanyForm();" />'}]
    });
}
function lawyersGrid() {
    var grid = jQuery("#company_lawyer_grid").kendoGrid({
        columns: [
            {field: "lawyerName", template: '<a href="javascript:" onclick="edit_lawyer_company(\'#= companyLawyersId #\');" rel="tooltip">#= lawyerName #</a>', title: _lang.lawyer, width: "110px"},
            {field: "comments", title: _lang.comments, sortable: false, width: "220px"},
            {template: '<a href="javascript:" onclick="edit_lawyer_company(\'#= companyLawyersId #\');" rel="tooltip" title="' + _lang.edit_lawyer + '"><i class="icon-alignment fa fa-pencil purple_color cursor-pointer-click"></i></a>&nbsp;<a href="javascript:" onclick="delete_lawyer_company(\'#= companyLawyersId #\');" rel="tooltip" title="' + _lang.delete_lawyer + '"><i class="icon-alignment fa fa-trash light_red-color"></i></a>', title: _lang.actions, width: "79px"}
        ],
        dataBound: function () {
            jQuery('.k-focusable tr').each(function () {
                jQuery('td:last-child', this).attr('align', 'center');
            });
        },
        dataSource: lawyersDataSource,
        editable: "popup",
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: false,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{name: "add_lawyer", template: '<input type="button" class="btn btn-default btn-info pull-left mb-10" value="' + _lang.add_lawyer + '" name="add_lawyer" id="" onclick="lawyerCompanyForm();" />'}]
    });
}
function auditCompanyForm() {

    jQuery.ajax({
        url: getBaseURL() + 'companies/add_auditor/' + document.company_id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#add-audit-company-form').length <= 0) {
                    jQuery('<div id="add-audit-company-form"></div>').appendTo("body");
                    var newAuditCompanyFormDialog1 = jQuery('#add-audit-company-form');
                    newAuditCompanyFormDialog1.html(response.html);
                    jQuery("#form-submit", newAuditCompanyFormDialog1).click(function () {
                        auditCompanyFormElement = jQuery('form#audit_company_form', newAuditCompanyFormDialog1);
                            if (auditCompanyFormElement.validationEngine('validate')) {
                                if (jQuery('#auditor_id', auditCompanyFormElement).val() === '') {
                                    pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.auditCompany])});
                                } else {
                                    jQuery.ajax({
                                        url: getBaseURL() + 'companies/add_auditor',
                                        data: auditCompanyFormElement.serialize(),
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function () {
                                            jQuery('.loader-submit', newAuditCompanyFormDialog1).addClass('loading');
                                            jQuery('#form-submit', newAuditCompanyFormDialog1).attr('disabled', 'disabled');
                                        },
                                        success: function (response) {
                                            if (response.status == true) {
                                                if (jQuery('#notify-me-before-container', newAuditCompanyFormDialog1).is(':visible')) {
                                                    loadUserLatestReminders('refresh');
                                                }
                                                jQuery("#auditors_grid").data("kendoGrid").dataSource.read();
                                                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.addedNewAuditorSuccessfully});
                                            } else {
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newAuditCompanyFormDialog1).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }, complete: function () {
                                        jQuery('.modal', newAuditCompanyFormDialog1).modal('hide');
                                        jQuery('.loader-submit', newAuditCompanyFormDialog1).removeClass('loading');
                                        jQuery('#form-submit', newAuditCompanyFormDialog1).removeAttr('disabled');

                                    },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                }
                            }
                    });
                    jQuery('.modal', newAuditCompanyFormDialog1).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newAuditCompanyFormDialog1);
                    });
                    if (jQuery('.hijri-date-picker', '#expiry-date-container').length > 0) {
                        makeFieldsHijriDatePicker({fields: ['designationDate','expiryDate']});
                        jQuery('#expiryDate', '#expiry-date-container').val(gregorianToHijri(jQuery('#expiry-date-gregorian', '#expiry-date-container').val()));
                        jQuery('#designationDate', '#designation-date-container').val(gregorianToHijri(jQuery('#designation-date-gregorian', '#designation-date-container').val()));
                    } else {
                        makeFieldsDatePicker({fields: ['designationDate', 'expiryDate']});
                    }
                    notifyMeBeforeEvent({'input': 'expiryDate'}, jQuery('#audit_company_form'));
                    auditCompaniesAutoComplete(newAuditCompanyFormDialog1);
                    auditCompanyFormElement.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
            }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function lawyerCompanyForm() {
    jQuery.ajax({
        url: getBaseURL() + 'companies/add_lawyer/' + document.company_id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#add-lawyer-company-form').length <= 0) {
                    jQuery('<div id="add-lawyer-company-form"></div>').appendTo("body");
                    var newLawyerCompanyFormDialog = jQuery('#add-lawyer-company-form');
                    newLawyerCompanyFormDialog.html(response.html);
                    jQuery("#form-submit", newLawyerCompanyFormDialog).click(function () {
                        lawyerCompanyFormElement = jQuery('form#lawyer_company_form', newLawyerCompanyFormDialog);
                            if (lawyerCompanyFormElement.validationEngine('validate')) {
                                if (jQuery('#auditor_id', lawyerCompanyFormElement).val() === '') {
                                    pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.lawyer])});
                                } else {
                                    jQuery.ajax({
                                        url: getBaseURL() + 'companies/add_lawyer',
                                        data: lawyerCompanyFormElement.serialize(),
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function () {
                                            jQuery('.loader-submit', newLawyerCompanyFormDialog).addClass('loading');
                                            jQuery('#form-submit', newLawyerCompanyFormDialog).attr('disabled', 'disabled');
                                        },

                                        success: function (response) {
                                            if (response.status == true) {
                                                jQuery("#company_lawyer_grid").data("kendoGrid").dataSource.read();
                                            } else {
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newLawyerCompanyFormDialog).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }, complete: function () {
                                        jQuery('.modal', newLawyerCompanyFormDialog).modal('hide');
                                        jQuery('.loader-submit', newLawyerCompanyFormDialog).removeClass('loading');
                                        jQuery('#form-submit', newLawyerCompanyFormDialog).removeAttr('disabled');

                                    },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                }
                            }

                    });
                    jQuery('.modal', newLawyerCompanyFormDialog).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newLawyerCompanyFormDialog);
                    });
            contactsAutocomplete();
            newLawyerCompanyFormDialog.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
            }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
function edit_audit_company(id) {
    auditCompanyFormElement = jQuery('form#audit_company_form', newAuditCompanyFormDialog);
    jQuery.ajax({
        url: getBaseURL() + 'companies/edit_auditor/' + id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            if (response.html) {
                if (jQuery('#edit-audit-company-form').length <= 0) {
                    jQuery('<div id="edit-audit-company-form"></div>').appendTo("body");
                    var newAuditCompanyFormDialog1 = jQuery('#edit-audit-company-form');
                    newAuditCompanyFormDialog1.html(response.html);
                    jQuery("#form-submit", newAuditCompanyFormDialog1).click(function () {
                        auditCompanyFormElement = jQuery('form#audit_company_form', newAuditCompanyFormDialog1);
                            if (auditCompanyFormElement.validationEngine('validate')) {
                                if (jQuery('#auditor_id', auditCompanyFormElement).val() === '') {
                                    pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.auditCompany])});
                                } else {
                                    jQuery.ajax({
                                        url: getBaseURL() + 'companies/edit_auditor/' + id + '/',
                                        data: auditCompanyFormElement.serialize(),
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function () {
                                            jQuery('.loader-submit', newAuditCompanyFormDialog1).addClass('loading');
                                            jQuery('#form-submit', newAuditCompanyFormDialog1).attr('disabled', 'disabled');
                                        },
                                        success: function (response) {
                                            if (response.status == true) {
                                                if (jQuery('#notify-me-before-container', newAuditCompanyFormDialog1).is(':visible')) {
                                                    loadUserLatestReminders('refresh');
                                                }
                                                jQuery("#auditors_grid").data("kendoGrid").dataSource.read();
                                                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.addedNewAuditorSuccessfully});
                                            } else {
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newAuditCompanyFormDialog1).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }, complete: function () {
                                        jQuery('.modal', newAuditCompanyFormDialog1).modal('hide');
                                        jQuery('.loader-submit', newAuditCompanyFormDialog1).removeClass('loading');
                                        jQuery('#form-submit', newAuditCompanyFormDialog1).removeAttr('disabled');

                                    },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                }
                            }
                    });
                    jQuery('.modal', newAuditCompanyFormDialog1).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newAuditCompanyFormDialog1);
                    });
                    if (jQuery('.hijri-date-picker', '#expiry-date-container').length > 0) {
                        makeFieldsHijriDatePicker({fields: ['designationDate','expiryDate']});
                        jQuery('#expiryDate', '#expiry-date-container').val(gregorianToHijri(jQuery('#expiry-date-gregorian', '#expiry-date-container').val()));
                        jQuery('#designationDate', '#designation-date-container').val(gregorianToHijri(jQuery('#designation-date-gregorian', '#designation-date-container').val()));
                    } else {
                        makeFieldsDatePicker({fields: ['designationDate', 'expiryDate']});
                    }
                    notifyMeBeforeEvent({'input': 'expiryDate'}, jQuery('#audit_company_form'));
                    auditCompaniesAutoComplete(newAuditCompanyFormDialog1);
                    auditCompanyFormElement.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
            }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function edit_lawyer_company(id) {

    jQuery.ajax({
        url: getBaseURL() + 'companies/edit_lawyer/' + id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#edit-lawyer-company-form').length <= 0) {
                    jQuery('<div id="edit-lawyer-company-form"></div>').appendTo("body");
                    var newLawyerCompanyFormDialog = jQuery('#edit-lawyer-company-form');
                    newLawyerCompanyFormDialog.html(response.html);
                    jQuery("#form-submit", newLawyerCompanyFormDialog).click(function () {
                        lawyerCompanyFormElement = jQuery('form#lawyer_company_form', newLawyerCompanyFormDialog);
                            if (lawyerCompanyFormElement.validationEngine('validate')) {
                                if (jQuery('#auditor_id', lawyerCompanyFormElement).val() === '') {
                                    pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.lawyer])});
                                } else {
                                    jQuery.ajax({
                                        url: getBaseURL() + 'companies/edit_lawyer/' + id + '/',
                                        data: lawyerCompanyFormElement.serialize(),
                                        type: 'POST',
                                        dataType: 'JSON',
                                        beforeSend: function () {
                                            jQuery('.loader-submit', newLawyerCompanyFormDialog).addClass('loading');
                                            jQuery('#form-submit', newLawyerCompanyFormDialog).attr('disabled', 'disabled');
                                        },

                                        success: function (response) {
                                            if (response.status == true) {
                                                jQuery("#company_lawyer_grid").data("kendoGrid").dataSource.read();
                                            } else {
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newLawyerCompanyFormDialog).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }, complete: function () {
                                        jQuery('.modal', newLawyerCompanyFormDialog).modal('hide');
                                        jQuery('.loader-submit', newLawyerCompanyFormDialog).removeClass('loading');
                                        jQuery('#form-submit', newLawyerCompanyFormDialog).removeAttr('disabled');

                                    },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                }
                            }

                    });
                    jQuery('.modal', newLawyerCompanyFormDialog).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newLawyerCompanyFormDialog);
                    });
            contactsAutocomplete();
            newLawyerCompanyFormDialog.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
            }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
function resetAuditCompanyForm() {
    $auditCompanyFormId = jQuery('form#audit_company_form', newAuditCompanyFormDialog);
    $auditCompanyFormId[0].reset();
    $auditCompanyFormId.validationEngine('hide');
    jQuery('#auditor_id', $auditCompanyFormId).val('');
    jQuery('#auditCompanyLookUp', $auditCompanyFormId).val('');
    jQuery('#designationDate', $auditCompanyFormId).val('');
    jQuery('#expiryDate', $auditCompanyFormId).val('');
    jQuery('#auditComments', $auditCompanyFormId).val('');
    jQuery('#auditFees', $auditCompanyFormId).val('');
}
function resetLawyerCompanyForm() {
    $lawyerCompanyFormId = jQuery('form#lawyer_company_form', newLawyerCompanyFormDialog);
    $lawyerCompanyFormId[0].reset();
    $lawyerCompanyFormId.validationEngine('hide');
    jQuery('#lawyer_id', $lawyerCompanyFormId).val('');
    jQuery('#lookupLawyer', $lawyerCompanyFormId).val('');
    jQuery('#comments', $lawyerCompanyFormId).val('');
}
function delete_audit_company(id, event) {
    confirmationDialog('confim_delete_action', {resultHandler: _delete_audit_company, parm: id});
    function _delete_audit_company(id){
        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_auditor/' + id + '/',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.status == true) {
                    jQuery("#auditors_grid").data("kendoGrid").dataSource.read();
                } else {
                    pinesN(response.message, response.type)
                }
                },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    event.preventDefault();
}
function delete_lawyer_company(id) {
    confirmationDialog('confim_delete_action', {resultHandler: _delete_lawyer_company, parm: id});
    function _delete_lawyer_company(id) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_lawyer/' + id + '/',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                if (response.status == true) {
                    jQuery("#company_lawyer_grid").data("kendoGrid").dataSource.read();
                } else {
                    pinesN(response.message, response.type)
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function auditCompaniesAutoComplete(container) {
    auditCompanyFormElement = jQuery('form#audit_company_form', container);
    jQuery('#auditCompanyLookUp', auditCompanyFormElement).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#auditorType", auditCompanyFormElement).val();
            request.more_filters = {};
            if (lookupType == 'companies') {
                request.more_filters.category = ['Internal'];//tofilter category of companies
                request.more_filters.requestFlag = 'categoryFlagOnly';//to specify this page form
            }
            jQuery.ajax({
                url: getBaseURL() + lookupType + '/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_add.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
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
            var lookupType = jQuery('select#auditorType', auditCompanyFormElement).val();
            if (ui.item.record.id > 0) {
                jQuery('#auditor_id').val(ui.item.record.id);
                auditCompanyFormElement.validationEngine('hide');
            } else if (ui.item.record.id == -1) {
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": auditCompanyFormElement,
                        "lookupResultHandler": setContactDataAfterAutocompleteOfAuditorForm,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": auditCompanyFormElement,
                        "lookupResultHandler": setCompanyDataAfterAutocompleteOfAuditorForm,
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
function setContactDataAfterAutocompleteOfAuditorForm(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#auditor_id', container).val(record.id);
    jQuery('#auditCompanyLookUp', container).val(name);
}
function setCompanyDataAfterAutocompleteOfAuditorForm(record, container) {
    var name = record.name;
    jQuery('#auditor_id', container).val(record.id);
    jQuery('#auditCompanyLookUp', container).val(name);
}
function onChangeAuditorType() {
    jQuery('#auditor_id', '#audit_company_form').val('');
    jQuery('#auditCompanyLookUp', '#audit_company_form').val('');
}
function setCompanyAuditorId(record) {
    jQuery('#auditor_id', auditCompanyFormElement).val(record.id);
    jQuery('#auditCompanyLookUp', auditCompanyFormElement).val(record.name);
}
function setCompanyDialogueLawyerToFormAfterAutocomplete(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#lawyer_id', container).val(record.id);
    jQuery('#lookupLawyer', container).val(name);
}
function contactsAutocomplete() {
    contactAutocompleteMultiOption('lookupNotaryPublic', 2, setNotaryPublicToFormAfterAutocomplete); // to notary public field
    jQuery("#lookupLawyer", '#lawyer_company_form').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'contacts/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_contacts_matched_for_lawyer.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                record: item
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
            if (ui.item.record.id && ui.item.record.id > 0) {
                setCompanyDialogueLawyerToFormAfterAutocomplete(ui.item.record);
            } else if (ui.item.record.id == -1) {
                companyContactFormMatrix.contactDialog = {
                    "referalContainerId": jQuery('#lawyer_company_form'),
                    "lookupResultHandler": setCompanyDialogueLawyerToFormAfterAutocomplete,
                    "lookupValue": ui.item.record.term
                }
                contactAddForm();
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
function validateCompanyDetailedForm() {
    jQuery("#companyDetailedForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        'custom_error_messages': {
            '#name': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.companyName])
                }
            },
            '#shortName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.companyShortName])
                }
            }
        }
    });
}
function companyNiceDropDownLists() {
    jQuery("#nationality_id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseNationality,
        width: '100%'
    });
    jQuery("#company_id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCompany,
        width: '100%'
    });
    jQuery("#company_legal_type_id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseLegalType,
        width: '100%'
    });
    jQuery(".country-id", jQuery('#address-details-container', '#companyDetailedForm')).chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCountry,
        width: '100%'
    });
    jQuery("#capitalCurrency, #shareParValueCurrency").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.currency,
        width: '100%'
    });
    jQuery("#company-category-id").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCategory,
        width: '100%'
    });
}
function quickSearchCompanies() {
    jQuery("#lookupCompanies").autocomplete({
        autoFocus: true,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    response(jQuery.map(data, function (item) {
                        return {
                            label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                            value: item.name,
                            record: item
                        }
                    }));
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id != jQuery("#id", '#companyDetailedForm').val())
                window.location = getBaseURL() + 'companies/tab_company/' + ui.item.record.id;
        }
    });
}
function saveCompanyForm() {
    jQuery('#companyDetailedForm').submit();
}
function onChangeCategoryList(index) {
    if (1 == index) {
        jQuery('label.control-label', '#shortNameBox').addClass('required');
        jQuery('#shortName', '#companyDetailedForm').removeAttr('disabled');
        jQuery('#detailsContainer').slideDown();
    } else {
        jQuery('label.control-label', '#shortNameBox').removeClass('required');
        jQuery('#shortName', '#companyDetailedForm').attr('disabled', 'disabled');
        jQuery('#detailsContainer').slideUp();
    }
}
function setNotaryPublicToFormAfterAutocomplete(record) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#registrationByLawNotaryPublic', '#companyDetailedForm').val(record.id);
    jQuery('#lookupNotaryPublic', '#companyDetailedForm').val(name);
}
function initDateFields() {
    makeFieldsHijriDatePicker({fields: ['registration-by-law-date-date-hijri','cr-released-on-container-date-hijri','registration-date-hijri']});
    jQuery('#registration-by-law-date-date-hijri', '#registration-by-law-date-container').val(gregorianToHijri(jQuery('#registration-by-law-date-gregorian', '#registration-by-law-date-container').val()));
    jQuery('#cr-released-on-container-date-hijri', '#cr-released-on-container').val(gregorianToHijri(jQuery('#cr-released-on-container-date-gregorian', '#cr-released-on-container').val()));
    jQuery('#registration-date-hijri', '#registration-date-container').val(gregorianToHijri(jQuery('#registration-date-gregorian', '#registration-date-container').val()));
}

function socialSecurityDischargeInitDates() {
    if (jQuery('.visualize-hijri-date', '#released-on-hijri-container').length > 0) {
        makeFieldsHijriDatePicker({fields: ['expiresOn']});
        makeFieldsHijriDatePicker({fields: ['releasedOn']});
        jQuery('#expiresOn', '#expires-on-hijri-container').val(gregorianToHijri(jQuery('#expires-on-gregorian', '#expires-on-hijri-container').val()));
        jQuery('#releasedOn', '#released-on-hijri-container').val(gregorianToHijri(jQuery('#released-on-gregorian', '#released-on-hijri-container').val()));
    } else {
        makeFieldsDatePicker({fields: ['releasedOn'], hiddenField: 'releasedOn_Hidden', container: 'social_security_form'});
        makeFieldsDatePicker({fields: ['expiresOn'], hiddenField: 'expiresOn_Hidden', container: 'social_security_form'});
    }
    jQuery('#releasedOn').datepicker('option', 'maxDate', jQuery('#expiresOn_Hidden').val());
    jQuery('#releasedOn').datepicker("refresh");
    jQuery('#expiresOn').datepicker('option', 'minDate', jQuery('#releasedOn_Hidden').val());
    jQuery('#expiresOn').datepicker("refresh");
    jQuery('#releasedOn_Hidden').change(function () {
        var minDate = jQuery('#releasedOn_Hidden').val();
        if (undefined === minDate || minDate === '') {
            jQuery('#expiresOn').val('');
        } else {
            jQuery('#expiresOn').datepicker('destroy');
            if (jQuery('.visualize-hijri-date', '#released-on-hijri-container').length > 0) {
                makeFieldsHijriDatePicker({fields: ['expiresOn']});
                jQuery('#expiresOn', '#expires-on-hijri-container').val(gregorianToHijri(jQuery('#expires-on-gregorian', '#expires-on-hijri-container').val()));
            } else {
                makeFieldsDatePicker({fields: ['expiresOn'], hiddenField: 'expiresOn_Hidden', container: 'social_security_form'});
            }
            jQuery('#expiresOn').datepicker('option', 'minDate', minDate);
            jQuery('#expiresOn').datepicker("refresh");
        }
    });
    jQuery('#expiresOn_Hidden').change(function () {
        var maxDate = jQuery('#expiresOn_Hidden').val();
        if (undefined === maxDate || maxDate === '') {
            jQuery('#releasedOn').val('');
        } else {
            jQuery('#releasedOn').datepicker('destroy');
            if (jQuery('.visualize-hijri-date', '#released-on-hijri-container').length > 0) {
                makeFieldsHijriDatePicker({fields: ['releasedOn']});
                jQuery('#releasedOn', '#released-on-hijri-container').val(gregorianToHijri(jQuery('#released-on-gregorian', '#released-on-hijri-container').val()));
            } else {
                makeFieldsDatePicker({fields: ['releasedOn'], hiddenField: 'releasedOn_Hidden', container: 'social_security_form'});
            }
            jQuery('#releasedOn').datepicker('option', 'maxDate', maxDate);
            jQuery('#releasedOn').datepicker("refresh");
        }
    });
    jQuery('#expiresOn').click(function () {
        var minDate = jQuery('#releasedOn_Hidden').val();
        jQuery('#expiresOn').datepicker('destroy');
        if (jQuery('.visualize-hijri-date', '#released-on-hijri-container').length > 0) {
            makeFieldsHijriDatePicker({fields: ['expiresOn']});
            jQuery('#expiresOn', '#expires-on-hijri-container').val(gregorianToHijri(jQuery('#expires-on-gregorian', '#expires-on-hijri-container').val()));
        } else {
            makeFieldsDatePicker({fields: ['expiresOn'], hiddenField: 'expiresOn_Hidden', container: 'social_security_form'});
        }
        if (undefined === minDate || minDate === '') {
            jQuery('#expiresOn').datepicker('option', 'minDate', null);
        } else {
            jQuery('#expiresOn').datepicker('option', 'minDate', minDate);
        }
        jQuery('#expiresOn').datepicker("refresh");
    });
    jQuery('#releasedOn').click(function () {
        var maxDate = jQuery('#expiresOn_Hidden').val();
        jQuery('#releasedOn').datepicker('destroy');
        if (jQuery('.visualize-hijri-date', '#released-on-hijri-container').length > 0) {
            makeFieldsHijriDatePicker({fields: ['releasedOn']});
            jQuery('#releasedOn', '#released-on-hijri-container').val(gregorianToHijri(jQuery('#released-on-gregorian', '#released-on-hijri-container').val()));
        } else {
            makeFieldsDatePicker({fields: ['releasedOn'], hiddenField: 'releasedOn_Hidden', container: 'social_security_form'});
        }
        if (undefined === maxDate || maxDate === '') {
            jQuery('#releasedOn').datepicker('option', 'maxDate', null);
        } else {
            jQuery('#releasedOn').datepicker('option', 'maxDate', maxDate);
        }
        jQuery('#releasedOn').datepicker("refresh");
    });
    notifyMeBeforeEvent({'input': 'expiresOn'}, jQuery('#social_security_form'));
}

socialSecurityDataSource = new kendo.data.DataSource({
    transport: {
        read: {
            url: function () {
                return getBaseURL() + "companies/social_security/" + jQuery('#id', '#companyDetailedForm').val();
            },
            dataType: "JSON",
            data: {},
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText);
                showHideGridContent(response, 'social_security_discharge_grid');
                if (response.totalRows < gridSize10)
                    jQuery('.k-pager-wrap', jQuery('#social_security_discharge_grid')).hide();
                if (_lang.languageSettings['langDirection'] === 'rtl')
                    gridScrollRTL();
            }
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
                typeOfDischarge: {type: "string"},
                releasedOn: {type: "string"},
                expiresOn: {type: "string"},
                remindNames: {type: "string"},
                remindGroups: {type: "string"},
            }
        },
        parse: function(response) {
            var rows = [];
            if(response.data){
                var data = response.data;
                rows = response;
                rows.data = data;
            }
            return rows;
        }
    }, error: function (e) {
        defaultAjaxJSONErrorsHandler(e.xhr)
    },
    pageSize: gridSize10,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});

function socialSecurityGrid() {
    var grid = jQuery("#social_security_discharge_grid").kendoGrid({
        columns: [
            {field: "typeOfDischarge", title: _lang.typeOfDischarge, width: "80px"},
            {field: "releasedOn", title: _lang.releasedOn, width: "80px", format: "{0:yyyy-MM-dd}", template: "#= (releasedOn == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(releasedOn) : releasedOn, 'yyyy-MM-dd'))#"},
            {field: "expiresOn", title: _lang.expiresOn, width: "80px", format: "{0:yyyy-MM-dd}", template: "#= (expiresOn == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(expiresOn) : expiresOn, 'yyyy-MM-dd'))#"},
            {field: "remindNames", title: _lang.remind, width: "150px", template: '#= (remindNames!=null)?remindNames:"" #'},
            {field: "remindGroups", title: _lang.userGroups, width: "80px", template: '#= (remindGroups!=null)?remindGroups:"" #'},
            {field: "reference", title: _lang.internalReferenceNumber, width: "100px"},
            {template: '<a href="javascript:" onclick="editSocialSecurityDischarge(\'#= id #\');" rel="tooltip" title="' + _lang.editSocialSecurityDischarge + '"><i class="icon-alignment fa fa-pencil purple_color cursor-pointer-click"></i></a> &nbsp; <a href="javascript:" onclick="deleteSocialSecurityDischarge(\'#= id #\');" rel="tooltip" title="' + _lang.deleteDischarge + '"><i class="icon-alignment fa fa-trash light_red-color"></i></a>', title: _lang.actions, width: "79px"}
        ],
        dataBound: function () {
            jQuery('.k-focusable tr').each(function () {
                jQuery('td:last-child', this).attr('align', 'center');
            });
        },
        dataSource: socialSecurityDataSource,
        editable: "popup",
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{name: "add_discharge", template: '<input type="button" class="btn btn-default btn-info pull-left mb-10" value="' + _lang.addDischarge + '" name="add_discharge" id="" onclick="socialSecurityDischargeCompanyForm();" />'}]
    });
}
function socialSecurityDischargeCompanyForm() {
    jQuery.ajax({
        url: getBaseURL() + 'companies/add_social_security/' + document.company_id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#add-social-security-form').length <= 0) {
                    jQuery('<div id="add-social-security-form"></div>').appendTo("body");
                    var newDischargeSocialSecurityFormDialog = jQuery('#add-social-security-form');
                    newDischargeSocialSecurityFormDialog.html(response.html);
                    jQuery("#form-submit", newDischargeSocialSecurityFormDialog).click(function () {
                        socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                        if (socialSecurityDischargeFormElement.validationEngine('validate')) {
                            if (jQuery('#typeOfDischarge', socialSecurityDischargeFormElement).val() === '') {
                                pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.typeOfDischarge])});
                            } else {
                                jQuery('.ui-button.ui-widget', jQuery("[aria-describedby='add_new_social_security_discharge']")).addClass('disabled');
                                jQuery.ajax({
                                    url: getBaseURL() + 'companies/add_social_security',
                                    data: socialSecurityDischargeFormElement.serialize(),
                                    type: 'POST',
                                    dataType: 'JSON',
                                    beforeSend: function () {
                                        jQuery('.loader-submit', newDischargeSocialSecurityFormDialog).addClass('loading');
                                        jQuery('#form-submit', newDischargeSocialSecurityFormDialog).attr('disabled', 'disabled');
                                    },
                                    success: function (response) {
                                        if (response.status == true) {
                                            if (jQuery('#notify-me-before-container', newDischargeSocialSecurityFormDialog).is(':visible')) {
                                                loadUserLatestReminders('refresh');
                                            }
                                            jQuery("#social_security_discharge_grid").data("kendoGrid").dataSource.read();
                                                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                        } else {
                                            if (response.reminderErrorMsg) {
                                                pinesMessageV2({ty: 'warning', m: response.reminderErrorMsg});
                                                jQuery("#social_security_discharge_grid").data("kendoGrid").dataSource.read();
                                            } else {
                                                // jQuery('.ui-button.ui-widget', jQuery("[aria-describedby='add_new_social_security_discharge']")).removeClass('disabled');
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newDischargeSocialSecurityFormDialog).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }
                                    }, complete: function () {
                                        jQuery('.modal', newDischargeSocialSecurityFormDialog).modal('hide');
                                        jQuery('.loader-submit', newDischargeSocialSecurityFormDialog).removeClass('loading');
                                        jQuery('#form-submit', newDischargeSocialSecurityFormDialog).removeAttr('disabled');

                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        } else {
                        }
                    });
                    jQuery('.modal', newDischargeSocialSecurityFormDialog).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newDischargeSocialSecurityFormDialog);
                    });
                    socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                    socialSecurityDischargeLookup('typeOfDischarge');
                    socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                    socialSecurityDischargeInitDates();
                    userLookup('remind', 'remindId', '', 'active');
                    socialSecurityDischargeFormElement.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler

    });
}

function editSocialSecurityDischarge(id) {
    jQuery.ajax({
        url: getBaseURL() + 'companies/edit_social_security/' + id + '/',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#edit-social-security-form').length <= 0) {
                    jQuery('<div id="edit-social-security-form"></div>').appendTo("body");
                    var newDischargeSocialSecurityFormDialog = jQuery('#edit-social-security-form');
                    newDischargeSocialSecurityFormDialog.html(response.html);
                    jQuery("#form-submit", newDischargeSocialSecurityFormDialog).click(function () {
                        socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                        if (socialSecurityDischargeFormElement.validationEngine('validate')) {
                            if (jQuery('#typeOfDischarge', socialSecurityDischargeFormElement).val() === '') {
                                pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.typeOfDischarge])});
                            } else {
                                jQuery('.ui-button.ui-widget', jQuery("[aria-describedby='add_new_social_security_discharge']")).addClass('disabled');
                                jQuery.ajax({
                                    url: getBaseURL() + 'companies/edit_social_security/' + id + '/',
                                    data: socialSecurityDischargeFormElement.serialize(),
                                    type: 'POST',
                                    dataType: 'JSON',
                                    beforeSend: function () {
                                        jQuery('.loader-submit', newDischargeSocialSecurityFormDialog).addClass('loading');
                                        jQuery('#form-submit', newDischargeSocialSecurityFormDialog).attr('disabled', 'disabled');
                                    },
                                    success: function (response) {
                                        if (response.status == true) {
                                            if (jQuery('#notify-me-before-container', newDischargeSocialSecurityFormDialog).is(':visible')) {
                                                loadUserLatestReminders('refresh');
                                            }
                                            jQuery("#social_security_discharge_grid").data("kendoGrid").dataSource.read();
                                                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                        } else {
                                            if (response.reminderErrorMsg) {
                                                pinesMessageV2({ty: 'warning', m: response.reminderErrorMsg});
                                                jQuery("#social_security_discharge_grid").data("kendoGrid").dataSource.read();
                                            } else {
                                                // jQuery('.ui-button.ui-widget', jQuery("[aria-describedby='add_new_social_security_discharge']")).removeClass('disabled');
                                                var errorMsg = '';
                                                for (i in response.validationErrors) {
                                                    jQuery('#' + i, newDischargeSocialSecurityFormDialog).addClass('invalid').focus(function () {
                                                        jQuery(this).removeClass('invalid');
                                                    });
                                                    errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                                }
                                                if (errorMsg != '') {
                                                    pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                                }
                                            }
                                        }
                                    }, complete: function () {
                                        jQuery('.modal', newDischargeSocialSecurityFormDialog).modal('hide');
                                        jQuery('.loader-submit', newDischargeSocialSecurityFormDialog).removeClass('loading');
                                        jQuery('#form-submit', newDischargeSocialSecurityFormDialog).removeAttr('disabled');

                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        } else {
                        }
                    });
                    jQuery('.modal', newDischargeSocialSecurityFormDialog).modal({
                        keyboard: false,
                        show: true,
                        backdrop: 'static'
                    });
                    jQuery('.modal').on('hidden.bs.modal', function () {
                        destroyModal(newDischargeSocialSecurityFormDialog);
                    });
                    socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                    socialSecurityDischargeLookup('typeOfDischarge');
                    socialSecurityDischargeFormElement = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
                    socialSecurityDischargeInitDates();
                    userLookup('remind', 'remindId', '', 'active');
                    socialSecurityDischargeFormElement.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false});
                }
            }
            }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteSocialSecurityDischarge(id) {
    confirmationDialog('confim_delete_action', {resultHandler: _delete_social_security, parm: id});
    function _delete_social_security(id) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_social_security/' + id + '/',
            data: {},
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                var ty = 'error';
                var m = '';
                if (response.status == true) {
                    jQuery("#social_security_discharge_grid").data("kendoGrid").dataSource.read();
                    ty = 'information';
                    m = _lang.deleteRecordSuccessfull;
                } else {
                    m = _lang.recordNotDeleted;
                }
                pinesMessageV2({ty: ty, m: m});
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}

function resetSocialSecurityDischargeForm() {
    $socialSecurityDischargeFormId = jQuery('form#social_security_form', newDischargeSocialSecurityFormDialog);
    $socialSecurityDischargeFormId.validationEngine('hide');
    jQuery('#typeOfDischarge', $socialSecurityDischargeFormId).val('');
    jQuery('#releasedOn', $socialSecurityDischargeFormId).val('');
    jQuery('#expiresOn', $socialSecurityDischargeFormId).val('');
    jQuery('#remind', $socialSecurityDischargeFormId).val('');
    jQuery('#remindId', $socialSecurityDischargeFormId).val('');
    jQuery('#type_of_discharge_id', $socialSecurityDischargeFormId).val('');
}

jQuery(document).on("keypress", "form#dischargeTypesForm", function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        jQuery(".btn-discharge-types-save").trigger("click");
        return false;
    }
});
jQuery(document).on("keypress", "form#lawyer_company_form", function (e) {
    var code = e.keyCode || e.which;
    if (code == 13) {
        e.preventDefault();
        jQuery(".btn-company-lawyer-save").trigger("click");
        return false;
    }
});

function addNewSocialSecurityDischargeType() {
     jQuery.ajax({
        url: getBaseURL() + 'company_ss_discharge_types/add',
        data: {},
        type: 'POST',
        dataType: 'JSON',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery('#add-social-security-discharge').remove()
            if (jQuery('#add-social-security-discharge').length <= 0) {
                jQuery('<div id="add-social-security-discharge"></div>').appendTo("body");
                var socialSecurityDischargeDialog = jQuery('#add-social-security-discharge');
                socialSecurityDischargeDialog.html(response.html);
                jQuery("#form-submit-2", socialSecurityDischargeDialog).click(function () {
                    var dataIsValid = jQuery("form#dischargeTypesForm").validationEngine('validate');
                    if (dataIsValid) {
                        jQuery.ajax({
                            url: getBaseURL() + 'company_ss_discharge_types/add',
                            beforeSend: function () {
                                jQuery('.loader-submit-2', socialSecurityDischargeDialog).addClass('loading');
                                jQuery('#form-submit-2', socialSecurityDischargeDialog).attr('disabled', 'disabled');

                            },
                            data: {name: jQuery('#dischargeName', socialSecurityDischargeDialog).val()},
                            dataType: 'JSON',
                            type: 'POST',
                            success: function (response) {
                                if (response.result) {
                                    jQuery('#type_of_discharge_id', '#social_security_form').append(jQuery('<option/>', {value: response.data.id, text: response.data.name})).val(response.data.id).trigger("chosen:updated");
                                    pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                } else {
                                    var errorMsg = '';
                                    for (i in response.validationErrors) {
                                        errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                    }
                                    if (errorMsg != '') {
                                        pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                    }
                                }
                            }, complete: function () {
                                jQuery('.modal', socialSecurityDischargeDialog).modal('hide');
                                jQuery('.loader-submit', socialSecurityDischargeDialog).removeClass('loading');
                                jQuery('#form-submit', socialSecurityDischargeDialog).removeAttr('disabled');

                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    }
                });
                jQuery('.modal', socialSecurityDischargeDialog).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
    });



jQuery('.modal', jQuery("#socialSecurityDischargeContainer")).css('zIndex','9999');
    jQuery("form#dischargeTypesForm").validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false, 'custom_error_messages': {'#dischargeName': {'required': {'message': _lang.validation_field_required.sprintf([_lang.name])}}}});
    jQuery('#socialSecurityDischargeContainer').dialog({
        autoOpen: true,
        buttons: [
            {
                'class': 'btn btn-info btn-discharge-types-save',
                click: function () {
                    var container = jQuery(this);
                    var dataIsValid = jQuery("form#dischargeTypesForm").validationEngine('validate');
                    if (dataIsValid) {
                        jQuery.ajax({
                            url: getBaseURL() + 'company_ss_discharge_types/add',
                            beforeSend: function () {
                            },
                            data: {name: jQuery('#dischargeName', container).val()},
                            dataType: 'JSON',
                            type: 'POST',
                            success: function (response) {
                                if (response.result) {
                                    container.dialog("close");
                                    jQuery('#type_of_discharge_id', '#social_security_form').append(jQuery('<option/>', {value: response.data.id, text: response.data.name})).val(response.data.id).trigger("chosen:updated");
                                    pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                } else {
                                    var errorMsg = '';
                                    for (i in response.validationErrors) {
                                        errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                    }
                                    if (errorMsg != '') {
                                        pinesMessageV2({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                    }
                                }
                            }, error: defaultAjaxJSONErrorsHandler
                        });
                    }
                },
                text: _lang.save
            },
            {
                'class': 'btn btn-link',
                click: function () {
                    jQuery('#dischargeName', '#socialSecurityDischargeContainer').val('');
                    jQuery('#dischargeTypesForm').validationEngine("hide");
                    jQuery(this).dialog("close");
                },
                text: _lang.cancel
            }
        ],
        close: function () {
            jQuery('#dischargeName', jQuery(this)).val('');
            jQuery('#dischargeTypesForm').validationEngine("hide");
        },
        open: function () {
            var that = jQuery(this);
            that.removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '40%', '200');
            }));
            resizeNewDialogWindow(that, '40%', '200');
        },
        draggable: false,
        modal: false,
        resizable: false,
        title: _lang.addNewDischargeType
    });
}

function socialSecurityDischargeLookup(lookupField) {
    $lookupField = jQuery('#' + lookupField);
    $lookupField.autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'companies/discharge_type_autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched.sprintf([request.term]),
                                value: '',
                                record: {id: -1, term: request.term}
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.dischargeType, value: item.dischargeType, record: item}
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 2,
        select: function (event, ui) {
            for (i in ui.item.record)
                if (ui.item.record.id > 0) {
                    jQuery('#type_of_discharge_id').val(ui.item.record.id);
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
jQuery(document).ready(function () {
    validateCompanyDetailedForm();
    companyNiceDropDownLists();
    quickSearchCompanies();
    initDateFields();
    contactsAutocomplete();
    ctrlS(saveCompanyForm);
    socialSecurityGrid();
    auditorsGrid();
    lawyersGrid();
    companyUsersLookup();
    companyRegistrationAuthorityLookup();
    jQuery('div[id^=address-details-container-]', '#companyDetailedForm').each(function () {
        copyAddressFromLookup(jQuery(this));
    });
    var firstError = jQuery('.help-block.error:first', "#companyDetailedForm");
    if (firstError.length && undefined != firstError.attr('for')) {
        scrollToId(firstError.attr('for'));
    }

    jQuery('#category').change(function () {
        onChangeCategoryList(this.selectedIndex);
    });
    newDischargeSocialSecurityFormDialog = jQuery('#add_new_social_security_discharge');
    quickAddDischargeSocialSecurityFormDialog = jQuery('#quick_add_social_security_discharge');
    newAuditCompanyFormDialog = jQuery('#add_new_audit_company');
    newLawyerCompanyFormDialog = jQuery('#add_new_lawyer_company');
    jQuery(window).bind('beforeunload', function (event) {
        if (jQuery('.changed', "form#companyDetailedForm").length > 0) {
            return _lang.warning_you_have_unsaved_changes;
        }
    });
    jQuery("form#companyDetailedForm").dirty_form({
        includeHidden: true
    }).submit(function (e) {
        jQuery(window).unbind('beforeunload');
    });
    if (document.location.href.substr(document.location.href.length - 5, 5) == 'print') {
        window.open(jQuery('#printAnchor').attr('href'));
        document.location = document.location.href.substr(0, document.location.href.length - 6);
    }
});
function isFormChanged() {
    if (jQuery('.changed', "form#companyDetailedForm").length > 0 && confirm(_lang.warning_you_have_unsaved_changes_save_now)) {
        jQuery('form#companyDetailedForm').attr('action', jQuery('form#companyDetailedForm').attr('action') + '/print').submit();
        return false;
    }
    return true;
}
function companyRegistrationAuthorityLookup() {
    jQuery('#lookupRegistrationAuthority').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.more_filters = {};
            request.more_filters.category = ['Internal'];
            request.more_filters.requestFlag = 'categoryFlagOnly';
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_add.sprintf([request.term]),
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
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#registrationAuthority').val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                companyContactFormMatrix.companyDialog = {
                    "lookupResultHandler": setcompanyRegistrationAuthorityToForm,
                    "lookupValue": ui.item.record.term
                };
                companyAddForm();
            }
        }
    });
}
function setcompanyRegistrationAuthorityToForm(record) {
    jQuery('#lookupRegistrationAuthority').val(record.name);
    jQuery('#registrationAuthority').val(record.id);
}
function companyUsersLookup() {
    jQuery('#lookupCompanyUsers', '#companyDialog').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({url: getBaseURL() + 'users/autocomplete/active', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {user_id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.firstName + ' ' + item.lastName, value: '', record: item}
                        }));
                    }
                }});
        }, minLength: 3, select: function (event, ui) {
            if (ui.item.record.id > 0) {
                setNewCaseMultiOption(jQuery('#selected_company_users', '#companyDialog'), {id: ui.item.record.id, value: ui.item.record.firstName + ' ' + ui.item.record.lastName, name: 'Company_Users'});
            }
        }});
}
function toggleNotes(companyId, companyNoteId) {
    // collapse('company-notes-toggle', 'notes');
    // if(jQuery('#company-notes-toggle i').hasClass('glyphicon-chevron-down')) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/get_company_notes/',
            data: {
                'company_id': companyId
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    var notesList = jQuery('#notes-list');
                    jQuery('#company-current-notes').removeClass('d-none');
                    notesList.html(response.html);
                    if (response.notes_count === 0) {
                        jQuery('#company-notes-pagination-container').html('');
                    }
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    // }

}
function uploadDocumentDone(message, type, companyNoteId, companyEdit) {
    if (undefined == companyNoteId)
        companyNoteId = 0;
    if (undefined == companyEdit)
        companyEdit = false;
    var msg = '';
    for (i in message) {
        msg = message[i];
        msg = msg.replace(/\\'+/g, "'");
        pinesMessageV2({ty: type, m: msg});
    }
    if (!companyEdit) {
        if (companyNoteId > 0)
            toggleNotes(jQuery('#id').val(), companyNoteId);
    } else {
        if (companyNoteId > 0) {
            toggleNotes(jQuery('#id').val(), companyNoteId);
        }
    }
}
function toggleNote(id) {
   var company_note = jQuery('#company-note-' + id);
    if (jQuery('#note-text', company_note).is(':visible')) {
        jQuery('#note-text', company_note).slideUp();
        jQuery('i', '#company-note-' + id + ' a:first').removeClass('fa-angle-double-down');
        jQuery('i', '#company-note-' + id + ' a:first').addClass('fa-angle-double-right');
    } else {
        jQuery('#note-text', company_note).slideDown();
        jQuery('i', '#company-note-' + id + ' a:first').removeClass('fa-angle-double-right');
        jQuery('i', '#company-note-' + id + ' a:first').addClass('fa-angle-double-down');
    }
}
function expandAllNotes() {
    var notesList = jQuery('#notes-list');
    //hide up all notes
    jQuery('span#note-text', jQuery('div.notes-list', notesList)).slideDown();
    jQuery('a > i.tg', notesList).removeClass('fa-angle-double-right');
    jQuery('a > i.tg', notesList).addClass('fa-angle-double-down');
}
function collapseAllNotes() {
    var notesList = jQuery('#notes-list');
    //hide up all notes
    jQuery('span#note-text', jQuery('div.notes-list', notesList)).slideUp();
    jQuery('a > i.tg', notesList).removeClass('fa-angle-double-down');
    jQuery('a > i.tg', notesList).addClass('fa-angle-double-right');
}
// this function used for showing notes popup of company
function showCompanyNotePopup(companyID, noteID) {
    var data = {
        "company_id":companyID,
        "note_id":noteID,
        "load-html":1
    };
    var url = getBaseURL() + 'companies/add_note';
    jQuery.ajax({
        dataType: 'JSON',
        type:'post',
        url: url,
        data:data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.popuphtml) {
                if (jQuery('#company-add-note-dialog').length <= 0) {
                    jQuery('<div id="company-add-note-dialog"></div>').appendTo("body");
                    var companyAddNoteDialog = jQuery('#company-add-note-dialog');
                    companyAddNoteDialog.html(response.popuphtml);
                    companyAddNoteFormEvents(companyAddNoteDialog);
                    tinymce.remove();
                    tinymce.init({
                        selector: '#note',
                        menubar: false,
                        statusbar: false,
                        branding: false,
                        height: 200,
                        resize: false,
                        plugins: ['link'],
                        toolbar: 'formatselect | bold italic underline | link | undo redo ',
                        block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                        formats: {
                          underline: { inline: 'u', exact : true }
                        },
                        setup: function (editor) {
                            editor.on('init', function (e) {
                                setTimeout(function () {
                                    jQuery('#note_ifr').contents().find('body').prop("dir", "auto");
                                    jQuery('#note_ifr').contents().find('body').focus();
                                }, 100);
                            });
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
// this function used for initializing notes popup components
function companyAddNoteFormEvents(companyAddNoteDialog) {
    initializeModalSize(companyAddNoteDialog);
    if (jQuery('.hijri-date-picker', '#created-on-hijri-container').length > 0) {
        makeFieldsHijriDatePicker({fields: ['created-on']});
        jQuery('#created-on', '#created-on-hijri-container').val(gregorianToHijri(jQuery('#created-on-gregorian', '#created-on-hijri-container').val()));
    } else {
        setDatePicker('#date-picker', companyAddNoteDialog);
    }
    lookUpUsers(jQuery('#create-note-look-up', companyAddNoteDialog), jQuery('#created-by', companyAddNoteDialog), 'error_note_div', jQuery('.added-by-container', companyAddNoteDialog), companyAddNoteDialog);
    jQuery('#company-add-note-submit').on('click', function () {
        jQuery(this).attr("disabled", "disabled");
        jQuery('form#company-note-form').submit();
        jQuery('#company-add-note-submit').removeAttr("disabled");
    });
    jQuery('.modal', companyAddNoteDialog).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.modal', companyAddNoteDialog).modal('hide');
        }
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(companyAddNoteDialog);
    });
    jQuery('.modal-body').on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#pendingReminders').parent().popover('hide');
    });
}
// this function used for dynamically add new url input for notes popup of company
function addFileUrl() {
    jQuery('#files-path-url').before('<div class="form-group col-md-12 row m-0"><div class="col-md-9 col-xs-12 offset-md-3 no-padding d-flex mt-10"><div class="col-md-10"><input type="text" class="form-control" value="" name="paths[]"/></div><button class="remove-record-icon" type="button" onclick="jQuery(this).parent().parent().remove();">×</button></div></div>');
    jQuery('#files-path-url').prev().find('input:last').focus();
}
// this function used for dynamically add new attachment input for notes popup of company
function addAttachFile() {
    attachmentsCount++;
    jQuery('#attachment-file').before('<div class="form-group col-md-12 row m-0"><div class="col-md-8 offset-md-3 d-flex"><i class="fa-solid fa-link px-2 pull-left padding-all-10"></i><div class="col-md-8 row m-0"><input type="file" style="width:100%" name="attachment_'+attachmentsCount +'" id="attachment_'+attachmentsCount +'" value="" class="margin-top"/></div><button class="remove-record-icon margin-top" type="button" onclick="jQuery(this).parent().parent().remove();">×</button><input type="hidden" name="attachments[]" value="attachment_' + attachmentsCount + '"/></div></div>');
    jQuery('#attachment-file').prev().find('input:last').focus();
}
//this function is used by the response of adding company note
function closeCompanyNotePopup() {
    jQuery('.modal', 'body').modal('hide');
}
// this function used for showing edit notes popup of company
function editNote(noteID, companyID) {
    var data = {};
    var url = getBaseURL() + 'companies/edit_note/' + noteID + '/' + companyID;
    jQuery.ajax({
        dataType: 'JSON',
        type:'post',
        url: url,
        data:data,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.popuphtml) {
                if (jQuery('#company-edit-note-dialog').length <= 0) {
                    jQuery('<div id="company-edit-note-dialog"></div>').appendTo("body");
                    var companyAddNoteDialog = jQuery('#company-edit-note-dialog');
                    companyAddNoteDialog.html(response.popuphtml);
                    companyEditNoteFormEvents(companyAddNoteDialog);
                    tinymce.remove();
                    tinymce.init({
                        selector: '#note-edit',
                        menubar: false,
                        statusbar: false,
                        branding: false,
                        height: 200,
                        resize: false,
                        plugins: ['link'],
                        toolbar: 'formatselect | bold italic underline | link | undo redo ',
                        block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                        formats: {
                          underline: { inline: 'u', exact : true }
                        },
                        setup: function (editor) {
                            editor.on('init', function (e) {
                                jQuery('#note-edit_ifr').contents().find('body').prop("dir", "auto");
                                jQuery('#note-edit_ifr').contents().find('body').focus();
                            });
                        }
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
// this function used for initializing notes popup components
function companyEditNoteFormEvents(companyAddNoteDialog) {
    initializeModalSize(companyAddNoteDialog);
    if (jQuery('.hijri-date-picker', '#created-on-edit-hijri-container').length > 0) {
        makeFieldsHijriDatePicker({fields: ['created-on-edit']});
        jQuery('#created-on-edit', '#created-on-edit-hijri-container').val(gregorianToHijri(jQuery('#created-on-edit-gregorian', '#created-on-edit-hijri-container').val()));
    } else {
        setDatePicker('#date-picker', companyAddNoteDialog);
    }
    lookUpUsers(jQuery('#edit-note-look-up', companyAddNoteDialog), jQuery('#created-by-edit', companyAddNoteDialog), 'error_note_div', jQuery('.added-by-container', companyAddNoteDialog), companyAddNoteDialog);
    jQuery('#company-edit-note-submit').on('click', function () {
        jQuery(this).attr("disabled", "disabled");
        var noteTextAreaEl = tinymce.activeEditor.getContent();
        if (noteTextAreaEl == '' || noteTextAreaEl.length < 3) {
            pinesMessageV2({ty: 'warning', m: _lang.noteFieldIsRequired});
            jQuery('#company-edit-note-submit').removeAttr("disabled");
        } else {
            jQuery('form#company-note-edit-form').submit();
            jQuery('#company-edit-note-submit').removeAttr("disabled");
        }
    });
    jQuery('.modal', companyAddNoteDialog).modal({
        keyboard: false,
        backdrop: 'static',
        show: true
    });
    jQuery(document).keyup(function (e) {
        if (e.keyCode == 27) {
            jQuery('.modal', companyAddNoteDialog).modal('hide');
        }
    });
    jQuery('.modal-body').on("scroll", function() {
        jQuery('.bootstrap-select.open').removeClass('open');
    });
    jQuery('.modal').on('hidden.bs.modal', function () {
        destroyModal(companyAddNoteDialog);
    });
    jQuery('.modal').on('shown.bs.modal', function (e) { // IE9 not supported
        jQuery('#pendingReminders').parent().popover('hide');
    });
}
// this function used for dynamically add new url input of edit notes popup of company
function addFileUrlEdit() {
    jQuery('#files-path-url').before('<div class="col-md-12 text-center"><br><div class="col-md-6 col-md-offset-3 no-padding"><input type="text" class="form-control margin-left-url-input" value="" name="paths[]"/></div><div class="col-md-2 col-md-offset-1"><button class="remove-record-icon" type="button" onclick="jQuery(this).parent().parent().remove();">×</button></div></div>');
    jQuery('#files-path-url').prev().find('input:last').focus();
}
// this function used for dynamically add new attachment input of edit notes popup of company
function addAttachFileEdit() {
    attachmentsCount++;
    jQuery('#attachment-file').before('<div class="form-group col-md-12"><div class="d-flex col-md-8 offset-md-3"><i class="fa fa-link px-2 pull-left padding-all-10"></i><div class="col-md-8"><input type="file" style="width:100%" name="attachment_'+attachmentsCount +'" id="attachment_'+attachmentsCount +'" value="" class="margin-top"/></div><button class="remove-record-icon" type="button" onclick="jQuery(this).parent().parent().remove();">×</button><input type="hidden" name="attachments[]" value="attachment_' + attachmentsCount + '"/></div></div>');
    jQuery('#attachment-file').prev().find('input:last').focus();
}
function deleteSelectedNote(param) {
        const id = param.id;
        const companyID = param.companyID;

        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_note/' + id,
            dataType: "json",
            type: "POST",
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case true:  // remove successfuly
                        ty = 'information';
                        m = _lang.deleteRecordSuccessfull;
                        break;
                    case false: // could not delete record
                        m = _lang.deleteRecordFailed;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: ty, m: m});
                toggleNotes(companyID, id);
                jQuery('#company-notes-toggle').click();
            }, error: defaultAjaxJSONErrorsHandler
        });
}
function deleteCompany(id, step) {
    id = id || 0;
    step = step || "confirm_message";
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL().concat('companies').concat('/delete_company'),
        data: {'step': step,'company_id':id },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery("#company-delete-dialog").remove();
                if (jQuery('#company-delete-dialog').length <= 0) {
                    jQuery('<div id="company-delete-dialog"></div>').appendTo("body");
                    jQuery('#company-delete-dialog').html(response.html);
                }
                jQuery('.modal', jQuery('#company-delete-dialog')).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function submitThisForm(formID){
    if(formID){
        jQuery('#loader-global').show();
        jQuery("#"+formID).submit();
    }
}

function changeRemindType(element){
    if(jQuery(element).val() === 'users'){
        jQuery('#users-wrapper').removeClass('d-none');
        jQuery('#user-groups-wrapper').addClass('d-none');
        jQuery(".remove").trigger("click");
    } else{
        jQuery('#user-groups-wrapper').removeClass('d-none');
        jQuery('#users-wrapper').addClass('d-none');
        jQuery(".remove").trigger("click");
    }
}