<div class="row no-margin col-md-12">
    <div class="row no-margin col-md-12">
        <div class="master-contract col-md-12 p-0" style="margin-top: 15px;">
            <div>
                <div class="card border-info">
                    <div class="card-body">
                        <div align="center">
                            <h4><?php echo  $this->lang->line("contracts"); ?></h4>
                        </div>

                        <?php if (empty($contracts)) {
                            ?>
                            <p>
                                <?php echo  $this->lang->line("no_records_found"); ?>
                                <a class="btn btn-default" href="javascript:;" onclick="contractGenerate('customer-portal');">
                                    <?php echo  $this->lang->line("request_mou"); ?>
                                </a>
                            </p>
                        <?php } else {?>
                            <div class="row no-margin col-md-12 p-0">
                                <a class="btn btn-default" href="javascript:;" onclick="contractGenerate('customer-portal');">
                                    <?php echo  $this->lang->line("request_mou"); ?>
                                </a>
                            </div>
                            <br/>
                            <div class="row no-margin col-md-12 p-0 padding-top-20">
                                <table class="table table-striped table-condensed table-hover" cellspacing="0" width="100%" id="contracts-table">
                                    <thead>
                                    <tr>
                                        <th><?php echo  $this->lang->line("ID"); ?></th>
                                        <th><?php echo  $this->lang->line("name"); ?></th>
                                        <th><?php echo  $this->lang->line("status"); ?></th>
                                        <th><?php echo  $this->lang->line("mou_type"); ?></th>
                                        <th><?php echo  $this->lang->line("reference_number"); ?></th>
                                        <th><?php echo  $this->lang->line("requested_by"); ?></th>
                                        <th><?php echo  $this->lang->line("assignee"); ?></th>
                                        <th><?php echo  $this->lang->line("lastUpdate"); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($contracts as $contract) {
                                        ?>
                                        <tr>
                                            <td>
                                                <a href="<?php echo  site_url("contracts/view/" . $contract["id"]); ?>">
                                                    <?php echo  $model_code . $contract["id"]; ?>
                                                </a>
                                            </td>
                                            <td><?php echo  htmlentities($contract["name"]); ?></td>
                                            <td><?php echo  htmlentities($contract["status"]); ?></td>
                                            <td><?php echo  htmlentities($contract["type"]); ?></td>
                                            <td><?php echo  htmlentities($contract["reference_number"]); ?></td>
                                            <td><?php echo  htmlentities($contract["requester_name"]); ?></td>
                                            <td><?php echo  htmlentities($contract["assignee"]); ?></td>
                                            <td><?php echo  $contract["modifiedOn"]; ?></td>
                                        </tr>
                                    <?php }; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php }; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    activateTabs();
</script>
