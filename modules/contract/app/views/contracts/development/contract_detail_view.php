<?php defined('BASEPATH') OR exit('No direct script access allowed'); 
$id = $contract["id"];

?>
<header class="bg-white shadow-sm mb-4" >
    <div class="container py-3">
        <div class="d-flex justify-content-between align-items-center">
            <span class="mb-0"><a href="<?php echo base_url('contracts/view/'.$id); ?>"><?php echo $contract["name"]?></a><div>  <small class="text-muted"  ><?php echo $contract["description"]; ?></small> </div></span>

            <div>
               <a href="javascript:void(0)" onclick="contractEditForm('<?php echo $id;?>', event);" class="btn btn-primary" >Update</a>
            </div>
        </div>
    </div>
</header>

<div class="container-fluid mb-5" id="contract-detail-view" data-contractid="<?php echo $contract["id"]?>">
    <div class="row">
        <!-- Workflow Column -->
        <div class="col-lg-8" id="workflow-steps-container">
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
                <p class="mt-2">Loading workflow data...</p>
            </div>
        </div>

        <!-- Contract Summary Column -->
        <div class="col-lg-4 mb-4">
            <div class="contract-summary-box">
                <div class="summary-header">
                    <h5 class="mb-0 font-weight-bold">Contract Summary</h5>
                    <small class="text-muted" id="contract-reference"><?php echo $contract["reference_number"]; ?></small>
                </div>
                <div class="summary-content">

                    <div class="row summary-item">
                        <div class="col-6 label">Type:</div>
                        <div class="col-6 value" id="contract-type"><?php echo $contract["type"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Category:</div>
                        <div class="col-6 value" id="contract-category"><?php echo $contract["sub_type"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Parties:</div>
                        <div class="col-6 value" id="contract-supplier"><?php foreach($parties as $party ) {echo $party["party_name"]."(".$party["party_category_name"]."), ";} ?></div>

                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Value:</div>
                        <div class="col-6 value" id="contract-value"><?php echo $contract["currency"] . " " . number_format($contract["value"], 0, '.', ','); ?></div>
                    </div>

                    <div class="row summary-item">
                        <div class="col-6 label">Applicable Law:</div>
                        <div class="col-6 value" id="contract-applicable-law"><?php echo $contract["applicable_law"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Status:</div>
                        <span class="col-6 value" id="contract-status" style="color: <?php echo $contract["status_color"]; ?>"><?php echo $contract["status_name"]; ?></span>
                    </div>


                    <div class="row summary-item">
                        <div class="col-6 label">Risk Level:</div>
                        <div class="col-6 value" id="contract-risk-level"><?php echo $contract["priority"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Department:</div>
                        <div class="col-6 value" id="contract-department"><?php echo $contract["assigned_team"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Assignee:</div>
                        <div class="col-6 value" id="contract-assignee"><?php echo $contract["assignee"] ?? "N/A"; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Workflow Name:</div>
                        <div class="col-6 value" id="contract-workflow-name"><a href="<?php echo base_url("contract_workflows/configure/".$contract["workflow_id"]); ?>"><?php echo $contract["workflow_name"]; ?></a></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Archived:</div>
                        <div class="col-6 value" id="contract-archived"><?php echo $contract["archived"]; ?></div>
                    </div>
                    <h6 class="mt-3">Contract Timeline</h6>
                    <div class="row summary-item">
                        <div class="col-6 label">Contract Date:</div>
                        <div class="col-6 value" id="contract-contract-date"><?php echo $contract["contract_date"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Effective Date:</div>
                        <div class="col-6 value" id="contract-effective-date"><?php echo $contract["effective_date"] ?? "N/A"; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Start Date:</div>
                        <div class="col-6 value" id="contract-start-date"><?php echo $contract["start_date"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">End Date:</div>
                        <div class="col-6 value" id="contract-end-date"><?php echo $contract["end_date"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Duration:</div>
                        <div class="col-6 value" id="contract-duration"><?php
                            $start = new DateTime($contract["start_date"]);
                            $end = new DateTime($contract["end_date"]);
                            $interval = $start->diff($end);
                            echo round($interval->days / 30.42) . " months";
                            ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Remaining Days:</div>
                        <div class="col-6 value" id="contract-remaining-days"><?php
                            $today = new DateTime('2025-08-14');
                            $end = new DateTime($contract["end_date"]);
                            $interval = $today->diff($end);
                            echo $interval->days . " days";
                            ?></div>
                    </div>
                    <h6 class="mt-3">Completion Dates</h6>
                    <div class="row summary-item">
                        <div class="col-6 label">Expected Completion Date:</div>
                        <div class="col-6 value" id="contract-expected-completion-date"><?php echo $contract["expected_completion_date"] ?? "N/A"; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Actual Completion Date:</div>
                        <div class="col-6 value" id="contract-actual-completion-date"><?php echo $contract["actual_completion_date"] ?? "N/A"; ?></div>
                    </div>
                    <h6 class="mt-3">Performance Security</h6>
                    <div class="row summary-item">
                        <div class="col-6 label">Performance Security Commencement Date:</div>
                        <div class="col-6 value" id="contract-perf-security-commencement"><?php echo $contract["perf_security_commencement_date"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Performance Security Expiry Date:</div>
                        <div class="col-6 value" id="contract-bond-expiry"><?php echo $contract["perf_security_expiry_date"]; ?></div>
                    </div>
                    <h6 class="mt-3">Audit Trail</h6>
                    <div class="row summary-item">
                        <div class="col-6 label">Created By:</div>
                        <div class="col-6 value" id="contract-created-by"><?php echo $contract["creator"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Created Date:</div>
                        <div class="col-6 value" id="contract-created-date"><?php echo (new DateTime($contract["createdOn"]))->format('Y-m-d'); ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Last Modified By:</div>
                        <div class="col-6 value" id="contract-modified-by"><?php echo $contract["modifier"]; ?></div>
                    </div>
                    <div class="row summary-item">
                        <div class="col-6 label">Last Modified Date:</div>
                        <div class="col-6 value" id="contract-modified-date"><?php echo (new DateTime($contract["modifiedOn"]))->format('Y-m-d'); ?></div>
                    </div>
                    <div class="mt-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Workflow Progress</span>
                            <span id="workflow-progress-text">0%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div id="workflow-progress-bar" class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="contract-attachments-box mt-4" id="contract-docs-container">
                <div class="card">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Attachments</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover" id="attachments-table">
                                <thead>
                                <tr>
                                    <th>File Name</th>
                                   
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody id="attachments-list">
                                <!-- Attachments will be loaded here -->
                                <tr>
                                    <td colspan="3" class="text-center">Loading attachments...</td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-right mt-3">
                            <a class="btn btn-sm btn-primary" id="upload-new-btn" onclick="uploadFileForm();">
                                <i class="fa fa-upload mr-1"></i> Upload New
                            </a>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>
<script>
    var viewableExtensions = <?php echo json_encode($this->document_management_system->viewable_documents_extensions);?>
</script>