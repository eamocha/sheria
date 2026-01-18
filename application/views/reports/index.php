<div class="col-md-12" id="core-reports">
    <div class="row no-margin">
        <div class="form-group col-md-6">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("companies"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fa fa-building"></i>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("reports/shares_by_date"); ?>"><?php echo $this->lang->line("company_shares_by_date"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/shares_by_holder"); ?>"><?php echo $this->lang->line("company_shares_by_shareholder"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/shareholder_votes"); ?>"><?php echo $this->lang->line("company_shareholder_votes"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/company_shareholders_tree_view"); ?>"><?php echo $this->lang->line("company_shareholders_tree_view"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/shareholders_finder"); ?>"><?php echo $this->lang->line("shareholders_finder"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/board_member_finder"); ?>"><?php echo $this->lang->line("board_member_finder"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/companies_per_ss_expiry_dates"); ?>"><?php echo $this->lang->line("companies_per_ss_expiry_dates"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/companies_assets"); ?>"><?php echo $this->lang->line("companies_assets"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/company_related_matters"); ?>"><?php echo $this->lang->line("company_related_matters"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("contacts"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin">
                            <div class="col-md-2 no-margin margin-bottom portlet-icon">
                                <i class="fa fa-user"></i>
                            </div>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("reports/contacts_per_group_of_companies"); ?>"><?php echo $this->lang->line("contacts_per_group_of_companies"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-margin">
        <div class="form-group col-md-6">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("time_tracking"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fa fa-clock"></i>
                        </div>
                        <div class="col-md-10">
                            <p class="core-access"><a href="<?php echo site_url("reports/time_tracking_by_case/"); ?>"><?php echo $this->lang->line("time_tracking_by_case"); ?></a></p>
                            <p class="core-access"><a href="<?php echo site_url("reports/time_tracking_by_seniority/"); ?>"><?php echo $this->lang->line("time_tracking_by_seniority"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/time_tracking_kpi"); ?>"><?php echo $this->lang->line("time_tracking_kpi_report"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/my_time_tracking_kpi"); ?>"><?php echo $this->lang->line("my_time_tracking_kpi_report"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 core-access">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("tasks"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fa fa-clipboard-list"></i>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("reports/task_roll_session/"); ?>"><?php echo $this->lang->line("task_roll_session"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row no-margin">
        <div class="form-group col-md-6 core-access">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("cases"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fa fa-briefcase"></i>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("reports/conflict_of_interset"); ?>"><?php echo $this->lang->line("conflict_of_interest"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/case_value_tiers/"); ?>"><?php echo $this->lang->line("case_value_tiers"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/case_values_per_client_name/"); ?>"><?php echo $this->lang->line("case_values_per_client_name_report"); ?></a></p>
                            <?php if ($AllowFeatureSLAManagement) { ?>
                                <p><a href="<?php echo site_url("reports/sla_met_vs_breached_bar_chart/"); ?>"><?php echo $this->lang->line("sla_met_vs_breached_cases"); ?></a></p>
                            <?php } ?>
                            <p><a href="<?php echo site_url("reports/matters_attachments_report/"); ?>"><?php echo $this->lang->line("matters_attachments_report"); ?></a></p>
                            <?php if ($AllowFeatureSLAManagement) { ?>
                                <p><a href="<?php echo site_url("reports/sla/"); ?>"><?php echo $this->lang->line("sla_report"); ?></a></p>
                            <?php } ?>
                            <?php $created_reports_array = []; ?>
                            <?php foreach ($reports["created_reports"] as $key => $value) { ?>
                                <p><a href="reports/report_builder_view/<?php echo $value["id"]; ?>"><?php echo $value["keyName"]; ?></a></p>
                                <?php $created_reports_array[] = $value["id"]; ?>
                            <?php } ?>
                            <?php foreach ($reports["shared_reports"] as $key => $value) { ?>
                                <?php if (!in_array($value["id"], $created_reports_array)) { ?>
                                    <p><a href="reports/report_builder_view/<?php echo $value["id"]; ?>"><?php echo $value["keyName"]; ?></a></p>
                                <?php } ?>
                            <?php } ?>
                            <p><a href="<?php echo site_url("reports/case_status_risks_fee_notes/"); ?>"><?php echo $this->lang->line("case_status_with_fee_notes"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/case_other_reports/"); ?>"><?php echo $this->lang->line("other_reports"); ?></a></p>

                            <p><a href="<?php echo site_url("reports/report_builder"); ?>"><i class="fa-solid fa-circle-plus"></i> <?php echo $this->lang->line("report_builder"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 core-access">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("hearings"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fa fa-gavel"></i>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("reports/hearings_roll_session_report"); ?>"><?php echo $this->lang->line("hearings_roll_session_report"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/hearing_roll_session_per_court"); ?>"><?php echo $this->lang->line("hearing_roll_session_per_court_circuit"); ?></a></p>
                            <p><a href="<?php echo site_url("reports/hearings_pending_updates"); ?>"><?php echo $this->lang->line("hearings_pending_updates"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="form-group col-md-6 core-access">
            <div class="portlet">
                <div class="portlet-header">
                    <?php echo $this->lang->line("correspondence"); ?>
                </div>
                <div class="portlet-content">
                    <div class="d-flex">
                        <div class="col-md-2 no-margin margin-bottom portlet-icon">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <div class="col-md-10">
                            <p><a href="<?php echo site_url("front_office/report"); ?>"><?php echo $this->lang->line("correspondence_logs"); ?></a></p>
                            <p><a href="<?php echo site_url("front_office/report"); ?>"><?php echo $this->lang->line("status_reports"); ?></a></p>
                            <p><a href="<?php echo site_url("front_office/report"); ?>"><?php echo $this->lang->line("sla_report"); ?></a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php if ($AllowContractSLAManagement) { ?>
            <div class="form-group col-md-6 contract-access">
                <div class="portlet">
                    <div class="portlet-header">
                        <?php echo $this->lang->line("contract"); ?>
                    </div>
                    <div class="portlet-content">
                        <div class="d-flex">
                            <div class="col-md-2 no-margin margin-bottom portlet-icon">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <div class="col-md-10">
                                <p><a href="<?php echo site_url("modules/contract/reports/sla_met_vs_breached_bar_chart/"); ?>"><?php echo $this->lang->line("sla_met_vs_breached_cases"); ?></a></p>
                                <p><a href="<?php echo site_url("modules/contract/reports/sla/"); ?>"><?php echo $this->lang->line("sla_report"); ?></a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        jQuery(".portlet").addClass("jqui ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
            .find(".portlet-header")
            .addClass("ui-widget-header ui-corner-all")
            .prepend("<span role='button' class='fa-rapper'><i class='fa-solid fa-minus pull-right'></i></span>")
            .end()
            .find(".portlet-content");

        jQuery(".fa-rapper").click(function () {
            jQuery(this).children().toggleClass("fa-minus").toggleClass("fa-plus");
            jQuery(this).parents(".portlet:first").find(".portlet-content").toggle('normal');
        });
    });
</script>