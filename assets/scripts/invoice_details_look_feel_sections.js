var taskBoardColumns = null, nbOfColumns = 3;

jQuery(document).ready(function () {
    nbOfColumns = jQuery("#nbOfColumns"), taskBoardColumns = jQuery("#taskBoardColumns"),
            minNb = parseInt(nbOfColumns.attr('min')), maxNb = parseInt(nbOfColumns.attr('max'));


    jQuery('select', taskBoardColumns).each(function (index, element) {
        jQuery(element).change(function (event) {
            var colId = this.getAttribute('colId');
//			jQuery("#taskBoardColumnName" + colId).validationEngine('validate');
        }).chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: 'content',
            width: '100%',
            onResultsShow: function () {
                updateChosenOptions();
            }
        });
    });
    boardFormValidationRules();
});


function taskStatusIsSet(field, rules, i, options) {
    var colId = field.attr('colId');
    var values = jQuery('#taskStatusId' + colId).val();
    if (field.val() == '' || (null != values && String(values).split(',').length > 0)) {
        return true;
    }
    return _lang.validation_field_required.sprintf([_lang.task_status]);

}
function removeColumnsDescFromForms(totalNbOfCol) {
    for (var i = 6; i > totalNbOfCol; i--) {
        jQuery('#col_' + i, taskBoardColumns).remove();
    }
}
function boardFormValidationRules() {
    jQuery("#lookFeel").validationEngine({
		validationEventTrigger :"submit",
        autoPositionUpdate: true,
        promptPosition: 'topLeft',
        scroll: false,
        'custom_error_messages': {
            '#taskBoardName': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.taskBoardName])
                }
            },
            '#nbOfColumns': {
                'required': {
                    'message': _lang.validation_field_required.sprintf([_lang.nbOfColumns])
                }
            }
        }
    });
}
function updateChosenOptions() {
    var selects = jQuery('.task-status-chosen-selected');
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