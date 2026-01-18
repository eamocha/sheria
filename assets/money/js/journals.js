function initJournalsGrid() {
	var grid = jQuery('#journalGrid');
        document.getElementsByName("page").value =1;
        document.getElementsByName("skip").value =0;
	if (undefined === grid.data('kendoGrid')) {
		grid.kendoGrid(gridOptions);
		return false;
	}
	grid.data('kendoGrid').dataSource.page(1);
	return false;
}
var gridDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL('money') + "vouchers/journals_list",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				animateDropdownMenuInGrids('journalGrid');
			}
		},
		parameterMap: function (options, operation) {
			if ("read" !== operation && options.models) {
				return {
					models: kendo.stringify(options.models)
				};
			} else {
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
                    id: {editable: false, type: "number"},
                    organization_id: {type: "string"},
                    refNum: {type: "string"},
                    referenceNum: {type: "string"},
                    dated: {type: "date"},
                    amount: {type: "string"},
                    createdByName: {type: "string"},
                    createdOn: {type: "date"},
                    modifiedByName: {type: "string"},
                    modifiedOn: {type: "date"}
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
                        row['createdByName'] = escapeHtml(row['createdByName']);
                        row['modifiedByName'] = escapeHtml(row['modifiedByName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
	},
	pageSize: 20,
	serverPaging: true,
	serverFiltering: true,
	serverSorting: true
});
var gridOptions = {};
var currencyCode = '';
jQuery(document).ready(function () {
	currencyCode = ' (' + jQuery('#currencyCode').val() + ')';
	gridOptions = {
		autobind: true,
		dataSource: gridDataSrc,
		columnMenu: {messages: _lang.kendo_grid_sortable_messages},
		columns: [
			{field: 'id', title: ' ', filterable: false, sortable: false,
				template: '<div class="dropdown">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
						'<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/journal_print/#= id #">' + _lang.print + '</a>' +
						'<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/journal_edit/#= id #">' + _lang.viewEdit + '</a>' +
						'<a class="dropdown-item" href="javascript:;" onclick="deleteJournal(\'#= id#\');">' + _lang.deleteRow + '</a>' +
						'</div></div>', width: '70px'
			},
			{field: "refNum", template: '#= str_pad(refNum, 5, "0", "STR_PAD_LEFT") #', title: _lang.journalRefNum, width: '219px'},
			{field: "referenceNum", title: _lang.externalRef, width: '154px'},
			{field: "dated", format: "{0:yyyy-MM-dd}", title: _lang.date, width: '100px'},
			{field: "amount", title: _lang.amount + currencyCode, width: '120px'},
			{field: "description", title: _lang.description, width: '112px'},
			{field: "createdByName", title: _lang.createdBy, width: '145px',template: '#= (createdByName!=null && createdStatus=="Inactive")? createdByName+" ("+_lang.custom[createdStatus]+")":((createdByName!=null)?createdByName:"") #'},
			{field: "createdOn", format: "{0:yyyy-MM-dd}", title: _lang.createdOn, width: '138px'},
			{field: "modifiedByName", title: _lang.modifiedBy, width: '140px',template: '#= (modifiedByName!=null && modifiedStatus=="Inactive")? modifiedByName+" ("+_lang.custom[modifiedStatus]+")":((modifiedByName!=null)?modifiedByName:"") #'},
			{field: "modifiedOn", format: "{0:yyyy-MM-dd}", title: _lang.modifiedOn, width: '139px'}
		],
		editable: "",
		filterable: false,
		height: 500,
		pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
		reorderable: true,
		resizable: true,
		scrollable: true,
		selectable: "single",
		sortable: {
			mode: "multiple"
		},
		toolbar: [{
				name: "journal-grid-toolbar",
				template: '<div class="col-md-2 no-padding">'
						+ '<h4 class="col-md-9">' + _lang.manualJournals + '</h4>'
						+ '</div>'
						+ '<div class="col-md-2 no-padding advanced-search">'
						+ '<a href="javascript:;" class="" onclick="journalsAdvancedSearchFilters()">' + _lang.advancedSearch + '</a>'
						+ '</div>'
						+ '<div class="col-md-1 pull-right">'
						+ '<div class="btn-group pull-right">'
						+ '<button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown">'
						+ _lang.actions + ' <span class="caret"></span>'
						+ '<span class="sr-only">Toggle Dropdown</span>'
						+ '</button>'
						+ '<div class="dropdown-menu" aria-labelledby="dLabel" role="menu">'
						+ '<a class="dropdown-item" href="' + getBaseURL('money') + 'vouchers/journal_add" >' + _lang.addJournal + ' </a>'
						+ '</div>'
						+ '</div>'
						+ '</div>'
			}]
	};
	initJournalsGrid();
	jQuery('#journalSearchFilters').bind('submit', function (e) {
		jQuery("form#journalSearchFilters").validationEngine({
			validationEventTrigger: "submit",
			autoPositionUpdate: true,
			promptPosition: 'bottomRight',
			scroll: false
		});
		if (!jQuery('form#journalSearchFilters').validationEngine("validate")) {
			return false;
		}
		e.preventDefault();
		initJournalsGrid();
	});
});
function journalsAdvancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		makeFieldsDatePicker({fields: ['dateValue', 'dateEndValue', 'createdOnValue', 'createdOnEndValue', 'modifiedOnValue', 'modifiedOnEndValue']});
		userLookup('createdByValue');
		userLookup('modifiedByValue');
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
	var filtersForm = jQuery('#journalSearchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('journalSearchFilters', '.', true);
	var filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
function deleteJournal(voucherID) {
	if (confirm(_lang.confirmationDeleteSelectedRecord)) {
		jQuery.ajax({
			url: getBaseURL('money') + 'vouchers/journal_delete',
			type: 'POST',
			dataType: 'JSON',
			data: {voucherID: voucherID},
			success: function (response) {
				var ty = 'error';
				var m = '';
				switch (response.status) {
					case 101:	// removed successfuly
						ty = 'information';
						m = _lang.selectedJournalDeleted;
						break;
					case 202:	// could not remove record
						ty = 'warning';
						m = _lang.recordNotDeleted;
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
				jQuery('#journalGrid').data("kendoGrid").dataSource.read();
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function validateIntegers(field, rules, i, options) {
	var val = field.val();
	var integerPattern = /^(?:[1-9]\d*|0)$/;
	if (!integerPattern.test(val)) {
		return _lang.integerAllowed;
	}
}

function validateDecimals(field, rules, i, options) {
	var val = field.val();
	var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
	if (!decimalPattern.test(val)) {
		return _lang.decimalAllowed;
	}
}
