<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Automated Legal Proofreading</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
          <div class="timeline">
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-primary mr-3">1</span>
                    <div>
                        <h5>Formatting Check <small class="text-muted">10:15 AM - 10 Feb 2025</small></h5>
                        <p>Automated scan for indentation, alignment, and heading structures.</p>
                        <button class="btn btn-sm btn-outline-info">View Report</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-secondary mr-3">2</span>
                    <div>
                        <h5>Citation Validation <small class="text-muted">10:45 AM - 10 Feb 2025</small></h5>
                        <p>Cross-checking legal references against existing statutes.</p>
                        <button class="btn btn-sm btn-outline-success">Fix Citations</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-warning mr-3">3</span>
                    <div>
                        <h5>Statutory Compliance Check <small class="text-muted">11:30 AM - 10 Feb 2025</small></h5>
                        <p>Ensuring the draft aligns with all mandatory legislative requirements.</p>
                        <button class="btn btn-sm btn-outline-primary">Resolve Issues</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="d-flex align-items-start">
                    <span class="badge badge-success mr-3">4</span>
                    <div>
                        <h5>Final Proof Approval <small class="text-muted">Pending</small></h5>
                        <p>Proofreading complete and awaiting counsel confirmation.</p>
                        <button class="btn btn-sm btn-outline-success">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        border-left: 3px solid #dee2e6;
        padding-left: 20px;
        margin-left: 10px;
    }
    .timeline-item {
        position: relative;
    }
    .timeline-item::before {
        content: "";
        position: absolute;
        left: -10px;
        top: 8px;
        background-color: #fff;
        border: 3px solid #007bff;
        border-radius: 50%;
        width: 20px;
        height: 20px;
    }
</style>
