<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V7_16_1 extends CI_Controller
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
        $this->update_database_file();
    }

    public function update_database_file()
    {
        $this->load->model('instance_data');
        $data = $this->instance_data->get_value_by_key('installationType');
        if ($data && isset($data['keyValue']) && $data['keyValue'] === 'on-cloud') {
            $this->write_log($this->log_path, 'Database update file starts');
            $database_file_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';
            $new_content = str_replace('$CI', '//$CI', file_get_contents($database_file_path));
            if (@!file_put_contents($database_file_path, $new_content)) {
                $this->write_log($this->log_path, "failed to put content to file $database_file_path", 'error');
            } else {
                $this->write_log($this->log_path, "Database File is updated");
            }
            $new_content = str_replace('if', '//if', file_get_contents($database_file_path));
            if (@!file_put_contents($database_file_path, $new_content)) {
                $this->write_log($this->log_path, "failed to put content to file $database_file_path", 'error');
            } else {
                $this->write_log($this->log_path, "Database File is updated");
            }
            $new_content = str_replace('exit', '//exit', file_get_contents($database_file_path));
            if (@!file_put_contents($database_file_path, $new_content)) {
                $this->write_log($this->log_path, "failed to put content to file $database_file_path", 'error');
            } else {
                $this->write_log($this->log_path, "Database File is updated");
            }
        }else{
            $this->write_log($this->log_path, "On server installation - No update will be done");

        }
    }
}
