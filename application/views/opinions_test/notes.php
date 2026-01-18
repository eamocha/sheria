<div class="container-fluid mt-4">
    <h3 class="mb-3 fw-bold">Opinion with Private Notes</h3>
    <p class="mb-4">Below is the legal opinion text. You can add private notes for your reference and highlight key sections of the document.</p>

    <!-- Opinion Text Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">Opinion Document</h5>
            <p>
                The Constitution guarantees fundamental freedoms, including the right to fair labor practices.
                <mark>Any law or policy that undermines these protections may be deemed unconstitutional.</mark>
            </p>
            <p>
                Previous rulings have established that collective bargaining agreements must be respected,
                and employees are entitled to seek redress in cases of unlawful dismissal.
            </p>
        </div>
    </div>

    <!-- Private Notes Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="fw-bold">My Private Notes</h5>
            <form>
                <div class="mb-3">
                    <textarea class="form-control" rows="4" placeholder="Write your private notes here..."></textarea>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="importantNote">
                    <label class="form-check-label fw-bold" for="importantNote">Mark as Important</label>
                </div>
                <button type="submit" class="btn btn-outline-secondary btn-sm">Save Note</button>
            </form>
        </div>
    </div>

    <!-- Saved Notes Display -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="fw-bold">Saved Notes</h5>
            <ul class="list-group">
                <li class="list-group-item">
                    <span class="fw-bold">[Important]</span> Consider referencing Opinion #245 in future cases.
                </li>
                <li class="list-group-item">
                    Review Section 2.1 on collective bargaining before next submission.
                </li>
            </ul>
        </div>
    </div>
</div>
