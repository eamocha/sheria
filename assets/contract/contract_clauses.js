function contractClauseForm(id) {
    id = id || false;
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL('contract') + 'clauses/'+(id? ('edit/'+id) : 'add'),
        type: 'GET',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery('#contract-clause-form-container').length <= 0) {
                    jQuery('<div id="contract-clause-form-container"></div>').appendTo("body");
                    var contractClauseFormContainer = jQuery('#contract-clause-form-container');
                    contractClauseFormContainer.html(response.html);
                    commonModalDialogEvents(contractClauseFormContainer, contractClauseFormSubmit);
                    contractClauseFormEvents(contractClauseFormContainer);
                }

            }else{
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'error', m: response.display_message});
                }else{
                    pinesMessage({ty: 'error', m: _lang.invalid_record});
                }

            }
            jQuery('#loader-global').hide();
        }, complete: function () {
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractClauseFormEventsOld(container) {
    jQuery('.select-picker', container).selectpicker({dropupAuto: false});
    initializeModalSize(container);
    loadSelectizeOptions('watchers', container);
    if(jQuery('#private', container).val() == 1){
        jQuery('.selectize-control', jQuery('#privacy-container', container)).removeClass('d-none');
    }else{
        jQuery('.selectize-control', jQuery('#privacy-container', container)).addClass('d-none');

    }
    loadSelectizeOptions('editors', container);
    tinymce.remove('#contract-clause-form-container #content');
    tinymce.init({
        selector: '#content',
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link | undo redo | alignleft aligncenter alignright alignjustify',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: {"style": "margin: 0;"},
        formats: {
            underline: {inline: 'u', exact: true},
            alignleft: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: {align: 'left'}
            },
            aligncenter: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: {align: 'center'}
            },
            alignright: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: {align: 'right'}
            },
            alignjustify: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: {align: 'justify'}
            }
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#content_ifr').contents().find('body').prop("dir", "auto");
                e.pasteAsPlainText = true;
            });
        }
    });
    jQuery('#label', container).selectize({
        plugins: ['remove_button'],
        placeholder: _lang.startTyping,
        delimiter: ',',
        persist: false,
        create: function(input) {
            return {
                value: input,
                text: input
            }
        }
    });
}
function contractClauseFormEvents(container) {
    jQuery('.select-picker', container).selectpicker({ dropupAuto: false });
    initializeModalSize(container);
    loadSelectizeOptions('watchers', container);
    if (jQuery('#private', container).val() == 1) {
        jQuery('.selectize-control', jQuery('#privacy-container', container)).removeClass('d-none');
    } else {
        jQuery('.selectize-control', jQuery('#privacy-container', container)).addClass('d-none');
    }
    loadSelectizeOptions('editors', container);

    // Remove any existing TinyMCE instance for the specific textarea
    tinymce.remove('#contract-clause-form-container #content');

    // Initialize TinyMCE with a more specific selector
    tinymce.init({
        selector: '#contract-clause-form-container #content', // Specific to the container
        menubar: false,
        statusbar: false,
        branding: false,
        height: 200,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link | undo redo | alignleft aligncenter alignright alignjustify',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: { "style": "margin: 0;" },
        formats: {
            underline: { inline: 'u', exact: true },
            alignleft: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: { align: 'left' }
            },
            aligncenter: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: { align: 'center' }
            },
            alignright: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: { align: 'right' }
            },
            alignjustify: {
                selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img',
                attributes: { align: 'justify' }
            }
        },
        paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                console.log('TinyMCE initialized for #content');
                jQuery('#content_ifr').contents().find('body').prop("dir", "auto");
                e.pasteAsPlainText = true;
            });
        }
    });

    jQuery('#label', container).selectize({
        plugins: ['remove_button'],
        placeholder: _lang.startTyping,
        delimiter: ',',
        persist: false,
        create: function (input) {
            return {
                value: input,
                text: input
            };
        }
    });
}

function contractClauseFormSubmit(container) {
    var id = jQuery('#id', container).val();
    var formData = new FormData(document.getElementById(jQuery("form#contract-clause-form", container).attr('id')));

    // Get the TinyMCE editor instance and sync content to textarea
    var editor = tinymce.get('content'); // Use the specific editor ID
    if (editor) {
        editor.save(); // Sync content to the textarea
        console.log('TinyMCE content:', editor.getContent()); // Debug content
    } else {
        console.error('TinyMCE editor not found for #content');
    }

    // Log FormData for debugging
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false,
        cache: false,
        processData: false,
        url: getBaseURL('contract') + 'clauses/' + (id ? ('edit/' + id) : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                var msg = _lang.feedback_messages.updatesSavedSuccessfully;
                pinesMessage({ ty: 'success', m: msg });
                jQuery('.modal', container).modal('hide');
                window.location = window.location.href;
            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ ty: 'error', m: response.display_message });
                }
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                }
            }
        },
        complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function contractClauseFormSubmitold(container) {
    var id = jQuery('#id', container).val();
    var formData = new FormData(document.getElementById(jQuery("form#contract-clause-form", container).attr('id')));
    formData.append('content',tinymce.activeEditor.getContent());
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit').addClass('loading');
            jQuery('.modal-save-btn').attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        url: getBaseURL('contract') + 'clauses/'+(id? ('edit/'+id) : 'add'),
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.result) {
                var msg = _lang.feedback_messages.updatesSavedSuccessfully;
                pinesMessage({ty: 'success', m: msg});
                jQuery('.modal', container).modal('hide');
                window.location = window.location.href;

            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'error', m: response.display_message});
                }
                if (typeof response.validation_errors !== 'undefined') {
                    displayValidationErrors(response.validation_errors, container);
                }

            }
        }, complete: function () {
            jQuery('.modal-save-btn').removeAttr('disabled');
            jQuery('.loader-submit').removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function contractClauseDelete(params) {

    jQuery.ajax({
        url: getBaseURL('contract') + 'clauses/delete',
        dataType: 'json',
        data: {id: params.id},
        type: 'POST',
        success: function (response) {
            if (response.result) {
                pinesMessage({ty: 'success', m: response.display_message});
                jQuery('div#item-' + params.id, '.administration-container').remove();

            } else {
                if (typeof response.display_message !== 'undefined') {
                    pinesMessage({ty: 'error', m: response.display_message});
                }else{
                    pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});

                }
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

// clicking on set as private link
function setAsPrivate(container, itemObj, creator, loggedUser) {
    creator = creator || false;
    loggedUser = loggedUser || false;
    itemObj = itemObj || false;
    container = jQuery(container, '#contract-clause-form-container');
    jQuery('#private', container).val(1);
    jQuery('#private-link', container).addClass('d-none');
    jQuery('#public-link', container).removeClass('d-none');
    jQuery('.shared-with-label', container).addClass('d-none');
    jQuery('#private', container).val(1);
    jQuery('#watchers', container).removeClass('d-none');
    jQuery('.selectize-control', container).removeClass('d-none');
    if (creator && loggedUser && (Number(creator) != Number(loggedUser))) { // add modified user to watchers list in edit mode in case the logged user is not the owner / creator - Number using to cast id with leading zero to number in mysql
        var control = itemObj[0].selectize;
        control.addItem(loggedUser, true);
    }
}

// clicking on set as public link in case, contact or comapny
function setAsPublic(container) {
    var $select = jQuery('#watchers').selectize();
    var control = $select[0].selectize;
    control.clear();
    control.clearOptions();
    jQuery('#private', container).val('');
    jQuery('#private-link', container).removeClass('d-none');
    jQuery('#public-link', container).addClass('d-none');
    jQuery('.shared-with-label', container).removeClass('d-none');
    jQuery('#watchers', container).addClass('d-none');
    jQuery('.selectize-control', container).addClass('d-none');
}

function updateFields(that, container) {
    var checked = jQuery(that).is(':checked') ? true : false;
    jQuery("[data-field=type_id]", container).addClass('d-none');
    if(checked){
        jQuery('.contract-type-all', container).removeAttr('disabled');
        jQuery('select[id=contract-type]', container).attr('disabled', 'disabled');
        jQuery('#contract-types-container', container).addClass('d-none');
    }else{
        jQuery('.contract-type-all', container).attr('disabled', 'disabled');
        jQuery('select[id=contract-type]', container).removeAttr('disabled');
        jQuery('#contract-types-container', container).removeClass('d-none');
    }
}