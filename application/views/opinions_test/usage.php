<div class="container mt-5">
    <h3 class="mb-4">System Usage Analytics Dashboard</h3>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Total Users</h6>
                <h3>1,250</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Advisories Accessed</h6>
                <h3>3,450</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Search Queries</h6>
                <h3>980</h3>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm p-3">
                <h6>Avg. Session Time</h6>
                <h3>12 mins</h3>
            </div>
        </div>
    </div>

    <!-- Usage Trends -->
    <div class="card shadow-sm p-3 mb-4">
        <h5>User Activity Over Time</h5>
        <canvas id="usageTrendChart" height="120"></canvas>
    </div>

    <!-- Search Trends -->
    <div class="card shadow-sm p-3 mb-4">
        <h5>Top Search Terms</h5>
        <canvas id="searchTrendsChart" height="120"></canvas>
    </div>

    <!-- Advisory Categories -->
    <div class="card shadow-sm p-3">
        <h5>Advisory Access by Category</h5>
        <canvas id="categoryChart" height="120"></canvas>
    </div>
</div>

<!-- Load Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // User Activity Line Chart
    new Chart(document.getElementById("usageTrendChart"), {
        type: "line",
        data: {
            labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
            datasets: [{
                label: "Active Users",
                data: [200, 250, 300, 400, 380, 450],
                borderColor: "#007bff",
                fill: false,
                tension: 0.1
            }]
        }
    });

    // Search Trends Bar Chart
    new Chart(document.getElementById("searchTrendsChart"), {
        type: "bar",
        data: {
            labels: ["Data Privacy", "Employment Law", "Contracts", "Taxation", "Intellectual Property"],
            datasets: [{
                label: "Search Count",
                data: [120, 95, 150, 80, 60],
                backgroundColor: "#28a745"
            }]
        }
    });

    // Advisory Categories Pie Chart
    new Chart(document.getElementById("categoryChart"), {
        type: "pie",
        data: {
            labels: ["Corporate Law", "Criminal Law", "Civil Litigation", "Family Law", "Commercial Contracts"],
            datasets: [{
                data: [500, 300, 250, 200, 400],
                backgroundColor: ["#007bff", "#ffc107", "#28a745", "#dc3545", "#17a2b8"]
            }]
        }
    });
</script>
