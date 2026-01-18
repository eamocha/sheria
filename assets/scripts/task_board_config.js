var taskBoardColumns = null, nbOfColumns = 0;
var calendarPalette = [
    '1ABC9C', '16A085', '27AE60', 'F1C40F', 'E67E22',
    'F39C12', 'D35400', '3498DB', '2980B9', 'E74C3C',
    'C0392B', '9B59B6', '8E44AD', '34495E', '2C3E50',
    '7F8C8D'
];
jQuery(document).ready(function () {
    initiateColorPalettes();
    nbOfColumns = jQuery("#nbOfColumns"), taskBoardColumns = jQuery("#taskBoardColumns"),
            minNb = parseInt(nbOfColumns.attr('min')), maxNb = parseInt(nbOfColumns.attr('max'));
    nbOfColumns.spinner({change: function (event, ui) {
            changeColumns()
        }, icons: {down: "ui-icon-circle-triangle-s", up: "ui-icon-circle-triangle-n"}});
    changeColumns();
    jQuery('select', taskBoardColumns).each(function (index, element) {
        jQuery(element).change(function (event) {
            var colId = this.getAttribute('colId');
            jQuery("#taskBoardColumnName" + colId).validationEngine('validate');
        }).chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.chooseTaskStatus,
            width: '100%',
            onResultsShow: function () {
                updateChosenOptions();
            }
        });
    });
    boardFormValidationRules();
});
function changeColumnsSpinner(){
    var columnsNumb = jQuery('#nbOfColumnsHidden');
    var columnsNumbVal = parseInt(columnsNumb.val());
    columnsNumb.val(columnsNumbVal + 1);
    addSingleColumn(parseInt(columnsNumbVal + 1));
}
function checkMinimumBoardsNumber() {
    var columnsNumb = jQuery('#taskBoardForm .board-column').length;
    if (columnsNumb < minNb) {
        pinesMessageV2({ty: 'warning', m: _lang.feedback_messages.minimumAllowedNumberOfColumns});
        return false;
    }
    return true;
}
function changeColumns() {
    if (nbOfColumns.val() == '' || parseInt(nbOfColumns.val()) < minNb) {
        nbOfColumns.val(minNb);
        changeColumns();
        return false;
    }
    if (nbOfColumns.val() == '' || parseInt(nbOfColumns.val()) > maxNb) {
        nbOfColumns.val(maxNb);
        changeColumns();
        return false;
    }
    var nbOfColumnsInitilazed = jQuery('.board-column', '#taskBoardColumns').length;
    if (nbOfColumnsInitilazed < nbOfColumns.val()) {
        addColumnsToForm(nbOfColumnsInitilazed, nbOfColumns.val());
    }
    else if (nbOfColumnsInitilazed > nbOfColumns.val()) {
        removeColumnsDescFromForms(nbOfColumns.val());
    }
    else
        return true;
}
function calTotalTasksColumns() {
    var totalNum = jQuery('#nbOfColumns').val();
    jQuery('#nbOfColumns').val(totalNum - 1);
    var count = 1;
    jQuery('.board-column').each(function () {
        var res = this.id.split("col_");
        this.id = 'col_' + count;
        jQuery("[name='Task_Board_Column[" + res[1] + "][task_board_id]']").attr('name', "Task_Board_Column[" + count + "][task_board_id]");
        jQuery("#taskBoardColumnName" + res[1]).attr('colid', count);
        jQuery("#taskBoardColumnName" + res[1]).attr('id', "taskBoardColumnName" + count);
        jQuery("[name='Task_Board_Column[" + res[1] + "][name]']").attr('name', "Task_Board_Column[" + count + "][name]");
        jQuery("[name='Task_Board_Column[" + res[1] + "][color]']").attr('name', "Task_Board_Column[" + count + "][color]");
        jQuery("[name='Task_Board_Column_Option[" + res[1] + "][task_board_id]']").attr('name', "Task_Board_Column_Option[" + count + "][task_board_id]");
        jQuery("[name='Task_Board_Column_Option[[" + res[1] + "][task_board_column_id]']").attr('name', "Task_Board_Column_Option[[" + count + "][task_board_column_id]");
        jQuery("[name='Task_Board_Column_Option[" + res[1] + "][task_status_id][]']").attr('name', "Task_Board_Column_Option[" + count + "][task_status_id][]");
        jQuery("#taskStatusId" + res[1]).attr('colid', count);
        jQuery("#taskStatusId" + res[1]).attr('id', "taskStatusId" + count);
        jQuery("#taskStatusId" + res[1] + "_chosen").attr('id', "taskStatusId" + count + "_chosen");
        count++;
    });
}

function addColumnsToForm(nbOfColumnsInitilazed, totalNbOfCol){
    for(var i=nbOfColumnsInitilazed;i<totalNbOfCol;i=colIndx){
        var colIndx = i+1;
        addSingleColumn(colIndx);
    }
}

function addSingleColumn(colIndx) {
    jQuery('<div class="board-column col-md-2 form-group padding-all-10" id="col_' + colIndx + '">' +
                        '<div class="grey-box-container-confg-board">' +
                        '<a onclick="jQuery(this).parent().parent().remove();calTotalTasksColumns();" href="javascript:;" class="float-right mt-10"><i class="icon-alignment fa fa-trash light_red-color pull-left-arabic font-15"></i></a>'+
                        '<label class="required mt-10">'+
                        _lang.columnTitle + ':</label>' +
                        '<input type="hidden" name="Task_Board_Column[' + colIndx + '][task_board_id]" id="" />' +
                        '<input class="task-board-titles form-control form-group mt-10" value="" name="Task_Board_Column[' + colIndx + '][name]" id="taskBoardColumnName' + colIndx + '" colId="' + colIndx + '" type="text" data-validation-engine="validate[required, maxSize[16], funcCall[taskStatusIsSet[]]]" />' +
                        '<label class="required">' +
                        _lang.taskStatuses + ':</label>' +
                        '<input type="hidden" name="Task_Board_Column_Option[' + colIndx + '][task_board_id]" id="" />' +
                        '<input type="hidden" name="Task_Board_Column_Option[' + colIndx + '][task_board_column_id]" id="" />' +
                        '<select multiple name="Task_Board_Column_Option[' + colIndx + '][task_status_id][]" id="taskStatusId' + colIndx + '" colId="' + colIndx + '" class="form-control task-status-chosen-selected">' +
                        jQuery('#taskStatuses').html() +
                        '</select>' +
                        '<div class="flex-row-margin no-padding"><label class="required">' + _lang.columnColor + ':</label>' +
                        '<div class="color-platter-container flex-end-item no-margin-bottom">' +
                        '<input type="hidden" value="#'+calendarPalette[0]+'" name="Task_Board_Column[' + colIndx + '][color]" class="color-palette" />' +
                        '</div></div>' +
                        '</div>' +
                        '</div>'
        ).insertBefore('#column-add');
        initiateColorPalettes();
        jQuery('#taskStatusId' + colIndx, taskBoardColumns).change(function (event) {
            var colId = this.getAttribute('colId');
            jQuery("#taskBoardColumnName" + colId).validationEngine('validate');
        }).chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.chooseTaskStatus,
            width: '100%',
            onResultsShow: function () {
                updateChosenOptions();
            }
        });
}
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
    jQuery("#taskBoardForm").validationEngine({
        validationEventTrigger: "submit",
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

function initiateColorPalettes() {
    var spectrumPaletteOptions = {
        palette: [
            [calendarPalette[0], calendarPalette[1], calendarPalette[2], calendarPalette[3], calendarPalette[4]],
            [calendarPalette[5], calendarPalette[6], calendarPalette[7], calendarPalette[8], calendarPalette[9]],
            [calendarPalette[10], calendarPalette[11], calendarPalette[12], calendarPalette[13], calendarPalette[14]],
            [calendarPalette[15], calendarPalette[16], calendarPalette[17], calendarPalette[18], calendarPalette[19]],
            [calendarPalette[20], calendarPalette[21], calendarPalette[22], calendarPalette[23], calendarPalette[24]]
        ],
        showPaletteOnly: true,
        showPalette: true,
        theme: 'sp-light inline',
        preferredFormat: "hex"
    }
    jQuery('.color-palette').spectrum(spectrumPaletteOptions);
}
