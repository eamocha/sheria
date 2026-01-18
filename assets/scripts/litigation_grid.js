var gridId = 'legalCaseGrid';
var casesGrid = null;
var enableQuickSearch = false;
notificationsNoteTemplate = '', authIdLoggedIn = '';
var casesSearchDataSrc, casesSearchGridOptions;
function caseQuickSearch(keyCode, term) {
    if (keyCode == 13) {//&& term.length > 1
        revertAllFilters();
        enableQuickSearch = true;
        document.getElementsByName("page").value = 1;
        document.getElementsByName("skip").value = 0;
        jQuery('#quickSearchFilterCaseValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterDescriptionValue', '#filtersFormWrapper').val(term);
        jQuery('#quickSearchFilterInternalReferenceValue', '#filtersFormWrapper').val(term);
        casesGrid.data("kendoGrid").dataSource.page(1);
    }
}
function loadEventsForFilters() {
    makeFieldsDatePicker({fields: ['arrivalDateValue', 'caseArrivalDateValue', 'dueDateValue', 'arrivalDateEndValue', 'caseArrivalDateEndValue', 'dueDateEndValue', 'closedOnValue', 'closedOnEndValue', 'sentenceDateValue', 'sentenceDateEndValue', 'hearingDateValue', 'hearingDateEndValue', 'modifiedOnValue', 'modifiedOnEndValue', 'createdOnValue', 'createdOnEndValue']});
    userLookup('assigneeValue');
    contactAutocompleteMultiOption('contactOutsourceToValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('contributorsHelpersValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    companyAutocompleteMultiOption(jQuery('#companiesValue', '#caseSearchFilters'), resultHandlerAfterCompaniesLegalCasesAutocomplete);
    contactAutocompleteMultiOption('contactValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('referredByValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    contactAutocompleteMultiOption('requestedByValue', '3', resultHandlerAfterContactsLegalCasesAutocomplete);
    jQuery('#clientTypeOpertator', '#caseSearchFilters').change(function () {
        jQuery('#clientNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#foreignClientTypeOpertator', '#caseSearchFilters').change(function () {
        jQuery('#clientForeignNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#clientNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#clientTypeOpertator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });

    jQuery('#clientForeignNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignClientTypeOpertator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.noMatchesFound,
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.contactForeignName,
                                        value: item.contactForeignName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: item.foreignName,
                                        value: item.foreignName,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });

    // jQuery('#outsourceTypeOperator', '#caseSearchFilters').change(function () {
    //     jQuery('#outsourceToValue', '#caseSearchFilters').val('');

    //     if (jQuery(this).val() == 'Company') {
    //         jQuery('#outsourceToField', '#caseSearchFilters').val('companyOutsourceTo');
    //         jQuery('#outsourceToFunction', '#caseSearchFilters').val('companyoutsourceto_field_value');
    //     } else {
    //         jQuery('#outsourceToField', '#caseSearchFilters').val('contactOutsourceTo');
    //         jQuery('#outsourceToFunction', '#caseSearchFilters').val('contactoutsourceto_field_value');
    //     }
    // });

    jQuery('#outsourceToValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = 'Company';//jQuery('select#outsourceTypeOperator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });

    jQuery('#advisorsToValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = 'Contact';//jQuery('select#outsourceTypeOperator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'Company' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });

    jQuery('#opponentTypeOpertator', '#caseSearchFilters').change(function () {
        jQuery('#opponentNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#foreignOpponentTypeOpertator', '#caseSearchFilters').change(function () {
        jQuery('#opponentForeignNameValue', '#caseSearchFilters').val('');
    });
    jQuery('#opponentNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            var lookupType = jQuery('select#opponentTypeOpertator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'companies' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                                if (lookupType == 'contacts') {
                                    return {
                                        label: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        value: item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName,
                                        record: item
                                    }
                                } else if (lookupType == 'companies') {
                                    return {
                                        label: null == item.shortName ? item.name : item.name + ' (' + item.shortName + ')',
                                        value: item.name,
                                        record: item
                                    }
                                }
                            }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    jQuery('#opponentForeignNameValue', '#caseSearchFilters').autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            request.lookupForeignName = true;
            var lookupType = jQuery('select#foreignOpponentTypeOpertator', '#caseSearchFilters').val();
            if (lookupType !== '') {
                lookupType = lookupType === 'companies' ? 'companies' : 'contacts';
                jQuery.ajax({
                    url: getBaseURL() + lookupType + '/autocomplete',
                    dataType: "json",
                    data: request,
                    error: defaultAjaxJSONErrorsHandler,
                    success: function (data) {
                        if (data.length < 1) {
                            response([{
                                    label: _lang.no_results_matched_add.sprintf([request.term]),
                                    value: '',
                                    record: {
                                        id: -1,
                                        term: request.term
                                    }
                                }]);
                        } else {
                            response(jQuery.map(data, function (item) {
                               if (lookupType == 'contacts') {
                                   return {
                                       label: item.contactForeignName,
                                       value: item.contactForeignName,
                                       record: item
                                   }
                               } else if (lookupType == 'companies') {
                                   return {
                                       label: item.foreignName,
                                       value: item.foreignName,
                                       record: item
                                   }
                               }
                           }));
                        }
                    }
                });
            }

        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
        }
    });
    jQuery('#opponentNationalityValue').autocomplete({autoFocus: false, delay: 600, source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({url: getBaseURL() + 'home/load_country_list', dataType: "json", data: request, error: defaultAjaxJSONErrorsHandler, success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched_for.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.countryName, value: item.countryName, record: item}
                        }));
                    }
                }});
        }, minLength: 2, select: function (event, ui) {
        }}
    );
    userLookup('modifiedByValue');
    userLookup('createdByValue');
}

function exportCasesToExcel(exportAllColumns) {
    exportAllColumns = exportAllColumns || false;
    var newFormFilter = jQuery('#exportResultsForm');
    var filters = checkWhichTypeOfFilterIUseAndReturnFilters();
    jQuery('#filtersForExport', newFormFilter).val(JSON.stringify(filters));
    jQuery('#filtersInfoForExport', newFormFilter).val(JSON.stringify(getExportInfoFilter('caseSearchFilters')));
    jQuery('#export-all-columns', newFormFilter).val(exportAllColumns);
    var pageNumber = jQuery('#exporting-module input[type="radio"]:checked').val();
    jQuery('#page_number', newFormFilter).val(pageNumber);
    newFormFilter.attr('action', getBaseURL() + 'export/litigation_case').submit();
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#caseSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('caseSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
    } else if (jQuery('#quickSearchFilterCaseValue', '#caseSearchFilters').val() || jQuery('#quickSearchFiltercategoryValue', '#caseSearchFilters').val() || jQuery('#quickSearchFilterDescriptionValue', '#caseSearchFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    filters.customFields = searchFilters.customFields;
    enableAll(filtersForm);
    return filters;
}
function uploadDocumentDone(message, type, caseCommentId, caseEdit) {
    if (undefined == caseCommentId)
        caseCommentId = 0;
    if (undefined == caseEdit)
        caseEdit = false;
    message = jQuery.parseJSON(message || "null");
    type = jQuery.parseJSON(type || "null");
    for (i in message) {
        pinesMessage({ty: type[i], m: message[i]});
    }
    if (!caseEdit) {
        jQuery("#commentDialog").dialog("close");
        jQuery('#commentFormContainer', '#commentDialog').removeClass('d-none');
        jQuery('#loading', '#commentDialog').addClass('d-none');
        if (caseCommentId > 0)
            toggle_comments(jQuery('#id').val(), true);
    } else {
        jQuery("#commentDialogEdit").dialog("close");
        jQuery('#commentFormContainerEdit', '#commentDialogEdit').removeClass('d-none');
        jQuery('#loading', '#commentDialogEdit').addClass('d-none');
        if (caseCommentId > 0)
            toggle_comments(jQuery('#id').val(), true);
    }
}
function caseCommentFormSubmitAndStartUpload() {
    jQuery('#comment', '#commentDialog').text(jQuery('.nicEdit-main', '#commentDialog').html());
    if (jQuery('#comment', '#commentDialog').text() == '<br>') {
        pinesMessage({ty: 'warning', m: _lang.commentFieldIsRequired});
        return false;
    }
    jQuery('#commentFormContainer', '#commentDialog').addClass('d-none');
    jQuery('#loading', '#commentDialog').removeClass('d-none');
    return true;
}
function addCaseDocument(caseId) {
    if (undefined == caseId)
        caseId = 0;
    var commentDialog = jQuery("#commentDialog");
    if (!commentDialog.is(':data(dialog)')) {
        if (caseId > 0) {
            caseCommentDialogForm(caseId);
        }
    } else {
        commentDialog.dialog("open");
        jQuery('#createdOn').blur();
        jQuery('#comment').focus();
    }
    if (jQuery('#innerWrap').length == 0) {
        var uiDialogButtons = jQuery('.ui-dialog-buttonpane');
        jQuery('.ui-dialog-buttonset').addClass('col-md-8');
        uiDialogButtons.addClass('col-md-12 pull-right');
        uiDialogButtons.wrapInner("<div class='pull-right col-md-5' id='innerWrap'></div>");
        jQuery(".btnSaveNewNote", uiDialogButtons).parent().before(notificationsNoteTemplate);
    }
}
function caseCommentDialogForm(caseId) {
    var subject = jQuery('#subject').val();
    if (jQuery('#subject').val().length > 50) {
        subject = jQuery('#subject').val().substring(0, 50) + '...';
    } else {
        subject = jQuery('#subject').val();
    }
    var commentDialog = jQuery("#commentDialog");
    jQuery("form#caseCommentForm").validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    commentDialog.dialog({
        autoOpen: true,
        buttons: [{
            text: _lang.save,
            'class': 'btn btn-info btnSaveNewNote pull-left',
            click: function () {
                var commentValid = jQuery("form#caseCommentForm").validationEngine('validate');
                if (commentValid) {
                    var commentTextarea = jQuery('.nicEdit-main', jQuery("form#caseCommentForm"));
                    var nicE = new nicEditors.findEditor('comment');
                    var textAreaText = nicE.getContent();
                    if (commentTextarea.text() == '' || commentTextarea.text().length < 3) {
                        pinesMessage({ty: 'warning', m: _lang.noteFieldIsRequired});
                    } else {
                        //this code was added to ensure that copy and pasting from Gmail does not result in broken html - refer to A4L-3100 for more info
                        nicE.setContent(textAreaText + '<p class="MsoNormal" style="margin-bottom: 0px; color: rgb(34, 34, 34); font-family: arial, sans-serif; font-size: 12.8px;">&nbsp;</p>');
                        jQuery('form#caseCommentForm').submit();
                    }
                }
            }
        },
            {
                text: _lang.cancel,
                'class': 'btn btn-link pull-left',
                click: function () {
                    resetCaseCommentFormValues();
                    jQuery(this).dialog("close");
                }
            }],
        close: function () {
            resetCaseCommentFormValues();
        },
        open: function () {
            jQuery('.nicEdit-main', jQuery('form#caseCommentForm')).html('');
            makeFieldsDatePicker({fields: ['createdOn']});
            userLookup('userFullName', 'user_id', '', 'active');
            jQuery('#createdOn').blur();
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(commentDialog, '80%', '460');
            }));
            resizeNewDialogWindow(commentDialog, '80%', '460');
            jQuery('#user_id', jQuery('form#caseCommentForm')).val(authIdLoggedIn);
        },
        draggable: true,
        modal: false,
        resizable: true,
        title: _lang.addNewCommentTitle.sprintf([caseId, subject])
    });
}
function submitCaseDocumentForm() {
    jQuery('form#caseCommentForm').submit();
}
function resetCaseCommentFormValues() {
    var commentDialog = jQuery("#commentDialog");
    jQuery("form#caseCommentForm", commentDialog).validationEngine('hide');
    jQuery('#comment', commentDialog).text('');
    jQuery('.nicEdit-main', commentDialog).html('');
    jQuery('#files_path', commentDialog).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="add_file_path()">' + _lang.addURL + '</button></div>');
    jQuery("form#caseCommentForm", commentDialog)[0].reset();
    jQuery(".extra-attachment-container", commentDialog).remove();
}
function add_file_path() {
    jQuery('#addMorePath').remove();
    jQuery('#files_path').append('<div class="row help-inline col-md-12 mt-2"><br/><input type="text" class="form-control" value="" name="paths[]"/>&nbsp;&nbsp;<button class="btn btn-default btn-sm" type="button" onclick="jQuery(this).parent().remove();"><i class="fa-solid fa-trash-can red mt-2 p-2"></i></button>&nbsp;&nbsp;<button id="addMorePath" class="btn btn-default btn-link" type="button" onclick="add_file_path()">' + _lang.addMore + '</button></div>');
    jQuery('#files_path input:last').focus();
}
function attach_file() {
    countAttachments++;
    jQuery('#files_attachment').append('<div class="extra-attachment-container col-md-12"><div class=" col-md-10"><input type="file"   style="width:100%"  name="attachment_' + countAttachments + '" id="attachment_' + countAttachments + '" value="" class="margin-top" /></div><button class="btn btn-default btn-sm" type="button" onclick="jQuery(this).parent().remove();"><i class="fa-solid fa-trash-can red"></i></button>&nbsp;&nbsp;<input type="hidden" name="attachments[]" value="attachment_' + countAttachments + '"/></div>');
    jQuery('#files_attachment input:last').focus();
}
function append_new_comment(caseCommentId) {
    if (jQuery('#commentsList').html() == '') {
        toggle_comments(jQuery('#id').val());
    } else
        jQuery.ajax({
            url: getBaseURL() + 'cases/get_last_comment/',
            data: {
                'id': caseCommentId,
                'caseId': jQuery('#id').val()
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    if (jQuery('#no_comment')) {
                        jQuery('#no_comment').remove();
                    }
                    jQuery('#commentsList').append('<div id="caseComment_' + caseCommentId + '"></div>');
                    jQuery('#caseComment_' + caseCommentId).html(response.html);
                    jQuery('#caseComment_' + caseCommentId).slideDown();
                    jQuery('#nbOfNotesHistory').html('(' + response.nbOfNotesHistory + ')');
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
}
function toggle_comment(id) {
    case_comment = jQuery('#case-comment_' + id);
    if (jQuery('#commentText', case_comment).is(':visible')) {
        jQuery('#commentText', case_comment).slideUp();
        jQuery('i', '#case-comment_' + id + ' a:first').removeClass('fa-solid fa-angle-down');
        jQuery('i', '#case-comment_' + id + ' a:first').addClass('fa-solid fa-angle-right');
    } else {
        jQuery('#commentText', case_comment).slideDown();
        jQuery('i', '#case-comment_' + id + ' a:first').removeClass('fa-solid fa-angle-right');
        jQuery('i', '#case-comment_' + id + ' a:first').addClass('fa-solid fa-angle-down');
    }
}
function toggle_comments(caseCommentId, fetchFromServer) {
    fetchFromServer = fetchFromServer || false;
    if (jQuery('#commentsList').html() == '' || fetchFromServer) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/get_all_comments/',
            data: {
                'id': caseCommentId
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    var commentsList = jQuery('#commentsList');
                    commentsList.html(response.html);
                    toggle_comments_slide_down_events(commentsList);
                    jQuery('#nbOfNotesHistory').html('(' + response.nbOfNotesHistory + ')');
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        commentsList = jQuery('div:last-child.commentsList');
        var commentsContainer = jQuery('#caseCommentsFieldset');
        if (commentsContainer.is(':visible')) {
            commentsContainer.slideUp();
            jQuery('#caseCommentToggleIcon').removeClass('fa-solid fa-angle-down');
            jQuery('#caseCommentToggleIcon').addClass('fa-solid fa-angle-right');
        } else {
            toggle_comments_slide_down_events(jQuery('#commentsList'));
        }
    }
}
function toggle_comments_slide_down_events(commentsList) {
    //slide down notes container
    jQuery('#caseCommentsFieldset').slideDown();
    //fix up the icon of "Notes History"
    jQuery('#caseCommentToggleIcon').removeClass('fa-solid fa-angle-right');
    jQuery('#caseCommentToggleIcon').addClass('fa-solid fa-angle-down');
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', commentsList)).hide();
    //fix up all notes "arrow" icons
    jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-down');
    jQuery('a > i', commentsList).addClass('fa-solid fa-angle-right');
    //slide down last note
    var lastNoteContainer = jQuery('div:nth-child(2)', commentsList);
    jQuery('span#commentText', lastNoteContainer).slideDown();
    var caseCommentId = lastNoteContainer.attr('id');
    //scroll To(caseCommentId);
    if (undefined != caseCommentId)
        scrollToId('#' + caseCommentId);
    //fix last note icon
    jQuery('a > i', lastNoteContainer).removeClass('fa-solid fa-angle-right');
    jQuery('a > i', lastNoteContainer).addClass('fa-solid fa-angle-down');
}
function expandAllNotes() {
    var commentsList = jQuery('#commentsList');
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', commentsList)).slideDown();
    jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-right');
    jQuery('a > i', commentsList).addClass('fa-solid fa-angle-down');
}
function collapseAllNotes() {
    var commentsList = jQuery('#commentsList');
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', commentsList)).slideUp();
    jQuery('a > i', commentsList).removeClass('fa-solid fa-angle-down');
    jQuery('a > i', commentsList).addClass('fa-solid fa-angle-right');
}

function editCaseCommentFormSubmitAndStartUpload() {
    jQuery('#commentEdit', '#commentDialogEdit').text(jQuery('.nicEdit-main', '#commentDialogEdit').html());
    if (jQuery('#commentEdit', '#commentDialogEdit').text() == '') {
        pinesMessage({ty: 'warning', m: _lang.noteFieldIsRequired});
        return false;
    }
    jQuery('#commentFormContainerEdit', '#commentDialogEdit').addClass('d-none');
    jQuery('#loading', '#commentDialogEdit').removeClass('d-none');
    return true;
}
function edit_comment(id, case_id) {
    var subject = jQuery('#subject').val();
    if (jQuery('#subject').val().length > 50) {
        subject = jQuery('#subject').val().substring(0, 50) + '...';
    } else {
        subject = jQuery('#subject').val();
    }
    jQuery(document.body).append('<div id="commentDialogEdit"></div>');
    var commentDialogEdit = jQuery('#commentDialogEdit');

    jQuery.ajax({
        url: getBaseURL() + 'cases/edit_comment/' + id + '/' + case_id,
        data: {},
        type: 'post',
        dataType: 'json',
        success: function (response) {
            commentDialogEdit.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        click: function () {
                            var commentValid = jQuery("form#caseCommentFormEdit").validationEngine('validate');
                            if (commentValid) {
                                var commentTextarea = jQuery('.nicEdit-main', jQuery("form#caseCommentFormEdit"));
                                if (commentTextarea.text() == '' || commentTextarea.text().length < 3) {
                                    pinesMessage({ty: 'warning', m: _lang.noteFieldIsRequired});
                                } else {
                                    jQuery('form#caseCommentFormEdit').submit();
                                }
                            }
                        }
                    },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            jQuery('#commentEdit', commentDialogEdit).text('');
                            jQuery('.nicEdit-main', commentDialogEdit).html('');
                            jQuery('#files_path', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="add_file_pathEdit()">Add file path</button></div>');
                            jQuery('#files_attachment', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="attach_fileEdit()">Attach a file</button></div>');
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function () {
                    jQuery('#commentEdit', commentDialogEdit).text('');
                    jQuery('.nicEdit-main', commentDialogEdit).html('');
                    jQuery('#files_path', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="add_file_pathEdit()">Add file path</button></div>');
                    jQuery('#files_attachment', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="attach_fileEdit()">Attach a file</button></div>');
                    jQuery(this).dialog("close");
                },
                open: function () {
                },
                draggable: true,
                resizable: true,
                modal: false,
                title: _lang.editComment.sprintf([case_id, subject])
            });
            commentDialogEdit.html(response.html);
            jQuery("form#caseCommentFormEdit", commentDialogEdit).validationEngine({
                validationEventTrigger: "submit",
                autoPositionUpdate: true,
                promptPosition: 'bottomRight',
                scroll: false
            });
            jQuery('#caseIdEdit').val(case_id);
            makeFieldsDatePicker({fields: ['createdOnEdit']});
            userLookup('userFullNameEdit', 'user_idEdit', '', 'active');
            jQuery('#createdOnEdit').blur();
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(commentDialogEdit, '80%', '460');
            }));
            resizeNewDialogWindow(commentDialogEdit, '80%', '460');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function add_file_pathEdit() {
    jQuery('#addMorePathEdit').remove();
    jQuery('#files_pathEdit').append('<div class="row mt-2 help-inline col-md-12"><br/><input type="text" class="form-control" value="" name="paths[]"/>&nbsp;&nbsp;<button class="btn btn-default btn-sm" type="button" onclick="jQuery(this).parent().remove();"><i class="fa-solid fa-trash-can red mt-2 p-2"></i></button>&nbsp;&nbsp;<button id="addMorePathEdit" class="btn btn-default btn-link" type="button" onclick="add_file_pathEdit()">' + _lang.addMore + '</button></div>');
    jQuery('#files_pathEdit input:last').focus();
}
function attach_fileEdit() {
    countAttachments++;
    jQuery('#addMoreAttachmentEdit').remove();
    jQuery('#files_attachmentEdit').append('<div class="help-inline-block col-md-12"><br/><input type="file" name="attachment_' + countAttachments + '" id="attachment_' + countAttachments + '" value="" class="form-control" />&nbsp;&nbsp;<button class="btn btn-default btn-sm" type="button" onclick="jQuery(this).parent().remove();"><i class="fa-solid fa-trash-can red"></i></button>&nbsp;&nbsp;<button id="addMoreAttachmentEdit" class="btn btn-default btn-link" type="button" onclick="attach_fileEdit()">' + _lang.addMore + '</button><input type="hidden" name="attachments[]" value="attachment_' + countAttachments + '"/></div>');
    jQuery('#files_attachmentEdit input:last').focus();
}
function modify_comment(caseCommentId) {
    jQuery.ajax({
        url: getBaseURL() + 'cases/get_last_comment/',
        data: {
            'id': caseCommentId,
            'caseId': jQuery('#id').val()
        },
        type: 'post',
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                caseCommentDiv = jQuery('#case-comment_' + caseCommentId)
                caseCommentDiv.html(response.html);
                caseCommentDiv.html(jQuery('div:first', caseCommentDiv).html());
                jQuery('#commentText', caseCommentDiv).slideUp();
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function unarchivedSelectedCases() {
    confirmationDialog('confirmationUnarchiveCases', {
        resultHandler: function(){ 
        jQuery.ajax({
            url: getBaseURL() + 'cases/archive_unarchive_cases',
            type: 'POST',
            dataType: 'JSON',
            data: {
                gridData: form2js('gridFormContent', '.', true)
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// saved successfuly
                        ty = 'information';
                        m = _lang.feedback_messages.updatesSavedSuccessfully;
                        break;
                    case 101:	// could not save records
                        m = _lang.feedback_messages.updatesFailed;
                        break;
                    default:
                        break;
                }
                pinesMessage({ty: ty, m: m});
                $legalCaseGrid.data("kendoGrid").dataSource.read();
                disabledUnArchiveBtn();
            },
             error: defaultAjaxJSONErrorsHandler 
        });
    }
    });
}
function archiveSelectedCases() {
    confirmationDialog('confirmationArchiveCases', {
        resultHandler: function(){ 
        jQuery.ajax({
            url: getBaseURL() + 'cases/archive_selected_cases',
            type: 'POST',
            dataType: 'JSON',
            data: {
                gridData: form2js('gridFormContent', '.', true)
            },
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// saved successfuly
                        ty = 'information';
                        m = _lang.feedback_messages.updatesSavedSuccessfully;
                        break;
                    case 101:	// could not save records
                        m = _lang.feedback_messages.updatesFailed;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: ty, m: m});
                $legalCaseGrid.data("kendoGrid").dataSource.read();
                disabledArchiveBtn();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
});
}
function enableDisableUnarchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title','');
    }
    else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        disabledUnArchiveBtn();
    } 
}
function enableDisableArchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#archivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip_litigation').attr('title','');
    }
    else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
       disabledArchiveBtn();
    } 
}
function enableDisable_tooltips(statusChkBx){
    enableDisableUnarchivedButton(statusChkBx);
    enableDisableArchivedButton(statusChkBx);


}
function checkUncheckAllCheckboxes(statusChkBx) {
    if (statusChkBx.checked && jQuery("tbody" + " INPUT[type='checkbox']").length >= 1) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title','');
        jQuery('#archive_tooltip_litigation').attr('title','');
        jQuery('#archive_tooltip_matter').attr('title','');
    } else {
        disabledUnArchiveBtn();
    }
    jQuery("tbody" + " INPUT[type='checkbox']").attr('checked', statusChkBx.checked);
}
jQuery(document).ready(function () {
    casesGrid = jQuery('#' + gridId);
    jQuery('.multi-select', '#caseSearchFilters').chosen({no_results_text: _lang.no_results_matched, placeholder_text: _lang.select, width: "100%"}).change();
    $legalCaseGrid = jQuery('#legalCaseGrid');
    if (document.location.href.substr(document.location.href.length - 5, 5) == 'print') {
        window.open(jQuery('#printAnchor').attr('href'));
        document.location = document.location.href.substr(0, document.location.href.length - 6);
    }
    newCaseFormDialog = jQuery('#' + newCaseFormDialogId);
    caseFormElement = jQuery('form#legalCaseAddForm', newCaseFormDialog)
    try {
        if (casesGrid.length > 0) {
            gridFiltersEvents('Litigation', 'legalCaseGrid', 'caseSearchFilters');
            gridInitialization();
            jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
                $legalCaseGrid.data('kendoGrid').dataSource.read();
            });
            jQuery('#caseSearchFilters').bind('submit', function (e) {
                jQuery("form#caseSearchFilters").validationEngine({
                    validationEventTrigger: "submit",
                    autoPositionUpdate: true,
                    promptPosition: 'bottomRight',
                    scroll: false
                });
                if (!jQuery('#caseSearchFilters').validationEngine("validate")) {
                    return false;
                }
                e.preventDefault();
                jQuery('#caseLookUp').val('');
                if (jQuery('#submitAndSaveFilter').is(':visible')) {
                    gridAdvancedSearchLinkState = true;
                }
                enableQuickSearch = false;
                document.getElementsByName("page").value = 1;
                document.getElementsByName("skip").value = 0;
                casesGrid.data('kendoGrid').dataSource.page(1);
            });
        } else {
            jQuery(window).bind('beforeunload', function (event) {
                if (jQuery('.changed', "form#legalCaseAddForm").length > 0 || jQuery('.k-dirty').length > 0) {
                    return _lang.warning_you_have_unsaved_changes;
                }
            });
            jQuery("form#legalCaseAddForm").dirty_form({
                includeHidden: true
            }).submit(function (e) {
                jQuery(window).unbind('beforeunload');
            });
            bindNewCaseFormEvents(true, "legalCaseAddForm");
            ctrlS(function () {
                caseFormElement.submit();
            });
        }
    } catch (e) {
        bindNewCaseFormEvents(true, "legalCaseAddForm");
        ctrlS(function () {
            caseFormElement.submit();
        });
    }
});
function isFormChanged() {
    if (jQuery('.changed', "form#legalCaseAddForm").length > 0 && confirm(_lang.warning_you_have_unsaved_changes_save_now)) {
        jQuery('form#legalCaseAddForm').attr('action', jQuery('form#legalCaseAddForm').attr('action') + '/print').submit();
        return false;
    }
    return true;
}
function convertToLitigation($caseId) {
    if (confirm(_lang.confirmationConvertCaseToLitigation)) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/convert_case_to_litigation/' + $caseId,
            type: 'POST',
            dataType: 'JSON',
            success: function (response) {
                var ty = 'error';
                var m = '';
                switch (response.status) {
                    case 202:	// update successfuly
                        ty = 'information';
                        m = _lang.feedback_messages.updatesSavedSuccessfully;
                        break;
                    case 101:	// could not save changes
                        m = _lang.feedback_messages.updatesFailed;
                        break;
                    default:
                        break;
                }
                pinesMessageV2({ty: ty, m: m});
                $legalCaseGrid.data("kendoGrid").dataSource.read();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function recordCaseExpense($caseId) {
    window.location = getBaseURL('money') + 'vouchers/case_expenses_add/' + $caseId;
}
function caseCommissions(case_id) {
    var caseCommissionsFormDialog = jQuery('#caseCommissionsFormDialog');
    var commissionDetails = jQuery('#commissionDetails');
    jQuery.ajax({
        url: getBaseURL() + 'cases/case_commissions/',
        data: {'case_id': case_id, 'formType': 'fetchForm'},
        type: 'POST',
        dataType: 'JSON',
        success: function (response) {
            caseCommissionsFormDialog.html(response.html);
            caseCommissionsFormDialog.dialog({
                autoOpen: true,
                buttons: [{
                        text: _lang.save,
                        'class': 'btn btn-info',
                        id: 'btnSubmitSave',
                        click: function () {
                            var dataIsValid = jQuery("form#caseCommissionsForm", this).validationEngine('validate');
                            var formData = jQuery("form#caseCommissionsForm", this).serialize();
                            if (dataIsValid) {
                                totalOfCommission = 0;
                                jQuery('tr', '#commissionDetails').each(function () {
                                    trID = jQuery(this).attr('id');
                                    totalOfCommission = totalOfCommission + jQuery('#commissionRate_' + trID).val() * 1;
                                });
                                if (totalOfCommission <= 100) {
                                    var that = this;
                                    jQuery.ajax({
                                        beforeSend: function () {
                                        },
                                        data: formData,
                                        dataType: 'JSON',
                                        type: 'POST',
                                        url: getBaseURL() + 'cases/case_commissions',
                                        success: function (response) {
                                            if (!response.result) {
                                                pinesMessage({ty: 'error', m: _lang.invalid_record});
                                                jQuery(that).dialog("close");
                                            } else {
                                                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                                jQuery(that).dialog("close");
                                            }
                                        },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                } else {
                                    pinesMessage({ty: 'warning', m: _lang.totalOfCommissionsInRowMustBeEqualOrLessThan100.sprintf([_lang.sharesValue])});
                                }
                            }
                        }
                    },
                    {
                        text: _lang.cancel,
                        'class': 'btn btn-link',
                        click: function () {
                            jQuery(this).dialog("close");
                        }
                    }],
                close: function () {
                },
                open: function () {
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '50%', '400');
                    }));
                    resizeNewDialogWindow(that, '50%', '400');
                    jQuery('tr', '#commissionDetails').each(function () {
                        trID = jQuery(this).attr('id');
                        partnerLookup('commissionBenifitiary_' + trID, 'commissionBenifitiaryName_' + trID);
                    });
                    caseCommissionsFormValidate();
                },
                draggable: true,
                modal: false,
                resizable: true,
                title: _lang.partnersShares
            });
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addPartnerCommissionRow() {
    trCount++;
    var caseCommissionsNewRow = '';
    caseCommissionsNewRow +=
            '<tr id="' + trCount + '"><td>' +
            '<input type="hidden" id="commissionBenifitiary_' + trCount + '" name="commissionBenifitiaryIds[]" value="" required="" data-validation-engine="validate[required]"/>' +
            '<input type="text" id="commissionBenifitiaryName_' + trCount + '" value="" class="form-control lookup"  onblur="checkLookupValidity(jQuery(this), jQuery(\'#commissionBenifitiary_' + trCount + '\'));" />' +
            '</td><td>' +
            '<input type="text" class="form-control" id="commissionRate_' + trCount + '" name="commissionRate[]"  required="" data-validation-engine="validate[required,funcCall[validateNumber]]"/>' +
            '</td><td><a class="pull-right" href="javascript:;" onclick="deleteRow(jQuery(this));"><i class="remove-icon"></i></a></td></tr>';
    jQuery('#commissionDetails').append(caseCommissionsNewRow);
    partnerLookup('commissionBenifitiary_' + trCount, 'commissionBenifitiaryName_' + trCount);
    caseCommissionsFormValidate();
}
function deleteRow(RowJqObject) {
    RowJqObject.parent().parent().remove();
    if (jQuery('#commissionDetails').html() === '') {
        addPartnerCommissionRow();
    }
}
function partnerLookup(commissionBenifitiary, commissionBenifitiaryName) {
    jQuery("#" + commissionBenifitiaryName).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL('money') + 'accounts/lookup_partner',
                dataType: "json",
                data: request,
                success: function (data) {
                    if (data.length < 1) {
                        response([{
                                label: _lang.no_results_matched,
                                value: '',
                                record: {
                                    id: -1,
                                    term: request.term
                                }
                            }]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {
                                label: item.name + ' - ' + item.currencyCode,
                                value: item.name + ' - ' + item.currencyCode,
                                record: item
                            };
                        }));
                    }
                }, error: defaultAjaxJSONErrorsHandler
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#' + commissionBenifitiary).val(ui.item.record.id);
            } else if (ui.item.record.id === -1) {
                //quickAddNewClient(ui.item.record.term);
            }
        }
    });
}
function getCustomTranslation(val) {
    return _lang.custom[val];
}
function validateNumbers(field, rules, i, options) {
    var val = field.val();
    var decimalPattern = /^[0-9]+(\.[0-9]{1,2})?$/;
    if (!decimalPattern.test(val)) {
        return _lang.decimalAllowed;
    }
}
function slaShowLogs(caseId, event) {
    preventFormPropagation(event);
    var slaShowLogsDialog = jQuery('#slaShowLogsDialog');
    jQuery.ajax({
        url: getBaseURL() + 'cases/show_sla_working_hours/' + caseId,
        dataType: 'JSON',
        success: function (response) {
            if (response.result) {
                slaShowLogsDialog.html(response.html).removeClass('d-none');
                ;
                jQuery('.modal', slaShowLogsDialog).modal({
                    keyboard: false
                });
            } else {
                pinesMessage({ty: 'warning', m: _lang.slaPrefNotDefined});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function slaShowLogs(caseId, event) {
    preventFormPropagation(event);
    var slaShowLogsDialog = jQuery('#slaShowLogsDialog');
    jQuery.ajax({
        url: getBaseURL() + 'cases/show_sla_working_hours/' + caseId,
        dataType: 'JSON',
        beforeSend: function () {
            jQuery("#loader-global").show();
        },
        success: function (response) {
            jQuery("#loader-global").hide();
            if (response.result) {
                slaShowLogsDialog.html(response.html).removeClass('d-none');
                ;
                jQuery('.modal', slaShowLogsDialog).modal();
            } else {
                pinesMessage({ty: 'warning', m: _lang.slaPrefNotDefined});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: 'id', title: ' ', filterable: false, sortable: false, template: '<input type="checkbox" name="caseIds[]" title="'+_lang.archiveCheckboxTitle+'" id="caseId_#= id #" value="#= id #" onchange="enableDisable_tooltips(this);" />' +
                    '#= channel == "CP" || visibleToCP == "1" ? \'<span class="flag-green" title="\' + visibleFromCP + \'"></span>\' : \'\'#' +
                    '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'cases/edit/#= id #">' + _lang.viewEdit + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="logActivityDialog(false, {legalCaseLookupId: \'#= id #\', legalCaseLookup: \'#= caseID #\', legalCaseSubject: \'#= addslashes(encodeURIComponent(subject)) #\', billable: \'#= timeTrackingBillable #\', clientName: \'#= clientName #\', modifiedByName: \'#= modifiedByName #\', createdOn: \'#= convert(createdOn).date #\', createdByName: \'#= createdByName #\', modifiedOn: \'#= convert(modifiedOn).date #\', status: \'#= status #\', clientId: \'#= client_id #\'},\'hideCase\');">' + _lang.log_time + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="TimerForm(null, \'\\add\', {legal_case_id: \'#= id #\', subject: \'#= caseID +\'\\: \'+addslashes(encodeURIComponent(subject)) #\'});">' + _lang.start_timer + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="archiveUnarchiveCase(\'#= id #\',\'#= archived #\');">#= (archived == \'no\') ? \''+_lang.archive+'\' : \''+_lang.unarchive+'\'  # </a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="recordRelatedExpense(\'#= id #\');">' + _lang.recordExpense + '</a>' +
                    '<a class="dropdown-item"  href="' + getBaseURL() + 'cases/export_to_word/#= id #">' + _lang.exportToWord + '</a>' +
                    '# if (channel != "CP") { # <a class="dropdown-item" href="javascript:;" onclick="showMatterInCustomerPortal(\'#= id #\', event);">#= visibleToCP == 1 ? _lang.hideInCustomerPortal : _lang.showMatterInCustomerPortal #</a> #} #'+
                    '<a class="dropdown-item" style="#= (slaFeature == \'yes\') ? \'\' : \'display: none;\'  #" href="javascript:;" onclick="slaShowLogs(\'#= id #\', event);">' + _lang.showSLAElapsedTime + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteCaseRecord(\'#= id #\');">' + _lang.delete + '</a>' +
                    '</div></div>', width: '75px', attributes:{class: "flagged-gridcell"}
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, filterable: false, title: _lang.id, template: '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= caseID #</a>', width: '120px'};
            } else if (item === 'subject') {
                array_push = {field: item, title: _lang.subject_Case, template: '<a href="' + getBaseURL() + 'cases/edit/#= id #"><bdi>#=  replaceHtmlCharacter(addslashes(subject)).replace("\\\\\\\'", "\\\'") #</bdi></a><i class="iconLegal iconPrivacy#=private#"></i>', width: '220px'};
            } else if (item === 'latest_development') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.latestDevelopment, template: '#= getLatestDevelopment(latest_development) #', width: '350px'};
            } else if (item === 'assignee') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.assigneeCaseMatter, template: '#= (assignee!=null && userStatus=="Inactive")? assignee+" ("+_lang.custom[userStatus]+")":((assignee!=null)?assignee:"") #', width: '192px'};
            } else if (item === 'priority') {
                array_push = {field: item, title: _lang.priority, template: '#= getCustomTranslation(priority) #', width: '90px'};
            } else if (item === 'statusComments') {
                array_push = {field: item, title: _lang.status_comments, template: '#= (statusComments!=null&&statusComments!="") ? statusComments.substring(0,40)+"..." : ""#', width: '320px'};
            }
            else if (item === 'caseArrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.arrival_date, width: '140px', template: "#= (caseArrivalDate == null) ? '' : kendo.toString(caseArrivalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'arrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '120px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'dueDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '158px', template: "#= (dueDate == null) ? '' : kendo.toString(dueDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'caseClientPosition') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientPosition_Case, width: '140px'};
            }
            else if (item === 'caseSuccessProbability') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.successProbability_case, width: '165px'};
            }
            else if (item === 'opponentNames') {
                array_push = {encoded: false, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, field: item, title: _lang.opponents, width: '292px', template: "#= (opponentNames == null) ? '' : opponentNames #"};
            }
            else if (item === 'estimatedEffort') {
                array_push = {field: item, title: _lang.estEffort, template: '#= jQuery.fn.timemask({time: estimatedEffort}) #', width: '151px'};
            }
            else if (item === 'effectiveEffort') {
                array_push = {field: item, title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort}) #', width: '170px'};
            }
            else if (item === 'caseValue') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.caseValue, template: "#= (caseValue==null)?'':kendo.toString(caseValue, \"n2\") #", width: '182px'};
            }
            else if (item === 'judgmentValue') {
                array_push = {field: item, title: _lang.judgmentValue, template: "#= (judgmentValue==null)?'':kendo.toString(judgmentValue, \"n2\") #", width: '168px'};
            }
            else if (item === 'recoveredValue') {
                array_push = {field: item, title: _lang.recoveredValue, template: "#= (recoveredValue==null)?'':kendo.toString(recoveredValue, \"n2\") #", width: '154px'};
            }
            else if (item === 'archivedCases') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.archived, template: '#= getCustomTranslation(archivedCases) #', width: '100px'};
            }
            else if (item === 'type') {
                array_push = {field: item, title: _lang.caseType, width: '170px'};
            }
           /* else if (item === 'clientName') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientName_Case, width: '175px'};
            }*/
            else if (item === 'status') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.workflow_status, width: '182px'};
            }
            else if (item === 'providerGroup') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'internalReference') {
                array_push = {field: item, template: '#= (internalReference==null)?"":internalReference #' +
                '# if(internalReference!=null && allowInternalRefLink==1) { # <a href="javascript:;" class="icon-alignment no-border m-0 p-0 btn btn-link" onclick="internalRefNumLink(\'#= internalRefLink #\', \'#= internalReference #\');"><i class="fa fa-external-link"></i></a> # } #' +
                '', sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'caseStage') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation('litigationCaseStage'), width: '182px', template: "#= caseStage && stage_id ? '<a href=\"' + getBaseURL() + 'cases/events/' + id + '?stage=stage-' + stage_id + '-container' + '\">' + caseStage + '</a>' : caseStage ? '<a href=\"' + getBaseURL() + 'cases/events/' + id + '\">' + caseStage + '</a>' : '' #"};
            }
            else if (item === 'requestedByName') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'client_foreign_name') {
                array_push = {field: item, title: _lang.clientForeignName, width: '170px'};
            }
            else if (item === 'opponent_foreign_name') {
                array_push = {field: item, title: _lang.opponentForeignName, width: '170px'};
            }
            else if(item === 'last_hearing') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation('last_hearing'), width: '182px', template: "#= (kendo.toString((hijriCalendarEnabled == 1 ? gregorianToHijri(last_hearing, true) : (last_hearing != null ? last_hearing : '')), 'yyyy-MM-dd')) #"};
            }else {
                array_push = {field: item, title: getTranslation(item), width: '182px'};
            }
            tableColumns.push(array_push);
        });
    }
    casesSearchDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    if ($response.result != undefined && !$response.result) {
                        if ($response.gridDetails != undefined) {
                            setGridDetails($response.gridDetails);
                        }
                        if ($response.feedbackMessage != undefined) {
                            pinesMessage({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                        } else {
                            pinesMessage({ty: 'error', m: _lang.updatesFailed});
                        }
                    }
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if(hasAccessToExport!=1){
                                pinesMessage({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            }else{
                                if($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportCasesToExcel(true);
                                    } else {
                                        exportCasesToExcel();
                                    }
                                }else {
                                    applyExportingModuleMethod(this);
                                }
                            }
                        });
                        gridEvents();
                        loadExportModalRanges($response.totalRows);
                    }
                    if (gridAdvancedSearchLinkState) {
                        gridAdvancedSearchLinkState = false;
                    } else {
                        if (jQuery('#filtersFormWrapper').is(':visible')) {
                            jQuery('#filtersFormWrapper').slideUp();
                            scrollToId('#filtersFormWrapper');
                        }
                    }
                    jQuery('#selectAllCheckboxes').attr('checked', false);
                    disabledUnArchiveBtn();
                    animateDropdownMenuInGrids('legalCaseGrid');
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" == operation) {
                    options.loadWithSavedFilters = 0;
                    if (gridSavedFiltersParams) {
                        options.filter = gridSavedFiltersParams;
                        var gridFormData = [];
                        gridFormData.formData = ["gridFilters"];
                        gridFormData.formData.gridFilters = gridSavedFiltersParams;
                        setGridFiltersData(gridFormData, 'legalCaseGrid');
                        options.loadWithSavedFilters = 1;
                        options.take = gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize;
                        gridSavedFiltersParams = '';
                    } else {
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
                    options.sortData = JSON.stringify(casesSearchDataSrc.sort());
                    jQuery('#sortablesForExport', '#exportResultsForm').val(JSON.stringify(options.sort, null, 2));
                }
                return options;
            }
        },
        schema: {
            type: "json",
            data: "data",
            total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {type: "integer"},
                    status: {type: "string"},
                    type: {type: "string"},
                    providerGroup: {type: "string"},
                    assignee: {type: "string"},
                    subject: {type: "string"},
                    caseArrivalDate: {type: "date"},
                    category: {type: "string"},
                    caseValue: {type: "number"},
                    private: {type: "string"},
                    clientName: {type: "string"},
                    timeTrackingBillable: {type: "string"},
                    latest_development: {type: "string"},
                    priority: {type: "string"},
                    arrivalDate: {type: "date"},
                    dueDate: {type: "date"},
                    statusComments: {type: "string"},
                    recoveredValue: {type: "number"},
                    judgmentValue: {type: "number"},
                    archivedCases: {type: "string"},
                    internalReference: {type: "string"},
                    caseStage: {type: "string"}
                }
            },
            parse: function(response) {
                var rows = [];
                if(response.data){
                    var data = response.data;
                    rows = response;
                    rows.data = [];
                    for (var i = 0; i < data.length; i++) {
                        var row = data[i];
                        row['assignee'] = escapeHtml(row['assignee']);
                        row['subject'] = escapeHtml(row['subject']);
                        row['statusComments'] = escapeHtml(row['statusComments']);
                        row['latest_development'] = escapeHtml(row['latest_development']);
                        row['opponentNames'] = escapeHtml(row['opponentNames']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultAjaxJSONErrorsHandler(e.xhr);
        },
        pageSize: gridSavedPageSize ? gridSavedPageSize : gridDefaultPageSize,
        serverPaging: true,
        serverFiltering: true,
        serverSorting: true,
        sort: jQuery.parseJSON(gridSavedColumnsSorting || "null"),
    });
    casesSearchGridOptions = {
        autobind: true,
        dataSource: casesSearchDataSrc,
        columns: tableColumns,
        editable: false,
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 20, 50, 100], refresh: true},
        reorderable: true,
        resizable: true,
        scrollable: true,
        selectable: "single",
        // sortable: {
        //     mode: "multiple"
        // },
        toolbar: [{
                name: "toolbar-menu",
                template: '<div></div>'

            }],
        columnResize: function () {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    gridTriggers({'gridContainer': casesGrid, 'gridOptions': casesSearchGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
    var grid = casesGrid.data('kendoGrid');
    grid.thead.find("th:first").append(jQuery('<input id="selectAllCheckboxes" class="selectAll" type="checkbox" title="' + _lang.selectAllRecords + '" onclick="checkUncheckAllCheckboxes(this);" />'));
    grid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    displayColHeaderPlaceholder();
}

function internalRefNumLink(targeturl, internalRefNum) {
    var input = document.body.appendChild(document.createElement("input"));
    input.value = internalRefNum;
    input.focus();
    input.select();
    document.execCommand('copy');
    input.parentNode.removeChild(input);
    window.open(targeturl, '_blank');
}

function getLatestDevelopment(value)
{
    return ( value != null && value != "" ) ? ("<span class='tooltip-title' title='" + value + "'>" + helpers.truncate(value, 50, true,   _lang.languageSettings['langDirection'] === 'rtl') + "</span>" ) : "";
}
