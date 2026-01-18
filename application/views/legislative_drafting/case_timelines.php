<div class="container my-4">
    <h2 class="mb-4 text-dark font-weight-bold">
        <i class="fas fa-history mr-2"></i> Case Timeline & Activity Log
    </h2>

    <div class="row mb-3 align-items-center">
        <div class="col-md-6">
            <h5 class="mb-0 text-dark">Case Reference: <span class="text-primary font-weight-bold">HCCC E056/2023</span></h5>
        </div>
        <div class="col-md-6 text-md-right">
            <button class="btn btn-primary">
                <i class="fas fa-plus mr-1"></i> Add New Entry
            </button>
        </div>
    </div>

    <div class="timeline-v2">
        <div class="timeline-item-v2">
            <div class="timeline-icon-v2 bg-success">
                <i class="fas fa-upload text-white"></i>
            </div>
            <div class="timeline-content-v2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="timeline-title-v2 mb-1">Document Uploaded: Final Judgment Order</h6>
                    <small class="text-muted text-nowrap">Just now</small>
                </div>
                <p class="mb-1">The final judgment order for the case has been uploaded and is available in the documents section.</p>
                <small class="text-muted">By: <span class="font-weight-bold">Jane Smith</span></small>
            </div>
        </div>

        <div class="timeline-item-v2">
            <div class="timeline-icon-v2 bg-info">
                <i class="fas fa-file-alt text-white"></i>
            </div>
            <div class="timeline-content-v2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="timeline-title-v2 mb-1">Drafting of Appeal Brief Initiated</h6>
                    <small class="text-muted text-nowrap">30 minutes ago</small>
                </div>
                <p class="mb-1">Work on the initial draft of the appeal brief has commenced. Key arguments are being outlined.</p>
                <small class="text-muted">By: <span class="font-weight-bold">Jane Smith</span></small>
            </div>
        </div>

        <div class="timeline-item-v2">
            <div class="timeline-icon-v2 bg-warning">
                <i class="fas fa-envelope text-white"></i>
            </div>
            <div class="timeline-content-v2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="timeline-title-v2 mb-1">Communication Logged: Email to Client</h6>
                    <small class="text-muted text-nowrap">1 hour ago</small>
                </div>
                <p class="mb-1">An email was sent to the client to request additional documents and to provide a status update on the case.</p>
                <small class="text-muted">By: <span class="font-weight-bold">John Doe</span></small>
            </div>
        </div>

        <div class="timeline-item-v2">
            <div class="timeline-icon-v2 bg-secondary">
                <i class="fas fa-calendar-alt text-white"></i>
            </div>
            <div class="timeline-content-v2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="timeline-title-v2 mb-1">Court Hearing Scheduled</h6>
                    <small class="text-muted text-nowrap">Yesterday at 2:30 PM</small>
                </div>
                <p class="mb-1">The next court hearing has been scheduled for <strong>March 15, 2025</strong>, at 10:00 AM.</p>
                <small class="text-muted">By: <span class="font-weight-bold">System</span></small>
            </div>
        </div>

        <div class="timeline-item-v2">
            <div class="timeline-icon-v2 bg-info">
                <i class="fas fa-file-signature text-white"></i>
            </div>
            <div class="timeline-content-v2">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="timeline-title-v2 mb-1">Pleadings Filed</h6>
                    <small class="text-muted text-nowrap">2 days ago</small>
                </div>
                <p class="mb-1">The initial pleadings have been filed with the court and copies served to the opposing counsel.</p>
                <small class="text-muted">By: <span class="font-weight-bold">Jane Smith</span></small>
            </div>
        </div>
    </div>
</div>

<style>
    /* New Timeline CSS */
    .timeline-v2 {
        position: relative;
        padding-left: 20px;
        border-left: 2px solid #e9ecef;
    }

    .timeline-item-v2 {
        position: relative;
        margin-bottom: 25px;
    }

    .timeline-icon-v2 {
        position: absolute;
        left: -32px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .timeline-content-v2 {
        padding: 10px 15px;
        border-radius: .25rem;
        background-color: #f8f9fa;
        box-shadow: 0 1px 3px