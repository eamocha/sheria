<style>
    .k-grid {
        font-size: 14px;
    }

</style>

<div class="container-fluid mt-3">
    <h2>Incoming Correspondence</h2>

    <!-- Filter Panel -->
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="search">Search:</label>
                                <input type="text" class="form-control filter-input" id="search" placeholder="Search by subject, sender, etc...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_from">Date From:</label>
                                <input type="date" class="form-control filter-input" id="date_from">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_to">Date To:</label>
                                <input type="date" class="form-control filter-input" id="date_to">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="source">Source:</label>
                                <select id="source" class="form-control filter-input">
                                    <option value="">All Sources</option>
                                    <option value="Safaricom">Safaricom</option>
                                    <option value="Airtel">Airtel</option>
                                    <option value="KeBS">KeBS</option>
                                    <option value="Internal">Internal</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="doc_type">Document Type:</label>
                                <select id="doc_type" class="form-control filter-input">
                                    <option value="">All Types</option>
                                    <option value="Letter">Letter</option>
                                    <option value="MOU">MOU</option>
                                    <option value="Complaint">Complaint</option>
                                    <option value="Memo">Memo</option>
                                    <option value="Email">Email</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="assigned_to">Assigned To:</label>
                                <select id="assigned_to" class="form-control filter-input">
                                    <option value="">All Assignees</option>
                                    <option value="Mercy Kioko">Mercy Kioko</option>
                                    <option value="Francis Okutoyi">Francis Okutoyi</option>
                                    <option value="Beatrice Mumbi">Beatrice Mumbi</option>
                                    <option value="John Doe">John Doe</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="action_required">Action Required:</label>
                                <select id="action_required" class="form-control filter-input">
                                    <option value="">All Actions</option>
                                    <option value="Review">Review</option>
                                    <option value="Sign">Sign</option>
                                    <option value="Action">Action</option>
                                    <option value="Response">Response</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="priority">Priority:</label>
                                <select id="priority" class="form-control filter-input">
                                    <option value="">All Priorities</option>
                                    <option value="High">High</option>
                                    <option value="Medium">Medium</option>
                                    <option value="Low">Low</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="requires_signature">Requires Signature:</label>
                                <select id="requires_signature" class="form-control filter-input">
                                    <option value="">All</option>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="status">Status:</label>
                                <select id="status" class="form-control filter-input">
                                    <option value="">All Statuses</option>
                                    <option value="Received">Received</option>
                                    <option value="In Review">In Review</option>
                                    <option value="Pending Signature">Pending Signature</option>
                                    <option value="Closed">Closed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <button id="reset-filters" class="btn btn-default k-button" style="margin-top: 25px;">
                                <span class="k-icon k-i-refresh"></span> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kendo Grid -->
    <div id="correspondenceGrid"></div>
</div>
