<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Legislative Drafting Workflow with Communication</h3>
</div>
<div class="timeline">
    <!-- Step 1: Drafting -->
    <div class="timeline-item">
        <span class="timeline-badge bg-primary"><i class="fas fa-file-alt"></i></span>
        <div class="timeline-content">
            <h6>Drafting</h6>
            <p class="small text-muted mb-1">Initial drafting of the legislative document by Parliamentary Counsel.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-primary">Share Draft</button>
                <button class="btn btn-sm btn-outline-secondary">Message Requester</button>
            </div>
        </div>
    </div>
    <!-- Step 2: Reviewing -->
    <div class="timeline-item">
        <span class="timeline-badge bg-info"><i class="fas fa-search"></i></span>
        <div class="timeline-content">
            <h6>Reviewing Draft</h6>
            <p class="small text-muted mb-1">Peer review to ensure accuracy, compliance, and quality.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-info">Send for Review</button>
                <button class="btn btn-sm btn-outline-secondary">Request Clarification</button>
            </div>
        </div>
    </div>
    <!-- Step 3: Awaiting Feedback -->
    <div class="timeline-item">
        <span class="timeline-badge bg-warning"><i class="fas fa-hourglass-half"></i></span>
        <div class="timeline-content">
            <h6>Awaiting Feedback</h6>
            <p class="small text-muted mb-1">Waiting for requester’s feedback or approval before proceeding.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-warning text-white">Send Reminder</button>
                <button class="btn btn-sm btn-outline-secondary">Chat with Requester</button>
            </div>
        </div>
    </div>
    <!-- Step 4: Finalizing -->
    <div class="timeline-item">
        <span class="timeline-badge bg-success"><i class="fas fa-check-circle"></i></span>
        <div class="timeline-content">
            <h6>Finalizing</h6>
            <p class="small text-muted mb-1">Final changes implemented and document prepared for sign-off.</p>
            <div class="mt-2">
                <button class="btn btn-sm btn-success">Send Final Draft</button>
                <button class="btn btn-sm btn-outline-secondary">Update Requester</button>
            </div>
        </div>
    </div>
</div>

<style>
    .timeline {
        position: relative;
        margin-left: 2rem;
        padding-left: 1rem;
        border-left: 2px solid #dee2e6;
    }
    .timeline-item {
        position: relative;
        margin-bottom: 2rem;
    }
    .timeline-badge {
        position: absolute;
        left: -30px;
        top: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
    }
    .timeline-content {
        background: #fff;
        padding: 1rem;
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>

<!-- Audit Trail: Access & Edit Logs -->
<div class="container-fluid mt-4">
    <h3 class="mb-3 fw-bold">Audit Trail & Access Logs</h3>
    <p class="mb-3">Track who accessed or edited legal opinions to ensure accountability and transparency. Use filters to drill down by user, action, or date range.</p>

    <!-- Filters / Toolbar -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form class="form-row">
                <div class="form-group col-md-3 mb-2">
                    <label class="mb-1">Date From</label>
                    <input type="date" id="fltFrom" class="form-control">
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label class="mb-1">Date To</label>
                    <input type="date" id="fltTo" class="form-control">
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label class="mb-1">User</label>
                    <select id="fltUser" class="form-control">
                        <option value="">All Users</option>
                        <option>Amina Njoroge</option>
                        <option>Brian Otieno</option>
                        <option>Wanjiru Kamau</option>
                        <option>Peter Ochieng</option>
                        <option>Grace Wambui</option>
                        <option>Samuel Kilonzo</option>
                    </select>
                </div>
                <div class="form-group col-md-3 mb-2">
                    <label class="mb-1">Action</label>
                    <select id="fltAction" class="form-control">
                        <option value="">All Actions</option>
                        <option>VIEW</option>
                        <option>EDIT</option>
                        <option>DOWNLOAD</option>
                        <option>EXPORT</option>
                        <option>COMPARE</option>
                        <option>SHARE</option>
                        <option>LOGIN</option>
                    </select>
                </div>
                <div class="form-group col-md-9 mb-2">
                    <label class="mb-1">Quick Search</label>
                    <input type="text" id="fltSearch" class="form-control" placeholder="Search by opinion ID, details, IP, device...">
                </div>
                <div class="form-group col-md-3 mb-2 d-flex align-items-end">
                    <button type="button" id="btnApplyFilters" class="btn btn-outline-secondary mr-2">Apply</button>
                    <button type="button" id="btnResetFilters" class="btn btn-light">Reset</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Audit Table -->
    <div class="card shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive mb-0">
                <table class="table table-bordered mb-0" id="auditTable">
                    <thead class="table-light">
                    <tr>
                        <th>Timestamp</th>
                        <th>User</th>
                        <th>Role</th>
                        <th>Opinion ID</th>
                        <th>Action</th>
                        <th>Details</th>
                        <th>IP / Device</th>
                        <th>Outcome</th>
                        <th>Controls</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>2025-08-24 09:12</td>
                        <td>Amina Njoroge</td>
                        <td>Parliamentary Counsel</td>
                        <td>OPN-2025-0143</td>
                        <td>EDIT</td>
                        <td>Updated Section 3.2; saved as v2.1</td>
                        <td>102.68.14.22 / Chrome</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewVersion('v2.1')">View</button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="compareVersions('v2.0','v2.1')">Compare</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-24 08:47</td>
                        <td>Brian Otieno</td>
                        <td>Reviewer</td>
                        <td>OPN-2025-0143</td>
                        <td>VIEW</td>
                        <td>Viewed marked-up draft</td>
                        <td>41.90.112.5 / Firefox</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="openLog('OPN-2025-0143')">Log</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-23 16:05</td>
                        <td>Wanjiru Kamau</td>
                        <td>Administrator</td>
                        <td>OPN-2025-0109</td>
                        <td>EXPORT</td>
                        <td>Exported PDF (redacted)</td>
                        <td>105.163.9.18 / Edge</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="revokeExport('OPN-2025-0109')">Revoke</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-23 11:22</td>
                        <td>Peter Ochieng</td>
                        <td>Parliamentary Counsel</td>
                        <td>OPN-2025-0098</td>
                        <td>COMPARE</td>
                        <td>Compared v1.3 → v1.4</td>
                        <td>196.201.220.33 / Chrome</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="compareVersions('v1.3','v1.4')">Reopen</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-22 15:40</td>
                        <td>Grace Wambui</td>
                        <td>Reviewer</td>
                        <td>OPN-2025-0070</td>
                        <td>EDIT</td>
                        <td>Inline comment added on Clause 5</td>
                        <td>41.72.56.7 / Safari</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewComments('OPN-2025-0070')">Comments</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-22 10:03</td>
                        <td>Samuel Kilonzo</td>
                        <td>Auditor</td>
                        <td>OPN-2025-0044</td>
                        <td>VIEW</td>
                        <td>Accessed final approved version</td>
                        <td>62.24.115.90 / Chrome</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="openLog('OPN-2025-0044')">Log</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-21 18:27</td>
                        <td>Fatuma Abdalla</td>
                        <td>Parliamentary Counsel</td>
                        <td>OPN-2025-0032</td>
                        <td>DOWNLOAD</td>
                        <td>Downloaded DOCX (internal)</td>
                        <td>197.232.2.14 / Chrome</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="revokeDownload('OPN-2025-0032')">Revoke</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-21 09:14</td>
                        <td>Kevin Mwangi</td>
                        <td>Reviewer</td>
                        <td>OPN-2025-0020</td>
                        <td>EDIT</td>
                        <td>Resolved 2 comments; saved as v1.2</td>
                        <td>105.161.40.201 / Firefox</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="compareVersions('v1.1','v1.2')">Compare</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-20 14:31</td>
                        <td>Rose Atieno</td>
                        <td>Records Officer</td>
                        <td>OPN-2025-0011</td>
                        <td>SHARE</td>
                        <td>Shared with MOJ (read-only link)</td>
                        <td>41.206.16.75 / Chrome</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="revokeShare('OPN-2025-0011')">Revoke</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-20 08:05</td>
                        <td>Daniel Cheruiyot</td>
                        <td>System Admin</td>
                        <td>OPN-2025-0009</td>
                        <td>LOGIN</td>
                        <td>MFA verified; session started</td>
                        <td>154.70.120.8 / Edge</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="openSecurity('OPN-2025-0009')">Session</button>
                        </td>
                    </tr>
                    <tr>
                        <td>2025-08-19 17:18</td>
                        <td>Njeri Gachanja</td>
                        <td>Parliamentary Counsel</td>
                        <td>OPN-2025-0002</td>
                        <td>EDIT</td>
                        <td>Amended citations; saved as v1.1</td>
                        <td>102.135.11.4 / Safari</td>
                        <td>Success</td>
                        <td>
                            <button class="btn btn-sm btn-outline-secondary" onclick="compareVersions('v1.0','v1.1')">Compare</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    // Minimal demo handlers
    function viewVersion(v){ alert('Open version: ' + v); }
    function compareVersions(a,b){ alert('Compare ' + a + ' vs ' + b); }
    function openLog(id){ alert('Open full log for ' + id); }
    function revokeExport(id){ alert('Revoke export permissions for ' + id); }
    function revokeDownload(id){ alert('Revoke download for ' + id); }
    function revokeShare(id){ alert('Revoke external share for ' + id); }
    function viewComments(id){ alert('Open comments for ' + id); }
    function openSecurity(id){ alert('Open security/session details for ' + id); }

    // Simple client-side filter (works even without DataTables/Kendo)
    (function(){
        var tbl = document.getElementById('auditTable').getElementsByTagName('tbody')[0];
        function apply(){
            var q = (document.getElementById('fltSearch').value || '').toLowerCase();
            var u = document.getElementById('fltUser').value;
            var a = document.getElementById('fltAction').value;
            var from = document.getElementById('fltFrom').value;
            var to = document.getElementById('fltTo').value;

            Array.prototype.forEach.call(tbl.rows, function(row){
                var text = row.innerText.toLowerCase();
                var user = row.cells[1].innerText;
                var action = row.cells[4].innerText;
                var ts = row.cells[0].innerText.replace(' ', 'T');
                var show = true;

                if(q && text.indexOf(q) === -1) show = false;
                if(u && user !== u) show = false;
                if(a && action !== a) show = false;
                if(from && (new Date(ts) < new Date(from))) show = false;
                if(to && (new Date(ts) > new Date(to + 'T23:59'))) show = false;

                row.style.display = show ? '' : 'none';
            });
        }
        document.getElementById('btnApplyFilters').addEventListener('click', apply);
        document.getElementById('btnResetFilters').addEventListener('click', function(){
            ['fltFrom','fltTo','fltUser','fltAction','fltSearch'].forEach(function(id){
                var el = document.getElementById(id); if(!el) return; if(el.tagName==='SELECT') el.selectedIndex = 0; else el.value='';
            });
            apply();
        });
    })();
</script>
