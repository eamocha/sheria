<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Change Detection Dashboard</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <div class="timeline">
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-primary mr-3">1</span>
                    <div>
                        <h5>Initial Draft Scan <small class="text-muted">08:45 AM - 10 Feb 2025</small></h5>
                        <p>System performs an initial scan for formatting compliance.</p>
                        <button class="btn btn-sm btn-outline-info">View Scan Report</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-warning mr-3">2</span>
                    <div>
                        <h5>Change Detected <small class="text-muted">09:10 AM - 10 Feb 2025</small></h5>
                        <p>Detected unformatted section heading in Clause 5.</p>
                        <button class="btn btn-sm btn-outline-warning">Highlight Issue</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-danger mr-3">3</span>
                    <div>
                        <h5>Incorrect Citation Format <small class="text-muted">09:25 AM - 10 Feb 2025</small></h5>
                        <p>Detected citation not matching legal style guide in Clause 8.</p>
                        <button class="btn btn-sm btn-outline-danger">View Suggestion</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="d-flex align-items-start">
                    <span class="badge badge-success mr-3">4</span>
                    <div>
                        <h5>Corrections Applied <small class="text-muted">09:40 AM - 10 Feb 2025</small></h5>
                        <p>All detected formatting issues corrected and verified.</p>
                        <button class="btn btn-sm btn-outline-success">Mark Complete</button>
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
