function userFormValidationRules() {
    jQuery("#userDetailsForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false,
        'custom_error_messages': {
            '#firstName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.firstName])
                }
            },
            '#lastName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.lastName])
                }
            },
            '#confirmPassword': {
                'condRequired': {
                    'message': _lang.validation_field_required.sprintf([_lang.passwordConfirmation])
                },
                'equals': {
                    'message': _lang.validation_field_passwords_not_match
                }
            }
        }
    });
}
function submitUsersForm() {
    jQuery('form#userDetailsForm').submit();
}
function setCustomerPortalUserSelectedCompany(company) {
    var container = jQuery('#userDetailsForm');
    var theWrapper = jQuery('#selected-companies', container);
    if (company.id && !jQuery('#customer-portal-user-company' + company.id, theWrapper).length) {
        theWrapper.append(jQuery('<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="customer-portal-user-company' + company.id + '"><span id="' + company.id + '">' + company.name + '</span>').append(jQuery('<input type="hidden" value="' + company.id + '" name="companies_customer_portal_users[]" />')).append(jQuery('<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button remove-button-event flex-end-item" tabindex="-1" onclick="jQuery(this.parentNode).remove();" ><i class="fa fa-remove"></i></a></div> ')));
    }
}
jQuery(document).ready(function () {
    ctrlS(submitUsersForm);
    userFormValidationRules();
    companyAutocompleteMultiOption(jQuery('#lookup-companies', '#userDetailsForm'), setCustomerPortalUserSelectedCompany, false, true);
    var userContainer = jQuery('#userDetailsForm');
    var lookupDetails = {'lookupField': jQuery('#lookup-contacts', userContainer), 'errorDiv': 'contact_id', 'hiddenId': '#lookup-contact-id', 'resultHandler': 'reminderContactResultHandler'};
    var moreFilters = {'keyName': 'excludeCustomersExceptCurrentCustomer', 'value': jQuery('#lookup-contact-id', userContainer).val()};
    lookUpContacts(lookupDetails, userContainer, false, moreFilters);
});
