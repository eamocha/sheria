<?php $this->load->view("partial/header"); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <ul class="breadcrumb">
                <li class="breadcrumb-item"><a href="dashboard/admin"><?php echo $this->lang->line("administration"); ?></a></li>
                <li class="breadcrumb-item active"><?php echo $this->lang->line("case_type"); ?></li>
                <li class="breadcrumb-item"><a href="<?php echo site_url("case_types/add"); ?>"><?php echo $this->lang->line("add_case_type"); ?></a></li>
            </ul>
        </div>
        <div class="col-md-12 no-padding">
            <div class="col-md-12 form-group row" id="pagination">
                <div class="col-md-6 no-padding col-xs-12">
                    <h4><?php echo $this->lang->line("total_records"); ?>: <?php echo $this->case_type->get("paginationTotalRows"); ?></h4>
                </div>
                <?php $links = $this->case_type->get("paginationLinks"); ?>
                <?php if (!empty($links)) { ?>
                    <div class="col-md-6 no-padding col-xs-12" id="pagination">
                        <ul class="pagination pull-right">
                            <?php echo $links; ?>
                        </ul>
                    </div>
                <?php } ?>
                <?php unset($links); ?>
            </div>
            <?php if (count($records) > 0) { ?>
                <div class="col-md-12 form-group">
                    <div class="dropdown more pull-right margin-right10">
                        <a href="" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                            <i class="icon fa fa-cog"></i> <span class="caret no-margin"></span>
                        </a>
                        <div aria-labelledby="dLabel" role="menu" class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="<?php echo site_url("export/case_types"); ?>">
                                <?php echo $this->lang->line("export_to_excel"); ?>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <tr>
                            <th><?php echo $this->lang->line("name"); ?></th>
                            <th><?php echo $this->lang->line("corporate_matter"); ?></th>
                            <th><?php echo $this->lang->line("litigation_case"); ?></th>
                            <th><?php echo $this->lang->line("criminal_case"); ?></th>
                            <th class="toolTipSLA">
                                <div class="d-flex justify-content-between border-0">
                                    <label><?php echo $this->lang->line("sla_number_of_days"); ?></label>
                                    <span class="tooltip-title cursor-pointer-click" title="<?php echo $this->lang->line("toolTipSLA"); ?>" data-toggle="tooltip">
                                        <i class="fa fa-question-circle"></i>
                                    </span>
                                </div>
                            </th>
                            <th><?php echo $this->lang->line("edit"); ?></th>
                            <th><?php echo $this->lang->line("delete"); ?></th>
                        </tr>
                        <?php foreach ($records as $record) { ?>
                            <tr>
                                <td><?php echo $record["name"]; ?></td>
                                <td><?php echo $record["corporate"]; ?></td>
                                <td><?php echo $record["litigation"]; ?></td>
                                <td><?php echo $record["criminal"]; ?></td>
                                <td>
                                    <?php if ($record["litigationSLA"] != NULL) { ?>
                                        <?php echo $this->lang->line("default"); ?> (<?php echo $record["litigationSLA"]; ?>)
                                    <?php } ?>
                                    <?php if (count($record["case_type_due_conditions"]) != 0 && $record["litigationSLA"] != 0) { ?>,<?php } ?>
                                    <?php $isLayoutRTL = $this->is_auth->is_layout_rtl(); ?>
                                    <?php foreach ($record["case_type_due_conditions"] as $key => $value) { ?>
                                        <?php if (!$isLayoutRTL) { ?>
                                            <?php echo $value["clientData"]["type"] == "all" ? $this->lang->line("all_clients") : $value["clientData"]["clientName"]; ?>
                                            - <?php echo $value["priority"] == "all" ? $this->lang->line("all_priorities") : $value["priority"]; ?>
                                            (<?php echo $value["due_in"]; ?>)
                                        <?php } else { ?>
                                            (<?php echo $value["due_in"]; ?>)
                                            - <?php echo $value["priority"] == "all" ? $this->lang->line("all_clients") : $value["priority"]; ?>
                                            <?php echo $value["clientData"]["type"] == "all" ? $this->lang->line("all_priorities") : $value["clientData"]["clientName"]; ?>
                                        <?php } ?>
                                        <?php if ($key != count($record["case_type_due_conditions"]) - 1) { ?>,<?php } ?>
                                    <?php } ?>
                                </td>
                                <td>
                                    <a href="<?php echo site_url("case_types/edit/" . $record["id"]); ?>">
                                        <i class="fa fa-edit fa-lg"></i>
                                    </a>
                                </td>
                                <td>
                                    <a href="javascript:;" onclick="return confirm(_lang.confirmationDeleteSelectedRecord) ? document.location = '<?php echo site_url("case_types/delete/" . $record["id"]); ?>' : false;">
                                        <i class="fa fa-trash fa-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php } ?>
                    </table>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php $this->load->view("partial/footer"); ?>

<script>
    jQuery('.tooltip-title').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 400,
        interactive: true
    });
</script>