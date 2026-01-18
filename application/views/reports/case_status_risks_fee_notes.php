<style>
    .wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
    }
    .k-grid table {
        table-layout: auto;
    }

    .k-grid td {
        white-space: normal;
        vertical-align: top;
    }

    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        cursor: help;
    }

    .tooltip-inner {
        max-width: 400px;
        white-space: pre-wrap;
    }

    .export-buttons {
        margin-bottom: 15px;
    }

    .search-box {
        margin-bottom: 15px;
        max-width: 300px;
    }

         /* Ensure column headers don't wrap */
     .k-grid-header .k-link {
         white-space: nowrap !important;
         overflow: hidden !important;
         text-overflow: ellipsis !important;
         padding: 8px 12px !important;
     }

    .k-grid-header th {
        white-space: nowrap !important;
        overflow: hidden !important;
    }

    /* Better grid container for horizontal scrolling */
    #grid-container {
        overflow-x: auto;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: white;
    }

    /* Ensure grid takes full width */
    #grid {
        min-width: 2000px; /* Minimum width to accommodate all columns */
    }

    /* Text truncation for cells */
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    /* Column resize handle */
    .k-grid .k-resize-handle {
        background-color: #007bff;
    }
    .k-grid td {
        white-space: normal !important;
        word-wrap: break-word !important;
        vertical-align: top;
    }
    .k-grid td.wrap-text {
        white-space: normal !important;
        word-wrap: break-word !important;
        word-break: break-word !important;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo site_url("reports"); ?>"><?php echo $this->lang->line("reports");?></a></li>
                <li class="breadcrumb-item active"><?php echo $this->lang->line("case_status_with_fee_notes"); ?></li>
            </ul>

            <!-- Control Buttons -->
            <div class="row mb-3">

                <div class="col-md-6">
                    <div class="search-box">
                        <div class="input-group">
                            <input type="text" id="globalSearch" class="form-control" placeholder="Search all columns...">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="clearSearch">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="btn-group export-buttons">
                        <button id="exportExcel" class="btn btn-success btn-sm">
                            <i class="fa fa-file-excel-o"></i> Export to Excel
                        </button>

                    </div>
                </div>
            </div>

            <!-- Grid Container -->
            <div id="grid-container" style="overflow-x: auto;">
                <div id="grid"></div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function() {
        // Show loader when starting
        showLoader(true);

        // Define truncateText function in global scope first
        window.truncateText = function(text, maxLength) {
            if (!text) return '';
            if (text.length <= maxLength) return text;

            var truncated = text.substring(0, maxLength) + '...';
            return '<span class="text-truncate" data-fulltext="' + text.replace(/"/g, '&quot;') + '">' + truncated + '</span>';
        };

        var allColumns = [
            {
                field: "caseID",
                title: "Case ID",
                width: 100,
                template: '<a href="' + getBaseURL() + 'cases/edit/#= id #" class="text-primary font-weight-bold">#= caseID #</a>'
            },
            {
                field: "subject",
                title: "Subject",
                width: 200,
                template: '<a href="' + getBaseURL() + 'cases/edit/#= id #" class="text-primary">#= subject #</a>'
            },

            {
                field: "description",
                title: "Description",
                width: 400,
                attributes: { "class": "wrap-text" }
            },
            { field: "status", title: "Status", width: 120 },

            { field: "statusComments", title: "Status Comments", width: 200 },
            { field: "outsourcedCompanies", title: "External Counsel", width: 200 },
            { field: "arrivalDate", title: "Filed On", format: "{0:MM/dd/yyyy}", width: 120 },
            { field: "next_actions", title: "Next Actions", width: 200, template: "#= window.truncateText(next_actions, 60) #", hidden: true },

            { field: "internalReference", title: "Internal Ref.", width: 150 },
            { field: "assignee", title: "Assignee", width: 150 },
            { field: "caseValue", title: "Value sued", format: "{0:c}", width: 120 },

            { field: "totalBill", title: "Counsel Fee", format: "{0:c}", width: 120 },
            { field: "totalPaymentsMade", title: "Paid", format: "{0:c}", width: 120 },
            { field: "totalBalanceDue", title: "Balance Due", format: "{0:c}", width: 120 },
            { field: "externalizeLawyers", title: "External Lawyers", width: 200, hidden: true },
            { field: "estimatedEffort", title: "Est. Effort", width: 120, hidden: true },
            { field: "dueDate", title: "Due Date", format: "{0:MM/dd/yyyy}", width: 120, hidden: true },
            { field: "priority", title: "Priority", width: 100 },
            { field: "category", title: "Category", width: 150, hidden: true },
            { field: "createdOn", title: "Created On", format: "{0:MM/dd/yyyy}", width: 120, hidden: true },
            { field: "modifiedOn", title: "Modified On", format: "{0:MM/dd/yyyy}", width: 120, hidden: true },
            { field: "type", title: "Type", width: 120, hidden: true },
            { field: "providerGroup", title: "Provider Group", width: 150, hidden: true },

            { field: "contact", title: "Contact", width: 200, hidden: true },
            { field: "role", title: "Role", width: 120, hidden: true },
            { field: "sentenceDate", title: "Sentence Date", format: "{0:MM/dd/yyyy}", width: 120, hidden: true },
            { field: "related_risks", title: "Related Risks", width: 400, template: "#= window.truncateText(related_risks, 100) #", hidden: true },

            { field: "contactContributor", title: "Contributors", width: 200, hidden: true },
            { field: "contactOutsourceTo", title: "Outsourced To (Contact)", width: 200, hidden: true },
            { field: "companyOutsourceTo", title: "Outsourced To (Company)", width: 200, hidden: true },
            {
                field: "archived",
                title: "Archived",
                width: 100,
                template: "#= archived ? '<span class=\"badge badge-warning\">Yes</span>' : '<span class=\"badge badge-success\">No</span>' #"
            }
        ];

        // Main columns (visible by default)
        var mainColumns = allColumns.map(function(col) {
            var mainFields = ['caseID', 'subject','description','outsourcedCompanies', 'status', 'arrivalDate', 'caseValue', 'totalBalanceDue', 'assignee', 'archived','totalBill','totalPaymentsMade'];
            if (mainFields.includes(col.field)) {
                return Object.assign({}, col, { hidden: false });
            }
            return Object.assign({}, col, { hidden: true });
        });

        var grid = jQuery("#grid").kendoGrid({
            dataSource: {
                transport: {
                    read: {
                        url: getBaseURL() + "reports/get_case_status_risks_fee_notes",
                        dataType: "json",
                        type: "GET",
                        complete: function() {
                            showLoader(false);
                        },
                        error: function() {
                            showLoader(false);
                            alert('Error loading data. Please try again.');
                        }
                    }
                },
                pageSize: 20,
                serverPaging: false,
                serverSorting: false,
                serverFiltering: false,
                schema: {
                    model: {
                        fields: {
                            id: { type: "number" },
                            subject: { type: "string" },
                            description: { type: "string" },
                            priority: { type: "string" },
                            arrivalDate: { type: "date" },
                            dueDate: { type: "date" },
                            statusComments: { type: "string" },
                            outsourcedCompanies: { type: "string" },
                            category: { type: "string" },
                            next_actions: { type: "string" },
                            caseValue: { type: "number" },
                            internalReference: { type: "string" },
                            externalizeLawyers: { type: "string" },
                            estimatedEffort: { type: "number" },
                            createdOn: { type: "date" },
                            modifiedOn: { type: "date" },
                            archived: { type: "boolean" },
                            caseID: { type: "string" },
                            status: { type: "string" },
                            type: { type: "string" },
                            providerGroup: { type: "string" },
                            assignee: { type: "string" },
                            contact: { type: "string" },
                            role: { type: "string" },
                            sentenceDate: { type: "date" },
                            related_risks: { type: "string" },
                            totalBill: { type: "number" },
                            totalPaymentsMade: { type: "number" },
                            totalBalanceDue: { type: "number" },
                            contactContributor: { type: "string" },
                            contactOutsourceTo: { type: "string" },
                            companyOutsourceTo: { type: "string" }
                        }
                    }
                }
            },
            height: 550,
            scrollable: true,
            pageable: {
                pageSizes: [10, 20, 50, 100],
                refresh: true,
                messages: {
                    display: "Showing {0}-{1} of {2} records",
                    itemsPerPage: "items per page"
                },
            },
            sortable: true,
            filterable: true,
            resizable: true,
            reorderable: true, // Movable columns
            columnMenu: true,
            columns: mainColumns,
            dataBound: function(e) {
                showLoader(false);

                jQuery('.k-grid-content td.wrap-text').css({
                    'white-space': 'normal',
                    'word-wrap': 'break-word',
                    'word-break': 'break-word'
                });

                jQuery('.k-grid-content .text-truncate').each(function() {
                    var $el = jQuery(this);
                    var fullText = $el.data('fulltext');
                    if (fullText && fullText.length > $el.text().length) {
                        $el.attr('title', fullText);
                    }
                });
            },
            dataBinding: function(e) {
                showLoader(true);
            }
        }).data("kendoGrid");

        jQuery('#globalSearch').on('keyup', function(e) {
            var searchValue = jQuery(this).val();

            if (!searchValue) {
                grid.dataSource.filter({});
                return;
            }

            if (searchValue.length < 2) return;

            showLoader(true);

            // Get the model fields to check types
            var modelFields = grid.dataSource.options.schema.model.fields;
            var filters = [];

            // Only search string fields
            for (var fieldName in modelFields) {
                if (modelFields.hasOwnProperty(fieldName)) {
                    var fieldConfig = modelFields[fieldName];
                    // Only search string fields, skip dates and numbers
                    if (fieldConfig.type === 'string') {
                        filters.push({
                            field: fieldName,
                            operator: "contains",
                            value: searchValue
                        });
                    }
                }
            }

            if (filters.length > 0) {
                grid.dataSource.filter({
                    logic: "or",
                    filters: filters
                });
            } else {
                grid.dataSource.filter({});
            }

            setTimeout(function() {
                showLoader(false);
            }, 500);
        });

        jQuery('#clearSearch').on('click', function() {
            showLoader(true);
            jQuery('#globalSearch').val('');
            grid.dataSource.filter({});
            setTimeout(function() {
                showLoader(false);
            }, 300);
        });

        // Toggle columns functionality
        var showAllColumns = false;
        jQuery('#toggleColumns').on('click', function() {
            showAllColumns = !showAllColumns;
            var $button = jQuery(this);

            showLoader(true);

            if (showAllColumns) {
                var visibleColumns = allColumns.map(function(col) {
                    return Object.assign({}, col, { hidden: false });
                });
                grid.setOptions({ columns: visibleColumns });
                $button.html('<i class="fa fa-columns"></i> Show Main Columns');
            } else {
                grid.setOptions({ columns: mainColumns });
                $button.html('<i class="fa fa-columns"></i> Show All Columns');
            }

            setTimeout(function() {
                showLoader(false);
            }, 500);
        });

        // Export to Excel functionality
        jQuery('#exportExcel').on('click', function() {
            showLoader(true);
            setTimeout(function() {
                exportToCSV();
                showLoader(false);
            }, 500);
        });

        function exportToCSV() {
            var data = grid.dataSource.data();
            var csvContent = "data:text/csv;charset=utf-8,";

            // Headers
            var headers = [];
            var visibleColumns = grid.columns.filter(function(col) {
                return !col.hidden;
            });

            visibleColumns.forEach(function(col) {
                headers.push('"' + col.title + '"');
            });
            csvContent += headers.join(",") + "\r\n";

            // Data rows
            data.forEach(function(item) {
                var row = [];
                visibleColumns.forEach(function(col) {
                    var value = item[col.field];

                    // Handle different data types
                    if (col.format && col.format.includes('date') && value) {
                        value = kendo.toString(new Date(value), "MM/dd/yyyy");
                    } else if (col.format && col.format.includes('c') && value) {
                        value = '$' + parseFloat(value).toFixed(2);
                    } else if (col.field === 'archived') {
                        value = value ? 'Yes' : 'No';
                    } else if (typeof value === 'string') {
                        // Remove HTML tags for CSV
                        value = value.replace(/<[^>]*>/g, '');
                        value = value.replace(/"/g, '""');
                    }

                    row.push('"' + (value || '') + '"');
                });
                csvContent += row.join(",") + "\r\n";
            });

            // Download
            var encodedUri = encodeURI(csvContent);
            var link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", "cases_report_" + new Date().toISOString().split('T')[0] + ".csv");
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Initialize tooltips
        jQuery(function() {
            jQuery('body').tooltip({
                selector: '.text-truncate',
                container: 'body'
            });
        });

        // Safety timeout
        setTimeout(function() {
            showLoader(false);
        }, 10000);
    });
</script>