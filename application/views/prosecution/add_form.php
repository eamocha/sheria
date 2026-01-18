
<div class="container mt-4">
    <h3>Add / Edit Prosecution Case</h3>
    <form id="prosecution-case-form" method="post">

        <div class="form-row">
            <div class="form-group col-md-4">
                <label>Financial Year</label>
                <select class="form-control" name="financial_year">
                    <option>2025</option>
                    <option>2024</option>
                </select>
            </div>
            <div class="form-group col-md-4">
                <label>Origin of Complaint</label>
                <input type="text" class="form-control" name="origin" placeholder="E.g. Licensing Department">
            </div>
            <div class="form-group col-md-4">
                <label>Approval Date</label>
                <input type="date" class="form-control" name="approval_date">
            </div>
        </div>

        <!-- Accused Person(s) -->
        <div class="form-group">
            <label>Accused Person(s)</label>
            <div id="accused-list">
                <input type="text" name="accused[]" class="form-control mb-2" placeholder="Enter accused name">
            </div>
            <button type="button" class="btn btn-sm btn-secondary" onclick="addAccused()">Add Another</button>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Arrest Location</label>
                <input type="text" class="form-control" name="arrest_location">
            </div>
            <div class="form-group col-md-6">
                <label>Arrest Date</label>
                <input type="date" class="form-control" name="arrest_date">
            </div>
        </div>

        <div class="form-group">
            <label>Case Reference No (Police/Court)</label>
            <input type="text" class="form-control" name="reference_no" placeholder="E.g. CF 123/2025">
        </div>

        <div class="form-group">
            <label>Brief of Case</label>
            <textarea class="form-control" name="case_brief" rows="3"></textarea>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Offence Type</label>
                <select name="offence_type" class="form-control">
                    <option>SIM Card Fraud</option>
                    <option>Telecom Offence</option>
                    <option>Broadcast Violation</option>
                    <option>Consumer Protection</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label>Case Status</label>
                <select name="status" class="form-control">
                    <option>PBC</option>
                    <option>PUI</option>
                    <option>PAKA</option>
                    <option>Finalized</option>
                    <option>Withdrawn</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Next Mention/Hearing Date</label>
                <input type="date" class="form-control" name="next_hearing_date">
            </div>
            <div class="form-group col-md-6">
                <label>Investigating Officer</label>
                <input type="text" name="investigating_officer" class="form-control" placeholder="e.g., Sgt. Kiplangat">
            </div>
        </div>

        <div class="form-group">
            <label>Remarks</label>
            <textarea name="remarks" class="form-control" rows="2"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Save Case</button>
        <a href="master_register" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script>
    function addAccused() {
        let input = document.createElement("input");
        input.type = "text";
        input.name = "accused[]";
        input.className = "form-control mb-2";
        input.placeholder = "Enter accused name";
        document.getElementById("accused-list").appendChild(input);
    }
</script>