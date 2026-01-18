<?php

require "Top_controller.php";
class Vouchers extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->authenticate_actions_per_license("core");
        $this->load->model("voucher_header", "voucher_headerfactory");
        $this->voucher_header = $this->voucher_headerfactory->get_instance();
        $this->load->model("bill_header", "bill_headerfactory");
        $this->bill_header = $this->bill_headerfactory->get_instance();
        $this->load->model("bill_details", "bill_detailsfactory");
        $this->bill_details = $this->bill_detailsfactory->get_instance();
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        $this->load->model("money_preference");
        $this->load->model("supplier_tax", "supplier_taxfactory");
        $this->supplier_tax = $this->supplier_taxfactory->get_instance();
        $this->responseData = default_response_data();
    }

    /**
     * Add a new bill
     * POST /vouchers/add
     */
    public function bill_add()
    {
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            $response = $this->save(0);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }

    /**
     * @param $bill_id
     * @return void
     *
     */
    public function edit($bill_id)
    {
        $response = $this->responseData;
        if ($this->input->post(NULL)) {
            $response = $this->save($bill_id0);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }

    /**
     * List all bills with optional filtering
     * POST /vouchers/list_bills
     */
    /**
     * List all bills with optional filtering
     * POST /vouchers/list_bills
     */
    public function list_bills()
    {
        $response = $this->responseData;

        $filter = $this->input->post("filter") ?: [];
        $sortable = $this->input->post("sort") ?: [];

        $bills_data = $this->voucher_header->k_load_all_bills($filter, $sortable);

        if (isset($bills_data['data']) && is_array($bills_data['data'])) {
            $response["success"]["data"] = $bills_data['data'];
            $response["success"]["pagination"] = [
                'total' => $bills_data['total'] ?? count($bills_data['data']),
                'page' => $bills_data['page'] ?? 1,
                'page_size' => $bills_data['page_size'] ?? 20
            ];
        } else {
            $response["error"] = $this->lang->line("no_data_found");
        }

        $this->render($response);
    }

    /**
     * Get bill details by ID
     * POST /vouchers/get_bill
     */
    public function get_bill()
    {
        $response = $this->responseData;
        $bill_id = $this->input->post("bill_id");

        if (!empty($bill_id)) {
            $bill_data = $this->voucher_header->fetch_bill_voucher($bill_id);

            if (!empty($bill_data)) {
                $response["success"]["data"] = $bill_data;
            } else {
                $response["error"] = $this->lang->line("bill_not_found");
            }
        } else {
            $response["error"] = $this->lang->line("bill_id_required");
        }

        $this->render($response);
    }

    private function save($bill_id = 0)
    {
        $response = $this->responseData;
        $post_data = $this->input->post(NULL);
        $user_id = $this->user_logged_in_data["user_id"];

        try {
            // Validate required fields
            $required_fields = ['organization_id', 'supplier_id', 'referenceNum', 'dated', 'dueDate', 'total', 'accounts', 'desc', 'quantity', 'price'];
            foreach ($required_fields as $field) {
                if (!isset($post_data[$field])) {
                    throw new Exception($this->lang->line("missing_required_field") . ": " . $field);
                }
            }

            // Validate array lengths match
            $array_fields = ['accounts', 'desc', 'quantity', 'price'];
            $array_lengths = [];
            foreach ($array_fields as $field) {
                if (!is_array($post_data[$field])) {
                    throw new Exception($this->lang->line("field_must_be_array") . ": " . $field);
                }
                $array_lengths[$field] = count($post_data[$field]);
            }

            if (count(array_unique($array_lengths)) !== 1) {
                throw new Exception($this->lang->line("arrays_must_have_same_length"));
            }

            // Get tax preferences
            $activateTax = $this->money_preference->get_key_groups();
            $displayTax = $activateTax["ActivateTaxesinInvoices"]["TEnabled"] ?? 0;

            // Start transaction
            $this->db->trans_start();

            // Set default values
            $rate = $post_data['rate'] ?? "1.0000000000";
            $description = $post_data['description'] ?? '';
            $client_id = $post_data['client_id'] ?? null;
            $case_id = $post_data['case_id'] ?? null;

            // Create voucher header
            $this->voucher_header->set_field("organization_id", $post_data['organization_id']);
            $this->voucher_header->set_field("refNum", $this->auto_generate_rf("BI"));
            $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($post_data['dated'])));
            $this->voucher_header->set_field("voucherType", "BI");
            $this->voucher_header->set_field("referenceNum", $post_data['referenceNum']);
            $this->voucher_header->set_field("description", $description);
            $this->voucher_header->set_field("attachment", null);

            if (!$this->voucher_header->insert()) {
                throw new Exception($this->lang->line("failed_create_voucher_header"));
            }

            $voucher_header_id = $this->voucher_header->get_field("id");

            // Link to case if provided
            if (!empty($case_id)) {
                $this->load->model("voucher_related_case");
                $this->voucher_related_case->set_field("legal_case_id", $case_id);
                $this->voucher_related_case->set_field("voucher_header_id", $voucher_header_id);
                if (!$this->voucher_related_case->insert()) {
                    throw new Exception($this->lang->line("failed_link_bill_case"));
                }
            }

            // Add client to case if applicable
            $client_added_to_case = false;
            if (!empty($case_id) && !empty($client_id)) {
                $client_added_to_case = $this->legal_case->add_client_to_case([
                    'case_id' => $case_id,
                    'client_id' => $client_id
                ]);
            }

            // Create supplier credit entry
            $this->load->model("voucher_detail", "voucher_detailfactory");
            $this->voucher_detail=$this->voucher_detailfactory->get_instance();
            $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
            $this->voucher_detail->set_field("account_id", $post_data['supplier_id']);
            $this->voucher_detail->set_field("drCr", "C");
            $this->voucher_detail->set_field("local_amount", $post_data['total']);
            $this->voucher_detail->set_field("foreign_amount", $post_data['total'] / $rate * 1);
            $this->voucher_detail->set_field("description", "BILL-CREDIT");

            if (!$this->voucher_detail->insert()) {
                throw new Exception($this->lang->line("failed_create_credit_entry"));
            }

            // Create bill header
            $this->bill_header->set_field("voucher_header_id", $voucher_header_id);
            $this->bill_header->set_field("account_id", $post_data['supplier_id']);
            $this->bill_header->set_field("dueDate", date("Y-m-d H:i", strtotime($post_data['dueDate'])));
            $this->bill_header->set_field("total", $post_data['total']);
            $this->bill_header->set_field("displayTax", $displayTax);
            $this->bill_header->set_field("status", "open");
            $this->bill_header->set_field("client_id", $client_id);

            if (!$this->bill_header->insert()) {
                throw new Exception($this->lang->line("failed_create_bill_header"));
            }

            $bill_header_id = $this->bill_header->get_field("id");

            // Update voucher description with bill ID
            $this->voucher_detail->set_field("description", "BIL-" . $bill_header_id);
            $this->voucher_detail->update();

            // Create bill line items
            $line_items_count = count($post_data['accounts']);
            for ($key = 0; $key < $line_items_count; $key++) {
                $this->bill_details->reset_fields();
                $this->bill_details->set_field("bill_header_id", $bill_header_id);
                $this->bill_details->set_field("account_id", $post_data['accounts'][$key]);
                $this->bill_details->set_field("description", $post_data['desc'][$key]);
                $this->bill_details->set_field("quantity", $post_data['quantity'][$key]);
                $this->bill_details->set_field("price", $post_data['price'][$key]);
                $this->bill_details->set_field("basePrice", $post_data['price'][$key]);
                $this->bill_details->set_field("tax_id", null);
                $this->bill_details->set_field("percentage", null);

                if (!$this->bill_details->insert()) {
                    throw new Exception($this->lang->line("failed_create_line_item"));
                }
            }

            // Create expense debit entries
            $grouped_accounts = $this->bill_details->load_grouped_accounts($bill_header_id);
            foreach ($grouped_accounts as $account) {
                $local_amount = $account["quantity"] * $account["basePrice"];
                $foreign_amount = $account["quantity"] * $account["price"];

                $this->voucher_detail->reset_fields();
                $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                $this->voucher_detail->set_field("account_id", $account["account_id"]);
                $this->voucher_detail->set_field("drCr", "D");
                $this->voucher_detail->set_field("local_amount", $local_amount);
                $this->voucher_detail->set_field("foreign_amount", $foreign_amount);
                $this->voucher_detail->set_field("description", "BIL-" . $bill_header_id);

                if (!$this->voucher_detail->insert()) {
                    throw new Exception($this->lang->line("failed_create_debit_entry"));
                }
            }

            // Set bill status and create reminder
            $this->set_bill_status($bill_header_id);

            $reminder = [
                "remindDate" => $post_data['dueDate'],
                "related_object" => $this->bill_header->get("_table")
            ];
            $reminder["summary"] = sprintf(
                $this->lang->line("bill_notification_message"),
                $this->lang->line("bill"),
                $post_data['dueDate'],
                $post_data['supplier'] ?? ''
            );
            $this->notify_me_before_due_date($voucher_header_id, $reminder);

            // Commit transaction
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                throw new Exception($this->lang->line("transaction_failed"));
            }

            $response["success"]["data"] = [
                'voucher_header_id' => $voucher_header_id,
                'bill_header_id' => $bill_header_id,
                'reference_number' => $post_data['referenceNum'],
                'line_items_count' => $line_items_count,
                'client_added_to_case' => $client_added_to_case
            ];
            $response["success"]["msg"] = $this->lang->line("bill_created_successfully");

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response["error"] = $e->getMessage();
        }

        return $response;
    }
    /**
     * Record payment for a bill (against voucher_id)
     * POST /vouchers/bill_payment_add
     */
    public function bill_payment_add()
    {
        $response = $this->responseData;
        $voucher_id = $this->input->post("voucher_id");

        if (!empty($voucher_id) && $this->input->post(NULL)) {
            $response = $this->bill_payment_save($voucher_id);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }

        $this->render($response);
    }
    /**
     * Update existing payment
     * POST /vouchers/bill_payment_edit
     */
    public function bill_payment_edit()
    {
        $response = $this->responseData;
        $voucher_id = $this->input->post("voucher_id");
        $payment_id = $this->input->post("payment_id");

        if (!empty($voucher_id) && $this->input->post(NULL)) {
            // Validate voucher access
            if (!$this->validate_voucher($voucher_id)) {
                $response["error"] = $this->lang->line("access_denied");
                $this->render($response);
                return;
            }

            $response = $this->bill_payment_save($voucher_id, $payment_id);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }

        $this->render($response);
    }
    /**
     * Get payment history for a bill voucher
     * POST /vouchers/bill_payments_made
     */
    public function bill_payments_made()
    {
        $response = $this->responseData;
        $voucher_id = $this->input->post("voucher_id");

        if (!empty($voucher_id)) {
            // Validate voucher access
            if (!$this->validate_voucher($voucher_id)) {
                $response["error"] = $this->lang->line("access_denied");
                $this->render($response);
                return;
            }

            try {
                $this->load->model(["bill_payment_bill"]);
                $this->load->model("bill_header", "bill_headerfactory");
                $this->bill_header = $this->bill_headerfactory->get_instance();

                // Get bill data from voucher
                $bill_data = $this->voucher_header->fetch_bill_voucher($voucher_id);

                if (empty($bill_data)) {
                    $response["error"] = $this->lang->line("invalid_record");
                    $this->render($response);
                    return;
                }

                // Get payment history
                $bill_payments = $this->bill_payment_bill->load_all(["where" => ["bill_header_id", $bill_data["id"]]]);

                // Calculate balances
                $this->bill_header->fetch($bill_data["id"]);
                $credits_available = 0;
                $balance_due = $bill_data["total"];

                foreach ($bill_payments as $payment) {
                    $credits_available += $payment["amount"] * 1;
                    $balance_due = $bill_data["total"] * 1 - $credits_available;
                }

                $balance_due = number_format($balance_due, 2, NULL, "");

                $response["success"]["data"] = [
                    'bill_data' => $bill_data,
                    'credits_available' => $credits_available,
                    'balance_due' => $balance_due,
                    'payment_history' => $bill_payments,
                    'bill_id' => $bill_data["id"],
                    'voucher_header_id' => $bill_data["voucher_header_id"]
                ];

            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
            }
        } else {
            $response["error"] = $this->lang->line("voucher_id_required");
        }

        $this->render($response);
    }
    /**
     * Delete a payment
     * POST /vouchers/bill_payment_delete
     */
    public function bill_payment_delete()
    {
        $response = $this->responseData;
        $voucher_id = $this->input->post("voucher_id");
        $payment_id = $this->input->post("payment_id");

        if (!empty($voucher_id) && !empty($payment_id)) {
            try {
                // Validate voucher and payment access
                if (!$this->validate_voucher_and_payment($voucher_id, $payment_id)) {
                    $response["error"] = $this->lang->line("access_denied");
                    $this->render($response);
                    return;
                }

                $this->load->model("account", "accountfactory");
                $this->account = $this->accountfactory->get_instance();
                $this->load->model(["bill_payment", "bill_payment_bill"]);

                $result = false;

                $this->bill_payment->fetch($payment_id);
                $voucher_header_id = $this->bill_payment->get_field("voucher_header_id");
                $this->bill_payment_bill->fetch(["bill_payment_id" => $payment_id]);
                $bill_header_id = $this->bill_payment_bill->get_field("bill_header_id");

                if ($this->bill_payment_bill->delete(["where" => ["bill_payment_id", $payment_id]])) {
                    $result = $this->bill_payment->delete($payment_id);

                    if ($result && $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]])) {
                        $this->dms->delete_module_record_container("BI-PY", $voucher_header_id);

                        // Update bill status
                        if ($this->set_bill_status($bill_header_id)) {
                            $response["success"]["data"] = [
                                'status' => 101,
                                'message' => $this->lang->line("payment_deleted_successfully")
                            ];
                        } else {
                            $response["success"]["data"] = [
                                'status' => 202,
                                'message' => $this->lang->line("payment_deleted_but_status_update_failed")
                            ];
                        }
                    } else {
                        $result = false;
                    }
                }

                if (!$result) {
                    $response["error"] = $this->lang->line("failed_delete_payment");
                }

            } catch (Exception $e) {
                $response["error"] = $e->getMessage();
            }
        } else {
            $response["error"] = $this->lang->line("voucher_payment_id_required");
        }

        $this->render($response);
    }
    /**
     * Save payment (create or update)
     */
    private function bill_payment_save($voucher_id = 0, $payment_id = 0)
    {
        $response = $this->responseData;

        try {
            // Validate voucher access
            if (0 < $voucher_id && !$this->validate_voucher($voucher_id)) {
                $response["error"] = $this->lang->line("access_denied");
                return $response;
            }

            $post_data = $this->input->post(NULL);

            // Load required models
            $this->load->model("account", "accountfactory");
            $this->account = $this->accountfactory->get_instance();
            $this->load->model(["bill_payment", "bill_payment_bill"]);
            $this->load->model("bill_header", "bill_headerfactory");
            $this->bill_header = $this->bill_headerfactory->get_instance();

            $this->load->model("exchange_rate");
            $rates = $this->exchange_rate->get_organization_exchange_rates($this->session->userdata("organizationID"));

            if (!isset($rates)) {
                $response["error"] = $this->lang->line("exchange_rates_not_set");
                return $response;
            }

            // Get bill data from voucher
            $this->voucher_header->fetch($voucher_id);
            $voucher_organization_id = $this->voucher_header->get_field("organization_id");
            $this->voucher_header->reset_fields();

            if ($this->session->userdata("organizationID") != $voucher_organization_id) {
                $response["error"] = $this->lang->line("access_denied");
                return $response;
            }

            $bill_data = $this->voucher_header->fetch_bill_voucher($voucher_id);

            // Validate bill status
            if (in_array($bill_data["status"], ["draft", "cancelled"])) {
                $response["error"] = $this->lang->line("you_can_not_record_any_payments_for_this_bill");
                return $response;
            }

            if (empty($bill_data)) {
                $response["error"] = $this->lang->line("invalid_record");
                return $response;
            }

            // Calculate current balance
            $bill_payments = $this->bill_payment_bill->load_all(["where" => ["bill_header_id", $bill_data["id"]]]);
            $this->bill_header->fetch($bill_data["id"]);
            $credits_available = 0;
            $balance_due = $bill_data["total"];

            foreach ($bill_payments as $payment) {
                $credits_available += $payment["amount"] * 1;
                $balance_due = $bill_data["total"] * 1 - $credits_available;
            }

            $balance_due = number_format($balance_due, 2, NULL, "");

            // Check if bill is already paid
            if ($balance_due == 0 && $this->bill_header->get_field("status") == "paid" && $payment_id == 0) {
                $response["error"] = $this->lang->line("you_can_not_record_any_payments_for_this_bill");
                return $response;
            }

            // Start transaction
            $this->db->trans_start();

            $result = true;

            if (0 < $payment_id && $this->bill_payment->fetch($payment_id)) {
                // UPDATE EXISTING PAYMENT
                $voucher_header_id = $this->bill_payment->get_field("voucher_header_id");
                $this->bill_payment_bill->fetch(["bill_payment_id" => $payment_id]);
                $amount = $this->bill_payment_bill->get_field("amount");

                // Delete existing records
                $this->bill_payment_bill->delete(["where" => ["bill_payment_id", $payment_id]]);
                $this->voucher_detail->delete(["where" => ["voucher_header_id", $voucher_header_id]]);

                // Update voucher header
                $this->voucher_header->fetch($voucher_header_id);
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($post_data['paidOn'])));
                $this->voucher_header->set_field("voucherType", "BI-PY");
                $this->voucher_header->set_field("referenceNum", $post_data['referenceNum']);
                $this->voucher_header->set_field("description", $post_data['comments']);

                if ($this->voucher_header->update()) {
                    $voucher_header_id = $this->voucher_header->get_field("id");
                    $this->account->fetch($post_data['account_id']);
                    $paid_through_currency = $this->account->get_field("currency_id");

                    // Recreate voucher details
                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                    $this->voucher_detail->set_field("account_id", $post_data['account_id']);
                    $this->voucher_detail->set_field("drCr", "C");
                    $this->voucher_detail->set_field("local_amount", $post_data['amount'] * $rates[$paid_through_currency]);
                    $this->voucher_detail->set_field("foreign_amount", $post_data['amount']);
                    $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $bill_data["refNum"]);

                    if ($this->voucher_detail->insert()) {
                        $this->voucher_detail->reset_fields();
                        $this->account->fetch($post_data['supplierAccountId']);
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $post_data['supplierAccountId']);
                        $this->voucher_detail->set_field("drCr", "D");
                        $this->voucher_detail->set_field("local_amount", $post_data['amount'] * $rates[$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $post_data['amount'] * $rates[$paid_through_currency] / $rates[$this->account->get_field("currency_id")]);
                        $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $bill_data["refNum"]);

                        if ($this->voucher_detail->insert()) {
                            // Update bill payment
                            $this->bill_payment->set_field("voucher_header_id", $voucher_header_id);
                            $this->bill_payment->set_field("account_id", $post_data['account_id']);
                            $this->bill_payment->set_field("paymentMethod", $post_data['paymentMethod']);
                            $this->bill_payment->set_field("total", $post_data['amount']);
                            $this->bill_payment->set_field("supplier_account_id", $post_data['supplierAccountId']);
                            $this->bill_payment->set_field("billPaymentTotal", $post_data['amount'] * $rates[$paid_through_currency]);

                            if ($this->bill_payment->update()) {
                                $bill_payment_id = $this->bill_payment->get_field("id");

                                // Recreate bill payment bill link
                                $this->bill_payment_bill->reset_fields();
                                $this->bill_payment_bill->set_field("bill_payment_id", $bill_payment_id);
                                $this->bill_payment_bill->set_field("bill_header_id", $post_data['bill_id']);
                                $this->bill_payment_bill->set_field("amount", $post_data['amount'] * $rates[$paid_through_currency]);

                                if ($this->bill_payment_bill->insert()) {
                                    // Update bill status
                                    $this->bill_header->fetch($post_data['bill_id']);
                                    $this->bill_header->set_field("status", "open");

                                    if (abs($balance_due + $amount - $post_data['amount'] * $rates[$paid_through_currency]) < 0) {
                                        $this->bill_header->set_field("status", "paid");
                                    } else {
                                        if ($post_data['amount'] * $rates[$paid_through_currency] < $balance_due + $amount) {
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
                    }
                }
            } else {
                // CREATE NEW PAYMENT
                $this->account->fetch($post_data['account_id']);
                $paid_through_currency = $this->account->get_field("currency_id");

                // Validate payment amount doesn't exceed balance
                if ($balance_due < round($post_data['amount'] * $rates[$paid_through_currency], 2)) {
                    $response["error"] = $this->lang->line("allowed_amount");
                    return $response;
                }

                // Create voucher header for payment
                $this->voucher_header->set_field("organization_id", $this->session->userdata("organizationID"));
                $this->voucher_header->set_field("refNum", $this->auto_generate_rf("BI-PY"));
                $this->voucher_header->set_field("dated", date("Y-m-d", strtotime($post_data['paidOn'])));
                $this->voucher_header->set_field("voucherType", "BI-PY");
                $this->voucher_header->set_field("referenceNum", $post_data['referenceNum']);
                $this->voucher_header->set_field("description", $post_data['comments']);
                $bill_payment_id = "";

                if ($this->voucher_header->insert()) {
                    $voucher_header_id = $this->voucher_header->get_field("id");

                    // Create credit entry (payment account)
                    $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                    $this->voucher_detail->set_field("account_id", $post_data['account_id']);
                    $this->voucher_detail->set_field("drCr", "C");
                    $this->voucher_detail->set_field("local_amount", $post_data['amount'] * $rates[$paid_through_currency]);
                    $this->voucher_detail->set_field("foreign_amount", $post_data['amount']);

                    if ($this->voucher_detail->insert()) {
                        $first_voucher_detail_id = $this->voucher_detail->get_field("id");
                        $this->voucher_detail->reset_fields();

                        // Create debit entry (supplier account)
                        $this->account->fetch($post_data['supplierAccountId']);
                        $this->voucher_detail->set_field("voucher_header_id", $voucher_header_id);
                        $this->voucher_detail->set_field("account_id", $post_data['supplierAccountId']);
                        $this->voucher_detail->set_field("drCr", "D");
                        $this->voucher_detail->set_field("local_amount", $post_data['amount'] * $rates[$paid_through_currency]);
                        $this->voucher_detail->set_field("foreign_amount", $post_data['amount'] * $rates[$paid_through_currency] / $rates[$this->account->get_field("currency_id")]);

                        if ($this->voucher_detail->insert()) {
                            // Create bill payment record
                            $this->bill_payment->reset_fields();
                            $this->bill_payment->set_field("voucher_header_id", $voucher_header_id);
                            $this->bill_payment->set_field("account_id", $post_data['account_id']);
                            $this->bill_payment->set_field("paymentMethod", $post_data['paymentMethod']);
                            $this->bill_payment->set_field("total", $post_data['amount']);
                            $this->bill_payment->set_field("supplier_account_id", $post_data['supplierAccountId']);
                            $this->bill_payment->set_field("billPaymentTotal", $post_data['amount'] * $rates[$paid_through_currency]);

                            if ($this->bill_payment->insert()) {
                                // Update voucher descriptions
                                $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $post_data['bill_id']);
                                $this->voucher_detail->update();
                                $this->voucher_detail->fetch($first_voucher_detail_id);
                                $this->voucher_detail->set_field("description", "BIL-PY BIL-" . $post_data['bill_id']);
                                $this->voucher_detail->update();

                                $first_voucher_detail_id = $this->voucher_detail->get_field("id");
                                $bill_payment_id = $this->bill_payment->get_field("id");

                                // Link payment to bill
                                $this->bill_payment_bill->set_field("bill_payment_id", $bill_payment_id);
                                $this->bill_payment_bill->set_field("bill_header_id", $post_data['bill_id']);
                                $this->bill_payment_bill->set_field("amount", $post_data['amount'] * $rates[$paid_through_currency]);

                                if ($this->bill_payment_bill->insert()) {
                                    // Update bill status
                                    $this->bill_header->fetch($post_data['bill_id']);
                                    $this->bill_header->set_field("status", "open");
                                    $payment_inserted_id = $this->bill_payment_bill->get_field("id");
                                    $this->bill_payment_bill->fetch($payment_inserted_id);

                                    if ($balance_due == $this->bill_payment_bill->get_field("amount")) {
                                        $this->bill_header->set_field("status", "paid");
                                    } else {
                                        if ($this->bill_payment_bill->get_field("amount") < $balance_due) {
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
                    }
                } else {
                    $result = false;
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE || !$result) {
              $response["error"]= $this->lang->line("transaction_failed");
            }

            $response["success"]["data"] = [
                'voucher_header_id' => $voucher_header_id ?? '',
                'bill_payment_id' => $bill_payment_id ?? '',
                'reference_number' => $post_data['referenceNum'] ?? '',
                'amount_paid' => $post_data['amount'] ?? '',
                'new_bill_status' => $this->get_bill_status($post_data['bill_id'])
            ];
            $response["success"]["msg"] = sprintf($this->lang->line("save_record_successfull"), $this->lang->line("payment"));

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $response["error"] = $e->getMessage();
        }

        return $response;
    }
    /**
     * Validate voucher access
     */
    private function validate_voucher($voucher_id)
    {
        $this->voucher_header->fetch($voucher_id);
        $voucher_organization_id = $this->voucher_header->get_field("organization_id");
        return $this->session->userdata("organizationID") == $voucher_organization_id;
    }

    /**
     * Validate voucher and payment access
     */
    private function validate_voucher_and_payment($voucher_id, $payment_id)
    {
        if (!$this->validate_voucher($voucher_id)) {
            return false;
        }
        $this->load->model("bill_payment", "bill_paymentfactory");
        $this->bill_payment = $this->bill_paymentfactory->get_instance();

        if (!$this->bill_payment->fetch($payment_id)) {
            return false;
        }

        return true;
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

    private function auto_generate_rf($voucher_type = "")
    {
        return $this->voucher_header->auto_generate_rf($voucher_type);
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

}

?>