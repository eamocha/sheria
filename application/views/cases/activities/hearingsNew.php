<div class="row m-0 col-md-12 no-margin no-padding mt-10 mb-10 case-hearing-container" id="hearings-<?php echo $stage_id; ?>-inside">
    <?php
    echo form_input(['id' => 'legal-case', 'value' => $case_id, 'data-field' => 'case_id', 'type' => 'hidden']);
    echo form_input(['id' => 'stage-id', 'value' => $stage_id, 'data-field' => 'stage_id', 'type' => 'hidden']);
    ?>

    <div id="events-details-container-toggel" class="no-padding flex-stages mb-10 w-100">
        <div onclick="toggleElements(jQuery('#hearings-container-<?php echo $stage_id; ?>-container-icon', this), jQuery('#hearings-container-<?php echo $stage_id; ?>', '.case-hearing-container'));">
            <span class="close-open-icon">
                <i id="hearings-container-<?php echo $stage_id; ?>-container-icon" class="fa-solid fa-chevron-down font-16"></i>
            </span>
        </div>

        <div class="flex-stages no-padding cursor-pointer-click">
            <label class="font-700 big-text-font-size mt-10"><?php echo $this->lang->line("hearings"); ?></label>
        </div>

        <div class="flex-end-item flex-stages no-padding prl-5 bg-eee-click action-add-btn">
            <div class="ml-5 cursor-pointer-click disable-anchor" onclick="legalCaseHearingForm(0, false, '', true, function(){legalCaseEvents.openHearingTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)}, '<?php echo !empty($stage_id) ? $stage_id : false; ?>', <?php echo !empty($stage_id) ? false : true; ?>);">
                <span class="close-open-icon center-round-icon">
                    <i class="fa fa fa-plus font-16"></i>
                </span>
                <span class="back-title-color"><?php echo $this->lang->line("add_new_hearing"); ?></span>
            </div>
        </div>
    </div>

    <div id="hearings-events-details-container" class="col-md-12 no-padding">
        <div class="col-md-12 no-padding">
            <div id="all-stage-hearings">
                <div class="col-md-12 no-padding" id="hearings-container-<?php echo $stage_id; ?>">
                    <?php if (!empty($hearings)): ?>
                        <?php foreach ($hearings as $key_hearing => $hearing): ?>
                            <?php
                            if (!empty($hearing["date"])) {
                                $hearing_date = new DateTime($hearing["date"]);
                                $hearing_date = $hearing_date->setTime(12, 0, 0);
                                $current_date = new DateTime();
                                $current_date = $current_date->setTime(12, 0, 0);
                                $is_due_hearing = $current_date < $hearing_date;
                                $due_to_hearing_diff = $hearing_date->diff($current_date)->days;
                                $hearing_color = $is_due_hearing ? "back-title-color" : ($due_to_hearing_diff == 0 ? "green_date" : "grey_91");
                            }
                            $date_postponed_hearing = new DateTime($hearing["postponed_date"]);
                            $hearing_day = date_create($hearing["date"]);
                            ?>

                            <div class="col-md-12 flex-row mb-10 no-padding list-card-container">
                                <div class="blue-card-border flex-stretch center-all border-radius-5px-ar">
                                    <span style="color: white">#<?php echo $total_rows - ($key_hearing + ($page_number - 1) * $page_limit); ?></span>
                                </div>

                                <div class="list-card hearing-list-card flex-stretch width--36px">
                                    <div class="d-flex mb-10">
                                        <div class="col-md-6 no-padding pull-right-arabic">
                                            <span class="bold-title font-18 <?php echo $hearing_color; ?> cursor-pointer-click bg-eee-click pull-right-arabic" onclick="legalCaseHearingForm('<?php echo $hearing["id"]; ?>','','<?php echo $case_id; ?>', false, function(){legalCaseEvents.openHearingTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)})">
                                                <span class="pull-right-arabic"><?php echo $hearings_model_code . $hearing["id"]; ?> - </span>
                                                <span class="pull-right-arabic">
                                                    <?php echo !$hijri_calendar_enabled ? date_format($hearing_day, "l, Y-m-d H:i") : date_format($hearing_day, "l, ") . " " . gregorianToHijri($hearing["date"], "Y-m-d") . " " . date_format($hearing_day, "H:i"); ?>
                                                </span>
                                            </span>
                                        </div>

                                        <div class="col-md-6 no-padding">
                                            <div class="purple_color pull-right flex-stages no-margin-important no-padding font-15">
                                                <?php if ($verification_process_enabled): ?>
                                                    <?php if ($hearing["verifiedSummary"] == 1): ?>
                                                        <img src="assets/images/icons/verified-hearing.svg" class="tooltip-title mrl-5" title="<?php echo $this->lang->line("hearing_verified_tooltip"); ?>" width="15"/>
                                                    <?php else: ?>
                                                        <?php if (0 < $hearing["id"]): ?>
                                                            <img class="tooltip-title mrl-5" title="<?php echo $this->lang->line("hearing_not_verified"); ?>" width="15" src="assets/images/icons/unverified-hearing.svg" />
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                <?php endif; ?>

                                                <img style="height: 27px" class="hearing-report-flag pull-right row-title tooltip-title mrl-20" width="15" src="assets/images/icons/hearing-report-<?php echo 0 < $hearing["clientReportEmailSent"] ? "" : "not-"; ?>sent.svg" title="<?php echo 0 < $hearing["clientReportEmailSent"] ? sprintf($this->lang->line("hearing_sent_report_flag_title"), $hearing["clientReportEmailSent"]) : $this->lang->line("hearing_not_sent_report_flag_title"); ?>">

                                                <div class="dropdown more">
                                                    <a class="btn btn-default btn-xs no-outline no-border no-padding font-18" data-toggle="dropdown" href="">
                                                        <i class="purple_color icon-alignment fa fa-ellipsis-v cursor-pointer-click"></i>
                                                    </a>
                                                    <div class="dropdown-menu pull-right" role="menu" aria-labelledby="dLabel">
                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="legalCaseHearingForm('<?php echo $hearing["id"]; ?>', '', '<?php echo $case_id; ?>', false, function(){legalCaseEvents.openHearingTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)})"><?php echo $this->lang->line("view_edit"); ?></a>

                                                        <?php $systemPreferences = $this->session->userdata("systemPreferences"); ?>
                                                        <?php if (isset($systemPreferences["AllowFeatureHearingVerificationProcess"]) && $systemPreferences["AllowFeatureHearingVerificationProcess"] == "yes"): ?>
                                                            <a class="dropdown-item bg-eee-click <?php echo $hearing["verifiedSummary"] == "1" ? "d-none" : ""; ?>" href="javascript:;" onclick="verifyHearingWindow('<?php echo $hearing["id"]; ?>','<?php echo $case_id; ?>')"><?php echo $this->lang->line("verify"); ?></a>
                                                        <?php endif; ?>

                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="legalCaseHearingForm('<?php echo $hearing["id"]; ?>', 'clone', '', true, function(){legalCaseEvents.openHearingTab('<?php echo $stage_id; ?>','<?php echo $case_id; ?>', true)})"><?php echo $this->lang->line("clone"); ?></a>
                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="generateHearingReport('<?php echo $hearing["id"]; ?>', function(){legalCaseEvents.openHearingTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true)})"><?php echo $this->lang->line("generate_hearing_report"); ?></a>
                                                        <a class="dropdown-item bg-eee-click <?php echo $hearing["judged"] == "yes" ? "d-none" : ""; ?>" href="javascript:;" onclick="hearingSetJudgment('<?php echo $hearing["id"]; ?>', '<?php echo $case_id; ?>', function(){legalCaseEvents.openHearingTab('<?php echo $stage_id; ?>','<?php echo $case_id; ?>', true)})"><?php echo $this->lang->line("set_judgment"); ?></a>
                                                        <a class="dropdown-item bg-eee-click" href='cases/hearing_export_to_word/<?php echo $hearing["id"]; ?>'><?php echo $this->lang->line("export_to_word"); ?></a>
                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="recordRelatedExpense('<?php echo $case_id; ?>', 'hearing', '<?php echo $hearing["id"]; ?>')"><?php echo $this->lang->line("record_expense"); ?></a>
                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="recordRelatedExpense('<?php echo $case_id; ?>', 'hearing', '<?php echo $hearing["id"]; ?>', true)"><?php echo $this->lang->line("bulk_expenses"); ?></a>
                                                        <a class="dropdown-item bg-eee-click" href="javascript:;" onclick="confirmationDialog('confirm_delete_record', {resultHandler: deleteCaseHearingWithCallback, parm: {id: '<?php echo $hearing["id"]; ?>', callback: function(){legalCaseEvents.openHearingTab(<?php echo !empty($stage_id) ? $stage_id : "null"; ?>,'<?php echo $case_id; ?>', true)}});"><?php echo $this->lang->line("delete"); ?></a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Rest of the hearing details (lawyers, advisors, postponement, documents, etc.) -->
                                    <!-- ... -->

                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div id="hearing-pagination-page-nb<?php echo !empty($stage_id) ? $stage_id : "null"; ?>-wrapper" class="pull-right no-padding pagination-box mrl-5">
                            <?php echo form_dropdown(
                                "hearing-pagination-page-nb-" . (!empty($stage_id) ? $stage_id : "null"),
                                ["10" => 10, "20" => 20, "50" => 50],
                                $page_limit,
                                "id=\"hearing-pagination-page-nb-" . (!empty($stage_id) ? $stage_id : "null") . "\" class=\"form-control pagination-select\" onchange=\"legalCaseEvents.changePageLimit(this," . (!empty($stage_id) ? $stage_id : "null") . "," . $case_id . ", 'hearings')\""
                            ); ?>
                        </div>

                        <div id="hearing-pagination-box-<?php echo !empty($stage_id) ? $stage_id : "null"; ?>" class="pull-right no-padding pagination-box"></div>
                    <?php else: ?>
                        <p class="font-700 big-text-font-size grey-color text-center">
                            <?php echo sprintf($this->lang->line("no_related_record_found"), $this->lang->line("hearing")); ?>
                        </p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    jQuery('#hearing-pagination-box-<?php echo !empty($stage_id) ? $stage_id : "null"; ?>').paginationX({
        page: <?php echo $page_number; ?>,
        limit: <?php echo $page_limit; ?>,
        total: '<?php echo $total_rows; ?>',
        pageShow: 3,
        min: 1,
        max: 3,
        visible: ['start','end','last','next','number'],
        clickFun: function (page, limit, total, pageTotal, pageShow) {
            legalCaseEvents.openHearingTab('<?php echo !empty($stage_id) ? $stage_id : "null"; ?>','<?php echo $case_id; ?>', true, page, <?php echo $page_limit; ?>);
        }
    });

    var disableMatter = '<?php echo $legalCase["archived"] == "yes" && isset($systemPreferences["disableArchivedMatters"]) && $systemPreferences["disableArchivedMatters"] ? true : false; ?>';
</script>