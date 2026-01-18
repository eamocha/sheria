var movementDialog = null;
var gridSize10 = 10;
function moveShares() {
    var movementDialog = jQuery("#sharesMovementDialog");
    resetFieldsDialog();
    movementDialog.dialog("open");
    makeFieldsDatePicker({fields: ['initiatedOn'], hiddenField: 'initiatedOn_Hidden', container: 'sharesMovementForm'});
    makeFieldsDatePicker({fields: ['executedOn'], hiddenField: 'executedOn_Hidden', container: 'sharesMovementForm'});
    makeFieldsHijriDatePicker({fields: ['initiated-on-date-hijri','executed-on-date-hijri']});
    jQuery('#initiated-on-date-hijri', '#initiated-on-date-container').val(gregorianToHijri(jQuery('#initiated-on-date-gregorian', '#initiated-on-date-container').val()));
    jQuery('#executed-on-date-hijri', '#executed-on-date-container').val(gregorianToHijri(jQuery('#executed-on-date-gregorian', '#executed-on-date-container').val()));
    jQuery('#executedOn').datepicker('option', 'minDate', new Date(jQuery('#initiatedOn_Hidden').val()));
    jQuery('#initiatedOn_Hidden').change(function () {
        var minDate = jQuery('#initiatedOn_Hidden').val();
        if (undefined === minDate || minDate === '') {
            jQuery('#executedOn').val('');
        } else {
            jQuery('#executedOn').datepicker('destroy');
            makeFieldsDatePicker({fields: ['executedOn'], hiddenField: 'executedOn_Hidden', container: 'sharesMovementForm'});
            jQuery('#executedOn').datepicker('option', 'minDate', new Date(minDate));
            jQuery('#executedOn').datepicker("refresh");
        }
    });
    jQuery('#executedOn').click(function () {
        var minDate = jQuery('#initiatedOn_Hidden').val();
        jQuery('#executedOn').datepicker('destroy');
        makeFieldsDatePicker({fields: ['executedOn'], hiddenField: 'executedOn_Hidden', container: 'sharesMovementForm'});
        if (undefined === minDate || minDate === '') {
            jQuery('#executedOn').datepicker('option', 'minDate', null);
        } else {
            jQuery('#executedOn').datepicker('option', 'minDate', new Date(minDate));
        }
        jQuery('#executedOn').datepicker("refresh");
    });
}
function enableMovementFrom(movementDialog) {
    jQuery("#sharesFrom, #sharesFromType", movementDialog).removeAttr('disabled').parent().parent().parent().removeClass('d-none').slideDown('slow');
}
function disableMovementFrom(movementDialog) {
    jQuery("#sharesFrom, #sharesFromType", movementDialog).attr('disabled', 'disabled').parent().parent().parent().addClass('d-none').slideUp('fast');
}
function sharesMovementTypeChanged(lookupField, FromTo, movementDialog) {
    lookupField.value = '';
    jQuery("#shareholder_contact_id" + FromTo, movementDialog).val('');
    jQuery("#shareholder_company_id" + FromTo, movementDialog).val('');
}
function resetFieldsDialog() {
    formId = jQuery("#sharesMovementForm");
    jQuery('#shareholder_company_idTo', formId).val('');
    jQuery('#shareholder_contact_idTo', formId).val('');
    jQuery('#shareholder_company_idFrom', formId).val('');
    jQuery('#shareholder_contact_idFrom', formId).val('');
}
function sharesMovementDialogForm() {
    //UI Dialog
    movementDialog = jQuery("#sharesMovementDialog");
    movementDialog.dialog({
        autoOpen: false,
        buttons: [{
                text: _lang.save,
                'class': 'btn btn-info',
                click: function () {
                    var dataIsValid = jQuery("form#sharesMovementForm", this).validationEngine('validate');
                    jQuery("#sharesFrom, #sharesFromType, #sharesTo, #sharesToType", this).attr('disabled', 'disabled');
                    var formData = jQuery("form#sharesMovementForm", this).serialize();
                    jQuery("input:not([type='hidden']), select", this).removeAttr('disabled');
                    if (jQuery("#sharesMovementType", this).val() !== 'transfer') {
                        disableMovementFrom(movementDialog);
                    }
                    if (dataIsValid) {
                        if (jQuery("#sharesMovementType", this).val() !== 'transfer') {
                            if (jQuery('#shareholder_company_idTo', this).val() === ''
                                    && jQuery('#shareholder_contact_idTo', this).val() === '') {
                                pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.memberName])});
                                return;
                            }
                        } else {
                            if ((jQuery('#shareholder_company_idFrom', this).val() === '' && jQuery('#shareholder_contact_idFrom', this).val() === '') ||
                                    (jQuery('#shareholder_company_idTo', this).val() === '' && jQuery('#shareholder_contact_idTo', this).val() === '')) {
                                pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.memberName])});
                                return;
                            }
                        }
                        var that = this;
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery("#output", that).html('<img src="assets/images/icons/16/loader-submit.gif" />');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'companies/move_shares/' + jQuery('#companyId').val(),
                            success: function (response) {
                                jQuery("#output", that).html('');
                                if (response.result) {
                                    jQuery(that).dialog("close");
                                    jQuery("form#sharesMovementForm", that)[0].reset();
                                    disableMovementFrom(movementDialog);
                                    jQuery('#shareholdersGrid').data("kendoGrid").dataSource.read();
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
            }
            , {
                text: _lang.cancel,
                'class': 'btn btn-link',
                click: function () {
                    jQuery(this).dialog("close");
                }
            }],
        close: function () {
            jQuery('.invalid', this).removeClass("invalid");
            jQuery("form#sharesMovementForm").validationEngine('hide');
            jQuery("form#sharesMovementForm", this)[0].reset();
            disableMovementFrom(movementDialog);
            jQuery(window).unbind('resize');
        },
        open: function () {
            var that = jQuery(this);
            that.removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '70%', '520');
            }));
            resizeNewDialogWindow(that, '70%', '520');

        },
        dialogClass: '',
        draggable: true,
        modal: false,
        title: _lang.sharesOperations,
        resizable: true,
        responsive: true
    });
    // Enable the From Field according to the movement type
    jQuery("#sharesMovementType", movementDialog).change(function () {
        if (this.value != 'transfer') {
            disableMovementFrom(movementDialog);
        } else {
            enableMovementFrom(movementDialog);
        }
    });
    //lookup contacts and companies
    jQuery("#sharesFrom", movementDialog).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#sharesFromType", movementDialog).val();
            request.more_filters = {};
            if (lookupType == 'companies') {
                request.more_filters.id = jQuery('#company_idFrom', movementDialog).val();//to ignore the company of tab.
                request.more_filters.category = ['Internal', 'Group'];//tofilter category of companies
                request.more_filters.requestFlag = 'sharesTransfer';//to specify this page form
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
        minLength: 1,
        select: function (event, ui) {
            var lookupType = jQuery("select#sharesFromType", movementDialog).val();
            var elementId = lookupType == 'contacts' ? "#shareholder_contact_idFrom" : "#shareholder_company_idFrom";
            if (ui.item.record.id > 0) {
                jQuery(elementId).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": movementDialog,
                        "lookupResultHandler": setContactDataAfterAutocompleteToSharesTransferFormFrom,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": movementDialog,
                        "lookupResultHandler": setCompanyDataAfterAutocompleteToSharesTransferFormFrom,
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
    // lookup company or contact
    jQuery("#sharesTo", movementDialog).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#sharesToType", movementDialog).val();
            request.more_filters = {};
            if (lookupType == 'companies') {
                request.more_filters.id = jQuery('#company_idTo', movementDialog).val();//to ignore the company of tab.
                request.more_filters.category = ['Internal', 'Group'];//tofilter category of companies
                request.more_filters.requestFlag = 'sharesTransfer';//to specify this page form
            }
            jQuery.ajax({
                url: getBaseURL() + lookupType + '/autocomplete',
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
            var lookupType = jQuery("select#sharesToType", movementDialog).val();
            var elementId = lookupType == 'contacts' ? "#shareholder_contact_idTo" : "#shareholder_company_idTo";
            if (ui.item.record.id > 0) {
                jQuery(elementId).val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": movementDialog,
                        "lookupResultHandler": setContactDataAfterAutocompleteToSharesTransferFormTo,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                      companyContactFormMatrix.companyDialog = {
                        "referalContainerId": movementDialog,
                        "lookupResultHandler": setCompanyDataAfterAutocompleteToSharesTransferFormTo,
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

    jQuery("#sharesFromType, #sharesToType", movementDialog).each(function (index, element) {
        jQuery(element).change(function () {
            sharesMovementTypeChanged(this.parentNode.parentNode.children[1].children[0], this.getAttribute('fromto'), movementDialog);
        });
    });
    jQuery("#sharesMovementForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        'custom_error_messages': {
            '#sharesMovementType': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.shares_transfer_type])
                }
            },
            '#sharesFrom': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.shares_transfer_from])
                }
            },
            '#sharesTo': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.shares_transfer_to])
                }

            },
            '#numberOfShares': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.number_of_shares])
                }

            },
            '#category': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.category])
                }

            },
            '#initiatedOn': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.executed_on])
                }

            },
            '#executedOn': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.executed_on])
                }
            }
        }
    });
}
function setContactDataAfterAutocompleteToSharesTransferFormTo(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#shareholder_contact_idTo', container).val(record.id);
    jQuery('#sharesTo', container).val(name);
}
function setCompanyDataAfterAutocompleteToSharesTransferFormTo(record,container) {
    var name = record.name;
    jQuery('#shareholder_company_idTo', container).val(record.id);
    jQuery('#sharesTo', container).val(name);
}
function setContactDataAfterAutocompleteToSharesTransferFormFrom(record, container) {
    var name = record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName;
    jQuery('#shareholder_contact_idFrom', container).val(record.id);
    jQuery('#sharesFrom', container).val(name);
}
function setCompanyDataAfterAutocompleteToSharesTransferFormFrom(record, container) {
    var name = record.name;
    jQuery('#shareholder_company_idFrom', container).val(record.id);
    jQuery('#sharesFrom', container).val(name);
}
function deletePreferredShare(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        if(id == "null"){
            jQuery("#preferredSharesGridContainer").data("kendoGrid").dataSource.read();
        }
        else{
            jQuery.ajax({
                url: getBaseURL() + 'preferred_shares/delete/' + id + '/',
                data: {},
                type: 'POST',
                dataType: 'JSON',
                success: function (response) {
                    if (response.status == true) {
                        jQuery("#preferredSharesGridContainer").data("kendoGrid").dataSource.read();
                        jQuery(".preferred-shares-categories").val(response.preferred_shares.categories);
                        jQuery(".preferred-shares-groups").val(response.preferred_shares.shares);
                    } else {
                        pinesMessageV2({ty: 'error', m: '<ul>' + response.message + '</ul>'});
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }
}

function validatePositiveNumbers(field, rules, i, options) {
    var val = field.val();
    var positiveNumbersPattern = /^(0|[1-9]\d*)$/;
    if (!positiveNumbersPattern.test(val)) {
        return _lang.onlyPositiveNumbersAllowed;
    }
}
jQuery(document).ready(function () {
    jQuery("#companyCapitalForm").validationEngine({
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });

    kendo.culture().calendar.firstDay = 1;
    var preferredSharesDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "preferred_shares/get_company_shares_grid",
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    var response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if (response.totalRows < gridSize10)
                        jQuery('.k-pager-wrap', jQuery('#preferredSharesGridContainer')).hide();
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            create: {
                complete: function (XHRObj) {
                    try {
                        var response = jQuery.parseJSON(XHRObj.responseText || "null");
                        jQuery(".preferred-shares-categories").val(response.data.preferred_shares.categories);
                        jQuery(".preferred-shares-groups").val(response.data.preferred_shares.shares);
                    } catch (e) {
                    }
                },
                dataType: "json",
                type: 'post',
                url: getBaseURL() + "preferred_shares/add"
            },
            update: {
                complete: function (XHRObj) {
                    try {
                        var response = jQuery.parseJSON(XHRObj.responseText || "null");
                        jQuery(".preferred-shares-categories").val(response.data.preferred_shares.categories);
                        jQuery(".preferred-shares-groups").val(response.data.preferred_shares.shares);
                    } catch (e) {
                    }
                },
                dataType: "json",
                type: 'post',
                url: function (Prefered_Share) {
                    return getBaseURL() + "preferred_shares/edit/" + Prefered_Share.id
                }
            },
            parameterMap: function (options, operation) {
                var companyId = jQuery('#companyId', '#companyCapitalForm').val();
                if (undefined == companyId)
                    companyId = 0;
                if ("read" == operation) {
                    if (undefined == options.filter) {
                        options.filter = {};
                        options.filter.logic = 'and';
                        options.filter.filters = [{
                                filters: [{
                                        field: 'company_id',
                                        value: companyId,
                                        operator: 'eq'
                                    }]
                            }];
                    } else {
                        alert(options.filter.length)
                    }

                } else if ("update" == operation || "create" == operation) {
                    options.issueDate = kendo.toString(options.issueDate, "yyyy-MM-dd");
                    options.company_id = companyId;
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, nullable: true},
                    company_id: {type: "number"},
                    issueDate: {field: "issueDate", type: "date"},
                    numberOfShares: {type: "number"},
                    series: {type: "string"},
                    retrieved: {type: "string"},
                    comment: {type: "string"}
                }
            }
        },
        pageSize: gridSize10, serverPaging: true, serverFiltering: true, serverSorting: true
    });
    var preferredSharesGridOptions = {
        autobind: true,
        dataSource: preferredSharesDataSrc,
        columns: [
            //{field:"id", title: _lang.id, editable: false, filterable: false, width: 1},
            {field: "issueDate", title: _lang.issue_date, format: "{0:yyyy-MM-dd}", template:"#= (issueDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(issueDate) : issueDate, 'yyyy-MM-dd'))#", width: '120px'},
            {field: "numberOfShares", title: _lang.number_of_shares, width: '150px'},
            {field: "series", title: _lang.series, width: '72px'},
            {field: "retrieved", title: _lang.retrieved, values: ['', 'yes', 'no'], width: '96px'},
            {field: "comment", title: _lang.comment, width: '192px', sortable: false},
            {template: '<a href="javascript:" onclick="deletePreferredShare(\'#= id #\');" rel="tooltip"><i class="fa-solid fa-trash-can red"></i></a>', title: _lang.actions, width: "79px"}
        ],
        //<a href="#" class="k-button k-button-icontext k-grid-add pull-right"><span class="k-icon k-add"></span>' + _lang.add + '</a>
        toolbar: [
            {name: "gridToolbarOpen", template: '<div class="help-inline pull-left">'},
            {name: "create", text: _lang.add},
            {name: "save", text: _lang.save},
            {name: "cancel", text: _lang.cancel},
            {name: "gridToolbarClose", template: '</div>'}
        ],
        editable: true,
        filterable: false,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {mode: "multiple"}
    };
    jQuery('#preferredSharesGridContainer').kendoGrid(preferredSharesGridOptions);
    var shareholdersDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: function () {
                    var companyId = jQuery('#companyId', '#companyCapitalForm').val();
                    if (undefined == companyId)
                        companyId = 0;
                    return getBaseURL() + "companies/shareholders/" + String(companyId)
                },
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                } else if ("update" == operation || "create" == operation) {
                }
                return options;
            }
        },
        schema: {
            data: "data",
            type: "json",
            total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, nullable: true},
                    company_id: {type: "number"},
                    shares_movement_id: {type: "number"},
                    shareholderId: {type: "number"},
                    shareholderType: {type: "string"},
                    shareholderName: {type: "string"},
                    type: {type: "string"},
                    numberOfShares: {type: "number"},
                    percentage: {type: "number"},
                    sharesValue: {type: "number"},
                    currency: {type: "string"},
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
                        row['shareholderType'] = escapeHtml(row['shareholderType']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: 10,
        serverPaging: true,
        serverFiltering: false,
        serverSorting: false
    });
    var shareholdersGridOptions = {
        columns: [
            {field: 'shareholderId', title: ' ', filterable: false, sortable: false, template:
            '<div class="dropdown">' + gridActionIconHTML + '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
            '<li><a href="javascript:;" onclick="editShareMovement(\'#= shareholderId #\', true);">' + _lang.edit + '</a></li>' +
            '<li><a href="javascript:;" onclick="deleteShareMovement(\'#= shareholderId #\', \'#= "" #\', true);">' + _lang.deleteRow + '</a></li>' +
            '</ul></div>', title: ' ', width: '70px'
            },
            {field: "shareholderType", title: _lang.shareholderType, template: "#= (shareholderType=='Person')?'Contact': shareholderType#", width: '120px'},
            {field: "shareholderName", template: "<a href="+getBaseURL() + "#= (shareholderType=='Person')?'contacts/edit':(companyCategory==='Internal' ? 'companies/tab_company' : 'companies/containers_list')#" +'/${id}><bdi>${shareholderName}</bdi></a>' ,title: _lang.shareholderName, width: '137px'},
            {field: "numberOfShares", template: "#= kendo.toString(numberOfShares, \"n\") #", title: _lang.number_of_shares, width: '136px'},
            {field: "percentage", template: "#= kendo.toString(percentage, \"p4\") #", title: _lang.percentage, width: '120px'},
            {field: "sharesValue", template: "#= kendo.toString(sharesValue, \"n\") #", title: _lang.sharesValue, width: '120px'},
            {field: "currency", title: _lang.currency, width: '120px'},
            {field: "comments", title: _lang.comments, width: '120px'},
        ],
        dataSource: shareholdersDataSrc,
//		height: 250,
        selectable: "single",
        sortable: true,
        pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },
            };
    jQuery("#shareholdersGrid").kendoGrid(shareholdersGridOptions);
    customGridToolbarCSSButtons();
    jQuery("#capitalCurrency, #shareParValueCurrency").chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.currency,
        width: "100%",
        height: 100
    });
    sharesMovementDialogForm();
    jQuery('.label-tooltip').each(function (index, element) {
        jQuery(element).tooltipster({
            content: jQuery(element).attr('tooltipTitle'),
            contentAsHTML: true,
            timer: 22800,
            animation: 'grow',
            delay: 200,
            theme: 'tooltipster-default',
            touchDevices: false,
            trigger: 'hover',
            maxWidth: 350,
        });
    });
    function calculateTotalShares() {
        var totalShares = 0;
        jQuery('.add-to-total-shares').each(function() {
            let totalAddSharesValue = eval(jQuery(this).attr('id') + 'Value').rawValue;
            if(totalAddSharesValue !== '')
            totalShares += parseInt(totalAddSharesValue);
        });
        jQuery('.total-shares-input').text(number_format(totalShares, 0));
    }
    jQuery('.add-to-total-shares').bind('input propertychange', function() {
        let totalSharesValue = eval(jQuery(this).attr('id') + 'Value').rawValue;
        totalSharesValue = totalSharesValue.replace(/,(?=[\d,]*\.\d{2}\b)/g, '');
        if(!totalSharesValue.match(/^\d+$/)) {
            if(totalSharesValue.val() !== '') {
                jQuery(this).val(totalSharesValue.replace(/\D/g,''));
            } else {
                calculateTotalShares();
            }
        } else {
            calculateTotalShares();
        }
    });

});