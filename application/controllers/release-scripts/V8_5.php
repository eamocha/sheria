<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH.'libraries/traits/MigrationLogTrait.php';

class V8_5 extends CI_Controller
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
        $this->grant_users_to_access_new_permissions();
        $this->move_voucher_related_case_id_to_new_table();
        $this->update_themes_tabs();
        $this->contract_summary_to_approval_summary();
        $this->contract_summary_to_signature_summary();
        $this->write_log($this->log_path, 'End migration script');
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
                if ($module === 'core') {
                    $new_permissions = $group_permissions;
                    if(in_array('/tasks/edit/', $group_permission) || in_array('/tasks/add/', $group_permission)){
                        array_push($new_permissions['core'], '/tasks/matter_upload_file/');
                        array_push($new_permissions['core'], '/tasks/matter_load_documents/');
                        array_push($new_permissions['core'], '/tasks/return_matter_doc_thumbnail/');
                        array_push($new_permissions['core'], '/tasks/matter_download_file/');
                        array_push($new_permissions['core'], '/tasks/matter_delete_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);

                    }
                    if (in_array('/cases/litigation_case/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/list_hearings/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/case_containers/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/case_containers/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/cases/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/companies/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/companies/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/contacts/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/contacts/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/docs/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/docs/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/intellectual_properties/upload_file/', $group_permission)) {
                        array_push($new_permissions['core'], '/intellectual_properties/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/export/time_types/', $group_permission)) {
                        array_push($new_permissions['core'], '/export/time_internal_statuses/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/time_types/', $group_permission)|| in_array('/time_types/add/', $group_permission)) {
                        array_push($new_permissions['core'], '/time_internal_statuses/');
                        array_push($new_permissions['core'], '/time_internal_statuses/index/');
                        array_push($new_permissions['core'], '/time_internal_statuses/add/');
                        array_push($new_permissions['core'], '/time_internal_statuses/edit/');
                        array_push($new_permissions['core'], '/time_internal_statuses/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'contract') {
                    $new_permissions = $group_permissions;
                    if (in_array('/contracts/upload_file/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/upload_directory/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/reports/trial_balance/', $group_permission)) {
                        array_push($new_permissions['money'], '/reports/general_ledger/');
                        array_push($new_permissions['money'], '/reports/detailed_general_ledger/');
                        array_push($new_permissions['money'], '/reports/export_excel_detailed_general_ledger/');
                        array_push($new_permissions['money'], '/reports/export_excel_general_ledger/');
                        array_push($new_permissions['money'], '/reports/export_pdf_detailed_general_ledger/');
                        array_push($new_permissions['money'], '/reports/export_pdf_general_ledger/');
                        
                        array_push($new_permissions['money'], '/reports/receivables/');
                        array_push($new_permissions['money'], '/reports/export_excel_receivables/');
                        array_push($new_permissions['money'], '/reports/export_pdf_receivables/');
                        array_push($new_permissions['money'], '/reports/payables/');
                        array_push($new_permissions['money'], '/reports/export_excel_payables/');
                        array_push($new_permissions['money'], '/reports/export_pdf_payables/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_export_options/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/partner_invoice_export_options/');
                        array_push($new_permissions['money'], '/partners/export_statement/');
                        array_push($new_permissions['money'], '/vouchers/partner_invoice_export_to_word/');
                        array_push($new_permissions['money'], '/vouchers/partner_statement_export_to_word/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/clients/add/', $group_permission)) {
                        array_push($new_permissions['money'], '/clients/partner_shares/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
        
    public function move_voucher_related_case_id_to_new_table()
    {
        $this->write_log($this->log_path, 'move_voucher_related_case_id_to_new_table started', 'info');
        $all_voucher_header_query = $this->db->query("select id, case_id from voucher_headers  where case_id IS NOT NULL");
        $all_voucher_header = $all_voucher_header_query ->result();
        $this->load->model('voucher_related_case');
            foreach ($all_voucher_header as $voucher_header) {
                if (!empty($voucher_header->case_id)) {
                    $case_ids = explode(',', $voucher_header->case_id);
                    foreach ($case_ids as $case_id) {
                        $this->voucher_related_case->reset_fields();
                        $this->voucher_related_case->set_field('voucher_header_id', $voucher_header->id);
                        $this->voucher_related_case->set_field('legal_case_id',$case_id);
                        $this->voucher_related_case->insert();             
                    }
                }
            }
        $this->remove_voucher_header_case_id_field();

        $this->write_log($this->log_path, 'move_voucher_related_case_id_to_new_table is done', 'info');
    }

    private function remove_voucher_header_case_id_field()
    {
        $this->write_log($this->log_path, 'remove_voucher_header_case_id_field started', 'info');

        if ($this->db->dbdriver === 'sqlsrv') {
            $this->db->query( "declare @table_name nvarchar(256)
            declare @col_name nvarchar(256)
            declare @Command  nvarchar(1000)

            set @table_name = 'voucher_headers'
            set @col_name = 'case_id'

            select @Command = 'ALTER TABLE ' + @table_name + ' drop constraint ' + d.name
             from sys.tables t
              join    sys.default_constraints d
               on d.parent_object_id = t.object_id
              join    sys.columns c
               on c.object_id = t.object_id
                and c.column_id = d.parent_column_id
             where t.name = @table_name
              and c.name = @col_name
            execute (@Command)");
        }
        
        $this->db->query("ALTER TABLE voucher_headers DROP COLUMN case_id");

        $this->write_log($this->log_path, 'remove_voucher_header_case_id_field is done', 'info');
    }

    public function update_themes_tabs()
    {
        $themes_dir = "assets".DIRECTORY_SEPARATOR."app_themes";
        $dirs = scandir($themes_dir);
        if (is_array($dirs) && count($dirs) > 2) {
            $this->write_log($this->log_path, "Done - Get all Dirs");
            unset($dirs[0], $dirs[1]);
            foreach ($dirs as $index => $value) {
                if(!is_dir($themes_dir.DIRECTORY_SEPARATOR.$value)){
                    unset($dirs[$index]);
                }
            }
            $this->write_log($this->log_path, "Done - Uset .. - . folders and files from array dirs");
            foreach ($dirs as $key => $value) {
                $theme_path = $themes_dir.DIRECTORY_SEPARATOR. $value.DIRECTORY_SEPARATOR;
                $this->write_log($this->log_path, "Done - Get Json files from theme ".$value);
                $theme_file = file_get_contents($theme_path.$value.'.json', FILE_USE_INCLUDE_PATH);
                $theme_json = json_decode($theme_file, true);
                $theme_scss = file($theme_path.$value.'_customer_portal.scss');
                $scss_text = '';
                $color_text = '

.cp-title{
    > h3{
        color: $customer_portal_login_text_color !important;
    }
}';
                foreach ($theme_scss as $line) {
                    $scss_text.= $line;
                }
                $scss_text .= $color_text;
                $save_file_scss = file_put_contents($theme_path.$value.'_customer_portal.scss', $scss_text);
                if (@!$save_file_scss) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value."_customer_portal.scss");
                } else {
                    $this->write_log($this->log_path, "Done - save scss file ".$value."_customer_portal.scss theme ".$value);
                }

                $json_array = array("customer_portal_login" => array("login_text_color" => "#28629d"));
                $theme_json['customer_portal'] = array_merge($theme_json['customer_portal'], $json_array);
                $save_json = file_put_contents($theme_path.$value.'.json', json_encode($theme_json));
                if (@!$save_json) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value.".json  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save json file ".$value.".json theme ".$value);
                }

                $data['theme'] = $theme_json;
                $scss_variables = $this->load->view('look_feel/style', $data, true);
                $save_css_variables = file_put_contents($theme_path.'variables.scss', $scss_variables);
                if (@!$save_css_variables) {
                    $this->write_log($this->log_path, "Error - failed to put content to file ".$value."_customer_portal.scss  theme ".$value);
                } else {
                    $this->write_log($this->log_path, "Done - save scss file variables.scss theme ".$value);
                }
                $this->load->library('scss_compiler');
                $scss = new Scss_compiler();
                $compile =  $scss->compile($scss_variables.$scss_text);
                $save_css = file_put_contents($theme_path.$value.'_customer_portal.css', $compile);
                if (@!$save_css) {
                    $this->write_log($this->log_path, "Error - failed to put content to file .css");
                } else {
                    $this->write_log($this->log_path, "Done - save css file .css theme ".$value);
                }
            }
        }
    }

    public function contract_summary_to_approval_summary()
    {
        $this->write_log($this->log_path, 'migrate contract summary to approval summary started', 'info');
        $contract_approval_status = $this->db->query("SELECT id, contract_id FROM contract_approval_status");
        $all_contract_approval_status = $contract_approval_status->result_array();
        foreach ($all_contract_approval_status as $approval) {
            $contract_query = $this->db->query("SELECT id, summary  FROM contract WHERE contract.id = " . $approval['id']);
            $contract_data = $contract_query->result_array()[0];
            $this->db->query("UPDATE contract_approval_status SET contract_approval_status.summary = '{$contract_data['summary']}' WHERE contract_approval_status.id = {$approval['id']}");
        }
        $this->write_log($this->log_path, 'contract summary migrated to approval summary successfully', 'info');
    }
    public function contract_summary_to_signature_summary()
    {
        $this->write_log($this->log_path, 'migrate contract summary to signature summary started', 'info');
        $contract_signature_status = $this->db->query("SELECT id, contract_id FROM contract_signature_status");
        $all_contract_signature_status = $contract_signature_status->result_array();
        foreach ($all_contract_signature_status as $signature) {
            $contract_query = $this->db->query("SELECT id, summary  FROM contract WHERE contract.id = " . $signature['id']);
            $contract_data = $contract_query->result_array()[0];
            $this->db->query("UPDATE contract_signature_status SET contract_signature_status.summary = '{$contract_data['summary']}' WHERE contract_signature_status.id = {$signature['id']}");
        }
        $this->db->query("ALTER TABLE contract DROP COLUMN summary");
        $this->write_log($this->log_path, 'contract summary migrated to signature summary successfully', 'info');
    }
}
