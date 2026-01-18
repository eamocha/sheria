<?php

require "Core_controller.php";
class Checkphpmodules extends Core_controller
{
    public $Legal_Case;
    public $defaultWorkflow = "";
    public $controller_name = "ldmis";
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "ldmis";

    }
    public function index()
    {
        $this->load->view("partial/header");
        $this->load->view("checkphpmodules/index");
        $this->load->view("partial/footer");

    }
    public function move_status($contract_id = 0, $status_id = 0)
    {
        $response = $this->responseData;
        $needs_approval = false;
        $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
        $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
        $this->load->model("contract_approval_submission", "contract_approval_submissionfactory");
        $this->contract_approval_submission = $this->contract_approval_submissionfactory->get_instance();
        $this->load->model("language");
        $lang = $this->user_preference->get_field("keyValue");
        $lang_id = $this->language->get_id_by_session_lang($lang);
        if (!$contract_id || !$status_id) {
            $response["error"] = "missing data";
        } else {
            if ($this->contract->fetch($contract_id) && $contract_id && $status_id) {
                $workflow_applicable = $this->contract->get_field("workflow_id");
                $this->load->model("contract_workflow_status_transition", "contract_workflow_status_transitionfactory");
                $this->contract_workflow_status_transition = $this->contract_workflow_status_transitionfactory->get_instance();
                $old_status = $this->contract->get_field("status_id");
                $type_id = $this->contract->get_field("type_id");
                $this->contract_workflow_status_transition->fetch(["workflow_id" => $workflow_applicable, "from_step" => $old_status, "to_step" => $status_id]);
                $transition_id = $this->contract_workflow_status_transition->get_field("id");
                if ($transition_id && $this->contract_workflow_status_transition->fetch($transition_id) && $this->contract_workflow_status_transition->get_field("approval_needed") && $this->contract_approval_submission->fetch(["contract_id" => $contract_id]) && $this->contract_approval_submission->get_field("status") !== "approved") {
                    $response["error"] = $this->lang->line("needs_approval_before");
                    $needs_approval = true;
                }
                if (!$needs_approval) {
                    $workflow_applicable = 0 < $this->contract->get_field("workflow_id") ? $this->contract->get_field("workflow_id") : ($allowed_statuses = []);
                    $allowed_statuses = $this->contract_workflow_status_transition->load_available_steps($old_status, $workflow_applicable, $lang_id);
                    if ($status_id === $old_status || !in_array($status_id, array_keys($allowed_statuses["available_statuses"]))) {
                        $response["error"] = $this->lang->line("permission_not_allowed");
                    } else {
                        $this->load->model("contract_fields", "contract_fieldsfactory");
                        $this->contract_fields = $this->contract_fieldsfactory->get_instance();
                        $this->contract_fields->load_all_fields($type_id);
                        $data = $this->contract_fields->return_screen_fields($contract_id, $transition_id, $lang_id);
                        if ($transition_id && $data) {
                            $data["title"] = $this->contract_workflow_status_transition->get_field("name");
                            $response["success"]["transition_id"] = $transition_id;
                            $response["success"]["data"] = $data;
                        } else {
                            $this->contract->fetch($contract_id);
                            $this->contract->set_field("status_id", $status_id);
                            if (!$this->contract->update()) {
                                $response["error"] = $this->lang->line("contract_move_status_invalid");
                                $response["validation_errors"] = $this->contract->get("validationErrors");
                            } else {
                                $this->load->model("approval", "approvalfactory");
                                $this->approval = $this->approvalfactory->get_instance();
                                $this->approval->workflow_status_approval_events($contract_id, $this->contract->get_field("workflow_id"), $status_id);
                                $this->load->model("contract_contributor", "contract_contributorfactory");
                                $this->contract_contributor = $this->contract_contributorfactory->get_instance();
                                $transitions_accessible = $this->contract_workflow_status_transition->load_available_steps($status_id, $this->contract->get_field("workflow_id"), $lang_id);
                                $response["available_statuses"] = $transitions_accessible["available_statuses"];
                                $response["status_transitions"] = $transitions_accessible["status_transitions"];
                                $response["contract"]["id"] = $contract_id;
                                $status = $this->contract_status->load_status_details($status_id);
                                $old_status_details = $this->contract_status->load_status_details($old_status);
                                $response["status_name"] = $status["status_name"];
                                $response["status_color"] = $status["status_color"];
                                $logged_in_user = $this->user_logged_in_data["user_id"];
                                $this->user_profile->fetch(["user_id" => $logged_in_user]);
                                $full_name = $this->user_profile->get_field("firstName") . " " . $this->user_profile->get_field("lastName");
                                $contributors = $this->contract_contributor->load_contributors($contract_id);
                                $notify["contributors"] = $contributors ? array_column($contributors, "id") : [];
                                $notify["logged_in_user"] = $full_name;
                                $this->load->model("contract_sla_management", "contract_sla_managementfactory");
                                $this->contract_sla_management = $this->contract_sla_managementfactory->get_instance();
                                $this->contract_sla_management->contract_sla($contract_id, $logged_in_user, 1);
                                $this->contract->send_notifications("edit_contract_status", $notify, ["id" => $contract_id, "status" => $status["status_name"], "old_status" => $old_status_details["status_name"]]);
                                if ($this->system_preference->get_values()["webhooks_enabled"] == 1) {
                                    $webhook_data = $this->contract->load_contract_details($contract_id);
                                    $this->contract->trigger_web_hook("contract_status_updated", $webhook_data);
                                }
                                $response["success"]["msg"] = sprintf($this->lang->line("status_updated_message"), $this->lang->line("contract"));
                            }
                        }
                    }
                }
            }
        }
        $this->render($response);
    }
}