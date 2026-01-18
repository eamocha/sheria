<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Legislative Drafting: National Health Bill</h3>
</div>
<div class="timeline">
    <!-- Step 1: Drafting -->
    <div class="timeline-item">
        <span class="timeline-badge bg-primary"><i class="fas fa-file-alt"></i></span>
        <div class="timeline-content">
            <h6>Drafting</h6>
            <p class="small text-muted mb-1">Initial drafting of the legislative document by Parliamentary Counsel.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-primary">Share Draft</button>
                <button class="btn btn-sm btn-outline-secondary">Message Requester</button>
            </div>
        </div>
    </div>
    <!-- Step 2: Reviewing -->
    <div class="timeline-item">
        <span class="timeline-badge bg-info"><i class="fas fa-search"></i></span>
        <div class="timeline-content">
            <h6>Reviewing Draft</h6>
            <p class="small text-muted mb-1">Peer review to ensure accuracy, compliance, and quality.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-info">Send for Review</button>
                <button class="btn btn-sm btn-outline-secondary">Request Clarification</button>
            </div>
        </div>
    </div>
    <!-- Step 3: Awaiting Feedback -->
    <div class="timeline-item">
        <span class="timeline-badge bg-warning"><i class="fas fa-hourglass-half"></i></span>
        <div class="timeline-content">
            <h6>Awaiting Feedback</h6>
            <p class="small text-muted mb-1">Waiting for requester’s feedback or approval before proceeding.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-warning text-white">Send Reminder</button>
                <button class="btn btn-sm btn-outline-secondary">Chat with Requester</button>
            </div>
        </div>
    </div>
    <!-- Step 4: Finalizing -->
    <div class="timeline-item">
        <span class="timeline-badge bg-success"><i class="fas fa-check-circle"></i></span>
        <div class="timeline-content">
            <h6>Finalizing</h6>
            <p class="small text-muted mb-1">Final changes implemented and document prepared for sign-off.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-success">Send Final Draft</button>
                <button class="btn btn-sm btn-outline-secondary">Update Requester</button>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        margin-left: 2rem;
        padding-left: 1rem;
        border-left: 2px solid #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-badge {
        position: absolute;
        left: -30px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .timeline-content {
        background: #fff;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>