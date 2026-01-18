<?php

if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require APPPATH . "controllers/Top_controller.php";
class Opinions extends Top_controller
{
    public $Opinion;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model("opinion", "opinionfactory");
        $this->opinion = $this->opinionfactory->get_instance();
        $this->currentTopNavItem = "Legal Opinions";

        if (!$this->is_auth->is_logged_in()) {
            redirect("users/login");
        }
    }

    //Opinions
    public function index($view)
    {

        $this->load->view("partial/header");
        $this->load->view("opinions_test/".$view);
        $this->load->view("partial/footer");

    }


}

