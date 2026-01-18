<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Version Management</h1>
            <p class="text-muted mb-0">Track and manage rule versions and effective dates</p>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createVersionModal">
            <i class="bi bi-tags me-1"></i> Create New Version
        </button>
    </div>

    <!-- Version Timeline -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Version Timeline</h5>
        </div>
        <div class="card-body">
            <div class="timeline">
                <!-- Current Version -->
                <div class="timeline-item mb-4">
                    <div class="d-flex">
                        <div class="timeline-marker bg-success rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Version 2.1 <span class="badge bg-success">Current</span></h6>
                                    <p class="text-muted mb-1">Updated slab rates and added new commercial rules</p>
                                    <small class="text-muted">Effective: Jan 1, 2024 • Created: Dec 15, 2023</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-outline-secondary">Compare</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Previous Versions -->
                <div class="timeline-item mb-4">
                    <div class="d-flex">
                        <div class="timeline-marker bg-secondary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Version 2.0</h6>
                                    <p class="text-muted mb-1">Major update with new matter categories</p>
                                    <small class="text-muted">Effective: Jul 1, 2023 • Created: Jun 15, 2023</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-outline-secondary">Compare</button>
                                    <button class="btn btn-outline-success">Restore</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item mb-4">
                    <div class="d-flex">
                        <div class="timeline-marker bg-secondary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Version 1.2</h6>
                                    <p class="text-muted mb-1">Bug fixes and performance improvements</p>
                                    <small class="text-muted">Effective: Mar 1, 2023 • Created: Feb 20, 2023</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-outline-secondary">Compare</button>
                                    <button class="btn btn-outline-success">Restore</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="d-flex">
                        <div class="timeline-marker bg-secondary rounded-circle me-3" style="width: 12px; height: 12px; margin-top: 6px;"></div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">Version 1.1</h6>
                                    <p class="text-muted mb-1">Minor corrections and clarifications</p>
                                    <small class="text-muted">Effective: Jan 1, 2023 • Created: Dec 10, 2022</small>
                                </div>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">View Details</button>
                                    <button class="btn btn-outline-secondary">Compare</button>
                                    <button class="btn btn-outline-success">Restore</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Version Comparison -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Version Comparison</h5>
            <div class="btn-group btn-group-sm">
                <button class="btn btn-outline-secondary">v2.1</button>
                <button class="btn btn-outline-secondary">v2.0</button>
                <button class="btn btn-outline-primary">Compare Selected</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                    <tr>
                        <th>Rule Name</th>
                        <th width="45%">Version 2.1</th>
                        <th width="45%">Version 2.0</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Instruction Fee - Civil Suit</td>
                        <td>
                            <small>Slab 1: 0-1M @ 7.5%</small><br>
                            <small>Slab 2: 1-5M @ 5.0%</small><br>
                            <small>Slab 3: 5-10M @ 1.44%</small>
                        </td>
                        <td>
                            <small>Slab 1: 0-1M @ 7.5%</small><br>
                            <small>Slab 2: 1-5M @ 5.0%</small><br>
                            <small>Slab 3: 5M+ @ 1.44%</small>
                        </td>
                        <td><span class="badge bg-warning">Modified</span></td>
                    </tr>
                    <tr>
                        <td>Conveyancing - Sale/Purchase</td>
                        <td><small>No changes</small></td>
                        <td><small>No changes</small></td>
                        <td><span class="badge bg-success">Same</span></td>
                    </tr>
                    <tr>
                        <td>Commercial Agreement Drafting</td>
                        <td><small>New rule added</small></td>
                        <td><small>Not present</small></td>
                        <td><span class="badge bg-info">Added</span></td>
                    </tr>
                    <tr>
                        <td>Probate Administration</td>
                        <td><small>Fixed amount: KES 25,000</small></td>
                        <td><small>Percentage: 2.5%</small></td>
                        <td><span class="badge bg-danger">Changed</span></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>