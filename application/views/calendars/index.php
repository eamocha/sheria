<?php
// PHP logic   stay at the top if preferred, defining variables needed in the HTML
if (isset($users[$logged_user_id])) {
    $logged_user_name = $users[$logged_user_id];
    unset($users[$logged_user_id]);
} else {
    $logged_user_name = null;
}
?>

<div class="container-fluid no-padding scheduler-container">
    <div class="row no-padding">
        <div class="col-sm-3 col-md-2 col-xs-2 no-margin">
            <?php echo form_open("", "name=\"filters\" class=\"form-inline\""); ?>
            <?php echo form_input(["value" => $this->session->userdata("AUTH_user_id"), "id" => "user-auth", "type" => "hidden"]); ?>
            <br />
            <div id="calendar-integrations-button-container"><br />
                <a id="calendar-integrations-button" href="javascript:;" onClick="integrationPopup();" class="btn btn-default">
                    <i class="fa fa-calendar p-0 no-border" style="padding: 0 !important;"></i>&nbsp;<?php echo $this->lang->line("sync_your_calendar"); ?>
                </a>
            </div>
            <div id="calendar-attendees-container" class="col-md-12 no-padding">
                <br />
                <div><p><?php echo $this->lang->line("show_events_for_users"); ?></p></div>
                <div id="attendees">
                    <?php if ($logged_user_name): ?>
                        <p id="user_<?php echo $logged_user_id; ?>">
                            <?php echo form_input(["class" => "color-palette", "type" => "hidden"]); ?>
                            <span title="<?php echo $logged_user_name; ?>">
                                <?php echo form_checkbox("users[]", $logged_user_id, false, "class=\"inline calendar-attendees-checkboxes\""); ?>
                                <?php echo 26 < strlen($logged_user_name) ? " " . substr($logged_user_name, 0, 26) . "..." : " " . $logged_user_name; ?>
                            </span>
                        </p>
                    <?php endif; ?>
                    <?php foreach ($users as $user_id => $name): ?>
                        <p id="user_<?php echo $user_id; ?>">
                            <?php echo form_input(["class" => "color-palette", "type" => "hidden"]); ?>
                            <span title="<?php echo $name; ?>">
                                <?php echo form_checkbox("users[]", $user_id, false, "class=\"inline calendar-attendees-checkboxes\""); ?>
                                <?php echo 26 < strlen($name) ? " " . mb_substr($name, 0, 26) . "..." : " " . $name; ?>
                            </span>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
        <div id="scheduler_here" class="dhx_cal_container col-sm-9 col-md-10 col-xs-10">
            <div class="dhx_cal_navline">
                <div class="dhx_cal_prev_button">&nbsp;</div>
                <div class="dhx_cal_next_button">&nbsp;</div>
                <div class="dhx_cal_today_button"></div>
                <div class="dhx_cal_date"></div>
                <div class="dhx_cal_tab day-tab" name="day_tab" onclick="alert('')"></div>
                <div class="dhx_cal_tab week-tab" name="week_tab"></div>
                <div class="dhx_cal_tab month-tab" name="month_tab"></div>
                <div class="dhx_cal_tab year-tab" name="year_tab" ></div>
                <div id="refresh_dhx_scheduler" class="dhx_cal_custom_button"><i class="purple_color fa-solid fa-arrows-rotate"></i>&nbsp;<?php echo $this->lang->line("refresh"); ?></div>
            </div>
            <div class="dhx_cal_header">
            </div>
            <div class="dhx_cal_data">
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    var calendarSettings = '<?php echo $calendar_settings; ?>';
    var authIdLoggedIn = '<?php echo $this->is_auth->get_user_id(); ?>';
    var selectedDate = '<?php echo $selected_date; ?>';
    var mode = '<?php echo isset($user_calendar_settings["view"]) ? $user_calendar_settings["view"] : "week"; ?>';
    var showCalendarIntegrationPopup = '<?php echo $show_calendar_integration_popup; ?>';
    var calendarIntegrationEnabled = '<?php echo $calendar_integration_enabled; ?>';
</script>
