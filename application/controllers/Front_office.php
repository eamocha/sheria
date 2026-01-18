<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require "Top_controller.php";
class Front_office extends Top_controller {

    public function __construct() {
        parent::__construct();

       
        $this->load->model("correspondence", "correspondencefactory");
        $this->correspondence = $this->correspondencefactory->get_instance();
        $this->load->model("correspondence_activity_log","correspondence_activity_logfactory");
        $this->correspondence_activity_log=$this->correspondence_activity_logfactory->get_instance();
        $this->load->model("correspondence_type","correspondence_typefactory");
        $this->correspondence_type= $this->correspondence_typefactory->get_instance();
        $this->load->model("correspondence_status","correspondence_statusfactory");
        $this->correspondence_status= $this->correspondence_statusfactory->get_instance();
           $this->load->model("correspondence_document_type","correspondence_document_typefactory");
        $this->correspondence_document_type= $this->correspondence_document_typefactory->get_instance();

        //  $this->load->model('File_model');        // For file data
     //   $this->load->model('Task_model');        // For task data
    }
    public function autocomplete()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $search = $this->input->get('term');
        $data = $this->correspondence->lookup($search);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

public function dashboard()
{
    $this->authenticate_exempted_actions();
    if (!$this->is_auth->is_logged_in()) {
        redirect("users/login");
    }

        // Fetch data for the dashboard
//        $data['incoming_this_month'] = $this->correspondence->get_incoming_count(date('Y-m'));
//        $data['outgoing_this_month'] = $this->correspondence->get_outgoing_count(date('Y-m'));
//        $data['files_checked_out']   = rand(0,8);//$this->File->get_checked_out_count();
//        $data['overdue_tasks']       = rand(1,10);//$this->Task->get_overdue_tasks();
//        $data['pending_signature_review_incoming'] = $this->correspondence->get_pending_signature_review_count('incoming');
//        $data['pending_dispatch_outgoing'] = $this->correspondence->get_pending_dispatch_count();
//        $data['pending_tasks']       = rand(1,7);//$this->Task_model->get_pending_tasks($this->session->userdata('user_id')); // Pass user ID
//        $data['recent_activity']     = $this->correspondence->get_recent_activity();

        $data=[];
    $data["types"] = $this->correspondence_type->load_list([],["firstLine" => ["0" => "All "]]);

        $this->includes("jquery/apexcharts/apexcharts.min","js");
        $this->includes("jquery/apexcharts/polyfill.min","js");
        // Load the dashboard view
        $this->load->view('partial/header');
        $this->load->view('front_office/dashboard', $data);
        $this->load->view('partial/footer');
    }
    public function incoming()
    {
        $correspondences = $this->correspondence->get_incoming_correspondence();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "data" => $correspondences,
                "total" => count($correspondences)
            ]));
    }
    public function outgoing()
    {
        $correspondences=$this->correspondence->get_outgoing_correspondence();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "data" => $correspondences,
                "total" => count($correspondences)
            ]));
    }
    public function report()
    {
        $this->load->view('partial/header');
        $this->load->view('front_office/report');
        $this->load->view('partial/footer');
    }
    public function index($view="incoming") {
        $data['title'] = ucfirst($view).' Correspondences';
        $data["correspondences"]= $view=="incoming" ? $this->correspondence->get_incoming_correspondence() : $this->correspondence->get_outgoing_correspondence();
        $data["model"]="correspondence";
        $this->load->model("grid_saved_column");

       // $grid_details = $this->grid_saved_column->get_user_grid_details($data["model"]);

        $data["statuses"]=[];


        $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");

        $this->includes("frontOffice/js/".$view."_correspondence_grid", "js");
            $this->includes("frontOffice/css/correspondence-incoming-list", "css");

        $this->includes("scripts/advance_search_custom_field_template", "js");
        $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
        $this->includes("jquery/timemask", "js");



        $this->load->view('partial/header');
        $this->load->view('front_office/'.$view, $data);
        $this->load->view('partial/footer');
    }

    public function view($id)
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        if (!$id || !is_numeric($id)) {
            redirect("front_office");
        }
        $data=[];
        $data['correspondence'] = $this->correspondence->get_correspondence_by_id($id);

        $this->includes("frontOffice/css/front-office-view","css");

        $this->includes("frontOffice/js/correspondence_workflow","js");
        $this->includes("frontOffice/js/correspondence_view","js");
        $type_id=$data['correspondence']["correspondence_type_id"];

        $data['workflow_steps']=$this->correspondence->get_workflow_processes_by_correspondence_type($id,$type_id);

        $this->load->view('partial/header');
        $this->load->view("front_office/view/correspondence-item",$data);
        $this->load->view('partial/footer');

    }
    public function get_status_options()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data["statusOptions"]= $this->correspondence_status->load_list();
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data)); 

    }
    public function get_correspondence_types()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $data["types"] = $this->correspondence_type->load_list([],["firstLine" => ["0" => "All "]]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function get_correspondence_lookup()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $search = $this->input->post("search");
        $data["correspondences"] = $this->correspondence->get_correspondence_lookup($search);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function get_document_types_options()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        $this->load->model("correspondence_document_type", "correspondence_document_typefactory");
        $this->correspondence_document_type = $this->correspondence_document_typefactory->get_instance();
        $data["document_types"] = $this->correspondence_document_type->load_list([],["firstLine" => ["0" => "All "]]);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }
    public function get_activities_log($id)
    { if (!$this->input->is_ajax_request()) {
        show_404();
    }
    $data= $this->correspondence->get_correspondence_activity_logs($id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));

    }
    public function get_timeline()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
       $id=(int)$this->input->post("id");
       $type_id=(int)$this->input->post("correspondence_type_id");
       $workflow_steps=$this->correspondence->get_workflow_processes_by_correspondence_type($id,$type_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode([
                "workflow_steps" => $workflow_steps
            ]));

    }
    public function update_workflow_step($correspondence_id=0,$correspondence_type_id=0)
    {
        if (!$this->input->is_ajax_request())
        {
        show_404();
        }
        $this->load->model("correspondence_workflow_step", "correspondence_workflow_stepfactory");
        $this->correspondence_workflow_step = $this->correspondence_workflow_stepfactory->get_instance();
        $this->load->model("correspondence_workflow","correspondence_workflowfactory");
        $this->correspondence_workflow=$this->correspondence_workflowfactory->get_instance();


        if(!$this->input->post(null)) {

            $data["stages"] = $this->correspondence_workflow_step->load_list(["where" => [["correspondence_type_id", $correspondence_type_id]], "order_by" => ["sequence_order", "asc"]]);
            $response["result"] = true;
            $response["html"] = $this->load->view("front_office/view/timeline_update_form", $data, true);
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }else {
            //update thestage_progress table
            $workflow_step_id=$this->input->post("stage_id" );
            $correspondence_id=$this->input->post("correspondence_id");
            $correspondence_type_id=$this->input->post("correspondence_type_id");
            $dataSet=array("correspondence_id" =>$correspondence_id , "status" => $this->input->post("status"),"workflow_step_id"=>$workflow_step_id,"comments"=>$this->input->post("details" ),
                "modifiedBy"=>$this->is_auth->get_user_id(), "completion_date"=>$this->input->post("status")=="completed"?date("Y-m-d H:i" ):null);
            $keys = ['correspondence_id', 'workflow_step_id'];
            if($this->correspondence_workflow->insert_on_duplicate_key_update($dataSet,$keys)){
                $this->correspondence_workflow->reset_fields();
                //if completed, updated activities table and set the next stage current if it is not the last step

                $activitiesTableFields=["correspondence_id"=>$correspondence_id, "user_id"=>$this->is_auth->get_user_id(), "createdBy"=>$this->is_auth->get_user_id(), "createdOn"=>date("Y-m-d H:i:s"), "action"=>"Workflow timeline update ",
                    "details"=>"Status: '".$this->input->post("status")."', in the '". $this->input->post("stageText")."' Stage. Remarks: ". $this->input->post("details")];
                $this->correspondence_activity_log->set_fields($activitiesTableFields);
                if($this->correspondence_activity_log->insert()){
                    $response["result"]= true;
                }else{
                    $response["validationErrors"] = $this->correspondence_activity_log->get("validationErrors");
                    $response["result"]= false;
                }

                if ($this->input->post("status")=="completed"){ //if in completed status, then update appropriately
                    $next_stage_id = $this->correspondence_workflow_step->get_next_stage_id($workflow_step_id,$correspondence_type_id);
                    if ($next_stage_id) {
                        //insert the next stage to db
                        $nextStepFieldsToSet=["correspondence_id"=>$correspondence_id, "workflow_step_id"=>$next_stage_id,"status"=>"current", "createdBy"=>$this->is_auth->get_user_id(),"createdOn"=>date("Y-m-d H:i:s")];
                        $this->correspondence_workflow->insert_on_duplicate_key_update($nextStepFieldsToSet,$keys);
                        //update activity table

                        $response["result"]=true;
                    } else {
                        $this->correspondence_status->fetch(["name" => "Completed"]);
                        $last_status_id = $this->correspondence_status->get_field("id")??0;


                        $update_data =[ "status_id" => $last_status_id, "modifiedOn" => date("Y-m-d H:i:s")];
                        $conditions = ['id' => $correspondence_id];
                        $result = $this->correspondence->update($update_data, false, false, $conditions);

                        if(!$result){
                            $response["validationErrors"] = $this->correspondence->get("validationErrors");
                        } else {
                            $response["result"] = true;
                            $response["final_stage_reached"] = $this->lang->line("completed");
                        }
                        //update the activity table
                    }
                }//end status completed actions

            }
            ///end if inserted
            $this->output->set_content_type("application/json")->set_output(json_encode($response));

        }
    }


    public function add()
    {
        if (!$this->input->is_ajax_request()) {
        show_404();
    }
       if (!$this->input->post(null)) {
           $data["type_options"] = $this->correspondence_type->load_list();

           $data["action_required_options"] = array_combine($this->correspondence->get("actionRequiredValues"), [$this->lang->line("review"), $this->lang->line("sign"), $this->lang->line("draft"), $this->lang->line("action"),
               $this->lang->line("advice"),$this->lang->line("investigate"),$this->lang->line("respond"),$this->lang->line("diarize"),$this->lang->line("note"),$this->lang->line("other")]);

           $data["signature_options"] = array_combine($this->correspondence->get("requiresSignatureValues"), [$this->lang->line("yes"), $this->lang->line("no")]);
           $data["category_options"] = array_combine($this->correspondence->get("categoryValues"), [$this->lang->line("incoming"), $this->lang->line("outgoing")]);
           $data["priority_options"] = array_combine($this->correspondence->get("priorityValues"), [$this->lang->line("critical"), $this->lang->line("high"), $this->lang->line("medium"), $this->lang->line("low")]);
           $data["relatedTo_options"] = array_combine($this->correspondence->get("relatedToObjectValues"), [$this->lang->line("civil_case"), $this->lang->line("criminal_case"), $this->lang->line("legal_matter"), $this->lang->line("opinion"), $this->lang->line("contract"), $this->lang->line("agreement"), $this->lang->line("conveyancing"), $this->lang->line("correspondence")]);

           $data["status_options"] = $this->correspondence_status->load_list();     //['' => 'Select', 'Pending' => 'Pending', 'Closed' => 'Closed', 'Actioned' => 'Actioned'];
           $data["receipt_modes"] = ['' => 'Select', 'Hand Delivery' => 'Hand Delivery', 'Email' => 'Email', 'Courier' => 'Courier'];
           $data["dispatch_modes"] = ['' => 'Select', 'Post' => 'Post', 'Courier' => 'Courier', 'Email' => 'Email'];
           $data['dispatch_options'] = ['' => 'Select', 'Email' => 'Email', 'Courier' => 'Courier'];
           $this->load->model("correspondence_document_type", "correspondence_document_typefactory");
           $this->correspondence_document_type = $this->correspondence_document_typefactory->get_instance();
            // Load document types, first line has none.
              $data["document_type_options"] = $this->correspondence_document_type->load_list([]);
            //   $data["document_type_options"] = $this->correspondence_document_type->load_list();
            //default company
           $data["system_preferences"] = $this->session->userdata("systemPreferences");
           $data["defaultCompany_id"]=$data["system_preferences"]["defaultCompany"]??0;
           $this->load->model("company", "companyfactory"); //to get name of company
           $this->company = $this->companyfactory->get_instance();
           $this->company->fetch(["id" =>  $data["defaultCompany_id"]]);
           $data["defaultCompany_name"]= $this->company->get_field("name");

           $data['user_options'] = [];
           $data['next_ref_number'] = $this->correspondence->generate_next_reference_number();
           $response["html"] = $this->load->view("front_office/form", $data, true);
           $this->output->set_content_type("application/json")->set_output(json_encode($response));
       }
       else
       {///handle form submit
          $post_data = $this->input->post(NULL);
            $this->correspondence->set_fields($post_data);
            $this->correspondence->set_field("body", $this->input->post("body", true, "<h1><h2><h3><h4><h5><h6><strong><em><u><a><p><img>"));
        
            $this->correspondence->set_field("createdBy", $this->is_auth->get_user_id());
            $this->correspondence->set_field("createdOn", date('Y-m-d H:i:s'));
            $this->correspondence->set_field("reference_number", $this->correspondence->generate_next_reference_number());
        
            // $this->correspondence->set_field("assigned_to", $this->is_auth->get_user_id()); // Default to current user
            // $this->correspondence->set_field("assignee_team_id", $this->is_auth->get_user_team_id()); // Default to current user's team
            // $this->correspondence->set_field("modifiedOn", date('Y-m-d H:i:s'));
            // $this->correspondence->set_field("modifiedBy", $this->is_auth->get_user_id());
            // Handle file upload
            if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
                $upload_path = FCPATH . 'attachments/';
                if (!is_dir($upload_path)) {
                    mkdir($upload_path, 0777, true);
                }
                $filename = uniqid() . '_' . basename($_FILES['attachment']['name']);
                $target = $upload_path . $filename;
                if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                    // Save $filename or $target in  DB as needed
                    $this->correspondence->set_field("file", $filename);
                } else {
                    $response = [
                        'result' => false,
                        'display_message' => 'Failed to upload attachment.'
                    ];
                    return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
            }
            // Validate input
            if (!$this->correspondence->validate()) {
                $response = [
                    'result' => false,
                    'display_message' => $this->lang->line("data_missing"),
                    'validationErrors' => $this->correspondence->get("validationErrors")
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }   
            // Insert the correspondence
            if ($this->correspondence->insert()) {
                //if send_notifications_email is set as 1, check whether assigned_to is available and send notification email to the assignee
                
        if(isset($post_data['send_notifications_email']) && $post_data['send_notifications_email'] == 1) {
            $assigned_to = $this->correspondence->get_field("assigned_to");
            if ($assigned_to) {
                $this->load->model("user", "userfactory");
                $this->user = $this->userfactory->get_instance();
                if ($this->user->fetch($assigned_to)) {
                    $user_email = $this->user->get_field("email");
                    if ($user_email) {
                        if (!empty($user_email)) {
                            // Prepare email data
                            $email_data = [
                                'to' => $user_email,
                                'subject' => "New Correspondence COR-".$this->correspondence->get_field("id"),
                                'content' => 'You have been assigned a new correspondence with reference number: ' . $this->correspondence->get_field("reference_number").'</br> Subject: ' .$this->correspondence->get_field("subject").
                                                     '.</br> View it here: <a href="' . base_url('front_office/view/' . $this->correspondence->get_field("id")) . '">Click here</a>'];
                           //send the email
                           $this->send_correspondence_notification($email_data);
                        } else {
                            $response = ['result' => false, 'display_message' => $this->lang->line("no_email_address_found_for_assignee")];

                        }

                    }
                }
            }//end assigne check
        }//end send notification

                $response = [
                    'result' => true,
                    'display_message' => $this->lang->line("correspondence_added_successfully"),
                    'correspondence_id' => $this->correspondence->get_field("id")
                ];
            } else {
                $response = [
                    'result' => false,
                    'display_message' => $this->lang->line("error_adding_correspondence"),
                    'validationErrors' => $this->correspondence->get("validationErrors")
                ];
            }
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
              
         

       }
    }
    /**
     * Add a note or update correspondence status
     * @return JSON response with result and message
     */
    public function add_note_update()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
      
        $logged_in_user = $this->is_auth->get_fullname();
        $response=array();
        $updateType = $this->input->post('updateType');
        $params = $this->input->post('params'); // expects an array

        $document_attachment=false;

        // Check for file upload
        if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
          
            $upload_path = $this->config->item("files_path") . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "correspondences". DIRECTORY_SEPARATOR .$params["correspondence_id"].DIRECTORY_SEPARATOR;
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }
            $filename = basename($_FILES['attachment']['name']);
            $target = $upload_path . $filename;
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
              
                $params['file'] = $filename;
                //load correspodendence_document model:   "id", "name", "size", "extension", "correspondence_id", "document_type_id", "document_status_id", "comments", "createdOn", "modifiedOn", "createdBy", "modifiedBy"
                $this->load->model("correspondence_document", "correspondence_documentfactory");
                $this->correspondence_document = $this->correspondence_documentfactory->get_instance();
                $this->correspondence_document->set_field("correspondence_id", $params['correspondence_id']);
                $this->correspondence_document->set_field("name", basename($_FILES['attachment']['name']));
                $this->correspondence_document->set_field("size", $_FILES['attachment']['size']);
                $this->correspondence_document->set_field("extension", pathinfo($filename, PATHINFO_EXTENSION));
                $this->correspondence_document->set_field("document_type_id", $params['document_type'] ?? 0); // Default to 0 if not set
                $this->correspondence_document->set_field("document_status_id", 1); // Default to 'Pending' status
                $this->correspondence_document->set_field("comments", $params['details'] ?? ''); // Default to empty if not set
                // Set createdBy and createdOn fields
                $this->correspondence_document->set_field("modifiedBy", $this->is_auth->get_user_id());
                $this->correspondence_document->set_field("modifiedOn", date('Y-m-d H:i:s'));        
                $this->correspondence_document->set_field("createdBy", $this->is_auth->get_user_id());
                $this->correspondence_document->set_field("createdOn", date('Y-m-d H:i:s'));

                $document_attachment=true;// to track for the switch statement on document upload
                // Insert the document record
                if (!$this->correspondence_document->insert()) {
                    $response = [
                        'result' => false,
                        'display_message' => $this->lang->line("error_adding_document"),
                        'validationErrors' => $this->correspondence_document->get("validationErrors")
                    ];
                    return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }

                
            } else {
                $response = [
                    'result' => false,
                    'display_message' => 'Failed to upload attachment.'
                ];
                return $this->output->set_content_type('application/json')->set_output(json_encode($response));
            }
        } //end file attachment

        // Validate input
        if (!$updateType || !is_array($params) || empty($params['correspondence_id']) || empty($params['details'])) {
            $response = ['result' => false,
                'display_message' => $this->lang->line("data_missing")
            ];
            return $this->output->set_content_type('application/json')->set_output(json_encode($response));
        }

        $data = [
            'correspondence_id' => $params['correspondence_id'],
            'details' => $params['details'],
            'update_type' => $updateType,
            'createdBy' => $this->session->userdata('user_id'),
            'createdOn' => date('Y-m-d H:i:s')
        ];

        $action="";
        switch ($updateType) {
            case "status":
                $action = "Status update";
                 $new_status_name= $this->correspondence_status->fetch($params['status']) ? $this->correspondence_status->get_field("name") : ""; 
                $data['details'] = "Changed status to '" . $new_status_name . "' with remarks: " . $data['details'];
                if ($this->correspondence->fetch($params['correspondence_id'])) {
                    $this->correspondence->set_field("status_id",$params['status']);
                    $this->correspondence->set_field("modifiedOn",date('Y-m-d H:i:s'));
                    if ($this->correspondence->update()) {
                        $response["result"] = true;
                       
                                      
                        $email_details=[
                            'subject' => "Correspondence Status Update COR-".$params['correspondence_id']. " - by " .$logged_in_user,
                            'content' => 'The status of correspondence with reference number: ' . $this->correspondence->get_field("reference_number") . ' has been updated.</br> New Status: ' .$new_status_name .
                                '. </br>Remarks: ' . $data['details'] . '</br> View it here: <a href="' . base_url('front_office/view/' . $params['correspondence_id']) . '">Click here</a>'
                        ];                        
                        //send email notification if send_notifications_email is set as 1
                       $this->status_update_notification($params, $email_details);
                    } else {
                        $response['result'] = false;
                        $response["validationErrors"] = $this->correspondence->get("validationErrors");
                      return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    }
                }
                break;
            case "note":
                $action = "Adding a Note";
                $data['details'] = "Remarks: " . $data['details'];
                 $email_details=[
                            'subject' => "Correspondence Update COR-".$params['correspondence_id']. " - by " .$logged_in_user,
                            'content' => 'The correspondence with reference number: ' . $this->correspondence->get_field("reference_number") . ' has been updated '.
                                '. Remarks: ' . $data['details'] . '</br> View it here: <a href="' . base_url('front_office/view/' . $params['correspondence_id']) . '">Click here</a>'
                        ];                        
                        //send email notification if send_notifications_email is set as 1
                       $this->status_update_notification($params, $email_details);
                break;
            case "reassign":
                $action = "User assignment ";
                $data['details'] = "Assigned/Re-Assigned an officer. " . $data['details'];
                if ($this->correspondence->fetch($params['correspondence_id'])) {
                   $this->correspondence->set_field("assigned_to" , $params['assignee_id']);
                    $this->correspondence->set_field("assignee_team_id" , $params['provider_group_id']);
                    $this->correspondence->set_field("modifiedOn" , date("Y-m-d H:i:s"));
                    if (!$this->correspondence->update()) {
                        $response["result"] = false;
                        $response["validationErrors"] = $this->correspondence->get("validationErrors");
                        return $response;
                    } else {
                         $email_details=[
                            'subject' => "A new correspondence: COR-".$params['correspondence_id']. " - by " .$logged_in_user." needs your attention",
                            'content' => 'Dear,  ' . $logged_in_user . ',</br> Please Note that a correspondence with reference number: ' . $this->correspondence->get_field("reference_number") . ' requires your attention '.
                                '.</br> Details: ' . $params['details'] . '</br> View it here: <a href="' . base_url('front_office/view/' . $params['correspondence_id']) . '">Click here</a>'
                        ];                        
                        //send email notification if send_notifications_email is set as 1
                       $this->status_update_notification($params, $email_details);
                        $response["result"] = true;
                    }
                }
                break;
            case "document":
                $action = "Document Attachment";
             if($document_attachment) {
                //send email notification if send_notifications_email is set as 1
                $email_details=[
                            'subject' => "Correspondence Document upload: COR-".$params['correspondence_id']. " - by " .$logged_in_user,
                            'content' => 'Dear,  ' . $logged_in_user . ',</br> Please Note that a document has been uploaded to the correspondence: ' . $this->correspondence->get_field("reference_number") .'.</br> Details: ' . $params['details'] . '</br> View it here: <a href="' . base_url('front_office/view/' . $params['correspondence_id']) . '">Click here</a>'
                        ];                        
                        //send email notification if send_notifications_email is set as 1
                       $this->status_update_notification($params, $email_details);
                        $response["result"] = true;
                    }
                
                break;
                case "link-correspondence":
            $action = "Linking related correspondence";
            $related_correspondence_id = $params['related_correspondence_id'];
            if ($this->correspondence->fetch($params['correspondence_id'])) {
                //load the related correspondence model
                $this->load->model("correspondence_relationships", "correspondence_relationshipsfactory");
                $this->correspondence_relationships = $this->correspondence_relationshipsfactory->get_instance();
                if (!$this->correspondence_relationships->fetch($related_correspondence_id)) {
                    $response["result"] = false;
                    $response["display_message"] = "Related correspondence not found.";
                    return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                }
                
                $related_correspondences = $this->correspondence_relationships->load_list(["where" => [["correspondence_id1", $params['correspondence_id']], ["correspondence_id2", $related_correspondence_id]]]);
                if (!in_array($related_correspondence_id, $related_correspondences)) {

                    $this->correspondence_relationships->set_field("correspondence_id1", $params['correspondence_id']);
                    $this->correspondence_relationships->set_field("correspondence_id2", $related_correspondence_id);
                    $this->correspondence_relationships->set_field("createdBy", $this->is_auth->get_user_id());
                    $this->correspondence_relationships->set_field("createdOn", date("Y-m-d H:i:s"));
                    $this->correspondence_relationships->set_field("comments", $params['details'] ?? 'related'); // Default to 'related' if not provided
                    if (!$this->correspondence_relationships->insert()) {
                        $response["result"] = false;
                        $response["validationErrors"] = $this->correspondence_relationships->get("validationErrors");
                        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
                    }
                    $response["result"] = true;
                    $response["display_message"] = "Related correspondence linked successfully.";
                } else {
                    $response["result"] = false;
                    $response["display_message"] = "This correspondence is already linked.";
                }
            }
            break;
            default:
            {
                $response["result"] = false;
                $response['display_message']= $this->lang->line("cannot_be_blank_rule");
            }
        }
            $activityFields=[ "correspondence_id"=> $data['correspondence_id'], "user_id"=>$this->is_auth->get_user_id(),"createdBy"=>$this->is_auth->get_user_id(), "createdOn"=>date("Y-m-d H:i:s"), "action"=>$action, "details"=> $data['details']];
            $this->correspondence_activity_log->set_fields($activityFields);
            if($this->correspondence_activity_log->insert())
            {
                $response["result"]= true;
            }else
            {
                $response["display_message"] = $this->correspondence_activity_log->get("validationErrors");
                $response["result"]= false;
            }

        return $this->output->set_content_type('application/json')->set_output(json_encode($response));
    }
public function load_relationship_form($base_id) {
    $data['base_id'] = $base_id;
    $html = $this->load->view('front_office/relationship_form', $data, true);
     return $this->output->set_content_type('application/json')->set_output(json_encode(['html' => $html]));
     
}

public function save_relationship() {
    $this->form_validation->set_rules('target_id', 'Related Correspondence', 'required');
    
    if ($this->form_validation->run() == FALSE) {
        echo json_encode(['result' => false, 'validationErrors' => $this->form_validation->error_array()]);
    } else {
        $saveData = [
            'parent_id' => $this->input->post('base_id'),
            'child_id'  => $this->input->post('target_id'),
            'type'      => $this->input->post('rel_type'),
            'remarks'   => $this->input->post('remarks')
        ];
        $this->db->insert('correspondence_links', $saveData);
        echo json_encode(['result' => true]);
    }
}
    /**
     * Send a notification email when the status of a correspondence is updated
     * @param array $params
     */
    public function status_update_notification($params,$email_details)
    {
         if(isset($params['send_notifications_email']) && $params['send_notifications_email'] == 1) {
               // Do not send email if the flag is not set
            //load correspondence model
             $this->load->model("correspondence", "correspondencefactory");
                $this->correspondence = $this->correspondencefactory->get_instance();
                if (!$this->correspondence->fetch($params['correspondence_id'])) {
                   exit("correspondence wrong"); // Correspondence not found
                }
                // Check if the correspondence has an assigned user and is not the creator
             $assigned_to = $this->correspondence->get_field("assigned_to");
             $createdBy = $this->correspondence->get_field("createdBy");

             if ($assigned_to && $assigned_to != $createdBy) { // Do not send email to the creator
                 $this->load->model("user", "userfactory");
                 $this->user = $this->userfactory->get_instance();
                 if ($this->user->fetch($assigned_to)) {
                     $assigned_toEmail = $this->user->get_field("email");
                     $createdByEmail=$this->user->get_field("email", $createdBy);
                     if ($assigned_toEmail) {
                         $email_data = [
                             'to' => $assigned_toEmail,
                             'subject' => $email_details['subject'],
                             'content' => $email_details['content']
                         ];
                         //send the email
                         $this->send_correspondence_notification($email_data);
                     }
                    ///notify creator if they are different from the assigned user
                     
                     if ($createdByEmail && $createdByEmail != $assigned_toEmail) { // Do not send email to the assigned user
                         // Send email notification to the creator as well
                         $email_data_creator = [
                             'to' => $createdByEmail,
                             'subject' => $email_details['subject'],
                             'content' => $email_details['content']
                               ];
                         //send the email
                         $this->send_correspondence_notification($email_data_creator);
                     }
                 }
             }
         }else{
            //NO SENDING
         }
       
    }
    
    public function dashboard_stats()
    {    // Get filters from GET parameters
        $year = $this->input->get('year');
        $month = $this->input->get('month');
        $type = $this->input->get('type');

        // Prepare filters array
        $filters = [];
        if ($year) $filters['year'] = $year;
        if ($month) $filters['month'] = $month;
        if ($type) $filters['type'] = $type;

        // Get dashboard data from the model
        $dashboard = $this->correspondence->get_dashboard_stats($filters);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($dashboard));


    }

    //method to send corresonndence notifications. it should take an array object that has to email, subject and body. return true if sent, false if not
    public function send_correspondence_notification($email_data)
    {
        if (empty($email_data['to']) || empty($email_data['subject']) || empty($email_data['content'])) {
            return false; // Invalid data
        }

        $this->load->library('email_notifications');
        return $this->email_notifications->send_email($email_data['to'], $email_data['subject'], $email_data['content']);
    }


public function download_file($document_id)
{
    $this->load->model("correspondence_document", "correspondence_documentfactory");
    $this->correspondence_document = $this->correspondence_documentfactory->get_instance();

    if (!$this->correspondence_document->fetch($document_id)) {
        show_error('File not found.', 404);
        return;
    }

    $correspondence_id = $this->correspondence_document->get_field("correspondence_id");
    $document_name = $this->correspondence_document->get_field("name");

    // Use the correct base path
    $base_path = FCPATH . 'files';
    $path = $base_path
        . DIRECTORY_SEPARATOR . "attachments"
        . DIRECTORY_SEPARATOR . "correspondences"
        . DIRECTORY_SEPARATOR . $correspondence_id
        . DIRECTORY_SEPARATOR . $document_id
        . DIRECTORY_SEPARATOR  ;

        // Best implementation with error handling
$base_path = $base_path;
$filename = $document_name;
$file_path = realpath($base_path . $filename);

if ($file_path && file_exists($file_path)) {
    $document["content"] = file_get_contents($file_path);
    if ($document === false) {
        log_message('error', 'Failed to read file despite existence: '.$file_path);
        // Handle read error
    }else {
        // Set headers for download
        $f = finfo_open();
                $mime_type = finfo_buffer($f, $document["content"], FILEINFO_MIME_TYPE);
                $document["mime_type"] = $mime_type;
                header("Content-Type:", $mime_type);
                echo $document["content"];
    }
} else {
    log_message('error', 'File not found at: '.$base_path.$filename);
    // Handle missing file
}



   
}

/**
 * Reads and returns the binary content of a file.
 * @param string $path
 * @return string|false
 */
protected function get_file_content($path)
{
    return file_get_contents($path);
}


public function download_file1($correspondence_id, $document_id, $document_name)
{
    // Sanitize inputs
    $correspondence_id = (int)$correspondence_id;
    $document_id = (int)$document_id;
    $document_name = basename($document_name); // Prevent directory traversal

    // Build the file path
    $path = $this->config->item("files_path")
        . DIRECTORY_SEPARATOR . "attachments"
        . DIRECTORY_SEPARATOR . "correspondences"
        . DIRECTORY_SEPARATOR . $correspondence_id
        . DIRECTORY_SEPARATOR . $document_id
        . DIRECTORY_SEPARATOR . $document_name;

    if (!file_exists($path) || !is_file($path)) {
        show_error('File not found.', 404);
        return;
    }

    // Set headers and force download
    $this->load->helper('download');
    // Optionally, get the mime type
    $mime = mime_content_type($path);
    header('Content-Type: ' . $mime);
    header('Content-Disposition: attachment; filename="' . rawurldecode($document_name) . '"');
    header('Content-Length: ' . filesize($path));
    readfile($path);
    exit;
}

    // Methods for fetching data (called by AJAX in the view, if needed)
    public function get_incoming_count_json() {
        $month = $this->input->post('month'); // Or use date('Y-m')
        $count = $this->correspondence->get_incoming_count($month);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['count' => $count]));
    }

    public function get_outgoing_count_json() {
        $month = $this->input->post('month');
        $count = $this->correspondence->get_outgoing_count($month);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['count' => $count]));
    }

   ///function to fetch documents per correspondence
   public function get_documents_per_correspondence_json($correspondence_id) {
        $this->load->model("correspondence_document", "correspondence_documentfactory");
        $this->correspondence_document = $this->correspondence_documentfactory->get_instance();
        $documents = $this->correspondence_document->get_documents_per_correspondence($correspondence_id);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['documents' => $documents]));
    }

    public function get_recent_activity_json() {
        $activity = $this->correspondence->get_recent_activity();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['activity' => $activity]));
    }

    public function get_recent_files_json() {
        $files = $this->File_model->get_recent_files();
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['files' => $files]));
    }


}
