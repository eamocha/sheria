<?php
$this->load->library("common_functions");
$systemPreferences = $this->session->userdata("systemPreferences");
$case_has_multi_containers = !empty($count_legal_case_related_containers["data"]["totalRows"]) && 1 < $count_legal_case_related_containers["data"]["totalRows"];
if ($case_has_multi_containers) {
    $this->load->view("cases/case_has_multi_containers_modal");
}
$case_id=$legalCase["id"];
$case_category=$legalCase["category"];
?>
<div id="edit-legal-case-container">
    <?php $this->load->view("cases/top_nav", ["is_edit" => 0, "main_tab" => true]); ?>
    <div id="casesDetails" class="main-offcanvas main-offcanvas-left">
        <div id="newCaseFormDialog" class="col-xs-12 no-padding">
            <?php   $this->load->view("partial/tabs_subnav_vertical", $tabsNLogs);   ?>
            <div class="resp-main-body-width-70 no-padding flex-scroll-auto flex-grow" id="main-content-side">
                <div class="main-content-section">
                    <div id="top-section-div"></div>
                    <?php $this->load->view("cases/object_header"); ?>
                    <div class="row col-md-12 no-margin no-padding">
                        <div class="col-md-9">
                            <div class="col-md-12 no-padding matter">
                                <?php
                                echo form_open(current_url(), 'class="form-horizontal editmode" novalidate id="legalCaseAddForm" method="post"');
                                echo form_input(["name" => "id", "id" => "id", "value" => $legalCase["id"], "data-field" => "case_id", "type" => "hidden"]);
                                echo form_input(["name" => "send_notifications_email", "id" => "send_notifications_email", "value" => $hide_show_notification_edit_legal_case, "type" => "hidden"]);
                                echo form_input(["name" => "allUsersProviderGroup", "id" => "all-users-provider-group", "value" => $allUsersProviderGroupId, "type" => "hidden"]);
                                echo form_input(["name" => "externalizeLawyers", "id" => "externalizeLawyers", "value" => $legalCase["externalizeLawyers"], "type" => "hidden"]);
                                echo form_input(["name" => "category", "id" => "category", "value" => $legalCase["category"], "type" => "hidden"]);
                                echo form_input(["name" => "archived", "id" => "archived", "value" => $legalCase["archived"], "type" => "hidden"]);
                                echo form_input(["name" => "visibleToCP", "id" => "visibleToCP", "value" => $legalCase["visibleToCP"], "type" => "hidden"]);
                                echo form_input(["name" => "case_status_id", "id" => "archived", "value" => $legalCase["case_status_id"], "type" => "hidden"]);
                                echo form_input(["name" => "stage", "id" => "litigation-stage-id", "value" => $legalCase["stage"], "type" => "hidden"]);
                                echo form_input(["name" => "time_logs_cap_ratio", "id" => "time-logs-cap-ratio", "value" => $legalCase["time_logs_cap_ratio"], "type" => "hidden"]);
                                echo form_input(["name" => "expenses_cap_ratio", "id" => "expenses-cap-ratio", "value" => $legalCase["expenses_cap_ratio"], "type" => "hidden"]);
                                echo form_input(["name" => "cap_amount", "id" => "cap-amount", "value" => $legalCase["cap_amount"], "type" => "hidden"]);
                                echo form_input(["name" => "cap_amount_enable", "id" => "cap-amount-enable", "value" => $legalCase["cap_amount_enable"], "type" => "hidden"]);
                                echo form_input(["name" => "cap_amount_disallow", "id" => "cap-amount-disallow", "value" => $legalCase["cap_amount_disallow"], "type" => "hidden"]);
                                echo form_input(["name" => "workflow", "value" => $legalCase["workflow"], "type" => "hidden"]);
                                ?>
                                <div class="box-section row margin-bottom-15" id="personal_info_div">
                                    <?php   $this->load->view("cases/parties/test");   ?>
                                    <?php   $this->load->view("cases/edit_mode");   ?>
                                </div>
                                <?php if($legalCase["category"] == "Matter") {  ?>
                                    <div class="box-section row margin-bottom-15" id="adr_sessions_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/court-activity.svg" height="20" width="20">
                                            <span class="text-with-hint tooltip-title" title="<?php echo $this->lang->line("matter_activities_helper") ?>">
                                                <?php echo $this->lang->line("matter_sessions");?>
                                            </span>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container" id="case-events-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <?php
                                                echo form_open("", 'name="court_activitySearchFilters" id="court_activitySearchFilters" method="post" class="no-margin"');
                                                echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "legal_case_id", "value" => $legalCase["id"], "type" => "hidden"]);
                                                echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                                echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                                echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                                echo form_input(["name" => "", "id"=>"legal-case", "value" => $legalCase["id"], "type" => "hidden"]);
                                                ?>    <?php echo form_close();?>
                                                <div class="row-cols-4 margin-bottom-10 ">
                                                    <a class="cursor-pointer-click btn btn-primary col-auto"  onclick="legalCaseHearingForm(0, false, '', true, function(){legalCaseEvents.openHearingTab('<?php echo !empty($stageID) ? $stageID : "null"; ?>','<?php echo  $legalCase["id"]; ?>', true)}, '<?php echo !empty($stageID) ? $stageID : false; ?>', <?php echo !empty($stageID) ? false : true; ?>);"><?php echo $this->lang->line("add_adr_session")?></a>
                                                    <a class=" btn btn-primary col-auto" onclick="fetchBasicRecords(<?php echo $legalCase["id"]?>)" ><i class="icon-refresh"></i> Refresh</a>
                                                </div>

                                                <div class="card">

                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0">
                                                                <thead class="thead-light">
                                                                <tr><th> ID</th><th>Purpose</th><th>Activity Date/Time</th><th>Comments</th><th>Created On</th></tr>
                                                                </thead>
                                                                <tbody id="hearing-data">
                                                                <!-- Data will be loaded here via JavaScript -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php } ///end ADR SESSIONS
                                if($legalCase["category"] == "Litigation") {  ?>
                                    <div class="box-section row margin-bottom-15" id="court_activities_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/court-activity.svg" height="20" width="20">
                                            <span class="text-with-hint tooltip-title" title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_activities_helper") : $this->lang->line("criminal_activities_helper");?>">
                                                <?php echo $this->lang->line("court_activities");?>
                                            </span>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container" id="case-events-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <?php
                                                echo form_open("", 'name="court_activitySearchFilters" id="court_activitySearchFilters" method="post" class="no-margin"');
                                                echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "legal_case_id", "value" => $legalCase["id"], "type" => "hidden"]);
                                                echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                                echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                                echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                                echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                                echo form_input(["name" => "", "id"=>"legal-case", "value" => $legalCase["id"], "type" => "hidden"]);
                                                ?>    <?php echo form_close();?>
                                                <div class="row-cols-4 margin-bottom-10 ">
                                                    <a class="cursor-pointer-click btn btn-primary col-auto"  onclick="legalCaseHearingForm(0, false, '', true, function(){legalCaseEvents.openHearingTab('<?php echo !empty($stageID) ? $stageID : "null"; ?>','<?php echo  $legalCase["id"]; ?>', true)}, '<?php echo !empty($stageID) ? $stageID : false; ?>', <?php echo !empty($stageID) ? false : true; ?>);"><?php echo $this->lang->line("add_court_activity")?></a>
                                                    <a class=" btn btn-primary col-auto" onclick="fetchBasicRecords(<?php echo $legalCase["id"]?>)" ><i class="icon-refresh"></i> Refresh</a>
                                                </div>

                                                <div class="card">

                                                    <div class="card-body p-0">
                                                        <div class="table-responsive">
                                                            <table class="table table-hover mb-0">
                                                                <thead class="thead-light">
                                                                <tr><th> ID</th><th>Purpose</th><th>Activity Date/Time</th><th>Comments</th></th><th>Created On</th></tr>
                                                                </thead>
                                                                <tbody id="hearing-data">
                                                                <!-- Data will be loaded here via JavaScript -->
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                <?php }// end the court activities
                                $case_id=$legalCase["id"];
                                if($legalCase["category"] == "Litigation") {  ?>
                                    <div class="box-section row margin-bottom-15" id="tasks_summary_div">
                                        <h2 class="box-title box-shadow_container ">Related  Tasks</h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container" id="tasks-container">
                                            <div class="col-md-12 no-padding grid-section">
                                                <a class="cursor-pointer-click btn btn-primary col-auto"  onclick="taskAddForm('<?php echo $case_id; ?>', '<?php echo !empty($stage_id) ? $stage_id : false; ?>', function (){legalCaseEvents.openTaskTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)}, <?php echo !empty($stage_id) ? false : true; ?>);"> Add Task</a>
                                                <a class=" btn btn-primary col-auto" onclick="getCaseTasksSummary(<?php echo $case_id?>)" > Refresh</a>
                                            </div>
                                            <table  class="table table-bordered table-striped">
                                                <thead class="thead-light">
                                                <tr><th>Task Type</th><th>Title</th><th>Assigned To</th><th>Status</th><th>Due Date</th></tr>
                                                </thead>
                                                <tbody id="task-data"></tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="box-section row margin-bottom-15" id="case_docs_summary_div">
                                        <h2 class="box-title box-shadow_container ">Related Documents</h2>
                                        <div class="col-md-12 box-shadow_container padding-15 main-grid-container" id="case-docs-summary-container">
                                            <div class="col-md-12 no-padding grid-section" id="case-docs-summary-dataGrid">
                                                <a class="margin-bottom-10 cursor-pointer-click btn btn-primary" onclick="openDocsModal()" >Upload Document</a>
                                                <?php $this->load->view("cases/main_page_docs/uploadform.php");?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>

                                <div class="box-section row margin-bottom-15" id="outsourcing_to_lawyers_div">
                                    <h2 class="box-title box-shadow_container ">
                                        <img src="assets/images/icons/outsourcing.svg" height="20" width="20">
                                        <span class="text-with-hint tooltip-title" title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_outsourcing_to_helper") : $this->lang->line("matter_outsourcing_to_helper");?>">
                                                <?php echo $this->lang->line("outsourcing_to_lawyers");?>
                                            </span>
                                    </h2>
                                    <div class="col-md-12 box-shadow_container padding-15 main-grid-container">
                                        <div class="col-md-12 no-padding grid-section">
                                            <?php
                                            echo form_open("", 'name="outsourceSearchFilters" id="outsourceSearchFilters" method="post" class="no-margin"');
                                            echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                            echo form_input(["name" => "legal_case_id", "value" => $legalCase["id"], "type" => "hidden"]);
                                            echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                            echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                            echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                            echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                            ?>
                                            <div class="form-group d-none">
                                                <div class="controls">
                                                    <?php
                                                    echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "legal_cases_contacts.case_id", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[0].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[0].filters[0].value", "value" => $id, "type" => "hidden"]);?>
                                                </div>
                                                <div class="controls">
                                                    <?php
                                                    echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "legal_cases_contacts.contactType", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].value", "value" => "outsource", "type" => "hidden"]);
                                                    ?>
                                                </div>
                                            </div>
                                            <?php echo form_close();?>
                                            <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? "class='k-rtl'" : "";?>>
                                                <div id="outsource-grid" class="grid-container kendo-desktop grid-container-height-auto"></div>
                                            </div>
                                            <div class="row no-margin">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="box-section row margin-bottom-15" id="related_contributors_div">
                                    <h2 class="box-title box-shadow_container ">
                                        <img src="assets/images/icons/contribution.svg" height="20" width="20">
                                        <span class="text-with-hint tooltip-title" title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_related_contributors_helper") : $this->lang->line("matter_related_contributors_helper");?>">
                                                <?php echo $this->lang->line("related_contributors");?>
                                            </span>
                                    </h2>
                                    <div class="col-md-12 box-shadow_container padding-15 main-grid-container">
                                        <div class="col-md-12 no-padding grid-section">
                                            <?php
                                            echo form_open("", 'name="lawyersContributorsSearchFilters" id="lawyersContributorsSearchFilters" method="post" class="no-margin"');
                                            echo form_input(["name" => "take", "value" => "5", "type" => "hidden"]);
                                            echo form_input(["name" => "skip", "value" => "0", "type" => "hidden"]);
                                            echo form_input(["name" => "page", "value" => "1", "type" => "hidden"]);
                                            echo form_input(["name" => "pageSize", "value" => "5", "type" => "hidden"]);
                                            echo form_input(["name" => "filter.logic", "id" => "defaultFilterLogic", "value" => "and", "type" => "hidden"]);
                                            ?>
                                            <div class="form-group d-none">
                                                <div class="controls">
                                                    <?php
                                                    echo form_input(["name" => "filter.filters[0].filters[0].field", "value" => "legal_cases_contacts.case_id", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[0].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[0].filters[0].value", "value" => $id, "type" => "hidden"]);
                                                    ?>
                                                </div>
                                                <div class="controls">  <?php echo form_input(["name" => "filter.filters[1].filters[0].field", "value" => "legal_cases_contacts.contactType", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].operator", "value" => "eq", "type" => "hidden"]);
                                                    echo form_input(["name" => "filter.filters[1].filters[0].value", "value" => "contributor", "type" => "hidden"]);
                                                    ?>
                                                </div>
                                            </div>
                                            <?php echo form_close();?>
                                            <div <?php echo $this->session->userdata("AUTH_language") == "arabic" ? 'class="k-rtl"' : "";?>>
                                                <div id="lawyersContributorsGrid" class="grid-container kendo-desktop grid-container-height-auto"></div>
                                            </div>
                                            <div class="row no-margin">&nbsp;</div>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if (isset($legalCase["id"]) && is_numeric($legalCase["id"])) {?>
                                    <div class="box-section row margin-bottom-15" id="case_notes_tabs_btn_container">
                                        <h2 class="box-title box-shadow_container ">
                                            <i id="case_notes_tabs_toggle_icon" class="fa fa-sticky-note purple_color"></i>
                                            <?php     echo $this->lang->line("notes");?>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15" id="case_notes_tabs">
                                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                                <li id="case_notes_all_threads_list" class="nav-item" role="presentation">
                                                    <?php $lcId= (int) $legalCase["id"];?>
                                                    <a onclick="fetch_case_comments_tab('<?php echo $lcId ?>', jQuery('#case_notes_all_threads'), 'cases/get_all_comments', 1);" class="nav-link active" id="case_notes_all_threads-tab" data-toggle="tab" href='#case_notes_all_threads' role="tab" aria-controls="case_notes_all_threads" aria-selected="true">
                                                        <?php      echo $this->lang->line("case_notes_all_threads");    ?>
                                                    </a>
                                                </li>
                                                <li id="case_notes_core_and_cp_list" class="nav-item" role="presentation">
                                                    <a onclick="fetch_case_comments_tab('<?php   echo $lcId?>', jQuery('#case_notes_core_and_cp'), 'cases/get_all_core_and_cp_comments', 2);" class="nav-link" id="case_notes_core_and_cp-tab" data-toggle="tab" href="#case_notes_core_and_cp" role="tab" aria-controls="case_notes_core_and_cp" aria-selected="false">
                                                        <?php echo $this->lang->line("case_notes_core_and_cp");    ?>
                                                    </a>
                                                </li>
                                                <li id="case_notes_emails_list" class="nav-item" role="presentation">
                                                    <a onclick="fetch_case_comments_tab('<?php echo  $lcId   ?>', jQuery('#case_notes_emails'), 'cases/get_all_email_comments', 3);" class="nav-link" id="case_notes_emails-tab" data-toggle="tab" href="#case_notes_emails" role="tab" aria-controls="case_notes_emails" aria-selected="false">
                                                        <?php echo $this->lang->line("case_notes_emails");    ?>
                                                    </a>
                                                </li>
                                            </ul>
                                            <div class="tab-content" id="myTabContent">
                                                </br>
                                                <div class="col-xs-12 no-padding">
                                                    <?php    $legalCase["isCustomerPortal"] = $isCustomerPortal;   ?>
                                                    <div class="col-md-12 add-note-bottom-container margin-bottom margin-top no-margin-left no-padding">
                                                        <input type="button" value="<?php    echo $this->lang->line("add_note");?>" class="btn btn-default btn-info margin-right" id="add_comment" onclick="addCaseDocument('<?php    echo (int) $legalCase["id"];?>')" />
                                                    </div>
                                                </div>
                                                <div class="activity-container case-notes-container tab-pane fade show active" id="case_notes_all_threads" role="tabpanel" aria-labelledby="case_notes_all_threads-tab"><div class="comments-lists-container"></div></div>
                                                <div class="activity-container case-notes-container tab-pane fade" id="case_notes_core_and_cp" role="tabpanel" aria-labelledby="case_notes_core_and_cp-tab"><div class="comments-lists-container"></div></div>
                                                <div class="activity-container case-notes-container tab-pane fade" id="case_notes_emails"  role="tabpanel" aria-labelledby="case_notes_emails-tab"><div class="comments-lists-container"></div></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="box-section row margin-bottom-15" id="case_history_div">
                                        <h2 class="box-title box-shadow_container ">
                                            <img src="assets/images/icons/history.svg" height="20" width="20">
                                            <?php    echo $this->lang->line("case_history");?>
                                        </h2>
                                        <div class="col-md-12 box-shadow_container padding-15 height-auto">
                                            <?php    if ($legalCase["category"] != "Litigation") {       ?>
                                                <ul class="nav nav-tabs" id="history-tab" role="tablist">
                                                    <li id="caseStagesHistoryList" class="nav-item" role="presentation">
                                                        <a onclick="toggle_case_stages();" class="nav-link active" id="caseStagesHistoryList-tab" data-toggle="tab" href="#caseStagesHistory" role="tab" aria-controls="caseStagesHistory" aria-selected="true">
                                                            <?php        echo $this->lang->line("case_stages");        ?>
                                                        </a>
                                                    </li>
                                                    <li id="auditReportHistoryHref" class="nav-item" role="presentation">
                                                        <a class="nav-link label-tooltip tooltip-title" id="auditReportHistoryHref-tab" data-toggle="tab" href="#auditReportHistory" role="tab" aria-controls="auditReportHistory" aria-selected="false" onclick="fetch_audit_report_history('<?php echo (int) $case_id?>', '<?php   echo $legalCase["category"] == "Litigation" ? "Litigation" : "Matter";?>');"    title="<?php echo $this->lang->line("case_history_audit_report_helper"); ?>" >
                                                            <?php echo $this->lang->line("audit_report"); ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="history-tab-content">
                                                    <br>
                                                    <div class="activity-container history-report-table tab-pane fade show active" id="caseStagesHistory" role="tabpanel" aria-labelledby="auditReportHistoryHref-tab"></div>
                                                    <div class="activity-container history-report-table tab-pane fade" id="auditReportHistory" role="tabpanel" aria-labelledby="auditReportHistoryHref-tab"></div>
                                                </div>
                                                <?php
                                            } else {
                                                ?>
                                                <ul class="nav nav-tabs" id="history-tab" role="tablist">
                                                    <li id="auditReportHistoryHref" class="nav-item" role="presentation">
                                                        <a class="nav-link label-tooltip tooltip-title" id="auditReportHistoryHref-tab" data-toggle="tab" href="#auditReportHistory" role="tab" aria-controls="auditReportHistory" aria-selected="false" onclick="fetch_audit_report_history('<?php        echo (int) $legalCase["id"]; ?>', '<?php        echo $legalCase["category"] == "Litigation" ? "Litigation" : "Matter";?>');" title="<?php        echo $this->lang->line("case_history_audit_report_helper");?>" >
                                                            <?php
                                                            echo $this->lang->line("audit_report");
                                                            ?>
                                                        </a>
                                                    </li>
                                                </ul>
                                                <div class="tab-content" id="history-tab-content">
                                                    <br>
                                                    <div class="activity-container history-report-table tab-pane fade" id="auditReportHistory" role="tabpanel" aria-labelledby="auditReportHistoryHref-tab"></div>
                                                </div>
                                                <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="primary-style">
                                        <div class="modal fade" id="module_expired_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="myModalLabel">
                                                            <?php    echo $this->lang->line("case_comment_email_expiry_title");   ?>
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <?php    echo $this->lang->line("case_comment_email_expiry_body");   ?>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="close_model no_bg_button pull-right text-align-right" data-dismiss="modal">
                                                            <?php    echo $this->lang->line("close");   ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        </div>
                        <?php $this->load->view("cases/right_boxes", ["custom_fields" => $custom_fields]);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div class="d-none" id="caseCommissionsFormDialog"></div>
<div class="d-none" id="slaShowLogsDialog"></div>
<div id="case-outsource-dialog">
    <?php
    if (!isset($data)) {
        $data = [];
    }
    $this->load->view("cases/outsource_add", compact("data"));
    ?>
</div>
<div id="caseStageHistoryDialog" class="d-none">
    <?php $this->load->view("cases/case_stage_history_form"); ?>
</div>

<script>
    function openDocsModal() {
        jQuery('#docsModal').modal('show');
    }
</script>

<script type="text/javascript">
    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($disableArchivedMatters) && $disableArchivedMatters ? true : false;?>';
    <?php if ($case_has_multi_containers) {?>
    var formReadyToSubmit = false;
    <?php }?>
    var assigneeInfo = {
        assigneeId: '<?php echo $legalCase["user_id"];?>',
        //assigneeName: '<?php //echo $legalCase["Assignee"];?>',//this breaks the code when the assignee name has an apostrophe
        assigneeName: <?php echo json_encode($legalCase["Assignee"]); ?>,
        assignedTeamId: '<?php echo $legalCase["provider_group_id"];?>'
    };
    var availableUsers = <?php echo json_encode($usersList);?>;
    var isPrivate = '<?php echo $legalCase["private"] == "yes";?>';
    jQuery(document).ready(function() {
        let caseId=<?php echo (int) $legalCase["id"];?>;
        //load tasks in front end
        getCaseTasksSummary(caseId);
        fetchBasicRecords(caseId)
        fetchClientAccountStatus(caseId);

        jQuery("#reason_for_reassignment_controls").hide();
        jQuery('#legal-case-add-form', jQuery("#edit-legal-case-container")).click(function(e) {
            e.preventDefault();
            window.onbeforeunload = null;
            jQuery(window).off('beforeunload');
            var container = jQuery("#edit-legal-case-container");
            if (/MSIE \\d|Trident.*rv:/.test(navigator.userAgent)) {
                var formData = jQuery('#main-content-side :input').serializeArray();
            } else {
                var formData = jQuery("form#legalCaseAddForm", container).serializeArray();
            }
            formData.push({
                name: "action",
                value: "save_case"
            }, {
                name: "legal_case_id",
                value: <?php echo (int) $legalCase["id"];?>
            });
            formData.forEach(function(item) {
                if (item.name === 'clientType') {
                    item.value = item.value === 'company' ? 'companies' : 'contacts';
                }
            });
            var url = getBaseURL() + 'cases/edit';
            jQuery.ajax({
                dataType: 'JSON',
                url: url,
                data: formData,
                type: 'POST',
                beforeSend: function() {
                    ajaxEvents.beforeActionEvents(container);
                },
                success: function(response) {
                    jQuery(".inline-error").addClass('d-none');
                    if (response.result) {
                        pinesMessageV2({
                            ty: 'success',
                            m: response.message
                        });
                    } else {
                        displayValidationErrors(response.validationErrors, container);
                    }
                },
                complete: function() {
                    formData.forEach(key => {
                        if (key.name == "subject") {
                            jQuery('#matter-title').html(key.value);
                            jQuery('#matter-title').tooltipster('content', key.value);
                        }
                    });
                    ajaxEvents.completeEventsAction(container, false, {}, '<i class="icon-alignment fa fa-floppy-o white-text"></i> ' + _lang.save);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        });
        //privacy section
        var containerPrivacy = jQuery('#case-privacy-container');
        lookupPrivateUsers(jQuery('#lookup-case-users', containerPrivacy), 'Legal_Case_Watchers_Users', '#selected-watchers', 'case-privacy-container', containerPrivacy, 'legalCaseAddForm');
        //end privacy section
        if (jQuery('.delete-icon').length == 1) {
            jQuery('.delete-icon').addClass('d-none');
        }
        jQuery(".error").parents().closest('.foldable').removeClass('d-none')
        jQuery('input[name="send_notifications_email"]', '#legalCaseAddForm').removeAttr('checked');
        notifyMeBeforeEvent({
            'input': 'dueDate'
        }, jQuery('#legalCaseAddForm'));
        fetch_case_comments_tab(<?php echo (int) $legalCase["id"];?>, jQuery('#case_notes_all_threads'), null, 1);
        fetch_case_stages_history('<?php echo (int) $legalCase["id"];?>', true);
        new AutoNumeric('#caseValue', 'float');
        <?php if ($legalCase["category"] == "Litigation") {?>
        new AutoNumeric('#judgmentValue', 'float');
        new AutoNumeric('#recoveredValue', 'float');
        <?php }?>
        let caseCategory = <?php echo $legalCase["category"] == "Litigation" ? 1 : 0;?>;
        editCaseDemo(caseCategory);
    });
    var authIdLoggedIn = '<?php echo $this->is_auth->get_user_id();?>';
    var notificationsNoteTemplate = '<?php $this->load->view("notifications/wrapper", ["hide_show_notification" => $hide_show_notification, "container" => "commentDialog"]);?>';
    function reassignment_reason(){
//document.getElementById("reason_for_reassignment").value="";
        jQuery("#reason_for_reassignment_controls").show();
    }

    function validateNumbers(field, rules, i, options) {
        var val = field.val();
        var decimalPattern = /^[0-9]+(\\.[0-9]{1,2})?\$/;
        if (!decimalPattern.test(val)) {
            return _lang.decimalAllowed;
        }
    }

    function validateFormat(field, rules, i, options) {
        var val = field.val();
        var decimalPattern = /( ?%)\\d/;
        if (decimalPattern.test(val)) {
            return _lang.percentageNotAllowed;
        }
    }
    <?php
    if ($notify_before) {?>
    notifyMeBefore(jQuery('#legalCaseAddForm'));
    <?php }?>
    jQuery("#legalCaseRelatedContainerLookup").autocomplete({
        autoFocus: true,
        delay: 600,
        source: function(request, response) {
            request.term = request.term.trim();
            jQuery.ajax({
                url: getBaseURL() + 'case_containers/autocomplete',
                dataType: "json",
                data: request,
                error: defaultAjaxJSONErrorsHandler,
                success: function(data) {
                    if (data.length < 1) {
                        response([{
                            label: _lang.no_results_matched.sprintf([request.term]),
                            value: '',
                            record: {
                                id: -1,
                                term: request.term
                            }
                        }]);
                    } else {
                        response(jQuery.map(data, function(item) {
                            return {
                                label: item.subject,
                                value: item.subject,
                                record: item
                            }
                        }));
                    }
                }
            });
        },
        minLength: 3,
        select: function(event, ui) {
            if (ui.item.record.id > 0) {
                jQuery('#legalCaseRelatedContainerId').val(ui.item.record.id);
                jQuery('#legalCaseRelatedContainerLinkId').removeClass('d-none').attr('href', '<?php echo site_url("case_containers/edit/");?>' + ui.item.record.id).attr('title', 'MC' + ui.item.record.id);
            }
        }
    });
    var contactRoles = [<?php $jsArr = ["{value:'', text:'-'}"];
        if (!empty($contactGridRoles)) {
            foreach ($contactGridRoles as $value => $text) {
                $jsArr[] = "{value: " . (int) $value * 1 . ", text:'" . addslashes($text) . "'}";
            }
        }
        echo implode(", ", $jsArr);
        ?>];
    var companyRoles = [<?php $jsArr = ["{value:\"\", text:\"-\"}"];
        foreach ($companyGridRoles as $value => $text) {
            $jsArr[] = "{value: " . (int) $value * 1 . ", text:'" . addslashes($text) . "'}";
        }
        echo implode(", ", $jsArr);
        ?>];
    var container = jQuery("#edit-legal-case-container");
    positionMatterTitle = 'top-left';
    if (_lang.languageSettings['langDirection'] === 'rtl'){
        positionMatterTitle = 'top-right';
    }
    jQuery('#matter-title').tooltipster({
        position: positionMatterTitle,
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    jQuery('.tooltip-title', container).tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    <?php if ($case_has_multi_containers) {?>
    jQuery('#legalCaseAddForm').on('submit', function() {
        if (!formReadyToSubmit) {
            jQuery('#case_has_multi_containers_modal').modal('show');
        }
        return formReadyToSubmit;
    });
    jQuery(document).on('click', '#case-submit', function() {
        formReadyToSubmit = true;
        jQuery('#legalCaseAddForm').submit();
    });
    <?php }?>
    jQuery('#legalCaseRelatedContainerLinkId').tooltipster({
        contentAsHTML: true,
        timer: 22800,
        animation: 'grow',
        delay: 200,
        theme: 'tooltipster-default',
        touchDevices: false,
        trigger: 'hover',
        maxWidth: 350,
        interactive: true
    });
    jQuery('#case_type_id', container).selectpicker();
    jQuery('#priority', container).selectpicker();
    jQuery('#legal_case_stage_id', container).selectpicker();
    jQuery('#client-type', container).selectpicker();
    jQuery('#opponent_member_type', container).selectpicker();
    jQuery('#legal_case_success_probability_id', container).selectpicker();
    jQuery('#legal_case_client_position_id', container).selectpicker();
    jQuery('#opponent-position', container).selectpicker();

    setDatePicker('#case-arrival-date', container);
    setDatePicker('#arrival-date', container);
    setDatePicker('#due-date', container);
    setDatePicker('#closed-on', container);
    if (jQuery('.visualize-hijri-date', container).length > 0) {
        getHijriDate(jQuery('#case-arrival-date', container), jQuery('#arrival-date-container', container));
        getHijriDate(jQuery('#arrival-date', container), jQuery('#filed-on-date-container', container));
        getHijriDate(jQuery('#due-date', container), jQuery('#due-date-container', container));
        getHijriDate(jQuery('#closed-on', container), jQuery('#closed-on-container', container));
    }
    //date piker event registration end
    jQuery(document).ready(function() {
        caseResources.getCaseCompanies(<?php echo (int) $legalCase["id"];?>);
        caseResources.getCaseContacts(<?php echo (int) $legalCase["id"];?>);
    });
    var legalCaseIdView = <?php echo (int) $legalCase["id"];?>;
    jQuery('#case_type_id', container).change(function() {
        assignmentPerType(this.value, 'matter', container);
    });
    var lookupDetailsRequestedBy = {
        'lookupField': jQuery('#lookup-requested-by', container),
        'errorDiv': 'requestedBy',
        'hiddenId': '#requested-by',
        'resultHandler': setRequestedByToForm
    };
    lookUpContacts(lookupDetailsRequestedBy, container);
    var lookupDetailsReferredBy = {
        'lookupField': jQuery('#referredByLookup', container),
        'errorDiv': 'referredBy',
        'hiddenId': '#referredBy',
        'resultHandler': setReferredByToForm
    };
    lookUpContacts(lookupDetailsReferredBy, container);

    function internalRefNumLink(targeturl) {
        var internalReference = document.getElementById("internalReference");
        internalReference.select();
        internalReference.setSelectionRange(0, 99999); /* For mobile devices */
        document.execCommand("copy");
        window.open(targeturl, '_blank');
    }
    //update the fees widgets
    function fetchClientAccountStatus(caseId) {

        jQuery.ajax({
            url: getBaseURL() + 'cases/get_matter_account_status/' + caseId,
            type: "GET",
            dataType: "json",
            success: function (response) {
                if (response.success) {
                    jQuery('#extCounsel-total_fees').html(formatMoney(response.data.total_fees));
                    jQuery('#extCounsel-amount_settled').html(formatMoney(response.data.total_settled));
                    jQuery('#extCounsel-balance_due').html(formatMoney(response.data.balance_due));
                } else {
                    pinesMessageV2({ ty: 'error', m: response.message });
                }
            }, complete: function () {

            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    function formatMoney(amount, decimalCount = 2, decimal = ".", thousands = ",") {
        try {
            decimalCount = Math.abs(decimalCount);
            decimalCount = isNaN(decimalCount) ? 2 : decimalCount;

            const negativeSign = amount < 0 ? "-" : "";

            let i = parseInt(amount = Math.abs(Number(amount) || 0).toFixed(decimalCount)).toString();
            let j = (i.length > 3) ? i.length % 3 : 0;

            return negativeSign + (j ? i.substr(0, j) + thousands : '') + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands) + (decimalCount ? decimal + Math.abs(amount - i).toFixed(decimalCount).slice(2) : "");
        } catch (e) {
            console.log(e)
        }
    }
    //list of matter feeNotes from cases/list_matter_feenotes(id). load the retruned response.html to #feeNotes-container modal
    function list_matter_feenotes(caseId) {
        jQuery.ajax({
            url: getBaseURL() + 'cases/list_matter_feenotes/' + caseId,
            type: "GET",
            dataType: "json",
            beforeSend: function() {
                jQuery("#loader-global").show();
            },
            success: function (response) {
                if (response.success) {
                    // Remove any existing modal to avoid duplicates
                    jQuery('#feeNotesModal').remove();
                    // Append the modal HTML to the body
                    jQuery('body').append(response.html);
                    // Show the modal
                    jQuery('#feeNotesModal').modal('show');
                } else {
                    pinesMessageV2({ ty: 'error', m: response.message });
                }
            },
            complete: function () {
                jQuery("#loader-global").hide();
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }



</script>
<style>
    #main-content-side .main-content-section {
        height: calc(100vh - 110px);
        margin-top: 54px;
    }
    #sub-menu-sidebar .left-menu{
        height: calc(100vh - 116px);
        margin-top: 60px !important;
    }
    #sub-menu-sidebar .left-menu .sub-menu a{
        width: 100%;
        display: block;
    }
    #main-container{
        padding: 0 !important;
    }
    .wrap-margin-bottom{
        margin-bottom: 0 !important;
    }
    .main-offcanvas{
        margin-top: 0 !important;
    }
    <?php
    if ($this->session->userdata("AUTH_language") != "english") {?>
    @media only screen and (max-width:1550px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 6vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1500px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 5vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1400px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 3.4vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1300px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 3.2vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1280px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 2.5vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    @media only screen and (max-width:1240px){
        .btn-meet-now a{
            text-overflow: ellipsis;
            overflow: hidden;
            white-space: nowrap;
            width: 1.7vw;
            font-size: 9px;
            display: inline-block;
        }
    }
    <?php    }    ?>
    .priority-critical{
        height: 20px
    }
</style>