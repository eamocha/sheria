<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Draft Comparison View</h3>
</div>
<div class="card mb-4">
    <div class="card-body">
        <p class="mb-4 small text-muted">Compare how the proposed draft will alter or implement changes to existing legislation, highlighting differences clearly.</p>
        <table class="table table-bordered table-hover">
            <thead class="thead-light">
            <tr>
                <th>Section</th>
                <th>Current Law</th>
                <th>Proposed Change</th>
                <th>Impact Summary</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>Tax Code §12.4</td>
                <td><pre class="mb-0">All businesses shall file annual returns...</pre></td>
                <td><pre class="mb-0">All businesses and freelancers shall file quarterly returns...</pre></td>
                <td>Expands scope to freelancers; changes filing frequency.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Full Context</button>
                    <button class="btn btn-sm btn-success">Accept Change</button>
                </td>
            </tr>
            <tr>
                <td>Environmental Act §5.2</td>
                <td><pre class="mb-0">Factories must limit emissions to 50 units...</pre></td>
                <td><pre class="mb-0">Factories must limit emissions to 35 units and adopt eco-monitoring devices...</pre></td>
                <td>Stricter emission limits; adds monitoring requirement.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Full Context</button>
                    <button class="btn btn-sm btn-warning">Flag for Review</button>
                </td>
            </tr>
            <tr>
                <td>Fisheries Act §8.1</td>
                <td><pre class="mb-0">Fishing vessels may operate within 50 nautical miles...</pre></td>
                <td><pre class="mb-0">Fishing vessels may operate within 30 nautical miles; protected zones established...</pre></td>
                <td>Reduces fishing area; introduces conservation zones.</td>
                <td>
                    <button class="btn btn-sm btn-primary">View Full Context</button>
                    <button class="btn btn-sm btn-success">Accept Change</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
