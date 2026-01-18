<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_6 extends CI_Controller
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
        $this->update_contract_comments();
        $this->update_license_users();
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
                    if(in_array('/cases/archive_unarchive_cases/', $group_permission)){
                        array_push($new_permissions['core'], '/dashboard/archiving/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/tasks/archive_unarchive_tasks/', $group_permission)){
                        array_push($new_permissions['core'], '/dashboard/archiving_task/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/time_tracking/edit/', $group_permission)){
                        array_push($new_permissions['core'], '/time_tracking/time_log_export_to_word/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/case_containers/edit/', $group_permission)){
                        array_push($new_permissions['core'], '/case_containers/create_slot_for_advanced_export/');
                        array_push($new_permissions['core'], '/case_containers/delete_slot_for_advanced_export/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if(in_array('/case_containers/export_to_word/', $group_permission)){
                        array_push($new_permissions['core'], '/case_containers/advanced_export_to_word/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/money_dashboard/index/', $group_permission)) {
                        array_push($new_permissions['money'], '/money_dashboards/index/');
                        array_push($new_permissions['money'], '/money_dashboards/widget_delete/');
                        array_push($new_permissions['money'], '/money_dashboards/widget_save/');
                        array_push($new_permissions['money'], '/money_dashboards/widget_update/');
                        array_push($new_permissions['money'], '/money_dashboards/money_dashboard_config/');
                        array_push($new_permissions['money'], '/money_dashboards/set_widgets_new_order/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function update_contract_comments()
    {
        $this->write_log($this->log_path, 'updating contract comments');
        $this->load->helper('format_comment_patterns');
        $contract_comments = $this->db->query("SELECT id, comment FROM contract_comment WHERE channel = 'A4L'");
        $all_contract_comments = $contract_comments->result_array();
        foreach ($all_contract_comments as $comment) {
            $this->write_log($this->log_path, 'updateing contract comment of id ' . $comment['id']);
            $new_comment = format_comment_patterns($comment['comment']);
            $this->db->query("UPDATE contract_comment SET contract_comment.comment = '" . $new_comment  . "' WHERE contract_comment.id = " . $comment['id']);
        }
        $this->write_log($this->log_path, 'done from updating contract comments');
    }
    public function update_license_users()
    {
        $this->write_log($this->log_path, 'updating license user type');
        $core_path = substr(COREPATH, 0, -12);
        $contract_license_file_path = $core_path . 'modules' . DIRECTORY_SEPARATOR . 'contract' . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'config' .  DIRECTORY_SEPARATOR . 'license.php';
        if (is_file($contract_license_file_path)) {
            $fibonacci_str = file_get_contents($contract_license_file_path);
            $b64_license = $this->license_decode($fibonacci_str);
            if ($b64_license) {
                $this->db->query("UPDATE users SET users.type = 'both'");
            }
        }
        $this->write_log($this->log_path, 'done from updating license user type');
    }
    private function license_decode($str)
    {
        $a4l_encoded = base64_encode('INFOSYSTA-LICENSE-KEYWORD');
        $key_length = strlen($a4l_encoded);
        if (empty($str) || (!empty($str) && substr($str, -$key_length) !== $a4l_encoded)) {
            return false;
        } else {
            $str = substr($str, 0, -$key_length);
        }
        $length = strlen($str);
        $newStr = '';
        $fibonacciPositions = array();
        $i = 0;
        $Un2 = -1;
        $Un1 = 1;
        while ($i < $length) {
            $Un = $Un1 + $Un2;
            $Un2 = $Un1;
            $Un1 = $Un;
            $fibonacciPositions[] = $Un + $i;
            if (!in_array($i, $fibonacciPositions)) {
                $newStr .= $str[$i];
            }
            $i++;
        }
        return base64_decode($newStr);
    }
}
