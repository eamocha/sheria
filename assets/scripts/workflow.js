function showWorkflowForm(id) {
    var url = getBaseURL() + 'manage_workflows/add';
    if (id != 0) {
        url = getBaseURL() + 'manage_workflows/edit/' + id;
    }
    jQuery.ajax({
        url: url,
        type: "POST",
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
                jQuery('#case_types').html(response.html_case_types);
                initializeModalSize(workflowContainer, 0.4, 0.4);
                commonModalDialogEvents(workflowContainer)
                jQuery("#submitFormBtn", workflowContainer).click(function () {
                    workflowSubmit(workflowContainer, url);
                });
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
function showTriggerForm(id) {
    var url = getBaseURL() + 'manage_workflows/add';
    if (id != 0) {
        url = getBaseURL() + 'manage_workflows/edit/' + id;
    }
    jQuery.ajax({
        url: url,
        type: "POST",
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
                jQuery('#case_types').html(response.html_case_types);
                initializeModalSize(workflowContainer, 0.5, 0.5);
                commonModalDialogEvents(workflowContainer)
                jQuery("#submitFormBtn", workflowContainer).click(function () {
                    workflowSubmit(workflowContainer, url);
                });
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
function workflowSubmit(workflow_container, url) {
    var formData = jQuery('form#workflowForm', workflow_container).serialize();
    jQuery.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
            jQuery('.inline-error', workflow_container).addClass('d-none');
        },
        success: function (response) {
            if (!response.result) {
                if (response.html) {
                    var container = "#migration_matter_status-container";
                    if (jQuery(container).length <= 0) {
                        jQuery("<div id='migration_matter_status-container'></div>").appendTo("body");
                    }
                    var migrationMatterStatusContainer = jQuery(container);
                    migrationMatterStatusContainer.html(response.html);
                    initializeModalSize(migrationMatterStatusContainer, 0.4, 0.4);
                    commonModalDialogEvents(migrationMatterStatusContainer);
                    jQuery("#submitFormBtn", migrationMatterStatusContainer).click(function () {
                        migrateMatterStatus(migrationMatterStatusContainer, url, workflow_container);
                    });
                } else if (response.message) {
                    pinesMessage({ty: 'error', m: response.message});
                } else {
                    displayValidationErrors(response.validationErrors, workflow_container);
                }

            } else {
                if (response.workflow_id) {
                    window.location = getBaseURL() + 'manage_workflows/statuses/' + response.workflow_id;
                } else {
                    window.location = window.location.href;
                }
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteStatus(statusId) {
    window.location = getBaseURL() + 'manage_workflows/delete_status/' + statusId;
}
function deleteWorkflowStatus(statusId) {
    var workflowId = jQuery('.ui-tabs-active').attr('aria-controls');
    jQuery.ajax({
        url: getBaseURL() + 'manage_workflows/delete_workflow_status/' + statusId + '/' + workflowId,
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
function deleteWorkflow(workflowId) {
    window.location = getBaseURL() + 'manage_workflows/delete_workflow/' + workflowId;
}
function statusFormPopup(id, type, workflowId) {
    id = id || false;
    type = type || false;
    workflowId = workflowId || false;
    var url = getBaseURL() + 'manage_workflows/add_status/' + (workflowId ? workflowId : '');
    if (type == "update") {
        url = getBaseURL() + 'manage_workflows/edit_status/' + id;
    }
    jQuery.ajax({
        url: url,
        type: "POST",
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.html) {
                var workflowStatusId = "#status-container";
                if (jQuery(workflowStatusId).length <= 0) {
                    jQuery("<div id='status-container'></div>").appendTo("body");
                    var workflowStatusContainer = jQuery(workflowStatusId);
                    workflowStatusContainer.html(response.html);
                    showToolTip();
                    commonModalDialogEvents(workflowStatusContainer);
                    initializeModalSize(workflowStatusContainer, 0.4, id ? 0.38 : 0.25);
                    jQuery("#submitFormBtn", workflowStatusContainer).click(function () {
                        statusSubmit(workflowStatusContainer, workflowId, url);
                    });
                    jQuery(workflowStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            statusSubmit(workflowStatusContainer, workflowId, url);
                        }
                    });
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
function workflowStatusFormPopup(id, type) {
    var url = getBaseURL() + 'manage_workflows/add_workflow_status/' + id;
    if (type == "update") {
        url = getBaseURL() + 'manage_workflows/edit_workflow_status/' + id;
    }
    jQuery.ajax({
        url: url,
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
                    initializeModalSize(workflowStatusContainer, 0.4, 0.25);
                    showToolTip();
                    commonModalDialogEvents(workflowStatusContainer);
                    jQuery("#submitFormBtn", workflowStatusContainer).click(function () {
                        statusSubmit(workflowStatusContainer, id, url);
                    });
                    jQuery('.select-picker', container).selectpicker({
                        dropupAuto: false
                    });
                    var container = jQuery('#workflowStatusFormNew');
                    jQuery("#add-workflow-status", container).click(function () { //quick add status
                        statusFormPopup(0, false, id);
                    });
                    jQuery(workflowStatusContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            statusSubmit(workflowStatusContainer, id, url);
                        }
                    });
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
function statusSubmit(container, workflowId, url) {
    var formData = jQuery('form#workflowStatusFormNew', container).serialize();
    jQuery.ajax({
        url: url,
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
            jQuery('.inline-error', container).addClass('d-none');
        },
        success: function (response) {
            if (!response.result) {
                displayValidationErrors(response.validationErrors, container);
                if(typeof response.message !== 'undefined'){
                    pinesMessage({ty: 'error', m: response.message});
                }
            } else {
                if (workflowId) {
                    window.location = getBaseURL() + 'manage_workflows/statuses/' + workflowId;
                } else {
                    window.location = window.location.href;
                }

            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function migrateMatterStatus(container, workflow_url, workflow_container) {
    var formData = jQuery('form#migrate_matter_status', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'manage_workflows/migrate_matter_status',
        type: "POST",
        data: formData,
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                workflowSubmit(workflow_container, workflow_url);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function getAreaOfPracticeByCategory() {
    var category = jQuery('#workflow_category').val();
    var id = jQuery('#id').val();
    jQuery.ajax({
        url: getBaseURL() + 'manage_workflows/get_area_of_practice_by_category',
        type: "POST",
        data: {category: category, id: id},
        dataType: "JSON",
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            if (response.result) {
                jQuery('#case_types').html(response.html);
            }
        }, complete: function () {
            jQuery("#loader-global").hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

}
function initializeSelectPermissions() {
    jQuery('.category-selectized').selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableCategories,
        createOnBlur: true,
        groups: [
        ],
        optgroupField: 'class',
    });
}
jQuery(document).ready(function () {
    jQuery("#sysConfTabs").tabs().addClass("ui-tabs-vertical ui-helper-clearfix no-margin-left");
    jQuery("#sysConfTabs-li li").removeClass("ui-corner-top").addClass("ui-corner-left");
});
