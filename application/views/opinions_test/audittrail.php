<div class="container mt-4">
    <h3 class="mb-3 font-weight-bold">Admin Usage Analytics</h3>

    <div class="row">
        <!-- Logins Chart -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded-2xl">
                <div class="card-body">
                    <h5 class="card-title">Logins Over Time</h5>
                    <canvas id="loginsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Module Usage -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm rounded-2xl">
                <div class="card-body">
                    <h5 class="card-title">Most Used Modules</h5>
                    <canvas id="modulesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Example data - replace with backend API data
    const loginsData = [12, 19, 7, 14, 22, 30, 18];
    const modulesData = {
        labels: ['Opinions', 'Contracts', 'Case Mgmt', 'Reports', 'Settings'],
        usage: [45, 25, 15, 10, 5]
    };

    // Logins Chart
    new Chart(document.getElementById('loginsChart'), {
        type: 'line',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Logins',
                data: loginsData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0,123,255,0.2)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Module Usage Chart
    new Chart(document.getElementById('modulesChart'), {
        type: 'doughnut',
        data: {
            labels: modulesData.labels,
            datasets: [{
                data: modulesData.usage,
                backgroundColor: ['#007bff','#28a745','#ffc107','#dc3545','#6c757d']
            }]
        }
    });
</script>
