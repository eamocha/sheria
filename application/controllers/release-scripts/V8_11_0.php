<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_11_0 extends CI_Controller
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
        $this->add_debit_note_prefix();
        $this->create_attachments_money_dir();
        $this->edit_spanish_sample_data();
        $this->grant_users_to_access_new_permissions();
        $this->migrate_old_users_note_attachments('case');
        $this->migrate_old_users_note_attachments('company');
        $this->migrate_old_users_note_attachments('contract');

        if (CLOUD) {
            $this->relocate_initial_configuration_folder();
            $this->relocate_import_data_template_file('matter_containers');
            $this->relocate_import_data_template_file('companies');
            $this->relocate_import_data_template_file('corporate_matters');
            $this->relocate_import_data_template_file('intellectual_properties');
            $this->relocate_import_data_template_file('tasks');
            $this->modify_htaccess();
        }
        
        $this->write_log($this->log_path, 'End migration script');
    }

    public function add_debit_note_prefix()
    {
        $this->write_log($this->log_path, 'Adding debit note prefix');
        $organizations = $this->db->query("SELECT id FROM organizations");
        $all_organizations = $organizations->result_array();
        $prefixes = [];
        foreach ($all_organizations as $organization) {
            $prefixes[$organization['id']] = "DN-";
        }
        $res = serialize($prefixes);
        $this->db->query("INSERT INTO system_preferences VALUES ('DebitNoteValues','debitNoteNumberPrefix','{$res}');");
        $this->write_log($this->log_path, 'Done from adding debit note prefix');
    }

    public function create_attachments_money_dir()
    {
        $this->write_log($this->log_path, 'Create needed money attachments directory');
        foreach (["debit_notes_payments", "credit_notes", "debit_notes"] as $typeDirectory) {
            $tempDirectory = $this->config->item('files_path') . DIRECTORY_SEPARATOR . "attachments" . DIRECTORY_SEPARATOR . "money" . DIRECTORY_SEPARATOR . $typeDirectory;
            if (!is_dir($tempDirectory)) {
                @mkdir($tempDirectory, 0755);
            }
        }
        $this->write_log($this->log_path, 'Done from creating money attachments directory');
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

    public function edit_spanish_sample_data()
    {
        $this->write_log($this->log_path, 'Start edit spanish sample data');
        $this->load->model('system_preference');
        $systemLang = $this->system_preference->get_value_by_key('systemLanguage')['keyValue'];
        if ($systemLang == 'spanish') {
            $this->insert_additional_spanish_contacts_titles();
            $this->edit_spanish_contact_company_categories();
            $this->edit_spanish_contact_company_sub_categories();
            $this->edit_spanish_workflow_status();
        }
        $this->write_log($this->log_path, 'End edit spanish sample data');
    }

    private function insert_additional_spanish_contacts_titles()
    {
        $this->write_log($this->log_path, 'Start insert additional spanish contacts title');
        $this->load->model('title', 'titlefactory');
        $this->title = $this->titlefactory->get_instance();
        $data_to_fix = [
           ['language_id' => '1', 'name' => 'Miss'],
           ['language_id' => '2', 'name' => 'الأنسة'],
           ['language_id' => '3', 'name' => 'Madame'],
           ['language_id' => '4', 'name' => 'Srta.'],
           ['language_id' => '1', 'name' => 'Ms.'],
           ['language_id' => '2', 'name' => 'السيدة'],
           ['language_id' => '3', 'name' => 'Mme.'],
           ['language_id' => '4', 'name' => 'Sra./Srta.']
        ];
        
        $id_inserted = 0;
        foreach ($data_to_fix as $data) {
            $row_count = $this->db->get_where('titles_languages', $data)->num_rows();
            if ($row_count == 0 || $data['name'] == 'السيدة') {
                if ($data['language_id'] == '1') {
                    if ($this->db->dbdriver == 'sqlsrv') {
                        $this->db->query('INSERT INTO titles DEFAULT VALUES');
                    } else {
                        $title_data = array('id' => null);
                        $this->db->insert('titles', $title_data);
                    }
                    $id_inserted = $this->db->insert_id();
                }
                $data['title_id'] = $id_inserted;
                $this->db->insert('titles_languages', $data);
            }
        }
        
        $this->write_log($this->log_path, 'End insert additional spanish contacts title');
    }

    private function edit_spanish_contact_company_categories()
    {
        $this->write_log($this->log_path, 'Start edit spanish contact company categories');
        $this->load->model('contact_company_category');
        $data_to_fix = [
            ['fetchName' => 'External Advisor', 'name' => 'Asesor Externo'],
            ['fetchName' => 'Lead', 'name' => 'Lead'],
            ['fetchName' => 'Not Categorized', 'name' => 'No Clasificado'],
            ['fetchName' => 'Opponent', 'name' => 'Oponente'],
            ['fetchName' => 'Partner', 'name' => 'Socio'],
            ['fetchName' => 'Prospect', 'name' => 'Prospecto'],
        ];
        $this->update_data($this->contact_company_category, $data_to_fix, 'keyName');
        $this->write_log($this->log_path, 'End edit spanish contact company categories');
    }

    private function edit_spanish_contact_company_sub_categories()
    {
        $this->write_log($this->log_path, 'Start edit spanish contact company sub categories');
        $this->load->model('contact_company_sub_category');
        $data_to_fix = [
            ['fetchName' => 'Auditora', 'name' => 'Auditor'],
            ['fetchName' => 'Asesor externo', 'name' => 'Asesor Externo'],
            ['fetchName' => 'Abogado externo', 'name' => 'Abogado Externo'],
            ['fetchName' => 'Dirigir', 'name' => 'Lead'],
            ['fetchName' => 'Notario publico', 'name' => 'Notario Público'],
            ['fetchName' => 'Colega de oficina', 'name' => 'Compañero de Oficina'],
            ['fetchName' => 'Adversario', 'name' => 'Oponente'],
            ['fetchName' => 'Otros', 'name' => 'Otro'],
            ['fetchName' => 'Compañero', 'name' => 'Socio']
        ];
        $this->update_data($this->contact_company_sub_category, $data_to_fix, 'name');
        $this->write_log($this->log_path, 'End edit spanish contact company sub categories');
    }

    private function edit_spanish_workflow_status()
    {
        $this->write_log($this->log_path, 'Start edit spanish workflow status');
        $this->load->model('workflow_status', 'workflow_statusfactory');
        $this->workflow_status = $this->workflow_statusfactory->get_instance();
        $data_to_fix_workflow_status = [
            ['fetchName' => 'En progresos', 'name' => 'En Curso'],
            ['fetchName' => 'Pendiente', 'name' => 'En Espera']
        ];

        $this->load->model('legal_case_container_status');
        $data_to_fix_legal_case_container_status = [
            ['fetchName' => 'En curso', 'name' => 'En Curso'],
            ['fetchName' => 'En espera', 'name' => 'En Espera']
        ];

        $this->load->model('task_status');
        $data_to_fix_task_status = [
            ['fetchName' => '2-En progreso', 'name' => '2-En Curso'],
            ['fetchName' => '3-Pendiente', 'name' => '3-En Espera'],
            ['fetchName' => '5-Hecho', 'name' => '5-Finalizado']
        ];
        
        $this->load->model('ip_status');
        $data_to_fix_ip_status = [
            ['fetchName' => '2-En progreso', 'name' => '2-En Curso'],
            ['fetchName' => '3-Pendiente interna', 'name' => '3-En Espera (Interno)'],
            ['fetchName' => '4-Externa pendiente', 'name' => '4-En Espera (Externo)'],
            ['fetchName' => '6-Hecho', 'name' => '6-Finalizado']
        ];
        
        $this->load->model('advisor_task_status');
        $data_to_fix_advisor_task_status = [
            ['fetchName' => '2-En progreso', 'name' => '2-En Curso'],
            ['fetchName' => '3-Pendiente Interna', 'name' => '3-En Espera (Interno)'],
            ['fetchName' => '4-Pendiente Externa', 'name' => '4-En Espera (Externo)'],
            ['fetchName' => '6-Hecho', 'name' => '6-Finalizado']
        ];

        $this->update_data($this->workflow_status, $data_to_fix_workflow_status, 'name');
        $this->update_data($this->legal_case_container_status, $data_to_fix_legal_case_container_status, 'name');
        $this->update_data($this->task_status, $data_to_fix_task_status, 'name');
        $this->update_data($this->ip_status, $data_to_fix_ip_status, 'name');
        $this->update_data($this->advisor_task_status, $data_to_fix_advisor_task_status, 'name');
        $this->write_log($this->log_path, 'End edit spanish workflow status');
    }

    private function update_data($model, $data_to_fix, $fetch_column)
    {
        $this->write_log($this->log_path, 'Start update spanish data');
        foreach ($data_to_fix as $data) {
            $model->reset_fields();
            if ($model->fetch([$fetch_column => $data['fetchName']])) {
                $model->set_field('name', $data['name']);
                $model->update();
            }
        }
        $this->write_log($this->log_path, 'End update spanish data');
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
                    if (in_array('/tasks/view/', $group_permission)) {
                        array_push($new_permissions['core'], '/tasks/view_document/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/companies/delete_note/', $group_permission)) {
                        array_push($new_permissions['core'], '/companies/delete_document_comment/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/setup/invoice_number_prefix/', $group_permission)) {
                        array_push($new_permissions['money'], '/setup/debit_note_number_prefix/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/vouchers/invoice_add/', $group_permission)) {
                        array_push($new_permissions['money'], '/vouchers/debit_note_add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/invoice_notes/index/', $group_permission)) {
                        array_push($new_permissions['money'], '/credit_note_reasons/index/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                        array_push($new_permissions['money'], '/debit_note_reasons/index/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/invoice_notes/add/', $group_permission)) {
                        array_push($new_permissions['money'], '/credit_note_reasons/add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                        array_push($new_permissions['money'], '/debit_note_reasons/add/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/invoice_notes/edit/', $group_permission)) {
                        array_push($new_permissions['money'], '/credit_note_reasons/edit/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                        array_push($new_permissions['money'], '/debit_note_reasons/edit/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/invoice_notes/delete/', $group_permission)) {
                        array_push($new_permissions['money'], '/credit_note_reasons/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                        array_push($new_permissions['money'], '/debit_note_reasons/delete/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    public function relocate_initial_configuration_folder()
    {
        $this->write_log($this->log_path, 'Start relocate initial configuration folder');
        $files = glob(INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files'
        . DIRECTORY_SEPARATOR . 'initial_configuration' . DIRECTORY_SEPARATOR . '*');
        $folder = INSTANCE_PATH  . 'files' . DIRECTORY_SEPARATOR . 'initial_configuration';
        
        if (!file_exists($folder)) {
            mkdir($folder);
        }

        foreach ($files as $file) {
            copy($file, $folder . DIRECTORY_SEPARATOR . basename($file));
        }

        $this->write_log($this->log_path, 'End relocate initial configuration folder');
    }
    
    public function migrate_old_users_note_attachments($module)
    {
        $this->write_log($this->log_path, 'Start migration of old users note attachments');
        
        $this->load->model('document_management_system', 'document_management_systemfactory');
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        
        // Get all case, contract, and company notes attachments and set module_path
        if ($module == 'case') {
            $comment_attachments_sql =
            "SELECT case_comments.case_id, case_comments.createdOn, case_comments.user_id, case_comments.modifiedBy, 
            case_comments.createdByChannel, case_comments.modifiedByChannel, case_comment_attachments.path
            FROM case_comments 
            INNER JOIN case_comment_attachments on case_comments.id = case_comment_attachments.case_comment_id
            INNER JOIN documents_management_system dms on case_comment_attachments.path = dms.id 
            WHERE case_comment_attachments.uploaded = 'Yes'";
        } elseif ($module == 'company') {
            $comment_attachments_sql =
            "SELECT company_notes.company_id, company_notes.created_on, company_notes.created_by, company_notes.modified_by, company_note_details.path
            FROM company_notes 
            INNER JOIN company_note_details ON company_notes.id = company_note_details.company_note_id
            INNER JOIN documents_management_system dms on company_note_details.path = dms.id 
            WHERE company_note_details.uploaded = 'Yes'";
        } else {
            $comment_attachments_sql =
            "SELECT contract_id, createdOn, createdBy, modifiedBy, channel, modifiedByChannel, comment FROM contract_comment";
        }
        
        $query_execution = $this->db->query($comment_attachments_sql);
        $attachments = $query_execution->result_array();

        if ($module == 'contract') {
            // Get path for a note attachment from comment column
            foreach ($attachments as $key => $attachment) {
                $ids = null;
                preg_match_all("/[|]+\d+/", $attachment['comment'], $ids);
                $ids = $ids[0];
                $attachments[$key]['selected_ids'] = [];
                $this->load->library('dms', ['user_id' => $attachment['createdBy'], 'release_script' => true]);
                for ($index = 0; $index < count($ids); $index++) {
                    $id = $ids[$index];
                    $id = ltrim($id, $id[0]);
                    if ($this->dms->get_document_details(['id' => $id])) {
                        array_push($attachments[$key]['selected_ids'], $id);
                    }
                }
                if (empty($attachments[$key]['selected_ids'])) {
                    unset($attachments[$key]);
                }
            }
        }

        foreach ($attachments as $attachment) {
            if ($module != 'contract') {
                $user_id = $module == 'case' ? $attachment['user_id'] : $attachment['created_by'];
                $this->load->library('dms', ['user_id' => $user_id, 'release_script' => true]);
            }
            $target_folder_name = $module == 'case' || $module == 'contract' ? $attachment['createdOn'] : $attachment['created_on'];
            $module_record_id = $module == 'case' ? $attachment['case_id'] : ($module == 'contract' ? $attachment['contract_id'] : $attachment['company_id']);
            $target_folder = $this->dms->get_document_details(['name' => $target_folder_name, 'module' => $module, 'module_record_id' => $module_record_id]);

            // Check if parent folder exists
            if ($target_folder) {
                if ($module == 'contract') {
                    $this->dms->move_document_handler($target_folder['id'], $attachment['selected_ids'], [], $module);
                } else {
                    $this->dms->move_document_handler($target_folder['id'], [$attachment['path']], [], $module);
                }
            } else {
                $is_attachment_visible_in_ap = false;
                if ($module == 'case') {
                    $is_attachment_visible_in_ap = $this->document_management_system->is_visible_in_ap($attachment['path']);
                }
                // Create parent folder
                $container_name = $module == 'case' ? 'Matter Notes Attachments' : ($module == 'contract' ? 'Contract_Notes_Attachments' : 'Company_Notes_Attachments');
                $new_note_parent_folder = $this->dms->create_folder([
                    'name' => $target_folder_name,
                    'module' => $module,
                    'module_record_id' => $module_record_id,
                    'container_name' => $container_name,
                    'child_ap_visible' => $is_attachment_visible_in_ap
                ]);
                if ($new_note_parent_folder) {
                    if ($module == 'contract') {
                        $this->dms->move_document_handler($new_note_parent_folder['id'], $attachment['selected_ids'], [], $module);
                    } else {
                        $this->dms->move_document_handler($new_note_parent_folder['id'], [$attachment['path']], [], $module);
                    }
                } else {
                    $this->write_log($this->log_path, 'Creating parent folder failed');
                }
            }
        }
        $this->write_log($this->log_path, 'End of migration of old users note attachments');
    }

    public function relocate_import_data_template_file($module)
    {
        $this->write_log($this->log_path, 'Start relocate matter containers template file');
        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files'
        . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'template.xlsx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR .
        'template.xlsx';
        copy($src, $dest);
        $this->write_log($this->log_path, 'End relocate matter containers template file');
    }

    public function modify_htaccess()
    {
        $this->write_log($this->log_path, 'Start modifying advisor-portal .htaccess file', 'info');

        $this->load->model('instance_data');
        $instance_data_values = $this->instance_data->get_values();
        $this->instance_name = $instance_data_values['instanceID'];

        $config = parse_ini_file(INSTANCE_PATH . '../config.ini');
        $htaccess_directory_path =INSTANCE_PATH . 'advisor-portal';
        $htaccess_file_path = $htaccess_directory_path . DIRECTORY_SEPARATOR . '.htaccess';

        if (!file_exists($htaccess_directory_path)) {
            mkdir($htaccess_directory_path);
        }

        file_put_contents($htaccess_file_path, "\t" .' RewriteEngine On ' .PHP_EOL. "\t" . ' RewriteRule (.*) ' . $config['advisor_portal_base_url'] . '/' . $this->instance_name . ' [R=301,L]');

        $this->write_log($this->log_path, 'Modifying advisor-portal .htaccess file is done', 'info');
    }
}
