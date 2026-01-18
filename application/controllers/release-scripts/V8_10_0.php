<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_10_0 extends CI_Controller
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
        $this->update_api_htaccess();
        $this->move_contract_cp_screens();
        $this->write_log($this->log_path, 'End migration script');
    }

    private function update_api_htaccess()
    {
        $this->write_log($this->log_path, 'Start update api/.htaccess file script');
        $htaccess_file = fopen(INSTANCE_PATH . 'modules' . DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . '.htaccess', 'a');
        fwrite($htaccess_file, "\n<If '%{THE_REQUEST} =~ m#/modules/api/contacts/bulk_add#'> \n   php_value max_input_vars 15000 \n</If>");
        fclose($htaccess_file);
        $this->write_log($this->log_path, 'End update api/.htaccess file script');
    }

    public function move_contract_cp_screens(){

        $this->write_log($this->log_path, 'start migrating contract screens to the new tables');
        $sql = "SELECT customer_portal_screens.*"
                . " FROM customer_portal_screens"
                . " WHERE customer_portal_screens.applicable_on='contract'";

        $query_execution = $this->db->query($sql);
        $cp_screens = $query_execution->result_array();
        
	///contract tables
	    $this->load->model('contract_cp_screen', 'contract_cp_screenfactory');
        $this->contract_cp_screen = $this->contract_cp_screenfactory->get_instance();
        $this->load->model('contract_cp_screen_field', 'contract_cp_screen_fieldfactory');
        $this->contract_cp_screen_field = $this->contract_cp_screen_fieldfactory->get_instance();
        $this->load->model('contract_cp_screen_field_language', 'contract_cp_screen_field_languagefactory');
        $this->contract_cp_screen_field_language = $this->contract_cp_screen_field_languagefactory->get_instance();
	
	///cases tables
        $this->load->model('customer_portal_screen', 'customer_portal_screenfactory');
        $this->customer_portal_screen = $this->customer_portal_screenfactory->get_instance();

        foreach ($cp_screens as $screen ) {
            $screen_fields = [];
            $this->contract_cp_screen->reset_fields();
            $this->contract_cp_screen->set_fields(array('type_id' => $screen['case_type_id'], 'sub_type_id' => 0, 'name' => $screen['name'], 'description' => $screen['description'], 'showInPortal' => $screen['showInPortal'], 'contract_request_type_category_id'=>$screen['request_type_category_id']));
            if($this->contract_cp_screen->insert()){
                $screen_id= $this->contract_cp_screen->get_field('id');
                $this->write_log($this->log_path, 'Insert screen in table (contract_customer_portal_screen) with id '.$screen_id);
                $sql_get_fields = "SELECT customer_portal_screen_fields.*"
                    . " FROM customer_portal_screen_fields"
                    . " WHERE customer_portal_screen_fields.customer_portal_screen_id = ".$screen['id'];
        
                $screen_fields = $this->db->query($sql_get_fields)->result_array();
                foreach ($screen_fields  as $field ) {
                    $screen_fields_lang = [];
                    $this->contract_cp_screen_field->reset_fields();
                    $this->contract_cp_screen_field->set_fields(array('screen_id' => $screen_id, 'related_field' => $field['relatedCaseField'], 'isRequired' => $field['isRequired'], 'visible' => $field['visible'], 'requiredDefaultValue' => $field['requiredDefaultValue'], 'fieldDescription' => $field['fieldDescription'], 'sortOrder'=> $field['sortOrder'] ));
                    if($this->contract_cp_screen_field->insert()){
                        $screen_field_id= $this->contract_cp_screen_field->get_field('id');
                        $this->write_log($this->log_path, 'Insert screen field in table (contract_cp_screen_fields) with id '.$screen_field_id);
                        
                        $sql_get_fields_per_language = "SELECT customer_portal_screen_field_languages.*"
                            . " FROM customer_portal_screen_field_languages"
                            . " WHERE customer_portal_screen_field_languages.customer_portal_screen_field_id = ".$field['id'];

                        $screen_fields_lang = $this->db->query($sql_get_fields_per_language)->result_array();
                        foreach ($screen_fields_lang  as $field_lang ) {
                            $this->contract_cp_screen_field_language->reset_fields();
                            $this->contract_cp_screen_field_language->set_fields(array('screen_field_id' => $screen_field_id, 'language_id' => $field_lang['language_id'], 'labelName' => $field_lang['labelName']));
                            if($this->contract_cp_screen_field_language->insert()){
                                $this->write_log($this->log_path, 'Insert screen field language in table (contract_cp_screen_field_language) with id '.$this->contract_cp_screen_field_language->get_field('id'));
                                if($this->db->query("Delete from customer_portal_screen_field_languages where id=". $field_lang['id'])){
                                    $this->write_log($this->log_path, 'Delete screen field language from table (customer_portal_screen_field_languages) with id '.$field_lang["id"]);
                                }
                                else{
                                    $this->write_log($this->log_path, 'Failed to delete screen field languages from table (customer_portal_screen_field_languages) with id '.$field_lang['id']);  
                                }
                            }
                            else{
                                $this->write_log($this->log_path, 'failed to insert screen field language in table (contract_cp_screen_field_language) with id '.$this->contract_cp_screen_field_language->get_field('id'));
                            }
                        }
                        if($this->db->query("Delete from customer_portal_screen_fields where id=". $field['id'])){
                            $this->write_log($this->log_path, 'Delete screen field from table (contract_cp_screen_fields) with id '.$field['id']);
                        }
                        else{
                            $this->write_log($this->log_path, 'failed to delete screen field from table (contract_cp_screen_fields) with id '.$field['id']);
                        }
            
                    }
                    else{
                        $this->write_log($this->log_path, 'Failed to insert screen field in table (contract_cp_screen_fields)');
                    }
                    
                }
                if($this->customer_portal_screen->delete(array('where' => array('id', $screen['id'])))){
                    $this->write_log($this->log_path, 'Delete screen from table (contract_cp_screens) with id '.$screen['id']);
                }
                else{
                    $this->write_log($this->log_path, 'failed to delete screen from table (contract_cp_screens)'); 
                }
            }
            else{
                $this->write_log($this->log_path, 'Failed to insert screen in table (contract_cp_screens)');
            }

        }
        $this->write_log($this->log_path, 'End of migrating contract screens to the new tables');
    }
}
