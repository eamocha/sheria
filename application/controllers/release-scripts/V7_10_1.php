<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_10_1 extends CI_Controller {

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
        $this->insert_stage_missing_language_values();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    public function insert_stage_missing_language_values(){
        $this->write_log($this->log_path, 'insert_stage_missing_language_values started', 'info');
        $query = 'SELECT legal_case_stages.id FROM legal_case_stages WHERE NOT EXISTS(SELECT legal_case_stage_languages.legal_case_stage_id FROM legal_case_stage_languages WHERE legal_case_stages.id = legal_case_stage_languages.legal_case_stage_id)';
        $legal_case_stages = $this->db->query($query)->result_array();
        
        foreach($legal_case_stages as $stage){
            for($i = 1; $i < 5; $i++){
                $q = 'INSERT INTO legal_case_stage_languages(legal_case_stage_id, language_id, name) VALUES(' . $stage['id'] . ',' . $i . ',\'UNDEFINED\');';
                $this->db->query($q);
            }
        }
        
        $this->write_log($this->log_path, 'insert_stage_missing_language_values is done', 'info');
    }
}
