<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Contract_workflows extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_type_language", "contract_type_languagefactory");
        $this->contract_type_language = $this->contract_type_languagefactory->get_instance();
        $this->load->model("contract_status");
        $this->load->model("contract_status_language", "contract_status_languagefactory");
        $this->contract_status_language = $this->contract_status_languagefactory->get_instance();
        $this->load->model("contract_workflow", "contract_workflowfactory");
        $this->contract_workflow = $this->contract_workflowfactory->get_instance();
        $this->load->model("contract_workflow_status_relation", "contract_workflow_status_relationfactory");
        $this->contract_workflow_status_relation = $this->contract_workflow_status_relationfactory->get_instance();
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $this->load->model(["contract_workflow_status_transition_permission"]);
        $this->load->model("contract_workflow_status_transition_screen_field", "contract_workflow_status_transition_screen_fieldfactory");
        $this->contract_workflow_status_transition_screen_field = $this->contract_workflow_status_transition_screen_fieldfactory->get_instance();
        $this->load->model("contract_workflow_step_function","contract_workflow_step_functionfactory");
        $this->contract_workflow_step_function=$this->contract_workflow_step_functionfactory->get_instance();
        $this->load->model("contract_workflow_step_checklist","contract_workflow_step_checklistfactory");
        $this->contract_workflow_step_checklist=$this->contract_workflow_step_checklistfactory->get_instance();
    }
    public function index($workflow_id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_workflows"));
        $data = [];
        $data["workflows"] = $this->contract_workflow->load_workflows();
        foreach ($data["workflows"] as $workflow) {
            $data["records"][$workflow["id"]]["statuses"] = $this->contract_workflow->load_all_statuses_per_workflow($workflow["id"]);
            $data["records"][$workflow["id"]]["transitions"] = $this->contract_workflow->load_all_transitions_per_workflow($workflow["id"]);
        }

        $data["workflow_id"] = $workflow_id;
        $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "css");
        $this->includes("jquery/arrows-and-boxes/jquery.wz_jsgraphics", "js");
        $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "js");
        $this->includes("contract/workflows", "js");
        $this->load->view("partial/header");
        $this->load->view("workflows/index", $data);
        $this->load->view("partial/footer");
    }
    public function add()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->input->post(NULL)) {
            $data = [];
            $this->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
            $this->contract_workflow_per_type = $this->contract_workflow_per_typefactory->get_instance();
            $data["types"] = $this->contract_type_language->load_all_per_language();
            $data["title"] = $this->lang->line("add");
            $data["workflow"] = false;
            $data["workflows_types"] = array_column($this->contract_workflow_per_type->load_all(), "type_id");
            $response["html"] = $this->load->view("workflows/form", $data, true);
        } else {
            $response["result"] = true;
            $response = $this->contract_workflow->add($this->input->post(NULL));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($id && $this->validate_id($id)) {
            if (!$this->input->post(NULL)) {
                $data = [];
                $data["types"] = $this->contract_type_language->load_all_per_language();
                $data["title"] = $this->lang->line("edit_workflow");
                $this->contract_workflow->fetch($id);
                $data["workflow"] = $this->contract_workflow->get_fields();
                $this->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
                $this->contract_workflow_per_type = $this->contract_workflow_per_typefactory->get_instance();
                $data["selected_types"] = array_column($this->contract_workflow_per_type->load_all(["where" => ["workflow_id", $id]]), "type_id");
                $workflows_types = array_column($this->contract_workflow_per_type->load_all(), "type_id");
                $data["workflows_types"] = array_values(array_diff($workflows_types, $data["selected_types"]));
                $response["html"] = $this->load->view("workflows/form", $data, true);
            } else {
                $result = $this->contract_workflow->validate_workflow_edit($this->input->post(NULL));
                if (!$result["result"]) {
                    if (isset($result["related_contracts"]) && !empty($result["related_contracts"])) {
                        $result["workflow_id"] = $id;
                        $result["statuses"] = $this->contract_workflow->load_all_statuses_per_workflow($this->contract_workflow->get("system_workflow_id"), true);
                        $response["html"] = $this->load->view("workflows/statuses_migration_form", $result, true);
                    } else {
                        $response = $result;
                    }
                } else {
                    $response["result"] = $this->contract_workflow->edit_workflow($this->input->post(NULL));
                }
            }
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function update_step($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($id && $this->validate_id($id)) {
            if (!$this->input->post(NULL)) {
                $data = [];
                $data["types"] = $this->contract_type_language->load_all_per_language();
                $data["title"] = $this->lang->line("edit_workflow");
                $this->contract_workflow->fetch($id);
                $data["workflow"] = $this->contract_workflow->get_fields();
                $this->load->model("contract_workflow_per_type", "contract_workflow_per_typefactory");
                $this->contract_workflow_per_type = $this->contract_workflow_per_typefactory->get_instance();
                $data["selected_types"] = array_column($this->contract_workflow_per_type->load_all(["where" => ["workflow_id", $id]]), "type_id");
                $workflows_types = array_column($this->contract_workflow_per_type->load_all(), "type_id");
                $data["workflows_types"] = array_values(array_diff($workflows_types, $data["selected_types"]));
                $response["html"] = $this->load->view("workflows/form", $data, true);
            } else {
                $result = $this->contract_workflow->validate_workflow_edit($this->input->post(NULL));
                if (!$result["result"]) {
                    if (isset($result["related_contracts"]) && !empty($result["related_contracts"])) {
                        $result["workflow_id"] = $id;
                        $result["statuses"] = $this->contract_workflow->load_all_statuses_per_workflow($this->contract_workflow->get("system_workflow_id"), true);
                        $response["html"] = $this->load->view("workflows/statuses_migration_form", $result, true);
                    } else {
                        $response = $result;
                    }
                } else {
                    $response["result"] = $this->contract_workflow->edit_workflow($this->input->post(NULL));
                }
            }
        } else {
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function tests($workflow_id)
    {
        $this->load->view("partial/header");
        $this->load->view("contracts/tests/1",);
        $this->load->view("partial/footer");}
   public function configure($workflow_id)
{
    $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("manage_workflow_steps"));

    $this->contract_workflow->fetch($workflow_id);
    $data['workflow'] = $this->contract_workflow->get_fields();
    $steps = $this->contract_workflow->load_all_statuses_per_workflow($workflow_id);
    
    // Use 'use' to pass $workflow_id into the closure
    $steps = array_map(function ($step) use ($workflow_id) {
        $step_id = $step['id'];
        
        // Correct WHERE clause syntax
        $step['checklist'] = $this->contract_workflow_step_checklist->load_all([
            "where" => [['step_id', $step_id]]
        ]);
        
        $step['functions'] = $this->contract_workflow_step_function->load_all([
            "where" => [['step_id', $step_id]]
        ]);
        
        // Correct WHERE clause syntax for multiple conditions
        $step["transitions"] = $this->contract_workflow_status_transition->load_all([
            "where" => [
                ['from_step', $step_id],
                ['workflow_id', $workflow_id]
            ]
        ]);
        
        return $step;
    }, $steps);
    
    $data["workflow"]['steps'] = $steps;

    $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "css");
    $this->includes("jquery/arrows-and-boxes/jquery.wz_jsgraphics", "js");
    $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "js");
    $this->includes("contract/workflows", "js");
    $this->includes("tests/style", "css");
    $this->includes("tests/script", "js");
    $this->load->view("partial/header");
    $this->load->view("workflows/configure", $data);
    $this->load->view("partial/footer");
}
public function fetch_workflow_data($workflow_id)
{
    $this->contract_workflow->fetch($workflow_id);
    $response['workflow'] = $this->contract_workflow->get_fields();
    $steps = $this->contract_workflow->load_all_statuses_per_workflow($workflow_id);
    
    // Use 'use' to pass $workflow_id into the closure
    $steps = array_map(function ($step) use ($workflow_id) {
        $step_id = $step['id'];
        
        $step['checklist'] = $this->contract_workflow_step_checklist->load_all([
            "where" => [['step_id', $step_id]]
        ]);
        
        $step['functions'] = $this->contract_workflow_step_function->load_all([
            "where" => [['step_id', $step_id]]
        ]);
        
        $step["transitions"] = $this->contract_workflow_status_transition->load_all([
            "where" => [
                ['from_step', $step_id],
                ['workflow_id', $workflow_id]
            ]
        ]);
        
        return $step;
    }, $steps);
    
    $response["workflow"]['steps'] = $steps;
    $this->output->set_content_type("application/json")->set_output(json_encode($response));
}

    /** is used when already a status/step has been added and this is to create a relationship
     * @param $id
     * @return void
     */
   public function add_workflow_status($id = 0)
{
    if (!$this->input->is_ajax_request()) {
        show_404();
    }
    
    $workflow_id = $id ?: $this->input->post("workflow_id");
    $response = ['result' => false];
    
    // GET THE STEPS ALREADY IN DB
    $workflow_statuses = $this->contract_workflow_status_relation->load_all([
        "where" => ["workflow_id", $workflow_id]
    ]);
    
    if ($this->input->post()) {
        $post_data = $this->input->post(NULL, TRUE); // Get all POST data with XSS filtering
        
        // Validate required fields
        if (empty($post_data['status_id'])) {
            $response["validation_errors"]["status_id"] = $this->lang->line("status_id_required");
            $response["result"] = false;
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
            return;
        }
        
        // Check if status is already linked to this workflow
        $existing = $this->contract_workflow_status_relation->fetch([
            "workflow_id" => $workflow_id, 
            "status_id" => $post_data['status_id']
        ]);
        
        if ($existing) {
            $response["validation_errors"]["status_id"] = $this->lang->line("workflow_status_already_assigned");
            $response["result"] = false;
        } else {
            $start_point = empty($workflow_statuses) ? 1 : 0;
            $approval_start_point = !empty($post_data['approval_start_point']) ? 1 : 0;
            
            $this->contract_workflow_status_relation->set_fields($post_data);
            $this->contract_workflow_status_relation->set_field("start_point", $start_point);
            $this->contract_workflow_status_relation->set_field("approval_start_point", $approval_start_point);
            $this->contract_workflow_status_relation->set_field("workflow_id", $workflow_id);
            
            if ($this->contract_workflow_status_relation->insert()) {
                $response["result"] = true;
                $response["workflow_id"] = $workflow_id;
                $response["message"] = $this->lang->line("workflow_status_added_successfully");
            } else {
                $response["validation_errors"] = $this->contract_workflow_status_relation->get("validationErrors");
                $response["result"] = false;
            }
        }
    } else {
        // GET request - show form
        $data["workflow_id"] = $workflow_id;
        $statuses = $this->contract_status_language->load_list_per_language();
        
        $current_workflow_statuses_ids = array_column($workflow_statuses, "status_id");
        
        // Corrected array filtering
        $filtered_statuses = array_filter($statuses, function ($status_id) use ($current_workflow_statuses_ids) {
            return !in_array($status_id, $current_workflow_statuses_ids);
        }, ARRAY_FILTER_USE_KEY);
        
        $data["statuses"] = $filtered_statuses;
        $data["has_approval_start_point"] = in_array(1, array_column($workflow_statuses, "approval_start_point"));
        $data["title"] = $this->lang->line("add_new_workflow_status");
        
        $response["html"] = $this->load->view("workflows/workflow_status_form", $data, true);
        $response["result"] = true;
    }
    
    $this->output->set_content_type("application/json")->set_output(json_encode($response));
}
    public function add_transition($workflow_id = 0, $status_id = 0)
    {
        $this->load->model("language");
        $language_id = $this->language->get_id_by_session_lang();
        $this->load->model("email_notification_scheme");
        if (!$this->input->is_ajax_request()) {
            if (!$status_id || !$this->contract_status_language->fetch(["status_id" => $status_id, "language_id" => $language_id])) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $data = $this->load_common_data();
            $data["from_step_name"] = $this->contract_status_language->get_field("name");
            $data["workflow_id"] = $workflow_id;
            $data["to_steps"] = $this->contract_status->load_allowed_to_statuses($status_id, $workflow_id);
            $data["transition"] = $this->contract_workflow_status_transition->get_fields();
            $data["transition"]["from_step"] = $status_id;
            $data["notifications"] = $this->email_notification_scheme->get_fields();
            $data["allow_advanced_settings"] = true;
            $data["plan_feature_warning_msg"] = "";
            if ($this->session->userdata("selected_plan") && $this->session->userdata("plan_excluded_features")) {
                $plan_execluded_features = explode(",", $this->session->userdata("plan_excluded_features"));
                if (empty($plan_execluded_features) && in_array("Advanced-Workflows-&-Approvals", $plan_execluded_features)) {
                    $data["allow_advanced_settings"] = false;
                    $plan_feature_warning_msgs = $this->session->userdata("plan_feature_warning_msgs");
                    $data["plan_feature_warning_msg"] = $plan_feature_warning_msgs["Advanced-Workflows-&-Approvals"] ?? $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_feature");
                }
            }
            $this->includes("scripts/status_transitions", "js");
            $this->includes("contract/transitions", "js");
            $this->load->view("partial/header");
            $this->load->view("workflows/transition_form", $data);
            $this->load->view("partial/footer");
        } else {
            $post_data = $this->input->post(NULL);
            array_walk($post_data, [$this, "sanitize_post"]);
            unset($post_data["id"]);
            $response["result"] = true;
            $this->contract_workflow_status_transition->set_fields($post_data);
            $this->contract_workflow_status_transition->set_field("approval_needed", isset($post_data["approval_needed"]) && $post_data["approval_needed"] === "yes" ? 1 : 0);
            if ($this->contract_workflow_status_transition->insert()) {
                $transition_id = $this->contract_workflow_status_transition->get_field("id");
                $permissions = $this->input->post("permissions");
                if ($permissions) {
                    $permissions["transition_id"] = $transition_id;
                    $permissions["users"] = isset($permissions["users"]) ? implode(",", $permissions["users"]) : "";
                    $permissions["user_groups"] = isset($permissions["user_groups"]) ? implode(",", $permissions["user_groups"]) : "";
                    $response["result"] = $this->contract_workflow_status_transition_permission->save_value($permissions);
                }
                $screen_fields = $this->input->post("screen_fields");
                if ($screen_fields) {
                    $this->contract_workflow_status_transition_screen_field->set_field("transition_id", $transition_id);
                    $this->contract_workflow_status_transition_screen_field->set_field("data", serialize($screen_fields));
                    if (!$this->contract_workflow_status_transition_screen_field->insert()) {
                        $response["validation_errors"] = $this->contract_workflow_status_transition_screen_field->get("validationErrors");
                        $response["result"] = false;
                    }
                }
                $notifications = $this->input->post("notifications");
                if ($notifications) {
                    $notifications["trigger_action"] = "contract_transition_" . $transition_id;
                    $notifications["notify_to"] = isset($notifications["notify_to"]) ? implode(";", $notifications["notify_to"]) : "";
                    $notifications["notify_cc"] = isset($notifications["notify_cc"]) ? implode(";", $notifications["notify_cc"]) : "";
                    $this->email_notification_scheme->set_field("hide_show_send_email_notification", "1");
                    $this->email_notification_scheme->set_fields($notifications);
                    $result = $this->email_notification_scheme->insert();
                    if (!$result) {
                        $response["result"] = false;
                        $response["display_error"] = $this->lang->line("permissions_not_saved");
                    }
                }
            } else {
                $response["validation_errors"] = $this->contract_workflow_status_transition->get("validationErrors");
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function edit_transition($transition_id)
    {
        $this->load->model("email_notification_scheme");
        if (!$this->input->is_ajax_request()) {
            if (!$transition_id || !$this->contract_workflow_status_transition->fetch($transition_id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("contract_workflows");
            }
            $workflow_id = $this->contract_workflow_status_transition->get_field("workflow_id");
            $data = $this->load_common_data();
            $data["transition"] = $this->contract_workflow_status_transition->get_fields();
            $this->load->model("language");
            $language_id = $this->language->get_id_by_session_lang();
            $this->contract_status_language->fetch(["status_id" => $data["transition"]["from_step"], "language_id" => $language_id]);
            $data["from_step_name"] = $this->contract_status_language->get_field("name");
            $data["workflow_id"] = $workflow_id;
            $data["to_steps"] = $this->contract_status->load_allowed_to_statuses($data["transition"]["from_step"], $workflow_id, $data["transition"]["to_step"]);
            $permissions = $this->contract_workflow_status_transition_permission->load_permissions($transition_id);
            if (!empty($permissions)) {
                foreach ($permissions as $value) {
                    if (!empty($value["users"])) {
                        foreach (explode(",", $value["users"]) as $id) {
                            $data["permissions"]["users"][] = $this->user->get_name_by_id($id);
                        }
                    }
                    if (!empty($value["user_groups"])) {
                        foreach (explode(",", $value["user_groups"]) as $id) {
                            $data["permissions"]["user_groups"][] = $this->user_group->get_name_by_id($id);
                        }
                    }
                }
            }
            if ($this->contract_workflow_status_transition_screen_field->fetch(["transition_id" => $transition_id])) {
                $data["selected_fields"] = unserialize($this->contract_workflow_status_transition_screen_field->get_field("data"));
            }
            $this->email_notification_scheme->fetch(["trigger_action" => "contract_transition_" . $transition_id]);
            $data["notifications"] = $this->email_notification_scheme->get_fields();
            if ($data["notifications"]["notify_to"]) {
                $data["notifications"]["notify_to"] = explode(";", $data["notifications"]["notify_to"]);
                if (!empty($data["notifications"]["notify_to"])) {
                    foreach ($data["notifications"]["notify_to"] as $to) {
                        if (!in_array($to, $data["users_emails"])) {
                            $data["users_emails"][]["email"] = $to;
                        }
                    }
                }
            }
            if ($data["notifications"]["notify_cc"]) {
                $data["notifications"]["notify_cc"] = explode(";", $data["notifications"]["notify_cc"]);
                if (!empty($data["notifications"]["notify_cc"])) {
                    foreach ($data["notifications"]["notify_cc"] as $cc) {
                        if (!in_array($cc, $data["users_emails"])) {
                            $data["users_emails"][]["email"] = $cc;
                        }
                    }
                }
            }
            $data["allow_advanced_settings"] = true;
            $data["plan_feature_warning_msg"] = "";
            if ($this->session->userdata("selected_plan") && $this->session->userdata("plan_excluded_features")) {
                $plan_execluded_features = explode(",", $this->session->userdata("plan_excluded_features"));
                if (!empty($plan_execluded_features) && in_array("Advanced-Workflows-&-Approvals", $plan_execluded_features)) {
                    $data["allow_advanced_settings"] = false;
                    $plan_feature_warning_msgs = $this->session->userdata("plan_feature_warning_msgs");
                    $data["plan_feature_warning_msg"] = $plan_feature_warning_msgs["Advanced-Workflows-&-Approvals"] ?? $this->lang->line("you_do_not_have_enough_previlages_to_access_the_requested_feature");
                }
            }
            $this->includes("scripts/status_transitions", "js");
            $this->includes("contract/transitions", "js");
            $this->load->view("partial/header");
            $this->load->view("workflows/transition_form", $data);
            $this->load->view("partial/footer");
        } else {
            $response["result"] = true;
            $this->contract_workflow_status_transition->set_fields($this->input->post(NULL));
            $this->contract_workflow_status_transition->set_field("approval_needed", $this->input->post("approval_needed", true) === "yes" ? 1 : 0);
            if ($this->contract_workflow_status_transition->update()) {
                $permissions = $this->input->post("permissions");
                if ($permissions) {
                    $permissions["transition_id"] = $transition_id;
                    $permissions["users"] = isset($permissions["users"]) ? implode(",", $permissions["users"]) : "";
                    $permissions["user_groups"] = isset($permissions["user_groups"]) ? implode(",", $permissions["user_groups"]) : "";
                    $this->contract_workflow_status_transition_permission->save_value($permissions);
                } else {
                    $this->contract_workflow_status_transition_permission->delete_transition_permission($transition_id);
                }
                $screen_fields = $this->input->post("screen_fields");
                if ($screen_fields) {
                    $screen_fetched = $this->contract_workflow_status_transition_screen_field->fetch(["transition_id" => $transition_id]);
                    $this->contract_workflow_status_transition_screen_field->set_field("transition_id", $transition_id);
                    $this->contract_workflow_status_transition_screen_field->set_field("data", serialize($screen_fields));
                    $screen_fields_result = $screen_fetched ? $this->contract_workflow_status_transition_screen_field->update() : $this->contract_workflow_status_transition_screen_field->insert();
                    if (!$screen_fields_result) {
                        $response["validation_errors"] = $this->contract_workflow_status_transition_screen_field->get("validationErrors");
                        $response["result"] = false;
                    }
                } else {
                    $this->contract_workflow_status_transition_screen_field->delete(["where" => [["transition_id", $transition_id]]]);
                }
                $notifications = $this->input->post("notifications");
if ($notifications) {
    $trigger_action = "contract_transition_" . $transition_id;
    
    // 1. Prepare data
    $notifications["trigger_action"] = $trigger_action;
    $notifications["notify_to"] = isset($notifications["notify_to"]) ? implode(";", $notifications["notify_to"]) : "";
    $notifications["notify_cc"] = isset($notifications["notify_cc"]) ? implode(";", $notifications["notify_cc"]) : "";
    
    // 2. Check if a notification scheme already exists for this transition
    $scheme_exists = $this->email_notification_scheme->fetch(["trigger_action" => $trigger_action]);
    
    $this->email_notification_scheme->set_field("hide_show_send_email_notification", "1");
    $this->email_notification_scheme->set_fields($notifications);
    
    // 3. Update if exists, otherwise insert
    $notif_result = $scheme_exists ? $this->email_notification_scheme->update() : $this->email_notification_scheme->insert();
    
    if (!$notif_result) {
        $response["result"] = false;
        $response["display_error"] = $this->lang->line("permissions_not_saved"); // Or a specific notification error
    }
}
            } else {
                $response["validation_errors"] = $this->contract_workflow_status_transition->get("validationErrors");
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function load_common_data()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("status_transition"));
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["users_list"] = $this->user->load_available_list();
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        $data["user_groups_list"] = $this->user_group->load_available_list();
        $users_emails = $this->user->load_active_emails();
        $data["users_emails"] = array_map(function ($users_emails) {
            return ["email" => $users_emails];
        }, array_keys($users_emails));
        $this->load->model("contract_fields", "contract_fieldsfactory");
        $this->contract_fields = $this->contract_fieldsfactory->get_instance();
        $data["screen_fields"] = $this->contract_fields->fields;
        return $data;
    }
    public function set_as_start_point($workflow, $status)
    {
        if (!$this->input->is_ajax_request() || !$status || !$workflow) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        if ($this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status])) {
            $this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "start_point" => 1]);
            $this->contract_workflow_status_relation->set_field("start_point", 0);
            $this->contract_workflow_status_relation->update();
            $this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status]);
            $this->contract_workflow_status_relation->set_field("start_point", 1);
            $response["result"] = $this->contract_workflow_status_relation->update() ? true : false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function set_as_approval_start_point($workflow, $status)
    {
        if (!$this->input->is_ajax_request() || !$status || !$workflow) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        if ($this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status])) {
            $this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "approval_start_point" => 1]);
            $this->contract_workflow_status_relation->set_field("approval_start_point", 0);
            $this->contract_workflow_status_relation->update();
            $this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status]);
            $this->contract_workflow_status_relation->set_field("approval_start_point", 1);
            $response["result"] = $this->contract_workflow_status_relation->update() ? true : false;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_workflow_status($status_id, $workflow_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        } else {
            $response = [];
            if (!$status_id || !$workflow_id) {
                $response["result"] = "ERROR";
                $response["message"] = $this->lang->line("error");
            } else {
                if (0 < count($this->contract_workflow->load_status_transitions($status_id, $workflow_id))) {
                    $data = [];
                    $data["status_id"] = $status_id;
                    $data["workflow_id"] = $workflow_id;
                    $response["result"] = "FOREIGN_KEY_CONSTRAINT";
                    $response["html"] = $this->load->view("workflows/delete_status_error_modal", $data, true);
                } else {
                    if ($this->contract_workflow_status_relation->delete(["where" => [["status_id", $status_id], ["workflow_id", $workflow_id]]])) {
                        $this->contract_workflow_status_relation->fetch(["workflow_id" => $workflow_id, "start_point" => 1]);
                        $start_point = $this->contract_workflow_status_relation->get_field("status_id");
                        $this->contract_workflow_status_relation->move_contracts_to_start_point_status($status_id, $start_point, $workflow_id);
                        $response["result"] = "DELETED";
                        $response["message"] = $this->lang->line("record_deleted_successfully");
                    } else {
                        $response["result"] = "ERROR";
                        $response["message"] = $this->lang->line("record_not_deleted");
                    }
                }
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function delete_transition($transition_id)
    {
        if (!$transition_id || !$this->contract_workflow_status_transition->fetch($transition_id)) {
            $response["result"] = false;
        }
        if ($this->contract_workflow_status_transition->delete($transition_id)) {
            $response["result"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_workflow($workflow_id)
    {
        if ($this->contract_workflow->fetch($workflow_id) && $this->contract_workflow->get_field("category") != "system") {
            if ($this->contract_workflow->delete_workflow($workflow_id)) {
                $this->set_flashmessage("information", $this->lang->line("record_deleted"));
                redirect("contract_workflows/index");
            }
            $this->set_flashmessage("error", sprintf($this->lang->line("delete_workflow_status_failed"), $this->lang->line("contract")));
        } else {
            $this->set_flashmessage("error", $this->lang->line("failed_workflow_system_delete"));
        }
        redirect("contract_workflows/index/" . $workflow_id);
    }
    public function view_status_transitions($status_id, $workflow_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $data = [];
        $this->load->model("language");
        $language_id = $this->language->get_id_by_session_lang();
        $this->contract_status_language->fetch(["status_id" => $status_id, "language_id" => $language_id]);
        $data["from_step_name"] = $this->contract_status_language->get_field("name");
        $data["transitions"] = $this->contract_workflow->load_all_transitions_per_workflow($workflow_id, $status_id);
        $response["html"] = $this->load->view("workflows/view_status_transitions", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function migrate_statuses()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        if ($this->input->post(NULL)) {
            $error = 0;
            foreach ($this->input->post("new_statuses") as $key => $status_id) {
                $data = ["workflow_id" => $this->input->post("workflow_id"), "old_status" => $this->input->post("old_statuses")[$key], "new_status" => $status_id, "type" => $this->input->post("type")[$key]];
                if (!$this->contract_workflow->update_contract_status_workflow($data)) {
                    $error++;
                }
            }
            $response["result"] = $error ? false : true;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
}

?>