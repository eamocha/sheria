
<style>
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
        margin-bottom: 20px;
    }
    .bg-total { background-color: #343a40; }
    .bg-success { background-color: #28a745; }
    .bg-danger { background-color: #dc3545; }
    .bg-info { background-color: #17a2b8; }
    .bg-warning { background-color: #ffc107; color: black; }
</style>

<div class="container-fluid mt-4">
    <h2 class="mb-4">Advanced Prosecution Dashboard</h2>

    <!-- Filters -->
    <form class="form-row mb-4">
        <div class="form-group col-md-2">
            <label>Year</label>
            <select class="form-control"><option>2025</option><option>2024</option></select>
        </div>
        <div class="form-group col-md-2">
            <label>Status</label>
            <select class="form-control"><option>All</option><option>PBC</option><option>PUI</option></select>
        </div>
        <div class="form-group col-md-2">
            <label>Offence Type</label>
            <select class="form-control"><option>All</option><option>SIM</option><option>Telecom</option></select>
        </div>
        <div class="form-group col-md-2">
            <label>Officer</label>
            <input type="text" class="form-control" placeholder="Officer">
        </div>
        <div class="form-group col-md-2">
            <label>Quarter</label>
            <select class="form-control"><option>Q1</option><option>Q2</option><option>Q3</option></select>
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-block">Apply Filters</button>
        </div>
    </form>

    <!-- Summary Stats -->
    <div class="row text-white">
        <div class="col-md-3"><div class="stat-card bg-total">Total Cases <h4>87</h4></div></div>
        <div class="col-md-3"><div class="stat-card bg-success">Finalized <h4>30</h4></div></div>
        <div class="col-md-3"><div class="stat-card bg-danger">PBC <h4>22</h4></div></div>
        <div class="col-md-3"><div class="stat-card bg-warning">PUI <h4>12</h4></div></div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h5>Case Status Distribution</h5>
                <div id="donutChart"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h5>Monthly Case Trends</h5>
                <div id="lineChart"></div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row">
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h5>Offence Categories by Status</h5>
                <div id="stackedBarChart"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-3 mb-4">
                <h5>Prosecution Success Rate</h5>
                <div id="radialChart"></div>
            </div>
        </div>
    </div>
</div>

<script>
    // Donut
    new ApexCharts(document.querySelector("#donutChart"), {
        chart: { type: 'donut' },
        labels: ['PBC', 'PUI', 'Finalized', 'Withdrawn', 'Convicted', 'Acquitted'],
        series: [22, 12, 30, 5, 10, 8],
        colors: ['#007bff', '#ffc107', '#28a745', '#6c757d', '#6610f2', '#20c997']
    }).render();

    // Line Chart
    new ApexCharts(document.querySelector("#lineChart"), {
        chart: { type: 'line' },
        series: [{
            name: 'New Cases',
            data: [10, 14, 8, 16, 12, 9, 7, 15, 18, 11, 13, 9]
        }],
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
        },
        colors: ['#17a2b8']
    }).render();

    // Stacked Bar
    new ApexCharts(document.querySelector("#stackedBarChart"), {
        chart: { type: 'bar', stacked: true },
        series: [
            { name: 'PBC', data: [4, 5, 6, 2, 5] },
            { name: 'PUI', data: [2, 3, 1, 2, 4] },
            { name: 'Finalized', data: [5, 4, 7, 3, 6] }
        ],
        xaxis: {
            categories: ['SIM', 'Telecom', 'Broadcasting', 'Consumer', 'Other']
        },
        colors: ['#dc3545', '#ffc107', '#28a745']
    }).render();

    // Radial Bar
    new ApexCharts(document.querySelector("#radialChart"), {
        chart: { type: 'radialBar' },
        series: [70],
        labels: ['Success %'],
        colors: ['#28a745']
    }).render();
</script>