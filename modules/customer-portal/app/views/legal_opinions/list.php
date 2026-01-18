
    <style>
        .request-card {
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .request-card:hover {
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .status-badge {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
        }
        .progress-tracker {
            position: relative;
            height: 30px;
            margin-bottom: 20px;
        }
        .progress-step {
            position: absolute;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #6c757d;
            font-weight: bold;
            z-index: 2;
        }
        .progress-step.active {
            background-color: #007bff;
            color: white;
        }
        .progress-step.completed {
            background-color: #28a745;
            color: white;
        }
        .progress-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 2px;
            background-color: #e9ecef;
            z-index: 1;
        }
        .progress-line-fill {
            position: absolute;
            top: 50%;
            left: 0;
            height: 2px;
            background-color: #28a745;
            z-index: 1;
        }
        .comment-box {
            border-left: 3px solid #007bff;
            background-color: #f8f9fa;
        }
        .attachment-item {
            padding: 8px;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 5px;
        }
    </style>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <!-- Requests List -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Legal Opinions Management</h5>
                    <button class="btn btn-primary" data-toggle="modal" onclick="addLegalOpinionRequest('customer-portal',0,false)" data-target="#addRequestModal">
                        <i class="fas fa-plus"></i> Add New Request
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="opinions-table">
                            <thead>
                            <tr>
                                <th>Ref No.</th>
                                <th>Subject</th>
                                <th>Request Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php  foreach ($opinions as $item){?>
                            <tr class="request-row" >
                                <td><?php echo $item["opinionId"];?></td>
                                <td><?php echo $item["title"]?></td>
                                <td><?php echo $item["createdOn"];?></td>
                                <td><?php echo $item["due_date"]?></td>
                                <td><?php echo $item["opinionStatus"];?></td>
                                <td><?php echo $item["assigned_to"];?></td>
                                <td>
                                    <button class="btn btn-sm btn-primary" data-toggle="tooltip" title="Edit" onclick=" fetchLegalOpinionItem(<?php echo $item['id'] ?>);">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr><?php  }?>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>




<script>    activateTabs();</script>



