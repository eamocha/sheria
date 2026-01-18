<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";
class Custom_fields extends Top_controller
{
    public $Custom_Field;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("custom_field", "custom_fieldfactory");
        $this->custom_field = $this->custom_fieldfactory->get_instance();
    }
    public function companies()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("company_custom_fields"));
        $this->display("company");
    }
    public function contacts()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contact_custom_fields"));
        $this->display("contact");
    }
    public function cases()
    {
        $this->authenticate_actions_per_license();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("case_custom_fields"));
        $this->display("legal_case");
    }
    private function display($model)
    {
        $data = [];
        $data["tmp"] = $this->custom_field->load_fields($model);
        $data["tmp1"] = [];
        $data["records"] = [];
        foreach ($data["tmp"] as $key => $val) {
            if (!array_key_exists($val["id"], $data["tmp1"])) {
                $data["tmp1"][$val["id"]] = $data["tmp"][$key];
            }
            $data["tmp1"][$val["id"]]["name_" . $val["langName"]] = $val["customName"];
            unset($data["tmp1"][$val["id"]]["customName"]);
            unset($data["tmp1"][$val["id"]]["langName"]);
        }
        $data["records"] = $data["tmp1"];
        $data["fb"] = $this->session->flashdata("fb");
        $data["model"] = $model;
        $data["controller"] = $this->custom_field->get_model_properties("model", $model, "controller");
        $this->load->model("language");
        $data["langCount"] = sizeof($this->language->load_all());
        $data["languages"] = $this->language->loadAvailableLanguages(true);
        $this->includes("scripts/custom_fields_grid", "js");
        $this->load->view("partial/header");
        $this->load->view("custom_fields/index", $data);
        $this->load->view("partial/footer");
    }
    public function add($model)
    {
        $this->save($model);
    }
    public function edit($model, $custom_field_id)
    {
        $this->save($model, $custom_field_id);
    }
    private function save($model, $custom_field_id = NULL)
    {
        $response = $data = [];
        $this->load->model(["custom_fields_language"]);
        if (!empty($custom_field_id) && !$this->custom_field->fetch($custom_field_id)) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("custom_fields/" . $model);
        }
        if (!$this->input->post(NULL)) {
            $data["custom_field_language_data"] = $this->custom_fields_language->load_custom_field_language_data($custom_field_id);
            $data["custom_types"] = $this->custom_field->get_types();
            $data["custom_field_data"] = $this->custom_field->get_fields();
            if ($data["custom_field_data"]["type"] == "lookup") {
                $data["custom_field_data"]["type"] = $data["custom_field_data"]["type"] . "_" . $data["custom_field_data"]["type_data"];
            }
            $data["model"] = $model;
       if ($model === "legal_case") {
                $this->load->model("case_type");
                $this->load->model("custom_fields_case_type", "custom_fields_case_typefactory");
                $this->custom_fields_case_type = $this->custom_fields_case_typefactory->get_instance();
                $data["model_category"] = explode(",", $data["custom_field_data"]["category"]);
                if ($custom_field_id) {
                    if ($data["custom_field_data"]["model"] == "legal_case") {
                        $where = ["(litigation='yes' or corporate='yes' or criminal='yes') and isDeleted=0"];
                    } else {
                        if ($data["custom_field_data"]["model"] == "matter") {
                            $where = [["corporate", "yes"], ["isDeleted", 0]];
                        } elseif ($data["custom_field_data"]["model"] == "criminal") {
                            $where = [["criminal", "yes"], ["isDeleted", 0]];
                        }else {
                            $where = [["litigation", "yes"], ["isDeleted", 0]];
                        }
                    }
                }
                $data["model_types"] = $custom_field_id ? $this->case_type->get_all_case_types_by_condition($where) : [];
                $data["categories"] = [["id" => "matter", "name" => $this->lang->line("corporate_matter")], ["id" => "litigation", "name" => $this->lang->line("litigation_case")],["id" => "criminal", "name" => $this->lang->line("criminal_case")]];
                $data["cf_model_types"] = $custom_field_id ? array_column($this->custom_fields_case_type->load_all(["where" => ["custom_field_id", $custom_field_id]]), "type_id") : [];
                $response["html_case_types"] = $this->load->view("custom_fields/types", $data, true);
            }
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("custom_fields"));
           $response["html"] = $this->load->view("custom_fields/form", $data, true);
        } else {
            $response = $this->custom_field->save_custom_field_data($custom_field_id, $model, $this->input->post(NULL));
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function delete($model, $id)
    {
        $response = [];
        $this->load->model(["custom_fields_language", "custom_field_value"]);
        $this->load->model("customer_portal_screen", "customer_portal_screenfactory");
        $this->load->model("grid_saved_column");
        $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();
        $valid = $model == "legal_case" ? $this->custom_field->check_field_relation_per_category_type($id) : $this->custom_field_value->check_field_relation($id);
        if ($valid) {
            if ($model == "legal_case") {
                if (!$this->customer_portal_screen->check_field_cp_relation($id)) {
                    $response["feedbackMessage"] = ["ty" => "warning", "m" => $this->lang->line("delete_record_related_screen_cp_failed")];
                    $response["result"] = false;
                } else {
                    $this->load->model("workflow_status_transition_screen_field", "workflow_status_transition_screen_fieldfactory");
                    $this->workflow_status_transition_screen_field = $this->workflow_status_transition_screen_fieldfactory->get_instance();
                    if (!$this->workflow_status_transition_screen_field->check_field_relation($id)) {
                        $response["feedbackMessage"] = ["ty" => "warning", "m" => $this->lang->line("record_related_workflow_screen")];
                        $response["result"] = false;
                    }
                }
            }
            if (empty($response)) {
                if ($this->custom_fields_language->delete(["where" => [["custom_field_id", $id]]])) {
                    $this->custom_field_value->delete(["where" => [["custom_field_id", $id]]]);
                    $this->load->model("custom_fields_case_type", "custom_fields_case_typefactory");
                    $this->custom_fields_case_type = $this->custom_fields_case_typefactory->get_instance();
                    $this->custom_fields_case_type->delete(["where" => [["custom_field_id", $id]]]);
                    if ($this->custom_field->delete($id)) {
                        if ($model == "task" || $model == "company") {
                            $saved_column_details = $this->grid_saved_column->get_grid_saved_column($model);
                            foreach ($saved_column_details as $index => $column) {
                                if (isset($column["grid_details"])) {
                                    $grid_saved_details = unserialize($column["grid_details"]);
                                    $custom_field_index = array_search("custom_field_" . $id, empty($grid_saved_details["selected_columns"]) ? [] : $grid_saved_details["selected_columns"]);
                                    $custom_field_index = !empty($grid_saved_details["selected_columns"]) ? array_search("custom_field_" . $id, $grid_saved_details["selected_columns"]) : false;
                                    if ($custom_field_index) {
                                        unset($grid_saved_details["selected_columns"][$custom_field_index]);
                                    }
                                    $saved_columns_array_sorts = explode("},{", substr($grid_saved_details["sort"], 2, -2));
                                    foreach ($saved_columns_array_sorts as $index => $sort) {
                                        if (strpos($sort, "custom_field_" . $id)) {
                                            unset($saved_columns_array_sorts[$index]);
                                        }
                                    }
                                    $resorted = "[{" . implode("},{", $saved_columns_array_sorts) . "}]";
                                    $grid_saved_details["sort"] = $resorted;
                                    $serialized_details = serialize($grid_saved_details);
                                    $this->grid_saved_column->fetch($column["id"]);
                                    $this->grid_saved_column->set_field("grid_details", $serialized_details);
                                    $this->grid_saved_column->update();
                                }
                            }
                        }
                        $response["feedbackMessage"] = ["ty" => "information", "m" => $this->lang->line("record_deleted")];
                        $response["result"] = true;
                    } else {
                        $response["feedbackMessage"] = ["ty" => "error", "m" => $this->lang->line("record_not_deleted")];
                        $response["result"] = false;
                    }
                } else {
                    $response["feedbackMessage"] = ["ty" => "error", "m" => $this->lang->line("record_not_deleted")];
                    $response["result"] = false;
                }
            }
        } else {
            $response["feedbackMessage"] = ["ty" => "warning", "m" => sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line($model))];
            $response["result"] = false;
        }
        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
    public function set_fields_order()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            $response["result"] = $this->custom_field->set_fields_order($this->input->post("fields_order_data"));
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            show_404();
        }
    }
    public function company_asset()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("company_asset_custom_fields"));
        $this->display("company_asset");
    }
    public function tasks()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("task_custom_fields"));
        $this->display("task");
    }
    public function opinions()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("opinion_custom_fields"));
        $this->display("opinion");
    }
    public function conveyancing()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_custom_fields"));
        $this->display("conveyancing");
    }
    public function get_practice_area_by_category()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $response = [];
        $where = NULL;
        $category = $this->input->post("category");
        if ($category && count($category) == 1) {
            if (isset($category[0]) && $category[0] == "matter") {
                $where = [["corporate", "yes"], ["isDeleted", 0]];
            }elseif (isset($category[0]) && $category[0] == "criminal") {
                $where = [["criminal", "yes"], ["isDeleted", 0]];
            } else {
                $where = [["litigation", "yes"], ["isDeleted", 0]];
            }
        } else {
            $where = ["(litigation='yes' or corporate='yes' or criminal='yes' ) and isDeleted=0"];
        }
        $this->load->model("case_type");
        $data["model_types"] = $this->case_type->get_all_case_types_by_condition($where);
        if (!empty($data["model_types"])) {
            $response["result"] = true;
            $response["html"] = $this->load->view("custom_fields/types", $data, true);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    public function is_cf_used()
    {
        $id = $this->input->post("id", true);
        if (!$id || !$this->custom_field->fetch($id) || !$this->input->is_ajax_request()) {
            show_404();
        }
        $valid = true;
        $changed = $this->custom_field->is_custom_field_model_details_changed($id, $this->custom_field->get_field("category"));
        if ($changed) {
            $this->load->model("customer_portal_screen", "customer_portal_screenfactory");
            $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();
            $old_types = array_column($this->custom_fields_case_type->load_all(["where" => ["custom_field_id", $id]]), "type_id");
            $new_types = $this->input->post("model_type", true);
            $removed_types = array_diff($old_types, $new_types);
            $valid = $this->custom_field->return_field_value_relation($id, implode(",", $this->input->post("category", true)), $removed_types);
            if (!$valid) {
                if (!$this->customer_portal_screen->check_field_cp_relation($id)) {
                    $response["display_message_key"] = "cf_validation_used_in_matter_and_cp";
                } else {
                    $response["display_message_key"] = "cf_validation_used_in_matter";
                }
            } else {
                if (!$this->customer_portal_screen->check_field_cp_relation($id)) {
                    $valid = false;
                    $response["display_message_key"] = "cf_validation_used_in_cp";
                }
            }
        }
        $response["result"] = $valid ? true : false;
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}

?>