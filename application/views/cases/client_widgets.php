<style>
    .fee-notes-container {
        position: absolute;
        top: 10px;
        right: 15px;
        z-index: 10;
    }
    .card {
        position: relative;
    }
</style>

<div id="client-account-status" class="col-md-12 row padding-10">
    <!-- Total Fees -->
    <div class="col-md-4">
        <div class="card shadow-sm border-left-primary">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-money fa-2x text-success"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Total Fees</h6>
                    <h4 class="mb-0 font-weight-bold text-dark" id="extCounsel-total_fees">
                        <span class="loader-submit loading"></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Amount Settled -->
    <div class="col-md-4">
        <div class="card shadow-sm border-left-success">
            <div class="fee-notes-container">
                <a href="javascript:;" id="view_fee_notes" onclick="list_matter_feenotes(<?php echo $legalCase['id']; ?>)">
                    <i class="fa fa-eye"></i> View Fee Notes
                </a>
            </div>
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-check-circle fa-2x text-primary"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Amount Settled</h6>
                    <h4 class="mb-0 font-weight-bold text-dark" id="extCounsel-amount_settled">
                        <span class="loader-submit loading"></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Balance Due -->
    <div class="col-md-4">
        <div class="card shadow-sm border-left-danger">
            <div class="card-body d-flex align-items-center">
                <div class="mr-3">
                    <i class="fa fa-balance-scale fa-2x text-danger"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Balance Due</h6>
                    <h4 class="mb-0 font-weight-bold text-dark" id="extCounsel-balance_due">
                        <span class="loader-submit loading"></span>
                    </h4>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function(){
        fetchClientAccountStatus(<?php echo $legalCase['id']; ?>);
    });
</script>