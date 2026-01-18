<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5_5 extends CI_Controller
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
        $this->grant_users_to_access_new_permissions();
        $this->update_instance_config_file();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function grant_users_to_access_new_permissions()
    {
        $this->write_log($this->log_path, 'add new actions to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group) {
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach ($group_permissions as $module => $group_permission) {
                if ($module === 'core') {
                    $new_permissions = $group_permissions;
                    if(in_array('/docs/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/docs/download_docs_zip_file/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/companies/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/companies/download_docs_zip_file/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/contacts/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/contacts/download_docs_zip_file/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/contracts/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/contracts/download_docs_zip_file/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/cases/download_file/', $group_permission)){
                        array_push($new_permissions['core'], '/cases/download_docs_zip_file/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    private function check_config_parameter($option, $file)
    {
        $this->write_log($this->log_path, 'check_config_parameter started', 'info');
        $lines = file($file, FILE_IGNORE_NEW_LINES);

        foreach ($lines as $key => $line) {
            if (strpos($line, $option) > 0) {
                return true;
            }
        }
        $this->write_log($this->log_path, 'check_config_parameter is done', 'info');
        return false;
    }

    public function update_instance_config_file(){
        $this->write_log($this->log_path, 'update instance config file');
        $file = getcwd() . DIRECTORY_SEPARATOR . "application" . DIRECTORY_SEPARATOR . "config" . DIRECTORY_SEPARATOR . "instance.php";
        $this->load->model('instance_data');
        $data = $this->instance_data->get_value_by_key('installationType');
        if ($data && isset($data['keyValue']) && $data['keyValue'] === 'on-cloud') {
            $str = "\$config['allow_download_folder'] = false;";
        }else{
            $str = "\$config['allow_download_folder'] = true;";
        }
        $option = 'allow_download_folder';
        if (file_exists($file)) {
            if (!$this->check_config_parameter($option, $file)) {
                $data = PHP_EOL . $str . PHP_EOL;
                if (!file_put_contents($file, $data, FILE_APPEND | LOCK_EX)) {
                    $this->write_log($this->log_path, '    couldn\'t modify ' . $file);
                }
            }
        } else {
            $this->write_log($this->log_path, $file . ' doesn\'t exists.', 'info');
        }
        $this->write_log($this->log_path, 'update_instance_config_file is done', 'info');
    }



}
