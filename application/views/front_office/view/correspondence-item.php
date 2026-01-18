<style>
    /* Allow dropdowns to show outside the grid cell */
.k-grid-content, .k-grid-content-locked {
    overflow: visible !important;
}

.k-grid td {
    overflow: visible !important;
    white-space: nowrap;
}
</style>
<div class="container-fluid mt-4">
    <input type="hidden" id="item-id" value="<?php echo $correspondence["id"] ?>">
      <input type="hidden" id="correspondence_type_id" value="<?php echo $correspondence["correspondence_type_id"]?>">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Correspondence Item Details</span>
                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $correspondence["status_name"])) ?>">
                        <?php echo $correspondence["status_name"] ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>
                                <?php echo $correspondence["reference_number"] ?>
                            </h5>
                            <p class="text-muted">
                                <?php echo $correspondence["correspondence_type_name"] ?>
                            </p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Date Received:</strong>
                                <?php echo date("Y-m-d",strtotime($correspondence["date_received"])) ?>
                            </p>
                            <p><strong>Document Date:</strong>
                                <?php echo date("Y-m-d",strtotime( $correspondence["document_date"])) ?>
                            </p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Source/Sender</strong></h6>
                            <p>
                                <?php echo $correspondence["sender_name"] ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Receipt Mode</strong></h6>
                            <p>
                                <?php echo $correspondence["mode_of_receipt"] ?>
                            </p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Recipient/Addressee</strong></h6>
                            <p>
                                <?php echo $correspondence["recipient_name"] ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Dispatch Mode</strong></h6>
                            <p>
                                <?php echo $correspondence["mode_of_dispatch"] ?>
                            </p>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6><strong>Requires Signature</strong></h6>
                            <p>
                                <?php echo $correspondence["requires_signature"] ?>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6><strong>Due Date</strong></h6>
                            <p>
                                <?php echo date("Y-m-d",strtotime( $correspondence["due_date"])) ?>
                            </p>
                        </div>

                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6><strong>Subject</strong></h6>
                            <p>
                                <?php echo $correspondence["subject"] ?>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <h6><strong>Details</strong></h6>
                            <p>
                                <?php echo $correspondence["body"] ?>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <h6><strong>Remarks</strong></h6>
                            <p>
                                <?php echo $correspondence["comments"] ?>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <h6><strong>File Name</strong></h6>
                            <p>
                                <?php echo $correspondence["filename"] ?>
                            </p>
                        </div>
                        <div class="col-md-12">
                            <h6><strong>Action Required</strong></h6>
                            <p>
                                <?php echo $correspondence["action_required"] ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Workflow Process</span>
                    <button class="btn btn-sm btn-primary" onclick="update_workflow_timeline('general')" data-toggle="modal" data-target="#updateProgressModal">
                        Update Progress 
                    </button>
                </div>
                <div class="card-body">
                    <div class="timeline" id="processTimeline">
                        <!-- Timeline steps will be loaded here via JS -->
                    </div>
                </div>
            </div>

           

            <div class="card">
                <div class="card-header">
                    Activity Log
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm" id="activity-log">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php //foreach ($activity_log as $log){ ?>
                                <tr>
                                    <td><?php //echo $log['createdOn']; ?></td>
                                    <td><?php //echo $log['createdBy']; ?></td>
                                    <td><?php //echo $log['action']; ?></td>
                                    <td><?php //echo $log['details']; ?></td>
                                </tr>
                            <?php //}; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="correspondence-docs-container">

            <div class="card mb-4">
                <div class="card-header">
                    Add Note or Update
                </div>
                <div class="card-body">
                    <form id="updateForm">
                        <div class="form-group">
                            <label for="updateType">Update Type</label>
                            <select class="form-control" id="updateType" onchange="showRelevantFields()">
                                <option value="note">Note</option>
                                <option value="status">Status Change</option>
                                <option value="reassign">Assign/Reassign</option>
                                <option value="document">Document Upload</option>
                                <option value="task">Task</option>
                            </select>
                        </div>

                        <div id="dynamicFields"></div>

                        <div class="form-group">
                            <label for="updateDetails">Details</label>
                            <textarea class="form-control" id="updateDetails" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input name="send_notifications_email" class="form-check-input" type="checkbox" id="send_notifications_email" checked>
                                <label class="form-check-label" for="send_notifications_email">
                                    Send Email Notification
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="fileUploadGroup">
                            <label for="updateFile">Attach File </label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" name="attachment" id="updateFile">
                                <label class="custom-file-label" for="updateFile">Choose file...</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Submit Update</button>
                    </form>
                </div>
            </div>

            <div id="taskPane" class="card mt-3" style="display: none;">
                <div class="card-header">
                    Create New Task
                </div>
                <div class="card-body">
                    <p><a href="javascript:;" onclick="taskAddForm();"> create a related task </a> </p>
                    <button class="btn btn-secondary" onclick="hideTaskPane()">Cancel</button>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-users"></i> Actors
                </div>
                <div class="card-body">
                    <h6><strong>Created By</strong></h6>
                    <p>
                        <?php echo $correspondence["createdBy"] ?><br>

                    </p>

                    <h6><strong>Assigned To</strong></h6>
                    <p>
                        <?php echo $correspondence["assignee_name"] ?><br>
                    </p>



                </div>
            </div>
             <div class="card mb-4">
                <div class="card-header">
                    <i class="sprite fas fa-file-invoice"></i>  Related Correspondences
                </div>
                <div class="card-body">
                    <div class="text-center ">
                        <button class="btn btn-sm btn-primary" id="<?php echo $correspondence["id"]; ?>" onclick="loadRelationshipForm(<?php echo $correspondence['id']; ?>)">
                            Link To Correspondence
                        </button>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="sprite sprite-task"></i>  Related Tasks
                </div>
                <div class="card-body">
                    <div class="text-center ">
                        <button class="btn btn-sm btn-primary" id="<?php echo $correspondence["id"]; ?>" onclick='taskForm(<?php echo $correspondence["id"]; ?>)'>
                            Add Task
                        </button>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                 <i class="sprite sprite-reminder"></i>   Related Reminders
                </div>
                <div class="card-body">
                    <div class="text-center ">
                        <button class="btn btn-sm btn-primary" id="<?php echo $correspondence["id"]; ?>"
                                onclick='reminderForm(<?php echo $correspondence["id"]; ?>)'>
                           Add reminder
                        </button>
                    </div>
                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    <i class="sprite file fas fa-file" ></i> Related Documents
                </div>
                <div class="card-body ">
                    <div class="document-item">
                        
                    </div>
                    
                </div>
            </div>

        </div>
    </div>
</div>

<script>
   const correspondenceId = $('#item-id').val();
   const correspondenceTypeId = $('#correspondence_type_id').val();


           $(document).ready(function() {
                    load_workflow_steps()
                 
                     // Update file input label
    $('.custom-file-input').on('change', function () {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose file...');
    });
    });
    fetchAndDisplayCorrespondenceDocuments(correspondenceId);

    function fetchAndDisplayCorrespondenceDocuments(correspondenceId) {
    // Adjust selector to your actual container for documents
    const $container = $('#correspondence-docs-container .card:has(.document-item), #correspondence-docs-container .card:has(.document-list)');
    let $docList = $container.find('.document-list');
    if ($docList.length === 0) {
        $docList = $('<div class="document-list"></div>');
        $container.find('.card-body').prepend($docList);
    }
    $docList.empty();

    $.ajax({
        url: getBaseURL() + 'front_office/get_documents_per_correspondence_json/' + correspondenceId,
        method: 'GET',
        dataType: 'json',
        beforeSend: function () {
            $docList.html('<div class="text-center my-3"><span class="spinner-border spinner-border-sm"></span> Loading documents...</div>');
        },
        success: function (response) {
            $docList.empty();
            if (!response.documents || response.documents.length === 0) {
                $docList.html('<div class="text-muted text-center">No documents found for this correspondence.</div>');
                return;
            }
            response.documents.forEach(function(doc) {
                const docType = doc.document_type_name ? `<span class="badge badge-info mr-2">${doc.document_type_name}</span>` : '';
                const uploaded = doc.createdOn ? `<span class="small text-muted">Uploaded: ${doc.createdOn.split(' ')[0]} by ${doc.creator_name}</span>` : '';
                const comments = doc.comments ? `<div class="small text-muted mt-1">${doc.comments}</div>` : '';
                //const downloadUrl = getBaseURL() + 'front_office/download_file/' + doc.correspondence_id + '/' + doc.id+ '/' + doc.name;
                const downloadUrl = getBaseURL() + 'front_office/download_file/' + doc.id;//               const downloadUrl = getBaseURL() + 'front_office/download_file/' + doc.correspondence_id + '/' + doc.id + '/' + encodeURIComponent(doc.name);
                const viewUrl = 'javascript:preview_attachment(' + doc.id + ', "correspondences", ' + doc.correspondence_id + ', "\\\\", "' + doc.extension + '");';

                $docList.append(`
                    <div class="document-item mb-3 p-2 border rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1"><strong>${doc.name}</strong> ${docType}</h6>
                                ${uploaded}
                                ${comments}
                            </div>
                            <div>
                                <a href="${downloadUrl}" class="btn btn-sm btn-outline-primary mr-1" target="_blank">
                                    <i class="fas fa-download"></i> Download
                                </a>
                               
                            </div>
                        </div>
                    </div>
                `);
            });
        },
        error: function () {
            $docList.html('<div class="text-danger text-center">Failed to load documents.</div>');
        }
    });
}

 

</script>




