<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">File Categorization & Templates</h3>
</div>

<div class="card">
    <div class="card-body">


        <!-- Category Tabs -->
        <ul class="nav nav-tabs" id="fileCategoryTabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="bills-tab" data-toggle="tab" href="#bills" role="tab">Bills</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="proclamations-tab" data-toggle="tab" href="#proclamations" role="tab">Proclamations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="regulations-tab" data-toggle="tab" href="#regulations" role="tab">Regulations</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="notices-tab" data-toggle="tab" href="#notices" role="tab">Government Notices</a>
            </li>
        </ul>

        <div class="tab-content mt-3" id="fileCategoryContent">
            <!-- Bills -->
            <div class="tab-pane fade show active" id="bills" role="tabpanel">
                <h5>Bill Template</h5>
                <p class="text-muted">Use this template to create legislative bills.</p>
                <button class="btn btn-primary btn-sm">Create New Bill</button>
                <table class="table table-sm mt-3">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>National Education Bill</td>
                        <td>Wanjiku Kamau</td>
                        <td>05 Feb 2025</td>
                        <td><span class="badge badge-warning">Draft</span></td>
                        <td>
                            <button class="btn btn-outline-info btn-sm">View</button>
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Proclamations -->
            <div class="tab-pane fade" id="proclamations" role="tabpanel">
                <h5>Proclamation Template</h5>
                <p class="text-muted">For official proclamations from the government.</p>
                <button class="btn btn-primary btn-sm">Create New Proclamation</button>
                <table class="table table-sm mt-3">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Proclamation on National Holiday</td>
                        <td>John Mwangi</td>
                        <td>12 Jan 2025</td>
                        <td><span class="badge badge-success">Published</span></td>
                        <td>
                            <button class="btn btn-outline-info btn-sm">View</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Regulations -->
            <div class="tab-pane fade" id="regulations" role="tabpanel">
                <h5>Regulation Template</h5>
                <p class="text-muted">For detailed rules and regulations under existing acts.</p>
                <button class="btn btn-primary btn-sm">Create New Regulation</button>
                <table class="table table-sm mt-3">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Environmental Protection Regulations</td>
                        <td>Aisha Abdi</td>
                        <td>02 Mar 2025</td>
                        <td><span class="badge badge-secondary">Pending Review</span></td>
                        <td>
                            <button class="btn btn-outline-info btn-sm">View</button>
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Government Notices -->
            <div class="tab-pane fade" id="notices" role="tabpanel">
                <h5>Government Notice Template</h5>
                <p class="text-muted">For formal notices from ministries and agencies.</p>
                <button class="btn btn-primary btn-sm">Create New Notice</button>
                <table class="table table-sm mt-3">
                    <thead>
                    <tr>
                        <th>Title</th>
                        <th>Created By</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>Notice of Public Consultation</td>
                        <td>Peter Otieno</td>
                        <td>15 Feb 2025</td>
                        <td><span class="badge badge-info">Open</span></td>
                        <td>
                            <button class="btn btn-outline-info btn-sm">View</button>
                            <button class="btn btn-outline-primary btn-sm">Edit</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
