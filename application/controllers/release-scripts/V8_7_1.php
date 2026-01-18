<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_7_1 extends CI_Controller
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
        $this->template_folder_path_for_contract();
        $this->remove_extensions_from_allowed_file_uploads();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function remove_extensions_from_allowed_file_uploads(){
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';
        $this->write_log($this->log_path,'remove html and htm from allowed extensions in config file');
        if (@$file_content = file_get_contents($allowed_file_uploads_path)) {
            if(strpos($file_content, 'html|htm|')) {
                $file_content = str_replace("html|htm|", "", $file_content);
                if (@!file_put_contents($allowed_file_uploads_path, $file_content)) {
                    $this->write_log($this->log_path,"failed to put content to file $allowed_file_uploads_path");
                }else{
                    $this->write_log($this->log_path,'Done - update allowed extensions in config file');
                }
            }
        } else {
            $this->write_log($this->log_path,"failed to get content of file $allowed_file_uploads_path");
        }
    }

    public function template_folder_path_for_contract()
    {
        $this->write_log($this->log_path, 'start migrating template folder path for contract');
        $this->load->model('doc_generator');
        $template_folder_path = $this->doc_generator->get_value_by_key('template_folder_path');
        if($template_folder_path){
            $this->doc_generator->set_value_by_key('contract_template_folder_path', $template_folder_path);
        }
        $this->write_log($this->log_path, 'End migrating template folder path for contract');
    }

}
