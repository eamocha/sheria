<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Vouchersbreaksavefunction extends Money_controller
{
    public $Voucher_Header;
    public $Voucher_Detail;
    public $sumOtherServicesFees = 0;
    public $sumItemsFees = 0;
    public $totalHours = 0;
    public $sumLegalFees = 0;
    public $quote_enabled_editing_statuses = ["open"];
    public function __construct()
    {
        parent::__construct();
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->load->model("voucher_detail", "voucher_detailfactory");
        $this->load->model("voucher_related_case");
        $this->voucher_detail = $this->voucher_detailfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("vouchers") . " | " . $this->lang->line("money"));
        $this->currentTopNavItem = "money";
        $this->load->library("dms");
        $this->load->helper("is_rtl");
    }
    private function auto_generate_rf($voucher_type = "")
    {
        return $this->voucher_header->auto_generate_rf($voucher_type);
    }
    private function validate_voucher($id, $return_voucher = false)
    {
        $voucher_row = $this->voucher_header->load(["where" => ["id =" . $id . " and organization_id = " . $this->session->userdata("organizationID")]]);
        if ($voucher_row) {
            return $return_voucher ? $voucher_row : true;
        }
        return false;
    }
    public function journal_add()
    {
        $this->journal_save(0);
    }
    public function journal_edit($id = 0)
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/journals_list");
        }
        $this->journal_save($id);
    }
    private function journal_save($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("journals") . " | " . $this->lang->line("money"));
        $data = [];
        if ($this->input->post(NULL)) {
            $this->validate_current_organization($this->input->post("organization_id"), "journals_list");
            if ($id != 0) {
                if (!$this->validate_voucher($id)) {
                    redirect("vouchers/journals_list");
                }
                $this->voucher_detail->delete(["where" => ["voucher_header_id", $id]]);
                $this->voucher_header->fetch($id);
                $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                $this->voucher_header->set_field("description", $this->input->post("description"));
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("dated"))));
                if ($this->voucher_header->update()) {
                    if ($this->save_journal_details($this->input->post(NULL), $this->voucher_header->get_field("id"))) {
                        $result = true;
                    } else {
                        $this->voucher_header->delete($this->voucher_header->get_field("id"));
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            } else {
                $this->voucher_header->set_fields($this->input->post(NULL));
                $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                $this->voucher_header->set_field("refNum", $this->auto_generate_rf("JV"));
                $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("dated"))));
                $this->voucher_header->set_field("voucherType", "JV");
                if ($this->voucher_header->insert()) {
                    if ($this->save_journal_details($this->input->post(NULL), $this->voucher_header->get_field("id"))) {
                        $result = true;
                    } else {
                        $result = false;
                    }
                } else {
                    $result = false;
                }
            }
            if ($result) {
                $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("journal")));
                redirect("vouchers/journals_list/");
            }
        }
        if ($id != 0) {
            $data["voucher"] = $this->voucher_header->fetch_voucher_details($id);
            if (empty($data["voucher"])) {
                redirect("vouchers/journals_list");
            }
        }
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $data["accounts"] = $this->account->load_accounts_per_organization();
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (!empty($exchange_rates)) {
            $data["rates"] = json_encode($exchange_rates);
            $this->includes("money/js/journal_form", "js");
            $this->load->view("journals/form", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    public function journals_list()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("journals") . " | " . $this->lang->line("money"));
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->voucher_header->k_load_all_journals($filter, $sortable);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/journals", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("journals/index", $data);
            $this->load->view("partial/footer");
        }
    }
    private function save_journal_details($journal_data, $voucher_header_id)
    {
        foreach ($journal_data["accounts"] as $key => $val) {
            $this->voucher_detail->reset_fields();
            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
            $this->voucher_detail->set_field("account_id", $val);
            $this->voucher_detail->set_field("drCr", $journal_data["voucherType"][$key]);
            $this->voucher_detail->set_field("local_amount", $journal_data["localAmount"][$key]);
            $this->voucher_detail->set_field("foreign_amount", $journal_data["foreignAmount"][$key]);
            $this->voucher_detail->set_field("description", $journal_data["desc"][$key]);
            if (!$this->voucher_detail->insert()) {
                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                $this->voucher_header->delete($voucher_header_id);
                $result = false;
                return $result;
            }
            $result = true;
        }
    }
    public function journal_print($voucherHeaderId)
    {
        if (0 < $voucherHeaderId && !$this->validate_voucher($voucherHeaderId)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/journals_list");
        }
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $data = [];
        $data["journalHeader"] = $this->voucher_header->load_journal_header($voucherHeaderId);
        if (false === $data["journalHeader"]) {
            redirect(app_url("", "money"));
        }
        $data["journalDetails"] = $this->voucher_detail->load_details_with_accounts($voucherHeaderId);
        $data["journalHeader"]["dated"] = date("F d, Y", strtotime($data["journalHeader"]["dated"]));
        $data["baseCurrency"] = $this->session->userdata("organizationCurrency");
        $this->load->view("partial/header");
        $this->load->view("journals/print", $data);
        $this->load->view("partial/footer");
    }
    public function journal_delete()
    {
        if ($this->input->is_ajax_request()) {
            $voucher_id = $this->input->post("voucherID");
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model(["bill_payment", "bill_payment_bill"]);
            $result = false;
            if ($this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_id]])) {
                $result = $this->voucher_header->delete($voucher_id);
            } else {
                $result = false;
            }
            if ($result) {
                $response["status"] = 101;
            } else {
                $response["status"] = 202;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            redirect("vouchers/bill_payments_made");
        }
    }
    public function bill_add()
    {
        $this->bill_save(0);
    }
    public function bill_edit($id = 0)
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/bills_list");
        }
        $this->bill_save($id);
    }
    private function validate_current_organization($organization_id = 0, $redirection_url = "")
    {
        if (0 < $organization_id && $organization_id != $this->session->userdata("organizationID")) {
            $this->set_flashmessage("warning", $this->lang->line("changing_entity_notification"));
            redirect("vouchers/" . $redirection_url);
        }
    }
    private function bill_save($id = 0, $tax = "")
    {
        $this->load->helper("Text");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("kendoui/js/kendo.web.min", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("bills") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        $this->load->model(["money_preference"]);
        $activateTax = $this->money_preference->get_key_groups();
        if ($tax != "") {
            if ($tax == 0) {
                $this->set_flashmessage("information", sprintf($this->lang->line("tasks_converted_to_hide"), $this->lang->line("bill")));
            } else {
                $this->set_flashmessage("information", sprintf($this->lang->line("tasks_converted_to_unhide"), $this->lang->line("bill")));
            }
            $data["activateTax"] = $tax;
        } else {
            $data["activateTax"] = $activateTax["ActivateTaxesinInvoices"]["TEnabled"];
        }
        $this->load->model("supplier_tax", "supplier_taxfactory");
        $this->supplier_tax = $this->supplier_taxfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $data["taxes"] = $this->supplier_tax->get_supplier_taxes();
        $data["bill"] = ["id" => "", "organization_id" => "", "supplierAccountId" => "", "supplierAccountName" => "", "supplierAccountCurrency" => "", "client_id" => "", "clientName" => "", "case_id" => "", "case_subject" => "", "referenceNum" => "", "dated" => "", "description" => "", "sub_total" => 0, "total_tax" => 0, "total" => 0, "attachment" => ""];
        if ($this->input->post(NULL)) {
            $this->validate_current_organization($this->input->post("organization_id"), "bills_list");
            $uploaded = false;
            $result = false;
            if ($id != 0) {
                $this->load->model("bill_details", "bill_detailsfactory");
                $this->bill_details = $this->bill_detailsfactory->get_instance();
                $this->bill_header->fetch($this->input->post("id"));
                if ($this->bill_header->get_field("voucher_header_id") != $id) {
                    redirect("vouchers/bills_list");
                } else {
                    $this->bill_details->delete(["where" => ["bill_header_id", $this->bill_header->get_field("id")]]);
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $id]]);
                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $id]]);
                    $this->voucher_header->fetch($id);
                    $_POST["voucher_header_id"] = $id;
                    $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("dated"))));
                    $this->voucher_header->set_field("voucherType", "BI");
                    $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                    $this->voucher_header->set_field("description", $this->input->post("description"));
                    if ($this->voucher_header->update()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        if ($this->input->post("case_id")) {
                            $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                            $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                            $this->voucher_related_case->insert();
                        }
                        $client_id = $this->input->post("client_id");
                        if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                            $client_added_to_case_message = "<li>" . $this->lang->line("client_added_to_case") . "</li>";
                        }
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("supplier_id"));
                        $this->voucher_detail->set_field("drCr", "C");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("total"));
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("total") / $this->input->post("rate") * 1);
                        $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                        if ($this->voucher_detail->insert()) {
                            $this->bill_header->set_field("account_id", $this->input->post("supplier_id"));
                            $this->bill_header->set_field("dueDate", date("Y-m-d H:i", strtotime($this->input->post("dueDate"))));
                            $this->bill_header->set_field("total", $this->input->post("total"));
                            $this->bill_header->set_field("client_id", $client_id);
                            if ($this->bill_header->update()) {
                                $bill_header_id = $this->bill_header->get_field("id");
                                $this->set_bill_status($bill_header_id);
                                foreach ($this->input->post("accounts") as $key => $val) {
                                    $this->bill_details->reset_fields();
                                    $this->bill_details->set_field("bill_header_id", $bill_header_id);
                                    $this->bill_details->set_field("account_id", $val);
                                    $this->bill_details->set_field("description", $this->input->post("desc")[$key]);
                                    $this->bill_details->set_field("quantity", $this->input->post("quantity")[$key]);
                                    $this->bill_details->set_field("price", $this->input->post("price")[$key]);
                                    $this->bill_details->set_field("basePrice", $this->input->post("basePrice")[$key]);
                                    $this->bill_details->set_field("tax_id", empty($this->input->post("taxIds")[$key]) ? NULL : $this->input->post("taxIds")[$key]);
                                    $this->bill_details->set_field("percentage", empty($this->input->post("percentage")[$key]) ? NULL : $this->input->post("percentage")[$key]);
                                    if (!$this->bill_details->insert()) {
                                        $this->bill_details->delete(["where" => ["bill_header_id", $bill_header_id]]);
                                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                        $result = false;
                                        if ($result) {
                                            $grouped_accounts = $this->bill_details->load_grouped_accounts($bill_header_id);
                                            foreach ($grouped_accounts as $key => $val) {
                                                $val["local_amount"] = $val["quantity"] * $val["basePrice"];
                                                $val["foreign_amount"] = $val["quantity"] * $val["price"];
                                                $this->voucher_detail->reset_fields();
                                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                $this->voucher_detail->set_field("account_id", $val["account_id"]);
                                                $this->voucher_detail->set_field("drCr", "D");
                                                $this->voucher_detail->set_field("local_amount", $val["local_amount"]);
                                                $this->voucher_detail->set_field("foreign_amount", $val["foreign_amount"]);
                                                $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                                                if ($this->voucher_detail->insert() && !empty($grouped_accounts[$key]["tax_id"])) {
                                                    $tax_account = $this->supplier_tax->get_supplier_taxes_account($grouped_accounts[$key]["tax_id"]);
                                                    $this->voucher_detail->reset_fields();
                                                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                    $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                                                    $this->voucher_detail->set_field("drCr", "D");
                                                    $this->voucher_detail->set_field("local_amount", $val["local_amount"] * $grouped_accounts[$key]["percentage"] / 100);
                                                    $this->voucher_detail->set_field("foreign_amount", $val["foreign_amount"] * $grouped_accounts[$key]["percentage"] / 100);
                                                    $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                                                    $this->voucher_detail->insert();
                                                }
                                            }
                                        }
                                    } else {
                                        $result = true;
                                    }
                                }
                            }
                        } else {
                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                }
            } else {
                if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                    $client_added_to_case_message = "<li>" . $this->lang->line("client_added_to_case") . "</li>";
                }
                $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                $this->voucher_header->set_field("refNum", $this->auto_generate_rf("BI"));
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("dated"))));
                $this->voucher_header->set_field("voucherType", "BI");
                $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                $this->voucher_header->set_field("description", $this->input->post("description"));
                if ($uploaded) {
                    $upload_data = $this->upload->data();
                    $this->voucher_header->set_field("attachment", $upload_data["file_name"]);
                }
                if ($this->voucher_header->insert()) {
                    $voucher_header_id = $this->voucher_header->get_field("id");
                    if ($this->input->post("case_id")) {
                        $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                        $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_related_case->insert();
                    }
                    $_POST["voucher_header_id"] = $voucher_header_id;
                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                    $this->voucher_detail->set_field("account_id", $this->input->post("supplier_id"));
                    $this->voucher_detail->set_field("drCr", "C");
                    $this->voucher_detail->set_field("local_amount", $this->input->post("total"));
                    $this->voucher_detail->set_field("foreign_amount", $this->input->post("total") / $this->input->post("rate") * 1);
                    if ($this->voucher_detail->insert()) {
                        $this->load->model("bill_header", "bill_headerfactory");
                        $this->bill_header = $this->bill_headerfactory->get_instance();
                        $this->load->model("bill_details", "bill_detailsfactory");
                        $this->bill_details = $this->bill_detailsfactory->get_instance();
                        $this->bill_header->set_field("voucher_header_id", $voucher_header_id);
                        $this->bill_header->set_field("account_id", $this->input->post("supplier_id"));
                        $this->bill_header->set_field("dueDate", date("Y-m-d H:i", strtotime($this->input->post("dueDate"))));
                        $this->bill_header->set_field("total", $this->input->post("total"));
                        $this->bill_header->set_field("displayTax", $data["activateTax"]);
                        $this->bill_header->set_field("status", "open");
                        $this->bill_header->set_field("client_id", $this->input->post("client_id"));
                        if ($this->bill_header->insert()) {
                            $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                            $this->voucher_detail->update();
                            $bill_header_id = $this->bill_header->get_field("id");
                            foreach ($this->input->post("accounts") as $key => $val) {
                                $this->bill_details->reset_fields();
                                $this->bill_details->set_field("bill_header_id", $bill_header_id);
                                $this->bill_details->set_field("account_id", $val);
                                $this->bill_details->set_field("description", $this->input->post("desc")[$key]);
                                $this->bill_details->set_field("quantity", $this->input->post("quantity")[$key]);
                                $this->bill_details->set_field("price", $this->input->post("price")[$key]);
                                $this->bill_details->set_field("basePrice", $this->input->post("basePrice")[$key]);
                                $this->bill_details->set_field("tax_id", empty($this->input->post("taxIds")[$key]) ? NULL : $this->input->post("taxIds")[$key]);
                                $this->bill_details->set_field("percentage", empty($this->input->post("percentage")[$key]) ? NULL : $this->input->post("percentage")[$key]);
                                if (!$this->bill_details->insert()) {
                                    $this->bill_details->delete(["where" => ["bill_header_id", $bill_header_id]]);
                                    $this->bill_header->delete($this->bill_header->get_field("id"));
                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_header->delete($this->voucher_header->get_field("id"));
                                    $result = false;
                                    if ($result) {
                                        $grouped_accounts = $this->bill_details->load_grouped_accounts($bill_header_id);
                                        foreach ($grouped_accounts as $key => $val) {
                                            $val["local_amount"] = $val["quantity"] * $val["basePrice"];
                                            $val["foreign_amount"] = $val["quantity"] * $val["price"];
                                            $this->voucher_detail->reset_fields();
                                            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                            $this->voucher_detail->set_field("account_id", $val["account_id"]);
                                            $this->voucher_detail->set_field("drCr", "D");
                                            $this->voucher_detail->set_field("local_amount", $val["local_amount"]);
                                            $this->voucher_detail->set_field("foreign_amount", $val["foreign_amount"]);
                                            $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                                            if ($this->voucher_detail->insert() && !empty($grouped_accounts[$key]["tax_id"])) {
                                                $tax_account = $this->supplier_tax->get_supplier_taxes_account($grouped_accounts[$key]["tax_id"]);
                                                $this->voucher_detail->reset_fields();
                                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                                $this->voucher_detail->set_field("account_id", $tax_account["account_id"]);
                                                $this->voucher_detail->set_field("drCr", "D");
                                                $this->voucher_detail->set_field("local_amount", $val["local_amount"] * $grouped_accounts[$key]["percentage"] / 100);
                                                $this->voucher_detail->set_field("foreign_amount", $val["foreign_amount"] * $grouped_accounts[$key]["percentage"] / 100);
                                                $this->voucher_detail->set_field("description", "BIL-" . $this->bill_header->get_field("id"));
                                                $this->voucher_detail->insert();
                                            }
                                        }
                                    }
                                } else {
                                    $result = true;
                                }
                            }
                        }
                    } else {
                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                        $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                        $this->voucher_header->delete($this->voucher_header->get_field("id"));
                        $result = false;
                    }
                    if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                        $upload_response = $this->dms->upload_file(["module" => "BI", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                    }
                } else {
                    $result = false;
                }
            }
            if ($result) {
                $reminder = ["remindDate" => $this->input->post("dueDate"), "related_object" => $this->bill_header->get("_table")];
                $reminder["summary"] = sprintf($this->lang->line("bill_notification_message"), $this->lang->line("bill"), $this->input->post("dueDate"), $this->input->post("supplier"));
                $this->notify_me_before_due_date($voucher_header_id, $reminder);
                $bill_saved = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("bill"));
                $this->set_flashmessage("success", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $bill_saved . "</li>" : $bill_saved);
                redirect("vouchers/bills_list/");
            }
        }
        $data["clients_do_not_match"] = false;
        if ($id != 0) {
            $data["bill"] = $this->voucher_header->fetch_bill_voucher($id);
            if ($data["bill"]["case_id"]) {
                $data["case_client"] = $this->legal_case->get_case_client($data["bill"]["case_id"]);
            }
            $this->bill_header->fetch(["voucher_header_id" => $id]);
            $data["activateTax"] = $this->bill_header->get_field("displayTax") * 1;
            if (!empty($data["bill"])) {
                $this->load->model("bill_details", "bill_detailsfactory");
                $this->bill_details = $this->bill_detailsfactory->get_instance();
                $data["bill_details"] = $this->bill_details->fetch_bill_details($data["bill"]["id"]);
            } else {
                redirect("vouchers/bill_add");
            }
        }
        if (0 < $id) {
            $active = site_url("vouchers/bill_edit/");
            $data["clients_do_not_match"] = $this->check_case_client_match_bill_client($data);
        } else {
            $active = site_url("vouchers/bill_add");
        }
        $data["tabsNLogs"] = $this->_get_bill_tabs_view_vars($id, $active);
        $data["id"] = $id;
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $data["accounts"] = $this->account->load_accounts_per_organization("Expenses");
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (!empty($exchange_rates)) {
            $data["rates"] = $exchange_rates;
            if ($id && !empty($data["bill"]["id"])) {
                $data["bill"]["exchangeRate"] = $data["rates"][$data["bill"]["currency_id"]];
            }
            $data["rates"] = json_encode($data["rates"]);
            $data = array_merge($data, $this->return_notify_before_data($id, $this->bill_header->get("_table")));
            $this->includes("money/js/bill_form", "js");
            $this->load->view("bills/form", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    private function _get_bill_tabs_view_vars(&$id, $active = "")
    {
        $data["subNavItems"] = [];
        $data["activeSubNavItem"] = $active;
        if ($id) {
            $data["subNavItems"][site_url("vouchers/bill_edit/")] = $this->lang->line("public_info");
            $data["subNavItems"][site_url("vouchers/bill_payments_made/")] = $this->lang->line("payments_made");
            $data["subNavItems"][site_url("vouchers/bill_related_documents/")] = $this->lang->line("related_documents");
            return $data;
        }
        $data["subNavItems"][site_url("vouchers/bill_add")] = $this->lang->line("bill");
        return $data;
    }
    private function _get_expense_tabs_view_vars(&$id, $active = "", $bulk = false)
    {
        $data["subNavItems"] = [];
        $data["activeSubNavItem"] = $active;
        $data["id"] = $id;
        if ($id) {
            $data["subNavItems"][site_url("vouchers/expense_edit/")] = $this->lang->line("public_info");
            $data["subNavItems"][site_url("vouchers/expense_related_documents/")] = $this->lang->line("related_documents");
            return $data;
        }
        if (!$bulk) {
            $data["subNavItems"][site_url("vouchers/expense_add")] = $this->lang->line("Expense");
        } else {
            $data["subNavItems"][site_url("vouchers/expenses_add_bulk")] = $this->lang->line("bulk_expenses");
        }
        return $data;
    }
    public function bills_list($supplierId = 0, $supplierAccountId = 0, $clientId = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("bills") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = "Bill_Header";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"]));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                }
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            $response = array_merge($response, $this->voucher_header->k_load_all_bills($filter, $sortable));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $this->load->model("bill_header", "bill_headerfactory");
            $this->bill_header = $this->bill_headerfactory->get_instance();
            $data["statuses"] = $this->bill_header->get("statusValues");
            array_unshift($data["statuses"], "", "overdue");
            unset($data["statuses"][0]);
            $data["statuses"] = array_combine($data["statuses"], [$this->lang->line("overdue"), $this->lang->line("open"), $this->lang->line("partially_paid"), $this->lang->line("paid")]);
            if (0 < $supplierId) {
                $this->load->model("vendor");
                $supplierData = $this->vendor->fetch_vendor($supplierId);
                $data["supplierNameFilter"] = $supplierData["vendorName"];
            } else {
                $data["supplierNameFilter"] = "";
            }
            if (0 < $supplierAccountId) {
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $supplierAccountData = $this->account->fetch_account($supplierAccountId);
                $data["supplierAccountFilterName"] = $supplierAccountData["name"] . " - " . $supplierAccountData["currencyCode"];
                $data["supplierAccountFilterId"] = $supplierAccountData["id"];
            } else {
                $data["supplierAccountFilterName"] = "";
                $data["supplierAccountFilterId"] = "";
            }
            $data["client_account"] = $this->fetch_clinet_account($data, "accountID");
            $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
            $data["gridSavedFiltersData"] = false;
            $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
            if ($data["gridDefaultFilter"]) {
                $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
                $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            }
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            if (0 < $clientId) {
                $this->load->model("client");
                $clientData = $this->client->fetch_client($clientId);
                $data["clientNameFilter"] = $clientData["clientName"];
            } else {
                $data["clientNameFilter"] = "";
            }
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/bills", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("bills/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function bill_payment_add($voucher_id = 0)
    {
        $this->bill_payment_save($voucher_id);
    }
    public function bill_payment_edit($voucher_id = 0, $payment_id = 0)
    {
        if (0 < $voucher_id && !$this->validate_voucher($voucher_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/bills_list");
        }
        $this->bill_payment_save($voucher_id, $payment_id);
    }
    private function bill_payment_save($voucher_id = 0, $payment_id = 0)
    {
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("kendoui/js/kendo.web.min", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("bill_payment") . " | " . $this->lang->line("money"));
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model(["bill_payment", "bill_payment_bill"]);
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        $data = ["payment_data" => ["currency_id" => "", "total" => "", "paymentMethod" => "", "referenceNum" => "", "description" => "", "account_id" => ""]];
        $this->load->model("exchange_rate");
        $data["rates"] = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (isset($data["rates"])) {
            if (0 < $voucher_id) {
                $this->voucher_header->fetch($voucher_id);
                $voucher_organization_id = $this->voucher_header->get_field("organization_id");
                $this->voucher_header->reset_fields();
                if ($this->session->userdata("organizationID") != $voucher_organization_id) {
                    $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
                    redirect("vouchers/bills_list");
                }
                $data["bill_data"] = $this->voucher_header->fetch_bill_voucher($voucher_id);
                if (in_array($data["bill_data"]["status"], ["draft", "cancelled"])) {
                    $this->set_flashmessage("warning", $this->lang->line("you_can_not_record_any_payments_for_this_bill"));
                    redirect("vouchers/bills_list");
                }
                if (!empty($data["bill_data"])) {
                    $bill_payments = $this->bill_payment_bill->load_all(["where" => ["bill_header_id", $data["bill_data"]["id"]]]);
                    $this->bill_header->fetch($data["bill_data"]["id"]);
                    $data["bill_data"]["credits_available"] = 0;
                    $data["bill_data"]["balance_due"] = $data["bill_data"]["total"];
                    foreach ($bill_payments as $payment) {
                        $data["bill_data"]["credits_available"] += $payment["amount"] * 1;
                        $data["bill_data"]["balance_due"] = $data["bill_data"]["total"] * 1 - $data["bill_data"]["credits_available"];
                    }
                    $data["bill_data"]["balance_due"] = number_format($data["bill_data"]["balance_due"], 2, NULL, "");
                    if ($data["bill_data"]["balance_due"] == 0 && $this->bill_header->get_field("status") == "paid" && $payment_id == 0) {
                        $this->set_flashmessage("warning", $this->lang->line("you_can_not_record_any_payments_for_this_bill"));
                        redirect("vouchers/bills_list");
                    }
                } else {
                    $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
                    redirect("vouchers/bill_payments_made/" . $voucher_id);
                }
            } else {
                $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
                redirect("vouchers/bill_payments_made/" . $voucher_id);
            }
            if ($this->input->post(NULL)) {
                $_POST["paidOn"] = date("Y-m-d H:i", strtotime($this->input->post("paidOn")));
                $uploaded = false;
                $result = true;
                if (0 < $payment_id && $this->bill_payment->fetch($payment_id)) {
                    $voucher_header_id = $this->bill_payment->get_field("voucher_header_id");
                    $this->bill_payment_bill->fetch(["bill_payment_id" => $payment_id]);
                    $amount = $this->bill_payment_bill->get_field("amount");
                    $this->bill_payment_bill->delete(["where" => ["bill_payment_id", $payment_id]]);
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                    $this->voucher_header->fetch($voucher_header_id);
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                    $this->voucher_header->set_field("voucherType", "BI-PY");
                    $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                    $this->voucher_header->set_field("description", $this->input->post("comments"));
                    if ($this->voucher_header->update()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        $this->account->fetch($this->input->post("account_id"));
                        $paid_through_currency = $this->account->get_field("currency_id");
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("account_id"));
                        $this->voucher_detail->set_field("drCr", "C");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                        $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $data["bill_data"]["refNum"]);
                        $this->voucher_detail->insert();
                        $this->voucher_detail->reset_fields();
                        $this->account->fetch($this->input->post("supplierAccountId"));
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("supplierAccountId"));
                        $this->voucher_detail->set_field("drCr", "D");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency] / $data["rates"][$this->account->get_field("currency_id")]);
                        $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $data["bill_data"]["refNum"]);
                        if ($this->voucher_detail->insert()) {
                            $this->bill_payment->set_field("voucher_header_id", $voucher_header_id);
                            $this->bill_payment->set_field("account_id", $this->input->post("account_id"));
                            $this->bill_payment->set_field("paymentMethod", $this->input->post("paymentMethod"));
                            $this->bill_payment->set_field("total", $this->input->post("amount"));
                            $this->bill_payment->set_field("supplier_account_id", $this->input->post("supplierAccountId"));
                            $this->bill_payment->set_field("billPaymentTotal", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                            if ($this->bill_payment->update()) {
                                $bill_payment_id = $this->bill_payment->get_field("id");
                                $this->bill_payment_bill->reset_fields();
                                $this->bill_payment_bill->set_field("bill_payment_id", $bill_payment_id);
                                $this->bill_payment_bill->set_field("bill_header_id", $this->input->post("bill_id"));
                                $this->bill_payment_bill->set_field("amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                                if ($this->bill_payment_bill->insert()) {
                                    $this->bill_header->fetch($this->input->post("bill_id"));
                                    $this->bill_header->set_field("status", "open");
                                    if (abs($data["bill_data"]["balance_due"] + $amount - $this->input->post("amount") * $data["rates"][$paid_through_currency]) < 0) {
                                        $this->bill_header->set_field("status", "paid");
                                    } else {
                                        if ($this->input->post("amount") * $data["rates"][$paid_through_currency] < $data["bill_data"]["balance_due"] + $amount) {
                                            $this->bill_header->set_field("status", "partially paid");
                                        }
                                    }
                                    $this->bill_header->update();
                                    $result = true;
                                } else {
                                    $this->bill_payment->delete($bill_payment_id);
                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_header->delete($voucher_header_id);
                                    $result = false;
                                }
                            } else {
                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_header->delete($voucher_header_id);
                                $result = false;
                            }
                        } else {
                            $this->voucher_header->delete($voucher_header_id);
                            $result = false;
                        }
                        if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                            $existant_file = $this->dms->model->get_document_details(["module" => "BI-PY", "module_record_id" => $voucher_header_id, "system_document" => 0]);
                            if (!empty($existant_file)) {
                                $this->dms->delete_document("BI-PY", $existant_file["id"]);
                            }
                            $upload_response = $this->dms->upload_file(["module" => "BI-PY", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                        }
                    }
                } else {
                    $this->account->fetch($this->input->post("account_id"));
                    $paid_through_currency = $this->account->get_field("currency_id");
                    if ($data["bill_data"]["balance_due"] < round($this->input->post("amount") * $data["rates"][$paid_through_currency], 2)) {
                        $this->set_flashmessage("warning", $this->lang->line("allowed_amount"));
                        $data = ["payment_data" => ["currency_id" => $this->input->post("currency_id"), "total" => $data["bill_data"]["balance_due"], "paymentMethod" => $this->input->post("paymentMethod"), "referenceNum" => $this->input->post("referenceNum"), "description" => $this->input->post("comments"), "account_id" => $this->input->post("account_id")]];
                        redirect("vouchers/bill_payment_edit/" . $voucher_id . "/" . $payment_id);
                    }
                    $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->voucher_header->set_field("refNum", $this->auto_generate_rf("BI-PY"));
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                    $this->voucher_header->set_field("voucherType", "BI-PY");
                    $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                    $this->voucher_header->set_field("description", $this->input->post("comments"));
                    $bill_payment_id = "";
                    if ($this->voucher_header->insert()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("account_id"));
                        $this->voucher_detail->set_field("drCr", "C");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                        $this->voucher_detail->insert();
                        $first_voucher_detail_id = $this->voucher_detail->get_field("id");
                        $this->voucher_detail->reset_fields();
                        $this->account->fetch($this->input->post("supplierAccountId"));
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("supplierAccountId"));
                        $this->voucher_detail->set_field("drCr", "D");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount") * $data["rates"][$paid_through_currency] / $data["rates"][$this->account->get_field("currency_id")]);
                        if ($this->voucher_detail->insert()) {
                            $this->bill_payment->reset_fields();
                            $this->bill_payment->set_field("voucher_header_id", $voucher_header_id);
                            $this->bill_payment->set_field("account_id", $this->input->post("account_id"));
                            $this->bill_payment->set_field("paymentMethod", $this->input->post("paymentMethod"));
                            $this->bill_payment->set_field("total", $this->input->post("amount"));
                            $this->bill_payment->set_field("supplier_account_id", $this->input->post("supplierAccountId"));
                            $this->bill_payment->set_field("billPaymentTotal", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                            if ($this->bill_payment->insert()) {
                                $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $this->input->post("bill_id"));
                                $this->voucher_detail->update();
                                $this->voucher_detail->fetch($first_voucher_detail_id);
                                $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $this->input->post("bill_id"));
                                $this->voucher_detail->update();
                                $first_voucher_detail_id = $this->voucher_detail->get_field("id");
                                $bill_payment_id = $this->bill_payment->get_field("id");
                                $this->bill_payment_bill->set_field("bill_payment_id", $bill_payment_id);
                                $this->bill_payment_bill->set_field("bill_header_id", $this->input->post("bill_id"));
                                $this->bill_payment_bill->set_field("amount", $this->input->post("amount") * $data["rates"][$paid_through_currency]);
                                if ($this->bill_payment_bill->insert()) {
                                    $this->bill_header->fetch($this->input->post("bill_id"));
                                    $this->bill_header->set_field("status", "open");
                                    $payment_inserted_id = $this->bill_payment_bill->get_field("id");
                                    $this->bill_payment_bill->fetch($payment_inserted_id);
                                    if ($data["bill_data"]["balance_due"] == $this->bill_payment_bill->get_field("amount")) {
                                        $this->bill_header->set_field("status", "paid");
                                    } else {
                                        if ($this->bill_payment_bill->get_field("amount") < $data["bill_data"]["balance_due"]) {
                                            $this->bill_header->set_field("status", "partially paid");
                                        }
                                    }
                                    $this->bill_header->update();
                                    $result = true;
                                } else {
                                    $this->bill_payment->delete($bill_payment_id);
                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_header->delete($voucher_header_id);
                                    $result = false;
                                }
                            } else {
                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_header->delete($voucher_header_id);
                                $result = false;
                            }
                        } else {
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_header->delete($voucher_header_id);
                            $result = false;
                        }
                        if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                            $upload_response = $this->dms->upload_file(["module" => "BI-PY", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                        }
                    }
                }
                if ($result) {
                    $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("payment")));
                    redirect("vouchers/bill_payments_made/" . $voucher_id);
                }
            }
            if (0 < !$voucher_id && 0 < !$payment_id) {
                redirect("vouchers/bill_payments_made/" . $voucher_id);
            }
            $data["voucher_id"] = $voucher_id;
            $data["tabsNLogs"] = [];
            if (0 < $payment_id) {
                $data["payment_data"] = $this->bill_payment->load_payment_data($payment_id);
                $data["tabsNLogs"]["subNavItems"][site_url("vouchers/bill_payment_edit/" . $voucher_id . "/" . $payment_id)] = $this->lang->line("record_bill_payment");
                $data["tabsNLogs"]["activeSubNavItem"] = site_url("vouchers/bill_payment_edit/" . $voucher_id . "/" . $payment_id);
                $file = $this->dms->model->get_document_details(["module" => "BI-PY", "module_record_id" => $data["payment_data"]["voucher_header_id"], "visible" => 1]);
                if (!empty($file)) {
                    $data["payment_data"]["attachment_id"] = $file["id"];
                    $data["payment_data"]["attachment"] = $file["full_name"];
                } else {
                    $data["payment_data"]["attachment_id"] = "";
                    $data["payment_data"]["attachment"] = "";
                }
            } else {
                $data["tabsNLogs"]["subNavItems"][site_url("vouchers/bill_payment_add/" . $voucher_id)] = $this->lang->line("record_bill_payment");
                $data["tabsNLogs"]["activeSubNavItem"] = site_url("vouchers/bill_payment_add/" . $voucher_id);
                $data["payment_data"]["attachment_id"] = "";
                $data["payment_data"]["attachment"] = "";
            }
            $data["paymentMethod"] = $this->bill_payment->get("paymentMethodValues");
            array_unshift($data["paymentMethod"], "");
            $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("bank_transfer"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            $data["accounts"] = $this->account->load_accounts_per_organization("AssetCashBank");
            $data["rates"] = json_encode($data["rates"]);
            $this->load->view("bill_payments/record_payment", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    public function bill_payments_made($voucher_id)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("bills") . " | " . $this->lang->line("payments_made") . " | " . $this->lang->line("money"));
        if (0 < $voucher_id && !$this->validate_voucher($voucher_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/bills_list");
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->voucher_header->k_load_all_bills_payments_made($filter, $sortable);
                for ($p = 0; $p < count($response["data"]); $p++) {
                    $file = $this->dms->model->get_document_details(["module" => $response["data"][$p]["voucherType"], "module_record_id" => $response["data"][$p]["id"], "system_document" => 0]);
                    $response["data"][$p]["attachment_id"] = isset($file["id"]) ? $file["id"] : "";
                    $response["data"][$p]["attachment_type"] = isset($file["id"]) ? $file["type"] : "";
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $this->load->model(["bill_payment_bill"]);
            $this->load->model("bill_header", "bill_headerfactory");
            $this->bill_header = $this->bill_headerfactory->get_instance();
            $data["bill_data"] = $this->voucher_header->fetch_bill_voucher($voucher_id);
            if (!empty($data["bill_data"])) {
                $bill_payments = $this->bill_payment_bill->load_all(["where" => ["bill_header_id", $data["bill_data"]["id"]]]);
                $this->bill_header->fetch($data["bill_data"]["id"]);
                $data["bill_data"]["credits_available"] = 0;
                $data["bill_data"]["balance_due"] = $data["bill_data"]["total"];
                foreach ($bill_payments as $payment) {
                    $data["bill_data"]["credits_available"] += $payment["amount"] * 1;
                    $data["bill_data"]["balance_due"] -= $payment["amount"] * 1;
                    $data["bill_data"]["balance_due"] = round($data["bill_data"]["balance_due"], 2);
                }
            }
            $data["bill_id"] = $data["bill_data"]["id"];
            $data["voucher_header_id"] = $data["bill_data"]["voucher_header_id"];
            $data["tabsNLogs"] = $this->_get_bill_tabs_view_vars($voucher_id, site_url("vouchers/bill_payments_made/"));
            $data["id"] = $voucher_id;
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/bill_payments_made", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("bill_payments/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function bill_payment_print($paymentVoucherId, $paymentId, $voucherId)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("print") . " | " . $this->lang->line("bill_payment") . " | " . $this->lang->line("money"));
        if (0 < $paymentVoucherId && !$this->validate_voucher($paymentVoucherId)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/bills_list");
        }
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model(["bill_payment", "bill_payment_bill"]);
        $data = [];
        $data["paymentHeader"] = $this->bill_payment->load_header_details($paymentVoucherId, $paymentId);
        if (false === $data["paymentHeader"]) {
            redirect(app_url("", "money"));
        }
        $this->load->library("towords", ["major" => $this->inflector->pluralize($data["paymentHeader"]["currencyName"]), "amount" => $data["paymentHeader"]["total"]]);
        $data["thirdpartyAccount"] = $this->account->fetch_account($data["paymentHeader"]["thirdPartyAccountId"]);
        $data["paymentHeader"]["literalAmount"] = $this->towords->get_words();
        $data["paymentHeader"]["total"] = number_format($data["paymentHeader"]["total"], 2);
        $data["paymentHeader"]["dated"] = date("F d, Y", strtotime($data["paymentHeader"]["dated"]));
        $data["paymentDetails"] = $this->bill_payment_bill->load_lines_with_bill_details($paymentId);
        $data["billHeaderData"] = [];
        $data["billDetailsData"] = [];
        if (0 < $voucherId) {
            $data["billHeaderData"] = $this->voucher_header->fetch_bill_voucher($voucherId);
            $this->load->model("bill_details", "bill_detailsfactory");
            $this->bill_details = $this->bill_detailsfactory->get_instance();
            $data["billDetailsData"] = $this->bill_details->fetch_bill_details($data["billHeaderData"]["id"]);
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["accounts"] = $this->account->load_accounts_per_organization("Expenses");
            foreach ($data["billDetailsData"] as $billKey => $billLine) {
                foreach ($data["accounts"] as $billAccount) {
                    if ($billAccount["id"] == $billLine["account_id"]) {
                        $data["billDetailsData"][$billKey]["accountName"] = $billAccount["accountName"];
                    }
                }
            }
        }
        $this->load->view("partial/header");
        $this->load->view("bill_payments/print", $data);
        $this->load->view("partial/footer");
    }
    public function bill_payment_delete()
    {
        if ($this->input->is_ajax_request()) {
            $voucher_id = $this->input->post("BillVoucherID");
            $payment_id = $this->input->post("paymentID");
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model(["bill_payment", "bill_payment_bill"]);
            $result = false;
            if ($this->validate_voucher_and_payment($voucher_id, $payment_id)) {
                $this->bill_payment->fetch($payment_id);
                $voucher_header_id = $this->bill_payment->get_field("voucher_header_id");
                $this->bill_payment_bill->fetch(["bill_payment_id" => $payment_id]);
                $bill_header_id = $this->bill_payment_bill->get_field("bill_header_id");
                if ($this->bill_payment_bill->delete(["where" => ["bill_payment_id", $payment_id]])) {
                    $result = $this->bill_payment->delete($payment_id);
                    if ($result && $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                        $this->dms->delete_module_record_container("BI-PY", $voucher_header_id);
                    } else {
                        $result = false;
                    }
                }
            }
            if ($result) {
                if ($this->set_bill_status($bill_header_id)) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 202;
                }
            } else {
                $response["status"] = 202;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->set_flashmessage("warning", $this->lang->line("invalid_request"));
            redirect("vouchers/bill_payments_made");
        }
    }
    private function set_bill_status($bill_header_id)
    {
        $this->load->model("bill_payment_bill");
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        $bill_payments = $this->bill_payment_bill->load_all(["where" => ["bill_header_id", $bill_header_id]]);
        $this->bill_header->fetch($bill_header_id);
        $total = $this->bill_header->get_field("total");
        $credits_available = 0;
        $balance_due = $total;
        foreach ($bill_payments as $payment) {
            $credits_available = bcadd($balance_due, $payment["amount"], 2);
            $balance_due = bcsub($balance_due, $payment["amount"], 2);
        }
        if ($balance_due == 0) {
            $status = "paid";
        } else {
            if ($credits_available == 0) {
                $status = "open";
            } else {
                if (0 < $credits_available) {
                    $status = "partially paid";
                }
            }
        }
        $this->bill_header->set_field("status", $status);
        if ($this->bill_header->update()) {
            return true;
        }
        return false;
    }
    private function validate_voucher_and_payment($voucher_id, $payment_id)
    {
        $this->load->model("bill_payment_bill");
        $bill_record = $this->bill_payment_bill->load_bill_record($payment_id);
        return $bill_record["voucher_header_id"] == $voucher_id;
    }
    public function bill_related_documents($id = "")
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/bills_list");
        }
        $this->related_documents($id, "BI", "bill");
    }
    public function bill_load_documents()
    {
        $this->load_documents();
    }
    public function bill_upload_file()
    {
        $this->upload_file();
    }
    public function bill_rename_file()
    {
        $this->rename_file("BI");
    }
    public function bill_edit_documents()
    {
        $this->edit_documents();
    }
    public function bill_download_file($file_id)
    {
        $this->download_file("BI", $file_id);
    }
    public function bill_delete_document()
    {
        $this->delete_document("BI");
    }
    public function bill_payment_download_file($file_id)
    {
        $this->download_file("BI-PY", $file_id);
    }
    public function expense_related_documents($id = "")
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/expenses_list");
        }
        $this->related_documents($id, "EXP", "expense");
    }
    public function expense_load_documents()
    {
        $this->load_documents();
    }
    public function expense_upload_file()
    {
        $this->upload_file();
    }
    public function expense_rename_file()
    {
        $this->rename_file("EXP");
    }
    public function expense_edit_documents()
    {
        $this->edit_documents();
    }
    public function expense_download_file($file_id)
    {
        $this->download_file("EXP", $file_id);
    }
    public function expense_delete_document()
    {
        $this->delete_document("EXP");
    }
    public function expense_add()
    {
        $this->expense_save();
    }
    public function case_expenses_add($caseId, $bulk = false)
    {
        if (0 < $caseId) {
            $data = $this->return_case_client_details($caseId);
            if ($bulk ? $this->expenses_add_bulk($data) : $this->expense_save(0, $data)) {
                if ($data["case_category"] != "IP") {
                    $have_expenses_tab_access = $this->is_auth->check_uri_permissions("/cases/", "/cases/expenses/", "core", true, true);
                    if ($have_expenses_tab_access) {
                        redirect(app_url("cases/expenses/" . $caseId));
                    } else {
                        redirect(app_url("cases/my_expenses/" . $caseId));
                    }
                } else {
                    redirect(app_url("intellectual_properties/expenses/" . $caseId));
                }
            }
        } else {
            redirect(app_url("dashboard"));
        }
    }
    public function expense_edit($id = 0)
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/expenses_list");
        }
        $this->expense_save($id);
    }
    public function get_expense_accounts_by_type($account_type = "")
    {
        $response = [];
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->user->fetch(["email" => $this->user->get("isAdminUser")]);
        $fetch_by_user = $this->user->get_field("id") != $this->is_auth->get_user_id();
        if ($this->input->is_ajax_request()) {
            $account_type = $this->input->post("account_type");
        }
        if (isset($account_type) && !empty($account_type)) {
            switch ($account_type) {
                case "Cash":
                    $accountType = ["type_id" => "1", "typeType" => "'Asset'"];
                    $response["accounts"] = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    break;
                case "Credit Card":
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $response["accounts"] = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    break;
                case "Cheque & Bank":
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $response["accounts"] = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    break;
                case "Online payment":
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $liability_accounts = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $bank_accounts = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    $response["accounts"] = array_merge($liability_accounts, $bank_accounts);
                    break;
                case "Other":
                    $accountType = ["type_id" => "1", "typeType" => "'Asset'"];
                    $cach_accounts = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    $accountType = ["type_id" => "5", "typeType" => "'Liability'"];
                    $liability_accounts = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    $accountType = ["type_id" => "2", "typeType" => "'Asset'"];
                    $bank_accounts = $this->account->load_accounts_per_organization_per_account_type_id($accountType, false, false, $fetch_by_user);
                    $array1 = array_merge($cach_accounts, $liability_accounts);
                    $array2 = array_merge($array1, $bank_accounts);
                    $response["accounts"] = $array2;
                    break;
            }
        }
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response["accounts"];
        }
    }
    public function petty_cash_user_mapping($userId = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("petty_cash_user_mapping") . " | " . $this->lang->line("money"));
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $usersAccountsMapping = $this->account->load_account_user_mapping($userId);
        $accountType = ["type_id" => "1,2,5", "typeType" => "'Asset', 'Liability'"];
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $allUsers = $this->user->load_users_list();
        if (strcmp($userId, "")) {
            $userName = $allUsers[$userId];
            $allUsers = [$userId => $userName];
        }
        $data = [];
        $data["mappingArray"] = [];
        foreach ($usersAccountsMapping as $mapArr) {
            $data["mappingArray"][$mapArr["userId"]]["userName"] = $mapArr["userName"];
            $data["mappingArray"][$mapArr["userId"]]["accounts"][$mapArr["accountId"]] = $mapArr["accountName"];
        }
        $data["allAccounts"] = $this->account->load_accounts_per_organization_per_account_type_id($accountType, ["value" => "full_account_name"]);
        unset($data["allAccounts"][""]);
        $data["userId"] = $userId;
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("accounts/petty_cash_user_mapping", $data);
        $this->load->view("partial/footer");
    }
    public function petty_cash_user_mapping_edit()
    {
        if ($this->input->post(NULL)) {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            if ($this->input->is_ajax_request()) {
                $userId = $this->input->post("userId");
                $accountIDs = $this->input->post("maps") ? $this->input->post("maps") : [];
                $userMapping["accounts"] = ["userId" => $userId, "accounts" => $accountIDs, "accounts_old" => $this->input->post("accounts_old")];
                $this->account->update_accounts_users_mapping($userMapping);
                $response = ["status" => "success"];
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            } else {
                unset($_POST["hiddenUserId"]);
                unset($_POST["userId"]);
                foreach ($this->input->post("accounts") as $key => $value) {
                    $accounts[$key] = $this->input->post("accounts")[$key] ? $this->input->post("accounts")[$key] : [];
                    $userMapping["accounts"] = ["userId" => $key, "accounts" => $accounts[$key], "accounts_old" => $this->input->post("accounts_old")[$key]];
                    $this->account->update_accounts_users_mapping($userMapping);
                }
                $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("expense")));
                redirect("vouchers/petty_cash_user_mapping");
            }
        }
    }
    public function petty_case_user_mapping_export_to_excel()
    {
        $userId = $this->input->post(NULL) && $this->input->post("hiddenUserId") ? $this->input->post("hiddenUserId") : "";
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $usersAccountsMapping = $this->account->load_account_user_mapping($userId);
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $allUsers = $this->user->load_users_list();
        if (strcmp($userId, "")) {
            $userName = $allUsers[$userId];
            $allUsers = [$userId => $userName];
        }
        $data = [];
        $data["mappingArray"] = [];
        foreach ($usersAccountsMapping as $mapArr) {
            $data["mappingArray"][$mapArr["userId"]]["userName"] = $mapArr["userName"];
            $data["mappingArray"][$mapArr["userId"]]["accounts"][$mapArr["accountId"]] = $mapArr["accountName"];
        }
        $filename = urlencode(str_replace(" ", "_", $this->lang->line("petty_cash_user_mapping")));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/petty_cash_user_mapping", $data);
        $this->load->view("excel/footer");
    }
    private function expense_saveold($id = 0, $extraData = [])
    {
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("expenses") . " | " . $this->lang->line("money"));
        $this->load->helper(["text"]);
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        $data = ["expense" => [], "isCasePreset" => false];
        $caseId = "";
        $caseSubject = $case_category = "";
        $clientId = "";
        $clientName = "";
        $billingStatus = "";
        $related_hearing = $related_event = $related_task = false;
        if (!empty($extraData)) {
            $caseId = $extraData["caseId"];
            $caseSubject = $extraData["caseSubject"];
            $clientId = isset($extraData["clientId"]) ? $extraData["clientId"] : "";
            $clientName = isset($extraData["clientName"]) ? $extraData["clientName"] : "";
            $case_category = $extraData["case_category"];
            $related_hearing = isset($extraData["hearing"]) ? $extraData["hearing"] : false;
            $related_task = isset($extraData["task"]) ? $extraData["task"] : false;
            $related_event = isset($extraData["event"]) ? $extraData["event"] : false;
            $data["isCasePreset"] = true;
        }
        $data["expense"] = ["id" => "", "expense_account" => "", "expense_category_id" => "", "paid_through" => "", "tax_id" => "", "amount" => "", "billingStatus" => "", "dated" => "", "description" => "", "referenceNum" => "", "attachment" => "", "case_id" => $caseId, "case_subject" => $caseSubject, "case_category" => $case_category, "vendor_id" => "", "vendorName" => "", "client_id" => $clientId, "client_account_id" => "", "clientName" => $clientName, "paymentMethod" => "", "related_hearing" => $related_hearing, "related_task" => $related_task, "related_event" => $related_event];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $case_currency_id = $this->legal_case->get_money_currency();
        if (!empty($exchange_rates)) {
            $data["rates"] = $exchange_rates;
            if ($this->input->post(NULL)) {
                if ($this->license_availability === false) {
                    $this->set_flashmessage("error", $this->licensor->get_license_message());
                    redirect("vouchers/expense_edit/" . $id);
                }
                $this->validate_current_organization($this->input->post("organization_id"), "expenses_list");
                if (0 == $id && $this->input->post("case_id") && $this->input->post("client_id")) {
                    $expense_amount = $this->input->post("amount");
                    if ($this->input->post("tax_id")) {
                        $this->load->model("tax", "taxfactory");
                        $this->tax = $this->taxfactory->get_instance();
                        $tax_account = $this->tax->get_tax_account($this->input->post("tax_id"));
                        $expense_amount = $this->input->post("amount") * 100 / ($tax_account["percentage"] + 100);
                        $expense_amount = number_format($expense_amount, $allowed_decimal_format, ".", "");
                    }
                    if ($this->input->post("case_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id)) {
                        $validate_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $expense_amount);
                    }
                    if ($this->input->post("case_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id) && $validate_capping_amount == "disallow") {
                        $this->legal_case->fetch($this->input->post("case_id"));
                        $this->set_flashmessage("error", sprintf($this->lang->line("capping_amount_validation"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation")));
                        redirect("vouchers/expenses_list/");
                    }
                }
                $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                $initial_expense_status = $moneyPreferences["keyValue"];
                $result = false;
                if ($this->input->post("expense_account") == $this->input->post("paid_through")) {
                    $this->set_flashmessage("error", $this->lang->line("transaction_not_saved"));
                    redirect("vouchers/expenses_list/");
                }
                if ($this->input->post("amount") <= 0) {
                    $this->set_flashmessage("error", $this->lang->line("amount_should_be_positive"));
                    redirect("vouchers/expenses_list/");
                }
                if (0 != $id) {
                    $voucher_header_id = $id;
                    $_POST["voucher_header_id"] = $id;
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                    $this->voucher_header->fetch($voucher_header_id);
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                    $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                    $this->voucher_header->set_field("description", $this->input->post("comments"));
                    if ($this->voucher_header->update()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        if ($this->input->post("case_id")) {
                            $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                            $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                            $this->voucher_related_case->insert();
                        }
                        $client_id = $this->input->post("client_id");
                        if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                            $client_added_to_case_message = "<li>" . $this->lang->line("client_added_to_case") . "</li>";
                        }
                        $client_account_id = $this->input->post("client_account_id");
                        $this->expense->fetch(["voucher_header_id" => $voucher_header_id]);
                        $this->expense->set_fields($this->input->post(NULL));
                        $this->load->model("money_preference");
                        $billingStatus = $this->expense->get_field("billingStatus");
                        if ($billingStatus != "invoiced" && $billingStatus != "reimbursed") {
                            if ($billingStatus != "internal") {
                                $this->expense->set_field("client_id", $client_id);
                                $this->expense->set_field("client_account_id", $client_account_id);
                                $this->expense->set_field("billingStatus", $billingStatus);
                            } else {
                                $this->expense->set_field("client_id", NULL);
                                $this->expense->set_field("client_account_id", NULL);
                                $this->expense->set_field("billingStatus", "internal");
                            }
                        }
                        if ($this->expense->update() && $this->expense->get_field("status") == "approved") {
                            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                            $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                            $this->voucher_detail->set_field("drCr", "C");
                            $paid_through_account = $this->account->fetch_account($this->input->post("paid_through"));
                            $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]]);
                            $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                            $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                            if ($this->voucher_detail->insert()) {
                                $expense_local_amount = 0;
                                $expense_foreign_amount = 0;
                                $expense_account = $this->account->fetch_account($this->input->post("expense_account"));
                                if ($this->input->post("tax_id")) {
                                    $this->load->model("tax", "taxfactory");
                                    $this->tax = $this->taxfactory->get_instance();
                                    $tax_account = $this->tax->get_tax_account($this->input->post("tax_id"));
                                    $expense_amount = $this->input->post("amount") * 100 / ($tax_account["percentage"] + 100);
                                    $tax_amount = $this->input->post("amount") - $expense_amount;
                                    $tax_local_amount = $tax_amount * $data["rates"][$paid_through_account["currency_id"]];
                                    $tax_foreign_amount = $tax_local_amount / $data["rates"][$tax_account["currency_id"]];
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
                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $result = false;
                        }
                    } else {
                        $result = false;
                    }
                    if ($result) {
                        $expense_saved = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("expense"));
                        $this->set_flashmessage("success", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" : $expense_saved);
                        if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id)) {
                            $validate_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $expense_saved);
                        }
                        if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id) && $validate_capping_amount == "warning") {
                            $validation_capping_amount_message = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                            $this->set_flashmessage("information", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" . "<li>" . $validation_capping_amount_message . "</li>" : $expense_saved . "<li>" . $validation_capping_amount_message . "</li>");
                        } else {
                            $this->set_flashmessage("success", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" : $expense_saved);
                        }
                        redirect($this->input->post("referrer"));
                    }
                } else {
                    if (!$systemPreferences["requireExpenseDocument"] || $systemPreferences["requireExpenseDocument"] && !empty($_FILES["uploadDoc"]["name"])) {
                        if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                            $client_added_to_case_message = "<li>" . $this->lang->line("client_added_to_case") . "</li>";
                        }
                        $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                        $this->voucher_header->set_field("refNum", $this->auto_generate_rf("EXP"));
                        $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                        $this->voucher_header->set_field("voucherType", "EXP");
                        $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                        $this->voucher_header->set_field("description", $this->input->post("comments"));
                        $voucher_header_id = "";
                        if ($this->voucher_header->insert()) {
                            $voucher_header_id = $this->voucher_header->get_field("id");
                            if ($this->input->post("case_id")) {
                                $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                                $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                                $this->voucher_related_case->insert();
                            }
                            $_POST["voucher_header_id"] = $voucher_header_id;
                            $this->expense->set_fields($this->input->post(NULL));
                            $this->load->model("money_preference");
                            $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                            $this->expense->set_field("status", $moneyPreferences["keyValue"]);
                            if ($this->input->post("billingStatus") != "internal") {
                                $this->expense->set_field("client_account_id", $this->input->post("client_account_id") ? $this->input->post("client_account_id") : NULL);
                                $this->expense->set_field("client_id", $this->input->post("client_id"));
                                $this->expense->set_field("billingStatus", $this->input->post("billingStatus"));
                            }
                            $this->expense->set_field("voucher_header_id", $voucher_header_id);
                            if ($this->expense->insert()) {
                                $result = true;
                                if ($this->expense->get_field("status") == "approved") {
                                    $this->expense->fetch($this->expense->get_field("id"));
                                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                    $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                                    $this->voucher_detail->set_field("drCr", "C");
                                    $paid_through_account = $this->account->fetch_account($this->input->post("paid_through"));
                                    $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$paid_through_account["currency_id"]]);
                                    $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                                    $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
                                    if ($this->voucher_detail->insert()) {
                                        $expense_local_amount = 0;
                                        $expense_foreign_amount = 0;
                                        $expense_account = $this->account->fetch_account($this->input->post("expense_account"));
                                        if ($this->input->post("tax_id")) {
                                            $this->load->model("tax", "taxfactory");
                                            $this->tax = $this->taxfactory->get_instance();
                                            $tax_account = $this->tax->get_tax_account($this->input->post("tax_id"));
                                            $expense_amount = $this->input->post("amount") * 100 / ($tax_account["percentage"] + 100);
                                            $tax_amount = $this->input->post("amount") - $expense_amount;
                                            $tax_local_amount = $tax_amount * $data["rates"][$paid_through_account["currency_id"]];
                                            $tax_foreign_amount = $tax_local_amount / $data["rates"][$tax_account["currency_id"]];
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
                                }
                            } else {
                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_header->delete($voucher_header_id);
                                $result = false;
                            }
                            if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                                $upload_response = $this->dms->upload_file(["module" => "EXP", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                            }
                        } else {
                            $result = false;
                        }
                        if ($result) {
                            if ($initial_expense_status == "open") {
                                $this->send_notification_to_groups_users($voucher_header_id, $this->expense->get_field("id"));
                            }
                            $expense_saved = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("expense"));
                            if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id)) {
                                $validate_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $expense_saved);
                            }
                            if ($this->input->post("client_id") && $this->input->post("case_id") && !empty($case_currency_id) && $this->input->post("billingStatus") == "to-invoice" && $validate_capping_amount == "warning") {
                                $validation_capping_amount_message = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                                $this->set_flashmessage("information", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" . "<li>" . $validation_capping_amount_message . "</li>" : $expense_saved . "<li>" . $validation_capping_amount_message . "</li>");
                            } else {
                                $this->set_flashmessage("success", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" : $expense_saved);
                            }
                            if (!$this->input->post("create_another") == "yes") {
                                if ($data["isCasePreset"]) {
                                    return true;
                                }
                                redirect("vouchers/expenses_list/");
                            }
                            $cloned_data = $this->input->post(NULL);
                        }
                    } else {
                        $this->set_flashmessage("error", $this->lang->line("missing_uploaded_file_data"));
                        redirect($this->input->post("referrer"));
                    }
                }
            }
            $this->load->model("expense_category", "expense_categoryfactory");
            $this->expense_category = $this->expense_categoryfactory->get_instance();
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model("tax", "taxfactory");
            $this->tax = $this->taxfactory->get_instance();
            $data["require_expense_document"] = $systemPreferences["requireExpenseDocument"];
            $data["clients_do_not_match"] = false;
            if ($id != 0) {
                $data["expense"] = $this->voucher_header->fetch_expense_details($id);
                if ($data["expense"]["case_id"]) {
                    $data["case_client"] = $this->legal_case->get_case_client($data["expense"]["case_id"]);
                }
                if (empty($data["expense"])) {
                    $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
                    redirect("vouchers/expense_add");
                }
            }
            if (0 < $id) {
                $active = site_url("vouchers/expense_edit/");
                $data["clients_do_not_match"] = $this->check_case_client_match_expense_client($data);
            } else {
                $active = site_url("vouchers/expense_add");
            }
            $data["tabsNLogs"] = $this->_get_expense_tabs_view_vars($id, $active);
            $data["taxes"] = $this->tax->get_taxes();
            $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list();
            if (0 < $id) {
                $data["paid_through"] = $this->get_expense_accounts_by_type($data["expense"]["paymentMethod"]);
                $paid_through_ids = [];
                if (isset($data["paid_through"]) && !empty($data["paid_through"])) {
                    foreach ($data["paid_through"] as $value) {
                        $paid_through_ids[] = $value["id"];
                    }
                }
                if (isset($data["expense"]["paid_through"]) && !empty($data["expense"]["paid_through"])) {
                    if (!in_array($data["expense"]["paid_through"], $paid_through_ids)) {
                        $data["paid_through_permission_flag"] = false;
                        $this->load->model("account", "accountfactory");
                        $this->account = $this->accountfactory->get_instance();
                        $data["paid_through_account_data"] = $this->account->get_account_data($data["expense"]["paid_through"]);
                    } else {
                        $data["paid_through_permission_flag"] = true;
                    }
                }
            }
            $data["rates"] = json_encode($data["rates"]);
            $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
            array_unshift($data["paymentMethod"], "");
            $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            $this->includes("money/js/expense_form", "js");
            $this->includes("money/js/common_expenses_form_functions", "js");
            if (0 != $id) {
                $data["is_edit_mode"] = true;
                $data["voucherId"] = $id;
                $data["objName"] = "expense";
                $data["modelName"] = "EXP";
                $data["subModelName"] = "EXP-DOCS";
                $data["systemPreferences"] = $this->session->userdata("systemPreferences");
                $this->includes("kendoui/js/kendo.web.min", "js");
                $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
                $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
                $this->includes("jquery/form2js", "js");
                $this->includes("jquery/toObject", "js");
                $this->includes("jquery/dropzone", "js");
                $this->includes("money/js/expenses", "js");
                $this->includes("jquery/css/dropzone", "css");
                if ($this->is_auth->is_layout_rtl()) {
                    $this->includes("styles/rtl/fixes", "css");
                }
            }
            $this->load->library("user_agent");
            $data["referrer"] = "dashboard";
            if ($this->agent->is_referral()) {
                $data["referrer"] = $this->agent->referrer();
            }
            if (isset($cloned_data)) {
                $data["expense"] = $this->voucher_header->fetch_expense_details($voucher_header_id);
                $data["expense"] = array_merge($data["expense"], $cloned_data);
                $data["expense"]["expense_category_id"] = "";
                $data["expense"]["attachment"] = "";
                $data["expense"]["amount"] = "";
                $data["expense"]["id"] = "";
                $data["expense"]["description"] = $cloned_data["comments"];
                $data["expense"]["dated"] = $cloned_data["paidOn"];
                unset($data["expense"]["comments"]);
                unset($data["expense"]["paidOn"]);
            }
            $hearing_subject = $data["expense"]["related_hearing"]["subject"];
            $systemPreferences = $this->session->userdata("systemPreferences");
            if ($hearing_subject && isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
                $text = explode(" ", $hearing_subject);
                $hijri_date = gregorianToHijri($text[0], "Y-m-d");
                $data["expense"]["related_hearing"]["subject"] = str_replace($text[0], $hijri_date, $hearing_subject);
            }
            $this->load->view("expenses/form", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    public function my_expenses_list($clientId = 0, $supplierId = 0)
    {
        $this->authenticate_exempted_actions();
        $this->expenses_list_data($clientId, $supplierId, true);
    }
    public function expenses_list($clientId = 0, $supplierId = 0)
    {
        $this->expenses_list_data($clientId, $supplierId);
    }
    private function expenses_list_data($clientId = 0, $supplierId = 0, $my_expenses = false)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("expenses") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = "Expense";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"]));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                }
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            if ($my_expenses) {
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $auth_user_id = $this->session->userdata("AUTH_user_id");
                $user_accounts_mapping = $this->account->load_account_user_mapping($auth_user_id);
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            if ($my_expenses) {
                if (!empty($user_accounts_mapping)) {
                    $response = array_merge($response, $this->voucher_header->k_load_all_expenses($filter, $sortable));
                }
            } else {
                $response = array_merge($response, $this->voucher_header->k_load_all_expenses($filter, $sortable));
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data["billingStatuses"] = array_combine($this->expense->get("billingStatusValues"), $this->expense->get("billingStatusValues"));
            $data["statuses"] = array_combine($this->expense->get("expenseStatusValues"), $this->expense->get("expenseStatusValues"));
            foreach ($data["statuses"] as $key => $value) {
                $keys[] = $this->lang->line($key);
            }
            $data["statuses"] = array_combine($this->expense->get("expenseStatusValues"), $keys);
            $this->load->model("expense_category", "expense_categoryfactory");
            $this->expense_category = $this->expense_categoryfactory->get_instance();
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list();
            $account_type = ["type_id" => "1,2,5", "typeType" => "'Asset', 'Liability'"];
            $data["paid_through"] = $this->account->load_accounts_per_organization_per_account_type_id($account_type, ["value" => "accountName"]);
            unset($data["paid_through"][""]);
            $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
            array_unshift($data["paymentMethod"], "");
            $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            unset($data["paymentMethod"][""]);
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            if (0 < $clientId) {
                $this->load->model("client");
                $clientData = $this->client->fetch_client($clientId);
                $data["clientNameFilter"] = $clientData["clientName"];
            } else {
                $data["clientNameFilter"] = "";
            }
            if (0 < $supplierId) {
                $this->load->model("vendor");
                $supplierData = $this->vendor->fetch_vendor($supplierId);
                $data["supplierNameFilter"] = $supplierData["vendorName"];
            } else {
                $data["supplierNameFilter"] = "";
            }
            $data["client_account"] = $this->fetch_clinet_account($data, "clientAccountID");
            if ($my_expenses) {
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $auth_user_id = $this->session->userdata("AUTH_user_id");
                $user_accounts_mapping = $this->account->load_account_user_mapping($auth_user_id);
                $data["user_paid_through_Accounts"] = [];
                foreach ($user_accounts_mapping as $value) {
                    $data["user_paid_through_Accounts"][] = $value["accountId"];
                }
                $data["my_expenses"] = true;
            }
            $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
            $data["gridSavedFiltersData"] = false;
            $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
            if ($data["gridDefaultFilter"]) {
                $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
                $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
                if ($data["supplierNameFilter"] && isset($data["gridSavedFiltersData"]["gridFilters"])) {
                    $decode_filter = json_decode($data["gridSavedFiltersData"]["gridFilters"], true);
                    $supplier_filter = ["filters" => [["field" => "supplier", "operator" => "eq", "value" => $data["supplierNameFilter"]]]];
                    array_push($decode_filter["filters"], $supplier_filter);
                    $data["gridSavedFiltersData"]["gridFilters"] = json_encode($decode_filter);
                }
            }
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/common_expenses_form_functions", "js");
            $this->includes("money/js/expenses", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("expenses/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function expense_delete()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $voucher_id = $this->input->post("voucherID");
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            $exists = $this->expense->check_expense_related_invoice_exists($voucher_id);
            if (!$exists) {
                $result = false;
                $expense = $this->voucher_header->fetch_expense_details($voucher_id);
                if (!in_array($expense["billingStatus"], ["invoiced", "reimbursed"])) {
                    $expense_title = $expense["expensesCategoryName"] . "/" . $expense["paymentMethod"] . "-" . $expense["amount"] . "-" . $expense["paid_through_currency"];
                    if ($this->expense->delete(["where" => ["voucher_header_id", $voucher_id]])) {
                        $this->send_notification_to_expense_creator($voucher_id, $expense_title, "delete_expense");
                        if ($expense["status"] == "approved") {
                            if ($this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_id]])) {
                                $this->dms->delete_module_record_container("EXP", $voucher_id);
                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_id]]);
                                $result = $this->voucher_header->delete($voucher_id);
                            }
                        } else {
                            $this->dms->delete_module_record_container("EXP", $voucher_id);
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_id]]);
                            $result = $this->voucher_header->delete($voucher_id);
                        }
                    }
                }
                if ($result) {
                    $response["status"] = "success";
                } else {
                    $response["status"] = "failed";
                }
            } else {
                $response["status"] = "exists_invoice_fk";
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->set_flashmessage("warning", $this->lang->line("invalid_request"));
            redirect("vouchers/expenses_list");
        }
    }
    private function set_expense_status($invoice_details, $expense_status)
    {
        $ids = [];
        foreach ($invoice_details as $value) {
            if ($value["expense_id"]) {
                $ids[] = $value["expense_id"];
            }
        }
        if (!empty($ids)) {
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            $this->expense->update_expenses_status_by_id($expense_status, join(",", $ids));
        }
    }
    private function set_time_logs_status($invoice_details, $status)
    {
        $ids = [];
        foreach ($invoice_details as $value) {
            if ($value["time_logs_id"]) {
                $ids[] = $value["time_logs_id"];
            }
        }
        if (!empty($ids)) {
            $this->load->model("user_activity_log", "user_activity_logfactory");
            $this->user_activity_log = $this->user_activity_logfactory->get_instance();
            $this->user_activity_log->update_logs_invoicing_status($status, $ids);
        }
    }
    public function change_expense_status()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $voucher_id = $this->input->post("voucher_id");
            $billingStatus = $this->input->post("billingStatus");
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            if (empty($billingStatus)) {
                $data["expense"] = $this->voucher_header->fetch_expense_details($voucher_id);
                $data["case_client"] = $this->legal_case->get_case_client($data["expense"]["case_id"]);
                $data["clients_do_not_match"] = $this->check_case_client_match_expense_client($data);
                $response["html"] = $this->load->view("expenses/change_billing_status", $data, true);
            } else {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $voucher_header_id = $this->input->post("voucher_header_id");
                $expense_id = $this->input->post("expense_id");
                $client_id = $this->input->post("client_id");
                $client_account_id = $this->input->post("account_id");
                $this->expense->fetch($expense_id);
                if (!in_array($this->expense->get_field("billingStatus"), ["invoiced", "reimbursed"])) {
                    $expense_details = $this->voucher_header->fetch_expense_details($voucher_header_id);
                    $expense_details["client_id"] = $client_id;
                    if ($expense_details["case_id"] && $expense_details["client_id"] && $this->legal_case->add_client_to_case($expense_details)) {
                        $response["client_added_to_case"] = true;
                    }
                    if (!in_array($billingStatus, ["internal", "not-set"])) {
                        $this->expense->set_field("client_id", $client_id);
                        if ($billingStatus == "non-billable") {
                            $this->expense->set_field("client_account_id", NULL);
                        } else {
                            $this->expense->set_field("client_account_id", $client_account_id);
                        }
                        $this->expense->set_field("billingStatus", $billingStatus);
                    } else {
                        if ($billingStatus == "internal") {
                            $this->expense->set_field("client_id", NULL);
                        } else {
                            $this->expense->set_field("client_id", $client_id);
                        }
                        $this->expense->set_field("client_account_id", NULL);
                        $this->expense->set_field("billingStatus", $billingStatus);
                    }
                }
                $response["status"] = $this->expense->update();
                $response["validationErrors"] = $this->expense->get("validationErrors");
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function move_expense_status_to_open()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $data["voucher_id"] = $this->input->post("voucher_id");
            $data["transition"] = $this->input->post("transition");
            $data["comment"] = $this->input->post("comment");
            if ($data["transition"] == 1) {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $this->expense->fetch($this->input->post("expense_id"));
                $voucher_header_id = $this->expense->get_field("voucher_header_id");
                $current_status = $this->expense->get_field("status");
                if ($current_status != "open") {
                    $ids = [];
                    $ids[] = $this->input->post("expense_id");
                    $this->load->model("expense_status_note", "expense_status_notefactory");
                    $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                    $this->expense_status_note->set_field("expense_id", $this->input->post("expense_id"));
                    $this->expense_status_note->set_field("transition", "open");
                    if (!empty($data["comment"])) {
                        $this->expense_status_note->set_field("note", $data["comment"]);
                    }
                    if ($this->expense_status_note->insert()) {
                        $this->load->model("expense", "expensefactory");
                        $this->expense = $this->expensefactory->get_instance();
                        $response["status"] = $this->expense->update_expenses_approval_status_by_id("open", join(",", $ids));
                        if ($response["status"]) {
                            $this->send_notification_to_groups_users($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_open");
                            $this->send_notification_to_expense_creator($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_open");
                            $response["expense_status"] = "open";
                            $this->update_transaction($this->input->post("expense_id"), "revert");
                        }
                    } else {
                        $response["validationErrors"] = $this->expense_status_note->get("validationErrors");
                    }
                }
            } else {
                $expense = $this->voucher_header->fetch_expense_details($data["voucher_id"]);
                $data["expense_id"] = $expense["expenseID"];
                $response["html"] = $this->load->view("expenses/change_expense_status", $data, true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function move_expense_status_to_approved()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $data["voucher_id"] = $this->input->post("voucher_id");
            $data["transition"] = $this->input->post("transition");
            $data["comment"] = $this->input->post("comment");
            if ($data["transition"] == 1) {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $this->expense->fetch($this->input->post("expense_id"));
                $this->load->model("legal_case", "legal_casefactory");
                $this->legal_case = $this->legal_casefactory->get_instance();
                $voucher_header_id = $this->expense->get_field("voucher_header_id");
                $current_status = $this->expense->get_field("status");
                $allowed_decimal_format = $this->config->item("allowed_decimal_format");
                if ($current_status != "approved") {
                    $expenses_data = $this->voucher_header->fetch_expense_details($this->input->post("voucher_header_id"));
                    $expense_amount = $expenses_data["amount"];
                    if (!empty($expenses_data["tax_id"])) {
                        $this->load->model("tax", "taxfactory");
                        $this->tax = $this->taxfactory->get_instance();
                        $tax_account = $this->tax->get_tax_account($expenses_data["tax_id"]);
                        $expense_amount = $expenses_data["amount"] * 100 / ($tax_account["percentage"] + 100);
                        $expense_amount = number_format($expense_amount, $allowed_decimal_format, ".", "");
                    }
                    $case_currency_id = $this->legal_case->get_money_currency();
                    $validation_capping_amount = "";
                    if (!empty($expenses_data["client_id"]) && !empty($expenses_data["case_id"]) && $expenses_data["billingStatus"] == "to-invoice" && !empty($case_currency_id)) {
                        $validation_capping_amount = $this->legal_case->validate_capping_amount($expenses_data["client_id"], $expenses_data["case_id"], $case_currency_id, false, $expense_amount, NULL, true);
                    }
                    if (!empty($expenses_data["client_id"]) && !empty($expenses_data["case_id"]) && !empty($case_currency_id) && $expenses_data["billingStatus"] == "to-invoice" && $validation_capping_amount == "disallow") {
                        $this->legal_case->fetch($expenses_data["case_id"]);
                        $response["status"] = false;
                        $response["message"] = sprintf($this->lang->line("capping_amount_validation"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                    } else {
                        if (!empty($expenses_data["client_id"]) && !empty($expenses_data["case_id"]) && $expenses_data["billingStatus"] == "to-invoice" && $validation_capping_amount == "warning") {
                            $response["warning"] = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                        }
                        $ids = [];
                        $ids[] = $this->input->post("expense_id");
                        $this->load->model("expense_status_note", "expense_status_notefactory");
                        $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                        $this->expense_status_note->set_field("expense_id", $this->input->post("expense_id"));
                        $this->expense_status_note->set_field("transition", "approved");
                        if (!empty($data["comment"])) {
                            $this->expense_status_note->set_field("note", $data["comment"]);
                        }
                        if ($this->expense_status_note->insert()) {
                            $this->load->model("expense", "expensefactory");
                            $this->expense = $this->expensefactory->get_instance();
                            $response["status"] = $this->expense->update_expenses_approval_status_by_id("approved", join(",", $ids));
                            if ($response["status"]) {
                                $response["expense_status"] = "approved";
                                $this->send_notification_to_expense_creator($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_approved");
                                $this->send_notification_to_approve_expense_groups_users($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_approved");
                                $this->update_transaction($this->input->post("expense_id"));
                            }
                        } else {
                            $response["validationErrors"] = $this->expense_status_note->get("validationErrors");
                        }
                    }
                }
            } else {
                $expense = $this->voucher_header->fetch_expense_details($data["voucher_id"]);
                $data["expense_id"] = $expense["expenseID"];
                $response["html"] = $this->load->view("expenses/change_expense_status", $data, true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function move_expense_status_to_needs_revision()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $data["voucher_id"] = $this->input->post("voucher_id");
            $data["transition"] = $this->input->post("transition");
            $data["comment"] = $this->input->post("comment");
            if ($data["transition"] == 1) {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $this->expense->fetch($this->input->post("expense_id"));
                $voucher_header_id = $this->expense->get_field("voucher_header_id");
                $current_status = $this->expense->get_field("status");
                if ($current_status != "needs_revision") {
                    $ids = [];
                    $ids[] = $this->input->post("expense_id");
                    $this->load->model("expense_status_note", "expense_status_notefactory");
                    $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                    $this->expense_status_note->set_field("expense_id", $this->input->post("expense_id"));
                    $this->expense_status_note->set_field("transition", "needs_revision");
                    if (!empty($data["comment"])) {
                        $this->expense_status_note->set_field("note", $data["comment"]);
                    }
                    if ($this->expense_status_note->insert()) {
                        $this->load->model("expense", "expensefactory");
                        $this->expense = $this->expensefactory->get_instance();
                        $response["status"] = $this->expense->update_expenses_approval_status_by_id("needs_revision", join(",", $ids));
                        if ($response["status"]) {
                            $response["expense_status"] = "needs_revision";
                            $this->send_notification_to_expense_creator($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_needs_revision");
                            $this->update_transaction($this->input->post("expense_id"), "revert");
                        }
                    } else {
                        $response["validationErrors"] = $this->expense_status_note->get("validationErrors");
                    }
                }
            } else {
                $expense = $this->voucher_header->fetch_expense_details($data["voucher_id"]);
                $data["expense_id"] = $expense["expenseID"];
                $response["html"] = $this->load->view("expenses/change_expense_status", $data, true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function move_expense_status_to_cancelled()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $data["voucher_id"] = $this->input->post("voucher_id");
            $data["transition"] = $this->input->post("transition");
            $data["comment"] = $this->input->post("comment");
            if ($data["transition"] == 1) {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $this->expense->fetch($this->input->post("expense_id"));
                $voucher_header_id = $this->expense->get_field("voucher_header_id");
                $current_status = $this->expense->get_field("status");
                if ($current_status != "cancelled") {
                    $ids = [];
                    $ids[] = $this->input->post("expense_id");
                    $this->load->model("expense_status_note", "expense_status_notefactory");
                    $this->expense_status_note = $this->expense_status_notefactory->get_instance();
                    $this->expense_status_note->set_field("expense_id", $this->input->post("expense_id"));
                    $this->expense_status_note->set_field("transition", "cancelled");
                    if (!empty($data["comment"])) {
                        $this->expense_status_note->set_field("note", $data["comment"]);
                    }
                    if ($this->expense_status_note->insert()) {
                        $this->load->model("expense", "expensefactory");
                        $this->expense = $this->expensefactory->get_instance();
                        $response["status"] = $this->expense->update_expenses_approval_status_by_id("cancelled", join(",", $ids));
                        if ($response["status"]) {
                            $response["expense_status"] = "cancelled";
                            $this->send_notification_to_expense_creator($voucher_header_id, $this->input->post("expense_id"), "expense_status_to_cancelled");
                            $this->update_transaction($this->input->post("expense_id"), "revert");
                        }
                    } else {
                        $response["validationErrors"] = $this->expense_status_note->get("validationErrors");
                    }
                }
            } else {
                $expense = $this->voucher_header->fetch_expense_details($data["voucher_id"]);
                $data["expense_id"] = $expense["expenseID"];
                $response["html"] = $this->load->view("expenses/change_expense_status", $data, true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    private function send_notification_to_expense_creator($voucher_id, $expense_title = "", $action = "edit_expense")
    {
        $this->load->model(["money_preference"]);
        $notify_users_expense_by_email = $this->money_preference->get_value_by_key("notifyUsersExpenseByEmail");
        $this->voucher_header->fetch($voucher_id);
        $creator_id = $this->voucher_header->get_field("createdBy");
        $created_on = date("Y-m-d", strtotime($this->voucher_header->get_field("createdOn")));
        $modified_on = date("Y-m-d", strtotime($this->voucher_header->get_field("modifiedOn")));
        $login_user_id = $this->session->userdata("AUTH_user_id");
        $result = false;
        if ($creator_id != $login_user_id) {
            $this->load->library("system_notification");
            $notifications_data = ["to" => $creator_id, "objectName" => "expense", "object" => $action, "object_id" => $voucher_id, "object_title" => $expense_title, "objectModelCode" => "", "targetUser" => $creator_id, "user_logged_in_name" => $this->session->userdata("AUTH_userProfileName")];
            $this->system_notification->notification_add($notifications_data);
            if ($notify_users_expense_by_email["keyValue"] == 1) {
                $this->load->library("email_notifications");
                $this->load->model("email_notification_scheme");
                $notifications_emails = $this->email_notification_scheme->get_emails("add_expense", "expense", ["id" => $voucher_id, "user_ids" => (array) $login_user_id]);
                extract($notifications_emails);
                $to_emails = $notifications_emails["to_emails"] ?? [];
                $cc_emails = $notifications_emails["cc_emails"] ?? [];
                $email_notifications_data = ["to" => $to_emails, "object" => $action, "object_id" => $voucher_id, "objectName" => "add_expense", "cc" => $cc_emails, "expenseId" => $expense_title, "userLoggedInName" => $this->session->userdata("AUTH_userProfileName"), "createdOn" => $created_on, "modifiedOn" => $modified_on, "fromLoggedUser" => $this->is_auth->get_fullname()];
                $this->email_notifications->notify($email_notifications_data);
            }
        }
        return $result;
    }
    private function send_notification_to_groups_users($voucher_id, $expense_title, $action = "add_expense")
    {
        $this->load->model(["money_preference"]);
        $notify_user_group_expense = $this->money_preference->get_value_by_key("notifyUserGroupExpense");
        $notify_uers_expense = $this->money_preference->get_value_by_key("notifyUsersExpense");
        $notify_users_expense_by_email = $this->money_preference->get_value_by_key("notifyUsersExpenseByEmail");
        $this->voucher_header->fetch($voucher_id);
        $creator_id = $this->voucher_header->get_field("createdBy");
        $created_on = date("Y-m-d", strtotime($this->voucher_header->get_field("createdOn")));
        $modified_on = date("Y-m-d", strtotime($this->voucher_header->get_field("modifiedOn")));
        $login_user_id = $this->session->userdata("AUTH_user_id");
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
            if (($action == "edit_expense" || $action == "expense_status_to_open") && $login_user_id != $creator_id && ($key = array_search($creator_id, $all_users)) !== false) {
                unset($all_users[$key]);
            }
            $this->load->model("notification", "notificationfactory");
            $this->notification = $this->notificationfactory->get_instance();
            $this->load->library("system_notification");
            foreach ($all_users as $user_id) {
                $notifications_data = ["to" => $user_id, "objectName" => "expense", "object" => $action, "object_id" => $voucher_id, "object_title" => $expense_title, "objectModelCode" => "", "targetUser" => $user_id, "user_logged_in_name" => $this->session->userdata("AUTH_userProfileName")];
                $this->system_notification->notification_add($notifications_data);
            }
            if ($notify_users_expense_by_email["keyValue"] == 1) {
                $this->load->library("email_notifications");
                $this->load->model("email_notification_scheme");
                $notifications_emails = $this->email_notification_scheme->get_emails("add_expense", "expense", ["id" => $voucher_id, "user_ids" => $all_users]);
                extract($notifications_emails);
                $to_emails = $notifications_emails["to_emails"] ?? [];
                $cc_emails = $notifications_emails["cc_emails"] ?? [];
                $email_notifications_data = ["to" => $to_emails, "object" => $action, "object_id" => $voucher_id, "objectName" => "add_expense", "cc" => $cc_emails, "expenseId" => $expense_title, "userLoggedInName" => $this->session->userdata("AUTH_userProfileName"), "createdOn" => $created_on, "modifiedOn" => $modified_on, "fromLoggedUser" => $this->is_auth->get_fullname()];
                $this->email_notifications->notify($email_notifications_data);
            }
            return true;
        } else {
            return false;
        }
    }
    private function send_notification_to_approve_expense_groups_users($voucher_id, $expense_title, $action = "expense_status_to_approved")
    {
        $this->load->model(["money_preference"]);
        $notify_users_group_to_approve_expense = $this->money_preference->get_value_by_key("notifyUsersGroupToApproveExpense");
        $notify_users_to_approve_expense = $this->money_preference->get_value_by_key("notifyUsersToApproveExpense");
        $notify_users_expense_by_email = $this->money_preference->get_value_by_key("notifyUsersExpenseByEmail");
        $this->voucher_header->fetch($voucher_id);
        $creator_id = $this->voucher_header->get_field("createdBy");
        $created_on = date("Y-m-d", strtotime($this->voucher_header->get_field("createdOn")));
        $modified_on = date("Y-m-d", strtotime($this->voucher_header->get_field("modifiedOn")));
        $login_user_id = $this->session->userdata("AUTH_user_id");
        $all_users = [];
        $users_groups = [];
        $users = [];
        if ($notify_users_group_to_approve_expense["keyValue"]) {
            $users_groups = array_keys($this->user->load_users_ids_in_groups($notify_users_group_to_approve_expense["keyValue"]));
        }
        if ($notify_users_to_approve_expense["keyValue"]) {
            $users = explode(",", $notify_users_to_approve_expense["keyValue"]);
        }
        $all_users = array_unique(array_merge($users_groups, $users), SORT_REGULAR);
        if (($key = array_search($login_user_id, $all_users)) !== false) {
            unset($all_users[$key]);
        }
        if ($all_users) {
            if ($action == "expense_status_to_approved" && $login_user_id != $creator_id && ($key = array_search($creator_id, $all_users)) !== false) {
                unset($all_users[$key]);
            }
            $this->load->model("notification", "notificationfactory");
            $this->notification = $this->notificationfactory->get_instance();
            $this->load->library("system_notification");
            foreach ($all_users as $user_id) {
                $notifications_data = ["to" => $user_id, "objectName" => "expense", "object" => $action, "object_id" => $voucher_id, "object_title" => $expense_title, "objectModelCode" => "", "targetUser" => $user_id, "user_logged_in_name" => $this->session->userdata("AUTH_userProfileName")];
                $this->system_notification->notification_add($notifications_data);
            }
            if ($notify_users_expense_by_email["keyValue"] == 1) {
                $this->load->library("email_notifications");
                $this->load->model("email_notification_scheme");
                $notifications_emails = $this->email_notification_scheme->get_emails("add_expense", "expense", ["id" => $voucher_id, "user_ids" => $all_users]);
                extract($notifications_emails);
                $to_emails = $notifications_emails["to_emails"] ?? [];
                $cc_emails = $notifications_emails["cc_emails"] ?? [];
                $email_notifications_data = ["to" => $to_emails, "object" => $action, "object_id" => $voucher_id, "objectName" => "add_expense", "cc" => $cc_emails, "expenseId" => $expense_title, "userLoggedInName" => $this->session->userdata("AUTH_userProfileName"), "createdOn" => $created_on, "modifiedOn" => $modified_on, "fromLoggedUser" => $this->is_auth->get_fullname()];
                $this->email_notifications->notify($email_notifications_data);
            }
            return true;
        } else {
            return false;
        }
    }
    public function get_expense_notes()
    {
        $id = $this->input->post("id");
        $this->load->model("expense_status_note", "expense_status_notefactory");
        $this->expense_status_note = $this->expense_status_notefactory->get_instance();
        $this->load->helper("text");
        $data = [];
        $data["expense_notes"] = $this->expense_status_note->fetch_all_expense_notes($id);
        if (!empty($data)) {
            $response["nbOfNotesHistory"] = $this->expense_status_note->count_all_expense_notes($id);
            $response["html"] = $this->load->view("expenses/comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function bulk_change_expense_status_to_open()
    {
        $response = [];
        $response["flag"] = true;
        if (!$this->input->post(NULL)) {
            $this->set_flashmessage("information", $this->lang->line("no_data"));
        } else {
            $grid_data = $this->input->post("gridData");
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            foreach ($grid_data["voucherIds"] as $key => $id) {
                $this->expense->fetch("voucher_header_id = " . $id);
                $current_status = $this->expense->get_field("status");
                $billing_status = $this->expense->get_field("billingStatus");
                if ($current_status != "open" && !in_array($billing_status, ["invoiced", "reimbursed", "to-invoice"])) {
                    $this->expense->set_field("status", "open");
                    $response["status"] = $this->expense->update();
                    $this->update_transaction($this->expense->get_field("id"), "revert");
                } else {
                    $response["flag"] = false;
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function bulk_change_expense_status_to_approved()
    {
        $response = [];
        $response["flag"] = true;
        if (!$this->input->post(NULL)) {
            $this->set_flashmessage("information", $this->lang->line("no_data"));
        } else {
            $grid_data = $this->input->post("gridData");
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            foreach ($grid_data["voucherIds"] as $key => $id) {
                $this->expense->fetch("voucher_header_id = " . $id);
                $current_status = $this->expense->get_field("status");
                if ($current_status != "approved") {
                    $this->expense->set_field("status", "approved");
                    $response["status"] = $this->expense->update();
                    $this->update_transaction($this->expense->get_field("id"));
                } else {
                    $response["flag"] = false;
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function update_transaction($id, $action = "add")
    {
        $this->expense->fetch($id);
        $expenses_data = $this->expense->get_fields();
        if ($action != "add") {
            $this->voucher_detail->delete(["where" => ["voucher_header_id", $expenses_data["voucher_header_id"]]]);
        } else {
            $this->load->model("exchange_rate");
            $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
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
            $paid_through_account = $this->account->fetch_account($expenses_data["paid_through"]);
            $this->voucher_detail->set_field("local_amount", $expenses_data["amount"] * $data["rates"][$paid_through_account["currency_id"]]);
            $this->voucher_detail->set_field("foreign_amount", $expenses_data["amount"]);
            $this->voucher_detail->set_field("description", "EXP-" . $this->expense->get_field("id"));
            if ($this->voucher_detail->insert()) {
                $expense_local_amount = 0;
                $expense_foreign_amount = 0;
                $expense_account = $this->account->fetch_account($expenses_data["expense_account"]);
                if (isset($expenses_data["tax_id"]) && !empty($expenses_data["tax_id"])) {
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $tax_account = $this->tax->get_tax_account($expenses_data["tax_id"]);
                    $expense_amount = $expenses_data["amount"] * 100 / ($tax_account["percentage"] + 100);
                    $tax_amount = $expenses_data["amount"] - $expense_amount;
                    $tax_local_amount = $tax_amount * $data["rates"][$paid_through_account["currency_id"]];
                    $tax_foreign_amount = $tax_local_amount / $data["rates"][$tax_account["currency_id"]];
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
    public function convert_expense_billingStatus_to_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = ["result" => false];
            $client_account_id = $this->input->post("client_account_id");
            $expensesIds = $this->input->post("expensesIds");
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            if (isset($expensesIds)) {
                foreach ($expensesIds as $val) {
                    $this->expense->reset_fields();
                    $this->expense->fetch($val);
                    $this->expense->set_field("billingStatus", "to-invoice");
                    $this->expense->set_field("client_account_id", $client_account_id);
                    $response["result"] = $this->expense->update();
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function invoice_add($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoices") . " | " . $this->lang->line("money"));
        $this->load->helper("encrypt_decrypt_helper");
        $data = $this->prepare_invoice($id, false);
        $active = site_url("vouchers/invoice_add");
        $data["tabsNLogs"] = $this->_get_invoice_tabs_view_vars($id, $active);
        $data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
        $this->load->view("invoices/invoice_form", $data);
    }
    public function debit_note_add($id = 0)
    {
        if (empty($id) || !is_numeric($id) || !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("debit_notes") . " | " . $this->lang->line("money"));
        $this->load->helper("encrypt_decrypt_helper");
        $data = $this->prepare_invoice($id, true);
        $data["is_debit_note"] = true;
        $data["is_create_debit_note"] = true;
        $active = site_url("vouchers/invoice_add");
        $data["tabsNLogs"] = $this->_get_invoice_tabs_view_vars(0, $active);
        $data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
        $this->load->view("invoices/invoice_form", $data);
    }
    public function convert_quote_to_invoice($id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoices") . " | " . $this->lang->line("money"));
        if (!is_numeric($id) || $id <= 0) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/quotes_list");
        }
        $voucher_row = $this->validate_voucher($id, true);
        if (!$voucher_row) {
            $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
            redirect("vouchers/quotes_list");
        }
        $this->load->model("quote_header", "quote_headerfactory");
        $this->load->model("quote_detail", "quote_detailfactory");
        $quote_header = $this->quote_headerfactory->get_instance();
        $quote_detail = $this->quote_detailfactory->get_instance();
        $quote_header->fetch(["voucher_header_id" => $id]);
        $quote_id = $quote_header->get_field("id");
        $current_status = $quote_header->get_field("paidStatus");
        $quote_data_header = $quote_header->get_quote_header_by_id($quote_id);
        $quote_data_details = $quote_detail->get_quote_details_by_id($quote_id);
        if (!$quote_data_header || !$quote_data_details || in_array($current_status, ["rejected", "cancelled", "invoiced", "open"])) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("invalid_record")));
            redirect("vouchers/quotes_list");
        }
        $data = $this->prepare_invoice(0, false);
        $data["create_invoice_from_quote_id"] = $quote_id;
        $active = site_url("vouchers/invoice_add");
        $data["tabsNLogs"] = $this->_get_invoice_tabs_view_vars(0, $active);
        $this->load->view("invoices/invoice_form", $data);
    }
    public function invoice_edit($id = 0)
    {
        $is_debit_note = false;
        if (is_numeric($id) && 0 < $id) {
            $voucher_row = $this->validate_voucher($id, true);
            if ($voucher_row["voucherType"] == "DBN") {
                $is_debit_note = true;
            }
            if (!$voucher_row) {
                $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
                redirect("vouchers/invoices_list");
            }
        } else {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line($is_debit_note ? "debit_notes" : "invoices") . " | " . $this->lang->line("money"));
        $this->load->helper("encrypt_decrypt_helper");
        $data = $this->prepare_invoice($id, $is_debit_note);
        $data["is_debit_note"] = $is_debit_note;
        $active = site_url("vouchers/invoice_edit/");
        $data["tabsNLogs"] = $this->_get_invoice_tabs_view_vars($id, $active);
        $data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
        $this->load->view("invoices/invoice_form", $data);
    }
    private function prepare_invoice($id, $is_debit_note)
    {
        if ($this->license_availability === false) {
            $this->set_flashmessage("error", $this->licensor->get_license_message());
            redirect("vouchers/invoices_list/");
        }
        $this->load->model("user_preference");
        $this->load->model(["money_preference"]);
        $system_preferences = $this->session->userdata("systemPreferences");
        $organization_id = $this->session->userdata("organizationID");
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($organization_id);
        if (empty($exchange_rates)) {
            redirect("setup/rate_between_money_currencies");
        }
        $time_tracking_sales_account_arr = unserialize($system_preferences["timeTrackingSalesAccount"]);
        $time_tracking_sales_account = $time_tracking_sales_account_arr[$organization_id];
        if (!$time_tracking_sales_account || empty($time_tracking_sales_account)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("you_have_to_set_time_tracking_sales_account")));
            redirect("setup/time_tracking_sales_account");
        }
        $money_language = $this->user_preference->get_value("money_language");
        $money_language_key = $money_language === "" ? 0 : $money_language;
        if (0 < $id) {
            $data["invoice_id"] = $id;
        }
        $invoice_lang = $this->money_preference->get_values_by_group("InvoiceLanguage");
        foreach ($invoice_lang as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$money_language_key];
        }
        $money_preference = $this->money_preference->get_key_groups();
        $data["activate_tax"] = $money_preference["ActivateTaxesinInvoices"]["TEnabled"];
        $data["activate_discount"] = unserialize($money_preference["ActivateDiscountinInvoices"]["DEnabled"])[$organization_id]["enabled"];
        if (!isset($data["activate_discount"])) {
            redirect("setup/configure_invoice_discount");
        }
        $data["display_item_date"] = $money_preference["InvoiceItems"]["DisplayItemDate"];
        $partners_commissions = unserialize($system_preferences["partnersCommissions"]);
        if (isset($partners_commissions[$organization_id]) && !empty($partners_commissions[$organization_id])) {
            $data["partners_commissions"] = $partners_commissions[$organization_id];
        }
        $system_commission_account = false;
        $system_partner_commission_asset_account = false;
        if ($data["partners_commissions"] == "yes") {
            $system_commission_account = isset($system_preferences["systemCommissionAccount"]) ? unserialize($system_preferences["systemCommissionAccount"]) : false;
            if ($system_commission_account && isset($system_commission_account[$organization_id])) {
                $system_partner_commission_asset_account = $system_commission_account[$organization_id];
            }
            if (!$system_partner_commission_asset_account || empty($system_partner_commission_asset_account)) {
                $this->set_flashmessage("warning", sprintf($this->lang->line("you_have_to_set_the_default_global_partner_shares_account")));
                redirect("setup/global_partner_shares_account");
            }
        }
        $this->load->model("organization", "organizationfactory");
        $this->organization = $this->organizationfactory->get_instance();
        $data["e_invoicing"] = $this->organization->check_if_einvoice_active($organization_id);
        $data["e_invoicing_key"] = $this->organization->get_einvoice_key($organization_id);
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $invoiceNumberPrefix = unserialize($system_preferences[$is_debit_note ? "debitNoteNumberPrefix" : "invoiceNumberPrefix"]);
        $data["invoice_number_prefix"] = $invoiceNumberPrefix[$organization_id];
        $data["auto_generate_invoice_number"] = $this->invoice_header->auto_generate_rf($is_debit_note, true, $data["e_invoicing"], $data["e_invoicing_key"]);
        $this->load->model("tax", "taxfactory");
        $this->tax = $this->taxfactory->get_instance();
        $this->load->model("discount", "discountfactory");
        $this->discount = $this->discountfactory->get_instance();
        $this->load->model("invoice_transaction_type", "invoice_transaction_typefactory");
        $this->invoice_transaction_type = $this->invoice_transaction_typefactory->get_instance();
        $data["transaction_types"] = $this->invoice_transaction_type->get_invoice_transaction_type_list();
        $this->load->model("payment_method", "payment_methodfactory");
        $this->payment_method = $this->payment_methodfactory->get_instance();
        $data["payment_methods"] = $this->payment_method->get_payment_method_list();
        $this->load->model(["term", "invoice_note"]);
        $data["terms"] = $this->term->get_terms($money_language);
        $data["notes_descriptions"] = $this->invoice_note->load_all();
        $data["discounts"] = $this->discount->get_discounts();
        $data["rates"] = $exchange_rates;
        $data["money_language"] = $money_language;
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["countries"] = $this->country->load_countries_list();
        if ($data["e_invoicing"] && $data["activate_discount"] == "invoice_level_before_tax") {
            redirect("setup/configure_invoice_discount");
        }
        $this->includes("vue/node_modules/primevue/resources/primevue.min", "css");
        $this->includes("vue/node_modules/primevue/resources/themes/bootstrap4-light-blue/theme", "css");
        $this->includes("vue/node_modules/primeicons/primeicons", "css");
        $this->includes("vue/node_modules/primeflex/primeflex", "css");
        $this->includes("jquery/timemask", "js");
        $this->includes("money/js/invoice_form", "js");
        $this->includes("vue/node_modules/vue/dist/vue.global.prod", "js");
        $this->includes("vue/node_modules/axios/dist/axios.min", "js");
        $this->includes("vue/node_modules/primevue/core/core.min", "js");
        $this->includes("vue/node_modules/primevue/utils/utils.min", "js");
        $this->includes("vue/node_modules/primevue/api/api", "js");
        $this->includes("vue/node_modules/primevue/config/config.min", "js");
        $this->includes("vue/node_modules/primevue/ripple/ripple.min", "js");
        $this->includes("vue/node_modules/primevue/tooltip/tooltip.min", "js");
        $this->includes("vue/node_modules/primevue/overlayeventbus/overlayeventbus.min", "js");
        $this->includes("vue/node_modules/primevue/overlaypanel/overlaypanel.min", "js");
        $this->includes("vue/node_modules/primevue/fileupload/fileupload.min", "js");
        $this->includes("vue/node_modules/primevue/confirmdialog/confirmdialog.min", "js");
        $this->includes("vue/node_modules/primevue/button/button.min", "js");
        $this->includes("vue/node_modules/primevue/virtualscroller/virtualscroller.min", "js");
        $this->includes("vue/node_modules/primevue/autocomplete/autocomplete.min", "js");
        $this->includes("vue/node_modules/primevue/dialog/dialog.min", "js");
        $this->includes("vue/node_modules/primevue/inputtext/inputtext.min", "js");
        $this->includes("vue/node_modules/quill/dist/quill.min", "js");
        $this->includes("vue/node_modules/primevue/editor/editor.min", "js");
        $this->includes("vue/node_modules/primevue/accordion/accordion.min", "js");
        $this->includes("vue/node_modules/primevue/accordiontab/accordiontab.min", "js");
        $this->includes("vue/node_modules/primevue/dropdown/dropdown.min", "js");
        $this->includes("vue/node_modules/primevue/textarea/textarea.min", "js");
        $this->includes("vue/node_modules/primevue/skeleton/skeleton.min", "js");
        $this->includes("vue/node_modules/primevue/inputnumber/inputnumber.min", "js");
        $this->includes("vue/node_modules/primevue/panel/panel.min", "js");
        $this->includes("vue/node_modules/primevue/confirmationservice/confirmationservice.min", "js");
        $this->includes("vue/node_modules/@ckeditor/ckeditor5-build-classic/build/ckeditor", "js");
        $this->includes("vue/node_modules/@ckeditor/ckeditor5-vue/dist/ckeditor", "js");
        return $data + $this->return_notify_before_data($id, $this->invoice_header->get("_table"));
    }
    private function _get_invoice_tabs_view_vars($id, $active = "")
    {
        $data["subNavItems"] = [];
        $data["activeSubNavItem"] = $active;
        $data["id"] = $id;
        if ($id) {
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->load->model("item_commission");
            $invoice_id = $this->invoice_header->load(["where" => ["voucher_header_id", $id]])["id"];
            $invoice_partners = $this->item_commission->fetch_commissions($invoice_id);
            $data["subNavItems"][site_url("vouchers/invoice_edit/")] = $this->lang->line("public_info");
            $data["subNavItems"][site_url("vouchers/invoice_payments_made/")] = $this->lang->line("payments_made");
            $data["subNavItems"][site_url("vouchers/invoice_related_documents/")] = $this->lang->line("related_documents");
            if ($this->is_commissions_enabled() && $this->is_settlements_per_invoice_enabled() && 0 < count($invoice_partners)) {
                $data["subNavItems"][site_url("vouchers/invoice_partners_settlements/")] = $this->lang->line("partners_settlements");
            }
            return $data;
        }
        $data["subNavItems"][site_url("vouchers/invoice_add")] = $this->lang->line("invoice");
        return $data;
    }
    public function change_invoice_number()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = ["status" => false];
            $count = $this->voucher_header->validate_refNum("INV", $this->input->post("editRefNum"), $this->input->post("voucherHeaderId"));
            if ($count["count"] == 0) {
                $response["autoGenerateNb"] = $this->input->post("editRefNum");
                $systemPreferences = $this->session->userdata("systemPreferences");
                $invoiceNumberPrefix = unserialize($systemPreferences["invoiceNumberPrefix"]);
                if ($this->input->post("editPrefix") && $this->input->post("editPrefix") != $invoiceNumberPrefix[$this->session->userdata("organizationID")]) {
                    $invoiceNumberPrefix[$this->session->userdata("organizationID")] = $this->input->post("editPrefix");
                    $dataSet = ["groupName" => "InvoiceValues", "keyName" => "invoiceNumberPrefix", "keyValue" => serialize($invoiceNumberPrefix)];
                    $this->load->model("system_preference");
                    if ($this->system_preference->set_value_by_key("InvoiceValues", "invoiceNumberPrefix", serialize($invoiceNumberPrefix))) {
                        $response["status"] = true;
                        $response["prefix"] = $this->input->post("editPrefix");
                    } else {
                        $response["msg"] = $this->lang->line("error");
                    }
                } else {
                    $response["status"] = true;
                    $response["prefix"] = $this->input->post("editPrefix");
                }
            } else {
                $response["msg"] = $this->lang->line("invoice_number_already_exists");
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function convert_invoice_to_open()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $response["status"] = 202;
            $voucherID = $this->input->post("voucherID");
            $invoice = $this->voucher_header->load_invoice_voucher($voucherID);
            $systemPreferences = $this->session->userdata("systemPreferences");
            if ($this->is_commissions_enabled()) {
                if (isset($systemPreferences["systemCommissionAccount"])) {
                    $systemCommissionAccount = unserialize($systemPreferences["systemCommissionAccount"]);
                    if (isset($systemCommissionAccount[$this->session->userdata("organizationID")]) && !empty($systemCommissionAccount[$this->session->userdata("organizationID")])) {
                        $system_partner_commission_asset_account = $systemCommissionAccount[$this->session->userdata("organizationID")];
                    } else {
                        $response["status"] = 102;
                    }
                } else {
                    $response["status"] = 102;
                }
            }
            if ($response["status"] != 102) {
                $allowed_decimal_format = $this->config->item("allowed_decimal_format");
                if ($invoice["paidStatus"] == "open") {
                    $response["status"] = 101;
                } else {
                    if (!in_array($invoice["paidStatus"], ["partially paid", "paid"])) {
                        $this->load->model("exchange_rate");
                        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
                        $data["rates"] = $exchange_rates;
                        $this->voucher_detail->set_field("voucher_header_id", $voucherID);
                        $this->voucher_detail->set_field("account_id", $invoice["clientAccountId"]);
                        $this->load->model("account", "accountfactory");
                        $this->account = $this->accountfactory->get_instance();
                        $client_account = $this->account->fetch_account($invoice["clientAccountId"]);
                        $this->voucher_detail->set_field("drCr", "D");
                        $this->voucher_detail->set_field("local_amount", $invoice["total"] * $data["rates"][$client_account["currency_id"]] * 1);
                        $this->voucher_detail->set_field("foreign_amount", $invoice["total"]);
                        $this->voucher_detail->set_field("description", $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"]);
                        if ($this->voucher_detail->insert()) {
                            $this->load->model("invoice_detail", "invoice_detailfactory");
                            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                            $invoice_data = $this->invoice_detail->load_all(["where" => ["invoice_header_id", $invoice["invoice_id"]]]);
                            if (!empty($invoice_data)) {
                                $item_accounts = [];
                                $tax_accounts = [];
                                $discount_accounts = [];
                                $commission_accounts = [];
                                $item_commissions_data_array = [];
                                if ($this->is_commissions_enabled()) {
                                    $this->load->model("item_commission");
                                    $item_commissions_data_array = $this->item_commission->fetch_commissions($invoice["invoice_id"]);
                                }
                                foreach ($invoice_data as $value) {
                                    $amount = (double) number_format($value["quantity"] * $value["unitPrice"] * 1, $allowed_decimal_format, ".", "");
                                    if (array_key_exists($value["account_id"], $item_accounts)) {
                                        $item_accounts[$value["account_id"]]["local_amount"] = $item_accounts[$value["account_id"]]["local_amount"] + (double) number_format($amount * $data["rates"][$client_account["currency_id"]] * 1, $allowed_decimal_format, ".", "");
                                        $item_accounts[$value["account_id"]]["foreign_amount"] = $item_accounts[$value["account_id"]]["foreign_amount"] + $amount;
                                    } else {
                                        $item_accounts[$value["account_id"]] = ["voucher_header_id" => $voucherID, "account_id" => $value["account_id"], "drCr" => "C", "local_amount" => (double) number_format($amount * $data["rates"][$client_account["currency_id"]] * 1, $allowed_decimal_format, ".", ""), "foreign_amount" => $amount, "description" => $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"]];
                                    }
                                    if (!empty($value["discountPercentage"])) {
                                        $discount_account["account_id"] = $value["account_id"];
                                        $discount_amount = (double) (number_format($amount * $value["discountPercentage"], $allowed_decimal_format, ".", "") * 1) / 100;
                                        if (array_key_exists($discount_account["account_id"], $discount_accounts)) {
                                            $discount_accounts[$discount_account["account_id"]]["local_amount"] = $discount_accounts[$discount_account["account_id"]]["local_amount"] + number_format($discount_amount * $data["rates"][$client_account["currency_id"]], $allowed_decimal_format, ".", "") * 1;
                                            $discount_accounts[$discount_account["account_id"]]["foreign_amount"] = $discount_accounts[$discount_account["account_id"]]["foreign_amount"] + $discount_amount;
                                        } else {
                                            $discount_accounts[$discount_account["account_id"]] = ["voucher_header_id" => $voucherID, "account_id" => $value["account_id"], "drCr" => "D", "local_amount" => number_format($discount_amount * $data["rates"][$client_account["currency_id"]], $allowed_decimal_format, ".", "") * 1, "foreign_amount" => $discount_amount, "description" => $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"] . " -Discount"];
                                        }
                                    }
                                    if (!empty($value["tax_id"])) {
                                        $this->load->model("tax", "taxfactory");
                                        $this->tax = $this->taxfactory->get_instance();
                                        $tax_account = $this->tax->get_tax_account($value["tax_id"]);
                                        if (!empty($value["discountPercentage"])) {
                                            $tax_amount = number_format(bcdiv(number_format(($amount - $discount_amount) * $value["percentage"] * 1, $allowed_decimal_format, ".", ""), 100, $allowed_decimal_format + 1), $allowed_decimal_format, ".", "");
                                        } else {
                                            $tax_amount = number_format(bcdiv(number_format($amount * $value["percentage"] * 1, $allowed_decimal_format, ".", ""), 100, $allowed_decimal_format + 1), $allowed_decimal_format, ".", "");
                                        }
                                        if (array_key_exists($tax_account["account_id"], $tax_accounts)) {
                                            $tax_accounts[$tax_account["account_id"]]["local_amount"] = $tax_accounts[$tax_account["account_id"]]["local_amount"] + number_format($tax_amount * $data["rates"][$client_account["currency_id"]], $allowed_decimal_format, ".", "") * 1;
                                            $tax_accounts[$tax_account["account_id"]]["foreign_amount"] = $tax_accounts[$tax_account["account_id"]]["foreign_amount"] + $tax_amount;
                                        } else {
                                            $tax_accounts[$tax_account["account_id"]] = ["voucher_header_id" => $voucherID, "account_id" => $tax_account["account_id"], "drCr" => "C", "local_amount" => number_format($tax_amount * $data["rates"][$client_account["currency_id"]], $allowed_decimal_format, ".", "") * 1, "foreign_amount" => $tax_amount, "description" => $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"]];
                                        }
                                    }
                                    if (!empty($item_commissions_data_array)) {
                                        $system_partner_commission_asset_account_data = ["voucher_header_id" => $voucherID, "account_id" => $system_partner_commission_asset_account, "drCr" => "D", "local_amount" => "", "foreign_amount" => "", "description" => $this->lang->line("partners_shares") . " - " . $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"]];
                                        $total_local_amount = 0;
                                        $total_foreign_amount = 0;
                                        foreach ($item_commissions_data_array as $item_commissions_value) {
                                            if ($item_commissions_value["invoice_details_id"] == $value["id"]) {
                                                $amount = number_format($value["quantity"] * $value["unitPrice"], $allowed_decimal_format, ".", "") * 1;
                                                if (!empty($value["discountPercentage"])) {
                                                    $amount = $amount - number_format($amount * $value["discountPercentage"] * 1, $allowed_decimal_format, ".", "") / 100;
                                                }
                                                $itemPartnerShareAccount = $this->account->fetch_account($item_commissions_value["account_id"]);
                                                $localAmount = number_format(number_format($amount * $data["rates"][$client_account["currency_id"]], $allowed_decimal_format, ".", "") * 1 / $data["rates"][$itemPartnerShareAccount["currency_id"]] * $item_commissions_value["commission"], $allowed_decimal_format, ".", "") * 1 / 100;
                                                $foreignAmount = number_format($amount * $item_commissions_value["commission"] * 1, $allowed_decimal_format, ".", "") / 100;
                                                $total_local_amount += $localAmount;
                                                $total_foreign_amount += $localAmount;
                                                $commission_accounts[] = ["voucher_header_id" => $voucherID, "account_id" => $item_commissions_value["account_id"], "drCr" => "C", "local_amount" => $localAmount, "foreign_amount" => $foreignAmount, "description" => $this->lang->line("partners_shares") . " - " . $invoice["prefix"] . $invoice["refNum"] . $invoice["suffix"]];
                                            }
                                        }
                                        $system_partner_commission_asset_account_data["local_amount"] = $total_local_amount;
                                        $globalPartnerShareAccount = $this->account->fetch_account($system_partner_commission_asset_account);
                                        $system_partner_commission_asset_account_data["foreign_amount"] = number_format($total_foreign_amount / $data["rates"][$globalPartnerShareAccount["currency_id"]], $allowed_decimal_format, ".", "");
                                        $commission_accounts[] = $system_partner_commission_asset_account_data;
                                    }
                                }
                                if ($result = $this->voucher_detail->insert_batch($item_accounts)) {
                                    if (!empty($discount_accounts)) {
                                        $this->voucher_detail->insert_batch($discount_accounts);
                                    }
                                    if (!empty($tax_accounts) && $this->voucher_detail->insert_batch($tax_accounts)) {
                                        $result = true;
                                    }
                                    if ($result && !empty($commission_accounts)) {
                                        $this->voucher_detail->insert_batch($commission_accounts);
                                    }
                                }
                                if ($result) {
                                    $this->load->model("invoice_header", "invoice_headerfactory");
                                    $this->invoice_header = $this->invoice_headerfactory->get_instance();
                                    if ($this->invoice_header->change_invoice_status($invoice["invoice_id"], "open")) {
                                        $this->load->model("invoice_detail", "invoice_detailfactory");
                                        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                                        $invoice_details = $this->invoice_detail->load_all_invoice_details($invoice["invoice_id"], "");
                                        $this->set_time_logs_status($invoice_details, "invoiced");
                                        $this->set_expense_status($invoice_details, "invoiced");
                                        $response["status"] = 101;
                                    }
                                } else {
                                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucherID]]);
                                }
                            }
                        }
                    }
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function set_invoice_as_draft()
    {
        $this->change_invoice_status("draft");
    }
    public function cancel_invoice()
    {
        $this->change_invoice_status("cancelled");
    }
    private function change_invoice_status($status)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $response["status"] = 202;
            $voucherID = $this->input->post("voucherID");
            $invoice = $this->voucher_header->load_invoice_voucher($voucherID);
            if (!in_array($invoice["paidStatus"], ["partially paid", "paid"])) {
                $this->load->model("invoice_header", "invoice_headerfactory");
                $this->invoice_header = $this->invoice_headerfactory->get_instance();
                if ($this->invoice_header->change_invoice_status($invoice["invoice_id"], $status)) {
                    $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucherID]]);
                    $this->load->model("invoice_detail", "invoice_detailfactory");
                    $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                    $invoice_details = $this->invoice_detail->load_all_invoice_details($invoice["invoice_id"], "");
                    $this->set_time_logs_status($invoice_details, "to-invoice");
                    $this->set_expense_status($invoice_details, "to-invoice");
                    $response["status"] = 101;
                }
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function invoices_list($clientId = 0, $invoice_id = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoices") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = "Invoice_Header";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"]));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                }
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            $response = array_merge($response, $this->voucher_header->k_load_all_invoices($filter, $sortable));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data["partnersCommissions"] = $this->is_commissions_enabled() ? "yes" : "no";
            $data["invoiceVoucherTypes"] = ["" => "", "INV" => $this->lang->line("invoices"), "DBN" => $this->lang->line("debit_notes")];
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsDateTime"] = $this->get_filter_operators("dateTime");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $data["paidStatus"] = $this->invoice_header->get("paidStatusValues");
            array_unshift($data["paidStatus"], "", "overdue");
            unset($data["paidStatus"][0]);
            $data["paidStatus"] = array_combine($data["paidStatus"], [$this->lang->line("overdue"), $this->lang->line("draft"), $this->lang->line("open"), $this->lang->line("partially_paid"), $this->lang->line("paid"), $this->lang->line("cancelled")]);
            if (0 < $clientId) {
                $this->load->model("client");
                $clientData = $this->client->fetch_client($clientId);
                $data["clientNameFilter"] = $clientData["clientName"];
            } else {
                $data["clientNameFilter"] = "";
            }
            $data["client_account"] = $this->fetch_clinet_account($data, "accountID");
            $data["invoice_id"] = $invoice_id ? $invoice_id : "";
            $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
            $data["gridSavedFiltersData"] = false;
            $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
            if ($data["gridDefaultFilter"]) {
                $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
                $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            }
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $this->load->helper("encrypt_decrypt_helper");
            $data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/invoices", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("invoices/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function invoice_details($voucher_id = 0, $viewType = "external")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoice_details") . " | " . $this->lang->line("money"));
        if (0 < $voucher_id) {
            $data = [];
            $data["voucherId"] = $voucher_id;
            $this->load->model("voucher_header", "voucher_headerfactory");
            $this->voucher_header = $this->voucher_headerfactory->get_instance();
            $this->load->model("invoice_detail", "invoice_detailfactory");
            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->load->model("item_commission");
            $this->load->model("invoice_detail_cover_page_template", "invoice_detail_cover_page_template_factory");
            $this->covertemplate = $this->invoice_detail_cover_page_template_factory->get_instance();
            $data["templates"] = $this->covertemplate->load_cover_template_by_organizationID();
            $this->load->model("invoice_detail_look_feel_section", "invoice_detail_look_feel_section_factory");
            $this->Section = $this->invoice_detail_look_feel_section_factory->get_instance();
            $sections = $this->Section->load_all();
            $data["sections"] = $sections;
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->invoice_header->fetch(["voucher_header_id" => $voucher_id]);
            $data["activateTax"] = $this->invoice_header->get_field("displayTax");
            $data["activateDiscount"] = $this->invoice_header->get_field("displayDiscount");
            $data["discount_percentage"] = $this->invoice_header->get_field("discount_percentage");
            $this->load->model("user_preference");
            $moneyLanguage = $this->user_preference->get_value("money_language");
            $data["moneyLanguage"] = $moneyLanguage;
            $data["invoice"] = $this->voucher_header->load_invoice_voucher($voucher_id);
            $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_id);
            $data["items"] = [];
            $data["expenses"] = [];
            $data["timeLogs"] = [];
            $data["partners"] = [];
            $normalPartners = [];
            $thirdParties = [];
            $data["partnersByTypeId"] = [];
            $data["partnersByTypeId"]["items"] = [];
            $data["partnersByTypeId"]["expenses"] = [];
            $data["partnersByTypeId"]["timeLogs"] = [];
            if (!empty($data["invoice"])) {
                $moneyLanguage = $this->user_preference->get_value("money_language");
                $data["invoice_details"] = $this->invoice_detail->load_invoice_details($data["invoice"]["id"], $moneyLanguage);
                $itemCommissions = $this->item_commission->fetch_commissions($data["invoice"]["id"]);
                if (!empty($itemCommissions)) {
                    foreach ($itemCommissions as $curItemCommission) {
                        if (!strcmp($curItemCommission["isThirdParty"], "yes")) {
                            $thirdParties[$curItemCommission["partnerName"]][$curItemCommission["invoice_details_id"]] = $curItemCommission;
                        } else {
                            $normalPartners[$curItemCommission["partnerName"]][$curItemCommission["invoice_details_id"]] = $curItemCommission;
                        }
                        if (strcmp($curItemCommission["item_id"], "")) {
                            $data["partnersByTypeId"]["items"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                        } else {
                            if (strcmp($curItemCommission["expense_id"], "")) {
                                $data["partnersByTypeId"]["expenses"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                            } else {
                                if (strcmp($curItemCommission["time_logs_id"], "")) {
                                    $data["partnersByTypeId"]["timeLogs"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                                }
                            }
                        }
                    }
                    $data["partners"] = array_merge_recursive($normalPartners, $thirdParties);
                }
                foreach ($data["invoice_details"] as $val) {
                    if (!empty($val["time_logs_id"])) {
                        $data["timeLogs"][$val["worker"]][] = $val;
                    } else {
                        if (!empty($val["expense_id"])) {
                            array_push($data["expenses"], $val);
                        } else {
                            if (!empty($val["item_id"])) {
                                array_push($data["items"], $val);
                            }
                        }
                    }
                }
            }
            $this->invoice_header->fetch(["voucher_header_id" => $voucher_id]);
            $data["time_logs_grouped"] = $this->invoice_header->get_field("groupTimeLogsByUserInExport");
            $this->load->view("partial/header");
            if ($viewType == "internal") {
                $this->load->view("invoices/details_report_with_commissions", $data);
            } else {
                $this->load->view("invoices/details_report_without_commissions", $data);
            }
            $this->load->view("partial/footer");
        } else {
            redirect("vouchers/invoices_list");
        }
    }
    public function invoice_payment_add($voucher_id = 0)
    {
        if (0 < !$voucher_id || !$this->validate_voucher($voucher_id)) {
            $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
            redirect("vouchers/invoices_list/");
        }
        $this->invoice_payment_save($voucher_id);
    }
    public function invoice_payment_edit($voucher_id = 0, $payment_id = 0)
    {
        if (0 < !$voucher_id || !$this->validate_voucher($voucher_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->invoice_payment_save($voucher_id, $payment_id);
    }
    private function invoice_payment_save($voucher_id = 0, $payment_id = 0)
    {
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("kendoui/js/kendo.web.min", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoice_payment") . " | " . $this->lang->line("money"));
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model(["invoice_payment", "invoice_payment_invoice"]);
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $data = ["payment_data" => false, "other_payment_data" => false, "common_payment_data" => false];
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (0 < $payment_id && !$this->invoice_payment->fetch($payment_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("invalid_record")));
            redirect("vouchers/invoice_payments_made/" . $voucher_id);
        }
        $data["invoice_data"] = $this->voucher_header->load_invoice_voucher($voucher_id);
        if (empty($data["invoice_data"])) {
            $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
            redirect("vouchers/invoice_payments_made/" . $voucher_id);
        }
        $data["is_debit_note"] = $data["invoice_data"]["voucherType"] == "DBN";
        $payment_voucher_type = $data["invoice_data"]["voucherType"] == "DBN" ? "DBN-PY" : "INV-PY";
        $data["rates"] = $exchange_rates;
        if (!isset($data["rates"]) || !isset($data["rates"][$data["invoice_data"]["currency_id"]])) {
            redirect("setup/rate_between_money_currencies");
        }
        if (in_array($data["invoice_data"]["paidStatus"], ["draft", "cancelled"])) {
            $this->set_flashmessage("warning", $this->lang->line("you_can_not_record_any_payments_for_this_invoice"));
            redirect("vouchers/invoices_list");
        }
        $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_data"]["id"]]]);
        $this->invoice_header->fetch($data["invoice_data"]["id"]);
        $data["invoice_data"]["clientCurrencyId"] = $data["invoice_data"]["currency_id"];
        $data["invoice_data"]["credits_available"] = 0;
        $data["invoice_data"]["balance_due"] = $data["invoice_data"]["total"];
        foreach ($invoice_payments as $payment) {
            $data["invoice_data"]["credits_available"] += $payment["amount"] * 1;
        }
        $data["invoice_data"]["balance_due"] -= $data["invoice_data"]["credits_available"];
        $credit_notes_total = $this->invoice_header->invoice_credit_notes_total($data["invoice_data"]["id"]);
        $data["invoice_data"]["credit_notes_total"] = round($credit_notes_total, 2);
        $data["invoice_data"]["balance_due"] -= $credit_notes_total;
        $data["invoice_data"]["balance_due"] = number_format($data["invoice_data"]["balance_due"], 2, NULL, "");
        if ($data["invoice_data"]["balance_due"] == 0 && $this->invoice_header->get_field("paidStatus") == "paid" && $payment_id == 0) {
            $this->set_flashmessage("warning", $this->lang->line("you_can_not_record_any_payments_for_this_invoice"));
            redirect("vouchers/invoices_list");
        }
        $this->load->model("deposit", "depositfactory");
        $this->deposit = $this->depositfactory->get_instance();
        $data["trust_account_data"] = $this->deposit->load_client_trust_accounts($data["invoice_data"]["client_id"]);
        $this->config->load("accounts_map", true);
        $accounts_map = $this->config->item("accounts_map");
        if ($this->input->post(NULL)) {
            $_POST["paidOn"] = date("Y-m-d H:i", strtotime($this->input->post("paidOn")));
            $result = true;
            $this->account->fetch($this->input->post("account_id"));
            $deposit_to_currency = $this->account->get_field("currency_id");
            $other_deposit_to_currency = 0;
            if ($this->input->post("other_account_id")) {
                $this->account->fetch($this->input->post("other_account_id"));
                $other_deposit_to_currency = $this->account->get_field("currency_id");
            }
            $amount = $this->input->post("amount") * $data["rates"][$deposit_to_currency] / $data["rates"][$this->input->post("clientCurrencyId")];
            $other_amount = !$this->input->post("other_amount") ? 0 : $this->input->post("other_amount") * $data["rates"][$other_deposit_to_currency] / $data["rates"][$this->input->post("clientCurrencyId")];
            $total_amount = !$this->input->post("other_amount") ? round($amount, 2) : round($amount + $other_amount, 2);
            if (0 < $payment_id) {
                $voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
                $other_payment_data = $this->invoice_payment->load_other_payment_data($payment_id, $voucher_header_id);
                $old_amount = $this->delete_invoice_payment_invoice($payment_id);
                $old_other_amount = 0;
                if (isset($other_payment_data["id"])) {
                    $old_other_amount = $this->delete_invoice_payment_invoice($other_payment_data["id"]);
                    $this->invoice_payment->delete($other_payment_data["id"]);
                }
                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                $this->voucher_header->fetch($voucher_header_id);
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                $this->voucher_header->set_field("voucherType", $payment_voucher_type);
                $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                $this->voucher_header->set_field("description", $this->input->post("comments"));
                if ($this->voucher_header->update()) {
                    $cr_amount = NULL;
                    $dbt_amount = NULL;
                    $gain_loss_added = false;
                    $main_voucher_details = !$this->voucher_detail->load_main_details_with_accounts($voucher_id) ? NULL : array_values($this->voucher_detail->load_main_details_with_accounts($voucher_id))[0];
                    $local_currency_amount = $this->input->post("amount") * $data["rates"][$deposit_to_currency];
                    $local_currency_other_amount = !$this->input->post("other_amount") ? 0 : $this->input->post("other_amount") * $data["rates"][$other_deposit_to_currency];
                    $local_currency_total_amount = $local_currency_amount + $local_currency_other_amount;
                    $invoice_balance = $this->calculate_invoice_balance($accounts_map, $data, $main_voucher_details, $local_currency_total_amount);
                    $gain_loss_added = false;
                    if ($invoice_balance < 0) {
                        $cr_amount = $this->add_gain_loss_record($data, $invoice_balance, false, $local_currency_total_amount);
                        $gain_loss_added = true;
                    }
                    if (abs($data["invoice_data"]["balance_due"] + $old_amount + $old_other_amount - round($total_amount, 2)) < 0) {
                        $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_data"]["id"]]]);
                        $voucher_details = [];
                        $previous_voucher_header_id = NULL;
                        foreach ($invoice_payments as $payment) {
                            $in = $this->invoice_payment->load(["where" => [["id", $payment["invoice_payment_id"]]]]);
                            $current_payment_voucher_header_id = $in["voucher_header_id"];
                            if ($current_payment_voucher_header_id != $previous_voucher_header_id) {
                                $voucher_details = array_merge($voucher_details, $this->voucher_detail->load_details_with_accounts($in["voucher_header_id"]));
                            }
                            $previous_voucher_header_id = $in["voucher_header_id"];
                        }
                        $m_debit = 0;
                        $crdt = $local_currency_total_amount;
                        foreach ($voucher_details as $voucher_d) {
                            if ($voucher_d["drCr"] === "C" && $voucher_d["account_type_id"] != $accounts_map["TrustAsset"]["type_id"] && substr($voucher_d["description"], 0, 6) != "INV-GL" && $voucher_d["model_type"] != "partner") {
                                $crdt += $voucher_d["local_amount"];
                            }
                        }
                        if (!is_null($main_voucher_details)) {
                            $m_debit += $main_voucher_details["local_amount"];
                        }
                        $diff = $m_debit - $crdt;
                        if ($diff != 0 && !$gain_loss_added) {
                            $cr_amount = $this->add_gain_loss_record($data, $diff, true, $local_currency_total_amount);
                        }
                    }
                    if ($this->insert_inv_pay_voucher_details($data, ["deposit_to" => $deposit_to_currency, "other_deposit_to" => $other_deposit_to_currency], $cr_amount, $dbt_amount)) {
                        if ($this->is_settlements_per_invoice_enabled() && !$this->is_partners_share_added($voucher_id)) {
                            $this->load->model("item_commission");
                            $item_commissions_data_array = $this->item_commission->fetch_commissions($data["invoice_data"]["id"]);
                            $client_account = $this->account->fetch_account($data["invoice_data"]["account_id"]);
                            $commission_accounts = $this->prepare_commissions_data($data, $item_commissions_data_array, $voucher_header_id, $data["invoice_data"], $client_account, $total_amount);
                            if (!empty($commission_accounts)) {
                                $this->voucher_detail->insert_batch($commission_accounts);
                            }
                        }
                        if ($this->insert_inv_pay_data($data, ["deposit_to" => $deposit_to_currency, "other_deposit_to" => $other_deposit_to_currency], "update")) {
                            $this->invoice_header->fetch($this->input->post("invoice_id"));
                            $this->invoice_header->set_field("paidStatus", "open");
                            if (abs($data["invoice_data"]["balance_due"] + $old_amount + $old_other_amount - round($total_amount, 2)) < 0) {
                                $this->invoice_header->set_field("paidStatus", "paid");
                                $this->load->model("invoice_detail", "invoice_detailfactory");
                                $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                                $invoice_details = $this->invoice_detail->load_all_invoice_details($this->invoice_header->get_field("id"), "");
                                $this->set_expense_status($invoice_details, "reimbursed");
                                $this->set_time_logs_status($invoice_details, "reimbursed");
                            } else {
                                if (round($total_amount, 2) < $data["invoice_data"]["balance_due"] + $old_amount + $old_other_amount) {
                                    $this->invoice_header->set_field("paidStatus", "partially paid");
                                    $this->load->model("invoice_detail", "invoice_detailfactory");
                                    $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                                    $invoice_details = $this->invoice_detail->load_all_invoice_details($this->invoice_header->get_field("id"), "");
                                    $this->set_expense_status($invoice_details, "invoiced");
                                    $this->set_time_logs_status($invoice_details, "invoiced");
                                }
                            }
                            $this->invoice_header->update();
                            $result = true;
                        }
                    } else {
                        $this->voucher_header->delete($voucher_header_id);
                        $result = false;
                    }
                    if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                        $existant_file = $this->dms->model->get_document_details(["module" => $payment_voucher_type, "module_record_id" => $voucher_header_id, "system_document" => 0]);
                        if (!empty($existant_file)) {
                            $this->dms->delete_document($payment_voucher_type, $existant_file["id"]);
                        }
                        $this->dms->upload_file(["module" => $payment_voucher_type, "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                    }
                }
            } else {
                $trust_account_balance_validation = $this->input->post("paymentMethod") === "Trust Account" ? $this->input->post("amount") * $data["rates"][$deposit_to_currency] / $data["rates"][$this->session->userdata("organizationCurrencyID")] <= $data["trust_account_data"]["balance_amount"] * 1 : true;
                $other_trust_account_balance_validation = $this->input->post("other_payment_method") === "Trust Account" ? $this->input->post("other_amount") * $data["rates"][$other_deposit_to_currency] / $data["rates"][$this->session->userdata("organizationCurrencyID")] <= $data["trust_account_data"]["balance_amount"] * 1 : true;
                if ($data["invoice_data"]["balance_due"] * 1 < round($total_amount, 2) || !$trust_account_balance_validation || !$other_trust_account_balance_validation) {
                    $this->set_flashmessage("warning", !$trust_account_balance_validation || !$other_trust_account_balance_validation ? $this->lang->line("allowed_amount_trust_account") : $this->lang->line("allowed_amount"));
                    $data = ["payment_data" => ["total" => $data["invoice_data"]["balance_due"], "paymentMethod" => $this->input->post("paymentMethod"), "referenceNum" => $this->input->post("referenceNum"), "description" => $this->input->post("comments"), "account_id" => $this->input->post("account_id")], "other_payment_data" => ["total" => $data["invoice_data"]["balance_due"], "paymentMethod" => $this->input->post("other_payment_method"), "referenceNum" => $this->input->post("referenceNum"), "description" => $this->input->post("comments"), "account_id" => $this->input->post("other_account_id")]];
                    redirect("vouchers/invoice_payment_add/" . $voucher_id);
                }
                $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                $this->voucher_header->set_field("refNum", $this->auto_generate_rf($payment_voucher_type));
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                $this->voucher_header->set_field("voucherType", $payment_voucher_type);
                $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                $this->voucher_header->set_field("description", $this->input->post("comments"));
                if ($this->voucher_header->insert()) {
                    $voucher_header_id = $this->voucher_header->get_field("id");
                    $cr_amount = NULL;
                    $dbt_amount = NULL;
                    $gain_loss_added = false;
                    $main_voucher_details = !$this->voucher_detail->load_main_details_with_accounts($voucher_id) ? NULL : array_values($this->voucher_detail->load_main_details_with_accounts($voucher_id))[0];
                    $local_currency_amount = $this->input->post("amount") * $data["rates"][$deposit_to_currency];
                    $local_currency_other_amount = !$this->input->post("other_amount") ? 0 : $this->input->post("other_amount") * $data["rates"][$other_deposit_to_currency];
                    $local_currency_total_amount = $local_currency_amount + $local_currency_other_amount;
                    $invoice_balance = $this->calculate_invoice_balance($accounts_map, $data, $main_voucher_details, $local_currency_total_amount);
                    $gain_loss_added = false;
                    if ($invoice_balance < 0) {
                        $cr_amount = $this->add_gain_loss_record($data, $invoice_balance, false, $local_currency_total_amount);
                        $gain_loss_added = true;
                    }
                    $this->invoice_payment->reset_fields();
                    if (abs($data["invoice_data"]["balance_due"] - round($total_amount, 2)) < 0) {
                        $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_data"]["id"]]]);
                        $voucher_details = [];
                        $previous_voucher_header_id = NULL;
                        foreach ($invoice_payments as $payment) {
                            $this->invoice_payment->fetch($payment["invoice_payment_id"]);
                            $current_payment_voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
                            if ($current_payment_voucher_header_id != $previous_voucher_header_id) {
                                $voucher_details = array_merge($voucher_details, $this->voucher_detail->load_details_with_accounts($this->invoice_payment->get_field("voucher_header_id")));
                            }
                            $previous_voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
                        }
                        $m_debit = 0;
                        $crdt = $local_currency_total_amount;
                        foreach ($voucher_details as $voucher_d) {
                            if ($voucher_d["drCr"] === "C" && $voucher_d["account_type_id"] != $accounts_map["TrustAsset"]["type_id"] && substr($voucher_d["description"], 0, 6) != "INV-GL" && $voucher_d["model_type"] != "partner") {
                                $crdt += $voucher_d["local_amount"];
                            }
                        }
                        if (!is_null($main_voucher_details)) {
                            $m_debit += $main_voucher_details["local_amount"];
                        }
                        $diff = $m_debit - $crdt;
                        if ($diff != 0 && !$gain_loss_added) {
                            $cr_amount = $this->add_gain_loss_record($data, $diff, true, $local_currency_total_amount);
                        }
                        $this->invoice_payment->reset_fields();
                    }
                    if ($this->insert_inv_pay_voucher_details($data, ["deposit_to" => $deposit_to_currency, "other_deposit_to" => $other_deposit_to_currency], $cr_amount, $dbt_amount)) {
                        if ($this->is_settlements_per_invoice_enabled() && !$this->is_partners_share_added($voucher_id)) {
                            $this->load->model("item_commission");
                            $item_commissions_data_array = $this->item_commission->fetch_commissions($data["invoice_data"]["id"]);
                            $client_account = $this->account->fetch_account($data["invoice_data"]["account_id"]);
                            $commission_accounts = $this->prepare_commissions_data($data, $item_commissions_data_array, $voucher_header_id, $data["invoice_data"], $client_account, $total_amount);
                            if (!empty($commission_accounts)) {
                                $this->voucher_detail->insert_batch($commission_accounts);
                            }
                        }
                        if ($this->insert_inv_pay_data($data, ["deposit_to" => $deposit_to_currency, "other_deposit_to" => $other_deposit_to_currency])) {
                            $this->invoice_header->fetch($this->input->post("invoice_id"));
                            $this->invoice_header->set_field("paidStatus", "open");
                            if (abs($data["invoice_data"]["balance_due"] - round($total_amount, 2)) < 0) {
                                $this->invoice_header->set_field("paidStatus", "paid");
                                $this->load->model("invoice_detail", "invoice_detailfactory");
                                $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                                $invoice_details = $this->invoice_detail->load_all_invoice_details($this->invoice_header->get_field("id"), "");
                                $this->set_expense_status($invoice_details, "reimbursed");
                                $this->set_time_logs_status($invoice_details, "reimbursed");
                            } else {
                                if (round($total_amount, 2) < $data["invoice_data"]["balance_due"]) {
                                    $this->invoice_header->set_field("paidStatus", "partially paid");
                                }
                            }
                            $this->invoice_header->update();
                            $result = true;
                        }
                    } else {
                        $this->voucher_header->delete($voucher_header_id);
                        $result = false;
                    }
                    if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                        $this->dms->upload_file(["module" => $payment_voucher_type, "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                    }
                }
            }
            if ($result) {
                $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("payment")));
                redirect("vouchers/invoice_payments_made/" . $voucher_id);
            }
        }
        $data["voucher_id"] = $voucher_id;
        if (0 < $payment_id) {
            $data["payment_data"] = $this->invoice_payment->load_payment_data($payment_id);
            $data["other_payment_data"] = $this->invoice_payment->load_other_payment_data($payment_id, $data["payment_data"]["voucher_header_id"]);
            $data["tabsNLogs"]["subNavItems"][site_url("vouchers/invoice_payment_edit/" . $voucher_id . "/" . $payment_id)] = $this->lang->line("record_invoice_payment");
            $data["tabsNLogs"]["activeSubNavItem"] = site_url("vouchers/invoice_payment_edit/" . $voucher_id . "/" . $payment_id);
            $file = $this->dms->model->get_document_details(["module" => $payment_voucher_type, "module_record_id" => $data["payment_data"]["voucher_header_id"], "system_document" => 0]);
            $data["common_payment_data"]["attachment_id"] = !empty($file) ? $file["id"] : "";
            $data["common_payment_data"]["attachment"] = !empty($file) ? $file["full_name"] : "";
        } else {
            $data["tabsNLogs"]["subNavItems"][site_url("vouchers/invoice_payment_add/" . $voucher_id)] = $this->lang->line("record_invoice_payment");
            $data["tabsNLogs"]["activeSubNavItem"] = site_url("vouchers/invoice_payment_add/" . $voucher_id);
        }
        $data["paymentMethod"] = $this->invoice_payment->get("paymentMethodValues");
        array_unshift($data["paymentMethod"], "");
        $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("bank_transfer"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque"), $this->lang->line("online_payment"), $this->lang->line("trust_account"), $this->lang->line("other")]);
        $data["accounts"] = $this->account->load_accounts_per_organization("AssetCashBank");
        $data["rates"] = json_encode($data["rates"]);
        $this->includes("money/js/invoice_payments", "js");
        $this->load->view("partial/header");
        $this->load->view("invoice_payments/record_payment", $data);
        $this->load->view("partial/footer");
    }
    private function calculate_invoice_balance($accounts_map, $data, $main_voucher_details, $local_currency_total_amount)
    {
        $initial_invoice_payment = clone $this->invoice_payment;
        $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_data"]["id"]]]);
        $voucher_details = [];
        $previous_voucher_header_id = NULL;
        foreach ($invoice_payments as $payment) {
            $this->invoice_payment->fetch($payment["invoice_payment_id"]);
            $current_payment_voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
            if ($current_payment_voucher_header_id != $previous_voucher_header_id) {
                $voucher_details = array_merge($voucher_details, $this->voucher_detail->load_details_with_accounts($this->invoice_payment->get_field("voucher_header_id")));
            }
            $previous_voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
        }
        $total_payed_amount = 0;
        foreach ($voucher_details as $voucher_d) {
            if ($voucher_d["drCr"] === "C" && $voucher_d["account_type_id"] != $accounts_map["TrustAsset"]["type_id"] && substr($voucher_d["description"], 0, 6) != "INV-GL" && $voucher_d["model_type"] != "partner") {
                $total_payed_amount += $voucher_d["local_amount"];
            }
        }
        $this->invoice_payment = $initial_invoice_payment;
        $main_voucher_local_amount = is_null($main_voucher_details) ? 0 : $main_voucher_details["local_amount"];
        $local_currency_balance = $main_voucher_local_amount - $total_payed_amount;
        return $local_currency_balance - $local_currency_total_amount;
    }
    private function add_gain_loss_record($data, $amount, $last_payment, $local_currency_total_amount)
    {
        $account_gainorloss = $this->account->load(["where" => [["organization_id", $this->session->userdata("organizationID")], ["name", "Exchange gain or loss"]]]);
        if (empty($account_gainorloss) || !$account_gainorloss) {
            $new_account = $this->accountfactory->get_instance();
            $new_account->set_field("organization_id", $this->session->userdata("organizationID"));
            $new_account->set_field("currency_id", $this->session->userdata("organizationCurrencyID"));
            $new_account->set_field("account_type_id", "11");
            $new_account->set_field("name", "Exchange gain or loss");
            $new_account->set_field("model_name", "internal");
            $new_account->set_field("model_type", "internal");
            $new_account->set_field("systemAccount", "yes");
            $new_account->set_field("number", "1");
            $new_account->set_field("show_in_dashboard", "1");
            $new_account->insert(true);
            $gl_account_id = $new_account->get_field("id");
        } else {
            $gl_account_id = $account_gainorloss["id"];
        }
        $v_amount = abs($amount);
        $type = $last_payment && 0 < $amount ? "D" : "C";
        $inv_description = "INV-GL " . $data["invoice_data"]["prefix"] . $data["invoice_data"]["refNum"] . $data["invoice_data"]["suffix"];
        $result01 = $this->insert_voucher_details(["description" => $inv_description, "account_id" => $gl_account_id, "drCr" => $type, "local_amount" => $v_amount, "foreign_amount" => $v_amount]);
        if ($type == "C") {
            $cr_amount = abs($local_currency_total_amount - round($v_amount, 2));
        } else {
            $cr_amount = abs($local_currency_total_amount + round($v_amount, 2));
        }
        return $cr_amount;
    }
    public function settlement_of_partner_account()
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("warning", $this->lang->line("invalid_request"));
            redirect("accounts/partners");
        } else {
            $response = [];
            $account_id = $this->input->post("account_id");
            $amount = $this->input->post("amount");
            $paid_through = $this->input->post("paid_through");
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            if (!empty($account_id)) {
                $data = [];
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                if (empty($amount) && empty($paid_through)) {
                    if ($this->is_commissions_enabled() && $this->is_settlements_per_invoice_enabled()) {
                        $data["invoices"] = $this->invoice_header->get_partner_paid_invoices($account_id);
                    }
                    $data["partner_account"] = $this->account->fetch_account($account_id);
                    $data["accounts"] = $this->account->load_accounts_per_organization("AssetCashBank");
                    $data["balance"] = $this->input->post("balance");
                    if ($this->input->post("invoice_id")) {
                        $data["invoice_id"] = $this->input->post("invoice_id");
                    }
                    $response["html"] = $this->load->view("accounts/settlement_of_partner_account", $data, true);
                } else {
                    $description = "PY - " . $this->input->post("comments");
                    if ($this->input->post("invoice_id")) {
                        $this->invoice_header->fetch($this->input->post("invoice_id"));
                        $this->voucher_header->fetch($this->invoice_header->get_field("voucher_header_id"));
                        $description .= " - " . $this->invoice_header->get_field("prefix") . $this->voucher_header->get_field("refNum") . $this->invoice_header->get_field("suffix");
                        $this->voucher_header->reset_fields();
                    }
                    $this->load->model("exchange_rate");
                    $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
                    $data["rates"] = $exchange_rates;
                    $data["partner_account"] = $this->account->fetch_account($this->input->post("account_id"));
                    $data["paid_through"] = $this->account->fetch_account($this->input->post("paid_through"));
                    $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->voucher_header->set_field("refNum", $this->auto_generate_rf("PY"));
                    $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                    $this->voucher_header->set_field("voucherType", "PY");
                    $this->voucher_header->set_field("description", $this->input->post("comments"));
                    if ($this->voucher_header->insert()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                        $this->voucher_detail->set_field("drCr", "C");
                        $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$data["paid_through"]["currency_id"]]);
                        $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount"));
                        $this->voucher_detail->set_field("description", $description);
                        if ($this->input->post("invoice_id")) {
                            $this->load->model("settlement_invoice");
                            $this->settlement_invoice->set_field("voucher_header_id", $voucher_header_id);
                            $this->settlement_invoice->set_field("invoice_header_id", $this->input->post("invoice_id"));
                            $this->settlement_invoice->insert();
                        }
                        if ($result = $this->voucher_detail->insert()) {
                            $this->voucher_detail->reset_fields();
                            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                            $this->voucher_detail->set_field("account_id", $this->input->post("account_id"));
                            $this->voucher_detail->set_field("drCr", "D");
                            $this->voucher_detail->set_field("local_amount", $this->input->post("amount") * $data["rates"][$data["paid_through"]["currency_id"]]);
                            $this->voucher_detail->set_field("foreign_amount", $this->input->post("amount") * $data["rates"][$data["paid_through"]["currency_id"]] / $data["rates"][$data["partner_account"]["currency_id"]]);
                            $this->voucher_detail->set_field("description", $description);
                            $result = $this->voucher_detail->insert();
                            if ($result) {
                                $response["status"] = 500;
                            } else {
                                $response["status"] = 101;
                                $response["validationErrors"] = $this->voucher_detail->get("validationErrors");
                                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_header->delete($voucher_header_id);
                            }
                        } else {
                            $response["status"] = 101;
                            $response["validationErrors"] = $this->voucher_detail->get("validationErrors");
                            $this->voucher_header->delete($voucher_header_id);
                        }
                    } else {
                        $response["status"] = 101;
                        $response["validationErrors"] = $this->voucher_header->get("validationErrors");
                    }
                }
            } else {
                $response["status"] = 102;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function invoice_payment_print($payment_voucher_id, $payment_id, $voucher_id)
    {
        $data = [];
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("print") . " | " . $this->lang->line("invoice_payment") . " | " . $this->lang->line("money"));
        $data = $this->get_payment_print_components($payment_voucher_id, $payment_id, $voucher_id);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $this->load->view("partial/header");
        $money_language = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        $system_language = $this->session->userdata("AUTH_language");
        $this->switch_language($system_language, $money_language, false);
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $data["is_print"] = true;
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $this->load->view("invoice_payments/invoice_payment_export_view", $data);
        $this->switch_language($money_language, $system_language, true);
        $this->load->view("partial/footer");
    }
    public function invoice_payments_made($voucher_id)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoices") . " | " . $this->lang->line("payments_made") . " | " . $this->lang->line("money"));
        if (0 < $voucher_id && !$this->validate_voucher($voucher_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->voucher_header->k_load_all_invoices_payments_made($filter, $sortable);
                for ($p = 0; $p < count($response["data"]); $p++) {
                    $file = $this->dms->model->get_document_details(["module" => $response["data"][$p]["voucherType"], "module_record_id" => $response["data"][$p]["id"], "system_document" => 0]);
                    $response["data"][$p]["attachment_id"] = isset($file["id"]) ? $file["id"] : "";
                    $response["data"][$p]["attachment_type"] = isset($file["id"]) ? $file["type"] : "";
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data = [];
            $this->load->model("invoice_payment_invoice");
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $data["invoice_data"] = $this->voucher_header->load_invoice_voucher($voucher_id);
            if (empty($data["invoice_data"])) {
                $this->set_flashmessage("warning", $this->lang->line("invalid_record"));
                redirect("vouchers/invoices_list");
            }
            $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_data"]["id"]]]);
            $this->invoice_header->fetch($data["invoice_data"]["id"]);
            $data["invoice_data"]["credits_available"] = 0;
            $data["invoice_data"]["balance_due"] = $data["invoice_data"]["total"];
            foreach ($invoice_payments as $payment) {
                $data["invoice_data"]["credits_available"] += $payment["amount"] * 1;
                $data["invoice_data"]["balance_due"] -= $payment["amount"] * 1;
            }
            $credit_notes_total = $this->invoice_header->invoice_credit_notes_total($data["invoice_data"]["id"]);
            $data["invoice_data"]["credit_notes_total"] = round($credit_notes_total, 2);
            $data["invoice_data"]["balance_due"] -= $credit_notes_total;
            $data["invoice_data"]["balance_due"] = round($data["invoice_data"]["balance_due"], 2);
            $data["is_debit_note"] = $data["invoice_data"]["voucherType"] == "DBN";
            $data["invoice_id"] = $data["invoice_data"]["id"];
            $data["voucher_header_id"] = $data["invoice_data"]["voucher_header_id"];
            $data["tabsNLogs"] = $this->_get_invoice_tabs_view_vars($voucher_id, site_url("vouchers/invoice_payments_made/"));
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["is_settlements_per_invoice_enabled"] = $this->is_settlements_per_invoice_enabled();
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/invoice_payments_made", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("invoice_payments/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function invoice_payment_export_to_word($payment_voucher_id, $payment_id, $voucher_id, $template_id = "")
    {
        $data = $this->get_payment_print_components($payment_voucher_id, $payment_id, $voucher_id);
        if (!empty($template_id)) {
            $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
            $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
            $this->organization_invoice_template->fetch($template_id);
            $data["template"] = $this->organization_invoice_template->get_fields();
            $template_settings = $data["template"]["settings"] ? $data["template"]["settings"] : $this->organization_invoice_template->get("default_template_settings");
            $data["settings"] = unserialize($template_settings);
        }
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $money_language = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        $system_language = $this->session->userdata("AUTH_language");
        $this->switch_language($system_language, $money_language, false);
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $data["is_print"] = false;
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $html = $this->load->view("invoice_payments/invoice_payment_export_view", $data, true);
        $this->switch_language($money_language, $system_language, true);
        $docx->addHeader(["default" => $this->add_export_header($data, $docx)]);
        $docx->embedHTML($html);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "invoice_payments";
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $fileName = $this->lang->line("receipt_voucher") . "_" . date("YmdHi");
        $docx->createDocx($temp_directory . DIRECTORY_SEPARATOR . $fileName);
        $this->load->helper("download");
        $content = file_get_contents($temp_directory . DIRECTORY_SEPARATOR . $fileName . ".docx");
        unlink($temp_directory . DIRECTORY_SEPARATOR . $fileName . ".docx");
        $filename_encoded = $this->downloaded_file_name_by_browser($fileName . ".docx");
        force_download($filename_encoded, $content);
        exit;
    }
    public function invoice_payment_delete()
    {
        if ($this->input->is_ajax_request()) {
            $voucher_id = $this->input->post("InvoiceVoucherID");
            $payment_id = $this->input->post("paymentID");
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model(["invoice_payment", "invoice_payment_invoice"]);
            $result = false;
            if ($this->validate_voucher_and_invoice_payment($voucher_id, $payment_id)) {
                $this->invoice_payment->fetch($payment_id);
                $voucher_header_id = $this->invoice_payment->get_field("voucher_header_id");
                $this->invoice_payment_invoice->fetch(["invoice_payment_id" => $payment_id]);
                $invoice_header_id = $this->invoice_payment_invoice->get_field("invoice_header_id");
                if ($this->invoice_payment_invoice->delete(["where" => ["invoice_payment_id", $payment_id]])) {
                    $other_payment_data = $this->invoice_payment->load_other_payment_data($payment_id, $voucher_header_id);
                    $payment_data = $this->invoice_payment->load_payment_data($payment_id);
                    if ($result = $this->invoice_payment->delete($payment_id)) {
                        if (isset($other_payment_data["id"]) && !empty($other_payment_data["id"]) && $this->invoice_payment_invoice->delete(["where" => ["invoice_payment_id", $other_payment_data["id"]]])) {
                            $result = $this->invoice_payment->delete($other_payment_data["id"]);
                        }
                        if ($this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                            $this->dms->delete_module_record_container($payment_data["voucherType"], $voucher_header_id);
                        } else {
                            $result = false;
                        }
                    }
                }
            }
            if ($result) {
                if ($this->set_invoice_status($invoice_header_id)) {
                    $this->load->model("invoice_detail", "invoice_detailfactory");
                    $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                    $invoice_details = $this->invoice_detail->load_all_invoice_details($invoice_header_id, "");
                    $this->set_expense_status($invoice_details, "invoiced");
                    $this->set_time_logs_status($invoice_details, "invoiced");
                    $response["status"] = 101;
                } else {
                    $response["status"] = 202;
                }
            } else {
                $response["status"] = 202;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $this->set_flashmessage("warning", $this->lang->line("invalid_request"));
            redirect("vouchers/invoice_payments_made");
        }
    }
    private function set_invoice_status($invoice_header_id)
    {
        $this->load->model("invoice_payment_invoice");
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->invoice_header->fetch($invoice_header_id);
        $status = $this->invoice_header->get_field("paidStatus");
        if (!in_array($status, ["draft", "cancelled"])) {
            $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $invoice_header_id]]);
            $total = $this->invoice_header->get_field("total");
            $credits_available = 0;
            $balance_due = $total;
            foreach ($invoice_payments as $payment) {
                $credits_available += $payment["amount"] * 1;
                $balance_due -= $payment["amount"] * 1;
            }
            $credit_notes_total = $this->invoice_header->invoice_credit_notes_total($invoice_header_id);
            $balance_due -= $credit_notes_total;
            if ($balance_due == 0) {
                $status = "paid";
            } else {
                if ($credits_available == 0) {
                    $status = "open";
                    $invoice_data["status"] = $this->input->post("status");
                    if (!empty($invoice_data["status"])) {
                        $status = "draft";
                    }
                } else {
                    if (0 < $credits_available) {
                        $status = "partially paid";
                    }
                }
            }
        }
        $this->invoice_header->set_field("paidStatus", $status);
        if ($this->invoice_header->update()) {
            return true;
        }
        return false;
    }
    public function invoice_related_documents($id = "")
    {
        if (0 < $id) {
            $voucher_row = $this->validate_voucher($id, true);
        }
        if (!$voucher_row) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->related_documents($id, $voucher_row["voucherType"], "invoice");
    }
    public function invoice_load_documents()
    {
        $this->load_documents();
    }
    public function invoice_upload_file()
    {
        $this->upload_file();
    }
    public function invoice_rename_file()
    {
        $doc_module = $this->input->post("module");
        $doc_module = empty($doc_module) ? "INV" : $doc_module;
        $this->rename_file($doc_module);
    }
    public function invoice_edit_documents()
    {
        $this->edit_documents();
    }
    public function quote_edit_documents()
    {
        $this->edit_documents();
    }
    public function invoice_download_file($file_id, $doc_module)
    {
        $this->download_file(empty($doc_module) ? "INV" : $doc_module, $file_id);
    }
    public function invoice_delete_document()
    {
        $doc_module = $this->input->post("module");
        $doc_module = empty($doc_module) ? "INV" : $doc_module;
        $this->delete_document($doc_module);
    }
    public function invoice_payment_download_file($file_id, $doc_module = NULL)
    {
        $doc_module = empty($doc_module) ? "INV-PY" : $doc_module;
        $this->download_file($doc_module, $file_id);
    }
    private function validate_voucher_and_invoice_payment($voucher_id, $payment_id)
    {
        $this->load->model("invoice_payment_invoice");
        $invoice_record = $this->invoice_payment_invoice->load_invoice_record($payment_id);
        return $invoice_record["voucher_header_id"] == $voucher_id;
    }
    public function expense_export_to_excel($expense_type = "")
    {
        $this->load->helper("text");
        $filter = json_decode($this->input->post("filter"), true);
        $sortable = json_decode($this->input->post("sort"), true);
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $selected_columns = [];
        if ($this->input->post("export_all_columns") == "false") {
            $selected_columns = $this->return_current_columns("Expense");
        }
        $page_number = json_decode($this->input->post("page_number"), true);
        if ($expense_type != "all_expenses") {
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $auth_user_id = $this->session->userdata("AUTH_user_id");
            $user_accounts_mapping = $this->account->load_account_user_mapping($auth_user_id);
            if (!empty($user_accounts_mapping)) {
                $data = $selected_columns + $this->voucher_header->k_load_all_expenses($filter, $sortable, false, false, $page_number);
            } else {
                $data["data"] = [];
            }
        } else {
            $data = $selected_columns + $this->voucher_header->k_load_all_expenses($filter, $sortable, false, false, $page_number);
        }
        $filename = $expense_type == "all_expenses" ? urlencode($this->lang->line("expenses")) : urlencode($this->lang->line("export_my_expenses"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/expenses_list", $data);
        $this->load->view("excel/footer");
    }
    public function bill_export_to_excel()
    {
        $filter = json_decode($this->input->post("filter"), true);
        $sortable = json_decode($this->input->post("sort"), true);
        $selected_columns = ["columns_to_select" => false];
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        if ($this->input->post("export_all_columns") == "false") {
            $selected_columns = $this->return_current_columns("Bill_Header");
        }
        $data = $selected_columns + $this->voucher_header->k_load_all_bills($filter, $sortable);
        $filename = urlencode($this->lang->line("excel_bills"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/bills_list", $data);
        $this->load->view("excel/footer");
    }
    public function invoice_export_to_excel()
    {
        $filter = json_decode($this->input->post("filter"), true);
        $sortable = json_decode($this->input->post("sort"), true);
        $selected_columns = ["columns_to_select" => false];
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        if ($this->input->post("export_all_columns") == "false") {
            $selected_columns = $this->return_current_columns("Invoice_Header");
        }
        $data = $selected_columns + $this->voucher_header->k_load_all_invoices($filter, $sortable, json_decode($this->input->post("page_number"), true));
        $filename = urlencode($this->lang->line("excel_invoices"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/invoices_list", $data);
        $this->load->view("excel/footer");
    }
    public function invoice_export_to_word($voucher_header_id = 0, $invoiceTemplateId = 0, $format = "")
    {
        if (0 < $voucher_header_id && !$this->validate_voucher($voucher_header_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $data = [];
        $this->load->model(["money_preference"]);
        $this->load->model("user_preference");
        $invoice_lang = $this->money_preference->get_values_by_group("InvoiceLanguage");
        $moneyLanguage = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        $system_language = $this->session->userdata("AUTH_language");
        $this->switch_language($system_language, $moneyLanguage, false);
        foreach ($invoice_lang as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$moneyLanguage];
        }
        $invoice_statuses = ["draft" => $this->lang->line("draft"), "open" => $this->lang->line("open"), "paid" => $this->lang->line("paid"), "partially paid" => $this->lang->line("partially_paid"), "cancelled" => $this->lang->line("cancelled")];
        $this->load->model("organization", "organizationfactory");
        $this->organization = $this->organizationfactory->get_instance();
        $data = $this->fill_template_settings($data, $invoiceTemplateId, $moneyLanguage);
        $data["is_sample_template"] = $data["template"]["settings"] ? false : true;
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["export_header"] = $this->voucher_header->load_invoice_for_template($voucher_header_id, $this->user_preference->get_value("money_language"));
        $data["export_header"]["paid_status"] = $invoice_statuses[$data["export_header"]["paidStatus"]];
        $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_id);
        $data["export_details"] = $this->invoice_detail->load_invoice_details($data["export_header"]["id"], $this->user_preference->get_value("money_language"));
        $data["invoice_description"] = $data["export_header"]["description"];
        $data["form_sub_total"] = $this->input->post("form_sub_total");
        $data["form_total_tax"] = $this->input->post("form_total_tax");
        $data["form_total"] = $this->input->post("form_total");
        $prefix = $data["export_header"]["prefix"];
        $organizationID = $this->session->userdata("organizationID");
        $INPValue = unserialize($systemPreferences["invoiceNumberPrefix"]);
        if (!empty($organizationID) && isset($systemPreferences["invoiceNumberPrefix"]) && isset($INPValue[$organizationID])) {
            $prefix = $INPValue[$organizationID];
        }
        $data["e_invoicing"] = $this->organization->check_if_einvoice_active($organizationID);
        $data["qr_code"] = $data["e_invoicing"] ? $this->generate_qr_code($voucher_header_id, "invoice") : NULL;
        $fileName = $data["export_header"]["prefix"] . $data["export_header"]["refNum"] . "_" . date("Ymd");
        $this->switch_language($moneyLanguage, $system_language, true);
        $this->voucher_fill_common_data_and_download_file("invoices", $data, $fileName, "invoice", $format);
    }
    private function generate_qr_code($id, $voucher_type)
    {
        $corepath = substr(COREPATH, 0, -12);
        $temp_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "invoices" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id() . DIRECTORY_SEPARATOR;
        if (!is_dir($temp_directory)) {
            @mkdir($temp_directory, 493);
        }
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        $generated_string = "";
        $file_name = $temp_directory . time() . "qr_image.png";
        require_once $corepath . "/application/libraries/zatca-qr/Includes.php";
        require_once $corepath . "/application/libraries/phpqrcode/qrlib.php";
        if ($voucher_type == "invoice") {
            $data["qr_data"] = $this->voucher_header->load_invoice_qr_data($id);
            if (!empty($data["qr_data"]["rate"])) {
                $total = round($data["qr_data"]["total"] * $data["qr_data"]["rate"], $allowed_decimal_format);
                $total_tax = round($data["qr_data"]["total_tax"] * $data["qr_data"]["rate"], $allowed_decimal_format);
            } else {
                $total = $data["qr_data"]["total"];
                $total_tax = $data["qr_data"]["total_tax"];
            }
            $generated_string = GenerateQrCode::fromArray([new Seller($data["qr_data"]["entity_name"]), new TaxNumber($data["qr_data"]["entity_tax_number"]), new InvoiceDate(date("Y-m-d H:i:s", strtotime($data["qr_data"]["invoice_date"]))), new InvoiceTotalAmount($total), new InvoiceTaxAmount($total_tax)])->toBase64();
        } else {
            if ($voucher_type == "credit_note") {
                $data["qr_data"] = $this->credit_note_header->load_credit_note_qr_data($id);
                if (!empty($data["qr_data"]["rate"])) {
                    $total = round($data["qr_data"]["total"] * $data["qr_data"]["rate"], $allowed_decimal_format);
                    $total_tax = round($data["qr_data"]["total_tax"] * $data["qr_data"]["rate"], $allowed_decimal_format);
                } else {
                    $total = $data["qr_data"]["total"];
                    $total_tax = $data["qr_data"]["total_tax"];
                }
                $generated_string = GenerateQrCode::fromArray([new Seller($data["qr_data"]["entity_name"]), new TaxNumber($data["qr_data"]["entity_tax_number"]), new InvoiceDate(date("Y-m-d H:i:s", strtotime($data["qr_data"]["credit_note_date"]))), new InvoiceTotalAmount($total), new InvoiceTaxAmount($total_tax)])->toBase64();
            } else {
                return NULL;
            }
        }
        QRcode::png($generated_string, $file_name, "H", 2);
        return $file_name;
    }
    private function related_documents($id = "", $voucherType, $objName)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("related_documents") . " | " . $this->lang->line($objName . "s") . " | " . $this->lang->line("money"));
        $data = [];
        $data["id"] = $id;
        $active = site_url("vouchers/" . $objName . "_related_documents/");
        $tabsFunc = "_get_" . $objName . "_tabs_view_vars";
        $data["tabsNLogs"] = $this->{$tabsFunc}($id, $active);
        $data["objName"] = $objName;
        $data["module"] = $voucherType;
        $data["module_record"] = "contract";
        $data["module_record_id"] = $id;
        $data["module_controller"] = "vouchers";
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->load->model("user_preference");
        $document_editor = $this->user_preference->get_value_by_user("document_editor", $this->is_auth->get_user_id());
        if (!empty($document_editor)) {
            $document_editor = unserialize($this->user_preference->get_value_by_user("document_editor", $this->is_auth->get_user_id()));
        }
        if (isset($document_editor["installation_popup_displayed"])) {
            if (!$document_editor["installation_popup_displayed"]) {
                $data["show_document_editor_installation_modal"] = true;
                $document_editor["installation_popup_displayed"] = true;
                $this->user_preference->set_value("document_editor", serialize($document_editor), $this->is_auth->get_user_id());
            }
        } else {
            $this->user_preference->set_value("document_editor", serialize(["installation_popup_displayed" => true]), $this->is_auth->get_user_id());
            $data["show_document_editor_installation_modal"] = true;
        }
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("money/js/documents_management_system", "js");
        $this->includes("jquery/dropzone", "js");
        $this->includes("jquery/css/dropzone", "css");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("vouchers/documents", $data);
        $this->load->view("partial/footer");
    }
    public function preview_document($id = 0)
    {
        $response = [];
        if (0 < $id) {
            echo $this->dms->get_preview_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dms->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("modules/money/vouchers/preview_document/" . $id);
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", ["mode" => "preview"], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_documents()
    {
        $response = $this->dms->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "term" => $this->input->post("term")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    private function upload_file()
    {
        $response = $this->dms->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "upload_key" => "uploadDoc", "comment" => $this->input->post("comment")]);
        if ($this->input->post("dragAndDrop")) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $html = "<html>\r\n                <head>\r\n                    <meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />\r\n                    <script type=\"text/javascript\">\r\n                        if(window.top.uploadDocumentDone) window.top.uploadDocumentDone('" . $response["message"] . "', '" . ($response["status"] ? "success" : "error") . "');\r\n                    </script>\r\n                </head>\r\n            </html>";
            $this->output->set_content_type("text/html")->set_output($html);
        }
    }
    private function rename_file($module)
    {
        $response = $this->dms->rename_document($module, $this->input->post("document_id"), "file", $this->input->post("new_name"));
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    private function edit_documents()
    {
        $response = $this->dms->edit_documents(json_decode($this->input->post("models"), true));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function download_file($module, $file_id)
    {
        $response = $this->dms->download_file($module, $file_id);
        if (!$response["status"]) {
            $this->set_flashmessage("error", $response["message"]);
            redirect($this->agent->referrer());
        }
    }
    private function delete_document($module)
    {
        $response = $this->dms->delete_document($module, $this->input->post("document_id"));
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    private function invoice_details_pre_export($invoiceId, $templateId)
    {
        $this->load->view("invoices/invoice_details_pre_export", ["invoiceId" => $invoiceId, "templateId" => $templateId]);
    }
    public function invoice_details_export($withCover, $invoiceId, $templateId)
    {
        $corepath = substr(COREPATH, 0, -12);
        $this->load->model("invoice_detail_cover_page_template", "invoice_detail_cover_page_template_factory");
        $this->covertemplate = $this->invoice_detail_cover_page_template_factory->get_instance();
        $this->covertemplate->fetch($templateId);
        $template_name = "invoice-details-" . $this->session->userdata("AUTH_language") . ".docx";
        $templateDirctory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "cover_templates" . DIRECTORY_SEPARATOR . $template_name;
        $uploadDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "cover_templates" . DIRECTORY_SEPARATOR . str_pad($this->covertemplate->get_field("organization_id"), 4, "0", STR_PAD_LEFT);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "cover_templates";
        $logo = $this->covertemplate->get_field("logo");
        $file_logo = $uploadDirectory . DIRECTORY_SEPARATOR . $logo;
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $imageOptions = ["src" => $file_logo, "dpi" => 300, "width" => 480, "height" => 150, "imageAlign" => "right"];
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $voucher = $this->voucher_header->k_load_all_invoices(["filters" => [["field" => "voucher_headers.id", "operator" => "eq", "value" => $invoiceId]]], false);
        $voucher = $voucher["data"][0];
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->invoice_header->fetch(["voucher_header_id" => $invoiceId]);
        $invoiceNumber = $this->invoice_header->get_field("invoiceNumber");
        if ($withCover == 1) {
            $docx = new CreateDocxFromTemplate($templateDirctory);
            $valuesTable = [];
            if (file_exists($file_logo) && strcmp($logo, "")) {
                $headerImage = new WordFragment($docx, "firstHeader");
                $headerImage->addImage($imageOptions);
                $valuesTable[0][0]["value"] = $headerImage;
                $valuesTable[0][0]["vAlign"] = "center";
                $widthTableCols = ["12308"];
                $paramsTable = ["border" => "nil", "columnWidths" => $widthTableCols];
                $headerTable = new WordFragment($docx, "firstHeader");
                $headerTable->addTable($valuesTable, $paramsTable);
                $docx->addHeader(["first" => $headerTable]);
            }
            $postVars = $this->input->post(NULL, false);
            $date = date("d.m.Y", time());
            $clientName = isset($this->licensor) ? $this->licensor->get("clientName") : "";
            $docx_variables = ["SUBHEADER" => ["type" => "text", "data" => $this->covertemplate->get_field("subHeader")], "HEADER" => ["type" => "text", "data" => $this->covertemplate->get_field("header")], "EMAIL" => ["type" => "text", "data" => $this->covertemplate->get_field("email")], "COMPANY_NAME" => ["type" => "text", "data" => $voucher["accountName"]], "INVOICE_NUMBER" => ["type" => "text", "data" => $invoiceNumber], "INVOICE_ID" => ["type" => "text", "data" => $invoiceId], "CASE_INTERNAL_REF" => ["type" => "text", "data" => $voucher["caseInternalReference"]], "REF" => ["type" => "text", "data" => $voucher["caseSubject"]], "CUR" => ["type" => "text", "data" => $voucher["clientCurrency"]], "AMOUNT" => ["type" => "text", "data" => $voucher["balanceDue"]], "date" => ["type" => "text", "data" => $date], "NOTE" => ["type" => "html", "data" => nl2br($this->invoice_header->get_field("notes")), "display_type" => "inline"], "CLIENT_NAME" => ["type" => "text", "data" => $clientName]];
            $this->load->library("phpdocxconf");
            $phpdocxObj = new phpdocxconf();
            $breaks = ["<br />", "<br>", "<br/>", "<br />", "<br>", "<br/>"];
            $text = str_ireplace($breaks, "\\n\\r", nl2br($this->covertemplate->get_field("footer")));
            $docx->replaceVariableByText(["FOOTER_DATA" => $text], ["target" => "footer", "parseLineBreaks" => true]);
            $billTo = str_ireplace($breaks, "\\n\\r", nl2br($this->invoice_header->get_field("billTo")));
            $docx->replaceVariableByText(["BILL_TO" => $billTo], ["parseLineBreaks" => true]);
            $phpdocxObj->replaceVariables($docx, $docx_variables);
        } else {
            $docx = new createDocx();
            $valuesTable = [];
            if (($file_header = $this->covertemplate->get_field("header")) != "") {
                $headerText = new WordFragment($docx, "defaultHeader");
                $headerText->addText($file_header, ["fontSize" => "8"]);
                $valuesTable[0][0]["value"] = $headerText;
                $valuesTable[0][0]["vAlign"] = "center";
            }
            if (file_exists($file_logo) && strcmp($logo, "")) {
                $headerImage = new WordFragment($docx, "defaultHeader");
                $headerImage->addImage($imageOptions);
                $valuesTable[0][1]["value"] = $headerImage;
                $valuesTable[0][1]["vAlign"] = "center";
            }
            if (!empty($valuesTable)) {
                $widthTableCols = ["7308", "5000"];
                $paramsTable = ["border" => "nil", "columnWidths" => $widthTableCols];
                $headerTable = new WordFragment($docx, "defaultHeader");
                $headerTable->addTable($valuesTable, $paramsTable);
                $docx->addHeader(["default" => $headerTable]);
            }
            $docx->modifyPageLayout("A4-landscape");
            $docx->addBreak();
            $docx->embedHTML($this->get_invoice_detail_data($invoiceId));
            $docx->addBreak();
        }
        $docx->createDocx($tempDirectory . "/test");
        $this->load->helper("download");
        $content = file_get_contents($tempDirectory . "/test.docx");
        unlink($tempDirectory . "/test.docx");
        $fileName = $this->invoice_header->get_field("prefix") . $voucher["refNum"] . $this->invoice_header->get_field("suffix") . "_" . date("YmdHi") . ".docx";
        force_download($fileName, $content);
        exit;
    }
    private function get_invoice_detail_data($id)
    {
        $data = [];
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->load->model("item_commission");
        $this->load->model("invoice_detail_look_feel_section", "invoice_detail_look_feel_section_factory");
        $this->Section = $this->invoice_detail_look_feel_section_factory->get_instance();
        $sections = $this->Section->load_all();
        $data["sections"] = $sections;
        $this->load->model("user_preference");
        $moneyLanguage = $this->user_preference->get_value("money_language");
        $data["moneyLanguage"] = $moneyLanguage;
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->invoice_header->fetch(["voucher_header_id" => $id]);
        $data["activateTax"] = $this->invoice_header->get_field("displayTax");
        $data["activateDiscount"] = $this->invoice_header->get_field("displayDiscount");
        $data["discount_percentage"] = $this->invoice_header->get_field("discount_percentage");
        $data["invoice"] = $this->voucher_header->load_invoice_voucher($id);
        $this->invoice_header->fetch(["voucher_header_id" => $voucher_id]);
        $data["time_logs_grouped"] = $this->invoice_header->get_field("groupTimeLogsByUserInExport");
        if (!empty($data["invoice"])) {
            $moneyLanguage = $this->user_preference->get_value("money_language");
            $data["invoice_details"] = $this->invoice_detail->load_invoice_details($data["invoice"]["id"], $moneyLanguage);
            $data["item_commissions"] = $this->item_commission->fetch_commissions($data["invoice"]["id"]);
            if (!empty($data["item_commissions"])) {
                $data["partners"] = [];
                foreach ($data["item_commissions"] as $item_commissions_val) {
                    if (array_key_exists($item_commissions_val["partnerName"], $data["partners"])) {
                        $data["partners"][$item_commissions_val["partnerName"]][$item_commissions_val["invoice_details_id"]] = $item_commissions_val;
                    } else {
                        $data["partners"][$item_commissions_val["partnerName"]] = [];
                        $data["partners"][$item_commissions_val["partnerName"]][$item_commissions_val["invoice_details_id"]] = $item_commissions_val;
                    }
                }
            }
            if (isset($data["partners"])) {
                $data["item_commissions"] = [];
                foreach ($data["partners"] as $key => $val) {
                    array_push($data["item_commissions"], $data["partners"][$key]);
                }
            }
            $data["items"] = [];
            $data["expenses"] = [];
            $data["time_logs"] = [];
            foreach ($data["invoice_details"] as $val) {
                if (!empty($val["time_logs_id"])) {
                    if (array_key_exists($val["worker"], $data["time_logs"])) {
                        array_push($data["time_logs"][$val["worker"]], $val);
                    } else {
                        $data["time_logs"][$val["worker"]] = [];
                        array_push($data["time_logs"][$val["worker"]], $val);
                    }
                } else {
                    if (!empty($val["expense_id"])) {
                        array_push($data["expenses"], $val);
                    } else {
                        if (!empty($val["item_id"])) {
                            array_push($data["items"], $val);
                        }
                    }
                }
            }
        }
        $direction = in_array($this->session->userdata("AUTH_language"), ["arabic", "persian", "hibrew"]) ? "rtl" : "ltr";
        $html = "<table width=\"100%\" style=\"font-size: 14.5px\" dir=\"" . $direction . "\"><tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td align=\"left\">" . $this->lang->line("quantity") . "</td><td>&nbsp;</td><td  align=\"left\">" . $this->lang->line("total") . "</td></tr>";
        foreach ($data["sections"] as $section) {
            $fieldName = "name";
            $fieldName = $data["moneyLanguage"] . $fieldName;
            $title = $section[$fieldName];
            if (isset($section["content"]) && $section["content"] != "") {
                $html .= "<tr style='height:1px;'><td style=' font-weight: bold' colspan=\"4\">" . $title . "</td></tr>";
            }
            $content = explode(",", $section["content"]);
            foreach ($content as $id) {
                switch ($id) {
                    case "0":
                        $html .= $this->load->view("invoices/details_report_items", ["items" => $data["items"], "title" => $title, "activateDiscount" => $data["activateDiscount"], "discount_percentage" => $data["discount_percentage"], "activateTax" => $data["activateTax"]], true);
                        break;
                    case "1":
                        $html .= $this->load->view("invoices/details_report_expenses", ["expenses" => $data["expenses"], "invoice" => $data["invoice"], "title" => $title, "activateDiscount" => $data["activateDiscount"], "discount_percentage" => $data["discount_percentage"], "activateTax" => $data["activateTax"]], true);
                        break;
                    case "2":
                        $html .= $this->load->view("invoices/details_report_time_logs", ["timeLogs" => $data["time_logs"], "title" => $title, "activateDiscount" => $data["activateDiscount"], "discount_percentage" => $data["discount_percentage"], "activateTax" => $data["activateTax"], "time_logs_grouped" => $data["time_logs_grouped"]], true);
                        break;
                }
            }
        }
        $html .= "\r\n            <tr>\r\n                <td>&nbsp;</td>\r\n                <td>&nbsp;</td>\r\n                <td><strong>" . $this->lang->line("total") . ":" . "</strong></td>\r\n                <td>&nbsp;</td>\r\n                <td><strong>" . $invoice["clientAccountCurrency"] . "</strong></td>\r\n                <td><strong>" . number_format($this->sumItemsFees + $this->sumOtherServicesFees + $this->sumLegalFees, 2, ".", ",") . "</strong></td>\r\n            </tr>";
        $html .= "</table>";
        return $html;
    }
    public function invoice_edit_unhide_tax()
    {
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->invoice_header->unhide_invoice_tax($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function invoice_edit_hide_tax()
    {
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->invoice_header->hide_invoice_tax($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function bill_edit_unhide_tax()
    {
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->bill_header->unhide_bill_tax($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function bill_edit_hide_tax()
    {
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->bill_header->hide_bill_tax($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function invoice_edit_unhide_discount()
    {
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->invoice_header->unhide_invoice_discount($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function invoice_edit_hide_discount()
    {
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["status"] = $this->invoice_header->hide_invoice_discount($voucherID);
                if ($response["status"]) {
                    $response["status"] = 101;
                } else {
                    $response["status"] = 102;
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function bills_add_hide_unhide_tax($tax = "")
    {
        $this->bill_save(0, $tax);
    }
    public function bills_bulk_payment()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("bulk_payment") . " | " . $this->lang->line("bills") . " | " . $this->lang->line("money"));
        $data["supplier_id"] = "";
        $data = ["bills" => [], "supplier_id" => "", "supplier_name" => "", $data["supplier_id"]];
        if ($this->input->is_ajax_request()) {
            $supplier_account_id = $this->input->post("supplier_account_id");
            if (0 < $supplier_account_id) {
                $data["bills"] = $this->voucher_header->load_all_open_bills_by_supplier_account($supplier_account_id);
            }
            $response["html"] = $this->load->view("bills/bills_list", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->load->model("exchange_rate");
            $data["rates"] = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
            if ($this->input->post(NULL)) {
                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $this->load->model(["bill_payment", "bill_payment_bill"]);
                $this->load->model("bill_header", "bill_headerfactory");
                $this->bill_header = $this->bill_headerfactory->get_instance();
                $voucherIds = join(",", $this->input->post("voucherIds"));
                $data["bill_data"] = $this->voucher_header->fetch_bills_voucher($voucherIds, true);
                $_POST["paidOn"] = date("Y-m-d H:i", strtotime($this->input->post("paidOn")));
                $result = true;
                if ($result) {
                    $this->account->fetch($this->input->post("account_id"));
                    $paid_through_currency = $this->account->get_field("currency_id");
                    $i = 0;
                    $_POST["amount"] = $this->input->post("amount") * $data["rates"][$paid_through_currency];
                    foreach ($data["bill_data"] as $bill_val) {
                        $i++;
                        $balance_due = 0;
                        $injectPayment = false;
                        if ($bill_val["balanceDue"] <= $this->input->post("amount")) {
                            $balance_due = $bill_val["balanceDue"] / $data["rates"][$paid_through_currency];
                            $_POST["amount"] = $this->input->post("amount") - $bill_val["balanceDue"];
                            $status = "paid";
                            $injectPayment = true;
                        } else {
                            if (0 < $this->input->post("amount")) {
                                $balance_due = $this->input->post("amount") / $data["rates"][$paid_through_currency];
                                $status = "partially paid";
                                $injectPayment = true;
                                $_POST["amount"] = 0;
                            }
                        }
                        if ($injectPayment) {
                            $this->voucher_header->reset_fields();
                            $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                            $this->voucher_header->set_field("refNum", $this->auto_generate_rf("BI-PY"));
                            $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($this->input->post("paidOn"))));
                            $this->voucher_header->set_field("voucherType", "BI-PY");
                            $this->voucher_header->set_field("referenceNum", $this->input->post("referenceNum"));
                            $this->voucher_header->set_field("description", $this->input->post("comments"));
                            if ($this->voucher_header->insert()) {
                                $this->voucher_detail->reset_fields();
                                $voucher_header_id = $this->voucher_header->get_field("id");
                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                $this->voucher_detail->set_field("account_id", $this->input->post("account_id"));
                                $this->voucher_detail->set_field("drCr", "C");
                                $this->voucher_detail->set_field("local_amount", $balance_due * $data["rates"][$paid_through_currency]);
                                $this->voucher_detail->set_field("foreign_amount", $balance_due);
                                $this->voucher_detail->insert();
                                $first_voucher_detail_id = $this->voucher_detail->get_field("id");
                                $this->voucher_detail->reset_fields();
                                $this->account->fetch($this->input->post("supplierAccountId"));
                                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                $this->voucher_detail->set_field("account_id", $this->input->post("supplierAccountId"));
                                $this->voucher_detail->set_field("drCr", "D");
                                $this->voucher_detail->set_field("local_amount", $balance_due * $data["rates"][$paid_through_currency]);
                                $this->voucher_detail->set_field("foreign_amount", $balance_due * $data["rates"][$paid_through_currency] / $data["rates"][$this->account->get_field("currency_id")]);
                                if ($this->voucher_detail->insert()) {
                                    $this->bill_payment->reset_fields();
                                    $this->bill_payment->set_field("voucher_header_id", $voucher_header_id);
                                    $this->bill_payment->set_field("account_id", $this->input->post("account_id"));
                                    $this->bill_payment->set_field("paymentMethod", $this->input->post("paymentMethod"));
                                    $this->bill_payment->set_field("total", $balance_due);
                                    $this->bill_payment->set_field("supplier_account_id", $this->input->post("supplierAccountId"));
                                    $this->bill_payment->set_field("billPaymentTotal", $balance_due * $data["rates"][$paid_through_currency]);
                                    if ($this->bill_payment->insert()) {
                                        $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $bill_val["id"]);
                                        $this->voucher_detail->update();
                                        $this->voucher_detail->fetch($first_voucher_detail_id);
                                        $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $bill_val["id"]);
                                        $this->voucher_detail->update();
                                        $bill_payment_id = $this->bill_payment->get_field("id");
                                        $this->bill_payment_bill->reset_fields();
                                        $this->bill_header->fetch(["voucher_header_id" => $bill_val["id"]]);
                                        $this->bill_payment_bill->set_field("bill_payment_id", $bill_payment_id);
                                        $this->bill_payment_bill->set_field("bill_header_id", $this->bill_header->get_field("id"));
                                        $this->bill_payment_bill->set_field("amount", $balance_due * $data["rates"][$paid_through_currency]);
                                        if ($this->bill_payment_bill->insert()) {
                                            $this->bill_header->set_field("status", $status);
                                            $this->bill_header->update();
                                            $result = true;
                                            if (!empty($_FILES) && !empty($_FILES["file"]["name"])) {
                                                $upload_response = $this->dms->upload_file(["module" => "BI-PY", "module_record_id" => $voucher_header_id, "upload_key" => "file"]);
                                            }
                                        } else {
                                            $this->bill_payment->delete($bill_payment_id);
                                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $result = false;
                                        }
                                    } else {
                                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                        $this->voucher_header->delete($voucher_header_id);
                                        $result = false;
                                    }
                                } else {
                                    $this->voucher_header->delete($voucher_header_id);
                                    $result = false;
                                }
                            }
                        }
                    }
                }
                if ($result) {
                    $this->set_flashmessage("success", "success");
                    $supplierAccount = $this->account->fetch_account($this->input->post("supplierAccountId"));
                    $data["supplier_id"] = $supplierAccount["id"];
                    $data["supplier_name"] = $supplierAccount["fullName"] . " - " . $supplierAccount["currencyCode"];
                    redirect("vouchers/bills_list/0/" . $data["supplier_id"]);
                }
            }
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model("bill_payment");
            $data["paymentMethod"] = $this->bill_payment->get("paymentMethodValues");
            array_unshift($data["paymentMethod"], "");
            $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("bank_transfer"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            $data["accounts"] = $this->account->load_accounts_per_organization("AssetCashBank");
            $data["rates"] = json_encode($data["rates"]);
            $this->load->view("partial/header");
            $this->load->view("bills/bulk_payments", $data);
            $this->load->view("partial/footer");
        }
    }
    public function invoice_details_export_excel($voucher_id = 0, $templateId = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("invoice_details") . " | " . $this->lang->line("money"));
        if (0 < $voucher_id) {
            $voucherId = $voucher_id;
            $this->load->model("voucher_header", "voucher_headerfactory");
            $this->voucher_header = $this->voucher_headerfactory->get_instance();
            $this->load->model("invoice_detail", "invoice_detailfactory");
            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->load->model("item_commission");
            $this->load->model("invoice_detail_cover_page_template", "invoice_detail_cover_page_template_factory");
            $this->covertemplate = $this->invoice_detail_cover_page_template_factory->get_instance();
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->invoice_header->fetch(["voucher_header_id" => $voucher_id]);
            $activateTax = $this->invoice_header->get_field("displayTax");
            $activateDiscount = $this->invoice_header->get_field("displayDiscount");
            $discount_percentage = $this->invoice_header->get_field("discount_percentage");
            $invoiceNumber = $this->invoice_header->get_field("invoiceNumber");
            $time_logs_grouped = $this->invoice_header->get_field("groupTimeLogsByUserInExport");
            $this->load->model("user_preference");
            $moneyLanguage = $this->user_preference->get_value("money_language");
            $invoice = $this->voucher_header->load_invoice_voucher($voucher_id);
            $voucher_related_cases = $this->voucher_related_case->load_voucher_related_cases($voucher_id);
            $items = [];
            $expenses = [];
            $timeLogs = [];
            $partners = [];
            $normalPartners = [];
            $thirdParties = [];
            $partnersByTypeId = [];
            $partnersByTypeId["items"] = [];
            $partnersByTypeId["expenses"] = [];
            $partnersByTypeId["timeLogs"] = [];
            if (!empty($invoice)) {
                $moneyLanguage = $this->user_preference->get_value("money_language");
                $invoice_details = $this->invoice_detail->load_invoice_details($invoice["id"], $moneyLanguage);
                $itemCommissions = $this->item_commission->fetch_commissions($invoice["id"]);
                if (!empty($itemCommissions)) {
                    foreach ($itemCommissions as $curItemCommission) {
                        if (!strcmp($curItemCommission["isThirdParty"], "yes")) {
                            $thirdParties[$curItemCommission["partnerName"]][$curItemCommission["invoice_details_id"]] = $curItemCommission;
                        } else {
                            $normalPartners[$curItemCommission["partnerName"]][$curItemCommission["invoice_details_id"]] = $curItemCommission;
                        }
                        if (strcmp($curItemCommission["item_id"], "")) {
                            $partnersByTypeId["items"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                        } else {
                            if (strcmp($curItemCommission["expense_id"], "")) {
                                $partnersByTypeId["expenses"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                            } else {
                                if (strcmp($curItemCommission["time_logs_id"], "")) {
                                    $partnersByTypeId["timeLogs"][$curItemCommission["invoice_details_id"]][$curItemCommission["partnerName"]][] = $curItemCommission;
                                }
                            }
                        }
                    }
                    $partners = array_merge_recursive($normalPartners, $thirdParties);
                }
                foreach ($invoice_details as $val) {
                    if (!empty($val["time_logs_id"])) {
                        $timeLogs[$val["worker"]][] = $val;
                    } else {
                        if (!empty($val["expense_id"])) {
                            array_push($expenses, $val);
                        } else {
                            if (!empty($val["item_id"])) {
                                array_push($items, $val);
                            }
                        }
                    }
                    if (!empty($val["invoiceId"])) {
                        $invoice_num = $val["invoiceId"];
                    }
                }
            }
            $clientName = isset($this->licensor) ? $this->licensor->get("clientName") : "";
            $this->load->library("PHPExcel");
            $objPHPExcel = new PHPExcel();
            $objPHPExcel->setActiveSheetIndex(0);
            $alignCenter = ["alignment" => ["horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_CENTER]];
            $alignRight = ["alignment" => ["horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT]];
            $alignLeft = ["alignment" => ["horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_LEFT]];
            $boldRight = ["alignment" => ["horizontal" => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT], "font" => ["bold" => true]];
            $objPHPExcel->getActiveSheet()->setCellValue("C2", "Services & Legal Fees - " . $clientName);
            $objPHPExcel->getActiveSheet()->mergeCells("C2:E2");
            $objPHPExcel->getActiveSheet()->setCellValue("A4", "Invoice#");
            $objPHPExcel->getActiveSheet()->setCellValue("B4", $invoice_num);
            $objPHPExcel->getActiveSheet()->setCellValue("A5", "Client:");
            $objPHPExcel->getActiveSheet()->setCellValue("B5", $invoice["clientAccountName"]);
            $objPHPExcel->getActiveSheet()->setCellValue("A9", "Matter");
            $objPHPExcel->getActiveSheet()->setCellValue("B9", "C/M No.");
            $objPHPExcel->getActiveSheet()->setCellValue("C9", "Date");
            $objPHPExcel->getActiveSheet()->setCellValue("D9", "Services - " . $invoice["clientAccountName"]);
            $objPHPExcel->getActiveSheet()->setCellValue("E9", "Time");
            $objPHPExcel->getActiveSheet()->setCellValue("G9", "Fees");
            $objPHPExcel->getActiveSheet()->setCellValue("B10", $invoiceNumber);
            $objPHPExcel->getActiveSheet()->setCellValue("E10", "in h");
            $objPHPExcel->getActiveSheet()->setCellValue("D11", "General Consultancy from ");
            $objPHPExcel->getActiveSheet()->setCellValue("H9", "Tax");
            $objPHPExcel->getActiveSheet()->setCellValue("I9", "Fees with Tax");
            if (!empty($voucher_related_cases)) {
                $objPHPExcel->getActiveSheet()->setCellValue("C4", "Matter ID");
                $objPHPExcel->getActiveSheet()->getStyle("C4")->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue("C5", "Practice Area");
                $objPHPExcel->getActiveSheet()->getStyle("C5")->getFont()->setBold(true);
                $position = "D";
                foreach ($voucher_related_cases as $related_cases) {
                    $objPHPExcel->getActiveSheet()->setCellValue($position . "4", " " . $related_cases["legal_case_id"]);
                    $objPHPExcel->getActiveSheet()->setCellValue($position . "5", $related_cases["practice_area"]);
                    $position++;
                }
            }
            if (strcmp($templateId, "")) {
                $corepath = substr(COREPATH, 0, -12);
                $this->covertemplate->fetch($templateId);
                $uploadDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "cover_templates" . DIRECTORY_SEPARATOR . str_pad($this->covertemplate->get_field("organization_id"), 4, "0", STR_PAD_LEFT);
                $logo = $this->covertemplate->get_field("logo");
                $imgSrc = $uploadDirectory . DIRECTORY_SEPARATOR . $logo;
                if (file_exists($imgSrc) && strcmp($logo, "")) {
                    $objDrawing = new PHPExcel_Worksheet_Drawing();
                    $objDrawing->setPath($imgSrc);
                    $objDrawing->setCoordinates("E2");
                    $objDrawing->setOffsetX(30);
                    $objDrawing->setWidth(110);
                    $objDrawing->setHeight(55);
                    $objDrawing->getShadow()->setVisible(false);
                    $objDrawing->getShadow()->setDirection(45);
                    $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());
                }
            }
            $partnerArrayMap = [];
            $partnerNames = array_keys($partners);
            $paPosition = "J";
            $lastSheetLetter = "I";
            foreach ($partnerNames as $pName) {
                $objPHPExcel->getActiveSheet()->setCellValue($paPosition . "10", $pName);
                $objPHPExcel->getActiveSheet()->getStyle($paPosition . "10")->getFont()->setItalic(true);
                $partnerArrayMap[$pName] = $paPosition;
                $lastSheetLetter = $paPosition;
                $paPosition++;
            }
            $totalSumAll = ["timeLogs" => ["quantity" => 0, "amount" => 0, "partners" => []], "expenses" => ["quantity" => 0, "amount" => 0, "partners" => []], "items" => ["quantity" => 0, "amount" => 0, "partners" => []]];
            foreach ($totalSumAll as $category => $defaultVals) {
                foreach ($partnerArrayMap as $pName => $excelColumn) {
                    $totalSumAll[$category]["partners"][$pName] = 0;
                }
            }
            $rowNumber = 13;
            foreach ($timeLogs as $userLog => $userLogsArr) {
                $workerTitle = isset($userLogsArr[0]) && !empty($userLogsArr[0]) ? $userLogsArr[0]["workerTitle"] : "";
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, "Fees");
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, "Tax");
                $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, "Fees with Tax");
                $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $rowNumber++;
                $objPHPExcel->getActiveSheet()->setCellValue("B" . $rowNumber, $workerTitle . " " . $userLog);
                $objPHPExcel->getActiveSheet()->getStyle("B" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, $userLogsArr[0]["currencyCode"] . " " . $userLogsArr[0]["unitPrice"]);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $rowNumber += 2;
                $totalPerUser = [];
                $totalPerUser["quantity"] = 0;
                $totalPerUser["amount"] = 0;
                $totalPerUser["partners"] = [];
                foreach ($userLogsArr as $curLog) {
                    $amount = $curLog["sub_total_after_line_disc"];
                    if (!empty($discount_percentage) && ($activateDiscount == "invoice_level_before_tax" || $activateDiscount == "both_item_before_level")) {
                        $amount -= $amount * $discount_percentage * 1 / 100;
                    }
                    $tax = $curLog["tax_amount"];
                    $amount_with_tax = $amount + $tax;
                    $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($amount_with_tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("C" . $rowNumber, date("d.m.Y", strtotime($curLog["logDate"])));
                    $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, $curLog["time_log_description"]);
                    $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setWrapText(true);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $curLog["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($amount, 2));
                    $totalPerUser["quantity"] += $curLog["quantity"];
                    $totalPerUser["amount"] += $amount;
                    $totalPerUser["tax"] += $tax;
                    $totalPerUser["amount_with_tax"] = $totalPerUser["amount"] + $totalPerUser["tax"];
                    $timeLogRelatedPartners = isset($partnersByTypeId["timeLogs"][$curLog["id"]]) ? $partnersByTypeId["timeLogs"][$curLog["id"]] : [];
                    foreach ($partnerArrayMap as $pName => $excelColumn) {
                        $paCommission = 0;
                        if (!isset($timeLogRelatedPartners[$pName])) {
                            $partnerCommissionVal = "";
                        } else {
                            $commission = $curLog["sub_total_after_line_disc"];
                            $commissionStr = "";
                            foreach ($timeLogRelatedPartners[$pName] as $paData) {
                                $paCommission += $commission * $paData["commission"] / 100;
                                $commissionStr .= $paData["commission"] . "% ";
                            }
                            $partnerCommissionVal = number_format($paCommission, 0) . " (" . trim($commissionStr) . ")";
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($excelColumn . $rowNumber, $partnerCommissionVal);
                        $objPHPExcel->getActiveSheet()->getStyle($excelColumn . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        if (!isset($totalPerUser["partners"][$pName])) {
                            $totalPerUser["partners"][$pName] = $paCommission;
                        } else {
                            $totalPerUser["partners"][$pName] += $paCommission;
                        }
                    }
                    $rowNumber++;
                }
                $objPHPExcel->getActiveSheet()->setCellValue("E" . $rowNumber, "________");
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, "________");
                foreach ($partnerArrayMap as $pa => $letter) {
                    $objPHPExcel->getActiveSheet()->setCellValue($letter . $rowNumber, "________");
                }
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $rowNumber++;
                $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Sub - Total " . $userLog . ": ");
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $totalPerUser["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($totalPerUser["amount"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($totalPerUser["tax"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($totalPerUser["amount_with_tax"], 2));
                $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
                $totalSumAll["timeLogs"]["quantity"] += $totalPerUser["quantity"];
                $totalSumAll["timeLogs"]["amount"] += $totalPerUser["amount"];
                $totalSumAll["timeLogs"]["tax"] += $totalPerUser["tax"];
                $totalSumAll["timeLogs"]["amount_with_tax"] += $totalPerUser["amount_with_tax"];
                foreach ($totalPerUser["partners"] as $sPaName => $paTotal) {
                    $totalSumAll["timeLogs"]["partners"][$sPaName] += $paTotal;
                    $objPHPExcel->getActiveSheet()->setCellValue($partnerArrayMap[$sPaName] . $rowNumber, number_format($paTotal, 0));
                    $objPHPExcel->getActiveSheet()->getStyle($partnerArrayMap[$sPaName] . $rowNumber)->getFont()->setBold(true);
                }
                $rowNumber += 2;
            }
            $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Total Legal Services:");
            $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->applyFromArray($boldRight);
            $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $totalSumAll["timeLogs"]["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
            $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($totalSumAll["timeLogs"]["amount"], 2));
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($totalSumAll["timeLogs"]["tax"], 2));
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($totalSumAll["timeLogs"]["amount_with_tax"], 2));
            $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
            foreach ($totalSumAll["timeLogs"]["partners"] as $sPaName => $paTotal) {
                $objPHPExcel->getActiveSheet()->setCellValue($partnerArrayMap[$sPaName] . $rowNumber, number_format($paTotal, 0));
                $objPHPExcel->getActiveSheet()->getStyle($partnerArrayMap[$sPaName] . $rowNumber)->getFont()->setBold(true);
            }
            $rowNumber += 2;
            if (!empty($expenses)) {
                $objPHPExcel->getActiveSheet()->setCellValue("B" . $rowNumber, "Other Services");
                $objPHPExcel->getActiveSheet()->getStyle("B" . $rowNumber)->getFont()->setBold(true);
                $rowNumber += 2;
                $totalPerUser = [];
                $totalPerUser["quantity"] = 0;
                $totalPerUser["amount"] = 0;
                $totalPerUser["tax"] = 0;
                $totalPerUser["amount_with_tax"] = 0;
                $totalPerUser["partners"] = [];
                foreach ($expenses as $expense) {
                    $amount = $expense["sub_total_after_line_disc"];
                    if (!empty($discount_percentage) && ($activateDiscount == "invoice_level_before_tax" || $activateDiscount == "both_item_before_level")) {
                        $amount -= $amount * $discount_percentage * 1 / 100;
                    }
                    $tax = $expense["tax_amount"];
                    $amount_with_tax = $amount + $tax;
                    $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($amount_with_tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("C" . $rowNumber, date("d.m.Y", strtotime($expense["paidOn"])));
                    $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, $expense["itemDescription"]);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $expense["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($amount, 2));
                    $totalPerUser["quantity"] += $expense["quantity"];
                    $totalPerUser["amount"] += $amount;
                    $totalPerUser["tax"] += $tax;
                    $totalPerUser["amount_with_tax"] = $totalPerUser["amount"] + $totalPerUser["tax"];
                    $expensesRelatedPartners = isset($partnersByTypeId["expenses"][$expense["id"]]) ? $partnersByTypeId["expenses"][$expense["id"]] : [];
                    foreach ($partnerArrayMap as $pName => $excelColumn) {
                        $paCommission = 0;
                        if (!isset($expensesRelatedPartners[$pName])) {
                            $partnerCommissionVal = "";
                        } else {
                            $commission = $expense["sub_total_after_line_disc"];
                            $commissionStr = "";
                            foreach ($expensesRelatedPartners[$pName] as $paData) {
                                $paCommission += $commission * $paData["commission"] / 100;
                                $commissionStr .= $paData["commission"] . "% ";
                            }
                            $partnerCommissionVal = number_format($paCommission, 0) . " (" . trim($commissionStr) . ")";
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($excelColumn . $rowNumber, $partnerCommissionVal);
                        $objPHPExcel->getActiveSheet()->getStyle($excelColumn . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        if (!isset($totalPerUser["partners"][$pName])) {
                            $totalPerUser["partners"][$pName] = $paCommission;
                        } else {
                            $totalPerUser["partners"][$pName] += $paCommission;
                        }
                    }
                    $rowNumber++;
                }
                $objPHPExcel->getActiveSheet()->setCellValue("E" . $rowNumber, "________");
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, "________");
                foreach ($partnerArrayMap as $pa => $letter) {
                    $objPHPExcel->getActiveSheet()->setCellValue($letter . $rowNumber, "________");
                }
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $rowNumber++;
                $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Total Other Services: ");
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $totalPerUser["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($totalPerUser["amount"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($totalPerUser["tax"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($totalPerUser["amount_with_tax"], 2));
                $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
                $totalSumAll["expenses"]["quantity"] += $totalPerUser["quantity"];
                $totalSumAll["expenses"]["amount"] += $totalPerUser["amount"];
                $totalSumAll["expenses"]["tax"] += $totalPerUser["tax"];
                foreach ($totalPerUser["partners"] as $sPaName => $paTotal) {
                    $totalSumAll["expenses"]["partners"][$sPaName] += $paTotal;
                    $objPHPExcel->getActiveSheet()->setCellValue($partnerArrayMap[$sPaName] . $rowNumber, number_format($paTotal, 0));
                    $objPHPExcel->getActiveSheet()->getStyle($partnerArrayMap[$sPaName] . $rowNumber)->getFont()->setBold(true);
                }
                $rowNumber += 2;
            }
            if (!empty($items)) {
                $objPHPExcel->getActiveSheet()->setCellValue("B" . $rowNumber, "Third Party");
                $objPHPExcel->getActiveSheet()->getStyle("B" . $rowNumber)->getFont()->setBold(true);
                $rowNumber += 2;
                $totalPerUser = [];
                $totalPerUser["quantity"] = 0;
                $totalPerUser["amount"] = 0;
                $totalPerUser["tax"] = 0;
                $totalPerUser["amount_with_tax"] = 0;
                $totalPerUser["partners"] = [];
                foreach ($items as $item) {
                    $amount = $item["sub_total_after_line_disc"];
                    if (!empty($discount_percentage) && ($activateDiscount == "invoice_level_before_tax" || $activateDiscount == "both_item_before_level")) {
                        $amount -= $amount * $discount_percentage * 1 / 100;
                    }
                    $tax = $item["tax_amount"];
                    $amount_with_tax = $amount + $tax;
                    $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($amount_with_tax, 2));
                    $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, $item["itemDescription"]);
                    $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format((double) $item["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                    $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($amount, 2));
                    $totalPerUser["quantity"] += $item["quantity"];
                    $totalPerUser["amount"] += $amount;
                    $totalPerUser["tax"] += $tax;
                    $totalPerUser["amount_with_tax"] = $totalPerUser["amount"] + $totalPerUser["tax"];
                    $itemsRelatedPartners = isset($partnersByTypeId["items"][$item["id"]]) ? $partnersByTypeId["items"][$item["id"]] : [];
                    foreach ($partnerArrayMap as $pName => $excelColumn) {
                        $paCommission = 0;
                        if (!isset($itemsRelatedPartners[$pName])) {
                            $partnerCommissionVal = "";
                        } else {
                            $commission = $item["sub_total_after_line_disc"];
                            $commissionStr = "";
                            foreach ($itemsRelatedPartners[$pName] as $paData) {
                                $paCommission += $commission * $paData["commission"] / 100;
                                $commissionStr .= $paData["commission"] . "% ";
                            }
                            $partnerCommissionVal = number_format($paCommission, 0) . " (" . trim($commissionStr) . ")";
                        }
                        $objPHPExcel->getActiveSheet()->setCellValue($excelColumn . $rowNumber, $partnerCommissionVal);
                        $objPHPExcel->getActiveSheet()->getStyle($excelColumn . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                        if (!isset($totalPerUser["partners"][$pName])) {
                            $totalPerUser["partners"][$pName] = $paCommission;
                        } else {
                            $totalPerUser["partners"][$pName] += $paCommission;
                        }
                    }
                    $rowNumber++;
                }
                $objPHPExcel->getActiveSheet()->setCellValue("E" . $rowNumber, "________");
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, "________");
                foreach ($partnerArrayMap as $pa => $letter) {
                    $objPHPExcel->getActiveSheet()->setCellValue($letter . $rowNumber, "________");
                }
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $rowNumber++;
                $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Total Third Party Expenses: ");
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValueExplicit("E" . $rowNumber, number_format($totalPerUser["quantity"], 2, ".", ","), PHPExcel_Cell_DataType::TYPE_STRING);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($totalPerUser["amount"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($totalPerUser["tax"], 2));
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($totalPerUser["amount_with_tax"], 2));
                $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
                $totalSumAll["items"]["quantity"] += $totalPerUser["quantity"];
                $totalSumAll["items"]["amount"] += $totalPerUser["amount"];
                $totalSumAll["items"]["tax"] += $totalPerUser["tax"];
                $totalSumAll["items"]["amount_with_tax"] += $totalPerUser["amount_with_tax"];
                foreach ($totalPerUser["partners"] as $sPaName => $paTotal) {
                    $totalSumAll["items"]["partners"][$sPaName] += $paTotal;
                    $objPHPExcel->getActiveSheet()->setCellValue($partnerArrayMap[$sPaName] . $rowNumber, number_format($paTotal, 0));
                    $objPHPExcel->getActiveSheet()->getStyle($partnerArrayMap[$sPaName] . $rowNumber)->getFont()->setBold(true);
                }
                $rowNumber += 2;
                $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, "________");
                foreach ($partnerArrayMap as $pa => $letter) {
                    $objPHPExcel->getActiveSheet()->setCellValue($letter . $rowNumber, "________");
                }
                $rowNumber++;
            }
            $totalAmount = $totalSumAll["timeLogs"]["amount"] + $totalSumAll["expenses"]["amount"] + $totalSumAll["items"]["amount"];
            $totalTax = $totalSumAll["timeLogs"]["tax"] + $totalSumAll["expenses"]["tax"] + $totalSumAll["items"]["tax"];
            $totalAmountWithTax = $totalAmount + $totalTax;
            if (!empty($discount_percentage) && ($activateDiscount == "invoice_level_after_tax" || $activateDiscount == "both_item_after_level")) {
                $invDiscountAmount = $totalAmountWithTax * $discount_percentage * 1 / 100;
                $totalAmountWithTax = $totalAmountWithTax - $invDiscountAmount;
                $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Discount: ");
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getFont()->setBold(true);
                $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($invDiscountAmount, 2));
                $rowNumber++;
            }
            $objPHPExcel->getActiveSheet()->setCellValue("D" . $rowNumber, "Grand Total: ");
            $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $objPHPExcel->getActiveSheet()->getStyle("D" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue("F" . $rowNumber, $invoice["clientAccountCurrency"]);
            $objPHPExcel->getActiveSheet()->getStyle("F" . $rowNumber)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $objPHPExcel->getActiveSheet()->getStyle("F" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->setCellValue("G" . $rowNumber, number_format($totalAmount, 2));
            $objPHPExcel->getActiveSheet()->setCellValue("H" . $rowNumber, number_format($totalTax, 2));
            $objPHPExcel->getActiveSheet()->setCellValue("I" . $rowNumber, number_format($totalAmountWithTax, 2));
            $objPHPExcel->getActiveSheet()->getStyle("H" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("I" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("E" . $rowNumber)->getFont()->setBold(true);
            $objPHPExcel->getActiveSheet()->getStyle("G" . $rowNumber)->getFont()->setBold(true);
            $controlSum = 0;
            foreach ($totalSumAll["timeLogs"]["partners"] as $sPaName => $paTotal) {
                $curPartnerVal = 0;
                foreach ($totalSumAll as $categ => $categVals) {
                    $curPartnerVal += $categVals["partners"][$sPaName];
                }
                $objPHPExcel->getActiveSheet()->setCellValue($partnerArrayMap[$sPaName] . $rowNumber, number_format($curPartnerVal, 0));
                $objPHPExcel->getActiveSheet()->getStyle($partnerArrayMap[$sPaName] . $rowNumber)->getFont()->setBold(true);
                $controlSum += $curPartnerVal;
            }
            $controlSumLabel = ++$lastSheetLetter;
            $controlSumValue = ++$lastSheetLetter;
            $objPHPExcel->getActiveSheet()->setCellValue($controlSumLabel . $rowNumber, "Sum:");
            $objPHPExcel->getActiveSheet()->setCellValue($controlSumValue . $rowNumber, number_format($controlSum, 0));
            $objPHPExcel->getActiveSheet()->getStyle($controlSumLabel . $rowNumber)->applyFromArray(["font" => ["bold" => true, "italic" => true]]);
            $objPHPExcel->getActiveSheet()->getColumnDimension("D")->setWidth(100);
            $objPHPExcel->getActiveSheet()->getStyle("A1:C5")->applyFromArray(["font" => ["bold" => true]]);
            $objPHPExcel->getActiveSheet()->getStyle("C2")->getFont()->setSize(16);
            $objPHPExcel->getActiveSheet()->getStyle("C6")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            for ($col = "A"; $col !== "AA"; $col++) {
                $objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
            }
            $objPHPExcel->getActiveSheet()->getStyle("A9:I11")->applyFromArray(["font" => ["bold" => true]]);
            $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setAutoSize(false);
            $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setAutoSize(false);
            $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setAutoSize(false);
            $objPHPExcel->getActiveSheet()->getColumnDimension("G")->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension("H")->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension("I")->setWidth(15);
            $objPHPExcel->getActiveSheet()->getStyle("A4:" . $lastSheetLetter . $rowNumber)->getFont()->setName("Arial");
            $objPHPExcel->getActiveSheet()->getStyle("A4:" . $lastSheetLetter . $rowNumber)->getFont()->setSize(9);
            $objPHPExcel->getActiveSheet()->getStyle("G12:" . $lastSheetLetter . $rowNumber)->applyFromArray($alignRight);
            $objPHPExcel->getActiveSheet()->getStyle("C7:C" . $rowNumber)->applyFromArray($alignRight);
            $objPHPExcel->getActiveSheet()->getStyle("E6:" . $lastSheetLetter . "7")->applyFromArray($alignCenter);
            $objPHPExcel->getActiveSheet()->getStyle("C6")->applyFromArray($alignLeft);
            $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, "Excel2007");
            $corepath = substr(COREPATH, 0, -12);
            $file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "test.xlsx";
            $objWriter->save($file);
            $this->load->helper("download");
            $content = file_get_contents($file);
            unlink($file);
            $fileName = $this->invoice_header->get_field("prefix") . $invoice["refNum"] . $this->invoice_header->get_field("suffix") . "_" . date("YmdHi") . ".xlsx";
            force_download($fileName, $content);
            exit;
        }
    }
    private function check_case_client_match_expense_client($data)
    {
        if ($data["expense"]["case_id"] && $data["expense"]["client_id"] && $data["case_client"]) {
            if (in_array($data["expense"]["billingStatus"], ["not-set", "to-invoice", "non-billable"]) && $data["case_client"]["client_id"] != $data["expense"]["client_id"]) {
                return true;
            }
        } else {
            return false;
        }
    }
    private function check_case_client_match_bill_client($data)
    {
        if ($data["bill"]["case_id"] && $data["bill"]["client_id"] && $data["case_client"]) {
            if ($data["case_client"]["client_id"] != $data["bill"]["client_id"]) {
                return true;
            }
        } else {
            return false;
        }
    }
    public function hearing_expenses_add($case_id, $id, $bulk = false)
    {
        if (0 < $case_id && $id) {
            $data = $this->return_case_client_details($case_id);
            $this->load->model("legal_case_hearing", "legal_case_hearingfactory");
            $this->legal_case_hearing = $this->legal_case_hearingfactory->get_instance();
            $this->legal_case_hearing->fetch($id);
            if ($case_id != $this->legal_case_hearing->get_field("legal_case_id")) {
                redirect(app_url("dashboard"));
            }
            $data["hearing"] = ["hearingID" => $this->legal_case_hearing->get("modelCode") . $id, "id" => $id, "subject" => $this->legal_case_hearing->get_field("startDate") . " " . $this->legal_case_hearing->get_field("startTime")];
            if ($bulk ? $this->expenses_add_bulk($data) : $this->expense_save(0, $data)) {
                redirect(app_url("cases/expenses/" . $case_id));
            }
        } else {
            redirect(app_url("dashboard"));
        }
    }
    public function task_expenses_add($case_id, $id, $bulk = false)
    {
        if (0 < $case_id && $id) {
            $data = $this->return_case_client_details($case_id);
            $this->load->model("task", "taskfactory");
            $this->task = $this->taskfactory->get_instance();
            $this->task->fetch($id);
            if ($case_id != $this->task->get_field("legal_case_id")) {
                redirect(app_url("dashboard"));
            }
            $data["task"] = ["id" => $id, "title" => $this->task->get_field("title")];
            if ($bulk ? $this->expenses_add_bulk($data) : $this->expense_save(0, $data)) {
                if ($data["case_category"] != "IP") {
                    redirect(app_url("cases/expenses/" . $case_id));
                }
                redirect(app_url("intellectual_properties/expenses/" . $case_id));
            }
        } else {
            redirect(app_url("dashboard"));
        }
    }
    public function event_expenses_add($case_id, $id, $bulk = false)
    {
        if (0 < $case_id && $id) {
            $data = $this->return_case_client_details($case_id);
            $this->load->model("legal_case_event", "legal_case_eventfactory");
            $this->legal_case_event = $this->legal_case_eventfactory->get_instance();
            $this->legal_case_event->fetch($id);
            if (intval($case_id) !== intval($this->legal_case_event->get_field("legal_case"))) {
                redirect(app_url("dashboard"));
            }
            $data["event"] = ["id" => $id, "subject" => $this->legal_case_event->return_event_subject($id)];
            if ($bulk ? $this->expenses_add_bulk($data) : $this->expense_save(0, $data)) {
                redirect(app_url("cases/expenses/" . $case_id));
            }
        } else {
            redirect(app_url("dashboard"));
        }
    }
    private function return_case_client_details($case_id)
    {
        $this->load->model("client");
        $data = $this->client->load_case_client_details($case_id);
        if (!$data) {
            $this->load->model("legal_case", "legal_casefactory");
            $this->legal_case = $this->legal_casefactory->get_instance();
            $this->legal_case->fetch($case_id);
            $data = ["caseId" => $case_id, "caseSubject" => $this->legal_case->get_field("subject"), "clientId" => "", "clientName" => "", "case_category" => $this->legal_case->get_field("category")];
        }
        return $data;
    }
    private function return_notify_before_data($id = NULL, $table = "")
    {
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $system_preferences = $this->session->userdata("systemPreferences");
        $data["default_interval_date"] = $system_preferences["reminderIntervalDate"];
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
        $data["notify_before"] = $id && $table ? $this->reminder->load_notify_before_data_to_related_object($id, $table) : false;
        return $data;
    }
    private function notify_me_before_due_date($related_id, $reminder)
    {
        $notify_before = $this->input->post("notify_me_before");
        if (empty($this->reminder)) {
            $this->load->model("reminder", "reminderfactory");
            $this->reminder = $this->reminderfactory->get_instance();
        }
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($related_id, $reminder["related_object"]);
        if ($current_reminder && !$notify_before) {
            $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if ($notify_before && $reminder["remindDate"]) {
            $reminder = array_merge($reminder, ["user_id" => $this->is_auth->get_user_id(), "related_id" => $related_id, "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => $notify_before["type"]]);
            return $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        return false;
    }
    public function relate_matters_to_invoice()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("legal_case_commission");
        $this->load->model("legal_case_partner_share");
        $response = [];
        if (!$this->input->post(NULL)) {
            $return = $this->input->get("return");
            $client_id = $this->input->get("client_id");
            $this->load->helper("text");
            switch ($return) {
                case "cases":
                    $data["cases"] = $this->legal_case->load_cases_by_client_id($client_id);
                    $data["title"] = $this->lang->line("related_case");
                    $data["button_action"] = $this->lang->line("next");
                    $response["related_cases"] = $data["cases"];
                    $response["html"] = $this->load->view("invoices/matter_expenses_time_logs/related_matter", $data, true);
                    break;
                case "expenses_and_time_logs":
                    $cases = $this->input->get("cases");
                    $data["expenses"] = $this->legal_case->k_load_all_legal_case_expenses_per_client($cases, $client_id, $this->input->get("client_account_id"));
                    $response["expenses"]["data"] = $data["expenses"];
                    if ($data["expenses"]) {
                        $response["expenses"]["title"] = $this->lang->line("select_related_expense_to_item");
                        $response["expenses"]["html"] = $this->load->view("invoices/matter_expenses_time_logs/invoice_case_expenses", $data, true);
                    }
                    $money_preference = $this->money_preference->get_value_by_key("userRatePerHour");
                    $organization_id = $this->session->userdata("organizationID");
                    $user_rate_per_hour = "";
                    if (isset($money_preference["keyValue"])) {
                        $user_rate_per_hour = unserialize($money_preference["keyValue"]);
                        $user_rate_per_hour = isset($user_rate_per_hour[$organization_id]) ? $user_rate_per_hour[$organization_id] : false;
                    }
                    $this->load->model("user_activity_log", "user_activity_logfactory");
                    $this->user_activity_log = $this->user_activity_logfactory->get_instance();
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $this->load->model("discount", "discountfactory");
                    $this->discount = $this->discountfactory->get_instance();
                    $activate_tax = $this->money_preference->get_key_groups();
                    $data_3["activate_tax"] = $activate_tax["ActivateTaxesinInvoices"]["TEnabled"];
                    $data_3["activate_discount"] = $activate_tax["ActivateDiscountinInvoices"]["DEnabled"];
                    $data["time_logs"] = $this->user_activity_log->load_case_related_user_activity_logs($cases, $user_rate_per_hour);
                    $data["operators_date"] = $this->get_filter_operators("date");
                    $data_3["taxes"] = ["" => $this->lang->line("none")] + $this->tax->load_list_with_percentage();
                    $data_3["discounts"] = ["" => $this->lang->line("none")] + $this->discount->get_dropdown_discount_list();
                    $response["time_logs"]["data"] = $data["time_logs"];
                    if ($data["time_logs"]) {
                        $response["time_logs"]["title"] = $this->lang->line("select_related_time_logs_to_item");
                        $response["time_logs"]["html"] = $this->load->view("invoices/matter_expenses_time_logs/filter_invoice_case_time_logs", $data, true) . $this->load->view("invoices/matter_expenses_time_logs/invoice_case_time_logs", $data, true) . $this->load->view("invoices/matter_expenses_time_logs/time_logs_tax_discount", $data_3, true);
                    }
                    $response["caseCommissions"] = $this->legal_case_commission->fetch_commissions($cases);
                    $response["casesPartnerShares"] = $this->legal_case_partner_share->load_partners_shares_by_cases($cases);
                    break;
                case "chosen_cases":
                    $data["title"] = $this->lang->line("selected_cases");
                    $data["button_action"] = $this->lang->line("ok");
                    $all_cases = $this->legal_case->load_cases_by_client_id($client_id);
                    if ($voucher_header_id = $this->input->get("voucher_header_id")) {
                        $this->load->model("invoice_detail", "invoice_detailfactory");
                        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                        $cases = $this->invoice_detail->load_invoice_related_cases($voucher_header_id);
                        if (!empty($cases["case_id"])) {
                            $cases["case_id"] = explode(",", $cases["case_id"]);
                            if ($cases["paidStatus"] === "draft") {
                                $data["cases"] = $all_cases;
                                $data["button_action"] = $this->lang->line("next");
                                $all_case_id = array_column($data["cases"], "id");
                                foreach ($cases["case_id"] as $key => $case_id) {
                                    if (in_array($case_id, $all_case_id)) {
                                        $index = array_keys($all_case_id, $case_id);
                                        $data["cases"][$index[0]]["checked"] = true;
                                    }
                                }
                                $response["related_cases"] = $all_cases;
                            } else {
                                $case_subjects = explode(":/;", $cases["caseSubject"]);
                                $data["chosen_cases"] = [];
                                foreach ($cases["case_id"] as $key => $case_id) {
                                    $data["chosen_cases"][]["caseId"] = $this->legal_case->get("modelCode") . $case_id . " - " . $case_subjects[$key];
                                }
                            }
                        } else {
                            $response["related_cases"] = [];
                            if ($cases["paidStatus"] === "draft") {
                                $data["title"] = $this->lang->line("related_case");
                                $data["cases"] = $all_cases;
                                $data["button_action"] = $this->lang->line("next");
                                $response["related_cases"] = $all_cases;
                            }
                        }
                    } else {
                        $chosen_cases = array_column(array_filter($this->input->get("chosen_cases")), "id");
                        foreach ($all_cases as $key => $case) {
                            if (in_array($case["id"], $chosen_cases)) {
                                $all_cases[$key]["checked"] = true;
                            }
                        }
                        $data["cases"] = $all_cases;
                        $data["button_action"] = $this->lang->line("next");
                        $response["related_cases"] = $all_cases;
                    }
                    $response["html"] = $this->load->view("invoices/matter_expenses_time_logs/related_matter", $data, true);
                    break;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function return_current_columns($model)
    {
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $user_default_filter = $this->grid_saved_filter->getDefaultFilter($model, $this->session->userdata("AUTH_user_id"));
        $models_columns = $this->grid_saved_column->get("models_columns");
        $grid_details = $this->grid_saved_column->get_user_grid_details($model, $user_default_filter["id"]);
        $response["columns_to_select"] = $grid_details["selected_columns"];
        $response["export_selected_columns"] = true;
        $all_columns = $models_columns[$model]["all_columns"];
        foreach ($grid_details["selected_columns"] as $key) {
            $response["selected_columns"][$key] = $all_columns[$key];
        }
        return $response;
    }
    private function insert_inv_pay_voucher_details($data = [], $currencies, $cr_amount = NULL, $dbt_amount = NULL)
    {
        $dbt = $dbt_amount ?? $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]];
        $is_debit_note = $data["invoice_data"]["voucherType"] == "DBN";
        $inv_description = ($is_debit_note ? "DBN-PY " : "INV-PY ") . $data["invoice_data"]["prefix"] . $data["invoice_data"]["refNum"] . $data["invoice_data"]["suffix"];
        $result = $this->insert_voucher_details(["description" => $inv_description, "account_id" => $this->input->post("account_id"), "drCr" => "D", "local_amount" => $dbt, "foreign_amount" => $dbt / $data["rates"][$currencies["deposit_to"]]]);
        if ($result) {
            if ($this->input->post("other_amount")) {
                $other_local_amount = $this->input->post("other_amount") * $data["rates"][$currencies["other_deposit_to"]];
                $result = $this->insert_voucher_details(["description" => $inv_description, "account_id" => $this->input->post("other_account_id"), "drCr" => "D", "local_amount" => $other_local_amount, "foreign_amount" => $this->input->post("other_amount")]);
            }
            if ($this->input->post("paymentMethod") == "Trust Account" || $this->input->post("other_payment_method") == "Trust Account") {
                $credit_acc = ["account_id" => $this->input->post("trust_asset_account"), "drCr" => "C", "description" => $inv_description];
                $debit_acc = ["account_id" => $this->input->post("trust_liability_account"), "drCr" => "D", "description" => $inv_description];
                if ($this->input->post("paymentMethod") === "Trust Account") {
                    $credit_acc = $credit_acc + ["local_amount" => $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]], "foreign_amount" => $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]]];
                    $debit_acc = $debit_acc + ["local_amount" => $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]], "foreign_amount" => $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]]];
                    if ($this->insert_voucher_details($credit_acc)) {
                        $this->insert_voucher_details($debit_acc);
                    }
                } else {
                    $credit_acc = $credit_acc + ["local_amount" => $other_local_amount, "foreign_amount" => $other_local_amount];
                    $debit_acc = $debit_acc + ["local_amount" => $other_local_amount, "foreign_amount" => $other_local_amount];
                    if ($this->insert_voucher_details($credit_acc)) {
                        $this->insert_voucher_details($debit_acc);
                    }
                }
            }
            $amount = $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]];
            $other_amount = !$this->input->post("other_amount") ? 0 : $this->input->post("other_amount") * $data["rates"][$currencies["other_deposit_to"]];
            $cr = $cr_amount ?? $amount;
            $total_amount = $cr + $other_amount;
            return $this->insert_voucher_details(["description" => $inv_description, "account_id" => $this->input->post("clientAccountId"), "drCr" => "C", "local_amount" => $total_amount, "foreign_amount" => ($amount + $other_amount) / $data["rates"][$this->input->post("clientCurrencyId")]]);
        }
        return false;
    }
    private function insert_voucher_details($details)
    {
        $voucher_header_id = $this->voucher_header->get_field("id");
        $this->voucher_detail->reset_fields();
        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
        $this->voucher_detail->set_fields($details);
        return $this->voucher_detail->insert();
    }
    private function insert_inv_pay_data($data = [], $currencies, $action = "insert")
    {
        $amount = $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]] / $data["rates"][$this->input->post("clientCurrencyId")];
        $voucher_header_id = $this->voucher_header->get_field("id");
        $this->invoice_payment->set_field("voucher_header_id", $voucher_header_id);
        $this->invoice_payment->set_field("account_id", $this->input->post("account_id"));
        $this->invoice_payment->set_field("paymentMethod", $this->input->post("paymentMethod"));
        $this->invoice_payment->set_field("total", $this->input->post("amount"));
        $this->invoice_payment->set_field("client_account_id", $data["invoice_data"]["clientAccountId"]);
        $this->invoice_payment->set_field("invoicePaymentTotal", $this->input->post("amount") * $data["rates"][$currencies["deposit_to"]] / $data["rates"][$this->input->post("clientCurrencyId")]);
        $this->invoice_payment->set_field("exchangeRate", $data["rates"][$currencies["deposit_to"]]);
        if ($action == "update" ? $this->invoice_payment->update() : $this->invoice_payment->insert()) {
            $invoice_payment_id = $this->invoice_payment->get_field("id");
            $this->invoice_payment_invoice->reset_fields();
            $this->invoice_payment_invoice->set_field("invoice_payment_id", $invoice_payment_id);
            $this->invoice_payment_invoice->set_field("invoice_header_id", $this->input->post("invoice_id"));
            $this->invoice_payment_invoice->set_field("amount", number_format($amount, 2, NULL, ""));
            if ($this->invoice_payment_invoice->insert()) {
                if ($this->input->post("other_amount")) {
                    $this->invoice_payment->reset_fields();
                    $this->invoice_payment->set_field("voucher_header_id", $voucher_header_id);
                    $this->invoice_payment->set_field("account_id", $this->input->post("other_account_id"));
                    $this->invoice_payment->set_field("paymentMethod", $this->input->post("other_payment_method"));
                    $this->invoice_payment->set_field("total", $this->input->post("other_amount"));
                    $this->invoice_payment->set_field("client_account_id", $data["invoice_data"]["clientAccountId"]);
                    $this->invoice_payment->set_field("invoicePaymentTotal", $this->input->post("other_amount") * $data["rates"][$currencies["other_deposit_to"]] / $data["rates"][$this->input->post("clientCurrencyId")]);
                    $this->invoice_payment->set_field("exchangeRate", $data["rates"][$currencies["deposit_to"]]);
                    if ($this->invoice_payment->insert()) {
                        $invoice_payment_id = $this->invoice_payment->get_field("id");
                        $this->invoice_payment_invoice->reset_fields();
                        $this->invoice_payment_invoice->set_field("invoice_payment_id", $invoice_payment_id);
                        $this->invoice_payment_invoice->set_field("invoice_header_id", $this->input->post("invoice_id"));
                        $this->invoice_payment_invoice->set_field("amount", $this->input->post("other_amount") * $data["rates"][$currencies["other_deposit_to"]] / $data["rates"][$this->input->post("clientCurrencyId")]);
                        if ($this->invoice_payment_invoice->insert()) {
                            return true;
                        }
                        $this->invoice_payment->delete($invoice_payment_id);
                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                        $this->voucher_header->delete($voucher_header_id);
                        $result = false;
                    } else {
                        $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                        $this->voucher_header->delete($voucher_header_id);
                        $result = false;
                    }
                }
                return true;
            }
            $this->invoice_payment->delete($invoice_payment_id);
            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
            $this->voucher_header->delete($voucher_header_id);
            $result = false;
        } else {
            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
            $this->voucher_header->delete($voucher_header_id);
            $result = false;
        }
        return $result;
    }
    private function delete_invoice_payment_invoice($payment_id = 0)
    {
        if (0 < $payment_id) {
            $this->invoice_payment_invoice->reset_fields();
            $this->invoice_payment_invoice->fetch(["invoice_payment_id" => $payment_id]);
            $amount = $this->invoice_payment_invoice->get_field("amount");
            $this->invoice_payment_invoice->delete(["where" => ["invoice_payment_id", $payment_id]]);
            return $amount;
        }
    }
    public function delete_bill($voucher_header_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            if (0 < $voucher_header_id) {
                $this->db->trans_start();
                $this->load->model(["bill_payment", "bill_payment_bill"]);
                $this->load->model("bill_header", "bill_headerfactory");
                $this->load->model("bill_details", "bill_detailsfactory");
                $this->load->model("voucher_header", "voucher_headerfactory");
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $this->bill_header = $this->bill_headerfactory->get_instance();
                $this->bill_details = $this->bill_detailsfactory->get_instance();
                $this->voucher_header = $this->voucher_headerfactory->get_instance();
                $this->bill_header->fetch(["voucher_header_id" => $voucher_header_id]);
                $bill_id = $this->bill_header->get_field("id");
                $paid_status = $this->bill_header->get_field("status");
                if (!in_array($paid_status, ["partially paid", "paid"])) {
                    $this->bill_payment_bill->fetch(["bill_header_id" => $bill_id]);
                    $payment_id = $this->bill_payment_bill->get_field("bill_payment_id");
                    if ($payment_id) {
                        $this->bill_payment->fetch(["id" => $payment_id]);
                        $voucher_payment_id = $this->bill_payment->get_field("voucher_header_id");
                        if ($this->bill_payment_bill->delete(["where" => ["bill_payment_id", $payment_id]])) {
                            $this->document_management_system->delete(["where" => [["module_record_id", $payment_id], ["module", "BI-PY"]]]);
                            $result = $this->bill_payment->delete($payment_id);
                            if ($result && $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_payment_id]])) {
                                $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                $this->voucher_header->delete(["where" => ["id", $voucher_payment_id]]);
                            }
                        }
                    }
                    if ($this->bill_details->delete(["where" => ["bill_header_id", $bill_id]])) {
                        $this->document_management_system->delete(["where" => [["module_record_id", $voucher_header_id], ["module", "BI"]]]);
                        if ($this->bill_header->delete(["where" => ["voucher_header_id", $voucher_header_id]]) && $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_header->delete(["where" => ["id", $voucher_header_id]]);
                        }
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status()) {
                        $response["status"] = true;
                    } else {
                        $response["status"] = false;
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function delete_invoice($voucher_header_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            if (0 < $voucher_header_id) {
                $this->db->trans_start();
                $this->load->model(["invoice_payment", "invoice_payment_invoice"]);
                $this->load->model("invoice_header", "invoice_headerfactory");
                $this->load->model("invoice_detail", "invoice_detailfactory");
                $this->load->model("voucher_header", "voucher_headerfactory");
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->load->model("item_commission");
                $this->load->model("settlement_invoice");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $this->invoice_header = $this->invoice_headerfactory->get_instance();
                $this->invoice_detail = $this->invoice_detailfactory->get_instance();
                $this->voucher_header = $this->voucher_headerfactory->get_instance();
                $this->invoice_header->fetch(["voucher_header_id" => $voucher_header_id]);
                $invoice_id = $this->invoice_header->get_field("id");
                $paid_status = $this->invoice_header->get_field("paidStatus");
                $invoice_data = $this->voucher_header->load_invoice_voucher($voucher_header_id);
                $is_debit_note = $invoice_data["voucherType"] == "DBN";
                $invoice_settlements = $this->settlement_invoice->get_invoice_settlements($invoice_id);
                if (0 < count($invoice_settlements)) {
                    $response["status"] = false;
                    $this->db->trans_complete();
                } else {
                    if (!in_array($paid_status, ["partially paid", "paid"])) {
                        $this->invoice_payment_invoice->fetch(["invoice_header_id" => $invoice_id]);
                        $payment_id = $this->invoice_payment_invoice->get_field("invoice_payment_id");
                        if ($payment_id) {
                            $this->invoice_payment->fetch(["id" => $payment_id]);
                            $voucher_payment_id = $this->invoice_payment->get_field("voucher_header_id");
                            if ($this->invoice_payment_invoice->delete(["where" => ["invoice_payment_id", $payment_id]])) {
                                $this->document_management_system->delete(["where" => [["module_record_id", $payment_id], ["module", $is_debit_note ? "DBN-PY" : "INV-PY"]]]);
                                $result = $this->invoice_payment->delete($payment_id);
                                if ($result && $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_payment_id]])) {
                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_header->delete(["where" => ["id", $voucher_payment_id]]);
                                }
                            }
                        }
                        $this->delete_timelog_invoice_status($invoice_id);
                        $this->item_commission->delete(["where" => ["invoice_header_id", $invoice_id]]);
                        if ($this->invoice_detail->delete_invoice_detail_items($invoice_id)) {
                            $this->document_management_system->delete(["where" => [["module_record_id", $voucher_header_id], ["module", $is_debit_note ? "DBN" : "INV"]]]);
                            if ($this->invoice_header->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                                $this->load->model("quote_header", "quote_headerfactory");
                                $this->quote_header = $this->quote_headerfactory->get_instance();
                                if ($this->quote_header->fetch(["related_invoice_id" => $voucher_header_id])) {
                                    $this->quote_header->set_field("related_invoice_id", NULL);
                                    $this->quote_header->set_field("paidStatus", "approved");
                                    $this->quote_header->update();
                                }
                                if ($this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                                    $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                    $this->voucher_header->delete(["where" => ["id", $voucher_header_id]]);
                                }
                            }
                        }
                        $this->db->trans_complete();
                        if ($this->db->trans_status()) {
                            $response["status"] = true;
                        } else {
                            $response["status"] = false;
                        }
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function get_expenses_need_approved()
    {
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("accounts");
            }
            $response = [];
            if ($this->input->post("accountID")) {
                $this->load->model("expense", "expensefactory");
                $this->expense = $this->expensefactory->get_instance();
                $amount = $this->expense->get_not_approved_expenses_amount($this->input->post("accountID"));
                $response["data"]["amount"] = number_format($amount["notApprovedBalance"], 2, NULL, "");
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    private function _get_quote_tabs_view_vars(&$id, $active = "")
    {
        $data["subNavItems"] = [];
        $data["activeSubNavItem"] = $active;
        $data["id"] = $id;
        if ($id) {
            $data["subNavItems"][site_url("vouchers/quote_edit/")] = $this->lang->line("quote");
            $data["subNavItems"][site_url("vouchers/quote_related_documents/")] = $this->lang->line("related_documents");
            return $data;
        }
        $data["subNavItems"][site_url("vouchers/quote_add")] = "quote";
        return $data;
    }
    public function quote_related_documents($id = "")
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/quotes_list");
        }
        $this->related_documents($id, "QOT", "quote");
    }
    public function quote_load_documents()
    {
        $this->load_documents();
    }
    public function quote_upload_file()
    {
        $this->upload_file();
    }
    public function quote_rename_file()
    {
        $this->rename_file("QOT");
    }
    public function quote_download_file($file_id)
    {
        $this->download_file("QOT", $file_id);
    }
    public function quote_delete_document()
    {
        $this->delete_document("QOT");
    }
    public function quotes_list($clientId = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("quotes") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $data["model"] = "Quote_Header";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($data["model"]));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"];
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]);
                }
            }
            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            $response = array_merge($response, $this->voucher_header->k_load_all_quotes($filter, $sortable));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            $data["partnersCommissions"] = $this->is_commissions_enabled() ? "yes" : "no";
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["operatorsDate"] = $this->get_filter_operators("date");
            $data["operatorsGroupList"] = $this->get_filter_operators("groupList");
            $this->load->model("quote_header", "quote_headerfactory");
            $this->quote_header = $this->quote_headerfactory->get_instance();
            $data["paidStatus"] = $this->quote_header->get("paidStatusValues");
            array_unshift($data["paidStatus"], "", "overdue");
            unset($data["paidStatus"][0]);
            $data["paidStatus"] = array_combine($data["paidStatus"], ["", $this->lang->line("open"), $this->lang->line("rejected"), $this->lang->line("invoiced"), $this->lang->line("cancelled"), $this->lang->line("approved")]);
            if (0 < $clientId) {
                $this->load->model("client");
                $clientData = $this->client->fetch_client($clientId);
                $data["clientNameFilter"] = $clientData["clientName"];
            } else {
                $data["clientNameFilter"] = "";
            }
            $data["client_account"] = $this->fetch_clinet_account($data, "accountID");
            $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($data["model"], $this->session->userdata("AUTH_user_id"));
            $data["gridSavedFiltersData"] = false;
            $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($data["model"], $this->session->userdata("AUTH_user_id"));
            if ($data["gridDefaultFilter"]) {
                $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
                $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            }
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("money/js/quotes", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("quotes/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function quote_add()
    {
        $this->quote_save(0);
    }
    public function quote_edit($id = 0)
    {
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/quotes_list");
        }
        $this->quote_save($id);
    }
    private function quote_save($id = 0, $tax = "", $discount = "", $date_item = "")
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("quote") . " | " . $this->lang->line("money"));
        $this->load->model("quote_detail", "quote_detailfactory");
        $this->quote_detail = $this->quote_detailfactory->get_instance();
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("quote_time_logs_item");
        $this->load->helper(["text"]);
        $this->includes("jquery/tinymce/tinymce.min", "js");
        $this->includes("jquery/jquery.dirtyform", "js");
        $data = [];
        $data["quote"] = ["id" => "", "organization_id" => "", "voucher_header_id" => "", "suffix" => "", "clientAccountId" => "", "clientAccountName" => "", "clientAccountCurrency" => "", "referenceNum" => "", "dated" => "", "description" => "", "total" => 0, "term_id" => "", "status" => "", "dueOn" => "", "purchaseOrder" => "", "notes" => "", "billTo" => "", "groupTimeLogsByUserInExport" => "", "quoteNumber" => ""];
        $this->load->model(["money_preference"]);
        $money_preference = $this->money_preference->get_key_groups();
        $organization_id = $this->session->userdata("organizationID");
        if ($tax != "") {
            $data["activateTax"] = $tax;
        } else {
            $data["activateTax"] = $money_preference["ActivateTaxesinInvoices"]["TEnabled"];
        }
        $discount_account_config = unserialize($money_preference["ActivateDiscountinInvoices"]["DEnabled"])[$organization_id];
        if ($discount != "") {
            if ($discount == "1" && empty($discount_account_config["account_id"])) {
                redirect("setup/configure_invoice_discount");
            }
            $data["activateDiscount"] = $discount;
        } else {
            if ($discount_account_config["enabled"] == "no") {
                $data["activateDiscount"] = "0";
            } else {
                if (empty($discount_account_config["account_id"])) {
                    redirect("setup/configure_invoice_discount");
                }
                $data["activateDiscount"] = "1";
            }
        }
        $data["display_item_date"] = $date_item != "" ? $date_item : $money_preference["InvoiceItems"]["DisplayItemDate"];
        $data["quote_details"] = [];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $data["quoteNumberPrefix"] = "";
        $data["autoGeneratequoteNumber"] = $this->quote_header->auto_generate_rf();
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (!empty($exchange_rates)) {
            $data["rates"] = $exchange_rates;
            $expenses_ids = [];
            $logs_ids = [];
            if ($this->input->post(NULL)) {
                if ($this->license_availability === false) {
                    $this->set_flashmessage("error", $this->licensor->get_license_message());
                    redirect("vouchers/quote_edit/" . $id);
                }
                $result = false;
                $this->validate_current_organization($this->input->post("organization_id"), "quotes_list");
                $_POST["prefix"] = $data["quoteNumberPrefix"];
                $_POST["dueOn"] = date("Y-m-d H:i", strtotime($this->input->post("dueOn")));
                $_POST["dated"] = date("Y-m-d", strtotime($this->input->post("quoteDate")));
                if ($id != 0) {
                    $this->quote_header->fetch($this->input->post("id"));
                    if ($this->quote_header->get_field("voucher_header_id") != $id) {
                        redirect("vouchers/quotes_list");
                    } else {
                        $this->quote_detail->delete_quote_detail_items($this->quote_header->get_field("id"));
                        $this->voucher_related_case->delete(["where" => ["voucher_header_id", $id]]);
                        $this->voucher_header->fetch($id);
                        $_POST["voucher_header_id"] = $id;
                        if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                            $upload_response = $this->dms->upload_file(["module" => "QOT", "module_record_id" => $id, "upload_key" => "uploadDoc"]);
                        }
                        if (!$this->input->post("refNum")) {
                            $_POST["refNum"] = $this->voucher_header->get_field("refNum");
                        }
                        $this->voucher_header->set_field("refNum", $this->input->post("refNum"));
                        $this->voucher_header->set_field("referenceNum", $this->input->post("quoteNumber"));
                        $this->voucher_header->set_field("dated", $this->input->post("dated"));
                        if ($this->voucher_header->update()) {
                            $voucher_header_id = $this->voucher_header->get_field("id");
                            if ($this->input->post("related_cases")) {
                                $related_cases = explode(",", $this->input->post("related_cases"));
                                $voucher_related_cases_array = [];
                                $related_cases_tempArr = [];
                                foreach ($related_cases as $case_id) {
                                    $related_cases_tempArr["legal_case_id"] = $case_id;
                                    $related_cases_tempArr["voucher_header_id"] = $voucher_header_id;
                                    array_push($voucher_related_cases_array, $related_cases_tempArr);
                                }
                                if (!empty($voucher_related_cases_array) && !$this->voucher_related_case->insert_batch($voucher_related_cases_array)) {
                                    $this->invoice_detail->delete(["where" => ["invoice_header_id", $invoice_header_id]]);
                                    $this->invoice_header->delete($invoice_header_id);
                                    $this->voucher_header->delete($voucher_header_id);
                                }
                            }
                            $voucher_header_id = $this->voucher_header->get_field("id");
                            $_POST["paidStatus"] = $this->quote_header->get_field("paidStatus");
                            $_POST["prefix"] = $data["quoteNumberPrefix"];
                            $this->quote_header->set_fields($this->input->post(NULL));
                            $this->quote_header->set_field("account_id", $this->input->post("client_id"));
                            $this->quote_header->set_field("notes", $this->input->post("notes", true, true));
                            $this->quote_header->set_field("description", $this->input->post("quote_description"));
                            if ($this->quote_header->update()) {
                                $this->load->model("account", "accountfactory");
                                $this->account = $this->accountfactory->get_instance();
                                $this->account->fetch($this->input->post("client_id"));
                                $this->account->set_field("accountData", $this->input->post("billTo"));
                                $this->account->update();
                                $quote_header_id = $this->quote_header->get_field("id");
                                $this->set_quote_status($quote_header_id);
                                $time_logs_item_count = 0;
                                if ($this->input->post("itemIds")) {
                                    $time_logs_ids = array_filter($this->input->post("timeLogsIds"));
                                    foreach ($this->input->post("itemIds") as $key => $itemIds) {
                                        $this->quote_detail->reset_fields();
                                        $this->quote_detail->set_field("quote_header_id", $quote_header_id);
                                        $this->quote_detail->set_field("account_id", $this->input->post("accountIds")[$key]);
                                        if (!empty($this->input->post("subItemIds")[$key])) {
                                            $this->quote_detail->set_field("sub_item_id", $this->input->post("subItemIds")[$key]);
                                        }
                                        $this->quote_detail->set_field("item_id", empty($itemIds) ? NULL : $itemIds);
                                        $this->quote_detail->set_field("item", $this->input->post("item")[$key]);
                                        if (!empty($this->input->post("expenseIds")[$key])) {
                                            $this->quote_detail->set_field("expense_id", $this->input->post("expenseIds")[$key]);
                                        }
                                        $this->quote_detail->set_field("unitPrice", $this->input->post("unitPrice")[$key]);
                                        $this->quote_detail->set_field("quantity", $this->input->post("quantity")[$key]);
                                        $this->quote_detail->set_field("itemDescription", $this->input->post("description")[$key]);
                                        $this->quote_detail->set_field("tax_id", empty($this->input->post("taxIds")[$key]) ? NULL : $this->input->post("taxIds")[$key]);
                                        $this->quote_detail->set_field("discount_id", empty($this->input->post("discountIds")[$key]) ? NULL : $this->input->post("discountIds")[$key]);
                                        $this->quote_detail->set_field("percentage", empty($this->input->post("percentage")[$key]) ? NULL : $this->input->post("percentage")[$key]);
                                        $this->quote_detail->set_field("discountPercentage", empty($this->input->post("discountPercentage")[$key]) ? NULL : $this->input->post("discountPercentage")[$key]);
                                        $this->quote_detail->set_field("item_date", empty($this->input->post("item_date")[$key]) ? NULL : $this->input->post("item_date")[$key]);
                                        if (!empty($this->input->post("timeLogsIds")[$key])) {
                                            foreach (json_decode($this->input->post("timeLogsIds")[$key]) as $key1 => $value1) {
                                                $quantities = json_decode($this->input->post("quantities")[$key]);
                                                $this->quote_detail->set_field("id", NULL);
                                                if ($this->quote_header->get_field("groupTimeLogsByUserInExport") == "1") {
                                                    $this->quote_detail->set_field("quantity", $quantities[$key1]);
                                                } else {
                                                    $this->quote_detail->set_field("quantity", $this->input->post("quantity")[$key]);
                                                }
                                                if ($this->quote_detail->insert()) {
                                                    $this->quote_time_logs_item->set_field("id", NULL);
                                                    $this->quote_time_logs_item->set_field("item", $this->quote_detail->get_field("id"));
                                                    $this->quote_time_logs_item->set_field("time_log", $value1);
                                                    $this->quote_time_logs_item->set_field("user_id", $this->input->post("user_id")[$time_logs_item_count]);
                                                    $description_per_log = explode(";;;", $this->input->post("description_per_log")[$time_logs_item_count]);
                                                    $date_per_log = explode(",", $this->input->post("date_per_log")[$time_logs_item_count]);
                                                    $this->quote_time_logs_item->set_field("date", date("Y-m-d", strtotime($date_per_log[$key1])));
                                                    if (!empty($description_per_log)) {
                                                        $description_per_log_key1 = $description_per_log[$key1] == "null" ? NULL : $description_per_log[$key1];
                                                        $this->quote_time_logs_item->set_field("description", $description_per_log_key1);
                                                    }
                                                    $this->quote_time_logs_item->insert();
                                                    $result = true;
                                                } else {
                                                    $this->quote_detail->delete(["where" => ["quote_header_id", $quote_header_id]]);
                                                    $result = false;
                                                    $time_logs_item_count++;
                                                }
                                            }
                                        } else {
                                            $this->quote_detail->set_field("id", NULL);
                                            if ($this->quote_detail->insert()) {
                                                $result = true;
                                            } else {
                                                $this->quote_detail->delete(["where" => ["quote_header_id", $quote_header_id]]);
                                                $result = false;
                                            }
                                        }
                                    }
                                }
                            } else {
                                $result = false;
                            }
                        }
                    }
                } else {
                    if (!$this->input->post("refNum")) {
                        $_POST["refNum"] = $this->quote_header->auto_generate_rf();
                    }
                    $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                    $this->voucher_header->set_field("refNum", $this->input->post("refNum"));
                    $this->voucher_header->set_field("referenceNum", $this->input->post("quoteNumber"));
                    $this->voucher_header->set_field("dated", $this->input->post("dated"));
                    $this->voucher_header->set_field("voucherType", "QOT");
                    $this->load->model(["money_preference"]);
                    if ($this->voucher_header->insert()) {
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        if ($this->input->post("related_cases")) {
                            $related_cases = explode(",", $this->input->post("related_cases"));
                            $voucher_related_cases_array = [];
                            $related_cases_tempArr = [];
                            foreach ($related_cases as $case_id) {
                                $related_cases_tempArr["legal_case_id"] = $case_id;
                                $related_cases_tempArr["voucher_header_id"] = $voucher_header_id;
                                array_push($voucher_related_cases_array, $related_cases_tempArr);
                            }
                            if (!empty($voucher_related_cases_array) && !$this->voucher_related_case->insert_batch($voucher_related_cases_array)) {
                                $this->invoice_detail->delete(["where" => ["invoice_header_id", $invoice_header_id]]);
                                $this->invoice_header->delete($invoice_header_id);
                                $this->voucher_header->delete($voucher_header_id);
                            }
                        }
                        $voucher_header_id = $this->voucher_header->get_field("id");
                        $_POST["voucher_header_id"] = $voucher_header_id;
                        $_POST["prefix"] = $data["quoteNumberPrefix"];
                        $this->quote_header->set_fields($this->input->post(NULL));
                        $this->quote_header->set_field("voucher_header_id", $voucher_header_id);
                        $this->quote_header->set_field("displayTax", $data["activateTax"]);
                        $this->quote_header->set_field("displayDiscount", $data["activateDiscount"]);
                        $this->quote_header->set_field("display_item_date", $data["display_item_date"] ? 1 : 0);
                        $this->quote_header->set_field("notes", $this->input->post("notes", true, true));
                        $this->quote_header->set_field("description", $this->input->post("quote_description"));
                        if (!$this->input->post("status")) {
                            $this->quote_header->set_field("paidStatus", "open");
                        } else {
                            $this->quote_header->set_field("paidStatus", "draft");
                        }
                        $this->quote_header->set_field("account_id", $this->input->post("client_id"));
                        $this->quote_header->set_field("quoteDate", date("Y-m-d H:i", strtotime($this->input->post("quoteDate"))));
                        if ($this->quote_header->insert()) {
                            $this->load->model("account", "accountfactory");
                            $this->account = $this->accountfactory->get_instance();
                            $this->account->fetch($this->input->post("client_id"));
                            $this->account->set_field("accountData", $this->input->post("billTo"));
                            $this->account->update();
                            $quote_header_id = $this->quote_header->get_field("id");
                            $time_logs_item_count = 0;
                            $time_logs_ids = $this->input->post("timeLogsIds") ? array_filter($this->input->post("timeLogsIds")) : [];
                            foreach ($this->input->post("itemIds") as $key => $itemIds) {
                                $this->quote_detail->reset_fields();
                                $this->quote_detail->set_field("quote_header_id", $quote_header_id);
                                $this->quote_detail->set_field("account_id", $this->input->post("accountIds")[$key]);
                                if (!empty($this->input->post("subItemIds")[$key])) {
                                    $this->quote_detail->set_field("sub_item_id", $this->input->post("subItemIds")[$key]);
                                }
                                $this->quote_detail->set_field("item_id", empty($itemIds) ? NULL : $itemIds);
                                if (!empty($this->input->post("expenseIds")[$key])) {
                                    $this->quote_detail->set_field("expense_id", $this->input->post("expenseIds")[$key]);
                                    $expenses_ids[] = $this->input->post("expenseIds")[$key];
                                }
                                $this->quote_detail->set_field("item", $this->input->post("item")[$key]);
                                $this->quote_detail->set_field("unitPrice", $this->input->post("unitPrice")[$key]);
                                $this->quote_detail->set_field("quantity", $this->input->post("quantity")[$key]);
                                $this->quote_detail->set_field("itemDescription", $this->input->post("description")[$key]);
                                $this->quote_detail->set_field("tax_id", empty($this->input->post("taxIds")[$key]) ? NULL : $this->input->post("taxIds")[$key]);
                                $this->quote_detail->set_field("discount_id", empty($this->input->post("discountIds")[$key]) ? NULL : $this->input->post("discountIds")[$key]);
                                $this->quote_detail->set_field("percentage", empty($this->input->post("percentage")[$key]) ? NULL : $this->input->post("percentage")[$key]);
                                $this->quote_detail->set_field("discountPercentage", empty($this->input->post("discountPercentage")[$key]) ? NULL : $this->input->post("discountPercentage")[$key]);
                                $this->quote_detail->set_field("item_date", empty($this->input->post("item_date")[$key]) ? NULL : $this->input->post("item_date")[$key]);
                                if (!empty($this->input->post("timeLogsIds")[$key])) {
                                    foreach (json_decode($this->input->post("timeLogsIds")[$key]) as $key1 => $value1) {
                                        $quantities = json_decode($this->input->post("quantities")[$key]);
                                        $this->quote_detail->set_field("id", NULL);
                                        if ($this->quote_header->get_field("groupTimeLogsByUserInExport") == "1") {
                                            $this->quote_detail->set_field("quantity", $quantities[$key1]);
                                        } else {
                                            $this->quote_detail->set_field("quantity", $this->input->post("quantity")[$key]);
                                        }
                                        $logs_ids[] = $value1;
                                        if ($this->quote_detail->insert()) {
                                            $this->quote_time_logs_item->set_field("id", NULL);
                                            $this->quote_time_logs_item->set_field("item", $this->quote_detail->get_field("id"));
                                            $this->quote_time_logs_item->set_field("time_log", $value1);
                                            $this->quote_time_logs_item->set_field("user_id", $this->input->post("user_id")[$time_logs_item_count]);
                                            $description_per_log = explode(";;;", $this->input->post("description_per_log")[$time_logs_item_count]);
                                            $date_per_log = explode(",", $this->input->post("date_per_log")[$time_logs_item_count]);
                                            $this->quote_time_logs_item->set_field("date", date("Y-m-d", strtotime($date_per_log[$key1])));
                                            if (!empty($description_per_log)) {
                                                $description_per_log_key1 = $description_per_log[$key1] == "null" ? NULL : $description_per_log[$key1];
                                                $this->quote_time_logs_item->set_field("description", $description_per_log_key1);
                                            }
                                            $this->quote_time_logs_item->insert();
                                            $result = true;
                                        } else {
                                            $this->quote_detail->delete(["where" => ["quote_header_id", $quote_header_id]]);
                                            $this->quote_header->delete($quote_header_id);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                            $result = false;
                                            $time_logs_item_count++;
                                        }
                                    }
                                } else {
                                    $this->quote_detail->set_field("id", NULL);
                                    if ($this->quote_detail->insert()) {
                                        $result = true;
                                    } else {
                                        $this->quote_detail->delete(["where" => ["quote_header_id", $quote_header_id]]);
                                        $this->quote_header->delete($quote_header_id);
                                        $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                        $this->voucher_header->delete($voucher_header_id);
                                        $result = false;
                                        if (!$result) {
                                            $this->quote_detail->delete(["where" => ["quote_header_id", $quote_header_id]]);
                                            $this->quote_header->delete($quote_header_id);
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                                            $this->voucher_header->delete($voucher_header_id);
                                        }
                                    }
                                }
                            }
                        } else {
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $this->voucher_header->get_field("id")]]);
                            $this->voucher_header->delete($this->voucher_header->get_field("id"));
                            $result = false;
                        }
                        if (!empty($_FILES) && !empty($_FILES["uploadDoc"]["name"])) {
                            $upload_response = $this->dms->upload_file(["module" => "QOT", "module_record_id" => $voucher_header_id, "upload_key" => "uploadDoc"]);
                        }
                    }
                    if ($result && !$this->input->post("status")) {
                        $reminder = ["remindDate" => $this->input->post("quoteDate"), "related_object" => $this->quote_header->get("_table")];
                        $reminder["summary"] = sprintf($this->lang->line("quote_notification_message"), $this->lang->line("quote"), $this->input->post("prefix") . $quote_header_id, $this->input->post("quoteDate"), $this->input->post("prefix") . $quote_header_id, $this->input->post("clientName"));
                        $this->notify_me_before_due_date($voucher_header_id, $reminder);
                        $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("quote")));
                        redirect("vouchers/quote_edit/" . $this->voucher_header->get_field("id"));
                    }
                }
                if ($result) {
                    $reminder = ["remindDate" => $this->input->post("quoteDate"), "related_object" => $this->quote_header->get("_table")];
                    $reminder["summary"] = sprintf($this->lang->line("quote_notification_message"), $this->lang->line("quote"), $this->input->post("prefix") . $quote_header_id, $this->input->post("quoteDate"), $this->input->post("prefix") . $quote_header_id, $this->input->post("clientName"));
                    $this->notify_me_before_due_date($id, $reminder);
                    $this->set_flashmessage("success", sprintf($this->lang->line("save_record_successfull"), $this->lang->line("quote")));
                    redirect("vouchers/quote_edit/" . $this->voucher_header->get_field("id"));
                }
            }
            $this->load->model("item", "itemfactory");
            $this->item = $this->itemfactory->get_instance();
            $this->load->model("tax", "taxfactory");
            $this->tax = $this->taxfactory->get_instance();
            $this->load->model("discount", "discountfactory");
            $this->discount = $this->discountfactory->get_instance();
            $this->load->model(["term", "invoice_note"]);
            if ($id != 0) {
                $this->quote_header->fetch(["voucher_header_id" => $id]);
                $data["activateTax"] = $this->quote_header->get_field("displayTax") * 1;
                $data["activateDiscount"] = $this->quote_header->get_field("displayDiscount") * 1;
                $data["display_item_date"] = $this->quote_header->get_field("display_item_date") * 1;
                $data["quote"] = $this->voucher_header->load_quote_voucher($id);
                $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($id);
                if ($data["quote"] && isset($data["quote"]["related_invoice_id"])) {
                    $this->voucher_header->fetch(["id" => $this->quote_header->get_field("related_invoice_id")]);
                    $this->load->model("invoice_header", "invoice_headerfactory");
                    $this->invoice_header = $this->invoice_headerfactory->get_instance();
                    $this->invoice_header->fetch(["voucher_header_id" => $data["quote"]["related_invoice_id"]]);
                    $prefix = $this->invoice_header->get_field("prefix");
                    $data["related_invoice_id"] = $prefix . $this->voucher_header->get_field("refNum");
                }
                if (isset($data["quote"]["clientAccountId"])) {
                    $this->load->model("account", "accountfactory");
                    $this->account = $this->accountfactory->get_instance();
                    $account_data = $this->account->fetch($data["quote"]["clientAccountId"]);
                    $data["quote"]["model_id"] = $this->account->get_field("model_id");
                }
                $data["autoGenerateQuoteNumber"] = $data["quote"]["refNum"];
                $data["quoteNumberPrefix"] = $data["quote"]["prefix"];
                if (!empty($data["quote"])) {
                    if (!empty($data["quote"]["billTo"])) {
                        $data["quote"]["billTo"] = str_replace("<br />", "", $data["quote"]["billTo"]);
                    }
                    $moneyLanguage = $this->user_preference->get_value("money_language");
                    $data["quote_details"] = $this->quote_detail->load_all_quote_details($data["quote"]["id"], $moneyLanguage);
                    $time_logs = [];
                    foreach ($data["quote_details"] as $key => $val) {
                        if ($this->quote_header->get_field("groupTimeLogsByUserInExport") == "1") {
                            if (!empty($val["time_logs_id"])) {
                                if (array_key_exists($val["worker"] . $val["case_id"], $time_logs)) {
                                    if ($time_logs[$val["worker"] . $val["case_id"]]["unitPrice"] == $val["unitPrice"] && $time_logs[$val["worker"] . $val["case_id"]]["percentage"] == $val["percentage"]) {
                                        $time_logs[$val["worker"] . $val["case_id"]]["quantity"] = $time_logs[$val["worker"] . $val["case_id"]]["quantity"] + $val["quantity"] * 1;
                                        $time_logs[$val["worker"] . $val["case_id"]]["quantities"][] = $val["quantity"];
                                        $time_logs[$val["worker"] . $val["case_id"]]["time_logs_id"][] = $val["time_logs_id"];
                                        $time_logs[$val["worker"] . $val["case_id"]]["time_log_description"][] = $val["time_log_description"];
                                        $time_logs[$val["worker"] . $val["case_id"]]["logDate"][] = $val["logDate"];
                                        unset($data["quote_details"][$key]);
                                    } else {
                                        $time_logs[$val["worker"] . $val["case_id"]]["itemDescription"] = sprintf($this->lang->line("time_logs_item_description"), $val["quantity"], $val["currencyCode"], $val["unitPrice"]);
                                    }
                                } else {
                                    $val["time_logs_id"] = [$val["time_logs_id"]];
                                    $val["quantities"] = [$val["quantity"]];
                                    $val["time_log_description"] = [$val["time_log_description"]];
                                    $val["logDate"] = [$val["logDate"]];
                                    $time_logs[$val["worker"] . $val["case_id"]] = $val;
                                    unset($data["quote_details"][$key]);
                                }
                            }
                        } else {
                            $data["quote_details"][$key]["quantities"] = [$val["quantity"]];
                            $data["quote_details"][$key]["time_log_description"] = [$val["time_log_description"]];
                            $data["quote_details"][$key]["logDate"] = [$val["logDate"]];
                            if (isset($data["quote_details"][$key]["time_logs_id"])) {
                                $data["quote_details"][$key]["time_logs_id"] = [$val["time_logs_id"]];
                            }
                        }
                    }
                    if (!empty($time_logs)) {
                        foreach ($time_logs as $val) {
                            array_push($data["quote_details"], $val);
                        }
                        sort($data["quote_details"]);
                    }
                    foreach ($data["quote_details"] as $keyINV => $recordINV) {
                        $data["quote_details"][$keyINV]["quantity"] = number_format($recordINV["quantity"], 2, ".", ",");
                    }
                    $quotes_items_types = [];
                    $time_logs_data = [];
                    $expenses_data = [];
                    $items_data = [];
                    foreach ($data["quote_details"] as $key => $val) {
                        if (!empty($val["time_logs_id"])) {
                            $time_logs_data[] = $val["id"];
                        } else {
                            if (!empty($val["expense_id"])) {
                                $expenses_data[] = $val["id"];
                            } else {
                                $items_data[] = $val["id"];
                            }
                        }
                    }
                    if (!empty($items_data)) {
                        $quotes_items_types["items"] = $items_data;
                    }
                    if (!empty($time_logs_data)) {
                        $quotes_items_types["time_logs"] = $time_logs_data;
                    }
                    if (!empty($expenses_data)) {
                        $quotes_items_types["expenses"] = $expenses_data;
                    }
                    $data["quotes_items_types"] = $quotes_items_types;
                    $data["sub_items"] = $this->item->get_sub_items_by_quote($data["quote"]["id"]);
                } else {
                    redirect("vouchers/quotes_list");
                }
            }
            if (0 < $id) {
                $active = site_url("vouchers/quote_edit/");
            } else {
                $active = site_url("vouchers/quote_add");
            }
            $data["tabsNLogs"] = $this->_get_quote_tabs_view_vars($id, $active);
            $moneyLanguage = $this->user_preference->get_value("money_language");
            $configNotes = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_from_list")]];
            $this->term->set("_listFieldName", $moneyLanguage . "name");
            $data["terms"] = $this->term->get_terms($moneyLanguage);
            $data["terms_list"] = [];
            foreach ($data["terms"] as $term) {
                $data["terms_list"][$term["id"]] = $term["name"];
            }
            $data["notes_list"] = $this->invoice_note->load_list([], $configNotes);
            $data["notes_descriptions"] = $this->invoice_note->load_all();
            $data["items"] = $this->item->get_items();
            $data["taxes"] = $this->tax->get_taxes();
            $data["discounts"] = $this->discount->get_discounts();
            if ($id && !empty($data["quote"]["id"])) {
                $data["quote"]["exchangeRate"] = $data["rates"][$data["quote"]["currency_id"]];
            }
            $data["rates"] = json_encode($data["rates"]);
            $this->load->model(["money_preference"]);
            $money_preference = $this->money_preference->get_value_by_key("userRatePerHour");
            $invoice_lang = $this->money_preference->get_values_by_group("InvoiceLanguage");
            $moneyLanguage = $moneyLanguage === "" ? 0 : $moneyLanguage;
            foreach ($invoice_lang as $key => $val) {
                $val = unserialize($val);
                $data["labels"][$key] = $val[$moneyLanguage];
            }
            $organizationID = $this->session->userdata("organizationID");
            $data["userRatePerHour"] = "";
            if (isset($money_preference["keyValue"])) {
                $userRatePerHour = unserialize($money_preference["keyValue"]);
            }
            if (isset($userRatePerHour[$organizationID])) {
                $data["userRatePerHour"] = $userRatePerHour[$organizationID];
            }
            $timeTrackingSalesAccount = unserialize($systemPreferences["timeTrackingSalesAccount"]);
            $data["timeTrackingSalesAccount"] = $timeTrackingSalesAccount[$this->session->userdata("organizationID")];
            if (!$data["timeTrackingSalesAccount"] || empty($data["timeTrackingSalesAccount"])) {
                $this->set_flashmessage("warning", sprintf($this->lang->line("you_have_to_set_time_tracking_sales_account")));
                redirect("setup/time_tracking_sales_account");
            }
            $data = array_merge($data, $this->return_notify_before_data($id, $this->quote_header->get("_table")));
            $this->includes("money/js/quote_form", "js");
            $this->load->view("quotes/form", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    public function quote_export_to_word($voucher_header_id = 0, $quoteTemplateId = 0, $format = "")
    {
        if (0 < $voucher_header_id && !$this->validate_voucher($voucher_header_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/quotes_list");
        }
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $data = [];
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $this->load->model("quote_detail", "quote_detailfactory");
        $this->quote_detail = $this->quote_detailfactory->get_instance();
        $this->load->model(["money_preference"]);
        $this->load->model("user_preference");
        $invoice_lang = $this->money_preference->get_values_by_group("InvoiceLanguage");
        $moneyLanguage = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        foreach ($invoice_lang as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$moneyLanguage];
        }
        $data = $this->fill_template_settings($data, $quoteTemplateId, $moneyLanguage);
        $data["is_sample_template"] = $data["template"]["settings"] ? false : true;
        $data["settings"]["body"]["show"]["invoice-nb-container"] = false;
        $data["export_header"] = $this->voucher_header->load_quote_for_template($voucher_header_id, $this->user_preference->get_value("money_language"));
        $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_id);
        $data["export_details"] = $this->quote_detail->load_all_quote_details($data["export_header"]["id"], $this->user_preference->get_value("money_language"));
        $data["invoice_description"] = $data["export_header"]["description"] ?? "";
        $data["form_sub_total"] = number_format($this->input->post("form_sub_total"), $allowed_decimal_format, ".", ",");
        $data["form_total_tax"] = number_format($this->input->post("form_total_tax"), $allowed_decimal_format, ".", ",");
        $data["form_total"] = number_format($this->input->post("form_total"), $allowed_decimal_format, ".", ",");
        $fileName = $data["export_header"]["refNum"] . "_" . date("Ymd");
        $this->voucher_fill_common_data_and_download_file("quotes", $data, $fileName, "quote", $format);
    }
    private function voucher_quote_export_to_word($viewPath, $data, $fileName)
    {
        $time_logs = [];
        foreach ($data["quote_details"] as $key => $val) {
            if ($this->quote_header->get_field("groupTimeLogsByUserInExport") == "1" && !empty($val["time_logs_id"])) {
                if (array_key_exists($val["worker"] . $val["case_id"], $time_logs)) {
                    if ($time_logs[$val["worker"] . $val["case_id"]]["unitPrice"] == $val["unitPrice"] && $time_logs[$val["worker"] . $val["case_id"]]["percentage"] == $val["percentage"]) {
                        $time_logs[$val["worker"] . $val["case_id"]]["quantity"] = $time_logs[$val["worker"] . $val["case_id"]]["quantity"] + $val["quantity"] * 1;
                        unset($data["quote_details"][$key]);
                    }
                } else {
                    $time_logs[$val["worker"] . $val["case_id"]] = $val;
                    unset($data["quote_details"][$key]);
                }
            }
        }
        if (!empty($time_logs)) {
            foreach ($time_logs as $val) {
                array_push($data["quote_details"], $val);
            }
            sort($data["quote_details"]);
        }
        foreach ($data["quote_details"] as $keyINV => $recordINV) {
            $data["quote_details"][$keyINV]["quantity"] = number_format($recordINV["quantity"], 2, ".", ",");
        }
        $quotes_items_types = [];
        foreach ($data["quote_details"] as $key => $val) {
            if (!empty($val["time_logs_id"])) {
                $quotes_items_types["time_logs"][] = $val["id"];
            } else {
                if (!empty($val["expense_id"])) {
                    $quotes_items_types["expenses"][] = $val["id"];
                } else {
                    $quotes_items_types["items"][] = $val["id"];
                }
            }
        }
        $data["quotes_items_types"] = $quotes_items_types;
        $moneyLang = $this->user_preference->get_value("money_language");
        $data["direction"] = $moneyLang == "fl2" ? "rtl" : "ltr";
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $docx->modifyPageLayout("A4-landscape");
        $html = $this->load->view($viewPath, $data, true);
        $docx->embedHTML($html, ["downloadImages" => true]);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "quotes" . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($tempDirectory)) {
            @mkdir($tempDirectory, 493);
        }
        $docx->createDocx($tempDirectory . "/" . $fileName);
        $this->load->helper("download");
        $content = file_get_contents($tempDirectory . "/" . $fileName . ".docx");
        unlink($tempDirectory . "/" . $fileName . ".docx");
        $filenameEncoded = $this->downloaded_file_name_by_browser($fileName . ".docx");
        force_download($filenameEncoded, $content);
        exit;
    }
    public function delete_quote($voucher_header_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            if (0 < $voucher_header_id) {
                $this->db->trans_start();
                $this->load->model("quote_header", "quote_headerfactory");
                $this->load->model("quote_detail", "quote_detailfactory");
                $this->load->model("voucher_header", "voucher_headerfactory");
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->load->model("quote_status_note", "quote_status_notefactory");
                $this->quote_status_note = $this->quote_status_notefactory->get_instance();
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $this->quote_header = $this->quote_headerfactory->get_instance();
                $this->quote_detail = $this->quote_detailfactory->get_instance();
                $this->voucher_header = $this->voucher_headerfactory->get_instance();
                $this->quote_header->fetch(["voucher_header_id" => $voucher_header_id]);
                if (!$this->quote_header->get_field("related_invoice_id") && $this->quote_header->get_field("paidStatus") != "invoiced") {
                    $quote_id = $this->quote_header->get_field("id");
                    $quote_details = $this->quote_detail->load_all_quote_details($quote_id, "");
                    $this->delete_timelog_quote_status($quote_id);
                    if ($this->quote_detail->delete_quote_detail_items($quote_id)) {
                        $this->set_time_logs_status($quote_details, "to-invoice");
                        $this->set_expense_status($quote_details, "to-invoice");
                        $this->document_management_system->delete(["where" => [["module_record_id", $voucher_header_id], ["module", "QOT"]]]);
                        $this->quote_status_note->delete(["where" => [["quote_id", $quote_id]]]);
                        if ($this->quote_header->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                            $this->load->model("invoice_header", "invoice_headerfactory");
                            $this->invoice_header = $this->invoice_headerfactory->get_instance();
                            if ($this->invoice_header->fetch(["related_quote_id" => $voucher_header_id])) {
                                $this->invoice_header->set_field("related_quote_id", NULL);
                                $this->invoice_header->update();
                            }
                            $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
                            $this->voucher_header->delete(["where" => ["id", $voucher_header_id]]);
                        }
                    }
                    $this->db->trans_complete();
                    if ($this->db->trans_status()) {
                        $response["status"] = true;
                    } else {
                        $response["status"] = false;
                    }
                } else {
                    $response["status"] = false;
                    $response["failed_reason"] = $this->lang->line("related_invoice");
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function set_quote_status($quote_header_id, $status = "")
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $this->quote_header->fetch($quote_header_id);
        if ($status == "") {
            $status = $this->quote_header->get_field("paidStatus");
        }
        $this->quote_header->set_field("paidStatus", $status);
        if ($this->quote_header->update()) {
            return true;
        }
        return false;
    }
    public function move_quote_status_to($status)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            $data["voucher_id"] = $this->input->post("voucher_id");
            $data["transition"] = $this->input->post("transition");
            $data["comment"] = $this->input->post("comment");
            $this->load->model("quote_header", "quote_headerfactory");
            $this->quote_header = $this->quote_headerfactory->get_instance();
            if ($data["transition"] == 1) {
                $this->quote_header->fetch($this->input->post("quote_id"));
                $current_status = $this->quote_header->get_field("paidStatus");
                if ($current_status != $status) {
                    $id = $this->input->post("quote_id");
                    $this->load->model("quote_status_note", "quote_status_notefactory");
                    $this->quote_status_note = $this->quote_status_notefactory->get_instance();
                    $this->quote_status_note->set_field("quote_id", $this->input->post("quote_id"));
                    $this->quote_status_note->set_field("transition", $status);
                    if (!empty($data["comment"])) {
                        $this->quote_status_note->set_field("note", $data["comment"]);
                    }
                    if ($this->quote_status_note->insert()) {
                        $response["status"] = $this->quote_header->change_quote_status($id, $status);
                        if ($response["status"]) {
                            $this->load->model("quote_detail", "quote_detailfactory");
                            $this->quote_detail = $this->quote_detailfactory->get_instance();
                            $quote_details = $this->quote_detail->load_all_quote_details($this->input->post("quote_id"), "");
                            $new_status = "to-invoice";
                            if ($status == "approved") {
                                $new_status = "invoiced";
                            }
                            $this->set_time_logs_status($quote_details, $new_status);
                            $this->set_expense_status($quote_details, $new_status);
                            $response["status"] = $status;
                        }
                    } else {
                        $response["validationErrors"] = $this->quote_status_note->get("validationErrors");
                    }
                }
            } else {
                $this->quote_header->fetch(["voucher_header_id" => $data["voucher_id"]]);
                $data["quote_id"] = $this->quote_header->get_field("id");
                $response["html"] = $this->load->view("quotes/change_quote_status", $data, true);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function get_quote_notes()
    {
        $id = $this->input->post("id");
        $this->load->model("quote_status_note", "quote_status_notefactory");
        $this->quote_status_note = $this->quote_status_notefactory->get_instance();
        $this->load->helper("text");
        $data = [];
        $data["quote_notes"] = $this->quote_status_note->fetch_all_quote_notes($id);
        if (!empty($data)) {
            $response["nbOfNotesHistory"] = $this->quote_status_note->count_all_quote_notes($id);
            $response["html"] = $this->load->view("quotes/comments", $data, true);
            $response["result"] = true;
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function list_client_quotes()
    {
        $accountId = $this->input->post("accountID");
        $this->load->model("quote_header", "quote_headerfactory");
        $this->load->model(["term"]);
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $data = [];
        $moneyLanguage = $this->user_preference->get_value("money_language");
        $this->term->set("_listFieldName", $moneyLanguage . "name");
        $data["terms"] = $this->term->load_list([], ["value" => $moneyLanguage . "name"]);
        $data["quotes"] = $this->quote_header->fetch_all_client_quotes($accountId);
        if (!empty($data) && $data["quotes"]) {
            $response["related_quotes"] = $data["quotes"];
            $response["status"] = true;
            $response["html"] = $this->load->view("invoices/client_quotes", $data, true);
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function relate_matters_to_quote()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("legal_case_commission");
        $response = [];
        if (!$this->input->post(NULL)) {
            $return = $this->input->get("return");
            $client_id = $this->input->get("client_id");
            $this->load->helper("text");
            switch ($return) {
                case "cases":
                    $data["cases"] = $this->legal_case->load_cases_by_client_id($client_id);
                    $data["title"] = $this->lang->line("related_case");
                    $data["button_action"] = $this->lang->line("next");
                    $response["related_cases"] = $data["cases"];
                    $response["html"] = $this->load->view("invoices/matter_expenses_time_logs/related_matter", $data, true);
                    break;
                case "expenses_and_time_logs":
                    $cases = $this->input->get("cases");
                    $data["expenses"] = $this->legal_case->k_load_all_legal_case_expenses_per_client($cases, $client_id, $this->input->get("client_account_id"));
                    $response["expenses"]["data"] = $data["expenses"];
                    if ($data["expenses"]) {
                        $response["expenses"]["title"] = $this->lang->line("select_related_expense_to_item");
                        $response["expenses"]["html"] = $this->load->view("invoices/matter_expenses_time_logs/invoice_case_expenses", $data, true);
                    }
                    $money_preference = $this->money_preference->get_value_by_key("userRatePerHour");
                    $organization_id = $this->session->userdata("organizationID");
                    $user_rate_per_hour = "";
                    if (isset($money_preference["keyValue"])) {
                        $user_rate_per_hour = unserialize($money_preference["keyValue"]);
                        $user_rate_per_hour = isset($user_rate_per_hour[$organization_id]) ? $user_rate_per_hour[$organization_id] : false;
                    }
                    $this->load->model("user_activity_log", "user_activity_logfactory");
                    $this->user_activity_log = $this->user_activity_logfactory->get_instance();
                    $this->load->model("tax", "taxfactory");
                    $this->tax = $this->taxfactory->get_instance();
                    $this->load->model("discount", "discountfactory");
                    $this->discount = $this->discountfactory->get_instance();
                    $activate_tax = $this->money_preference->get_key_groups();
                    $data_3["activate_tax"] = $activate_tax["ActivateTaxesinInvoices"]["TEnabled"];
                    $data_3["activate_discount"] = $activate_tax["ActivateDiscountinInvoices"]["DEnabled"];
                    $data["time_logs"] = $this->user_activity_log->load_case_related_user_activity_logs($cases, $user_rate_per_hour);
                    $data["operators_date"] = $this->get_filter_operators("date");
                    $data_3["taxes"] = ["" => $this->lang->line("none")] + $this->tax->load_list_with_percentage();
                    $data_3["discounts"] = ["" => $this->lang->line("none")] + $this->discount->get_dropdown_discount_list();
                    $response["time_logs"]["data"] = $data["time_logs"];
                    if ($data["time_logs"]) {
                        $response["time_logs"]["title"] = $this->lang->line("select_related_time_logs_to_item");
                        $response["time_logs"]["html"] = $this->load->view("invoices/matter_expenses_time_logs/filter_invoice_case_time_logs", $data, true) . $this->load->view("invoices/matter_expenses_time_logs/invoice_case_time_logs", $data, true) . $this->load->view("invoices/matter_expenses_time_logs/time_logs_tax_discount", $data_3, true);
                    }
                    $response["caseCommissions"] = $this->legal_case_commission->fetch_commissions($cases);
                    break;
                case "chosen_cases":
                    $data["title"] = $this->lang->line("selected_cases");
                    $data["button_action"] = $this->lang->line("ok");
                    $all_cases = $this->legal_case->load_cases_by_client_id($client_id);
                    if ($voucher_header_id = $this->input->get("voucher_header_id")) {
                        $this->load->model("quote_detail", "quote_detailfactory");
                        $this->quote_detail = $this->quote_detailfactory->get_instance();
                        $cases = $this->quote_detail->load_quote_related_cases($voucher_header_id);
                        if (!empty($cases["case_id"])) {
                            $cases["case_id"] = explode(",", $cases["case_id"]);
                            if ($cases["paidStatus"] === "open") {
                                $data["cases"] = $all_cases;
                                $data["button_action"] = $this->lang->line("next");
                                $all_case_id = array_column($data["cases"], "id");
                                foreach ($cases["case_id"] as $key => $case_id) {
                                    if (in_array($case_id, $all_case_id)) {
                                        $index = array_keys($all_case_id, $case_id);
                                        $data["cases"][$index[0]]["checked"] = true;
                                    }
                                }
                                $response["related_cases"] = $all_cases;
                            } else {
                                $case_subjects = explode(":/;", $cases["caseSubject"]);
                                $data["chosen_cases"] = [];
                                foreach ($cases["case_id"] as $key => $case_id) {
                                    $data["chosen_cases"][]["caseId"] = $this->legal_case->get("modelCode") . $case_id . " - " . $case_subjects[$key];
                                }
                            }
                        } else {
                            $response["related_cases"] = [];
                            if ($cases["paidStatus"] === "open") {
                                $data["title"] = $this->lang->line("related_case");
                                $data["cases"] = $all_cases;
                                $data["button_action"] = $this->lang->line("next");
                                $response["related_cases"] = $all_cases;
                            }
                        }
                    } else {
                        $chosen_cases = array_column(array_filter($this->input->get("chosen_cases")), "id");
                        foreach ($all_cases as $key => $case) {
                            if (in_array($case["id"], $chosen_cases)) {
                                $all_cases[$key]["checked"] = true;
                            }
                        }
                        $data["cases"] = $all_cases;
                        $data["button_action"] = $this->lang->line("next");
                        $response["related_cases"] = $all_cases;
                    }
                    $response["html"] = $this->load->view("invoices/matter_expenses_time_logs/related_matter", $data, true);
                    break;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function convert_expense_billingStatus_to_quote()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = ["result" => false];
            $client_account_id = $this->input->post("client_account_id");
            $expensesIds = $this->input->post("expensesIds");
            $this->load->model("expense", "expensefactory");
            $this->expense = $this->expensefactory->get_instance();
            if (isset($expensesIds)) {
                foreach ($expensesIds as $val) {
                    $this->expense->reset_fields();
                    $this->expense->fetch($val);
                    $this->expense->set_field("billingStatus", "to-invoice");
                    $this->expense->set_field("client_account_id", $client_account_id);
                    $response["result"] = $this->expense->update();
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function quote_edit_unhide_discount()
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["result"] = $this->quote_header->unhide_quote_discount($voucherID);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function quote_edit_hide_discount()
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["result"] = $this->quote_header->hide_quote_discount($voucherID);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function quote_add_hide_unhide_tax($tax = "", $discount = "")
    {
        $this->quote_save(0, $tax, $discount);
    }
    public function quote_add_hide_unhide_discount($tax = "", $discount = "")
    {
        $this->quote_save(0, $tax, $discount);
    }
    public function get_quote_items()
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        $this->load->model("quote_detail", "quote_detailfactory");
        $this->quote_detail = $this->quote_detailfactory->get_instance();
        $this->load->model("item", "itemfactory");
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->item = $this->itemfactory->get_instance();
        $response = [];
        $quote_id = $this->input->post("quote_id");
        $moneyLanguage = $this->user_preference->get_value("money_language");
        $data["quote_details"] = $this->quote_detail->load_all_quote_details($quote_id, $moneyLanguage);
        $data["quote"] = ["id" => "", "organization_id" => "", "voucher_header_id" => "", "suffix" => "", "clientAccountId" => "", "clientAccountName" => "", "clientAccountCurrency" => "", "referenceNum" => "", "dated" => "", "description" => "", "total" => 0, "term_id" => "", "term_days_nb" => 0, "status" => "", "dueOn" => "", "purchaseOrder" => "", "notes" => "", "billTo" => "", "groupTimeLogsByUserInExport" => "", "quoteNumber" => ""];
        $this->quote_header->fetch(["id" => $quote_id]);
        $voucher_header_id = $this->quote_header->get_field("voucher_header_id");
        $data["activateTax"] = $this->quote_header->get_field("displayTax") * 1;
        $data["activateDiscount"] = $this->quote_header->get_field("displayDiscount") * 1;
        $data["items"] = $this->item->get_items();
        $time_logs = [];
        foreach ($data["quote_details"] as $key => $val) {
            if ($this->quote_header->get_field("groupTimeLogsByUserInExport") == "1") {
                if (!empty($val["time_logs_id"])) {
                    if (array_key_exists($val["worker"] . $val["case_id"], $time_logs)) {
                        if ($time_logs[$val["worker"] . $val["case_id"]]["unitPrice"] == $val["unitPrice"] && $time_logs[$val["worker"] . $val["case_id"]]["percentage"] == $val["percentage"]) {
                            $time_logs[$val["worker"] . $val["case_id"]]["quantity"] = $time_logs[$val["worker"] . $val["case_id"]]["quantity"] + $val["quantity"] * 1;
                            $time_logs[$val["worker"] . $val["case_id"]]["quantities"][] = $val["quantity"];
                            $time_logs[$val["worker"] . $val["case_id"]]["time_logs_id"][] = $val["time_logs_id"];
                            $time_logs[$val["worker"] . $val["case_id"]]["time_log_description"][] = $val["time_log_description"];
                            $time_logs[$val["worker"] . $val["case_id"]]["logDate"][] = $val["logDate"];
                            unset($data["quote_details"][$key]);
                        } else {
                            $time_logs[$val["worker"] . $val["case_id"]]["itemDescription"] = sprintf($this->lang->line("time_logs_item_description"), $val["quantity"], $val["currencyCode"], $val["unitPrice"]);
                        }
                    } else {
                        $val["time_logs_id"] = [$val["time_logs_id"]];
                        $val["quantities"] = [$val["quantity"]];
                        $val["time_log_description"] = [$val["time_log_description"]];
                        $val["logDate"] = [$val["logDate"]];
                        $time_logs[$val["worker"] . $val["case_id"]] = $val;
                        unset($data["quote_details"][$key]);
                    }
                }
            } else {
                $data["quote_details"][$key]["quantities"] = [$val["quantity"]];
                $data["quote_details"][$key]["time_log_description"] = [$val["time_log_description"]];
                $data["quote_details"][$key]["logDate"] = [$val["logDate"]];
                if (isset($data["quote_details"][$key]["time_logs_id"])) {
                    $data["quote_details"][$key]["time_logs_id"] = [$val["time_logs_id"]];
                }
            }
        }
        if (!empty($time_logs)) {
            foreach ($time_logs as $val) {
                array_push($data["quote_details"], $val);
            }
            sort($data["quote_details"]);
        }
        foreach ($data["quote_details"] as $keyINV => $recordINV) {
            $data["quote_details"][$keyINV]["quantity"] = number_format($recordINV["quantity"], 2, ".", ",");
        }
        $quotes_items_types = [];
        $time_logs_data = [];
        $expenses_data = [];
        $items_data = [];
        foreach ($data["quote_details"] as $key => $val) {
            if (!empty($val["time_logs_id"])) {
                $time_logs_data[] = $val["id"];
            } else {
                if (!empty($val["expense_id"])) {
                    $expenses_data[] = $val["id"];
                } else {
                    $items_data[] = $val["id"];
                }
            }
        }
        if (!empty($items_data)) {
            $quotes_items_types["items"] = $items_data;
        }
        if (!empty($time_logs_data)) {
            $quotes_items_types["time_logs"] = $time_logs_data;
        }
        if (!empty($expenses_data)) {
            $quotes_items_types["expenses"] = $expenses_data;
        }
        $data["quote"] = $this->voucher_header->load_quote_voucher($voucher_header_id);
        $data["quotes_items_types"] = $quotes_items_types;
        $response["html"] = $this->load->view("quotes/items", $data, true);
        $response["status"] = true;
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function delete_timelog_invoice_status($invoice_id)
    {
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $this->load->model("user_activity_log", "user_activity_logfactory");
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
        $result = $this->invoice_detail->get_invoice_detail_timelogs_ids($invoice_id);
        $ids = [];
        foreach ($result as $record) {
            $ids[] = $record["time_log"];
        }
        if (!empty($ids)) {
            $this->user_activity_log->delete_logs_invoicing_status_by_ids($ids);
        }
    }
    private function delete_timelog_quote_status($quote_id)
    {
        $this->load->model("quote_detail", "quote_detailfactory");
        $this->quote_detail = $this->quote_detailfactory->get_instance();
        $this->load->model("user_activity_log", "user_activity_logfactory");
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
        $result = $this->quote_detail->get_quote_detail_timelogs_ids($quote_id);
        $ids = [];
        foreach ($result as $record) {
            $data = $this->user_activity_log->fetch_invoiced_user_activity_logs($record["time_log"]);
            if (empty($data)) {
                $ids[] = $record["time_log"];
            }
        }
        if (!empty($ids)) {
            $this->user_activity_log->delete_logs_invoicing_status_by_ids($ids);
        }
    }
    public function edit_invoice_hide_unhide_item_date($voucher_id = 0)
    {
        if ($this->input->post(NULL)) {
            $display_item = $this->input->post("display");
            if ($voucher_id) {
                $response["status"] = false;
                $this->load->model("invoice_header", "invoice_headerfactory");
                $this->invoice_header = $this->invoice_headerfactory->get_instance();
                $response = [];
                $response["status"] = $this->invoice_header->hide_unhide_invoice_item_date($voucher_id, $display_item);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function edit_quote_hide_unhide_item_date($voucher_id = 0)
    {
        if ($this->input->post(NULL)) {
            $display_item = $this->input->post("display");
            if ($voucher_id) {
                $response["status"] = false;
                $this->load->model("quote_header", "quote_headerfactory");
                $this->quote_header = $this->quote_headerfactory->get_instance();
                $response = [];
                $response["status"] = $this->quote_header->hide_unhide_quote_item_date($voucher_id, $display_item);
                $this->output->set_content_type("application/json");
                $this->output->set_output(json_encode($response));
            }
        }
    }
    public function add_quote_hide_unhide_item_date($activate_tax, $activate_discount, $display_item)
    {
        $this->quote_save(0, $activate_tax, $activate_discount, $display_item);
    }
    public function invoice_export_options()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
        $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
        if ($this->input->get("return") === "html") {
            $data["auto_export_type"] = $this->input->get("auto_export_type");
            $data["invoice_templates"] = $this->organization_invoice_template->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["type", "invoice"]]], ["value" => "name"]);
            $data["export_options"] = ["preview" => $this->lang->line("preview"), "word" => "Word", "pdf" => "PDF"];
            if ($this->input->get("type") && $this->input->get("type") != "invoice") {
                unset($data["export_options"]["preview"]);
            }
            $data["selected_template_id"] = "";
            if (!empty($data["auto_export_type"]) && $this->input->get("id")) {
                $this->load->model("invoice_header", "invoice_headerfactory");
                $this->invoice_header = $this->invoice_headerfactory->get_instance();
                $invoice_row = $this->invoice_header->load(["where" => ["voucher_header_id", $this->input->get("id")]]);
                if (!empty($invoice_row) && !empty($invoice_row["invoice_template_id"])) {
                    $data["selected_template_id"] = $invoice_row["invoice_template_id"];
                } else {
                    $defaultInvoiceTemplate = $this->organization_invoice_template->get_entity_default_invoice_template("invoice", $this->session->userdata("organizationID"));
                    if (!empty($defaultInvoiceTemplate)) {
                        $data["selected_template_id"] = $defaultInvoiceTemplate["id"];
                    }
                }
            }
            $response["html"] = $this->load->view("invoices/export_options", $data, true);
            $response["selected_template_id"] = $data["selected_template_id"];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function open_file_viewer()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = [];
        $data["file"] = $this->input->get("file");
        if (!empty($data["file"])) {
            $response["html"] = $this->load->view("invoices/file_viewer", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function payment_export_options()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
        $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
        if ($this->input->get("return") === "html") {
            $data["invoice_templates"] = ["" => $this->lang->line("none")] + $this->organization_invoice_template->load_list(["where" => ["organization_id", $this->session->userdata("organizationID")]], ["value" => "name"]);
            $data["export_options"] = ["word" => "Word", "pdf" => "PDF"];
            $data["is_header_template_only"] = true;
            $response["html"] = $this->load->view("invoices/export_options", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function advice_fee_note_export_options($voucher_header_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        if ($this->input->get("return") === "html") {
            $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
            $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $this->load->model("item_commission");
            $invoice_id = $this->invoice_header->load(["where" => ["voucher_header_id", $voucher_header_id]])["id"];
            $data["invoice_templates"] = $this->organization_invoice_template->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["type", "partner"]]], ["value" => "name"]);
            $parrtners = $this->item_commission->fetch_commissions($invoice_id);
            $temp_parrtners = array_unique(array_column($parrtners, "id"));
            $data["partners"] = array_intersect_key($parrtners, $temp_parrtners);
            $response["html"] = $this->load->view("invoices/advice_fee_export_options", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function advice_fee_note_export_to_word($partner_account_id, $voucher_header_invoice_ids, $invoice_template_id, $payment_id = NULL)
    {
        $data = [];
        $invoices = [];
        $this->load->model(["money_preference"]);
        $this->load->model("user_preference");
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        $invoice_lang = $this->money_preference->get_values_by_group("PartnerInvLanguage");
        $money_language = $this->user_preference->get_value("money_language");
        $money_language_index = $money_language == "" ? 0 : $money_language;
        foreach ($invoice_lang as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$money_language_index];
        }
        $data = $this->fill_template_settings($data, $invoice_template_id, $money_language_index);
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->load->model("item_commission");
        $this->load->model("invoice_payment");
        $this->load->model("settlement_invoice");
        $this->load->model("exchange_rate");
        $rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $payment_due_to = $this->account->fetch_account($partner_account_id);
        if (!is_array($voucher_header_invoice_ids)) {
            $voucher_header_invoice_ids = [$voucher_header_invoice_ids];
        }
        foreach ($voucher_header_invoice_ids as $voucher_header_invoice_id) {
            $invoice = $this->voucher_header->load_partner_invoice_for_template($voucher_header_invoice_id, $money_language);
            $invoice_details = $this->invoice_detail->load_invoice_details($invoice["id"], $money_language);
            $partner_commissions = $this->item_commission->fetch_commissions(array_values($invoice_details)[0]["invoice_header_id"], $partner_account_id);
            $invoice_header_data = [];
            $invoice_header_data["activate_discount"] = $invoice["displayDiscount"];
            $invoice_header_data["discount_percentage"] = $invoice["discount_percentage"];
            $invoice_header_data["discount_amount"] = $invoice["discount_amount"];
            $invoice_header_data["discount_type"] = $invoice["discount_value_type"];
            $invoice_total = $invoice["lines_total_subtotal"] - $invoice["lines_total_discount"];
            $partner_total_commissions = $this->calculate_partner_total_commissions($partner_commissions, $invoice_details, $invoice_header_data);
            $invoice_total_taxes = $invoice["lines_total_tax"];
            $invoice_total_taxes_percentage = $invoice_total_taxes / $invoice["total"] * 100;
            $total_payments = 0;
            if (is_null($payment_id)) {
                $this->load->model("invoice_payment_invoice");
                $payments = $this->invoice_payment_invoice->load_voucher_payments($invoice["invoice_header_id"]);
                foreach ($payments as $payment) {
                    $total_payments += $payment["total"] * $rates[$payment["currency_id"]];
                }
            } else {
                $payment_made = $this->invoice_payment->load_payment_data($payment_id);
                $total_payments = $payment_made["paymentAmount"] * $rates[$payment_made["currency_id"]] * 1;
            }
            $commission_percentage = $partner_total_commissions / $invoice_total * 100;
            $payment_without_tax = $total_payments - $total_payments * $invoice_total_taxes_percentage / 100;
            $commission_amount = $payment_without_tax * $commission_percentage / 100;
            $tax_amount = $total_payments * $invoice_total_taxes_percentage / 100;
            $invoice_data = [];
            $invoice_data["client_name"] = $invoice["clientAccountName"];
            $invoice_data["invoice_nb"] = $invoice["refNum"];
            $invoice_data["invoice_total"] = number_format($invoice["total"] * $rates[$invoice["currency_id"]] * 1, $allowed_decimal_format, ".", ",");
            $invoice_data["date"] = is_null($payment_id) ? $invoice["dated"] : $payment_made["dated"];
            $invoice_data["payment_amount"] = number_format($total_payments, $allowed_decimal_format, ".", ",");
            $invoice_data["commission_percentage"] = number_format($commission_percentage, $allowed_decimal_format, ".", "");
            $invoice_data["deductions"] = number_format($tax_amount, $allowed_decimal_format, ".", ",");
            $invoice_data["partner_amount"] = number_format($commission_amount, $allowed_decimal_format, ".", ",");
            array_push($invoices, $invoice_data);
        }
        $amounts_total = 0;
        foreach ($invoices as $invoice) {
            $amounts_total += (double) str_replace(",", "", $invoice["partner_amount"]);
        }
        $fileName = $data["template"]["name"] . "_" . date("Ymd");
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $properties = ["creator" => "Sheria360", "lastModifiedBy" => "Sheria360", "revision" => "1"];
        $docx->addProperties($properties);
        $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400, "marginRight" => 700, "marginLeft" => 700];
        $docx->modifyPageLayout("letter", $layout_options);
        $data["direction"] = is_rtl($data["settings"]["body"]["general"]["title"][$money_language_index]) ? "rtl" : "ltr";
        $data["money_language"] = $money_language_index;
        $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_invoice_id);
        $data["export_data"]["invoices"] = $invoices;
        $data["export_data"]["payment_due_to"] = $payment_due_to["name"] ?? "";
        $data["export_data"]["currency"] = $this->session->userdata("organizationCurrency");
        $data["export_data"]["total"] = number_format($amounts_total, $allowed_decimal_format, ".", ",");
        $html = $this->load->view("invoices/advice_fee_note_export_to_word", $data, true);
        $docx->embedHTML($html, ["downloadImages" => true]);
        $docx->addHeader(["default" => $this->add_export_header($data, $docx)]);
        $docx->addFooter(["default" => $this->add_export_footer($data, $docx)]);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($tempDirectory)) {
            @mkdir($tempDirectory, 493);
        }
        $docx->createDocx($tempDirectory . "/" . $fileName);
        $this->load->helper("download");
        $content = file_get_contents($tempDirectory . "/" . $fileName . ".docx");
        unlink($tempDirectory . "/" . $fileName . ".docx");
        $filenameEncoded = $this->downloaded_file_name_by_browser($fileName . ".docx");
        force_download($filenameEncoded, $content);
        exit;
    }
    public function quote_export_to_excel()
    {
        $filter = json_decode($this->input->post("filter"), true);
        $sortable = json_decode($this->input->post("sort"), true);
        $selected_columns = ["columns_to_select" => false];
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        if ($this->input->post("export_all_columns") == "false") {
            $selected_columns = $this->return_current_columns("Quote_Header");
        }
        $data = $selected_columns + $this->voucher_header->k_load_all_quotes($filter, $sortable);
        $filename = urlencode($this->lang->line("excel_quotes"));
        $this->output->set_content_type("application/vnd.ms-excel");
        $this->output->set_header("Content-Description: File Transfer");
        $this->output->set_header("Content-Disposition: attachment; filename*=UTF-8''" . $filename . "_" . date("Ymd") . ".xls");
        $this->load->view("excel/header");
        $this->load->view("excel/quotes_list", $data);
        $this->load->view("excel/footer");
    }
    public function quote_edit_unhide_tax()
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["result"] = $this->quote_header->unhide_quote_tax($voucherID);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function quote_edit_hide_tax()
    {
        $this->load->model("quote_header", "quote_headerfactory");
        $this->quote_header = $this->quote_headerfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $response = [];
            $voucherID = $this->input->post("voucherID");
            if ($voucherID) {
                $response["result"] = $this->quote_header->hide_quote_tax($voucherID);
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function switch_language($first_language, $second_language, $is_switch_from_money)
    {
        $this->load->model("system_preference");
        $money_languages_available_db = $this->get_money_languages_available_db();
        $money_languages_available = $this->get_all_available_languages($money_languages_available_db);
        $this->load->model("language");
        $system_languages_available = array_column($this->language->load_all(), "fullName");
        $money_language = $second_language;
        $system_language = $first_language;
        if ($is_switch_from_money) {
            $money_language = $first_language;
            $system_language = $second_language;
            $language_to_switch_on = $system_language;
        } else {
            $language_to_switch_on = $money_languages_available[$money_language];
        }
        $money_language = $money_languages_available[$money_language];
        if ($money_language === $system_language || !in_array($money_language, $system_languages_available)) {
            return NULL;
        }
        $_SESSION["AUTH_language"] = $language_to_switch_on;
        $this->lang->load("common", $language_to_switch_on, false, true);
    }
    private function get_all_available_languages($money_languages)
    {
        $money_languages_available = [];
        foreach ($money_languages as $key => $money_language) {
            $money_language = mb_strtolower($money_language, "UTF-8");
            if ($money_language === "arabic" || $money_language === "العربية") {
                $money_languages_available[$key] = "arabic";
            } else {
                if ($money_language === "english") {
                    $money_languages_available[$key] = "english";
                } else {
                    if ($money_language === "french" || $money_language === "français") {
                        $money_languages_available[$key] = "french";
                    } else {
                        if ($money_language === "spanish" || $money_language === "española") {
                            $money_languages_available[$key] = "spanish";
                        } else {
                            $money_languages_available[$key] = $money_languages[$key];
                        }
                    }
                }
            }
        }
        return $money_languages_available;
    }
    private function calculate_total_amount_of_invoice($voucher_id)
    {
        $allowed_decimal_format = $this->config->item("allowed_decimal_format");
        $item_accounts = [];
        $this->load->model("exchange_rate");
        $rate = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $invoice = $this->voucher_header->load_invoice_voucher($voucher_id);
        $client_account = $this->account->fetch_account($invoice["clientAccountId"]);
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $invoice_data = $this->invoice_detail->load_all(["where" => ["invoice_header_id", $invoice["invoice_id"]]]);
        $local_amount = 0;
        $foreign_amount = 0;
        foreach ($invoice_data as $value) {
            $amount = (double) number_format($value["quantity"] * $value["unitPrice"] * 1, $allowed_decimal_format, ".", "");
            $local_amount = $local_amount + (double) number_format((double) $amount * (double) $rate[$client_account["currency_id"]] * 1, $allowed_decimal_format, ".", "");
            $foreign_amount = $foreign_amount + $amount;
        }
        $item_accounts["local_amount"] = $local_amount;
        $item_accounts["foreign_amount"] = $foreign_amount;
        return $item_accounts;
    }
    private function add_export_header($data, $docx)
    {
        if ($data["settings"]["header"]["show"]["logo-container"] && $data["settings"]["header"]["general"]["logo"]) {
            $main_directory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . "organizations";
            if (!$data["is_sample_template"]) {
                $file = $main_directory . DIRECTORY_SEPARATOR . $this->organization->get("modelCode") . str_pad($this->session->userdata("organizationID"), 4, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . $data["settings"]["header"]["general"]["logo"];
            } else {
                $file = $main_directory . DIRECTORY_SEPARATOR . "templates" . DIRECTORY_SEPARATOR . "logo-sample.png";
            }
            if (file_exists($file)) {
                $image_options = ["src" => $file, "imageAlign" => $data["settings"]["header"]["show"]["center-logo"] ? "center" : ($data["direction"] == "rtl" ? "left" : "right")];
                if ($data["settings"]["header"]["show"]["image_full_width"]) {
                    $image_options = ["src" => $file, "imageAlign" => $data["settings"]["header"]["show"]["center-logo"] ? "center" : ($data["direction"] == "rtl" ? "right" : "left"), "textWrap" => 1, "relativeToHorizontal" => "page"];
                }
                $header_image = new WordFragment($docx, "defaultHeader");
                $header_image->addImage($image_options);
            }
        }
        if ($data["settings"]["header"]["show"]["center-logo"]) {
            return $header_image;
        }
        if ($data["settings"]["header"]["show"]["company-info-container"]) {
            $header_text = new WordFragment($docx, "defaultHeader");
            $company_info = $data["settings"]["header"]["general"]["notes"];
            if (!empty($data["template"]["organization_id"])) {
                $this->load->model("organization", "organizationfactory");
                $org_row = $this->organizationfactory->get_instance()->load(["where" => ["id", $data["template"]["organization_id"]]]);
                $additional_id_types_arr = $this->organizationfactory->get_instance()->get_additional_id_types();
                $additional_id_type = isset($additional_id_types_arr[$org_row["additional_id_type"]]) ? $additional_id_types_arr[$org_row["additional_id_type"]] : "";
                $company_info = str_replace(["{{entity_name}}", "{{entity_address1}}", "{{entity_address2}}", "{{entity_city}}", "{{entity_state}}", "{{entity_zip}}", "{{entity_tax_number}}", "{{entity_website}}", "{{entity_phone}}", "{{entity_fax}}", "{{entity_mobile}}", "{{entity_comments}}", "{{additional_id_type}}", "{{additional_id_value}}", "{{street_name}}", "{{building_number}}", "{{address_additional_number}}"], [$org_row["name"], $org_row["address1"], $org_row["address2"], $org_row["city"], $org_row["state"], $org_row["zip"], $org_row["tax_number"], $org_row["website"], $org_row["phone"], $org_row["fax"], $org_row["mobile"], $org_row["comments"], $additional_id_type, $org_row["additional_id_value"], $org_row["street_name"], $org_row["building_number"], $org_row["address_additional_number"]], $company_info);
            }
            $html_text = $data["direction"] == "rtl" ? "<div style=\"direction: rtl; font-family: " . $data["settings"]["properties"]["page-font"] . ";\">" . $company_info . "</div>" : "<div style=\"text-align: left;\">" . $company_info . "</div>";
            $header_text->embedHTML($html_text);
        }
        $values_table = $data["direction"] == "rtl" ? [[["value" => $header_image], ["value" => $header_text]]] : [[["value" => $header_text], ["value" => $header_image]]];
        $width_table_cols = $data["direction"] == "rtl" ? [6000, 7000] : [7000, 6000];
        $params_table = ["border" => "nil", "columnWidths" => $width_table_cols];
        $header_table = new WordFragment($docx, "defaultHeader");
        $header_table->addTable($values_table, $params_table);
        return $header_table;
    }
    private function add_export_footer($data, $docx)
    {
        $footer_table = new WordFragment($docx, "defaultFooter");
        if ($data["settings"]["footer"]["show"]["footer-container"] && $data["settings"]["footer"]["general"]["notes"] || $data["settings"]["footer"]["show"]["page-numbering"]) {
            if ($data["settings"]["footer"]["show"]["footer-container"] && $data["settings"]["footer"]["general"]["notes"]) {
                $footer = new WordFragment($docx, "defaultFooter");
                $footer->embedHTML(html_entity_decode($data["settings"]["footer"]["general"]["notes"]));
                $valuesTable = [[["value" => $footer, "vAlign" => "center"]]];
                $footer_table_cols = [9000];
            }
            if ($data["settings"]["footer"]["show"]["page-numbering"]) {
                $numbering = new WordFragment($docx, "defaultFooter");
                $options = ["textAlign" => "right"];
                $numbering->addPageNumber("numerical", $options);
                $valuesTable = [[["value" => $numbering, "vAlign" => "right"]]];
                $footer_table_cols = [9000];
            }
            if ($data["settings"]["footer"]["show"]["page-numbering"] && $data["settings"]["footer"]["show"]["footer-container"] && $data["settings"]["footer"]["general"]["notes"]) {
                $valuesTable = [[["value" => $footer, "vAlign" => "center"], ["value" => $numbering, "vAlign" => "right"]]];
                $footer_table_cols = [9000, 100];
            }
            $footer_parm_table = ["border" => "nil", "columnWidths" => $footer_table_cols];
            $footer_table->addTable($valuesTable, $footer_parm_table);
        }
        return $footer_table;
    }
    private function get_payment_print_components($payment_voucher_id, $payment_id, $voucher_id)
    {
        if (0 < $payment_voucher_id && !$this->validate_voucher($payment_voucher_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model(["invoice_payment", "invoice_payment_invoice"]);
        $data = [];
        $data["payment_header"] = $this->invoice_payment->load_header_details($payment_voucher_id, $payment_id);
        $data["other_payment_data"] = $this->invoice_payment->load_other_payment_data($payment_id, $payment_voucher_id);
        if (false === $data["payment_header"]) {
            redirect(app_url("", "money"));
        }
        $this->load->library("towords", ["major" => $this->inflector->pluralize($data["payment_header"]["currencyName"]), "amount" => $data["payment_header"]["total"]]);
        $data["thirdpartyAccount"] = $this->account->fetch_account($data["payment_header"]["thirdPartyAccountId"]);
        $data["payment_header"]["literalAmount"] = $this->towords->get_words();
        $data["payment_header"]["total"] = number_format($data["payment_header"]["total"], 2);
        $data["payment_details"] = $this->invoice_payment_invoice->load_lines_with_invoice_details($payment_id);
        $data["direction"] = $this->is_auth->is_layout_rtl() ? "rtl" : "ltr";
        $data["invoice_header_data"] = [];
        $data["invoice_details_data"] = [];
        if (0 < $voucher_id) {
            $data["invoice_header_data"] = $this->voucher_header->load_invoice_voucher($voucher_id);
            if (!empty($data["invoice_header_data"])) {
                $invoice_payments = $this->invoice_payment_invoice->load_all(["where" => ["invoice_header_id", $data["invoice_header_data"]["id"]]]);
                $data["invoice_header_data"]["balance_due"] = $data["invoice_header_data"]["total"];
                foreach ($invoice_payments as $payment) {
                    $data["invoice_header_data"]["balance_due"] -= $payment["amount"] * 1;
                }
            }
            $money_language = $this->user_preference->get_value("money_language");
            $this->load->model("invoice_detail", "invoice_detailfactory");
            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
            $data["invoice_details_data"] = $this->invoice_detail->load_invoice_details($data["invoice_header_data"]["id"], $money_language);
        }
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->invoice_header->fetch(["voucher_header_id" => $voucher_id]);
        $data["activate_tax"] = $this->invoice_header->get_field("displayTax");
        $data["activate_discount"] = $this->invoice_header->get_field("displayDiscount");
        $data["total_discount_percentage"] = $this->invoice_header->get_field("discount_percentage");
        return $data;
    }
    public function get_filtered_time_logs()
    {
        $response = [];
        $post_data = $this->input->post(NULL);
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->helper("text");
        $money_preference = $this->money_preference->get_value_by_key("userRatePerHour");
        $organization_id = $this->session->userdata("organizationID");
        $user_rate_per_hour = "";
        if (isset($money_preference["keyValue"])) {
            $user_rate_per_hour = unserialize($money_preference["keyValue"]);
            $user_rate_per_hour = isset($user_rate_per_hour[$organization_id]) ? $user_rate_per_hour[$organization_id] : false;
        }
        $this->load->model("user_activity_log", "user_activity_logfactory");
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
        $filter = $post_data["filter"];
        $time_logs = $this->user_activity_log->load_case_related_user_activity_logs($post_data["cases"], $user_rate_per_hour, $filter);
        $data["operators_date"] = $this->get_filter_operators("date");
        $data["time_logs"] = $time_logs;
        $response["time_logs"]["data"] = $data["time_logs"];
        if ($data["time_logs"]) {
            $response["time_logs"]["title"] = $this->lang->line("select_related_time_logs_to_item");
            $response["time_logs"]["html"] = $this->load->view("invoices/matter_expenses_time_logs/invoice_case_time_logs", $data, true);
        } else {
            $response["error"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function expenses_add_bulk($extraData = [])
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("expenses") . " | " . $this->lang->line("money"));
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->helper(["text"]);
        $caseId = "";
        $caseSubject = $case_category = "";
        $clientId = "";
        $clientName = "";
        $billingStatus = "";
        $related_hearing = $related_event = $related_task = false;
        $data["isCasePreset"] = false;
        if (!empty($extraData)) {
            $caseId = $extraData["caseId"];
            $caseSubject = $extraData["caseSubject"];
            $clientId = isset($extraData["clientId"]) ? $extraData["clientId"] : "";
            $clientName = isset($extraData["clientName"]) ? $extraData["clientName"] : "";
            $case_category = $extraData["case_category"];
            $related_hearing = isset($extraData["hearing"]) ? $extraData["hearing"] : false;
            $related_task = isset($extraData["task"]) ? $extraData["task"] : false;
            $related_event = isset($extraData["event"]) ? $extraData["event"] : false;
            $data["isCasePreset"] = true;
        }
        $data["expense"] = ["id" => "", "expense_account" => "", "expense_category_id" => "", "paid_through" => "", "tax_id" => "", "amount" => "", "billingStatus" => "", "dated" => "", "description" => "", "referenceNum" => "", "attachment" => "", "case_id" => $caseId, "case_subject" => $caseSubject, "case_category" => $case_category, "vendor_id" => "", "vendorName" => "", "client_id" => $clientId, "client_account_id" => "", "clientName" => $clientName, "paymentMethod" => "", "related_hearing" => $related_hearing, "related_task" => $related_task, "related_event" => $related_event];
        $systemPreferences = $this->session->userdata("systemPreferences");
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $case_currency_id = $this->legal_case->get_money_currency();
        if (!empty($exchange_rates)) {
            $data["rates"] = $exchange_rates;
            if ($this->input->post(NULL)) {
                $post_data = $this->input->post(NULL);
                $this->load->model("money_preference");
                if ($this->license_availability === false) {
                    $this->set_flashmessage("error", $this->licensor->get_license_message());
                    redirect("vouchers/expense_edit/" . $id);
                }
                $this->validate_current_organization($this->input->post("organization_id"), "expenses_list");
                $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                $initial_expense_status = $moneyPreferences["keyValue"];
                $result = false;
                if ($this->input->post("expense_account") == $this->input->post("paid_through")) {
                    $this->set_flashmessage("error", $this->lang->line("transaction_not_saved"));
                    redirect("vouchers/expenses_list/");
                }
                if (!$systemPreferences["requireExpenseDocument"] || $systemPreferences["requireExpenseDocument"] && !empty($_FILES["expenses"]["name"])) {
                    if ($this->input->post("case_id") && $this->legal_case->add_client_to_case($this->input->post(NULL))) {
                        $client_added_to_case_message = "<li>" . $this->lang->line("client_added_to_case") . "</li>";
                    }
                    $counter = 0;
                    if ($this->input->post("client_id") && $this->input->post("case_id") && $this->input->post("billingStatus") == "to-invoice" && !empty($case_currency_id)) {
                        $validate_capping_amount = $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $post_data["total"]);
                    }
                    if ($this->input->post("case_id") && $this->input->post("client_id") && !empty($case_currency_id) && $this->input->post("billingStatus") == "to-invoice" && $validate_capping_amount == "disallow") {
                        $this->legal_case->fetch($this->input->post("case_id"));
                        $this->set_flashmessage("error", sprintf($this->lang->line("capping_amount_validation"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation")));
                        redirect("vouchers/expenses_list/");
                    }
                    foreach ($post_data["expenses"] as $expense) {
                        $this->voucher_header->reset_fields();
                        $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                        $this->voucher_header->set_field("refNum", $this->auto_generate_rf("EXP"));
                        $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($expense["paidOn"])));
                        $this->voucher_header->set_field("voucherType", "EXP");
                        $this->voucher_header->set_field("referenceNum", $expense["referenceNum"]);
                        $this->voucher_header->set_field("description", $expense["comments"]);
                        $voucher_header_id = "";
                        if ($this->voucher_header->insert()) {
                            $voucher_header_id = $this->voucher_header->get_field("id");
                            if ($this->input->post("case_id")) {
                                $this->voucher_related_case->set_field("legal_case_id", $this->input->post("case_id"));
                                $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                                $this->voucher_related_case->insert();
                                $this->voucher_related_case->reset_fields();
                            }
                            $this->voucher_header->reset_fields();
                            $moneyPreferences = $this->money_preference->get_value_by_key("expenseStatus");
                            $this->expense->reset_fields();
                            $this->expense->set_field("voucher_header_id", $voucher_header_id);
                            $this->expense->set_field("expense_category_id", $expense["expense_category_id"]);
                            $this->expense->set_field("expense_account", $expense["expense_account"]);
                            $this->expense->set_field("paid_through", $this->input->post("paid_through"));
                            $this->expense->set_field("vendor_id", $expense["vendor_id"]);
                            $this->expense->set_field("billingStatus", "internal");
                            if ($this->input->post("billingStatus") != "internal") {
                                $this->expense->set_field("client_account_id", $this->input->post("client_account_id") ? $this->input->post("client_account_id") : NULL);
                                $this->expense->set_field("client_id", $this->input->post("client_id"));
                                $this->expense->set_field("billingStatus", $this->input->post("billingStatus"));
                            }
                            $this->expense->set_field("tax_id", $expense["tax_id"]);
                            $this->expense->set_field("status", $moneyPreferences["keyValue"]);
                            $this->expense->set_field("amount", $expense["amount"]);
                            $this->expense->set_field("paymentMethod", $this->input->post("paymentMethod"));
                            $this->expense->set_field("task", $this->input->post("task"));
                            $this->expense->set_field("hearing", $this->input->post("hearing"));
                            $this->expense->set_field("event", $this->input->post("event"));
                            if ($this->expense->insert()) {
                                $result = true;
                                $upload_key = "file_to_be_uploaded";
                                $_FILES[$upload_key]["name"] = $_FILES["expenses"]["name"][$counter]["uploadDoc"];
                                $_FILES[$upload_key]["type"] = $_FILES["expenses"]["type"][$counter]["uploadDoc"];
                                $_FILES[$upload_key]["tmp_name"] = $_FILES["expenses"]["tmp_name"][$counter]["uploadDoc"];
                                $_FILES[$upload_key]["error"] = $_FILES["expenses"]["error"][$counter]["uploadDoc"];
                                $_FILES[$upload_key]["size"] = $_FILES["expenses"]["size"][$counter]["uploadDoc"];
                                if (!empty($_FILES) && !empty($_FILES[$upload_key])) {
                                    $upload_response = $this->dms->upload_file(["module" => "EXP", "module_record_id" => $voucher_header_id, "upload_key" => $upload_key]);
                                }
                                if ($this->expense->get_field("status") == "approved") {
                                    $expense_id = $this->expense->get_field("id");
                                    $this->expense->reset_fields();
                                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                                    $this->voucher_detail->set_field("account_id", $this->input->post("paid_through"));
                                    $this->voucher_detail->set_field("drCr", "C");
                                    $paid_through_account = $this->account->fetch_account($this->input->post("paid_through"));
                                    $this->voucher_detail->set_field("local_amount", $expense["amount"] * $data["rates"][$paid_through_account["currency_id"]]);
                                    $this->voucher_detail->set_field("foreign_amount", $expense["amount"]);
                                    $this->voucher_detail->set_field("description", "EXP-" . $expense_id);
                                    if ($this->voucher_detail->insert()) {
                                        $expense_local_amount = 0;
                                        $expense_foreign_amount = 0;
                                        $expense_account = $this->account->fetch_account($expense["expense_account"]);
                                        if (!empty($expense["tax_id"])) {
                                            $this->load->model("tax", "taxfactory");
                                            $this->tax = $this->taxfactory->get_instance();
                                            $tax_account = $this->tax->get_tax_account($expense["tax_id"]);
                                            $expense_amount = $expense["amount"] * 100 / ($tax_account["percentage"] + 100);
                                            $tax_amount = $expense["amount"] - $expense_amount;
                                            $tax_local_amount = $tax_amount * $data["rates"][$paid_through_account["currency_id"]];
                                            $tax_foreign_amount = $tax_local_amount / $data["rates"][$tax_account["currency_id"]];
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
                                            $expense_local_amount = $expense["amount"] * $data["rates"][$paid_through_account["currency_id"]];
                                            $expense_foreign_amount = $expense["amount"] * $data["rates"][$paid_through_account["currency_id"]] / $data["rates"][$expense_account["currency_id"]];
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
                                            $this->voucher_related_case->delete(["where" => ["voucher_header_id", $voucher_header_id]]);
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
                            }
                        } else {
                            $result = false;
                        }
                        if ($result && $initial_expense_status == "open") {
                            $this->send_notification_to_groups_users($voucher_header_id, $this->expense->get_field("id"));
                        }
                        $counter++;
                    }
                    if ($result) {
                        $case_currency_id = $this->legal_case->get_money_currency();
                        if ($this->input->post("case_id") && $this->input->post("client_id") && !empty($case_currency_id) && $this->input->post("billingStatus") == "to-invoice" && $this->legal_case->validate_capping_amount($this->input->post("client_id"), $this->input->post("case_id"), $case_currency_id, false, $post_data["total"]) == "warning") {
                            $validation_capping_amount_message = sprintf($this->lang->line("cap_amount_warning"), $this->legal_case->get_field("category") == "Matter" ? $this->lang->line("matter") : $this->lang->line("litigation"));
                            $expense_saved = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("expenses"));
                            $this->set_flashmessage("information", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" . "<li>" . $validation_capping_amount_message . "</li>" : $expense_saved . "<li>" . $validation_capping_amount_message . "</li>");
                        } else {
                            $expense_saved = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("expenses"));
                            $this->set_flashmessage("success", isset($client_added_to_case_message) && $client_added_to_case_message ? $client_added_to_case_message . "<li>" . $expense_saved . "</li>" : $expense_saved);
                        }
                        if (!$this->input->post("create_another") == "yes") {
                            if ($data["isCasePreset"]) {
                                return true;
                            }
                            redirect($this->input->post("referrer"));
                        }
                        $cloned_data = $this->input->post(NULL);
                        redirect($this->input->post("referrer"));
                    }
                } else {
                    $this->set_flashmessage("error", $this->lang->line("missing_uploaded_file_data"));
                    redirect($this->input->post("referrer"));
                }
            }
            $this->load->model("expense_category", "expense_categoryfactory");
            $this->expense_category = $this->expense_categoryfactory->get_instance();
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model("tax", "taxfactory");
            $this->tax = $this->taxfactory->get_instance();
            $data["require_expense_document"] = $systemPreferences["requireExpenseDocument"];
            $data["clients_do_not_match"] = false;
            $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list();
            $active = site_url("vouchers/expense_add");
            $data["tabsNLogs"] = $this->_get_expense_tabs_view_vars($id, $active);
            $data["taxes"] = $this->tax->get_taxes();
            $data["rates"] = json_encode($data["rates"]);
            $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
            $data["expense"]["paymentMethod"] = "Cash";
            $data["paid_through"] = [];
            array_unshift($data["paymentMethod"], "");
            $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
            $data["voucherId"] = $id;
            $data["objName"] = "expense";
            $data["modelName"] = "EXP";
            $data["subModelName"] = "EXP-DOCS";
            $data["systemPreferences"] = $this->session->userdata("systemPreferences");
            $this->includes("money/js/bulk_expenses", "js");
            $this->includes("money/js/common_expenses_form_functions", "js");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("jquery/dropzone", "js");
            $this->includes("money/js/expenses", "js");
            $this->includes("jquery/css/dropzone", "css");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->library("user_agent");
            $data["referrer"] = "dashboard";
            if ($this->agent->is_referral()) {
                $data["referrer"] = $this->agent->referrer();
            }
            if (isset($cloned_data)) {
                $data["expense"] = $this->voucher_header->fetch_expense_details($voucher_header_id);
                $data["expense"] = array_merge($data["expense"], $cloned_data);
                $data["expense"]["expense_category_id"] = "";
                $data["expense"]["attachment"] = "";
                $data["expense"]["amount"] = "";
                $data["expense"]["id"] = "";
                $data["expense"]["description"] = $cloned_data["comments"];
                $data["expense"]["dated"] = $cloned_data["paidOn"];
                unset($data["expense"]["comments"]);
                unset($data["expense"]["paidOn"]);
            }
            $hearing_subject = $data["expense"]["related_hearing"]["subject"];
            $systemPreferences = $this->session->userdata("systemPreferences");
            if ($hearing_subject && isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
                $text = explode(" ", $hearing_subject);
                $hijri_date = gregorianToHijri($text[0], "Y-m-d");
                $data["expense"]["related_hearing"]["subject"] = str_replace($text[0], $hijri_date, $hearing_subject);
            }
            $this->load->view("expenses/bulk_form", $data);
        } else {
            redirect("setup/rate_between_money_currencies");
        }
    }
    private function get_money_languages_available_db()
    {
        $this->load->model("system_preference");
        $money_languages_available_db = [$this->system_preference->get_value_by_key("systemDefaultLanguage")["keyValue"], "fl1" => $this->system_preference->get_value_by_key("systemForeignLanguage_1")["keyValue"], "fl2" => $this->system_preference->get_value_by_key("systemForeignLanguage_2")["keyValue"]];
        return $money_languages_available_db;
    }
    private function fetch_clinet_account($data = [], $filter_key = "")
    {
        if (isset($data["gridSavedFiltersData"]["gridFilters"])) {
            $decoded_filters = json_decode($data["gridSavedFiltersData"]["gridFilters"], true);
            $filters = [];
            if (isset($decoded_filters["filters"])) {
                $filters = $decoded_filters["filters"];
            }
            if (!empty($filters)) {
                foreach ($filters as $index => $filter) {
                    if ($filter["filters"][0]["field"] == $filter_key) {
                        $this->load->model("account", "accountfactory");
                        $this->account = $this->accountfactory->get_instance();
                        $client = $this->account->fetch_account($filter["filters"][0]["value"]);
                        return $client["fullName"];
                    }
                }
            }
        }
        return "";
    }
    public function partner_invoice_export_options($voucher_header_id)
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
        $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
        if ($this->input->get("return") === "html") {
            $data["invoice_templates"] = $this->organization_invoice_template->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["type", "partner"]]], ["value" => "name"]);
            $data["export_options"] = ["word" => "Word", "pdf" => "PDF"];
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $invoice_id = $this->invoice_header->load(["where" => ["voucher_header_id", $voucher_header_id]])["id"];
            $this->load->model("item_commission");
            $parrtners = $this->item_commission->fetch_commissions($invoice_id);
            $temp_parrtners = array_unique(array_column($parrtners, "id"));
            $data["partners"] = array_intersect_key($parrtners, $temp_parrtners);
            $response["html"] = $this->load->view("invoices/partner_export_options", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function partner_invoice_export_to_word($voucher_header_id = 0, $invoice_template_id = 0, $partner_account_id = 0)
    {
        if (0 < $voucher_header_id && !$this->validate_voucher($voucher_header_id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $data = [];
        $this->load->model(["money_preference"]);
        $this->load->model("user_preference");
        $invoice_lang = $this->money_preference->get_values_by_group("PartnerInvLanguage");
        $moneyLanguage = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        foreach ($invoice_lang as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$moneyLanguage];
        }
        $data = $this->fill_template_settings($data, $invoice_template_id, $moneyLanguage);
        $data["is_sample_template"] = $data["template"]["settings"] ? false : true;
        $this->load->model("invoice_detail", "invoice_detailfactory");
        $this->invoice_detail = $this->invoice_detailfactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_id);
        $data["export_header"] = $this->voucher_header->load_partner_invoice_for_template($voucher_header_id, $this->user_preference->get_value("money_language"));
        $data["export_details"] = $this->invoice_detail->load_all_invoice_details_by_partner($partner_account_id, $data["export_header"]["invoice_header_id"], $this->user_preference->get_value("money_language"));
        $payment_due_to = $this->account->fetch_account($partner_account_id);
        $data["export_header"]["payment_due_to"] = $payment_due_to["name"] ?? "";
        $fileName = $data["template"]["name"] . "_" . date("Ymd");
        $this->voucher_fill_common_data_and_download_file("invoices", $data, $fileName, "partner_statement");
    }
    public function partner_statement_export_to_word()
    {
        $voucher_header_ids = $this->input->post("invoices") ?? [];
        $invoice_template_id = $this->input->post("template_id") ?? 0;
        $partner_account_id = $this->input->post("account_id") ?? 0;
        if ($this->is_settlements_per_invoice_enabled()) {
            $this->advice_fee_note_export_to_word($partner_account_id, $voucher_header_ids, $invoice_template_id);
        } else {
            $invoice_header_ids = [];
            if (empty($voucher_header_ids) || $invoice_template_id == 0 || $partner_account_id == 0) {
                $this->set_flashmessage("warning", sprintf($this->lang->line("invalid_request")));
                redirect($this->agent->referrer());
            }
            if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
                $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
            }
            $data = [];
            $this->load->model(["money_preference"]);
            $this->load->model("user_preference");
            $invoice_lang = $this->money_preference->get_values_by_group("PartnerInvLanguage");
            $moneyLanguage = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
            foreach ($invoice_lang as $key => $val) {
                $val = unserialize($val);
                $data["labels"][$key] = $val[$moneyLanguage];
            }
            $data = $this->fill_template_settings($data, $invoice_template_id, $moneyLanguage);
            $data["is_sample_template"] = $data["template"]["settings"] ? false : true;
            $this->load->model("invoice_detail", "invoice_detailfactory");
            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $data["voucher_related_cases"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_ids);
            $data["export_header"] = $this->voucher_header->load_partner_invoice_for_template($voucher_header_ids);
            foreach ($data["export_header"] as $v) {
                $invoice_header_ids[] = $v["invoice_header_id"];
            }
            $data["export_details"] = $this->invoice_detail->load_all_invoice_details_by_partner($partner_account_id, $invoice_header_ids, $this->user_preference->get_value("money_language"));
            $data["export_header"] = 0 < count($data["export_header"]) ? $data["export_header"][0] : [];
            $payment_due_to = $this->account->fetch_account($partner_account_id);
            $data["export_header"]["payment_due_to"] = $payment_due_to["name"] ?? "";
            $data["export_header"]["base_currency"] = $this->session->userdata("organizationCurrency");
            $fileName = $data["template"]["name"] . "_" . date("Ymd");
            $this->voucher_fill_common_data_and_download_file("invoices", $data, $fileName, "partner_statement");
        }
    }
    private function fill_template_settings($data, $template_id, $money_language = NULL, $voucher_type = NULL)
    {
        $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
        $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
        $this->organization_invoice_template->fetch($template_id);
        $data["template"] = $this->organization_invoice_template->get_fields();
        $template_settings = $data["template"]["settings"] ? $data["template"]["settings"] : ($voucher_type == "bill" ? $this->organization_invoice_template->get("default_bill_template_settings") : $this->organization_invoice_template->get("default_template_settings"));
        $data["settings"] = unserialize($template_settings);
        if (empty($data["settings"]["body"]["general"]["line_items"])) {
            $data["settings"] = $this->organization_invoice_template->fix_empty_templates($data["settings"]);
        }
        if (empty($data["settings"]["body"]["general"]["title"])) {
            $invoice_label = unserialize($this->money_preference->get_values_by_group("InvoiceLanguage")["invoice"]);
            $data["settings"]["body"]["general"]["title"] = $invoice_label;
        }
        if (!is_null($money_language)) {
            $data["money_language"] = $money_language;
        }
        return $data;
    }
    public function invoice_export_description_table($invoice_template_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        $data = $this->fill_template_settings($data, $invoice_template_id);
        $show_invoice_description_table = $data["settings"]["body"]["show"]["invoice-description-table"];
        $response["result"] = $show_invoice_description_table;
        if ($show_invoice_description_table) {
            $response["html"] = $this->load->view("invoices/export_options_invoice_description", [], true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function voucher_fill_common_data_and_download_file($path, $data, $fileName, $voucher_type, $format = "docx")
    {
        $is_preview = false;
        if ($format == "preview") {
            $is_preview = true;
            $format = "pdf";
        }
        $time_logs_summary = [];
        $money_language = $this->user_preference->get_value("money_language");
        if ($data["settings"]["body"]["show"]["time-logs-summary-container"] != 0) {
            foreach ($data["export_details"] as $key => &$val) {
                if (!empty($money_language) && (!empty($val["foreign_first_name"]) || !empty($val["foreign_last_name"]))) {
                    $val["item"] = $val["foreign_first_name"] . " " . $val["foreign_last_name"];
                    $val["worker"] = $val["foreign_first_name"] . " " . $val["foreign_last_name"];
                }
                if (!empty($val["time_logs_id"])) {
                    $time_log_index = $val["worker"] . $val["case_id"];
                    if (array_key_exists($time_log_index, $time_logs_summary)) {
                        if ($time_logs_summary[$time_log_index]["unitPrice"] == $val["unitPrice"]) {
                            $time_logs_summary[$time_log_index]["quantity"] = $time_logs_summary[$time_log_index]["quantity"] + $val["quantity"] * 1;
                            if ($voucher_type != "quote") {
                                $time_logs_summary[$time_log_index]["line_sub_total"] += $val["line_sub_total"];
                                $time_logs_summary[$time_log_index]["sub_total_after_line_disc"] += $val["sub_total_after_line_disc"];
                                $time_logs_summary[$time_log_index]["tax_amount"] += $val["tax_amount"];
                                $time_logs_summary[$time_log_index]["total"] += $val["total"];
                            }
                        }
                    } else {
                        $time_logs_summary[$val["worker"] . $val["case_id"]] = $val;
                    }
                }
            }
            unset($val);
        }
        $data["time_logs_summary"] = $time_logs_summary;
        $time_logs = [];
        foreach ($data["export_details"] as $key => &$val) {
            if (!empty($val["time_logs_id"])) {
                if ($data["export_header"]["groupTimeLogsByUserInExport"] == "1") {
                    $time_log_index = $val["worker"] . $val["case_id"];
                    if (array_key_exists($time_log_index, $time_logs)) {
                        if ($time_logs[$time_log_index]["unitPrice"] == $val["unitPrice"] && $time_logs[$time_log_index]["percentage"] == $val["percentage"]) {
                            $time_logs[$time_log_index]["quantity"] = $time_logs[$time_log_index]["quantity"] + $val["quantity"] * 1;
                            $time_logs[$time_log_index]["itemDescription"] .= "\r\n" . (!empty($val["item_time_type"]) ? $val["item_time_type"] . " - " : "") . $val["item_date"] . " - " . $val["quantity"] . (!empty($val["time_log_description"]) ? " - " . $val["time_log_description"] : "");
                            if ($voucher_type != "quote") {
                                $time_logs[$time_log_index]["line_sub_total"] += $val["line_sub_total"];
                                $time_logs[$time_log_index]["sub_total_after_line_disc"] += $val["sub_total_after_line_disc"];
                                $time_logs[$time_log_index]["tax_amount"] += $val["tax_amount"];
                                $time_logs[$time_log_index]["total"] += $val["total"];
                            }
                            unset($data["export_details"][$key]);
                        }
                    } else {
                        $time_logs[$time_log_index] = $val;
                        $time_logs[$time_log_index]["itemDescription"] = (!empty($val["item_time_type"]) ? $val["item_time_type"] . " - " : "") . $val["item_date"] . " - " . $val["quantity"] . (!empty($val["time_log_description"]) ? " - " . $val["time_log_description"] : "");
                        unset($data["export_details"][$key]);
                    }
                } else {
                    if (!empty($data["settings"]["body"]["show"]["time-logs-rebuild-description"])) {
                        if ($voucher_type != "quote") {
                            $data["export_details"][$key]["itemDescription"] = (!empty($val["item_time_type"]) ? $val["item_time_type"] . " - " : "") . (!empty($val["time_log_comments"]) && $val["time_log_comments"] != "null" ? $val["time_log_comments"] : $val["item_date"] . " - " . $val["quantity"]);
                        } else {
                            $data["export_details"][$key]["itemDescription"] = (!empty($val["item_time_type"]) ? $val["item_time_type"] . " - " : "") . (!empty($val["time_log_description"]) && $val["time_log_description"] != "null" ? $val["time_log_description"] : $val["item_date"] . " - " . $val["quantity"]);
                        }
                    }
                }
            }
            if (!empty($money_language) && (!empty($val["foreign_first_name"]) || !empty($val["foreign_last_name"]))) {
                $val["item"] = $val["foreign_first_name"] . " " . $val["foreign_last_name"];
                $val["worker"] = $val["foreign_first_name"] . " " . $val["foreign_last_name"];
            }
        }
        unset($val);
        if (!empty($time_logs)) {
            foreach ($time_logs as $val) {
                array_push($data["export_details"], $val);
            }
            sort($data["export_details"]);
        }
        foreach ($data["export_details"] as $keyINV => $recordINV) {
            $data["export_details"][$keyINV]["quantity"] = number_format($recordINV["quantity"], 2, ".", ",");
        }
        $invoices_items_types = [];
        foreach ($data["export_details"] as $key => $val) {
            if (!empty($val["item_id"])) {
                $invoices_items_types["items"][] = $val["id"];
            } else {
                if (!empty($val["time_logs_id"])) {
                    $invoices_items_types["time_logs"][] = $val["id"];
                } else {
                    if (!empty($val["expense_id"])) {
                        $invoices_items_types["expenses"][] = $val["id"];
                    }
                }
            }
        }
        $data["export_items_types"] = $invoices_items_types;
        $data["path"] = $path;
        $this->load->model("exchange_rate");
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));
        if (!empty($exchange_rates)) {
            $data["rates"] = $exchange_rates;
        }
        if (!empty($data["export_header"])) {
            $data["export_header"]["exchangeRate"] = $data["rates"][$data["export_header"]["currency_id"]];
            $data["export_header"]["show_description_column"] = $data["settings"]["body"]["show"]["invoice-description-column"] ?? "";
        }
        $data["is_debit_note_record"] = !empty($data["export_header"]["voucherType"]) && $data["export_header"]["voucherType"] == "DBN";
        $data["related_invoice_data"] = [];
        if ($data["is_debit_note_record"] && !empty($data["export_header"]["original_invoice_id"])) {
            $data["related_invoice_data"] = $this->voucher_header->load_invoice_by_invoice_id($data["export_header"]["original_invoice_id"]);
        }
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $properties = ["creator" => "Sheria360", "lastModifiedBy" => "Sheria360", "revision" => "1"];
        $docx->addProperties($properties);
        $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400, "marginRight" => 700, "marginLeft" => 700];
        if ($data["settings"]["body"]["show"]["full_width_layout"]) {
            $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400, "marginRight" => 200, "marginLeft" => 200];
        }
        if ($voucher_type == "invoice") {
            $size = $data["settings"]["properties"]["page-orientation"] == "landscape" ? $data["settings"]["properties"]["page-size"] . "-landscape" : $data["settings"]["properties"]["page-size"];
            $docx->modifyPageLayout($size, $layout_options);
            $docx->setDefaultFont($data["settings"]["properties"]["page-font"]);
        } else {
            $docx->modifyPageLayout("letter", $layout_options);
        }
        if (isset($this->session->all_userdata()["AUTH_language"]) && $this->session->all_userdata()["AUTH_language"] == "arabic") {
            $this->load->library("NumToAr", ["number" => "1", "sex" => "male"]);
        }
        $money_language = $this->user_preference->get_value("money_language") === "" ? 0 : $this->user_preference->get_value("money_language");
        $data["direction"] = is_rtl($data["settings"]["body"]["general"]["title"][$money_language]) && $this->session->userdata("moneyLanguage")["money_language"] == "fl2" ? "rtl" : "ltr";
        $data["money_language"] = $money_language;
        if ($voucher_type == "partner_statement") {
            $html = $this->load->view("invoices/partner_export_to_word", $data, true);
            $docx->embedHTML($html, ["downloadImages" => true]);
        } else {
            $data = $this->set_tables_borders_and_sizes($data, $format);
            $data["export_format"] = $format;
            if ($voucher_type == "quote") {
                $html_body_invoice_info = $this->load->view("quotes/export_to_word_body_invoice_info", $data, true);
                $html_body_description_table = $this->load->view("quotes/export_to_word_body_description_table", $data, true);
                $html_body_notes = $this->load->view("quotes/export_to_word_body_notes", $data, true);
                $html_body_invoice_contents = $this->load->view("quotes/export_to_word", $data, true);
            } else {
                $html_body_invoice_info = $this->load->view("invoices/export_to_word_body_invoice_info", $data, true);
                $html_body_description_table = $this->load->view("invoices/export_to_word_body_description_table", $data, true);
                $html_body_notes = $this->load->view("invoices/export_to_word_body_notes", $data, true);
                $html_body_invoice_contents = $this->load->view("invoices/export_to_word", $data, true);
            }
            $docx->embedHTML($html_body_invoice_info);
            $is_notes_added = false;
            if (!empty($data["settings"]["body"]["show"]["invoice-description-table"])) {
                $docx->embedHTML($html_body_description_table);
                if (!empty($data["settings"]["body"]["show"]["notes-container"])) {
                    $docx->embedHTML($html_body_notes);
                    $is_notes_added = true;
                }
                $docx->addBreak(["type" => "page"]);
            }
            $docx->embedHTML($html_body_invoice_contents);
            if (!$is_notes_added) {
                $docx->embedHTML($html_body_notes);
            }
        }
        if (!empty($data["qr_code"])) {
            $image_options = ["src" => $data["qr_code"], "imageAlign" => "center"];
            $docx->addImage($image_options);
        }
        $docx->addHeader(["default" => $this->add_export_header($data, $docx)]);
        $docx->addFooter(["default" => $this->add_export_footer($data, $docx)]);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($tempDirectory)) {
            @mkdir($tempDirectory, 493);
        }
        $docx->createDocx($tempDirectory . DIRECTORY_SEPARATOR . $fileName);
        if ($format == "pdf") {
            if (!empty($data["qr_code"])) {
                unlink($data["qr_code"]);
            }
            $this->attach_invoice_as_pdf($tempDirectory, $fileName, $is_preview);
        } else {
            $this->load->helper("download");
            $content = file_get_contents($tempDirectory . DIRECTORY_SEPARATOR . $fileName . ".docx");
            unlink($tempDirectory . DIRECTORY_SEPARATOR . $fileName . ".docx");
            if (!empty($data["qr_code"])) {
                unlink($data["qr_code"]);
            }
            $filenameEncoded = $this->downloaded_file_name_by_browser($fileName . ".docx");
            force_download($filenameEncoded, $content);
        }
        exit;
    }
    private function attach_invoice_as_pdf($temp_directory, $file_name, $is_preview = false)
    {
        $file_path = $temp_directory . DIRECTORY_SEPARATOR . $file_name . ".pdf";
        $tmp_file = $temp_directory . DIRECTORY_SEPARATOR . $file_name . ".docx";
        $core_path = substr(COREPATH, 0, -12);
        PhpdocxUtilities::parseConfig($core_path . "application/config/phpdocx.ini", true);
        $docx = new CreateDocx($tmp_file);
        $docx->transformDocument($tmp_file, $file_path, "libreoffice");
        $this->load->helper("download");
        $content = file_get_contents($file_path);
        unlink($file_path);
        unlink($tmp_file);
        $file_name_encoded = $this->downloaded_file_name_by_browser($file_name . ".pdf");
        force_download($file_name_encoded, $content, $is_preview, $is_preview);
    }
    public function invoice_partners_settlements($id = NULL)
    {
        if (!$this->is_settlements_per_invoice_enabled() || !$this->is_commissions_enabled() || is_null($id) || $id == 0) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        if (0 < $id && !$this->validate_voucher($id)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("access_denied")));
            redirect("vouchers/invoices_list");
        }
        $this->partners_settlements($id);
    }
    private function partners_settlements($id)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("partners_settlements") . " | " . $this->lang->line("invoices") . " | " . $this->lang->line("money"));
        $data = [];
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();
        $this->load->model("settlement_invoice");
        $this->load->model("item_commission");
        $invoice_id = $this->invoice_header->load(["where" => ["voucher_header_id", $id]])["id"];
        $partners_accounts_ids = array_unique(array_column($this->item_commission->fetch_commissions($invoice_id), "account_id"));
        $payments_settlements_voucher_ids = [$id];
        $this->load->model("invoice_payment_invoice");
        $invoice_payments = array_column($this->invoice_payment_invoice->load_voucher_payments($invoice_id), "voucher_header_id");
        if (!empty($invoice_payments)) {
            array_push($payments_settlements_voucher_ids, ...$invoice_payments);
        }
        $partner_settlements = array_column($this->settlement_invoice->get_invoice_settlements($invoice_id), "voucher_header_id");
        if (!empty($partner_settlements)) {
            array_push($payments_settlements_voucher_ids, ...$partner_settlements);
        }
        $data["partners_accounts"] = $partners_accounts_ids ? $this->account->get_partner_accounts_details($partners_accounts_ids, $payments_settlements_voucher_ids) : NULL;
        $data["invoice_id"] = $invoice_id;
        if (!empty($data["partners_accounts"])) {
            foreach ($data["partners_accounts"] as $key => $account) {
                $data["partners_accounts"][$key]["transactions"] = $this->voucher_detail->load_account_invoice_transactions($account["id"], $payments_settlements_voucher_ids);
            }
        }
        $active = site_url("vouchers/invoice_partners_settlements/");
        $tabsFunc = "_get_invoice_tabs_view_vars";
        $data["tabsNLogs"] = $this->{$tabsFunc}($id, $active);
        $this->includes("money/js/partners_details", "js");
        if ($this->is_auth->is_layout_rtl()) {
            $this->includes("styles/rtl/fixes", "css");
        }
        $this->load->view("partial/header");
        $this->load->view("vouchers/partners_settlements", $data);
        $this->load->view("partial/footer");
    }
    public function delete_settlement($id)
    {
        $response = false;
        if ($this->input->is_ajax_request() && $this->voucher_detail->delete(["where" => ["voucher_header_id", $id]])) {
            $this->load->model("settlement_invoice");
            $this->settlement_invoice->delete(["where" => ["voucher_header_id", $id]]);
            $response = true;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function prepare_commissions_data($data, $item_commissions_data_array, $voucher_header_id, $invoice_voucher, $client_account, $payment_amount)
    {
        $commission_accounts = [];
        if ($this->is_commissions_enabled() && !empty($item_commissions_data_array)) {
            $allowed_decimal_format = $this->config->item("allowed_decimal_format");
            $system_partner_commission_asset_account = false;
            $system_preferences = $this->session->userdata("systemPreferences");
            $system_commission_account = isset($system_preferences["systemCommissionAccount"]) ? unserialize($system_preferences["systemCommissionAccount"]) : false;
            if ($system_commission_account && isset($system_commission_account[$this->session->userdata("organizationID")])) {
                $system_partner_commission_asset_account = $system_commission_account[$this->session->userdata("organizationID")];
            }
            $money_language = $this->user_preference->get_value("money_language");
            $this->load->model("invoice_detail", "invoice_detailfactory");
            $this->invoice_detail = $this->invoice_detailfactory->get_instance();
            $this->load->model("invoice_header", "invoice_headerfactory");
            $this->invoice_header = $this->invoice_headerfactory->get_instance();
            $invoice_details = $this->invoice_detail->load_invoice_details($invoice_voucher["invoice_id"], $money_language);
            $system_partner_commission_asset_account_data = ["voucher_header_id" => $voucher_header_id, "account_id" => $system_partner_commission_asset_account, "drCr" => "D", "local_amount" => "", "foreign_amount" => "", "description" => $this->lang->line("partners_shares") . " - " . $this->invoice_header->get_field("prefix") . $invoice_voucher["refNum"] . $this->invoice_header->get_field("suffix")];
            $invoice_header_data = [];
            $invoice_header_data["activate_discount"] = $invoice_voucher["displayDiscount"];
            $invoice_header_data["discount_percentage"] = $invoice_voucher["discount_percentage"];
            $invoice_header_data["discount_amount"] = $invoice_voucher["discount_amount"];
            $invoice_header_data["discount_type"] = $invoice_voucher["discount_value_type"];
            $invoice_total = $invoice_voucher["lines_total_subtotal"] - $invoice_voucher["lines_total_discount"];
            $invoice_total_taxes = $invoice_voucher["lines_total_tax"];
            $invoice_total_taxes_percentage = $invoice_total_taxes / $invoice_voucher["total"] * 100;
            $total_local_amount = 0;
            $total_foreign_amount = 0;
            foreach ($invoice_details as $invoice_details_value) {
                foreach ($item_commissions_data_array as $item_commissions_value) {
                    if ($item_commissions_value["invoice_details_id"] == $invoice_details_value["id"] && !empty($item_commissions_value["account_id"])) {
                        if (!in_array($item_commissions_value["account_id"], array_column($commission_accounts, "account_id"))) {
                            $current_partner_commissions = array_filter($item_commissions_data_array, function ($item) {
                                return $item["account_id"] === $item_commissions_value["account_id"];
                            });
                            $current_partner_total_commissions = $this->calculate_partner_total_commissions($current_partner_commissions, $invoice_details, $invoice_header_data);
                            $commission_percentage = $current_partner_total_commissions / $invoice_total * 100;
                            $payment_without_tax = $payment_amount - $payment_amount * $invoice_total_taxes_percentage / 100;
                            $commission_amount = $payment_without_tax * $data["rates"][$client_account["currency_id"]] * $commission_percentage / 100;
                            $item_partner_share_account = $this->account->fetch_account($item_commissions_value["account_id"]);
                            $local_amount = number_format($commission_amount, $allowed_decimal_format, ".", "");
                            $foreign_amount = number_format($commission_amount / $data["rates"][$item_partner_share_account["currency_id"]], $allowed_decimal_format, ".", "");
                            $total_local_amount += $local_amount;
                            $total_foreign_amount += $local_amount;
                            $commission_accounts[] = ["voucher_header_id" => $voucher_header_id, "account_id" => $item_commissions_value["account_id"], "drCr" => "C", "local_amount" => (double) $local_amount, "foreign_amount" => (double) $foreign_amount, "description" => $this->lang->line("partners_shares") . " - " . $this->invoice_header->get_field("prefix") . $invoice_voucher["refNum"] . $this->invoice_header->get_field("suffix")];
                        }
                    }
                }
            }
            $system_partner_commission_asset_account_data["local_amount"] = $total_local_amount;
            $global_partner_share_account = $this->account->fetch_account($system_partner_commission_asset_account);
            $system_partner_commission_asset_account_data["foreign_amount"] = 0;
            if (0 < $data["rates"][$global_partner_share_account["currency_id"]]) {
                $system_partner_commission_asset_account_data["foreign_amount"] = $total_foreign_amount / $data["rates"][$global_partner_share_account["currency_id"]];
            }
            $commission_accounts[] = $system_partner_commission_asset_account_data;
        }
        return $commission_accounts;
    }
    private function is_commissions_enabled()
    {
        $systemPreferences = $this->session->userdata("systemPreferences");
        if (!empty($systemPreferences["partnersCommissions"])) {
            $partnersCommissions = unserialize($systemPreferences["partnersCommissions"]);
            if (isset($partnersCommissions[$this->session->userdata("organizationID")]) && !empty($partnersCommissions[$this->session->userdata("organizationID")]) && $partnersCommissions[$this->session->userdata("organizationID")] == "yes") {
                return true;
            }
        }
        return false;
    }
    private function is_partners_share_added($voucher_id)
    {
        $voucher_records = $this->voucher_detail->load_details_with_accounts($voucher_id);
        foreach ($voucher_records as $record) {
            if ($record["model_type"] == "partner") {
                return true;
            }
        }
        return false;
    }
    private function is_settlements_per_invoice_enabled()
    {
        $this->load->model(["money_preference"]);
        $settlements_per_invoice = $this->money_preference->get_values_by_group("PartnerSettlementsPerInvoice");
        if (is_array($settlements_per_invoice) && 0 < count($settlements_per_invoice)) {
            return array_values($settlements_per_invoice)[0];
        }
        return false;
    }
    private function calculate_partner_total_commissions($partner_commissions, $invoice_details, $invoice_header)
    {
        $current_partner_total_commissions = 0;
        foreach ($partner_commissions as $commission) {
            $commission_item_index = array_search($commission["invoice_details_id"], array_column($invoice_details, "id"));
            $commission_item = $invoice_details[$commission_item_index];
            $commissionAmount = $commission_item["sub_total_after_line_disc"];
            $current_partner_total_commissions += $commissionAmount * $commission["commission"] / 100;
        }
        return $current_partner_total_commissions;
    }
    public function bill_export_options()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = $response = [];
        if ($this->input->get("return") === "html") {
            $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
            $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
            $data["templates"] = $this->organization_invoice_template->load_list(["where" => [["organization_id", $this->session->userdata("organizationID")], ["type", "bill"]]], ["value" => "name"]);
            $response["html"] = $this->load->view("bills/bill_export_options", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function bill_export_to_word($bill_id, $voucher_header_id, $template_id)
    {
        $data = [];
        $this->load->model(["money_preference"]);
        $this->load->model("user_preference");
        $this->load->model("bill_details", "bill_detailsfactory");
        $this->bill_details = $this->bill_detailsfactory->get_instance();
        $data["allowed_decimal_format"] = $this->config->item("allowed_decimal_format");
        $data["bill_header"] = $this->voucher_header->fetch_bill_voucher($voucher_header_id);
        $data["bill_details"] = $this->bill_details->fetch_bill_details($bill_id);
        $invoice_totals = $this->calculate_bill_sub_total_and_tax($data["bill_details"]);
        $data["bill_header"]["sub_total"] = $invoice_totals["sub_total"];
        $data["bill_header"]["total_tax"] = $invoice_totals["total_tax"];
        $bill_language = $this->money_preference->get_values_by_group("BillLanguage");
        $money_language = $this->user_preference->get_value("money_language");
        $money_language_index = $money_language == "" ? 0 : $money_language;
        foreach ($bill_language as $key => $val) {
            $val = unserialize($val);
            $data["labels"][$key] = $val[$money_language_index];
        }
        $data = $this->fill_template_settings($data, $template_id, $money_language_index, "bill");
        $fileName = $data["template"]["name"] . "_" . date("Ymd");
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $properties = ["creator" => "Sheria360", "lastModifiedBy" => "Sheria360", "revision" => "1"];
        $docx->addProperties($properties);
        $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400];
        $docx->modifyPageLayout("letter", $layout_options);
        $data["direction"] = is_rtl($data["settings"]["body"]["general"]["title"][$money_language_index]) ? "rtl" : "ltr";
        $data["money_language"] = $money_language_index;
        $data["bill_related_matter"] = $this->voucher_related_case->load_voucher_related_cases($voucher_header_id);
        $data["currency"] = $this->session->userdata("organizationCurrency");
        $html = $this->load->view("bills/bill_export_to_word", $data, true);
        $docx->embedHTML($html, ["downloadImages" => true]);
        $docx->addHeader(["default" => $this->add_export_header($data, $docx)]);
        $docx->addFooter(["default" => $this->add_export_footer($data, $docx)]);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($tempDirectory)) {
            @mkdir($tempDirectory, 493);
        }
        $docx->createDocx($tempDirectory . "/" . $fileName);
        $this->load->helper("download");
        $content = file_get_contents($tempDirectory . "/" . $fileName . ".docx");
        unlink($tempDirectory . "/" . $fileName . ".docx");
        $filenameEncoded = $this->downloaded_file_name_by_browser($fileName . ".docx");
        force_download($filenameEncoded, $content);
        exit;
    }
    public function calculate_bill_sub_total_and_tax($bill_details)
    {
        $sub_total = 0;
        $total_tax = 0;
        foreach ($bill_details as $item) {
            $sub_total += $item["amount"];
            $total_tax += $item["amount"] * $item["percentage"] / 100;
        }
        return ["sub_total" => $sub_total, "total_tax" => $total_tax];
    }
    public function view_document($id = 0)
    {
        $response = [];
        if (0 < $id) {
            echo $this->dms->get_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dms->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("modules/money/vouchers/view_document/" . $id);
            if (!empty($response["document"]["extension"]) && in_array($response["document"]["extension"], $this->document_management_system->image_types)) {
                $response["iframe_content"] = $this->load->view("documents_management_system/view_image_document", ["url" => $response["document"]["url"]], true);
            }
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", [], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_credit_note($credit_note_id)
    {
        $this->save_credit_note($credit_note_id, true);
    }
    public function save_credit_note($id, $is_edit = false)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("credit_note") . " | " . $this->lang->line("money"));
        $this->load->model("exchange_rate");
        $this->load->model("user_preference");
        $this->load->model("money_preference");
        $money_preference = $this->money_preference->get_key_groups();
        $system_preferences = $this->session->userdata("systemPreferences");
        $organization_id = $this->session->userdata("organizationID");
        $this->check_if_allowed_to_create($system_preferences, $money_preference, $organization_id);
        if ($is_edit) {
            $data = $this->prepare_credit_note_data($id, NULL, $money_preference, $organization_id);
        } else {
            $data = $this->prepare_credit_note_data(NULL, $id, $money_preference, $organization_id);
        }
        $this->load_credit_note_libraries();
        $this->load->view("credit_notes/credit_note_form", $data);
    }
    private function check_if_allowed_to_create($system_preferences, $money_preference, $organization_id)
    {
        $exchange_rates = $this->exchange_rate->get_organization_exchange_rates($organization_id);
        if (empty($exchange_rates)) {
            $this->set_flashmessage("warning", $this->lang->line("set_default_exchange_rate"));
            redirect("setup/rate_between_money_currencies");
        }
        $time_tracking_sales_account_arr = unserialize($system_preferences["timeTrackingSalesAccount"]);
        $time_tracking_sales_account = $time_tracking_sales_account_arr[$organization_id];
        if (!$time_tracking_sales_account || empty($time_tracking_sales_account)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("you_have_to_set_time_tracking_sales_account")));
            redirect("setup/time_tracking_sales_account");
        }
        $activate_discount = unserialize($money_preference["ActivateDiscountinInvoices"]["DEnabled"])[$organization_id]["enabled"];
        if (!isset($activate_discount)) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("set_invoice_discount")));
            redirect("setup/configure_invoice_discount");
        }
        $partners_commissions = unserialize($system_preferences["partnersCommissions"]);
        if (isset($partners_commissions[$organization_id]) && !empty($partners_commissions[$organization_id])) {
            $system_commission_account = false;
            $system_partner_commission_asset_account = false;
            if ($partners_commissions[$organization_id] == "yes") {
                $system_commission_account = isset($system_preferences["systemCommissionAccount"]) ? unserialize($system_preferences["systemCommissionAccount"]) : false;
                if ($system_commission_account && isset($system_commission_account[$organization_id])) {
                    $system_partner_commission_asset_account = $system_commission_account[$organization_id];
                }
                if (!$system_partner_commission_asset_account || empty($system_partner_commission_asset_account)) {
                    $this->set_flashmessage("warning", sprintf($this->lang->line("you_have_to_set_the_default_global_partner_shares_account")));
                    redirect("setup/global_partner_shares_account");
                }
            }
        }
    }
    private function prepare_credit_note_data($credit_note_id, $invoice_id, $money_preference, $organization_id)
    {
        $data = [];
        $front_end_data = [];
        $this->load->helper("encrypt_decrypt_helper");
        $this->load->model("tax", "taxfactory");
        $this->tax = $this->taxfactory->get_instance();
        $this->load->model("discount", "discountfactory");
        $this->discount = $this->discountfactory->get_instance();
        $this->load->model("credit_note_reason", "credit_note_reason_factory");
        $this->credit_note_reason = $this->credit_note_reason_factory->get_instance();
        $this->load->model("term");
        $this->load->model("invoice_note");
        $this->load->model("invoice_header", "invoice_headerfactory");
        $this->load->model("organization_invoice_template", "organization_invoice_templatefactory");
        $this->load->model("country", "countryfactory");
        $this->load->model("invoice_transaction_type", "invoice_transaction_typefactory");
        $this->country = $this->countryfactory->get_instance();
        $this->invoice_transaction_type = $this->invoice_transaction_typefactory->get_instance();
        $this->organization_invoice_template = $this->organization_invoice_templatefactory->get_instance();
        $this->invoice_header = $this->invoice_headerfactory->get_instance();
        $money_language = $this->user_preference->get_value("money_language");
        $money_language_key = $money_language === "" ? 0 : $money_language;
        $this->term->set("_listFieldName", $money_language . "name");
        $credit_note_lang = $this->money_preference->get_values_by_group("InvoiceLanguage");
        $terms = $this->term->load_list([], ["id" => "id", "value" => $money_language . "name"]);
        foreach ($credit_note_lang as $key => $val) {
            $val = unserialize($val);
            $front_end_data["labels"][$key] = $val[$money_language_key];
        }
        foreach ($terms as $term_id => $term_value) {
            $termObject = (object) ["value" => $term_id, "viewValue" => $term_value];
            $front_end_data["terms"][] = $termObject;
        }
        $system_preferences = $this->session->userdata("systemPreferences");
        $partners_commissions = unserialize($system_preferences["partnersCommissions"]);
        if (isset($partners_commissions[$organization_id]) && !empty($partners_commissions[$organization_id])) {
            $commissions = $partners_commissions[$organization_id];
        }
        $front_end_data["is_settlements_per_invoice_enabled"] = $this->is_settlements_per_invoice_enabled();
        $front_end_data["is_e_invoicing_enabled"] = $this->organization->check_if_einvoice_active($this->session->userdata("organizationID"));
        $front_end_data["partners_commissions"] = $commissions ?? false;
        $front_end_data["credit_note_id"] = $credit_note_id;
        $front_end_data["invoice_id"] = $invoice_id;
        $front_end_data["activate_tax"] = $money_preference["ActivateTaxesinInvoices"]["TEnabled"];
        $front_end_data["activate_discount"] = unserialize($money_preference["ActivateDiscountinInvoices"]["DEnabled"])[$organization_id]["enabled"];
        $front_end_data["display_item_date"] = $money_preference["InvoiceItems"]["DisplayItemDate"];
        $front_end_data["notes_descriptions"] = $this->invoice_note->load_all();
        $front_end_data["credit_note_reasons"] = $this->credit_note_reason->get_credit_note_reason_dropdown();
        $front_end_data["discounts"] = $this->discount->get_discounts();
        $front_end_data["rates"] = $this->exchange_rate->get_organization_exchange_rates($organization_id);
        $front_end_data["money_language"] = $money_language;
        $front_end_data["countries"] = $this->country->load_countries_list();
        $front_end_data["transaction_types"] = $this->invoice_transaction_type->get_invoice_transaction_type_dropdown();
        $front_end_data["organization_id"] = $this->session->userdata("organizationID");
        $front_end_data["organization_currency"] = $this->session->userdata("organizationCurrency");
        $front_end_data["organization_currency_id"] = $this->session->userdata("organizationCurrencyID");
        $front_end_data["user_id"] = $this->session->userdata("AUTH_user_id");
        $front_end_data["site_url"] = site_url();
        $front_end_data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
        $front_end_data["templates"] = $this->organization_invoice_template->get_templates_list("invoice", $organization_id);
        $front_end_data["is_rtl"] = $this->is_auth->is_layout_rtl();
        $front_end_data["e_invoicing"] = $this->organization->check_if_einvoice_active($this->session->userdata("organizationID"));
        $data["front_end_data"] = json_encode($front_end_data);
        return $data;
    }
    private function load_credit_note_libraries()
    {
        $this->includes("vue/node_modules/primevue/resources/primevue.min", "css");
        $this->includes("vue/node_modules/primevue/resources/themes/saga-blue/theme", "css");
        $this->includes("vue/node_modules/primeicons/primeicons", "css");
        $this->includes("scss/primeflex@3.1.0", "css");
        $this->includes("jquery/timemask", "js");
        $this->includes("vue/node_modules/vue/dist/vue.global", "js");
        $this->includes("vue/node_modules/axios/dist/axios.min", "js");
        $this->includes("vue/node_modules/primevue/api/api.min", "js");
        $this->includes("vue/node_modules/primevue/config/config.min", "js");
        $this->includes("vue/node_modules/primevue/utils/utils.min", "js");
        $this->includes("vue/node_modules/primevue/ripple/ripple.min", "js");
        $this->includes("vue/node_modules/primevue/core/core.min", "js");
        $this->includes("vue/node_modules/primevue/fileupload/fileupload.min", "js");
        $this->includes("vue/node_modules/primevue/button/button.min", "js");
        $this->includes("vue/node_modules/primevue/autocomplete/autocomplete.min", "js");
        $this->includes("vue/node_modules/primevue/dialog/dialog.min", "js");
        $this->includes("vue/node_modules/primevue/inputtext/inputtext.min", "js");
        $this->includes("vue/node_modules/primevue/breadcrumb/breadcrumb.min", "js");
        $this->includes("vue/node_modules/primevue/menu/menu.min", "js");
        $this->includes("vue/node_modules/primevue/dropdown/dropdown.min", "js");
        $this->includes("vue/node_modules/primevue/calendar/calendar.min", "js");
        $this->includes("vue/node_modules/primevue/fieldset/fieldset.min", "js");
        $this->includes("vue/node_modules/primevue/divider/divider.min", "js");
        $this->includes("vue/node_modules/primevue/fileupload/fileupload.min", "js");
        $this->includes("vue/node_modules/primevue/textarea/textarea.min", "js");
        $this->includes("vue/node_modules/primevue/blockui/blockui.min", "js");
        $this->includes("vue/node_modules/primevue/card/card.min", "js");
        $this->includes("vue/node_modules/primevue/datatable/datatable.min", "js");
        $this->includes("vue/node_modules/primevue/column/column.min", "js");
        $this->includes("vue/node_modules/primevue/inputnumber/inputnumber.min", "js");
        $this->includes("vue/node_modules/primevue/toast/toast.min", "js");
        $this->includes("vue/node_modules/primevue/toastservice/toastservice.min", "js");
        $this->includes("vue/node_modules/primevue/selectbutton/selectbutton.min", "js");
        $this->includes("vue/node_modules/primevue/badge/badge.min", "js");
        $this->includes("vue/node_modules/@ckeditor/ckeditor5-build-classic/build/ckeditor", "js");
        $this->includes("vue/node_modules/@ckeditor/ckeditor5-build-classic/build/translations/ar", "js");
        $this->includes("vue/node_modules/@ckeditor/ckeditor5-vue/dist/ckeditor", "js");
        $this->includes("vue/node_modules/primevue/confirmdialog/confirmdialog.min", "js");
        $this->includes("vue/node_modules/primevue/confirmationservice/confirmationservice.min", "js");
    }
    public function credit_notes()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("credit_notes") . " | " . $this->lang->line("money"));
        $this->load->helper("encrypt_decrypt_helper");
        $data["token"] = encrypt_string($this->session->userdata("AUTH_email_address"));
        $this->includes("vue/node_modules/primevue/resources/primevue.min", "css");
        $this->includes("vue/node_modules/primevue/resources/themes/saga-blue/theme", "css");
        $this->includes("vue/node_modules/primeicons/primeicons", "css");
        $this->includes("scss/primeflex@3.1.0", "css");
        $this->includes("vue/node_modules/vue/dist/vue.global.prod", "js");
        $this->includes("vue/node_modules/axios/dist/axios.min", "js");
        $this->includes("vue/node_modules/primevue/utils/utils.min", "js");
        $this->includes("vue/node_modules/primevue/api/api", "js");
        $this->includes("vue/node_modules/primevue/config/config.min", "js");
        $this->includes("vue/node_modules/primevue/ripple/ripple.min", "js");
        $this->includes("vue/node_modules/primevue/tooltip/tooltip.min", "js");
        $this->includes("vue/node_modules/primevue/core/core.min", "js");
        $this->includes("vue/node_modules/primevue/confirmdialog/confirmdialog.min", "js");
        $this->includes("vue/node_modules/primevue/confirmationservice/confirmationservice.min", "js");
        $this->includes("vue/node_modules/primevue/toast/toast.min", "js");
        $this->includes("vue/node_modules/primevue/datatable/datatable.min", "js");
        $this->includes("vue/node_modules/primevue/column/column.min", "js");
        $this->includes("vue/node_modules/primevue/dropdown/dropdown.min", "js");
        $this->includes("vue/node_modules/primevue/multiselect/multiselect.min", "js");
        $this->includes("vue/node_modules/primevue/calendar/calendar.min", "js");
        $this->includes("vue/node_modules/primevue/badge/badge.min", "js");
        $this->includes("vue/node_modules/primevue/toastservice/toastservice.min", "js");
        $this->load->view("partial/header");
        $this->load->view("credit_notes/index", $data);
    }
    public function credit_note_export_to_word($credit_note_id, $template_id, $file_type)
    {
        $data = [];
        $this->load->model("money_preference");
        $this->load->model("user_preference");
        $this->load->model("credit_note_related_case");
        $this->load->model("credit_note_header");
        $this->load->model("credit_note_detail");
        $this->load->model("term");
        $this->load->model("organization", "organizationfactory");
        $this->organization = $this->organizationfactory->get_instance();
        $invoice_language = $this->money_preference->get_values_by_group("InvoiceLanguage");
        $money_language = $this->user_preference->get_value("money_language");
        $money_language_index = $money_language == "" ? 0 : $money_language;
        foreach ($invoice_language as $key => $value) {
            $value = unserialize($value);
            $data["labels"][$key] = $value[$money_language_index];
        }
        $terms = $this->term->load_list([], ["id" => "id", "value" => $money_language . "name"]);
        $data = $this->fill_template_settings($data, $template_id, $money_language_index);
        $data["export_format"] = $file_type;
        $data["is_sample_template"] = $data["template"]["settings"] ? false : true;
        $data["credit_note_header_data"] = $this->credit_note_header->get_credit_note_header_data($credit_note_id);
        $data["credit_note_header_data"]["term_title"] = $terms[$data["credit_note_header_data"]["term_id"]];
        $data["credit_note_related_cases"] = $this->credit_note_related_case->load_credit_note_related_cases($credit_note_id);
        $data["credit_note_details_data"] = $this->credit_note_detail->load_credit_note_details($credit_note_id);
        $data["e_invoicing"] = $this->organization->check_if_einvoice_active($this->session->userdata("organizationID"));
        $data["money_language"] = $money_language_index;
        $data["qr_code"] = $data["e_invoicing"] ? $this->generate_qr_code($data["credit_note_header_data"]["credit_note_header_id"], "credit_note") : NULL;
        $data["direction"] = is_rtl($data["labels"]["credit_note"]) && $this->session->userdata("moneyLanguage")["money_language"] == "fl2" ? "rtl" : "ltr";
        $file_name = $data["credit_note_header_data"]["credit_note_number_full"] . "_" . date("Ymd");
        $data = $this->set_tables_borders_and_sizes($data, $file_type);
        $time_logs = [];
        if (!empty($time_logs)) {
            foreach ($time_logs as $val) {
                array_push($data["credit_note_details_data"], $val);
            }
            sort($data["credit_note_details_data"]);
        }
        foreach ($data["credit_note_details_data"] as $key => $record) {
            $data["credit_note_details_data"][$key]["quantity"] = number_format($record["quantity"], 2, ".", ",");
        }
        $credit_note_items_types = [];
        foreach ($data["credit_note_details_data"] as $key => $val) {
            if (!empty($val["item_id"])) {
                $credit_note_items_types["items"][] = $val["id"];
            } else {
                if (!empty($val["expense_id"])) {
                    $credit_note_items_types["expenses"][] = $val["id"];
                } else {
                    $credit_note_items_types["time_logs"][] = $val["id"];
                }
            }
        }
        $data["export_items_types"] = $credit_note_items_types;
        $corepath = substr(COREPATH, 0, -12);
        require_once $corepath . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
        $docx = new createDocx();
        $properties = ["creator" => "Sheria360", "lastModifiedBy" => "Sheria360", "revision" => "1"];
        $docx->addProperties($properties);
        $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400, "marginRight" => 700, "marginLeft" => 700];
        if ($data["settings"]["body"]["show"]["full_width_layout"]) {
            $layout_options = ["marginTop" => ($data["settings"]["body"]["css"]["margin-top"] ? $data["settings"]["body"]["css"]["margin-top"] * 4 : 0) * 1440, "marginHeader" => 400, "marginRight" => 200, "marginLeft" => 200];
        }
        $size = $data["settings"]["properties"]["page-orientation"] == "landscape" ? $data["settings"]["properties"]["page-size"] . "-landscape" : $data["settings"]["properties"]["page-size"];
        $docx->modifyPageLayout($size, $layout_options);
        $docx->setDefaultFont($data["settings"]["properties"]["page-font"]);
        $credit_note_export_view = $this->load->view("credit_notes/export_to_word_body", $data, true);
        $docx->embedHTML($credit_note_export_view);
        if (!empty($data["qr_code"])) {
            $image_options = ["src" => $data["qr_code"], "imageAlign" => "center"];
            $docx->addImage($image_options);
        }
        $docx->addHeader(["default" => $this->add_export_header($data, $docx)]);
        $docx->addFooter(["default" => $this->add_export_footer($data, $docx)]);
        $tempDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . "usr_" . $this->is_auth->get_user_id();
        if (!is_dir($tempDirectory)) {
            @mkdir($tempDirectory, 493);
        }
        $docx->createDocx($tempDirectory . DIRECTORY_SEPARATOR . $file_name);
        if ($file_type == "pdf") {
            if (!empty($data["qr_code"])) {
                unlink($data["qr_code"]);
            }
            $this->attach_invoice_as_pdf($tempDirectory, $file_name);
        } else {
            $this->load->helper("download");
            $content = file_get_contents($tempDirectory . DIRECTORY_SEPARATOR . $file_name . ".docx");
            unlink($tempDirectory . DIRECTORY_SEPARATOR . $file_name . ".docx");
            if (!empty($data["qr_code"])) {
                unlink($data["qr_code"]);
            }
            $filenameEncoded = $this->downloaded_file_name_by_browser($file_name . ".docx");
            force_download($filenameEncoded, $content);
        }
        exit;
    }
    private function set_tables_borders_and_sizes($data, $format)
    {
        $font_size_difference = 0;
        if ($format == "pdf") {
            $font_size_difference = 1;
        }
        $tables_borders_style = NULL;
        switch ($data["settings"]["body"]["css"]["tables-borders"]) {
            case "none":
                $tables_borders_style = "table-border-none";
                break;
            case "rows":
                $tables_borders_style = "table-border-row";
                break;
            case "columns":
                $tables_borders_style = "table-border-column";
                break;
            case "both":
                $tables_borders_style = "table-bordered";
                break;
            default:
                $this->write_log ("Unexpected tables-borders value: " . $data["settings"]["body"]["css"]["tables-borders"],"error");
                break;
        }
                $tables_borders_style = "table-bordered";
                $data["tables_borders_style"] = $tables_borders_style;
                $data["invoice_information_font_size"] = $data["settings"]["body"]["css"]["invoice-information-font-size"] - $font_size_difference;
                $data["invoice_tables_font_size"] = $data["settings"]["body"]["css"]["invoice-tables-font-size"] - $font_size_difference;
                $data["invoice_summation_font_size"] = $data["settings"]["body"]["css"]["invoice-summation-font-size"] - $font_size_difference;
                $data["invoice_notes_font_size"] = $data["settings"]["body"]["css"]["invoice-notes-font-size"] - $font_size_difference;
                return $data;
    }
    public function credit_notes_export_to_excel()
    {
        $columns = $this->input->get("exported_columns");
        if (empty($columns)) {
            redirect("vouchers/credit_notes");
        }
        $data = [];
        $money_language = $this->user_preference->get_value("money_language");
        $this->load->model("credit_note_header");
        $data["columns"] = explode("-", $columns);
        $data["credit_notes_list"] = $this->credit_note_header->get_credit_notes_list($this->session->userdata("organizationID"), $money_language);
        $file_name = $this->lang->line("credit_notes");
        $sheets_names = $this->lang->line("credit_notes");
        $tables_views = $this->load->view("credit_notes/credit_notes_list", $data, true);
        $this->load->helper("export_xlsx_helper");
        export_tables_to_excel($file_name, $sheets_names, $tables_views);
    }
    public function new_expense_default_values(){
        $data=[];
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        $this->load->model("account", "accountfactory");
        $this->load->model("tax", "taxfactory");
        $this->tax = $this->taxfactory->get_instance();
        $this->load->model("expense_category", "expense_categoryfactory");
        $this->expense_category = $this->expense_categoryfactory->get_instance();
        
        $data["rates"] = json_encode($data["rates"]);//exchange rates
        $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
        array_unshift($data["paymentMethod"], "");
        $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
        $data["taxes"] = $this->tax->get_taxes();
      // $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list();
       $data["paid_through"] = $this->get_expense_accounts_by_type($data["expense"]["paymentMethod"]);

     //  exit(json_encode($data));
        $this->load->view("partial/header");
        $this->load->view("setup/new_expense_default_values", $data);
        $this->load->view("partial/footer");
    }
    public function expenseQuickAdd(){

        $this->load->model("expense_category", "expense_categoryfactory");
        $this->expense_category = $this->expense_categoryfactory->get_instance();
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();
        if($this->input->post(null)){ exit(json_encode($this->input->post(null)));
            $this->expense_save();
        }else {
            $data["title"] = $this->lang->line("expense");
        $data["caseId"] = 0;
        $data["expense_categories"] = $this->expense_category->load_expense_category_accounts_list();
        $data["paymentMethod"] = $this->expense->get("paymentMethodValues");
        array_unshift($data["paymentMethod"], "");
        $data["paymentMethod"] = array_combine($data["paymentMethod"], [$this->lang->line("choose_one"), $this->lang->line("cash"), $this->lang->line("credit_card"), $this->lang->line("cheque_and_bank"), $this->lang->line("online_payment"), $this->lang->line("other")]);
        $data["currencies"] = [];
        $data["external_counsels"] = [];

        //exit(json_encode($this->input->post(null)));
        $response["html"] = $this->load->view("expenses/expenses_quick_add", $data, true);
        $response["status"] = true;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    private function expense_save($id = 0, $extraData = [])
{
    $this->load->model("expense", "expensefactory");
    $this->expense = $this->expensefactory->get_instance();

    $this->load->model("account", "accountfactory");
    $this->account = $this->accountfactory->get_instance();

    $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title_format"), $this->lang->line("expenses"));

    $formData = [];
    if ($id > 0) {
        // EDIT MODE
        $voucher = $this->voucher->get($id);
        if (!$voucher) {
            show_error("Invalid expense");
        }

        $formData = $this->prepare_edit_mode_data($id, $voucher);
        $this->includes("money/js/expense_edit_form", "js"); // optional if you have JS specific to edit mode
    }

    $data = [
        "accounts" => $this->account->get_cash_and_bank_accounts(),
        "payment_methods" => $this->payment_method->get_all(),
        "categories" => $this->expense_category->get_all(),
        "vendors" => $this->vendor->get_all(),
        "clients" => $this->client->get_all(),
        "taxes" => $this->tax->get_all(),
    ];

    $data = array_merge($data, $formData, $extraData);

    if ($this->input->post()) {
        $this->load->library("form_validation");
        $this->form_validation->set_rules("amount", "Amount", "required|numeric");

        if ($this->form_validation->run()) {
            $voucherData = [
                "expense_category_id" => $this->input->post("expense_category_id"),
                "paid_through_account_id" => $this->input->post("paid_through"),
                "payment_method" => $this->input->post("paymentMethod"),
                "dated" => $this->input->post("dated"),
                "description" => $this->input->post("description"),
                "reference_number" => $this->input->post("referenceNum"),
                "vendor_id" => $this->input->post("vendor_id"),
                "client_id" => $this->input->post("client_id"),
                "tax_id" => $this->input->post("tax_id"),
                "related_hearing" => $this->input->post("related_hearing"),
                "related_task" => $this->input->post("related_task"),
                "related_event" => $this->input->post("related_event"),
            ];

            $voucher_header_id = $this->voucher->save($voucherData, $id);

            if ($voucher_header_id) {
                $voucher_detail = [
                    "voucher_id" => $voucher_header_id,
                    "account_id" => $this->input->post("expense_account"),
                    "amount" => $this->input->post("amount"),
                ];
                $this->voucherDetail->save($voucher_detail);

                if ($this->input->post("case_id")) {
                    $voucher_case = [
                        "voucher_id" => $voucher_header_id,
                        "case_id" => $this->input->post("case_id"),
                        "case_subject" => $this->input->post("case_subject"),
                        "case_category" => $this->input->post("case_category"),
                    ];
                    $this->voucherCase->save($voucher_case);
                }

                $this->handle_document_upload($voucher_header_id); // <<--- file upload logic

                $this->set_flashmessage("success", "Expense saved successfully.");
                redirect("expenses/expense_list");
            }
        }
    }

    $this->render("money/expense_form", $data);
}

private function prepare_edit_mode_data($id, $voucher)
{
    $voucher = (array) $voucher;
    $voucherDetails = $this->voucherDetail->get_by_voucher($id);
    $voucherCase = $this->voucherCase->get_by_voucher($id);
    $document = $this->document->get_by_voucher($id);

    $editData = [
        "id" => $voucher["id"],
        "expense_account" => $voucherDetails[0]["account_id"] ?? "",
        "expense_category_id" => $voucher["expense_category_id"] ?? "",
        "paid_through" => $voucher["paid_through_account_id"] ?? "",
        "tax_id" => $voucher["tax_id"] ?? "",
        "amount" => $voucherDetails[0]["amount"] ?? "",
        "billingStatus" => $voucher["billing_status"] ?? "",
        "dated" => $voucher["dated"] ?? "",
        "description" => $voucher["description"] ?? "",
        "referenceNum" => $voucher["reference_number"] ?? "",
        "attachment" => $voucher["attachment"] ?? "",
        "case_id" => $voucherCase["case_id"] ?? "",
        "case_subject" => $voucherCase["case_subject"] ?? "",
        "case_category" => $voucherCase["case_category"] ?? "",
        "vendor_id" => $voucher["vendor_id"] ?? "",
        "vendorName" => $voucher["vendor_name"] ?? "",
        "client_id" => $voucher["client_id"] ?? "",
        "client_account_id" => $voucher["client_account_id"] ?? "",
        "clientName" => $voucher["client_name"] ?? "",
        "paymentMethod" => $voucher["payment_method"] ?? "",
        "related_hearing" => $voucher["related_hearing"] ?? false,
        "related_task" => $voucher["related_task"] ?? false,
        "related_event" => $voucher["related_event"] ?? false,
    ];

    $data = [
        "expense" => $editData,
        "rates" => $this->exchange_rate->get_organization_exchange_rates(
            $this->session->userdata("organizationID")
        ),
        "isCasePreset" => false,
        "voucher" => $voucher,
        "voucherDetails" => $voucherDetails,
        "document" => $document,
    ];

    return $data;
}

private function handle_document_upload($voucher_id)
{
    if (!empty($_FILES['attachment']['name'])) {
        $config['upload_path'] = './uploads/documents/';
        $config['allowed_types'] = 'pdf|jpg|jpeg|png|doc|docx';
        $config['max_size'] = 2048;
        $config['file_name'] = 'expense_' . $voucher_id . '_' . time();

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('attachment')) {
            $fileData = $this->upload->data();

            $this->load->model('document');
            $this->document->save([
                'voucher_id' => $voucher_id,
                'file_path' => $fileData['file_name'],
                'file_type' => $fileData['file_type'],
                'uploaded_by' => $this->session->userdata('userID'),
                'uploaded_on' => date("Y-m-d H:i:s"),
            ]);
        } else {
            $this->set_flashmessage("error", $this->upload->display_errors());
        }
    }
}

    private function load_expense_dependencies()
    {
        $this->load->model("expense", "expensefactory");
        $this->expense = $this->expensefactory->get_instance();

        $this->load->model("account", "accountfactory");
        $this->account = $this->accountfactory->get_instance();

        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();

        $this->load->model("exchange_rate");
        $this->load->helper(["text"]);
    }
    private function prepare_expense_form_data($id, $extraData)
    {
        $caseId = $extraData["caseId"] ?? "";
        $caseSubject = $extraData["caseSubject"] ?? "";
        $clientId = $extraData["clientId"] ?? "";
        $clientName = $extraData["clientName"] ?? "";
        $case_category = $extraData["case_category"] ?? "";

        $data = [
            "expense" => ["id" => "", "expense_account" => "", "expense_category_id" => "", "paid_through" => "", "tax_id" => "", "amount" => "", "billingStatus" => "", "dated" => "", "description" => "", "referenceNum" => "", "attachment" => "", "case_id" => $caseId, "case_subject" => $caseSubject, "case_category" => $case_category, "vendor_id" => "",
                "vendorName" => "",
                "client_id" => $clientId, "client_account_id" => "", "clientName" => $clientName, "paymentMethod" => "", "related_hearing" => $extraData["hearing"] ?? false, "related_task" => $extraData["task"] ?? false, "related_event" => $extraData["event"] ?? false,
            ],
            "isCasePreset" => !empty($extraData),
            "rates" => $this->exchange_rate->get_organization_exchange_rates(
                $this->session->userdata("organizationID")
            ),
        ];

        return $data;
    }
    private function handle_expense_submission($id, &$data)
    {
        if ($this->license_availability === false) {
            $this->set_flashmessage("error", $this->licensor->get_license_message());
            redirect("vouchers/expense_edit/" . $id);
        }

        $this->validate_current_organization($this->input->post("organization_id"), "expenses_list");

        if ($this->input->post("expense_account") == $this->input->post("paid_through")) {
            $this->set_flashmessage("error", $this->lang->line("transaction_not_saved"));
            redirect("vouchers/expenses_list/");
        }

        if ($this->input->post("amount") <= 0) {
            $this->set_flashmessage("error", $this->lang->line("amount_should_be_positive"));
            redirect("vouchers/expenses_list/");
        }

        $result = $this->save_or_update_voucher($id, $data);

        if ($result) {
            $this->set_flashmessage("success", $this->lang->line("save_record_successfull"));
            redirect("vouchers/expenses_list/");
        } else {
            $this->set_flashmessage("error", $this->lang->line("error_occurred_while_saving"));
            redirect("vouchers/expenses_list/");
        }
    }
    private function save_or_update_voucher($id, &$data)
    {
        if ($id == 0) {
            return $this->create_new_expense($data);
        } else {
            return $this->update_existing_expense($id, $data);
        }
    }
    private function render_expense_form($id, $data)
    {
        $this->includes("money/js/expense_form", "js");
        $this->includes("money/js/common_expenses_form_functions", "js");

        if ($id != 0) {
            $data["is_edit_mode"] = true;
            $data["voucherId"] = $id;
            // other includes and setup for edit mode
        }

        $this->load->library("user_agent");
        $data["referrer"] = $this->agent->is_referral() ? $this->agent->referrer() : "dashboard";

        $this->load->view("expenses/form", $data);
    }
    private function create_new_expense(&$data)
    {
        $this->load->model("voucher_header", "voucherfactory");
        $this->voucher = $this->voucherfactory->get_instance();

        $this->load->model("voucher_detail", "voucherDetailFactory");
        $this->voucherDetail = $this->voucherDetailFactory->get_instance();

        $this->load->model("voucher_related_case", "voucherCaseFactory");
        $this->voucherRelatedCase = $this->voucherCaseFactory->get_instance();

        $amount = unformat_money($this->input->post("amount"));
        $tax_id = $this->input->post("tax_id");
        $tax = $this->get_tax_details($tax_id);
        $tax_amount = 0;
        $main_amount = $amount;

        if ($tax) {
            $tax_amount = ($tax->inclusive) ? $this->calculate_tax_inclusive($amount, $tax->rate) : $this->calculate_tax_exclusive($amount, $tax->rate);
            $main_amount = $amount - $tax_amount;
        }

        // Create voucher header
        $header_id = $this->voucher->insert([
            "voucher_type_id" => VOUCHER_TYPE_EXPENSE,
            "reference_num" => $this->input->post("referenceNum"),
            "organization_id" => $this->session->userdata("organizationID"),
            "date_created" => now(),
            "dated" => $this->input->post("dated"),
            "amount" => $amount,
            "currency_id" => $this->input->post("currency_id"),
            "description" => $this->input->post("description"),
            "created_by" => $this->session->userdata("userID"),
            "billing_status" => $this->input->post("billingStatus")
        ]);

        if (!$header_id) return false;

        // Insert voucher detail (debit: expense)
        $this->voucherDetail->insert([
            "voucher_id" => $header_id,
            "account_id" => $this->input->post("expense_account"),
            "amount" => $main_amount,
            "is_debit" => 1
        ]);

        // Insert tax line (if applicable)
        if ($tax_amount > 0 && isset($tax->account_id)) {
            $this->voucherDetail->insert([
                "voucher_id" => $header_id,
                "account_id" => $tax->account_id,
                "amount" => $tax_amount,
                "is_debit" => 1
            ]);
        }

        // Insert paid-through account (credit)
        $this->voucherDetail->insert([
            "voucher_id" => $header_id,
            "account_id" => $this->input->post("paid_through"),
            "amount" => $amount,
            "is_debit" => 0
        ]);

        // Link to case (if present)
        $case_id = $this->input->post("case_id");
        if (!empty($case_id)) {
            $this->voucherRelatedCase->insert([
                "voucher_id" => $header_id,
                "case_id" => $case_id
            ]);
        }

        // Upload attachment (if any)
        $this->handle_document_upload($header_id);

        // Trigger notifications if needed
        $this->notify_if_open($header_id);

        return true;
    }
    private function update_existing_expense($id, &$data)
    {
        $this->load->model("voucher_header", "voucherfactory");
        $this->voucher = $this->voucherfactory->get_instance();

        $this->load->model("voucher_detail", "voucherDetailFactory");
        $this->voucherDetail = $this->voucherDetailFactory->get_instance();

        $this->load->model("voucher_related_case", "voucherCaseFactory");
        $this->voucherRelatedCase = $this->voucherCaseFactory->get_instance();

        $amount = unformat_money($this->input->post("amount"));
        $tax_id = $this->input->post("tax_id");
        $tax = $this->get_tax_details($tax_id);
        $tax_amount = 0;
        $main_amount = $amount;

        if ($tax) {
            $tax_amount = ($tax->inclusive) ? $this->calculate_tax_inclusive($amount, $tax->rate) : $this->calculate_tax_exclusive($amount, $tax->rate);
            $main_amount = $amount - $tax_amount;
        }

        // Update voucher header
        $this->voucher->update($id, [
            "reference_num" => $this->input->post("referenceNum"),
            "dated" => $this->input->post("dated"),
            "amount" => $amount,
            "currency_id" => $this->input->post("currency_id"),
            "description" => $this->input->post("description"),
            "billing_status" => $this->input->post("billingStatus")
        ]);

        // Remove existing details & case associations
        $this->voucherDetail->delete_by_voucher($id);
        $this->voucherRelatedCase->delete_by_voucher($id);

        // Re-insert voucher detail
        $this->voucherDetail->insert([
            "voucher_id" => $id,
            "account_id" => $this->input->post("expense_account"),
            "amount" => $main_amount,
            "is_debit" => 1
        ]);

        if ($tax_amount > 0 && isset($tax->account_id)) {
            $this->voucherDetail->insert([
                "voucher_id" => $id,
                "account_id" => $tax->account_id,
                "amount" => $tax_amount,
                "is_debit" => 1
            ]);
        }

        $this->voucherDetail->insert([
            "voucher_id" => $id,
            "account_id" => $this->input->post("paid_through"),
            "amount" => $amount,
            "is_debit" => 0
        ]);

        $case_id = $this->input->post("case_id");
        if (!empty($case_id)) {
            $this->voucherRelatedCase->insert([
                "voucher_id" => $id,
                "case_id" => $case_id
            ]);
        }

        // Upload new document if any
        $this->handle_document_upload($id);

        return true;
    }
    private function get_tax_details($tax_id)
    {
        $this->load->model("tax");
        return $this->tax->get($tax_id);
    }

    private function calculate_tax_exclusive($amount, $rate)
    {
        return round(($amount * $rate) / 100, 2);
    }

    private function calculate_tax_inclusive($amount, $rate)
    {
        return round(($amount * $rate) / (100 + $rate), 2);
    }



    private function notify_if_open($voucher_id)
    {
        if ($this->input->post("billingStatus") == "open") {
            $this->notification->expense_created($voucher_id);
        }
    }

}

