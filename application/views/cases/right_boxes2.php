<?php
$systemPreferences = $this->session->userdata("systemPreferences");
?>
<div class="primary-style col-md-3 right-side-section no-padding-left">
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-blue blue-opacity-bg">
            <i class="fa fa-users" aria-hidden="true"></i>
            <?php echo htmlspecialchars($this->lang->line("people")); ?>
            <button type="button" class="btn float-right btn-meet-now">
                <img width="15" height="15" src="assets/images/icons/microsoft-teams.svg" alt="Teams Icon">
                <a target="_blank" class="tooltip-title" title="<?php echo htmlspecialchars($this->lang->line("meet_now")); ?>" href="https://teams.microsoft.com/l/entity/0443dea6-e92d-4a36-b8b2-98f9c0b11095/matterID?context=<?php echo urlencode('{\"subEntityId\": \"' . htmlspecialchars($legalCase["id"]) . '\"}'); ?>"><?php echo htmlspecialchars($this->lang->line("meet_now")); ?></a>
            </button>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label"><?php echo htmlspecialchars($this->lang->line("provider_group")); ?></span></label>
                <div class="no-padding float-right flex-grow width-50-per">
                    <?php echo form_dropdown("provider_group_id", $Provider_Groups, $legalCase["provider_group_id"], "id=\"provider_group_id\" class=\"form-control select-picker\" form=\"legalCaseAddForm\" data-live-search=\"true\""); ?>
                    <div data-field="provider_group_id" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label"><?php echo htmlspecialchars($this->lang->line("assignee")); ?></span></label>
                <div class="no-padding float-right flex-grow width-50-per">
                    <?php if (!in_array($legalCase["user_id"], $usersProviderGroup)) { $usersProviderGroup[$legalCase["user_id"]] = $legalCase["Assignee"]; } echo form_dropdown("user_id", $usersProviderGroup, $legalCase["user_id"], "id=\"userId\" class=\"form-control select-picker\" form=\"legalCaseAddForm\" data-live-search=\"true\""); ?>
                    <div data-field="user_id" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label"><?php echo htmlspecialchars($this->lang->line("referred_by")); ?></span><a href="<?php echo site_url("contacts/edit/" . htmlspecialchars($legalCase["referredBy"])); ?>" id="referredByLinkId" class="icon-alignment <?php echo $legalCase["referredBy"] ? "" : "d-none"; ?>"><i class="fa fa-external-link"></i></a></label>
                <div class="no-padding float-right flex-grow">
                    <?php echo form_input(["name" => "referredBy", "id" => "referredBy", "type" => "hidden", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["referredBy"])); echo form_input(["name" => "referredByName", "id" => "referredByLookup", "class" => "lookup form-control", "placeholder" => htmlspecialchars($this->lang->line("start_typing")), "title" => htmlspecialchars($this->lang->line("start_typing")), "onblur" => "if (this.value === '') { jQuery('#referredByLinkId').addClass('d-none'); }", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["referredByName"])); ?>
                    <div data-field="referredBy" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label text-with-hint tooltip-title" title="<?php echo htmlspecialchars($legalCase["category"] == "Litigation" ? $this->lang->line("litigation_requested_by_helper") : $this->lang->line("matter_requested_by_helper")); ?>"><?php echo htmlspecialchars($this->lang->line("requested_by")); ?></span><a href="<?php echo site_url("contacts/edit/" . htmlspecialchars($legalCase["requestedBy"])); ?>" id="requestedByLinkId" target="_blank" class="icon-alignment <?php echo $legalCase["requestedBy"] ? "" : "d-none"; ?>"><i class="fa fa-external-link"></i></a></label>
                <div class="no-padding float-right flex-grow">
                    <?php echo form_input(["name" => "requestedBy", "id" => "requested-by", "type" => "hidden", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["requestedBy"])); echo form_input(["name" => "requestedByName", "id" => "lookup-requested-by", "class" => "form-control lookup", "placeholder" => htmlspecialchars($this->lang->line("start_typing")), "title" => htmlspecialchars($this->lang->line("start_typing")), "onblur" => "if (this.value === '') { jQuery('#requestedByLinkId').addClass('d-none'); }", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["requestedByName"])); ?>
                    <div data-field="requestedBy" class="inline-error d-none"></div>
                </div>
            </div>
            <?php if (!empty($custom_fields["people"])) { $this->load->view("custom_fields/form_custom_field_template_people_right", ["custom_fields" => $custom_fields["people"]]); } ?>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-purple blue-opacity-bg">
            <i class="fa fa-fw fa-lock" aria-hidden="true"></i>
            <?php echo htmlspecialchars($this->lang->line("privacy")); ?>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20 scroll case-privacy-container" id="case-privacy-container">
            <label class="control-label no-padding-right col-md-12 no-padding padding-top-10 min-width-80 no-margin-bottom">
                <span class="control-label"><?php echo htmlspecialchars($this->lang->line("shared_with")); ?></span>
                <div class="float-right">
                    <a class="btn btn-default btn-link pull-left <?php echo $legalCase["private"] == "yes" ? "d-none" : ""; ?>" id="privateLink" href="javascript:;" onClick="setAsPrivate(jQuery('#case-privacy-container', '#edit-legal-case-container'), 'cases', '', '', '', true);"><?php echo htmlspecialchars($this->lang->line("set_as_private")); ?></a>
                    <a class="btn btn-default btn-link pull-left <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>" id="publicLink" href="javascript:;" onClick="setAsPublic(jQuery('#case-privacy-container', '#edit-legal-case-container'), 'cases', true);"><?php echo htmlspecialchars($this->lang->line("set_as_public")); ?></a>
                    <?php echo form_input(["id" => "private", "name" => "private", "value" => htmlspecialchars($legalCase["private"]), "type" => "hidden"], "", "form=\"legalCaseAddForm\""); ?>
                </div>
            </label>
            <div class="col-md-12 no-padding">
                <label class="shared-with-label padding-top7 <?php echo $legalCase["private"] == "yes" ? "d-none" : ""; ?>"><?php echo htmlspecialchars($this->lang->line("everyone")); ?></label>
                <div class="lookup-box-container margin-bottom input-group col-md-12 no-padding <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?> users-lookup-container form-group">
                    <?php echo form_input(["id" => "lookup-case-users", "name" => "lookupCaseUsers", "class" => "form-control users-lookup", "title" => htmlspecialchars($this->lang->line("start_typing")), "form" => "legalCaseAddForm"], ""); ?>
                    <span class="input-group-addon bs-caret users-lookup-icon" id="shared-with-users" onclick="jQuery('#lookup-case-users', '#legal-matter-dialog').focus();"><span class="caret"></span></span>
                </div>
                <div id="selected-watchers" class="no-margin <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>">
                    <?php
                    if ($legalCasewatchersUsers) {
                        foreach ($legalCasewatchersUsers as $id => $name) {
                            echo "<div class=\"flex-item-box row multi-option-selected-items no-margin w-100 height-30px\" id=\"watchers" . htmlspecialchars($id) . "\">";
                            echo "<span id=\"" . htmlspecialchars($id) . "\">" . htmlspecialchars(isset($legalCasewatchersUsersStatus) && $legalCasewatchersUsersStatus[$id] == "Inactive" ? $name . " (" . $this->lang->line("Inactive") . ")" : $name) . "</span>";
                            echo form_input(["value" => htmlspecialchars($id), "name" => "Legal_Case_Watchers_Users[]", "type" => "hidden", "form" => "legalCaseAddForm"]);
                            echo "<a href=\"javascript:;\" class=\"btn btn-default btn-sm btn-link pull-right remove-button flex-end-item\" tabindex=\"-1\" onClick=\"removeBoxElement(jQuery(this.parentNode),'#selected-watchers','watcher-lookup-container','#ticket-modifiable-fields');\"><i class=\"fa-solid fa-xmark\"></i></a>";
                            echo "</div>";
                        }
                    } else {
                        $userId = $this->session->userdata("AUTH_user_id");
                        echo "<div class=\"flex-item-box row multi-option-selected-items no-margin w-100 height-30px\" id=\"watchers" . htmlspecialchars($userId) . "\">";
                        echo "<span id=\"" . htmlspecialchars($userId) . "\">" . htmlspecialchars($this->session->userdata("AUTH_userProfileName")) . "</span>";
                        echo form_input(["value" => htmlspecialchars($userId), "name" => "Legal_Case_Watchers_Users[]", "type" => "hidden", "form" => "legalCaseAddForm"]);
                        echo "<a href=\"javascript:;\" class=\"btn btn-default btn-sm btn-link pull-right remove-button flex-end-item\" tabindex=\"-1\" onClick=\"removeBoxElement(jQuery(this.parentNode),'#selected-watchers','watcher-lookup-container','#ticket-modifiable-fields');\"><i class=\"fa-solid fa-xmark\"></i></a>";
                        echo "</div>";
                    }
                    ?>
                </div>
                <div data-field="case_watchers" class="inline-error d-none padding-5"></div>
                <div class="col-md-12 no-padding autocomplete-helper <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>">
                    <div class="inline-text"><?php echo htmlspecialchars($this->lang->line("helper_autocomplete_privacy")); ?></div>
                </div>
                <div class="mt-5 alert alert-info col-md-12 alert-message <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>" role="alert">
                    <span><?php echo htmlspecialchars(sprintf($this->lang->line("case_watchers_info"), $legalCase["category"] == "Litigation" ? $this->lang->line("litigation") : $this->lang->line("matter"))); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-green blue-opacity-bg">
            <i class="fa fa-calendar no-border" aria-hidden="true"></i>
            <?php echo htmlspecialchars($this->lang->line("date_and_times")); ?>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20" id="date-pikers-case-right-boxes" style="position: relative">
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label required-label line-height-20 text-with-hint tooltip-title" title="<?php echo htmlspecialchars($legalCase["category"] == "Litigation" ? $this->lang->line("litigation_arrival_date_helper") : $this->lang->line("matter_arrival_date_helper")); ?>"><?php echo htmlspecialchars($this->lang->line("arrival_date")); ?></span></label>
                <div class="no-padding float-right flex-grow" id="arrival-date-container">
                    <div class="input-group date no-padding date-picker" id="case-arrival-date">
                        <?php echo form_input(["name" => "caseArrivalDate_Hidden", "id" => "caseArrivalDate_Hidden", "type" => "hidden"], htmlspecialchars($legalCase["caseArrivalDate"]), "form=\"legalCaseAddForm\""); echo form_input(["name" => "caseArrivalDate", "id" => "caseArrivalDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#caseArrivalDate_Hidden').val(''); }"], htmlspecialchars($legalCase["caseArrivalDate"]), "form=\"legalCaseAddForm\""); ?>
                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="caseArrivalDate" class="inline-error d-none"></div>
                </div>
                <?php if ($systemPreferences["hijriCalendarConverter"]) { echo "<div class=\"visualize-hijri-date no-margin-left no-margin-right\"><a href=\"javascript:;\" id=\"date-conversion\" onclick=\"HijriConverter(jQuery('#caseArrivalDate', '#casesDetails'), true);\" title=\"" . htmlspecialchars($this->lang->line("hijri_date_converter")) . "\" class=\"btn btn-link float-right no-padding\">" . htmlspecialchars($this->lang->line("hijri")) . "</a></div>"; } ?>
            </div>
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 trim-width-33-per text-with-hint tooltip-title" style="<?php echo $this->is_auth->is_layout_rtl() ? "min-width: 110px" : "min-width: 103px"; ?>" title="<?php echo htmlspecialchars($legalCase["category"] == "Litigation" ? $this->lang->line("litigation_filed_on_helper") : $this->lang->line("matter_filed_on_helper")); ?>"><span class="control-label line-height-20"><?php echo htmlspecialchars($this->lang->line("filed_on")); ?></span></label>
                <div class="no-padding float-right flex-grow" id="filed-on-date-container">
                    <div class="input-group date no-padding date-picker" id="arrival-date">
                        <?php echo form_input(["name" => "arrivalDate", "id" => "arrivalDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#arrivalDate_Hidden').val(''); }"], htmlspecialchars($legalCase["arrivalDate"]), "form=\"legalCaseAddForm\""); echo form_input(["name" => "arrivalDate_Hidden", "id" => "arrivalDate_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["arrivalDate"]), "form=\"legalCaseAddForm\""); ?>
                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="arrivalDate" class="inline-error d-none"></div>
                </div>
                <?php if ($systemPreferences["hijriCalendarConverter"]) { echo "<div class=\"visualize-hijri-date no-margin-left no-margin-right\"><a href=\"javascript:;\" id=\"date-conversion\" onclick=\"HijriConverter(jQuery('#arrivalDate', '#casesDetails'), true);\" title=\"" . htmlspecialchars($this->lang->line("hijri_date_converter")) . "\" class=\"btn btn-link float-right no-padding\">" . htmlspecialchars($this->lang->line("hijri")) . "</a></div>"; } ?>
            </div>
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label line-height-20 text-with-hint tooltip-title" title="<?php echo htmlspecialchars($legalCase["category"] == "Litigation" ? $this->lang->line("litigation_due_date_helper") : $this->lang->line("matter_due_date_helper")); ?>"><?php echo htmlspecialchars($this->lang->line("due_date")); ?></span></label>
                <div class="no-padding float-right flex-grow" id="due-date-container">
                    <div class="input-group date no-padding date-picker" id="due-date">
                        <?php echo form_input(["name" => "dueDate", "id" => "dueDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "data-validation-engine" => "validate[custom[date]]", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#dueDate_Hidden').val(''); }"], htmlspecialchars($legalCase["dueDate"]), "form=\"legalCaseAddForm\""); echo form_input(["name" => "dueDate_Hidden", "id" => "dueDate_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["dueDate"]), "form=\"legalCaseAddForm\""); ?>
                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="dueDate" class="inline-error d-none"></div>
                    <div class="float-right padding-right-15 <?php echo !$notify_before && !$legalCase["dueDate"] ? "d-none" : ""; ?>" id="notify-me-before-link"><a href="javascript:;" onclick="notifyMeBefore(jQuery('#edit-legal-case-container'));"><?php echo htmlspecialchars($this->lang->line("notify_me_before")); ?></a></div>
                </div>
                <?php if ($systemPreferences["hijriCalendarConverter"]) { echo "<div class=\"visualize-hijri-date no-margin-left no-margin-right\"><a href=\"javascript:;\" id=\"date-conversion\" onclick=\"HijriConverter(jQuery('#dueDate', '#casesDetails'), true);\" title=\"" . htmlspecialchars($this->lang->line("hijri_date_converter")) . "\" class=\"btn btn-link float-right no-padding\">" . htmlspecialchars($this->lang->line("hijri")) . "</a></div>"; } ?>
            </div>
            <div class="form-group no-padding d-none" id="notify-me-before-container">
                <div class="flex-item-box form-group no-padding min-height-35">
                    <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label line-height-20"><?php echo htmlspecialchars($this->lang->line("notify_me_before")); ?></span></label>
                    <div class="no-padding float-right flex-grow" id="notify-me-before">
                        <?php echo form_input(["name" => "notify_me_before[id]", "form" => "legalCaseAddForm", "value" => $notify_before["id"] ? $notify_before["id"] : "", "disabled" => true, "type" => "hidden"]); echo form_input(["name" => "notify_me_before[time]", "form" => "legalCaseAddForm", "class" => "form-control", "value" => $notify_before["time"] ? $notify_before["time"] : $systemPreferences["reminderIntervalDate"], "id" => "notify-me-before-time", "disabled" => true, "data-validation-engine" => "validate[required]"]); echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, $notify_before["time_type"], "class='form-control select-picker' id='notify-me-before-time-type' form='legalCaseAddForm' disabled data-validation-engine='validate[required]'"); ?>
                    </div>
                </div>
                <div class="flex-item-box form-group no-padding min-height-35">
                    <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label line-height-20"><?php echo htmlspecialchars($this->lang->line("reminder_by")); ?></span></label>
                    <div class="flex-grow">
                        <?php echo form_dropdown("notify_me_before[type]", $notify_me_before_types, $notify_before["type"], "class='form-control select-picker' id='notify-me-before-type' form='legalCaseAddForm' disabled data-validation-engine='validate[required]'"); ?>
                    </div>
                    <div><a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#edit-legal-case-container'));" class="btn btn-link no-padding"><i class="icon-alignment fa fa-trash light_red-color"></i></a></div>
                </div>
            </div>
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label line-height-20"><?php echo htmlspecialchars($this->lang->line("closed_on")); ?></span></label>
                <div class="no-padding float-right flex-grow" id="closed-on-container">
                    <div class="input-group date no-padding date-picker" id="closed-on">
                        <?php echo form_input(["name" => "closedOn_Hidden", "id" => "closedOn_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], htmlspecialchars($legalCase["closedOn"]), "form=\"legalCaseAddForm\""); echo form_input(["name" => "closedOn", "id" => "closedOn", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "data-validation-engine" => "validate[custom[date]]", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#closedOn_Hidden').val(''); }"], htmlspecialchars($legalCase["closedOn"]), "form=\"legalCaseAddForm\""); ?>
                        <span class="input-group-addon"><i class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="closedOn" class="inline-error d-none"></div>
                </div>
                <?php if ($systemPreferences["hijriCalendarConverter"]) { echo "<div class=\"visualize-hijri-date no-margin-left no-margin-right\"><a href=\"javascript:;\" id=\"date-conversion\" onclick=\"HijriConverter(jQuery('#closedOn', '#casesDetails'), true);\" title=\"" . htmlspecialchars($this->lang->line("hijri_date_converter")) . "\" class=\"btn btn-link float-right no-padding\">" . htmlspecialchars($this->lang->line("hijri")) . "</a></div>"; } ?>
            </div>
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-120 trim-width-33-per"><span class="control-label line-height-20 text-with-hint tooltip-title" title="<?php echo htmlspecialchars($this->lang->line("supported_time_units")); ?>"><?php echo htmlspecialchars($this->lang->line("estimated_effort")); ?></span></label>
                <div class="no-padding float-right flex-grow">
                    <?php echo form_input(["name" => "estimatedEffort", "form" => "legalCaseAddForm", "id" => "estimatedEffortHour", "data-validation-engine" => "validate[funcCall[validateHumanReadableTime]]", "class" => "no-margin form-control", "onblur" => "convertMinsToHrsMins(jQuery(this));"], $this->timemask->timeToHumanReadable($legalCase["estimatedEffort"]), "form=\"legalCaseAddForm\""); ?>
                    <div data-field="estimatedEffort" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per"><span class="control-label line-height-20"><?php echo htmlspecialchars($this->lang->line("effectiveEffort")); ?></span><a href="<?php echo site_url("cases/time_logs/" . htmlspecialchars($legalCase["id"])); ?>" class="icon-alignment btn btn-default btn-link"><i class="fa fa-external-link"></i></a></label>
                <div class="no-padding float-right flex-grow">
                    <span class="control-label line-height-20 float-right padding-15 bold-font-family"><?php echo $legalCase["effectiveEffort"] ? $this->timemask->timeToHumanReadable($legalCase["effectiveEffort"]) : 0; ?></span>
                </div>
            </div>
            <?php if (!empty($custom_fields["date"])) { $this->load->view("custom_fields/form_custom_field_template_date_right", ["custom_fields" => $custom_fields["date"]]); } ?>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-red blue-opacity-bg flex-row">
            <i class="fa fa-building mx-1" aria-hidden="true"></i>
            <span class="mx-1"><?php echo htmlspecialchars($this->lang->line("related_companies")); ?></span>
            <div class="float-right cursor-pointer-click flex-end-item" onclick="addContactPopup('company','addCompany', 'cases/edit/','<?php echo htmlspecialchars($this->lang->line("add_company")); ?>','<?php echo htmlspecialchars($this->lang->line("company_name")); ?>','company_id','companyType')">
                <i class="fa fa-plus no-padding" aria-hidden="true"></i>
                <?php echo htmlspecialchars($this->lang->line("add_new")); ?>
            </div>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div id="related-case-companies-container"></div>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-gold blue-opacity-bg flex-row">
            <i class="fa fa-users mx-1" aria-hidden="true"></i>
            <span class="mx-1"><?php echo htmlspecialchars($this->lang->line("related_contacts")); ?></span>
            <div class="float-right cursor-pointer-click flex-end-item" onclick="addContactPopup('contact','addContact', 'cases/edit/', '<?php echo htmlspecialchars($this->lang->line("add_contact")); ?>','<?php echo htmlspecialchars($this->lang->line("contact_name")); ?>','contact_id','contactType')">
                <i class="fa fa-plus no-padding" aria-hidden="true"></i>
                <?php echo htmlspecialchars($this->lang->line("add_new")); ?>
            </div>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div id="related-case-contacts-container"></div>
        </div>
    </div>
    <div class="right-box-container" onclick="collapse('audit-section-heading', 'audit-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true);">
        <h2 id="audit-section-heading" class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-purple blue-opacity-bg">
            <i class="audit-icon"></i>
            <a href="javascript:;" class="toggle-title float-right"><i class="fa fa-angle-right icon font-18 purple_color"> </i></a>
            <?php echo htmlspecialchars($this->lang->line("audit")); ?>
        </h2>
        <div id="audit-section-content" class="d-none right-box-body box-shadow_container padding-10 mb-20 audit-section-content">
            <?php $this->load->view("cases/audit"); ?>
        </div>
    </div>
</div>