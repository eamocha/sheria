    <style>
        .table th, .table td {
            vertical-align: middle !important;
            font-size: 14px;
        }
        .table-actions .btn {
            margin-right: 5px;
        }
    </style>

<div class="container-fluid mt-4">
    <h2 class="mb-4">Case Master Register</h2>

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
            <input type="text" class="form-control" placeholder="Investigating Officer">
        </div>
        <div class="form-group col-md-2 d-flex align-items-end">
            <button class="btn btn-primary btn-block">Filter</button>
        </div>
    </form>

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover">
            <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Financial Year</th>
                <th>Origin</th>
                <th>Approval Date</th>
                <th>Accused Name</th>
                <th>Arrest Date & Location</th>
                <th>Case Reference</th>
                <th>Case Brief</th>
                <th>Status</th>
                <th>Investigating Officer</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <!-- Sample data -->
            <tr>
                <td>1</td>
                <td>2025</td>
                <td>Consumer Dept</td>
                <td>2025-03-10</td>
                <td>John Omolo</td>
                <td>Kisumu, 2025-03-12</td>
                <td>CF 621/2025</td>
                <td>Unauthorized FM station</td>
                <td><span class="badge badge-warning">PBC</span></td>
                <td>Inspector Wanjala</td>
                <td class="table-actions">
                    <a href="#" class="btn btn-sm btn-info">View</a>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-warning">Update</a>
                    <a href="#" class="btn btn-sm btn-secondary">Exhibit</a>
                </td>
            </tr>
            <tr>
                <td>2</td>
                <td>2025</td>
                <td>Licensing</td>
                <td>2025-02-18</td>
                <td>Jane Mugo</td>
                <td>Nairobi, 2025-02-19</td>
                <td>CF 518/2025</td>
                <td>SIM fraud operations</td>
                <td><span class="badge badge-success">Finalized</span></td>
                <td>Sergeant Kiplangat</td>
                <td class="table-actions">
                    <a href="#" class="btn btn-sm btn-info">View</a>
                    <a href="#" class="btn btn-sm btn-primary">Edit</a>
                    <a href="#" class="btn btn-sm btn-warning">Update</a>
                    <a href="#" class="btn btn-sm btn-secondary">Exhibit</a>
                </td>
            </tr>
            <!-- More rows can be rendered dynamically -->
            </tbody>
        </table>
    </div>
</div>

