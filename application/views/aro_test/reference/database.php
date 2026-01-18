<div class="container-fluid py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">ARO Database Browser</h1>
            <p class="text-muted mb-0">Search and browse Advocate's Remuneration Order provisions</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-outline-primary">
                <i class="bi bi-download me-1"></i> Export
            </button>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#searchHelpModal">
                <i class="bi bi-question-circle me-1"></i> Search Help
            </button>
        </div>
    </div>

    <!-- Search Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Search ARO provisions, schedules, or specific rules...">
                        <button class="btn btn-primary" type="button">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select class="form-select">
                        <option value="">All Schedules</option>
                        <option>Schedule 1 - Civil Matters</option>
                        <option>Schedule 2 - Conveyancing</option>
                        <option>Schedule 3 - Probate</option>
                        <option>Schedule 4 - Commercial</option>
                        <option>Schedule 5 - Miscellaneous</option>
                    </select>
                </div>
            </div>
            <div class="mt-3">
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="searchTitles" checked>
                    <label class="form-check-label" for="searchTitles">Titles</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="searchContent" checked>
                    <label class="form-check-label" for="searchContent">Content</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="searchNotes">
                    <label class="form-check-label" for="searchNotes">Annotations</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" id="searchPrecedents">
                    <label class="form-check-label" for="searchPrecedents">Precedents</label>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Search Results -->
        <div class="col-lg-8">
            <!-- Quick Filters -->
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <button class="btn btn-outline-primary btn-sm active">All Provisions</button>
                <button class="btn btn-outline-primary btn-sm">Instruction Fees</button>
                <button class="btn btn-outline-primary btn-sm">Getting-Up Fees</button>
                <button class="btn btn-outline-primary btn-sm">Conveyancing</button>
                <button class="btn btn-outline-primary btn-sm">Probate</button>
                <button class="btn btn-outline-primary btn-sm">Commercial</button>
                <button class="btn btn-outline-primary btn-sm">Recently Updated</button>
            </div>

            <!-- Search Results -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">ARO Provisions (247 results)</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary active">Relevance</button>
                        <button class="btn btn-outline-secondary">Schedule</button>
                        <button class="btn btn-outline-secondary">Date</button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <!-- Result 1 -->
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="mb-1">
                                    <span class="badge bg-primary me-2">Schedule 1</span>
                                    Instruction Fee in Civil Suits
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-calculator"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-2">For money suits and civil litigation matters where the value of the subject matter can be ascertained.</p>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Calculation Method:</strong> Slab-based percentage</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Effective Date:</strong> January 1, 2024</small>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded small">
                                <h6 class="mb-2">Fee Structure:</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>0 - 1M:</strong> 7.5%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>1M - 5M:</strong> 5.0%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>5M - 10M:</strong> 1.44%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Above 10M:</strong> 0.72%
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <span class="badge bg-success me-1">Active</span>
                                <span class="badge bg-info me-1">Frequently Used</span>
                                <span class="badge bg-warning">12 Related Precedents</span>
                            </div>
                        </div>

                        <!-- Result 2 -->
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="mb-1">
                                    <span class="badge bg-success me-2">Schedule 2</span>
                                    Conveyancing - Sale and Purchase
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-calculator"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-2">For agreements for sale, purchase, and transfer of property including preparation of necessary documents.</p>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Calculation Method:</strong> Slab-based percentage</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Effective Date:</strong> January 1, 2024</small>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded small">
                                <h6 class="mb-2">Fee Structure:</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <strong>0 - 1M:</strong> 1.5%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>1M - 5M:</strong> 1.0%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>5M - 10M:</strong> 0.72%
                                    </div>
                                    <div class="col-md-3">
                                        <strong>Above 10M:</strong> 0.5%
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <span class="badge bg-success me-1">Active</span>
                                <span class="badge bg-info me-1">Frequently Used</span>
                                <span class="badge bg-warning">8 Related Precedents</span>
                            </div>
                        </div>

                        <!-- Result 3 -->
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="mb-1">
                                    <span class="badge bg-warning me-2">Schedule 3</span>
                                    Probate and Administration
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-calculator"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-2">For obtaining grants of probate and letters of administration, including estate administration.</p>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Calculation Method:</strong> Slab-based percentage</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Effective Date:</strong> January 1, 2024</small>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded small">
                                <h6 class="mb-2">Fee Structure:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>0 - 1M:</strong> 4.0%
                                    </div>
                                    <div class="col-md-4">
                                        <strong>1M - 5M:</strong> 3.0%
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Above 5M:</strong> 2.0%
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <span class="badge bg-success me-1">Active</span>
                                <span class="badge bg-secondary me-1">Medium Usage</span>
                                <span class="badge bg-warning">5 Related Precedents</span>
                            </div>
                        </div>

                        <!-- Result 4 -->
                        <div class="list-group-item">
                            <div class="d-flex w-100 justify-content-between align-items-start mb-2">
                                <h5 class="mb-1">
                                    <span class="badge bg-info me-2">Schedule 4</span>
                                    Commercial Agreement Drafting
                                </h5>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-bookmark"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-calculator"></i>
                                    </button>
                                    <button class="btn btn-outline-primary">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <p class="mb-2">For drafting commercial agreements, contracts, and related business documents.</p>

                            <div class="row g-3 mb-2">
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Calculation Method:</strong> Fixed amount with complexity factors</small>
                                </div>
                                <div class="col-md-6">
                                    <small class="text-muted"><strong>Effective Date:</strong> February 1, 2024</small>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded small">
                                <h6 class="mb-2">Fee Structure:</h6>
                                <div class="row">
                                    <div class="col-md-4">
                                        <strong>Simple:</strong> KES 15,000
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Medium:</strong> KES 25,000 - 50,000
                                    </div>
                                    <div class="col-md-4">
                                        <strong>Complex:</strong> KES 50,000 - 100,000
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <span class="badge bg-success me-1">Active</span>
                                <span class="badge bg-warning me-1">New Rule</span>
                                <span class="badge bg-warning">2 Related Precedents</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <nav aria-label="Search results pagination">
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

        <!-- Right Column - Filters & Tools -->
        <div class="col-lg-4">
            <!-- Schedule Navigation -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">ARO Schedules</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Schedule 1 - Civil Matters
                            <span class="badge bg-primary rounded-pill">84</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Schedule 2 - Conveyancing
                            <span class="badge bg-success rounded-pill">56</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Schedule 3 - Probate
                            <span class="badge bg-warning rounded-pill">42</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Schedule 4 - Commercial
                            <span class="badge bg-info rounded-pill">38</span>
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            Schedule 5 - Miscellaneous
                            <span class="badge bg-secondary rounded-pill">27</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Annotations -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Annotations</h5>
                    <button class="btn btn-sm btn-outline-primary">View All</button>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-primary rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Today, 11:30</small>
                                    <p class="mb-0 small">Added note to Instruction Fee about uplift limitations</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker bg-success rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Yesterday, 16:45</small>
                                    <p class="mb-0 small">Updated commercial drafting fees based on new precedent</p>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="d-flex">
                                <div class="timeline-marker bg-info rounded-circle me-3" style="width: 8px; height: 8px; margin-top: 6px;"></div>
                                <div>
                                    <small class="text-muted">Dec 15, 2023</small>
                                    <p class="mb-0 small">Added cross-reference to related probate provisions</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Tools -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Quick Tools</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-journal-plus me-2"></i>Add New Annotation
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-bookmark-check me-2"></i>My Bookmarked Provisions
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-clock-history me-2"></i>View History
                        </button>
                        <button class="btn btn-outline-primary text-start">
                            <i class="bi bi-download me-2"></i>Download ARO PDF
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Search Help Modal -->
<div class="modal fade" id="searchHelpModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ARO Database Search Help</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <h6>Search Tips:</h6>
                <ul>
                    <li>Use specific terms like "instruction fee civil" or "conveyancing sale"</li>
                    <li>Search by schedule number: "schedule 1" or "S1"</li>
                    <li>Use quotation marks for exact phrases</li>
                    <li>Filter by schedule using the dropdown menu</li>
                </ul>

                <h6 class="mt-3">Available Filters:</h6>
                <div class="row">
                    <div class="col-md-6">
                        <strong>By Schedule:</strong>
                        <ul class="small">
                            <li>Schedule 1 - Civil Matters</li>
                            <li>Schedule 2 - Conveyancing</li>
                            <li>Schedule 3 - Probate</li>
                            <li>Schedule 4 - Commercial</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <strong>By Fee Type:</strong>
                        <ul class="small">
                            <li>Instruction Fees</li>
                            <li>Getting-Up Fees</li>
                            <li>Attendance Fees</li>
                            <li>Drafting Fees</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>