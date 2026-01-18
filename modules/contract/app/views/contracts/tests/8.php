
<header class="bg-white shadow-sm mb-4">
    <div class="container-fluid py-3">
        <h1 class="h3 mb-0 text-primary font-weight-bold">Contract Workflow Builder</h1>
    </div>
</header>

<div class="container-fluid mb-5">
    <div class="row">
        <!-- Left Column: Add New Step Form (col-lg-8) -->
        <div class="col-lg-8">
            <div class="workflow-config-section">
                <h4 class="mb-4 text-primary">Add New Workflow Step</h4>
                <form id="addStepForm">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="stepTitle">Step Title:</label>
                            <input type="text" class="form-control" id="stepTitle" placeholder="e.g., Draft Contract" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepResponsibility">Responsible User/Role:</label>
                            <input type="text" class="form-control" id="stepResponsibility" placeholder="e.g., SO/OCP, Legal Team" required>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="stepIcon">Step Icon:</label>
                            <select class="form-control" id="stepIcon" required>
                                <option value="">Select Icon for Step</option>
                                <!-- Icons will be populated by JS -->
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="stepContractLink">Contract Link (Placeholder):</label>
                            <input type="text" class="form-control" id="stepContractLink" placeholder="e.g., CA/SCM/2023/001_Draft.pdf">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="stepActivity">Activity:</label>
                        <textarea class="form-control" id="stepActivity" rows="2" placeholder="e.g., Drafting of contract based on bid documents"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="stepOutput">Output:</label>
                        <input type="text" class="form-control" id="stepOutput" placeholder="e.g., Draft contract">
                    </div>

                    <h5 class="mt-4 mb-3 text-secondary">Transition Checklist Items</h5>
                    <div id="checklistItemsContainer">
                        <!-- Dynamic checklist item inputs will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addChecklistItemInput()">
                        <i class="fa fa-plus-circle"></i> Add Checklist Item
                    </button>

                    <h5 class="mt-4 mb-3 text-secondary">Configure Action Buttons (Dropdown)</h5>
                    <div id="actionButtonsContainer">
                        <!-- Dynamic action button inputs will be added here -->
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-info mb-3" onclick="addActionButtonInput()">
                        <i class="fa fa-plus-circle"></i> Add Action Button
                    </button>

                    <button type="submit" class="btn btn-primary btn-block">Add Step</button>
                </form>
            </div>
        </div>

        <!-- Right Column: Configured Workflow Steps & Export (col-lg-4) -->
        <div class="col-lg-4">
            <div class="workflow-config-section">
                <h4 class="mb-4 text-primary">Configured Workflow Steps</h4>
                <div id="configuredWorkflowDisplay">
                    <p class="text-muted text-center" id="noStepsMessage">No steps configured yet. Add a step using the form on the left.</p>
                </div>
                <hr>
                <h4 class="mt-4 mb-3 text-primary">Export Configuration (JSON)</h4>
                <div class="form-group">
                    <textarea class="form-control" id="exportConfig" rows="10" readonly></textarea>
                </div>
                <button type="button" class="btn btn-info btn-block" onclick="copyConfigToClipboard()">
                    <i class="fa fa-copy"></i> Copy to Clipboard
                </button>
            </div>
        </div>
    </div>
</div>

<footer class="bg-white py-3 border-top">
    <div class="container-fluid text-center">
        <p class="text-muted mb-0">© 2025 Contract Authority Management System</p>
    </div>
</footer>

<!-- Modal Structure for Transitions -->
<div class="modal fade" id="transitionModal" tabindex="-1" aria-labelledby="transitionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-right">
        <div class="modal-content h-100">
            <div class="modal-header">
                <h5 class="modal-title" id="transitionModalLabel">Transition from Step: <span id="currentStepTitleInModal"></span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>Available Steps for Transition:</h6>
                <div id="availableTransitionsList">
                    <!-- Transition buttons will be dynamically added here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>