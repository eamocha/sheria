function onChangeMainClassificationReloadSubClassification(mainValue, selectedVal){
	selectedVal = selectedVal || '';
	jQuery.ajax({
		url:getBaseURL()+'cases/urls',
		type:'POST',
		dataType:'JSON',
		data:{
			mainClassificationId: mainValue
		},
		success:function(response){
			var newOptions = '<option value="">' + _lang.chooseSubClassification + '</option>';
			for(i in response.subList){
				newOptions += '<option value="' + i  +  '">' + response.subList[i] + '</option>';
			}
			jQuery('#sub_case_document_classification_id', '#archivedHardCopiesForm').html(newOptions).val(selectedVal);
		},
		error: defaultAjaxJSONErrorsHandler
	});
}
function saveArchivedHardCopies(){
	var formData = form2js('archivedHardCopiesForm', '.', true);
	var rowContent = '<td id="classification">%s&nbsp;</td>'+
	'<td id="subClassification">%s&nbsp;</td>'+
	'<td style="height: auto;" id="notes">%s&nbsp;</td>'+
	'<td class="actions"><a href="javascript:;" onclick="editArchivedHardCopy({id: \'%s\', classId: \'%s\', subClassId: \'%s\'})" class="icon edit-icon" title="%s"></a>&nbsp;'+
	'<a href="javascript:;" onclick="removeArchivedHardCopy(\'%s\')" title="%s"><i class="fa-solid fa-trash-can red"></i></a>'+
	'</td>';
	var rowHTML = '<tr id="archivedRecord_%s">' + rowContent + '</tr>';

	//
	//
	if (jQuery("form#archivedHardCopiesForm", '#archivedHardCopiesContainer').validationEngine('validate')){
		jQuery.ajax({
			url:getBaseURL()+'cases/urls',
			type:'POST',
			dataType:'JSON',
			data:{
				archivedHardCopiesFormData : formData
			},
			success:function(response){
				if(response.errors){
					var errorMsg = '';
					for(i in response.errors){
						jQuery('#' + i, '#archivedHardCopiesForm').addClass('invalid').focus(function(){
							jQuery(this).removeClass('invalid');
						});
						errorMsg += '<li>' + response.errors[i] + '</li>';
					}
					if (errorMsg != ''){
						pinesMessage({ty:'error', m:'<ul>'+errorMsg+'</ul>'});
					}
				} else if(response.result){
					if (formData.archivedId){
						//record updated
						var formElement = jQuery('#archivedHardCopiesForm');
						var row = jQuery('#archivedRecord_'+formData.archivedId);
						var classText = jQuery('option:selected', jQuery('#case_document_classification_id', formElement)).text();
						var subClassText = jQuery('option:selected', jQuery('#sub_case_document_classification_id', formElement)).text();
						formData.notes = formData.notes || '  ';
						row.html(rowContent.sprintf([
							classText,
							subClassText,
							formData.notes,
							formData.archivedId,
							formData.case_document_classification_id,
							formData.sub_case_document_classification_id,
							_lang.edit,
							formData.archivedId,
							_lang.remove
							]));
					} else {
						//new record added
						var formElement = jQuery('#archivedHardCopiesForm');
						var row = jQuery('#archivedRecord_'+formData.archivedId);
						var classText = jQuery('option:selected', jQuery('#case_document_classification_id', formElement)).text();
						var subClassText = jQuery('option:selected', jQuery('#sub_case_document_classification_id', formElement)).text();
						formData.notes = formData.notes || '  ';
						jQuery('#archivedHardCopiesRows', '#archivedHardCopiesContainer').append(rowHTML.sprintf([
							response.record.id,
							classText,
							subClassText,
							formData.notes,
							response.record.id,
							formData.case_document_classification_id,
							formData.sub_case_document_classification_id,
							_lang.edit,
							response.record.id,
							_lang.remove
							]));
					}
					jQuery('#inputsForm', '#archivedHardCopiesForm').addClass('d-none');
					resetAndToggleArchivedHardCopiesForm();
				}
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function resetAndToggleArchivedHardCopiesForm(){
	jQuery('form#archivedHardCopiesForm')[0].reset();
	jQuery('form#archivedHardCopiesForm').validationEngine('hide');
	jQuery('#archivedId', 'form#archivedHardCopiesForm').val('');
}
function editArchivedHardCopy(data){
	if(!jQuery('#inputsForm', '#archivedHardCopiesForm').is(':visible')){
		jQuery('#inputsForm', '#archivedHardCopiesForm').removeClass('d-none');
	}
	resetAndToggleArchivedHardCopiesForm();
	jQuery('#archivedId', 'form#archivedHardCopiesForm').val(data.id);
	jQuery('#case_document_classification_id', 'form#archivedHardCopiesForm').val(data.classId);
	onChangeMainClassificationReloadSubClassification(data.classId, data.subClassId);
	jQuery('#notes', '#inputsForm').val( jQuery('td#notes', jQuery('#archivedRecord_'+data.id, '#archivedHardCopiesRows')).text() );

}
function addArchivedHardCopiesClick(){

     jQuery('#inputsForm').removeClass('d-none');
	resetAndToggleArchivedHardCopiesForm();
}
function removeArchivedHardCopy(id){
	if(confirm(_lang.confirmationDeleteSelectedRecord)){
		jQuery.ajax({
			url:getBaseURL()+'cases/urls',
			type:'POST',
			dataType:'JSON',
			data:{
				archivedHardCopyId: id
			},
			success:function(response){
				var ty = 'error';
				var m = '';
				switch(response.status){
					case 202:	// removed successfuly
						ty = 'information';
						m = _lang.selectedRecordDeleted;
						jQuery('tr#archivedRecord_' + id, '#archivedHardCopiesRows').remove();
						break;
					case 101:	// could not remove record
						m = _lang.recordNotDeleted;
						break;
					default:
						break;
				}
				pinesMessage({ty: ty, m: m});
			},
			error: defaultAjaxJSONErrorsHandler
		});
	}
}
function clickArchivedHardCopyLink(){
	if(jQuery('#archivedHardCopiesContainer').is(':visible')){
		jQuery('#archivedHardCopiesContainer').slideUp();
		jQuery('i','#ArchivedHardCopyLink a:first').removeClass('fa-solid fa-angle-down');
		jQuery('i','#ArchivedHardCopyLink a:first').addClass('fa-solid fa-angle-right');
	}else{
		jQuery('#archivedHardCopiesContainer').slideDown();
		jQuery('i','#ArchivedHardCopyLink a:first').removeClass('fa-solid fa-angle-right');
		jQuery('i','#ArchivedHardCopyLink a:first').addClass('fa-solid fa-angle-down');
	}
	jQuery("#archivedHardCopiesForm", '#archivedHardCopiesContainer').validationEngine({
		validationEventTrigger :"submit",
		autoPositionUpdate: true,
		promptPosition:'bottomLeft',
		scroll: false,
		'custom_error_messages': {
			'#case_document_classification_id' : {
				'required': {
					'message': _lang.validation_field_required.sprintf([_lang.caseDocumentClassification])
				}
			},
			'#sub_case_document_classification_id' : {
				'required': {
					'message': _lang.validation_field_required.sprintf([_lang.subCaseDocumentClassification])
				}
			}
		}
	});
}
