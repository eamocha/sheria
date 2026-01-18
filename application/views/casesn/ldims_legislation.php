<div class="container mt-4">
    <h3 class="mb-4">Bill Progress Tracker</h3>
    <div class="timeline">

        <!-- Stage 1: First Reading -->
        <div class="timeline-item">
            <span class="timeline-badge bg-primary"><i class="fas fa-book-open"></i></span>
            <div class="timeline-content">
                <h6>First Reading</h6>
                <p class="small text-muted mb-1">Bill introduced and read for the first time in Parliament.</p>
                <div class="mb-2">
                    <span class="badge bg-primary">Completed</span>
                    <small class="text-muted ms-2">05 Aug 2025</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-file-pdf"></i> View Bill Document</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-history"></i> View History</button>
                </div>
            </div>
        </div>

        <!-- Stage 2: Committee Stage -->
        <div class="timeline-item">
            <span class="timeline-badge bg-warning"><i class="fas fa-users"></i></span>
            <div class="timeline-content">
                <h6>Committee Stage</h6>
                <p class="small text-muted mb-1">Detailed examination and amendments in committee.</p>
                <div class="mb-2">
                    <span class="badge bg-warning text-dark">In Progress</span>
                    <small class="text-muted ms-2">Expected completion: 15 Aug 2025</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-warning"><i class="fas fa-pen"></i> Submit Amendment</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-clipboard-list"></i> View Amendments</button>
                </div>
            </div>
        </div>

        <!-- Stage 3: Public Participation -->
        <div class="timeline-item">
            <span class="timeline-badge bg-info"><i class="fas fa-people-arrows"></i></span>
            <div class="timeline-content">
                <h6>Public Participation</h6>
                <p class="small text-muted mb-1">Citizens and stakeholders invited to submit feedback on the bill.</p>
                <div class="mb-2">
                    <span class="badge bg-info text-dark">Open</span>
                    <small class="text-muted ms-2">Closes: 20 Aug 2025</small>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-info"><i class="fas fa-upload"></i> Submit Feedback</button>
                    <button class="btn btn-sm btn-outline-primary"><i class="fas fa-file-alt"></i> View Submissions</button>
                </div>
            </div>
        </div>

        <!-- Stage 4: Second Reading -->
        <div class="timeline-item">
            <span class="timeline-badge bg-secondary"><i class="fas fa-scroll"></i></span>
            <div class="timeline-content">
                <h6>Second Reading</h6>
                <p class="small text-muted mb-1">Debate on the general principles of the bill.</p>
                <div class="mb-2">
                    <span class="badge bg-secondary">Pending</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-eye"></i> View Schedule</button>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-bell"></i> Send Reminder</button>
                </div>
            </div>
        </div>

        <!-- Stage 5: Third Reading -->
        <div class="timeline-item">
            <span class="timeline-badge bg-secondary"><i class="fas fa-check-double"></i></span>
            <div class="timeline-content">
                <h6>Third Reading</h6>
                <p class="small text-muted mb-1">Final consideration before passing into law.</p>
                <div class="mb-2">
                    <span class="badge bg-secondary">Pending</span>
                </div>
                <div>
                    <button class="btn btn-sm btn-outline-success"><i class="fas fa-gavel"></i> Record Vote</button>
                    <button class="btn btn-sm btn-outline-secondary"><i class="fas fa-history"></i> View Debate</button>
                </div>
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

<!-- Font Awesome for icons -->
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
