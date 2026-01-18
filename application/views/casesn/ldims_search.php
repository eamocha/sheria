<div class="container-fluid mb-5">
    <header class="bg-white shadow-sm mb-4">
        <div class="container-fluid py-3">
            <h1 class="h3 mb-0 text-primary font-weight-bold">
                Legislative Drafting Search Summaries
            </h1>
            <p class="mb-0 text-muted">
                View, manage, and take action on draft Bills, proposals, and amendments from various MDAs for onward transmission to Parliament.
            </p>
        </div>
    </header>

    <!-- Search & Filter Controls -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form class="form-inline">
                <input type="text" class="form-control mb-2 mr-sm-2" placeholder="Search by Title or Bill No.">
                <select class="form-control mb-2 mr-sm-2">
                    <option value="">Filter by Status</option>
                    <option>Draft</option>
                    <option>Under Review</option>
                    <option>Awaiting Public Participation</option>
                    <option>Submitted to Parliament</option>
                </select>
                <input type="date" class="form-control mb-2 mr-sm-2" placeholder="From Date">
                <input type="date" class="form-control mb-2 mr-sm-2" placeholder="To Date">
                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fa fa-search"></i> Search
                </button>
            </form>
        </div>
    </div>

    <!-- Search Results Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title">Search Results</h5>
            <table class="table table-hover table-bordered">
                <thead class="thead-light">
                <tr>
                    <th>Bill No.</th>
                    <th>Title</th>
                    <th>Originating MDA</th>
                    <th>Submission Date</th>
                    <th>Status</th>
                    <th>Deadline</th>
                    <th>Public Participation</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <!-- Example Row -->
                <tr>
                    <td>LD/2025/001</td>
                    <td>Data Protection Amendment Bill</td>
                    <td>Ministry of ICT</td>
                    <td>2025-08-01</td>
                    <td><span class="badge badge-warning">Awaiting Public Participation</span></td>
                    <td>2025-08-30</td>
                    <td>
                        <button class="btn btn-sm btn-outline-info">
                            <i class="fa fa-users"></i> View Submissions (12)
                        </button>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <button class="btn btn-success"><i class="fa fa-check"></i> Approve</button>
                            <button class="btn btn-danger"><i class="fa fa-times"></i> Reject</button>
                            <button class="btn btn-secondary"><i class="fa fa-eye"></i> View</button>
                            <button class="btn btn-primary"><i class="fa fa-share"></i> Forward to Parliament</button>
                        </div>
                    </td>
                </tr>
                <!-- Repeat for more proposals -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Public Participation Section -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-info text-white">
            Public Participation Feedback Tracker
        </div>
        <div class="card-body">
            <p class="mb-3">Monitor submissions from the public regarding Bills under consideration.</p>
            <ul class="list-group">
                <li class="list-group-item">
                    <strong>Data Protection Amendment Bill</strong> — 12 submissions received, 8 supporting, 4 opposing.
                    <button class="btn btn-sm btn-outline-primary float-right">
                        <i class="fa fa-eye"></i> Review Feedback
                    </button>
                </li>
                <li class="list-group-item">
                    <strong>Environmental Management Bill</strong> — Public participation ongoing until 2025-09-15.
                    <button class="btn btn-sm btn-outline-primary float-right">
                        <i class="fa fa-eye"></i> Review Feedback
                    </button>
                </li>
            </ul>
        </div>
    </div>
</div>
