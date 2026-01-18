// Define helper functions FIRST, before the grid initialization
function formatFileSize(bytes) {
    if (!bytes || bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function getDocumentStatus(statusId) {
    var statusMap = {
        "1": "Draft",
        "2": "Final",
        "3": "Approved",
        "4": "Rejected"
    };
    return statusMap[statusId] || "Unknown";
}

function getDocumentType(typeId) {
    var typeMap = {
        "1": "Court Order",
        "2": "Pleading",
        "3": "Affidavit",
        "4": "Memo",
        "5": "Bill"
    };
    return typeMap[typeId] || "Other";
}

// Alternative: Check if these functions already exist in global scope
if (typeof formatFileSize === 'undefined') {
    window.formatFileSize = formatFileSize;
}
if (typeof getDocumentStatus === 'undefined') {
    window.getDocumentStatus = getDocumentStatus;
}
if (typeof getDocumentType === 'undefined') {
    window.getDocumentType = getDocumentType;
}
 
function uploadDocument(case_id) {
    jQuery.ajax({
        dataType: 'JSON',
        url: getBaseURL() + "cases/upload_file_from_case_view/" + case_id,
        type: "GET",
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function (response) {
            if (response && response.success) {
                if (response.html) {
                    var $incoming = jQuery(response.html);
                    var modalEl = $incoming.filter('.modal').attr('id') || $incoming.find('.modal').first().attr('id') || 'case-upload-modal';
                    
                    jQuery('#' + modalEl).remove();
                    jQuery('body').append($incoming);
                   
                    var $modal = jQuery('#' + modalEl);
                    if (!$modal.length) {
                        $modal = $incoming.filter('.modal').length ? $incoming.filter('.modal') : $incoming.find('.modal').first();
                    }
                    
                    // Fix the form action URL to include the correct case_id
                    $modal.find('form').attr('action', getBaseURL() + "cases/upload_file/" + case_id);
                    $modal.find('input[name="module_record_id"]').val(case_id);
                    
                    // Show modal
                    $modal.modal('show');

                    // Bind the form submission
                    bindUploadFormOnModal($modal, case_id);

                    // Cleanup modal element after it's hidden
                    $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                        jQuery(this).remove();
                    });
                } else {
                    if (response.message) pinesMessage({ty: 'success', m: response.message});
                    jQuery("#case-docs-summary-dataGrid").data("kendoGrid").dataSource.read();
                }
            } else {
                if (response && response.html) {
                    var $incoming = jQuery(response.html);
                    jQuery('body').append($incoming);
                    var $modal = $incoming.filter('.modal').length ? $incoming.filter('.modal') : $incoming.find('.modal').first();
                    if ($modal.length) {
                        // Fix the form action URL
                        $modal.find('form').attr('action', getBaseURL() + "cases/upload_file/" + case_id);
                        $modal.find('input[name="module_record_id"]').val(case_id);
                        
                        $modal.modal('show');
                        bindUploadFormOnModal($modal, case_id);
                        $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                            jQuery(this).remove();
                        });
                    }
                } else {
                    pinesMessage({ty: 'error', m: response.message || 'Failed to load upload form.'});
                }
            }
        }
    });
}

function submitUploadForm($form, $modal, case_id) {
    var formData = new FormData($form[0]);
    jQuery.ajax({
        url: $form.attr('action') || (getBaseURL() + "cases/upload_file"),
        type: ($form.attr('method') || 'POST').toUpperCase(),
        data: formData,
        dataType: 'json',
        processData: false,
        contentType: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            // Disable submit button to prevent multiple submissions
            $modal.find('button[type="submit"]').prop('disabled', true).html('Uploading...');
        },
        success: function(resp) {
            jQuery('#loader-global').hide();
            // Re-enable submit button
            $modal.find('button[type="submit"]').prop('disabled', false).html('Save');
                       
           initializeCaseDocs(case_id);
            
            // Close and cleanup modal
            $modal.modal('hide');
            $modal.off('hidden.bs.modal').on('hidden.bs.modal', function () {
                jQuery(this).remove();
            });
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        error: function () {
            jQuery('#loader-global').hide();
            pinesMessage({ty: 'error', m: 'Upload failed. Please try again.'});
        }
    });
}

// Define a helper to bind submit event on the modal's form
function bindUploadFormOnModal($modal, case_id) {
    // Remove existing event handlers to prevent duplicates
    $modal.off('submit', 'form');
    $modal.off('click', '#form-submit');
    
    // Bind form submission when form is submitted
    $modal.on('submit', 'form', function (e) {
        e.preventDefault();
        submitUploadForm(jQuery(this), $modal, case_id);
    });
    
    // Also bind the Save button click to form submission
    $modal.on('click', '#form-submit', function (e) {
        e.preventDefault();
        var $form = $modal.find('form');
        submitUploadForm($form, $modal, case_id);
    });
}

// Handle upload success and refresh grid
function handleUploadSuccess(response) {
    if (response && response.success) {
        pinesMessage({ty: 'success', m: response.message || 'Document uploaded successfully!'});
        // Refresh the grid
        jQuery("#case-docs-summary-dataGrid").data("kendoGrid").dataSource.read();
    } else {
        pinesMessage({ty: 'error', m: response.message || response.error || 'Upload failed!'});
    }
}

// NOW initialize the grid
function initializeCaseDocs(case_id){
    jQuery("#case-docs-summary-dataGrid").kendoGrid({
        dataSource: {
            transport: {
                read: {
                    url: getBaseURL() + "cases/load_documents",
                    dataType: "json",
                    type: "POST",
                    async: false,
                    complete: function (XHRObj) {
                        jQuery('#loader-global').hide();
                        if (XHRObj.responseText == 'access_denied' || XHRObj.responseText == 'login_needed') {
                            return false;
                        }
                        var $response = jQuery.parseJSON(XHRObj.responseText || "null");
                        if (undefined !== $response.error) {
                            pinesMessage({ty: 'error', m: $response.error});
                        }
                    },
                    beforeSend: function () {
                        jQuery('#loader-global').show();
                    },
                    data: function() {
                        return {
                            module: "case",
                            module_record_id: case_id,
                            type: "file"
                        };
                    }
                },
                parameterMap: function (options, operation) {
                    if (operation === "read") {
                        options.module = "case";
                        options.module_record_id = case_id;
                        options.type = "file";
                        if (options.page) options.page = options.page;
                        if (options.pageSize) options.pageSize = options.pageSize;
                        if (options.skip) options.skip = options.skip;
                        if (options.take) options.take = options.take;
                        if (options.sort) options.sort = options.sort;
                    }
                    return options;
                }
            },
            schema: {
                data: "data",
                total: "totalRows",
                model: {
                    id: "id",
                    fields: {
                        id: { type: "number" },
                        name: { type: "string" },
                        full_name: { type: "string" },
                        extension: { type: "string" },
                        document_status_id: { type: "string" },
                        document_type_id: { type: "string" },
                        creator_full_name: { type: "string" },
                        createdOn: { type: "date" },
                        size: { type: "number" },
                        version: { type: "number" },
                        visible_in_cp: { type: "number" },
                        visible_in_ap: { type: "number" },
                        system_document: { type: "number" },
                        private: { type: "number" },
                        is_accessible: { type: "number" }
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
                            row['full_name'] = escapeHtml(row['full_name']);
                            row['name'] = escapeHtml(row['name']);
                            rows.data.push(row);
                        }
                    }
                    return rows;
                }
            },
            error: function (e) {
                if (typeof defaultAjaxJSONErrorsHandler !== 'undefined') {
                    defaultAjaxJSONErrorsHandler(e.xhr);
                }
            },
            pageSize: 10,
            serverPaging: true,
            serverSorting: true,
            serverFiltering: true
        },
        columns: [
            {
                field: "name", 
                title: "Document Name", 
                width: 200,
                template: '#= name # (#= extension #)'
            },
            { 
                field: "version", 
                title: "Version", 
                width: 80 
            },
            {
                field: "document_status_id",
                title: "Status",
                width: 120,
                template: "#= getDocumentStatus(document_status_id) #"
            },
            {
                field: "document_type_id",
                title: "Document Type",
                width: 150,
                template: "#= getDocumentType(document_type_id) #"
            },
            { 
                field: "size", 
                title: "Size", 
                width: 100,
                template: "#= formatFileSize(size) #"
            },
            { 
                field: "creator_full_name", 
                title: "Created By", 
                width: 150 
            },
            { 
                field: "createdOn", 
                title: "Created On", 
                format: "{0:yyyy-MM-dd HH:mm}", 
                width: 150 
            },
            {
                title: "Actions",
                width: 120,
                template: 
                    '<div class="dropdown" data-index="#= id #" id="docs-actions-menu_#= id #">' + 
                        '<button class="btn btn-default btn-xs" data-toggle="dropdown">' + 
                            '<i class="purple_color fa-solid fa-gear"></i> <span class="caret no-margin"></span>' + 
                        '</button>' +
                        '<div class="dropdown-menu" role="menu" aria-labelledby="dLabel" id="docs-dropdown-menu_#= id #">' +
                            '<a class="dropdown-item" target="_blank" href="' + getBaseURL() + 'cases/view_document/#= id #/#= encodeURIComponent(full_name) #">View</a>' +
                            '<a class="dropdown-item" href="' + getBaseURL() + 'cases/download_document/#= id #">Download</a>' +
                            '# if (system_document != "1") { #' +
                                '<a class="dropdown-item" href="javascript:;" onclick="editDocument(#= id #)">Edit</a>' +
                                '<a class="dropdown-item" href="javascript:;" onclick="deleteDocument(#= id #)">Delete</a>' +
                            '# } #' +
                        '</div>' +
                    '</div>'
            }
        ],
        toolbar: [
            {
                name: "upload",
                template: '<button class="btn btn-primary" onclick="uploadDocument('+case_id+')">Upload Document</button>'
            }
        ],
        scrollable: true,
        sortable: true,
        pageable: {
            refresh: true,
            pageSizes: [10, 20, 50, 100],
            buttonCount: 5,
            messages: window._lang ? window._lang.kendo_grid_pageable_messages : undefined
        },
        dataBound: function() {
            if (typeof animateDropdownMenuInGrids !== 'undefined') {
                animateDropdownMenuInGrids('case-docs-summary-dataGrid');
            }
        },
        editable: false
    });
}

// Action functions (can be defined after grid initialization)
function editDocument(id) {
    console.log("Edit document:", id);
    // Implement your edit functionality
}

function deleteDocument(id) {
    if (confirm("Are you sure you want to delete this document?")) {
        console.log("Delete document:", id);
        // Implement delete functionality
        jQuery.post(getBaseURL() + "cases/delete_document/" + id, function(response) {
            if (response.status) {
                jQuery("#case-docs-summary-dataGrid").data("kendoGrid").dataSource.read();
            }
        });
    }
}

jQuery(document).ready(function() {
  
    var caseId = jQuery('#case-docs-summary-dataGrid').data('field');
    initializeCaseDocs(caseId);
});