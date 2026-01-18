<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";

class Contract_templates extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_template", "contract_templatefactory");
        $this->contract_template = $this->contract_templatefactory->get_instance();
        $this->load->library("dmsnew");
    }

    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_templates"));
        $data["records"] = $this->contract_template->load_all();
        $this->includes("jquery/tinymce/tinymce.min", "js");
        $this->includes("contract/templates", "js");
        $this->load->view("partial/header");
        $this->load->view("contract_templates/index", $data);
        $this->load->view("partial/footer");
    }

    public function add()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_templates"));
        if (!$this->input->post(NULL)) {
            $data = $this->contract_template->load_data(strtolower(substr($this->session->userdata("AUTH_language"), 0, 2)));
            $this->includes("contract/templates", "js");
            $this->load->view("partial/header");
            $this->load->view("contract_templates/form", $data);
        } else {

            $response = $this->contract_template->save_data();
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    public function edit($id = 0)
    {
        if (!$this->input->post(NULL)) {
            if (!$this->contract_template->fetch($id)) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("contract_templates");
            }
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("contract_templates"));
            $data = $this->contract_template->load_data(strtolower(substr($this->session->userdata("AUTH_language"), 0, 2)));
            $data["records"] = $this->contract_template->load_template_data($id);
            $this->includes("contract/templates", "js");
            $this->load->view("partial/header");
            $this->load->view("contract_templates/form", $data);
        } else {
            $response = $this->contract_template->update_data();
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }
    }

    public function preview_template()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        if ($this->input->post(NULL)) {
            $response = $this->contract_template->validate_questionnaire();
            if ($response["result"]) {
                $data = $this->load_common_data();
                $data["data"] = $this->input->post();
                $data["data"]["pages"] = $response["pages_data"];
                $data["title"] = $this->lang->line("questionnaire_preview");
                $response["pages_count"] = count($this->input->post("pages"));
                $data["data"]["required_fields"] = $response["required_fields"];
                $data["channel"] = $this->web_channel;
                $data["end_date_is_in_variables"] = $response["end_date_is_in_variables"];
                $response["html"] = $this->load->view("contract_templates/questionnaire_preview", $data, true);
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function delete()
    {
        $response["result"] = false;
        if ($id = $this->input->post("id", true)) {
            $response["result"] = $this->contract_template->delete_relations($id);
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function show_hide_in_cp($id, $flag = 0)
    {
        $this->contract_template->fetch($id);
        $this->contract_template->set_field("show_in_cp", $flag);
        if ($this->contract_template->update()) {
            $this->set_flashmessage("information", $this->lang->line("updates_saved_successfully"));
            redirect("contract_templates/index");
        }
    }

    private function load_common_data()
    {
        $this->load->model(["provider_group"]);
        $data["assigned_teams"] = $this->provider_group->load_list([]);
        $this->load->model("reminder", "reminderfactory");
        $this->reminder = $this->reminderfactory->get_instance();
        $data["notify_me_before_time_types"] = array_combine($this->reminder->get("notify_me_before_time_type"), [$this->lang->line("day_or_days"), $this->lang->line("week_or_weeks"), $this->lang->line("month")]);
        $this->load->model("user", "userfactory");
        $this->user = $this->userfactory->get_instance();
        $data["users_list"] = $this->user->load_available_list();
        $users_emails = $this->user->load_active_emails();
        $data["users_emails"] = array_map(function ($users_emails) {
            return ["email" => $users_emails];
        }, array_keys($users_emails));
        $data["assigned_teams_list"] = $this->provider_group->load_all();
        $this->provider_group->fetch(["allUsers" => 1]);
        $data["assigned_team_id"] = $this->provider_group->get_field("id");
        return $data;
    }
}

?>