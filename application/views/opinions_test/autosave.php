<div class="container-fluid mt-4">
    <h3 class="mb-3 font-weight-bold">Opinion Drafting with Auto-Save</h3>

    <div class="card shadow-sm rounded-2xl mb-4">
        <div class="card-body">
            <form id="opinionForm">
                <div class="form-group">
                    <label for="opinionTitle" class="font-weight-bold">Opinion Title</label>
                    <input type="text" class="form-control" id="opinionTitle" name="opinionTitle" placeholder="Enter opinion title" required>
                </div>

                <div class="form-group">
                    <label for="opinionContent" class="font-weight-bold">Opinion Content</label>
                    <textarea class="form-control" id="opinionContent" name="opinionContent" rows="8" placeholder="Start drafting here..."></textarea>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted" id="autosaveStatus">Not yet saved</small>
                    <button type="submit" class="btn btn-success">Submit Final Opinion</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let autosaveTimer;
    const statusEl = document.getElementById('autosaveStatus');

    function autoSaveOpinion() {
        clearTimeout(autosaveTimer);
        autosaveTimer = setTimeout(() => {
            const title = document.getElementById('opinionTitle').value;
            const content = document.getElementById('opinionContent').value;

            if(title.trim() || content.trim()) {
                // simulate ajax save
                fetch('/opinions/autosave', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ title, content })
                })
                    .then(() => {
                        const now = new Date().toLocaleTimeString();
                        statusEl.innerText = "Auto-saved at " + now;
                    })
                    .catch(() => {
                        statusEl.innerText = "⚠️ Auto-save failed. Will retry...";
                    });
            }
        }, 2000); // save 2s after typing stops
    }

    document.getElementById('opinionTitle').addEventListener('input', autoSaveOpinion);
    document.getElementById('opinionContent').addEventListener('input', autoSaveOpinion);

    document.getElementById('opinionForm').addEventListener('submit', function(e){
        e.preventDefault();
        // Final submit action
        statusEl.innerText = "✅ Opinion submitted successfully!";
    });
</script>
