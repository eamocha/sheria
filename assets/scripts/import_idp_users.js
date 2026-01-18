let importIdpUsers = (function() {
    'use strict';

    function lookUpIdpUsers() {
        let userContainer = jQuery('#idp-user-lookup-container');
        let lookupDetails = { 'lookupField': jQuery('#lookup-users', userContainer), 'lookupContainer': 'idp-user-lookup-container', 'errorDiv': 'lookupUsers', 'boxName': 'users', 'boxId': '#selected-users', 'resultHandler': userSelectingCallback };
        _lookUpIdpUsers(lookupDetails, userContainer, true);
    }

    /*
     * Lookup for users from idp graph api
     * Retrieving users depending on the term entered with 3 characters and above
     * @param array lookupDetails( details for the lookup input),string container(jQuery selector of modal container),boolean isBoxContainer(whether the lookup field will be set in a box or input)
     */
    function _lookUpIdpUsers(lookupDetails, container, isBoxContainer, moreFilters) {
        moreFilters = moreFilters || false;
        isBoxContainer = isBoxContainer || false;
        //Instantiate the Bloodhound suggestion engine
        let mySuggestion = new Bloodhound({
            datumTokenizer: function(datum) {
                return Bloodhound.tokenizers.whitespace('');
            },
            queryTokenizer: Bloodhound.tokenizers.whitespace,
            remote: {
                url: getBaseURL() + 'saml_sso/lookup_users?term=%QUERY',
                replace: function(url, uriEncodedQuery) {
                    return url.replace('%QUERY', uriEncodedQuery);
                },
                filter: function(data) {
                    return data;
                },
                'cache': false,
                wildcard: '%QUERY'
            }
        });
        //Initialize the Bloodhound suggestion engine
        mySuggestion.initialize();
        //Instantiate the Typeahead UI
        jQuery(lookupDetails['lookupField']).typeahead({
            hint: false,
            highlight: true,
            minLength: 3
        }, {
            source: mySuggestion.ttAdapter(),
            display: function(item) {
                if (!isBoxContainer) {
                    return item.first_name + " " + item.last_name + " (" + item.email + ")";
                }
            },
            templates: {
                empty: ['<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                suggestion: function(data) {
                    if (typeof(data.error) !== 'undefined') {
                        !isNaN(data.error) ? pinesMessageV2({ ty: 'error', m: _lang.import_azure_error.sprintf([data.error]), d: 5000 }) :
                            pinesMessageV2({ ty: 'error', m: data.error });
                    }

                    return typeof(data.email) !== 'undefined' ? "<div>" + (data.first_name + " " + data.last_name + " (" + data.email + ")") + '</div>' : "<div>" + _lang.noMatchesFound + '</div>';
                }
            }
        }).on('typeahead:selected', function(obj, datum) {
            if (isBoxContainer) {
                jQuery("div", jQuery('.' + lookupDetails['lookupContainer'], container)).find("[data-field=" + lookupDetails['errorDiv'] + "]").addClass('d-none');
                lookupBoxContainerDesign(jQuery('.' + lookupDetails['lookupContainer'], container));
                setNewBoxElement(lookupDetails['boxId'], lookupDetails['lookupContainer'], '#' + container.attr('id'), { id: datum.id, value: datum.first_name + " " + datum.last_name + " (" + datum.email + ")", name: lookupDetails['boxName'], isIdp: true, email: datum.email, firstName: datum.first_name, lastName: datum.last_name });
            }
        }).on('typeahead:asyncrequest', function() {
            jQuery('.loader-submit', container).addClass('loading');
        }).on('typeahead:asynccancel typeahead:asyncreceive', function(obj, datum) {
            if (datum === undefined) {
                //number of dialogs allowed to open is 2(if dialogs count is less than 2,user can open another dialog else user will not have the permission to open a new dialog)
                if (countDialog('contact-dialog-') < 2) {
                    jQuery('.empty', container).html(_lang.noMatchesFound.sprintf([lookupDetails['lookupField'].val()]));

                } else {
                    jQuery('.empty', container).html(_lang.no_results_matched).removeClass('click').attr('onClick', '');
                }
            }
            if (obj.currentTarget['value'] == '' && datum == undefined && moreFilters) {
                //if searching for all result(no term sent) and the response is undefined and moreFilters array is defined
                // then change the response message of the lookup
                jQuery('.empty', '#' + obj.currentTarget['form']['id']).html(moreFilters['messageDisplayed']);
            }
            jQuery('.loader-submit', container).removeClass('loading');
        }).on('focus', function() {
            highLightFirstSuggestion();
        });
    }

    let userSelectingCallback = function _userSelectingCallback(response) {};

    function importIdpUsers() {
        let userObj = [];
        jQuery(".idp-users", jQuery("#import-idp-container")).each(function() {
            userObj.push({ email: jQuery(this).data('email'), first_name: jQuery(this).data('firstname'), last_name: jQuery(this).data('lastname'), })
        });
        if (userObj.length == 0) {
            pinesMessageV2({ ty: 'error', m: _lang.chooseUsers })
            return;
        }
        if (jQuery("#user_group_id", jQuery("#import-idp-container")).val() === "") {
            pinesMessageV2({ ty: 'error', m: _lang.chooseUserGroup })
            return;
        }
        jQuery('#loader-global').show();
        jQuery.ajax({

            data: { 'users': userObj, 'users_group': jQuery("#user_group_id", jQuery("#import-idp-container")).val() , 'type': jQuery("#access-type", jQuery("#import-idp-container")).val() },
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'saml_sso/import_users',
            success: function(response) {
                jQuery('#loader-global').hide();
                if (response.error)
                    pinesMessageV2({ ty: 'error', m: response.message })
                else if (response.validation_errors.length > 0) {
                    var valid_errors = response.validation_errors.join('\n');
                    pinesMessageV2({ ty: 'warning', m: valid_errors })
                } else {
                    pinesMessageV2({ ty: 'success', m: response.message });
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function importIdpCPUsers() {
        let userObj = [];
        jQuery(".idp-users", jQuery("#import-idp-container")).each(function() {
            userObj.push({ email: jQuery(this).data('email'), first_name: jQuery(this).data('firstname'), last_name: jQuery(this).data('lastname'), })
        });
        if (userObj.length == 0) {
            pinesMessageV2({ ty: 'error', m: _lang.chooseUsers })
            return;
        }
        if (jQuery("#user_group_id", jQuery("#import-idp-container")).val() === "") {
            pinesMessageV2({ ty: 'error', m: _lang.chooseUserGroup })
            return;
        }
        jQuery('#loader-global').show();
        jQuery.ajax({
            data: { 'users': userObj, 'form_data': jQuery("#search-portal-users-idp").serialize() },
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'saml_sso/import_cp_users',
            success: function(response) {
                jQuery('#loader-global').hide();
                console.log(response);
                if (response.error)
                    pinesMessageV2({ ty: 'error', m: response.message })
                else
                    pinesMessageV2({ ty: 'success', m: response.message });
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    return {
        lookUpIdpUsers: lookUpIdpUsers,
        importIdpUsers: importIdpUsers,
        importIdpCPUsers: importIdpCPUsers
    };
}());

jQuery(document).ready(function() {
    importIdpUsers.lookUpIdpUsers();
});