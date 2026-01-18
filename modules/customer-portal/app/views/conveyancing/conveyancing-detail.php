
    <style>
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-in-progress {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-completed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-delayed {
            background-color: #f8d7da;
            color: #721c24;
        }
        .timeline {
            position: relative;
            padding-left: 50px;
        }
        .timeline:before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: #dee2e6;
        }
        .timeline-item {
            position: relative;
            margin-bottom: 20px;
        }
        .timeline-item:before {
            content: '';
            position: absolute;
            left: -38px;
            top: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: #6c757d;
            border: 3px solid #fff;
        }
        .timeline-item.completed:before {
            background: #28a745;
        }
        .timeline-item.current:before {
            background: #007bff;
            animation: pulse 1.5s infinite;
        }
        .timeline-item.pending:before {
            background: #ffc107;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }
        .document-item {
            border-left: 3px solid #007bff;
            padding-left: 10px;
            margin-bottom: 10px;
        }
    </style>


<div class="container-fluid mt-4">


    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?php echo $title?></span>
                    <span class="status-badge status-<?php echo $instrument["status"]?>"><?php echo $instrument["status"]?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>CNV-<?php echo $instrument["id"]?></h5>
                            <p class="text-muted"><?php echo $instrument["instrument_type"]?></p>
                            <p class="text-muted"><?php echo $instrument["transaction_type_name"]?></p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Initiated:</strong> <?php echo $instrument["date_initiated"]?></p>
                            <p><strong>Last Updated:</strong>  <?php echo  $instrument["modifiedOn"]?></p>
                        </div>
                    </div>

                    <hr>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h6>Title</h6>
                            <p><?php echo $instrument["title"]?></p>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Property Value</h6>
                            <p><?php echo $instrument["property_value"]?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Amount Approved </h6>
                            <p><?php echo $instrument["amount_approved"]?></p>
                        </div>

                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Vendor/Seller</h6>
                            <p><?php echo $instrument["party_name"]?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Chargor/Authority</h6>
                            <p>Communications Authority</p>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Staff</h6>
                            <p><?php echo $instrument["staff"]?> (CA Staff)</p>
                        </div>
                        <div class="col-md-6">
                            <h6>External Counsel</h6>
                            <p><?php echo $instrument["external_counsel"]??"Not yet Nominated"?> </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <h6>Description</h6>
                            <p><?php echo $instrument["description"]?>.</p>
                        </div>
                    </div>
                </div>
            </div>



            <div class="card">
                <div class="card-header">
                    Activity Log
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>User</th>
                                <th>Action</th>
                                <th>Details</th>
                            </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="conveyancing-docs-container">
            <div class="card mb-4">
                <div class="card-header">
                    Documents
                </div>
                <div class="card-body">
                    <div id="conveyancing-docs">

                    </div>

                    <hr>
                    <div class="text-center ">
                        <button class="btn btn-sm btn-primary" id="<?php echo $instrument["id"];?>" onclick="uploadFileForm()">
                            <i class="fas fa-upload"></i> Upload New Document
                        </button>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    Stakeholders
                </div>
                <div class="card-body">
                    <h6>Related Staff</h6>
                    <p><?php echo $instrument["staff"];?><br>
                       </p>

                    <h6>HRA</h6>
                    <p><?php echo $instrument["creator_name"];?><br>
                     </p>

                    <h6>LS</h6>
                    <p><?php echo $instrument["assignee"]??"Not Yet Assigned";?><br>
                       </p>

                    <h6>External Counsel</h6>
                    <p><?php echo $instrument["external_counsel"]??"Not Nominated Yet";?><br>
                       </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Add Note or Update
                </div>
                <div class="card-body">
                    <form>
                        <div class="form-group">
                            <label for="updateType">Update Type</label>
                            <select class="form-control" id="updateType">
                                <option>Note</option>
                                <option>Status Change</option>
                                <option>Document Request</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="updateDetails">Details</label>
                            <textarea class="form-control" id="updateDetails" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <label for="updateFile">Attach File (Optional)</label>
                            <div class="custom-file">
                                <input type="file" name="uploadDoc" id="uploadDoc" value="" class="margin-top-5" />
                                <label class="custom-file-label" for="uploadDoc">Choose file...</label>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Submit Update</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    // Update file input label
    jQuery('.custom-file-input').on('change', function() {
        let fileName = jQuery(this).val().split('\\').pop();
        jQuery(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose file...');
    });
    //get conveyancingId
    let conveyancingId =<?php echo $instrument["id"];?>;
    loadDocuments();

    ///function to load documents from conveyancing/load_documents
    function loadDocuments() {
        if (!conveyancingId) {
            console.warn("conveyancingId is not set. Cannot load documents.");
            jQuery('#conveyancing-docs').html('<p class="text-danger">Error: Instrument ID is missing. Cannot load documents.</p>');
            return;
        }

        jQuery.ajax({
            url: getBaseURL('customer-portal')+'conveyancing/load_documents',
            type: 'POST',
            dataType: 'json', // Assuming your endpoint returns JSON
            data: {
                module: "conveyancing",
                module_record_id: conveyancingId,
            },
            beforeSend: function() {
                jQuery('#conveyancing-docs').html('<p class="text-center text-muted"><i class="fas fa-spinner fa-spin"></i> Loading documents...</p>');
            },
            success: function(response) {
                if (response.commentHtml) {
                    jQuery('#conveyancing-docs').html(response.commentHtml);
                } else {
                    // If response is not successful or no HTML is returned
                    jQuery('#conveyancing-docs').html('<p class="text-muted">No documents found for this item.</p>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading documents:', error);
                // Display an error message if the AJAX call fails
                jQuery('#conveyancing-docs').html('<p class="text-danger">Failed to load documents. Please try again.</p>');
            }
        });
    }
</script>