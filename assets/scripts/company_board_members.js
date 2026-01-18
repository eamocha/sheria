function openBoardMembersForm() {
    var boardMemberDialog = jQuery("#boardMembersDialog");
    jQuery('#id', '#boardMembersForm').val('');
    jQuery('#contact_company_id', '#boardMembersForm').val('');
    boardMemberDialog.dialog("open");
}
function boardMembersDialogForm() {
    //UI Dialog
    jQuery('#id', '#boardMembersForm').val('');
    var boardDialog = jQuery("#boardMembersDialog");
    boardDialog.dialog({
        autoOpen: false,
        buttons: [{
                text: _lang.save,
                'class': 'btn btn-info',
                click: function () {
                    var dataIsValid = jQuery("form#boardMembersForm", this).validationEngine('validate');
                    var formData = jQuery("form#boardMembersForm", this).serialize();
                    var urlRequest = jQuery('#id', '#boardMembersForm').val() ?
                            'companies/edit_board_member/' + jQuery('#id', '#boardMembersForm').val() :
                            'companies/add_board_member';
                    if (jQuery('#contact_company_id').val() === '') {
                        pinesMessageV2({ty: 'warning', m: _lang.validation_field_required.sprintf([_lang.memberName])});
                    } else if (dataIsValid) {
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
                                    if (jQuery('#notify-me-before-container', 'form#boardMembersForm').is(':visible')) {
                                        loadUserLatestReminders('refresh');
                                    }
                                    jQuery(that).dialog("close");
                                    jQuery("form#boardMembersForm", that)[0].reset();
                                    jQuery('#boardMembersGrid').data("kendoGrid").dataSource.read();
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
                    jQuery("form#boardMembersForm", this)[0].reset();
                }
            }],
        close: function () {
            jQuery("form#boardMembersForm", this).validationEngine('hide');
            jQuery("form#boardMembersForm", this)[0].reset();
            jQuery("#notify-me-before-container", '#boardMembersDialog').addClass('d-none');
            jQuery('#notify-me-before-id', '#boardMembersForm').val('');
            jQuery(window).unbind('resize');
        },
        open: function () {
            jQuery(this).removeClass('d-none');
            var that = jQuery(this);
            that.removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '60%', '500');
            }));
            resizeNewDialogWindow(that, '60%', '500');
            jQuery('#pendingReminders').parent().popover('hide');
        },
        draggable: true,
        modal: false,
        title: _lang.boardMembersForm,
        resizable: true,
        responsive: true,
    });
    jQuery('#memberType', '#boardMembersForm').change(function () {
        jQuery("#contact_company_id", boardDialog).val('');
        jQuery("#memberName", boardDialog).val('');
    });
    //lookup (members)
    jQuery("#memberName", boardDialog).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery("select#memberType", boardDialog).val();
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
            if (ui.item.record.id > 0) {
                jQuery('#contact_company_id', '#boardMembersForm').val(ui.item.record.id);
            } else if (ui.item.record.id == -1) {
                var lookupType = jQuery("select#memberType", boardDialog).val();
                if (lookupType == 'contacts') {
                    companyContactFormMatrix.contactDialog = {
                        "referalContainerId": boardDialog,
                        "lookupResultHandler": setContactCompanyDataToBoardMemberFromAfterAutocomplete,
                        "lookupValue": ui.item.record.term
                    }
                    contactAddForm();
                } else {
                    companyContactFormMatrix.companyDialog = {
                        "referalContainerId": boardDialog,
                        "lookupResultHandler": setContactCompanyDataToBoardMemberFromAfterAutocomplete,
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
    makeFieldsDatePicker({fields: ['designatedOn', 'tillDate']});
    notifyMeBeforeEvent({'input': 'tillDate'}, jQuery('#boardMembersDialog'));
    jQuery("#boardMembersForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomLeft',
        scroll: false,
        'custom_error_messages': {
            '#designatedOn': {'required': {'message': _lang.validation_field_required.sprintf([_lang.designated_on])}},
            '#memberName': {'required': {'message': _lang.validation_field_required.sprintf([_lang.memberName])}},
            '#board_member_role_id': {'required': {'message': _lang.validation_field_required.sprintf([_lang.roleBoardMember])}}
        }
    });
}
function setContactCompanyDataToBoardMemberFromAfterAutocomplete(record, container) {
    var memberName = record.name ? record.name : (record.father ? record.firstName + ' ' + record.father + ' ' + record.lastName : record.firstName + ' ' + record.lastName);
    jQuery('#contact_company_id', container).val(record.id);
    jQuery('#memberName', container).val(memberName);
}
function editBoardMember(Id) {
    var boardMemberDialog = jQuery("#boardMembersDialog");
    jQuery.ajax({
        url: getBaseURL() + 'companies/edit_board_member/' + Id,
        dataType: "json",
        beforeSend: function () {
            jQuery("#output", '#boardMembersForm').html('<img height="18" src="assets/images/icons/16/loader-submit.gif" />');
            jQuery('#boardMembersFormFieldSet').hide();
        },
        success: function (response) {
            boardMemberDialog.dialog("open");
            if (response.id) {
                var memberType = (response.memberType == 'Company') ? 'companies' : 'contacts';
                jQuery('#id', '#boardMembersForm').val(response.id);
                jQuery('#contact_company_id', '#boardMembersForm').val(response.linkId);
                jQuery('#memberType', '#boardMembersForm').val(memberType);
                jQuery('#memberName', '#boardMembersForm').val(response.memberName);
                jQuery('#designatedOn', '#boardMembersForm').val(response.designatedOn);
                jQuery('#board_member_role_id', '#boardMembersForm').val(response.board_member_role_id);
                jQuery('#tillDate', '#boardMembersForm').val(response.tillDate);
                jQuery('#comments', '#boardMembersForm').val(response.comments);
                jQuery('#permanentRepresentation', '#boardMembersForm').val(response.permanentRepresentation);
                jQuery("#output", '#boardMembersForm').html('&nbsp;');
                jQuery('#designation-date-hijri', '#designation-date-container').val(gregorianToHijri(jQuery('#designatedOn', '#designation-date-container').val()));
                jQuery('#till-date-hijri', '#till-date-container').val(gregorianToHijri(jQuery('#tillDate', '#till-date-container').val()));
                jQuery('#boardMembersFormFieldSet').removeClass('d-none').show();
                if (response.notify_before) {
                    notifyMeBefore(jQuery('#boardMembersDialog'));
                    jQuery('#notify-me-before-id', '#boardMembersForm').val(response.notify_before.id);
                    jQuery('#notify-me-before-time', '#boardMembersForm').val(response.notify_before.time);
                    jQuery('#notify-me-before-time-type', '#boardMembersForm').val(response.notify_before.time_type).selectpicker('refresh');
                    jQuery('#notify-me-before-type', '#boardMembersForm').val(response.notify_before.type).selectpicker('refresh');

                }
            }
        }, error: defaultAjaxJSONErrorsHandler
    });
}
function hideBoardMemberHistoryForm() {
    jQuery('#filtersFormWrapper').slideToggle();
    jQuery('#boardMemberHistoryFilters').addClass('d-none');
    jQuery('#addFiltersBtnId').removeClass('disabled');
}
function boardMemberAddFiltersClick() {
    jQuery('#addFiltersBtnId').addClass('disabled');
    jQuery('#boardMemberHistoryFilters').removeClass('d-none').show();
    jQuery('#filtersFormWrapper').removeClass('d-none').show();
}
function currentBoardMemberBtnClick() {
    jQuery('#boardMemberHistoryFilters')[0].reset();
    jQuery('#submitBtn', '#boardMemberHistoryFilters').click();
    jQuery('#addFiltersBtnId').removeClass('disabled');
}
function deleteBoardMember(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'companies/delete_board_member/' + id,
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
                        m = _lang.deleteRecordSuccessfull;
                        jQuery('#boardMembersGrid').data("kendoGrid").dataSource.read();
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
function designatedOnDatePicker(){
    var maxDate = new Date();
    jQuery('#designatedOn').datepicker('option', 'maxDate', maxDate);
    jQuery('#designatedOn').datepicker('option', 'yearRange', 'c-100:c+100');
    jQuery('#designatedOn').datepicker("refresh");
}
jQuery(document).ready(function () {
    var companyId = jQuery('#boardMemberCompanyId', '#boardMemberHistoryFilters').val();
    if (undefined == companyId)
        companyId = 0;

    var boardMemberDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "companies/tab_board_members/" + companyId,
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('boardMembersGrid');
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    var filtersForm = jQuery('#boardMemberHistoryFilters');
                    disableEmpty(filtersForm);
                    var searchFilters = form2js('boardMemberHistoryFilters', '.', true);
                    options.filter = searchFilters.filter;
                    enableAll(filtersForm);
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
                    id: {editable: false, nullable: true},
                    designatedOn: {type: "date"},
                    memberName: {type: "string"},
                    roleName: {type: "string"},
                    tillDate: {type: "date"},
                    comments: {type: "string"},
                    permanentRepresentation: {type: "string"}

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
                        row['memberName'] = escapeHtml(row['memberName']);
                        row['memberType'] = escapeHtml(row['memberType']);
                        row['permanentRepresentation'] = escapeHtml(row['permanentRepresentation']);
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
        serverFiltering: true,
        serverSorting: false
    });
    var boardMembersGridOptions = {
        autobind: true,
        dataSource: boardMemberDataSrc,
        columns: [
            {field: 'id', title: ' ', filterable: false, sortable: false, template:
                        '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                        '<a class="dropdown-item" href="javascript:;" onclick="editBoardMember(\'#= id #\');">' + _lang.viewEdit + '</a>' +
                        '<a class="dropdown-item" href="javascript:;" onclick="deleteBoardMember(\'#= id #\');">' + _lang.deleteRow + '</a>' +
                        '</div></div>', title: ' ', width: '70px'
            },
            {field: "designatedOn", template: "#= (designatedOn == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(designatedOn) : designatedOn, 'yyyy-MM-dd'))#", title: _lang.designated_on, format: "{0:yyyy-MM-dd}", width: 110},
            {
                field: "memberName",
                template: '#= getCompanyGridTemplate(memberType, category, memberName, linkId) #',
                title: _lang.memberName,
                width: 210
            },
            {
                field: "memberType",
                title: _lang.memberType,
                template: "#= (memberType=='Person')?'Contact': memberType#",
                width: 90
            },
            {
                field: "roleName",
                title: _lang.roleBoardMember,
                width: 100
            },
            {
                field: "permanentRepresentation",
                template: '#= permanentRepresentation == "not-set" ? _lang.notSet : (permanentRepresentation == "yes" ? _lang.yes : _lang.no) #',
                title: _lang.permanentRepresentation,
                width: 160
            },
            {
                field: "tillDate",
                template: "#= (tillDate == null) ? '' : (kendo.toString(hijriCalendarEnabled == 1 ? gregorianToHijri(tillDate) : tillDate, 'yyyy-MM-dd'))#",
                title: _lang.until,
                format: "{0:yyyy-MM-dd}",
                width: 81
            },
            {
                field: "comments",
                title: _lang.comment,
                width: "192px",
                sortable: false
            }
        ],
        toolbar: [
            {
                name: "board-member-toolbar",
                template: '<div class="">'
                    + '<h4>' + _lang.boardMembers + '</h4>'
                    + '</div>'
            }
        ],
        filterable: false,
        height: 470,
 pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: false,buttonCount:5
        },
                reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        }
    };
    jQuery('#boardMembersGrid').kendoGrid(boardMembersGridOptions);
    boardMembersDialogForm();
    jQuery('#boardMemberHistoryFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#boardMembersGrid').data('kendoGrid').dataSource.read();
        hideBoardMemberHistoryForm();
        return false;
    });
    makeFieldsDatePicker({fields: ['designatedOnValue', 'tillDateValue', 'designatedOnEndValue', 'tillDateEndValue']});
    makeFieldsHijriDatePicker({fields: ['designation-date-hijri','till-date-hijri', 'designated-on-value-hijri-filter', 'designated-on-value-end-hijri-filter','tillDate-value-hijri-filter','tillDateEndValue-hijri-filter']});
    designatedOnDatePicker();
});
