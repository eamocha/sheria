<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Clients extends Money_controller
{
    public $Client;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("client");
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("clients_money"));
        $this->currentTopNavItem = "money";
    }
    public function autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $term = $this->db->escape_like_str($term);
        $configList = ["key" => "clients_view.id", "value" => "name"];
        $this->db->select("clients_view.*");
        $this->db->where("clients_view.model = 'clients' and clients_view.name LIKE '%" . $term . "%'");
        $result = $this->db->get("clients_view");
        $results = $configList == "array" ? $result->result_array() : $result->result();
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("clients_money"));
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("clients");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->client->k_load_all_clients($filter, $sortable);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["clientTypes"] = ["" => "", "Company" => $this->lang->line("company"), "Person" => $this->lang->line("contact")];
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/clients", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("clients/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function add()
    {
        $this->save(0);
    }
    private function save($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("clients_money"));
        $data = [];
        $data["client"] = ["id" => "", "clientName" => "", "company_id" => "", "contact_id" => ""];
        $client_extra_data = [];
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->config->load("accounts_map", true);
        $accountsMap = $this->config->item("accounts_map");
        $account_type_id = $accountsMap["Client"]["type_id"];
        if ($this->input->post(NULL)) {
            $model = "contact";
            $account_model_name = "Person";
            if ($this->input->post("clientType") == "companies") {
                $model = "company";
                $account_model_name = "Company";
                $data["client"]["company_id"] = $this->input->post("contact_company_id");
            } else {
                $data["client"]["contact_id"] = $this->input->post("contact_company_id");
            }
            if ($this->input->post("term_id")) {
                $client_extra_data["term_id"] = $this->input->post("term_id");
            }
            if (is_numeric($this->input->post("discount_percentage"))) {
                $client_extra_data["discount_percentage"] = $this->input->post("discount_percentage");
            }
            $this->account->set_field("accountData", $this->input->post("accountData"));
            $result = $this->client->get_client($model, $this->input->post("contact_company_id"), $client_extra_data);
            if (!is_nan($result)) {
                $accountResult = false;
                if ($this->input->post("currency_id")) {
                    $this->account->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->account->set_field("currency_id", $this->input->post("currency_id"));
                    $this->account->set_field("account_type_id", $account_type_id);
                    $this->account->set_field("name", $this->input->post("clientName"));
                    $this->account->set_field("description", $this->input->post("description"));
                    $this->account->set_field("model_id", $result);
                    $this->account->set_field("member_id", $this->input->post("contact_company_id"));
                    $this->account->set_field("model_name", $account_model_name);
                    $this->account->set_field("model_type", "client");
                    $this->account->set_field("systemAccount", "no");
                    $this->account->set_field("number", $this->input->post("number"));
                    $this->account->set_field("show_in_dashboard", "1");
                    $accountResult = $this->account->insert();
                }
                if (!$this->input->is_ajax_request()) {
                    if ($accountResult) {
                        $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("client_money")));
                        redirect("clients/index/");
                    }
                } else {
                    if ($accountResult) {
                        $response["status"] = true;
                        $response["account"] = $this->account->fetch_account($this->account->get_field("id"));
                        if ($response["account"]["model_type"] == "client") {
                            $response["account"]["client"] = $this->client->fetch_client($response["account"]["model_id"]);
                        }
                    } else {
                        $response["status"] = false;
                        $response["validationErrors"] = $this->account->get("validationErrors");
                    }
                    $response["model_id"] = $result;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            } else {
                if (!$this->input->is_ajax_request()) {
                    $data["client"]["clientName"] = $this->input->post("clientName");
                    $this->set_flashmessage("error", sprintf($this->lang->line("save_record_failed"), $this->lang->line("client_money")));
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        }
        if ($id != 0) {
            $data["client"] = $this->client->fetch_client($id);
        }
        $this->load->model("term");
        $money_language = $this->user_preference->get_value("money_language");
        $data["terms"] = $this->term->load_list([], ["value" => $money_language . "name", "firstLine" => ["" => $this->lang->line("choose_term")]]);
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["currencies"] = $this->country->load_money_currency_list("id", "currency_country");
        $this->load->model("account_number_prefix_per_entity", "account_number_prefix_per_entityfactory");
        $this->account_number_prefix_per_entity = $this->account_number_prefix_per_entityfactory->get_instance();
        $this->account_number_prefix_per_entity->fetch(["organization_id" => $this->session->userdata("organizationID"), "account_type_id" => $account_type_id]);
        $data["client"]["prefix"] = $this->account_number_prefix_per_entity->get_field("account_number_prefix");
        $max_numbers = $this->account->load_max_numbers_per_acc_type($account_type_id);
        $data["client"]["number"] = isset($max_numbers[$account_type_id]) ? $max_numbers[$account_type_id] : 1;
        if (!$this->input->is_ajax_request()) {
            $this->load->view("partial/header");
            $this->load->view("clients/form", $data);
            $this->load->view("partial/footer");
        } else {
            $data["quick_add"] = true;
            $response["html"] = $this->load->view("clients/form", $data, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function export_to_excel()
    {
        $filter = json_decode($this->input->post("filter"), true);
        $sortable = json_decode($this->input->post("sort"), true);
        $data = $this->client->k_load_all_clients($filter, $sortable);
        $filename = urlencode($this->lang->line("clients_money"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/clients_list", $data);
        $this->load->view("excel/footer");
    }
    public function edit_client($id)
    {
        if ($this->input->post(NULL)) {
            $response = [];
            $response["status"] = false;
            if (!$this->input->is_ajax_request()) {
                $this->set_flashmessage("error", sprintf($this->lang->line("save_record_failed"), $this->lang->line("client_money")));
                redirect("clients/client_details/" . $id);
            }
            if (!empty($id)) {
                $this->client->fetch($id);
                $this->client->set_field("term_id", $this->input->post("term_id"));
                $this->client->set_field("discount_percentage", $this->input->post("discount_percentage"));
                if ($this->client->update()) {
                    $response["status"] = true;
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = $this->get_client_tabs($id, site_url("clients/edit_client"));
            $data["id"] = $id;
            $data["related_account_url"] = $this->load->view("companies/get_links", $data, true);
            $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
            $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
            $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
            $this->load->model("deposit", "depositfactory");
            $this->deposit = $this->depositfactory->get_instance();
            $data["deposits"] = $this->deposit->load_client_trust_accounts($id);
            $this->load->model("term");
            $money_language = $this->user_preference->get_value("money_language");
            $data["terms"] = $this->term->load_list([], ["value" => $money_language . "name", "firstLine" => ["" => $this->lang->line("choose_term")]]);
            $this->includes("money/js/client_common_tabs", "js");
            $this->load->view("partial/header");
            $this->load->view("clients/edit_client", $data);
            $this->load->view("partial/footer");
        }
    }
    public function client_details($id)
    {
        $data = [];
        $data = $this->get_client_tabs($id, site_url("clients/client_details"));
        $data["id"] = $id;
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $data["accountRecords"] = $this->account->get_client_accounts_details($id);
        $related_comp_cont = $this->client->fetch_client_related_contact_company($id);
        $data["name"] = $related_comp_cont["name"];
        $data["member_id"] = $data["member_id"];
        $data["related_account_url"] = $this->load->view("companies/get_links", $data, true);
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $data["deposits"] = $this->deposit->load_client_trust_accounts($id);
        $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
        $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
        $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
        $this->includes("money/js/client_common_tabs", "js");
        $this->includes("money/js/client_details", "js");
        $open_balance_info_msg = $this->session->userdata("open_balance_info_msg");
        if ($open_balance_info_msg == 1) {
            $data["open_balance_info_msg"] = $this->lang->line("open_balance_saved");
            $this->session->unset_userdata("open_balance_info_msg");
        }
        $this->load->view("partial/header");
        $this->load->view("clients/client_details", $data);
        $this->load->view("partial/footer");
    }
    private function get_client_tabs(&$id = "", $active = "")
    {
        if ($id < 0) {
            $this->set_flashmessage("information", $this->lang->line("invalid_record"));
            redirect("clients");
        }
        $data = [];
        $data["clientData"] = $this->client->fetch_client($id);
        if ($id && !$data["clientData"]) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("clients");
        }
        if ($data["clientData"]["type"] == "Company") {
            $this->load->model("company", "companyfactory");
            $this->company = $this->companyfactory->get_instance();
            $this->company->fetch($data["clientData"]["member_id"]);
            $data["category"] = $this->company->get_field("category");
        }
        $data["member_id"] = str_pad($data["clientData"]["member_id"], 8, "0", STR_PAD_LEFT);
        $data["name"] = str_pad($data["clientData"]["id"], 8, "0", STR_PAD_LEFT);
        $data["clientData"]["memberEditAction"] = $this->load->view("companies/get_links", $data, true);
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("client_money"));
        $data["subNavItems"][site_url("clients/client_details")] = $this->lang->line("public_info");
        $data["subNavItems"][site_url("clients/edit_client")] = $this->lang->line("other_details");
        $data["subNavItems"][site_url("clients/documents")] = $this->lang->line("related_documents");
        $data["subNavItems"][site_url("clients/partner_shares")] = $this->lang->line("partner_shares");
        $data["activeSubNavItem"] = $active;
        return $data;
    }
    public function account_statement($clientId)
    {
        $response = [];
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $response["accountRecords"] = $this->account->get_client_accounts_details($clientId);
        $response["nbOfAccounts"] = count($response["accountRecords"]);
        $response["accountRecords"] = $response["nbOfAccounts"] == 1 ? $response["accountRecords"][0] : $response["accountRecords"];
        $response["html"] = 1 < $response["nbOfAccounts"] ? $this->load->view("clients/account_statement", ["accounts" => $response["accountRecords"]], true) : "";
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function deposits()
    {
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("client_trust_funds"));
        if ($this->input->is_ajax_request()) {
            $response = $this->deposit->k_load_all_deposits($this->input->post("filter"), $this->input->post("sort"));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $payment_methods = $this->deposit->get("payment_method_values");
            $data["payment_methods"] = array_combine($payment_methods, [$this->lang->line("bank_transfer"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $data["operatorsBigText"] = $this->get_filter_operators("bigText");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/deposits", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("deposits/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function add_deposit()
    {
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
        $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
        $system_preferences = $this->session->userdata("systemPreferences");
        $trust_asset_accounts = NULL;
        if (isset($system_preferences["trustAssetAccount"])) {
            $trust_asset_accounts = unserialize($system_preferences["trustAssetAccount"]);
        }
        if (!$this->input->post(NULL)) {
            if ($trust_asset_accounts && is_array($trust_asset_accounts) && isset($trust_asset_accounts[$this->session->userdata("organizationID")])) {
                $this->load->model("exchange_rate");
                $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
                if (!empty($exchange_rates)) {
                    $data = [];
                    $data["title"] = $this->lang->line("add_deposit");
                    $data["rates"] = json_encode($exchange_rates);
                    $data["deposit"] = $this->deposit->get_fields() + $this->client_trust_accounts_relation->get_fields();
                    $data["deposit"]["client_name"] = "";
                    $data["deposit"]["description"] = $data["deposit"]["client_name"];
                    $data["deposit"]["deposited_on"] = $data["deposit"]["description"];
                    $data["trust_asset_account"] = $this->account->fetch_transaction_account($trust_asset_accounts[$this->session->userdata("organizationID")]);
                    $data["trust_asset_account"]["id"] = $trust_asset_accounts[$this->session->userdata("organizationID")];
                    $response["html"] = $this->load->view("deposits/form", $data + $this->load_common_deposits_data(), true);
                } else {
                    $response["result"] = false;
                }
            } else {
                $response["result"] = false;
                $response["message"] = $this->lang->line("no_trust_account");
            }
        } else {
            $validate = $this->validate_deposit_form();
            if ($validate["status"]) {
                $this->voucher_header->insert();
                $voucher_header_id = $this->voucher_header->get_field("id");
                $this->client_trust_accounts_relation->fetch(["client" => $this->input->post("client"), "organization_id" => $this->session->userdata("organizationID")]);
                if (!$this->client_trust_accounts_relation->get_field("trust_liability_account")) {
                    $this->client_trust_accounts_relation->set_field("trust_liability_account", $this->add_client_trust_liability_account());
                    $this->client_trust_accounts_relation->insert();
                }
                $this->deposit->set_field("voucher_header_id", $voucher_header_id);
                $this->deposit->set_field("client_trust_accounts_id", $this->client_trust_accounts_relation->get_field("id"));
                if ($this->deposit->insert()) {
                    $response["result"] = $this->add_voucher_details($voucher_header_id);
                }
            } else {
                $response["validation_errors"] = $validate["error"];
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_deposit($id, $voucher_header_id)
    {
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model("voucher_detail", "voucher_detailfactory");
        $this->voucher_detail = $this->voucher_detailfactory->get_instance();
        $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
        $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (!$this->input->post(NULL)) {
            if (!empty($exchange_rates)) {
                $data = [];
                $data["title"] = $this->lang->line("edit_deposit");
                $rates = $exchange_rates;
                $data["rates"] = json_encode($rates);
                $data["deposit"] = $this->deposit->fetch_record($id);
                $data["deposit"]["local_amount"] = $data["deposit"]["foreign_amount"] * $rates[$data["deposit"]["currency"]] / $rates[$this->session->userdata("organizationCurrencyID")];
                $data["trust_asset_account"] = $this->account->fetch_transaction_account($data["deposit"]["trust_asset_account"]);
                $data["trust_account_data"] = $this->deposit->load_client_trust_accounts($data["deposit"]["client"]);
                $response["html"] = $this->load->view("deposits/form", $data + $this->load_common_deposits_data(), true);
            } else {
                $response["result"] = false;
            }
        } else {
            $this->deposit->fetch($id);
            $validate = $this->validate_deposit_form();
            if ($validate["status"]) {
                $rates = $exchange_rates;
                $trust_account_data = $this->deposit->load_client_trust_accounts($this->input->post("client"));
                $old_amount = $this->deposit->get_field("foreign_amount") * $rates[$this->deposit->get_field("currency")] / $rates[$this->session->userdata("organizationCurrencyID")];
                $new_total_amount = $trust_account_data["total_credit"] * 1 - $old_amount * 1 + $this->input->post("local_amount");
                if ($trust_account_data["total_debit"] * 1 <= $new_total_amount) {
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                    $this->voucher_header->set_field("id", $voucher_header_id);
                    if ($this->voucher_header->update()) {
                        $this->client_trust_accounts_relation->reset_fields();
                        $this->client_trust_accounts_relation->fetch(["client" => $this->input->post("client"), "organization_id" => $this->session->userdata("organizationID")]);
                        if (!$this->client_trust_accounts_relation->get_field("trust_liability_account")) {
                            $this->client_trust_accounts_relation->set_field("trust_liability_account", $this->add_client_trust_liability_account());
                            $this->client_trust_accounts_relation->insert();
                        }
                        $this->deposit->set_field("voucher_header_id", $voucher_header_id);
                        $this->deposit->set_field("client_trust_accounts_id", $this->client_trust_accounts_relation->get_field("id"));
                        if ($this->deposit->update()) {
                            $response["result"] = $this->add_voucher_details($voucher_header_id);
                        }
                    }
                } else {
                    $response["validation_errors"]["foreign_amount"] = $this->lang->line("amount_less_than_paid");
                }
            } else {
                $response["validation_errors"] = $validate["error"];
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function validate_deposit_form()
    {
        $post_data = $this->input->post(NULL);
        array_walk($post_data, [$this, "sanitize_post"]);
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
        $this->voucher_header->set_field("refNum", $this->voucher_header->auto_generate_rf("DP"));
        $this->voucher_header->set_field("dated", $post_data["deposited_on"]);
        $this->voucher_header->set_field("voucherType", "DP");
        $this->voucher_header->set_field("description", $post_data["description"]);
        $voucher_validation = $this->voucher_header->get("validate");
        $voucher_validation["dated"]["message"] = sprintf($this->lang->line("required_date_rule"), $this->lang->line("deposited_on"));
        $this->voucher_header->set("validate", $voucher_validation);
        $this->client_trust_accounts_relation->reset_fields();
        $this->client_trust_accounts_relation->set_field("client", $post_data["client"]);
        $this->client_trust_accounts_relation->set_field("trust_asset_account", $post_data["trust_asset_account"]);
        $this->client_trust_accounts_relation->set_field("organization_id", $this->session->userdata("organizationID"));
        $this->deposit->set_field("foreign_amount", $post_data["foreign_amount"]);
        $this->deposit->set_field("currency", $post_data["currency"]);
        $this->deposit->set_field("payment_method", $post_data["payment_method"]);
        $lookup_validation_errors = $this->client_trust_accounts_relation->get_lookup_validation_errors($this->client_trust_accounts_relation->get("lookupInputsToValidate"), $post_data);
        if ($this->client_trust_accounts_relation->validate() && !$lookup_validation_errors && $this->deposit->validate() && $this->voucher_header->validate()) {
            $response["status"] = true;
        } else {
            $response["error"] = $this->client_trust_accounts_relation->get_validation_errors($lookup_validation_errors) + $this->deposit->get("validationErrors") + $this->voucher_header->get("validationErrors");
            $response["status"] = false;
        }
        return $response;
    }
    private function add_client_trust_liability_account()
    {
        $this->config->load("accounts_map", true);
        $accounts_map = $this->config->item("accounts_map");
        $account_type_id = $accounts_map["TrustLiability"]["type_id"];
        $max_numbers = $this->account->load_max_numbers_per_acc_type($account_type_id);
        $this->account->set_field("organization_id", $this->session->userdata("organizationID"));
        $this->account->set_field("currency_id", $this->session->userdata("organizationCurrencyID"));
        $this->account->set_field("account_type_id", $account_type_id);
        $this->account->set_field("number", isset($max_numbers[$account_type_id]) ? $max_numbers[$account_type_id] : 1);
        $this->account->set_field("name", $this->input->post("client_lookup") . " Trust Liability Account");
        $this->account->set_field("systemAccount", "no");
        $this->account->set_field("model_type", "internal");
        $this->account->set_field("model_name", "internal");
        $this->account->set_field("description", $this->lang->line("add_trust_liability_acc_description"));
        $this->account->set_field("show_in_dashboard", "1");
        $this->account->insert();
        return $this->account->get_field("id");
    }
    private function add_voucher_details($voucher_header_id)
    {
        if (empty($this->voucher_detail)) {
            $this->load->model("voucher_detail", "voucher_detailfactory");
            $this->voucher_detail = $this->voucher_detailfactory->get_instance();
        }
        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
        $this->voucher_detail->set_field("account_id", $this->input->post("trust_asset_account"));
        $this->voucher_detail->set_field("drCr", "D");
        $this->voucher_detail->set_field("local_amount", $this->input->post("local_amount"));
        $this->voucher_detail->set_field("foreign_amount", $this->input->post("local_amount"));
        $this->voucher_detail->set_field("description", "DP-" . $this->deposit->get_field("id"));
        if ($this->voucher_detail->insert()) {
            $this->voucher_detail->reset_fields();
            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
            $this->voucher_detail->set_field("account_id", $this->client_trust_accounts_relation->get_field("trust_liability_account"));
            $this->voucher_detail->set_field("drCr", "C");
            $this->voucher_detail->set_field("local_amount", $this->input->post("local_amount"));
            $this->voucher_detail->set_field("foreign_amount", $this->input->post("local_amount"));
            $this->voucher_detail->set_field("description", "DP-" . $this->deposit->get_field("id"));
            return $this->voucher_detail->insert();
        }
        return false;
    }
    private function load_common_deposits_data()
    {
        $data = [];
        $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["currencies"] = $this->country->load_money_currency_list("id", "currency_country");
        unset($data["currencies"][""]);
        $payment_method_values = $this->deposit->get("payment_method_values");
        array_unshift($payment_method_values, "");
        $data["payment_method_values"] = array_combine($payment_method_values, [$this->lang->line("choose_one"), $this->lang->line("bank_transfer"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque"), $this->lang->line("online_payment"), $this->lang->line("other")]);
        return $data;
    }
    public function delete_deposit()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("voucher_detail", "voucher_detailfactory");
        $this->voucher_detail = $this->voucher_detailfactory->get_instance();
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->load->model("voucher_related_case");
        $response = [];
        $id = $this->input->post("id");
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $rates = $exchange_rates;
        $deposit_data = $this->deposit->fetch_record($id);
        $deposit_data["local_amount"] = round($deposit_data["foreign_amount"] * $rates[$deposit_data["currency"]] / $rates[$this->session->userdata("organizationCurrencyID")], 2);
        $trust_account_data = $this->deposit->load_client_trust_accounts($deposit_data["client"]);
        $new_total_amount = $trust_account_data["total_credit"] * 1 - $deposit_data["local_amount"] * 1;
        $response["result"] = false;
        if ($trust_account_data["total_debit"] * 1 <= $new_total_amount) {
            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $deposit_data["voucher_header_id"]]]);
            if ($this->voucher_detail->delete(["where" => ["voucher_header_id", $deposit_data["voucher_header_id"]]]) && $this->deposit->delete(["where" => ["id", $id]])) {
                if ($this->voucher_header->delete($deposit_data["voucher_header_id"])) {
                    $response["result"] = true;
                    $response["feedback_message"] = ["type" => "success", "message" => sprintf($this->lang->line("delete_record_successfull"), $this->lang->line("deposit"))];
                } else {
                    $response["feedback_message"] = ["type" => "error", "message" => sprintf($this->lang->line("delete_record_failed"), $this->lang->line("deposit"))];
                }
            }
        } else {
            $response["feedback_message"] = ["type" => "warning", "message" => $this->lang->line("amount_less_than_paid")];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function export_deposits_to_excel()
    {
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $data = $this->deposit->k_load_all_deposits($this->input->post("filter"), $this->input->post("sort"));
        $filename = urlencode(str_replace(" ", "_", $this->lang->line("client_trust_funds")));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/deposits_list", $data);
        $this->load->view("excel/footer");
    }
    public function load_trust_data()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("common", "commonfactory");
        $this->common = $this->commonfactory->get_instance();
        $response["amount"] = $this->common->load_client_trust_accounts($this->input->get("client_id"), $this->session->userdata("organizationCurrencyID")) . " " . $this->session->userdata("organizationCurrency");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function open_balance($client_account_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $account_data = $this->account->fetch_account($client_account_id);
        $data["client_account_name"] = $account_data["name"] . " (" . $account_data["currencyCode"] . ")";
        $data["client_account_id"] = $client_account_id;
        $data["client_currency_id"] = $account_data["currency_id"];
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        if (!empty($exchange_rates)) {
            if ($this->input->post("form_action") == "submit") {
                $rates = $exchange_rates;
                $post_data = $this->input->post(NULL);
                array_walk($post_data, [$this, "sanitize_post"]);
                if (0 < $post_data["local_amount"] && is_numeric($post_data["local_amount"])) {
                    $this->load->model("voucher_header", "voucher_headerfactory");
                    $this->voucher_header = $this->voucher_headerfactory->get_instance();
                    $this->load->model("voucher_detail", "voucher_detailfactory");
                    $this->voucher_detail = $this->voucher_detailfactory->get_instance();
                    $this->voucher_header->set_fields($this->input->post(NULL));
                    $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->voucher_header->set_field("refNum", $this->voucher_header->auto_generate_rf("JV"));
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("dated"))));
                    $this->voucher_header->set_field("voucherType", "JV");
                    if ($this->voucher_header->insert()) {
                        $this->voucher_detail->reset_fields();
                        $this->voucher_detail->set_field("voucher_header_id", $this->voucher_header->get_field("id"));
                        $this->voucher_detail->set_field("account_id", $client_account_id);
                        $this->voucher_detail->set_field("drCr", "C");
                        if (isset($post_data["currency"])) {
                            if ($data["client_currency_id"] == $post_data["currency"]) {
                                $local_amount = (double) number_format($post_data["local_amount"] * 1, $allowed_decimal_format, ".", "");
                                $foreign_amount = isset($post_data["foreign_amount"]) ? (double) number_format($post_data["foreign_amount"] * 1, $allowed_decimal_format, ".", "") : $local_amount;
                            } else {
                                $local_amount = (double) number_format($post_data["local_amount"] * 1, $allowed_decimal_format, ".", "");
                                $foreign_amount = isset($post_data["foreign_amount"]) ? (double) number_format($post_data["foreign_amount"] / $rates[$data["client_currency_id"]] * 1, $allowed_decimal_format, ".", "") : $local_amount;
                            }
                        } else {
                            $local_amount = (double) number_format($post_data["local_amount"] * 1, $allowed_decimal_format, ".", "");
                            $foreign_amount = (double) number_format($post_data["local_amount"] * 1, $allowed_decimal_format, ".", "");
                        }
                        $this->voucher_detail->set_field("local_amount", $local_amount);
                        $this->voucher_detail->set_field("foreign_amount", $foreign_amount);
                        $this->voucher_detail->set_field("description", $post_data["description"]);
                        if (!$this->voucher_detail->insert()) {
                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $this->voucher_header->get_field("id")]]);
                            $this->voucher_header->delete($this->voucher_header->get_field("id"));
                            $response["error"] = $this->lang->line("transaction_not_saved");
                        } else {
                            $this->voucher_detail->reset_fields();
                            $this->voucher_detail->set_field("voucher_header_id", $this->voucher_header->get_field("id"));
                            $this->voucher_detail->set_field("account_id", $post_data["opening_balance_account"]);
                            $this->voucher_detail->set_field("drCr", "D");
                            $this->voucher_detail->set_field("local_amount", $post_data["local_amount"]);
                            $this->voucher_detail->set_field("foreign_amount", $post_data["local_amount"]);
                            $this->voucher_detail->set_field("description", $post_data["description"]);
                            if (!$this->voucher_detail->insert()) {
                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $this->voucher_header->get_field("id")]]);
                                $this->voucher_header->delete($this->voucher_header->get_field("id"));
                                $response["error"] = $this->lang->line("transaction_not_saved");
                            } else {
                                $this->account->fetch($client_account_id);
                                $this->account->set_field("has_open_balance", 1);
                                $this->account->update();
                                $response["result"] = true;
                                $this->session->set_userdata("open_balance_info_msg", 1);
                            }
                        }
                    }
                } else {
                    $response["error"] = $this->lang->line("decimal_allowed");
                }
            } else {
                $account_type = ["type_id" => "8", "typeType" => "'Equity'"];
                $equity_db_accounts = $this->account->load_common_accounts($account_type);
                $equity_accounts = [];
                foreach ($equity_db_accounts as $e_account) {
                    $equity_accounts[$e_account["id"]] = $e_account["name"];
                }
                $data["equity_accounts"] = $equity_accounts;
                $data["rates"] = json_encode($exchange_rates);
                $this->load->model("country", "countryfactory");
                $this->country = $this->countryfactory->get_instance();
                $data["currencies"] = $this->country->load_money_currency_list("id", "currency_country");
                unset($data["currencies"][""]);
                $response["html"] = $this->load->view("clients/open_balance", $data, true);
            }
        } else {
            $response["error"] = $this->lang->line("no_exchange_rate");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function documents($id)
    {
        $data = $this->get_client_tabs($id, site_url("clients/documents"));
        $data["id"] = $id;
        $related_comp_cont = $this->client->fetch_client_related_contact_company($id);
        $data["name"] = $related_comp_cont["name"];
        $data["member_id"] = $data["member_id"];
        $data["related_account_url"] = $this->load->view("companies/get_links", $data, true);
        $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
        $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
        $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
        $data["docs"]["module_container"] = "clients";
        $data["docs"]["directory"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR . $data["docs"]["module_container"];
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $data["deposits"] = $this->deposit->load_client_trust_accounts($id);
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        $this->includes("money/js/client_common_tabs", "js");
        $this->includes("money/js/client_documents", "js");
        $this->load->view("partial/header");
        $this->load->view("clients/documents", $data);
        $this->load->view("partial/footer");
    }
    public function upload_file()
    {
        $this->load->library("dms");
        $response = $this->dms->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc"]);
        if ($this->input->post("dragAndDrop")) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $html = "<html>\r\n                <head>\r\n                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n                    <script type=\"text/javascript\">\r\n                        if(window.top.uploadDocumentDone) window.top.uploadDocumentDone('" . $response["message"] . "', '" . ($response["status"] ? "success" : "error") . "');\r\n                    </script>\r\n                </head>\r\n            </html>";
            $this->output->set_content_type("text/html")->set_output($html);
        }
    }
    public function load_documents()
    {
        $this->load->library("dms");
        $response = $this->dms->load_documents(["module" => $this->input->get("module"), "module_record_id" => $this->input->get("module_record_id"), "lineage" => $this->input->get("lineage")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function delete_document()
    {
        $document_id = $this->input->post("document_id");
        $this->load->library("dms");
        $response = $this->dms->delete_document("client", $document_id);
        $response["data"] = ["modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->session->userdata("AUTH_user_id"), "modifier_full_name" => $this->session->userdata("AUTH_userProfileName")];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function download_file($file_id)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $this->load->library("dms");
        $this->dms->download_file("client", $file_id);
    }
    public function return_doc_thumbnail($id = 0, $name = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id) {
            $this->load->library("dms");
            $response = $this->dms->get_file_download_data("client", $id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($name ? $name : $id, $content);
            }
        }
    }
    private function financials($id)
    {
        $data = $this->get_client_tabs($id, site_url("clients/financials"));
        $data["id"] = $id;
        $related_comp_cont = $this->client->fetch_client_related_contact_company($id);
        $data["name"] = $related_comp_cont["name"];
        $data["member_id"] = $data["member_id"];
        $data["related_account_url"] = $this->load->view("companies/get_links", $data, true);
        $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
        $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
        $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
        $this->includes("money/js/client_common_tabs", "js");
        $this->includes("money/js/client_financials", "js");
        $this->load->view("partial/header");
        $this->load->view("clients/financials", $data);
        $this->load->view("partial/footer");
    }
    public function partner_shares($id)
    {
        $this->client->fetch($id);
        if ($this->input->post()) {
            if (!$this->input->is_ajax_request()) {
                redirect("clients/partner_shares/" . $id);
            }
            $response = [];
            if ($this->input->post("action") == "load_partners") {
                $this->load->model("client_partner_share");
                $response["partners_shares"] = $this->client_partner_share->load_partners_shares($id);
            } else {
                if ($this->input->post("action") == "save_partners") {
                    $this->load->model("client_partner_share");
                    $partners = $this->input->post("partners") ?? [];
                    $percentages = $this->input->post("percentages") ?? [];
                    $partners_shares = [];
                    foreach ($partners as $key => $partner_id) {
                        if (0 < $partner_id) {
                            $partners_shares[$key]["client_id"] = $id;
                            $partners_shares[$key]["account_id"] = $partner_id;
                            $partners_shares[$key]["percentage"] = $percentages[$key] ?? 0;
                        }
                    }
                    $response["status"] = $this->client_partner_share->save_partners_shares($id, $partners_shares);
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = $this->get_client_tabs($id, site_url("clients/partner_shares"));
            $data["id"] = $id;
            $related_comp_cont = $this->client->fetch_client_related_contact_company($id);
            $data["name"] = $related_comp_cont["name"];
            $data["member_id"] = $data["member_id"];
            $data["related_account_url"] = $this->load->view("companies/get_links", $data, true);
            $this->load->model("deposit", "depositfactory");
            $this->deposit = $this->depositfactory->get_instance();
            $data["deposits"] = $this->deposit->load_client_trust_accounts($id);
            $this->load->model("client_trust_accounts_relation", "client_trust_accounts_relationfactory");
            $this->client_trust_accounts_relation = $this->client_trust_accounts_relationfactory->get_instance();
            $data["trust_asset_accounts_per_client"] = $this->client_trust_accounts_relation->load_trust_asset_accounts_per_client();
            $this->includes("money/js/client_common_tabs", "js");
            $this->load->view("partial/header");
            $this->load->view("clients/partner_shares", $data);
            $this->load->view("partial/footer");
        }
    }
}

?>