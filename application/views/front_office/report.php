
     <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <style>
        .report-selection {
            margin-bottom: 20px;
        }

        .report-parameter {
            margin-bottom: 20px;
            padding: 15px;
            border: 1px solid #f0f0f0;
            border-radius: 5px;
            background-color: #fff;
        }

        .report-viewer {
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #f0f0f0;
            border-radius: 5px;
            background-color: #fff;
        }

        .date-range-group {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .date-range-group label {
            margin-right: 10px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .report-table th,
        .report-table td {
            padding: 8px;
            border: 1px solid #ddd;
        }

        .report-table th {
            background-color: #f0f0f0;
        }

        .report-actions {
            margin-top: 10px;
            display: flex;
            gap: 10px;
        }
    </style>

<div class="container-fluid mt-3">
    <h2>Front office Reporting </h2>

    <div class="report-selection">
        <h4>Select Report:</h4>
        <div class="list-group">
            <button class="list-group-item list-group-item-action" data-report="correspondence_logs">Correspondence Logs
                (Incoming & Outgoing)</button>
            <button class="list-group-item list-group-item-action" data-report="status_reports">Status Reports
                (including signing/review)</button>
            <button class="list-group-item list-group-item-action" data-report="turnaround_time">Turnaround Time
                Analysis</button>
            <button class="list-group-item list-group-item-action" data-report="file_movement_history">File Movement
                History</button>
            <button class="list-group-item list-group-item-action" data-report="user_activity_logs">User Activity
                Logs</button>
        </div>
    </div>

    <div class="report-parameter" id="report-parameters" style="display: none;">
        <h4>Report Parameters</h4>
        <div class="date-range-group">
            <label>Date Range:</label>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="date_range_type" id="monthly" value="monthly">
                <label class="form-check-label" for="monthly">Monthly</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="date_range_type" id="yearly" value="yearly">
                <label class="form-check-label" for="yearly">Yearly</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="date_range_type" id="custom" value="custom">
                <label class="form-check-label" for="custom">Custom</label>
            </div>
        </div>

        <div class="form-row" id="custom_date_range" style="display: none;">
            <div class="form-group col-md-6">
                <label for="start_date">Start Date:</label>
                <input type="text" id="start_date" class="form-control datepicker">
            </div>
            <div class="form-group col-md-6">
                <label for="end_date">End Date:</label>
                <input type="text" id="end_date" class="form-control datepicker">
            </div>
        </div>

        <div id="report-specific-filters">
        </div>

        <button id="generate-report" class="btn btn-primary">Generate Report</button>
    </div>

    <div class="report-viewer" id="report-viewer" style="display: none;">
        <h4>Report Viewer</h4>
        <div id="report-content">
        </div>
        <div class="report-actions">
            <button class="btn btn-secondary" onclick="window.print()">Print</button>
            <button class="btn btn-success" id="export-excel">Export to Excel</button>
            <button class="btn btn-outline-primary" id="save-report">Save Report</button>
        </div>
    </div>
</div>



     <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.js"></script>
     <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
     <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
     <script>
         $(document).ready(function () {
             // Initialize datepickers
             jQuery('.datepicker').flatpickr({
                 dateFormat: "Y-m-d",
             });

             // Report Selection
             jQuery('.list-group-item').on('click', function () {
                 jQuery('.list-group-item').removeClass('active');
                 jQuery(this).addClass('active');
                 var reportType = jQuery(this).data('report');
                 jQuery('#report-parameters').hide();
                 jQuery('#report-specific-filters').empty(); // Clear any previous filters
                 jQuery('#report-viewer').hide();

                 if (reportType === 'correspondence_logs') {
                     loadCorrespondenceLogFilters();
                 } else if (reportType === 'status_reports') {
                     loadStatusReportFilters();
                 } else if (reportType === 'turnaround_time') {
                     loadTurnaroundTimeFilters();
                 } else if (reportType === 'file_movement_history') {
                     loadFileMovementHistoryFilters();
                 } else if (reportType === 'user_activity_logs') {
                     loadUserActivityLogsFilters();
                 }
                 jQuery('#report-parameters').show();
             });

             // Date Range Selection
             jQuery('input[name="date_range_type"]').on('change', function () {
                 if (jQuery(this).val() === 'custom') {
                     jQuery('#custom_date_range').show();
                 } else {
                     jQuery('#custom_date_range').hide();
                 }
             });

             // Generate Report Button
             jQuery('#generate-report').on('click', function () {
                 var reportType = jQuery('.list-group-item.active').data('report');
                 if (!reportType) {
                     alert('Please select a report type.');
                     return;
                 }
                 jQuery('#report-viewer').show();
                 jQuery('#report-content').empty();

                 if (reportType === 'correspondence_logs') {
                     generateCorrespondenceLogsReport();
                 } else if (reportType === 'status_reports') {
                     generateStatusReport();
                 } else if (reportType === 'turnaround_time') {
                     generateTurnaroundTimeAnalysis();
                 } else if (reportType === 'file_movement_history') {
                     generateFileMovementHistory();
                 } else if (reportType === 'user_activity_logs') {
                     generateUserActivityLogs();
                 }
             });

             // Function to load correspondence log filters
             function loadCorrespondenceLogFilters() {
                 var filters = `
                    <div class="form-row">
                        <div class="form-group col-md-4">
                            <label for="log_type">Log Type:</label>
                            <select id="log_type" class="form-control">
                                <option value="">All</option>
                                <option value="incoming">Incoming</option>
                                <option value="outgoing">Outgoing</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="log_status">Status:</label>
                            <select id="log_status" class="form-control">
                                <option value="">All</option>
                                <option value="Received">Received</option>
                                <option value="In Review">In Review</option>
                                <option value="Pending Signature">Pending Signature</option>
                                <option value="Sent">Sent</option>
                                 <option value="Dispatched">Dispatched</option>
                                <option value="Closed">Closed</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label for="log_doc_type">Document Type:</label>
                            <select id="log_doc_type" class="form-control">
                                <option value="">All</option>
                                 <option value="Letter">Letter</option>
                                <option value="Memo">Memo</option>
                                <option value="Report">Report</option>
                                <option value="Contract">Contract</option>
                                <option value="MOU">MOU</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                `;
                 jQuery('#report-specific-filters').html(filters);
             }

             // Function to load status report filters
             function loadStatusReportFilters() {
                 var filters = `
                    <div class="form-group">
                        <label for="status_type">Status Type:</label>
                        <select id="status_type" class="form-control">
                            <option value="">All</option>
                            <option value="correspondence">Correspondence</option>
                            <option value="signature">Signature/Review</option>
                        </select>
                    </div>
                `;
                 jQuery('#report-specific-filters').html(filters);
             }

             // Function to load turnaround time filters
             function loadTurnaroundTimeFilters() {
                 var filters = `
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="turnaround_by">Turnaround By:</label>
                            <select id="turnaround_by" class="form-control">
                                <option value="">All</option>
                                <option value="department">Department</option>
                                <option value="user">User</option>
                                 <option value="document_type">Document Type</option>
                            </select>
                        </div>
                         <div class="form-group col-md-6">
                            <label for="turnaround_direction">Direction:</label>
                            <select id="turnaround_direction" class="form-control">
                                <option value="">All</option>
                                <option value="incoming">Incoming</option>
                                <option value="outgoing">Outgoing</option>
                            </select>
                        </div>
                    </div>
                `;
                 jQuery('#report-specific-filters').html(filters);
             }

             // Function to load file movement history filters
             function loadFileMovementHistoryFilters() {
                 var filters = `
                    <div class="form-group">
                        <label for="file_name">File Name:</label>
                        <input type="text" id="file_name" class="form-control">
                    </div>
                `;
                 jQuery('#report-specific-filters').html(filters);
             }

             // Function to load user activity logs filters
             function loadUserActivityLogsFilters() {
                 var filters = `
                    <div class="form-group">
                        <label for="activity_type">Activity Type:</label>
                        <select id="activity_type" class="form-control">
                            <option value="">All</option>
                            <option value="login">Login</option>
                            <option value="create">Create</option>
                            <option value="update">Update</option>
                            <option value="delete">Delete</option>
                            <option value="file_access">File Access</option>
                        </select>
                    </div>
                `;
                 jQuery('#report-specific-filters').html(filters);
             }

             function generateCorrespondenceLogsReport() {
                 var logType = jQuery('#log_type').val();
                 var status = jQuery('#log_status').val();
                 var docType = jQuery('#log_doc_type').val();
                 var startDate = jQuery('#start_date').val();
                 var endDate = jQuery('#end_date').val();
                 var dateRangeType = jQuery('input[name="date_range_type"]:checked').val();


                 var reportData = [
                     { date: '2025-05-01', type: 'Incoming', serial: 'IC-2025-001', source: 'Airtel', subject: 'License Renewal', status: 'In Review' },
                     { date: '2025-04-28', type: 'Outgoing', serial: 'OC-2025-002', source: 'Telkom', subject: 'Payment Reminder', status: 'Sent' },
                     { date: '2025-05-02', type: 'Incoming', serial: 'IC-2025-003', source: 'Safaricom', subject: 'Meeting Request', status: 'Received' },
                     { date: '2025-04-29', type: 'Outgoing', serial: 'OC-2025-004', source: 'Ministry of ICT', subject: 'Policy Update', status: 'Dispatched' },
                 ];

                 var filteredData = reportData.filter(item => {
                     var matchesType = !logType || item.type.toLowerCase().includes(logType.toLowerCase());
                     var matchesStatus = !status || item.status.toLowerCase().includes(status.toLowerCase());
                     var matchesDocType = !docType || item.docType.toLowerCase().includes(docType.toLowerCase());

                     var matchesDate = true;
                     if (dateRangeType === 'monthly') {
                         var reportMonth = item.date.substring(0, 7);
                         var selectedMonth = startDate.substring(0, 7);
                         matchesDate = matchesDate && reportMonth === selectedMonth;
                     } else if (dateRangeType === 'yearly') {
                         var reportYear = item.date.substring(0, 4);
                         var selectedYear = startDate.substring(0, 4);
                         matchesDate = matchesDate && reportYear === selectedYear;
                     }
                     else if (dateRangeType === 'custom') {
                         matchesDate = matchesDate && (!startDate || item.date >= startDate) && (!endDate || item.date <= endDate);
                     }

                     return matchesType && matchesStatus && matchesDocType && matchesDate;
                 });

                 if (filteredData.length === 0) {
                     jQuery('#report-content').html('<p>No matching records found.</p>');
                     return;
                 }

                 var html = '<table class="report-table"><thead><tr><th>Date</th><th>Type</th><th>Serial Number</th><th>Source</th><th>Subject</th><th>Status</th></tr></thead><tbody>';
                 filteredData.forEach(item => {
                     html += `<tr><td>${item.date}</td><td>${item.type}</td><td>${item.serial}</td><td>${item.source}</td><td>${item.subject}</td><td>${item.status}</td></tr>`;
                 });
                 html += '</tbody></table>';
                 jQuery('#report-content').html(html);
             }

             function generateStatusReport() {
                 var statusType = jQuery('#status_type').val();
                 var startDate = jQuery('#start_date').val();
                 var endDate = jQuery('#end_date').val();
                 var dateRangeType = jQuery('input[name="date_range_type"]:checked').val();

                 var reportData = [
                     { date: '2025-05-01', type: 'Correspondence', item: 'IC-2025-001', status: 'In Review', signatureStatus: 'Pending' },
                     { date: '2025-04-28', type: 'Correspondence', item: 'OC-2025-002', status: 'Sent', signatureStatus: 'Not Required' },
                     { date: '2025-05-02', type: 'Correspondence', item: 'IC-2025-003', status: 'Received', signatureStatus: 'Pending' },
                     { date: '2025-04-29', type: 'Correspondence', item: 'OC-2025-004', status: 'Dispatched', signatureStatus: 'Not Required' },
                     { date: '2025-05-05', type: 'Signature', item: 'MOU with KeBS', status: 'Pending Signature', signatureStatus: 'Pending' },
                     { date: '2025-05-06', type: 'Signature', item: 'Contract Agreement', status: 'Signed', signatureStatus: 'Signed' },
                 ];

                 var filteredData = reportData.filter(item => {
                     var matchesType = !statusType || (statusType === 'correspondence' && item.type === 'Correspondence') ||
                         (statusType === 'signature' && item.type === 'Signature');

                     var matchesDate = true;
                     if (dateRangeType === 'monthly') {
                         var reportMonth = item.date.substring(0, 7);
                         var selectedMonth = startDate.substring(0, 7);
                         matchesDate = matchesDate && reportMonth === selectedMonth;
                     } else if (dateRangeType === 'yearly') {
                         var reportYear = item.date.substring(0, 4);
                         var selectedYear = startDate.substring(0, 4);
                         matchesDate = matchesDate && reportYear === selectedYear;
                     }
                     else if (dateRangeType === 'custom') {
                         matchesDate = matchesDate && (!startDate || item.date >= startDate) && (!endDate || item.date <= endDate);
                     }
                     return matchesType && matchesDate;
                 });

                 if (filteredData.length === 0) {
                     jQuery('#report-content').html('<p>No matching records found.</p>');
                     return;
                 }

                 var html = '<table class="report-table"><thead><tr><th>Date</th><th>Type</th><th>Item</th><th>Status</th><th>Signature Status</th></tr></thead><tbody>';
                 filteredData.forEach(item => {
                     html += `<tr><td>${item.date}</td><td>${item.type}</td><td>${item.item}</td><td>${item.status}</td><td>${item.signatureStatus}</td></tr>`;
                 });
                 html += '</tbody></table>';
                 jQuery('#report-content').html(html);
             }

             function generateTurnaroundTimeAnalysis() {
                 var turnaroundBy = jQuery('#turnaround_by').val();
                 var startDate = jQuery('#start_date').val();
                 var endDate = jQuery('#end_date').val();
                 var dateRangeType = jQuery('input[name="date_range_type"]:checked').val();
                 var direction = jQuery('#turnaround_direction').val();

                 var reportData = [
                     { dateReceived: '2025-05-01', dateActioned: '2025-05-03', type: 'Incoming', department: 'Legal', user: 'John Doe', documentType: 'Letter', timeTaken: 2 },
                     { dateReceived: '2025-04-28', dateActioned: '2025-05-02', type: 'Outgoing', department: 'Finance', user: 'Jane Smith', documentType: 'Memo', timeTaken: 4 },
                     { dateReceived: '2025-05-02', dateActioned: '2025-05-04', type: 'Incoming', department: 'Legal', user: 'John Doe', documentType: 'Letter', timeTaken: 2 },
                     { dateReceived: '2025-04-29', dateActioned: '2025-05-05', type: 'Outgoing', department: 'HR', user: 'David Lee', documentType: 'Report', timeTaken: 6 },
                     { dateReceived: '2025-05-03', dateActioned: '2025-05-08', type: 'Incoming', department: 'Legal', user: 'Jane Smith', documentType: 'MOU', timeTaken: 5 },
                 ];

                 var filteredData = reportData.filter(item => {

                     var matchesDirection = !direction || item.type.toLowerCase() === direction.toLowerCase();
                     var matchesDate = true;
                     if (dateRangeType === 'monthly') {
                         var reportMonth = item.dateReceived.substring(0, 7);
                         var selectedMonth = startDate.substring(0, 7);
                         matchesDate = matchesDate && reportMonth === selectedMonth;
                     } else if (dateRangeType === 'yearly') {
                         var reportYear = item.dateReceived.substring(0, 4);
                         var selectedYear = startDate.substring(0, 4);
                         matchesDate = matchesDate && reportYear === selectedYear;
                     }
                     else if (dateRangeType === 'custom') {
                         matchesDate = matchesDate && (!startDate || item.dateReceived >= startDate) && (!endDate || item.dateReceived <= endDate);
                     }
                     return matchesDirection && matchesDate;
                 });

                 var html = '';
                 if (turnaroundBy === 'department') {
                     html = generateTurnaroundByDepartment(filteredData);
                 } else if (turnaroundBy === 'user') {
                     html = generateTurnaroundByUser(filteredData);
                 }  else if (turnaroundBy === 'document_type'){
                     html = generateTurnaroundByDocType(filteredData);
                 }
                 else {
                     html = generateTurnaroundOverall(filteredData);
                 }
                 jQuery('#report-content').html(html);
             }

             function generateTurnaroundOverall(data) {
                 if (data.length === 0) {
                     return '<p>No data available for turnaround time analysis.</p>';
                 }
                 var totalTime = 0;
                 data.forEach(item => {
                     totalTime += item.timeTaken;
                 });
                 var averageTime = totalTime / data.length;
                 return `<p>Average Turnaround Time: ${averageTime.toFixed(2)} days</p>`;
             }

             function generateTurnaroundByDepartment(data) {
                 if (data.length === 0) {
                     return '<p>No data available for turnaround time analysis.</p>';
                 }
                 var departmentData = {};
                 data.forEach(item => {
                     if (!departmentData[item.department]) {
                         departmentData[item.department] = { totalTime: 0, count: 0 };
                     }
                     departmentData[item.department].totalTime += item.timeTaken;
                     departmentData[item.department].count++;
                 });

                 var html = '<table class="report-table"><thead><tr><th>Department</th><th>Average Turnaround Time</th></tr></thead><tbody>';
                 for (var dept in departmentData) {
                     var averageTime = departmentData[dept].totalTime / departmentData[dept].count;
                     html += `<tr><td>${dept}</td><td>${averageTime.toFixed(2)} days</td></tr>`;
                 }
                 html += '</tbody></table>';
                 return html;
             }

             function generateTurnaroundByUser(data) {
                 if (data.length === 0) {
                     return '<p>No data available for turnaround time analysis.</p>';
                 }
                 var userData = {};
                 data.forEach(item => {
                     if (!userData[item.user]) {
                         userData[item.user] = { totalTime: 0, count: 0 };
                     }
                     userData[item.user].totalTime += item.timeTaken;
                     userData[item.user].count++;
                 });

                 var html = '<table class="report-table"><thead><tr><th>User</th><th>Average Turnaround Time</th></tr></thead><tbody>';
                 for (var user in userData) {
                     var averageTime = userData[user].totalTime / userData[user].count;
                     html += `<tr><td>${user}</td><td>${averageTime.toFixed(2)} days</td></tr>`;
                 }
                 html += '</tbody></table>';
                 return html;
             }

             function generateTurnaroundByDocType(data) {
                 if (data.length === 0) {
                     return '<p>No data available for turnaround time analysis.</p>';
                 }
                 var docTypeData = {};
                 data.forEach(item => {
                     if (!docTypeData[item.documentType]) {
                         docTypeData[item.documentType] = { totalTime: 0, count: 0 };
                     }
                     docTypeData[item.documentType].totalTime += item.timeTaken;
                     docTypeData[item.documentType].count++;
                 });

                 var html = '<table class="report-table"><thead><tr><th>Document Type</th><th>Average Turnaround Time</th></tr></thead><tbody>';
                 for (var docType in docTypeData) {
                     var averageTime = docTypeData[docType].totalTime / docTypeData[docType].count;
                     html += `<tr><td>${docType}</td><td>${averageTime.toFixed(2)} days</td></tr>`;
                 }
                 html += '</tbody></table>';
                 return html;
             }

             function generateFileMovementHistory() {
                 var fileName = jQuery('#file_name').val();
                 var startDate = jQuery('#start_date').val();
                 var endDate = jQuery('#end_date').val();
                 var dateRangeType = jQuery('input[name="date_range_type"]:checked').val();

                 var reportData = [
                     { file: 'Contract Agreement 001.pdf', checkedOutTo: 'John Doe', dateCheckedOut: '2025-05-01', dateReturned: '2025-05-05', location: 'Records Room A' },
                     { file: 'MOU Project X.docx', checkedOutTo: 'Jane Smith', dateCheckedOut: '2025-04-28', dateReturned: null, location: 'Records Room B' },
                     { file: 'Budget Proposal 2025.xlsx', checkedOutTo: 'David Lee', dateCheckedOut: '2025-05-03', dateReturned: '2025-05-04', location: 'Finance Department' },
                 ];

                 var filteredData = reportData.filter(item => {
                     var matchesFileName = !fileName || item.file.toLowerCase().includes(fileName.toLowerCase());
                     var matchesDate = true;
                     if (dateRangeType === 'monthly') {
                         var reportMonth = item.dateCheckedOut.substring(0, 7);
                         var selectedMonth = startDate.substring(0, 7);
                         matchesDate = matchesDate && reportMonth === selectedMonth;
                     } else if (dateRangeType === 'yearly') {
                         var reportYear = item.dateCheckedOut.substring(0, 4);
                         var selectedYear = startDate.substring(0, 4);
                         matchesDate = matchesDate && reportYear === selectedYear;
                     }
                     else if (dateRangeType === 'custom') {
                         matchesDate = matchesDate && (!startDate || item.dateCheckedOut >= startDate) && (!endDate || item.dateCheckedOut <= endDate);
                     }
                     return matchesFileName && matchesDate;
                 });

                 if (filteredData.length === 0) {
                     jQuery('#report-content').html('<p>No matching records found.</p>');
                     return;
                 }
                 var html = '<table class="report-table"><thead><tr><th>File Name</th><th>Checked Out To</th><th>Date Checked Out</th><th>Date Returned</th><th>Location</th></tr></thead><tbody>';
                 filteredData.forEach(item => {
                     html += `<tr><td>${item.file}</td><td>${item.checkedOutTo}</td><td>${item.dateCheckedOut}</td><td>${item.dateReturned || '-'}</td><td>${item.location}</td></tr>`;
                 });
                 html += '</tbody></table>';
                 jQuery('#report-content').html(html);
             }

             function generateUserActivityLogs() {
                 var activityType = jQuery('#activity_type').val();
                 var startDate = jQuery('#start_date').val();
                 var endDate = jQuery('#end_date').val();
                 var dateRangeType = jQuery('input[name="date_range_type"]:checked').val();

                 var reportData = [
                     { user: 'John Doe', activity: 'Login', timestamp: '2025-05-08 09:00:00' },
                     { user: 'Jane Smith', activity: 'Create', timestamp: '2025-05-08 10:30:00' },
                     { user: 'John Doe', activity: 'File Access', timestamp: '2025-05-08 11:00:00' },
                     { user: 'David Lee', activity: 'Update', timestamp: '2025-05-08 12:45:00' },
                     { user: 'Jane Smith', activity: 'Delete', timestamp: '2025-05-08 14:00:00' },
                 ];

                 var filteredData = reportData.filter(item => {
                     var matchesActivity = !activityType || item.activity.toLowerCase().includes(activityType.toLowerCase());
                     var matchesDate = true;
                     if (dateRangeType === 'monthly') {
                         var reportMonth = item.timestamp.substring(0, 7);
                         var selectedMonth = startDate.substring(0, 7);
                         matchesDate = matchesDate && reportMonth === selectedMonth;
                     } else if (dateRangeType === 'yearly') {
                         var reportYear = item.timestamp.substring(0, 4);
                         var selectedYear = startDate.substring(0, 4);
                         matchesDate = matchesDate && reportYear === selectedYear;
                     }
                     else if (dateRangeType === 'custom') {
                         matchesDate = matchesDate && (!startDate || item.timestamp >= startDate) && (!endDate || item.timestamp <= endDate);
                     }
                     return matchesActivity && matchesDate;
                 });

                 if (filteredData.length === 0) {
                     jQuery('#report-content').html('<p>No matching records found.</p>');
                     return;
                 }

                 var html = '<table class="report-table"><thead><tr><th>User</th><th>Activity</th><th>Timestamp</th></tr></thead><tbody>';
                 filteredData.forEach(item => {
                     html += `<tr><td>${item.user}</td><td>${item.activity}</td><td>${item.timestamp}</td></tr>`;
                 });
                 html += '</tbody></table>';
                 jQuery('#report-content').html(html);
             }

// Export to Excel
             jQuery('#export-excel').on('click', function () {
                 var reportType = jQuery('.list-group-item.active').data('report');
                 if (!reportType) {
                     alert('Please select a report type to export.');
                     return;
                 }

                 var table = jQuery('#report-content table')[0];
                 if (!table) {
                     alert('No report data to export.');
                     return;
                 }

                 var wb = XLSX.utils.table_to_book(table);
                 XLSX.writeFile(wb, 'report.xlsx');
             });

// Save Report
             jQuery('#save-report').on('click', function () {
                 var reportContent = jQuery('#report-content').html();
                 if (!reportContent) {
                     alert('No report to save.');
                     return;
                 }
// In a real application, you would send this data to the server to save it.
                 alert('Report content saved! (Data would be sent to server)');
                 console.log('Report Content:', reportContent);
             });
         });
     </script>
