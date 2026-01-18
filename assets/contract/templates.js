var container;
jQuery(document).ready(function () {
    container = jQuery('#contract-template-container');
    jQuery('.select-picker', '.template-container').selectpicker({ dropupAuto: false });
    jQuery('#type', container).change(function () {
        contractTypeEvent(jQuery(this).val(), jQuery("#sub-type", container));
    });
    if (jQuery('#template-id', container).val() == '') {
        pageSectionEvents();
    } else {
        jQuery('.select-picker', '.variable-body-edit').selectpicker({ dropupAuto: false });
    }
});

function contractTemplateFormSubmit() {
    jQuery(".variable-description-text").each(function () {
        if (jQuery(this).attr('id')) {
            jQuery(".variable-description", jQuery(this).parent()).html(tinymce.get(jQuery(this).attr('id')).getContent());
        }
    });
    jQuery(".page-description-text").each(function () {
        if (jQuery(this).attr('id')) {
            jQuery(".page-description", jQuery(this).parent()).html(tinymce.get(jQuery(this).attr('id')).getContent());
        }
    });
    var templateId = jQuery('#template-id', container).val();
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_templates/' + (templateId == '' ? 'add' : 'edit'),
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        success: function (response) {
            if (response.result) {
                window.location = getBaseURL('contract') + 'contract_templates';
            } else {
                jQuery('.inline-error', container).addClass('d-none');
                validateForm(response);
            }

        }, error: defaultAjaxJSONErrorsHandler
    });
}

function validateForm(response) {
    if (typeof response.validation_errors !== 'undefined') {
        pinesMessage({ ty: 'warning', m: _lang.feedback_messages.fillAllRequiredFields });
        jQuery.each(response.validation_errors, function (category, val) {
            if (typeof val == 'object') {
                jQuery.each(val, function (rowId, error) {
                    if (typeof error == 'object') {
                        jQuery.each(error, function (rowId1, error1) {
                            if (typeof error1 == 'object') {
                                jQuery.each(error1, function (rowId2, error2) {
                                    if (typeof error2 == 'object') {
                                        jQuery.each(error2, function (rowId3, error3) {
                                            if (typeof error3 == 'object') {
                                                jQuery.each(error3, function (rowId4, error4) {
                                                    if (typeof error4 == 'object') {
                                                        jQuery.each(error4, function (rowId5, error5) {
                                                            field = category + '[' + rowId + '][' + rowId1 + '][' + rowId2 + '][' + rowId3 + '][' + rowId4 + '][' + rowId5 + ']';
                                                            jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error5).addClass('validation-error');
                                                        });
                                                    } else {
                                                        field = category + '[' + rowId + '][' + rowId1 + '][' + rowId2 + '][' + rowId3 + '][' + rowId4 + ']';
                                                        jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error4).addClass('validation-error');
                                                    }
                                                });
                                            } else {
                                                field = category + '[' + rowId + '][' + rowId1 + '][' + rowId2 + '][' + rowId3 + ']';
                                                jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error3).addClass('validation-error');
                                            }

                                        });
                                    } else {
                                        field = category + '[' + rowId + '][' + rowId1 + '][' + rowId2 + ']';
                                        alert(field);
                                        jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error2).addClass('validation-error');
                                    }

                                });
                            } else {
                                field = category + '[' + rowId + '][' + rowId1 + ']';
                                jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error1).addClass('validation-error');
                            }
                        });
                    } else {
                        field = category + '[' + rowId + ']';

                        jQuery("div", container).find("[data-field='" + field + "']").removeClass('d-none').html(error).addClass('validation-error');
                    }
                });
            } else {
                displayValidationErrors(response.validation_errors, container);
            }

        });
    }
}
function previewTemplate() {
    jQuery(".variable-description-text").each(function () {
        if (jQuery(this).attr('id')) {
            jQuery(".variable-description", jQuery(this).parent()).html(tinymce.get(jQuery(this).attr('id')).getContent());
        }
    });
    jQuery(".page-description-text").each(function () {
        if (jQuery(this).attr('id')) {
            jQuery(".page-description", jQuery(this).parent()).html(tinymce.get(jQuery(this).attr('id')).getContent());
        }
    });
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery('.inline-error', container).addClass('d-none');
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_templates/preview_template',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result) {
                if (response.html) {
                    if (jQuery('#contract-generate-container').length <= 0) {
                        jQuery('<div id="contract-generate-container" class="primary-style"></div>').appendTo("body");
                        var contractGenerateContainer = jQuery('#contract-generate-container');
                        contractGenerateContainer.html(response.html);
                        initializeModalSize(contractGenerateContainer);
                        commonModalDialogEvents(contractGenerateContainer);
                        previewTemplateEvents(contractGenerateContainer, response.pages_count);
                    }
                }
            } else {
                validateForm(response);
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function previewTemplateEvents(container, pagesNb) {
    var currentPageNb;
    jQuery('#doc-name', container).val(jQuery('#doc-name-preffix', container).val() + jQuery('#doc-name-suffix', container).text());
    jQuery('.tooltip-title', container).tooltipster({
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
    jQuery("[id^='variable-']", container).each(function () {
        var parent = jQuery('.' + jQuery(this).attr('id'), container);
        switch (jQuery(this).attr('data-type')) {
            case 'date':
                setDatePicker(jQuery(this).parent(), container);
                break;
            case 'date_time':
                if (jQuery(this).parent().hasClass('date-picker')) {
                    setDatePicker(jQuery(this).parent(), container);
                }
                if (jQuery(this).hasClass('time')) {
                    jQuery(this).timepicker({
                        'timeFormat': 'H:i',
                    });
                }
                break;
            case 'list':
                jQuery(this).selectpicker();
                break;
            case 'single_lookup':
                var fieldTypeData = jQuery(this).attr('data-lookup-type');
                var mySuggestion = new Bloodhound({
                    datumTokenizer: function (datum) {
                        return Bloodhound.tokenizers.whitespace('');
                    },
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    remote: {
                        url: getBaseURL() + fieldTypeData + '/autocomplete?term=%QUERY',
                        filter: function (data) {
                            return data;
                        },
                        replace: function (url, uriEncodedQuery) {
                            return url.replace('%QUERY', uriEncodedQuery);
                        },
                        'cache': false,
                        wildcard: '%QUERY',
                    }
                });
                mySuggestion.initialize();
                jQuery(this).typeahead({
                    hint: false,
                    highlight: true,
                    minLength: 2
                },
                    {
                        source: mySuggestion.ttAdapter(),
                        display: function (item) {
                            return item.father ? item.firstName + ' ' + item.father + ' ' + item.lastName : item.firstName + ' ' + item.lastName;
                        },
                        templates: {
                            empty: [
                                '<div class="empty"> ' + _lang.noMatchesFound + '</div>'].join('\n'),
                            suggestion: function (data) {
                                foreignFullName = data.foreignFullName.trim();
                                return '<div>' + (data.father ? data.firstName + ' ' + data.father + ' ' + data.lastName : data.firstName + ' ' + data.lastName) + (foreignFullName ? (' - ' + foreignFullName) : '') + '</div>'
                            }
                        }
                    }).on('typeahead:asyncrequest', function () {
                        jQuery('.loader-submit', parent).addClass('loading');
                    }).on('typeahead:asynccancel typeahead:asyncreceive', function () {
                        jQuery('.loader-submit', parent).removeClass('loading');
                    });
                lookupCommonFunctions(jQuery(this), jQuery('.field-member-id', parent), fieldTypeData, parent);
                break;
            case 'multiple_lookup_per_type':
                var lookupDetails = {
                    'lookupField': jQuery('.field-lookup', parent),
                    'hiddenInput': jQuery('.field-member-id', parent),
                };
                jQuery('.field-member-type', parent).selectpicker().change(function () {
                    jQuery(".field-lookup", parent).val('');
                    jQuery(".field-member-id", parent).val('');
                    jQuery('.field-lookup', parent).typeahead('destroy');
                    lookupCompanyContactType(lookupDetails, parent, jQuery(this).val());
                });
                jQuery('.field-lookup', parent).typeahead('destroy');
                lookupCompanyContactType(lookupDetails, parent, jQuery('.field-member-type option:selected', parent).val());
                break;
            case 'multiple_fields_per_type':
                jQuery(this, container).selectpicker();
                setDatePicker('#end-date', container);
                break;
            default:
                break;
        }
    });
    currentPageNb = 1;
    progressPerc = 100 / pagesNb;
    jQuery('.progress-bar', jQuery('#progress-bar', container)).css('width', progressPerc + '%').attr('progress', progressPerc);
    jQuery('#pages-count', jQuery('#progress-bar', container)).html(pagesNb);
    if (pagesNb == 1) {
        jQuery('.next-page', jQuery('.modal-footer', container)).addClass('d-none');
    }
    jQuery(".next-page").click(function () {
        nextPageNb = parseInt(currentPageNb) + 1;
        jQuery('#current-page').html(nextPageNb);
        jQuery('.modal-footer', container).attr('data-field', nextPageNb);
        lastProgress = jQuery('.progress-bar', container).attr('progress');
        newProgress = parseInt(lastProgress) + parseInt(progressPerc);
        jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);
        jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
        currentPageNb = nextPageNb;
        jQuery("[page-number=" + nextPageNb + "]", container).removeClass('d-none');
        if (nextPageNb == pagesNb) {
            jQuery('.next-page', jQuery('.modal-footer', container)).addClass('d-none');
        }
        jQuery('.previous', jQuery('.modal-footer', container)).removeClass('d-none');
    });
    jQuery(".previous").click(function () {
        prevPageNb = parseInt(currentPageNb) - 1;
        if (prevPageNb > 0) {
            jQuery('#current-page').html(prevPageNb);
            jQuery('.modal-footer', container).attr('data-field', prevPageNb);

            lastProgress = jQuery('.progress-bar', container).attr('progress');
            newProgress = parseInt(lastProgress) - parseInt(progressPerc);

            jQuery('.progress-bar', container).css('width', newProgress + '%').attr('progress', newProgress);

            jQuery("[page-number=" + currentPageNb + "]", container).addClass('d-none');
            currentPageNb = prevPageNb;
            jQuery("[page-number=" + prevPageNb + "]", container).removeClass('d-none');
            if (jQuery('.next-page', jQuery('.modal-footer', container)).hasClass('d-none')) {
                jQuery('.next-page', jQuery('.modal-footer', container)).removeClass('d-none');
            }
            if (prevPageNb == 1) {
                jQuery('.previous', jQuery('.modal-footer', container)).addClass('d-none');
            }
        }
    });
}

function pageSectionEvents(isEdit) {
    var sectionCount = jQuery('#pages-count', container).val();
    sectionCount++;
    var clonedSection = jQuery('.page-section-default', container).clone();
    var clonedId = 'page-section-' + sectionCount;
    clonedSection.attr('id', clonedId).removeClass('page-section-default').removeClass('d-none');
    var clonedListItem = jQuery('.page-item-default', container).clone();
    var clonedListItemId = 'page-item-' + sectionCount;
    clonedListItem.attr('id', clonedListItemId).removeClass('page-item-default').removeClass('d-none');
    jQuery('.page-item-text', clonedListItem).text(_lang.page + ' ' + sectionCount);
    jQuery(".page-item", ".pages-list-section").each(function () {
        jQuery(this).removeClass('active');
    });
    clonedListItem.attr('onClick', 'goToPage(\'' + sectionCount + '\')');
    jQuery(".page-section", ".pages-section").each(function () {
        jQuery(this).addClass('d-none');
    });
    clonedListItem.addClass('active');
    jQuery('.page-section-body', clonedSection).attr('id', 'page-section-body-' + sectionCount);
    jQuery('.page-values', clonedSection).attr('id', 'page-values-' + sectionCount);
    jQuery('.page-group-count', clonedSection).attr('id', 'page-group-count-' + sectionCount);
    jQuery('.remove-page', clonedListItem).attr('onClick', 'removePage(\'' + sectionCount + '\')');
    jQuery('.page-title-text', clonedSection).text(_lang.page + ' ' + sectionCount);
    jQuery('a.group-section-link', clonedSection).attr('onClick', 'groupSectionEvent(\'' + sectionCount + '\')');
    jQuery('.page-description', clonedSection).attr('id', 'page-description-' + sectionCount);
    jQuery('.page-description-text', clonedSection).attr('id', 'page-description-text-' + sectionCount);
    jQuery('.pages-list-section', container).append(clonedListItem);
    jQuery('.pages-section', container).append(clonedSection);
    tinymce.init({
        selector: '#page-description-text-' + sectionCount,
        menubar: false,
        statusbar: false,
        branding: false,
        height: 70,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: { "style": "margin: 0;" },
    });
    jQuery(':input, select', '#page-values-' + sectionCount).each(function () {
        var field = jQuery(this).attr('data-field');
        jQuery(this).attr('name', 'pages[' + sectionCount + '][' + field + ']');
        jQuery('div.inline-error', jQuery(this).parent()).attr('data-field', 'pages[' + sectionCount + '][' + field + ']');
    });
    jQuery('#pages-count', container).val(sectionCount);
    if (!isEdit) {
        groupSectionEvent(sectionCount);
    }
}

function removePage(sectionCount) {
    var pageCount = jQuery('.page-section', '.pages-section').length - 1;
    if (pageCount > 1) {
        jQuery('#page-section-' + sectionCount, '.pages-section').remove();
        jQuery('#page-item-' + sectionCount, '.pages-list-section').remove();
    } else {
        pinesMessage({ ty: 'information', m: _lang.templateShouldContainAtLeastOnePage });
    }
}
function removeGroup(pageSection, sectionCount) {
    var groupCount = jQuery('.group-details', jQuery('.groups-section-body', '#page-section-' + pageSection)).length - 1;
    if (groupCount > 1) {
        jQuery('#group-section-' + sectionCount, jQuery('.groups-section-body', '#page-section-' + pageSection)).remove();
        jQuery('#group-item-' + sectionCount, jQuery('.groups-list-section', '#page-section-' + pageSection)).remove();
    } else {
        pinesMessage({ ty: 'information', m: _lang.pageShouldContainAtLeastOneGroup });
    }
}
function removeVariable(groupSection, sectionCount) {
    var variableCount = jQuery('.variable-details', jQuery('.variables-section-body', '#group-section-' + groupSection)).length - 1;
    if (variableCount > 1) {
        jQuery('#variable-section-' + sectionCount, jQuery('.variables-section-body', '#group-section-' + groupSection)).remove();
    } else {
        pinesMessage({ ty: 'information', m: _lang.groupShouldContainAtLeastOneVariable });
    }
}

function downloadTemplateFile(id) {
    var downloadUrl = getBaseURL() + 'docs/download_file/' + id;
    window.location = downloadUrl;
}

function groupSectionEvent(pageSection, isEdit) {
    var sectionCount = jQuery('#groups-count', container).val();
    sectionCount++;
    var clonedSection = jQuery('.group-section-default', '#page-section-body-' + pageSection).clone();
    var clonedId = 'group-section-' + sectionCount;
    clonedSection.attr('id', clonedId).removeClass('group-section-default').removeClass('d-none');
    var clonedListItem = jQuery('.group-item-default', '#page-section-body-' + pageSection).clone();
    var clonedListItemId = 'group-item-' + sectionCount;
    clonedListItem.attr('id', clonedListItemId).removeClass('group-item-default').removeClass('d-none');
    var pageGroupCount = parseInt(jQuery('#page-group-count-' + pageSection, '#page-section-body-' + pageSection).val()) + 1;
    jQuery('.group-item-text', clonedListItem).text(_lang.group + ' ' + pageGroupCount);
    jQuery(".group-item", jQuery('.groups-list-section', '#page-section-body-' + pageSection)).each(function () {
        jQuery(this).removeClass('active');
    });
    clonedListItem.attr('onClick', 'goToGroup(\'' + pageSection + '\',\'' + sectionCount + '\')');
    jQuery(".group-details", '#page-section-body-' + pageSection).each(function () {
        jQuery(this).addClass('d-none');
    });
    clonedListItem.addClass('active');
    jQuery('.group-body', clonedSection).attr('id', 'group-body-' + sectionCount);
    jQuery('.group-variable-count', clonedSection).attr('id', 'group-variable-count-' + sectionCount);
    jQuery('.remove-group', clonedListItem).attr('onClick', 'removeGroup(\'' + pageSection + '\', \'' + sectionCount + '\')');
    jQuery('#page-group-count-' + pageSection, '#page-section-body-' + pageSection).val(pageGroupCount);
    jQuery('.group-title-text', clonedSection).text(_lang.group + ' ' + pageGroupCount);
    jQuery('.group-title-input', clonedListItem).attr('name', 'pages[' + pageSection + '][groups][' + sectionCount + '][title]');
    jQuery('div.inline-error', jQuery('.group-title-input', clonedListItem).parent()).attr('data-field', 'pages[' + pageSection + '][groups][' + sectionCount + '][title]');
    jQuery('a.variable-section-link', '#page-section-body-' + pageSection).attr('onClick', 'variableSectionEvent(\'' + pageSection + '\', \'' + sectionCount + '\')');
    jQuery('.groups-list-section', '#page-section-body-' + pageSection).append(clonedListItem);
    jQuery('.groups-section-body', '#page-section-body-' + pageSection).append(clonedSection);
    jQuery('.groups-section-body', '#page-section-body-' + pageSection).removeClass('d-none');
    jQuery('#groups-count', container).val(sectionCount);
    if (!isEdit) {
        variableSectionEvent(pageSection, sectionCount)
    }
}

function variableSectionEvent(pageSection, groupCount, variableExists) {
    var variableExists = variableExists || false;
    var sectionCount = jQuery('#variables-count', container).val();
    sectionCount++;
    var clonedSection = jQuery('.variable-section-default', '#group-body-' + groupCount).clone();
    var clonedId = 'variable-section-' + sectionCount;
    clonedSection.attr('id', clonedId).removeClass('variable-section-default').removeClass('d-none');
    jQuery('.mapped-fields', clonedSection).selectpicker().change(function () {
        var field = jQuery(this).val();
        if (field == 'name') {
            if (jQuery('.system-required-fields', clonedSection).hasClass('d-none')) {
                jQuery('.system-required-fields', clonedSection).removeClass('d-none');
                jQuery("[data-field=is_required]", clonedSection).addClass('d-none').selectpicker('destroy').val('yes').selectpicker();
            }
        } else {
            jQuery('.system-required-fields', clonedSection).addClass('d-none');
            jQuery("[data-field=is_required]", clonedSection).removeClass('d-none').selectpicker('destroy').val('no').selectpicker();
        }
    });
    jQuery('.select-picker', clonedSection).selectpicker({ dropupAuto: false });
    jQuery('.remove-variable', clonedSection).attr('onClick', 'removeVariable(\'' + groupCount + '\', \'' + sectionCount + '\')');
    var groupVariableCount = parseInt(jQuery('#group-variable-count-' + groupCount, '#group-body-' + groupCount).val()) + 1;
    jQuery('.variable-description', clonedSection).attr('id', 'variable-description-' + sectionCount);
    jQuery('.variable-description-text', clonedSection).attr('id', 'variable-description-text-' + sectionCount);
    jQuery('#group-variable-count-' + groupCount, '#group-body-' + groupCount).val(groupVariableCount);
    jQuery('.variable-name-text', clonedSection).text(_lang.variable + ' ' + groupVariableCount);
    jQuery(':input, select', clonedSection).each(function () {
        var field = jQuery(this).attr('data-field');
        if (field) {
            jQuery(this).attr('name', 'pages[' + pageSection + '][groups][' + groupCount + '][variables][' + sectionCount + '][' + field + ']');
            jQuery('div.inline-error', jQuery(this).parent()).attr('data-field', 'pages[' + pageSection + '][groups][' + groupCount + '][variables][' + sectionCount + '][' + field + ']');
        }
    });

    jQuery('.variable-property', clonedSection).selectpicker().change(function () {
        var property = jQuery(this).val();
        var unSelectedContainer = jQuery("div.property_details", clonedSection).not('select-' + property);
        jQuery("[data-field=property_details]", unSelectedContainer).attr('disabled', 'disabled');
        unSelectedContainer.addClass('d-none');
        var selectedContainer = jQuery("div.property_details", clonedSection).filter('.select-' + property);
        jQuery("[data-field=property_details]", selectedContainer).removeAttr('disabled').selectpicker('refresh');
        selectedContainer.removeClass('d-none');
        if (property == 'template_field') {
            if (!jQuery('.system-required-fields', clonedSection).hasClass('d-none')) {
                jQuery('.system-required-fields', clonedSection).addClass('d-none');
                jQuery("[data-field=is_required]", clonedSection).removeClass('d-none').selectpicker('destroy').val('no').selectpicker();
            }
            jQuery('select.select-template_field', clonedSection).selectpicker('destroy').val('').selectpicker();
        } else {
            jQuery('.mapped-fields', clonedSection).selectpicker('destroy').val('name').selectpicker();
            jQuery('.system-required-fields', clonedSection).removeClass('d-none');
            jQuery("[data-field=is_required]", clonedSection).addClass('d-none').selectpicker('destroy').val('yes').selectpicker();
            jQuery('#options-container', clonedSection).addClass('d-none');
        }

    });
    if (!variableExists) {
        var options = jQuery('#options-data', clonedSection).selectize({
            plugins: ['remove_button'],
            placeholder: 'options',
            delimiter: ',',
            persist: false,
            create: function (input) {
                return {
                    value: input,
                    text: input
                }
            }
        });
        var optionsSelectize = options[0].selectize;
        optionsFields = ['list', 'check_boxes', 'radio_buttons'];
        jQuery('select.select-template_field', clonedSection).on('change', function () {
            if (jQuery.inArray(jQuery(this).val(), optionsFields) !== -1) {
                jQuery('#options-container', clonedSection).removeClass('d-none');
            } else {
                jQuery('#options-container', clonedSection).addClass('d-none');
                optionsSelectize.clear();
            }
        });
    }
    jQuery('.variables-section-body', '#group-body-' + groupCount).append(clonedSection);
    jQuery('.variables-section-body', '#group-body-' + groupCount).removeClass('d-none');
    jQuery('#variables-count', container).val(sectionCount);
    tinymce.init({
        selector: '#variable-description-text-' + sectionCount,
        menubar: false,
        statusbar: false,
        branding: false,
        height: 100,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | link',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: { "style": "margin: 0;" },
    });
}

function goToPage(pageCount) {
    jQuery(".page-item", ".pages-list-section").each(function () {
        jQuery(this).removeClass('active');
    });
    jQuery('#page-item-' + pageCount).addClass('active');
    jQuery(".page-section", ".pages-section").each(function () {
        jQuery(this).addClass('d-none');
    });
    jQuery('#page-section-' + pageCount, '.pages-section').removeClass('d-none');
}
function goToGroup(pageSection, groupCount) {
    jQuery(".group-item", jQuery('.groups-list-section', '#page-section-body-' + pageSection)).each(function () {
        jQuery(this).removeClass('active');
    });
    jQuery('#group-item-' + groupCount, jQuery('.groups-list-section', '#page-section-body-' + pageSection)).addClass('active');
    jQuery(".group-details", '#page-section-body-' + pageSection).each(function () {
        jQuery(this).addClass('d-none');
    });
    jQuery('.variable-section-link', '#page-section-body-' + pageSection).attr('onClick', 'variableSectionEvent(\'' + pageSection + '\', \'' + groupCount + '\')');
    jQuery('#group-section-' + groupCount, '.groups-section').removeClass('d-none');
}

function deleteContractTemplate(params) {
    jQuery.ajax({
        url: getBaseURL('contract') + 'contract_templates/delete',
        type: 'POST',
        dataType: 'JSON',
        data: {
            'id': params.id
        },
        success: function (response) {
            if (response.result) {
                pinesMessage({ ty: 'information', m: _lang.deleteRecordSuccessfull });
                jQuery('#item-' + params.id, '.administration-container').remove();

            } else {
                pinesMessage({ ty: 'error', m: _lang.deleteRecordFailed });
                return false;
            }

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function removeDocument() {
    jQuery('.selected-document-link', container).addClass('d-none');
    jQuery('.upload-document-input', container).removeClass('d-none');
    jQuery('.upload-document-input', container).removeAttr('disabled');
}