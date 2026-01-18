var opinionStatusValues = ['open', 'In Progress', 'done'];
var enableQuickSearch = false;
var enableOpinionStatusesQuickSearch = true;
var licenseFlag = false, customFieldsNames;
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['dueDateValue', 'createdOnValue', 'modifiedOnValue', 'dueDateEndValue', 'createdOnEndValue', 'modifiedOnEndValue']});
    caseLookup(jQuery('#caseIdValueLookUp'));
    userLookup('assignedToValue');
    userLookup('reporterValue');
    userLookup('createdByValue');
    userLookup('modifiedByValue');
    opinionLocationLookup('locationValue');
    lookUpContracts({
        'lookupField': jQuery('#contractLookup', $searchFiltersForm),
        'hiddenId': jQuery('#contract-value', $searchFiltersForm),
    }, $searchFiltersForm);
}
function opinionQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        enableOpinionStatusesQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val(term);
        $opinionsGrid.data("kendoGrid").dataSource.page(1);
    }
}
function getFormFilters() {
    var filtersForm = jQuery('#searchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('searchFilters', '.', true);
    var filters = '';
    var myOpinions;
    try {
        myOpinions = parseInt(jQuery('#userId').attr('auth'));
    } catch (e) {
        myOpinions = 0;
    }
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function enableDisableUnarchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title', '');
    } else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        disabledUnArchiveBtn();
    }
}
function unarchivedSelectedOpinions() {
    if (confirm(_lang.confirmationUnarchiveOpinions)) {
        jQuery.ajax({
            url: getBaseURL() + 'conveyancing/archive_unarchive_opinions',
            type: 'POST',
            dataType: 'JSON',
            data: {
                gridData: form2js('gridFormContent', '.', true)
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// saved successfuly
                        ty = 'information';
                        m = _lang.feedback_messages.updatesSavedSuccessfully;
                        break;
                    case 101:	// could not save records
                        m = _lang.feedback_messages.updatesFailed;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#opinionsGrid').data("kendoGrid").dataSource.read();
                disabledUnArchiveBtn();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function exportOpinionsToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    jQuery("#opinion-contributors").prop('disabled',false).trigger('chosen:updated'); // temporarily enable the opinion-contributors dropdown so the value can be sent
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = getFormFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('searchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    var related_opinions = (opinions) ? 'my_opinions' : (reportedByMe) ? 'reported_opinions' : 'all';
    newFormFilter.attr('action', getBaseURL() + 'export/conveyancing/' + related_opinions).submit();
    if(contributedByMe) {
        jQuery("#opinion-contributors").prop('disabled',true).trigger('chosen:updated'); // re-disable opinion-contributors after finishing the export
    }
}
function deleteSelectedRow(id) {
    jQuery('#pendingReminders').parent().popover('hide');
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'conveyancing/delete',
            type: 'POST',
            dataType: 'JSON',
            data: {opinionId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// removed successfuly
                        ty = 'information';
                        m = _lang.opinionDeletedSuccessfully;
                        loadUserLatestReminders('refresh');
                        break;
                    case 101:	// could not remove record
                        m = _lang.recordNotDeleted;
                        break;
                    case 303:	// could not remove record, opinion related to many object & component
                        m = _lang.feedback_messages.deleteOpinionFailed;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery('#opinionsGrid').data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function checkUncheckAllCheckboxes(statusChkBx) {
    if (statusChkBx.checked && jQuery("tbody" + " INPUT[type='checkbox']").length >= 1) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title', '');
    } else {
        disabledUnArchiveBtn();
    }
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', statusChkBx.checked);
}
function opinionStatusFilterClick(anchor, all) {
    all = all || false;
    anchor = jQuery(anchor);
    checkbox = jQuery('input[type=checkbox]', anchor);
    icon = jQuery('i', anchor);
    //	jQuery(anchor.parent().parent().parent()).toggleClass('open');
    checkbox.attr('checked', !checkbox.is(':checked'));
    if (checkbox.is(':checked')) {
        icon.addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
    } else {
        icon.removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
    }
    if (all) {
        jQuery('input[type=checkbox]', anchor.parent().parent()).attr('checked', checkbox.is(':checked'));
        if (checkbox.is(':checked')) {
            jQuery('i', anchor.parent().parent()).addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
        } else {
            jQuery('i', anchor.parent().parent()).removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
        }
    } else if (
            jQuery('input[type=checkbox]:checked', anchor.parent().parent()).length == (jQuery('input[type=checkbox]', anchor.parent().parent()).length - 1)
            && jQuery('input[type=checkbox]:first', anchor.parent().parent()).is(':checked')
            ) {
        var allBox = jQuery('input[type=checkbox]:first', anchor.parent().parent()).attr('checked', false);
        jQuery('i', allBox.parent()).removeClass('checkbox-yes-icon').addClass('checkbox-no-icon');
    } else if (
            jQuery('input[type=checkbox]:checked', anchor.parent().parent()).length == (jQuery('input[type=checkbox]', anchor.parent().parent()).length - 1)
            && !jQuery('input[type=checkbox]:first', anchor.parent().parent()).is(':checked')
            ) {
        var allBox = jQuery('input[type=checkbox]:first', anchor.parent().parent()).attr('checked', true);
        jQuery('i', allBox.parent()).addClass('checkbox-yes-icon').removeClass('checkbox-no-icon');
    }
}
function opinionStatusQuickSearch() {
    enableOpinionStatusesQuickSearch = true;
    $opinionsGrid.data("kendoGrid").dataSource.read();
}



var gridOptions = {};
var $searchFiltersForm = null;
jQuery(document).ready(function () {
    $opinionsGrid = jQuery('#opinionsGrid');
    gridInitialization();
    jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
        jQuery("#opinion-contributors").prop('disabled',false).trigger('chosen:updated'); // temporarily enable the opinion-contributors dropdown so the value can be sent
        $opinionsGrid.data('kendoGrid').dataSource.read();
        if(contributedByMe) {
            jQuery("#opinion-contributors").prop('disabled',true).trigger('chosen:updated'); // re-disable opinion-contributors after finishing the export
        }
    });
    jQuery('.multi-select', '#searchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    jQuery('#searchFilters').bind('submit', function (e) {
        jQuery("form#searchFilters").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        if (!jQuery('form#searchFilters').validationEngine("validate")) {
            return false;
        }
        e.preventDefault();
        jQuery('#opinionLookUp').val('');
        enableQuickSearch = false;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#opinionsGrid').data('kendoGrid').dataSource.page(1);
    });

    jQuery('.checkbox', '#opinionStatusesList').click(function (e) {
        e.stopPropagation();
    });
});
function getCustomTranslation(val) {
    return _lang.custom[val];
}
function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function changeLookUp(value) {
    if (value == 'lookUp') {
        caseLookup(jQuery('#caseIdValue'));
        jQuery('#caseIdValue').addClass('lookup');
        jQuery("#caseIdValue").val('');
        jQuery('#caseIdValue').attr('title', _lang.startTyping);
        jQuery('#caseIdValue').attr('placeholder', _lang.startTyping);
        jQuery("#lookup_type").val('legal_case_id');
    } else {
        if (jQuery("#caseIdValue").hasClass('ui-autocomplete-input')) {
            jQuery("#caseIdValue").autocomplete("destroy");
            jQuery("#caseIdValue").val('');
            jQuery('#caseIdValue').removeClass('ui-autocomplete-input lookup');
            jQuery('#caseIdValue').removeAttr('title');
            jQuery('#caseIdValue').removeAttr('placeholder');
            jQuery("#lookup_type").val('caseFullSubject');
        }
    }
}
function gridInitialization() {
    var tableColumns = [];
    var savePageSize = false;
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: "id", title: ' ', filterable: false, sortable: false, template: '<input type="checkbox" name="instrumentIds[]" id="instrument_#= id #" title="' + _lang.archiveCheckboxTitle + '" value="#= id #" onchange="enableDisableUnarchivedButton(this);" />' +
                    '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu list-grid-action-demo ' + (isLayoutRTL() ? 'dropdown-menu-right" ' : '" ')  + 'role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="javascript:;" onclick="conveyancingEditForm(\'#= id #\')">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')">' + _lang.deleteRow + '</a>' +
                    '</div></div>', width: '70px'});
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'conveyancing_id') {
                array_push = {field: item, template: '<a href="conveyancing/view/#=id#" rel="tooltip" title="' + _lang.edit_item + '">#= conveyancing_id #</a>', title: _lang.id, width: '124px'};
            } else if (item === 'title') {
                array_push = {field: item, title: _lang.title, template: '<a title="#= title #" href="conveyancing/view/#=id#"><bdi>#= title #</bdi></a>', width: '300px'};
            } else if (item === 'instrument_type') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.natureOfInstrument, width: '137px'};
            }else if (item === 'transaction_type_name') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.transactionType, width: '137px'};
            } else if (item === 'status') {
                array_push = {field: item, title: _lang.workflow_status, width: '138px'};
            } else if (item === 'staff') {
                array_push = {field: item, title: _lang.staffName, width: '138px'};
            }else if (item === 'staff_pf_no') {
                array_push = {field: item, title: _lang.pfNo, width: '138px'};
            }  else if (item === 'external_counsel') {
                array_push = {field: item, title: _lang.externalCounsel, width: '138px'};
            }  else if (item === 'reference_number') {
                array_push = {field: item, title: _lang.internalReferenceNb, width: '138px'};
            } else if (item === 'description') {
                array_push = {field: item, title: _lang.description, template: '<span><bdi>#= replaceHtmlCharacter(description) #</bdi></span>', width: '300px'};
            }  else if (item === 'assignee') {
                array_push = {field: item, title: _lang.assignedTo, width: '140px', template: '#= (assignee!=null)?assignee:"" #'};
            }else if (item === 'property_value') {
                array_push = {field: item, title: _lang.propertyValue, width: '154px', template: '#= kendo.toString(property_value, "n2") #'};
            } else if (item === 'amount_approved') {
                array_push = {field: item, title: _lang.amountApproved, width: '154px', template: '#= kendo.toString(amount_approved, "n2") #'};
            }  else if (item === 'amount_paid') {
                array_push = {field: item, title: _lang.amountPaid, width: '168px', template: '#= kendo.toString(amount_paid, "n2") #'};
            } else if(item === 'amount_requested') {
                array_push = {field: 'amount_requested', title: _lang.amountRequested, template: '#=kendo.toString(amount_requested, "n2") #' , width: '120px', sortable: false};
            } else if (item === 'due_date') {
                array_push = {field: item,  title: _lang.due_date, width: '168px'};
            } else if (item === 'date_initiated') {
                array_push = {field: item,  title: _lang.initiated_on, width: '168px'};
            }  else if (item === 'createdBy') {
                array_push = {field: item, title: _lang.createdBy, width: '155px', template: '#= (createdBy!=null)?createdBy:"" #'};
            } else if (item === 'createdOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.dateReceived, width: '133px'};
            } else if (item === 'modifiedBye') {
                array_push = {field: item, title: _lang.modifiedBy, width: '155px', template: '#= (modifiedByName!=null)?modifiedByName:"" #'};
            } else if (item === 'modifiedOn') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '133px'};
            } else if (item === 'archived') {
                array_push = {field: item, title: _lang.archived, template: '#= getCustomTranslation(archived) #', width: '101px'};
            } else if (item.startsWith('custom_field_')) { //check if the item is a custom field then get the title name from a defined array
                array_push = {field: item, title: customFieldsNames[item],width: '140px', template: '#= ' + item + '!==null ? (' + item + '.length>255 ? ' + item + '.substring(0, 255) + "..." :' + item + ' ):"" #'};  
            } else if (item === 'contributors') {
                array_push = {field: item, title: _lang.lawFirm,width: '140px'};
            } else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    var gridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if (hasAccessToExport != 1) {
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            } else {
                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportOpinionsToExcel(true);
                                    } else {
                                        exportOpinionsToExcel();
                                    }
                                } else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (jQuery('#filtersFormWrapper').is(':visible'))
                        jQuery('#filtersFormWrapper').slideUp();
                    jQuery('#selectAllCheckboxes').attr('checked', false);
                    disabledUnArchiveBtn();
                    animateDropdownMenuInGrids('opinionsGrid');
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    var userValue = jQuery('#quickSearchFilterAssignedToValue').val();
                    operation.url = userValue !== '' ? (getBaseURL() + 'conveyancing/my_instruments') : (getBaseURL() + 'conveyancing/index');
                    options.filter = getFormFilters();
                    options.sortData = JSON.stringify(gridDataSrc.sort());
                    if (savePageSize) {
                        options.savePageSize = true;
                        savePageSize = false;
                    }
                    if (enableOpinionStatusesQuickSearch) {
                        var opinionStatuses = jQuery("input.opinion-status-group:checked", '#opinionStatusesList').map(function () {
                            return this.value;
                        }).get();
                        options.OpinionStatusesFilter = opinionStatuses;
                    }
                }
                jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                options.caseIdFilter = jQuery('#caseIdFilter', '#searchFilters').val();
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
                    
                    id: { type: "number" },
                    conveyancing_id: { type: "string" },
                    title: { type: "string" },
                    instrument_type_id: { type: "number" },
                    instrument_type: { type: "string" },
                    transaction_type: { type: "number" },
                    transaction_type_name: { type: "string" },
                    reference_number: { type: "string" },
                    parties: { type: "string" },
                    initiated_by: { type: "number" },
                    assignee_id: { type: "number" },
                    assignee: { type: "string" },
                    staff_pf_no: { type: "string" },
                    staff: { type: "string" },
                    date_initiated: { type: "date" },
                    due_date: { type: "date" },
                    description: { type: "string" },
                    external_counsel: { type: "string" },
                    property_value: { type: "number" },
                    amount_requested: { type: "number" },
                    amount_approved: { type: "number" },
                    createdOn: { type: "date" },
                    createdBy: { type: "number" },
                    creator_name: { type: "string" },
                    modifiedOn: { type: "date" },
                    modifiedBy: { type: "number" },
                    modifier_name: { type: "string" },
                    archived: { type: "string" },
                    channel: { type: "string" },
                    visible_to_CP: { type: "boolean" },
                    date_received: { type: "date" },
                    status: { type: "string" },
                    requested_by: { type: "number" }
                    
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
                        if(row['description'] != null){
                            row['description'] = escapeHtml(row['description'].replace(/(<([^>]+)>)/gi, ""));
                        }else{
                            row['description'] = '';
                        }
                        if(row['title'] != null){
                            row['title'] = escapeHtml(row['title'].replace(/(<([^>]+)>)/gi, ""));
                        }else{
                            row['title'] = '';
                        }
                        row['assignee'] = escapeHtml(row['assignee']);
                        row['instrument_type'] = escapeHtml(row['instrument_type']);
                        row['transaction_type_name'] = escapeHtml(row['transaction_type_name']);
                        row['status'] = escapeHtml(row['status']);  
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['modifiedBy'] = escapeHtml(row['modifiedBy']);
                        row['modifiedBy'] = escapeHtml(row['modifiedBy']);
                        row['createdOn'] = escapeHtml(row['createdOn']);
                        row['modifiedOn'] = escapeHtml(row['modifiedOn']);  
                        row['assigned_to'] = escapeHtml(row['assigned_to']);
                        row['contributors'] = escapeHtml(row['contributors']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr)
        },
        pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null")
    });
    gridOptions = {
        autobind: true,
        dataSource: gridDataSrc,
        columnResize: function (e) {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        },
        columns: tableColumns,
        dataBound: function () {
            jQuery('.opinions-title-desc').each(function (index, element) {
                if(jQuery(element).hasClass("tooltipstered")){
                    jQuery(element).tooltipster("destroy");
                }
                jQuery(element).tooltipster({
                    contentAsHTML: true,
                    timer: 22800,
                    animation: 'grow',
                    delay: 200,
                    theme: 'tooltipster-default',
                    touchDevices: false,
                    trigger: 'hover',
                    maxWidth: 350,
                    interactive: true
                });
            });
        },
        editable: "",
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: {
            mode: "multiple"
        },
        toolbar: [{
                name: "toolbar-menu",
                template: '<div></div>'

            }],
    };
    gridTriggers({'gridContainer': $opinionsGrid, 'gridOptions': gridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    var gridGrid = $opinionsGrid.data('kendoGrid');
    gridGrid.thead.find('th:first').append('<input type="checkbox" id="selectAllCheckboxes" onchange="checkUncheckAllCheckboxes(this);" title="' + _lang.selectAllRecords + '" />');
    gridGrid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    displayColHeaderPlaceholder();
    if(contributedByMe) {
        jQuery("#opinion-contributors").prop('disabled',true).trigger('chosen:updated');
    }
}
function opinionCallBack() {
    jQuery('#opinionsGrid').data("kendoGrid").dataSource.read();
    return true;
}

