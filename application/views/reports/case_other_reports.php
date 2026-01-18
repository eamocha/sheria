<div class="col-md-12" id="core-reports">
    <div class=" row col-md-12 no-padding">
            <div class="col-md-12">
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo site_url("reports");?>"><?php echo $this->lang->line("reports");?></a></li>
                    <li class="active breadcrumb-item"><?php echo $this->lang->line("other_reports");?></li>
                </ul>
            </div>
        <div class="col-md-6 no-padding">
            <div  id="filtersList">
                <div class="form-group  col-md-12 no-padding">
                    <div class="portlet">
                        <div class="portlet-header"><?php echo $this->lang->line("cases");?>                        </div>
                        <div class="portlet-content">
                            <div class="margin-left d-flex">
                                <div class="col-md-2 no-margin margin-bottom portlet-icon">
                                    <i class="fa fa-briefcase"></i>
                                </div>
                                <div class="col-md-10">
                                    <p><a href="<?php echo site_url("reports/advanced_case_report/");?>"><?php echo $this->lang->line("advanced_case_report");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_assignee_per_status/");?>"><?php echo $this->lang->line("cases_per_assignee_per_status");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_assignee_per_due_date/");?>"><?php echo $this->lang->line("cases_per_assignee_per_due_date");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_company_per_assignee/");?>"><?php echo $this->lang->line("cases_per_company_per_assignee");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_contact_per_assignee/");?>"><?php echo $this->lang->line("cases_per_contact_per_assignee");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_company_per_role/");?>"><?php echo $this->lang->line("cases_per_company_per_role");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_contact_per_role/"); ?>"><?php echo $this->lang->line("cases_per_contact_per_role");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_per_external_lawyer_per_status/"); ?>"><?php echo $this->lang->line("cases_per_external_lawyer_per_status"); ?></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 no-padding">
            <div  id="filtersList">
                <div class="form-group  col-md-12 no-padding">
                    <div class="portlet">
                        <div class="portlet-header"><?php echo $this->lang->line("prosecution_reports");?>                        </div>
                        <div class="portlet-content">
                            <div class="margin-left d-flex">
                                <div class="col-md-2 no-margin margin-bottom portlet-icon">
                                    <i class="fa fa-briefcase"></i>
                                </div>
                                <div class="col-md-10">

                                    <p><a href="reports/prosecution/complaints" >Complaints/Inquiry Report</a></p>
                                    <p><a href="reports/prosecution/surveillance" >Surveillance/Detection Exercises Report</a></p>
                                    <p><a href="reports/prosecution/investigations" >Investigations and Enforcement Action Exercises Report</a></p>
                                    <p><a href="reports/prosecution/case-log" >Case Log/Inventory of Cases</a></p>
                                    <p><a href="reports/prosecution/pbc" >Current Cases Pending Before Court (Monthly Status)</a></p>
                                    <p><a href="reports/prosecution/case-category" >Category of Cases and Statistics</a></p>
                                    <p><a href="reports/prosecution/prosecution" >Prosecution Reports (Quarterly/Annual)</a></p>

                                    <p><a href="<?php echo site_url("reports/master_register/");?>"><?php echo $this->lang->line("master_register");?></a></p>
                                    <p><a href="<?php echo site_url("reports/case_log_summary/");?>"><?php echo $this->lang->line("case_log_summary");?></a></p>
                                    <p><a href="<?php echo site_url("reports/cases_pending_before_court/");?>"><?php echo $this->lang->line("cases_pending_before_court");?></a></p>
                                    <p><a href="<?php echo site_url("reports/exhibit_reports/");?>"><?php echo $this->lang->line("exhibit_reports");?></a></p>
                                    <p><a href="<?php echo site_url("reports/category_cases_statistics/");?>"><?php echo $this->lang->line("category_cases_statistics");?></a></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery(".portlet").addClass("jqui ui-widget ui-widget-content ui-helper-clearfix ui-corner-all")
            .find(".portlet-header")
            .addClass("ui-widget-header ui-corner-all")
            .end()
            .find(".portlet-content");
    });
</script>