<?php

defined('BASEPATH') OR exit('No direct script access allowed');
//require "Top_controller.php";
require "Core_controller.php";
class Exhibits extends Core_controller {

    public function __construct() {
        parent::__construct();
        //load exhibits model
        $this->load->model('case_exhibit', 'case_exhibitfactory');
        $this->case_exhibit=$this->case_exhibitfactory->get_instance();
        $this->load->model("exhibit_activities_log","exhibit_activities_logfactory");
        $this->exhibit_activities_log=$this->exhibit_activities_logfactory->get_instance();

        $this->load->model("exhibit_location","exhibit_locationfactory");
        $this->exhibit_location=$this->exhibit_locationfactory->get_instance();
        $this->load->model("exhibit_chain_of_movement","exhibit_chain_of_movementfactory");
        $this->exhibit_chain_of_movement=$this->exhibit_chain_of_movementfactory->get_instance();


        ///dmsnew library upload
        $this->load->library('dmsnew');


    }
    public function frontendIncludes()
    {  $this->includes("bootstrap/js/bootstrap4.6.1.bundle.min", "js");
        $this->includes("customerPortal/clientPortal/css/datatables.min", "css");
        $this->includes("customerPortal/clientPortal/js/datatables.min", "js");
        $this->includes("customerPortal/clientPortal/js/jquery.dataTables.min", "js");
        $this->includes("customerPortal/clientPortal/js/dataTables.bootstrap.min", "js");

    }

    public function index1() {
        $data = $this->case_exhibit->k_load_all_exhibits("","","");


         $this->frontendIncludes();//include js files
        $this->load->view("partial/header");
        $this->load->view("prosecution/google/exhibits",$data);
        $this->load->view("partial/footer");
    }

    /**
     * Renders the exhibit management view or handles the AJAX request for data.
     */
    public function index()
    {   $this->includes("kendoui/js/kendo.web.min", "js");
        $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
        $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
        $this->includes("jquery/css/chosen", "css");
        $this->includes("jquery/chosen.min", "js");
        $this->includes("jquery/form2js", "js");
        $this->includes("jquery/jquery.dirtyform", "js");
        $this->includes("jquery/toObject", "js");
        $this->includes("scripts/show_hide_customer_portal", "js");

        $response=[];
        // Check if the request is an AJAX POST from DataTables
        if ($this->input->post('take') || $this->input->post('skip')) {
            // Retrieve Kendo UI-style parameters from the POST request
            $filter = $this->input->post('filter');
            $sortable = $this->input->post('sortable');
            $take = $this->input->post('take');
            $skip = $this->input->post('skip');

            // Call the model method to get exhibit data
            $exhibits_data = $this->case_exhibit->k_load_all_exhibits($filter, $sortable);
          //  exit(json_encode($exhibits_data));
            // Process the data to combine opponents and clients into a single 'parties' column
            $processed_data = [];
            foreach ($exhibits_data['data'] as $row) {
                // Combine opponents and clients into a 'parties' string
                $row['parties'] = trim($row['opponents'] . ' vs. ' . $row['clients'], ' vs.');

                // Add the processed row to the new array
                $processed_data[] = $row;
            }

            // Prepare the JSON response for DataTables
            $response = [
                'data' => $processed_data,
                'recordsTotal' => $exhibits_data['totalRows'],
                'recordsFiltered' => $exhibits_data['totalRows'], // For filtered data, this should be the filtered count
            ];


            // Set the content type to JSON and echo the response
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($response));
        } else {
            $this->frontendIncludes();//include js files
           $this->load->view("partial/header");

            $this->load->view("prosecution/index",$response);
            $this->load->view("partial/footer");
            // If it's not an AJAX request, load the main view

        }
    }
public function view_details($exhibit_id)
{
    $exhibit_id = is_numeric($exhibit_id) ? (int)$exhibit_id : $exhibit_id;
    try {
        $exhibit = $this->case_exhibit->get_exhibit_record($exhibit_id);
        if (!$exhibit) {
            throw new Exception('Exhibit not found');
        }
        // Process exhibit data
    } catch (Exception $e) {
        log_message('error', 'Failed to load exhibit: ' . $e->getMessage());
        show_error('Unable to load exhibit data', 500);
    }

    $this->includes("flatpickr/flatpickr.min", "css");
    $this->includes("flatpickr/flatpickr", "js");
    $this->includes("scripts/exhibits/exhibits", "js");

    $this->load->view("partial/header");
    $data=  $this->case_exhibit->get_exhibit_record($exhibit_id);
    $data["history"]=$this->exhibit_activities_log->get_activity_log_by_exhibit_id($exhibit_id);
    $data["chain_of_custody"]=$this->exhibit_chain_of_movement->get_movement_by_exhibit_id($exhibit_id);

    $this->load->view("prosecution/view_exhibit",$data);
    $this->load->view("partial/footer");

}
 public function edit()
 {
     if (!$this->input->post(null)){
         $id=$this->input->get("id");
     $data['exhibit_data']=$this->case_exhibit->get_exhibit_record($id);
     $response["result"] = true;
     $response["html"] = $this->load->view("prosecution/exhibits/management/edit", $data, true);

     }else{
         $this->case_exhibit->reset_fields();
         $post_data = $this->input->post(NULL);
         // Ensure the primary key is set for update
         if (isset($post_data['id'])) {
             $this->case_exhibit->set_field('id', $post_data['id']);
         } elseif (isset($post_data['exhibit_id'])) {
             $this->case_exhibit->set_field('exhibit_id', $post_data['exhibit_id']);
         }
         $this->case_exhibit->set_fields($post_data);
         $this->case_exhibit->set_field("modifiedOn", date("Y-m-d H:i:s"));
         $this->case_exhibit->set_field("createdOn", date("Y-m-d H:i:s"));
         $this->case_exhibit->set_field("createdBy", $this->is_auth->get_user_id());
         $this->case_exhibit->set_field("modifiedBy", $this->is_auth->get_user_id());
         $response['result'] = false;
         if ($this->case_exhibit->validate()){
             if ($this->case_exhibit->update()){
                 $this->case_exhibit->reset_fields();
                 $response["result"] = true;
                 $response['message'] = "Updated successifully";
             }
         } else {
             $response["validation_errors"] = $this->case_exhibit->get("validationErrors");
         }

     }
     $this->output->set_content_type("application/json")->set_output(json_encode($response));
 }
 public function delete()
 {
     if(!$this->input->post(null)) {
         $data =$this->input->get(null); 
         $response["result"] = true;
         $response["html"] = $this->load->view("prosecution/exhibits/management/delete", $data, true);

     }else{
         $response["result"]=false;

     }
     $this->output->set_content_type("application/json")->set_output(json_encode($response));
 }
    public function change_status()
    {
        if(!$this->input->post(null)){
            $data=$this->input->get(null);
        $response["result"] = true;
        $response["html"] = $this->load->view("prosecution/exhibits/management/change_status", $data, true);

    }else{
        $post_data=$this->input->post();
      //  $this->case_exhibit->reset_fields();
        //exhibit_status
        $this->case_exhibit->set_field("modifiedOn", date("Y-m-d H:i:s"));
            $this->case_exhibit->set_field("exhibit_status", $post_data['new_status']);
            $this->case_exhibit->set_field("date_approved_for_disposal", $post_data['date_approved_for_disposal']);
            $this->case_exhibit->set_field("date_disposed", $post_data['date_disposed']);
            $this->case_exhibit->set_field("manner_of_disposal", $post_data['manner_of_disposal']);
            $this->case_exhibit->set_field("disposal_remarks", $post_data['disposal_remarks']);
        $this->case_exhibit->set_field('id', $post_data['exhibit_id']);
        if ($this->case_exhibit->update()){
            $response["result"] = true;
            $response["message"] = "Updated successfully";
        }else {
            $response["result"] = false;
        }

}
        $this->output->set_content_type("application/json")->set_output(json_encode($response));

}
    public function transfer_custody()
    {  $response["result"]=false;
        if (!$this->input->post(null)){
            $data=$this->input->get(null);

        $response["result"] = true;
        $response["html"] = $this->load->view("prosecution/exhibits/management/custody_transfer", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }else{
            $post_data=$this->input->post(NULL);
            $this->exhibit_chain_of_movement->reset_fields();
            $this->exhibit_chain_of_movement->set_fields($post_data);
            if ($this->exhibit_chain_of_movement->validate()){
                if ($this->exhibit_chain_of_movement->insert()){
                    $response["result"] = true;
                    $response["message"] = "Updated successifully";

                }
            }else{
                $response["validation_errors"] = $this->exhibit_chain_of_movement->get("validationErrors");
            }

            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }

    }
    public function add_note()
    {
        $response['result']=false;
        if(!$this->input->post(null)){
        $data=$this->input->get(null);
        $response["result"] = true;
        $response["html"] = $this->load->view("prosecution/exhibits/management/add_note", $data, true);
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
        }else{
            $post_data=$this->input->post(null);
            $this->exhibit_activities_log->set_fields($post_data);
            if($this->exhibit_activities_log->insert()){
                $response["result"]=true;
                $response['message']="Saved successfully";
            }
            $this->output->set_content_type("application/json")->set_output(json_encode($response));

        }

    }

public function fetch_exhibit_record($exhibit_id){

      //
    $this->output->set_content_type("application/json")->set_output(json_encode($data));
}
public function fetch_exhibit_movement($exhibit_id)
{
    $data=  $this->exhibit_chain_of_movement->get_movement_by_exhibit_id($exhibit_id);

}

public  function exhibit_activities_log($id)
{
 $data["data"]=$this->exhibit_activities_log->get_activity_log_by_exhibit_id($id);//exit(json_encode($data));
}
    ///function to handle file upload
    public function upload_file()
    {
        if ($this->input->get(NULL, true)) {
            $id = $this->input->get("id", true);
            $data = $this->load_documents_form_data($id, $this->input->get("lineage", true));
            $data["title"] = $this->lang->line("upload_file");
            $data["module"] = "cases";
            $response["result"] = true;
            $response["html"] = $this->load->view("prosecution/forms/attachments_form", $data, true);
        }
        if ($this->input->post(NULL, true)) {
            if (!$_FILES["uploadDoc"]["name"]) {
                $response["status"] = false;
                $response["validation_errors"]["uploadDoc"] = $this->lang->line("file_required");
            } else {
                $response = $this->dmsnew->upload_file(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "upload_key" => "uploadDoc", "document_type_id" => $this->input->post("document_type_id"), "document_status_id" => $this->input->post("document_status_id"), "comment" => $this->input->post("comment")]);
                $this->load->model("document_management_system", "document_management_systemfactory");

                $this->document_management_system = $this->document_management_systemfactory->get_instance();
               // $response["related_documents_count"] = $this->document_management_system->count_contract_related_documents($this->input->post("module_record_id"));
                $response["module_record_id"] = $this->input->post("module_record_id");
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }
    private function load_documents_form_data($id, $lineage)
    {
        $this->load->model("exhibit_document_type");
        $this->load->model("exhibit_document_status");

        $data["document_statuses"] = $this->exhibit_document_type->load_list([], ["firstLine" => ["" => " "]]);
        $data["document_types"] = $this->exhibit_document_status->load_list([], ["firstLine" => ["" => " "]]);
        $data["attachment_type"] = "exhibit";
        $data["module_record"] = "exhibit";
        $data["module_record_id"] = $id;
        return $data;
    }
    public function load_documents()
    {
        $response = $this->dmsnew->load_documents(["module" => $this->input->post("module"), "module_record_id" => $this->input->post("module_record_id"), "lineage" => $this->input->post("lineage"), "term" => $this->input->post("term")]);
        if ($this->input->is_ajax_request()) {
            $this->output->set_content_type("application/json");
            $this->output->set_output(json_encode($response));
        } else {
            return $response;
        }
    }
    ///location autocomplete
    // In your controller
    public function location_autoComplete() {
    $term = $this->input->get('term');
    $this->db->like('name', $term);
    $query = $this->db->get('locations');
    $data = $query->result_array();
    echo json_encode($data);
    exit;
   }

}
?>