<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5_3 extends CI_Controller
{
    use MigrationLogTrait;

    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }

    public function index()
    {
        $this->add_fields_to_invoice_templates();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function add_fields_to_invoice_templates()
    {
        $this->write_log($this->log_path, 'Started adding Invoice Ref to invoice templates');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['body'])){
                if(!isset($settings['body']['show']['invoice-ref-container'])){
                    $settings['body']['show']['invoice-ref-container'] = false;
                }
                if(!isset($settings['body']['show']['invoice-description-table'])){
                    $settings['body']['show']['invoice-description-table'] = false;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'Invoice Ref added Succeddfully');
    }
}
