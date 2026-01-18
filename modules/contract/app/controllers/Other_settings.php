<?php


if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Other_settings extends Contract_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model("contract_reference_numbering", "contract_reference_numberingfactory");
      $this->contract_reference_numbering = $this->contract_reference_numberingfactory->get_instance();

    }
    public function index()
    {
        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
        $data['formats'] = $this->contract_reference_numbering->load_all();//exit(json_encode($data));

//        $this->load->model("contract", "contractfactory");
//        $this->contract = $this->contractfactory->get_instance();
//       $data['ref']= $this->contract->get_new_ref_number_();

        $this->load->view("partial/header");
        $this->load->view("other_settings/index", $data);
        $this->load->view("partial/footer");
    }
    public function set_active()
    {
        $response= ['status' => ''];
        $id = $this->input->post('id');
       $da= $this->contract_reference_numbering->load(["where"=>["is_active",1]]);
        $this->contract_reference_numbering->fetch($da["id"]);
        $this->contract_reference_numbering->set_field("is_active",0);
        if($this->contract_reference_numbering->update()){
            $this->contract_reference_numbering->reset_fields();
            if ($this->contract_reference_numbering->fetch($id)){
                //  $this->contract_reference_numbering->reset_fields();
                    $this->contract_reference_numbering->set_field("is_active",1);
                    if($this->contract_reference_numbering->update()){
                        $response= ['status' => 'success'];
                    }else{
                        $response= ['status' => 'unable to update'];
                    }
            }else{
                $response= ['status' => 'failed'];
            }
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

    public function edit()
    {
        if (!$this->input->is_ajax_request()) {
            redirect("other_settings");
        }


        $post_data=$this->input->post(null);
        $response= ['ok' => false, 'row' => $post_data];//['ok' =>false,"error"=> ''];

        $this->contract_reference_numbering->fetch($post_data["id"]);
        $this->contract_reference_numbering->set_fields($post_data);
        if ($this->contract_reference_numbering->update()){
           $row= $this->contract_reference_numbering->fetch($post_data["id"]);
            $response=['ok' => true, 'row' => $row];
        }else
        {
            $response= ['error' => 'failed update'];
        }
        $this->output->set_content_type("application/json")->set_output(json_encode($response));
    }

}