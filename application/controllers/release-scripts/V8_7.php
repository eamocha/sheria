<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_7 extends CI_Controller
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
        $this->add_discount_account_for_all_entities();
        $this->change_money_dashboard_widgets_type();
        $this->add_advisor_task_default_workflow();
        $this->add_advisor_task_default_workflow_statuses();
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
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/money_preferences/', $group_permission)) {
                        array_push($new_permissions['money'], '/setup/configure_invoice_discount/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_export_to_word/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/bill_export_to_word/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/organization_invoice_templates/', $group_permission)) {
                        array_push($new_permissions['money'], '/organization_invoice_templates/bills_templates/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/reports/uninvoiced_time_logs/', $group_permission)) {
                        array_push($new_permissions['money'], '/reports/time_logs_per_month/');
                        array_push($new_permissions['money'], '/reports/time_logs_per_month_export_to_excel/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/setup/index/', $group_permission)) {
                        array_push($new_permissions['money'], '/setup/configure_invoice_discount/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/accounts/delete_account/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/delete_settlement/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_download_file/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'contract') {
                    $new_permissions = $group_permissions;
                    if (in_array('/contracts/related_contracts/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/related_cases/');
                        array_push($new_permissions['contract'], '/contracts/related_contract_add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/contracts/related_contract_delete/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/related_case_delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/contracts/edit/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/archive_unarchive_contracts/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/contracts/sign_contract_doc/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contracts/upload_signed_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'core') {
                    $new_permissions = $group_permissions;
                    if (in_array('/docs/', $group_permission)) {
                        array_push($new_permissions['core'], '/docs/urls/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/docs/create_folder', $group_permission)) {
                        array_push($new_permissions['core'], '/docs/document_add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/docs/delete_document', $group_permission)) {
                        array_push($new_permissions['core'], '/docs/document_delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_types/index/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_types/index/');
                        array_push($new_permissions['core'], '/Docs_document_statuses/index/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }

                    if (in_array('/Contact_document_types/edit/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_types/edit/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_types/add/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_types/add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_types/delete/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_types/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_statuses/edit/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_statuses/edit/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_statuses/add/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_statuses/add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/Contact_document_statuses/delete/', $group_permission)) {
                        array_push($new_permissions['core'], '/Docs_document_statuses/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/cases/delete_document/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/delete_document_comment/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/cases/related/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/related_contracts/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/cases/related_add/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/related_contract_add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/cases/related_delete/', $group_permission)) {
                        array_push($new_permissions['core'], '/cases/related_contract_delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/companies/tab_company/', $group_permission)) {
                        array_push($new_permissions['core'], '/companies/entity_data_management/');
                        array_push($new_permissions['core'], '/companies/entity_data_management_submit/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/export/contact_document_statuses/', $group_permission)) {
                        array_push($new_permissions['core'], '/export/docs_document_statuses/');
                        array_push($new_permissions['core'], '/export/docs_document_types/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/reports/report_builder/', $group_permission)) {
                        array_push($new_permissions['core'], '/reports/matters_attachments_report/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/export/report_builder_excel/', $group_permission)) {
                        array_push($new_permissions['core'], '/export/export_matters_attachments_report/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }
    public function add_discount_account_for_all_entities()
    {
        $this->write_log($this->log_path, 'Add Discount Account for all entities');

        $discount_set = $this->db->query("SELECT keyValue FROM system_preferences WHERE groupName = 'ActivateDiscountinInvoices'");
        $discount_setting = $discount_set->result_array();
        $discount_ena = 'no';
        if (!empty($discount_setting) && $discount_setting[0]['keyValue'] == '1') {
            $discount_ena = 'item_level';
        }
        $organizations = $this->db->query("SELECT id, currency_id FROM organizations");
        $all_organizations = $organizations->result_array();
        $discount_account_per_organization = [];
        foreach ($all_organizations as $organization) {
            $this->write_log($this->log_path, 'Adding discount account for entity: ' . $organization['id']);
            $this->db->query("INSERT INTO accounts (organization_id, currency_id, account_type_id, name, systemAccount, description, model_id, member_id, model_name, model_type, createdBy, createdOn, modifiedBy, modifiedOn,number) VALUES ({$organization['id']}, {$organization['currency_id']}, 9, 'Discount', 'yes', '', NULL, NULL, 'internal', 'internal', NULL, NULL, NULL, NULL,13);");
            $discount_id = $this->db->insert_id();
            $this->db->query("UPDATE voucher_details SET account_id = '$discount_id' WHERE description LIKE '%Discount%' AND voucher_header_id in (SELECT id FROM voucher_headers WHERE organization_id ='{$organization['id']}' AND voucherType = 'INV')");
            $discount_account_per_organization[$organization['id']] = ['enabled' => $discount_ena, 'account_id' => $discount_id];
        }
        $res = serialize($discount_account_per_organization);
        $this->db->query("UPDATE system_preferences SET keyValue = '{$res}' WHERE groupName = 'ActivateDiscountinInvoices'");

        $this->write_log($this->log_path, 'done from discount account');
    }
    public function change_money_dashboard_widgets_type()
    {
        $this->write_log($this->log_path, 'Started change_money_dashboard_widgets_type');

        $this->db->query("UPDATE money_dashboard_widgets_types SET type = 'barChart' WHERE name in ('invoices_by_client', 'expenses_per_category');");

        $this->write_log($this->log_path, 'Done change_money_dashboard_widgets_type');
    }

    public function add_advisor_task_default_workflow()
    {
        $this->write_log($this->log_path, 'start add_advisor_task_default_workflow');

        $query = "SELECT * FROM system_preferences WHERE groupName = 'SystemValues' AND keyName = 'systemLanguage'";
        $filters = $this->db->query($query);
        $filters_array = $filters->result_array();
        $sys_lang = $filters_array[0] ?? null;

        $this->load->model('advisor_task_workflow', 'advisor_task_workflowfactory');
        $this->advisor_task_workflow = $this->advisor_task_workflowfactory->get_instance();

        if ($this->db->dbdriver === 'sqlsrv') {
            if ($sys_lang['keyValue'] === 'english') {
                $insert_query = "INSERT INTO advisor_task_workflows (name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES ('System Workflow (default)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            } else if ($sys_lang['keyValue'] === 'arabic') {
                $insert_query = "INSERT INTO advisor_task_workflows (name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES ('سير عمل النظام (افتراضي)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            } else if ($sys_lang['keyValue'] === 'spanish') {
                $insert_query = "INSERT INTO advisor_task_workflows (name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES ('Flujo de trabajo del sistema (predeterminado)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            }
        } else {
            if ($sys_lang['keyValue'] === 'english') {
                $insert_query = "INSERT INTO advisor_task_workflows (id, name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES (NULL, 'System Workflow (default)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            } else if ($sys_lang['keyValue'] === 'arabic') {
                $insert_query = "INSERT INTO advisor_task_workflows (id, name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES (NULL, 'سير عمل النظام (افتراضي)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            } else if ($sys_lang['keyValue'] === 'spanish') {
                $insert_query = "INSERT INTO advisor_task_workflows (id, name, type, createdBy, createdOn, modifiedBy, modifiedOn) VALUES (NULL, 'Flujo de trabajo del sistema (predeterminado)', 'system', 1, CURRENT_TIMESTAMP, 1, CURRENT_TIMESTAMP);";
            }
        }

        $this->db->query($insert_query);

        $this->write_log($this->log_path, 'done add_advisor_task_default_workflow');
    }

    public function add_advisor_task_default_workflow_statuses()
    {
        $this->write_log($this->log_path, 'start add_advisor_task_default_workflow_statuses');

        $statuses_arr = [
            [1, 1, 1],
            [1, 2, 0],
            [1, 3, 0],
            [1, 4, 0],
            [1, 5, 0]
        ];

        foreach ($statuses_arr as $status) {
            if ($this->db->dbdriver === 'sqlsrv') {
                $query = "INSERT INTO advisor_task_workflow_statuses (advisor_task_workflow_id, advisor_task_status_id, start_point) VALUES ( $status[0], $status[1], $status[2]);";
            } else {
                $query = "INSERT INTO advisor_task_workflow_statuses (id, advisor_task_workflow_id, advisor_task_status_id, start_point) VALUES (NULL, $status[0], $status[1], $status[2]);";
            }
            $this->db->query($query);
        }

        $this->write_log($this->log_path, 'done add_advisor_task_default_workflow_statuses');
    }
}
