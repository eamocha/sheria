<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class Case_types extends Core_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("case_type");
    }
    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("case_types"));
        $data["records"] = $this->case_type->load_all_data();
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("case_types/index", $data);
    }
    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_case_type");
                $data["field_label"] = $this->lang->line("type");
                $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
            }
            if ($this->input->post(NULL)) {
                if ($this->input->post("caseType")) {
                    if ($this->input->post("caseType") == "litigation") {
                        $this->case_type->set_field("litigation", "yes");
                        $this->case_type->set_field("corporate", "no");
                        $this->case_type->set_field("criminal", "no");
                    } elseif ($this->input->post("caseType") == "corporate") {
                        $this->case_type->set_field("litigation", "no");
                        $this->case_type->set_field("corporate", "yes");
                        $this->case_type->set_field("criminal", "no");
                    } else{
                        $this->case_type->set_field("litigation", "no");
                        $this->case_type->set_field("corporate", "no");
                        $this->case_type->set_field("criminal", "yes");
                    }
                } else {
                    $this->case_type->set_field("litigation", "yes");
                    $this->case_type->set_field("corporate", "yes");
                    $this->case_type->set_field("criminal", "yes");
                }
                $this->case_type->set_field("name", trim($this->input->post("name")));
                $this->case_type->set_field("isDeleted", "0");
                if (!$this->case_type->insert()) {
                    $response["validationErrors"] = $this->case_type->get("validationErrors");
                }
                $response["id"] = $this->case_type->get_field("id");
                $response["name"] = $this->input->post("name");
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->save(0);
        }
    }
    public function edit($id = "0")
    {
        $this->save($id);
    }
    private function save($id = "0")
    {
        $data = [];
        if ($this->input->post(NULL)) {
            $_POST["litigation"] = $this->input->post("litigation") == "yes" ? "yes" : "no";
            $_POST["corporate"] = $this->input->post("corporate") == "yes" ? "yes" : "no";
            $_POST["criminal"] = $this->input->post("criminal") == "yes" ? "yes" : "no";
            $_POST["litigationSLA"] = $this->input->post("litigationSLA") != "" ? $this->input->post("litigationSLA") : NULL;
            if ($this->input->post("litigation") == "no" && $this->input->post("criminal") == "no" && $this->input->post("corporate") == "no" && $this->input->post("name")) {
                $this->set_flashmessage("information", $this->lang->line("case_type_flags_required_msg"));
                redirect("case_types");
            } else {
                $_POST["isDeleted"] = 0;
                $this->case_type->set_fields($this->input->post(NULL));
                if (empty($id)) {
                    $result = false;
                    $new_id = $this->case_type->add_case_type($this->input->post(NULL));
                    if (isset($new_id)) {
                        $result = true;
                        $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
                        $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
                        $this->case_types_due_condition->insert_update_records($new_id, $this->input->post("conditions_data"));
                    }
                } else {
                    $numRows = $this->case_type->count_field_rows("customer_portal_screens", "case_type_id", $id);
                    if (0 < $numRows && $this->input->post("corporate") == "no") {
                        $this->set_flashmessage("warning", sprintf($this->lang->line("edit_case_type_failed_related_screen"), $this->lang->line("portal_screens")));
                        redirect("case_types/index");
                    }
                    $result = $this->case_type->update();
                    $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
                    $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
                    $this->case_types_due_condition->insert_update_records($id, $this->input->post("conditions_data"));
                    if ($result) {
                        $mv_data = ["where" => ["area_of_practice" => $id], "dataset" => ["areaOfPractice" => $this->case_type->get_field("name")]];
                        $this->case_type->refresh_mv_data("mv_hearings", $mv_data);
                    }
                }
                if ($result) {
                    $this->set_flashmessage("success", $this->lang->line("record_saved"));
                    redirect("case_types/index");
                } else {
                    if ($this->case_type->is_valid()) {
                        $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                        redirect("case_types/index");
                    }
                }
            }
        } else {
            $this->case_type->fetch($id);
            $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
            $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
            $case_type_conditions = $this->case_types_due_condition->get_conditions_per_case_type($id);
            $type_name = $this->case_type->get_field("name");
            $static_case_type = $this->case_type->get("caseTypeOfIP");
            if ($type_name === $static_case_type) {
                redirect("case_types/index");
            }
        }
        $this->load->model("task", "taskfactory");
        $this->task = $this->taskfactory->get_instance();
        $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
        $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
        $data["case_type_due_conditions"] = $this->case_types_due_condition->get_conditions_per_case_type($id);
        $data["priorities"] = array_combine($this->task->get("priorityValuesSla"), [$this->lang->line("choose_all"), $this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("app4legal_browser_title"), $this->lang->line("case_types"));
        $this->load->view("case_types/form", $data);
    }
    public function get_case_type_due_conditions($case_type_id)
    {
        $this->load->model("case_types_due_condition", "case_types_due_conditionfactory");
        $this->case_types_due_condition = $this->case_types_due_conditionfactory->get_instance();
        return $this->case_types_due_condition->get_conditions_per_case_type($case_type_id);
    }
    public function delete($id)
    {
        $this->case_type->fetch($id);
        $type_name = $this->case_type->get_field("name");
        $static_case_type = $this->case_type->get("caseTypeOfIP");
        if ($type_name === $static_case_type) {
            redirect("case_types/index");
        }
        $check_workflow = $this->case_type->count_rows_table("workflow_case_types", ["case_type_id" => $id, "isDeleted" => 0], ["workflows", "workflow_case_types.workflow_id = workflows.id"]);
        if (0 < $check_workflow) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("workflow")));
            redirect("case_types/index");
        }
        $numRows = $this->case_type->count_field_rows("customer_portal_screens", "case_type_id", $id);
        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("portal_screens")));
            redirect("case_types/index");
        }
        $cases_not_deleted = $this->case_type->count_rows_table("legal_cases", ["case_type_id" => $id, "isDeleted" => 0]);
        if (0 < $cases_not_deleted) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed_Case"), $this->lang->line("the_case")));
            redirect("case_types/index");
        }
        if ($this->case_type->soft_delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
        }
        redirect("case_types/index");
    }
    public function get_case_types_by_case_category()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $where = NULL;
        $category = $this->input->post("category");
        if (!empty($category)) {
            if ($category == "Matter") {
                $where = [["corporate", "yes"], ["isDeleted", 0]];
            } else {
                if ($category == "Litigation") {
                    $where = [["litigation", "yes"], ["isDeleted", 0]];
                }
                if ($category == "criminal") {
                    $where = [["criminal", "yes"], ["isDeleted", 0]];
                }
            }
        } else {
            $where = "(litigation='yes' or corporate='yes',or criminal='yes') and isDeleted=0";
        }
        $response["case_types"] = $this->case_type->get_all_case_types_by_condition($where);
        if (!empty($response["case_types"])) {
            $response["result"] = true;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
}

?>