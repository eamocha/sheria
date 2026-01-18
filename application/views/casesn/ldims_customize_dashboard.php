<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet">
<div class="container-fluid my-4">
    <h2 class="mb-3"><i class="fas fa-tachometer-alt"></i> Legislative Work Dashboard</h2>
    <p class="lead">
        Your role-tailored command center for legislative drafting, research, and workflow management.
    </p>

    <!-- Role Switcher -->
    <div class="mb-3">
        <label for="roleSelect"><strong>Switch Role View:</strong></label>
        <select id="roleSelect" class="form-control w-auto d-inline-block">
            <option>Attorney</option>
            <option>Researcher</option>
            <option>Administrator</option>
        </select>
    </div>

    <div class="row">
        <!-- Pending Documents -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-hourglass-half"></i> Pending Documents
                </div>
                <div class="card-body">
                    <h3>12</h3>
                    <p>Awaiting your review or action.</p>
                    <button class="btn btn-outline-primary btn-sm">View All</button>
                </div>
            </div>
        </div>

        <!-- Recent Drafts -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-file-alt"></i> Recent Legislative Drafts
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-2">
                        <li>Bill on Data Protection (Amendment)</li>
                        <li>Environmental Regulation Draft</li>
                        <li>Tax Reform Proposal</li>
                    </ul>
                    <button class="btn btn-outline-success btn-sm">Open Drafts</button>
                </div>
            </div>
        </div>

        <!-- Historical Documents -->
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <i class="fas fa-archive"></i> Historical Records
                </div>
                <div class="card-body">
                    <p>Search through archived legislation and past matters.</p>
                    <button class="btn btn-outline-secondary btn-sm">Search Archives</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popular Searches & Active Matters -->
    <div class="row mt-4">
        <!-- Active Matters -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white">
                    <i class="fas fa-briefcase"></i> Active Matters
                </div>
                <div class="card-body">
                    <ul>
                        <li>Land Use Bill – Committee Stage</li>
                        <li>Digital Trade Law – Public Participation</li>
                    </ul>
                    <button class="btn btn-outline-info btn-sm">Manage Matters</button>
                </div>
            </div>
        </div>

        <!-- Popular Searches -->
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white">
                    <i class="fas fa-search"></i> Popular Searches
                </div>
                <div class="card-body">
                    <ol>
                        <li>Public Procurement Regulations</li>
                        <li>Freedom of Information Act</li>
                        <li>Tax Code Amendments</li>
                    </ol>
                    <button class="btn btn-outline-warning btn-sm">Explore More</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
