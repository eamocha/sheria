<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Legislative Trends & Key Focus Areas</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
       <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-gavel fa-2x text-primary mb-2"></i>
                        <h6 class="mb-1">Drafts Analyzed</h6>
                        <h4 class="text-dark">1,245</h4>
                        <small class="text-muted">Past 5 Years</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-balance-scale fa-2x text-success mb-2"></i>
                        <h6 class="mb-1">Key Legal Issues</h6>
                        <h4 class="text-dark">48</h4>
                        <small class="text-muted">Emerging Trends</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-lightbulb fa-2x text-warning mb-2"></i>
                        <h6 class="mb-1">Focus Areas</h6>
                        <h4 class="text-dark">12</h4>
                        <small class="text-muted">Priority Sectors</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <i class="fas fa-chart-line fa-2x text-danger mb-2"></i>
                        <h6 class="mb-1">Amendment Patterns</h6>
                        <h4 class="text-dark">327</h4>
                        <small class="text-muted">Detected Changes</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <canvas id="trendsBarChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="focusPieChart"></canvas>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctxBar = document.getElementById('trendsBarChart').getContext('2d');
    new Chart(ctxBar, {
        type: 'bar',
        data: {
            labels: ['2019', '2020', '2021', '2022', '2023'],
            datasets: [{
                label: 'Drafts Analyzed',
                data: [200, 250, 300, 245, 250],
                backgroundColor: 'rgba(54, 162, 235, 0.6)'
            }]
        },
        options: { responsive: true }
    });

    const ctxPie = document.getElementById('focusPieChart').getContext('2d');
    new Chart(ctxPie, {
        type: 'pie',
        data: {
            labels: ['Health', 'Education', 'Environment', 'Economy', 'Security'],
            datasets: [{
                data: [25, 20, 15, 30, 10],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.6)',
                    'rgba(54, 162, 235, 0.6)',
                    'rgba(255, 206, 86, 0.6)',
                    'rgba(75, 192, 192, 0.6)',
                    'rgba(153, 102, 255, 0.6)'
                ]
            }]
        },
        options: { responsive: true }
    });
</script>
