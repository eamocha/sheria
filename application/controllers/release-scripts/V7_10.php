<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_10 extends CI_Controller {

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
        $this->add_support_to_word_documents_on_mac();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    /**
     * add new file types for word documents to support it on mac
     */
    public function add_support_to_word_documents_on_mac(){
        $this->write_log($this->log_path, 'Changes on allowed files upload started', 'info');
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';
        
        if(!file_exists($allowed_file_uploads_path)){
            $this->write_log($this->log_path, 'target file not found (' . $allowed_file_uploads_path . ')', 'error');
            return;
        }
        
        $target_keys = [
            'company', 'contact', 'case', 'doc', 'BI', 'BI-PY', 'EXP', 'INV', 'QOT', 'INV-PY', 'task'
        ];
        $fp = fopen($allowed_file_uploads_path, "r+");
        
        while ($line = stream_get_line($fp, 1024 * 1024, "\n")){
            foreach($target_keys as $target_key){
                $str_pos = strpos($line, '$config[\'' . $target_key . '\']');
                if($str_pos !== false){
                    $this->write_log($this->log_path, 'replacing config variable: ' . '$config[\'' . $target_key . '\']', 'info');
                    $new_line = substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")+2);
                    file_put_contents($allowed_file_uploads_path, str_replace($new_line, substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")) . "|pages';", file_get_contents($allowed_file_uploads_path)));
                }
            }
        }
        
        $this->write_log($this->log_path, 'Changes on allowed files upload is done', 'info');
    }
}
