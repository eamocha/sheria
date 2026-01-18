<div class="">
    <h2>Case Details</h2>



    <div class="card">
        <div class="card-header btn-primary text-white">
            Case Summary
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Case Number:</strong> <span id="caseNumber"></span></p>
                    <p><strong>Origin of Matter:</strong> <span id="originOfMatter"></span></p>
                    <p><strong>Date of Complaint:</strong> <span id="dateOfComplaint"></span></p>
                    <p><strong>Investigating Officer:</strong> <span id="investigatingOfficer"></span></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Nature of Case:</strong> <span id="natureOfCase"></span></p>
                    <p><strong>Approval of Enforcement Date:</strong> <span id="approvalEnforcementDate"></span></p>
                </div>
            </div>
            <p><strong>Brief of the Case:</strong> <span id="briefOfCase"></span></p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Accused Details
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="accusedTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <button class="btn btn-sm btn-success mt-2" id="addAccused">Add Accused</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Investigation Log
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="investigationLogTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Officer</th>
                    <th>Activity</th>
                    <th>Details</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <button class="btn btn-sm btn-success mt-2" id="addLogEntry">Add Log Entry</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Court Proceedings
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="courtProceedingsTable">
                <thead>
                <tr>
                    <th>Date of Hearing</th>
                    <th>Court</th>
                    <th>Case Reference (CF/PF)</th>
                    <th>Next Hearing Date</th>
                    <th>Status</th>
                    <th>Brief of Proceedings</th>
                    <th>Investigating Officer</th>
                    <th>Remarks</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <button class="btn btn-sm btn-success mt-2" id="addProceeding">Add Proceeding</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Witnesses
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="witnessTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="witnessTableBody">
                </tbody>
            </table>
            <button class="btn btn-sm btn-success mt-2" id="addWitness">Add Witness</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Exhibit Details
        </div>
        <div class="card-body">
            <a href="/exhibits?caseId=123" class="btn btn-info btn-sm">View Exhibits</a>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Documents/Attachments
        </div>
        <div class="card-body">
            <ul class="list-group" id="attachmentList">
            </ul>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="custom-file mt-2">
                    <input type="file" class="custom-file-input" id="newAttachments" multiple>
                    <label class="custom-file-label" for="newAttachments">Choose file</label>
                </div>
                <button type="submit" class="btn btn-sm btn-primary mt-2">Upload</button>
            </form>
        </div>
    </div>

    <div class="modal fade" id="accusedModal" tabindex="-1" role="dialog" aria-labelledby="accusedModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="accusedModalLabel">Add/Edit Accused</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="accusedForm">
                        <div class="form-group">
                            <label for="accusedName">Name</label>
                            <input type="text" class="form-control" id="accusedName" name="accusedName" required>
                        </div>
                        <div class="form-group">
                            <label for="accusedAddress">Address</label>
                            <input type="text" class="form-control" id="accusedAddress" name="accusedAddress" required>
                        </div>
                        <input type="hidden" id="accusedId" name="accusedId"> <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="logEntryModal" tabindex="-1" role="dialog" aria-labelledby="logEntryModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logEntryModalLabel">Add Investigation Log Entry</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="logEntryForm">
                        <div class="form-group">
                            <label for="logEntryDate">Date</label>
                            <input type="date" class="form-control" id="logEntryDate" name="logEntryDate" required>
                        </div>
                        <div class="form-group">
                            <label for="logEntryOfficer">Officer</label>
                            <select class="form-control" id="logEntryOfficer" name="logEntryOfficer" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="logEntryActivity">Activity</label>
                            <input type="text" class="form-control" id="logEntryActivity" name="logEntryActivity" required>
                        </div>
                        <div class="form-group">
                            <label for="logEntryDetails">Details</label>
                            <textarea class="form-control" id="logEntryDetails" name="logEntryDetails" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="proceedingModal" tabindex="-1" role="dialog" aria-labelledby="proceedingModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="proceedingModalLabel">Add Court Proceeding</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="proceedingForm">
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="proceedingDate">Date of Hearing</label>
                                <input type="date" class="form-control" id="proceedingDate" name="proceedingDate" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="proceedingCourt">Court</label>
                                <input type="text" class="form-control" id="proceedingCourt" name="proceedingCourt" required>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="proceedingReference">Case Reference (CF/PF)</label>
                                <input type="text" class="form-control" id="proceedingReference" name="proceedingReference">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-4">
                                <label for="proceedingNextDate">Next Hearing Date</label>
                                <input type="date" class="form-control" id="proceedingNextDate" name="proceedingNextDate">
                            </div>
                            <div class="form-group col-md-4">
                                <label for="proceedingStatus">Status</label>
                                <select class="form-control" id="proceedingStatus" name="proceedingStatus" required>
                                    <option value="">Choose...</option>
                                    <option value="PUI">PUI</option>
                                    <option value="PAKA">PAKA</option>
                                    <option value="Next Hg">Next Hg</option>
                                    <option value="Mn">Mn</option>
                                    <option value="Jdgmnt">Jdgmnt</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="proceedingOfficer">Investigating Officer</label>
                                <select class="form-control" id="proceedingOfficer" name="proceedingOfficer" required>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="proceedingBrief">Brief of Proceedings</label>
                            <textarea class="form-control" id="proceedingBrief" name="proceedingBrief" rows="3"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="proceedingRemarks">Remarks</label>
                            <textarea class="form-control" id="proceedingRemarks" name="proceedingRemarks" rows="2"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="witnessModal" tabindex="-1" role="dialog" aria-labelledby="witnessModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="witnessModalLabel">Add/Edit Witness</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="witnessForm">
                        <div class="form-group">
                            <label for="witnessName">Name</label>
                            <input type="text" class="form-control" id="witnessName" name="witnessName" required>
                        </div>
                        <div class="form-group">
                            <label for="witnessContact">Contact</label>
                            <input type="text" class="form-control" id="witnessContact" name="witnessContact">
                        </div>
                        <input type="hidden" id="witnessId" name="witnessId">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function() {
        // Function to load case details (Replace with your CodeIgniter/AJAX)
        function loadCaseDetails(caseId) {
            //jQuery.ajax({
            //   url: `/your-codeigniter-controller/cases/${caseId}`, // Adapt URL
            //   method: 'GET',
            //   dataType: 'json',
            //   success: function(data) {
            //    jQuery('#caseNumber').text(data.caseNumber);
            //    jQuery('#originOfMatter').text(data.originOfMatter);
            //    jQuery('#dateOfComplaint').text(data.dateOfComplaint);
            //    jQuery('#natureOfCase').text(data.natureOfCase);
            //    jQuery('#approvalEnforcementDate').text(data.approvalEnforcementDate);
            //    jQuery('#investigatingOfficer').text(data.investigatingOfficer);
            //    jQuery('#briefOfCase').text(data.briefOfCase);
            //     populateAccusedTable(data.accused);
            //     populateInvestigationLogTable(data.investigationLog);
            //     populateCourtProceedingsTable(data.courtProceedings);
            //     populateWitnessTable(data.witnesses); // Populate witnesses
            //     populateAttachmentList(data.attachments);
            //   }
            // });
            //Dummy data for testing
            const data = {
                caseNumber: "2024-001",
                originOfMatter: "Public",
                dateOfComplaint: "2024-03-10",
                investigatingOfficer: "Officer Jane Omolo",
                natureOfCase: "Frequency tamparing",
                approvalEnforcementDate: "2024-03-15",
                briefOfCase: "A brief description of the theft case",
                accused: [
                    { id: 101, name: "John Odinga", address: "123 Nairobi Street" },
                    { id: 102, name: "Alice wanjiko", address: "456 Ongata Rongai Ave" }
                ],
                investigationLog: [
                    { id: 201, date: "2024-03-16", officer: "Officer Martin", activity: "Interviewed witness", details: "Interviewed the main witness at the scene." },
                    { id: 202, date: "2024-03-17", officer: "Officer Wairimu", activity: "Collected evidence", details: "Collected fingerprints and other evidence." }
                ],
                courtProceedings: [
                    { id: 301, dateOfHearing: "2024-03-20", court: "City Court", caseReference: "CC-2024-123", nextHearingDate: "2024-04-05", status: "PUI", briefOfProceedings: "Initial hearing.", investigatingOfficer: "Officer Omolo", remarks: "Case is under investigation." }
                ],
                witnesses: [ // Sample witness data
                    { id: 501, name: "Witness One", contact: "0700111222" },
                    { id: 502, name: "Second Witness", contact: "second@example.com" }
                ],
                attachments: [
                    { id: 401, filename: "witness_statement.pdf", url: "/files/witness_statement.pdf" },
                    { id: 402, filename: "evidence_photos.zip", url: "/files/evidence_photos.zip" }
                ]
            };

            jQuery('#caseNumber').text(data.caseNumber);
            jQuery('#originOfMatter').text(data.originOfMatter);
            jQuery('#dateOfComplaint').text(data.dateOfComplaint);
            jQuery('#investigatingOfficer').text(data.investigatingOfficer);
            jQuery('#natureOfCase').text(data.natureOfCase);
            jQuery('#approvalEnforcementDate').text(data.approvalEnforcementDate);
            jQuery('#briefOfCase').text(data.briefOfCase);
            populateAccusedTable(data.accused);
            populateInvestigationLogTable(data.investigationLog);
            populateCourtProceedingsTable(data.courtProceedings);
            populateWitnessTable(data.witnesses); // Populate witnesses table
            populateAttachmentList(data.attachments);
        }

        // Populate Accused Table
        function populateAccusedTable(accused) {
            let tableBody = jQuery('#accusedTable tbody');
            tableBody.empty();
            jQuery.each(accused, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.name}</td>
            <td>${item.address}</td>
            <td>
              <button class="btn btn-sm btn-primary edit-accused" data-id="${item.id}">Edit</button>
              <button class="btn btn-sm btn-danger delete-accused" data-id="${item.id}">Delete</button>
            </td>
          </tr>
        `);
            });
        }

        // Populate Investigation Log Table
        function populateInvestigationLogTable(logEntries) {
            let tableBody = jQuery('#investigationLogTable tbody');
            tableBody.empty();
            jQuery.each(logEntries, function (index, item) {
                tableBody.append(`
          <tr>
            <td>${item.date}</td>
            <td>${item.officer}</td>
            <td>${item.activity}</td>
            <td>${item.details}</td>
            <td>
              <button class="btn btn-sm btn-primary edit-log-entry" data-id="${item.id}">Edit</button>
              <button class="btn btn-sm btn-danger delete-log-entry" data-id="${item.id}">Delete</button>
            </td>
                </tr>
            `);
            });
        }

        // Populate Court Proceedings Table
        function populateCourtProceedingsTable(proceedings) {
            let tableBody = jQuery('#courtProceedingsTable tbody');
            tableBody.empty();
            jQuery.each(proceedings, function (index, item) {
                tableBody.append(`
            <tr>
            <td>${item.dateOfHearing}</td>
            <td>${item.court}</td>
            <td>${item.caseReference}</td>
            <td>${item.nextHearingDate || '-'}</td>
            <td>${item.status}</td>
            <td>${item.briefOfProceedings || '-'}</td>
            <td>${item.investigatingOfficer}</td>
            <td>${item.remarks || '-'}</td>
            <td>
                <button class="btn btn-sm btn-primary edit-proceeding" data-id="${item.id}">Edit</button>
                <button class="btn btn-sm btn-danger delete-proceeding" data-id="${item.id}">Delete</button>
            </td>
        </tr>
            `);
            });
        }

        // Populate Witness Table
        function populateWitnessTable(witnesses) {
            let tableBody = jQuery('#witnessTableBody');
            tableBody.empty();
            jQuery.each(witnesses, function (index, item) {
                tableBody.append(`
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.contact || '-'}</td>
                        <td>
                            <button class="btn btn-sm btn-primary edit-witness" data-id="${item.id}">Edit</button>
                            <button class="btn btn-sm btn-danger delete-witness" data-id="${item.id}">Delete</button>
                        </td>
                    </tr>
                `);
            });
        }

        // Populate Attachments List
        function populateAttachmentList(attachments) {
            let listGroup = jQuery('#attachmentList');
            listGroup.empty();
            jQuery.each(attachments, function (index, item) {
                listGroup.append(`
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <a href="${item.url}" target="_blank">${item.filename}</a>
            <button class="btn btn-sm btn-danger delete-attachment" data-id="${item.id}">Delete</button>
        </li>
            `);
            });
        }

        // Load initial data (replace '123' with the actual case ID from your route)
        loadCaseDetails(123);

        // --- Modal Interactions (Example - Adapt to your backend logic) ---

        // Add Accused Modal
        jQuery('#addAccused').click(function () {
            jQuery('#accusedModalLabel').text('Add Accused');
            jQuery('#accusedId').val(''); // Clear ID for adding
            jQuery('#accusedName').val('');
            jQuery('#accusedAddress').val('');
            jQuery('#accusedModal').modal('show');
        });

        // Edit Accused (Example - Needs backend integration to fetch data)
        jQuery('#accusedTable').on('click', '.edit-accused', function () {
            const accusedId = jQuery(this).data('id');
            jQuery('#accusedModalLabel').text('Edit Accused');
            jQuery('#accusedId').val(accusedId);
            // AJAX call to fetch accused details and populate the modal form
            // For now, pre-fill with example data:
            jQuery('#accusedName').val('Sample Accused Name');
            jQuery('#accusedAddress').val('Sample Accused Address');
            jQuery('#accusedModal').modal('show');
        });

        jQuery('#accusedForm').submit(function (event) {
            event.preventDefault();
            // AJAX call to save/update accused data
            jQuery('#accusedModal').modal('hide');
            // Reload case details or update the accused table
        });

        // Add Log Entry Modal
        jQuery('#addLogEntry').click(function () {
            jQuery('#logEntryModalLabel').text('Add Investigation Log Entry');
            jQuery('#logEntryForm')[0].reset(); // Clear form
            // AJAX to fetch officers for the dropdown if not already loaded
            jQuery('#logEntryModal').modal('show');
        });

        jQuery('#logEntryForm').submit(function (event) {
            event.preventDefault();
            // AJAX call to save log entry data
            jQuery('#logEntryModal').modal('hide');
            // Reload case details or update the log table
        });

        // Add Proceeding Modal
        jQuery('#addProceeding').click(function () {
            jQuery('#proceedingModalLabel').text('Add Court Proceeding');
            jQuery('#proceedingForm')[0].reset(); // Clear form
            // AJAX to fetch officers for the dropdown if not already loaded
            jQuery('#proceedingModal').modal('show');
        });

        jQuery('#proceedingForm').submit(function (event) {
            event.preventDefault();
            // AJAX call to save proceeding data
            jQuery('#proceedingModal').modal('hide');
            // Reload case details or update the proceedings table
        });

        // Add Witness Modal
        jQuery('#addWitness').click(function () {
            jQuery('#witnessModalLabel').text('Add Witness');
            jQuery('#witnessForm')[0].reset(); // Clear form
            jQuery('#witnessId').val(''); // Clear ID for adding
            jQuery('#witnessModal').modal('show');
        });

        // Edit Witness (Example - Needs backend integration to fetch data)
        jQuery('#witnessTable').on('click', '.edit-witness', function () {
            const witnessId = jQuery(this).data('id');
            jQuery('#witnessModalLabel').text('Edit Witness');
            jQuery('#witnessId').val(witnessId);
            // AJAX call to fetch witness details and populate the modal form
            // For now, pre-fill with example data:
            const row = jQuery(this).closest('tr');
            const name = row.find('td:eq(0)').text();
            const contact = row.find('td:eq(1)').text();
            jQuery('#witnessName').val(name);
            jQuery('#witnessContact').val(contact);
            jQuery('#witnessModal').modal('show');
        });

        jQuery('#witnessForm').submit(function (event) {
            event.preventDefault();
            // AJAX call to save/update witness data
            jQuery('#witnessModal').modal('hide');
            // Reload case details or update the witness table
        });

        // Delete Accused (Example - Needs backend integration)
        jQuery('#accusedTable').on('click', '.delete-accused', function () {
            const accusedId = jQuery(this).data('id');
            if (confirm('Are you sure you want to delete this accused?')) {
                // AJAX call to delete accused
                // On success, reload case details or update the accused table
            }
        });

        // Delete Log Entry (Example - Needs backend integration)
        jQuery('#investigationLogTable').on('click', '.delete-log-entry', function () {
            const logId = jQuery(this).data('id');
            if (confirm('Are you sure you want to delete this log entry?')) {
                // AJAX call to delete log entry
                // On success, reload case details or update the log table
            }
        });

        // Delete Proceeding (Example - Needs backend integration)
        jQuery('#courtProceedingsTable').on('click', '.delete-proceeding', function () {
            const proceedingId = jQuery(this).data('id');
            if (confirm('Are you sure you want to delete this court activity?')) {
                // AJAX call to delete proceeding
                // On success, reload case details or update the proceedings table
            }
        });

        // Delete Witness (Example - Needs backend integration)
        jQuery('#witnessTable').on('click', '.delete-witness', function () {
            const witnessId = jQuery(this).data('id');
            if (confirm('Are you sure you want to delete this witness?')) {
                // AJAX call to delete witness
                // On success, reload case details or update the witness table
            }
        });

        // Delete Attachment (Example - Needs backend integration)
        jQuery('#attachmentList').on('click', '.delete-attachment', function () {
            const attachmentId = jQuery(this).data('id');
            if (confirm('Are you sure you want to delete this attachment?')) {
                // AJAX call to delete attachment
                // On success, reload the attachment list
            }
        });

        // Update file input label for new attachments
        jQuery('#newAttachments').on('change', function () {
            let fileNames = "";
            for (let i = 0; i < this.files.length; i++) {
                fileNames += this.files[i].name + ", ";
            }
            fileNames = fileNames.slice(0, -2);
            jQuery(this).next('.custom-file-label').html(fileNames || "Choose file");
        });

        // Handle new attachment upload (Example - Needs backend integration)
        jQuery('#uploadForm').submit(function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            // AJAX call to upload new attachments
            // On success, reload the attachment list
        });

        // Fetch Officers for Dropdowns (Example - Adapt to your backend)
        function loadOfficers() {
            //jQuery.ajax({
            //   url: '/your-codeigniter-controller/get-officers', // Replace with your actual URL
            //   method: 'GET',
            //   dataType: 'json',
            //   success: function(data) {
            //     let officerSelects =jQuery('select[id$="Officer"]'); // Select all officer dropdowns
            //     officerSelects.empty().append('<option value="">Choose...</option>');
            //    jQuery.each(data, function(key, value) {
            //       officerSelects.append('<option value="' + key + '">' + value + '</option>');
            //     });
            //   }
            // });
            // Dummy officer data for testing
            const officers = {
                1: "Officer John Omolo",
                2: "Officer Jane Kamau",
                3: "Officer David Lee"
            };
            let officerSelects = jQuery('select[id$="Officer"]'); // Select all officer dropdowns
            officerSelects.empty().append('<option value="">Choose...</option>');
            jQuery.each(officers, function (key, value) {
                officerSelects.append('<option value="' + key + '">' + value + '</option>');
            });
        }

        loadOfficers(); // Load officers on page load
    });

    // Function to populate the witness table
    function populateWitnessTable(witnesses) {
        let tableBody = jQuery('#witnessTableBody');
        tableBody.empty();
        witnesses.forEach(function (witness) {
            tableBody.append(`
                <tr>
                    <td>${witness.name}</td>
                    <td>${witness.contact || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-witness" data-id="${witness.id}">Edit</button>
                        <button class="btn btn-sm btn-danger delete-witness" data-id="${witness.id}">Delete</button>
                    </td>
                </tr>
            `);
        });
    }
</script>