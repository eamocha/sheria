var step;
function activateTabs() {
    jQuery('#contracts-table').DataTable({
        /* Disable initial sort */
        "aaSorting": []
    });
    setActiveTab('contracts');
}

function contractGenerate(module) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL(module) + 'contracts/add',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#contract-generate-container').length <= 0) {
                        jQuery('<div id="contract-generate-container" class="primary-style"></div>').appendTo("body");
                        var contractGenerateContainer = jQuery('#contract-generate-container');
                        contractGenerateContainer.html(response.html);
                        initializeModalSize(contractGenerateContainer);
                        commonModalDialogEvents(contractGenerateContainer);
                        contractGenerateEvents(module, contractGenerateContainer);
                    }
                }
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractGenerateEvents(module, container) {
    var currentForm, currentFs, nextForm, nextFs, previousFs, screenId;
    var $continue, option;
    var nextStep, prevStep, pagesNb, currentPageNb, previousPageNb = 0;
    jQuery(".next").click(function () {
        // alert(step);
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        $continue = true;
        if (currentForm) {
            // alert(step);
            switch (true) {
                case step == 1:
                    jQuery('.inline-error', currentFs).addClass('d-none').html('');
                    if ('undefined' == typeof option || (option && (option !== jQuery('#option', '#form1').val() || (option == 'add' && jQuery('#contract-add-container', container).length < 1) || (option == 'choose' && jQuery('#contract-template-container', container).length < 1)))) {
                        // send ajax request else only move to the next page and show the form
                        option = jQuery('#option', currentForm).val();
                        $continue = false;
                        if (!jQuery('#option', currentForm).val()) {
                            jQuery("[data-field='generate_contract']", currentFs).removeClass('d-none').html(_lang.feedback_messages.optionRequired);
                        } else {
                            nextStep = (step + 1);
                            nextFs = jQuery('#fieldset' + nextStep, container);
                            nextForm = jQuery('#form' + nextStep, nextFs);
                            nextForm.html('');
                            jQuery.ajax({
                                dataType: 'JSON',
                                url: getBaseURL(module) + 'contracts/add',
                                type: 'GET',
                                data: {option: option, step: step},
                                beforeSend: function () {
                                    jQuery('#loader-global').show();
                                },
                                success: function (response) {
                                    if (response.result) {
                                        if (response.html) {
                                            $continue = true;
                                            nextForm.html(response.html);
                                            switch (option) {
                                                case 'choose':
                                                    jQuery('.select-picker', container).selectpicker({dropupAuto: false});
                                                    jQuery('.select-picker', container).selectpicker({dropupAuto: false});
                                                    jQuery('#type', container).change(function () {
                                                        if(jQuery(this).val()) {
                                                            contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
                                                            contractTemplateEvent(jQuery(this).val(), jQuery("#sub-type").val(), jQuery("#templates", container));
                                                        }
                                                    });
                                                    jQuery('#sub-type', container).change(function () {
                                                        contractTemplateEvent(jQuery("#type", container).val(), jQuery(this).val(), jQuery("#templates", container));
                                                    });
                                                    break;
                                                default:
                                                    break;
                                            }

                                            if ($continue) {
                                                //activate next step on progressbar using the index of nextFs
                                                jQuery('.modal-footer').removeClass('d-none');
                                                jQuery('.modal-footer', container).attr('data-field', nextStep);
                                                jQuery('.modal-body', container).removeClass('first-step');
                                                jQuery('.modal-body', container).addClass('second-step');
                                                jQuery('.previous', jQuery('.modal-footer', container)).removeClass('d-none');
                                                currentFs.hide();
                                                nextFs.show();

                                            }
                                        }
                                    } else {
                                        $continue = false;
                                        pinesMessage({ty: 'warning', m: response.display_message});
                                    }


                                }, complete: function () {
                                    jQuery('#loader-global').hide();
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    } else {
                        nextStep = (step + 1);
                        nextFs = jQuery('#fieldset' + nextStep, container);
                        nextForm = jQuery('#form' + nextStep, nextFs);
                    }
                    if ($continue) {
                        if (option == 'add') {
                            jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
                        }
                        jQuery('.modal-footer', container).removeClass('d-none');
                        jQuery('.modal-footer', container).attr('data-field', nextStep);
                        jQuery('.modal-body', container).removeClass('first-step');
                        jQuery('.previous', jQuery('.modal-footer', container)).removeClass('d-none');
                        currentFs.hide();
                        nextFs.show();
                    }
                    break;
                case step == 2:
                    jQuery('.inline-error', currentFs).addClass('d-none').html('');
                    if (option == 'choose') {
                        if (!jQuery('#templates', currentForm).val()) {
                            jQuery("[data-field='templates']", currentFs).removeClass('d-none').html(_lang.feedback_messages.templateRequired);
                            $continue = false;
                        }
                        if ($continue) {
                            jQuery('#doc-name', currentForm).val(jQuery('#doc-name-preffix', currentForm).val() + jQuery('#doc-name-suffix', currentForm).text());
                            nextStep = (step + 1);
                            nextFs = jQuery('#fieldset' + nextStep, container);
                            nextForm = jQuery('#form' + nextStep, nextFs);
                            if ('undefined' == typeof option || (option && option !== jQuery('#option', '#form1').val()) || (option && (previousPageNb == 0 || !previousPageNb))) {
                                jQuery.ajax({
                                    url: getBaseURL(module) + 'contracts/add',
                                    dataType: 'JSON',
                                    type: 'GET',
                                    data: {
                                        option: option,
                                        step: step,
                                        template_id: jQuery('#templates', currentForm).val()
                                    },
                                    beforeSend: function () {
                                        jQuery('#loader-global').show();
                                    },
                                    success: function (response) {
                                        if (response.result) {
                                            nextForm.html(response.html);
                                            currentPageNb = 1;
                                            pagesNb = response.pages;
                                            questionnaireFormEvents(pagesNb, currentFs, nextForm, nextStep, module, container);
                                        } else {
                                            $continue = false;
                                            pinesMessage({ty: 'warning', m: response.display_message});
                                        }

                                    }, complete: function () {
                                        jQuery('#loader-global').hide();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        }
                    }
                    if (option == 'add') {
                        if (!jQuery('input[type=radio]', currentForm).is(':checked')) {
                            jQuery("[data-field='screens']", currentFs).removeClass('d-none').html(_lang.feedback_messages.screenRequired);
                            $continue = false;
                        }
                        if ($continue) {
                            nextStep = (step + 1);
                            nextFs = jQuery('#fieldset' + nextStep, container);
                            nextForm = jQuery('#form' + nextStep, nextFs);
                            screenId = jQuery('input[type=radio]:checked', currentForm).val();
                            if ('undefined' == typeof option || (option && option !== jQuery('#option', '#form1').val()) || (option && jQuery('#contract-screens-container', nextForm).length < 1)) {
                                jQuery.ajax({
                                    url: getBaseURL(module) + 'contracts/add',
                                    dataType: 'JSON',
                                    type: 'GET',
                                    data: {
                                        option: option,
                                        step: step,
                                        screen_id: screenId
                                    },
                                    beforeSend: function () {
                                        jQuery('#loader-global').show();
                                    },
                                    success: function (response) {
                                        if (response.result) {
                                            nextForm.html(response.html);
                                                jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
                                                jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
                                                jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');

                                            jQuery('.modal-footer', container).attr('data-field', nextStep);
                                            currentFs.hide();
                                            nextFs.show();
                                            screenFormEvents(container);
                                        } else {
                                            $continue = false;
                                            pinesMessage({ty: 'warning', m: response.display_message});
                                        }

                                    }, complete: function () {
                                        jQuery('#loader-global').hide();
                                    },
                                    error: defaultAjaxJSONErrorsHandler
                                });
                            }
                        }
                    }
                    break;
                case (step > 2):
                    jQuery('.inline-error', currentForm).addClass('d-none');

                    nextPageNb = parseInt(currentPageNb) + 1;
                    nextStep = (step + 1);
                    if (contractGenerateEventCheckRequired(jQuery("[page-number=" + currentPageNb + "]", container))) {
                        jQuery('#current-page').html(nextPageNb);
                        jQuery('.modal-footer', container).attr('data-field', nextStep);

                        lastProgress = jQuery('.progress-bar', container).attr('progress');
                        newProgress = parseInt(lastProgress) + parseInt(progressPerc);

                        jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);

                        jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
                        currentPageNb = nextPageNb;
                        jQuery("[page-number=" + nextPageNb + "]", container).removeClass('d-none');
                        if (nextPageNb == pagesNb) {
                            jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
                            jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
                        }
                    }


                    break;
            }
        }

    });
    jQuery(".previous").click(function () {
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        prevStep = (step - 1);
        previousFs = jQuery('#fieldset' + prevStep, container);
        previousPageNb = parseInt(currentPageNb) - 1;
        //moving back between template pages
        if (option == 'choose' && (step > 2) && (previousPageNb > 0)) {
            previousStep = (step - 1);
            jQuery('#current-page').html(previousPageNb);
            jQuery('.modal-footer', container).attr('data-field', previousStep);

            lastProgress = jQuery('.progress-bar', container).attr('progress');
            newProgress = parseInt(lastProgress) - parseInt(progressPerc);

            jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);
            jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
            currentPageNb = previousPageNb;
            jQuery("[page-number=" + previousPageNb + "]", container).removeClass('d-none');
            jQuery('#form-submit', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('#notification-div', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('.next', jQuery('.modal-footer', container)).removeClass('d-none');
        } else {
            currentFs.hide();
            previousFs.show();
            jQuery('.modal-footer', container).attr('data-field', prevStep);
            jQuery('#form-submit', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('#notification-div', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('.next', jQuery('.modal-footer', container)).removeClass('d-none');
            if (prevStep === 1) {
                jQuery('.modal-body', container).addClass('first-step');
                jQuery('.modal-body', container).removeClass('second-step');
                jQuery('.modal-footer').addClass('d-none');
            }
            if (previousPageNb == 0) {
                jQuery("#progress-bar", container).addClass("d-none");
                jQuery(".modal-header", container).removeClass("progress-padding");
            }
        }
    });
    jQuery("#form-submit").click(function () {
        step = parseInt(jQuery('.modal-footer', container).attr('data-field'));
        currentFs = jQuery('#fieldset' + step, container);
        currentForm = jQuery('form', currentFs);
        if (option == 'add') {
            var formData = new FormData(document.getElementById('form' + step));
            formData.append('option', option);
            formData.append('step', step);
            formData.append('screen_id', screenId);
            //save the contract
            jQuery.ajax({
                url: getBaseURL(module) + 'contracts/add',
                dataType: 'JSON',
                type: 'POST',
                contentType: false, // required to be disabled
                cache: false,
                processData: false,
                data: formData,
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    jQuery('.inline-error', currentForm).addClass('d-none');
                    if (response.result) {
                        window.location.href = getBaseURL(module) + 'contracts/view/' + response.id;
                    } else {
                        displayValidationErrors(response.validationErrors, currentForm);
                    }
                }, complete: function () {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
        if (option == 'choose') {
            if (contractGenerateEventCheckRequired(jQuery("[page-number=" + currentPageNb + "]", container))) {
                var formData = jQuery("#form1, #form2, #form3").serialize();
                //save the contract
                jQuery.ajax({
                    url: getBaseURL(module) + 'contracts/add',
                    dataType: 'JSON',
                    type: 'POST',
                    data: formData,
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    success: function (response) {
                        jQuery('.inline-error', currentForm).addClass('d-none');
                        if (response.result) {
                            window.location.href = getBaseURL(module) + 'contracts/view/' + response.id;
                        } else {
                            displayValidationErrors(response.validationErrors, currentForm);
                        }
                    }, complete: function () {
                        jQuery('#loader-global').hide();
                    },
                    error: defaultAjaxJSONErrorsHandler
                });
            }
        }
    });
}

function initializeField(input, options) {
    input.selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: options,
        createOnBlur: true,
        groups: [],
        optgroupField: 'class',
    });
}

function contractGenerateEventCheckRequired(container) {
    jQuery(".inline-error", container).addClass('d-none');
    $next = true;
    jQuery("[id^='variable-']", container).each(function () {
        var parent = jQuery('.' + jQuery(this).attr('id'), container);
        isRequired = jQuery(this).attr('is_required');
        if (isRequired == 1) {//check value if set
            if (!jQuery(this).val()) {
                $next = false;
                jQuery("div", parent).find("[data-field=" + jQuery(this).attr('id') + "]").html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
                jQuery('.modal-body').scrollTo(parent);
            }
        }
    });
    var radios = jQuery(".radio_buttons", container);
    for( var j=0; j<radios.length; j++ ) {
        if( !isOneInputChecked(radios[j], 'radio') ) {
            $next = false;
            jQuery(".inline-error", radios[j]).html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
            jQuery('.modal-body').scrollTo(radios[j]);
        }
    }
    var checkboxes = jQuery(".check_boxes", container);
    for( var j=0; j<checkboxes.length; j++ ) {
        if( !isOneInputChecked(checkboxes[j], 'checkbox') ) {
            $next = false;
            jQuery(".inline-error", checkboxes[j]).html(_lang.feedback_messages.fieldRequired).removeClass('d-none');
            jQuery('.modal-body').scrollTo(checkboxes[j]);
        }
    }
    return $next;
}


// function contractAddMetadata(module, option, step, currentFs, currentFs){
//     jQuery.ajax({
//         dataType: 'JSON',
//         url: getBaseURL(module) + 'contracts/add',
//         type: 'GET',
//         data: {option: option, step: step},
//         beforeSend: function () {
//             jQuery('#loader-global').show();
//         },
//         success: function (response) {
//             if (response.result) {
//                 if (response.html) {
//                     $continue = true;
//                     nextForm.html(response.html);
//                     switch (option) {
//                         case 'add':
//                             contractFormEvents(nextForm);
//                             jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
//                             jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
//                             jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
//                             break;
//                         case 'choose':
//                             jQuery('.select-picker', container).selectpicker({dropupAuto: false});
//                             break;
//                         default:
//                             break;
//                     }
//                     if ($continue) {
//                         //activate next step on progressbar using the index of nextFs
//                         jQuery('.modal-footer').removeClass('d-none');
//                         jQuery('.modal-footer', container).attr('data-field', nextStep);
//                         jQuery('.modal-body', container).removeClass('first-step');
//                         jQuery('.previous', jQuery('.modal-footer', container)).removeClass('d-none');
//                         currentFs.hide();
//                         nextFs.show();
//
//                     }
//
//                 }
//             } else {
//                 $continue = false;
//                 pinesMessage({ty: 'warning', m: response.display_message});
//             }
//
//
//         }, complete: function () {
//             jQuery('#loader-global').hide();
//         },
//         error: defaultAjaxJSONErrorsHandler
//     });
//     return $continue;
// }

function questionnaireFormEvents(pagesNb, currentFs, nextForm, nextStep, module, container) {
    nextFs = jQuery('#fieldset' + nextStep, container);
    jQuery('.tooltip-title', nextForm).tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    jQuery("[id^='variable-']", nextForm).each(function () {
        var parent = jQuery('.' + jQuery(this).attr('id'), container);
        switch (jQuery(this).attr('data-type')) {
            case 'date':
                setDatePicker(jQuery(this).parent(), container);
                break;
            case 'date_time':
                if (jQuery(this).parent().hasClass('date-picker')) {
                    setDatePicker(jQuery(this).parent(), container);
                }
                if (jQuery(this).hasClass('time')) {
                    jQuery(this).timepicker({
                        'timeFormat': 'H:i',
                    });
                }
                break;
            case 'list':
                jQuery(this).selectpicker();
                break;
            case 'single_lookup':
                var fieldTypeData = jQuery(this).attr('data-lookup-type');

                switch (fieldTypeData) {
                    case 'contacts':
                        var lookupDetails = {
                            'lookupField': jQuery(this),
                            'hiddenId': '.field-member-id',
                            'errorDiv': fieldTypeData
                        };
                        if (module == 'contract') {
                            lookUpContacts(lookupDetails, parent);
                        }
                        if (module == 'customer-portal') {


                            lookUpCustomerPortalUsers(lookupDetails, parent, false, 'contracts');

                        }

                        break;
                    default:
                        break;
                }
                break;

            case
            'multiple_lookup_per_type':
                var lookupDetails = {
                    'lookupField': jQuery('.field-lookup', parent),
                    'hiddenInput': jQuery('.field-member-id', parent),
                };
                jQuery('.field-member-type', parent).selectpicker().change(function () {
                    jQuery(".field-lookup", parent).val('');
                    jQuery(".field-member-id", parent).val('');
                    jQuery('.field-lookup', parent).typeahead('destroy');
                    lookupCompanyContactType(lookupDetails, parent, jQuery(this).val(), module);
                });
                jQuery('.field-lookup', parent).typeahead('destroy');
                lookupCompanyContactType(lookupDetails, parent, jQuery('.field-member-type option:selected', parent).val(), module);
                break;
            case 'multiple_fields_per_type':
                jQuery(this).selectpicker();
                setDatePicker('#end-date', container);
                break;
            default:
                break;

        }
    });
    if (pagesNb) {

        progressPerc = 100 / pagesNb;
        jQuery('.progress-bar', jQuery('#progress-bar', container)).css('width', progressPerc + '%').attr('progress', progressPerc);
        jQuery('#pages-count', jQuery('#progress-bar', container)).html(pagesNb);
        if (pagesNb == 1) {
            jQuery('.next', jQuery('.modal-footer', container)).addClass('d-none');
            jQuery('#form-submit', jQuery('.modal-footer', container)).removeClass('d-none');
            jQuery('#notification-div', jQuery('.modal-footer', container)).removeClass('d-none');
        }
        jQuery("#progress-bar", container).removeClass("d-none");
        jQuery(".modal-header", container).addClass("progress-padding");
        jQuery('.modal-footer', container).attr('data-field', nextStep);
        currentFs.hide();
        nextFs.show();
    } else {
        pinesMessage({ty: 'error', m: _lang.feedback_messages.contractQuestionnairePagesError});

    }
    return true;
}
function screenFormEvents(container){

    jQuery('input[type="text"], textarea, select', container).addClass('form-control');
    jQuery('div[requiredField="yes"]', container).each(function () {
        var container = jQuery(this);
        jQuery('.cp-screen-field', container).attr('required', '');
    });
    var prefixId="cp-date-";
    jQuery("[id^=" + prefixId + "]", container).each(function () {
        var id = jQuery(this).attr('id');
        switch (jQuery(this).attr('field-type')) {
            case 'date':
                setDatePicker(jQuery(this).parent(), container, datePickerOptions);
                break;
            case 'date_time':
                if (jQuery(this).parent().hasClass('date-picker')) {
                    setDatePicker(jQuery(this).parent(), container, datePickerOptions);
                }
                if (jQuery(this).hasClass('time')) {
                    jQuery(this).timepicker({
                        'timeFormat': 'H:i',
                    });
                }
                break;
            }
        });

    jQuery('.cp-screen-list-field', container).selectpicker({
        dropupAuto: false
    });
    if (jQuery('.related-assigned-team', container).length > 0 && jQuery('.related-assigned-team', container).val() > 0 && jQuery('#assignee', container).length > 0) {
        reloadUsersListByAssignedTeam(jQuery('.related-assigned-team', container).val(), jQuery('#assignee', container), 'customer-portal');
    }
}

function reloadUsersListByAssignedTeam(pGId, userListFieldId,module) {
    module = module || false;
    jQuery.ajax({
        url:  getBaseURL() + (module ? 'modules/'+module+'/' : '') +'/users/autocomplete/active',
        dataType: 'JSON',
        data: {
            join: ['provider_groups_users'],
            more_filters: {'provider_group_id': pGId},
            term: ''
        },
        success: function (results) {
            var newOptions = '<option value="">' + _lang.chooseUsers + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].firstName + ' ' + results[i].lastName + '</option>';
                }
            }
            userListFieldId.html(newOptions);
            jQuery(userListFieldId).trigger("chosen:updated");
        }, error: defaultAjaxJSONErrorsHandler});
}

function contractTypeEvent(typeId, subTypeId, selectedId) {
    selectedId = selectedId || 0;
    jQuery.ajax({
        url: getBaseURL('customer-portal') + 'contracts/load_sub_contract_types',
        dataType: 'JSON',
        data: { 'type_id': typeId },
        type: 'GET',
        success: function (results) {
            var newOptions = '<option value="0">' + _lang.none + '</option>';
            if (typeof results != "undefined" && results != null && results.length > 0) {
                for (i in results) {
                    newOptions += '<option value="' + results[i].id + '">' + results[i].name + '</option>';
                }
            }
            subTypeId.html(newOptions).val(selectedId).selectpicker("refresh");

        }, error: defaultAjaxJSONErrorsHandler
    });
}
function contractTemplateEvent(typeId, subTypeId, templateContainer) {
    jQuery.ajax({
        url: getBaseURL('customer-portal') + 'contracts/load_templates_per_contract_types',
        dataType: 'JSON',
        data: {'type_id': typeId, 'sub_type_id': subTypeId},
        type: 'GET',
        success: function (response) {
            var newOptions = '<option value="">' + _lang.choose + '</option>';
            if (typeof response.templates != "undefined" && response.templates != null) {
                for (i in response.templates) {
                    newOptions += '<option value="' + i + '">' + response.templates[i] + '</option>';
                }
            }
            templateContainer.html(newOptions).selectpicker("refresh");

        }, error: defaultAjaxJSONErrorsHandler
    });
}