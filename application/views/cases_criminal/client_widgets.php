<?php
$system_preferences = $this->session->userdata("systemPreferences");
$disable_widget = true;
$popover_content = false;
if (!$legalCase["client_id"] && (!isset($system_preferences["caseValueCurrency"]) || !$system_preferences["caseValueCurrency"])) {
    $popover_content = sprintf($this->lang->line("client_case_not_set"), "clientCaseForm('" . $id . "')") . " " . sprintf($this->lang->line("case_currency_not_set"), "system_preferences");
} else {
    if (!$legalCase["client_id"]) {
        $popover_content = sprintf($this->lang->line("client_case_not_set"), "clientCaseForm('" . $id . "')");
    } else {
        if (!isset($system_preferences["caseValueCurrency"]) || !$system_preferences["caseValueCurrency"]) {
            $popover_content = sprintf($this->lang->line("case_currency_not_set"), "system_preferences");
        } else {
            $disable_widget = false;
        }
    }
}
if ($legalCase["cap_amount_enable"] == "1") {
    $column_widget = "col-sm-5ths col-md-5ths ";
} else {
    $column_widget = "col-sm-3 col-md-3 ";
}
?>
<div id="client-account-status" class="col-md-12 vertical-padding-10">
    <?php echo form_input(["id" => "system-client-currency", "value" => $system_preferences["caseValueCurrency"], "type" => "hidden"]);?>
    <div class="<?php echo $column_widget; ?> widget-columns no-padding padding-right-10 pl-15">
        <div id="trust-container" class="widget-data box-shadow_container widget-container-blue">
            <span class="circle-title-blue"><img width="25" class="tooltipTable" src="assets/images/icons/money-2x.png" alt=""></span>
            <span class="white-title"><?php echo $this->lang->line("trust"); ?></span>
            <span class="details white-title pull-right">
                <?php if (!$disable_widget) {?> <span class="loader-submit loading"></span>
                <?php } else {?>
                    <i class="help-sign fa-solid fa-circle-question"></i>
                <?php }?>
            </span>
            <div class="popover-content d-none">
                <span class="warning-msg <?php echo $disable_widget ? "" : "d-none";?>"><?php echo $popover_content; ?></span>
                <span class="help-msg <?php echo $disable_widget ? "d-none" : "";?>"><?php echo $this->lang->line("popover_client_trust_status");?></span>
            </div>
        </div>
    </div>
    <div class="<?php echo $column_widget;?> widget-columns no-padding padding-right-10 pl-15">
        <div id="paid-container" class="widget-data box-shadow_container widget-container-green">
            <span class="circle-title-green"><img width="25" class="tooltipTable" src="assets/images/icons/money-currency-2x.png" alt=""></span>
            <span class="white-title"><?php echo $this->lang->line("paid");?></span>
            <span class="details white-title pull-right">
                <?php if (!$disable_widget) {?>
                    <span class="loader-submit loading"></span>
                <?php } else {?>  <i class="help-sign fa-solid fa-circle-question"></i>
                <?php }?>
            </span>
            <div class="popover-content d-none">
                <span class="warning-msg <?php echo $disable_widget ? "" : "d-none";?>">
                    <?php echo $popover_content; ?></span>
                <span class="help-msg <?php echo $disable_widget ? "d-none" : "";?>"><?php echo $this->lang->line("popover_client_paid_status");?></span>
            </div>
        </div>
    </div>
    <div class="<?php echo $column_widget;?> widget-columns no-padding padding-right-10 pl-15">
        <div id="balance-due-container" class="widget-data box-shadow_container widget-container-red">
            <span class="circle-title-red"><img width="25" class="tooltipTable" src="assets/images/icons/donation-2x.png" alt=""></span>
            <span class="white-title"><?php echo $this->lang->line("due"); ?></span>
            <span class="details white-title pull-right">        <?php if (!$disable_widget) {
                ?>
                    <span class="loader-submit loading"></span>
                <?php } else {?>
                    <i class="help-sign fa-solid fa-circle-question"></i>
                <?php }?>
            </span>
            <div class="popover-content d-none">
                <span class="warning-msg <?php echo $disable_widget ? "" : "d-none";?> ">
                    <?php echo $popover_content; ?></span>
                <span class="help-msg <?php echo $disable_widget ? "d-none" : "";?>">
                    <?php echo $this->lang->line("popover_client_due_status"); ?></span>
            </div>
        </div>
    </div>
    <div class="<?php echo $column_widget; ?> widget-columns no-padding padding-right-10 pl-15">
        <div id="billable-container" class="box-shadow_container widget-container-gold widget-data">
            <div class="circle-title-gold d-inline-block"><img width="25" class="tooltipTable" src="assets/images/icons/outline-2x.png" alt=""></div>
            <div class="white-title d-inline-block"><?php echo $this->lang->line("billable"); ?></div>
            <label class="details white-title pull-right"><?php if (!$disable_widget) {?>
                    <span class="loader-submit loading"></span>

                                                                                             <?php } else {?>
                    <i class="help-sign fa-solid fa-circle-question"></i>
                <?php }?>
            </label>
            <div class="popover-content d-none">
                <span class="warning-msg <?php echo $disable_widget ? "" : "d-none";?>"><?php echo $popover_content;?></span>
                <span class="help-msg <?php echo $disable_widget ? "d-none" : "";?>"><?php echo $this->lang->line("popover_client_billable_status");?></span>
                <table class="table table-bordered table-condensed d-none" id="details-table">
                    <tbody>
                    <tr>
                        <td>
                            <b><?php echo $this->lang->line("expenses");?></b>
                        </td>
                        <td class="text-right" id="expenses-amount"></td>
                    </tr>
                    <tr>
                        <td>
                            <b><?php echo $this->lang->line("time_logs"); ?></b>
                        </td>
                        <td class="text-right" id="logs-amount"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div id="cap-amount-container" class="<?php echo $column_widget; ?> widget-columns no-padding padding-right-15 pl-15 <?php echo $legalCase["cap_amount_enable"] == "1" ? "" : " d-none";?>" onmouseenter="if(caseSettings){ caseSettings.hoverUpdateCapWidget()};">
        <div id="capping-container" class="widget-data box-shadow_container widget-container-purple">
            <span class="circle-title-red"><img width="25" class="tooltipTable" src="assets/images/icons/limit-2x.png" alt=""></span>
            <span class="white-title"><?php echo $this->lang->line("cap"); ?></span>
            <span class="details white-title pull-right"> <?php if (!$disable_widget) {?>
                    <span class="loader-submit loading"></span>
                <?php }
                else { ?>
                    <i class="help-sign fa-solid fa-circle-question"></i>
                <?php }?>
            </span>
            <div class="popover-content d-none">
                <span class="warning-msg <?php echo $disable_widget ? "" : "d-none";?>"><?php echo $popover_content; ?></span>
                <span class="help-msg <?php echo $disable_widget ? "d-none" : "";?>"><?php echo sprintf($this->lang->line("popover_capping_status"));?></span>
                <table class="table table-bordered table-condensed d-none" id="details-table">
                    <tbody>
                    <tr>
                        <td>
                            <b><?php echo $this->lang->line("remaining_cap_amount"); ?></b>
                        </td>
                        <td class="text-right" id="remaining-cap-amount"></td>
                    </tr>
                    <tr>
                        <td>
                            <b id="expenses-cap-ratio-percentage"><?php echo sprintf($this->lang->line("expenses_cap_ratio_percentage"), $legalCase["expenses_cap_ratio"]) . "%";?></b>
                        </td>
                        <td class="text-right" id="expenses-amount"></td>
                    </tr>
                    <tr>
                        <td>
                            <b id="time-logs-cap-ratio-percentage"><?php echo sprintf($this->lang->line("time_logs_cap_ratio_percentage"), $legalCase["time_logs_cap_ratio"]) . "%";?></b>
                        </td>
                        <td class="text-right" id="logs-amount"></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    caseSettings = typeof caseSettings !=="undefined" ?  caseSettings : false;
</script>