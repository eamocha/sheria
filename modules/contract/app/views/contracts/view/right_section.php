<div class="contract-dates side-box-wrapper">
    <div onclick="collapse('date-section-heading', 'date-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true)">
        <h4 role="button" id="date-section-heading">
            <?php echo $this->lang->line("contract_dates_section"); ?>
            <a href="javascript:;" class="float-right"><i class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a>
        </h4>
    </div>
    <div class="d-none" id="date-section-content">
        <ul>
            <hr class="hr-separator">
            <li><img src="assets/images/contract/calendar.svg" height="18" width="16" class="filter-color"><b><?php echo $this->lang->line("contract_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["contract_date"]; ?></span></li>
            <li><img src="assets/images/contract/calendar.svg" height="18" width="16" class="filter-color"><b><?php echo $this->lang->line("effective_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("effective_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["effective_date"]; ?></span></li>
            <li><img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contract_start_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_start_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["start_date"]; ?></span></li>
            <li><img src="assets/images/contract/clock.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contract_duration"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_duration_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["contract_duration"]; ?></span></li>
            <li><img src="assets/images/contract/calendar.svg" height="18" width="16" class="filter-color"><b><?php echo $this->lang->line("expected_completion_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("expected_completion_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["expected_completion_date"]; ?></span></li>
            <li><img src="assets/images/contract/icon.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("actual_completion_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("actual_completion_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["actual_completion_date"]; ?></span></li>
            <li><img src="assets/images/contract/icon.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("expiry_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("end_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["end_date"]; ?></span></li>
            <li><img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("perf_security_commencement_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("perf_security_commencement_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["perf_security_commencement_date"]; ?></span></li>
            <li><img src="assets/images/contract/racing-flag.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("perf_security_expiry_date"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("perf_security_expiry_date_helper"); ?>"></i>&nbsp</b><span><?php echo $contract["perf_security_expiry_date"]; ?></span></li>
            <li><img src="assets/images/contract/renewal.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contract_renewal"); ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $this->lang->line("contract_renewal_helper"); ?>"></i>&nbsp</b><span><?php echo $this->lang->line($contract["renewal"]); ?></span></li>
            <li><img src="assets/images/contract/created_on.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contract_created_on"); ?>:&nbsp</b><span><?php echo $contract["createdOn"]; ?></span></li>
            <li><img src="assets/images/contract/modified_on.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contract_modified_on"); ?>:&nbsp</b><span><?php echo $contract["modifiedOn"]; ?></span></li>
            <?php if (!empty($custom_fields["date"])) {
                foreach ($custom_fields["date"] as $field) { ?>
                    <li><img src="assets/images/contract/calendar.svg" height="18" width="18" class="filter-color"><b><?php echo $field["customName"]; ?> &nbsp;<i class="fa-solid fa-circle-question tooltip-title text-with-hint" title="<?php echo $field["customName"] . '_helper'; ?>"></i>&nbsp</b><span><?php echo $field["date_value"] ? $field["date_value"] . ($field["type"] === "date_time" && $field["time_value"] ? " - " . $field["time_value"] : "") : $this->lang->line("none"); ?></span></li>
                <?php }
            } ?>
        </ul>
    </div>
</div>
<div class="mt-4 side-box-wrapper">
    <div onclick="collapse('notifications-section-heading', 'notifications-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true)">
        <h4 role="button" id="notifications-section-heading"><?php echo $this->lang->line("notifications"); ?> <a href="javascript:;" class="float-right"><i class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a></h4>
    </div>
    <div class="d-none" id="notifications-section-content">
        <ul>
            <hr class="hr-separator">
            <li><img src="assets/images/contract/notification.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("notify_me_before"); ?>:&nbsp</b>
                <span><?php echo $notify_before["time"] ?? ""; echo $notify_before["time_type"] ?? ""; ?></span>
            </li>
            <li id="emails"><img src="assets/images/contract/notification.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("notify_users"); ?>:&nbsp</b>
                <span class="emails-images">
                    <?php $emails = explode(";", $notifications["emails"]);
                    if (empty($emails)) {
                        echo $this->lang->line("none");
                    } else {
                        foreach ($emails as $index => $email) {
                            if ($index < 1) {
                                echo $email;
                                unset($emails[$index]);
                            }
                        }
                    }
                    if (count($emails)) { ?>
                        <a href="javascript:;" id="emails-link"><span style="text-decoration: underline;margin-left: 15PX;"><?php echo sprintf($this->lang->line("plus_more"), count($emails)); ?></span></a>
                    </span><?php } ?>
                <div class="popover-content d-none">
                    <div class="emails-list">
                        <ul class="no-list-style">
                            <?php foreach ($emails as $email) { ?>
                                <li><span><?php echo $email; ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </li>
            <li id="assigned-teams"><img src="assets/images/contract/notification.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("notify_teams"); ?>:&nbsp</b>
                <div class="assigned-teams-images">
                    <?php if (!empty($notifications["teams"])) {
                        foreach ($notifications["teams"] as $index => $team) {
                            if ($index < 1) {
                                echo $team["name"];
                                unset($notifications["teams"][$index]);
                            }
                        }
                    }
                    if (count($notifications["teams"])) { ?>
                    <a href="javascript:;" id="assigned-teams-link"><span style="text-decoration: underline;margin-left: 15PX;"><?php echo sprintf($this->lang->line("plus_more"), count($notifications["teams"])); ?></span></a>
                </div>
                <?php } ?>
                <div class="popover-content d-none">
                    <div class="assigned-teams-list">
                        <ul class="no-list-style">
                            <?php foreach ($notifications["teams"] as $team) { ?>
                                <li><span><?php echo $team["name"]; ?></span></li>
                            <?php } ?>
                        </ul>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</div>
<div class="mt-4 side-box-wrapper">
    <div onclick="collapse('people-section-heading', 'people-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true)">
        <h4 role="button" id="people-section-heading"><?php echo $this->lang->line("people"); ?>
            <button type="button" class="btn no-margin btn-meet-now">
                <img class="no-margin" width="15" height="15" src="assets/images/icons/microsoft-teams.svg">&nbsp;&nbsp;<a target="_blank" href="<?php echo "https://teams.microsoft.com/l/entity/0443dea6-e92d-4a36-b8b2-98f9c0b11095/contractID?context={%22subEntityId%22:%20%22" . $contract["id"] . "%22}"; ?>">
                    <?php echo $this->lang->line("meet_now"); ?></a>
            </button>
            <a href="javascript:;" class="float-right"><i class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a>
        </h4>
    </div>
    <div class="d-none" id="people-section-content">
        <ul>
            <hr class="hr-separator">
            <li><img src="assets/images/contract/document.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("reference_number"); ?>:&nbsp</b><span><?php echo $contract["reference_number"]; ?></span></li>
            <li><img src="assets/images/contract/group.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("requester"); ?>:&nbsp</b><span><?php echo $contract["requester"]; ?></span></li>
            <li><img src="assets/images/contract/group.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("provider_group"); ?>:&nbsp</b><span><?php echo $contract["assigned_team"]; ?></span></li>
            <li><img src="assets/images/contract/assignee.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("assignee"); ?>:&nbsp</b><span><?php echo $contract["assignee"]; ?></span></li>
            <li><img src="assets/images/contract/createdby.svg" height="18" width="18" class="filter-color mr-2"><b><?php echo $this->lang->line("created_by"); ?>:&nbsp</b><span><?php echo htmlentities($contract["creator"]); ?></span></li>
            <li><img src="assets/images/contract/modifiedby.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("modified_by"); ?>:&nbsp</b><span><?php echo htmlentities($contract["modifier"]); ?></span></li>
            <li><img src="assets/images/contract/default-user.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("authorized_signatory"); ?>:&nbsp</b><span><?php echo htmlentities($contract["authorized_signatory_name"]); ?></span></li>
            <li id="contributors"><img src="assets/images/contract/contributors.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("contributors"); ?>:&nbsp</b><span class="contributors-images">
                <?php if (empty($contributors)) {
                    echo $this->lang->line("none");
                } else {
                    foreach ($contributors as $index => $user) {
                        if ($index <= 1) { ?>
                            <img class="tooltip-custom" data-tooltip-content="#tooltip_content" title="<?php echo $user["status"] == "Inactive" ? htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($user["name"]); ?>" src="<?php echo "users/get_profile_picture/" . $user["id"] . "/1"; ?>" style=" ">
                            <?php unset($contributors[$index]);
                        }
                    }
                    if (count($contributors)) { ?>
                        <a href="javascript:;" id="contributors-link"><span style="text-decoration: underline;margin-left: 15PX;"><?php echo sprintf($this->lang->line("plus_more"), count($contributors)); ?></span></a>
                    <?php }
                } ?>
                    <div class="popover-content d-none">
                        <div class="contributors-list">
                            <ul class="no-list-style">
                                <?php foreach ($contributors as $user) { ?>
                                    <li><span class="user-hover"><img class="img-circle" width="30" src="<?php echo "users/get_profile_picture/" . $user["id"] . "/1"; ?>">
                                        <span><?php echo $user["status"] == "Inactive" ? htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($user["name"]); ?></span>
                                    </span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </span>
            </li>
            <li id="collaborators"><img src="assets/images/contract/contributors.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("collaborators"); ?>:&nbsp</b>
                <span class="collaborators-images">
                    <?php if (empty($collaborators)) {
                        echo $this->lang->line("none");
                    } else {
                        foreach ($collaborators as $index => $user) {
                            if ($index < 1) {
                                echo $user["status"] == "Inactive" ? htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($user["name"]);
                                unset($collaborators[$index]);
                            }
                        }
                        if (count($collaborators)) { ?>
                            <a href="javascript:;" id="collaborators-link"><span style="text-decoration: underline;margin-left: 15PX;"><?php echo sprintf($this->lang->line("plus_more"), count($collaborators)); ?></span></a>
                        <?php }
                    } ?>
                    <div class="popover-content d-none">
                        <div class="watchers-list">
                            <ul class="no-list-style">
                                <?php foreach ($collaborators as $user) { ?>
                                    <li><span><?php echo $user["status"] == "Inactive" ? htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($user["name"]); ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </span>
            </li>
            <?php if (!empty($custom_fields["people"])) {
                foreach ($custom_fields["people"] as $field) { ?>
                    <li><img src="assets/images/contract/<?php echo $field["type_data"]; ?>.svg" height="18" width="18" class="filter-color"><b><?php echo $field["customName"]; ?>:&nbsp</b>
                        <span><?php echo $field["value"] ? implode(",", $field["value"]) : $this->lang->line("none"); ?></span>
                    </li>
                <?php }
            } ?>
        </ul>
    </div>
</div>
<div class="mt-4 side-box-wrapper">
    <div onclick="collapse('privacy-section-heading', 'privacy-section-content', false, 'fast', 'fa-solid fa-angle-down', 'fa-solid fa-angle-right', true)">
        <h4 role="button" id="privacy-section-heading"><?php echo $this->lang->line("privacy"); ?><a href="javascript:;" class="float-right"><i class="fa fa-angle-right icon font-18 purple_color">&nbsp;</i></a></h4>
    </div>
    <div class="d-none" id="privacy-section-content">
        <ul>
            <hr class="hr-separator">
            <li id="watchers"><img src="assets/images/contract/watchers.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("privacy"); ?>:&nbsp</b><span><?php if (empty($watchers)) {
                        echo $this->lang->line("no_privacy");
                    } else { ?><a href="javascript:;" id="watchers-link"><?php echo count($watchers); ?> <?php echo $this->lang->line("users"); ?></a><?php } ?>
                    <div class="popover-content d-none">
                        <div class="watchers-list">
                            <ul class="no-list-style">
                                <?php foreach ($watchers as $watcher) { ?>
                                    <li><span><?php echo $watcher["status"] == "Inactive" ? htmlentities($watcher["name"]) . "(" . $this->lang->line("Inactive") . ")" : htmlentities($watcher["name"]); ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </span>
            </li>
            <li id="contract-watchers" class="contract-watchers <?php echo $visible_to_cp == 1 ? "" : "d-none"; ?>"><img src="assets/images/contract/contract_watchers.svg" height="18" width="18" class="filter-color"><b><?php echo $this->lang->line("watchers"); ?>:&nbsp</b>
                <span class="contract-watchers-images">
                    <?php if (empty($contract_watchers)) {
                        echo $this->lang->line("none");
                    } else {
                        echo htmlentities($contract_watchers[0]["name"]);
                    }
                    unset($contract_watchers[0]);
                    if (count($contract_watchers)) { ?>
                        <a href="javascript:;" id="contract-watchers-link"><span style="text-decoration: underline;margin-left: 15PX;"><?php echo sprintf($this->lang->line("plus_more"), count($contract_watchers)); ?></span></a>
                    <?php } ?>
                    <div class="popover-content d-none">
                        <div class="contract-watchers-list" name="contract_watchers">
                            <ul class="no-list-style">
                                <?php foreach ($contract_watchers as $user) { ?>
                                    <li><span><?php echo htmlentities($user["name"]); ?></span></li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </span>
                <span title="<?php echo $this->lang->line("watchers_helper"); ?>" class="tooltip-title float-right"><i class="fa-solid fa-circle-question"></i></span>
            </li>
        </ul>
    </div>
</div>