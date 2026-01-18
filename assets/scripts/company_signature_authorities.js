function getFormFilters() {
    var filtersForm = jQuery('#companyAssetsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companyAssetsSearchFilters', '.', true);
    var filters = '';
    filters = searchFilters.filter;
    enableAll(filtersForm);
    return filters;
}
var signatureAuthorityDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL() + "companies/company_signature_authorities/",
            dataType: "JSON",
            type: "POST",
            complete: function () {
                animateDropdownMenuInGrids('signatureAuthorityGrid');
            }
        },
        parameterMap: function (options, operation) {
            if ("read" == operation) {
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
                id: {editable: false, nullable: true},
                company_id: {type: "integer"},
                name: {type: "string"},
                authorized_signatory: {type: "string"},
                kind_of_signature: {type: "string"},
                joint_signature_with: {type: "string"},
                sole_signature: {type: "string"},
                capacity: {type: "string"},
                term_of_the_authorization: {type: "string"}
            }
        }
    }, error: function (e) {
        if (e.xhr.responseText != 'True')
            defaultAjaxJSONErrorsHandler(e.xhr)
    },
    batch: true,
    pageSize: 10,
    editable: false,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});

var signatureAuthorityGridOptions = {
    autobind: true,
    dataSource: signatureAuthorityDataSrc,
    columnMenu: {messages: _lang.kendo_grid_sortable_messages},
    columns: [
        {field: 'id', title: ' ', filterable: false, sortable: false, template:
                    '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="javascript:;" onclick="signatureAuthorityDialogForm(false,#= id #);">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="confirmationDialog(\'confirm_delete_sa\', {resultHandler: deleteSignatureAuthority, parm: {\'id\': \'#= id #\'} });">' + _lang.delete + '</a>' +

                    '</div></div>', title: ' ', width: '70px'
        },
        {field: "signature_authority_id", title: _lang.id, width: '90px'},
        {field: "name", title: _lang.name, width: '120px'},
        {field: "authorized_signatory", title: _lang.authorizedSignatory, width: '274px'},
        {field: "kind_of_signature", title: _lang.kindOfSignature, width: '155px'},
        {field: "joint_signature_with", title: _lang.jointSignatureWith, width: '181px'},
        {field: "sole_signature", title: _lang.soleSignature, width: '160px'},
        {field: "capacity", title: _lang.capacity, width: '160px'},
        {field: "term_of_the_authorization", title: _lang.termOfTheAuthorization, width: '191px'},
        {field: "attachment_name", title: _lang.document,template:'<a href="companies/download_file/#= attachment_id #">#= attachment_name == null ? \'\' : attachment_name  #</a> <b><a href="javascript:;" onclick="confirmationDialog(\'confirm_delete_document\',{resultHandler: deleteSignatureAuthorityDocument, parm: {id: #= id #, attachmentId: #= attachment_id #}})"><i title="#= _lang.delete#" class= "#= attachment_name == null ? "" : "fa-solid fa-trash-can red"#"></i></a> <a href="companies/documents/#=company_id#\\##=parent_lineage#"><i title="#= _lang.showMore#" class= "#= attachment_name == null ? "" : "fa-light fa-list blue"#"></i></a></b>', width: '191px'},
    ],
    filterable: false,
         pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },
            reorderable: true,
    resizable: true,
    scrollable: true,
    sortable: {mode: "multiple"},
    selectable: "single",
    height: '330',
    toolbar: [{
            name: "quick-add-edit",
            template: '<div class="col-md-4 no-padding">'
                    + '<h4>' + _lang.signatureAuthority + '</h4>'
                    + '</div>'
        }],
};
function searchSignatureAuthority() {
    var signatureAuthorityGrid = jQuery('#signatureAuthorityGrid', '#signatureAuthorityGridForm');
    if (undefined == signatureAuthorityGrid.data('kendoGrid')) {
        signatureAuthorityGrid.kendoGrid(signatureAuthorityGridOptions);
        return false;
    }
    signatureAuthorityGrid.data('kendoGrid').dataSource.read();
    return false;
}
function exportToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('companyAssetsSearchFilters')));
    newFormFilter.attr('action', getBaseURL() + 'export/company_signature_authorities').submit();
}
jQuery(document).ready(function () {
    searchSignatureAuthority();
});


function sa_name_autocomplete() {
    jQuery('#saNameType').change(function () {
        jQuery("#contact_company_id").val('');
        jQuery("#saNameLookup").val('');
    });
    jQuery("#saNameLookup").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#saNameType").val();
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
        minLength: 1,
        select: function (event, ui) {
            var lookupType = jQuery("select#saNameType").val();
            if (ui.item.record.id > 0) {
                jQuery('#contact_company_id', jQuery(this).parent()).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": jQuery("#signatureAuthorityDialog"),
                        "lookupResultHandler": setContactCompanyDataToSignatureAuthority,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                      companyContactFormMatrix.companyDialog = {
                        "referalContainerId":jQuery("#signatureAuthorityDialog"),
                        "lookupResultHandler": setContactCompanyDataToSignatureAuthority,
                        "lookupValue": ui.item.record.term
                    };
                    companyAddForm();
                }
            }
        }
    });
}

function signatureAuthorityDialogForm(isFormLoaded, id) {
    var company_id = jQuery("#company_id").val();
    var URL;
    var msg = '';
    if (id == '') {
        URL = 'add_signature_authority';
        msg = _lang.feedback_messages.addedNewSignatureSuccessfully;
    } else {
        URL = 'edit_signature_authority/' + id;
        msg = _lang.feedback_messages.updatesSavedSuccessfully;
        ;
    }
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'companies/' + URL,
        success: function (response) {
            if (response.html) {
                var signatureAuthorityId = "#signature-authority-form-div";
                if (jQuery(signatureAuthorityId).length <= 0) {
                    jQuery("<div id='signature-authority-form-div'></div>").appendTo("body");
                    var signatureAuthorityContainer = jQuery(signatureAuthorityId);
                    signatureAuthorityContainer.html(response.html);
                    commonModalDialogEvents(signatureAuthorityContainer, signatureAuthorityFormSubmit);
                    initializeModalSize(signatureAuthorityContainer);
                    jQuery('.select-picker', signatureAuthorityContainer).selectpicker({dropupAuto: false});
                    jQuery('#company-id', signatureAuthorityContainer).val(jQuery('#companyId', '#assetsGridForm').val());
                    fixDateTimeFieldDesign(signatureAuthorityContainer);
                    loadCustomFieldsEvents('custom-field-',signatureAuthorityContainer);
                    jQuery('#company_id', '#signatureAuthorityAddForm').val(company_id);
                    sa_name_autocomplete();
                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function signatureAuthorityFormSubmit(container){
    var formData = new FormData(document.getElementById('signatureAuthorityAddForm'));
    var id = jQuery('#sa-id').val();
    URL = id > 0 ? 'edit_signature_authority/' + id : 'add_signature_authority';
    jQuery.ajax({
        url: getBaseURL() + 'companies/' + URL,
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.addedNewSignatureSuccessfully});
                if (jQuery('#signatureAuthorityGrid').length) {
                    jQuery('#signatureAuthorityGrid').data("kendoGrid").dataSource.read();
                }
                jQuery(".modal", container).modal("hide");
                
            } else {
                displayValidationErrors(response.validationErrors, container);
            }
        }
        , complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function setContactCompanyDataToSignatureAuthority(record, container) {
    var contactName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#contact_company_id', container).val(record.id);
    jQuery('#saNameLookup', container).val(contactName);
}

function deleteSignatureAuthorityDocument(data){
    jQuery.ajax({
        url: getBaseURL() + 'companies/delete_signature_authority_document',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'sa_id': data.id,
            'document_id': data.attachmentId,
        },
        success: function (response) {
            pinesMessageV2({ty: response.status ? 'information' : 'error', m: response.message});
            if (response.status) {
                jQuery('#signatureAuthorityGrid').data('kendoGrid').dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteSignatureAuthority(params){
    jQuery.ajax({
        url: getBaseURL() + 'companies/delete_signature_authority',
        type: 'POST',
        dataType: 'JSON',
        data: 
            {
                'sa_id' : params['id'],
            },
        success: function (response) {
            pinesMessageV2({ty: response.status ? 'success' : 'error', m: response.message});
            if (response.status) {
                jQuery('#signatureAuthorityGrid').data('kendoGrid').dataSource.read();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
