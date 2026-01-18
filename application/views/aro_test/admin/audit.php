<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Audit Log</h1>
            <p class="text-muted mb-0">Track all system changes and user actions</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Log
            </button>
            <button class="btn btn-outline-danger">
                <i class="bi bi-trash me-1"></i> Clear Old Logs
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Action Type</label>
                    <select class="form-select">
                        <option value="">All Actions</option>
                        <option>Rule Created</option>
                        <option>Rule Modified</option>
                        <option>Rule Deleted</option>
                        <option>Slab Updated</option>
                        <option>Version Created</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">User</label>
                    <select class="form-select">
                        <option value="">All Users</option>
                        <option>admin@lawfirm.com</option>
                        <option>partner@lawfirm.com</option>
                        <option>associate@lawfirm.com</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date Range</label>
                    <input type="date" class="form-control" value="2024-01-01">
                </div>
                <div class="col-md-3">
                    <label class="form-label">To</label>
                    <input type="date" class="form-control" value="2024-01-18">
                </div>
            </div>
        </div>
    </div>

    <!-- Audit Log Table -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Audit History (1,247 entries)</h5>
            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="realTimeUpdates" checked>
                <label class="form-check-label" for="realTimeUpdates">Live Updates</label>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Details</th>
                        <th>IP Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2024-01-18 14:30:25</td>
                        <td>admin@lawfirm.com</td>
                        <td><span class="badge bg-info">Rule Modified</span></td>
                        <td>Instruction Fee - Civil Suit</td>
                        <td>Updated slab 3 range (5M-10M @ 1.44%)</td>
                        <td>192.168.1.100</td>
                    </tr>
                    <tr>
                        <td>2024-01-18 11:15:42</td>
                        <td>partner@lawfirm.com</td>
                        <td><span class="badge bg-success">Rule Created</span></td>
                        <td>Commercial Agreement Drafting</td>
                        <td>Added new fixed amount rule</td>
                        <td>192.168.1.101</td>
                    </tr>
                    <tr>
                        <td>2024-01-17 16:20:18</td>
                        <td>admin@lawfirm.com</td>
                        <td><span class="badge bg-warning">Slab Updated</span></td>
                        <td>Conveyancing - Sale/Purchase</td>
                        <td>Modified percentage from 1.5% to 1.44%</td>
                        <td>192.168.1.100</td>
                    </tr>
                    <tr>
                        <td>2024-01-16 09:45:33</td>
                        <td>associate@lawfirm.com</td>
                        <td><span class="badge bg-primary">Version Created</span></td>
                        <td>Version 2.1</td>
                        <td>Created new rule version</td>
                        <td>192.168.1.102</td>
                    </tr>
                    <tr>
                        <td>2024-01-15 13:20:57</td>
                        <td>admin@lawfirm.com</td>
                        <td><span class="badge bg-danger">Rule Deleted</span></td>
                        <td>Obsolete Civil Rule</td>
                        <td>Removed deprecated rule</td>
                        <td>192.168.1.100</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav aria-label="Audit log pagination">
                <ul class="pagination mb-0 justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>