function addCaseToContainer() {
    var formId = jQuery('#caseContainerContainer');
    var caseId = jQuery('#caseId', formId).val();
    if(caseId){
        jQuery.ajax({
            url: getBaseURL() + "case_containers/add",
            type: 'POST',
            dataType: 'JSON',
            data: {legal_case_container_id: containerId, legal_case_id: caseId, action: 'addCase'},
            beforeSend: function () {
            },
            success: function (response) {
                if(response.result){
                    displayRelatedCases();
                }else{
                    if(response.error == "self-related"){
                        pinesMessage({ty: 'warning', m: _lang.feedback_messages.addedNewRelatedCaseContainerFailedCaseExist.sprintf([caseId, containerId])});
                    }
                    else {
                        pinesMessage({ty: 'warning', m: _lang.feedback_messages.addedNewRelatedCaseContainerFailedCaseRelated.sprintf([caseId])});
                    }
                }
                jQuery('#caseId', formId).val('');
                jQuery('#caseLookUp', formId).val('');
                jQuery('#btnAdd', formId).attr('disabled', 'disabled');
            }, error: defaultAjaxJSONErrorsHandler
        });
    } else{
        jQuery('#caseLookUp').parent().addClass('has-error');
        pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.case])});
    }
}
function addContainerToContainer() {
    var formId = jQuery('#caseContainerContainer');
    var relatedContainerId = jQuery('#containerId', formId).val();
    if(relatedContainerId){
        jQuery.ajax({
            url: getBaseURL() + "case_containers/add_container",
            type: 'POST',
            dataType: 'JSON',
            data: {legal_case_container_id: containerId, related_container_id: relatedContainerId},
            beforeSend: function () {
            },
            success: function (response) {
                if(response.result){
                    displayRelatedContainers();
                } else{
                    if(response.error == 'relation_exists'){
                        pinesMessage({ty: 'warning', m: _lang.feedback_messages.addedNewRelatedContainerFailedContainerExist.sprintf([relatedContainerId, containerId])});
                    } else if(response.error == 'self_relation'){
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.addedNewRelatedContainerFailedContainerSelf.sprintf([relatedContainerId, containerId])});
                    } else if(response.error == 'data_missing'){
                        pinesMessage({ty: 'error', m: _lang.feedback_messages.requiredFormFields});
                        jQuery('#containerLookup').parent().addClass('has-error');
                    }
                }
                jQuery('#containerId', formId).val('');
                jQuery('#containerLookup', formId).val('');
                jQuery('#containerBtnAdd', formId).attr('disabled', 'disabled');
            }, error: defaultAjaxJSONErrorsHandler
        });
    } else{
        jQuery('#containerLookup').parent().addClass('has-error');
        pinesMessage({ty: 'error', m: _lang.validation_field_required.sprintf([_lang.caseContainer])});
    }
}
function removeCaseContainer(caseId) {
    if (confirm(_lang.confirmationDeleteRelation)) {
        jQuery.ajax({
            url: getBaseURL() + 'case_containers/add',
            type: 'POST',
            dataType: 'JSON',
            data: {legal_case_id: caseId, legal_case_container_id: containerId, action: 'deleteCase'},
            success: function (response) {
                if (response.result) { 
                    displayRelatedCases();
                } else {
                   pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function removeRelatedContainer(relatedContainerId) {
    if (confirm(_lang.confirmationDeleteRelation)) {
        jQuery.ajax({
            url: getBaseURL() + 'case_containers/add',
            type: 'POST',
            dataType: 'JSON',
            data: {related_container_id: relatedContainerId, legal_case_container_id: containerId, action: 'deleteRelatedContainer'},
            success: function (response) {
                if (response.result) { 
                    displayRelatedContainers();
                } else {
                   pinesMessage({ty: 'error', m: _lang.deleteRecordFailed});
                }
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
jQuery(document).ready(function () {
    var pageContainer = jQuery('#caseContainerContainer');
    jQuery("#caseLookUp", pageContainer).autocomplete({
        autoFocus: true,
        delay: 600,
        source: function (request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'cases/autocomplete',
                dataType: "json",
                data: request, error: defaultAjaxJSONErrorsHandler,
                success: function (data) {
                    if (data.length < 1) {
                        response([{label: _lang.no_results_matched.sprintf([request.term]), value: '', record: {id: -1, term: request.term}}]);
                    } else {
                        response(jQuery.map(data, function (item) {
                            return {label: item.subject, value: item.subject, record: item}
                        }));
                    }
                }
            });
        },
        response: function (event, ui) {
        },
        minLength: 3,
        select: function (event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#caseId', pageContainer).val(ui.item.record.id);
                jQuery('#btnAdd', pageContainer).removeAttr('disabled');
            }
        }
    });
    jQuery('#containerLookup').change(function(){
        jQuery(this).parent().removeClass('has-error');
    }).focusout(function(){
        jQuery(this).parent().removeClass('has-error');
    }).focusin(function(){
        jQuery(this).parent().removeClass('has-error');
    });
    jQuery('#caseLookUp').change(function(){
        jQuery(this).parent().removeClass('has-error');
    }).focusout(function(){
        jQuery(this).parent().removeClass('has-error');
    }).focusin(function(){
        jQuery(this).parent().removeClass('has-error');
    });
    var checkbox = ['slot-litigation', 'slot-corporate', 'slot-ip'];
    for (let i = 0; i < checkbox.length; i++){ // check / uncheck all nested checkboxes when clicking on a the main checkbox
        jQuery('.' + checkbox[i] + '-all').click(function () {
            jQuery('.' + checkbox[i]).each(function () {
                this.checked = jQuery('.' + checkbox[i] + '-all').is(':checked') ? true : false;
            });
        });
    }
    jQuery('.slot-stage-all').click(function () { // check / uncheck stage checkboxes when clicking on the main checkbox of the stage
        var mainCheckbox = jQuery(this);
        jQuery('input:checkbox', mainCheckbox.parent().parent().parent().parent()).each(function () {
            this.checked = mainCheckbox.is(':checked') ? true : false;
        });
    });
    jQuery('.slot-hearing-all').click(function () { // check / uncheck hearing checkboxes when clicking on the main checkbox of the hearings
        var mainCheckbox = jQuery(this);
        jQuery('input:checkbox', mainCheckbox.parent().parent().parent()).each(function () {
            this.checked = mainCheckbox.is(':checked') ? true : false;
        });
        if(mainCheckbox.is(':checked')){ // check stage section when clicking on all hearings
            jQuery('.slot-stage-all', jQuery(mainCheckbox.parent().parent().parent().parent())).attr('checked', 'checked');
        }
    });
    jQuery('.slot-hearing, .slot-litigation, .slot-corporate, .slot-ip', 'tbody').click(function () { // check / uncheck table columns when clicking on the checkbox of the hearing row, litigaiton row, corporate row or ip row 
        var mainCheckbox = jQuery(this);
        jQuery('input:checkbox', jQuery('thead', mainCheckbox.parent().parent().parent().parent())).each(function () {
            if(mainCheckbox.is(':checked')){
                this.checked = true;
            }
        });
    });
    jQuery('.slot-hearing', 'tbody').click(function () { // check stage checkbox when clicking on the checkbox of the hearing 
        if(jQuery(this).is(':checked')){
            jQuery('.slot-stage-all', jQuery(jQuery(this).closest('.stage-main-container'))).attr('checked', 'checked');
        }
    });
    jQuery('.slot-stage').click(function () { // check main stage checkbox when clicking on the checkbox of the stage metadata field like court type 
        if(jQuery(this).is(':checked')){
            jQuery('.slot-stage-all', jQuery(jQuery(this).parent().parent().parent().parent())).attr('checked', 'checked');
        }
    });
});
function displayRelatedCases() {
    jQuery.ajax({
        url: getBaseURL() + "case_containers/return_cases_details/",
        type: 'GET',
        dataType: "json",
        data: {legal_case_container_id: containerId},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                jQuery('#related-cases-details', '#caseContainerContainer').html(response.html);
                showLatestLitigationStagesOnly(jQuery('#latest-stages-only', '#caseContainerContainer'));
            } else {
                jQuery('#related-cases-details', '#caseContainerContainer').html('');
            }
        }
    });
}
function displayRelatedContainers() {
    jQuery.ajax({
        url: getBaseURL() + "case_containers/return_related_containers_details/",
        type: 'GET',
        dataType: "json",
        data: {legal_case_container_id: containerId},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if (response.html) {
                jQuery('#related-containers-details', '#caseContainerContainer').html(response.html);
                showLatestLitigationStagesOnly(jQuery('#latest-stages-only', '#caseContainerContainer'));
            } else {
                jQuery('#related-containers-details', '#caseContainerContainer').html('');
            }
        }
    });
}
function showLatestLitigationStagesOnly(that) {
    if (jQuery(that, '#caseContainerContainer').is(':checked')) {
        jQuery('.not-latest-stage').addClass('d-none');
        jQuery('.latest-stage').addClass('no-border').removeClass('border-bottom');
    } else {
        jQuery('.not-latest-stage').removeClass('d-none');
        jQuery('.latest-stage').removeClass('no-border');
        if (jQuery('.latest-stage').hasClass('border-bottom')) {
            jQuery('.latest-stage').addClass('border-bottom');
        }
    }
}
function caseContainerAdvancedExport(id, slotId) {
    window.location = getBaseURL() + 'case_containers/advanced_export_to_word/' + id + '/' + slotId;
}
function caseContainerExportWord(id) {
    window.location = getBaseURL() + 'case_containers/export_to_word/' + id + (jQuery('#latest-stages-only', '#caseContainerContainer').is(':checked') ? '/1' : '');
}
function caseContainerCallBack() {
    if (typeof $containerGridId !== 'undefined') {
        $containerGridId.data("kendoGrid").dataSource.read();
    }
    return true;
}
function caseContainerCreateSlots() {
    hideSlotForm();
    jQuery.ajax({
        url: getBaseURL() + "case_containers/create_slot_for_advanced_export",
        type: 'POST',
        dataType: "json",
        data: {legal_case_container_id: containerId},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if(response.allow_adding_slots){
                jQuery('#slot-name', '#slot-container').val(response.suggested_name);
                jQuery("input[type='checkbox'][class*='slot-']").removeClass('d-none');
                jQuery('#slot-container').removeClass('d-none');
                jQuery("#sumbit-slot").html(_lang.saveSlot).val(_lang.saveSlot);
                jQuery('#sumbit-slot').unbind('click').click(function(){
                   createContainerSlotForExport(); 
                });
            }else{
                pinesMessageV2({ty: 'error', m: response.msg});
            }
        }
    });
}
function hideSlotForm(){
    jQuery("input[type='checkbox'][class*='slot-']").prop("checked", false).addClass('d-none');
    jQuery('#slot-container').addClass('d-none');
}
function createContainerSlotForExport(){
    var formData = jQuery("form#slot-form").serializeArray();
    jQuery("input[type='checkbox'][class*='slot-']:checked").each(function() {
        formData.push({name: this.name, value: this.value});
    });
    formData.push({name: 'legal_case_container_id', value: containerId});
    formData.push({name: 'action', value: 'create'});
    jQuery.ajax({
        url: getBaseURL() + "case_containers/create_slot_for_advanced_export",
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if(response.result){
                hideSlotForm();
                loadContainerFields();
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            }else if(typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, jQuery('#caseContainerContainer'));
            }else if(response.error){
                pinesMessageV2({ty: 'error', m: response.error});
            }
        }
    });
}
function caseContainerDeleteSlotCallback(slotId){
    jQuery.ajax({
        url: getBaseURL() + "case_containers/delete_slot_for_advanced_export",
        type: 'POST',
        dataType: "json",
        data: {slot_id: slotId},
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            hideSlotForm();
            if(response.result){
                jQuery('.slot-menu-' + slotId, '.dropdown-submenu.show-extra-menu').remove();
                pinesMessageV2({ty: 'information', m: _lang.deleteRecordSuccessfull});
            }else{
                pinesMessageV2({ty: 'error', m: _lang.deleteRecordFailed});
            }
        }
    });
}
function nestedLoop(obj) {
    const res = {};
    function recurse(obj, current) {
        for (const key in obj) {
            let value = obj[key];
            if(value != undefined) {
                if (value && typeof value === 'object') {
                    recurse(value, key);
                } else {
                    // Do your stuff here to var value
                    alert('key: ' + key + ' ::::  Value: ' + value);
                }
            }
        }
    }
    recurse(obj);
    return res;
}
function getKeys(object) {
    function iter(o, p) {
        if (Array.isArray(o)) { return; }
        if (o && typeof o === 'object') {
            var keys = Object.keys(o);
            if (keys.length) {
                keys.forEach(function (k) { iter(o[k], p.concat(k)); });
            }
            return;
        }
        result.push(p.join('.'));
    }
    var result = [];
    iter(object, []);
    return result;
}
function caseContainerManageSlot(id, name, data){
    hideSlotForm(); // hide previous slot if was selected
    jQuery("#sumbit-slot").html(_lang.updateSlot).val(_lang.updateSlot);
    jQuery('#sumbit-slot').unbind('click').click(function(){
        editContainerSlotForExport(id);
    });
    jQuery('#slot-name', '#slot-container').val(name);
    jQuery("input[type='checkbox'][class*='slot-']").removeClass('d-none');
    jQuery('#slot-container').removeClass('d-none');
    jQuery.ajax({
        url: getBaseURL() + "case_containers/edit_slot_for_advanced_export/" + id,
        type: 'GET',
        dataType: "json",
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if(response.result){
                jQuery(response.data).each(function (index, value){
                    jQuery.each(value, function (_index, _data) {
                        if(_index == 'litigation'){
                            let litigationColumnCounter = true;
                            let litigationDataCounter = true;
                            jQuery.each(_data,function (_litigationIndex, _litigationData){
                                if(typeof _litigationData['columns'] !== 'undefined'){
                                    jQuery.each(_litigationData['columns'], function (_litigationColumnIndex, _litigationColumnData){
                                        jQuery("#slot-litigation-"+_litigationIndex+"-columns-"+_litigationColumnData).prop('checked', true);
                                    });
                                    if(typeof _litigationData['stage'] !== 'undefined'){
                                        jQuery.each(_litigationData['stage'], function (_litigationStageIndex, _litigationStageData){
                                            let hearingColumnCounter = true;
                                            let hearingDataCounter = true;
                                            if(typeof _litigationStageData['columns'] !== 'undefined'){
                                                jQuery.each(_litigationStageData['columns'], function (_litigationStageDataIndex, _litigationStageDataData){
                                                    jQuery("#slot-litigation-"+_litigationIndex+"-stage-"+_litigationStageIndex+"-columns-"+ _litigationStageDataData).prop('checked', true);
                                                });
                                                if(_litigationStageData['columns'].length === 8) jQuery("#slot-litigation-stages-"+ _litigationIndex+ "-" + _litigationStageIndex + "-all").prop('checked', true);
                                            }
                                            if(typeof _litigationStageData['hearing-columns'] !== 'undefined'){
                                                jQuery.each(_litigationStageData['hearing-columns'], function (_litigationHearingColumnIndex, _litigationHearingColumnData){
                                                    jQuery("#slot-litigation-"+_litigationIndex+"-hearing-"+_litigationStageIndex + "-" + _litigationHearingColumnData).prop('checked', true);
                                                });
                                                if(_litigationStageData['hearing-columns'].length !== 8) hearingColumnCounter = false;
                                            }
                                            if(typeof _litigationStageData['hearing-data'] !== 'undefined'){
                                                jQuery.each(_litigationStageData['hearing-data'], function (_litigationHearingDataIndex, _litigationHearingDataData){
                                                    jQuery("#slot-litigation-"+_litigationIndex+"-hearing-data-"+_litigationStageIndex + "-" + _litigationHearingDataData).prop('checked', true);
                                                });
                                                if(jQuery(".slot-hearing-litigation-"+ _litigationIndex+ "-stage-" + _litigationStageIndex + "-hearing-count").length !== _litigationStageData['hearing-data'].length) hearingDataCounter = false;
                                            }
                                            if(hearingColumnCounter && hearingDataCounter) jQuery("#slot-hearing-litigation-"+ _litigationIndex+ "-stage-" + _litigationStageIndex + "-all").prop('checked', true);
                                        });
                                    }
                                    if(_litigationData['columns'].length !== 12) litigationColumnCounter = false;
                                }else{
                                    jQuery.each(_litigationData, function (_litigationDataIndex, _litigationDataData){
                                        jQuery("#slot-litigation-"+_litigationDataData).prop('checked', true);
                                    });
                                    if(_litigationData.length !== jQuery(".slot-litigation-count").length) litigationDataCounter = false;
                                }
                            });
                            if(litigationDataCounter && litigationColumnCounter) jQuery("#slot-litigation-columns-all").prop('checked', true);
                        } else if(_index == 'corporate'){
                            if(typeof _data['columns'] !== 'undefined'){
                                jQuery.each(_data['columns'], function (_corporateColumnIndex, _corporateColumnData){
                                    jQuery("#slot-corporate-columns-"+_corporateColumnData).prop('checked', true);
                                });
                            }
                            if(typeof _data['data'] !== 'undefined'){
                                jQuery.each(_data['data'], function (_corporateDataIndex, _corporateDataData){
                                    jQuery("#slot-corporate-details-"+_corporateDataData).prop('checked', true);
                                });
                            }
                            if(_data['columns'].length === 10 && jQuery(".slot-corporate-count").length === _data['data'].length) jQuery("#slot-corporate-columns-all").prop('checked', true);
                        } else if(_index == 'ip'){
                            let ipColumnCounter = true;
                            let ipDataCounter = true;
                            jQuery.each(_data,function (_ipColumnIndex, _ipData) {
                                if (_ipColumnIndex === 'columns') {
                                    jQuery.each(_ipData, function (_ipColumnIndex, _ipColumnData) {
                                        jQuery("#slot-ip-columns-" + _ipColumnData).prop('checked', true);
                                    });
                                    if (_ipData.length !== 7) ipColumnCounter = false;
                                }else{
                                    jQuery.each(_ipData, function (_ipDataIndex, _ipColumnDataData) {
                                        jQuery("#slot-ip-details-"+_ipColumnDataData).prop('checked', true);
                                    });
                                    if(_ipData.length !== jQuery(".slot-ip-count").length) ipDataCounter = false;
                                }
                            });
                            if(ipDataCounter && ipColumnCounter) jQuery("#slot-ip-columns-all").prop('checked', true);
                        }
                    })
                });
            } else{
                pinesMessageV2({ty:'information', m: response.message})
            }
        }
    });
}
function slotNestedItems(savedData, key){
    
}
function editContainerSlotForExport(id){
    var formData = jQuery("form#slot-form").serializeArray();
    jQuery("input[type='checkbox'][class*='slot-']:checked").each(function() {
        formData.push({name: this.name, value: this.value});
    });
    formData.push({name: 'legal_case_container_id', value: containerId});
    formData.push({name: 'slot_id', value: id});
    jQuery.ajax({
        url: getBaseURL() + "case_containers/edit_slot_for_advanced_export",
        type: 'POST',
        dataType: "json",
        data: formData,
        error: defaultAjaxJSONErrorsHandler,
        success: function (response) {
            if(response.result){
                hideSlotForm();
                loadContainerFields();
                pinesMessageV2({ty: 'success', m: _lang.feedback_messages.updatesSavedSuccessfully});
            }else if(typeof response.validationErrors !== 'undefined' && response.validationErrors) {
                displayValidationErrors(response.validationErrors, jQuery('#caseContainerContainer'));
            }else if(response.error){
                pinesMessageV2({ty: 'error', m: response.error});
            }
        }
    });
}