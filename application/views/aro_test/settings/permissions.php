<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">User Permissions</h1>
            <p class="text-muted mb-0">Manage ARO feature access and approval workflows</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export Permissions
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-person-plus me-1"></i> Add User
            </button>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Role-Based Permissions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Role-Based Permissions</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                            <tr>
                                <th>Feature / Action</th>
                                <th class="text-center">Partner</th>
                                <th class="text-center">Senior Advocate</th>
                                <th class="text-center">Associate</th>
                                <th class="text-center">Paralegal</th>
                                <th class="text-center">Admin</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>View ARO Calculator</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Perform Calculations</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Apply Uplift (up to 15%)</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Apply Uplift (above 15%)</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-warning"><i class="bi bi-dash-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Approve Calculations</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-warning"><i class="bi bi-dash-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Generate Bill of Costs</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Manage ARO Rules</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>View Reports</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-warning"><i class="bi bi-dash-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            <tr>
                                <td>Manage User Permissions</td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-danger"><i class="bi bi-x-circle-fill"></i></td>
                                <td class="text-center text-success"><i class="bi bi-check-circle-fill"></i></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">
                            <i class="bi bi-check-circle-fill text-success me-1"></i> Full Access •
                            <i class="bi bi-dash-circle-fill text-warning me-1"></i> Limited Access •
                            <i class="bi bi-x-circle-fill text-danger me-1"></i> No Access
                        </small>
                    </div>
                </div>
            </div>

            <!-- User Management -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">User Access Management</h5>
                    <div class="input-group" style="width: 300px;">
                        <input type="text" class="form-control" placeholder="Search users...">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                            <tr>
                                <th>User</th>
                                <th>Role</th>
                                <th>Department</th>
                                <th>Last Login</th>
                                <th>Status</th>
                                <th width="150">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-primary fw-bold">JD</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">John Advocate</h6>
                                            <small class="text-muted">john@lawfirm.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Senior Advocate</td>
                                <td>Commercial Litigation</td>
                                <td>Today, 14:30</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Revoke</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-success fw-bold">SJ</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Sarah Johnson</h6>
                                            <small class="text-muted">sarah@lawfirm.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Associate</td>
                                <td>Property Law</td>
                                <td>Yesterday, 16:45</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Revoke</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-warning bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-warning fw-bold">MJ</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Michael Partner</h6>
                                            <small class="text-muted">michael@lawfirm.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Partner</td>
                                <td>Management</td>
                                <td>Dec 15, 2023</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Revoke</button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-info bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-info fw-bold">AP</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Admin User</h6>
                                            <small class="text-muted">admin@lawfirm.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Administrator</td>
                                <td>IT</td>
                                <td>Today, 09:15</td>
                                <td><span class="badge bg-success">Active</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary">Edit</button>
                                        <button class="btn btn-outline-danger">Revoke</button>
                                    </div>
                                </td>
                            </tr>
                            <tr class="table-warning">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-secondary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center me-3">
                                            <span class="text-secondary fw-bold">NP</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">New Paralegal</h6>
                                            <small class="text-muted">paralegal@lawfirm.com</small>
                                        </div>
                                    </div>
                                </td>
                                <td>Paralegal</td>
                                <td>Litigation Support</td>
                                <td>Never</td>
                                <td><span class="badge bg-warning">Pending</span></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-success">Activate</button>
                                        <button class="btn btn-outline-danger">Reject</button>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Approval Workflow Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Approval Workflow</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Default Approver</label>
                        <select class="form-select">
                            <option>Matter Partner</option>
                            <option selected>Department Head</option>
                            <option>Designated Partner</option>
                            <option>Practice Area Lead</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Escalation Time</label>
                        <div class="input-group">
                            <input type="number" class="form-control" value="48" min="1" max="168">
                            <span class="input-group-text">hours</span>
                        </div>
                        <small class="form-text text-muted">Time before pending approval is escalated</small>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="autoEscalate" checked>
                        <label class="form-check-label" for="autoEscalate">
                            Auto-escalate overdue approvals
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="emailNotifications" checked>
                        <label class="form-check-label" for="emailNotifications">
                            Send email notifications for approvals
                        </label>
                    </div>
                </div>
            </div>

            <!-- Audit Settings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Audit & Security</h5>
                </div>
                <div class="card-body">
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="logAllActions" checked>
                        <label class="form-check-label" for="logAllActions">
                            Log all user actions
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="requireReauth" checked>
                        <label class="form-check-label" for="requireReauth">
                            Require re-authentication for sensitive actions
                        </label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="sessionTimeout">
                        <label class="form-check-label" for="sessionTimeout">
                            Enable session timeout (30 minutes)
                        </label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="ipWhitelist">
                        <label class="form-check-label" for="ipWhitelist">
                            Restrict access to office IP addresses
                        </label>
                    </div>
                </div>
            </div>

            <!-- Quick Statistics -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Access Statistics</h5>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-6">Total Users:</dt>
                        <dd class="col-sm-6">24</dd>

                        <dt class="col-sm-6">Active Users:</dt>
                        <dd class="col-sm-6">22</dd>

                        <dt class="col-sm-6">Pending Activation:</dt>
                        <dd class="col-sm-6">2</dd>

                        <dt class="col-sm-6">Admin Users:</dt>
                        <dd class="col-sm-6">3</dd>

                        <dt class="col-sm-6">Partner Users:</dt>
                        <dd class="col-sm-6">5</dd>

                        <dt class="col-sm-6">Last Audit:</dt>
                        <dd class="col-sm-6">Dec 15, 2023</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>