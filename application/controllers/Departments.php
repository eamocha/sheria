<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Top_controller.php";

class Departments extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("department");
    }

    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("departments"));

        $data["records"] = $this->department->paginate(["order_by" => ["name asc"]]);
        $data["fb"] = $this->session->flashdata("fb");
        $this->load->view("departments/index", $data);
    }

    public function add()
    {
        if ($this->input->is_ajax_request()) {
            $response = [];
            if ($this->input->get("quick_add_form")) {
                $data = [];
                $data["title"] = $this->lang->line("add_department");
                $data["fieldLabel"] = $this->lang->line("name");
                $data["component"] = "departments";

                if ($this->input->get("is_new_form")) {
                    $data["field_label"] = $this->lang->line("name");
                    $response["html"] = $this->load->view("administration/quick_dialog_form", $data, true);
                } else {
                    $response["html"] = $this->load->view("administration/onthefly_template", $data, true);
                }
                $response["isLayoutRTL"] = $this->is_auth->is_layout_rtl();
            }
            if ($this->input->post(NULL)) {
                $this->department->set_fields($this->input->post(NULL));
                if (!$this->department->insert()) {
                    $response["validationErrors"] = $this->department->get("validationErrors");
                }
                $response["id"] = $this->department->get_field("id");
                $response["name"] = $this->input->post("name");
            }

            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            // If not an AJAX request, call the save method for a full page form
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
            $this->department->set_fields($this->input->post(NULL));
            if (empty($id)) {
                $result = $this->department->insert(); // Insert new record
            } else {
                $result = $this->department->update(); // Update existing record
            }

            if ($result) {
                $this->set_flashmessage("success", $this->lang->line("record_saved"));
                redirect("departments/index"); // Redirect to department list on success
            } else {
                if ($this->department->is_valid()) {
                    $this->set_flashmessage("error", $this->lang->line("record_could_not_be_saved"));
                    redirect("departments/index");
                }
            }
        } else {
            $this->department->fetch($id);
        }

        $data["id"] = $id;
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("departments"));
        $this->load->view("departments/form", $data);
    }

    public function delete($id)
    {
        $this->load->model("user_profile","user_profilefactory");
        $this->user_profile=$this->user_profilefactory->get_instance();
        // Check if there are any related records (e.g., users belonging to this department)
        $numRows = $this->department->count_field_rows("user_profile", "department_id", $id);

        if (0 < $numRows) {
            $this->set_flashmessage("warning", sprintf($this->lang->line("delete_record_related_object_failed"), $this->lang->line("users"))); // Adjust language line
            redirect("departments/index");
        }
        if ($this->department->delete($id)) {
            $this->set_flashmessage("information", $this->lang->line("record_deleted"));
            redirect("departments/index");
        } else {
            $this->set_flashmessage("error", $this->lang->line("record_not_deleted"));
            redirect("departments/index");
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
        $response = $this->department->lookup($term);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
}