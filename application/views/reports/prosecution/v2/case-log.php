<div class="container">
    <h2>Prosecution Reports (Quarterly/Annual)</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="prosecutionReportForm">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="reportPeriodType">Period Type</label>
                        <select id="reportPeriodType" class="form-control" name="reportPeriodType" required>
                            <option value="">Choose...</option>
                            <option value="quarterly">Quarterly</option>
                            <option value="annual">Annual</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="quarterlyOptions">
                        <label for="reportQuarter">Quarter</label>
                        <select id="reportQuarter" class="form-control" name="reportQuarter">
                            <option value="">Choose...</option>
                            <option value="1">Quarter 1</option>
                            <option value="2">Quarter 2</option>
                            <option value="3">Quarter 3</option>
                            <option value="4">Quarter 4</option>
                        </select>
                    </div>
                    <div class="form-group col-md-6" id="annualOptions">
                        <label for="reportYear">Year</label>
                        <select id="reportYear" class="form-control" name="reportYear">
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
            <div class="table-responsive">
                <table class="table table-bordered" id="prosecutionReportTable">
                    <thead>
                    <tr>
                        <th>Period</th>
                        <th>Total Cases Charged</th>
                        <th>Convictions</th>
                        <th>Acquittals</th>
                        <th>Withdrawals</th>
                        <th>Pending Cases</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-sm btn-success mt-2" id="exportProsecutionReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Toggle visibility of quarter/annual options
        $('#reportPeriodType').change(function() {
            $('#quarterlyOptions').toggle($(this).val() === 'quarterly');
            $('#annualOptions').toggle($(this).val() === 'annual');
        }).change(); // Trigger on load

        // Populate Year Dropdown (Example)
        function populateYearDropdown() {
            let currentYear = new Date().getFullYear();
            let startYear = currentYear - 10;
            let yearSelect = $('#reportYear');
            for (let i = startYear; i <= currentYear; i++) {
                yearSelect.append(`<option value="${i}">${i}</option>`);
            }
        }
        populateYearDropdown();

        $('#prosecutionReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            // AJAX call to your CodeIgniter backend
            const reportData = [
                { period: "Q1 2025", charged: 50, convictions: 30, acquittals: 10, withdrawals: 5, pending: 5 }
            ];
            populateProsecutionReportTable(reportData);
        });

        function populateProsecutionReportTable(data) {
            let tableBody = $('#prosecutionReportTable tbody');
            tableBody.empty();
            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td>${item.period}</td>
            <td>${item.charged}</td>
            <td>${item.convictions}</td>
            <td>${item.acquittals}</td>
            <td>${item.withdrawals}</td>
            <td>${item.pending}</td>
          </tr>
        `);
            });
        }

        $('#exportProsecutionReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
        });
    });
</script>