
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .card-custom {
            border-radius: 1rem;
            box-shadow: 0 4px 25px rgba(0, 0, 0, 0.1);
        }
        .alert-custom {
            display: none;
            text-align: center;
            font-weight: 600;
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
        .btn-custom {
            transition: all 0.2s ease-in-out;
        }
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
    </style>


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card card-custom p-4 p-md-5">
                <div class="card-body">
                    <h1 class="card-title text-center text-dark fw-bold mb-4">
                        <i class="fas fa-lock text-secondary me-2"></i> Secure E-Signature
                    </h1>

                    <!-- The "Document" to be signed -->
                    <div class="border border-secondary-subtle rounded-3 p-4 mb-4">
                        <p id="document-content" class="text-body-secondary lead mb-0">
                            This is the original document content that will be electronically signed. The signature will be a unique cryptographic hash of this exact text.
                        </p>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <button id="sign-button" class="btn btn-primary w-100 btn-custom fw-bold py-3 d-flex align-items-center justify-content-center">
                                <i class="fas fa-signature me-2"></i> Sign Document
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button id="tamper-button" class="btn btn-warning w-100 btn-custom fw-bold py-3 d-flex align-items-center justify-content-center" disabled>
                                <i class="fas fa-exclamation-triangle me-2"></i> Simulate Tampering
                            </button>
                        </div>
                    </div>

                    <!-- Signature and status display area -->
                    <div class="mt-4 pt-4 border-top border-secondary-subtle">
                        <h3 class="h4 text-dark fw-bold mb-3">Verification Status</h3>
                        <div id="signature-display" class="alert alert-light text-body-secondary p-4 d-none">
                            <p class="fw-bold mb-1 d-flex align-items-center">
                                <i class="fas fa-fingerprint me-2 text-primary"></i> Document Signature:
                            </p>
                            <code id="signature-value" class="signature-code d-block mt-2"></code>
                        </div>

                        <div id="result-message" class="alert alert-custom mt-3" role="alert"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Use a unique ID for the stored signature
    const signatureKey = 'eSignatureDemoSignature';

    // Get DOM elements
    const documentContent = document.getElementById('document-content');
    const signButton = document.getElementById('sign-button');
    const tamperButton = document.getElementById('tamper-button');
    const signatureDisplay = document.getElementById('signature-display');
    const signatureValue = document.getElementById('signature-value');
    const resultMessage = document.getElementById('result-message');

    // Initial state
    let originalDocumentText = documentContent.textContent;
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

    // Simulates the signing process by hashing the document content.
    async function signDocument() {
        if (documentSigned) {
            displayMessage('Document is already signed!', 'alert-warning');
            return;
        }

        const signature = await hashString(documentContent.textContent);

        // Store the signature in the browser's session storage.
        // In a real application, this would be stored securely on a server.
        sessionStorage.setItem(signatureKey, signature);
        documentSigned = true;

        // Update the UI to show the signature
        signatureValue.textContent = signature;
        signatureDisplay.classList.remove('d-none');
        signButton.disabled = true;
        tamperButton.disabled = false;
        displayMessage('Document has been securely signed! The signature is stored.', 'alert-success');
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
            return true;
        } else {
            displayMessage('Verification Failed: The document has been tampered with!', 'alert-danger');
            return false;
        }
    }

    // Simulates a user making a change to the document content.
    function simulateTampering() {
        if (!documentSigned) {
            displayMessage('Please sign the document before simulating tampering.', 'alert-warning');
            return;
        }

        const currentText = documentContent.textContent;
        // Change a small part of the text to simulate tampering
        if (currentText.includes('exact text')) {
            documentContent.textContent = currentText.replace('exact text', 'modified text');
            displayMessage('Document content has been tampered with!', 'alert-danger');
        } else {
            documentContent.textContent = originalDocumentText;
            displayMessage('Restored to original document. Click "Simulate Tampering" again.', 'alert-info');
        }

        // Immediately verify the document to show the result
        verifyDocument();
    }

    // Displays a message to the user using Bootstrap alerts.
    function displayMessage(message, alertClass) {
        resultMessage.textContent = message;
        resultMessage.className = `alert alert-custom ${alertClass} d-block`;
    }

    // Event Listeners
    signButton.addEventListener('click', signDocument);
    tamperButton.addEventListener('click', simulateTampering);
</script>

</body>
</html>
