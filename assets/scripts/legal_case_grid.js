var gridId = 'legalCaseGrid';
var casesGrid = null;
var enableQuickSearch = false;
notificationsNoteTemplate = '', authIdLoggedIn = '';
var unsaved = false;
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
    makeFieldsDatePicker({fields: ['arrivalDateValue', 'caseArrivalDateValue', 'dueDateValue', 'arrivalDateEndValue', 'caseArrivalDateEndValue', 'dueDateEndValue', 'closedOnValue', 'closedOnEndValue', 'sentenceDateValue', 'sentenceDateEndValue', 'modifiedOnValue', 'modifiedOnEndValue', 'createdOnValue', 'createdOnEndValue']});
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
    newFormFilter.attr('action', getBaseURL() + 'export/legal_matter').submit();
}
function checkWhichTypeOfFilterIUseAndReturnFilters() {
    var filtersForm = jQuery('#caseSearchFilters');
    disableEmpty(filtersForm);
    var searchFilters = form2js('caseSearchFilters', '.', true);
    var filters = '';
    if (!enableQuickSearch) {
        filters = searchFilters.filter;
        filters.customFields = searchFilters.customFields;
    } else if (jQuery('#quickSearchFilterCaseValue', '#caseSearchFilters').val() || jQuery('#quickSearchFiltercategoryValue', '#caseSearchFilters').val() || jQuery('#quickSearchFilterDescriptionValue', '#caseSearchFilters').val()) {
        filters = searchFilters.quickSearch;
    }
    enableAll(filtersForm);
    return filters;
}
function uploadDocumentDone(message, type, caseCommentId, caseEdit) {
    if (undefined == caseCommentId)
        caseCommentId = 0;
    if (undefined == caseEdit)
        caseEdit = false;
    type = jQuery.parseJSON(type || "null");
    var msg = '';
    for (i in message) {
        msg = message[i];
        msg = msg.replace(/\\'+/g, "'");
        pinesMessageV2({ty: type[i], m: msg});
    }
    if (!caseEdit) {
        jQuery("#commentDialog").dialog("close");
        jQuery('#commentFormContainer', '#commentDialog').removeClass('d-none');
        jQuery('#loading', '#commentDialog').addClass('d-none');
        if (caseCommentId > 0){
            jQuery('.comments-lists-container','.case-notes-container').each(function(){
                jQuery(this).empty();
            });
            fetch_case_comments_tab(jQuery('#id').val(), jQuery('.case-notes-container.active'), null, jQuery('.case-notes-container.active .comments-lists-container').data('index'));
        }
    } else {
        jQuery("#commentDialogEdit").dialog("close");
        jQuery('#commentFormContainerEdit', '#commentDialogEdit').removeClass('d-none');
        jQuery('#loading', '#commentDialogEdit').addClass('d-none');
        if (caseCommentId > 0) {
            jQuery('.comments-lists-container','.case-notes-container').each(function(){
                jQuery(this).empty();
            });
            fetch_case_comments_tab(jQuery('#id').val(), jQuery('.case-notes-container.active'), null, jQuery('.case-notes-container.active .comments-lists-container').data('index'));
        }
    }
}
function caseCommentFormSubmitAndStartUpload() {
    jQuery('#comment', '#commentDialog').text(jQuery('.nicEdit-main', '#commentDialog').html());
    if (jQuery('#comment', '#commentDialog').text() == '<br>') {
        pinesMessageV2({ty: 'warning', m: _lang.commentFieldIsRequired});
        return false;
    }
    jQuery('#commentFormContainer', '#commentDialog').addClass('d-none');
    jQuery('#loading', '#commentDialog').removeClass('d-none');
    return true;
}
function addCaseDocument(caseId) {
    jQuery('#visibleStatus').val(jQuery('#isVisibleStatus').is(":checked") ? 'yes' : 'no');
    jQuery(".notificationWrapper").show();
    if (undefined == caseId) caseId = 0;
    var commentDialog = jQuery("#commentDialog");
    if (!commentDialog.is(':data(dialog)')) {
        if (caseId > 0) {
            caseCommentDialogForm(caseId);
        }
    } else {
        commentDialog.dialog("open");
    }
    jQuery('#createdOn').blur();
    if (jQuery('#innerWrap').length == 0) {
        var uiDialogButtons = jQuery('.ui-dialog-buttonpane');
        jQuery('.ui-dialog-buttonset').addClass('col-md-8');
        uiDialogButtons.addClass('col-md-12 float-right');
        uiDialogButtons.wrapInner("<div class='float-right col-md-5' id='innerWrap'></div>");
        jQuery(".btnSaveNewNote", uiDialogButtons).parent().before(notificationsNoteTemplate);
    }
    var data = {'action' : 'add_comment','legal_case_id': caseId};
    jQuery.ajax({
        url: getBaseURL().concat('cases/edit'),
        data: data,
        dataType: 'JSON',
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var addCommentId = "add-comment-dialog-container";
                jQuery('<div id="add-comment-dialog-container"></div>').appendTo("body");
                var container = jQuery("#" + addCommentId);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                jQuery('.tooltip-title', container).tooltipster();
                setDatePicker('#created-on-container', container);
                lookUpUsers(jQuery('#userFullName', container), jQuery('#user_id', container), 'created-on-container', jQuery('.created-on-container', container), container);
                jQuery('#user_id', container).val(authIdLoggedIn);
                tinymce.remove();
                tinymce.init({
                    selector: '#comment',
                    menubar: false,
                    statusbar: false,
                    branding: false,
                    height: 200,
                    resize: false,
                    plugins: ['link'],
                    toolbar: 'formatselect | bold italic underline | link | undo redo ',
                    block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                    formats: {
                        underline: {inline: 'u', exact: true}
                    },
                    content_style: ".mce-content-body {font-size:12px;font-family:Arial,sans-serif;}",
                    setup: function (editor) {
                        editor.on('init', function (e) {
                            jQuery('#comment_ifr').contents().find('body').prop("dir", "auto");
                            jQuery('#comment_ifr').contents().find('body').focus();
                        });
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });
                jQuery("#add-comment-dialog-submit", container).on('click', function () {
                   addComment(caseId);
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function addComment(caseId){
    var container = jQuery("#add-case-contact-container");
    var url = getBaseURL() + 'cases/add_comment';
    var formData = new FormData(document.getElementById(jQuery("form#caseCommentForm", container).attr('id')));
    formData.append('comment', tinymce.activeEditor.getContent());
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: url,
        data: formData,
        beforeSend: function () {
            ajaxEvents.beforeActionEvents(container);
        },
        success: function (response) {
            jQuery(".inline-error").addClass('d-none');
            if(response.status){
                if(response.warning){
                    pinesMessageV2({ty: 'warning', m: response.message});
                }
                pinesMessageV2({ty: 'success', m: _lang.record_added_successfull.sprintf([_lang.note])});
                jQuery(".comments-lists-container").empty('');
                
                fetch_case_comments_tab(caseId, jQuery('.case-notes-container.active'), null, jQuery('.case-notes-container.active .comments-lists-container').data('index'));

                jQuery('.modal').modal('hide');
            } else {
                if(response.message){
                    pinesMessageV2({ty: 'error', m: response.message});
                }
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            ajaxEvents.completeEventsAction(container);
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function editComment(caseId){
    var container = jQuery("#edit-case-contact-container");
    var url = getBaseURL() + 'cases/edit_comment';
    var formData = new FormData(document.getElementById(jQuery("form#caseCommentFormEdit", container).attr('id')));
    formData.append('commentEdit', tinymce.activeEditor.getContent());
    jQuery.ajax({
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: url,
        data: formData,
        beforeSend: function () {
            ajaxEvents.beforeActionEvents(container);
        },
        success: function (response) {
            jQuery(".inline-error").addClass('d-none');
            if(response.status){
                if(response.warning){
                    pinesMessageV2({ty: 'warning', m: response.message});
                }
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                jQuery(".comments-lists-container").empty('');
                fetch_case_comments_tab(caseId, jQuery('.case-notes-container.active'), null, jQuery('.case-notes-container.active .comments-lists-container').data('index'));
                jQuery('.modal').modal('hide');
            } else {
                if(response.message){
                    pinesMessageV2({ty: 'error', m: response.message});
                }
                displayValidationErrors(response.validationErrors, container);
            }
        }, complete: function () {
            ajaxEvents.completeEventsAction(container);
        },
        error: defaultAjaxJSONErrorsHandler
    });
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
    commentDialog.removeClass('d-none');
    commentDialog.dialog({
        autoOpen: true,
        buttons: [{
                text: _lang.save,
                'class': 'btn btn-info btnSaveNewNote pull-left',
                click: function () {
                    var commentValid = jQuery("form#caseCommentForm").validationEngine('validate');
                    if (commentValid) {
                        var commentTextarea = tinymce.activeEditor.getContent();
                        if (commentTextarea == '' || commentTextarea.length < 3) {
                            pinesMessageV2({ty: 'warning', m: _lang.noteFieldIsRequired});
                        } else {
                            //this code was added to ensure that copy and pasting from Gmail does not result in broken html - refer to A4L-3100 for more info
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
            if (jQuery('#innerWrap').length == 0) {
                var uiDialogButtons = jQuery('.ui-dialog-buttonpane');
                jQuery('.ui-dialog-buttonset').addClass('col-md-8');
                uiDialogButtons.addClass('col-md-12 float-right');
                uiDialogButtons.wrapInner("<div class='float-right col-md-5' id='innerWrap'></div>");
                jQuery(".btnSaveNewNote", uiDialogButtons).parent().before(notificationsNoteTemplate);
            }
            tinymce.init({
                selector: '#comment',
                menubar: false,
                statusbar: false,
                branding: false,
                height: 200,
                resize: false,
                plugins: ['link'],
                toolbar: 'formatselect | bold italic underline | link | undo redo ',
                block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                formats: {
                    underline: {inline: 'u', exact: true}
                },
                setup: function (editor) {
                    editor.on('init', function (e) {
                        jQuery('#comment_ifr').contents().find('body').prop("dir", "auto");
                        jQuery('#comment_ifr').contents().find('body').focus();
                    });
                }
            });
        },
        draggable: true,
        modal: false,
        resizable: true,
        title: _lang.addNewCommentTitle.sprintf([caseId, subject])
    });

}
function showCommentsToCP() {

    if (jQuery("#isVisibleStatus", "#caseCommentForm").is(":checked"))
    {
        jQuery("#visibleStatus", "#caseCommentForm").val("yes");
        jQuery(".notificationWrapper").show();
    }
    else
    {
        jQuery("#visibleStatus", "#caseCommentForm").val("no");
        jQuery("#send_notifications_email", "#caseCommentForm").val("");
        jQuery(".notificationWrapper").hide();
        jQuery('#notificationSendEmailId').attr('checked', false);
    }
}
function showCommentsToAP() {

    if (jQuery("#isVisibleToAPStatus", "#caseCommentForm").is(":checked"))
    {
        jQuery("#visibleToAPStatus", "#caseCommentForm").val("yes");
        jQuery(".notificationWrapper").show();
    }
    else
    {
        jQuery("#visibleToAPStatus", "#caseCommentForm").val("no");
        jQuery("#send_notifications_email", "#caseCommentForm").val("");
        jQuery(".notificationWrapper").hide();
        jQuery('#notificationSendEmailId').attr('checked', false);
    }
}
function editVisibilityCommentsToCP()
{

    if (jQuery("#isVisibleCP", "#caseCommentFormEdit").is(":checked"))
    {
        jQuery("#visibleStatus", "#caseCommentFormEdit").val("yes");
    }
    else
    {
        jQuery("#visibleStatus", "#caseCommentFormEdit").val("no");
    }
}
function editVisibilityCommentsToAP()
{

    if (jQuery("#isVisibleToAP", "#caseCommentFormEdit").is(":checked"))
    {
        jQuery("#visibleToAPStatus", "#caseCommentFormEdit").val("yes");
    }
    else
    {
        jQuery("#visibleToAPStatus", "#caseCommentFormEdit").val("no");
    }
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
    jQuery('#files_path').append('<div class="row help-inline col-md-12 mt-2"><div class="col-md-11 p-0 padding-right-10 pt-5"><input type="text" class="form-control" value="" name="paths[]"/></div><span class="cursor-pointer-click" type="button" onclick="jQuery(this).parent().remove();"><i class="fa fa-trash light_red-color mt-2 p-2 float-right"></i></span>&nbsp;&nbsp;<span id="addMorePath" class="col-md-12 btn-link p-0 mt-2" type="button" onclick="add_file_path()">' + _lang.addMore + '</span></div>');
    jQuery('#files_path input:last').focus();
}
function attach_file() {
    countAttachments++;
    jQuery('#files_attachment').append('<div class="extra-attachment-container flex-center-inline row m-0 p-0 w-100">' +
            '<label for="attachment_' + countAttachments + '" class="custom-file-upload flex-center-inline">\n' +
                '<i class="padding-top-0 fa fa-cloud-upload purple_color px-2"></i>' + _lang.chooseFile +
            '</label>' +
            '<input class="custom-file-upload-input" type="file" onchange="helpers.bindFileNameToUploadFile(this,\'attachment_' + countAttachments + '-selected\')"  style="width:100%"  name="attachment_' + countAttachments + '" id="attachment_' + countAttachments + '" value="" class="margin-top" />' +
            '<span id="attachment_' + countAttachments + '-selected" class="trim-width-120 v-al-n-3"></span>' +
            '<span class="flex-end-item" type="button" onclick="jQuery(this).parent().remove();"><i class="icon-alignment fa fa-trash light_red-color float-right v-al-n-3 cursor-pointer-click line-height-20 mb-2"></i></span><input type="hidden" name="attachments[]" value="attachment_' + countAttachments + '"/>\n' +
        '</div>');
    jQuery('#files_attachment input:last').focus();
}
function toggle_comment(id) {
    case_comment = jQuery('#case-comment_' + id, '.case-notes-container.active');
    if (jQuery('#commentText', case_comment).is(':visible')) {
        jQuery('#commentText', case_comment).slideUp();
        jQuery('svg', '#case-comment_' + id + ' a:first').removeClass('fa-angles-down');
        jQuery('svg', '#case-comment_' + id + ' a:first').addClass('fa-angles-right');
    } else {
        jQuery('#commentText', case_comment).slideDown();
        jQuery('svg', '#case-comment_' + id + ' a:first').removeClass('fa-angles-right');
        jQuery('svg', '#case-comment_' + id + ' a:first').addClass('fa-angles-down');
    }
}
function deleteComment(params) {
    jQuery.ajax({
        url: getBaseURL() + params.url + params.comment_id,
        method: 'get',
        dataType: 'json',
        success: function (response) {
            var ty = 'error';
            var m = '';
            switch (response.status) {
                case 500:	// remove successfuly
                    ty = 'information';
                    m = _lang.deleteRecordSuccessfull;

                    jQuery('.comments-lists-container', '.case-notes-container').each(function () {
                        jQuery(this).empty();
                    });

                    fetch_case_comments_tab(params.case_id, jQuery('.case-notes-container.active'), null, jQuery('.case-notes-container.active .comments-lists-container').data('index'));
                    break;
                case 101:	// could not delete record
                    m = _lang.deleteRecordFailed;
                    break;
                default:
                    break;
            }

            jQuery('.modal', '.confirmation-dialog-container').modal('hide');

            pinesMessageV2({ty: ty, m: m});
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function fetch_case_stages_history(caseId, fetchFromServer) {
    fetchFromServer = fetchFromServer || false;
    caseStagesHistory = jQuery('#caseStagesHistory');
    if (jQuery('#caseStagesHistory').html() == '' || fetchFromServer) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/fetch_case_stages_history/',
            data: {
                'id': caseId
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                caseStagesHistory.html(response.html);
                if (!fetchFromServer)
                    toggle_history();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        toggle_history();
    }
}
function fetch_audit_report_history(caseId, category, fetchFromServer) {
    fetchFromServer = fetchFromServer || false;
    auditReportHistory = jQuery('#auditReportHistory');
    if (auditReportHistory.html() == '' || fetchFromServer) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/fetch_audit_report_history/',
            data: {
                'id': caseId,
                'category': category
            },
            type: 'post',
            dataType: 'json',
            success: function (response) {
                auditReportHistory.html(response.html);
                if (!fetchFromServer)
                    toggle_audit_report(auditReportHistory);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        toggle_audit_report(auditReportHistory);
    }
}

function toggle_audit_report(auditReportHistory) {
    if (auditReportHistory.is(':visible')) {
        auditReportHistory.slideUp();
    } else {
        jQuery('#caseStagesHistory').slideUp();
        jQuery('#caseStagesHistoryList').removeClass('active');
        jQuery('#auditReportHistoryList').addClass('active');
        auditReportHistory.slideDown();
    }
}
function toggle_case_stages() {
    caseStagesHistory = jQuery('#caseStagesHistory');
    if (caseStagesHistory.is(':visible')) {
        caseStagesHistory.slideUp();
    } else {
        caseStagesHistory.slideDown();
        jQuery('#caseStagesHistoryList').addClass('active');
        jQuery('#auditReportHistory').slideUp();
        jQuery('#auditReportHistoryList').removeClass('active');

    }
    if (caseStagesHistory.length == 0) {
        jQuery('#auditReportHistoryHref').click();
    }
}
function editCaseStageHistory(tdJQobj) {
    var caseStageHistoryDialog = jQuery("#caseStageHistoryDialog");
    var caseStageHistoryForm = jQuery('form#caseStageHistoryForm');
    caseStageHistoryForm.validationEngine({
        validationEventTrigger: "submit",
        autoPositionUpdate: true,
        promptPosition: 'bottomRight',
        scroll: false
    });
    jQuery("#caseStageHistoryDialog").dialog({
        autoOpen: true,
        buttons: [{
                text: _lang.save,
                'class': 'btn btn-info btnSaveNewNote',
                click: function () {
                    var isValid = caseStageHistoryForm.validationEngine('validate');
                    var formData = jQuery("form#caseStageHistoryForm", this).serialize();
                    if (isValid) {
                        var that = this;
                        jQuery.ajax({
                            beforeSend: function () {
                                jQuery('#loading', caseStageHistoryDialog).removeClass('d-none');
                            },
                            data: formData,
                            dataType: 'JSON',
                            type: 'POST',
                            url: getBaseURL() + 'cases/edit_case_stage_history',
                            success: function (response) {
                                if (!response.result) {
                                    pinesMessageV2({ty: 'error', m: _lang.invalid_record});
                                    resetCaseStageHistoryFormValues();
                                    jQuery(that).dialog("close");
                                } else {
                                    pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                    fetch_case_stages_history(tdJQobj.attr('legal_case_id'), true);
                                    resetCaseStageHistoryFormValues();
                                    jQuery(that).dialog("close");
                                }
                            },
                            error: defaultAjaxJSONErrorsHandler
                        });
                    }
                }
            },
            {
                text: _lang.cancel,
                'class': 'btn btn-link',
                click: function () {
                    resetCaseStageHistoryFormValues();
                    jQuery(this).dialog("close");
                }
            }],
        close: function () {
            resetCaseStageHistoryFormValues();
            jQuery(window).unbind('resize');
        },
        open: function () {
            jQuery('input', '#caseStageHistoryDialog').blur();
            makeFieldsDatePicker({fields: ['modifiedOn'], maxDate: '0'}, '', '', '0');
            userLookup('modifiedByName', 'modifiedBy');
            jQuery('#loading', caseStageHistoryDialog).addClass('d-none');
            jQuery('#id', caseStageHistoryForm).val(tdJQobj.attr('id'));
            jQuery('#legal_case_id', caseStageHistoryForm).val(tdJQobj.attr('legal_case_id'));
            jQuery('#modifiedBy', caseStageHistoryForm).val(tdJQobj.attr('modifiedBy'));
            jQuery('#modifiedByName', caseStageHistoryForm).val(tdJQobj.attr('modifiedByName'));
            jQuery('#modifiedOn', caseStageHistoryForm).val(tdJQobj.attr('modifiedOn'));

            var that = jQuery(this);
            jQuery(this).removeClass('d-none');
            jQuery(window).bind('resize', (function () {
                resizeNewDialogWindow(that, '50%', '260');
            }));
            resizeNewDialogWindow(that, '50%', '260');
        },
        draggable: true,
        modal: false,
        responsive: true,
        resizable: true,
        title: _lang.editCaseStageHistory
    });
}
function deleteCaseStageHistory(tdJQobj) {
    if (confirm(_lang.confirmationDeleteSelectedRecord)) {
        jQuery.ajax({
            data: {id: tdJQobj.attr('id')},
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'cases/delete_case_stage_history',
            success: function (response) {
                if (!response.result) {
                    pinesMessageV2({ty: 'error', m: _lang.recordNotDeleted});
                } else {
                    pinesMessageV2({ty: 'success', m: _lang.deleteRecordSuccessfull});
                    tdJQobj.parent().remove();
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else
        return false;
}
function resetCaseStageHistoryFormValues() {
    jQuery('#id', caseStageHistoryForm).val('');
    jQuery('#legal_case_id', caseStageHistoryForm).val('');
    jQuery('#modifiedBy', caseStageHistoryForm).val('');
    jQuery('#modifiedByName', caseStageHistoryForm).val('');
    jQuery('#modifiedOn', caseStageHistoryForm).val('');
    jQuery("form#caseStageHistoryForm", caseStageHistoryDialog).validationEngine('hide');
}
function toggle_notes() {
    var notesContainer = jQuery('#notes');
    var notesToggleIcon = jQuery('#notesToggleIcon');
    if (notesContainer.is(':visible')) {
        notesContainer.slideUp();
        notesToggleIcon.removeClass('fa-solid fa-angle-down');
        notesToggleIcon.addClass('fa-solid fa-angle-right');
    } else {
        notesContainer.slideDown();
        notesToggleIcon.removeClass('fa-solid fa-angle-right');
        notesToggleIcon.addClass('fa-solid fa-angle-down');
    }
}
function toggle_history() {
    var historyContainer = jQuery('#history');
    var historyToggleIcon = jQuery('#historyToggleIcon');
    if (historyContainer.is(':visible')) {
        historyContainer.slideUp();
        historyToggleIcon.removeClass('fa-solid fa-angle-down');
        historyToggleIcon.addClass('fa-solid fa-angle-right');
    } else {
        historyContainer.slideDown();
        historyToggleIcon.removeClass('fa-solid fa-angle-right');
        historyToggleIcon.addClass('fa-solid fa-angle-down');
        toggle_case_stages();
    }
}

function toggle_case_notes_tabs(case_id) {
    var notes_tabs_container = jQuery('#case_notes_tabs');
    var notes_tabs_toggle_icon = jQuery('#case_notes_tabs_toggle_icon');
    if (notes_tabs_container.is(':visible')) {
        notes_tabs_container.slideUp();
        notes_tabs_toggle_icon.removeClass('fa-solid fa-angle-down');
        notes_tabs_toggle_icon.addClass('fa-solid fa-angle-right');
    } else {
        notes_tabs_container.slideDown();
        notes_tabs_toggle_icon.removeClass('fa-solid fa-angle-right');
        notes_tabs_toggle_icon.addClass('fa-solid fa-angle-down');

        // init the default active tab
        fetch_case_comments_tab(case_id, jQuery('#case_notes_all_threads'), null, 1);
    }
}
/**
 * 
 * @param {type} case_id
 * @param {type} tab
 * @param {type} url
 * @param {type} tab_index
 * @return {undefined}
 */
function fetch_case_comments_tab(case_id, tab, url, tab_index) {

    var comments_container = tab.find('.comments-lists-container');

    if (url == null) {
        switch (tab_index) {
            case 1:
                url = 'cases/get_all_comments';
                break;
            case 2:
                url = 'cases/get_all_core_and_cp_comments';
                break;
            case 3:
                url = 'cases/get_all_email_comments';
                break;
            default:
                url = 'cases/get_all_comments';
        }
    }

    if (comments_container.html() == '') {
        jQuery.ajax({
            url: getBaseURL() + url,
            type: 'post',
            data: {
                'id': case_id
            },
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    toggle_case_comments_tab(tab, tab_index);

                    comments_container.html(response.html).attr('data-index', tab_index);

                    generate_pagination_links();
                } else if (!response.status && response.module_expired) {
                    jQuery('#module_expired_modal').modal('show');
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        toggle_case_comments_tab(tab, tab_index);
    }
}

function toggle_case_comments_tab(tab, index) {
    jQuery('.case-notes-container').each(function () {
        jQuery(this).addClass('hidden').removeClass('active');
    });

    tab.removeClass('hidden').addClass('active');

    jQuery('#case_notes_tabs > ul > li:not([data-index="' + index + '"])').each(function () {
        jQuery(this).removeClass('active');
    });

    jQuery('#case_notes_tabs > ul > li[data-index="' + index + '"]').addClass('active');
}

function expandAllNotes() {
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', jQuery('.case-notes-container.active'))).slideDown();
    jQuery('a > i.fa-angle-right', jQuery('.case-notes-container.active')).removeClass('fa-angle-right').addClass('fa-angle-down');
}
function collapseAllNotes() {
    //hide up all notes
    jQuery('span#commentText', jQuery('div.commentsList', jQuery('.case-notes-container.active'))).slideUp();
    jQuery('a > i.fa-angle-down', jQuery('.case-notes-container.active')).removeClass('fa-angle-down').addClass('fa-angle-right');
}
function expandAllEmailNotes() {
    //hide up all notes
    jQuery('.case-comments-emails-table tr.comment-container').each(function () {
        jQuery(this).find('td').slideDown(300).css({'display': 'table-cell'});
    });
    jQuery('i.fa-angle-right', jQuery('.case-notes-container.active')).removeClass('fa-angle-right').addClass('fa-angle-down');
}
function collapseAllEmailNotes() {
    //hide up all notes
    jQuery('.case-comments-emails-table tr.comment-container').each(function () {
        jQuery(this).find('td').slideUp(100);
    });
    jQuery('i.fa-angle-down', jQuery('.case-notes-container.active')).removeClass('fa-angle-down').addClass('fa-angle-right');
}

function get_case_comments(url, container) {
    jQuery.ajax({
        url: url,
        data: {'id': jQuery('#id').val()},
        type: 'post',
        dataType: 'json',
        beforeSend: function () {
            container.html('<div id="loading" align="center"><img src="assets/images/icons/16/loader-submit.gif" width="23" height="16" /></div>');
        },
        success: function (response) {
            container.html(response.html);
            generate_pagination_links();
        }, error: defaultAjaxJSONErrorsHandler
    });
}

function generate_pagination_links() {
    jQuery('a', '.case-notes-container:not(.hidden) .comments-lists-container .caseCommentsPagination').each(function () {
        aSearchHref = jQuery(this).attr('href');
        jQuery(this).attr('onclick', "get_case_comments('" + aSearchHref + "'," + "jQuery('.case-notes-container:not(.hidden) .comments-lists-container .caseCommentsPagination').parent()" + ');');
        jQuery(this).attr('href', 'javascript:;');
    });
}

function editCaseCommentFormSubmitAndStartUpload() {
    jQuery('#commentEdit', '#commentDialogEdit').text(jQuery('.nicEdit-main', '#commentDialogEdit').html());
    if (jQuery('#commentEdit', '#commentDialogEdit').text() == '') {
        pinesMessageV2({ty: 'warning', m: _lang.noteFieldIsRequired});
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
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + 'cases/edit_comment/' + id + '/' + case_id,
        data: {'action' : 'add_comment','legal_case_id': case_id},
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.status) {
                var editCommentId = "edit-case-contact-container";
                jQuery('<div id="edit-case-contact-container"></div>').appendTo("body");
                var container = jQuery("#" + editCommentId);
                container.html(response.html);
                initializeModalSize(container, 0.35, 'auto');
                commonModalDialogEvents(container);
                jQuery('.tooltip-title', container).tooltipster();
                setDatePicker('#created-on-container', container);
                lookUpUsers(jQuery('#userFullNameEdit', container), jQuery('#user_idEdit', container), 'created-on-container', jQuery('.created-on-container', container), container);
                tinymce.remove();
                tinymce.init({
                    selector: '#commentEdit',
                    menubar: false,
                    statusbar: false,
                    branding: false,
                    height: 200,
                    resize: false,
                    plugins: ['link'],
                    toolbar: 'formatselect | bold italic underline | link | undo redo ',
                    block_formats: 'Paragraph=p;Heading 1=h1;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;',
                    formats: {
                        underline: {inline: 'u', exact: true}
                    },
                    content_style: ".mce-content-body {font-size:12px;font-family:Arial,sans-serif;}",
                    setup: function (editor) {
                        editor.on('init', function (e) {
                            jQuery('#comment_ifr').contents().find('body').prop("dir", "auto");
                            jQuery('#comment_ifr').contents().find('body').focus();
                        });
                        editor.on('change', function () {
                            editor.save();
                        });
                    }
                });
                jQuery("#edit-comment-dialog-submit", container).on('click', function () {
                    editComment(case_id);
                });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });

    //
    // jQuery.ajax({
    //     url: getBaseURL() + 'cases/edit_comment/' + id + '/' + case_id,
    //     data: {},
    //     type: 'post',
    //     dataType: 'json',
    //     success: function (response) {
    //         commentDialogEdit.dialog({
    //             autoOpen: true,
    //             buttons: [{
    //                     text: _lang.save,
    //                     'class': 'btn btn-info',
    //                     click: function () {
    //                         var commentValid = jQuery("form#caseCommentFormEdit").validationEngine('validate');
    //                         if (commentValid) {
    //                             var commentTextarea = tinymce.activeEditor.getContent();
    //                             if (commentTextarea == '' || commentTextarea.length < 3) {
    //                                 pinesMessage({ty: 'warning', m: _lang.noteFieldIsRequired});
    //                             } else {
    //                                 jQuery('form#caseCommentFormEdit').submit();
    //                             }
    //                         }
    //                     }
    //                 },
    //                 {
    //                     text: _lang.cancel,
    //                     'class': 'btn btn-link',
    //                     click: function () {
    //                         jQuery('#commentEdit', commentDialogEdit).text('');
    //                         jQuery('#files_path', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="add_file_pathEdit()">Add file path</button></div>');
    //                         jQuery('#files_attachment', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="attach_fileEdit()">Attach a file</button></div>');
    //                         jQuery(this).dialog("close");
    //                     }
    //                 }],
    //             close: function () {
    //                 jQuery('#commentEdit', commentDialogEdit).text('');
    //                 jQuery('#files_path', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="add_file_pathEdit()">Add file path</button></div>');
    //                 jQuery('#files_attachment', commentDialogEdit).html('<div class="col-md-12"><button class="btn btn-default btn-link" type="button" onclick="attach_fileEdit()">Attach a file</button></div>');
    //                 jQuery(this).dialog("close");
    //             },
    //             open: function () {
    //             },
    //             draggable: true,
    //             modal: false,
    //             resizable: true,
    //             title: _lang.editComment.sprintf([case_id, subject])
    //         });
    //         commentDialogEdit.html(response.html);
    //         jQuery("form#caseCommentFormEdit", commentDialogEdit).validationEngine({
    //             validationEventTrigger: "submit",
    //             autoPositionUpdate: true,
    //             promptPosition: 'bottomRight',
    //             scroll: false
    //         });
    //         jQuery('#caseIdEdit').val(case_id);
    //         makeFieldsDatePicker({fields: ['createdOnEdit']});
    //         userLookup('userFullNameEdit', 'user_idEdit', '', 'active');
    //         jQuery('#createdOnEdit').blur();
    //         jQuery(window).bind('resize', (function () {
    //             resizeNewDialogWindow(commentDialogEdit, '80%', '460');
    //         }));
    //         resizeNewDialogWindow(commentDialogEdit, '80%', '460');
    //     },
    //     error: defaultAjaxJSONErrorsHandler
    // });
}
function add_file_pathEdit() {
    jQuery('#addMorePathEdit').remove();
    jQuery('#files_pathEdit').append('<div class="row help-inline col-md-12 mt-2"><div class="col-md-10 p-0 pt-5"><input type="text" class="form-control" value="" name="paths[]"/></div><span class="cursor-pointer-click" type="button" onclick="jQuery(this).parent().remove();"><i class="fa fa-trash light_red-color mt-2 p-2 float-right"></i></span>&nbsp;&nbsp;<span id="addMorePathEdit" class="col-md-12 btn-link p-0" type="button" onclick="add_file_pathEdit()">' + _lang.addMore + '</span></div>');
    jQuery('#files_pathEdit input:last').focus();
}
function attach_fileEdit() {
    countAttachments++;
    jQuery('#addMoreAttachmentEdit').remove();
    jQuery('#files_attachmentEdit').append('<div class="extra-attachment-container flex-center-inline row m-0 p-0 w-100">' +
        '<label for="attachment_' + countAttachments + '" class="custom-file-upload flex-center-inline">\n' +
        '<i class="padding-top-0 fa fa-cloud-upload purple_color px-2"></i>' + _lang.chooseFile +
        '</label>' +
        '<input class="custom-file-upload-input" type="file" onchange="helpers.bindFileNameToUploadFile(this,\'attachment_' + countAttachments + '\')"  style="width:100%"  name="attachment_' + countAttachments + '" id="attachment_' + countAttachments + '" value="" class="margin-top" />' +
        '<span id="attachment_' + countAttachments + '" class="trim-width-120 v-al-n-3"></span>' +
        '<span class="flex-end-item" type="button" onclick="jQuery(this).parent().remove();"><i class="icon-alignment fa fa-trash light_red-color float-right v-al-n-3 cursor-pointer-click line-height-20 mb-2"></i></span><input type="hidden" name="attachments[]" value="attachment_' + countAttachments + '"/>\n' +
        '</div>');
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
                pinesMessageV2({ty: ty, m: m});
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
function archiveSelectedCases() {
    if (confirm(_lang.confirmationArchiveCases)) {
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
}
function enableDisableUnarchivedButton(statusChkBx) {

    if (statusChkBx.checked) {
        jQuery('#unarchivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip').attr('title', '');
    }
    else if (!statusChkBx.checked && (jQuery("tbody" + " INPUT[type='checkbox']:checked").length == 0)) {
        disabledUnArchiveBtn();
    }
}

function enableDisableArchivedButton(statusChkBx) {
    if (statusChkBx.checked) {
        jQuery('#archivedButtonId').removeClass('disabled');
        jQuery('#archive_tooltip_matter').attr('title','');
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
        jQuery('#archive_tooltip').attr('title', '');
        jQuery('#archive_tooltip_litigation').attr('title', '');
        jQuery('#archive_tooltip_matter').attr('title', '');
    } else {
        disabledUnArchiveBtn();
        disabledArchiveBtn();
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
    statusTopNavContainer = jQuery('#status-top-nav-container');
    topHeaderIconsContainer = jQuery('.top-header-icons-container');
    topRightSaveBtn = jQuery('.top-right-save-btn');
    caseFormElement = jQuery('form#legalCaseAddForm', newCaseFormDialog)
    if('undefined' !== typeof(disableMatter) && disableMatter){
        disableFields(newCaseFormDialog);
        disableFields(statusTopNavContainer);
        disableFields(topHeaderIconsContainer);
        disableFields(topRightSaveBtn);
    }
    try {
        setTimeout(function () {
            if (licenseHasExpired) {
                disableExpiredFields(newCaseFormDialog);
                jQuery(':submit').click(function () {
                    alertLicenseExpirationMsg();
                    return false;
                });
            }
        }, 200);

        if (casesGrid.length > 0) {
            gridFiltersEvents('Matter', 'legalCaseGrid', 'caseSearchFilters');
            gridInitialization();
            jQuery('.k-pager-refresh-top', '.k-grid-info-refresh-top').on('click', function () {
                casesGrid.data('kendoGrid').dataSource.read();
            });
            jQuery('#caseSearchFilters').bind('submit', function (e) {
                jQuery("form#caseSearchFilters").validationEngine({
                    validationEventTrigger: "submit",
                    autoPositionUpdate: true,
                    promptPosition: 'bottomRight',
                    scroll: false
                });
                if (!jQuery('form#caseSearchFilters').validationEngine("validate")) {
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
            // jQuery("#provider_group_id").chosen().change(function () {
            //     unsaved = true;
            // });
            // jQuery("#userId").chosen().change(function () {
            //     unsaved = true;
            // });

            jQuery(window).bind('beforeunload', function (event) {
                if (jQuery('.changed', "form#legalCaseAddForm").length > 0 || jQuery('.k-dirty').length > 0 || unsaved) {
                    return _lang.warning_you_have_unsaved_changes;
                }
            });
            jQuery("form#legalCaseAddForm").dirty_form({
                includeHidden: true
            }).submit(function (e) {
                jQuery(window).unbind('beforeunload');
                unsaved = false;
            });
            bindNewCaseFormEvents(true, "legalCaseAddForm");
//			if (caseFormElement.hasClass('editmode')) bindEditModeEvents();
            ctrlS(function () {
                caseFormElement.submit();
            });
        }
    } catch (e) {
        bindNewCaseFormEvents(true, "legalCaseAddForm");
//		if (caseFormElement.hasClass('editmode')) bindEditModeEvents();
        ctrlS(function () {
            caseFormElement.submit();
        });
    }
    
    jQuery('.effective-effort-tooltip').tooltipster({
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
});
function isFormChanged() {
    if (jQuery('.changed', "form#legalCaseAddForm").length > 0 && confirm(_lang.warning_you_have_unsaved_changes_save_now)) {
        jQuery('form#legalCaseAddForm').attr('action', jQuery('form#legalCaseAddForm').attr('action') + '/print').submit();
        return false;
    }
    return true;
}
function resetconvertLitigationDialogue() {
    jQuery('#case_type_id', '#convertLitigationDialogue').val('');
    jQuery('#case_stage_id', '#convertLitigationDialogue').val('');
}
function convertToLitigation($caseId, $caseTypeId, $caseStage, $isFormLoaded, $isEdit) {
    $isFormLoaded = $isFormLoaded || false;
    $isEdit = $isEdit || false;
    var convertLitigationDialogue = jQuery("#convertLitigationDialogue");
    if ($isFormLoaded == false) {
        jQuery.ajax({
            dataType: 'JSON',
            type: 'POST',
            url: getBaseURL() + 'cases/convert_case_to_litigation',
            success: function (response) {
                if (response.status) {
                    if (convertLitigationDialogue.length) {
                        convertLitigationDialogue.remove();
                    }
                    console.log(response.html);
                    jQuery('<div id="convertLitigationDialogue" class="d-none"></div>').html(response.html).appendTo('body');
                    convertToLitigation($caseId, $caseTypeId, $caseStage, true, $isEdit);
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else {
        jQuery("form#convertLitigationForm").validationEngine({
            validationEventTrigger: "submit",
            autoPositionUpdate: true,
            promptPosition: 'bottomRight',
            scroll: false
        });
        convertLitigationDialogue.dialog({
            autoOpen: true,
            buttons: [{
                    text: _lang.save,
                    'class': 'btn btn-info btnSaveNewNote pull-left',
                    click: function () {
                        var commentValid = jQuery("form#convertLitigationForm").validationEngine('validate');
                        if (commentValid) {
                            var caseType = jQuery('#case_type_id', '#convertLitigationDialogue').val();
                            var caseStage = jQuery('#case_stage_id', '#convertLitigationDialogue').val();
                            jQuery.ajax({
                                url: getBaseURL() + 'cases/convert_case_to_litigation/' + $caseId,
                                data: {
                                    'caseType': caseType,
                                    'caseStage': caseStage
                                },
                                type: 'POST',
                                dataType: 'JSON',
                                success: function (response) {
                                    switch (response.status) {
                                        case 202:	// update successfuly
                                            resetconvertLitigationDialogue();
                                            jQuery(convertLitigationDialogue).dialog("close");
                                            if ($isEdit)
                                                location.reload();
                                            else
                                                $legalCaseGrid.data("kendoGrid").dataSource.read();
                                            break;
                                        case 101:	// could not save changes
                                            m = _lang.feedback_messages.updatesFailed;
                                            pinesMessageV2({ty: 'error', m: m});
                                            break;
                                        default:
                                            break;
                                    }
                                },
                                beforeSend: function(){
                                    checkRelatedFields({case_id : $caseId ,category:'matter', new_category: 'litigation', old_type: $caseTypeId, new_type: jQuery('#case_type_id', '#convertLitigationDialogue').val()});
                                },
                                error: defaultAjaxJSONErrorsHandler
                            });
                        }
                    }
                },
                {
                    text: _lang.cancel,
                    'class': 'btn btn-link pull-left',
                    click: function () {
                        resetconvertLitigationDialogue();
                        jQuery(this).dialog("close");
                    }
                }],
            close: function () {
                resetconvertLitigationDialogue();
            },
            open: function () {
                var that = jQuery(this);
                that.removeClass('d-none');
                if (jQuery("#case_type_id option[value='" + $caseTypeId + "']").length > 0)
                    jQuery('#case_type_id', '#convertLitigationDialogue').val($caseTypeId);
                if (jQuery("#case_stage_id option[value='" + $caseStage + "']").length > 0)
                    jQuery('#case_stage_id', '#convertLitigationDialogue').val($caseStage);
                jQuery(window).bind('resize', (function () {
                    resizeNewDialogWindow(convertLitigationDialogue, '50%', '200');
                }));
                resizeNewDialogWindow(convertLitigationDialogue, '50%', '200');
            },
            draggable: false,
            modal: false,
            resizable: false,
            title: _lang.convertToLitigation
        });
        convertLitigationDialogue.dialog("open");
    }
}
function recordCaseExpense($caseId) {
    window.location = getBaseURL('money') + 'vouchers/case_expenses_add/' + $caseId;
}
function caseCommissions(case_id, event) {
    preventFormPropagation(event);
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
                                                pinesMessageV2({ty: 'error', m: _lang.invalid_record});
                                                jQuery(that).dialog("close");
                                            } else {
                                                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
                                                jQuery(that).dialog("close");
                                            }
                                        },
                                        error: defaultAjaxJSONErrorsHandler
                                    });
                                } else {
                                    pinesMessageV2({ty: 'warning', m: _lang.totalOfCommissionsInRowMustBeEqualOrLessThan100.sprintf([_lang.sharesValue])});
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
                    jQuery('tr', '#commissionDetails').each(function () {
                        trID = jQuery(this).attr('id');
                        partnerLookup('commissionBenifitiary_' + trID, 'commissionBenifitiaryName_' + trID);
                    });
                    caseCommissionsFormValidate();
                    var that = jQuery(this);
                    that.removeClass('d-none');
                    jQuery(window).bind('resize', (function () {
                        resizeNewDialogWindow(that, '45%', '400');
                    }));
                    resizeNewDialogWindow(that, '45%', '400');
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
function addPartnerCommissionRow(event) {
    preventFormPropagation(event)
    trCount++;
    var caseCommissionsNewRow = '';
    caseCommissionsNewRow +=
            '<tr id="' + trCount + '"><td>' +
            '<input type="hidden" id="commissionBenifitiary_' + trCount + '" name="commissionBenifitiaryIds[]" value="" required="" data-validation-engine="validate[required]"/>' +
            '<input type="text" id="commissionBenifitiaryName_' + trCount + '" value="" class="form-control lookup" data-validation-engine="validate[required]"  onblur="checkLookupValidity(jQuery(this), jQuery(\'#commissionBenifitiary_' + trCount + '\'));"/>' +
            '</td><td>' +
            '<input type="text" class="form-control" id="commissionRate_' + trCount + '" name="commissionRate[]"  required="" data-validation-engine="validate[required,funcCall[validateNumber]]"/>' +
            '</td><td><a class="float-right" href="javascript:;" onclick="deleteRow(jQuery(this), event);"><i class="remove-icon"></i></a></td></tr>';
    jQuery('#commissionDetails').append(caseCommissionsNewRow);
    partnerLookup('commissionBenifitiary_' + trCount, 'commissionBenifitiaryName_' + trCount);
    caseCommissionsFormValidate();
}
function deleteRow(RowJqObject, event) {
    preventFormPropagation(event)
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
        minLength: 1,
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
                pinesMessageV2({ty: 'warning', m: _lang.slaPrefNotDefined});
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function gridInitialization() {
    var tableColumns = [];
    if (jQuery('#display-columns').val()) {
        var columnsArray = (jQuery('#display-columns').val()).split(',');
        tableColumns.push({field: 'id', title: ' ', filterable: false, sortable: false, template: '<input type="checkbox" name="caseIds[]" id="caseId_#= id #" value="#= id #" title="' + _lang.archiveCheckboxTitle + '" onchange="enableDisable_tooltips(this);" />' +
                    '#= channel == "CP" || visibleToCP == "1" ? \'<span class="flag-green" title="\' + visibleFromCP + \'"></span>\' : \'\'#' +
                    '<div class="dropdown more">' + gridActionIconHTML + '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel">' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'cases/edit/#= id #">' + _lang.viewEdit + '</a></li>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="logActivityDialog(false, {legalCaseLookupId: \'#= id #\', legalCaseLookup: \'#= caseID #\', legalCaseSubject: \'#= addslashes(encodeURIComponent(subject)) #\', billable: \'#= timeTrackingBillable #\', clientName: \'#= clientName #\', modifiedByName: \'#= modifiedByName #\', createdOn: \'#= convert(createdOn).date #\', createdByName: \'#= createdByName #\', modifiedOn: \'#= convert(modifiedOn).date #\', status: \'#= status #\', clientId: \'#= client_id #\'},\'hideCase\');">' + _lang.log_time + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="TimerForm(null, \'\\add\', {legal_case_id: \'#= id #\', subject: \'#= caseID +\'\\: \'+addslashes(encodeURIComponent(subject)) #\'});">' + _lang.start_timer + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="archiveUnarchiveCase(\'#= id #\',\'#= archived #\');">#= (archived == \'no\') ? \''+_lang.archive+'\' : \''+_lang.unarchive+'\'  # </a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="convertToLitigation(\'#= id #\',\'#= case_type_id #\',\'#= legal_case_stage_id #\');">' + _lang.convertToLitigation + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="recordCaseExpense(\'#= id #\');">' + _lang.recordExpense + '</a>' +
                    '<a class="dropdown-item" href="' + getBaseURL() + 'cases/export_to_word/#= id #">' + _lang.exportToWord + '</a>' +
                    '# if (channel != "CP") { # <a class="dropdown-item" href="javascript:;" onclick="showMatterInCustomerPortal(\'#= id #\', event);">#= visibleToCP == 1 ? _lang.hideInCustomerPortal : _lang.showMatterInCustomerPortal #</a> #} #'+
                    '<a class="dropdown-item" style="#= (slaFeature == \'yes\') ? \'\' : \'display: none;\'  #" href="javascript:;" onclick="slaShowLogs(\'#= id #\', event);">' + _lang.showSLAElapsedTime + '</a>' +
                    '<a class="dropdown-item" href="javascript:;" onclick="deleteCaseRecord(\'#= id #\');">' + _lang.delete + '</a>' +
                    '</div></div>', width: '75px', attributes:{class: "flagged-gridcell"}
        });
        jQuery.each(columnsArray, function (i, item) {
            if (item === 'id') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, filterable: false, title: _lang.id, template: '<a href="' + getBaseURL() + 'cases/edit/#= id #">#= caseID #</a>', width: '120px'};
            } else if (item === 'subject') {
                array_push = {field: item, title: _lang.subject_Case, template: '<a href="' + getBaseURL() + 'cases/edit/#= id #"><bdi>#= replaceHtmlCharacter(addslashes(subject)).replace("\\\\\\\'", "\\\'") #</bdi></a><i class="iconLegal iconPrivacy#=private#"></i>', width: '220px'};
            } else if (item === 'latest_development') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.latestDevelopment, template: '#= getLatestDevelopment(latest_development)#', width: '350px'};
            } else if (item === 'assignee') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.assigneeCaseMatter, width: '192px', template: '#= (assignee!=null && userStatus=="Inactive")? assignee+" ("+_lang.custom[userStatus]+")":((assignee!=null)?assignee:"") #'};
            } else if (item === 'priority') {
                array_push = {field: item, title: _lang.priority, template: '#= getCustomTranslation(priority) #', width: '100px'};
            } else if (item === 'statusComments') {
                array_push = {field: item, title: _lang.status_comments, template: '#= (statusComments!=null&&statusComments!="") ? statusComments.substring(0,40)+"..." : ""#', width: '320px'};
            }
            else if (item === 'caseArrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.arrival_date, width: '158px', template: "#= (caseArrivalDate == null) ? '' : kendo.toString(caseArrivalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'arrivalDate') {
                array_push = {field: item, format: "{0:yyyy-MM-dd}", title: _lang.filedOn, width: '158px', template: "#= (arrivalDate == null) ? '' : kendo.toString(arrivalDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'dueDate') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, format: "{0:yyyy-MM-dd}", title: _lang.due_date, width: '143px', template: "#= (dueDate == null) ? '' : kendo.toString(dueDate, 'yyyy-MM-dd') #"};
            }
            else if (item === 'estimatedEffort') {
                array_push = {field: item, title: _lang.estEffort, template: '#= jQuery.fn.timemask({time: estimatedEffort}) #', width: '150px'};
            }
            else if (item === 'effectiveEffort') {
                array_push = {field: item, title: _lang.efftEffort, template: '#= jQuery.fn.timemask({time: effectiveEffort}) #', width: '162px'};
            }
            else if (item === 'caseValue') {
                array_push = {field: item, title: _lang.caseValue, template: "#= (caseValue==null)?'':kendo.toString(caseValue, \"n2\") #", width: '182px'};
            }
            else if (item === 'archivedCases') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.archived, template: '#= getCustomTranslation(archivedCases) #', width: '109px'};
            }
            else if (item === 'type') {
                array_push = {field: item, title: _lang.caseType, width: '170px'};
            }
            else if (item === 'clientName') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.clientName_Case, width: '175px'};
            }
            else if (item === 'status') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: _lang.workflow_status, width: '182px'};
            }
            else if (item === 'providerGroup') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'internalReference') {
                array_push = {field: item, template: '#= (internalReference==null)?"":internalReference #' +
                '# if(internalReference!=null && allowInternalRefLink==1) { # <a href="javascript:;" class="icon-alignment no-border no-margin p-0 btn btn-link" onclick="internalRefNumLink(\'#= internalRefLink #\', \'#= internalReference #\');"><i class="fa fa-external-link"></i></a> # } #' +
                '', sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'caseStage') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation('matterCaseStage'), width: '182px'};
            }
            else if (item === 'requestedByName') {
                array_push = {field: item, sortable: !sqlsrv2008, headerAttributes: {"col-header-placeholder": sqlsrv2008 ? _lang.columnDisabledSorting : ''}, title: getTranslation(item), width: '182px'};
            }
            else if (item === 'client_foreign_name') {
                array_push = {field: item, title: _lang.clientForeignName, width: '170px'};
            }
            else {
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
                    $response = jQuery.parseJSON(XHRObj.responseText);
                    if ($response.result != undefined && !$response.result) {
                        if ($response.gridDetails != undefined) {
                            setGridDetails($response.gridDetails);
                        }
                        if ($response.feedbackMessage != undefined) {
                            pinesMessage({ty: $response.feedbackMessage.ty, m: $response.feedbackMessage.m});
                        } else {
                            pinesMessageV2({ty: 'error', m: _lang.updatesFailed});
                        }
                    }
                    if ($response.columns_html) {
                        jQuery('#column-picker-trigger-container').html($response.columns_html);
                        jQuery('*[data-callexport]').on('click', function () {
                            if (hasAccessToExport != 1) {
                                pinesMessageV2({ty: 'warning', m: _lang.you_do_not_have_enough_previlages_to_access_the_requested_page});
                            } else {

                                if ($response.totalRows <= 10000) {
                                    if (jQuery(this).data('callexport').indexOf("true") >= 0) {
                                        exportCasesToExcel(true);
                                    } else {
                                        exportCasesToExcel();
                                    }
                                } else {
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
                    animateDropdownMenuInGrids('legalCaseGrid', 200);
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
                        options.sortData = JSON.stringify(casesSearchDataSrc.sort());
                        options.filter = checkWhichTypeOfFilterIUseAndReturnFilters();
                    }
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
                    case_type_id: {type: "integer"},
                    status: {type: "string"},
                    type: {type: "string"},
                    providerGroup: {type: "string"},
                    assignee: {type: "string"},
                    subject: {type: "string"},
                    caseArrivalDate: {type: "date"},
                    dueDate: {type: "date"},
                    category: {type: "string"},
                    caseValue: {type: "number"},
                    private: {type: "string"},
                    clientName: {type: "string"},
                    timeTrackingBillable: {type: "string"},
                    latest_development: {type: "string"},
                    priority: {type: "string"},
                    arrivalDate: {type: "date"},
                    statusComments: {type: "string"},
                    archivedCases: {type: "string"},
                    internalReference: {type: "string"},
                    caseStage: {type: "string"},
                    requestedByName: {type: "string"}
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
                        row['latest_development'] = escapeHtml(row['latest_development']);
                        row['statusComments'] = escapeHtml(row['statusComments']);
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
        // sortable: {
        //     mode: "multiple"
        // },
        selectable: "single",
        toolbar: [{
                name: "toolbar-menu",
                template: '<div></div>'

            }],
        columnResize: function (e) {
            fixFooterPosition();
            resizeHeaderAndFooter();
        },
        columnReorder: function (e) {
            orderColumns(e);
        }
    };
    if (casesGrid.length > 0) {
        gridTriggers({'gridContainer': casesGrid, 'gridOptions': casesSearchGridOptions, 'gridColumnsLength': Object.keys(tableColumns).length});
        var grid = casesGrid.data('kendoGrid');
        grid.thead.find("th:first").append(jQuery('<input id="selectAllCheckboxes" class="selectAll" type="checkbox" title="' + _lang.selectAllRecords + '" onclick="checkUncheckAllCheckboxes(this);" />'));
        grid.thead.find("[data-field=actionsCol]>.k-header-column-menu").remove();
    }
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

function checkRelatedFields(data)
{
    if(data.new_type === data.old_type){
        return true;
    }
    jQuery.ajax({
        url: getBaseURL() + "cases/check_custom_fields_relation",
        method: "post",
        dataType: "JSON",
        data: data,
        success: function(response){
            if(response.result){
               return true;
            }else{
                pinesMessageV2({ty: 'warning', m: response.display_message});
            }
        }
    });
}

function getLatestDevelopment(value)
{
    return ( value != null && value != "" ) ? ("<span class='tooltip-title' title='" + value + "'>" + helpers.truncate(value, 50, true,   _lang.languageSettings['langDirection'] === 'rtl') + "</span>" ) : "";
}
/**
 * Get All case resources as related companies, contacts, outsourcing, and contributors
 * @type {{getCaseCompanies: getCaseCompanies}}
 */
var caseResources = (function() {
    'use strict';

    /**
     * Get all case companies
     */
    function getCaseCompanies(caseId) {
        jQuery.ajax({
            url: getBaseURL() + "cases/edit",
            method: "post",
            dataType: "JSON",
            data: getCaseFilters(caseId,'readCompanies','companies'),
            success: function(response){
                if(response.data){
                    var caseCompaniesContainer = jQuery("#related-case-companies-container");
                    caseCompaniesContainer.empty();
                    if(response.data.length > 0){
                        jQuery.each(response.data, function(index, value){
                            var caseRelatedResourcesColumnContainer = jQuery('<div class="min-height-30"></div>');
                            var caseRelatedResourcesColumn = jQuery('<div class="pt-5"></div>');
                            caseRelatedResourcesColumnContainer.append(caseRelatedResourcesColumn);
                            if(value.legal_case_company_role_id != 0){
                                caseRelatedResourcesColumn.append('<div class="trim-width-63-per"><span class="">'+ value.companyName +'</span>'+ '<span class="label-color"> ('+ helpers.getObjectFromArr(companyRoles, 'text', 'value', value.legal_case_company_role_id) +')</span>'+'</div>');
                            } else{
                                caseRelatedResourcesColumn.append('<div class="trim-width-63-per"><span class="">'+ value.companyName +'</span>'+'</div>');
                            }
                            caseRelatedResourcesColumn.append('<a class="float-right" href="javascript:;" onClick="deleteSelectedRecord('+value.id+', \'deleteCompanies\', \'companiesGrid\',' + function(){
                                    caseResources.getCaseCompanies(legalCaseIdView);
                                } +')"\n' +
                                'title="Delete"><i class="icon-alignment fa fa-trash light_red-color pull-left-arabic" aria-hidden="true"></i></a>');
                            caseRelatedResourcesColumn.append('<i class="icon-alignment fa fa-pencil purple_color cursor-pointer-click float-right pull-left-arabic" aria-hidden="true" onclick="addContactPopup(\'company\',\'updateCompanies\', \'cases/edit/\',_lang.editCompany,_lang.companyName,\'company_id\',\'companyType\','+parseInt(value.companyId)+','+value.id+')"></i>');
                            caseRelatedResourcesColumn.append('<span id="company_'+ parseInt(value.companyId) +'"><i class="icon-alignment fa fa-eye purple_color cursor-pointer-click float-right pull-left-arabic" aria-hidden="true"></i></span>');
                            caseCompaniesContainer.append(caseRelatedResourcesColumnContainer);
                            jQuery('#company_'+ parseInt(value.companyId)).tooltipster({
                                content: getCompanyDetails(value),
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
                        });
                    } else{
                        var caseRelatedResourcesColumnContainer = jQuery('<div class="min-height-30"></div>');
                        var caseRelatedResourcesColumn = jQuery('<div class="center-v-flex pt-5"></div>');
                        caseRelatedResourcesColumnContainer.append(caseRelatedResourcesColumn);
                        caseRelatedResourcesColumn.append('<i class="icon-alignment fa fa-flag purple_color" aria-hidden="true"></i><span class="control-label pl-7">'+ _lang.no_related_matched_add.sprintf([_lang.custom.company,'<span class="link-style" onclick="addContactPopup(\'company\', \'addCompany\', \'cases/edit/\', _lang.addCompany, _lang.companyName,\'company_id\', \'companyType\')">'+_lang.custom.company+'</span>'])+'</span>');
                        caseCompaniesContainer.append(caseRelatedResourcesColumnContainer);
                    }

                }
            }
        });
    }

    /**
     * Get company details
     * @param data json
     */
    function getCompanyDetails(data){
        var result = '<div class="min_width_250">' +
                '<label>' +
                    '<span>'+ _lang.companyDetails+ '</span>' +
                    '<a target="_blank" href="'+ getBaseURL() +'\companies/tab_company/'+ data.companyId +'"><i class="icon-alignment fa fa-external-link purple_color cursor-pointer-click float-right" aria-hidden="true"></i></a>'+
                '</label>' +
                '<table class="table table-bordered border-grey table-condensed no-margin no-margin-top" id="company-view-details">'+
                    '<tbody id="company-view-details-body">'+
                        '<tr>' +
                            '<td>'+ _lang.company_category +'</td>' +
                            '<td>'+ (data.companyCategory === null ? '' : data.companyCategory) +'</td>' +
                        '</tr>'+
                        '<tr>' +
                            '<td>'+ _lang.internalReference +'</td>' +
                            '<td>'+ (data.internalReference === null ? '' : data.internalReference) +'</td>' +
                        '</tr>'+
                        '<tr>' +
                            '<td>'+ _lang.registrationNb +'</td>' +
                            '<td>'+ (data.registrationNb === null ? '' : data.registrationNb) +'</td>' +
                        '</tr>'+
                    '</tbody>' +
                '</table>' +
            '</div>';
        return result;
    }

    /**
     * Get contact details
     * @param data json
     */
    function getContactDetails(data){
        var result = '<div class="min_width_250 padding-10">' +
                        '<label>' +
                            '<span>'+ _lang.contactDetails+ '</span>' +
                            '<a target="_blank" href="'+ getBaseURL() +'\contacts/edit/'+ data.contactId +'"><i class="icon-alignment fa fa-external-link purple_color cursor-pointer-click float-right" aria-hidden="true"></i></a>'+
                        '</label>' +
                        '<table class="table table-bordered table-condensed no-margin no-margin-top" id="contact-view-details">'+
                            '<tbody id="contact-view-details-body">'+
                                '<tr>' +
                                    '<td>'+ _lang.email +'</td>' +
                                    '<td>'+ (data.email === null ? '' : ('<a href="mailto:'+data.email+'">'+data.email+'</a>')) +'</td>' +
                                '</tr>'+
                                '<tr>' +
                                    '<td>'+ _lang.jobTitle +'</td>' +
                                    '<td>'+ data.jobTitle +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.mobile +'</td>' +
                                    '<td>'+ data.mobile +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.phone +'</td>' +
                                    '<td>'+ data.phone +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.internalReferenceNumber +'</td>' +
                                    '<td>'+ (data.internalReference === null ? '' : data.internalReference) +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.comments +'</td>' +
                                    '<td>'+ (data.contact_comments === null ? '' : data.contact_comments) +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.country +'</td>' +
                                    '<td>'+ (data.country === null ? '' : data.country) +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.city +'</td>' +
                                    '<td>'+ (data.city === null ? '' : data.city) +'</td>' +
                                '</tr>' +
                                '<tr>' +
                                    '<td>'+ _lang.address +'</td>' +
                                    '<td>'+ (data.address1 === null ? '' : data.address1) +'</td>' +
                                '</tr>' +
                            '</tbody>' +
                        '</table>' +
                    '</div>';
        return result;
    }

    /**
     * Get all case contacts
     */
    function getCaseContacts(caseId) {
        jQuery.ajax({
            url: getBaseURL() + "cases/edit",
            method: "post",
            dataType: "JSON",
            data: getCaseFilters(caseId,'readContacts','contact'),
            success: function(response){
                if(response.data){
                    var caseContactsContainer = jQuery("#related-case-contacts-container");
                    caseContactsContainer.empty();
                    if(response.data.length > 0){
                        jQuery.each(response.data, function(index, value){
                            var caseRelatedResourcesColumnContainer = jQuery('<div class="min-height-30"></div>');
                            var caseRelatedResourcesColumn = jQuery('<div class="pt-5"></div>');
                            caseRelatedResourcesColumnContainer.append(caseRelatedResourcesColumn);
                            if(value.legal_case_contact_role_id != 0){
                                caseRelatedResourcesColumn.append('<div class="trim-width-63-per"><span class="">'+ value.contactName +'</span>'+ '<span class="label-color"> ('+ helpers.getObjectFromArr(contactRoles, 'text', 'value', value.legal_case_contact_role_id) +')</span>'+'</div>');
                            } else{
                                caseRelatedResourcesColumn.append('<div class="trim-width-63-per"><span class="">'+ value.contactName +'</span>'+'</div>');
                            }
                            caseRelatedResourcesColumn.append('<a class="float-right" href="javascript:;" onClick="deleteSelectedRecord('+value.id+', \'deleteContacts\', \'contactsGrid\',' + function(){
                                    caseResources.getCaseContacts(legalCaseIdView);
                                } +')"\n' +
                                'title="Delete"><i class="icon-alignment fa fa-trash light_red-color pull-left-arabic" aria-hidden="true"></i></a>');
                            caseRelatedResourcesColumn.append('<i class="icon-alignment fa fa-pencil purple_color cursor-pointer-click float-right pull-left-arabic" aria-hidden="true" onclick="addContactPopup(\'contact\', \'updateContacts\', \'cases/edit/\', _lang.editContact, _lang.contactName, \'contact_id\', \'contactType\','+parseInt(value.contactId)+','+parseInt(value.id)+')"></i>');
                            caseRelatedResourcesColumn.append('<span id="contact_'+ parseInt(value.contactId) +'"><i class="icon-alignment fa fa-eye purple_color cursor-pointer-click float-right pull-left-arabic" aria-hidden="true"></i></span>');
                            if(value.client_portal_id != null){
                                caseRelatedResourcesColumn.append('<span class="icon-alignment big-title-text-font-size client-portal-blue float-right mt-3 pull-left-arabic" id="client_portal_related_'+ parseInt(value.contactId) +'"></span>');
                            }
                            caseContactsContainer.append(caseRelatedResourcesColumnContainer);
                            jQuery('#contact_'+ parseInt(value.contactId)).tooltipster({
                                content: getContactDetails(value),
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
                        });
                    } else{
                        var caseRelatedResourcesColumnContainer = jQuery('<div class="min-height-30"></div>');
                        var caseRelatedResourcesColumn = jQuery('<div class="center-v-flex pt-5"></div>');
                        caseRelatedResourcesColumnContainer.append(caseRelatedResourcesColumn);
                        caseRelatedResourcesColumn.append('<i class="icon-alignment fa fa-flag purple_color" aria-hidden="true"></i><span class="control-label pl-7">'+ _lang.no_related_matched_add.sprintf([_lang.custom.person,'<span class="link-style" onclick="addContactPopup(\'contact\', \'addContact\', \'cases/edit/\', _lang.addContact, _lang.contactName,\'contact_id\', \'contactType\')">'+_lang.custom.person+'</span>'])+'</span>');
                        caseContactsContainer.append(caseRelatedResourcesColumnContainer);
                    }
                }
            }
        });
    }
    /*
***
*/
function getBasicCourtActivityList() {
    jQuery.ajax({
        url: getBaseURL() + "cases/edit",
        method: "post",
        dataType: "JSON",
        data: { 'returnData': 1, 'action' : 'readBasicCourtActivity'},
        success: function(response){
            if(response.result) console.log("sd");
        }
    });
}

    /**
     * Get all case outsourcing
     */
    function getCaseOutsourcing() {
        jQuery.ajax({
            url: getBaseURL() + "cases/edit",
            method: "post",
            dataType: "JSON",
            data: { 'returnData': 1, 'action' : 'readOutsource'},
            success: function(response){
                if(response.result) console.log("sd");
            }
        });
    }


    /**
     * Get all case contributors
     */
    function getCaseContributors() {
        jQuery.ajax({
            url: getBaseURL() + "cases/edit",
            method: "post",
            dataType: "JSON",
            data: { 'returnData': 1, 'action' : 'readContributor'},
            success: function(response){
                if(response.result) console.log("sd");
            }
        });
    }

    /**
     * Get resources case filter
     * @param caseId Int "case id"
     * @param resource string "readContributor,readOutsource,readContacts,readCompanies,readBasicCourtActivity"
     * @param resourceName string "contributor,outsource,contacts,companies,court_activities"
     */
    function getCaseFilters(caseId, resource, resourceName){
        if(resourceName === 'companies'){
            return {'filter[logic]' : 'and', 'returnData': 1, 'action' : resource, 'filter[filters][0][filters][0][field]': 'legal_cases_companies.case_id',
                'filter[filters][0][filters][0][operator]' : 'eq', 'filter[filters][0][filters][0][value]' : caseId}
        } else{
            return {'filter[logic]' : 'and', 'returnData': 1, 'action' : resource, 'filter[filters][0][filters][0][field]': 'legal_cases_contacts.case_id',
                'filter[filters][0][filters][0][operator]' : 'eq', 'filter[filters][0][filters][0][value]' : caseId,
                'filter[filters][1][filters][0][field]': 'legal_cases_contacts.contactType', 'filter[filters][1][filters][0][operator]': 'eq',
                'filter[filters][1][filters][0][value]' : resourceName};
        }
    }

    return {
        getCaseCompanies: getCaseCompanies,
        getCaseContacts: getCaseContacts,
        getCaseOutsourcing: getCaseOutsourcing,
        getCaseContributors: getCaseContributors,
        getBasicCourtActivityList:getBasicCourtActivityList
        };
}());
