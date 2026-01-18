<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Money_preference extends System_preference
{
    protected $modelName = "money_preference";
    protected $systemPreferences = ["ExpensesValues" => ["expenseStatus" => "", "requireExpenseDocument" => NULL, "notifyUserGroupExpense" => "", "notifyUsersExpense" => "", "notifyUsersGroupToApproveExpense" => "", "notifyUsersToApproveExpense" => "", "notifyUsersExpenseByEmail" => NULL], "UsersValues" => ["userRatePerHour" => "", "userGroupsAppearInUserRatePerHourGrid" => ""], "ModuleLanguages" => ["systemDefaultLanguage" => "", "systemForeignLanguage_1" => "", "systemForeignLanguage_2" => ""], "InvoiceLanguage" => ["invoice" => "", "billTo" => "", "quote" => "", "invoiceNbr" => "", "date" => "", "due_on" => "", "purchaseOrder" => "", "terms" => "", "items" => "", "subTotal" => "", "totalWithTax" => "", "totalWithDiscount" => "", "total" => "", "Expenses_Item" => "", "Expenses_Sub_item" => "", "Expenses_Description" => "", "Expenses_Quantity" => "", "Expenses_Unit_Price" => "", "Expenses_Amount" => "", "Expenses_Tax" => "", "Expenses_Discount" => "", "Expenses_Date" => "", "Items_Item" => "", "Items_Sub_item" => "", "Items_Description" => "", "Items_Quantity" => "", "Items_Unit_Price" => "", "Items_Amount" => "", "Items_Tax" => "", "Items_Discount" => "", "Items_Date" => "", "Time_Logs_Item" => "", "Time_Logs_Sub_item" => "", "Time_Logs_Description" => "", "Time_Logs_Quantity" => "", "Time_Logs_Unit_Price" => "", "Time_Logs_Amount" => "", "Time_Logs_Tax" => "", "Time_Logs_Discount" => "", "Time_Logs_Date" => "", "time_logs" => "", "expenses" => "", "tax_number" => "", "sub_total_after_discount" => "", "userCode" => "", "case_id" => "", "case_subject" => "", "reference_case" => "", "invoice_ref" => "", "exchange_rate" => "", "legal_matters" => "", "time_logs_quantity_unit" => "", "currency_only" => "", "credit_note" => "", "credit_note_number" => "", "invoice_credited" => "", "credit_note_reference" => "", "sub_total_exclusive_vat" => "", "tax_amount" => "", "payment_method" => "", "transaction_type" => "", "status" => "", "paid_amount" => "", "remaining_amount" => "", "debit_note_number" => "", "related_invoice_number" => ""], "ActivateTaxesinInvoices" => ["TEnabled" => NULL], "ActivateDiscountinInvoices" => ["DEnabled" => NULL], "MoneyCurrency" => ["currencies" => ""], "InvoiceValues" => ["invoiceNumberPrefix" => "", "partnersCommissions" => "", "systemCommissionAccount" => ""], "timeTracking" => ["timeTrackingSalesAccount" => ""], "trustAccount" => ["trustAssetAccount" => ""], "InvoiceItems" => ["DisplayItemDate" => ""], "PartnerSettlementsPerInvoice" => ["SettlementsPerInvoiceEnabled" => NULL], "PartnerInvLanguage" => ["partner_name" => "", "payment_due_to" => "", "client_name" => "", "invoice_nb" => "", "partner_date" => "", "partner_item" => "", "partner_item_description" => "", "partner_item_date" => "", "partner_item_quantity" => "", "partner_item_uprice" => "", "partner_item_tamount" => "", "partner_item_percentage" => "", "partner_item_amount" => "", "partner_item_hour" => "", "partner_item_rate" => "", "total_items" => "", "partner_time_logs" => "", "partner_expenses" => "", "partner_items" => "", "partner_case_id" => "", "partner_case_subject" => "", "partner_legal_matters" => "", "partner_deductions" => "", "partner_invoice_total" => "", "partner_paid_amount" => "", "partner_amount_payable" => ""], "BillLanguage" => ["bill_supplier" => "", "bill_number" => "", "bill_tax_number" => "", "bill_date" => "", "bill_due_date" => "", "bill_related_matter" => "", "bill_client" => "", "bill_account" => "", "bill_description" => "", "bill_quantity" => "", "bill_price" => "", "bill_tax" => "", "bill_amount" => "", "bill_total" => "", "bill_details" => "", "bill_sub_total" => "", "bill_total_tax" => ""]];
    protected function invoice_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function billTo_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function quote_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function invoiceNbr_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function date_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function due_on_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function purchaseOrder_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function terms_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function tax_number_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function sub_total_after_discount_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function items_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function totalWithTax_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function totalWithDiscount_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function total_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function invoice_language_line_read($keyName)
    {
        $valuesList = (array) empty($this->systemPreferences["InvoiceLanguage"][$keyName]) ? array_fill_keys(["", "fl1", "fl2"], "") : unserialize($this->systemPreferences["InvoiceLanguage"][$keyName]);
        if (isset($valuesList[0])) {
            $valuesList[""] = $valuesList[0];
            unset($valuesList[0]);
        }
    }
    protected function userCode_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function case_id_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function case_subject_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function reference_case_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function invoice_ref_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function exchange_rate_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function legal_matters_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function time_logs_quantity_unit_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function currency_only_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function credit_note_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function credit_note_number_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function invoice_credited_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function credit_note_reference_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function sub_total_exclusive_vat_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function status_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function paid_amount_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function remaining_amount_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function tax_amount_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function payment_method_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function transaction_type_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function debit_note_number_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function related_invoice_number_read($keyName)
    {
        return $this->invoice_language_line_read($keyName);
    }
    protected function invoice_language_line_write($keyName, $keyValue)
    {
        if (!is_array($keyValue)) {
            $keyValue = array_fill_keys(["", "fl1", "fl2"], "");
        }
        $keyValue = serialize($keyValue);
        $this->systemPreferences["InvoiceLanguage"][$keyName] = $keyValue;
        return $keyValue;
    }
    protected function invoice_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function billTo_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function quote_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function invoiceNbr_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function date_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function due_on_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function purchaseOrder_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function terms_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function tax_number_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function sub_total_after_discount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function items_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function subTotal_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function totalWithTax_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function totalWithDiscount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function total_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Description_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Discount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Quantity_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Sub_item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Tax_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Unit_Price_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Expenses_Date_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Description_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Discount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Quantity_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Sub_item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Tax_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Unit_Price_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Items_Date_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Description_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Discount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Quantity_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Sub_item_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Tax_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Unit_Price_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function Time_Logs_Date_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function time_logs_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function expenses_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    public function get_values_by_group($group_name)
    {
        return $this->ci->money_preference->load_list(["where" => ["groupName", $group_name]]);
    }
    protected function userCode_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function case_id_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function case_subject_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function reference_case_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function invoice_ref_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function exchange_rate_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function legal_matters_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function time_logs_quantity_unit_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function currency_only_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function credit_note_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function credit_note_number_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function invoice_credited_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function credit_note_reference_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function sub_total_exclusive_vat_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function status_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function paid_amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function remaining_amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function tax_amount_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function payment_method_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function transaction_type_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function debit_note_number_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function related_invoice_number_write($keyName, $keyValue)
    {
        return $this->invoice_language_line_write($keyName, $keyValue);
    }
    protected function partner_inv_language_line_write($keyName, $keyValue)
    {
        if (!is_array($keyValue)) {
            $keyValue = array_fill_keys(["", "fl1", "fl2"], "");
        }
        $keyValue = serialize($keyValue);
        $this->systemPreferences["PartnerInvLanguage"][$keyName] = $keyValue;
        return $keyValue;
    }
    protected function partner_name_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function payment_due_to_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function client_name_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function invoice_nb_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_date_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function total_items_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_time_logs_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_expenses_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_items_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_description_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_date_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_quantity_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_uprice_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_tamount_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_percentage_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_amount_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_hour_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_item_rate_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_case_id_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_case_subject_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_legal_matters_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_deductions_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_invoice_total_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_paid_amount_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function partner_amount_payable_write($keyName, $keyValue)
    {
        return $this->partner_inv_language_line_write($keyName, $keyValue);
    }
    protected function bill_language_line_read($keyName)
    {
        $valuesList = (array) empty($this->systemPreferences["BillLanguage"][$keyName]) ? array_fill_keys(["", "fl1", "fl2"], "") : unserialize($this->systemPreferences["BillLanguage"][$keyName]);
        if (isset($valuesList[0])) {
            $valuesList[""] = $valuesList[0];
            unset($valuesList[0]);
        }
    }
    protected function bill_language_line_write($keyName, $keyValue)
    {
        if (!is_array($keyValue)) {
            $keyValue = array_fill_keys(["", "fl1", "fl2"], "");
        }
        $keyValue = serialize($keyValue);
        $this->systemPreferences["BillLanguage"][$keyName] = $keyValue;
        return $keyValue;
    }
    protected function bill_supplier_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_supplier_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_number_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_number_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_tax_number_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_tax_number_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_date_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_date_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_due_date_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_due_date_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_client_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_client_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_related_matter_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_related_matter_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_account_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_account_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_description_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_description_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_quantity_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_quantity_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_price_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_price_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_tax_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_tax_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_amount_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_amount_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_total_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_total_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_details_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_details_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_total_tax_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_total_tax_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
    protected function bill_sub_total_read($keyName)
    {
        return $this->bill_language_line_read($keyName);
    }
    protected function bill_sub_total_write($keyName, $keyValue)
    {
        return $this->bill_language_line_write($keyName, $keyValue);
    }
}

?>