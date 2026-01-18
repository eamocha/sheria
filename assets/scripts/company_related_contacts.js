var enableQuickSearch = true;
jQuery(document).ready(function () {
    relateToContactLookup();
    var companyRelatedContactsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/related_contacts/" + jQuery('#companyIdFilter').val(),
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            update: {
                url: getBaseURL() + "companies/related_contacts_edit",
                dataType: "json",
                type: "POST",
                complete: function(XHRObj){
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if(response.result){
                        pinesMessageV2({ty:'information', m:_lang.feedback_messages.updatesSavedSuccessfully});
                        jQuery('#relatedContactsGrid').data('kendoGrid').dataSource.read();
                    }
                }
            },
            parameterMap: function (options, operation) {
                if ("read" === operation) {
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
                    description:{type:"string",editable:true},
                    jobTitle: {type: "string",editable:false},
                    email: {type: "string",editable:false},
                    phone: {type: "string",editable:false},
                    mobile: {type: "string",editable:false},
                    category: {type: "string",editable:false},
                    fullName: {type: "string",editable:false},
                    actions: {editable:false}
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
                        row['category'] = escapeHtml(row['category']);
                        row['fullName'] = escapeHtml(row['fullName']);
                        rows.data.push(row);
                    }
                }
                return rows;
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
    var companyRelatedContactsGridOptions = {
        autobind: true,
        editable: true,
        dataSource: companyRelatedContactsDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {field: "fullName", title: _lang.name, template: '<a href="' + getBaseURL() + 'contacts/edit/#= id #">#= fullName #</a><i class="iconLegal iconPrivacy#=private#"></i>', width: '150px'},
            {field: "jobTitle", title: _lang.jobTitle, width: '150px'},
            {field: "description", title:  _lang.description, template: '#= (description!=null&&description!="") ? ((description.length>100)? description.substring(0,100)+"..." : description) : ""#', width: '320px'},
            {field: "email", title: _lang.email, width: '192px'},
            {field: "phone", title: _lang.phone, width: '120px'},
            {field: "mobile", title: _lang.mobile, width: '120px'},
            {field: "category", title: _lang.category, width: '120px', template: "#= (category == null) ? '' : category #"},
            {field: "actions", title: _lang.actions, width: '120px', template: '<div class="wraper-actions"><div class="list-of-actions"><a href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')"><i class="fa fa-fw fa-trash"></i></a></div></div>'}
        ],
        toolbar: [{name: "quick-search", template: '<div class="col-md-4 no-padding">'
                        + '<div class="input-group col-md-8">'
                        + '<input type="text" class="form-control search quick-search-filter" placeholder=" ' + _lang.searchContact + '" name="contactLookUp" id="contactLookUp" onkeyup="contactQuickSearch(event.keyCode, this.value);" title="' + _lang.searchContact + '" />'
                        + '</div>'
                        + '</div>'
            },{name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}],
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
    var relatedContactsGrid = jQuery('#relatedContactsGrid');
    if (undefined == relatedContactsGrid.data('kendoGrid')) {
        relatedContactsGrid.kendoGrid(companyRelatedContactsGridOptions);
        return false;
    }
    relatedContactsGrid.data('kendoGrid').dataSource.read();
    return false;
});

function deleteSelectedRow(contactId) {
    confirmationDialog('confim_delete_action', {resultHandler: _deleteSelectedRow, parm: contactId});
}

function _deleteSelectedRow(contactId) {
    var companyId = jQuery('#companyIdFilter', '#companyRelatedContactsSearchFilters').val();
    jQuery.ajax({
        url: getBaseURL() + 'companies/related_contacts_delete',
        dataType: "json",
        type: "POST",
        data: {
            contactId: contactId,
            companyId: companyId
        },
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.result) {
                case true:  // remove successfuly
                    ty = 'success';
                    m = _lang.relationRemovedSuccessfully;
                    break;
                case false: // could not delete record
                    m = _lang.deleteRecordFailed;
                    break;
                default:
                    break;
            }
            pinesMessageV2({ty: ty, m: m});
            jQuery('#relatedContactsGrid').data('kendoGrid').dataSource.read();
        }
    });
}
function contactQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterContactValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterContactValue2', '#filtersFormWrapper').val(term);
        var relatedContactsGrid = jQuery('#relatedContactsGrid');
        relatedContactsGrid.data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#companyRelatedContactsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('companyRelatedContactsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterContactValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function exportCompanyRelatedContactsToExcel() {
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    newFormFilter.attr('action', getBaseURL() + 'export/company_related_contacts/' + jQuery('#companyIdFilter').val()).submit();
}
function getTranslation(fieldValue) {
    return _lang.custom[fieldValue];
}
function relateToContactLookup() {
    jQuery("#relatedContactLookUp", '#relatedContactsForm').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'contacts/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_add.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
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
            if (ui.item.record.id > 0) {
                jQuery('#relatedContactId', '#relatedContactsForm').val(ui.item.record.id);
                jQuery('#btnAdd', '#relatedContactsForm').removeAttr('disabled');
            } else if (ui.item.record.id == -1) {
                companyContactFormMatrix.contactDialog = {
                    "referalContainerId": jQuery('#relatedContactsForm'),
                    "lookupResultHandler": setCompanyRelatedContactData,
                    "lookupValue": ui.item.record.term
                }
                contactAddForm();
            }
        }
    });
}
function addRelatedContact() {
    var newRelatedContact = jQuery('#relatedContactId', '#relatedContactsForm').val();
    var companyIdFilter = jQuery('#companyIdFilter', '#companyRelatedContactsSearchFilters').val();
    jQuery.ajax({
        url: getBaseURL() + 'companies/related_contacts_add',
        dataType: "json",
        type: "POST",
        data: {companyId: companyIdFilter, newContactId: newRelatedContact},
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 202:	// saved successfuly
                    ty = 'success';
                    m = _lang.feedback_messages.addedNewRelatedContactSuccessfully.sprintf([response.companyName, response.newContactName]);
                    break;
                case 101:	// could not save form
                    m = _lang.feedback_messages.addedNewRelatedContactFailed;
                    break;
                case 103:	// could not save contact already liked
                    m = _lang.feedback_messages.addedNewRelatedContactFailedContactExist.sprintf([response.companyName, response.newContactName])
                    break;
                default:
                    break;
            }
            pinesMessageV2({ty: ty, m: m});
            jQuery('#relatedContactsGrid').data('kendoGrid').dataSource.read();
            jQuery('#relatedContactId', '#relatedContactsForm').val('');
            jQuery('#relatedContactLookUp', '#relatedContactsForm').val('');
            jQuery('#btnAdd', '#relatedContactsForm').attr('disabled', 'disabled');
        }
        , error: defaultAjaxJSONErrorsHandler
    });
}
jQuery(document).ready(function(){
    try {
        // searchRelatedContacts();
        // relateToContactLookup();
        customGridToolbarCSSButtons();
    } catch (e){
    }
});