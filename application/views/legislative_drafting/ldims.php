<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3 class="mb-0">Legislation Requests</h3>
    <button id="btnNewRequest" class="btn btn-primary" data-toggle="modal" data-target="#newRequestModal">+ New Request</button>
</div>
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="requestsTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Reference No.</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>MCDA</th>
                    <th>Priority</th>
                    <th>Assigned To</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Due Date</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sampleData = [
                    [1,'REF-2025-001','Tax Reform Bill','Bill','Ministry of Finance','High','John Doe','Drafting','2025-08-01','2025-08-20','2025-08-05'],
                    [2,'REF-2025-002','Environmental Protection Act','Act','Ministry of Environment','Medium','Jane Smith','Review','2025-07-28','2025-08-15','2025-08-03'],
                    [3,'REF-2025-003','Public Health Amendment','Amendment','Ministry of Health','High','David Okello','Approved','2025-07-25','2025-08-10','2025-08-04'],
                    [4,'REF-2025-004','Education Policy Update','Policy','Ministry of Education','Low','Sarah Kamau','Drafting','2025-07-20','2025-08-25','2025-08-02'],
                    [5,'REF-2025-005','Transport Safety Bill','Bill','Ministry of Transport','Medium','Ali Hassan','Rejected','2025-07-18','2025-08-12','2025-08-01'],
                    [6,'REF-2025-006','Cybersecurity Regulation','Regulation','ICT Authority','High','Mary Atieno','Review','2025-07-15','2025-08-22','2025-08-04'],
                    [7,'REF-2025-007','Housing Development Plan','Plan','Ministry of Housing','Low','Peter Mwangi','Approved','2025-07-12','2025-08-18','2025-08-03'],
                    [8,'REF-2025-008','Agricultural Subsidy Bill','Bill','Ministry of Agriculture','Medium','Pauline Ouma','Drafting','2025-07-10','2025-08-16','2025-08-02'],
                    [9,'REF-2025-009','Water Resource Management Act','Act','Water Services Board','High','Chris Otieno','Review','2025-07-05','2025-08-14','2025-08-03'],
                    [10,'REF-2025-010','Energy Efficiency Program','Program','Ministry of Energy','Medium','Anne Wanjiku','Approved','2025-07-01','2025-08-19','2025-08-04']
                ];
                foreach($sampleData as $row){
                    $statusClass = 'badge-secondary';
                    if($row[7] == 'Drafting') $statusClass = 'badge-primary';
                    if($row[7] == 'Review') $statusClass = 'badge-warning';
                    if($row[7] == 'Approved') $statusClass = 'badge-success';
                    if($row[7] == 'Rejected') $statusClass = 'badge-danger';
                    echo "<tr>
                            <td>{$row[0]}</td>
                            <td>{$row[1]}</td>
                            <td>{$row[2]}</td>
                            <td>{$row[3]}</td>
                            <td>{$row[4]}</td>
                            <td>{$row[5]}</td>
                            <td>{$row[6]}</td>
                            <td><span class='status-badge badge {$statusClass}'>{$row[7]}</span></td>
                            <td>{$row[8]}</td>
                            <td>{$row[9]}</td>
                            <td>{$row[10]}</td>
                            <td>
                                <button class='btn btn-sm btn-info btn-sm-view' data-id='{$row[0]}'>View</button>
                                <button class='btn btn-sm btn-secondary btn-sm-assign' data-id='{$row[0]}'>Assign</button>
                            </td>
                        </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $('#requestsTable').DataTable({
            responsive: true,
            dom: 'Bfrtip',
            buttons: [ 'csvHtml5', 'excelHtml5', 'pdfHtml5', 'print' ]
        });
        $('#newRequestForm').on('submit', function(e){
            e.preventDefault();
            alert('This would submit to backend in production');
            $('#newRequestModal').modal('hide');
        });
        $('#requestsTable').on('click', '.btn-sm-view', function(){
            alert('Viewing details for request ID: ' + $(this).data('id'));
        });
        $('#requestsTable').on('click', '.btn-sm-assign', function(){
            alert('Assigning counsel for request ID: ' + $(this).data('id'));
        });
    });
</script>
