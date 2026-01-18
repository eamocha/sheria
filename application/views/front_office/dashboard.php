
<div class="container-fluid mt-4">
    <!-- Place this at the top of your dashboard.php, just below the container-fluid opening -->
    <div class="row mb-4">
        <div class="col-md-3">
            <label for="dashboardYear" class="mb-0">Year</label>
            <select id="dashboardYear" class="form-control">
                <?php
                $currentYear = date('Y');
                for ($y = $currentYear; $y >= $currentYear - 5; $y--) {
                    echo "<option value=\"$y\">$y</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="dashboardMonth" class="mb-0">Month</label>
            <select id="dashboardMonth" class="form-control">
                <option value="">All Months</option>
                <?php
                $months = [
                    1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
                    5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
                    9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
                ];
                foreach ($months as $num => $name) {
                    echo "<option value=\"$num\">$name</option>";
                }
                ?>
            </select>
        </div>
        <div class="col-md-3">
            <label for="dashboardType" class="mb-0">Type</label>
         <?php  echo form_dropdown(
            "dashboardType",
            $types, "", 'id="dashboardType" class="form-control select-picker" data-live-search="true" data-field="correspondence_types" data-size="' . $this->session->userdata("max_drop_down_length") . '"'
            );
            ?>

        </div>
        <div class="col-md-3 d-flex align-items-end">
            <button id="dashboardFilterBtn" class="btn btn-primary btn-block">Apply Filters</button>
        </div>
    </div>
    <h2 class="mb-4">Correspondences Dashboard</h2>
    <div class="row">
        <!-- Summary Cards -->
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Correspondences</h5>
                    <h2 id="totalCorrespondences">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 id="completedCorrespondences">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Ongoing</h5>
                    <h2 id="ongoingCorrespondences">0</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Pending</h5>
                    <h2 id="pendingCorrespondences">0</h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card">
                <div class="card-header">Correspondences Over Time</div>
                <div class="card-body">
                    <div id="correspondenceTrendChart"></div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-header">Status Distribution</div>
                <div class="card-body">
                    <div id="statusPieChart"></div>
                </div>
            </div>
        </div>
    </div>
    <!-- Recent Activity Table -->
    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">Recent Correspondence Activity</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Serial No.</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Assignee</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody id="recentActivityTable">
                            <!-- Rows will be populated by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    jQuery(document).ready(function () {
   // document.addEventListener('DOMContentLoaded', function() {
        // Chart instances (to allow updating)
        let trendChart, pieChart;

        // Function to load dashboard stats via AJAX
        function loadDashboardStats(filters = {}) {
              // Show loader before request
            $('#loader-global').show();

            // Build query string
            const params = new URLSearchParams(filters).toString();
            fetch('<?= site_url('front_office/dashboard_stats'); ?>' + (params ? '?' + params : ''), {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                // Update summary cards
                document.getElementById('totalCorrespondences').textContent = data.stats.total;
                document.getElementById('completedCorrespondences').textContent = data.stats.completed;
                document.getElementById('ongoingCorrespondences').textContent = data.stats.ongoing;
                document.getElementById('pendingCorrespondences').textContent = data.stats.pending;

                // Update or render Correspondence Trend Chart
                const trendOptions = {
                    chart: { type: 'line', height: 300 },
                    series: [{
                        name: 'Correspondences',
                        data: data.trend.counts
                    }],
                    xaxis: { categories: data.trend.dates },
                    stroke: { curve: 'smooth' },
                    colors: ['#007bff']
                };
                if (trendChart) {
                    trendChart.updateOptions(trendOptions);
                } else {
                    trendChart = new ApexCharts(document.querySelector("#correspondenceTrendChart"), trendOptions);
                    trendChart.render();
                }

                // Update or render Status Pie Chart
                const pieOptions = {
                    chart: { type: 'donut', height: 300 },
                    labels: data.status.labels,
                    series: data.status.series,
                    colors: ['#28a745', '#ffc107', '#dc3545', '#6c757d'],
                    legend: { position: 'bottom' }
                };
                if (pieChart) {
                    pieChart.updateOptions(pieOptions);
                } else {
                    pieChart = new ApexCharts(document.querySelector("#statusPieChart"), pieOptions);
                    pieChart.render();
                }

                // Update Recent Activity Table
                let tbody = document.getElementById('recentActivityTable');
                tbody.innerHTML = '';
                data.recent.forEach(item => {
                    let badgeClass = 'secondary';
                    if(item.status === 'Completed') badgeClass = 'success';
                    else if(item.status === 'Ongoing') badgeClass = 'warning';
                    else if(item.status === 'Pending') badgeClass = 'danger';
                    else if(item.status === 'Cancelled') badgeClass = 'dark';
                    tbody.innerHTML += `
                        <tr>
                            <td>${item.date}</td>
                            <td>${item.serial}</td>
                            <td>${item.type}</td>
                            <td><span class="badge badge-${badgeClass}">${item.status}</span></td>
                            <td>${item.assignee}</td>
                            <td><a href="${getBaseURL()+"front_office/view/"+item.id}" class="btn btn-sm btn-outline-primary">View</a></td>
                        </tr>
                    `;
                });
                if (data.recent.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No activity found</td></tr>';
                }
            })
            .catch(err => {
                  pinesMessage({ty: 'error', m: _lang.feedback_messages.error});
                 
                console.error(err);
            })
             .finally(() => {
                // Hide loader after request completes (success or error)
                $('#loader-global').hide();
            });
        }

        // Initial load
        loadDashboardStats({
            year: document.getElementById('dashboardYear').value,
            month: document.getElementById('dashboardMonth').value,
            type: document.getElementById('dashboardType').value
        });

        // Filter button event
        document.getElementById('dashboardFilterBtn').addEventListener('click', function() {
            const year = document.getElementById('dashboardYear').value;
            const month = document.getElementById('dashboardMonth').value;
            const type = document.getElementById('dashboardType').value;
            loadDashboardStats({year, month, type});
        });
    });
</script>