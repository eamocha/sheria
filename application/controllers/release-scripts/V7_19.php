<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_19 extends CI_Controller
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
        $this->fix_bill_status();
        $this->add_creation_logs_to_litigation_stages();
        $this->update_instance_config_file();
        if($this->db->dbdriver == 'mysqli'){
            // changes were made on mysql only due to the new way of reading the grid data & columns 
            $this->rename_hearing_columns_in_saved_filters();
        }
        // the commented functions will be executed only for a specific client on the cloud
//        $this->remove_duplicate_case_contacts_companies();
//        $this->add_missing_client_contacts_companies_to_cases();
//        $this->add_missing_opponent_contacts_companies_to_cases();
//        $this->fix_wrong_time_log_billing_statuses();
        $this->write_log($this->log_path, 'End migration script');
    }

    private function fix_bill_status()
    {
        $sql = "select id from bills_full_details where balanceDue = '0.00' and billStatus in ('partially paid', 'overdue')";
        $query_execution = $this->db->query($sql);
        $bills = $query_execution->result_array();
        $this->write_log($this->log_path, 'Done - Reading the wrong bills');
        foreach ($bills as $key => $voucher_header_id) {
            if ($voucher_header_id['id']) {
                $update = "UPDATE bill_headers SET status = 'paid' WHERE bill_headers.voucher_header_id = '{$voucher_header_id['id']}' and status = 'partially paid'";
                $result = $this->db->query($update);
                if ($result) {
                    $this->write_log($this->log_path, 'Done - Updating of the bill of voucher header id = '.$voucher_header_id['id']);
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update the bill of voucher header id = '.$voucher_header_id['id']);
                }
            }
        }
    }
    
    private function fix_wrong_time_log_billing_statuses()
    {
        $this->write_log($this->log_path, 'Start reading the wrong time logs of type partially paid and open');
        $sql = "SELECT invoice_time_logs_items.time_log as time_log"
                . " FROM invoice_time_logs_items"
                . " LEFT JOIN invoice_details ON invoice_details.id = invoice_time_logs_items.item"
                . " LEFT JOIN invoice_headers on invoice_headers.id = invoice_details.invoice_header_id"
                . " LEFT JOIN user_activity_log_invoicing_statuses on user_activity_log_invoicing_statuses.id = invoice_time_logs_items.time_log"
                . " WHERE invoice_headers.paidStatus in ('partially paid', 'open') and user_activity_log_invoicing_statuses.log_invoicing_statuses != 'invoiced'";
        $query_execution = $this->db->query($sql);
        $time_logs = $query_execution->result_array();
        foreach ($time_logs as $key => $time_log) {
            // update time log status
            if ($this->db->query("UPDATE user_activity_log_invoicing_statuses set log_invoicing_statuses = 'invoiced' WHERE id = '{$time_log['time_log']}'")) {
                $this->write_log($this->log_path, 'Done - Updating of time log id  = '.$time_log['time_log']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to insert the time log id = '.$time_log['time_log']);
            }
        }
        $this->write_log($this->log_path, 'Start reading the wrong time logs of type paid');
        $sql = "SELECT invoice_time_logs_items.time_log as time_log"
                . " FROM invoice_time_logs_items"
                . " LEFT JOIN invoice_details ON invoice_details.id = invoice_time_logs_items.item"
                . " LEFT JOIN invoice_headers on invoice_headers.id = invoice_details.invoice_header_id"
                . " LEFT JOIN user_activity_log_invoicing_statuses on user_activity_log_invoicing_statuses.id = invoice_time_logs_items.time_log"
                . " WHERE invoice_headers.paidStatus = 'paid' and user_activity_log_invoicing_statuses.log_invoicing_statuses != 'reimbursed'";
        $query_execution = $this->db->query($sql);
        $time_logs = $query_execution->result_array();
        foreach ($time_logs as $key => $time_log) {
            // update time log status
            if ($this->db->query("UPDATE user_activity_log_invoicing_statuses set log_invoicing_statuses = 'reimbursed' WHERE id = '{$time_log['time_log']}'")) {
                $this->write_log($this->log_path, 'Done - Updating of time log id  = '.$time_log['time_log']);
            } else {
                $this->write_log($this->log_path, 'Error - Failed to insert the time log id = '.$time_log['time_log']);
            }
        }
    }
    
    private function remove_duplicate_case_contacts_companies()
    {
        $this->write_log($this->log_path, 'remove duplicate case contacts');
        $this->db->query("DELETE c1
            FROM legal_cases_contacts c1 
            INNER JOIN legal_cases_contacts c2 
            WHERE c1.id > c2.id AND
            c1.case_id = c2.case_id 
            AND c1.contact_id = c2.contact_id 
            AND c1.contactType = c2.contactType 
            AND c1.contactType = 'contact';");
        
        $this->write_log($this->log_path, 'remove duplicate case companies'); 
        $this->db->query("DELETE c1
            FROM legal_cases_companies c1 
            INNER JOIN legal_cases_companies c2 
            WHERE c1.id > c2.id AND
            c1.case_id = c2.case_id 
            AND c1.company_id = c2.company_id 
            AND c1.companyType = c2.companyType 
            AND c1.companyType = 'company';");
    }
    
    private function add_missing_client_contacts_companies_to_cases(){
        $this->write_log($this->log_path, 'add missing client contacts companies to cases');
        $sql = "SELECT legal_cases.id, clients.contact_id, clients.company_id"
                . " FROM legal_cases"
                . " LEFT JOIN clients ON clients.id = legal_cases.client_id"
                . " WHERE legal_cases.client_id is not null and legal_cases.id > 0 and legal_cases.id <= 15555";
        $query_execution = $this->db->query($sql);
        $cases = $query_execution->result_array();
        $this->load->model(array('legal_case_contact', 'legal_case_company'));
        $this->legal_case_contact->disable_builtin_logs();
        $this->legal_case_company->disable_builtin_logs();
        $this->load->model('user', 'userfactory');
        $this->user = $this->userfactory->get_instance();
        $is_admin_email = $this->user->get('isAdminUser');
        $this->user->fetch(['email' => $is_admin_email]);
        $admin_user = $this->user->get_field('id');
        foreach ($cases as $key => $case) {
            if($case['contact_id'] > 0){
                $this->legal_case_contact->reset_fields();
                if(!$this->legal_case_contact->fetch(array('contact_id' => $case['contact_id'], 'case_id' => $case['id'], 'contactType' => 'contact'))){
                    $this->write_log($this->log_path, "add contact id '{$case['contact_id']}' to case id '{$case['id']}'");
                    $this->legal_case_contact->set_fields(array('contact_id' => $case['contact_id'], 'case_id' => $case['id'], 'contactType' => 'contact', 'createdOn' => date("Y-m-d H:i:s"), 'createdBy' => $admin_user, 'modifiedOn' => date("Y-m-d H:i:s"), 'modifiedBy' => $admin_user));
                    $this->legal_case_contact->insert();
                    $this->legal_case_contact->reset_fields();
                }
            }else if($case['company_id'] > 0){
                $this->legal_case_company->reset_fields();
                if(!$this->legal_case_company->fetch(array('company_id' => $case['company_id'], 'case_id' => $case['id'], 'companyType' => 'company'))){
                    $this->write_log($this->log_path, "add company id '{$case['company_id']}' to case id '{$case['id']}'");
                    $this->legal_case_company->set_fields(array('company_id' => $case['company_id'], 'case_id' => $case['id'], 'companyType' => 'company', 'createdOn' => date("Y-m-d H:i:s"), 'createdBy' => $admin_user, 'modifiedOn' => date("Y-m-d H:i:s"), 'modifiedBy' => $admin_user));
                    $this->legal_case_company->insert();
                    $this->legal_case_company->reset_fields();
                }
            }
        }
    }
    
    private function add_missing_opponent_contacts_companies_to_cases(){
        $this->write_log($this->log_path, 'add missing opponent contacts companies to cases');
        $sql = "SELECT legal_cases.id, opponents.contact_id, opponents.company_id"
                . " FROM legal_cases"
                . " LEFT JOIN legal_case_opponents ON legal_case_opponents.case_id = legal_cases.id"
                . " LEFT JOIN opponents ON opponents.id = legal_case_opponents.opponent_id"
                . " WHERE legal_cases.id > 0 and legal_cases.id <= 15555";
        $query_execution = $this->db->query($sql);
        $cases = $query_execution->result_array();
        $this->load->model(array('legal_case_contact', 'legal_case_company'));
        $this->legal_case_contact->disable_builtin_logs();
        $this->legal_case_company->disable_builtin_logs();
        $this->load->model('user', 'userfactory');
        $this->user = $this->userfactory->get_instance();
        $is_admin_email = $this->user->get('isAdminUser');
        $this->user->fetch(['email' => $is_admin_email]);
        $admin_user = $this->user->get_field('id');
        foreach ($cases as $key => $case) {
            if($case['contact_id'] > 0){
                $this->legal_case_contact->reset_fields();
                if(!$this->legal_case_contact->fetch(array('contact_id' => $case['contact_id'], 'case_id' => $case['id'], 'contactType' => 'contact'))){
                    $this->write_log($this->log_path, "add contact id '{$case['contact_id']}' to case id '{$case['id']}'");
                    $this->legal_case_contact->set_fields(array('contact_id' => $case['contact_id'], 'case_id' => $case['id'], 'contactType' => 'contact', 'createdOn' => date("Y-m-d H:i:s"), 'createdBy' => $admin_user, 'modifiedOn' => date("Y-m-d H:i:s"), 'modifiedBy' => $admin_user));
                    $this->legal_case_contact->insert();
                    $this->legal_case_contact->reset_fields();
                }
            }else if($case['company_id'] > 0){
                $this->legal_case_company->reset_fields();
                if(!$this->legal_case_company->fetch(array('company_id' => $case['company_id'], 'case_id' => $case['id'], 'companyType' => 'company'))){
                    $this->write_log($this->log_path, "add company id '{$case['company_id']}' to case id '{$case['id']}'");
                    $this->legal_case_company->set_fields(array('company_id' => $case['company_id'], 'case_id' => $case['id'], 'companyType' => 'company', 'createdOn' => date("Y-m-d H:i:s"), 'createdBy' => $admin_user, 'modifiedOn' => date("Y-m-d H:i:s"), 'modifiedBy' => $admin_user));
                    $this->legal_case_company->insert();
                    $this->legal_case_company->reset_fields();
                }
            }
        }
    }
    
    public function add_creation_logs_to_litigation_stages(){
        $this->write_log($this->log_path, 'add creation logs to stages');
        $sql = "SELECT legal_case_litigation_details.id, audit_logs.created, audit_logs.user_id"
                . " FROM legal_case_litigation_details"
                . " LEFT JOIN audit_logs ON audit_logs.recordId = legal_case_litigation_details.id"
                . " WHERE audit_logs.model = 'legal_case_litigation_detail' and audit_logs.action = 'insert'";
        $query_execution = $this->db->query($sql);
        $logs = $query_execution->result_array();
        $this->load->model('legal_case_litigation_detail', 'legal_case_litigation_detailfactory');
        $this->legal_case_litigation_detail = $this->legal_case_litigation_detailfactory->get_instance();
        $this->legal_case_litigation_detail->disable_builtin_logs();
        foreach ($logs as $key => $log) {
            if($this->legal_case_litigation_detail->fetch($log['id']) && $this->legal_case_litigation_detail->get_field('createdOn') == NULL){
                $this->write_log($this->log_path, "add logs to litigation # '{$log['id']}'");
                $this->legal_case_litigation_detail->set_fields(array('createdOn' => $log['created'], 'createdBy' => $log['user_id']));
                $this->legal_case_litigation_detail->update();
            }
        }
    }
    
    private function check_config_parameter($option, $file)
    {
        $this->write_log($this->log_path, 'check_config_parameter started', 'info');
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        
        foreach ($lines as $key => $line) {
            if (strpos($line, $option) > 0) {
                return true;
            }
        }
        
        $this->write_log($this->log_path, 'check_config_parameter is done', 'info');
        
        return false;
    }
    
    public function update_instance_config_file(){
        $this->write_log($this->log_path, 'update instance config file');
        $file = getcwd() . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "instance.php";
        $this->load->model('instance_data');
        $data = $this->instance_data->get_value_by_key('installationType');
        if ($data && isset($data['keyValue']) && $data['keyValue'] === 'on-cloud') {
            $str = "\$config['PHP_executable_path'] = '/bin/php71';// PHP executable path that located in Environment variables. For windows, it should be like: 'C:\php\php.exe'. For Linux, it should be like: /bin/php71";
        }else{
            $str = "\$config['PHP_executable_path'] = 'C:\php\php.exe';// PHP executable path that located in Environment variables. For windows, it should be like: 'C:\php\php.exe'. For Linux, it should be like: /bin/php71";
        }
        $option = 'PHP_executable_path';

        if (file_exists($file)) {
            if (!$this->check_config_parameter($option, $file)) {
                $data = PHP_EOL . $str . PHP_EOL;
                if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
                    $this->write_log($this->log_path, '    couldn\'t modify ' . $file);
                }
            }
        } else {
            $this->write_log($this->log_path, $file . ' doesn\'t exists.', 'info');
        }

        $this->write_log($this->log_path, 'update_instance_config_file is done', 'info');
    }
    
    public function rename_hearing_columns_in_saved_filters(){
        $this->write_log($this->log_path, 'start renaming columns in hearing grid saved filters');
        $this->load->model('grid_saved_filter', 'grid_saved_filterfactory');
        $this->grid_saved_filter = $this->grid_saved_filterfactory->get_instance();
        $fields_arr = [
            // old => new
            'legal_case_hearings.opponents' => 'legal_case_hearings.opponents_en',
            'legal_case_hearings.opponent_foreign_name' => 'legal_case_hearings.opponent_foreign_name_en',
            'ld.legal_case_stage' => 'legal_case_hearings.legal_case_stage_id',
            'legal_case_hearings.hearing_client_position_id' => 'legal_case_hearings.matter_client_position_id',
            'legal_case_hearings.clientForeignName' => 'legal_case_hearings.client_foreign_name'
        ];
//        this array is for testing to reproduce the old saved filter values        
//        $fields_arr = [
//            // new => old
//            'legal_case_hearings.opponents_en' => 'legal_case_hearings.opponents',
//            'legal_case_hearings.opponent_foreign_name_en' => 'legal_case_hearings.opponent_foreign_name',
//            'legal_case_hearings.legal_case_stage_id' => 'ld.legal_case_stage',
//            'legal_case_hearings.matter_client_position_id' => 'legal_case_hearings.hearing_client_position_id',
//            'legal_case_hearings.client_foreign_name' => 'legal_case_hearings.clientForeignName'
//        ];
        $sql = "SELECT id, formData FROM grid_saved_filters WHERE model = 'Legal_Case_Hearing'";
        $query_execution = $this->db->query($sql);
        $saved_filters = $query_execution->result_array();
        foreach ($saved_filters as $key => $saved_filter) {
            $form_data = [];
            $this->grid_saved_filter->fetch($saved_filter['id']);
            $this->write_log($this->log_path, "fix filters in filter # '{$saved_filter['id']}'");
            $form_data = unserialize($saved_filter['formData']);
            if(!empty($form_data) && isset($form_data['gridFilters'])){
                $json = json_decode($form_data['gridFilters']);
                foreach($json->filters as $k => $filter){
                    if(isset($filter->filters[0]->field)){
                        foreach($fields_arr as $old_field => $new_field){
                            if($filter->filters[0]->field == $old_field){
                                $json->filters[$k]->filters[0]->field = $new_field;
                            }
                        }
                    }
                }
                // update saved filter
                $new_form_data = serialize(['gridFilters' => json_encode($json, JSON_UNESCAPED_UNICODE)]);
                if ($this->db->query("UPDATE grid_saved_filters set formData = '{$new_form_data}' WHERE id = '{$saved_filter['id']}'")) {
                    $this->write_log($this->log_path, 'Done - Updating of saved filter id  = '.$saved_filter['id']);
                } else {
                    $this->write_log($this->log_path, 'Error - Failed to update the saved filter id = '.$saved_filter['id']);
                }
            }
        }
        $this->write_log($this->log_path, 'end renaming columns');
    }
}
