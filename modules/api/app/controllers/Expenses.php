<?php

require "Top_controller.php";
class Expenses extends Top_controller
{
    public $responseData;
    public $fieldNames;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->load->model("voucher_detail", "voucher_detailfactory");
        $this->voucher_detail = $this->voucher_detailfactory->get_instance();
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->load->model("voucher_related_case");
        $this->account = $this->accountfactory->get_instance();
        $this->responseData = default_response_data();
        $this->fieldNames = ["paidOn", "case_id", "referenceNum", "comments", "paid_through", "amount", "paymentMethod", "tax_id", "expense_category_id", "expense_account", "vendor_id"];
    }
    public function list_expenses()
    {
        $legal_case_id = $this->input->post("legal_case_id");
        if ($legal_case_id) {
            $this->load->model("user_profile");
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $user_id = $this->user_logged_in_data["user_id"];
            $this->user_profile->fetch(["user_id" => $user_id]);
            $override_privacy = $this->user_profile->get_field("overridePrivacy");
            $visible_cases_ids = $this->legal_case->api_load_visible_cases_ids("array", $user_id, $override_privacy);
            if (!in_array($legal_case_id, $visible_cases_ids)) {
                $response = $this->responseData;
                $response["error"] = $this->lang->line("access_denied");
                $this->render($response);
            }
            $data["case_id"] = $legal_case_id;
            $this->load_expense_data($data);
        } else {
            $user_group_id = $this->user_logged_in_data["user_group_id"];
            $list_expenses_permission = $this->api_check_user_permissions($user_group_id, "expenses", "list_expenses", "/money/vouchers/expenses_list/");
            if ($list_expenses_permission) {
                $this->load_expense_data();
            } else {
                $this->list_my_expenses();
            }
        }
    }
    public function list_my_expenses()
    {
        $organization_id = $this->input->post("entity_id") ? $this->input->post("entity_id") : $this->user_logged_in_data["organizationID"];
        $user_id = $this->user_logged_in_data["user_id"];
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $system_preference = $this->system_preferences;
        $system_admin_grp_id = $system_preference["SystemValues"]["systemAdministrationGroupId"];
        $user_accounts = $this->account->api_load_account_user_mapping("expenses_paid_through_acc_types", $user_id, $organization_id, $system_admin_grp_id);
        $user_mapping_expenses_paid_through_acc = [];
        foreach ($user_accounts as $value) {
            $user_mapping_expenses_paid_through_acc[] = $value["accountId"];
        }
        $data["user_mapping_accounts"] = $user_mapping_expenses_paid_through_acc;
        $this->load_expense_data($data);
    }
    private function load_expense_data($data = [])
    {
        $response = $this->responseData;
        $money_lang = $this->user_logged_in_data["moneyLanguage"];
        $organization_id = $this->input->post("entity_id") ? $this->input->post("entity_id") : $this->user_logged_in_data["organizationID"];
        $page_size = strcmp($this->input->post("pageSize"), "") ? $this->input->post("pageSize") : 20;
        $page_nb = strcmp($this->input->post("pageNb"), "") ? $this->input->post("pageNb") : 1;
        $skip = ($page_nb - 1) * $page_size;
        $term = trim((string) $this->input->post("term"));
        $response["success"] = $this->voucher_header->api_load_all_expenses($organization_id, $money_lang, $page_size, $skip, $term, $data);
        $response["success"]["dbDriver"] = $this->getDBDriver();
        $this->render($response);
    }
    public function add($entity_id = 0)
    {
        $this->check_license_availability();
        $response = $this->responseData;
        $fieldNames = $this->fieldNames;
        $data = [];
        $user_id = $this->user_logged_in_data["user_id"];
        $money_lang = $this->user_logged_in_data["moneyLanguage"];
        if (0 < $entity_id) {
            $organization_id = $entity_id;
        } else {
            if ($this->input->post("entity_id")) {
                $organization_id = $this->input->post("entity_id");
            } else {
                $organization_id = $this->user_logged_in_data["organizationID"];
            }
        }
        $system_preference = $this->get_all_money_preferences_values();
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_all_exchange_rates();
        if (!isset($exchange_rates[$organization_id]) && isset($exchange_rates[$organization_id * 1])) {
            $organization_id = $organization_id * 1;
        }
        if (isset($exchange_rates[$organization_id]) && !isset($exchange_rates[$organization_id * 1])) {
            $organization_id = str_pad($organization_id, 4, "0", STR_PAD_LEFT);
        }
        if (isset($exchange_rates[$organization_id])) {
            $data["rates"] = $exchange_rates[$organization_id];
            if (isset($data["rates"])) {
                if ($this->input->post(NULL)) {
                    $this->load->model("legal_case", "legal_casefactory");
                    $this->legal_case = $this->legal_casefactory->get_instance();
                    $case_currency_id = $this->legal_case->get_money_currency(true);
                    if ($this->input->post("client_id") && $this->input->post("case_id") && !empty($case_currency_id) && $this->input->post("billingStatus") == "to-invoice") {
                        $validation_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $this->input->post("amount"), NULL, true);
                    }
                    if ($this->input->post("client_id") && $this->input->post("case_id") && !empty($case_currency_id) && $this->input->post("billingStatus") == "to-invoice" && $validation_capping_amount == "disallow") {
                        $this->legal_case->fetch($this->input->post("case_id"));
                        $legal_case_fields = $this->legal_case->get_fields();
                        $response["error"] = [];
                        $response["error"]["message"] = sprintf($this->lang->line("capping_amount_validation"), $legal_case_fields["category"] == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                    } else {
                        $initial_expense_status = $system_preference["ExpensesValues"]["expenseStatus"];
                        if (!$system_preference["ExpensesValues"]["requireExpenseDocument"] || $system_preference["ExpensesValues"]["requireExpenseDocument"] && !empty($_FILES["uploadDoc"]["name"])) {
                            foreach ($fieldNames as $field) {
                                if (!$this->input->post($field)) {
                                    $_POST[$field] = NULL;
                                }
                            }
                            if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                                $client_added_to_case_message = $this->lang->line("client_added_to_case");
                            }
                            $this->voucher_header->set_field("organization_id", $organization_id);
                            $rf = $this->voucher_header->auto_generate_rf("EXP");
                            $this->voucher_header->set_field("refNum", $rf);
                            $paidOn = $this->input->post("paidOn") ? date("Y-m-d", strtotime($this->input->post("paidOn"))) : date("Y-m-d", time());
                            $this->voucher_header->set_field("dated", $paidOn);
                            $this->voucher_header->set_field("voucherType", "EXP");
                            $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                            $this->voucher_header->set_field("description", $this->input->post("comments"));
                            $this->voucher_header->set_field("createdBy", $user_id);
                            $this->voucher_header->set_field("createdOn", date("Y-m-d H:i:s", time()));
                            $this->voucher_header->set_field("modifiedBy", $user_id);
                            $this->voucher_header->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                            $this->voucher_header->disable_builtin_logs();
                            if ($this->voucher_header->insert()) {
                                $voucher_header_id = $this->voucher_header->get_field("id");
                                if ($this->input->post("case_id")) {
                                    $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                                    $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                                    if (!$this->voucher_related_case->insert()) {
                                        $this->voucher_header->delete($voucher_header_id);
                                    }
                                }
                                $_POST["voucher_header_id"] = $voucher_header_id;
                                $this->expense->set_fields($this->input->post(NULL));
                                $this->load->model("money_preference");
                                $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                                $this->expense->set_field("status", $moneyPreferences["keyValue"]);
                                $billingStatus = $this->input->post("billingStatus") ? $this->input->post("billingStatus") : "internal";
                                $this->expense->set_field("billingStatus", $billingStatus);
                                $this->expense->set_field("voucher_header_id", $voucher_header_id);
                                $this->load->model("account", "accountfactory");
                                $this->account = $this->accountfactory->get_instance();
                                $this->load->model("expense_category", "expense_categoryfactory");
                                $this->expense_category = $this->expense_categoryfactory->get_instance();
                                $billing_status_approved = true;
                                switch ($billingStatus) {
                                    case "not-set":
                                    case "non-billable":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            $this->expense->set_field("client_id", $this->input->post("client_id"));
                                            $this->expense->set_field("client_account_id", NULL);
                                        }
                                        break;
                                    case "to-invoice":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            if (!$this->input->post("client_account_id") || $this->input->post("client_account_id") == "" || !$this->account->fetch(["id" => $this->input->post("client_account_id"), "model_type" => "client", "model_id" => $this->input->post("client_id")])) {
                                                $billing_status_approved = false;
                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_header->delete($voucher_header_id);
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_account"));
                                            } else {
                                                $this->expense->set_field("client_id", $this->input->post("client_id"));
                                                $this->expense->set_field("client_account_id", $this->input->post("client_account_id"));
                                            }
                                        }
                                        break;
                                    default:
                                        $this->expense->set_field("billingStatus", "internal");
                                        $this->expense->set_field("client_id", NULL);
                                        $this->expense->set_field("client_account_id", NULL);
                                        if ($billing_status_approved) {
                                            if (!$this->expense_category->fetch($this->input->post("expense_category_id")) || !$this->account->fetch(["id" => $this->expense_category->get_field("account_id"), "organization_id" => $organization_id])) {
                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_header->delete($voucher_header_id);
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("expense_category"));
                                                $result = false;
                                            } else {
                                                if ($this->expense->insert()) {
                                                    $lang = $this->get_lang_code();
                                                    if ($this->expense->get_field("status") == "approved") {
                                                        $this->expense->fetch($this->expense->get_field("id"));
                                                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                        $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                                                        $this->voucher_detail->set_field("drCr", "C");
                                                        $paid_through_account = $this->account->fetch_account($this->input->post("paid_through"), $lang);
                                                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]]);
                                                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                                                        $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                        if ($this->voucher_detail->insert()) {
                                                            $expense_local_amount = 0;
                                                            $expense_foreign_amount = 0;
                                                            $expense_account = $this->account->fetch_account($this->input->post("expense_account"), $lang);
                                                            if ($this->input->post("tax_id")) {
                                                                $this->load->model("tax", "taxfactory");
                                                                $this->tax = $this->taxfactory->get_instance();
                                                                $tax_account = $this->tax->get_tax_account($this->input->post("tax_id"), $organization_id);
                                                                $expense_amount = $this->input->post("amount") * 100 / ($tax_account["percentage"] + 100);
                                                                $tax_amount = $this->input->post("amount") - $expense_amount;
                                                                $tax_local_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]];
                                                                $tax_foreign_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                $this->voucher_detail->reset_fields();
                                                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                                $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                                                                $this->voucher_detail->set_field("drCr", "D");
                                                                $this->voucher_detail->set_field("local_amount", $tax_local_amount);
                                                                $this->voucher_detail->set_field("foreign_amount", $tax_foreign_amount);
                                                                $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                                if (!$this->voucher_detail->insert()) {
                                                                    $this->expense->delete($this->expense->get_field("id"));
                                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_header->delete($voucher_header_id);
                                                                    $result = false;
                                                                } else {
                                                                    $expense_local_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]];
                                                                    $expense_foreign_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                }
                                                            } else {
                                                                $expense_local_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]];
                                                                $expense_foreign_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                            }
                                                            $this->voucher_detail->reset_fields();
                                                            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                            $this->voucher_detail->set_field("account_id", $this->input->post("expense_account"));
                                                            $this->voucher_detail->set_field("drCr", "D");
                                                            $this->voucher_detail->set_field("local_amount", $expense_local_amount);
                                                            $this->voucher_detail->set_field("foreign_amount", $expense_foreign_amount);
                                                            $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                            if ($this->voucher_detail->insert()) {
                                                                $result = true;
                                                            } else {
                                                                $this->expense->delete($this->expense->get_field("id"));
                                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                $this->voucher_header->delete($voucher_header_id);
                                                                $result = false;
                                                            }
                                                        } else {
                                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                            $result = false;
                                                        }
                                                    } else {
                                                        $result = true;
                                                    }
                                                } else {
                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                    $this->voucher_header->delete($voucher_header_id);
                                                    $result = false;
                                                    $response["error"] = $this->expense->get("validationErrors");
                                                }
                                            }
                                        } else {
                                            $result = false;
                                        }
                                }
                            } else {
                                $result = false;
                            }
                            if ($result) {
                                if ($initial_expense_status == "open") {
                                    $this->send_notification_to_groups_users($voucher_header_id, $this->expense->get_field("id"));
                                }
                                $expense_saved = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("record_expense"));
                                $response["success"]["msg"] = isset($client_added_to_case_message) && $client_added_to_case_message ? [$client_added_to_case_message, $expense_saved] : $expense_saved;
                                $response["success"]["data"]["id"] = $voucher_header_id;
                                if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && $validation_capping_amount == "warning") {
                                    $response["warning"]["msg"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                                }
                                if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                                    $this->config->load("allowed_file_uploads", true);
                                    $allowed_types = $this->config->item("EXP", "allowed_file_uploads");
                                    $allowed_types_arr = explode("|", $allowed_types);
                                    $file_info = pathinfo($_FILES["uploadDoc"]["name"]);
                                    $file_info_extension = strtolower($file_info["extension"]);
                                    if (in_array($file_info_extension, $allowed_types_arr)) {
                                        $this->load->library("dms", ["channel" => $this->user_logged_in_data["channel"], "user_id" => $this->user_logged_in_data["user_id"]]);
                                        $upload_response = $this->dms->upload_file(["module" => "EXP", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                                    } else {
                                        $response["error"] = [];
                                        $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                                        $response["error"]["notAllowedExtensions"][] = $file_info_extension;
                                    }
                                }
                            }
                        } else {
                            $response["error"] = [];
                            $response["error"]["message"] = $this->lang->line("missing_uploaded_file_data");
                        }
                    }
                } else {
                    $this->load->model("expense_category", "expense_categoryfactory");
                    $this->expense_category = $this->expense_categoryfactory->get_instance();
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $data["taxes"] = $this->tax->get_taxes($organization_id, $money_lang);
                    $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
                    array_unshift($data["paymentMethod"], "");
                    $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
                    $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list(false, $organization_id, $money_lang);
                    $data["paid_through"] = $this->account->api_load_accounts_per_organization_per_account_type_id(["type_id" => "1", "typeType" => "'Asset'"], $organization_id, $user_id);
                    $data["require_expense_document"] = $system_preference["ExpensesValues"]["requireExpenseDocument"];
                    $response["success"]["data"] = $data;
                }
            } else {
                $response["error"] = $this->lang->line("no_exchange_rate");
            }
        } else {
            $response["error"] = $this->lang->line("no_exchange_rate");
        }
        $this->render($response);
    }
    private function _validate_voucher($id, $organization_id)
    {
        $result = false;
        if ($this->voucher_header->load(["where" => ["id =" . $id . " and organization_id = " . $organization_id]])) {
            $result = true;
        }
        return $result;
    }
    public function edit($voucherHeaderId = 0)
    {
        $this->check_license_availability();
        $response = $this->responseData;
        $user_id = $this->user_logged_in_data["user_id"];
        $money_lang = $this->user_logged_in_data["moneyLanguage"];
        $this->voucher_header->fetch(0 < $voucherHeaderId ? $voucherHeaderId : ($this->input->post("id") && $this->input->post("id") != "" ? $this->input->post("id") : $voucherHeaderId));
        $organization_id = $this->voucher_header->get_field("organization_id");
        $data = [];
        if (!is_null($organization_id)) {
            $this->load->model("exchange_rate");
            $data["rates"] = $this->exchange_rate->get_organization_exchange_rates($organization_id);
        }
        if (isset($data["rates"])) {
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            if (0 < $voucherHeaderId) {
                if ($this->_validate_voucher($voucherHeaderId, $organization_id)) {
                    $this->load->model("expense_category", "expense_categoryfactory");
                    $this->expense_category = $this->expense_categoryfactory->get_instance();
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $data["taxes"] = $this->tax->get_taxes($organization_id, $money_lang);
                    $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
                    array_unshift($data["paymentMethod"], "");
                    $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
                    $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list(false, $organization_id, $money_lang);
                    $data["paid_through"] = $this->account->api_load_accounts_per_organization_per_account_type_id(["type_id" => "1", "typeType" => "'Asset'"], $organization_id, $user_id);
                    $response["success"]["data"]["formData"] = $data;
                    $data = [];
                    $data["expenseValues"] = $this->voucher_header->fetch_expense_details($voucherHeaderId, $organization_id, $money_lang);
                    $data["expense_status_actions"] = ["open" => ["url" => "move_expense_status_to_open", "display" => "Open"], "cancelled" => ["url" => "move_expense_status_to_cancelled", "display" => "Cancel"], "needs_revision" => ["url" => "move_expense_status_to_needs_revision", "display" => "Needs Revision"], "approved" => ["url" => "move_expense_status_to_approved", "display" => "Approve"]];
                    if (in_array($data["expenseValues"]["billingStatus"], ["invoiced", "reimbursed"])) {
                        if ($data["expenseValues"]["status"] == "approved") {
                            unset($data["expense_status_actions"]);
                        } else {
                            unset($data["expense_status_actions"][$data["expenseValues"]["status"]]);
                        }
                    } else {
                        unset($data["expense_status_actions"][$data["expenseValues"]["status"]]);
                    }
                    $response["success"]["data"]["expenseValues"] = $data["expenseValues"];
                    $response["success"]["data"]["expenseValues"]["expense_status_actions"] = isset($data["expense_status_actions"]) ? $data["expense_status_actions"] : [];
                    $this->load->model("expense_status_note", "expense_status_notefactory");
                    $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                    $this->load->helper("text");
                    $data["expense_notes"] = $this->expense_status_note->fetch_all_expense_notes($data["expenseValues"]["expenseID"]);
                    $response["success"]["data"]["expenseValues"]["expense_notes"] = $data["expense_notes"];
                    $data["case_client"] = $this->legal_case->get_case_client($data["expenseValues"]["case_id"]);
                    if (!$data["case_client"]) {
                        $data["case_client"]["client_id"] = NULL;
                        $data["case_client"]["clientName"] = NULL;
                    }
                    $response["success"]["data"]["case_client"] = $data["case_client"];
                }
            } else {
                $postFields = $this->input->post(NULL);
                $fieldNames = $this->fieldNames;
                foreach ($fieldNames as $field) {
                    if (!$this->input->post($field)) {
                        $_POST[$field] = NULL;
                    }
                }
                if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($postFields)) {
                    $client_added_to_case_message = $this->lang->line("client_added_to_case");
                }
                $safeFields = $this->expense->get("apiSafeFields");
                $tempFields = [];
                foreach ($safeFields as $safeField) {
                    if (!$this->input->post($safeField)) {
                        $tempFields[] = $safeField;
                    }
                }
                if (!empty($tempFields)) {
                    $response["error"] = $this->lang->line("missing_voucher_fields");
                } else {
                    if ($this->input->post(NULL)) {
                        if (!$this->input->post("id") || !$this->input->post("id")) {
                            $response["error"] = $this->lang->line("missing_voucher_id");
                        } else {
                            $result = false;
                            $voucher_header_id = $this->input->post("id");
                            unset($_POST["id"]);
                            unset($postFields["id"]);
                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_header->fetch($voucher_header_id);
                            $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                            $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                            $this->voucher_header->set_field("description", $this->input->post("comments"));
                            $this->voucher_header->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                            $this->voucher_header->set_field("modifiedBy", $user_id);
                            $this->voucher_header->disable_builtin_logs();
                            if ($this->voucher_header->update()) {
                                if ($this->input->post("case_id")) {
                                    $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                                    $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                                    $this->voucher_related_case->insert();
                                }
                                $client_id = $this->input->post("client_id");
                                $this->expense->fetch(["voucher_header_id" => $voucher_header_id]);
                                $expense_client_id = $this->expense->get_field("client_id");
                                $this->expense->set_fields($postFields);
                                $this->load->model("money_preference");
                                $billingStatus = $this->expense->get_field("billingStatus");
                                $billing_status_approved = true;
                                switch ($billingStatus) {
                                    case "not-set":
                                    case "non-billable":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            $this->expense->set_field("client_id", $this->input->post("client_id"));
                                            $this->expense->set_field("client_account_id", NULL);
                                        }
                                        break;
                                    case "to-invoice":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            if (!$this->input->post("client_account_id") || $this->input->post("client_account_id") == "" || !$this->account->fetch(["id" => $this->input->post("client_account_id"), "model_type" => "client", "model_id" => $this->input->post("client_id")])) {
                                                $billing_status_approved = false;
                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_account"));
                                            } else {
                                                $this->expense->set_field("client_id", $this->input->post("client_id"));
                                                $this->expense->set_field("client_account_id", $this->input->post("client_account_id"));
                                            }
                                        }
                                        break;
                                    default:
                                        $this->expense->set_field("billingStatus", "internal");
                                        $this->expense->set_field("client_id", NULL);
                                        $this->expense->set_field("client_account_id", NULL);
								}
                                        if ($billing_status_approved) {
                                            $this->load->model("expense_category", "expense_categoryfactory");
                                            $this->expense_category = $this->expense_categoryfactory->get_instance();
                                            if (!$this->expense_category->fetch($this->input->post("expense_category_id")) || !$this->account->fetch(["id" => $this->expense_category->get_field("account_id"), "organization_id" => $organization_id])) {
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("expense_category"));
                                                $result = false;
                                            } else {
                                                if ($this->expense->update()) {
                                                    $lang = $this->get_lang_code();
                                                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                    $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                                                    $this->voucher_detail->set_field("drCr", "C");
                                                    if ($paid_through_account = $this->account->fetch_account($this->input->post("paid_through"), $lang)) {
                                                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]]);
                                                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                                                        $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                        if ($this->voucher_detail->insert()) {
                                                            $expense_local_amount = 0;
                                                            $expense_foreign_amount = 0;
                                                            if ($this->input->post("expense_account") && ($expense_account = $this->account->fetch_account($this->input->post("expense_account"), $lang))) {
                                                                if ($this->input->post("tax_id")) {
                                                                    $this->load->model("tax", "taxfactory");
                                                                    $this->tax = $this->taxfactory->get_instance();
                                                                    $tax_account = $this->tax->get_tax_account($this->input->post("tax_id"), $organization_id);
                                                                    $expense_amount = $this->input->post("amount") * 100 / ($tax_account["percentage"] + 100);
                                                                    $tax_amount = $this->input->post("amount") - $expense_amount;
                                                                    $tax_local_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]];
                                                                    $tax_foreign_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                    $this->voucher_detail->reset_fields();
                                                                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                                    $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                                                                    $this->voucher_detail->set_field("drCr", "D");
                                                                    $this->voucher_detail->set_field("local_amount", $tax_local_amount);
                                                                    $this->voucher_detail->set_field("foreign_amount", $tax_foreign_amount);
                                                                    $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                                    if (!$this->voucher_detail->insert()) {
                                                                        $this->expense->delete($this->expense->get_field("id"));
                                                                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                        $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                        $this->voucher_header->delete($voucher_header_id);
                                                                    } else {
                                                                        $expense_local_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]];
                                                                        $expense_foreign_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                    }
                                                                } else {
                                                                    $expense_local_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]];
                                                                    $expense_foreign_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                }
                                                                $this->voucher_detail->reset_fields();
                                                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                                $this->voucher_detail->set_field("account_id", $this->input->post("expense_account"));
                                                                $this->voucher_detail->set_field("drCr", "D");
                                                                $this->voucher_detail->set_field("local_amount", $expense_local_amount);
                                                                $this->voucher_detail->set_field("foreign_amount", $expense_foreign_amount);
                                                                $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                                                if ($this->voucher_detail->insert()) {
                                                                    $result = true;
                                                                } else {
                                                                    $this->expense->delete($this->expense->get_field("id"));
                                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_header->delete($voucher_header_id);
                                                                }
                                                            }
                                                        } else {
                                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                        }
                                                    }
                                                } else {
                                                    $result = false;
                                                    $response["error"] = $this->expense->get("validationErrors");
                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                }
                                            }
                                        } else {
                                            $result = false;
                                        }
                                //}
                            }
                            if ($result) {
                                $expense_saved = $this->lang->line("expense_saved_successfully");
                                $response["success"]["msg"] = isset($client_added_to_case_message) && $client_added_to_case_message ? [$client_added_to_case_message, $expense_saved] : $expense_saved;
                            }
                        }
                    } else {
                        $response["error"] = $this->lang->line("data_missing");
                    }
                }
            }
        } else {
            $response["error"] = $this->lang->line("no_exchange_rate");
        }
        $this->render($response);
    }
    public function move_expense_status_to_open()
    {
        $response = $this->responseData;
        $data["comment"] = $this->input->post("comment");
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->expense->fetch(["voucher_header_id" => $this->input->post("id")]);
        $expense_id = $this->expense->get_field("id");
        $ids = [];
        if ($expense_id) {
            $ids[] = $this->expense->get_field("id");
            $result = $this->expense->update_expenses_approval_status_by_id("open", join(",", $ids));
            $response["error"] = $this->expense->get("validationErrors");
            if ($result) {
                $this->send_notification_to_groups_users($this->input->post("id"), $this->expense->get_field("id"), "edit_expense");
                $this->send_notification_to_expense_creator($this->input->post("id"), $this->expense->get_field("id"));
                $this->load->model("expense_status_note", "expense_status_notefactory");
                $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                $this->expense_status_note->set_field("expense_id", $expense_id);
                $this->expense_status_note->set_field("transition", "open");
                $this->expense_status_note->set_field("createdBy", $this->user_logged_in_data["user_id"]);
                $this->expense_status_note->set_field("createdOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                if (!empty($data["comment"])) {
                    $this->expense_status_note->set_field("note", $data["comment"]);
                }
                $this->expense_status_note->disable_builtin_logs();
                $this->expense_status_note->insert();
            }
            $this->update_transaction($expense_id, "revert");
            $response["success"]["message"] = sprintf($this->lang->line("expense_status_changed_to"), $this->lang->line("open"));
            $response["success"]["status"] = "Open";
        }
        $this->render($response);
    }
    public function move_expense_status_to_approved()
    {
        $response = $this->responseData;
        $data["comment"] = $this->input->post("comment");
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->expense->fetch(["voucher_header_id" => $this->input->post("id")]);
        $expenses_data = $this->expense->get_fields();
        $this->voucher_header->fetch($expenses_data["voucher_header_id"]);
        $voucher_expenses_data = $this->voucher_header->get_fields();
        $expense_id = $this->expense->get_field("id");
        $ids = [];
        if ($expense_id) {
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $case_currency_id = $this->legal_case->get_money_currency(true);
            if (!empty($expenses_data["client_id"]) && !empty($voucher_expenses_data["case_id"]) && !empty($case_currency_id) && $expenses_data["billingStatus"] == "to-invoice") {
                $validation_capping_amount = $this->legal_case->validate_capping_amount($expenses_data["client_id"], $voucher_expenses_data["case_id"], $case_currency_id, false, $expenses_data["amount"], NULL, true);
            }
            if (!empty($expenses_data["client_id"]) && !empty($voucher_expenses_data["case_id"]) && !empty($case_currency_id) && $expenses_data["billingStatus"] == "to-invoice" && $validation_capping_amount == "disallow") {
                $this->legal_case->fetch($this->input->post("case_id"));
                $legal_case_fields = $this->legal_case->get_fields();
                $response["error"] = [];
                $response["error"]["message"] = sprintf($this->lang->line("capping_amount_validation"), $legal_case_fields["category"] == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
            } else {
                $ids[] = $this->expense->get_field("id");
                $result = $this->expense->update_expenses_approval_status_by_id("approved", join(",", $ids));
                $response["error"] = $this->expense->get("validationErrors");
                if ($result) {
                    $this->send_notification_to_expense_creator($this->input->post("id"), $this->expense->get_field("id"));
                    $this->load->model("expense_status_note", "expense_status_notefactory");
                    $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                    $this->expense_status_note->set_field("expense_id", $expense_id);
                    $this->expense_status_note->set_field("transition", "approved");
                    $this->expense_status_note->set_field("createdBy", $this->user_logged_in_data["user_id"]);
                    $this->expense_status_note->set_field("createdOn", date("Y-m-d H:i:s", time()));
                    $this->expense_status_note->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                    $this->expense_status_note->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                    if (!empty($data["comment"])) {
                        $this->expense_status_note->set_field("note", $data["comment"]);
                    }
                    $this->expense_status_note->disable_builtin_logs();
                    $this->expense_status_note->insert();
                }
                $this->update_transaction($expense_id);
                $response["success"]["message"] = sprintf($this->lang->line("expense_status_changed_to"), $this->lang->line("approved"));
                $response["success"]["status"] = "Approved";
                if (!empty($expenses_data["client_id"]) && !empty($voucher_expenses_data["case_id"]) && $expenses_data["billingStatus"] == "to-invoice" && $validation_capping_amount == "warning") {
                    $response["warning"]["msg"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                }
            }
        }
        $this->render($response);
    }
    public function move_expense_status_to_needs_revision()
    {
        $response = $this->responseData;
        $data["comment"] = $this->input->post("comment");
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->expense->fetch(["voucher_header_id" => $this->input->post("id")]);
        $expense_id = $this->expense->get_field("id");
        $ids = [];
        if ($expense_id) {
            $ids[] = $this->expense->get_field("id");
            $result = $this->expense->update_expenses_approval_status_by_id("needs_revision", join(",", $ids));
            $response["error"] = $this->expense->get("validationErrors");
            if ($result) {
                $this->send_notification_to_expense_creator($this->input->post("id"), $this->expense->get_field("id"));
                $this->load->model("expense_status_note", "expense_status_notefactory");
                $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                $this->expense_status_note->set_field("expense_id", $expense_id);
                $this->expense_status_note->set_field("transition", "needs_revision");
                $this->expense_status_note->set_field("createdBy", $this->user_logged_in_data["user_id"]);
                $this->expense_status_note->set_field("createdOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                if (!empty($data["comment"])) {
                    $this->expense_status_note->set_field("note", $data["comment"]);
                }
                $this->expense_status_note->disable_builtin_logs();
                $this->expense_status_note->insert();
            }
            $this->update_transaction($expense_id, "revert");
            $response["success"]["message"] = sprintf($this->lang->line("expense_status_changed_to"), $this->lang->line("needs_revision"));
            $response["success"]["status"] = "Needs Revision";
        }
        $this->render($response);
    }
    public function move_expense_status_to_cancelled()
    {
        $response = $this->responseData;
        $data["comment"] = $this->input->post("comment");
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->expense->fetch(["voucher_header_id" => $this->input->post("id")]);
        $expense_id = $this->expense->get_field("id");
        $ids = [];
        if ($expense_id) {
            $ids[] = $this->expense->get_field("id");
            $result = $this->expense->update_expenses_approval_status_by_id("cancelled", join(",", $ids));
            $response["error"] = $this->expense->get("validationErrors");
            if ($result) {
                $this->send_notification_to_expense_creator($this->input->post("id"), $this->expense->get_field("id"));
                $this->load->model("expense_status_note", "expense_status_notefactory");
                $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                $this->expense_status_note->set_field("expense_id", $expense_id);
                $this->expense_status_note->set_field("transition", "cancelled");
                $this->expense_status_note->set_field("createdBy", $this->user_logged_in_data["user_id"]);
                $this->expense_status_note->set_field("createdOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                $this->expense_status_note->set_field("modifiedBy", $this->user_logged_in_data["user_id"]);
                if (!empty($data["comment"])) {
                    $this->expense_status_note->set_field("note", $data["comment"]);
                }
                $this->expense_status_note->disable_builtin_logs();
                $this->expense_status_note->insert();
            }
            $this->update_transaction($expense_id, "revert");
            $response["success"]["message"] = sprintf($this->lang->line("expense_status_changed_to"), $this->lang->line("cancelled"));
            $response["success"]["status"] = "Cancelled";
        }
        $this->render($response);
    }
    private function update_transaction($id, $action = "add")
    {
        $this->expense->fetch($id);
        $expenses_data = $this->expense->get_fields();
        if ($action != "add") {
            $this->voucher_detail->delete(["where" => ["voucher_header_id", $expenses_data["voucher_header_id"]]]);
        } else {
            $this->load->model("exchange_rate");
            $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->user_logged_in_data["organizationID"]);
            if (!empty($exchange_rates)) {
                $this->load->model("legal_case", "legal_casefactory");
                $this->legal_case = $this->legal_casefactory->get_instance();
                $data["rates"] = $exchange_rates;
            }
            $this->voucher_detail->reset_fields();
            $this->voucher_detail->set_field("voucher_header_id", $expenses_data["voucher_header_id"]);
            $this->voucher_detail->set_field("account_id", $expenses_data["paid_through"]);
            $this->voucher_detail->set_field("drCr", "C");
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $lang = $this->get_lang_code();
            $paid_through_account = $this->account->fetch_account($expenses_data["paid_through"], $lang);
            $this->voucher_detail->set_field("local_amount", $expenses_data["amount"] * $data["rates"][$paid_through_account["currency_id"]]);
            $this->voucher_detail->set_field("foreign_amount", $expenses_data["amount"]);
            $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
            if ($this->voucher_detail->insert()) {
                $expense_local_amount = 0;
                $expense_foreign_amount = 0;
                $expense_account = $this->account->fetch_account($expenses_data["expense_account"], $lang);
                if (isset($expenses_data["tax_id"]) && !empty($expenses_data["tax_id"])) {
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $tax_account = $this->tax->get_tax_account($expenses_data["tax_id"], $expense_account["organization_id"]);
                    $expense_amount = $expenses_data["amount"] * 100 / ($tax_account["percentage"] + 100);
                    $tax_amount = $expenses_data["amount"] - $expense_amount;
                    $tax_local_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]];
                    $tax_foreign_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                    $this->voucher_detail->reset_fields();
                    $this->voucher_detail->set_field("voucher_header_id", $expenses_data["voucher_header_id"]);
                    $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                    $this->voucher_detail->set_field("drCr", "D");
                    $this->voucher_detail->set_field("local_amount", $tax_local_amount);
                    $this->voucher_detail->set_field("foreign_amount", $tax_foreign_amount);
                    $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                    if (!$this->voucher_detail->insert()) {
                        $this->expense->delete($this->expense->get_field("id"));
                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $expenses_data["voucher_header_id"]]]);
                        $this->voucher_header->delete($expenses_data["voucher_header_id"]);
                        $result = false;
                    } else {
                        $expense_local_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]];
                        $expense_foreign_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                    }
                } else {
                    $expense_local_amount = $expenses_data["amount"] * $data["rates"][$paid_through_account["currency_id"]];
                    $expense_foreign_amount = $expenses_data["amount"] * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                }
                $this->voucher_detail->reset_fields();
                $this->voucher_detail->set_field("voucher_header_id", $expenses_data["voucher_header_id"]);
                $this->voucher_detail->set_field("account_id", $expenses_data["expense_account"]);
                $this->voucher_detail->set_field("drCr", "D");
                $this->voucher_detail->set_field("local_amount", $expense_local_amount);
                $this->voucher_detail->set_field("foreign_amount", $expense_foreign_amount);
                $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                if ($this->voucher_detail->insert()) {
                    $result = true;
                } else {
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $expenses_data["voucher_header_id"]]]);
                    $this->voucher_header->delete($expenses_data["voucher_header_id"]);
                    $result = false;
                }
            } else {
                $this->voucher_detail->delete(["where" => ["voucher_header_id", $expenses_data["voucher_header_id"]]]);
                $result = false;
            }
        }
    }
    public function get_expense_accounts_by_type()
    {
        $response = $this->responseData;
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $account_type = $this->input->post("account_type");
        $organization_id = $this->input->post("entity_id") ? $this->input->post("entity_id") : $this->user_logged_in_data["organizationID"];
        $user_id = $this->user_logged_in_data["user_id"];
        if (isset($account_type) && !empty($account_type)) {
            switch ($account_type) {
                case "Cash":
                    $accountType = ["type_id" => "1", "typeType" => "'Asset'"];
                    $response["success"]["data"]["accounts"] = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    break;
                case "Credit Card":
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $response["success"]["data"]["accounts"] = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    break;
                case "Cheque & Bank":
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $response["success"]["data"]["accounts"] = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    break;
                case "Online payment":
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $liability_accounts = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $bank_accounts = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    $response["success"]["data"]["accounts"] = array_merge($liability_accounts, $bank_accounts);
                    break;
                case "Other":
                    $accountType = ["type_id" => "1", "typeType" => "'Asset'"];
                    $cach_accounts = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $liability_accounts = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $bank_accounts = $this->account->api_load_accounts_per_organization_per_account_type_id($accountType, $organization_id, $user_id);
                    $array1 = array_merge($cach_accounts, $liability_accounts);
                    $array2 = array_merge($array1, $bank_accounts);
                    $response["success"]["data"]["accounts"] = $array2;
                    break;
            }
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    private function send_notification_to_expense_creator($voucher_id, $expense_title = "", $action = "edit_expense")
    {
        $this->voucher_header->fetch(["id" => $voucher_id]);
        $creator_id = $this->voucher_header->get_field("createdBy");
        $login_user_id = $this->user_logged_in_data["user_id"];
        $result = false;
        if ($creator_id && $creator_id != $login_user_id) {
            $this->load->library("system_notification");
            $notifications_data = ["to" => $creator_id, "objectName" => "expense", "object" => $action, "object_id" => $voucher_id, "object_title" => $expense_title, "objectModelCode" => "", "targetUser" => $creator_id, "user_logged_in_name" => $this->user_logged_in_data["profileName"], "createdBy" => $login_user_id, "createdOn" => date("Y-m-d H:i:s"), "modifiedBy" => $login_user_id, "modifiedOn" => date("Y-m-d H:i:s")];
            $this->system_notification->notification_add($notifications_data);
        }
        return $result;
    }
    private function send_notification_to_groups_users($voucher_id, $expense_title, $action = "add_expense")
    {
        $this->load->model(["money_preference"]);
        $notify_user_group_expense = $this->money_preference->get_value_by_key("notifyUserGroupExpense");
        $notify_uers_expense = $this->money_preference->get_value_by_key("notifyUsersExpense");
        $this->voucher_header->fetch($voucher_id);
        $creator_id = $this->voucher_header->get_field("createdBy");
        $login_user_id = $this->user_logged_in_data["user_id"];
        $all_users = [];
        $users_groups = [];
        $users = [];
        if ($notify_user_group_expense["keyValue"]) {
            $users_groups = array_keys($this->user->load_users_ids_in_groups($notify_user_group_expense["keyValue"]));
        }
        if ($notify_uers_expense["keyValue"]) {
            $users = explode(",", $notify_uers_expense["keyValue"]);
        }
        $all_users = array_unique(array_merge($users_groups, $users), SORT_REGULAR);
        if (($key = array_search($login_user_id, $all_users)) !== false) {
            unset($all_users[$key]);
        }
        if ($all_users) {
            if (!empty($action) && $login_user_id != $creator_id && ($key = array_search($creator_id, $all_users)) !== false) {
                unset($all_users[$key]);
            }
            $this->load->model("notification", "notificationfactory");
            $this->notification = $this->notificationfactory->get_instance();
            $logged_user_id = $this->user_logged_in_data["user_id"];
            $this->load->library("system_notification");
            foreach ($all_users as $user_id) {
                $notifications_data = ["to" => $user_id, "objectName" => "expense", "object" => $action, "object_id" => $voucher_id, "object_title" => $expense_title, "objectModelCode" => "", "targetUser" => $user_id, "user_logged_in_name" => $this->user_logged_in_data["profileName"], "createdBy" => $logged_user_id, "createdOn" => date("Y-m-d H:i:s"), "modifiedBy" => $logged_user_id, "modifiedOn" => date("Y-m-d H:i:s")];
                $this->system_notification->notification_add($notifications_data);
            }
            return true;
        } else {
            return false;
        }
    }
    public function add_bulk($entity_id = 0)
    {
        $this->check_license_availability();
        $response = $this->responseData;
        $fieldNames = $this->fieldNames;
        $data = [];
        $user_id = $this->user_logged_in_data["user_id"];
        $money_lang = $this->user_logged_in_data["moneyLanguage"];
        if (0 < $entity_id) {
            $organization_id = $entity_id;
        } else {
            if ($this->input->post("entity_id")) {
                $organization_id = $this->input->post("entity_id");
            } else {
                $organization_id = $this->user_logged_in_data["organizationID"];
            }
        }
        $system_preference = $this->get_all_money_preferences_values();
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_all_exchange_rates();
        if (!isset($exchange_rates[$organization_id]) && isset($exchange_rates[$organization_id * 1])) {
            $organization_id = $organization_id * 1;
        }
        if (isset($exchange_rates[$organization_id]) && !isset($exchange_rates[$organization_id * 1])) {
            $organization_id = str_pad($organization_id, 4, "0", STR_PAD_LEFT);
        }
        $case_currency_id = $this->legal_case->get_money_currency(true);
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        if (isset($exchange_rates[$organization_id])) {
            $data["rates"] = $exchange_rates[$organization_id];
            if (isset($data["rates"])) {
                if ($this->input->post(NULL)) {
                    $initial_expense_status = $system_preference["ExpensesValues"]["expenseStatus"];
                    if (!$system_preference["ExpensesValues"]["requireExpenseDocument"] || $system_preference["ExpensesValues"]["requireExpenseDocument"] && !empty($_FILES["files"]["name"])) {
                        foreach ($fieldNames as $field) {
                            if (!$this->input->post($field)) {
                                $_POST[$field] = NULL;
                            }
                        }
                        if ($this->input->post("case_id")) {
                            $this->load->model("legal_case", "legal_casefactory");
                            $this->legal_case = $this->legal_casefactory->get_instance();
                            if ($this->legal_case->add_client_to_case($this->input->post(NULL))) {
                                $client_added_to_case_message = $this->lang->line("client_added_to_case");
                            }
                        }
                        $post_data = $this->input->post(NULL);
                        $counter = 0;
                        if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && $this->input->post("records") && is_array($post_data["records"]) && !empty($case_currency_id)) {
                            $total = 0;
                            foreach ($post_data["records"] as $expense) {
                                $total = bcadd($total, $expense["amount"], $allowed_decimal_format);
                            }
                            $validate_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $total, NULL, true);
                        }
                        if ($this->input->post("case_id") && $this->input->post("client_id") && $this->input->post("billingStatus") == "to-invoice" && $this->input->post("records") && !empty($currency_value) && $validate_capping_amount == "disallow") {
                            $this->legal_case->fetch($this->input->post("case_id"));
                            $response["error"] = [];
                            $response["error"]["message"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                        }
                        foreach ($post_data["records"] as $expense) {
                            $this->expense->reset_fields();
                            $this->voucher_header->reset_fields();
                            $this->voucher_header->set_field("organization_id", $organization_id);
                            $this->voucher_header->set_field("refNum", $this->voucher_header->auto_generate_rf("EXP"));
                            $paidOn = !empty($expense["paidOn"]) ? date("Y-m-d", strtotime($expense["paidOn"])) : date("Y-m-d", time());
                            $this->voucher_header->set_field("dated", $paidOn);
                            $this->voucher_header->set_field("voucherType", "EXP");
                            $this->voucher_header->set_field("referenceNum", $expense["referenceNum"] ?? "");
                            $this->voucher_header->set_field("description", $expense["comments"] ?? "");
                            $this->voucher_header->set_field("createdBy", $user_id);
                            $this->voucher_header->set_field("createdOn", date("Y-m-d H:i:s", time()));
                            $this->voucher_header->set_field("modifiedBy", $user_id);
                            $this->voucher_header->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                            $this->voucher_header->disable_builtin_logs();
                            $voucher_header_id = "";
                            if ($this->voucher_header->insert()) {
                                $voucher_header_id = $this->voucher_header->get_field("id");
                                if ($this->input->post("case_id")) {
                                    $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                                    $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                                    if (!$this->voucher_related_case->insert()) {
                                        $this->voucher_header->delete($voucher_header_id);
                                    }
                                }
                                $this->voucher_header->reset_fields();
                                $this->load->model("account", "accountfactory");
                                $this->account = $this->accountfactory->get_instance();
                                $this->load->model("money_preference");
                                $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                                $billing_status = $this->input->post("billingStatus") != "internal" && $this->input->post("billingStatus") != "" ? $this->input->post("billingStatus") : "internal";
                                $this->expense->set_field("voucher_header_id", $voucher_header_id);
                                $this->expense->set_field("expense_category_id", $expense["expense_category_id"] ?? "");
                                $this->expense->set_field("expense_account", $expense["expense_account"] ?? "");
                                $this->expense->set_field("paid_through", $this->input->post("paid_through"));
                                $this->expense->set_field("vendor_id", $expense["vendor_id"] ?? "");
                                $this->expense->set_field("billingStatus", $billing_status);
                                $this->expense->set_field("tax_id", $expense["tax_id"] ?? "");
                                $this->expense->set_field("status", $moneyPreferences["keyValue"]);
                                $this->expense->set_field("amount", $expense["amount"] ?? "");
                                $this->expense->set_field("paymentMethod", $this->input->post("paymentMethod"));
                                $this->expense->set_field("task", $this->input->post("task"));
                                $this->expense->set_field("hearing", $this->input->post("hearing"));
                                $this->expense->set_field("event", $this->input->post("event"));
                                $this->load->model("expense_category", "expense_categoryfactory");
                                $this->expense_category = $this->expense_categoryfactory->get_instance();
                                $billing_status_approved = true;
                                switch ($billing_status) {
                                    case "not-set":
                                    case "non-billable":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            $this->expense->set_field("client_id", $this->input->post("client_id"));
                                            $this->expense->set_field("client_account_id", NULL);
                                        }
                                        break;
                                    case "to-invoice":
                                        if (!$this->input->post("client_id") || $this->input->post("client_id") == "") {
                                            $billing_status_approved = false;
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_money"));
                                        } else {
                                            if (!$this->input->post("client_account_id") || $this->input->post("client_account_id") == "" || !$this->account->fetch(["id" => $this->input->post("client_account_id"), "model_type" => "client", "model_id" => $this->input->post("client_id")])) {
                                                $billing_status_approved = false;
                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_header->delete($voucher_header_id);
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("client_account"));
                                            } else {
                                                $this->expense->set_field("client_id", $this->input->post("client_id"));
                                                $this->expense->set_field("client_account_id", $this->input->post("client_account_id"));
                                            }
                                        }
                                        break;
                                    default:
                                        $this->expense->set_field("client_id", NULL);
                                        $this->expense->set_field("client_account_id", NULL);
                                        if ($billing_status_approved) {
                                            if (!$this->expense_category->fetch($this->input->post("expense_category_id")) || !$this->account->fetch(["id" => $this->expense_category->get_field("account_id"), "organization_id" => $organization_id])) {
                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                $this->voucher_header->delete($voucher_header_id);
                                                $response["error"] = sprintf($this->lang->line("required_rule"), $this->lang->line("expense_category"));
                                                $result = false;
                                            } else {
                                                if ($this->expense->insert()) {
                                                    $result = true;
                                                    if (!empty($_FILES["files"]["name"])) {
                                                        $upload_key = "file_to_be_uploaded";
                                                        if (isset($_FILES["files"]["name"][$counter]["uploadDoc"]) && isset($_FILES["files"]["type"][$counter]["uploadDoc"]) && isset($_FILES["files"]["tmp_name"][$counter]["uploadDoc"]) && isset($_FILES["files"]["error"][$counter]["uploadDoc"]) && isset($_FILES["files"]["size"][$counter]["uploadDoc"])) {
                                                            $_FILES[$upload_key]["name"] = $_FILES["files"]["name"][$counter]["uploadDoc"];
                                                            $_FILES[$upload_key]["type"] = $_FILES["files"]["type"][$counter]["uploadDoc"];
                                                            $_FILES[$upload_key]["tmp_name"] = $_FILES["files"]["tmp_name"][$counter]["uploadDoc"];
                                                            $_FILES[$upload_key]["error"] = $_FILES["files"]["error"][$counter]["uploadDoc"];
                                                            $_FILES[$upload_key]["size"] = $_FILES["files"]["size"][$counter]["uploadDoc"];
                                                            if (!empty($_FILES) && !empty($_FILES[$upload_key])) {
                                                                $this->config->load("allowed_file_uploads", true);
                                                                $allowed_types = $this->config->item("EXP", "allowed_file_uploads");
                                                                $allowed_types_arr = explode("|", $allowed_types);
                                                                $file_info = pathinfo($_FILES[$upload_key]["name"]);
                                                                $file_info_extension = strtolower($file_info["extension"]);
                                                                if (in_array($file_info_extension, $allowed_types_arr)) {
                                                                    $this->load->library("dms", ["channel" => $this->user_logged_in_data["channel"], "user_id" => $this->user_logged_in_data["user_id"]]);
                                                                    $upload_response = $this->dms->upload_file(["module" => "EXP", "module_record_id" => $voucher_header_id, "upload_key" => $upload_key]);
                                                                } else {
                                                                    $response["error"] = [];
                                                                    $response["error"]["message"] = $this->lang->line("unallowed_upload_extensions");
                                                                    $response["error"]["notAllowedExtensions"][] = $file_info_extension;
                                                                }
                                                            }
                                                        }
                                                    }
                                                    if ($this->expense->get_field("status") == "approved") {
                                                        $expense_id = $this->expense->get_field("id");
                                                        $this->expense->reset_fields();
                                                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                        $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                                                        $this->voucher_detail->set_field("drCr", "C");
                                                        $lang = $this->get_lang_code();
                                                        $paid_through_account = $this->account->fetch_account($this->input->post("paid_through"), $lang);
                                                        $this->voucher_detail->set_field("local_amount", $expense["amount"] * $data["rates"][$paid_through_account["currency_id"]]);
                                                        $this->voucher_detail->set_field("foreign_amount", $expense["amount"]);
                                                        $this->voucher_detail->set_field("description", "EXP-" . $expense_id);
                                                        if ($this->voucher_detail->insert()) {
                                                            $expense_local_amount = 0;
                                                            $expense_foreign_amount = 0;
                                                            $expense_account = $this->account->fetch_account($expense["expense_account"], $lang);
                                                            if (!empty($expense["tax_id"])) {
                                                                $this->load->model("tax", "taxfactory");
                                                                $this->tax = $this->taxfactory->get_instance();
                                                                $tax_account = $this->tax->get_tax_account($expense["tax_id"], $organization_id);
                                                                $expense_amount = $expense["amount"] * 100 / ($tax_account["percentage"] + 100);
                                                                $tax_amount = $expense["amount"] - $expense_amount;
                                                                $tax_local_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]];
                                                                $tax_foreign_amount = $tax_amount * $data["rates"][$tax_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                $this->voucher_detail->reset_fields();
                                                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                                $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                                                                $this->voucher_detail->set_field("drCr", "D");
                                                                $this->voucher_detail->set_field("local_amount", $tax_local_amount);
                                                                $this->voucher_detail->set_field("foreign_amount", $tax_foreign_amount);
                                                                $this->voucher_detail->set_field("description", "EXP-" . $expense_id);
                                                                if (!$this->voucher_detail->insert()) {
                                                                    $this->expense->delete($expense_id);
                                                                    if (!empty($upload_response["file"]["id"]) && !empty($upload_response["file"]["module"])) {
                                                                        $this->dms->delete_document($upload_response["file"]["module"], $upload_response["file"]["id"]);
                                                                    }
                                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                    $this->voucher_header->delete($voucher_header_id);
                                                                    $result = false;
                                                                } else {
                                                                    $expense_local_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]];
                                                                    $expense_foreign_amount = $expense_amount * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                                }
                                                            } else {
                                                                $expense_local_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]];
                                                                $expense_foreign_amount = $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
                                                            }
                                                            $this->voucher_detail->reset_fields();
                                                            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                            $this->voucher_detail->set_field("account_id", $expense["expense_account"]);
                                                            $this->voucher_detail->set_field("drCr", "D");
                                                            $this->voucher_detail->set_field("local_amount", $expense_local_amount);
                                                            $this->voucher_detail->set_field("foreign_amount", $expense_foreign_amount);
                                                            $this->voucher_detail->set_field("description", "EXP-" . $expense_id);
                                                            if ($this->voucher_detail->insert()) {
                                                                $this->voucher_detail->reset_fields();
                                                                $result = true;
                                                            } else {
                                                                $this->expense->delete($expense_id);
                                                                if (!empty($upload_response["file"]["id"]) && !empty($upload_response["file"]["module"])) {
                                                                    $this->dms->delete_document($upload_response["file"]["module"], $upload_response["file"]["id"]);
                                                                }
                                                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                                $this->voucher_header->delete($voucher_header_id);
                                                                $result = false;
                                                            }
                                                        } else {
                                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                            $result = false;
                                                        }
                                                    }
                                                } else {
                                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                                    $this->voucher_header->delete($voucher_header_id);
                                                    $result = false;
                                                    $response["error"] = $this->expense->get("validationErrors");
                                                }
                                            }
                                        } else {
                                            $result = false;
                                        }
                                }
                            } else {
                                $result = false;
                            }
                            if ($result) {
                                if ($initial_expense_status == "open") {
                                    $this->send_notification_to_groups_users($voucher_header_id, $this->expense->get_field("id"));
                                }
                                $response["success"]["data"]["id"][] = $voucher_header_id;
                            }
                            $counter++;
                        }
                        if ($result) {
                            $expense_saved = sprintf($this->lang->line("record_added_successfull"), $this->lang->line("record_expense"));
                            $response["success"]["msg"] = isset($client_added_to_case_message) && $client_added_to_case_message ? [$client_added_to_case_message, $expense_saved] : $expense_saved;
                            if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && $this->input->post("records") && !empty($currency_value) && $validate_capping_amount == "warning") {
                                $this->legal_case->fetch($this->input->post("case_id"));
                                $response["warning"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                            }
                        }
                    } else {
                        $response["error"] = [];
                        $response["error"]["message"] = $this->lang->line("missing_uploaded_file_data");
                    }
                } else {
                    $this->load->model("expense_category", "expense_categoryfactory");
                    $this->expense_category = $this->expense_categoryfactory->get_instance();
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $data["taxes"] = $this->tax->get_taxes($organization_id, $money_lang);
                    $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
                    array_unshift($data["paymentMethod"], "");
                    $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
                    $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list(false, $organization_id, $money_lang);
                    $data["paid_through"] = $this->account->api_load_accounts_per_organization_per_account_type_id(["type_id" => "1", "typeType" => "'Asset'"], $organization_id, $user_id);
                    $data["require_expense_document"] = $system_preference["ExpensesValues"]["requireExpenseDocument"];
                    $response["success"]["data"] = $data;
                }
            } else {
                $response["error"] = $this->lang->line("no_exchange_rate");
            }
        } else {
            $response["error"] = $this->lang->line("no_exchange_rate");
        }
        $this->render($response);
    }
}

?>