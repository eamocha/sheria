<style>
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
    }
    .status-Pending {
        background-color: #fff3cd;
        color: #856404;
    }
    .status-In-progress {
        background-color: #cce5ff;
        color: #004085;
    }
    .status-Completed {
        background-color: #d4edda;
        color: #155724;
    }
    .status-Delayed {
        background-color: #f8d7da;
        color: #721c24;
    }
    #instruments-table {
        font-size: 0.9rem; /* Adjust this value as needed for desired size */
        width: 100%; /* Make the table stretch to 100% of its container */
    }
    /* Rule to prevent wrapping in the first column */
    #instruments-table td:first-child,
    #instruments-table th:first-child {
        white-space: nowrap;
    }
    /* Optional: Style for the dropdown toggle in the table */
    #instruments-table .dropdown-toggle {
        padding: 0; /* Remove default button padding */
        border: none; /* Remove default button border */
        background: none; /* Remove default button background */
        color: #007bff; /* Bootstrap primary blue for links */
        text-decoration: none; /* Ensure it looks like a link */
    }
    #instruments-table .dropdown-toggle::after {
        display: none; /* Hide the default Bootstrap dropdown arrow */
    }
    /* Add a custom icon for the dropdown */
    #instruments-table .dropdown-toggle .fa-chevron-down {
        font-size: 0.7em; /* Smaller arrow */
        vertical-align: middle;
        margin-left: 5px;
    }
</style>

<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Conveyancing Instruments</span>
                    <button class="btn btn-primary"  onclick="conveyancingInstrumentForm('customer-portal','add')">
                        <i class="fas fa-plus"></i> <?php echo $this->lang->line("add") ?>
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-condensed table-hover " id="instruments-table">
                            <thead class="thead-light">
                            <tr>
                                <th>ID.</th>
                                <th>Instrument </th>
                                <th>Title</th>
                                <th>Transaction </th>
                                <th>Staff</th>
                                <th>Staff .</th>
                                <th>Property Value</th>
                                <th>Approved</th>
                                <th>Initiated </th>
                                <th>Status</th>
                                <th>Archived</th>
                                <th>Last Updated</th>

                            </tr>
                            </thead>
                            <tbody>
                            <?php if (isset($instruments)){
                                foreach($instruments as $instrument){

                                    ?>
                                    <tr>
                                        <td>
                                            <div class="dropdown">
                                                <a class="dropdown-toggle text-primary text-decoration-none" href="" role="button" id="dropdownMenuLink_<?php echo $instrument["id"]; ?>" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <?php echo $instrument["conveyancing_id"]?> <i class="fas fa-chevron-down"></i>
                                                </a>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuLink_<?php echo $instrument["id"]; ?>">
                                                    <a class="dropdown-item" href="<?php echo site_url('conveyancing/view/'.$instrument["id"]) ?>">View</a>
                                                    <a class="dropdown-item" href="javascript:;" onclick="conveyancingInstrumentForm('customer-portal','edit',<?php echo $instrument["id"]?>)">Edit</a>
                                                    <a class="dropdown-item text-danger" href="javascript:void(0);" onclick="deleteConveyancingInstrument('<?php echo $instrument["id"]?>')">Delete</a>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo $instrument["instrument_type"]?></td>
                                        <td> <a href="<?php echo site_url('conveyancing/view/'.$instrument["id"]) ?>"><?php echo $instrument["title"]?></a></td>
                                        <td><?php echo $instrument["transaction_type_name"]?></td>
                                        <td><?php echo $instrument["staff"]?></td>
                                        <td><?php echo $instrument["staff_pf_no"] ?></td>
                                        <td><?php echo $instrument["property_value"] ?></td>
                                        <td><?php echo $instrument["amount_approved"]?></td>
                                        <td><?php echo $instrument["date_initiated"]?></td>
                                        <td><span class="status-badge status-<?php echo $instrument["status"] ?>"><?php echo $instrument["status"]?></span></td>
                                        <td><?php echo $instrument["archived"]?></td>
                                        <td><?php echo $instrument["modifiedOn"]?></td>


                                    </tr>
                                <?php     }
                            } else{?>
                                <tr>
                                    <td colspan="10">
                                        No  records found
                                    </td>
                                </tr><?php }?>
                            </tbody>
                        </table>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script>
    activateTabs();
    // Update file input label
    $('.custom-file-input').on('change', function() {
        let fileName = $(this).val().split('\\').pop();
        $(this).next('.custom-file-label').addClass("selected").html(fileName || 'Choose files...');
    });

    function deleteConveyancingInstrument(id) {
            jQuery.ajax({
                url: '<?php echo site_url("conveyancing/delete"); ?>', // Adjust this URL if your delete endpoint is different
                type: 'POST',
                dataType: 'JSON',
                data: { instrumentId: id }, // Ensure your backend expects 'instrumentId'
                success: function (response) {
                    var ty = 'error';
                    var m = '';
                    switch (response.status) {
                        case 202: // removed successfully
                            ty = 'information';
                            m = 'Record deleted successfully.';
                            location.reload();
                            break;
                        case 101: // could not remove record
                            m = 'Could not delete record. Not Allowed';
                            break;
                        case 303: // could not remove record, related to many objects
                            m = 'Cannot delete record: it is linked to other data.';
                            break;
                        default:
                            m = 'An unknown error occurred.';
                            break;
                    }
                    pinesMessage({ty: ty, m: m}); // Assuming pinesMessage is available
                },
                error: defaultAjaxJSONErrorsHandler // Assuming defaultAjaxJSONErrorsHandler is available
            });
    }

</script>