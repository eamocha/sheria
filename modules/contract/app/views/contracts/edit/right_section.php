<div class="contract-dates edit-dates">
    <h4><?php echo $this->lang->line("dates"); ?></h4>
    <div>
        <!-- Contract Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/calendar.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("contract_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "contract_date", "value" => $contract["contract_date"], "id" => "date-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="contract_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Effective Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/calendar.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("effective_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("effective_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "effective_date", "value" => $contract["effective_date"], "id" => "effective_date", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="effective_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Commencement Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("contract_start_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_start_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "start_date", "value" => $contract["start_date"], "id" => "start-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="start_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Contract Duration -->
        <div class="form-group row">
            <?php $duration_types = ["days" => $this->lang->line("days"), "months" => $this->lang->line("months"), "years" => $this->lang->line("years")]; ?>
            <div class="p-0 col-lg-3 col-md-6 col-sm-6 col-xs-6 label-div">
                <img src="assets/images/contract/clock.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("contract_duration"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_duration_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-9 col-md-6 col-sm-6 col-xs-6 d-flex align-items-center">
                <div class="mr-2 flex-grow-1">
                    <?php echo form_input(["name" => "contract_duration", "value" => $contract["contract_duration"], "id" => "duration-input", "placeholder" => "Duration", "class" => "form-control", "autocomplete" => "off"]); ?>
                    <div data-field="contract_duration" class="inline-error d-none"></div>
                </div>
                <div class="flex-shrink-0" style="width: 120px;">
                    <?php echo form_dropdown("duration_type", $duration_types, "", "id=\"duration-type\" class=\"form-control select-picker\""); ?>
                    <div data-field="duration_type" class="inline-error d-none"></div>
                </div>
            </div>
        </div>

        <!-- Expected Completion Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("expected_completion_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("expected_completion_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "expected_completion_date", "value" => $contract["expected_completion_date"], "id" => "expected-completion-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="expected_completion_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Actual Completion Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("actual_completion_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("actual_completion_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "actual_completion_date", "value" => $contract["actual_completion_date"], "id" => "actual-completion-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="actual_completion_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Expiry Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/icon.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("expiry_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("expiry_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "end_date", "value" => $contract["end_date"], "id" => "end-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="end_date" class="inline-error d-none"></div>
                <div class="col-md-10 p-0" id="notify-me-before-link">
                    <span class="assign-to-me-link-id-wrapper">
                        <a href="javascript:;" id="notify-me-link"
                           onclick="notifyMeBeforeRenewal(jQuery('#right-section', '.contract-container'));"><?php echo $this->lang->line("notify_me_before"); ?></a>
                    </span>
                </div>
            </div>
        </div>

        <!-- Notify Me Before Section -->
        <div class="form-group row d-none" id="notify-me-before-container">
            <div class="col-md-12 form-group row p-0">
                <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                    <img src="assets/images/contract/renewal.svg" height="25" width="25"
                         class="filter-color"><b
                            class="col-form-label"><?php echo $this->lang->line("notify_me_before"); ?>:&nbsp </b>
                </div>
                <div class="row p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                    <?php echo form_input(["name" => "notify_me_before[id]", "value" => $notify_before["id"] ?? "", "disabled" => true, "type" => "hidden"]); ?>
                    <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                        <?php echo form_input(["name" => "notify_me_before[time]", "class" => "form-control", "value" => $notify_before["time"] ?? "90", "id" => "notify-me-before-time", "disabled" => true]); ?>
                    </div>
                    <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12 pr-0">
                        <?php echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, $notify_before["time_type"] ?? "days", "class=\"form-control select-picker\" id=\"notify-me-before-time-type\" disabled"); ?>
                    </div>
                    <div class="col-lg-2 col-md-12 col-sm-12 col-xs-12 p-0">
                        <a href="javascript:;"
                           onclick="hideRemindMeBefore(jQuery('#right-section', '.contract-container'));"
                           class="btn btn-link">
                            <i class="red fa-solid fa-trash-can"></i>
                        </a>
                    </div>
                    <div data-field="notify_before" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/renewal.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("notify_users"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <select name="notifications[emails][]"
                        placeholder="<?php echo $this->lang->line("select_users"); ?>" id="notify-to-emails"
                        multiple="multiple" tabindex="-1">
                </select>
                <div data-field="emails" class="inline-error d-none"></div>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/renewal.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("notify_teams"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <select name="notifications[teams][]"
                        placeholder="<?php echo $this->lang->line("select_assigned_teams"); ?>" id="notify-to-teams"
                        multiple="multiple" tabindex="-1">
                    <?php
                    if (is_array($notifications["teams"])) {
                        foreach ($notifications["teams"] as $key => $val) {
                            echo '<option selected="selected" value="' . $val["id"] . '">' . $val["name"] . '</option>';
                        }
                    }
                    ?>
                </select>
                <div data-field="teams" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Performance Security Start Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("perf_security_commencement_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("perf_security_commencement_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "perf_security_commencement_date", "value" => $contract["perf_security_commencement_date"], "id" => "perf-security-commencement-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="perf_security_commencement_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Performance Security Expiry Date -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("perf_security_expiry_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("perf_security_expiry_date_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["name" => "perf_security_expiry_date", "value" => $contract["perf_security_expiry_date"], "id" => "perf-security-expiry-date-input", "placeholder" => "YYYY-MM-DD", "class" => "form-control datepicker", "autocomplete" => "off"]); ?>
                <div data-field="perf_security_expiry_date" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Renewal -->
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/renewal.svg" height="25" width="25" class="filter-color">
                <b class="col-form-label"><?php echo $this->lang->line("renewal"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("renewal_helper"); ?>"></i>&nbsp</b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_dropdown("renewal_type", $renewals, $contract["renewal_type"], "id=\"renewal\" class=\"form-control select-picker\" onchange=\"renewalEvents(jQuery('#right-section', '#contracts-details'));\""); ?>
                <div data-field="renewal_type" class="inline-error d-none"></div>
            </div>
        </div>

        <!-- Custom Date Fields -->
        <?php
        if (!empty($custom_fields["date"])) {
            foreach ($custom_fields["date"] as $date_field) {
                echo '<div class="form-group row">
                        <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                            <img src="assets/images/contract/calendar.svg" height="18" width="18" class="filter-color">
                            <b class="col-form-label">' . $date_field["customName"] . ' &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="' . $date_field["customName"] . '_helper"></i>&nbsp</b>
                            ' . $date_field["hidden_custom_field_id"] . $date_field["hidden_value_id"] . $date_field["hidden_record_id"] . '
                        </div>
                        <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">';
                if ($date_field["type"] == "date_time") {
                    echo '<div id="date-custom-field-' . $date_field["id"] . '">
                            ' . $date_field["custom_field"] . '
                          </div>';
                } else {
                    echo '<div id="date-custom-field-' . $date_field["id"] . '" class="input-group date" data-date-format="mm-dd-yyyy">
                            ' . $date_field["custom_field"] . '
                          </div>';
                }
                echo '</div></div>';
            }
        }
        ?>
    </div>
</div>

<!-- Contract Details Section (Unchanged) -->
<div class="details-right-section edit-details-right-section">
    <h4><?php echo $this->lang->line("contract_details"); ?></h4>
    <div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/document.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("reference_number"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input("reference_number", $contract["reference_number"], "id=\"ref-nb\" class=\"form-control\" dir=\"auto\" autocomplete=\"off\""); ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/group.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("requester"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 requester-container">
                <div class="col-md-12 p-0 users-lookup-container margin-bottom-5">
                    <?php echo form_input(["name" => "requester_id", "id" => "requester-id", "value" => $contract["requester_id"], "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "requester_name", "id" => "requester-lookup", "value" => $contract["requester"], "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]); ?>
                </div>
                <div data-field="requester_id" class="inline-error d-none"></div>
            </div>
        </div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/group.svg" height="25" width="25" class="filter-color"><b   class="col-form-label"><?php echo $this->lang->line("provider_group"); ?>:&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                <?php echo form_input(["id" => "all-users-provider-group", "value" => $assigned_team_id, "type" => "hidden"]); ?>
                <?php echo form_dropdown("assigned_team_id", $assigned_teams, $contract["assigned_team_id"], "id=\"assigned-team-id\" class=\"form-control select-picker\" data-live-search=\"true\""); ?>
                <div data-field="assigned_team_id" class="inline-error d-none"></div>
            </div>
        </div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/assignee.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("assignee"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12  col-xs-12">
                <?php echo form_dropdown("assignee_id", $assignees, $contract["assignee_id"], "id=\"assignee-id\" data-live-search=\"true\" class=\"form-control select-picker\""); ?>
            </div>
        </div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/default-user.svg" height="25" width="25"
                     class="filter-color"><b
                        class="col-form-label"><?php echo $this->lang->line("authorized_signatory"); ?>:&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 authorized-signatory-container">
                <div class="col-md-12 p-0 users-lookup-container margin-bottom-5">
                    <?php echo form_input(["name" => "authorized_signatory", "id"=> "authorized-signatory-id", "value" => $contract["authorized_signatory"], "type" => "hidden"]); ?>
                    <?php echo form_input(["name" => "authorized_signatory_name", "id" => "authorized-signatory-lookup", "value" => $contract["authorized_signatory_name"], "class" => "form-control lookup", "title" => $this->lang->line("start_typing")]); ?>
                </div>
                <div data-field="authorized_signatory" class="inline-error d-none"></div>
            </div>
        </div>
        <div class="form-group row">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/contributors.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("contributors"); ?>
                    :&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12  col-xs-12">
                <select name="contributors[]" id="contributors" multiple="multiple" class="selectized" tabindex="-1"
                        style="display: none;">
                    <?php
                    if (isset($contributors)) {
                        foreach ($contributors as $user) {
                            echo '<option selected="selected" value="' . $user["id"] . '">' . ($user["status"] == "Inactive" ? htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($user["name"])) . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        <div id="contract-watchers" class="contract-watchers form-group row <?php echo $visible_to_cp == 1 ? "" : "d-none"; ?>">
            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                <img src="assets/images/contract/contract_watchers.svg" height="25" width="25"
                     class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("watchers"); ?>:&nbsp </b>
            </div>
            <div class="p-0 col-lg-6 col-md-12 col-sm-12  col-xs-12 watcher-lookup-container">
                <div id="fields-to-modify" class="col-md-12 no-padding users-lookup-container margin-bottom-5">
                    <?php echo form_input(["type" => "hidden", "id" => "contract-id", "name" => "contract_id", "value" => $contract_id]); ?>
                    <?php echo form_input(["type" => "hidden", "id" => "object-category", "name" => "object_category", "class" => "object-category", "value" => "contract"]); ?>
                    <?php echo form_input(["id" => "lookup-watchers", "name" => "lookupWatchers", "class" => "form-control lookup search margin-bottom", "onChange" => "showCustomerPortal.handleMultiselectLookupInput('#contract-id');"]); ?>
                    <div id="selected-watchers" class="height-50 no-margin escape-bottom-padding <?php echo 0 < count($contract_watchers) ? "selected-item-box" : ""; ?>">
                        <?php
                        if ($contract_watchers) {
                            foreach ($contract_watchers as $watcher) {
                                echo '<div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="contract_watchers' . $watcher["id"] . '">
                                        <span id="' . $watcher["id"] . '">' . $watcher["name"] . '</span>';
                                echo form_input(["value" => $watcher["id"], "name" => "contract_watchers[]", "type" => "hidden"]);
                                echo '<a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onClick="removeBoxElement(jQuery(this.parentNode),\'#selected-watchers\',\'watcher-lookup-container\',\'#fields-to-modify\');">
                                        <i class="fa fa-remove"></i>
                                      </a>
                                  </div>';
                            }
                        }
                        ?>
                    </div>
                    <div data-field="contract_watchers" class="inline-error hide"></div>
                </div>
            </div>
        </div>
        <div id="main-contract-privacy-container">
            <div class="form-group row" id="contract-privacy-container">
                <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                    <img src="assets/images/contract/watchers.svg" height="25" width="25"
                         class="filter-color"><b class="col-form-label"><?php echo $this->lang->line("privacy"); ?>
                        :&nbsp </b>
                </div>
                <div class="p-0 col-lg-6 col-md-12 col-sm-12  col-xs-12">
                    <select name="watchers[]" placeholder="<?php echo $this->lang->line("select_users"); ?>"
                            class="selectized" id="watchers" multiple="multiple" tabindex="-1" style="display: none;">
                        <?php
                        if (!empty($watchers)) {
                            foreach ($watchers as $watcher) {
                                echo '<option selected="selected" value="' . $watcher["id"] . '"> ' . ($watcher["status"] === "Inactive" ? $watcher["name"] . "(" . $this->lang->line("Inactive") . ")" : $watcher["name"]) . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <a class="action-btn <?php echo $contract["private"] ? "d-none" : ""; ?>" id="private-link"
                       href="javascript:;"
                       onclick="setAsPrivate(jQuery('#contract-privacy-container', '.contract-container'), jQuery('#watchers', '#contract-privacy-container'), '<?php echo $contract["createdBy"]; ?>', '<?php echo $this->session->userdata("AUTH_user_id"); ?>');"><?php echo $this->lang->line("set_as_private"); ?></a>
                    <a class="action-btn <?php echo $contract["private"] ? "" : "d-none"; ?>" id="public-link"
                       href="javascript:;"
                       onClick="setAsPublic(jQuery('#contract-privacy-container'));"><?php echo $this->lang->line("set_as_public"); ?></a>
                    <?php echo form_input(["id" => "private", "name" => "private", "value" => $contract["private"] ? 1 : 0, "type" => "hidden"]); ?>
                </div>
            </div>
            <?php
            if (!empty($custom_fields["people"])) {
                foreach ($custom_fields["people"] as $field) {
                    echo '<div class="form-group row">
                            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12 label-div">
                                <img src="assets/images/contract/' . $field["type_data"] . '.svg" height="25" width="25"
                                     class="filter-color"><b class="col-form-label">' . $field["customName"] . ':&nbsp </b>
                            </div>
                            <div class="p-0 col-lg-6 col-md-12 col-sm-12 col-xs-12">
                                ' . $field["hidden_custom_field_id"] . $field["hidden_value_id"] . $field["hidden_record_id"] . $field["custom_field"] . '
                            </div>
                          </div>';
                }
            }
            ?>
        </div>
        <div class="button-actions-div">
            <div class="form-group row">
                <button type="button" class="button-save"
                        id="form-submit"><?php echo $this->lang->line("save"); ?></button>
                <button type="button" class="btn btn-link button-cancel"
                        onclick="cancelEditingForm('<?php echo $contract["id"]; ?>');"><?php echo $this->lang->line("cancel"); ?></button>
            </div>
        </div>
    </div>
</div>
    <script>
        //availableUsers = //;
        isPrivate = '<?php echo $contract["private"] == 1; ?>';
        availableUsers = <?php echo json_encode($users_list); ?>;
        availableEmails = <?php echo json_encode($users_emails); ?>;
        availableAssignedTeams = <?php echo json_encode($assigned_teams_list); ?>;
        var selectedEmails = <?php echo $notifications["emails"] ? json_encode(explode(";", $notifications["emails"])) : json_encode(explode(";", $this->session->userdata("AUTH_email_address"))); ?>;
        //var selectedTeams = //;
        // externalToEmails = 'null';
        <?php
        if ($notify_before) {
            echo '            notifyMeBeforeRenewal(jQuery(\'#right-section\', \'#contracts-details\'));
        ';
        }
        ?>
    </script>

<script>
jQuery(function($) {
    // Helper to add days/months/years to a date
    function addToDate(date, value, type) {
        let d = new Date(date);
        if (isNaN(d)) return null;
        value = parseInt(value, 10);
        if (type === 'days') {
            d.setDate(d.getDate() + value);
        } else if (type === 'months') {
            d.setMonth(d.getMonth() + value);
        } else if (type === 'years') {
            d.setFullYear(d.getFullYear() + value);
        }
        return d;
    }

    // Format date as YYYY-MM-DD
    function formatDate(date) {
        if (!date) return '';
        let m = (date.getMonth() + 1).toString().padStart(2, '0');
        let d = date.getDate().toString().padStart(2, '0');
        return date.getFullYear() + '-' + m + '-' + d;
    }

    function updateDates() {
        let duration = $('#duration-input').val();
        let durationType = $('#duration-type').val();
        let startDateStr = $('#start-date-input').val();

        if (!duration || !durationType || !startDateStr) {
            // Clear calculated fields if not enough data
            $('#expected-completion-date-input').val('');
            $('#perf-security-commencement-date-input').val('');
            $('#perf-security-expiry-date-input').val('');
            return;
        }

        let startDate = new Date(startDateStr);
        if (isNaN(startDate)) {
            $('#expected-completion-date-input').val('');
            $('#perf-security-commencement-date-input').val('');
            $('#perf-security-expiry-date-input').val('');
            return;
        }

        // Calculate expected completion date
        let expectedCompletion = addToDate(startDate, duration, durationType);
        $('#expected-completion-date-input').val(formatDate(expectedCompletion));

        // perf_security_commencement_date = start_date
        $('#perf-security-commencement-date-input').val(formatDate(startDate));

        // perf_security_expiry_date = start_date + duration + 30 days
        let perfSecExpiry = addToDate(startDate, parseInt(duration) + 30, durationType === 'days' ? 'days' : durationType === 'months' ? 'months' : 'years');
        if (durationType !== 'days') {
            // For months/years, add duration first, then 30 days
            perfSecExpiry = addToDate(expectedCompletion, 30, 'days');
        }
        $('#perf-security-expiry-date-input').val(formatDate(perfSecExpiry));
    }

    // When duration, duration type, or start date changes, recalculate
    $('#duration-input, #duration-type, #start-date-input').on('change keyup', updateDates);

    // Optionally, trigger on page load if values are present
    updateDates();
});

jQuery(document).ready(function () {
    jQuery('.datepicker').datepicker({
        dateFormat: 'yy-mm-dd'
    });
});
</script>