
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
        padding-left: 30px;
    }

    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 20px;
    }

    .timeline-item:last-child {
        border-left: 2px solid transparent;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -9px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #dee2e6;
    }

    .timeline-item.completed::before {
        background: #28a745;
        border: 2px solid #28a745;
    }

    .timeline-item.current::before {
        background: #007bff;
        border: 2px solid #007bff;
        animation: pulse 2s infinite;
    }

    .timeline-item.pending::before {
        background: #ffc107;
        border: 2px solid #fbc10b;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
    }

    .modal-content {
        border-radius: 0.5rem;
    }

    .modal-header {
        border-bottom: 1px solid #e9ecef;
        background-color: #f8f9fa;
        border-radius: 0.5rem 0.5rem 0 0;
    }

    .modal-title {
        font-weight: 600;
    }

    .document-item {
        border-left: 3px solid #007bff;
        padding-left: 10px;
        margin-bottom: 10px;
    }
</style>


<div class="container-fluid mt-4" >
   <input type="hidden" id="instrument-id" value="<?=$instrument["id"]?>">
    <div class="row">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><?php echo $title?></span>
                    <span class="status-badge status-<?php echo $instrument["status"]?>"><?php echo ucfirst($instrument["status"])?></span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>CNV-<?php echo $instrument["id"]?></h5>
                            <p class="text-muted"><?php echo $instrument["transaction_type_name"]?></p>
                        </div>
                        <div class="col-md-6 text-right">
                            <p><strong>Initiated:</strong> <?php echo $instrument["date_initiated"]?></p>
                            <p><strong>Last Updated:</strong>  <?php echo  date("Y-m-d",strtotime($instrument["modifiedOn"]))?></p>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Parties</h6>
                            <p><?php echo $instrument["parties"]?></p>
                        </div>
                        <div class="col-md-6">
                            <h6>Chargor/Authority</h6>
                            <p>Communications Authority</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6>Initiated By</h6>
                            <p><?php echo $instrument["staff"]?> (Staff)</p>
                        </div>
                        <div class="col-md-6">
                            <h6>External Counsel</h6>
                            <p id="external-counsel-name-container">
                                <?php
                                $id = $instrument["id"];
                                if ($instrument["external_counsel_id"]) {
                                    echo '<a href="' . base_url() ."companies/tab_company/".$instrument["external_counsel_id"]. '">' . htmlspecialchars($instrument["external_counsel_name"]) . '</a>';
                                } else {
                                    echo '<a href="javascript:void(0)" onclick="nominate_counsel(' . $id . ')">
                <i class="fas fa-user-tie" title="Nominate Legal Counsel"></i> Nominate Lawyer
              </a>';
                                }
                                ?>

                            </p>
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

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Process Timeline</span>
                    <button class="btn btn-sm btn-primary" onclick="updateProcessTimeline(<?php echo $instrument["id"]?>,'general')" data-target="#updateProgressModal">
                        Update Progress
                    </button>
                </div>
                <div class="card-body">
                    <div class="timeline" id="processTimeline">
                        <!-- Timeline items will be dynamically generated -->
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

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4" id="conveyancing-docs-container">

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
                                <option value="document">Document Request</option>
                                <option value="task">Task</option>
                            </select>
                        </div>

                        <!-- Dynamic Fields Container -->
                        <div id="dynamicFields"></div>

                        <div class="form-group">
                            <label for="updateDetails">Details</label>
                            <textarea class="form-control" id="updateDetails" rows="3" required></textarea>
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

            <!-- Task Pane (hidden by default) -->
            <div id="taskPane" class="card mt-3" style="display: none;">
                <div class="card-header">
                    Create New Task
                </div>
                <div class="card-body">
                    <!-- Task form would go here -->
                    <p><a href="javascript:;" onclick="taskAddForm();">  create a related task </a> </p>
                    <button class="btn btn-secondary" onclick="hideTaskPane()">Cancel</button>
                </div>
            </div>
            <!-- end the updating of action area -->
            <div class="card mb-4">
                <div class="card-header">
                    Stakeholders
                </div>
                <div class="card-body">
                    <h6>CA Staff</h6>
                    <p><?php echo $instrument["staff"]?><br>
                        <small class="text-muted">john.kamau@ca.go.ke</small></p>

                    <h6>HRA</h6>
                    <p><?php echo $instrument["staff"]?><br>
                        <small class="text-muted">Betty.h@ca.go.ke</small></p>

                    <h6>LS</h6>
                    <p><?php echo $instrument["assignee"]?><br>
                        <small class="text-muted">Odhiambo.Geofrey@ca.go.ke</small></p>


                </div>
            </div>
            <div class="card mb-4">
                <div class="card-header">
                    Documents
                </div>
                <div class="card-body">
                    <div class="conveyancing-documents-container" id="conveyancing-documents-container">
            

                    </div>


                    <hr>
                    <!-- <div class="text-center ">
                        <button class="btn btn-sm btn-primary" id="<?php echo $instrument["id"];?>" onclick="uploadFileForm(<?php echo $instrument["id"];?>)">
                            <i class="fas fa-upload"></i> Upload New Document
                        </button>
                    </div> -->
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // Update file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose file...');
    });

jQuery(document).ready(function () {
  
  var conveyancingInstrumentId = <?php echo $instrument["id"]; ?>;

  loadConveyancingDocuments(conveyancingInstrumentId)
   
});
</script>