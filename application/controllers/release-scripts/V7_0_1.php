<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V7_0_1 extends Top_controller
{
    public $log_path = null;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        ini_set('max_execution_time', 0);
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
        $this->index();
    }

    public function index()
    {
        $this->write_log($this->log_path, 'start script: fix_permissions');
        $this->fix_permissions();
    }

    public function fix_permissions()
    {
        $this->write_log($this->log_path, 'fix_permissions: started');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $modules = array(
            'document' => array(
                'permission_module' => 'core',
                'controller' => 'docs',
                'list_document_permission' => '/documents/',
                'list_document_permission_update' => '/docs/'
            )
        );
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        foreach ($user_groups as $group) {
            $this->write_log($this->log_path, "fix_permissions: group {$group['id']} ");
            $user_group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($user_group_permissions['core'])) {
                $this->write_log($this->log_path, "fix_permissions: core module");
                foreach ($modules as $module_key => $module_properties) {
                    if (in_array($module_properties['list_document_permission'], $user_group_permissions['core'])) {
                        $this->write_log($this->log_path, "fix_permissions: doc permission is exists");
                        $user_group_permissions['core'][] = "/{$module_properties['controller']}/list_file_versions/";
                        if (isset($module_properties['list_document_permission_update'])) {
                            $action_key = array_search($module_properties['list_document_permission'], $user_group_permissions['core']);
                            $user_group_permissions['core'][$action_key] = $module_properties['list_document_permission_update'];
                        }
                    }
                }
                $this->user_group_permission->set_permission_data($group['id'], $user_group_permissions);
            }
        }
        $this->write_log($this->log_path, 'fix_permissions: done');
    }
}
