<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Workflow Demo</title>
    <!-- Bootstrap CSS for styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
        }
        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
        .signature-code {
            word-wrap: break-word;
            font-family: monospace;
            font-size: 0.875rem;
            background-color: #fff;
            border: 1px solid #e9ecef;
            padding: 0.75rem;
            border-radius: 0.5rem;
        }
        .list-group-item.audited {
            background-color: #e9ecef;
        }
        textarea.form-control {
            resize: none;
            height: 200px;
        }
        /* Custom styling for the timeline */
        .timeline {
            position: relative;
            padding-left: 2rem;
        }
        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 3px;
            height: 100%;
            background-color: #dee2e6;
            border-radius: 1.5px;
        }
        .timeline-item {
            position: relative;
            padding-bottom: 2rem;
            display: flex;
            align-items: center;
        }
        .timeline-item-icon {
            position: absolute;
            left: -1rem;
            top: 0;
            z-index: 1;
            width: 2rem;
            height: 2rem;
            border-radius: 50%;
            background-color: #dee2e6;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 3px solid #f0f2f5;
            transition: background-color 0.3s ease;
        }
        .timeline-item.active .timeline-item-icon {
            background-color: #0d6efd;
        }
        .timeline-content {
            margin-left: 2rem;
            flex-grow: 1;
        }
        .timeline-button {
            margin-left: auto;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-custom p-4 p-md-5">
                <div class="card-body">
                    <h1 class="card-title text-center text-dark fw-bold mb-4">
                        <i class="fas fa-project-diagram text-primary me-2"></i> Document Workflow Demo
                    </h1>

                    <p class="text-center text-muted mb-5">
                        This demo showcases a simple digital workflow for drafting, reviewing, and signing a document.
                    </p>

                    <!-- Timeline for the simplified workflow -->
                    <h3 class="h5 text-dark fw-bold mb-3">Workflow Steps</h3>
                    <div class="timeline mb-5">
                        <div class="timeline-item active" id="step-draft">
                            <div class="timeline-item-icon"><i class="fas fa-pencil-alt"></i></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold">1. Draft & Review</h6>
                                <p class="text-muted mb-0">Edit the document content below. This is the drafting phase where changes are made freely.</p>
                            </div>
                        </div>
                        <div class="timeline-item" id="step-finalize">
                            <div class="timeline-item-icon"><i class="fas fa-check-double"></i></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold">2. Finalize Document</h6>
                                <p class="text-muted mb-0">After drafting, finalize the document. It will be locked and ready for the next stage.</p>
                            </div>
                        </div>
                        <div class="timeline-item" id="step-sign">
                            <div class="timeline-item-icon"><i class="fas fa-signature"></i></div>
                            <div class="timeline-content">
                                <h6 class="fw-bold">3. Sign Document</h6>
                                <p class="text-muted mb-0">The document is ready for a final signature. Clicking the sign button completes the workflow.</p>
                            </div>
                        </div>
                    </div>

                    <!-- The "Document" to be prepared -->
                    <h3 class="h5 text-dark fw-bold mb-2" id="document-header">1. Draft & Review Document</h3>
                    <div class="border border-secondary-subtle rounded-3 p-4 mb-4">
                            <textarea id="document-content" class="form-control" aria-label="Document content">This is the official document for review and preparation. You can make any necessary edits or add details before finalizing it.

This workflow is fully digital, requiring no printing or scanning.
                            </textarea>
                    </div>

                    <div class="d-grid gap-3 mb-4">
                        <button id="next-step-button" class="btn btn-primary w-100 fw-bold py-3">
                            <i class="fas fa-arrow-right me-2"></i> Finalize Document
                        </button>
                    </div>

                    <!-- Status and audit log display area -->
                    <div class="mt-4 pt-4 border-top border-secondary-subtle">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h3 class="h5 text-dark fw-bold mb-3">Document Status</h3>
                                <div id="status-display" class="alert alert-light text-body-secondary p-4">
                                    <p class="fw-bold mb-1 d-flex align-items-center">
                                        <i class="fas fa-clock me-2 text-primary"></i> Current Status:
                                    </p>
                                    <code id="status-value" class="signature-code d-block mt-2">Drafting</code>
                                </div>
                                <div id="result-message" class="alert mt-3 d-none" role="alert"></div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h5 text-dark fw-bold mb-3">Audit Log</h3>
                                <div id="audit-log" class="list-group">
                                    <div class="list-group-item list-group-item-action audited">
                                        <p class="mb-1">Demo loaded.</p>
                                        <small class="text-muted">System | Just now</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Get DOM elements
    const documentContent = document.getElementById('document-content');
    const nextStepButton = document.getElementById('next-step-button');
    const statusDisplay = document.getElementById('status-display');
    const statusValue = document.getElementById('status-value');
    const resultMessage = document.getElementById('result-message');
    const auditLog = document.getElementById('audit-log');
    const documentHeader = document.getElementById('document-header');

    // Timeline items
    const stepDraft = document.getElementById('step-draft');
    const stepFinalize = document.getElementById('step-finalize');
    const stepSign = document.getElementById('step-sign');

    let currentStep = 0; // 0: Draft, 1: Finalize, 2: Sign

    function updateUI() {
        // Reset all timeline items
        stepDraft.classList.remove('active');
        stepFinalize.classList.remove('active');
        stepSign.classList.remove('active');

        // Update UI based on current step
        switch(currentStep) {
            case 0:
                // Draft & Review
                stepDraft.classList.add('active');
                documentContent.readOnly = false;
                nextStepButton.textContent = "Finalize Document";
                nextStepButton.innerHTML = `<i class="fas fa-arrow-right me-2"></i> Finalize Document`;
                documentHeader.textContent = "1. Draft & Review Document";
                statusValue.textContent = "Drafting";
                break;
            case 1:
                // Finalize Document
                stepDraft.classList.add('active');
                stepFinalize.classList.add('active');
                documentContent.readOnly = true;
                nextStepButton.textContent = "Send for Signing";
                nextStepButton.innerHTML = `<i class="fas fa-signature me-2"></i> Send for Signing`;
                documentHeader.textContent = "2. Finalize Document";
                statusValue.textContent = "Finalized and ready for signature";
                break;
            case 2:
                // Sign Document
                stepDraft.classList.add('active');
                stepFinalize.classList.add('active');
                stepSign.classList.add('active');
                documentContent.readOnly = true;
                nextStepButton.textContent = "Workflow Complete";
                nextStepButton.innerHTML = `<i class="fas fa-check-circle me-2"></i> Workflow Complete`;
                nextStepButton.disabled = true;
                documentHeader.textContent = "3. Sign Document";
                statusValue.textContent = "Signed";
                break;
        }
    }

    function handleNextStep() {
        currentStep++;
        if (currentStep > 2) {
            currentStep = 2; // Clamp at the final step
        }

        switch(currentStep) {
            case 1:
                displayMessage('Document finalized. You can no longer edit the content.', 'alert-success');
                addToAuditLog('Document finalized and sent for review.', 'User');
                break;
            case 2:
                displayMessage('Document successfully signed. The workflow is complete.', 'alert-success');
                addToAuditLog('Document signed and archived.', 'User');
                break;
        }
        updateUI();
    }

    // Helper function to display messages in an alert box.
    function displayMessage(message, alertClass) {
        resultMessage.textContent = message;
        resultMessage.className = `alert mt-3 ${alertClass}`;
        resultMessage.classList.remove('d-none');
    }

    // Helper function to add entries to the audit log.
    function addToAuditLog(action, user) {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const newEntry = document.createElement('div');
        newEntry.className = 'list-group-item list-group-item-action audited';
        newEntry.innerHTML = `<p class="mb-1">${action}</p><small class="text-muted">${user} | ${timeString}</small>`;
        auditLog.prepend(newEntry);
    }

    // Event Listeners for buttons
    nextStepButton.addEventListener('click', handleNextStep);

    // Initialize the UI on page load
    updateUI();
</script>

</body>
</html>
