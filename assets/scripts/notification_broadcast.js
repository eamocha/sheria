notificationForm = null;
jQuery(document).ready(function(){
	notificationForm = jQuery('#notificationForm');
	notificationUsersLookUp();
	bkLib.onDomLoaded(function() {
		new nicEditor({
			iconsPath : 'assets/jquery/nicEdit/nicEditorIcons.gif',
			buttonList : ['fontSize','bold','italic','underline']
		})
		.panelInstance('message');
        jQuery('.nicEdit-main').attr('dir', 'auto');
    });
});
function notificationUsersLookUp(){
	jQuery('#lookupUsers').autocomplete({
		autoFocus: false,
		delay: 600,
		source: function( request, response ) {
			request.term = request.term.trim();
			jQuery.ajax({
				url: getBaseURL() + 'users/autocomplete/active',
				dataType: "json",
				data: request, error: defaultAjaxJSONErrorsHandler,
				success: function( data ) {
					if(data.length < 1){
						response([{
							label: _lang.no_results_matched_for.sprintf([request.term]),
							value: '',
							record: {
								user_id:-1,
								term: request.term
							}
						}]);
					} else {
						response( jQuery.map( data, function( item ) {
							return {
								label: item.firstName + ' ' + item.lastName,
								value: '',
								record: item
							}
						}));
					}
				}
			});
		},
		minLength: 2,
		select: function( event, ui) {
			if (ui.item.record.id>0){
				setUsersToBox(ui.item.record);
			}
		}
	});
}
function setUsersToBox(record){
	setNewCaseMultiOption(jQuery('#selected_users'), {
		id: record.id,
		value: record.firstName + ' ' + record.lastName,
		name: 'Users'
	});
}
function submitNotificationForm(){
	var message = jQuery('#message', notificationForm);
	message.text(jQuery('.nicEdit-main', notificationForm).html());
	var mes = '';
	if(message.text() == '<br>'){
		mes = _lang.validation_field_required.sprintf([_lang.message])
	}
	mes = (mes == '') ? '' : '<li>' + mes + '.</li>';
	if(jQuery('#selected_users > div.row', notificationForm).length < 1 && !jQuery('#allUsers').is(':checked')){
		mes = mes + '<li>' + _lang.validation_field_required.sprintf([_lang.users])+ '.</li>';
	}
	if(mes != ""){
		pinesMessage({ty: 'warning', m: '<ul>' + mes + '</ul>'});
		jQuery(".form-submit-loader").hide();
		return false;
	}
	return true;
}
function notifyAllUsers(){
	var notificationUsersCheckboxe = jQuery('#allUsers');
	var notificationUsersLookup = jQuery('#lookupUsers');
	if(notificationUsersCheckboxe.is(':checked')){
		notificationUsersLookup.autocomplete( "disable" ).attr('readonly', 'readonly').addClass('ui-state-disabled');
	}else{
		notificationUsersLookup.autocomplete( "enable" ).removeAttr('readonly').removeClass('ui-state-disabled');
	}
	jQuery('#selected_users').html('');
}
