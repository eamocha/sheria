<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_18 extends CI_Controller
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
        $this->update_matter_board_saved_filter();
        $this->add_file_extensions();
        $this->write_log($this->log_path, 'End migration script');
    }

    private function update_matter_board_saved_filter()
    {
        $this->load->model('planning_board');
        $this->load->model('planning_board_saved_filter', 'planning_board_filter');
        $planning_boards =  $this->planning_board->load_all();
        if (!empty($planning_boards) && is_array($planning_boards)) {
            foreach ($planning_boards as $board) {
                $this->write_log($this->log_path, 'Getting saved filters by board id start');
                $filters = $this->planning_board_filter->get_saved_filters_by_board_id($board['id']);
                $this->write_log($this->log_path, 'Getting saved filters by board id end');
                if (!empty($filters)) {
                    foreach ($filters as &$filter){
                        $filter_details = unserialize($filter['keyValue']);
                        if (!empty($filter_details) && is_array($filter_details)) {
                            if($filter_details['showList'] == 3){
                                $filter_details['type_filter'] = '2';
                                $filter_details['showList'] = '';
                            } else if($filter_details['showList'] == 4){
                                $filter_details['type_filter'] = '1';
                                $filter_details['showList'] = '';
                            }
                        }
                        $filter['keyValue'] = serialize($filter_details);
                        $this->write_log($this->log_path, 'Updating saved filter od id '. $filter['id'] .' by board id start');
                        $this->planning_board_filter->update_filters_by_board_id($filter['id'],$filter['keyValue']);
                        $this->write_log($this->log_path, 'Updating saved filter od id '. $filter['id'] .' by board id end');
                    }
                    unset($filter);
                }
            }
        }
    }
    /**
     * add new file types: rtf
     */
    public function add_file_extensions(){
        $this->write_log($this->log_path, 'Changes on allowed files upload started', 'info');
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';

        if(!file_exists($allowed_file_uploads_path)){
            $this->write_log($this->log_path, 'target file not found (' . $allowed_file_uploads_path . ')', 'error');
            return;
        }

        $target_keys = [
            'company', 'contact', 'case', 'doc', 'BI', 'BI-PY', 'EXP', 'INV', 'QOT', 'INV-PY', 'task', 'caseContainer'
        ];
        $handle = fopen($allowed_file_uploads_path, "r");
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                // process the line read.
                foreach($target_keys as $target_key){
                    $str_pos = strpos($line, '$config[\'' . $target_key . '\']');
                    if($str_pos !== false){
                        $this->write_log($this->log_path, 'replacing config variable: ' . '$config[\'' . $target_key . '\']', 'info');
                        $new_line = substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")+2);
                        file_put_contents($allowed_file_uploads_path, str_replace($new_line, substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")) . "|rtf';", file_get_contents($allowed_file_uploads_path)));
                    }
                }
            }

            fclose($handle);
        } else {
            $this->write_log($this->log_path, 'error opening the file (' . $allowed_file_uploads_path . ')', 'error');
        }

        $this->write_log($this->log_path, 'Changes on allowed files upload is done', 'info');
    }
}
