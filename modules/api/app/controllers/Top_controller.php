<?php

require "Ci_top_controller.php";
class Top_controller extends CI_Top_controller
{
    public $excempted_uri;
    public $permissions_map;
    public $system_preferences;
    public $user_logged_in_data;
    protected $apikey;
    protected $result;
    protected $response;
    const TERMS_MIN_NUMBER = 3;
    public function __construct()
    {
        parent::__construct();
        $this->load->library("api");
        $this->excempted_uri = ["/users/check_api/", "/auth_users/change_language/", "/auth_users/change_money_language/", "/auth_users/autocomplete/", "/auth_users/change_entity/", "/auth_users/load_country_list/", "/auth_users/change_password/", "/opponents/autocomplete/", "/clients/autocomplete/", "/cases/get_due_in_value/", "/contracts/autocomplete/", "/clients/select/", "/cases/autocomplete/", "/cases/ip_autocomplete/", "/tasks/autocomplete/", "/tasks/list_my_tasks/", "/tasks/list_tasks_reported_by_me/", "/task_locations/autocomplete/", "/contacts/autocomplete/", "/companies/autocomplete/", "/suppliers/autocomplete/", "/reminders/list_my_reminders/", "/reminders/get_reminders_count/", "/clients/accounts/", "/administration/checkCustomerPortalFeature/", "/administration/licenceAvailability/", "/administration/getOutlookValue/", "/expenses/list_expenses/", "/expenses/list_my_expenses/", "/time_tracking/my_time_logs/", "/time_tracking/my_time_entries/", "/time_tracking/all_time_logs/", "/calendars/list_my_meetings/", "/hearings/autocomplete/", "/events/autocomplete/", "/accounts/get_account_details/", "/hearings/load_stages_data/", "/hearings/delete_document/", "/cases/add_screen_transition/", "/tasks/add_screen_transition/", "/tasks/upload_file/", "/tasks/download_file/", "/tasks/return_doc_thumbnail/", "/tasks/return_assignment_rules/", "/cases/return_assignment_rules/", "/tasks/load_stage_data/", "/contacts/manage_money_accounts/", "/hearings/load_data/", "/hearings/load_court_fields/", "/hearings/load_case_hearing_data/", "/auth_users/get_available_languages/", "/dashboards/list_user_records/", "/dashboards/count_objects_records/", "/dashboards/get_recently_visited/", "/users/user_info/", "/user_integrations/get/", "/user_integrations/set/", "/user_integrations/revoke_access/", "/module_preferences/set/", "/module_preferences/get/", "/module_preferences/delete/", "/documents/update_lock_status/", "/tasks/tasks_contributed_by_me/", "/vouchers/bill_add/","/vouchers/get_bill"];
        $this->load->config('permissions_map');
        $this->permissions_map = $this->config->item('permissions_map');
        //$this->permissions_map = ["/cases/add_litigation/" => "/cases/add_litigation/", "/cases/add_legal_matter/" => "/cases/add_legal_matter/", "/cases/note_add/" => "/cases/add_comment/", "/cases/email_note_add/" => "/cases/add_comment/", "/cases/note_edit/" => "/cases/edit_comment/", "/cases/note_list/" => "/cases/get_all_comments/", "/cases/note_list_only/" => "/cases/get_all_core_and_cp_comments/", "/cases/email_note_list/" => "/cases/get_all_email_comments/", "/cases/note_delete/" => "/cases/delete_comment/", "/cases/email_note_delete/" => "/cases/delete_email_comment/", "/cases/legal_matter/" => "/cases/legal_matter/", "/cases/litigation_case/" => "/cases/litigation_case/", "/cases/intellectual_property/" => "/cases/intellectual_property/", "/cases/edit/" => "/cases/edit/", "/cases/edit_single/" => "/cases/edit/", "/cases/attachment_add/" => "/cases/upload_file/", "/hearings/list_hearings/" => "/cases/list_hearings/", "/companies/add/" => "/companies/add/", "/companies/edit/" => "/companies/tab_company/", "/companies/list_companies/" => "/companies/index/", "/companies/check_if_exists/" => "/companies/idex/", "/companies/note_add/" => "/companies/note_add/", "/companies/note_edit/" => "/companies/edit_note/", "/companies/note_list/" => "/companies/get_company_notes/", "/companies/note_delete/" => "/companies/delete_note/", "/contacts/add/" => "/contacts/add/", "/contacts/bulk_add/" => "/contacts/add/", "/contacts/edit/" => "/contacts/edit/", "/contacts/delete/" => "/contacts/delete/", "/contacts/list_contacts/" => "/contacts/index/", "/contacts/check_if_exists/" => "/contacts/index/", "/tasks/add/" => "/tasks/add/", "/tasks/edit/" => "/tasks/edit/", "/tasks/load_data/" => "/tasks/add/", "/task_locations/add/" => "/task_locations/add/", "/hearings/add/" => "/cases/hearings/", "/hearings/edit/" => "/cases/hearings/", "/reminders/add/" => "/reminders/add/", "/reminders/edit/" => "/reminders/edit/", "/reminders/delete/" => "/reminders/delete/", "/reminders/dismiss/" => "/reminders/dismiss/", "/reminders/postpone/" => "/reminders/postpone/", "/expenses/add/" => "/money/vouchers/expense_add/", "/expenses/edit/" => "/money/vouchers/expense_edit/", "/expenses/move_expense_status_to_open/" => "/money/vouchers/move_expense_status_to_open/", "/expenses/move_expense_status_to_approved/" => "/money/vouchers/move_expense_status_to_approved/", "/expenses/move_expense_status_to_needs_revision/" => "/money/vouchers/move_expense_status_to_needs_revision/", "/expenses/move_expense_status_to_cancelled/" => "/money/vouchers/move_expense_status_to_cancelled/", "/expenses/get_expense_accounts_by_type/" => "/money/vouchers/get_expense_accounts_by_type/", "/cases/related_companies/" => "/cases/edit/", "/cases/related_companies_add/" => "/cases/edit/", "/cases/related_companies_update/" => "/cases/edit/", "/cases/related_companies_delete/" => "/cases/edit/", "/cases/related_contacts/" => "/cases/edit/", "/cases/related_contacts_add/" => "/cases/edit/", "/cases/related_contacts_update/" => "/cases/edit/", "/cases/related_contacts_delete/" => "/cases/edit/", "/cases/related_opponents/" => "/cases/edit/", "/cases/related_opponents_add/" => "/cases/edit/", "/cases/related_opponents_delete/" => "/cases/edit/", "/cases/case_outsources/" => "/cases/edit/", "/cases/add_case_outsource/" => "/cases/edit/", "/cases/update_case_outsource/" => "/cases/edit/", "/cases/delete_case_outsource/" => "/cases/edit/", "/cases/move_status/" => "/cases/edit/", "/time_tracking/add/" => "/time_tracking/add/", "/time_tracking/edit/" => "/time_tracking/edit/", "/time_tracking/load_data/" => "/time_tracking/add/", "/accounts/add/" => "/accounts/add/", "/accounts/quick_add/" => "/accounts/quick_add/", "/notifications/list_notifications/" => "/notifications/index/", "/notifications/get_unseen_count/" => "/notifications/index/", "/notifications/notify/" => "/notifications/broadcast/", "/notifications/delete/" => "/notifications/delete/", "/intellectual_properties/add/" => "/intellectual_properties/add/", "/intellectual_properties/edit/" => "/intellectual_properties/edit/", "/intellectual_properties/list_data/" => "/intellectual_properties/index/", "/calendars/add/" => "/calendars/add/", "/calendars/edit/" => "/calendars/edit/", "/calendars/list_user_meetings/" => "/calendars/view/", "/documents/company_load_documents/" => "/companies/documents/", "/documents/company_upload_file/" => "/companies/upload_file/", "/documents/company_create_folder/" => "/companies/create_folder/", "/documents/company_rename_file/" => "/companies/rename_file/", "/documents/company_rename_folder/" => "/companies/rename_folder/", "/documents/company_edit_documents/" => "/companies/edit_documents/", "/documents/company_share_folder/" => "/companies/share_folder/", "/documents/company_download_file/" => "/companies/download_file/", "/documents/company_delete_document/" => "/companies/delete_document/", "/documents/contact_load_documents/" => "/contacts/documents/", "/documents/contact_upload_file/" => "/contacts/upload_file/", "/documents/contact_create_folder/" => "/contacts/create_folder/", "/documents/contact_rename_file/" => "/contacts/rename_file/", "/documents/contact_rename_folder/" => "/contacts/rename_folder/", "/documents/contact_edit_documents/" => "/contacts/edit_documents/", "/documents/contact_share_folder/" => "/contacts/share_folder/", "/documents/contact_download_file/" => "/contacts/case_file_download/", "/documents/contact_delete_document/" => "/contacts/delete_document/", "/documents/case_load_documents/" => "/cases/documents/", "/documents/case_upload_file/" => "/cases/upload_file/", "/documents/case_create_folder/" => "/cases/create_folder/", "/documents/case_rename_file/" => "/cases/rename_file/", "/documents/case_rename_folder/" => "/cases/rename_folder/", "/documents/case_edit_documents/" => "/cases/edit_documents/", "/documents/case_share_folder/" => "/cases/share_folder/", "/documents/case_download_file/" => "/cases/download_file/", "/documents/case_email_download_file/" => "/cases/download_file/", "/documents/case_delete_document/" => "/cases/delete_document/", "/documents/case_email_delete_document/" => "/cases/delete_document/", "/documents/intellectual_property_load_documents/" => "/intellectual_properties/documents/", "/documents/intellectual_property_upload_file/" => "/intellectual_properties/upload_file/", "/documents/intellectual_property_create_folder/" => "/intellectual_properties/create_folder/", "/documents/intellectual_property_rename_file/" => "/intellectual_properties/rename_file/", "/documents/intellectual_property_rename_folder/" => "/intellectual_properties/rename_folder/", "/documents/intellectual_property_edit_documents/" => "/intellectual_properties/edit_documents/", "/documents/intellectual_property_share_folder/" => "/intellectual_properties/share_folder/", "/documents/intellectual_property_download_file/" => "/intellectual_properties/download_file/", "/documents/intellectual_property_delete_document/" => "/intellectual_properties/delete_document/", "/documents/docs_load_documents/" => "/docs/load_documents/", "/documents/docs_upload_file/" => "/docs/upload_file/", "/documents/docs_create_folder/" => "/docs/create_folder/", "/documents/docs_rename_file/" => "/docs/rename_file/", "/documents/docs_rename_folder/" => "/docs/rename_folder/", "/documents/docs_edit_documents/" => "/docs/edit_documents/", "/documents/docs_share_folder/" => "/docs/share_folder/", "/documents/docs_download_file/" => "/docs/download_file/", "/documents/docs_delete_document/" => "/docs/delete_document/", "/documents/bill_load_documents/" => "/money/vouchers/bill_related_documents/", "/documents/bill_upload_file/" => "/money/vouchers/bill_upload_file/", "/documents/bill_rename_file/" => "/money/vouchers/bill_rename_file/", "/documents/bill_edit_documents/" => "/money/vouchers/bill_edit_documents/", "/documents/bill_download_file/" => "/money/vouchers/bill_download_file/", "/documents/bill_delete_document/" => "/money/vouchers/bill_delete_document/", "/documents/bill_file_info/" => "/money/vouchers/bill_download_file/", "/documents/expense_load_documents/" => "/money/vouchers/expense_related_documents/", "/documents/expense_upload_file/" => "/money/vouchers/expense_upload_file/", "/documents/expense_rename_file/" => "/money/vouchers/expense_rename_file/", "/documents/expense_edit_documents/" => "/money/vouchers/expense_edit_documents/", "/documents/expense_download_file/" => "/money/vouchers/expense_download_file/", "/documents/expense_delete_document/" => "/money/vouchers/expense_delete_document/", "/documents/expense_file_info/" => "/money/vouchers/expense_download_file/", "/documents/invoice_load_documents/" => "/money/vouchers/invoice_related_documents/", "/documents/invoice_upload_file/" => "/money/vouchers/invoice_upload_file/", "/documents/invoice_file_rename/" => "/money/vouchers/invoice_rename_file/", "/documents/invoice_edit_documents/" => "/money/vouchers/invoice_edit_documents/", "/documents/invoice_download_file/" => "/money/vouchers/invoice_download_file/", "/documents/invoice_delete_document/" => "/money/vouchers/invoice_delete_document/", "/documents/invoice_file_info/" => "/money/vouchers/invoice_download_file/", "/clients/check_if_exists/" => "/money/clients/index/", "/clients/add/" => "/money/clients/add/", "/cases/change_litigation_stage/" => "/cases/change_litigation_stage/", "/tasks/view/" => "/tasks/view/", "/tasks/move_status/" => "/tasks/view/", "/tasks/add_comment/" => "/tasks/add_comment/", "/tasks/edit_comment/" => "/tasks/edit_comment/", "/tasks/comments/" => "/tasks/comments/", "/documents/docs_move_document/" => "/docs/move_document/", "/documents/case_move_document/" => "/cases/move_document/", "/documents/contact_move_document/" => "/contacts/move_document/", "/documents/company_move_document/" => "/companies/move_document/", "/documents/intellectual_property_move_document/" => "/intellectual_properties/move_document/", "/hearings/set_judgment/" => "/cases/hearings/", "/hearings/verify/" => "/cases/hearings/", "/documents/docs_file_info/" => "/docs/download_file/", "/documents/case_file_info/" => "/cases/download_file/", "/documents/intellectual_property_file_info/" => "/intellectual_properties/download_file/", "/documents/company_file_info/" => "/companies/download_file/", "/documents/contact_file_info/" => "/contacts/download_file/", "/documents/case_container_load_documents/" => "/case_containers/documents/", "/documents/case_container_upload_file/" => "/case_containers/upload_file/", "/documents/case_container_upload_files/" => "/case_containers/upload_file/", "/documents/case_container_create_folder/" => "/case_containers/create_folder/", "/documents/case_container_rename_file/" => "/case_containers/rename_file/", "/documents/case_container_rename_folder/" => "/case_containers/rename_folder/", "/documents/case_container_edit_documents/" => "/case_containers/edit_documents/", "/documents/case_container_share_folder/" => "/case_containers/share_folder/", "/documents/case_container_download_file/" => "/case_containers/download_file/", "/documents/case_container_delete_document/" => "/case_containers/delete_document/", "/documents/case_container_file_info/" => "/case_containers/download_file/", "/documents/case_container_move_document/" => "/case_containers/move_document/", "/expenses/add_bulk/" => "/money/vouchers/expenses_add_bulk/", "/documents/contract_download_file/" => "/contract/contracts/download_file/", "/documents/contract_upload_file/" => "/contract/contracts/upload_file/", "/documents/contract_file_info/" => "/contract/contracts/download_file/", "/clauses/load_data/" => "/contract/clauses/index/", "/clauses/list_clauses/" => "/contract/clauses/index/", "/clauses/lookup/" => "/contract/clauses/index/", "/clauses/edit/" => "/contract/clauses/edit/", "/clauses/add/" => "/contract/clauses/add/", "/contracts/add/" => "/contract/contracts/add/", "/documents/contract_create_folder/" => "/contract/contracts/create_folder/", "/documents/contract_rename_folder/" => "/contract/contracts/rename_folder/", "/documents/contract_rename_file/" => "/contract/contracts/rename_file/", "/documents/contract_edit_documents/" => "/contract/contracts/edit_documents/", "/documents/contract_share_folder/" => "/contract/contracts/share_folder/", "/documents/contract_delete_document/" => "/contract/contracts/delete_document/", "/documents/contract_load_documents/" => "/contract/contracts/documents/", "/documents/contract_move_document/" => "/contract/contracts/move_document/", "/contracts/edit/" => "/contract/contracts/edit/", "/documents/docs_upload_template/" => "/docs/upload_file/", "/documents/docs_load_all_folders/" => "/docs/load_documents/", "/tasks/list_all_tasks/" => "/tasks/all_tasks/", "/contract_templates/add/" => "/contract_templates/add/", "/contract_templates/edit/" => "/contract_templates/edit/", "/contracts/get_contract_type_subtypes/" => "/contract_templates/add/", "/contracts/get_contract_types/" => "/contract/contracts/add/", "/contracts/load_templates_per_contract_types/" => "/contract/contracts/add/", "/contracts/add_contract_using_questionnaire/" => "/contract/contracts/add/", "/contracts/add_note/" => "/contract/contracts/add_comment/", "/contracts/email_note_add/" => "/contract/contracts/add_comment/", "/contracts/edit_note/" => "/contract/contracts/edit_comment/", "/contracts/note_list/" => "/contract/contracts/get_all_comments/", "/contracts/note_list_only/" => "/contract/contracts/get_all_core_and_cp_comments/", "/contracts/email_note_list/" => "/contract/contracts/get_all_email_comments/", "/contracts/note_delete/" => "/contract/contracts/delete_comment/", "/contracts/email_note_delete/" => "/contract/contracts/delete_email_comment/", "/contracts/list_contracts/" => "/contract/contracts/index/", "/contracts/move_status/" => "/contract/contracts/move_status/", "/contracts/approvals/" => "/contract/contracts/approval_center/", "/contracts/signature/" => "/contract/contracts/signature_center/", "/legal_opinions/add/"=> "/legal_opinions/add/",	"/legal_opinions/edit/"=> "/legal_opinions/edit/",	"/legal_opinions/load_data/"=> "/legal_opinions/add/",	"/task_locations/add/"=> "/task_locations/add/",	"/legal_opinions/view/"=> "/legal_opinions/view/",	"/legal_opinions/move_status/"=> "/legal_opinions/view/",	"/legal_opinions/add_comment/"=> "/legal_opinions/add_comment/",	"/legal_opinions/edit_comment/"=> "/legal_opinions/edit_comment/",	"/legal_opinions/comments/"=> "/legal_opinions/comments/",	"/legal_opinions/list_all_opinions/"=> "/legal_opinions/list_all_opinions/","/suppliers/list_all_suppliers/"=> "/suppliers/list_all_suppliers/"];
        $this->load->library("ErrorCodes");
        $this->load->model("action");
        $apiKey = $this->input->get_request_header("X-api-key", true);  
        $this->response = default_response_data();
        if (!$apiKey) {
            $this->response["error"] = ["msg" => $this->lang->line("invalid_API_KEY"), "code" => $this->errorcodes->get_unauthorized()];
            $this->render($this->response);
        }
        $requestChannel = $this->input->get_request_header("X-api-channel", true);
        if (!in_array($requestChannel, ["outlook", "mobile", "gmail", "document-editor", "microsoft-teams", "word", "webex"])) {
            $license_data = $this->licensor->get_all_licenses();
            $selected_plan = $license_data["core"]["App4Legal"]["plan"] ?? false;
            $plan_excluded_features = $selected_plan ? $this->get_plan_excluded_features($selected_plan) : false;
            if ($plan_excluded_features) {
                $plan_excluded_features = explode(",", $plan_excluded_features);
                if (in_array("Third-Party-Integration-(APIs)", $plan_excluded_features)) {
                    $this->response["error"] = ["msg" => $this->lang->line("plan_feature_warning_enterprise"), "code" => $this->errorcodes->get_forbidden()];
                    $this->render($this->response);
                }
            }
        }
        $this->load->model("legal_case", "legal_casefactory");
        $this->legal_case = $this->legal_casefactory->get_instance();
        if (isset($requestChannel)) {
            switch ($requestChannel) {
                case "outlook":
                    $requestChannel = $this->legal_case->get("apiOutlookChannel");
                    break;
                case "mobile":
                    $requestChannel = $this->legal_case->get("apiMobileChannel");
                    break;
                case "gmail":
                    $requestChannel = $this->legal_case->get("apiGmailChannel");
                    break;
                case "microsoft-teams":
                    $requestChannel = $this->legal_case->get("apiMicrosoftTeamsChannel");
                    break;
                default:
                    $requestChannel = $this->legal_case->get("webChannel");
            }
        } else {
            $requestChannel = $this->legal_case->get("webChannel");
        }
        if ($apiKey === false) {
            $this->response = $this->action->check_key("");
        } else {
            $this->response = $this->action->check_key($apiKey, $this->getInstanceConfig("api_key_validity_time"));
        }
        if (isset($this->response["error"]) && $this->response["error"]) {
            $this->render($this->response);
        }
        $this->apiKey = $apiKey;
        $this->system_preferences = $this->get_all_system_preferences_values();
        $this->user_logged_in_data = $this->response["success"]["data"];
        $this->user_logged_in_data["channel"] = $requestChannel;
        $this->_set_money_data();
        $this->load->model("user_preference");
        $user_id = $this->user_logged_in_data["user_id"];
        $this->user_preference->reset_fields();
        $this->user_preference->fetch(["user_id" => $user_id, "keyName" => "language"]);
        $language = $this->user_preference->get_field("keyValue");
        $this->config->set_item("language", $language);
        $this->license_package = $this->licensor->license_package;
        if ($this->license_package == "core_contract" && $this->user_logged_in_data["type"] !== "both") {
            $this->license_package = $this->user_logged_in_data["type"];
        }
        $this->_check_user_permissions($this->user_logged_in_data["user_group_id"]);
        $this->load->model("instance_data");
        $installation_type = $this->instance_data->get_value_by_key("installationType");
        $this->cloud_installation_type = $installation_type["keyValue"] == "on-cloud";
        if ($this->cloud_installation_type) {
            $instance_client_type = $this->instance_data->get_value_by_key("clientType");
            $this->instance_client_type = $instance_client_type["keyValue"];
        }
        define("BASEURL", substr(base_url(), 0, -1 * strlen(MODULE) - 9));
    }
    public function render($response)
    {
        $output = $this->output->set_output(json_encode($response));
        header("Content-Type: application/json");
        header("Access-Control-Allow-Origin: *");
        echo $output->get_output();
        exit;
    }
    public function get_plan_excluded_features($plan)
    {
        $excluded_features = ["cloud-basic" => "In-line-Word-Editor,Document-Automation-&-Templates,Multi-entities-Accounting,Advanced-Permissions,In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "cloud-business" => "In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "cloud-enterprise" => "", "self-business" => "In-Document-Search,Advanced-Workflows-&-Approvals,LDAP-User-Management-Integration,Azure-User-Management-Integration,Third-Party-Integration-(APIs),Service-Level-Agreement", "self-enterprise" => ""];
        return $excluded_features[$plan] ?? false;
    }
    private function _set_money_data()
    {
        $user_id = $this->user_logged_in_data["user_id"];
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $user_id, "keyName" => "money_language"]);
        $this->user_logged_in_data["moneyLanguage"] = $this->user_preference->get_field("keyValue");
        $this->user_preference->reset_fields();
        $this->user_preference->fetch(["user_id" => $user_id, "keyName" => "organization"]);
        $this->user_logged_in_data["organizationID"] = $this->user_preference->get_field("keyValue");
        if (!$this->user_logged_in_data["organizationID"]) {
            $this->load->model("organization", "organizationfactory");
            $this->organization = $this->organizationfactory->get_instance();
            $organizationList = $this->organization->load_organization_list();
            if (!empty($organizationList)) {
                $this->user_logged_in_data["organizationID"] = $organizationList[0]["id"];
            } else {
                $response = default_response_data();
                $response["error"] = $this->lang->line("You_do_not_have_entities_in_App4Legal_Money");
                $this->render($response);
            }
        }
    }
    private function _check_user_permissions($user_group_id, $controller = "", $action = "", $permissions_map = "")
    {
        $controller = $controller != "" ? "/" . $controller . "/" : "/" . $this->uri->rsegment(1) . "/";
        if ($this->uri->rsegment(2) != "" || $action != "") {
            $action = $action != "" ? $controller . $action . "/" : $controller . $this->uri->rsegment(2) . "/";
        } else {
            $action = $controller . "index/";
        }
        $module = "core";
        $have_access = false;
        if (!in_array($action, $this->excempted_uri) || $permissions_map != "") {
            $corePermissionAction = isset($permissions_map) && $permissions_map != "" ? $permissions_map : (isset($this->permissions_map[$action]) ? $this->permissions_map[$action] : false);

            if (!$corePermissionAction) {
                $have_access = false;
            } else {
                $action = $corePermissionAction;
                $this->load->model("user_group_permission");
                $roles_allowed_uris = $this->user_group_permission->get_permissions($user_group_id, false);
                $module_selected = substr($action, 0, strpos($action, "/", 1) + 1);
                $controller_selected = substr($action, strlen($module_selected) - 1, strpos($action, "/", strlen($module_selected)) - strlen($module_selected) + 2);
                if (in_array($module_selected, ["/money/", "/contract/"])) {
                    $module = substr($module_selected, 1, strlen($module_selected) - 2);
                    $controller = $controller_selected;
                    $action = substr($action, strlen($module) + 1);
                } else {
                    $controller = substr($action, 0, strpos($action, "/", 1) + 1);
                }
                $roles_allowed_uris = $roles_allowed_uris[$module];
                if ($this->is_auth->_array_in_array("/", $roles_allowed_uris)) {
                    $have_access = true;
                } else {
                    foreach ($roles_allowed_uris as $allowed_uris) {
                        if ($allowed_uris != NULL && ($this->is_auth->_array_in_array($controller, [$allowed_uris]) || $this->is_auth->_array_in_array($action, [$allowed_uris]))) {
                            $have_access = true;
                        }
                    }
                }
            }
        } else {
            $have_access = true;
        }

        if (!$have_access) {
            if ($permissions_map == "") {
                $response = default_response_data();
                $response["error"] = ["msg" => $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_page"), "code" => $this->errorcodes->get_forbidden()];
                $this->render($response);
            } else {
                return false;
            }
        } else {
            if ($permissions_map != "") {
                return true;
            }
        }
    }
    public function getApiKey()
    {
        return $this->apiKey;
    }
    public function api_check_user_permissions($user_group_id, $controller = "", $action = "", $permissions_map = "")
    {
        if ($controller != "" && $action != "" && $permissions_map != "") {
            return $this->_check_user_permissions($user_group_id, $controller, $action, $permissions_map);
        }
        $this->_check_user_permissions($user_group_id, $controller, $action, $permissions_map);
    }
    public function check_license_availability()
    {
        if (!$this->licensor->check_license_date(MODULE)) {
            $response = default_response_data();
            $response["error"] = strip_tags($this->licensor->get_license_message(MODULE));
            $this->render($response);
        }
    }
    public function get_all_money_preferences_values()
    {
        $this->load->model("money_preference");
        return $this->money_preference->get_key_groups();
    }
    public function get_lang_code()
    {
        $this->load->model("user_preference");
        $this->user_preference->fetch(["user_id" => $this->user_logged_in_data["user_id"], "keyName" => "language"]);
        $lang = $this->user_preference->get_field("keyValue");
        if ($lang) {
            switch ($lang) {
                case "english":
                    $lang = "en";
                    break;
                case "arabic":
                    $lang = "ar";
                    break;
                case "french":
                    $lang = "fr";
                    break;
                default:
                    $lang = "en";
            }
        } else {
            $lang = "en";
        }
        return $lang;
    }
    public function getDBDriver()
    {
        return $this->db->dbdriver === "mysqli" ? "MYSQL" : "SQLSRV";
    }
    public function validate_sortable_fields($sortable, $allowedFields)
    {
        if (is_array($sortable) && !empty($sortable) && !empty($allowedFields)) {
            foreach ($sortable as $field => $dir) {
                if (!(isset($field) && isset($dir)) || empty($field) || empty($dir)) {
                    return "incorrect sortable paramteres. it should be defined with field and dir";
                }
                if (!in_array($field, $allowedFields)) {
                    return "you are trying to sort a field not exists";
                }
                if (!in_array($dir, ["asc", "desc"])) {
                    return "dir should be defined with asc or desc";
                }
            }
        }
        return true;
    }
    public function validate_email_format($email)
    {
        if (isset($email)) {
            $emails = explode(";", $email);
            $matches = NULL;
            foreach ($emails as $singleEmail) {
                if (trim($singleEmail) != "" && !preg_match("/(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){255,})(?!(?:(?:\\x22?\\x5C[\\x00-\\x7E]\\x22?)|(?:\\x22?[^\\x5C\\x22]\\x22?)){65,}@)(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22))(?:\\.(?:(?:[\\x21\\x23-\\x27\\x2A\\x2B\\x2D\\x2F-\\x39\\x3D\\x3F\\x5E-\\x7E]+)|(?:\\x22(?:[\\x01-\\x08\\x0B\\x0C\\x0E-\\x1F\\x21\\x23-\\x5B\\x5D-\\x7F]|(?:\\x5C[\\x00-\\x7F]))*\\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\\]))\$/", trim($singleEmail), $matches)) {
                    return false;
                }
            }
        }
        return true;
    }
    public function regenerate_note($note)
    {
        $note = mb_convert_encoding($note, "UTF-8", "UTF-8");
        $note = str_replace("&lt !--[if !supportLists]--&gt;", "", $note);
        $note = str_replace("&lt;!--[endif]--&gt;", "", $note);
        return $note;
    }
    public function lookup_term_validation($term)
    {
        if ($this->_lookup_term_validation($term)) {
            $response["error"] = sprintf($this->lang->line("min_length_rule"), $this->lang->line("term"), $this::TERMS_MIN_NUMBER);
            $this->render($response);
            exit;
        }
    }
    private function _lookup_term_validation($term)
    {
        return mb_strlen($term) < $this::TERMS_MIN_NUMBER;
    }
    public function authenticate_actions_per_license($access = "core")
    {
        if ($this->license_package == "core_contract") {
            return true;
        }
        if ($this->license_package !== $access) {
            $response = default_response_data();
            $response["error"] = sprintf($this->lang->line("license_permission_denied"), $this->lang->line($access));
            if ($this->user_logged_in_data["type"] !== $access) {
                $response["error"] = sprintf($this->lang->line("permission_not_allowed_for"), $this->lang->line($access));
            }
            $this->render($response);
        }
        return true;
    }
}

?>