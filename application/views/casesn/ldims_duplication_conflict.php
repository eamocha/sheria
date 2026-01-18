<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Conflict & Duplication Detection</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">This Business Intelligence (BI) powered view proactively identifies and alerts users to potential conflicts or duplications within the legislative draft. Using advanced analytics, it cross-references against existing laws, case law, and internal drafts in real time.</p>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Clause/Section</th>
                <th>Conflict Type</th>
                <th>Detected In</th>
                <th>Severity</th>
                <th>Details</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Tax Code §14.2</td>
                <td>Duplication</td>
                <td>Existing Taxation Amendment Bill (2021)</td>
                <td><span class="badge badge-warning">Medium</span></td>
                <td>Proposed clause duplicates wording from an active bill.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Conflict</button>
                    <button class="btn btn-sm btn-success">Resolve</button>
                </td>
            </tr>
            <tr>
                <td>Environmental Act §3.5</td>
                <td>Contradiction</td>
                <td>Environmental Protection Act (1998)</td>
                <td><span class="badge badge-danger">High</span></td>
                <td>Emission limits in draft contradict existing statutory limits.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Conflict</button>
                    <button class="btn btn-sm btn-danger">Request Amendment</button>
                </td>
            </tr>
            <tr>
                <td>Fisheries Act §10.1</td>
                <td>Inconsistency</td>
                <td>Maritime Safety Regulation (2015)</td>
                <td><span class="badge badge-info">Low</span></td>
                <td>Operational zones overlap with restricted maritime zones.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Conflict</button>
                    <button class="btn btn-sm btn-warning">Flag for Review</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
