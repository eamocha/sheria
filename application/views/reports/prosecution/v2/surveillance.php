<div class="container">
    <h2>Surveillance/Detection Exercises Report</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="surveillanceReportForm">
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
                <table class="table table-bordered" id="surveillanceReportTable">
                    <thead>
                    <tr>
                        <th>S/NO.</th>
                        <th>Date of Exercise</th>
                        <th>Location</th>
                        <th>Objective</th>
                        <th>Findings</th>
                        <th>Officers Involved</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-sm btn-success mt-2" id="exportSurveillanceReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#surveillanceReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            // AJAX call to your CodeIgniter backend
            const reportData = [
                { sno: 1, date: "2025-04-10", location: "Market Area X", objective: "Detect unlicensed vendors", findings: "5 unlicensed vendors identified", officers: "Officer Charlie, Officer Delta" }
            ];
            populateSurveillanceReportTable(reportData);
        });

        function populateSurveillanceReportTable(data) {
            let tableBody = $('#surveillanceReportTable tbody');
            tableBody.empty();
            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.sno}</td>
            <td>${item.date}</td>
            <td>${item.location}</td>
            <td>${item.objective}</td>
            <td>${item.findings}</td>
            <td>${item.officers}</td>
          </tr>
        `);
            });
        }

        $('#exportSurveillanceReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
        });
    });
</script>