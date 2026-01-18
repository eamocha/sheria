jQuery(function () {
    var formObj = jQuery('#formContainer');
    formObj.validationEngine({validationEventTrigger: "submit", autoPositionUpdate: true, promptPosition: 'bottomRight', scroll: false});
    jQuery('#submitFormBtn').click(function () {
        if (formObj.validationEngine('validate')) {
            formObj.submit();
        }
    });

    clientInitializationCaseTypes(jQuery('#sla-management-form'), { 'onselect': onCaseClientSelect },0);
   
    initializeSelectNotifications(jQuery('#sla-management-form'));

    jQuery('.sla-statuses').chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCaseStatus,
        width: '100%',
        onResultsShow: function () {
            var selects = jQuery('.sla-statuses');
            var selected = [];
            selects.find("option").each(function () {
                if (this.selected) {
                    selected[this.value] = this;
                }
            }).each(function () {
                this.disabled = selected[this.value] && selected[this.value] !== this;
            });
            selects.trigger("chosen:updated");
        }
    });
    jQuery("#sla_workflow").change(function () {
        var workflow_id= jQuery(this).val();
        jQuery.ajax({
            url: getBaseURL() + 'sla_management/get_status_by_workflow',
            type: "POST",
            data: {workflow_id:workflow_id},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.result) {
                    jQuery('#sla_status').html(response.html);
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
        
         jQuery.ajax({ 
            url: getBaseURL() + 'sla_management/get_practice_area_by_workflow',
            type: "POST",
            data: {workflow_id:workflow_id},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.result) {
                    jQuery('#sla_practice_area').html(response.html);
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });

    });
 
});
function slaStatusIsSet(field, rules, i, options) {
    var colId = field.attr('colId');
    var values = jQuery('#caseStatusId' + colId).val();
    if (field.val() == '' || (null != values && String(values).split(',').length > 0)) {
        return true;
    }
    return _lang.validation_field_required.sprintf([_lang.case_status]);

}