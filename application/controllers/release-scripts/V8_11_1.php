<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_11_1 extends CI_Controller
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
        $this->remove_sort_from_litigation_corporate_grid();
        $this->write_log($this->log_path, 'end migration script');
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

    public function remove_sort_from_litigation_corporate_grid()
    {
        $this->write_log($this->log_path, 'start remove sort from litigation and corporate grid');
        $models = ['Litigation', 'Matter'];
        $this->load->model('grid_saved_column');
        foreach ($models as $model) {
            $grid_saved_columns = $this->grid_saved_column->load_all(['where' => ['model', $model]]);
            if (!empty($grid_saved_columns)) {
                foreach ($grid_saved_columns as $grid_saved_column) {
                    if (!empty($grid_saved_column['grid_details'])) {
                        $grid_details = unserialize($grid_saved_column['grid_details']);
                        if (!empty($grid_details['sort'])) {
                            $grid_details['sort'] = '';
                            $grid_details = serialize($grid_details);
                            $query = "UPDATE grid_saved_columns SET grid_details = '{$grid_details}' WHERE id = {$grid_saved_column['id']}";
                            $this->db->query($query);
                        }
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'end remove sort from litigation and corporate grid');
    }
}
