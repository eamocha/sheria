<div class="exhibit-management-container">
    <!-- Header with Status Indicator -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Exhibit Details</h2>
        <span class="badge badge-pill badge-secondary" id="exhibitStatusBadge">ACTIVE</span>
    </div>

    <!-- Main Exhibit Card with Tabs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span id="exhibitDetailHeader">Exhibit S/No.: -</span>
                <small class="text-muted ml-2" id="exhibitLastUpdated">Last updated: -</small>
            </div>
            <div class="btn-group">
                <button class="btn btn-warning btn-sm rounded mr-2" id="editExhibitBtn">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm rounded mr-2" id="deleteExhibitBtn">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-info btn-sm rounded" id="changeStatusBtn">
                    <i class="fas fa-exchange-alt"></i> Change Status
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Exhibit Details Tabs -->
            <ul class="nav nav-tabs" id="exhibitTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details">Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents">Documents</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="photos-tab" data-toggle="tab" href="#photos">Photos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="chain-tab" data-toggle="tab" href="#chain">Chain of Custody</a>
                </li>
            </ul>

            <div class="tab-content pt-3" id="exhibitTabContent">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details" role="tabpanel">
                    <div class="row">
                        <!-- Basic Information Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                                <div class="detail-row">
                                    <span class="detail-label">S/no.:</span>
                                    <span class="detail-value" id="detail_sno">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Label/name:</span>
                                    <span class="detail-value" id="detail_label_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Description:</span>
                                    <span class="detail-value" id="detail_description_of_exhibit">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Current status:</span>
                                    <span class="detail-value" id="detail_current_status">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Current location:</span>
                                    <span class="detail-value" id="detail_current_location">-</span>
                                </div>
                            </div>

                            <div class="detail-section mt-4">
                                <h5><i class="fas fa-calendar-alt"></i> Dates</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Date received:</span>
                                    <span class="detail-value" id="detail_date_received">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date disposed:</span>
                                    <span class="detail-value" id="detail_date_disposed">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Case Information Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="fas fa-gavel"></i> Case Information</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Case reference:</span>
                                    <span class="detail-value" id="detail_caseReference">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Case subject:</span>
                                    <span class="detail-value" id="detail_case_subject_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Court:</span>
                                    <span class="detail-value" id="detail_court_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Clients:</span>
                                    <span class="detail-value" id="detail_clients">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Opponents:</span>
                                    <span class="detail-value" id="detail_opponents">-</span>
                                </div>
                            </div>

                            <div class="detail-section mt-4">
                                <h5><i class="fas fa-user-shield"></i> Personnel</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Officers involved:</span>
                                    <span class="detail-value" id="detail_officers_involved">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Created by:</span>
                                    <span class="detail-value" id="detail_created_by">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Last modified by:</span>
                                    <span class="detail-value" id="detail_modified_by">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Associated Documents</h5>
                        <button class="btn btn-success btn-sm rounded" id="addDocumentBtn">
                            <i class="fas fa-plus"></i> Add Document
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="documentsTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Date Added</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No documents associated</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Photos Tab -->
                <div class="tab-pane fade" id="photos" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Associated Photos</h5>
                        <button class="btn btn-success btn-sm rounded" id="addPhotoBtn">
                            <i class="fas fa-plus"></i> Add Photo
                        </button>
                    </div>

                    <div class="row" id="photosGrid">
                        <div class="col-12 text-center text-muted">
                            No photos associated
                        </div>
                    </div>
                </div>

                <!-- History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Activity History</h5>
                        <button class="btn btn-primary btn-sm rounded" id="addNoteBtn">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </div>

                    <div class="timeline" id="exhibitTimeline">
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">No activity recorded</div>
                                <div class="timeline-text text-muted">This exhibit has no recorded activity</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chain of Custody Tab -->
                <div class="tab-pane fade" id="chain" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Chain of Custody</h5>
                        <button class="btn btn-info btn-sm rounded" id="addCustodyBtn">
                            <i class="fas fa-plus"></i> Record Transfer
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="custodyTable">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Purpose</th>
                                <th>Notes</th>
                                <th>Recorded By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No custody transfers recorded</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Footer -->
    <div class="quick-actions mt-3">
        <button class="btn btn-outline-primary rounded mr-2" id="printExhibitBtn">
            <i class="fas fa-print"></i> Print Details
        </button>
        <button class="btn btn-outline-secondary rounded mr-2" id="exportExhibitBtn">
            <i class="fas fa-file-export"></i> Export
        </button>
        <button class="btn btn-outline-info rounded mr-2" id="qrCodeBtn">
            <i class="fas fa-qrcode"></i> Generate QR Code
        </button>
        <button class="btn btn-outline-dark rounded" onclick="window.location.href='<?php echo base_url('exhibits'); ?>'">
            <i class="fas fa-arrow-left"></i> Back to List
        </button>
    </div>
</div>

<!-- Modal Includes -->
<?php $this->load->view('prosecution/exhibits/management/edit'); ?>
<?php $this->load->view('prosecution/exhibits/management/add_document'); ?>
<?php $this->load->view('prosecution/exhibits/management/add_photo'); ?>
<?php $this->load->view('prosecution/exhibits/management/add_note'); ?>
<?php $this->load->view('prosecution/exhibits/management/status_change'); ?>
<?php $this->load->view('prosecution/exhibits/management/custody_transfer'); ?>
<?php $this->load->view('prosecution/exhibits/management/qr_code'); ?>

