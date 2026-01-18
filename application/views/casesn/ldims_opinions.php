<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Legal Opinion Requests</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">Our system provides a dedicated module to <strong>record and manage requests for legal opinions</strong>. This centralized dashboard allows submission of new requests with all necessary details such as requesting department, subject matter, and urgency. The system automatically assigns a unique ID, tracks the request's status, and manages the workflow from assignment to final review and approval. It also maintains a searchable archive of all past requests and corresponding legal opinions.</p>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Request ID</th>
                <th>Requester</th>
                <th>Department/Agency</th>
                <th>Subject</th>
                <th>Priority</th>
                <th>Status</th>
                <th>Date Received</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>OPN-001</td>
                <td>Wanjiku Kamau</td>
                <td>Ministry of Health</td>
                <td>Interpretation of Public Health Act §12</td>
                <td><span class="badge badge-danger">High</span></td>
                <td><span class="badge badge-warning">Pending</span></td>
                <td>2025-08-01</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-success">Assign</button>
                    <button class="btn btn-sm btn-secondary">Archive</button>
                </td>
            </tr>
            <tr>
                <td>OPN-002</td>
                <td>Otieno Onyango</td>
                <td>Kenya National Highways Authority</td>
                <td>Review of Road Safety Regulation Amendment</td>
                <td><span class="badge badge-warning">Medium</span></td>
                <td><span class="badge badge-info">In Progress</span></td>
                <td>2025-07-28</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-success">Assign</button>
                    <button class="btn btn-sm btn-secondary">Archive</button>
                </td>
            </tr>
            <tr>
                <td>OPN-003</td>
                <td>Faith Njeri</td>
                <td>Office of the Attorney General</td>
                <td>Constitutional compliance of new privacy bill</td>
                <td><span class="badge badge-danger">High</span></td>
                <td><span class="badge badge-success">Completed</span></td>
                <td>2025-07-20</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-secondary">Archive</button>
                </td>
            </tr>
            <tr>
                <td>OPN-004</td>
                <td>Mutiso Kilonzo</td>
                <td>Ministry of Lands</td>
                <td>Clarification on Land Registration Act amendments</td>
                <td><span class="badge badge-warning">Medium</span></td>
                <td><span class="badge badge-warning">Pending</span></td>
                <td>2025-07-18</td>
                <td>
                    <button class="btn btn-sm btn-primary">View</button>
                    <button class="btn btn-sm btn-success">Assign</button>
                    <button class="btn btn-sm btn-secondary">Archive</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
