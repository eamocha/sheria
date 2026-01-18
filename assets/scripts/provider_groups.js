/**
 * userUserAutocomplete fuunction
 * get searched users
 */
function userUserAutocomplete() {
    jQuery("#lookupUser").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'users/autocomplete?join[]=user_groups',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched_for.sprintf([request.term]),
                                value: '',
                                record: {
                                    id: -1
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.firstName + ' ' + item.lastName,
                                value: "",
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 1,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                setSelectedUserAssigned(ui.item.record);
            }
        }
    });

    /**
     * This could be a global solution for the autocomplete dropdownlist width issue
     */
    jQuery.ui.autocomplete.prototype._resizeMenu = function () {
        var ul = this.menu.element;
        ul.outerWidth(this.element.outerWidth()).css({'max-width': 'none', 'border-radius': '4px'});
    }
}
/**
 * set Assigned user
 *  from get function
 */
function setSelectedUserAssigned(users) {
    var theWrapper = jQuery('#selected_users');
    if(!jQuery('#user_group_' + users.user_group_id, theWrapper).length){
        theWrapper.append("<div id='user_group_" + users.user_group_id +"' ><label>"+users.user_groups_name +"</label></div>");
        if (users.id && !jQuery('#user_group_'+users.user_group_id+'  #user_assigned_' + parseInt(users.id), theWrapper).length) {
            jQuery('#user_group_' + users.user_group_id, theWrapper).append(
                    jQuery('<div class="row multi-option-selected-items no-margin" id="user_assigned_' + parseInt(users.id) + '"><span id="' + parseInt(users.id) + '">' + users.firstName + ' ' + users.lastName + ' ('+ users.status +')' +'</span> </div>')
                    .append(jQuery('<input type="hidden" value="' + parseInt(users.id) + '" name="user_assigned[]" />'))
                    .append('<input value="x" onclick=deleteItem("'+users.user_group_id+'","'+parseInt(users.id)+'") type="button" class="btn btn-default btn-xs pull-right" />')
                    ); 
        }
    }else{
        if (users.id && !jQuery('#user_group_'+users.user_group_id+'  #user_assigned_' + parseInt(users.id)).length) {
            jQuery('#user_group_' + users.user_group_id, theWrapper).append(
                    jQuery('<div class="row multi-option-selected-items no-margin" id="user_assigned_' + parseInt(users.id) + '"><span id="' + parseInt(users.id) + '">' + users.firstName + ' ' + users.lastName + ' ('+ users.status +')' +'</span> </div>')
                    .append(jQuery('<input type="hidden" value="' + parseInt(users.id) + '" name="user_assigned[]" />'))
                    .append('<input value="x" onclick=deleteItem("'+users.user_group_id+'","'+parseInt(users.id)+'") type="button" class="btn btn-default btn-xs pull-right" />')
                    ); 
        }
    }
   
    
}
/**
 * delete item from list
 * @param {int} user_group_id 
 * @param {int} user_id 
 */
function deleteItem(user_group_id,user_id){
    jQuery('#user_group_'+user_group_id+' #user_assigned_' + user_id).remove();
    if (!jQuery('#user_group_'+user_group_id+' .multi-option-selected-items').length) {
        jQuery('#user_group_'+user_group_id).remove();
    }
}
jQuery(document).ready(function () {
    userUserAutocomplete();
    var allUsers = jQuery('#all-users');
    allUsers.on('click',function () {
        if (allUsers.is(':checked')) {
            jQuery(".all_selected_users").addClass("d-none");
        } else {
            jQuery(".all_selected_users").removeClass("hide");
        }
    });
});