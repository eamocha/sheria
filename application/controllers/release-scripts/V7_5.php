<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class V7_5 extends CI_Controller {

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->load->database();
    }

    public function index() {
        $this->update_database_file();
        $this->update_empty_client_name();
    }

    public function write_log($file_path, $message, $type = 'info') {
        $log_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
        $pr = fopen($log_path, 'a');
        fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
        fclose($pr);
        if ($type == 'error') {
            echo $type . ': ' . $message . ". Please check the log file '$log_path' for more details and to fix the error";
            exit;
        }
    }

    /**
     * update database file content for on-cloud users only. For on server, the file will be updated manually and the below changes will be overwritten.
     *
     * @return void
     */
    public function update_database_file() {
        $this->write_log($this->log_path, 'Database update file starts');
        $database_file_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
        $new_content = str_replace('$active_record', '$query_builder', file_get_contents($database_file_path));
        if (@!file_put_contents($database_file_path, $new_content)) {
            $this->write_log($this->log_path, "failed to put content to file $database_file_path", 'error');
        } else {
            $this->write_log($this->log_path, "Database File is updated");
        }
    }
   /**
    * update_empty_client_name function
    * update empty client id in time log table to show client name in retrive
    * @return void
    */
    public function update_empty_client_name() {
        // load mode user_activity_log
        $this->load->model('user_activity_log', 'user_activity_logfactory');
        // init model and get_instance
        $this->user_activity_log = $this->user_activity_logfactory->get_instance();
         // load all user_activity_log
        $all_user_activity_log = $this->user_activity_log->load_all();
        $this->write_log($this->log_path, 'select all user_activity_log');
        if(!empty($all_user_activity_log) ){ // ckeck empty return valus
            foreach ($all_user_activity_log as $key => $value) { // foreach data
               if(empty($value['client_id']) && !empty($value['legal_case_id'])){ // check id empty client_id and time log have legal_case_id
                if (($this->db->dbdriver === 'sqlsrv')) {
                        // get case  client_id in mssql 
                        $get_case_client = $this->db->query("SELECT TOP 1 legal_cases.client_id FROM legal_cases WHERE legal_cases.id = ".$value['legal_case_id'])->result_array();
                    }else{
                         // get case  client_id in mysql 
                        $get_case_client = $this->db->query(
                            "SELECT legal_cases.client_id FROM `legal_cases` WHERE `legal_cases`.`id` = ".$value['legal_case_id']."
                            LIMIT 1"
                            )->result_array(); // return client_id data array to update in time log table
                    }                      
                        // update user client_id in time log table
                       if($get_case_client && !empty($get_case_client)){ // check if query done and not empty data retrived
                            $this->write_log($this->log_path, 'update time log id = '.$value['id']. 'with client_id='.$get_case_client[0]['client_id']);
                            $this->db->set('client_id', $get_case_client[0]['client_id']);
                            $this->db->where('id', $value['id']);
                            $this->db->update('user_activity_logs');
                            $this->write_log($this->log_path, 'update successfully time log id = '.$value['id']. 'with client_id='.$get_case_client[0]['client_id']);
                        }else{
                            // if not get data from legal_cases table
                            $this->write_log($this->log_path, 'case =>'.$value['legal_case_id']. 'not have clinet id');
                       }      
                }
            }   
        }
    }

}
