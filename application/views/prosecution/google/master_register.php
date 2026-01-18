<div class="">
    <h2>Master Register</h2>



    <div class="card">
        <div class="card-header btn-primary text-white">
            Case List
        </div>
        <div class="card-body">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="filterStatus">Filter by Status:</label>
                    <select id="filterStatus" class="form-control form-control-sm">
                        <option value="">All</option>
                        <option value="Open">Open</option>
                        <option value="Pending Investigation">Pending Investigation</option>
                        <option value="Pending Court">Pending Court</option>
                        <option value="Closed">Closed</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filterOfficer">Filter by Officer:</label>
                    <select id="filterOfficer" class="form-control form-control-sm">
                        <option value="">All</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="filterYear">Filter by Financial Year:</label>
                    <select id="filterYear" class="form-control form-control-sm">
                        <option value="">All</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="filterOffence">Filter by Offence:</label>
                    <select id="filterOffence" class="form-control form-control-sm">
                        <option value="">All</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="searchInput">Search:</label>
                <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Enter case number, accused name, etc.">
            </div>

            <table id="caseTable" class="table table-striped table-bordered" style="width:100%">
                <thead>
                <tr>
                    <th>Financial Year</th>
                    <th>Case Number</th>
                    <th>Origin of Matter</th>
                    <th>Accused Name</th>
                    <th>Offence</th>
                    <th>Case Status</th>
                    <th>Investigating Officer</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
                <tfoot>
                <tr>
                    <th>Financial Year</th>
                    <th>Case Number</th>
                    <th>Origin of Matter</th>
                    <th>Accused Name</th>
                    <th>Offence</th>
                    <th>Case Status</th>
                    <th>Investigating Officer</th>
                    <th>Actions</th>
                </tr>
                </tfoot>
            </table>

            <div class="mt-3">
                <button class="btn btn-primary btn-sm" id="exportCsv">Export to CSV</button>
            </div>

        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // --- DataTables Initialization ---
        let caseTable = jQuery('#caseTable').DataTable({
            // DataTables options (see https://datatables.net/examples/)
            // Example options:
            // "paging": true,
            // "ordering": true,
            // "searching": true, // Enable search (we have a separate input too)
            "responsive": true,
            "processing": true, // Show 'Processing...'
            // "serverSide": true, // Enable server-side processing (recommended for large datasets)
            // "ajax": {
            //   "url": "/your-codeigniter-controller/get-cases", // Replace with your CI URL
            //   "type": "POST", // Or "GET"
            //   "data": function(d) {
            //     // Add filter data to the request
            //     d.status = jQuery('#filterStatus').val();
            //     d.officer = jQuery('#filterOfficer').val();
            //     d.year = jQuery('#filterYear').val();
            //     d.offence = jQuery('#filterOffence').val();
            //     d.search = jQuery('#searchInput').val(); // Add global search
            //   }
            // },
            "columns": [
                { "data": "financialYear" },
                { "data": "caseNumber" },
                { "data": "originOfMatter" },
                { "data": "accusedName" },
                { "data": "offence" },
                { "data": "caseStatus" },
                { "data": "investigatingOfficer" },
                {
                    "data": "caseNumber",
                    "render": function(data, type, row) {
                        return '<a href=<?php echo base_url("/cases/criminal_case_details/' + data + '")?> class="btn btn-sm btn-info">View</a>'; // Adapt URL
                    }
                }
            ],
            // Example dummy data
            "data": [
                { "financialYear": "2023", "caseNumber": "2023-001", "originOfMatter": "Public", "accusedName": "John Omolo", "offence": "Theft", "caseStatus": "Open", "investigatingOfficer": "Officer Martin Nyauma" },
                { "financialYear": "2023", "caseNumber": "2023-002", "originOfMatter": "Surveillance", "accusedName": "Jane Kamau", "offence": "Fraud", "caseStatus": "Pending Investigation", "investigatingOfficer": "Danielle Ramogo" },
                { "financialYear": "2024", "caseNumber": "2024-003", "originOfMatter": "Request", "accusedName": "Peter Jones", "offence": "Assault", "caseStatus": "Pending Court", "investigatingOfficer": "Officer C" },
                { "financialYear": "2024", "caseNumber": "2024-004", "originOfMatter": "Public", "accusedName": "Mary Wilson", "offence": "Trespassing", "caseStatus": "Closed", "investigatingOfficer": "Officer Martin Nyauma" },
                { "financialYear": "2024", "caseNumber": "2024-005", "originOfMatter": "Surveillance", "accusedName": "David Brown", "offence": "Robbery", "caseStatus": "Open", "investigatingOfficer": "Danielle Ramogo" }

            ]
        });

        // --- Filtering ---
        jQuery('#filterStatus, #filterOfficer, #filterYear, #filterOffence').on('change', function() {
            // caseTable.ajax.reload(); // If using server-side processing
            caseTable.draw(); // If using client-side processing (like the dummy data)
        });

        // --- Search ---
        jQuery('#searchInput').on('keyup', function() {
            caseTable.search(this.value).draw();
        });

        // --- Export to CSV (Example - Adapt to your server-side export) ---
        jQuery('#exportCsv').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
            // window.location.href = '/your-codeigniter-controller/export-cases'; // Example URL
        });

        // --- Create New Case (Example - Adapt to your navigation) ---
        jQuery('#createNewCase').on('click', function() {
            window.location.href = '/cases/create'; // Example URL
        });

        // --- Refresh Register (Example - Reload the page or data) ---
        jQuery('#refreshRegister').on('click', function() {
            // caseTable.ajax.reload(); // If using server-side processing
            // or
            window.location.reload(); // Simple page refresh
        });

        // --- Load Filter Options (Example - Adapt to your backend) ---
        function loadFilterOptions() {
            // jQuery.ajax({
            //   url: '/your-codeigniter-controller/get-filter-options', // Replace with your CI URL
            //   method: 'GET',
            //   dataType: 'json',
            //   success: function(data) {
            //     populateDropdown(jQuery('#filterOfficer'), data.officers);
            //     populateDropdown(jQuery('#filterYear'), data.years);
            //     populateDropdown(jQuery('#filterOffence'), data.offences);
            //   }
            // });
            // Dummy data for testing
            const filterData = {
                officers: ["Officer Martin Nyauma", "Danielle Ramogo", "Officer C", "Officer D"],
                years: ["2023", "2024", "2025"],
                offences: ["Theft", "Fraud", "Assault", "Trespassing", "Robbery"]
            };
            populateDropdown(jQuery('#filterOfficer'), filterData.officers);
            populateDropdown(jQuery('#filterYear'), filterData.years);
            populateDropdown(jQuery('#filterOffence'), filterData.offences);
        }

        function populateDropdown(selectElement, options) {
            jQuery.each(options, function(index, value) {
                selectElement.append('<option value="' + value + '">' + value + '</option>');
            });
        }

        loadFilterOptions();
    });
</script>