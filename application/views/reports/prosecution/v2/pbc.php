<div class="container">
    <h2>Current Cases Pending Before Court (Monthly Status)</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="pendingCourtReportForm">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="reportMonth">Month</label>
                        <select id="reportMonth" class="form-control" name="reportMonth" required>
                            <option value="">Choose...</option>
                            <option value="1">January</option>
                            <option value="2">February</option>
                            <option value="12">December</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reportYear">Year</label>
                        <select id="reportYear" class="form-control" name="reportYear" required>
                            <option value="">Choose...</option>
                        </select>
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
            <table class="table table-bordered" id="pendingCourtReportTable">
                <thead>
                <tr>
                    <th>S/NO.</th>
                    <th>CASE REFERENCE</th>
                    <th>ACCUSED</th>
                    <th>OFFENCE</th>
                    <th>COURT</th>
                    <th>STATUS</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <button class="btn btn-sm btn-success mt-2" id="exportPendingCourtReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Populate Year Dropdown (Example)
        function populateYearDropdown() {
            let currentYear = new Date().getFullYear();
            let startYear = currentYear - 10; // Show past 10 years
            let yearSelect = $('#reportYear');

            for (let i = startYear; i <= currentYear + 5; i++) { // Show 5 years into the future
                yearSelect.append(`<option value="${i}">${i}</option>`);
            }
            yearSelect.val(currentYear); // Select current year by default
        }

        populateYearDropdown();

        // Generate Report (Example - Adapt to your CodeIgniter backend)
        $('#pendingCourtReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize(); // Get form data

            // $.ajax({
            //   url: '/your-codeigniter-controller/generate-pending-court-report', // Replace with your CI URL
            //   method: 'POST',
            //   data: formData,
            //   dataType: 'json',
            //   success: function(data) {
            //     populatePendingCourtReportTable(data);
            //   },
            //   error: function(xhr, status, error) {
            //     console.error('Error generating report:', error);
            //     alert('Failed to generate report. Please try again.');
            //   }
            // });
            // Dummy data for testing
            const reportData = [
                { sno: 1, caseReference: "CR627/181/2022", accused: "AUSTIN ACTION JOHN", offence: "Establishing FM Station without a valid license", court: "Kisumu Law Courts", status: "Next Hearing 05/02/2025" },
                { sno: 2, caseReference: "CR628/182/2022", accused: "JANE DOE", offence: "Operating a radio station without a license", court: "Nairobi Law Courts", status: "Pending Judgment" },
                { sno: 3, caseReference: "CR629/183/2022", accused: "PETER SMITH", offence: "Interference with broadcasting signals", court: "Mombasa Law Courts", status: "Trial in Progress" }
            ];
            populatePendingCourtReportTable(reportData);
        });

        // Populate Report Table
        function populatePendingCourtReportTable(data) {
            let tableBody = $('#pendingCourtReportTable tbody');
            tableBody.empty();
            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.sno}</td>
            <td>${item.caseReference}</td>
            <td>${item.accused}</td>
            <td>${item.offence}</td>
            <td>${item.court}</td>
            <td>${item.status}</td>
          </tr>
        `);
            });
        }

        // Export to CSV (Example - Adapt to your server-side export)
        $('#exportPendingCourtReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
            // window.location.href = '/your-codeigniter-controller/export-pending-court-report';
        });
    });
</script>