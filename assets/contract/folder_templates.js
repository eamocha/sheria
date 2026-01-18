function formSubmit(){
    var container = jQuery('#folder-template-form');
    var createdFolders = {"nodes": []};
    jQuery("#folder-structure-container",container).jstree().open_all();
    var v =jQuery("#folder-structure-container",container).jstree(true).get_json('#', {'flat': true});
    for (i = 0; i < v.length; i++) {
        var z = v[i];
        if(z["id"] != 'j1_1'){ // exclude the root folder "Related Documents"
            var parentObj = jQuery('#' + z["id"]).parents('.jstree-node').first();
            var childObj = jQuery('#' + z["id"] + '_anchor', '#' + z["id"]).html();
            var period = childObj.lastIndexOf('</i>');
            createdFolders['nodes'].push({"folder_key": z["id"], "folder_name":childObj.substring(period + 4), "parent_key" : parentObj.attr('id')});
        }
    }
    jQuery('#folder_structure', container).val(JSON.stringify(createdFolders));
    var formData = jQuery("form#folder-template-form","#contract-folder-templates").serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#folder-template-submit-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL('contract') + 'folder_templates/page_actions',
        success: function (response) {
            if (response.result) {
                // load table
                pinesMessage({ty: 'success', m: _lang.done});
                window.location = window.location.href;
            } else {
                jQuery(".inline-error",container).html(response.error).removeClass('d-none');
            }
        }, complete: function () {
            jQuery('#folder-template-submit-btn',container).removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteFolderTemplate(id){
    document.location = getBaseURL('contract') + 'folder_templates/delete/' + id;
}
function getNestedChildren(arr, parent) {
    var out = [];
    for(var i in arr) {
        if(arr[i].parent_key == parent) {
            var children = getNestedChildren(arr, arr[i].folder_key);
            if(children.length) {
                arr[i].children = children;
            }
            out.push(arr[i]);
        }
    }
    return out;
}
function expandAllTreeNodes(){
    jQuery('#folder-structure-container','#folder-template-form').jstree('open_all');
}
function collapseAllTreeNotes(){
    jQuery('#folder-structure-container','#folder-template-form').jstree('close_all');
}
function selectizeContractTypes(){
    selectRemoveAllOptions('#folder-template-form',disabledOptions);
    jQuery('.contract-type-selectize','#contract-types').selectize({
        plugins: ['remove_button','select_remove_all_options'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableTypes,
        createOnBlur: true,
        groups: [],
        optgroupField: 'class',
        render: {
            option: function(item, escape) {
                if (jQuery.inArray(item.id,disabledOptions)!== -1) {
                    return '<div style="pointer-events: none; color: #aaaaaa;">' + escape(item.name) + '</div>';
                }
                return '<div>' + escape(item.name) + '</div>';
            }
        }
    });
}
jQuery(document).ready(function () {
    selectizeContractTypes();
    var folderTemplates = getNestedChildren(savedTemplates, 'j1_1');
    var folderData = [{ "text" : _lang.relatedDocuments}];
    if(templateId){ // build fodler structure from Database to be compatible with Jstree data
        folderData = [{ "text" : _lang.relatedDocuments, "children" : folderTemplates}];
    }
    jQuery('#folder-structure-container',"#folder-template-form").jstree({
            'core' : {
                   "check_callback" : true,// so that create works
                    'data' : folderData
            },
            "plugins" : ["contextmenu", "dnd", "sort", "unique"]
    }).on('contextmenu', function(event) {
        if(event.target.parentElement.id == 'j1_1'){
            // remove all actions in contextmenu for root directory and keep only "Create"
            jQuery("ul.vakata-context.jstree-contextmenu li:not(:first)").remove();
        }
    }).on('loaded.jstree', function() {// expand all folders when loading the tree folders 
        jQuery('#folder-structure-container',"#folder-template-form").jstree('open_all');
    });
    jQuery('#folder-template-form','#contract-folder-templates').find('input').keypress(function (e) {
        if (e.which == 13) {// Enter pressed?
            formSubmit();
        }
    });
    jQuery("#folder-template-submit-btn",'#folder-template-form').click(function () {
        formSubmit();
    });
    if(templateId){
        jQuery.ajax({
            url: getBaseURL('contract') + 'folder_templates/page_actions',
            type: "POST",
            data: {action: 'get_types', id: templateId},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.html) {
                    jQuery('#contract-types', '#folder-template-form').html(response.html);
                    selectizeContractTypes();
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
});
