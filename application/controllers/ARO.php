<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class ARO extends Core_controller
{

    public $controller_name = "ARO";

    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "ARO";

    }
    public function index($folder="",$view){
        if ($folder !== "") {
            $folder = $folder."/";
        }
        $this->includes("bootstrap-5.3.8/css/bootstrap.min", "css");
        $this->includes("bootstrap-icons-1.13.1/bootstrap-icons", "css");

        $this->includes("bootstrap-5.3.8/js/bootstrap.bundle.min", "js");


        $this->load->view("partial/header");
        $this->load->view("aro_test/".$folder.$view);
        $this->load->view("partial/footer");

    }
}