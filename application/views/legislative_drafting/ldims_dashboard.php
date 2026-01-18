<div class="container mt-4">
    <h3 class="mb-4">Legislative Drafting KPI Dashboard</h3>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <h6 class="text-muted">Pending Requests</h6>
                    <h3 class="text-primary" id="pendingCount">42</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <h6 class="text-muted">Drafts in Progress</h6>
                    <h3 class="text-warning" id="inProgressCount">30</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <h6 class="text-muted">Approved Drafts</h6>
                    <h3 class="text-success" id="approvedCount">85</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-danger">
                <div class="card-body text-center">
                    <h6 class="text-muted">Bottlenecks</h6>
                    <h3 class="text-danger" id="bottleneckCount">5</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4">
        <!-- Productivity Trend -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Team Productivity Over Time</strong>
                </div>
                <div class="card-body">
                    <canvas id="productivityChart" height="150"></canvas>
                </div>
            </div>
        </div>

        <!-- Bottlenecks by Stage -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <strong>Bottlenecks by Workflow Stage</strong>
                </div>
                <div class="card-body">
                    <canvas id="bottleneckChart" height="150"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Productivity Chart
    const ctx1 = document.getElementById('productivityChart').getContext('2d');
    new Chart(ctx1, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug'],
            datasets: [{
                label: 'Drafts Completed',
                data: [5, 8, 15, 20, 30, 50, 70, 85],
                borderColor: '#0d6efd',
                backgroundColor: 'rgba(13,110,253,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } }
        }
    });

    // Bottleneck Chart
    const ctx2 = document.getElementById('bottleneckChart').getContext('2d');
    new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: ['Drafting', 'Reviewing', 'Awaiting Feedback', 'Approval'],
            datasets: [{
                label: 'Number of Requests',
                data: [2, 1, 5, 2],
                backgroundColor: ['#ffc107', '#0d6efd', '#dc3545', '#198754']
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>
