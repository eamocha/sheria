<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Draft Progress Timeline</h3>
</div>
<div class="card">
    <div class="card-body">
        <div id="timelineRequestInfo" class="mb-4 small text-muted">REF-2025-001 — Tax Reform Bill</div>
        <div class="timeline" id="draftTimeline">
            <div class="timeline-item completed">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Instructions Received</strong>
                        <span class="badge badge-light">02 Feb 2025</span>
                    </div>
                    <div class="small text-muted mb-2">Request logged and initial instructions captured</div>
                    <button class="btn btn-sm btn-outline-primary">View Details</button>
                </div>
            </div>
            <div class="timeline-item completed">
                <div class="timeline-badge">✓</div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Research</strong>
                        <span class="badge badge-light">06 Feb 2025</span>
                    </div>
                    <div class="small text-muted mb-2">Legal research and precedent checks</div>
                    <button class="btn btn-sm btn-outline-primary">View Research Docs</button>
                </div>
            </div>
            <div class="timeline-item current">
                <div class="timeline-badge">●</div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Drafting</strong>
                        <span class="badge badge-light">In Progress</span>
                    </div>
                    <div class="small text-muted mb-2">Drafting of the legislative text</div>
                    <button class="btn btn-sm btn-success">Open Draft</button>
                    <button class="btn btn-sm btn-warning">Assign Task</button>
                </div>
            </div>
            <div class="timeline-item upcoming">
                <div class="timeline-badge"></div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Review</strong>
                        <span class="badge badge-secondary">Pending</span>
                    </div>
                    <div class="small text-muted mb-2">Internal and external reviews</div>
                    <button class="btn btn-sm btn-outline-secondary" disabled>Start Review</button>
                </div>
            </div>
            <div class="timeline-item upcoming">
                <div class="timeline-badge"></div>
                <div class="timeline-content">
                    <div class="d-flex justify-content-between">
                        <strong>Approval</strong>
                        <span class="badge badge-secondary">Pending</span>
                    </div>
                    <div class="small text-muted mb-2">Final approvals and sign-off</div>
                    <button class="btn btn-sm btn-outline-secondary" disabled>Approve</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        margin: 1.5rem 0;
        padding-left: 3rem; /* increased padding to prevent overlap */
    }
    .timeline::before {
        content: '';
        position: absolute;
        left: 20px;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e9ecef;
        border-radius: 2px;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 1.5rem;
    }
    .timeline-badge {
        position: absolute;
        left: 8px;
        top: 6px; /* adjusted to align with content */
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 12px;
        z-index: 1; /* ensures badge stays on top */
    }
    .timeline-content {
        padding: .5rem .75rem;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: .25rem;
        margin-left: 2.5rem; /* push content right to clear badge */
    }
    .timeline-item.completed .timeline-badge { background: #28a745; }
    .timeline-item.current .timeline-badge { background: #007bff; }
    .timeline-item.upcoming .timeline-badge { background: #6c757d; }
</style>
