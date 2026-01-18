<div class="row no-margin col-md-12" id="cp-module-container">
    <div class="row no-margin col-md-12">
        <div class="master-contract col-md-12" style="margin-top: 15px;">
            <div class="card border-info">
                <div class="card-body">
                    <div class="row no-margin justify-content-center">
                        <h4><?= $this->lang->line("other_agreements_in_menu") ?></h4>
                    </div>
                    <br/>
                    <div class="row mb-4 mr-4 padding-top7 justify-content-end">
                        <a href="javascript:;" onclick="contractGenerate('customer-portal');" class="request-button">
                            <?= $this->lang->line("request_mou") ?>
                        </a>
                    </div>

                    <div class="row no-margin d-flex">
                        <!-- All Contracts -->
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="rectangle-div">
                                <div class="col-md-12 title"><?= $this->lang->line("list_mous") ?></div>
                                <a tabindex="-1" href="<?= site_url("contracts/all_contracts") ?>">
                                    <img src="assets/images/contract/all_contracts.svg" width="120" height="120">
                                </a>
                                <div class="row no-margin col-md-12 count-div">
                                    <div class="col-md-6 count-label"><?= $this->lang->line("mou_numbers") ?></div>
                                    <div class="col-md-6 count"><?= $count["all_contracts"] ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Awaiting Approvals -->
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="rectangle-div">
                                <div class="col-md-12 title"><?= $this->lang->line("awaiting_approvals") ?></div>
                                <a tabindex="-1" href="<?= site_url("contracts/awaiting_approvals") ?>">
                                    <img src="assets/images/contract/awaiting_approvals.svg" width="120" height="120">
                                </a>
                                <div class="row no-margin col-md-12 count-div">
                                    <div class="col-md-6 count-label"><?= $this->lang->line("mou_numbers") ?></div>
                                    <div class="col-md-6 count"><?= $count["awaiting_approvals"] ?></div>
                                </div>
                            </div>
                        </div>

                        <!-- Awaiting Signatures -->
                        <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                            <div class="rectangle-div">
                                <div class="col-md-12 title"><?= $this->lang->line("awaiting_signatures") ?></div>
                                <a tabindex="-1" href="<?= site_url("contracts/awaiting_signatures") ?>">
                                    <img src="assets/images/contract/awaiting_signatures.svg" width="120" height="120">
                                </a>
                                <div class="row no-margin col-md-12 count-div">
                                    <div class="col-md-6 count-label"><?= $this->lang->line("mou_numbers") ?></div>
                                    <div class="col-md-6 count"><?= $count["awaiting_signatures"] ?></div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end row -->
                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end master-contract -->
    </div>
</div>

<script>
    setActiveTab('contracts');
</script>
