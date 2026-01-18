<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Client extends My_Model
{
    protected $modelName = "client";
    protected $modelCode = "";
    protected $_table = "clients";
    protected $_listFieldName = "";
    protected $_fieldsNames = ["id", "company_id", "contact_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy", "term_id", "discount_percentage"];
    protected $allowedNulls = ["company_id", "contact_id", "createdOn", "createdBy", "modifiedOn", "modifiedBy", "term_id"];
    protected $builtInLogs = true;
    protected $contact;
    protected $company;
    public function __construct()
    {
        parent::__construct();
        $this->validate = ["company_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("company_id"))], "contact_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("contact_id"))], "term_id" => ["required" => false, "allowEmpty" => true, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("term_id"))], "discount_percentage" => ["required" => false, "allowEmpty" => false, "rule" => "numeric", "message" => sprintf($this->ci->lang->line("is_numeric_rule"), $this->ci->lang->line("discount(%)"))]];
        $this->ci->load->model("contact", "contactfactory");
        $this->contact = $this->ci->contactfactory->get_instance();
        $this->ci->load->model("company", "companyfactory");
        $this->company = $this->ci->companyfactory->get_instance();
    }
    public function insert($skipValidation = false)
    {
        return false;
    }
    public function delete($userQueryParts = [])
    {
        if (parent::delete($userQueryParts)) {
            return true;
        }
        return false;
    }
    public function fetch_client($id)
    {
        $query = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.*, clients_view.name as clientName";
        $query["where"] = [["model", "clients"], ["id", $id]];
        return $this->load($query);
    }
    public function fetch_client_related_contact_company($id)
    {
        $query = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.member_name as name";
        $query["where"] = [["model", "clients"], ["id", $id]];
        return $this->load($query);
    }
    public function k_load_all_clients($filter, $sortable)
    {
        $query = [];
        $response = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.id, clients_view.name, clients_view.type, clients_view.member_id, clients_view.member_name, companies_full_details.category as company_category";
        $query["join"] = ["companies_full_details", "companies_full_details.id = clients_view.member_id AND clients_view.type = 'Company' ", "left"];
        if (is_array($filter) && isset($filter["filters"])) {
            foreach ($filter["filters"] as $_filter) {
                $this->prep_k_filter($_filter, $query, $filter["logic"]);
            }
            unset($_filter);
            array_push($query["where"], ["model", "clients"]);
        } else {
            $query["where"] = ["model", "clients"];
        }
        $this->prep_query($query);
        $response["totalRows"] = $this->ci->db->count_all_results($this->_table);
        if (is_array($sortable) && !empty($sortable)) {
            foreach ($sortable as $_sort) {
                $query["order_by"][] = [$_sort["field"], $_sort["dir"]];
            }
        } else {
            $query["order_by"] = ["clients_view.id desc"];
        }
        if ($limit = $this->ci->input->post("take", true)) {
            $query["limit"] = [$this->ci->input->post("take", true), $this->ci->input->post("skip", true)];
        }
        $response["data"] = $this->load_all($query);
        return $response;
    }
    public function get_client($model, $id, $client_extra_data = [])
    {
        return $this->_get_client($model, $id, $this->ci->is_auth->get_user_id(), $client_extra_data);
    }
    private function insert_client($model, $id, $user_id, $client_extra_data = [])
    {
        if (!empty($model) && 0 < $id) {
            $data = is_array($client_extra_data) ? $client_extra_data : [];
            $data[$model . "_id"] = $id;
            $data["createdOn"] = date("Y-m-d H:i:s");
            $data["createdBy"] = $user_id;
            $data["modifiedOn"] = date("Y-m-d H:i:s");
            $data["modifiedBy"] = $user_id;
            $this->ci->db->insert("clients", $data);
            if (0 < $this->ci->db->affected_rows()) {
                return $this->ci->db->insert_id();
            }
            return false;
        }
        return false;
    }
    public function load_case_client_details($caseId)
    {
        $query = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.id as clientId, clients_view.name as clientName, legal_cases.id as caseId, legal_cases.subject as caseSubject , legal_cases.category as case_category";
        $query["join"] = [["legal_cases", "legal_cases.client_id = clients_view.id", "inner"]];
        $query["where"] = [["clients_view.model", "clients"], ["legal_cases.id", $caseId]];
        return $this->load($query);
    }
    public function load_client_details()
    {
        $query = [];
        $this->_table = "clients_view";
        $query["select"] = "clients_view.id, clients_view.name";
        $query["where"] = [["clients_view.model", "clients"]];
        return $this->load_all($query);
    }
    public function api_get_client($model, $id, $user_id)
    {
        return $this->_get_client($model, $id, $user_id);
    }
    public function _get_client($model, $id, $user_id, $client_extra_data = [])
    {
        if (!empty($model) && 0 < $id) {
            if ($model == "company") {
                $result = $this->company->fetch($id);
            } else {
                $result = $this->contact->fetch($id);
            }
            if ($result) {
                $query = $this->ci->db->get_where("clients", [$model . "_id" => $id]);
                if (0 < $query->num_rows()) {
                    $row = $query->row();
                    return $row->id;
                }
                return $this->insert_client($model, $id, $user_id, $client_extra_data);
            }
            return false;
        }
        return false;
    }
    public function load_clients_list($configList = [])
    {
        $_table = $this->_table;
        $this->_table = "clients_view";
        if (!$configList) {
            $configList = ["key" => "id", "value" => "name"];
        }
        $configQury = ["select" => ["clients_view.id, clients_view.name", false], "where" => [["clients_view.model", "clients"]]];
        $clients_list = $this->load_list($configQury, $configList);
        $this->_table = $_table;
        return $clients_list;
    }
    public function check_if_exists($id, $field)
    {
        $_table = $this->_table;
        $this->_table = "clients";
        $query = ["select" => ["id", false], "where" => [[(string) $field, $id]]];
        $clients = $this->load_all($query);
        $this->_table = $_table;
        return $clients;
    }
    public function manage_money_accounts($model, $member_id, $user_id, $target, $force_add = false, $lang = "")
    {
        $model_name = $model == "contact" ? "Person" : "Company";
        $model_type = "";
        $this->ci->load->model("contact_company_category");
        if ($model == "contact") {
            $this->ci->load->model("contact", "contactfactory");
            $this->ci->contact = $this->ci->contactfactory->get_instance();
            $this->ci->contact->fetch($member_id);
            $category = $this->ci->contact->get_field("contact_category_id");
        } else {
            $this->ci->load->model("company", "companyfactory");
            $this->ci->company = $this->ci->companyfactory->get_instance();
            $this->ci->company->fetch($member_id);
            $category = $this->ci->company->get_field("company_category_id");
        }
        if ($category) {
            $account_name = $model == "company" ? $this->ci->company->get_field("name") : ($this->ci->contact->get_field("father") ? $this->ci->contact->get_field("firstName") . " " . $this->ci->contact->get_field("father") . " " . $this->ci->contact->get_field("lastName") : $this->ci->contact->get_field("firstName") . " " . $this->ci->contact->get_field("lastName"));
            $this->ci->load->model("organization", "organizationfactory");
            $this->ci->organization = $this->ci->organizationfactory->get_instance();
            $organizations = $this->ci->organization->load_all_entities();
            $this->ci->contact_company_category->fetch($category);
            $category_name = $this->ci->contact_company_category->get_field("keyName");
            $accounts = [];
            if (in_array($category_name, ["Client", "Internal"]) || strpos($target, "client") !== false) {
                $model_type = "client";
                $model_id = $this->_get_client($model, $member_id, $user_id);
            } else {
                if ($category_name == "Supplier" || strpos($target, "supplier") !== false) {
                    $model_type = "supplier";
                    $query = $this->ci->db->get_where("vendors", [$model . "_id" => $member_id]);
                    if (0 < $query->num_rows()) {
                        $row = $query->row();
                        $model_id = $row->id;
                    } else {
                        $data = [];
                        $data[$model . "_id"] = $member_id;
                        $data["createdOn"] = date("Y-m-d H:i:s");
                        $data["createdBy"] = $user_id;
                        $data["modifiedOn"] = date("Y-m-d H:i:s");
                        $data["modifiedBy"] = $user_id;
                        $this->ci->db->insert("vendors", $data);
                        if (0 < $this->ci->db->affected_rows()) {
                            $model_id = $this->ci->db->insert_id();
                        }
                    }
                } else {
                    if ($category_name == "Partner" || strpos($target, "partner") !== false) {
                        $model_type = "partner";
                        $query = $this->ci->db->get_where("partners", [$model . "_id" => $member_id]);
                        if (0 < $query->num_rows()) {
                            $row = $query->row();
                            $model_id = $row->id;
                        } else {
                            $data = [];
                            $data[$model . "_id"] = $member_id;
                            $data["createdOn"] = date("Y-m-d H:i:s");
                            $data["createdBy"] = $user_id;
                            $data["modifiedOn"] = date("Y-m-d H:i:s");
                            $data["modifiedBy"] = $user_id;
                            $data["isThirdParty"] = "no";
                            $this->ci->db->insert("partners", $data);
                            if (0 < $this->ci->db->affected_rows()) {
                                $model_id = $this->ci->db->insert_id();
                            }
                        }
                    }
                }
            }
            if ($model_type && $model_id) {
                if (count($organizations) == 1) {
                    $accounts[0] = ["model" => $model, "model_id" => $model_id, "organization_id" => $organizations[0]["id"], "currency_id" => $organizations[0]["currencyID"], "account_name" => $account_name];
                    $this->insert_money_accounts($accounts, $member_id, $category, $user_id, $target, "", $lang);
                    $force_add = true;
                } else {
                    foreach ($organizations as $organization) {
                        $saved_accounts = [];
                        $query = [];
                        $table = $this->_table;
                        $this->_table = "accounts_details_lookup";
                        $query["select"] = "accounts_details_lookup.id as account_id, accounts_details_lookup.fullName as account_name, accounts_details_lookup.currencyCode, accounts_details_lookup.currency_id as currency_id";
                        $query["where"] = [["model_name", $model_name], ["member_id", $member_id], ["model_type", $model_type], ["organization_id", $organization["id"]]];
                        $saved_accounts = $this->load_all($query);
                        $this->_table = $table;
                        if (empty($saved_accounts)) {
                            $accounts[] = ["editable" => "yes", "organization_name" => $organization["name"], "organization_id" => $organization["id"], "currency" => $organization["currencyCode"], "currency_id" => $organization["currency_id"], "model" => $model, "model_id" => $model_id, "account_name" => $account_name];
                            if ($force_add) {
                                $this->insert_money_accounts($accounts, $member_id, $category, $user_id, $target, "", $lang);
                                $accounts = [];
                            }
                        } else {
                            foreach ($saved_accounts as $saved_account) {
                                $accounts[] = ["editable" => "no", "organization_name" => $organization["name"], "organization_id" => $organization["id"], "currency" => $organization["currencyCode"], "currency_id" => $saved_account["currency_id"], "model" => $model, "model_id" => $model_id, "account_name" => $saved_account["account_name"]];
                            }
                        }
                    }
                    $i = 0;
                    foreach ($accounts as $account) {
                        if ($account["editable"] == "no") {
                            $force_add = true;
                        }
                    }
                }
            }
        }
        $return = $force_add ? [] : ["accounts" => $accounts];
        return $return;
    }
    public function insert_money_accounts($data, $member_id, $category, $user_id, $target = "", $new_category_id = "", $lang = "")
    {
        if (!empty($data)) {
            $model = $data[0]["model"];
            $this->ci->load->model("contact_company_category");
            $this->ci->contact_company_category->fetch($category);
            $category_name = $this->ci->contact_company_category->get_field("keyName");
            $this->ci->config->load("accounts_map", true);
            $accountsMap = $this->ci->config->item("accounts_map");
            $bill_to = "";
            if (in_array($category_name, ["Client", "Internal"]) || strpos($target, "client") !== false) {
                $model_type = "client";
                if ($model == "company") {
                    $this->ci->load->model("company", "companyfactory");
                    $this->ci->company = $this->ci->companyfactory->get_instance();
                    $this->ci->company->fetch($member_id);
                    if ($target == "client" && 0 < $new_category_id) {
                        $this->ci->company->set_field("company_category_id", $new_category_id);
                        $this->ci->company->update();
                    }
                    $companyData = $this->ci->company->load_all_company_data($member_id, $lang != "" ? $lang : $this->ci->session->userdata("AUTH_language"));
                    $company = $companyData[0];
                    $bill_to .= $company["name"] . "\n";
                    $bill_to .= empty($company["address"]) || $company["address"] == "" ? "" : $company["address"] . "\n";
                    $bill_to .= empty($company["zip"]) || $company["zip"] == "" ? "" : $company["zip"];
                    $bill_to .= empty($company["city"]) || $company["city"] == "" ? "" : (empty($company["zip"]) || $company["zip"] == "" ? "" : ", ") . $company["city"];
                    $bill_to .= empty($company["state"]) || $company["state"] == "" ? "" : ((empty($company["zip"]) || $company["zip"] == "") && (empty($company["city"]) || $company["city"] == "") ? "" : ", ") . $company["state"];
                    $bill_to .= empty($company["countryName"]) || $company["countryName"] == "" ? "" : ((empty($company["zip"]) || $company["zip"] == "") && (empty($company["city"]) || $company["city"] == "") && (empty($company["state"]) || $company["state"] == "") ? "" : ", ") . $company["countryName"];
                } else {
                    if ($model == "contact") {
                        $this->ci->load->model("contact", "contactfactory");
                        $this->ci->contact = $this->ci->contactfactory->get_instance();
                        $this->ci->contact->fetch($member_id);
                        if ($target == "client" && 0 < $new_category_id) {
                            $this->ci->contact->set_field("contact_category_id", $new_category_id);
                            $this->ci->contact->update();
                        }
                        $contactData = $this->ci->contact->loadAllContactData($member_id, $lang != "" ? $lang : $this->ci->session->userdata("AUTH_language"));
                        $contact = $contactData[0];
                        $bill_to .= ($contact["father"] ? $contact["firstName"] . " " . $contact["father"] . " " . $contact["lastName"] : $contact["firstName"] . " " . $contact["lastName"]) . "\n";
                        $bill_to .= empty($contact["address1"]) || $contact["address1"] == "" ? "" : $contact["address1"] . "\n";
                        $bill_to .= empty($contact["address2"]) || $contact["address2"] == "" ? "" : $contact["address2"] . "\n";
                        $bill_to .= empty($contact["zip"]) || $contact["zip"] == "" ? "" : $contact["zip"];
                        $bill_to .= empty($contact["city"]) || $contact["city"] == "" ? "" : (empty($contact["zip"]) || $contact["zip"] == "" ? "" : ", ") . $contact["city"];
                        $bill_to .= empty($contact["state"]) || $contact["state"] == "" ? "" : ((empty($contact["zip"]) || $contact["zip"] == "") && (empty($contact["city"]) || $contact["city"] == "") ? "" : ", ") . $contact["state"];
                        $bill_to .= empty($contact["country"]) || $contact["country"] == "" ? "" : ((empty($contact["zip"]) || $contact["zip"] == "") && (empty($contact["city"]) || $contact["city"] == "") && (empty($contact["state"]) || $contact["state"] == "") ? "" : ", ") . $contact["country"];
                    }
                }
                $account_type_id = $accountsMap["Client"]["type_id"];
            } else {
                if ($category_name == "Supplier" || strpos($target, "supplier") !== false) {
                    $model_type = "supplier";
                    $this->ci->load->model("vendor");
                    $account_type_id = $accountsMap["Supplier"]["type_id"];
                } else {
                    if ($category_name == "Partner" || strpos($target, "partner") !== false) {
                        $model_type = "partner";
                        $this->ci->load->model("partner");
                        $account_type_id = $accountsMap["Partner"]["type_id"];
                    }
                }
            }
            $this->ci->load->model("account", "accountfactory");
            $this->ci->account = $this->ci->accountfactory->get_instance();
            foreach ($data as $account) {
                $max_numbers = $this->ci->account->load_max_numbers_per_acc_type($account_type_id, $account["organization_id"]);
                $this->ci->account->reset_fields();
                $this->ci->account->set_field("organization_id", $account["organization_id"]);
                $this->ci->account->set_field("currency_id", $account["currency_id"]);
                $this->ci->account->set_field("name", $account["account_name"]);
                $this->ci->account->set_field("number", isset($max_numbers[$account_type_id]) ? $max_numbers[$account_type_id] : 1);
                $this->ci->account->set_field("systemAccount", "no");
                $this->ci->account->set_field("description", NULL);
                $this->ci->account->set_field("model_id", $account["model_id"]);
                $this->ci->account->set_field("member_id", $member_id);
                $this->ci->account->set_field("model_name", $account["model"] == "company" ? "Company" : "Person");
                $this->ci->account->set_field("account_type_id", $account_type_id);
                $this->ci->account->set_field("model_type", $model_type);
                $this->ci->account->set_field("accountData", $bill_to);
                $this->ci->account->disable_builtin_logs();
                $this->ci->account->set_field("createdOn", date("Y-m-d H:i:s", time()));
                $this->ci->account->set_field("modifiedOn", date("Y-m-d H:i:s", time()));
                $this->ci->account->set_field("createdBy", $user_id);
                $this->ci->account->set_field("modifiedBy", $user_id);
                $this->ci->account->set_field("show_in_dashboard", "1");
                $this->ci->account->insert();
            }
            return true;
        } else {
            return false;
        }
    }
}

?>