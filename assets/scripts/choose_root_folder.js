function loadIntegrationGrid(provider) {
    var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
    var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
    var integrationDocumentsDataSrc = new kendo.data.DataSource({
        transport: {
            read: {
                url: getBaseURL() + "integrations/load_documents/" + provider + "/" + 1,
                dataType: "JSON",
                type: "POST",
                complete: function (XHRObj) {
                    jQuery('#loader-global').hide();
                    if (XHRObj.responseText == 'access_denied') {
                        return false;
                    }
                    if (XHRObj.responseText == 'login_needed') {
                        return false;
                    }
                    $response = jQuery.parseJSON(XHRObj.responseText || "null");
                    jQuery('#lineage', integrationDocumentsForm).val($response.lineage);
                    updateIntegrationCrumbLink($response.lineage, 'dropbox');
                    if (_lang.languageSettings['langDirection'] === 'rtl')
                        gridScrollRTL();
                    animateDropdownMenuInGrids('integrationDocumentsGrid_' + provider);
                },
                beforeSend: function () {
                    jQuery('#loader-global').show();
                }
            },
            parameterMap: function (options, operation) {
                if ("read" !== operation && options.models) {
                    return {
                        models: kendo.stringify(options.models)
                    };
                } else {
                    // options.module = module;
                    // options.module_record_id = moduleRecordId;
                    options.lineage = jQuery('#lineage', integrationDocumentsForm).val();
                }
                return options;
            }
        },
        schema: {type: "json", data: "data", total: "totalRows",
            model: {
                id: "id",
                fields: {
                    id: {editable: false, type: "string"},
                    name: {editable: false, type: "string"},
                    type: {editable: false, type: "string"},
                    lineage: {editable: false, type: "string"}
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
                        row['id'] = escapeHtml(row['id']);
                        row['name'] = escapeHtml(row['name']);
                        row['type'] = escapeHtml(row['type']);
                        row['lineage'] = escapeHtml(row['lineage']);
                        row['providerName'] = escapeHtml(row['providerName']);
                        rows.data.push(row);
                    }
                }
                return rows;
            }
        }, error: function (e) {
            defaultErrorHandler(e.xhr)
        },
        batch: true, pageSize: 10, serverPaging: false, serverFiltering: false, serverSorting: false
    });
    $integrationDocumentsGridOptions = {
        autobind: true,
        dataSource: integrationDocumentsDataSrc,
        columns: [
            {
                field: "id",
                template:
                    '<input type="checkbox" class="check-default-folder row-checkbox center-checkbox-grid" onclick="checkDefaultFile(this, \'#= lineage #\')" title="' + _lang.select + '"/>'+
                    '<i fileId="#= id #" class="pull-right fs-common-icon #= (type == \'folder\') ? \'selectable \' + getExtIcon(\'folder\') : getExtIcon(extension) #" onclick="toggleFileIconSelection(this, \'#= type #\')"></i>',
                sortable: false, filterable: false, title: " ", width: '80px'
            },
            {
                field: "name",
                template: '<i class="iconLegal"></i><a href="javascript:;" # if (type == \'file\') { # onclick="integrationDownloadFile(\'#= name #\', \'#= lineage #\', \'#= providerName #\')" # } else { # onclick="integrationLoadDirectoryContent(\'#= addslashes(lineage) #\', \'#= providerName #\')" # } #>#= name #</a>',
                title: _lang.name,
                width: '300px'
            }
        ],
        filterable: false,
        height: 500,
        pageable: {input: true, messages: _lang.kendo_grid_pageable_messages, numeric: false, pageSizes: [10, 15, 20], refresh: true},
        reorderable: false,
        resizable: true,
        scrollable: true,
        selectable: "single",
        sortable: false,
    };
    if (undefined === $integrationDocumentsGrid.data('kendoGrid')) {
        $integrationDocumentsGrid.kendoGrid($integrationDocumentsGridOptions);
        return false;
    }
    resetPagination($integrationDocumentsGrid);

}
function updateIntegrationCrumbLink(lineage, provider){
    var BreadcrumbContainer = jQuery('#integrationBreadcrumbContainer_' + provider);
    jQuery('.not-fixed', BreadcrumbContainer).remove();
    // count nb of slashes in lineage: one slash => root directry otherwise it is a sub folder
    if((lineage.match(new RegExp(/\//ig, "g")) || []).length > 0){
        // build bread crumb when accessing sub folders
        var directories = lineage.split('/');
        for (i in directories) {
            if(i > 0 && directories[i]){ // root directory is already exists
                jQuery("#initial-current-folder").addClass('d-none');
                var path = "";
                for (j in directories){
                    if(j> 0 && j<=i){
                        path += "/" + directories[j];
                    }
                }
                toPrintDirectory = "";
                if(parseInt(i) + 1 === directories.length){
                    toPrintDirectory  = '<span class="black_color">&nbsp;&nbsp;(' + _lang.currentFolder + ')</span>';
                }
                nodeToAdd = '<li class="breadcrumb-item not-fixed">';
                nodeToAdd += '<a href="javascript: integrationLoadDirectoryContent(\'' + addslashes(path) + '\', \'' + provider + '\');">' + directories[i] + '</a>'+ toPrintDirectory;
                nodeToAdd += '</li>';
                BreadcrumbContainer.append(nodeToAdd);
            }
            if(lineage === "/"){
                jQuery("#initial-current-folder").removeClass('d-none');
            }
        }
        jQuery('li.active', BreadcrumbContainer).removeClass('active bold');
    }
}

function integrationLoadDirectoryContent(documentLineage, provider) {
    var $integrationDocumentsGrid = jQuery('#integrationDocumentsGrid_' + provider);
    var integrationDocumentsForm = jQuery('#integrationDocumentsForm_' + provider);
    document.location.hash = '';
    jQuery('#term', integrationDocumentsForm).val('');
    jQuery('#integrationAttachmentLookUp', $integrationDocumentsGrid).val('');
    jQuery('#lineage', integrationDocumentsForm).val(documentLineage);
    var directories = documentLineage.split('/');
    jQuery('#integrationAttachmentLookUp').attr('placeholder', _lang.searchIn.sprintf([directories[directories.length-1]]));
    resetPagination($integrationDocumentsGrid);
    checkDefaultFile(false, documentLineage);
}
function chooseFolder(){
    var chhosegFolderGrid = jQuery('#integrationDocumentsGrid_' + 'dropbox').data("kendoGrid");
    var selectedItem = jQuery("#default-folder").val();
    if(selectedItem){
        jQuery.ajax({
            url: getBaseURL() + 'integrations/choose_default_folder/',
            data: {'selectedItem': selectedItem, "provider" : 'dropbox'},
            dataType: 'JSON',
            type: 'POST',
            success: function (response) {
                response.status ? pinesMessageV2({ty: 'success', m: _lang.dropboxEnableSyncDone}) : pinesMessageV2({ty: 'erroe', m: _lang.noSelectedFolder});
                document.location = getBaseURL() + 'integrations/';
            },
            error: defaultAjaxJSONErrorsHandler
        });
    } else{
        pinesMessageV2({ty: 'information', m: _lang.noSelectedFolder});
    }
}
function checkDefaultFile(element, lineage){
    element = element || false;
    lineage = lineage || false;
    if(element){
        var isChecked = jQuery(element).is(":checked");
        jQuery(".check-default-folder").prop('checked', false);
        if(isChecked){
            jQuery(element).prop('checked', true);
            jQuery("#default-folder").val(lineage);
            jQuery("#choose-folder-button").text(_lang.ChooseSelectedDirectory);
        }else{
            jQuery("#default-folder").val(currentLineage);
            jQuery("#choose-folder-button").text(_lang.SelectCurrentDirectory);
        }
    } else{
        jQuery("#default-folder").val(lineage);
        currentLineage = lineage;
        jQuery(".check-default-folder").prop('checked', false);
        jQuery("#choose-folder-button").text(_lang.SelectCurrentDirectory);
    }
}