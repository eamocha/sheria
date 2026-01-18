<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Money_preferences extends Money_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("money_setup"));
        $this->load->model("money_preference");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("money_setup"));
        if ($this->input->post(NULL)) {
            $data = $this->input->post("systemValues");
            if (empty($data)) {
                redirect("money_preferences");
            }
            if (isset($data["currencies"]) && in_array($data["currencies"], $data)) {
                $deleted_currencyIds = [];
                $currencies = $this->money_preference->get_value("currencies", "MoneyCurrency");
                if (isset($data["currencies"]["keyValue"]) && !empty($data["currencies"]["keyValue"])) {
                    $deleted_currencyIds = array_diff(explode(", ", $currencies), $data["currencies"]["keyValue"]);
                }
                if (!empty($deleted_currencyIds)) {
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $accounts = $this->account->load_all_accounts_by_currency(join(",", $deleted_currencyIds));
                    if (!empty($accounts)) {
                        unset($data["currencies"]);
                        $this->set_flashmessage("warning", $this->lang->line("you_might_have_active_accounts_on_this_currency"));
                        redirect("money_preferences");
                    } else {
                        $this->delete_removed_currencies_rates($deleted_currencyIds);
                    }
                } else {
                    if (empty($data["currencies"]["keyValue"])) {
                        unset($data["currencies"]);
                        $this->set_flashmessage("warning", $this->lang->line("you_might_have_active_accounts_on_this_currency"));
                        redirect("money_preferences");
                    }
                }
                $added_currencies = [];
                $added_currencies = array_diff($data["currencies"]["keyValue"], explode(", ", $currencies));
                $response["status"] = $this->money_preference->set_value_by_key($data["currencies"]["groupName"], $data["currencies"]["keyName"], $data["currencies"]["keyValue"]) ? 202 : 101;
                unset($data["currencies"]);
                if (!empty($added_currencies)) {
                    $this->insert_added_currencies_rates($added_currencies);
                    $this->set_flashmessage("warning", $this->lang->line("set_default_exchange_rate"));
                    redirect("setup/rate_between_money_currencies");
                }
            }
            if (array_key_exists("userRatePerHour", $data)) {
                $organizationID = $this->session->userdata("organizationID");
                $systemPreferences = $this->session->userdata("systemPreferences");
                $userRatePerHour = [];
                if (isset($systemPreferences["userRatePerHour"])) {
                    $userRatePerHour = @unserialize($systemPreferences["userRatePerHour"]);
                }
                $userRatePerHour[$organizationID] = $data["userRatePerHour"]["keyValue"];
                $serializedUserRate = serialize($userRatePerHour);
                $data["userRatePerHour"]["keyValue"] = $serializedUserRate;
            }
            $result = $this->money_preference->set_values_by_group_key($data, true);
            if (!$this->input->is_ajax_request()) {
                if ($result) {
                    $this->session->set_userdata("systemPreferences", $this->money_preference->get_values());
                    $this->set_flashmessage("success", $this->lang->line("records_updated"));
                } else {
                    $this->set_flashmessage("error", $this->lang->line("records_not_updated"));
                }
                redirect("money_preferences");
            } else {
                if ($result) {
                    $this->session->set_userdata("systemPreferences", $this->money_preference->get_values());
                    $response["status"] = "success";
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        } else {
            $data = [];
            $data["sysPreferences"] = $this->money_preference->get_key_groups();
            foreach ($data["sysPreferences"] as $groupName => $keyValues) {
                $groupNameHTMLOptions = form_input(["name" => "systemValues[%s][groupName]", "id" => "groupNameOf%s", "type" => "hidden", "value" => $groupName]);
                foreach ($keyValues as $key => $val) {
                    $data["formHTML"][$key] = sprintf($groupNameHTMLOptions, $key, $key) . form_input(["name" => "systemValues[" . $key . "][keyName]", "id" => $key, "type" => "hidden", "class" => "form-control", "value" => $key]);
                    $data["formHTML"][$key] .= $this->get_from_html($key, $val);
                }
            }
            unset($key);
            unset($val);
            $this->includes("jquery/filterTable", "js");
            $this->includes("scripts/system_preferences", "js");
            $this->load->view("partial/header");
            $this->load->view("system_preferences/index", $data);
            $this->load->view("partial/footer");
        }
    }
    private function get_from_html($key, $value)
    {
        if (method_exists($this, $key)) {
            return call_user_func([$this, $key], $value);
        }
        return form_input(["name" => "systemValues[" . $key . "][keyValue]", "id" => "value" . $key, "type" => "text", "class" => "form-control w-50", "value" => $value]);
    }
    private function invoice($default)
    {
        return $this->invoice_language_line("invoice", $default);
    }
    private function quote($default)
    {
        return $this->invoice_language_line("quote", $default);
    }
    private function billTo($default)
    {
        return $this->invoice_language_line("billTo", $default);
    }
    private function invoiceNbr($default)
    {
        return $this->invoice_language_line("invoiceNbr", $default);
    }
    private function date($default)
    {
        return $this->invoice_language_line("date", $default);
    }
    private function due_on($default)
    {
        return $this->invoice_language_line("due_on", $default);
    }
    private function purchaseOrder($default)
    {
        return $this->invoice_language_line("purchaseOrder", $default);
    }
    private function terms($default)
    {
        return $this->invoice_language_line("terms", $default);
    }
    private function items($default)
    {
        return $this->invoice_language_line("items", $default);
    }
    private function subTotal($default)
    {
        return $this->invoice_language_line("subTotal", $default);
    }
    private function totalWithTax($default)
    {
        return $this->invoice_language_line("totalWithTax", $default);
    }
    private function totalWithDiscount($default)
    {
        return $this->invoice_language_line("totalWithDiscount", $default);
    }
    private function total($default)
    {
        return $this->invoice_language_line("total", $default);
    }
    private function Expenses_Item($default)
    {
        return $this->invoice_language_line("Expenses_Item", $default);
    }
    private function Expenses_Description($default)
    {
        return $this->invoice_language_line("Expenses_Description", $default);
    }
    private function Expenses_Sub_item($default)
    {
        return $this->invoice_language_line("Expenses_Sub_item", $default);
    }
    private function Expenses_Quantity($default)
    {
        return $this->invoice_language_line("Expenses_Quantity", $default);
    }
    private function Expenses_Unit_Price($default)
    {
        return $this->invoice_language_line("Expenses_Unit_Price", $default);
    }
    private function Expenses_Amount($default)
    {
        return "<span class=\"col-md-4 col-xs-4 bold\">" . $this->money_preference->get_value("systemDefaultLanguage", "ModuleLanguages") . "</span>" . "<span class=\"col-md-4 col-xs-4 bold\">" . $this->money_preference->get_value("systemForeignLanguage_1", "ModuleLanguages") . "</span>" . "<span class=\"col-md-4 col-xs-4 bold\">" . $this->money_preference->get_value("systemForeignLanguage_2", "ModuleLanguages") . "</span> <br />" . $this->invoice_language_line("Expenses_Amount", $default);
    }
    private function Expenses_Tax($default)
    {
        return $this->invoice_language_line("Expenses_Tax", $default);
    }
    private function Expenses_Discount($default)
    {
        return $this->invoice_language_line("Expenses_Discount", $default);
    }
    private function Expenses_Date($default)
    {
        return $this->invoice_language_line("Expenses_Date", $default);
    }
    private function Items_Item($default)
    {
        return $this->invoice_language_line("Items_Item", $default);
    }
    private function Items_Description($default)
    {
        return $this->invoice_language_line("Items_Description", $default);
    }
    private function Items_Sub_item($default)
    {
        return $this->invoice_language_line("Items_Sub_item", $default);
    }
    private function Items_Quantity($default)
    {
        return $this->invoice_language_line("Items_Quantity", $default);
    }
    private function Items_Unit_Price($default)
    {
        return $this->invoice_language_line("Items_Unit_Price", $default);
    }
    private function Items_Amount($default)
    {
        return $this->invoice_language_line("Items_Amount", $default);
    }
    private function Items_Tax($default)
    {
        return $this->invoice_language_line("Items_Tax", $default);
    }
    private function Items_Discount($default)
    {
        return $this->invoice_language_line("Items_Discount", $default);
    }
    private function Items_Date($default)
    {
        return $this->invoice_language_line("Items_Date", $default);
    }
    private function Time_Logs_Item($default)
    {
        return $this->invoice_language_line("Time_Logs_Item", $default);
    }
    private function Time_Logs_Description($default)
    {
        return $this->invoice_language_line("Time_Logs_Description", $default);
    }
    private function Time_Logs_Sub_item($default)
    {
        return $this->invoice_language_line("Time_Logs_Sub_item", $default);
    }
    private function Time_Logs_Quantity($default)
    {
        return $this->invoice_language_line("Time_Logs_Quantity", $default);
    }
    private function Time_Logs_Unit_Price($default)
    {
        return $this->invoice_language_line("Time_Logs_Unit_Price", $default);
    }
    private function Time_Logs_Amount($default)
    {
        return $this->invoice_language_line("Time_Logs_Amount", $default);
    }
    private function Time_Logs_Tax($default)
    {
        return $this->invoice_language_line("Time_Logs_Tax", $default);
    }
    private function Time_Logs_Discount($default)
    {
        return $this->invoice_language_line("Time_Logs_Discount", $default);
    }
    private function Time_Logs_Date($default)
    {
        return $this->invoice_language_line("Time_Logs_Date", $default);
    }
    private function time_logs($default)
    {
        return $this->invoice_language_line("time_logs", $default);
    }
    private function expenses($default)
    {
        return $this->invoice_language_line("expenses", $default);
    }
    private function tax_number($default)
    {
        return $this->invoice_language_line("tax_number", $default);
    }
    private function sub_total_after_discount($default)
    {
        return $this->invoice_language_line("sub_total_after_discount", $default);
    }
    private function case_id($default)
    {
        return $this->invoice_language_line("case_id", $default);
    }
    private function case_subject($default)
    {
        return $this->invoice_language_line("case_subject", $default);
    }
    private function reference_case($default)
    {
        return $this->invoice_language_line("reference_case", $default);
    }
    private function invoice_ref($default)
    {
        return $this->invoice_language_line("invoice_ref", $default);
    }
    private function exchange_rate($default)
    {
        return $this->invoice_language_line("exchange_rate", $default);
    }
    private function legal_matters($default)
    {
        return $this->invoice_language_line("legal_matters", $default);
    }
    private function time_logs_quantity_unit($default)
    {
        return $this->invoice_language_line("time_logs_quantity_unit", $default);
    }
    private function currency_only($default)
    {
        return $this->invoice_language_line("currency_only", $default);
    }
    private function credit_note($default)
    {
        return $this->invoice_language_line("credit_note", $default);
    }
    private function credit_note_number($default)
    {
        return $this->invoice_language_line("credit_note_number", $default);
    }
    private function invoice_credited($default)
    {
        return $this->invoice_language_line("invoice_credited", $default);
    }
    private function credit_note_reference($default)
    {
        return $this->invoice_language_line("credit_note_reference", $default);
    }
    private function sub_total_exclusive_vat($default)
    {
        return $this->invoice_language_line("sub_total_exclusive_vat", $default);
    }
    private function status($default)
    {
        return $this->invoice_language_line("status", $default);
    }
    private function paid_amount($default)
    {
        return $this->invoice_language_line("paid_amount", $default);
    }
    private function remaining_amount($default)
    {
        return $this->invoice_language_line("remaining_amount", $default);
    }
    private function tax_amount($default)
    {
        return $this->invoice_language_line("tax_amount", $default);
    }
    private function payment_method($default)
    {
        return $this->invoice_language_line("payment_method", $default);
    }
    private function transaction_type($default)
    {
        return $this->invoice_language_line("transaction_type", $default);
    }
    private function debit_note_number($default)
    {
        return $this->invoice_language_line("debit_note_number", $default);
    }
    private function related_invoice_number($default)
    {
        return $this->invoice_language_line("related_invoice_number", $default);
    }
    private function invoice_language_line($key, $value)
    {
        $value = (array) (empty($value) ? array_fill_keys(["", "fl1", "fl2"], "") : unserialize($value));
        if (isset($value[0])) {
            $value[""] = $value[0];
            unset($value[0]);
        }
        return "<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][]", "id" => "value" . $key, "type" => "text", "value" => $value[""], "class" => "form-control keyName" . $key]) . "</span>\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl1]", "id" => "value" . $key, "type" => "text", "value" => $value["fl1"], "class" => "form-control keyName" . $key]) . "</span >\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl2]", "id" => "value" . $key, "type" => "text", "value" => $value["fl2"], "class" => "form-control keyName" . $key]) . "</span>";
    }
    private function expenseStatus($default)
    {
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $values = array_combine($this->expense->get("expenseStatusValues"), [$this->lang->line("approved"), $this->lang->line("needs_revision"), $this->lang->line("open"), $this->lang->line("cancelled")]);
        return form_dropdown("systemValues[expenseStatus][keyValue]", $values, $default, "id=\"valueexpenseStatus\"");
    }
    private function userRatePerHour($default)
    {
        $money_preference = $this->money_preference->get_value_by_key("userRatePerHour");
        $organizationID = $this->session->userdata("organizationID");
        $userRatePerHour = "";
        if (isset($money_preference["keyValue"])) {
            $userRatesPerHour = @unserialize($money_preference["keyValue"]);
        }
        if (isset($userRatesPerHour[$organizationID])) {
            $userRatePerHour = $userRatesPerHour[$organizationID];
        }
        return form_input(["name" => "systemValues[userRatePerHour][keyValue]", "id" => "valueuserRatePerHour", "type" => "text", "class" => "w-50 form-control", "value" => $userRatePerHour]);
    }
    private function invoiceNumberPrefix($default)
    {
        return form_input(["name" => "systemValues[invoiceNumberPrefix][keyValue]", "id" => "valueinvoiceNumberPrefix", "type" => "text", "class" => "form-control", "value" => $default]);
    }
    private function TEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[TEnabled][keyValue]", $array, $default, "id=\"valueTEnabled\"");
    }
    private function SettlementsPerInvoiceEnabled($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return "<span id=\"settlements-per-invoice-container\">" . form_dropdown("systemValues[SettlementsPerInvoiceEnabled][keyValue]", $array, $default, "id=\"valueSettlementsPerInvoiceEnabled\"") . "</span><span class=\"icon-helper\"><i role=\"button\" title=\"" . $this->lang->line("helper_partners_settlements") . "\" class=\"fa fa-question-circle fa-lg\"></i></span>";
    }
    public function save_system_value()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("money_preferences");
        }
        $response = [];
        $systemValue = $this->input->post("systemValue");
        if ($systemValue["groupName"] == "MoneyCurrency" && $systemValue["keyName"] == "currencies") {
            $currencies = $this->money_preference->get_value("currencies", "MoneyCurrency");
            if (empty($systemValue["keyValue"])) {
                $response["status"] = 102;
                $this->set_flashmessage("warning", $this->lang->line("you_might_have_active_accounts_on_this_currency"));
            } else {
                $currency_has_active_accounts = false;
                $added_currencies = [];
                $removed_currencies = [];
                $added_currencies = array_diff($systemValue["keyValue"], explode(", ", $currencies));
                $removed_currencies = array_diff(explode(", ", $currencies), $systemValue["keyValue"]);
                if (!empty($removed_currencies)) {
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $accounts = $this->account->load_all_accounts_by_currency(join(",", $removed_currencies));
                    if (!empty($accounts)) {
                        $currency_has_active_accounts = true;
                    }
                }
                if ($currency_has_active_accounts) {
                    $response["status"] = 102;
                    $this->set_flashmessage("warning", $this->lang->line("you_might_have_active_accounts_on_this_currency"));
                } else {
                    $response["status"] = $this->money_preference->set_value_by_key($systemValue["groupName"], $systemValue["keyName"], $systemValue["keyValue"]) ? 202 : 101;
                    if (!empty($removed_currencies)) {
                        $this->delete_removed_currencies_rates($removed_currencies);
                    }
                    if (!empty($added_currencies)) {
                        $this->insert_added_currencies_rates($added_currencies);
                        $this->set_flashmessage("warning", $this->lang->line("set_default_exchange_rate"));
                        $response["status"] = 500;
                    }
                }
            }
        } else {
            if ($systemValue["groupName"] == "UsersValues" && $systemValue["keyName"] == "userRatePerHour") {
                $organizationID = $this->session->userdata("organizationID");
                $systemPreferences = $this->session->userdata("systemPreferences");
                $userRatePerHour = [];
                if (isset($systemPreferences["userRatePerHour"])) {
                    $userRatePerHour = @unserialize($systemPreferences["userRatePerHour"]);
                }
                $userRatePerHour[$organizationID] = $systemValue["keyValue"];
                $response["status"] = $this->money_preference->set_value_by_key("UsersValues", "userRatePerHour", serialize($userRatePerHour)) ? 202 : 101;
            } else {
                if ($systemValue["groupName"] == "UsersValues" && $systemValue["keyName"] == "userGroupsAppearInUserRatePerHourGrid") {
                    $this->load->model("user_rate_per_hour", "user_rate_per_hourfactory");
                    $this->user_rate_per_hour = $this->user_rate_per_hourfactory->get_instance();
                    $group_have_related_rate = $this->user_rate_per_hour->get_all_related_user_group($systemValue["keyValue"]);
                    if (isset($group_have_related_rate) && $group_have_related_rate) {
                        $this->user_rate_per_hour->delete_all_user_rate_related_to_groups($group_have_related_rate);
                    }
                    $response["status"] = $this->money_preference->set_value_by_key($systemValue["groupName"], $systemValue["keyName"], $systemValue["keyValue"]) ? 202 : 101;
                } else {
                    $response["status"] = $this->money_preference->set_value_by_key($systemValue["groupName"], $systemValue["keyName"], $systemValue["keyValue"]) ? 202 : 101;
                }
            }
        }
        $response["result"] = $systemValue;
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function currencies($default)
    {
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $currencies = $this->country->load_currency_list("id", "currency_country");
        unset($currencies[""]);
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[currencies][keyValue][]", $currencies, $default, "id=\"valuecurrencies\" multiple=\"multiple\" required=\"\"");
    }
    private function userGroupsAppearInUserRatePerHourGrid($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return form_dropdown("systemValues[userGroupsAppearInUserRatePerHourGrid][keyValue][]", $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]]), $default, "id=\"valueuserGroupsAppearInUserRatePerHourGrid\" multiple=\"multiple\"");
    }
    private function notifyUserGroupExpense($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return "<span id=\"notify-user-group-container\">" . form_dropdown("systemValues[notifyUserGroupExpense][keyValue][]", $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]]), $default, "id=\"valuenotifyUserGroupExpense\" multiple=\"multiple\"") . "</span><span class=\"icon-helper\"><i role=\"button\" title=\"" . $this->lang->line("helper_notify_users__groups_expense") . "\" class=\"fa fa-question-circle fa-lg\"></i></span>";
    }
    private function notifyUsersExpense($default)
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return "<span id=\"notify-users-container\">" . form_dropdown("systemValues[notifyUsersExpense][keyValue][]", $this->user->load_users_list("", ["key" => "id", "value" => "name"]), $default, "id=\"valuenotifyUsersExpense\" multiple=\"multiple\"") . "</span><span class=\"icon-helper\"><i role=\"button\" title=\"" . $this->lang->line("helper_notify_users_expense") . "\" class=\"fa fa-question-circle fa-lg\"></i></span>";
    }
    private function notifyUsersGroupToApproveExpense($default)
    {
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return "<span id=\"notify-user-group-to-approve-container\">" . form_dropdown("systemValues[notifyUsersGroupToApproveExpense][keyValue][]", $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]]), $default, "id=\"valuenotifyUsersGroupToApproveExpense\" multiple=\"multiple\"") . "</span><span class=\"icon-helper\"><i role=\"button\" title=\"" . $this->lang->line("helper_notify_users__groups_expense") . "\" class=\"fa fa-question-circle fa-lg\"></i></span>";
    }
    private function notifyUsersToApproveExpense($default)
    {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        return "<span id=\"notify-users-to-approve-container\">" . form_dropdown("systemValues[notifyUsersToApproveExpense][keyValue][]", $this->user->load_users_list("", ["key" => "id", "value" => "name"]), $default, "id=\"valuenotifyUsersToApproveExpense\" multiple=\"multiple\"") . "</span><span class=\"icon-helper\"><i role=\"button\" title=\"" . $this->lang->line("helper_notify_users_expense") . "\" class=\"fa fa-question-circle fa-lg\"></i></span>";
    }
    private function notifyUsersExpenseByEmail($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[notifyUsersExpenseByEmail][keyValue]", $array, $default, "id=\"valuenotifyUsersExpenseByEmail\"");
    }
    private function requireExpenseDocument($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[requireExpenseDocument][keyValue]", $array, $default, "id=\"valuerequireExpenseDocument\"");
    }
    private function trustAssetAccount($default)
    {
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        if (!empty($default)) {
            $default = explode(", ", $default);
        }
        $this->config->load("accounts_map", true);
        $accounts_map = $this->config->item("accounts_map");
        return form_dropdown("systemValues[trustAssetAccount][keyValue][]", $this->account->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["account_type_id", $accounts_map["TrustAsset"]["type_id"]]]]), $default, "id=\"valuetrustAssetAccount\"");
    }
    private function DisplayItemDate($default)
    {
        $array = [1 => $this->lang->line("yes"), 0 => $this->lang->line("no")];
        return form_dropdown("systemValues[DisplayItemDate][keyValue]", $array, $default, "id=\"valueDisplayItemDate\"");
    }
    public function has_related_user_group()
    {
        $group_ids = $this->input->post("group_ids", true);
        $this->load->model("user_rate_per_hour", "user_rate_per_hourfactory");
        $this->user_rate_per_hour = $this->user_rate_per_hourfactory->get_instance();
        $has_user_rate = $this->user_rate_per_hour->has_related_user_group($group_ids);
        $this->output->set_content_type("application/json")->set_output(json_encode($has_user_rate));
    }
    private function userCode($default)
    {
        return $this->invoice_language_line("userCode", $default);
    }
    private function partner_name($default)
    {
        return $this->partner_inv_language_line("partner_name", $default);
    }
    private function payment_due_to($default)
    {
        return $this->partner_inv_language_line("payment_due_to", $default);
    }
    private function client_name($default)
    {
        return $this->partner_inv_language_line("client_name", $default);
    }
    private function invoice_nb($default)
    {
        return $this->partner_inv_language_line("invoice_nb", $default);
    }
    private function partner_date($default)
    {
        return $this->partner_inv_language_line("partner_date", $default);
    }
    private function total_items($default)
    {
        return $this->partner_inv_language_line("total_items", $default);
    }
    private function partner_time_logs($default)
    {
        return $this->partner_inv_language_line("partner_time_logs", $default);
    }
    private function partner_items($default)
    {
        return $this->partner_inv_language_line("partner_items", $default);
    }
    private function partner_expenses($default)
    {
        return $this->partner_inv_language_line("partner_expenses", $default);
    }
    private function partner_item($default)
    {
        return $this->partner_inv_language_line("partner_item", $default);
    }
    private function partner_item_description($default)
    {
        return $this->partner_inv_language_line("partner_item_description", $default);
    }
    private function partner_item_date($default)
    {
        return $this->partner_inv_language_line("partner_item_date", $default);
    }
    private function partner_item_quantity($default)
    {
        return $this->partner_inv_language_line("partner_item_quantity", $default);
    }
    private function partner_item_uprice($default)
    {
        return $this->partner_inv_language_line("partner_item_uprice", $default);
    }
    private function partner_item_tamount($default)
    {
        return $this->partner_inv_language_line("partner_item_tamount", $default);
    }
    private function partner_item_percentage($default)
    {
        return $this->partner_inv_language_line("partner_item_percentage", $default);
    }
    private function partner_item_amount($default)
    {
        return $this->partner_inv_language_line("partner_item_amount", $default);
    }
    private function partner_item_hour($default)
    {
        return $this->partner_inv_language_line("partner_item_hour", $default);
    }
    private function partner_item_rate($default)
    {
        return $this->partner_inv_language_line("partner_item_rate", $default);
    }
    private function partner_case_id($default)
    {
        return $this->partner_inv_language_line("partner_case_id", $default);
    }
    private function partner_case_subject($default)
    {
        return $this->partner_inv_language_line("partner_case_subject", $default);
    }
    private function partner_legal_matters($default)
    {
        return $this->partner_inv_language_line("partner_legal_matters", $default);
    }
    private function partner_deductions($default)
    {
        return $this->partner_inv_language_line("partner_deductions", $default);
    }
    private function partner_invoice_total($default)
    {
        return $this->partner_inv_language_line("partner_invoice_total", $default);
    }
    private function partner_paid_amount($default)
    {
        return $this->partner_inv_language_line("partner_paid_amount", $default);
    }
    private function partner_amount_payable($default)
    {
        return $this->partner_inv_language_line("partner_amount_payable", $default);
    }
    private function partner_inv_language_line($key, $value)
    {
        $value = (array) (empty($value) ? array_fill_keys(["", "fl1", "fl2"], "") : unserialize($value));
        if (isset($value[0])) {
            $value[""] = $value[0];
            unset($value[0]);
        }
        return "<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][]", "id" => "value" . $key, "type" => "text", "value" => $value[""], "class" => "form-control keyName" . $key]) . "</span>\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl1]", "id" => "value" . $key, "type" => "text", "value" => $value["fl1"], "class" => "form-control keyName" . $key]) . "</span >\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl2]", "id" => "value" . $key, "type" => "text", "value" => $value["fl2"], "class" => "form-control keyName" . $key]) . "</span>";
    }
    private function bill_supplier($default)
    {
        return $this->bill_language_line("bill_supplier", $default);
    }
    private function bill_number($default)
    {
        return $this->bill_language_line("bill_number", $default);
    }
    private function bill_tax_number($default)
    {
        return $this->bill_language_line("bill_tax_number", $default);
    }
    private function bill_date($default)
    {
        return $this->bill_language_line("bill_date", $default);
    }
    private function bill_due_date($default)
    {
        return $this->bill_language_line("bill_due_date", $default);
    }
    private function bill_client($default)
    {
        return $this->bill_language_line("bill_client", $default);
    }
    private function bill_related_matter($default)
    {
        return $this->bill_language_line("bill_related_matter", $default);
    }
    private function bill_account($default)
    {
        return $this->bill_language_line("bill_account", $default);
    }
    private function bill_description($default)
    {
        return $this->bill_language_line("bill_description", $default);
    }
    private function bill_quantity($default)
    {
        return $this->bill_language_line("bill_quantity", $default);
    }
    private function bill_price($default)
    {
        return $this->bill_language_line("bill_price", $default);
    }
    private function bill_tax($default)
    {
        return $this->bill_language_line("bill_tax", $default);
    }
    private function bill_amount($default)
    {
        return $this->bill_language_line("bill_amount", $default);
    }
    private function bill_total($default)
    {
        return $this->bill_language_line("bill_total", $default);
    }
    private function bill_details($default)
    {
        return $this->bill_language_line("bill_details", $default);
    }
    private function bill_total_tax($default)
    {
        return $this->bill_language_line("bill_total_tax", $default);
    }
    private function bill_sub_total($default)
    {
        return $this->bill_language_line("bill_sub_total", $default);
    }
    private function bill_language_line($key, $value)
    {
        $value = (array) (empty($value) ? array_fill_keys(["", "fl1", "fl2"], "") : unserialize($value));
        if (isset($value[0])) {
            $value[""] = $value[0];
            unset($value[0]);
        }
        return "<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][]", "id" => "value" . $key, "type" => "text", "value" => $value[""], "class" => "form-control keyName" . $key]) . "</span>\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl1]", "id" => "value" . $key, "type" => "text", "value" => $value["fl1"], "class" => "form-control keyName" . $key]) . "</span >\r\n\t\t\t\t<span class=\"col-md-4 col-xs-4\">" . form_input(["name" => "systemValues[" . $key . "][keyValue][fl2]", "id" => "value" . $key, "type" => "text", "value" => $value["fl2"], "class" => "form-control keyName" . $key]) . "</span>";
    }
    private function insert_added_currencies_rates($currencies)
    {
        $this->load->model("exchange_rate");
        $exchange_rates = [];
        $all_organizations = $this->session->userdata("organizations");
        foreach (array_keys($all_organizations) as $organization_id) {
            foreach ($currencies as $currency_id) {
                array_push($exchange_rates, ["currency_id" => $currency_id, "organization_id" => $organization_id, "rate" => NULL]);
            }
        }
        $this->exchange_rate->insert_batch($exchange_rates);
    }
    private function delete_removed_currencies_rates($currencies)
    {
        $this->load->model("exchange_rate");
        $ids = implode(",", $currencies);
        $this->exchange_rate->delete(["where" => ["currency_id IN (" . $ids . ")"]]);
    }
}

?>