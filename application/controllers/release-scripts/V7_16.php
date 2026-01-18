<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V7_16 extends CI_Controller
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
        $this->update_report_builder_permissions();
    }
    /*     * *
     * (check if user group has action permission to the report builder modules then add this permission to report builder view
     * @access public
     */

    public function update_report_builder_permissions()
    {
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        $this->load->model('user_group_permission');
        foreach ($user_groups as $group) {
            $group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (in_array('/reports/', $group_permissions['core']) || in_array('/reports/report_builder/', $group_permissions['core']) ) {
                array_push($group_permissions['core'], '/reports/report_builder_view/');
            }
            $this->user_group_permission->set_permission_data($group['id'], $group_permissions);
        }
        $this->write_log($this->log_path, 'end of migration script');
    }
}
