function loadSelectizeOptions(id, container) {
    jQuery('#' + id, container).selectize({
        plugins: ['remove_button'],
        placeholder: _lang.startTyping,
        valueField: 'id',
        labelField: 'firstName',
        searchField: ['firstName', 'lastName'],
        create: false,
        persist: false,
        render: {
            option: function (item, escape) {
                return '<div><span>' + escape(item.firstName) + ' ' + escape(item.lastName) + '</span></div>';
            },
            item: function (item, escape) {
                if (item.lastName !== null && typeof item.lastName !== 'undefined') {
                    return '<div><span>' + escape(item.firstName) + ' ' + escape(item.lastName) + '</span></div>';
                } else {
                    return '<div><span>' + escape(item.firstName) + '</span></div>';
                }

            }
        },
        load: function (query, callback) {
            if (query.length < 3)
                return callback();
            jQuery.ajax({
                url: getBaseURL('contract') + "users/autocomplete/active",
                type: 'GET',
                data: {
                    term: encodeURIComponent(query),
                },
                dataType: 'json',
                error: function () {
                    callback();
                },
                success: function (res) {
                    callback(res);
                }
            });
        }
    });
}

function loadSelectizeContactOptions(id, container, url, moreFilters) {
    var moreFilters = moreFilters ? {'email': 'required'} : false;
    var $select = jQuery('#' + id, container).selectize({
        plugins: ['remove_button'],
        placeholder: _lang.startTyping,
        valueField: 'id',
        labelField: 'firstName',
        searchField: ['firstName', 'lastName'],
        create: false,
        persist: false,
        render: {
            option: function (item, escape) {
                return '<div><span>' + escape(item.firstName) + ' ' + escape(item.lastName) + '</span></div>';
            },
            item: function (item, escape) {
                if (item.lastName !== null && typeof item.lastName !== 'undefined') {
                    return '<div><span>' + escape(item.firstName) + ' ' + escape(item.lastName) + '</span></div>';
                } else {
                    return '<div><span>' + escape(item.firstName) + '</span></div>';
                }

            }
        },
        onItemRemove: function (value) {
            jQuery('#' + value, '#emails-container').remove();
            if (!jQuery('.inline-email', "#emails-container").length) {
                jQuery("#emails-container").addClass('d-none');
            }
        },
        onItemAdd: function (value, $item) {
            if (value && !jQuery('#' + value, '#emails-container').length) {
                var selectize = $select[0].selectize;
                var email = selectize['options'][value]['email'];
                jQuery("#emails-container").removeClass('d-none');
                jQuery(".emails-div", "#emails-container").append('<div id=' + value + '><input name="contacts[email][]" type="hidden" value='+email+'> <span class="inline-email" style="display: block;">' + email + "</span></div>");
            }

        },
        load: function (query, callback) {
            if (query.length < 3)
                return callback();
            jQuery.ajax({
                url: getBaseURL() + url,
                type: 'GET',
                data: moreFilters ?
                    {term: encodeURIComponent(query), more_filters: moreFilters}
                    :
                    {term: encodeURIComponent(query)},
                dataType: 'json',
                error: function () {
                    callback();
                },
                success: function (res) {
                    callback(res);
                }
            });
        }
    });
}


function showHideInCustomerPortal(contractId, isVisible, event, isForm) {
    preventFormPropagation(event);
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('contract') + 'contracts/' +(isVisible == 1 ? 'hide_in_customer_portal/' : 'show_in_customer_portal/')+ contractId + '/' + isForm,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                var showCustomerDialogExit = document.getElementById("show-customer-dialog");
                if(showCustomerDialogExit){
                    showCustomerDialogExit.remove();
                }
                jQuery('<div id="show-customer-dialog"></div>').appendTo("body");
                var showCustomerDialog = jQuery('#show-customer-dialog');
                showCustomerDialog.html(response.html);
                jQuery(".select-picker",showCustomerDialog).selectpicker();
                commonModalDialogEvents(showCustomerDialog, submitShowInCustomerPortal);
            } else {
                if(!response.result){
                    pinesMessage({ty: 'error', m: response.display_message});
                } else {
                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                    if (jQuery('#contractsGrid').length) {
                        jQuery('#contractsGrid').data('kendoGrid').dataSource.read();
                    }else{
                        jQuery('.cp-status', '#header-section').removeClass('green').addClass('gray').attr('title', _lang.hiddenInCP);
                        if ('undefined' !== typeof response.header_actions_section_html) {
                            jQuery('#header-actions-section', '.contract-container').html(response.header_actions_section_html);
                        }
                    }
                    if (isVisible){
                        jQuery('#contract-watchers','#main-section').hide();
                    }
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function submitShowInCustomerPortal(){
    var showCustomerPortalForm = jQuery('#show-customer-portal-form');
    var showCustomerDialogModal = jQuery('#show-customer-dialog-modal');
    var showCustomerPortalFormData = showCustomerPortalForm.serialize();
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/show_in_customer_portal/',
        type: 'POST',
        dataType: 'JSON',
        data: showCustomerPortalFormData,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if(response.result){
                if (jQuery('#contractsGrid').length) {
                    jQuery('#contractsGrid').data('kendoGrid').dataSource.read();
                }else{
                    jQuery('.cp-status', '#header-section').removeClass('gray').addClass('green').attr('title', _lang.containerVisibleFromCP);
                    if ('undefined' !== typeof response.header_actions_section_html) {
                        jQuery('#header-actions-section', '.contract-container').html(response.header_actions_section_html);
                    }
                }
                jQuery('.modal', '#show-customer-dialog').modal('hide');
                pinesMessage({ty: 'success', m: _lang.successful});
                if ('undefined' !== typeof response.right_section_html){
                    jQuery('#right-section', '.contract-container').html(response.right_section_html);
                    showToolTip('.contract-container', '#contract-watchers');
                }
            } else {
                if(typeof response.display_message !== 'undefined'){
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
                if(typeof response.validation_errors !== 'undefined') {
                    jQuery(".inline-error").addClass('d-none');
                    displayValidationErrors(response.validation_errors, showCustomerDialogModal);
                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function contractDelete(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contracts/delete/',
        data: Array.isArray(params)?{'id': params}:{'id': params.id},
        type: 'post',
        dataType: 'json',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            ty = 'error';
            if (response.result) {
                ty = 'success';
                if (jQuery('#contractsGrid').length) {
                    jQuery('#contractsGrid').data('kendoGrid').dataSource.read();
                }else{
                    window.location = getBaseURL('contract');
                }
            }
            pinesMessage({ty: ty, m: response.display_message});
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function slaLogs(contractId, event) {
    preventFormPropagation(event);
    jQuery.ajax({
        url:  getBaseURL('contract') + 'sla_management/show_sla_working_hours/'+ contractId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.result) {
                jQuery('<div id="sla-show-logs-dialog"></div>').appendTo("body");
                var slaShowLogsDialog = jQuery('#sla-show-logs-dialog');
                slaShowLogsDialog.html(response.html);
                jQuery('.modal', slaShowLogsDialog).modal();
            } else {
                pinesMessageV2({ty: 'warning', m: _lang.slaPrefNotDefined});
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function refreshSlaList(contractId) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'sla_management/show_sla_working_hours/'+ contractId,
        dataType: 'JSON',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.result) {
                var tmpContainer = jQuery('<div id="tmpContainer"></div>').addClass("d-none").appendTo("body");
                tmpContainer.html(response.html);
                jQuery('.modal-body', '#sla-show-logs-dialog').html(jQuery('.modal-body', tmpContainer).html());
                tmpContainer.remove();
                
            } else {
                pinesMessage({ty: 'warning', m: _lang.slaPrefNotDefined});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteSla(id) {
    var container = jQuery('#sla-table');
    jQuery.ajax({
        url: getBaseURL('contract') + 'sla_management/delete/',
        type: 'POST',
        dataType: "json",
        data: {id :id},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.result) {
                jQuery('#sla-'+id , container).remove();
                pinesMessage({ty: 'success', m: _lang.feedback_messages.slaDeletedSuccessfully});
            } else {
                (response.error)? pinesMessage({ty: 'error', m: _lang.feedback_messages.deleteRecordWithRelatedObject}) : pinesMessage({ty: 'error', m: _lang.feedback_messages.recordNotDeleted});
            }
        }
    });
}

