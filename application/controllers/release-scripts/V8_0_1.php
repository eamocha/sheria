<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_0_1 extends CI_Controller
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
        $this->fix_empty_invoice_templates();
        $this->fix_category_in_company_grid_saved_filers();
        $this->write_log($this->log_path, 'done from migration script');
    }
    
    public function fix_category_in_company_grid_saved_filers(){
        $this->write_log($this->log_path, 'start fixing category column in company grid saved filters table');
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $fields_arr = [
            // old => new
            'companies.company_category_id' => 'companies.company_sub_category_id'
        ];
//        this array is for testing to reproduce the old saved filter values        
//        $fields_arr = [
//            // new => old
//            'companies.company_sub_category_id' => 'companies.company_category_id'
//        ];
        $sql = "SELECT id, filterName, formData FROM grid_saved_filters WHERE model = 'Company'";
        $query_execution = $this->db->query($sql);
        $saved_filters = $query_execution->result_array();
        foreach ($saved_filters as $key => $saved_filter) {
            $form_data = [];
            $this->write_log($this->log_path, "fix filters in filter # '{$saved_filter['id']}'");
            $form_data = unserialize($saved_filter['formData']);
            if(!empty($form_data) && isset($form_data['gridFilters'])){
                $json = json_decode($form_data['gridFilters']);
                foreach($json->filters as $k => $filter){
                    if(isset($filter->filters[0]->field)){
                        foreach($fields_arr as $old_field => $new_field){
                            if($filter->filters[0]->field == $old_field){
                                $json->filters[$k]->filters[0]->field = $new_field;
                                // update saved filter
                                $new_form_data = serialize(['gridFilters' => json_encode($json, JSON_UNESCAPED_UNICODE)]);
                                if (strpos($saved_filter['filterName'], "(Sub-category)") === FALSE) {
                                    if ($this->db->query("UPDATE grid_saved_filters set formData = '{$new_form_data}', filterName = '{$saved_filter['filterName']} (Sub-category)' WHERE id = '{$saved_filter['id']}'")) {
                                        $this->write_log($this->log_path, 'Done - Updating filters and filter name of id  = '.$saved_filter['id']);
                                    } else {
                                        $this->write_log($this->log_path, 'Error - Failed to update the saved filter id = '.$saved_filter['id']);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'end renaming columns');
    }

    public function fix_empty_invoice_templates()
    {
        $this->write_log($this->log_path, 'started fixing empty invoice template');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates");
        $templates = $query->result_array();
        foreach ($templates as $value => $template){
            $settings = unserialize($template['settings']);
            if(isset($settings['body']['general']['line_items']) && empty($settings['body']['general']['line_items'])){
                $settings['body']['general']['line_items']['expenses'] = 1;
                $settings['body']['general']['line_items']['time_logs'] = 2;
                $settings['body']['general']['line_items']['items'] = 3;
                $settings = addslashes(serialize($settings));
                $this->db->query("update organization_invoice_templates set settings = '{$settings}' WHERE id = {$template['id']}");
                $this->write_log($this->log_path, "template having the id {$template['id']} has been fixed");
            }
        }
        $this->write_log($this->log_path, 'fixed empty invoice templates');

    }
}