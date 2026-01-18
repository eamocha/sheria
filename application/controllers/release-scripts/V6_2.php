<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
require(APPPATH . "controllers/Top_controller.php");

class V6_2 extends Top_controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->index();
    }

    public function index()
    {
        $this->update_case_permissions();
        $this->update_global_partner_share_acc();
    }

    /***
    function description (check if user group has '/cases/edit' action permission then if the group
    dosen't have '/cases/move_status/' permission it will be granted by update)
    * @access public

    */
    public function update_case_permissions()
    {
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $user_groups = $this->user_group->load_all(array('select' => array('id, name')));
        $this->load->model('user_group_permission');
        foreach ($user_groups as $group) {
            $group_permissions = $this->user_group_permission->get_permissions($group['id'], false);
            if (isset($group_permissions['core']) && in_array('/cases/edit/', $group_permissions['core'])) {
                if (!in_array("/cases/move_status/", $group_permissions['core'])) {
                    array_push($group_permissions['core'], '/cases/move_status/');
                    $this->user_group_permission->set_permission_data($group['id'], $group_permissions);
                }
            }
        }
    }
    public function update_global_partner_share_acc()
    {
        $comissions = $this->system_preference->get_value_by_key('systemCommissionAccount');
        $comission_accounts = unserialize($comissions['keyValue']);
        $this->db->select('id,currency_id');
        $query =  $this->db->get('organizations');
        if ($query->num_rows()) {
            foreach ($query->result() as $row) {
                $account_ids = $this->db->select('id')->where('name', 'Global Partner Share Account')->where('organization_id', $row->id)->get('accounts');
                if ($account_ids->num_rows()) {
                    foreach ($account_ids->result() as $account) {
                        $data = array(
                          'name' => 'Partner Expenses',
                          'account_type_id' => '11',
                        );
                        $this->db->where('id', $account->id);
                        $this->db->update('accounts', $data);
                    }
                } else {
                    $data = array(
                        'organization_id' => $row->id ,
                        'currency_id' =>  $row->currency_id,
                        'account_type_id' => '11' ,
                        'name' => 'Partner Expenses' ,
                        'systemAccount' => 'no' ,
                        'description' => '' ,
                        'model_name' => 'internal',
                        'model_type' => 'internal'
                     );
                    $this->db->insert('accounts', $data);
                    if ($comission_accounts && isset($comission_accounts[$row->id])) {
                        unset($comission_accounts[$row->id]);
                        $this->system_preference->set_value_by_key('InvoiceValues', 'systemCommissionAccount', serialize($comission_accounts));
                    }
                }
            }
        }
    }
}
