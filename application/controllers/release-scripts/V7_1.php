<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
class V7_1 extends CI_Controller
{
    public $log_path = null;

    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->hooks->enabled = false;
        $this->log_path = 'release-scripts' . DIRECTORY_SEPARATOR . get_class($this);
        $this->write_log($this->log_path, 'start migration script');
    }
    public function index()
    {
        $this->write_log($this->log_path, 'start script: add_trust_asset_accounts_per_org');
        $this->add_trust_asset_accounts_per_org();
        $this->update_attachment_extensions_config_file();
    }
    public function write_log($file_path, $message, $type = 'info')
    {
        $log_path = FCPATH . 'files' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR . $file_path . '.log';
        $pr = fopen($log_path, 'a');
        fwrite($pr, date('Y-m-d H:i:s') . " [$type] - $message \n");
        fclose($pr);
        if ($type == 'error') {
            echo $type.': '.$message .". Please check the log file '$log_path' for more details and to fix the error";
            exit;
        }
    }
    public function add_trust_asset_accounts_per_org()
    {
        $this->write_log($this->log_path, 'add_trust_asset_accounts_per_org: started');
        $query = 'SELECT id, currency_id from organizations';
        $results = $this->db->query($query)->result_array();
        $organizations = array();
        $this->load->model('system_preference');
        foreach ($results as $row) {
            $organizations[$row['id']] = $row['currency_id'];
        }
        $trust_asset_account = $this->db->query("SELECT id from accounts_types where name='Trust Asset Account' AND type='Asset'")->result_array();
        
        foreach ($organizations as $id => $currency_id) {
            $this->write_log($this->log_path, "add_trust_asset_accounts_per_org: org {$id}");
            $this->db->query("INSERT INTO accounts (organization_id, currency_id, account_type_id, name, systemAccount, description, model_name, model_type,number) VALUES ({$id},{$currency_id}, {$trust_asset_account[0]['id']}, 'Trust Asset Account', 'yes', '', 'internal', 'internal', 1)");
            $inserted = $this->db->insert_id();
            $system_trust_accounts[$id] = $inserted;
            $this->write_log($this->log_path, "add_trust_asset_accounts_per_org: insert account for oreg {$id} is {$inserted}");
        }
        $this->write_log($this->log_path, 'add_default_system_trust_asset_accounts_per_org: started');
         
        $this->system_preference->set_value_by_key('trustAccount', 'trustAssetAccount', serialize($system_trust_accounts));
          
        $this->write_log($this->log_path, 'add_default_system_trust_asset_accounts_per_org: done');
        $this->write_log($this->log_path, 'add_trust_asset_accounts_per_org: done');
    }
    public function update_attachment_extensions_config_file()
    {
        $allowed_file_uploads_path = FCPATH . 'application' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'allowed_file_uploads.php';
        $this->write_log($this->log_path,'update attachment extensions in config file');
        if (@$file_content = file_get_contents($allowed_file_uploads_path)) {
            if(!strpos($file_content, 'mov|wav|3gp|avi')) {
                $file_content = str_replace("';", "|mov|wav|3gp|avi';", $file_content);
                $excluded     = "\$config['organization_invoice_templates'] = 'gif|jpg|jpeg|png';";
                $updated_excluded = "\$config['organization_invoice_templates'] = 'gif|jpg|jpeg|png|mov|wav|3gp|avi';";
                $file_content = str_replace($updated_excluded, $excluded, $file_content);
                $excluded     = "\$config['user_profile_picture'] = 'gif|jpg|jpeg|png';";
                $updated_excluded = "\$config['user_profile_picture'] = 'gif|jpg|jpeg|png|mov|wav|3gp|avi';";
                $file_content = str_replace($updated_excluded, $excluded, $file_content);
            }
            if (@!file_put_contents($allowed_file_uploads_path, $file_content)) {
                $this->write_log($this->log_path,"failed to put content to file $allowed_file_uploads_path");
            }
        } else {
            $this->write_log($this->log_path,"failed to get content of file $allowed_file_uploads_path");
        }
        $this->write_log($this->log_path,'Done - update attachment extensions config file');
    }
}
