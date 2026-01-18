// function searchRelatedCases() {
// 	var relatedCasesGridId = jQuery('#relatedCaseGrid');
// 	if (undefined == relatedCasesGridId.data('kendoGrid')) {
// 		relatedCasesGridId.kendoGrid(relatedCasesGridOptions);
// 		return false;
// 	}
// 	relatedCasesGridId.data('kendoGrid').dataSource.read();
// 	return false;
// }
function getRelatedCasesFormFilters() {
	var filtersForm = jQuery('#relatedCasesSearchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('relatedCasesSearchFilters', '.', true);
	var filters = '';
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
function relateToCaseLookup() {
	jQuery("#relatedCaseLookUp", '#relatedCasesForm').autocomplete({
		autoFocus: true,
		delay: 600,
		source: function(request, response) {
			request.term = request.term.trim();
                        request.more_filters = {};
                        request.more_filters.caseType = 'ExtractIP';
			jQuery.ajax({
				url: getBaseURL() + 'cases/autocomplete',
				dataType: "json",
				data: request, error: defaultAjaxJSONErrorsHandler,
				success: function(data) {
					if (data.length < 1) {
						response([{
								label: _lang.no_results_matched.sprintf([request.term]),
								value: '',
								record: {
									id: -1,
									term: request.term
								}
							}]);
					} else {
						response(jQuery.map(data, function(item) {
							return {
								label: item.subject,
								value: item.subject,
								record: item
							}
						}));
					}
				}
			});
		},
		response: function(event, ui) {
		},
		minLength: 3,
		select: function(event, ui) {
			if (ui.item.record.id > 0) {
				jQuery('#relatedCaseId', '#relatedCasesForm').val(ui.item.record.id);
				jQuery('#btnAdd', '#relatedCasesForm').removeAttr('disabled');
			}
		}
	});
}
function addRelatedCase() {
	var newRelatedCase = jQuery('#relatedCaseId', '#relatedCasesForm').val();
	var newCaseId = jQuery('#filterRelatedCaseIdValue', '#relatedCasesSearchFilters').val();
	jQuery.ajax({
		url: getBaseURL() + controller + '/related_add',
		dataType: "json",
		type: "POST",
		data: {caseId: newCaseId, newCaseId: newRelatedCase},
		success: function(response) {
			var ty = 'error';
			var m = '';
			switch (response.status) {
				case 202:	// saved successfuly
					ty = 'success';
					m = _lang.feedback_messages.addedNewRelatedCaseSuccessfully.sprintf([newCaseId, newRelatedCase]);
					break;
				case 101:	// could not save form
					m = _lang.feedback_messages.addedNewRelatedCaseFailed;
					break;
				case 102:	// could not save case self
					m = _lang.feedback_messages.addedNewRelatedCaseFailedCaseSelf.sprintf([newCaseId, newRelatedCase])
					break;
				case 103:	// could not save case already liked
					m = _lang.feedback_messages.addedNewRelatedCaseFailedCaseExist.sprintf([newCaseId, newRelatedCase])
					break;
				default:
					break;
			}
			pinesMessage({ty: ty, m: m});
			jQuery('#relatedCaseGrid').data('kendoGrid').dataSource.read();
			jQuery('#relatedCaseId', '#relatedCasesForm').val('');
			jQuery('#relatedCaseLookUp', '#relatedCasesForm').val('');
			jQuery('#btnAdd', '#relatedCasesForm').attr('disabled', 'disabled');
		},error: defaultAjaxJSONErrorsHandler
	});
}
function deleteSelectedRow(id) {
	if (confirm(_lang.confirmationDeleteSelectedRecord)) {
		jQuery.ajax({
			url: getBaseURL() + controller + '/related_delete',
			dataType: "json",
			type: "POST",
			data: {
				recordId: id
			},
			success: function(response) {
				var ty = 'error';
				var m = '';
				switch (response.status) {
					case 202:	// remove successfuly
						ty = 'success';
						m = _lang.relationRemovedSuccessfully;
						break;
					case 101:	// could not delete record
						m = _lang.deleteRecordFailed;
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
				jQuery('#relatedCaseGrid').data('kendoGrid').dataSource.read();
			},error: defaultAjaxJSONErrorsHandler
		});
	}
}

jQuery(document).ready(function() {
	try {
		var relatedCasesDataSrc = new kendo.data.DataSource({
			transport: {
				read: {
					url: getBaseURL() + controller + "/related",
					dataType: "JSON",
					type: "POST",
					complete: function(){
						if (_lang.languageSettings['langDirection'] === 'rtl')
							gridScrollRTL();
					}
				},
				update: {
					url: getBaseURL() +  controller + "/related_edit",
					dataType: "json",
					type: "POST",
					complete: function(XHRObj) {
						var response = jQuery.parseJSON(XHRObj.responseText || "null");
						if (response.result) {
							pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
						} else {
							pinesMessage({ty: 'error', m: '<ul><li>' + response.validationErrors + '</li></ul>'});
						}
						jQuery('#relatedCaseGrid').data('kendoGrid').dataSource.read();
					}
				},
				parameterMap: function(options, operation) {
					if ("read" !== operation && options.models) {
						return {
							models: kendo.stringify(options.models)
						};
					} else {
						options.filter = getRelatedCasesFormFilters();
						options.returnData = 1;
					}
					return options;
				}
			},
			schema: {type: "json", data: "data", total: "totalRows",
                            model: {
                                id: "id",
                                fields: {
                                    id: {editable: false, type: "integer"},
                                    caseID: {editable: false, type: "string"},
                                    companies: {editable: false, type: "string"},
                                    referredBy: {editable: false, type: "string"},
                                    assignedTo: {editable: false, type: "string"},
                                    caseType: {editable: false, type: "string"},
                                    casePriority: {editable: false, type: "string"},
                                    caseStatus: {editable: false, type: "string"},
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
                                        row['companies'] = escapeHtml(row['companies'], ["<br />"]);
                                        row['assignedTo'] = escapeHtml(row['assignedTo']);
                                        rows.data.push(row);
                                    }
                                }
                                return rows;
                            }
			}, error: function(e) {
				defaultAjaxJSONErrorsHandler(e.xhr)
			},
			batch: true, pageSize: 5, serverPaging: true, serverFiltering: true, serverSorting: true
		});

		var relatedCasesGridOptions = {
			autobind: true,
			dataSource: relatedCasesDataSrc,
			columnMenu: {messages: _lang.kendo_grid_sortable_messages},
			columns: [
				{field: "", template:
						'<div class="wraper-actions"><div class="list-of-actions">' +
						'<a class="pull-right" href="javascript:;" onclick="deleteSelectedRow(#= recordId #)" title="' + _lang.deleteRow + '"><i class="fa fa-fw fa-trash light_red-color"></i></a>' +
						'</div></div>',
					sortable: false, title: "", width: '30px'},
				{field: "caseID", template: '<a href="' + getBaseURL() + 'cases/edit/#= case_id #">#= caseID #</a>', title: _lang.caseId, width: '112px'},
				{field: "comments", title: _lang.comment, width: '180px'},
				{encoded: false, field: "companies", title: _lang.companies, width: '155px', template: "#= (companies == null) ? '' : companies #"},
				{field: "referredBy", title: _lang.referred_by, width: '130px'},
				{field: "assignedTo", title: _lang.assignee, width: '140px',template: '#= (assignedTo!=null && assignedToStatus=="Inactive")? assignedTo+" ("+_lang.custom[assignedToStatus]+")":((assignedTo!=null)?assignedTo:"") #'},
				{field: "caseType", title: _lang.caseType, width: '143px'},
				{field: "casePriority", title: _lang.casePriority, width: '140px'},
				{field: "caseStatus", title: _lang.case_status, width: '160px'}
			],
			editable: true,
			filterable: false,
			pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [5, 10, 20, 50, 100], refresh: true},
			reorderable: true,
			resizable: true,
			scrollable: true,
			sortable: {mode: "multiple"},
					selectable: "single",
			toolbar: [{name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
		};
		var relatedCasesGridId = jQuery('#relatedCaseGrid');
		if (undefined == relatedCasesGridId.data('kendoGrid')) {
			relatedCasesGridId.kendoGrid(relatedCasesGridOptions);
		}
		relateToCaseLookup();
		customGridToolbarCSSButtons();
	} catch (e) {
	}
});
