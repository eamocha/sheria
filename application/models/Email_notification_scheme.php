<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
class Email_notification_scheme extends My_Model
{
    protected $modelName = "email_notification_scheme";
    protected $_table = "email_notifications_scheme";
    protected $_fieldsNames = ["id", "trigger_action", "notify_to", "notify_cc", "createdBy", "createdOn", "modifiedBy", "modifiedOn", "hide_show_send_email_notification"];
    protected $predefinedValues = ["assignee", "invitees", "reporter", "case_creator", "note_creator", "lawyers", "case_assignee", "users_defined", "creator", "requester", "watchers", "contributors", "approvers", "advisors", "advisor_assignee", "signees", "verification_process_user_groups", "advisor_task_assignee", "advisor_task_requester", "matter_contributors", "expense_users"];
    protected $userCcEditableValues = ["add_litigation_case", "add_matter_case", "add_ip_case", "edit_litigation_case", "edit_matter_case", "edit_ip_case", "edit_case_status", "cp_edit_ticket", "cp_add_comment", "add_case_event", "edit_case_event", "notify_me_before", "add_matter_container", "edit_matter_container", "add_contract", "edit_contract", "edit_contract_status", "add_contract_comment", "legal_edit_ticket", "legal_add_comment", "notify_requested_by_watchers_cp", "add_contract_inform_assignee", "amend_contract", "renew_contract", "contract_approved", "contract_signed", "contract_awaiting_signature", "negotiation_requested", "negotiation_comment_added", "negotiation_completed", "contract_notify_requested_by_watchers_cp"];
    protected $cpTriggers = ["legal_edit_ticket", "legal_add_comment", "notify_requested_by_watchers_cp", "notify_new_client_portal_user", "contract_notify_requested_by_watchers_cp"];
    protected $advTriggers = ["core_user_assigned_case", "core_user_add_comment", "core_user_edit_case_status", "core_user_add_comment_on_advisor_task"];
    protected $userToEditableValues = ["cp_add_ticket", "add_case_event", "edit_case_event", "edit_case_status", "add_contract", "edit_contract", "edit_contract_status", "add_contract_comment", "contract_rejected", "cp_add_contract", "amend_contract", "renew_contract", "contract_approved", "contract_signed", "negotiation_requested", "negotiation_comment_added", "negotiation_completed", "add_litigation_case", "add_matter_case", "edit_litigation_case", "edit_matter_case", "add_note_case", "edit_ip_case", "add_ip_case", "add_hearing", "cp_edit_ticket", "cp_add_comment", "add_contract_inform_assignee"];
    protected $onlyA4lCreator = ["edit_matter_case"];
    protected $meetings = ["add_meeting", "edit_meeting"];
    protected $builtInLogs = true;
    protected $checkbox = ["add_matter_case", "edit_matter_case", "add_litigation_case", "edit_litigation_case", "add_opinions", "edit_opinion","add_tasks", "edit_task", "add_meeting", "edit_meeting", "add_hearing", "add_ip_case", "edit_ip_case", "add_case_event", "edit_case_event", "notify_from_notifications", "add_task_note","add_opinion_note", "add_note_case", "add_matter_container", "edit_matter_container", "add_contract", "edit_contract", "add_contract_comment", "legal_edit_ticket", "legal_add_comment", "notify_requested_by_watchers_cp", "notify_new_client_portal_user", "amend_contract", "contract_approved", "contract_signed", "renew_contract", "negotiation_requested", "add_user", "contract_notify_requested_by_watchers_cp"];
    protected $core_actions = ["add_matter_case", "edit_matter_case", "add_litigation_case", "edit_litigation_case",  "add_opinions", "edit_opinion","add_tasks", "edit_task", "add_meeting", "edit_meeting", "add_hearing", "add_ip_case", "edit_ip_case", "add_case_event", "edit_case_event", "edit_case_status", "add_note_case", "add_matter_container", "edit_matter_container", "legal_edit_ticket", "legal_add_comment", "notify_requested_by_watchers_cp", "notify_new_client_portal_user", "add_user", "contract_notify_requested_by_watchers_cp"];
    protected $contract_actions = ["add_contract", "add_contract_inform_assignee", "edit_contract", "add_contract_comment", "amend_contract", "contract_approved", "contract_rejected", "contract_signed", "renew_contract", "negotiation_requested", "forward_negotiation", "complete_negotiation", "edit_contract_status", "cp_add_contract", "contract_awaiting_signature"];
    protected $matter_contributors_triggers = ["edit_litigation_case", "add_note_case", "edit_matter_case"];
    protected $moneyTriggers = ["add_expense"];
    public function __construct()
    {
        parent::__construct();
    }
    public function load_notifications_scheme()
    {
        $query = [];
        $query["select"] = ["email_notifications_scheme.trigger_action,email_notifications_scheme.notify_to,email_notifications_scheme.notify_cc"];
        $result = $this->load_all();
        return $result;
    }
    public function update_records($id, $data, $field)
    {
        $this->reset_fields();
        $this->fetch(["id" => $id]);
        $this->set_field($field, $data);
        if ($this->update()) {
            return true;
        }
        return false;
    }
    public function get_emails($object, $model, $model_data)
    {
        $result = [];
        $this->fetch(["trigger_action" => $object]);
        $notify_to = $this->get_field("notify_to");
        $notify_cc = $this->get_field("notify_cc");
        $notify_to_emails = explode(";", $notify_to);
        $notify_cc_emails = explode(";", $notify_cc);
        $to_emails = $this->load_emails($notify_to_emails, $model, $model_data);
        $cc_emails = $this->load_emails($notify_cc_emails, $model, $model_data);
        if (!empty($cc_emails)) {
            foreach ($cc_emails as $key => $cc_email) {
                if (!empty($to_emails) && in_array($cc_email, is_array($to_emails[0]) ? $to_emails[0] : $to_emails)) {
                    unset($cc_emails[$key]);
                }
            }
        }
        $result["to_emails"] = !empty($to_emails) && is_array($to_emails[0]) ? $to_emails[0] : $to_emails;
        $result["cc_emails"] = $cc_emails;
        $this->reset_fields();
        return $result;
    }
    private function get_creators_emails($model, $model_id)
    {
        $_table = $this->_table;
        $this->_table = "users";
        $query = [];
        $query["select"] = "users.email";
        if ($model == "case_comments") {
            $query["join"][] = [$model, $model . ".user_id = users.id", "left"];
        } else {
            $query["join"][] = [$model, $model . ".createdBy = users.id", "left"];
        }
        $query["join"][] = ["user_profiles", "user_profiles.user_id = users.id", "left"];
        $query["where"] = [[$model . ".id = ", $model_id], ["user_profiles.status", "Active"]];
        $query["where"] = [$model . ".id = ", $model_id];
        $emails = $this->load($query);
        $this->_table = $_table;
        return $emails["email"];
    }
    private function get_assignee_emails($model, $model_id)
    {
        $_table = $this->_table;
        $this->_table = "users";
        $query = [];
        $query["select"] = "users.email";
        if ($model == "legal_cases" || $model == "legal_case_containers") {
            $query["join"][] = [$model, $model . ".user_id = users.id", "left"];
        } else {
            if ($model == "contract") {
                $query["join"][] = [$model, $model . ".assignee_id = users.id", "left"];
            } else {
                $query["join"][] = [$model, $model . ".assigned_to = users.id", "left"];
            }
        }
        $query["join"][] = ["user_profiles", "user_profiles.user_id = users.id", "left"];
        $query["where"] = [[$model . ".id = ", $model_id], ["user_profiles.status", "Active"]];
        $emails = $this->load($query);
        $this->_table = $_table;
        return $emails["email"]??"";
    }
    private function get_reporter_emails($model, $model_id)
    {
        $_table = $this->_table;
        $this->_table = "users";
        $query = [];
        $query["select"] = "users.email";
        $query["join"][] = [$model, $model . ".reporter = users.id", "left"];
        $query["join"][] = ["user_profiles", "user_profiles.user_id = users.id", "left"];
        $query["where"] = [[$model . ".id = ", $model_id], ["user_profiles.status", "Active"]];
        $query["where"] = [$model . ".id = ", $model_id];
        $emails = $this->load($query);
        $this->_table = $_table;
        return $emails["email"];
    }
    private function get_needed_emails($model_array)
    {
        $_table = $this->_table;
        $this->_table = "users";
        $emails = [];
        if ($model_array) {
            foreach ($model_array as $id) {
                $query = [];
                $query["select"] = "users.email";
                $query["join"][] = ["user_profiles", "user_profiles.user_id = users.id", "left"];
                $query["where"][] = ["user_profiles.status", "Active"];
                $query["where"][] = ["users.id = ", $id];
                $result = $this->load($query);
                $emails[] = $result["email"]??"";
            }
        }
        $this->_table = $_table;
        return $emails;
    }
    private function get_contract_requester_emails($model, $model_id)
    {
        $_table = $this->_table;
        $this->_table = "contacts_grid";
        $query = [];
        $query["select"] = "contacts_grid.email";
        $query["join"] = [$model, $model . ".requester_id = contacts_grid.id", "left"];
        $query["where"] = [$model . ".id = ", $model_id];
        $emails = $this->load($query);
        $this->_table = $_table;

        // Check if $emails is an array and has the expected data
        if (is_array($emails) && isset($emails["email"])) {
            return $emails["email"];
        }

        // Return a default value or handle the error
        return null; // or an empty string, or throw an exception, depending on your needs
    }
    private function load_emails($notification_emails, $model, $model_data)
    {
        $predefined_values = $emails = $data = [];
        foreach ($notification_emails as $key => $notify_cc_email) {
            if (in_array($notify_cc_email, $this->predefinedValues)) {
                $predefined_values[] = $notify_cc_email;
                unset($notification_emails[$key]);
            }
        }
        if (!empty($predefined_values)) {
            foreach ($predefined_values as $predefined_value) {
                switch ($predefined_value) {
                    case "assignee":
                        $emails[] = $this->get_assignee_emails($model, $model_data["id"]);
                        break;
                    case "case_creator":
                        if (!isset($model_data["unsetCreator"])) {
                            $emails[] = $this->get_creators_emails($model, $model_data["id"]);
                        }
                        break;
                    case "note_creator":
                        $emails[] = $this->get_creators_emails("case_comments", $model_data["case_comment_id"]);
                        break;
                    case "task_creator":
                        $emails[] = $this->get_creators_emails($model, $model_data["id"]);
                        break;
                    case "opinion_creator":
                        $emails[] = $this->get_creators_emails($model, $model_data["id"]);
                        break;
                    case "reporter":
                        $emails[] = $this->get_reporter_emails($model, $model_data["id"]);
                        break;
                    case "case_assignee":
                        $emails[] = $this->get_assignee_emails($model, $model_data["id"]);
                        break;
                    case "invitees":
                        $emails[] = $this->get_needed_emails($model_data["watchers_ids"]);
                        break;
                    case "contributors":
                        $emails = array_merge($emails, $this->get_needed_emails($model_data["contributors_ids"]) ?? []);
                        break;
                    case "approvers":
                        $user_emails = $this->get_needed_emails($model_data["user_approvers_ids"]) ?? [];
                        $collaborator_emails = [];
                        if (!empty($model_data["collaborator_approvers_ids"])) {
                            foreach ($model_data["collaborator_approvers_ids"] as $collaborator) {
                                $query["where"] = ["users.id", $collaborator];
                                $collaborator_emails[] = $this->get_customer_portal_users_email($query);
                            }
                        }
                        $emails = array_merge($emails, $user_emails, $collaborator_emails);
                        break;
                    case "signees":
                        $user_emails = $this->get_needed_emails($model_data["user_signees_ids"]) ?? [];
                        $collaborator_emails = [];
                        if (!empty($model_data["collaborator_signees_ids"])) {
                            foreach ($model_data["collaborator_signees_ids"] as $collaborator) {
                                $query["where"] = ["users.id", $collaborator];
                                $collaborator_emails[] = $this->get_customer_portal_users_email($query);
                            }
                        }
                        $emails = array_merge($emails, $user_emails, $collaborator_emails);
                        break;
                    case "lawyers":
                        $emails = array_merge($emails, $this->get_needed_emails($model_data["lawyers"]));
                        break;
                    case "shared_with":
                        $emails = array_merge($emails, $this->get_needed_emails($model_data["watchers_ids"]) ?? []);
                        break;
                    case "creator":
                        if ($model == "legal_case_containers") {
                            $emails[] = $this->get_creators_emails($model, $model_data["id"]);
                        } else {
                            $query["join"] = [$model, $model . ".createdBy = users.id", "left"];
                            $query["where"] = [$model . ".id = ", $model_data["id"]];
                            $return_value = $this->get_customer_portal_users_email($query);
                            if ($return_value != NULL) {
                                $emails[] = $return_value;
                            }
                        }
                        break;
                    case "requester":
                        if ($model === "contract") {
                            $return_value = $this->get_contract_requester_emails($model, $model_data["id"]);
                        } else {
                            $query["join"] = ["legal_cases", "legal_cases.requestedBy = users.contact_id", "left"];
                            $query["where"] = ["legal_cases.id = ", $model_data["id"]];
                            $return_value = $this->get_customer_portal_users_email($query);
                        }
                        if ($return_value != NULL) {
                            $emails[] = $return_value;
                        }
                        break;
                    case "watchers":
                        $query["join"] = ["customer_portal_ticket_watchers", "customer_portal_ticket_watchers.customer_portal_user_id = users.id", "left"];
                        $query["where"] = ["customer_portal_ticket_watchers.legal_case_id = ", $model_data["id"]];
                        $return_value = $this->get_customer_portal_users_email($query, true);
                        if ($return_value != NULL) {
                            $emails = array_merge($emails, $return_value);
                        }
                        break;
                    case "advisors":
                        $query["join"] = ["legal_cases_contacts", "legal_cases_contacts.case_id = " . $model_data["id"], "left"];
                        $query["where"] = ["legal_cases_contacts.contactType", "external lawyer"];
                        $return_value = $this->get_advisor_users_emails($query);
                        if ($return_value) {
                            $emails[] = $return_value;
                        }
                        break;
                    case "verification_process_user_groups":
                        $user_gruop_list = implode(", ", $model_data["user_groups"]);
                        $email_arr = $this->ci->db->query("SELECT email FROM users LEFT JOIN user_profiles ON user_profiles.user_id = users.id WHERE user_group_id IN (" . $user_gruop_list . ") AND user_profiles.status = 'Active'")->result_array();
                        foreach ($email_arr as $email_item) {
                            $emails[] = $email_item["email"];
                        }
                        break;
                    case "advisor_task_assignee":
                        $this->ci->load->model("advisor_task", "advisor_taskfactory");
                        $this->advisor_task = $this->ci->advisor_taskfactory->get_instance();
                        $advisor_task_assignee = $this->advisor_task->get_task_assignee($model_data["id"]);
                        $emails[] = $advisor_task_assignee["email"];
                        break;
                    case "advisor_task_requester":
                        $this->ci->load->model("advisor_task", "advisor_taskfactory");
                        $this->advisor_task = $this->ci->advisor_taskfactory->get_instance();
                        $advisor_task_requester = $this->advisor_task->get_task_requester($model_data["id"]);
                        $emails[] = $advisor_task_requester["email"];
                        break;
                    case "matter_contributors":
                        $contributors_emails = $this->ci->legal_case->load_all_cases_lawyers_contributors_emails($model_data["id"]);
                        $emails = array_merge($emails, $contributors_emails);
                        break;
                    case "expense_users":
                        $emails = array_merge($emails, $this->get_needed_emails($model_data["user_ids"]));
                        break;
                    default:
                }
                        $data = isset($notification_emails) && !empty($notification_emails) ? array_unique(array_merge($notification_emails, $emails)) : array_unique($emails);

            }
        } else {
            $data = array_unique($notification_emails);
        }
        return $data;
    }
    public function get_user_full_name($user_ids, $model = "user_profile")
    {
        $this->ci->load->model("user_profile");
        $full_name = [];
        if (!is_array($user_ids)) {
            $user_ids = explode(" ", $user_ids);
        }
        foreach ($user_ids as $user_id) {
            if ($model == "user_profile") {
                $this->ci->{$model}->fetch(["user_id" => $user_id]);
            } else {
                $this->ci->{$model}->fetch(["id" => $user_id]);
            }
            $full_name[] = $this->ci->{$model}->get_field("firstName") . " " . $this->ci->{$model}->get_field("lastName");
            $this->ci->{$model}->reset_fields();
        }
        return implode(", ", $full_name);
    }
    public function get_advisor_user_full_name($user_ids)
    {
        $this->ci->load->model("advisor_users", "advisor_usersfactory");
        $this->advisor_users = $this->ci->advisor_usersfactory->get_instance();
        $full_name = [];
        if (!is_array($user_ids)) {
            $user_ids = explode(" ", $user_ids);
        }
        foreach ($user_ids as $user_id) {
            $this->advisor_users->fetch(["id" => $user_id]);
            $full_name[] = $this->advisor_users->get_field("firstName") . " " . $this->advisor_users->get_field("lastName");
            $this->advisor_users->reset_fields();
        }
        return implode(", ", $full_name);
    }
    private function get_customer_portal_users_email($query_parts = [], $multiple = false)
    {
        $_table = $this->_table;
        $this->_table = "customer_portal_users as users";
        $query["select"] = "users.email";
        if (isset($query_parts["join"])) {
            $query["join"][] = $query_parts["join"];
        }
        if (isset($query_parts["where"])) {
            $query["where"][] = $query_parts["where"];
        }
        if ($multiple) {
            $result = $this->load_all($query);
            $emails = [];
            foreach ($result as $value) {
                $emails[] = $value["email"];
            }
        } else {
            $result = $this->load($query);
            $emails = $result["email"];
        }
        $this->_table = $_table;
        return $emails;
    }
    private function get_advisor_users_emails($query_parts = [], $multiple = false)
    {
        $_table = $this->_table;
        $this->_table = "advisor_users as users";
        $query["select"] = "users.email";
        if (isset($query_parts["join"])) {
            $query["join"] = $query_parts["join"];
        }
        if (isset($query_parts["where"])) {
            $query["where"] = $query_parts["where"];
        }
        $query["where"][] = ["users.contact_id = legal_cases_contacts.contact_id"];
        $query["where"][] = ["user_profiles.status", "Active"];
        $query["join"][] = ["user_profiles", "user_profiles.user_id = users.id", "left"];
        if ($multiple) {
            $result = $this->load_all($query);
            $emails = [];
            foreach ($result as $value) {
                $emails[] = $value["email"];
            }
        } else {
            $result = $this->load($query);
            $emails = $result["email"];
        }
        $this->_table = $_table;
        return $emails;
    }
    public function load_workflow_transition_notification_details($id)
    {
        $_table = $this->_table;
        $this->_table = "workflow_status_transition as transition";
        $query = [];
        $query["select"] = ["transition.id, transition.name, transition.workflow_id, workflows.name as workflow_name", false];
        $query["join"][] = ["workflows", "workflows.id = transition.workflow_id", "inner"];
        $query["where"][] = ["transition.id", $id];
        $result = $this->load($query);
        $this->_table = $_table;
        return $result;
    }
    public function load_contract_workflow_transition_notification_details($id)
    {
        $_table = $this->_table;
        $this->_table = "contract_workflow_status_transition as transition";
        $query = [];
        $query["select"] = ["transition.id, transition.name, transition.workflow_id, workflows.name as workflow_name", false];
        $query["join"][] = ["contract_workflow as workflows", "workflows.id = transition.workflow_id", "inner"];
        $query["where"][] = ["transition.id", $id];
        $result = $this->load($query);
        $this->_table = $_table;
        return $result;
    }
    public function get_hide_show_send_email_notification_by_trigger_action($trigger_action)
    {
        $query = [];
        $query["select"] = ["hide_show_send_email_notification"];
        $query["where"][] = ["trigger_action", $trigger_action];
        $result = $this->load($query);
        return $result ? $result["hide_show_send_email_notification"] : NULL;
    }
    public function load_available_users_emails()
    {
        $this->ci->load->model("user", "userfactory");
        $this->ci->user = $this->ci->userfactory->get_instance();
        $users_emails = $this->ci->user->load_active_emails();
        return array_map(function ($users_emails) {
            return ["email" => $users_emails];
        }, array_keys($users_emails));
    }
    public function load_sla_details($id)
    {
        $_table = $this->_table;
        $this->_table = "customer_portal_sla as sla";
        $query = [];
        $query["select"] = ["sla.id, sla.name, sla.workflow_id, workflows.name as workflow_name", false];
        $query["join"][] = ["workflows", "workflows.id = sla.workflow_id", "inner"];
        $query["where"][] = ["sla.id", $id];
        $result = $this->load($query);
        $this->_table = $_table;
        return $result;
    }
    /// New function to load SLA Contract details
    public function load_sla_contract_details($id)
    {
        $_table = $this->_table;
        $this->_table = "contract_sla_management as sla";
        $query = [];
        $query["select"] = ["sla.id, sla.name, sla.workflow_id, workflows.name as workflow_name", false];
        $query["join"][] = ["contract_workflow as workflows", "workflows.id = sla.workflow_id", "inner"];
        $query["where"][] = ["sla.id", $id];
        $result = $this->load($query);
        $this->_table = $_table;
        return $result; //end of new function
    }
}

?>