<div class="container">
    <h2>Category of Cases and Statistics</h2>

    <div class="card">
        <div class="card-header bg-primary text-white">
            Report Filters
        </div>
        <div class="card-body">
            <form id="categoryCasesReportForm">
                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="reportQuarter">Quarter</label>
                        <select id="reportQuarter" class="form-control" name="reportQuarter" required>
                            <option value="">Choose...</option>
                            <option value="1">Quarter 1</option>
                            <option value="2">Quarter 2</option>
                            <option value="3">Quarter 3</option>
                            <option value="4">Quarter 4</option>
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="reportFYYear">FY Year</label>
                        <select id="reportFYYear" class="form-control" name="reportFYYear" required>
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
                <table class="table table-bordered" id="categoryCasesReportTable">
                    <thead>
                    <tr>
                        <th rowspan="2">Quarter</th>
                        <th colspan="7">STATISTICS OF CASES HANDLED DURING THE <span id="reportQuarterDisplay"></span> QUARTER/FY YEAR <span id="reportYearDisplay"></span></th>
                    </tr>
                    <tr>
                        <th>Broad Nature of Cases/Category</th>
                        <th>No. of cases reported/detected</th>
                        <th>Cases in progress (No. of cases PBC)</th>
                        <th>Cases in progress (No. of cases PUI)</th>
                        <th>Cases in progress (No. of cases PAKA)</th>
                        <th>Cases Closed (No. of cases Withdrawn)</th>
                        <th>Cases Closed (No. of cases Acquitted)</th>
                        <th>Cases Closed (No. of cases Convicted)</th>
                        <th>Totals</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="2">Cases Detected</td>
                        <td id="totalCasesDetected"></td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="2">In Progress =</td>
                        <td id="totalCasesInProgress"></td>
                        <td colspan="7"></td>
                    </tr>
                    <tr>
                        <td colspan="2">Closed =</td>
                        <td id="totalCasesClosed"></td>
                        <td colspan="7"></td>
                    </tr>
                    </tfoot>
                </table>
            </div>
            <button class="btn btn-sm btn-success mt-2" id="exportCategoryCasesReport">Export to CSV</button>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Populate Year Dropdown (Example)
        function populateFYYearDropdown() {
            let currentYear = new Date().getFullYear();
            let startYear = currentYear - 10;
            let yearSelect = $('#reportFYYear');

            for (let i = startYear; i <= currentYear + 5; i++) {
                yearSelect.append(`<option value="<span class="math-inline">\{i\}"\></span>{i}</option>`);
            }
            yearSelect.val(currentYear);
        }

        populateFYYearDropdown();

        // Generate Report (Example - Adapt to your CodeIgniter backend)
        $('#categoryCasesReportForm').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();

            // $.ajax({
            //   url: '/your-codeigniter-controller/generate-category-cases-report', // Replace with your CI URL
            //   method: 'POST',
            //   data: formData,
            //   dataType: 'json',
            //   success: function(data) {
            //     populateCategoryCasesReportTable(data);
            //     $('#reportQuarterDisplay').text($('#reportQuarter option:selected').text());
            //     $('#reportYearDisplay').text($('#reportFYYear option:selected').text());
            //   },
            //   error: function(xhr, status, error) {
            //     console.error('Error generating report:', error);
            //     alert('Failed to generate report. Please try again.');
            //   }
            // });
            // Dummy data for testing
            const reportData = [
                { category: "Telecommunications offences", reported: 10, pbc: 2, pui: 3, paka: 1, withdrawn: 1, acquitted: 1, convicted: 2, totals: 10 },
                { category: "SIM Card offences", reported: 5, pbc: 1, pui: 1, paka: 0, withdrawn: 0, acquitted: 1, convicted: 2, totals: 5 },
                { category: "Type Approval/Equipment offences", reported: 8, pbc: 2, pui: 2, paka: 1, withdrawn: 1, acquitted: 0, convicted: 2, totals: 8 },
                { category: "Electronic Transactions/Systems", reported: 12, pbc: 3, pui: 3, paka: 2, withdrawn: 2, acquitted: 1, convicted: 1, totals: 12 },
                { category: "Radio Communication/Frequency", reported: 7, pbc: 1, pui: 2, paka: 1, withdrawn: 1, acquitted: 1, convicted: 1, totals: 7 },
                { category: "Broadcasting offences", reported: 9, pbc: 2, pui: 2, paka: 1, withdrawn: 0, acquitted: 2, convicted: 2, totals: 9 },
                { category: "Postal/Courier offences", reported: 6, pbc: 1, pui: 1, paka: 0, withdrawn: 0, acquitted: 2, convicted: 2, totals: 6 },
                { category: "Consumer Protection Offences", reported: 11, pbc: 3, pui: 3, paka: 1, withdrawn: 1, acquitted: 2, convicted: 2, totals: 11 }
            ];
            populateCategoryCasesReportTable(reportData);
            $('#reportQuarterDisplay').text($('#reportQuarter option:selected').text());
            $('#reportYearDisplay').text($('#reportFYYear option:selected').text());
        });

        // Populate Report Table
        function populateCategoryCasesReportTable(data) {
            let tableBody = $('#categoryCasesReportTable tbody');
            tableBody.empty();
            let totalReported = 0;
            let totalInProgress = 0;
            let totalClosed = 0;

            $.each(data, function(index, item) {
                tableBody.append(`
          <tr>
            <td></td>
            <td><span class="math-inline">\{item\.category\}</td\>
<td\></span>{item.reported}</td>
            <td><span class="math-inline">\{item\.pbc\}</td\>
<td\></span>{item.pui}</td>
            <td><span class="math-inline">\{item\.paka\}</td\>
<td\></span>{item.withdrawn}</td>
            <td><span class="math-inline">\{item\.acquitted\}</td\>
<td\></span>{item.convicted}</td>
            <td>${item.totals}</td>
          </tr>
        `);
                totalReported += item.reported;
                totalInProgress += item.pbc + item.pui + item.paka;
                totalClosed += item.withdrawn + item.acquitted + item.convicted;
            });

            $('#totalCasesDetected').text(totalReported);
            $('#totalCasesInProgress').text(totalInProgress);
            $('#totalCasesClosed').text(totalClosed);
        }

        // Export to CSV (Example - Adapt to your server-side export)
        $('#exportCategoryCasesReport').on('click', function() {
            alert('Export to CSV functionality will be implemented here (server-side).');
            // window.location.href = '/your-codeigniter-controller/export-category-cases-report';
        });
    });
</script>