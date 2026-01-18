<div class="container-fluid py-3">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">Case #<?php echo $legalCase['id']; ?> - General Information</h4>
                    <p class="text-muted mb-0"><?php echo $legalCase['client_name']; ?> - <?php echo $Case_Types[$legalCase['case_type_id']]; ?> Case</p>
                </div>
                <span class="badge badge-primary p-2"><?php echo $Case_Statuses[$legalCase['case_status_id']]; ?></span>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Case Public Information -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel mr-2"></i>
                        Case Public Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Reference Case</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $legalCase['internalReference'] ?: 'Not specified'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Status</label>
                                <div>
                                    <?php if ($legalCase['closedOn']): ?>
                                        <span class="badge badge-danger p-2">Closed</span>
                                    <?php else: ?>
                                        <span class="badge badge-success p-2">Ongoing</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Subject</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $legalCase['subject']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Case Type</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $Case_Types[$legalCase['case_type_id']]; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Parties Information -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Client Position</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $clientPositions[$legalCase['legal_case_client_position_id']] ?? 'None'; ?></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Client Name</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $legalCase['client_name']; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <h5 class="text-muted">Vs.</h5>
                    </div>

                    <!-- Case Details -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Description</label>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0"><?php echo $legalCase['description'] ?: 'No description provided'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="font-weight-bold">Latest Development</label>
                                <div class="border rounded p-3 bg-light">
                                    <p class="mb-0"><?php echo $legalCase['latest_development'] ?: 'No recent developments'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Case Properties -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Priority</label>
                                <div>
                                    <?php
                                    $priorityClass = [
                                            'critical' => 'danger',
                                            'high' => 'warning',
                                            'medium' => 'info',
                                            'low' => 'secondary'
                                    ];
                                    ?>
                                    <span class="badge badge-<?php echo $priorityClass[$legalCase['priority']] ?? 'secondary'; ?> p-2">
                                        <?php echo $priorities[$legalCase['priority']]; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Case Stage</label>
                                <p class="form-control-plaintext border-bottom pb-2"><?php echo $caseStages[$legalCase['legal_case_stage_id']]; ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Success Probability</label>
                                <div>
                                    <span class="badge badge-info p-2">
                                        <?php echo $successProbabilities[$legalCase['legal_case_success_probability_id']] ?? 'Not specified'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">Case Value</label>
                                <p class="form-control-plaintext border-bottom pb-2">$<?php echo number_format($legalCase['caseValue'], 2); ?></p>
                            </div>
                        </div>
                    </div>

                    <?php if ($legalCase['category'] == 'Litigation'): ?>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Judgment Value</label>
                                    <p class="form-control-plaintext border-bottom pb-2">$<?php echo number_format($legalCase['judgmentValue'], 2); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="font-weight-bold">Recovered Value</label>
                                    <p class="form-control-plaintext border-bottom pb-2">$<?php echo number_format($legalCase['recoveredValue'], 2); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Transitions -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exchange-alt mr-2"></i>
                        Status Transitions
                    </h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($statusTransitions)): ?>
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="thead-light">
                                <tr>
                                    <th>Transition</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Comments</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php foreach ($statusTransitions as $transition): ?>
                                    <tr>
                                        <td><?php echo $transition['name']; ?></td>
                                        <td><?php echo $transition['fromStepName']; ?></td>
                                        <td><?php echo $transition['toStepName']; ?></td>
                                        <td><?php echo $transition['comments'] ?: '-'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p class="text-muted text-center mb-0">No status transitions available</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Activity Logs -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-history mr-2"></i>
                        Activity Logs
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="border-left border-success pl-3 mb-3">
                                <h6>Created</h6>
                                <p class="mb-1"><strong>By:</strong> <?php echo $tabsNLogs['actionLogs']['insert']['by']; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $tabsNLogs['actionLogs']['insert']['email']; ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?php echo $tabsNLogs['actionLogs']['insert']['on']; ?></p>
                                <span class="badge badge-success"><?php echo $tabsNLogs['actionLogs']['insert']['status']; ?></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="border-left border-warning pl-3 mb-3">
                                <h6>Last Updated</h6>
                                <p class="mb-1"><strong>By:</strong> <?php echo $tabsNLogs['actionLogs']['update']['by']; ?></p>
                                <p class="mb-1"><strong>Email:</strong> <?php echo $tabsNLogs['actionLogs']['update']['email']; ?></p>
                                <p class="mb-1"><strong>Date:</strong> <?php echo $tabsNLogs['actionLogs']['update']['on']; ?></p>
                                <span class="badge badge-success"><?php echo $tabsNLogs['actionLogs']['update']['status']; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Case Options -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-cog mr-2"></i>
                        Case Options
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="font-weight-bold">Externalize Lawyers:</span>
                        <span class="badge badge-<?php echo $legalCase['externalizeLawyers'] == 'yes' ? 'success' : 'danger'; ?> ml-2">
                            <?php echo ucfirst($legalCase['externalizeLawyers']); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Partners Commissions:</span>
                        <span class="badge badge-<?php echo $partnersCommissions == 'yes' ? 'success' : 'danger'; ?> ml-2">
                            <?php echo ucfirst($partnersCommissions); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">SLA Feature:</span>
                        <span class="badge badge-<?php echo $slaFeature == 'yes' ? 'success' : 'danger'; ?> ml-2">
                            <?php echo ucfirst($slaFeature); ?>
                        </span>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Shared Documents:</span>
                        <span class="badge badge-<?php echo $sharedDocumentsWithAdvisors ? 'success' : 'danger'; ?> ml-2">
                            <?php echo $sharedDocumentsWithAdvisors ? 'Yes' : 'No'; ?>
                        </span>
                    </div>
                    <div class="mb-0">
                        <span class="font-weight-bold">Archived Matters:</span>
                        <span class="badge badge-<?php echo $disableArchivedMatters == '1' ? 'success' : 'secondary'; ?> ml-2">
                            <?php echo $disableArchivedMatters == '1' ? 'Disabled' : 'Enabled'; ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Assigned Team -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-users mr-2"></i>
                        Assigned Team
                    </h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php foreach ($usersProviderGroup as $id => $name): ?>
                            <?php if (!empty($id) && !empty($name)): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo $name; ?>
                                    <span class="badge badge-primary badge-pill"><?php echo $id; ?></span>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Dates Information -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        Dates
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="font-weight-bold">Arrival Date:</span>
                        <p class="mb-0"><?php echo $legalCase['arrivalDate']; ?></p>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Due Date:</span>
                        <p class="mb-0"><?php echo $legalCase['dueDate']; ?></p>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Created On:</span>
                        <p class="mb-0"><?php echo $legalCase['createdOn']; ?></p>
                    </div>
                    <div class="mb-0">
                        <span class="font-weight-bold">Modified On:</span>
                        <p class="mb-0"><?php echo $legalCase['modifiedOn']; ?></p>
                    </div>
                </div>
            </div>

            <!-- Workflow Information -->
            <div class="card mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-project-diagram mr-2"></i>
                        Workflow
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="font-weight-bold">Workflow Used:</span>
                        <p class="mb-0"><?php echo $workflow_applicable['name']; ?></p>
                    </div>
                    <div class="mb-3">
                        <span class="font-weight-bold">Workflow Status:</span>
                        <span class="badge badge-success"><?php echo ucfirst($legalCase['workflow_status_category']); ?></span>
                    </div>
                    <div class="mb-0">
                        <span class="font-weight-bold">Category:</span>
                        <p class="mb-0"><?php echo $workflow_applicable['category']; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>