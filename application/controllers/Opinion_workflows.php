<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Opinion_workflows extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion_workflow", "opinion_workflowfactory");
        $this->opinion_workflow = $this->opinion_workflowfactory->get_instance();
        $this->load->model("opinion_status");
        $this->load->model("opinion_type", "opinion_typefactory");
        $this->opinion_type = $this->opinion_typefactory->get_instance();
        $this->load->model("opinion_workflow_status_relation", "opinion_workflow_status_relationfactory");
        $this->opinion_workflow_status_relation = $this->opinion_workflow_status_relationfactory->get_instance();
        $this->load->model("opinion_workflow_status_transition", "opinion_workflow_status_transitionfactory");
        $this->opinion_workflow_status_transition = $this->opinion_workflow_status_transitionfactory->get_instance();
        $this->load->model(["opinion_workflow_status_transition_permission"]);
        $this->load->model("opinion_workflow_status_transition_screen_field", "opinion_workflow_status_transition_screen_fieldfactory");
        $this->opinion_workflow_status_transition_screen_field = $this->opinion_workflow_status_transition_screen_fieldfactory->get_instance();
    }
    public function index($workflow_id = 0)
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("opinion_workflows"));
        $data = [];
        $data["workflows"] = $this->opinion_workflow->load_workflows();
        foreach ($data["workflows"] as $workflow) {
            $data["records"][$workflow["id"]]["statuses"] = $this->opinion_workflow->load_all_statuses_per_workflow($workflow["id"]);
            $data["records"][$workflow["id"]]["transitions"] = $this->opinion_workflow->load_all_transitions_per_workflow($workflow["id"]);
        }
        $data["workflow_id"] = $workflow_id;
        $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "css");
        $this->includes("jquery/arrows-and-boxes/jquery.wz_jsgraphics", "js");
        $this->includes("jquery/arrows-and-boxes/arrowsandboxes", "js");
        $this->includes("scripts/opinion_workflow", "js");
        $this->load->view("partial/header");
        $this->load->view("opinion_workflows/index", $data);
        $this->load->view("partial/footer");
    }
    public function add_workflow()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if (!$this->input->post(NULL)) {
            $data = []; 
            $this->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
            $this->opinion_workflow_type = $this->opinion_workflow_typefactory->get_instance();
            $data["opinion_types"] = $this->opinion_type->load_all_per_language();
            $data["title"] = $this->lang->line("add_new_workflow");
            $data["workflow"] = false;
            $data["workflows_types"] = array_column($this->opinion_workflow_type->load_all(), "type_id");
            $response["html"] = $this->load->view("opinion_workflows/form", $data, true);
        } else {
            $response["result"] = true;
            $response = $this->opinion_workflow->add_workflow($this->input->post(NULL));
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function edit_workflow($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response["result"] = false;
        if ($id && $this->validate_id($id)) {
            if (!$this->input->post(NULL)) {
                $data = [];
                $data["opinion_types"] = $this->opinion_type->load_all_per_language();
                $data["title"] = $this->lang->line("edit_workflow");
                $this->opinion_workflow->fetch($id);
                $data["workflow"] = $this->opinion_workflow->get_fields();
                $this->load->model("opinion_workflow_type", "opinion_workflow_typefactory");
                $this->opinion_workflow_type = $this->opinion_workflow_typefactory->get_instance();
                $data["selected_types"] = array_column($this->opinion_workflow_type->load_all(["where" => ["workflow_id", $id]]), "type_id");
                $workflows_types = array_column($this->opinion_workflow_type->load_all(), "type_id");
                $data["workflows_types"] = array_values(array_diff($workflows_types, $data["selected_types"]));
                $response["html"] = $this->load->view("opinion_workflows/form", $data, true);
            } else {
                $result = $this->opinion_workflow->validate_workflow_edit($this->input->post(NULL));
                if (!$result["result"]) {
                    if (isset($result["related_opinions"]) && !empty($result["related_opinions"])) {
                        $result["workflow_id"] = $id;
                        $result["statuses"] = $this->opinion_workflow->load_all_statuses_per_workflow($this->opinion_workflow->get("system_workflow_id"), true);
                        $response["html"] = $this->load->view("opinion_workflows/statuses_migration_form", $result, true);
                    } else {
                        $response = $result;
                    }
                } else {
                    $response["result"] = $this->opinion_workflow->edit_workflow($this->input->post(NULL));
                }
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_workflow_status($id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $workflow_id = $id ?: $this->input->post("workflow_id");
        $workflow_statuses = $this->opinion_workflow_status_relation->load_all(["where" => ["workflow_id", $workflow_id]]);
        if ($this->input->post(NULL)) {
            if ($this->opinion_workflow_status_relation->fetch(["workflow_id" => $workflow_id, "status_id" => $this->input->post("status_id")])) {
                $response["validation_errors"]["status_id"] = $this->lang->line("workflow_status_already_assigned");
                $response["result"] = false;
            } else {
                $start_point = empty($workflow_statuses) ? 1 : 0;
                $this->opinion_workflow_status_relation->set_fields($this->input->post(NULL));
                $this->opinion_workflow_status_relation->set_field("start_point", $start_point);
                if ($this->opinion_workflow_status_relation->insert()) {
                    $response["result"] = true;
                    $response["workflow_id"] = $workflow_id;
                } else {
                    $response["validation_errors"] = $this->opinion_workflow_status_relation->get("validationErrors");
                    $response["result"] = false;
                }
            }
        } else {
            $data["workflow_id"] = $workflow_id;
            $statuses = $this->opinion_status->load_list();
            $current_workflow_statuses_ids = array_map(function ($item) {
                return $item["status_id"];
            }, $workflow_statuses);
            $filtered_statuses = array_filter($statuses, 
            function ($key) use ($current_workflow_statuses_ids)  {  
                return !in_array($key, $current_workflow_statuses_ids);
            }, ARRAY_FILTER_USE_KEY);
            $data["statuses"] = $filtered_statuses;
            $data["title"] = $this->lang->line("add_new_workflow_status");
            $response["html"] = $this->load->view("opinion_workflows/workflow_status_form", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function add_transition($workflow_id = 0, $status_id = 0)
    {
        if (!$this->input->is_ajax_request()) {
            if (!$status_id || !$this->opinion_status->fetch($status_id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("dashboard");
            }
            $data = $this->load_common_data();
            $data["from_step_name"] = $this->opinion_status->get_field("name");
            $data["workflow_id"] = $workflow_id;
            $data["to_steps"] = $this->opinion_status->load_allowed_to_statuses($status_id, $workflow_id);
            $data["transition"] = $this->opinion_workflow_status_transition->get_fields();
            $data["transition"]["from_step"] = $status_id;
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
            $this->includes("scripts/opinion_transitions", "js");
            $this->load->view("partial/header");
            $this->load->view("opinion_workflows/transition_form", $data);
            $this->load->view("partial/footer");
        } else {
            $post_data = $this->input->post(NULL);
            array_walk($post_data, [$this, "sanitize_post"]);
            unset($post_data["id"]);
            $response["result"] = true;
            $this->opinion_workflow_status_transition->set_fields($post_data);
            if ($this->opinion_workflow_status_transition->insert()) {
                $transition_id = $this->opinion_workflow_status_transition->get_field("id");
                $permissions = $this->input->post("permissions");
                if ($permissions) {
                    $permissions["transition"] = $transition_id;
                    $permissions["users"] = isset($permissions["users"]) ? implode(",", $permissions["users"]) : "";
                    $permissions["user_groups"] = isset($permissions["user_groups"]) ? implode(",", $permissions["user_groups"]) : "";
                    $response["result"] = $this->opinion_workflow_status_transition_permission->save_value($permissions);
                }
                $screen_fields = $this->input->post("screen_fields");
                if ($screen_fields) {
                    $this->opinion_workflow_status_transition_screen_field->set_field("transition", $transition_id);
                    $this->opinion_workflow_status_transition_screen_field->set_field("data", serialize($screen_fields));
                    if (!$this->opinion_workflow_status_transition_screen_field->insert()) {
                        $response["validation_errors"] = $this->opinion_workflow_status_transition_screen_field->get("validationErrors");
                        $response["result"] = false;
                    }
                }
            } else {
                $response["validation_errors"] = $this->opinion_workflow_status_transition->get("validationErrors");
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    public function edit_transition($transition_id)
    {
        if (!$this->input->is_ajax_request()) {
            if (!$transition_id || !$this->opinion_workflow_status_transition->fetch($transition_id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("opinion_workflows");
            }
            $workflow_id = $this->opinion_workflow_status_transition->get_field("workflow_id");
            $data = $this->load_common_data();
            $data["transition"] = $this->opinion_workflow_status_transition->get_fields();
            $this->opinion_status->fetch($data["transition"]["from_step"]);
            $data["from_step_name"] = $this->opinion_status->get_field("name");
            $data["workflow_id"] = $workflow_id;
            $data["to_steps"] = $this->opinion_status->load_allowed_to_statuses($data["transition"]["from_step"], $workflow_id, $data["transition"]["to_step"]);
            $permissions = $this->opinion_workflow_status_transition_permission->load_permissions($transition_id);
            if (!empty($permissions)) {
                foreach ($permissions as $value) {
                    if (!empty($value["users"])) {
                        foreach (explode(",", $value["users"]) as $id) {
                            $data["users_permitted"][] = $this->user->get_name_by_id($id);
                        }
                    }
                    if (!empty($value["user_groups"])) {
                        foreach (explode(",", $value["user_groups"]) as $id) {
                            $data["user_groups_permitted"][] = $this->user_group->get_name_by_id($id);
                        }
                    }
                }
            }
            if ($this->opinion_workflow_status_transition_screen_field->fetch(["transition" => $transition_id])) {
                $data["selected_fields"] = unserialize($this->opinion_workflow_status_transition_screen_field->get_field("data"));
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
            $this->includes("scripts/opinion_transitions", "js");
            $this->load->view("partial/header");
            $this->load->view("opinion_workflows/transition_form", $data);
            $this->load->view("partial/footer");
        } else {
            $response["result"] = true;
            $this->opinion_workflow_status_transition->set_fields($this->input->post(NULL));
            if ($this->opinion_workflow_status_transition->update()) {
                $permissions = $this->input->post("permissions");
                if ($permissions) {
                    $permissions["transition"] = $transition_id;
                    $permissions["users"] = isset($permissions["users"]) ? implode(",", $permissions["users"]) : "";
                    $permissions["user_groups"] = isset($permissions["user_groups"]) ? implode(",", $permissions["user_groups"]) : "";
                    $this->opinion_workflow_status_transition_permission->save_value($permissions);
                } else {
                    $this->opinion_workflow_status_transition_permission->delete_transition_permission($transition_id);
                }
                $screen_fields = $this->input->post("screen_fields");
                if ($screen_fields) {
                    $screen_fetched = $this->opinion_workflow_status_transition_screen_field->fetch(["transition" => $transition_id]);
                    $this->opinion_workflow_status_transition_screen_field->set_field("transition", $transition_id);
                    $this->opinion_workflow_status_transition_screen_field->set_field("data", serialize($screen_fields));
                    $screen_fields_result = $screen_fetched ? $this->opinion_workflow_status_transition_screen_field->update() : $this->opinion_workflow_status_transition_screen_field->insert();
                    if (!$screen_fields_result) {
                        $response["validation_errors"] = $this->opinion_workflow_status_transition_screen_field->get("validationErrors");
                        $response["result"] = false;
                    }
                } else {
                    $this->opinion_workflow_status_transition_screen_field->delete(["where" => [["transition", $transition_id]]]);
                }
            } else {
                $response["validation_errors"] = $this->opinion_workflow_status_transition->get("validationErrors");
                $response["result"] = false;
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }
    private function load_common_data()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("status_transition"));
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["users_list"] = $this->user->load_available_list();
        $this->load->model("user_group", "user_groupfactory");
        $this->user_group = $this->user_groupfactory->get_instance();
        $data["user_groups_list"] = $this->user_group->load_available_list();
        $this->load->model("opinion_fields", "opinion_fieldsfactory");
        $this->opinion_fields = $this->opinion_fieldsfactory->get_instance();
        $data["screen_fields"] = $this->opinion_fields->fields;
        return $data;
    }
    public function set_as_start_point($workflow, $status)
    {
        if (!$this->input->is_ajax_request() || !$status || !$workflow) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard");
        }
        $response = [];
        if ($this->opinion_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status])) {
            $this->opinion_workflow_status_relation->fetch(["workflow_id" => $workflow, "start_point" => 1]);
            $this->opinion_workflow_status_relation->set_field("start_point", 0);
            $this->opinion_workflow_status_relation->update();
            $this->opinion_workflow_status_relation->fetch(["workflow_id" => $workflow, "status_id" => $status]);
            $this->opinion_workflow_status_relation->set_field("start_point", 1);
            $response["result"] = $this->opinion_workflow_status_relation->update() ? true : false;
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
                if (0 < count($this->opinion_workflow->load_status_transitions($status_id, $workflow_id))) {
                    $data = [];
                    $data["status_id"] = $status_id;
                    $data["workflow_id"] = $workflow_id;
                    $response["result"] = "FOREIGN_KEY_CONSTRAINT";
                    $response["html"] = $this->load->view("opinion_workflows/delete_status_error_modal", $data, true);
                } else {
                    if ($this->opinion_workflow_status_relation->delete(["where" => [["status_id", $status_id], ["workflow_id", $workflow_id]]])) {
                        $this->opinion_workflow_status_relation->fetch(["workflow_id" => $workflow_id, "start_point" => 1]);
                        $start_point = $this->opinion_workflow_status_relation->get_field("status_id");
                        $this->opinion_workflow_status_relation->move_opinions_to_start_point_status($status_id, $start_point, $workflow_id);
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
        if (!$transition_id || !$this->opinion_workflow_status_transition->fetch($transition_id)) {
            $response["result"] = false;
        }
        $this->opinion_workflow_status_transition_permission->delete_transition_permission($transition_id);
        $this->opinion_workflow_status_transition_screen_field->delete(["where" => ["transition", $transition_id]]);
        if ($this->opinion_workflow_status_transition->delete($transition_id)) {
            $response["result"] = true;
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function delete_workflow($workflow_id)
    {
        if ($this->opinion_workflow->fetch($workflow_id) && $this->opinion_workflow->get_field("type") != "system") {
            if ($this->opinion_workflow->delete_workflow($workflow_id)) {
                $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            } else {
                $this->set_flashmessage("error", sprintf($this->lang->line("delete_workflow_status_failed"), $this->lang->line("opinion")));
            }
        } else {
            $this->set_flashmessage("error", $this->lang->line("failed_workflow_system_delete"));
        }
        redirect("opinion_workflows/index/" . $workflow_id);
    }
    public function view_status_transitions($status_id, $workflow_id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $data = [];
        $this->opinion_status->fetch($status_id);
        $data["from_step_name"] = $this->opinion_status->get_field("name");
        $data["transitions"] = $this->opinion_workflow->load_all_transitions_per_workflow($workflow_id, $status_id);
        $response["html"] = $this->load->view("opinion_workflows/view_status_transitions", $data, true);
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
                if (!$this->opinion_workflow->update_opinion_statuses_workflow($data)) {
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