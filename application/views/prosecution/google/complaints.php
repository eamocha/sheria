<div class="">
    <h2>Case Details</h2>
    <p class="text-muted">Manage case details, accused persons, investigation logs, court proceedings, arrests, and attachments.</p>
    <hr> 
    <?php 
    $legal_case_id=$legalCase["id"];
     if(!empty($closure_recommendation) && !empty($closure_recommendation["id"])){?>   
        <!-- alert with button to allow approve /close the file at this phase with reasons. alighn to the right of the alert -->
    <div class="alert alert-info row" role="alert">
        <div class="col-md-8">
            <p class="date-recommended">Investigating officer: <?php echo $closure_recommendation["recommendation_status"]?></p> 
            <?php echo $closure_recommendation["investigation_officer_recommendation"];?>
                                        <p class="muted-text"><?php echo $closure_recommendation["date_recommended"];?> By <?php echo $closure_recommendation["createdByName"]?></p>
        </div>
    <div class="col-md-4 text-right">
     <a class="btn btn-sm btn-primary " id="submitCaseAction" href="javascript:void(0);" onclick="">Take Action</a>
    </div>
    </div>
    <?php }?>

    <div class="card">
        <div class="card-header btn-primary text-white">
            Case Summary
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Subject:</strong> <span id=""> <?php echo $legalCase["subject"];?></span></p>
                     <p><strong>Case Number:</strong> <span id="caseNumber"></span></p>
                    <p><strong>Origin of Matter:</strong> <span id="originOfMatter"></span></p>
                    <p><strong>Date recorded:</strong> <span id="dateOfComplaint"></span></p>
                    <p><strong>Investigating Officer:</strong> <span id="investigatingOfficer"></span> &nbsp;&nbsp;&nbsp; <a href="javascript:void(0);" onclick="loadOfficers()" id="assignOfficerBtn">Assign Officer</a></p>
                    <p><strong>Brief of the Case:</strong> <span id="briefOfCase"></span></p>
                    <p><strong>Status of the Case:</strong> <span id=""><?php echo $criminalCaseDetails["status_of_case"]??"-"?></span></p>
                    <p>
                        <strong>Complaint/detection form:</strong>
                        <span id="">
                            <?php if (!empty($criminalCaseDetails['initial_entry_document_id'])){ ?>
                                <a href="<?php echo base_url('cases/download_file/' . $criminalCaseDetails['initial_entry_document_id']); ?>" target="_blank" class="btn btn-link btn-sm">
                                    Download
                                </a>
                            <?php } else{ ?>
                                <span class="text-muted">No file uploaded</span>
                            <?php } ?>
                        </span>
                        &nbsp;&nbsp;&nbsp;
                        <a href="javascript:void(0);" onclick="uploadFileForm('complaintForm')">Attach</a>
                    </p>
                </div>
                <div class="col-md-6">
                    <p><strong>Category of offence:</strong> <span id="natureOfCase"></span></p>
                      <p><strong>Type of offence:</strong><span id="typeOfOffence"></span></span></p>
                      <p><strong>Police Station Reported:</strong> <span id="policeStationReported"></span></p>
                    <p><strong>Police Station OB Number:</strong> <span id="policeStationObNumber"></span></p>
                    <p><strong>Police Case File Number:</strong> <span id="policeCaseFileNumber"></span></p>
                  

                    <p><strong>Date Approved for Enforcement:</strong> <span id="approvalEnforcementDate"></span></p>
                    <p><strong>Signed Authorization:</strong> <span id="authorizationAttachment">
                         <?php if (!empty($criminalCaseDetails['authorization_document_id'])): ?>
                                <a href="<?php echo base_url('cases/download_file/' . $criminalCaseDetails['authorization_document_id']); ?>" target="_blank" class="btn btn-link btn-sm">
                                    Download
                                </a>
                            <?php else: ?>
                                <span class="text-muted">No file uploaded</span>
                            <?php endif; ?>
                    </span> &nbsp;&nbsp;&nbsp;<a href="javascript:void(0);" onclick="uploadFileForm('authorizationForm')">Attach here</a></p>
                    <!-- add action to be taken on the file. either close  or send for approval or recommend further investigation   -->
                    <div class="form-group"> 
                      <?php if(empty($closure_recommendation) && empty($closure_recommendation["id"]))
                      {?>
                         <button readOnly="true" class="btn btn-sm btn-primary" id="recommendActionBtn">Recommend Action to be taken</button>
                        <?php
                      }?>
                    </div>
                </div>

        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
          Republic Versus
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="accusedTable">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Contact Type</th>
                    <th>Address</th>
                    <th>Comments</th>
                    <th>Statement Taken</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php

                if (empty($relatedOpponentData)) { ?>
                    <tr><td colspan="6" class="text-center">No accused added yet.</td></tr>
                <?php } else {
                    foreach ($relatedOpponentData as $opponent) {
                        $link_segment = ($opponent['opponent_member_type'] === 'company') ? 'companies/tab_company/' : 'contacts/tab_contact/';
                        ?>
                        <tr>
                            <td>
                                <a href='<?php echo base_url($link_segment) . $opponent['opponent_member_id']; ?>'>
                                    <?php echo $opponent['opponentName']; ?>
                                </a>
                            </td>
                            <td><?php echo ucfirst($opponent['opponent_member_type']); ?></td>
                            <td> -</td> <td>-</td> <td>-</td> <td>
                                <button class="btn btn-sm btn-primary edit-accused"
                                        data-id="<?php echo $opponent['opponent_id']; ?>">Edit</button>
                                <button class="btn btn-sm btn-danger delete-accused"
                                        data-id="<?php echo $opponent['opponent_id']; ?>">Delete</button>
                            </td>
                        </tr>
                        <?php
                    }
                } ?>
                </tbody>
            </table>
            <a class="btn btn-sm btn-primary mt-2"
               onclick="open_party_form(<?php echo $legal_case_id ?>)"
               title="Add New Party">
                <i class="fas fa-plus-square me-1"></i> Add Party
            </a>

        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Investigation Log
        </div>
        <div class="card-body">
            <?php if (!empty($investigation_log)) { ?>    
                 <table class="table table-stripped table-bordered" id="">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                
                    <th>Activity</th>
                    <th>Details</th>
                    <th>Investigating Officer</th>
                    <th>Attachments</th>
                    <th>Actions</th>
                </tr>
                </thead>
               <tbody>
                
                    <?php foreach ($investigation_log as $index => $log): ?>
          <tr>
            <td><?= $index + 1 ?></td>
            <td><?= htmlspecialchars($log["log_date"]) ?></td>
            <td><?= ucwords(str_replace("_", " ", $log["action_taken"])) ?></td>
            <td><?= nl2br(htmlspecialchars($log["details"])) ?></td>
            <td><?= htmlspecialchars(trim($log["creator_name"])) ?></td>
            <td> <?php if (!empty($log['doc_id'])): ?>
               <a class="btn btn-sm btn-success" href="<?= base_url('cases/download_file/' . $log['doc_id']) ?>" target="_blank">Download</a>
             <?php endif; ?>
            </td>
           <td>
             <button class="btn btn-sm btn-primary edit-log-entry" data-id="<?= $log["id"] ?>">Edit</button>
             <button class="btn btn-sm btn-danger delete-log-entry" data-id="<?= $log["id"] ?>">Delete</button>
            
           </td>
          </tr>
                  <?php endforeach; ?>
        </tbody>
               </table>
                <?php } else { ?>
                    <div class="text-center">No investigation logs available.</div>
                <?php } ?>
                
            <button class="btn btn-sm btn-primary mt-2" id="addLogEntry">Add Log Entry</button>
        </div>
    </div>
    

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Arrests
        </div>
        <div class="card-body">
            <table class="table table-bordered" id="arrestsTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                    <th>Police station</th>
                    <th>OB Number</th>
                    <th>Case File Number</th>
                    <th>Attachments</th>
                     <th>Remarks</th>
                    <th>Options</th>
                </tr>
                </thead>
                <tbody id="arrestsTableBody">
                <?php if (!empty($arrestDetails) && is_array($arrestDetails)): ?>
                    <?php foreach ($arrestDetails as $arrest): ?>
                        <tr>
                            <td><?= htmlspecialchars($arrest['arrest_date'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($arrest['arrested_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($arrest['arrest_police_station'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($arrest['arrest_ob_number'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($arrest['arrest_case_file_number'] ?? '-') ?></td>
                            <td>
                                <?php if (!empty($arrest['arrest_attachments'])): ?>
                                    <a href="<?= base_url('cases/download_file/' . $arrest['attachment_id']) ?>" target="_blank" class="btn btn-link btn-sm">Download</a>
                                <?php else: ?>
                                    <span class="text-muted">No file</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($arrest['arrest_remarks'] ?? '-') ?></td>
                            <td>
                                <a class="btn btn-sm btn-primary edit-arrest" data-id="<?= $arrest['id'] ?>" onclick="showRecordArrestModal(<?=$legal_case_id?>,<?= $arrest['id'] ?>, 'edit')">Edit</a>
                                <a class="btn btn-sm btn-danger delete-arrest" data-id="<?= $arrest['id'] ?>">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No arrests recorded yet.</td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
            <button class="btn btn-sm btn-primary mt-2" id="recordArrest" onclick="showRecordArrestModal(<?php echo $legal_case_id?>)">Record an arrest</button>
        </div>
    </div>

    <div class="card mt-4 d-none">
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
            <button class="btn btn-sm btn-primary mt-2" id="addProceeding">Add Proceeding</button>
        </div>
    </div>

    <div class="card mt-4 d-none">
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
            <button class="btn btn-sm btn-primary mt-2" id="addWitness">Add Witness</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Exhibit Details
        </div>
        <div class="card-body">
          <!-- This section can be used to display exhibit details if needed -->
           
            <table class="table table-bordered table-striped">
  <thead class="">
    <tr>
      <th>#</th>
      <th>Label</th>
      <th>Description</th>
      <th>Date Received</th>
      <th>Manner of Disposal</th>
      <th>Temporary Removals</th>
      <th>Created By</th>
      <th>Created On</th>
    </tr>
  </thead>
  <tbody id="exhibitTableBody">
    <!-- Data will be inserted here dynamically -->
  </tbody>
 </table>
            <button class="btn btn-sm btn-primary mt-2" id="addExhibit">Add Exhibit</button>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header btn-primary text-white">
            Documents/Attachments
        </div>
        <div class="card-body">
            <ul class="list-group" id="attachmentList">
            </ul>
            <a href="javascript:void(0);" class="btn btn-sm btn-primary mt-2" onclick="uploadFileForm()">Upload</a>
        </div>
    </div>
    <script>
    // Load documents for this case and populate the attachment list
    function loadDocuments() {
        var caseId = <?php echo (int)$legalCase["id"]; ?>;
        jQuery.ajax({
            url: getBaseURL() + 'cases/load_documents',
            type: 'POST',
            data: {
                module: 'case',
                module_record_id: caseId,
                lineage: '',
                term: '',
                 type: 'file'
            },
            dataType: 'json',
            beforeSend: function () {
                jQuery('#loader-global').show();
            },
            complete: function () {
                jQuery('#loader-global').hide();
            },
            success: function(response) {
                if (response.status && Array.isArray(response.data)) {
                    var $list = jQuery('#attachmentList');
                    $list.empty();
                    if (response.data.length === 0) {
                        $list.append('<li class="list-group-item text-muted">No documents found.</li>');
                    } else {
                        response.data.forEach(function(doc) {
                            var fileUrl = getBaseURL() + 'cases/download_file/' + doc.id;
                          
                            var fileName = doc.full_name || doc.name;
                            var creator = doc.display_creator_full_name ? ' by ' + doc.display_creator_full_name : '';
                            var createdOn = doc.display_created_on ? ' (' + doc.display_created_on + ')' : '';
                            $list.append(
                                '<li class="list-group-item d-flex justify-content-between align-items-center">' +
                                '<a href="' + fileUrl + '" target="_blank">' + fileName + '</a>' +
                                '<span class="text-muted small ml-2">' + creator + createdOn + '</span>' +
                                '<button class="btn btn-sm btn-danger delete-attachment" data-id="' + doc.id + '">Delete</button>' +
                                '</li>'
                            );
                        });
                    }
                } else {
                    jQuery('#attachmentList').html('<li class="list-group-item text-danger">Failed to load documents.</li>');
                }
            },
            error: function() {
                jQuery('#attachmentList').html('<li class="list-group-item text-danger">Error loading documents.</li>');
            }
        });
    }

    // Call loadDocuments on page ready
    jQuery(function() {
        loadDocuments();
    });

    // Optionally, reload after upload or delete
    jQuery(document).on('click', '.delete-attachment', function() {
        var docId = jQuery(this).data('id');
        if (confirm('Are you sure you want to delete this document?')) {
            jQuery.ajax({
                url: getBaseURL() + 'cases/delete_document',
                type: 'POST',
                data: { id: docId },
                dataType: 'json',
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                complete: function () {
                    jQuery('#loader-global').hide();
                },
                success: function(response) {
                    if (response.status) {
                        loadDocuments();
                        pinesMessage({ ty: 'success', m: response.message || 'Document deleted.' });
                    } else {
                        pinesMessageV2({ ty: 'error', m: response.message || 'Failed to delete document.' });
                    }
                },
                error: function() {
                    pinesMessageV2({ ty: 'error', m: 'Error deleting document.' });
                }
            });
        }
    });
    </script>
    <?php // $this->load->view("prosecution/forms/accused_form",$legalCase["id"])?>
    <?php $this->load->view("prosecution/forms/investigation_log_form",$legalCase["id"])?>

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
    <?php $this->load->view("prosecution/forms/exhibit_form",$legalCase["id"])?>
    <!-- modal to submitCaseAction. Should have the box for remarks and 3 buttons to either approve the case, recommend further investigation or close the case -->
        <?php $this->load->view("prosecution/forms/case_action_form",$legalCase["id"])?>

    <!-- witness modal -->
        <?php $this->load->view("prosecution/forms/witness_form",$legalCase["id"])?>

</div>

 <script>
  var  caseid=<?php echo $legalCase["id"]?>;
  var case_id=caseid;
  var assignee_id=<?php echo json_encode($legalCase["user_id"])?>;
    jQuery(document).ready(function() 
    {
     
                jQuery('.datepicker').datepicker('destroy').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                    todayHighlight: true,
                    language: 'en'
                });


        // Load initial data (replace '123' with the actual case ID from your route)
        loadCaseDetails(caseid);

        // --- Modal Interactions (Example - Adapt to your backend logic) ---


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
            jQuery('#witnessModalLabel').text('Add Action Taken');
            jQuery('#witnessForm')[0].reset(); // Clear form
            jQuery('#witnessId').val(''); // Clear ID for adding
            jQuery('#witnessModal').modal('show');
        });

        //add exhibit modal
        jQuery('#addExhibit').click(function () {
            jQuery('#exhibitModalLabel').text('Add Exhibit');
            jQuery('#exhibitForm')[0].reset(); // Clear form
            jQuery('#exhibitModal').modal('show');
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

        //function to take action on a recommendation of file closure

        jQuery('#recommendActionBtn').on('click', function() {
            jQuery('#caseActionModal').modal('show');
            jQuery(".recommendation").show();
            jQuery(".approval").hide();
            jQuery('#caseActionModalLabel-header').text('Recommend Case For Further Actions');
        });

   

        // Show modal when Take Action button is clicked
jQuery('#submitCaseAction').on('click', function() {
    jQuery('#caseActionModal').modal('show');
    jQuery(".recommendation").hide();
    jQuery(".approval").show();
    
    jQuery('#caseActionModalLabel-header').text('Take Action on Case');
});

       
    
    });
     // Function to load case details (Replace with your CodeIgniter/AJAX)
        function loadCaseDetails(caseId) {
         
            const data = {
                caseNumber: "M<?php echo $legalCase["id"]?>",
                dateOfComplaint:  "<?php echo $legalCase["caseArrivalDate"]?>", 
                investigatingOfficer: <?php echo json_encode($legalCase["Assignee"]); ?>,
                natureOfCase: <?php echo json_encode($legalCase["practice_area"]); ?>,
                typeOfOffence:<?php echo json_encode($criminalCaseDetails['offence_subcategory_name']??"")?>,
                approvalEnforcementDate: <?php echo json_encode($criminalCaseDetails['date_investigation_authorized']??"")?>,
                 originOfMatter:  <?php echo json_encode($criminalCaseDetails['origin_of_case']??"")?>,
                briefOfCase:  <?php echo json_encode($legalCase["description"])?>,
                assignOfficerBtn: assignee_id&&assignee_id>0 ?"Reassign Officer":"Assign Officer",
                policeStationReported: <?php echo json_encode($criminalCaseDetails['police_station_reported'] ?? ''); ?>,
                policeStationObNumber: <?php echo json_encode($criminalCaseDetails['police_station_ob_number'] ?? ''); ?>,
                policeCaseFileNumber: <?php echo json_encode($criminalCaseDetails['police_case_file_number'] ?? ''); ?>,

             

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
            jQuery('#typeOfOffence').text(data.typeOfOffence);
            jQuery('#approvalEnforcementDate').text(data.approvalEnforcementDate);
            jQuery('#briefOfCase').text(data.briefOfCase);
             jQuery('#assignOfficerBtn').text(data.assignOfficerBtn);
            jQuery('#policeStationReported').text(data.policeStationReported);
            jQuery('#policeStationObNumber').text(data.policeStationObNumber);
            jQuery('#policeCaseFileNumber').text(data.policeCaseFileNumber);
           

          //  populateAccusedTable(<?php echo json_encode($clientData)?>);
          
           
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
            <td>${item.type}</td>
            <td>
              <button class="btn btn-sm btn-primary edit-accused" data-id="${item.id}">Edit</button>
              <button class="btn btn-sm btn-danger delete-accused" data-id="${item.id}">Delete</button>
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

     // Fetch Officers for Dropdowns (Example - Adapt to your backend)
      
  function loadOfficers() {
            jQuery.ajax({
               url: getBaseURL()+'cases/assignOfficers/'+case_id, // Replace with your actual URL
               method: 'GET',
               dataType: 'json',
               beforeSend: function () {
                jQuery('#loader-global').show();
               },
               complete: function () {
                jQuery('#loader-global').hide();
               },
               success: function(response) {
                if (response.html) {
                if (jQuery('#assignment-dialog').length <= 0) {
                    jQuery('<div id="assignment-dialog"></div>').appendTo("body");
                }
                var assignmentDialog = jQuery('#assignment-dialog');
                assignmentDialog.html(response.html);
                // Show the modal after injecting the HTML
                if (jQuery('#assignOfficerModal').length) {
                    jQuery('#assignOfficerModal').modal('show');
                }
                // Attach submit trigger for the officer assignment form
                assignmentDialog.off('submit', 'form').on('submit', 'form', submitAssignOfficerForm);

            } else if (typeof response.error !== 'undefined' && response.error) {
                pinesMessageV2({ ty: 'error', m: response.error });
            }
               
               }
             });
            
        }
    function submitAssignOfficerForm(){
            event.preventDefault();
            
            var form = jQuery(this);
            var formData = form.serialize();
            jQuery.ajax({
                url: getBaseURL()+'cases/assignOfficers/'+case_id,
                type: "POST",
                data: formData,
                dataType: 'json',
                beforeSend: function () {
                    jQuery('#loader-global').show();
                },
                success: function (response) {
                    if (response.success) {
                        pinesMessage({ ty: 'success', m: response.message });
                        if (jQuery('#assignOfficerModal').length) {
                            jQuery('#assignOfficerModal').modal('hide');
                        }
                        //  update the investigatingOfficer id with the new name
                        var selectedOfficerName = form.find('select[name="user_id"] option:selected').text();
                        jQuery('#investigatingOfficer').text(selectedOfficerName || '');
                        
                    } else {
                        pinesMessageV2({ ty: 'error', m: response.message });
                    }
                },
                complete: function () {
                    jQuery('#loader-global').hide();
                },
                error: defaultAjaxJSONErrorsHandler
            });
            return false;
        }
    
    //function to upload a file. send an ajax request to the server to load the upload file form
function uploadFileForm(what) 
{
    jQuery.ajax({
        dataType: 'JSON',
         type: 'GET',
        url: getBaseURL() + 'cases/attach_file/'+ <?php echo $legalCase['id']?>+'/'+what,
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        success: function (response) {
            if (response.result && response.html) {
                if (jQuery(".upload-file-container").length <= 0) {
                    jQuery('<div class="d-none upload-file-container"></div>').appendTo("body");
                    var uploadFileContainer = jQuery('.upload-file-container');
                    uploadFileContainer.html(response.html).removeClass('d-none');
                    jQuery('.select-picker', uploadFileContainer).selectpicker();
                    var lineage = 0;//jQuery('#lineage', documentsForm).val();
                    jQuery('#lineage', uploadFileContainer).val(lineage);
                    commonModalDialogEvents(uploadFileContainer);
                    jQuery("#form-submit", uploadFileContainer).click(function () {
                        uploadFileFormSubmit(uploadFileContainer);
                    });
                    jQuery(uploadFileContainer).find('input').keypress(function (e) {
                        // Enter pressed?
                        if (e.which == 13) {
                            e.preventDefault();
                            uploadFileFormSubmit(uploadFileContainer);
                        }
                    });
                      jQuery('.modal', uploadFileContainer).modal('show');
                }
            }

        }, complete: function () {
            jQuery('#loader-global').hide();
        },
        error: defaultAjaxJSONErrorsHandler
     });
}

////submit
function uploadFileFormSubmit(container) {
    var formData = new FormData(document.getElementById(jQuery("form", container).attr('id')));
    jQuery.ajax({
        dataType: 'JSON',
        data: formData,
        type: 'POST',
        url: getBaseURL(module) + 'cases/attach_file',
        contentType: false, // required to be disabled
        cache: false,
        processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', container).attr('disabled', 'disabled');
        },
        success: function (response) {
            jQuery('.inline-error', container).addClass('d-none');
            if (response.status) {
                ty = 'success';
                jQuery('.modal', container).modal('hide');
               // resetPagination($documentsGrid);
                //showHideEmptyGridMessage("hide");
                jQuery('#related-documents-count', '.conveyancing-container').text(response.related_documents_count);
            } else {
                ty = 'error';
                if ('undefined' !== typeof response.validation_errors) {
                    displayValidationErrors(response.validation_errors, container);
                }

            }
            if ('undefined' !== typeof response.message) {
                pinesMessage({ty: ty, m: response.message});
            }
        }, complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}
       

function submitCaseAction(statusBtn,actionType) {
    if(!validateRemarks()){
        return;
    }
    var remarks = jQuery('#caseActionRemarks').val();
    var caseId = caseid;
    var caseActionId=jQuery('#caseActionId').val();
  
    jQuery.ajax({
        beforeSend: function () {
           jQuery('#loader-global').show();
        },
        data: {remarks: remarks, caseId: caseId, status:statusBtn, actionType:actionType,recommendation_id:caseActionId},
        dataType: 'JSON',
        type: 'POST',
        url: getBaseURL() + 'cases/case_file_closure_action',
        success: function (response) {
            if (response.result) {
                                
                pinesMessage({ ty: 'success', m: response.message});
                jQuery('#caseActionRemarks').val('');
                jQuery('#caseActionModal').modal('hide');
                if(response.moved_toPBC){
                    //redirect to cases/litigation/caseId
                    
                }
               
            } else {
                    pinesMessageV2({ ty: 'error', m: response.error });
            }
        }, complete: function () {
            jQuery('#loader-global').hide();           

        },
        error: defaultAjaxJSONErrorsHandler
    });
}

///validate 
function validateRemarks() {
    var remarks = jQuery('#caseActionRemarks').val().trim();
    if (!remarks) {
        jQuery('#caseActionRemarks').addClass('is-invalid');
        if (jQuery('#caseActionRemarks').next('.invalid-feedback').length === 0) {
            jQuery('#caseActionRemarks').after('<div class="invalid-feedback">Remarks are required.</div>');
        }
        jQuery('#caseActionRemarks').focus();
        return false;
    } else {
        jQuery('#caseActionRemarks').removeClass('is-invalid');
        jQuery('#caseActionRemarks').next('.invalid-feedback').remove();
        return true;
    }
}
///arrests 
// Load the arrest form modal via AJAX from the controller and show it
function showRecordArrestModal(caseId,record_id,mode="add") {
    jQuery.ajax({
        url: getBaseURL()  + 'cases/arrests', // Adjust base_url as needed
        type: 'GET',
        data: { case_id: caseId,record_id:record_id,mode:mode }, // Pass case_id if needed
        beforeSend: function () {
            jQuery('#loader-global').show();
        },
        complete: function () {
            jQuery('#loader-global').hide();
        },
        success: function(response) {
            if (response.html) {
            // Remove any existing modal to avoid duplicates
            jQuery('#recordArrestModal').remove();

            if (jQuery('#recordArrest-dialog').length <= 0) {
                    jQuery('<div id="recordArrest-dialog"></div>').appendTo("body");
                }
                var recordArrestDialog = jQuery('#recordArrest-dialog');
                recordArrestDialog.html(response.html);
                // Show the modal after injecting the HTML
                if (jQuery('#recordArrestModal').length) {
                    jQuery('#recordArrestModal').modal('show');
                }
                  //  setDatePicker('#arrest_date', "#recordArrest-dialog");
                     setDatePicker('#arrest-date-add-new', recordArrestDialog);
                // Attach submit trigger for  form
                recordArrestDialog.off('submit', 'form').on('submit', 'form', submitArrestRecord);
           
            
            } else if (typeof response.error !== 'undefined' && response.message) {
            pinesMessageV2({ ty: 'error', m: response.message });
            }
        },
        error: defaultAjaxJSONErrorsHandler
    });
}

///function to handle submitArrestRecord
function submitArrestRecord() {
var container = jQuery("#recordArrestModal");
    event.preventDefault();

    var form = jQuery(this);
    var formData =jQuery('#recordArrestForm', container).serialize();
    jQuery.ajax({
        url: getBaseURL() + 'cases/arrests', 
        type: 'POST',
        data: formData,
        dataType: 'json',
       // contentType: false,
       // cache: false,
       // processData: false,
        beforeSend: function () {
            jQuery('#loader-global').show();
            jQuery('.modal-save-btn', form).attr('disabled', 'disabled');
        },
        success: function (response) {
              jQuery('.inline-error', container).addClass('d-none');
            if (response.success) {
                pinesMessage({ ty: 'success', m: response.message });
                jQuery('#recordArrestModal').modal('hide');
                // Optionally reload arrests table or update UI

            } else {
               
                if (response.validationErrors) {
                    displayValidationErrors(response.validationErrors, container);
                }else{
                    pinesMessageV2({ ty: 'error', m: response.error || 'Failed to save arrest record.' });

                }
            }
        },
        complete: function () {
            jQuery('#loader-global').hide();
            jQuery('.modal-save-btn', container).removeAttr('disabled');
        },
        error: defaultAjaxJSONErrorsHandler
    });
}




</script>