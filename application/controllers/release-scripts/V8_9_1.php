<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_9_1 extends CI_Controller
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
        $this->fix_client_name_in_user_activity_log_grid_saved_filers('User_Activity_Log');
        $this->fix_client_name_in_user_activity_log_grid_saved_filers('User_Activity_Log_Money_Module');
        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_whats_new_flag()
    {
        // check if class's name contains 0 at the end, this means it is a major/minor release. Only major/minor releases will have new release notes
        if (substr(get_class($this), -1) == '0') {
            $this->write_log($this->log_path, 'Start updating whats new flag');
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->user->set_users_whats_new_flag();
            $this->write_log($this->log_path, 'End updating whats new flag');
        }
    }

    public function fix_client_name_in_user_activity_log_grid_saved_filers($model) {
        $this->write_log($this->log_path, 'start fixing client name column in user activity log grid saved filters table');
        $this->load->model('grid_saved_column');
        $grid_saved_columns = $this->grid_saved_column->fetch(array('model' => $model));
        if($grid_saved_columns) {
            $grid_saved_columns_grid_details = unserialize($this->grid_saved_column->get_field('grid_details'));
            if (!empty($grid_saved_columns_grid_details['selected_columns'])) {
                $column_key = array_search('allRecordsClientName', $grid_saved_columns_grid_details['selected_columns']);
                if ($column_key) {
                    $grid_saved_columns_grid_details['selected_columns'][$column_key] = 'clientName';
                    $grid_saved_columns_grid_details = serialize($grid_saved_columns_grid_details);
                    $this->grid_saved_column->set_field('grid_details', $grid_saved_columns_grid_details);
                    $this->grid_saved_column->update();
                }
            }
        }
        $this->write_log($this->log_path, 'end renaming column');
    }
}
