<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contracts extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("party_category_language", "party_category_languagefactory");
        $this->party_category_language = $this->party_category_languagefactory->get_instance();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $this->load->model("sub_contract_type_language", "sub_contract_type_languagefactory");
        $this->sub_contract_type_language = $this->sub_contract_type_languagefactory->get_instance();
        $this->load->model("applicable_law_language", "applicable_law_languagefactory");
        $this->applicable_law_language = $this->applicable_law_languagefactory->get_instance();
        $this->load->model("contract_status");
        $this->load->model("contract_party", "contract_partyfactory");
        $this->contract_party = $this->contract_partyfactory->get_instance();
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
        $this->load->model("custom_fields_per_model_type", "custom_fields_per_model_typefactory");
        $this->custom_fields_per_model_type = $this->custom_fields_per_model_typefactory->get_instance();
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        $this->load->model("contract_user", "contract_userfactory");
        $this->contract_user = $this->contract_userfactory->get_instance();
        $this->load->model("contract_contributor", "contract_contributorfactory");
        $this->contract_contributor = $this->contract_contributorfactory->get_instance();
        $this->load->library("dmsnew");
        $this->load->model("iso_currency");
        $this->load->model("contract_renewal_notification_assigned_team", "contract_renewal_notification_assigned_teamfactory");
        $this->contract_renewal_notification_assigned_team = $this->contract_renewal_notification_assigned_teamfactory->get_instance();
        $this->load->model("contract_renewal_notification_email", "contract_renewal_notification_emailfactory");
        $this->contract_renewal_notification_email = $this->contract_renewal_notification_emailfactory->get_instance();
        $this->load->model("contract_collaborator", "contract_collaboratorfactory");
        $this->contract_collaborator = $this->contract_collaboratorfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("contract_signature_submission", "contract_signature_submissionfactory");
        $this->contract_signature_submission = $this->contract_signature_submissionfactory->get_instance();
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $this->load->model("email_notification_scheme");
        $this->load->model("contract_sla_management", "contract_sla_managementfactory");
        $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
    }
    public function mou(){

     $this->authenticate_exempted_actions();
            $this->index("mou");
    }
    //gect active contracts
    public function active_contracts()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("active_contracts"));
        $this->index("contract","Active");
    }
    ///get suspended
    public function suspended_contracts()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("suspended_contracts"));
        $this->index("contract","Suspended");
    }
    //get expired contracts
    public function expired_contracts()
    {
        $this->authenticate_exempted_actions();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("expired_contracts"));
        $this->index("contract","Expired");
    }

    public function index($commercial_type="contract", $specific_status="")
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $category = "contract";
        $data["model"] = $category;
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($category, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($category, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else {
                $page_size_modified = $this->input->post("pageSize") != $data["grid_saved_details"]["pageSize"];
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"]??"";
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]??"");
                }
            }

            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            $response = array_merge($response, $this->contract->k_load_all($filter, $sortable));
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"),$commercial_type=="mou"? $this->lang->line("mou_agreements"): $this->lang->line("contract"));
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["text_empty"] = $this->get_filter_operators("text_empty");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $this->load->model("contract_status_language", "contract_status_languagefactory");
            $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
            $data["statuses"] = $this->contract_status_language->load_list_per_language();
            $data["categories"] = $this->party_category_language->load_list_per_language();
            $data["types"] = $this->contract_type_language->load_list_per_language($commercial_type);
            $data["archivedValues"] = array_combine($this->contract->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";
            $data["statusValues"] = array_combine($this->contract->get("statusValues"), [$this->lang->line("either"), $this->lang->line("active"), $this->lang->line("inactive"),$this->lang->line("expired"),$this->lang->line("suspended")]);
            $data["defaultStatusValue"] = $specific_status?$specific_status:$this->lang->line("either");
            ///to categorize mou and contract
            $data["categoryValues"] = array_combine($this->contract->get("categoryValues"), [$this->lang->line("contract"), $this->lang->line("mou")]);
            $data["defaultCategoryValue"] = $commercial_type=="mou"?"mou":"contract";
            $data["stageValues"] = array_combine($this->contract->get("stageValues"), [$this->lang->line("either"), $this->lang->line("development"),$this->lang->line("implementation")]);
            $data["defaultStageValue"] = "Either";

            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->contract->get("modelName"));
            $data["priorities"] = array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $this->load->model(["provider_group"]);
            $data["assigned_teams"] = $this->provider_group->load_list([]);
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $data["applicable_laws"] = $this->applicable_law_language->load_list_per_language();
            $data["sub_type"] = $this->sub_contract_type_language->load_all_per_language_Advanced_Search();
            $this->load->model("country", "countryfactory");
            $this->country = $this->countryfactory->get_instance();
            $data["countries"] = $this->country->load_countries_list();
            $sysPref = $this->system_preference->get_key_groups();
            $SystemValues = $sysPref["ContractDefaultValues"];
            $data["contract_sla_feature"] = isset($SystemValues["AllowContractSLAManagement"]) && $SystemValues["AllowContractSLAManagement"] ? $SystemValues["AllowContractSLAManagement"] : "no";
            unset($data["statuses"][""]);
            unset($data["categories"][""]);
            unset($data["types"][""]);
            unset($data["countries"][""]);
            unset($data["applicable_laws"][""]);
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $commercial_type=="contract"?$this->includes("contract/index", "js"):$this->includes("contract/index_mous", "js");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("contract/show_hide_customer_portal", "js");
            $this->includes("money/js/accounting", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");

            $this->load->view("contracts/index", $data);
            $this->load->view("partial/footer");
        }
    }
   
    public function edit($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $response["result"] = false;
        $trigger = "edit_contract";
        if ($this->contract->fetch($contract_id)) {
            if (!$this->input->post(NULL)) {
                $data["statusValues"] = array_combine($this->contract->get("statusValues"), ["", $this->lang->line("active"), $this->lang->line("Inactive"),$this->lang->line("expired"),$this->lang->line("suspended")]);
                $data["defaultStatusValue"] ="Active";
               $cat=$this->contract->get_field("category");
                $data = array_merge($this->load_contract_data($contract_id, "edit"), $this->load_common_data($trigger,$cat));
                $data["sub_types"] = $this->sub_contract_type_language->load_list_per_type_per_language($data["contract"]["type_id"]);
                $data["assignees"] = ["" => "---"] + $this->user->load_users_list($data["contract"]["assigned_team_id"], ["key" => "id", "value" => "name"]);
                $this->load->model("user", "userfactory");
                $data['contract_id']=$contract_id;
                $this->load->model("department");
                $data['departments']=$this->department->load_list(["order_by" => ["name", "asc"]] ,["firstLine" => [""=>$this->lang->line("all")]]);
                //if reference_number is not set, then generate a new one
                if (empty($data["contract"]["reference_number"])) {
                    $data["contract"]["reference_number"] = $this->get_new_ref_number();
                }              
                $this->user = $this->userfactory->get_instance();
                $data["users_list"] = $this->user->load_available_list();
                $this->contract->update_recent_ids($contract_id, "contracts");
                $response["right_section_html"] = $this->load->view("contracts/edit/right_section", $data, true);
                $response["details_section_html"] = $this->load->view("contracts/edit/details_section", $data, true);
            } else {
                $response = $this->save($contract_id, $trigger);
                if ($response["result"]) {
                    $data = $this->load_contract_data($contract_id);
                    $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($data["contract"]["status_id"], $data["contract"]["workflow_id"]);
                    $data["available_statuses"] = $transitions_accessible["available_statuses"];
                    $data["status_transitions"] = $transitions_accessible["status_transitions"];
                    $data["statusValues"] = array_combine($this->contract->get("statusValues"), ["",$this->lang->line("active"), $this->lang->line("Inactive"),$this->lang->line("expired"),$this->lang->line("suspended")]);
                    $data["defaultStatusValue"] = "Active";
                    
                    $sysPref = $this->system_preference->get_key_groups();
                    $SystemValues = $sysPref["ContractDefaultValues"];
                    $data["contract_sla_feature"] = isset($SystemValues["AllowContractSLAManagement"]) && $SystemValues["AllowContractSLAManagement"] ? $SystemValues["AllowContractSLAManagement"] : "no";
                    $response["right_section_html"] = $this->load->view("contracts/view/right_section", $data, true);
                    $response["details_section_html"] = $this->load->view("contracts/view/details_section", $data, true);
                    $response["header_section_html"] = $this->load->view("contracts/view/header_section", $data, true);
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_contract_data($contract_id, $action = "view")
    {
        $data["model_code"] = $this->contract->get("modelCode");
        $data["contract"] = $this->contract->load_data($contract_id);
        $data['reference_number']=$data["contract"]["reference_number"]??$this->get_new_ref_number();//added by Atinga to suppress error
        $data["parties"] = $this->contract_party->fetch_contract_parties_data($contract_id);
        $data["contributors"] = $this->contract_contributor->load_contributors($contract_id);
        $data["collaborators"] = $this->contract_collaborator->load_collaborators($contract_id);
        $data["watchers"] = $data["contract"]["private"] ? $this->contract_user->load_users($contract_id) : [];
        $data["contract_watchers"] = $data["watchers"];// $data["contract"]["private"] ? $this->contract_user->load_users($contract_id) : [];//added by Atinga to suppress error
        $this->load->model("contract_renewal_history", "contract_renewal_historyfactory");
        $this->contract_renewal_history = $this->contract_renewal_historyfactory->get_instance();
        $data["renewal_history"] = $this->contract_renewal_history->load_history($contract_id);
        $this->load->model("contract_amendment_history", "contract_amendment_historyfactory");
        $this->contract_amendment_history = $this->contract_amendment_historyfactory->get_instance();
        $data["amendment_history"] = $this->contract_amendment_history->load_history($contract_id);
        $custom_fields = $action === "edit" ? $this->custom_field->get_field_html($this->contract->get("modelName"), $contract_id) : $this->custom_field->load_contract_custom_fields($contract_id, $this->contract->get("modelName"));
        $section_types = $this->custom_field->section_types;
        if (!empty($custom_fields)) {
            foreach ($custom_fields as $field) {
                if ($field["type"] === "lookup") {
                    $field["value"] = $this->custom_field->get_lookup_data($field);
                }
                $data["custom_fields"][$section_types[$field["type"]]][] = $field;
            }
        }
        $data["visible_to_cp"] = !strcmp($this->contract->get_field("channel"), $this->cp_channel) || $this->contract->get_field("visible_to_cp") == "1";
        $this->contract_renewal_notification_email->fetch(["contract_id" => $contract_id]);
        $data["notifications"]["emails"] = $this->contract_renewal_notification_email->get_field("emails");
        $selected_emails = explode(";", $data["notifications"]["emails"]);
        $users_emails = $this->user->load_active_emails();
        foreach ($selected_emails as $email) {
            if (!in_array($email, $users_emails)) {
                $users_emails[$email] = $email;
            }
        }
        $data["users_emails"] = array_map(function ($users_emails) {
            return ["email" => $users_emails];
        }, array_keys($users_emails));
        $data["notifications"]["teams"] = $this->contract_renewal_notification_assigned_team->load_assigned_teams($contract_id);
        $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($contract_id, $this->contract->get("_table"));
        $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
        $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
        if ($data["visible_to_cp"]) {
            $data["contract_id"] = $contract_id;
            $data["contract_watchers"] = $this->customer_portal_contract_watcher->get_all_contract_watchers($contract_id);
        }
        return $data;
    }
    private function load_common_data($trigger,$commercial_service_type="")
    {
        $commercial_service_type=$commercial_service_type??$this->contract->get_field("category"); //defeaut to contracts
        $data["categories"] = $this->party_category_language->load_list_per_language();
        $data["renewals"] = ["" => "---"] + array_combine($this->contract->get("renewal_values"), [$this->lang->line("one_time"), $this->lang->line("renewable_automatically"), $this->lang->line("renewable_with_notice"), $this->lang->line("unlimited_period"), $this->lang->line("other")]);
        $data["types"] = $this->contract_type_language->load_list_per_language(strtolower($commercial_service_type));
        $data["applicable_laws"] = $this->applicable_law_language->load_list_per_language();
        $data["priorities"] = array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $this->load->model(["provider_group"]);
        $data["assigned_teams"] = $this->provider_group->load_list([]);
        $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => ["" => $this->lang->line("none")]]);
        $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action($trigger);
        $data["today"] = date("Y-m-d");
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["users_list"] = $this->user->load_available_list();
        $data["assigned_teams_list"] = $this->provider_group->load_all();
        $this->load->model("country", "countryfactory");
        $this->country = $this->countryfactory->get_instance();
        $data["countries"] = $this->country->load_countries_list();
        $this->provider_group->fetch(["allUsers" => 1]);
        $data["assigned_team_id"] = $this->provider_group->get_field("id");
        return $data;
    }
    private function save($contract_id = 0, $trigger = "add_contract")
    {
        $data = $this->load_common_data("add_contract", $this->input->post("category"));
        $this->provider_group->fetch(["allUsers" => 1]);
        $data["all_teams_id"] = $this->provider_group->get_field("id");
        $this->load->model("party");
        $party_member_types = $this->input->post("party_member_type");
        $party_member_ids = $this->input->post("party_member_id");
        $party_categories = $this->input->post("party_category");
        $post_data = $this->input->post(NULL);
        if (empty($post_data["department_id"])) {
            $post_data["department_id"]=null;
        }
        //if stage is not set, then set it to development
        if (empty($post_data["stage"])) {
            $post_data["stage"] = "Development";
        }
        array_walk($post_data, [$this, "sanitize_post"]);
        $result = [];
        $check_sla = false;
        $sysPref = $this->system_preference->get_key_groups();
        $SystemValues = $sysPref["ContractDefaultValues"];
        $featureOption = isset($SystemValues["AllowContractSLAManagement"]) && $SystemValues["AllowContractSLAManagement"] ? $SystemValues["AllowContractSLAManagement"] : "no";
        if ($featureOption == "yes" && $contract_id) {
            $old_data = [];
            $old_data["contract"] = $this->contract->load_data($contract_id);
            $old_data["parties"] = $this->contract_party->load_contract_parties($contract_id);
            if ($party_member_types && $party_member_ids) {
                $parties_new_data = $this->party->return_parties($party_member_types, $party_member_ids);
                $parties_new_data_array = [];
                foreach ($parties_new_data as $party) {
                    $parties_new_data_array += [$party["party_id"] => $party["party_member_type"]];
                }
            }
            if (!empty($parties_new_data_array) && !empty($old_data["parties"])) {
                $result = array_diff_key($parties_new_data_array, $old_data["parties"]);
            } else {
                if (empty($parties_new_data_array) && !empty($old_data["parties"]) || !empty($parties_new_data_array) && empty($old_data["parties"])) {
                    $result += ["not equal"];
                }
            }
            if ($post_data["type_id"] != $old_data["contract"]["type_id"] || $post_data["priority"] != $old_data["contract"]["priority"] || !empty($result)) {
                $check_sla = true;
            }
        }
        if (!$contract_id || $contract_id && $this->input->post("type_id") !== $this->contract->get_field("type_id")) {
            if ($this->input->post("type_id")) {
                $workflow_applicable = $this->contract_status->load_workflow_status_per_type($post_data["type_id"]);
                if (empty($workflow_applicable)) {
                    $workflow_applicable = $this->contract_status->load_system_workflow_status();
                }
            }
            $post_data["status_id"] = isset($workflow_applicable["status_id"]) ? $workflow_applicable["status_id"] : "1";
            $post_data["workflow_id"] = isset($workflow_applicable["workflow_id"]) ? $workflow_applicable["workflow_id"] : "1";
        }
        $this->contract->set_fields($post_data);
        $this->contract->set_field("archived", $contract_id ? $this->contract->get_field("archived") : "no");
        $this->contract->set_field("channel", $contract_id ? $this->contract->get_field("channel") : $this->web_channel);
        $this->contract->set_field("modifiedByChannel", $this->web_channel);
        $this->contract->set_field("status", $contract_id ? $this->contract->get_field("status") : "Active");
        $this->contract->set_field("sub_type_id", $post_data["sub_type_id"] ?: NULL);
        $this->contract->set_field("priority", $post_data["priority"] ?? "medium");
        $this->contract->set_field("assigned_team_id", $data["all_teams_id"]);
        $this->contract->set_field("contract_date", $post_data["contract_date"] ?: NULL);
        $this->contract->set_field("requester_id", $post_data["requester_id"] ?: NULL);
        if ($contract_id != 0 && $post_data["value"] && !$post_data["currency_id"]) {
            $fields_validation = $this->contract->get("validate");
            $fields_validation["currency_id"] = ["required" => ["required" => true, "allowEmpty" => false, "rule" => "numeric", "message" => $this->lang->line("cannot_be_blank_rule")]];
            $this->contract->set("validate", $fields_validation);
        }
        $lookup_errors = $this->contract->get_lookup_validation_errors($this->contract->get("lookupInputsToValidate"), $post_data);
        if ($this->contract->validate() && !$lookup_errors) {
            $notify_before = $this->input->post("notify_me_before");
            $end_date = $this->input->post("end_date");
            if ($notify_before && $end_date && (!$notify_before["time"] || !$notify_before["time_type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                if ($is_not_nb) {
                    $response["result"] = false;
                    $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                } else {
                    $response["result"] = false;
                    $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                }
            } else {
                if ($notify_before && !$end_date) {
                    $response["result"] = false;
                    $response["validationErrors"]["end_date"] = $this->lang->line("cannot_be_blank_rule");
                } else {
                    if ($this->input->post("visible_in_cp") == 1) {
                        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
                        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
                        $this->load->model("contact", "contactfactory");
                        $this->contact = $this->contactfactory->get_instance();
                        $this->contact->fetch($this->input->post("requester_id"));
                        $add_requested_by_as_cp_user["result"] = true;
                        if (!$this->customer_portal_users->fetch(["contact_id" => $this->input->post("requester_id")])) {
                            $add_requested_by_as_cp_user = $this->customer_portal_users->add_requested_by_as_cp_user("contract");
                        }
                        if ($add_requested_by_as_cp_user["result"]) {
                            $this->contract->set_field("visible_to_cp", 1);
                        } else {
                            $response["result"] = false;
                            $response["validationErrors"]["visible_to_cp"] = $add_requested_by_as_cp_user["message"];
                        }
                    }
                    if (!isset($response["validationErrors"])) {
                        $result = $contract_id ? $this->contract->update() : $this->contract->insert();
                        $id = $contract_id;
                        $contract_id = $this->contract->get_field("id");
                        $original_contract_id = $this->contract->get_field("amendment_of");
                        $this->load->model("related_contract", "related_contractfactory");
                        $this->related_contract = $this->related_contractfactory->get_instance();
                        if (!empty($original_contract_id) && !$this->related_contract->fetch(["contract_a_id" => $original_contract_id, "contract_b_id" => $contract_id]) && !$this->related_contract->fetch(["contract_a_id" => $contract_id, "contract_b_id" => $original_contract_id])) {
                            $this->related_contract->set_field("contract_a_id", $original_contract_id);
                            $this->related_contract->set_field("contract_b_id", $contract_id);
                            $this->related_contract->set_field("comments", sprintf($this->lang->line("amendment_from_history_comment"), $this->contract->get("modelCode") . $contract_id, $this->contract->get("modelCode") . $original_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                            if ($this->related_contract->insert()) {
                                $this->related_contract->reset_fields();
                                $this->related_contract->set_field("contract_a_id", $contract_id);
                                $this->related_contract->set_field("contract_b_id", $original_contract_id);
                                $this->related_contract->set_field("comments", sprintf($this->lang->line("amendment_to_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                                if (!$this->related_contract->insert()) {
                                    $response["validation_errors"]["relation"] = $this->related_contract->get("validationErrors");
                                }
                            } else {
                                $response["validation_errors"]["relation"] = $this->related_contract->get("validationErrors");
                            }
                        }
                        if ($party_member_types && $party_member_ids) {
                            $parties_data = $this->party->return_parties($party_member_types, $party_member_ids);
                            if ($this->contract_party->delete_contract_parties($contract_id) && !empty($parties_data)) {
                                foreach ($parties_data as $key => $value) {
                                    $parties_data[$key]["contract_id"] = $contract_id;
                                    $parties_data[$key]["party_category_id"] = isset($party_categories[$key]) && !empty($party_categories[$key]) ? $party_categories[$key] : NULL;
                                }
                                $this->contract_party->insert_contract_parties($contract_id, $parties_data);
                            }
                            $this->contract->feed_related_contracts_to_parties($party_member_types, $party_member_ids, $contract_id);
                        }
                        $this->contract->feed_related_contracts_to_requester($this->input->post("requester_id"), $contract_id);
                        if ($trigger === "edit_contract") {
                            $this->save_watchers();
                        }
                        $notify_users = $this->insert_related_users($contract_id, $trigger);
                        if (!empty($post_data["customFields"]) && is_array($post_data["customFields"]) && count($post_data["customFields"])) {
                            foreach ($post_data["customFields"] as $key => $field) {
                                $post_data["customFields"][$key]["recordId"] = $contract_id;
                            }
                            $this->custom_field->update_custom_fields($post_data["customFields"]);
                        }
                        !$id ? $this->contract_sla_management->contract_sla($contract_id, $this->is_auth->get_user_id()) : ($check_sla ? $this->contract_sla_management->edit_contract_sla($contract_id, $this->is_auth->get_user_id()) : "");
                        $this->notify_before_end_date($contract_id);
                        $post_data["id"] = $contract_id;
                        $this->load->model("approval", "approvalfactory");
                        $this->approval = $this->approvalfactory->get_instance();
                        $this->load->model("signature", "signaturefactory");
                        $this->signature = $this->signaturefactory->get_instance();
                        if ($trigger !== "amend_contract") {
                            if (!$this->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                                $data["approval_center"] = $this->approval->update_approval_contract($post_data);
                            }
                            if (!$this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                                $data["signature_center"] = $this->signature->update_signature_contract($post_data);
                            }
                        }
                        if ($this->contract->get_field("authorized_signatory")) {
                            $this->signature->inject_authorized_signatory_in_signature_models($contract_id, $this->contract->get_field("authorized_signatory"));
                        }
                        if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                            $data = $this->contract->load_contract_details($contract_id);
                            $this->contract->trigger_web_hook($trigger == "add_contract" || $trigger == "amend_contract" ? "contract_created" : "contract_updated", $data);
                        }
                        $response["id"] = $contract_id;
                        $response["model_code"] = $this->contract->get("modelCode");
                        if ($result) {
                            $this->contract->update_recent_ids($contract_id, "contracts");
                            if ($trigger !== "edit_contract") {
                                $this->contract->inject_folder_templates($contract_id, "contract", $this->contract->get_field("type_id"));
                            }
                        }
                        $response["result"] = $result;
                    }
                }
            }
        } else {
            $response["result"] = false;
            $response["validationErrors"] = $this->contract->get_validation_errors($lookup_errors);
        }
        return $response;
    }
    private function insert_related_users($contract_id, $trigger)
    {
        $this->load->model("notification", "notificationfactory");
        $this->notification = $this->notificationfactory->get_instance();
        $contributors = $this->input->post("contributors") ?? [];
        $contributors_data = ["contract_id" => $contract_id, "users" => $contributors];
        $this->contract_contributor->insert_contributors($contributors_data);
        $watchers = $this->input->post("watchers", true) ?? [];
        if ($watchers) {
            if ($contributors) {
                $watchers = array_merge($watchers, array_diff($contributors, $watchers));
            }
            $assigned_to = $this->input->post("assignee_id");
            if (!in_array($assigned_to, $watchers)) {
                $watchers[] = $assigned_to;
            }
            if (!in_array($this->is_auth->get_user_id(), $watchers)) {
                $watchers[] = $this->is_auth->get_user_id();
            }
            $watchers_data = ["contract_id" => $contract_id, "users" => $watchers];
            $this->contract_user->insert_users($watchers_data);
        } else {
            $this->contract_user->insert_users(["contract_id" => $contract_id, "users" => []]);
        }
        if ($this->input->post("send_notifications_email") || $trigger == "edit_contract") {
            $this->load->model("language");
            $lang_id = $this->language->get_id_by_session_lang();
            $this->contract_type_language->fetch(["type_id" => $this->contract->get_field("type_id"), "language_id" => $lang_id]);
            $contract_data = ["id" => $contract_id, "type" => $this->contract_type_language->get_field("name"), "priority" => $this->contract->get_field("priority"), "contract_date" => $this->contract->get_field("contract_date"), "end_date" => $this->contract->get_field("end_date"), "description" => nl2br($this->contract->get_field("description"))];
            if ($trigger == "amend_contract") {
                $contract_data["original_contract_id"] = $this->input->post("original_contract_id");
                $this->contract->fetch($contract_data["original_contract_id"]);
                $contract_data["original_contract_name"] = $this->contract->get_field("name");
                $this->contract->fetch($contract_id);
            }
            $this->contract->send_notifications($trigger, ["contributors" => $contributors, "logged_in_user" => $this->is_auth->get_fullname()], $contract_data);
            if ($trigger == "add_contract") {
                $this->contract->send_notifications("add_contract_inform_assignee", ["contributors" => $contributors, "logged_in_user" => $this->is_auth->get_fullname()], $contract_data);
            }
        }
        $notifications["total_notifications"] = $this->notification->update_pending_notifications($watchers);
        return $notifications;
    }
    private function notify_before_end_date($contract_id, $action = "add")
    {
        $result = true;
        $notify_before = $this->input->post("notify_me_before", true);
        $end_date = $this->contract->get_field("end_date");
        $renewal_end_date = $this->input->post("required_fields[end_date]");
        if ($action !== "add") {
            $current_reminder = $this->reminder->load_notify_before_data_to_related_object($contract_id, $this->contract->get("_table"));
            if ($current_reminder && !$notify_before) {
                $result = $this->reminder->remind_before_due_date([], $current_reminder["id"]);
            }
        }
        if ($notify_before && $end_date || $notify_before && $renewal_end_date) {
            $reminder = ["user_id" => $this->is_auth->get_user_id(), "remindDate" => $end_date ? $end_date : $renewal_end_date, "contract_id" => $contract_id, "related_id" => $contract_id, "related_object" => $this->contract->get("_table"), "notify_before_time" => $notify_before["time"], "notify_before_time_type" => $notify_before["time_type"], "notify_before_type" => "popup_email"];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_expiry_date_message"), $this->lang->line("contract"), $this->contract->get("modelCode") . $contract_id, $end_date ? $end_date : $renewal_end_date);
            $result = $this->reminder->remind_before_due_date($reminder, isset($notify_before["id"]) ? $notify_before["id"] : NULL);
        }
        $notifications = $this->input->post("notifications", true);
        if (isset($notifications["emails"]) && $notifications["emails"]) {
            $this->contract_renewal_notification_email->delete(["where" => ["contract_id", $contract_id]]);
            $this->contract_renewal_notification_email->set_field("contract_id", $contract_id);
            $this->contract_renewal_notification_email->set_field("emails", implode(";", $notifications["emails"]));
            $this->contract_renewal_notification_email->insert();
            $this->contract_renewal_notification_email->reset_fields();
        }
        if (isset($notifications["teams"]) && $notifications["teams"]) {
            $this->contract_renewal_notification_assigned_team->delete(["where" => ["contract_id", $contract_id]]);
            foreach ($notifications["teams"] as $team) {
                $this->contract_renewal_notification_assigned_team->set_field("contract_id", $contract_id);
                $this->contract_renewal_notification_assigned_team->set_field("assigned_team", $team);
                $this->contract_renewal_notification_assigned_team->insert();
                $this->contract_renewal_notification_assigned_team->reset_fields();
            }
        }
        $this->contract->fetch($contract_id);
        // Create a task for the new contract
        $task_id = $this->create_contract_task($this->contract);

        return $result;
    }
    public function view($contract_id = 0, $edit_mode = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if (!$this->validate_id($contract_id) || !$this->contract->fetch($contract_id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("contracts/index");
        }
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract"));
        $data = $this->load_contract_data($contract_id);
        if (!$this->input->is_ajax_request()) {
            $data["statusValues"] = array_combine($this->contract->get("statusValues"), [$this->lang->line("none"),$this->lang->line("active"), $this->lang->line("Inactive"),$this->lang->line("expired"),$this->lang->line("suspended")]);
            $data["defaultStatusValue"] =$contract_id? $data["contract"]["status"]:"Active";
            $data["model_code"] = $this->contract->get("modelCode");
            $data["category"] = $this->contract->get("category");
            $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($data["contract"]["status_id"], $data["contract"]["workflow_id"]);
            $data["available_statuses"] = $transitions_accessible["available_statuses"];
            $data["status_transitions"] = $transitions_accessible["status_transitions"];
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_contract_comment");
            $data["visible_to_cp"] = !strcmp($this->contract->get_field("channel"), $this->cp_channel) || $this->contract->get_field("visible_to_cp") == "1";
            $sysPref = $this->system_preference->get_key_groups();
            $SystemValues = $sysPref["ContractDefaultValues"];
            $data["contract_sla_feature"] = isset($SystemValues["AllowContractSLAManagement"]) && $SystemValues["AllowContractSLAManagement"] ? $SystemValues["AllowContractSLAManagement"] : "no";
            $data["enableContractRenewalFeature"] = $SystemValues["EnableContractRenewalFeature"];
            $data["is_edit_mode"] = $edit_mode;
            $this->load->model("document_management_system", "document_management_systemfactory");
            $this->document_management_system = $this->document_management_systemfactory->get_instance();
            $data["related_documents_count"] = $this->document_management_system->count_contract_related_documents($contract_id);
            $this->contract_approval_submission->fetch(["contract_id" => $contract_id]);
            $data["overall_approval_status"] = $this->contract_approval_submission->get_field("status");
            $this->contract_signature_submission->fetch(["contract_id" => $contract_id]);
            $data["overall_signature_status"] = $this->contract_signature_submission->get_field("status");
            $this->contract->update_recent_ids($contract_id, "contracts");

            $this->load_javascript_libraries();
            $this->load->view("partial/header");
            $this->load->view("contracts/view/form", $data);
            $this->load->view("partial/footer");
        } else {
           $response["right_section_html"] =  $this->contract->get("category")=="mou"?$this->load->view("contracts/view/right_section_mou", $data, true):$this->load->view("contracts/view/right_section", $data, true);
            $response["details_section_html"] = $this->load->view("contracts/view/details_section", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function get_all_comments()
    {
        $id = $this->input->post("id");
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("text");
        $data["contract_id"] = $id;
        $data["contract_comments"] = $this->contract_comment->load_all_comments($id);
        if (!empty($data)) {
            $response["nb_of_notes_history"] = $this->contract_comment->count_all_contract_comments($id);
            $data["contract_comments_pagination"] = $this->get_comments_pagination_data();
            $response["html"] = $this->load->view("contracts/view/comments/comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    private function get_comments_pagination_data()
    {
        $paginationConfig = [];
        $paginationConfig["pagination"]["paginationLinks"] = $this->contract_comment->get("paginationLinks");
        $paginationConfig["pagination"]["configPage"] = $this->contract_comment->pagination_config("page");
        $paginationConfig["pagination"]["configInPage"] = $this->contract_comment->pagination_config("inPage");
        $paginationConfig["pagination"]["paginationTotalRows"] = $this->contract_comment->get("paginationTotalRows");
        return $paginationConfig["pagination"];
    }
    public function get_all_core_and_cp_comments()
    {
        $id = $this->input->post("id");
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("text");
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $data = [];
        $data["contract_comments"] = $this->contract_comment->fetch_all_contract_core_and_cp_comments($id);
        if (!empty($data)) {
            $response["nb_of_notes_history"] = $this->contract_comment->count_all_contract_core_and_cp_comments($id);
            $data["contract_comments_pagination"] = $this->get_comments_pagination_data();
            $response["html"] = $this->load->view("contracts/view/comments/comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function get_all_email_comments()
    {
        $id = $this->input->post("id");
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("text");
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $data["contract_id"] = $id;
        $data["contract_comments_emails"] = $this->contract_comment->fetch_all_contract_comments_emails($id);
        if (!empty($data)) {
            $response["nb_of_notes_history"] = $this->contract_comment->count_all_contract_comments_emails($id);
            $data["contract_comments_pagination"] = $this->get_comments_pagination_data();
            $response["html"] = $this->load->view("contracts/view/comments/email_comments", $data, true);
            $response["status"] = true;
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function move_status()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $contract_id = $this->input->post("contract_id", true);
        $status_id = $this->input->post("status_id", true);
        $response["result"] = false;
        $needs_approval = false;
        if ($this->contract->fetch($contract_id) && $contract_id && $status_id) {
            $transition_id = $this->input->post("transition_id", true);
            $old_status = $this->contract->get_field("status_id");
            $type_id = $this->contract->get_field("type_id");
            if ($transition_id && $this->contract_workflow_status_transition->fetch($transition_id) && $this->contract_workflow_status_transition->get_field("approval_needed") && $this->contract_approval_submission->fetch(["contract_id" => $contract_id]) && $this->contract_approval_submission->get_field("status") !== "approved") {
                $response["message"] = $this->lang->line("needs_approval_before");
                $response["result"] = false;
                $needs_approval = true;
            }
            if (!$needs_approval) {
                $workflow_applicable = 0 < $this->contract->get_field("workflow_id") ? $this->contract->get_field("workflow_id") : 1;
                $allowed_statuses = $this->contract_workflow_status_transition->load_available_steps($old_status, $workflow_applicable);
                if ($status_id === $old_status || !in_array($status_id, array_keys($allowed_statuses["available_statuses"]))) {
                    $response["message"] = $this->lang->line("permission_not_allowed");
                } else {
                    $this->load->model("contract_fields", "contract_fieldsfactory");
                    $this->contract_fields = $this->contract_fieldsfactory->get_instance();
                    $this->contract_fields->load_all_fields($type_id);
                    $data = $this->contract_fields->return_screen_fields($contract_id, $transition_id);
                    if ($transition_id && $data) {
                        $data["title"] = $this->contract_workflow_status_transition->get_field("name");
                        $response["result"] = true;
                        $response["screen_html"] = $this->load->view("templates/screen_fields", $data, true);
                    } else {
                        $this->contract->fetch($contract_id);
                        $this->contract->set_field("status_id", $status_id);
                        if (!$this->contract->update()) {
                            $response["message"] = $this->lang->line("contract_move_status_invalid");
                            $response["validation_errors"] = $this->contract->get("validationErrors");
                        } else {
                            $this->load->model("approval", "approvalfactory");
                            $this->approval = $this->approvalfactory->get_instance();
                            $overall_status = $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
                            if ($overall_status != "") {
                                $response["overall_status"] = $overall_status;
                            }
                            $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($status_id, $this->contract->get_field("workflow_id"));
                            $data["available_statuses"] = $transitions_accessible["available_statuses"];
                            $data["status_transitions"] = $transitions_accessible["status_transitions"];
                            $data["contract"]["id"] = $contract_id;
                            $this->contract_sla_management->contract_sla($contract_id, $this->is_auth->get_user_id());
                            $status = $this->contract_status->load_status_details($status_id);
                            $old_status_details = $this->contract_status->load_status_details($old_status);
                            $response["status_name"] = $status["status_name"];
                            $response["status_color"] = $status["status_color"];
                            $response["html"] = $this->load->view("contracts/view/header/statuses_section", $data, true);
                            $contributors = $this->contract_contributor->load_contributors($contract_id);
                            $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                            $notify["logged_in_user"] = $this->is_auth->get_fullname();
                            $this->contract->send_notifications("edit_contract_status", $notify, ["id" => $contract_id, "status" => $status["status_name"], "old_status" => $old_status_details["status_name"]]);
                            if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                                $webhook_data = $this->contract->load_contract_details($contract_id);
                                $this->contract->trigger_web_hook("contract_status_updated", $webhook_data);
                            }
                            $response["message"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("contract"));
                            $response["result"] = true;
                        }
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function save_transition_screen_fields($contract_id = 0, $transition = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = true;
        $this->load->model("contract_fields", "contract_fieldsfactory");
        $this->contract_fields = $this->contract_fieldsfactory->get_instance();
        $this->contract_workflow_status_transition->fetch($transition);
        $status_id = $this->contract_workflow_status_transition->get_field("to_step");
        if ($this->input->post(NULL)) {
            $validation = $this->contract_fields->validate_fields($transition);
            $response["result"] = $validation["result"];
            if (!$validation["result"]) {
                $response["validation_errors"] = $validation["errors"];
            } else {
                $this->contract->fetch($contract_id);
                $this->contract->set_field("status_id", $status_id);
                if ($this->contract->update()) {
                    $this->load->model("approval", "approvalfactory");
                    $this->approval = $this->approvalfactory->get_instance();
                    $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
                    $save_result = $this->contract_fields->save_fields($contract_id);
                    $this->contract_sla_management->contract_sla($contract_id, $this->is_auth->get_user_id());
                    if (!$save_result["result"]) {
                        $response["result"] = $save_result["result"];
                        $response["validation_errors"] = $save_result["validation_errors"];
                    } else {
                        $data = $this->load_contract_data($contract_id);
                        $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($data["contract"]["status_id"], $data["contract"]["workflow_id"]);
                        $data["available_statuses"] = $transitions_accessible["available_statuses"];
                        $data["status_transitions"] = $transitions_accessible["status_transitions"];
                        $response["right_section_html"] = $this->load->view("contracts/view/right_section", $data, true);
                        $response["details_section_html"] = $this->load->view("contracts/view/details_section", $data, true);
                        $response["header_section_html"] = $this->load->view("contracts/view/header_section", $data, true);
                        $response["display_message"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("contract"));
                        if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                            $this->contract->trigger_web_hook("contract_status_updated", $data);
                        }
                    }
                } else {
                    $response["result"] = false;
                    $response["display_message"] = $this->lang->line("workflowActionInvalid");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function set_privacy()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => true];
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function approval_center($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        if ($this->contract->fetch($contract_id)) {
            $this->load->model("contract_approval_status", "contract_approval_statusfactory");
            $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
            $this->load->model("contract_approval_history", "contract_approval_historyfactory");
            $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
            $data = false;
            if ($this->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                $data["overall_status"] = $this->contract_approval_submission->get_field("status");
                $data["approval_history"] = $this->contract_approval_history->load_history($contract_id);
                $data["approval_center"] = $this->contract_approval_status->load_approval_center_for_contract($contract_id);
                $this->load->model("contract_approval_negotiation_comment", "contract_approval_negotiation_commentfactory");
                $this->contract_approval_negotiation_comment = $this->contract_approval_negotiation_commentfactory->get_instance();
                $this->load->helper("revert_comment_html");
                $data["negotiations"] = $this->contract_approval_negotiation_comment->load_negotiations_for_contract($contract_id);
                $data["manager"] = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
            } else {
                if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                    $data["enable_approve_all"] = true;
                }
            }
            $data["contract"]["id"] = $contract_id;
            $data["contract"]["name"] = $this->contract->get_field("name");
            $data["contract"]["workflow_id"] = $this->contract->get_field("workflow_id");
            $this->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
            $this->contract_workflow_status_relation = $this->contract_workflow_status_relationfactory->get_instance();
            $data["contract"]["approval_start_point"] = $this->contract_workflow_status_relation->get_approval_start_point_status($data["contract"]["workflow_id"]);
            $data["model_code"] = $this->contract->get("modelCode");
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_center/index", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function approve_all_contract()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("contract_approval_history", "contract_approval_historyfactory");
        $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $response["result"] = false;
        $contract_id = $this->input->post("contract_id");
        if ($contract_id) {
            $available_record = false;
            if ($this->contract_approval_submission->fetch(["contract_id" => $contract_id])) {
                $available_record = true;
            }
            $this->contract_approval_submission->set_field("status", "approved");
            $this->contract_approval_submission->set_field("contract_id", $contract_id);
            $result = $available_record ? $this->contract_approval_submission->update() : $this->contract_approval_submission->insert();
            if ($result && $this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $this->contract_signature_submission->set_field("status", "awaiting_signature");
                $this->contract_signature_submission->update();
            }
            $this->contract_approval_history->set_field("contract_id", $contract_id);
            $this->contract_approval_history->set_field("done_on", date("Y-m-d H:i:s"));
            $this->contract_approval_history->set_field("done_by", $this->is_auth->get_user_id());
            $this->contract_approval_history->set_field("done_by_type", "user");
            $this->contract_approval_history->set_field("from_action", "-");
            $this->contract_approval_history->set_field("to_action", "approved");
            $this->contract_approval_history->set_field("label", "None");
            $this->contract_approval_history->set_field("action", "approve");
            $this->contract_approval_history->set_field("enforce_previous_approvals", "0");
            $this->contract_approval_history->set_field("comment", $this->lang->line("contract_approved_all_comment"));
            $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
            $this->contract_approval_history->set_field("approval_channel", "A4L");
            if ($this->contract_approval_history->insert()) {
                $response["result"] = true;
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function submit_for_approval()
    {// approving a contract
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("contract_approval_history", "contract_approval_historyfactory");
        $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $response["result"] = false;
        $response["validation_errors"] = [];
        $post_data = $this->input->post(NULL);
        if (!empty($post_data)) {
            $this->contract->fetch($post_data["contract_id"]);
            if ($this->contract_approval_status->fetch($post_data["contract_approval_status_id"])) {
                $old_status = $this->contract_approval_status->get_field("status");
                if ($post_data["status"] === "rejected") {
                    $fields_validation = $this->contract_approval_history->get("validate");
                    $fields_validation["comment"] = ["required" => ["required" => true, "allowEmpty" => false, "rule" => ["minLength", 1], "message" => $this->lang->line("cannot_be_blank_rule")]];
                    $this->contract_approval_history->set("validate", $fields_validation);
                    if (!empty($post_data["documents"]["id"]) && empty($post_data["documents"]["status_id"])) {
                        $response["validation_errors"]["status_id"] = $this->lang->line("cannot_be_blank_rule");
                    }
                    $this->contract->send_notifications("contract_rejected", ["contributors" => [], "logged_in_user" => $this->is_auth->get_fullname()], ["id" => $post_data["contract_id"]]);
                }
                $this->contract_approval_history->set_fields($post_data);
                $this->contract_approval_history->set_field("done_on", $post_data["done_on"] ? $post_data["done_on"] . " " . date("H:i:s") : "");
                $this->contract_approval_history->set_field("done_by", $this->is_auth->get_user_id());
                $this->contract_approval_history->set_field("done_by_type", "user");
                $this->contract_approval_history->set_field("from_action", $old_status);
                $this->contract_approval_history->set_field("to_action", $post_data["status"]);
                $this->contract_approval_history->set_field("label", $this->contract_approval_status->get_field("label"));
                $this->contract_approval_history->set_field("action", "approve");
                $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
                $this->contract_approval_history->set_field("approval_channel", "A4L");
                if ($this->contract_approval_history->validate() && empty($response["validation_errors"])) {
                    $this->contract_approval_history->insert();
                    $this->contract_approval_status->set_field("status", $post_data["status"]);
                    $this->contract_approval_status->update();
                    $contract_approval_status_id = $this->contract_approval_status->get_field("id");
                    $order = $this->contract_approval_status->get_field("rank");
                    if ($post_data["enforce_previous_approvals"]) {
                        $this->contract_approval_status->enforce_previous_approvals($post_data["contract_id"], $contract_approval_status_id, $post_data["enforce_previous_approvals"], $this->contract_approval_status->get_field("rank"));
                    } else {
                        if ($post_data["status"] === "approved" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                            if (!$this->contract_approval_status->load_pending_approvals($post_data["contract_id"])) {
                                $this->contract_approval_submission->set_field("status", "approved");
                                if ($this->contract_approval_submission->update()) {
                                    $contributors = $this->contract_contributor->load_contributors($post_data["contract_id"]);
                                    $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                                    $notify["logged_in_user"] = $this->is_auth->get_fullname();
                                    $this->contract->send_notifications("contract_approved", $notify, ["id" => $post_data["contract_id"]]);
                                    if ($this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                                        $this->contract_signature_submission->set_field("status", "awaiting_signature");
                                        $this->contract_signature_submission->update();
                                        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
                                        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
                                        $next_signees = $this->contract_signature_status->load_next_order(0, $post_data["contract_id"]);
                                        if ($next_signees && !empty($next_signees)) {
                                            $signees["users"] = [];
                                            $signees["collaborators"] = [];
                                            $signees["user_groups"] = [];
                                            foreach ($next_signees as $next) {
                                                $signature_data = $this->contract_signature_status->load_signature_data($next["id"]);
                                                $signature_data["users"] != "" ? $signees["users"] : "";
                                                $signature_data["collaborators"] != "" ? $signees["collaborators"] : "";
                                                $signature_data["user_groups"] != "" ? $signees["user_groups"] : "";
                                            }
                                            $this->load->model("signature", "signaturefactory");
                                            $this->signature = $this->signaturefactory->get_instance();
                                            $this->signature->notify_required_signees($signees, $post_data["contract_id"]);
                                        }
                                    }
                                }
                            } else {
                                if ($old_status === "rejected") {
                                    $this->contract_approval_submission->set_field("status", "awaiting_approval");
                                    $this->contract_approval_submission->update();
                                }
                                $this->load->model("approval", "approvalfactory");
                                $this->approval = $this->approvalfactory->get_instance();
                                $response["email_sent"] = $this->approval->notify_next_approvers($order, $post_data["contract_id"]);
                            }
                        }
                    }
                    if ($post_data["status"] === "rejected" && $this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                        $this->contract_approval_submission->set_field("status", "awaiting_revision");
                        $this->contract_approval_submission->update();
                    }
                    if (!empty($post_data["documents"]["id"])) {
                        $this->load->model("document_management_system", "document_management_systemfactory");
                        $this->document_management_system = $this->document_management_systemfactory->get_instance();
                        foreach ($post_data["documents"]["id"] as $doc_id) {
                            $this->document_management_system->fetch($doc_id);
                            $this->document_management_system->set_field("document_status_id", $post_data["documents"]["status_id"]);
                            $this->document_management_system->update();
                            $this->document_management_system->reset_fields();
                        }
                    }
                    $response["overall_status"] = $this->contract_approval_submission->get_field("status");
                    $response["result"] = true;
                } else {
                    $response["result"] = false;
                    $response["validation_errors"] = array_merge($response["validation_errors"], $this->contract_approval_history->get("validationErrors"));
                }
            }
        } else {
            $get_data = $this->input->get(NULL);
            $data = [];
            if (isset($get_data["contract_approval_status_id"]) && $this->contract_approval_status->fetch($get_data["contract_approval_status_id"])) {
                $contract_id = $this->contract_approval_status->get_field("contract_id");
                $this->contract_approval_submission->fetch(["contract_id" => $contract_id]);
                if ($this->contract_approval_submission->get_field("status") != "drafting") {
                    $rank = $this->contract_approval_status->get_field("rank");
                    $allowed = false;
                    $assignees = $this->contract_approval_status->load_approval_data($get_data["contract_approval_status_id"]);
                    if ($assignees["users"] && in_array($this->is_auth->get_user_id(), explode(",", $assignees["users"]))) {
                        $allowed = true;
                    }
                    if ($assignees["user_groups"] && in_array($this->session->userdata("AUTH_user_group_id"), explode(",", $assignees["user_groups"]))) {
                        $allowed = true;
                    }
                    if (!$allowed) {
                        $response["display_message"] = $get_data["approved"] == "true" ? $this->lang->line("no_permission_to_approve") : $this->lang->line("no_permission_to_reject");
                    } else {
                        $approve = $get_data["approved"] == "true";
                        $data["title"] = $approve ? $this->lang->line("approve") : $this->lang->line("reject");
                        $data["approve"] = $approve ? true : false;
                        $data["today"] = date("Y-m-d", time());
                        if ($approve) {
                            $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
                            $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
                            $data["signatures"] = $this->user_signature_attachment->load_all(["where" => ["user_id", $this->is_auth->get_user_id()], "order_by" => ["is_default", "DESC"]]);
                            $data["signature_path"] = "modules/contract/contracts/get_signature_picture/";
                        } else {
                            $previous_ranks = $this->contract_approval_status->load_previous_ranks($contract_id, $rank);
                            if (array_filter($previous_ranks)) {
                                $data["previous_ranks"] = ["" => ""] + $previous_ranks;
                            }
                        }
                        $response["result"] = true;
                        $response["html"] = $this->load->view("contracts/view/approval_center/form", $data, true);
                    }
                } else {
                    $this->contract->fetch($contract_id);
                    $contract["workflow_id"] = $this->contract->get_field("workflow_id");
                    $this->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
                    $this->contract_workflow_status_relation = $this->contract_workflow_status_relationfactory->get_instance();
                    $approval_start_point = $this->contract_workflow_status_relation->get_approval_start_point_status($contract["workflow_id"]);
                    $response["display_message"] = $this->lang->line("approval_status_should_be_awaiting_approval_in_order_to_approve") . ($approval_start_point ? ", " . sprintf($this->lang->line("click_approval_start_point"), $approval_start_point) : ", " . sprintf($this->lang->line("set_approval_start_point_then_click_it"), base_url() . "contract_workflows/index" . (isset($contract["workflow_id"]) ? "#" . $contract["workflow_id"] : "")));
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function documents($contract_id = "")
    {
        $data = [];
        $this->contract->fetch($contract_id);
        $contract = $this->contract->get_fields();
        $data["contract"] = $contract;
        $data["contract_id"] = $contract_id;
        $data["module"] = "contract";
        $data["module_record"] = "contract";
        $data["module_record_id"] = $contract_id;
        $data["module_controller"] = "contracts";
        $data["module_prefix"] = "contract";
        $data["model_code"] = $this->contract->get("modelCode");
        $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
        $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
        $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
        $data["urlGrid"] = true;
        $data["crumbParent"] = $this->contract->get("modelCode") . $contract_id;
        $data["A4L_doc_tab_name"] = $this->lang->line("a4l_documents");
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->load->model("user_preference");
        $data["related_documents_count"] = $this->document_management_system->count_contract_related_documents($contract_id);
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
        $this->load->model("integration");
        $data["integrations"] = $this->integration->find_all(["is_active", 1]);
        $response["html"] = $this->load->view("documents_management_system/index", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_documents($module = "", $module_record_id = "", $lineage = "", $term = "")
    { $module = $module ? $module : $this->input->post("module");
        $module_record_id = $module_record_id ? $module_record_id : $this->input->post("module_record_id");
        $lineage = $lineage ? $lineage : $this->input->post("lineage");
        $term = $term ? $term : $this->input->post("term"); 
      
        $response = $this->dmsnew->load_documents(["module" => $module, "module_record_id" =>$module_record_id, "lineage" => $lineage, "term" =>$term]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    public function upload_file()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data = $this->load_documents_form_data($contract_id, $this->input->get("lineage", true));
            $data["title"] = $this->lang->line("upload_file");
            $data["module"] = "contract";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/upload_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_documents_form_data($contract_id, $lineage)
    {
        $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
        $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
        $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
        $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
        $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
        $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
        $data["module_record"] = "contract";
        $data["module_record_id"] = $contract_id;
        return $data;
    }
    public function upload_signed_document()
    {
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data = $this->load_documents_form_data($contract_id, false);
            $data["title"] = $this->lang->line("upload_signed_document");
            $data["module"] = "contract";
            $response["result"] = false;
            if ($this->input->get("contract_signature_status_id")) {
                $data["contract_signature_status_id"] = $this->input->get("contract_signature_status_id");
                if ($this->contract_signature_status->fetch($data["contract_signature_status_id"])) {
                    $signees = $this->contract_signature_status->load_signature_data($data["contract_signature_status_id"]);
                    $allowed = false;
                    if ($signees["users"] && in_array($this->is_auth->get_user_id(), explode(",", $signees["users"]))) {
                        $allowed = true;
                    }
                    if ($signees["user_groups"] && in_array($this->session->userdata("AUTH_user_group_id"), explode(",", $signees["user_groups"]))) {
                        $allowed = true;
                    }
                    if ($signees["is_requester_manager"]) {
                        $this->contract->fetch($contract_id);
                        $manager = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
                        if ($manager && $manager["id"] == $this->is_auth->get_user_id()) {
                            $allowed = true;
                        }
                    }
                    if (!$allowed) {
                        $response["message"] = $this->lang->line("no_permission_to_sign");
                    } else {
                        $response["result"] = true;
                        $response["html"] = $this->load->view("documents_management_system/upload_form", $data, true);
                    }
                } else {
                    $response["message"] = $this->lang->line("invalid_record");
                }
            }
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");
                $this->document_management_system = $this->document_management_systemfactory->get_instance();
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
                if ($response["status"] && $this->input->post("contract_signature_status_id")) {
                    if ($this->contract_signature_status->fetch($this->input->post("contract_signature_status_id"))) {
                        $this->update_contract_signature_status($this->input->post("module_record_id"));
                        $this->contract_signature_submission->fetch(["contract_id" => $this->input->post("module_record_id")]);
                        $response["overall_status"] = $this->contract_signature_submission->get_field("status");
                        $response["message"] = $this->lang->line("updates_saved_successfully");
                    } else {
                        $response["message"] = $this->lang->line("invalid_record");
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function update_contract_signature_status($contract_id)
    {
        $this->load->model("contract_signature_history", "signature_historyfactory");
        $this->contract_signature_history = $this->signature_historyfactory->get_instance();
        $new_status = "signed";
        $this->contract_signature_history->set_field("done_on", date("Y-m-d H:i:s"));
        $this->contract_signature_history->set_field("done_by", $this->is_auth->get_user_id());
        $this->contract_signature_history->set_field("done_by_type", "user");
        $this->contract_signature_history->set_field("from_action", $this->contract_signature_status->get_field("status"));
        $this->contract_signature_history->set_field("to_action", $new_status);
        $this->contract_signature_history->set_field("label", $this->contract_signature_status->get_field("label"));
        $this->contract_signature_history->set_field("action", "sign");
        $this->contract_signature_history->set_field("contract_id", $contract_id);
        $this->contract_signature_history->set_field("comment", $this->lang->line("contract_signed_comment"));
        if ($this->contract_signature_history->insert()) {
            $this->contract_signature_status->set_field("status", $new_status);
            $this->contract_signature_status->update();
        }
        $this->contract->fetch($contract_id);
        if (!$this->contract_signature_status->load_pending_signatures($contract_id)) {
            if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $this->contract_signature_submission->set_field("status", "signed");
                $this->contract_signature_submission->update();
                $this->contract_signature_submission->reset_fields();
            }
            $contributors = $this->contract_contributor->load_contributors($contract_id);
            $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
            $notify["logged_in_user"] = $this->is_auth->get_fullname();
            $this->contract->send_notifications("contract_signed", $notify);
        } else {
            $order = $this->contract_signature_status->get_field("rank");
            $next_signees = $this->contract_signature_status->load_next_order($order, $contract_id);
            if ($next_signees && !empty($next_signees)) {
                foreach ($next_signees as $next) {
                    $this->contract_signature_status->fetch($next["id"]);
                    $this->contract_signature_status->set_field("status", "awaiting_signature");
                    $this->contract_signature_status->update();
                    $this->contract_signature_status->reset_fields();
                    $signature_data = $this->contract_signature_status->load_signature_data($next["id"]);
                    $signature_data["users"] != "" ? $signees["users"] : "";
                    $signature_data["collaborators"] != "" ? $signees["collaborators"] : "";
                    $signature_data["user_groups"] != "" ? $signees["user_groups"] : "";
                }
                $this->load->model("signature", "signaturefactory");
                $this->signature = $this->signaturefactory->get_instance();
                $this->signature->notify_required_signees($signees, $contract_id);
            }
        }
    }
    public function upload_directory()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data["title"] = $this->lang->line("upload_directory");
            $data["module"] = "contract";
            $data["module_record_id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_directory_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDir"]["name"][0]) {
                $response[0]["status"] = false;
                $response[0]["validation_errors"]["uploadDir"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_directory(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDir", "folderext" => $this->input->post("folderext")]);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function create_folder()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data["title"] = $this->lang->line("create_folder");
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $data["module_record_id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dmsnew->create_folder(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "visible_in_cp" => 0, "name" => $this->input->post("name")]);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function rename_folder()
    {
        if (!$this->input->post(NULL, true)) {
            $document_id = $this->input->get("document_id", true);
            $this->document_management_system->fetch($document_id);
            $data["title"] = $this->lang->line("rename_folder");
            $data["name"] = $this->document_management_system->get_field("name");
            $data["document_id"] = $document_id;
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        } else {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dmsnew->rename_document("contract", $this->input->post("document_id"), "folder", $this->input->post("name"));
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function rename_file()
    {
        if (!$this->input->post(NULL, true)) {
            $document_id = $this->input->get("document_id", true);
            $this->document_management_system->fetch($document_id);
            $data["title"] = $this->lang->line("rename_file");
            $data["name"] = $this->document_management_system->get_field("name");
            $data["document_id"] = $document_id;
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/document_name_form", $data, true);
        } else {
            $post_data = $this->input->post(NULL, true);
            if (!$post_data["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["name"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $response = $this->dmsnew->rename_document("contract", $this->input->post("document_id"), "file", $this->input->post("name"));
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_documents()
    {
        $response = $this->dmsnew->edit_documents(json_decode($this->input->post("models"), true));
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function share_folder()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response["status"] = true;
        if ($this->dmsnew->model->fetch(["module" => "contract", "id" => $this->input->post("folder_id")])) {
            $this->load->model("document_managment_user", "document_managment_userfactory");
            $this->document_managment_user = $this->document_managment_userfactory->get_instance();
            if ($this->input->post("modeType") == "getHtml") {
                $data["isPrivate"] = $this->input->post("private");
                $share_users = $this->document_managment_user->load_watchers_users($this->input->post("folder_id"));
                $data["sharedWithUsers"] = isset($share_users[0]) ? $share_users[0] : [];
                $data["sharedWithUsersStatus"] = isset($share_users[1]) ? $share_users[1] : [];
                $data["title"] = $this->lang->line("share_with");
                $response["html"] = $this->load->view("documents_management_system/shared_with_form", $data, true);
            } else {
                $response = $this->dmsnew->share_folder("contract", $this->input->post("folder_id"), $this->input->post("private"), $this->input->post("watchers_users"));
            }
        } else {
            $response["status"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function download_file($file_id, $newest_version = false)
    {
        $newest_version = $newest_version == "true" ? true : false;
        $response = $this->dmsnew->download_file("contract", $file_id, $newest_version);
        if (!$response["status"]) {
            $this->set_flashmessage("error", $response["message"]);
            redirect($this->agent->referrer());
        }
    }
    public function download_files()
    {
        $files = explode(",", $this->input->get("files"));
        $response = $this->dmsnew->download_files_as_zip("contract", $files, "files");
    }
    public function urls()
    {
        if ($this->input->post(NULL)) {
            $response = [];
            $mainClassificationId = $this->input->post("mainClassificationId");
            if ($mainClassificationId) {
                $this->load->model("contract_document_classification");
                $response["subList"] = $this->contract_document_classification->load_sub_classification_list($mainClassificationId);
            } else {
                $this->load->model("contract_url", "contract_urlfactory");
                $this->contract_url = $this->contract_urlfactory->get_instance();
                $filter = $this->input->post("filter");
                $sortable = $this->input->post("sort");
                if ($this->input->post("returnData")) {
                    $response = $this->contract_url->k_load_all_contract_url($filter, $sortable);
                }
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        }
    }
    public function add_url()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("contract_url", "contract_urlfactory");
        $this->contract_url = $this->contract_urlfactory->get_instance();
        if ($this->input->post(NULL)) {
            $this->contract_url->set_fields($this->input->post(NULL));
            $this->contract_url->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract_url->set_field("modifiedBy", $this->is_auth->get_user_id());
            $response["result"] = $this->contract_url->insert();
            if (!$response["result"]) {
                $response["validation_errors"] = $this->contract_url->get("validationErrors");
            }
        } else {
            $contract_id = $this->input->get("contract_id", true);
            $data["title"] = $this->lang->line("add");
            $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
            $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
            $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
            $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
            $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
            $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
            $data["path_types"] = array_combine($this->contract_url->get("path_typeValues"), ["-", $this->lang->line("network_drive"), $this->lang->line("web")]);
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $data["module_record_id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/url_form", $data, true);
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function edit_url()
    {
        $response = [];
        $response["result"] = false;
        if ($this->input->post(NULL)) {
            $this->load->model("contract_url", "contract_urlfactory");
            $this->contract_url = $this->contract_urlfactory->get_instance();
            $documentsData = json_decode($this->input->post("models"), true);
            foreach ($documentsData as $documentData) {
                $this->contract_url->fetch($documentData["id"]);
                if ($documentData["path_type"] == "web" && substr($documentData["path"], 0, 7) !== "http://" && substr($documentData["path"], 0, 8) !== "https://") {
                    $documentData["path"] = "http://" . $documentData["path"];
                }
                $this->contract_url->set_fields($documentData);
                $this->contract_url->set_field("document_type_id", $documentData["document_type_id"]);
                $this->contract_url->set_field("document_status_id", $documentData["document_status_id"]);
                $response["result"] = $this->contract_url->update();
            }
            if ($response["result"]) {
                $this->contract->set_field("id", $this->contract_url->get_field("contract_id"));
            }
        }
        $response["validation_errors"] = $this->contract_url->get("validationErrors");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_url()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $response["result"] = false;
        $doc_id = $this->input->post("docId");
        if (!empty($doc_id)) {
            $this->load->model("contract_url", "contract_urlfactory");
            $this->contract_url = $this->contract_urlfactory->get_instance();
            $result = $this->db->where("id", $doc_id)->delete($this->contract_url->get("_table"));
            if ($result) {
                $response["result"] = true;
            }
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function list_file_versions()
    {
        $list_file_verions_response = $this->dmsnew->list_file_versions("contract", $this->input->post("file_id"), true);
        if (!empty($list_file_verions_response["data"]["file_versions"])) {
            $response["html"] = $this->load->view("documents_management_system/file_document_versions", $list_file_verions_response["data"], true);
        }
        $response["status"] = $list_file_verions_response["status"];
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete_document()
    {
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->document_management_system->fetch($this->input->post("document_id"));
        $module_record_id = $this->document_management_system->get_field("module_record_id");
        $response = $this->dmsnew->delete_document("contract", $this->input->post("document_id"), $this->input->post("newest_version") == "true");
        $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($module_record_id);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function generate_document($id)
    {
        $this->load->model("doc_generator");
        $template_folder_path = $this->doc_generator->get_value_by_key("contract_template_folder_path");
        if (!$template_folder_path || !$id) {
            $error_msg = !$template_folder_path ? sprintf($this->lang->line("object_template_folder_path_is_not_specified"), $this->lang->line("contracts")) : $this->lang->line("invalid_record");
            if ($this->input->is_ajax_request()) {
                $response = ["result" => false, "error" => $error_msg];
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            } else {
                $this->set_flashmessage("warning", $error_msg);
                redirect("contracts/documents/" . $id);
            }
        } else {
            $template_record = $this->dmsnew->model->get_document_details(["id" => $template_folder_path]);
            $data["versioning"] = true;
            $data["type"] = "contract";
            if ($this->input->get("action", true) == "read") {
                $parties = $this->contract_party->fetch_contract_parties_data($id);
                if (!empty($parties)) {
                    $data["parties"] = "";
                    foreach ($parties as $key => $party) {
                        $party_number = $key + 1;
                        $data["parties"] .= (0 < $key ? ", " : "") . $party["party_name"] . (isset($party["party_category_name"]) ? " - (" . $party["party_category_name"] . ")" : "");
                        $data["party" . $party_number] = $parties[$key]["party_name"];
                        $data["party" . $party_number . "_category"] = $parties[$key]["party_category_name"];
                    }
                }
            }
            $response = $this->dmsnew->generate_contract_document($template_record, "contract", $id, "contract", "contract", $data);
            $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($id);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function check_folder_privacy()
    {
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $private_folders = $this->dmsnew->check_folder_privacy($this->input->post("id"), $this->input->post("lineage"));
        $response["result"] = empty($private_folders) ? false : true;
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function view_document($id = 0)
    {
        $response = [];
        if (0 < $id) {
            echo $this->dmsnew->get_document_content($id);
            exit;
        }
        $id = $this->input->post("id");
        if (!empty($id)) {
            $response["document"] = $this->dmsnew->get_document_details(["id" => $id]);
            $response["document"]["url"] = app_url("modules/contract/contracts/view_document/" . $id);
            if (!empty($response["document"]["extension"]) && in_array($response["document"]["extension"], $this->document_management_system->image_types)) {
                $response["iframe_content"] = $this->load->view("documents_management_system/view_image_document", ["url" => $response["document"]["url"]], true);
            }
        }
        $response["html"] = $this->load->view("documents_management_system/view_document", [], true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function list_contract_docs($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $data["title"] = $this->input->get("type") == "signature" ? $this->lang->line("contract_to_sign") : $this->lang->line("needs_approval");
        $data["docs"] = $this->contract->load_all_contract_docs($contract_id);
        if (empty($data["docs"])) {
            $response["result"] = false;
            $response["display_message"] = $this->lang->line("contract_unavailable");
        } else {
            $response["result"] = true;
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $response["html"] = $this->load->view("contracts/view/contract_list", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function awaiting_my_approvals()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("awaiting_my_approvals"));
        $this->authenticate_exempted_actions();
        $this->get_awaiting_approvals();
    }
    private function get_awaiting_approvals($all = false)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $category = "awaiting_approvals";
        $data["model"] = $category;
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($category, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($category, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
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
            $response = array_merge($response, $this->contract_approval_submission->load_awaiting_approvals($filter, $sortable));
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["text_empty"] = $this->get_filter_operators("text_empty");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $this->load->model("contract_status_language", "contract_status_languagefactory");
            $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
            $data["statuses"] = $this->contract_status_language->load_list_per_language();
            $data["categories"] = $this->party_category_language->load_list_per_language();
            $data["types"] = $this->contract_type_language->load_list_per_language();
            $data["archivedValues"] = array_combine($this->contract->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";
            unset($data["statuses"][""]);
            unset($data["categories"][""]);
            unset($data["types"][""]);
            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->contract->get("modelName"));
            $data["priorities"] = array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $this->load->model(["provider_group"]);
            $data["assigned_teams"] = $this->provider_group->load_list([]);
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["users_list"] = $this->user->load_all_list();
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $data["user_groups_list"] = $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]]);
            $data["users_list"][0] = "";
            if (!$all) {
                $data["approver_user"] = $this->session->userdata("AUTH_user_id");
                $data["approver_user_group"] = $this->session->userdata("AUTH_user_group_id");
                $data["my_approvals"] = true;
            } else {
                $data["approver_user"] = "";
                $data["approver_user_group"] = "";
                $data["all_approvals"] = true;
            }
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("contract/awaiting_approvals", "js");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("awaiting_approvals/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function awaiting_approvals()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("awaiting_approvals"));
        $this->get_awaiting_approvals(true);
    }
    public function add_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->post(NULL)) {
            $comment = $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($comment));
            $_POST["edited"] = 0;
            $response["result"] = false;
            $logged_user = $this->session->userdata("AUTH_user_id");
            $this->contract_comment->set_fields($this->input->post(NULL));
            $this->contract_comment->set_field("createdBy", $logged_user);
            $this->contract_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("createdOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("modifiedBy", $logged_user);
            $this->contract_comment->set_field("channel", $this->web_channel);
            $this->contract_comment->set_field("modifiedByChannel", $this->web_channel);
            $this->contract_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->contract_comment->insert()) {
                $contract_id = $this->input->post("contract_id");
                $this->contract->fetch($contract_id);
                $contributors = $this->contract_contributor->load_contributors($contract_id);
                $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                $notify["logged_in_user"] = $this->is_auth->get_fullname();
                if ($this->input->post("send_notifications_email")) {
                    $this->contract->send_notifications("add_contract_comment", $notify, ["id" => $contract_id, "comment" => $comment]);
                }
                $response["result"] = true;
                $response["data"]["contract_id"] = $this->contract_comment->get_field("contract_id");
                $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($contract_id);
                $contract_comment = $this->input->post("comment");
                $parent_folder_name = $this->contract_comment->get_field("createdOn");
                $this->move_contract_attachments_to_parent_folder($contract_comment, $parent_folder_name, $contract_id);
            } else {
                $response["validation_errors"] = $this->contract_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_comment()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("contract_comment", "contract_commentfactory");
        $this->contract_comment = $this->contract_commentfactory->get_instance();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        if ($this->input->get("id") && $this->input->get("contract_id")) {
            $data = [];
            if ($this->contract_comment->fetch(["id" => $this->input->get("id"), "contract_id" => $this->input->get("contract_id")])) {
                $data["comment"] = $this->contract_comment->get_fields();
                $creator_name = $this->user->get_name_by_id($data["comment"]["createdBy"]);
                $data["comment"]["created_by_name"] = $creator_name["name"];
                $data["title"] = $this->lang->line("edit_comment");
                $response["html"] = $this->load->view("contracts/view/comments/form", $data, true);
            }
        }
        if ($this->input->post(NULL)) {
            $_POST["comment"] = format_comment_patterns($this->regenerate_note($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>")), true);
            $this->contract_comment->fetch(["id" => $this->input->post("id"), "contract_id" => $this->input->post("contract_id")]);
            if ($this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>") != $this->contract_comment->get_field("comment")) {
                $_POST["edited"] = 1;
            }
            $createdOn = date("Y-m-d H:i:s");
            $this->contract_comment->set_field("createdOn", date("Y-m-d H:i:s"));
            $newComment = $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
            $this->move_contract_attachments_to_parent_folder($this->contract_comment->get_field("comment"), $createdOn, $this->input->post("contract_id"), $newComment);
            $logged_user = $this->session->userdata("AUTH_user_id");
            $this->contract_comment->set_fields($this->input->post(NULL));
            $this->contract_comment->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->contract_comment->set_field("modifiedBy", $logged_user);
            $this->contract_comment->set_field("modifiedByChannel", $this->web_channel);
            $this->contract_comment->set_field("comment", $this->input->post("comment", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
            if ($this->contract_comment->update()) {
                $response["result"] = true;
                $response["data"] = ["contract_id" => $this->input->post("contract_id")];
            } else {
                $response["validation_errors"] = $this->contract_comment->get("validationErrors");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_comment($id)
    {
        if (0 < $id && $this->input->is_ajax_request()) {
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            $this->load->model("contract_comment_email", "contract_comment_emailfactory");
            $this->contract_comment_email = $this->contract_comment_emailfactory->get_instance();
            $this->contract_comment_email->delete(["where" => ["contract_comment", $id]]);
            $response["status"] = false;
            if ($this->contract_comment->fetch($id) && $this->contract_comment->delete($id)) {
                $response["status"] = true;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function delete_email_comment($id)
    {
        $response = [];
        $this->load->model("contract_comment_email", "contract_comment_emailfactory");
        $this->contract_comment_email = $this->contract_comment_emailfactory->get_instance();
        if ($this->contract_comment_email->fetch($id)) {
            $this->load->model("contract_comment", "contract_commentfactory");
            $this->contract_comment = $this->contract_commentfactory->get_instance();
            $contract_comment_id = $this->contract_comment_email->get_field("contract_comment");
            $contract_comment_email_id = $this->contract_comment_email->get_field("id");
            if ($this->contract_comment_email->delete(["where" => ["id", $contract_comment_email_id]]) && $this->contract_comment->delete(["where" => ["id", $contract_comment_id]])) {
                $response["status"] = true;
            } else {
                $response["status"] = false;
            }
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function return_doc_thumbnail($id = 0, $name = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id) {
            $this->load->library("dmsnew");
            $response = $this->dmsnew->get_file_download_data("contract", $id);
            $content = $response["data"]["file_content"];
            if ($content) {
                $this->load->helper("download");
                force_download($name ? $name : $id, $content);
            }
        }
    }
    public function delete()
    {
        $contract_id = $this->input->post("id");
        $response["result"] = false;
        $response = [];
        $count = 0;
        $contracts_not_deleted = [];
        $contracts_deleted = [];
        if (is_array($contract_id)) {
            foreach ($contract_id[0] as $id) {
                $result = $this->delete_contract($id);
                if ($result["result"]) {
                    $contracts_deleted[] = $result["contract_id"];
                } else {
                    $contracts_not_deleted[] = $result["contract_id"];
                }
            }
            if (empty($contracts_not_deleted)) {
                $response["result"] = true;
                $response["display_message"] = $this->lang->line("delete_contracts_successfully");
            } else {
                $response["display_message"] = sprintf($this->lang->line("delete_record_failed"), implode(", ", $contracts_not_deleted));
            }
        } else {
            $response = $this->delete_contract($contract_id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function delete_contract($contract_id = 0)
    {
        if ($contract_id && $this->contract->fetch($contract_id)) {
            if ($this->contract->fetch(["amendment_of" => $contract_id])) {
                $response["display_message"] = $this->lang->line("delete_contract_rejected");
            } else {
                $this->load->model("contract_approval_status", "contract_approval_statusfactory");
                $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
                $this->load->model("contract_approval_history", "contract_approval_historyfactory");
                $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
                $this->load->model("contract_signature_status", "contract_signature_statusfactory");
                $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
                $this->load->model("contract_signature_history", "signature_historyfactory");
                $this->contract_signature_history = $this->signature_historyfactory->get_instance();
                $this->load->model("contract_comment", "contract_commentfactory");
                $this->contract_comment = $this->contract_commentfactory->get_instance();
                $this->load->model("contract_renewal_history", "contract_renewal_historyfactory");
                $this->contract_renewal_history = $this->contract_renewal_historyfactory->get_instance();
                $this->load->model("milestone", "milestonefactory");
                $this->milestone = $this->milestonefactory->get_instance();
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $this->load->model("contract_sla", "contract_slafactory");
                $this->contract_sla = $this->contract_slafactory->get_instance();
                $this->contract_sla->delete(["where" => ["contract_id", $contract_id]]);
                $this->load->model("contract_comment_email", "contract_comment_emailfactory");
                $this->contract_comment_email = $this->contract_comment_emailfactory->get_instance();
                $this->contract_comment_email->delete_contract_comment_email($contract_id);
                $this->load->model("related_contract", "related_contractfactory");
                $this->related_contract = $this->related_contractfactory->get_instance();
                $this->contract_party->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_renewal_history->delete(["where" => ["contract_id", $contract_id]]);
                $this->load->model("contract_amendment_history", "contract_amendment_historyfactory");
                $this->contract_amendment_history = $this->contract_amendment_historyfactory->get_instance();
                $this->contract_amendment_history->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_amendment_history->delete(["where" => ["amended_id", $contract_id]]);
                $this->contract_user->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_approval_submission->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_approval_status->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_approval_history->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_signature_submission->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_signature_status->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_signature_history->delete(["where" => ["contract_id", $contract_id]]);
                $this->contract_comment->delete(["where" => ["contract_id", $contract_id]]);
                $this->related_contract->delete(["where" => ["contract_a_id", $contract_id]]);
                $this->related_contract->delete(["where" => ["contract_b_id", $contract_id]]);
                $custom_fields = $this->custom_field->load_custom_fields($contract_id, "contract");
                $this->milestone->delete_contract_milestones($contract_id);
                $this->reminder->dismiss_related_reminders_by_related_object_ids($contract_id, "contract_id");
                $custom_fields_id = array_column($custom_fields, "id");
                if (isset($custom_fields_id) && !empty($custom_fields_id)) {
                    $this->db->where("recordId", $contract_id)->where_in("custom_field_id", $custom_fields_id)->delete("custom_field_values");
                }
                if ($this->contract->delete($contract_id)) {
                    $response["result"] = true;
                    $response["display_message"] = sprintf($this->lang->line("delete_record_successfull"), $this->contract->get("modelCode") . $contract_id);
                    $response["contract_id"] = $this->contract->get("modelCode") . $contract_id;
                } else {
                    $response["display_message"] = sprintf($this->lang->line("delete_record_failed"), $this->contract->get("modelCode") . $contract_id);
                    $response["contract_id"] = $this->contract->get("modelCode") . $contract_id;
                }
            }
        } else {
            $response["display_message"] = sprintf($this->lang->line("delete_record_failed"), $this->contract->get("modelCode") . $contract_id);
        }
        return $response;
    }
    public function add_approver_per_contract()
    {
        $response = $this->approver_per_contract();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function approver_per_contract($action = "add")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        $this->load->model("approval", "approvalfactory");
        $this->approval = $this->approvalfactory->get_instance();
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $this->load->model("contract_approval_user", "contract_approval_userfactory");
        $this->contract_approval_user = $this->contract_approval_userfactory->get_instance();
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $this->load->model("contract_approval_collaborator", "contract_approval_collaboratorfactory");
        $this->contract_approval_collaborator = $this->contract_approval_collaboratorfactory->get_instance();
        $this->load->model("contract_approval_user_group", "contract_approval_user_groupfactory");
        $this->contract_approval_user_group = $this->contract_approval_user_groupfactory->get_instance();
        $this->load->model("contract_approval_contact", "contract_approval_contactfactory");
        $this->contract_approval_contact = $this->contract_approval_contactfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id");
            if ($action === "edit") {
                $details = $this->contract_approval_status->load_approval_data($this->input->get("id"));
                $data["bm_collaborators_list"] = $this->contract_party->fetch_bm_contacts_per_company($details["party_id"], $details["role_id"]);
                $data["sh_collaborators_list"] = $this->contract_party->fetch_sh_contacts_per_company($details["party_id"]);
            } else {
                $this->contract->fetch($contract_id);
                $details = $this->contract_approval_status->get_fields();
                $details["rank"] = $this->contract_approval_status->load_last_approval_rank($contract_id);
            }
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $data["user_groups_list"] = $this->user_group->load_available_list();
            $data = array_merge($data, $details);
            $data["approvers_signees"] = [];
            if (!empty($data["users"])) {
                $users = [];
                foreach (explode(",", $data["users"]) as $key => $id) {
                    $users[$key] = $this->user->get_name_by_id($id);
                    $users[$key]["type"] = "User";
                    $users[$key]["type_language"] = $this->lang->line("user");
                    array_push($data["approvers_signees"], $users[$key]);
                }
            }
            if (!empty($data["collaborators"])) {
                $collaborators = [];
                foreach (explode(",", $data["collaborators"]) as $key => $id) {
                    $collaborators[$key] = $this->customer_portal_users->get_name_by_id($id);
                    $collaborators[$key]["type"] = "Collaborator";
                    $collaborators[$key]["type_language"] = $this->lang->line("collaborator");
                    array_push($data["approvers_signees"], $collaborators[$key]);
                }
            }
            if (!empty($data["user_groups"])) {
                $user_groups = [];
                foreach (explode(",", $data["user_groups"]) as $key => $id) {
                    $user_groups[] = $this->user_group->get_name_by_id($id);
                }
                $data["user_groups"] = $user_groups;
            }
            if (!empty($data["contacts"])) {
                $contacts = [];
                foreach (explode(",", $data["contacts"]) as $key => $id) {
                    $contacts[$key] = $this->contact->get_name_by_id($id);
                    $contacts[$key]["type"] = "Contact";
                    $contacts[$key]["type_language"] = $this->lang->line("contact");
                    array_push($data["approvers_signees"], $contacts[$key]);
                }
            }
            $data["contract_company_parties"] = $this->contract_party->list_contract_company_parties($contract_id);
            $data["title"] = $action === "edit" ? $this->lang->line("edit_approvers") : $this->lang->line("add_approvers");
            $response["type"] = "approval";
            $data["type"] = $response["type"];
            $data["contract"]["id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_signature_row_form", $data, true);
        }
        if ($this->input->post(NULL, true) && $this->contract->fetch($this->input->post("contract_id", true))) {
            $post_data = $this->input->post(NULL, true);
            if (!$this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                $this->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
                $this->contract_workflow_status_relation = $this->contract_workflow_status_relationfactory->get_instance();
                $this->contract_workflow_status_relation->fetch(["workflow_id" => $this->contract->get_field("workflow_id"), "status_id" => $this->contract->get_field("status_id")]);
                $status_is_approval_start_point = $this->contract_workflow_status_relation->get_field("approval_start_point");
                $this->contract_approval_submission->set_field("contract_id", $post_data["contract_id"]);
                $this->contract_approval_submission->set_field("status", $status_is_approval_start_point ? "awaiting_approval" : "drafting");
                $this->contract_approval_submission->insert();
            }
            $data["overall_status"] = $this->contract_approval_submission->get_field("status");
            $this->contract_approval_submission->reset_fields();
            $old_data = [];
            if ($action === "edit") {
                $this->contract_approval_status->fetch($post_data["id"]);
                $old_data = $this->contract_approval_status->get_fields();
            }
            $this->contract_approval_status->set_fields($post_data);
            $this->contract_approval_status->set_field("party_id", $post_data["party_id"] ?: NULL);
            $awaiting_approval_order = $this->contract_approval_status->load_awaiting_approval_order($post_data["contract_id"], $post_data["id"]);
            if ($awaiting_approval_order) {
                if ($awaiting_approval_order["rank"] === $post_data["rank"]) {
                    $status = "awaiting_approval";
                } else {
                    if ($post_data["rank"] < $awaiting_approval_order["rank"]) {
                        $status = "awaiting_approval";
                        $this->contract_approval_status->update_status_per_order($awaiting_approval_order["rank"], $post_data["contract_id"]);
                    } else {
                        $status = "pending";
                    }
                }
            } else {
                if (!$awaiting_approval_order) {
                    $status = "awaiting_approval";
                } else {
                    $status = "pending";
                }
            }
            $this->contract_approval_status->set_field("status", $status);
            if (!$this->contract_approval_status->validate()) {
                $response["validation_errors"] = $this->contract_approval_status->get("validationErrors");
            }
            $post_data["Approvers_Signees_types"] = isset($post_data["Approvers_Signees_types"]) ? $post_data["Approvers_Signees_types"] : [];
            if (!in_array("User", $post_data["Approvers_Signees_types"]) && !in_array("Collaborator", $post_data["Approvers_Signees_types"]) && !isset($post_data["user_groups"]) && !$post_data["is_requester_manager"] && !$post_data["is_board_member"] && !$post_data["is_shareholder"]) {
                $response["validation_errors"]["required_users"] = $this->lang->line("approvers_error");
            }
            $approved_order = $this->contract_approval_status->load_latest_orders_approved($post_data["contract_id"]);
            if ($approved_order && in_array($post_data["rank"], $approved_order)) {
                $response["validation_errors"]["rank"] = sprintf($this->lang->line("validation_max_order"), $post_data["rank"]);
            }
            if ($post_data["is_board_member"] && (!isset($post_data["bm_collaborator"]) || !$post_data["party_id"])) {
                $response["validation_errors"]["board_member"] = $this->lang->line("bm_feature_required");
            }
            if ($post_data["is_shareholder"] && (!isset($post_data["sh_collaborator"]) || !$post_data["party_id"])) {
                $response["validation_errors"]["shareholder"] = $this->lang->line("sh_feature_required");
            }
            if (!isset($response["validation_errors"])) {
                $result = $action === "edit" ? $this->contract_approval_status->update() : $this->contract_approval_status->insert();
                if ($result) {
                    $contract_approval_status_id = $this->contract_approval_status->get_field("id");
                    $this->contract_approval_user->delete(["where" => ["contract_approval_status_id", $contract_approval_status_id]]);
                    $this->contract_approval_collaborator->delete(["where" => ["contract_approval_status_id", $contract_approval_status_id]]);
                    $this->contract_approval_contact->delete(["where" => ["contract_approval_status_id", $contract_approval_status_id]]);
                    if (isset($post_data["bm_collaborator"]) && !empty($post_data["bm_collaborator"])) {
                        foreach ($post_data["bm_collaborator"] as $approver_id) {
                            $this->contract_approval_collaborator->reset_fields();
                            $this->contract_approval_collaborator->set_field("contract_approval_status_id", $contract_approval_status_id);
                            $this->contract_approval_collaborator->set_field("user_id", $approver_id);
                            $this->contract_approval_collaborator->set_field("type", "board_member");
                            if ($this->contract_approval_collaborator->insert()) {
                                $this->contract_collaborator->reset_fields();
                                $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                $this->contract_collaborator->set_field("user_id", $approver_id);
                                $this->contract_collaborator->insert();
                                $this->contract->set_field("visible_to_cp", 1);
                                $this->contract->update();
                            }
                        }
                    }
                    if (isset($post_data["sh_collaborator"]) && !empty($post_data["sh_collaborator"])) {
                        foreach ($post_data["sh_collaborator"] as $approver_id) {
                            $this->contract_approval_collaborator->reset_fields();
                            $this->contract_approval_collaborator->set_field("contract_approval_status_id", $contract_approval_status_id);
                            $this->contract_approval_collaborator->set_field("user_id", $approver_id);
                            $this->contract_approval_collaborator->set_field("type", "shareholder");
                            if ($this->contract_approval_collaborator->insert()) {
                                $this->contract_collaborator->reset_fields();
                                $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                $this->contract_collaborator->set_field("user_id", $approver_id);
                                $this->contract_collaborator->insert();
                                $this->contract->set_field("visible_to_cp", 1);
                                $this->contract->update();
                            }
                        }
                    }
                    if (isset($post_data["Approvers_Signees"]) && !empty($post_data["Approvers_Signees"])) {
                        foreach ($post_data["Approvers_Signees"] as $key => $approver_id) {
                            $type = $post_data["Approvers_Signees_types"][$key];
                            $model = "contract_approval_" . strtolower($type);
                            if (!$this->{$model}->fetch(["contract_approval_status_id" => $contract_approval_status_id, $type == "Contact" ? "contact_id" : "user_id" => $approver_id])) {
                                $this->{$model}->reset_fields();
                                $this->{$model}->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->{$model}->set_field($type == "Contact" ? "contact_id" : "user_id", $approver_id);
                                if ($this->{$model}->insert() && $type == "Collaborator") {
                                    $this->contract_collaborator->reset_fields();
                                    $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                    $this->contract_collaborator->set_field("user_id", $approver_id);
                                    $this->contract_collaborator->insert();
                                    $this->contract->set_field("visible_to_cp", 1);
                                    $this->contract->update();
                                }
                            }
                        }
                    }
                    $this->contract_approval_user_group->delete(["where" => ["contract_approval_status_id", $contract_approval_status_id]]);
                    if (isset($post_data["user_groups"]) && !empty($post_data["user_groups"])) {
                        foreach ($post_data["user_groups"] as $user_group_id) {
                            $this->contract_approval_user_group->reset_fields();
                            $this->contract_approval_user_group->set_field("contract_approval_status_id", $contract_approval_status_id);
                            $this->contract_approval_user_group->set_field("user_group_id", $user_group_id);
                            $this->contract_approval_user_group->insert();
                        }
                    }
                    $response["history_result"] = $this->log_approval_history($this->contract_approval_status->get_fields(), $action, $old_data);
                    $this->contract_approval_status->reset_fields();
                    $response["overall_status"] = $data["overall_status"];
                    $response["result"] = true;
                }
            }
        }
        return $response;
    }
    private function log_approval_history($data, $action, $old_data = [])
    {
        $this->load->model("contract_approval_history", "contract_approval_historyfactory");
        $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
        $this->contract_approval_history->set_field("done_on", date("Y-m-d H:i:s"));
        $this->contract_approval_history->set_field("done_by", $this->is_auth->get_user_id());
        $this->contract_approval_history->set_field("done_by_type", "user");
        $this->contract_approval_history->set_field("from_action", $action === "edit" ? $old_data["status"] : "-");
        $this->contract_approval_history->set_field("to_action", $data["status"]);
        $this->contract_approval_history->set_field("contract_id", $data["contract_id"]);
        $this->contract_approval_history->set_field("label", $action === "edit" ? $old_data["label"] : $data["label"]);
        $this->contract_approval_history->set_field("action", $action);
        $this->contract_approval_history->set_field("enforce_previous_approvals", "0");
        $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
        $this->contract_approval_history->set_field("approval_channel", "A4L");
        $key = $action === "edit" ? "edit_approver_comment" : "add_approver_comment";
        if ($action === "edit") {
            unset($old_data["status"]);
            unset($data["status"]);
            $diff = array_diff($old_data, $data);
            if (!empty($diff)) {
                $comment = $this->lang->line($key) . "</br>";
                foreach ($diff as $k => $old_val) {
                    $comment .= $this->lang->line($k) . ": " . $this->lang->line("from") . " " . $old_val . " " . $this->lang->line("to") . " " . $data[$k] . "</br>";
                }
            } else {
                $comment = $this->lang->line($key);
            }
        } else {
            $comment = $this->lang->line($key);
        }
        $this->contract_approval_history->set_field("comment", $comment);
        return $this->contract_approval_history->insert();
    }
    public function edit_approver_per_contract()
    {
        $response = $this->approver_per_contract("edit");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_approver_per_contract()
    {
        $id = $this->input->post("id");
        $response = [];
        $response["result"] = false;
        $this->load->model("contract_approval_user", "contract_approval_userfactory");
        $this->contract_approval_user = $this->contract_approval_userfactory->get_instance();
        $this->load->model("contract_approval_collaborator", "contract_approval_collaboratorfactory");
        $this->contract_approval_collaborator = $this->contract_approval_collaboratorfactory->get_instance();
        $this->load->model("contract_approval_user_group", "contract_approval_user_groupfactory");
        $this->contract_approval_user_group = $this->contract_approval_user_groupfactory->get_instance();
        $this->load->model("contract_approval_contact", "contract_approval_contactfactory");
        $this->contract_approval_contact = $this->contract_approval_contactfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $old_data = $this->contract_approval_status->load_approval_details($id);
        $this->contract_approval_user->delete(["where" => ["contract_approval_status_id", $id]]);
        $this->contract_approval_collaborator->delete(["where" => ["contract_approval_status_id", $id]]);
        $this->contract_approval_user_group->delete(["where" => ["contract_approval_status_id", $id]]);
        $this->contract_approval_contact->delete(["where" => ["contract_approval_status_id", $id]]);
        if ($this->contract_approval_status->delete($id)) {
            $this->load->model("contract_approval_history", "contract_approval_historyfactory");
            $this->contract_approval_history = $this->contract_approval_historyfactory->get_instance();
            $this->contract_approval_history->set_field("done_on", date("Y-m-d H:i:s"));
            $this->contract_approval_history->set_field("done_by", $this->is_auth->get_user_id());
            $this->contract_approval_history->set_field("done_by_type", "user");
            $this->contract_approval_history->set_field("from_action", $old_data["status"]);
            $this->contract_approval_history->set_field("to_action", "-");
            $this->contract_approval_history->set_field("contract_id", $old_data["contract_id"]);
            $this->contract_approval_history->set_field("label", $old_data["label"]);
            $this->contract_approval_history->set_field("action", "delete");
            $this->contract_approval_history->set_field("enforce_previous_approvals", "0");
            $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
            $this->contract_approval_history->set_field("approval_channel", "A4L");
            unset($old_data["id"]);
            unset($old_data["contract_id"]);
            unset($old_data["status"]);
            $comment = $this->lang->line("delete_approver_comment") . "</br>";
            foreach ($old_data as $k => $old_val) {
                $comment .= $this->lang->line($k) . ": " . ($old_val ?: "-") . "</br>";
            }
            $this->contract_approval_history->set_field("comment", $comment);
            $this->contract_approval_history->insert();
            $response["result"] = true;
            $response["display_message"] = $this->lang->line("record_deleted");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function send_email_to_contact_approver()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $this->load->model("contact", "contactfactory");
        $this->contact = $this->contactfactory->get_instance();
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->model("contact_emails");
        $response["result"] = false;
        if ($post_data = $this->input->post(NULL)) {
            if (!$this->input->post("email") || !$this->input->post("email")["subject"]) {
                $response["validation_errors"]["email_subject"] = $this->lang->line("cannot_be_blank_rule");
            }
            if (!$this->input->post("email") || !$this->input->post("email")["message"]) {
                $response["validation_errors"]["email_message"] = $this->lang->line("cannot_be_blank_rule");
            }
            if (empty($post_data["approverContacts"])) {
                $response["validation_errors"]["approverContacts"] = $this->lang->line("cannot_be_blank_rule");
            }
            if (!isset($response["validation_errors"])) {
                $this->load->library("email_notifications");
                $contacts_emails = [];
                $contacts_names = [];
                $contacts_no_emails = [];
                foreach ($post_data["approverContacts"] as $contact_id) {
                    $emails = $this->contact_emails->load_contact_emails($contact_id);
                    if (!empty($emails)) {
                        list($contacts_emails[]) = array_values($emails);
                        $contacts_names[] = $this->contact->get_name_by_id($contact_id);
                    } else {
                        $contacts_no_emails[] = $this->contact->get_name_by_id($contact_id);
                    }
                }
                if (empty($contacts_no_emails)) {
                    if (isset($post_data["documents"])) {
                        $this->load->model("document_management_system", "document_management_systemfactory");
                        $this->document_management_system = $this->document_management_systemfactory->get_instance();
                        $attachments = [];
                        foreach ($post_data["documents"] as $key => $document) {
                            $this->document_management_system->fetch($document);
                            $attachments[$key]["path"] = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "contracts" . $this->document_management_system->get_field("lineage");
                            $attachments[$key]["name"] = $this->document_management_system->get_field("name") . "." . $this->document_management_system->get_field("extension");
                        }
                        $logged_in_user[] = ["email" => $this->session->userdata("AUTH_email_address"), "name" => $this->session->userdata("AUTH_userProfileName")];
                        $this->load->model("system_preference");
                        $this->system_preferences = $this->system_preference->get_key_groups();
                        $mail_subject_prefix = $this->system_preferences["OutgoingMail"]["outgoingMailSubjectPrefix"];
                        $mail_subject_prefix = !empty($mail_subject_prefix) ? $mail_subject_prefix . " - " : "";
                        $this->contract->fetch($post_data["contract_id"]);
                        $subject = $mail_subject_prefix . $this->contract->get("modelCode") . $post_data["contract_id"] . ": " . $this->contract->get_field("name") . " - " . $post_data["email"]["subject"];
                        $cc_users = [];
                        if (isset($post_data["cc_users"]) && !empty($post_data["cc_users"])) {
                            foreach ($this->input->post("cc_users") as $key => $user_id) {
                                $this->user->fetch($user_id);
                                $cc_users[] = $this->user->get_field("email");
                            }
                        }
                        $email["content"] = $post_data["email"]["message"];
                        $email_content = $this->load->view("templates/email", $email, true);
                        $response["result"] = $this->email_notifications->send_email($contacts_emails, $subject, $email_content, $cc_users, $attachments, $logged_in_user);
                        if (!$response["result"]) {
                            $response["display_message"] = $this->lang->line("email_could_not_be_sent");
                        } else {
                            $this->load->model("contract_approval_status", "contract_approval_statusfactory");
                            $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
                            $this->load->model("contract_approval_history", "approval_historyfactory");
                            $this->contract_approval_history = $this->approval_historyfactory->get_instance();
                            $this->contract_approval_status->fetch($post_data["approval_status_id"]);
                            $this->contract_approval_history->set_field("done_on", date("Y-m-d H:i:s"));
                            $this->contract_approval_history->set_field("done_by", $this->is_auth->get_user_id());
                            $this->contract_approval_history->set_field("done_by_type", "user");
                            $this->contract_approval_history->set_field("from_action", $this->contract_approval_status->get_field("status"));
                            $this->contract_approval_history->set_field("to_action", $this->contract_approval_status->get_field("status"));
                            $this->contract_approval_history->set_field("contract_id", $post_data["contract_id"]);
                            $this->contract_approval_history->set_field("label", $this->contract_approval_status->get_field("label"));
                            $this->contract_approval_history->set_field("action", "documents_sent");
                            $this->contract_approval_history->set_field("enforce_previous_approvals", "0");
                            $this->contract_approval_history->set_field("comment", "Documents sent to " . implode(", ", array_column($contacts_names, "name")));
                            $this->contract_approval_history->set_field("done_by_ip", $this->input->ip_address());
                            $this->contract_approval_history->set_field("approval_channel", "A4L");
                            $this->contract_approval_history->insert();
                        }
                    } else {
                        $response["display_message"] = $this->lang->line("you_should_select_document_to_be_sent");
                    }
                } else {
                    $response["display_message"] = sprintf($this->lang->line(1 < count($contacts_no_emails) ? "selected_contacts_dont_not_have_emails" : "contact_does_not_have_emails"), implode(", ", array_column($contacts_no_emails, "name")));
                }
            }
        } else {
            if ($this->input->get("contract_id") && $this->contract->fetch($this->input->get("contract_id"))) {
                $contract_id = $this->input->get("contract_id", true);
                $this->load->model("contract", "contractfactory");
                $this->contract = $this->contractfactory->get_instance();
                $data = $this->contract->load_contract_docs_to_approve($contract_id);
                $this->load->model("contract_approval_status", "contract_approval_statusfactory");
                $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
                $data["title"] = $this->lang->line("send_email");
                $data["contract_id"] = $contract_id;
                $approval_data = $this->contract_approval_status->load_approval_data($this->input->get("approval_status_id"));
                if (!empty($approval_data["contacts"])) {
                    $contacts = [];
                    $count = 0;
                    foreach (explode(",", $approval_data["contacts"]) as $id) {
                        $contacts[$count] = $this->contact->get_name_by_id($id);
                        $contact_emails = $this->contact_emails->load_contact_emails($id);
                        $contacts[$count]["email"] = !empty($contact_emails) ? array_values($contact_emails)[0] : false;
                        $count++;
                    }
                    $data["contacts"] = $contacts;
                }
                $response["result"] = true;
                $response["html"] = $this->load->view("contracts/view/approval_center/request_contact_approval", $data, true);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function upload_approval_document()
    {
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id", true);
            $data["title"] = $this->lang->line("upload_file");
            $this->load->model("contract_document_status_language", "contract_document_status_languagefactory");
            $this->contract_document_status_language = $this->contract_document_status_languagefactory->get_instance();
            $this->load->model("contract_document_type_language", "contract_document_type_languagefactory");
            $this->contract_document_type_language = $this->contract_document_type_languagefactory->get_instance();
            $data["document_statuses"] = $this->contract_document_status_language->load_list_per_language();
            $data["document_types"] = $this->contract_document_type_language->load_list_per_language();
            $data["module"] = "contract";
            $data["module_record"] = "contract";
            $data["module_record_id"] = $contract_id;
            $data["contract_approval_status_id"] = $this->input->get("contract_approval_status_id");
            $response["result"] = true;
            $response["html"] = $this->load->view("documents_management_system/upload_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                if ($response["status"]) {
                    $this->load->model("contract_approval_documents", "contract_approval_documentsfactory");
                    $this->contract_approval_documents = $this->contract_approval_documentsfactory->get_instance();
                    $this->contract_approval_documents->set_field("document_id", $response["file"]["id"]);
                    $this->contract_approval_documents->set_field("contract_approval_status_id", $this->input->post("contract_approval_status_id"));
                    $this->contract_approval_documents->insert();
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function signature_center($contract_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        if ($this->contract->fetch($contract_id)) {
            $this->load->model("contract_signature_status", "contract_signature_statusfactory");
            $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
            $this->load->model("contract_signature_history", "signature_historyfactory");
            $this->contract_signature_history = $this->signature_historyfactory->get_instance();
            $data = false;
            if ($this->contract_signature_submission->fetch(["contract_id" => $contract_id])) {
                $data["overall_status"] = $this->contract_signature_submission->get_field("status");
                $data["signature_history"] = $this->contract_signature_history->load_history($contract_id);
                $data["signature_center"] = $this->contract_signature_status->load_signature_center_for_contract($contract_id);
                $data["manager"] = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
            }
            $data["contract"]["id"] = $contract_id;
            $data["contract"]["name"] = $this->contract->get_field("name");
            $data["model_code"] = $this->contract->get("modelCode");
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/signature_center/index", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_signature_signee_per_contract()
    {
        $response = $this->signature_signee_per_contract();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function signature_signee_per_contract($action = "add")
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = ["result" => false];
        $this->load->model("signature", "signaturefactory");
        $this->signature = $this->signaturefactory->get_instance();
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        $this->load->model("contract_signature_user", "contract_signature_userfactory");
        $this->contract_signature_user = $this->contract_signature_userfactory->get_instance();
        $this->load->model("customer_portal_users", "customer_portal_usersfactory");
        $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
        $this->load->model("contract_signature_collaborator", "contract_signature_collaboratorfactory");
        $this->contract_signature_collaborator = $this->contract_signature_collaboratorfactory->get_instance();
        $this->load->model("contract_signature_user_group", "contract_signature_user_groupfactory");
        $this->contract_signature_user_group = $this->contract_signature_user_groupfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $contract_id = $this->input->get("contract_id");
            if ($action === "edit") {
                $details = $this->contract_signature_status->load_signature_data($this->input->get("id"));
                $data["bm_collaborators_list"] = $this->contract_party->fetch_bm_contacts_per_company($details["party_id"], $details["role_id"]);
                $data["sh_collaborators_list"] = $this->contract_party->fetch_sh_contacts_per_company($details["party_id"]);
            } else {
                $this->contract->fetch($contract_id);
                $details = $this->contract_signature_status->get_fields();
                $details["rank"] = $this->contract_signature_status->load_last_signature_rank($contract_id);
            }
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $data["user_groups_list"] = $this->user_group->load_available_list();
            $data = array_merge($data, $details);
            $data["approvers_signees"] = [];
            if (!empty($data["users"])) {
                $users = [];
                foreach (explode(",", $data["users"]) as $key => $id) {
                    $users[$key] = $this->user->get_name_by_id($id);
                    $users[$key]["type"] = "User";
                    $users[$key]["type_language"] = $this->lang->line("user");
                    array_push($data["approvers_signees"], $users[$key]);
                }
            }
            if (!empty($data["collaborators"])) {
                $collaborators = [];
                foreach (explode(",", $data["collaborators"]) as $key => $id) {
                    $collaborators[$key] = $this->customer_portal_users->get_name_by_id($id);
                    $collaborators[$key]["type"] = "Collaborator";
                    $collaborators[$key]["type_language"] = $this->lang->line("collaborator");
                    array_push($data["approvers_signees"], $collaborators[$key]);
                }
            }
            if (!empty($data["user_groups"])) {
                $user_groups = [];
                foreach (explode(",", $data["user_groups"]) as $key => $id) {
                    $user_groups[] = $this->user_group->get_name_by_id($id);
                }
                $data["user_groups"] = $user_groups;
            }
            $data["contract_company_parties"] = $this->contract_party->list_contract_company_parties($contract_id);
            $data["title"] = $action === "edit" ? $this->lang->line("edit_signee") : $this->lang->line("add_signee");
            $response["type"] = "signature";
            $data["type"] = $response["type"];
            $data["contract"]["id"] = $contract_id;
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_signature_row_form", $data, true);
        }
        if ($this->input->post(NULL, true) && $this->contract->fetch($this->input->post("contract_id", true))) {
            $post_data = $this->input->post(NULL, true);
            if (!$this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]])) {
                $this->contract_signature_submission->set_field("contract_id", $post_data["contract_id"]);
                $over_all_status = "awaiting_approval";
                if ($this->contract_approval_submission->fetch(["contract_id" => $post_data["contract_id"]]) && $this->contract_approval_submission->get_field("status") == "approved") {
                    $over_all_status = "awaiting_signature";
                }
                $this->contract_signature_submission->set_field("status", $over_all_status);
                $this->contract_signature_submission->insert();
            }
            $data["overall_status"] = $this->contract_signature_submission->get_field("status");
            $this->contract_signature_submission->reset_fields();
            $old_data = [];
            if ($action === "edit") {
                $this->contract_signature_status->fetch($post_data["id"]);
                $old_data = $this->contract_signature_status->get_fields();
            }
            $this->contract_signature_status->set_fields($post_data);
            $this->contract_signature_status->set_field("party_id", $post_data["party_id"] ?: NULL);
            $awaiting_signature_order = $this->contract_signature_status->load_awaiting_signature_order($post_data["contract_id"], $post_data["id"]);
            if ($awaiting_signature_order) {
                if ($awaiting_signature_order["rank"] === $post_data["rank"]) {
                    $status = "awaiting_signature";
                } else {
                    if ($post_data["rank"] < $awaiting_signature_order["rank"]) {
                        $status = "awaiting_signature";
                        $this->contract_signature_status->update_status_per_order($awaiting_signature_order["rank"], $post_data["contract_id"]);
                    } else {
                        $status = "pending";
                    }
                }
            } else {
                if (!$awaiting_signature_order) {
                    $status = "awaiting_signature";
                } else {
                    $status = "pending";
                }
            }
            $this->contract_signature_status->set_field("status", $status);
            if (!$this->contract_signature_status->validate()) {
                $response["validation_errors"] = $this->contract_signature_status->get("validationErrors");
            }
            $post_data["Approvers_Signees_types"] = isset($post_data["Approvers_Signees_types"]) ? $post_data["Approvers_Signees_types"] : [];
            if (!in_array("User", $post_data["Approvers_Signees_types"]) && !in_array("Collaborator", $post_data["Approvers_Signees_types"]) && !isset($post_data["user_groups"]) && !$post_data["is_requester_manager"] && !$post_data["is_board_member"] && !$post_data["is_shareholder"]) {
                $response["validation_errors"]["required_users"] = $this->lang->line("signees_error");
            }
            $signed_order = $this->contract_signature_status->load_latest_orders_signed($post_data["contract_id"]);
            if ($signed_order && in_array($post_data["rank"], $signed_order)) {
                $response["validation_errors"]["rank"] = sprintf($this->lang->line("validation_max_order"), $post_data["rank"]);
            }
            if ($post_data["is_board_member"] && (!isset($post_data["bm_collaborator"]) || !$post_data["party_id"])) {
                $response["validation_errors"]["board_member"] = $this->lang->line("bm_feature_required");
            }
            if ($post_data["is_shareholder"] && (!isset($post_data["sh_collaborator"]) || !$post_data["party_id"])) {
                $response["validation_errors"]["shareholder"] = $this->lang->line("sh_feature_required");
            }
            if (!isset($response["validation_errors"])) {
                $result = $action === "edit" ? $this->contract_signature_status->update() : $this->contract_signature_status->insert();
                if ($result) {
                    $contract_signature_status_id = $this->contract_signature_status->get_field("id");
                    $this->contract_signature_user->delete(["where" => ["contract_signature_status_id", $contract_signature_status_id]]);
                    $this->contract_signature_collaborator->delete(["where" => ["contract_signature_status_id", $contract_signature_status_id]]);
                    $this->contract_signature_user_group->delete(["where" => ["contract_signature_status_id", $contract_signature_status_id]]);
                    if (isset($post_data["bm_collaborator"]) && !empty($post_data["bm_collaborator"])) {
                        foreach ($post_data["bm_collaborator"] as $signature_id) {
                            $this->contract_signature_collaborator->reset_fields();
                            $this->contract_signature_collaborator->set_field("contract_signature_status_id", $contract_signature_status_id);
                            $this->contract_signature_collaborator->set_field("user_id", $signature_id);
                            $this->contract_signature_collaborator->set_field("type", "board_member");
                            if ($this->contract_signature_collaborator->insert()) {
                                $this->contract_collaborator->reset_fields();
                                $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                $this->contract_collaborator->set_field("user_id", $signature_id);
                                $this->contract_collaborator->insert();
                                $this->contract->set_field("visible_to_cp", 1);
                                $this->contract->update();
                            }
                        }
                    }
                    if (isset($post_data["sh_collaborator"]) && !empty($post_data["sh_collaborator"])) {
                        foreach ($post_data["sh_collaborator"] as $signature_id) {
                            $this->contract_signature_collaborator->reset_fields();
                            $this->contract_signature_collaborator->set_field("contract_signature_status_id", $contract_signature_status_id);
                            $this->contract_signature_collaborator->set_field("user_id", $signature_id);
                            $this->contract_signature_collaborator->set_field("type", "shareholder");
                            if ($this->contract_signature_collaborator->insert()) {
                                $this->contract_collaborator->reset_fields();
                                $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                $this->contract_collaborator->set_field("user_id", $signature_id);
                                $this->contract_collaborator->insert();
                                $this->contract->set_field("visible_to_cp", 1);
                                $this->contract->update();
                            }
                        }
                    }
                    if (isset($post_data["Approvers_Signees"]) && !empty($post_data["Approvers_Signees"])) {
                        foreach ($post_data["Approvers_Signees"] as $key => $signee_id) {
                            $type = $post_data["Approvers_Signees_types"][$key];
                            $model = "contract_signature_" . strtolower($type);
                            $this->{$model}->reset_fields();
                            $this->{$model}->set_field("contract_signature_status_id", $contract_signature_status_id);
                            $this->{$model}->set_field($type == "contact" ? "contact_id" : "user_id", $signee_id);
                            if (!$this->{$model}->fetch(["contract_signature_status_id" => $contract_signature_status_id, $type == "Contact" ? "contact_id" : "user_id" => $signee_id]) && $this->{$model}->insert() && $type == "Collaborator") {
                                $this->contract_collaborator->reset_fields();
                                $this->contract_collaborator->set_field("contract_id", $post_data["contract_id"]);
                                $this->contract_collaborator->set_field("user_id", $signee_id);
                                $this->contract_collaborator->insert();
                                $this->contract->set_field("visible_to_cp", 1);
                                $this->contract->update();
                            }
                        }
                    }
                    if (isset($post_data["user_groups"]) && !empty($post_data["user_groups"])) {
                        foreach ($post_data["user_groups"] as $user_group_id) {
                            $this->contract_signature_user_group->reset_fields();
                            $this->contract_signature_user_group->set_field("contract_signature_status_id", $contract_signature_status_id);
                            $this->contract_signature_user_group->set_field("user_group_id", $user_group_id);
                            $this->contract_signature_user_group->insert();
                        }
                    }
                    $response["history_result"] = $this->log_signature_history($this->contract_signature_status->get_fields(), $action, $old_data);
                    $this->contract_signature_status->reset_fields();
                    $response["overall_status"] = $data["overall_status"];
                    $response["result"] = true;
                }
            }
        }
        return $response;
    }
    private function log_signature_history($data, $action, $old_data = [])
    {
        $this->load->model("contract_signature_history", "signature_historyfactory");
        $this->contract_signature_history = $this->signature_historyfactory->get_instance();
        $this->contract_signature_history->set_field("done_on", date("Y-m-d H:i:s"));
        $this->contract_signature_history->set_field("done_by", $this->is_auth->get_user_id());
        $this->contract_signature_history->set_field("done_by_type", "user");
        $this->contract_signature_history->set_field("from_action", $action === "edit" ? $old_data["status"] : "-");
        $this->contract_signature_history->set_field("to_action", $data["status"]);
        $this->contract_signature_history->set_field("contract_id", $data["contract_id"]);
        $this->contract_signature_history->set_field("label", $data["label"]);
        $this->contract_signature_history->set_field("action", $action);
        $key = $action === "edit" ? "edit_signee_comment" : "add_signee_comment";
        if ($action === "edit") {
            unset($old_data["status"]);
            unset($data["status"]);
            $diff = array_diff($old_data, $data);
            if (!empty($diff)) {
                $comment = $this->lang->line($key) . "</br>";
                foreach ($diff as $k => $old_val) {
                    $comment .= $this->lang->line($k) . ": " . $this->lang->line("from") . " " . $old_val . " " . $this->lang->line("to") . " " . $data[$k] . "</br>";
                }
            } else {
                $comment = $this->lang->line($key);
            }
        } else {
            $comment = $this->lang->line($key);
        }
        $this->contract_signature_history->set_field("comment", $comment);
        return $this->contract_signature_history->insert();
    }
    public function edit_signature_signee_per_contract()
    {
        $response = $this->signature_signee_per_contract("edit");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function sign_contract_doc()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $response["result"] = false;
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        if ($this->input->get(NULL, true)) {
            $get_data = $this->input->get(NULL, true);
            $contract_id = $get_data["contract_id"];
            if (isset($get_data["contract_signature_status_id"]) && $this->contract_signature_status->fetch($get_data["contract_signature_status_id"])) {
                $signees = $this->contract_signature_status->load_signature_data($get_data["contract_signature_status_id"]);
                $allowed = false;
                if ($signees["users"] && in_array($this->is_auth->get_user_id(), explode(",", $signees["users"]))) {
                    $allowed = true;
                }
                if ($signees["user_groups"] && in_array($this->session->userdata("AUTH_user_group_id"), explode(",", $signees["user_groups"]))) {
                    $allowed = true;
                }
                if ($signees["is_requester_manager"]) {
                    $this->contract->fetch($contract_id);
                    $manager = $this->contract->load_requester_manager($this->contract->get_field("requester_id"));
                    if ($manager && $manager["id"] == $this->is_auth->get_user_id()) {
                        $allowed = true;
                    }
                }
                if (!$allowed) {
                    $response["display_message"] = $this->lang->line("no_permission_to_sign");
                } else {
                    $data["docs"] = $this->contract->load_approval_signature_documents($contract_id, "docx");
                    if (empty($data["docs"])) {
                        $response["display_message"] = $this->lang->line("no_related_contracts");
                    } else {
                        $data["title"] = $this->lang->line("contract_to_sign");
                        $data["contract_id"] = $contract_id;
                        $data["id"] = $this->input->get("contract_signature_status_id", true);
                        $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
                        $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
                        $data["signatures"] = $this->user_signature_attachment->load_all(["where" => ["user_id", $this->is_auth->get_user_id()]]);
                        $response["result"] = true;
                        $response["html"] = $this->load->view("contracts/view/signature_center/contract_list", $data, true);
                    }
                }
            }
        } else {
            $post_data = $this->input->post(NULL, true);
            if ($this->input->post("document_id", true)) {
                $this->document_management_system->fetch(["id" => $post_data["document_id"]]);
                $doc_details = $this->document_management_system->get_fields();
                $this->document_management_system->reset_fields();
                $core_path = substr(COREPATH, 0, -12);
                $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
                $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];
                $this->document_management_system->fetch($doc_details["parent"]);
                $lineage = $this->document_management_system->get_field("lineage");
                $template_dir = $documents_root_direcotry . "contracts" . $lineage;
                if (is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
                    copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
                    require_once $core_path . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                    $docx = new CreateDocxFromTemplate($tmp_file);
                    $docx->setTemplateSymbol("%%");
                    $template_variables = $docx->getTemplateVariables();
                    $template_variables = array_filter($template_variables);
                    if (empty($template_variables)) {
                        $response["display_message"] = $this->lang->line("no_variable_to_replace");
                    } else {
                        if (isset($post_data["id"]) && $this->contract_signature_status->fetch($post_data["id"])) {
                            if (!isset($post_data["signature_id"]) || !isset($post_data["variable_name"])) {
                                $response["display_message"] = $this->lang->line("no_signature_variable");
                            } else {
                                $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
                                $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
                                $this->user_signature_attachment->fetch($post_data["signature_id"]);
                                $signature_variable = $post_data["variable_name"];
                                $user_signature = $this->user_signature_attachment->get_field("signature");
                                $signature_picture = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($this->is_auth->get_user_id(), 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "signature" . DIRECTORY_SEPARATOR . $user_signature;
                                if ($user_signature && file_exists($signature_picture)) {
                                    $wf = new WordFragment($docx, "document");
                                    $wf->addImage(["src" => $signature_picture, "width" => 150, "height" => 40]);
                                    $docx->replaceVariableByWordFragment([$signature_variable => $wf], ["type" => "inline"]);
                                    $file_path = $template_dir . DIRECTORY_SEPARATOR . rand(1000, 9999);
                                    $docx->createDocx($file_path);
                                    require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                                    $docx = new Phpdocx\Create\CreateDocx();
                                    $doc_details["extension"] = "pdf";
                                    $docx->transformDocument($file_path . ".docx", $file_path . "." . $doc_details["extension"], "libreoffice");
                                    $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                                    $this->document_management_system->reset_fields();
                                    $this->document_management_system->set_fields(["type" => "file", "name" => $doc_details["name"], "extension" => $doc_details["extension"], "size" => filesize($file_path . "." . $doc_details["extension"]), "parent" => $doc_details["parent"], "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "document_type_id" => NULL, "document_status_id" => NULL, "comment" => NULL, "module" => $doc_details["module"], "module_record_id" => $doc_details["module_record_id"], "system_document" => 0, "visible" => 1, "visible_in_cp" => 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->is_auth->get_user_id(), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id(), "modifiedByChannel" => "A4L"]);
                                    if ($this->document_management_system->insert()) {
                                        $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                                        if ($this->document_management_system->update()) {
                                            if (rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                                                $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                                                $response["result"] = true;
                                                if (empty($file_existant_version)) {
                                                    $response["result"] = true;
                                                    $response["display_message"] = $this->lang->line("doc_generated_successfully");
                                                } else {
                                                    $this->file_versioning($file_existant_version, $uploaded_file, $response);
                                                }
                                            }
                                        } else {
                                            $response["validation_errors"] = $this->document_management_system->get("validationErrors");
                                        }
                                    } else {
                                        $response["validation_errors"] = $this->document_management_system->get("validationErrors");
                                    }
                                } else {
                                    $response["display_message"] = $this->lang->line("no_signature_saved");
                                }
                                if ($response["result"]) {
                                    $this->update_contract_signature_status($post_data["contract_id"]);
                                } else {
                                    $response["display_message"] = $this->lang->line("contract_file_not_found");
                                }
                            }
                        }
                    }
                    unlink($tmp_file);
                } else {
                    $response["display_message"] = $this->lang->line("contract_file_not_found");
                }
            } else {
                $response["display_message"] = $this->lang->line("contract_required");
            }
            $this->contract_signature_submission->fetch(["contract_id" => $post_data["contract_id"]]);
            $response["overall_status"] = $this->contract_signature_submission->get_field("status");
            $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($post_data["contract_id"]);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function file_versioningaug2025($file_existant_version, $uploaded_file, &$response)
    {
        $template_dir = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "contracts";
        $versions_container = [];
        if ($file_existant_version["version"] == 1) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->set_fields(["name" => $uploaded_file["id"] . "_versions", "type" => "folder", "parent" => $uploaded_file["parent"], "module" => $uploaded_file["module"], "module_record_id" => $uploaded_file["module_record_id"], "system_document" => 1, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->is_auth->get_user_id(), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id(), "modifiedByChannel" => "A4L"]);
            if ($this->document_management_system->insert()) {
                $versions_container_lineage = empty($uploaded_file["parent_lineage"]) ? DIRECTORY_SEPARATOR . $uploaded_file["parent"] : $uploaded_file["parent_lineage"];
                $this->document_management_system->set_field("lineage", $versions_container_lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                if ($this->document_management_system->update() && mkdir($template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("lineage"))) {
                    $versions_container = $this->document_management_system->get_fields();
                }
            }
        } else {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch(["name" => $file_existant_version["id"] . "_versions", "system_document" => 1]);
            $this->document_management_system->set_field("name", $uploaded_file["id"] . "_versions");
            if ($this->document_management_system->update()) {
                $versions_container = $this->document_management_system->get_fields();
            }
        }
        if (!empty($versions_container)) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($file_existant_version["id"]);
            $versioned_file_lineage = $versions_container["lineage"] . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id");
            $this->document_management_system->set_fields(["parent" => $versions_container["id"], "lineage" => $versioned_file_lineage, "visible" => 0, "visible_in_cp" => 0, "visible_in_ap" => 0]);
            if ($this->document_management_system->update() && rename($template_dir . $file_existant_version["lineage"], $template_dir . $versioned_file_lineage)) {
                $response["result"] = true;
            }
        }
    }
    private function file_versioning($file_existant_version, $uploaded_file, &$response)
    {
        $template_dir = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "contracts";

        $versions_container = [];
        if ($file_existant_version["version"] == 1) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->set_fields([
                "name" => $uploaded_file["id"] . "_versions",
                "type" => "folder",
                "parent" => $uploaded_file["parent"],
                "module" => $uploaded_file["module"],
                "module_record_id" => $uploaded_file["module_record_id"],
                "system_document" => 1,
                "visible" => 0,
                "visible_in_cp" => 0,
                "visible_in_ap" => 0,
                "createdOn" => date("Y-m-d H:i:s"),
                "createdBy" => $this->is_auth->get_user_id(),
                "createdByChannel" => "A4L",
                "modifiedOn" => date("Y-m-d H:i:s"),
                "modifiedBy" => $this->is_auth->get_user_id(),
                "modifiedByChannel" => "A4L"
            ]);

            if ($this->document_management_system->insert()) {
                // FIX: Properly construct the versions container lineage
                $parent_lineage = $uploaded_file["parent_lineage"] ?? '';
                $versions_container_lineage = !empty($parent_lineage) ?
                    $parent_lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id") :
                    DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id");

                // Remove any double slashes
                $versions_container_lineage = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $versions_container_lineage);

                $this->document_management_system->set_field("lineage", $versions_container_lineage);

                if ($this->document_management_system->update()) {
                    // FIX: Create the versions directory with proper path
                    $versions_dir = $template_dir . $versions_container_lineage;
                    if (!is_dir($versions_dir)) {
                        if (mkdir($versions_dir, 0755, true)) {
                            $versions_container = $this->document_management_system->get_fields();
                        }
                    } else {
                        $versions_container = $this->document_management_system->get_fields();
                    }
                }
            }
        } else {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch(["name" => $file_existant_version["id"] . "_versions", "system_document" => 1]);

            if ($this->document_management_system->get_field("id")) {
                $this->document_management_system->set_field("name", $uploaded_file["id"] . "_versions");
                if ($this->document_management_system->update()) {
                    $versions_container = $this->document_management_system->get_fields();
                }
            }
        }

        if (!empty($versions_container)) {
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($file_existant_version["id"]);

            if ($this->document_management_system->get_field("id")) {
                // FIX: Properly construct the versioned file lineage
                $versioned_file_lineage = $versions_container["lineage"] . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id");

                // Remove any double slashes
                $versioned_file_lineage = str_replace(DIRECTORY_SEPARATOR . DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $versioned_file_lineage);

                $this->document_management_system->set_fields([
                    "parent" => $versions_container["id"],
                    "lineage" => $versioned_file_lineage,
                    "visible" => 0,
                    "visible_in_cp" => 0,
                    "visible_in_ap" => 0
                ]);

                if ($this->document_management_system->update()) {
                    $source_path = $template_dir . $file_existant_version["lineage"];
                    $destination_path = $template_dir . $versioned_file_lineage;

                    // FIX: Check if source file exists before moving
                    if (file_exists($source_path)) {
                        // Ensure destination directory exists
                        $destination_dir = dirname($destination_path);
                        if (!is_dir($destination_dir)) {
                            mkdir($destination_dir, 0755, true);
                        }

                        if (rename($source_path, $destination_path)) {
                            $response["result"] = true;
                        } else {
                            // Fallback: try copy if rename fails
                            if (copy($source_path, $destination_path)) {
                                unlink($source_path);
                                $response["result"] = true;
                            }
                        }
                    } else {
                        // Source file doesn't exist, but we can still proceed
                        $response["result"] = true;
                        log_message('warning', 'Source file not found during versioning: ' . $source_path);
                    }
                }
            }
        }

        return $response;
    }
    public function delete_signee_per_contract()
    {
        $id = $this->input->post("id");
        $response = [];
        $response["result"] = false;
        $this->load->model("contract_signature_user", "contract_signature_userfactory");
        $this->contract_signature_user = $this->contract_signature_userfactory->get_instance();
        $this->load->model("contract_signature_user_group", "contract_signature_user_groupfactory");
        $this->contract_signature_user_group = $this->contract_signature_user_groupfactory->get_instance();
        $this->load->model("contract_signature_contact", "contract_signature_contactfactory");
        $this->contract_signature_contact = $this->contract_signature_contactfactory->get_instance();
        $this->load->model("contract_signature_collaborator", "contract_signature_collaboratorfactory");
        $this->contract_signature_collaborator = $this->contract_signature_collaboratorfactory->get_instance();
        $this->load->model("contract_signature_status", "contract_signature_statusfactory");
        $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
        $old_data = $this->contract_signature_status->load_signature_details($id);
        $this->contract_signature_user->delete(["where" => ["contract_signature_status_id", $id]]);
        $this->contract_signature_collaborator->delete(["where" => ["contract_signature_status_id", $id]]);
        $this->contract_signature_user_group->delete(["where" => ["contract_signature_status_id", $id]]);
        $this->contract_signature_contact->delete(["where" => ["contract_signature_status_id", $id]]);
        if ($this->contract_signature_status->delete($id)) {
            $this->load->model("contract_signature_history", "signature_historyfactory");
            $this->contract_signature_history = $this->signature_historyfactory->get_instance();
            $this->contract_signature_history->set_field("done_on", date("Y-m-d H:i:s"));
            $this->contract_signature_history->set_field("done_by", $this->is_auth->get_user_id());
            $this->contract_signature_history->set_field("done_by_type", "user");
            $this->contract_signature_history->set_field("from_action", $old_data["status"]);
            $this->contract_signature_history->set_field("to_action", "-");
            $this->contract_signature_history->set_field("contract_id", $old_data["contract_id"]);
            $this->contract_signature_history->set_field("label", $old_data["label"]);
            $this->contract_signature_history->set_field("action", "delete");
            unset($old_data["id"]);
            unset($old_data["contract_id"]);
            unset($old_data["status"]);
            $comment = $this->lang->line("delete_signee_comment") . "</br>";
            foreach ($old_data as $k => $old_val) {
                $comment .= $this->lang->line($k) . ": " . $old_val . "</br>";
            }
            $this->contract_signature_history->set_field("comment", $comment);
            $this->contract_signature_history->insert();
            $response["result"] = true;
            $response["display_message"] = $this->lang->line("record_deleted");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function awaiting_my_signatures()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("awaiting_my_signatures"));
        $this->authenticate_exempted_actions();
        $this->get_awaiting_signatures();
    }
    private function get_awaiting_signatures($all = false)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
        $category = "awaiting_signatures";
        $data["model"] = $category;
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($category, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($category, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($category));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
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
            $response = array_merge($response, $this->contract_signature_submission->load_awaiting_signatures($filter, $sortable));
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["text_empty"] = $this->get_filter_operators("text_empty");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");
            $this->load->model("contract_status_language", "contract_status_languagefactory");
            $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
            $data["statuses"] = $this->contract_status_language->load_list_per_language();
            $data["categories"] = $this->party_category_language->load_list_per_language();
            $data["types"] = $this->contract_type_language->load_list_per_language();
            unset($data["statuses"][""]);
            unset($data["categories"][""]);
            unset($data["types"][""]);
            $data["dataCustomFields"] = $this->custom_field->load_list_per_language($this->contract->get("modelName"));
            $data["priorities"] = array_combine($this->contract->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $data["archivedValues"] = array_combine($this->contract->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";
            $this->load->model(["provider_group"]);
            $data["assigned_teams"] = $this->provider_group->load_list([]);
            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $data["users_list"] = $this->user->load_all_list();
            $this->load->model("user_group", "user_groupfactory");
            $this->user_group = $this->user_groupfactory->get_instance();
            $data["user_groups_list"] = $this->user_group->load_list(["where" => ["name !=", $this->user_group->get("superAdminInfosystaName")]]);
            $data["users_list"][0] = "";
            if (!$all) {
                $data["signee_user"] = $this->session->userdata("AUTH_user_id");
                $data["signee_user_group"] = $this->session->userdata("AUTH_user_group_id");
                $data["my_signatures"] = true;
            } else {
                $data["signee_user"] = "";
                $data["signee_user_group"] = "";
                $data["all_signatures"] = true;
            }
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("contract/awaiting_signatures", "js");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("awaiting_signatures/index", $data);
            $this->load->view("partial/footer");
        }
    }
    public function awaiting_signatures()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("awaiting_signatures"));
        $this->get_awaiting_signatures(true);
    }
    public function get_signature_picture($id = 0)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if ($id < 0) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("contracts/index");
        }
        $this->load->model("user_signature_attachment", "user_signature_attachmentfactory");
        $this->user_signature_attachment = $this->user_signature_attachmentfactory->get_instance();
        $this->user_signature_attachment->fetch($id);
        $user_id = $this->user_signature_attachment->get_field("user_id");
        $signature_picture = $this->user_signature_attachment->get_field("signature");
        if (!empty($signature_picture)) {
            $fileDirectory = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "users" . DIRECTORY_SEPARATOR . str_pad($user_id, 10, "0", STR_PAD_LEFT) . DIRECTORY_SEPARATOR . "signature";
            $file = $fileDirectory . DIRECTORY_SEPARATOR . $signature_picture;
            $content = @file_get_contents($file);
            if ($content) {
                $this->load->helper("download");
                force_download($signature_picture, $content);
            }
        }
    }
    public function hide_in_customer_portal($contract_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = false;
        if ($this->contract->fetch($contract_id)) {
            $this->contract->set_field("visible_to_cp", 0);
            if (!$this->contract->update()) {
                $response["display_message"] = $this->lang->line("updates_failed");
            } else {
                $response["result"] = true;
                $data["contract"] = $this->contract->get_fields();
                $data["visible_to_cp"] = !strcmp($this->contract->get_field("channel"), $this->cp_channel) || $this->contract->get_field("visible_to_cp") == "1";
                $response["header_actions_section_html"] = $this->load->view("contracts/view/header/actions_section", $data, true);
            }
        } else {
            $response["display_message"] = $this->lang->line("invalid_record");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function show_in_customer_portal($contract_id = 0, $is_form = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = false;
        if ($contract_id) {
            if ($this->contract->fetch($contract_id)) {
                $response["visible"] = $this->contract->get_field("visible_to_cp");
                if ($this->contract->get_field("channel") == "CP") {
                    $response["display_message"] = $this->lang->line("contract_is_imported_from_cp");
                } else {
                    $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
                    $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
                    $data["requester_id"] = $this->contract->get_field("requester_id");
                    $data["contract"] = $this->contract->load_data($contract_id);
                    $data["title"] = $this->lang->line("show_contract_in_customer_portal");
                    $data["watchers"] = $this->customer_portal_contract_watcher->get_contract_watchers($contract_id);
                    $data["is_form"] = $is_form;
                    $response["html"] = $this->load->view("customer_portal/show_in_customer_portal", $data, true);
                    $response["result"] = true;
                }
            } else {
                $response["display_message"] = $this->lang->line("invalid_record");
            }
        } else {
            $response = $this->save_show_in_customer_portal();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function save_show_in_customer_portal()
    {
        $response["result"] = false;
        $post_data = $this->input->post(NULL);
        if (!empty($post_data["contract_id"])) {
            $this->load->model("customer_portal_users", "customer_portal_usersfactory");
            $this->customer_portal_users = $this->customer_portal_usersfactory->get_instance();
            $this->load->model("contact", "contactfactory");
            $this->contact = $this->contactfactory->get_instance();
            if (empty($post_data["requester_id"])) {
                $response["validation_errors"]["requester_id"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                if (!$this->contact->fetch($post_data["requester_id"])) {
                    $response["display_message"] = $this->lang->line("invalid_record");
                } else {
                    $this->load->library("email_notifications");
                    $response["result"] = true;
                    if (!$this->customer_portal_users->fetch(["contact_id" => $this->contact->get_field("id")])) {
                        $add_requested_by_as_cp_user = $this->customer_portal_users->add_requested_by_as_cp_user("contract");
                        $response["result"] = $add_requested_by_as_cp_user["result"];
                        if (isset($add_requested_by_as_cp_user["message"])) {
                            $response["display_message"] = $add_requested_by_as_cp_user["message"];
                        }
                    }
                    if ($response["result"]) {
                        $this->contract->fetch(["id" => $post_data["contract_id"]]);
                        $this->contract->set_field("requester_id", $post_data["requester_id"]);
                        $this->contract->set_field("visible_to_cp", 1);
                        if ($this->contract->update()) {
                            $licenses = $this->licensor->get_all_licenses();
                            if ($this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("notify_requested_by_watchers_cp")) {
                                $this->load->model("contact_emails");
                                $to_emails = $this->contact_emails->load_contact_emails($this->contact->get_field("id"));
                                $cc_emails = "";
                                if (!empty($post_data["watchers"]) && is_array($post_data["watchers"])) {
                                    foreach ($post_data["watchers"] as $item) {
                                        $cc_emails_sperator = empty($cc_emails) ? "" : ";";
                                        $this->customer_portal_users->reset_fields();
                                        $this->customer_portal_users->fetch($item);
                                        $cc_emails = $cc_emails . $cc_emails_sperator . $this->customer_portal_users->get_field("email");
                                    }
                                }
                                $notifications_data = ["to" => $to_emails, "cc" => $cc_emails, "object" => "contract_notify_requested_by_watchers_cp", "objectModelCode" => $this->contract->get("modelCode"), "object_id" => $post_data["contract_id"], "object_name" => $this->contract->get_field("name"), "requested_by_name_cp" => $post_data["requester_name"], "category_cp" => $this->lang->line("contract"), "department_cp" => $licenses["core"]["App4Legal"]["clientName"], "controller" => "contracts", "fromLoggedUser" => $this->is_auth->get_fullname()];
                                $this->email_notifications->notify($notifications_data);
                            }
                            $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
                            $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
                            $watchers = $this->input->post("watchers") ? $this->input->post("watchers") : NULL;
                            $this->customer_portal_contract_watcher->add_watchers_to_contract($watchers, $this->input->post("contract_id"));
                            $response["result"] = true;
                            if ($post_data["is_form"] == 1) {
                                $data["contract"] = $this->contract->get_fields();
                                $data["visible_to_cp"] = !strcmp($this->contract->get_field("channel"), $this->cp_channel) || $this->contract->get_field("visible_to_cp") == "1";
                                $response["header_actions_section_html"] = $this->load->view("contracts/view/header/actions_section", $data, true);
                                $data_contract = $this->load_contract_data($post_data["contract_id"]);
                                $response["right_section_html"] = $this->load->view("contracts/view/right_section", $data_contract, true);
                            }
                        } else {
                            $response["validation_errors"] = $this->contract->get("validationErrors");
                        }
                    }
                }
            }
        }
        return $response;
    }
    private function save_watchers()
    {
        $response["result"] = false;
        $post_data = $this->input->post(NULL);
        if (!empty($post_data["contract_id"])) {
            $this->load->model("customer_portal_contract_watcher", "customer_portal_contract_watcherfactory");
            $this->customer_portal_contract_watcher = $this->customer_portal_contract_watcherfactory->get_instance();
            $watchers = !empty($post_data["contract_watchers"]) ? $post_data["contract_watchers"] : NULL;
            $this->customer_portal_contract_watcher->add_watchers_to_contract($watchers, $post_data["contract_id"]);
            $response["result"] = true;
        }
        return $response;
    }
    public function show_hide_document_in_cp()
    {
        $this->dmsnew->model->fetch($this->input->post("id"));
        $this->contract->fetch($this->dmsnew->model->get_field("module_record_id"));
        if ($this->contract->get_field("visible_to_cp") || $this->contract->get_field("channel") == "CP") {
            $module = "contract";
            $response = $this->dmsnew->show_hide_document_in_cp($this->input->post("id"), $module);
        } else {
            $response["info"] = sprintf($this->lang->line("contract_not_shared_in_cp"), $this->lang->line($this->dmsnew->model->get_field("type")));
        }
        $response["result"] = !isset($response["error"]) && !isset($response["info"]) ? true : false;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function show_children_documents_in_cp()
    {
        $this->dmsnew->show_hide_children_documents($this->input->post("id"), "contract", "show");
    }
    public function load_summary()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("index");
        }
        $response["result"] = false;
        $type = $this->input->get("type") ?: $this->input->post("type");
        $model = "contract_" . $type . "_status";
        $model_factory = $model . "factory";
        $this->load->model($model, $model_factory);
        $this->{$model} = $this->{$model_factory}->get_instance();
        if ($this->input->get("approval_signature_status_id") && $this->{$model}->fetch($this->input->get("approval_signature_status_id"))) {
            $data["data"] = $this->{$model}->get_fields();
            $response["result"] = true;
            $data["module"] = "contract";
            $data["type"] = $type;
            $response["html"] = $this->load->view("contracts/summary_form", $data, true);
        }
        if ($this->input->post("approval_signature_status_id") && $this->{$model}->fetch($this->input->post("approval_signature_status_id"))) {
            if (!$this->input->post("summary")) {
                $response["validation_errors"]["summary"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $this->{$model}->set_field("summary", $this->input->post("summary"));
                if ($this->{$model}->update()) {
                    $response["result"] = true;
                } else {
                    $response["validation_errors"] = $this->contract->get("validationErrors");
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_document_variables()
    {
        $response["result"] = false;
        $get_data = $this->input->get(NULL, true);
        if ($this->input->get("document_id", true)) {
            $this->document_management_system->fetch(["id" => $get_data["document_id"]]);
            $doc_details = $this->document_management_system->get_fields();
            $this->document_management_system->reset_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . $doc_details["name"] . "." . $doc_details["extension"];
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "contracts" . $lineage;
            if (is_file($template_dir . DIRECTORY_SEPARATOR . $get_data["document_id"])) {
                copy($template_dir . DIRECTORY_SEPARATOR . $get_data["document_id"], $tmp_file);
                require_once $core_path . "/application/libraries/phpdocx-advanced/classes/CreateDocx.php";
                $docx = new CreateDocxFromTemplate($tmp_file);
                $docx->setTemplateSymbol("%%");
                $template_variables = $docx->getTemplateVariables();
                $variables = array_filter($template_variables);
                if ($variables) {
                    $data["variables"] = array_unique($variables["document"]);
                    $response["result"] = true;
                    $response["html"] = $this->load->view("contracts/view/signature_center/variables", $data, true);
                } else {
                    $response["display_message"] = $this->lang->line("no_variable_to_replace");
                }
                unlink($tmp_file);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_tasks($contract_id = "")
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->task->k_load_all_tasks($filter, $sortable, "", true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->contract->fetch($contract_id);
            $contract_data = $this->contract->get_fields();
            $data["contract"] = $contract_data;
            $data["model_code"] = $this->contract->get("modelCode");
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["archivedValues"] = array_combine($this->task->get("archivedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $this->load->model("task_status");
            $this->load->model("task_type", "task_typefactory");
            $this->task_type = $this->task_typefactory->get_instance();
            $data["type"] = $this->task_type->load_list_per_language();
            $config_status = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_status")]];
            $data["status"] = $this->task_status->load_list([], $config_status);
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $data["assignedToFullName"] = "";
            $data["priorities"] = array_combine($this->task->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $response["html"] = $this->load->view("contracts/view/related_tasks", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function related_legalOpinions($contract_id = "")
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort"); 
            $response = $this->opinion->k_load_all_opinions($filter, $sortable, "", true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $data = [];
            $this->contract->fetch($contract_id);
            $contract_data = $this->contract->get_fields();
            $data["contract"] = $contract_data;
            $data["model_code"] = $this->contract->get("modelCode");
            $data["operatorsText"] = $this->get_filter_operators("text");
            $data["operatorsNumbers"] = $this->get_filter_operators("number");
            $data["operatorsList"] = $this->get_filter_operators("list");
            $data["archivedValues"] = array_combine($this->opinion->get("archivedValues"), ["", $this->lang->line("yes"), $this->lang->line("no")]);
            $this->load->model("opinion_status");
            $this->load->model("opinion_type", "opinion_typefactory");
            $this->opinion_type = $this->opinion_typefactory->get_instance();
            $data["type"] = $this->opinion_type->load_list_per_language();
            $config_status = ["value" => "name", "firstLine" => ["" => $this->lang->line("choose_status")]];
            $data["status"] = $this->opinion_status->load_list([], $config_status);
            $data["toMeId"] = $this->is_auth->get_user_id();
            $data["toMeFullName"] = $this->is_auth->get_fullname();
            $data["assignedToFullName"] = "";
            $data["priorities"] = array_combine($this->opinion->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
            $response["html"] = $this->load->view("contracts/view/related_opinions", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function save_as_pdfold()
    {
        $response["result"] = false;
        if ($this->input->post("document_id", true)) {
            $post_data = $this->input->post(NULL, true);
            $this->document_management_system->fetch($post_data["document_id"]);
            $doc_details = $this->document_management_system->get_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . "." . $doc_details["extension"];
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "contracts" . $lineage;
            if (is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
                copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
                $doc_details["extension"] = "pdf";
                $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                $file_path = $template_dir;
                $docx = new Phpdocx\Create\CreateDocx();
                $docx->transformDocument($tmp_file, $file_path . ".pdf", "libreoffice");
                $this->document_management_system->reset_fields();
                $this->document_management_system->set_fields($doc_details);
                $this->document_management_system->set_fields(["id" => NULL, "extension" => $doc_details["extension"], "size" => filesize($file_path . "." . $doc_details["extension"]), "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1, "createdOn" => date("Y-m-d H:i:s"), "createdBy" => $this->is_auth->get_user_id(), "createdByChannel" => "A4L", "modifiedOn" => date("Y-m-d H:i:s"), "modifiedBy" => $this->is_auth->get_user_id(), "modifiedByChannel" => "A4L", "visible_in_ap" => 0]);
                if ($this->document_management_system->insert()) {
                    $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                    if ($this->document_management_system->update() && rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                        unlink($tmp_file);
                        $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                        if (empty($file_existant_version)) {
                            $response["result"] = true;
                            $response["display_message"] = $this->lang->line("doc_generated_successfully");
                        } else {
                            $this->file_versioning($file_existant_version, $uploaded_file, $response);
                        }
                    }
                    $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($doc_details["module_record_id"]);
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        }
    }
    public function save_as_pdf()
    {
        $response["result"] = false;
        if ($this->input->post("document_id", true)) {
            $post_data = $this->input->post(NULL, true);
            $this->document_management_system->fetch($post_data["document_id"]);
            $doc_details = $this->document_management_system->get_fields();
            $core_path = substr(COREPATH, 0, -12);
            $documents_root_direcotry = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR;
            $tmp_file = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "tmp" . DIRECTORY_SEPARATOR . "contracts" . DIRECTORY_SEPARATOR . rand(1000, 9999) . "." . $doc_details["extension"];
            $this->document_management_system->reset_fields();
            $this->document_management_system->fetch($doc_details["parent"]);
            $lineage = $this->document_management_system->get_field("lineage");
            $template_dir = $documents_root_direcotry . "contracts" . $lineage;

            // Ensure tmp directory exists
            $tmp_dir = dirname($tmp_file);
            if (!is_dir($tmp_dir)) {
                mkdir($tmp_dir, 0755, true);
            }

            if (is_file($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"])) {
                copy($template_dir . DIRECTORY_SEPARATOR . $post_data["document_id"], $tmp_file);
                $doc_details["extension"] = "pdf";
                $file_existant_version = $this->document_management_system->get_document_existant_version($doc_details["name"] . "." . $doc_details["extension"], "file", $lineage);
                require_once $core_path . "/application/libraries/phpdocx-premium-12.5-ns/Classes/Phpdocx/Create/CreateDocx.php";
                $file_path = $template_dir;
                $docx = new Phpdocx\Create\CreateDocx();
                $docx->transformDocument($tmp_file, $file_path . ".pdf", "libreoffice");

                // Wait for the file to be created
                $attempts = 0;
                while ($attempts < 10 && !file_exists($file_path . ".pdf")) {
                    usleep(300000); // 0.3 seconds
                    $attempts++;
                }

                if (file_exists($file_path . ".pdf")) {
                    $this->document_management_system->reset_fields();
                    $this->document_management_system->set_fields($doc_details);

                    // ONLY CHANGE: Add the required 'type' field to the original field set
                    $this->document_management_system->set_fields([
                        "type" => "file", // REQUIRED FIELD ADDED
                        "id" => NULL,
                        "extension" => $doc_details["extension"],
                        "size" => filesize($file_path . "." . $doc_details["extension"]),
                        "version" => empty($file_existant_version) ? 1 : $file_existant_version["version"] + 1,
                        "createdOn" => date("Y-m-d H:i:s"),
                        "createdBy" => $this->is_auth->get_user_id(),
                        "createdByChannel" => "A4L",
                        "modifiedOn" => date("Y-m-d H:i:s"),
                        "modifiedBy" => $this->is_auth->get_user_id(),
                        "modifiedByChannel" => "A4L",
                        "visible_in_ap" => 0
                    ]);

                    if ($this->document_management_system->insert()) {
                        $this->document_management_system->set_field("lineage", $lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"));
                        if ($this->document_management_system->update() && rename($file_path . "." . $doc_details["extension"], $template_dir . DIRECTORY_SEPARATOR . $this->document_management_system->get_field("id"))) {
                            unlink($tmp_file);
                            $uploaded_file = $this->document_management_system->get_document_full_details(["d.id" => $this->document_management_system->get_field("id")]);
                            if (empty($file_existant_version)) {
                                $response["result"] = true;
                                $response["display_message"] = $this->lang->line("doc_generated_successfully");
                            } else {
                                $this->file_versioning($file_existant_version, $uploaded_file, $response);
                            }
                        }
                        $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($doc_details["module_record_id"]);
                    }
                } else {
                    // Conversion failed - clean up
                    if (file_exists($tmp_file)) unlink($tmp_file);
                }
                $this->output->set_content_type("application/json")->set_output(json_encode($response));
            }
        }
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
        $results = $this->contract->lookup($term);
        $this->output->set_content_type("application/json")->set_output(json_encode($results));
    }
    public function approvers_signees_autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->contract->lookup_approvers_signees($term, $this->input->get("lookup_field"));
        $this->output->set_content_type("application/json")->set_output(json_encode($results));
    }
    public function activate_deactivate()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $id = $this->input->post("id", true);
        $status = $this->input->post("status", true);
        $response["result"] = true;
        if ($id) {
            $this->contract->fetch($id);
            $this->contract->set_field("status", $status);
            if (!$this->contract->update()) {
                $response["validation_errors"] = $this->contract->get("validationErrors");
                $response["result"] = false;
            }
        }
        $response["status"] = $this->lang->line($status);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function renew()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = [];
        $response["result"] = true;
        if ($this->input->get()) {
            $original_contract_id = $this->input->get("contract_id");
            if ($this->contract->fetch($original_contract_id)) {
                $data["status"] = $this->contract->get_field("status");
                $data["old_contract_id"] = $original_contract_id;
                $data["archived"] = $this->contract->get_field("archived");
                $data["old_end_date"] = $this->contract->get_field("end_date");
                $data["old_renewal"] = $this->contract->get_field("renewal_type");
                $data["renewals"] = ["" => "---"] + array_combine($this->contract->get("renewal_values"), [$this->lang->line("one_time"), $this->lang->line("renewable_automatically"), $this->lang->line("renewable_with_notice"), $this->lang->line("unlimited_period"), $this->lang->line("other")]);
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $users_emails = $this->user->load_active_emails();
                $data["users_emails"] = array_map(function ($users_emails) {
                    return ["email" => $users_emails];
                }, array_keys($users_emails));
                $this->load->model(["provider_group"]);
                $data["assigned_teams_list"] = $this->provider_group->load_all();
                $this->load->model("reminder", "reminderfactory");
                $this->reminder = $this->reminderfactory->get_instance();
                $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                $data["approval_center"] = false;
                if ($this->contract_approval_submission->fetch(["contract_id" => $original_contract_id])) {
                    $data["approval_center"] = true;
                }
                $data["signature_center"] = false;
                if ($this->contract_signature_submission->fetch(["contract_id" => $original_contract_id])) {
                    $data["signature_center"] = true;
                }
                $response["html"] = $this->load->view("contracts/renew_form", $data, true);
            }
        } else {
            $original_contract_id = $this->input->post("contract_id", true);
            if ($this->input->post("deactivate_original_contract", true) && $this->contract->fetch($original_contract_id)) {
                $this->contract->set_field("status", "Inactive");
                $this->contract->update();
                $response["deactivated"] = "yes";
            }
            if ($this->input->post("archive_original_contract", true) && $this->contract->fetch($original_contract_id)) {
                $this->contract->set_field("archived", "yes");
                $this->contract->update();
                $response["archived"] = "yes";
            }
            $post_data = $this->input->post(NULL, true);
            if ($this->contract->fetch($original_contract_id)) {
                $contract_data = $this->contract->get_fields();
                $this->contract->reset_fields();
                $this->contract->set_fields($contract_data);
                $this->contract->set_fields($post_data);
                $this->contract->set_field("id", NULL);
                $this->contract->set_field("archived", "no");
                $this->contract->set_field("status", "Active");
                if (!$this->input->post("contract_date", true)) {
                    $response["result"] = false;
                    $response["validation_errors"]["contract_date"] = $this->lang->line("cannot_be_blank_rule");
                } else {
                    if ($this->contract->validate()) {
                        $notify_before = $this->input->post("notify_me_before");
                        $end_date = $this->input->post("end_date");
                        if ($notify_before && $end_date && (!$notify_before["time"] || !$notify_before["time_type"] || ($is_not_nb = !is_numeric($notify_before["time"])))) {
                            if ($is_not_nb) {
                                $response["validation_errors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                            } else {
                                $response["validation_errors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                            }
                        } else {
                            $this->contract->insert();
                            $renewed_contract_id = $this->contract->get_field("id");
                            if ($this->input->post("inherit_ac", true) && $this->contract_approval_submission->fetch(["contract_id" => $original_contract_id])) {
                                $this->inherit_approvals($original_contract_id, $renewed_contract_id);
                            }
                            if ($this->input->post("inherit_sc", true) && $this->contract_signature_submission->fetch(["contract_id" => $original_contract_id])) {
                                $this->inherit_signatures($original_contract_id, $renewed_contract_id);
                            }
                            $parties = $this->contract_party->load_all(["where" => ["contract_id", $original_contract_id]]);
                            if ($parties) {
                                foreach ($parties as $party) {
                                    $this->contract_party->reset_fields();
                                    foreach ($party as $key => $val) {
                                        $this->contract_party->set_field($key, $val);
                                    }
                                    $this->contract_party->set_field("id", NULL);
                                    $this->contract_party->set_field("contract_id", $renewed_contract_id);
                                    $this->contract_party->insert();
                                }
                            }
                            $contributors = $this->contract_contributor->load_all(["where" => ["contract_id", $original_contract_id]]);
                            $contributor_array = [];
                            foreach ($contributors as $value) {
                                array_push($contributor_array, $value["user_id"]);
                            }
                            if ($contributors) {
                                foreach ($contributors as $contributor) {
                                    $this->contract_contributor->reset_fields();
                                    foreach ($contributor as $key => $val) {
                                        $this->contract_contributor->set_field($key, $val);
                                    }
                                    $this->contract_contributor->set_field("id", NULL);
                                    $this->contract_contributor->set_field("contract_id", $renewed_contract_id);
                                    $this->contract_contributor->insert();
                                }
                            }
                            $watchers = $this->contract_user->load_all(["where" => ["contract_id", $original_contract_id]]);
                            if ($watchers) {
                                foreach ($watchers as $watcher) {
                                    $this->contract_user->reset_fields();
                                    foreach ($watcher as $key => $val) {
                                        $this->contract_user->set_field($key, $val);
                                    }
                                    $this->contract_user->set_field("id", NULL);
                                    $this->contract_user->set_field("contract_id", $renewed_contract_id);
                                    $this->contract_user->insert();
                                }
                            }
                            $this->load->model("custom_field_value");
                            $custom_fields = $this->custom_field_value->load_all(["where" => ["recordId", $original_contract_id]]);
                            if ($custom_fields) {
                                foreach ($custom_fields as $custom_field) {
                                    $this->custom_field_value->reset_fields();
                                    foreach ($custom_field as $key => $val) {
                                        $this->custom_field_value->set_field($key, $val);
                                    }
                                    $this->custom_field_value->set_field("id", NULL);
                                    $this->custom_field_value->set_field("recordId", $renewed_contract_id);
                                    $this->custom_field_value->insert();
                                }
                            }
                            $this->notify_before_end_date($renewed_contract_id);
                            $this->load->model("language");
                            $lang_id = $this->language->get_id_by_session_lang();
                            $this->contract_type_language->fetch(["type_id" => $this->contract->get_field("type_id"), "language_id" => $lang_id]);
                            $this->contract->send_notifications("renew_contract", ["contributors" => $contributor_array, "logged_in_user" => $this->is_auth->get_fullname()], ["id" => $this->contract->get_field("id")]);
                            $this->load->model("contract_renewal_history", "contract_renewal_historyfactory");
                            $this->contract_renewal_history = $this->contract_renewal_historyfactory->get_instance();
                            $this->contract_renewal_history->set_field("renewed_on", date("Y-m-d"));
                            $this->contract_renewal_history->set_field("renewed_by", $this->is_auth->get_user_id());
                            $this->contract_renewal_history->set_field("comment", sprintf($this->lang->line("renewal_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $renewed_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                            $this->contract_renewal_history->set_field("contract_id", $original_contract_id);
                            $this->contract_renewal_history->set_field("renewal_id", $renewed_contract_id);
                            if ($this->contract_renewal_history->insert()) {
                                $this->load->model("related_contract", "related_contractfactory");
                                $this->related_contract = $this->related_contractfactory->get_instance();
                                $this->related_contract->set_field("contract_a_id", $original_contract_id);
                                $this->related_contract->set_field("contract_b_id", $renewed_contract_id);
                                $this->related_contract->set_field("comments", sprintf($this->lang->line("renewal_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $renewed_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                                if ($this->related_contract->insert()) {
                                    $this->related_contract->reset_fields();
                                    $this->related_contract->set_field("contract_a_id", $renewed_contract_id);
                                    $this->related_contract->set_field("contract_b_id", $original_contract_id);
                                    $this->related_contract->set_field("comments", sprintf($this->lang->line("renewal_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $renewed_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                                    if ($this->related_contract->insert()) {
                                        $data["model_code"] = $this->contract->get("modelCode");
                                        $response["model_code"] = $this->contract->get("modelCode");
                                        $response["new_contract_id"] = $renewed_contract_id;
                                        $data["renewal_history"] = $this->contract_renewal_history->load_history($original_contract_id);
                                        $response["renewal_history_section_html"] = $this->load->view("contracts/view/history/renewal", $data, true);
                                    } else {
                                        $response["validation_errors"]["relation"] = $this->lang->line("contract_relation_error");
                                    }
                                }
                            } else {
                                $response["validation_errors"]["history"] = $this->lang->line("renewal_history_error");
                            }
                            $this->contract->inject_folder_templates($renewed_contract_id, "contract", $this->contract->get_field("type_id"));
                        }
                    }
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function inherit_approvals($original_contract_id, $new_contract)
    {
        $this->contract_approval_submission->reset_fields();
        $this->contract_approval_submission->set_field("contract_id", $new_contract);
        $this->contract_approval_submission->set_field("status", "awaiting_approval");
        if ($this->contract_approval_submission->insert()) {
            $this->load->model("contract_approval_status", "contract_approval_statusfactory");
            $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
            $this->load->model("contract_approval_user", "contract_approval_userfactory");
            $this->contract_approval_user = $this->contract_approval_userfactory->get_instance();
            $this->load->model("contract_approval_collaborator", "contract_approval_collaboratorfactory");
            $this->contract_approval_collaborator = $this->contract_approval_collaboratorfactory->get_instance();
            $this->load->model("contract_collaborator", "contract_collaboratorfactory");
            $this->contract_collaborator = $this->contract_collaboratorfactory->get_instance();
            $this->load->model("contract_approval_user_group", "contract_approval_user_groupfactory");
            $this->contract_approval_user_group = $this->contract_approval_user_groupfactory->get_instance();
            $this->load->model("contract_approval_contact", "contract_approval_contactfactory");
            $this->contract_approval_contact = $this->contract_approval_contactfactory->get_instance();
            $this->load->model("contract_approval_bm_role");
            $approvals = $this->contract_approval_status->load_approvals_per_contract($original_contract_id);
            if ($approvals) {
                $first_order = array_keys(array_column($approvals, "rank"), $approvals[0]["rank"]);
                foreach ($approvals as $i => $approval) {
                    $this->contract_approval_status->reset_fields();
                    $this->contract_approval_status->set_field("contract_id", $new_contract);
                    $this->contract_approval_status->set_field("is_requester_manager", $approval["is_requester_manager"]);
                    $this->contract_approval_status->set_field("is_board_member", $approval["is_board_member"]);
                    $this->contract_approval_status->set_field("is_shareholder", $approval["is_shareholder"]);
                    $this->contract_approval_status->set_field("party_id", $approval["party_id"]);
                    $this->contract_approval_status->set_field("rank", $approval["rank"]);
                    $this->contract_approval_status->set_field("label", $approval["label"]);
                    $status = "pending";
                    if (in_array($i, $first_order)) {
                        $status = "awaiting_approval";
                    }
                    $this->contract_approval_status->set_field("status", $status);
                    if ($this->contract_approval_status->insert()) {
                        $contract_approval_status_id = $this->contract_approval_status->get_field("id");
                        $users = $this->contract_approval_user->load_all(["where" => ["contract_approval_status_id", $approval["id"]]]);
                        if ($users) {
                            foreach ($users as $user) {
                                $this->contract_approval_user->reset_fields();
                                $this->contract_approval_user->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->contract_approval_user->set_field("user_id", $user["user_id"]);
                                $this->contract_approval_user->insert();
                            }
                        }
                        $collaborators = $this->contract_approval_collaborator->load_all(["where" => ["contract_approval_status_id", $approval["id"]]]);
                        if ($collaborators) {
                            foreach ($collaborators as $collaborator) {
                                $this->contract_approval_collaborator->reset_fields();
                                $this->contract_approval_collaborator->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->contract_approval_collaborator->set_field("type", $collaborator["type"]);
                                $this->contract_approval_collaborator->set_field("user_id", $collaborator["user_id"]);
                                $this->contract_approval_collaborator->insert();
                            }
                        }
                        $user_groups = $this->contract_approval_user_group->load_all(["where" => ["contract_approval_status_id", $approval["id"]]]);
                        if ($user_groups) {
                            foreach ($user_groups as $user_group) {
                                $this->contract_approval_user_group->reset_fields();
                                $this->contract_approval_user_group->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->contract_approval_user_group->set_field("user_group_id", $user_group["user_group_id"]);
                                $this->contract_approval_user_group->insert();
                            }
                        }
                        $bm_roles = $this->contract_approval_bm_role->load_all(["where" => ["contract_approval_status_id", $approval["id"]]]);
                        if ($bm_roles) {
                            foreach ($bm_roles as $bm_role) {
                                $this->contract_approval_bm_role->reset_fields();
                                $this->contract_approval_bm_role->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->contract_approval_bm_role->set_field("role_id", $bm_role["role_id"]);
                                $this->contract_approval_bm_role->insert();
                            }
                        }
                        $contacts = $this->contract_approval_contact->load_all(["where" => ["contract_approval_status_id", $approval["id"]]]);
                        if ($contacts) {
                            foreach ($contacts as $contact) {
                                $this->contract_approval_contact->reset_fields();
                                $this->contract_approval_contact->set_field("contract_approval_status_id", $contract_approval_status_id);
                                $this->contract_approval_contact->set_field("contact_id", $contact["contact_id"]);
                                $this->contract_approval_contact->insert();
                            }
                        }
                    }
                }
            }
        }
    }
    private function inherit_signatures($original_contract_id, $new_contract)
    {
        $this->contract_signature_submission->reset_fields();
        $this->contract_signature_submission->set_field("contract_id", $new_contract);
        $this->contract_signature_submission->set_field("status", "awaiting_approval");
        if ($this->contract_signature_submission->insert()) {
            $this->load->model("contract_signature_status", "contract_signature_statusfactory");
            $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
            $this->load->model("contract_signature_user", "contract_signature_userfactory");
            $this->contract_signature_user = $this->contract_signature_userfactory->get_instance();
            $this->load->model("contract_signature_collaborator", "contract_signature_collaboratorfactory");
            $this->contract_signature_collaborator = $this->contract_signature_collaboratorfactory->get_instance();
            $this->load->model("contract_collaborator", "contract_collaboratorfactory");
            $this->contract_collaborator = $this->contract_collaboratorfactory->get_instance();
            $this->load->model("contract_signature_user_group", "contract_signature_user_groupfactory");
            $this->contract_signature_user_group = $this->contract_signature_user_groupfactory->get_instance();
            $this->load->model("contract_signature_bm_role");
            $signatures = $this->contract_signature_status->load_signatures_per_contract($original_contract_id);
            if ($signatures) {
                $first_order = array_keys(array_column($signatures, "rank"), $signatures[0]["rank"]);
                foreach ($signatures as $i => $signature) {
                    $this->contract_signature_status->reset_fields();
                    $this->contract_signature_status->set_field("contract_id", $new_contract);
                    $this->contract_signature_status->set_field("is_requester_manager", $signature["is_requester_manager"]);
                    $this->contract_signature_status->set_field("is_shareholder", $signature["is_shareholder"]);
                    $this->contract_signature_status->set_field("is_board_member", $signature["is_board_member"]);
                    $this->contract_signature_status->set_field("party_id", $signature["party_id"]);
                    $this->contract_signature_status->set_field("rank", $signature["rank"]);
                    $this->contract_signature_status->set_field("label", $signature["label"]);
                    $status = "pending";
                    if (in_array($i, $first_order)) {
                        $status = "awaiting_signature";
                    }
                    $this->contract_signature_status->set_field("status", $status);
                    if ($this->contract_signature_status->insert()) {
                        $contract_signature_status_id = $this->contract_signature_status->get_field("id");
                        $users = $this->contract_signature_user->load_all(["where" => ["contract_signature_status_id", $signature["id"]]]);
                        if ($users) {
                            foreach ($users as $user) {
                                $this->contract_signature_user->reset_fields();
                                $this->contract_signature_user->set_field("contract_signature_status_id", $contract_signature_status_id);
                                $this->contract_signature_user->set_field("user_id", $user["user_id"]);
                                $this->contract_signature_user->insert();
                            }
                        }
                        $collaborators = $this->contract_signature_collaborator->load_all(["where" => ["contract_signature_status_id", $signature["id"]]]);
                        if ($collaborators) {
                            foreach ($collaborators as $collaborator) {
                                $this->contract_signature_collaborator->reset_fields();
                                $this->contract_signature_collaborator->set_field("contract_signature_status_id", $contract_signature_status_id);
                                $this->contract_signature_collaborator->set_field("type", $collaborator["type"]);
                                $this->contract_signature_collaborator->set_field("user_id", $collaborator["user_id"]);
                                $this->contract_signature_collaborator->insert();
                            }
                        }
                        $user_groups = $this->contract_signature_user_group->load_all(["where" => ["contract_signature_status_id", $signature["id"]]]);
                        if ($user_groups) {
                            foreach ($user_groups as $user_group) {
                                $this->contract_signature_user_group->reset_fields();
                                $this->contract_signature_user_group->set_field("contract_signature_status_id", $contract_signature_status_id);
                                $this->contract_signature_user_group->set_field("user_group_id", $user_group["user_group_id"]);
                                $this->contract_signature_user_group->insert();
                            }
                        }
                        $bm_roles = $this->contract_signature_bm_role->load_all(["where" => ["contract_signature_status_id", $signature["id"]]]);
                        if ($bm_roles) {
                            foreach ($bm_roles as $bm_role) {
                                $this->contract_signature_bm_role->reset_fields();
                                $this->contract_signature_bm_role->set_field("contract_signature_status_id", $contract_signature_status_id);
                                $this->contract_signature_bm_role->set_field("role_id", $bm_role["role_id"]);
                                $this->contract_signature_bm_role->insert();
                            }
                        }
                    }
                }
            }
        }
    }
    public function related_reminders($contract_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("reminders");
            }
            $response = [];
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("returnData")) {
                $response = $this->reminder->k_load_all_reminders($filter, $sortable);
                $systemPreferences = $this->session->userdata("systemPreferences");
                if (isset($systemPreferences["hijriCalendarFeature"]) && $systemPreferences["hijriCalendarFeature"]) {
                    foreach ($response["data"] as $key => &$val) {
                        $val["remindDate"] = gregorianToHijri($val["remindDate"], "Y-m-d");
                        $val["createdOn"] = gregorianToHijri($val["createdOn"], "Y-m-d");
                        $val["modifiedOn"] = gregorianToHijri($val["modifiedOn"], "Y-m-d");
                    }
                }
            }
        } else {
            $data = [];
            $this->contract->fetch($contract_id);
            $data["model_code"] = $this->contract->get("modelCode");
            $data["contract"] = $this->contract->get_fields();
            $response["html"] = $this->load->view("contracts/view/related_reminders", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_cases($contract_id = "")
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("contract", "contractfactory");
        $this->contract = $this->contractfactory->get_instance();
        if ($this->input->post(NULL)) {
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $this->load->model("case_related_contract", "case_related_contractfactory");
            $this->case_related_contract = $this->case_related_contractfactory->get_instance();
            $response = $this->case_related_contract->k_load_all_contract_related_cases($filter, $sortable);
        } else {
            $data = [];
            $this->contract->fetch($contract_id);
            $contract_data = $this->contract->get_fields();
            $contract = $this->contract->get_fields();
            $data["model_code"] = $this->contract->get("modelCode");
            $data["contract"] = $contract;
            $data["contract_full_name"] = $contract["name"];
            $response["html"] = $this->load->view("contracts/view/related_cases", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_contracts($contract_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        $this->load->model("related_contract", "related_contractfactory");
        $this->related_contract = $this->related_contractfactory->get_instance();
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("index");
            }
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            $response = $this->related_contract->k_load_all_related_contracts($filter, $sortable);
        } else {
            $data = [];
            $this->contract->fetch($contract_id);
            $contract = $this->contract->get_fields();
            $data["model_code"] = $this->contract->get("modelCode");
            $data["contract"] = $contract;
            $data["contract_full_name"] = $contract["name"];
            $response["html"] = $this->load->view("contracts/view/related_contracts", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_contract_add()
    {
        if ($this->input->post(NULL)) {
            $this->load->model("related_contract", "related_contractfactory");
            $this->related_contract = $this->related_contractfactory->get_instance();
            $response["result"] = false;
            $contract_id = $this->input->post("contract_id");
            $related_contract_id = $this->input->post("related_contract_id");
            if ($contract_id !== $related_contract_id) {
                $related = $this->related_contract->check_contract_relation_exists($contract_id, $related_contract_id);
                if (!$related) {
                    $this->related_contract->set_field("contract_a_id", $contract_id);
                    $this->related_contract->set_field("contract_b_id", $related_contract_id);
                    $response["result"] = $this->related_contract->insert();
                    if ($response["result"]) {
                        $this->related_contract->reset_fields();
                        $this->related_contract->set_field("contract_a_id", $related_contract_id);
                        $this->related_contract->set_field("contract_b_id", $contract_id);
                        if ($this->related_contract->insert()) {
                            $response["display_message"] = sprintf($this->lang->line("contract_relation_succeeded"), $this->contract->get("modelCode") . $contract_id, $this->contract->get("modelCode") . $related_contract_id);
                        }
                    } else {
                        $response["display_message"] = $this->lang->line("contract_relation_failed");
                    }
                } else {
                    $response["display_message"] = sprintf($this->lang->line("contract_relation_exists"), $this->contract->get("modelCode") . $contract_id, $this->contract->get("modelCode") . $related_contract_id);
                }
            } else {
                $response["display_message"] = $this->lang->line("contract_relation_failed");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function related_case_add()
    {
        if ($this->input->post(NULL)) {
            $this->load->model("case_related_contract", "case_related_contractfactory");
            $this->case_related_contract = $this->case_related_contractfactory->get_instance();
            $response["result"] = false;
            $contract_id = $this->input->post("contract_id");
            $related_case_id = $this->input->post("related_case_id");
            $response["result"] = $this->case_related_contract->check_related_contract_existence($related_case_id, $contract_id);
            if ($response["result"]) {
                $this->load->model("case_related_contract", "case_related_contractfactory");
                $this->case_related_contract = $this->case_related_contractfactory->get_instance();
                $data = ["legal_case_id" => $related_case_id, "contract_id" => $contract_id];
                $this->case_related_contract->set_fields($data);
                if ($this->case_related_contract->insert()) {
                    $response["result"] = true;
                    $response["display_message"] = $this->lang->line("updates_saved_successfully");
                } else {
                    $response["display_message"] = $this->lang->line("updates_failed");
                }
            } else {
                $response["display_message"] = sprintf($this->lang->line("legal_case_contract_relation_exists"), $related_case_id, $contract_id);
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function related_contract_edit()
    {
        $response = [];
        $response["result"] = false;
        $response["validationErrors"] = "";
        if ($this->input->post(NULL)) {
            $this->load->model("related_contract", "related_contractfactory");
            $this->related_contract = $this->related_contractfactory->get_instance();
            $data = json_decode($this->input->post("models"), true);
            if (3 <= strlen($data[0]["comments"])) {
                $response["result"] = $this->related_contract->update_multiple_record($data);
            } else {
                $response["validationErrors"] = sprintf($this->lang->line("min_length_rule"), $this->lang->line("comments"), 3);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function related_case_delete()
    {
        $this->load->model("case_related_contract", "case_related_contractfactory");
        $this->case_related_contract = $this->case_related_contractfactory->get_instance();
        if ($this->input->post(NULL)) {
            $response = [];
            $record_id = $this->input->post("recordId");
            if ($record_id) {
                $this->case_related_contract->fetch($record_id);
                $case_id = $this->case_related_contract->get_field("legal_case_id");
                $contract_id = $this->case_related_contract->get_field("contract_id");
                $response["result"] = false;
                $this->db->where("id", $record_id);
                if ($this->db->delete("case_related_contracts")) {
                    $this->case_related_contract->fetch(["legal_case_id" => $contract_id, "contract_id" => $case_id]);
                    $id = $this->case_related_contract->get_field("id");
                    $this->db->where("id", $id);
                    if ($this->db->delete("case_related_contracts")) {
                        $response["result"] = true;
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function related_contract_delete()
    {
        $this->load->model("related_contract", "related_contractfactory");
        $this->related_contract = $this->related_contractfactory->get_instance();
        if ($this->input->post(NULL)) {
            $response = [];
            $record_id = $this->input->post("recordId");
            if ($record_id) {
                $this->related_contract->fetch($record_id);
                $contract_a_id = $this->related_contract->get_field("contract_a_id");
                $contract_b_id = $this->related_contract->get_field("contract_b_id");
                $response["result"] = false;
                $this->db->where("id", $record_id);
                if ($this->db->delete("related_contracts")) {
                    $this->related_contract->fetch(["contract_a_id" => $contract_b_id, "contract_b_id" => $contract_a_id]);
                    $id = $this->related_contract->get_field("id");
                    $this->db->where("id", $id);
                    if ($this->db->delete("related_contracts")) {
                        $response["result"] = true;
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    public function amend()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data = [];
        $response["result"] = true;
        if ($this->input->get()) {
            $original_contract_id = $this->input->get("contract_id");
            if ($this->contract->fetch($original_contract_id)) {
$cat=$this->contract->get_field("category");
                $trigger = "amend_contract";
                $data = $this->load_common_data($trigger,$cat);
                $users_emails = $this->user->load_active_emails();
                $data["users_emails"] = array_map(function ($users_emails) {
                    return ["email" => $users_emails];
                }, array_keys($users_emails));
                $data = array_merge($this->load_contract_data($original_contract_id, "edit"), $this->load_common_data($trigger,$cat));
                $data["contract"]["name"] = sprintf($this->lang->line("amendment_prefix"), date("Y") . date("m")) . $data["contract"]["name"];
                $data["sub_types"] = $this->sub_contract_type_language->load_list_per_type_per_language($data["contract"]["type_id"]);
                $data["assignees"] = ["" => "---"] + $this->user->load_users_list($data["contract"]["assigned_team_id"], ["key" => "id", "value" => "name"]);
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                $data["users_list"] = $this->user->load_available_list();
                $data["title"] = $this->lang->line($trigger);
                $data["assignees"] = ["" => "---"] + $this->user->load_users_list("", ["key" => "id", "value" => "name"]);
                $data["custom_fields"] = $this->custom_field->get_field_html($this->contract->get("modelName"), $original_contract_id);
                $data["approval_center"] = false;
                if ($this->contract_approval_submission->fetch(["contract_id" => $original_contract_id])) {
                    $data["approval_center"] = true;
                }
                $data["signature_center"] = false;
                if ($this->contract_signature_submission->fetch(["contract_id" => $original_contract_id])) {
                    $data["signature_center"] = true;
                }
                $response["html"] = $this->load->view("contracts/amend_form", $data, true);
            }
        } else {
            $sysPref = $this->system_preference->get_key_groups();
            $SystemValues = $sysPref["ContractDefaultValues"];
            $create_new_contract_on_amendment = isset($SystemValues["createNewContractOnAmendment"]) && $SystemValues["createNewContractOnAmendment"] ? $SystemValues["createNewContractOnAmendment"] : "no";
            $original_contract_id = $this->input->post("original_contract_id", true);
            $amended_contract_id=$original_contract_id;//default let it be the original id
            $post_data = $this->input->post(NULL, true);
            $original_contract=[];
            $new_contract=[];
            if ($create_new_contract_on_amendment == "no")
            { //update the contract
                if ($this->contract->fetch($original_contract_id)) {
                    $original_contract=$this->contract->get_fields();
                    //set the fields from the post data for update: type_id, sub_type_id,name,description,value,currency_id,requester_id,contract_date,renewal_type,start_date,end_date,
                    // Clean post data for fields that could violate constraints (e.g. empty string for foreign keys)
                    $safe_fields = [
                        "type_id"        => $post_data["type_id"] ?? null,
                        "sub_type_id"    => $post_data["sub_type_id"] ?? null,
                        "name"           => $post_data["name"] ?? null,
                        "description"    => $post_data["description"] ?? null,
                        "value"          => $post_data["value"] ?? null,
                        "currency_id"    => !empty($post_data["currency_id"]) ? $post_data["currency_id"] : null,
                        "requester_id"   => !empty($post_data["requester_id"]) ? $post_data["requester_id"] : null,
                        "contract_date"  => $post_data["contract_date"] ?? null,
                        "renewal_type"   => $post_data["renewal_type"] ?? null,
                        "start_date"     => $post_data["start_date"] ?? null,
                        "end_date"       => $post_data["end_date"] ?? null,
                    ];

                    // Set the fields for update
                    $this->contract->set_fields($safe_fields);

                    // Attempt to save the update
                    if (!$this->contract->update()) {
                        $response["result"] = false;
                        $response["validation_errors"]["contract"] = $this->lang->line("update_failed");
                    }
               
                }//fetch contract is valid
            } else {
                $response = $this->save(0, "amend_contract");
                if ($response["result"]) {
                    $amended_contract_id = $response["id"];
                }
                //make changes if its a new contract created
                $this->load->model("contract_approval_status", "contract_approval_statusfactory");
                $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
                $this->load->model("contract_signature_status", "contract_signature_statusfactory");
                $this->contract_signature_status = $this->contract_signature_statusfactory->get_instance();
                if ($this->input->post("deactivate_original_contract", true) && $this->contract->fetch($original_contract_id)) {
                    $this->contract->set_field("status", "Inactive");
                    $this->contract->update();
                    $response["deactivated"] = "yes";
                }
                if ($this->input->post("archive_original_contract", true) && $this->contract->fetch($original_contract_id)) {
                    $this->contract->set_field("archived", "yes");
                    $this->contract->update();
                    $response["archived"] = "yes";
                }
                if ($this->input->post("inherit_ac", true) && $this->contract_approval_submission->fetch(["contract_id" => $original_contract_id])) {
                    $this->inherit_approvals($original_contract_id, $amended_contract_id);
                }
                if ($this->input->post("inherit_sc", true) && $this->contract_signature_submission->fetch(["contract_id" => $original_contract_id])) {
                    $this->inherit_signatures($original_contract_id, $amended_contract_id);
                }
                $this->contract->fetch($amended_contract_id);
                $this->contract->set_field("amendment_of", $original_contract_id);
                $this->contract->update();

                //relate the two contracts. ie. the old and the new contract
                $this->load->model("related_contract", "related_contractfactory");
                $this->related_contract = $this->related_contractfactory->get_instance();
                $this->related_contract->set_field("contract_a_id", $original_contract_id);
                $this->related_contract->set_field("contract_b_id", $amended_contract_id);
                $this->related_contract->set_field("comments", sprintf($this->lang->line("amendment_from_history_comment"), $this->contract->get("modelCode") . $amended_contract_id, $this->contract->get("modelCode") . $original_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                if ($this->related_contract->insert()) {
                    $this->related_contract->reset_fields();
                    $this->related_contract->set_field("contract_a_id", $amended_contract_id);
                    $this->related_contract->set_field("contract_b_id", $original_contract_id);
                    $this->related_contract->set_field("comments", sprintf($this->lang->line("amendment_to_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $amended_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                    if ($this->related_contract->insert()) {
                    } else {
                        $response["validation_errors"]["relation"] = $this->lang->line("contract_relation_error");
                    }
                }else{
                     $response["validation_errors"]["relation"] = $this->lang->line("contract_relation_error");
                }

            }


                    $this->load->model("contract_amendment_history", "contract_amendment_historyfactory");
                    $this->contract_amendment_history = $this->contract_amendment_historyfactory->get_instance();
                    $this->contract_amendment_history->set_field("amended_on", date("Y-m-d"));
                    $this->contract_amendment_history->set_field("amended_by", $this->is_auth->get_user_id());
                    $this->contract_amendment_history->set_field("comment", sprintf($this->lang->line("amendment_to_history_comment"), $this->contract->get("modelCode") . $original_contract_id, $this->contract->get("modelCode") . $amended_contract_id, $this->is_auth->get_fullname(), date("Y-m-d H:i:s")));
                    $this->contract_amendment_history->set_field("contract_id", $original_contract_id);
                    $this->contract_amendment_history->set_field("amended_id", $amended_contract_id);
                    $this->contract_amendment_history->set_field("amendment_approval_status", $this->input->post("amendment_approval_status",true));
                    if ($this->contract_amendment_history->insert()) {
                        //update the contract_amendment_history_details table whose fields are 'id', 'amendment_history_id', 'contract_id', 'field_name', 'old_value', 'new_value', 'createdOn'
                        $this->load->model("contract_amendment_history_details","contract_amendment_history_detailsfactory");
                        $this->contract_amendment_history_details = $this->contract_amendment_history_detailsfactory->get_instance();
                        $amendment_history_id= $this->contract_amendment_history->get_field("id");
                        //loop through the original contract fields and compare with the new contract fields(if the new contract is created) or the post contract fields (if the original contract is amended or the create_new_contract_on_amendment is no)


                        //get the new contract fields   
                        $new_contract = $this->contract->get_fields();
                        // foreach ($original_contract as $field_name => $old_value) {
                        //     if (isset($new_contract[$field_name]) && $new_contract[$field_name] != $old_value) {
                        //         $this->contract_amendment_history_details->reset_fields();
                        //         $this->contract_amendment_history_details->set_field("field_name", $field_name);
                        //         $this->contract_amendment_history_details->set_field("old_value", $old_value);
                        //         $this->contract_amendment_history_details->set_field("new_value", $new_contract[$field_name]);
                        //         if (!$this->contract_amendment_history_details->insert()) {
                        //             $response["validation_errors"]["history"] = $this->lang->line("amendment_history_error");
                        //         }
                        //     }
                        // }


                        //load contract helper to compare the fields
                        $this->load->helper("contract");
                        $fields_to_compare = [ "type_id", "sub_type_id", "name", "description", "value",
                            "currency_id", "requester_id", "contract_date", "renewal_type",
                                "start_date", "end_date"];
                              
                        $changes = get_contract_amendment_changes($original_contract, $new_contract, $fields_to_compare);
                        foreach ($changes as $change) {
                            $this->contract_amendment_history_details->reset_fields();
                            $this->contract_amendment_history_details->set_field("field_name", $change["field_name"]);
                            $this->contract_amendment_history_details->set_field("old_value", $change["old_value"]);
                            $this->contract_amendment_history_details->set_field("new_value", $change["new_value"]);
                             $this->contract_amendment_history_details->set_field("amendment_history_id", $amendment_history_id);
                            $this->contract_amendment_history_details->set_field("contract_id", $original_contract_id);
                       
                            if (!$this->contract_amendment_history_details->insert()) {
                                  //  $response["validation_errors"]["history"] = $this->contract_amendment_history_details->get("validationErrors");
                               
                            }else {
                                $response["result"] = true;
                            }
                        }

                        
                        //loading the amendment history details to display in the response
                        $data["model_code"] = $this->contract->get("modelCode");
                        $response["model_code"] = $this->contract->get("modelCode");
                        $response["new_contract_id"] = $amended_contract_id;
                        $data["amendment_history"] = $this->contract_amendment_history->load_history($original_contract_id);
                        $response["amendment_history_section_html"] = $this->load->view("contracts/view/history/amendment", $data, true);
                    } else {
                        $response["validation_errors"]["history"] = $this->contract_amendment_history->get("validationErrors");
                    }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    //get amendment history details
    public function get_amendment_history_details($amendment_history_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $this->load->model("contract_amendment_history_details", "contract_amendment_history_detailsfactory");
        $this->contract_amendment_history_details = $this->contract_amendment_history_detailsfactory->get_instance();
        if ($amendment_history_id) {
            $response["result"] = true;
            $data["amendment_details"] = $this->contract_amendment_history_details->load_all(["where" => ["amendment_history_id", $amendment_history_id]]);
        
             $response["html"] = $this->load->view("contracts/view/history/contract_amendment_history_details", $data, true);
        } else {
            $response["result"] = false;
            $response["message"] = $this->lang->line("invalid_record");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    /**
     * Add a new contract or generate a contract from a template.
     * Handles both the initial form display and the submission of contract data.
     */
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            $this->upload_contract();
        } else {
            if (!$this->input->post(NULL)) {
                $response["result"] = true;
                if ($this->input->get("option", true) && $this->input->get("step", true)) {
                    $option = $this->input->get("option", true);
                    $step = $this->input->get("step", true);
                    $commercial_service_category= $this->input->get("commercial_service_category");
                    switch ($option) {
                        case "choose":
                            if ($step == 1) {
                                $data["types"] = $this->contract_type_language->load_list_per_language($commercial_service_category);
                                $data["title"] = $commercial_service_category=="mou"?$this->lang->line("generate_mou"):$this->lang->line("generate_contract");
                                $response["result"] = true;

                                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_contract");
                                $data["show_notification"] = true;
                                $data["option"] = $option;
                                $data["commercial_service_category"] = $commercial_service_category;
                                $response["html"] = $this->load->view("contracts/generate/form", $data, true);

                            } else {
                                if ($this->input->get("template_id")) {
                                    $this->load->model("contract_template", "contract_templatefactory");
                                    $this->contract_template = $this->contract_templatefactory->get_instance();
                                    $data = $this->contract_template->load_template_data($this->input->get("template_id"));
                                    $users_emails = $this->user->load_active_emails();
                                    $data["users_emails"] = array_map(function ($users_emails) {
                                        return ["email" => $users_emails];
                                    }, array_keys($users_emails));
                                    $this->load->model(["provider_group"]);
                                    $data["assigned_teams_list"] = $this->provider_group->load_all();
                                    $data["today"] = date("Y-m-d");
                                    $this->load->model("reminder", "reminderfactory");
                                    $this->reminder = $this->reminderfactory->get_instance();
                                    $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
                                    $data["channel"] = $this->web_channel;
                                    $data["reference_number"] = $this->get_new_ref_number(); //set default reference number
                                    $response["pages"] = count($data["pages"]);
                                    $response["html"] = $this->load->view("contracts/generate/questionnaire_form", $data, true);
                                } else {
                                    $response["result"] = false;
                                    $response["display_message"] = $this->lang->line("no_contract_template_chosen");
                                }
                            }
                            break;
                        default:
                            if ($step == 1) {
                                $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("add_contract");
                                $data["show_notification"] = true;
                                $data["option"] = $option;
                                $data["commercial_service_category"] = $commercial_service_category;
                                $response["html"] = $this->load->view("contracts/generate/form", $data, true);
                            }
                    }
                }
            } else {
                $option = $this->input->post("option", true);
                switch ($option) {
                    case "upload":
                        $response = $this->upload_contract();
                        break;
                    case "choose":
                        $response = $this->contract->save_contract_from_template();
                        if ($response["result"]) {
                            $this->notify_before_end_date($response["id"]);


                        }
                        break;
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function upload_contract()
    {
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        if (!$this->input->post(NULL)) {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("upload_contract"));
            $data = $this->load_common_data("add_contract");
            $users_emails = $this->user->load_active_emails();
            $data["users_emails"] = array_map(function ($users_emails) {
                return ["email" => $users_emails];
            }, array_keys($users_emails));
            $this->provider_group->fetch(["allUsers" => 1]);
            $data["title"] = $this->lang->line("upload_contract");
            $data["all_teams_id"] = $this->provider_group->get_field("id");
            $data["assignees"] = ["" => "---"] + $this->user->load_users_list("", ["key" => "id", "value" => "name"]);
            $data["default_priority"] = "medium";
            $data["reference_number"] = $this->get_new_ref_number(); //set default reference number

            $data["custom_fields"] = $this->custom_field->get_field_html($this->contract->get("modelName"), 0);
            $this->includes("jquery/dropzone_v6/dropzone.min", "js");
            $this->includes("jquery/dropzone_v6/dropzone", "css");
            $this->includes("contract/upload_contract", "js");
            $this->load->view("partial/header");
            $this->load->view("contracts/generate/upload_contract", $data);
        } else {
            $response = $this->save();
            if ($response["result"]) {
                $this->load->library("dmsnew");
                $failed_uploads_count = 0;
                foreach ($_FILES as $file_key => $file) {
                    if ($file["error"] != 4) {
                        $upload_response = $this->dmsnew->upload_file(["module" => "contract", "module_record_id" => $response["id"], "createdByChannel" => "A4L", "upload_key" => $file_key]);
                        if (!$upload_response["status"]) {
                            $failed_uploads_count++;
                        }
                    }
                }
                if (0 < $failed_uploads_count) {
                    $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                }
            }
            return $response;
        }
    }
    /**
     * Get a new reference number for a contract.
     * This method can be called via AJAX to retrieve a new reference number.
     */
public function get_new_ref_number()
{
    $this->load->model("contract", "contractfactory");
    $this->contract = $this->contractfactory->get_instance();
   return $this->contract->get_new_ref_number();

}
    /**
     * Add custom fields for contract types.
     * This method handles AJAX requests to add custom fields based on the contract type ID.
     */
    public function add_contract_type_custom_fields()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($this->input->get("type_id")) {
            $data["custom_fields"] = $this->custom_field->get_field_html($this->contract->get("modelName"), 0, NULL, false, [], $this->input->get("type_id"));
            $response["html"] = $this->load->view("custom_fields/dialog_form_custom_field_template", ["custom_fields" => $data["custom_fields"]], true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_negotiation()
    {
        $response["result"] = false;
        if ($this->input->get("contract_approval_status_id", true) && $this->input->get("contract_id", true)) {
            $data["title"] = $this->lang->line("start_negotiation");
            $data["module"] = "contract";
            $data["contract_id"] = $this->input->get("contract_id", true);
            $data["contract_approval_status_id"] = $this->input->get("contract_approval_status_id", true);
            $data["hide_show_notification"] = $this->email_notification_scheme->get_hide_show_send_email_notification_by_trigger_action("negotiation_requested");
            $data["show_notification"] = true;
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_center/negotiation/form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_comment_negotiation()
    {
        $response["result"] = false;
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->add_comment_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function forward_negotiation()
    {
        $response["result"] = false;
        if ($this->input->get("negotiation_id", true) && $this->input->get("contract_id", true)) {
            $data["title"] = $this->lang->line("forward_negotiation");
            $data["module"] = "contract";
            $data["contract_id"] = $this->input->get("contract_id", true);
            $data["negotiation_id"] = $this->input->get("negotiation_id", true);
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/approval_center/negotiation/forward", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
            $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
            $response = $this->contract_approval_negotiation->forward_negotiation();
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function complete_negotiation()
    {
        $response["result"] = false;
        $this->load->model("contract_approval_negotiation", "contract_approval_negotiationfactory");
        $this->contract_approval_negotiation = $this->contract_approval_negotiationfactory->get_instance();
        if ($this->input->post(NULL, true)) {
            $response = $this->contract_approval_negotiation->complete_negotiation("", "user", $this->is_auth->get_fullname());
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function download_docs_zip_file()
    {
        $this->_download_docs_zip_file("contract", "contracts", $_GET["selected_items"]);
    }
    public function load_templates_per_contract_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $response["templates"] = [];
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        if ($this->input->get("type_id")) {
            $where[] = ["type_id", $this->input->get("type_id")];
            if ($this->input->get("sub_type_id")) {
                $where[] = ["sub_type_id", $this->input->get("sub_type_id")];
            }
            $response["templates"] = $this->contract_template->load_list(["where" => $where]);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_sub_contract_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $response = $this->sub_contract_type_language->load_all_per_type($this->input->get("type_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_board_members_per_party()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if ($this->input->get("party_id")) {
            $response["users"] = $this->contract_party->fetch_bm_contacts_per_company($this->input->get("party_id"), $this->input->get("role_id"));
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function load_shareholders_per_party()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if ($this->input->get("party_id")) {
            $response["users"] = $this->contract_party->fetch_sh_contacts_per_company($this->input->get("party_id"));
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function archive_unarchive_contracts()
    {
        $response = [];
        $contracts_ids = $this->input->post("contracts_ids");
        if ($contracts_ids[0] == "archive") {
            $all_archived = [];
            for ($x = 0; $x < count($contracts_ids[1]); $x++) {
                $id = $contracts_ids[1][$x];
                $result = $this->db->where("contract.id = " . $id)->update("contract", ["archived" => "yes"]);
                if ($result) {
                    array_push($all_archived, "yes");
                }
            }
            $response["status"] = count($all_archived) == count($contracts_ids[1]) ? true : false;
        } else {
            if ($contracts_ids[0] == "unarchive") {
                $all_unarchived = [];
                for ($x = 0; $x < count($contracts_ids[1]); $x++) {
                    $id = $contracts_ids[1][$x];
                    $result = $this->db->where("contract.id = " . $id)->update("contract", ["archived" => "no"]);
                    if ($result) {
                        array_push($all_unarchived, "no");
                    }
                }
                $response["status"] = count($all_unarchived) == count($contracts_ids[1]) ? true : false;
            } else {
                if (!empty($contracts_ids)) {
                    $contract_details = $this->contract->load(["select" => ["id,archived"], "where" => ["id", $contracts_ids]]);
                    $result = $this->db->where("contract.id = (" . $contracts_ids . ")")->update("contract", ["archived" => $contract_details["archived"] == "no" ? "yes" : "no"]);
                    $response["archived"] = $result ? $contract_details["archived"] == "no" ? "yes" : "no" : $contract_details["archived"];
                    $response["status"] = $result;
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function draft_collaborate($contract_id = "", $document_id = "")
    {
        $data = [];
        $this->contract->fetch($contract_id);
        $contract = $this->contract->get_fields();
        $data["contract"] = $contract;
        $data["contract_id"] = $contract_id;
        $data["model_code"] = $this->contract->get("modelCode");
        $data["docs"] = $this->dmsnew->load_documents_for_collaboration($this->contract->get("modelName"), $contract_id);
        $data["document_id"] = $document_id;


        if (!$this->input->is_ajax_request()) {
            $data["show_toolbar"]=true;
            $this->document_management_system->fetch(["id" => $document_id]);//to get the parent_lineage
            $data["parent_lineage"] = $this->document_management_system->get_field("parent");

            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("draft_and_collaborate"));
            $this->includes("contract/cp_contract_common", "js");
            $this->load->view("partial/header");
            $this->load->view("contracts/view/draft_collaborate", $data);
            $this->load->view("partial/footer");
        } else {
            $data["show_share_button"] = true;
            $response["html"] = $this->load->view("contracts/view/draft_collaborate", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function load_docs_count()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("document_management_system", "document_management_systemfactory");
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $response["count"] = $this->document_management_system->count_contract_related_documents($this->input->get("contract_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function share_doc()
    {
        $this->authenticate_exempted_actions();
        $response["result"] = true;
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        if ($this->input->post()) {
            if (!$this->input->post("emails")) {
                $response["result"] = false;
                $response["validation_errors"]["emails"] = $this->lang->line("cannot_be_blank_rule");
            } else {
                $emails = explode(";", $this->input->post("emails"));
                $this->load->model("system_preference");
                $systemPreferences = $this->system_preference->get_key_groups();
                $subject = $systemPreferences["OutgoingMail"]["outgoingMailSubjectPrefix"] . " " . $this->contract->get("modelCode") . $this->input->post("module_id") . ": " . sprintf($this->lang->line("share_doc_email_subject"), $this->input->post("doc_name"));
                $active_emails = $this->user->load_active_emails();
                $module_id = $this->input->post("module_id");
                foreach ($emails as $email) {
                    $is_external_email = !in_array($email, $active_emails);
                    if ($is_external_email) {
                        $this->load->model("external_user_token", "external_user_tokenfactory");
                        $this->external_user_token = $this->external_user_tokenfactory->get_instance();
                        $this->load->model("external_share_document", "external_share_documentfactory");
                        $this->external_share_document = $this->external_share_documentfactory->get_instance();
                        $external_user_token_data = $this->external_user_token->generate_external_user_token();
                        $this->external_share_document->set_field("token_id", $external_user_token_data["external_user_token_id"]);
                        $this->external_share_document->set_field("document_id", $this->input->post("doc_id"));
                        $this->external_share_document->set_field("share_type", $this->input->post("action"));
                        $this->external_share_document->set_field("external_user_email", $email);
                        $this->external_share_document->insert();
                        $url = base_url() . "external_actions/draft_collaborate_otp/" . $external_user_token_data["external_user_token_id"] . "/" . $external_user_token_data["external_user_token"];
                    } else {
                        $url = base_url() . "contracts/draft_collaborate/" . $module_id . "/" . $this->input->post("doc_id");
                    }
                    $view_data = ["user_profile_name" => $this->session->userdata("AUTH_userProfileName"), "action" => $this->input->post("action"), "url" => $url];
                    $content = $this->load->view("templates/email", ["content" => $this->load->view("notifications/share_doc_email_content", $view_data, true)], true);
                    $this->load->library("email_notifications");
                    $response["result"] = $this->email_notifications->send_email([$email], $subject, $content);
                }
            }
        } else {
            $this->load->model("email_notification_scheme");
            $data["users_emails"] = $this->email_notification_scheme->load_available_users_emails();
            $data["module_id"] = $this->input->get("module_id");
            $data["doc_id"] = $this->input->get("doc_id");
            $this->load->model("document_management_system", "document_management_systemfactory");
            $this->document_management_system = $this->document_management_systemfactory->get_instance();
            $this->document_management_system->fetch($data["doc_id"]);
            $data["doc_name"] = $this->document_management_system->get_field("name");
            $data["doc_extension"] = $this->document_management_system->get_field("extension");
            $data["actions"] = ["edit" => $this->lang->line("edit"), "view" => $this->lang->line("view")];
            $response["html"] = $this->load->view("documents_management_system/share_form", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function resend_approval_email()
    {
        $this->authenticate_exempted_actions();
        if (!$this->input->is_ajax_request()) {
            redirect("dashboard");
        }
        $this->load->model("approval", "approvalfactory");
        $this->approval = $this->approvalfactory->get_instance();
        $this->load->model("contract_approval_status", "contract_approval_statusfactory");
        $this->contract_approval_status = $this->contract_approval_statusfactory->get_instance();
        $this->contract_approval_status->fetch($this->input->post("approval_status_id"));
        $response = $this->approval->notify_next_approvers((int) $this->contract_approval_status->get_field("rank") - 1, $this->input->post("contract_id"));
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function move_contract_attachments_to_parent_folder($comment, $parent_folder_name, $contract_id, $newComment = NULL)
    {
        $ids = $this->get_comment_attachments($comment);
        if (0 < count($ids) && isset($newComment)) {
            $parent_id = $this->dmsnew->get_document_details(["id" => end($ids)])["parent"];
            $this->dmsnew->rename_document("contract", $parent_id, "folder", $parent_folder_name);
            $newIds = $this->get_comment_attachments($newComment);
            $ids = array_diff($newIds, $ids);
        } else {
            if (isset($newComment)) {
                $ids = $this->get_comment_attachments($newComment);
            }
        }
        if (0 < count($ids)) {
            $parent_folder = $this->dmsnew->create_note_parent_folder($contract_id, $parent_folder_name, "contract");
            $this->dmsnew->move_document_handler($parent_folder["id"], $ids, [], "contract");
        }
    }
    private function get_comment_attachments($comment)
    {
        $ids = [];
        preg_match_all("/[|]+\\d+/", $comment, $ids);
        $ids = $ids[0];
        for ($index = 0; $index < count($ids); $index++) {
            $id = $ids[$index];
            $id = ltrim($id, $id[0]);
            $ids[$index] = $id;
        }
        return $ids;
    }
    public function autocomplete_party()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->is_auth->is_logged_in()) {
            exit("login_needed");
        }
        $term = trim((string) $this->input->get("term"));
        $results = $this->contract_party->lookup_contact_company($term);
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($results));
    }

    /*
     * Milestones
     */
    public function milestone($contract_id = 0){
        $data = [];
        if($this->input->is_ajax_request()){
            $this->load->model("milestone", "milestonefactory");
            $this->milestone = $this->milestonefactory->get_instance();
            $data["milestones"] = $this->milestone->load_milestones_per_contract($contract_id);
            $this->contract->fetch($contract_id);
            $contract = $this->contract->get_fields();
            $data["visible_to_cp"] = (!strcmp($this->contract->get_field("channel"), $this->cp_channel) || ($this->contract->get_field("visible_to_cp") == "1"));
            $data["milestone_visible_to_cp"] = $this->contract->get_field("milestone_visible_to_cp");
            $data["model_code"] = $this->contract->get("modelCode");
            $data["contract"] = $contract;
            $data["contract_full_name"] = $contract["name"];
            $data["rtl"] = $this->is_auth->is_layout_rtl();
            $data["language"] = "en";// note that I hardcorded this. $this->session->userdata("AUTH_language_key");
//            $data["progress_statuses"] = [["value"=>"open","data-content"=>"<i class=\"fa-solid fa-hourglass-start fa-lg mr-2 ml-2\"></i>" . $this->lang->line("open_status")],["value"=>"in_progress","data-content"=>'<i class="fa-solid fa-spinner fa-lg mr-2 ml-2"></i>' . $this->lang->line("in_progress")],["value"=>"on_hold","data-content"=>'<i class="fa-solid fa-hand fa-lg mr-2 ml-2"></i>' . $this->lang->line("on_hold")],["value"=>"completed","data-content"=>'<i class="fa-solid fa-circle-check fa-lg mr-2 ml-2"></i>' . $this->lang->line("completed")],["value"=>"cancelled","data-content"=>'<i class="fa-solid fa-ban fa-lg mr-2 ml-2"></i>' . $this->lang->line("cancelled")]];
//            $data["financial_statuses"] = [["value"=>null,"data-content"=>$this->lang->line("select_financial_status")],["value"=>"paid","data-content"=>$this->lang->line("paid")],["value"=>"partially_paid","data-content"=>'<i class="fa-solid fa-percent mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("partially_paid")],["value"=>"not_paid","data-content"=>'<i class="fa-brands fa-creative-commons-nc mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("not_paid")],["value"=>"non_billable","data-content"=>'<i class="fa-solid fa-file-invoice-dollar mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("non_billable")],["value"=>"not_applicable","data-content"=>'<i class="fa-solid fa-xmark mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("not_applicable")],["value"=>"cancelled","data-content"=>'<i class="fa-solid fa-ban mr-3 cancelled fa-lg ml-2"></i>' . $this->lang->line("cancelled")]];

            $data["progress_statuses"] = [
                [
                    "value" => "open",
                    "data-content" => "<i class='fa-solid fa-hourglass-start fa-lg mr-2 ml-2'></i>Open"
                ],
                [
                    "value" => "in_progress",
                    "data-content" => "<i class='fa-solid fa-spinner fa-lg mr-2 ml-2'></i>In Progress"
                ],
                [
                    "value" => "on_hold",
                    "data-content" => "<i class='fa-solid fa-hand fa-lg mr-2 ml-2'></i>On Hold"
                ],
                [
                    "value" => "completed",
                    "data-content" => "<i class='fa-solid fa-circle-check fa-lg mr-2 ml-2'></i>Completed"
                ],
                [
                    "value" => "cancelled",
                    "data-content" => "<i class='fa-solid fa-ban fa-lg mr-2 ml-2'></i>Cancelled"
                ]
            ];
            $data["financial_statuses"] = [
                [
                    "value" => null,
                    "data-content" => "Financial Status"
                ],
                [
                    "value" => "paid",
                    "data-content" => "<i class='fa-solid fa-hand-holding-dollar mr-3 cancelled fa-lg ml-2'></i>Paid"
                ],
                [
                    "value" => "partially_paid",
                    "data-content" => "<i class='fa-solid fa-percent mr-3 cancelled fa-lg ml-2'></i>Partially Paid"
                ],
                [
                    "value" => "not_paid",
                    "data-content" => "<i class='fa-brands fa-creative-commons-nc mr-3 cancelled fa-lg ml-2'></i>Not Paid"
                ],
                [
                    "value" => "non_billable",
                    "data-content" => "<i class='fa-solid fa-file-invoice-dollar mr-3 cancelled fa-lg ml-2'></i>Non-billable"
                ],
                [
                    "value" => "not_applicable",
                    "data-content" => "<i class='fa-solid fa-xmark mr-3 cancelled fa-lg ml-2'></i>Not Applicable"
                ],
                [
                    "value" => "cancelled",
                    "data-content" => "<i class='fa-solid fa-ban mr-3 cancelled fa-lg ml-2'></i>Cancelled"
                ]
            ];
            $response["data"] = $data;
            $response["html"] = $this->load->view("contracts/view/milestones", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /*
     *
     */

    public function add_milestone(){
        $response = $this->milestone_per_contract();
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_milestone(){
        $response = $this->milestone_per_contract("edit");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    /*
     * Milestones per contract function
     */

    private function milestone_per_contract($action = "add") {
        $response = ["result" => false, "html" => ""]; // Initialize response

        if ($this->input->get(null, true)) {
            $milestone_id = $this->input->get("milestone_id");
            $contract_id = $this->input->get("contract_id");
            $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
            $data["notify_me_before_types"] = array_combine($this->reminder->get("notify_me_before_type"), [$this->lang->line("reminder_popup"), $this->lang->line("reminder_popup_and_email")]);
            $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => [""=>$this->lang->line("none")]]);
            $this->contract->fetch($contract_id);
            $contract = $this->contract->get_fields();
            $data["contract"]["id"] = $contract_id;
            $data["milestone_id"] = ($milestone_id ? $milestone_id : 0);

            if ($action == "edit") {
                $data["milestone_data"] = $this->load_milestone_data($milestone_id);
                $data["target"] = (($data["milestone_data"]["percentage"] == null) ? "amount" : "percentage");
                $data["page_title"] = $this->lang->line("edit_milestone");
                $data["notify_before"] = $this->reminder->load_notify_before_data_to_related_object($milestone_id, $this->milestone->get("_table"));
                !isset($data["notify_before"]["time"]);///check this---Atinga
                $data["reminder_interval_date"] = $this->system_preferences["reminderIntervalDate"] ?? ''; // Use null coalescing operator
                $response["notification_available"] = ($data["notify_before"] ? true : false);
            } else {
                $data["reminder_interval_date"] = $this->system_preferences["reminderIntervalDate"] ?? ''; // Use null coalescing operator
                $data["target"] = "amount";
                $data["milestone_data"]["currency_id"] = ($contract["currency_id"] ? $contract["currency_id"] : null);
                $data["page_title"] = $this->lang->line("add_milestone");
            }
            $response["result"] = true;
            $response["html"] = $this->load->view("contracts/view/milestone_form", $data, true);
        } elseif ($this->input->post(null, true) && $this->contract->fetch($this->input->post("contract_id", true))) {
            $response = $this->save_milestone();
        }

        return $response;
    }

    private function save_milestone()
    {

        $post_data = $this->input->post(null, true);
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $action = $this->milestone->fetch($post_data["id"]) ? "edit" : "add";
        $response["result"] = false;

        if ($action == "add") {
            $post_data["status"] = "open";
        } else {
            $this->milestone->fetch($post_data["id"]);
            $post_data["status"] = $this->milestone->get_field("status");
        }

        $this->milestone->set_fields($post_data);

        if ($this->milestone->validate()) {
            $contract_value=$this->contract->get_field("value");
            $contract_currency=$this->contract->get_field("currency_id");
           $contract_value_currency_isValid= (isset($contract_value, $contract_currency) && is_numeric($contract_value) && is_numeric($contract_currency) && $contract_value > 0 && $contract_currency > 0);

            $notify_before = $this->input->post("notify_me_before");
            $due_date = $this->input->post("due_date");
            $is_not_nb = (isset($notify_before["time"]) && !is_numeric($notify_before["time"]));

            if ($notify_before && ($due_date == null)) {
                $response["validationErrors"]["due_date"] = $this->lang->line("cannot_be_blank_rule");
            }
            elseif ($notify_before && ((!$notify_before["time"] || !$notify_before["time_type"] || !$notify_before["type"]) || $is_not_nb))
            {
                if ($is_not_nb) {
                    $response["validationErrors"]["notify_before"] = sprintf($this->lang->line("is_numeric_rule"), $this->lang->line("notify_before"));
                } else {
                    $response["validationErrors"]["notify_before"] = $this->lang->line("cannot_be_blank_rule");
                }
            }
            //check contract value is valid

            if ($contract_value_currency_isValid){
                if (isset($post_data["target"]) && $post_data["target"] == "on") {
                    $perc=(double)$post_data["percentage"];
                    if(isset($post_data["percentage"]) && $perc<=100 and $perc>0) {
                        $post_data["amount"]=$contract_value*$perc/100;
                        $post_data["currency_id"]=$contract_currency;
                    } else{
                        $response["validationErrors"]["percentage"]=$this->lang->line("percentage_value_incorrect");
                    }//end checking percentage
                } else {
                    $post_data["percentage"] = "";
                }
            }
            else{
                $response["validationErrors"]["percentage"]=$this->lang->line("contract_value_or_currency_not_set");
            }
            if (!isset($response["validationErrors"])) {               

                $this->milestone->set_field("channel", "A4L");
                $this->milestone->set_fields($post_data);
                $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
                $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
          
                if (($action == "add") ? $this->milestone->insert() : $this->milestone->update()) {
                    $milestone_id = $this->milestone->get_field("id");
                    if (isset($milestone_title) && ($milestone_title != $post_data["title"])) {
                        $ids = $this->contract_milestone_document->load_attachments_per_contract($milestone_id, $post_data["contract_id"]);
                        if (0 < count($ids)) {
                            $result = end($ids);
                            $parent_id = $this->dmsnew->get_document_details(["id" => $result, "module" => "contract", "module_record_id" => $post_data["contract_id"]])["parent"];
                            $this->dmsnew->rename_document("contract", $parent_id, "folder", $post_data["title"]);
                        }
                    }
                    $this->notify_me_before_due_date($milestone_id, $post_data["contract_id"]);
                    $this->load->library("dmsnew");
                    $failed_uploads_count = 0;
                    foreach ($_FILES as $file_key => $file) {
                        if ($file["error"] != 4) {
                            $upload_response = $this->dmsnew->upload_file(["module" => "contract", "module_record_id" => $post_data["contract_id"], "createdByChannel" => "A4L", "upload_key" => $file_key]);
                            if ($upload_response["status"]) {
                                $this->contract_milestone_document->set_field("document_id", $upload_response["file"]["id"]);
                                $this->contract_milestone_document->set_field("milestone_id", $milestone_id);
                                $this->contract_milestone_document->insert();
                                $this->contract_milestone_document->reset_fields();
                                $this->move_milestone_attachments_to_parent_folder($milestone_id, $post_data["contract_id"], $upload_response["file"]["id"], $post_data["title"]);
                            }
                            if (!$upload_response["status"]) {
                                $failed_uploads_count++;
                                continue;
                            }
                        }
                    }
                    if (0 < $failed_uploads_count) {
                        $response["validationErrors"]["files"] = sprintf($this->lang->line("files_were_not_uploaded"), $failed_uploads_count);
                        $this->milestone->delete(["where" => ["id", $milestone_id]]);
                    } else {
                        $response["result"] = true;
                    }
                }
            }
        } else {
            $response["result"] = false;
            $response["validationErrors"] = $this->milestone->get("validationErrors");
        }
        return $response;
    }

    public function milestone_documents($milestone_id){
        $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
        $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
        $data = [];
        $response["status"] = true;
        $data["milestone_id"] = $milestone_id;
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $this->milestone->fetch($milestone_id);
        $data["contract_id"] = $this->milestone->get_field("contract_id");
        $data["documents"] = $this->contract_milestone_document->load_all_attachments($milestone_id);
        $response["html"] = $this->load->view("contracts/view/milestone_documents", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_document_milestone(){
        $response["status"] = false;
        $this->load->model("contract_milestone_document", "contract_milestone_documentfactory");
        $this->contract_milestone_document = $this->contract_milestone_documentfactory->get_instance();
        if($parent_id = $this->contract_milestone_document->delete_document($this->input->post("document_id"))){
            $this->delete_document();
            if(!$this->document_management_system->fetch(["parent"=>$parent_id])){
                $this->dmsnew->delete_document("contract", $parent_id, ($this->input->post("newest_version") == "true"));
            }
            $response["status"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_milestone(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $response["status"] = $this->milestone->delete_milestone_documents($this->input->post("milestone_id"));
        $this->reminder->dismiss_related_reminders_by_related_object_ids($this->input->post("milestone_id"), "related_id");
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_milestone_data($milestone_id = 0){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $this->milestone->load_milestone_data($milestone_id);
        return $this->milestone->load_milestone_data($milestone_id);
    }
    private function notify_me_before_due_date($milestone_id, $contract_id){
        $notify_before = $this->input->post("notify_me_before");
        $due_date = $this->input->post("due_date");
        $title = $this->input->post("title");
        $current_reminder = $this->reminder->load_notify_before_data_to_related_object($milestone_id, $this->milestone->get("_table"));
        if(($current_reminder && !$notify_before)){
            return $this->reminder->remind_before_due_date([], $current_reminder["id"]);
        }
        if(($notify_before && $due_date)){
            $reminder = ["user_id"=>$this->is_auth->get_user_id(),"remindDate"=>$due_date,"contract_id"=>$contract_id,"related_id"=>$milestone_id,"related_object"=>$this->milestone->get("_table"),"notify_before_time"=>$notify_before["time"],"notify_before_time_type"=>$notify_before["time_type"],"notify_before_type"=>$notify_before["type"]];
            $reminder["summary"] = sprintf($this->lang->line("notify_me_before_message"), $this->lang->line("milestone"), $title . " " . $this->lang->line("related_to") . " " . $this->contract->get("modelCode") . $contract_id, $due_date);
            return $this->reminder->remind_before_due_date($reminder, (isset($current_reminder["id"]) ? $current_reminder["id"] : null));
        }
        return false;
    }
    public function change_milestone_status(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $milestone_id = $this->input->post("milestone_id");
        $status = $this->input->post("status");
        $response = $this->milestone->change_status($milestone_id, $status);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function change_milestone_financial_status(){
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $milestone_id = $this->input->post("milestone_id");
        $status = $this->input->post("financial_status");
        $response = $this->milestone->change_financial_status($milestone_id, $status);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function preview_document($id = 0){
        $response = [];
        if((0 < $id)){
            echo $this->dmsnew->get_preview_document_content($id);
            exit;
        }else{
            $id = $this->input->post("id");
            if(!empty($id)){
                $response["document"] = $this->dmsnew->get_document_details(["id"=>$id]);
                $response["document"]["url"] = app_url("modules/contract/contracts/preview_document/" . $id);
            }
            $response["html"] = $this->load->view("documents_management_system/view_document", ["mode"=>"preview"], true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    public function show_hide_milestone_cp(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $this->load->model("milestone", "milestonefactory");
        $this->milestone = $this->milestonefactory->get_instance();
        $post_data = $this->input->post(null);
        $response["result"] = false;
        if($this->contract->fetch($post_data["id"])){
            $this->contract->set_field("milestone_visible_to_cp", $post_data["flag"]);
            $milestone_parent_folder_array = $this->dmsnew->get_document_details(["name"=>"Milestones_attachments","module"=>"contract","module_record_id"=>$post_data["id"]]);
            if(is_array($milestone_parent_folder_array)){
                $milestone_parent_folder = $milestone_parent_folder_array["id"];
                $this->dmsnew->update_show_hide_in_cp($milestone_parent_folder, $post_data["flag"]);
            }
            if(!$this->contract->update()){
                $response["display_message"] = $this->lang->line("updates_failed");
            }else{
                $response["result"] = true;
            }
        }else{
            $response["display_message"] = $this->lang->line("invalid_record");
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_javascript_libraries(){
        $this->includes("jquery/tinymce/tinymce.min", "js");
        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
        $this->includes("jstree/jstree.min", "js");
        $this->includes("jstree/themes/default/style.min", "css");
        $this->includes("jquery/jquery.shiftcheckbox", "js");
        $this->includes("contract/view", "js");
        $this->includes("syncfusion/ej2-base/material", "css");
        $this->includes("syncfusion/ej2-buttons/material", "css");
        $this->includes("syncfusion/ej2-js-es5/material", "css");
        $this->includes("syncfusion/ej2-popups/material", "css");
        $this->includes("syncfusion/ej2-filemanager/material", "css");
        $this->includes("syncfusion/ej2-navigations/material", "css");
        $this->includes("syncfusion/ej2-lists/material", "css");
        $this->includes("syncfusion/ej2-dropdowns/material", "css");
        $this->includes("syncfusion/ej2-inputs/material", "css");
        $this->includes("syncfusion/ej2-splitbuttons/material", "css");
        $this->includes("syncfusion/ej2-calendars/material", "css");
        $this->includes("syncfusion/ej2-layouts/material", "css");
        $this->includes("syncfusion/ej2-richtexteditor/material", "css");
        $this->includes("syncfusion/ej2-grids/material", "css");
        $this->includes("syncfusion/ej2-treegrid/material", "css");
        $this->includes("syncfusion/ej2-gantt/material", "css");
        $this->includes("syncfusion/ej2-inplace-editor/material", "css");
        $this->includes("syncfusion/ej2-base/ej2-base.min", "js");
        $this->includes("syncfusion/ej2-layouts/ej2-layouts.min", "js");
        $this->includes("syncfusion/ej2-popups/ej2-popups.min", "js");
        $this->includes("syncfusion/ej2-data/ej2-data.min", "js");
        $this->includes("syncfusion/ej2-inputs/ej2-inputs.min", "js");
        $this->includes("syncfusion/ej2-lists/ej2-lists.min", "js");
        $this->includes("syncfusion/ej2-buttons/ej2-buttons.min", "js");
        $this->includes("syncfusion/ej2-splitbuttons/ej2-splitbuttons.min", "js");
        $this->includes("syncfusion/ej2-navigations/ej2-navigations.min", "js");
        $this->includes("syncfusion/ej2-grids/ej2-grids.min", "js");
        $this->includes("syncfusion/ej2-filemanager/ej2-filemanager.min", "js");
        $this->includes("syncfusion/ej2-dropdowns/ej2-dropdowns.min", "js");
        $this->includes("syncfusion/ej2-calendars/ej2-calendars.min", "js");
        $this->includes("syncfusion/ej2-treegrid/ej2-treegrid.min", "js");
        $this->includes("syncfusion/ej2-excel-export/ej2-excel-export.min", "js");
        $this->includes("syncfusion/ej2-file-utils/ej2-file-utils.min", "js");
        $this->includes("syncfusion/ej2-compression/ej2-compression.min", "js");
        $this->includes("syncfusion/ej2-pdf-export/ej2-pdf-export.min", "js");
        $this->includes("syncfusion/ej2-richtexteditor/ej2-richtexteditor.min", "js");
        $this->includes("syncfusion/ej2-gantt/ej2-gantt.min", "js");
        $this->includes("syncfusion/ej2-inplace-editor/ej2-inplace-editor.min", "js");
        $this->includes("contract/show_hide_customer_portal", "js");
        $this->includes("contract/cp_contract_common", "js");
        $this->includes("jquery/dropzone_v6/dropzone.min", "js");
        $this->includes("jquery/dropzone_v6/dropzone", "css");
        $this->includes("contract/related_documents", "js");
        $this->includes("contract/related_tasks", "js");

        $this->includes("contract/related_reminders", "js");
        $this->includes("contract/related_cases", "js");
        $this->includes("contract/related_contracts", "js");
       // $this->includes("contract/contract_time_logs", "js");
        //$this->includes("contract/expenses", "js");
       // $this->includes("contract/contract_settings", "js");
        $this->includes("contract/related_legalOpinions","js");
        $this->includes("jquery/timemask", "js");
        $this->includes("contract/documents_integration", "js");
    }
    private function move_milestone_attachments_to_parent_folder($milestone_id, $contract_id, $uploaded_document_id, $milestone_title){
        $ids = [];
        $ids = $this->contract_milestone_document->load_attachments_per_contract($milestone_id, $contract_id);
        $folder_name = $milestone_title;
        $parent_id = 0;
        if((0 < count($ids))){
            $result = end($ids);
            $parent_id = $this->dmsnew->get_document_details(["id"=>$result,"module"=>"contract","module_record_id"=>$contract_id])["parent"];
            if($this->document_management_system->fetch($parent_id)){
                $folder_name = $this->document_management_system->get_field("name");
            }
        }
        $new_id[] = $uploaded_document_id;
        $parent_folder = $this->dmsnew->create_parent_folder($contract_id, $parent_id, $folder_name, "contract", "milestone");
        $this->document_management_system->fetch($parent_folder["id"]);
        $parent_visible_in_cp = $this->document_management_system->get_field("visible_in_cp");
        if($parent_visible_in_cp){
            foreach($ids as $key => $id){
                $this->document_management_system->fetch($id);
                $this->document_management_system->set_field("visible_in_cp", 1);
                $this->document_management_system->update();
            }
        }
        $return = $this->dmsnew->move_document_handler($parent_folder["id"], $new_id, [], "contract");
    }
    public function related_sureties($contract_id = 0)
    {
        // Ensure the request is an AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard"); // Redirect if not AJAX
        }

        $response = []; // Initialize response array

        // Load the surety_bond model using a factory pattern
        $this->load->model("surety_bond", "surety_bondfactory");
        $this->surety_bond = $this->surety_bondfactory->get_instance();


        if ($this->input->post(NULL)) {
            $mode = $this->input->post("mode");
            $post_data = $this->input->post(NULL); // Get all POST data

            if (isset($mode)) {

                $suretyId = $this->input->post('id');

                $this->surety_bond->set_fields($post_data);
                if ($this->surety_bond->validate() && $this->surety_bond->validateDates($post_data)) {
                    $result = false; // Initialize result to false

                    if ($mode == "edit" && $suretyId > 0) {
                        if ($this->surety_bond->update(["where" => ["id", $suretyId]])) {
                            $result = true;
                        } else {
                            // Update failed, get validation errors if any specific to update operation
                            $response["validation_errors"] = $this->surety_bond->get("validationErrors");
                        }
                    } else {
                        $result = $this->surety_bond->insert();
                    }

                    if ($result) {
                        $this->contract->fetch($post_data['contract_id']);//load the contract in question
                        $effective_date=$post_data['effective_date'];
                        $expiry_date=$post_data['expiry_date'];
                        $this->contract->set_field("perf_security_commencement_date",$effective_date);
                        $this->contract->set_field("perf_security_expiry_date",$expiry_date);
                        $this->contract->update();
                        $this->surety_bond->reset_fields(); // Clear model fields after successful operation
                        $response["status"] = "success";
                        $response["message"] = "Saved Successfully";
                    } else {
                        $response["status"] = "error";
                        $response["message"] = "Error in saving";
                    }
                } else {
                    // Validation failed, return validation errors
                    $response["status"] = "error";
                    $response["validation_errors"] = $this->surety_bond->get("validationErrors");
                }
            } else {
                // Handle cases where 'mode' is not set in POST data (e.g., just filtering/sorting)
                $filter = $this->input->post("filter");
                $sortable = $this->input->post("sort");
                $data = $this->surety_bond->load_all_surety_bonds_by_contract($contract_id, $sortable);
                $response["html"] = $this->load->view("sureties/securities", $data, true);
            }

        } else {
            // Handle GET requests for loading the form or list
            $data = [];
            $data["contract_id"] = $contract_id;
            // Load currencies for dropdown
            $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => ["" => $this->lang->line("none")]]);
            $data["hide_show_notification"] = true;
            $data["archivedValues"] = array_combine($this->surety_bond->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";

            $suretyId = $this->input->get("suretyId");
            $mode = $this->input->get("mode");
            $data["mode"] = $mode;

            if (isset($mode)) {
                if ($mode == 'edit' && $suretyId > 0) {
                    // In edit mode, fetch the existing record to populate the form
                    if ($this->surety_bond->fetch($suretyId)) {
                        $data['bond'] = $this->surety_bond->get_fields();
                        $data["contract_id"] = $contract_id; // Ensure contract_id is passed

                        $response["html"] = $this->load->view("sureties/form", $data, true);
                    } else {
                        $this->set_flashmessage("error", $this->lang->line("invalid_record"));

                    }
                } elseif ($mode == 'loadForm') {
                    // Load an empty form for adding a new record
                    $response["html"] = $this->load->view("sureties/form", $data, true);
                }
            } else {
                // Default: load the list of surety bonds
                $data['surety_bonds'] = $this->surety_bond->load_all_surety_bonds_by_contract($contract_id)['data'];
                $response["html"] = $this->load->view("sureties/related_sureties", $data, true);
            }
        }
        // Set content type and output JSON response
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    // In contracts.php
    private function create_contract_task($contract) {
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();

        $assignee_id = $contract->get_field("assignee_id") ?: $this->is_auth->get_user_id();

        // Set task data using the contract object
        $task_data = [
            'contract_id' => $contract->get_field("id"),
            'title' => 'Review Contract: ' . $contract->get_field("name"),
            'description' => 'Please review the newly created contract : ' . $contract->get_field("name")." and start the process of drafting",
            'assigned_to' => $assignee_id,
            'due_date' => date('Y-m-d', strtotime('+7 days')),
            'priority' => 'medium',
            'archived' => "no",
            'workflow' => 1,
            'task_status_id' => 1, // "Open" status
            'task_type_id' => 1, // Appropriate task type ID
            'user_id' => $this->is_auth->get_user_id(),
            'reporter' => $this->is_auth->get_user_id(),
            'notify_me_before' => [
                'time' => 1,
                'time_type' => 'day_or_days',
                'type' => 'reminder_popup_and_email'
            ]
        ];

        $this->task->set_fields($task_data);

        if ($this->task->validate()) {
            $task_id = $this->task->insert();

            if ($task_id) {
                $this->send_task_notification_email($task_id, $contract);
                return $task_id;
            }
        }else   //echo json_encode($this->task->get("validationErrors"));

        return false;
    }
    private function send_task_notification_email($task_id, $contract) {
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $this->load->library("email_notifications");

        // Get task details
        $this->task->fetch($task_id);
        $task = $this->task->get_fields();

        // Get assignee details
        $assignee = $this->user->load($task['assigned_to']);

        // Prepare email data
        $subject = 'New Task Assigned: Review Contract ' . $contract->get_field("name");

        $view_data = [
            'task_title' => $task['title'],
            'task_description' => $task['description'],
            'due_date' => $task['due_date'],
            'priority' => ucfirst($task['priority']),
            'contract_name' => $contract->get_field("name"),
            'contract_id' => $contract->get_field("id"),
            'contract_code' => $contract->get("modelCode"),
            'assigner_name' => $this->is_auth->get_fullname(),
            'task_url' => base_url() . "tasks/view/" . $task_id
        ];

        $content = $this->load->view("templates/email", [
            'content' => $this->load->view("notifications/new_contract_task_email", $view_data, true)
        ], true);

        // Send email
        return $this->email_notifications->send_email(
            [$assignee['email']],
            $subject,
            $content
        );
    }
 public function test0($view){
        $data=[];
        $this->load->view("partial/header");
        $this->load->view("contracts/tests/".$view, $data);
        $this->load->view("partial/footer");
    }
    public function transitions($view){
        $data=[];

        $this->includes("tests/style", "css");
        $this->includes("tests/script", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/transitions/".$view, $data);
        $this->load->view("partial/footer");
    }
    public function contract_development($contract_id=0)
    { 
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if(!$contract_id && $contract_id<=0){
            return ;
        }
       $data=[];
       $data=$this->get_contract_workflow_data($contract_id);
        $this->includes("tests/contracts", "js");
        $this->includes("tests/contract_development", "css");
        $this->includes("contract/cp_contract_common", "js");
        $this->load->view("partial/header");
        $this->load->view("contracts/development/contract_detail_view",$data);
        $this->load->view("partial/footer");

    }
    public function test($contract_id) {
        // if (!$this->input->is_ajax_request()) {
        //     show_404();
        // }
      
            $data["success"]=true;
              $data["data"]=$this->get_contract_workflow_data($contract_id);
               
        $this->output->set_content_type("application/json")->set_output(json_encode($data));
    }

    ///
     public function get_contract_workflow_data($contract_id)
    {
             $contract=$this->load_contract_data($contract_id);//
             $workflow_id =(int) $contract['contract']['workflow_id']; 
             $currest_workflow_step= (int) $contract['contract']['status_id'];
               $this->load->model("language");
                       $language_id = $this->language->get_id_by_session_lang();

    if (!$workflow_id) {
        return [
            'success' => false,
            'message' => 'No workflow associated with this contract.'
        ];
    }
      $this->load->model('contract_workflow_status_relation','contract_workflow_status_relationfactory');
    $this->contract_workflow_status_relation=$this->contract_workflow_status_relationfactory->get_instance();

    $this->load->model('contract_status_language','contract_status_languagefactory');
    $this->contract_status_language=$this->contract_status_languagefactory->get_instance();
    $this->load->model('contract_workflow_step_function','contract_workflow_step_functionfactory');
    $this->contract_workflow_step_function=$this->contract_workflow_step_functionfactory->get_instance();
    $this->load->model('contract_workflow_step_checklist','contract_workflow_step_checklistfactory');
    $this->contract_workflow_step_checklist=$this->contract_workflow_step_checklistfactory->get_instance();
    $this->load->model('contract_workflow_steps_log','contract_workflow_steps_logfactory');
    $this->contract_workflow_steps_log=$this->contract_workflow_steps_logfactory->get_instance();
    $this->load->model('contract_workflow_status_transition','contract_workflow_status_transitionfactory');
    $this->contract_workflow_status_transition=$this->contract_workflow_status_transitionfactory->get_instance();
    

 //  $steps=$this->contract_workflow_status_relation->get_progressed_steps_in_workflow($workflow_id,$contract_id);
   $steps=$this->contract_workflow_status_relation->get_all_steps_in_workflow($workflow_id,$contract_id);

    if (empty($steps)) {
        return [
            'success' => false,
            'message' => 'No steps found for the associated workflow.'
        ];
    }
  
    $workflow_steps = [];
    $counter = 1;

    foreach ($steps as $step) { 
        $step_id =(int) $step['step_id'];//the status id from contract_status table
         $step_id_frmlangs =(int) $step['id']; // from contract_status_language table

        // 2. Get actions/functions for the step
        $functions = $this->contract_workflow_step_function->load_all(["where" => ['step_id' , $step_id_frmlangs]]);
      
        $actions = [];
        foreach ($functions as $func) {
            $actions[] = [
                'action' => $func['label'],
                 'function_name' => $func['function_name'],
                'icon'   => "fa " . $func['icon_class']
            ];
        }
        // 2. Get actions/functions for the step
$functions = $this->contract_workflow_step_function->load_all([
    "where" => ['step_id', $step_id_frmlangs]
]);

// Default actions (always included)
$defaultActions = [
     [
        'action'        => 'Add Task',
        'function_name' => 'addTask',
        'icon'          => 'fa fa-tasks'
    ],
    [
        'action'        => 'Add Reminder',
        'function_name' => 'addReminder',
        'icon'          => 'fa fa-bell'
    ],
    [
        'action'        => 'Notify',
        'function_name' => 'notify',
        'icon'          => 'fa fa-envelope'
    ],
    [
        'action'        => 'Cancel',
        'function_name' => 'cancel',
        'icon'          => 'fa fa-times-circle'
    ],
       [
        'divider'        => true
    ]
];
$actions = [];

// Actions coming from DB
foreach ($functions as $func) {
    $actions[] = [
        'action'        => $func['label'],
        'function_name' => $func['function_name'],
        'icon'          => "fa " . $func['icon_class']
    ];
}



// Merge DB + default actions
$actions = array_merge($actions, $defaultActions);
        // 3. Get checklist for the step
        $checklist =$this->contract_workflow_step_checklist->load_all(["where" => ['step_id' , $step_id]]);

        // 4. Get status from Contract_workflow_steps_log
        //   $where[] = ["contract_id", $contract_id];
        //   $where[] = ["step_id", $step_id];
                            
     //   $log = $this->contract_workflow_steps_log->load_all(["where" => $where]); 

        
       
        // 5. Get transitions → main_actions
        $transitions = $this->contract_workflow_status_transition->load_all(["where" => ['from_step' , $step_id, 'workflow_id', $workflow_id]]);
                       
        $main_actions = [];
        foreach ($transitions as $t) {
          
            $main_actions[] = [
                'label'   => $t['name'],
                'comment'   => $t['comment'],
                'class'   => $t['id'] == 1 ?  'btn-secondary':'btn-primary', // Example: primary for first transition
                'onclick' => "moveStatus({$contract_id},{$step_id}, {$t['to_step']},event)",//moveStatus(contractId, statusId, transitionId, e)
                'icon'    => !empty($t['icon_class']) ? "fa " . $t['icon_class'] :""
            ];
        }
        // 6. Build step structure
        $workflow_steps[] = [
            'step_id'       => (int)$step_id,
            'title'         => $counter . '. ' . $step['step_name'], // from contract_status_language
            'status'        =>  $step['step_status'] ?? 'pending', // Default to 'pending' if not set
            'step_icon'        =>  $step['step_icon'] ?? "fa fa-file", // Default icon
            'responsibility'=> $step['responsible_user_roles'] ?? null,
            'activity'      => $step['activity'] ?? null,
            'output'        => $step['step_output'] ?? null,
            'description'   => $step['description'] ?? null,
            'document_link' => "document_link", // placeholder
            'actions'       => $actions,
            'checklist'     => $checklist,
            'main_actions'  => $main_actions
            
        ];

        $counter++;
    }

    // 7. Final response
 $docs = $this->dmsnew->load_documents(["module" => "contract", "module_record_id" =>$contract_id, "lineage" => "", "term" =>""]);
       
 
    $object1 = [   
    'workflow_steps'   => $workflow_steps,   
    'last_updated'     => date('F d, Y H:i:s'),
    'system_status'    => 'OK',
    'attachments'      => $docs["data"]??[]  // you’ll handle
];

return array_merge($object1, $contract);

}


}