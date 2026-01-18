<div class="container">
    <h2>Investigations and Enforcement Action Exercises Report</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="investigationsReportForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="fromDate">From Date</label>
                        <input type="date" class="form-control" id="fromDate" name="fromDate" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="toDate">To Date</label>
                        <input type="date" class="form-control" id="toDate" name="toDate" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Generate Report</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header bg-primary text-white">
            Report Data
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="investigationsReportTable">
                    <thead>
                    <tr>
                        <th>S/NO.</th>
                        <th>Case Reference</th>
                        <th>Date of Action</th>
                        <th>Type of Action (e.g., Arrest, Seizure)</th>
                        <th>Location</th>
                        <th>Details/Findings</th>
                        <th>Officers Involved</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-sm btn-success mt-2" id="exportInvestigationsReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#investigationsReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            // AJAX call to your CodeIgniter backend
            const reportData = [
                { sno: 1, caseRef: "INV-001", date: "2025-04-15", type: "Arrest", location: "Premises Y", details: "Suspect arrested for...", officers: "Officer Echo" }
            ];
            populateInvestigationsReportTable(reportData);
        });

        function populateInvestigationsReportTable(data) {
            let tableBody = $('#investigationsReportTable tbody');
            tableBody.empty();
            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.sno}</td>
            <td>${item.caseRef}</td>
            <td>${item.date}</td>
            <td>${item.type}</td>
            <td>${item.location}</td>
            <td>${item.details}</td>
            <td>${item.officers}</td>
          </tr>
        `);
            });
        }

        $('#exportInvestigationsReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
        });
    });
</script>