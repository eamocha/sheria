<div class="container mt-4">
    <h3 class="mb-4">Legislative Drafting Requests Report</h3>

    <!-- Summary Cards -->
    <div class="row text-center mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Total Requests</h5>
                    <h2 class="text-primary">245</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Pending</h5>
                    <h2 class="text-warning">58</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>Completed</h5>
                    <h2 class="text-success">165</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>In Progress</h5>
                    <h2 class="text-info">22</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Volume Over Time -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">Requests Volume Over Time</div>
                <div class="card-body">
                    <canvas id="requestsVolumeChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Types of Requests -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">Types of Drafting Requests</div>
                <div class="card-body">
                    <canvas id="requestsTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Line Chart: Volume Over Time
    new Chart(document.getElementById("requestsVolumeChart"), {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
            datasets: [{
                label: "Requests",
                data: [15, 20, 25, 30, 28, 35, 40],
                borderColor: "#007bff",
                fill: false,
                tension: 0.1
            }]
        }
    });

    // Pie Chart: Types of Requests
    new Chart(document.getElementById("requestsTypeChart"), {
        type: "pie",
        data: {
            labels: ["Bills", "Regulations", "Proclamations", "Government Notices"],
            datasets: [{
                data: [120, 60, 40, 25],
                backgroundColor: ["#28a745", "#ffc107", "#17a2b8", "#dc3545"]
            }]
        }
    });
</script>
