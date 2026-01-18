<div class="container mt-4">
    <h3 class="mb-3 font-weight-bold">We Value Your Feedback</h3>

    <div class="card shadow-sm rounded-2xl">
        <div class="card-body">
            <form id="feedbackForm">
                <div class="form-group mb-3">
                    <label for="feedbackType" class="font-weight-bold">Feedback Type</label>
                    <select class="form-control" id="feedbackType" name="feedbackType" required>
                        <option value="">-- Select --</option>
                        <option value="issue">Report an Issue</option>
                        <option value="feature">Suggest a Feature</option>
                        <option value="change">Request a Change</option>
                    </select>
                </div>

                <div class="form-group mb-3">
                    <label for="feedbackMessage" class="font-weight-bold">Your Feedback</label>
                    <textarea class="form-control" id="feedbackMessage" name="feedbackMessage" rows="5" placeholder="Please describe your feedback here..." required></textarea>
                </div>

                <div class="form-group mb-3">
                    <label for="feedbackEmail" class="font-weight-bold">Your Email (optional)</label>
                    <input type="email" class="form-control" id="feedbackEmail" name="feedbackEmail" placeholder="Enter email if you want a response">
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted" id="feedbackStatus"></small>
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('feedbackForm').addEventListener('submit', function(e){
        e.preventDefault();

        const type = document.getElementById('feedbackType').value;
        const message = document.getElementById('feedbackMessage').value;
        const email = document.getElementById('feedbackEmail').value;

        const statusEl = document.getElementById('feedbackStatus');
        statusEl.innerText = "Submitting...";

        fetch('/feedback/submit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ type, message, email })
        })
            .then(() => {
                statusEl.innerText = "✅ Thank you! Your feedback has been submitted.";
                document.getElementById('feedbackForm').reset();
            })
            .catch(() => {
                statusEl.innerText = "⚠️ Failed to submit. Please try again.";
            });
    });
</script>
