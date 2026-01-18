jQuery(document).ready(function () {
	jQuery('.multi-select', '#userSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
	jQuery("#userSearchFilters").submit();
	customGridToolbarCSSButtons();
	jQuery('.tooltipTable').each(function (index, element) {
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
			interactive: true
		});
	});
});
function advancedSearchFilters() {
	if (!jQuery('#filtersFormWrapper').is(':visible')) {
		loadEventsForFilters();
		jQuery('#filtersFormWrapper').slideDown();
	} else {
		scrollToId('#filtersFormWrapper');
	}
}
function loadEventsForFilters() {
	makeFieldsDatePicker({fields: ['creationDateValue', 'creationDateEndValue']});
	userLookup('createdByValue');
}
function getSearchResults(filtersForm, formData) {
	jQuery.ajax({
		dataType: 'JSON',
		type: 'POST',
		url: getBaseURL('money') + 'users_rate/index',
		data: formData,
		beforeSend: function () {
			jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
		},
		success: function (response) {
			jQuery('#submit, #reset', filtersForm).attr('disabled', 'disabled');
			jQuery('#submit, #reset', filtersForm).removeAttr('disabled');
			jQuery('#userRateGrid').html(response.html);
			scrollToId('#userRateGrid');
		},
		error: defaultAjaxJSONErrorsHandler
	});
}
function searchUsers() {
document.getElementsByName("page").value = 1;
document.getElementsByName("skip").value = 0;
	if (undefined == jQuery('#userRateGrid').data('kendoGrid')) {
		jQuery('#userRateGrid').kendoGrid(contractSearchGridOptions);
		return false;
	}
	jQuery('#userRateGrid').data('kendoGrid').dataSource.page(1);
	return false;
}
function hideAdvancedSearch() {
	jQuery('#filtersFormWrapper').slideUp();
}
function getFormFilters() {
	var filters = '';
	var filtersForm = jQuery('#userSearchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('userSearchFilters', '.', true)
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}

var usersSearchDataSrc = new kendo.data.DataSource({
	transport: {
		read: {
			url: getBaseURL('money') + "users_rate/index",
			dataType: "JSON",
			type: "POST",
			complete: function () {
				if (jQuery('#filtersFormWrapper').is(':visible'))
					jQuery('#filtersFormWrapper').slideUp();
				if (_lang.languageSettings['langDirection'] === 'rtl')
					gridScrollRTL();
				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
			}
		},
		update: {
			url: getBaseURL('money') + "users_rate/rate_edit",
			dataType: "jsonp",
			type: "POST",
			complete: function (XHRObj) {
				var response = jQuery.parseJSON(XHRObj.responseText || "null");
				if (response.result) {
					pinesMessage({ty: 'information', m: _lang.feedback_messages.updateUserRateSuccessfully});
					jQuery('#userRateGrid').data('kendoGrid').dataSource.read();
				} else {
					var errorMsg = '';
					for (i in response.validationErrors) {
						errorMsg += '<li>' + response.validationErrors[i] + '</li>';
					}
					if (errorMsg != '') {
						pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
						jQuery('#userRateGrid').data('kendoGrid').dataSource.read();
					}
				}
				jQuery(':input', jQuery('li.search-field', 'ul.chosen-choices')).removeAttr('disabled');
			}
		},
		parameterMap: function (options, operation) {
			if ("read" == operation) {
				options.filter = getFormFilters();
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
                    id: {
                            editable: false,
                            type: "integer"
                    },
                    ratePerHour: {
                            type: "integer"
                    },
                    yearly_billable_target: {
                            type: "integer"
                    },
                    working_days_per_year: {
                            type: "integer"
                    },
                    userGroupName: {
                            editable: false,
                            type: "string"
                    },
                    providerGroup: {
                            editable: false,
                            type: "integer"
                    },
                    title: {
                            editable: false,
                            type: "string"
                    },
                    firstName: {
                            editable: false,
                            type: "string"
                    },
                    lastName: {
                            editable: false,
                            type: "string"
                    },
                    jobTitle: {
                            editable: false,
                            type: "string"
                    },
                    isLaywer: {
                            editable: false,
                            type: "string"
                    },
                    email: {
                            editable: false,
                            type: "string"
                    },
                    phone: {
                            editable: false,
                            type: "string"
                    },
                    fax: {
                            editable: false,
                            type: "string"
                    },
                    mobile: {
                            editable: false,
                            type: "string"
                    },
                    city: {
                            editable: false,
                            type: "string"
                    },
                    nationality: {
                            editable: false,
                            type: "string"
                    },
                    country: {
                            editable: false,
                            type: "string"
                    },
                    banned: {
                            editable: false,
                            type: "string"
                    },
                    ban_reason: {
                            editable: false,
                            type: "string"
                    },
                    status: {
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
                        row['title'] = escapeHtml(row['title']);
                        row['providerGroup'] = row['providerGroup'];
                        rows.data.push(row);
                    }
                }
                return rows;
            }
	},
	error: function (e) {
		if (e.xhr.responseText.validationErrors == '')
			defaultAjaxJSONErrorsHandler(e.xhr)
	},
	pageSize: 20,
	serverPaging: true,
	serverFiltering: true,
	serverSorting: true
});
var contractSearchGridOptions = {
	autobind: true,
	dataSource: usersSearchDataSrc,
	columns: [
		{
			field: "ratePerHour",
			title: _lang.ratePerHour,
			width: '156px'
		},
		{
			field: "yearly_billable_target",
			title: _lang.yearlyBillableTarget,
			width: '200px'
		},
		{
			field: "working_days_per_year",
			title: _lang.workingDaysPerYear,
			width: '180px'
		},
		{
			field: "id",
			filterable: false,
			title: _lang.id,
			template: '<a href="' + getBaseURL() + 'users/edit/#= id #" title="' + _lang.viewEdit + '">U#= id-1 #</a></li>',
			width: '135px'
		},
		{
			field: "firstName",
			title: _lang.firstName,
			width: '120px'
		},
		{
			field: "lastName",
			title: _lang.lastName,
			width: '120px'
		},
		{
			field: "userGroupName",
			title: _lang.user_group,
			width: '192px'
		},
		{
			field: "providerGroup",
			encoded: false,
			title: _lang.providerGroup,
			width: '192px',
			template: "#= (providerGroup == null) ? '' : providerGroup #"
		},
		{
			field: "title",
			title: _lang.title,
			width: '101px'
		},
		{
			field: "jobTitle",
			title: _lang.position,
			width: '192px'
		},
		{
			field: "isLawyer",
			title: _lang.is_lawyer,
			width: '113px'
		},
		{
			field: "email",
			title: _lang.email,
			width: '192px'
		},
		{
			field: "phone",
			title: _lang.phone,
			width: '120px'
		},
		{
			field: "mobile",
			title: _lang.mobile,
			width: '120px'
		},
		{
			field: "city",
			title: _lang.city,
			width: '120px'
		},
		{
			field: "country",
			title: _lang.country,
			width: '120px'
		},
		{
			field: "nationality",
			title: _lang.nationality,
			width: '120px'
		},
		{
			field: "banned",
			template: "#= (banned == 0) ? 'no' : 'yes' #",
			title: _lang.banned,
			width: '138px'
		},
		{
			field: "ban_reason",
			title: _lang.banReason,
			width: '163px'
		}
	],
	editable: true,
	filterable: false,
	height: 480,
	pageable: {
		input: true,
		messages: _lang.kendo_grid_pageable_messages,
		numeric: false,
		refresh: true,
		pageSizes: [10, 20, 50, 100]
	},
	reorderable: true,
	resizable: true,
	scrollable: true,
	selectable: "single",
	sortable: {
		mode: "multiple"
	},
	columnMenu: {
		messages: _lang.kendo_grid_sortable_messages
	},
	toolbar: [{
			name: "users-grid-toolbar",
			template: '<div class="col-md-3 no-padding col-xs-12">'
					+ '<h4>' + _lang.userRatePerHour +'<span tooltipTitle="'+_lang.userRatePerHourTooltip+'" class="user-rate-per-hour-tooltip tooltipTable"><i class="fa-solid fa-circle-question"></i></span></h4>'
					+ '</div>'
					+ '<div class="col-md-2 advanced-search">'
					+ '<a href="javascript:;" onclick="advancedSearchFilters()">' + _lang.advancedSearch + '</a>'
					+ '</div>'
		},
		{name: "gridToolbarOpen", template: '<div class=" pull-right">'},
		{name: "save", text: _lang.save},
		{name: "cancel", text: _lang.cancel},
		{name: "gridToolbarClose", template: '</div>'}
	]
};
