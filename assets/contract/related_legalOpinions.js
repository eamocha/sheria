var enableQuickSearch, legalOpinionsGridDataSrc, legalOpinionsGridOptions;
var legalOpinionGrid = jQuery("#legalOpinionsGrid");

function legalOpinionGridEvents() {
    legalOpinionsGridInitialization();
    searchRelatedLegalOpinions();
    jQuery('#legalOpinionsSearchFilters').bind('submit', function (e) {
        e.preventDefault();
        jQuery('#legalOpinionLookUp').val('');
        enableQuickSearch = false;
        jQuery(legalOpinionGrid).data('kendoGrid').dataSource.read();
    });
}

function legalOpinionQuickSearch(keyCode, term) {
    if (keyCode == 13) {
        if (term.length > 0) {
            document.getElementsByName("page").value = 1;
            document.getElementsByName("skip").value = 0;
            enableQuickSearch = true;
			jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val(term);
        } else {
            enableQuickSearch = false;
        }
        jQuery(legalOpinionGrid).data("kendoGrid").dataSource.page(1);
    }
}

function searchRelatedLegalOpinions() {
    var grid = jQuery(legalOpinionGrid);
    if (undefined == grid.data('kendoGrid')) {
        grid.kendoGrid(legalOpinionsGridOptions);
        var gridGrid = grid.data('kendoGrid');
        return false;
    }
    grid.data('kendoGrid').dataSource.read();
    return false;
}
function getOpinionFormFilters() {
    var filtersForm = jQuery('#legalOpinionsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('legalOpinionsSearchFilters', '.', true);
    var filters = [];

    if (!enableQuickSearch) {
        if (searchFilters.filter?.filters) {
            filters = searchFilters.filter.filters;
        }
    } else {
        if (searchFilters.quickSearch?.filters) {
            filters = searchFilters.quickSearch.filters;
        }
    }

    enableAll(filtersForm);

    return {
        logic: 'and',
        filters: filters
    };
}

function getOpinionFormFiltersold() {
    var filtersForm = jQuery('#legalOpinionsSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('legalOpinionsSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
	} else if (jQuery('#quickSearchFilterTitleValue', '#filtersFormWrapper').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);

    return filters;
}

function deleteLegalOpinionSelectedRow(id) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            url: getBaseURL() + 'legal_opinions/delete',
            type: 'POST',
            dataType: 'JSON',
            data: {legalOpinionId: id},
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// removed successfuly
                        ty = 'information';
                        m = _lang.selectedRecordDeleted;
                        break;
                    case 101:	// could not remove record
                        m = _lang.recordNotDeleted;
                        break;
                    case 303:	// could not remove record, legalOpinion related to many object & component
                        m = _lang.feedback_messages.deleteLegalOpinionFailed;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                jQuery(legalOpinionGrid).data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function contractLegalOpinionAddForm() {
    if (contractLicenseHasExpired) {
        alertLicenseExpirationMsg();
        return false;
    }
    var contractId = jQuery('#contractIdInPage', 'legalOpinionGridFormContent').val();
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'legal_opinions/add/0/0/0/' + contractId,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                if (jQuery('#legalOpinion-dialog').length <= 0) {
                    jQuery('<div id="legalOpinion-dialog"></div>').appendTo("body");
                    var legalOpinionDialog = jQuery('#legalOpinion-dialog');
                    legalOpinionDialog.html(response.html);
                    initTinyTemp('instructions', "#legalOpinion-dialog", "legalOpinion");
                    jQuery('.modal', legalOpinionDialog).modal({
                        keyboard: false,
                        backdrop: 'static',
                        show: true
                    });
                    var legalOpinionId = jQuery("#id", legalOpinionDialog).val();
                    fixDateTimeFieldDesign(legalOpinionDialog);
                    loadCustomFieldsEvents('custom-field-', legalOpinionDialog);
                    jQuery("#save-legalOpinion-btn", legalOpinionDialog).click(function () {
                        legalOpinionFormSubmit(legalOpinionDialog, legalOpinionId, false);
                    });
                    jQuery(legalOpinionDialog).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            legalOpinionFormSubmit(legalOpinionDialog, legalOpinionId, false);
                        }
                    });
                    jQuery('#lookup-contract-id', legalOpinionDialog).val(contractId);
                    opinionFormEvents(legalOpinionDialog, response);
                    jQuery('#type', legalOpinionDialog).change(function () {
                        assignmentPerType(this.value, 'legalOpinion', legalOpinionDialog);
                    });
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.feedback_messages.privateLegalOpinionMeetingMessage});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function legalOpinionsGridInitialization() {
    legalOpinionsGridDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL('contract') + "contracts/related_legalOpinions",
                dataType: "JSON",
                type: "POST",
                complete: function () {
                    if (jQuery('#filtersFormWrapper').is(':visible')) jQuery('#filtersFormWrapper').slideUp();
                    jQuery('#unarchivedButtonId').attr('disabled', 'disabled');
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    options.filter = getOpinionFormFilters();
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
                    id: {editable: false, type: "integer"},
                    legalOpinionId: { type: "string" },
                    title: {type: "string"},
                    caseId: {type: "string"},
                    legal_case_id: {type: "integer"},
                    legalOpinionType: {type: "string"},
                    private: {type: "string"},
                    location: {type: "string"},
                    priority: {type: "string"},
                    legalOpinionStatus: {type: "string"},
                    description: {type: "string"},
                    assigned_to: {type: "string"},
                    estimated_effort: {type: "string"},
                    due_date: {type: "date"},
                    createdBy: {type: "string"},
                    createdOn: {type: "date"},
                    archivedLegalOpinions: {type: "string"},
                    actions: {type: "string"}
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
                        row['createdBy'] = escapeHtml(row['createdBy']);
                        row['assigned_to'] = escapeHtml(row['assigned_to']);
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
        serverSorting: true
    });
  legalOpinionsGridOptions = {
        autobind: true,
        dataSource: legalOpinionsGridDataSrc,
        columnMenu: {messages: _lang.kendo_grid_sortable_messages},
        columns: [
            {
                field: "id",
                template: '<a href="legal_opinions/view/#=id#" rel="tooltip" title="' + _lang.edit_legalOpinion + '">#= id #</a><i class="iconLegal iconPrivacy#=private#"></i>',
                title: _lang.legalOpinionId,
                width: '100px'
            },
            {field: "title", title: _lang.title, width: '180px'},
            {field: "opinionType", title: _lang.legalOpinion_type, width: '129px'},
            {field: "opinionStatus", title: _lang.legalOpinion_status, width: '146px'},
            {field: 'instructions', title: _lang.instructions, template: '<a class="opinions-title-desc" title="#= displayContent(instructions) #" href="legal_opinions/view/#=id#"><bdi>#= instructions #</bdi></a>', width: '300px'},
            {field: "priority", title: _lang.priority, width: '100px'},
            {field: "location", title: _lang.location, width: '100px'},
            {
                field: "assigned_to",
                title: _lang.assignedTo,
                width: '140px',
                template: '#= (assigned_to!=null)?assigned_to:"" #'
            },
            {field: "estimated_effort", title: _lang.effort, width: '73px'},
            {field: "due_date", format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '150px'},
            {
                field: "createdBy",
                title: _lang.createdBy,
                width: '140px',
                template: '#= (createdBy!=null)?createdBy:"" #'
            },
            {field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '132px'},
            {field: "archivedLegalOpinions", title: _lang.archived, width: '104px'},
            {
                field: "actions",
                template: '<a href="javascript:;" onclick="deleteLegalOpinionSelectedRow(\'#= id #\')"  title="' + _lang.deleteRow + '"><i class="fa-solid fa-xmark"></i></a>',
                sortable: false,
                title: _lang.actions,
                width: '65px'
            },
        ],
        editable: "",
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
        height: 330,
        scrollable: true,
        sortable: {mode: "multiple"},
        selectable: "single",
        toolbar: [{
            name: "legalOpinion-grid-toolbar",
            template: '<div class="col-md-3">'
                + '<div class="input-group col-md-12 margin-top">'
                + '<input type="text" class="form-control search" placeholder=" '
                + _lang.searchLegalOpinion + '" name="legalOpinionLookUp" id="legalOpinionLookUp" onkeyup="legalOpinionQuickSearch(event.keyCode, this.value);" title="'
                + _lang.searchLegalOpinion + '" />'
                + '</div>'
                + '</div>'
                + '<div class="col-md-1 float-right">'
                + '<div class="btn-group float-right">'
                + '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
                + _lang.actions + ' <span class="caret"></span>'
                + '<span class="sr-only">Toggle Dropdown</span>'
                + '</button>'
                + '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
                + '<a role="button" class="dropdown-item" onclick="contractLegalOpinionAddForm()"  >'
                + _lang.addNewLegalOpinion + '</a>'
                + '</div>'
                + '</div>'
                + '</div>'
        }]
    };


}

function legalOpinionCallBack() {
    if (undefined == jQuery(legalOpinionGrid).data('kendoGrid')) {
        return true;
    }
    jQuery(legalOpinionGrid).data("kendoGrid").dataSource.read();
    return true;
}