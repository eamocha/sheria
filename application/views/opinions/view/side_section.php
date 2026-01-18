<div id="people-module" class="module toggle-wrap">
    <div id="people-module-heading" class="mod-header" onclick="collapse('people-module-heading', 'people-module-body')">
        <a href="javascript:;" class="toggle-title">
            <i class="fa-solid fa-angle-down icon">&nbsp;</i>
        </a>
        <h4 class="toggle-title"><?php echo $this->lang->line("people"); ?></h4>
    </div>
    <div class="mod-content" id="people-module-body">
        <ul class="section-details" id="people-details">
            <li class="people-details">
                <dl>
                    <dt><?php echo $this->lang->line("assigned_to"); ?>:</dt>
                    <dd>
                        <span id="assignee-val">
                            <span class="aui-avatar-inner">
                                <img class="img-circle" width="30" height="30"
                                     src="<?php echo "users/get_profile_picture/" . $opinion_data["assigned_to"] . "/1"; ?>">
                            </span>
                            <?php
                            echo $opinion_data["assignee_status"] == "Inactive" ?
                                htmlentities($opinion_data["assignee_fullname"]) . "(" . $this->lang->line("Inactive") . ")" :
                                htmlentities($opinion_data["assignee_fullname"]);
                            ?>
                        </span>
                    </dd>
                </dl>
                <dl>
                    <dt><?php echo $this->lang->line("requested_by"); ?>:</dt>
                    <dd>
                        <span id="reporter-val" class="view-issue-field">
                            <span class="aui-avatar-inner">
                                <img class="img-circle" width="30" height="30"
                                     src="<?php echo "users/get_profile_picture/" . $opinion_data["reporter"] . "/1"; ?>">
                            </span>
                            <?php
                            echo $opinion_data["reporter_status"] == "Inactive" ?
                                htmlentities($opinion_data["reporter_fullname"]) . "(" . $this->lang->line("Inactive") . ")" :
                                htmlentities($opinion_data["reporter_fullname"]);
                            ?>
                        </span>
                    </dd>
                </dl>
            </li>
        </ul>
        <ul class="section-details" id="contributors">
            <li>
                <dl>
                    <dt><?php echo $this->lang->line("contributors"); ?>:</dt>
                    <dd>
                        <?php
                        if (empty($contributors)) {
                            echo $this->lang->line("none");
                        } else {
                            echo "<a href=\"javascript:;\" id=\"contributors-link\">";
                            echo count($contributors) . " " . $this->lang->line("users") . "</a>";
                        }
                        ?>
                        <div class="popover-content d-none">
                            <div class="contributors-list">
                                <ul class="no-list-style">
                                    <?php foreach ($contributors as $user): ?>
                                        <li>
                                            <span class="user-hover">
                                                <img class="img-circle" width="30" height="30"
                                                     src="<?php echo "users/get_profile_picture/" . $user["id"] . "/1"; ?>">
                                                <span>
                                                    <?php
                                                    echo $user["status"] == "Inactive" ?
                                                        htmlentities($user["name"]) . "(" . $this->lang->line("Inactive") . ")" :
                                                        htmlentities($user["name"]);
                                                    ?>
                                                </span>
                                            </span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </dd>
                </dl>
            </li>
        </ul>
        <ul class="section-details" id="watchers">
            <li>
                <dl>
                    <dt><?php echo $this->lang->line("shared_with"); ?>:</dt>
                    <dd>
                        <?php
                        if (!$opinion_data["private"] || $opinion_data["private"] == "no") {
                            echo $this->lang->line("public");
                        } else {
                            if (!empty($watchers)) {
                                echo "<a href=\"javascript:;\" id=\"watchers-link\">";
                                echo count($watchers) . " " . $this->lang->line("users") . "</a>";
                            }
                        }
                        ?>
                        <div class="popover-content d-none">
                            <div class="watchers-list">
                                <ul class="no-list-style">
                                    <?php if (isset($watchers)): ?>
                                        <?php foreach ($watchers as $watcher): ?>
                                            <li>
                                                <span class="user-hover">
                                                    <img class="img-circle" width="30" height="30"
                                                         src="<?php echo "users/get_profile_picture/" . $watcher["id"] . "/1"; ?>">
                                                    <span>
                                                        <?php
                                                        echo $watcher["status"] == "Inactive" ?
                                                            htmlentities($watcher["name"]) . "(" . $this->lang->line("Inactive") . ")" :
                                                            htmlentities($watcher["name"]);
                                                        ?>
                                                    </span>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </dd>
                </dl>
            </li>
        </ul>
        <ul class="section-details" id="users">
            <li>
                <dl>
                    <dt><?php echo $this->lang->line("created_by"); ?>:</dt>
                    <dd>
                        <img class="img-circle" width="30" height="30"
                             src="<?php echo "users/get_profile_picture/" . $opinion_data["createdById"] . "/1"; ?>">
                        <?php echo htmlentities($opinion_data["createdBy"]); ?>
                    </dd>
                </dl>
            </li>
            <li>
                <dl>
                    <dt><?php echo $this->lang->line("modified_by"); ?>:</dt>
                    <dd>
                        <img id="modified-by-image" class="img-circle" width="30" height="30"
                             src="<?php echo "users/get_profile_picture/" . $opinion_data["modifiedBy"] . "/1"; ?>">
                        <span id="modified-by"><?php echo htmlentities($opinion_data["modifiedByName"]); ?></span>
                    </dd>
                </dl>
            </li>
        </ul>
        <div id="custom-fields-people-module" class="module">
            <?php $this->load->view("opinions/view/custom_fields/people_section"); ?>
        </div>
    </div>
</div>
<div id="dates-module" class="module toggle-wrap" onclick="collapse('dates-module-heading', 'dates-module-body');">
    <div id="dates-module-heading" class="mod-header">
        <a href="javascript:;" class="toggle-title"> <i class="fa-solid fa-angle-down icon">&nbsp;</i></a>
        <h4 class="toggle-title"><?php echo $this->lang->line("dates"); ?></h4>
    </div>
    <div class="mod-content" id="dates-module-body">
        <ul class="section-details">
            <li>
                <dl class="dates">
                    <dt><?php echo $this->lang->line("due_date"); ?>:</dt>
                    <dd class="date">
                        <span data-name="Due" id="due-date">
                            <?php echo $opinion_data["due_date"]; ?>
                        </span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl class="dates">
                    <dt><?php echo $this->lang->line("created_on"); ?>:</dt>
                    <dd class="date">
                        <span data-name="Created_on" id="due-date">
                            <?php echo date("Y-m-d", strtotime($opinion_data["createdOn"])); ?>
                        </span>
                    </dd>
                </dl>
            </li>
            <li>
                <dl class="dates">
                    <dt><?php echo $this->lang->line("modified_on"); ?>:</dt>
                    <dd class="date">
                        <span data-name="Modified_on" id="due-date">
                            <p id="modified-on"><?php echo date("Y-m-d", strtotime($opinion_data["modifiedOn"])); ?></p>
                        </span>
                    </dd>
                </dl>
            </li>
        </ul>
        <div id="custom-fields-dates-module" class="module">
            <?php $this->load->view("opinions/view/custom_fields/dates_section"); ?>
        </div>
    </div>
</div>
<div>
    <div class="d-flex align-items-center">
        <h4><?php echo $this->lang->line("time_tracking"); ?></h4>

        <a href="javascript:;"
           class="margin-left10"
           onclick="logActivityDialog(false,
               {
               'legalCaseLookupId': '<?php echo $opinion_data["legal_case_id"] != NULL ? $opinion_data["legal_case_id"] : ""; ?>',
               'legalCaseLookup': '<?php echo $opinion_data["legal_case_id"] != NULL ? $opinion_data["legal_case_id"] : ""; ?>',
               'legalCaseSubject': '<?php echo $opinion_data["caseSubject"] != NULL ? trim(preg_replace("/\s+/", " ", $opinion_data["caseSubject"])) : ""; ?>',
               'legalCaseCategory': '<?php echo $opinion_data["caseCategory"] != NULL ? $opinion_data["caseCategory"] : ""; ?>',
               'opinion_id': '<?php echo $opinion_data["OpinionId"] != NULL ? "T" . $opinion_data["OpinionId"] : ""; ?>',
               'opinionSubject': '<?php echo $opinion_data["title"] ?? ''; ?>',
               'opinionId': '<?php echo $opinion_data["OpinionId"] ?? ''; ?>',
               'modifiedOn': '<?php echo $opinion_data["modifiedOn"] ?? ''; ?>',
               'opinionStatus': '<?php echo $opinion_data["status"] ?? ''; ?>',
               'createdByName': '<?php echo $opinion_data["createdBy"] ?? ''; ?>',
               'createdOn': '<?php echo $opinion_data["createdOn"] ?? ''; ?>',
               'modifiedByName': '<?php echo $opinion_data["modifiedByName"] ?? ''; ?>',
               'opinion_legal_case': '<?php echo $opinion_data["opinion_legal_case"] ?? ''; ?>',
               'clientId': '<?php echo $opinion_data["clientId"] ?? ''; ?>',
               'clientName': '<?php echo $opinion_data["clientName"] ?? ''; ?>'
               },
               '',
               'opinionLog',
               true);">

        </a>
        <a href="javascript:;"
           onclick="TimerForm(null,'add',null,
               {
               'opinionId': '<?php echo $opinion_data["OpinionId"] ?? ''; ?>',
               'opinionSubject':  '<?php echo $opinion_data["title"] ?? ''; ?>'
               });">
            <img width="22" height="18" src="assets/images/icons/start-timer-black.svg">
        </a>
    </div>
    <div id="time-tracking-module" class="module toggle-wrap" onclick="collapse('time-tracking-module-heading', 'time-tracking-module-body');">
        <div id="time-tracking-module-heading" class="mod-header">
            <a href="javascript:;" class="toggle-title">
                <i class="fa-solid fa-angle-down icon">&nbsp;</i>
            </a>
        </div>
        <div class="mod-content" id="time-tracking-module-body">
            <div id="tt-single-table-info" class="tt-inner section-details">
                <dl>
                    <dt class="tt-text">
                        <?php echo $this->lang->line("estimated"); ?>:
                    </dt>
                    <dd class="tt-graph-container">
                        <table cellspacing="0" cellpadding="0" class="tt-graph">
                            <tbody>
                            <tr class="tt-graph">
                                <td style="width:<?php echo $time_tracking["estimated_width"]; ?>%;"
                                    class="estimated-<?php echo $time_tracking["estimated_status"]; ?>">
                                    <img src="compressed_asset/images/icons/spacer.gif">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </dd>
                    <dd class="tt-values">
                        <?php
                        echo $opinion_data["estimated_effort"] ?
                            $this->timemask->timeToHumanReadable($opinion_data["estimated_effort"]) :
                            $this->lang->line("not_specified");
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt class="tt-text">
                        <?php echo $this->lang->line("remaining"); ?>:
                    </dt>
                    <dd class="tt-graph-container">
                        <table id="tt-graph-estimated-eff" cellspacing="0" cellpadding="0" class="tt-graph">
                            <tbody>
                            <tr class="tt-graph">
                                <td style="width:<?php echo $time_tracking["logged_width"]; ?>%;"
                                    class="remaining-<?php echo $time_tracking["logged_width"] ? "inactive" : $time_tracking["remaining_status"]; ?>">
                                    <img src="compressed_asset/images/icons/spacer.gif">
                                </td>
                                <td style="width:<?php echo $time_tracking["remaining_width"]; ?>%;"
                                    class="remaining-<?php echo $time_tracking["remaining_status"]; ?>">
                                    <img src="compressed_asset/images/icons/spacer.gif">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </dd>
                    <dd class="tt-values">
                        <?php
                        echo $opinion_data["estimated_effort"] && $opinion_data["effectiveEffort"] ?
                            $this->timemask->timeToHumanReadable($opinion_data["estimated_effort"] - $opinion_data["effectiveEffort"]) :
                            $this->lang->line("not_specified");
                        ?>
                    </dd>
                </dl>
                <dl>
                    <dt class="tt-text">
                        <?php echo $this->lang->line("logged"); ?>:
                    </dt>
                    <dd class="tt-graph-container">
                        <table cellspacing="0" cellpadding="0" class="tt-graph">
                            <tbody>
                            <tr class="tt-graph">
                                <td style="width:<?php echo $time_tracking["logged_width"]; ?>%;"
                                    class="logged-<?php echo $time_tracking["logged_status"]; ?>">
                                    <img src="compressed_asset/images/icons/spacer.gif">
                                </td>
                                <td style="width:<?php echo $time_tracking["remaining_width"]; ?>%;"
                                    class="logged-<?php echo $time_tracking["remaining_width"] ? "inactive" : $time_tracking["logged_status"]; ?>">
                                    <img src="compressed_asset/images/icons/spacer.gif">
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </dd>
                    <dd id="tt-values-effective-eff" class="tt-values">
                        <?php
                        echo $opinion_data["effectiveEffort"] ?
                            $this->timemask->timeToHumanReadable($opinion_data["effectiveEffort"]) :
                            $this->lang->line("not_specified");
                        ?>
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>