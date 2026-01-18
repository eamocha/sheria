<?php

require "Top_controller.php";
class Suppliers extends Top_controller
{
    public $responseData;
    public function __construct()
    {
        parent::__construct();
        $this->load->model("vendor");
        $this->responseData = default_response_data();
    }
    public function autocomplete()
    {
        $response = $this->responseData;
        $term = trim((string) $this->input->post("term"));
        $this->lookup_term_validation($term);
        if (!empty($term)) {
            $response["success"]["data"] = $this->vendor->api_lookup($term);
        } else {
            $response["error"] = $this->lang->line("data_missing");
        }
        $this->render($response);
    }
    public function list_suppliers(){
        $response = $this->responseData;
        $response["success"]["data"] = $this->vendor->api_get_all_suppliers();

        $this->render($response);
    }

    public function fetch_vendor($id){
        $response = $this->responseData;
        $response["success"]["data"] = $this->vendor->fetch_vendor($id);
        $this->render($response);
    }

}

