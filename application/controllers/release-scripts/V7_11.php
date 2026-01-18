<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_11 extends CI_Controller {

    use MigrationLogTrait;

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->load->database();
        $this->write_log($this->log_path, 'start migration script');
    }
    
    public function index() {
        $this->remove_courts_foreign_keys();
        $this->change_htaccess_file_for_added_module();
        $this->update_permissions_scheme();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    /**
     * remove the courts similarities and replace the deleted courts id (if they are used somewhere) 
     * with the id of first court of its similarities group
     */
    public function remove_courts_foreign_keys(){
        $this->write_log($this->log_path, 'remove_courts_foreign_keys started', 'info');
        $query = $this->db->query("SELECT * FROM legal_case_litigation_details WHERE court_id IS NOT NULL");
        $result = $query->result_array();
        $similarities = $this->get_courts_similarities();
        foreach($result as $row){
            foreach($similarities as $v){
                $first_record = $v[0];
                array_shift($v);
                foreach($v as $court){
                    if($court["id"] == $row["court_id"]){
                        $this->db->set("court_id", $first_record["id"]);
                        $this->db->where("id", $row["id"]);
                        $this->db->update("legal_case_litigation_details");
                    }
                }
            }
        }
        $this->delete_all_court_similarities($similarities);
        $this->write_log($this->log_path, 'remove_courts_foreign_keys is done', 'info');
    }
    
    /**
     * get courts similarities by matching all the similar court names
     * regardless the white-spaces
     * 
     * @return array
     */
    private function get_courts_similarities(){
        $similarities = [];
        $query = $this->db->query("SELECT * FROM courts");
        $result = $query->result_array();
        foreach($result as $row){
            $this->group_similarities($similarities, $row['id'], preg_replace('!\s+!', ' ', trim($row['name'])));
        }
        return $similarities;
    }
    
    /**
     * group all the similar courts together
     * 
     * @param type $similarities
     * @param type $id
     * @param type $name
     */
    private function group_similarities(&$similarities, $id, $name){
        $found = false;
        foreach($similarities as $k => $v){
            if($v[0]['name'] === $name){
                $similarities[$k][] = ['id' => $id, 'name' => $name];
                $found = true;
                break;
            }
        }
        if(!$found){
            $similarities[] = [
                ['id' => $id, 'name' => $name]
            ];
        }
    }
    
    /**
     * delete all the courts similarities except the first one
     * 
     * @param type $similarities
     */
    private function delete_all_court_similarities($similarities){
        foreach($similarities as $v){
            array_shift($v);
            foreach($v as $court){
                $this->db->query("DELETE FROM courts WHERE id = {$court["id"]}");
            }
        }
    }
    
    /**
     * replace app4legal by cloud site url in A4G module for cloud instances only 
    */
    public function change_htaccess_file_for_added_module(){
        $this->write_log($this->log_path, 'change_htaccess_file_for_added_module started', 'info');
        $this->load->model('instance_data');
        $data = $this->instance_data->get_value_by_key('installationType');
        if ($data && isset($data['keyValue']) && $data['keyValue'] === 'on-cloud') {
            $data = $this->instance_data->get_value_by_key('instanceID');
            if($data && isset($data['keyName']) && $data['keyName'] == 'instanceID'){
                $file = file_get_contents(FCPATH . 'modules' . DIRECTORY_SEPARATOR . 'A4G' . DIRECTORY_SEPARATOR . '.htaccess');
                $file = str_replace('/app4legal/', '/site/' . $data['keyValue'] . '/', $file);
                file_put_contents(FCPATH . 'modules' . DIRECTORY_SEPARATOR . 'A4G' . DIRECTORY_SEPARATOR . '.htaccess', $file);
            }
        }
        $this->write_log($this->log_path, 'change_htaccess_file_for_added_module is done', 'info');
    }

    /*
     * This update is required to give clients the access to export options instead of export (invoice or payment) for the users having the permission
     */

    public function update_permissions_scheme(){
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'money'){
                    if(in_array('/vouchers/invoice_export_to_word/', $group_permission)){
                        $key = array_search('/vouchers/invoice_export_to_word/', $group_permission);
                        $new_permissions = $group_permissions;
                        unset($new_permissions['money'][$key]);
                        array_push($new_permissions['money'], '/vouchers/invoice_export_options/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/vouchers/invoice_payment_export_to_word/', $group_permission)){
                        $key = array_search('/vouchers/invoice_payment_export_to_word/', $group_permission);
                        $new_permissions = $group_permissions;
                        unset($new_permissions['money'][$key]);
                        array_push($new_permissions['money'], '/vouchers/payment_export_options/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
    }
}
