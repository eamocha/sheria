<div class="container mt-4">
    <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Case Details: <?php echo htmlspecialchars($legalCase['subject']); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Case ID:</strong> <?php echo htmlspecialchars($legalCase['id']); ?></li>
                                <li class="list-group-item"><strong>Status:</strong> <span class="badge badge-info"><?php echo htmlspecialchars($legalCase['Status']); ?></span></li>
                                <li class="list-group-item"><strong>Category:</strong> <?php echo htmlspecialchars($legalCase['category']); ?></li>
                                <li class="list-group-item"><strong>Practice Area:</strong> <?php echo htmlspecialchars($legalCase['practice_area']); ?></li>
                                <li class="list-group-item"><strong>Internal Reference:</strong> <?php echo htmlspecialchars($legalCase['internalReference']); ?></li>
                                <li class="list-group-item"><strong>Priority:</strong> <?php echo htmlspecialchars($legalCase['priority']); ?></li>
                                <li class="list-group-item"><strong>Workflow:</strong> <?php echo htmlspecialchars($workflow_applicable['name']); ?></li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Assigned To:</strong> <?php echo htmlspecialchars($legalCase['Assignee']); ?></li>
                                <li class="list-group-item"><strong>Arrival Date:</strong> <?php echo htmlspecialchars($legalCase['arrivalDate']); ?></li>
                                <li class="list-group-item"><strong>Due Date:</strong> <?php echo htmlspecialchars($legalCase['dueDate'] ?? 'N/A'); ?></li>
                                <li class="list-group-item"><strong>Closed On:</strong> <?php echo htmlspecialchars($legalCase['closedOn'] ?? 'N/A'); ?></li>
                                <li class="list-group-item"><strong>Created On:</strong> <?php echo htmlspecialchars($legalCase['createdOn']); ?></li>
                                <li class="list-group-item"><strong>Modified On:</strong> <?php echo htmlspecialchars($legalCase['modifiedOn']); ?></li>
                                <li class="list-group-item"><strong>Effective Effort:</strong> <?php echo htmlspecialchars($legalCase['effectiveEffort']); ?></li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item"><strong>Case Value:</strong> <?php echo htmlspecialchars($legalCase['caseValue']); ?></li>
                                <li class="list-group-item"><strong>Recovered Value:</strong> <?php echo htmlspecialchars($legalCase['recoveredValue']); ?></li>
                                <li class="list-group-item"><strong>Cap Amount:</strong> <?php echo htmlspecialchars($legalCase['cap_amount']); ?></li>
                            </ul>
                        </div>
                    </div>
                    <hr>
                    <div class="mt-3">
                        <h6>Description</h6>
                        <p class="text-muted"><?php echo nl2br(htmlspecialchars($legalCase['description'] ?? 'No description provided.')); ?></p>
                    </div>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Client Information</h5>
                    <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#addClientModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-0"><?php echo  htmlspecialchars(strtoupper($clientData['name']??"")); ?></h6>
                                <small class="text-muted"><?php echo htmlspecialchars(strtoupper($legalCaseClientPositionName ?? 'CLIENT')); ?></small>
                            </div>
                            <div>
                                <button class="btn btn-sm btn-outline-info" data-toggle="tooltip" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" data-toggle="tooltip" title="Delete">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card mb-4 shadow-sm">
                <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Opponents</h5>
                    <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#addOpponentModal">
                        <i class="fas fa-plus"></i> Add
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($relatedOpponentData)): ?>
                        <?php foreach ($relatedOpponentData as $opponent): ?>
                            <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2 opponent-item">
                                <div class="party-name flex-grow-1"><?php echo htmlspecialchars(strtoupper($opponent['opponentName'])); ?></div>
                                <div class="dotted-line"></div>
                                <div class="party-position text-muted text-right pr-2">
                                    <small><?php echo htmlspecialchars(strtoupper($opponent['position_name'])); ?></small>
                                </div>
                                <div class="party-actions d-flex">
                                    <button class="btn btn-sm btn-outline-info ml-2" data-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger ml-1" data-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-muted text-center">No opponents found.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .opponent-item {
        position: relative;
    }
    .party-name {
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
    }
    .dotted-line {
        flex-grow: 1;
        margin: 0 5px;
        border-bottom: 1px dotted #ccc;
        height: 1em;
        line-height: 1em;
    }
    .party-position {
        white-space: nowrap;
    }
</style>