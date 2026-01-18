<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Setup extends Money_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("settings"));
        $this->currentTopNavItem = "money";
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("money") . " | " . $this->lang->line("settings"));
        $this->load->model("money_preference");
        $organization_id = $this->session->userdata("organizationID");
        $moneyPreferences = $this->money_preference->get_key_groups();
        $data["activateTax"] = $moneyPreferences["ActivateTaxesinInvoices"]["TEnabled"];
        $data["activateDiscount"] = unserialize($moneyPreferences["ActivateDiscountinInvoices"]["DEnabled"])[$organization_id]["enabled"];
        $data["activateInvoiceDetailsFormat"] = unserialize($moneyPreferences["InvoiceValues"]["partnersCommissions"])[$organization_id];
        $data["isOpenDiscountPopup"] = empty($this->isOpenDiscountPopup) ? 0 : 1;
        $this->includes("money/js/setup", "js");
        $this->load->view("partial/header");
        $this->load->view("setup/index", $data);
        $this->load->view("partial/footer");
    }
    public function rate_between_money_currencies()
    {
        $is_new_organization = false;
        if ($this->input->post("organizationId") && $this->input->post("organizationId")) {
            $organization_id = $this->input->post("organizationId");
            $is_new_organization = true;
        } else {
            $organization_id = $this->session->userdata("organizationID");
            $organization_id = $this->organization->str_pad_left_organization_id($organization_id);
        }
        $this->load->model("exchange_rate");
        $is_json_response = $this->input->is_ajax_request();
        if ($this->input->post(NULL)) {
            $new_exchange_rates = [];
            foreach ($this->input->post("rates") as $key => $value) {
                array_push($new_exchange_rates, ["currency_id" => $this->input->post("currencyids")[$key], "organization_id" => $organization_id, "rate" => $value]);
            }
            $this->save_exchange_rates($new_exchange_rates, $is_json_response, $organization_id, $is_new_organization);
        } else {
            $this->load->model("system_preference");
            $system_preferences = $this->system_preference->get_values_by_group("MoneyCurrency");
            if (!isset($system_preferences["currencies"]) || empty($system_preferences["currencies"])) {
                $this->set_flashmessage("warning", $this->lang->line("first_you_have_to_set_money_currencies_and_your_default_currency"));
                redirect("money_preferences");
            } else {
                $this->view_exchange_rates($system_preferences, $organization_id, $is_json_response);
            }
        }
    }
    private function save_exchange_rates($new_exchange_rates, $is_json_response, $organization_id, $is_new_organization)
    {
        $success_responses = 0;
        if ($is_new_organization) {
            if ($this->exchange_rate->insert_batch($new_exchange_rates)) {
                $success_responses = count($new_exchange_rates);
            }
        } else {
            foreach ($new_exchange_rates as $exchange_rate) {
                if ($this->exchange_rate->fetch(["currency_id" => $exchange_rate["currency_id"], "organization_id" => $exchange_rate["organization_id"]])) {
                    $this->exchange_rate->set_field("rate", $exchange_rate["rate"]);
                    if ($this->exchange_rate->update()) {
                        $success_responses++;
                    }
                } else {
                    $this->exchange_rate->reset_fields();
                    $this->exchange_rate->set_fields($exchange_rate);
                    if ($this->exchange_rate->insert()) {
                        $success_responses++;
                        $this->exchange_rate->reset_fields();
                    }
                }
            }
        }
        if (count($new_exchange_rates) == $success_responses) {
            if ($is_json_response) {
                $response["rates"] = json_encode($this->exchange_rate->get_organization_exchange_rates($organization_id));
                $response["status"] = true;
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("success", $this->lang->line("save_rates_success"));
                redirect("setup");
            }
        } else {
            if ($is_json_response) {
                $response["status"] = false;
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("warning", $this->lang->line("save_rates_fail"));
                redirect("setup/rate_between_money_currencies");
            }
        }
    }
    private function view_exchange_rates($system_preferences, $organization_id, $is_json_response)
    {
        $data = [];
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["currencies"] = $this->country->load_all(["where" => ["id in (" . $system_preferences["currencies"] . ")"]]);
        $data["default_currency"] = $this->country->load($this->session->userdata("organizationCurrencyID"));
        $data["exchange_rates"] = $this->exchange_rate->get_organization_exchange_rates($organization_id);
        if ($is_json_response) {
            $response["html"] = $this->load->view("setup/rate_between_money_currencies", $data, true);
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->load->view("partial/header");
            $this->load->view("setup/rate_between_money_currencies", $data);
            $this->load->view("partial/footer");
        }
    }
    public function time_tracking_sales_account()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($this->input->post(NULL)) {
            $this->load->model("system_preference");
            $timeTrackingSalesAccount = [];
            if (isset($system_preferences["timeTrackingSalesAccount"])) {
                $timeTrackingSalesAccount = unserialize($system_preferences["timeTrackingSalesAccount"]);
            }
            $timeTrackingSalesAccount[$organization_id] = $this->input->post("sales_account");
            $dataSet = ["groupName" => "timeTracking", "keyName" => "timeTrackingSalesAccount", "keyValue" => serialize($timeTrackingSalesAccount)];
            if ($this->system_preference->set_value_by_key("timeTracking", "timeTrackingSalesAccount", serialize($timeTrackingSalesAccount))) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("setup");
                } else {
                    $response["salesAccount"] = json_encode($timeTrackingSalesAccount[$organization_id]);
                    $response["status"] = true;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            } else {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("warning", $this->lang->line("error"));
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        } else {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["sales_accounts"] = $this->account->load_accounts_per_organization("Income", true);
            $data["timeTrackingSalesAccount"] = [];
            if (!empty($organization_id)) {
                if (isset($system_preferences["timeTrackingSalesAccount"])) {
                    $timeTrackingSalesAccount = unserialize($system_preferences["timeTrackingSalesAccount"]);
                }
                if (isset($timeTrackingSalesAccount[$organization_id])) {
                    $data["timeTrackingSalesAccount"] = $timeTrackingSalesAccount[$organization_id];
                }
            }
            if (!$this->input->is_ajax_request()) {
                $this->load->view("partial/header");
                $this->load->view("setup/time_tracking_sales_account", $data);
                $this->load->view("partial/footer");
            } else {
                $response["html"] = $this->load->view("setup/time_tracking_sales_account", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function invoice_number_prefix()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($this->input->post(NULL)) {
            $this->load->model("system_preference");
            $invoiceNumberPrefix = [];
            if (isset($system_preferences["invoiceNumberPrefix"])) {
                $invoiceNumberPrefix = unserialize($system_preferences["invoiceNumberPrefix"]);
            }
            $invoiceNumberPrefix[$organization_id] = $this->input->post("prefix");
            $dataSet = ["groupName" => "InvoiceValues", "keyName" => "invoiceNumberPrefix", "keyValue" => serialize($invoiceNumberPrefix)];
            if ($this->system_preference->set_value_by_key("InvoiceValues", "invoiceNumberPrefix", serialize($invoiceNumberPrefix))) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("setup");
                } else {
                    $response["prefix"] = json_encode($invoiceNumberPrefix[$organization_id]);
                    $response["status"] = true;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            } else {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("warning", $this->lang->line("error"));
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        } else {
            $data["invoiceNumberPrefix"] = "";
            if (!empty($organization_id)) {
                if (isset($system_preferences["invoiceNumberPrefix"])) {
                    $invoiceNumberPrefix = unserialize($system_preferences["invoiceNumberPrefix"]);
                }
                if (isset($invoiceNumberPrefix[$organization_id])) {
                    $data["invoiceNumberPrefix"] = $invoiceNumberPrefix[$organization_id];
                }
            }
            if (!$this->input->is_ajax_request()) {
                $this->load->view("partial/header");
                $this->load->view("setup/invoice_number_prefix", $data);
                $this->load->view("partial/footer");
            } else {
                $response["html"] = $this->load->view("setup/invoice_number_prefix", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function partners_commissions()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($this->input->post(NULL)) {
            $this->load->model("system_preference");
            $partnersCommissions = [];
            if (isset($system_preferences["partnersCommissions"])) {
                $partnersCommissions = unserialize($system_preferences["partnersCommissions"]);
            }
            $partnersCommissions[$organization_id] = $this->input->post("partnersCommissions");
            $dataSet = ["groupName" => "InvoiceValues", "keyName" => "partnersCommissions", "keyValue" => serialize($partnersCommissions)];
            if ($this->system_preference->set_value_by_key("InvoiceValues", "partnersCommissions", serialize($partnersCommissions))) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("setup");
                } else {
                    $response["partnersCommissions"] = json_encode($partnersCommissions[$organization_id]);
                    $response["status"] = true;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            } else {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("warning", $this->lang->line("error"));
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        } else {
            $data["partnersCommissions"] = "";
            if (!empty($organization_id)) {
                if (isset($system_preferences["partnersCommissions"])) {
                    $partnersCommissions = unserialize($system_preferences["partnersCommissions"]);
                }
                if (isset($partnersCommissions[$organization_id])) {
                    $data["partnersCommissions"] = $partnersCommissions[$organization_id];
                }
            }
            if (!$this->input->is_ajax_request()) {
                $this->load->view("partial/header");
                $this->load->view("setup/partners_commissions", $data);
                $this->load->view("partial/footer");
            } else {
                $response["html"] = $this->load->view("setup/partners_commissions", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function global_partner_shares_account()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        if ($this->input->post(NULL)) {
            $this->load->model("system_preference");
            $systemCommissionAccount = [];
            if (isset($system_preferences["systemCommissionAccount"])) {
                $systemCommissionAccount = unserialize($system_preferences["systemCommissionAccount"]);
            }
            $systemCommissionAccount[$organization_id] = $this->input->post("asset_account");
            $dataSet = ["groupName" => "InvoiceValues", "keyName" => "systemCommissionAccount", "keyValue" => serialize($systemCommissionAccount)];
            if ($this->system_preference->set_value_by_key("InvoiceValues", "systemCommissionAccount", serialize($systemCommissionAccount))) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("setup");
                } else {
                    $response["incomeAccount"] = json_encode($systemCommissionAccount[$organization_id]);
                    $response["status"] = true;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            } else {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("warning", $this->lang->line("error"));
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                }
            }
        } else {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["asset_accounts"] = $this->account->load_accounts_per_organization("expenses_and_other_expenses", true);
            $data["systemCommissionAccount"] = [];
            if (!empty($organization_id)) {
                if (isset($system_preferences["systemCommissionAccount"])) {
                    $systemCommissionAccount = unserialize($system_preferences["systemCommissionAccount"]);
                }
                if (isset($systemCommissionAccount[$organization_id])) {
                    $data["systemCommissionAccount"] = $systemCommissionAccount[$organization_id];
                }
            }
            if (!$this->input->is_ajax_request()) {
                $this->load->view("partial/header");
                $this->load->view("setup/global_partner_shares_account", $data);
                $this->load->view("partial/footer");
            } else {
                $response["html"] = $this->load->view("setup/global_partner_shares_account", $data, true);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function set_trust_account()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        $accounts = [];
        if (!empty($organization_id) && isset($system_preferences["trustAssetAccount"])) {
            $accounts = unserialize($system_preferences["trustAssetAccount"]);
        }
        if ($this->input->post(NULL)) {
            $response["result"] = false;
            $this->load->model("system_preference");
            $post_acc = trim($this->input->post("account", true));
            if ($post_acc) {
                $accounts[$organization_id] = $post_acc;
                if ($this->system_preference->set_value_by_key("trustAccount", "trustAssetAccount", serialize($accounts))) {
                    $response["result"] = true;
                }
            } else {
                $response["validation_errors"]["account"] = $this->lang->line("cannot_be_blank_rule");
            }
        } else {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->config->load("accounts_map", true);
            $accounts_map = $this->config->item("accounts_map");
            $data["accounts"] = $this->account->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["account_type_id", $accounts_map["TrustAsset"]["type_id"]]]], ["firstLine" => ["" => $this->lang->line("choose_one")]]);
            $data["account_id"] = [];
            if (isset($accounts[$organization_id])) {
                $data["account_id"] = $accounts[$organization_id];
            }
            $data["title"] = $this->lang->line("trust_account");
            $response["html"] = $this->load->view("setup/global_accounts_for_entities", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function configure_invoice_discount()
    {
        if (!$this->input->is_ajax_request()) {
            $this->isOpenDiscountPopup = 1;
            return $this->index();
        }
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->session->userdata("systemPreferences");
        $accounts = [];
        if (!empty($organization_id) && isset($system_preferences["DEnabled"])) {
            $accounts = unserialize($system_preferences["DEnabled"]);
        }
        $this->load->model("organization", "organizationfactory");
        $this->organization = $this->organizationfactory->get_instance();
        $e_invoicing = $this->organization->check_if_einvoice_active($organization_id);
        if ($this->input->post(NULL)) {
            $response["result"] = false;
            $this->load->model("system_preference");
            $enabled = trim($this->input->post("enabled", true));
            $discount_account_id = trim($this->input->post("account_id", true));
            if ($enabled !== "no" && empty($discount_account_id)) {
                $response["validation_errors"]["account"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $accounts[$organization_id] = ["enabled" => $enabled, "account_id" => $discount_account_id];
                if ($this->system_preference->set_value_by_key("ActivateDiscountinInvoices", "DEnabled", serialize($accounts))) {
                    $response["result"] = true;
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["income_accounts"] = $this->account->load_accounts_per_organization("Income", true);
            if ($e_invoicing) {
                $data["discount_choices"] = ["no" => $this->lang->line("no"), "item_level" => $this->lang->line("item_level"), "invoice_level_after_tax" => $this->lang->line("invoice_level_after_tax"), "both_item_after_level" => $this->lang->line("both_item_after_level")];
            } else {
                $data["discount_choices"] = ["no" => $this->lang->line("no"), "item_level" => $this->lang->line("item_level"), $this->lang->line("invoice_level") => ["invoice_level_after_tax" => $this->lang->line("invoice_level_after_tax"), "invoice_level_before_tax" => $this->lang->line("invoice_level_before_tax")], $this->lang->line("both_levels") => ["both_item_after_level" => $this->lang->line("both_item_after_level"), "both_item_before_level" => $this->lang->line("both_item_before_level")]];
            }
            $data["account_id"] = "";
            $data["enabled"] = "";
            if (isset($accounts[$organization_id])) {
                $data["enabled"] = $accounts[$organization_id]["enabled"];
                $data["account_id"] = $accounts[$organization_id]["account_id"];
            }
            $response["html"] = $this->load->view("setup/global_discount_account", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function credit_note_number_prefix()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->system_preference->get_values();
        if ($this->input->post(NULL)) {
            $credit_note_number_prefix = [];
            if (isset($system_preferences["creditNoteNumberPrefix"])) {
                $credit_note_number_prefix = unserialize($system_preferences["creditNoteNumberPrefix"]);
            }
            $credit_note_number_prefix[$organization_id] = $this->input->post("prefix");
            if ($this->system_preference->set_value_by_key("CreditNoteValues", "creditNoteNumberPrefix", serialize($credit_note_number_prefix))) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("setup");
            } else {
                $this->set_flashmessage("warning", $this->lang->line("error"));
            }
        } else {
            $data["credit_note_number_prefix"] = "";
            if (!empty($organization_id)) {
                if (isset($system_preferences["creditNoteNumberPrefix"])) {
                    $credit_note_number_prefix = unserialize($system_preferences["creditNoteNumberPrefix"]);
                }
                if (isset($credit_note_number_prefix[$organization_id])) {
                    $data["credit_note_number_prefix"] = $credit_note_number_prefix[$organization_id];
                }
            }
            $this->load->view("partial/header");
            $this->load->view("setup/credit_note_number_prefix", $data);
            $this->load->view("partial/footer");
        }
    }
    public function debit_note_number_prefix()
    {
        $organization_id = $this->session->userdata("organizationID");
        $system_preferences = $this->system_preference->get_values();
        if ($this->input->post(NULL)) {
            $debit_note_number_prefix = [];
            if (isset($system_preferences["debitNoteNumberPrefix"])) {
                $debit_note_number_prefix = unserialize($system_preferences["debitNoteNumberPrefix"]);
            }
            $this->load->model("organization", "organizationfactory");
            $this->organization = $this->organizationfactory->get_instance();
            $e_invoicing = $this->organization->check_if_einvoice_active($organization_id);
            if (!empty($debit_note_number_prefix[$organization_id]) && $e_invoicing) {
                if (!$this->input->is_ajax_request()) {
                    $this->set_flashmessage("warning", $this->lang->line("deny_prefix_change_e_invoicing"));
                    redirect("setup");
                } else {
                    $response["status"] = false;
                    $this->output->set_content_type("application/json");
                    $this->output->set_output(json_encode($response));
                    return NULL;
                }
            }
            $debit_note_number_prefix[$organization_id] = $this->input->post("prefix");
            if ($this->system_preference->set_value_by_key("DebitNoteValues", "debitNoteNumberPrefix", serialize($debit_note_number_prefix))) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("setup");
            } else {
                $this->set_flashmessage("warning", $this->lang->line("error"));
            }
        } else {
            $data["debit_note_number_prefix"] = "";
            if (!empty($organization_id) && isset($system_preferences["debitNoteNumberPrefix"])) {
                $debit_note_number_prefix = unserialize($system_preferences["debitNoteNumberPrefix"]);
                if (isset($debit_note_number_prefix[$organization_id])) {
                    $data["debit_note_number_prefix"] = $debit_note_number_prefix[$organization_id];
                }
            }
            $this->load->view("partial/header");
            $this->load->view("setup/debit_note_number_prefix", $data);
            $this->load->view("partial/footer");
        }
    }
}

?>