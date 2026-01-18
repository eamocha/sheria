var outsourceGridId = 'outsource-grid';
var dialog = jQuery('#case-outsource-dialog');
var modal = jQuery('#outsource-modal', '#case-outsource-dialog');
var form = jQuery('form#outsource-add-form', '#case-outsource-dialog');
var grid = jQuery('#' + outsourceGridId);
var title = _lang.addOutsource;
var outsourceCompanyLookupInput = null;
var newAddedCompany = null;
var companyContactsSelectize = null;

var outsourceDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL() + "cases/edit",
            dataType: "JSON",
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText || "null");
                showHideGridContent(response, outsourceGridId);

                if (response.totalRows < gridSize5) {
                    jQuery('.k-pager-wrap', jQuery('#' + outsourceGridId)).hide();
                }

                if (_lang.languageSettings['langDirection'] === 'rtl') {
                    gridScrollRTL();
                }
            }
        },
        update: {
            url: getBaseURL() + "cases/edit",
            dataType: "jsonp",
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText || "null");

                if (response.status == 202) {
                    pinesMessage({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                    jQuery('#' + outsourceGridId).data('kendoGrid').dataSource.read();
                } else if (response.status == 101) { // record already exist
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist });
                } else {
                    pinesMessage({ ty: 'error', m: _lang.feedback_messages.updatesFailed });
                }
            }
        },
        parameterMap: function (options, operation) {
            if ("read" !== operation && options.models) {
                // for (i in options.models) {
                //     if (parseInt(options.models[i]['legal_case_contact_role_id']) < 1) {
                //         options.models[i]['legal_case_contact_role_id'] = null;
                //     }
                // }

                return {
                    models: kendo.stringify(options.models),
                    action: 'updateOutsource'
                };
            } else {
                options.legal_case_id = jQuery('input[name="legal_case_id"]', '#outsourceSearchFilters').val();
                options.returnData = 1;
                options.action = 'readOutsource';
                options.filter = getFormFilters('outsourceSearchFilters');
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
                id: {
                    editable: false,
                    type: "integer"
                },
                case_id: {
                    editable: false,
                    type: "integer"
                },
                outsource_id: {
                    editable: false,
                    type: "integer"
                },
                company_name: {
                    editable: false,
                    type: "string",
                },
                company_id: {
                    editable: false,
                    type: "integer"
                },
                contacts_names: {
                    editable: false,
                    type: "array",
                },
                contacts_ids: {
                    editable: false,
                    type: "array"
                },
                are_licensed_advisors: {
                    editable: false,
                    type: "array"
                },
                // outsource_type: {
                //     editable: false, 
                //     type: "string",
                // },
                // role_name: {
                //     field: "role_name",
                //     editable: false
                // },
                // role_id: {
                //     field: "role_id",
                //     editable: false
                // },
                // comments: {
                //     type: "string"
                // },
                actions: {
                    editable: false,
                    type: "string"
                }
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

                    row['id'] = escapeHtml(row['id']);
                    row['outsource_id'] = escapeHtml(row['outsource_id']);
                    row['company_name'] = escapeHtml(row['company_name']);
                    row['company_id'] = escapeHtml(row['company_id']);

                    let contacts_names = escapeHtml(row['contacts_names']);
                    row['contacts_names'] = contacts_names.split(", ");

                    let contacts_ids = escapeHtml(row['contacts_ids']);
                    row['contacts_ids'] = contacts_ids.split(", ");

                    let are_licensed_advisors = escapeHtml(row['are_licensed_advisors']);
                    row['are_licensed_advisors'] = are_licensed_advisors.split(", ");

                    // OLD
                    // row['outsource_name'] = escapeHtml(row['outsource_name']);
                    // row['comments'] = escapeHtml(row['comments']);
                    // row['role_name'] = escapeHtml(row['role_name']);
                    // row['outsource_type'] = escapeHtml(row['outsource_type']);
                    rows.data.push(row);
                }
            }

            return rows;
        }
    }, error: function (e) {
        if (e.xhr.responseText == '{"status":102,"validationErrors":[]}') {
            defaultAjaxJSONErrorsHandler(e.xhr);
        }
    },
    batch: true,
    pageSize: gridSize5,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});

var outsourceGridOptions = {
    autobind: true,
    dataSource: outsourceDataSrc,
    columnMenu: {
        messages: _lang.kendo_grid_sortable_messages
    },
    columns: [
        // OLD
        // {
        //     field: 'outsource_type',
        //     hidden: true
        // },
        // {
        //     field: "outsource_name", 
        //     template: '<a href="#= getBaseURL() + \'companies/tab_company/\' + outsource_id #">#= outsource_name #</a>', 
        //     title: _lang.name
        // },
        // {
        //     field: 'outsource_type',
        //     title: _lang.type
        // },
        // {
        //     field: "role_name", 
        //     title: _lang.role
        // },
        // {
        //     field: "comments", 
        //     title: _lang.comments
        // },
        {
            field: "company_name",
            template: '<a href="#= getBaseURL() + \'companies/tab_company/\' + company_id #">#= company_name #</a>',
            title: _lang.company
        },
        {
            field: "contacts_names",
            template: '# for (var i = 0; i < contacts_names.length; i++) { #<a href="#= getBaseURL() + \'contacts/edit/\' + (contacts_ids[i] ?? null) #">#= ((are_licensed_advisors[i] == 1) ? (contacts_names[i] + " (" + _lang.isLicensedAdvisor + ")"): contacts_names[i]) #</a># if (i+1 != contacts_names.length) {#,&nbsp; #}} #',
            title: _lang.externalAdvisors
        },
        {
            field: "",
            sortable: false,
            title: "",
            width: '90px',
            template:
                '<span class="m-2">' +
                '<a href="javascript:;" class="d-inline-block" onclick="quick_add_fee_note(\'#= case_id #\', \'#= company_id #\')" title="' + _lang.edit + '">' +
                '<i class="fa fa-money text-success" aria-hidden="true"></i>' +
                '</a>' +
                '</span>' +
                '<span>' +
                '<a href="javascript:;" class="d-inline-block" onclick="editOutsourceForm(\'#= id #\', \'#= company_id #\', \'#= company_name #\', \'#= contacts_ids.map(function(contact_id) { return contact_id}).toString() #\', \'#= contacts_names.map(function(contacts_name) { return contacts_name}).toString() #\')" title="' + _lang.edit + '">' +
                '<i class="fa fa-pencil-square-o purple_color" aria-hidden="true"></i>' +
                '</a>' +
                '</span>' +
                '<span>' +
                '<a href="javascript:;" class="d-inline-block" onclick="deleteOutsource(\'#= id #\', \'#= \'deleteOutsource\' #\', \'outsource-grid\')" title="' + _lang.deleteRow + '">' +
                '<i class="fa fa-fw fa-trash light_red-color"></i>' +
                '</a>' +
                '</span>'
        }
    ],
    editable: true,
    filterable: false,
    reorderable: true,
    resizable: true,
    scrollable: true,
    sortable: {
        mode: "multiple"
    },
    selectable: "single",
    toolbar: [
        {
            name: "contacts-grid-toolbar",
            template: '<div class="col-md-12 col-xs-12 no-padding margin-bottom-10"><div class="btn-group pull-left"> '
                + '<input type="button" style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="openOutsourceForm()" value="' + _lang.add + '" />'
                + '</div>'
        },
        {
            name: "save", text: _lang.save
        },
        {
            name: "cancel", text: _lang.cancel
        }
    ]
};
function quick_add_fee_note(caseId, externalCounselId) {
    caseId = caseId || 0;
    externalCounselId = externalCounselId || 0;
    if (licenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/load_quick_add_feeNote_form/' + caseId + '/' + externalCounselId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#quick-add-fee-note-dialog').length <= 0) {
                    jQuery('<div id="quick-add-fee-note-dialog"></div>').appendTo("body");
                    var quickAddFeeNoteDialog = jQuery('#quick-add-fee-note-dialog');
                    quickAddFeeNoteDialog.html(response.html);
                    initTinyTemp('notes', "#quick-add-fee-note-dialog", "core");
                    commonModalDialogEvents(quickAddFeeNoteDialog);
                    fixDateTimeFieldDesign(quickAddFeeNoteDialog);
                    loadCustomFieldsEvents('custom-field-', quickAddFeeNoteDialog);
                    jQuery("#save-quick-add-fee-note-btn", quickAddFeeNoteDialog).click(function () {
                        quickAddFeeNoteFormSubmit(quickAddFeeNoteDialog);
                    });
                    jQuery(quickAddFeeNoteDialog).find('input').keypress(function (e)
                    {
                        // Enter pressed?
                        if (e.which == 13) {
                            quickAddFeeNoteFormSubmit(quickAddFeeNoteDialog);
                        }
                    });
                    //quickAddFeeNoteFormEvents(quickAddFeeNoteDialog, response);
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function editOutsourceForm(outsource_id, company_id, company_name, contacts_ids, contacts_names) {
    outsource_id = outsource_id || null;
    company_id = company_id || null;
    company_name = company_name || null;
    contacts_ids = contacts_ids ? contacts_ids.split(",") : [];
    contacts_names = contacts_names ? contacts_names.split(",") : [];

    getContactsByCompany(company_id, 1, function () {
        for (let i in contacts_ids) {
            let row = {
                value: parseInt(contacts_ids[i] ?? null),
                text: contacts_names[i] ?? null
            };

            companyContactsSelectize.addItem(row.value, true);
        }
    });

    jQuery('#legal-case-outsource-id').val(outsource_id);
    jQuery('#outsource-company-lookup').val(company_name);
    jQuery('#outsource-company-id').val(company_id);

    openOutsourceForm(true);
}

function openOutsourceForm(isEdit) {
    isEdit = isEdit || false;

    if (isEdit) {
        jQuery('input[name="action"]', form).val('updateOutsource');
    } else {
        jQuery('input[name="action"]', form).val('addOutsource');
    }

    form.validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
    });

    jQuery('.modal-title', '#outsource-modal').html(title);
    initializeModalSize('#case-outsource-dialog', 0.3, 0.25);
    jQuery('#outsource-modal').modal('show');
}

function submitAddForm() {
    if (form.validationEngine('validate')) {
        jQuery.ajax({
            data: form.serialize(),
            beforeSend: function () {
                jQuery('#outsource-dialog-save', dialog.parent()).attr('disabled', 'disabled');
            },
            success: function (response) {
                jQuery('#outsource-dialog-save', dialog.parent()).removeAttr('disabled');

                switch (response.status) {
                    case 202:	// saved successfuly
                        if (jQuery('.roleChanged_OnTheFly', dialog).val() === 'yes') {
                            pinesMessageV2({ ty: 'information', m: _lang.feedback_messages.onTheFlyAddedRequestRefresh });
                        }

                        if (response.hide_share_documents_with_advisors) {
                            jQuery('#share-documents-with-advisors').remove();
                        }

                        modal.modal('hide');
                        grid.data("kendoGrid").dataSource.read();
                        pinesMessageV2({ ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully });
                        break;
                    case 101:	// record already exist
                        var outsourceType = jQuery('#outsource-type-operator').val();
                        pinesMessageV2({ ty: 'error', m: outsourceType == 'contact' ? _lang.feedback_messages.addedNewContactFailedRecordExist : _lang.feedback_messages.addedNewCompanyFailedRecordExist });
                        break;
                    case 102:	// form has validation errors
                        var errorMsg = '';

                        for (i in response.validationErrors) {
                            jQuery('#' + i, dialog).addClass('invalid').focus(function () {
                                jQuery(this).removeClass('invalid');
                            });
                            errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                        }

                        if (errorMsg != '') {
                            pinesMessageV2({ ty: 'error', m: '<ul>' + errorMsg + '</ul>' });
                        }

                        break;
                    default:
                        modal.modal('hide');
                        break;
                }
            }, error: defaultAjaxJSONErrorsHandler,
            type: 'post',
            url: getBaseURL() + 'cases/edit'
        });
    }
}

function toggleOutsourcingContainer(icon, div) {
    toggleFieldsetGroup(icon, div);

    if (undefined == jQuery('#' + outsourceGridId).data('kendoGrid')) {
        jQuery('#' + outsourceGridId).kendoGrid(outsourceGridOptions);
        customGridToolbarCSSButtons();
    }
}
function toggleRoleField() {
    var outsourceType = jQuery('#outsource-type-operator').val();

    if (outsourceType == 'company') {
        jQuery('#company-role-container').removeClass('d-none');
        jQuery('#company-role-id').attr('disabled', false).selectpicker('refresh');
        jQuery('#contact-role-container').addClass('d-none');
        jQuery('#contact-role-id').attr('disabled', true);
    } else {
        jQuery('#contact-role-container').removeClass('d-none');
        jQuery('#contact-role-id').attr('disabled', false).selectpicker('refresh');
        jQuery('#company-role-container').addClass('d-none');
        jQuery('#company-role-id').attr('disabled', true);
    }

    jQuery('#outsource-lookup').val(null);
    jQuery('#outsource-id').val(null);
}

function openCategoryAddForm(container) {
    var lookupType = jQuery("select#outsource-type-operator", '#' + container).val();
    quickAdministrationDialog('contact_company_categories', '#case-outsource-dialog', true, false, false, false, lookupType == 'company' ? _lang.addContactCompanyCategory : null);
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

jQuery(document).ready(function () {
    var container = 'outsource-add-form';
    modal = jQuery('#outsource-modal', '#case-outsource-dialog');
    form = jQuery('form#outsource-add-form', '#case-outsource-dialog');
    grid = jQuery('#' + outsourceGridId);

    jQuery('#outsource-modal', '#case-outsource-dialog').on('hidden.bs.modal', function () {
        form.trigger('reset');
        // jQuery("#outsource-category-id").val('').selectpicker('refresh');
        // jQuery("#contact-role-id").val('').selectpicker('refresh');
        // jQuery("#company-role-id").val('').selectpicker('refresh');
        // jQuery("#outsource-type-operator").val('company').selectpicker('refresh');

        jQuery('#outsource-company-id').val(null);
        companyContactsSelectize.clearOptions();
        jQuery('input[name="share_documents_with_outsource"]').prop('checked', true);
    });

    // jQuery("#outsource-lookup-tooltip").tooltipster();

    // toggleRoleField();

    // jQuery('#outsource-type-operator').change(function(){
    //     toggleRoleField();
    // });

    jQuery('#outsource-dialog-save').click(function () {
        submitAddForm();
    });

    // jQuery("#outsource-category-id").change(function(){
    //     jQuery('#outsource-lookup').val(null);
    //     jQuery('#outsource-id').val(null);
    // });
    // OutsourceInitialization(jQuery("#"+container));
    // jQuery("#add-categories", "#" + container).click(function () { //quick add contact category
    //     openCategoryAddForm(container);
    // });

    if (undefined == jQuery('#' + outsourceGridId).data('kendoGrid')) {
        jQuery('#' + outsourceGridId).kendoGrid(outsourceGridOptions);
        customGridToolbarCSSButtons();
    }

    initializeLookup();
});

function companySelectedCallback(e, data) {
    getContactsByCompany(data.id, 2);

    // Instantiate the Bloodhound suggestion engine
    var mySuggestion = new Bloodhound({
        datumTokenizer: function (datum) {
            return Bloodhound.tokenizers.whitespace('');
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        replace: function (url, uriEncodedQuery) {
            return url + "#" + uriEncodedQuery;
            // the part after the hash is not sent to the server
        },
        remote: {
            url: getBaseURL() + 'contacts/autocomplete?term=%QUERY',
            filter: function (data) {
                return data;
            },
            'cache': false,
            wildcard: '%QUERY',
        }
    });

    // Initialize the Bloodhound suggestion engine
    mySuggestion.initialize();
}

function getContactsByCompany(company_id, select_all_items, callback) {
    company_id = company_id || false;
    select_all_items = select_all_items || 1;
    callback = callback || null;

    jQuery.ajax({
        url: getBaseURL() + 'contacts/get_contacts_by_company/' + company_id,
        success: function (response) {
            if (response.data) {

                let selectizeData = response.data.map(function (item) {
                    let contact_name = parseInt(item.is_licensed_advisor) == 1 ? item.name + ' (' + _lang.isLicensedAdvisor + ')' : item.name;
                    return {
                        value: parseInt(item.id),
                        text: contact_name + (item.email != null ? ' (' + item.email + ')' : '')
                    }
                });

                companyContactsSelectize.addOption(selectizeData);

                if (select_all_items === 2) {
                    for (let item of selectizeData) {
                        companyContactsSelectize.addItem(item.value, true);
                    }
                }

                if (callback) {
                    callback();
                }
            } else {
                companyContactsSelectize.clearOptions();
            }
        }
    });
}

function companyUnSelectedCallback() {
    jQuery('#outsource-company-id').on('change', function (e) {
        let companyId = jQuery(this).val();

        if (companyId == null || companyId.length <= 0) {
            companyContactsSelectize.clearOptions();
        }
    });
}

var tempContactName = null;
function initializeLookup() {
    outsourceCompanyLookupInput = jQuery('#outsource-company-lookup');

    var lookupDetails = {
        'lookupField': outsourceCompanyLookupInput,
        'hiddenId': '#outsource-company-id',
        'resultHandler': addNewCompanyCallback,
        'errorDiv': 'companies',
        'isBoxContainer': false
    };

    onSelectLookupCallback = companySelectedCallback;
    onChangeLookupCallback = companyUnSelectedCallback;

    lookUpCompanies(lookupDetails, form);

    jQuery('#outsource-company-contacts-lookup').selectize({
        plugins: ['remove_button'],
        create: function (input) {
            company = {
                id: document.getElementById("outsource-company-id").value,
                name: outsourceCompanyLookupInput[0].value
            }
            tempContactName = input;
            triggerAddContact(input, addNewContactCallback, null, company, { fromAdvisor: true });
            return {
                value: input,
                text: input,
            };
        },
    });

    companyContactsSelectize = jQuery(document.getElementById('outsource-company-contacts-lookup'))[0].selectize;

    companyUnSelectedCallback();
}

function addNewCompanyCallback(result) {
    newAddedCompany = result;
    jQuery('#outsource-company-id').val(result.id);
}

function addNewContactCallback(result) {
    getContactsByCompany(jQuery('#outsource-company-id').val(), 1, function () {
        //companyContactsSelectize.removeOption(result.firstName);

        companyContactsSelectize.refreshOptions(false);
        companyContactsSelectize.addItem(parseInt(result.id), true);

        if (tempContactName) {
            companyContactsSelectize.removeOption(tempContactName);
            tempContactName = null;
        }
    });
}

function editOutsource() {

}

function deleteOutsource(id, actionType, gridId, callback) {
    callback = callback || false;

    confirmationDialog('confirmation_delete_selected_record', {
        resultHandler: function () {
            jQuery.ajax({
                url: getBaseURL() + 'cases/edit',
                dataType: "json",
                type: "POST",
                data: {
                    recordId: id,
                    action: actionType
                },
                success: function (response) {
                    var ty = 'error';
                    var m = '';

                    switch (response.status) {
                        case 202:	// remove successfuly
                            ty = 'information';
                            m = _lang.deleteRecordSuccessfull;
                            if (callback && isFunction(callback)) {
                                callback();
                            }
                            break;
                        case 102:	// could not delete record
                            m = _lang.deleteRecordFailed;
                            break;
                        default:
                            break;
                    }

                    pinesMessageV2({ ty: ty, m: m });
                    jQuery('#' + gridId).data('kendoGrid').dataSource.read();
                }, error: defaultAjaxJSONErrorsHandler
            });
        }
    });
}