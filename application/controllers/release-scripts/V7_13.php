<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_13 extends CI_Controller
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
        $this->update_allowed_file_uploads();
        $this->write_log($this->log_path, 'End migration script');
    }
    
    /**
     * add the caseContainer allowed file types to confing/allowed_file_uploads.php
     */
    public function update_allowed_file_uploads()
    {
        $this->write_log($this->log_path, 'update_allowed_file_uploads started', 'info');
        $file = getcwd() . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "allowed_file_uploads.php";
        $str = "\$config['caseContainer'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|tiff|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv|mov|wav|3gp|avi|pages';";
        $option = 'caseContainer';

        if (file_exists($file)) {
            if (!$this->config_option_already_exists($option, $file)) {
                $data = PHP_EOL . $str . PHP_EOL;

                if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
                    $this->write_log($this->log_path, '    couldn\'t modify ' . $file);
                }
            }
        } else {
            $this->write_log($this->log_path, $file . ' doesn\'t exists.', 'info');
        }

        $this->write_log($this->log_path, 'update_allowed_file_uploads is done', 'info');
    }
    
    private function config_option_already_exists($option, $file)
    {
        $this->write_log($this->log_path, 'config_option_already_exists started', 'info');
        $lines = file($file, FILE_IGNORE_NEW_LINES);
        
        foreach ($lines as $key => $line) {
            if (strpos($line, $option) > 0) {
                return true;
            }
        }
        
        $this->write_log($this->log_path, 'config_option_already_exists is done', 'info');
        
        return false;
    }
}
