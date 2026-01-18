var contractBoardColumns = null, nbOfColumns = 0;
var calendarPalette = [
    '1ABC9C', '16A085', '27AE60', 'F1C40F', 'E67E22',
    'F39C12', 'D35400', '3498DB', '2980B9', 'E74C3C',
    'C0392B', '9B59B6', '8E44AD', '34495E', '2C3E50',
    '7F8C8D'
];
jQuery(document).ready(function () {
    initiateColorPalettes();
    nbOfColumns = jQuery("#nbOfColumns"), contractBoardColumns = jQuery("#contract-board-columns"),
        nbOfColumnsHidden = jQuery("#nbOfColumnsHidden")
    minNb = parseInt(nbOfColumnsHidden.attr('min')), maxNb = parseInt(nbOfColumnsHidden.attr('max'));
    nbOfColumns.spinner(
        {
            change: function (event, ui) {
                changeColumns();
            },
            icons: {down: "ui-icon-circle-triangle-s", up: "ui-icon-circle-triangle-n"}
        }
    );
    changeColumns();
    jQuery('select', contractBoardColumns).each(function (index, element) {
        jQuery(element).chosen({
            no_results_text: _lang.no_results_matched,
            placeholder_text: _lang.chooseCaseStatus,
            width: '100%',
            onResultsShow: function () {
                updateChosenOptions();
            }
        });
    });
});

function changeColumnsSpinner() {
    var columnsNumb = jQuery('#nbOfColumnsHidden');
    var columnsNumbVal = parseInt(columnsNumb.val());
    columnsNumb.val(columnsNumbVal + 1);
    addSingleColumn(parseInt(columnsNumbVal + 1));
}

function contractBoardSubmit() {
    let url = getBaseURL('contract').concat('dashboard/save_board_config/');
    var formData = jQuery("form#board-config-form", '#board-configuration').serializeArray();
    jQuery.ajax({
        url: url,
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            jQuery(".inline-error").addClass('d-none');
            if (response.result) {
                window.location = getBaseURL('contract') + 'dashboard/boards';
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
                if (typeof response.validation_errors !== 'undefined') {
                    jQuery.each(response.validation_errors, function (category, val) {
                        jQuery.each(val, function (rowId, errors) {
                            if (typeof errors == 'object') {
                                displayValidationErrors(errors, jQuery('#col_' + rowId, '#contract-board-columns'));
                            } else {
                                displayValidationErrors(val, jQuery('.' + category));
                            }
                        });
                    });
                }
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function changeColumns() {
    if (nbOfColumns.val() == '' || parseInt(nbOfColumns.val()) < minNb) {
        changeColumns();
        return false;
    }
    if (nbOfColumns.val() == '' || parseInt(nbOfColumns.val()) > maxNb) {
        changeColumns();
        return false;
    }
    var nbOfColumnsInitilazed = jQuery('.board-column', '#contract-board-columns').length;
    if (nbOfColumnsInitilazed < nbOfColumns.val()) {
        addColumnsToForm(nbOfColumnsInitilazed, nbOfColumns.val());
    } else if (nbOfColumnsInitilazed > nbOfColumns.val()) {
        removeColumnsDescFromForms(nbOfColumns.val());
    } else
        return true;
}

function calTotalContractsColumns() {
    var totalNum = jQuery('#nbOfColumnsHidden').val();
    jQuery('#nbOfColumnsHidden').val(totalNum - 1);
    var count = 1;
    jQuery('.board-column').each(function () {
        var res = this.id.split("col_");
        this.id = 'col_' + count;
        jQuery("#column-name" + res[1]).attr('colId', count);
        jQuery("#column-name" + res[1]).attr('id', "column-name" + count);
        jQuery("[name='columns[" + res[1] + "][name]']").attr('name', "columns[" + count + "][name]");
        jQuery("[name='columns[" + res[1] + "][color]']").attr('name', "columns[" + count + "][color]");
        jQuery("[name='options[" + res[1] + "][board_column_id]']").attr('name', "options[" + count + "][board_column_id]");
        jQuery("[name='options[" + res[1] + "][status_id][]']").attr('name', "options[" + count + "][status_id][]");
        jQuery("#status-id" + res[1]).attr('colId', count);
        jQuery("#status-id" + res[1]).attr('id', "status-id" + count);
        jQuery("#status-id" + res[1] + "_chosen").attr('id', "status-id" + count + "_chosen");
        count++;
    });
}

function addColumnsToForm(nbOfColumnsInitilazed, totalNbOfCol) {
    for (var i = nbOfColumnsInitilazed; i < totalNbOfCol; i = colIndx) {
        var colIndx = i + 1;
        addSingleColumn(colIndx);
    }
}

function addSingleColumn(colIndx) {
    jQuery('<div class="board-column col-md-2 form-group padding-all-10 columns options" id="col_' + colIndx + '">' +
        '<div class="grey-box-container-confg-board">' +
        '<a onclick="jQuery(this).parent().parent().remove();calTotalContractsColumns();" href="javascript:;" class="float-right mt-10"><i class="icon-alignment fa fa-trash light_red-color float-left-arabic font-15"></i></a>' +
        '<label class="required mt-10">' +
        _lang.columnTitle + ':</label>' +
        '<input class="contract-board-titles form-control form-group mt-10" value="" name="columns[' + colIndx + '][name]" id="column-name' + colIndx + '" colId="' + colIndx + '" type="text" data-validation-engine="validate[required, maxSize[16], funcCall[caseStatusIsSet[]]]" />' +
        '<div data-field="name" class="inline-error d-none"></div>' +
        '<label class="required">' +
        _lang.caseStatuses + ':</label>' +
        '<input type="hidden" name="options[' + colIndx + '][board_column_id]" id="" />' +
        '<select multiple name="options[' + colIndx + '][status_id][]" id="status-id' + colIndx + '" colId="' + colIndx + '" class="form-control contract-status-chosen-selected">' +
        jQuery('#contract-statuses').html() +
        '</select>' +
        '<div data-field="status_id" class="inline-error d-none"></div>' +
        '<div class="flex-row-margin no-padding"><label class="required">' + _lang.columnColor + ':</label>' +
        '<div class="color-platter-container flex-end-item no-margin-bottom">' +
        '<input type="hidden" value="#' + calendarPalette[0] + '" name="columns[' + colIndx + '][color]" class="color-palette" />' +
        '</div></div>' +
        '</div>' +
        '</div>'
    ).insertBefore('#column-add');
    initiateColorPalettes();
    jQuery('#status-id' + colIndx, contractBoardColumns).chosen({
        no_results_text: _lang.no_results_matched,
        placeholder_text: _lang.chooseCaseStatus,
        width: '100%',
        onResultsShow: function () {
            updateChosenOptions();
        }
    });
}

function caseStatusIsSet(field, rules, i, options) {
    var colId = field.attr('colId');
    var values = jQuery('#status-id' + colId).val();
    if (field.val() == '' || (null != values && String(values).split(',').length > 0)) {
        return true;
    }
    return _lang.validation_field_required.sprintf([_lang.case_status]);

}

function removeColumnsDescFromForms(totalNbOfCol) {
    for (var i = 6; i > totalNbOfCol; i--) {
        jQuery('#col_' + i, contractBoardColumns).remove();
    }
}

function updateChosenOptions() {
    var selects = jQuery('.contract-status-chosen-selected');
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

function postFilters(boardId) {
    jQuery.ajax({
        url: getBaseURL('contract').concat('dashboard/board_post_filters/').concat(boardId),
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.html) {
                jQuery("#post-filter", "#board-configuration").html(response.html);
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

function boardPostFilterForm(boardId, filterPostId) {
    filterPostId = filterPostId || false;
    let url = getBaseURL('contract').concat('dashboard/add_edit_board_post_filters/').concat(boardId);
    let data = {};
    jQuery.ajax({
        url: filterPostId ? url.concat("/" + filterPostId) : url,
        data: data,
        dataType: 'JSON',
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                let postFilterBoard = "post-filter-board";
                jQuery('<div id="post-filter-board"></div>').appendTo("body");
                var container = jQuery("#" + postFilterBoard);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                jQuery(container).find('input').keypress(function (e) {
                    // Enter pressed?
                    if (e.which == 13) {
                        e.preventDefault();
                        boardPostFilterFormSubmit(boardId);
                    }
                });
                jQuery("#filter-board-field", container).selectpicker();
                selectBoardFilterInput(container);
                jQuery("#filter-board-operator", container).selectpicker();
                jQuery("#filter-board-field-options", container).selectpicker();
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function selectBoardFilterInput(container) {
    let filterBoardOperatorContainer = jQuery("#filter-board-operator-container", container);
    let filterBoardFieldContainer = jQuery("#filter-board-field-container", container);
    let filterBoardOperator = jQuery("#filter-board-operator", container);
    let filterBoardField = jQuery("#filter-board-field", container);
    let filterBoardFieldDetails = jQuery("#filter-board-field-details", container);
    jQuery("#filter-board-field", container).on("changed.bs.select", function (event) {
        let selectedFilter = jQuery(event.currentTarget);
        let fieldOperator = selectedFilter.find(':selected').data('field_operator');
        let field = selectedFilter.find(':selected').data('field_name');
        if (selectedFilter.val()) {
            filterBoardOperatorContainer.removeClass("d-none")
            filterBoardFieldContainer.removeClass("d-none")
        } else {
            filterBoardOperatorContainer.addClass("d-none")
            filterBoardFieldContainer.addClass("d-none")
        }
        let OperatorContent = '<option value=""></option>';
        jQuery.each(operators[fieldOperator], function (index, value) {
            OperatorContent += '<option value="' + index + '">' + value + '</option>';
        });
        filterBoardOperator.html(OperatorContent).selectpicker('refresh');
        filterBoardFieldDetails.html(fieldsDetails[field].html);
        loadFilterEvent(fieldsDetails[field].field_type, container, fieldsDetails[field].id);
    });
}

function loadFilterEvent(type, container, id) {
    jQuery("#criteria-field-" + id, container).selectpicker();
}

function boardPostFilterFormSubmit(boardId) {
    let boardFilterGrid = jQuery('#board-filter-list-grid');
    let container = jQuery("#post-filter-board-container");
    let url = getBaseURL('contract').concat('dashboard/add_edit_board_post_filters/');
    var formData = jQuery("form#post-filter-board-form", container).serializeArray();
    jQuery.ajax({
        url: url,
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            ajaxEvents.beforeActionEvents(container);
        },
        success: function (response) {
            jQuery(".inline-error").addClass('d-none');
            if (response.status) {
                jQuery(".modal").modal("hide");
                postFilters(boardId);
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'warning', m: response.display_message});
                }
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                }
            }
        }, complete: function () {
            ajaxEvents.completeEventsAction(container);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function deleteBoardPostFilter(boardId, filterPostId) {
    let boardFilterGrid = jQuery('#board-filter-list-grid');
    confirmationDialog('confirm_delete_record', {
        resultHandler: function () {
            jQuery.ajax({
                url: getBaseURL('contract').concat('dashboard/delete_board_post_filter/').concat(filterPostId),
                dataType: 'JSON',
                type: 'POST',
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    if (response.status) {
                        pinesMessageV2({ty: 'success', m: _lang.feedback_messages.success});
                        postFilters(boardId);
                    } else {
                        pinesMessageV2({ty: 'error', m: _lang.feedback_messages.deleteRowFailed});
                    }
                }, complete: function () {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }, parm: filterPostId
    })
}