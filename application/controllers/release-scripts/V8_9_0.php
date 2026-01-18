<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_9_0 extends CI_Controller
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
        $this->update_whats_new_flag();
        $this->generate_autonumber_draft_einvoicing();
        $this->grant_users_to_access_new_permissions();
        $this->write_log($this->log_path, 'End migration script');
    }

    public function update_whats_new_flag() {
        // check if class's name contains 0 at the end, this means it is a major/minor release. Only major/minor releases will have new release notes
        if(substr(get_class($this), -1) == '0') {
            $this->write_log($this->log_path, 'Start updating whats new flag');
            $this->load->model("user", "userfactory");
            $this->user = $this->userfactory->get_instance();
            $this->user->set_users_whats_new_flag();
            $this->write_log($this->log_path, 'End updating whats new flag');
        }
    }
    public function generate_autonumber_draft_einvoicing()
    {
        $this->write_log($this->log_path, 'Start Updating Draft INV/CN (auto-numbring)');
        $query = $this->db->query("SELECT id FROM organizations where e_invoicing = 'active'");
        $organizations = $query->result_array();
        foreach ($organizations as $organization) {
            $i_refnum = 0;
            $query_vouchers = $this->db->query("SELECT id FROM voucher_headers WHERE refNum='0' AND voucherType = 'IVDRFT' AND organization_id = '{$organization['id']}';");
            $vouchers = $query_vouchers->result_array();
            foreach ($vouchers as $voucher) {
                $i_refnum++;
                $this->db->query("UPDATE voucher_headers set refNum = '{$i_refnum}' WHERE id = '{$voucher['id']}'");
                $this->db->query("UPDATE invoice_headers set draft_invoice_number = '{$i_refnum}' WHERE voucher_header_id = '{$voucher['id']}'");
            }
            $c_refnum = 0;
            $query_crn = $this->db->query("SELECT id FROM credit_note_headers WHERE prefix='DRFT' AND paid_status = 'draft' AND organization_id = '{$organization['id']}';");
            $crns = $query_crn->result_array();
            foreach ($crns as $crn) {
                $c_refnum++;
                $this->db->query("UPDATE credit_note_headers set credit_note_number = '{$c_refnum}', draft_credit_note_number='{$c_refnum}' WHERE id = '{$crn['id']}'");
            }
        }
        $this->write_log($this->log_path, 'End Updating Draft INV/CN numbering');
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
                if ($module === 'contract') {
                    $new_permissions = $group_permissions;
                    if (in_array('/contract_document_generator/index/', $group_permission)) {
                        array_push($new_permissions['contract'], '/folder_templates/index/');
                        array_push($new_permissions['contract'], '/folder_templates/edit/');
                        array_push($new_permissions['contract'], '/folder_templates/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
}
