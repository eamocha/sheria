<?php
$google_integration_enabled = !empty($integration_settings["calendar"]["enabled"]) && $integration_settings["calendar"]["provider"] == "google";
$ms_integration_enabled = !empty($integration_settings["calendar"]["enabled"]) && $integration_settings["calendar"]["provider"] == "ms_cloud";

?>

<div id="integration-list" class="integration-list">
    <h4><?php echo $this->lang->line("integration_container_title"); ?></h4>
    <p><?php echo $this->lang->line("integration_container_desctiption"); ?></p>
    <div class="integration-list-item">
        <div class="integration-list-item-content">
            <img width="32" height="32" src="assets/images/google.png" alt="Google Logo">
            <ul>
                <li>
                    <h4><?php echo $this->lang->line("google_calendar"); ?></h4>
                </li>
                <?php if ($google_integration_enabled): ?>
                    <li id="google-selected-calendar">
                        <span><?php echo sprintf($this->lang->line("connected_calendar"), $integration_settings["calendar"]["calendar_name"]); ?></span>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="https://docs.sheria360.com/pages/viewpage.action?pageId=17177836" target="_blank"><?php echo $this->lang->line("learn_more"); ?></a>
                </li>
            </ul>
        </div>
        <div class="integration-list-item-button">
            <?php echo form_input(["value" => $google_integration_enabled ? "yes" : "no", "id" => "google-integration", "type" => "hidden"]); ?>
            <input id="google-integration-toggle" type="checkbox" data-toggle="toggle" data-integration-type="google" data-integration-status-holder="google-integration" data-integration-calendar-label="google-selected-calendar" <?php echo $google_integration_enabled ? "checked=\"checked\"" : ""; ?>>
        </div>
    </div>
    <div class="integration-list-item">
        <div class="integration-list-item-content">
            <img width="32" height="32" src="assets/images/office_365.png" alt="Office 365 Logo">
            <ul>
                <li>
                    <h4><?php echo $this->lang->line("office_365_calendar"); ?></h4>
                </li>
                <?php if ($ms_integration_enabled): ?>
                    <li id="ms-cloud-selected-calendar">
                        <span><?php echo sprintf($this->lang->line("connected_calendar"), $integration_settings["calendar"]["calendar_name"]); ?></span>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="https://docs.sheria360.com/pages/viewpage.action?pageId=17993718" target="_blank"><?php echo $this->lang->line("learn_more"); ?></a>
                </li>
            </ul>
        </div>
        <div class="integration-list-item-button">
            <?php echo form_input(["value" => $ms_integration_enabled ? "yes" : "no", "id" => "ms-cloud-integration", "type" => "hidden"]); ?>
            <input id="ms-cloud-integration-toggle" type="checkbox" data-toggle="toggle" data-integration-type="ms_cloud" data-integration-status-holder="ms-cloud-integration" data-integration-calendar-label="ms-cloud-selected-calendar" <?php echo $ms_integration_enabled ? "checked=\"checked\"" : ""; ?>>
        </div>
    </div>
</div>
<script>
    jQuery(document).ready(function () {
        jQuery("[id$='-integration-toggle']").bootstrapToggle().change(function () {
            var integrationToggle = jQuery(this);
            var integrationType = jQuery(this).attr('data-integration-type');
            var integrationStatusHolder = jQuery('#' + jQuery(this).attr('data-integration-status-holder'));
            if (integrationToggle.prop('checked')) {
                if (integrationStatusHolder.val() == 'no') {
                    jQuery("#loader-global").show();
                    enableCalendarIntegration(integrationType, integrationToggle);
                }
            } else {
                if (jQuery(integrationStatusHolder).val() == 'yes') {
                    var integrationCalendarLabel = jQuery('#' + jQuery(this).attr('data-integration-calendar-label'));
                    confirmationDialog('confirm_disable_' + integrationType + '_calendar_sync', {resultHandler: disableCalendarIntegration, parm: {integrationCalendarLabel: integrationCalendarLabel, integrationStatusHolder: integrationStatusHolder}, onCloseHandler: resetIntegrationToggle, onCloseParm: {status: 'on', integrationToggle: integrationToggle}});
                }
            }
        });
    });
</script>
