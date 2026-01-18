var gridSize5 = 5;
var gridSize10 = 10;
try {
    var lawyersContributorsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {url: getBaseURL() + "cases/edit", dataType: "JSON", type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    showHideGridContent(response, 'lawyersContributorsGrid');
                    if (response.totalRows < gridSize5)
                        jQuery('.k-pager-wrap', jQuery('#lawyersContributorsGrid')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "cases/edit",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.status == 202) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                        jQuery('#lawyersContributorsGrid').data('kendoGrid').dataSource.read();
                    } else if (response.status == 101) { // record already exist
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['legal_case_contact_role_id']) < 1) {
                            options.models[i]['legal_case_contact_role_id'] = null;
                        }
                    }
                    return {
                        models: kendo.stringify(options.models),
                        action: 'updateContributor'
                    };
                } else {
                    options.filter = getFormFilters('lawyersContributorsSearchFilters');
                    options.returnData = 1;
                    options.action = 'readContributor';
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    id: {editable: false, type: "integer"},
                    case_id: {editable: false, type: "integer"},
                    contact_id: {editable: false, type: "integer"},
                    contactName: {editable: false, type: "string"},
                    legal_case_contact_role_id: {field: "legal_case_contact_role_id"},
                    comments: {type: "string"},
                    createdOn: {editable: false, type: "string"},
                    createdBy: {editable: false, type: "string"},
                    actions: {editable: false, type: "string"}
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
                        row['contactName'] = escapeHtml(row['contactName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText == '{"status":102,"validationErrors":[]}')
                defaultAjaxJSONErrorsHandler(e.xhr);
        },
        batch: true,
        pageSize: gridSize5,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var lawyersContributorsGridOptions = {
        autobind: true,
        dataSource: lawyersContributorsDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "contactName", template: '<a href="#= getBaseURL() + \'contacts/edit/\' + contactId #">#= contactName #</a>', title: _lang.contactName},
            {field: "legal_case_contact_role_id", title: _lang.role, values: []},
            {field: "comments", title: _lang.comments},
            {field: "createdBy", title: _lang.addedBy},
            {field: "createdOn", title: _lang.addedOn, width: '185px'},
            {field: "", sortable: false, title: "", width: '40px', template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteContributor\', \'lawyersContributorsGrid\')" title="' + _lang.deleteRow + '"><i class="fa fa-fw fa-trash light_red-color"></i></a>'}
        ],
        editable: true,
        filterable: false,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        sortable: {
            mode: "multiple"
        },
        selectable: "single",
        toolbar: [{
                name: "contributor-grid-toolbar-menu",
                template: '<div class="col-md-12 col-xs-12 no-padding margin-bottom-10"><div class="btn-group pull-left">'
                        + '<input  style="line-height:1.20" type="button" class="btn btn-default btn-info margin-right" onclick="addContactPopup(\'contributor\',\'addContributor\',\'cases/edit/\',_lang.addContributor, _lang.contactName,\'contact_id\', \'contactType\')" value="'
                        + _lang.add + '" />'
                        + '</div>'
            }, {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
    };
    var contactsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {url: getBaseURL() + "cases/edit", dataType: "JSON", type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    showHideGridContent(response, 'contactsGrid');
                    if (response.totalRows < gridSize5)
                        jQuery('.k-pager-wrap', jQuery('#contactsGrid')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "cases/edit",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.status == 202) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                        jQuery('#contactsGrid').data('kendoGrid').dataSource.read();
                    } else if (response.status == 101) { // record already exist
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['legal_case_contact_role_id']) < 1) {
                            options.models[i]['legal_case_contact_role_id'] = null;
                        }
                    }
                    return {
                        models: kendo.stringify(options.models),
                        action: 'updateContacts'
                    };
                } else {
                    options.filter = getFormFilters('contactsSearchFilters');
                    options.returnData = 1;
                    options.action = 'readContacts';
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    id: {editable: false, type: "integer"},
                    case_id: {editable: false, type: "integer"},
                    contact_id: {editable: false, type: "integer"},
                    contactName: {editable: false, type: "string"},
                    legal_case_contact_role_id: {field: "legal_case_contact_role_id"},
                    comments: {type: "string"},
                    actions: {editable: false, type: "string"}
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
                        row['contactName'] = escapeHtml(row['contactName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText == '{"status":102,"validationErrors":[]}')
                defaultAjaxJSONErrorsHandler(e.xhr);
        },
        batch: true,
        pageSize: gridSize5,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var contactsGridOptions = {
        autobind: true,
        dataSource: contactsDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "contactName", template: '<a href="#= getBaseURL() + \'contacts/edit/\' + contactId #">#= contactName #</a>', title: _lang.contactName},
            {field: "legal_case_contact_role_id", title: _lang.role, values: [], width: '100px'},
            {field: "comments", title: _lang.comments, width: '160px'},
            {field: "actions", sortable: false, title: "", width: '80px', template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteContacts\', \'contactsGrid\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>'}
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
            {name: "contacts-grid-toolbar",
                template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<h4 class="col-md-4 no-padding">' + _lang.contacts + '</h4>'
                        + '</div>'
                        + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left"> '
                        + '<input type="button"  style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addContactForm(\'contact\')" value="'
                        + _lang.add + '" />'
                        + '</div>'
            },
            {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
    };
    var companiesDataSrc = new kendo.data.DataSource({
        transport: {
            read: {url: getBaseURL() + "cases/edit", dataType: "JSON", type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    showHideGridContent(response, 'companiesGrid');
                    if (response.totalRows < gridSize5)
                        jQuery('.k-pager-wrap', jQuery('#companiesGrid')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "cases/edit",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.status == 202) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                        jQuery('#companiesGrid').data('kendoGrid').dataSource.read();
                    } else if (response.status == 101) { // record already exist
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewCompanyFailedRecordExist});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['legal_case_company_role_id']) < 1) {
                            options.models[i]['legal_case_company_role_id'] = null;
                        }
                    }
                    return {
                        models: kendo.stringify(options.models),
                        action: 'updateCompanies'
                    };
                } else {
                    options.filter = getFormFilters('companiesSearchFilters');
                    options.returnData = 1;
                    options.action = 'readCompanies';
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    id: {editable: false, type: "integer"},
                    case_id: {editable: false, type: "integer"},
                    company_id: {editable: false, type: "integer"},
                    companyName: {editable: false, type: "string"},
                    legal_case_company_role_id: {field: "legal_case_company_role_id"},
                    comments: {type: "string"},
                    actions: {editable: false, type: "string"}
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
                        row['companyName'] = escapeHtml(row['companyName']);
                        row['companyCategory'] = escapeHtml(row['companyCategory']);
                        row['companyCategory'] = escapeHtml(row['companyCategory']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText == '{"status":102,"validationErrors":[]}')
                defaultAjaxJSONErrorsHandler(e.xhr);
        },
        batch: true,
        pageSize: gridSize5,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var companiesGridOptions = {
        autobind: true,
        dataSource: companiesDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "companyName", template: '#= getCompanyGridTemplate("Company", companyCategory, companyName, companyId) #', title: _lang.companyName},
            {field: "legal_case_company_role_id", title: _lang.role, values: []},
            {field: "comments", title: _lang.comments},
            {field: "actions", sortable: false, title: _lang.actions, width: '80px', template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteCompanies\', \'companiesGrid\')" title="' + _lang.deleteRow + '"><i class=fa-solid fa-trash-can red"></i></a>'}
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
            {name: "companies-grid-toolbar",
                template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<div class="col-md-12 col-xs-12 no-padding"><h4 class="col-md-4 no-padding">' + _lang.companies + '</h4>'
                        + '</div>'
                        + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left">'
                        + '<input type="button" style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addCompanyForm()" value="'
                        + _lang.add + '" />'
                        + '</div>'
            },
            {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}
        ]
    };
    var outsourcingDataSrc = new kendo.data.DataSource({
        transport: {
            read: {url: getBaseURL() + "cases/edit", dataType: "JSON", type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    showHideGridContent(response, 'outsourcingGrid');
                    if (response.totalRows < gridSize5)
                        jQuery('.k-pager-wrap', jQuery('#outsourcingGrid')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "cases/edit",
                dataType: "jsonp",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.status == 202) {
                        pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                        jQuery('#outsourcingGrid').data('kendoGrid').dataSource.read();
                    } else if (response.status == 101) { // record already exist
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist});
                    } else {
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    for (i in options.models) {
                        if (parseInt(options.models[i]['legal_case_contact_role_id']) < 1) {
                            options.models[i]['legal_case_contact_role_id'] = null;
                        }
                    }
                    return {
                        models: kendo.stringify(options.models),
                        action: 'updateOutsourcing'
                    };
                } else {
                    options.filter = getFormFilters('outsourcingSearchFilters');
                    options.returnData = 1;
                    options.action = 'readOutsourcing';
                }

                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {id: "id", fields: {
                    id: {editable: false, type: "integer"},
                    case_id: {editable: false, type: "integer"},
                    contact_id: {editable: false, type: "integer"},
                    contactName: {editable: false, type: "string"},
                    legal_case_contact_role_id: {field: "legal_case_contact_role_id"},
                    comments: {type: "string"},
                    actions: {editable: false, type: "string"}
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
                        row['contactName'] = escapeHtml(row['contactName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            if (e.xhr.responseText == '{"status":102,"validationErrors":[]}')
                defaultAjaxJSONErrorsHandler(e.xhr);
        },
        batch: true,
        pageSize: gridSize5,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true
    });
    var outsourcingGridOptions = {
        autobind: true,
        dataSource: outsourcingDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "contactName", template: '<a href="#= getBaseURL() + \'contacts/edit/\' + contactId #">#= contactName #</a>', title: _lang.external_lawyer},
            {field: "legal_case_contact_role_id", title: _lang.role, values: []},
            {field: "comments", title: _lang.comments},
            {field: "actions", sortable: false, title: _lang.actions, width: '80px', template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteOutsourcing\', \'outsourcingGrid\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>'}
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
            {name: "outsourcing-grid-toolbar",
                template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<h4 class="col-md-5 no-padding">' + _lang.outsourcing + '</h4>&nbsp;&nbsp;'
                        + '</div>'
                        + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left">'
                        + '<input type="button" style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addContactForm(\'external lawyer\')" value="'
                        + _lang.add + '" />'
                        + '</div>'
            },
            {name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}
        ]
    };
} catch (e) {
}
function toggleLawyersContributorsContainer(icon, div) {
    toggleFieldsetGroup(icon, div);
    var lawyersContributorsGridId = jQuery('#lawyersContributorsGrid');
    if (undefined == lawyersContributorsGridId.data('kendoGrid')) {
        lawyersContributorsGridOptions.columns[1].values = contactRoles;
        lawyersContributorsGridId.kendoGrid(lawyersContributorsGridOptions);
        customGridToolbarCSSButtons();
    }
}
function toggleCompaniesContactsContainer(icon, div) {
    toggleFieldsetGroup(icon, div);
    var contactsGridId = jQuery('#contactsGrid');
    if (undefined == contactsGridId.data('kendoGrid')) {
        contactsGridOptions.columns[1].values = contactRoles;
        contactsGridId.kendoGrid(contactsGridOptions);
        customGridToolbarCSSButtons();
    }
    var companiesGridId = jQuery('#companiesGrid');
    if (undefined == companiesGridId.data('kendoGrid')) {
        companiesGridOptions.columns[1].values = companyRoles;
        companiesGridId.kendoGrid(companiesGridOptions);
        customGridToolbarCSSButtons();
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
function deleteSelectedRecord(id, actionType, gridId, callback) {
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
                    pinesMessageV2({ty: ty, m: m});
                    jQuery('#' + gridId).data('kendoGrid').dataSource.read();
                }, error: defaultAjaxJSONErrorsHandler
            });
        }
    });
}
function addContactForm(contactType) {
    var contactDialog = jQuery('#caseContactDialog');
    var contactAddForm = jQuery('form#contactAddForm', contactDialog);
    jQuery('#contactType', contactAddForm).val(contactType);
    jQuery('input[name="action"]', contactAddForm).val('addContact');
    $gridId = jQuery('#contactsGrid');
    $title = _lang.addContact;
    if (contactType == 'contributor') {
        $gridId = jQuery('#lawyersContributorsGrid');
        jQuery('input[name="action"]', contactAddForm).val('addContributor');
        $title = _lang.addContributor;
    } else if (contactType == 'external lawyer') {
        $gridId = jQuery('#outsourcing-contacts-grid');
        jQuery('input[name="action"]', contactAddForm).val('addOutsourcingContact');
        $title = _lang.outsourcing;
    }
    contactAddForm.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false, 'custom_error_messages': {'#contactName': {'required': {'message': _lang.validation_field_required.sprintf([_lang.contactName])}}}});
    if (!contactDialog.is(':data(dialog)')) {
        contactDialog.dialog({
            autoOpen: false,
            buttons: [
                {
                    'class': 'btn btn-info',
                    click: function () {
                        var that = this;
                        if (contactAddForm.validationEngine('validate')) {
                            jQuery.ajax({
                                data: jQuery(contactAddForm, that).serialize(),
                                beforeSend: function () {
                                    jQuery('#contactDialogSave', contactDialog.parent()).attr('disabled', 'disabled');
                                },
                                success: function (response) {
                                    jQuery('#contactDialogSave', contactDialog.parent()).removeAttr('disabled');
                                    switch (response.status) {
                                        case 202:	// saved successfuly
                                            if (jQuery('.roleChanged_OnTheFly',contactDialog).val() === 'yes') {
                                                pinesMessage({ty: 'information', m: _lang.feedback_messages.onTheFlyAddedRequestRefresh});
                                            }
                                            contactDialog.dialog("close");
                                            $gridId.data("kendoGrid").dataSource.read();
                                            caseResources.getCaseContacts(legalCaseIdView);
                                            pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                            break;
                                        case 101:	// record already exist
                                            pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist});
                                            break;
                                        case 102:	// form has validation errors
                                            var errorMsg = '';
                                            for (i in response.validationErrors) {
                                                jQuery('#' + i, contactDialog).addClass('invalid').focus(function () {
                                                    jQuery(this).removeClass('invalid');
                                                });
                                                errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                            }
                                            if (errorMsg != '') {
                                                pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                            }
                                            break;
                                        default:
                                            contactDialog.dialog("close");
                                            break;
                                    }
                                }, error: defaultAjaxJSONErrorsHandler,
                                type: 'post',
                                url: getBaseURL() + 'cases/edit'
                            });
                        }
                    },
                    id: 'contactDialogSave',
                    text: _lang.save
                },
                {
                    'class': 'btn btn-link',
                    click: function () {
                        contactDialog.dialog("close");
                        resetDialogForm(contactAddForm);
                    },
                    text: _lang.cancel
                }
            ],
            close: function () {
                jQuery(window).unbind('resize');
                resetDialogForm(contactAddForm);
            },
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '50%', '300');
                }));
                resizeNewDialogWindow(that, '50%', '300');
                preventEnterSubmit(jQuery('#contactName', contactAddForm));
            },
            draggable: true,
            modal: false,
            resizable: true,
            responsive: true,
            title: $title
        });
        contactAutocompleteMultiOption('contactName', 2, setContactToFormAfterAutocomplete);
    }
    jQuery('#ui-id-3').html($title);
    contactDialog.dialog("open");
}
function setContactToFormAfterAutocomplete(record) {
    jQuery('#contactId', '#contactAddForm').val(record.id);
    jQuery('#contactName', '#contactAddForm').val(record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
}
function addCompanyForm(companyType) {
    companyType = companyType || 'company';

    var companyDialog = jQuery('#caseCompanyDialog');
    var companyAddForm = jQuery('form#companyAddForm', companyDialog);

    jQuery('#companyType', companyAddForm).val(companyType);
    jQuery('input[name="action"]', companyAddForm).val('addCompany');

    var gridId = jQuery('#companiesGrid');

    if (companyType == 'external lawyer') {
        gridId = jQuery('#outsourcing-companies-grid');
        jQuery('input[name="action"]', companyAddForm).val('addOutsourcingCompany');
    }

    companyAddForm.validationEngine({
        validationEventTrigger: "submit", 
        autoPositionUpdate: true, 
        promptPosition: 'bottomRight', 
        scroll: false, 
        'custom_error_messages': {
            '#companyName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.companyName])
                }
            }
        }
    });

    if (!companyDialog.is(':data(dialog)')) {
        companyDialog.dialog({
            autoOpen: false,
            buttons: [
                {
                    'class': 'btn btn-info',
                    click: function () {
                        var that = this;
                        if (companyAddForm.validationEngine('validate')) {
                            jQuery.ajax({
                                data: jQuery(companyAddForm, that).serialize(),
                                beforeSend: function () {
                                    jQuery('#companyDialogSave', companyDialog.parent()).attr('disabled', 'disabled');
                                },
                                success: function (response) {
                                    jQuery('#companyDialogSave', companyDialog.parent()).removeAttr('disabled');
                                    switch (response.status) {
                                        case 202:	// saved successfuly
                                            if (jQuery('.roleChanged_OnTheFly', companyDialog).val() === 'yes') {
                                                pinesMessage({ty: 'information', m: _lang.feedback_messages.onTheFlyAddedRequestRefresh});
                                            }
                                            companyDialog.dialog("close");
                                            gridId.data("kendoGrid").dataSource.read();
                                            caseResources.getCaseCompanies(legalCaseIdView);
                                            pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                            break;
                                        case 101:	// record already exist
                                            pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewCompanyFailedRecordExist});
                                            break;
                                        case 102:	// form has validation errors
                                            var errorMsg = '';

                                            for (i in response.validationErrors) {
                                                jQuery('#' + i, companyDialog).addClass('invalid').focus(function () {
                                                    jQuery(this).removeClass('invalid');
                                                });
                                                errorMsg += '<li>' + response.validationErrors[i] + '</li>';
                                            }

                                            if (errorMsg != '') {
                                                pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                                            }

                                            break;
                                        default:
                                            companyDialog.dialog("close");
                                            break;
                                    }
                                },
                                type: 'post',
                                url: getBaseURL() + 'cases/edit'
                            });
                        }
                    },
                    id: 'companyDialogSave',
                    text: _lang.save
                },
                {
                    'class': 'btn btn-link',
                    click: function () {
                        companyDialog.dialog("close");
                        resetDialogForm(companyAddForm);
                    },
                    text: _lang.cancel
                }
            ],
            close: function () {
//                jQuery('.ui-widget-overlay.ui-front').hide();
                jQuery(window).unbind('resize');
                resetDialogForm(companyAddForm);
            },
            open: function () {
//                jQuery('<div class="ui-widget-overlay ui-front" style="z-index: 99;"></div>').appendTo('body');
                var that = jQuery(this);
                that.removeClass('d-none');
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(that, '50%', '300');
                }));
                resizeNewDialogWindow(that, '50%', '300');
                preventEnterSubmit(jQuery('#companyName', companyAddForm));
            },
            draggable: true,
            modal: false,
            resiable: true,
            responsive: true,
            title: _lang.addNewCompany
        });
        companyAutocompleteMultiOption(jQuery('#companyName', companyDialog),setCompanyToFormAfterAutocomplete,true);
    }
    companyDialog.dialog("open");
}
function setCompanyToFormAfterAutocomplete(record) {
    jQuery('#companyId', '#companyAddForm').val(record.id);
    jQuery('#companyName', '#companyAddForm').val(record.name);
}
function resetDialogForm(form) {
    jQuery(form)[0].reset();
}
function newRecordAdded(container,data, refresh){
    var contactsGridId = jQuery('#contactsGrid');
    jQuery('.roleChanged_OnTheFly','#'+container).val('yes');
    contactRoles.push({'value':parseInt(data.id), 'text': data.name});
}
function newComapnyRecordAdded(container,data, refresh) {
    var companyGridId = jQuery('#companiesGrid');
    jQuery('.roleChanged_OnTheFly','#'+container).val('yes');
    companyRoles.push({'value':parseInt(data.id), 'text': data.name});
}
var outsourcingCompaniesDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL() + "cases/edit", 
            dataType: "JSON", 
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText || "null");
                showHideGridContent(response, 'outsourcing-companies-grid');

                if (response.totalRows < gridSize5) {
                    jQuery('.k-pager-wrap', jQuery('#outsourcing-companies-grid')).hide();
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
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    jQuery('#outsourcing-companies-grid').data('kendoGrid').dataSource.read();
                } else if (response.status == 101) { // record already exist
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewCompanyFailedRecordExist});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
            }
        },
        parameterMap: function (options, operation) {
            if ("read" !== operation && options.models) {
                for (i in options.models) {
                    if (parseInt(options.models[i]['legal_case_company_role_id']) < 1) {
                        options.models[i]['legal_case_company_role_id'] = null;
                    }
                }

                return {
                    models: kendo.stringify(options.models),
                    action: 'updateOutsourcingCompanies'
                };
            } else {
                options.filter = getFormFilters('outsourcingCompaniesSearchFilters');
                options.returnData = 1;
                options.action = 'readOutsourcingCompanies';
            }

            return options;
        }
    },
    schema: {
        type: "json",
        data: "data",
        total: "totalRows",
        model: {id: "id", fields: {
                id: {editable: false, type: "integer"},
                case_id: {editable: false, type: "integer"},
                company_id: {editable: false, type: "integer"},
                companyName: {editable: false, type: "string"},
                legal_case_company_role_id: {field: "legal_case_company_role_id"},
                comments: {type: "string"},
                actions: {editable: false, type: "string"}
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
                    row['companyName'] = escapeHtml(row['companyName']);
                    row['companyCategory'] = escapeHtml(row['companyCategory']);
                    row['companyCategory'] = escapeHtml(row['companyCategory']);
                    rows.data.push(row);
                }
            }
            return rows;
        }
    }, error: function (e) {
        if (e.xhr.responseText == '{"status":102,"validationErrors":[]}')
            defaultAjaxJSONErrorsHandler(e.xhr);
    },
    batch: true,
    pageSize: gridSize5,
    serverPaging: true,
    serverFiltering: true,
    serverSorting: true
});

var outsourcingCompaniesGridOptions = {
    autobind: true,
    dataSource: outsourcingCompaniesDataSrc,
    columnMenu: {
        messages: _lang.kendo_grid_sortable_messages
    },
    columns: [
        {
            field: "companyName", 
            template: '#= getCompanyGridTemplate("Company", companyCategory, companyName, companyId) #', 
            title: _lang.companyName
        },
        {
            field: "legal_case_company_role_id", 
            title: _lang.role, 
            values: []
        },
        {
            field: "comments", 
            title: _lang.comments
        },
        {
            field: "actions", 
            sortable: false, 
            title: _lang.actions, 
            width: '80px', 
            template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteOutsourcingCompany\', \'outsourcing-companies-grid\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>'
        }
    ],
    editable: true,
    filterable: false,
    pageable: {
        input: true, 
        messages: _lang.kendo_grid_pageable_messages, 
        numeric: false, 
        pageSizes: [5, 10, 20, 50, 100], 
        refresh: true
    },
    reorderable: true,
    resizable: true,
    scrollable: true,
    sortable: {
        mode: "multiple"
    },
    selectable: "single",
    toolbar: [
        {
            name: "companies-grid-toolbar",
            template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<div class="col-md-12 col-xs-12 no-padding"><h4 class="col-md-4 no-padding">' + _lang.companies + '</h4>'
                    + '</div>'
                    + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left">'
                        + '<input type="button" style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addCompanyForm(\'external lawyer\')" value="' + _lang.add + '" />'
                    + '</div>'
        },
        {
            name: "save", 
            text: _lang.save
        }, 
        {
            name: "cancel", 
            text: _lang.cancel
        }
    ]
};

var outsourcingContactsDataSrc = new kendo.data.DataSource({
    transport: {
        read: {
            url: getBaseURL() + "cases/edit", 
            dataType: "JSON", 
            type: "POST",
            complete: function (XHRObj) {
                var response = jQuery.parseJSON(XHRObj.responseText);
                showHideGridContent(response, 'outsourcing-contacts-grid');

                if (response.totalRows < gridSize5) {
                    jQuery('.k-pager-wrap', jQuery('#outsourcing-contacts-grid')).hide();
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
                var response = jQuery.parseJSON(XHRObj.responseText);

                if (response.status == 202) {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    jQuery('#outsourcing-contacts-grid').data('kendoGrid').dataSource.read();
                } else if (response.status == 101) { // record already exist
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewContactFailedRecordExist});
                } else {
                    pinesMessage({ty: 'error', m: _lang.feedback_messages.updatesFailed});
                }
            }
        },
        parameterMap: function (options, operation) {
            if ("read" !== operation && options.models) {
                for (i in options.models) {
                    if (parseInt(options.models[i]['legal_case_contact_role_id']) < 1) {
                        options.models[i]['legal_case_contact_role_id'] = null;
                    }
                }

                return {
                    models: kendo.stringify(options.models),
                    action: 'updateOutsourcingContacts'
                };
            } else {
                options.filter = getFormFilters('outsourcingContactsSearchFilters');
                options.returnData = 1;
                options.action = 'readOutsourcingContacts';
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
                contact_id: {
                    editable: false, 
                    type: "integer"
                },
                contactName: {
                    editable: false, 
                    type: "string"
                },
                legal_case_contact_role_id: {
                    field: "legal_case_contact_role_id"
                },
                comments: {
                    type: "string"
                },
                actions: {
                    editable: false, 
                    type: "string"
                }
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
                    row['contactName'] = escapeHtml(row['contactName']);
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

var outsourcingContactsGridOptions = {
    autobind: true,
    dataSource: outsourcingContactsDataSrc,
    columnMenu: {
        messages: _lang.kendo_grid_sortable_messages
    },
    columns: [
        {
            field: "contactName", 
            template: '<a href="#= getBaseURL() + \'contacts/edit/\' + contactId #">#= contactName #</a>', 
            title: _lang.contactName
        },
        {
            field: "legal_case_contact_role_id", 
            title: _lang.role, 
            values: []
        },
        {
            field: "comments", 
            title: _lang.comments
        },
        {
            field: "actions", 
            sortable: false, 
            title: _lang.actions, 
            width: '80px', 
            template: '<a href="javascript:;" onclick="deleteSelectedRecord(\'#= id #\', \'deleteOutsourcingContacts\', \'outsourcing-contacts-grid\')" title="' + _lang.deleteRow + '"><i class="fa-solid fa-trash-can red"></i></a>'
        }
    ],
    editable: true,
    filterable: false,
    pageable: {
        input: true, messages: _lang.kendo_grid_pageable_messages, 
        numeric: false, 
        pageSizes: [5, 10, 20, 50, 100],
        refresh: true
    },
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
            template: '<div class="col-md-12 col-xs-12 no-padding">'
                        + '<h4 class="col-md-4 no-padding">' + _lang.contacts + '</h4>'
                    + '</div>'
                    + '<div class="col-md-12 col-xs-12 no-padding"><div class="btn-group pull-left"> '
                        + '<input type="button"  style="line-height:1.20" class="btn btn-default btn-info margin-right" onclick="addContactForm(\'external lawyer\')" value="' + _lang.add + '" />'
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
function addContactPopup(contactType, action, url, title, fieldName, fieldNameId, type, contactId, caseContactId){
    title = title || _lang.addContact;
    fieldName = fieldName || _lang.contactName;
    fieldNameId = fieldNameId || 'contact_id';
    type = type || 'contactType';
    contactId = contactId || 0;
    caseContactId = caseContactId || 0;
    var data = {'action' : 'contact_add','id': legalCaseIdView, 'title': title, 'field_name': fieldName, 'field_name_id': fieldNameId, 'type': type, 'contactId': contactId, 'caseContactId': caseContactId};
    jQuery.ajax({
        url: getBaseURL().concat(url),
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var addContactId = "add-contact-container";
                jQuery('<div id="add-contact-container"></div>').appendTo("body");
                var container = jQuery("#" + addContactId);
                container.html(response.html);
                initializeModalSize(container, 0.25, 'auto');
                commonModalDialogEvents(container);
                jQuery('#role', container).selectpicker();
                var lookupContactDetails = {'lookupField': jQuery('#contactName', container), 'errorDiv': fieldNameId, 'hiddenId': '#contactId', 'resultHandler': function (record){
                        var name = (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
                        jQuery('#contactName').val(name);
                        jQuery('#contact_id').val(record.id);
                    }};
                jQuery('#contactType').val(contactType);
                switch (contactType) {
                    case 'company':
                        var lookupCompanyDetails = {'lookupField': jQuery('#contactName', container), 'hiddenId': jQuery('#contactId'),'errorDiv': fieldNameId, 'isBoxContainer': false};
                        lookUpCompanies(lookupCompanyDetails, container);
                        break;
                    case 'contact':
                        lookUpContacts(lookupContactDetails, container);
                        break;
                    default:
                        lookUpContacts(lookupContactDetails, container);
                        break;
                }
                var actionInput = jQuery('input[name="action"]');
                $title = _lang.addContact;
                actionInput.val(action);
                if (contactType == 'contributor') {
                    actionInput.val('addContributor');
                    $title = _lang.addContributor;
                } else if (contactType == 'external lawyer') {
                    actionInput.val('addOutsourcingContact');
                    $title = _lang.outsourcing;
                }
                jQuery("#add-contact-dialog-submit", container).on('click', function () {
                    cantactAddRecord(fieldNameId,contactType, action,caseContactId);
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function cantactAddRecord(fieldNameId,contactType, action, caseContactId) {
    var container = jQuery("#add-case-contact-container");
    var formData = jQuery("form#contactAddForm", container).serializeArray();
    if(action === 'updateContacts'){
        formData.push({
            name: "models", value: JSON.stringify([{
                "comments": jQuery('#comments', jQuery("#add-case-contact-container")).val(),
                "legal_case_contact_role_id": jQuery('#role', jQuery("#add-case-contact-container")).val(),
                "contactId": jQuery('#contactId', jQuery("#add-case-contact-container")).val(),
                "id": caseContactId
            }])
        });
    } else if(action === 'updateCompanies') {
        formData.push({
            name: "models", value: JSON.stringify([{
                "comments": jQuery('#comments', jQuery("#add-case-contact-container")).val(),
                "legal_case_company_role_id": jQuery('#role', jQuery("#add-case-contact-container")).val(),
                "contactId": jQuery('#contactId', jQuery("#add-case-contact-container")).val(),
                "id": caseContactId
            }])
        });
    }
    var url = getBaseURL() + 'cases/edit';
    $gridId = jQuery('#contactsGrid');
    jQuery.ajax({
        dataType: 'JSON',
        url: url,
        data: formData,
        type: 'POST',
        beforeSend: function () {
            ajaxEvents.beforeActionEvents(container);
        },
        success: function (response) {
            jQuery(".inline-error").addClass('d-none');
            switch (response.status) {
                case 202:	// saved successfuly
                    caseResources.getCaseContacts(legalCaseIdView);
                    caseResources.getCaseCompanies(legalCaseIdView);
                    if(contactType === 'contributor'){
                        $gridId = jQuery('#lawyersContributorsGrid');
                        $gridId.data("kendoGrid").dataSource.read();
                    } else if(contactType === 'external lawyer'){
                        $gridId = jQuery('#outsourcing-contacts-grid');
                        $gridId.data("kendoGrid").dataSource.read();
                    }
                    pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    jQuery('.modal').modal('hide');
                    break;
                case 101:	// record already exist
                    ajaxEvents.displayValidationError(fieldNameId, container, _lang.feedback_messages.addedNewContactFailedRecordExist);
                    break;
                case 102:	// form has validation errors
                    displayValidationErrors(response.validationErrors, container);
                    break;
                default:
                    pinesMessageV2({ty: 'error', m: response.message});
                    break;
            }
        }, complete: function () {
            ajaxEvents.completeEventsAction(container);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
jQuery(document).ready(function(){
    var lawyersContributorsGridId = jQuery('#lawyersContributorsGrid');
    if (undefined == lawyersContributorsGridId.data('kendoGrid')) {
        lawyersContributorsGridOptions.columns[1].values = contactRoles;
        lawyersContributorsGridId.kendoGrid(lawyersContributorsGridOptions);
        customGridToolbarCSSButtons();
    }
    if (undefined == jQuery('#' + outsourceGridId).data('kendoGrid')) {
        jQuery('#' + outsourceGridId).kendoGrid(outsourceGridOptions);
        customGridToolbarCSSButtons();
    }
});
