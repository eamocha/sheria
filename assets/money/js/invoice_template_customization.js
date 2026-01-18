var detailsArray, allowedFileUploads;
jQuery(document).ready(function () {
    initializeColorPicker();
    jQuery("div", '#invoice-template-customization').find("[data-section=properties-settings]").addClass('selected');
    jQuery("div", '#invoice-template-customization').find("[data-content=properties-settings]").addClass('selected-settings-border');
    jQuery("#upload-logo").on('click', function (e) {
        jQuery("#file-browse").click();
    });
    jQuery("#file-browse").change(function () {
        readURL(this);
    });
    if(jQuery("#center-logo-checkbox", '#invoice-template-customization').is(':checked')){
        jQuery("#company-info-container-checkbox", '#invoice-template-customization').attr('disabled', 'disabled');
    }
    jQuery("[type='number']").keypress(function (event) {
        event.preventDefault();
    });
    jQuery("#top-margin", '#invoice-template-customization').bind('keyup mouseup keydown', function (e) {
        var margin = jQuery(this).val()
        if (margin === '') {
            margin = 0;
        }
        if (Number(margin) > Number(jQuery(this).attr('max'))) {
            e.preventDefault();
            margin = jQuery(this).attr('max');
            jQuery(this).val(margin);
        }
        jQuery(".template-body", '#invoice-template-customization').css('margin-top', margin + 'in');
    });
    jQuery("#invoice-information-font-size", '#invoice-template-customization').bind('keyup mouseup keydown', e => changeFontSize(e, ".body-header"));
    jQuery("#invoice-tables-font-size", '#invoice-template-customization').bind('keyup mouseup keydown', e => changeFontSize(e, ".invoice-table"));
    jQuery("#invoice-summation-font-size", '#invoice-template-customization').bind('keyup mouseup keydown', e => changeFontSize(e, ".total-section"));
    jQuery("#invoice-notes-font-size", '#invoice-template-customization').bind('keyup mouseup keydown', e => changeFontSize(e, ".body-footer"));
    jQuery("#invoice-title-0", '#invoice-template-customization').bind('keyup mouseup', function () {
        jQuery("span", jQuery('#title-container', '#invoice-template-customization')).html(jQuery(this).val());
    });
    jQuery("#invoice-title-fl1", '#invoice-template-customization').bind('keyup mouseup', function () {
        jQuery("span", jQuery('#title-container', '#invoice-template-customization')).html(jQuery(this).val());
    });
    jQuery("#invoice-title-fl2", '#invoice-template-customization').bind('keyup mouseup', function () {
        jQuery("span", jQuery('#title-container', '#invoice-template-customization')).html(jQuery(this).val());
    });
    showToolTip();
    tinymce.init({
        selector: '#header-notes',
        content_style: "body {font-size: 10pt;}",
        menubar: false,
        statusbar: false,
        branding: false,
        height: 180,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | alignleft aligncenter alignright',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: {"style": "margin: 0;"},
        formats: {
            underline: {inline: 'u', exact: true},
            alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'left'}},
            aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'center'}},
            alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'right'}},
            alignjustify: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'justify'}},
            p: { block: 'p', styles: { 'font-size': '10pt' } },
        }, paste_preprocess: function (plugin, args) {
            tinymce.activeEditor.windowManager.alert('Some of the pasted content will not stay with the same format');
        },
        paste_postprocess: function (pl, o) {
            jQuery("#company-info-container", '#invoice-template-customization').html(o.node.innerHTML);
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#header-notes_ifr').contents().find('body').prop("dir", "auto");
                jQuery('#header-notes_ifr').contents().find('body').focus();
                e.pasteAsPlainText = true;
            });
            editor.on('keyup', function (e) {
                jQuery("#company-info-container", '#invoice-template-customization').html(tinymce.activeEditor.getContent());
            });
        }
    });
    tinymce.init({
        selector: '#footer-notes',
        content_style: "body {font-size: 10pt;}",
        menubar: false,
        statusbar: false,
        branding: false,
        height: 100,
        resize: false,
        plugins: ['link', 'paste'],
        paste_text_sticky: true,
        apply_source_formatting: true,
        toolbar: 'formatselect | bold italic underline | alignleft aligncenter alignright',
        block_formats: 'Paragraph=p;Huge=h4;Normal=h5;Small=h6;',
        paste_word_valid_elements: "b,strong,i,em,h1,h2,span,br,div,p",
        paste_webkit_styles: "color font-size",
        paste_remove_styles: false,
        paste_retain_style_properties: "all",
        forced_root_block: 'p',
        forced_root_block_attrs: {"style": "margin: 0;"},
        formats: {
            underline: {inline: 'u', exact: true},
            alignleft: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'left'}},
            aligncenter: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'center'}},
            alignright: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'right'}},
            alignjustify: {selector: 'p,h1,h2,h3,h4,h5,h6,td,th,div,ul,ol,li,table,img', attributes: {align: 'justify'}},
            p: { block: 'p', styles: { 'font-size': '10pt' } }
        },
        paste_postprocess: function (pl, o) {
            jQuery("#footer-container", '#invoice-template-customization').html(helpers.decodeHtml(o.node.innerHTML));
        },
        setup: function (editor) {
            editor.on('init', function (e) {
                jQuery('#footer-notes_ifr').contents().find('body').prop("dir", "auto");
                jQuery('#footer-notes_ifr').contents().find('body').focus();
                e.pasteAsPlainText = true;
            });
            editor.on('keyup', function (e) {
                jQuery("#footer-container", '#invoice-template-customization').html(helpers.decodeHtml(tinymce.activeEditor.getContent()));
            });
        }
    });
    jQuery("#show-summary", '#invoice-template-customization').selectpicker();
    toggleFullWidthLayout(jQuery("#margin-narrow").is(":checked"));
    changeTablesBorders(jQuery("input[name='body[css][tables-borders]']:checked").val());
    changePageSize(jQuery("input[name='properties[page-size]']:checked").val());
    changePageFont();
    changePageOrientation(jQuery("input[name='properties[page-orientation]']:checked").val());
    showUserCode();
    showSummary();

    jQuery('#company-info-container-help-btn').tooltipster({
        content: jQuery('#company-info-container-help-info').html(),
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 500,
        interactive: true,
        repositionOnScroll: true,
        position: 'bottom'

    });
});
function showOptionSettings(optionId) {
    jQuery("div", '#invoice-template-customization').removeClass('selected-settings-border');
    jQuery('.config-option', '#invoice-template-customization').removeClass('selected');
    jQuery("div", '#invoice-template-customization').find("[data-section=" + optionId + "]").addClass('selected');
    jQuery('.settings-details', '#invoice-template-customization').addClass('d-none');
    jQuery('#' + optionId, '#invoice-template-customization').removeClass('d-none');
    jQuery("div", '#invoice-template-customization').find("[data-content=" + optionId + "]").addClass('selected-settings-border');
}
//toggle item display, if the checkbox is checked then remove the hide class else add the hide class, depending on the id and parent selector of the parameter itemId
function toggleItemDisplay(that, itemId, settingsSection) {
    var isChecked = jQuery(that).is(':checked');
    if (isChecked) {
        if (that.id === "show-logo-checkbox") {
            jQuery("#center-logo-checkbox", '#invoice-template-customization').attr('disabled', false);
            jQuery("#system-size-checkbox", '#invoice-template-customization').attr('disabled', false);
            jQuery("#full-width-checkbox", '#invoice-template-customization').attr('disabled', false);
            jQuery("#upload-logo", '#invoice-template-customization').attr('disabled', false);
        }
        if (itemId === "center-logo"){
            if(!jQuery('#logo-container', '#invoice-template-customization').hasClass('d-none')){
                jQuery('#logo-container-parent', '#invoice-template-customization').removeClass('pull-left');
                jQuery('#logo-container-parent', '#invoice-template-customization').removeClass('pull-right');
            }
            toggleItemDisplay(jQuery('#company_info_container', '#invoice-template-customization'), 'company-info-container', 'header');
            jQuery("#company-info-container-checkbox", '#invoice-template-customization').attr('checked', false);
            jQuery("#company-info-container-checkbox", '#invoice-template-customization').attr('disabled', 'disabled');
        }
        else if (itemId === "paid-amount-container") {
            jQuery('.paid-amount-container', '#invoice-template-customization').removeClass('d-none');
        }
        else if (itemId === "invoice-description-column") {
            jQuery('.invoice-description-column', '#invoice-template-customization').removeClass('d-none');
            colspan = $('.subtotal-per-table', '#invoice-template-customization').attr('colSpan');
            colspan = parseInt(colspan) + 1;
            $('.subtotal-per-table', '#invoice-template-customization').attr('colspan', colspan);
        }
        
        else if (that.id === "system-size-checkbox") {
            modifyImageSize(true);
        }
        else {
            jQuery('#' + itemId, '#invoice-template-customization').removeClass('d-none');
        }
    } else {
        if (that.id === "show-logo-checkbox") {
            jQuery("#center-logo-checkbox", '#invoice-template-customization').attr('disabled', 'disabled');
            jQuery("#system-size-checkbox", '#invoice-template-customization').attr('disabled', 'disabled');
            jQuery("#full-width-checkbox", '#invoice-template-customization').attr('disabled', 'disabled');
            jQuery("#upload-logo", '#invoice-template-customization').attr('disabled', 'disabled');
        }
        if (itemId === "center-logo") {
            if(!jQuery('#logo-container', '#invoice-template-customization').hasClass('d-none')){
                if (!jQuery('#is_rtl', '#template-customization-form').val()) {
                    jQuery('#logo-container-parent', '#invoice-template-customization').addClass('pull-right');
                } else {
                    jQuery('#logo-container-parent', '#invoice-template-customization').addClass('pull-left');
                }
                jQuery("#company-info-container-checkbox", '#invoice-template-customization').attr('disabled', false);
            }
        }
        else if (itemId === "paid-amount-container") {
            jQuery('.paid-amount-container', '#invoice-template-customization').addClass('d-none');
        }
        else if (itemId === "invoice-description-column") {
            jQuery('.invoice-description-column', '#invoice-template-customization').addClass('d-none');
            colspan = $('.subtotal-per-table', '#invoice-template-customization').attr('colspan');
            colspan = parseInt(colspan) - 1;
            $('.subtotal-per-table', '#invoice-template-customization').attr('colspan', colspan);
        }
        else if (that.id === "system-size-checkbox") {
            modifyImageSize(false);
        }
        else {
            jQuery('#' + itemId, '#invoice-template-customization').addClass('d-none');
        }
    }
    detailsArray[settingsSection]['show'][itemId] = isChecked;
}


function readURL(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        allowedFileUploads = allowedFileUploads ? (allowedFileUploads.constructor === Array ? allowedFileUploads : allowedFileUploads.split('|')) : false;
        reader.onload = function (e) {
            var image = new Image();
            image.src = e.target.result;
            image.onload = function () {
                var MAX_WIDTH = 350;
                var MAX_HEIGHT = 250;
                var width = this.width;
                var height = this.height;
                var systemLogoSizeChecked = jQuery("#system-size-checkbox", '#invoice-template-customization').is(':checked');
                if(systemLogoSizeChecked){
                    if (width > height) {
                        if (width > MAX_WIDTH) {
                            height *= MAX_WIDTH / width;
                            width = MAX_WIDTH;
                        }
                    } else {
                        if (height > MAX_HEIGHT) {
                            width *= MAX_HEIGHT / height;
                            height = MAX_HEIGHT;
                        }
                    }
                }
                jQuery('#logo-image', '#invoice-template-customization').attr('src', this.src);
                jQuery('#logo-image', '#invoice-template-customization').attr('width', width);
                jQuery('#logo-image', '#invoice-template-customization').attr('height', height);
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}
function submitInvoiceTemplateCustomization(id) {
    var container = jQuery('#invoice-template-customization');
    jQuery('[name="header[general][notes]"]', container).text(tinymce.editors[0].getContent());
    jQuery('[name="footer[general][notes]"]', container).text(tinymce.editors[1].getContent());
    var formData = new FormData(document.getElementById('template-customization-form'));
    formData.append('settings', JSON.stringify(detailsArray));
    if (detailsArray['body']['general']['line_items']) {
        formData.append('body[general][line_items][expenses]', detailsArray['body']['general']['line_items']['expenses']);
        formData.append('body[general][line_items][items]', detailsArray['body']['general']['line_items']['items']);
        formData.append('body[general][line_items][time_logs]', detailsArray['body']['general']['line_items']['time_logs']);
    }
    formData.delete('invoice-margin');
    if (typeof jQuery('input[type=file]', container)[0].files[0] !== 'undefined') {
        formData.append('logo', jQuery('input[type=file]', container)[0].files[0]);
    }
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('.btn-submit', container).attr('disabled', 'disabled');
        },
        url: getBaseURL('money') + "organization_invoice_templates/save/" + id,
        type: "POST",
        data: formData,
        contentType: false, // required to be disabled
        cache: false,
        processData: false, // required to be disabled
        success: function (response)
        {
            if (response.result) {
                pinesMessage({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            } else {
                var errorMsg = '';
                for (i in response.validation_errors) {
                    errorMsg += '<li>' + response.validation_errors[i] + '</li>';
                }
                if (errorMsg != '') {
                    pinesMessage({ty: 'error', m: '<ul>' + errorMsg + '</ul>'});
                }
            }
        }, complete: function () {
            jQuery('.btn-submit', container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

function showSummary() {
    let summaryValue = jQuery("#show-summary :selected").val();
    if (summaryValue == null) return;
    let timeLogsSummaryContainer = jQuery("#time-logs-summary-container");
    let invoiceTemplateSummary = jQuery("#invoice-template-summary");
    let templateItemsContent = jQuery("#template-items-content");
    let showUserCodeCheckboxContainer = jQuery("#show-user-code-checkbox-container");
    if (summaryValue == 0) {
        timeLogsSummaryContainer.addClass("d-none");
        invoiceTemplateSummary.addClass("d-none");
        templateItemsContent.removeClass("d-none");
        showUserCodeCheckboxContainer.addClass("d-none");
    } else if (summaryValue == 1) {
        timeLogsSummaryContainer.removeClass("d-none");
        invoiceTemplateSummary.addClass("d-none");
        templateItemsContent.removeClass("d-none");
        showUserCodeCheckboxContainer.removeClass("d-none");
    } else {
        timeLogsSummaryContainer.addClass("d-none");
        invoiceTemplateSummary.removeClass("d-none");
        showUserCodeCheckboxContainer.removeClass("d-none");
        templateItemsContent.addClass("d-none");
    }
    detailsArray['body']['show']['time-logs-summary-container'] = summaryValue;
}

function showUserCode() {
    var isChecked = jQuery("#show-user-code-checkbox").is(':checked');
    if (isChecked) {
        jQuery(".show-user-code",jQuery('#invoice-template-customization')).removeClass('d-none');
        jQuery("#total-summary-time-logs",jQuery('#invoice-template-customization')).attr("colspan",4);
    } else {
        jQuery(".show-user-code",jQuery('#invoice-template-customization')).addClass('d-none');
        jQuery("#total-summary-time-logs",jQuery('#invoice-template-customization')).attr("colspan",3);
    }
    detailsArray['body']['show']['show-user-code'] = isChecked;
}

function toggleFullWidthLayout(isEnabled) {
    if (isEnabled) {
        jQuery(".template-body", '#invoice-template').addClass('mr-5 ml-5');
    } else {
        jQuery(".template-body", '#invoice-template').removeClass('mr-5 ml-5');
    }
    detailsArray['body']['show']['full_width_layout'] = isEnabled;
}

function modifyImageSize(enabled) {
    var MAX_WIDTH = 350;
    var MAX_HEIGHT = 250;
    var width =  jQuery("#logo-image", '#invoice-template-customization').width();
    var height = jQuery("#logo-image", '#invoice-template-customization').height();
    if (enabled){
        if (width > height) {
            if (width > MAX_WIDTH) {
                height *= MAX_WIDTH / width;
                width = MAX_WIDTH;
            }
        } else {
            if (height > MAX_HEIGHT) {
                width *= MAX_HEIGHT / height;
                height = MAX_HEIGHT;
            }
        }
    }
    jQuery('#logo-image', '#invoice-template-customization').css('width', enabled ? width : 'unset');
    jQuery('#logo-image', '#invoice-template-customization').css('height', enabled ? height : 'unset');
}

function changeTablesBorders(borderType) {
    if (borderType === "none") {
        jQuery('.invoice-table').removeClass('table-bordered table-border-row table-border-column');
        jQuery('.invoice-table').addClass('table-border-none');
    }
    else if (borderType === "rows") {
        jQuery('.invoice-table').removeClass('table-border-none table-bordered table-border-column');
        jQuery('.invoice-table').addClass('table-border-row');
    }
    else if (borderType === "columns") {
        jQuery('.invoice-table').removeClass('table-border-none table-bordered table-border-row');
        jQuery('.invoice-table').addClass('table-border-column');
    }
    else if (borderType === "both") {
        jQuery('.invoice-table').removeClass('table-border-none table-border-row table-border-column');
        jQuery('.invoice-table').addClass('table-bordered');
    }
    detailsArray['body']['css']['tables-borders'] = borderType;
}

function initializeColorPicker() {
    const colorsPalette = [
        ["#000", "#444", "#666", "#999", "#ccc", "#eee", "#f3f3f3", "#fff"],
        ["#f00", "#f90", "#ff0", "#0f0", "#0ff", "#00f", "#90f", "#f0f"],
        ["#f4cccc", "#fce5cd", "#fff2cc", "#d9ead3", "#d0e0e3", "#cfe2f3", "#d9d2e9", "#ead1dc"],
        ["#ea9999", "#f9cb9c", "#ffe599", "#b6d7a8", "#a2c4c9", "#9fc5e8", "#b4a7d6", "#d5a6bd"],
        ["#e06666", "#f6b26b", "#ffd966", "#93c47d", "#76a5af", "#6fa8dc", "#8e7cc3", "#c27ba0"],
        ["#c00", "#e69138", "#f1c232", "#6aa84f", "#45818e", "#3d85c6", "#674ea7", "#a64d79"],
        ["#900", "#b45f06", "#bf9000", "#38761d", "#134f5c", "#0b5394", "#351c75", "#741b47"],
        ["#600", "#783f04", "#7f6000", "#274e13", "#0c343d", "#073763", "#20124d", "#4c1130"]
    ];
    const pickersIds = ["#invoice-information-font-color", "#invoice-tables-font-color", "#invoice-summation-font-color", "#invoice-notes-font-color", "#tables-headers-background-color", "#page-color"];
    const elementsClasses = [".body-header", ".invoice-table", ".total-section", ".body-footer", ".table-header", ".invoice-template-content"];
    elementsClasses.forEach((element, index) => {
        var spectrumPaletteOptions = {
            palette: colorsPalette,
            containerClassName: 'bg-white',
            preferredFormat: "hex",
            showButtons: false,
            showPalette: true,
            change: function (color) {
                if (element === '.table-header' || element === '.invoice-template-content') {
                    jQuery(element, '#invoice-template-customization').css('background-color', color.toHexString());
                } else {
                    jQuery(element, '#invoice-template-customization').css('color', color.toHexString());
                }
            }
        }
        jQuery(pickersIds[index]).spectrum(spectrumPaletteOptions);
    });
}

function changePageSize(size) {
    if (size === "A4") {
        jQuery('.invoice-template-content', '#invoice-template-customization').removeClass("size-legal size-letter");
        jQuery('.invoice-template-content', '#invoice-template-customization').addClass("size-a4");
        jQuery('#page-size-text').text("8-1/4 x 11-3/4 inches");
    } else if (size === "letter") {
        jQuery('.invoice-template-content', '#invoice-template-customization').removeClass("size-legal size-a4");
        jQuery('.invoice-template-content', '#invoice-template-customization').addClass("size-letter");
        jQuery('#page-size-text').text("8.5 x 11 inches");
    } else if (size === "legal") {
        jQuery('.invoice-template-content', '#invoice-template-customization').removeClass("size-a4 size-letter");
        jQuery('.invoice-template-content', '#invoice-template-customization').addClass("size-legal");
        jQuery('#page-size-text').text("8.5 x 14 inches");
    }
}

function changePageFont() {
    const fontName = jQuery("#page-font :selected").val();
    jQuery('.invoice-template-content', '#invoice-template-customization').css('font-family', fontName);
}

function changePageOrientation(orientation) {
    if (orientation === "landscape") {
        jQuery('.invoice-template-content', '#invoice-template-customization').css('width', 'auto');
        jQuery('.invoice-template-content', '#invoice-template-customization').css('height', '600px');
    } else {
        jQuery('.invoice-template-content', '#invoice-template-customization').css('width', '');
        jQuery('.invoice-template-content', '#invoice-template-customization').css('height', '');
    }
}

function movePosition(group, element, direction) {
    orderList = detailsArray['body']['invoice_information_order'][group].slice();
    const index = orderList.indexOf(element);
    if ((direction === "up" && index === 0) || (direction === "down" && index === orderList.length - 1)) {
        return;
    }
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    orderList.splice(index, 1);
    orderList.splice(newIndex, 0, element);
    detailsArray['body']['invoice_information_order'][group] = orderList;
    var wrapper = jQuery('#' + element).closest('div');
    if (direction === "up") {
        wrapper.insertBefore(wrapper.prev());
    } else {
        wrapper.insertAfter(wrapper.next());
    }
}

function changeFontSize(event, section) {
    var size = jQuery(event.target).val();
    if (size === '') {
        size = 0;
    }
    if (Number(size) > Number(jQuery(event.target).attr('max'))) {
        event.preventDefault();
        size = jQuery(event.target).attr('max');
        jQuery(event.target).val(size);
    }
    jQuery(section, '#invoice-template-customization').css('font-size', size + 'pt');
}

function moveTablePosition(table, direction) {
    let lineItems = detailsArray['body']['general']['line_items'];
    let summaryValue = jQuery("#show-summary :selected").val();
    const tableNumber = lineItems[table];
    if ((direction === "up" && tableNumber <= 1) || (direction === "down" && tableNumber >= 3)) {
        return;
    }
    lineItems = swapKeyValue(lineItems);
    const tablesOrder = [];
    tablesOrder.push(lineItems[1]);
    tablesOrder.push(lineItems[2]);
    tablesOrder.push(lineItems[3]);
    const index = tablesOrder.indexOf(table);
    const newIndex = direction === 'up' ? index - 1 : index + 1;
    tablesOrder.splice(index, 1);
    tablesOrder.splice(newIndex, 0, table);
    const newOrder = {
        "expenses": tablesOrder.indexOf("expenses") + 1,
        "time_logs": tablesOrder.indexOf("time_logs") + 1,
        "items": tablesOrder.indexOf("items") + 1
    };
    detailsArray['body']['general']['line_items'] = newOrder;
    var wrapper = null;
    if (summaryValue === '2') {
        wrapper = jQuery(".invoice-table", "#invoice-template-summary").eq(tableNumber - 1);;
    } else {
        wrapper = jQuery(".invoice-table", "#template-items-content").eq(tableNumber - 1);
    }
    if (direction === "up") {
        wrapper.insertBefore(wrapper.prev());
    } else {
        wrapper.insertAfter(wrapper.next());
    }
}

function swapKeyValue(object) {
    return Object.entries(object).reduce((acc, [key, value]) => (acc[value] = key, acc), {});
}
