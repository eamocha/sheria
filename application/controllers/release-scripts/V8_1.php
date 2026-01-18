<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_1 extends CI_Controller
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
        $this->update_litigation_permissions();
        $this->fix_empty_expense_castegory_name_in_invoice_details();
        $this->write_log($this->log_path, 'done from migration script');
    }
    
    public function fix_empty_expense_castegory_name_in_invoice_details()
    {
        $this->write_log($this->log_path, 'fixing expense category name in invoice details table');
        $query = "SELECT invoice_details.id, invoice_details.expense_id, invoice_details.item, expense_categories.name, invoice_headers.paidStatus, expenses.billingStatus FROM invoice_details LEFT JOIN invoice_headers ON invoice_headers.id = invoice_details.invoice_header_id LEFT JOIN expenses ON expenses.id = invoice_details.expense_id LEFT JOIN expense_categories ON expense_categories.id = expenses.expense_category_id WHERE expense_id IS NOT NULL AND item = ''";
        $invoice_details = $this->db->query($query)->result_array();
        foreach($invoice_details as $invoice_detail){
            $this->write_log($this->log_path, 'update expense category name for invoice detail id ' . $invoice_detail['id']);
            $this->db->query('UPDATE invoice_details SET item = "' . $invoice_detail['name'] . '" WHERE id = "' . $invoice_detail['id'] . '"');
            if($invoice_detail['paidStatus'] === 'paid' && $invoice_detail['billingStatus'] !== 'reimbursed'){
                $this->write_log($this->log_path, 'update expense status to "reimbursed" for invoice detail id ' . $invoice_detail['id']);
                $this->db->query('UPDATE expenses SET billingStatus = "reimbursed" WHERE id = "' . $invoice_detail['expense_id'] . '"');
            }
            if($invoice_detail['paidStatus'] === 'open' && $invoice_detail['billingStatus'] !== 'invoiced'){
                $this->write_log($this->log_path, 'update expense status to "invoiced" for invoice detail id ' . $invoice_detail['id']);
                $this->db->query('UPDATE expenses SET billingStatus = "invoiced" WHERE id = "' . $invoice_detail['expense_id'] . '"');
            }
        }
        $this->write_log($this->log_path, 'fixing expense category name ended');

    }
    
    public function update_litigation_permissions(){
        $this->write_log($this->log_path, 'add new action to user permissions');
        $this->load->model('user_group', 'user_groupfactory');
        $this->user_group = $this->user_groupfactory->get_instance();
        $this->load->model('user_group_permission');
        $user_groups = $this->user_group->load_all();
        foreach ($user_groups as $user_group){
            $group_permissions = $this->user_group_permission->get_permissions($user_group['id'], false);
            foreach($group_permissions as $module => $group_permission){
                if($module === 'core'){
                    if(in_array('/cases/litigation/', $group_permission)){
                        $this->write_log($this->log_path, 'grant user group #' . $user_group['id'] . ' to change litigation stage');
                        $new_permissions = $group_permissions;
                        array_push($new_permissions['core'], '/cases/change_litigation_stage/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
}
