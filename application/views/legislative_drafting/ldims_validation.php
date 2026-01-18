<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Compliance Validation</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">This view validates drafts against statutory requirements and legal standards to ensure compliance before approval.</p>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Draft Reference No.</th>
                <th>Title</th>
                <th>Validation Area</th>
                <th>Status</th>
                <th>Reviewed On</th>
                <th>Reviewer</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>DRFT-2023-001</td>
                <td>Taxation Amendment Bill</td>
                <td>Alignment with existing Tax Code</td>
                <td><span class="badge badge-success">Compliant</span></td>
                <td>02 Aug 2023</td>
                <td>John Kamau</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Report</button>
                    <button class="btn btn-sm btn-warning">Revalidate</button>
                </td>
            </tr>
            <tr>
                <td>DRFT-2023-014</td>
                <td>Environmental Standards Bill</td>
                <td>Environmental Regulations Compliance</td>
                <td><span class="badge badge-danger">Non-Compliant</span></td>
                <td>15 Jul 2023</td>
                <td>Jane Smith</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Report</button>
                    <button class="btn btn-sm btn-danger">Request Changes</button>
                </td>
            </tr>
            <tr>
                <td>DRFT-2023-021</td>
                <td>Fisheries Reform Bill</td>
                <td>Consistency with International Agreements</td>
                <td><span class="badge badge-warning">Pending Review</span></td>
                <td>—</td>
                <td>—</td>
                <td>
                    <button class="btn btn-sm btn-primary">Start Validation</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
