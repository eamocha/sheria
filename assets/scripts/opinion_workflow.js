jQuery(document).ready(function () {
    jQuery("#tabs", '#opinion-workflows-management').tabs().addClass("ui-tabs-vertical ui-helper-clearfix no-margin-left");
    jQuery("#tabs-li li", '#opinion-workflows-management').removeClass("ui-corner-top").addClass("ui-corner-left col-md-2");
    var url = window.location.href;
    var statuses = url.split('/');
    var id = statuses[statuses.length - 1];
    if (jQuery.isNumeric(id)) {
        jQuery("#tabs li[aria-controls='" + id + "'] a").click();
    }
});

function workflowForm(id) {
    id = id || false;
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/' + (!id ? 'add_workflow' : ('edit_workflow/' + id)),
        type: "GET",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var workflowId = "#workflow-container";
                if (jQuery(workflowId).length <= 0) {
                    jQuery("<div id='workflow-container'></div>").appendTo("body");
                    var workflowContainer = jQuery(workflowId);
                    workflowContainer.html(response.html);
                    commonModalDialogEvents(workflowContainer, workflowFormSubmit);
                    initializeModalSize(workflowContainer, 0.4, 0.4);
                }
            } else {
                pinesMessage({ty: 'error', m: _lang.invalid_record});
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function workflowFormSubmit(container) {
    var formData = jQuery('form#workflow-form', container).serialize();
    var id = jQuery('#id', container).val();
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/' + (!id ? 'add_workflow' : ('edit_workflow/' + id)),
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery('.inline-error', container).addClass('d-none');
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            if (response.result) {
                var workflowId = typeof response.workflow_id !== 'undefined' ? response.workflow_id : id;
                window.location = getBaseURL() + 'opinion_workflows/index/' + workflowId;
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'error', m: response.display_message});
                    return;
                }
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                    return;
                }
                if (typeof response.html !== 'undefined') {
                    var statusesMigrationId = "#statuses-migration-container";
                    if (jQuery(statusesMigrationId).length <= 0) {
                        jQuery("<div id='statuses-migration-container'></div>").appendTo("body");
                        var statusesMigrationContainer = jQuery(statusesMigrationId);
                        statusesMigrationContainer.html(response.html);
                        initializeModalSize(statusesMigrationContainer, 0.4, 0.4);
                        commonModalDialogEvents(statusesMigrationContainer, statusesMigrationSubmit);
                        jQuery('.select-picker', statusesMigrationContainer).selectpicker();
                    }
                    return;
                }
                pinesMessage({ty: 'error', m: response.actionFailed});
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function statusesMigrationSubmit(container) {
    var formData = jQuery('form#status-migration-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/migrate_statuses/',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            if (!response.result) {
                pinesMessage({ty: 'error', m: _lang.actionFailed});
            } else {
                jQuery('.modal', container).modal('hide');
                workflowFormSubmit(jQuery('#workflow-container'));
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function workflowStatusForm(id) {
    if (!id) {
        pinesMessage({ty: 'error', m: _lang.invalid_record});
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/add_workflow_status/' + id,
        type: "POST",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var workflowStatusId = "#workflow-status-container";
                if (jQuery(workflowStatusId).length <= 0) {
                    jQuery("<div id='workflow-status-container'></div>").appendTo("body");
                    var workflowStatusContainer = jQuery(workflowStatusId);
                    workflowStatusContainer.html(response.html);
                    commonModalDialogEvents(workflowStatusContainer, workflowStatusFormSubmit);
                    initializeModalSize(workflowStatusContainer, 0.4, 0.25);
                    showToolTip();
                    jQuery('.select-picker', workflowStatusContainer).selectpicker({
                        dropupAuto: false
                    });
                }
            }
        }, complete: function () {

            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function workflowStatusFormSubmit(container) {
    var formData = jQuery('form#workflow-status-form', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/add_workflow_status/',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery('.inline-error', container).addClass('d-none');
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            if (!response.result) {
                displayValidationErrors(response.validation_errors, container);
            } else {
                window.location = getBaseURL() + 'opinion_workflows/index/' + response.workflow_id;
            }
        }, complete: function () {
            jQuery('.modal-save-btn', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function statusTransitionsViewForm(statusId, workflowId) {
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/view_status_transitions/' + statusId + '/' + workflowId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            var diagramId = "#diagram-container";
            if (jQuery(diagramId).length <= 0) {
                jQuery("<div id='diagram-container'></div>").appendTo("body");
                var diagramContainer = jQuery(diagramId);
                diagramContainer.html(response.html);
                commonModalDialogEvents(diagramContainer);
                jQuery("pre.arrows-and-boxes").arrows_and_boxes();
                jQuery(".arrowsandboxes-powered-by").remove();
                jQuery('.arrowsandboxes-node').css('height', 'auto');
                if (response.isLayoutRTL) {
                    // fix labels
                    labelTransitions = jQuery('.arrowsandboxes-label');
                    labelTransitions.each(function () {
                        var oldLeftMargin = jQuery(this).css('margin-left');
                        jQuery(this).css('margin-left', '0px');
                        jQuery(this).css('margin-right', oldLeftMargin);
                    });
                    // fix nodes positions
                    var wrapperNodes = jQuery('.arrowsandboxes-node');
                    wrapperNodes.parent().each(function () {
                        var stepMarginLeft = jQuery(this).css('margin-left');
                        jQuery(this).css('margin-left', '0px');
                        jQuery(this).css('margin-right', stepMarginLeft);
                    });
                    // fix arrows positions
                    var arrowWrapper = jQuery('.arrowsandboxes-arrow-wrapper');
                    arrowWrapper.children().each(function () {
                        var oldLeft = jQuery(this).css('left');
                        jQuery(this).css('left', '0px');
                        jQuery(this).css('right', oldLeft);
                    });
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function setAsStartPoint(workflowId, statusId) {
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/set_as_start_point/' + workflowId + '/' + statusId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL() + 'opinion_workflows/index/' + workflowId;
                window.location = window.location.href;
            }
        },
        complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteWorkflowStatus(statusId) {
    var workflowId = jQuery('.ui-tabs-active', '#opinion-workflows-management').attr('aria-controls');
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/delete_workflow_status/' + statusId + '/' + workflowId,
        type: 'POST',
        success: function (response) {
            if (response.result == 'DELETED') {
                jQuery('#workflow-' + workflowId + '-status-' + statusId).remove();
                pinesMessage({ty: 'success', m: response.message});
            } else if (response.result == 'FOREIGN_KEY_CONSTRAINT') {
                if (jQuery('#status-delete-error-modal').length <= 0) {
                    jQuery('<div id="status-delete-error-modal"></div>').appendTo("body");
                }
                var statusDeleteErrorModalContainer = jQuery('#status-delete-error-modal');
                statusDeleteErrorModalContainer.html(response.html);
                statusDeleteErrorModalContainer.find('.modal').modal('show');
            } else {
                pinesMessage({ty: 'error', m: response.message});
            }
        }, beforeSend: function () {
            jQuery('#loader-global').show();
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteTransition(transitionId) {
    if (!transitionId) {
        pinesMessage({ty: 'error', m: _lang.invalid_record});
        return false;
    }
    jQuery.ajax({
        url: getBaseURL() + 'opinion_workflows/delete_transition/' + transitionId,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#transition-' + transitionId, '#opinion-workflows-management').remove();
                pinesMessage({ty: 'success', m: _lang.deleteRecordSuccessfull});
            } else {
                pinesMessage({ty: 'error', m: _lang.recordNotDeleted});
            }
        },
        complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteWorkflow(workflowId) {
    window.location = getBaseURL() + 'opinion_workflows/delete_workflow/' + workflowId;
}