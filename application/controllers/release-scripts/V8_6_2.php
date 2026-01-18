<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_6_2 extends CI_Controller
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
        $this->alter_advisor_password_field_length();
        $this->grant_users_to_access_new_permissions();
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
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if(in_array('/vouchers/invoice_download_file/', $group_permission) || in_array('/vouchers/bill_download_file/', $group_permission) || in_array('/vouchers/bill_payment_download_file/', $group_permission) || in_array('/vouchers/invoice_payment_download_file/', $group_permission) || in_array('/vouchers/expense_download_file/', $group_permission) || in_array('/vouchers/quote_download_file/', $group_permission)){
                        array_push($new_permissions['money'], '/vouchers/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function alter_advisor_password_field_length()
    {
        $this->write_log($this->log_path, 'Started alter_advisor_password_field_length');

        if (empty($filters_array)) {
            if ($this->db->dbdriver === 'sqlsrv') {
                $this->db->query("ALTER TABLE advisor_users ALTER COLUMN password NVARCHAR(255);");
            } else {
                $this->db->query("ALTER TABLE `advisor_users` MODIFY `password` VARCHAR(255);");
            }
        }

        $this->write_log($this->log_path, 'Done alter_advisor_password_field_length');
    }
}
