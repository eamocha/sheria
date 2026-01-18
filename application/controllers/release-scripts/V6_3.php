<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_3 extends Top_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->update_calendars_permissions();
    }
    /*     * *
     * (check if user group has action permission to the calendars modules then add this permission to calendars/(add/edit/delete/list) and delete the calendars module permission
     * @access public
     */

    public function update_calendars_permissions()
    {
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        $this->load->model('user_group_permission');
        foreach ($user_groups as $group) {
            $group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($group_permissions['calendar'])) {
                if (in_array('/', $group_permissions['calendar'])) {
                    array_push($group_permissions['core'], '/calendars/');
                } else {
                    if (in_array('/calendars/index/', $group_permissions['calendar'])) {
                        array_push($group_permissions['core'], '/calendars/view/');
                    }
                    if (in_array('/calendars/add/', $group_permissions['calendar'])) {
                        array_push($group_permissions['core'], '/calendars/add/');
                    }
                    if (in_array('/calendars/edit/', $group_permissions['calendar'])) {
                        array_push($group_permissions['core'], '/calendars/edit/');
                    }
                    if (in_array('/calendars/delete/', $group_permissions['calendar'])) {
                        array_push($group_permissions['core'], '/calendars/delete/');
                    }
                }
                unset($group_permissions['calendar']);
                $this->user_group_permission->set_permission_data($group['id'], $group_permissions);
            }
        }
    }
}
