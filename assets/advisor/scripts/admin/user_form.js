var contactId = 0;

function userFormValidationRules() {
    jQuery("#user-details-form").validationEngine({
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
    jQuery('form#user-details-form').submit();
}
function setCustomerPortalUserSelectedCompany(company) {
    // var container = jQuery('#user-details-form');
    // var theWrapper = jQuery('#selected-companies', container);
    // if (company.id && !jQuery('#customer-portal-user-company' + company.id, theWrapper).length) {
    //     theWrapper.append(jQuery('<div class="row multi-option-selected-items no-margin" id="customer-portal-user-company' + company.id + '"><span id="' + company.id + '">' + company.name + '</span> </div>').append(jQuery('<input type="hidden" value="' + company.id + '" name="companies_customer_portal_users[]" />')).append(jQuery('<input value="x" type="button" class="btn btn-default btn-xs pull-right x-icon" onclick="jQuery(this.parentNode).remove();" />')));
    // }
}

function splitContactEmails(emails) {
    var splitted = emails.split(';');
    var result = [];

    for (email of splitted) {
        result.push(email.trim());
    }

    return result;
}

function getContactId() {
    return jQuery('input[name="contact_id"]', '#user-details-form').val();
}

function addContactEmail() {
    var emailField = jQuery('#contact-email', '#contact-email-form');
    var email = emailField.val();
    var validation = validateEmail(emailField);

    if (validation === true) {
        jQuery.ajax({
            url: getBaseURL() + 'contacts/add_email',
            method: 'post',
            data: {
                contact_id: getContactId(),
                email: email
            },
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    jQuery('#add-contact-email-modal').modal('hide');

                    contact_email = '<option value="' + email + '">' + email + '</option>';
                    jQuery('#email-select', '#user-details-form').append(contact_email).val(email).selectpicker('refresh');
                }
            },
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        pinesMessage({ ty: 'error', m: validation });
    }
}

jQuery(document).ready(function () {
    ctrlS(submitUsersForm);
    userFormValidationRules();

    jQuery("#email-select").selectpicker();

    jQuery('#add-contact-email', '#user-details-form').click(function () {
        jQuery('#add-contact-email-modal').modal('show');
    });

    jQuery("#lookup-companies", '#user-details-form').autocomplete({
        autoFocus: true,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'companies/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    response(jQuery.map(data, function (item) {
                        return {
                            label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                            value: item.name,
                            record: item
                        }
                    }));
                }
            });
        },
        minLength: 3,
        change: function (event, ui) {
            if (ui.item != null) {
                jQuery(this).next('input[name="advisor_company_id"]').val(ui.item.record.id);
            } else if (ui.item == null) {
                jQuery(this).val(null);
                jQuery(this).next('input[name="advisor_company_id"]').val(null);
            }
        },
        select: function (event, ui) {
            if (ui.item != null) {
                jQuery(this).next('input[name="advisor_company_id"]').val(ui.item.record.id);
            } else if (ui.item == null || ui.item.record.id < 1) {
                jQuery(this).val(null);
                jQuery(this).next('input[name="advisor_company_id"]').val(null);
            }
        }
    });

    jQuery("#lookup-contacts", '#user-details-form').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'contacts/autocomplete?extra_data=contactAllCompanies&show_email=true',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
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
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName + (item.email != null ? ' (' + item.email + ')' : ''),
                                value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName + (item.email != null ? ' (' + item.email + ')' : ''),
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery(this).next('input[name="contact_id"]').val(ui.item.record.id);
                jQuery('#firstName', '#user-details-form').val(ui.item.record.firstName);
                jQuery('#lastName', '#user-details-form').val(ui.item.record.lastName);

                // if the contact has semicolon separated emails, split them and display them as a list
                var contact_emails = ui.item.record.email != null && ui.item.record.email.length > 1 ? splitContactEmails(ui.item.record.email) : [];

                if (contact_emails.length > 1 && contact_emails.constructor == Array) {
                    jQuery('#contact-email-input', '#user-details-form').addClass('d-none');

                    var contact_emails_list = '';

                    for (email of contact_emails) {
                        contact_emails_list += '<option value="' + email + '">' + email + '</option>';
                    }

                    jQuery('#email-select', '#user-details-form').html(contact_emails_list).attr('disabled', false).selectpicker('refresh');
                    jQuery('#email', '#user-details-form').val('').attr('disabled', true);
                    jQuery('#contact-email-select', '#user-details-form').removeClass('d-none');
                } else {
                    jQuery('#email', '#user-details-form').val(ui.item.record.email);
                    jQuery('#contact-email-input', '#user-details-form').removeClass('d-none');
                    jQuery('#email-select', '#user-details-form').html(contact_emails_list).attr('disabled', true);
                    jQuery('#email', '#user-details-form').attr('disabled', false);
                    jQuery('#contact-email-select', '#user-details-form').addClass('d-none');
                }

                jQuery('#phone', '#user-details-form').val(ui.item.record.phone);
                jQuery('#mobile', '#user-details-form').val(ui.item.record.mobile);
                jQuery('#jobTitle', '#user-details-form').val(ui.item.record.jobTitle);
                jQuery('#address', '#user-details-form').val(ui.item.record.address1);
                getContactCompanies(ui.item.record.id);
            } else if (ui.item == null || ui.item.record.id < 1) {
                jQuery(this).val(null);
                jQuery(this).next('input[name="contact_id"]').val(null);
            }
        },
        change: function (event, ui) {
            if (ui.item == null) {
                jQuery(this).val(null);
                jQuery(this).next('input[name="contact_id"]').val(null);

                // hide the email select and show the email input
                jQuery('#contact-email-input', '#user-details-form').removeClass('d-none');
                jQuery('#email-select', '#user-details-form').html('').attr('disabled', true);
                jQuery('#email', '#user-details-form').val('').attr('disabled', false);
                jQuery('#contact-email-select', '#user-details-form').addClass('d-none');

                jQuery('#lookup-companies').val(null);
                jQuery('#advisor-company-id').val(null);
            }
        }
    });
});
