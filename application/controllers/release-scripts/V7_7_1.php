<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class V7_7_1 extends CI_Controller {

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->load->database();
        $this->load->model('instance_data');
    }
    
    private function write_log($file_path, $message, $type = 'info') {
        $log_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
        $pr = fopen($log_path, 'a');
        fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
        fclose($pr);
        if ($type == 'error') {
            echo $type . ': ' . $message . ". Please check the log file '$log_path' for more details and to fix the error";
            exit;
        }
    }

    public function index() {
        $this->replace_assets_path();
    }
    
    private function replace_assets_path(){
        $this->write_log($this->log_path, 'START :: replace_assets_path ...');
        $instance_data = $this->instance_data->load_all();
        foreach($instance_data as $value){
            if (strpos($value['keyValue'], 'compressed_asset') !== false) {
                $this->instance_data->set_value_by_key($value['keyName'], str_replace('compressed_asset/', 'assets/', $value['keyValue']));
            }
        }
        $this->write_log($this->log_path, 'DONE :: replace_assets_path ...');
    }
}
