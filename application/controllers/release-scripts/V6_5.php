<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_5 extends Top_controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->generate_api_key_for_ad_users();
        $this->add_administrator_grp_for_saved_filters_grids();
    }
    /*
     * generate api key for the AD users who don't have an api key
     */

    public function generate_api_key_for_ad_users()
    {
        $this->load->model('user', 'Userfactory');
        $this->user = $this->userfactory->get_instance();
        $ad_users_ids = $this->user->load_all(array('select' => array('id'), 'where' => array("isAd = 1 AND (api_key = '' OR api_key IS NULL)")));
        foreach ($ad_users_ids as $user_id) {
            $this->user->reset_fields();
            if ($this->user->fetch(array('id' => $user_id['id']))) {
                $this->user->set_field('api_key', $this->is_auth->_encode($user_id['id']));
                $this->user->update();
            }
        }
    }
    /*
     * add administrator group as a default value for grid saved filter if gridsAdminUserGroups in system_preferences has no value else keep the value as it is
     */

    public function add_administrator_grp_for_saved_filters_grids()
    {
        $this->load->model('system_preference');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        if ($this->user_group->fetch(array('name' => 'Administrator'))) {
            $administrator_grp_id = $this->user_group->get_field('id');
            $grids_admin_usr_grps = $this->system_preference->get_value_by_key('gridsAdminUserGroups');
            if (isset($grids_admin_usr_grps['keyValue']) && !$grids_admin_usr_grps['keyValue'] || !isset($grids_admin_usr_grps['keyValue'])) {
                $this->system_preference->set_value_by_key('DefaultValues', 'gridsAdminUserGroups', $administrator_grp_id);
            }
        }
    }
}
