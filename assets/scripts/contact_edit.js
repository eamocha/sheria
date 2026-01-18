var loadingImgHTML = '<div class="row" id="loadingGif" align="center"><img src="assets/images/icons/16/loader-submit.gif" width="24" height="24" /></div>';
function contactContactAutocomplete() {
    jQuery("#lookupContact").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'contacts/autocomplete',
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
                                label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0 && ui.item.record.id != jQuery("#id", '#contactDetailsForm').val())
                window.location = getBaseURL() + 'contacts/edit/' + ui.item.record.id;
        }
    });
}
function addContactAsAdvisor(id){
    window.location = getBaseURL() + 'advisors/user_add?contact-as-advisor=' + id;
}
function contactFormValidationRules() {
    jQuery("#contactDetailsForm").validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomLeft', scroll: false, 'custom_error_messages': {'#firstName': {'required': {'message': _lang.validation_field_required.sprintf([_lang.firstName])}}, '#lastName': {'required': {'message': _lang.validation_field_required.sprintf([_lang.lastName])}}}});
}
function contactNationalitiesLookup() {
    jQuery('#lookupNationalities', '#contactDetailsForm').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({url: getBaseURL() + 'home/load_country_list', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.countryName, value: '', record: item};
                        }));
                    }
                }});
        }, minLength: 2, select: function (event, ui) {
            if (ui.item.record.id > 0) {
                setNewCaseMultiOption(jQuery('#selected_nationalities', '#contactDetailsForm'), {id: ui.item.record.id, value: ui.item.record.countryName, name: 'Contact_Nationalities'});
            }
        }});
    jQuery('#lookupNationalities', '#contactDetailsForm').autocomplete("option", "appendTo", "#contactDetailsForm");
}
jQuery(document).ready(function () {
    if (licenseHasExpired) {
        disableExpiredFields("contactDetailsForm");
        jQuery(':submit').click(function () {
            alertLicenseExpirationMsg();
            return false;
        });
    }
    //ctrlAltC(quickAddCompany);
    contactFormValidationRules();
    var d = new Date();
    makeFieldsDatePicker({fields: ['dateOfBirth'], yearRange: '1900:' + d.getFullYear()});
    contactContactAutocomplete();
    companyAutocompleteMultiOption(jQuery('#lookupCompanies', '#contactDetailsForm'), setSelectedCompany, true, true);
    niceDropDownLists();
    contactNationalitiesLookup();
    copyAddressFromLookup('contactDetailsForm');
    lookUpUsers(jQuery('#manager', jQuery('#contactDetailsForm')), jQuery('#manager-id', jQuery('#contactDetailsForm')), 'manager_id', jQuery('.manager-container', jQuery('#contactDetailsForm')), jQuery('#contactDetailsForm'), {'minLength' : 3});
    jQuery('.tooltip-title', '#contactDetailsForm').tooltipster();
});

function addContactEmail(contactId, emailField){
    var email = emailField.val();
    var validation = validateEmail(emailField);
    jQuery('.footer-bg').attr('style', 'background-color:' + jQuery('.footer').css('background-color') + '!important');
    if (validation === true) {
        jQuery.ajax({
            url: getBaseURL() + 'contacts/add_email',
            method: 'post',
            data: {
                contact_id: contactId,
                email: email
            },
            dataType: "json",
            success: function(response){
                if (response.status) {
                    addContactEmailRow(response.contact_email.id, email, jQuery('#selected-emails'));
                    emailField.val(null);
                    pinesMessageV2({ty: 'success', m: _lang.recordAddedSuccessfully});
                } else {
                    pinesMessageV2({ty: 'error', m: response.error_message});
                }
            },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function(){
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        pinesMessageV2({ty: 'error', m: validation});
    }
}

function addContactEmailRow(contactEmailId, email, container){
    var html = '';

    html += '<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="contact-email' + contactEmailId + '">';
        html += '<span id="' + contactEmailId + '"> <a href="mailto:' + email + '" >' + email + '</a></span>';
        html += '<input type="hidden" name="contact_emails[]" value="' + contactEmailId + '">';
        html += '<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right flex-end-item" tabindex="-1" onclick="deleteContactEmail(' + contactEmailId + ',jQuery(this.parentNode));"><i class="fa-solid fa-xmark"></i></a>';
    html += '</div>';

    container.append(html);
}

function deleteContactEmail(contactId, container){
    var contactEmailId = container.find('input[name="contact_emails[]"]').val();
    var contactEmail = container.find('input[name="contact_emails[]"]').data('email');

    jQuery.ajax({
        url: getBaseURL() + 'contacts/delete_email',
        data: {
            contact_id: contactId,
            contact_email_id: contactEmailId,
            contact_email: contactEmail
        },
        dataType: 'json',
        success: function(response){
            if (response.status) {
                container.remove();
                pinesMessageV2({ty: 'success', m: _lang.deleteRecordSuccessfull});
            } else {
                pinesMessageV2({ty: 'error', m: response.error_message});
            }
        },
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function(){
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteContact(id, step) {
    id = id || 0;
    step = step || "confirm_message";
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url:  getBaseURL() + 'contacts/delete/' + id,
        data: {'step': step},
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery("#contact-delete-dialog").remove();
                if (jQuery('#contact-delete-dialog').length <= 0) {
                    jQuery('<div id="contact-delete-dialog"></div>').appendTo("body");
                    jQuery('#contact-delete-dialog').html(response.html);
                }
                jQuery('.modal', jQuery('#contact-delete-dialog')).modal({
                    keyboard: false,
                    show: true,
                    backdrop: 'static'
                });
            }
            if(response.isAdvisor) {
                pinesMessageV2({ty: 'information', m: response.msg});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function submitThisForm(formID){
    if(formID){
        jQuery('#loader-global').show();
        jQuery("#"+formID).submit();
    }
}