<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_17 extends CI_Controller
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
        $this->add_file_extensions();
        $this->write_log($this->log_path, 'End migration script');
    }

    /**
     * add new file types: dwg and dwf
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
                        file_put_contents($allowed_file_uploads_path, str_replace($new_line, substr($line, $str_pos, strpos(substr($line, $str_pos, strlen($line)), "';")) . "|dwg|dwf';", file_get_contents($allowed_file_uploads_path)));
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
