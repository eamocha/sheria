<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Related Legal Acts & Feedback Metrics</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">This view tracks and displays existing legal acts that will be altered or implemented by the draft law or regulation.</p>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Act Reference No.</th>
                <th>Title</th>
                <th>Jurisdiction</th>
                <th>Effective Date</th>
                <th>Status</th>
                <th>Impact</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>ACT-2005-001</td>
                <td>Taxation Act 2005</td>
                <td>National</td>
                <td>01 Jan 2006</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>Amend specific sections on corporate tax rates</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-warning">Compare</button>
                </td>
            </tr>
            <tr>
                <td>ACT-2012-045</td>
                <td>Environmental Protection Act</td>
                <td>National</td>
                <td>15 Apr 2013</td>
                <td><span class="badge badge-success">Active</span></td>
                <td>Introduce new emission limits</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-warning">Compare</button>
                </td>
            </tr>
            <tr>
                <td>ACT-1998-032</td>
                <td>Fisheries Regulation Act</td>
                <td>National</td>
                <td>10 Aug 1998</td>
                <td><span class="badge badge-danger">Repealed</span></td>
                <td>Replace outdated provisions with new standards</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-warning">Compare</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Feedback & Performance Metrics</h5>
        <div class="row">
            <div class="col-md-4">
                <div class="p-3 border rounded">
                    <h6>User Satisfaction Survey</h6>
                    <p class="small text-muted">Average Score: <strong>4.3 / 5</strong></p>
                    <button class="btn btn-sm btn-outline-primary">View Survey Results</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded">
                    <h6>Performance Dashboard</h6>
                    <p class="small text-muted">Draft Processing Time: <strong>15 days</strong></p>
                    <button class="btn btn-sm btn-outline-success">View Dashboard</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 border rounded">
                    <h6>Feedback Loops</h6>
                    <p class="small text-muted">Number of Feedback Items: <strong>27</strong></p>
                    <button class="btn btn-sm btn-outline-warning">Review Feedback</button>
                </div>
            </div>
        </div>
    </div>
</div>
