<div class="container">
    <h2>Complaints/Inquiry Report</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="complaintsReportForm">
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
                <div class="form-group">
                    <label for="originFilter">Origin of Matter</label>
                    <select id="originFilter" class="form-control" name="originFilter">
                        <option value="">All</option>
                        <option value="public">Public</option>
                        <option value="surveillance">Surveillance</option>
                        <option value="request">Request for Enforcement</option>
                    </select>
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
                <table class="table table-bordered" id="complaintsReportTable">
                    <thead>
                    <tr>
                        <th>S/NO.</th>
                        <th>Date of Complaint</th>
                        <th>Origin of Matter</th>
                        <th>Nature of Case/Inquiry</th>
                        <th>Complainant Name</th>
                        <th>Accused Name(s)</th>
                        <th>Investigating Officer</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-sm btn-success mt-2" id="exportComplaintsReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#complaintsReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            // AJAX call to your CodeIgniter backend to fetch report data
            // Example dummy data:
            const reportData = [
                { sno: 1, date: "2025-04-01", origin: "Public", nature: "Illegal FM Broadcast", complainant: "Concerned Citizen", accused: "XYZ Radio", officer: "Officer Alpha" },
                { sno: 2, date: "2025-04-05", origin: "Surveillance", nature: "Unlicensed Telecom Equipment", complainant: "", accused: "ABC Ltd", officer: "Officer Beta" }
            ];
            populateComplaintsReportTable(reportData);
        });

        function populateComplaintsReportTable(data) {
            let tableBody = $('#complaintsReportTable tbody');
            tableBody.empty();
            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.sno}</td>
            <td>${item.date}</td>
            <td>${item.origin}</td>
            <td>${item.nature}</td>
            <td>${item.complainant}</td>
            <td>${item.accused}</td>
            <td>${item.officer}</td>
          </tr>
        `);
            });
        }

        $('#exportComplaintsReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
        });
    });
</script>