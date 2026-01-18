<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require "Top_controller.php";

class orders_decrees extends Top_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = sprintf($this->lang->line("sheria360_browser_title"), $this->lang->line("conveyancing_management"));
        // Load libraries
        $this->load->library("dmsnew");
    }


    public function index()
    {
        $data = [];



            $this->includes("kendoui/js/kendo.web.min", "js");
            $this->includes("kendoui/styles/kendo.common-bootstrap.min", "css");
            $this->includes("kendoui/styles/kendo.bootstrap.min", "css");
            $this->includes("jquery/form2js", "js");
            $this->includes("jquery/toObject", "js");
            $this->includes("scripts/conveyancing", "js");
            $this->includes("scripts/advance_search_custom_field_template", "js");
            $this->includes(app_url("compressed_asset/system_defaults.js?a=" . random_string("alpha")), "js", true);
            $this->includes("jquery/timemask", "js");


            $this->load->view("partial/header");
         $this->load->view("orders_decrees/index");
            $this->load->view("partial/footer");

    }


    public function document_status(){  echo "coming soon";}
    public function transaction_types(){  echo "coming soon";}
    public function document_types(){  echo "coming soon";}
    public function manage_workflows(){  echo "coming soon";}

}