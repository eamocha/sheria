<div class="container-fluid mt-4">
    <h3 class="mb-3">Add Legal Advisory Opinion</h3>
    <p class="text-muted">Fill in details and assign multiple tags to categorize the opinion.</p>

    <form method="post" action="<?php echo site_url('opinions/save'); ?>">
        <!-- Opinion Date -->
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Date</label>
            <div class="col-sm-10">
                <input type="date" name="date" class="form-control" required>
            </div>
        </div>

        <!-- Agency -->
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Agency</label>
            <div class="col-sm-10">
                <input type="text" name="agency" class="form-control" placeholder="e.g. Attorney General’s Office" required>
            </div>
        </div>

        <!-- Case Type -->
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Case Type</label>
            <div class="col-sm-10">
                <input type="text" name="case_type" class="form-control" placeholder="e.g. Constitutional Law" required>
            </div>
        </div>

        <!-- Tags -->
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Tags</label>
            <div class="col-sm-10">
                <select name="tags[]" class="form-control chosen-select" multiple data-placeholder="Select or type tags...">
                    <option value="Constitutional Law">Constitutional Law</option>
                    <option value="Labor Dispute">Labor Dispute</option>
                    <option value="Public Procurement">Public Procurement</option>
                    <option value="Data Protection">Data Protection</option>
                    <option value="Administrative Law">Administrative Law</option>
                </select>
                <small class="form-text text-muted">You can select multiple tags to categorize this opinion.</small>
            </div>
        </div>

        <!-- Summary -->
        <div class="form-group row">
            <label class="col-sm-2 col-form-label">Summary</label>
            <div class="col-sm-10">
                <textarea name="summary" class="form-control" rows="4" placeholder="Brief summary of the opinion..." required></textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="form-group row">
            <div class="col-sm-10 offset-sm-2">
                <button type="submit" class="btn btn-primary">Save Opinion</button>
            </div>
        </div>
    </form>
</div>

<!-- Chosen init -->
<script>
    $(function() {
        $(".chosen-select").chosen({
            width: "100%",
            no_results_text: "No tag found, type to add: "
        });
    });
</script>
