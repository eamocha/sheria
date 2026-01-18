

<!--    <div class="mb-3 d-flex justify-content-end">-->
<!--        <!-- Actions Dropdown -->-->
<!--        <div class="dropdown">-->
<!--            <button class="btn btn-primary btn-sm dropdown-toggle rounded" type="button" id="actionsDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">-->
<!--                Actions-->
<!--            </button>-->
<!--            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="actionsDropdown">-->
<!--                <a class="dropdown-item" href="#" id="addNewExhibit">Add new exhibit</a>-->
<!--                <a class="dropdown-item" href="#" id="refreshExhibits">Refresh list</a>-->
<!--                <div class="dropdown-divider"></div>-->
<!--                <a class="dropdown-item" href="#" id="exportToExcel">Export to Excel</a>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->


        <div class="card-body">
            <!-- Filter Inputs Section -->
            <div class="filter-row row align-items-end">
                <div class="col-md-3 form-group">
                    <label for="filterCaseReference">Case reference</label>
                    <input type="text" class="form-control form-control-sm rounded" id="filterCaseReference" placeholder="Filter Case Ref">
                </div>
                <div class="col-md-3 form-group">
                    <label for="filterCourt">Court</label>
                    <input type="text" class="form-control form-control-sm rounded" id="filterCourt" placeholder="Filter Court">
                </div>
                <div class="col-md-3 form-group">
                    <label for="filterParties">Parties</label>
                    <input type="text" class="form-control form-control-sm rounded" id="filterParties" placeholder="Filter Parties">
                </div>
                <div class="col-md-3 form-group">
                    <label for="filterDescription">Description</label>
                    <input type="text" class="form-control form-control-sm rounded" id="filterDescription" placeholder="Filter Description">
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="filterDateReceived">Date received</label>
                    <input type="date" class="form-control form-control-sm rounded" id="filterDateReceived">
                </div>
                <div class="col-md-3 form-group mt-3">
                    <label for="filterDisposal">Manner of disposal</label>
                    <input type="text" class="form-control form-control-sm rounded" id="filterDisposal" placeholder="Filter Disposal">
                </div>
                <div class="col-md-3 form-group mt-3 d-flex align-items-end">
                    <button class="btn btn-primary btn-sm rounded mr-2" id="applyFilters">Apply filters</button>
                    <button class="btn btn-secondary btn-sm rounded" id="clearFilters">Clear filters</button>
                </div>
            </div>

            <!-- Kendo UI Grid Container -->
            <div id="exhibitGrid"></div>
        </div>



<!-- Exhibit Modal -->
<div class="modal fade" id="exhibitModal" tabindex="-1" role="dialog" aria-labelledby="exhibitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- Increased modal size -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exhibitModalLabel">Add exhibit</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exhibitForm">
                    <input type="hidden" id="exhibitId" name="exhibitId"> <!-- Hidden field for exhibit ID -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sno">S/no.</label>
                                <input type="text" class="form-control rounded" id="sno" name="sno" required>
                            </div>
                            <div class="form-group">
                                <label for="label_name">Label/name</label>
                                <input type="text" class="form-control rounded" id="label_name" name="label_name" required>
                            </div>
                            <div class="form-group">
                                <label for="status_on_pickup">Status on pickup</label>
                                <input type="text" class="form-control rounded" id="status_on_pickup" name="status_on_pickup">
                            </div>
                            <div class="form-group">
                                <label for="description_of_exhibit">Description of exhibit</label>
                                <textarea class="form-control rounded" id="description_of_exhibit" name="description_of_exhibit" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="date_received">Date received</label>
                                <input type="date" class="form-control rounded" id="date_received" name="date_received">
                            </div>
                            <div class="form-group">
                                <label for="temporary_removals">Temporary removals (reason and date)</label>
                                <input type="text" class="form-control rounded" id="temporary_removals" name="temporary_removals" placeholder="e.g., For analysis, 2024-07-01">
                            </div>
                            <div class="form-group">
                                <label for="pickup_location">Pickup location</label>
                                <input type="text" class="form-control rounded" id="pickup_location" name="pickup_location">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="manner_of_disposal">Manner of disposal</label>
                                <input type="text" class="form-control rounded" id="manner_of_disposal" name="manner_of_disposal" placeholder="e.g., Returned to owner, Destroyed">
                            </div>
                            <div class="form-group">
                                <label for="date_disposed">Date disposed</label>
                                <input type="date" class="form-control rounded" id="date_disposed" name="date_disposed">
                            </div>
                            <div class="form-group">
                                <label for="date_approved_for_disposal">Date approved for disposal</label>
                                <input type="date" class="form-control rounded" id="date_approved_for_disposal" name="date_approved_for_disposal">
                            </div>
                            <div class="form-group">
                                <label for="disposal_remarks">Disposal remarks</label>
                                <textarea class="form-control rounded" id="disposal_remarks" name="disposal_remarks" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="caseReference">Case reference (file no)</label>
                                <input type="text" class="form-control rounded" id="caseReference" name="caseReference">
                            </div>
                            <div class="form-group">
                                <label for="case_subject_name">Case subject</label>
                                <input type="text" class="form-control rounded" id="case_subject_name" name="case_subject_name">
                            </div>
                            <div class="form-group">
                                <label for="court_name">Court</label>
                                <input type="text" class="form-control rounded" id="court_name" name="court_name">
                            </div>
                            <div class="form-group">
                                <label for="opponents">Opponents</label>
                                <input type="text" class="form-control rounded" id="opponents" name="opponents">
                            </div>
                            <div class="form-group">
                                <label for="clients">Clients</label>
                                <input type="text" class="form-control rounded" id="clients" name="clients">
                            </div>
                            <div class="form-group">
                                <label for="current_location">Current location</label>
                                <input type="text" class="form-control rounded" id="current_location" name="current_location">
                            </div>
                            <div class="form-group">
                                <label for="officers_involved">Officers involved</label>
                                <input type="text" class="form-control rounded" id="officers_involved" name="officers_involved">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary rounded mt-3">Save exhibit</button>
                </form>
            </div>
        </div>
    </div>
</div>


<script>


    jQuery(document).ready(function() {
        // Kendo UI DataSource configuration
        let exhibitDataSource = new kendo.data.DataSource({
            transport: {
                read: {
                    url: getBaseURL() + 'exhibits/index', // Your CodeIgniter controller method
                    type: "POST",
                    dataType: "json",
                    beforeSend: function(xhr) {
                        jQuery('#loader-global').show(); // Show loader before request
                    },
                    complete: function(xhr, status) {
                        jQuery('#loader-global').hide(); // Hide loader after request completes
                    },
                    data: function() {
                        // This function prepares the data to be sent to the server for filtering, sorting, pagination
                        let kendoData = {};
                        let filters = [];

                        // Collect individual filter values from custom filter inputs
                        const caseRefFilter = jQuery('#filterCaseReference').val();
                        if (caseRefFilter) {
                            filters.push({ field: "caseReference", operator: "contains", value: caseRefFilter });
                        }
                        const courtFilter = jQuery('#filterCourt').val();
                        if (courtFilter) {
                            filters.push({ field: "court_name", operator: "contains", value: courtFilter });
                        }
                        const partiesFilter = jQuery('#filterParties').val();
                        if (partiesFilter) {
                            // For parties, filter on both opponents and clients
                            filters.push({
                                logic: "or",
                                filters: [
                                    { field: "opponents", operator: "contains", value: partiesFilter },
                                    { field: "clients", operator: "contains", value: partiesFilter }
                                ]
                            });
                        }
                        const descriptionFilter = jQuery('#filterDescription').val();
                        if (descriptionFilter) {
                            // For description, filter on both label_name and description_of_exhibit
                            filters.push({
                                logic: "or",
                                filters: [
                                    { field: "label_name", operator: "contains", value: descriptionFilter },
                                    { field: "description_of_exhibit", operator: "contains", value: descriptionFilter }
                                ]
                            });
                        }
                        const dateReceivedFilter = jQuery('#filterDateReceived').val();
                        if (dateReceivedFilter) {
                            filters.push({ field: "date_received", operator: "eq", value: dateReceivedFilter });
                        }
                        const disposalFilter = jQuery('#filterDisposal').val();
                        if (disposalFilter) {
                            filters.push({ field: "manner_of_disposal", operator: "contains", value: disposalFilter });
                        }

                        // Combine all filters with 'and' logic if multiple filters are present
                        if (filters.length > 0) {
                            kendoData.filter = { logic: "and", filters: filters };
                        }

                        // Kendo UI automatically adds sort, page, pageSize, etc. to 'd'
                        // We just ensure our custom filters are added.
                        return kendoData;
                    }
                }
            },
            schema: {
                data: "data",       // The array of data records
                total: "totalRows",  // The total number of records
                model: { // Define model fields for better data handling by Kendo UI
                    id: "sno", // Define the unique identifier for the model
                    fields: {
                        sno: { type: "string", editable: false, nullable: true }, // Not editable, can be null for new items
                        label_name: { type: "string" },
                        status_on_pickup: { type: "string", nullable: true },
                        description_of_exhibit: { type: "string", nullable: true },
                        date_received: { type: "date", nullable: true },
                        temporary_removals: { type: "string", nullable: true },
                        manner_of_disposal: { type: "string", nullable: true },
                        date_disposed: { type: "date", nullable: true },
                        caseReference: { type: "string", nullable: true },
                        case_id: { type: "string", nullable: true },
                        case_subject_name: { type: "string", nullable: true },
                        court_name: { type: "string", nullable: true },
                        opponents: { type: "string", nullable: true },
                        clients: { type: "string", nullable: true },
                        // New fields added to schema
                        current_location: { type: "string", nullable: true },
                        date_approved_for_disposal: { type: "date", nullable: true },
                        disposal_remarks: { type: "string", nullable: true },
                        officers_involved: { type: "string", nullable: true },
                        pickup_location: { type: "string", nullable: true }
                    }
                }
            },
            pageSize: 10,       // Number of items per page
            serverPaging: true, // Enable server-side paging
            serverSorting: true, // Enable server-side sorting
            serverFiltering: true, // Enable server-side filtering
            sort: { field: "sno", dir: "desc" } // Default sort
        });

        // Initialize Kendo UI Grid
        jQuery("#exhibitGrid").kendoGrid({
            dataSource: exhibitDataSource,
            height: 550, // Set a fixed height or adjust as needed
            scrollable: true,
            sortable: true,
            filterable: true, // Enable built-in per-column filtering (search box)
            reorderable: true, // Enable column reordering
            columnMenu: true, // Enable the column menu for hide/show options
            pageable: {
                refresh: true,
                pageSizes: [10, 25, 50, 100],
                buttonCount: 5
            },
            columns: [
                {
                    field: "sno",
                    title: "S/no.",
                    width: 90, // Increased width
                    template: function(dataItem) {
                        return '<a href="' + getBaseURL() + 'exhibits/view_details/' + (dataItem.sno || '-') + '">' + (dataItem.sno || '-') + '</a>';
                    }
                },
                {
                    field: "caseReference",
                    title: "Case reference (file no)",
                    width: 180, // Increased width, moved to second position
                    template: function(dataItem) {
                        if (dataItem.case_id) {
                            return '<a href="' + getBaseURL() + 'cases/edit/' + (dataItem.case_id || '-') + '">' + (dataItem.caseReference || '-') + '</a>';
                        }
                        return dataItem.caseReference || '- ';
                    }
                },
                {
                    field: "label_name",
                    title: "Label/name",
                    width: 150, // Increased width
                    template: function(dataItem) {
                        return '<a href="' + getBaseURL() + 'exhibits/view_details/' + (dataItem.sno || '-') + '">' + (dataItem.label_name || '-') + '</a>';
                    }
                },
                { field: "status_on_pickup", title: "Status on pickup", width: 150, template: "#: status_on_pickup || '-' #" }, // Increased width
                { field: "pickup_location", title: "Pickup location", width: 150, template: "#: pickup_location || '-' #" }, // New column
                {
                    field: "combined_description", // Dummy field for sorting/filtering if needed
                    title: "Identifying markings",
                    width: 300, // Increased width
                    template: function(dataItem) {
                        let description = dataItem.label_name || '';
                        if (dataItem.description_of_exhibit) {
                            description += (description ? ' - ' : '') + dataItem.description_of_exhibit;
                        }
                        return description || '- ';
                    },
                    sortable: false, // Sorting on combined field needs custom backend logic
                    filterable: false // Filtering on combined field needs custom backend logic
                },
                { field: "date_received", title: "Date received", width: 130, format: "{0:yyyy-MM-dd}", template: "#: kendo.toString(date_received, 'yyyy-MM-dd') || '-' #" }, // Increased width
                { field: "temporary_removals", title: "Temporary removals", width: 180, template: "#: temporary_removals || '-' #" }, // Increased width
                { field: "manner_of_disposal", title: "Manner of disposal", width: 180, template: "#: manner_of_disposal || '-' #" }, // Increased width
                { field: "date_disposed", title: "Date disposed", width: 130, format: "{0:yyyy-MM-dd}", template: "#: kendo.toString(date_disposed, 'yyyy-MM-dd') || '-' #" }, // New column (was in schema, now displayed)
                { field: "date_approved_for_disposal", title: "Date approved for disposal", width: 180, format: "{0:yyyy-MM-dd}", template: "#: kendo.toString(date_approved_for_disposal, 'yyyy-MM-dd') || '-' #" }, // New column
                { field: "disposal_remarks", title: "Disposal remarks", width: 200, template: "#: disposal_remarks || '-' #" }, // New column
                { field: "court_name", title: "Court", width: 150, template: "#: court_name || '-' #" },
                { field: "court_number", title: "Court number", width: 120, template: "#: court_number || '-' #" }, // Increased width. Note: This field is not in your JSON, will be empty.
                {
                    field: "combined_parties", // Dummy field for sorting/filtering if needed
                    title: "Parties",
                    width: 250, // Increased width
                    template: function(dataItem) {
                        let parties = [];
                        if (dataItem.opponents) {
                            parties.push(dataItem.opponents);
                        }
                        if (dataItem.clients) {
                            parties.push(dataItem.clients);
                        }
                        return parties.join(' vs. ') || '- ';
                    },
                    sortable: false, // Sorting on combined field needs custom backend logic
                    filterable: false // Filtering on combined field needs custom backend logic
                },
                {
                    field: "case_subject_name",
                    title: "Case subject",
                    width: 250, // Increased width
                    template: function(dataItem) {
                        if (dataItem.case_id) {
                            return '<a href="' + getBaseURL() + 'cases/edit/' + (dataItem.case_id || '-') + '">' + (dataItem.case_subject_name || '-') + '</a>';
                        }
                        return dataItem.case_subject_name || '- ';
                    }
                },
                { field: "current_location", title: "Current location", width: 150, template: "#: current_location || '-' #" }, // New column
                { field: "officers_involved", title: "Officers involved", width: 150, template: "#: officers_involved || '-' #" }, // New column
                {
                    title: "Actions",
                    width: 150,
                    template: function(dataItem) {
                        return `
                                <div class="dropdown">
                                    <button class="btn btn-info btn-sm dropdown-toggle rounded" type="button" id="dropdownMenuButton_${dataItem.sno}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Actions
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_${dataItem.sno}">
                                        <a class="dropdown-item" href="${getBaseURL()}exhibits/view_details/${dataItem.sno}">View</a>
                                        <a class="dropdown-item edit-item" href="javascript:void(0);" data-sno="${dataItem.sno}">Edit</a>
                                        <a class="dropdown-item delete-item" href="javascript:void(0);" data-sno="${dataItem.sno}">Delete</a>
                                    </div>
                                </div>
                            `;
                    }
                }
            ],
            // Excel Export configuration
            excel: {
                fileName: "Exhibits.xlsx",
                proxyURL: getBaseURL() + 'exhibits/excel_export_proxy', // Your backend proxy for saving the file
                filterable: true // Apply current grid filters to the exported data
            },
            // Enable striped rows
            // The templates below are corrected to use proper Kendo UI template syntax
            altRowTemplate: `
                    <tr data-uid="#= uid #" role="row" class="k-alt">
                        <td>#: sno || '-' #</td>
                        <td><a href="${getBaseURL()}cases/edit/#= case_id || '-' #">#: caseReference || '-' #</a></td>
                        <td><a href="${getBaseURL()}exhibits/view_details/#= sno || '-' #">#: label_name || '-' #</a></td>
                        <td>#: status_on_pickup || '-' #</td>
                        <td>#: pickup_location || '-' #</td>
                        <td>#: (label_name || '-') # - #: (description_of_exhibit || '-') #</td>
                        <td>#: kendo.toString(date_received, "yyyy-MM-dd") || '-' #</td>
                        <td>#: temporary_removals || '-' #</td>
                        <td>#: manner_of_disposal || '-' #</td>
                        <td>#: kendo.toString(date_disposed, "yyyy-MM-dd") || '-' #</td>
                        <td>#: kendo.toString(date_approved_for_disposal, "yyyy-MM-dd") || '-' #</td>
                        <td>#: disposal_remarks || '-' #</td>
                        <td>#: court_name || '-' #</td>
                        <td>#: court_number || '-' #</td>
                        <td>#: (opponents || '-') # vs. #: (clients || '-') #</td>
                        <td><a href="${getBaseURL()}cases/edit/#= case_id || '-' #">#: case_subject_name || '-' #</a></td>
                        <td>#: current_location || '-' #</td>
                        <td>#: officers_involved || '-' #</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-info btn-sm dropdown-toggle rounded" type="button" id="dropdownMenuButton_#: sno #" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_#: sno #">
                                    <a class="dropdown-item" href="${getBaseURL()}exhibits/view_details/#= sno || '-' #">View</a>
                                    <a class="dropdown-item edit-item" href="javascript:void(0);" data-sno="#= sno #">Edit</a>
                                    <a class="dropdown-item delete-item" href="javascript:void(0);" data-sno="#= sno #">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `,
            rowTemplate: `
                    <tr data-uid="#= uid #" role="row">
                        <td>#: sno || '-' #</td>
                        <td><a href="${getBaseURL()}cases/edit/#= case_id || '-' #">#: caseReference || '-' #</a></td>
                        <td><a href="${getBaseURL()}exhibits/view_details/#= sno || '-' #">#: label_name || '-' #</a></td>
                        <td>#: status_on_pickup || '-' #</td>
                        <td>#: pickup_location || '' #</td>
                        <td>#: (label_name || '-') # - #: (description_of_exhibit || '-') #</td>
                        <td>#: kendo.toString(date_received, "yyyy-MM-dd") || '-' #</td>
                        <td>#: temporary_removals || '-' #</td>
                        <td>#: manner_of_disposal || '-' #</td>
                        <td>#: kendo.toString(date_disposed, "yyyy-MM-dd") || '-' #</td>
                        <td>#: kendo.toString(date_approved_for_disposal, "yyyy-MM-dd") || '-' #</td>
                        <td>#: disposal_remarks || '-' #</td>
                        <td>#: court_name || '-' #</td>
                        <td>#: court_number || '-' #</td>
                        <td>#: (opponents || '-') # vs. #: (clients || '-') #</td>
                        <td><a href="${getBaseURL()}cases/edit/#= case_id || '-' #">#: case_subject_name || '-' #</a></td>
                        <td>#: current_location || '-' #</td>
                        <td>#: officers_involved || '-' #</td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-info btn-sm dropdown-toggle rounded" type="button" id="dropdownMenuButton_#: sno #" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Actions
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton_#: sno #">
                                    <a class="dropdown-item" href="${getBaseURL()}exhibits/view_details/#= sno || '-' #">View</a>
                                    <a class="dropdown-item edit-item" href="javascript:void(0);" data-sno="#= sno #">Edit</a>
                                    <a class="dropdown-item delete-item" href="javascript:void(0);" data-sno="#= sno #">Delete</a>
                                </div>
                            </div>
                        </td>
                    </tr>
                `
        });

        // Function to show details (example) - no longer directly called from grid, but kept for reference
        function showDetails(e) {
            e.preventDefault();
            let dataItem = this.dataItem(jQuery(e.currentTarget).closest("tr"));
            alert("Details for Exhibit S/No.: " + dataItem.sno + "\nCase: " + dataItem.caseReference + "\nSubject: " + dataItem.case_subject_name);
            // You can open a modal or navigate to a details page here
            // window.location.href = getBaseURL() + "exhibits/details/" + dataItem.sno;
        }

        // Function to populate modal for editing an exhibit
        function editExhibit(e) {
            e.preventDefault();
            // Get the dataItem associated with the clicked row
            // Kendo UI's command buttons pass the event object, from which we can get the dataItem
            let dataItem;
            if (e.data) { // If called from Kendo Grid command button
                dataItem = e.data;
            } else { // If called from a custom event listener (like the dropdown)
                const sno = jQuery(e.currentTarget).data('sno');
                dataItem = exhibitDataSource.get(sno); // Retrieve dataItem by ID
            }

            if (!dataItem) {
                console.error("Data item not found for editing.");
                return;
            }

            jQuery('#exhibitModalLabel').text('Edit exhibit');
            jQuery('#exhibitForm')[0].reset(); // Clear form fields

            // Populate form fields with dataItem values
            jQuery('#exhibitId').val(dataItem.sno); // Hidden ID for update
            jQuery('#sno').val(dataItem.sno);
            jQuery('#label_name').val(dataItem.label_name);
            jQuery('#status_on_pickup').val(dataItem.status_on_pickup);
            jQuery('#description_of_exhibit').val(dataItem.description_of_exhibit);

            // Format date fields for input type="date"
            if (dataItem.date_received) {
                jQuery('#date_received').val(kendo.toString(dataItem.date_received, "yyyy-MM-dd"));
            } else {
                jQuery('#date_received').val('');
            }

            jQuery('#temporary_removals').val(dataItem.temporary_removals);
            jQuery('#manner_of_disposal').val(dataItem.manner_of_disposal);

            if (dataItem.date_disposed) {
                jQuery('#date_disposed').val(kendo.toString(dataItem.date_disposed, "yyyy-MM-dd"));
            } else {
                jQuery('#date_disposed').val('');
            }

            if (dataItem.date_approved_for_disposal) {
                jQuery('#date_approved_for_disposal').val(kendo.toString(dataItem.date_approved_for_disposal, "yyyy-MM-dd"));
            } else {
                jQuery('#date_approved_for_disposal').val('');
            }

            jQuery('#disposal_remarks').val(dataItem.disposal_remarks);
            jQuery('#caseReference').val(dataItem.caseReference);
            // jQuery('#case_id').val(dataItem.case_id); // case_id is not directly in form
            jQuery('#case_subject_name').val(dataItem.case_subject_name);
            jQuery('#court_name').val(dataItem.court_name);
            // jQuery('#court_number').val(dataItem.court_number); // court_number not in JSON
            jQuery('#opponents').val(dataItem.opponents);
            jQuery('#clients').val(dataItem.clients);
            jQuery('#current_location').val(dataItem.current_location);
            jQuery('#officers_involved').val(dataItem.officers_involved);
            jQuery('#pickup_location').val(dataItem.pickup_location);


            jQuery('#exhibitModal').modal('show');
        }

        // Function to handle delete action
        function deleteExhibit(e) {
            e.preventDefault();
            const sno = jQuery(e.currentTarget).data('sno');
            if (confirm('Are you sure you want to delete exhibit S/No.: ' + sno + '?')) {
                // Implement AJAX call to delete the item from the backend
                console.log("Attempting to delete exhibit with S/No.:", sno);
                // Example AJAX call:
                /*
                jQuery.ajax({
                    url: getBaseURL() + 'exhibits/delete_exhibit/' + sno,
                    type: 'POST', // Or 'DELETE' if your API supports it
                    dataType: 'json',
                    beforeSend: function() { jQuery('#loader-global').show(); },
                    complete: function() { jQuery('#loader-global').hide(); },
                    success: function(response) {
                        if (response.status === 'success') {
                            alert('Exhibit deleted successfully!');
                            exhibitDataSource.read(); // Refresh grid
                        } else {
                            alert('Error deleting exhibit: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX error:", status, error);
                        alert('An error occurred while deleting the exhibit.');
                    }
                });
                */
                alert('Delete functionality needs backend implementation for S/No.: ' + sno);
                exhibitDataSource.read(); // Refresh grid for demonstration
            }
        }

        // Event listener for "Apply Filters" button
        jQuery('#applyFilters').on('click', function() {
            exhibitDataSource.read();
        });

        // Event listener for "Clear Filters" button
        jQuery('#clearFilters').on('click', function() {
            jQuery('#filterCaseReference').val('');
            jQuery('#filterCourt').val('');
            jQuery('#filterParties').val('');
            jQuery('#filterDescription').val('');
            jQuery('#filterDateReceived').val('');
            jQuery('#filterDisposal').val('');
            exhibitDataSource.filter({}); // Clear all filters in the DataSource
            exhibitDataSource.read(); // Re-read data
        });

        // Event listener for "Add New Exhibit" button (now in dropdown)
        jQuery('#addNewExhibit').on('click', function() {
            jQuery('#exhibitModalLabel').text('Add new exhibit');
            jQuery('#exhibitForm')[0].reset(); // Clear form fields
            jQuery('#exhibitId').val(''); // Clear hidden ID for new record (indicates new item)
            jQuery('#sno').val(''); // Clear sno for new record
            jQuery('#exhibitModal').modal('show');
        });

        // Event listener for "Refresh List" button (now in dropdown)
        jQuery('#refreshExhibits').on('click', function() {
            exhibitDataSource.read(); // Reload data
        });

        // Event listener for "Export to Excel" button (new)
        jQuery('#exportToExcel').on('click', function() {
            let grid = jQuery("#exhibitGrid").data("kendoGrid");
            if (grid) {
                grid.saveAsExcel(); // Trigger Excel export
            }
        });

        // Handle form submission for adding/editing exhibit
        jQuery('#exhibitForm').on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission

            const formData = jQuery(this).serializeArray(); // Use serializeArray to get an array of objects
            let exhibitData = {};
            jQuery(formData).each(function(index, obj){
                exhibitData[obj.name] = obj.value;
            });

            const exhibitId = jQuery('#exhibitId').val();
            let url = getBaseURL() + "exhibits/save_exhibit"; // Default for adding
            let method = "POST";

            if (exhibitId) {
                // If exhibitId exists, it's an update operation
                url = getBaseURL() + "exhibits/update_exhibit/" + exhibitId; // Example update URL
                method = "POST"; // Or PUT/PATCH if your API supports it
            }

            console.log("Form data submitted:", exhibitData);
            console.log("URL:", url);
            console.log("Method:", method);

            // Example AJAX call (uncomment and adapt for your save/update endpoint)
            /*
            jQuery.ajax({
                url: url,
                type: method,
                data: exhibitData, // Send as object, Kendo UI DataSource expects this structure
                dataType: "json",
                beforeSend: function() { jQuery('#loader-global').show(); },
                complete: function() { jQuery('#loader-global').hide(); },
                success: function(response) {
                    if (response.status === 'success') {
                        alert('Exhibit saved successfully!');
                        jQuery('#exhibitModal').modal('hide');
                        exhibitDataSource.read(); // Reload Kendo Grid data
                    } else {
                        alert('Error saving exhibit: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX error:", status, error);
                    alert('An error occurred while saving the exhibit.');
                }
            });
            */

            // For demonstration, just hide modal and refresh table
            alert('Exhibit form submitted. (Saving/Updating functionality needs backend implementation)');
            jQuery('#exhibitModal').modal('hide');
            exhibitDataSource.read(); // Reload data
        });

        // Delegate click events for edit and delete buttons within the Kendo Grid
        // This is necessary because the buttons are added dynamically by Kendo templates
        jQuery('#exhibitGrid').on('click', '.edit-item', function(e) {
            editExhibit(e); // Pass the event object to editExhibit
        });

        jQuery('#exhibitGrid').on('click', '.delete-item', function(e) {
            deleteExhibit(e); // Pass the event object to deleteExhibit
        });
    });
</script>
