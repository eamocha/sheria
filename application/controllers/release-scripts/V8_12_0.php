<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

include APPPATH . 'libraries/traits/MigrationLogTrait.php';

class V8_12_0 extends CI_Controller
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
        $this->add_field_to_invoice_templates();
        $this->fix_money_dashboard_title_language();
        $this->remove_files_from_signature_folder();
        $this->grant_users_to_access_new_permissions();
        $this->insert_titles_for_existing_tasks();
        if (CLOUD) {
            $this->relocate_import_tasks_template_file();
            $this->relocate_export_tasks_template_files();
            $this->relocate_initial_configuration_folder();
            $this->relocate_import_data_template_file('contacts');
            $this->relocate_import_data_template_file('companies');
        }
        $this->replace_old_hearing_template();
        $this->update_seniority_levels();
        $this->remove_id_nb_column_from_grid_saved_columns();
        $this->remove_crnb_column_from_grid_saved_columns();
        $this->set_default_invoice_template();
        $this->write_log($this->log_path, 'End migration script');
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
    public function add_field_to_invoice_templates()
    {
        $this->write_log($this->log_path, 'Started adding Field to invoice templates - show description colmn');
        $this->load->model('organization', 'organizationfactory');
        $this->organization = $this->organizationfactory->get_instance();
        $query = $this->db->query("SELECT * FROM organization_invoice_templates where type = 'invoice'");
        $templates = $query->result_array();
        foreach ($templates as $value => $template) {
            if (empty($template['settings'])) {
                $default_settings = 'a:4:{s:10:"properties";a:4:{s:9:"page-size";s:6:"letter";s:10:"page-color";s:7:"#ffffff";s:9:"page-font";s:7:"Calibri";s:16:"page-orientation";s:8:"portrait";}s:6:"header";a:2:{s:4:"show";a:5:{s:14:"logo-container";b:1;s:22:"company-info-container";b:1;s:11:"center-logo";b:0;s:16:"logo-system-size";b:1;s:16:"image_full_width";b:0;}s:7:"general";a:2:{s:5:"notes";s:290:"<p style="margin: 0;">&nbsp;</p><p style="margin: 0;">Your Business Name</p><p style="margin: 0;">Your Registration Number</p><p style="margin: 0;">Your Street</p><p style="margin: 0;">City, State, Country</p><p style="margin: 0;">Your Phone Number</p><p style="margin: 0;">Your Website</p>";s:4:"logo";s:11:"logo_54.png";}}s:4:"body";a:4:{s:4:"show";a:26:{s:19:"matter-id-container";b:1;s:24:"matter-subject-container";b:1;s:17:"bill-to-container";b:1;s:20:"invoice-nb-container";b:1;s:22:"invoice-date-container";b:1;s:18:"due-date-container";b:1;s:15:"terms-container";b:1;s:24:"invoice-status-container";b:1;s:21:"paid-amount-container";b:0;s:12:"po-container";b:1;s:17:"amount_in_letters";b:0;s:27:"time-logs-summary-container";b:0;s:15:"notes-container";b:1;s:15:"title-container";b:1;s:19:"matter-reference-nb";b:1;s:10:"tax_number";i:1;s:18:"sub-total-discount";b:0;s:14:"show-user-code";b:0;s:18:"show-exchange-rate";b:0;s:21:"invoice-ref-container";b:1;s:25:"invoice-description-table";b:0;s:26:"invoice-description-column";b:1;s:20:"show-entity-currency";b:0;s:17:"full_width_layout";b:1;s:7:"qr-code";b:0;s:29:"time-logs-rebuild-description";b:0;}s:3:"css";a:11:{s:10:"margin-top";s:3:"0.5";s:29:"invoice-information-font-size";s:2:"10";s:24:"invoice-tables-font-size";s:2:"10";s:27:"invoice-summation-font-size";s:2:"10";s:23:"invoice-notes-font-size";s:2:"10";s:14:"tables-borders";s:4:"both";s:30:"invoice-information-font-color";s:7:"#000000";s:25:"invoice-tables-font-color";s:7:"#000000";s:28:"invoice-summation-font-color";s:7:"#000000";s:24:"invoice-notes-font-color";s:7:"#000000";s:31:"tables-headers-background-color";s:7:"#87CEEB";}s:7:"general";a:1:{s:10:"line_items";a:3:{s:8:"expenses";s:1:"1";s:9:"time_logs";s:1:"2";s:5:"items";s:1:"3";}}s:25:"invoice_information_order";a:2:{s:11:"client_data";a:5:{i:0;s:19:"matter-id-container";i:1;s:24:"matter-subject-container";i:2;s:19:"matter-reference-nb";i:3;s:17:"bill-to-container";i:4;s:10:"tax_number";}s:12:"invoice_data";a:8:{i:0;s:20:"invoice-nb-container";i:1;s:21:"invoice-ref-container";i:2;s:24:"invoice-status-container";i:3;s:22:"invoice-date-container";i:4;s:18:"due-date-container";i:5;s:15:"terms-container";i:6;s:12:"po-container";i:7;s:18:"show-exchange-rate";}}}s:6:"footer";a:2:{s:4:"show";a:2:{s:16:"footer-container";b:0;s:14:"page-numbering";b:0;}s:7:"general";a:1:{s:5:"notes";s:58:"<p style="margin: 0;" align="center">www.app4legal.com</p>";}}}';
                $this->db->query("update organization_invoice_templates set settings = '{$default_settings}' WHERE id = {$template['id']}");
            } else {
                $settings = unserialize($template['settings']);
                if (!isset($settings['body']['show']['invoice-description-column'])) {
                    $settings['body']['show']['invoice-description-column'] = true;
                }
                $new_settings = serialize($settings);
                $this->db->query("update organization_invoice_templates set settings = '{$new_settings}' WHERE id = {$template['id']}");
            }
        }
        $this->write_log($this->log_path, 'Field added Succeddfully');
    }

    private function fix_money_dashboard_title_language()
    {
        $this->write_log($this->log_path, 'Start Fixing Title Languages of Money Dashboard');
        $this->load->model('money_dashboard_widget');
        $this->load->model('money_dashboard_widget_title_language');
        $widget_title_languages = $this->money_dashboard_widget_title_language->load_all();
        if (count($widget_title_languages) == 0) {
            $widgets = $this->money_dashboard_widget->load_all();
            $titles = array();
            foreach ($widgets as $widget) {
                $widget_title = isset($widget['title']) ? $widget['title'] : null;
                if (!is_null($widget_title)) {
                    array_push(
                        $titles,
                        array('widget_id' => $widget['id'], 'language_id' => 1, 'title' => $widget_title),
                        array('widget_id' => $widget['id'], 'language_id' => 2, 'title' => $widget_title),
                        array('widget_id' => $widget['id'], 'language_id' => 3, 'title' => $widget_title),
                        array('widget_id' => $widget['id'], 'language_id' => 4, 'title' => $widget_title)
                    );
                }
            }
            if ($this->money_dashboard_widget_title_language->insert_batch($titles)) {
                $this->db->query("ALTER TABLE money_dashboard_widgets DROP COLUMN title;");
            }
        }
        $this->write_log($this->log_path, 'End of Fixing Title Languages of Money Dashboard');
    }

    public function remove_files_from_signature_folder()
    {
        $this->write_log($this->log_path, 'remove files from signature folder');
        $this->load->library(array('is_auth', 'dms'));
        $this->load->model('document_management_system', 'document_management_systemfactory');
        $this->document_management_system = $this->document_management_systemfactory->get_instance();
        $this->load->model('approval_signature_document', 'approval_signature_documentfactory');
        $this->approval_signature_document = $this->approval_signature_documentfactory->get_instance();
        $signature_execution_folders = $this->db->query("SELECT documents_management_system.id FROM documents_management_system where module = 'contract' and name = '--signed--' and system_document = 1");
        $signature_execution_folders_array = $signature_execution_folders->result_array();
        foreach ($signature_execution_folders_array as $key => $folder) {
            $this->document_management_system->fetch($folder['id']);
            $this->document_management_system->set_field('system_document', 0);
            $this->document_management_system->update();
            $this->document_management_system->reset_fields();
            $signature_documents = $this->db->query("SELECT documents_management_system.id FROM documents_management_system where module = 'contract' and parent = '{$folder['id']}'");
            $signature_documents_array = $signature_documents->result_array();
            foreach ($signature_documents_array as $document) {
                $this->approval_signature_document->reset_fields();
                $this->approval_signature_document->set_field('document_id', $document['id']);
                $this->approval_signature_document->set_field('to_be_signed', 1);
                $this->approval_signature_document->insert();
            }
            $this->dms->rename_document('contract', $folder['id'], 'folder', 'Signature-Execution');
        }
        $this->write_log($this->log_path, 'done from removing files from signature folder');
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
                if ($module == 'contract') {
                    $new_permissions = $group_permissions;
                    if (in_array('/contract_workflows/set_as_start_point/', $group_permission)) {
                        array_push($new_permissions['contract'], '/contract_workflows/set_as_approval_start_point/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
                if ($module === 'money') {
                    $new_permissions = $group_permissions;
                    if (in_array('/reports/receivables/', $group_permission)) {
                        array_push($new_permissions['money'], '/reports/receivables_aging_summary/');
                        array_push($new_permissions['money'], '/reports/receivables_aging_details/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/reports/payables/', $group_permission)) {
                        array_push($new_permissions['money'], '/reports/payables_aging_summary/');
                        array_push($new_permissions['money'], '/reports/payables_aging_details/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                    if (in_array('/clients/add/', $group_permission)) {
                        array_push($new_permissions['money'], '/clients/edit_client/');
                        $this->user_group_permission->set_permission_data($user_group['id'], $new_permissions);
                    }
                }
            }
        }
        $this->write_log($this->log_path, 'done from user permissions');
    }

    private function relocate_import_tasks_template_file()
    {
        $this->write_log($this->log_path, 'Start relocate import tasks template file');
        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'tasks' . DIRECTORY_SEPARATOR . 'template.xlsx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . 'tasks' . DIRECTORY_SEPARATOR . 'template.xlsx';
        copy($src, $dest);
        $this->write_log($this->log_path, 'End relocate import tasks template file');
    }

    private function relocate_export_tasks_template_files()
    {
        $this->write_log($this->log_path, 'Start relocate export tasks template file');

        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_details_rtl.docx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_details_rtl.docx';
        copy($src, $dest);

        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_details_ltr.docx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_details_ltr.docx';
        copy($src, $dest);

        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_client_details_rtl.docx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_client_details_rtl.docx';
        copy($src, $dest);

        $src = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_client_details_ltr.docx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'attachments' . DIRECTORY_SEPARATOR . 'word_templates' . DIRECTORY_SEPARATOR . 'task_client_details_ltr.docx';
        copy($src, $dest);

        $this->write_log($this->log_path, 'End relocate export tasks template file');
    }

    private function insert_titles_for_existing_tasks()
    {
        $this->write_log($this->log_path, 'Start Insert Titles for Tasks');
        if ($this->db->dbdriver == 'sqlsrv') {
            $this->db->query("IF OBJECT_ID('[dbo].[udf_StripHTML]') IS NOT NULL DROP FUNCTION [dbo].[udf_StripHTML]");
            $this->db->query("CREATE FUNCTION [dbo].[udf_StripHTML] (@HTMLText VARCHAR(MAX))
            RETURNS VARCHAR(MAX)
            AS
                 BEGIN
                     DECLARE @Start INT;
                     DECLARE @End INT;
                     DECLARE @Length INT;
                     SET @Start = CHARINDEX('<', @HTMLText);
                     SET @End = CHARINDEX('>', @HTMLText, CHARINDEX('<', @HTMLText));
                     SET @Length = (@End - @Start) + 1;
                     WHILE @Start > 0
                           AND @End > 0
                           AND @Length > 0
                         BEGIN
                             SET @HTMLText = STUFF(@HTMLText, @Start, @Length, '');
                             SET @Start = CHARINDEX('<', @HTMLText);
                             SET @End = CHARINDEX('>', @HTMLText, CHARINDEX('<', @HTMLText));
                             SET @Length = (@End - @Start) + 1;
                         END;
                     RETURN LTRIM(RTRIM(@HTMLText));
                 END;");
            $this->db->query("UPDATE tasks SET title = SUBSTRING(dbo.udf_StripHTML(description), 1, 250);");
            $this->db->query("IF OBJECT_ID('[dbo].[udf_StripHTML]') IS NOT NULL DROP FUNCTION [dbo].[udf_StripHTML]");
        } else {
            $this->db->query("DROP FUNCTION IF EXISTS strip_tags;");
            $this->db->query("CREATE FUNCTION strip_tags(Dirty text) RETURNS text
                BEGIN
                DECLARE iStart, iEnd, iLength   INT;
                WHILE locate('<', Dirty) > 0 AND locate('>', Dirty, locate('<', Dirty)) > 0
                DO
                BEGIN
                    SET iStart = locate('<', Dirty), iEnd = locate('>', Dirty, locate('<', Dirty));
                    SET iLength = (iEnd - iStart) + 1;
                    IF iLength > 0 THEN
                    BEGIN
                        SET Dirty = insert(Dirty, iStart, iLength, '');
                    END;
                    END IF;
                END;
                END WHILE;
                RETURN Dirty;
                END");
            $this->db->query("UPDATE tasks SET title = SUBSTRING(strip_tags(description), 1, 250)");
            $this->db->query("DROP FUNCTION IF EXISTS strip_tags;");
        }
        $this->write_log($this->log_path, 'End Insert Titles for Tasks');
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

    public function replace_old_hearing_template()
    {
        $this->write_log($this->log_path, 'Start replace old hearing template');
        $hearing_template_sql = "SELECT id, name, module, parent, size, lineage, createdBy, document_type_id, document_status_id, comment, modifiedBy, createdByChannel, modifiedByChannel FROM documents_management_system WHERE name = 'hearing_english_default_template' OR name = 'hearing_arabic_default_template'";
        $query_execution = $this->db->query($hearing_template_sql);
        $hearing_templates_details = $query_execution->result_array();
        $this->load->model('document_management_system', 'document_management_systemfactory');
        $this->document_management_system = $this->document_management_systemfactory->get_instance();

        foreach ($hearing_templates_details as $hearing_template_detail) {
            $this->load->library('dms', ['user_id' => $hearing_template_detail['createdBy']]);
            $this->dms->delete_document($hearing_template_detail['module'], $hearing_template_detail['id']);

            //upload file
            $size = $hearing_template_detail['size'];
            $parent = $hearing_template_detail['parent'];
            $parent_visible_in_cp = $this->document_management_system->is_visible_in_cp($parent);
            $parent_visible_in_ap = $this->document_management_system->is_visible_in_ap($parent);
            $lineage_array = explode('/', $hearing_template_detail['lineage']);
            array_pop($lineage_array);
            $file_lineage = implode('/', $lineage_array);

            // Insert the file to DB
            $this->document_management_system->reset_fields();
            $this->document_management_system->set_fields(
                array(
                    'type' => 'file',
                    'name' => $hearing_template_detail['name'],
                    'extension' => 'docx',
                    'size' => $size,
                    'parent' => $parent,
                    'version' => 1,
                    'document_type_id' => null,
                    'document_status_id' => null,
                    'comment' => null,
                    'module' => 'doc',
                    'module_record_id' => null,
                    'system_document' => 0,
                    'visible' => 1,
                    'visible_in_cp' => $parent_visible_in_cp ? 1 : 0,
                    'visible_in_ap' => $parent_visible_in_ap,
                    'createdOn' => date("Y-m-d H:i:s"),
                    'createdBy' => $hearing_template_detail['createdBy'],
                    'createdByChannel' => $hearing_template_detail['createdByChannel'],
                    'modifiedOn' => date("Y-m-d H:i:s"),
                    'modifiedBy' => $hearing_template_detail['modifiedBy'],
                    'modifiedByChannel' => $hearing_template_detail['modifiedByChannel'],
                    'document_type_id' => $hearing_template_detail['document_type_id'],
                    'document_status_id' => $hearing_template_detail['document_status_id'],
                    'comment' => $hearing_template_detail['comment'],
                )
            );
            if ($this->document_management_system->insert()) {
                $this->document_management_system->set_field('lineage', $file_lineage . DIRECTORY_SEPARATOR . $this->document_management_system->get_field('id'));
                if ($this->document_management_system->update()) {
                    $file_path = substr(COREPATH, 0, -12) . 'files' . DIRECTORY_SEPARATOR . 'initial_configuration' . DIRECTORY_SEPARATOR . $hearing_template_detail['name'] . '.docx';
                    // All default template files should be placed in the '/files/initial_configuration/' directory
                    copy($file_path, $this->dms->get_module_container_path('doc') . $this->document_management_system->get_field('lineage'));
                }
            }
        }
        $this->write_log($this->log_path, 'End replace old hearing template');
    }

    public function update_seniority_levels()
    {
        $this->write_log($this->log_path, 'Started fill seniority levels');
        $this->load->model('seniority_level');
        $this->load->model('language');
        $this->languages = $this->language->loadAvailableLanguages(true);
        foreach ($this->languages as $lang) {
            if ($lang['fullName']) {
                switch ($lang) {
                    case 'arabic':
                        foreach (['متدرب', 'مبتدأ', 'فئة المتوسطة', 'رتبة عليا', 'تنفيذي', 'شريك', 'المالك'] as $level) {
                            $this->seniority_level->set_field('name', $level);
                            $this->seniority_level->insert();
                            $this->seniority_level->reset_fields();
                        }
                        break;
                    case 'spanish':
                        foreach (['Interno', 'Entrada', 'Medio', 'Mayor', 'Ejecutivo', 'Pareja', 'Dueño'] as $level) {
                            $this->seniority_level->set_field('name', $level);
                            $this->seniority_level->insert();
                            $this->seniority_level->reset_fields();
                        }
                        break;
                    default:
                        foreach (['Intern', 'Entry', 'Middle', 'Senior', 'Executive', 'Partner', 'Owner'] as $level) {
                            $this->seniority_level->set_field('name', $level);
                            $this->seniority_level->insert();
                            $this->seniority_level->reset_fields();
                        }
                        break;
                }
            }
        }
        $this->write_log($this->log_path, 'Done fill seniority levels');
    }

    public function remove_id_nb_column_from_grid_saved_columns()
    {
        $this->write_log($this->log_path, 'Start remove id_nb column from grid saved columns');
        $this->load->model('grid_saved_column');
        $grid_saved_columns = $this->grid_saved_column->load_all(['where' => ['model', 'Contact']]);
        if (!empty($grid_saved_columns)) {
            foreach ($grid_saved_columns as $grid_saved_column) {
                if (!empty($grid_saved_column['grid_details'])) {
                    $grid_details = unserialize($grid_saved_column['grid_details']);
                    if (!empty($grid_details['sort'])) {
                        $sort = json_decode($grid_details['sort']);
                        foreach ($sort as $index => $item) {
                            if ($item->field == 'id_nb') {
                                unset($sort[$index]);
                            }
                        }
                        $grid_details['sort'] = json_encode(array_values($sort));
                    }
                    if (!empty($grid_details['selected_columns'])) {
                        $id_nb_column_index = array_search('id_nb', $grid_details['selected_columns']);
                        unset($grid_details['selected_columns'][$id_nb_column_index]);
                    }
                    $grid_details = serialize($grid_details);
                    $query = "UPDATE grid_saved_columns SET grid_details = '{$grid_details}' WHERE id = {$grid_saved_column['id']}";
                    $this->db->query($query);
                }
            }
        }
        $this->write_log($this->log_path, 'End remove id_nb column from grid saved columns');
    }
    public function remove_crnb_column_from_grid_saved_columns()
    {
        $this->write_log($this->log_path, 'Start remove crNumber column from grid saved columns');
        $this->load->model('grid_saved_column');
        $grid_saved_columns = $this->grid_saved_column->load_all(['where' => ['model', 'Company']]);
        if (!empty($grid_saved_columns)) {
            foreach ($grid_saved_columns as $grid_saved_column) {
                if (!empty($grid_saved_column['grid_details'])) {
                    $grid_details = unserialize($grid_saved_column['grid_details']);
                    if (!empty($grid_details['sort'])) {
                        $sort = json_decode($grid_details['sort']);
                        foreach ($sort as $index => $item) {
                            if ($item->field == 'crNumber') {
                                unset($sort[$index]);
                            }
                        }
                        $grid_details['sort'] = json_encode(array_values($sort));
                    }
                    if (!empty($grid_details['selected_columns'])) {
                        $cr_nb_column_index = array_search('crNumber', $grid_details['selected_columns']);
                        unset($grid_details['selected_columns'][$cr_nb_column_index]);
                    }
                    $grid_details = serialize($grid_details);
                    $query = "UPDATE grid_saved_columns SET grid_details = '{$grid_details}' WHERE id = {$grid_saved_column['id']}";
                    $this->db->query($query);
                }
            }
        }
        $this->write_log($this->log_path, 'End remove CR NB column from grid saved columns');
    }
    public function relocate_import_data_template_file($module)
    {
        $this->write_log($this->log_path, 'Start relocate ' . $module . ' template file');
        $src  = INSTANCE_PATH . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app4legal_latest' . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'template.xlsx';
        $dest = INSTANCE_PATH . 'files' . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'template.xlsx';
        copy($src, $dest);
        $this->write_log($this->log_path, 'End relocate ' . $module . ' template file');
    }
    public function set_default_invoice_template()
    {
        $this->write_log($this->log_path, 'Start set invoice default template');
        $query = $this->db->query("SELECT organization_id FROM organization_invoice_templates WHERE type = 'invoice' 
            group by organization_id having count(organization_id) = 1 ");
        $organization_templates = $query->result_array();
        foreach ($organization_templates as $organization_template) {
            $query = "UPDATE organization_invoice_templates SET is_default = '1' WHERE organization_id = {$organization_template['organization_id']}";
            $this->db->query($query);
        }
        $this->write_log($this->log_path, 'End set invoice default template');
    }
}
