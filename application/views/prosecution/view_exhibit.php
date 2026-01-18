<style>
    .detail-label {
        font-weight: bold;
    }
</style>
<div class="exhibit-management-container">
    <!-- Header with Status Indicator -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Exhibit Details</h2>
        <span class="badge badge-pill badge-secondary" id="exhibitStatusBadge">ACTIVE</span>
    </div>
    <!-- Main Exhibit Card with Tabs -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <span id="exhibitDetailHeader">Exhibit S/No.: -</span>
                <small class="text-muted ml-2" id="exhibitLastUpdated">Last updated: -</small>
            </div>
            <div class="btn-group">
                <button class="btn btn-warning btn-sm rounded mr-2" id="editExhibitBtn" onclick="openEditExhibitModal(<?php echo $id?>)">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-danger btn-sm rounded mr-2" id="deleteExhibitBtn" onclick="openDeleteExhibitModal(<?php echo $id?>)" >
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button class="btn btn-info btn-sm rounded" id="changeStatusBtn" onclick="openStatusChangeModal(<?php echo $id?>)">
                    <i class="fas fa-exchange-alt"></i> Change Status
                </button>
            </div>
        </div>
        <div class="card-body">
            <!-- Exhibit Details Tabs -->
            <ul class="nav nav-tabs" id="exhibitTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">Details</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="documents-tab" data-toggle="tab" href="#documents" role="tab" aria-controls="documents" aria-selected="false"
                       onclick="loadDocumentsTab();">Documents</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" id="history-tab" data-toggle="tab" href="#history" role="tab" aria-controls="history" aria-selected="false"
                       onclick="loadHistoryTab();">History</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="chain-tab" data-toggle="tab" href="#chain" role="tab" aria-controls="chain" aria-selected="false"
                       onclick="loadChainOfCustodyTab();">Chain of Custody</a>
                </li>
            </ul>

            <div class="tab-content pt-3" id="exhibitTabContent">
                <!-- Details Tab -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="row">
                        <!-- Basic Information Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                                <div class="detail-row">
                                    <span class="detail-label">S/no.:</span>
                                    <span class="detail-value" id="detail_sno">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Label/name:</span>
                                    <span class="detail-value" id="detail_label_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Description:</span>
                                    <span class="detail-value" id="detail_description_of_exhibit">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Current status:</span>
                                    <span class="detail-value" id="detail_current_status">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Current location:</span>
                                    <span class="detail-value" id="detail_current_location">-</span>
                                </div>
                            </div>

                            <div class="detail-section mt-4">
                                <h5><i class="fas fa-calendar-alt"></i> Dates</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Date received:</span>
                                    <span class="detail-value" id="detail_date_received">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Date disposed:</span>
                                    <span class="detail-value" id="detail_date_disposed">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Case Information Column -->
                        <div class="col-md-6">
                            <div class="detail-section">
                                <h5><i class="fas fa-gavel"></i> Case Information</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Case reference:</span>
                                    <span class="detail-value" id="detail_caseReference">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Case subject:</span>
                                    <span class="detail-value" id="detail_case_subject_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Court:</span>
                                    <span class="detail-value" id="detail_court_name">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Clients:</span>
                                    <span class="detail-value" id="detail_clients">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Opponents:</span>
                                    <span class="detail-value" id="detail_opponents">-</span>
                                </div>
                            </div>

                            <div class="detail-section mt-4">
                                <h5><i class="fas fa-user-shield"></i> Personnel</h5>
                                <div class="detail-row">
                                    <span class="detail-label">Officers involved:</span>
                                    <span class="detail-value" id="detail_officers_involved">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Created by:</span>
                                    <span class="detail-value" id="detail_created_by">-</span>
                                </div>
                                <div class="detail-row">
                                    <span class="detail-label">Last modified by:</span>
                                    <span class="detail-value" id="detail_modified_by">-</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Documents Tab -->
                <div class="tab-pane fade" id="documents" role="tabpanel" aria-labelledby="documents-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Associated Documents</h5>
                        <button class="btn btn-primary btn-sm rounded" id="addDocumentBtn" onclick="openAddDocumentModal(<?php echo $id?>)">
                            <i class="fas fa-plus"></i> Add Document
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="documentsTable">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Date Added</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No documents associated</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>


                <!-- History Tab -->
                <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Activity History</h5>
                        <button class="btn btn-primary btn-sm rounded" id="addNoteBtn" onclick="openAddNoteModal(<?php echo $id?>)">
                            <i class="fas fa-plus"></i> Add Note
                        </button>
                    </div>
                    

                    <div class="timeline" id="exhibitTimeline">
                        <div class="timeline-item">
                            <div class="timeline-point"></div>
                            <div class="timeline-content">
                                <div class="timeline-date">No activity recorded</div>
                                <div class="timeline-text text-muted">This exhibit has no recorded activity</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chain of Custody Tab -->
                <div class="tab-pane fade" id="chain" role="tabpanel" aria-labelledby="chain-tab">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5>Chain of Custody</h5>
                        <button class="btn btn-info btn-sm rounded" id="addCustodyBtn" onclick="openAddCustodyModal(<?php echo $id?>)">
                            <i class="fas fa-plus"></i> Record Transfer
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover" id="custodyTable">
                            <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Purpose</th>
                                <th>Notes</th>
                                <th>Recorded By</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td colspan="6" class="text-center text-muted">No custody transfers recorded</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">

    // Dummy Exhibit Data
    const dummyExhibitData = {
        sno: "EXH-"+ <?php echo $id?>,
        label_name: <?php echo json_encode($exhibit_label)?>,
        description_of_exhibit:  <?php echo json_encode($description)?>,
        current_status:  <?php echo json_encode($exhibit_status)?>,
        current_location:  <?php echo json_encode($current_location_name)?>,
        date_received:<?php echo json_encode($date_received)?>,
        date_disposed: <?php echo json_encode($date_disposed)?>, // Empty for demonstration
        caseReference: <?php echo json_encode($caseReference)?>,
        case_subject_name: <?php echo json_encode($caseSubject)?>,
        court_name: <?php echo json_encode("")?>,
        clients: <?php echo json_encode("")?>,
        opponents:<?php echo json_encode("")?>,
        officers_involved: <?php echo json_encode($officer_name)?>,
        created_by:<?php echo json_encode($createdBy)?>,
        modified_by: <?php echo json_encode($modifiedByName)?>,
        last_updated: <?php echo json_encode($modifiedOn)?>,
        documents: [
            { name: "Search Warrant.pdf", type: "PDF", size: "2.5 MB", dateAdded: "2023-01-16", addedBy: "Admin User" },
            { name: "Evidence Log.xlsx", type: "Excel", size: "0.8 MB", dateAdded: "2023-01-17", addedBy: "Det. Jane Smith" },
            { name: "Forensic Report.docx", type: "Word", size: "1.2 MB", dateAdded: "2023-02-01", addedBy: "Dr. Lee" }
        ],

        history: [
            { date: "2023-01-15 10:00", text: "Exhibit received and logged.", recordedBy: "Admin User" },
            { date: "2023-01-20 09:00", text: "Transferred to Forensics Lab for analysis.", recordedBy: "Det. Jane Kamau" },
            { date: "2023-02-05 16:00", text: "Analysis completed. Report generated.", recordedBy: "Inspector JM Kariul" },
            { date: "2023-02-10 11:30", text: "Exhibit returned to evidence locker.", recordedBy: "Admin User" }
        ],
        chain_of_custody: [
            { dateTime: "2023-01-15 10:00", from: "Crime Scene", to: "Evidence Locker A (Admin User)", purpose: "Initial Receipt", notes: "Collected from 123 Main St.", recordedBy: "Admin User" },
            { dateTime: "2023-01-20 09:00", from: "Evidence Locker A (Admin User)", to: "Forensics Lab (Dr. Lee)", purpose: "Forensic Analysis", notes: "For data extraction.", recordedBy: "Det. Jane Smith" },
            { dateTime: "2023-02-10 11:00", from: "Forensics Lab (Dr. Lee)", to: "Evidence Locker A (Admin User)", purpose: "Return after Analysis", notes: "Analysis complete.", recordedBy: "Dr. Lee" }
        ]
    };

    // Function to simulate an AJAX request
    function simulateAjax(data, delay = 300) {
        return new Promise(resolve => {
            setTimeout(() => {
                resolve(data);
            }, delay);
        });
    }

    // Function to populate the Details tab
    async function loadDetailsTab() {
        try {
            const data = await simulateAjax(dummyExhibitData);

            document.getElementById('exhibitStatusBadge').textContent = data.current_status || '-';
            document.getElementById('exhibitStatusBadge').className = `badge badge-pill badge-${data.current_status === 'ACTIVE' ? 'success' : data.current_status === 'DISPOSED' ? 'danger' : 'secondary'}`;
            document.getElementById('exhibitDetailHeader').textContent = `Exhibit S/No.: ${data.sno || '-'}`;
            document.getElementById('exhibitLastUpdated').textContent = `Last updated: ${data.last_updated || '-'}`;

            document.getElementById('detail_sno').textContent = data.sno || '-';
            document.getElementById('detail_label_name').textContent = data.label_name || '-';
            document.getElementById('detail_description_of_exhibit').textContent = data.description_of_exhibit || '-';
            document.getElementById('detail_current_status').textContent = data.current_status || '-';
            document.getElementById('detail_current_location').textContent = data.current_location || '-';
            document.getElementById('detail_date_received').textContent = data.date_received || '-';
            document.getElementById('detail_date_disposed').textContent = data.date_disposed || '-';
            document.getElementById('detail_caseReference').textContent = data.caseReference || '-';
            document.getElementById('detail_case_subject_name').textContent = data.case_subject_name || '-';
            document.getElementById('detail_court_name').textContent = data.court_name || '-';
            document.getElementById('detail_clients').textContent = data.clients || '-';
            document.getElementById('detail_opponents').textContent = data.opponents || '-';
            document.getElementById('detail_officers_involved').textContent = data.officers_involved || '-';
            document.getElementById('detail_created_by').textContent = data.created_by || '-';
            document.getElementById('detail_modified_by').textContent = data.modified_by || '-';

    

        } catch (error) {
            console.error("Error loading exhibit details:", error);
        }
    }

    // Function to populate the Documents tab
    async function loadDocumentsTab() {
        try {
            const data = await simulateAjax(dummyExhibitData.documents);
            const tableBody = document.querySelector('#documentsTable tbody');
            tableBody.innerHTML = ''; // Clear existing rows

            if (data && data.length > 0) {
                data.forEach(doc => {
                    const row = tableBody.insertRow();
                    row.innerHTML = `
                    <td>${doc.name || '-'}</td>
                    <td>${doc.type || '-'}</td>
                    <td>${doc.size || '-'}</td>
                    <td>${doc.dateAdded || '-'}</td>
                    <td>${doc.addedBy || '-'}</td>
                    <td>
                        <button class="btn btn-sm btn-primary rounded mr-1 view-document-btn"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-danger rounded delete-document-btn"><i class="fas fa-trash"></i></button>
                    </td>
                `;
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No documents associated</td></tr>`;
            }
        } catch (error) {
            console.error("Error loading documents:", error);
        }
    }



    // Function to populate the History tab
    async function loadHistoryTab() {
        try {
            const data = await simulateAjax(dummyExhibitData.history);
            const timeline = document.getElementById('exhibitTimeline');
            timeline.innerHTML = ''; // Clear existing history

            if (data && data.length > 0) {
                data.forEach(item => {
                    const timelineItem = document.createElement('div');
                    timelineItem.className = 'timeline-item';
                    timelineItem.innerHTML = `
                    <div class="timeline-point"></div>
                    <div class="timeline-content">
                        <div class="timeline-date">${item.date || '-'}</div>
                        <div class="timeline-text">${item.text || '-'}</div>
                        <small class="text-muted">Recorded by: ${item.recordedBy || '-'}</small>
                    </div>
                `;
                    timeline.appendChild(timelineItem);
                });
            } else {
                timeline.innerHTML = `
                <div class="timeline-item">
                    <div class="timeline-point"></div>
                    <div class="timeline-content">
                        <div class="timeline-date">No activity recorded</div>
                        <div class="timeline-text text-muted">This exhibit has no recorded activity</div>
                    </div>
                </div>
            `;
            }
        } catch (error) {
            console.error("Error loading history:", error);
        }
    }

    // Function to populate the Chain of Custody tab
    async function loadChainOfCustodyTab() {
        try {
            const data = await simulateAjax(dummyExhibitData.chain_of_custody);
            const tableBody = document.querySelector('#custodyTable tbody');
            tableBody.innerHTML = ''; // Clear existing rows

            if (data && data.length > 0) {
                data.forEach(transfer => {
                    const row = tableBody.insertRow();
                    row.innerHTML = `
                    <td>${transfer.dateTime || '-'}</td>
                    <td>${transfer.from || '-'}</td>
                    <td>${transfer.to || '-'}</td>
                    <td>${transfer.purpose || '-'}</td>
                    <td>${transfer.notes || '-'}</td>
                    <td>${transfer.recordedBy || '-'}</td>
                `;
                });
            } else {
                tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No custody transfers recorded</td></tr>`;
            }
        } catch (error) {
            console.error("Error loading chain of custody:", error);
        }
    }

          
jQuery(document).ready(function () {

    //  flatpickr(".datetimepicker", {
    //     enableTime: true,
    //     dateFormat: "Y-m-d H:i",
    //     time_24hr: true
    // });

        // Initial load of the Details tab
        loadDetailsTab();

        // Ensure jQuery is loaded before attempting to use $
        if (typeof jQuery !== 'undefined') {
            $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
                const targetTabId = $(e.target).attr('href'); // activated tab
                switch (targetTabId) {
                    case '#details':
                        loadDetailsTab();
                        break;
                    case '#documents':
                        loadDocumentsTab();
                        break;
                    case '#history':
                        loadHistoryTab();
                        break;
                    case '#chain':
                        loadChainOfCustodyTab();
                        break;
                }
            });
        } else {
            console.warn("jQuery is not loaded. Tab functionality may not work as expected.");
        }
              
    });

</script>