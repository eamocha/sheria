<?php
$this->load->view("partial/header");
$this->load->view("partial/tabs_subnav", $tabsNLogs);
$id = $expense["id"];
$approved = false;
?>

<style>
    .input-append,
    .input-prepend {
        font-size: 12px;
        white-space: normal;
    }
</style>

<div class="container-fluid py-3">
    <div class="row">
        <?php
        $paid_through_permission = isset($paid_through_permission_flag) && !$paid_through_permission_flag;
        if (empty($expense["id"])) {
            echo "<div class='col-md-12 form-group'>";
            echo "<h4>".$this->lang->line("record_new_expense")."</h4>";
            echo "</div>";
        } else {
            $approved = $expense["status"] == "approved" ? true : false;
        }
        ?>

        <?php echo form_open("", "novalidate class='form-horizontal col-md-12' id='expensesForm' enctype='multipart/form-data'"); ?>

        <!-- Hidden Fields -->
        <?php echo form_input(["name" => "voucher_header_id", "id" => "voucher_header_id", "value" => $expense["id"], "type" => "hidden"]); ?>
        <?php echo form_input(["name" => "organization_id", "id" => "organization_id", "value" => $this->session->userdata("organizationID"), "type" => "hidden"]); ?>
        <?php echo form_input(["name" => "case_id", "id" => "caseLookupId", "value" => isset($expense["case_id"]) ? $expense["case_id"] : "", "type" => "hidden"]); ?>
        <?php echo form_input(["name" => "expense_status", "id" => "expense-status", "value" => isset($expense["status"]) && !empty($expense["id"]) ? $expense["status"] : "", "type" => "hidden"]); ?>
        <?php echo form_input(["name" => "vendor_id", "id" => "vendor_id", "value" => $expense["vendor_id"], "type" => "hidden"]); ?>
        <?php echo form_input(["id" => "isCasePreset", "value" => $isCasePreset, "type" => "hidden"]); ?>
        <?php echo form_input(["name" => "referrer", "id" => "referrer", "value" => $referrer, "type" => "hidden"]); ?>

        <div class="col-md-12 row">
            <!-- Left Column -->
            <div class="col-6">
                <!-- Expense Category -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right required"><?php echo $this->lang->line("expense_category"); ?></label>
                    <div class="col-md-7">
                        <?php echo form_input(["id" => "expense_account", "name" => "expense_account", "type" => "hidden"]); ?>
                        <select name="expense_category_id" id="expense_category_id" required="required" data-validation-engine="validate[required]" onchange="setExpenseCategory();" class="form-control">
                            <option value=""><?php echo $this->lang->line("choose_one"); ?></option>
                            <?php if (!empty($expense_categories)) {
                                foreach ($expense_categories as $expenses_category) { ?>
                                    <option <?php echo $expense["expense_category_id"] == $expenses_category["id"] ? "selected" : ""; ?>
                                            value="<?php echo $expenses_category["id"]; ?>"
                                            expense_account="<?php echo $expenses_category["account_id"]; ?>"
                                            amount="<?php echo $expenses_category["amount"]; ?>">
                                        <?php echo $expenses_category["name"]; ?>
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="form-group row">
                    <label class="control-label col-md-3 required"><?php echo $this->lang->line("payment_method"); ?></label>
                    <div class="col-md-7">
                        <?php
                        $paymentMethodAttrs = $paid_through_permission ?
                            "disabled='disabled' id='paymentMethod' required='required' data-validation-engine='validate[required]' onchange='changeAccountsValues(this.value);' class='form-control'" :
                            "id='paymentMethod' required='required' data-validation-engine='validate[required]' onchange='changeAccountsValues(this.value);' class='form-control'";
                        echo form_dropdown("paymentMethod", $paymentMethod, isset($expense["paymentMethod"]) && !empty($expense["paymentMethod"]) ? $expense["paymentMethod"] : "Cash", $paymentMethodAttrs);
                        ?>
                    </div>
                </div>

                <!-- Paid Through -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right required"><?php echo $this->lang->line("paid_through"); ?></label>
                    <div class="col-md-7">
                        <?php if ($paid_through_permission) { ?>
                            <?php echo form_input(["type" => "hidden", "name" => "paid_through", "value" => $expense["paid_through"]]); ?>
                            <select name="paid_through_dropdown" id="paid_through" required="required" data-validation-engine="validate[required]" onchange="setCurrencyCode();setAccountBalance();setExpenseNeedApprovedBalance();" disabled="disabled" class="form-control">
                                <option value="<?php echo $expense["paid_through"]; ?>" currencyCode="<?php echo $paid_through_account_data["currencyCode"]; ?>" currency="<?php echo $paid_through_account_data["currency_id"]; ?>">
                                    <?php echo $paid_through_account_data["name"]; ?>
                                </option>
                            </select>
                        <?php } else { ?>
                            <select name="paid_through" id="paid_through" required="required" data-validation-engine="validate[required]" onchange="setCurrencyCode();setAccountBalance();setExpenseNeedApprovedBalance();" class="form-control">
                                <option value=""><?php echo $this->lang->line("choose_account"); ?></option>
                                <?php if (!empty($paid_through)) {
                                    foreach ($paid_through as $account) { ?>
                                        <option <?php echo $account["id"] == $expense["paid_through"] ? "selected" : ""; ?>
                                                value="<?php echo $account["id"]; ?>"
                                                currencyCode="<?php echo $account["currencyCode"]; ?>"
                                                currency="<?php echo $account["currency_id"]; ?>">
                                            <?php echo $account["full_account_name"]; ?>
                                        </option>
                                    <?php }
                                } ?>
                            </select>
                        <?php } ?>
                    </div>
                    <?php if ($paid_through_permission) { ?>
                        <div style="padding-top: 5px" class="col-md-1">
                            <span tooltipTitle="<?php echo $this->lang->line("paid_through_authorization"); ?>" class="tooltipTable pull-left">
                                <i class="fa-solid fa-circle-question purple_color"></i>
                            </span>
                        </div>
                    <?php } else { ?>
                        <div style="padding-top: 5px">
                            <a href="<?php echo site_url("accounts/add_cash_bank_account/"); ?>" class="btn-link">
                                <?php echo $this->lang->line("add_account"); ?>
                            </a>
                        </div>
                    <?php } ?>
                </div>

                <!-- Amount -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right required"><?php echo $this->lang->line("amount"); ?></label>
                    <div class="col-md-7">
                        <div class="input-group">
                            <span class="input-group-text paidThroughCurrency"></span>
                            <?php echo form_input(["id" => "currencyID", "value" => "", "type" => "hidden"]); ?>
                            <?php echo form_input([
                                "class" => "form-control",
                                "name" => "amount",
                                "id" => "amount",
                                "value" => $expense["amount"],
                                "required" => "required",
                                "autocomplete" => "Off",
                                "data-validation-engine" => "validate[required,funcCall[validateDecimal],funcCall[checkAmountMaxDigitsNum]]"
                            ]); ?>
                        </div>
                    </div>
                </div>

                <!-- Balance (hidden by default) -->
                <div class="form-group row d-none" id="balanceDiv">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("balance"); ?></label>
                    <div class="col-md-7" style="padding-top:8px"><span id="accountBalance"></span></div>
                </div>

                <!-- Expenses Waiting Approval (hidden by default) -->
                <div class="form-group row d-none" id="need-approved-div">
                    <label class="control-label col-md-5 no-padding-right"><?php echo $this->lang->line("total_expenses_waiting_approval"); ?></label>
                    <div class="col-md-7" style="padding-top:8px"><span id="expence-need-approved-balance"></span></div>
                </div>

                <!-- Exchange Rate -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"></label>
                    <div class="col-md-7">
                        <?php echo form_input(["value" => "", "id" => "currencyID", "type" => "hidden"]); ?>
                        <?php echo form_input(["value" => "1.00", "id" => "rate", "name" => "rate", "type" => "hidden"]); ?>
                        <div class="pull-left" dir="ltr">
                            1&nbsp;<span class="paidThroughCurrency"></span>&nbsp;=
                            <div style="display:inline;">
                                <span id="rateText">1.00</span> <?php echo $this->session->userdata("organizationCurrency"); ?>
                            </div>
                        </div>
                        &nbsp;<a href="javascript:;" class="btn-link" onclick="change_exchange_rate();">
                            <?php echo $this->lang->line("edit"); ?>
                        </a>
                    </div>
                </div>

                <!-- Paid On Date -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right required"><?php echo $this->lang->line("paid_on"); ?></label>
                    <div class="col-md-7">
                        <?php echo form_input([
                            "name" => "paidOn",
                            "id" => "paidOn",
                            "placeholder" => "YYYY-MM-DD",
                            "autocomplete" => "off",
                            "value" => !empty($expense["dated"]) ? date("Y-m-d", strtotime($expense["dated"])) : date("Y-m-d", time()),
                            "class" => "date start form-control datepicker",
                            "data-validation-engine" => "validate[required,custom[date]]"
                        ]); ?>
                    </div>
                </div>

                <!-- Tax -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("inclusive_tax"); ?></label>
                    <div class="col-md-7">
                        <select class="form-control" id="tax_id" name="tax_id" onchange="displayTaxAmount();">
                            <option value=""></option>
                            <?php if (!empty($taxes)) {
                                foreach ($taxes as $record) { ?>
                                    <option <?php echo $record["id"] == $expense["tax_id"] ? "selected" : ""; ?>
                                            value="<?php echo $record["id"]; ?>"
                                            percentage="<?php echo $record["percentage"]; ?>">
                                        <?php echo $record["name"]; ?> (<?php echo $record["percentage"]; ?>%)
                                    </option>
                                <?php }
                            } ?>
                        </select>
                    </div>
                </div>

                <!-- Tax Amount (hidden by default) -->
                <div class="form-group row d-none" id="taxAmountContainer">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("tax_amount"); ?></label>
                    <div class="col-md-7" style="padding-top:8px"><span id="taxAmountValue"></span></div>
                </div>

                <!-- Comments -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("comments"); ?></label>
                    <div class="col-md-7">
                        <?php echo form_textarea([
                            "dir" => "auto",
                            "name" => "comments",
                            "id" => "comments",
                            "class" => "form-control",
                            "value" => $expense["description"],
                            "rows" => "2",
                            "cols" => "0"
                        ]); ?>
                    </div>
                </div>
            </div> <!-- End Left Column -->

            <!-- Right Column -->
            <div class="col-6">
                <?php if (!empty($expense["id"])) { ?>
                    <!-- Status Section -->
                    <div class="form-group row">
                        <label class="control-label col-md-3"><?php echo $this->lang->line("status"); ?></label>
                        <div class="col-md-3 input-prepend">
                            <?php
                            $cssClass = "";
                            if ($expense["status"] == "open") {
                                $cssClass = "orange";
                            } elseif ($expense["status"] == "needs_revision") {
                                $cssClass = "red";
                            } elseif ($expense["status"] == "approved") {
                                $cssClass = "darkGreen";
                            } elseif ($expense["status"] == "cancelled") {
                                $cssClass = "purple";
                            }
                            ?>
                            <span id="expense_status" style="width: 100%;" class="input-group-text <?php echo $cssClass; ?>">
                                <?php echo $this->lang->line($expense["status"]); ?>
                            </span>
                        </div>
                        <div class="pull-right col-md-6">
                            <?php if (!in_array($expense["billingStatus"], ["invoiced", "reimbursed"])) { ?>
                                <div class="dropdown more col-md-9">
                                    <a href="" data-toggle="dropdown" class="dropdown-toggle btn btn-default btn-xs">
                                        <i class="icon fas fa-cog fa-xs"></i> <span class="caret no-margin"></span>
                                    </a>
                                    <div class="statuses-options dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dLabel">
                                        <a class="dropdown-item <?php echo $expense["status"] == "approved" ? "d-none" : ""; ?>"
                                           href="javascript:;"
                                           onclick="changeExpenseStatus(<?php echo $expense["id"]; ?>,'approved','reload');">
                                            <?php echo $this->lang->line("approve"); ?>
                                        </a>
                                        <a class="dropdown-item <?php echo $expense["status"] === "open" ? "d-none" : ""; ?>"
                                           href="javascript:;"
                                           onclick="changeExpenseStatus(<?php echo $expense["id"]; ?>,'open','reload');">
                                            <?php echo $this->lang->line("backToOpen"); ?>
                                        </a>
                                        <a class="dropdown-item <?php echo $expense["status"] === "needs_revision" ? "d-none" : ""; ?>"
                                           href="javascript:;"
                                           onclick="changeExpenseStatus(<?php echo $expense["id"]; ?>,'needs_revision','reload');">
                                            <?php echo $this->lang->line("moveToNeedsRevision"); ?>
                                        </a>
                                        <a class="dropdown-item <?php echo $expense["status"] === "cancelled" ? "d-none" : ""; ?>"
                                           href="javascript:;"
                                           onclick="changeExpenseStatus(<?php echo $expense["id"]; ?>,'cancelled','reload');">
                                            <?php echo $this->lang->line("cancel"); ?>
                                        </a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>

                <!-- Reference Number -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("reference_number"); ?></label>
                    <div class="col-md-6">
                        <?php echo form_input([
                            "dir" => "auto",
                            "autocomplete" => "Off",
                            "name" => "referenceNum",
                            "id" => "referenceNum",
                            "class" => "form-control",
                            "value" => $expense["referenceNum"]
                        ]); ?>
                    </div>
                </div>

                <!-- Vendor -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("vendor"); ?></label>
                    <div class="col-md-6">
                        <?php echo form_input([
                            "name" => "vendorName",
                            "id" => "vendorName",
                            "placeholder" => $this->lang->line("search"),
                            "class" => "form-control lookup search",
                            "value" => $expense["vendorName"],
                            "onblur" => "checkLookupValidity(jQuery(this), jQuery('#vendor_id')); checkVendorName(jQuery(this));"
                        ]); ?>
                    </div>
                </div>

                <!-- Tax Number -->
                <div class="form-group row">
                    <label class="control-label col-md-3 o-padding-right">
                        <?php echo $this->lang->line("tax_number"); ?>
                    </label>
                    <div class="col-md-6">
                        <?php echo form_input("tax_number", $expense["tax_number"] ?? "", [
                            "id" => "tax-number",
                            "class" => "form-control disabled cursor-not-allowed input-disabled",
                            "readonly" => true,
                            "disabled" => true,
                            "autocomplete" => "off"
                        ]); ?>
                    </div>
                </div>

                <!-- Related Case -->
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("related_case"); ?></label>
                    <div class="col-md-6">
                        <?php
                        $caselookup_input_attributes = [
                            "id" => "caseLookup",
                            "value" => isset($expense["case_id"]) ? $expense["case_id"] : "",
                            "placeholder" => $this->lang->line("client_or_matter_placeholder"),
                            "title" => $this->lang->line("client_or_matter_placeholder"),
                            "onblur" => "checkLookupValidity(jQuery(this), jQuery('#caseLookupId')); if (this.value === '') { jQuery('#caseLookupId').val(''); jQuery('#caseSubject', this.parentNode).text(''); jQuery('#caseSubjectLinkId').addClass('d-none'); jQuery('#clientName').attr('readonly', false); emptyRelatedData(); }",
                            "class" => "form-control lookup search"
                        ];

                        if ($isCasePreset) {
                            $caselookup_input_attributes["class"] = "form-control";
                            $caselookup_input_attributes["readonly"] = true;
                        }

                        echo form_input($caselookup_input_attributes);
                        ?>
                        <span id="caseSubject" rel="tooltip" class="col-md-7 no-padding" title="">
                            <?php echo character_limiter($expense["case_subject"], 20); ?>
                        </span>
                    </div>
                    <div class="col-md-2 padding-10">
                        <?php if (isset($expense["case_id"])) { ?>
                            <a href="<?php echo $expense["case_category"] == "IP" ? app_url("intellectual_properties/edit/" . $expense["case_id"]) : app_url("cases/edit/" . $expense["case_id"]); ?>"
                               id="caseSubjectLinkId"
                               class="pull-left <?php echo 0 < $expense["case_id"] ? "" : "d-none"; ?>">
                                <?php echo $this->lang->line("goto"); ?>
                            </a>
                        <?php } ?>
                    </div>
                </div>

                <?php if (isset($expense["case_id"])) { ?>
                    <!-- Relate To Section -->
                    <div id="relate-to-container" class="<?php echo $expense["case_id"] ? "" : "d-none"; ?>">
                        <div class="form-group row">
                            <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("relate_to"); ?></label>
                            <div class="col-md-6">
                                <label>
                                    <?php echo form_checkbox("", $expense["related_task"]["id"] ? true : false, $expense["related_task"]["id"] ? true : false, [
                                        "onclick" => "relateTo(this,'task',relateTask)",
                                        "id" => "relate-task"
                                    ]); ?>
                                    &nbsp;&nbsp;<?php echo $this->lang->line("task"); ?>&nbsp;&nbsp;
                                </label>
                                <label class="<?php echo $expense["case_category"] === "Litigation" ? "" : "d-none"; ?>" id="relate-to-hearing">
                                    <?php echo form_checkbox("", $expense["related_hearing"]["id"] ? true : false, $expense["related_hearing"]["id"] ? true : false, [
                                        "onclick" => "relateTo(this,'hearing',relateHearing)",
                                        "id" => "relate-hearing"
                                    ]); ?>
                                    &nbsp;&nbsp;<?php echo $this->lang->line("hearing"); ?>&nbsp;&nbsp;
                                </label>
                                <label class="<?php echo $expense["case_category"] === "Litigation" ? "" : "d-none"; ?>" id="relate-to-event">
                                    <?php echo form_checkbox("", $expense["related_event"]["id"] ? true : false, $expense["related_event"]["id"] ? true : false, [
                                        "onclick" => "relateTo(this,'event',relateEvent)",
                                        "id" => "relate-event"
                                    ]); ?>
                                    &nbsp;&nbsp;<?php echo $this->lang->line("event"); ?>&nbsp;&nbsp;
                                </label>
                            </div>
                        </div>

                        <!-- Related Task -->
                        <div class="form-group row <?php echo $expense["related_task"]["id"] ? "" : "d-none"; ?>" id="related-task">
                            <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("related_task"); ?></label>
                            <div class="col-md-6">
                                <?php echo form_input([
                                    "name" => "task",
                                    "id" => "task-id",
                                    "value" => $expense["related_task"]["id"],
                                    "type" => "hidden"
                                ]); ?>
                                <?php echo form_input([
                                    "id" => "task-lookup",
                                    "value" => $expense["related_task"]["id"],
                                    "placeholder" => $this->lang->line("start_typing"),
                                    "title" => $this->lang->line("start_typing"),
                                    "class" => "form-control lookup search"
                                ]); ?>
                                <span id="task-detail" rel="tooltip" class="col-md-7 no-padding" title="">
                                    <?php echo character_limiter($expense["related_task"]["title"], 20); ?>
                                </span>
                            </div>
                            <div class="col-md-2 padding-10">
                                <?php
                                $task_url = $expense["case_category"] == "Litigation" ? "cases/events/" :
                                    ($expense["case_category"] == "IP" ? "intellectual_properties/tasks/" : "cases/tasks/");
                                ?>
                                <a href="<?php echo app_url($task_url . $expense["case_id"]); ?>"
                                   id="task-link"
                                   class="pull-left <?php echo 0 < $expense["case_id"] ? "" : "d-none"; ?>">
                                    <?php echo $this->lang->line("goto"); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Related Hearing -->
                        <div class="form-group row <?php echo $expense["related_hearing"]["id"] ? "" : "d-none"; ?>" id="related-hearing">
                            <?php echo form_input([
                                "name" => "hearing",
                                "id" => "hearing-id",
                                "value" => $expense["related_hearing"]["id"],
                                "type" => "hidden"
                            ]); ?>
                            <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("related_hearing"); ?></label>
                            <div class="col-md-6">
                                <?php echo form_input([
                                    "id" => "hearing-lookup",
                                    "value" => $expense["related_hearing"]["hearingID"] . ": " . $expense["related_hearing"]["subject"],
                                    "placeholder" => $this->lang->line("start_typing"),
                                    "title" => $this->lang->line("start_typing"),
                                    "class" => "form-control lookup search"
                                ]); ?>
                            </div>
                            <div class="col-md-2 padding-10">
                                <a href="<?php echo app_url("cases/events/" . $expense["case_id"]); ?>"
                                   id="hearing-link"
                                   class="pull-left">
                                    <?php echo $this->lang->line("goto"); ?>
                                </a>
                            </div>
                        </div>

                        <!-- Related Event -->
                        <div class="form-group row <?php echo $expense["related_event"]["id"] ? "" : "d-none"; ?>" id="related-event">
                            <?php echo form_input([
                                "name" => "event",
                                "id" => "event-id",
                                "value" => $expense["related_event"]["id"],
                                "type" => "hidden"
                            ]); ?>
                            <label class="control-label col-md-3 no-padding-right"><?php echo $this->lang->line("related_event"); ?></label>
                            <div class="col-md-6">
                                <?php echo form_input([
                                    "id" => "event-lookup",
                                    "value" => $expense["related_event"]["subject"],
                                    "placeholder" => $this->lang->line("start_typing"),
                                    "title" => $this->lang->line("start_typing"),
                                    "class" => "form-control lookup search"
                                ]); ?>
                            </div>
                            <div class="col-md-2 padding-10">
                                <a href="<?php echo app_url("cases/events/" . $expense["case_id"]); ?>"
                                   id="event-link"
                                   class="pull-left">
                                    <?php echo $this->lang->line("goto"); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php } ?>

                <?php if (!in_array($expense["billingStatus"], ["invoiced", "reimbursed"])) { ?>
                    <!-- Billing Status -->
                    <?php
                    $billing_status = $expense["billingStatus"];
                    $internal_checked = $isCasePreset ? "" : "checked";
                    $not_set_checked = $isCasePreset ? "checked" : "";
                    $to_invoice_checked = "";
                    $non_billable_checked = "";

                    switch ($billing_status) {
                        case "to-invoice":
                            $internal_checked = "";
                            $to_invoice_checked = "checked";
                            $not_set_checked = "";
                            break;
                        case "non-billable":
                            $internal_checked = "";
                            $non_billable_checked = "checked";
                            $not_set_checked = "";
                            break;
                        case "not-set":
                            $internal_checked = "";
                            $non_billable_checked = "";
                            $not_set_checked = "checked";
                            break;
                    }
                    ?>

                    <div class="form-group row">
                        <label class="control-label col-md-3 no-padding-right"></label>
                        <div class="col-md-6 row">
                            <?php echo form_input([
                                "name" => "client_id",
                                "id" => "client_id",
                                "value" => $expense["client_id"],
                                "type" => "hidden"
                            ]); ?>
                            <?php echo form_input([
                                "name" => "client_case_id",
                                "id" => "client_case_id",
                                "value" => isset($case_client["client_id"]) && $case_client["client_id"] ? $case_client["client_id"] : "",
                                "type" => "hidden"
                            ]); ?>
                            <?php echo form_input([
                                "name" => "client_case_name",
                                "id" => "client_case_name",
                                "value" => isset($case_client["clientName"]) && $case_client["clientName"] ? $case_client["clientName"] : "",
                                "type" => "hidden"
                            ]); ?>
                            <?php echo form_input([
                                "id" => "clientAccountIdinitialVal",
                                "value" => $expense["client_account_id"],
                                "type" => "hidden"
                            ]); ?>

                            <div class='col-md-6 no-padding'>
                                <label class="col-md-12 no-padding">
                                    <input type="radio"
                                           onclick="selectBillingStatus(this.value);"
                                           value="internal"
                                           id="billingStatus"
                                           disabled
                                           name="billingStatus"
                                        <?php echo $internal_checked; ?> />
                                    <?php echo $this->lang->line("internal"); ?>
                                </label>
                            </div>

                            <div class="col-md-6 no-padding" id="client_radio_button">
                                <label class="col-md-12 no-padding">
                                    <input type="radio"
                                           onclick="selectBillingStatus(this.value);"
                                           value="not-set"
                                           id="billingStatus"
                                           disabled
                                           name="billingStatus"
                                        <?php echo $not_set_checked; ?> />
                                    <?php echo $this->lang->line("client_money"); ?>
                                </label>
                            </div>

                            <div id="radio_button_icon" class="col-md-5 no-padding d-none">
                                <label>
                                    <a href="javascript:;"><i class="radio-button-icon"></i></a>
                                    <?php echo $this->lang->line("client_money"); ?>
                                </label>
                            </div>

                            <div id="clientDetailsContainer" class="col-md-12 no-padding">
                                <?php
                                $clientname_input_attributes = [
                                    "disabled" => true,
                                    "name" => "clientName",
                                    "id" => "clientName",
                                    "placeholder" => $this->lang->line("start_typing"),
                                    "title" => $this->lang->line("start_typing"),
                                    "class" => "form-control lookup search",
                                    "value" => $expense["clientName"],
                                    "data-validation-engine" => "validate[required]"
                                ];

                                if ($isCasePreset) {
                                    $clientname_input_attributes["readonly"] = true;
                                } else {
                                    $clientname_input_attributes["onblur"] = "checkLookupValidity(jQuery(this), jQuery('#client_id')); checkClientName();";
                                }

                                if (isset($expense["clientName"]) && $expense["clientName"] && isset($case_client["client_id"]) && $case_client["client_id"] && $expense["client_id"] == $case_client["client_id"]) {
                                    $clientname_input_attributes["readonly"] = true;
                                }

                                echo form_input($clientname_input_attributes);
                                ?>

                                <br />
                                <div id="clientBillingDetails" <?php echo $isCasePreset ? "no-margin no-padding row col-md-12 " : "class='d-none no-margin no-padding row col-md-12'"; ?>>
                                    <div class="col-md-6 no-padding" id="client_radio_button">
                                        <label class="col-md-12 no-padding">
                                            <input type="radio"
                                                   onclick="selectBillingStatus(this.value);"
                                                   value="non-billable"
                                                   disabled
                                                   id="billingStatus"
                                                   name="billingStatus"
                                                <?php echo $non_billable_checked; ?> />
                                            <?php echo $this->lang->line("Non-Billable"); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-6 no-padding" id="client_radio_button">
                                        <label class="col-md-12 no-padding">
                                            <input type="radio"
                                                   onclick="selectBillingStatus(this.value);"
                                                   value="to-invoice"
                                                   disabled
                                                   id="billingStatus"
                                                   name="billingStatus"
                                                <?php echo $to_invoice_checked; ?> />
                                            <?php echo $this->lang->line("billable"); ?>
                                        </label>
                                    </div>
                                    <div class="col-md-12 no-padding">
                                        <div id="client_accounts" class="col-md-12 no-padding d-none">
                                            <?php echo $this->lang->line("client_account"); ?>
                                            <select id="account_id" name="account_id" class="form-control" data-validation-engine="validate[required]"></select>
                                        </div>
                                        <a href="javascript:;" id="addAccountId" onclick="addNewClientAccount();" class="btn btn-default btn-link">
                                            <?php echo $this->lang->line("add"); ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div> <!-- End Right Column -->
        </div> <!-- End row -->

        <?php if (empty($expense["id"])) { ?>
            <!-- Document Upload Section (for new expenses only) -->
            <div class="col-md-12 no-margin no-padding">
                <div class="col-md-12 form-group row">
                    <h4><?php echo $this->lang->line("upload_document"); ?></h4>
                </div>
                <div class="col-md-6 no-margin">
                    <div class="form-group row">
                        <label class="control-label col-md-3 <?php echo $require_expense_document ? "required" : ""; ?>" style="padding-top:2px">
                            <?php echo $this->lang->line("upload_document"); ?>
                        </label>
                        <div class="col-md-9">
                            <input type="file"
                                   name="uploadDoc"
                                   id="uploadDoc"
                                   value=""
                                   class=""
                                <?php echo $require_expense_document ? "data-validation-engine='validate[required]'" : ""; ?> />
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Form Submission Section -->
        <div class="col-md-12 no-margin no-padding">
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="control-label col-md-3 no-padding-right"></label>
                    <div class="col-md-7">
                        <?php echo form_submit("submitBtn2", $this->lang->line("save"), [
                            "class" => "btn btn-info " . ($approved ? "d-none" : ""),
                            "id" => "expenseFormSubmit"
                        ]); ?>

                        <?php if (empty($expense["id"])) { ?>
                            <label>
                                <input type="checkbox" name="create_another" value="yes">
                                <strong><?php echo $this->lang->line("create_and_create_another"); ?></strong>
                            </label>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($expense["id"]) && is_numeric($expense["id"])) { ?>
            <!-- Notes Section (for existing expenses only) -->
            <div class="col-md-12 no-margin no-padding">
                <div class="col-md-6">
                    <div class="f_head" id="expense-notes" onclick="toggleComments('<?php echo $expense["expenseID"]; ?>')">
                        <i id="notesToggleIcon" class="fa-solid fa-angle-right">&nbsp;</i>
                        <?php echo $this->lang->line("notes"); ?>
                    </div>
                    <div class="col-md-12 no-padding col-xs-12" style="display:none;" id="notes">
                        <div class="activity-container d-none" id="expense-comments-fieldset">
                            <div id="comments-list"></div>
                            <div class="col-md-11 margin-bottom margin-top no-margin-left">
                                <div id="expenseCommentsPaginationContainer"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php echo form_close(); ?>
    </div> <!-- End container-fluid -->

    <!-- Modal Dialogs -->
    <div id="exchangeRateFormDialog"></div>
    <div id="clientFormDialog"></div>
    <div id="supplierFormDialog"></div>
    <div id="exchangeRates" class="d-none"><?php echo $rates; ?></div>
    <div id="accountsFormDialog"></div>



    <?php $this->load->view("partial/footer"); ?>

    <script>
        var clients_do_not_match = <?php echo isset($clients_do_not_match) && $clients_do_not_match ? $clients_do_not_match : "false"; ?>;
        var organizationCurrencyID = '<?php echo $this->session->userdata("organizationCurrencyID"); ?>';
        var expensePaidThrough = '<?php echo $expense["paid_through"]; ?>';
        var isEditMode = <?php echo isset($is_edit_mode) && $is_edit_mode ? "true" : "false"; ?>;

        function checkAmountMaxDigitsNum(field, rules, i, options) {
            var value = field.val();
            if (value <= 0) {
                return _lang.onlyPositiveNumbersAllowed;
            }
            if (Math.round(value).toString().length > 12) {
                return _lang.maxAmountAllowedDigitsNum;
            }
        }

        function changeAccountsValues(val) {
            jQuery.ajax({
                url: getBaseURL('money') + 'vouchers/get_expense_accounts_by_type/',
                data: {
                    'account_type': val
                },
                type: 'POST',
                dataType: 'JSON',
                beforeSend: function() {
                    jQuery('#loader-global').show();
                },
                success: function(response) {
                    jQuery('#loader-global').hide();
                    if (response.accounts) {
                        var newOptions = '<option value="">&nbsp;</option>';
                        for (var i in response.accounts) {
                            newOptions += '<option currency="' + response.accounts[i].currency_id + '" currencyCode="' + response.accounts[i].currencyCode + '" value="' + response.accounts[i].id + '"' +
                                ((response.accounts[i].id == expensePaidThrough) ? 'selected' : '') +
                                '>' + response.accounts[i].full_account_name + '</option>';
                        }
                        jQuery('#paid_through').html(newOptions).trigger("chosen:updated");
                        setCurrencyCode();
                        setAccountBalance();
                        setExpenseNeedApprovedBalance();
                    }
                },
                error: defaultAjaxJSONErrorsHandler
            });
        }

        function emptyRelatedData() {
            var str = 'related-';
            jQuery.each(jQuery("div[id^=related-]", '#relate-to-container'), function() {
                var relatedObject = jQuery(this).attr('id').substring(jQuery(this).attr('id').length, str.length);
                jQuery('#' + relatedObject + '-id', this).val('');
                jQuery('#' + relatedObject + '-lookup', this).val('');
                jQuery('#' + relatedObject + '-link', this).addClass('d-none');
                jQuery('#' + relatedObject + '-detail', this).html('').addClass('d-none');
            });
        }


    </script>