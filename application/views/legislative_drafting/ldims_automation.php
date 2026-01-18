<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Workflow Automation Dashboard</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
         <div class="timeline">
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-primary mr-3">1</span>
                    <div>
                        <h5>Instructions Received <small class="text-muted">08:45 AM - 10 Feb 2025</small></h5>
                        <p>Draft request logged and assigned to Wanjiku Kamau.</p>
                        <button class="btn btn-sm btn-outline-info">View Details</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-secondary mr-3">2</span>
                    <div>
                        <h5>Research <small class="text-muted">11:30 AM - 11 Feb 2025</small></h5>
                        <p>Background research and legal precedent gathering by research team.</p>
                        <button class="btn btn-sm btn-outline-success">Mark Complete</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-warning mr-3">3</span>
                    <div>
                        <h5>Drafting <small class="text-muted">03:15 PM - 13 Feb 2025</small></h5>
                        <p>Initial draft prepared and shared with collaborating counsels.</p>
                        <button class="btn btn-sm btn-outline-primary">Edit Draft</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item mb-4">
                <div class="d-flex align-items-start">
                    <span class="badge badge-info mr-3">4</span>
                    <div>
                        <h5>Review <small class="text-muted">09:00 AM - 16 Feb 2025</small></h5>
                        <p>Draft under peer review for accuracy and compliance.</p>
                        <button class="btn btn-sm btn-outline-warning">Request Changes</button>
                    </div>
                </div>
            </div>
            <div class="timeline-item">
                <div class="d-flex align-items-start">
                    <span class="badge badge-success mr-3">5</span>
                    <div>
                        <h5>Approval <small class="text-muted">Pending</small></h5>
                        <p>Awaiting final sign-off from the approving authority.</p>
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
