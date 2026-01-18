function getAreaOfPracticeByCategory(templateId) { // templateId used in edit form to get saved data from DB
    var category = jQuery('#folder-template-category', '#folderTemplateForm').val();
    if(category == 'ip'){
        // IP cases do not have area of practices
        jQuery('#case-types', '#folderTemplateForm').html('<div class="margin-top"><span>' + _lang.ExpenseStatus['not-set'] + '</span></div>');
        return;
    }else{
        jQuery.ajax({
            url: getBaseURL() + 'case_folder_templates/page_actions',
            type: "POST",
            data: {category: category, action: 'get_types', id: templateId},
            dataType: "JSON",
            beforeSend: function () {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.html) {
                    jQuery('#case-types', '#folderTemplateForm').html(response.html);
                }
            }, complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
}
function formSubmit(){
    var container = jQuery('#folderTemplateForm');
    var createdFolders = {"nodes": []};
    jQuery("#folder-structure-container").jstree().open_all();
    var v =jQuery("#folder-structure-container").jstree(true).get_json('#', {'flat': true});
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
    var formData = jQuery("form#folderTemplateForm").serialize();
    jQuery.ajax({
        beforeSend: function () {
            jQuery('.loader-submit', container).addClass('loading');
            jQuery('#folder-template-submit-btn', container).attr('disabled', 'disabled');
        },
        data: formData,
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'case_folder_templates/page_actions',
        success: function (response) {
            if (response.result) {
                // load table
                pinesMessage({ty: 'success', m: _lang.done});
                window.location = window.location.href;
            } else {
                pinesMessage({ty: 'error', m: response.error});
            }
        }, complete: function () {
            jQuery('#folder-template-submit-btn').removeAttr('disabled');
            jQuery('.loader-submit', container).removeClass('loading');

        },
        error: defaultAjaxJSONErrorsHandler
    });
}
function deleteFolderTemplate(id){
    document.location = getBaseURL() + 'case_folder_templates/delete/' + id;
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
    jQuery('#folder-structure-container').jstree('open_all');
}
function collapseAllTreeNotes(){
    jQuery('#folder-structure-container').jstree('close_all');
}
jQuery(document).ready(function () {
    jQuery('.category-selectized').selectize({
        plugins: ['remove_button'],
        valueField: 'id',
        labelField: 'name',
        searchField: ['name'],
        options: availableCategories,
        createOnBlur: true,
        groups: [],
        optgroupField: 'class'
    });
    var folderTemplates = getNestedChildren(savedTemplates, 'j1_1');
    var folderData = [{ "text" : _lang.relatedDocuments}];
    if(templateId){ // build fodler structure from Database to be compatible with Jstree data
        folderData = [{ "text" : _lang.relatedDocuments, "children" : folderTemplates}];
    }
    jQuery('#folder-structure-container').jstree({
            'core' : {
                   "check_callback" : true,// so that create works
                    'data' : folderData
            },
            "plugins" : ["contextmenu", "dnd", "sort", "unique"]
    }).on('contextmenu', function(event) {
        console.log(event);
        if(event.target.parentElement.id == 'j1_1'){
            // remove all actions in contextmenu for root directory and keep only "Create"
            jQuery("ul.vakata-context.jstree-contextmenu li:not(:first)").remove();
        }
    }).on('loaded.jstree', function() {// expand all folders when loading the tree folders 
        jQuery('#folder-structure-container').jstree('open_all');
    });
    jQuery('#folderTemplateForm').find('input').keypress(function (e) {
        if (e.which == 13) {// Enter pressed?
            formSubmit();
        }
    });
    jQuery("#folder-template-submit-btn").click(function () {
        formSubmit();
    });
    if(templateId){
        getAreaOfPracticeByCategory(templateId);
    }
});
