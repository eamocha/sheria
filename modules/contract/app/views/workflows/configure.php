<div class="container-fluid mb-5">
     <div class="col-md-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item" aria-current="page"><a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a></li>
                    <li class="breadcrumb-item" aria-current="page"><a href="<?php echo app_url("modules/contract/contract_workflows/index#").$workflow['id']; ?>"><?php echo $this->lang->line("workflows"); ?></a></li>
                    <li class="breadcrumb-item" aria-current="page">
                        <a href="<?php echo app_url("modules/contract/contract_statuses"); ?>">
                            <?php echo $this->lang->line("contract_statuses"); ?>
                        </a>
                    </li>
                </ol>
            </nav>
        </div>
    <input type="hidden" name="workflow_id"  id="workflow_id" value="<?php echo $workflow['id']?>" />

    <header class="bg-white shadow-sm mb-4">
        <div class="container-fluid py-3">
            <h1 class="h3 mb-0  font-weight-bold">Workflow Builder for <b><?php echo $workflow['name']?></b></h1>

        </div>
    </header>
    
    <div class="row">
        
        <div class="col-lg-6">
            <div class="workflow-config-section">
                <h4 class="mb-4 ">Manage Steps </h4>
                <form id="addStepForm">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="stepTitle">Step Title:</label>
                            <input type="text" class="form-control" id="stepTitle" placeholder="e.g., Draft Contract" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepIcon">Step Icon:</label>
                            <select class="form-control" id="stepIcon" required>
                                <option value="">Select Icon for Step</option>
                            </select>
                        </div>

                    </div>
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label for="stepContractLink">Category:</label>
                          <select class="form-control" id="category_id" name="category_id">
                              <option value="">Select Category</option>
                              <option value="1">New</option>
                              <option value="2">In progress</option>
                              <option value="3">Done</option>
                          </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepResponsibility">Responsible User/Role:</label>
                            <input type="text" class="form-control" id="stepResponsibility" placeholder="e.g., SO/OCP, Legal Team" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stepActivity">Activity to be done:</label>
                        <textarea class="form-control" id="stepActivity" rows="2" placeholder="e.g., Drafting of contract based on bid documents"></textarea>
                    </div>
                    <div class="form-row">

                        <div class="form-group col-md-6">
                            <label for="stepInput">Step Input:</label>
                            <input type="text" class="form-control" id="stepInput" placeholder="e.g., Bid documents, Negotiation Minutes">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepOutput">Step Output:</label>
                            <input type="text" class="form-control" id="stepOutput" placeholder="e.g., Draft contract">
                        </div>
                    </div>

                    <div class="form-group form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="includeContractFile" checked="checked">
                        <label class="form-check-label" for="includeContractFile">Show link to the contract File?</label>
                    </div>
                    <div class="form-group form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="isStartPoint">
                        <label class="form-check-label" for="isStartPoint">Is Start Point?</label>
                    </div>

                    <div class="form-group form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="approvalStartPoint">
                        <label class="form-check-label" for="approvalStartPoint">Is Approval Start Point?</label>
                    </div>
                    <div class="form-group form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="isSignaturePoint">
                        <label class="form-check-label" for="isSignaturePoint">Is Signature Point?</label>
                    </div>
                    <div class="form-group form-check mt-3">
                        <input type="checkbox" class="form-check-input" id="isGlobal">
                        <label class="form-check-label" for="isGlobal">Is Global Step?</label>
                    </div>

                    <h5 class="mt-4 mb-3 text-secondary">Transition Checklist Items</h5>
                    <div id="checklistItemsContainer">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addChecklistItemInput()">
                        <i class="fa fa-plus-circle"></i> Add Checklist Item
                    </button>

                    <h5 class="mt-4 mb-3 text-secondary">Configure Action Buttons (Dropdown)</h5>
                    <div id="actionButtonsContainer">
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addActionButtonInput()">
                        <i class="fa fa-plus-circle"></i> Add Action Button
                    </button>

                    <input type="hidden" name="add_edit_form"  id="add_edit_form" value="" />
                    <button type="submit" class="btn btn-primary btn-block">Add Step</button>
                </form>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="workflow-config-section">
                <h4 class="mb-4 ">Configured Workflow Steps</h4>
                <div id="configuredWorkflowDisplay">
                    <p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>
                </div>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="transitionModal" tabindex="-1" aria-labelledby="transitionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-right modal-dialog-scrollable h-100 m-0">
        <div class="modal-content h-100 border-0">
            <div class="modal-header">
                <h5 class="modal-title" id="transitionModalLabel">Transition from Step: <span id="currentStepTitleInModal"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Available Steps for Transition:</h6>
                <div id="availableTransitionsList">
                </div>
            </div>
            <div class="modal-footer" id="transitionModalFooter">
            </div>
        </div>
    </div>
</div>
