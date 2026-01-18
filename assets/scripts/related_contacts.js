function searchRelatedContacts(){
	var relatedContactsGridId = jQuery('#relatedContactGrid');
	if (undefined == relatedContactsGridId.data('kendoGrid')){
		relatedContactsGridId.kendoGrid(relatedContactsGridOptions);
		return false;
	}
	relatedContactsGridId.data('kendoGrid').dataSource.read();
	return false;
}
function getRelatedContactsFormFilters(){
	var filtersForm = jQuery('#relatedContactsSearchFilters');
	disableEmpty(filtersForm);
	var searchFilters = form2js('relatedContactsSearchFilters', '.', true);
	var filters = '';
	filters = searchFilters.filter;
	enableAll(filtersForm);
	return filters;
}
function relateToContactLookup(){
	jQuery("#relatedContactLookUp", '#relatedContactsForm').autocomplete({
		autoFocus: true,
		delay: 600,
		source: function( request, response ) {
			request.term = request.term.trim();
			jQuery.ajax({
				url: getBaseURL() + 'contacts/autocomplete',
				dataType: "json",
				data: request, error: defaultAjaxJSONErrorsHandler,
				success: function( data ) {
					if(data.length < 1){
						response([{
							label: _lang.no_results_matched.sprintf([request.term]),
							value: '',
							record: {
								id:-1,
								term: request.term
							}
						}]);
					} else {
						response( jQuery.map( data, function( item ) {
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
		response: function(event, ui) {},
		minLength: 3,
		select: function( event, ui) {
			if (ui.item.record.id>0){
					jQuery('#relatedContactId', '#relatedContactsForm').val(ui.item.record.id);
					jQuery('#btnAdd', '#relatedContactsForm').removeAttr('disabled');
				}
		}
	});
}
function addRelatedContact(){
	var newRelatedContact = jQuery('#relatedContactId', '#relatedContactsForm').val();
	var newContactId = jQuery('#filterRelatedContactIdValue', '#relatedContactsSearchFilters').val();
	jQuery.ajax({
		url: getBaseURL() + 'contacts/related_add',
		dataType: "json",
		type: "POST",
		data: {contactId: newContactId, newContactId : newRelatedContact},
		success: function(response) {
			var ty = 'error';
			var m = '';
			switch(response.status){
				case 202:	// saved successfuly
					ty = 'success';
					m = _lang.feedback_messages.addedNewRelatedContactSuccessfully.sprintf([response.contactName, response.newContactName]);
					break;
				case 101:	// could not save form
					m = _lang.feedback_messages.addedNewRelatedContactFailed;
					break;
				case 102:	// could not save contact self
					m = _lang.feedback_messages.addedNewRelatedContactFailedContactSelf.sprintf([response.contactName, response.newContactName])
					break;
				case 103:	// could not save contact already liked
					m = _lang.feedback_messages.addedNewRelatedContactFailedContactExist.sprintf([response.contactName, response.newContactName])
					break;
				default:
					break;
			}
			pinesMessageV2({ty: ty, m: m});
			jQuery('#relatedContactGrid').data('kendoGrid').dataSource.read();
			jQuery('#relatedContactId', '#relatedContactsForm').val('');
			jQuery('#relatedContactLookUp', '#relatedContactsForm').val('');
			jQuery('#btnAdd', '#relatedContactsForm').attr('disabled', 'disabled');
		},error: defaultAjaxJSONErrorsHandler
	});
}
function deleteSelectedRow(id){
	if(confirm(_lang.confirmationDeleteSelectedRecord)){
		jQuery.ajax({
			url: getBaseURL() + 'contacts/related',
			dataType: "json",
			type: "POST",
			data: {
				recordId: id
			},
			success: function(response) {
				var ty = 'error';
				var m = '';
				switch(response.status){
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
				pinesMessageV2({ty: ty, m: m});
				jQuery('#relatedContactGrid').data('kendoGrid').dataSource.read();
			},error: defaultAjaxJSONErrorsHandler
		});
	}
}
try{
	var relatedContactsDataSrc = new kendo.data.DataSource({
		transport: {
			read: {
				url: getBaseURL() + "contacts/related",
				dataType: "JSON",
				type: "POST",
                                complete: function(){
                                    if (_lang.languageSettings['langDirection'] === 'rtl')
                                        gridScrollRTL();
                                }
			},
			update: {
				url: getBaseURL() + "contacts/related_edit",
				dataType: "json",
				type: "POST",
				complete: function(XHRObj){
					var response = jQuery.parseJSON(XHRObj.responseText || "null");
					if(response.result){
						pinesMessageV2({ty:'information', m:_lang.feedback_messages.updatesSavedSuccessfully});
						jQuery('#relatedContactGrid').data('kendoGrid').dataSource.read();
					}else{
						var errorMsg = '';
						for(i in response.validationErrors){
							errorMsg += '<li>' + response.validationErrors[i] + '</li>';
						}
						if (errorMsg != ''){
							pinesMessageV2({ty:'error', m:'<ul>'+errorMsg+'</ul>'});
							jQuery('#relatedContactGrid').data('kendoGrid').dataSource.read();
						}
					}
				}
			},
			parameterMap: function(options, operation){
				if ("read" !== operation && options.models) {
					return {
						models: kendo.stringify(options.models)
					};
				} else {
					options.filter = getRelatedContactsFormFilters();
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
                            contactID: {editable: false, type: "string"},
                            contactName: {editable: false, type: "string"},
                            comments: {type: "string", editable: true},
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
                                row['contactID'] = escapeHtml(row['contactID']);
                                row['comments'] = escapeHtml(row['comments']);
                                rows.data.push(row);
                            }
                        }
                        return rows;
                    }
		},error: function(e) {defaultAjaxJSONErrorsHandler(e.xhr)},
		batch: true, pageSize: 5, serverPaging: true, serverFiltering: true, serverSorting: true
	});
	var relatedContactsGridOptions = {
		autobind: true,
		dataSource: relatedContactsDataSrc,
		columnMenu: {messages: _lang.kendo_grid_sortable_messages},
		columns:[
			{field: "contactID", template: '<a href="' + getBaseURL() + 'contacts/edit/#= contact_id #">#= contactID #</a>',title: _lang.contactID, width: '100px'},
			{field: "contactName", title: _lang.contactName, width: '140px'},
			{field: "comments", title: _lang.comment, width: '200px'},
			{field: "actions", template: '<div class="wraper-actions"><div class="list-of-actions"><a href="javascript:;" onclick="deleteSelectedRow(\'#= id #\')"><i class="fa fa-fw fa-trash"></i></a></div></div>',sortable:false, title: _lang.actions, width: '180px'}
	        

		],
		editable: true,
		filterable: false,
		pageable: {
            messages: _lang.kendo_grid_pageable_messages,  pageSizes: [10, 20, 50, 100], refresh: true,buttonCount:5
        },
		reorderable: true,
		resizable: true,
		scrollable: true,
		sortable:{mode: "multiple"},
                selectable: "single",
		toolbar: [{name: "save", text: _lang.save}, {name: "cancel", text: _lang.cancel}]
	};
} catch (e){
}
jQuery(document).ready(function(){
	try {
		searchRelatedContacts();
		relateToContactLookup();
		customGridToolbarCSSButtons();
		var lastScrollLeft = 0;
	    jQuery(".k-grid-content").scroll(function() {
	        var documentScrollLeft = jQuery(".k-grid-content").scrollLeft();
	        if (lastScrollLeft != documentScrollLeft) {
	            lastScrollLeft = documentScrollLeft;
	            jQuery(".wraper-actions").css("right",-lastScrollLeft)

	            if(isLayoutRTL())
	                jQuery(".wraper-actions").css("left",lastScrollLeft)

	        }
	    });
	} catch (e){
	}
});
