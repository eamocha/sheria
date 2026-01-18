<?php $systemPreferences = $this->session->userdata("systemPreferences"); ?>
<div class="primary-style col-md-3 mt-2 right-side-section no-padding-left">
<div class="card mb-4"> <?php // Example $outcome array structure
        $outcome = [
            'judgment_summary' => 'Judgment for Plaintiff with damages awarded',
            'judgment_date' => '2023-11-15',
            'appeal_deadline' => '2023-12-15',
            'compliance_due' => '2024-01-15',
            'updated_at' => '2023-11-16 09:30:00'
        ];

        // Example $orders array structure
        $orders = [
            [
                'id' => 1023,
                'order_type' => 'Injunction',
                'description' => 'Defendant prohibited from contacting plaintiff',
                'order_date' => '2023-11-10',
                'file_reference' => 'DOC-4567'
            ],
            // ... more orders
        ];?>
        <div class="card-header bg-light">
            <h2 class="h5 mb-0">Case Outcomes & Orders</h2>
        </div>
        <div class="card-body">
            <!-- Current Status -->
            <div class="mb-4 p-3 border rounded">
                <h3 class="h6 fw-bold">Current Status</h3>
                <div class="d-flex align-items-center">
                    <span class="badge bg-success me-2">Active</span>
                    <span>Last updated: <?= date('M j, Y', strtotime(date("Y-m-d"))) ?></span>
                </div>
            </div>

            <!-- Final Judgment -->
            <div class="mb-4 p-3 border rounded">
                <h3 class="h6 fw-bold">Final Judgment</h3>
                <p><?= htmlspecialchars($outcome['judgment_summary'] ?? 'No judgment entered') ?></p>
                <div class="text-muted small">
                    <i class="fas fa-calendar-alt me-1"></i>
                    <?= isset($outcome['judgment_date']) ? date('F j, Y', strtotime($outcome['judgment_date'])) : 'Pending' ?>
                </div>
            </div>

            <!-- Court Orders -->
            <div class="mb-4">
                <h3 class="h6 fw-bold mb-3">Court Orders</h3>
                <ul class="list-group">
                    <?php foreach ($orders as $order): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-start py-2">
                            <div class="ms-2 me-auto">
                                <div class="fw-bold"><?= htmlspecialchars($order['order_type']) ?></div>
                                <?= htmlspecialchars($order['description']) ?>
                            </div>
                            <div class="text-end">
                        <span class="badge bg-light text-dark">
                            <?= date('M j, Y', strtotime($order['order_date'])) ?>
                        </span>
                                <a href="<?= site_url('orders/view/'.$order['id']) ?>"
                                   target="_blank"
                                   class="d-block mt-1 text-primary small">
                                    View Details
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                        <li class="list-group-item text-muted">No orders recorded</li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Next Steps -->
            <div class="p-3 bg-light rounded">
                <h3 class="h6 fw-bold">Next Steps</h3>
                <ul class="mb-0">
                    <li>Appeal deadline: <?= isset($outcome['appeal_deadline']) ? date('F j, Y', strtotime($outcome['appeal_deadline'])) : 'N/A' ?></li>
                    <li>Compliance review: <?= $outcome['compliance_due'] ?? 'Pending' ?></li>
                </ul>
            </div>
        </div>
    </div> 
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-blue blue-opacity-bg">
            <i class="fa fa-users" aria-hidden="true"></i>
            <?php echo $this->lang->line("people"); ?>
            <button type="button" class="btn float-right btn-meet-now">
                <img width="15" height="15" src="assets/images/icons/microsoft-teams.svg">&nbsp;&nbsp;
                <a target="_blank" class="tooltip-title" title="<?php echo $this->lang->line("meet_now"); ?>"
                   href="<?php echo "https://teams.microsoft.com/l/entity/0443dea6-e92d-4a36-b8b2-98f9c0b11095/matterID?context={%22subEntityId%22:%20%22" . $legalCase["id"] . "%22}"; ?>">
                    <?php echo $this->lang->line("meet_now"); ?></a>
            </button>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                    <span class="control-label"><?php echo $this->lang->line("provider_group"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow width-50-per">
                    <?php echo form_dropdown("provider_group_id", $Provider_Groups, $legalCase["provider_group_id"], 'id="provider_group_id" class="form-control select-picker" form="legalCaseAddForm" data-live-search="true"');
                    ?>
                    <div data-field="provider_group_id" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                    <span class="control-label"><?php echo $this->lang->line("assignee"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow width-50-per">
                    <?php if (!in_array($legalCase["user_id"], $usersProviderGroup)) {
                        $usersProviderGroup[$legalCase["user_id"]] = $legalCase["Assignee"];
                    }
                    echo form_dropdown("user_id", $usersProviderGroup, $legalCase["user_id"], 'id="userId" class="form-control select-picker"  onChange ="reassignment_reason()"  form="legalCaseAddForm" data-live-search="true"'); ?>
                    <div data-field="user_id" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box" id="">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                    <span class="control-label"><?php echo $this->lang->line("reassignment_reason"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow width-50-per">
                    <?php $data = array( 'name' => 'reason_for_reassignment', 'id' => 'reason_for_reassignment', 'placeholder' => 'Give Reason for re-assigning the case', 'class' => 'form-control' );
                    echo form_textarea($data);                  ?>
                    <div data-field="reason_for_reassignment" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                    <span class="control-label"><?php echo $this->lang->line("referred_by"); ?></span>
                    <a href="<?php echo site_url("contacts/edit/" . $legalCase["referredBy"]); ?>" id="referredByLinkId"
                       class="icon-alignment <?php echo $legalCase["referredBy"] ? "" : "d-none"; ?>"><i
                                class="fa fa-external-link"></i></a>
                </label>
                <div class="no-padding float-right flex-grow">
                    <?php
                    echo form_input(["name" => "referredBy", "id" => "referredBy", "type" => "hidden", "form" => "legalCaseAddForm"], $legalCase["referredBy"]);
                    echo form_input(["name" => "referredByName", "id" => "referredByLookup", "class" => "lookup form-control", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing"), "onblur" => "if (this.value === '') { jQuery('#referredByLinkId').addClass('d-none'); }", "form" => "legalCaseAddForm"], $legalCase["referredByName"]);
                    ?>
                    <div data-field="referredBy" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding min-height-35 flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label text-with-hint tooltip-title"
                              title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_requested_by_helper") : $this->lang->line("matter_requested_by_helper"); ?>">
                            <?php echo $this->lang->line("requested_by"); ?>
                        </span>
                    <a href="<?php echo site_url("contacts/edit/" . $legalCase["requestedBy"]); ?>"
                       id="requestedByLinkId" target="_blank"
                       class="icon-alignment  <?php echo $legalCase["requestedBy"] ? "" : "d-none"; ?> "><i
                                class="fa fa-external-link"></i></a>
                </label>
                <div class="no-padding float-right flex-grow">
                    <?php
                    echo form_input(["name" => "requestedBy", "id" => "requested-by", "type" => "hidden", "form" => "legalCaseAddForm"], $legalCase["requestedBy"]);
                    echo form_input(["name" => "requestedByName", "id" => "lookup-requested-by", "class" => "form-control lookup", "placeholder" => $this->lang->line("start_typing"), "title" => $this->lang->line("start_typing"), "onblur" => "if (this.value === '') { jQuery('#requestedByLinkId').addClass('d-none'); }", "form" => "legalCaseAddForm"], $legalCase["requestedByName"]);
                    ?>
                    <div data-field="requestedBy" class="inline-error d-none"></div>
                </div>
            </div>
            <?php if (!empty($custom_fields["people"])) {
                $this->load->view("custom_fields/form_custom_field_template_people_right", ["custom_fields" => $custom_fields["people"]]);
            } ?>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-purple blue-opacity-bg">
            <i class="fa fa-fw fa-lock" aria-hidden="true"></i><?php echo $this->lang->line("privacy"); ?>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20 scroll case-privacy-container"
             id="case-privacy-container">
            <label class="control-label no-padding-right col-md-12 no-padding padding-top-10 min-width-80 no-margin-bottom">
                <span class="control-label"><?php echo $this->lang->line("shared_with"); ?></span>
                <div class="float-right">
                    <a class="btn btn-default btn-link pull-left <?php echo $legalCase["private"] == "yes" ? "d-none" : ""; ?>"
                       id="privateLink" href="javascript:;"
                       onClick="setAsPrivate(jQuery('#case-privacy-container', '#edit-legal-case-container'), 'cases', '', '', '', true);"><?php echo $this->lang->line("set_as_private"); ?></a>
                    <a class="btn btn-default btn-link pull-left <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>"
                       id="publicLink" href="javascript:;"
                       onClick="setAsPublic(jQuery('#case-privacy-container', '#edit-legal-case-container'), 'cases', true);"><?php echo $this->lang->line("set_as_public"); ?></a>
                    <?php echo form_input(["id" => "private", "name" => "private", "value" => $legalCase["private"], "type" => "hidden"], "", 'form="legalCaseAddForm"'); ?>
                </div>
            </label>
            <div class="col-md-12 no-padding">
                <label class="shared-with-label padding-top7  <?php echo $legalCase["private"] == "yes" ? "d-none" : ""; ?>">
                    <?php echo $this->lang->line("everyone"); ?>
                </label>
                <div class="lookup-box-container margin-bottom input-group col-md-12 no-padding  <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?> users-lookup-container form-group">
                    <?php
                    echo form_input(["id" => "lookup-case-users", "name" => "lookupCaseUsers", "class" => "form-control users-lookup", "title" => $this->lang->line("start_typing"), "form" => "legalCaseAddForm"], "");
                    ?>
                    <span class="input-group-addon bs-caret users-lookup-icon" id="shared-with-users"
                          onclick="jQuery('#lookup-case-users', '#legal-matter-dialog').focus();"><span
                                class="caret"></span></span>
                </div>
                <div id="selected-watchers"
                     class="no-margin <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>">
                    <?php if ($legalCasewatchersUsers) {
                        foreach ($legalCasewatchersUsers as $id => $name) {?>
                            <div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px"
                                 id="watchers<?php echo $id; ?>">
                                <span id="<?php echo $id; ?>"><?php echo isset($legalCasewatchersUsersStatus) && $legalCasewatchersUsersStatus[$id] == "Inactive" ? $name . " (" . $this->lang->line("Inactive") . ")" : $name; ?></span>
                                <?php echo form_input(["value" => $id, "name" => "Legal_Case_Watchers_Users[]", "type" => "hidden", "form" => "legalCaseAddForm"]);
                                ?>
                                <a href="javascript:;"
                                   class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item"
                                   tabindex="-1"
                                   onClick="removeBoxElement(jQuery(this.parentNode),'#selected-watchers','watcher-lookup-container','#ticket-modifiable-fields');">
                                    <i class="fa-solid fa-xmark"></i>
                                </a>
                            </div>
                        <?php }
                    } else { ?>
                    <div class="flex-item-box row multi-option-selected-items no-margin w-100 height-30px" id="watchers<?php echo $id; ?>">
                        <span id="<?php echo $this->session->userdata("AUTH_user_id"); ?>"><?php echo $this->session->userdata("AUTH_userProfileName"); ?></span>
                        <?php echo form_input(["value" => $this->session->userdata("AUTH_user_id"), "name" => "Legal_Case_Watchers_Users[]", "type" => "hidden", "form" => "legalCaseAddForm"]); ?>
                        <a href="javascript:;" class="btn btn-default btn-sm btn-link pull-right remove-button flex-end-item" tabindex="-1" onClick="removeBoxElement(jQuery(this.parentNode),'#selected-watchers','watcher-lookup-container','#ticket-modifiable-fields');"><i class="fa-solid fa-xmark"></i></a>
                        </div><?php }
                    ?>
                </div>
                <div data-field="case_watchers" class="inline-error d-none padding-5"></div>
                <div class="col-md-12 no-padding autocomplete-helper  <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>">
                    <div class="inline-text"><?php echo $this->lang->line("helper_autocomplete_privacy"); ?></div>
                </div>
                <div class="mt-5 alert alert-info col-md-12 alert-message <?php echo $legalCase["private"] == "yes" ? "" : "d-none"; ?>"
                     role="alert">
                    <span><?php echo sprintf($this->lang->line("case_watchers_info"), $legalCase["category"] == "Litigation" ? $this->lang->line("litigation") : $this->lang->line("matter")); ?></span>
                </div>
            </div>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-15 background-border-design-green blue-opacity-bg">
            <i class="fa fa-calendar no-border"
               aria-hidden="true"></i><?php echo $this->lang->line("date_and_times"); ?>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20" id="date-pikers-case-right-boxes"
             style="position: relative">
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label required-label line-height-20 text-with-hint tooltip-title"
                              title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_arrival_date_helper") : $this->lang->line("matter_arrival_date_helper"); ?>">
                            <?php echo $this->lang->line("arrival_date"); ?>
                        </span>
                </label>
                <div class="no-padding float-right flex-grow" id="arrival-date-container">
                    <div class="input-group date no-padding date-picker"
                         id="case-arrival-date"><?php echo form_input(["name" => "caseArrivalDate_Hidden", "id" => "caseArrivalDate_Hidden", "type" => "hidden"], $legalCase["caseArrivalDate"], 'form="legalCaseAddForm"');
                        echo form_input(["name" => "caseArrivalDate", "id" => "caseArrivalDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#caseArrivalDate_Hidden').val(''); }"], $legalCase["caseArrivalDate"], 'form="legalCaseAddForm"');
                        ?>
                        <span class="input-group-addon"><i
                                    class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="caseArrivalDate" class="inline-error d-none"></div>
                </div>
                <?php
                if ($systemPreferences["hijriCalendarConverter"]) { ?>
                    <div class="visualize-hijri-date no-margin-left no-margin-right">
                        <a href="javascript:;" id="date-conversion"
                           onclick="HijriConverter(jQuery('#caseArrivalDate', '#casesDetails'), true);"
                           title="<?php echo $this->lang->line("hijri_date_converter"); ?>"
                           class="btn btn-link float-right no-padding">
                            <?php echo $this->lang->line("hijri"); ?>
                        </a>
                    </div>
                    <?php
                } ?>
            </div>
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 trim-width-33-per text-with-hint tooltip-title"
                       style="<?php echo $this->is_auth->is_layout_rtl() ? "min-width: 110px" : "min-width: 103px"; ?>"
                       title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_filed_on_helper") : $this->lang->line("matter_filed_on_helper"); ?>">
                    <span class="control-label line-height-20"><?php echo $this->lang->line("filed_on"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow" id="filed-on-date-container">
                    <div class="input-group date no-padding date-picker" id="arrival-date">
                        <?php
                        echo form_input(["name" => "arrivalDate", "id" => "arrivalDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#arrivalDate_Hidden').val(''); }"], $legalCase["arrivalDate"], 'form="legalCaseAddForm"');
                        echo form_input(["name" => "arrivalDate_Hidden", "id" => "arrivalDate_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], $legalCase["arrivalDate"], 'form="legalCaseAddForm"');
                        ?>
                        <span class="input-group-addon"><i
                                    class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="arrivalDate" class="inline-error d-none"></div>
                </div>
                <?php if ($systemPreferences["hijriCalendarConverter"]) { ?>
                    <div class="visualize-hijri-date no-margin-left no-margin-right">
                    <a href="javascript:;" id="date-conversion"
                       onclick="HijriConverter(jQuery('#arrivalDate', '#casesDetails'), true);"
                       title="<?php echo $this->lang->line("hijri_date_converter"); ?>"
                       class="btn btn-link float-right no-padding">
                        <?php echo $this->lang->line("hijri"); ?>
                    </a>
                    </div><?php
                } ?>
            </div>
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label line-height-20 text-with-hint tooltip-title"
                              title="<?php echo $legalCase["category"] == "Litigation" ? $this->lang->line("litigation_due_date_helper") : $this->lang->line("matter_due_date_helper"); ?>">
                            <?php echo $this->lang->line("due_date"); ?>
                        </span>
                </label>
                <div class="no-padding float-right flex-grow" id="due-date-container">
                    <div class="input-group date no-padding date-picker" id="due-date">
                        <?php echo form_input(["name" => "dueDate", "id" => "dueDate", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "data-validation-engine" => "validate[custom[date]]", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#dueDate_Hidden').val(''); }"], $legalCase["dueDate"], 'form="legalCaseAddForm"');
                        echo form_input(["name" => "dueDate_Hidden", "id" => "dueDate_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], $legalCase["dueDate"], 'form="legalCaseAddForm"');
                        ?>
                        <span class="input-group-addon"><i
                                    class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="dueDate" class="inline-error d-none"></div>
                    <div class="float-right padding-right-15 <?php echo !$notify_before && !$legalCase["dueDate"] ? "d-none" : ""; ?> "
                         id="notify-me-before-link">
                        <a href="javascript:;"
                           onclick="notifyMeBefore(jQuery('#edit-legal-case-container'));"><?php echo $this->lang->line("notify_me_before"); ?></a>
                    </div>
                </div><?php if ($systemPreferences["hijriCalendarConverter"]) { ?>
                    <div class="visualize-hijri-date no-margin-left no-margin-right">
                        <a href="javascript:;" id="date-conversion"
                           onclick="HijriConverter(jQuery('#dueDate', '#casesDetails'), true);"
                           title="<?php echo $this->lang->line("hijri_date_converter"); ?>"
                           class="btn btn-link float-right no-padding">
                            <?php echo $this->lang->line("hijri"); ?>
                        </a>
                    </div>
                <?php } ?>
            </div>
            <div class="form-group no-padding  d-none" id="notify-me-before-container">
                <div class="flex-item-box form-group no-padding min-height-35">
                    <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label line-height-20"><?php echo $this->lang->line("notify_me_before"); ?></span>
                    </label>
                    <div class="no-padding float-right flex-grow" id="notify-me-before">
                        <?php
                        echo form_input(["name" => "notify_me_before[id]", "form" => "legalCaseAddForm", "value" => $notify_before["id"] ??"", "disabled" => true, "type" => "hidden"]);
                        echo form_input(["name" => "notify_me_before[time]", "form" => "legalCaseAddForm", "class" => "form-control", "value" => $notify_before["time"] ?? $systemPreferences["reminderIntervalDate"], "id" => "notify-me-before-time", "disabled" => true, "data-validation-engine" => "validate[required]"]);
                        echo form_dropdown("notify_me_before[time_type]", $notify_me_before_time_types, $notify_before["time_type"]??"", "class='form-control select-picker' id='notify-me-before-time-type' form='legalCaseAddForm' disabled data-validation-engine='validate[required]'");
                        ?>
                    </div>
                </div>
                <div class="flex-item-box form-group no-padding min-height-35">
                    <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label line-height-20"><?php echo $this->lang->line("reminder_by"); ?></span>
                    </label>
                    <div class="flex-grow">
                        <?php echo form_dropdown("notify_me_before[type]", $notify_me_before_types, $notify_before["type"]??"", "class='form-control select-picker' id='notify-me-before-type' form='legalCaseAddForm' disabled data-validation-engine='validate[required]'");
                        ?>
                    </div>
                    <div>
                        <a href="javascript:;" onclick="hideRemindMeBefore(jQuery('#edit-legal-case-container'));"
                           class="btn btn-link no-padding">
                            <i class="icon-alignment fa fa-trash light_red-color"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100  trim-width-33-per">
                        <span class="control-label line-height-20">
                            <?php echo $this->lang->line("closed_on"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow" id="closed-on-container">
                    <div class="input-group date no-padding date-picker" id="closed-on">
                        <?php echo form_input(["name" => "closedOn_Hidden", "id" => "closedOn_Hidden", "type" => "hidden", "form" => "legalCaseAddForm"], $legalCase["closedOn"], 'form="legalCaseAddForm"');
                        echo form_input(["name" => "closedOn", "id" => "closedOn", "form" => "legalCaseAddForm", "placeholder" => "YYYY-MM-DD", "data-validation-engine" => "validate[custom[date]]", "autocomplete" => "off", "class" => "form-control", "onblur" => "if (this.value === '') { jQuery('#closedOn_Hidden').val(''); }"], $legalCase["closedOn"], 'form="legalCaseAddForm"');
                        ?>
                        <span class="input-group-addon"><i
                                    class="fa fa-calendar purple_color p-9 cursor-pointer-click no-border no-border-left m-0"></i></span>
                    </div>
                    <div data-field="closedOn" class="inline-error d-none"></div>
                </div>
                <?php
                if ($systemPreferences["hijriCalendarConverter"]) { ?>
                    <div class="visualize-hijri-date no-margin-left no-margin-right">
                        <a href="javascript:;" id="date-conversion"
                           onclick="HijriConverter(jQuery('#closedOn', '#casesDetails'), true);"
                           title="<?php echo $this->lang->line("hijri_date_converter"); ?>"
                           class="btn btn-link float-right no-padding">
                            <?php echo $this->lang->line("hijri"); ?></a>
                    </div>
                <?php  } ?>
            </div>
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-120 trim-width-33-per">
                            <span class="control-label line-height-20 text-with-hint tooltip-title"
                                  title="<?php echo $this->lang->line("supported_time_units"); ?>">
                                <?php echo $this->lang->line("estimated_effort"); ?></span>
                </label>
                <div class="no-padding float-right flex-grow">
                    <?php echo form_input(["name" => "estimatedEffort", "form" => "legalCaseAddForm", "id" => "estimatedEffortHour", "data-validation-engine" => "validate[funcCall[validateHumanReadableTime]]", "class" => "no-margin form-control", "onblur" => "convertMinsToHrsMins(jQuery(this));"], $this->timemask->timeToHumanReadable($legalCase["estimatedEffort"]), 'form="legalCaseAddForm"');
                    ?>
                    <div data-field="estimatedEffort" class="inline-error d-none"></div>
                </div>
            </div>
            <div class="form-group no-padding  flex-item-box">
                <label class="control-label no-padding-right no-padding padding-top-10 min-width-100 trim-width-33-per">
                        <span class="control-label line-height-20">
                            <?php echo $this->lang->line("effectiveEffort"); ?>
                        </span>
                    <a href="<?php echo site_url("cases/time_logs/" . $legalCase["id"]); ?>"
                       class="icon-alignment btn btn-default btn-link"><i class="fa fa-external-link"></i></a>
                </label>
                <div class="no-padding float-right flex-grow">
                        <span class="control-label line-height-20 float-right padding-15 bold-font-family">
                            <?php echo $legalCase["effectiveEffort"] ? $this->timemask->timeToHumanReadable($legalCase["effectiveEffort"]) : 0; ?></span>
                </div>
            </div>
            <?php if (!empty($custom_fields["date"])) {
                $this->load->view("custom_fields/form_custom_field_template_date_right", ["custom_fields" => $custom_fields["date"]]);
            } ?>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-red blue-opacity-bg flex-row">
            <i class="fa fa-building mx-1" aria-hidden="true"></i>
            <span class="mx-1"><?php echo $this->lang->line("related_companies"); ?></span>
            <div class="float-right cursor-pointer-click flex-end-item"
                 onclick="addContactPopup('company','addCompany', 'cases/edit/','<?php echo $this->lang->line("add_company"); ?>','<?php echo $this->lang->line("company_name"); ?>','company_id','companyType')">
                <i class="fa fa-plus no-padding" aria-hidden="true"></i>
                <?php echo $this->lang->line("add_new"); ?>
            </div>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div id="related-case-companies-container"></div>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-gold blue-opacity-bg flex-row">
            <i class="fa  fa-exclamation-triangle mx-1" aria-hidden="true"></i>
            <span class="mx-1"><?php echo $this->lang->line("related_risks"); ?></span>
            <div class="float-right cursor-pointer-click flex-end-item" onclick="addMatterRisk()">
                <i class="fa fa-plus no-padding" aria-hidden="true"></i>
                <?php echo $this->lang->line("add_new"); ?>
            </div>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div id="related-case-risks-container">
                <span class="loader-submit loading"></span>
            </div>
        </div>
    </div>
    <div class="right-box-container">
        <h2 class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-gold blue-opacity-bg flex-row">
            <i class="fa fa-users mx-1" aria-hidden="true"></i>
            <span class="mx-1"><?php echo $this->lang->line("related_contacts"); ?></span>
            <div class="float-right cursor-pointer-click flex-end-item"
                 onclick="addContactPopup('contact','addContact', 'cases/edit/', '<?php echo $this->lang->line("add_contact"); ?>','<?php echo $this->lang->line("contact_name"); ?>','contact_id','contactType')">
                <i class="fa fa-plus no-padding" aria-hidden="true"></i>
                <?php echo $this->lang->line("add_new"); ?>
            </div>
        </h2>
        <div class="right-box-body box-shadow_container padding-10 mb-20">
            <div id="related-case-contacts-container"></div>
        </div>
    </div>
    <div class="right-box-container"
         onclick="collapse('audit-section-heading', 'audit-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true);">
        <h2 id="audit-section-heading"
            class="right-box-header box-title box-shadow_container no-margin padding-10 background-border-design-purple blue-opacity-bg">
            <i class="audit-icon"></i>
            <a href="javascript:;" class="toggle-title float-right"> <i
                        class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a>
            <?php echo $this->lang->line("audit"); ?>
        </h2>
        <div id="audit-section-content"
             class="d-none right-box-body box-shadow_container padding-10 mb-20 audit-section-content">
            <?php $this->load->view("cases/audit"); ?>
        </div>
    </div>

</div>

<script>
    ///addmatterrisk form load from case/related_risks controller as modal popup
    function addMatterRisk(riskId = null) {
        jQuery.ajax({
            url: getBaseURL() + "cases/related_risks",
            type: "GET",
            data: {riskId: riskId,case_id: "<?php echo $legalCase['id']; ?>"},
            beforeSend: function () {
                showLoader(true);
            },
            success: function (response) {
                if(response.success){
                    jQuery('#addCaseRiskModal').remove();
                    jQuery('body').append(response.html);
                    jQuery('#addCaseRiskModal').modal('show');

                    // Attach save handler after modal is shown
                    jQuery(document).off('click', '#saveLegalCaseRiskBtn').on('click', '#saveLegalCaseRiskBtn', function(e) {
                        e.preventDefault();
                        save_legal_case_risk();
                    });
                } else {
                    pinesMessageV2({ ty: 'error', m: response.message });
                }
            },
            complete: function () {
                showLoader(false);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function save_legal_case_risk() {
        var formData = new FormData(document.getElementById(jQuery("form","#addCaseRiskModal").attr('id')));

        jQuery.ajax({
            url: getBaseURL() + "cases/related_risks",
            type: "POST",
            data: formData,
            dataType: "json",
            data: formData,
            contentType: false, // required to be disabled
            cache: false,
            processData: false,
            beforeSend: function () {
                showLoader(true);
            },
            success: function (response) {
                if(response.success){
                    jQuery('#addCaseRiskModal').modal('hide');
                    pinesMessageV2({ ty: 'success', m: response.message });
                    load_related_risks(); // Reload risks after save
                } else {
                    pinesMessageV2({ ty: 'error', m: response.message });
                }
            },
            complete: function () {
                showLoader(false);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }

    function load_related_risks() {
        jQuery.ajax({
            url: getBaseURL() + "cases/get_related_risks",
            type: "GET",
            data: {case_id: "<?php echo $legalCase['id']; ?>"},
            dataType: "json",
            beforeSend: function () {
                showLoader(true);
            },
            success: function (response) {
                if (response.success && response.html) {
                    jQuery("#related-case-risks-container").html(response.html);
                } else {
                    jQuery("#related-case-risks-container").html('<div class="alert alert-danger">Failed to load risks.</div>');
                }
            },
            complete: function () {
                showLoader(false);
            },
            error: defaultAjaxJSONErrorsHandler
        });
    }
    //implement ajax call to delete risk

    function deleteRelatedRisk(riskId) {
        if (confirm("Are you sure you want to delete this")) {
            jQuery.ajax({
                url: getBaseURL() + "cases/delete_related_risk",
                type: "POST",
                data: {riskId: riskId},
                dataType: "json",
                beforeSend: function () {
                    showLoader(true);
                },
                success: function (response) {
                    if (response.success) {
                        pinesMessageV2({ ty: 'success', m: response.message });
                        load_related_risks(); // Reload risks after deletion
                    } else {
                        pinesMessageV2({ ty: 'error', m: response.message });
                    }
                },
                complete: function () {
                    showLoader(false);
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }
    }

    // Initial load of related risks on page ready
    jQuery(function() {
        load_related_risks();
    });
</script>