<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_8 extends CI_Controller {

    use MigrationLogTrait;

    public $log_path = null;

    public function __construct() {
        parent::__construct();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->load->database();
        $this->load->model('instance_data');
    }


    public function index() {
        $this->change_money_dashboard();
        $this->update_allowed_files_uploads();
        $this->update_permission_tasks();
    }

    /**
     * Replace the money dashboard URL 'dashboard' to the new controller URL 'money_dashboard'
     * in the user group permissions table
     */
    private function change_money_dashboard(){
        $this->write_log($this->log_path, "START :: change_money_dashboard ...");
        $this->replace_user_group_permission_url('money', '/dashboard/', '/money_dashboard/');
        $this->write_log($this->log_path, "END :: change_money_dashboard ...");
    }

    private function replace_user_group_permission_url($target_module, $old_url, $new_url){
        $this->write_log($this->log_path, "START :: replace_user_group_permission_url ...");
        $this->load->model('user_group_permission');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all();

        foreach($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);

            foreach($group_permissions as $module => $group_permission){
                if($module == $target_module){
                    for($i = 0; $i < count($group_permission); $i++){
                        if(strpos($group_permission[$i], $old_url) !== false){
                            $group_permission[$i] = str_replace($old_url, $new_url, $group_permission[$i]);
                            $group_permissions[$module] = $group_permission;
                            $this->user_group_permission->set_permission_data($user_group['id'], $group_permissions);
                        }
                    }
                }
            }
        }

        $this->write_log($this->log_path, "END :: replace_user_group_permission_url ...");
    }
    
    public function update_allowed_files_uploads(){
        $this->write_log($this->log_path, 'Changes on allowed files upload started', 'info');
        if (@$file_content = file_get_contents(FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php')) {
            $file_content .= "\n\$config['task'] = 'doc|docx|xls|xlsx|pps|ppt|pptx|pdf|tif|jpg|png|gif|jpeg|bmp|html|htm|txt|msg|eml|vcf|zip|rar|mpg|mp3|mp4|flv|mov|wav|3gp|avi';";
            if (@!file_put_contents(FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php', $file_content)) {
                $this->write_log($this->log_path, "failed to put content to file '" . FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "allowed_file_uploads.php'");
            }
        } else {
            $this->write_log($this->log_path, "failed to get content of file '" . FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . "allowed_file_uploads.php'");
        }
        $this->write_log($this->log_path, 'Changes on allowed files upload is done', 'info');
    }
    
    public function update_permission_tasks()
    {
        $this->write_log($this->log_path, "START :: update task view permission depending on task edit ...");
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    if(in_array('/tasks/edit/', $group_permission)){
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['core'], '/tasks/view/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, "END :: update task view permission depending on task edit ...");

    }
}
