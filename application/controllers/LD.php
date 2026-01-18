<?php
if (!defined("BASEPATH")) {
    exit("No direct script access allowed");
}
require "Core_controller.php";
class LD extends Core_controller
{

    public $Legal_Case;
    public $defaultWorkflow = "";
    public $controller_name = "ldmis";
    public function __construct()
    {
        parent::__construct();
        $this->currentTopNavItem = "ldmis";

    }
    public function index($page="ldims")
    {
        $this->load->view("partial/header");
        $this->load->view("legislative_drafting/".$page);
        $this->load->view("partial/footer");

    }
}