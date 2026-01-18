<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_6_3 extends CI_Controller
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
        $this->update_saved_invoice_template();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_saved_invoice_template()
    {
        $this->write_log($this->log_path, 'start update invoice template to be compatible with new layout options');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['body'])){
                if(!isset($settings['body']['show']['full_width_layout'])){
                    $settings['body']['show']['full_width_layout'] = false;
                }
                if(!isset($settings['header']['show']['image_full_width'])){
                    $settings['header']['show']['image_full_width'] = false;
                }
                $template['settings'] = serialize($settings);
            }
            $this->db->query("update organization_invoice_templates set settings = '{$template['settings']}' WHERE id = {$template['id']}");
        }
        $this->write_log($this->log_path, 'end update invoice template to be compatible with new layout options');
    }
}
