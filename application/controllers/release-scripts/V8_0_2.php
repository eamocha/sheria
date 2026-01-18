<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_0_2 extends CI_Controller
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
        $this->fix_wrong_tax_amount_in_voucher_details();
        $this->fix_contact_company_categories();
        $this->write_log($this->log_path, 'done from migration script');
    }
    
    public function fix_wrong_tax_amount_in_voucher_details(){
        if($this->db->dbdriver == 'mysqli'){
            $this->write_log($this->log_path, 'start fixing tax amount in voucher details table');
            $query = 'SELECT voucher_details.id, voucher_details.voucher_header_id, voucher_details.local_amount, voucher_details.foreign_amount FROM voucher_details LEFT JOIN voucher_headers ON voucher_headers.id = voucher_details.voucher_header_id LEFT JOIN accounts ON accounts.id = voucher_details.account_id WHERE voucher_headers.voucherType = "EXP" AND voucher_details.local_amount = voucher_details.foreign_amount AND accounts.account_type_id = 7';
            $tax_transactions = $this->db->query($query)->result_array();
            foreach($tax_transactions as $tax_transaction){
                $this->write_log($this->log_path, 'load paid through transaction for voucher header #' . $tax_transaction['voucher_header_id']);
                $query = 'SELECT voucher_details.id, voucher_details.local_amount, voucher_details.foreign_amount FROM voucher_details WHERE voucher_details.voucher_header_id = "' . $tax_transaction['voucher_header_id'] . '" AND voucher_details.drCr = "C"';
                $paid_through_transaction = $this->db->query($query)->result_array();
                if(!empty($paid_through_transaction) && count($paid_through_transaction) == 1){
                    $this->write_log($this->log_path, 'calculate tax foreign name for voucher header #' . $tax_transaction['voucher_header_id']);
                    $tax_account_rate = $paid_through_transaction[0]['foreign_amount'] / $paid_through_transaction[0]['local_amount'];
                    $tax_new_foreign_amount = $tax_transaction['foreign_amount'] / $tax_account_rate;
                    $this->write_log($this->log_path, 'load expense category for voucher header #' . $tax_transaction['voucher_header_id']);
                    $query = 'SELECT voucher_details.id, voucher_details.local_amount, voucher_details.foreign_amount FROM voucher_details WHERE voucher_details.voucher_header_id = "' . $tax_transaction['voucher_header_id'] . '" AND voucher_details.drCr = "D" and voucher_details.id != "' . $tax_transaction['id'] . '"';
                    $expense_category_transaction = $this->db->query($query)->result_array();
                    if(!empty($expense_category_transaction) && count($expense_category_transaction) == 1){
                        $this->write_log($this->log_path, 'calculate tax local name for voucher header #' . $tax_transaction['voucher_header_id']);
                        $tax_new_local_amount = $paid_through_transaction[0]['local_amount'] - $expense_category_transaction[0]['local_amount'];
                        if($tax_new_foreign_amount > 0 && $tax_new_local_amount > 0 && $tax_new_local_amount != $tax_transaction['local_amount']){
                            $this->write_log($this->log_path, 'update tax local amount and foreign name for voucher header #' . $tax_transaction['voucher_header_id']);
                            $this->db->query('UPDATE voucher_details SET local_amount = "' . $tax_new_local_amount . '", foreign_amount = "' . $tax_new_foreign_amount . '" WHERE voucher_details.id = "' . $tax_transaction['id'] . '"');
                        }
                    }
                }
                $this->write_log($this->log_path, 'done from voucher header #' . $tax_transaction['voucher_header_id']);
            }
            $this->write_log($this->log_path, 'end fixing tax amount');
        }
    }
    
    public function fix_contact_company_categories(){
        $this->write_log($this->log_path, 'fix contact/company categories');
        
        $this->write_log($this->log_path, 'add keyName to table');
        if($this->db->dbdriver == 'mysqli'){
            $this->db->query("ALTER TABLE contact_company_categories ADD keyName VARCHAR(255) NOT NULL");
        }else{
            $this->db->query("ALTER TABLE contact_company_categories ADD keyName nvarchar(255) NULL");
        }
        
        $this->write_log($this->log_path, 'set keyName values');
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Client' WHERE name in ('Client', 'موكل', 'Cliente');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'External Advisor' WHERE name in ('External Advisor', 'مستشار خارجي', 'Asesor externo');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Internal' WHERE name in ('Internal', 'داخلي', 'Interno');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Lead' WHERE name in ('Lead', 'موكل محتمل', 'Dirigir');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Not Categorized' WHERE name in ('Not Categorized', 'غير مصنف', 'No categorizado');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Partner' WHERE name in ('Partner', 'شريك', 'Compañero');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Prospect' WHERE name in ('Prospect', 'موكل متوقع', 'Perspectiva');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Supplier' WHERE name in ('Supplier', 'مورد', 'Proveedor');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Opponent' WHERE name in ('Opponent', 'خصم', 'Adversario');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Other' WHERE name in ('Other', 'آخر', 'Otro');");
        $this->db->query("UPDATE contact_company_categories SET keyName = 'Third Party' WHERE name in ('Third Party', 'الطرف الثالث', 'Tercero');");
        
        $this->write_log($this->log_path, 'add english sample data when table is empty');
        $query = 'SELECT * FROM contact_company_categories';
        $categories = $this->db->query($query)->result_array();
        if(empty($categories)){
            if($this->db->dbdriver == 'mysqli'){
                $this->write_log($this->log_path, 'insert contact/company categories');
                $this->db->query("INSERT INTO `contact_company_categories` (`id`, `keyName`, `name`, `color`) VALUES
                    (1, 'Client', 'Client', '#5CB85C'),
                    (2, 'External Advisor', 'External Advisor', '#A9A9A9'),
                    (3, 'Internal', 'Internal', '#006400'),
                    (4, 'Lead', 'Lead', '#CCCC00'),
                    (5, 'Not Categorized', 'Not Categorized', '#cfcfcf'),
                    (6, 'Partner', 'Partner', '#3A87AD'),
                    (7, 'Prospect', 'Prospect', '#F89406'),
                    (8, 'Supplier', 'Supplier', '#858585'),
                    (9, 'Opponent', 'Opponent', '#fbb450'),
                    (10, 'Other', 'Other', '#A9A9A9'),
                    (11, 'Third Party', 'Third Party', '#67abcc');");
            }
        }else{
            $this->write_log($this->log_path, 'table already has data');
        }
        $this->write_log($this->log_path, 'end fixing contact/company categories');
    }
}