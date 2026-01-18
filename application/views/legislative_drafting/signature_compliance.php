<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bid Security & Compliance Demo</title>
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
    </style>
</head>
<body>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card card-custom p-4 p-md-5">
                <div class="card-body">
                    <h1 class="card-title text-center text-dark fw-bold mb-4">
                        <i class="fas fa-shield-alt text-primary me-2"></i> Security & Compliance
                    </h1>

                    <p class="text-center text-muted mb-5">
                        PKI-based signatures , tamper-evident seals, and audit logs protecting a document.
                    </p>

                    <!-- The "Document" to be signed -->
                    <div class="border border-secondary-subtle rounded-3 p-4 mb-4">
                        <p id="document-content" class="text-body-secondary lead mb-0">
                            This is the original, legally-binding document content that will be electronically signed. The signature ensures its integrity.
                        </p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <button id="sign-button" class="btn btn-primary w-100 fw-bold py-3">
                                <i class="fas fa-signature me-2"></i> Sign Document
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button id="tamper-button" class="btn btn-warning w-100 fw-bold py-3" disabled>
                                <i class="fas fa-exclamation-triangle me-2"></i> check Tampering
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button id="verify-button" class="btn btn-success w-100 fw-bold py-3" disabled>
                                <i class="fas fa-check-double me-2"></i> Verify Signature
                            </button>
                        </div>
                    </div>

                    <!-- Signature and status display area -->
                    <div class="mt-4 pt-4 border-top border-secondary-subtle">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <h3 class="h4 text-dark fw-bold mb-3">Signature & Status</h3>
                                <div id="signature-display" class="alert alert-light text-body-secondary p-4 d-none">
                                    <p class="fw-bold mb-1 d-flex align-items-center">
                                        <i class="fas fa-fingerprint me-2 text-primary"></i> Digital Signature:
                                    </p>
                                    <code id="signature-value" class="signature-code d-block mt-2"></code>
                                </div>
                                <div id="result-message" class="alert mt-3 d-none" role="alert"></div>
                            </div>
                            <div class="col-md-6">
                                <h3 class="h4 text-dark fw-bold mb-3">Audit Log</h3>
                                <div id="audit-log" class="list-group">
                                    <div class="list-group-item list-group-item-action audited">
                                        <p class="mb-1">Document loaded.</p>
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

<!-- MFA Modal -->
<div class="modal fade" id="mfaModal" tabindex="-1" aria-labelledby="mfaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mfaModalLabel">Multi-Factor Authentication (MFA)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Enter the 6-digit code sent to your mobile device to proceed with signing.</p>
                <div class="input-group mb-3">
                    <input type="text" id="mfa-code" class="form-control" placeholder="123456" aria-label="MFA Code">
                </div>
                <div id="mfa-message" class="alert alert-danger d-none">Incorrect code.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="mfa-verify-btn" class="btn btn-primary">Verify Code</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Use a unique ID for the stored signature
    const signatureKey = 'bidSecurityDemoSignature';
    const originalDocumentText = document.getElementById('document-content').textContent;

    // Get DOM elements
    const documentContent = document.getElementById('document-content');
    const signButton = document.getElementById('sign-button');
    const tamperButton = document.getElementById('tamper-button');
    const verifyButton = document.getElementById('verify-button');
    const signatureDisplay = document.getElementById('signature-display');
    const signatureValue = document.getElementById('signature-value');
    const resultMessage = document.getElementById('result-message');
    const auditLog = document.getElementById('audit-log');
    const mfaModal = new bootstrap.Modal(document.getElementById('mfaModal'));
    const mfaVerifyBtn = document.getElementById('mfa-verify-btn');
    const mfaCodeInput = document.getElementById('mfa-code');
    const mfaMessage = document.getElementById('mfa-message');

    // Initial state
    let documentSigned = false;

    // --- Cryptographic Hashing Logic (Simulating a digital signature) ---
    // This function creates a SHA-256 hash of a given string.
    async function hashString(str) {
        const encoder = new TextEncoder();
        const data = encoder.encode(str);
        const hashBuffer = await crypto.subtle.digest('SHA-256', data);
        const hashArray = Array.from(new Uint8Array(hashBuffer));
        const hashHex = hashArray.map(b => b.toString(16).padStart(2, '0')).join('');
        return hashHex;
    }

    // Simulates the MFA process.
    function showMfaModal() {
        mfaCodeInput.value = '';
        mfaMessage.classList.add('d-none');
        mfaModal.show();
    }

    async function verifyMfa() {
        const enteredCode = mfaCodeInput.value;
        // Hardcoded code for demo purposes
        if (enteredCode === '123456') {
            mfaModal.hide();
            await signDocument();
        } else {
            mfaMessage.classList.remove('d-none');
        }
    }

    // Simulates the signing process.
    async function signDocument() {
        if (documentSigned) {
            displayMessage('Document is already signed!', 'alert-warning');
            return;
        }

        const signature = await hashString(documentContent.textContent);

        // Store the signature in the browser's session storage.
        // This represents a secure, server-side storage in a real system.
        sessionStorage.setItem(signatureKey, signature);
        documentSigned = true;

        // Update the UI to show the signature and enable verification
        signatureValue.textContent = signature;
        signatureDisplay.classList.remove('d-none');
        signButton.disabled = true;
        tamperButton.disabled = false;
        verifyButton.disabled = false;
        displayMessage('Document has been securely signed!', 'alert-success');

        // Log the event
        addToAuditLog('Document signed successfully.', 'Jane Doe');
    }

    // Verifies the document by comparing the current hash with the stored one.
    async function verifyDocument() {
        const storedSignature = sessionStorage.getItem(signatureKey);
        if (!storedSignature) {
            displayMessage('No signature found. Please sign the document first.', 'alert-secondary');
            return;
        }

        const currentSignature = await hashString(documentContent.textContent);

        if (currentSignature === storedSignature) {
            displayMessage('Verification Successful: The document has not been tampered with.', 'alert-success');
            // Log the event
            addToAuditLog('Signature verified successfully.', 'System');
            return true;
        } else {
            displayMessage('Verification Failed: The document has been tampered with!', 'alert-danger');
            // Log the event
            addToAuditLog('Verification failed: Document integrity compromised.', 'System');
            return false;
        }
    }

    // Simulates a user making a change to the document content (tamper-evident seal).
    function simulateTampering() {
        if (!documentSigned) {
            displayMessage('Please sign the document before simulating tampering.', 'alert-warning');
            return;
        }

        const currentText = documentContent.textContent;
        // Change a small part of the text to simulate tampering
        if (currentText.includes('legally-binding')) {
            documentContent.textContent = currentText.replace('legally-binding', 'unofficial');
            displayMessage('Document content has been tampered with!', 'alert-danger');
            // Log the event
            addToAuditLog('Document content was tampered with.', 'Robert Kamau');
        } else {
            documentContent.textContent = originalDocumentText;
            displayMessage('Restored to original document.', 'alert-info');
            // Log the event
            addToAuditLog('Document content restored to original.', 'Robert Kamau');
        }

        // Re-verify the document to show the broken seal.
        verifyDocument();
    }

    // Displays a message to the user using Bootstrap alerts.
    function displayMessage(message, alertClass) {
        resultMessage.textContent = message;
        resultMessage.className = `alert mt-3 ${alertClass}`;
        resultMessage.classList.remove('d-none');
    }

    // Adds an entry to the live audit log.
    function addToAuditLog(action, user) {
        const now = new Date();
        const timeString = now.toLocaleTimeString();
        const newEntry = document.createElement('div');
        newEntry.className = 'list-group-item list-group-item-action audited';
        newEntry.innerHTML = `<p class="mb-1">${action}</p><small class="text-muted">${user} | ${timeString}</small>`;
        auditLog.prepend(newEntry);
    }

    // Event Listeners
    signButton.addEventListener('click', showMfaModal);
    mfaVerifyBtn.addEventListener('click', verifyMfa);
    tamperButton.addEventListener('click', simulateTampering);
    verifyButton.addEventListener('click', verifyDocument);
</script>

</body>
</html>
