<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_8_1 extends CI_Controller {

    use MigrationLogTrait;

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->load->database();
    }


    public function index() {
        $this->restructure_timers_sqlsrv();
        $this->write_log($this->log_path, 'end of the migration script');
    }

    public function restructure_timers_sqlsrv()
    {
        if ($this->db->dbdriver === 'sqlsrv') {
            $get_all_active_timers = $this->db->query(
            "SELECT * FROM user_preferences WHERE keyName='activityLogTimer'"
            )->result_array();
            // check if data not empty
            if (isset($get_all_active_timers) && !empty($get_all_active_timers)) {
                foreach ($get_all_active_timers as $key => $value) {
                    $value['id'] = $value['user_id'];
                    $this->write_log($this->log_path, " get old timer data id=".$value['id']);
                    if ($value['keyValue']) {
                        // get old timer data
                        $oldtimer = unserialize($value['keyValue']);
                        // check if this old structure
                        if (isset($oldtimer['startedOn'])) {
                            // init new timer structure
                            $timer['id'] = rand();
                            $timer['status'] = 'active';
                            $timer['logs'] = array(
                                        array('start_date'=>$oldtimer['startedOn'],'end_date'=>null)
                                );
                            $timer['description'] = '' ;
                            $timer['object'] = [];
                            $task_id  = $oldtimer['task_id'];
                            $legalcase_id  = $oldtimer['legal_case_id'];
                            if (isset($task_id) && !empty($task_id)) {
                                array_push($timer['object'], ['task'=>$task_id]);
                            }
                            if (isset($legalcase_id) && !empty($legalcase_id)) {
                                array_push($timer['object'], ['matter'=>$legalcase_id]);
                            }
                            $timers[] = $timer;
                            // update query to set new structure
                            $update = $this->db->query("UPDATE user_preferences SET keyValue ='".serialize($timers)."' WHERE keyName='activityLogTimer' AND user_id=".$value['user_id']);
                            if ($update) {
                                $msg = "update timer structure id=".$value['id'];
                                $this->write_log($this->log_path, $msg);
                            } else {
                                $msg = "fail update timer structure id=".$value['id'];
                                $this->write_log($this->log_path, $msg);
                            }
                        } else {
                            $msg = "timer already in new structure";
                            $this->write_log($this->log_path, $msg);                    
                        }
                    }
                }
            }
        } 
    }
}
