<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Parliamentary Counsels Collaboration</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">Our system facilitates seamless <strong>collaboration among Parliamentary Counsels</strong> working on the same draft through a comprehensive set of features. It includes <strong>real-time co-authoring</strong> capabilities, allowing multiple users to edit the same document simultaneously while tracking changes and identifying who made each edit. The system also integrates a <strong>comment and annotation feature</strong> for discussing specific clauses, and a dedicated messaging tool for instant communication. Furthermore, it maintains a <strong>complete version history</strong>, ensuring all users can review past iterations and revert to previous versions if needed.</p>

        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Draft ID</th>
                <th>Title</th>
                <th>Current Editors</th>
                <th>Last Edited</th>
                <th>Version</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>DRF-001</td>
                <td>Public Health Amendment Bill</td>
                <td>Wanjiku Kamau, Otieno Onyango</td>
                <td>2025-08-09 14:32</td>
                <td>v1.4</td>
                <td>
                    <button class="btn btn-sm btn-primary">Open for Editing</button>
                    <button class="btn btn-sm btn-info">View Comments</button>
                    <button class="btn btn-sm btn-secondary">Version History</button>
                </td>
            </tr>
            <tr>
                <td>DRF-002</td>
                <td>Land Registration Act Review</td>
                <td>Mutiso Kilonzo</td>
                <td>2025-08-08 10:15</td>
                <td>v2.0</td>
                <td>
                    <button class="btn btn-sm btn-primary">Open for Editing</button>
                    <button class="btn btn-sm btn-info">View Comments</button>
                    <button class="btn btn-sm btn-secondary">Version History</button>
                </td>
            </tr>
            <tr>
                <td>DRF-003</td>
                <td>Data Privacy & Protection Bill</td>
                <td>Faith Njeri, Otieno Onyango</td>
                <td>2025-08-07 16:48</td>
                <td>v3.2</td>
                <td>
                    <button class="btn btn-sm btn-primary">Open for Editing</button>
                    <button class="btn btn-sm btn-info">View Comments</button>
                    <button class="btn btn-sm btn-secondary">Version History</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
