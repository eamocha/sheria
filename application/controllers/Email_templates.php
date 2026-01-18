<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";

class Email_templates extends Core_controller
{
    protected $email_template;

    public function __construct()
    {
        parent::__construct();

        $this->load->model("email_template", "email_templatefactory");
        $this->email_template = $this->email_templatefactory->get_instance();

    }

    public function index()
    {
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("email_templates"));
$data=array();
$data['templates']=$this->email_template->load_all_templates();

        $this->load->view("partial/header");
        $this->load->view("email_templates/index",$data);
        $this->load->view("partial/footer");
    }
    public function load_template_details($id)
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->email_template->fetch($id);
        $record = $this->email_template->get_fields();

        $this->output->set_content_type("application/json");
        if ($record) {

            $this->output->set_output(json_encode($record));
        } else {
            $this->output->set_status_header(404);
            $this->output->set_output(json_encode(["error" => "Template not found"]));
        }
    }


    public function add()
    {

        $this->save(0);
    }


    public function edit($id = "0")
    {
        $this->save($id);
    }


    private function save($id = 0)
    {
        $data = [];

        if ($id > 0) {
            $record = $this->email_template->load(["where" => ["id" => $id]]);
            if (!$record) {
                $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                redirect("email_templates");
            }
            $data["record"] = $record;
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("edit_email_template"));
        } else {
            // New record details
            $data["record"] = $this->email_template->create_object();
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("add_email_template"));
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();


            $save_data = [
                "template_name"  => $post_data["template_name"],
                "subject"        => $post_data["subject"],
                "body_content"   => $post_data["body_content"],
                "is_active"      => isset($post_data["is_active"]) ? 1 : 0,
                // Ensure the template key is only set/updated for a new record, not edited
                "template_key"   => ($id == 0) ? $post_data["template_key"] : $data["record"]["template_key"],
            ];

            // 2. Variable Count Validation (Critical Logic)
            $variable_count = substr_count($save_data["body_content"], "%s");
            $save_data["variable_count"] = $variable_count;

            // 3. Save Attempt
            if ($this->email_template->save($save_data, $id)) {
                $message = ($id > 0) ? $this->lang->line("record_updated") : $this->lang->line("record_added");
                $this->set_flashmessage("success", $message);
                redirect("email_templates");
            }

            // If validation or save fails, reload the form with input data and errors
            $data["record"] = array_merge($data["record"], $save_data);
        }

        // Load the view for editing/adding
        $data["fb"] = $this->session->flashdata("fb");
        $data["errors"] = $this->email_template->get_errors();
        $this->load->view("partial/header");
        $this->load->view("email_templates/edit", $data);
        $this->load->view("partial/footer");
    }
    public function save_template($id)
    {
        if (!$this->input->is_ajax_request() || $id == 0) {
            show_404();
        }
        $response = ["success" => false, "errors" => [], "message" => "Unable to process request."];
        $post_data = $this->input->post();
        $this->load->helper("revert_comment_html");
        $this->load->helper("format_comment_patterns");
        $body_content = $this->input->post("body_content", true, "</p></b><h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>");
        $post_data["body_content"]= $_POST["body_content"] = format_comment_patterns($this->regenerate_note($body_content));

       // $post_data["body_content"]="test";$this->input->post("body_content", FALSE); //$this->input->post("body_content", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>") ;
       if ($this->email_template->fetch($id)) {
           $save_data = [
               // "template_name"  => $post_data["template_name"],
               "subject" => $post_data["subject"],
               "body_content" => $post_data["body_content"],
               "is_active" => isset($post_data["is_active"]) ? 1 : 0,
               "variable_count" => (int)$post_data["variable_count"],
           ];

           $this->email_template->set_fields($post_data);
$this->email_template->validate();
           if ($this->email_template->update()) {
               $response["success"] = true;
               $response["message"] = $this->lang->line("record_updated");
           } else {
               $response["errors"] = $this->email_template->get("validationErrors");
               $response["message"] = $this->lang->line("save_failed"). " Make sure all fields are filled correctly.";
           }
       }

        $this->output->set_content_type("application/json");
        $this->output->set_output(json_encode($response));
    }
}