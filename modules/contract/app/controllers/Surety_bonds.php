<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Surety_bonds extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("surety_bond","surety_bondfactory");
        $this->surety_bond=$this->surety_bondfactory->get_instance();

    }

    public function index()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $data = [];
        $this->load->model("grid_saved_filter", "grid_saved_filterfactory");
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $this->load->model("grid_saved_column");
     
       $model= $data["model"] = "surety_bond";
        $data["gridSavedFilters"] = $this->grid_saved_filter->loadFiltersList($model, $this->session->userdata("AUTH_user_id"));
        $data["gridSavedFiltersData"] = false;
        $data["gridDefaultFilter"] = $this->grid_saved_filter->getDefaultFilter($model, $this->session->userdata("AUTH_user_id"));
        if ($data["gridDefaultFilter"]) {
            $gridSavedData = $this->grid_saved_filter->load_data($data["gridDefaultFilter"]["id"]);
            $data["gridSavedFiltersData"] = unserialize($gridSavedData["formData"]);
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($model, $data["gridDefaultFilter"]["id"]));
        } else {
            $data = array_merge($data, $this->grid_saved_column->get_user_grid_details($model));
        }
        if ($this->input->post(NULL)) {
            if (!$this->input->is_ajax_request()) {
                redirect("dashboard");
            }
            $filter = $this->input->post("filter");
            $sortable = $this->input->post("sort");
            if ($this->input->post("loadWithSavedFilters") === "1") {
                $filter = json_decode($this->input->post("filter"), true);
            } else { $psize=$data["grid_saved_details"]["pageSize"]??0;
                $page_size_modified = $this->input->post("pageSize") != $psize;
                $sort_modified = $this->input->post("sortData") && $this->input->post("sortData") != $data["grid_saved_details"]["sort"];
                if ($page_size_modified || $sort_modified) {
                    $_POST["model"] = $data["model"];
                    $_POST["grid_saved_filter_id"] = $data["gridDefaultFilter"]["id"]??"";
                    $response = $this->grid_saved_column->save();
                    $response["gridDetails"] = $this->grid_saved_column->get_user_grid_details($data["model"], $data["gridDefaultFilter"]["id"]??"");
                }
            }

            $response["columns_html"] = $this->load->view("grid_saved_columns/dialog_columns_contents", $data, true);
            $response = array_merge($response, $this->surety_bond->k_load_all($filter, $sortable));// exit(json_encode($this->surety_bond->k_load_all($filter, $sortable)));
            $this->output->set_content_type("application/json")->set_output(json_encode($response));
        } else {
            $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("sureties"));
            $data["operators"]["text"] = $this->get_filter_operators("text");
            $data["operators"]["big_text"] = $this->get_filter_operators("bigText");
            $data["operators"]["number"] = $this->get_filter_operators("number");
            $data["operators"]["number_only"] = $this->get_filter_operators("number_only");
            $data["operators"]["date"] = $this->get_filter_operators("date");
            $data["operators"]["time"] = $this->get_filter_operators("time");
            $data["operators"]["list"] = $this->get_filter_operators("list");
            $data["operators"]["text_empty"] = $this->get_filter_operators("text_empty");
            $data["operators"]["group_list"] = $this->get_filter_operators("groupList");

            $data["archivedValues"] = array_combine($this->surety_bond->get("archivedValues"), [$this->lang->line("either"), $this->lang->line("yes"), $this->lang->line("no")]);
            $data["defaultArchivedValue"] = "no";

            $data["loggedUserIsAdminForGrids"] = $this->is_auth->userIsGridAdmin();
//exit(json_encode($data));
            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
          //  $this->includes("contract/index", "js");
            $this->includes("jquery/tabledit/jquery.tabledit.min", "js");
            $this->includes("contract/show_hide_customer_portal", "js");
            $this->includes("money/js/accounting", "js");
            if ($this->is_auth->is_layout_rtl()) {
                $this->includes("styles/rtl/fixes", "css");
            }
            $this->load->view("partial/header");
            $this->load->view("sureties/index", $data);
            $this->load->view("partial/footer");
        }
    }

    public function related_sureties($contract_id = 0)
    {
        // Ensure the request is an AJAX request
        if (!$this->input->is_ajax_request()) {
            $this->set_flashmessage("error", $this->lang->line("invalid_record"));
            redirect("dashboard"); // Redirect if not AJAX
        }

        $response = []; // Initialize response array

        // Load the surety_bond model using a factory pattern
        $this->load->model("surety_bond", "surety_bondfactory");
        $this->surety_bond = $this->surety_bondfactory->get_instance();



        if ($this->input->post(NULL)) {
            $mode = $this->input->post("mode");
            $post_data = $this->input->post(NULL); // Get all POST data

            if (isset($mode)) {

                $suretyId = $this->input->post('id');

                $this->surety_bond->set_fields($post_data);
                if ($this->surety_bond->validate() && $this->surety_bond->validateDates($post_data)) {
                    $result = false; // Initialize result to false

                    if ($mode == "edit" && $suretyId > 0) {
                        if ($this->surety_bond->update(["where" => ["id", $suretyId]])) {
                            $result = true; // Update successful

                        } else {
                            // Update failed, get validation errors if any specific to update operation
                            $response["validation_errors"] = $this->surety_bond->get("validationErrors");
                        }
                    } else {
                        $result = $this->surety_bond->insert();
                    }

                    if ($result) {
                        $this->contract->fetch($post_data['contract_id']);//load the contract in question
                        $effective_date=$post_data['effective_date'];
                        $expiry_date=$post_data['expiry_date'];
                        $this->contract->set_field("perf_security_commencement_date",$effective_date);
                        $this->contract->set_field("perf_security_expiry_date",$expiry_date);
                        $this->contract->update();
                        $this->surety_bond->reset_fields(); // Clear model fields after successful operation
                        $response["status"] = "success";
                        $response["message"] = "Saved Successfully";
                    } else {
                        $response["status"] = "error";
                        $response["message"] = "Error in saving";
                    }
                } else {
                    // Validation failed, return validation errors
                    $response["status"] = "error";
                    $response["validation_errors"] = $this->surety_bond->get("validationErrors");
                }
            } else {
                // Handle cases where 'mode' is not set in POST data (e.g., just filtering/sorting)
                $filter = $this->input->post("filter");
                $sortable = $this->input->post("sort");
                $data = $this->surety_bond->load_all_surety_bonds_by_contract($contract_id, $sortable);
                $response["html"] = $this->load->view("sureties/securities", $data, true);
            }

        } else {
            // Handle GET requests for loading the form or list
            $data = [];
            $data["contract_id"] = $contract_id;
            // Load currencies for dropdown
            $data["currencies"] = $this->iso_currency->load_list(["order_by" => ["id", "asc"]], ["firstLine" => ["" => $this->lang->line("none")]]);
            $data["hide_show_notification"] = true;

            $suretyId = $this->input->get("suretyId");
            $mode = $this->input->get("mode");
            $data["mode"] = $mode;

            if (isset($mode)) {
                if ($mode == 'edit' && $suretyId > 0) {
                    // In edit mode, fetch the existing record to populate the form
                    // This data should include createdBy and createdOn
                    if ($this->surety_bond->fetch($suretyId)) {
                        $data['bond'] = $this->surety_bond->get_fields();
                        $data["contract_id"] = $contract_id; // Ensure contract_id is passed

                        $response["html"] = $this->load->view("sureties/form", $data, true);
                    } else {
                        $this->set_flashmessage("error", $this->lang->line("invalid_record"));
                        // Consider setting an error status in $response if record not found for edit
                    }
                } elseif ($mode == 'loadForm') {
                    // Load an empty form for adding a new record
                    $response["html"] = $this->load->view("sureties/form", $data, true);
                }
            } else {
                // Default: load the list of surety bonds
                $data['Surety_bonds'] = $this->surety_bond->load_all_surety_bonds_by_contract($contract_id)['data'];
                $response["html"] = $this->load->view("sureties/related_sureties", $data, true);
            }
        }
        // Set content type and output JSON response
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }


}

