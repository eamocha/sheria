function membersList(memberTypeSelected, memberSelected){
	memberTypeSelected = memberTypeSelected || jQuery("select#memberType", '#boardMemberFinderForm').val();
	memberSelected = memberSelected || '';
	jQuery.ajax({
		url: getBaseURL() + 'reports/board_member_finder',
		data: {memberType: memberTypeSelected},
		type:'POST',
		dataType:'JSON',
		success: function( response ) {
			var attr = ' ';
			var newOptions = '<option value="">' + _lang.chooseMember + '</option>';
			for(i in response.records){
				if(i == memberSelected)
					attr = 'selected="selected"';
				else
					attr = ' ';
				newOptions += '<option value="' + i  +  '"' + attr  + '>' + response.records[i] + '</option>';
			}
			jQuery('#member', '#boardMemberFinderForm').html(newOptions).trigger("chosen:updated");
		},
		error: defaultAjaxJSONErrorsHandler
	});
}
jQuery(document).ready(function(){
	jQuery("#memberType", '#boardMemberFinderForm').change(function(){
		membersList();
	});
	var memberType = jQuery('#memberType', '#boardMemberFinderForm').val();
	jQuery("#member", '#boardMemberFinderForm').chosen({
		no_results_text: _lang.no_results_matched, 
		placeholder_text:  _lang.chooseMember,
                width:'100%'
	}).change(function(){
		window.location=getBaseURL()+'reports/board_member_finder/' + jQuery('#memberType', '#boardMemberFinderForm').val() + '/' + jQuery('#member', '#boardMemberFinderForm').val();
	});
});